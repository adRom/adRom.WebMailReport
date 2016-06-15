<?php

class Timer {

	var $start;
	var $pause_time;

	/*  start the timer  */
	function timer($start = 0) {
		if($start) { $this->start(); }
	}

	/*  start the timer  */
	function start() {
		$this->start = $this->get_time();
		$this->pause_time = 0;
	}

	/*  pause the timer  */
	function pause() {
		$this->pause_time = $this->get_time();
	}

	/*  unpause the timer  */
	function unpause() {
		$this->start += ($this->get_time() - $this->pause_time);
		$this->pause_time = 0;
	}

	/*  get the current timer value  */
	function get($decimals = 8) {
		return round(($this->get_time() - $this->start),$decimals);
	}

	/*  format the time in seconds  */
	function get_time() {
		list($usec,$sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}

class Logger {

	function __constructor(){}
	
	function writeLog($jsondata){
		$fp = fopen('logger.log','a');
		$time = date('d.m.Y h:i:s -->: ', time());
		$msg = "------------------------------------------------------------------------------------------------ \n". $time . ": \n" . print_R($jsondata, true).
		"\n------------------------------------------------------------------------------------------------\n";
		
		fwrite($fp,$msg);
		fflush($fp);
		fclose($fp);
	}
	
	function writeTimeLog($processing_time, $count){
		$fp = fopen('timer.log','a');
		fwrite($fp,$processing_time ." s for " . $count ." items." . "\n");
		fflush($fp);
		fclose($fp);
	}
	
	function writeError($errorMg = ""){	
		$time = date('d.m.Y h:i:s -->: ', time());
		$fp = fopen('error.log','a');
		
		$errorMsgLog = "------------------------------------------------------------------------------------------------ \n" . "ERROR: " . $time . ": " . $errorMg . "\n------------------------------------------------------------------------------------------------\n";
		
		fwrite($fp, $errorMsgLog );
		fflush($fp);
		fclose($fp);
	}
	
	function createFakeData(){

		$jsondata = array();
		for ($i = 1; $i <= 1000; $i++) {
			
			// $jsondata[] = array( 
				// "senderDomain" => "example.com", 
				// "recipient" => "john.doe@example.com", 
				// "timestamp" => "2015-02-12T14:59:30Z", 
				// "bounceType" => "Hardbounce", 
				// "reason" => "551 user does not exist",
				// "transactionId" => "abcd1234" 
			// ); //bounce
			
			// $jsondata[] = array(
				// "senderDomain" => "example.com",
				// "recipient" => "john.doe@example.com",
				// "timestamp" => "2015-02-12T14:59:30Z",
				// "delivered" => true,
				// "reason" => null,
				// "transactionId" => "abcd1234",
				// "ipAddress" => "198.51.100.1",
			// ); //sendlog /deliveredDropped
			
			// $jsondata[] = array(
				// "senderDomain" => "example.com",
				// "recipient" => "john.doe@example.com",
				// "timestamp" => "2015-02-12T14:59:30Z",
				// "mailingId" => "1",
				// "transactionId" => "abcd1234",
			// ); //FeedbackLoop
			
		}
		return $jsondata;
	}
	
}

?>
