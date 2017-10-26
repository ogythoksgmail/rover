<?php 
require "class.phpmailer.php";
//require "open_connection.php";

function send_email_shift_close($arrTo,$arrCc,$field){
	
	$mail             = new PHPMailer();
	$mailserver = get_mail_server();

	$body = file_get_contents('email_shift_close_content.html');
	$body = str_replace('[#name#]', $field->store_name,$body);
	$body = str_replace('[#date#]', $field->business_date,$body);
	$body = str_replace('[#pos_name#]', $field->pos_name,$body);
	$body = str_replace('[#shift#]', $field->shift,$body);
	$body = str_replace('[#opening_balance#]', formatCurrency($field->opening_balance),$body);
	$body = str_replace('[#close_amount#]', formatCurrency($field->close_amount),$body);
	$body = str_replace('[#cash_system_amount#]', formatCurrency($field->cash_system_amount),$body);
	$body = str_replace('[#system_amount#]', formatCurrency($field->system_amount),$body);
	$body = str_replace('[#best_regards_name#]', 'POS Management',$body);
	
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host       = $mailserver->Host; // SMTP server
	$mail->SMTPDebug  = $mailserver->SMTPDebug;                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only
	$mail->SMTPAuth   = $mailserver->SMTPAuth;                  // enable SMTP authentication
	$mail->SMTPSecure = $mailserver->SMTPSecure;                 // sets the prefix to the servier //tls or ssl
	$mail->Port       = $mailserver->Port;                   // set the SMTP port for the GMAIL server // tls => 587 or ssl => 465
	$mail->Username   = $mailserver->Username;  // GMAIL username
	$mail->Password   = $mailserver->Password;            // GMAIL password

	$mail->SetFrom($mailserver->Username, 'POS Management');

	$mail->AddReplyTo($mailserver->Username,"POS Management");

	$mail->Subject    = $field->store_name." Shift Close Information (".$field->pos_name." - ".$field->business_date." - shift ".$field->shift.")";

	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

	$mail->MsgHTML($body);

	$is_success = true;
	
	if(isset($arrTo) && sizeof($arrTo) > 0){
		foreach($arrTo as $key => $email_address){
			if(isset($email_address) && trim($email_address) != ''){
				$mail->AddAddress($email_address, "");
			}
		}
		if(isset($arrCc) && sizeof($arrCc) > 0){
			foreach($arrCc as $key => $cc_email_address){
				if(isset($cc_email_address) && trim($cc_email_address) != ''){
					$mail->AddCC($cc_email_address, "");
				}
			}
		}

		if(!$mail->Send()) {
		  $is_success = false;
		}
		
	}
	return $is_success;
}

function formatCurrency($params) {

	$num = '';
	$is_use_cent = '';
	if(isset($params)){
		if(is_array($params)){
			if(isset ($params['num']) && trim($params['num'])!=''){
				$num = trim($params['num']);
			}
			if(isset($params['is_use_cent']) && trim($params['is_use_cent'])!=''){
				$is_use_cent = $params['is_use_cent'];
			}
		} else {
			if(trim($params)!=''){
				$num = trim($params);
			}
		}
	}

	$num = str_replace('/\$|\,/g','',$num);
   // echo $num;
	if(is_nan($num))
		$num = 0;
	$sign = ($num == ($num = abs($num)));
	$num = floor($num*100+0.50000000001);
	$cents = $num%100;
	$num = floor($num/100);
	if($cents<10)
		$cents = "0" . $cents;
	for ($i = 0; $i < floor( (strlen($num)-(1+$i)) / 3 ); $i++)
		$num = substr($num,0,strlen($num) - (4*$i+3)).','.substr($num,strlen($num)-(4*$i+3));
	
	$return_value = ((($sign)?'':'-') . $num . '.' . $cents);
	if(isset($is_use_cent) && trim($is_use_cent)!=''){
		if($is_use_cent==0){
			$return_value = ((($sign)?'':'-') . $num );
		}
	}
	
	return ((($sign)?'':'-') . $num . '.' . $cents);
//            return ((($sign)?'':'-') . $num);
}


?>

