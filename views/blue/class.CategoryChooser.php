<?php
/**
 * Implementation of CategoryChooser view
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
 * Class which outputs the html page for CategoryChooser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_CategoryChooser extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];
		$form = $this->params['form'];
		$selcats = $this->params['selcats'];

		$this->htmlStartPage(getMLText("choose_target_category"));
		$this->globalBanner();
		$this->pageNavigation(getMLText("choose_target_category"));
?>

<script language="JavaScript">
var targetName = opener.document.<?php echo $form?>.categoryname<?php print $form ?>;
var targetID = opener.document.<?php echo $form?>.categoryid<?php print $form ?>;
$(document).ready(function(){
	$('#getcategories').click(function(){
//    alert($('#keywordta option:selected').text());
		var value = '';
		$('#keywordta option:selected').each(function(){
			value += ' ' + $(this).text();
		});
		targetName.value = value;
		targetID.value = $('#keywordta').val();
		window.close();
		return true;
	});
});
</script>

<?php
			$this->contentContainerStart();
			$selcatsarr = explode(',', $selcats);
?>
<table>
	<tr>
		<td valign="top" class="inputDescription"><?php echo getMLText("categories")?>:</td>
		<td>
			<select id="keywordta" size="5" style="min-width: 100px;" multiple>
<?php
			foreach($categories as $category) {
				echo "<option value=\"".$category->getId()."\"";
				if(in_array($category->getID(), $selcatsarr))
					echo " selected";
				echo ">".htmlspecialchars($category->getName())."</option>\n";
			}
?>
			</select>
		</td>
	</tr>
	<tr>
	  <td></td>
		<td>
			<input type="button" id='getcategories' value="<?php echo getMLText("accept")?>"> &nbsp;&nbsp;
		</td>
	</tr>

</table>
<?php
			$this->contentContainerEnd();
			$this->htmlEndPage();
	} /* }}} */
}
?>
