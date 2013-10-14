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


class SeedDMS_Bootstrap_Style extends SeedDMS_View_Common {
	var $imgpath;
	var $extraheader;

	function __construct($params, $theme='bootstrap') {
		$this->theme = $theme;
		$this->params = $params;
		$this->imgpath = '../views/'.$theme.'/images/';
		$this->extraheader = '';
	}

	function htmlStartPage($title="", $bodyClass="") { /* {{{ */
		echo "<!DOCTYPE html>\n";
		echo "<html lang=\"en\">\n<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">'."\n";
		echo '<link href="../styles/'.$this->theme.'/bootstrap/css/bootstrap.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/application.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/font-awesome/css/font-awesome.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/datepicker/css/datepicker.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/chosen/css/chosen.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/jqtree/jqtree.css" rel="stylesheet">'."\n";
		if($this->extraheader)
			echo $this->extraheader;
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/jquery/jquery.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="../js/jquery.passwordstrength.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/jquery.noty.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/layouts/topRight.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/themes/default.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/jqtree/tree.jquery.js"></script>'."\n";

		echo '<link rel="shortcut icon" href="../styles/'.$this->theme.'/favicon.ico" type="image/x-icon"/>'."\n";
		if($this->params['session'] && $this->params['session']->getSu()) {
?>
<style type="text/css">
.navbar-inverse .navbar-inner {
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#882222), to(#111111));
background-image: webkit-linear-gradient(top, #882222, #111111);
background-image: linear-gradient(to bottom, #882222, #111111);;
}
</style>
<?php
		}
		echo "<title>".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS").(strlen($title)>0 ? ": " : "").htmlspecialchars($title)."</title>\n";
		echo "</head>\n";
		echo "<body".(strlen($bodyClass)>0 ? " class=\"".$bodyClass."\"" : "").">\n";
		if($this->params['session'] && $flashmsg = $this->params['session']->getSplashMsg()) {
			$this->params['session']->clearSplashMsg();
?>
		<script>
  	noty({
  		text: '<?= $flashmsg['msg'] ?>',
  		type: '<?= $flashmsg['type'] ?>',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			timeout: 1500,
			_template: '<div class="noty_message alert alert-block alert-error"><span class="noty_text"></span><div class="noty_close"></div></div>'
  	});
		</script>
<?php
		}
	} /* }}} */

	function htmlAddHeader($head) { /* {{{ */
		$this->extraheader .= $head;
	} /* }}} */

	function htmlEndPage() { /* {{{ */
		$this->footNote();
		echo '<script src="../styles/'.$this->theme.'/bootstrap/js/bootstrap.min.js"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/datepicker/js/bootstrap-datepicker.js"></script>'."\n";
		foreach(array('de', 'es', 'ca', 'nl', 'fi', 'cs', 'it', 'fr', 'sv', 'sl', 'pt-BR', 'zh-CN', 'zh-TW') as $lang)
			echo '<script src="../styles/'.$this->theme.'/datepicker/js/locales/bootstrap-datepicker.'.$lang.'.js"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/chosen/js/chosen.jquery.min.js"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/application.js"></script>'."\n";
		echo "</body>\n</html>\n";
	} /* }}} */

	function footNote() { /* {{{ */
		echo '<div class="row-fluid" style="padding-top: 20px;">'."\n";
		echo '<div class="span12">'."\n";
		echo '<div class="alert alert-info">'."\n";
		if ($this->params['printdisclaimer']){
			echo "<div class=\"disclaimer\">".getMLText("disclaimer")."</div>";
		}

		if (isset($this->params['footnote']) && strlen((string)$this->params['footnote'])>0) {
			echo "<div class=\"footNote\">".(string)$this->params['footnote']."</div>";
		}
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	
		return;
	} /* }}} */

	function contentStart() { /* {{{ */
		echo "<div class=\"container-fluid\">\n";
		echo " <div class=\"row-fluid\">\n";
	} /* }}} */

	function contentEnd() { /* {{{ */
		echo " </div>\n";
		echo "</div>\n";
	} /* }}} */

	function globalBanner() { /* {{{ */
		echo "<div class=\"navbar navbar-inverse navbar-fixed-top\">\n";
		echo " <div class=\"navbar-inner\">\n";
		echo "  <div class=\"container\">\n";
		echo "   <a class=\"brand\" href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS")."</a>\n";
		echo "  </div>\n";
		echo " </div>\n";
		echo "</div>\n";
	} /* }}} */

	function menuClipboard($clipboard) { /* {{{ */
		$content = '';
		$content .= "   <ul id=\"main-menu-clipboard\" class=\"nav pull-right\">\n";
		$content .= "    <li class=\"dropdown\">\n";
		$content .= "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText('clipboard')." (".count($clipboard['folders'])."/".count($clipboard['docs']).") <i class=\"icon-caret-down\"></i></a>\n";
		$content .= "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		foreach($clipboard['folders'] as $folderid) {
			if($folder = $this->params['dms']->getFolder($folderid))
				$content .= "    <li><a href=\"../out/out.ViewFolder.php?id=".$folder->getID()."\"><i class=\"icon-folder-close-alt\"></i> ".htmlspecialchars($folder->getName())."</a></li>\n";
		}
		foreach($clipboard['docs'] as $docid) {
			if($document = $this->params['dms']->getDocument($docid))
				$content .= "    <li><a href=\"../out/out.ViewDocument.php?documentid=".$document->getID()."\"><i class=\"icon-file\"></i> ".htmlspecialchars($document->getName())."</a></li>\n";
		}
		$content .= "    <li class=\"divider\"></li>\n";
		if(isset($this->params['folder']) && $this->params['folder']->getAccessMode($this->params['user']) >= M_READWRITE) {
			$content .= "    <li><a href=\"../op/op.MoveClipboard.php?targetid=".$this->params['folder']->getID()."&refferer=".urlencode($this->params['refferer'])."\">".getMLText("move_clipboard")."</a></li>\n";
		}
		$content .= "    <li><a href=\"../op/op.ClearClipboard.php?refferer=".urlencode($this->params['refferer'])."\">".getMLText("clear_clipboard")."</a></li>\n";
		$content .= "     </ul>\n";
		$content .= "    </li>\n";
		$content .= "   </ul>\n";
		return $content;
	} /* }}} */

	function globalNavigation($folder=null) { /* {{{ */
		echo "<div class=\"navbar navbar-inverse navbar-fixed-top\">\n";
		echo " <div class=\"navbar-inner\">\n";
		echo "  <div class=\"container\">\n";
		echo "   <a class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".nav-col1\">\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "   </a>\n";
		echo "   <a class=\"brand\" href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS")."</a>\n";
		if(isset($this->params['user']) && $this->params['user']) {
			echo "   <div class=\"nav-collapse nav-col1\">\n";
			echo "   <ul id=\"main-menu-admin\"class=\"nav pull-right\">\n";
			echo "    <li class=\"dropdown\">\n";
			echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".($this->params['session']->getSu() ? getMLText("switched_to") : getMLText("signed_in_as"))." '".htmlspecialchars($this->params['user']->getFullName())."' <i class=\"icon-caret-down\"></i></a>\n";
			echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
			if (!$this->params['user']->isGuest()) {
				echo "    <li><a href=\"../out/out.MyDocuments.php?inProcess=1\">".getMLText("my_documents")."</a></li>\n";
				echo "    <li><a href=\"../out/out.MyAccount.php\">".getMLText("my_account")."</a></li>\n";
				echo "    <li class=\"divider\"></li>\n";
			}
			if($this->params['enablelanguageselector']) {
				echo "    <li class=\"dropdown-submenu\">\n";
				echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("language")."</a>\n";
				echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
				$languages = getLanguages();
				foreach ($languages as $currLang) {
					if($this->params['session']->getLanguage() == $currLang)
						echo "<li class=\"active\">";
					else
						echo "<li>";
					echo "<a href=\"../op/op.SetLanguage.php?lang=".$currLang."&referer=".$_SERVER["REQUEST_URI"]."\">";
					echo getMLText($currLang)."</a></li>\n";
				}
				echo "     </ul>\n";
				echo "    </li>\n";
				if($this->params['user']->isAdmin())
					echo "    <li><a href=\"../out/out.SubstituteUser.php\">".getMLText("substitute_user")."</a></li>\n";
				echo "    <li class=\"divider\"></li>\n";
			}
			if($this->params['session']->getSu()) {
				echo "    <li><a href=\"../op/op.ResetSu.php\">".getMLText("sign_out_user")."</a></li>\n";
			} else {
				echo "    <li><a href=\"../op/op.Logout.php\">".getMLText("sign_out")."</a></li>\n";
			}
			echo "     </ul>\n";
			echo "    </li>\n";
			echo "   </ul>\n";

			echo "   <div id=\"menu-clipboard\">";
			$clipboard = $this->params['session']->getClipboard();
			if (!$this->params['user']->isGuest() && (count($clipboard['docs']) + count($clipboard['folders'])) > 0) {
				echo $this->menuClipboard($clipboard);
			}
			echo "   </div>";


			echo "   <ul class=\"nav\">\n";
	//		echo "    <li id=\"first\"><a href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".getMLText("content")."</a></li>\n";
	//		echo "    <li><a href=\"../out/out.SearchForm.php?folderid=".$this->params['rootfolderid']."\">".getMLText("search")."</a></li>\n";
			if ($this->params['enablecalendar']) echo "    <li><a href=\"../out/out.Calendar.php?mode=".$this->params['calendardefaultview']."\">".getMLText("calendar")."</a></li>\n";
			if ($this->params['user']->isAdmin()) echo "    <li><a href=\"../out/out.AdminTools.php\">".getMLText("admin_tools")."</a></li>\n";
			echo "    <li><a href=\"../out/out.Help.php\">".getMLText("help")."</a></li>\n";
			echo "   </ul>\n";
			echo "     <form action=\"../op/op.Search.php\" class=\"form-inline navbar-search pull-left\" autocomplete=\"off\">";
			if ($folder!=null && is_object($folder) && !strcasecmp(get_class($folder), "SeedDMS_Core_Folder")) {
				echo "      <input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\" />";
			}
			echo "      <input type=\"hidden\" name=\"navBar\" value=\"1\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"1\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"2\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"3\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"4\" />";
			echo "      <input name=\"query\" class=\"search-query\" id=\"searchfield\" data-provide=\"typeahead\" type=\"text\" style=\"width: 150px;\" placeholder=\"".getMLText("search")."\"/>";
			if($this->params['enablefullsearch']) {
				echo "      <label class=\"checkbox\" style=\"color: #999999;\"><input type=\"checkbox\" name=\"fullsearch\" value=\"1\" title=\"".getMLText('fullsearch_hint')."\"/> ".getMLText('fullsearch')."</label>";
			}
	//		echo "      <input type=\"submit\" value=\"".getMLText("search")."\" id=\"searchButton\" class=\"btn\"/>";
			echo "</form>\n";
			echo "    </div>\n";
		}
		echo "  </div>\n";
		echo " </div>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	function getFolderPathHTML($folder, $tagAll=false, $document=null) { /* {{{ */
		$path = $folder->getPath();
		$txtpath = "";
		for ($i = 0; $i < count($path); $i++) {
			$txtpath .= "<li>";
			if ($i +1 < count($path)) {
				$txtpath .= "<a href=\"../out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\" rel=\"folder_".$path[$i]->getID()."\" ondragover=\"allowDrop(event)\" ondrop=\"onDrop(event)\">".
					htmlspecialchars($path[$i]->getName())."</a>";
			}
			else {
				$txtpath .= ($tagAll ? "<a href=\"../out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\">".
										 htmlspecialchars($path[$i]->getName())."</a>" : htmlspecialchars($path[$i]->getName()));
			}
			$txtpath .= " <span class=\"divider\">/</span></li>";
		}
		if($document)
			$txtpath .= "<li><a href=\"../out/out.ViewDocument.php?documentid=".$document->getId()."\">".htmlspecialchars($document->getName())."</a></li>";

		return '<ul class="breadcrumb">'.$txtpath.'</ul>';
	} /* }}} */
	
	function pageNavigation($pageTitle, $pageType=null, $extra=null) { /* {{{ */

		if ($pageType!=null && strcasecmp($pageType, "noNav")) {
			if($pageType == "view_folder" || $pageType == "view_document")
				echo $pageTitle."\n";
			echo "<div class=\"navbar\">\n";
			echo " <div class=\"navbar-inner\">\n";
			echo "  <div class=\"container\">\n";
			echo "   <a class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".col2\">\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo " 		<span class=\"icon-bar\"></span></a>\n";
			switch ($pageType) {
				case "view_folder":
					$this->folderNavigationBar($extra);
					break;
				case "view_document":
					$this->documentNavigationBar($extra);
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
			echo " 	</div>\n";
			echo " </div>\n";
			echo "</div>\n";
		} else {
			echo "<legend>".$pageTitle."</legend>\n";
		}

		return;
	} /* }}} */

	private function folderNavigationBar($folder) { /* {{{ */
		if (!is_object($folder) || strcasecmp(get_class($folder), "SeedDMS_Core_Folder")) {
			echo "<ul class=\"nav\">\n";
			echo "</ul>\n";
			return;
		}
		$accessMode = $folder->getAccessMode($this->params['user']);
		$folderID = $folder->getID();
		echo "<id=\"first\"><a href=\"../out/out.ViewFolder.php?folderid=". $folderID ."&showtree=".showtree()."\" class=\"brand\">".getMLText("folder")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		if ($accessMode == M_READ && !$this->params['user']->isGuest()) {
			echo "<li id=\"first\"><a href=\"../out/out.FolderNotify.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_folder_notify")."</a></li>\n";
		}
		else if ($accessMode >= M_READWRITE) {
			echo "<li id=\"first\"><a href=\"../out/out.AddSubFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("add_subfolder")."</a></li>\n";
			echo "<li><a href=\"../out/out.AddDocument.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("add_document")."</a></li>\n";
			if($this->params['enablelargefileupload'])
				echo "<li><a href=\"../out/out.AddMultiDocument.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("add_multiple_documents")."</a></li>\n";
			echo "<li><a href=\"../out/out.EditFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_folder_props")."</a></li>\n";
			if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
				echo "<li><a href=\"../out/out.MoveFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("move_folder")."</a></li>\n";

			if ($accessMode == M_ALL) {
				if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
					echo "<li><a href=\"../out/out.RemoveFolder.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("rm_folder")."</a></li>\n";
			}
			if ($accessMode == M_ALL) {
				echo "<li><a href=\"../out/out.FolderAccess.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_folder_access")."</a></li>\n";
			}
			echo "<li><a href=\"../out/out.FolderNotify.php?folderid=". $folderID ."&showtree=".showtree()."\">".getMLText("edit_existing_notify")."</a></li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function documentNavigationBar($document)	{ /* {{{ */
		$accessMode = $document->getAccessMode($this->params['user']);
		$docid=".php?documentid=" . $document->getID();
		echo "<id=\"first\"><a href=\"../out/out.ViewDocument". $docid ."\" class=\"brand\">".getMLText("document")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

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
		echo "</div>\n";
		return;
	} /* }}} */

	private function accountNavigationBar() { /* {{{ */
		echo "<id=\"first\"><a href=\"../out/out.MyAccount.php\" class=\"brand\">".getMLText("my_account")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		if (!$this->params['disableselfedit']) echo "<li id=\"first\"><a href=\"../out/out.EditUserData.php\">".getMLText("edit_user_details")."</a></li>\n";
		
		if (!$this->params['user']->isAdmin()) 
			echo "<li><a href=\"../out/out.UserDefaultKeywords.php\">".getMLText("edit_default_keywords")."</a></li>\n";

		echo "<li><a href=\"../out/out.ManageNotify.php\">".getMLText("edit_existing_notify")."</a></li>\n";

		if ($this->params['enableusersview']){
			echo "<li><a href=\"../out/out.UsrView.php\">".getMLText("users")."</a></li>\n";
			echo "<li><a href=\"../out/out.GroupView.php\">".getMLText("groups")."</a></li>\n";
		}		
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function myDocumentsNavigationBar() { /* {{{ */

		echo "<id=\"first\"><a href=\"../out/out.MyDocuments.php?inProcess=1\" class=\"brand\">".getMLText("my_documents")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		echo "<li><a href=\"../out/out.MyDocuments.php?inProcess=1\">".getMLText("documents_in_process")."</a></li>\n";
		echo "<li><a href=\"../out/out.MyDocuments.php\">".getMLText("all_documents")."</a></li>\n";
		if($this->params['workflowmode'] == 'traditional') {
			echo "<li><a href=\"../out/out.ReviewSummary.php\">".getMLText("review_summary")."</a></li>\n";
			echo "<li><a href=\"../out/out.ApprovalSummary.php\">".getMLText("approval_summary")."</a></li>\n";
		} else {
			echo "<li><a href=\"../out/out.WorkflowSummary.php\">".getMLText("workflow_summary")."</a></li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function adminToolsNavigationBar() { /* {{{ */
		echo "    <id=\"first\"><a href=\"../out/out.AdminTools.php\" class=\"brand\">".getMLText("admin_tools")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "   <ul class=\"nav\">\n";

		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("user_group_management")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.UsrMgr.php\">".getMLText("user_management")."</a></li>\n";
		echo "      <li><a href=\"../out/out.GroupMgr.php\">".getMLText("group_management")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("definitions")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.DefaultKeywords.php\">".getMLText("global_default_keywords")."</a></li>\n";
		echo "     <li><a href=\"../out/out.Categories.php\">".getMLText("global_document_categories")."</a></li>\n";
		echo "     <li><a href=\"../out/out.AttributeMgr.php\">".getMLText("global_attributedefinitions")."</a></li>\n";
		if($this->params['workflowmode'] != 'traditional') {
			echo "     <li><a href=\"../out/out.WorkflowMgr.php\">".getMLText("global_workflows")."</a></li>\n";
			echo "     <li><a href=\"../out/out.WorkflowStatesMgr.php\">".getMLText("global_workflow_states")."</a></li>\n";
			echo "     <li><a href=\"../out/out.WorkflowActionsMgr.php\">".getMLText("global_workflow_actions")."</a></li>\n";
		}
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		if($this->params['enablefullsearch']) {
			echo "   <ul class=\"nav\">\n";
			echo "    <li class=\"dropdown\">\n";
			echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("fullsearch")." <i class=\"icon-caret-down\"></i></a>\n";
			echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
			echo "      <li><a href=\"../out/out.Indexer.php\">".getMLText("update_fulltext_index")."</a></li>\n";
			echo "      <li><a href=\"../out/out.CreateIndex.php\">".getMLText("create_fulltext_index")."</a></li>\n";
			echo "      <li><a href=\"../out/out.IndexInfo.php\">".getMLText("fulltext_info")."</a></li>\n";
			echo "     </ul>\n";
			echo "    </li>\n";
			echo "   </ul>\n";
		}

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("backup_log_management")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.BackupTools.php\">".getMLText("backup_tools")."</a></li>\n";
		if ($this->params['logfileenable'])
			echo "      <li><a href=\"../out/out.LogManagement.php\">".getMLText("log_management")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("misc")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li id=\"first\"><a href=\"../out/out.Statistic.php\">".getMLText("folders_and_documents_statistic")."</a></li>\n";
		echo "      <li><a href=\"../out/out.ObjectCheck.php\">".getMLText("objectcheck")."</a></li>\n";
		echo "      <li><a href=\"../out/out.Info.php\">".getMLText("version_info")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "<ul class=\"nav\">\n";
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */
	
	private function calendarNavigationBar($d){ /* {{{ */
		$ds="&day=".$d[0]."&month=".$d[1]."&year=".$d[2];
		echo "<id=\"first\"><a href=\"../out/out.Calendar.php?mode=y\" class=\"brand\">".getMLText("calendar")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		echo "<li><a href=\"../out/out.Calendar.php?mode=w".$ds."\">".getMLText("week_view")."</a></li>\n";
		echo "<li><a href=\"../out/out.Calendar.php?mode=m".$ds."\">".getMLText("month_view")."</a></li>\n";
		echo "<li><a href=\"../out/out.Calendar.php?mode=y".$ds."\">".getMLText("year_view")."</a></li>\n";
		if (!$this->params['user']->isGuest()) echo "<li><a href=\"../out/out.AddEvent.php\">".getMLText("add_event")."</a></li>\n";
		echo "</ul>\n";
		echo "</div>\n";
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
				foreach ($value as $subkey=>$subvalue) {
					$resultsURI .= ($first ? "?" : "&").$key."%5B".$subkey."%5D=".$subvalue;
					$first = false;
				}
			}
			else {
					$resultsURI .= ($first ? "?" : "&").$key."=".$value;
			}
			$first = false;
		}

		echo "<div class=\"pagination pagination-small\">";
		echo "<ul>";
		for ($i = 1; $i  <= $totalPages; $i++) {
			if ($i == $pageNumber)  echo "<li class=\"active\"><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$i."\">".$i."</a></li> ";
			else echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$i."\">".$i."</a></li>";
		}
		if ($totalPages>1) {
			echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=all\">".getMLText("all_pages")."</a></li>";
		}
		echo "</ul>";
		echo "</div>";

		return;
	} /* }}} */

	function contentContainer($content) { /* {{{ */
		echo "<div class=\"well\">\n";
		echo $content;
		echo "</div>\n";
		return;
	} /* }}} */

	function contentContainerStart($type='info') { /* {{{ */

		//echo "<div class=\"alert alert-".$type."\">\n";
		echo "<div class=\"well\">\n";
		return;
	} /* }}} */

	function contentContainerEnd() { /* {{{ */

		echo "</div>\n";
		return;
	} /* }}} */

	function contentHeading($heading, $noescape=false) { /* {{{ */

		if($noescape)
			echo "<legend>".$heading."</legend>\n";
		else
			echo "<legend>".htmlspecialchars($heading)."</legend>\n";
		return;
	} /* }}} */

	function contentSubHeading($heading, $first=false) { /* {{{ */

//		echo "<div class=\"contentSubHeading\"".($first ? " id=\"first\"" : "").">".htmlspecialchars($heading)."</div>\n";
		echo "<h5>".$heading."</h5>";
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
			return $this->imgpath.$icons[$ext];
		}
		else {
			return $this->imgpath.$icons["default"];
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
		print "<input type=\"hidden\" id=\"docid".$formName."\" name=\"docid\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" id=\"choosedocsearch\" data-provide=\"typeahead\" name=\"docname".$formName."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" />";
		print "<a data-target=\"#docChooser".$formName."\" href=\"out.DocumentChooser.php?form=".$formName."&folderid=".$this->params['rootfolderid']."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("document")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="docChooser<?= $formName ?>" tabindex="-1" role="dialog" aria-labelledby="docChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="docChooserLabel"><?php printMLText("choose_target_document") ?></h3>
  </div>
  <div class="modal-body">
    <p>Please wait, until document tree is loaded …</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
  </div>
</div>
		<script language="JavaScript">
modalDocChooser<?= $formName ?> = $('#docChooser<?= $formName ?>');
function documentSelected<?= $formName ?>(id, name) {
	$('#docid<?= $formName ?>').val(id);
	$('#choosedocsearch').val(name);
	modalDocChooser<?= $formName ?>.modal('hide');
}
		</script>
<?php
	} /* }}} */

	function printFolderChooser($formName, $accessMode, $exclude = -1, $default = false) { /* {{{ */
		print "<input type=\"hidden\" id=\"targetid".$formName."\" name=\"targetid".$formName."\" value=\"". (($default) ? $default->getID() : "") ."\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" id=\"choosefoldersearch".$formName."\" data-provide=\"typeahead\"  name=\"targetname".$formName."\" value=\"". (($default) ? htmlspecialchars($default->getName()) : "") ."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" />";
		print "<a data-target=\"#folderChooser".$formName."\" href=\"../out/out.FolderChooser.php?form=".$formName."&mode=".$accessMode."&exclude=".$exclude."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("folder")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="folderChooser<?= $formName ?>" tabindex="-1" role="dialog" aria-labelledby="folderChooser<?= $formName ?>Label" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="folderChooser<?= $formName ?>Label"><?php printMLText("choose_target_folder") ?></h3>
  </div>
  <div class="modal-body">
    <p>Please wait, until document tree is loaded …</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
  </div>
</div>
<script language="JavaScript">
/* Set up a callback which is called when a folder in the tree is selected */
modalFolderChooser<?= $formName ?> = $('#folderChooser<?= $formName ?>');
function folderSelected<?= $formName ?>(id, name) {
	$('#targetid<?= $formName ?>').val(id);
	$('#choosefoldersearch<?= $formName ?>').val(name);
	modalFolderChooser<?= $formName ?>.modal('hide');
}
</script>
<?php
	} /* }}} */

	function printCategoryChooser($formName, $categories=array()) { /* {{{ */
?>
<script language="JavaScript">
	function clearCategory<?php print $formName ?>() {
		document.<?php echo $formName ?>.categoryid<?php echo $formName ?>.value = '';
		document.<?php echo $formName ?>.categoryname<?php echo $formName ?>.value = '';
	}

	function acceptCategories() {
		var targetName = document.<?php echo $formName?>.categoryname<?php print $formName ?>;
		var targetID = document.<?php echo $formName?>.categoryid<?php print $formName ?>;
		var value = '';
		$('#keywordta option:selected').each(function(){
			value += ' ' + $(this).text();
		});
		targetName.value = value;
		targetID.value = $('#keywordta').val();
		return true;
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
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" disabled name=\"categoryname".$formName."\" value=\"".implode(' ', $names)."\">";
		print "<button type=\"button\" class=\"btn\" onclick=\"javascript:clearCategory".$formName."();\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#categoryChooser\" href=\"out.CategoryChooser.php?form=form1&cats=".implode(',', $ids)."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("category")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="categoryChooser" tabindex="-1" role="dialog" aria-labelledby="categoryChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="categoryChooserLabel"><?php printMLText("choose_target_category") ?></h3>
  </div>
  <div class="modal-body">
    <p>Please wait, until category list is loaded …</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
    <button class="btn" data-dismiss="modal" aria-hidden="true" onClick="acceptCategories();"><i class="icon-save"></i> <?php printMLText("save") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printKeywordChooser($formName, $keywords='', $fieldname='keywords') { /* {{{ */
?>
		    <div class="input-append">
				<input type="text" name="<?php echo $fieldname; ?>" value="<?php print htmlspecialchars($keywords);?>" />
				<a data-target="#keywordChooser" role="button" class="btn" data-toggle="modal" href="out.KeywordChooser.php?target=<?php echo $formName; ?>"><?php printMLText("keywords");?>…</a>
		    </div>
<div class="modal hide" id="keywordChooser" tabindex="-1" role="dialog" aria-labelledby="keywordChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="keywordChooserLabel"><?php printMLText("use_default_keywords") ?></h3>
  </div>
  <div class="modal-body">
    <p>Please wait, until keyword list is loaded …</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
    <button class="btn" data-dismiss="modal" aria-hidden="true" onClick="acceptKeywords();"><i class="icon-save"></i> <?php printMLText("save") ?></button>
  </div>
</div>
<?php
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

	function printDropFolderChooser($formName, $dropfolderfile="") { /* {{{ */
		print "<div class=\"input-append\">\n";
		print "<input readonly type=\"text\" id=\"dropfolderfile".$formName."\" name=\"dropfolderfile".$formName."\" value=\"".$dropfolderfile."\">";
		print "<button type=\"button\" class=\"btn\" onclick=\"javascript:clearFilename".$formName."();\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#dropfolderChooser\" href=\"out.DropFolderChooser.php?form=form1&dropfolderfile=".$dropfolderfile."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("choose_target_file")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="dropfolderChooser" tabindex="-1" role="dialog" aria-labelledby="dropfolderChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="dropfolderChooserLabel"><?php printMLText("choose_target_file") ?></h3>
  </div>
  <div class="modal-body">
    <p>Please wait, until file list is loaded …</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
<!--    <button class="btn" data-dismiss="modal" aria-hidden="true" onClick="acceptCategories();"><i class="icon-save"></i> <?php printMLText("save") ?></button> -->
  </div>
</div>
<script language="JavaScript">
/* Set up a callback which is called when a folder in the tree is selected */
modalDropfolderChooser = $('#dropfolderChooser');
function fileSelected(name) {
	$('#dropfolderfile<?= $formName ?>').val(name);
	modalDropfolderChooser.modal('hide');
}
function clearFilename<?php print $formName ?>() {
	$('#dropfolderfile<?= $formName ?>').val('');
}
</script>
<?php
	} /* }}} */

	function getImgPath($img) { /* {{{ */

		if ( is_file($this->imgpath.$img) ) {
			return $this->imgpath.$img;
		}
		return "../out/images/$img";
	} /* }}} */

	function printImgPath($img) { /* {{{ */
		print $this->getImgPath($img);
	} /* }}} */

	function infoMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-info\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function warningMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-warning\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function errorMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-error\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function exitError($pagetitle,$error) { /* {{{ */
	
		$this->htmlStartPage($pagetitle);
		$this->globalNavigation();
		$this->contentStart();

		print "<div class=\"alert alert-error\">";
		print "<h4>".getMLText('error')."!</h4>";
		print htmlspecialchars($error);
		print "</div>";
		print "<div><button class=\"btn\" onclick=\"window.history.back();\">".getMLText('back')."</button></div>";
		
		$this->htmlEndPage();
		
		add_log_line(" UI::exitError error=".$error." pagetitle=".$pagetitle);
		
		exit;	
	} /* }}} */

	/**
	 * Create a tree of folders using jqtree.
	 *
	 * The tree can contain folders only or include documents.
	 *
	 * @params integer $folderid current folderid. If set the tree will be
	 *   folded out and the all folders in the path will be visible
	 * @params integer $accessmode use this access mode when retrieving folders
	 *   and documents shown in the tree
	 * @params boolean $showdocs set to true if tree shall contain documents
	 *   as well.
	 */
	function printNewTreeNavigation($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0) { /* {{{ */
		function jqtree($path, $folder, $user, $accessmode, $showdocs=1, $expandtree=0) {
			if($path || $expandtree) {
				if($path)
					$pathfolder = array_shift($path);
				$subfolders = $folder->getSubFolders();
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>$subfolder->hasSubFolders() ? true : false, 'is_folder'=>true);
					if($expandtree || $pathfolder->getID() == $subfolder->getID()) {
						if($showdocs) {
							$documents = $folder->getDocuments();
							$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, $accessmode);
							foreach($documents as $document) {
								$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'load_on_demand'=>false, 'is_folder'=>false);
								$children[] = $node2;
							}
						}
						$node['children'] = jqtree($path, $subfolder, $user, $accessmode, $showdocs, $expandtree);
					}
					$children[] = $node;
				}
				return $children;
			} else {
				$subfolders = $folder->getSubFolders();
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>$subfolder->hasSubFolders() ? true : false, 'is_folder'=>true);
					$children[] = $node;
				}
				return $children;
			}
			return array();
		}

		if($folderid) {
			$folder = $this->params['dms']->getFolder($folderid);
			$path = $folder->getPath();
			$folder = array_shift($path);
			$node = array('label'=>$folder->getName(), 'id'=>$folder->getID(), 'load_on_demand'=>true, 'is_folder'=>true);
			if(!$folder->hasSubFolders()) {
				$node['load_on_demand'] = false;
				$node['children'] = array();
			} else {
				$node['children'] = jqtree($path, $folder, $this->params['user'], $accessmode, $showdocs, $expandtree);
			}
			$tree[] = $node;
			
		} else {
			$root = $this->params['dms']->getFolder($this->params['rootfolderid']);
			$tree = array(array('label'=>$root->getName(), 'id'=>$root->getID(), 'load_on_demand'=>true, 'is_folder'=>true));
		}

		echo "<div id=\"jqtree".$formid."\" style=\"margin-left: 10px;\" data-url=\"../op/op.Ajax.php?command=subtree&showdocs=".$showdocs."\"></div>\n";
?>
	<script language="JavaScript">
var data = <?php echo json_encode($tree); ?>;
$(function() {
  $('#jqtree<?= $formid ?>').tree({
		data: data,
		openedIcon: '<i class="icon-minus-sign"></i>',
		closedIcon: '<i class="icon-plus-sign"></i>',
		onCanSelectNode: function(node) {
			if(node.is_folder)
				folderSelected<?= $formid ?>(node.id, node.name);
			else
				documentSelected<?= $formid ?>(node.id, node.name);
		},
		autoOpen: true,
		drapAndDrop: true,
    onCreateLi: function(node, $li) {
        // Add 'icon' span before title
				if(node.is_folder)
					$li.find('.jqtree-title').before('<i class="icon-folder-close-alt" rel="folder_' + node.id + '" ondragover="allowDrop(event)" ondrop="onDrop(event)"></i> ').attr('rel', 'folder_' + node.id).attr('ondragover', 'allowDrop(event)').attr('ondrop', 'onDrop(event)');
				else
					$li.find('.jqtree-title').before('<i class="icon-file"></i> ');
    }
  });
});
	</script>
<?php
	} /* }}} */

	function printTreeNavigation($folderid, $showtree){ /* {{{ */
		if ($showtree==1){
			$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=0\"><i class=\"icon-minus-sign\"></i></a>", true);
			$this->contentContainerStart();
?>
	<script language="JavaScript">
	function folderSelected(id, name) {
		window.location = '../out/out.ViewFolder.php?folderid=' + id;
	}
	</script>
<?php
			$this->printNewTreeNavigation($folderid, M_READ, 0, '');
			$this->contentContainerEnd();
		} else {
			$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=1\"><i class=\"icon-plus-sign\"></i></a>", true);
		}
	} /* }}} */

	function printClipboard($clipboard){ /* {{{ */
		$dms = $this->params['dms'];
		$this->contentHeading(getMLText("clipboard"), true);
		echo "<div class=\"well\" ondragover=\"allowDrop(event)\" ondrop=\"onAddClipboard(event)\">\n";
		$clipboard = $this->params['session']->getClipboard();
//		print_r($clipboard);
		if(!$clipboard['docs'] && !$clipboard['folders']) {
			print "<div class=\"alert\">".getMLText("drag_icon_here")."</div>";
		} else {
			print "<table class=\"table\">";
			if($clipboard['folders']) {
				//echo "<tr><th colspan=\"3\">Folders</th></tr>\n";
				foreach($clipboard['folders'] as $folderid) {
					if($folder = $dms->getFolder($folderid)) {
						$comment = $folder->getComment();
						if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
						print "<tr rel=\"folder_".$folder->getID()."\" class=\"folder\" ondragover=\"allowDrop(event)\" ondrop=\"onDrop(event)\">";
					//	print "<td><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></td>";
						print "<td><a rel=\"folder_".$folder->getID()."\" draggable=\"true\" ondragstart=\"onDragStartFolder(event);\" href=\"out.ViewFolder.php?folderid=".$folder->getID()."&showtree=".showtree()."\"><img draggable=\"false\" src=\"".$this->imgpath."folder.png\" width=\"24\" height=\"24\" border=0></a></td>\n";
						print "<td><a href=\"out.ViewFolder.php?folderid=".$folder->getID()."&showtree=".showtree()."\">" . htmlspecialchars($folder->getName()) . "</a>";
						if($comment) {
							print "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
						}
						print "</td>\n";
						print "<td>\n";
						print "<div class=\"list-action\"><a href=\"../op/op.RemoveFromClipboard.php?folderid=".$this->params['folder']->getID()."&id=".$folderid."&type=folder\" title=\"".getMLText('rm_from_clipboard')."\"><i class=\"icon-remove\"></i></a></div>";
						print "</td>\n";
						print "</tr>\n";
					}
				}
			}
			$previewer = new SeedDMS_Preview_Previewer($this->params['cachedir'], 40);
			if($clipboard['docs']) {
				//echo "<tr><th colspan=\"3\">Documents</th></tr>\n";
				foreach($clipboard['docs'] as $docid) {
					if($document = $dms->getDocument($docid)) {
						$comment = $document->getComment();
						if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
						if($latestContent = $document->getLatestContent()) {
							$previewer->createPreview($latestContent);
							$version = $latestContent->getVersion();
							$status = $latestContent->getStatus();
							
							print "<tr>";

							if (file_exists($dms->contentDir . $latestContent->getPath())) {
								print "<td><a rel=\"document_".$docid."\" draggable=\"true\" ondragstart=\"onDragStartDocument(event);\" href=\"../op/op.Download.php?documentid=".$docid."&version=".$version."\">";
								if($previewer->hasPreview($latestContent)) {
									print "<img class=\"mimeicon\" width=\"40\"src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=40\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
								} else {
									print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
								}
								print "</a></td>";
							} else
								print "<td><img class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\"></td>";
							
							print "<td><a href=\"out.ViewDocument.php?documentid=".$docid."&showtree=".showtree()."\">" . htmlspecialchars($document->getName()) . "</a>";
							if($comment) {
								print "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
							}
							print "</td>\n";
							print "<td>\n";
							print "<div class=\"list-action\"><a href=\"../op/op.RemoveFromClipboard.php?folderid=".$this->params['folder']->getID()."&id=".$docid."&type=document\" title=\"".getMLText('rm_from_clipboard')."\"><i class=\"icon-remove\"></i></a></div>";
							print "</td>\n";
							print "</tr>";
						}
					}
				}
			}
			print "</table>";
		}
		echo "</div>\n";
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
  <param name="uc_uploadUrl" value="<?php echo $uploadurl; ?>"/>
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
    function appletInitialized(  ) {
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
