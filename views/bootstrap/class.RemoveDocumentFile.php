<?php
/**
 * Implementation of RemoveDocumentFile view
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
 * Class which outputs the html page for RemoveDocumentFile view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveDocumentFile extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$file = $this->params['file'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);
		$this->contentHeading(getMLText("rm_file"));
		$this->contentContainerStart();

?>
<form action="../op/op.RemoveDocumentFile.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('removedocumentfile'); ?>
	<input type="Hidden" name="documentid" value="<?php echo $document->getID()?>">
	<input type="Hidden" name="fileid" value="<?php echo $file->getID()?>">
	<p><?php printMLText("confirm_rm_file", array ("documentname" => htmlspecialchars($document->getName()), "name" => htmlspecialchars($file->getName())));?></p>
	<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("rm_file");?></button>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
