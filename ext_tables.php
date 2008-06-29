<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{
		
	t3lib_extMgm::addModule('user','txpasswordmgrM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}

$tempColumns = Array (
	"tx_passwordmgr_cert" => Array (		
		"config" => Array (
			"type" => "passthrough",
		)
	),
	"tx_passwordmgr_privkey" => Array (		
		"config" => Array (
			"type" => "passthrough",
		)
	),
);


t3lib_div::loadTCA("be_users");
t3lib_extMgm::addTCAcolumns("be_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("be_users","tx_passwordmgr_cert;;;;1-1-1, tx_passwordmgr_privkey");
?>
