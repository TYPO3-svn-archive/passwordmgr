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
 * Class 'addGroupMember' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_addGroupMember extends tx_passwordmgr_view_default {
	/**
	 * Build content for the add a member to a group page
	 *
	 * @return string html
	 */
	protected function innerContent() {
		$selectedGroupUid = $GLOBALS['moduleData']['groupUid'];
		if ( strlen($selectedGroupUid)>0 ) {
			$currentGroupUid = $selectedGroupUid;
		}

		$groupSelectOptions = array();
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_groupList');
		$groupList->init($GLOBALS['BE_USER']->user['uid']);
		foreach ( $groupList as $group ) {
			$selected = ($group['uid']==$selectedGroupUid) ? 'selected="selected"' : '';
			if ( !isset($currentGroupUid) ) {
				$currentGroupUid = $group['uid'];
			}
			$groupSelectOptions[] = '<option value="'.$group['uid'].'" '.$selected.'>'.$group['name'].'</option>';
		}
		$groupSelectContent = '
			<select name="DATA[tx_passwordmgr_groupUid]" onchange="document.passwordmgr.submit();">
				'.implode($groupSelectOptions).'
			</select>
		';

		// Current members of this group
		$memberList = t3lib_div::makeInstance('tx_passwordmgr_model_groupMemberList');
		$memberList->init($currentGroupUid);
		// All Be users
		$userList = t3lib_div::makeInstance('tx_passwordmgr_model_userList');
		$userList->init();
		// Calculate possible new member select options of this group
		$newMemberSelectOptions = array();
		foreach ( $userList as $user ) {
			$userInList = FALSE;
			foreach ($memberList as $member) {
				if ( $user['uid'] == $member['beUserUid'] ) {
					$userInList = TRUE;
				}
			}
			if ( !$userInList ) {
				$newMemberSelectOptions[] = '<option value="'.$user['uid'].'">'.$user['name'].'</option>';
			}
		}
		$newMemberSelectContent = '
			<select name="DATA[tx_passwordmgr_groupMemberUid]">' .
				implode($newMemberSelectOptions) . '
			</select>
		';

		$content = '
			<table border="0" cellpadding="2" cellspacing="1">
				<tr>
					<td>Group</td>
					<td>'.$groupSelectContent.'</td>
				</tr>
				<tr>
					<td>New member</td>
					<td>'.$newMemberSelectContent.'</td>
				</tr>
				<tr>
					<td><input type="submit" name="mysubmit" value="Add member" onclick="setAction(\'addGroupMember\'); passphrasePopUp(); return false;" /></td>
					<td></td>
				</tr>
			</table>
			<input type="hidden" name="DATA[tx_passwordmgr_action]" value="" />
			<input type="hidden" name="DATA[tx_passwordmgr_passphrase]" value="" />
			<input type="hidden" name="DATA[tx_passwordmgr_passwordUid]" value="" />
		';

		return($this->doc->section('Add member',$content,0,1));
	}
}
?>
