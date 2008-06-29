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
 * Class 'ssldata' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_sslData extends tx_passwordmgr_model_data {
	/**
	 * @var array ssl data details
	 */
	protected $data = array(
		'passwordUid' => integer,
		'beUserUid' => integer,
		'timeStamp' => integer, // last change date
		'createDate' => integer, // create date of group
		'key' => string,
		'data' => string
	);

	/**
	 * Initialize object
	 *
	 * @param integer id of password for this ssl data
	 * @param integer id of be user
	 * @return void
	 */
	public function init($passwordUid,$beUserUid) {
		$this['passwordUid'] = $passwordUid;
		$this['beUserUid'] = $beUserUid;
		$this->fetchDetails();
	}

	/**
	 * Fetch ssl data details an set in data array
	 *
	 * @return void
	 */
	protected function fetchDetails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'crdate, tstamp, sslkey, ssldata',
			'tx_passwordmgr_ssldata',
			'password_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['passwordUid'], 'tx_passwordmgr_password') .
				' AND be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'be_users')
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this['createDate'] = $row['crdate'];
		$this['timeStamp'] = $row['tstamp'];
		$this['key'] = $row['sslkey'];
		$this['data'] = $row['ssldata'];
	}

	/**
	 * Add ssl data to db
	 *
	 * @throws Exception if add failed
	 * @return void
	 */
	public function add() {
		$data = array(
			'password_uid' => $this['passwordUid'],
			'be_users_uid' => $this['beUserUid'],
			'tstamp' => $this['timeStamp'],
			'crdate' => $this['createDate'],
			'sslkey' => $this['key'],
			'ssldata' => $this['data']
		);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_passwordmgr_ssldata',
			$data
		);
		if ( $res ) {
			tx_passwordmgr_helper::addLogEntry(1, 'addSsl', 'Added ssl data for user '.$this['beUserUid'].', password '.$this['passwordUid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(1, 'addSsl', 'Can not add ssl data for user '.$this['beUserUid'].', password '.$this['passwordUid']);
			throw new Exception('Error adding ssl data for user '.$data['beUserUid'].' and password uid '.$data['passwordUid']);
		}
	}

	/**
	 * Update ssldata entry in db
	 *
	 * @throws Exception if db update failed
	 * @return void
	 */
	public function update() {
		$data = array(
			'tstamp' => $this['timeStamp'],
			'crdate' => $this['createDate'],
			'sslkey' => $this['key'],
			'ssldata' => $this['data']
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_passwordmgr_ssldata',
			'password_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['passwordUid'],'tx_passwordmgr_password') .
				' AND be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'be_users'),
			$data
		);
		$affectedRows = (integer)$GLOBALS['TYPO3_DB']->sql_affected_rows();
		if ( $affectedRows == 1 ) {
			tx_passwordmgr_helper::addLogEntry(1, 'updateSsl', 'Updated ssl data for user '.$this['beUserUid'].', password '.$this['passwordUid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'updateSsl', 'Can not update ssl data for user '.$this['beUserUid'].', password '.$this['passwordUid']);
			throw new Exception('Error updating ssl data for user '.$data['beUserUid'].' and password uid '.$data['passwordUid']);
		}
	}

	/**
	 * Delete an item from db
	 *
	 * @throws Exception if delete failed
	 * @return void
	 */
	public function delete() {
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_passwordmgr_ssldata',
			'password_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['passwordUid'], 'tx_passwordmgr_ssldata') .
				' AND be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['beUserUid'], 'tx_passwordmgr_ssldata')
		);
		$affectedRows = (integer)$GLOBALS['TYPO3_DB']->sql_affected_rows();
		if ( $affectedRows == 1 ) {
			tx_passwordmgr_helper::addLogEntry(1, 'deleteSsl', 'Removed ssl data for user '.$this['beUserUid'].', password '.$this['passwordUid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'deleteSsl', 'Wrong number of affected rows removing ssl data for user '.$this['beUserUid'].', password '.$this['passwordUid']);
			throw new Exception('Error deleting ssl data for user '.$this['beUserUid'].' and password uid '.$this['passwordUid']);
		}
	}
}
?>
