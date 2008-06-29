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
 * Class 'editGroup' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_editGroup extends tx_passwordmgr_action_default {
	public function execute() {
		try {
			// Input checks
			tx_passwordmgr_helper::checkLengthGreaterZero($GLOBALS['moduleData']['groupName'], 'name');
			tx_passwordmgr_helper::checkUserAccessToGroup($GLOBALS['moduleData']['groupUid']);

			// Get current group data
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group->init($GLOBALS['moduleData']['groupUid']);

			// Set changed group fields
			$changed = FALSE;
			if ( $GLOBALS['moduleData']['groupName'] != $group['name'] ) {
				$changed = TRUE;
				$group['name'] = $GLOBALS['moduleData']['groupName'];
			}

			// Update group
			if ( $changed ) {
				$group['timeStamp'] = time();
				$group->update();
			}
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'editPassword', 'Can not update password '.$password['name']);
		}

		$this->defaultView();
	}
}
?>
