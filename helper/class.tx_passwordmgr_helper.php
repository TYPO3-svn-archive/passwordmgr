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
	/**
	 * Check if length of a given string is > 0
	 *
	 * @throws Exception if string length not > 0
	 * @return void
	 */
	public static function checkLengthGreaterZero( $string, $name ) {
		if ( !(strlen($string) > 0) ) {
			throw new Exception('Input was required but not given. Required input: ' . $name);
		}
	}

	/**
	 * Verify a user is not member of a group
	 *
	 * @throws Exception if user is member of group
	 * @return void
	 */
	public static function checkUserNotMemberOfGroup( $userUid, $groupUid ) {
		try {
			$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');
			$member->init($userUid, $groupUid);
		} catch ( Exception $e ) {
			// groupMember->init() throws an Exception if user is not member of group, this should happen
			return;
		}
		throw new Exception('User is already member of group. group / member: ' . $groupUid . ' ' . $userUid);
	}

	/**
	 * Check if a user has sufficient rights in a group
	 *
	 * @param integer Uid of member
	 * @param integer Uid of group
	 * @param integer Minimum group rights: 0=view, 1=edit, 2=group admin
	 * @throws Exception if rights are not sufficient
	 * @return void
	 */
	public static function checkMemberRights( $userUid, $groupUid, $rights ) {
		$member = t3lib_div::makeInstance('tx_passwordmgr_model_groupmember');

		// Initialize group member, throws exception if user not member of group
		$member->init($userUid, $groupUid);

		// Check rights
		switch( $rights ) {
			case 0: // Initialized member has at least view rights
			break;
			case 1: // Check for edit rights
				if ( $member['rights'] < 1 ) {
					throw new Exception ('Insufficient rights of user in group. user / group / rights / expected rights: ' . $userUid . ' ' . $groupUid . ' ' . $member['rights'] . ' ' . $rights);
				}
			break;
			case 2: // Check for admin rights
				if ( $member['rights'] < 2 ) {
					throw new Exception ('Insufficient rights of user in group. user / group / rights / expected rights: ' . $userUid . ' ' . $groupUid . ' ' . $member['rights'] . ' ' . $rights);
				}
			break;
			default: // Should not happen
				throw new Exception ('Insufficient rights of user in group. user / group / rights / expected rights: ' . $userUid . ' ' . $groupUid . ' ' . $member['rights'] . ' ' . $rights);
			break;
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
			throw new Exception('Value of member rights wrong. rights: ' . $rights);
		}
	}

	/**
	 * Check if string one is identical to string two
	 *
	 * @param string
	 * @param string
	 * @throws Exception if strings are not identical
	 * @return void
	 */
	public static function checkIdenticalPasswords( $pw1, $pw2 ) {
		if ( strcmp($pw1, $pw2) ) {
			throw new Exception('Given passwords do not match');
		}
	}

	/**
	 * Check if length of a string is greater than 5
	 *
	 * @throws Exception if string length is shorter than 6
	 * @return void
	 */
	public static function checkPasswordMinimumLength( $pw ) {
		if ( strlen($pw) < 6 ) {
			throw new Exception('Password not long enough');
		}
	}

	/**
	 * Initialize new log object with given information and add object to log list
	 *
	 * @param integer priority
	 * @param string module identifier
	 * @param string log message
	 * @return void
	 */
	public static function addLogEntry($priority, $module, $message) {
		$log = t3lib_div::makeInstance('tx_passwordmgr_model_log');
		$log['priority'] = $priority;
		$log['module'] = $module;
		$log['message'] = $message;
		$GLOBALS['logList']->addListItem($log);
	}

	/**
	 * Calculate a random string of characters
	 *
	 * @param integer length of random string
	 * @return string random string
	 */
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
