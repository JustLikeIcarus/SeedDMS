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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_UserDefaultKeywords extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];

		$this->htmlStartPage(getMLText("edit_default_keywords"));
		$this->globalNavigation();
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
					if ($owner->getID() != $user->getID()) continue;

					if (isset($_GET["categoryid"]) && $category->getID()==$_GET["categoryid"]) $selected=$count;
					print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
					$count++;
				}
				?>
			</select>
			&nbsp;&nbsp;
		</td>

		<td id="keywords0" style="display : none;">
			<form action="../op/op.UserDefaultKeywords.php" method="post" name="addcategory">
			<input type="hidden" name="action" value="addcategory">
			<?php printMLText("name");?> : <input type="text" name="name">
			<input type="Submit" value="<?php printMLText("new_default_keyword_category"); ?>">
			</form>
		</td>
<?php
		foreach ($categories as $category) {
			$owner = $category->getOwner();
			if ($owner->getID() != $user->getID()) continue;

			print "<td id=\"keywords".$category->getID()."\" style=\"display : none;\">";
?>
			<table>
				<tr>
					<td colspan="2">
						<a href="../op/op.UserDefaultKeywords.php?categoryid=<?php print $category->getID();?>&action=removecategory"><img src="images/del.gif" border="0"><?php printMLText("rm_default_keyword_category");?></a>
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
						<form action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo "category".$category->getID()?>">
							<input type="hidden" name="action" value="editcategory">
							<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input name="name" value="<?php echo htmlspecialchars($category->getName())?>">
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
									<form action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo "cat".$category->getID().".".$list["id"]?>">
									<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="Hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="Hidden" name="action" value="editkeywords">
									<input type="text" name="keywords" value="<?php echo htmlspecialchars($list["keywords"]) ?>">
									<input name="action" value="editkeywords" type="Image" src="images/save.gif" title="<?php echo getMLText("save")?>" border="0">
									<!--	 <input name="action" value="removekeywords" type="Image" src="images/del.gif" title="<?php echo getMLText("delete")?>" border="0"> &nbsp; -->
									<a href="../op/op.UserDefaultKeywords.php?categoryid=<?php echo $category->getID()?>&keywordsid=<?php echo $list["id"]?>&action=removekeywords"><img src="images/del.gif" title="<?php echo getMLText("delete")?>" border=0></a>
									</form>
									<br>
						<?php }  ?>
					</td>
				</tr>
				<tr>
					<form action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo $category->getID().".add"?>">
					<td><input type="Submit" value="<?php printMLText("new_default_keywords");?>"></td>
					<td>
						<input type="hidden" name="action" value="newkeywords">
						<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
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
