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
		$memberUid = $GLOBALS['BE_USER']->user['uid'];

		// Get list of groups this user is member of
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		$groupList->init($memberUid);

		// Get list of groups where user has edit password rights
		$groupListWithEditRights = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		foreach ( $groupList as $group ) {
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($memberUid, $group['uid']);
			if ( $member['rights'] > 0 ) { // 1 = edit passwords; 2 = group admin
				$groupListWithEditRights->addListItem($group);
			}
		}

		// If user is member of least one group with add / edit rights, render content, else render error page
		if ( count($groupListWithEditRights) > 0 ) {
			$content = $this->addEditPasswordContent($groupListWithEditRights);
		} else { // No group existing for this user
			$content = $this->noGroupContent();
		}
		return($content);
	}

	/**
	 * Build add edit password content
	 *
	 * @param tx_passwordmgr_model_groupList Current group list of be user
	 * @return string html
	 */
	protected function addEditPasswordContent(tx_passwordmgr_model_grouplist $groupList) {
		// Instantiate userData to get preselected group
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');

		// Determine view or edit mode
		if ( $GLOBALS['moduleData']['passwordUid']=='new' || strlen($GLOBALS['moduleData']['passwordUid'])==0 ) {
			$addMode = TRUE;
		} else {
			$addMode = FALSE;
		}

		// Determine selected group
		if ( strlen($GLOBALS['moduleData']['groupUid']) ) {
			// Group is set by post
			$selectedGroupUid = $GLOBALS['moduleData']['groupUid'];
		} elseif ( strlen($userData['defaultGroupUid']) ) {
			// Else choose preselected group from userdata
			$selectedGroupUid = $userData['defaultGroupUid'];
		}
		// Compile group selector
		$groupSelectOptions = array();
		foreach ( $groupList as $group ) {
			// Select first group if none has been set
			if ( strlen($selectedGroupUid)==0 ) {
				$selectedGroupUid = $group['uid'];
			}
			$selected = ($group['uid']==$selectedGroupUid) ? ' selected="selected"' : '';
			$groupSelectOptions[] = '<option value="'.$group['uid'].'"'.$selected.'>'.$group['name'].'</option>';
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

		// Add password strenth inicator
		$this->doc->bodyTagAdditions = 'onload="testPassword(\'\')"';
		$content = '<script src="'.$GLOBALS['BACK_PATH'].$GLOBALS['temp_modPath'].'../res/password_strength.js" type="text/javascript"></script>';

		$content.= '
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
					<td><input type="password" value="" name="DATA[tx_passwordmgr_password1]" size="30" onkeyup="testPassword(this.value);" /></td>
				</tr>
				<tr>
					<td>Retype Password</td>
					<td><input type="password" value="" name="DATA[tx_passwordmgr_password2]" size="30" /></td>
				</tr>
				<tr>
					<td>Password strength</td>
					<td>
						<p id="pwStrength"></p>
					</td>
				</tr>
				<tr>
					<td><input type="submit" name="mysubmit" value="'.($addMode ? 'Add password' : 'Update password').'" onclick="setAction(\''.($addMode ? 'addPassword' : 'editPassword').'\');" /></td>
					<td></td>
				</tr>
			</table>
		';

		return($this->doc->section(($addMode ? 'Add password' : 'Update password'),$content,0,1));
	}
}
?>
