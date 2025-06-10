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
	<title>Timejet | Welcome </title>

	<?php include('header.php'); ?>
    <div id="ad">
        <?php if ($tjproUser != true){ include('ad.php');  } ?>
    </div>

	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-7LJ267N0P2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-7LJ267N0P2');
</script>
	
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
				<a class="nav-item nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a>
				<a class="nav-item nav-link" href="emp-login.php">Employee Login</a>
				<a class="nav-item nav-link" href="mgr-login.php">Manager Login</a>
				<a class="nav-item nav-link" href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" target="_blank">Pricing</a>
				<a class="nav-item nav-link" href="https://workspace.google.com/marketplace/app/timejet/531257058624" target="_blank">GS Install</a>
				<a class="nav-item nav-link" href="https://dlohia.com/attachments/Timejet.pdf" target="_blank">User Guide</a>
				<a class="nav-item nav-link" href="troubleshoot.php">Troubleshoot</a>
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
				<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
						<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
						<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
						<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
					</ol>
					<div class="carousel-inner">
						<div class="carousel-item active">
						<img class="d-block w-100" src="images/snapD.png" alt="Fast">
						</div>
						<div class="carousel-item">
						<img class="d-block w-100" src="images/snapA.png" alt="Accurate">
						</div>
						<div class="carousel-item">
						<img class="d-block w-100" src="images/snapB.png" alt="Simple">
						</div>
						<div class="carousel-item">
						<img class="d-block w-100" src="images/snapC.png" alt="TimeJet">
						</div>
					</div>
					<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			</div>
		</div>
		<br>
		<h5>Timejet ~ Time Tracker</h5>
		<br>
		<h6><a href="https://dlohia.com/attachments/Timejet.pdf" target="_blank">Download How to use - User Guide</a></h6>
		<h6><a href="https://workspace.google.com/marketplace/app/timejet/531257058624" target="_blank">Install Google Sheets - TimeJet</a></h6>
		<br>
		<p>
		Superfast tool for tracking your daily activities. You can click on start button to get started. There will be real time clock running to show your data.
		You can also modify the location of the tracker where it will be shown . 

		<br>
		<h5>all in one</h5>
		<p>
		Timejet is all in one tool to help you track time and motion of you associates ! Currently the version has 7 days of trial period, after that you can may upgrade to <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" target="_blank">TimeJet Pro</a> . </p>

		<h5>real time reporting </h5>
		<p>
		We provide full  access to edit / modify records for the associates . manager can remove or add access to any associate depending on requirement.  
		</p>

		<h5>data security </h5>
		<p>
		We do not share any of customers information to third party . we use the information only for analytics purpose to track usage and fix issues. In case you want to request for TimeJet account deletion , please visit <a href="https://docs.google.com/forms/d/e/1FAIpQLScfZ500QXUwWtBzKN4hrl1E4V_1GqortGoFRQe8V0ucLPbOIw/viewform" target="_blank">contact us</a> page . </p>
		</p>
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



