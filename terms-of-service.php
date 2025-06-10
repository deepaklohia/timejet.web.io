<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(isset($_SESSION["tjproUser"]) && $_SESSION["tjproUser"] === true){
    $tjproUser = $_SESSION["tjproUser"] ;
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>Timejet | Terms of Service </title>
<!--
	<link rel="stylesheet" href="css/style3.css" type="text/css">
-->
	<?php include('header.php'); ?>
   	<?php if ($tjproUser != true){include('ad.php');  }?>
</head>
<body>
	<nav class="navbar fixed-top navbar-expand-sm bg-light navbar-light">
			<!--
				//<nav class="navbar navbar-expand-sm bg-light navbar-light">
			-->
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
                <a class="nav-item nav-link" href="mgr-login.php">Manager Login</a>
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
				<h2><b>Terms of Service</b></h2>
				<p>
				These terms and conditions ("Agreement") sets forth the general terms and conditions of your use of the "TimeJet" application ("Application", "Service", “App”, or “Add-on”) and any of its related products and services (collectively, "Services"). This Agreement is legally binding between you ("User", "you" or "your") and this Application developer ("Operator", "we", "us" or "our").
				By accessing and using the Application and Services, you acknowledge that you have read, understood, and agree to be bound by the terms of this Agreement. If you are entering into this Agreement on behalf of a business or other legal entity, you represent that you have the authority to bind such entity to this Agreement, in which case the terms "User", "you" or "your" shall refer to such entity. If you do not have such authority, or if you do not agree with the terms of this Agreement, you must not accept this Agreement and may not access and use the Application and Services.
				You acknowledge that this Agreement is a contract between you and the Operator, even though it is electronic and is not physically signed by you, and it governs your use of the Application and Services.
				</p>

				<h4>Acceptance of Terms</h4>
				<p>
				You acknowledge that you have read this Agreement and agree to all its terms and conditions. 
				By accessing and using the Application and Services you agree to be bound by this Agreement. 
				If you do not agree to abide by the terms of this Agreement, you are not authorized to access or 
				use the Application and Services.Specifically, TimeJet can only be installed from the Google Workspace Marketplace 
				and when installing, Google will prompt you to accept this Agreement: “By clicking Continue, you acknowledge that 
				your information will be used in accordance with the terms of service and privacy policy of this application.”				
				</p>

				<h4>Description of Service</h4>
				<p>
				TimeJet is an add-on for Google Sheets that automates time tracking of daily tasks. 
				The Service is offered and provided subject to these Terms and solely for Your business / personal purposes. 
				You may connect to the Service using any Internet browser supported by the Service.
				The Service requires a Google account (Gmail or Google Workspace account) that will be used to access the Service configuration console, and a Google Sheets data which will contain the data processed by the Service.
				You understand and acknowledge that You are solely responsible for obtaining and maintaining the Internet access and all equipment necessary to use the Service, for appropriately configuring Your Google account,
				and for creating and managing its content.
				</p>

				<h4>Disclaimer of Warranty</h4>
				<p>
				The service is provided on an “as is” basis, without warranties of any kind, either express or implied, including, without limitation, implied warranties of title, merchantability, fitness for a particular purpose or non-infringement.
				</p>
				<p>
				We Make NO Warranty That:
				</p>
				<p>
					<li>
					The Service Will Be Error-free Or Uninterrupted (Including, Without Limitation, Interruptions That Occur In The Context Of Regularly Scheduled Maintenance);
					</li>
				</p>
				<p>
					<li>
					Any information or advice obtained by you in connection with the service will be accurate or complete; or
					</li>
				</p>
				<p>
					<li>
					The results of using the service will meet your requirements. Some states do not allow exclusion of an implied warranty, so this disclaimer may not apply to users.
					</li>
				</p>
				<p>

				<h4>Contacting Us</h4>
				If you would like to contact us to understand more about this Agreement or wish to contact us concerning any matter relating to it. 
				<p>
				<p>you may send an email to deepaklohia@live.com.
				contact us <a href="https://docs.google.com/forms/d/e/1FAIpQLScfZ500QXUwWtBzKN4hrl1E4V_1GqortGoFRQe8V0ucLPbOIw/viewform"> link </a>on the page.
				</p>
			</div>
		</div>
	</div>
	<br>
	<br>

	<footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA</a>
			All Rights Reserved
		</div>
	</footer>
</body>
</html>




