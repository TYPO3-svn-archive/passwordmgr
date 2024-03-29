<?php

########################################################################
# Extension Manager/Repository config file for ext: "passwordmgr"
#
# Auto generated 23-08-2008 01:50
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Password Manager',
	'description' => 'Module to store and retrieve passwords',
	'category' => 'module',
	'author' => 'Christian Kuhn',
	'author_email' => 'lolli@schwarzbu.ch',
	'shy' => '',
	'dependencies' => 0,
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.3',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.2.0-0.0.0',
			'0' => 'lang',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:65:{s:9:"ChangeLog";s:4:"dd27";s:21:"ext_conf_template.txt";s:4:"7008";s:12:"ext_icon.gif";s:4:"f0af";s:14:"ext_tables.php";s:4:"9c1b";s:14:"ext_tables.sql";s:4:"13a7";s:16:"locallang_db.xml";s:4:"d41d";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"c7c2";s:14:"mod1/index.php";s:4:"dab9";s:18:"mod1/locallang.xml";s:4:"59df";s:22:"mod1/locallang_mod.xml";s:4:"6bf8";s:19:"mod1/moduleicon.gif";s:4:"f0af";s:41:"model/class.tx_passwordmgr_model_data.php";s:4:"402f";s:49:"model/class.tx_passwordmgr_model_functionmenu.php";s:4:"2339";s:58:"model/class.tx_passwordmgr_model_functionmenu_allitems.php";s:4:"3309";s:68:"model/class.tx_passwordmgr_model_functionmenu_usernotinitialized.php";s:4:"f716";s:42:"model/class.tx_passwordmgr_model_group.php";s:4:"2ce5";s:46:"model/class.tx_passwordmgr_model_grouplist.php";s:4:"9d80";s:48:"model/class.tx_passwordmgr_model_groupmember.php";s:4:"4527";s:52:"model/class.tx_passwordmgr_model_groupmemberlist.php";s:4:"a0c4";s:41:"model/class.tx_passwordmgr_model_list.php";s:4:"4c99";s:40:"model/class.tx_passwordmgr_model_log.php";s:4:"776e";s:44:"model/class.tx_passwordmgr_model_loglist.php";s:4:"6d00";s:47:"model/class.tx_passwordmgr_model_moduledata.php";s:4:"93c6";s:45:"model/class.tx_passwordmgr_model_password.php";s:4:"4e58";s:49:"model/class.tx_passwordmgr_model_passwordlist.php";s:4:"d2d6";s:44:"model/class.tx_passwordmgr_model_ssldata.php";s:4:"6a7b";s:48:"model/class.tx_passwordmgr_model_ssldatalist.php";s:4:"9518";s:41:"model/class.tx_passwordmgr_model_user.php";s:4:"c13c";s:45:"model/class.tx_passwordmgr_model_userdata.php";s:4:"4cfe";s:45:"model/class.tx_passwordmgr_model_userlist.php";s:4:"f190";s:38:"helper/class.tx_passwordmgr_helper.php";s:4:"cbbc";s:39:"helper/class.tx_passwordmgr_openssl.php";s:4:"6765";s:17:"res/decrypted.png";s:4:"4930";s:24:"res/passphrasepopup.html";s:4:"b735";s:24:"res/password_strength.js";s:4:"3b6b";s:20:"res/passwordmgr.html";s:4:"5e99";s:14:"doc/manual.sxw";s:4:"4c3d";s:47:"view/class.tx_passwordmgr_view_addeditgroup.php";s:4:"e7c9";s:53:"view/class.tx_passwordmgr_view_addeditgroupmember.php";s:4:"dc1a";s:50:"view/class.tx_passwordmgr_view_addeditpassword.php";s:4:"dc88";s:51:"view/class.tx_passwordmgr_view_changepassphrase.php";s:4:"e6d0";s:42:"view/class.tx_passwordmgr_view_default.php";s:4:"c4bb";s:52:"view/class.tx_passwordmgr_view_initializekeypair.php";s:4:"c9d9";s:43:"view/class.tx_passwordmgr_view_overview.php";s:4:"7867";s:43:"view/class.tx_passwordmgr_view_settings.php";s:4:"7e2d";s:51:"controller/class.tx_passwordmgr_action_addgroup.php";s:4:"bb3a";s:57:"controller/class.tx_passwordmgr_action_addgroupmember.php";s:4:"22af";s:54:"controller/class.tx_passwordmgr_action_addpassword.php";s:4:"2568";s:59:"controller/class.tx_passwordmgr_action_changepassphrase.php";s:4:"2eb8";s:58:"controller/class.tx_passwordmgr_action_decryptpassword.php";s:4:"2ce7";s:50:"controller/class.tx_passwordmgr_action_default.php";s:4:"d412";s:54:"controller/class.tx_passwordmgr_action_deletegroup.php";s:4:"93e8";s:60:"controller/class.tx_passwordmgr_action_deletegroupmember.php";s:4:"417b";s:57:"controller/class.tx_passwordmgr_action_deletepassword.php";s:4:"9b2d";s:59:"controller/class.tx_passwordmgr_action_deselectpassword.php";s:4:"bcb4";s:52:"controller/class.tx_passwordmgr_action_editgroup.php";s:4:"aef6";s:58:"controller/class.tx_passwordmgr_action_editgroupmember.php";s:4:"3d37";s:55:"controller/class.tx_passwordmgr_action_editpassword.php";s:4:"763f";s:47:"controller/class.tx_passwordmgr_action_fold.php";s:4:"7e32";s:60:"controller/class.tx_passwordmgr_action_initializekeypair.php";s:4:"574f";s:52:"controller/class.tx_passwordmgr_action_interface.php";s:4:"3562";s:55:"controller/class.tx_passwordmgr_action_movepassword.php";s:4:"8904";s:57:"controller/class.tx_passwordmgr_action_selectpassword.php";s:4:"afcc";s:57:"controller/class.tx_passwordmgr_action_updatesettings.php";s:4:"d9c2";}',
);

?>