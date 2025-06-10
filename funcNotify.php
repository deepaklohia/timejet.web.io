
<?php 
 
 $errorStatus = "" ;

 if($_SERVER["REQUEST_METHOD"] == "POST"){

	$isMgr = $_POST["isMgr"] ;
	$userID1 =  $_POST["userID1"] ;
	$userID2 =  $_POST["userID2"] ;

	//************************************* */
	$to = 'youremail@gmail.com';
	$id = 'text@outlook.com' ;
	$pass = 'vEabfd-mimwo7';
	//************************************* */

	if ($isMgr){
		$subject = "dlohia | timejet : New Manager Signup" ;
		$mailBody = 'Dear Deepak Lohia,
		<br><br>
		a new manager has signed up using id <a href="mailto:'.$userID1.'">'.$userID1.'</a>.
		<br><br>
		<br><br>Regards,
		<br>DLA | TimeJet ';
	}
	else{
		$subject = "dlohia | timejet : New Employee Signup" ;
		$mailBody = 'Dear Deepak Lohia,
		<br><br>
		a new employee ('.$userID1.') has signed up under manager ('.$userID2.').
		<br><br>Regards,
		<br>DLA | TimeJet ';
	}


	//send email
	require 'PHPMailer/PHPMailerAutoload.php';
	 
	$mail = new PHPMailer;

	$mail->isSMTP();                                   // Set mailer to use SMTP
	$mail->Host = 'smtp.office365.com';             // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                            // Enable SMTP authentication
	$mail->Username = $id ;   // SMTP username
	$mail->Password = $pass ;                     // SMTP password
	$mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                 // TCP port to connect to


	$mail->setFrom($id, 'DLA | TimeJet');
	//$mail->addReplyTo($userID1, 'DLA | TimeJet');
	$mail->addAddress($to);   // To Email 
	$mail->addCC($id);
	$mail->isHTML(true);  // Set email format to HTML

	$mail->Subject = $subject ;
	$mail->Body = $mailBody;

	if(!$mail->send()) {
		$errorStatus = $mail->ErrorInfo;; 
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
	}  
}

if (empty($errorStatus)){
	echo "success:";
}
else{
	echo "error:".$errorStatus ;
}
?>