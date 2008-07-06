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
 * Class 'userlist' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_userList extends tx_passwordmgr_model_list {
	/**
	 * Initialize object
	 */
	public function init() {
		$this->fetchList();
	}

	/**
	 * Fetch all Backend users and try to initialize their ssl keys
	 * Add to list if a user is fully initialized
	 *
	 * @return	void
	 */
	protected function fetchList() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid, username, tx_passwordmgr_cert, tx_passwordmgr_privkey',
			'be_users',
			'1 ' . t3lib_BEfunc::BEenableFields('be_users') . t3lib_BEfunc::deleteClause('be_users'),
			'',
			'username' // ORDER
		);
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$user = t3lib_div::makeInstance('tx_passwordmgr_model_user');
			$user['uid'] = $row['uid'];
			$user['name'] = ($row['username']);
			$user['certificate'] = $row['tx_passwordmgr_cert'];
			$user['privateKey'] = $row['tx_passwordmgr_privkey'];
			try {
				$user['publicKey'] = tx_passwordmgr_openssl::extractPublicKeyFromCertificate($user['certificate']);
				$this->addListItem($user);
			} catch ( Exception $exception ) {
				tx_passwordmgr_helper::addLogEntry(1, 'beUserList', 'Can not add user '.$user['uid'].' to userlist, not initialized');
			}
		}
	}

	/**
	 * Add user object to list
	 *
	 * @param tx_passwordmgr_model_user
	 * @return void
	 */
	public function addListItem(tx_passwordmgr_model_user $user) {
		parent::addListItem($user);
	}
}
?>
