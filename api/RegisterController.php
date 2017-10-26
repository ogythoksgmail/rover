<?php
require_once("RegisterHandler.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "register_number":
		$handler = new RegisterHandler();
		$handler->registerNumber();
		break;
	
	case "verification_code":
		$handler = new RegisterHandler();
		$handler->checkVerificationCode();
		break;	
		
	case "verification_code_resend":
		$handler = new RegisterHandler();
		$handler->resendVerificationCode();
		break;

	case "profile":
		$handler = new RegisterHandler();
		$handler->saveProfile();
		break;
		
	case "test_sms":
		$handler = new RegisterHandler();
		$handler->testSms();
		break;	
			
	case "" :
		//404 - not found;
		break;
}
?>
