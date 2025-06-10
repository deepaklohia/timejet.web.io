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
	<title>Timejet | Privacy Policy</title>
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
                <a class="nav-item nav-link active" href="privacy-policy.php">Privacy Policy</a>
				<a class="nav-item nav-link" href="troubleshoot.php">Troubleshoot</a>
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
				<h2><b>Privacy Policy</b></h2>
				<br>
				<p>	
				Timejet Tracker is a free add-on created for Google Sheets by Deepak Lohia.
				</p>
				<p>	
				1.No personal data is collected by the developer from the add-on.
				</p>
				<p>	
				2.No access to users email is given to the developer from installation of this add-on.
				</p>
				<p>	
				3.No access to users Drive files is given to the developer from installation of this add-on.
				</p>
				<p>	
				4.Reporting through the ‘help’ button in the add-on menu is the best way to request technical support.
				</p>
				<p>	
				5.Any data stored within the add-on is stored on google user account.
				</p>
				<p>	
				6.There is no guarantee for support for the add-on and the add-on could be removed at any time without notice (although this is very unlikely).
				</p>
				<p>	
				7.No guarantee is made for the quality or reliability of the service.
				</p>
				<p>	
				you may use <a href="https://docs.google.com/forms/d/e/1FAIpQLScfZ500QXUwWtBzKN4hrl1E4V_1GqortGoFRQe8V0ucLPbOIw/viewform"> Contact us </a> link for any questions. 
				</p>			
			</div>
		</div>
	</div> 

	<br>
	<br>

	<footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA. </a>
			All Rights Reserved
		</div>
	</footer>
</body>
</html>




