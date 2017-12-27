<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	
	if($_SESSION['spi_emp']!="")  {
		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
		$db->del(TB_MULTIPURPOSE," mp_id = '".$_POST[mp_id]."' AND mp_type = 'cash_reserve' "); 
		$db->closedb();
	}else {
		echo " ลบเรียบร้อยแล้ว";
	}
?>
