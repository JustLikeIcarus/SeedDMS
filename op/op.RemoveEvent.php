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

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removeevent')) {
	UI::exitError(getMLText("edit_event"),getMLText("invalid_request_token"));
}

if (!isset($_POST["eventid"]) || !is_numeric($_POST["eventid"]) || intval($_POST["eventid"])<1) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

$event=getEvent($_POST["eventid"]);

if (($user->getID()!=$event["userID"])&&(!$user->isAdmin())){
	UI::exitError(getMLText("edit_event"),getMLText("access_denied"));
}

$res = delEvent($_POST["eventid"]);
                                
if (is_bool($res) && !$res) {
	UI::exitError(getMLText("edit_event"),getMLText("error_occured"));
}

add_log_line("?id=".$_POST["eventid"]);

$dt=getdate($event["start"]);

header("Location:../out/out.Calendar.php?mode=w&day=".$dt["mday"]."&year=".$dt["year"]."&month=".$dt["mon"]);

?>
