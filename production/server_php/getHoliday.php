<?php
	@session_start();
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD); 
	 //get holiday
	$arrayHoliday = array();
	$sql = "SELECT vcn_date FROM ".TB_VACATION." WHERE 1 ORDER BY vcn_date ";
	$result[vacation] = $db->select_query($sql);
	while($array[vacation] = $db->fetch($result[vacation])) {
		array_push($arrayHoliday,$array[vacation][vcn_date]);
	}
	echo join($arrayHoliday,",");
	$db->closedb();
		
?>