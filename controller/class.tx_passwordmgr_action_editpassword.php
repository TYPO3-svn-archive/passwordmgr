<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Christian Kuhn <lolli@schwarzbu.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class 'editPassword' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_editPassword extends tx_passwordmgr_action_default {
	/**
	 * Edit password data
	 * - Check if a password field changed an update
	 * - Check if ssldata changed an update for all group member
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Input checks
			tx_passwordmgr_helper::checkLengthGreaterZero($GLOBALS['moduleData']['passwordName'], 'name');
			tx_passwordmgr_helper::checkUserAccessToGroup($GLOBALS['moduleData']['groupUid']);

			// Get current password data
			$password = t3lib_div::makeInstance('tx_passwordmgr_model_password');
			$password->init($GLOBALS['moduleData']['passwordUid']);

			// Set changed password fields
			$changed = FALSE;
			if ( $GLOBALS['moduleData']['passwordName'] != $password['name'] ) {
				$changed = TRUE;
				$password['name'] = $GLOBALS['moduleData']['passwordName'];
			}
			if ( $GLOBALS['moduleData']['passwordLink'] != $password['link'] ) {
				$changed = TRUE;
				$password['link'] = $GLOBALS['moduleData']['passwordLink'];
			}
			if ( $GLOBALS['moduleData']['passwordUser'] != $password['user'] ) {
				$changed = TRUE;
				$password['user'] = $GLOBALS['moduleData']['passwordUser'];
			}

			// Update password
			if ( $changed ) {
				$password['timeStamp'] = time();
				$password->update();
			}

			// Update ssldata for every groupmember if password changed
			if ( strlen($GLOBALS['moduleData']['password1'])>0 ) {
				tx_passwordmgr_helper::checkIdenticalPasswords($GLOBALS['moduleData']['password1'], $GLOBALS['moduleData']['password2']);
				$sslList = $password->getSslList();
				foreach ( $sslList as $ssl ) {
					$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
					$member->init($ssl['beUserUid'],$GLOBALS['moduleData']['groupUid']);
					$keyAndData = tx_passwordmgr_openssl::encrypt($member['publicKey'], $GLOBALS['moduleData']['password1']);
					$ssl['timeStamp'] = time();
					$ssl['key'] = $keyAndData['key'];
					$ssl['data'] = $keyAndData['data'];
					$ssl->update();
				}
			}
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'editPassword', 'Can not update password '.$password['name']);
		}

		$this->defaultView();
	}
}
?>
