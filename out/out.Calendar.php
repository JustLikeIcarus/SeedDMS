<?php
//    MyDMS. Document Management System
//    Copyright (C) 2010 Matteo Lucarelli
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.Calendar.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if ($_GET["mode"]) $mode=$_GET["mode"];

// get required date else use current
$currDate = time();

if (isset($_GET["year"])&&is_numeric($_GET["year"])) $year=$_GET["year"];
else $year = (int)date("Y", $currDate);
if (isset($_GET["month"])&&is_numeric($_GET["month"])) $month=$_GET["month"];
else $month = (int)date("m", $currDate);
if (isset($_GET["day"])&&is_numeric($_GET["day"])) $day=$_GET["day"];
else $day = (int)date("d", $currDate);

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user,'mode'=>$mode, 'year'=>$year, 'month'=>$month, 'day'=>$day, 'firstdayofweek'=>$settings->_firstDayOfWeek));
if($view) {
	$view->show();
	exit;
}

?>
