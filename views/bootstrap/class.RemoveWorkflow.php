<?php
/**
 * Implementation of RemoveWorkflow view
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
 * Class which outputs the html page for Removeorkflow view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveWorkflow extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$workflow = $this->params['workflow'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($workflow->getName()))));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("rm_workflow"));
		$this->contentContainerStart();
		// Display the Workflow form.
?>
	<div class="row-fluid">
	<div class="span4">
	<p><?php printMLText("rm_workflow_warning"); ?></p>
	<form method="post" action="../op/op.RemoveWorkflow.php" name="form1" onsubmit="return checkForm();">
	<?php echo createHiddenFieldWithKey('removeworkflow'); ?>
	<table>
	<tr><td></td><td>
	<input type='hidden' name='workflowid' value='<?php echo $workflow->getId(); ?>'/>
	<button type='submit' class="btn"><i class="icon-remove"></i> <?php printMLText("rm_workflow"); ?></button>
	</td></tr></table>
	</form>
	</div>
	<div id="workflowgraph" class="span8">
	<iframe src="out.WorkflowGraph.php?workflow=<?php echo $workflow->getID(); ?>" width="100%" height="500" style="border: 1px solid #AAA;"></iframe>
	</div>
	</div>
<?php
		$this->contentContainerEnd();

		$this->htmlEndPage();
	} /* }}} */
}
?>
