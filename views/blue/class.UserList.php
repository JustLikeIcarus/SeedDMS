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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_UserList extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$allUsers = $this->params['allusers'];
		$httproot = $this->params['httproot'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("user_list"));
		$this->contentContainerStart();

		foreach ($allUsers as $currUser) {
			if ($currUser->isGuest())
				continue;

			$this->contentSubHeading(getMLText("user") . ": \"" . $currUser->getFullName() . "\"");
?>
	<table border="0">
		<tr>
			<td><?php printMLText("user_login");?>:</td>
			<td><?php print $currUser->getLogin();?></td>
		</tr>
	<tr>
			<td><?php printMLText("user_name");?>:</td>
			<td><?php print $currUser->getFullName();?></td>
		</tr>
		<tr>
			<td><?php printMLText("email");?>:</td>
			<td><a href="mailto:<?php print $currUser->getEmail();?>"><?php print $currUser->getEmail();?></a></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><?php print $currUser->getComment();?></td>
		</tr>
		<tr>
			<td><?php printMLText("groups");?>:</td>
			<td>
				<?php
					$groups = $currUser->getGroups();
					if (count($groups) == 0) {
						printMLText("no_groups");
					}
					else {
						for ($j = 0; $j < count($groups); $j++)	{
							print $groups[$j]->getName();
							if ($j +1 < count($groups))
								print ", ";
						}
					}
				?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("user_image");?>:</td>
			<td>
				<?php
					if ($currUser->hasImage())
						print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$currUser->getId()."\">";
					else
						printMLText("no_user_image");
				?>
			</td>
		</tr>
	</table>
<?php
		}

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
