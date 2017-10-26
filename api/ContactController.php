<?php
require_once("ContactHandler.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "get_contact":
		$handler = new ContactHandler();
		$handler->get();
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>
