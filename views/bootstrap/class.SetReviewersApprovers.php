<?php
/**
 * Implementation of SetReviewersApprovers view
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
 * Class which outputs the html page for SetReviewersApprovers view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SetReviewersApprovers extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$content = $this->params['version'];
		$enableadminrevapp = $this->params['enableadminrevapp'];
		$enableownerrevapp = $this->params['enableownerrevapp'];
		$enableselfrevapp = $this->params['enableselfrevapp'];

		$overallStatus = $content->getStatus();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);
		$this->contentHeading(getMLText("change_assignments"));

		// Retrieve a list of all users and groups that have review / approve privileges.
		$docAccess = $folder->getReadAccessList($enableadminrevapp, $enableownerrevapp);

		// Retrieve list of currently assigned reviewers and approvers, along with
		// their latest status.
		$reviewStatus = $content->getReviewStatus();
		$approvalStatus = $content->getApprovalStatus();

		// Index the review results for easy cross-reference with the Approvers List.
		$reviewIndex = array("i"=>array(), "g"=>array());
		foreach ($reviewStatus as $i=>$rs) {
			if ($rs["type"]==0) {
				$reviewIndex["i"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
			} elseif ($rs["type"]==1) {
				$reviewIndex["g"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
			}
		}

		// Index the approval results for easy cross-reference with the Approvers List.
		$approvalIndex = array("i"=>array(), "g"=>array());
		foreach ($approvalStatus as $i=>$rs) {
			if ($rs["type"]==0) {
				$approvalIndex["i"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
			} elseif ($rs["type"]==1) {
				$approvalIndex["g"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
			}
		}
?>

<?php $this->contentContainerStart(); ?>

<form action="../op/op.SetReviewersApprovers.php" method="post" name="form1">

<?php $this->contentSubHeading(getMLText("update_reviewers"));?>

  <div class="cbSelectTitle"><?php printMLText("individuals")?>:</div>
  <select class="chzn-select span9" name="indReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_reviewers'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<?php

		$res=$user->getMandatoryReviewers();
		foreach ($docAccess["users"] as $usr) {
			$mandatory=false;
			foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $mandatory=true;
			
			if ($mandatory){

				print "<option value=\"".$usr->getID()."\" disabled=\"disabled\">". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())." &lt;".$usr->getEmail()."&gt;</option>";
				print "<input id='revInd".$usr->getID()."' type='hidden' name='indReviewers[]' value='". $usr->getID() ."'>";

			} elseif (isset($reviewIndex["i"][$usr->getID()])) {

				switch ($reviewIndex["i"][$usr->getID()]["status"]) {
					case 0:
						print "<option value='". $usr->getID() ."' selected='selected'>".htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
						break;
					case -2:
						print "<option value='". $usr->getID() ."'>".htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
						break;
					default:
						print "<option value='". $usr->getID() ."' disabled='disabled'>".htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
						break;
				}
			} else {
				if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
				print "<option value='". $usr->getID() ."'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
			}
		}
?>
  </select>

  <div class="cbSelectTitle"><?php printMLText("groups")?>:</div>
  <select class="chzn-select span9" name="grpReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_reviewers'); ?>" data-no_results_text="<?php printMLText('unknown_group'); ?>">
<?php
		foreach ($docAccess["groups"] as $group) {

			$mandatory=false;
			foreach ($res as $r) if ($r['reviewerGroupID']==$group->getID()) $mandatory=true;
			
			if ($mandatory){

				print "<option value=\"".$group->getID()."\" disabled='disabled'>".htmlspecialchars($group->getName())."</option>";
				print "<input id='revGrp".$group->getID()."' type='hidden' name='grpReviewers[]' value='". $group->getID() ."' />";

			} elseif (isset($reviewIndex["g"][$group->getID()])) {

				switch ($reviewIndex["g"][$group->getID()]["status"]) {
					case 0:
						print "<option value='". $group->getID() ."' selected='selected'>".htmlspecialchars($group->getName())."</option>";
						break;
					case -2:
						print "<option value='". $group->getID() ."'>".htmlspecialchars($group->getName())."</option>";
						break;
					default:
						print "<option id='revGrp".$group->getID()."' type='checkbox' name='grpReviewers[]' value='". $group->getID() ."' disabled='disabled'>".htmlspecialchars($group->getName())."</option>";
						break;
				}
			} else {
				print "<option value='". $group->getID() ."'>".htmlspecialchars($group->getName())."</option>";
			}
		}
?>
  </select>

<?php $this->contentSubHeading(getMLText("update_approvers"));?>

  <div class="cbSelectTitle cbSelectMargin"><?php printMLText("individuals")?>:</div>
  <select class="chzn-select span9" name="indApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_approvers'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<?php

		$res=$user->getMandatoryApprovers();

		foreach ($docAccess["users"] as $usr) {

			$mandatory=false;
			foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $mandatory=true;

			if ($mandatory){
			
				print "<option value='". $usr->getID() ."' disabled='disabled'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())." &lt;".$usr->getEmail()."&gt;</option>";
				print "<input id='appInd".$usr->getID()."' type='hidden' name='indApprovers[]' value='". $usr->getID() ."'>";

			} elseif (isset($approvalIndex["i"][$usr->getID()])) {
			
				switch ($approvalIndex["i"][$usr->getID()]["status"]) {
					case 0:
						print "<option value='". $usr->getID() ."' selected='selected'>".htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
						break;
					case -2:
						print "<option value='". $usr->getID() ."'>".htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
						break;
					default:
						print "<option value='". $usr->getID() ."' disabled='disabled'>".htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
						break;
				}
			}
			else {
				if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
				print "<option value='". $usr->getID() ."'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</option>";
			}
		}
?>
  </select>
  <div class="cbSelectTitle"><?php printMLText("groups")?>:</div>

  <select class="chzn-select span9" name="grpApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_approvers'); ?>" data-no_results_text="<?php printMLText('unknown_group'); ?>">
<?php
		foreach ($docAccess["groups"] as $group) {

			$mandatory=false;
			foreach ($res as $r) if ($r['approverGroupID']==$group->getID()) $mandatory=true;

			if ($mandatory){

				print "<option type='checkbox' checked='checked' disabled='disabled'>".htmlspecialchars($group->getName())."</option>";
				print "<input id='appGrp".$group->getID()."' type='hidden' name='grpApprovers[]' value='". $group->getID() ."'>";

			} elseif (isset($approvalIndex["g"][$group->getID()])) {

				switch ($approvalIndex["g"][$group->getID()]["status"]) {
					case 0:
						print "<option value='". $group->getID() ."' selected='selected'>".htmlspecialchars($group->getName())."</option>";
						break;
					case -2:
						print "<option value='". $group->getID() ."'>".htmlspecialchars($group->getName())."</option>";
						break;
					default:
						print "<option value='". $group->getID() ."' disabled='disabled'>".htmlspecialchars($group->getName())."</option>";
						break;
				}
			}
			else {
				print "<option value='". $group->getID() ."'>".htmlspecialchars($group->getName())."</option>";
			}
		}
?>
  </select>

<p>
<input type='hidden' name='documentid' value='<?php echo $document->getID() ?>'/>
<input type='hidden' name='version' value='<?php echo $content->getVersion() ?>'/>
<input type="submit" class="btn" value="<?php printMLText("update");?>">
</p>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
