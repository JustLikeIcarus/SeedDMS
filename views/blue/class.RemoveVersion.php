<?php
/**
 * Implementation of RemoveVersion view
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
 * Class which outputs the html page for RemoveVersion view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveVersion extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$version = $this->params['version'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");
		$this->contentHeading(getMLText("rm_version"));
		$this->contentContainerStart();
?>
<form action="../op/op.RemoveVersion.php" name="form1" method="post">
	<?php echo createHiddenFieldWithKey('removeversion'); ?>
	<input type="hidden" name="documentid" value="<?php echo $document->getID()?>">
	<input type="hidden" name="version" value="<?php echo $version->getVersion()?>">
	<p><?php printMLText("confirm_rm_version", array ("documentname" => htmlspecialchars($document->getName()), "version" => $version->getVersion()));?></p>
	<input type="submit" value="<?php printMLText("rm_version");?>">
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
