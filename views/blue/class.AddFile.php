<?php
/**
 * Implementation of AddFile view
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
 * Class which outputs the html page for AddFile view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddFile extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");

?>
<script language="JavaScript">
function checkForm()
{
	msg = "";
	if (document.form1.userfile.value == "") msg += "<?php printMLText("js_no_file");?>\n";
	if (document.form1.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
<?php
	if (isset($settings->_strictFormCheck) && $settings->_strictFormCheck) {
	?>
	if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
<?php
	}
?>
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}
</script>
<?php
		$this->contentHeading(getMLText("linked_files"));
		$this->contentContainerStart();

?>
<table>
<tr>
	<td class="warning"><?php echo getMLText("max_upload_size")." : ".ini_get( "upload_max_filesize"); ?></td>
</tr>
<tr>
  <td><?php printf(getMLText('link_alt_updatedocument'), "out.AddFile2.php?documentid=".$document->getId()); ?></td>
</tr>
</table><br>

<form action="../op/op.AddFile.php" enctype="multipart/form-data" method="post" name="form1" onsubmit="return checkForm();">
<input type="Hidden" name="documentid" value="<?php print $document->getId(); ?>">
<table>
<tr>
	<td><?php printMLText("local_file");?>:</td>
	<td><input type="File" name="userfile" size="60"></td>
</tr>
<tr>
	<td><?php printMLText("name");?>:</td>
	<td><input type="text" name="name" size="60"></td>
</tr>
<tr>
	<td><?php printMLText("comment");?>:</td>
	<td><textarea name="comment" rows="4" cols="80"></textarea></td>
</tr>
</table>

	<p><input type="Submit" value="<?php printMLText("add");?>"></p>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
