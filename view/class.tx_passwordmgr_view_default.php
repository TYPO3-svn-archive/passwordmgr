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
 * Class 'view' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_default {
	// Holds the module html
	protected $content = '';

	// Instance of template
	protected $doc;

	// Holds all data given from controller
	protected $data;

	// Different table layouts
/*
	protected $tableLayouts = array(
		$
	);
 */
	public function __construct() {
		$this->doc = t3lib_div::makeInstance('template');
	}

	public function render( $data ) {
		$this->data = $data;
		$this->setDocDefaults();

		$bodyContent = $this->doc->header($GLOBALS['LANG']->getLL('title'));
		$bodyContent.= $this->logContent();
		$bodyContent.= $this->innerContent();

		$docHeaderButtons = array(
			'SHORTCUT' => ''
		);

		$markers = array(
			'VIEW_MENU' => $this->viewMenuContent(),
			'CONTENT' => $bodyContent
		);

		$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
		$this->content.= $this->doc->moduleBody($pageinfo, $docHeaderButtons, $markers);
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
	}

	public function printContent() {
		echo($this->content);
	}

	protected function setDocDefaults() {
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$this->doc->docType='xhtml_trans';

		// Default form tag
		$this->doc->form = '<form action="" method="post" name="passwordmgr">';

		// Template
		$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath(tx_passwordmgr_module1::extKey).'res/passwordmgr.html');

		// JavaScript for post var settings and passphrase popup
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				function passphrasePopUp() {
					window.open(\'../res/passphrasepopup.html\',\'Passphrase Popup\',\'height=300,width=500,status=0,menubar=0,scrollbars=1\');
				}
				function setAction(action) {
					document.forms["passwordmgr"].elements["DATA[tx_passwordmgr_action]"].value = action;
				}
				function setFieldValue(name, value) {
					document.forms["passwordmgr"].elements["DATA[tx_passwordmgr_"+name+"]"].value = value;
				}
			</script>
		';

		// Default table layouts
//		$this->doc-> 
	}

	protected function viewMenuContent() {
		$menuItems = array();
		foreach ( $this->data['functionMenu']['items'] as $funcKey => $funcValue ) {
			$selected = ($funcKey==$GLOBALS['moduleData']['view']) ? ' selected="selected"' : '';
			$menuItems[] = '<option value="'.$funcKey.'"'.$selected.'>'.$funcValue.'</option>';
		}

		$content = '
			<select name="DATA[tx_passwordmgr_view]" onchange="setFieldValue(\'groupUid\', \'\'); setFieldValue(\'passwordUid\', \'\'); document.passwordmgr.submit();">
				'.implode($menuItems).'
			</select>
		';

		return($content);
	}

	protected function logContent() {
		if ( count($GLOBALS['logList']) ) {
			$content = array();
			foreach( $GLOBALS['logList'] as $logItem ) {
				switch ( $logItem['priority'] ) {
					case 1:
						$style='style="color:green;"';
					break;
					case 2:
						$style='style="color:yellow;"';
					break;
					case 3:
						$style='style="color:red;"';
					break;
				}
				$content[] = '<p '.$style. '/>'.$logItem['module'].': '.$logItem['message'].'</p>';
			}
			return($this->doc->section('Log',implode($content),0,1));
		} else {
			return('');
		}
	}

	protected function innerContent() {
		return('<p>Inner content</p>');
	}
}
?>
