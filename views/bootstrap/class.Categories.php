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
require_once("class.Bootstrap.php");

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
class SeedDMS_View_Categories extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
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
?>
<div class="row-fluid">
<div class="span4">
<div class="well">
<?php echo getMLText("selection")?>:
			<select onchange="showCategories(this)" id="selector" class="span9">
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

</div>
</div>

<div class="span8">
<div class="well">

<table class="table-condensed"><tr>
		<td id="categories0" style="display : none;">	
			<form class="form-inline" action="../op/op.Categories.php" method="post">
  		<?php echo createHiddenFieldWithKey('addcategory'); ?>
			<input type="Hidden" name="action" value="addcategory">
			<?php printMLText("name");?> : <input type="text" name="name">
			<input type="submit" class="btn" value="<?php printMLText("new_document_category"); ?>">
			</form>
		</td>
	
<?php	
			foreach ($categories as $category) {
				print "<td id=\"categories".$category->getID()."\" style=\"display : none;\">";	
?>
			<table class="table-condensed">
				<tr>
					<td></td><td>
<?php
		if(!$category->isUsed()) {
?>
						<form style="display: inline-block;" method="post" action="../op/op.Categories.php" >
						<?php echo createHiddenFieldWithKey('removecategory'); ?>
						<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
						<input type="Hidden" name="action" value="removecategory">
						<button class="btn" type="submit"><i class="icon-remove"></i> <?php echo getMLText("rm_document_category")?></button>
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
					<td><?php echo getMLText("name")?>:</td>
					<td>
						<form class="form-inline" style="margin-bottom: 0px;" action="../op/op.Categories.php" method="post">
  		        <?php echo createHiddenFieldWithKey('editcategory'); ?>
							<input type="Hidden" name="action" value="editcategory">
							<input type="Hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input name="name" type="text" value="<?php echo htmlspecialchars($category->getName()) ?>">&nbsp;
							<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save");?></button>
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
showCategories(sel);
</script>
<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
