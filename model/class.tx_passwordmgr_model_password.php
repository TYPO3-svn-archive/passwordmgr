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
 * Class 'password' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_password extends tx_passwordmgr_model_data {
	/**
	 * @var array password details
	 */
	protected $data = array(
		'uid' => integer,
		'groupUid' => integer,
        'timeStamp' => integer, // last change date
		'createDate' => integer, // create date of group
		'name' => string,
		'link' => string,
		'user' => string
	);

	/**
	 * @var tx_passwordmgr_sslList Instance of Ssl data list object
	 */
	private $sslList;

	/**
	 * Initialize password
	 *
	 * @param integer id of password
	 */
	public function init($uid) {
		$this['uid'] = $uid;
		$this->fetchDetails();
	}

	/**
	 * Fetch password details and set in data array
	 *
	 * @return void
	 */
	protected function fetchDetails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tstamp, crdate, group_uid, name, link, user',
			'tx_passwordmgr_password',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'],'tx_passwordmgr_password')
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this['timeStamp'] = $row['tstamp'];
		$this['createDate'] = $row['crdate'];
		$this['groupUid'] = $row['group_uid'];
		$this['name'] = $row['name'];
		$this['link'] = $row['link'];
		$this['user'] = $row['user'];
	}

	/**
	 * Add password to db and set new uid in data array
	 *
	 * @throws Exception if add failed
	 * @return integer uid of new password
	 */
	public function add() {
		$data = array (
			'group_uid' => $this['groupUid'],
			'tstamp' => $this['timeStamp'],
			'crdate' => $this['createDate'],
			'name' => $this['name'],
			'link' => $this['link'],
			'user' => $this['user']
		);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_passwordmgr_password',
			$data
		);
		if ( $res ) {
			$this['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id($res);
			tx_passwordmgr_helper::addLogEntry(1, 'addPassword', 'Added password '.$data['name'].' uid '.$this['uid']);
		} else {
			throw new Exception ('Error adding password ' . $this['name']);
		}
		return ($this['uid']);
	}

	/**
	 * Update password entry in db
	 *
	 * @return void
	 */
	public function update() {
		$data = array (
			'group_uid' => $this['groupUid'],
			'tstamp' => $this['timeStamp'],
			'crdate' => $this['createDate'],
			'name' => $this['name'],
			'link' => $this['link'],
			'user' => $this['user']
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_passwordmgr_password',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'],'tx_passwordmgr_password'),
			$data
		);
		$this->checkAffectedRows('updatePassword', 1);
		tx_passwordmgr_helper::addLogEntry(1, 'updatePassword', 'Update password '.$data['name'].' uid '.$this['uid']);
	}

	/**
	 * Delete password
	 * - Delete sslData of this password for every user
	 * - Delete password
	 *
	 * @return void
	 */
	public function delete() {
		// Get sslData for every member with access to this password and delete it
		$sslDataList = $this->getSslList();
		$sslDataList->deleteListItems();

		// Delete password
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_passwordmgr_password',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'], 'tx_passwordmgr_password')
		);
		$this->checkAffectedRows('deletePassword', 1);
		tx_passwordmgr_helper::addLogEntry(1, 'deletePassword', 'Removed password '.$this['uid']);
	}

	/**
	 * Get list of ssl data items
	 * Instantiate list if not already existing first
	 *
	 * @return tx_passwordmgr_sslDataList Instance of data list
	 */
	public function getSslList() {
		if ( !is_object($this->sslList) ) {
			$this->sslList = t3lib_div::makeInstance('tx_passwordmgr_model_sslDataList');
			$this->sslList->init($this['uid']);
		}
		return($this->sslList);
	}
}
?>
