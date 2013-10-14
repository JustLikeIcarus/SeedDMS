<?php
/**
 * Implementation of SetExpires view
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
 * Class which outputs the html page for SetExpires view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SetExpires extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");
		$this->contentHeading(getMLText("set_expiry"));
		$this->contentContainerStart();
?>

<form action="../op/op.SetExpires.php" method="post">
<input type="hidden" name="documentid" value="<?php print $document->getID();?>">
	
<table>
<tr>
	<td><?php printMLText("expires");?>:</td>
	<td>
	<input type="radio" name="expires" value="false" <?php echo ($document->expires()?"":"checked") ?> ><?php printMLText("does_not_expire");?><br>
	<input type="radio" name="expires" value="true"  <?php echo ($document->expires()?"checked":"") ?> ><?php $this->printDateChooser(($document->expires()?$document->getExpires():-1), "exp");?>
	</td>
</tr>
</table>

<p>
<input type="submit" value="<?php printMLText("update");?>">
</p>
	
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
