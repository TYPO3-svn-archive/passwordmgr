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
 * Class 'changePassphrase' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_changePassphrase extends tx_passwordmgr_action_default {
	/**
	 * Change passphrase (master password) of existing private key
	 *
	 * @return void
	 */
	public function execute() {
		try {
			// Initialize current user object
			$user = t3lib_div::makeInstance('tx_passwordmgr_model_user');
			$user->init($GLOBALS['BE_USER']->user['uid']);

			// Check new passphrase
			$pw1 = $GLOBALS['moduleData']['password1'];
			$pw2 = $GLOBALS['moduleData']['password2'];
			tx_passwordmgr_helper::checkLengthGreaterZero( $pw1, 'Password 1' );
			tx_passwordmgr_helper::checkLengthGreaterZero( $pw2, 'Password 2' );
			tx_passwordmgr_helper::checkIdenticalPasswords( $pw1, $pw2 );
			tx_passwordmgr_helper::checkPasswordMinimumLength( $pw1 );

			// Get new private key
			$user['privateKey'] = tx_passwordmgr_openssl::changePassphrase($user['privateKey'], $GLOBALS['moduleData']['passphrase'], $pw1);

			// Store new private key in db
			$user->updatePrivateKey();
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'changePassphrase', 'Can not change master password');
		}

		$this->defaultView();
	}
}
?>
