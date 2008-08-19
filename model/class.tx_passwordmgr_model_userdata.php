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
 * Class 'userData' for the 'passwordmgr' extension.
 * Data class for be_user module data, stored in be_user uC
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_userData extends tx_passwordmgr_model_data {
	/**
	 * @var array List of user data open / non collapsed items
	 */
	protected $data = array(
		'view' => string, // Current view
		'openGroup' => array(), // Open groups
		'openMember' => array(), // Open member lists
		'openPassword' => array(), // Open password lists
		'selectedPassword' => integer, // Selected password, for moving
		'displayLogOnErrorOnly' => integer // If 1 display only errors in the log row and do not show info logs. Else show both
	);

	/**
	 * Get user specific module data
	 *
	 * @return void
	 */
	public function __construct() {
		$moduleUc = $GLOBALS['BE_USER']->getModuleData('user_txpasswordmgrM1');

		// Latest chosen view
		$this['view'] = $moduleUc['view'];

		// List of open group items
		if ( is_string($moduleUc['openGroup']) ) {
			$this['openGroup'] = unserialize($moduleUc['openGroup']);
		} else {
			$this['openGroup'] = '';
		}

		// List of open member items
		if ( is_string($moduleUc['openMember']) ) {
			$this['openMember'] = unserialize($moduleUc['openMember']);
		} else {
			$this['openMember'] = '';
		}

		// List of open password items
		if ( is_string($moduleUc['openPassword']) ) {
			$this['openPassword'] = unserialize($moduleUc['openPassword']);
		} else {
			$this['openPassword'] = '';
		}
		$this['selectedPassword'] = $moduleUc['selectedPassword'];

		// Wether or not to show only errors in log row
		if ( isset($moduleUc['displayLogOnErrorOnly']) && $moduleUc['displayLogOnErrorOnly'] == 1 ) {
			$this['displayLogOnErrorOnly'] = 1;
		} else {
			$this['displayLogOnErrorOnly'] = 0;
		}
	}

	/**
	 * Store view in user uc
	 *
	 * @param string view
	 * @return void
	 */
	public function updateView($view) {
		$this['view'] = $view;
		$this->update();
	}

	/**
	 * Mark an item as open
	 *
	 * @param string 'group', 'member' or 'password'
	 * @param integer id of group
	 * @throws Exception if item open failed
	 * @return void
	 */
	public function open($type, $groupUid) {
		try {
			$type = $this->getTypeName($type);
			$this->data[$type][$groupUid] = 1;
			$this->update();
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Mark an item as closed
	 *
	 * @param string 'group', 'member' or 'password'
	 * @param integer id of group
	 * @throws Exception if item close failed
	 * @return void
	 */
	public function close($type, $groupUid) {
		try {
			$type = $this->getTypeName($type);
			unset($this->data[$type][$groupUid]);
			$this->update();
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Determine if a item is open
	 *
	 * @param string 'group', 'member' or 'password'
	 * @param integer id of group
	 * @throws Exception if type is not within valid values
	 * @return bool
	 */
	public function isOpen($type, $groupUid) {
		$open = FALSE;
		try {
			$type = $this->getTypeName($type);
			if ( isset($this->data[$type][$groupUid]) ) {
				$open = TRUE;
			}
		} catch (Exception $e) {
			throw $e;
		}
		return($open);
	}

	/**
	 * Mark a password as selected. Needed for cut+paste
	 *
	 * @param integer Password uid
	 * @return void
	 */
	public function selectPassword($passwordUid) {
		$this['selectedPassword'] = $passwordUid;
		$this->update();
	}

	/**
	 * Deselect selected password. Needed for cut+paste
	 *
	 * @return void
	 */
	public function deselectPassword() {
		$this['selectedPassword'] = '';
		$this->update();
	}

	/**
	 * Wether or not given password uid is selected
	 *
	 * @param integer Password uid
	 * @return boolean TRUE if password is selected
	 */
	public function isSelectedPassword($passwordUid) {
		$isSel = FALSE;
		if ( $this['selectedPassword'] == $passwordUid ) {
			$isSel = TRUE;
		}
		return($isSel);
	}

	/**
	 * Change setting of display log on Error only
	 *
	 * @param boolean TRUE if only errors should be shown
	 * @return void
	 */
	public function changeDisplayLogOnErrorOnly($newValue) {
		$this['displayLogOnErrorOnly'] = $newValue;
		$this->update();
	}

	/**
	 * Helper function to translate $type to corresponding data array name
	 *
	 * @param string itemname
	 * @throws Exception if type not one of 'group', 'member' or 'password'
	 * @return string data array name
	 * @todo Find better function name
	 */
	protected function getTypeName($type) {
		switch ($type) {
			case 'group':
				return('openGroup');
			case 'password':
				return('openPassword');
			case 'member':
				return('openMember');
			default:
				throw new Exception ('Access to non existing fold type: ' . $type);
		}
	}

	/**
	 * Store new user module data in db
	 *
	 * @return void
	 */
	protected function update() {
		$moduleUc = array();
		$moduleUc['view'] = $this['view'];
		$moduleUc['openGroup'] = serialize($this['openGroup']);
		$moduleUc['openPassword'] = serialize($this['openPassword']);
		$moduleUc['openMember'] = serialize($this['openMember']);
		$moduleUc['selectedPassword'] = $this['selectedPassword'];
		$moduleUc['displayLogOnErrorOnly'] = $this['displayLogOnErrorOnly'];
		$GLOBALS['BE_USER']->pushModuleData('user_txpasswordmgrM1', $moduleUc);
	}
}
?>
