<?php

include_once 'database.php';
include_once 'bench.php'; 
include_once 'config.php';

$database = new Database();
$logger = new Logger();

$benchmark = false;
$logging = false;

//Retrieving raw JSON
$data = file_get_contents('php://input');

//Creating JSON object
$jsondata = json_decode($data,true);

if($benchmark){
	$timer = new Timer(1);
	$jsondata = $logger->createFakeData(); //create fakedata 
}

if($logging){ $logger->writeLog($jsondata); }

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {	
	$logger->writeError($conn->connect_error);	
	exit();
}

/**********INSERT DATA INTO TABLE**********/
$conn->autocommit(FALSE);
$errorMsg = "";

if(!empty($jsondata)){
	
	//INSERT PACKAGES (a package can only contain ONE certain type (bounce, feedbackloop OR sendlog))
	
	if(isset($jsondata[0]['delivered'])){
		//sendlog	
		$mysqliResult = $database->insert_sendlog($conn, $jsondata, $db_prefix, $db_table);
	} 
	else if(isset($jsondata[0]['mailingId'])){
		//feedbackloop		
		$mysqliResult = $database->insert_feedbackloop($conn, $jsondata, $db_prefix, $db_table);
	}
	else {
		//bounce
		$mysqliResult = $database->insert_bounce($conn, $jsondata, $db_prefix, $db_table);
	}
	
	//REACT ON SUCCEEDED/FAILED INSERTS
	if($mysqliResult['ok'] == true){
		$conn->commit();
		$mysqliResult['stmt']->close();
	} else {
		$errorMsg = $mysqliResult['msg'] ." ". print_R($jsondata,true);
		$logger->writeError($errorMsg);
	}	
		
} else {
	$mysqliResult['ok'] = false;	
	$logger->writeError("JsonData was empty.");
}

if($benchmark && $mysqliResult['ok']){ $logger->writeTimeLog($timer->get(), count($jsondata));}

$jsonresult = array( "successful" => $mysqliResult['ok']);
echo json_encode($jsonresult);
?>