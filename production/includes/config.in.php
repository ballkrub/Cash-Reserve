<?
	//local TEST
	define("DB_HOST","localhost");
	define("DB_NAME","spi");
	define("DB_USERNAME","root");
	define("DB_PASSWORD","1q2w3e4r");
	define("DB_NAME_INTRANET","intranet");
	
	
	//define array of financial for payer
	$payerArray=array("5775","5659");
	$reserveStatus1=array("WA"=>"รอพิจารณาอนุมัติ","WR"=>"รอรับเงิน","WRE"=>"รอคืนเงิน","RA"=>"คืนเงินแล้ว","ORE"=>"เกินกำหนดคืนเงิน","EX"=>"รายการถูกยกเลิก");
	$reserveStatus2=array("WA"=>"รอพิจารณาอนุมัติ","WR"=>"รอจ่ายเงิน","WRE"=>"รอคืนเงิน","RA"=>"คืนเงินแล้ว","ORE"=>"เกินกำหนดคืนเงิน","EX"=>"รายการถูกยกเลิก");
	$btnStatus=array("WA"=>"default","WC"=>"primary","WR"=>"info","WRE"=>"warning","RA"=>"success","ORE"=>"danger","EX"=>"danger");
	$qoataLimit=array("ASTMGR"=>"20000","MGR"=>"200000");
	//Exclude Level 3 OC Level 1
	$excludeEmpArray=array("5636","5934","5944","5777","5672","5952");

	//Table
	
	define("TB_MULTIPURPOSE","multi_purpose");
	define("TB_CASHRESERVE","cash_reserve");
	define("TB_CASHRESERVERENEW","cash_reserver_renew");
	define("TB_EMPLOYEE","employee");  
	define("TB_DEPARTMENT","department");  
	define("TB_FACTION","faction");  
	define("TB_POSITION","position");  
	define("TB_VACATION","vacation");  
	define("TB_USER","user");
	define("TB_PARK","tb_park");

?> 