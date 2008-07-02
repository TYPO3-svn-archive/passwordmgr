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
 * Class 'openssl' for the 'passwordmgr' extension.
 * Static methods for ssl issues
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_openssl {
	/**
	 * Checks if a public key can be retrieved from a certificate
	 * Helper function to verify that a certificate is not existing or invalid
	 *
	 * @throws Exception if public key extraction is successfull
	 * @return void
	 */
	public static function checkNoExtractPublicKeyFromCertificate($certificate) {
		try {
			tx_passwordmgr_openssl::extractPublicKeyFromCertificate($certificate);
		} catch ( Exception $e ) {
			return;
		}
		tx_passwordmgr_helper::addLogEntry(3, 'checkNoExtractPublicKeyFromCertificate', 'Extraction of public key from certificate successfull.');
		throw new Exception('Extraction of public key from certificate successfull');
	}

	/**
	 * Extracts the public key from a Certificate.
	 *
	 * @throws Exception if public key can not be retrieved
	 * @return string Public key
	 */
	public static function extractPublicKeyFromCertificate($certificate) {
		if ($res = openssl_pkey_get_public($certificate) ) {
			if ($detailArray = openssl_pkey_get_details($res)) {
				return($detailArray['key']);
			} else {
				throw new Exception('Can not extract public key from certificate');
			}
		} else {
			throw new Exception('Can not extract public key from certificate');
		}
	}

	/**
	 * Encrypt a string with a andomly generated secret key.
	 * - The secret key is encrypted with the users public key.
	 * - If the payload data is smaller than 20 character,
	 *   its padded with a random string to ensure a minimun length of $data
	 * - The encrypted data is a serialized array of payload and random data
	 * 
	 * @return array Key and data
	 */
	public static function encrypt($publicKey, $data) {
		$cryptData = array (
			'p' => $data,
			'r' => ''
		);
		if ( strlen($data)<20 ) {
			$cryptData['r'] = tx_passwordmgr_helper::getRandomString(20 - strlen($data));
		}
		$cryptString = serialize($cryptData);
		openssl_seal ($cryptString, $encrypted, $keys, array($publicKey));
		$keyAndData = array (
			'key' => $keys[0],
			'data' => $encrypted
		);
		return($keyAndData);
	}

	/**
	 * Decrypt data:
	 * - Use passphrase to decrypt the private key of a user
	 * - Use plaintext private key to decrypt envelope key
	 * - Use envelope key to decrypt data
	 * - Destroy plaintext key resources after usage
	 * - Extract payload string from serialized data array padded with random string
	 *
	 * @throws Exception if decryption fails
	 * @return string Plaintext data
	 */
	public static function decrypt($encryptedPrivateKey, $passphrase, $key, $data) {
		// Decrypt private key with passphrase and throw Exception if not successfull
		$privateKeyResource = openssl_pkey_get_private($encryptedPrivateKey, $passphrase);
		if ( !$privateKeyResource ) {
			tx_passwordmgr_helper::addLogEntry(3, 'decryptPrivateKey', 'Can not decrypt private key. Wrong password?');
			throw new Exception('Can not decrypt private Key');
		}

		// Decrypt data
		$success = openssl_open($data, $cryptString, $key, $privateKeyResource);

		// Destroy key resource and throw exception if decryption was not successfull
		if ( !$success ) {
			// Destroy private key resource
			if ( is_resource($privateKeyResource)) {
				openssl_pkey_free($privateKeyResource);
			}
			throw new Exception('Can not decrypt data');
		}

		// Destroy private key resource
		openssl_pkey_free($privateKeyResource);

		// Unserialize padded data array and return payload
		$cryptData = unserialize($cryptString);

		// return decrypted data
		return($cryptData['p']);
	}

	/**
	 * Change passphrase of private key
	 * - Use current passphrase to get private key resource
	 * - Export new private key encrypted with new passphrase
	 * - Destroy resource
	 *
	 * @param string Current encrypted private key
	 * @param string Current passphrase
	 * @param string New passphrase
	 * @throws Exception if new private key can not be created
	 * @return string New private key
	 */
	public static function changePassphrase($encryptedPrivateKey, $currentPassphrase, $newPassphrase) {
		/**
		 * @var array Configuration for new private keys
		 */
		$sslConfig = array(
			'encrypt_key' => TRUE,
			'private_key_bits' => 2048,
		);

		// Decrypt private key with passphrase and throw Exception if not successfull
		$privateKeyResource = openssl_pkey_get_private($encryptedPrivateKey, $currentPassphrase);
		if ( !$privateKeyResource ) {
			tx_passwordmgr_helper::addLogEntry(3, 'decryptPrivateKey', 'Can not decrypt private key. Wrong password?');
			throw new Exception('Can not decrypt private Key');
		}
		
		// Export new private key encrypted with new passphrase
		openssl_pkey_export($privateKeyResource, $newPrivateKey, $newPassphrase, $sslConfig);

		// Destroy private key resource
		openssl_pkey_free($privateKeyResource);

		return($newPrivateKey);
	}

	/**
	 * Create new self signed certificate and a private key for current be user
	 * Certificate is created with given be user information
	 * Private key is encrypted with passphrase
	 *
	 * @param string passphrase
	 * @return array Private key and certificate
	 */
	public static function createNewCertificateAndPrivateKey($passphrase) {
		/*
		 * @var integer Default validity of certificates in days
		 */
		$certificateValidDays = 365;

		/**
		 * @var array Configuration for new certificates
		 */
		$sslConfig = array(
			'private_key_bits' => 2048,
			'encrypt_key' => TRUE,
		);

		// Create new key pair
		$keyResource = openssl_pkey_new($sslConfig);

		// Extract private key string to $privateKey, encrypted with $passphrase
		openssl_pkey_export($keyResource, $privateKey, $passphrase, $sslConfig);

		// Set given certificate details extracted from be user information
		$certificateDetails = array();
		if ( strlen($GLOBALS['BE_USER']->user['realName']) ) {
			$certificateDetails['commonName'] = $GLOBALS['BE_USER']->user['realName'];
		} else {
			$certificateDetails['commonName'] = $GLOBALS['BE_USER']->user['username'];
		}
		if ( strlen($GLOBALS['BE_USER']->user['email']) ) {
			$certificateDetails['emailAddress'] = $GLOBALS['BE_USER']->user['email'];
		}

		// Create a certificate signing request from the keyResoure and the given certificate details
		$csr = openssl_csr_new($certificateDetails, $keyResource);

		// Self sign certificate signing request with default valid time and export certificate to $certificate
		$certificateResource = openssl_csr_sign($csr, null, $keyResource, $certificateValidDays, $sslConfig);
		openssl_x509_export($certificateResource, $certificate);

		// Destroy resources
		openssl_pkey_free($keyResource);
		openssl_x509_free($certificateResource);

		// Return certificate and private key
		$certificateAndPrivateKey = array (
			'certificate' => $certificate,
			'privateKey' => $privateKey
		);
		return($certificateAndPrivateKey);
	}
}
?>
