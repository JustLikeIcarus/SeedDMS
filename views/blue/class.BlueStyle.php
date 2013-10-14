<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2009-2012 Uwe Steinmann
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


class SeedDMS_Blue_Style extends SeedDMS_View_Common {
	function __construct($params, $theme='blue') {
		$this->theme = $theme;
		$this->params = $params;
	}

	protected function htmlStartPage($title="", $bodyClass="") { /* {{{ */

		if(file_exists("../themes/".$this->theme."/HTMLHead.html")) {
			include("../themes/".$this->theme."/HTMLHead.html");
		} else {
			echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"\n".
				"\"http://www.w3.org/TR/html4/strict.dtd\">\n";
			echo "<html>\n<head>\n";
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
			echo "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"../styles/".$this->theme."/style.css\"/>\n";
			echo "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"../styles/print.css\" media=\"print\"/>\n";
			echo "<link rel='shortcut icon' href='../styles/".$this->theme."/favicon.ico' type='image/x-icon'/>\n";
			echo "<script type='text/javascript' src='../js/jquery.min.js'></script>\n";
			echo "<script type='text/javascript' src='../js/jquery.passwordstrength.js'></script>\n";
			echo '<script type="text/javascript" src="../styles/'.$this->theme.'/application.js"></script>'."\n";
			echo "<title>".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS").(strlen($title)>0 ? ": " : "").htmlspecialchars($title)."</title>\n";
			echo "</head>\n";
			echo "<body".(strlen($bodyClass)>0 ? " class=\"".$bodyClass."\"" : "").">\n";
		}
	} /* }}} */

	protected function htmlEndPage() { /* {{{ */

		$this->footNote();
		if(file_exists("../themes/".$this->theme."/HTMLFoot.html")) {
			include("../themes/".$this->theme."/HTMLFoot.html");
		} else {
			echo "</body>\n</html>\n";
		}
	} /* }}} */

	function footNote() { /* {{{ */
		if ($this->params['printdisclaimer']){
			echo "<div class=\"disclaimer\">".getMLText("disclaimer")."</div>";
		}

		if (isset($this->params['footnote']) && strlen((string)$this->params['footnote'])>0) {
			echo "<div class=\"footNote\">".(string)$this->params['footnote']."</div>";
		}
	
		return;
	} /* }}} */

	function contentStart() { /* {{{ */
	} /* }}} */

	function contentEnd() { /* {{{ */
	} /* }}} */

	function globalBanner() { /* {{{ */
		echo "<div class=\"globalBox\" id=\"noNav\">\n";
		echo "<div class=\"globalTR\"></div>\n";
		echo "<div id=\"logo\"><img src='../styles/logo.png'></div>\n";
		echo "<div class=\"siteNameLogin\">".
			(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS").
			"</div>\n";
		echo "<div style=\"clear: both; height: 0px; font-size:0;\">&nbsp;</div>\n".
			"</div>\n";
		return;
	} /* }}} */

	function globalNavigation($folder=null) { /* {{{ */
		echo "<div class=\"globalBox\">\n";
		echo "<div class=\"globalTR\"></div>\n";
		if(isset($this->params['user']) && $this->params['user']) {
		echo "<ul class=\"globalNav\">\n";
			echo "<li id=\"first\"><a href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".getMLText("content")."</a></li>\n";
			echo "<li><a href=\"../out/out.SearchForm.php?folderid=".$this->params['rootfolderid']."\">".getMLText("search")."</a></li>\n";
			if ($this->params['enablecalendar']) echo "<li><a href=\"../out/out.Calendar.php?mode=".$this->params['calendardefaultview']."\">".getMLText("calendar")."</a></li>\n";
			if (!$this->params['user']->isGuest()) echo "<li><a href=\"../out/out.MyDocuments.php?inProcess=1\">".getMLText("my_documents")."</a></li>\n";
			if (!$this->params['user']->isGuest()) echo "<li><a href=\"../out/out.MyAccount.php\">".getMLText("my_account")."</a></li>\n";
			if ($this->params['user']->isAdmin()) echo "<li><a href=\"../out/out.AdminTools.php\">".getMLText("admin_tools")."</a></li>\n";
			echo "<li><a href=\"../out/out.Help.php\">".getMLText("help")."</a></li>\n";
			echo "<li id=\"search\">\n";
			echo "<form action=\"../op/op.Search.php\">";
			if ($folder!=null && is_object($folder) && !strcasecmp(get_class($folder), "SeedDMS_Core_Folder")) {
				echo "<input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\" />";
			}
			echo "<input type=\"hidden\" name=\"navBar\" value=\"1\" />";
			echo "<input type=\"hidden\" name=\"searchin[]\" value=\"1\" />";
			echo "<input type=\"hidden\" name=\"searchin[]\" value=\"2\" />";
			echo "<input type=\"hidden\" name=\"searchin[]\" value=\"3\" />";
			echo "<input name=\"query\" type=\"text\" size=\"20\" />";
			if($this->params['enablefullsearch']) {
				echo "<input type=\"checkbox\" name=\"fullsearch\" value=\"1\" title=\"".getMLText('fullsearch_hint')."\"/> ".getMLText('fullsearch_hint')."";
			}
			echo "<input type=\"submit\" value=\"".getMLText("search")."\" id=\"searchButton\"/></form>\n";
			echo "</li>\n</ul>\n";
			echo "<div id=\"logo\"><img src='../styles/logo.png'></div>\n";
			echo "<div class=\"siteName\">".
				(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS").
				"</div>\n";
			echo "<span class=\"absSpacerNorm\"></span>\n";
			echo "<div id=\"signatory\">".getMLText("signed_in_as")." ".htmlspecialchars($this->params['user']->getFullName()).
				" (<a href=\"../op/op.Logout.php\">".getMLText("sign_out")."</a>).</div>\n";
		}
		echo "<div style=\"clear: both; height: 0px; font-size:0;\">&nbsp;</div>\n".
			"</div>\n";
		return;
	} /* }}} */

	function pageNavigation($pageTitle, $pageType=null, $extra=null) { /* {{{ */
		global $settings;

		echo "<div class=\"headingContainer\">\n";
		// This spacer span is an awful hack, but it is the only way I know to
		// get the spacer to match the mainheading content's size.
		echo "<span class=\"absSpacerTitle\">".($settings->_titleDisplayHack ? $pageTitle : "")."</span>\n";
		echo "<div class=\"mainHeading\">".$pageTitle."</div>\n";
		echo "<div style=\"clear: both; height: 0px; font-size:0;\"></div>\n</div>\n";

		if ($pageType!=null && strcasecmp($pageType, "noNav")) {
			echo "<div class=\"localNavContainer\">\n";
			switch ($pageType) {
				case "view_folder":
					$this->folderNavigationBar($extra);
					break;
				case "view_document":
					$this->documentNavigationBar();
					break;
				case "my_documents":
					$this->myDocumentsNavigationBar();
					break;
				case "my_account":
					$this->accountNavigationBar();
					break;
				case "admin_tools":
					$this->adminToolsNavigationBar();
					break;
				case "calendar";
					$this->calendarNavigationBar($extra);
					break;
			}
			echo "<div style=\"clear: both; height: 0px; font-size:0;\"></div>\n</div>\n";
		}

		return;
	} /* }}} */

	function folderNavigationBar($folder) { /* {{{ */
		if (!is_object($folder) || strcasecmp(get_class($folder), "SeedDMS_Core_Folder")) {
			echo "<ul class=\"localNav\">\n";
			echo "</ul>\n";
			return;
		}
		$accessMode = $folder->getAccessMode($this->params['user']);
		$folderID = $folder->getID();
		echo "<ul class=\"localNav\">\n";
		if ($accessMode == M_READ && !$this->params['user']->isGuest()) {
			echo "<li id=\"first\"><a href=\"../out/out.FolderNotify.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_folder_notify")."</a></li>\n";
		}
		else if ($accessMode >= M_READWRITE) {
			echo "<li id=\"first\"><a href=\"../out/out.AddSubFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("add_subfolder")."</a></li>\n";
			echo "<li><a href=\"../out/out.AddDocument.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("add_document")."</a></li>\n";
			if($this->params['enablelargefileupload'])
				echo "<li><a href=\"../out/out.AddMultiDocument.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("add_multiple_documents")."</a></li>\n";
			echo "<li><a href=\"../out/out.EditFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_folder_props")."</a></li>\n";
			echo "<li><a href=\"../out/out.FolderNotify.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_existing_notify")."</a></li>\n";
			if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
				echo "<li><a href=\"../out/out.MoveFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("move_folder")."</a></li>\n";
		}
		if ($accessMode == M_ALL) {
			if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
				echo "<li><a href=\"../out/out.RemoveFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("rm_folder")."</a></li>\n";
			echo "<li><a href=\"../out/out.FolderAccess.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_folder_access")."</a></li>\n";
		}
		echo "</ul>\n";
		return;
	} /* }}} */

	function documentNavigationBar()	{ /* {{{ */
		global $document;

		$accessMode = $document->getAccessMode($this->params['user']);
		$docid=".php?documentid=" . $document->getID();

		echo "<ul class=\"localNav\">\n";
		if ($accessMode >= M_READWRITE) {
			if (!$document->isLocked()) {
				echo "<li id=\"first\"><a href=\"../out/out.UpdateDocument". $docid ."\">".getMLText("update_document")."</a></li>";
				echo "<li><a href=\"../op/op.LockDocument". $docid ."\">".getMLText("lock_document")."</a></li>";
				echo "<li><a href=\"../out/out.EditDocument". $docid ."\">".getMLText("edit_document_props")."</a></li>";
				echo "<li><a href=\"../out/out.MoveDocument". $docid ."\">".getMLText("move_document")."</a></li>";
			}
			else {
				$lockingUser = $document->getLockingUser();
				if (($lockingUser->getID() == $this->params['user']->getID()) || ($document->getAccessMode($this->params['user']) == M_ALL)) {
					echo "<li id=\"first\"><a href=\"../out/out.UpdateDocument". $docid ."\">".getMLText("update_document")."</a></li>";
					echo "<li><a href=\"../op/op.UnlockDocument". $docid ."\">".getMLText("unlock_document")."</a></li>";
					echo "<li><a href=\"../out/out.EditDocument". $docid ."\">".getMLText("edit_document_props")."</a></li>";
					echo "<li><a href=\"../out/out.MoveDocument". $docid ."\">".getMLText("move_document")."</a></li>";
					echo "<li><a href=\"../out/out.SetExpires". $docid ."\">".getMLText("expires")."</a></li>";
				}
			}
		}
		if ($accessMode == M_ALL) {
			echo "<li><a href=\"../out/out.RemoveDocument". $docid ."\">".getMLText("rm_document")."</a></li>";
			echo "<li><a href=\"../out/out.DocumentAccess". $docid ."\">".getMLText("edit_document_access")."</a></li>";
		}
		if ($accessMode >= M_READ && !$this->params['user']->isGuest()) {
			echo "<li><a href=\"../out/out.DocumentNotify". $docid ."\">".getMLText("edit_existing_notify")."</a></li>";
		}
		echo "</ul>\n";
		return;
	} /* }}} */

	function accountNavigationBar() { /* {{{ */
		echo "<ul class=\"localNav\">\n";
		if (!$this->params['disableselfedit']) echo "<li id=\"first\"><a href=\"../out/out.EditUserData.php\">".getMLText("edit_user_details")."</a></li>\n";
		
		if (!$this->params['user']->isAdmin()) 
			echo "<li><a href=\"../out/out.UserDefaultKeywords.php\">".getMLText("edit_default_keywords")."</a></li>\n";

		echo "<li><a href=\"../out/out.ManageNotify.php\">".getMLText("edit_existing_notify")."</a></li>\n";

		if ($this->params['enableusersview']){
			echo "<li><a href=\"../out/out.UsrView.php\">".getMLText("users")."</a></li>\n";
			echo "<li><a href=\"../out/out.GroupView.php\">".getMLText("groups")."</a></li>\n";
		}		
		echo "</ul>\n";
		return;
	} /* }}} */

	function myDocumentsNavigationBar() { /* {{{ */

		echo "<ul class=\"localNav\">\n";
		echo "<li id=\"first\"><a href=\"../out/out.MyDocuments.php?inProcess=1\">".getMLText("documents_in_process")."</a></li>\n";
		echo "<li><a href=\"../out/out.MyDocuments.php\">".getMLText("all_documents")."</a></li>\n";
		echo "<li><a href=\"../out/out.ReviewSummary.php\">".getMLText("review_summary")."</a></li>\n";
		echo "<li><a href=\"../out/out.ApprovalSummary.php\">".getMLText("approval_summary")."</a></li>\n";
		echo "</ul>\n";
		return;
	} /* }}} */

	function adminToolsNavigationBar() { /* {{{ */
		echo "<ul class=\"localNav\">\n";
		echo "<li id=\"first\"><a href=\"../out/out.Statistic.php\">".getMLText("folders_and_documents_statistic")."</a></li>\n";
		echo "<li><a href=\"../out/out.BackupTools.php\">".getMLText("backup_tools")."</a></li>\n";
		if ($this->params['logfileenable']) echo "<li><a href=\"../out/out.LogManagement.php\">".getMLText("log_management")."</a></li>\n";
		echo "<li><a href=\"../out/out.UsrMgr.php\">".getMLText("user_management")."</a></li>\n";
		echo "<li><a href=\"../out/out.GroupMgr.php\">".getMLText("group_management")."</a></li>\n";
		echo "<li><a href=\"../out/out.DefaultKeywords.php\">".getMLText("global_default_keywords")."</a></li>\n";
		echo "</ul>\n";
		return;
	} /* }}} */
	
	function calendarNavigationBar($d){ /* {{{ */
		$ds="&day=".$d[0]."&month=".$d[1]."&year=".$d[2];
	
		echo "<ul class=\"localNav\">\n";
		echo "<li><a href=\"../out/out.Calendar.php?mode=w".$ds."\">".getMLText("week_view")."</a></li>\n";
		echo "<li><a href=\"../out/out.Calendar.php?mode=m".$ds."\">".getMLText("month_view")."</a></li>\n";
		echo "<li><a href=\"../out/out.Calendar.php?mode=y".$ds."\">".getMLText("year_view")."</a></li>\n";
		if (!$this->params['user']->isGuest()) echo "<li><a href=\"../out/out.AddEvent.php\">".getMLText("add_event")."</a></li>\n";
		echo "</ul>\n";
		return;
	
	} /* }}} */

	function pageList($pageNumber, $totalPages, $baseURI, $params) { /* {{{ */

		if (!is_numeric($pageNumber) || !is_numeric($totalPages) || $totalPages<2) {
			return;
		}

		// Construct the basic URI based on the $_GET array. One could use a
		// regular expression to strip out the pg (page number) variable to
		// achieve the same effect. This seems to be less haphazard though...
		$resultsURI = $baseURI;
		$first=true;
		foreach ($params as $key=>$value) {
			// Don't include the page number in the basic URI. This is added in
			// during the list display loop.
			if (!strcasecmp($key, "pg")) {
				continue;
			}
			if (is_array($value)) {
				foreach ($value as $subvalue) {
					$resultsURI .= ($first ? "?" : "&").$key."%5B%5D=".$subvalue;
					$first = false;
				}
			}
			else {
					$resultsURI .= ($first ? "?" : "&").$key."=".$value;
			}
			$first = false;
		}

		echo "<div class=\"pageList\">";
		echo getMLText("results_page").": ";
		for ($i = 1; $i  <= $totalPages; $i++) {
			if ($i == $pageNumber)  echo "<span class=\"selected\">".$i."</span> ";
			else echo "<a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$i."\">".$i."</a>"." ";
		}
		if ($totalPages>1) {
			echo "<a href=\"".$resultsURI.($first ? "?" : "&")."pg=all\">".getMLText("all_pages")."</a>"." ";
		}
		echo "</div>";

		return;
	} /* }}} */

	function contentContainer($content) { /* {{{ */
		echo "<div class=\"contentContainer\">\n";
		echo "<div class=\"content\">\n";
		echo "<div class=\"content-l\"><div class=\"content-r\"><div class=\"content-br\"><div class=\"content-bl\">\n";
		echo $content;
		echo "</div></div></div></div>\n</div>\n</div>\n";
		return;
	} /* }}} */

	function contentContainerStart() { /* {{{ */

		echo "<div class=\"contentContainer\">\n";
		echo "<div class=\"content\">\n";
		echo "<div class=\"content-l\"><div class=\"content-r\"><div class=\"content-br\"><div class=\"content-bl\">\n";
		return;
	} /* }}} */

	function contentContainerEnd() { /* {{{ */

		echo "</div></div></div></div>\n</div>\n</div>\n";
		return;
	} /* }}} */

	function contentHeading($heading, $noescape=false) { /* {{{ */

		if($noescape)
			echo "<div class=\"contentHeading\">".$heading."</div>\n";
		else
			echo "<div class=\"contentHeading\">".htmlspecialchars($heading)."</div>\n";
		return;
	} /* }}} */

	function contentSubHeading($heading, $first=false) { /* {{{ */

		echo "<div class=\"contentSubHeading\"".($first ? " id=\"first\"" : "").">".htmlspecialchars($heading)."</div>\n";
		return;
	} /* }}} */

	function getMimeIcon($fileType) { /* {{{ */
		// for extension use LOWER CASE only
		$icons = array();
		$icons["txt"]  = "txt.png";
		$icons["text"] = "txt.png";
		$icons["doc"]  = "word.png";
		$icons["dot"]  = "word.png";
		$icons["docx"] = "word.png";
		$icons["dotx"] = "word.png";
		$icons["rtf"]  = "document.png";
		$icons["xls"]  = "excel.png";
		$icons["xlt"]  = "excel.png";
		$icons["xlsx"] = "excel.png";
		$icons["xltx"] = "excel.png";
		$icons["ppt"]  = "powerpoint.png";
		$icons["pot"]  = "powerpoint.png";
		$icons["pptx"] = "powerpoint.png";
		$icons["potx"] = "powerpoint.png";
		$icons["exe"]  = "binary.png";
		$icons["html"] = "html.png";
		$icons["htm"]  = "html.png";
		$icons["gif"]  = "image.png";
		$icons["jpg"]  = "image.png";
		$icons["jpeg"] = "image.png";
		$icons["bmp"]  = "image.png";
		$icons["png"]  = "image.png";
		$icons["tif"]  = "image.png";
		$icons["tiff"] = "image.png";
		$icons["log"]  = "log.png";
		$icons["midi"] = "midi.png";
		$icons["pdf"]  = "pdf.png";
		$icons["wav"]  = "sound.png";
		$icons["mp3"]  = "sound.png";
		$icons["c"]    = "source_c.png";
		$icons["cpp"]  = "source_cpp.png";
		$icons["h"]    = "source_h.png";
		$icons["java"] = "source_java.png";
		$icons["py"]   = "source_py.png";
		$icons["tar"]  = "tar.png";
		$icons["gz"]   = "gz.png";
		$icons["7z"]   = "gz.png";
		$icons["bz"]   = "gz.png";
		$icons["bz2"]  = "gz.png";
		$icons["tgz"]  = "gz.png";
		$icons["zip"]  = "gz.png";
		$icons["rar"]  = "gz.png";
		$icons["mpg"]  = "video.png";
		$icons["avi"]  = "video.png";
		$icons["tex"]  = "tex.png";
		$icons["ods"]  = "x-office-spreadsheet.png";
		$icons["ots"]  = "x-office-spreadsheet.png";
		$icons["sxc"]  = "x-office-spreadsheet.png";
		$icons["stc"]  = "x-office-spreadsheet.png";
		$icons["odt"]  = "x-office-document.png";
		$icons["ott"]  = "x-office-document.png";
		$icons["sxw"]  = "x-office-document.png";
		$icons["stw"]  = "x-office-document.png";
		$icons["odp"]  = "ooo_presentation.png";
		$icons["otp"]  = "ooo_presentation.png";
		$icons["sxi"]  = "ooo_presentation.png";
		$icons["sti"]  = "ooo_presentation.png";
		$icons["odg"]  = "ooo_drawing.png";
		$icons["otg"]  = "ooo_drawing.png";
		$icons["sxd"]  = "ooo_drawing.png";
		$icons["std"]  = "ooo_drawing.png";
		$icons["odf"]  = "ooo_formula.png";
		$icons["sxm"]  = "ooo_formula.png";
		$icons["smf"]  = "ooo_formula.png";
		$icons["mml"]  = "ooo_formula.png";

		$icons["default"] = "default.png";

		$ext = strtolower(substr($fileType, 1));
		if (isset($icons[$ext])) {
			return $icons[$ext];
		}
		else {
			return $icons["default"];
		}
	} /* }}} */

	function printDateChooser($defDate = -1, $varName) { /* {{{ */
	
		if ($defDate == -1)
			$defDate = mktime();
		$day   = date("d", $defDate);
		$month = date("m", $defDate);
		$year  = date("Y", $defDate);

		print "<select name=\"" . $varName . "day\">\n";
		for ($i = 1; $i <= 31; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($day) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select> \n";
		print "<select name=\"" . $varName . "month\">\n";
		for ($i = 1; $i <= 12; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($month) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select> \n";
		print "<select name=\"" . $varName . "year\">\n";	
		for ($i = $year-5 ; $i <= $year+5 ; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($year) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select>";
	} /* }}} */

	function printSequenceChooser($objArr, $keepID = -1) { /* {{{ */
		if (count($objArr) > 0) {
			$max = $objArr[count($objArr)-1]->getSequence() + 1;
			$min = $objArr[0]->getSequence() - 1;
		}
		else {
			$max = 1.0;
		}
		print "<select name=\"sequence\">\n";
		if ($keepID != -1) {
			print "  <option value=\"keep\">" . getMLText("seq_keep");
		}
		print "  <option value=\"".$max."\">" . getMLText("seq_end");
		if (count($objArr) > 0) {
			print "  <option value=\"".$min."\">" . getMLText("seq_start");
		}
		for ($i = 0; $i < count($objArr) - 1; $i++) {
			if (($objArr[$i]->getID() == $keepID) || (($i + 1 < count($objArr)) && ($objArr[$i+1]->getID() == $keepID))) {
				continue;
			}
			$index = ($objArr[$i]->getSequence() + $objArr[$i+1]->getSequence()) / 2;
			print "  <option value=\"".$index."\">" . getMLText("seq_after", array("prevname" => htmlspecialchars($objArr[$i]->getName())));
		}
		print "</select>";
	} /* }}} */
	
	function printDocumentChooser($formName) { /* {{{ */
?>
		<script language="JavaScript">
		var openDlg;
		function chooseDoc<?php print $formName ?>() {
			openDlg = open("../out/out.DocumentChooser.php?folderid=<?php echo $this->params['rootfolderid']?>&form=<?php echo urlencode($formName)?>", "openDlg", "width=480,height=480,scrollbars=yes,resizable=yes,status=yes");
		}
		</script>
<?php
		print "<input type=\"Hidden\" name=\"docid".$formName."\">";
		print "<input disabled name=\"docname".$formName."\">";
		print "&nbsp;&nbsp;<input type=\"Button\" value=\"".getMLText("document")."...\" onclick=\"chooseDoc".$formName."();\">";
	} /* }}} */

	function printFolderChooser($formName, $accessMode, $exclude = -1, $default = false) { /* {{{ */
?>
		<script language="JavaScript">
		var openDlg;
		function chooseFolder<?php print $formName ?>() {
			openDlg = open("out.FolderChooser.php?form=<?php echo $formName?>&mode=<?php echo $accessMode?>&exclude=<?php echo $exclude?>", "openDlg", "width=480,height=480,scrollbars=yes,resizable=yes,status=yes");
		}
		</script>
<?php
		print "<input type=\"Hidden\" name=\"targetid".$formName."\" value=\"". (($default) ? $default->getID() : "") ."\">";
		print "<input disabled name=\"targetname".$formName."\" value=\"". (($default) ? htmlspecialchars($default->getName()) : "") ."\">";
		print "&nbsp;&nbsp;<input type=\"Button\" value=\"".getMLText("folder")."...\" onclick=\"chooseFolder".$formName."();\">";
	} /* }}} */

	function printCategoryChooser($formName, $categories=array()) { /* {{{ */
?>
		<script language="JavaScript">
		var openDlg;
		function chooseCategory<?php print $formName ?>() {
			var current = document.<?php echo $formName ?>.categoryid<?php echo $formName ?>;
			openDlg = open("out.CategoryChooser.php?form=<?php echo $formName?>&cats="+current.value, "openDlg", "width=480,height=480,scrollbars=yes,resizable=yes,status=yes");
		}
		function clearCategory<?php print $formName ?>() {
			document.<?php echo $formName ?>.categoryid<?php echo $formName ?>.value = '';
			document.<?php echo $formName ?>.categoryname<?php echo $formName ?>.value = '';
		}
		</script>
<?php
		$ids = $names = array();
		if($categories) {
			foreach($categories as $cat) {
				$ids[] = $cat->getId();
				$names[] = htmlspecialchars($cat->getName());
			}
		}
		print "<input type=\"hidden\" name=\"categoryid".$formName."\" value=\"".implode(',', $ids)."\">";
		print "<input disabled name=\"categoryname".$formName."\" value=\"".implode(' ', $names)."\">";
		print "&nbsp;&nbsp;<a href=\"javascript:clearCategory".$formName."();\"><img src=\"images/del.gif\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\" alt=\"\"></a>";
		print "&nbsp;&nbsp;<input type=\"Button\" value=\"".getMLText("category")."...\" onclick=\"chooseCategory".$formName."();\">";
	} /* }}} */

	function printDropFolderChooser($formName, $dropfolderfile="") { /* {{{ */
?>
		<script language="JavaScript">
		var openDlg;
		function chooseDropFolderFile<?php print $formName ?>() {
			var current = document.<?php echo $formName ?>.dropfolderfile<?php echo $formName ?>;
			openDlg = open("out.DropFolderChooser.php?form=<?php echo $formName?>&dropfolderfile="+current.value, "openDlg", "width=480,height=480,scrollbars=yes,resizable=yes,status=yes");
		}
		function clearFilename<?php print $formName ?>() {
			document.<?php echo $formName ?>.dropfolderfile<?php echo $formName ?>.value = '';
		}
		</script>
<?php
		print "<input readonly name=\"dropfolderfile".$formName."\" value=\"".$dropfolderfile."\">";
		print "&nbsp;&nbsp;<a href=\"javascript:clearFilename".$formName."();\"><img src=\"images/del.gif\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\" alt=\"\"></a>";
		print "&nbsp;&nbsp;<input type=\"Button\" value=\"".getMLText("choose_target_file")."...\" onclick=\"chooseDropFolderFile".$formName."();\">";
	} /* }}} */

	function printAttributeEditField($attrdef, $objvalue, $fieldname='attributes') { /* {{{ */
		if($valueset = $attrdef->getValueSetAsArray()) {
			echo "<select name=\"".$fieldname."[".$attrdef->getId()."]\">";
			if($attrdef->getMinValues() < 1) {
				echo "<option value=\"\"></option>";
			}
			foreach($valueset as $value) {
				echo "<option value=\"".htmlspecialchars($value)."\"";
				if($value == $objvalue)
					echo " selected";
				echo ">".htmlspecialchars($value)."</option>";
			}
			echo "</select>";
		} else {
			echo "<input type=\"text\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"".htmlspecialchars($objvalue)."\" />";
		}
	} /* }}} */

	function getImgPath($img) { /* {{{ */

		if ( is_file("../styles/$this->theme/images/$img") ) {
			return "../styles/$this->theme/images/$img";
		}
		else if ( is_file("../styles/$this->theme/img/$img") ) {
			return "../styles/$this->theme/img/$img";
		}
		return "../out/images/$img";
	} /* }}} */

	function printImgPath($img) { /* {{{ */
		print $this->getImgPath($img);
	} /* }}} */

	function exitError($pagetitle,$error) { /* {{{ */
	
		$this->htmlStartPage($pagetitle);
		$this->globalNavigation();

		print "<div class=\"error\">";
		print htmlspecialchars($error);
		print "</div>";
		
		$this->htmlEndPage();
		
		add_log_line(" UI:exitError error=".$error." pagetitle=".$pagetitle);
		
		exit;	
	} /* }}} */

	// navigation flag is used for items links (navigation or selection)
	function printFoldersTree($accessMode, $exclude, $folderID, $currentFolderID=-1, $navigation=false) {	/* {{{ */
		if ($this->params['expandfoldertree']==2){
		
			// folder completely open
			$is_open=true;
			
		}else if ($this->params['expandfoldertree']==1 && $folderID==$this->params['rootfolderid'] ){
		
			$is_open=true;
			
		}else{
			// open the tree until the current folder
			$is_open=false;
			
			if ($currentFolderID!=-1){
				
				$currentFolder=$this->params['dms']->getFolder($currentFolderID);
				
				if (is_object($currentFolder)){
				
					$parent=$currentFolder->getParent();
					
					while (is_object($parent)){
						if ($parent->getID()==$folderID){
							$is_open=true;
							break;
						}
						$parent=$parent->getParent();
					}
				}
			}
		}
		
		$folder = $this->params['dms']->getFolder($folderID);
		if (!is_object($folder)) return;
		
		$subFolders = $folder->getSubFolders();
		$subFolders = SeedDMS_Core_DMS::filterAccess($subFolders, $this->params['user'], M_READ);
		
		if ($folderID == $this->params['rootfolderid']) print "<ul style='list-style-type: none;' class='tree'>\n";

		print "<li>\n";

		if (count($subFolders) > 0){
			print "<a href=\"javascript:toggleTree(".$folderID.")\"><img class='treeicon' name=\"treedot".$folderID."\" src=\"";	
			if ($is_open) $this->printImgPath("minus.png");
			else $this->printImgPath("plus.png");
			print "\" border=0></a>\n";
		}
		else{
			print "<img class='treeicon' src=\"";	
			$this->printImgPath("blank.png");
			print "\" border=0>\n";
		}

		if ($folder->getAccessMode($this->params['user']) >= $accessMode) {

			if ($folderID != $currentFolderID){
			
				if ($navigation) print "<a href=\"../out/out.ViewFolder.php?folderid=" . $folderID . "&showtree=1\">";
				else print "<a class=\"foldertree_selectable\" href=\"javascript:folderSelected(" . $folderID . ", '" . str_replace("'", "\\'", htmlspecialchars($folder->getName())) . "')\">";

			}else print "<span class=\"selectedfoldertree\">";
			
			if ($is_open) print "<img src=\"".$this->getImgPath("folder_opened.gif")."\" border=0 name=\"treeimg".$folderID."\">".htmlspecialchars($folder->getName());
			else print "<img src=\"".$this->getImgPath("folder_closed.gif")."\" border=0 name=\"treeimg".$folderID."\">".htmlspecialchars($folder->getName());

			if ($folderID != $currentFolderID) print "</a>\n";
			else print "</span>";

		}
		else print "<img src=\"".$this->getImgPath("folder_closed.gif")."\" width=18 height=18 border=0>".htmlspecialchars($folder->getName())."\n";

		if ($is_open) print "<ul style='list-style-type: none;' id=\"tree".$folderID."\" >\n";
		else print "<ul style='list-style-type: none; display: none;' id=\"tree".$folderID."\" >\n";
		
		for ($i = 0; $i < count($subFolders); $i++) {
		
			if ($subFolders[$i]->getID() == $exclude) continue;
			
			$this->printFoldersTree( $accessMode, $exclude, $subFolders[$i]->getID(),$currentFolderID,$navigation);
		}

		print "</ul>\n";
		
		if ($folderID == $this->params['rootfolderid']) print "</ul>\n";
	} /* }}} */

	function printTreeNavigation($folderid,$showtree){ /* {{{ */
?>
		<script language="JavaScript">
		function toggleTree(id){
			
			obj = document.getElementById("tree" + id);
			
			if ( obj.style.display == "none" ){
				obj.style.display = "";
				document["treeimg" + id].src = "<?php $this->printImgPath("folder_opened.gif"); ?>";
				document["treedot" + id].src = "<?php $this->printImgPath("minus.png"); ?>";
			}else{
				obj.style.display = "none";
				document["treeimg" + id].src = "<?php $this->printImgPath("folder_closed.gif"); ?>";
				document["treedot" + id].src = "<?php $this->printImgPath("plus.png"); ?>";
			}

		}
		</script>
<?php
	
		print "<table width=\"100%\"><tr>";

		if ($showtree==1){

			print "<td id='tree-open'>";

			$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=0\"><img src=\"".UI::getImgPath("m.png")."\" border=0></a>", true);
			$this->contentContainerStart();
			$this->printFoldersTree(M_READ, -1, $this->params['rootfolderid'], $folderid, true);
			$this->contentContainerEnd();

		}else{
		
			print "<td id='tree-closed'>";		
		
			$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=1\"><img src=\"".UI::getImgPath("p.png")."\" border=0></a>", true);
			$this->contentContainerStart();
			$this->contentContainerEnd();
		}

		print "</td><td>";
	} /* }}} */

	/**
	 * Output HTML Code for jumploader
	 *
	 * @param string $uploadurl URL where post data is send
	 * @param integer $folderid id of folder where document is saved
	 * @param integer $maxfiles maximum number of files allowed to upload
	 * @param array $fields list of post fields
	 */
	function printUploadApplet($uploadurl, $attributes, $maxfiles=0, $fields=array()){ /* {{{ */
?>
<applet id="jumpLoaderApplet" name="jumpLoaderApplet"
code="jmaster.jumploader.app.JumpLoaderApplet.class"
archive="jl_core_z.jar"
width="715"
height="400"
mayscript>
  <param name="uc_uploadUrl" value="<?php echo $uploadurl ?>"/>
  <param name="ac_fireAppletInitialized" value="true"/>
  <param name="ac_fireUploaderSelectionChanged" value="true"/>
  <param name="ac_fireUploaderFileStatusChanged" value="true"/>
  <param name="ac_fireUploaderFileAdded" value="true"/>
  <param name="uc_partitionLength" value="<?php echo $this->params['partitionsize'] ?>"/>
<?php
	if($maxfiles) {
?>
  <param name="uc_maxFiles" value="<?php echo $maxfiles ?>"/>
<?php
	}
?>
</applet>
<div id="fileLinks">
</div>

<!-- callback methods -->
<script language="javascript">
    /**
     * applet initialized notification
     */
    function uploaderInitialized(  ) {
        var uploader = document.jumpLoaderApplet.getUploader();
        var attrSet = uploader.getAttributeSet();
        var attr;
<?php
	foreach($attributes as $name=>$value) {
?>
        attr = attrSet.createStringAttribute( '<?php echo $name ?>', '<?php echo $value ?>' );
        attr.setSendToServer(true);
<?php
	}
?>
    }
    /**
     * uploader selection changed notification
     */
    function uploaderSelectionChanged( uploader ) {
        dumpAllFileAttributes();
    }
    /**
     * uploader file added notification
     */
    function uploaderFileAdded( uploader ) {
        dumpAllFileAttributes();
    }
    /**
     * file status changed notification
     */
    function uploaderFileStatusChanged( uploader, file ) {
        traceEvent( "uploaderFileStatusChanged, index=" + file.getIndex() + ", status=" + file.getStatus() + ", content=" + file.getResponseContent() );
        if( file.isFinished() ) { 
            var serverFileName = file.getId() + "." + file.getName(); 
            var linkHtml = "<a href='/uploaded/" + serverFileName + "'>" + serverFileName + "</a> " + file.getLength() + " bytes"; 
            var container = document.getElementById( "fileLinks"); 
            container.innerHTML += linkHtml + "<br />"; 
        } 
    }
    /**
     * trace event to events textarea
     */
    function traceEvent( message ) {
        document.debugForm.txtEvents.value += message + "\r\n";
    }
</script>

<!-- debug auxiliary methods -->
<script language="javascript">
    /**
     * list attributes of file into html
     */
    function listFileAttributes( file, edit, index ) {
        var attrSet = file.getAttributeSet();
        var content = "";
        var attr;
				var value;
				if(edit)
					content += "<form name='form" + index + "' id='form" + index + "' action='#' >";
        content += "<table>";
				content += "<tr class='dataRow' colspan='2'><td class='dataText'><b>" + file.getName() + "</b></td></tr>";

<?php
	if(!$fields || (isset($fields['name']) && $fields['name'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('name') ?></td>";
				if(attr = attrSet.getAttributeByName('name'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<input id='name" + index + "' name='name' type='input' value='" + value + "' />";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['comment']) && $fields['comment'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('comment') ?></td>";
				if(attr = attrSet.getAttributeByName('comment'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<textarea id='comment" + index + "' name='comment' cols='40' rows='2'>" + value + "</textarea>";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['reqversion']) && $fields['reqversion'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('version') ?></td>";
				if(attr = attrSet.getAttributeByName('reqversion'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<input id='reqversion" + index + "' name='reqversion' type='input' value='" + value + "' />";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['version_comment']) && $fields['version_comment'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('comment_for_current_version') ?></td>";
				if(attr = attrSet.getAttributeByName('version_comment'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<textarea id='version_comment" + index + "' name='version_comment' cols='40' rows='2'>" + value + "</textarea>";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['keywords']) && $fields['keywords'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('keywords') ?></td>";
				if(attr = attrSet.getAttributeByName('keywords'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit) {
					value = "<textarea id='keywords" + index + "' name='keywords' cols='40' rows='2'>" + value + "</textarea>";
					value += "<br /><a href='javascript:chooseKeywords(\"form" + index + ".keywords" + index +"\");'><?php echo getMLText("use_default_keywords");?></a>";
				}
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['categories']) && $fields['categories'])) {
?>
				content += "<tr class='dataRow'>";
				content += "<td class='dataField'><?php echo getMLText('categories') ?></td>";
				if(attr = attrSet.getAttributeByName('categoryids'))
					value = attr.getStringValue();
				else
					value = '';
				if(attr = attrSet.getAttributeByName('categorynames'))
					value2 = attr.getStringValue();
				else
					value2 = '';
				if(edit) {
					value = "<input type='hidden' id='categoryidform" + index + "' name='categoryids' value='" + value + "' />";
					value += "<input disabled id='categorynameform" + index + "' name='categorynames' value='" + value2 + "' />";
					value += "<br /><a href='javascript:chooseCategory(\"form" + index + "\", \"\");'><?php echo getMLText("use_default_categories");?></a>";
				} else {
					value = value2;
				}
        content += "<td class='dataText'>" + value + "</td>";
				content += "</tr>";
<?php
	}
?>

				if(edit) {
					content += "<tr class='dataRow'>";
					content += "<td class='dataField'></td>";
					content += "<td class='dataText'><input type='button' value='Set' onClick='updateFileAttributes("+index+")'/></td>";
					content += "</tr>";
        	content += "</table>";
        	content += "</form>";
				} else {
        	content += "</table>";
				}
        return content;
    }
    /**
     * return selected file if and only if single file selected
     */
    function getSelectedFile() {
        var file = null;
        var uploader = document.jumpLoaderApplet.getUploader();
        var selection = uploader.getSelection();
        var numSelected = selection.getSelectedItemCount();
        if( numSelected == 1 ) {
            var selectedIndex = selection.getSelectedItemIndexAt( 0 );
            file = uploader.getFile( selectedIndex );
        }
        return file;
    }
    /**
     * dump attributes of all files into html
     */
     function dumpAllFileAttributes() {
         var content = "";
         var uploader = document.jumpLoaderApplet.getUploader();
         var files = uploader.getAllFiles();
         var file = getSelectedFile();
				 if(file) {
					 for (var i = 0; i < uploader.getFileCount() ; i++) { 
						 if(uploader.getFile(i).getIndex() == file.getIndex())
							 content += listFileAttributes( uploader.getFile(i), 1, i );
						 else
							 content += listFileAttributes( uploader.getFile(i), 0, i );
					 }
					 document.getElementById( "fileList" ).innerHTML = content;
				 }
    }
     /**
      * update attributes for the selected file
      */
      function updateFileAttributes(index) {
        var uploader = document.jumpLoaderApplet.getUploader();
        var file = uploader.getFile( index );
        if( file != null ) {
				  var attr;
					var value;
          var attrSet = file.getAttributeSet();
					value = document.getElementById("name"+index);
          attr = attrSet.createStringAttribute( 'name', (value.value) ? value.value : "" );
          attr.setSendToServer( true );
					value = document.getElementById("comment"+index);
          attr = attrSet.createStringAttribute( 'comment', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("reqversion"+index);
          attr = attrSet.createStringAttribute( 'reqversion', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("version_comment"+index);
          attr = attrSet.createStringAttribute( 'version_comment', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("keywords"+index);
          attr = attrSet.createStringAttribute( 'keywords', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					value = document.getElementById("categoryidform"+index);
          attr = attrSet.createStringAttribute( 'categoryids', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					value = document.getElementById("categorynameform"+index);
          attr = attrSet.createStringAttribute( 'categorynames', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					dumpAllFileAttributes();
        } else {
            alert( "Single file should be selected" );
        }
     }
</script>
<form name="debugForm">
<textarea name="txtEvents" style="visibility: hidden;width:715px; font:10px monospace" rows="1" wrap="off" id="txtEvents"></textarea></p>
</form>
<p></p>
<p id="fileList"></p>
<?php
	} /* }}} */
}
?>
