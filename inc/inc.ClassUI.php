<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
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

require_once('inc.ClassUI_Default.php');
require_once('inc.ClassViewCommon.php');

/* $theme was possibly set in inc.Authentication.php */
if (!isset($theme) || strlen($theme)==0) {
	$theme = $settings->_theme;
}
if (strlen($theme)==0) {
	$theme="blue";
}

/* Sooner or later the parent will be removed, because all output will
 * be done by the new view classes.
 */
class UI extends UI_Default {
	/**
	 * Create a view from a class in the given theme
	 *
	 * This method will check for a class file in the theme directory
	 * and returns an instance of it.
	 *
	 * @param string $theme theme
	 * @param string $class name of view class
	 * @param array $params parameter passed to constructor of view class
	 * @return object an object of a class implementing the view
	 */
	static function factory($theme, $class='', $params=array()) { /* {{{ */
		global $settings, $session;
		if(!$class) {
			$class = 'Bootstrap';
			$classname = "SeedDMS_Bootstrap_Style";
		} else {
			$classname = "SeedDMS_View_".$class;
		}
		$filename = "../views/".$theme."/class.".$class.".php";
		if(file_exists($filename)) {
			require($filename);
			$view = new $classname($params, $theme);
			/* Set some configuration parameters */
			$view->setParam('refferer', $_SERVER['REQUEST_URI']);
			$view->setParam('session', $session);
			$view->setParam('sitename', $settings->_siteName);
			$view->setParam('rootfolderid', $settings->_rootFolderID);
			$view->setParam('disableselfedit', $settings->_disableSelfEdit);
			$view->setParam('enableusersview', $settings->_enableUsersView);
			$view->setParam('enablecalendar', $settings->_enableCalendar);
			$view->setParam('calendardefaultview', $settings->_calendarDefaultView);
			$view->setParam('enablefullsearch', $settings->_enableFullSearch);
			$view->setParam('enablelargefileupload', $settings->_enableLargeFileUpload);
			$view->setParam('printdisclaimer', $settings->_printDisclaimer);
			$view->setParam('footnote', $settings->_footNote);
			$view->setParam('logfileenable', $settings->_logFileEnable);
			$view->setParam('expandfoldertree', $settings->_expandFolderTree);
			$view->setParam('enablefoldertree', $settings->_enableFolderTree);
			$view->setParam('enablelanguageselector', $settings->_enableLanguageSelector);
			$view->setParam('workflowmode', $settings->_workflowMode);
			$view->setParam('partitionsize', $settings->_partitionSize);
			return $view;
		}
		return null;
	} /* }}} */

	static function getStyles() { /* {{{ */
		global $settings;

		$themes = array();
		$path = $settings->_rootDir . "views/";
		$handle = opendir($path);

		while ($entry = readdir($handle) ) {
			if ($entry == ".." || $entry == ".")
				continue;
			else if (is_dir($path . $entry) || is_link($path . $entry))
				array_push($themes, $entry);
		}
		closedir($handle);
		return $themes;
	} /* }}} */

	static function exitError($pagetitle, $error) {
		global $theme;
		$tmp = 'ErrorDlg';
		$view = UI::factory($theme, $tmp);
		$view->exitError($pagetitle, $error);
	}
}

?>
