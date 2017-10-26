<?php
require_once("SimpleRest.php");
require_once("connections.php");
require_once("Encryption.php");
require_once("SessionCode.php");

class ContactHandler extends SimpleRest {

	function get(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		if(_isValidParameters($POST)){
			
			$SessionCode = new SessionCode();
			
			$sessionCode = (isset($POST['sessionCode']) && !empty($POST['sessionCode'])) ? $POST['sessionCode'] : '';
			$numberId = (isset($POST['numberId']) && !empty($POST['numberId'])) ? $POST['numberId'] : '';
			
			if($SessionCode->isValid($sessionCode, $numberId)){
				$contacts = (isset($POST['contacts']) && !empty($POST['contacts'])) ? json_decode($POST['contacts']) : '';
				$json->contacts = $contacts;
				
				$conn = open_connection();
				
				if(!empty($contacts)){
					$contactNumbers = array();
					for($i=0; $i<sizeof($contacts); $i++){
						$contact = $contacts[$i];
						$contactNumbers[] = "'".$contact->contactNumberValid."'";
					}
					$contactNumberIn = implode(',',$contactNumbers);
					$query = "SELECT id_user, hp_no, profile_photo FROM tb_user WHERE is_active = 1 AND hp_no IN ($contactNumberIn) ";
					$rows = gets($conn, $query);
					$json->rows = $rows;
				}
				
				close_connection($conn);
				
				$json->isSuccess = 1;
			}
			else{
				$json->invalidCode = SESSION_CODE_INVALID;
				$json->invalidMessage = $LANG->getLang('SESSION_CODE_INVALID');
			}
		}
		else{
			$json->invalidCode = PARAMETER_NOT_VALID;
			$json->invalidMessage = $LANG->getLang('PARAMETER_NOT_VALID');
		}
		
		$statusCode = 200;
		
		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
		
		if(strpos($requestContentType,'application/json') !== false){
			$response = encodeJson($json);
			echo $response;
		}else{
			echo $LANG->getLang('message_access_not_permitted');
		}
	}
	
	private function sendSMS($hpNo, $verification_code, $verification_limit_time){
		
	}
	
	
	
}
?>