<?php
/**
 * Implementation of UsrView view
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
 * Class which outputs the html page for UsrView view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UsrView extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$users = $this->params['allusers'];
		$enableuserimage = $this->params['enableuserimage'];
		$httproot = $this->params['httproot'];

		$this->htmlStartPage(getMLText("my_account"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("my_account"), "my_account");

		$this->contentHeading(getMLText("users"));
		$this->contentContainerStart();

		echo "<table class=\"userView\">\n";
		echo "<thead>\n<tr>\n";
		echo "<th>".getMLText("name")."</th>\n";
		echo "<th>".getMLText("email")."</th>\n";
		echo "<th>".getMLText("comment")."</th>\n";
		if ($enableuserimage) echo "<th>".getMLText("user_image")."</th>\n";
		echo "</tr>\n</thead>\n";

		foreach ($users as $currUser) {

			if ($currUser->isGuest())
				continue;
				
			if ($currUser->isHidden()=="1") continue;
				
			echo "<tr>\n";
			
			print "<td>".htmlspecialchars($currUser->getFullName())."</td>";
			
			print "<td><a href=\"mailto:".htmlspecialchars($currUser->getEmail())."\">".htmlspecialchars($currUser->getEmail())."</a></td>";
			print "<td>".htmlspecialchars($currUser->getComment())."</td>";
			
			if ($enableuserimage){
				print "<td>";
				if ($currUser->hasImage()) print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$currUser->getId()."\">";
				else printMLText("no_user_image");
				print "</td>";	
			}
			
			echo "</tr>\n";
		}

		echo "</table>\n";

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
