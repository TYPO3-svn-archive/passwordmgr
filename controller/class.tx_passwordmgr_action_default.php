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
 * Class 'default' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_action_default implements tx_passwordmgr_action_interface {
	/**
	 * @var tx_passwordmgr_view_* view object
	 */
	protected $view;

	/**
	 * @var array Holds data that can be accessed from view object
	 */
	protected $data = array();

	/**
	 * Call default view
	 *
	 * @return void
	 */
	public function execute() {
		$this->defaultView();
	}

	/**
	 * Instantiate and call view
	 * - Determine if user is initialized and call initialize view if not
	 * - Check for view selection in post data and call
	 * - Use be user uc view setting if no past value found
	 * - Use default view 'overview' if no be user uc value found
	 *
	 * @throws Exception if view no view found
	 * @return void
	 */
	protected function defaultView() {
		$user =  t3lib_div::makeInstance('tx_passwordmgr_model_user');
		$user->init($GLOBALS['BE_USER']->user['uid']);

		// Check if user ssl can be initialized. Render initialize view if not.
		try {
			$user['publicKey'] = tx_passwordmgr_openssl::extractPublicKeyFromCertificate($user['certificate']);
		} catch ( Exception $e ) {
			$this->data['functionMenu'] = t3lib_div::makeInstance('tx_passwordmgr_model_functionMenu');
			$this->data['functionMenu']->init('userNotInitialized');
			$this->view = t3lib_div::makeInstance('tx_passwordmgr_view_initializeKeyPair');
			$this->view->render($this->data);
			return;
		}

		// Determine view
		// If already set from post, compare with user uc and modify if changed
		// If no post, use last user uc value
		// If no user uc value, use default and set user uc
		$userData = t3lib_div::makeInstance('tx_passwordmgr_model_userData');
		if ( strlen($GLOBALS['moduleData']['view']) > 0 ) {
			if ( !(strcmp($userData['view'],$GLOBALS['moduleData']['view'])==0) ) {
				$userData->updateView($GLOBALS['moduleData']['view']);
			}
		} elseif ( strlen($userData['view'])>0 && class_exists('tx_passwordmgr_view_'.$userData['view']) ) {
			$GLOBALS['moduleData']['view'] = $userData['view'];
		} else {
			$GLOBALS['moduleData']['view'] = 'overview';
			$userData->updateView($GLOBALS['moduleData']['view']);
		}

		// Istantiate and call view
		$viewKey = $GLOBALS['moduleData']['view'];
		$extPath = t3lib_extMgm::extPath(tx_passwordmgr_module1::extKey);
		if ( $viewKey && class_exists('tx_passwordmgr_view_'.$viewKey) && is_file($extPath.'view/class.tx_passwordmgr_view_'.strtolower($viewKey).'.php') ) {
			$this->data['functionMenu'] = t3lib_div::makeInstance('tx_passwordmgr_model_functionMenu');
			$this->data['functionMenu']->init('all');
			$this->view = t3lib_div::makeInstance('tx_passwordmgr_view_'.$viewKey);
			$this->view->render($this->data);
		} else {
			throw new Exception ('View not found: ' . $viewKey);
		}
	}

	/**
	 * Print out the module content
	 *
	 * @return void
	 */
	public function printContent() {
		$this->view->printContent();
	}
}
?>
