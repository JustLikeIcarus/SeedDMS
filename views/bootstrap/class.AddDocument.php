<?php
/**
 * Implementation of AddDocument view
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
 * Class which outputs the html page for AddDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddDocument extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$enableadminrevapp = $this->params['enableadminrevapp'];
		$enableownerrevapp = $this->params['enableownerrevapp'];
		$enableselfrevapp = $this->params['enableselfrevapp'];
		$strictformcheck = $this->params['strictformcheck'];
		$dropfolderdir = $this->params['dropfolderdir'];
		$workflowmode = $this->params['workflowmode'];
		$folderid = $folder->getId();

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true), "view_folder", $folder);
		
?>
<script language="JavaScript">
function checkForm()
	{
	msg = new Array();
	//if (document.form1.userfile[].value == "") msg += "<?php printMLText("js_no_file");?>\n";
			
<?php
			if ($strictformcheck) {
?>
	if(!document.form1.name.disabled){
		if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
	}
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
	if (document.form1.keywords.value == "") msg.push("<?php printMLText("js_no_keywords");?>");
<?php
			}
?>
	if (msg != ""){
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
	return true;
}

function addFiles()
	{
	var li = document.createElement('li');
	li.innerHTML = '<input type="File" name="userfile[]" size="60">';
	document.getElementById('files').appendChild(li);	
//	document.getElementById("files").innerHTML += '<br><input type="File" name="userfile[]" size="60">'; 
	document.form1.name.disabled=true;
}
		
</script>

<?php
		$msg = getMLText("max_upload_size").": ".ini_get( "upload_max_filesize");
		if($enablelargefileupload) {
			$msg .= "<p>".sprintf(getMLText('link_alt_updatedocument'), "out.AddMultiDocument.php?folderid=".$folderid."&showtree=".showtree())."</p>";
		}
		$this->warningMsg($msg);
		$this->contentHeading(getMLText("add_document"));
		$this->contentContainerStart();
		
		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $folder->getReadAccessList($enableadminrevapp, $enableownerrevapp);
		$this->contentSubHeading(getMLText("document_infos"));
?>
		<form action="../op/op.AddDocument.php" enctype="multipart/form-data" method="post" name="form1" onsubmit="return checkForm();">
		<?php echo createHiddenFieldWithKey('adddocument'); ?>
		<input type="hidden" name="folderid" value="<?php print $folderid; ?>">
		<input type="hidden" name="showtree" value="<?php echo showtree();?>">
		<table class="table-condensed">
		<tr>
			<td><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" size="60"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="3" cols="80"></textarea></td>
		</tr>
		<tr>
			<td><?php printMLText("keywords");?>:</td>
			<td><?php $this->printKeywordChooser("form1");?></td>
		</tr>
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td>
        <select class="chzn-select" name="categories[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<?php
			$categories = $dms->getDocumentCategories();
			foreach($categories as $category) {
				echo "<option value=\"".$category->getID()."\"";
				echo ">".$category->getName()."</option>";	
			}
?>
				</select>
      </td>
		</tr>
		<tr>
			<td><?php printMLText("sequence");?>:</td>
			<td><?php $this->printSequenceChooser($folder->getDocuments());?></td>
		</tr>
<?php
			$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_document, SeedDMS_Core_AttributeDefinition::objtype_all));
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
			<td><?php printMLText("expires");?>:</td>
			<td>
        <span class="input-append date span12" id="expirationdate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span3" size="16" name="expdate" type="text" value="<?php echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>&nbsp;
        <label class="checkbox inline">
				  <input type="checkbox" name="expires" value="false" checked><?php printMLText("does_not_expire");?>
        </label>
			</td>
		</tr>

		<tr>
			<td>
		<?php $this->contentSubHeading(getMLText("version_info")); ?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("version");?>:</td>
			<td><input type="text" name="reqversion" value="1"></td>
		</tr>
		<tr>
			<td><?php printMLText("local_file");?>:</td>
			<td>
			<a href="javascript:addFiles()"><?php printMLtext("add_multiple_files") ?></a>
			<ol id="files">
			<li><input type="file" name="userfile[]" size="60"></li>
			</ol>
			</td>
		</tr>
<?php if($dropfolderdir) { ?>
		<tr>
			<td><?php printMLText("dropfolder_file");?>:</td>
			<td><?php $this->printDropFolderChooser("form1");?></td>
		</tr>
<?php } ?>
		<tr>
			<td><?php printMLText("comment_for_current_version");?>:</td>
			<td><textarea name="version_comment" rows="3" cols="80"></textarea><br />
			<label class="checkbox inline"><input type="checkbox" name="use_comment" value="1" /> <?php printMLText("use_comment_of_document"); ?></label></td>
		</tr>
<?php
			$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_documentcontent, SeedDMS_Core_AttributeDefinition::objtype_all));
			if($attrdefs) {
				foreach($attrdefs as $attrdef) {
?>
		<tr>
			<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
			<td><?php $this->printAttributeEditField($attrdef, '', 'attributes_version') ?></td>
		</tr>
<?php
				}
			}
		if($workflowmode != 'traditional') {
?>
		<tr>	
      <td>
			<div class="cbSelectTitle"><?php printMLText("workflow");?>:</div>
      </td>
      <td>
<?php
				$mandatoryworkflow = $user->getMandatoryWorkflow();
				if($mandatoryworkflow) {
?>
				<?php echo $mandatoryworkflow->getName(); ?>
				<input type="hidden" name="workflow" value="<?php echo $mandatoryworkflow->getID(); ?>">
<?php
				} else {
?>
        <select class="_chzn-select-deselect span9" name="workflow" data-placeholder="<?php printMLText('select_workflow'); ?>">
<?php
					$workflows=$dms->getAllWorkflows();
					print "<option value=\"\">"."</option>";
					foreach ($workflows as $workflow) {
						print "<option value=\"".$workflow->getID()."\"";
						if($mandatoryworkflow && $mandatoryworkflow->getID() == $workflow->getID())
							echo " selected=\"selected\"";
						print ">". htmlspecialchars($workflow->getName())."</option>";
					}
?>
        </select>
<?php
				}
?>
      </td>
    </tr>
		<tr>	
      <td colspan="2">
			<?php $this->warningMsg(getMLText("add_doc_workflow_warning")); ?>
      </td>
		</tr>	
<?php
		} else {
?>
		<tr>
      <td>
		<?php $this->contentSubHeading(getMLText("assign_reviewers")); ?>
      </td>
		</tr>	
		<tr>	
      <td>
			<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
      </td>
      <td>
        <select class="chzn-select span9" name="indReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_reviewers'); ?>">
<?php
				$res=$user->getMandatoryReviewers();
				foreach ($docAccess["users"] as $usr) {
					if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $mandatory=true;

					if ($mandatory) print "<option disabled=\"disabled\" value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
					else print "<option value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
        </select>
      </td>
      </tr>
      <tr>
        <td>
			<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
        </td>
        <td>
        <select class="chzn-select span9" name="grpReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_reviewers'); ?>">
<?php
			foreach ($docAccess["groups"] as $grp) {
			
				$mandatory=false;
				foreach ($res as $r) if ($r['reviewerGroupID']==$grp->getID()) $mandatory=true;	

				if ($mandatory) print "<option value=\"".$grp->getID()."\" disabled=\"disabled\">".htmlspecialchars($grp->getName())."</option>";
				else print "<option value=\"".$grp->getID()."\">".htmlspecialchars($grp->getName())."</option>";
			}
?>
			</select>
			</td>
			</tr>
			
		  <tr>	
        <td>
		<?php $this->contentSubHeading(getMLText("assign_approvers")); ?>
        </td>
		  </tr>	
		
		  <tr>	
        <td>
			<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
        </td>
				<td>
      <select class="chzn-select span9" name="indApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_approvers'); ?>">
<?php
			$res=$user->getMandatoryApprovers();
			foreach ($docAccess["users"] as $usr) {
				if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 

				$mandatory=false;
				foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $mandatory=true;
				
				if ($mandatory) print "<option value=\"". $usr->getID() ."\" disabled='disabled'>". htmlspecialchars($usr->getFullName())."</option>";
				else print "<option value=\"". $usr->getID() ."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
			}
?>
			</select>
				</td>
		  </tr>	
		  <tr>	
        <td>
			<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
        </td>
        <td>
      <select class="chzn-select span9" name="grpApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_approvers'); ?>">
<?php
			foreach ($docAccess["groups"] as $grp) {
			
				$mandatory=false;
				foreach ($res as $r) if ($r['approverGroupID']==$grp->getID()) $mandatory=true;	

				if ($mandatory) print "<option value=\"". $grp->getID() ."\" disabled=\"disabled\">".htmlspecialchars($grp->getName())."</option>";
				else print "<option value=\"". $grp->getID() ."\">".htmlspecialchars($grp->getName())."</option>";

			}
?>
			</select>
				</td>
		  </tr>	
		  <tr>	
        <td colspan="2">
			<div class="alert"><?php printMLText("add_doc_reviewer_approver_warning")?></div>
        </td>
		  </tr>	
<?php
		}
?>
		</table>

			<p><input type="submit" class="btn" value="<?php printMLText("add_document");?>"></p>
		</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
