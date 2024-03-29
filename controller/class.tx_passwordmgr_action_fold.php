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
 * Class 'fold' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_fold extends tx_passwordmgr_action_default {
	/**
	 * Fold or unfold a group, password or member item
	 * Safe new open items in be user uc
	 *
	 * @return void
	 */
	public function execute() {
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');
		if ( $GLOBALS['moduleData']['foldState'] == 1 ) {
			$userData->open($GLOBALS['moduleData']['foldType'], $GLOBALS['moduleData']['groupUid']);
		} else {
			$userData->close($GLOBALS['moduleData']['foldType'], $GLOBALS['moduleData']['groupUid']);
		}

		$this->defaultView();
	}
}
?>
