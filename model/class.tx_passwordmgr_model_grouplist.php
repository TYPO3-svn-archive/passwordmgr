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
 * Class 'grouplist' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_groupList extends tx_passwordmgr_model_list {
	// id of user
	protected $userUid;

	public function init($userUid) {
		$this->userUid = $userUid;
		$this->fetchList();
	}

	protected function fetchList() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'DISTINCT tx_passwordmgr_group.uid AS uid, tx_passwordmgr_group.name AS name',
			'tx_passwordmgr_group, tx_passwordmgr_group_be_users_mm',
			'tx_passwordmgr_group_be_users_mm.group_uid = tx_passwordmgr_group.uid'. // Join constraint
				' AND tx_passwordmgr_group_be_users_mm.be_users_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this->userUid,'be_users') // All groups of this user
		);
		$i = 0;
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$this->list[$i] = t3lib_div::makeInstance('tx_passwordmgr_model_group');
			$this->list[$i]['uid'] = $row['uid'];
			$this->list[$i]['name'] = $row['name'];
			$i++;
		}
	}
}
?>
