<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	
	if($_SESSION[spi_emp]!="" AND $_POST['docNo'] != "") {
		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
		//echo $_POST[docNo];
		$array_data = array(
			'docCancel' => 'Y', 
			'docStatus' => 'EX', 
			'docCancelTimes' => date('Y-m-d H:i:s'), 
		);
		 $where_ = "docNo = ".$_POST[docNo];
		 // print_r($array_data);
		
		  if($db->update_db(TB_CASHRESERVE,$array_data,$where_)==true){
				echo "ยกเลิกรายการแล้ว ";
		  }else{
				echo " ยกเลิกรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง";
		  }
		  $db->closedb();
	} else {
		echo "Session หมดอายุ ตรวจสอบรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง";
	}
	
?>
