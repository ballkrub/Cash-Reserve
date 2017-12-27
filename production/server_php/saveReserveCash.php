<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	
	
	//upload file
	if($_FILES['docReserveAttach']['size'] > 0){
		$fileName = $_FILES['docReserveAttach']['name'];
		$tmpName  = $_FILES['docReserveAttach']['tmp_name'];
		$fileSize = $_FILES['docReserveAttach']['size'];
		$fileType = $_FILES['docReserveAttach']['type'];
		
		$path_parts = pathinfo($_FILES["docReserveAttach"]["name"]);
		$extension = $path_parts['extension'];
		$fp      = fopen($tmpName, 'r');
		$content = fread($fp, filesize($tmpName));
		$content = addslashes($content);
		fclose($fp);
		if(!get_magic_quotes_gpc())
		{
			$fileName = addslashes($fileName);
		}
	}
	if($_SESSION[spi_emp]!="") {
		//get Last  DocNumber by month and year
		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
		$sql = "SELECT * FROM ".TB_CASHRESERVE."  WHERE month(docDate)  =  '".date('m')."'   AND  year(docDate)  =  '".date('Y')."'   ";
		$result[getDoc] = $db->select_query($sql);
		$rows[getDoc] = $db->rows($result[getDoc]);
		if($rows[getDoc]==0) {
			 $docNo = date('ym')."000001";
		}else {
			 $docNo = date('ym').sprintf("%06d", $rows[getDoc]+1); 
		}
		//check level and qoata
		switch($_SESSION["reserveLevel"])  {
			case 1:
				//normal user 
				$add_result = $db->add_db(TB_CASHRESERVE,array(
					"docNo"=>$docNo,
					"docDate"=>date('Y-m-d'),
					"docEmpcode"=>$_SESSION[spi_emp],
					"docReserveDate"=>$_POST[docReserveDate],
					"docReceiveDate"=>$_POST[docReceiveDate],
					"docReserveType"=>$_POST[docReserveType],
					"docReserveFor"=>$_POST[docReserveFor],
					"docReserveAmount"=>$_POST[docReserveAmount],
					"docAttach"=>	$content , 
					"docAttachType"=>	$extension,
					"docReserveIP"=>	  $_SESSION["externalIP"]
				));
				break;
			case 2:
				//assist mgr user  level
				if($_POST[docReserveAmount]<=$qoataLimit["ASTMGR"]) { //auto approve
					$reserveStatus = "WR";
					$docApproved = "Y";
					$docApproveTime = date('Y-m-d H:i:s');
				}else {
					$reserveStatus = "WA";
					$docApproved = "N";
				}
				$add_result = $db->add_db(TB_CASHRESERVE,array(
					"docNo"=>$docNo,
					"docDate"=>date('Y-m-d'),
					"docEmpcode"=>$_SESSION[spi_emp],
					"docReserveDate"=>$_POST[docReserveDate],
					"docReceiveDate"=>$_POST[docReceiveDate],
					"docReserveType"=>$_POST[docReserveType],
					"docReserveFor"=>$_POST[docReserveFor],
					"docApproved"=>$docApproved,
					"docApprovalEmpcode"=>$_SESSION[spi_emp],
					"docApproveTime"=>$docApproveTime,
					"docReserveAmount"=>$_POST[docReserveAmount],
					"docStatus"=>$reserveStatus,
					"docAttach"=>	$content ,
					"docAttachType"=>	$extension,
					"docReserveIP"=>	$_SESSION["externalIP"]
				));
				break;
			case 3:
				//assist mgr user  level
				if($_POST[docReserveAmount]<=$qoataLimit["ASTMGR"] && !in_array($_SESSION[spi_emp],$excludeEmpArray)) { //auto approve
					$reserveStatus = "WR";
					$docApproved = "Y";
					$docApproveTime = date('Y-m-d H:i:s');
				}else {
					$reserveStatus = "WA";
					$docApproved = "N";
				}
				$add_result = $db->add_db(TB_CASHRESERVE,array(
					"docNo"=>$docNo,
					"docDate"=>date('Y-m-d'),
					"docEmpcode"=>$_SESSION[spi_emp],
					"docReserveDate"=>$_POST[docReserveDate],
					"docReceiveDate"=>$_POST[docReceiveDate],
					"docReserveType"=>$_POST[docReserveType],
					"docReserveFor"=>$_POST[docReserveFor],
					"docApproved"=>$docApproved,
					"docApprovalEmpcode"=>$_SESSION[spi_emp],
					"docApproveTime"=>$docApproveTime,
					"docReserveAmount"=>$_POST[docReserveAmount],
					"docStatus"=>$reserveStatus,
					"docAttach"=>	$content ,
					"docAttachType"=>	$extension,
					"docReserveIP"=>	 $_SESSION["externalIP"]
				));
				break;
			case 4:
				// mgr user  level tkorn chuto tassanee tanong sontaya
				if($_POST[docReserveAmount]<=$qoataLimit["MGR"]) { //auto approve <= 200000
					$reserveStatus = "WR";
					$docApproved = "Y";
					$docApproveTime = date('Y-m-d H:i:s');
				}else {
					$reserveStatus = "WA";
					$docApproved = "N";
				}
				$add_result = $db->add_db(TB_CASHRESERVE,array(
					"docNo"=>$docNo,
					"docDate"=>date('Y-m-d'),
					"docEmpcode"=>$_SESSION[spi_emp],
					"docReserveDate"=>$_POST[docReserveDate],
					"docReceiveDate"=>$_POST[docReceiveDate],
					"docReserveType"=>$_POST[docReserveType],
					"docReserveFor"=>$_POST[docReserveFor],
					"docApproved"=>$docApproved,
					"docApprovalEmpcode"=>$_SESSION[spi_emp],
					"docReserveAmount"=>$_POST[docReserveAmount],
					"docApproveTime"=>$docApproveTime,
					"docStatus"=>$reserveStatus,
					"docAttach"=>	$content ,
					"docAttachType"=>	$extension,
					"docReserveIP"=>	$_SESSION["externalIP"]
				));
				break;
		}
		
		$db->closedb();
		if($add_result) {
			echo "ทำรายการสำเร็จ กรุณาตรวจสอบในรายการใบสำรองเงิน !";
		}else {
			echo "ทำรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง !";
		}
		
		$db->closedb();
		
	} else {
		echo "Session หมดอายุ ทำรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง !";
	}
	
?>