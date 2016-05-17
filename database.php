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
	
		$stmt = $conn->prepare("INSERT INTO `" . $db_prefix . $db_table . "` (returnPath, recipient, transactionId, timestamp, reason, delivered, type) VALUES (?,?,?,?,?,?,?)");
		
		if($stmt == false)
		{
			$this->mysqliResult['ok'] = false;	
			$this->mysqliResult['msg'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
		}
		else 
		{		
			$stmt->bind_param("sssssii", $returnPath, $recipient, $transactionId, $timestamp, $reason, $delivered, $type);
			foreach($jsondata as $item)
			{
				$recipient = $item['recipient'];
				$transactionId = $item['transactionId'];
				$timestamp = $item['timestamp'];
				$returnPath = $item['returnPath'];
				$delivered = $item['delivered'];
				$reason = $item['reason'];
				$reason = $conn->real_escape_string($reason);
				$type = QueueType::SendLog;
				
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
	
		$stmt = $conn->prepare("INSERT INTO `" . $db_prefix . $db_table . "` (returnPath, recipient, transactionId, timestamp, bounceType, reason, type) VALUES (?,?,?,?,?,?,?)");
		
		if($stmt == false)
		{
		  $this->mysqliResult['ok'] = false;	
		  $this->mysqliResult['msg'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
		}
		else 
		{		
			$stmt->bind_param("ssssssi", $returnPath, $recipient, $transactionId, $timestamp, $bounceType, $reason, $type);
			foreach($jsondata as $item)
			{
				$recipient = $item['recipient'];
				$transactionId = $item['transactionId'];
				$timestamp = $item['timestamp'];
				$returnPath = $item['returnPath'];
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
	
		$stmt = $conn->prepare("INSERT INTO `" . $db_prefix . $db_table . "` (returnPath, recipient, transactionId, timestamp, mailingId, type) VALUES (?,?,?,?,?,?)");
		
		if($stmt == false)
		{
			$this->mysqliResult['ok'] = false;	
			$this->mysqliResult['msg'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
		}
		else 
		{		
			$stmt->bind_param("ssssii", $returnPath, $recipient, $transactionId, $timestamp, $mailingId, $type);
			foreach($jsondata as $item)
			{
				$recipient = $item['recipient'];
				$transactionId = $item['transactionId'];
				$timestamp = $item['timestamp'];
				$returnPath = $item['returnPathDomain'];
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