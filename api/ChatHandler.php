<?php
require_once("SimpleRest.php");
require_once("connections.php");
require_once("Encryption.php");
require_once("SessionCode.php");

class ChatHandler extends SimpleRest {

	function chatSync(){
		$json = getJsonDefault();
		$LANG = new LANG();
		
		$method = $_SERVER['REQUEST_METHOD'];
		$POST = json_decode(file_get_contents('php://input'),true);
		
		if(_isValidParameters($POST)){
			
			$SessionCode = new SessionCode();
			
			$sessionCode = (isset($POST['sessionCode']) && !empty($POST['sessionCode'])) ? $POST['sessionCode'] : '';
			$numberId = (isset($POST['numberId']) && !empty($POST['numberId'])) ? $POST['numberId'] : '';
			
			if($SessionCode->isValid($sessionCode, $numberId)){
				
				$listRoom = (isset($POST['listRoom']) && !empty($POST['listRoom'])) ? json_decode($POST['listRoom']) : array();
				$listRoomMember = (isset($POST['listRoomMember']) && !empty($POST['listRoomMember'])) ? json_decode($POST['listRoomMember']) : array();
				$listMessage = (isset($POST['listMessage']) && !empty($POST['listMessage'])) ? json_decode($POST['listMessage']) : array();
				$listMessageUser = (isset($POST['listMessageUser']) && !empty($POST['listMessageUser'])) ? json_decode($POST['listMessageUser']) : array();
				$listMessageUserOthers = (isset($POST['listMessageUserOthers']) && !empty($POST['listMessageUserOthers'])) ? json_decode($POST['listMessageUserOthers']) : array();
				$EXPIRED_LIMIT_DATE_MESSAGE = (isset($POST['EXPIRED_LIMIT_DATE_MESSAGE']) && !empty($POST['EXPIRED_LIMIT_DATE_MESSAGE'])) ? $POST['EXPIRED_LIMIT_DATE_MESSAGE'] : 1;
				
				$arrIdMessageChecking = array();
				
				$listResultMessageUserStatus = array();
				$listResultGetMessage = array();
				
				$conn = open_connection();
				
				if($listRoom != null && !empty($listRoom)){
					$query = "INSERT INTO `tb_room` (`id_room`, `room_type`, `group_name`) VALUES ";
					for($i=0; $i<sizeof($listRoom); $i++){
						$model = $listRoom[$i];
						if($i != 0){
							$query .= ", ";
						}
						$query .= "('".$model->idRoom."'
										, '".$model->roomType."'
										, '".$model->groupName."') ";
					}
					$query .= "ON DUPLICATE KEY UPDATE group_name = VALUES(group_name) ";
					update($conn,$query);	
				}
				
				if($listRoomMember != null && !empty($listRoomMember)){
					$query = "INSERT INTO `tb_room_member` (`id_room`, `id_user`, `is_typing`) VALUES ";
					for($i=0; $i<sizeof($listRoomMember); $i++){
						$model = $listRoomMember[$i];
						if($i != 0){
							$query .= ", ";
						}
						$query .= "('".$model->idRoom."'
											, '".$model->contactPhone."'
											, ".(!empty($model->isTyping) ? 1 : 0).") ";
					}
					$query .= "ON DUPLICATE KEY UPDATE is_typing = VALUES(is_typing) ";
					update($conn,$query);
				}
				
				if(isset($listMessage) && !empty($listMessage)){
					$query = "INSERT INTO `tb_message`
							(`id_message`,
							 `id_room`,
							 `id_user`,
							 `message_type`,
							 `message_text`) VALUES ";
					for($i=0; $i<sizeof($listMessage); $i++){
						$model = $listMessage[$i];
						if($i != 0){
							$query .= ", ";
						}
						$query .= "('".$model->idMessage."',
							'".$model->idRoom."',
							'".$model->contactPhoneFrom."',
							'".$model->messageType."',
							'".$model->message."') ";
						$arrIdMessageChecking[] = "'".$model->idMessage."'";
					}
					$query .= "ON DUPLICATE KEY UPDATE message_text = VALUES(message_text) ";
					update($conn,$query);
				}
				
				if(isset($listMessageUser) && !empty($listMessageUser)){
					$query = "INSERT INTO `tb_message_user`
							(`id_message`,
							 `id_user`,
							 `expired_message`) VALUES ";
					for($i=0; $i<sizeof($listMessageUser); $i++){
						$model = $listMessageUser[$i];
						if($i != 0){
							$query .= ", ";
						}
						$query .= "('".$model->idMessage."'
							, '".$model->contactPhone."'
							, NOW()+INTERVAL ".$EXPIRED_LIMIT_DATE_MESSAGE." DAY) ";
					}
					$query .= "ON DUPLICATE KEY UPDATE id_user = VALUES(id_user) ";
					update($conn,$query);
				}
				
				if(isset($listMessageUserOthers) && !empty($listMessageUserOthers)){
					/*$query = "INSERT INTO `tb_message_user`
							(`id_message`,
							 `id_user`,
							 `is_delivered`,
							`is_read`,
							 `expired_message`) VALUES ";
					for($i=0; $i<sizeof($listMessageUserOthers); $i++){
						$model = $listMessageUserOthers[$i];
						if($i != 0){
							$query .= ", ";
						}
						$query .= "('".$model->idMessage."'
							, '".$model->contactPhone."'
							, '".(!empty($model->isDelivered) ? 1 : 0)."'
							, '".(!empty($model->isRead) ? 1 : 0)."'
							, NOW()+INTERVAL ".$EXPIRED_LIMIT_DATE_MESSAGE." DAY) ";
					}
					$query .= "ON DUPLICATE KEY UPDATE is_delivered = VALUES(is_delivered), is_read = VALUES(is_read) ";
					update($conn,$query);*/
					
					for($i=0; $i<sizeof($listMessageUserOthers); $i++){
						$model = $listMessageUserOthers[$i];
						$query = "";
						if(!empty($model->isDelivered)){
							$query = "UPDATE `tb_message_user` SET `is_delivered` = '1' ";
							$query .= "WHERE `id_message` = '".$model->idMessage."' AND `id_user` = '".$model->contactPhone."' ";
						}
						if(!empty($model->isRead)){
							$query = "UPDATE `tb_message_user` SET `is_delivered` = '1',`is_read` = '1' ";
							$query .= "WHERE `id_message` = '".$model->idMessage."' AND `id_user` = '".$model->contactPhone."' ";
						}
						if(!empty($query)){
							update($conn,$query);
						}						
					}
				}
				
				// Check Message Status Which Has Been Sent
				if(isset($arrIdMessageChecking) && !empty($arrIdMessageChecking)){
					$strIdMessageIN = implode(',',$arrIdMessageChecking);	
					$query = "SELECT `id_message`, `id_user` AS contact_phone, `is_delivered`, `is_read` FROM `tb_message_user` WHERE id_message IN($strIdMessageIN) ";
					$rows = gets($conn, $query);
					if(isset($rows) && !empty($rows)){
						$listResultMessageUserStatus = $rows;
					}
					
				}
				
				// Get Message Which Has Been Sent to this Number
				$query = "SELECT a.id_message, a.id_user AS contact_phone, a.is_delivered, a.is_read 
					, b.id_room, b.id_user AS contact_phone_from,  b.message_type, b.message_text AS message
					, c.group_name, c.room_type
					FROM tb_message_user a
					INNER JOIN tb_message b ON a.id_message = b.id_message 
					INNER JOIN tb_room c ON b.id_room = c.id_room 
					WHERE 1=1 
					AND a.id_user = '$numberId' AND a.expired_message >= NOW() AND a.is_delivered = 0";
				$rowsTemp = gets($conn, $query);
				if(isset($rowsTemp) && !empty($rowsTemp)){
					$listResultGetMessage = $rowsTemp;
				}
				
				close_connection($conn);
				
				$json->row = array('listResultMessageUserStatus' => $listResultMessageUserStatus, 'listResultGetNewMessage' => $listResultGetMessage);
				
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