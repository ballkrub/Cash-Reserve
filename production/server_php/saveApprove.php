<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	
	if($_SESSION['spi_emp']!="" AND $_POST['docNo'] != "")  {
		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
		//echo $_POST[docNo];
		//approve
		if($_POST[docApproved]=="Y") {
			$array_data = array(
				'docApproved' => $_POST[docApproved], 
				'docApprovalEmpcode' => $_SESSION['spi_emp'], 
				'docStatus' => 'WR', 
				'docApproveTime' => date('Y-m-d H:i:s'), 
				"docApproveIP"=>	 $_SESSION["externalIP"]
			);
		}else {
			//not approve and cancel this record
			$array_data = array(
				'docApproved' => $_POST[docApproved], 
				'docApprovalEmpcode' => $_SESSION['spi_emp'], 
				'docCancel' => 'Y', 
				'docStatus' => 'EX', 
				'docApproveTime' => date('Y-m-d H:i:s'), 
				"docApproveIP"=>	 $_SESSION["externalIP"]
			);
		}
		 $where_ = "docNo = ".$_POST[docNo];
		 // print_r($array_data);
		  $db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD); 
		  if($db->update_db(TB_CASHRESERVE,$array_data,$where_)==true){
				echo "ทำรายการพิจารณาสำเร็จ";
		  }else{
				echo "ทำรายการพิจารณาไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง";
		  }
			$db->closedb();
		}else {
				echo "Session หมดอายุ ทำรายการพิจารณาไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง";
		}
?>
