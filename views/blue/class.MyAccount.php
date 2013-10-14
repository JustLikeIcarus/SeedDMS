<?php
/**
 * Implementation of MyAccount view
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
 * Class which outputs the html page for MyAccount view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_MyAccount extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$enableuserimage = $this->params['enableuserimage'];
		$passwordexpiration = $this->params['passwordexpiration'];
		$httproot = $this->params['httproot'];

		$this->htmlStartPage(getMLText("my_account"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("my_account"), "my_account");

		$this->contentHeading(getMLText("user_info"));
		$this->contentContainerStart();

		print "<table>\n";

		if ($enableuserimage){
			print "<tr>\n";
			print "<td rowspan=5 id=\"userImage\">".($user->hasImage() ? "<img class=\"userImage\" src=\"".$httproot . "out/out.UserImage.php?userid=".$user->getId()."\">" : getMLText("no_user_image"))."</td>\n";
			print "</tr>\n";
		}

		print "<tr>\n";
		print "<td>".getMLText("name")." : </td>\n";
		print "<td>".htmlspecialchars($user->getFullName()).($user->isAdmin() ? " (".getMLText("admin").")" : "")."</td>\n";
		print "</tr>\n<tr>\n";
		print "<td>".getMLText("user_login")." : </td>\n";
		print "<td>".$user->getLogin()."</td>\n";
		print "</tr>\n<tr>\n";
		print "<td>".getMLText("email")." : </td>\n";
		print "<td>".htmlspecialchars($user->getEmail())."</td>\n";
		print "</tr>\n<tr>\n";
		print "<td>".getMLText("comment")." : </td>\n";
		print "<td>".htmlspecialchars($user->getComment())."</td>\n";
		print "</tr>\n";
		if($passwordexpiration > 0) {
			print "<tr>\n";
			print "<td>".getMLText("password_expiration")." : </td>\n";
			print "<td>".htmlspecialchars($user->getPwdExpiration())."</td>\n";
			print "</tr>\n";
		}
		print "<tr>\n";
		print "<td>".getMLText("quota")." : </td>\n";
		print "<td>".SeedDMS_Core_File::format_filesize($user->getQuota())."</td>\n";
		print "</tr>\n";
		print "<tr>\n";
		print "<td>".getMLText("used_discspace")." : </td>\n";
		print "<td>".SeedDMS_Core_File::format_filesize($user->getUsedDiskSpace())."</td>\n";
		print "</tr>\n";
		print "</table>\n";

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
