<?php
/**
 * Implementation of Search result view
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
 * Class which outputs the html page for Search result view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Search extends SeedDMS_Bootstrap_Style {

	/**
	 * Mark search query sting in a given string
	 *
	 * @param string $str mark this text
	 * @param string $tag wrap the marked text with this html tag
	 * @return string marked text
	 */
	function markQuery($str, $tag = "b") { /* {{{ */
		$querywords = preg_split("/ /", $this->query);
		
		foreach ($querywords as $queryword)
			$str = str_ireplace("($queryword)", "<" . $tag . ">\\1</" . $tag . ">", $str);
		
		return $str;
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$fullsearch = $this->params['fullsearch'];
		$totaldocs = $this->params['totaldocs'];
		$totalfolders = $this->params['totalfolders'];
		$attrdefs = $this->params['attrdefs'];
		$allCats = $this->params['allcategories'];
		$allUsers = $this->params['allusers'];
		$mode = $this->params['mode'];
		$workflowmode = $this->params['workflowmode'];
		$enablefullsearch = $this->params['enablefullsearch'];
		$attributes = $this->params['attributes'];
		$categories = $this->params['categories'];
		$owner = $this->params['owner'];
		$startfolder = $this->params['startfolder'];
		$startdate = $this->params['startdate'];
		$stopdate = $this->params['stopdate'];
		$expstartdate = $this->params['expstartdate'];
		$expstopdate = $this->params['expstopdate'];
		$creationdate = $this->params['creationdate'];
		$expirationdate = $this->params['expirationdate'];
		$status = $this->params['status'];
		$this->query = $this->params['query'];
		$entries = $this->params['searchhits'];
		$totalpages = $this->params['totalpages'];
		$pageNumber = $this->params['pagenumber'];
		$searchTime = $this->params['searchtime'];
		$urlparams = $this->params['urlparams'];
		$searchin = $this->params['searchin'];
		$cachedir = $this->params['cachedir'];

		$this->htmlStartPage(getMLText("search_results"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("search_results"), "");

		echo "<div class=\"row-fluid\">\n";
		echo "<div class=\"span4\">\n";
?>
  <ul class="nav nav-tabs" id="searchtab">
	  <li <?php echo ($fullsearch == false) ? 'class="active"' : ''; ?>><a data-target="#database" data-toggle="tab"><?php printMLText('databasesearch'); ?></a></li>
<?php
		if($enablefullsearch) {
?>
	  <li <?php echo ($fullsearch == true) ? 'class="active"' : ''; ?>><a data-target="#fulltext" data-toggle="tab"><?php printMLText('fullsearch'); ?></a></li>
<?php
		}
?>
	</ul>
	<div class="tab-content">
	  <div class="tab-pane <?php echo ($fullsearch == false) ? 'active' : ''; ?>" id="database">
<?php
// Database search Form {{{
		$this->contentContainerStart();
?>
<form action="../op/op.Search.php" name="form1" onsubmit="return checkForm();">
<table class="table-condensed">
<tr>
<td><?php printMLText("search_query");?>:</td>
<td>
<input type="text" name="query" value="<?php echo $this->query; ?>" />
<select name="mode">
<option value="1" <?php echo ($mode=='AND') ? "selected" : ""; ?>><?php printMLText("search_mode_and");?>
<option value="0"<?php echo ($mode=='OR') ? "selected" : ""; ?>><?php printMLText("search_mode_or");?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("search_in");?>:</td>
<td>
<label class="checkbox" for="keywords"><input type="checkbox" id="keywords" name="searchin[]" value="1" <?php if(in_array('1', $searchin)) echo " checked"; ?>><?php printMLText("keywords");?> (<?php printMLText('documents_only'); ?>)</label>
<label class="checkbox" for="searchName"><input type="checkbox" name="searchin[]" id="searchName" value="2" <?php if(in_array('2', $searchin)) echo " checked"; ?>><?php printMLText("name");?></label>
<label class="checkbox" for="comment"><input type="checkbox" name="searchin[]" id="comment" value="3" <?php if(in_array('3', $searchin)) echo " checked"; ?>><?php printMLText("comment");?></label>
<label class="checkbox" for="attributes"><input type="checkbox" name="searchin[]" id="attributes" value="4" <?php if(in_array('4', $searchin)) echo " checked"; ?>><?php printMLText("attributes");?></label>
</td>
</tr>
<?php
		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
?>
<tr>
	<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
	<td><?php $this->printAttributeEditField($attrdef, isset($attributes[$attrdef->getID()]) ? $attributes[$attrdef->getID()] : '') ?></td>
</tr>
<?php
			}
		}
?>
<tr>
<td><?php printMLText("category");?>:<br />(<?php printMLText('documents_only'); ?>)</td>
<td>
<select class="chzn-select" name="categoryids[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<!--
<option value="-1"><?php printMLText("all_categories");?>
-->
<?php
		$tmpcatids = array();
		foreach($categories as $tmpcat)
			$tmpcatids[] = $tmpcat->getID();
		foreach ($allCats as $catObj) {
			print "<option value=\"".$catObj->getID()."\" ".(in_array($catObj->getID(), $tmpcatids) ? "selected" : "").">" . htmlspecialchars($catObj->getName()) . "\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("status");?>:<br />(<?php printMLText('documents_only'); ?>)</td>
<td>
<?php if($workflowmode == 'traditional') { ?>
<label class="checkbox" for='pendingReview'><input type="checkbox" id="pendingReview" name="pendingReview" value="1" <?php echo in_array(S_DRAFT_REV, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_DRAFT_REV);?></label>
<label class="checkbox" for='pendingApproval'><input type="checkbox" id="pendingApproval" name="pendingApproval" value="1" <?php echo in_array(S_DRAFT_APP, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_DRAFT_APP);?></label>
<?php } else { ?>
<label class="checkbox" for='inWorkflow'><input type="checkbox" id="inWorkflow" name="inWorkflow" value="1" <?php echo in_array(S_IN_WORKFLOW, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_IN_WORKFLOW);?></label>
<?php } ?>
<label class="checkbox" for='released'><input type="checkbox" id="released" name="released" value="1" <?php echo in_array(S_RELEASED, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_RELEASED);?></label>
<label class="checkbox" for='rejected'><input type="checkbox" id="rejected" name="rejected" value="1" <?php echo in_array(S_REJECTED, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_REJECTED);?></label>
<label class="checkbox" for='obsolete'><input type="checkbox" id="obsolete" name="obsolete" value="1" <?php echo in_array(S_OBSOLETE, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_OBSOLETE);?></label>
<label class="checkbox" for='expired'><input type="checkbox" id="expired" name="expired" value="1" <?php echo in_array(S_EXPIRED, $status) ? "checked" : ""; ?>><?php printOverallStatusText(S_EXPIRED);?></label>
</td>
</tr>
<tr>
<td><?php printMLText("owner");?>:</td>
<td>
<select class="chzn-select-deselect" name="ownerid" data-placeholder="<?php printMLText('select_users'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<option value="-1"></option>
<?php
		foreach ($allUsers as $userObj) {
			if ($userObj->isGuest())
				continue;
			print "<option value=\"".$userObj->getID()."\" ".(($owner && $userObj->getID() == $owner->getID()) ? "selected" : "").">" . htmlspecialchars($userObj->getLogin()." - ".$userObj->getFullName()) . "\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("under_folder")?>:</td>
<td><?php $this->printFolderChooser("form1", M_READ, -1, $startfolder);?></td>
</tr>
<tr>
<td><?php printMLText("creation_date");?>:</td>
<td>
        <label class="checkbox inline">
				  <input type="checkbox" name="creationdate" value="true" <?php if($creationdate) echo "checked"; ?>/><?php printMLText("between");?>
        </label><br />
        <span class="input-append date" style="display: inline;" id="createstartdate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span4" size="16" name="createstart" type="text" value="<?php if($startdate) printf("%02d-%02d-%04d", $startdate['day'], $startdate['month'], $startdate['year']); else echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>&nbsp;
				<?php printMLText("and"); ?>
        <span class="input-append date" style="display: inline;" id="createenddate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span4" size="16" name="createend" type="text" value="<?php if($stopdate) printf("%02d-%02d-%04d", $stopdate['day'], $stopdate['month'], $stopdate['year']); else echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
</td>
</tr>
<tr>
<td><?php printMLText("expires");?>:<br />(<?php printMLText('documents_only'); ?>)</td>
<td>
        <label class="checkbox inline">
				  <input type="checkbox" name="expirationdate" value="true" <?php if($expirationdate) echo "checked"; ?>/><?php printMLText("between");?>
        </label><br />
        <span class="input-append date" style="display: inline;" id="expirationstartdate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span4" size="16" name="expirationstart" type="text" value="<?php if($expstartdate) printf("%02d-%02d-%04d", $expstartdate['day'], $expstartdate['month'], $expstartdate['year']); else echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>&nbsp;
				<?php printMLText("and"); ?>
        <span class="input-append date" style="display: inline;" id="expirationenddate" data-date="<?php echo date('d-m-Y'); ?>" data-date-format="dd-mm-yyyy" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span4" size="16" name="expirationend" type="text" value="<?php if($expstopdate) printf("%02d-%02d-%04d", $expstopdate['day'], $expstopdate['month'], $expstopdate['year']); else echo date('d-m-Y'); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
</td>
</tr>
<tr>
<td></td><td><button type="submit" class="btn"><i class="icon-search"></i> <?php printMLText("search"); ?></button></td>
</tr>

</table>
</form>
<?php
		$this->contentContainerEnd();
// }}}
?>
		</div>
<?php
		if($enablefullsearch) {
	  	echo "<div class=\"tab-pane ".(($fullsearch == true) ? 'active' : '')."\" id=\"fulltext\">\n";
	$this->contentContainerStart();
?>
<form action="../op/op.Search.php" name="form2" onsubmit="return checkForm();">
<input type="hidden" name="fullsearch" value="1" />
<table class="table-condensed">
<tr>
<td><?php printMLText("search_query");?>:</td>
<td>
<input type="text" name="query" value="<?php echo $this->query; ?>" />
<!--
<select name="mode">
<option value="1" selected><?php printMLText("search_mode_and");?>
<option value="0"><?php printMLText("search_mode_or");?>
</select>
-->
</td>
</tr>
<tr>
<td><?php printMLText("owner");?>:</td>
<td>
<select class="chzn-select-deselect" name="ownerid">
<option value="-1"></option>
<?php
			foreach ($allUsers as $userObj) {
				if ($userObj->isGuest())
					continue;
				print "<option value=\"".$userObj->getID()."\" ".(($owner && $userObj->getID() == $owner->getID()) ? "selected" : "").">" . htmlspecialchars($userObj->getLogin()." - ".$userObj->getFullName()) . "\n";
			}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("category_filter");?>:</td>
<td>
<select class="chzn-select" name="categoryids[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<!--
<option value="-1"><?php printMLText("all_categories");?>
-->
<?php
		$tmpcatids = array();
		foreach($categories as $tmpcat)
			$tmpcatids[] = $tmpcat->getID();
		foreach ($allCats as $catObj) {
			print "<option value=\"".$catObj->getID()."\" ".(in_array($catObj->getID(), $tmpcatids) ? "selected" : "").">" . htmlspecialchars($catObj->getName()) . "\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td></td><td><button type="submit" class="btn"><i class="icon-search"></i> <?php printMLText("search"); ?></button></td>
</tr>
</table>

</form>
<?php
			$this->contentContainerEnd();
			echo "</div>\n";
		}
?>
	</div>
<?php
		echo "</div>\n";
		echo "<div class=\"span8\">\n";
// Database search Result {{{
		$foldercount = $doccount = 0;
		if($entries) {
			foreach ($entries as $entry) {
				if(get_class($entry) == 'SeedDMS_Core_Document') {
					$doccount++;
				} elseif(get_class($entry) == 'SeedDMS_Core_Folder') {
					$foldercount++;
				}
			}
			print "<div class=\"alert\">".getMLText("search_report", array("doccount" => $totaldocs, "foldercount" => $totalfolders, 'searchtime'=>$searchTime))."</div>";
			$this->pageList($pageNumber, $totalpages, "../op/op.Search.php", $urlparams);
			$this->contentContainerStart();

			print "<table class=\"table\">";
			print "<thead>\n<tr>\n";
			print "<th></th>\n";
			print "<th>".getMLText("name")."</th>\n";
			print "<th>".getMLText("attributes")."</th>\n";
			print "<th>".getMLText("status")."</th>\n";
			print "<th>".getMLText("action")."</th>\n";
			print "</tr>\n</thead>\n<tbody>\n";

			$previewer = new SeedDMS_Preview_Previewer($cachedir, 40);
			foreach ($entries as $entry) {
				if(get_class($entry) == 'SeedDMS_Core_Document') {
					$document = $entry;
					$owner = $document->getOwner();
					$lc = $document->getLatestContent();
					$version = $lc->getVersion();
					$previewer->createPreview($lc);

					if (in_array(3, $searchin))
						$comment = $this->markQuery(htmlspecialchars($document->getComment()));
					else
						$comment = htmlspecialchars($document->getComment());
					if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
					print "<tr>";
					//print "<td><img src=\"../out/images/file.gif\" class=\"mimeicon\"></td>";
					if (in_array(2, $searchin)) {
						$docName = $this->markQuery(htmlspecialchars($document->getName()), "i");
					} else {
						$docName = htmlspecialchars($document->getName());
					}
					print "<td><a class=\"standardText\" href=\"../out/out.ViewDocument.php?documentid=".$document->getID()."\">";
					if($previewer->hasPreview($lc)) {
						print "<img class=\"mimeicon\" width=\"40\"src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$lc->getVersion()."&width=40\" title=\"".htmlspecialchars($lc->getMimeType())."\">";
					} else {
						print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($lc->getFileType())."\" title=\"".htmlspecialchars($lc->getMimeType())."\">";
					}
					print "</a></td>";
					print "<td><a class=\"standardText\" href=\"../out/out.ViewDocument.php?documentid=".$document->getID()."\">/";
					$folder = $document->getFolder();
					$path = $folder->getPath();
					for ($i = 1; $i  < count($path); $i++) {
						print htmlspecialchars($path[$i]->getName())."/";
					}
					print $docName;
					print "</a>";
				print "<br /><span style=\"font-size: 85%; font-style: italic; color: #666; \">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $document->getDate())."</b>, ".getMLText('version')." <b>".$version."</b> - <b>".date('Y-m-d', $lc->getDate())."</b></span>";
					if($comment) {
						print "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
					}
					print "</td>";

					print "<td>";
					print "<ul class=\"unstyled\">\n";
					$lcattributes = $lc->getAttributes();
					if($lcattributes) {
						foreach($lcattributes as $lcattribute) {
							$attrdef = $lcattribute->getAttributeDefinition();
							print "<li>".htmlspecialchars($attrdef->getName()).": ".htmlspecialchars($lcattribute->getValue())."</li>\n";
						}
					}
					print "</ul>\n";
					print "<ul class=\"unstyled\">\n";
					$docttributes = $document->getAttributes();
					if($docttributes) {
						foreach($docttributes as $docttribute) {
							$attrdef = $docttribute->getAttributeDefinition();
							print "<li>".htmlspecialchars($attrdef->getName()).": ".htmlspecialchars($docttribute->getValue())."</li>\n";
						}
					}
					print "</ul>\n";
					print "</td>";

					$display_status=$lc->getStatus();
					print "<td>".getOverallStatusText($display_status["status"]). "</td>";
					print "<td>";
					print "<div class=\"list-action\">";
					if($document->getAccessMode($user) >= M_ALL) {
?>
     <a class_="btn btn-mini" href="../out/out.RemoveDocument.php?documentid=<?php echo $document->getID(); ?>"><i class="icon-remove"></i></a>
<?php
					} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>
<?php
					}
					if($document->getAccessMode($user) >= M_READWRITE) {
?>
     <a href="../out/out.EditDocument.php?documentid=<?php echo $document->getID(); ?>"><i class="icon-edit"></i></a>
<?php
					} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-edit"></i></span>
<?php
					}
?>
     <a class="addtoclipboard" rel="<?php echo "D".$document->getID(); ?>" msg="<?php printMLText('splash_added_to_clipboard'); ?>" _href="../op/op.AddToClipboard.php?documentid=<?php echo $document->getID(); ?>&type=document&id=<?php echo $document->getID(); ?>&refferer=<?php echo urlencode($this->params['refferer']); ?>" title="<?php printMLText("add_to_clipboard");?>"><i class="icon-copy"></i></a>
<?php
					print "</div>";
					print "</td>";

					print "</tr>\n";
				} elseif(get_class($entry) == 'SeedDMS_Core_Folder') {
					$folder = $entry;
					$owner = $folder->getOwner();
					if (in_array(2, $searchin)) {
						$folderName = $this->markQuery(htmlspecialchars($folder->getName()), "i");
					} else {
						$folderName = htmlspecialchars($folder->getName());
					}
					print "<td><a class=\"standardText\" href=\"../out/out.ViewFolder.php?folderid=".$folder->getID()."\"><img src=\"".$this->imgpath."folder.png\" width=\"24\" height=\"24\" border=0></a></td>";
					print "<td><a class=\"standardText\" href=\"../out/out.ViewFolder.php?folderid=".$folder->getID()."\">";
					$path = $folder->getPath();
					print "/";
					for ($i = 1; $i  < count($path)-1; $i++) {
						print htmlspecialchars($path[$i]->getName())."/";
					}
					print $folderName;
					print "</a>";
					print "<br /><span style=\"font-size: 85%; font-style: italic; color: #666;\">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $folder->getDate())."</b></span>";
					if (in_array(3, $searchin)) $comment = $this->markQuery(htmlspecialchars($folder->getComment()));
					else $comment = htmlspecialchars($folder->getComment());
					if (strlen($comment) > 50) $comment = substr($comment, 0, 47) . "...";
					if($comment) {
						print "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
					}
					print "</td>";
					print "<td>";
					print "<ul class=\"unstyled\">\n";
					$folderattributes = $folder->getAttributes();
					if($folderattributes) {
						foreach($folderattributes as $folderattribute) {
							$attrdef = $folderattribute->getAttributeDefinition();
							print "<li>".htmlspecialchars($attrdef->getName()).": ".htmlspecialchars($folderattribute->getValue())."</li>\n";
						}
					}
					print "</td>";
					print "<td></td>";
					print "<td>";
					print "<div class=\"list-action\">";
					if($folder->getAccessMode($user) >= M_ALL) {
?>
     <a class_="btn btn-mini" href="../out/out.RemoveFolder.php?folderid=<?php echo $folder->getID(); ?>"><i class="icon-remove"></i></a>
<?php
					} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>
<?php
					}
					if($folder->getAccessMode($user) >= M_READWRITE) {
?>
     <a class_="btn btn-mini" href="../out/out.EditFolder.php?folderid=<?php echo $folder->getID(); ?>"><i class="icon-edit"></i></a>
<?php
					} else {
?>
     <span style="padding: 2px; color: #CCC;"><i class="icon-edit"></i></span>
<?php
					}
?>
     <a class="addtoclipboard" rel="<?php echo "F".$folder->getID(); ?>" msg="<?php printMLText('splash_added_to_clipboard'); ?>" _href="../op/op.AddToClipboard.php?folderid=<?php echo $folder->getID(); ?>&type=folder&id=<?php echo $folder->getID(); ?>&refferer=<?php echo urlencode($this->params['refferer']); ?>" title="<?php printMLText("add_to_clipboard");?>"><i class="icon-copy"></i></a>
<?php
					print "</div>";
					print "</td>";
					print "</tr>\n";
				}
			}
			print "</tbody></table>\n";
			$this->contentContainerEnd();
			$this->pageList($pageNumber, $totalpages, "../op/op.Search.php", $_GET);
		} else {
			$numResults = $doccount + $foldercount;
			if ($numResults == 0) {
				print "<div class=\"alert alert-error\">".getMLText("search_no_results")."</div>";
			}
		}
// }}}
		echo "</div>";
		echo "</div>";
		$this->htmlEndPage();
	} /* }}} */
}
?>

