<?php

/*
ini_set('display_errors', 1);
error_reporting(E_ALL);
*/

// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["emploggedin"]) || $_SESSION["emploggedin"] !== true){
    header("location: emp-login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$startTime = $stopTime = $totalTime = $emp_id = $userName = $activity = $case_ref = $userComments = $lastActivity = "";
$startTime_err = $stopTime_err = $emp_id_err = $userName_err = $activity_err = "" ;
$mgr_id = $shift_start = $shift_end = $start_end_diff = $total_time = "" ; 
$loginTime = "";
$err = "" ;
$sub_day_left = 0 ;
$record_added = false ;
 
date_default_timezone_set('Asia/Colombo');

$curDTTM=strtotime(date('Y-m-d h:i:sa') );
$currDt = date("d-M-Y", $curDTTM) ; // date('Y-m-d h:i:sa') ; 

$emp_id = $_SESSION["emp_id"] ;
$emp_name = $_SESSION["emp_name"] ;
$mgr_id =  $_SESSION["mgr_id"] ;
$loginDate = $_SESSION["loginDate"] ;
$loginTime =  $_SESSION["loginTime"] ;
$loginTime =  $_SESSION["loginTime"] ;
$sub_day_left =  $_SESSION["sub_day_left"] ;
$tjproUser = $_SESSION["tjproUser"] ;
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>TimeJet | Employee Dashboard </title>
    <?php include('header.php'); ?>

    <div id="ad">
        <?php if ($tjproUser != true){ include('ad.php');  } ?>
    </div>
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
                <a class="nav-item nav-link active" href="emp-dash.php">Dashboard <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="emp-report.php">Reports</a>
                <a class="nav-item nav-link" href="emp-reset-pwd.php">Reset Password</a>
                <a class="nav-item nav-link" href="emp-logout.php">Log out</a>
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

                <!--SUSCRIPTION WARNING-->
                <div id ="subWarning" class="alert alert-warning small" role="alert">
                    Your subscription is about to expire in  <strong id="lblSubDays"><?php echo $sub_day_left ; ?> </strong> Day(s).  <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" style="color:#721c24" target="_blank"><strong>  Renew now. </strong> </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>

                <!--SUSCRIPTION EXPIRED-->
                <div id = "subExpired" class="alert alert-danger small" role="alert">
                    Your subscription has <strong> expired.  <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" style="color:#721c24" target="_blank"> Renew now. </a></strong>

                    <input type="image" onclick="refreshSubscription()" src="https://timejet.dlohia.com/images/refresh_2.png"  height="20px" />

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>

                <div id = "welcome" class="alert alert-secondary small" role="alert">
                    Welcome <strong id="lblEmpName"><?php echo $emp_name ; ?></strong>
                    . Login date <strong id="lblLoginDate"><?php echo $currDt ; ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                
                <!--MANAGER ID DETAILS -->
                <div class="alert alert-secondary small" role="alert">
                    Manager ID: <strong id="lblManagerID"><?php echo $mgr_id; ?> </strong>. 
                    User ID: <strong id="lblEmpID"><?php echo $emp_id; ?></strong>. 
                </div>
                <!--
                    <div class="alert alert-secondary alert-dismissible fade show small" role="alert">
                -->
                <!--START TIME -->
                <div class="input-group input-group-sm">
                <div class="input-group-prepend ">
                    <span class="input-group-text  alert-secondary">Start Time:</span>
                </div>
                <input type="text" id="txbxStartTime" class="form-control" readonly>
                </div>
                

                <!--STOP TIME 
                <div class="input-group input-group-sm">
                <div class="input-group-prepend ">
                    <span class="input-group-text  alert-secondary" id="">Stop Time:&nbsp;</span>
                </div>
                <input type="text" name="stopTime" id="txbxStopTime" value="" class="form-control" readonly>
                </div>
                -->
                <!--
                <input type="hidden" id="txbxStopTimeHidden" class="form-control">
                <input type="input" id="txbxStopTimeHidden" class="form-control">
                -->
                

                <br>
                <!--TOTAL TIME -->
                <div class="input-group input-group-sm">
                <div class="input-group-prepend ">
                    <span class="input-group-text  alert-secondary" id="">Total Time:</span>
                </div>
                <input type="text" name="totalTime" id="txbxTotalTime" value="<?php echo $totalTime; ?>" class="form-control" readonly>
                </div>
                <br>
                <!--ACTIVITY -->
                <div id="activityDiv" class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <!--
                    <span class="input-group-text  alert-secondary">Activity:</span>
                    -->
                    <button type="button" class="btn btn-outline-secondary  alert-secondary">Activity:</button>
                    <!--
                    <button type="button" id="btnDropDown2" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    -->
                    <button type="button" onclick="funcFetchActivity();" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Choose</span>
                    </button>
                    <div class="dropdown-menu" id="ddMenu">
                    <!--
                    <a class="dropdown-item">Production</a>
                    <a class="dropdown-item">Non-Production</a>
                    <a class="dropdown-item">Lunch</a>
            -->
                    <!--
                    <div role="separator" class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Separated link</a>
                    -->
                    </div>
                </div>
                <input type="text" name="activity" id="drpDwnActivity" class="form-control" aria-label="Text input with segmented dropdown button">
                </div>
                <br>

                <!--CASE REF -->
                <div id="caseRefDiv" class="input-group input-group-sm">
                    <div class="input-group-prepend">
                    <span class="input-group-text alert-secondary" >Case Ref#:</span>
                    </div>
                    <input type="text" name="case_ref" id="txbxCaseRef" value="<?php echo $case_ref; ?>" class="form-control">
                </div>
                <br>

                <!--COMMENTS -->
                <div id="commentsDiv" class="input-group input-group-sm">
                    <div class="input-group-prepend">
                    <span class="input-group-text alert-secondary">Comments&nbsp;</span>
                    </div>
                    <textarea name="userComments" id="txbxComments" value="<?php echo $userComments; ?>" class="form-control" aria-label="enter comments textarea"></textarea>
                </div>
                <br>
                <!--
                <h5><span class="badge badge-secondary d-sm-table" id="lblTimer">00:00:00</span></h5>
                -->
                <!--BUTTONS -->
                <button type="button" id="btnStartTime" class="btn btn-success rounded btn-group-sm" onclick="funcStartTime()">Start</button>
                <button type="button" id="btnStopTime" class="btn btn-danger shadow rounded btn-group-sm" onclick="funcStopTime()">Stop</button>
                
                <button type="button" id="btnPauseTime" title="pause activity" class="btn btn-outline-danger" onclick="funcPauseTime()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pause" viewBox="0 0 16 16">
                    <path d="M6 3.5a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5zm4 0a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </button>


                <button type="button" title= "resume activity" id="btnResumeTime" class="btn btn-outline-success" onclick="funcResumeTime()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                    <path d="M10.804 8 5 4.633v6.734L10.804 8zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696l6.363 3.692z"/>
                    </svg>
                </button>

                <!--
                <button type="button" title= "Refresh buttons" id="btnRefreshTime" class="btn btn-secondary rounded btn-group-sm" onclick="funcEnableButtons();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                </button>
                -->

                <button id="btnShowCtrl" class="btn btn-secondary rounded btn-group-sm">
                    <span>
                    <img src="https://timejet.dlohia.com/images/settings.png" height="15px" width="15px" alt="Snow">
                    </span>
                </button>

                <p>
                <div style="display:none;" class="alert alert-secondary alert-dismissible fade show small" role="alert" id="success-alert">
                    <i>End Time:</i>. 
                    <span id="lblEndTime"> 00:00:00 </span>
                    <br>
                    <i>Total Time:</i>
                    <span id="lblTotalTime"> 00:00:00 </span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div style="display:none;" class="alert alert-secondary alert-dismissible fade show small" role="alert" id="success-alert-pause">
                    <i>Activity Paused</i>. 
                </div>

                <div style="display:none;" class="alert alert-secondary alert-dismissible fade show small" role="alert" id="success-alert-resume">
                    <i>Resuming activity.</i>. 
                </div>


                <!--
                <div class="sidebar bottom">
                -->
                <div id="empControls" style="display:none;">
                    <!--
                    <div id="carouselSection" style="display:none;">
                    -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="ckbxSwPrmt" checked>
                        <label class="form-check-label" for="ckbxShowDate">
                            confirm before submit
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox" value="" id="ckbxCsRfReq" checked>
                        <label class="form-check-label">
                            case ref# required
                        </label>
                        <!--
                        <br>
                        <button type="button" class="btn btn-primary shadow rounded btn-group-xs" onclick="alert (validSubscription()) ;">Reinstate Subs</button>
                        -->
                    </div>
                </div>
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

<script type="text/javascript">

//$(document).ready(function(){


  var pauseTimerFlag = false;
  var activityPaused = false;
  var initSecPause = 0 ;
  
  var startTimerFlag = false;
  var initSec ;
  var timex ;
  var w;
  /*
  var loginTypeStatus = false;
  var endDate = new Date() ;
  var timex2 ;
  var loginDateGlobal = new Date() ;
  var loginDateGlobal2 = new Date() ;
  */

    let startTimeCtrl =  document.getElementById('txbxStartTime') ;
    let totalTimeCtrl = document.getElementById('txbxTotalTime')  ;
    var startTimeTemp ;
    

  //event handlers
  document.querySelector("#ddMenu").addEventListener("click", e =>{
      document.getElementById('drpDwnActivity').value  = e.target.innerHTML ; // local input
      document.getElementById('drpDwnActivity').style.borderColor = '#ced4da';
      changeEntry();
  });

  drpDwnActivity.addEventListener('input', function(e){
      if (e.target.value == "") {
            //red border
          document.getElementById('drpDwnActivity').style.borderColor = '#D75C5C';
      }
      else{
        //red border
          document.getElementById('drpDwnActivity').style.borderColor = '#ced4da';
      }
  });

  txbxCaseRef.addEventListener('change', function(e){
      if (e.target.value == "") {
        if ($('#ckbxCsRfReq').is(":checked")) {
          document.getElementById('txbxCaseRef').style.borderColor = '#D75C5C';
        }
      }
      else{
          document.getElementById('txbxCaseRef').style.borderColor = '#ced4da';
      }
  });

  btnShowCtrl.addEventListener("click", e =>{
    if ($('#empControls').is(":visible") ){
        $("#empControls").hide("slow");
    }
    else{
      $("#empControls").show("show");
    }
  });

  ckbxCsRfReq.addEventListener('change', function() {
    //var crV = document.getElementById('txbxCaseRef') ;
    if (this.checked == false) {
        document.getElementById('txbxCaseRef').style.borderColor = '#ced4da';
    }
    /*
    else {
        if (this.checked == true && crV.value == "" ) {
            crV.style.borderColor = '#D75C5C';
        }
    }
    */
  });

//});

loadDefaults();

function loadDefaults(){
    funcFetchActivity();
    var activity = document.getElementById('drpDwnActivity').value;

    var sub_day_left =  "<?php echo $sub_day_left ?>" ;  
    subAdj(sub_day_left);

    $("#btnPauseTime").hide();
    $("#btnResumeTime").hide();

    if(typeof(Worker) == "undefined") {
        alert ("your browser is not supported");
        //document.getElementById("result").innerHTML = "Sorry, your browser not supported";
    }

    funcStartTime();
};


function funcShowAlertActivity(stat){
    if (stat == 0){
        $("#success-alert-pause").show();
        $("#success-alert-pause").delay(4000).slideUp(500, function() {
            $(this).addClass('hidden');
        });
    }
    else{
        $("#success-alert-resume").show();
        $("#success-alert-resume").delay(4000).slideUp(500, function() {
        $(this).addClass('hidden');
        });
    }
}
 
function subAdj(sub_day_left){
    $("#subExpired").hide();
    $("#subWarning").hide();
    if (startTimerFlag == false){
        $(':input[id="btnStartTime"]').prop('disabled', false);
    }
    $(':input[id="btnStopTime"]').prop('disabled', false);
    $(':input[id="btnShowCtrl"]').prop('disabled', false);

    if (sub_day_left <= 0) {
        $("#subExpired").show();
        $("#welcome").hide();
        $(':input[id="btnStartTime"]').prop('disabled', true);
        $(':input[id="btnStopTime"]').prop('disabled', true);
        $(':input[id="btnShowCtrl"]').prop('disabled', true);
    }
    else if (sub_day_left <=7) {
        $("#subWarning").show();
        $("#welcome").show();
    }
    else{
        $("#welcome").show();
        $("#ad").hide();
    }
}

function funcFetchActivity(){
    let mgrEmail = document.getElementById('lblManagerID').innerHTML ;

    if( mgrEmail != "" ){
      $.ajax({
        url:'emp-fetch-act.php',
        type:'POST',
        data:{mgrEmail:mgrEmail},
        success:function(response){
          if(response.search("success:") > 0){
            var strVal = response;
            strVal = strVal.replace('"success:":' , "");
            strVal = strVal.replace('{[' , "[");
            strVal = strVal.replace(']}' , "]");
            let val = JSON.parse(strVal);

            let activities = [];
            for (var j=0; j < val.length; j++) {
              var actNm = val[j].activity_name ; 
              actNm = actNm.trim() ;
              if (actNm != ''){
                activities.push (actNm) ;
              }
            }

            document.getElementById('ddMenu').innerHTML = "";
            const div = document.querySelector('#ddMenu');
            activities.forEach(activity => {
              div.innerHTML += `<a class="dropdown-item href="#">${activity}</a>`;
            })
  
          }else{
            alert(removeExtraSpace(response));
          }
        }
      });
    }
};

function changeEntry() {
    var activity = document.getElementById('drpDwnActivity').value;

    if (activity != ""){
        var sinceTime = "<?php 
            echo date("h:i:s", $curDTTM)  ; 
        ?>" ;
        var mgr_id  = "<?php echo $mgr_id ?>" ;
        var emp_id  = "<?php echo $emp_id ?>" ;
        
        $.ajax({ 
            type : 'POST',
            data : {'activity':activity ,'mgr_id':mgr_id ,  'emp_id':emp_id , 'sinceTime' : sinceTime },
            url  : 'emp-act-change.php',
            success: function ( result ) {
                    // alert( result );    
            },
            error: function ( xhr ) {
                alert( "error" );
            }
        });
    }
};

function funcCurTime(){
    var rslt = false ;
    //var d = new Date().toLocaleTimeString();
    //return d;
    $.ajax({
        type:'POST',
        url:'get-st-time.php',
        success:function(response){
        rslt = true ;
        //alert (response) ;
        return response;
        }
    });

    if (rslt == false ){
        var d = new Date().toLocaleTimeString();
        //var str = moment(d).format('hh:mm:ss a');
        return d;
    }
};

function funcStartTime() {

    startTimerFlag = true ;
    initSec = 0 ;
    
    var startTime = "<?php
    $_SESSION["recordStatus"] = true ;
    $curDTTM = strtotime(date('Y-m-d h:i:sa') );
    $startTime = date("h:i:s", $curDTTM)  ; 
    echo $startTime ;
    ?>" ;

    startTime = funcCurTime();
    startTimeCtrl.value = startTime ;

    //startTimeTemp
    //endDate = v[4] ; //timestamp

    if (totalTimeCtrl.value != ""){
        //clearTimeout(timex);
        stopTimeWorker();
        //alert ("timer reset");
    }
    /*
    $('#btnPause').hide();
    #document.getElementById("btnStart").disabled = true;
    #document.getElementById("btnStop").disabled = true;
    $('#btnResume').show();
    funcShowAlert3();
    */

    totalTimeCtrl.value = "00:00:00" ;
    //startTimer();
    startTimeWorker();

    $("#btnResetTime").hide();
    $("#btnResumeTime").hide();
    //TEMP$("#btnPauseTime").show();
    //$("#btnRefreshTime").show();
    
    document.getElementById("btnStopTime").disabled = false;
    //$("#btnResetTime").removeClass("btn btn-secondary rounded btn-group-sm");
    //$("#btnResetTime").addClass("btn btn-warning rounded btn-group-sm");
    document.getElementById("btnStartTime").disabled = true;

  };

  function funcPauseTime() {
    if (startTimerFlag){
          //record the state PAUSE
          pauseTimerFlag = true;

          stopTimeWorker();
          //clearTimeout(timex);

          initSecPause = initSec ;
          $('#btnPauseTime').hide();

          //document.getElementById("btnStart2").disabled = true;
          document.getElementById("btnStopTime").disabled = true;

          $('#btnResumeTime').show();
          //$("#btnResume2").effect("pulsate", { times:3 }, 2000);
          funcShowAlertActivity(0);

          //enabling the flag that activity was paused
          activityPaused = true;
        }
    }

  function funcResumeTime() {
    if (startTimerFlag){
      //resume time RESUME
      pauseTimerFlag = false; 
      initSec = initSecPause ;
      //startTimer();
      startTimeWorker();
      $('#btnPauseTime').show();
      $('#btnResumeTime').hide();
      funcShowAlertActivity(1);

      document.getElementById("btnStopTime").disabled = false;
    }
  }

  /*
  function funcEnableButtons(){
    document.getElementById("btnStartTime").disabled = false;
    document.getElementById("btnStopTime").disabled = false;
  }
  */

  function startTimeWorker(){
    if(typeof(Worker) !== "undefined") {
        //console.log("worker loaded..");
        if(typeof(w) == "undefined") {
            //console.log("file loaded..");
            w = new Worker("js/time_worker.js");
        }
            w.onmessage = function(event) {
                var d = new Date("Dec 12, 1983 00:00:00");
                
                //let startTimeCtrl =  document.getElementById('txbxStartTime')
                //var d2 = new Date(startTimeCtrl.value);
                //var d2 = startTimeCtrl.value;
                
                //var date = new Date(Date.parse(startTimeCtrl.value));

                //console.log (date) ;
                
                //initSec += 1 ;
                //initSec +=  Number(event.data) ;
                //var intVal = 0 ;
                //if activity was paused
                if (activityPaused == true){
                    initSec =  Number(event.data) + initSecPause ;
                }else{
                    initSec =  Number(event.data) + initSecPause ;
                }

                d.setSeconds(d.getSeconds() + initSec);
                //d2.setSeconds(d2.getSeconds() + initSec);
                 
                /*
                intVal =  Number(event.data) + initSec ;
                d.setSeconds(d.getSeconds() + intVal);
                d2.setSeconds(d2.getSeconds() + intVal);
                */

                var h = d.getHours()  ;
                var m = d.getMinutes()   ; // 0 - 59
                var s = d.getSeconds()  ; // 0 - 59

                /*
                var h2 = d2.getHours()  ;
                var m2 = d2.getMinutes()   ; // 0 - 59
                var s2 = d2.getSeconds()  ; // 0 - 59
                */
                
                h = (h < 10) ? "0" + h : h;
                m = (m < 10) ? "0" + m : m;
                s = (s < 10) ? "0" + s : s;

                /*
                h2 = (h2 < 10) ? "0" + h2 : h2;
                m2 = (m2 < 10) ? "0" + m2 : m2;
                s2 = (s2 < 10) ? "0" + s2 : s2;
                */
                
                var time = h + ":" + m + ":" + s  ;
                //var time2 = h2 + ":" + m2 + ":" + s2  ;

                //document.getElementById('txbxStopTime').value = time2;
                //document.getElementById('txbxStopTimeHidden').value = time2;
                document.getElementById('txbxTotalTime').value = time;
                //document.getElementById("txbxTotalTime").innerHTML = event.data;
            };
        }  
    }

    function stopTimeWorker() { 
        w.terminate();
        w = undefined;
    }

    function refreshSubscription(){
        if (validSubscription() == true){
            alert("Subscription activated.");
        }
        else{
            alert("no subscription found.");
        }
    }

function validSubscription() {
    var mgr_id  = document.getElementById('lblManagerID').innerHTML ;
    var isValid = false ;
    
    $.ajax({
        type: "POST",
        url: "subscription-check.php",
        async:false,
        data: ({
            'mgr_id': mgr_id
        }),
        cache: false,
        success: function(response) {
            //alert(response);
            //if(response.search("valid") > 0){
            response = Number(response);
            //alert (response);
            subAdj(response);
            if (response > 0){
                isValid = true;
            }
        }
    });
    return isValid;
};

function addTimes(startTime, endTime) {
  var times = [0, 0, 0];
  var max = times.length;

  var a = (startTime || '').split(':');
  var b = (endTime || '').split(':');

  // normalize time values
  for (var i = 0; i < max; i++) {
    a[i] = isNaN(parseInt(a[i])) ? 0 : parseInt(a[i]);
    b[i] = isNaN(parseInt(b[i])) ? 0 : parseInt(b[i]);
  }

  // store time values
  for (var i = 0; i < max; i++) {
    times[i] = a[i] + b[i];
  }

  var hours = times[0];
  var minutes = times[1];
  var seconds = times[2];

  if (seconds >= 60) {
    var m = (seconds / 60) << 0;
    minutes += m;
    seconds -= 60 * m;
  }

  if (minutes >= 60) {
    var h = (minutes / 60) << 0;
    hours += h;
    minutes -= 60 * h;
  }

  
  return (
    Number('0' + hours) +  ':' +  ('0' + minutes).slice(-2) +   ':' +  ('0' + seconds).slice(-2)
  );
  
  //return getDateFromHours( hours + ':' + minutes + ':' + seconds)

}

/*
function getDateFromHours(time) {
    time = time.split(':');
    let now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), now.getDate(), ...time);
}
*/

function funcStopTime() {
    
    if (validSubscription() == false){
        alert("Your Subscription has expired.");
        return;
    }
    var mgr_id  = document.getElementById('lblManagerID').innerHTML ;
    var login_date = document.getElementById('lblLoginDate').innerHTML ;
    var emp_id  = document.getElementById('lblEmpID').innerHTML ;
    var emp_name = document.getElementById('lblEmpName').innerHTML ;
    var activityCtrl = document.getElementById('drpDwnActivity') ;
    var caseRefCtrl = document.getElementById('txbxCaseRef') ;
    var userComments = document.getElementById('txbxComments').value ;
    var startTime = document.getElementById('txbxStartTime').value ;
    var activity = activityCtrl.value ;
    var case_ref = caseRefCtrl.value ;


    if (activity == ""  ||  ($('#ckbxCsRfReq').is(":checked") == true && case_ref == "")){
        if (activity == "" ){
            activityCtrl.style.borderColor = '#D75C5C';
            $("#activityDiv").shake(100,10,3);
        }

        if ($('#ckbxCsRfReq').is(":checked") == true && case_ref == ""){
            caseRefCtrl.style.borderColor = '#D75C5C';
            $("#caseRefDiv").shake(100,10,3);
        }
        return;
    }

    var stopTime = funcCurTime();
    //alert (addTimes(startTime, totalTime));

    var d1 = new Date(login_date + " " + startTime );
    var d2 = new Date(login_date + " " + stopTime );
    var date = new Date(d2-d1);
    var hour = date.getUTCHours();
    var min = date.getUTCMinutes();
    var sec = date.getUTCSeconds();
    var totalTime = ( n(hour) + ":" + n(min) + ":" + n(sec)) ;

    //var tempStopTime  = document.getElementById('txbxStopTime').value  ;
    //var tempStopTime  = document.getElementById('txbxStopTimeHidden').value  ;
    var tempTotalTime  = document.getElementById('txbxTotalTime').value  ;


    if (activityPaused == true){
        stopTime = addTimes(startTime, totalTime);
        //stopTime = new Date(stopTime);
    }
    //document.getElementById('txbxStopTime').value  = stopTime ;
    //document.getElementById('txbxTotalTime').value = totalTime;

    let txt = " Start time = " +  startTime + "\n Stop Time = " + stopTime +  "\n Total Time = " +  totalTime + "\n want to submit ?" ;

    if  ($('#ckbxSwPrmt').is(":checked") == true) {
        if (confirm(txt) == false) {
            return ;
        }
    }

    $.ajax({
        type: "POST",
        url: "emp-rcd-stp-tm.php",
        data: ({
            'mgr_id': mgr_id , 'login_date' : login_date, 'emp_id': emp_id  , 'emp_name' : emp_name ,
                'activity': activity , 'case_ref': case_ref , 'userComments' : userComments , 
                'startTime' : startTime , 'stopTime' : stopTime , 'totalTime': totalTime
        }),
        cache: false,
        success: function(data) {
            //alert(data); DEBUG

            if ($('#lblEndTime').length > 0) {
                document.getElementById("lblEndTime").innerHTML = stopTime ;
                document.getElementById("lblTotalTime").innerHTML = totalTime ;
                funcShowAlert() ; 
            }

            document.getElementById('txbxStartTime').value = stopTime ;
            document.getElementById('txbxCaseRef').value = "" ;

            initSec = 0 ;
            funcStartTime();
            activityPaused = false;
            //document.getElementById('totalTime').value = totalTime;
            
        }
    });
};

function funcShowAlert(){
    $("#success-alert").show();
    $("#success-alert").delay(4000).slideUp(500, function() {
      $(this).addClass('hidden');
    });
};

function n(n){
    return n > 9 ? "" + n: "0" + n;
};

//removing linebreak etc
function removeExtraSpace(str){
    str = str.replace(/(\r\n|\n|\r)/gm, '') ;
    str = str.replaceAll('\n','') ;
    return str;
};

//JQuery function
jQuery.fn.shake = function(interval,distance,times){
    interval = typeof interval == "undefined" ? 100 : interval;
    distance = typeof distance == "undefined" ? 10 : distance;
    times = typeof times == "undefined" ? 3 : times;
    var jTarget = $(this);
    jTarget.css('position','relative');
    for(var iter=0;iter<(times+1);iter++){
       jTarget.animate({ left: ((iter%2==0 ? distance : distance*-1))}, interval);
    }
    return jTarget.animate({ left: 0},interval);
 }
</script>

</body>
</html>