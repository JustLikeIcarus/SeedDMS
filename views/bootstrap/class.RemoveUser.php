<?php
/**
 * Implementation of RemoveUser view
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
 * Class which outputs the html page for RemoveUser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveUser extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$rmuser = $this->params['rmuser'];
		$allusers = $this->params['allusers'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("rm_user"));
		$this->contentContainerStart();

?>
<form action="../op/op.UsrMgr.php" name="form1" method="post">
<input type="hidden" name="userid" value="<?php print $rmuser->getID();?>">
<input type="hidden" name="action" value="removeuser">
<?php echo createHiddenFieldWithKey('removeuser'); ?>
<p>
<?php printMLText("confirm_rm_user", array ("username" => htmlspecialchars($rmuser->getFullName())));?>
</p>

<p>
<?php printMLText("assign_user_property_to"); ?>:
<select name="assignTo">
<?php
		foreach ($allusers as $currUser) {
			if ($currUser->isGuest() || ($currUser->getID() == $rmuser->getID()) )
				continue;

			if ($rmuser && $currUser->getID()==$rmuser->getID()) $selected=$count;
			print "<option value=\"".$currUser->getID()."\">" . htmlspecialchars($currUser->getLogin()." - ".$currUser->getFullName());
		}
?>
</select>
</p>

<p><button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("rm_user");?></button></p>

</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
