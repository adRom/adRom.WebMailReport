<?php

include_once 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

//check if table exists
$tableExist = $conn->query("SELECT id FROM `".$db_prefix . $db_table."` LIMIT 1;");
if(empty($tableExist)){
	echo 'Table does not exist, call <a href="install.php" target="_blank"><b>install.php</b></a> at first';
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

<style>

*{
	font-family:verdana;
	font-size:9pt;
}

h1{
	color:#008ae3;
	font-size:20pt;
}

.alignRight{
	text-align:right !important;
}
.bold{
	font-weight:bold !important;
}

form.searchForm {    
    border: 1px solid #e2e2e2;	
    padding: 6px;
    float: left;
}
form.searchForm label{
	font-weight:bold;
}

.adromMailReport{
	border:1px solid #a7a7a7;	
	width:100%;	
	border-collapse:collapse;
	border-spacing:0;
}
.adromMailReport th{
	border:1px solid #e2e2e2;
	background-color:#008ae3;
	color:#ffffff;
	padding:10px;
	text-align:left;
}
.adromMailReport td{
	padding:6px 10px;
	border:1px solid #e2e2e2;
}
.adromMailReport tr.Bounce{
	background-color:#EFEEFF;
}
.adromMailReport tr.SendLog{
	background-color:#E6FFDF;
}
.adromMailReport tr.FeedbackLoop{
	background-color:#FFFEE1;
}

.paginWrapper {
	padding-top:2px;
}

.paginWrapper a,
.paginWrapper span{
    text-decoration:none;
    color:#000;
    border:1px solid #e2e2e2;
	border-right-width:0px;
    padding:6px;
	padding-left:10px;
	padding-right:10px;
	float:left;
}

.paginWrapper a:last-child{
	border-right-width:1px;	
}

.paginWrapper a.active{
    font-weight:bold;
	color:#ffffff;
	background-color:#008ae3;
}

.warning {
	color:#B30000;
	font-weight: bold;
	margin: 10px 0 0 0;
}

.countTable{
	margin-top:20px;
	width: 300px;	
	border-collapse:collapse;
	border-spacing:0;
}
.countTable th{
	text-align:left;
}
.countTable thead td{
	background-color: #E6E6E6;
}
.countTable td{
    border: 1px solid #C3C3C3;
}
.countTable tr.Bounce{
	background-color:#EFEEFF;
}
.countTable tr.SendLog{
	background-color:#E6FFDF;
}
.countTable tr.FeedbackLoop{
	background-color:#FFFEE1;
}
.version{
	float: right;
    padding: 4px;
	font-size: 10px;
	margin-top: 10px;
}
</style>

<a href="index.php" style="text-decoration:none;">
<h1>adRom Mail Report</h1>
</a>
<?php
// $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// $currentQuery = parse_url($currentUrl, PHP_URL_QUERY);
// parse_str($currentQuery, $urlParams);
// $urlParams['aaa'] = 'bbb';
// echo http_build_query($urlParams) . "\n";
?>

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

<div class="version">Version: 1.4</div>