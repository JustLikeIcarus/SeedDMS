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
require_once("class.Bootstrap.php");

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
class SeedDMS_View_SearchForm extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$attrdefs = $this->params['attrdefs'];
		$allCats = $this->params['allcategories'];
		$allUsers = $this->params['allusers'];
		$enablefullsearch = $this->params['enablefullsearch'];
		$workflowmode = $this->params['workflowmode'];

		$this->htmlStartPage(getMLText("search"));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation(getMLText("search"), "");
?>
<script language="JavaScript">
function checkForm()
{
	msg = new Array()
	if (document.form1.query.value == "")
	{
		if (!document.form1.creationdate.checked && !document.form1.lastupdate.checked &&
				!document.form1.pendingReview.checked && !document.form1.pendingApproval.checked)
			msg.push("<?php printMLText("js_no_query");?>");
	}
	
	if (msg != "") {
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
  <ul class="nav nav-tabs" id="searchtab">
	  <li class="active"><a data-target="#database" data-toggle="tab"><?php printMLText('databasesearch'); ?></a></li>
	  <li><a data-target="#full" data-toggle="tab"><?php printMLText('fullsearch'); ?></a></li>
	</ul>

	<div class="tab-content">
	  <div class="tab-pane active" id="database">
<?php
	$this->contentContainerStart();
?>
<form action="../op/op.Search.php" name="form1" onsubmit="return checkForm();">
<table class="table-condensed">
<tr>
<td><?php printMLText("search_query");?>:</td>
<td>
<input type="text" name="query">
<select name="mode">
<option value="1" selected><?php printMLText("search_mode_and");?>
<option value="0"><?php printMLText("search_mode_or");?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("search_in");?>:</td>
<td>
<label class="checkbox" for="keywords"><input type="checkbox" id="keywords" name="searchin[]" value="1"><?php printMLText("keywords");?> (<?php printMLText('documents_only'); ?>)</label>
<label class="checkbox" for="searchName"><input type="checkbox" name="searchin[]" id="searchName" value="2"><?php printMLText("name");?></label>
<label class="checkbox" for="comment"><input type="checkbox" name="searchin[]" id="comment" value="3"><?php printMLText("comment");?></label>
<label class="checkbox" for="attributes"><input type="checkbox" name="searchin[]" id="attributes" value="4"><?php printMLText("attributes");?></label>
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
<?php if($workflowmode == 'traditional') { ?>
<label class="checkbox" for='pendingReview'><input type="checkbox" id="pendingReview" name="pendingReview" value="1"><?php printOverallStatusText(S_DRAFT_REV);?></label>
<label class="checkbox" for='pendingApproval'><input type="checkbox" id="pendingApproval" name="pendingApproval" value="1"><?php printOverallStatusText(S_DRAFT_APP);?></label>
<?php } else { ?>
<label class="checkbox" for='inWorkflow'><input type="checkbox" id="inWorkflow" name="inWorkflow" value="1"><?php printOverallStatusText(S_IN_WORKFLOW);?></label>
<?php } ?>
<label class="checkbox" for='released'><input type="checkbox" id="released" name="released" value="1"><?php printOverallStatusText(S_RELEASED);?></label>
<label class="checkbox" for='rejected'><input type="checkbox" id="rejected" name="rejected" value="1"><?php printOverallStatusText(S_REJECTED);?></label>
<label class="checkbox" for='obsolete'><input type="checkbox" id="obsolete" name="obsolete" value="1"><?php printOverallStatusText(S_OBSOLETE);?></label>
<label class="checkbox" for='expired'><input type="checkbox" id="expired" name="expired" value="1"><?php printOverallStatusText(S_EXPIRED);?></label>
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
        <label class="checkbox inline">
				  <input type="checkbox" name="creationdate" value="true" /><?php printMLText("between");?><br>
        </label>
        <span class="input-append date" id="createstartdate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span3" size="16" name="createstart" type="text" value="<?php echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>&nbsp;
				<?php printMLText("and"); ?>
        <span class="input-append date" id="createenddate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span3" size="16" name="createend" type="text" value="<?php echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
</td>
</tr>
<tr>
<td><?php printMLText("expires");?>:<br />(<?php printMLText('documents_only'); ?>)</td>
<td>
        <label class="checkbox inline">
				  <input type="checkbox" name="expirationdate" value="true" /><?php printMLText("between");?><br>
        </label>
        <span class="input-append date" id="expirationstartdate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span3" size="16" name="expirationstart" type="text" value="<?php echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>&nbsp;
				<?php printMLText("and"); ?>
        <span class="input-append date" id="expirationenddate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span3" size="16" name="expirationend" type="text" value="<?php echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
</td>
</tr>
<tr>
<td></td><td><button type="submit" class="btn"><i class="icon-search"> <?php printMLText("search"); ?></button></td>
</tr>

</table>
</form>
<?php
	$this->contentContainerEnd();
?>
</div>
<?php
			if($enablefullsearch) {
?>
	  <div class="tab-pane" id="full">
<?php
	$this->contentContainerStart();
?>
<form action="../op/op.SearchFulltext.php" name="form2" onsubmit="return checkForm();">
<table class="table-condensed">
<tr>
<td><?php printMLText("search_query");?>:</td>
<td>
<input type="text" name="query">
<!--
<select name="mode">
<option value="1" selected><?php printMLText("search_mode_and");?>
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
<td></td><td><button type="submit" class="btn"><i class="icon-search"> <?php printMLText("search"); ?></button></td>
</tr>
</table>

</form>
<?php
		$this->contentContainerEnd();
	}
?>
</div>
</div>

<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
