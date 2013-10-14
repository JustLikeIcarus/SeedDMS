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
include("../inc/inc.LogInit.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

$dump_name = $settings->_contentDir.time().".sql";

$h=fopen($dump_name,"w");

if (is_bool($h)&&!$h)
	UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));

$tables = $db->TableList('TABLES');

foreach ($tables as $table){

	$query = "SELECT * FROM ".$table;
	$records = $db->getResultArray($query);
	
	fwrite($h,"\n-- TABLE: ".$table."--\n\n");
	
	foreach ($records as $record){
		
		$values="";
		$i = 1;
		foreach ($record as $column) {
			if (is_numeric($column)) $values .= $column;
			else $values .= "'".$column."'";
			
			if ($i<(count($record))) $values .= ",";
			$i++;
		}
		
		fwrite($h, "INSERT INTO " . $table . " VALUES (" . $values . ");\n");
	}
}

fclose($h);

if (SeedDMS_Core_File::gzcompressfile($dump_name,9)) unlink($dump_name);
else UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));

add_log_line();

header("Location:../out/out.BackupTools.php");

?>
