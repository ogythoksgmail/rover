<?php

function escape($string){     
  $string = strip_tags($string); 
  $string = htmlspecialchars($string); 
  $string = trim(rtrim(ltrim($string))); 
  return $string;
}



function getRefNumber($number,$countzero){
	$prefix = 0;
	$refnumber = "".$number;
	$refnumberlength = strlen($refnumber);
	if($refnumberlength < $countzero){
		for ($i = $refnumberlength ; $i < $countzero ; $i++){
			$refnumber = $prefix.$refnumber;
		}
	}
	return $refnumber;
}



?>