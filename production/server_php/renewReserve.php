<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	
	if($_SESSION[spi_emp]!="" AND $_POST['docNo'] != "") { //check Session
	
		 $db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD); 
		 //get holiday
		$arrayHoliday = array();
		$sql = "SELECT vcn_date FROM ".TB_VACATION." WHERE 1 ";
		$result[vacation] = $db->select_query($sql);
		while($array[vacation] = $db->fetch($result[vacation])) {
			array_push($arrayHoliday,$array[vacation][vcn_date]);
		}
	
		//get old reference by DocNo
		$sql = "SELECT * FROM ".TB_CASHRESERVE."  WHERE docNo = '".$_POST['docNo']."'  ";
		$result[oldDocInfo] = $db->select_query($sql);
		$array[oldDocInfo] = $db->fetch($result[oldDocInfo]);
		//find renew Times and initial old data to new docNo
		$docRenewTimes =  $array[oldDocInfo][docRenewTimes]+1;
		$refundDate =  calWorkingDayByDate(date('Y-m-d'),7,$arrayHoliday );
		//change oldDoc renewStatus = Y
		$array_data = array(
			'docRenew' => 'Y', 
			'docStatus' => 'WRE', 
			'docRenewTimes' => $docRenewTimes,
			'docRefundDate' => $refundDate //+7 working days
		);
		 $where_ = "docNo = ".$_POST['docNo'];
		 $db->update_db(TB_CASHRESERVE,$array_data,$where_);
		 //Add Reason tables
		 $add_result = $db->add_db(TB_CASHRESERVERENEW,array(
			"renewDocNo"=>$_POST[docNo],
			"renewReason"=>$_POST[renewReason],
			"renewTimes"=>$docRenewTimes,
			"renewDatetime"=>date("Y-m-d H:i:s")
		));
		   if($add_result){
				echo "ต่ออายุใบสำรองเงินสำเร็จ";
		  }else{
				echo "ต่ออายุใบสำรองเงินไม่สำเร็จ";
		  }
		 $db->closedb();
	} else {
		echo '<script language="Javascript">alert ("Session หมดอายุ ทำรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง !");</script>';
		echo "<meta http-equiv=\"refresh\" content=\"0; URL = index.php\">"; 
	}
?>