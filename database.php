<?php
include_once '/enums/QueueTypes.php';

class Database {
	
	var $mysqliResult;
	
	function __construct() {
		$this->mysqliResult = array(
			"ok" => true,
			"msg" => "",
			"stmt" => null
		);
	}

	function insert_sendlog($conn, $jsondata, $db_prefix, $db_table){
	
		$stmt = $conn->prepare("INSERT INTO `" . $db_prefix . $db_table . "` (senderDomain, recipient, transactionId, timestamp, reason, delivered, type, ipAddress) VALUES (?,?,?,?,?,?,?,?)");
		
		if($stmt == false)
		{
			$this->mysqliResult['ok'] = false;	
			$this->mysqliResult['msg'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
		}
		else 
		{		
			$stmt->bind_param("sssssiis", $senderDomain, $recipient, $transactionId, $timestamp, $reason, $delivered, $type, $ipAddress);
			foreach($jsondata as $item)
			{
				$recipient = $item['recipient'];
				$transactionId = $item['transactionId'];
				$timestamp = $item['timestamp'];
				$senderDomain = $item['senderDomain'];
				$delivered = $item['delivered'];
				$reason = $item['reason'];
				$reason = $conn->real_escape_string($reason);
				$type = QueueType::SendLog;
				$ipAddress = $item['ipAddress'];
				
				if($stmt->execute() === FALSE){
					$this->mysqliResult['ok'] = false;	
					$this->mysqliResult['msg'] = "SENDLOG could not be inserted" ."\n";
				}				
			}
			$this->mysqliResult['stmt'] = $stmt;
		}
		return $this->mysqliResult;
	}

	function insert_bounce($conn, $jsondata, $db_prefix, $db_table){
	
		$stmt = $conn->prepare("INSERT INTO `" . $db_prefix . $db_table . "` (senderDomain, recipient, transactionId, timestamp, bounceType, reason, type) VALUES (?,?,?,?,?,?,?)");
		
		if($stmt == false)
		{
		  $this->mysqliResult['ok'] = false;	
		  $this->mysqliResult['msg'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
		}
		else 
		{		
			$stmt->bind_param("ssssssi", $senderDomain, $recipient, $transactionId, $timestamp, $bounceType, $reason, $type);
			foreach($jsondata as $item)
			{
				$recipient = $item['recipient'];
				$transactionId = $item['transactionId'];
				$timestamp = $item['timestamp'];
				$senderDomain = $item['senderDomain'];
				$bounceType = $item['bounceType'];
				$reason = $item['reason'];
				$reason = $conn->real_escape_string($reason);
				$type = QueueType::Bounce;			
				
				if($stmt->execute() === FALSE){
					$this->mysqliResult['ok'] = false;
					$this->mysqliResult['msg'] = "BOUNCE could not be inserted" ."\n";
				}
			}
			$this->mysqliResult['stmt'] = $stmt;
		}
		return $this->mysqliResult;
	}

	function insert_feedbackloop($conn, $jsondata, $db_prefix, $db_table){
	
		$stmt = $conn->prepare("INSERT INTO `" . $db_prefix . $db_table . "` (senderDomain, recipient, transactionId, timestamp, mailingId, type) VALUES (?,?,?,?,?,?)");
		
		if($stmt == false)
		{
			$this->mysqliResult['ok'] = false;	
			$this->mysqliResult['msg'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
		}
		else 
		{		
			$stmt->bind_param("ssssii", $senderDomain, $recipient, $transactionId, $timestamp, $mailingId, $type);
			foreach($jsondata as $item)
			{
				$recipient = $item['recipient'];
				$transactionId = $item['transactionId'];
				$timestamp = $item['timestamp'];
				$senderDomain = $item['senderDomain'];
				$mailingId = $item['mailingId'];
				$type = QueueType::FeedbackLoop;			
				
				if($stmt->execute() === FALSE){
					$this->mysqliResult['ok'] = false;
					$this->mysqliResult['msg'] = "FEEDBACKLOOP could not be inserted" ."\n";
				}
			}	
			$this->mysqliResult['stmt'] = $stmt;
		}
		return $this->mysqliResult;
}

}
?>