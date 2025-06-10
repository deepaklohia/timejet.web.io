<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["emploggedin"]) && $_SESSION["emploggedin"] === true){
  header("location: emp-dash.php");
  exit;
}

require_once "config.php";
 
// Define variables and initialize with empty values
$emp_id =  $mgr_id = "";
$emp_id_err =  $mgr_id_err  = $emp_id_succ="" ; 

date_default_timezone_set('Asia/Colombo');

// Processing form data when form is submitted
/*
if($_SERVER["REQUEST_METHOD"] == "POST"){
  
}
*/
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>TimeJet | Employee Forgot Password</title>
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
            <a class="nav-item nav-link active" href="emp-login.php">Employee Login</a>
            <a class="nav-item nav-link" href="mgr-login.php">Manager Login</a>
            <a class="nav-item nav-link" href="privacy-policy.php">Privacy Policy</a>
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
            <h5>Forgot Password</h5>
            <p>
            Please fill email to send password.
            </p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                
                <div class="form-group <?php echo (!empty($mgr_id_err)) ? 'has-error' : ''; ?>">
                    <label>Manager Email</label>
                    <input type="email" name="mgr_id" id="mgr_id_" class="form-control" value="<?php echo $mgr_id; ?>">
                    <span id ="mgr_id_error" class="help-block" style="color:red;"></span>
                </div>  
                
                <div class="form-group <?php echo (!empty($emp_id_err)) ? 'has-error' : ''; ?>">
                    <label>Employee Email</label>
                    <input type="email" name="emp_id" id="emp_id_" class="form-control" value="<?php echo $emp_id; ?>">
                    <span id = "emp_id_error" class="help-block" style="color:red;"></span>
                    <span id = "emp_id_success" style="display: block; margin-top: 5px; margin-bottom: 10px;color:green;" ><?php echo $emp_id_succ; ?></span>
                </div>    

                <div class="form-group">
                    <button type="button" id="btnSend" class="btn btn-primary" onclick="funcSendMsg();">Send</button>
                </div>
                <!--
                <p>Don't have an account? <a href="emp-register.php">Employee Sign up</a>.</p>
                -->
            </form>
        </div>    
    </div>
</div>

<script>
    let mgrEmailCtrl = document.getElementById("mgr_id_") ;
    let empEmailCtrl = document.getElementById("emp_id_") ;
    let sendButton = document.getElementById("btnSend") ;

    mgrEmailCtrl.addEventListener('input', function(e){
        if (e.target.value == "") {
            mgrEmailCtrl.style.borderColor = '#D75C5C';
        }
        else{
            mgrEmailCtrl.style.borderColor = '#ced4da';
            document.getElementById("mgr_id_error").innerHTML = "" ;
            resetMsgs();            
        }
    });

    empEmailCtrl.addEventListener('input', function(e){
        if (e.target.value == "") {
            empEmailCtrl.style.borderColor = '#D75C5C';
        }
        else{
            empEmailCtrl.style.borderColor = '#ced4da';
            document.getElementById("emp_id_error").innerHTML = "" ;
            resetMsgs();
        }
    });

    function resetMsgs(){
        document.getElementById("emp_id_success").innerHTML = "" ;
        sendButton.disabled = false ;
        sendButton.innerHTML = "Send";
        sendButton.className = "btn btn-primary" ;
        sendButton.style.backgroundColor = '#007bff' ;
        /*
        if(sendButton.innerHTML.search("Error") >= 0){
            sendButton.innerHTML = "Retry";
        }
        */
    }
    
    function funcSendMsg(){
        //let name = nameCtrl.value ;
        let mgrEmail = mgrEmailCtrl.value ;
        let empEmail = empEmailCtrl.value ;

        let sendButton = document.getElementById("btnSend") ;

        if (mgrEmail == "" || emailCheck(mgrEmail) == false || empEmail == "" || emailCheck(empEmail) == false ){

            if (mgrEmail == "" || emailCheck(mgrEmail) == false ){
                mgrEmailCtrl.style.borderColor = '#D75C5C';
                document.getElementById("mgr_id_error").innerHTML = "blank or invalid email";
            }
            if (empEmail == "" || emailCheck(empEmail) == false ){
                empEmailCtrl.style.borderColor = '#D75C5C';
                document.getElementById("emp_id_error").innerHTML = "blank or invalid email";
            }
            return ;
        }
        
        sendButton.innerHTML = "Sending..." ;
        sendButton.disabled = true ;

        $.ajax({
            type:'POST',
            url:'emp-send-recv-email.php',
            data: ({
                mgrEmail:mgrEmail,
                empEmail:empEmail
            }),
            cache: false,
            success:function(data){
                //alert(response);
                if(data.search("success:") > 0){
                    sendButton.disabled = false ;
                    sendButton.style.backgroundColor = 'green' ;
                    sendButton.innerHTML = "Sent";
                    //alert("password link sent. check SPAM folder if not found")
                    document.getElementById("emp_id_success").innerHTML = "password link sent.check SPAM folder if not found."
                }else{
                    if(data.search("manager") >= 0){
                        document.getElementById("mgr_id_error").innerHTML = rmvSpc(data);
                    }
                    else{
                        if(data.search("SMTP Error:") >= 0){
                            document.getElementById("emp_id_error").innerHTML = 'SMTP Error: unable to send e-mail, ensure that your mailbox is available to receive messages.';
                            //document.getElementById("emp_id_error").innerHTML = rmvSpc(response);
                        }
                        else{
                            document.getElementById("emp_id_error").innerHTML = rmvSpc(data);
                        }                        
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
         
    <footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA. </a>
			All Rights Reserved
		</div>
	</footer> 

</body>
</html>