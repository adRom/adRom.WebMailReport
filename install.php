<?php 

include_once 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//CREATE TABLE IF NOT EXIST
$createTableQuery = "CREATE TABLE IF NOT EXISTS `".$db_name."`.`". $db_prefix . $db_table . "` ( 
`id` INT NULL AUTO_INCREMENT , 
`returnPath` VARCHAR(100) NULL , 
`recipient` VARCHAR(100) NOT NULL , 
`transactionId` VARCHAR(100) NULL , 
`timestamp` TIMESTAMP NOT NULL , 
`bounceType` VARCHAR(100) NOT NULL , 
`reason` VARCHAR(500) NOT NULL,
`mailingId` INT unsigned NULL , 
`delivered` INT unsigned NULL ,
`type` INT unsigned NULL COMMENT '1=bounce, 2=sendlog, 3=feedbackloop' ,
primary key (id) ) ENGINE = InnoDB";

if ($conn->query($createTableQuery) === FALSE) {
	echo "Error creating table: " . $conn->error;
} else {
	echo 'Table successfully created';
}
?>