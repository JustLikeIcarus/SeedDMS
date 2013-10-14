<?php
/**
 * Implementation of DocumentChooser view
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
 * Class which outputs the html page for DocumentChooser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DocumentChooser extends SeedDMS_Blue_Style {
	var $user;
	var $form;

	function printTree($path, $level = 0) { /* {{{ */
		$folder = $path[$level];
		$subFolders = SeedDMS_Core_DMS::filterAccess($folder->getSubFolders(), $this->user, M_READ);
		$documents  = SeedDMS_Core_DMS::filterAccess($folder->getDocuments(), $this->user, M_READ);
		
		if ($level+1 < count($path))
			$nextFolderID = $path[$level+1]->getID();
		else
			$nextFolderID = -1;

		if ($level == 0) {
			print "<ul style='list-style-type: none;'>\n";
		}
		print "  <li>\n";
		print "<img class='treeicon' src=\"";
		if ($level == 0) $this->printImgPath("minus.png");
		else if (count($subFolders) + count($documents) > 0) $this->printImgPath("minus.png");
		else $this->printImgPath("blank.png");
		print "\" border=0>\n";
		if ($folder->getAccessMode($this->user) >= M_READ) {
			print "<img src=\"".$this->getImgPath("folder_opened.gif")."\" border=0>".htmlspecialchars($folder->getName())."\n";
		}
		else
			print "<img src=\"".$this->getImgPath("folder_opened.gif")."\" width=18 height=18 border=0>".htmlspecialchars($folder->getName())."\n";
		print "  </li>\n";

		print "<ul style='list-style-type: none;'>";

		for ($i = 0; $i < count($subFolders); $i++) {
			if ($subFolders[$i]->getID() == $nextFolderID)
				$this->printTree($path, $level+1);
			else {
				print "<li>\n";
				$subFolders_ = SeedDMS_Core_DMS::filterAccess($subFolders[$i]->getSubFolders(), $this->user, M_READ);
				$documents_  = SeedDMS_Core_DMS::filterAccess($subFolders[$i]->getDocuments(), $this->user, M_READ);
				
				if (count($subFolders_) + count($documents_) > 0)
					print "<a href=\"out.DocumentChooser.php?form=".$this->form."&folderid=".$subFolders[$i]->getID()."\"><img class='treeicon' src=\"".$this->getImgPath("plus.png")."\" border=0></a>";
				else
					print "<img class='treeicon' src=\"".$this->getImgPath("blank.png")."\">";
				print "<img src=\"".$this->getImgPath("folder_closed.gif")."\" border=0>".htmlspecialchars($subFolders[$i]->getName())."\n";
				print "</li>";
			}
		}
		for ($i = 0; $i < count($documents); $i++) {
			print "<li>\n";
			print "<img class='treeicon' src=\"images/blank.png\">";
			print "<a  class=\"foldertree_selectable\" href=\"javascript:documentSelected(".$documents[$i]->getID().",'".str_replace("'", "\\'", htmlspecialchars($documents[$i]->getName()))."');\"><img src=\"images/file.gif\" border=0>".htmlspecialchars($documents[$i]->getName())."</a>";
			print "</li>";
		}

		print "</ul>\n";
		if ($level == 0) {
			print "</ul>\n";
		}
		
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$this->user = $this->params['user'];
		$folder = $this->params['folder'];
		$this->form = $this->params['form'];

		$this->htmlStartPage(getMLText("choose_target_document"));
		$this->globalBanner();
		$this->pageNavigation(getMLText("choose_target_document"));
?>

<script language="JavaScript">
var targetName;
var targetID;

function documentSelected(id, name) {
	targetName.value = name;
	targetID.value = id;
	window.close();
	return true;
}
</script>

<?php
		echo "<input type=\"text\" class=\"autocomplete\" />";
		$this->contentContainerStart();
		echo "<div id=\"resultautocomplete\"></div>";
		$this->contentContainerEnd();
		$this->contentContainerStart();
		$this->printTree($folder->getPath());
		$this->contentContainerEnd();
?>

<script language="JavaScript">
targetName = opener.document.<?php echo $this->form?>.docname<?php print $this->form ?>;
targetID   = opener.document.<?php echo $this->form?>.docid<?php print $this->form ?>;
</script>

<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
