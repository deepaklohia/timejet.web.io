<?php 
header("Access-Control-Allow-Origin: *");
require_once "config.php";

$errorStatus = "" ;
$mgr_id = "";
$mgr_name = "";
//$mgr_name_rslt = "" ;
//$id = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mgr_id =  trim($_POST["mgrEmail"]);

    // Check if username is empty
    if(empty($mgr_id)){
        $errorStatus = "Please enter manager email.";
    } else {
        $sql = "SELECT id, mgr_name FROM tj_login where mgr_id = ? AND user_type = 'mgr'" ;
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $mgr_id );
            //$param_mgrusername = $mgr_id ; 

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                //if there is user found
                if(mysqli_stmt_num_rows($stmt) == 1 ){
                    
                    //Binding values in result to variables
                    mysqli_stmt_bind_result($stmt, $id, $mgr_name_rslt);
                    mysqli_stmt_fetch($stmt) ; 
                    $mgr_name = $mgr_name_rslt ;
                    mysqli_stmt_free_result($stmt);
                    mysqli_stmt_close($stmt);
                                            
                    $sql = "UPDATE tj_login SET forgot_pass_identity = ?, reset_password = '1' WHERE mgr_id = ? AND user_type = 'mgr'" ;

                    if($stmt = mysqli_prepare($conn, $sql)){
                        $uniqidStr = md5(uniqid(mt_rand()));
                        mysqli_stmt_bind_param($stmt, "ss", $uniqidStr , $mgr_id);
                        
                        //$reset_token = time() . md5($email);
                        //$param_temp_pwd = $uniqidStr ;
                        //$param_mgr_id = $mgr_id ; 
                        mysqli_stmt_execute($stmt) ;
                    
                         
                        #use PHPMailer\PHPMailer\PHPMailer;
                        #use PHPMailer\PHPMailer\SMTP;
                        #use PHPMailer\PHPMailer\Exception;
 
                        //send email
                        //require 'PHPMailer/PHPMailerAutoload.php';
                        require_once 'PHPMailer2/Exception.php';
                        require_once 'PHPMailer2/PHPMailer.php';
                        require_once 'PHPMailer2/SMTP.php';
                        
                        $resetPassLink = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
                        $resetPassLink .= $_SERVER["SERVER_NAME"].dirname($_SERVER["PHP_SELF"]);        
                        $resetPassLink .= '/mgr-reset-pwd-web.php?fp_code='.$uniqidStr ;
                        
                        //************************************* */
                        $id = 'notify-email@gmail.com' ;
                        $pass = 'vE@bex-bindeq-m8mwo7';
                        //************************************* */

                        //send reset password email
                        $to = $mgr_id;
                        $subject = "dlohia | timejet : Password Reset Request" ;
                        $mailBody = 'Dear '.$mgr_name.', 
                        <br><br>Recently a request was submitted to reset your password. If this was a mistake, just ignore this email.
                        <br><br><a href="'.$resetPassLink.'"><b>Click here</b></a> to reset your password.
                        <br><br>Regards,
                        <br>DLA | TimeJet ';

                        //$mail = new PHPMailer;
                        $mail = new PHPMailer\PHPMailer\PHPMailer();

                        $mail->isSMTP();                                   // Set mailer to use SMTP
                        $mail->Host = 'smtp.office365.com';                    // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true ;                            // Enable SMTP authentication
                        $mail->Username = $id ;   // SMTP username
                        $mail->Password = $pass ;                     // SMTP password
                        $mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;                                 // TCP port to connect to

                        $mail->setFrom($id, 'DLA | TimeJet');
                        $mail->addReplyTo($id, 'DLA | TimeJet');
                        $mail->addAddress($to);   // To Email 
                        //$mail->addCC('cc@example.com');
                        $mail->addBCC($id);

                        $mail->isHTML(true);  // Set email format to HTML

                        $mail->Subject = $subject ;
                        $mail->Body = $mailBody;

                        if(!$mail->send()) {
                            $errorStatus = $mail->ErrorInfo;
                        }  
                    } 
                    /*
                    else {
                        $mgr_id_succ = "Password reset link has been sent to your registered email. Check junk folder if not received. " ;  
                        //echo '<script>document.getElementById("btnSubmit").innerHTML = "Email sent";</script>';
                    }
                    */
                }
                else if (mysqli_stmt_num_rows($stmt) > 1 ){
                    $errorStatus = "unknown error, user might have multiple accounts linked .  contact admin" ; 
                }
                else{
                    $errorStatus = "manager email not found. use <a href='mgr-register.php'>Manager Sign-up</a> to register. ";
                }
            }
        }
        else{
            //echo "Oops! Something went wrong. Please try again later.";
            $errorStatus = "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}

if (empty($errorStatus)){
    echo "success:";
}
else{
    echo "error:".$errorStatus ;
}

?>