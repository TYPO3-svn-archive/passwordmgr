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
 * Class 'initializeKeyPair' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_initializeKeyPair extends tx_passwordmgr_action_default {
	public function execute() {
		// Instantiate user object
		$user =  t3lib_div::makeInstance('tx_passwordmgr_model_user');
		$user->init($GLOBALS['BE_USER']->user['uid']);

		try {
			// Check non valid certificate and public key
			tx_passwordmgr_openssl::checkNoExtractPublicKeyFromCertificate($user['certificate']);

			// Check passwords
			$pw1 = $GLOBALS['moduleData']['password1'];
			$pw2 = $GLOBALS['moduleData']['password2'];
			tx_passwordmgr_helper::checkLengthGreaterZero( $pw1, 'Password 1' );
			tx_passwordmgr_helper::checkLengthGreaterZero( $pw2, 'Password 2' );
			tx_passwordmgr_helper::checkIdenticalPasswords( $pw1, $pw2 );
			tx_passwordmgr_helper::checkPasswordMinimumLength( $pw1 );

			// Create new certificate and private key and update in db
			$newCertificateAndPrivateKey = tx_passwordmgr_openssl::createNewCertificateAndPrivateKey($pw1);
			$user['certificate'] = $newCertificateAndPrivateKey['certificate'];
			$user['privateKey'] = $newCertificateAndPrivateKey['privateKey'];
			$user->update();

			// Modify view to switch to addGroup view after successfull initialization
			$GLOBALS['moduleData']['view'] = 'addEditGroup';
		} catch ( Exception $e ) {
			tx_passwordmgr_helper::addLogEntry(3, 'initializeKeyPair', 'Can not initialize certificate and private key');
		}

		$this->defaultView();
	}
}
?>
