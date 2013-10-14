<?php
/**
 * Implementation of KeywordChooser view
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
 * Class which outputs the html page for KeywordChooser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_KeywordChooser extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];
		$target = $this->params['form'];

		$this->htmlStartPage(getMLText("use_default_keywords"));
?>
<script language="JavaScript">
var targetObj = opener.document.<?php echo $target ?>;
var myTA;

function insertKeywords(keywords) {

	if (navigator.appName == "Microsoft Internet Explorer") {
		myTA.value += " " + keywords;
	}
	//assuming Mozilla
	else {
		selStart = myTA.selectionStart;
		
		myTA.value = myTA.value.substring(0,myTA.selectionStart) + " " 
			+ keywords
			+ myTA.value.substring(myTA.selectionStart,myTA.value.length);
		
		myTA.selectionStart = selStart + keywords.length+1;
		myTA.selectionEnd = selStart + keywords.length+1;
	}				  
	myTA.focus();
}

function cancel() {
	window.close();
	return true;
}

function acceptKeywords() {
	targetObj.value = myTA.value;
	window.close();
	return true;
}

obj = new Array();
obj[0] = -1;
obj[1] = -1;
function showKeywords(which) {
	if (obj[which] != -1)
		obj[which].style.display = "none";
	
	list = document.getElementById("categories" + which);
	
	id = list.options[list.selectedIndex].value;
	if (id == -1)
		return;
	
	obj[which] = document.getElementById("keywords" + id);
	obj[which].style.display = "";
}
</script>

<div>
<?php
		$this->contentHeading(getMLText("use_default_keywords"));
		$this->contentContainerStart();
?>

<table>

	<tr>
		<td valign="top" class="inputDescription"><?php echo getMLText("keywords")?>:</td>
		<td><textarea id="keywordta" rows="5" cols="30"></textarea></td>
	</tr>
	
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	

	<tr>
		<td class="inputDescription"><?php echo getMLText("global_default_keywords")?>:</td>
		<td>
			<select onchange="showKeywords(0)" id="categories0">
				<option value="-1"><?php echo getMLText("choose_category")?>
<?php
				foreach ($categories as $category) {
					$owner = $category->getOwner();
					if (!$owner->isAdmin())
						continue;
					
					print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
				}
?>
			</select>
		</td>
	</tr>
<?php
	foreach ($categories as $category) {
		$owner = $category->getOwner();
		if (!$owner->isAdmin())
			continue;
?>
	<tr id="keywords<?php echo $category->getID()?>" style="display : none;">
		<td valign="top" class="inputDescription"><?php echo getMLText("default_keywords")?>:</td>
		<td>
			<?php
				$lists = $category->getKeywordLists();
				
				if (count($lists) == 0) print getMLText("no_default_keywords");
				else {	
					print "<ul>";
					foreach ($lists as $list) {
						print "<li><a href='javascript:insertKeywords(\"".htmlspecialchars($list["keywords"])."\");'>".htmlspecialchars($list["keywords"])."</a></li>";
					}
					print "</ul>";
				}
			?>
		</td>
	</tr>
<?php } ?>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td class="inputDescription"><?php echo getMLText("personal_default_keywords")?>:</td>
		<td>
			<select onchange="showKeywords(1)" id="categories1">
				<option value="-1"><?php echo getMLText("choose_category")?>
<?php
				foreach ($categories as $category) {
					$owner = $category->getOwner();
					if ($owner->isAdmin())
						continue;
					
					print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
				}
?>
			</select>
		</td>
	</tr>
<?php
		foreach ($categories as $category) {
			$owner = $category->getOwner();
			if ($owner->isAdmin())
				continue;
?>
		<tr id="keywords<?php echo $category->getID()?>" style="display : none;">
			<td valign="top" class="inputDescription"><?php echo getMLText("default_keywords")?>:</td>
			<td class="standardText">
<?php
					$lists = $category->getKeywordLists();				
					if (count($lists) == 0) print getMLText("no_default_keywords");
					else {	
						print "<ul>";
						foreach ($lists as $list) {
							print "<li><a href='javascript:insertKeywords(\"".htmlspecialchars($list["keywords"])."\");'>".htmlspecialchars($list["keywords"])."</a></li>";
						}
						print "</ul>";
					}
?>
		</td>
	</tr>
<?php } ?>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td colspan="2">
			<br>
			<input type="Button" onclick="acceptKeywords();" value="<?php echo getMLText("accept")?>"> &nbsp;&nbsp;
			<input type="Button" onclick="cancel();" value="<?php echo getMLText("cancel")?>">
		</td>
	</tr>
</table>

<?php
		$this->contentContainerEnd();
?>
<script language="JavaScript">
myTA = document.getElementById("keywordta");
myTA.value = targetObj.value;
myTA.focus();
</script>
<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
