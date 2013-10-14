<?php
/**
 * Implementation of EditDocument view
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
 * Class which outputs the html page for EditDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditDocument extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$attrdefs = $this->params['attrdefs'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

?>
<script language="JavaScript">
function checkForm()
{
	msg = new Array();
	if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>)";
<?php
	if ($strictformcheck) {
	?>
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
	if (document.form1.keywords.value == "") msg.push("<?php printMLText("js_no_keywords");?>");
<?php
	}
?>
	if (msg != "")
	{
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}
</script>

<?php
		$this->contentHeading(getMLText("edit_document_props"));
		$this->contentContainerStart();

		if($document->expires())
			$expdate = date('d-m-Y', $document->getExpires());
		else
			$expdate = '';
?>
<form action="../op/op.EditDocument.php" name="form1" onsubmit="return checkForm();" method="post">
	<input type="hidden" name="documentid" value="<?php echo $document->getID() ?>">
	<table cellpadding="3">
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" value="<?php print htmlspecialchars($document->getName());?>" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"><?php print htmlspecialchars($document->getComment());?></textarea></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("keywords");?>:</td>
			<td class="standardText">
<?php
	$this->printKeywordChooser('form1', $document->getKeywords());
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td>
        <select class="chzn-select" name="categories[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<?php
			$categories = $dms->getDocumentCategories();
			foreach($categories as $category) {
				echo "<option value=\"".$category->getID()."\"";
				if(in_array($category, $document->getCategories()))
					echo " selected";
				echo ">".$category->getName()."</option>";	
			}
?>
				</select>
      </td>
		</tr>
		<tr>
			<td><?php printMLText("expires");?>:</td>
			<td>
        <span class="input-append date span12" id="expirationdate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span3" size="16" name="expdate" type="text" value="<?php echo $expdate; ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span><br />
        <label class="checkbox inline">
				  <input type="checkbox" name="expires" value="false"<?php if (!$document->expires()) print " checked";?>><?php printMLText("does_not_expire");?><br>
        </label>
			</td>
		</tr>
<?php
		if ($folder->getAccessMode($user) > M_READ) {
			print "<tr>";
			print "<td class=\"inputDescription\">" . getMLText("sequence") . ":</td>";
			print "<td>";
			$this->printSequenceChooser($folder->getDocuments(), $document->getID());
			print "</td></tr>";
		}
		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
?>
		<tr>
			<td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
			<td><?php $this->printAttributeEditField($attrdef, $document->getAttributeValue($attrdef)) ?></td>
		</tr>
<?php
			}
		}
?>
		<tr>
			<td></td>
			<td><button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
