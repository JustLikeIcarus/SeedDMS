<?php
/**
 * Implementation of ReviewDocument view
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
 * Class which outputs the html page for ReviewDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ReviewDocument extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$content = $this->params['version'];

		$reviews = $content->getReviewStatus();
		foreach($reviews as $review) {
			if($review['reviewID'] == $_GET['reviewid']) {
				$reviewStatus = $review;
				break;
			}
		}

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");
		$this->contentHeading(getMLText("submit_review"));
?>
<script language="JavaScript">
function checkIndForm()
{
	msg = "";
	if (document.form1.reviewStatus.value == "") msg += "<?php printMLText("js_no_review_status");?>\n";
	if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}
function checkGrpForm()
{
	msg = "";
	if (document.form1.reviewGroup.value == "") msg += "<?php printMLText("js_no_review_group");?>\n";
	if (document.form1.reviewStatus.value == "") msg += "<?php printMLText("js_no_review_status");?>\n";
	if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
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
		$this->contentContainerStart();

		// Display the Review form.
		if ($reviewStatus['type'] == 0) {
			if($reviewStatus["status"]!=0) {

				print "<table class=\"folderView\"><thead><tr>";
				print "<th>".getMLText("status")."</th>";
				print "<th>".getMLText("comment")."</th>";
				print "<th>".getMLText("last_update")."</th>";
				print "</tr></thead><tbody><tr>";
				print "<td>";
				printReviewStatusText($reviewStatus["status"]);
				print "</td>";
				print "<td>".htmlspecialchars($reviewStatus["comment"])."</td>";
				$indUser = $dms->getUser($reviewStatus["userID"]);
				print "<td>".$reviewStatus["date"]." - ". htmlspecialchars($indUser->getFullname()) ."</td>";
				print "</tr></tbody></table><br>";
			}
?>
	<form method="post" action="../op/op.ReviewDocument.php" name="form1" onsubmit="return checkIndForm();">
	<?php echo createHiddenFieldWithKey('reviewdocument'); ?>
	<table>
	<tr><td class='infos' valign='top'><?php printMLText("comment")?>:</td>
	<td class='infos' valign='top'><textarea name="comment" cols="80" rows="4"></textarea>
	</td></tr>
	<tr><td><?php printMLText("review_status")?></td>
	<td><select name="reviewStatus">
<?php if($reviewStatus['status'] != 1) { ?>
	<option value='1'><?php printMLText("status_reviewed")?></option>
<?php } ?>
<?php if($reviewStatus['status'] != -1) { ?>
	<option value='-1'><?php printMLText("rejected")?></option>
<?php } ?>
	</select>
	</td></tr><tr><td></td><td>
	<input type='hidden' name='reviewType' value='ind'/>
	<input type='hidden' name='documentid' value='<?php echo $document->getID() ?>'/>
	<input type='hidden' name='version' value='<?php echo $content->getVersion() ?>'/>
	<input type='submit' name='indReview' value='<?php printMLText("submit_review")?>'/>
	</td></tr></table>
	</form>
<?php
		}
		else if ($reviewStatus['type'] == 1) {

			if($reviewStatus["status"]!=0) {

				print "<table class=\"folderView\"><thead><tr>";
				print "<th>".getMLText("status")."</th>";
				print "<th>".getMLText("comment")."</th>";
				print "<th>".getMLText("last_update")."</th>";
				print "</tr></thead><tbody><tr>";
				print "<td>";
				printReviewStatusText($reviewStatus["status"]);
				print "</td>";
				print "<td>".htmlspecialchars($reviewStatus["comment"])."</td>";
				$indUser = $dms->getUser($reviewStatus["userID"]);
				print "<td>".$reviewStatus["date"]." - ". htmlspecialchars($indUser->getFullname()) ."</td>";
				print "</tr></tbody></table><br>\n";
			}

?>
	<form method="post" action="../op/op.ReviewDocument.php" name="form1" onsubmit="return checkGrpForm();">
	<table>
	<tr><td><?php printMLText("comment")?>:</td>
	<td><textarea name="comment" cols="80" rows="4"></textarea>
	</td></tr>
	<tr><td><?php printMLText("review_status")?>:</td>
	<td>
	<select name="reviewStatus">
<?php if($reviewStatus['status'] != 1) { ?>
	<option value='1'><?php printMLText("status_reviewed")?></option>
<?php } ?>
<?php if($reviewStatus['status'] != -1) { ?>
	<option value='-1'><?php printMLText("rejected")?></option>
<?php } ?>
	</select>
	</td></tr>
	<tr><td></td><td>
	<input type='hidden' name='reviewType' value='grp'/>
	<input type='hidden' name='reviewGroup' value='<?php echo $reviewStatus['required']; ?>'/>
	<input type='hidden' name='documentid' value='<?php echo $document->getID() ?>'/>
	<input type='hidden' name='version' value='<?php echo $content->getVersion() ?>'/>
	<input type='submit' name='groupReview' value='<?php printMLText("submit_review")?>'/></td></tr>
	</table>
	</form>
<?php
		}
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
