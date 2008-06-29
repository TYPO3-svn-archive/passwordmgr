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
 * Class 'overview' for the 'passwordmgr' extension.
 *
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package	TYPO3
 * @subpackage	tx_passwordmgr
 */
class tx_passwordmgr_view_initializeKeyPair extends tx_passwordmgr_view_default {
	/**
	 * Build content for the initialize key pair view
	 *
	 * @return string html
	 */
	protected function innerContent () {
		$this->doc->bodyTagAdditions = 'onload="testPassword(\'\')"';
		// Add password strength check js-function
		$content = '<script src="'.$GLOBALS['BACK_PATH'].$GLOBALS['temp_modPath'].'../res/password_strength.js" type="text/javascript"></script>';
		$content .= '
			<table border="0" cellpadding="2" cellspacing="1">
				<tr>
					<td class="bgColor4"><strong>New master password:</strong><br />Minimum 6 Characters</td>
					<td class="bgColor4">
						<input type="password" name="DATA[tx_passwordmgr_password1]" onkeyup="testPassword(this.value);" />
					</td>
				</tr>
				<tr>
					<td class="bgColor4"><strong>Confirm master password:</strong></td>
					<td class="bgColor4">
						<input type="password" name="DATA[tx_passwordmgr_password2]" />
					</td>
				</tr>
				<tr>
					<td class="bgColor4"><strong>Password strength:</strong></td>
					<td class="bgColor4">
						<p id="pwStrength"></p>
					</td>
				</tr>
				<tr>
					<td class="bgColor4"></td>
					<td class="bgColor4" align="right">
						<input type="submit" name="mysubmit" value="Create your master password" onclick="setAction(\'initializeKeyPair\');" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="DATA[tx_passwordmgr_action]" value="" />
		';

		return($this->doc->section('Create your master password',$content,0,1));
	}
}
?>
