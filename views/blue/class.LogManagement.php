<?php
/**
 * Implementation of LogManagement view
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
 * Class which outputs the html page for LogManagement view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_LogManagement extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$contentdir = $this->params['contentdir'];
		$logname = $this->params['logname'];

		$this->htmlStartPage(getMLText("backup_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->contentHeading(getMLText("log_management"));
		$this->contentContainerStart();

		$print_header=true;

		$handle = opendir($contentdir);
		$entries = array();
		while ($e = readdir($handle)){
			if (is_dir($contentdir.$e)) continue;
			if (strpos($e,".log")==FALSE) continue;
			if (strcmp($e,"current.log")==0) continue;
			$entries[] = $e;
		}
		closedir($handle);

		sort($entries);
		$entries = array_reverse($entries);

		foreach ($entries as $entry){
			
			if ($print_header){
				print "<table class=\"folderView\">\n";
				print "<thead>\n<tr>\n";
				print "<th>".getMLText("name")."</th>\n";
				print "<th>".getMLText("creation_date")."</th>\n";
				print "<th>".getMLText("file_size")."</th>\n";
				print "<th></th>\n";
				print "</tr>\n</thead>\n<tbody>\n";
				$print_header=false;
			}
					
			print "<tr>\n";
			print "<td><a href=\"out.LogManagement.php?logname=".$entry."\">".$entry."</a></td>\n";
			print "<td>".getLongReadableDate(filectime($contentdir.$entry))."</td>\n";
			print "<td>".formatted_size(filesize($contentdir.$entry))."</td>\n";
			print "<td><ul class=\"actions\">";
			
			print "<li><a href=\"out.RemoveLog.php?logname=".$entry."\">".getMLText("rm_file")."</a></li>";
			
			print "<li><a href=\"../op/op.Download.php?logname=".$entry."\">".getMLText("download")."</a></li>";
				
			print "</ul></td>\n";	
			print "</tr>\n";
		}

		if ($print_header) printMLText("empty_notify_list");
		else print "</table>\n";

		$this->contentContainerEnd();

		if ($logname && file_exists($contentdir.$logname)){

			$this->contentHeading(" ");
			$this->contentContainerStart();
			
			$this->contentSubHeading(sanitizeString($logname));

			echo "<div class=\"logview\">";
			echo "<pre>\n";
			readfile($contentdir.$logname);
			echo "</pre>\n";
			echo "</div>";

			$this->contentContainerEnd();
		}

		$this->htmlEndPage();
	} /* }}} */
}
?>
