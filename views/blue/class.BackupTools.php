<?php
/**
 * Implementation of BackupTools view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.BlueStyle.php");

/**
 * Class which outputs the html page for BackupTools view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_BackupTools extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$contentdir = $this->params['contentdir'];

		$this->htmlStartPage(getMLText("backup_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->contentHeading(getMLText("backup_tools"));
		$this->contentContainerStart();
		print getMLText("space_used_on_data_folder")." : ".SeedDMS_Core_File::format_filesize(dskspace($contentdir));
		$this->contentContainerEnd();

		// versioning file creation ////////////////////////////////////////////////////

		$this->contentHeading(getMLText("versioning_file_creation"));
		$this->contentContainerStart();
		print "<p>".getMLText("versioning_file_creation_warning")."</p>\n";

		print "<form action=\"../op/op.CreateVersioningFiles.php\" name=\"form1\">";
		$this->printFolderChooser("form1",M_READWRITE);
		print "<input type='submit' name='' value='".getMLText("versioning_file_creation")."'/>";
		print "</form>\n";

		$this->contentContainerEnd();

		// archive creation ////////////////////////////////////////////////////////////

		$this->contentHeading(getMLText("archive_creation"));
		$this->contentContainerStart();
		print "<p>".getMLText("archive_creation_warning")."</p>\n";

		print "<form action=\"../op/op.CreateFolderArchive.php\" name=\"form2\">";
		$this->printFolderChooser("form2",M_READWRITE);
		print "<input type=\"checkbox\" name=\"human_readable\" value=\"1\">".getMLText("human_readable");
		print "<input type='submit' name='' value='".getMLText("archive_creation")."'/>";
		print "</form>\n";

		// list backup files
		$this->contentSubHeading(getMLText("backup_list"));

		$print_header=true;

		$handle = opendir($contentdir);
		$entries = array();
		while ($e = readdir($handle)){
			if (is_dir($contentdir.$e)) continue;
			if (strpos($e,".tar.gz")==FALSE) continue;
			$entries[] = $e;
		}
		closedir($handle);

		sort($entries);
		$entries = array_reverse($entries);

		foreach ($entries as $entry){

			if ($print_header){
				print "<table class=\"folderView\">\n";
				print "<thead>\n<tr>\n";
				print "<th></th>\n";
				print "<th>".getMLText("folder")."</th>\n";
				print "<th>".getMLText("creation_date")."</th>\n";
				print "<th>".getMLText("file_size")."</th>\n";
				print "<th></th>\n";
				print "</tr>\n</thead>\n<tbody>\n";
				$print_header=false;
			}

			$folderid=substr($entry,strpos($entry,"_")+1);
			$folder=$dms->getFolder((int)$folderid);
					
			print "<tr>\n";
			print "<td><a href=\"../op/op.Download.php?arkname=".$entry."\">".$entry."</a></td>\n";
			if (is_object($folder)) print "<td>".htmlspecialchars($folder->getName())."</td>\n";
			else print "<td>".getMLText("unknown_id")."</td>\n";
			print "<td>".getLongReadableDate(filectime($contentdir.$entry))."</td>\n";
			print "<td>".SeedDMS_Core_File::format_filesize(filesize($contentdir.$entry))."</td>\n";
			print "<td><ul class=\"actions\">";
			print "<li><a href=\"out.RemoveArchive.php?arkname=".$entry."\">".getMLText("backup_remove")."</a></li>";
			print "</ul></td>\n";	
			print "</tr>\n";
		}

		if ($print_header) printMLText("empty_notify_list");
		else print "</table>\n";

		$this->contentContainerEnd();

		// dump creation ///////////////////////////////////////////////////////////////

		$this->contentHeading(getMLText("dump_creation"));
		$this->contentContainerStart();
		print "<p>".getMLText("dump_creation_warning")."</p>\n";

		print "<form action=\"../op/op.CreateDump.php\" name=\"form4\">";
		print "<input type='submit' name='' value='".getMLText("dump_creation")."'/>";
		print "</form>\n";

		// list backup files
		$this->contentSubHeading(getMLText("dump_list"));

		$print_header=true;

		$handle = opendir($contentdir);
		$entries = array();
		while ($e = readdir($handle)){
			if (is_dir($contentdir.$e)) continue;
			if (strpos($e,".sql.gz")==FALSE) continue;
			$entries[] = $e;
		}
		closedir($handle);

		sort($entries);
		$entries = array_reverse($entries);

		foreach ($entries as $entry){

			if ($print_header){
				print "<table class=\"folderView\">\n";
				print "<thead>\n<tr>\n";
				print "<th></th>\n";
				print "<th>".getMLText("creation_date")."</th>\n";
				print "<th>".getMLText("file_size")."</th>\n";
				print "<th></th>\n";
				print "</tr>\n</thead>\n<tbody>\n";
				$print_header=false;
			}

			print "<tr>\n";
			print "<td><a href=\"../op/op.Download.php?dumpname=".$entry."\">".$entry."</a></td>\n";
			print "<td>".getLongReadableDate(filectime($contentdir.$entry))."</td>\n";
			print "<td>".SeedDMS_Core_File::format_filesize(filesize($contentdir.$entry))."</td>\n";
			print "<td><ul class=\"actions\">";
			print "<li><a href=\"out.RemoveDump.php?dumpname=".$entry."\">".getMLText("dump_remove")."</a></li>";
			print "</ul></td>\n";	
			print "</tr>\n";
		}

		if ($print_header) printMLText("empty_notify_list");
		else print "</table>\n";

		$this->contentContainerEnd();

		// files deletion //////////////////////////////////////////////////////////////

		$this->contentHeading(getMLText("files_deletion"));
		$this->contentContainerStart();
		print "<p>".getMLText("files_deletion_warning")."</p>\n";

		print "<form action=\"../out/out.RemoveFolderFiles.php\" name=\"form3\">";
		$this->printFolderChooser("form3",M_READWRITE);
		print "<input type='submit' name='' value='".getMLText("files_deletion")."'/>";
		print "</form>\n";

		$this->contentContainerEnd();

		$this->htmlEndPage();
	} /* }}} */
}
?>
