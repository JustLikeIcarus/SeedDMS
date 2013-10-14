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

include("../inc/inc.Settings.php");
include("../inc/inc.LogInit.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Utils.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!isset($_POST["documentid"]) || !is_numeric($_POST["documentid"]) || intval($_POST["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$documentid = $_POST["documentid"];
$document = $dms->getDocument($documentid);

if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

if ($document->getAccessMode($user) < M_ALL) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

if (!isset($_POST["version"]) || !is_numeric($_POST["version"]) || intval($_POST["version"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
}

$version = $_POST["version"];
$content = $document->getContentByVersion($version);

if (!is_object($content)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
}

// control status.
$overallStatus = $content->getStatus();
if ($overallStatus["status"]==S_REJECTED || $overallStatus["status"]==S_OBSOLETE ) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("cannot_assign_invalid_state"));
}

// Retrieve a list of all users and groups that have review / approve
// privileges.
$docAccess = $document->getReadAccessList();
$accessIndex = array("i"=>array(), "g"=>array());
foreach ($docAccess["users"] as $i=>$da) {
	$accessIndex["i"][$da->getID()] = $i;
}
foreach ($docAccess["groups"] as $i=>$da) {
	$accessIndex["g"][$da->getID()] = $i;
}

// Retrieve list of currently assigned reviewers and approvers, along with
// their latest status.
$reviewStatus = $content->getReviewStatus();
$approvalStatus = $content->getApprovalStatus();
// Index the review results for easy cross-reference with the Approvers List.
$reviewIndex = array("i"=>array(), "g"=>array());
foreach ($reviewStatus as $i=>$rs) {
	if ($rs["status"]!=-2) {
		if ($rs["type"]==0) {
			$reviewIndex["i"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
		}
		else if ($rs["type"]==1) {
			$reviewIndex["g"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
		}
	}
}
// Index the approval results for easy cross-reference with the Approvers List.
$approvalIndex = array("i"=>array(), "g"=>array());
foreach ($approvalStatus as $i=>$rs) {
	if ($rs["status"]!=-2) {
		if ($rs["type"]==0) {
			$approvalIndex["i"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
		}
		else if ($rs["type"]==1) {
			$approvalIndex["g"][$rs["required"]] = array("status"=>$rs["status"], "idx"=>$i);
		}
	}
}

// Get the list of proposed reviewers, stripping out any duplicates.
$pIndRev = (isset($_POST["indReviewers"]) ? array_values(array_unique($_POST["indReviewers"])) : array());
$pGrpRev = (isset($_POST["grpReviewers"]) ? array_values(array_unique($_POST["grpReviewers"])) : array());
foreach ($pIndRev as $p) {
	if (is_numeric($p)) {
		if (isset($accessIndex["i"][$p])) {
			// Proposed reviewer is on the list of possible reviewers.
			if (!isset($reviewIndex["i"][$p])) {
				// Proposed reviewer is not a current reviewer, so add as a new
				// reviewer.
				$res = $content->addIndReviewer($docAccess["users"][$accessIndex["i"][$p]], $user);
				$unm = $docAccess["users"][$accessIndex["i"][$p]]->getFullName();
				$uml = $docAccess["users"][$accessIndex["i"][$p]]->getEmail();
				
				switch ($res) {
					case 0:
						// Send an email notification to the new reviewer.
						if($settings->_enableNotificationAppRev) {
							if ($notifier) {
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("review_request_email");
								$message = getMLText("review_request_email")."\r\n";
								$message .= 
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$content->_version."\r\n";
								
								$notifier->toIndividual($user, $docAccess["users"][$accessIndex["i"][$p]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_assigned"));
						break;
					case -4:
						// email error
						break;
				}
			}
			else {
				// Remove reviewer from the index of possible reviewers. If there are
				// any reviewers left over in the list of possible reviewers, they
				// will be removed from the review process for this document revision.
				unset($reviewIndex["i"][$p]);
			}
		}
	}
}
if (count($reviewIndex["i"]) > 0) {
	foreach ($reviewIndex["i"] as $rx=>$rv) {
		if ($rv["status"] == 0) {
			// User is to be removed from the review list.
			if (!isset($docAccess["users"][$accessIndex["i"][$rx]])) {
				// User does not have any review privileges for this document
				// revision or does not exist.
				$queryStr = "INSERT INTO `tblDocumentReviewLog` (`reviewID`, `status`, `comment`, `date`, `userID`) ".
					"VALUES ('". $reviewStatus[$rv["idx"]]["reviewID"] ."', '-2', '".getMLText("removed_reviewer")."', NOW(), '". $user->getID() ."')";
				$res = $db->getResult($queryStr);
			}
			else {
				$res = $content->delIndReviewer($docAccess["users"][$accessIndex["i"][$rx]], $user);
				$unm = $docAccess["users"][$accessIndex["i"][$rx]]->getFullName();
				$uml = $docAccess["users"][$accessIndex["i"][$rx]]->getEmail();
				switch ($res) {
					case 0:
						// Send an email notification to the reviewer.
						if($settings->_enableNotificationAppRev) {
							if ($notifier) {
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("review_deletion_email");
								$message = getMLText("review_deletion_email")."\r\n";
								$message .= 
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".			
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";
								
								$notifier->toIndividual($user, $docAccess["users"][$accessIndex["i"][$rx]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_removed"));
						break;
					case -4:
						// email error
						break;
				}
			}
		}
	}
}
foreach ($pGrpRev as $p) {
	if (is_numeric($p)) {
		if (isset($accessIndex["g"][$p])) {
			// Proposed reviewer is on the list of possible reviewers.
			if (!isset($reviewIndex["g"][$p])) {
				// Proposed reviewer is not a current reviewer, so add as a new
				// reviewer.
				$res = $content->addGrpReviewer($docAccess["groups"][$accessIndex["g"][$p]], $user);
				$gnm = $docAccess["groups"][$accessIndex["g"][$p]]->getName();
				switch ($res) {
					case 0:
						// Send an email notification to the new reviewer.
						if($settings->_enableNotificationAppRev) {
							if ($notifier) {
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("review_request_email");
								$message = getMLText("review_request_email")."\r\n";
								$message .=
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$content->_version."\r\n";
							
								$notifier->toGroup($user, $docAccess["groups"][$accessIndex["g"][$p]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_assigned"));
						break;
					case -4:
						// email error
						break;
				}
			}
			else {
				// Remove reviewer from the index of possible reviewers.
				unset($reviewIndex["g"][$p]);
			}
		}
	}
}
if (count($reviewIndex["g"]) > 0) {
	foreach ($reviewIndex["g"] as $rx=>$rv) {
		if ($rv["status"] == 0) {
			// Group is to be removed from the review list.
			if (!isset($docAccess["groups"][$accessIndex["g"][$rx]])) {
				// Group does not have any review privileges for this document
				// revision or does not exist.
				$queryStr = "INSERT INTO `tblDocumentReviewLog` (`reviewID`, `status`, `comment`, `date`, `userID`) ".
					"VALUES ('". $reviewStatus[$rv["idx"]]["reviewID"] ."', '-2', '".getMLText("removed_reviewer")."', NOW(), '". $user->getID() ."')";
				$res = $db->getResult($queryStr);
			}
			else {
				$res = $content->delGrpReviewer($docAccess["groups"][$accessIndex["g"][$rx]], $user);
				$gnm = $docAccess["groups"][$accessIndex["g"][$rx]]->getName();
				switch ($res) {
					case 0:
						// Send an email notification to the review group.
						if($settings->_enableNotificationAppRev) {
							if ($notifier) {
							
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("review_deletion_email");
								$message = getMLText("review_deletion_email")."\r\n";
								$message .= 
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".			
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

								$notifier->toGroup($user, $docAccess["groups"][$accessIndex["g"][$rx]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_removed"));
						break;
					case -4:
						// email error
						break;
				}
			}
		}
	}
}

// Get the list of proposed approvers, stripping out any duplicates.
$pIndApp = (isset($_POST["indApprovers"]) ? array_values(array_unique($_POST["indApprovers"])) : array());
$pGrpApp = (isset($_POST["grpApprovers"]) ? array_values(array_unique($_POST["grpApprovers"])) : array());
foreach ($pIndApp as $p) {
	if (is_numeric($p)) {
		if (isset($accessIndex["i"][$p])) {
			// Proposed approver is on the list of possible approvers.
			if (!isset($approvalIndex["i"][$p])) {
				// Proposed approver is not a current approver, so add as a new
				// approver.
				$res = $content->addIndApprover($docAccess["users"][$accessIndex["i"][$p]], $user);
				$unm = $docAccess["users"][$accessIndex["i"][$p]]->getFullName();
				$uml = $docAccess["users"][$accessIndex["i"][$p]]->getEmail();
				switch ($res) {
					case 0:
						// Send an email notification to the new approver.
						if($settings->_enableNotificationAppRev) {
							if ($overallStatus["status"]!=0 && $notifier) {
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("approval_request_email");
								$message = getMLText("approval_request_email")."\r\n";
								$message .= 
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".			
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$content->_version."\r\n";

								$notifier->toIndividual($user, $docAccess["users"][$accessIndex["i"][$p]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_assigned"));
						break;
					case -4:
						// email error
						break;
				}
			}
			else {
				// Remove approver from the index of possible approvers.
				unset($approvalIndex["i"][$p]);
			}
		}
	}
}
if (count($approvalIndex["i"]) > 0) {
	foreach ($approvalIndex["i"] as $rx=>$rv) {
		if ($rv["status"] == 0) {
			// User is to be removed from the approvers list.
			if (!isset($docAccess["users"][$accessIndex["i"][$rx]])) {
				// User does not have any approval privileges for this document
				// revision or does not exist.
				$queryStr = "INSERT INTO `tblDocumentApproveLog` (`approveID`, `status`, `comment`, `date`, `userID`) ".
					"VALUES ('". $approvalStatus[$rv["idx"]]["approveID"] ."', '-2', '".getMLText("removed_approver")."', NOW(), '". $user->getID() ."')";
				$res = $db->getResult($queryStr);
			}
			else {
				$res = $content->delIndApprover($docAccess["users"][$accessIndex["i"][$rx]], $user);
				$unm = $docAccess["users"][$accessIndex["i"][$rx]]->getFullName();
				$uml = $docAccess["users"][$accessIndex["i"][$rx]]->getEmail();
				switch ($res) {
					case 0:
						// Send an email notification to the approver.
						if($settings->_enableNotificationAppRev) {
							if ($notifier) {
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("approval_deletion_email");
								$message = getMLText("approval_deletion_email")."\r\n";
								$message .= 
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".			
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

								$notifier->toIndividual($user, $docAccess["users"][$accessIndex["i"][$rx]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_removed"));
						break;
					case -4:
						// email error
						break;
				}
			}
		}
	}
}
foreach ($pGrpApp as $p) {
	if (is_numeric($p)) {
		if (isset($accessIndex["g"][$p])) {
			// Proposed approver is on the list of possible approvers.
			if (!isset($approvalIndex["g"][$p])) {
				// Proposed approver is not a current approver, so add as a new
				// approver.
				$res = $content->addGrpApprover($docAccess["groups"][$accessIndex["g"][$p]], $user);
				$gnm = $docAccess["groups"][$accessIndex["g"][$p]]->getName();
				switch ($res) {
					case 0:
						// Send an email notification to the new approver.
						if($settings->_enableNotificationAppRev) {
							if ($overallStatus["status"]!=0 && $notifier) {
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("approval_request_email");
								$message = getMLText("approval_request_email")."\r\n";
								$message .=
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".			
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$content->_version."\r\n";

								$notifier->toGroup($user, $docAccess["groups"][$accessIndex["g"][$p]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_assigned"));
						break;
					case -4:
						// email error
						break;
				}
			}
			else {
				// Remove approver from the index of possible approvers.
				unset($approvalIndex["g"][$p]);
			}
		}
	}
}
if (count($approvalIndex["g"]) > 0) {
	foreach ($approvalIndex["g"] as $rx=>$rv) {
		if ($rv["status"] == 0) {
			// User is to be removed from the approvers list.
			if (!isset($docAccess["groups"][$accessIndex["g"][$rx]])) {
				// Group does not have any approval privileges for this document
				// revision or does not exist.
				
				$queryStr = "INSERT INTO `tblDocumentApproveLog` (`approveID`, `status`, `comment`, `date`, `userID`) ".
					"VALUES ('". $approvalStatus[$rv["idx"]]["approveID"] ."', '-2', '".getMLText("removed_approver")."', NOW(), '". $user->getID() ."')";
				$res = $db->getResult($queryStr);
			}
			else {
				$res = $content->delGrpApprover($docAccess["groups"][$accessIndex["g"][$rx]], $user);
				$gnm = $docAccess["groups"][$accessIndex["g"][$rx]]->getName();
				switch ($res) {
					case 0:
						// Send an email notification to the approval group.
						if($settings->_enableNotificationAppRev) {
							if ($notifier) {
							
								$subject = "###SITENAME###: ".$document->getName().", v.".$content->_version." - ".getMLText("approval_deletion_email");
								$message = getMLText("approval_deletion_email")."\r\n";
								$message .= 
									getMLText("document").": ".$document->getName()."\r\n".
									getMLText("version").": ".$content->_version."\r\n".
									getMLText("comment").": ".$content->getComment()."\r\n".
									getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".			
									"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

								$notifier->toGroup($user, $docAccess["groups"][$accessIndex["g"][$rx]], $subject, $message);
							}
						}
						break;
					case -1:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("internal_error"));
						break;
					case -2:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
						break;
					case -3:
						UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("reviewer_already_removed"));
						break;
					case -4:
						// email error
						break;
				}
			}
		}
	}
}



$content->verifyStatus(false,$user);

add_log_line("?documentid=".$documentid);
header("Location:../out/out.DocumentVersionDetail.php?documentid=".$documentid."&version=".$version);

?>
