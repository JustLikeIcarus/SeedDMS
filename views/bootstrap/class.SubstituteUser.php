<?php
/**
 * Implementation of SubstituteUser view
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
 * Class which outputs the html page for SubstituteUser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SubstituteUser extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$allUsers = $this->params['allusers'];

		$this->htmlStartPage(getMLText("substitute_user"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->contentHeading(getMLText("substitute_user"));
		$this->contentContainerStart();
?>
	<table class="table table-condensed">
	  <tr><th><?php printMLText('name'); ?></th><th><?php printMLText('email');?></th><th><?php printMLText('groups'); ?></th><th></th></tr>
<?php
		foreach ($allUsers as $currUser) {
			echo "<tr>";
			echo "<td>";
			echo $currUser->getFullName()." (".$currUser->getLogin().")<br />";
			echo "<small>".$currUser->getComment()."</small>";
			echo "</td>";
			echo "<td>";
			echo "<a href=\"mailto:".$currUser->getEmail()."\">".$currUser->getEmail()."</a><br />";
			echo "</td>";
			echo "<td>";
			$groups = $currUser->getGroups();
			if (count($groups) != 0) {
				for ($j = 0; $j < count($groups); $j++)	{
					print $groups[$j]->getName();
					if ($j +1 < count($groups))
						print ", ";
				}
			}
			echo "</td>";
			echo "<td>";
			if($currUser->getID() != $user->getID()) {
				echo "<a class=\"btn\" href=\"../op/op.SubstituteUser.php?userid=".$currUser->getID()."\"><i class=\"icon-exchange\"></i> ".getMLText('substitute_user')."</a> ";
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		$this->contentContainerEnd();

		$this->htmlEndPage();
	} /* }}} */
}
?>

