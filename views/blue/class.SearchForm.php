<?php
/**
 * Implementation of SearchForm view
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
 * Class which outputs the html page for SearchForm view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SearchForm extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$attrdefs = $this->params['attrdefs'];
		$allCats = $this->params['allcategories'];
		$allUsers = $this->params['allusers'];
		$enablefullsearch = $this->params['enablefullsearch'];

		$this->htmlStartPage(getMLText("search"));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "", $folder);
?>
<script language="JavaScript">
function checkForm()
{
	msg = "";
	if (document.form1.query.value == "")
	{
		if (!document.form1.creationdate.checked && !document.form1.lastupdate.checked &&
				!document.form1.pendingReview.checked && !document.form1.pendingApproval.checked)
			msg += "<?php printMLText("js_no_query");?>\n";
	}
	
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
		$this->contentHeading(getMLText("search"));
		$this->contentContainerStart();
?>
<?php
	if($enablefullsearch) {
?>
<div style="width: 35%; float: left;">
<?php
	}
?>
<h2><?php echo getMLText('databasesearch') ?></h2>
<form action="../op/op.Search.php" name="form1" onsubmit="return checkForm();">
<table class="searchform">
<tr>
<td><?php printMLText("search_query");?>:</td>
<td>
<input name="query">
<select name="mode">
<option value="1" selected><?php printMLText("search_mode_and");?><br>
<option value="0"><?php printMLText("search_mode_or");?>
</select>
<br />
<a href="javascript:chooseKeywords('form1.query');"><?php printMLText("use_default_keywords");?></a>
<script language="JavaScript">
var openDlg;

function chooseKeywords(target) {
	openDlg = open("out.KeywordChooser.php?target="+target, "openDlg", "width=500,height=400,scrollbars=yes,resizable=yes");
}
</script>
</td>
</tr>
<tr>
<td><?php printMLText("search_in");?>:</td>
<td><ul class="actions">
<li class="first"><input type="checkbox" id="keywords" name="searchin[]" value="1"><label for="keywords"><?php printMLText("keywords");?></label> (<?php printMLText('documents_only'); ?>)</li>
<li><input type="checkbox" name="searchin[]" id="searchName" value="2"><label for="searchName"><?php printMLText("name");?></label></li>
<li><input type="checkbox" name="searchin[]" id="comment" value="3"><label for="comment"><?php printMLText("comment");?></label></li>
<li><input type="checkbox" name="searchin[]" id="attributes" value="4"><label for="attributes"><?php printMLText("attributes");?></label></li>
</ul>
</td>
</tr>
<?php
		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
?>
<tr>
	<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
	<td><?php $this->printAttributeEditField($attrdef, '') ?></td>
</tr>
<?php
			}
		}
?>
<tr>
<td><?php printMLText("category");?>:<br />(<?php printMLText('documents_only'); ?>)</td>
<td>
<select name="categoryids[]" multiple>
<option value="-1"><?php printMLText("all_categories");?>
<?php
		foreach ($allCats as $catObj) {
			print "<option value=\"".$catObj->getID()."\">" . htmlspecialchars($catObj->getName()) . "\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("status");?>:<br />(<?php printMLText('documents_only'); ?>)</td>
<td>
<ul class="actions">
<li class="first"><input type="checkbox" id="pendingReview" name="pendingReview" value="1"><label for='pendingReview'><?php printOverallStatusText(S_DRAFT_REV);?></label></li>
<li><input type="checkbox" id="pendingApproval" name="pendingApproval" value="1"><label for='pendingApproval'><?php printOverallStatusText(S_DRAFT_APP);?></label></li>
<li><input type="checkbox" id="released" name="released" value="1"><label for='released'><?php printOverallStatusText(S_RELEASED);?></label></li>
<li><input type="checkbox" id="rejected" name="rejected" value="1"><label for='rejected'><?php printOverallStatusText(S_REJECTED);?></label></li>
<li><input type="checkbox" id="obsolete" name="obsolete" value="1"><label for='obsolete'><?php printOverallStatusText(S_OBSOLETE);?></label></li>
<li><input type="checkbox" id="expired" name="expired" value="1"><label for='expired'><?php printOverallStatusText(S_EXPIRED);?></label></li>
</ul>
</td>
</tr>
<tr>
<td><?php printMLText("owner");?>:</td>
<td>
<select name="ownerid">
<option value="-1"><?php printMLText("all_users");?>
<?php
		foreach ($allUsers as $userObj) {
			if ($userObj->isGuest())
				continue;
			print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin()." - ".$userObj->getFullName()) . "\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("under_folder")?>:</td>
<td><?php $this->printFolderChooser("form1", M_READ, -1, $folder);?></td>
</tr>
<tr>
<td><?php printMLText("creation_date");?>:</td>
<td>
<input type="Checkbox" name="creationdate" value="true">
<?php
		printMLText("between");
		print "&nbsp;&nbsp;";
		$this->printDateChooser(-1, "createstart");
		print "&nbsp;&nbsp;";
		printMLText("and");
		print "&nbsp;&nbsp;";
		$this->printDateChooser(-1, "createend");
?>
</td>
</tr>
<?php
/*
echo "<tr>\n<td>".getMLText("last_update").":</td>\n";
echo "<td><input type=\"Checkbox\" name=\"lastupdate\" value=\"true\">";
printMLText("between");
print "&nbsp;&nbsp;";
$this->printDateChooser(-1, "updatestart");
print "&nbsp;&nbsp;";
printMLText("and");
print "&nbsp;&nbsp;";
$this->printDateChooser(-1, "updateend");
echo "</td>\n</tr>\n";
*/
?>
<tr>
<td colspan="2"><input type="Submit" value="<?php printMLText("search"); ?>"></td>
</tr>

</table>

</form>
<?php
			if($enablefullsearch) {
?>
</div>
<div style="width: 35%; float: left; margin-left: 20px;">
<form action="../op/op.SearchFulltext.php" name="form2" onsubmit="return checkForm();">
<table class="searchform">
<h2><?php echo getMLText('fullsearch') ?></h2>
<tr>
<td><?php printMLText("search_query");?>:</td>
<td>
<input name="query">
<!--
<select name="mode">
<option value="1" selected><?php printMLText("search_mode_and");?><br>
<option value="0"><?php printMLText("search_mode_or");?>
</select>
-->
</td>
</tr>
<tr>
<td><?php printMLText("category_filter");?>:</td>
<td>
<select name="categoryids[]" multiple>
<!--
<option value="-1"><?php printMLText("all_categories");?>
-->
<?php
			$allCats = $dms->getDocumentCategories();
			foreach ($allCats as $catObj) {
				print "<option value=\"".$catObj->getID()."\">" . htmlspecialchars($catObj->getName()) . "\n";
			}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("owner");?>:</td>
<td>
<select name="ownerid">
<option value="-1"><?php printMLText("all_users");?>
<?php
			foreach ($allUsers as $userObj) {
				if ($userObj->isGuest())
					continue;
				print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin()." - ".$userObj->getFullName()) . "\n";
			}
?>
</select>
</td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="<?php printMLText("search"); ?>"></td>
</tr>
</table>

</form>
</div>
<div style="clear: both"></div>
<?php
		}
?>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
