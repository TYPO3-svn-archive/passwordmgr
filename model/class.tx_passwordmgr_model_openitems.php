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
 * Class 'openItems' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_model_openItems {
	protected $moduleUc = array();

	protected $open = array(
		'openGroup' => array(),
		'openMember' => array(),
		'openPassword' => array(),
	);

	public function __construct() {
		$this->moduleUc = $GLOBALS['BE_USER']->getModuleData('user_txpasswordmgrM1');
		if ( is_string($this->moduleUc['openGroup']) ) {
			$this->open['openGroup'] = unserialize($this->moduleUc['openGroup']);
		}
		if ( is_string($this->moduleUc['openMember']) ) {
			$this->open['openMember'] = unserialize($this->moduleUc['openMember']);
		}
		if ( is_string($this->moduleUc['openPassword']) ) {
			$this->open['openPassword'] = unserialize($this->moduleUc['openPassword']);
		}
	}

	public function open($type, $groupUid) {
		try {
			$type = $this->getTypeName($type);
			$this->open[$type][$groupUid] = 1;
			$this->moduleUc[$type] = serialize($this->open[$type]);
			$GLOBALS['BE_USER']->pushModuleData('user_txpasswordmgrM1', $this->moduleUc);
		} catch (Exception $e) {
			tx_passwordmgr_helper::addLogEntry(3, 'openItems', 'Can not open '.$type);
		}
	}
	
	public function close($type, $groupUid) {
		try {
			$type = $this->getTypeName($type);
			unset($this->open[$type][$groupUid]);
			$this->moduleUc[$type] = serialize($this->open[$type]);
			$GLOBALS['BE_USER']->pushModuleData('user_txpasswordmgrM1', $this->moduleUc);
		} catch (Exception $e) {
			tx_passwordmgr_helper::addLogEntry(3, 'openItems', 'Can not close '.$type);
		}
	}

	public function isOpen($type, $groupUid) {
		$open = FALSE;
		try {
			$type = $this->getTypeName($type);
			if ( isset($this->open[$type][$groupUid]) ) {
				$open = TRUE;
			}
		} catch (Exception $e) {
			tx_passwordmgr_helper::addLogEntry(3, 'openItems', 'Can not access '.$type);
		}
		return($open);
	}

	protected function getTypeName($type) {
		switch ($type) {
			case 'group':
				return('openGroup');
			case 'password':
				return('openPassword');
			case 'member':
				return('openMember');
			default:
				throw new Exception ('Access to non existing fold type');
		}
	}
}
?>
