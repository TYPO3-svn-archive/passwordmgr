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
 * Class 'addGroupMember' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_addGroupMember extends tx_passwordmgr_action_default {
	/**
	 * Add a be_user to a group
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Get input data
			$userUid = $GLOBALS['BE_USER']->user['uid'];
			$groupUid = $GLOBALS['moduleData']['groupUid'];
			$newMemberUid = $GLOBALS['moduleData']['groupMemberUid'];
			$rights = $GLOBALS['moduleData']['groupMemberRights'];
			$passphrase = $GLOBALS['moduleData']['passphrase'];

			// Check if user has admin rights in group
			tx_passwordmgr_helper::checkMemberRights( $userUid, $groupUid, 2 );

			// Check if new member is not already member of group
			tx_passwordmgr_helper::checkUserNotMemberOfGroup( $newMemberUid, $groupUid );

			// Check if rights are within valid range
			tx_passwordmgr_helper::checkRightsWithinRange($rights);

			// Initialize current user object for decryption of passwords
			$user = t3lib_div::makeInstance('tx_passwordmgr_model_user');
			$user->init($userUid);
			$user['publicKey'] = tx_passwordmgr_openssl::extractPublicKeyFromCertificate($user['certificate']);

			// Initialize new member object
			$newMember = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$newMember['groupUid'] = $groupUid;
			$newMember['beUserUid'] = $newMemberUid;
			$newMember['rights'] = $rights;

			// Add new member to group
			$newMember->add();

			// Re init new member to get his public key for encryption
			$newMember->init($newMemberUid, $groupUid);

			// Get list of passwords of this group
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group['uid'] = $groupUid;
			$passwordList = $group->getPasswordList();

			// Add ssl data for each password of new user
			foreach ( $passwordList as $password ) {
				// Decrypt ssl data of password of current user
				$sslData = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
				$sslData->init($password['uid'], $user['uid']);
				$plaintextData = tx_passwordmgr_openssl::decrypt($user['privateKey'], $passphrase, $sslData['key'], $sslData['data']);
				unset($sslData);

				// Encrypt data for new member
				$newMemberKeyAndData = tx_passwordmgr_openssl::encrypt($newMember['publicKey'], $plaintextData);
				unset($plaintextData);

				// Add ssl data for new member
				$sslDataNewMember = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
				$sslDataNewMember['passwordUid'] = $password['uid'];
				$sslDataNewMember['beUserUid'] = $newMemberUid;
				$sslDataNewMember['timeStamp'] = time();
				$sslDataNewMember['createDate'] = time();
				$sslDataNewMember['key'] = $newMemberKeyAndData['key'];
				$sslDataNewMember['data'] = $newMemberKeyAndData['data'];
				$sslDataNewMember->add();
			}
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(2, 'addGroupMember', $e->getMessage());
		}

		$this->defaultView();
	}
}
?>
