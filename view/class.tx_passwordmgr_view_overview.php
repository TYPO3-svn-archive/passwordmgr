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
 * Class 'overview' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_overview extends tx_passwordmgr_view_default {
	/**
	 * Build overview page
	 *
	 * @return string
	 */
	protected function innerContent() {
		// Get data
		$backPath = $GLOBALS['BACK_PATH'];
		$user =  t3lib_div::makeInstance('tx_passwordmgr_model_user');
		$user->init($GLOBALS['BE_USER']->user['uid']);
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		$groupList->init($user['uid']);
		$openItems = t3lib_div::makeInstance('tx_passwordmgr_model_openItems');

		$groupContent = array();
		foreach ( $groupList as $group ) {
			$isOpenGroup = $openItems->isOpen('group',$group['uid']);

			$memberContent = array();
			$passwordContent = array();

			if ( $isOpenGroup ) {
				// Member list
				$isOpenMember = $openItems->isOpen('member',$group['uid']);
				$memberList = $group->getMemberList();
				$memberContent[] = '
					<tr>
						<td></td>
						<td class="bgColor5">
							'.$this->foldIcon($group['uid'], 'member', $isOpenMember).'
						</td>
						<td class="bgColor5" colspan="2">Member: '.count($memberList).'</td>
						<td class="bgColor5">
							<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/new_el.gif').' onclick="setFieldValue(\'groupUid\', \''.$group['uid'].'\'); setFieldValue(\'view\', \'addGroupMember\'); document.passwordmgr.submit();" alt="Add new member" title="Add new member" />
						</td>
					</tr>
				';
				if ( $isOpenMember ) {
					$bgColor = 'bgColor4';
					foreach ( $memberList as $member ) {
						$memberContent[] = '
							<tr>
								<td colspan="2"></td>
								<td class="'.$bgColor.'" colspan="2">'.$member['name'].'</td>
								<td class="'.$bgColor.'">
									<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/garbage.gif').' onclick="setAction(\'deleteGroupMember\'); setFieldValue(\'groupUid\', \''.$group['uid'].'\'); setFieldValue(\'groupMemberUid\', \''.$member['beUserUid'].'\'); document.passwordmgr.submit();" alt="Delete member" title="Delete member" />
								</td>
							</tr>
						';
						$bgColor = $bgColor=='bgColor4' ? 'bgColor6' : 'bgColor4';
					}
				} // End of if member list is open
	
				// Password list
				$isOpenPassword = $openItems->isOpen('password',$group['uid']);
				$passwordList = $group->getPasswordList();
				$passwordContent[] = '
					<tr>
						<td></td>
						<td class="bgColor5">
							'.$this->foldIcon($group['uid'], 'password', $isOpenPassword).'
						</td>
						<td class="bgColor5" colspan="2">Passwords: '.count($passwordList).'</td>
						<td class="bgColor5">
							<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/new_el.gif').' onclick="setFieldValue(\'groupUid\', \''.$group['uid'].'\'); setFieldValue(\'view\', \'addEditPassword\'); document.passwordmgr.submit();" alt="Add new password" title="Add new password" />
						</td>
					</tr>
				';
				if ( $isOpenPassword ) {
					$bgColor = 'bgColor4';
					foreach ( $passwordList as $password ) {
						if ( strlen($password['link'])>0 ) {
							$nameAndLinkContent = '<a target="_blank" href="'.$password['link'].'">'.$password['name'].'</a>';
						} else {
							$nameAndLinkContent = $password['name'];
						}
						$userAndPasswordContent = 'User: '.$password['user'];
						if ( isset($this->data['plaintextPassword'][$password['uid']]) ) {
							$userAndPasswordContent .= '<br />Password: '.$this->data['plaintextPassword'][$password['uid']];
						}
						$passwordContent[] = '
							<tr>
								<td colspan="2"></td>
								<td class="'.$bgColor.'">'.$nameAndLinkContent.'</td>
								<td class="'.$bgColor.'">'.$userAndPasswordContent.'</td>
								<td class="'.$bgColor.'">
									<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/edit2.gif').' onclick="setFieldValue(\'view\', \'addEditPassword\'); setFieldValue(\'groupUid\', \''.$group['uid'].'\'); setFieldValue(\'passwordUid\', \''.$password['uid'].'\'); document.passwordmgr.submit();" alt="Edit password" title="Edit password" />
									<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/garbage.gif').' onclick="setAction(\'deletePassword\'); setFieldValue(\'passwordUid\', \''.$password['uid'].'\'); document.passwordmgr.submit();" alt="Delete password" title="Delete passwod" />
									<img style="cursor: pointer;" src="../res/decrypted.png" onclick="setAction(\'decryptPassword\'); setFieldValue(\'passwordUid\', \''.$password['uid'].'\'); passphrasePopUp();" alt="Decrypt password" title="Decrypt password" />
								</td>
							</tr>
						';
						$bgColor = $bgColor=='bgColor4' ? 'bgColor6' : 'bgColor4';
					}
				} // End of if password list is open
			} // End of if group is open

			// Content of this group
			$groupContent[] = '
				<tr>
					<td class="bgColor2">
							'.$this->foldIcon($group['uid'], 'group', $isOpenGroup).'
					</td>
					<td class="bgColor2" colspan="3">Group: '.$group['name'].'</td>
					<td class="bgColor2">
						<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/edit2.gif').' onclick="setFieldValue(\'view\', \'addEditGroup\'); setFieldValue(\'groupUid\', \''.$group['uid'].'\'); document.passwordmgr.submit();" alt="Edit group" title="Edit group" />
						<img style="cursor: pointer;" '.t3lib_iconWorks::skinImg($backPath, 'gfx/garbage.gif').' onclick="setAction(\'deleteGroup\'); setFieldValue(\'groupUid\', \''.$group['uid'].'\'); document.passwordmgr.submit();" alt="Delete group" title="Delete group" />
						'.$this->addNewGroupIcon().'
					</td>
				</tr>
				'.implode($memberContent).'
				'.implode($passwordContent).'
				<tr>
					<td colspan="5"></td>
				</tr>
			';
		} // End of for each group
		
		$content = '
			<table border="0" cellpadding="2" cellspacing="1">
				'.implode($groupContent).'
			</table>
		';

		return($this->doc->section('Groups and passwords',$content,0,1));
	}

	/**
	 * Add "new group" icon to docheader
	 * Overwrites default view method
	 *
	 * @return array Markers and content in the docheader
	 */
	protected function getDocHeaderButtons() {
		$docHeaderButtons = parent::getDocHeaderButtons();
		$docHeaderButtons['NEWGROUP'] = $this->addNewGroupIcon();
		return($docHeaderButtons);
	}

	/**
	 * Helper method to build a + / - icon to fold / unfold a group, memberlist or passwordlist
	 *
	 * @param integer uid of group
	 * @param string 'group', 'member' or 'password'
	 * @param bool TRUE if a type is open, minus icon is shown then, plus instead
	 * @return string icon html
	 */
	protected function foldIcon($groupUid, $type, $isOpen) {
		$foldImage = $isOpen ? 'gfx/selectnone.gif' : 'gfx/selectall.gif';
		$newFoldState = $isOpen ? '0' : '1';
		$foldText = $isOpen ? 'Collapse' : 'Expand';
		$imageTag = '<img
			style="cursor: pointer;"
			'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $foldImage).'
			onclick="
				setAction(\'fold\');
				setFieldValue(\'foldType\', \''.$type.'\');
				setFieldValue(\'foldState\', \''.$newFoldState.'\');
				setFieldValue(\'groupUid\', \''.$groupUid.'\');
				document.passwordmgr.submit();"
			alt="'.$foldText.'"
			title="'.$foldText.'"
			/>
		';
		return ($imageTag);				
	}

	/**
	 * Helper method to return html code for a new group icon
	 *
	 * @return string html
	 */
	protected function addNewGroupIcon() {
		$backPath = $GLOBALS['BACK_PATH'];
		$imageTag = '<img
			style="cursor: pointer;"
			'.t3lib_iconWorks::skinImg($backPath, 'gfx/new_el.gif').'
			onclick="
				setFieldValue(\'view\', \'addEditGroup\');
				document.passwordmgr.submit();"
			alt="Add new group"
			title="Add new group"
			/>';
		return ($imageTag);
	}
}
?>
