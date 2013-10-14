<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2011 Matteo Lucarelli
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

include("../inc/inc.Version.php");
include("../inc/inc.Settings.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if(isset($_GET['repair']) && $_GET['repair'] == 1) {
	$repair = 1;
} else {
	$repair = 0;
}

if(isset($_GET['unlink']) && $_GET['unlink'] == 1) {
	$unlink = 1;
} else {
	$unlink = 0;
}

if(isset($_GET['setfilesize']) && $_GET['setfilesize'] == 1) {
	$setfilesize = 1;
} else {
	$setfilesize = 0;
}

if(isset($_GET['setchecksum']) && $_GET['setchecksum'] == 1) {
	$setchecksum = 1;
} else {
	$setchecksum = 0;
}

$folder = $dms->getFolder($settings->_rootFolderID);
$unlinkedversions = $dms->getUnlinkedDocumentContent();
$nofilesizeversions = $dms->getNoFileSizeDocumentContent();
$nochecksumversions = $dms->getNoChecksumDocumentContent();

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'folder'=>$folder, 'unlinkedcontent'=>$unlinkedversions, 'nofilesizeversions'=>$nofilesizeversions, 'nochecksumversions'=>$nochecksumversions, 'unlink'=>$unlink, 'setfilesize'=>$setfilesize, 'setchecksum'=>$setchecksum, 'repair'=>$repair));
if($view) {
	$view->show();
	exit;
}

?>
