<?php
/**
 * Implementation of WorkspaceActionsMgr view
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
 * Class which outputs the html page for WorkspaceActionsMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_WorkflowActionsMgr extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$selworkflowaction = $this->params['selworkflowaction'];

		$workflowactions = $dms->getAllWorkflowActions();

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

?>
<script language="JavaScript">

function checkForm(num)
{
	msg = new Array()
	eval("var formObj = document.form" + num + ";");

	if (formObj.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
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


obj = -1;
function showWorkflowAction(selectObj) {
	if (obj != -1) {
		obj.style.display = "none";
	}

	id = selectObj.options[selectObj.selectedIndex].value;
	if (id == -1)
		return;

	obj = document.getElementById("keywords" + id);
	obj.style.display = "";

}
</script>
<?php
		$this->contentHeading(getMLText("workflow_actions_management"));
?>

<div class="row-fluid">
<div class="span4">
<div class="well">
<?php echo getMLText("selection")?>:
<select onchange="showWorkflowAction(this)" id="selector" class="span9">
<option value="-1"><?php echo getMLText("choose_workflow_action")?>
<option value="0"><?php echo getMLText("add_workflow_action")?>
<?php
		$selected=0;
		$count=2;
		foreach ($workflowactions as $currWorkflowAction) {
			if ($selworkflowaction && $currWorkflowAction->getID()==$selworkflowaction->getID()) $selected=$count;
			print "<option value=\"".$currWorkflowAction->getID()."\">" . htmlspecialchars($currWorkflowAction->getName());
			$count++;
		}
?>
</select>
</div>
</div>

<div class="span8">
<div class="well">
<table class="table-condensed">
	<tr>
	<td id="keywords0" style="display : none;">

	<form action="../op/op.WorkflowActionsMgr.php" method="post" name="form0" onsubmit="return checkForm('0');">
  <?php echo createHiddenFieldWithKey('addworkflowaction'); ?>
	<input type="Hidden" name="action" value="addworkflowaction">
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("workflow_action_name");?>:</td>
			<td><input type="text" name="name"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" class="btn" value="<?php printMLText("add_workflow_action");?>"></td>
		</tr>
	</table>
	</form>
	</td>

<?php
		foreach ($workflowactions as $currWorkflowAction) {

			print "<td id=\"keywords".$currWorkflowAction->getID()."\" style=\"display : none;\">";
?>
	<table class="table-condensed">
		<tr>
			<td></td>
			<td>
<?php
			if($currWorkflowAction->isUsed()) {
?>
				<p><?php echo getMLText('workflow_action_in_use') ?></p>
<?php
			} else {
?>
<form class="form-inline" action="../op/op.RemoveWorkflowAction.php" method="post">
  <?php echo createHiddenFieldWithKey('removeworkflowaction'); ?>
	<input type="hidden" name="workflowactionid" value="<?php print $currWorkflowAction->getID();?>">
	<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("rm_workflow_action");?></button>
</form>
<?php
			}
?>
			</td>
		</tr>
	<form action="../op/op.WorkflowActionsMgr.php" method="post" name="form<?php print $currWorkflowAction->getID();?>" onsubmit="return checkForm('<?php print $currWorkflowAction->getID();?>');">
	<?php echo createHiddenFieldWithKey('editworkflowaction'); ?>
	<input type="Hidden" name="workflowactionid" value="<?php print $currWorkflowAction->getID();?>">
	<input type="Hidden" name="action" value="editworkflowaction">
		<tr>
			<td><?php printMLText("workflow_action_name");?>:</td>
			<td><input type="text" name="name" value="<?php print htmlspecialchars($currWorkflowAction->getName());?>"></td>
		</tr>
		<tr>
			<td></td>
			<td><button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button></td>
		</tr>
	</form>
	</table>
</td>
<?php  } ?>
</tr></table>
</div>
</div>
</div>

<script language="JavaScript">

sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showWorkflowAction(sel);

</script>


<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>

