<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php

	@session_start();
    if( empty($_SESSION[spi_emp]) ){ header("location: ../../login.php"); }	
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");
	//get detpcode and faction
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	 $sql = "SELECT ".TB_EMPLOYEE.".emp_code,".TB_FACTION.".fact_code,".TB_DEPARTMENT.".dept_code,".TB_POSITION.".posi_code FROM ".TB_EMPLOYEE.",".TB_POSITION.",".TB_DEPARTMENT.",".TB_FACTION."  WHERE emp_code = '" .$_SESSION[spi_emp]. "' AND  ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code =".TB_DEPARTMENT.".dept_code AND ".TB_FACTION.".fact_code=".TB_DEPARTMENT.".fact_code  ";
	$result[employeeInfo] = $db->select_query($sql);
	$array[employeeInfo] = $db->fetch($result[employeeInfo]);
	print_r($array[employeeInfo] );

	//get email address by detpcode
	 $sql = "SELECT ".TB_EMPLOYEE.".emp_code,emp_eml,".TB_POSITION.".dept_code FROM ".TB_EMPLOYEE.",".TB_POSITION." WHERE ".TB_POSITION.".dept_code = '".$array[employeeInfo][dept_code]."' AND  posi_level >= '2' AND ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code  ";
	$result[employeeApprover] = $db->select_query($sql);
	$array[employeeApprover] = $db->fetch($result[employeeApprover]);
	print_r($array[employeeApprover] );
	//select type in database
	$db->closedb();
?>