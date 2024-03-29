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
 * Class 'addGroup' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_addGroup extends tx_passwordmgr_action_default {
	/**
	 * Add a group and set current be user as new member of this group
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Input check
			tx_passwordmgr_helper::checkLengthGreaterZero($GLOBALS['moduleData']['groupName'], 'Group name');

			// Add group
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group['timeStamp'] = time();
			$group['createDate'] = time();
			$group['cruserUid'] = $GLOBALS['BE_USER']->user['uid'];
			$group['name'] = $GLOBALS['moduleData']['groupName'];
			$groupUid = $group->add();

			// Add self as admin member to group
			$groupMember = t3lib_div::makeInstance('tx_passwordmgr_model_groupMember');
			$groupMember['groupUid'] = $groupUid;
			$groupMember['beUserUid'] = $GLOBALS['BE_USER']->user['uid'];
			$groupMember['rights'] = 2;
			$groupMember->add();
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(2, 'addGroup', $e->getMessage());
		}

		$this->defaultView();
	}
}
?>
