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
 * Class 'addEditGroup' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_addEditGroup extends tx_passwordmgr_view_default {
	/**
	 * Build content for the add / edit group page
	 *
	 * @return string html
	 */
	protected function innerContent() {
		// Determine add or edit mode
		if ( $GLOBALS['moduleData']['groupUid']=='new' || strlen($GLOBALS['moduleData']['groupUid'])==0 ) {
			$addMode = TRUE;
		} else {
			$addMode = FALSE;
		}

		// Compile group selector
		$groupSelectOptions = array();
		$selected = $addMode ? ' selected="selected"' : '';
		$groupSelectOptions[] = '<option value="new"'.$selected.'>New group</option>';
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		$groupList->init($GLOBALS['BE_USER']->user['uid']);
		$selectedGroupUid = $GLOBALS['moduleData']['groupUid'];
		foreach ( $groupList as $group ) {
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($GLOBALS['BE_USER']->user['uid'], $group['uid']);
			if ( $member['rights'] > 1 ) {
				$selected = ($group['uid']==$selectedGroupUid) ? 'selected="selected"' : '';
				$groupSelectOptions[] = '<option value="'.$group['uid'].'" '.$selected.'>'.$group['name'].'</option>';
			}
		}
		$groupSelectContent = '
			<select name="DATA[tx_passwordmgr_groupUid]" onchange="document.passwordmgr.submit();">
				'.implode($groupSelectOptions).'
			</select>
		';

		// Get current group values in edit mode
		if ( !$addMode ) {
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group->init($GLOBALS['moduleData']['groupUid']);
		}

		$content = '
			<table border="0" cellpadding="2" cellspacing="1">
				<tr>
					<td>Group</td>
					<td>'.$groupSelectContent.'</td>
				</tr>
				<tr>
					<td>Groupname</td>
					<td><input type="text" value="'.($addMode ? '' : $group['name']).'" name="DATA[tx_passwordmgr_groupName]" size="30" /></td>
				</tr>
				<tr>
					<td><input type="submit" value="'.($addMode ? 'Add group' : 'Update group').'" name="mysubmit" onclick="setAction(\''.($addMode ? 'addGroup' : 'editGroup').'\');" /></td>
					<td></td>
				</tr>
			</table>
		';

		return($this->doc->section(($addMode ? 'Add group' : 'Update group'),$content,0,1));
	}
}
?>
