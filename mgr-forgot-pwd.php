<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["mgrloggedin"]) && $_SESSION["mgrloggedin"] === true){
  header("location: mgr-dash.php");
  exit;
}
 
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$mgr_id =  "";
$mgr_id_err  = $mgr_id_succ  = "";

?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>TimeJet | Manager Forgot Password</title>
    <?php include('header.php'); ?>
</head>
<body>
	
<nav class="navbar fixed-top navbar-expand-sm bg-light navbar-light">
    <div class="container justify-content-center">
        <a class="navbar-brand" href="index.php">
        <img src="images/logo2.png" width="50" height="40" class="d-inline-block align-top" alt="">
        </a>
    
        <a class="navbar-brand mr-auto" href="index.php">TimeJet</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav mr-auto mt-2 mt-lg-0">
            <a class="nav-item nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link" href="emp-login.php">Employee Login</a>
            <a class="nav-item nav-link active" href="mgr-login.php">Manager Login</a>
            <a class="nav-item nav-link" href="privacy-policy.php">Privacy Policy</a>
            <a class="nav-item nav-link" href="https://docs.google.com/forms/d/e/1FAIpQLScfZ500QXUwWtBzKN4hrl1E4V_1GqortGoFRQe8V0ucLPbOIw/viewform">Contact Us</a>
            </div>
        </div>
    </div>
</nav>
    
<br>
<br>
<br>

<div class="container justify-content-center">
    <div class="card">
        <div class="card-body">
            <h4 id="titleH">Forgot Password</h4>
            <p>
                <!--
            <p>Please fill email to send password.</p>
-->
            </p>
            <div id="mgrSec">
                <div class="form-group <?php echo (!empty($mgr_id_err)) ? 'has-error' : ''; ?>">
                    <label>Enter Manager Email</label>
                    <input type="email" name="mgr_id" id="mgr_id_" class="form-control" value="<?php echo $mgr_id; ?>">
                    <span id ="mgr_id_error" class="help-block" style="color:red;"></span>
                    <span id = "mgr_id_success" style="display: block; margin-top: 5px; margin-bottom: 10px;color:green;" ></span>
                </div>    
                
                <div class="form-group">
                    <button type="button" id="btnSend" class="btn btn-primary" onclick="funcSendMsg();">Send</button>
                </div>
                <!--
                <p>Already have an account? <a href="mgr-login.php">Manager Login here</a>.</p>
                -->
            </div>
        </div>
    </div>    
</div>
            
<script>

let emlCtrl = document.getElementById("mgr_id_") ;
let sendButton = document.getElementById("btnSend") ;

emlCtrl.addEventListener('input', function(e){
  if (e.target.value == "") {
    emlCtrl.className  = "form-control border border-danger";
  }
  else{
    emlCtrl.className  = "form-control";
    document.getElementById("mgr_id_error").innerHTML = "" ;
    document.getElementById("mgr_id_success").innerHTML = "" ;
    sendButton.disabled = false ;
    if(sendButton.innerHTML.search("Error") >= 0){
        sendButton.innerHTML = "Retry";
    }
    else{
        //reset to default
        sendButton.innerHTML = "Send";
        sendButton.className = "btn btn-primary" ;
        sendButton.style.backgroundColor = '#007bff' ;
    }
  }
});

function funcSendMsg(){
    //let name = nameCtrl.value ;
    let mgrEmail = emlCtrl.value ;
    
    if (mgrEmail == "" || emailCheck(mgrEmail) == false ){
        emlCtrl.style.borderColor = '#D75C5C';
        //alert ("Please enter email");
        return ;
    }
    
    sendButton.innerHTML = "Sending..." ;
    sendButton.disabled = true ;

    $.ajax({
        type:'POST',
        //mode: 'cors',
        url:'mgr-send-recv-email.php',
        data:{mgrEmail:mgrEmail,
        },
        success:function(response){
            if(response.search("success:") > 0){
                sendButton.disabled = false ;
                sendButton.style.backgroundColor = 'green' ;
                sendButton.innerHTML = "Sent";
                document.getElementById("mgr_id_success").innerHTML = "password link sent.check SPAM folder if not found."
            }else{
                if(response.search("SMTP Error:") >= 0){
                    document.getElementById("mgr_id_error").innerHTML = 'SMTP Error: unable to send e-mail, ensure that your mailbox is available to receive messages.';
                }
                else{
                    document.getElementById("mgr_id_error").innerHTML = rmvSpc(response);
                }
                sendButton.disabled = false ;
                sendButton.style.backgroundColor = '#EE4B2B' ;
                sendButton.innerHTML = "Error..Retry" ;
            }
        }
    });
};

function emailCheck(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};

function rmvSpc(str){
    str = str.replace('success:', '') ;    
    str = str.replace('error:', '') ;
    str = str.replace(/(\r\n|\n|\r)/gm, '') ;
    str = str.replaceAll('\n','') ;
    return str;
  };

</script>

</body>
</html>