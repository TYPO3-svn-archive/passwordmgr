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
 * Class 'deleteGroup' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_deleteGroup extends tx_passwordmgr_action_default {
	/**
	 * Delete a group
	 * - Check access rights of current BE user
	 * - Delete group
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Get input data
			$userUid = $GLOBALS['BE_USER']->user['uid'];
			$groupUid = $GLOBALS['moduleData']['groupUid'];

			// Check if user has admin rights in group
			tx_passwordmgr_helper::checkMemberRights( $userUid, $groupUid, 2 );

			// Instantiate group object and set its uid
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group['uid'] = $groupUid;

			// Delete group
			$group->delete();
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(2, 'deleteGroup', $e->getMessage());
		}

		$this->defaultView();
	}
}
?>
