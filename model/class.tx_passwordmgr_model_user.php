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
 * Class 'ssluser' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_user extends tx_passwordmgr_model_data {
	protected $data = array(
		'uid' => integer,
		'name' => string,
		'privateKey' => string,
		'certificate' => string,
		'publicKey' => string
	);

	public function init($uid) {
		$this['uid'] = $uid;
		$this->fetchDetails();
	}

	protected function fetchDetails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'username, tx_passwordmgr_privkey, tx_passwordmgr_cert',
			'be_users',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'], 'be_users')
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this['name'] = ($row['username']);
		$this['privateKey'] = $row['tx_passwordmgr_privkey'];
		$this['certificate'] = $row['tx_passwordmgr_cert'];
	}

	/**
	 * Store certificate and crypted private key in db
	 *
	 * @return void
	 */
	public function update() {
		$fields = array(
			'tx_passwordmgr_cert' => $this['certificate'],
			'tx_passwordmgr_privkey' => $this['privateKey']
		);
		$this->updateFields($fields);
	}

	/**
	 * Store certificate
	 *
	 * @return	void
	 */
	public function updateCertificate() {
		$fields = array(
			'tx_passwordmgr_cert' => $this['certificate']
		);
		$this->updateFields($fields);
	}

	/**
	 * Store private key
	 *
	 * @return	void
	 */
	public function updatePrivateKey() {
		$fields = array(
			'tx_passwordmgr_privkey' => $this['privateKey']
		);
		$this->updateFields($fields);
	}

	/**
	 * Update fields in be_users table
	 *
	 * @throws Exception if more or less than one row was affected
	 * @return void
	 */
	protected function updateFields($fields) {
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'be_users',
			'uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this['uid'], 'be_users'),
			$fields
		);
		$affectedRows = (integer)$GLOBALS['TYPO3_DB']->sql_affected_rows();
		if ( $affectedRows == 1 ) {
			tx_passwordmgr_helper::addLogEntry(1, 'updateBeUserFields', 'Updated fields of user '.$this['uid']);
		} else {
			tx_passwordmgr_helper::addLogEntry(3, 'updateBeUserFields', 'Wrong number of affected rows updating fields of user '.$this['uid']);
			throw new Exception('Error updating user '.$this['uid']);
		}
	}
}
?>
