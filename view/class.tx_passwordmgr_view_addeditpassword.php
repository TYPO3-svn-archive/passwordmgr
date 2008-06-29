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
 * Class 'addEditPassword' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_addEditPassword extends tx_passwordmgr_view_default {
	/**
	 * Build content for the add or edit password page
	 *
	 * @return string html
	 */
	protected function innerContent() {
		// Determine view or edit mode
		if ( $GLOBALS['moduleData']['passwordUid']=='new' || strlen($GLOBALS['moduleData']['passwordUid'])==0 ) {
			$addMode = TRUE;
		} else {
			$addMode = FALSE;
		}

		// Compile group selector
		$selectedGroupUid = $GLOBALS['moduleData']['groupUid'];
		$groupSelectOptions = array();
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		$groupList->init($GLOBALS['BE_USER']->user['uid']);
		foreach ( $groupList as $group ) {
			if ( strlen($selectedGroupUid)==0 ) {
				$selectedGroupUid = $group['uid'];
			}
			$selected = ($group['uid']==$selectedGroupUid) ? 'selected="selected"' : '';
			$groupSelectOptions[] = '<option value="'.$group['uid'].'" '.$selected.'>'.$group['name'].'</option>';
		}
		$groupSelectContent = '
			<select name="DATA[tx_passwordmgr_groupUid]" onchange="setFieldValue(\'passwordUid\', \'new\'); document.passwordmgr.submit();">
				'.implode($groupSelectOptions).'
			</select>
		';

		// Compile password selector
		$passwordSelectOptions = array();
		$selected = $addMode ? ' selected="selected"' : '';
		$passwordSelectOptions[] = '<option value="new"'.$selected.'>New password</option>';
		$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
		$group->init($selectedGroupUid);
		$passwordList = $group->getPasswordList();
		foreach ( $passwordList as $password ) {
			$selected = ($password['uid']==$GLOBALS['moduleData']['passwordUid']) ? 'selected="selected"' : '';
			$passwordSelectOptions[] = '<option value="'.$password['uid'].'"'.$selected.'>'.$password['name'].'</option>';
		}
		$passwordSelectContent = '
			<select name="DATA[tx_passwordmgr_passwordUid]" onchange="document.passwordmgr.submit();">
				'.implode($passwordSelectOptions).'
			</select>
		';

		// Get current password values if in edit mode
		if ( !$addMode ) {
			$password = t3lib_div::makeInstance('tx_passwordmgr_model_password');
			$password->init($GLOBALS['moduleData']['passwordUid']);
		}

		$content = '
			<table border="0" cellpadding="2" cellspacing="1">
				<tr>
					<td>Group</td>
					<td>'.$groupSelectContent.'</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>'.$passwordSelectContent.'</td>
				</tr>
				<tr>
					<td>Name</td>
					<td><input type="text" value="'.($addMode ? '' : $password['name']).'" name="DATA[tx_passwordmgr_passwordName]" size="30" /></td>
				</tr>
				<tr>
					<td>Link</td>
					<td><input type="text" value="'.($addMode ? '' : $password['link']).'" name="DATA[tx_passwordmgr_passwordLink]" size="30" /></td>
				</tr>
				<tr>
					<td>User</td>
					<td><input type="text" value="'.($addMode ? '' : $password['user']).'" name="DATA[tx_passwordmgr_passwordUser]" size="30" /></td>
				</tr>
				<tr>
					<td>Password'.($addMode ? '' : '<br />Leave blank if not changed').'</td>
					<td><input type="text" value="" name="DATA[tx_passwordmgr_password1]" size="30" /></td>
				</tr>
				<tr>
					<td>Retype Password</td>
					<td><input type="text" value="" name="DATA[tx_passwordmgr_password2]" size="30" /></td>
				</tr>
				<tr>
					<td><input type="submit" name="mysubmit" value="'.($addMode ? 'Add password' : 'Update password').'" onclick="setAction(\''.($addMode ? 'addPassword' : 'editPassword').'\');" /></td>
					<td></td>
				</tr>
			</table>
			<input type="hidden" name="DATA[tx_passwordmgr_action]" value="" />
		';

		return($this->doc->section(($addMode ? 'Add password' : 'Update password'),$content,0,1));
	}
}
?>
