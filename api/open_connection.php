<?php
error_reporting(E_ALL ^ E_DEPRECATED);

include 'security.php';
function get_server_admin(){
	//return "http://dev.isoftdimension.co.id/poswebtest";
	return "http://192.168.1.106:8081/testing";
}
function open_connection(){
	$dbserver			= escape($_POST['dbserver']);
	//$dbserver			= "localhost";
	$dbname			= escape($_POST['dbname']);
 	$dbusername		= "posinv";
	$dbpassword		= "#posInv123";
	
	$conn = mysql_connect( $dbserver, $dbusername, $dbpassword );
	$db = mysql_select_db( $dbname );
	
	return $conn;
}
function open_connection_master(){
	//$server = escape($_POST['server']);
	$server = "localhost";
	$dbusername		= "posinv";
	$dbpassword		= "#posInv123";
	$dbname			= "postest_mt";
	
	$conn = mysql_connect( $server, $dbusername, $dbpassword );
	$db = mysql_select_db( $dbname );
	
	return $conn;
}
function close_connection($conn){
	mysql_close($conn);
}
function get_root(){
	return "/var/lib/tomcat7/webapps/poswebtest";
}


function get_mail_server(){
	$mailserver;
	$mailserver->Host       = "rsb18.rhostbh.com"; // SMTP server //rsb18.rhostbh.com
	$mailserver->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only
	$mailserver->SMTPAuth   = true;                  // enable SMTP authentication
	$mailserver->SMTPSecure = "ssl";                 // sets the prefix to the servier //tls or ssl
	$mailserver->Port       = 465;                   // set the SMTP port for the GMAIL server // tls => 587 or ssl => 465
	$mailserver->Username   = "pos_no_reply@isoftdimension.co.id";  // GMAIL username
	$mailserver->Password   = "8tXvnomLUR5iHoAnZpO5";            // GMAIL password
	$mailserver->From = "pos_no_reply@isoftdimension.co.id";
	
	
	return $mailserver;
}

define("ACTION_GET","0");
define("ACTION_UPDATE","1");

define("SYNC_ORDER_UP_CHECK_UNFINISHED_CO","get_check_unfinished_co");

define("SYNC_ORDER_UP_STATUS_INFO_INSERT","insert");
define("SYNC_ORDER_UP_STATUS_INFO_INSERT_WITHOUT_KITCHEN","insert_without_kitchen");
define("SYNC_ORDER_UP_STATUS_INFO_DELETE","delete");
define("SYNC_ORDER_UP_STATUS_INFO_DELETE_WITHOUT_KITCHEN","delete_without_kitchen");

?>
