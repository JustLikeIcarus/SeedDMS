<?php
/**
 * Implementation of RemoveFolderFiles view
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
 * Class which outputs the html page for RemoveFolderFiles view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveFolderFiles extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];

		$this->htmlStartPage(getMLText("files_deletion"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->contentHeading(getMLText("files_deletion"));
		$this->contentContainerStart();
?>
<form action="../op/op.RemoveFolderFiles.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('removefolderfiles'); ?>
	<input type="hidden" name="folderid" value="<?php echo $folder->getID()?>">
	<p><?php printMLText("confirm_rm_folder_files", array ("foldername" => htmlspecialchars($folder->getName())));?></p>
	<input type="submit" value="<?php printMLText("accept");?>">
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
