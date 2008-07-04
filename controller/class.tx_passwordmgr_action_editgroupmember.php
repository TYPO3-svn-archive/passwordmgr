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
		// Get input data
		$groupUid = $GLOBALS['moduleData']['groupUid'];
		$memberUid = $GLOBALS['moduleData']['groupMemberUid'];
		$rights = $GLOBALS['moduleData']['groupMemberRights'];

		try {
			// Check if be user has access to group
			tx_passwordmgr_helper::checkUserAccessToGroup($groupUid, $GLOBALS['BE_USER']->user['uid']);
			tx_passwordmgr_helper::checkMemberAccessGroupAdmin($groupUid, $GLOBALS['BE_USER']->user['uid']);
			// Check if rights are within valid range
			tx_passwordmgr_helper::checkRightsWithinRange($rights);

			// Initialize new member object
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($memberUid, $groupUid);
			$member['rights'] = $rights;
			$member->update();
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'editGroupMember', 'Can not edit group member');
		}

		$this->defaultView();
	}
}
?>
