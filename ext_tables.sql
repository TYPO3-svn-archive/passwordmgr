#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
	tx_passwordmgr_cert blob NOT NULL,
	tx_passwordmgr_privkey blob NOT NULL
);

#
# Table structure for table 'tx_passwordmgr_group'
#
CREATE TABLE tx_passwordmgr_group (
	uid int(11) NOT NULL auto_increment,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	name tinytext NOT NULL,

	PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_passwordmgr_group_be_users_mm'
#
CREATE TABLE tx_passwordmgr_group_be_users_mm (
	group_uid int(11) DEFAULT '0' NOT NULL,
	be_users_uid int(11) DEFAULT '0' NOT NULL,
	rights int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (group_uid,be_users_uid)
);

#
# Table structure for table 'tx_passwordmgr_password'
#
CREATE TABLE tx_passwordmgr_password (
	uid int(11) NOT NULL auto_increment,
	group_uid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	name text NOT NULL,
	link text NOT NULL,
	user text NOT NULL,

	PRIMARY KEY (uid),
	KEY group_uid (group_uid),
);

#
# Table structure for table 'tx_passwordmgr_ssldata'
#
CREATE TABLE tx_passwordmgr_ssldata (
	password_uid int(11) DEFAULT '0' NOT NULL,
	be_users_uid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	sslkey blob NOT NULL,
	ssldata blob NOT NULL,

	PRIMARY KEY (password_uid,be_users_uid)
);
