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
	/**
	 * @var string Holds the module html
	 */
	protected $content = '';

	/**
	 * @var template Instance of default typo3 template object
	 */
	protected $doc;

	/**
	 * @var array Holds all data given from controller
	 */
	protected $data;

	/**
	 * Default constructor to initialize template object
	 *
	 * @return void
	 */
	public function __construct() {
		$this->doc = t3lib_div::makeInstance('template');
	}

	/**
	 * Compile content
	 *
	 * @param array Data given from controller
	 */
	public function render( $data ) {
		// Set data in class variable
		$this->data = $data;

		// Initialize doc object
		$this->setDocDefaults();

		// Compile doc body made of a header row, a log content if existing and the main content
		$bodyContent = $this->doc->header($GLOBALS['LANG']->getLL('title'));
		$bodyContent.= $this->developmentWarning();
		$bodyContent.= $this->logContent();
		$bodyContent.= $this->innerContent();

		// Set shortcut item in docHeader
		$docHeaderButtons = array(
			'SHORTCUT' => ''
		);

		// Substitute these markers in template
		$markers = array(
			'VIEW_MENU' => $this->viewMenuContent(),
			'CONTENT' => $bodyContent
		);

		// Compile
		$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
		$this->content.= $this->doc->moduleBody($pageinfo, $docHeaderButtons, $markers);
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
	}

	/**
	 * Echo out module content
	 *
	 * @return void
	 */
	public function printContent() {
		echo($this->content);
	}

	/**
	 * Set doc object defaults
	 *
	 * @return void
	 */
	protected function setDocDefaults() {
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$this->doc->docType='xhtml_trans';

		// Default form tag
		$this->doc->form = '<form action="" method="post" name="passwordmgr">';

		// Template
		$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath(tx_passwordmgr_module1::extKey).'res/passwordmgr.html');

		// JavaScript to set post var data and to open passphrase popup
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
	}

	/**
	 * Content for the view selector in the top left. This is the main function menu
	 * Items of this menu are set from controller in data['functionmenu']
	 *
	 * @return string Selector html
	 */
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

	/**
	 * Content for log
	 * Iterates over all log objects in log list
	 *
	 * @return string log html
	 */
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

	/**
	 * Add a warning about development state of extension
	 *
	 * @return string html
	 */
	protected function developmentWarning() {
		$content = '<p style="color:red;">WARNING: This extension is in early development state. DO NOT ADD IMPORTANT OR REAL DATA!</p>';
		return($this->doc->section('Development warning',$content,0,1));
	}

	/**
	 * Main content method
	 * Depending view classes overwrite this method
	 *
	 * @return string html
	 */
	protected function innerContent() {
		return('<p>Inner content</p>');
	}
}
?>
