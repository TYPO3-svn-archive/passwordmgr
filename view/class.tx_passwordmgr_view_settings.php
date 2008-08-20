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
 * Class 'settings' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_settings extends tx_passwordmgr_view_default {
	/**
	 * Build content for the settings page
	 *
	 * @return string html
	 */
	protected function innerContent() {
		// Get current user Data
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');

		// Compile default group selector
		$defaultGroupSelectOptions = array();
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		$groupList->init($GLOBALS['BE_USER']->user['uid']);
		foreach ( $groupList as $group ) {
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($GLOBALS['BE_USER']->user['uid'], $group['uid']);
			if ( $member['rights'] > 1 ) {
				$selected = $userData['defaultGroupUid'] == $group['uid'] ? ' selected="selected"' : '';
				$defaultGroupSelectOptions[] = '<option value="' . $group['uid'] . '" ' . $selected . '>' . $group['name'] . '</option>';
			}
		}
		$defaultGroupSelectContent = '
			<select name="DATA[tx_passwordmgr_groupUid]">
				' . implode($defaultGroupSelectOptions) . '
			</select>
		';

		$content = '
			<table border="0" cellpadding="2" cellspacing="1">
				<tr>
					<td>Default group for new passwords</td>
					<td>' . $defaultGroupSelectContent . '</td>
				</tr>
				<tr>
					<td>Display log only if an error occurred</td>
					<td><input type="checkbox" name="DATA[tx_passwordmgr_displayLogOnErrorOnly]" ' . ($userData['displayLogOnErrorOnly'] ? 'checked="checked"' : '')  . '/></td>
				</tr>
				<tr>
					<td><input type="submit" value="Save settings" name="mysubmit" onclick="setAction(\'updateSettings\');" /></td>
					<td></td>
				</tr>
			</table>
		';

		return($this->doc->section('Settings', $content, 0, 1));
	}
}
?>
