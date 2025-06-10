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
	<title>Timejet | Troubleshoot </title>
	<link rel="stylesheet" href="css/style3.css" type="text/css">

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
          <a class="nav-item nav-link active" href="mgr-login.php">Manager Login</a>
          <a class="nav-item nav-link" href="privacy-policy.php">Privacy Policy</a>
          <a class="nav-item nav-link active" href="troubleshoot.php">Troubleshoot</a>
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
        
				<h3><b>Troubleshoot</b></h3>
        <h4><span style="color:green;">Google Sheets</span> Add-on</h4>
        <p>
          <div class="list-group w-100">
            <a href="#" data-mdb-toggle="collapse" aria-expanded="false" aria-controls="shortExampleAnswer1collapse" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">Add-On Is Not Loading Or Throwing Error.</h5>
            </div>
            <p class="mb-1">
            Un-Install And Re-Install Add-On From Google Market Place
            </p>
              <small>
              >> Go To Menu > Add-Ons > Manage Add-Ons . Choose Uninstall .
              <br>
              >> Refresh Your Browser . Add-Ons > Get Add-Ons And Install Again.
              </small>
              <p>
              <p class="mb-1">
                Enable Javascript In Browser
              </p>
              <small>
                >> Ensure That You Are Using Chrome Or Edge Browser Or Try Both .
                <br>
                >> Enable Javascript in Browser (Its Enabled By Default But Just Double Check -Https://Www.Enablejavascript.Io/En )
              </small>
          </a>

          <a href="#" data-mdb-toggle="collapse" aria-expanded="false"
            aria-controls="shortExampleAnswer1collapse" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">Incorrect date or time / timezone settings</h5>
            </div>
            <p class="mb-1">
              Change TimeZone settings on the g-sheet.
            </p>
            <small class="text-muted">
              >> open your google spreadsheet .
              <br>
              >> go to File > Settings > Timezone > choose your Time zone > Save and reload .
            </small>
          </a>

          <a href="https://docs.google.com/forms/d/e/1FAIpQLScfZ500QXUwWtBzKN4hrl1E4V_1GqortGoFRQe8V0ucLPbOIw/viewform" data-mdb-toggle="collapse" aria-expanded="false"
            aria-controls="shortExampleAnswer1collapse" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">Feedback / Suggestions</h5>
            </div>
            <p class="mb-1">
              use contact us page for any issues or suggestions .
            </p>
          </a>
        </div>
			</div>
		</div>
	</div>
	<footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA. </a>
			All Rights Reserved
		</div>
	</footer> 
</body>
</html>