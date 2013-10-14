<?php
/**
 * Implementation of UpdateDocument2 view
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
 * Class which outputs the html page for UpdateDocument2 view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UpdateDocument2 extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");

		$this->contentHeading(getMLText("update_document") . ": " . htmlspecialchars($document->getName()));
		$this->contentContainerStart();

		if ($document->isLocked()) {

			$lockingUser = $document->getLockingUser();
			
			print "<table><tr><td class=\"warning\">";
			
			printMLText("update_locked_msg", array("username" => htmlspecialchars($lockingUser->getFullName()), "email" => htmlspecialchars($lockingUser->getEmail())));
			
			if ($lockingUser->getID() == $user->getID())
				printMLText("unlock_cause_locking_user");
			else if ($document->getAccessMode($user) == M_ALL)
				printMLText("unlock_cause_access_mode_all");
			else
			{
				printMLText("no_update_cause_locked");
				print "</td></tr></table>";
				$this->contentContainerEnd();
				$this->htmlEndPage();
				exit;
			}

			print "</td></tr></table><br>";
		}

		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $document->getApproversList();

		$this->printUploadApplet('../op/op.UpdateDocument2.php', array('folderid'=>$folder->getId(), 'documentid'=>$document->getId()), 1, array('version_comment'=>1));

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
