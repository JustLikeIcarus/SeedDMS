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

/**
 * Adds file header to the tar file, it is used before adding file content.
 * code by calmarius at nospam dot atw dot hu
 *
 * @param resource $f file resource (provided by eg. fopen)
 * $param string $phisfn path to file
 * $param string $archfn path to file in archive, directory names must
 *        be followed by '/'
 */
function TarAddHeader($f,$phisfn,$archfn) { /* {{{ */
    $info=@stat($phisfn);
		if($info === false)
			return false;
    $ouid=sprintf("%6s ", decoct($info[4]));
    $ogid=sprintf("%6s ", decoct($info[5]));
    $omode=sprintf("%6s ", decoct(fileperms($phisfn)));
    $omtime=sprintf("%11s", decoct(filemtime($phisfn)));
    if (@is_dir($phisfn)) {
         $type="5";
         $osize=sprintf("%11s ", decoct(0));
    } else {
         $type='';
         $osize=sprintf("%11s ", decoct(filesize($phisfn)));
         clearstatcache();
    }
    $dmajor = '';
    $dminor = '';
    $gname = '';
    $linkname = '';
    $magic = '';
    $prefix = '';
    $uname = '';
    $version = '';
    $chunkbeforeCS=pack("a100a8a8a8a12A12",$archfn, $omode, $ouid, $ogid, $osize, $omtime);
    $chunkafterCS=pack("a1a100a6a2a32a32a8a8a155a12", $type, $linkname, $magic, $version, $uname, $gname, $dmajor, $dminor ,$prefix,'');

    $checksum = 0;
    for ($i=0; $i<148; $i++) $checksum+=ord(substr($chunkbeforeCS,$i,1));
    for ($i=148; $i<156; $i++) $checksum+=ord(' ');
    for ($i=156, $j=0; $i<512; $i++, $j++)    $checksum+=ord(substr($chunkafterCS,$j,1));

    fwrite($f,$chunkbeforeCS,148);
    $checksum=sprintf("%6s ",decoct($checksum));
    $bdchecksum=pack("a8", $checksum);
    fwrite($f,$bdchecksum,8);
    fwrite($f,$chunkafterCS,356);
    return true;
} /* }}} */

// Writes file content to the tar file must be called after a TarAddHeader
// f:file resource provided by fopen
// phisfn: path to file
// code by calmarius at nospam dot atw dot hu
function TarWriteContents($f,$phisfn) { /* {{{ */
	if(!file_exists($phisfn))
		return;
	if (@is_dir($phisfn)) {
		return;
	}
	$size=filesize($phisfn);
	$padding=$size % 512 ? 512-$size%512 : 0;
	$f2=fopen($phisfn,"rb");
	while (!feof($f2)) fwrite($f,fread($f2,1024*1024));
	$pstr=sprintf("a%d",$padding);
	fwrite($f,pack($pstr,''));
} /* }}} */

// Adds 1024 byte footer at the end of the tar file
// f: file resource
// code by calmarius at nospam dot atw dot hu
function TarAddFooter($f) { /* {{{ */
    fwrite($f,pack('a1024',''));
} /* }}} */

// thanks to Doudoux
function getFolderPathPlainAST($folder) { /* {{{ */
    $path="";
    $folderPath = $folder->getPath();
    for ($i = 0; $i  < count($folderPath); $i++) {
        $path .= $folderPath[$i]->getName();
        if ($i+1 < count($folderPath)) $path .= "/";
    }
    return $path;
} /* }}} */

function createFolderTar($folder,$ark) { /* {{{ */
	global $human_readable,$dms;

	$documents=$folder->getDocuments();
	foreach ($documents as $document){

		if (file_exists($dms->contentDir.$document->getDir())){

			if ($human_readable){
				// create an archive containing the files with original names and DMS path
				// thanks to Doudoux
				$latestContent = $document->getLatestContent();
				if (is_object($latestContent)) {
					TarAddHeader(
						$ark,
						$dms->contentDir.$latestContent->getPath(),
						getFolderPathPlainAST($folder)."/".$document->getID()."_".$latestContent->getOriginalFileName());

					TarWriteContents($ark, $dms->contentDir.$latestContent->getPath());
				}
			} else {

				// create a server backup archive
				$handle = opendir($dms->contentDir.$document->getDir());
				while ($entry = readdir($handle) ) {
					if (!is_dir($dms->contentDir.$document->getDir().$entry)){

						TarAddHeader($ark,$dms->contentDir.$document->getDir().$entry,$document->getDir().$entry);
						TarWriteContents($ark,$dms->contentDir.$document->getDir().$entry);
					}
				}
				closedir($handle);
			}
		}
	}

	$subFolders=$folder->getSubfolders();
	foreach ($subFolders as $folder)
		if (!createFolderTar($folder,$ark))
			return false;

	return true;
} /* }}} */

if (!isset($_GET["targetidform2"]) || !is_numeric($_GET["targetidform2"]) || intval($_GET["targetidform2"])<1) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_folder_id"));
}
$folderid = $_GET["targetidform2"];
$folder = $dms->getFolder($folderid);

if (!is_object($folder)) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_folder_id"));
}

$human_readable = (isset($_GET["human_readable"]) && $_GET["human_readable"]==1 ? true : false);

if ($human_readable)$ark_name = $settings->_contentDir.time()."_".$folderid."_HR.tar";
else $ark_name = $settings->_contentDir.time()."_".$folderid.".tar";

$ark = fopen($ark_name,"w");

if (!createFolderTar($folder,$ark)) {
	fclose($ark);
	unlink($ark_name);
	UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
}

TarAddFooter($ark);
fclose($ark);

if (SeedDMS_Core_File::gzcompressfile($ark_name,9)) unlink($ark_name);
else UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));

add_log_line();

header("Location:../out/out.BackupTools.php");

?>
