<?php
// PubNub info (change this to your account keys)
$pn_subkey = "subscribe_key"; // PubNub subscription key
$pn_pubkey = "publish_key"; // PubNub publish key


// Database info (may not need to be changed)
$db_host = "localhost"; // Database host
$db_uname = "root"; // Database username
$db_password = ""; // Database password
$db_database = "acro"; // Database database

$con = mysql_connect($db_host, $db_uname, $db_password) or die ("Could not connect to mysql");

// This creates the database if necessary
if(!mysql_select_db($db_database, $con)){
	// Create the database
	mysql_query("CREATE DATABASE $db_database");
	mysql_select_db($db_database, $con) or die ("Could not select database $db_database");
	// If the table doesn't exist, create
	mysql_query("CREATE TABLE IF NOT EXISTS `acro` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `acronym` varchar(12) DEFAULT NULL,
	  `votes` int(11) DEFAULT '0',
	  `body` varchar(160) DEFAULT NULL,
	  `from` varchar(12) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	");
}

?>