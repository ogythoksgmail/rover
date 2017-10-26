<?php
require_once("connections.php");
require_once("Encryption.php");
require_once("SessionCode.php");
//require_once('constants.php');


$dbName = 'postest_2_fnb';
$conn = open_connection_by_dbname($dbName);
if($conn){
	echo "<br/> DATABASE CONNECTION SUCCESS";
}
else{
	echo "<br/> DATABASE CONNECTION FAILED";
}
close_connection($conn);

/////////////////////////////////////////////////////////////////////////////////////


$date = date("Y-m-d");
$date2 = '2017-03-30';

$timeDate = strtotime($date);
$timeDate2 = strtotime($date2);

$addDays = 10;

$date=date_create($date);
date_add($date,date_interval_create_from_date_string($addDays." days"));
echo "<br/> date_format =  ".date_format($date,"Y-m-d");

$dateAdding = date_format(date(strtotime("+$addDays day",strtotime($date))),'Y-m-d');

$datecreate = date_create($date,'Y-m-d H:i:s');

echo "<br/> datecreate =  ".$datecreate;
echo "<br/> timeDate = $timeDate ";
echo "<br/> dateAdding =  ".$dateAdding;

if($timeDate > $timeDate2){
	echo "<br/> date ($date) greater than date2 ($date2) ";
}
else if($timeDate < $timeDate2){
	echo "<br/> date ($date)  lower than date2 ($date2) ";
}
else{
	echo "<br/> date ($date)  equals date2 ($date2)  ";
}

$Enc = new SelfEncryption();
$SessionCode = new SessionCode();

$testEncrypt = "20170329-ba96c8b0b7e049b3fee791d55815c8ce-pos_a039_39_resto_arifin";
echo "<br> word  =  ".$testEncrypt;
echo "<br> word encrypted = ".$Enc->encrypt($testEncrypt);
echo "<br> word decrypted = ".$Enc->decrypt($Enc->encrypt($testEncrypt));

echo "<br> isValid Date  =  ".$SessionCode->isValidSessionCodeLimitTime($Enc,$Enc->encrypt($testEncrypt));





?>