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
 * Class 'groupMember' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_groupMember extends tx_passwordmgr_model_data {
	/**
	 * @var array Member details
	 */
	protected $data = array(
		'beUserUid' => integer,
		'groupUid' => integer,
		'name' => string,
		'publicKey' => string,
		'certificate' => string,
		'rights' => integer // 0 = view passwords; 1 = + edit / add passwords; 2 = + add / edit / delete member, edit / delete group
	);

	/**
	 * Initialize group member object
	 *
	 * @param integer id of group member
	 * @param integer id of group
	 * @return void
	 */
	public function init($beUserUid, $groupUid) {
		$this['beUserUid'] = $beUserUid;
		$this['groupUid'] = $groupUid;
		$this->fetchDetails();
	}

	/**
	 * Fetch group member details
	 *
	 * @throws Exception if membership not found
	 * @return void
	 */
	protected function fetchDetails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'be_users.uid AS uid, be_users.username AS username, be_users.tx_passwordmgr_cert AS cert, tx_passwordmgr_group_be_users_mm.rights AS rights',
			'be_users, tx_passwordmgr_group_be_users_mm',
			'tx_passwordmgr_group_be_users_mm.be_users_uid=be_users.uid' . // Join Constraint
				' AND be_users.uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'be_users') .
				' AND tx_passwordmgr_group_be_users_mm.group_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['groupUid'], 'tx_passwordmgr_group_be_users_mm')
		);
		if ( $GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1 ) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$this['name'] = $row['username'];
			$this['certificate'] = $row['cert'];
			$this['publicKey'] = tx_passwordmgr_openssl::extractPublicKeyFromCertificate($this['certificate']);
			$this['rights'] = $row['rights'];
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'groupMemberFetchDetails', 'User '.$this['beUserUid'].' not member of group '.$this['groupUid']);
			throw new Exception ('Error getting group member details '.$this['beUserUid'].' group '.$this['groupUid']);
		}
	}

	/**
	 * Add a member to group
	 *
	 * @throws Exception if user can not be added
	 * @return void
	 */
	public function add() {
		$data = array (
			'be_users_uid' => $this['beUserUid'],
			'group_uid' => $this['groupUid'],
			'rights' => $this['rights']
		);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_passwordmgr_group_be_users_mm',
			$data
		);
		if ( $res ) {
			tx_passwordmgr_helper::addLogEntry(1, 'addMembership', 'Added user '.$data['be_users_uid'].' to group '.$data['group_uid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'addMembership', 'Can not add user '.$data['be_users_uid'].' to group '.$data['group_uid']);
			throw new Exception ('Error adding user '.$data['be_users_uid'].' to group');
		}
	}

	/**
	 * Update member rights
	 *
	 * @throws Exception if update failed
	 * @return void
	 */
	public function update() {
		$data = array(
			'rights' => $this['rights']
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_passwordmgr_group_be_users_mm',
			'group_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['groupUid'], 'tx_passwordmgr_group_be_users_mm') .
				' AND be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'tx_passwordmgr_group_be_users_mm'),
			$data
		);
		$this->checkAffectedRows('updateGroupMember', 1);
		tx_passwordmgr_helper::addLogEntry(1, 'updateGroupMember', 'Updated member rights of member '.$this['beUserUid'].' of group '.$this['groupUid']);
	}

	/**
	 * Delete membership of user from group
	 * - Delete sslData of every password of this group for this member
	 * - Delete member from group
	 *
	 * @throws Eception if user can not be deleted
	 * @return void
	 */
	public function delete() {
		// Get list of passwords of this group
		$passwordList = t3lib_div::makeInstance('tx_passwordmgr_model_passwordList');
		$passwordList->init($this['groupUid']);

		// Delete sslData of member of passwords in this group
		foreach ( $passwordList as $password ) {
			$sslDataOfMember = t3lib_div::makeInstance('tx_passwordmgr_model_ssldata');
			$sslDataOfMember->init($password['uid'], $this['beUserUid']);
			$sslDataOfMember->delete();
		}

		// Delete membership
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_passwordmgr_group_be_users_mm',
			'group_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['groupUid'], 'tx_passwordmgr_group_be_users_mm') .
				' AND be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'tx_passwordmgr_group_be_users_mm')
		);
		$this->checkAffectedRows('deleteGroupMember', 1);
		tx_passwordmgr_helper::addLogEntry(1, 'deleteGroupMember', 'Removed user '.$this['beUserUid'].' from group '.$groupUid);
	}
}
?>
