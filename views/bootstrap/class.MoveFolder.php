<?php
/**
 * Implementation of MoveFolder view
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
 * Class which outputs the html page for MoveFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_MoveFolder extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$target = $this->params['target'];

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true), "view_folder", $folder);
		$this->contentHeading(getMLText("move_folder"));
		$this->contentContainerStart();

?>
<form action="../op/op.MoveFolder.php" name="form1">
	<input type="Hidden" name="folderid" value="<?php print $folder->getID();?>">
	<input type="Hidden" name="showtree" value="<?php echo showtree();?>">
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("choose_target_folder");?>:</td>
			<td><?php $this->printFolderChooser("form1", M_READWRITE, $folder->getID(), $target);?></td>
		</tr>
		<tr>
			<td></td>
			<td><input class="btn" type="submit" value="<?php printMLText("move_folder"); ?>"></td>
		</tr>
	</table>
	</form>


<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
