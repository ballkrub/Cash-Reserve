<meta charset="utf-8">
<?php
	@session_start();
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");
	//check refundDate < Today -> Change to Expired
	$fileLogs = "logs/overRefunded/".date("Ymd").".txt";
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	$sql = "SELECT * FROM ".TB_CASHRESERVE."  WHERE docRefundDate  <  '".date('Y-m-d')."'  AND docStatus = 'WRE'    ";
	$result[checkRefundExpired] = $db->select_query($sql);
	$rows[checkRefundExpired] = $db->rows($result[checkRefundExpired]);
	while($array[checkRefundExpired] = $db->fetch($result[checkRefundExpired])) {
		$logString .= $array[checkRefundExpired][docNo]." Refund Expired  \n";
		$array_data = array(
			'docStatus' => 'ORE', 
			
		);
		$where_ = "docNo = ".$array[checkRefundExpired][docNo];
		// print_r($array_data);
		$db->update_db(TB_CASHRESERVE,$array_data,$where_);
	}
	file_put_contents($fileLogs, $logString);
	$db->closedb();
	//check reserve Expired over 7 days
	$logString  = "";
	$fileLogs = "logs/expired/".date("Ymd").".txt";
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	 $sql = "SELECT * FROM ".TB_CASHRESERVE."  WHERE docStatus = 'RA'  OR docStatus = 'WR'  OR docStatus = 'WA' ";
	
	$result[checkReserveExpired] = $db->select_query($sql);
	$rows[checkReserveExpired] = $db->rows($result[checkReserveExpired]);
	while($array[checkReserveExpired] = $db->fetch($result[checkReserveExpired])) {
		//if reserveDate >=7 working days next reserveDate change to expired
		$nextReserver7Days  =  calWorkingDayByDate($array[checkReserveExpired][docReserveDate],7);
		$todayDate = date('Y-m-d');
		if($todayDate>=$nextReserver7Days) {
			$logString .= $array[checkReserveExpired][docNo]." Reserve Cash Expired  \n";
			$array_data = array(
				'docStatus' => 'EX', 
			);
			$where_ = "docNo = ".$array[checkReserveExpired][docNo];
			// print_r($array_data);
			$db->update_db(TB_CASHRESERVE,$array_data,$where_);
		}
	}
	file_put_contents($fileLogs, $logString);
	$db->closedb();
	
?>