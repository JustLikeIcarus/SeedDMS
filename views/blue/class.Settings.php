<?php
/**
 * Implementation of Settings view
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
 * Class which outputs the html page for Settings view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Settings extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$settings = $this->params['settings'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("settings"));
		$this->contentContainerStart();

?>

<script language="JavaScript">
function ShowHide(strId)
{

  var objDiv = document.getElementById(strId);

  if (objDiv)
  {

    if(objDiv.style.display == 'block')
    {
      objDiv.style.display = 'none';
    }
    else
    {
      objDiv.style.display = 'block';
    }
  }
}
</script>

  <form action="../op/op.Settings.php" method="post" enctype="multipart/form-data" name="form0" >
  <input type="hidden" name="action" value="saveSettings" />
<?php
if(!is_writeable($settings->_configFilePath)) {
	echo "<p>".getMLText("settings_notwritable")."</p>";
} else {
?>
  <input type="Submit" value="<?php printMLText("save");?>" />
<?php
}
?>


  <div class="contentHeading" onClick="ShowHide('siteID')" style="cursor:pointer">+ <?php printMLText("settings_Site");?></div>
  <div id="siteID" style="display:block">
    <table>
      <!--
        -- SETTINGS - SITE - DISPLAY
      -->
      <tr ><td><b> <?php printMLText("settings_Display");?></b></td> </tr>
      <tr title="<?php printMLText("settings_siteName_desc");?>">
        <td><?php printMLText("settings_siteName");?>:</td>
        <td><input type="text" name="siteName" value="<?php echo $settings->_siteName ?>"/></td>
      </tr>
      <tr title="<?php printMLText("settings_footNote_desc");?>">
        <td><?php printMLText("settings_footNote");?>:</td>
        <td><input type="text" name="footNote" value="<?php echo $settings->_footNote ?>" size="100"/></td>
      </tr>
      <tr title="<?php printMLText("settings_printDisclaimer_desc");?>">
        <td><?php printMLText("settings_printDisclaimer");?>:</td>
        <td><input name="printDisclaimer" type="checkbox" <?php if ($settings->_printDisclaimer) echo "checked" ?> /></td>
      </tr>
       <tr title="<?php printMLText("settings_language_desc");?>">
        <td><?php printMLText("settings_language");?>:</td>
        <td>
         <SELECT name="language">
            <?php
              $languages = getLanguages();
              foreach($languages as $language)
              {
                echo '<option value="' . $language . '" ';
                 if ($settings->_language==$language)
                   echo "selected";
                echo '>' . getMLText($language) . '</option>';
             }
            ?>
          </SELECT>
        </td>
      </tr>
      <tr title="<?php printMLText("settings_theme_desc");?>">
        <td><?php printMLText("settings_theme");?>:</td>
        <td>
         <SELECT name="theme">
            <?php
              $themes = UI::getStyles();
              foreach($themes as $theme)
              {
                echo '<option value="' . $theme . '" ';
                 if ($settings->_theme==$theme)
                   echo "selected";
                echo '>' . $theme . '</option>';
             }
            ?>
          </SELECT>
        </td>
      </tr>

      <!--
        -- SETTINGS - SITE - EDITION
      -->
      <tr><td></td></tr><tr ><td><b> <?php printMLText("settings_Edition");?></b></td> </tr>
      <tr title="<?php printMLText("settings_strictFormCheck_desc");?>">
        <td><?php printMLText("settings_strictFormCheck");?>:</td>
        <td><input name="strictFormCheck" type="checkbox" <?php if ($settings->_strictFormCheck) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_viewOnlineFileTypes_desc");?>">
        <td><?php printMLText("settings_viewOnlineFileTypes");?>:</td>
        <td><input type="text" name="viewOnlineFileTypes" value="<?php echo $settings->getViewOnlineFileTypesToString() ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableConverting_desc");?>">
        <td><?php printMLText("settings_enableConverting");?>:</td>
        <td><input name="enableConverting" type="checkbox" <?php if ($settings->_enableConverting) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableEmail_desc");?>">
        <td><?php printMLText("settings_enableEmail");?>:</td>
        <td><input name="enableEmail" type="checkbox" <?php if ($settings->_enableEmail) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableUsersView_desc");?>">
        <td><?php printMLText("settings_enableUsersView");?>:</td>
        <td><input name="enableUsersView" type="checkbox" <?php if ($settings->_enableUsersView) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableFullSearch_desc");?>">
        <td><?php printMLText("settings_enableFullSearch");?>:</td>
        <td><input name="enableFullSearch" type="checkbox" <?php if ($settings->_enableFullSearch) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_stopWordsFile_desc");?>">
        <td><?php printMLText("settings_stopWordsFile");?>:</td>
        <td><input type="text" name="stopWordsFile" value="<?php echo $settings->_stopWordsFile; ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableFolderTree_desc");?>">
        <td><?php printMLText("settings_enableFolderTree");?>:</td>
        <td><input name="enableFolderTree" type="checkbox" <?php if ($settings->_enableFolderTree) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_expandFolderTree_desc");?>">
        <td><?php printMLText("settings_expandFolderTree");?>:</td>
        <td>
          <SELECT name="expandFolderTree">
            <OPTION VALUE="0" <?php if ($settings->_expandFolderTree==0) echo "SELECTED" ?> ><?php printMLText("settings_expandFolderTree_val0");?></OPTION>
            <OPTION VALUE="1" <?php if ($settings->_expandFolderTree==1) echo "SELECTED" ?> ><?php printMLText("settings_expandFolderTree_val1");?></OPTION>
            <OPTION VALUE="2" <?php if ($settings->_expandFolderTree==2) echo "SELECTED" ?> ><?php printMLText("settings_expandFolderTree_val2");?></OPTION>
          </SELECT>
      </tr>
      <tr title="<?php printMLText("settings_sortUsersInList_desc");?>">
        <td><?php printMLText("settings_sortUsersInList");?>:</td>
        <td>
          <SELECT name="sortUsersInList">
            <OPTION VALUE="" <?php if ($settings->_sortUsersInList=='') echo "SELECTED" ?> ><?php printMLText("settings_sortUsersInList_val_login");?></OPTION>
            <OPTION VALUE="fullname" <?php if ($settings->_sortUsersInList=='fullname') echo "SELECTED" ?> ><?php printMLText("settings_sortUsersInList_val_fullname");?></OPTION>
          </SELECT>
      </tr>

      <!--
        -- SETTINGS - SITE - CALENDAR
      -->
     <tr><td></td></tr><tr ><td><b> <?php printMLText("settings_Calendar");?></b></td> </tr>
      <tr title="<?php printMLText("settings_enableCalendar_desc");?>">
        <td><?php printMLText("settings_enableCalendar");?>:</td>
        <td><input name="enableCalendar" type="checkbox" <?php if ($settings->_enableCalendar) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_calendarDefaultView_desc");?>">
        <td><?php printMLText("settings_calendarDefaultView");?>:</td>
        <td>
          <SELECT name="calendarDefaultView">
            <OPTION VALUE="w" <?php if ($settings->_calendarDefaultView=="w") echo "SELECTED" ?> ><?php printMLText("week_view");?></OPTION>
            <OPTION VALUE="m" <?php if ($settings->_calendarDefaultView=="m") echo "SELECTED" ?> ><?php printMLText("month_view");?></OPTION>
            <OPTION VALUE="y" <?php if ($settings->_calendarDefaultView=="y") echo "SELECTED" ?> ><?php printMLText("year_view");?></OPTION>
          </SELECT>
      </tr>
     <tr title="<?php printMLText("settings_firstDayOfWeek_desc");?>">
        <td><?php printMLText("settings_firstDayOfWeek");?>:</td>
        <td>
          <SELECT name="firstDayOfWeek">
            <OPTION VALUE="0" <?php if ($settings->_firstDayOfWeek=="0") echo "SELECTED" ?> ><?php printMLText("sunday");?></OPTION>
            <OPTION VALUE="1" <?php if ($settings->_firstDayOfWeek=="1") echo "SELECTED" ?> ><?php printMLText("monday");?></OPTION>
            <OPTION VALUE="2" <?php if ($settings->_firstDayOfWeek=="2") echo "SELECTED" ?> ><?php printMLText("tuesday");?></OPTION>
            <OPTION VALUE="3" <?php if ($settings->_firstDayOfWeek=="3") echo "SELECTED" ?> ><?php printMLText("wednesday");?></OPTION>
            <OPTION VALUE="4" <?php if ($settings->_firstDayOfWeek=="4") echo "SELECTED" ?> ><?php printMLText("thursday");?></OPTION>
            <OPTION VALUE="5" <?php if ($settings->_firstDayOfWeek=="5") echo "SELECTED" ?> ><?php printMLText("friday");?></OPTION>
            <OPTION VALUE="6" <?php if ($settings->_firstDayOfWeek=="6") echo "SELECTED" ?> ><?php printMLText("saturday");?></OPTION>
          </SELECT>
      </tr>
    </table>
  </div>
  <br>
  <div class="contentHeading" onClick="ShowHide('systemID')" style="cursor:pointer">+ <?php printMLText("settings_System");?></div>
  <div id="systemID" style="display:block">
    <table>
     <!--
        -- SETTINGS - SYSTEM - SERVER
      -->
      <tr ><td><b> <?php printMLText("settings_Server");?></b></td> </tr>
      <tr title="<?php printMLText("settings_rootDir_desc");?>">
        <td><?php printMLText("settings_rootDir");?>:</td>
        <td><input type="text" name="rootDir" value="<?php echo $settings->_rootDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_httpRoot_desc");?>">
        <td><?php printMLText("settings_httpRoot");?>:</td>
        <td><input type="text" name="httpRoot" value="<?php echo $settings->_httpRoot ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_contentDir_desc");?>">
        <td><?php printMLText("settings_contentDir");?>:</td>
        <td><input type="text" name="contentDir" value="<?php echo $settings->_contentDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_stagingDir_desc");?>">
        <td><?php printMLText("settings_stagingDir");?>:</td>
        <td><input type="text" name="stagingDir" value="<?php echo $settings->_stagingDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_cacheDir_desc");?>">
        <td><?php printMLText("settings_cacheDir");?>:</td>
        <td><input type="text" name="cacheDir" value="<?php echo $settings->_cacheDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_luceneDir_desc");?>">
        <td><?php printMLText("settings_luceneDir");?>:</td>
        <td><input type="text" name="luceneDir" value="<?php echo $settings->_luceneDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_dropFolderDir_desc");?>">
        <td><?php printMLText("settings_dropFolderDir");?>:</td>
        <td><input type="text" name="dropFolderDir" value="<?php echo $settings->_dropFolderDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_logFileEnable_desc");?>">
        <td><?php printMLText("settings_logFileEnable");?>:</td>
        <td><input name="logFileEnable" type="checkbox" <?php if ($settings->_logFileEnable) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_logFileRotation_desc");?>">
        <td><?php printMLText("settings_logFileRotation");?>:</td>
        <td>
          <SELECT name="logFileRotation">
            <OPTION VALUE="h" <?php if ($settings->_logFileRotation=="h") echo "SELECTED" ?> ><?php printMLText("hourly");?></OPTION>
            <OPTION VALUE="d" <?php if ($settings->_logFileRotation=="d") echo "SELECTED" ?> ><?php printMLText("daily");?></OPTION>
            <OPTION VALUE="m" <?php if ($settings->_logFileRotation=="m") echo "SELECTED" ?> ><?php printMLText("monthly");?></OPTION>
          </SELECT>
      </tr>
      <tr title="<?php printMLText("settings_enableLargeFileUpload_desc");?>">
        <td><?php printMLText("settings_enableLargeFileUpload");?>:</td>
        <td><input name="enableLargeFileUpload" type="checkbox" <?php if ($settings->_enableLargeFileUpload) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_partitionSize_desc");?>">
        <td><?php printMLText("settings_partitionSize");?>:</td>
        <td><input type="text" name="partitionSize" value="<?php echo $settings->_partitionSize ?>" size="100" /></td>
      </tr>
      <!--
        -- SETTINGS - SYSTEM - AUTHENTICATION
      -->
      <tr ><td><b> <?php printMLText("settings_Authentication");?></b></td> </tr>
      <tr title="<?php printMLText("settings_enableGuestLogin_desc");?>">
        <td><?php printMLText("settings_enableGuestLogin");?>:</td>
        <td><input name="enableGuestLogin" type="checkbox" <?php if ($settings->_enableGuestLogin) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_restricted_desc");?>">
        <td><?php printMLText("settings_restricted");?>:</td>
        <td><input name="restricted" type="checkbox" <?php if ($settings->_restricted) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableUserImage_desc");?>">
        <td><?php printMLText("settings_enableUserImage");?>:</td>
        <td><input name="enableUserImage" type="checkbox" <?php if ($settings->_enableUserImage) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_disableSelfEdit_desc");?>">
        <td><?php printMLText("settings_disableSelfEdit");?>:</td>
        <td><input name="disableSelfEdit" type="checkbox" <?php if ($settings->_disableSelfEdit) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enablePasswordForgotten_desc");?>">
        <td><?php printMLText("settings_enablePasswordForgotten");?>:</td>
        <td><input name="enablePasswordForgotten" type="checkbox" <?php if ($settings->_enablePasswordForgotten) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_passwordЅtrength_desc");?>">
        <td><?php printMLText("settings_passwordStrength");?>:</td>
        <td><input type="text" name="passwordStrength" value="<?php echo $settings->_passwordStrength; ?>" size="2" /></td>
      </tr>
      <tr title="<?php printMLText("settings_passwordStrengthAlgorithm_desc");?>">
        <td><?php printMLText("settings_passwordStrengthAlgorithm");?>:</td>
        <td>
				  <select name="passwordStrengthAlgorithm">
					  <option value="simple" <?php if ($settings->_passwordStrengthAlgorithm=='simple') echo "selected" ?>><?php printMLText("settings_passwordStrengthAlgorithm_valsimple");?></option>
						<option value="advanced" <?php if ($settings->_passwordStrengthAlgorithm=='advanced') echo "selected" ?>><?php printMLText("settings_passwordStrengthAlgorithm_valadvanced");?></option>
					</select>
				</td>
      </tr>
      <tr title="<?php printMLText("settings_passwordExpiration_desc");?>">
        <td><?php printMLText("settings_passwordExpiration");?>:</td>
        <td><input name="passwordExpiration" value="<?php echo $settings->_passwordExpiration; ?>" size="3" /></td>
      </tr>
      <tr title="<?php printMLText("settings_passwordHistory_desc");?>">
        <td><?php printMLText("settings_passwordHistory");?>:</td>
        <td><input name="passwordHistory" value="<?php echo $settings->_passwordHistory; ?>" size="2" /></td>
      </tr>
      <tr title="<?php printMLText("settings_loginFailure_desc");?>">
        <td><?php printMLText("settings_loginFailure");?>:</td>
        <td><input name="loginFailure" value="<?php echo $settings->_loginFailure; ?>" size="2" /></td>
      </tr>
      <tr title="<?php printMLText("settings_quota_desc");?>">
        <td><?php printMLText("settings_quota");?>:</td>
        <td><input type="text" name="quota" value="<?php echo $settings->_quota; ?>" size="2" /></td>
      </tr>
      <tr title="<?php printMLText("settings_encryptionKey_desc");?>">
        <td><?php printMLText("settings_encryptionKey");?>:</td>
        <td><input type="text" name="encryptionKey" value="<?php echo $settings->_encryptionKey; ?>" size="32" /></td>
      </tr>
      <tr title="<?php printMLText("settings_cookieLifetime_desc");?>">
        <td><?php printMLText("settings_cookieLifetime");?>:</td>
        <td><input type="text" name="cookieLifetime" value="<?php echo $settings->_cookieLifetime; ?>" size="32" /></td>
      </tr>

      <!-- TODO Connectors -->

     <!--
        -- SETTINGS - SYSTEM - DATABASE
      -->
      <tr ><td><b> <?php printMLText("settings_Database");?></b></td> </tr>
      <tr title="<?php printMLText("settings_dbDriver_desc");?>">
        <td><?php printMLText("settings_dbDriver");?>:</td>
        <td><input type="text" name="dbDriver" value="<?php echo $settings->_dbDriver ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_dbHostname_desc");?>">
        <td><?php printMLText("settings_dbHostname");?>:</td>
        <td><input type="text" name="dbHostname" value="<?php echo $settings->_dbHostname ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_dbDatabase_desc");?>">
        <td><?php printMLText("settings_dbDatabase");?>:</td>
        <td><input type="text" name="dbDatabase" value="<?php echo $settings->_dbDatabase ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_dbUser_desc");?>">
        <td><?php printMLText("settings_dbUser");?>:</td>
        <td><input type="text" name="dbUser" value="<?php echo $settings->_dbUser ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_dbPass_desc");?>">
        <td><?php printMLText("settings_dbPass");?>:</td>
        <td><input type="text" name="dbPass" value="<?php echo $settings->_dbPass ?>" type="password" /></td>
      </tr>

     <!--
        -- SETTINGS - SYSTEM - SMTP
      -->
      <tr ><td><b> <?php printMLText("settings_SMTP");?></b></td> </tr>
      <tr title="<?php printMLText("settings_smtpServer_desc");?>">
        <td><?php printMLText("settings_smtpServer");?>:</td>
        <td><input type="text" name="smtpServer" value="<?php echo $settings->_smtpServer ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_smtpPort_desc");?>">
        <td><?php printMLText("settings_smtpPort");?>:</td>
        <td><input type="text" name="smtpPort" value="<?php echo $settings->_smtpPort ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_smtpSendFrom_desc");?>">
        <td><?php printMLText("settings_smtpSendFrom");?>:</td>
        <td><input type="text" name="smtpSendFrom" value="<?php echo $settings->_smtpSendFrom ?>" /></td>
      </tr>

    </table>
  </div>

  <br>
  <div class="contentHeading" onClick="ShowHide('advancedID')" style="cursor:pointer">+ <?php printMLText("settings_Advanced");?></div>
  <div id="advancedID" style="display:none">
    <table>
      <!--
        -- SETTINGS - ADVANCED - DISPLAY
      -->
      <tr ><td><b> <?php printMLText("settings_Display");?></b></td> </tr>
      <tr title="<?php printMLText("settings_siteDefaultPage_desc");?>">
        <td><?php printMLText("settings_siteDefaultPage");?>:</td>
        <td><input type="text" name="siteDefaultPage" value="<?php echo $settings->_siteDefaultPage ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_rootFolderID_desc");?>">
        <td><?php printMLText("settings_rootFolderID");?>:</td>
        <td><input type="text" name="rootFolderID" value="<?php echo $settings->_rootFolderID ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_titleDisplayHack_desc");?>">
        <td><?php printMLText("settings_titleDisplayHack");?>:</td>
        <td><input name="titleDisplayHack" type="checkbox" <?php if ($settings->_titleDisplayHack) echo "checked" ?> /></td>
      </tr>

      <!--
        -- SETTINGS - ADVANCED - AUTHENTICATION
      -->
      <tr ><td><b> <?php printMLText("settings_Authentication");?></b></td> </tr>
      <tr title="<?php printMLText("settings_guestID_desc");?>">
        <td><?php printMLText("settings_guestID");?>:</td>
        <td><input type="text" name="guestID" value="<?php echo $settings->_guestID ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_adminIP_desc");?>">
        <td><?php printMLText("settings_adminIP");?>:</td>
        <td><input type="text" name="adminIP" value="<?php echo $settings->_adminIP ?>" /></td>
      </tr>

      <!--
        -- SETTINGS - ADVANCED - EDITION
      -->
      <tr ><td><b> <?php printMLText("settings_Edition");?></b></td> </tr>
      <tr title="<?php printMLText("settings_workflowMode_desc");?>">
        <td><?php printMLText("settings_workflowMode");?>:</td>
        <td>
				  <select name="workflowMode">
					  <option value="traditional" <?php if ($settings->_workflowMode=='traditional') echo "selected" ?>><?php printMLText("settings_workflowMode_valtraditional");?></option>
						<option value="advanced" <?php if ($settings->_workflowMode=='advanced') echo "selected" ?>><?php printMLText("settings_workflowMode_valadvanced");?></option>
					</select>
				</td>
      </tr>
      <tr title="<?php printMLText("settings_versioningFileName_desc");?>">
        <td><?php printMLText("settings_versioningFileName");?>:</td>
        <td><input type="text" name="versioningFileName" value="<?php echo $settings->_versioningFileName ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableAdminRevApp_desc");?>">
        <td><?php printMLText("settings_enableAdminRevApp");?>:</td>
        <td><input name="enableAdminRevApp" type="checkbox" <?php if ($settings->_enableAdminRevApp) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableVersionDeletion_desc");?>">
        <td><?php printMLText("settings_enableVersionDeletion");?>:</td>
        <td><input name="enableVersionDeletion" type="checkbox" <?php if ($settings->_enableVersionDeletion) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableVersionModification_desc");?>">
        <td><?php printMLText("settings_enableVersionModification");?>:</td>
        <td><input name="enableVersionModification" type="checkbox" <?php if ($settings->_enableVersionModification) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableDuplicateDocNames_desc");?>">
        <td><?php printMLText("settings_enableDuplicateDocNames");?>:</td>
        <td><input name="enableDuplicateDocNames" type="checkbox" <?php if ($settings->_enableDuplicateDocNames) echo "checked" ?> /></td>
      </tr>

      <!--
        -- SETTINGS - ADVANCED - NOTIFICATION
      -->
      <tr ><td><b> <?php printMLText("settings_Notification");?></b></td> </tr>
      <tr title="<?php printMLText("settings_enableOwnerNotification_desc");?>">
        <td><?php printMLText("settings_enableOwnerNotification");?>:</td>
        <td><input name="enableOwnerNotification" type="checkbox" <?php if ($settings->_enableOwnerNotification) echo "checked" ?> /></td>
      </tr>
      <tr title="<?php printMLText("settings_enableNotificationAppRev_desc");?>">
        <td><?php printMLText("settings_enableNotificationAppRev");?>:</td>
        <td><input name="enableNotificationAppRev" type="checkbox" <?php if ($settings->_enableNotificationAppRev) echo "checked" ?> /></td>
      </tr>

      <!--
        -- SETTINGS - ADVANCED - SERVER
      -->
      <tr ><td><b> <?php printMLText("settings_Server");?></b></td> </tr>
      <tr title="<?php printMLText("settings_coreDir_desc");?>">
        <td><?php printMLText("settings_coreDir");?>:</td>
        <td><input type="text" name="coreDir" value="<?php echo $settings->_coreDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_luceneClassDir_desc");?>">
        <td><?php printMLText("settings_luceneClassDir");?>:</td>
        <td><input type="text" name="luceneClassDir" value="<?php echo $settings->_luceneClassDir ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_extraPath_desc");?>">
        <td><?php printMLText("settings_extraPath");?>:</td>
        <td><input type="text" name="extraPath" value="<?php echo $settings->_extraPath ?>" size="100" /></td>
      </tr>
      <tr title="<?php printMLText("settings_contentOffsetDir_desc");?>">
        <td><?php printMLText("settings_contentOffsetDir");?>:</td>
        <td><input type="text" name="contentOffsetDir" value="<?php echo $settings->_contentOffsetDir ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_maxDirID_desc");?>">
        <td><?php printMLText("settings_maxDirID");?>:</td>
        <td><input type="text" name="maxDirID" value="<?php echo $settings->_maxDirID ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_updateNotifyTime_desc");?>">
        <td><?php printMLText("settings_updateNotifyTime");?>:</td>
        <td><input type="text" name="updateNotifyTime" value="<?php echo $settings->_updateNotifyTime ?>" /></td>
      </tr>
      <tr title="<?php printMLText("settings_maxExecutionTime_desc");?>">
        <td><?php printMLText("settings_maxExecutionTime");?>:</td>
        <td><input type="text" name="maxExecutionTime" value="<?php echo $settings->_maxExecutionTime ?>" /></td>
      </tr>

      <tr ><td><b> <?php printMLText("index_converters");?></b></td> </tr>
<?php
	foreach($settings->_converters as $mimetype=>$cmd) {
?>
      <tr title="<?php echo $mimetype;?>">
        <td><?php echo $mimetype;?>:</td>
        <td><input type="text" name="converters[<?php echo $mimetype;?>]" value="<?php echo $cmd ?>" size="100" /></td>
      </tr>
<?php
	}
?>
    </table>
  </div>

	</form>


<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
