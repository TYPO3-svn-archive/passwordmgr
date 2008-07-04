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
 * Class 'helper' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_helper {
	public static function checkLengthGreaterZero( $string, $name ) {
		if ( strlen($string)>0 ) {
			return TRUE;
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'Input check', "Length of $name not greater than zero");
			throw new Exception("String $name not greater than 0");
		}
	}

	public static function checkUserNotMemberOfGroup( $groupUid, $userUid=FALSE ) {
		$userUid ? $targetUserUid=$userUid : $targetUserUid=$GLOBALS['BE_USER']->user['uid'];
		try {
			tx_passwordmgr_helper::checkUserAccessToGroup($groupUid, $targetUserUid);
		} catch ( Exception $e ) {
			return TRUE;
		}
		throw new Exception('User is member of group');
	}

	public static function checkUserAccessToGroup( $groupUid, $userUid=FALSE ) {
		$groupInGroupList = FALSE;
		$groupList = t3lib_div::makeInstance('tx_passwordmgr_model_grouplist');
		$userUid ? $groupList->init($userUid) : $groupList->init($GLOBALS['BE_USER']->user['uid']);
		foreach ( $groupList as $group ) {
			if ( $group['uid'] == (integer)$groupUid ) {
				$groupInGroupList = TRUE;
			}
		}
		if ( !$groupInGroupList ) {
			throw new Exception("Access violation of user to group");
		}
		return ($groupInGroupList);
	}

	/**
	 * Checks if group member is allowed to edit / add passwords of a group (rights >=1)
	 *
	 * @param integer Uid of group to check
	 * @param integer Uid of member
	 * @throws Exception if rights not sufficient
	 */
	public static function checkMemberAccessModifyPasswordList($groupUid, $memberUid) {
		$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
		$member->init($memberUid, $groupUid);
		if ( !($member['rights']>0) ) {
			tx_passwordmgr_helper::addLogEntry(3, 'modifyPasswordRightsCheck', 'Insufficient rights to edit this password');
			throw new Exception('Insufficient rights to edit this password');
		}
	}

	/**
	 * Checks if rights value >=0 and <=2
	 *
	 * @param integer rights
	 * @throws Exception if rights not within range
	 */
	public static function checkRightsWithinRange($rights) {
		if ( $rights<0 || $rights>2 || !strlen($rights)>0 ) {
			tx_passwordmgr_helper::addLogEntry(3, 'rightsWithinRange', 'Value of member rights wrong');
			throw new Exception('Value of member rights wrong');
		}
	}

	public static function checkIdenticalPasswords( $pw1, $pw2 ) {
		if ( strcmp($pw1, $pw2) ) {
			tx_passwordmgr_helper::addLogEntry(3, 'identicalPasswords', 'Passwords do not match');
			throw new Exception('Passwords do not match');
		}
	}

	public static function checkPasswordMinimumLength( $pw ) {
		if ( strlen($pw) < 6 ) {
			tx_passwordmgr_helper::addLogEntry(3, 'passwordMinimumLength', 'Password not long enough');
			throw new Exception('Password not long enough');
		}
	}

	public static function addLogEntry($priority, $module, $message) {
		$log = t3lib_div::makeInstance('tx_passwordmgr_model_log');
		$log['priority'] = $priority;
		$log['module'] = $module;
		$log['message'] = $message;
		$GLOBALS['logList']->addListItem($log);
	}

	public static function getRandomString($length) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$string = '';
		for ( $i=0; $i<$length; $i++ ) {
			$randNum = mt_rand(0, strlen($characters)-1);
			$string .= substr($characters, $randNum, 1);
		}
		return($string);
	}
}
?>
