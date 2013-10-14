<?php
/**
 * Implementation of FolderChooser view
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
 * Class which outputs the html page for FolderChooser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_FolderChooser extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$mode = $this->params['mode'];
		$exclude = $this->params['exclude'];
		$form = $this->params['form'];
		$rootfolderid = $this->params['rootfolderid'];

		$this->htmlStartPage(getMLText("choose_target_folder"));
		$this->globalBanner();
		$this->pageNavigation(getMLText("choose_target_folder"));
?>

<script language="JavaScript">
function toggleTree(id){
	obj = document.getElementById("tree" + id);
	if ( obj.style.display == "none" ) obj.style.display = "";
	else obj.style.display = "none";
}

var targetName;
var targetID;

function folderSelected(id, name) {
	targetName.value = name;
	targetID.value = id;
	window.close();
	return true;
}
</script>


<?php
		$this->contentContainerStart();
		$this->printFoldersTree($mode, $exclude, $rootfolderid);
		$this->contentContainerEnd();
?>


<script language="JavaScript">
targetName = opener.document.<?php echo $form?>.targetname<?php print $form ?>;
targetID   = opener.document.<?php echo $form?>.targetid<?php print $form ?>;
</script>

<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
