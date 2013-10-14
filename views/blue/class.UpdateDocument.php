<?php
/**
 * Implementation of UpdateDocument view
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
 * Class which outputs the html page for UpdateDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UpdateDocument extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$strictformcheck = $this->params['strictformcheck'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$dropfolderdir = $this->params['dropfolderdir'];
		$documentid = $document->getId();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");
?>

<script language="JavaScript">
function checkForm()
{
	msg = "";
<?php if($dropfolderdir) { ?>
	if (document.form1.userfile.value == "" && document.form1.dropfolderfileform1.value == "") msg += "<?php printMLText("js_no_file");?>\n";
<?php } else { ?>
	if (document.form1.userfile.value == "") msg += "<?php printMLText("js_no_file");?>\n";
<?php } ?>
<?php
	if ($strictformcheck) {
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
		$this->contentHeading(getMLText("update_document"));
		$this->contentContainerStart();

		if ($document->isLocked()) {

			$lockingUser = $document->getLockingUser();

			print "<table><tr><td class=\"warning\">";
			
			printMLText("update_locked_msg", array("username" => htmlspecialchars($lockingUser->getFullName()), "email" => $lockingUser->getEmail()));
			
			if ($lockingUser->getID() == $user->getID())
				printMLText("unlock_cause_locking_user");
			else if ($document->getAccessMode($user) == M_ALL)
				printMLText("unlock_cause_access_mode_all");
			else
			{
				printMLText("no_update_cause_locked");
				print "</td></tr></table>";
				$this->contentContainerEnd();
				$this->htmlEndPage();
				exit;
			}

			print "</td></tr></table><br>";
		}

		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $document->getApproversList();
?>

<table>
<tr>
	<td class="warning"><?php echo getMLText("max_upload_size")." : ".ini_get( "upload_max_filesize"); ?></td>
</tr>
<?php
	if($enablelargefileupload) {
?>
<tr>
  <td><?php printf(getMLText('link_alt_updatedocument'), "out.UpdateDocument2.php?documentid=".$document->getID()); ?></td>
</tr>
<?php
	}
?>
</table><br>


<form action="../op/op.UpdateDocument.php" enctype="multipart/form-data" method="post" name="form1" onsubmit="return checkForm();">
	<input type="hidden" name="documentid" value="<?php print $document->getID(); ?>">
	<table>
	
		<tr>
			<td><?php printMLText("local_file");?>:</td>
			<td><input type="File" name="userfile" size="60"></td>
		</tr>
<?php if($dropfolderdir) { ?>
		<tr>
			<td><?php printMLText("dropfolder_file");?>:</td>
			<td><?php $this->printDropFolderChooser("form1");?></td>
		</tr>
<?php } ?>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td class="standardText">
				<textarea name="comment" rows="4" cols="80"></textarea>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("expires");?>:</td>
			<td class="standardText">
				<input type="radio" name="expires" value="false"<?php if (!$document->expires()) print " checked";?>><?php printMLText("does_not_expire");?><br>
				<input type="radio" name="expires" value="true"<?php if ($document->expires()) print " checked";?>><?php $this->printDateChooser(-1, "exp");?>
			</td>
		</tr>
<?php
	$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_documentcontent, SeedDMS_Core_AttributeDefinition::objtype_all));
	if($attrdefs) {
		foreach($attrdefs as $attrdef) {
?>
    <tr>
	    <td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
	    <td><?php $this->printAttributeEditField($attrdef, '') ?></td>
    </tr>
<?php
		}
	}
?>
		<tr>
			<td colspan=2>
				<?php $this->contentSubHeading(getMLText("assign_reviewers")); ?>

				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
				<div class="cbSelectContainer cbSelectMargin">
				<ul class="cbSelectList">
<?php
				$res=$user->getMandatoryReviewers();
				foreach ($docAccess["users"] as $usr) {
					if ($usr->getID()==$user->getID()) continue;
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $mandatory=true;

					if ($mandatory) print "<li class=\"cbSelectItem\"><input type='checkbox' checked='checked' disabled='disabled'>". htmlspecialchars($usr->getFullName())."</li>";
					else print "<li class=\"cbSelectItem\"><input id='revInd".$usr->getID()."' type='checkbox' name='indReviewers[]' value='". $usr->getID() ."'>". htmlspecialchars($usr->getFullName())."</li>";
				}
?>
				</ul>
				</div>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				foreach ($docAccess["groups"] as $grp) {
				
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerGroupID']==$grp->getID()) $mandatory=true;	

					if ($mandatory) print "<li class=\"cbSelectItem\"><input type='checkbox' checked='checked' disabled='disabled'>".htmlspecialchars($grp->getName())."</li>";
					else print "<li class=\"cbSelectItem\"><input id='revGrp".$grp->getID()."' type='checkbox' name='grpReviewers[]' value='". $grp->getID() ."'>".htmlspecialchars($grp->getName())."</li>";
				}
?>
				</ul>
				</div>
					
				<?php $this->contentSubHeading(getMLText("assign_approvers")); ?>	
				
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
				<div class="cbSelectContainer cbSelectMargin">
				<ul class="cbSelectList">
<?php
				$res=$user->getMandatoryApprovers();
				foreach ($docAccess["users"] as $usr) {
					if ($usr->getID()==$user->getID()) continue; 

					$mandatory=false;
					foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $mandatory=true;
					
					if ($mandatory) print "<li class=\"cbSelectItem\"><input type='checkbox' checked='checked' disabled='disabled'>". htmlspecialchars($usr->getFullName())."</li>";
					else print "<li class=\"cbSelectItem\"><input id='appInd".$usr->getID()."' type='checkbox' name='indApprovers[]' value='". $usr->getID() ."'>". htmlspecialchars($usr->getFullName())."</li>";
				}
?>
				</ul>
				</div>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				foreach ($docAccess["groups"] as $grp) {
				
					$mandatory=false;
					foreach ($res as $r) if ($r['approverGroupID']==$grp->getID()) $mandatory=true;	

					if ($mandatory) print "<li class=\"cbSelectItem\"><input type='checkbox' checked='checked' disabled='disabled'>".htmlspecialchars($grp->getName());
					else print "<li class=\"cbSelectItem\"><input id='appGrp".$grp->getID()."' type='checkbox' name='grpApprovers[]' value='". $grp->getID() ."'>".htmlspecialchars($grp->getName());

				}
?>
				</ul>
				</div>
			</td>
			</tr>
		<tr>
			<td colspan="2"><?php printMLText("add_doc_reviewer_approver_warning")?></td>
		</tr>
		<tr>
			<td colspan="2"><input type="Submit" value="<?php printMLText("update_document")?>"></td>
		</tr>
	</table>
</form>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
