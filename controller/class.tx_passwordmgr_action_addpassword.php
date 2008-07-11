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
 * Class 'addPassword' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_addPassword extends tx_passwordmgr_action_default {
	/**
	 * Add a password to a group
	 * - Add password if user rights are sufficient for this group
	 * - Calculate ssldata for each member of the group and add to db
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Get input data
			$userUid =  $GLOBALS['BE_USER']->user['uid'];
			$groupUid = $GLOBALS['moduleData']['groupUid'];

			// Input checks
			tx_passwordmgr_helper::checkLengthGreaterZero($GLOBALS['moduleData']['passwordName'], "name");
			tx_passwordmgr_helper::checkLengthGreaterZero($GLOBALS['moduleData']['password1'], "password");
			tx_passwordmgr_helper::checkIdenticalPasswords($GLOBALS['moduleData']['password1'], $GLOBALS['moduleData']['password2']);

			// Check if user has add password rights in this group
			tx_passwordmgr_helper::checkMemberRights( $userUid, $groupUid, 1 );

			// Add password
			$password = t3lib_div::makeInstance('tx_passwordmgr_model_password');
			$password['groupUid'] = $GLOBALS['moduleData']['groupUid'];
			$password['timeStamp'] = time();
			$password['createDate'] = time();
			$password['name'] = $GLOBALS['moduleData']['passwordName'];
			$password['link'] = $GLOBALS['moduleData']['passwordLink'];
			$password['user'] = $GLOBALS['moduleData']['passwordUser'];
			$password->add();

			// Get list of members of this group
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group['uid'] = $groupUid;
			$memberList = $group->getMemberList();

			// Encrypt password for each member and add ssl data to db
			foreach ( $memberList as $member ) {
				$keyAndData = tx_passwordmgr_openssl::encrypt($member['publicKey'], $GLOBALS['moduleData']['password1']);
				$sslData = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
				$sslData['passwordUid'] = $password['uid'];
				$sslData['beUserUid'] = $member['beUserUid'];
				$sslData['timeStamp'] = time();
				$sslData['createDate'] = time();
				$sslData['key'] = $keyAndData['key'];
				$sslData['data'] = $keyAndData['data'];
				$sslData->add();
			}
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(2, 'addPassword', $e->getMessage());
		}

		$this->defaultView();
	}
}
?>
