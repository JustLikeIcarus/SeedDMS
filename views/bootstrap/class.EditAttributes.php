<?php
/**
 * Implementation of EditAttributes view
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
 * Class which outputs the html page for EditAttributes view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditAttributes extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$version = $this->params['version'];
		$attrdefs = $this->params['attrdefs'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

		$this->contentHeading(getMLText("edit_attributes"));
		$this->contentContainerStart();
?>
<form action="../op/op.EditAttributes.php" name="form1" method="POST">
	<?php echo createHiddenFieldWithKey('editattributes'); ?>
	<input type="Hidden" name="documentid" value="<?php print $document->getID();?>">
	<input type="Hidden" name="version" value="<?php print $version->getVersion();?>">
	<table class="table-condensed">
<?php
			if($attrdefs) {
				foreach($attrdefs as $attrdef) {
?>
<tr>
	<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
	<td><?php $this->printAttributeEditField($attrdef, $version->getAttributeValue($attrdef)) ?></td>
</tr>
<?php
				}
			}
?>
		<tr>
			<td colspan="2"><button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save") ?></button></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
