<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	
	
	if($_SESSION[spi_emp]!="" AND $_POST['docNo'] != "") {
		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
		//echo $_POST[docNo];
		 //get holiday
		$arrayHoliday = array();
		$sql = "SELECT vcn_date FROM ".TB_VACATION." WHERE 1 ";
		$result[vacation] = $db->select_query($sql);
		while($array[vacation] = $db->fetch($result[vacation])) {
			array_push($arrayHoliday,$array[vacation][vcn_date]);
		}
		
		 $refundDate =  calWorkingDayByDate(date('Y-m-d'),7,$arrayHoliday );
		
		$array_data = array(
			'docRefundDate' => $refundDate ,
			'docCheckerEmpcode' => $_SESSION['spi_emp'], 
			'docChecked' => 'Y', 
			'docStatus' => 'WRE', 
			'docCheckedTime' => date('Y-m-d H:i:s'), 
			"docPayIP"=>	 $_SESSION["externalIP"]
		);
		 $where_ = "docNo = ".$_POST[docNo];
		 // print_r($array_data);
		
		  if($db->update_db(TB_CASHRESERVE,$array_data,$where_)==true){
				echo "ตรวจสอบรายการและจ่ายเงินสำรองเรียบร้อยแล้ว";
		  }else{
				echo " ตรวจสอบรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง";
		  }
		  $db->closedb();
		  
	} else {
		echo "Session หมดอายุ ตรวจสอบรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง";
	}
	
?>
