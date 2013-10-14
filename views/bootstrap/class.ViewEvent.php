<?php
/**
 * Implementation of ViewEvent view
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
 * Class which outputs the html page for ViewEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ViewEvent extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$event = $this->params['event'];

		$this->htmlStartPage(getMLText("calendar"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("calendar"), "calendar");

		$this->contentHeading(getMLText("event_details"));
		$this->contentContainerStart();

		$u=$dms->getUser($event["userID"]);

		echo "<table class=\"table-condensed\">";

		echo "<tr>";
		echo "<td>".getMLText("name").": </td>";
		echo "<td>".htmlspecialchars($event["name"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td>".getMLText("comment").": </td>";
		echo "<td>".htmlspecialchars($event["comment"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td>".getMLText("from").": </td>";
		echo "<td>".getReadableDate($event["start"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td>".getMLText("to").": </td>";
		echo "<td>".getReadableDate($event["stop"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td>".getMLText("last_update").": </td>";
		echo "<td>".getLongReadableDate($event["date"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td>".getMLText("user").": </td>";
		echo "<td>".(is_object($u)?htmlspecialchars($u->getFullName()):getMLText("unknown_user"))."</td>";
		echo "</tr>";

		if (($user->getID()==$event["userID"])||($user->isAdmin())){
			echo "<tr>";
			echo "<td></td>";
			echo "<td>";
			print "<a href=\"../out/out.RemoveEvent.php?id=".$event["id"]."\" class=\"btn\"><i class=\"icon-remove\"></i> ".getMLText("delete")."</a> ";
			print "<a href=\"../out/out.EditEvent.php?id=".$event["id"]."\" class=\"btn\"><i class=\"icon-edit\"></i> ".getMLText("edit")."</a>";
			echo "</tr>";
		}

		echo "</table>";

		$this->contentContainerEnd();

		$this->htmlEndPage();
	} /* }}} */
}
?>
