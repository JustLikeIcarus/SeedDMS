<?php
/**
 * Implementation of Categories view
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
 * Class which outputs the html page for Categories view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Categories extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
?>

<script language="JavaScript">
obj = -1;
function showCategories(selectObj) {
	if (obj != -1)
		obj.style.display = "none";
	
	id = selectObj.options[selectObj.selectedIndex].value;
	if (id == -1)
		return;
	
	obj = document.getElementById("categories" + id);
	obj.style.display = "";
}
</script>
<?php
		$this->contentHeading(getMLText("global_document_categories"));
		$this->contentContainerStart();
?>
	<table>
	<tr>
		<td><?php echo getMLText("selection")?>:</td>
		<td>
			<select onchange="showCategories(this)" id="selector">
				<option value="-1"><?php echo getMLText("choose_category")?>
				<option value="0"><?php echo getMLText("new_document_category")?>

				<?php
				
				$selected=0;
				$count=2;				
				foreach ($categories as $category) {
				
					if (isset($_GET["categoryid"]) && $category->getID()==$_GET["categoryid"]) $selected=$count;				
					print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
					$count++;
				}
				?>
			</select>
			&nbsp;&nbsp;
		</td>

		<td id="categories0" style="display : none;">	
			<form action="../op/op.Categories.php" method="post">
  		<?php echo createHiddenFieldWithKey('addcategory'); ?>
			<input type="Hidden" name="action" value="addcategory">
			<?php printMLText("name");?> : <input name="name">
			<input type="submit" value="<?php printMLText("new_document_category"); ?>">
			</form>
		</td>
	
<?php	
			foreach ($categories as $category) {
				print "<td id=\"categories".$category->getID()."\" style=\"display : none;\">";	
?>
			<table>
				<tr>
					<td colspan="2">
<?php
		if(!$category->isUsed()) {
?>
						<form style="display: inline-block;" method="post" action="../op/op.Categories.php" >
						<?php echo createHiddenFieldWithKey('removecategory'); ?>
						<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
						<input type="Hidden" name="action" value="removecategory">
						<input value="<?php echo getMLText("rm_document_category")?>" type="submit">
						</form>
<?php
		} else {
?>
						<p><?php echo getMLText('category_in_use') ?></p>
<?php
		}
?>
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
						<form action="../op/op.Categories.php" method="post">
  		        <?php echo createHiddenFieldWithKey('editcategory'); ?>
							<input type="Hidden" name="action" value="editcategory">
							<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input name="name" type="text" value="<?php echo htmlspecialchars($category->getName()) ?>">&nbsp;
							<input type="submit" value="<?php printMLText("save");?>">
						</form>
					</td>
				</tr>
				
			</table>
		</td>
<?php } ?>
	</tr></table>
	
<script language="JavaScript">
sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showCategories(sel);
</script>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
