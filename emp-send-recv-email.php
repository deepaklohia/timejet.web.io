
<?php 
/*
ini_set('display_errors', 1);
error_reporting(E_ALL);
*/

require_once "config.php";

$errorStatus = "" ;
$mgr_id = "";
$mgr_id_err = $mgr_id_succ = "";
$emp_name = "" ;

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    $mgr_id = trim($_POST["mgrEmail"]);
    $emp_id = trim($_POST["empEmail"]);
    
    // Check if Manager username is empty
    if(empty($mgr_id)){
        $errorStatus = "Please enter manager email.";
    }
    else if(empty($emp_id)){
        $errorStatus = "Please enter employee email.";
    } 

    //validate manager email
    if(empty($errorStatus)){
        // Prepare a select statement
        $sql = "SELECT id FROM tj_login WHERE user_type = 'mgr' AND mgr_id = ?" ;
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $mgr_id);
            
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                    //check if managar email is found
                    if (mysqli_stmt_num_rows($stmt) == 0){
                        //$errorStatus = "manager email not found";
                        $errorStatus = "manager email not found. use <a href='mgr-register.php'>Manager Sign-up</a> to register. ";
                    }
            } else{
                $errorStatus = "Oops! Something went wrong. Please try again later." ;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    if(empty($errorStatus)){

        $sql = "SELECT id FROM tj_login WHERE mgr_id = ? AND emp_id = ? AND user_type = 'emp'";         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $mgr_id , $emp_id);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 0 ){                    
                    //$errorStatus = "employee email not found";
                    $errorStatus = "employee email not found .use <a href='emp-register.php'>Employee Signup </a> first";
                }
            }
            else{
                $errorStatus = "Oops! Something went wrong. Please try again later." ;
            }
        }
        mysqli_stmt_close($stmt);
    }

    //ALL OK SENDING EMAIL FOR RESET PASSWORD
    if(empty($errorStatus) ){
        $sql = "SELECT emp_id, emp_name FROM tj_login where mgr_id = ? AND emp_id = ? AND user_type = 'emp'" ;
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $mgr_id, $emp_id );

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1 ){    
                
                    //Binding values in result to variables
                    mysqli_stmt_bind_result($stmt, $emp_id_rslt, $emp_name_rslt);
                    mysqli_stmt_fetch($stmt) ; 
                    $emp_name = $emp_name_rslt ;
                    mysqli_stmt_free_result($stmt);
                    mysqli_stmt_close($stmt);
                    
                    $sql = "UPDATE tj_login SET forgot_pass_identity = ?, reset_password = '1' where mgr_id = ? AND emp_id = ? AND user_type = 'emp' " ;

                    if($stmt = mysqli_prepare($conn, $sql)){
                        $uniqidStr = md5(uniqid(mt_rand()));
                        mysqli_stmt_bind_param($stmt, "sss", $uniqidStr , $mgr_id, $emp_id);
                        //  $reset_token = time() . md5($email);

                        mysqli_stmt_execute($stmt) ;
                        
                        //send email
                        //require 'PHPMailer/PHPMailerAutoload.php';
                        //send email
                        
                        #use PHPMailer\PHPMailer\PHPMailer;
                        #use PHPMailer\PHPMailer\SMTP;
                        #use PHPMailer\PHPMailer\Exception;
                        
                        
                        require_once 'PHPMailer2/Exception.php';
                        require_once 'PHPMailer2/PHPMailer.php';
                        require_once 'PHPMailer2/SMTP.php';
                        //require_once 'PHPMailer2/vendor/autoload.php';
                        
                        $resetPassLink = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
                        $resetPassLink .= $_SERVER["SERVER_NAME"].dirname($_SERVER["PHP_SELF"]);        
                        $resetPassLink .= '/emp-reset-pwd-web.php?fp_code='.$uniqidStr ;
                        
                        //************************************* */
                        $id = 'notify-email@gmail.com' ;
                        $pass = 'vE@bex-bindeq-m8mwo7';
                        //************************************* */

                        //send reset password email
                        $to = $emp_id;
                        $subject = "dlohia | timejet : Password Reset Request" ;
                        $mailBody = 'Dear '.$emp_name.', 
                        <br><br>Recently a request was submitted to reset your password. If this was a mistake, just ignore this email.
                        <br><br><a href="'.$resetPassLink.'"><b>Click here</b></a> to reset your password.
                        <br><br>Regards,
                        <br>DLA | TimeJet ';

                        $mail = new PHPMailer\PHPMailer\PHPMailer();

                        $mail->isSMTP();                                   // Set mailer to use SMTP
                        $mail->Host = 'smtp.office365.com';                    // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true ;                            // Enable SMTP authentication
                        $mail->Username = $id ;   // SMTP username
                        $mail->Password = $pass ;                     // SMTP password
                        $mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;                                 // TCP port to connect to
                        //$mail->SMTPDebug = 2;

                        $mail->setFrom($id, 'DLA | TimeJet');
                        $mail->addReplyTo($id, 'DLA | TimeJet');
                        $mail->addAddress($to);   // To Email 
                        //$mail->addCC('cc@example.com');
                        $mail->addBCC($id);

                        $mail->isHTML(true);  // Set email format to HTML

                        $mail->Subject = $subject ;
                        $mail->Body = $mailBody ;

                        if(!$mail->send()){
                            $errorStatus = $mail->ErrorInfo ;
                        } 
                    }
                }
                else if (mysqli_stmt_num_rows($stmt) > 1 ){
                    $errorStatus = "unknown error, user might have multiple accounts linked . contact admin" ; 
                }
                else{
                    $errorStatus = "employee / mgr id mismatch. one of the ids is incorrect or under different user ." ;
                }
                mysqli_stmt_close($stmt);
            }
            else{
                $errorStatus = "Oops! Something went wrong. Please try again later.";
            }
        }
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