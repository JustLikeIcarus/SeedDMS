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
include("../inc/inc.AccessUtils.php");
include("../inc/inc.ClassAccess.php");
include("../inc/inc.ClassDocument.php");
include("../inc/inc.ClassFolder.php");
include("../inc/inc.ClassGroup.php");
include("../inc/inc.ClassUser.php");
include("../inc/inc.DBAccess.php");
include("../inc/inc.FileUtils.php");
include("../inc/inc.Authentication.php");

print "<html></body>";

if (!$user->isAdmin()) {
	print "<b>ERROR: You must be administrator to execute the update</b>";
	die;
}

function update_db()
{
	global $db;

	$fd = fopen ("update.sql", "r");
	
	if (is_bool($fd)&&!$fd) return false;

	$queryStr = fread($fd, filesize("update.sql"));
	
	if (is_bool($queryStr)&&!$queryStr) return false;
	
	fclose ($fd);
	if (!$db->getResult($queryStr)) return false;
	
	return true;
}

print "<b>Updating ...please wait</b><br>";

print "<pre>";

if (!update_db()) {

	print "</pre>";

	print "<b>ERROR: An error occurred during the DB update</b>";
	print "<br><b>Please try to execute the update.sql manually</b>";
	die;

}else print "</pre><b>Update done</b><br>";

print "</body></html>";

?>
