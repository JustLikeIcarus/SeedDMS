<?php
require_once("../inc/inc.ClassSettings.php");

function usage() { /* {{{ */
	echo "Usage:\n";
	echo "  seeddms-xmldump [-h] [-v] [--config <file>]\n";
	echo "\n";
	echo "Description:\n";
	echo "  This program creates an xml dump of the whole or parts of the dms.\n";
	echo "\n";
	echo "Options:\n";
	echo "  -h, --help: print usage information and exit.\n";
	echo "  -v, --version: print version and exit.\n";
	echo "  --config: set alternative config file.\n";
	echo "  --folder: set start folder.\n";
	echo "  --maxsize: maximum size of files to be include in output.\n";
} /* }}} */

function wrapWithCData($text) { /* {{{ */
	if(preg_match("/[<>&]/", $text))
		return("<![CDATA[".$text."]]>");
	else
		return $text;
} /* }}} */

$version = "0.0.1";
$shortoptions = "hv";
$longoptions = array('help', 'version', 'config:', 'folder:', 'maxsize:');
if(false === ($options = getopt($shortoptions, $longoptions))) {
	usage();
	exit(0);
}

/* Print help and exit */
if(isset($options['h']) || isset($options['help'])) {
	usage();
	exit(0);
}

/* Print version and exit */
if(isset($options['v']) || isset($options['verѕion'])) {
	echo $version."\n";
	exit(0);
}

/* Set alternative config file */
if(isset($options['config'])) {
	$settings = new Settings($options['config']);
} else {
	$settings = new Settings();
}

/* Set maximum size of files included in xml file */
if(isset($options['maxsize'])) {
	$maxsize = intval($maxsize);
} else {
	$maxsize = 100000;
}

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

require_once("SeedDMS/Core.php");

if(isset($options['folder'])) {
	$folderid = intval($options['folder']);
} else {
	$folderid = $settings->_rootFolderID;
}

function tree($folder, $parent=null, $indent='') { /* {{{ */
	global $index, $dms;
	echo $indent."<folder id=\"".$folder->getId()."\"";
	if($parent)
		echo " parent=\"".$parent->getID()."\"";
	echo ">\n";
	echo $indent." <attr name=\"name\">".wrapWithCData($folder->getName())."</attr>\n";
	echo $indent." <attr name=\"date\">".date('c', $folder->getDate())."</attr>\n";
	echo $indent." <attr name=\"defaultaccess\">".$folder->getDefaultAccess()."</attr>\n";
	echo $indent." <attr name=\"inheritaccess\">".$folder->inheritsAccess()."</attr>\n";
	echo $indent." <attr name=\"sequence\">".$folder->getSequence()."</attr>\n";
	if($folder->getComment())
		echo $indent." <attr name=\"comment\">".wrapWithCData($folder->getComment())."</attr>\n";
	echo $indent." <attr name=\"owner\">".$folder->getOwner()->getId()."</attr>\n";
	if($attributes = $folder->getAttributes()) {
		foreach($attributes as $attribute) {
			$attrdef = $attribute->getAttributeDefinition();
			echo $indent." <attr type=\"user\" attrdef=\"".$attrdef->getID()."\">".$attribute->getValue()."</attr>\n";
		}
	}
	if($folder->inheritsAccess()) {
		echo $indent." <acls type=\"inherited\" />\n";
	} else {
		echo $indent." <acls>\n";
		$accesslist = $folder->getAccessList();
		foreach($accesslist['users'] as $acl) {
			echo $indent."  <acl type=\"user\"";
			$user = $acl->getUser();
			echo " user=\"".$user->getID()."\"";
			echo " mode=\"".$acl->getMode()."\"";
			echo "/>\n";
		}
		foreach($accesslist['groups'] as $acl) {
			echo $indent."  <acl type=\"group\"";
			$group = $acl->getGroup();
			echo $indent." group=\"".$group->getID()."\"";
			echo $indent." mode=\"".$acl->getMode()."\"";
			echo "/>\n";
		}
		echo $indent." </acls>\n";
	}
	echo $indent."</folder>\n";
	$subfolders = $folder->getSubFolders();
	if($subfolders) {
		foreach($subfolders as $subfolder) {
			tree($subfolder, $folder, $indent);
		}
	}
	$documents = $folder->getDocuments();
	if($documents) {
		foreach($documents as $document) {
			$owner = $document->getOwner();
			echo $indent."<document id=\"".$document->getId()."\" folder=\"".$folder->getID()."\"";
			if($document->isLocked())
				echo " locked=\"true\"";
			echo ">\n";
			echo $indent." <attr name=\"name\">".wrapWithCData($document->getName())."</attr>\n";
			echo $indent." <attr name=\"date\">".date('c', $document->getDate())."</attr>\n";
			if($document->getExpires())
				echo $indent." <attr name=\"expires\">".date('c', $document->getExpires())."</attr>\n";
			echo $indent." <attr name=\"owner\">".$owner->getId()."</attr>\n";
			if($document->getKeywords())
				echo $indent." <attr name=\"keywords\">".wrapWithCData($document->getKeywords())."</attr>\n";
			echo $indent." <attr name=\"defaultaccess\">".$document->getDefaultAccess()."</attr>\n";
			echo $indent." <attr name=\"inheritaccess\">".$document->inheritsAccess()."</attr>\n";
			echo $indent." <attr name=\"sequence\">".$document->getSequence()."</attr>\n";
			if($document->isLocked()) {
				$user = $document->getLockingUser();
				echo $indent." <attr name=\"lockedby\">".$user->getId()."</attr>\n";
			}
			if($document->getComment())
				echo $indent." <attr name=\"comment\">".wrapWithCData($document->getComment())."</attr>\n";
			if($attributes = $document->getAttributes()) {
				foreach($attributes as $attribute) {
					$attrdef = $attribute->getAttributeDefinition();
					echo $indent." <attr type=\"user\" attrdef=\"".$attrdef->getID()."\">".$attribute->getValue()."</attr>\n";
				}
			}

			/* Check if acl is not inherited */
			if(!$document->inheritsAccess()) {
				$acls = $document->getAccessList();
				if($acls['groups'] || $acls['users']) {
					echo $indent." <acls>\n";
					if($acls['users']) {
						foreach($acls['users'] as $acluser) {
							$user = $acluser->getUser();
							echo $indent."  <acl type=\"user\">\n";
							echo $indent."   <attr name=\"user\">".$user->getId()."</attr>\n";
							echo $indent."   <attr name=\"mode\">".$acluser->getMode()."</attr>\n";
							echo $indent."  </acl>\n";
						}
					}
					if($acls['groups']) {
						foreach($acls['groups'] as $aclgroup) {
							$group = $aclgroup->getGroup();
							echo $indent."  <acl type=\"group\">\n";
							echo $indent."   <attr name=\"group\">".$group->getId()."</attr>\n";
							echo $indent."   <attr name=\"mode\">".$acluser->getMode()."</attr>\n";
							echo $indent."  </acl>\n";
						}
					}
					echo $indent." </acls>\n";
				}
			}

			$cats = $document->getCategories();
			if($cats) {
				echo $indent." <categories>\n";
				foreach($cats as $cat) {
					echo $indent."  <category>".$cat->getId()."</category>\n";
				}
				echo $indent." </categories>\n";
			}

			$versions = $document->getContent();
			if($versions) {
				echo $indent." <versions>\n";
				foreach($versions as $version) {
					$approvalStatus = $version->getApprovalStatus();
					$reviewStatus = $version->getReviewStatus();
					$owner = $version->getUser();
					echo $indent."  <version id=\"".$version->getVersion()."\">\n";
					echo $indent."   <attr name=\"mimetype\">".$version->getMimeType()."</attr>\n";
					echo $indent."   <attr name=\"date\">".date('c', $version->getDate())."</attr>\n";
					echo $indent."   <attr name=\"filetype\">".$version->getFileType()."</attr>\n";
					echo $indent."   <attr name=\"comment\">".wrapWithCData($version->getComment())."</attr>\n";
					echo $indent."   <attr name=\"owner\">".$owner->getId()."</attr>\n";
					echo $indent."   <attr name=\"orgfilename\">".wrapWithCData($version->getOriginalFileName())."</attr>\n";
					if($attributes = $version->getAttributes()) {
						foreach($attributes as $attribute) {
							$attrdef = $attribute->getAttributeDefinition();
							echo $indent."   <attr type=\"user\" attrdef=\"".$attrdef->getID()."\">".$attribute->getValue()."</attr>\n";
						}
					}
					if($approvalStatus) {
						echo $indent."   <approvals>\n";
						foreach($approvalStatus as $a) {
							echo $indent."    <approval id=\"".$a['approveID']."\">\n";
							echo $indent."     <attr name=\"type\">".$a['type']."</attr>\n";
							echo $indent."     <attr name=\"required\">".$a['required']."</attr>\n";
							echo $indent."     <attr name=\"status\">".$a['status']."</attr>\n";
							echo $indent."     <attr name=\"comment\">".wrapWithCData($a['comment'])."</attr>\n";
							echo $indent."     <attr name=\"user\">".$a['userID']."</attr>\n";
							echo $indent."     <attr name=\"date\">".$a['date']."</attr>\n";
							echo $indent."    </approval>\n";
						}
						echo $indent."   </approvals>\n";
					}
					if($reviewStatus) {
						echo $indent."   <reviews>\n";
						foreach($reviewStatus as $a) {
							echo $indent."    <review id=\"".$a['reviewID']."\">\n";
							echo $indent."     <attr name=\"type\">".$a['type']."</attr>\n";
							echo $indent."     <attr name=\"required\">".$a['required']."</attr>\n";
							echo $indent."     <attr name=\"status\">".$a['status']."</attr>\n";
							echo $indent."     <attr name=\"comment\">".wrapWithCData($a['comment'])."</attr>\n";
							echo $indent."     <attr name=\"user\">".$a['userID']."</attr>\n";
							echo $indent."     <attr name=\"date\">".$a['date']."</attr>\n";
							echo $indent."    </review>\n";
						}
						echo $indent."   </reviews>\n";
					}
					if(file_exists($dms->contentDir . $version->getPath())) {
						echo $indent."   <data length=\"".filesize($dms->contentDir . $version->getPath())."\">\n";
						if(filesize($dms->contentDir . $version->getPath()) < 1000000) {
							echo chunk_split(base64_encode(file_get_contents($dms->contentDir . $version->getPath())), 76, "\n");
						}
						echo $indent."   </data>\n";
					} else {
						echo $indent."   <!-- ".$dms->contentDir . $version->getPath()." not found -->\n";
					}
					echo $indent."  </version>\n";
				}
				echo $indent." </versions>\n";
			}

			$files = $document->getDocumentFiles();
			if($files) {
				echo $indent." <files>\n";
				foreach($files as $file) {
					$owner = $file->getUser();
					echo $indent."  <file id=\"".$file->getId()."\">\n";
					echo $indent."   <attr name=\"name\">".wrapWithCData($file->getName())."</attr>\n";
					echo $indent."   <attr name=\"mimetype\">".$file->getMimeType()."</attr>\n";
					echo $indent."   <attr name=\"date\">".date('c', $file->getDate())."</attr>\n";
					echo $indent."   <attr name=\"filetype\">".wrapWithCData($file->getFileType())."</attr>\n";
					echo $indent."   <attr name=\"owner\">".$owner->getId()."</attr>\n";
					echo $indent."   <attr name=\"comment\">".wrapWithCData($file->getComment())."</attr>\n";
					echo $indent."   <attr name=\"orgfilename\">".wrapWithCData($file->getOriginalFileName())."</attr>\n";
					echo $indent."   <data length=\"".filesize($dms->contentDir . $file->getPath())."\">\n";
					if(filesize($dms->contentDir . $file->getPath()) < 1000000) {
						echo chunk_split(base64_encode(file_get_contents($dms->contentDir . $file->getPath())), 76, "\n");
					}
					echo $indent."   </data>\n";
					echo $indent."  </file>\n";
				}
				echo $indent." </files>\n";
			}
			$links = $document->getDocumentLinks();
			if($links) {
				echo $indent." <links>\n";
				foreach($links as $link) {
					$owner = $link->getUser();
					$target = $link->getTarget();
					echo $indent."  <link id=\"".$link->getId()."\">\n";
					echo $indent."   <attr name=\"target\">".$target->getId()."</attr>\n";
					echo $indent."   <attr name=\"owner\">".$owner->getId()."</attr>\n";
					echo $indent."   <attr name=\"public\">".$link->isPublic()."</attr>\n";
					echo $indent."  </link>\n";
				}
				echo $indent." </links>\n";
			}
			$notifications = $document->getNotifyList();
			if($notifications) {
				if($notifications['groups'] || $notifications['users']) {
					echo $indent." <notifications>\n";
					if($notifications['users']) {
						foreach($notifications['users'] as $user) {
							echo $indent."  <notification type=\"user\">\n";
							echo $indent."   <attr name=\"user\">".$user->getId()."</attr>\n";
							echo $indent."  </notification>\n";
						}
					}
					if($notifications['groups']) {
						foreach($notifications['groups'] as $group) {
							echo $indent."  <notification type=\"group\">\n";
							echo $indent."   <attr name=\"group\">".$group->getId()."</attr>\n";
							echo $indent."  </notification>\n";
						}
					}
					echo $indent." </notification>\n";
				}
			}

			echo $indent."</document>\n";
		}
	}
} /* }}} */

$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");

$dms = new SeedDMS_Core_DMS($db, $settings->_contentDir.$settings->_contentOffsetDir);
if(!$dms->checkVersion()) {
	echo "Database update needed.";
	exit;
}

$dms->setRootFolderID($settings->_rootFolderID);

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<dms>\n";
$users = $dms->getAllUsers();
if($users) {
	echo "<users>\n";
	foreach ($users as $user) {
		echo " <user id=\"".$user->getId()."\">\n";
		echo "  <attr name=\"login\">".wrapWithCData($user->getLogin())."</attr>\n";
		echo "  <attr name=\"pwd\">".wrapWithCData($user->getPwd())."</attr>\n";
		echo "  <attr name=\"email\">".wrapWithCData($user->getEmail())."</attr>\n";
		echo "  <attr name=\"fullname\">".wrapWithCData($user->getFullName())."</attr>\n";
		echo "  <attr name=\"comment\">".wrapWithCData($user->getComment())."</attr>\n";
		echo "  <attr name=\"language\">".$user->getLanguage()."</attr>\n";
		echo "  <attr name=\"theme\">".$user->getTheme()."</attr>\n";
		echo "  <attr name=\"role\">".$user->getRole()."</attr>\n";
		echo "  <attr name=\"hidden\">".$user->isHidden()."</attr>\n";
		echo "  <attr name=\"disabled\">".$user->isDisabled()."</attr>\n";
		echo "  <attr name=\"pwdexpiration\">".$user->getPwdExpiration()."</attr>\n";
		if($image = $user->getImage()) {
			echo "  <image id=\"".$image['id']."\">\n";
			echo "   <attr name=\"mimetype\">".$image['mimeType']."</attr>\n";
			echo "   <data>".base64_encode($image['image'])."</data>\n";
			echo "  </image>\n";
		}
		echo " </user>\n";
	}
	echo "</users>\n";
}

$groups = $dms->getAllGroups();
if($groups) {
	echo "<groups>\n";
	foreach ($groups as $group) {
		echo " <group id=\"".$group->getId()."\">\n";
		echo "  <attr name=\"name\">".wrapWithCData($user->getFullName())."</attr>\n";
		echo "  <attr name=\"comment\">".wrapWithCData($user->getComment())."</attr>\n";
		$users = $group->getUsers();
		if($users) {
			echo "  <users>\n";
			foreach ($users as $user) {
				echo "   <user>".$user->getId()."</user>\n";
			}
			echo "  </users>\n";
		}
		echo " </group>\n";
	}
	echo "</groups>\n";
}

$categories = $dms->getAllKeywordCategories();
if($categories) {
	echo "<keywordcategories>\n";
	foreach($categories as $category) {
		$owner = $category->getOwner();
		echo " <keywordcategory id=\"".$category->getId()."\">\n";
		echo "  <attr name=\"name\">".wrapWithCData($category->getName())."</attr>\n";
		echo "  <attr name=\"owner\">".$owner->getId()."</attr>\n";
		if($keywords = $category->getKeywordLists()) {
			echo "  <keywords>\n";
			foreach($keywords as $keyword) {
				echo "   <keyword id=\"".$keyword['id']."\">\n";
				echo "    <attr name=\"name\">".wrapWithCData($keyword['keywords'])."</attr>\n";
				echo "   </keyword>\n";
			}
			echo "  </keywords>\n";
		}
		echo " </keywordcategory>\n";
	}
	echo "</keywordcategories>\n";
}

$categories = $dms->getDocumentCategories();
if($categories) {
	echo "<documentcategories>\n";
	foreach($categories as $category) {
		echo " <documentcategory id=\"".$category->getId()."\">\n";
		echo "  <attr name=\"name\">".wrapWithCData($category->getName())."</attr>\n";
		echo " </documentcategory>\n";
	}
	echo "</documentcategories>\n";
}

$attrdefs = $dms->getAllAttributeDefinitions();
if($attrdefs) {
	echo "<attrіbutedefinitions>\n";
	foreach ($attrdefs as $attrdef) {
		echo " <attributedefinition id=\"".$attrdef->getID()."\" objtype=\"";
		switch($attrdef->getObjType()) {
			case SeedDMS_Core_AttributeDefinition::objtype_all:
				echo "all";
				break;
			case SeedDMS_Core_AttributeDefinition::objtype_folder:
				echo "folder";
				break;
			case SeedDMS_Core_AttributeDefinition::objtype_document:
				echo "document";
				break;
			case SeedDMS_Core_AttributeDefinition::objtype_documentcontent:
				echo "documentcontent";
				break;
		}
		echo "\">\n";
		echo "  <attr name=\"name\">".$attrdef->getName()."</attr>\n";
		echo "  <attr name=\"multiple\">".$attrdef->getMultipleValues()."</attr>\n";
		echo "  <attr name=\"valueset\">".$attrdef->getValueSet()."</attr>\n";
		echo "  <attr name=\"type\">".$attrdef->getType()."</attr>\n";
		echo "  <attr name=\"minvalues\">".$attrdef->getMinValues()."</attr>\n";
		echo "  <attr name=\"maxvalues\">".$attrdef->getMaxValues()."</attr>\n";
		echo " </attributedefinition>\n";
	}
	echo "</attrіbutedefinitions>\n";
}

$folder = $dms->getFolder($folderid);
if($folder) {
	tree($folder);
}

echo "</dms>\n";
?>
