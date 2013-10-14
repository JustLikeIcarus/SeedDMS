<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Uwe Steinmann
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
include("../inc/inc.Utils.php");
include("../inc/inc.ClassSession.php");
include("../inc/inc.DBInit.php");

// Delete session from database

$dms_session = $_COOKIE["mydms_session"];

$session = new SeedDMS_Session($db);
$session->load($dms_session);

// If setting the user id to 0 worked, it would be a way to logout a
// user. It doesn't work because of a foreign constraint in the database
// won't allow it. So we keep on deleting the session and the cookie on
// logout
// $session->setUser(0); does not work because of foreign user constraint

if(!$session->delete($dms_session)) {
	UI::exitError(getMLText("logout"),$db->getErrorMsg());
}

// Delete Cookie
setcookie("mydms_session", $_COOKIE["mydms_session"], time()-3600, $settings->_httpRoot);

//Forward to Login-page
header("Location: ../out/out.Login.php");
?>
