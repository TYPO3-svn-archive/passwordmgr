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

// Default initialization
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:passwordmgr/mod1/locallang.xml');
// This checks permissions and exits if the users has no permission for entry.
$BE_USER->modAccess($MCONF,1);

/**
 * Module 'Password Manager' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_module1 {
	/**
	 * @const Extension Key
	 */
	const extKey = 'passwordmgr';

	/**
	 * @var tx_passwordmgr_model_logList Global log list Object
	 */
	public $logList = object;

	/**
	 * @var tx_passwordmgr_model_moduleData Global post / get data Object
	 */
	public $moduleData = object;

	/**
	 * @var template Dummy instance of template
	 */
	public $doc;

	/**
	 * Default constructor
	 * Load depending module classes and instantiate global objects
	 *
	 * @return void
	 */
	public function __construct() {
		//$moduleConfig = $GLOBALS['MCONF'];
		//$modTSconfig = t3lib_BEfunc::getModTSconfig( 0, 'mod.'.$this->MCONF['name'] );

		// Include class files
		$this->classLoader();

		// Init global logList object
		$GLOBALS['logList'] = t3lib_div::makeInstance('tx_passwordmgr_model_loglist');

		// Initialize module Data. This holds the chosen view, the action and all post Data values
		$GLOBALS['moduleData'] = t3lib_div::makeInstance('tx_passwordmgr_model_moduleData');

		// Create a dummy object of temlpate. This is a hack to make template->getPageInfo() happy
		$this->doc=t3lib_div::makeInstance('template');
	}

	/**
	 * Load all module classes
	 * @return void
	 */
	protected function classLoader() {
		// Get absolute path of extension directory
		$extPath = t3lib_extMgm::extPath(tx_passwordmgr_module1::extKey);
		// Add all classes to load array
		$this->include_once[] = $extPath . 'helper/class.tx_passwordmgr_helper.php';
		$this->include_once[] = $extPath . 'helper/class.tx_passwordmgr_openssl.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_interface.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_default.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_initializekeypair.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_addgroup.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_addpassword.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_addgroupmember.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_deletepassword.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_deletegroup.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_deletegroupmember.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_editpassword.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_editgroup.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_decryptpassword.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_fold.php';
		$this->include_once[] = $extPath . 'controller/class.tx_passwordmgr_action_changepassphrase.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_default.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_overview.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_initializekeypair.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_addeditpassword.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_addeditgroup.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_addgroupmember.php';
		$this->include_once[] = $extPath . 'view/class.tx_passwordmgr_view_changepassphrase.php';
		// Default data list class, implements Iterator, Countable
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_list.php';
		// Data list classes
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_userlist.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_grouplist.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_groupmemberlist.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_passwordlist.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_ssldatalist.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_loglist.php';
		// Default data class, implements ArrayAccess, IteratorAggregate
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_data.php';
		// Data classes
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_group.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_user.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_groupmember.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_password.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_ssldata.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_log.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_moduledata.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_functionmenu.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_functionmenu_allitems.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_functionmenu_usernotinitialized.php';
		$this->include_once[] = $extPath . 'model/class.tx_passwordmgr_model_userdata.php';
		// Load all classes
		foreach ($this->include_once as $incFile) {
			include_once($incFile);
		}
	}

	/**
	 * Main Controller action class
	 * Instantiate requested action class and execute
	 * Execute default action if no explicit action given
	 *
	 * @return void
	 */
	public function execute() {
/*
		foreach ( $GLOBALS['moduleData'] as $key=>$value ) {
			if ( strlen($value)>0 ) {
				debug($key.'  '.$value);
			}
		}
*/
		// Get absolute path of extension directory
		$extPath = t3lib_extMgm::extPath(tx_passwordmgr_module1::extKey);

		$actionKey = $GLOBALS['moduleData']['action'];
		if ( $actionKey && class_exists('tx_passwordmgr_action_'.$actionKey) && is_file($extPath.'controller/class.tx_passwordmgr_action_'.strtolower($actionKey).'.php') ) {
			$action = t3lib_div::makeInstance('tx_passwordmgr_action_'.$actionKey);
			$action->execute();
		} else {
			$action = t3lib_div::makeInstance('tx_passwordmgr_action_default');
			$action->execute();
		}
		$action->printContent();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/passwordmgr/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/passwordmgr/mod1/index.php']);
}

// Make instance
$SOBE = t3lib_div::makeInstance('tx_passwordmgr_module1');
// Execute controller
$SOBE->execute();
?>
