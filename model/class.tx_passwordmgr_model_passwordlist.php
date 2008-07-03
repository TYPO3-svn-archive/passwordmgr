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
 * Class 'passwordlist' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_passwordList extends tx_passwordmgr_model_list {
	/**
	 * @var integer id of group for this passwordlist
	 */
	protected $groupUid;

	/**
	 * Initialize object
	 *
	 * @param integer id of group
	 * @return void
	 */
	public function init($groupUid) {
		$this->groupUid = $groupUid;
		$this->fetchList();
	}

	/**
	 * Fetch all passwords of a group
	 *
	 * @return void
	 */
	protected function fetchList() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid, group_uid, name, link, user',
			'tx_passwordmgr_password',
			'tx_passwordmgr_password.group_uid=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->groupUid,'tx_passwordmgr_password'),
			'',
			'name' // ORDER
		);
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$password = t3lib_div::makeInstance('tx_passwordmgr_model_password');
			$password['uid'] = $row['uid'];
			$password['groupUid'] = $row['group_uid'];
			$password['name'] = $row['name'];
			$password['link'] = $row['link'];
			$password['user'] = $row['user'];
			$this->addListItem($password);
		}
	}

	/**
	 * Add password object to list
	 *
	 * @param tx_passwordmgr_model_password
	 * @return void
	 */
	public function addListItem(tx_passwordmgr_model_password $password) {
		parent::addListItem($password);
	}
}
?>
