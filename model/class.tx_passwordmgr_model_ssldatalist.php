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
 * Class 'ssldatalist' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_sslDataList extends tx_passwordmgr_model_list {
	// Password uid
	protected $passwordUid;

	public function init($passwordUid) {
		$this->passwordUid = $passwordUid;
		$this->fetchList();
	}

	protected function fetchList() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'password_uid, be_users_uid, sslkey, ssldata',
			'tx_passwordmgr_password, tx_passwordmgr_ssldata',
			'tx_passwordmgr_password.uid=tx_passwordmgr_ssldata.password_uid'. // Join Constraint
				' AND tx_passwordmgr_password.uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this->passwordUid,'tx_passwordmgr_password')
		);
		$i = 0;
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$this->list[$i] = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
			$this->list[$i]['passwordUid'] = $row['password_uid'];
			$this->list[$i]['beUserUid'] = $row['be_users_uid'];
			$this->list[$i]['key'] = $row['sslkey'];
			$this->list[$i]['data'] = $row['ssldata'];
			$i++;
		}
	}
}
?>
