<?php
	@session_start();
    if( empty($_SESSION[spi_emp]) || empty($_SESSION["sess_username"]) ){ header("location: ../../login.php"); }	
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");	
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	$reserveListGroupDay = "SELECT docReceiveDate FROM ".TB_CASHRESERVE.",".TB_EMPLOYEE.", ".TB_MULTIPURPOSE."  WHERE emp_code =  docEmpcode AND docReserveType = mp_id AND  mp_type = 'cash_reserve'  AND  docStatus = 'EX' ";
	if(isset($_POST[docReceiveDateStart]) || isset($_POST[docReceiveDateEnd])) {
		$reserveListGroupDay .= " AND docReceiveDate >=  '".$_POST[docReceiveDateStart]."'  AND docReceiveDate <=  '".$_POST[docReceiveDateEnd]."' AND  docReserveFor    LIKE '%".$_POST[keyword]."%'     ";
		// $reserveListGroupDay .= "  AND  docReceiveDate > adddate(now(),-10) ";
	}else {
		/// $reserveListGroupDay .= "  AND  docReceiveDate > adddate(now(),-10) ";
	} 
	if(($_POST[docReserveType])!="") {
		$reserveListGroupDay .= "   AND  docReserveType     =  '".$_POST[docReserveType]."'  " ;
	}
	if(($_POST[docEmpCode])!="") {
		$reserveListGroupDay .= "   AND  docEmpCode     =  '".$_POST[docEmpCode]."'  " ;
	}
	if(($_POST[docStatus])!="") {
		$reserveListGroupDay .= "   AND  docStatus     =  '".$_POST[docStatus]."'  " ;
	}
	
	//check EmpList
	if($_SESSION["sess_username"]!="admin" && $_SESSION["reserveLevel"]!="" && !empty($_SESSION["EmpCode"])) {
		if($_SESSION["reserveLevel"]==1) { // normal staff
			$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE." WHERE emp_code = '".$_SESSION["EmpCode"]."' ";
		}else if($_SESSION["reserveLevel"]==2 AND !in_array($_SESSION["spi_emp"],$payerArray) ) {
			//get emp_code in same department
			$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE.", ".TB_POSITION.",  ".TB_DEPARTMENT." WHERE ".TB_EMPLOYEE.".emp_STresign = 'N' AND ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND  ".TB_DEPARTMENT.".dept_code='".$_SESSION["spi_dept"]."' and (".TB_EMPLOYEE.".park_id='".$_SESSION["spi_park"] ."')  and ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ'  AND   ".TB_EMPLOYEE.".posi_code!='27' ORDER BY binary ".TB_EMPLOYEE.".emp_Fname  ";
		}else if($_SESSION["reserveLevel"]==3 AND !in_array($_SESSION["spi_emp"],$payerArray) ) {
			//get emp_code in same department
			$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE.", ".TB_POSITION.",  ".TB_DEPARTMENT." WHERE ".TB_EMPLOYEE.".emp_STresign = 'N' AND ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND  ".TB_DEPARTMENT.".dept_code='".$_SESSION["spi_dept"]."' and (".TB_EMPLOYEE.".park_id='".$_SESSION["spi_park"] ."')  and ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ' AND   ".TB_EMPLOYEE.".posi_code!='27' ORDER BY binary ".TB_EMPLOYEE.".emp_Fname  ";
		}else if($_SESSION["reserveLevel"]==4) {
			//get emp_code in same faction
			 $sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE.",  ".TB_POSITION.", ".TB_DEPARTMENT." WHERE ".TB_EMPLOYEE.".emp_STresign = 'N' AND ".TB_EMPLOYEE.".posi_code= ".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND  ".TB_DEPARTMENT.".fact_code='".$_SESSION["spi_fact"]."'  and ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ'  AND   ".TB_EMPLOYEE.".posi_code!='27' ORDER BY binary ".TB_EMPLOYEE.".emp_Fname  ";
		}else { // Super user payer _level 5
			$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE." WHERE ".TB_EMPLOYEE.".emp_STresign ='N' and (".TB_EMPLOYEE.".park_id='".$_SESSION["spi_park"] ."')  and  ".TB_EMPLOYEE.".posi_code!='27'  AND ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ' ORDER BY  ".TB_EMPLOYEE.".emp_Fname";
		}
	}else {
		$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE." WHERE ".TB_EMPLOYEE.".emp_STresign ='N' and  ".TB_EMPLOYEE.".posi_code!='27' and (".TB_EMPLOYEE.".park_id !='4') and  ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ'    ORDER BY  ".TB_EMPLOYEE.".emp_Fname";
	}

	//get Result List empcode In Array
	$arrEmpCode=array();
	$result[empCodeList] = $db->select_query($sqlgetEmpCode);
	while($array[empCodeList] = $db->fetch($result[empCodeList])){
		array_push($arrEmpCode,$array[empCodeList][emp_code]);
	}
	$reserveListGroupDay .= "AND docEmpCode IN (".join(",",$arrEmpCode).") ";
	 $reserveListGroupDay .= "GROUP BY  docReceiveDate ORDER  BY  docReceiveDate";
	 
	 //MAXIMUM APPROVAL
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	 $sql = "SELECT user_level,mp_title as maximumCash FROM ".TB_USER.",".TB_MULTIPURPOSE."  WHERE emp_code = '".$_SESSION[spi_emp]."' AND mp_type =  'cash_reserve_lv' AND mp_id_key = user_level ";
	$result[user_level] = $db->select_query($sql);
	$array[user_level] = $db->fetch($result[user_level]);
	$maximumCash = $array[user_level][maximumCash];
	$db->closedb();
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>intranet.spi.co.th center of data | บริษัท สหพัฒนาอินเตอร์โฮลดิ้ง จำกัด (มหาชน)</title>

<!-- Bootstrap -->
<link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<!-- iCheck -->
<link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
<!-- bootstrap-wysiwyg -->
<link href="../vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
<!-- Select2 -->
<link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<!-- Switchery -->
<link href="../vendors/switchery/dist/switchery.min.css" rel="stylesheet">
<!-- starrr -->
<link href="../vendors/starrr/dist/starrr.css" rel="stylesheet">
<!-- Custom Theme Style -->
<link href="../build/css/custom.min.css" rel="stylesheet">
<!-- Font Style -->
<link href="css/font.css" rel="stylesheet">
<!-- start: Favicon -->
<link rel="shortcut icon" href="favicon.ico">
</head>
<body class="nav-md">
<!-- start: approve model -->

<!-- Modal for approve --> 
<!-- start: renew model -->

<!-- Modal for renew --> 
<!-- Modal for  message dialog -->
<div class="modal fade " id="modal-messageDialog" role="dialog" aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> </button>
        <h4 class="modal-title" ><i class="fa fa-info"></i> กล่องโต้ตอบข้อความ</h4>
      </div>
      <div class="modal-body" id="txtMessage">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnDialogOK" data-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for message dialog --> 
<!-- Modal for info -->
<div class="modal fade " id="modal-reserverDetails" role="dialog" aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> </button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-info"></i> รายละเอียดการสำรองเงิน</h4>
      </div>
      <div class="modal-body">
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12 form-group " id="reserveDetail"> </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for info --> 
<!-- Modal for filter -->
<div class="modal fade modal-filter" role="dialog" aria-hidden="true" data-backdrop="false" >
  <form id="demo-form" data-parsley-validate action="reserveCashExpiredList.php" method="post" enctype="multipart/form-data">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> </button>
          <h4 class="modal-title" id="myModalLabel"><i class="fa fa-filter"></i> ตัวเลือกการกรอง</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
              <label for="fullname" >วันที่รับเงิน * :</label>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 form-group ">
              <input type="text" class="form-control date-picker"  id="docReceiveDateStart"  name="docReceiveDateStart"  readonly placeholder="เริ่มวันที่" validateDate="true"  data-parsley-required-message="กรุณาใส่วันที่สำรองเงิน" value="<?php if(isset($_POST[docReceiveDateStart])) echo $_POST[docReceiveDateStart]; else echo date('Y-01-01');  ?>">
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 form-group ">
              <input type="text" class="form-control date-picker " id="docReceiveDateEnd"  name="docReceiveDateEnd"   readonly placeholder="ถึงวันที่" validateDate="true"  data-parsley-required-message="กรุณาใส่วันที่สำรองเงิน" value="<?php if(isset($_POST[docReceiveDateEnd])) echo $_POST[docReceiveDateEnd]; else echo date('Y-12-31');  ?>">
            </div>
          </div>
          <div class="clearfix"></div>
          <label for="heard">ผู้สำรองเงิน:</label>
          <select id="docEmpCode"  name="docEmpCode" class="form-control">
            <option value="">ทั้งหมด</option>
            <?php 
			 		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
					$result[empCodeList] = $db->select_query($sqlgetEmpCode);
					while($array[empCodeList] = $db->fetch($result[empCodeList])){
				?>
            <option value="<?=$array[empCodeList][emp_code]?>" <?php if($array[empCodeList][emp_code]==$_POST[docEmpCode]) echo 'selected'; ?>>
            <?=$array[empCodeList][emp_Fname]?>
            <?=$array[empCodeList][emp_Lname]?>
            </option>
            <?php } 
				 	 $db->closedb();
			?>
          </select>
          <div class="clearfix"></div>
          <label for="heard">วัตถุประสงค์ของการสำรองเงิน:</label>
          <select id="docReserveType"  name="docReserveType" class="form-control" >
            <option value="">ทั้งหมด</option>
            <?php 
					$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
					$sql = "SELECT mp_id,mp_title FROM ".TB_MULTIPURPOSE."  WHERE mp_type = 'cash_reserve'  ";
					$result[reserveReason] = $db->select_query($sql);
					while($array[reserveReason] = $db->fetch($result[reserveReason])){
				?>
            <option value="<?=$array[reserveReason][mp_id]?>" <?php if($array[reserveReason][mp_id]==$_POST[docReserveType]) echo 'selected'; ?>>
            <?=$array[reserveReason][mp_title]?>
            </option>
            <?php } 
				 	 $db->closedb();
			?>
          </select>

          
          <div class="clearfix"></div>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
              <label for="docKeyword" >คำค้นหา :</label>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
              <input type="text" class="form-control  " id="docKeyword"  name="docKeyword"   placeholder="คำค้นหา" validateDate="true"  data-parsley-required-message="กรุณาใส่คำค้นหา" value="<?=$_POST[docKeyword];?>" >
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >ค้นหา</button>
        </div>
      </div>
    </div>
  </form>
</div>
<!-- Modal for filter -->
<div class="container body">
  <div class="main_container">
    <div class="col-md-3 left_col">
      <div class="left_col scroll-view">
        <div class="navbar nav_title" > <a href="index.php" class="site_title"><img src="images/logo.png" alt="logo" width="34" style="margin-left:7px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>intranet.spi.co.th</span></a> </div>
        <div class="clearfix"></div>
        <!-- menu profile quick info -->
        <div class="profile">
          <div class="profile_pic"> <img src="images/user.png" alt="..." class="img-circle profile_img"> </div>
          <div class="profile_info" > <span>ยินดีต้อนรับ,</span>
            <h2>
              <?=$_SESSION[spi_Fname];?>
              <?=$_SESSION[spi_Lname];?>
            </h2>
          </div>
        </div>
        <!-- /menu profile quick info --> 
        
        <br />  <br />
        
        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
          <div class="menu_section">
            <h3>&nbsp;</h3>
            <?php require_once("includes/menu.php"); ?>
          </div>
        </div>
        <!-- /sidebar menu --> 
        
      </div>
    </div>
    
    <!-- top navigation -->
    <div class="top_nav">
      <div class="nav_menu">
        <nav class="" role="navigation">
          <div class="nav toggle"> <a id="menu_toggle"><i class="fa fa-bars"></i></a> </div>
          <ul class="nav navbar-nav navbar-right" >
            <li class=""> <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 
              <?=$_SESSION[spi_Fname];?>
              <?=$_SESSION[spi_Lname];?>
              <span class=" fa fa-angle-down"></span> </a>
              <ul class="dropdown-menu dropdown-usermenu pull-right">
                <li><a href="../../login.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <!-- /top navigation --> 
    
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>รายการใบสำรองเงินหมดอายุ </h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".modal-filter"><i class="fa fa-filter"></i> ตัวกรอง</button>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="table-responsive">
                  <table class="table  ">
                    <thead>
                      <tr class="headings">
                        <th class="column-title">&nbsp; </th>
                        <th class="column-title">วันที่รับเงิน </th>
                        <th class="column-title hdRefund">กำหนดวันที่คืนเงิน </th>
                        <th class="column-title hdType">ประเภท </th>
                        <th class="column-title">จำนวนเงิน (บาท) </th>
                        <th class="column-title hdStatus">สถานะ </th>
                        <th class="column-title no-link last">&nbsp;</th>
                        </ tr>
                    </thead>
                    <tbody>
                      <?php 
							$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
							$result[reserveListGroupDay] = $db->select_query($reserveListGroupDay);
							while($array[reserveListGroupDay] = $db->fetch($result[reserveListGroupDay])){
						 ?>
                      <tr bgcolor="#DDD">
                        <td colspan="7" title="วันที่รับเงิน"><strong><i class="fa fa-calendar"></i>
                          <?=dateThai($array[reserveListGroupDay][docReceiveDate]);?>
                          </strong></td>
                      </tr>
                      <?
							$sqldocReceiveDate = "SELECT * FROM ".TB_CASHRESERVE.",".TB_EMPLOYEE.", ".TB_MULTIPURPOSE.", ".TB_POSITION.",  ".TB_DEPARTMENT."  WHERE   ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND emp_code =  docEmpcode AND docReserveType = mp_id AND  mp_type = 'cash_reserve'  AND  docReceiveDate =  '".$array[reserveListGroupDay][docReceiveDate]."'    AND docStatus = 'EX'  ";
							if(isset($_POST[docReceiveDateStart]) || isset($_POST[docReceiveDateEnd])) {
									$sqldocReceiveDate .= " AND docReceiveDate >=  '".$_POST[docReceiveDateStart]."'  AND docReceiveDate <=  '".$_POST[docReceiveDateEnd]."'  ";
							}
							if(($_POST[docReserveType])!="") {
								$sqldocReceiveDate .= "   AND  docReserveType     =  '".$_POST[docReserveType]."'  " ;
							}
							if(($_POST[docEmpCode])!="") {
								$sqldocReceiveDate .= "   AND  docEmpCode     =  '".$_POST[docEmpCode]."'  " ;
							}
							if(($_POST[docStatus])!="") {
								$sqldocReceiveDate .= "   AND  docStatus     =  '".$_POST[docStatus]."'  " ;
							}
							$sqldocReceiveDate .= " AND docEmpCode IN (".join(",",$arrEmpCode).")  ORDER  BY  docReceiveDate ";
							$result[reserveList] = $db->select_query($sqldocReceiveDate);
							while($array[reserveList] = $db->fetch($result[reserveList])){
						 ?>
                      <tr class="docReserveTran">
                        <td><?php if($array[reserveList][docAttach]!=null) { ?> <a href="viewAttach.php?docNo=<?=$array[reserveList][docNo]?>" target="_blank"><i class="fa fa-paperclip" title="ดูไฟล์แนบ"></i> </a> <?  } ?> </td>
                        <td title="แสดงรายละเอียด" class="reserveList" data-id="<?=$array[reserveList][docNo]?>"><span style="cursor:pointer;"> คุณ<?=$array[reserveList][emp_Fname]?>
                          <?=$array[reserveList][emp_Lname]?>
                          </span></td>
                        <td class="dtRefund"><span <?php $dateToday = date("Y-m-d");
							if($array[reserveList][docRefundDate] <$dateToday) echo 'style="color:#F00;"'; ?> >
                          <?php 
					  		if($array[reserveList][docStatus]=="WRE" || $array[reserveList][docStatus]=="ORE") echo dateThai($array[reserveList][docRefundDate]); else echo '-';
						 ?>
                          </span>
                          <?php if($array[reserveList][docRenewTimes] != 0 ) echo '<br /><i class="fa fa-warning"></i> <span style="color:#F00;">(ต่ออายุครั้งที่ '.$array[reserveList][docRenewTimes].')</span>'; ?></td>
                        <td class="dtType"><?=$array[reserveList][mp_title]?></td>
                        <td>฿
                          <?=number_format($array[reserveList][docReserveAmount],2);?></td>
                        <td class="dtStatus"><span  class="btn btn-<?=$btnStatus[$array[reserveList][docStatus]]?> btn-xs" style="cursor:default;">
                          <?php  
						  		if(!in_array($_SESSION["spi_emp"],$payerArray) ) {
							  	echo $reserveStatus1[$array[reserveList][docStatus]]; 
							  }
							  else { 
							  	echo  $reserveStatus2[$array[reserveList][docStatus]]; 
							  }  ?>
                          </span></td>
                        <?php  
						   //check in dept and in faction and limit 0-20000 asst. AND > mgr. 20000 - 200000
							if($array[reserveList][docStatus] == "WA" AND $_SESSION["reserveLevel"]>=2 AND ($array[reserveList][dept_code]==$_SESSION[dept_code] OR ($array[reserveList][fact_code]==$_SESSION[spi_fact] AND $_SESSION["reserveLevel"]==4 )) AND $array[reserveList][docReserveAmount] <= $maximumCash) {    ?>
                        <td><i class="fa fa-check-square approveConfirm btnTools"  title="พิจารณาอนุมัติใบสำรองเงิน" data-id="<?=$array[reserveList][docNo];?>" ></i></td>
                        <?  } else if($array[reserveList][docStatus] == "WR" AND in_array($_SESSION["spi_emp"],$payerArray) AND $array[reserveList][docReceiveDate] <= $dateToday  ) {   ?>
                        <td><i class="fa fa-money payConfirm btnTools"  title="จ่ายเงิน" data-id="<?=$array[reserveList][docNo];?>"></i></td>
                        <? } else if($array[reserveList][docStatus] == "WRE" AND in_array($_SESSION["spi_emp"],$payerArray)) {   ?>
                        <td><i class="fa fa-share-square refundConfirm btnTools"  title="คืนเงิน" data-id="<?=$array[reserveList][docNo];?>"></i></td>
                        <? } else if($array[reserveList][docStatus] == "RA" ) {   ?>
                        <td><i class="fa fa-check  btnTools"  title="คืนเงินแล้ว" ></i></td>
                        <? } else if($array[reserveList][docStatus] == "ORE" AND  $array[reserveList][docEmpcode]==$_SESSION["spi_emp"] AND $array[reserveList][docRenewTimes] <=3 ) {   ?>
                        <td><i class="fa fa-refresh  renewConfirm btnTools"  title="ขยายเวลาคืนเงิน"  data-id="<?=$array[reserveList][docNo];?>" ></i></td>
                        <? } else if(($array[reserveList][docStatus] == "WA" || $array[reserveList][docStatus] == "WR") AND  $array[reserveList][docEmpcode]==$_SESSION["spi_emp"] AND $array[reserveList][docReceiveDate] > $dateToday  ) { ?>
                        <td><i class="fa fa-times-circle cancelConfirm btnTools"  title="ยกเลิกใบสำรองเงิน" data-id="<?=$array[reserveList][docNo];?>" ></i></td>
                        <? } else { ?>
                        <td>&nbsp;</td>
                        <? } ?>
                      </tr>
                      <? }  
					  }    $db->closedb(); ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- end form for validations --> 
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>
<!-- /page content --> 

<!-- footer content -->
<footer>
  <div class="pull-right"> บริษัท สหพัฒนาอินเตอร์โฮลดิ้ง จำกัด (มหาชน) <br/>
    © 2017 Saha Pathana Inter Holding PCL. All Rights Reserved. Powerby IT Department Sriracha </div>
  <div class="clearfix"></div>
</footer>
<!-- /footer content -->
</div>
</div>

<!-- jQuery --> 
<script src="../vendors/jquery/dist/jquery.min.js"></script> 
<!-- Bootstrap --> 
<script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script> 
<!-- FastClick --> 
<script src="../vendors/fastclick/lib/fastclick.js"></script> 
<!-- NProgress --> 
<script src="../vendors/nprogress/nprogress.js"></script> 
<!-- bootstrap-progressbar --> 
<script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script> 
<!-- iCheck --> 
<script src="../vendors/iCheck/icheck.min.js"></script> 
<!-- bootstrap-daterangepicker --> 
<script src="js/moment/moment.min.js"></script> 
<script src="js/datepicker/daterangepicker.js"></script> 
<!-- bootstrap-wysiwyg --> 
<script src="../vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script> 
<script src="../vendors/jquery.hotkeys/jquery.hotkeys.js"></script> 
<script src="../vendors/google-code-prettify/src/prettify.js"></script> 
<!-- jQuery Tags Input --> 
<script src="../vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script> 
<!-- Switchery --> 
<script src="../vendors/switchery/dist/switchery.min.js"></script> 
<!-- Select2 --> 
<script src="../vendors/select2/dist/js/select2.full.min.js"></script> 
<!-- Parsley --> 
<script src="../vendors/parsleyjs/dist/parsley.min.js"></script> 
<!-- Autosize --> 
<script src="../vendors/autosize/dist/autosize.min.js"></script> 
<!-- jQuery autocomplete --> 
<script src="../vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script> 
<!-- starrr --> 
<script src="../vendors/starrr/dist/starrr.js"></script> 
<!-- Custom Theme Scripts --> 
<script src="../build/js/custom.min.js"></script>
<style>
	.docReserveTran: hover {
		background-color: #B9B9B9;
	}
	.btnTools {
		cursor:pointer;
	}
	
	
@media screen and (max-width: 480px){
	.hdType, .dtType, .hdRefund , .dtRefund , .hdStatus , .dtStatus {
		display: none;
	}
}

@media screen and (max-width: 720px){
	.hdType, .dtType, .hdStatus , .dtStatus {
		display: none;
	}
}

@media screen and (max-width: 960px){
	.hdType, .dtType {
		display: none;
	}
}
	
</style>
<!-- Parsley --> 
<script>
      $(document).ready(function() {  
		 $('.date-picker').daterangepicker({
			  singleDatePicker: true,
			  format: 'YYYY-MM-DD',
			  calender_style: "picker_4",
			}, function(start, end, label) {
			     console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			   //change recieve date
        });
		
		$( ".reserveList" ).click(function() {
			 var docNo =   $(this).attr("data-id");
			  $.post( "server_php/reserveDetails.php", { docNo: docNo })
				  .done(function( data ) {
					$( "#reserveDetail" ).html(data);
					//show modal()
					$( "#modal-reserverDetails" ).modal('show');
					//alert( "Data Loaded: " + data );
			 });
		});
		
		
		
    	
		
	
      });
	  
	  function messageAlert(txt) {
		  $('#txtMessage').html(txt);  
		  $('#modal-messageDialog').modal('show');
	   }
	  
</script> 
<!-- /Parsley -->
</body>
</html>