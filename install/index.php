<?php
define("SEEDDMS_INSTALL", "on");
include("../inc/inc.Settings.php");
$settings = new Settings();
$rootDir = realpath ("..");
$settings->_rootDir = $rootDir.'/';

$theme = "blue";
include("../inc/inc.Language.php");
include "../languages/en_GB/lang.inc";
include("../inc/inc.ClassUI.php");
$LANG['en_GB'] = $text;

UI::htmlStartPage("INSTALL");
UI::contentHeading("SeedDMS Installation...");
UI::contentContainerStart();
echo "<h2>".getMLText('settings_install_welcome_title')."</h2>";
echo "<div style=\"width: 600px;\">".getMLText('settings_install_welcome_text')."</div>";
echo '<p><a href="install.php">' . getMLText("settings_start_install") . '</a></p>';
UI::contentContainerEnd();
UI::htmlEndPage();
?>
