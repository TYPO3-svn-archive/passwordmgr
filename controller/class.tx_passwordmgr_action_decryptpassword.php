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
 * Class 'decryptPassword' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_decryptPassword extends tx_passwordmgr_action_default {
	/**
	 * Decrypt ssl data of a password with a users private key and his passphrase
	 * Sets plaintext password in $this->data['passwordUid']
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Get input data
			$passwordUid = $GLOBALS['moduleData']['passwordUid'];

			// Initialize user object
			$user = t3lib_div::makeInstance('tx_passwordmgr_model_user');
			$user->init($GLOBALS['BE_USER']->user['uid']);

			// Initialize sslData object
			$sslData = t3lib_div::makeInstance('tx_passwordmgr_model_sslData');
			$sslData->init($passwordUid, $user['uid']);

			// Decrypt sslData with private key
			$passphrase = $GLOBALS['moduleData']['passphrase'];
			$this->data['plaintextPassword'][$passwordUid] = tx_passwordmgr_openssl::decrypt($user['privateKey'], $passphrase, $sslData['key'], $sslData['data']);
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'decryptPassword', 'Can not decrypt password '.$passwordUid);
		}

		$this->defaultView();
	}
}
?>
