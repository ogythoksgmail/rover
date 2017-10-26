<?php
require_once("DataMasterHandler.php");
require_once("ShiftEndHandler.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "syncpaymenttype":
		$handler = new DataMasterHandler();
		$handler->getPaymentTypes();
		break;
		
	case "synccurrency":
		$handler = new DataMasterHandler();
		$handler->getCurrencies();
		break;
	
	case "syncmoneydimension":
		$handler = new DataMasterHandler();
		$handler->getMoneyDimensions();
		break;
		
	case "syncpos":
		$handler = new DataMasterHandler();
		$handler->getPoses();
		break;	

	case "synctable";
		$handler = new DataMasterHandler();
		$handler->getTables();
		break;
		
	case "synckitchen";
		$handler = new DataMasterHandler();
		$handler->getKitchens();
		break;

	case "syncshiftend_down":
		$handler = new ShiftEndHandler();
		$handler->getShiftEnd();
		break;
	
	case "sync_master_unit":
		$handler = new DataMasterHandler();
		$handler->getUoms();
		break;
	
	case "sync_master_brand":
		$handler = new DataMasterHandler();
		$handler->getBrands();
		break;
		
	case "sync_master_stock_type":
		$handler = new DataMasterHandler();
		$handler->getStockTypes();
		break;	
		
		
	
	
		
	case "" :
		//404 - not found;
		break;
}
?>
