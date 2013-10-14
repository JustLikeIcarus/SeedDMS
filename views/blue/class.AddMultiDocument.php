<?php
/**
 * Implementation of AddMultiDocument view
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
 * Class which outputs the html page for AddMultiDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddMultiDocument extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "view_folder", $folder);

?>
<script language="JavaScript">
var openDlg;
function chooseKeywords(target) {
	openDlg = open("out.KeywordChooser.php?target="+target, "openDlg", "width=500,height=400,scrollbars=yes,resizable=yes");
}
function chooseCategory(form, cats) {
	openDlg = open("out.CategoryChooser.php?form="+form+"&cats="+cats, "openDlg", "width=480,height=480,scrollbars=yes,resizable=yes,status=yes");
}
</script>

<?php
	$this->contentHeading(getMLText("add_document"));
	$this->contentContainerStart();

	// Retrieve a list of all users and groups that have review / approve
	// privileges.
	$docAccess = $folder->getReadAccessList();

	$this->printUploadApplet('../op/op.AddMultiDocument.php', array('folderid'=>$folder->getId()));

	$this->contentContainerEnd();
	$this->htmlEndPage();

	} /* }}} */
}
?>
