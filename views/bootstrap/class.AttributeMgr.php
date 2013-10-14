<?php
/**
 * Implementation of AttributeMgr view
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
 * Class which outputs the html page for AttributeMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AttributeMgr extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$attrdefs = $this->params['attrdefs'];

		$this->htmlStartPage(getMLText("admin_tools"));
?>

<script language="JavaScript">
obj = -1;
function showAttributeDefinitions(selectObj) {
	if (obj != -1)
		obj.style.display = "none";
	
	id = selectObj.options[selectObj.selectedIndex].value;
	if (id == -1)
		return;
	
	obj = document.getElementById("attrdefs" + id);
	obj.style.display = "";
}
</script>
<?php
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("attrdef_management"));
?>

<div class="row-fluid">
<div class="span4">
<div class="well">
<?php echo getMLText("selection")?>:
	<select onchange="showAttributeDefinitions(this)" id="selector" class="span9">
		<option value="-1"><?php echo getMLText("choose_attrdef")?>
		<option value="0"><?php echo getMLText("new_attrdef")?>
<?php
		$selected=0;
		$count=2;
		if($attrdefs) {
			foreach ($attrdefs as $attrdef) {
			
				if (isset($_GET["attrdefid"]) && $attrdef->getID()==$_GET["attrdefid"]) $selected=$count;				
				switch($attrdef->getObjType()) {
					case SeedDMS_Core_AttributeDefinition::objtype_all:
						$ot = getMLText("all");
						break;
					case SeedDMS_Core_AttributeDefinition::objtype_folder:
						$ot = getMLText("folder");
						break;
					case SeedDMS_Core_AttributeDefinition::objtype_document:
						$ot = getMLText("document");
						break;
					case SeedDMS_Core_AttributeDefinition::objtype_documentcontent:
						$ot = getMLText("version");
						break;
				}
				print "<option value=\"".$attrdef->getID()."\">" . htmlspecialchars($attrdef->getName() ." (".$ot.")");
				$count++;
			}
		}
?>
	</select>
</div>
</div>

<div class="span8">
<div class="well" id="attrdefs0" style="display : none;">
			<form action="../op/op.AttributeMgr.php" method="post">
  		<?php echo createHiddenFieldWithKey('addattrdef'); ?>
			<input type="hidden" name="action" value="addattrdef">
			<table class="table-condensed">
				<tr>
					<td><?php printMLText("attrdef_name");?>:</td><td><input type="text" name="name"></td>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_objtype");?>:</td><td><select name="objtype"><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_all ?>">All</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_folder ?>">Folder</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_document ?>"><?php printMLText("document"); ?></option><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_documentcontent ?>"><?php printMLText("version"); ?></option></select>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_type");?>:</td><td><select name="type"><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_int ?>">Integer</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_float ?>">Float</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_string ?>">String</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_boolean ?>">Boolean</option></select></td>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_multiple");?>:</td><td><input type="checkbox" value="1" name="multiple" /></td>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_minvalues");?>:</td><td><input type="text" value="" name="minvalues" /></td>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_maxvalues");?>:</td><td><input type="text" value="" name="maxvalues" /></td>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_valueset");?>:</td><td><input type="text" value="" name="valueset" /></td>
				</tr>
				<tr>
					<td><?php printMLText("attrdef_regex");?>:</td><td><input type="text" value="" name="regex" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" class="btn" value="<?php printMLText("new_attrdef"); ?>"></td>
				</tr>
			</table>
			</form>
			</div>
	
<?php	
	
			if($attrdefs) {
				foreach ($attrdefs as $attrdef) {
				
					print "<div id=\"attrdefs".$attrdef->getID()."\" style=\"display : none;\">";	
?>
				<div class="well">
<?php
					if(!$attrdef->isUsed()) {
?>
							<form style="display: inline-block;" method="post" action="../op/op.AttributeMgr.php" >
							<?php echo createHiddenFieldWithKey('removeattrdef'); ?>
							<input type="hidden" name="attrdefid" value="<?php echo $attrdef->getID()?>">
							<input type="hidden" name="action" value="removeattrdef">
							<button type="submit" class="btn"><i class="icon-remove"></i> <?php echo getMLText("rm_attrdef")?></button>
							</form>
<?php
					} else {
						echo '<p>'.getMLText('attrdef_in_use').'</p>';
						$res = $attrdef->getStatistics(3);
						if(isset($res['frequencies']) && $res['frequencies']) {
							print "<table class=\"table-condensed\">";
							print "<thead>\n<tr>\n";
							print "<th>".getMLText("count")."</th>\n";
							print "<th>".getMLText("value")."</th>\n";
							print "</tr></thead>\n<tbody>\n";
							foreach($res['frequencies'] as $entry) {
								echo "<tr><td>".$entry['c']."</td><td>".$entry['value']."</td></tr>";
							}
							print "</tbody></table>";
						}
						if($res['docs']) {
							print "<table class=\"table-condensed\">";
							print "<thead>\n<tr>\n";
							print "<th></th>\n";
							print "<th>".getMLText("name")."</th>\n";
							print "<th>".getMLText("owner")."</th>\n";
							print "<th>".getMLText("status")."</th>\n";
							print "<th>".getMLText("value")."</th>\n";
							print "<th>".getMLText("actions")."</th>\n";
							print "</tr></thead>\n<tbody>\n";
							foreach($res['docs'] as $doc) {
								$owner = $doc->getOwner();
								$latest = $doc->getLatestContent();
								$status = $latest->getStatus();
								print "<tr>\n";
								print "<td><i class=\"icon-file\"></i></td>";
								print "<td><a href=\"../out/out.ViewDocument.php?documentid=".$doc->getID()."\">" . htmlspecialchars($doc->getName()) . "</a></td>\n";
								print "<td>".htmlspecialchars($owner->getFullName())."</td>";
								print "<td>".getOverallStatusText($status["status"])."</td>";
								print "<td>".$doc->getAttributeValue($attrdef)."</td>";
								print "<td>";
								print "<a href='../out/out.EditDocument.php?documentid=".$doc->getID()."' class=\"btn btn-mini\"><i class=\"icon-edit\"></i> ".getMLText("edit")."</a>";
								print "</td></tr>\n";
							}
							print "</tbody></table>";
						}

						if($res['folders']) {
							print "<table class=\"table-condensed\">";
							print "<thead><tr>\n";
							print "<th></th>\n";
							print "<th>".getMLText("name")."</th>\n";
							print "<th>".getMLText("owner")."</th>\n";
							print "<th>".getMLText("value")."</th>\n";
							print "<th>".getMLText("actions")."</th>\n";
							print "</tr></thead>\n<tbody>\n";
							foreach($res['folders'] as $folder) {
								$owner = $folder->getOwner();
								print "<tr class=\"folder\">";
								print "<td><i class=\"icon-folder-close-alt\"></i></td>";
								print "<td><a href=\"../out/out.ViewFolder.php?folderid=".$folder->getID()."\">" . htmlspecialchars($folder->getName()) . "</a></td>\n";
								print "<td>".htmlspecialchars($owner->getFullName())."</td>";
								print "<td>".$folder->getAttributeValue($attrdef)."</td>";
								print "<td>";
								print "<a href='../out/out.EditFolder.php?folderid=".$folder->getID()."' class=\"btn btn-mini\"><i class=\"icon-edit\"></i> ".getMLText("edit")."</a>";
								print "</td></tr>";
							}
							print "</tbody></table>";
						}

						if($res['contents']) {
							print "<table class=\"table-condensed\">";
							print "<thead>\n<tr>\n";
							print "<th></th>\n";
							print "<th>".getMLText("name")."</th>\n";
							print "<th>".getMLText("owner")."</th>\n";
							print "<th>".getMLText("mimetype")."</th>\n";
							print "<th>".getMLText("version")."</th>\n";
							print "<th>".getMLText("value")."</th>\n";
							print "<th>".getMLText("actions")."</th>\n";
							print "</tr></thead>\n<tbody>\n";
							foreach($res['contents'] as $content) {
								$doc = $content->getDocument();
								$owner = $doc->getOwner();
								print "<tr>\n";
								print "<td><i class=\"icon-file\"></i></td>";
								print "<td><a href=\"../out/out.ViewDocument.php?documentid=".$doc->getID()."\">" . htmlspecialchars($doc->getName()) . "</a></td>\n";
								print "<td>".htmlspecialchars($owner->getFullName())."</td>";
								print "<td>".$content->getMimeType()."</td>";
								print "<td>".$content->getVersion()."</td>";
								print "<td>".$content->getAttributeValue($attrdef)."</td>";
								print "<td>";
								print "<a href='../out/out.EditDocument.php?documentid=".$doc->getID()."' class=\"btn btn-mini\"><i class=\"icon-edit\"></i> ".getMLText("edit")."</a>";
								print "</td></tr>\n";
							}
							print "</tbody></table>";
						}
					}
?>
				</div>
				<div class="well">
				<table class="table-condensed">
					<form action="../op/op.AttributeMgr.php" method="post">
					<tr>
						<td>
								<?php echo createHiddenFieldWithKey('editattrdef'); ?>
								<input type="Hidden" name="action" value="editattrdef">
								<input type="Hidden" name="attrdefid" value="<?php echo $attrdef->getID()?>" />
								<?php printMLText("attrdef_name");?>:
						</td>
						<td>
							<input type="text" name="name" value="<?php echo htmlspecialchars($attrdef->getName()) ?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_type");?>:
						</td>
						<td>
							<select name="type"><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_int ?>" <?php if($attrdef->getType() == SeedDMS_Core_AttributeDefinition::type_int) echo "selected"; ?>>Integer</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_float ?>" <?php if($attrdef->getType() == SeedDMS_Core_AttributeDefinition::type_float) echo "selected"; ?>>Float</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_string ?>" <?php if($attrdef->getType() == SeedDMS_Core_AttributeDefinition::type_string) echo "selected"; ?>>String</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::type_boolean ?>" <?php if($attrdef->getType() == SeedDMS_Core_AttributeDefinition::type_boolean) echo "selected"; ?>>Boolean</option></select>
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_objtype");?>:
						</td>
						<td>
							<select name="objtype"><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_all ?>">All</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_folder ?>" <?php if($attrdef->getObjType() == SeedDMS_Core_AttributeDefinition::objtype_folder) echo "selected"; ?>>Folder</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_document ?>" <?php if($attrdef->getObjType() == SeedDMS_Core_AttributeDefinition::objtype_document) echo "selected"; ?>>Document</option><option value="<?php echo SeedDMS_Core_AttributeDefinition::objtype_documentcontent ?>" <?php if($attrdef->getObjType() == SeedDMS_Core_AttributeDefinition::objtype_documentcontent) echo "selected"; ?>>Document content</option></select>
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_multiple");?>:
						</td>
						<td>
							<input type="checkbox" value="1" name="multiple" <?php echo $attrdef->getMultipleValues() ? "checked" : "" ?>/>
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_minvalues");?>:
						</td>
						<td>
							<input type="text" value="<?php echo $attrdef->getMinValues() ?>" name="minvalues" />
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_maxvalues");?>:
						</td>
						<td>
							<input type="text" value="<?php echo $attrdef->getMaxValues() ?>" name="maxvalues" />
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_valueset");?>:
						</td>
						<td>
							<input type="text" value="<?php echo $attrdef->getValueSet() ?>" name="valueset" />
						</td>
					</tr>
					<tr>
						<td>
							<?php printMLText("attrdef_regex");?>:
						</td>
						<td>
							<input type="text" value="<?php echo $attrdef->getRegex() ?>" name="regex" />
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save");?></button>
						</td>
					</tr>
					</form>
					
				</table>
			</div>
			</div>
<?php
				}
			}
?>
</div>
</div>
</div>
	
<script language="JavaScript">

sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showAttributeDefinitions(sel);

</script>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
