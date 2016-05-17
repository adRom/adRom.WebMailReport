<?php
abstract class QueueType
{
	const Bounce = 1;
	const SendLog = 2;
	const FeedbackLoop = 3;
	
	function getName($value){		
		$queueTypeClass = new ReflectionClass ('QueueType');
		$constants = $queueTypeClass->getConstants();
		$foundConstantName = "";
		foreach($constants as $constantName => $constantValue){
			if($constantValue == $value){
				$foundConstantName = $constantName;
			}
		}
		return $foundConstantName;
	}
	
	function getAll(){
		$queueTypeClass = new ReflectionClass ('QueueType');
		$constants = $queueTypeClass->getConstants();
		return $constants;
	}
}
?>