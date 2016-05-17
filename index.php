<html>
	<head>
		<link rel="stylesheet" href="style.css">
		<title>Index</title>
	</head>
	<body>
	
		<?php
			include_once 'config.php';
			
			$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
			if ($conn->connect_error)
			{
				echo '<div class="alert alert-danger" role="alert"><p>Connection failed: ' . $conn->connect_error . '</p></div>';
				exit;
			}
			
			//check if table exists
			$tableExist = $conn->query("SELECT id FROM `".$db_prefix . $db_table."` LIMIT 1;");
			if(empty($tableExist)){
				echo '<div class="alert alert-danger" role="alert">Table does not exist, call <a href="install.php" target="_blank"><b>install.php</b></a> at first</div>';
				exit;
			}

			if (isset($_GET["page"]) && !empty($_GET["page"])) { $page  = intval($_GET["page"]); } else { $page=1; }; 
			$start_from = ($page-1) * $items_per_page; 

			$selectQuery = "SELECT id, returnPath, recipient, transactionId, timestamp, bounceType, reason, mailingId, delivered, type FROM `".$db_prefix . $db_table."`";

			//QUERY-STRING: if is search
			if(isset($_GET['recipient']) && !empty($_GET['recipient'])){
				$selectQuery .= " WHERE recipient = ?";
			}

			$selectQuery .= " ORDER BY id DESC LIMIT ?, ?";

			$stmt = $conn->prepare($selectQuery);

			//BIND_VARIABLES: if is search
			if(isset($_GET['recipient']) && !empty($_GET['recipient'])){	
				$search = $conn->real_escape_string($_GET['recipient']);
				$stmt->bind_param("sii", $search, $start_from, $items_per_page);
			} else {
				$stmt->bind_param("ii", $start_from, $items_per_page);
			}

			$stmt->execute();
		?>

		<a href="index.php" style="text-decoration:none;">
			<h1>adRom Mail Report</h1>
		</a>
		
		<form action="index.php" method="GET" class="searchForm">
			<label>Recipient:</label>
			<input type="text" name="recipient" value="<?php echo isset($_GET['recipient']) ? $_GET['recipient'] : '';?>"/>
			<input type="submit" value="Search" class=""/>
		</form>
		<?php 
		if(isset($_GET['recipient']) && !empty($_GET['recipient'])){
		?>
		<form action="index.php" method="GET" class="searchForm" style="border-left:none;">	
			<input type="submit" value="Reset" class=""/>
		</form>
		<?php 
		}
		?>
		
		<table class="adromMailReport">
			<thead>
		  <tr>
			 <th style="width:140px;">Timestamp</th>
			 <th>Recipient</th>     
			 <?php if($returnPathVisible == true){ ?>
				<th>ReturnPath</th>
			 <?php } ?>
			 <th>BounceType</th>
			 <th>Reason</th>
			 <th>MailingId</th>
			 <th>Delivered</th>
			 <th>Type</th>
		  </tr>
		 </thead>
		 <tbody>
		<?php 
		$stmt->bind_result($id, $returnPath, $recipient, $transactionId, $timestamp, $bounceType, $reason, $mailingId, $delivered, $type);
		while ($stmt->fetch()) {
			$typeArray = array(1 => "Bounce", 2 => "SendLog", 3 => "FeedbackLoop");
		?> 
			<tr class="<?php 
				echo isset($typeArray[$type]) ? $typeArray[$type] : $typeArray[1];
				//QueueType				
			?>">
				<td><?php echo date('d.m.Y H:i:s',strtotime($timestamp)); ?></td>
				<td><?php echo $recipient; ?></td>		
				<?php if($returnPathVisible == true){ ?>
					<td><?php echo $returnPath; ?></td>
				<?php } ?>
				<td><?php echo $bounceType; ?></td>
				<td><?php echo $reason; ?></td>
				<td><?php echo $type == 3 ? $mailingId : ''; ?></td>
				<td><?php 
					if($type== 2){
						echo $delivered == 1 ? "Yes" : 'No'; 
					}
				?></td>
				<td><?php 			
					echo isset($typeArray[$type]) ? $typeArray[$type] : $typeArray[1];
				?></td>
			</tr>
		<?php 
		}; 
		?> 
		</tbody>
		</table>

		<?php 

		if($stmt->num_rows == 0){
			$total_records = 0;
		} else if(isset($_GET['recipient']) && !empty($_GET['recipient'])){
			$total_records = $stmt->num_rows;
		} else {
			$rs_result = $conn->query("SELECT * FROM `".$db_prefix . $db_table."`");
			$total_records = $rs_result->num_rows;
		}

		$total_pages = ceil($total_records / $items_per_page);

		if($total_records > 0){
		?>

		<div class="paginWrapper">
			
			<?php 		
				//If the current page is more than 1, show the First and Previous links		
				if($page > 1){
					echo "<a href='index.php?page=1' title='Page 1'>First</a>";
				}
			
				$max = $num_of_pages;
				
				if($page < $max){
					$sp = 1;
				} elseif($page >= ($total_pages - floor($max / 2)) ){
					$sp = $total_pages - $max + 1;
				} elseif($page >= $max){
					$sp = $page  - floor($max/2);
				}
				
				//If the current page >= $max then show link to 1st page
				if($page >= $max){
			
					echo "<a href='index.php?page=1' title='Page 1'>1</a>";
					echo "<span>..</span>";
				}
				
				for($i = $sp; $i <= ($sp + $max -1);$i++) {
					if($i > $total_pages){
						continue;
					}
					if($page == $i) {
						echo '<a class="active" href="index.php?page='.$i.'" title="Page '.$i.'">'. $i .'</a>';
					} else {
						echo '<a class="" href="index.php?page='.$i.'" title="Page '.$i.'">'. $i .'</a>';
					}		
				}
				
				if($page < ($total_pages - floor($max / 2))){
					echo "<span>..</span>";
					echo "<a href='index.php?page=".$total_pages."' title='Page ".$total_pages."'>". $total_pages."</a>";
				}
				
				if($page < $total_pages){
					echo "<a href='index.php?page=".$total_pages."' title='Page ".$total_pages."'>Last</a>";
				}
				
			?>

		</div>

		<?php 

		} else {
			//no records to display
			echo '<div class="warning">No records found.</div>';
		}

		$countArray = array("1" => "Bounce", "2" => "SendLog", "3" => "FeedbackLoop");
		$resultArray = array();
		foreach($countArray as $key => $ca){
			$bounceResult = $conn->query("SELECT COUNT(id) FROM `".$db_prefix . $db_table."` WHERE type=". $key .";");	
			$result = $bounceResult->fetch_row();
			$resultArray[$ca] = $result[0];
		}

		echo '<div style="clear:both;"></div>';
		echo '<table class="countTable">
			<thead>
				<tr><td colspan="2" style="background-color:#008ae3;color:#fff;padding:4px;font-weight:bold;">Legend/Count</td></tr>
				<tr>
					<td class="bold">Type</td>
					<td class="alignRight bold">Count</td>
				</tr>
			</thead>
			<tbody>';	
			foreach($resultArray as $key => $ra){
				echo '<tr class="'.$key.'"> <td>'.$key.'</td> <td class="alignRight">'.number_format($ra, 0, ",", ".").'</td> </tr>';
			}
		echo '</tbody>
			</table>';	
		?>
	
	</body>
</html>