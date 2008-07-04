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
 * Class 'movePassword' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_movePassword extends tx_passwordmgr_action_default {
	/**
	 * Move selected password to other group (insert).
	 * Decrypt passphrase and reencrypt for all users of new group
	 *
	 * @return void
	 */
	public function execute() {
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');

		$passwordUid = $userData['selectedPassword'];
		$passphrase = $GLOBALS['moduleData']['passphrase'];

		try {
			// Initialize user object
			$user = t3lib_div::makeInstance('tx_passwordmgr_model_user');
			$user->init($GLOBALS['BE_USER']->user['uid']);

			// Init password
			$password = t3lib_div::makeInstance('tx_passwordmgr_model_password');
			$password->init($passwordUid);

			// Init old group and check user access
			$oldGroup = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$oldGroup->init($password['groupUid']);
			tx_passwordmgr_helper::checkUserAccessToGroup($oldGroup['uid'], $user['uid']);
			tx_passwordmgr_helper::checkMemberAccessModifyPasswordList($oldGroup['uid'], $user['uid']);

			// Init new group and check user access
			$newGroup = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$newGroup->init($GLOBALS['moduleData']['groupUid']);
			tx_passwordmgr_helper::checkUserAccessToGroup($newGroup['uid'], $user['uid']);
			tx_passwordmgr_helper::checkMemberAccessModifyPasswordList($newGroup['uid'], $user['uid']);

			// Init current sslData of password of this user for decryption
			$sslData = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
			$sslData->init($password['uid'], $user['uid']);
			$plaintextPassword = tx_passwordmgr_openssl::decrypt($user['privateKey'], $passphrase, $sslData['key'], $sslData['data']);

			// Remove old sslList
			$oldSslList = $password->getSslList();
			$oldSslList->deleteListItems();

			// Encrypt data for every member of new group
			$newGroupMemberList = $newGroup->getMemberList();
			foreach ( $newGroupMemberList as $member ) {
				$keyAndData = tx_passwordmgr_openssl::encrypt($member['publicKey'], $plaintextPassword);
				$sslData = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
				$sslData['passwordUid'] = $password['uid'];
				$sslData['beUserUid'] = $member['beUserUid'];
				$sslData['timeStamp'] = time();
				$sslData['createDate'] = time();
				$sslData['key'] = $keyAndData['key'];
				$sslData['data'] = $keyAndData['data'];
				$sslData->add();
			}

			// Update group uid of password to new group
			$password['groupUid'] = $newGroup['uid'];
			$password['timeStamp'] = time();
			$password->update();

			// Deselect selected password
			$userData->deselectPassword();
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'movePassword', 'Can not move password');
		}

		$this->defaultView();
	}
}
?>
