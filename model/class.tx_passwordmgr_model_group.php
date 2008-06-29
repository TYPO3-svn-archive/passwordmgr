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
 * Class 'group' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_group extends tx_passwordmgr_model_data {
	/**
	 * @var array Group details
	 */
	protected $data = array(
		'uid' => integer, // uid of this group
		'timeStamp' => integer, // last change date
		'createDate' => integer, // create date of group
		'cruserUid' => integer, // uid of user that added this group
		'name' => string // name of this group
	);

	/**
	 * @var tx_passwordmgr_memberList Instance of the member list
	 */
	private $memberList;

	/**
	 * @var tx_passwordmgr_passwordList Instance of the password list
	 */
	private $passwordList;

	/**
	 * Initialize group with a groupUid and fetch group details
	 *
	 * @param integer id of group
	 * @return void
	 */
	public function init($uid) {
		$this['uid'] = $uid;
		$this->fetchDetails();
	}

	/**
	 * Fetch group details and set in data array
	 *
	 * @return void
	 */
	protected function fetchDetails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tstamp, crdate, cruser_id, name',
			'tx_passwordmgr_group',
			'uid=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'],'tx_passwordmgr_group')
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this['name'] = $row['name'];
		$this['timeStamp'] = $row['timeStamp'];
		$this['createDate'] = $row['crdate'];
		$this['cruserUid'] = $row['cruser_id'];
	}

	/**
	 * Return list of group members
	 *
	 * @return tx_passwordmgr_memberList
	 */
	public function getMemberList() {
		if ( !is_object($this->memberList) ) {
			$this->memberList = t3lib_div::makeInstance('tx_passwordmgr_model_groupMemberList');
			$this->memberList->init($this['uid']);
		}
		return($this->memberList);
	}

	/**
	 * Return list of passwords of this group
	 *
	 * @return tx_passwordmgr_passwordList
	 */
	public function getPasswordList() {
		if ( !is_object($this->passwordList) ) {
			$this->passwordList = t3lib_div::makeInstance('tx_passwordmgr_model_passwordList');
			$this->passwordList->init($this['uid'],$GLOBALS['BE_USER']->user['uid']);
		}
		return($this->passwordList);
	}

	/**
	 * Add new group to database
	 *
	 * @throws Exception if group can not be added
	 * @return integer uid of new group
	 */
	public function add() {
		$data = array(
			'tstamp' => $this['timeStamp'],
			'crdate' => $this['createDate'],
			'cruser_id' => $this['cruserUid'],
			'name' => $this['name'],
		);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_passwordmgr_group',
			$data
		);
		if ( $res ) {
			$this['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id($res);
			tx_passwordmgr_helper::addLogEntry(1, 'addGroup', 'Added Group '.$data['name']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'addGroup', 'Error adding group '.$data['name']);
			throw new Exception('Error adding group '.$data['name'].' to db');
		}
		return($this['uid']);
	}

	/**
	 * Update group details in database
	 *
	 * @throws Exception if update failed
	 * @return void
	 */
	public function update() {
		$data = array(
			'tstamp' => $this['timeStamp'],
			'crdate' => $this['createDate'],
			'cruser_id' => $this['cruserUid'],
			'name' => $this['name'],
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_passwordmgr_group',
			'uid=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'],'tx_passwordmgr_group'),
			$data
		);
		$affectedRows = (integer)$GLOBALS['TYPO3_DB']->sql_affected_rows();
		if ( $affectedRows == 1 ) {
			tx_passwordmgr_helper::addLogEntry(1, 'editGroup', 'Update group '.$data['name'].' uid '.$this['uid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'editGroup', 'Can not update group '.$this['name']);
			throw new Exception ('Error updating group '.$data['name']);
		}
	}

	/**
	 * Delete group in database
	 *
	 * @throws Exception if delete failed
	 * @return void
	 */
	public function delete() {
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_passwordmgr_group',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'], 'tx_passwordmgr_group')
		);
		$affectedRows = (integer)$GLOBALS['TYPO3_DB']->sql_affected_rows();
		if ( $affectedRows == 1 ) {
			tx_passwordmgr_helper::addLogEntry(1, 'deleteGroup', 'Removed group '.$this['uid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'deleteGroup', 'Wrong number of affected rows removing group '.$this['uid']);
			throw new Exception('Error deleting group '.$this['uid']);
		}
	}
}
?>
