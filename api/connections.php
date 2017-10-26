<?php
include 'security.php';
require_once 'lang.php';
include 'constants.php';

function open_connection(){
	$dbserver		= "localhost";
	$dbname			= "db_rover";
 	$dbusername		= "posinv";
	$dbpassword		= "#posInv123";
	
	$conn = mysqli_connect( $dbserver, $dbusername, $dbpassword, $dbname );
	
	return $conn;
}

function getJsonDefault(){
	$json = new stdclass();
	
	$json->isSuccess = 0;
	$json->invalidCode = '';
	$json->invalidMessage = '';
	$json->message = '';
	$json->total = 0;
	$json->isExpired = 0;
	$json->row = null;
	$json->rows = null;

	return $json;
}

function close_connection($conn){
	mysqli_close($conn);
}

function gets($conn, $query){
	$result = false;
	$arr = array();
	/*while ($row = mysqli_fetch_assoc($result)) {
        printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
    }*/
	$result = execute($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }
	
	mysqli_free_result($result);

	return $arr;
}
function get($conn, $query){
	$result = false;
	$singleResult = array();
	/*while ($row = mysqli_fetch_assoc($result)) {
        printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
    }*/
	$result = execute($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
        $singleResult = $row;
    }
	
	mysqli_free_result($result);

	return $singleResult;
}

function update($conn, $query){
	$result = execute($conn, $query);
	return $result;
}
function execute($conn, $query){
	$result = mysqli_query($conn,$query) or die(mysqli_error($conn) . " ; query => ".$query);
	return $result;
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

function _isValidParameters($POST){
	if(isset($POST) && !empty($POST)){
		$sessionCode = (isset($POST['sessionCode']) && !empty($POST['sessionCode'])) ? $POST['sessionCode'] : '';
		$numberId = (isset($POST['numberId']) && !empty($POST['numberId'])) ? $POST['numberId'] : '';
		if(!empty($sessionCode) && !empty($numberId)){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}	
}

function encodeHtml($responseData) {

	$htmlResponse = "<table border='1'>";
	foreach($responseData as $key=>$value) {
			$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
	}
	$htmlResponse .= "</table>";
	return $htmlResponse;		
}

function encodeJson($responseData) {
	$jsonResponse = json_encode($responseData);
	return $jsonResponse;		
}

function encodeXml($responseData) {
	// creating object of SimpleXMLElement
	$xml = new SimpleXMLElement('<?xml version="1.0"?><mobile></mobile>');
	foreach($responseData as $key=>$value) {
		$xml->addChild($key, $value);
	}
	return $xml->asXML();
}

function getIsOnline(){
	false;
}


?>
