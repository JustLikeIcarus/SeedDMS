<?php
require_once("inc/inc.ClassSettings.php");

function usage() { /* {{{ */
	echo "Usage:\n";
	echo "  seeddms-xmlimport [-h] [-v] [--config <file>]\n";
	echo "\n";
	echo "Description:\n";
	echo "  This program imports an xml dump into the dms.\n";
	echo "\n";
	echo "Options:\n";
	echo "  -h, --help: print usage information and exit.\n";
	echo "  -v, --version: print version and exit.\n";
	echo "  --config: set alternative config file.\n";
	echo "  --folder: set import folder.\n";
	echo "  --file: file containing the dump.\n";
	echo "  --sections: comma seperated list of sections to read from dump.\n";
	echo "     can be: users, groups, documents, folders, keywordcategories, or\n";
	echo "     documentcategories\n";
} /* }}} */

function insert_user($user) { /* {{{ */
	global $dms;

	print_r($user);
	if (is_object($dms->getUserByLogin($user['attributes']['login']))) {
		echo "User already exists\n";
	} else {
		$newUser = $dms->addUser(
			$user['attributes']['login'],
			$user['attributes']['pwd'],
			$user['attributes']['fullname'],
			$user['attributes']['email'],
			$user['attributes']['language'],
			$user['attributes']['theme'],
			$user['attributes']['comment'],
			$user['attributes']['role'],
			$user['attributes']['hidden'],
			$user['attributes']['disabled'],
			$user['attributes']['pwdexpiration']);
		if(!$newUser) {
			echo "Error: could not add user\n";
		}
	}
} /* }}} */

function insert_group($group) { /* {{{ */
	global $dms, $users;

	print_r($group);
	if (is_object($dms->getGroupByName($group['attributes']['name']))) {
		echo "Group already exists\n";
	} else {
		$newGroup = $dms->addGroup($group['attributes']['name'], $group['attributes']['comment']);
		if($newGroup) {
			foreach($group['users'] as $guser) {
				if(isset($users[$guser])) {
					$user = $users[$guser];
					if($newMember = $dms->getUserByLogin($user['attributes']['login'])) {
						$newGroup->addUser($newMember);
						echo $users[$guser]['attributes']['login']."\n";
					} else {
						echo "Error: could not find member of group\n";
					}
				}
			}
		} else {
			echo "Error: could not add group\n";
		}
	}
} /* }}} */

function insert_attributedefinition($attrdef) { /* {{{ */
	global $dms;

	print_r($attrdef);
	if(is_object($dms->getAttributeDefinitionByName($attrdef['attributes']['name']))) {
		echo "Attribute definition already exists\n";
	} else {
		if(!$newAttrdef = $dms->addAttributeDefinition($attrdef['attributes']['name'], $attrdef['objecttype'], $attrdef['attributes']['type'], $attrdef['attributes']['multiple'], $attrdef['attributes']['minvalues'], $attrdef['attributes']['maxvalues'], $attrdef['attributes']['valueset'])) {
			echo "Error: could not add attribute definition\n";
		}
	}
} /* }}} */

function insert_documentcategory($documentcat) { /* {{{ */
	global $dms;

	print_r($documentcat);
	if(is_object($dms->getDocumentCategoryByName($documentcat['attributes']['name']))) {
		echo "Document category already exists\n";
	} else {
		if(!$newCategory = $dms->addDocumentCategory($documentcat['attributes']['name'])) {
			echo "Error: could not add document category\n";
		}
	}
} /* }}} */

function insert_keywordcategory($keywordcat) { /* {{{ */
	global $dms;

	print_r($keywordcat);
	if(is_object($dms->getKeywordCategoryByName($keywordcat['attributes']['name'], $keywordcategories['attributes']['owner']))) {
		echo "Document category already exists\n";
	} else {
		if(!$newCategory = $dms->addKeywordCategory($keywordcat['attributes']['name'], $keywordcat['attributes']['owner'])) {
			echo "Error: could not add keyword category\n";
		}
	}
} /* }}} */

function insert_document($document) { /* {{{ */
	global $dms;

	print_r($document);
} /* }}} */

function insert_folder($folder) { /* {{{ */
	print_r($folder);
} /* }}} */

function startElement($parser, $name, $attrs) { /* {{{ */
	global $elementstack, $cur_user, $cur_group, $cur_folder, $cur_document, $cur_version, $cur_approval, $cur_review, $cur_attrdef, $cur_documentcat, $cur_keyword, $cur_keywordcat;

	$parent = end($elementstack);
	array_push($elementstack, array('name'=>$name, 'attributes'=>$attrs));
	switch($name) {
		case "USER":
			/* users can be the users data, the member of a group */
			$first = $elementstack[1];
			if($first['name'] == 'USERS') {
				$cur_user = array();
				$cur_user['id'] = $attrs['ID'];
				$cur_user['attributes'] = array();
			} elseif($first['name'] == 'GROUPS') {
			}
			break;
		case "GROUP":
			$first = $elementstack[1];
			if($first['name'] == 'GROUPS') {
				$cur_group = array();
				$cur_group['id'] = $attrs['ID'];
				$cur_group['attributes'] = array();
			}
			break;
		case "DOCUMENT":
			$cur_document = array();
			$cur_document['id'] = $attrs['ID'];
			$cur_document['folder'] = $attrs['FOLDER'];
			$cur_document['attributes'] = array();
			$cur_document['versions'] = array();
			break;
		case "FOLDER":
			$cur_folder = array();
			$cur_folder['id'] = $attrs['ID'];
			if(isset($attrs['PARENT']))
				$cur_folder['folder'] = $attrs['PARENT'];
			$cur_folder['attributes'] = array();
			break;
		case "VERSION":
			$cur_version = array();
			$cur_version['id'] = $attrs['ID'];
			$cur_version['attributes'] = array();
			$cur_version['approvals'] = array();
			$cur_version['reviews'] = array();
			break;
		case "APPROVAL":
			$cur_approval = array();
			$cur_approval['attributes'] = array();
			break;
		case "REVIEW":
			$cur_review = array();
			$cur_review['attributes'] = array();
			break;
		case 'ATTRIBUTEDEFINITION':
			$cur_attrdef = array();
			$cur_attrdef['attributes'] = array();
			$cur_attrdef['objecttype'] = $attrs['OBJTYPE'];
			break;
		case "ATTR":
			if($parent['name'] == 'DOCUMENT') {
				if(isset($attrs['TYPE']) && $attrs['TYPE'] == 'user') {
					$cur_document['user_attributes'][$attrs['ATTRDEF']] = '';
				} else {
					$cur_document['attributes'][$attrs['NAME']] = '';
				}
			} elseif($parent['name'] == 'VERSION') {
				if(isset($attrs['TYPE']) && $attrs['TYPE'] == 'user') {
					$cur_version['user_attributes'][$attrs['ATTRDEF']] = '';
				} else {
					$cur_version['attributes'][$attrs['NAME']] = '';
				}
			} elseif($parent['name'] == 'APPROVAL') {
				$cur_approval['attributes'][$attrs['NAME']] = '';
			} elseif($parent['name'] == 'REVIEW') {
				$cur_review['attributes'][$attrs['NAME']] = '';
			} elseif($parent['name'] == 'FOLDER') {
				if(isset($attrs['TYPE']) && $attrs['TYPE'] == 'user') {
					$cur_folder['user_attributes'][$attrs['ATTRDEF']] = '';
				} else {
					$cur_folder['attributes'][$attrs['NAME']] = '';
				}
			} elseif($parent['name'] == 'USER') {
				$cur_user['attributes'][$attrs['NAME']] = '';
			}
			break;
		case "ACL":
			$first = $elementstack[1];
			if($first['name'] == 'FOLDER') {
				$acl = array('type'=>$attrs['TYPE'], 'mode'=>$attrs['MODE']);
				if($attrs['TYPE'] == 'user') {
					$acl['user'] = $attrs['USER'];
				} elseif($attrs['TYPE'] == 'group') { 
					$acl['group'] = $attrs['GROUP'];
				}
				$cur_folder['acls'][] = $acl;
			} elseif($first['name'] == 'DOCUMENT') {
				$acl = array('type'=>$attrs['TYPE'], 'mode'=>$attrs['MODE']);
				if($attrs['TYPE'] == 'user') {
					$acl['user'] = $attrs['USER'];
				} elseif($attrs['TYPE'] == 'group') { 
					$acl['group'] = $attrs['GROUP'];
				}
				$cur_document['acls'][] = $acl;
			}
			break;
		case "DATA":
			if($parent['name'] == 'IMAGE') {
				$cur_user['image']['id'] = $parent['attributes']['ID'];
				$cur_user['image']['data'] = "";
			} elseif($parent['name'] == 'VERSION') {
				$cur_version['data'] = "";
			}
			break;
		case "KEYWORD":
			$cur_keyword = array();
			$cur_keyword['id'] = $attrs['ID'];
			$cur_keyword['attributes'] = array();
			break;
		case "KEYWORDCATEGORY":
			$cur_keywordcat = array();
			$cur_keywordcat['id'] = $attrs['ID'];
			$cur_keywordcat['attributes'] = array();
			$cur_keywordcat['keywords'] = array();
			break;
		case "DOCUMENTCATEGORY":
			$cur_documentcat = array();
			$cur_documentcat['id'] = $attrs['ID'];
			$cur_documentcat['attributes'] = array();
			break;
	}
} /* }}} */

function endElement($parser, $name) { /* {{{ */
	global $dms, $sections, $rootfolder, $elementstack, $users, $groups, $cur_user, $cur_group, $cur_folder, $cur_document, $cur_version, $cur_approval, $cur_review, $cur_attrdef, $cur_documentcat, $cur_keyword, $cur_keywordcat;

	array_pop($elementstack);
	$parent = end($elementstack);
	switch($name) {
		case "DOCUMENT":
			if(in_array('documents', $sections)) {
				insert_document($cur_document);
			}
			break;
		case "FOLDER":
			if(in_array('folders', $sections)) {
				insert_folder($cur_folder);
				$owner = $dms->getUser($cur_folder['attributes']['owner']);
//			$newfolder = $rootfolder->addSubFolder($cur_folder['name'], $cur_folder['comment'], $owner, $cur_folder['sequence']);
			}
			break;
		case "VERSION":
			$cur_document['versions'][] = $cur_version;
			break;
		case "APPROVAL":
			$cur_version['approvals'][] = $cur_approval;
			break;
		case "REVIEW":
			$cur_version['reviews'][] = $cur_review;
			break;
		case "USER":
			/* users can be the users data or the member of a group */
			$first = $elementstack[1];
			if($first['name'] == 'USERS') {
				$users[$cur_user['id']] = $cur_user;
				if(in_array('users', $sections)) {
					insert_user($cur_user);
				}
			}
			break;
		case "GROUP":
			$first = $elementstack[1];
			if($first['name'] == 'GROUPS') {
				$groups[$cur_group['id']] = $cur_group;
				if(in_array('groups', $sections)) {
					insert_group($cur_group);
				}
			}
			break;
		case 'ATTRIBUTEDEFINITION':
			if(in_array('attributedefinitions', $sections)) {
				insert_attributedefinition($cur_attrdef);
			}
			break;
		case 'KEYWORD':
			$cur_keywordcat['keywords'][] = $cur_keyword;
			break;
		case 'KEYWORDCATEGORY':
			if(in_array('keywordcategories', $sections)) {
				insert_keywordcategory($cur_keywordcat);
			}
			break;
		case 'DOCUMENTCATEGORY':
			if(in_array('documentcategories', $sections)) {
				insert_documentcategory($cur_documentcat);
			}
			break;
	}
} /* }}} */

function characterData($parser, $data) { /* {{{ */
	global $elementstack, $cur_user, $cur_group, $cur_folder, $cur_document, $cur_version, $cur_approval, $cur_review, $cur_attrdef, $cur_documentcat, $cur_keyword, $cur_keywordcat;

	$current = end($elementstack);
	$parent = prev($elementstack);
	switch($current['name']) {
		case 'ATTR':
			switch($parent['name']) {
				case 'DOCUMENT':
					if(isset($current['attributes']['TYPE']) && $current['attributes']['TYPE'] == 'user') {
						$cur_document['user_attributes'][$current['attributes']['ATTRDEF']] = $data;
					} else {
						$cur_document['attributes'][$current['attributes']['NAME']] = $data;
					}
					break;
				case 'FOLDER':
					if(isset($current['attributes']['TYPE']) && $current['attributes']['TYPE']  == 'user') {
						$cur_folder['user_attributes'][$current['attributes']['ATTRDEF']] = $data;
					} else {
						$cur_folder['attributes'][$current['attributes']['NAME']] = $data;
					}
					break;
				case 'VERSION':
					if(isset($current['attributes']['TYPE']) && $current['attributes']['TYPE']  == 'user') {
						$cur_version['user_attributes'][$current['attributes']['ATTRDEF']] = $data;
					} else {
						$cur_version['attributes'][$current['attributes']['NAME']] = $data;
					}
					break;
				case 'APPROVAL':
					$cur_approval['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'REVIEW':
					$cur_review['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'USER':
					$cur_user['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'GROUP':
					$cur_group['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'ATTRIBUTEDEFINITION':
					$cur_attrdef['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'DOCUMENTCATEGORY':
					$cur_documentcat['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'KEYWORDCATEGORY':
					$cur_keywordcat['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'KEYWORD':
					$cur_keyword['attributes'][$current['attributes']['NAME']] = $data;
					break;
				case 'IMAGE':
					$cur_user['image']['mimetype'] = $data;
					break;
			}
			break;
		case 'DATA':
			switch($parent['name']) {
				case 'IMAGE':
					$cur_user['image']['data'] .= $data;
					break;
				case 'VERSION':
					$cur_version['data'] .= $data;
					break;
			}
			break;
		case 'USER':
			$first = $elementstack[1];
			if($first['name'] == 'GROUPS') {
				$cur_group['users'][] = $data;
			}
			break;
	}
	
} /* }}} */

$version = "0.0.1";
$shortoptions = "hv";
$longoptions = array('help', 'version', 'config:', 'sections:', 'folder:', 'file:');
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

if(isset($options['folder'])) {
	$folderid = intval($options['folder']);
} else {
	$folderid = $settings->_rootFolderID;
}

$filename = '';
if(isset($options['file'])) {
	$filename = $options['file'];
} else {
	usage();
	exit(1);
}

$sections = array('documents', 'folders', 'groups', 'users', 'keywordcategories', 'documentcategories', 'attributedefinitions');
if(isset($options['sections'])) {
	$sections = explode(',', $options['sections']);
}

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

require_once("SeedDMS/Core.php");

$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");

$dms = new SeedDMS_Core_DMS($db, $settings->_contentDir.$settings->_contentOffsetDir);
if(!$dms->checkVersion()) {
	echo "Database update needed.";
	exit;
}
$dms->setRootFolderID($settings->_rootFolderID);

$rootfolder = $dms->getFolder($folderid);
if(!$rootfolder) {
	exit(1);
}

$elementstack = array();

$xml_parser = xml_parser_create("UTF-8");
xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, true);
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "characterData");
if (!($fp = fopen($filename, "r"))) {
    die("could not open XML input");
}
while ($data = fread($fp, 65535)) {
	if (!xml_parse($xml_parser, $data, feof($fp))) {
		die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($xml_parser)),
			xml_get_current_line_number($xml_parser)));
	}
}
?>
