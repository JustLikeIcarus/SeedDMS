<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005 Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2009-2013 Uwe Steinmann
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
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

if (isset($_GET["id"]) && is_numeric($_GET["id"]) && isset($_GET['type'])) {
	switch($_GET['type']) {
		case "folder":
			$session->removeFromClipboard($dms->getFolder($_GET['id']));
			break;
		case "document":
			$session->removeFromClipboard($dms->getDocument($_GET['id']));
			break;
	}
}

$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_removed_from_clipboard')));

$folderid = $_GET['folderid'];

if($_GET['refferer'])
	header("Location:".urldecode($_GET['refferer']));
else {
	$folderid = $_GET['folderid'];
	header("Location:../out/out.ViewFolder.php?folderid=".$folderid);
}
?>
