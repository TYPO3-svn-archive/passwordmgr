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
 * Class 'groupmember' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_groupMember extends tx_passwordmgr_model_data {
	protected $data = array(
		'beUserUid' => integer,
		'groupUid' => integer,
		'name' => string,
		'publicKey' => string,
		'certificate' => string
	);

	public function init($beUserUid, $groupUid) {
		$this['beUserUid'] = $beUserUid;
		$this['groupUid'] = $groupUid;
		$this->fetchDetails();
	}

	protected function fetchDetails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'username, tx_passwordmgr_cert',
			'be_users',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'be_users')
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this['name'] = ($row['username']);
		$this['certificate'] = $row['tx_passwordmgr_cert'];
		$this['publicKey'] = tx_passwordmgr_openssl::extractPublicKeyFromCertificate($this['certificate']);
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
			'group_uid' => $this['groupUid']
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
	 * Delete membership of user from group
	 *
	 * @throws Eception if user can not be deleted
	 * @return void
	 */
	public function delete() {
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_passwordmgr_group_be_users_mm',
			'group_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['groupUid'], 'tx_passwordmgr_group_be_users_mm') .
				' AND be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'tx_passwordmgr_group_be_users_mm')
		);
		$affectedRows = (integer)$GLOBALS['TYPO3_DB']->sql_affected_rows();
		if ( $affectedRows == 1 ) {
			tx_passwordmgr_helper::addLogEntry(1, 'deleteMembership', 'Removed user '.$this['beUserUid'].' from group '.$groupUid);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'deleteMembership', 'Wrong number of affected rows removing user '.$this['beUserUid'].' from group '.$groupUid);
			throw new Exception('Error removing user '.$this['beUserUid'].' from group '.$groupUid);
		}
	}
}
?>
