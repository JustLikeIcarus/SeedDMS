<?php
/**
 * Implementation of UserDefaultKeywords view
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
 * Class which outputs the html page for UserDefaultKeywords view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UserDefaultKeywords extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];

		$this->htmlStartPage(getMLText("edit_default_keywords"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("my_account"), "my_account");
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
		$this->contentHeading(getMLText("edit_default_keywords"));
?>
<div class="row-fluid">
<div class="span4">
<div class="well">
<?php echo getMLText("selection")?>:
	<select onchange="showKeywords(this)" id="selector">
		<option value="-1"><?php echo getMLText("choose_category")?>
		<option value="0"><?php echo getMLText("new_default_keyword_category")?>
<?php

		$selected=0;
		$count=2;
		foreach ($categories as $category) {

			$owner = $category->getOwner();
			if ($owner->getID() != $user->getID()) continue;

			if (isset($_GET["categoryid"]) && $category->getID()==$_GET["categoryid"]) $selected=$count;
			print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
			$count++;
		}
?>
			</select>
</div>
</div>

<div class="span8">
<div class="well">

<table class="table-condensed"><tr>
		<td id="keywords0" style="display : none;">
			<form action="../op/op.UserDefaultKeywords.php" method="post" name="addcategory">
			<input type="hidden" name="action" value="addcategory">
			<?php printMLText("name");?> : <input type="text" name="name">
			<input type="Submit" class="btn" value="<?php printMLText("new_default_keyword_category"); ?>">
			</form>
		</td>
<?php
		foreach ($categories as $category) {
			$owner = $category->getOwner();
			if ($owner->getID() != $user->getID()) continue;

			print "<td id=\"keywords".$category->getID()."\" style=\"display : none;\">";
?>
			<table class="table-condensed">
				<tr>
					<td colspan="2">
						<form action="../op/op.UserDefaultKeywords.php" method="post">
  						<?php echo createHiddenFieldWithKey('removecategory'); ?>
							<input type="Hidden" name="action" value="removecategory">
							<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input value="<?php printMLText("rm_default_keyword_category");?>" type="submit" class="btn" title="<?php echo getMLText("delete")?>">
						</form>
					</td>
				</tr>
				<tr>
					<td><?php echo getMLText("name")?>:</td>
					<td>
						<form class="form-inline" action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo "category".$category->getID()?>">
  						<?php echo createHiddenFieldWithKey('editcategory'); ?>
							<input type="hidden" name="action" value="editcategory">
							<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input name="name" type="text" value="<?php echo htmlspecialchars($category->getName())?>">
  						<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button>
						</form>
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
									<form class="form-inline" style="display: inline-block;" action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo "cat".$category->getID().".".$list["id"]?>">
									<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="Hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="Hidden" name="action" value="editkeywords">
									<input type="text" name="keywords" value="<?php echo htmlspecialchars($list["keywords"]) ?>">
									<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button>
									</form>
									<form style="display: inline-block;" method="post" action="../op/op.UserDefaultKeywords.php" >
  								<?php echo createHiddenFieldWithKey('removekeywords'); ?>
									<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="Hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="Hidden" name="action" value="removekeywords">
									<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("delete")?></button>
									</form>
									<br>
						<?php }  ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<form class="form-inline" action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo $category->getID().".add"?>">
  				  <?php echo createHiddenFieldWithKey('newkeywords'); ?>
						<input type="hidden" name="action" value="newkeywords">
						<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
						<input type="text" name="keywords">
						<input type="submit" class="btn" value="<?php printMLText("new_default_keywords");?>">
						</form>
					</td>
				</tr>

			</table>
		</td>
<?php } ?>
	</tr></table>
</div>
</div>
</div>

<script language="JavaScript">

sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showKeywords(sel);

</script>

<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
