<?php
/**
 * Implementation of RemoveFolder view
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
 * Class which outputs the html page for RemoveFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveFolder extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "view_folder", $folder);
		$this->contentHeading(getMLText("rm_folder"));
		$this->contentContainerStart();
?>
<form action="../op/op.RemoveFolder.php" method="post" name="form1">
	<input type="Hidden" name="folderid" value="<?php print $folder->getID();?>">
	<input type="Hidden" name="showtree" value="<?php echo showtree();?>">
  <?php echo createHiddenFieldWithKey('removefolder'); ?>
	<p>
	<?php printMLText("confirm_rm_folder", array ("foldername" => htmlspecialchars($folder->getName())));?>
	</p>
	<p><input type="submit" value="<?php printMLText("rm_folder");?>"></p>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
