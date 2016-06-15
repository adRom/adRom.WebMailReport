<?php 

include_once 'config.php';

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

$msg = "";

// Check connection
if ($conn->connect_error)
{
	//die("Connection failed: " . $conn->connect_error);
	$alertClass = "alert-danger";
	$msg = "<p>Connection failed: " . $conn->connect_error . "</p>";
} 
else 
{
	//CREATE TABLE IF NOT EXIST
	$createTableQuery = "CREATE TABLE `".$db_name."`.`". $db_prefix . $db_table . "` ( 
	`id` INT NULL AUTO_INCREMENT , 
	`senderDomain` VARCHAR(100) NULL ,
	`ipAddress` VARCHAR(100) NULL ,
	`recipient` VARCHAR(100) NOT NULL ,
	`transactionId` VARCHAR(100) NULL ,
	`timestamp` TIMESTAMP NOT NULL ,
	`bounceType` VARCHAR(100) NOT NULL ,
	`reason` VARCHAR(500) NOT NULL,
	`mailingId` INT unsigned NULL ,
	`delivered` INT unsigned NULL ,
	`type` INT unsigned NULL COMMENT '1=bounce, 2=sendlog, 3=feedbackloop',
	primary key (id) ) ENGINE = InnoDB";

	if ($conn->query($createTableQuery) === FALSE) {
		$alertClass = "alert-danger";
		$msg = "<p>Error creating table: " . $conn->error . "</p>";	
	} else {
		$alertClass = "alert-success";
		$msg = '<p>Table successfully created</p>';
		$msg .= "<p><i>Please delete the 'install.php'-file now.</i></p>";
	}
}

?>
<html>
	<head>
		<link rel="stylesheet" href="style.css">
		<title>Install</title>
	</head>
	<body>
		<div class="alert <?php echo $alertClass; ?>" role="alert">
		<?php echo $msg; ?>
		</div>
	</body>
</html>