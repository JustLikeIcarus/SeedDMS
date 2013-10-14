<?php
/**
 * Implementation of notifation system using email
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("inc.ClassNotify.php");

/**
 * Class to send email notifications to individuals or groups
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_Email extends SeedDMS_Notify {

	function toIndividual($sender, $recipient, $subject, $message, $params=array()) { /* {{{ */
		if ($recipient->getEmail()=="") return 0;

		if ((!is_object($sender) && strcasecmp(get_class($sender), "SeedDMS_Core_User")) ||
				(!is_object($recipient) && strcasecmp(get_class($recipient), "SeedDMS_Core_User"))) {
			return -1;
		}

		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: ". $sender->getFullName() ." <". $sender->getEmail() .">";
		$headers[] = "Reply-To: ". $sender->getFullName() ." <". $sender->getEmail() .">";

		$lang = $recipient->getLanguage();
		$message = getMLText("email_header", array(), "", $lang)."\r\n\r\n".getMLText($message, $params, "", $lang);
		$message .= "\r\n\r\n".getMLText("email_footer", array(), "", $lang);

		$subject = "=?UTF-8?B?".base64_encode(getMLText($subject, $params, "", $lang))."?=";
		mail($recipient->getEmail(), $subject, $message, implode("\r\n", $headers));

		return true;
	} /* }}} */

	function toGroup($sender, $groupRecipient, $subject, $message, $params=array()) { /* {{{ */
		if ((!is_object($sender) && strcasecmp(get_class($sender), "SeedDMS_Core_User")) ||
				(!is_object($groupRecipient) && strcasecmp(get_class($groupRecipient), "SeedDMS_Core_Group"))) {
			return -1;
		}

		foreach ($groupRecipient->getUsers() as $recipient) {
			$this->toIndividual($sender, $recipient, $subject, $message, $params);
		}

		return true;
	} /* }}} */

	function toList($sender, $recipients, $subject, $message, $params=array()) { /* {{{ */
		if ((!is_object($sender) && strcasecmp(get_class($sender), "SeedDMS_Core_User")) ||
				(!is_array($recipients) && count($recipients)==0)) {
			return -1;
		}

		foreach ($recipients as $recipient) {
			$this->toIndividual($sender, $recipient, $subject, $message, $params);
		}

		return true;
	} /* }}} */

	function sendPassword($sender, $recipient, $subject, $message) { /* {{{ */
		global $settings;

		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: ". $settings->_smtpSendFrom;
		$headers[] = "Reply-To: ". $settings->_smtpSendFrom;

		$subject = "=?UTF-8?B?".base64_encode($this->replaceMarker($subject))."?=";
		return (mail($recipient->getEmail(), $subject, $this->replaceMarker($message), implode("\r\n", $headers)) ? 0 : -1);
	} /* }}} */
}
?>
