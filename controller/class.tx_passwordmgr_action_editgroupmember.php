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
 * Class 'editGroupMember' for the 'passwordmgr' extension.
 * Update rights of existing group member
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_editGroupMember extends tx_passwordmgr_action_default {
	/**
	 * Edit rights of a group member
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Get input data
			$userUid = $GLOBALS['BE_USER']->user['uid'];
			$groupUid = $GLOBALS['moduleData']['groupUid'];
			$memberUid = $GLOBALS['moduleData']['groupMemberUid'];
			$newRights = $GLOBALS['moduleData']['groupMemberRights'];

			// Check if user has admin rights in group
			tx_passwordmgr_helper::checkMemberRights( $userUid, $groupUid, 2 );

			// Check if new rights value is within valid range
			tx_passwordmgr_helper::checkRightsWithinRange($newRights);

			// Initialize new member object
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($memberUid, $groupUid);

			// Only take admin rights from a member if there is another member with admin rights
			$numberOfGroupAdmins = 0;
			if ( $member['rights'] == 2 && $newRights < 2 ) {
				$memberList = t3lib_div::makeInstance('tx_passwordmgr_model_groupMemberList');
				$memberList->init($groupUid);
				foreach ( $memberList as $groupMember ) {
					if ( $groupMember['rights'] == 2 ) {
						$numberOfGroupAdmins++;
					}
				}
			} else {
				$numberOfGroupAdmins = 2;
			}

			// Edit member
			if ( $numberOfGroupAdmins >= 2 ) {
				$member['rights'] = $newRights;
				$member->update();
			} else {
				tx_passwordmgr_helper::addLogEntry(2, 'editGroupMember', 'Can not remove group admin rights from last admin member');
			}
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(2, 'editGroupMember', $e->getMessage());
		}

		$this->defaultView();
	}
}
?>
