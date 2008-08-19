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
 * Class 'moduleData' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_moduleData extends tx_passwordmgr_model_data {
	/**
	 * @var array Data array of possible form post vars
	 */
	protected $data = array(
		'view' => '',
		'action' => '',
		'foldType' => '',
		'foldState' => '',
		'groupUid' => '',
		'passwordUid' => '',
		'groupMemberUid' => '',
		'groupMemberRights' => '',
		'groupName' => '',
		'passphrase' => '',
		'passwordName' => '',
		'passwordLink' => '',
		'passwordUser' => '',
		'passwordPassword' => '',
		'password1' => '',
		'password2' => '',
		'displayLogOnErrorOnly' => '',
	);

	/**
	 * Fill data array with given post values
	 *
	 * @return void
	 */
	public function __construct() {
		// Stuff every post value into data array
		// This will fail if a non defined array key is accessed
		$formData = t3lib_div::_GP('DATA');
		if ( is_array($formData) ) {
			foreach ( $formData as $key=>$value ) {
				$key = substr($key, strlen('tx_passwordmgr_'));
				$this[$key] = $value;
			}
		}
	}
}
?>
