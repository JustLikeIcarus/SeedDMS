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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_ViewFolder extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$orderby = $this->params['orderby'];
		$enableFolderTree = $this->params['enableFolderTree'];
		$showtree = $this->params['showtree'];

		$folderid = $folder->getId();

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));

		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation(getFolderPathHTML($folder), "view_folder", $folder);

		if ($enableFolderTree) $this->printTreeNavigation($folderid,$showtree);

		$this->contentHeading(getMLText("folder_infos"));

		$owner = $folder->getOwner();
		$this->contentContainerStart();
		print "<table>\n<tr>\n".
					"<td>".getMLText("owner").":</td>\n".
					"<td><a class=\"infos\" href=\"mailto:".htmlspecialchars($owner->getEmail())."\">".htmlspecialchars($owner->getFullName())."</a>".
					"</td>\n</tr>\n<tr>\n".
					"<td>".getMLText("comment").":</td>\n".
					"<td>".htmlspecialchars($folder->getComment())."</td>\n</tr>\n";
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
		$this->contentContainerStart();

		$subFolders = $folder->getSubFolders($orderby);
		$subFolders = SeedDMS_Core_DMS::filterAccess($subFolders, $user, M_READ);
		$documents = $folder->getDocuments($orderby);
		$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, M_READ);

		if ((count($subFolders) > 0)||(count($documents) > 0)){
			print "<table class=\"folderView\">";
			print "<thead>\n<tr>\n";
			print "<th></th>\n";	
			print "<th><a href=\"../out/out.ViewFolder.php?folderid=". $folderid .($orderby=="n"?"":"&orderby=n")."\">".getMLText("name")."</a></th>\n";
			print "<th>".getMLText("owner")."</th>\n";
			print "<th>".getMLText("status")."</th>\n";
			print "<th>".getMLText("version")."</th>\n";
			print "<th>".getMLText("comment")."</th>\n";
			print "</tr>\n</thead>\n<tbody>\n";
		}
		else printMLText("empty_notify_list");


		foreach($subFolders as $subFolder) {

			$owner = $subFolder->getOwner();
			$comment = $subFolder->getComment();
			if (strlen($comment) > 50) $comment = substr($comment, 0, 47) . "...";
			$subsub = $subFolder->getSubFolders();
			$subsub = SeedDMS_Core_DMS::filterAccess($subsub, $user, M_READ);
			$subdoc = $subFolder->getDocuments();
			$subdoc = SeedDMS_Core_DMS::filterAccess($subdoc, $user, M_READ);
			
			print "<tr class=\"folder\">";
		//	print "<td><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></td>";
			print "<td><a href=\"out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\"><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></a></td>\n";
			print "<td><a href=\"out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\">" . htmlspecialchars($subFolder->getName()) . "</a></td>\n";
			print "<td>".htmlspecialchars($owner->getFullName())."</td>";
			print "<td colspan=\"1\"><small>".count($subsub)." ".getMLText("folders").", ".count($subdoc)." ".getMLText("documents")."</small></td>";
			print "<td></td>";
			print "<td>".htmlspecialchars($comment)."</td>";
			print "</tr>\n";
		}

		foreach($documents as $document) {

			$owner = $document->getOwner();
			$comment = $document->getComment();
			if (strlen($comment) > 50) $comment = substr($comment, 0, 47) . "...";
			$docID = $document->getID();
			if($latestContent = $document->getLatestContent()) {
				$version = $latestContent->getVersion();
				$status = $latestContent->getStatus();
				
				print "<tr>";

				if (file_exists($dms->contentDir . $latestContent->getPath()))
					print "<td><a href=\"../op/op.Download.php?documentid=".$docID."&version=".$version."\"><img class=\"mimeicon\" src=\"images/icons/".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\"></a></td>";
				else print "<td><img class=\"mimeicon\" src=\"images/icons/".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\"></td>";
				
				print "<td><a href=\"out.ViewDocument.php?documentid=".$docID."&showtree=".$showtree."\">" . htmlspecialchars($document->getName()) . "</a></td>\n";
				print "<td>".htmlspecialchars($owner->getFullName())."</td>";
				print "<td>";
				if ( $document->isLocked() ) {
					print "<img src=\"".$this->getImgPath("lock.png")."\" title=\"". getMLText("locked_by").": ".htmlspecialchars($document->getLockingUser()->getFullName())."\"> ";
				}
				print getOverallStatusText($status["status"])."</td>";
				print "<td>".$version."</td>";
				print "<td>".htmlspecialchars($comment)."</td>";
				print "</tr>\n";
			}
		}

		if ((count($subFolders) > 0)||(count($documents) > 0)) echo "</tbody>\n</table>\n";

		$this->contentContainerEnd();

		if ($enableFolderTree) print "</td></tr></table>";

		$this->contentEnd();

		$this->htmlEndPage();
	} /* }}} */
}

?>
