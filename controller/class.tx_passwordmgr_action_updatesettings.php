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
 * Class 'updateSettings' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_updateSettings extends tx_passwordmgr_action_default {
	/**
	 * Update settings in user uC if changed:
	 * - "Display log on error only"
	 *
	 * @return void
	 */
	public function execute() {
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');

		// Update log error flag
		if ( $GLOBALS['moduleData']['displayLogOnErrorOnly'] && !$userData['displayLogOnErrorOnly'] ) {
			$userData->changeDisplayLogOnErrorOnly(1);
		} elseif ( !$GLOBALS['moduleData']['displayLogOnErrorOnly'] && $userData['displayLogOnErrorOnly'] ) {
			$userData->changeDisplayLogOnErrorOnly(0);
		}

		// Update default group
		if ( $GLOBALS['moduleData']['groupUid'] != $userData['defaultGroupUid'] ) {
			$userData->updateDefaultGroupUid($GLOBALS['moduleData']['groupUid']);
		}

		// Update default rights
		if ( $GLOBALS['moduleData']['groupMemberRights'] != $userData['defaultRights'] ) {
			$userData->updateDefaultRights($GLOBALS['moduleData']['groupMemberRights']);
		}

		$this->defaultView();
	}
}
?>
