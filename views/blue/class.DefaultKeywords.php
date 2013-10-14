<?php
/**
 * Implementation of DefaultKeywords view
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
 * Class which outputs the html page for DefaultKeywords view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DefaultKeywords extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];
		$selcategoryid = $this->params['selcategoryid'];

$this->htmlStartPage(getMLText("admin_tools"));
$this->globalNavigation();
$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

?>
<script language="JavaScript">
obj = -1;
function showKeywords(selectObj) {
	if (obj != -1)
		obj.style.display = "none";

	id = selectObj.options[selectObj.selectedIndex].value;
	if (id == -1)
		return;

	obj = document.getElementById("keywords" + id);
	obj.style.display = "";
}
</script>
<?php

		$this->contentHeading(getMLText("global_default_keywords"));
		$this->contentContainerStart();
?>
	<table>
	<tr>
		<td><?php echo getMLText("selection")?>:</td>
		<td>
			<select onchange="showKeywords(this)" id="selector">
				<option value="-1"><?php echo getMLText("choose_category")?>
				<option value="0"><?php echo getMLText("new_default_keyword_category")?>

<?php
				
		$selected=0;
		$count=2;				
		foreach ($categories as $category) {
		
			$owner = $category->getOwner();
			if ((!$user->isAdmin()) && ($owner->getID() != $user->getID())) continue;

			if ($selcategoryid && $category->getID()==$selcategoryid) $selected=$count;				
			print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
			$count++;
		}
?>
			</select>
			&nbsp;&nbsp;
		</td>

		<td id="keywords0" style="display : none;">	
			<form action="../op/op.DefaultKeywords.php" method="post">
  		<?php echo createHiddenFieldWithKey('addcategory'); ?>
			<input type="Hidden" name="action" value="addcategory">
			<?php printMLText("name");?>: <input type="text" name="name">
			<input type="submit" value="<?php printMLText("new_default_keyword_category"); ?>">
			</form>
		</td>
<?php
		foreach ($categories as $category) {

			$owner = $category->getOwner();
			if ((!$user->isAdmin()) && ($owner->getID() != $user->getID())) continue;

			print "<td id=\"keywords".$category->getID()."\" style=\"display : none;\">";	
?>
			<table>
				<tr>
					<td colspan="2">
						<form action="../op/op.DefaultKeywords.php" method="post">
  						<?php echo createHiddenFieldWithKey('removecategory'); ?>
							<input type="Hidden" name="action" value="removecategory">
							<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input value="<?php printMLText("rm_default_keyword_category");?>" type="submit" title="<?php echo getMLText("delete")?>">
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php $this->contentSubHeading("");?>
					</td>
				</tr>				
				<tr>
					<td><?php echo getMLText("name")?>:</td>
					<td>
						<form action="../op/op.DefaultKeywords.php" method="post">
  						<?php echo createHiddenFieldWithKey('editcategory'); ?>
							<input type="hidden" name="action" value="editcategory">
							<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input name="name" type="text" value="<?php echo htmlspecialchars($category->getName()) ?>">&nbsp;
							<input type="submit" value="<?php printMLText("save");?>">
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php $this->contentSubHeading("");?>
					</td>
				</tr>
				
				<tr>
					<td><?php echo getMLText("default_keywords")?>:</td>
					<td>
						<?php
							$lists = $category->getKeywordLists();
							if (count($lists) == 0)
								print getMLText("no_default_keywords");
							else
								foreach ($lists as $list) {
?>
									<form style="display: inline-block;" method="post" action="../op/op.DefaultKeywords.php" >
  								<?php echo createHiddenFieldWithKey('editkeywords'); ?>
									<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="Hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="Hidden" name="action" value="editkeywords">
									<input name="keywords" type="text" value="<?php echo htmlspecialchars($list["keywords"]) ?>">
									<input name="action" value="editkeywords" type="Image" src="images/save.gif" title="<?php echo getMLText("save")?>" style="border: 0px;">
									<!--	 <input name="action" value="removekeywords" type="Image" src="images/del.gif" title="<?php echo getMLText("delete")?>" border="0"> &nbsp; -->
									</form>
									<form style="display: inline-block;" method="post" action="../op/op.DefaultKeywords.php" >
  								<?php echo createHiddenFieldWithKey('removekeywords'); ?>
									<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="hidden" name="action" value="removekeywords">
									<input name="action" value="removekeywords" type="Image" src="images/del.gif" title="<?php echo getMLText("delete")?>" style="border: 0px;">
									</form>
									<br>
						<?php }  ?>
					</td>
				</tr>
				<tr>
					<form action="../op/op.DefaultKeywords.php" method="post">
  				<?php echo createHiddenFieldWithKey('newkeywords'); ?>
					<td><input type="Submit" value="<?php printMLText("new_default_keywords");?>"></td>
					<td>
						<input type="Hidden" name="action" value="newkeywords">
						<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
						<input type="text" name="keywords">
					</td>
					</form>
				</tr>

			</table>
		</td>
<?php } ?>
	</tr></table>

<script language="JavaScript">

sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showKeywords(sel);

</script>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
