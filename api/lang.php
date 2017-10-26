<?php

class LANG {
	private $L = array();
	
	function __construct(){
		$this->L['message_access_not_permitted'] = "Sorry, Access is Not Permitted";
		$this->L['message_database_connection_failed'] = "Sorry, Database Connection Failed";
		$this->L['message_parameter_is_invalid'] = "Sorry, parameter is invalid. Please try again.";
		$this->L['message_save_failed'] = "Sorry, Saving Process is Failed. Please Check your field or internet connection and try again.";

		$this->L['message_failed_verification_code'] = "Sorry, Wrong Verification Code. Please fill with correct verification code.";
		$this->L['message_email_exist'] = "Sorry, Your Username has been Exist. Please use other Username.";
		$this->L['message_empty_data'] = "Sorry, Data is Empty";

		$this->L['message_unknown_store_code'] = "Unknown Store Code";
		$this->L['message_wrong_username_password'] = "Wrong Username or Password";
		$this->L['message_username_is_inactive'] = "Username is InActive. Please activated first.";
		
		$this->L['message_s_sms_code'] = "Your Rover SMS Code Verification : {0}";
		
		$this->L['SESSION_CODE_INVALID'] = "Session Invalid";
		$this->L['PARAMETER_NOT_VALID'] = "Sorry, parameter is invalid. Please try again.";
		$this->L['EMAIL_SENT_FAILED'] = "Email send failed !";
		$this->L['DATABASE_CONNECTION_FAILED'] = "Sorry, Database Connection Failed";
		
		$this->L['WRONG_VERIFICATION_CODE'] = "Wrong Verification Code";
		
		$this->L['ERR_CURL_NOT_ACTIVED'] = "CURL Service Not Actived Yet";
		$this->L['ERR_SMS_SENT_FAILED'] = "SMS Sent Failed";
	}
	
	public function getLang($key){
		return $this->L[$key];
	}
	
	public function get(){
		return $this->L;
	}
}

?>
