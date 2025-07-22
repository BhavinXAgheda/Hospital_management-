<!DOCTYPE html>
<?php 
include('func.php');  
include('newfunc.php');
$con=mysqli_connect("localhost","root","","myhmsdb");


  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $gender = $_SESSION['gender'];
  $lname = $_SESSION['lname'];
  $contact = $_SESSION['contact'];



if(isset($_POST['app-submit']))
{
  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $lname = $_SESSION['lname'];
  $gender = $_SESSION['gender'];
  $contact = $_SESSION['contact'];
  $doctor=$_POST['doctor'];
  $email=$_SESSION['email'];
  # $fees=$_POST['fees'];
  $docFees=$_POST['docFees'];

  $appdate=$_POST['appdate'];
  $apptime=$_POST['apptime'];
  $cur_date = date("Y-m-d");
  date_default_timezone_set('Asia/Kolkata');
  $cur_time = date("H:i:s");
  $apptime1 = strtotime($apptime);
  $appdate1 = strtotime($appdate);
	
  if(date("Y-m-d",$appdate1)>=$cur_date){
    if((date("Y-m-d",$appdate1)==$cur_date and date("H:i:s",$apptime1)>$cur_time) or date("Y-m-d",$appdate1)>$cur_date) {
      $check_query = mysqli_query($con,"select apptime from appointmenttb where doctor='$doctor' and appdate='$appdate' and apptime='$apptime'");

        if(mysqli_num_rows($check_query)==0){
          $query=mysqli_query($con,"insert into appointmenttb(pid,fname,lname,gender,email,contact,doctor,docFees,appdate,apptime,userStatus,doctorStatus) values($pid,'$fname','$lname','$gender','$email','$contact','$doctor','$docFees','$appdate','$apptime','1','1')");

          if($query)
          {
            echo "<script>alert('Your appointment successfully booked');</script>";
          }
          else{
            echo "<script>alert('Unable to process your request. Please try again!');</script>";
          }
      }
      else{
        echo "<script>alert('We are sorry to inform that the doctor is not available in this time or date. Please choose different time or date!');</script>";
      }
    }
    else{
      echo "<script>alert('Select a time or date in the future!');</script>";
    }
  }
  else{
      echo "<script>alert('Select a time or date in the future!');</script>";
  }
  
}

if(isset($_GET['cancel']))
  {
    $query=mysqli_query($con,"update appointmenttb set userStatus='0' where ID = '".$_GET['ID']."'");
    if($query)
    {
      echo "<script>alert('Your appointment successfully cancelled');</script>";
    }
  }





function generate_bill(){
  $con=mysqli_connect("localhost","root","","myhmsdb");
  $pid = $_SESSION['pid'];
  $output='';
  $query=mysqli_query($con,"select p.pid,p.ID,p.fname,p.lname,p.doctor,p.appdate,p.apptime,p.disease,p.allergy,p.prescription,a.docFees from prestb p inner join appointmenttb a on p.ID=a.ID and p.pid = '$pid' and p.ID = '".$_GET['ID']."'");
  while($row = mysqli_fetch_array($query)){
    $output .= '
    <label> Patient ID : </label>'.$row["pid"].'<br/><br/>
    <label> Appointment ID : </label>'.$row["ID"].'<br/><br/>
    <label> Patient Name : </label>'.$row["fname"].' '.$row["lname"].'<br/><br/>
    <label> Doctor Name : </label>'.$row["doctor"].'<br/><br/>
    <label> Appointment Date : </label>'.$row["appdate"].'<br/><br/>
    <label> Appointment Time : </label>'.$row["apptime"].'<br/><br/>
    <label> Disease : </label>'.$row["disease"].'<br/><br/>
    <label> Allergies : </label>'.$row["allergy"].'<br/><br/>
    <label> Prescription : </label>'.$row["prescription"].'<br/><br/>
    <label> Fees Paid : </label>'.$row["docFees"].'<br/>
    
    ';

  }
  
  return $output;
}


if(isset($_GET["generate_bill"])){
  require_once("TCPDF/tcpdf.php");
  $obj_pdf = new TCPDF('P',PDF_UNIT,PDF_PAGE_FORMAT,true,'UTF-8',false);
  $obj_pdf -> SetCreator(PDF_CREATOR);
  $obj_pdf -> SetTitle("Generate Bill");
  $obj_pdf -> SetHeaderData('','',PDF_HEADER_TITLE,PDF_HEADER_STRING);
  $obj_pdf -> SetHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
  $obj_pdf -> SetFooterFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
  $obj_pdf -> SetDefaultMonospacedFont('helvetica');
  $obj_pdf -> SetFooterMargin(PDF_MARGIN_FOOTER);
  $obj_pdf -> SetMargins(PDF_MARGIN_LEFT,'5',PDF_MARGIN_RIGHT);
  $obj_pdf -> SetPrintHeader(false);
  $obj_pdf -> SetPrintFooter(false);
  $obj_pdf -> SetAutoPageBreak(TRUE, 10);
  $obj_pdf -> SetFont('helvetica','',12);
  $obj_pdf -> AddPage();

  $content = '';

  $content .= '
      <br/>
      <h2 align ="center"> Global Hospitals</h2></br>
      <h3 align ="center"> Bill</h3>
      

  ';
 
  $content .= generate_bill();
  $obj_pdf -> writeHTML($content);
  ob_end_clean();
  $obj_pdf -> Output("bill.pdf",'I');

}

function get_specs(){
  $con=mysqli_connect("localhost","root","","myhmsdb");
  $query=mysqli_query($con,"select username,spec from doctb");
  $docarray = array();
    while($row =mysqli_fetch_assoc($query))
    {
        $docarray[] = $row;
    }
    return json_encode($docarray);
}

?>
<html lang="en">
  <head>


    <!-- Required meta tags -->
    <meta charset="utf-8">
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="styles.css">
       <link href="https://cdn.jsdelivr.net/npm/remixicon@3.0.0/fonts/remixicon.css" rel="stylesheet">


    <!-- Bootstrap CSS -->
    
        <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">

    
  
    
    



    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <a class="navbar-brand" href="#"><i class="fa fa-user-plus" aria-hidden="true"></i>Intelligent Doctor</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <style >
    /* General Styles */
body {
  margin: 0;
  padding: 0;
  background-color: #f5f5f5;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  font-family: Arial, sans-serif;
}

/* Chatbot Container */
.chatbot {
  /* width: 400px; */
  height: 600px;
  background-color: white;
  display: flex;
  flex-direction: column;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

/* Header */
.chatbot-header {
  background-color: #5d4bdb;
  color: white;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 16px;
}

.header-content {
  display: flex;
  align-items: center;
}

.robot-icon {
  width: 30px;
  height: 30px;
  margin-right: 10px;
}

.chatbot-name {
  font-weight: bold;
  font-size: 18px;
}

.dropdown-button {
  background: none;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
  border-radius: 50%;
}

.dropdown-button:hover{
  background: #3d39ac;
}

/* Chat Body */
.chatbot-body {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
  background-color: #f9f9f9;
  scrollbar-width: thin;
  scrollbar-color: #ccccf5 transparent;
}

.message {
  display: flex;
  align-items: flex-start;
  margin-bottom: 10px;
}

.bot-message
{
  flex-direction: row;
}
.user-message {
  justify-content: flex-end;
}

.chat-icon {
  width: 30px;
  height: 30px;
  margin-right: 10px;
}

.text-box {
  background-color: #e9e9f0;
  padding: 10px;
  border-radius: 8px;
  max-width: 70%;
  font-size: 14px;
  color: #333;
}

.chatbot-body .bot-message.thinking .text-box{
  padding: 2px 16px;
}

.chatbot-body .bot-message .text-box .thinking-indicator{
  display: flex;
  gap: 4px;
  padding-block: 15px;

}

.chatbot-body .bot-message .text-box .thinking-indicator .dot{
  height: 7px;
  width: 7px;
  opacity: 0.7;
  border-radius: 50%;
  background: #5d4bdb;
  animation: dotPulse 1.8s ease-in-out infinite; 
}

.chatbot-body .bot-message .text-box .thinking-indicator .dot:nth-child(1)
{
        animation-delay: 0.2s;
}

.chatbot-body .bot-message .text-box .thinking-indicator .dot:nth-child(2)
{
        animation-delay: 0.3s;
}

.chatbot-body .bot-message .text-box .thinking-indicator .dot:nth-child(1)
{
        animation-delay: 0.4s;
}

@keyframes dotPulse {
  0%,44%{
    transform: translateY(0);
  }
  28%{
    opacity: 0.4;
    transform: translateY(-4px);
  }
  44%{
    opacity: 0.2;
  }
  
}

.user-message .text-box {
  background-color: #5d4bdb;
  color: white;
}

/* Footer */
.chatbot-footer {
  display: flex;
  align-items: center;
  position: relative;
  padding: 10px;
  border-top: 1px solid #ddd;
  background-color: #fff;
}

.message-input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 20px;
  margin-right: 10px;
}

.footer-icons {
  display: flex;
  gap: 5px;
}

.emoji-button,
.send-button {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  border-radius: 50%;
}

.chatbot-footer .message-input:focus-within{
  outline: 2px solid #5d4bdb;
}

.chatbot-footer .send-button{
  display: none;
  background: #5d4bdb;
  color: #fff;
  border-radius: 50% ;
  font-size: 14px; /* */
}
.chatbot-footer .send-button:hover{
  background: #3d39ac;
}

.chatbot-footer .message-input .footer-icons:hover{
  background: #f5f5f5;
}

.chatbot-footer .message-input:valid~.footer-icons .send-button{
  display: block;
}

em-emoji-picker{
  position:absolute;
  left: 50%;
  top: -337px;
  width: 100%;
  max-width: 350px;
  max-height: 330px;
  visibility: hidden;
  transform: translateX(-50%);
}

body.show-emoji-picker em-emoji-picker{
  visibility: visible;
}

#chatbot-toggler{
  position: fixed;
  bottom: 30px;
  right: 35px;
  border: none;
  height: 50px;
  width: 50px;
  cursor: pointer;
  border-radius: 50%;
  background: #5350c4;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

#chatbot-toggler span{
  color: #fff;
  position: absolute;
}

body.show-chatbot #chatbot-toggler 
{
  transform: rotate(90deg);
}
body.show-chatbot #chatbot-toggler span:first-child,
#chatbot-toggler span:last-child{
  opacity: 0;
}

body.show-chatbot #chatbot-toggler span:last-child{
  opacity: 1;
}

.chatbot-popup{
  position: fixed;
  width: 420px;
  right: 35px;
  bottom: 90px;
  background: #fff;
  overflow: hidden;
  border-radius: 15px;
  opacity: 0;
  pointer-events: none;
  box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1),
  0 32px 64px -48px rgba(0, 0, 0, 0.5);
  transition: all 0.1s ease;
  transform: scale(0.2);
  transform-origin: bottom right;
}

body.show-chatbot .chatbot-popup{
  opacity: 1;
  pointer-events: auto;
  transform: scale(1);
}

/* Responsive media query for mobile screens */
@media (max-width:520px)  {
  #chatbot-toggler{
    right: 20px;
    bottom: 20px;
  }

  .chatbot-popup{
    right: 0;
    bottom: 0;
    height: 100%;
    border-radius: 0;
    width: 100%;
    }

    .chatbot-popup .chatbot-header{
      padding: 12px 15px;
    }

    .chatbot-body {
      height: calc(90% - 55px);
      padding: 25px 15px;
    }

    .chatbot-footer{
      padding: 10px 15px 15px;
    }
}
    .bg-primary {
    background: -webkit-linear-gradient(left, #3931af, #00c6ff);
}
.list-group-item.active {
    z-index: 2;
    color: #fff;
    background-color: #342ac1;
    border-color: #007bff;
}
.text-primary {
    color: #342ac1!important;
}

.btn-primary{
  background-color: #3c50c1;
  border-color: #3c50c1;
}
  </style>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
     <ul class="navbar-nav mr-auto">
       <li class="nav-item">
        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
      </li>
       <li class="nav-item">
        <a class="nav-link" href="#"></a>
      </li>
    </ul>
  </div>
</nav>
  </head>
  <style type="text/css">
    button:hover{cursor:pointer;}
    #inputbtn:hover{cursor:pointer;}
  </style>
  <body style="padding-top:50px;">
     <button id="chatbot-toggler">
    <span class="ri-chat-4-line"></span>
    <span class="ri-close-line"></span>
  </button>

  <div class="chatbot-popup">
  <div class="chatbot">
    <!-- Header -->
    <div class="chatbot-header">
      <div class="header-content">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Robot Logo" class="robot-icon">
        <span class="chatbot-name">Chatbot</span>
      </div>
      <button class="dropdown-button" id="close-chatbot">&#9662;</button>
    </div>

    <!-- Chat Body -->
    <div class="chatbot-body">
      <div class="message bot-message">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot Icon" class="chat-icon">
        <div class="text-box">
          <p>Hey there 👋 <br> How can I help you today?</p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="chatbot-footer">
      <input type="text" placeholder="Message..." class="message-input" required>
      <div class="footer-icons">
        <button type="button" class="emoji-button" id="emoji-picker">😊</button>
        <button type="submit" class="send-button" id="send-message">&#8593;</button>
      </div>
    </div>
  </div>
</div>

  <!-- Linking Emoji Mart Script for emoji picker -->
  <script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>

  <script src="script.js"></script>

  
   <div class="container-fluid" style="margin-top:50px;">
    <h3 style = "margin-left: 40%;  padding-bottom: 20px; font-family: 'IBM Plex Sans', sans-serif;"> Welcome &nbsp<?php echo $username ?> 
   </h3>
    <div class="row">
  <div class="col-md-4" style="max-width:25%; margin-top: 3%">
    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-action active" id="list-dash-list" data-toggle="list" href="#list-dash" role="tab" aria-controls="home">Dashboard</a>
      <a class="list-group-item list-group-item-action" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Book Appointment</a>
      <a class="list-group-item list-group-item-action" href="#app-hist" id="list-pat-list" role="tab" data-toggle="list" aria-controls="home">Appointment History</a>
      <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home">Prescriptions</a>
      
    </div><br>
  </div>
  <div class="col-md-8" style="margin-top: 3%;">
    <div class="tab-content" id="nav-tabContent" style="width: 681px;">


      <div class="tab-pane fade  show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
        <div class="container-fluid container-fullw bg-whitesmoke" >
              <div class="row">
               <div class="col-sm-4" style="left: 5%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-terminal fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;"> Book My Appointment</h4>
                      <script>
                        function clickDiv(id) {
                          document.querySelector(id).click();
                        }
                      </script>                      
                      <p class="links cl-effect-1">
                        <a href="#list-home" onclick="clickDiv('#list-home-list')">
                          Book Appointment
                        </a>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-sm-4" style="left: 10%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body" >
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-paperclip fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">My Appointments</h2>
                    
                      <p class="cl-effect-1">
                        <a href="#app-hist" onclick="clickDiv('#list-pat-list')">
                          View Appointment History
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
                </div>

                <div class="col-sm-4" style="left: 20%;margin-top:5%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body" >
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-list-ul fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Prescriptions</h2>
                    
                      <p class="cl-effect-1">
                        <a href="#list-pres" onclick="clickDiv('#list-pres-list')">
                          View Prescription List
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
                
         
            </div>
          </div>





      <div class="tab-pane fade" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
        <div class="container-fluid">
          <div class="card">
            <div class="card-body">
              <center><h4>Create an appointment</h4></center><br>
              <form class="form-group" method="post" action="admin-panel.php">
                <div class="row">
                  
                  <!-- <?php

                        $con=mysqli_connect("localhost","root","","myhmsdb");
                        $query=mysqli_query($con,"select username,spec from doctb");
                        $docarray = array();
                          while($row =mysqli_fetch_assoc($query))
                          {
                              $docarray[] = $row;
                          }
                          echo json_encode($docarray);

                  ?> -->
        

                    <div class="col-md-4">
                          <label for="spec">Specialization:</label>
                        </div>
                        <div class="col-md-8">
                          <select name="spec" class="form-control" id="spec">
                              <option value="" disabled selected>Select Specialization</option>
                              <?php 
                              display_specs();
                              ?>
                          </select>
                        </div>

                        <br><br>

                        <script>
                      document.getElementById('spec').onchange = function foo() {
                        let spec = this.value;   
                        console.log(spec)
                        let docs = [...document.getElementById('doctor').options];
                        
                        docs.forEach((el, ind, arr)=>{
                          arr[ind].setAttribute("style","");
                          if (el.getAttribute("data-spec") != spec ) {
                            arr[ind].setAttribute("style","display: none");
                          }
                        });
                      };

                  </script>

              <div class="col-md-4"><label for="doctor">Doctors:</label></div>
                <div class="col-md-8">
                    <select name="doctor" class="form-control" id="doctor" required="required">
                      <option value="" disabled selected>Select Doctor</option>
                
                      <?php display_docs(); ?>
                    </select>
                  </div><br/><br/> 


                        <script>
              document.getElementById('doctor').onchange = function updateFees(e) {
                var selection = document.querySelector(`[value=${this.value}]`).getAttribute('data-value');
                document.getElementById('docFees').value = selection;
              };
            </script>

                  
                  

                  
                        <!-- <div class="col-md-4"><label for="doctor">Doctors:</label></div>
                                <div class="col-md-8">
                                    <select name="doctor" class="form-control" id="doctor1" required="required">
                                      <option value="" disabled selected>Select Doctor</option>
                                      
                                    </select>
                                </div>
                                <br><br> -->

                                <!-- <script>
                                  document.getElementById("spec").onchange = function updateSpecs(event) {
                                      var selected = document.querySelector(`[data-value=${this.value}]`).getAttribute("value");
                                      console.log(selected);

                                      var options = document.getElementById("doctor1").querySelectorAll("option");

                                      for (i = 0; i < options.length; i++) {
                                        var currentOption = options[i];
                                        var category = options[i].getAttribute("data-spec");

                                        if (category == selected) {
                                          currentOption.style.display = "block";
                                        } else {
                                          currentOption.style.display = "none";
                                        }
                                      }
                                    }
                                </script> -->

                        
                    <!-- <script>
                    let data = 
                
              document.getElementById('spec').onchange = function updateSpecs(e) {
                let values = data.filter(obj => obj.spec == this.value).map(o => o.username);   
                document.getElementById('doctor1').value = document.querySelector(`[value=${values}]`).getAttribute('data-value');
              };
            </script> -->


                  
                  <div class="col-md-4"><label for="consultancyfees">
                                Consultancy Fees
                              </label></div>
                              <div class="col-md-8">
                              <!-- <div id="docFees">Select a doctor</div> -->
                              <input class="form-control" type="text" name="docFees" id="docFees" readonly="readonly"/>
                  </div><br><br>

                  <div class="col-md-4"><label>Appointment Date</label></div>
                  <div class="col-md-8"><input type="date" class="form-control datepicker" name="appdate"></div><br><br>

                  <div class="col-md-4"><label>Appointment Time</label></div>
                  <div class="col-md-8">
                    <!-- <input type="time" class="form-control" name="apptime"> -->
                    <select name="apptime" class="form-control" id="apptime" required="required">
                      <option value="" disabled selected>Select Time</option>
                      <option value="08:00:00">8:00 AM</option>
                      <option value="10:00:00">10:00 AM</option>
                      <option value="12:00:00">12:00 PM</option>
                      <option value="14:00:00">2:00 PM</option>
                      <option value="16:00:00">4:00 PM</option>
                    </select>

                  </div><br><br>

                  <div class="col-md-4">
                    <input type="submit" name="app-submit" value="Create new entry" class="btn btn-primary" id="inputbtn">
                  </div>
                  <div class="col-md-8"></div>                  
                </div>
              </form>
            </div>
          </div>
        </div><br>
      </div>
      
<div class="tab-pane fade" id="app-hist" role="tabpanel" aria-labelledby="list-pat-list">
        
              <table class="table table-hover">
                <thead>
                  <tr>
                    
                    <th scope="col">Doctor Name</th>
                    <th scope="col">Consultancy Fees</th>
                    <th scope="col">Appointment Date</th>
                    <th scope="col">Appointment Time</th>
                    <th scope="col">Current Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 

                    $con=mysqli_connect("localhost","root","","myhmsdb");
                    global $con;

                    $query = "select ID,doctor,docFees,appdate,apptime,userStatus,doctorStatus from appointmenttb where fname ='$fname' and lname='$lname';";
                    $result = mysqli_query($con,$query);
                    while ($row = mysqli_fetch_array($result)){
              
                      #$fname = $row['fname'];
                      #$lname = $row['lname'];
                      #$email = $row['email'];
                      #$contact = $row['contact'];
                  ?>
                      <tr>
                        <td><?php echo $row['doctor'];?></td>
                        <td><?php echo $row['docFees'];?></td>
                        <td><?php echo $row['appdate'];?></td>
                        <td><?php echo $row['apptime'];?></td>
                        
                          <td>
                    <?php if(($row['userStatus']==1) && ($row['doctorStatus']==1))  
                    {
                      echo "Active";
                    }
                    if(($row['userStatus']==0) && ($row['doctorStatus']==1))  
                    {
                      echo "Cancelled by You";
                    }

                    if(($row['userStatus']==1) && ($row['doctorStatus']==0))  
                    {
                      echo "Cancelled by Doctor";
                    }
                        ?></td>

                        <td>
                        <?php if(($row['userStatus']==1) && ($row['doctorStatus']==1))  
                        { ?>

													
	                        <a href="admin-panel.php?ID=<?php echo $row['ID']?>&cancel=update" 
                              onClick="return confirm('Are you sure you want to cancel this appointment ?')"
                              title="Cancel Appointment" tooltip-placement="top" tooltip="Remove"><button class="btn btn-danger">Cancel</button></a>
	                        <?php } else {

                                echo "Cancelled";
                                } ?>
                        
                        </td>
                      </tr>
                    <?php } ?>
                </tbody>
              </table>
        <br>
      </div>



      <div class="tab-pane fade" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
        
              <table class="table table-hover">
                <thead>
                  <tr>
                    
                    <th scope="col">Doctor Name</th>
                    <th scope="col">Appointment ID</th>
                    <th scope="col">Appointment Date</th>
                    <th scope="col">Appointment Time</th>
                    <th scope="col">Diseases</th>
                    <th scope="col">Allergies</th>
                    <th scope="col">Prescriptions</th>
                    <th scope="col">Bill Payment</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 

                    $con=mysqli_connect("localhost","root","","myhmsdb");
                    global $con;

                    $query = "select doctor,ID,appdate,apptime,disease,allergy,prescription from prestb where pid='$pid';";
                    
                    $result = mysqli_query($con,$query);
                    if(!$result){
                      echo mysqli_error($con);
                    }
                    

                    while ($row = mysqli_fetch_array($result)){
                  ?>
                      <tr>
                        <td><?php echo $row['doctor'];?></td>
                        <td><?php echo $row['ID'];?></td>
                        <td><?php echo $row['appdate'];?></td>
                        <td><?php echo $row['apptime'];?></td>
                        <td><?php echo $row['disease'];?></td>
                        <td><?php echo $row['allergy'];?></td>
                        <td><?php echo $row['prescription'];?></td>
                        <td>
                          <form method="get">
                          <!-- <a href="admin-panel.php?ID=" 
                              onClick=""
                              title="Pay Bill" tooltip-placement="top" tooltip="Remove"><button class="btn btn-success">Pay</button>
                              </a></td> -->

                              <a href="admin-panel.php?ID=<?php echo $row['ID']?>">
                              <input type ="hidden" name="ID" value="<?php echo $row['ID']?>"/>
                              <input type = "submit" onclick="alert('Bill Paid Successfully');" name ="generate_bill" class = "btn btn-success" value="Pay Bill"/>
                              </a>
                              </td>
                              </form>

                    
                      </tr>
                    <?php }
                    ?>
                </tbody>
              </table>
        <br>
      </div>




      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">...</div>
      <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
        <form class="form-group" method="post" action="func.php">
          <label>Doctors name: </label>
          <input type="text" name="name" placeholder="Enter doctors name" class="form-control">
          <br>
          <input type="submit" name="doc_sub" value="Add Doctor" class="btn btn-primary">
        </form>
      </div>
       <div class="tab-pane fade" id="list-attend" role="tabpanel" aria-labelledby="list-attend-list">...</div>
    </div>
  </div>
</div>
   </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.10.1/sweetalert2.all.min.js">
   </script>
  <script>
    const chatBody = document.querySelector(".chatbot-body");
const messageInput = document.querySelector(".message-input");
const sendMessageButton = document.querySelector("#send-message");
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");

// API setup
const API_KEY = "AIzaSyDzgGEt7Ouv8dIf9iYT6hUa_tdn2XCcdDc";
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`;

const userData = {
    message: null
}

const chatHistory = [];

// Create message element with dynamic classes & return it
const createMessageElement = (content, ...classes) => {
    const div = document.createElement("div");
    div.classList.add("message", ...classes);
    div.innerHTML = content;
    return div;
}

// Generate Response using bot API
const generateBotResponse = async (incomingMessageDiv) => {
    const messageElement = incomingMessageDiv.querySelector(".text-box");
    
     // Add user message to chat history
    chatHistory.push({
        role:"user",
        parts: [{text:userData.message}]
    });

    // API request options
    const requestOptions = {
        method : "POST",
        headers : {"Content-Type":"application/json"},
        body : JSON.stringify({
            contents:chatHistory
        })
    }

    try{
        //Fetch bot response from API
        const response = await fetch(API_URL,requestOptions);
        const data = await response.json();
        if(!response.ok) throw new Error(data.error.message);

        // Extract and display bot's response text
        const apiResponseText = data.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g,"$1").trim();
        messageElement.innerText = apiResponseText;

        // Add bot response to chat history
        chatHistory.push({
            role:"model",
            parts: [{text:apiResponseText}]
        });

    } catch(error){
        // Handle error in API response
        console.log(error);
        messageElement.innerText = error.message;
        messageElement.style.color = "#ff0000";
    } finally{
        incomingMessageDiv.classList.remove("thinking");
        chatBody.scrollTo({top: chatBody.scrollHeight,behavior:"smooth"});
    }
}

// Handle Outgoing user messages
const handleOutgoingMessage = (e) => {
    e.preventDefault();
    userData.message = messageInput.value.trim();
    messageInput.value = "";

    // Create and display user message
    const messageContent = `<div class="text-box"><p></p></div>`;

    const outgoingMessageDiv = createMessageElement(messageContent,"user-message");
    outgoingMessageDiv.querySelector(".text-box").textContent = userData.message;
    chatBody.appendChild(outgoingMessageDiv);
    chatBody.scrollTo({top: chatBody.scrollHeight,behavior:"smooth"});

    // Simulate bot response with thinking indicator after a delay
    setTimeout(() => {
        const messageContent = `<img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot Icon" class="chat-icon">
        <div class="text-box">
          <div class="thinking-indicator">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </div>
        </div>`;

        const incomingMessageDiv = createMessageElement(messageContent,"bot-message","thinking");
        chatBody.appendChild(incomingMessageDiv);
        chatBody.scrollTo({top: chatBody.scrollHeight,behavior:"smooth"});
        generateBotResponse(incomingMessageDiv);
    },600);
}

// Handle Enter key press for sending messages
messageInput.addEventListener("keydown", (e) =>{
    const userMessage = e.target.value.trim();
    if(e.key === "Enter" && userMessage) {
       handleOutgoingMessage(e);
    }
});

// Initialize emoji Picker & handle emoji selection
const picker = new EmojiMart.Picker({
    theme:"light",
    skinTonePosition:"none",
    previewPosition:"none",
    onEmojiSelect:(emoji) => {
        const{selectionStart:start,selectionEnd:end } = messageInput;
        messageInput.setRangeText(emoji.native,start,end,"end");
        messageInput.focus();
    },
    onClickOutside: (e) => {
        if(e.target.id === "emoji-picker"){
            document.body.classList.toggle("show-emoji-picker");
        }
        else{
            document.body.classList.remove("show-emoji-picker");
        }
    }
});

document.querySelector(".chatbot-footer").appendChild(picker);
sendMessageButton.addEventListener("click", (e) => handleOutgoingMessage(e));
chatbotToggler.addEventListener("click",() => document.body.classList.toggle("show-chatbot"))
closeChatbot.addEventListener("click",() => document.body.classList.remove("show-chatbot"));
  </script>


  </body>
</html>
