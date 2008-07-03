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

//require_once('../class.tx_passwordmgr_group.php');

/**
 * Class 'groupList' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_groupList extends tx_passwordmgr_model_list {
	/**
	 * @var integer id of be user
	 */
	protected $userUid;

	/**
	 * Initialize object
	 *
	 * @param integer id of be user
	 * @return void
	 */
	public function init($userUid) {
		$this->userUid = $userUid;
		$this->fetchList();
	}

	/**
	 * Fetch list group objects and initialize list
	 *
	 * @return void
	 */
	protected function fetchList() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'DISTINCT tx_passwordmgr_group.uid AS uid, tx_passwordmgr_group.name AS name',
			'tx_passwordmgr_group, tx_passwordmgr_group_be_users_mm',
			'tx_passwordmgr_group_be_users_mm.group_uid = tx_passwordmgr_group.uid'. // Join constraint
				' AND tx_passwordmgr_group_be_users_mm.be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this->userUid,'be_users'),
			'',
			'tx_passwordmgr_group.name' // ORDER
		);
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$group = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$group['uid'] = $row['uid'];
			$group['name'] = $row['name'];
			$this->addListItem($group);
		}
	}

	/**
	 * Add group object to list
	 *
	 * @param tx_passwordmgr_model_group
	 * @return void
	 */
	public function addListItem(tx_passwordmgr_model_group $group) {
		parent::addListItem($group);
	}
}
?>
