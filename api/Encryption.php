<?php

class SelfEncryption{
	
	private $alphabets;
	private $numbers;
	private $constEncrypted;
	
	function __construct(){
		$this->alphabets = "abcdefghijklmnopqrstuvwxyz^&*+";
		$this->numbers = "0123456789!@#%";
		$this->constEncrypted = 4;
	}
	
	function encrypt($word){
		
		$alphabets = $this->alphabets;
		$numbers = $this->numbers;
		$constEncrypted = $this->constEncrypted;
		
		$result = "";
		for($i=0; $i<strlen($word); $i++){
			$charTarget = $word[$i];
			$isFound = false;
			$alphabetIndexTarget = -1;
			$numberIndexTarget = -1;
			for($j=0; $j<(strlen($alphabets)-$constEncrypted); $j++){
				if($charTarget == $alphabets[$j]){
					$alphabetIndexTarget = $j;
					$isFound = true;
				}
			}
			if(!$isFound){
				for($j=0;$j<(strlen($numbers)-$constEncrypted);$j++){
					if($charTarget == $numbers[$j]){
						$numberIndexTarget = $j;
						$isFound = true;
					}
				}
			}
			if($alphabetIndexTarget >= 0){
				$result .= $alphabets[$alphabetIndexTarget+$constEncrypted];
			}
			else if($numberIndexTarget >= 0){
				$result .= $numbers[$numberIndexTarget+$constEncrypted];
			}
			else{
				if($charTarget=='-'){
					$result .= "(";
				}
				else if($charTarget=='_'){
					$result .= ")";
				}
				else{
					$result .= $charTarget;	
				}
			}
		}
		
		return $result;
		
	}

	function decrypt($word){
		$alphabets = $this->alphabets;
		$numbers = $this->numbers;
		$constEncrypted = $this->constEncrypted;
		
		$result = "";
		for($i=0; $i<strlen($word); $i++){
			$charTarget = $word[$i];
			$isFound = false;
			$alphabetIndexTarget = -1;
			$numberIndexTarget = -1;
			for($j=0; $j<(strlen($alphabets)); $j++){
				if($charTarget == $alphabets[$j]){
					$alphabetIndexTarget = $j;
					$isFound = true;
				}
			}
			if(!$isFound){
				for($j=0;$j<(strlen($numbers));$j++){
					if($charTarget == $numbers[$j]){
						$numberIndexTarget = $j;
						$isFound = true;
					}
				}
			}
			if($alphabetIndexTarget >= 0){
				$result .= $alphabets[$alphabetIndexTarget-$constEncrypted];
			}
			else if($numberIndexTarget >= 0){
				$result .= $numbers[$numberIndexTarget-$constEncrypted];
			}
			else{
				if($charTarget=='('){
					$result .= "-";
				}
				else if($charTarget==')'){
					$result .= "_";
				}
				else{
					$result .= $charTarget;	
				}
			}
		}
		
		return $result;
	}

}




?>