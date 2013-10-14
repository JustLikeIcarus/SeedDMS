<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
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
include("../inc/inc.LogInit.php");
include("../inc/inc.Utils.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Calendar.php");
include("../inc/inc.Authentication.php");

if ($user->isGuest()) {
	UI::exitError(getMLText("edit_event"),getMLText("access_denied"));
}

/* Check if the form data comes for a trusted request */
if(!checkFormKey('editevent')) {
	UI::exitError(getMLText("edit_event"),getMLText("invalid_request_token"));
}

if (!isset($_POST["from"]) && !(isset($_POST["frommonth"]) && isset($_POST["fromday"]) && isset($_POST["fromyear"])) ) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

if (!isset($_POST["to"]) && !(isset($_POST["tomonth"]) && isset($_POST["today"]) && isset($_POST["toyear"])) ) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

if (!isset($_POST["name"]) || !isset($_POST["comment"]) ) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

if (!isset($_POST["eventid"])) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

$name     = $_POST["name"];
$comment  = $_POST["comment"];
if(isset($_POST["from"])) {
	$tmp = explode('-', $_POST["from"]);
	$from = mktime(0,0,0, $tmp[1], $tmp[0], $tmp[2]);
} else {
	$from = mktime(0,0,0, intval($_POST["frommonth"]), intval($_POST["fromday"]), intval($_POST["fromyear"]));
}
if(isset($_POST["to"])) {
	$tmp = explode('-', $_POST["to"]);
	$to = mktime(23,59,59, $tmp[1], $tmp[0], $tmp[2]);
} else {
	$to = mktime(23,59,59, intval($_POST["tomonth"]), intval($_POST["today"]), intval($_POST["toyear"]));
}

if ($to<=$from){
	$to = $from + 86400 -1;
}

$res = editEvent($_POST["eventid"], $from, $to, $name, $comment );

if (is_bool($res) && !$res) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

add_log_line("?eventid=".$_POST["eventid"]."&name=".$name."&from=".$from."&to=".$to);

header("Location:../out/out.Calendar.php?mode=w&day=".$_POST["fromday"]."&year=".$_POST["fromyear"]."&month=".$_POST["frommonth"]);


?>
