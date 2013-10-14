<?php
/**
 * Implementation of UserList view
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
 * Class which outputs the html page for UserList view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UserList extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$allUsers = $this->params['allusers'];
		$httproot = $this->params['httproot'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation("", "admin_tools");
		$this->contentHeading(getMLText("user_list"));
		$this->contentContainerStart();
?>

	<table class="table table-condensed">
	  <tr><th></th><th><?php printMLText('name'); ?></th><th><?php printMLText('groups'); ?></th><th></th><th></th></tr>
<?php
		foreach ($allUsers as $currUser) {
			echo "<tr>";
			echo "<td>";
			if ($currUser->hasImage())
				print "<img width=\"50\" src=\"".$httproot . "out/out.UserImage.php?userid=".$currUser->getId()."\">";
			echo "</td>";
			echo "<td>";
			echo $currUser->getFullName()." (".$currUser->getLogin().")<br />";
			echo "<a href=\"mailto:".$currUser->getEmail()."\">".$currUser->getEmail()."</a><br />";
			echo "<small>".$currUser->getComment()."</small>";
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
			if($currUser->getQuota() != 0)
				echo SeedDMS_Core_File::format_filesize($currUser->getQuota())."<br />";
			echo SeedDMS_Core_File::format_filesize($currUser->getUsedDiskSpace())."<br />";
			echo "</td>";
			echo "<td>";
     	echo "<a href=\"../out/out.UsrMgr.php?userid=".$currUser->getID()."\"><i class=\"icon-edit\"></i></a> ";
     	echo "<a href=\"../out/out.RemoveUser.php?userid=".$currUser->getID()."\"><i class=\"icon-remove\"></i></a>";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
