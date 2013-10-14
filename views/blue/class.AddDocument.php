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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_AddDocument extends SeedDMS_Blue_Style {

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
		$folderid = $folder->getId();

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "view_folder", $folder);
		
?>
		<script language="JavaScript">
		function checkForm()
		{
			msg = "";
			//if (document.form1.userfile[].value == "") msg += "<?php printMLText("js_no_file");?>\n";
			
<?php
			if ($strictformcheck) {
?>
			if(!document.form1.name.disabled){
				if (document.form1.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
			}
			if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
			if (document.form1.keywords.value == "") msg += "<?php printMLText("js_no_keywords");?>\n";
<?php
			}
?>
			if (msg != ""){
				alert(msg);
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
		$this->contentHeading(getMLText("add_document"));
		$this->contentContainerStart();
		
		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $folder->getReadAccessList($enableadminrevapp, $enableownerrevapp);
		?>
		<table>
		<tr>
			<td class="warning"><?php echo getMLText("max_upload_size")." : ".ini_get( "upload_max_filesize"); ?></td>
		</tr>
<?php
			if($enablelargefileupload) {
?>
		<tr>
			<td><?php printf(getMLText('link_alt_updatedocument'), "out.AddMultiDocument.php?folderid=".$folderid."&showtree=".showtree()); ?></td>
		</tr>
<?php
			}
?>
		</table>
		<?php $this->contentSubHeading(getMLText("document_infos")); ?>
		
		<form action="../op/op.AddDocument.php" enctype="multipart/form-data" method="post" name="form1" onsubmit="return checkForm();">
		<?php echo createHiddenFieldWithKey('adddocument'); ?>
		<input type="hidden" name="folderid" value="<?php print $folderid; ?>">
		<input type="hidden" name="showtree" value="<?php echo showtree();?>">
		<table>
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
			<td>
			<textarea name="keywords" rows="1" cols="80"></textarea><br>
			<a href="javascript:chooseKeywords('form1.keywords');"><?php printMLText("use_default_keywords");?></a>
			<script language="JavaScript">
			var openDlg;
		
			function chooseKeywords(target) {
				openDlg = open("out.KeywordChooser.php?target="+target, "openDlg", "width=500,height=400,scrollbars=yes,resizable=yes");
			}
			</script>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td><?php $this->printCategoryChooser("form1");?></td>
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
			<input type="radio" name="expires" value="false" checked><?php printMLText("does_not_expire");?><br>
			<input type="radio" name="expires" value="true"><?php $this->printDateChooser(-1, "exp");?>
			</td>
		</tr>

		</table>

		<?php $this->contentSubHeading(getMLText("version_info")); ?>
		<table>
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
			<td><textarea name="version_comment" rows="3" cols="80"></textarea></td>
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
?>
		</table>

		<?php $this->contentSubHeading(getMLText("assign_reviewers")); ?>
		
			<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
			<div class="cbSelectContainer">
			<ul class="cbSelectList">
<?php
			$res=$user->getMandatoryReviewers();
			foreach ($docAccess["users"] as $usr) {
				if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
				$mandatory=false;
				foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $mandatory=true;
		
				if ($mandatory) print "<li class=\"cbSelectItem\"><input type='checkbox' checked='checked' disabled='disabled'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</li>";
				else print "<li class=\"cbSelectItem\"><input id='revInd".$usr->getID()."' type='checkbox' name='indReviewers[]' value='". $usr->getID() ."'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName())."</li>";
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
			<div class="cbSelectContainer">
			<ul class="cbSelectList">
<?php
			$res=$user->getMandatoryApprovers();
			foreach ($docAccess["users"] as $usr) {
				if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
				$mandatory=false;
				foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $mandatory=true;
				
				if ($mandatory) print "<li class=\"cbSelectItem\"><input type='checkbox' checked='checked' disabled='disabled'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName());
				else print "<li class=\"cbSelectItem\"><input id='appInd".$usr->getID()."' type='checkbox' name='indApprovers[]' value='". $usr->getID() ."'>". htmlspecialchars($usr->getLogin() . " - ". $usr->getFullName());
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

			<p><?php printMLText("add_doc_reviewer_approver_warning")?></p>
			<p><input type="Submit" value="<?php printMLText("add_document");?>"></p>
		</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
