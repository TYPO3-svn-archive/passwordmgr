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
 * Class 'deleteGroupMember' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_deleteGroupMember extends tx_passwordmgr_action_default {
	/**
	 * Delete a member and his ssl data for all passwords from a group
	 *
	 * @return void
	 */
	public function execute() {
		$groupMemberUid = $GLOBALS['moduleData']['groupMemberUid'];

		// Instantiate group object and set its uid
		$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
		$group['uid'] = $GLOBALS['moduleData']['groupUid'];

	
		try {
			// Check if user is allowed to access this group
			tx_passwordmgr_helper::checkUserAccessToGroup($group['uid']);

			// Remove ssl data of all passwords of this group for this user
			$passwordList = $group->getPasswordList();
			foreach ( $passwordList as $password ) {
				$sslDataOfGroupMember = t3lib_div::makeInstance('tx_passwordmgr_model_ssldata');
				$sslDataOfGroupMember->init($password['uid'], $groupMemberUid);
				$sslDataOfGroupMember->delete();
			}

			// Delete groupmembership
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupMember');
			$member->init($groupMemberUid, $group['uid']);
			$member->delete();
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'deleteGroup', 'Error deleting group '.$group['uid']);
		}

		$this->defaultView();
	}
}
?>
