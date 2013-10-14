<?php
/**
 * Implementation of ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ViewFolder extends SeedDMS_Bootstrap_Style {

	function getAccessModeText($defMode) { /* {{{ */
		switch($defMode) {
			case M_NONE:
				return getMLText("access_mode_none");
				break;
			case M_READ:
				return getMLText("access_mode_read");
				break;
			case M_READWRITE:
				return getMLText("access_mode_readwrite");
				break;
			case M_ALL:
				return getMLText("access_mode_all");
				break;
		}
	} /* }}} */

	function printAccessList($obj) { /* {{{ */
		$accessList = $obj->getAccessList();
		if (count($accessList["users"]) == 0 && count($accessList["groups"]) == 0)
			return;

		for ($i = 0; $i < count($accessList["groups"]); $i++)
		{
			$group = $accessList["groups"][$i]->getGroup();
			$accesstext = $this->getAccessModeText($accessList["groups"][$i]->getMode());
			print $accesstext.": ".htmlspecialchars($group->getName());
			if ($i+1 < count($accessList["groups"]) || count($accessList["users"]) > 0)
				print "<br />";
		}
		for ($i = 0; $i < count($accessList["users"]); $i++)
		{
			$user = $accessList["users"][$i]->getUser();
			$accesstext = $this->getAccessModeText($accessList["users"][$i]->getMode());
			print $accesstext.": ".htmlspecialchars($user->getFullName());
			if ($i+1 < count($accessList["users"]))
				print "<br />";
		}
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$orderby = $this->params['orderby'];
		$enableFolderTree = $this->params['enableFolderTree'];
		$enableClipboard = $this->params['enableClipboard'];
		$expandFolderTree = $this->params['expandFolderTree'];
		$showtree = $this->params['showtree'];
		$cachedir = $this->params['cachedir'];
		$workflowmode = $this->params['workflowmode'];
		$enableRecursiveCount = $this->params['enableRecursiveCount'];
		$maxRecursiveCount = $this->params['maxRecursiveCount'];

		$folderid = $folder->getId();

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));

		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder), "view_folder", $folder);

		echo "<div class=\"row-fluid\">\n";

		// dynamic columns - left column removed if no content and right column then fills span12.
		if (!($enableFolderTree || $enableClipboard)) {
			$LeftColumnSpan = 0;
			$RightColumnSpan = 12;
		} else {
			$LeftColumnSpan = 4;
			$RightColumnSpan = 8;
		}
		if ($LeftColumnSpan > 0) {
			echo "<div class=\"span".$LeftColumnSpan."\">\n";
			if ($enableFolderTree) {
				if ($showtree==1){
					$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=0\"><i class=\"icon-minus-sign\"></i></a>", true);
					$this->contentContainerStart();
?>
		<script language="JavaScript">
		function folderSelected(id, name) {
			window.location = '../out/out.ViewFolder.php?folderid=' + id;
		}
		</script>
<?php
					$this->printNewTreeNavigation($folderid, M_READ, 0, '', $expandFolderTree == 2);
					$this->contentContainerEnd();
				} else {
					$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=1\"><i class=\"icon-plus-sign\"></i></a>", true);
				}
				if ($enableClipboard) $this->printClipboard($this->params['session']->getClipboard());
				echo "</div>\n";
			}
		}
		echo "<div class=\"span".$RightColumnSpan."\">\n";

		$this->contentHeading(getMLText("folder_infos"));

		$owner = $folder->getOwner();
		$this->contentContainerStart();
		echo "<table class=\"table-condensed\">\n";
		if($user->isAdmin()) {
			echo "<tr>";
			echo "<td>".getMLText("id").":</td>\n";
			echo "<td>".htmlspecialchars($folder->getID())."</td>\n";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td>".getMLText("owner").":</td>\n";
		echo "<td><a href=\"mailto:".htmlspecialchars($owner->getEmail())."\">".htmlspecialchars($owner->getFullName())."</a></td>\n";
		echo "</tr>";
		if($folder->getComment()) {
			echo "<tr>";
			echo "<td>".getMLText("comment").":</td>\n";
			echo "<td>".htmlspecialchars($folder->getComment())."</td>\n";
			echo "</tr>";
		}

		if($user->isAdmin()) {
			if($folder->inheritsAccess()) {
				echo "<tr>";
				echo "<td>".getMLText("access_mode").":</td>\n";
				echo "<td>";
				echo getMLText("inherited");
				echo "</tr>";
			} else {
				echo "<tr>";
				echo "<td>".getMLText('default_access').":</td>";
				echo "<td>".$this->getAccessModeText($folder->getDefaultAccess())."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>".getMLText('access_mode').":</td>";
				echo "<td>";
				$this->printAccessList($folder);
				echo "</td>";
				echo "</tr>";
			}
		}
		$attributes = $folder->getAttributes();
		if($attributes) {
			foreach($attributes as $attribute) {
				$attrdef = $attribute->getAttributeDefinition();
		?>
				<tr>
				<td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
				<td><?php echo htmlspecialchars($attribute->getValue()); ?></td>
				</tr>
		<?php
			}
		}
		echo "</table>\n";
		$this->contentContainerEnd();

		$this->contentHeading(getMLText("folder_contents"));

		$subFolders = $folder->getSubFolders($orderby);
		$subFolders = SeedDMS_Core_DMS::filterAccess($subFolders, $user, M_READ);
		$documents = $folder->getDocuments($orderby);
		$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, M_READ);

		if ((count($subFolders) > 0)||(count($documents) > 0)){
			print "<table class=\"table\">";
			print "<thead>\n<tr>\n";
			print "<th></th>\n";	
			print "<th><a href=\"../out/out.ViewFolder.php?folderid=". $folderid .($orderby=="n"?"":"&orderby=n")."\">".getMLText("name")."</a></th>\n";
//			print "<th>".getMLText("owner")."</th>\n";
			print "<th>".getMLText("status")."</th>\n";
//			print "<th>".getMLText("version")."</th>\n";
			print "<th>".getMLText("action")."</th>\n";
			print "</tr>\n</thead>\n<tbody>\n";
		}
		else printMLText("empty_folder_list");


		foreach($subFolders as $subFolder) {

			$owner = $subFolder->getOwner();
			$comment = $subFolder->getComment();
			if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
			$subsub = $subFolder->getSubFolders();
			$subsub = SeedDMS_Core_DMS::filterAccess($subsub, $user, M_READ);
			$subdoc = $subFolder->getDocuments();
			$subdoc = SeedDMS_Core_DMS::filterAccess($subdoc, $user, M_READ);
			
			print "<tr rel=\"folder_".$subFolder->getID()."\" class=\"folder\" ondragover=\"allowDrop(event)\" ondrop=\"onDrop(event)\">";
		//	print "<td><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></td>";
			print "<td><a rel=\"folder_".$subFolder->getID()."\" draggable=\"true\" ondragstart=\"onDragStartFolder(event);\" href=\"out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\"><img draggable=\"false\" src=\"".$this->imgpath."folder.png\" width=\"24\" height=\"24\" border=0></a></td>\n";
			print "<td><a href=\"out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\">" . htmlspecialchars($subFolder->getName()) . "</a>";
			print "<br /><span style=\"font-size: 85%; font-style: italic; color: #666;\">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $subFolder->getDate())."</b></span>";
			if($comment) {
				print "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
			}
			print "</td>\n";
//			print "<td>".htmlspecialchars($owner->getFullName())."</td>";
			print "<td colspan=\"1\" nowrap><small>";
			if($enableRecursiveCount) {
				if($user->isAdmin()) {
					/* No need to check for access rights in countChildren() for
					 * admin. So pass 0 as the limit.
					 */
					$cc = $subFolder->countChildren($user, 0);
					print $cc['folder_count']." ".getMLText("folders")."<br />".$cc['document_count']." ".getMLText("documents");
				} else {
					$cc = $subFolder->countChildren($user, $maxRecursiveCount);
					if($maxRecursiveCount > 5000)
						$rr = 100.0;
					else
						$rr = 10.0;
					print (!$cc['folder_precise'] ? '~'.(round($cc['folder_count']/$rr)*$rr) : $cc['folder_count'])." ".getMLText("folders")."<br />".(!$cc['document_precise'] ? '~'.(round($cc['document_count']/$rr)*$rr) : $cc['document_count'])." ".getMLText("documents");
				}
			} else {
				print count($subsub)." ".getMLText("folders")."<br />".count($subdoc)." ".getMLText("documents");
			}
			print "</small></td>";
//			print "<td></td>";
			print "<td>";
			print "<div class=\"list-action\">";
			if($subFolder->getAccessMode($user) >= M_ALL) {
?>
     <a class_="btn btn-mini" href="../out/out.RemoveFolder.php?folderid=<?php echo $subFolder->getID(); ?>"><i class="icon-remove"></i></a>
<?php
			} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>
<?php
			}
			if($subFolder->getAccessMode($user) >= M_READWRITE) {
?>
     <a class_="btn btn-mini" href="../out/out.EditFolder.php?folderid=<?php echo $subFolder->getID(); ?>"><i class="icon-edit"></i></a>
<?php
			} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-edit"></i></span>
<?php
			}
?>
     <a class="addtoclipboard" rel="<?php echo "F".$subFolder->getID(); ?>" msg="<?php printMLText('splash_added_to_clipboard'); ?>" _href="../op/op.AddToClipboard.php?folderid=<?php echo $folder->getID(); ?>&type=folder&id=<?php echo $subFolder->getID(); ?>" title="<?php printMLText("add_to_clipboard");?>"><i class="icon-copy"></i></a>
<?php
			print "</div>";
			print "</td>";
			print "</tr>\n";
		}

		$previewer = new SeedDMS_Preview_Previewer($cachedir, 40);
		foreach($documents as $document) {

			$owner = $document->getOwner();
			$comment = $document->getComment();
			if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
			$docID = $document->getID();
			if($latestContent = $document->getLatestContent()) {
				$previewer->createPreview($latestContent);
				$version = $latestContent->getVersion();
				$status = $latestContent->getStatus();
				$needwkflaction = false;
				if($workflowmode == 'advanced') {
					$workflow = $latestContent->getWorkflow();
					if($workflow) {
						$needwkflaction = $latestContent->needsWorkflowAction($user);
					}
				}
				
				/* Retrieve attacheѕ files */
				$files = $document->getDocumentFiles();

				/* Retrieve linked documents */
				$links = $document->getDocumentLinks();
				$links = SeedDMS_Core_DMS::filterDocumentLinks($user, $links);

				print "<tr>";

				if (file_exists($dms->contentDir . $latestContent->getPath())) {
					print "<td><a rel=\"document_".$docID."\" draggable=\"true\" ondragstart=\"onDragStartDocument(event);\" href=\"../op/op.Download.php?documentid=".$docID."&version=".$version."\">";
					if($previewer->hasPreview($latestContent)) {
						print "<img draggable=\"false\" class=\"mimeicon\" width=\"40\"src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=40\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
					} else {
						print "<img draggable=\"false\" class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
					}
					print "</a></td>";
				} else
					print "<td><img draggable=\"false\" class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\"></td>";
				
				print "<td><a href=\"out.ViewDocument.php?documentid=".$docID."&showtree=".$showtree."\">" . htmlspecialchars($document->getName()) . "</a>";
				print "<br /><span style=\"font-size: 85%; font-style: italic; color: #666; \">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $document->getDate())."</b>, ".getMLText('version')." <b>".$version."</b> - <b>".date('Y-m-d', $latestContent->getDate())."</b></span>";
				if($comment) {
					print "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
				}
				print "</td>\n";
//				print "<td>".htmlspecialchars($owner->getFullName())."</td>";
				print "<td nowrap>";
				$attentionstr = '';
				if ( $document->isLocked() ) {
					$attentionstr .= "<img src=\"".$this->getImgPath("lock.png")."\" title=\"". getMLText("locked_by").": ".htmlspecialchars($document->getLockingUser()->getFullName())."\"> ";
				}
				if ( $needwkflaction ) {
					$attentionstr .= "<img src=\"".$this->getImgPath("attention.gif")."\" title=\"". getMLText("workflow").": "."\"> ";
				}
				if($attentionstr)
					print $attentionstr."<br />";
				print "<small>";
				if(count($files))
					print count($files)." ".getMLText("linked_files")."<br />";
				if(count($links))
					print count($links)." ".getMLText("linked_documents")."<br />";
				print getOverallStatusText($status["status"])."</small></td>";
//				print "<td>".$version."</td>";
				print "<td>";
				print "<div class=\"list-action\">";
				if($document->getAccessMode($user) >= M_ALL) {
?>
     <a class_="btn btn-mini" href="../out/out.RemoveDocument.php?documentid=<?php echo $docID; ?>"><i class="icon-remove"></i></a>
<?php
				} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>
<?php
				}
				if($document->getAccessMode($user) >= M_READWRITE) {
?>
     <a href="../out/out.EditDocument.php?documentid=<?php echo $docID; ?>"><i class="icon-edit"></i></a>
<?php
				} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-edit"></i></span>
<?php
				}
?>
     <a class="addtoclipboard" rel="<?php echo "D".$docID; ?>" msg="<?php printMLText('splash_added_to_clipboard'); ?>" _href="../op/op.AddToClipboard.php?folderid=<?php echo $folder->getID(); ?>&type=document&id=<?php echo $docID; ?>" title="<?php printMLText("add_to_clipboard");?>"><i class="icon-copy"></i></a>
<?php
				print "</div>";
				print "</td>";
				print "</tr>\n";
			}
		}

		if ((count($subFolders) > 0)||(count($documents) > 0)) echo "</tbody>\n</table>\n";


		echo "</div>\n";

		$this->contentEnd();

		$this->htmlEndPage();
	} /* }}} */
}

?>
