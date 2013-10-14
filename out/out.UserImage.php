<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
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
include("../inc/inc.DBInit.php");
include("../inc/inc.Authentication.php");

/* Get the user as passed to the script. This allows to show
 * pictures of other users as well
 */
$userid = intval($_GET["userid"]);
$myUser = $dms->getUser($userid);

if (!$myUser->hasImage())
	UI::exitError(getMLText("user_image"),getMLText("no_user_image"));

if($resArr = $myUser->getImage()) {
	header("ContentType: " . $resArr["mimeType"]);
	print base64_decode($resArr["image"]);
}
exit;

?>
