<?php
require_once("ChatHandler.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "sync_chat":
		$handler = new ChatHandler();
		$handler->chatSync();
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>
