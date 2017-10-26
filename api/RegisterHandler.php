<?php
require_once("SimpleRest.php");
require_once("connections.php");
require_once("Encryption.php");
require_once("SessionCode.php");
require_once('smsclass.php');

class RegisterHandler extends SimpleRest {

	function registerNumber(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		if(isset($POST)){
			$hpNo = (isset($POST['hp_no']) && !empty($POST['hp_no'])) ? $POST['hp_no'] : '';
			$userIdGenerated = (isset($POST['user_id_generated']) && !empty($POST['user_id_generated'])) ? $POST['user_id_generated'] : '';
			
			if(isset($hpNo) && !empty($hpNo) 
				&& isset($userIdGenerated) && !empty($userIdGenerated) ){
				
				$SessionCode = new SessionCode();
				
				$conn = open_connection();
				
				$query = "SELECT id_user, session_code, hp_no, profile_name, profile_photo FROM tb_user WHERE hp_no = '$hpNo' LIMIT 1 ";
				$row = get($conn, $query);
				$verificationCode = "";
				$verificationTimeLimit = VERIFICATION_CODE_TIME_LIMIT." hours from now";
				$sessionCode = "";
				if(getIsOnline()){
					$verificationCode = $SessionCode->generateCode();
				}
				else{
					$verificationCode = DEFAULT_GENERATE_CODE;
				}
				
				if(isset($row) && !empty($row)){
					$query = "UPDATE `tb_user`
						SET `verification_code` = '$verificationCode',
						  `verification_limit_time` = ADDTIME(NOW(),'".VERIFICATION_CODE_TIME_LIMIT.":00:00')
						WHERE `id_user` = '".$row['id_user']."'";
					update($conn, $query);
					$json->row = $row;
				}
				else{
					$sessionCode = $SessionCode->generateSessionCode();
					$query = "INSERT INTO `tb_user`
							(`id_user`,
							 `session_code`,
							 `hp_no`,
							 `verification_code`,
							 `verification_limit_time`)
						VALUES ('$userIdGenerated',
								'$sessionCode',
								'$hpNo',
								'$verificationCode',
								ADDTIME(NOW(),'".VERIFICATION_CODE_TIME_LIMIT.":00:00'))";
					update($conn, $query);
					
					$row = array('id_user' => $userIdGenerated);
					$json->row = $row;
				}
				$json->isSuccess = 1;
				if(getIsOnline()){
					$tempMessage = $LANG->getLang('message_s_sms_code');
					$tempMessage = str_replace('{0}', $verification_code, $tempMessage);
					$json->sms_content = $tempMessage;
			
					$json->is_sms_sent = $this->sendSMS($hpNo, $verificationCode, $verificationTimeLimit);
					/*$hpNoSms = str_replace('+62','0',$hpNo);
					if(!function_exists('curl_version'))
					{
						$json->curl_status = false;
					}
					else{
						$json->curl_status = true;
						$message = $LANG->getLang('message_s_sms_code');
						$message = str_replace('{0}', $verification_code, $message);
						$sms = new smsreguler();
						$sms->username = 'enermous';
						$sms->password = 'ACc7yB';
						$sms->apikey   = '4275a14814af13d2c108b9137781aa2a';
						$sms->setTo($hpNoSms);
						$sms->setText($message);
						$sts=$sms->smssend();
						$idreport=explode('|',$sts);
						setcookie("idreport", $idreport[1], time()+3600);
						if (substr($sts,0,1)=='0') {
							// success
							$json->sms_status = true;;
						}
						else{
							// failed
							$json->sms_status = false;;
						}
					}*/
				}
				
				close_connection($conn);
			}
			else{
				$json->invalidCode = PARAMETER_NOT_VALID;
				$json->invalidMessage = $LANG->getLang('PARAMETER_NOT_VALID');
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
	
	function checkVerificationCode(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		if(isset($POST)){
			$hpNo = (isset($POST['hp_no']) && !empty($POST['hp_no'])) ? $POST['hp_no'] : '';
			$userIdGenerated = (isset($POST['user_id']) && !empty($POST['user_id'])) ? $POST['user_id'] : '';
			$verificationCode = (isset($POST['verification_code']) && !empty($POST['verification_code'])) ? $POST['verification_code'] : '';
			
			if(isset($hpNo) && !empty($hpNo) 
				&& isset($userIdGenerated) && !empty($userIdGenerated) ){
				
				$SessionCode = new SessionCode();
				
				$conn = open_connection();
				
				$query = "SELECT id_user, session_code, hp_no, profile_name, profile_photo FROM tb_user WHERE hp_no = '$hpNo' 
					AND verification_code = '$verificationCode' AND verification_limit_time IS NOT NULL AND verification_limit_time >= now() 
					LIMIT 1 ";
				
				$row = get($conn, $query);
				
				if(isset($row) && !empty($row)){
					$sessionCode = $SessionCode->generateSessionCode();
					$query = "UPDATE `tb_user`
						SET `session_code` = '$sessionCode',
						  `verification_limit_time` = NULL,
						  `verification_code` = NULL,
						  `is_active` = 1
						WHERE `id_user` = '".$row['id_user']."'";
					update($conn, $query);
					
					$row['session_code'] = $sessionCode;
					$json->isSuccess = 1;
					$json->row = $row;
				}
				else{
					$json->invalidCode = WRONG_VERIFICATION_CODE;
					$json->invalidMessage = $LANG->getLang('WRONG_VERIFICATION_CODE');
				}
				
				close_connection($conn);
			}
			else{
				$json->invalidCode = PARAMETER_NOT_VALID;
				$json->invalidMessage = $LANG->getLang('PARAMETER_NOT_VALID');
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
	
	function resendVerificationCode(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		if(isset($POST)){
			$hpNo = (isset($POST['hp_no']) && !empty($POST['hp_no'])) ? $POST['hp_no'] : '';
			$userIdGenerated = (isset($POST['user_id']) && !empty($POST['user_id'])) ? $POST['user_id'] : '';
			
			if(isset($hpNo) && !empty($hpNo) 
				&& isset($userIdGenerated) && !empty($userIdGenerated) ){
				
				$SessionCode = new SessionCode();
				
				$conn = open_connection();
				
				$query = "SELECT id_user, session_code, hp_no, profile_name, profile_photo FROM tb_user WHERE id_user = '$userIdGenerated' LIMIT 1 ";
				$row = get($conn, $query);
				
				if(isset($row) && !empty($row)){
					
					$verificationCode = "";
					$verificationTimeLimit = VERIFICATION_CODE_TIME_LIMIT." hours from now";
					if(getIsOnline()){
						$verificationCode = $SessionCode->generateCode();
					}
					else{
						$verificationCode = DEFAULT_GENERATE_CODE;
					}
					
					$query = "UPDATE `tb_user`
						SET `verification_code` = '$verificationCode',
						  `verification_limit_time` = ADDTIME(NOW(),'".VERIFICATION_CODE_TIME_LIMIT.":00:00')
						WHERE `id_user` = '".$row['id_user']."'";
					update($conn, $query);
					
					$json->is_sms_sent = $this->sendSMS($hpNo, $verificationCode, $verificationTimeLimit);
					
					$json->isSuccess = 1;
				}
				else{
					$json->invalidCode = PARAMETER_NOT_VALID;
					$json->invalidMessage = $LANG->getLang('PARAMETER_NOT_VALID');
				}	

				close_connection($conn);				
			}
			else{
				$json->invalidCode = PARAMETER_NOT_VALID;
				$json->invalidMessage = $LANG->getLang('PARAMETER_NOT_VALID');
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
	
	function saveProfile(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		if(_isValidParameters($POST)){
			
			$SessionCode = new SessionCode();
			
			$sessionCode = (isset($POST['sessionCode']) && !empty($POST['sessionCode'])) ? $POST['sessionCode'] : '';
			$numberId = (isset($POST['numberId']) && !empty($POST['numberId'])) ? $POST['numberId'] : '';
			
			if($SessionCode->isValid($sessionCode, $numberId)){
				$profileName = (isset($POST['profileName']) && !empty($POST['profileName'])) ? $POST['profileName'] : '';
				$query = "UPDATE tb_user SET profile_name = '$profileName' WHERE  hp_no = '$numberId'";
				$conn = open_connection();
				update($conn,$query);
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
	
	function testSms(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		$numberId = (isset($POST['hp_no']) && !empty($POST['hp_no'])) ? trim($POST['hp_no']) : '';
		$message = (isset($POST['message']) && !empty(trim($POST['message']))) ? trim($POST['message']) : '';
		
		if(!empty($numberId) && !empty($message)){
			$numberId = str_replace('+62','0',$numberId);
			if(!function_exists('curl_version'))
			{
				$json->invalidCode = ERR_CURL_NOT_ACTIVED;
				$json->invalidMessage = $LANG->getLang('ERR_CURL_NOT_ACTIVED');
			}
			else{
				/*$sms = new smsreguler();
				$sms->username = 'enermous';
				$sms->password = 'ACc7yB';
				$sms->apikey   = '4275a14814af13d2c108b9137781aa2a';
				$sms->setTo($numberId);
				$sms->setText($message);
				$sts=$sms->smssend();
				$idreport=explode('|',$sts);
				setcookie("idreport", $idreport[1], time()+3600);
				if (substr($sts,0,1)=='0') {
					$json->isSuccess = 1;
				} else {	
					$json->invalidCode = ERR_SMS_SENT_FAILED;
					$json->invalidMessage = $LANG->getLang('ERR_SMS_SENT_FAILED');
				}*/
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
		$LANG = new LANG();
		$hpNo = str_replace('+62','0',$hpNo);
		if(!function_exists('curl_version'))
		{
			return false;
			//echo "CURL inactive";
		}
		else{
			$message = $LANG->getLang('message_s_sms_code');
			$message = str_replace('{0}', $verification_code, $message);
			$sms = new smsreguler();
			$sms->username = 'enermous';
			$sms->password = 'ACc7yB';
			$sms->apikey   = '4275a14814af13d2c108b9137781aa2a';
			$sms->setTo($hpNo);
			$sms->setText($message);
			$sts=$sms->smssend();
			$idreport=explode('|',$sts);
			setcookie("idreport", $idreport[1], time()+3600);
			if (substr($sts,0,1)=='0') {
				// success
				return true;
				//echo "SUCCESS";
			}
			else{
				// failed
				return false;
				//echo "FAILED";
			}
		}
	}
	
	
	
	
	
}
?>