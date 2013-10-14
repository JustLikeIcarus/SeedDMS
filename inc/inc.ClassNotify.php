<?php
/**
 * Abstract class of notifation system
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
 * Abstract class of notification systems
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010 Uwe Steinmann
 * @version    Release: @package_version@
 */
abstract class SeedDMS_Notify {
	/* User sending the notification
	 * Will only be used if the sender of one of the notify methods
	 * is not set
	 */
	protected $sender;

	abstract function toIndividual($sender, $recipient, $subject, $message, $params=array());
	abstract function toGroup($sender, $groupRecipient, $subject, $message, $params=array());
	abstract function toList($sender, $recipients, $subject, $message, $params=array());

	function replaceMarker($text) {
		global $settings;

		return(str_replace(
			array('###SITENAME###', '###HTTP_ROOT###', '###URL_PREFIX###'),
			array($settings->_siteName, $settings->_httpRoot, "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot),
			$text));
	}

	function setSender($user) {
		$this->sender = $user;
	}
}
?>
