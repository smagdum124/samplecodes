<?php
include_once('config.php');
session_start();

if(!isset($_SESSION['sess_var']))
{
	header("Location : logout.php");
	exit;
}

$errorMsg="";

if(isset($_POST['detagRFID']))
{

	$postdetag = array("rfidnumber"=>$_POST['rfid']);

	$urldetag = $baseUrl.'api/rfid/deleteRfid.php';
	$jsonResponsedetag = rest_call('POST',$urldetag, $postdetag,'multipart/form-data',$_COOKIE['kpmg-access']);

	$responsedetag = json_decode($jsonResponsedetag,true);
	//print_r($responsedetag);
	if($response['msg']=="no record")
	{
		$errorMsg="Detaged";
		//echo "Done";
	} else {
		$errorMsg=$response['msg'];
		//print_r($response);
	}
	
}

if(isset($_POST['newrfid']))
{
	if(!empty($_POST['newrfid'])){
		$postUploadAPI = array("rfidnumber"=>$_POST['rfid'],"newrfidnumber"=>$_POST['newrfid']);

		$urlassign = $baseUrl.'api/rfid/updateRfid.php';
		$jsonResponse = rest_call('POST',$urlassign, $postUploadAPI,'multipart/form-data',$_COOKIE['kpmg-access']);
		//print_r($jsonResponse);
		$response = json_decode($jsonResponse,true);
		if($response['msg']=="Successfull")
		{
			$errorMsg="Updated";
			//echo "Done";
		} else {
			$errorMsg=$response['msg'];
			//print_r($response);
		}
	}else{
		$errorMsg=$response['Please enter EPC code !'];
	}

}
if(!isset($_GET['id']))
{
	//echo "invalid access";
	//exit;
}
if(!$_SERVER['HTTP_REFERER']==$baseUrl."RFIDwebapp/card.php")
{
	//echo "invalid access";
	//exit;
}



/*
$postData = array("id"=>$_GET['id']);
$url = $baseUrl.'api/rfid/searchrfid.php';
$jsonResponse = rest_call('POST',$url, $postData,'multipart/form-data',"Bearer ".$_COOKIE['kpmg-access']);
$response = json_decode($jsonResponse,true)['cred'];
*/

/* get kpmg token */
    $postData = array("Username"=>"admin", "Password"=>"Pass@4321");
    $jsonData=json_encode($postData);
	//Added by Shubham - 08/08
    //$url = 'http://10.188.0.163:44909/api/Login';

	//$url = 'http://10.188.7.135:44442/api/Login';
    ////$jsonResponse = rest_call('POST',$url, $jsonData,'application/json',NULL,'44909');
	//$jsonResponse = rest_call('POST',$url, $jsonData,'application/json',NULL,'44442');
	//Experiential testing api
	$url = 'https://kpmg.experientialetc.com/kpmgApi/Login.php';
	$jsonResponse = rest_call('POST',$url, $jsonData,'application/json',NULL, '443');
    $response = json_decode($jsonResponse,TRUE);

    $kpmgToken=$response['token'];
/*end get kpmg token */

/* get reord from kpmg api */
	//Added by Shubham - 08/08
//$url='http://10.188.0.163:44909/api/Booking/GetVisitor?Id='.$_GET["id"];
//$url='http://10.188.7.135:44442/api/Booking/GetVisitor?Id='.$_GET["id"];
////$jsonResponse = rest_call('POST',$url, NULL,NULL,$kpmgToken,'44909');

//$jsonResponse = rest_call('POST',$url, NULL,NULL,$kpmgToken,'44442');

//Experiential testing api
$url= 'https://kpmg.experientialetc.com/kpmgApi/GetVisitor.php?Id='.$_GET["id"];
$jsonResponse = rest_call('POST',$url, NULL,NULL,$kpmgToken, '443');
$response = json_decode($jsonResponse,TRUE);

$urlrfid= $baseUrl.'api/rfid/getAssignedRFID.php';
$postDatarfid  = array("email"=>$response['EmailAddress'], "bookingId"=>$response['BookingId']);
$jsonResponserfid = rest_call('POST',$urlrfid, $postDatarfid,'multipart/form-data',$_COOKIE['kpmg-access']);
$responserfid = json_decode($jsonResponserfid,TRUE);
//print_r($responserfid);

//exit;
/* end get reord from kpmg api */

$insDate=strtotime($response['BookingDateTime']);//['date']
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID WebApp</title>
</head>
<style>

    body{
        /*background-image: url(assets/back.png);
         background-size: 50% 120%; */
        /* background-size: 120% 50% ; */
        /* background-size: 100% 100%; */
        /* background-repeat: no-repeat; */
        /* background-size: auto; */
        /* height: 100vh; */
        margin: 0px;
        font-family: 'UNIVERSFORKPMG-BOLD';
        src: url("font/UNIVERSFORKPMG-BOLD.TTF") format("truetype");
    }

    .searchimg{
        position: absolute;
        top: 50%;
        left: 30%;
        height: 5%;
        width: 15%;
    }

    .center{
        position: absolute;
        font-size:48px;
        top: 8%;
        left: 35%;
        
    }

    .boxContainer{
        margin: auto;
        margin-top: 20%;
        position: relative;
        left: -10%;
        width: 300px;
        height: 42px;
        border: 4px solid #2988b9 ;
        padding: 0px 10px;
        border-radius: 50px;
    }

    .elementsContainer{
        width: 50%;
        height: 50%;
        vertical-align: middle;
    }

    .search{
        border: none;
        height: 100%;
        width: 100%;
        padding: 0px 5px;
        border-radius: 50px;
        font-size: 18px;
        font-family: "Nunito";
        color: #424242;
        font-weight: 500;
    }

    .search:focus{
        outline: none;
    }

    .material-icons{
        font-size: 24;
        color: #2980b9;
    }

    @font-face {    
        font-family: 'UNIVERSFORKPMG-BOLD';
        src: url("font/UNIVERSFORKPMG-BOLD.TTF") format("truetype");
    }

    p{
        font-family: 'UNIVERSFORKPMG-BOLD';
        src: url("font/UNIVERSFORKPMG-BOLD.TTF") format("truetype");
    }

    .cardsCont{
        position: absolute;
        top:22%;
        display: flex;
        flex-wrap: wrap;
        flex-basis: 15em;
        justify-content: center;
    }

    .cardsContHead{
        position: absolute;
        top: -6%;
    }

    .cardsContHead, .searchContHead{
        width:100%;
        display: flex;
        font-size:140%;
        justify-content: center;    
    }

    .card{
        /* background-color: #00338D; */
        /* width: 30vw;
        height: 35vh; */
        width: 480px;
        height: 250px;
        border: 1px;
        border-radius: 8px;
        /* padding: 50px; */
        /* margin: 20px; */
        margin-left: 1.7%;
        margin-right: 1.7%;
        margin-bottom: 4%;
        /*background-image: url(assets/cardBG.png);*/
        background-size: 100% 100%;
        /* text-align:left; */
    }


    .assignRFIDCont{
        width:100%;
        display: block;
        justify-content: flex;
		width:100%;
        /* display: inline-block;*/
    }

    .assignRFIDCont .card{
        margin: 0 auto;
    }

    .backBtn{
        margin-left: 12vw;
        background-image: url(assets/backIcon.png);
        background-size: 100% 100%;
        width: 28px;
        height: 38px;
        position: relative;
        top: 5%;
        z-index: 1;
		margin-top:20px;
    }

    .idcardCont{    
        height: 60%;
        /* border: 1px red solid; */
        margin-left: 5%;
        margin-right: 5%;
        display: flex;
    }
    
     .idcardImgCont{
        /*background-color: yellow;*/
        border-radius:12px;
        /*height: 10vh;*/
        /*width: 30%;*/
        width: 170px; 
        height: 160px;
        margin-top:5%;
        margin-left:12%;
        /*position: relative; */
        top: 10%;
        left: 70%;
    } 
    
    .idcardImg{
        width: 100%; 
        height: 100%;
    } 

    .idcardCreds{
        color: white;
        font-size:;
        font-family: 'UNIVERSFORKPMG-BLACK';
        src: url("font/UNIVERSFORKPMG-BLACK.TTF") format("truetype");
		
    }

    #selCard{
		background-image: url("assets/greencardBG.png"); margin-left:auto;  margin-right:auto;
		height: 300%;
    }
	.rfid_no{
	position: relavive; width: 350px;height: 55px; border-radius: 10px; border: solid grey;margin-left:auto;  margin-right:auto;display:block;margin-top:20px;margin-bottom:20px;
	}
	/*.assignRFID{
	width: 200px;border-radius: 8px; background-color: #00338D;height:45px; font-size: 18px; color: white; border: none; margin-left:auto;  margin-right:auto;display:block;position: relative;
	}
	*/
	
	.searchb{
		display:flex;
	}
	
	.assignRFID{
	width: 200px;border-radius: 8px; background-color: #00338D;height:45px; font-size: 18px; color: white; border: none; margin-right:auto;display:block;position: relative;margin-left:10px;cursor:pointer;
	}
	.detagRFID{
		width: 200px;border-radius: 8px; background-color: #00338D;height:45px; font-size: 18px; color: white; border: none; margin-left:auto; display:block;position: relative;margin-right:10px;cursor:pointer;
	}
	
	.error{
        width: 480px;
        height: 50px;
        border: 1px;
		margin-left:auto;  margin-right:auto;display:block;
		color: red;
    }

    #assignRFID:disabled {
      background-color: #808080;
    }

    #assignRFID:not(:disabled) {
      background-color: #00338D;
    }

    #detagRFID:disabled {
      background-color: #808080;
    }
</style>
<script>
	function buttonDis(){
		/*
		document.getElementById("assignRFID").style.background='#808080';
		document.getElementById("assignRFID").disabled = false;
		document.getElementById("detagRFID").style.background='#00338D';
		document.getElementById("detagRFID").disabled = true;
		*/
	}
	
</script>
<body>
<!-- Added by shubham Jadhav - for logo - 17/2 - start  -->
    <div style="margin:15px;">
        <img src = "./assets/KPMG_logo.png" style = "width:15%;vertical-align:middle"/>
        <span style="vertical-align:middle;font-size:3vw;color: #00338D;">Innovation Kaleidoscope Centre</span>
		
    </div>
    <!-- Added by shubham Jadhav - for logo - 17/2 - end  -->
<div class="assignRFIDdiv" id="assignRFIDdiv" style="width:100%;height:300px;">
    <form id="myForm" method="POST">
        <div class="backBtn" id="assignRFIDBack" onclick="history.back()"></div>
		<div class="assignRFIDCont" id="assignRFIDCont">
			<div class="error">
<?php
if($errorMsg=="Detaged")
{
?>
<script>
alert("Rfid Detaged");
window.location.href="search.php";
</script>
<?php
} else if($errorMsg=="Updated")
{
?>
<script>
alert("Rfid Updated");
window.location.href="search.php";
</script>
<?php
} else {
echo $errorMsg;
}
?>
			</div>
		</div>
        <div class="assignRFIDCont" id="assignRFIDCont">
            <div class="card" id="selCard" name="selCard">
				<div class="idcardCont" id="innerselCard">
					<div class="idcardCreds">
						<h3 class="Name">Name: &nbsp;<?php echo $response['Name']; ?></h3>
						<h3 class="Company">Company: &nbsp;<?php echo $response['OrganizationName']; ?></h3>
						<h3 class="Email">Email: &nbsp;<?php echo $response['EmailAddress']; ?></h3>
						<h3 class="Mobile number">Mobile number: &nbsp;<?php echo $response['MobileNumber']; ?></h3>
					
						<h3 class="MeetingId">Meeting ID: &nbsp;<?php echo $response['BookingId']; ?></h3>
						<h3 class="RfidNo">RFID no: &nbsp;<?php echo $responserfid['list']; ?></h3>
					</div>
<?php
if(!$response['FileContent']=="")
{
?>
					<div class="idcardImgCont">
						<img class="idcardImg" src="data:image/png;base64,<?php echo $response['FileContent']; ?>" alt="">
					</div>
<?php
}
?>
				</div>
			</div>
			
            <input id="emp_name" name="emp_name" type="hidden" value="<?php echo $response['Name']; ?>">
			<input id="email_id" name="email_id" type="hidden" value="<?php echo $response['EmailAddress']; ?>">
			<input id="company" name="company" type="hidden" value="<?php echo $response['OrganizationName']; ?>">
			<input id="datetime" name="datetime" type="hidden" value="<?php echo $insDate; ?>">
			<input id="img_url" name="img_url" type="hidden" value="<?php echo $response['FileContent']; ?>">
			<input id="booking_id" name="booking_id" type="hidden" value="<?php echo $response['BookingId']; ?>">
			
			<input type="hidden" name="rfid" class="rfid_no" id="rfid" max="16" value="<?php echo $responserfid['list']; ?>">
           <input type="text" name="newrfid" class="rfid_no" id="newrfid" max="16" placeholder="Update Rfid Number" required>

  <div class="searchb">
    <button type="submit" id="detagRFID" class="detagRFID btn" name="detagRFID" onClick="CheckEmpty(this)" >Detag RFID</button>
    <button type="submit" id="assignRFID" class="assignRFID btn" name="assignRFID" onClick="CheckEmpty(this)">Update RFID</button>
  </div>
        </div>
    </form>
				<!-- Added by shubham Jadhav - for copyright footer  - 14/1 - start  -->
    <div style="margin:25px;font-size:0.9vw;bottom:10px;position:relative">
        &copy; <span>2023 KPMG Assurance and Consulting Services LLP, an Indian Limited Liability Partnership and a member firm of the KPMG global organization of independent member firms affiliated with KPMG International Limited ("KPMG International"), an English Company limited by guarantee. All rights reserved. The KPMG name and logo are registered trademarks of KPMG International.
 KPMG Assurance and Consulting Services LLP has entered into sub-license arrangements with certain entities in India. These application(s) are also for the use of such sub-licensees in India.</span>
    </div>
    <!-- Added by shubham Jadhav - for copyright footer - 14/1 - end  -->
</div>

	</body>
<script>

		
	/*	const FormElement = document.querySelector('form');
		FormElement.addEventListener('submit', event =>{
			var rfid = document.getElementById("newrfid").value;
			if( rfid.length == 0){
				alert('Please enter EPC code !');
				event.preventDefault();
			}else{
				var validRegx = /^[A-Za-z0-9]+$/;
				if(rfid.match(validRegx)){
				}else{
					alert("Special characters are not allowed(\`~'&*%$#@!,./;). Please enter a valid EPC code.");
					event.preventDefault();
				}
			}
		});
		*/
	
	function CheckEmpty(elem){
		var rfid = document.getElementById("newrfid").value;
		if(elem.id == "assignRFID"){
			
			if( rfid.length == 0){
				alert('Please enter EPC code !');
				//event.preventDefault();
			}else{
				var validRegx = /^[A-Za-z0-9]+$/;
				if(rfid.match(validRegx)){
				}else{
					alert("Special characters are not allowed(\`~'&*%$#@!,./;). Please enter a valid EPC code.");
					//event.preventDefault();
				}
			}
		}else if(elem.id == "detagRFID"){
			if( rfid.length !== 0){
				alert('Are you sure you want to Detag ?');
				//event.preventDefault();
			}
		}
	}	
	
	
	  // added by magdum -06-06-23
	
	 var inputBox = document.getElementById("newrfid");
    var detagRFIDButton = document.getElementById("detagRFID");
    var assignRFIDButton = document.getElementById("assignRFID");

    assignRFIDButton.disabled = true;

    inputBox.addEventListener("input", function() {
      if (inputBox.value !== "") {
        detagRFIDButton.disabled = true;
        assignRFIDButton.disabled = false;
      } else {
        detagRFIDButton.disabled = false;
        assignRFIDButton.disabled = true;
      }
    });

</script>
</html>