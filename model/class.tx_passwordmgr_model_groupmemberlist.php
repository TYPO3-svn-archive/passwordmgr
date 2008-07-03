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
 * Class 'groupmemberList' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_groupMemberList extends tx_passwordmgr_model_list {
	/**
	 * @var integer id of group for this memberlist
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
	 * Fetch list group objects and initialize list
	 *
	 * @return void
	 */
	protected function fetchList() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'be_users.uid AS uid, be_users.username AS username, be_users.tx_passwordmgr_cert AS cert, tx_passwordmgr_group_be_users_mm.rights AS rights',
			'tx_passwordmgr_group_be_users_mm, be_users',
			'tx_passwordmgr_group_be_users_mm.be_users_uid=be_users.uid'. // Join Constraint
				' AND tx_passwordmgr_group_be_users_mm.group_uid='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this->groupUid,'tx_passwordmgr_group_be_users_mm'),
			'',
			'be_users.username' // ORDER
		);
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$groupMember = t3lib_div::makeInstance('tx_passwordmgr_model_groupMember');
			$groupMember['groupUid'] = $this->groupUid;
			$groupMember['beUserUid'] = $row['uid'];
			$groupMember['name'] = $row['username'];
			$groupMember['certificate'] = $row['cert'];
			$groupMember['publicKey'] = tx_passwordmgr_openssl::extractPublicKeyFromCertificate($row['cert']);
			$groupMember['rights'] = $row['rights'];
			$this->addListItem($groupMember);
		}
	}

	/**
	 * Add group member to list
	 *
	 * @param tx_passwordmgr_model_groupMember
	 * @return void
	 */
	public function addListItem(tx_passwordmgr_model_groupMember $groupMember) {
		parent::addListItem($groupMember);
	}
}
?>
