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
 * Class 'addEditGroupMember' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_addEditGroupMember extends tx_passwordmgr_view_default {
	/**
	 * Build content for add / edit group membership page
	 *
	 * @return string html
	 */
	protected function innerContent() {
		$memberUid = $GLOBALS['BE_USER']->user['uid'];

		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_groupList');
		$groupList->init($memberUid);

		// Get list of groups where user has admin rights
		$groupListWithEditRights = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		foreach ( $groupList as $group ) {
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($memberUid, $group['uid']);
			if ( $member['rights'] > 1 ) { // 2 = group admin
				$groupListWithEditRights->addListItem($group);
			}
		}

		// If group list contains at least one group, render content, else render error page
		if ( count($groupListWithEditRights) > 0 ) {
			$content = $this->addEditGroupMemberContent($groupList);
		} else { // No group existing for this user
			$content = $this->noGroupContent();
		}
		return($content);
	}

	/**
	 * Build add / edit group member content
	 *
	 * @param tx_passwordmgr_model_groupList Current group list of be user
	 * @return string html
	 */
	protected function addEditGroupMemberContent(tx_passwordmgr_model_groupList $groupList) {
		// Instantiate userData to get default rights for new members
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');

		// Post vars
		$selectedGroupUid = $GLOBALS['moduleData']['groupUid'];
		$selectedMemberUid = $GLOBALS['moduleData']['groupMemberUid'];

		// Group selector
		if ( strlen($selectedGroupUid)>0 ) {
			$currentGroupUid = $selectedGroupUid;
		}
		$groupSelectOptions = array();
		foreach ( $groupList as $group ) {
			$selected = ($group['uid']==$selectedGroupUid) ? ' selected="selected"' : '';
			if ( !isset($currentGroupUid) ) {
				$currentGroupUid = $group['uid'];
			}
			$groupSelectOptions[] = '<option value="'.$group['uid'].'"'.$selected.'>'.$group['name'].'</option>';
		}
		$groupSelectContent = '
			<select name="DATA[tx_passwordmgr_groupUid]" onchange="document.passwordmgr.submit();">
				'.implode($groupSelectOptions).'
			</select>
		';

		// Current members of this group
		$memberList = t3lib_div::makeInstance('tx_passwordmgr_model_groupMemberList');
		$memberList->init($currentGroupUid);
		// All available be users
		$userList = t3lib_div::makeInstance('tx_passwordmgr_model_userList');
		$userList->init();

		// Member selector of this group
		$memberSelectOptions = array();
		$editMode = FALSE;
		$selected = '';
		foreach ( $userList as $user ) {
			// Find current members
			$userInMemberList = FALSE;
			foreach ($memberList as $member) {
				if ( $user['uid'] == $member['beUserUid'] ) {
					$userInMemberList = TRUE;
				}
			}

			// Set memberUid to first element if none has been selected
			if ( strlen($selectedMemberUid)==0) {
				$selectedMemberUid = $user['uid'];
			}

			// Check if this member was selected
			$selected = ($selectedMemberUid == $user['uid']) ? ' selected="selected"' : '';

			// Determine add or edit mode and set selector option
			if ( $userInMemberList ) {
				if ( $selectedMemberUid == $user['uid'] ) {
					$editMode = TRUE;
				}
				$memberSelectOptions[] = '<option value="'.$user['uid'].'"'.$selected.'>'.$user['name'].' (edit)</option>';
			} else {
				$memberSelectOptions[] = '<option value="'.$user['uid'].'"'.$selected.'>'.$user['name'].' (new)</option>';
			}
		}
		$memberSelectContent = '
			<select name="DATA[tx_passwordmgr_groupMemberUid]" onchange="document.passwordmgr.submit();">' .
				implode($memberSelectOptions) . '
			</select>
		';

		// Determine current rights in edit mode
		$selected = array(
			'0' => '',
			'1' => '',
			'2' => ''
		);
		if ( $editMode ) {
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupMember');
			$member->init($selectedMemberUid, $currentGroupUid);
			$selected[$member['rights']] = ' selected="selected"';
		} else {
			$selected[$userData['defaultRights']] = ' selected="selected"';
		}

		// Member rights selector
		$memberRightsContent = '
			<select name="DATA[tx_passwordmgr_groupMemberRights]">
				<option value="0"'.$selected[0].'>View passwords</option>
				<option value="1"'.$selected[1].'>View / add / edit passwords</option>
				<option value="2"'.$selected[2].'>Group admin</option>
			</select>
		';

		$content = '
			<table border="0" cellpadding="2" cellspacing="1">
				<tr>
					<td>Group</td>
					<td>'.$groupSelectContent.'</td>
				</tr>
				<tr>
					<td>Member</td>
					<td>'.$memberSelectContent.'</td>
				</tr>
				<tr>
					<td>Member rights</td>
					<td>'.$memberRightsContent.'</td>
				</tr>
				<tr>
					<td><input type="submit" name="mysubmit" value="'.($editMode ? 'Edit member rights' : 'Add member').'" onclick="setAction(\''.($editMode ? 'editGroupMember' : 'addGroupMember').'\'); '.($editMode ? 'document.passwordmgr.submit();' : 'passphrasePopUp();').' return false;" /></td>
					<td></td>
				</tr>
			</table>
		';

		return($this->doc->section('Add member',$content,0,1));
	}
}
?>
