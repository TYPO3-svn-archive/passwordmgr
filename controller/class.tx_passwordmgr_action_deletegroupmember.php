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
	 * Delete a member from a group
	 * - Check access of current BE user
	 * - Check member rights and ensure that not the last admin member is deleted
	 * - Delete member from group
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
			tx_passwordmgr_helper::checkMemberAccessGroupAdmin($group['uid'], $GLOBALS['BE_USER']->user['uid']);

			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupMember');
			$member->init($groupMemberUid, $group['uid']);

			// Only delete an admin member if there is another member with group admin rights
			$numberOfGroupAdmins = 0;
			if ( $member['rights'] == 2 ) {
				$memberList = $group->getMemberList();
				foreach ( $memberList as $groupMember ) {
					if ( $groupMember['rights'] == 2 ) {
						$numberOfGroupAdmins++;
					}
				}
			} else {
				$numberOfGroupAdmins = 2;
			}

			// Remove ssl data of all passwords of this group for this user
			if ( $numberOfGroupAdmins >=2 ) {
				// Delete groupmembership
				$member->delete();
			} else {
				tx_passwordmgr_helper::addLogEntry(3, 'deleteGroupMemebr', 'Can not delete last group admin from group '.$group['uid']);
			}

		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'deleteGroupMember', 'Error deleting group member '.$group['uid']);
		}

		$this->defaultView();
	}
}
?>
