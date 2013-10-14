<?php
/**
 * Implementation of WorkspaceMgr view
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
 * Class which outputs the html page for WorkspaceMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_WorkflowGraph extends SeedDMS_Bootstrap_Style {

	function printGraph() { /* {{{ */
		$transitions = $this->workflow->getTransitions();	
		if($transitions) {

			foreach($transitions as $transition) {
				$action = $transition->getAction();
				$maxtime = $transition->getMaxTime();
				$state = $transition->getState();
				$nextstate = $transition->getNextState();

				if(1 || !isset($this->actions[$action->getID()])) {
					$color = "#4B4";
					$iscurtransition = $this->curtransition && $transition->getID() == $this->curtransition->getID();
					if($iscurtransition) {
						$color = "#D00";
					} else {
						if($this->wkflog) {
							foreach($this->wkflog as $entry) {
								if($entry->getTransition()->getID() == $transition->getID()) {
									$color = "#DDD";
									break;
								}
							}
						}
					}
					$this->actions[$action->getID()] = $action->getID();
					$transusers = $transition->getUsers();
					$unames = array();
					foreach($transusers as $transuser) {
						$unames[] = $transuser->getUser()->getFullName();
					}
					echo "ggg.addNode(\"A".$transition->getID()."-".$action->getID()."\", { render: render_action, maxtime: '".implode(", ", $unames)."', label : \"".$action->getName()."\", color: '".$color."' });\n";
				}

				if(!isset($this->states[$state->getID()])) {
					$this->states[$state->getID()] = $state;
					$initstate = '';
					if($state == $this->workflow->getInitState())
						$initstate = " (START)";
					echo "ggg.addNode(\"S".$state->getID()."\", { label : \"".$state->getName()." ".$initstate."\" });\n";
				}
				if(!isset($this->states[$nextstate->getID()])) {
					$this->states[$state->getID()] = $nextstate;
					echo "ggg.addNode(\"S".$nextstate->getID()."\", { label : \"".$nextstate->getName()."\" });\n";
				}
			}

			foreach($transitions as $transition) {
				if(!in_array($transition->getID(), $this->seentrans)) {
					$state = $transition->getState();
					$nextstate = $transition->getNextState();
					$action = $transition->getAction();
					$iscurtransition = $this->curtransition && $transition->getID() == $this->curtransition->getID();

					echo "ggg.addEdge(\"S".$state->getID()."\",\"A".$transition->getID()."-".$action->getID()."\", { ".($iscurtransition ? "stroke: '#D00', 'stroke-width': '2px'" : "")." });\n";
					echo "ggg.addEdge(\"A".$transition->getID()."-".$action->getID()."\",\"S".$nextstate->getID()."\", { ".($iscurtransition ? "stroke: '#D00', 'stroke-width': '2px'" : "")." });\n";
					$this->seentrans[] = $transition->getID();
				}
			}
		}
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$this->workflow = $this->params['workflow'];
		$this->curtransition = $this->params['transition'];
		$document = $this->params['document'];

		if($document) {
			$latestContent = $document->getLatestContent();
			$this->wkflog = $latestContent->getWorkflowLog();
		} else {
			$this->wkflog = array();
		}

		$this->htmlAddHeader(
			'<script type="text/javascript" src="../styles/bootstrap/dracula/raphael-min.js"></script>'."\n".
			'<script type="text/javascript" src="../styles/bootstrap/dracula/dracula_graffle.js"></script>'."\n".
			'<script type="text/javascript" src="../styles/bootstrap/dracula/dracula_graph.js"></script>'."\n".
			'<script type="text/javascript" src="../styles/bootstrap/dracula/dracula_algorithms.js"></script>'."\n");
		$this->htmlStartPage(getMLText("admin_tools"));
//		$this->contentContainerStart();

?>
<div id="canvas" style="width: 100%; height:480px; _border: 1px solid #bbb;"></div>
<script language="JavaScript">
$(document).ready(function() {
  var width = $('#canvas').width();
  var height = $('#canvas').height();;
  var ggg = new Graph();
  ggg.edgeFactory.template.style.directed = true;

  var render_action = function(r, n) {
		/* the Raphael set is obligatory, containing all you want to display */
		var set = r.set().push(
			/* custom objects go here */
			r.rect(n.point[0]-45, n.point[1]-13, 90, 44).attr({"fill": (n.color == undefined ? "#feb" : n.color), r : "12px", "stroke-width" : "1px" })).push(
			r.text(n.point[0], n.point[1] + 10, (n.label || n.id) + "\n(" + (n.maxtime == undefined ? "Infinity" : n.maxtime) + ")"));
		return set;
	};

<?php
		$this->seentrans = array();
		$state = $this->workflow->getInitState();
		$this->states = array();
		$this->actions = array();
		$this->printGraph();
?>
    var layouter = new Graph.Layout.Spring(ggg);
    var renderer = new Graph.Renderer.Raphael('canvas', ggg, width, height);
});

</script>

<?php
//		$this->contentContainerEnd();
		echo "</body>\n</html>\n";
	} /* }}} */
}
?>
