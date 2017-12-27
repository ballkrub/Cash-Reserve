<meta charset="utf-8">
<?php
	@session_start();
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");
	//get Last  DocNumber by month and year
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	
	$add_result = $db->add_db(TB_MULTIPURPOSE,array(
		"mp_title"=>$_POST[mp_title],
		"mp_type"=>"cash_reserve"
	));
	 
	 $db->closedb();	
	if($add_result) {
		echo '<script language="Javascript">alert ("ทำรายการสำเร็จ  !");</script>';
		echo "<meta http-equiv=\"refresh\" content=\"0; URL = reserveType.php\">"; 
	}else {
		echo '<script language="Javascript">alert ("ทำรายการไม่สำเร็จ กรุณาทำรายการใหม่อีกครั้ง !");</script>';
		echo "<meta http-equiv=\"refresh\" content=\"0; URL = reserveType.php\">"; 
	}
	
	
	
?>