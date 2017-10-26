<?php

require_once("connections.php");

class SessionCode{
	
	function isValid($sesionCode, $numberId){
		$isValid = false;
		$conn = open_connection();
		if($conn){
			$query = "SELECT COUNT(*) AS hit_count FROM tb_user WHERE session_code = '$sesionCode' AND hp_no = '$numberId' ";
			$row = get($conn, $query);
			if(!empty($row)){
				if(isset($row['hit_count']) && !empty($row['hit_count']) && $row['hit_count'] > 0){
					$isValid = 1;
				}
			}
			close_connection($conn);
		}
		return $isValid;
	}
	
	function generateCode(){
		$code = "";
		for($i=0; $i<6; $i++){
			$code .= rand(1,9);
		}
		return $code;
	}
	
	function generateSessionCode(){
		$now = microtime(true);
		return sha1(md5($now));
	}
	
}


?>