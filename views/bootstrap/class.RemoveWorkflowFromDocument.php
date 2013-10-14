<?php
/**
 * Implementation of RemoveWorkflowFromDocument view
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
class SeedDMS_View_RemoveWorkflowFromDocument extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];

		$latestContent = $document->getLatestContent();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);
		$this->contentHeading(getMLText("rm_workflow"));

		$currentstate = $latestContent->getWorkflowState();
		$wkflog = $latestContent->getWorkflowLog();
		$workflow = $latestContent->getWorkflow();

		$msg = "The document is currently in state: ".$currentstate->getName()."<br />";
		if($wkflog) {
			foreach($wkflog as $entry) {
				if($entry->getTransition()->getNextState()->getID() == $currentstate->getID()) {
					$enterdate = $entry->getDate();
					$d = strptime($enterdate, '%Y-%m-%d %H:%M:%S');
					$enterts = mktime($d['tm_hour'], $d['tm_min'], $d['tm_sec'], $d['tm_mon']+1, $d['tm_mday'], $d['tm_year']+1900);
				}
			}
			$msg .= "The state was entered at ".$enterdate." which was ";
			$msg .= getReadableDuration((time()-$enterts))." ago.<br />";
		}
		$msg .= "The document may stay in this state for ".$currentstate->getMaxTime()." sec.";
		$this->infoMsg($msg);

		$this->contentContainerStart();
		// Display the Workflow form.
?>
	<div class="row-fluid">
	<div class="span4">
	<p><?php printMLText("rm_workflow_warning"); ?></p>
	<form method="post" action="../op/op.RemoveWorkflowFromDocument.php" name="form1" onsubmit="return checkForm();">
	<?php echo createHiddenFieldWithKey('removeworkflowfromdocument'); ?>
	<table>
	<tr><td></td><td>
	<input type='hidden' name='documentid' value='<?php echo $document->getId(); ?>'/>
	<input type='hidden' name='version' value='<?php echo $latestContent->getVersion(); ?>'/>
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

		if($wkflog) {
			$this->contentContainerStart();
			echo "<table class=\"table-condensed\">";
			echo "<tr><th>".getMLText('action')."</th><th>Start state</th><th>End state</th><th>".getMLText('date')."</th><th>".getMLText('user')."</th><th>".getMLText('comment')."</th></tr>";
			foreach($wkflog as $entry) {
				echo "<tr>";
				echo "<td>".getMLText('action_'.$entry->getTransition()->getAction()->getName())."</td>";
				echo "<td>".$entry->getTransition()->getState()->getName()."</td>";
				echo "<td>".$entry->getTransition()->getNextState()->getName()."</td>";
				echo "<td>".$entry->getDate()."</td>";
				echo "<td>".$entry->getUser()->getFullname()."</td>";
				echo "<td>".$entry->getComment()."</td>";
				echo "</tr>";
			}
			echo "</table>\n";
			$this->contentContainerEnd();
		}

		$this->htmlEndPage();
	} /* }}} */
}
?>
