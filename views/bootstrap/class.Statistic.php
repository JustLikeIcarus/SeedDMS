<?php
/**
 * Implementation of Statistic view
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
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for Statistic view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Statistic extends SeedDMS_Bootstrap_Style {
		var $dms;
		var $folder_count;
		var $document_count;
		var $file_count;
		var $storage_size;

	function getAccessColor($mode) { /* {{{ */
		if ($mode == M_NONE)
			return "gray";
		else if ($mode == M_READ)
			return "green";
		else if ($mode == M_READWRITE)
			return "blue";
		else // if ($mode == M_ALL)
			return "red";
	} /* }}} */

	function printFolder($folder) { /* {{{ */
		$this->folder_count++;
		$folder_size=0;
		$doc_count=0;

		$color = $folder->inheritsAccess() ? "black" : $this->getAccessColor($folder->getDefaultAccess());

		print "<li class=\"folderClass\">";
		print "<a style=\"color: $color\" href=\"out.ViewFolder.php?folderid=".$folder->getID()."\">".htmlspecialchars($folder->getName()) ."</a>";

		$owner = $folder->getOwner();
		$color = $this->getAccessColor(M_ALL);
		print " [<span style=\"color: $color\">".htmlspecialchars($owner->getFullName())."</span>] ";

		if (! $folder->inheritsAccess())
			$this->printAccessList($folder);

		$subFolders = $folder->getSubFolders();
		$documents = $folder->getDocuments();

		print "<ul>";

		foreach ($subFolders as $sub) $folder_size += $this->printFolder($sub);
		foreach ($documents as $document){
			$doc_count++;
			$folder_size += $this->printDocument($document);
		}

		print "</ul>";

		print "<small>".SeedDMS_Core_File::format_filesize($folder_size).", ".$doc_count." ".getMLText("documents")."</small>\n";

		print "</li>";

		return $folder_size;
	} /* }}} */

	function printDocument($document) { /* {{{ */
		$this->document_count++;

		$local_file_count=0;
		$folder_size=0;

		if (file_exists($this->dms->contentDir.$document->getDir())) {
			$handle = opendir($this->dms->contentDir.$document->getDir());
			while ($entry = readdir($handle) ) {
				if (is_dir($this->dms->contentDir.$document->getDir().$entry)) continue;
				else{
					$local_file_count++;
					$folder_size += filesize($this->dms->contentDir.$document->getDir().$entry);
				}

			}
			closedir($handle);
		}
		$this->storage_size += $folder_size;

		$color = $document->inheritsAccess() ? "black" : $this->getAccessColor($document->getDefaultAccess());
		print "<li class=\"documentClass\">";
		print "<a style=\"color: $color\" href=\"out.ViewDocument.php?documentid=".$document->getID()."\">".htmlspecialchars($document->getName())."</a>";

		$owner = $document->getOwner();
		$color = $this->getAccessColor(M_ALL);
		print " [<span style=\"color: $color\">".htmlspecialchars($owner->getFullName())."</span>] ";

		if (! $document->inheritsAccess()) $this->printAccessList($document);

		print "<small>".SeedDMS_Core_File::format_filesize($folder_size).", ".$local_file_count." ".getMLText("files")."</small>\n";

		print "</li>";

		$this->file_count += $local_file_count;
		return $folder_size;
	} /* }}} */

	function printAccessList($obj) { /* {{{ */
		$accessList = $obj->getAccessList();
		if (count($accessList["users"]) == 0 && count($accessList["groups"]) == 0)
			return;

		print " <span>(";

		for ($i = 0; $i < count($accessList["groups"]); $i++)
		{
			$group = $accessList["groups"][$i]->getGroup();
			$color = $this->getAccessColor($accessList["groups"][$i]->getMode());
			print "<span style=\"color: $color\">".htmlspecialchars($group->getName())."</span>";
			if ($i+1 < count($accessList["groups"]) || count($accessList["users"]) > 0)
				print ", ";
		}
		for ($i = 0; $i < count($accessList["users"]); $i++)
		{
			$user = $accessList["users"][$i]->getUser();
			$color = $this->getAccessColor($accessList["users"][$i]->getMode());
			print "<span style=\"color: $color\">".htmlspecialchars($user->getFullName())."</span>";
			if ($i+1 < count($accessList["users"]))
				print ", ";
		}
		print ")</span>";
	} /* }}} */

	function show() { /* {{{ */
		$this->dms = $this->params['dms'];
		$user = $this->params['user'];
		$rootfolder = $this->params['rootfolder'];

		$this->htmlStartPage(getMLText("folders_and_documents_statistic"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->folder_count=0;
		$this->document_count=0;
		$this->file_count=0;
		$this->storage_size=0;
?>
<style type="text/css">
.folderClass {
	list-style-image : url(<?php $this->printImgPath("folder_closed.gif");?>);
	list-style : url(<?php $this->printImgPath("folder_closed.gif");?>);
}

.documentClass {
	list-style-image : url(<?php $this->printImgPath("file.gif");?>);
	list-style : url(<?php $this->printImgPath("file.gif");?>);
}
</style>

<script language="JavaScript">

function showDocument(id) {
	url = "out.DetailedStatistic.php?documentid=" + id;
	alert(url);
}

function showFolder(id) {
	url = "out.DetailedStatistic.php?folderid=" + id;
	alert(url);
}

</script>

<?php

$this->contentHeading(getMLText("folders_and_documents_statistic"));
echo "<div class=\"row-fluid\">\n";
echo "<div class=\"span8\">\n";
echo "<div class=\"well\">\n";

print "<table class=\"table-condensed\"><tr><td>\n";

print "<ul>\n";
$this->printFolder($rootfolder);
print "</ul>\n";

print "</td></tr>";

print "</table>\n";
echo "</div>\n";
echo "</div>\n";

echo "<div class=\"span4\">\n";
echo "<div class=\"well\">\n";
print "<legend>".getMLText("legend")."</legend>\n";
print "<ul class=\"unstyled\">\n";
print "<li><span style=\"color:black\">".getMLText("access_inheritance")." </span></li>";
print "<li><span style=\"color:".$this->getAccessColor(M_ALL)."\">".getMLText("access_mode_all")." </span></li>";
print "<li><span style=\"color:".$this->getAccessColor(M_READWRITE)."\">".getMLText("access_mode_readwrite")." </span></li>";
print "<li><span style=\"color:".$this->getAccessColor(M_READ)."\">".getMLText("access_mode_read")." </span></li>";
print "<li><span style=\"color:".$this->getAccessColor(M_NONE)."\">".getMLText("access_mode_none")." </span></li>";
print "</ul>\n";

print "<legend>".getMLText("statistic")."</legend>\n";
print "<ul class=\"unstyled\">\n";
print "<li>".getMLText("folders").": ".$this->folder_count."</li>\n";
print "<li>".getMLText("documents").": ".$this->document_count."</li>\n";
print "<li>".getMLText("files").": ".$this->file_count."</li>\n";
print "<li>".getMLText("storage_size").": ".SeedDMS_Core_File::format_filesize($this->storage_size)."</li>\n";

print "</ul>\n";

echo "</div>\n";
echo "</div>\n";
echo "</div>\n";


$this->contentContainerEnd();
$this->htmlEndPage();
	} /* }}} */
}
?>
