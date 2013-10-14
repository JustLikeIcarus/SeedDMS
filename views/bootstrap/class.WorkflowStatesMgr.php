<?php
/**
 * Implementation of WorkspaceStatesMgr view
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
 * Class which outputs the html page for WorkspaceStatesMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_WorkflowStatesMgr extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$selworkflowstate = $this->params['selworkflowstate'];

		$workflowstates = $dms->getAllWorkflowStates();

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

?>
<script language="JavaScript">

function checkForm(num)
{
	msg = new Array();
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
function showWorkflowState(selectObj) {
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
		$this->contentHeading(getMLText("workflow_states_management"));
?>

<div class="row-fluid">
<div class="span4">
<div class="well">
<?php echo getMLText("selection")?>:
<select onchange="showWorkflowState(this)" id="selector" class="span9">
<option value="-1"><?php echo getMLText("choose_workflow_state")?>
<option value="0"><?php echo getMLText("add_workflow_state")?>
<?php
		$selected=0;
		$count=2;
		foreach ($workflowstates as $currWorkflowState) {
			if ($selworkflowstate && $currWorkflowState->getID()==$selworkflowstate->getID()) $selected=$count;
			print "<option value=\"".$currWorkflowState->getID()."\">" . htmlspecialchars($currWorkflowState->getName());
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

	<form action="../op/op.WorkflowStatesMgr.php" method="post" name="form0" onsubmit="return checkForm('0');">
  <?php echo createHiddenFieldWithKey('addworkflowstate'); ?>
	<input type="Hidden" name="action" value="addworkflowstate">
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("workflow_state_name");?>:</td>
			<td><input type="text" name="name"></td>
		</tr>
		<tr>
			<td><?php printMLText("workflow_state_docstatus");?>:</td>
			<td><select name="docstatus">
				<option value=""><?php printMLText('keep_doc_status'); ?></option>
				<option value="<?php echo S_RELEASED; ?>"><?php printMLText('released'); ?></option>
				<option value="<?php echo S_REJECTED; ?>"><?php printMLText('rejected'); ?></option>
			</select></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" class="btn" value="<?php printMLText("add_workflow_state");?>"></td>
		</tr>
	</table>
	</form>
	</td>

<?php
		foreach ($workflowstates as $currWorkflowState) {

			print "<td id=\"keywords".$currWorkflowState->getID()."\" style=\"display : none;\">";
?>
	<table class="table-condensed">
		<tr>
			<td></td>
			<td>
<?php
			if($currWorkflowState->isUsed()) {
?>
				<p><?php echo getMLText('workflow_state_in_use') ?></p>
<?php
			} else {
?>
<form class="form-inline" action="../op/op.RemoveWorkflowState.php" method="post">
  <?php echo createHiddenFieldWithKey('removeworkflowstate'); ?>
	<input type="hidden" name="workflowstateid" value="<?php print $currWorkflowState->getID();?>">
	<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("rm_workflow_state");?></button>
</form>
<?php
			}
?>
			</td>
		</tr>
	<form action="../op/op.WorkflowStatesMgr.php" method="post" name="form<?php print $currWorkflowState->getID();?>" onsubmit="return checkForm('<?php print $currWorkflowState->getID();?>');">
	<?php echo createHiddenFieldWithKey('editworkflowstate'); ?>
	<input type="Hidden" name="workflowstateid" value="<?php print $currWorkflowState->getID();?>">
	<input type="Hidden" name="action" value="editworkflowstate">
		<tr>
			<td><?php printMLText("workflow_state_name");?>:</td>
			<td><input type="text" name="name" value="<?php print htmlspecialchars($currWorkflowState->getName());?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("workflow_state_docstatus");?>:</td>
			<td><select name="docstatus">
				<option value=""><?php printMLText('keep_doc_status'); ?></option>
				<option value="<?php echo S_RELEASED; ?>" <?php if($currWorkflowState->getDocumentStatus() == S_RELEASED) echo "selected"; ?>><?php printMLText('released'); ?></option>
				<option value="<?php echo S_REJECTED; ?>" <?php if($currWorkflowState->getDocumentStatus() == S_REJECTED) echo "selected"; ?>><?php printMLText('rejected'); ?></option>
			</select></td>
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
showWorkflowState(sel);

</script>


<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>

