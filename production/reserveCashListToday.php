<?php

	@session_start();
    if( empty($_SESSION[spi_emp]) ){ header("location: ../../login.php"); }	
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");
	$rows = 0;
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	$sqlreserveListGroupDay = "SELECT docReceiveDate FROM ".TB_CASHRESERVE.",".TB_EMPLOYEE.", ".TB_MULTIPURPOSE."  WHERE emp_code =  docEmpcode AND docReserveType = mp_id AND  mp_type = 'cash_reserve' AND docCancel = 'N' AND docStatus != 'RA' AND  docStatus != 'EX'";
	if(isset($_POST[docReserveDateStart]) || isset($_POST[docReserveDateEnd])) {
		$sqlreserveListGroupDay .= " AND docReserveDate >=  '".$_POST[docReserveDateStart]."'  AND docReserveDate <=  '".$_POST[docReserveDateEnd]."'  AND  docReserveFor    LIKE '%".$_POST[keyword]."%'     ";
			// $sqlreserveListGroupDay .= "  AND  docReceiveDate > adddate(now(),-3) ";
	}else {
		// $sqlreserveListGroupDay .= "  AND  docReceiveDate > adddate(now(),-3) ";
	} 
	if(($_POST[docReserveType])!="") {
		$sqlreserveListGroupDay .= "   AND  docReserveType     =  '".$_POST[docReserveType]."'  " ;
	}
	if(($_POST[docEmpCode])!="") {
		$sqlreserveListGroupDay .= "   AND  docEmpCode     =  '".$_POST[docEmpCode]."'  " ;
	}
	if(($_POST[docStatus])!="") {
		$sqlreserveListGroupDay .= "   AND  docStatus     =  '".$_POST[docStatus]."'  " ;
	}
	
	//check EmpList
	if($_SESSION["reserveLevel"]==1) { // normal staff
		$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE." WHERE emp_code = '".$_SESSION["EmpCode"]."' ";
	}else if($_SESSION["reserveLevel"]==2 AND !in_array($_SESSION["spi_emp"],$payerArray) ) {
		//get emp_code in same department
		$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE.", ".TB_POSITION.",  ".TB_DEPARTMENT." WHERE ".TB_EMPLOYEE.".emp_STresign = 'N' AND ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND  ".TB_DEPARTMENT.".dept_code='".$_SESSION["spi_dept"]."' and (".TB_EMPLOYEE.".park_id='".$_SESSION["spi_park"] ."')  and ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ' ORDER BY binary ".TB_EMPLOYEE.".emp_Fname  ";
	}else if($_SESSION["reserveLevel"]==4) {
		//get emp_code in same faction
		$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE.",  ".TB_POSITION.", ".TB_DEPARTMENT." WHERE ".TB_EMPLOYEE.".emp_STresign = 'N' AND ".TB_EMPLOYEE.".posi_code= ".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND  ".TB_DEPARTMENT.".fact_code='".$_SESSION["spi_fact"]."' and (".TB_EMPLOYEE.".park_id='".$_SESSION["spi_park"] ."') and ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ' ORDER BY binary ".TB_EMPLOYEE.".emp_Fname  ";
	}else { // Super user payer _level 5
		$sqlgetEmpCode = "SELECT * FROM ".TB_EMPLOYEE." WHERE ".TB_EMPLOYEE.".emp_STresign ='N' and  ".TB_EMPLOYEE.".posi_code!='27' and (".TB_EMPLOYEE.".park_id='".$_SESSION["spi_park"] ."') and ".TB_EMPLOYEE.".emp_Fname != 'ผู้ดูแลระบบ' ORDER BY binary ".TB_EMPLOYEE.".emp_Fname";
	}
	//get Result List empcode In Array
	$arrEmpCode=array();
	$result[empCodeList] = $db->select_query($sqlgetEmpCode);
	while($array[empCodeList] = $db->fetch($result[empCodeList])){
		array_push($arrEmpCode,$array[empCodeList][emp_code]);
	}
	$sqlreserveListGroupDay .= "AND docEmpCode IN (".join(",",$arrEmpCode).")  ";
	//just only Today
	$sqlreserveListGroupDay .= "AND docStatus = 'WR' AND docReceiveDate = '".date('Y-m-d')."'  ";
	$sqlreserveListGroupDay .= "GROUP BY  docReceiveDate ORDER  BY  docReceiveDate";
	 
	
	
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
<div class="modal fade modal-confirm" id="modal-approveReserve" role="dialog" aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> </button>
        <h4 class="modal-title" id="modalLabelTitle"><i class="fa fa-info"></i> อนุมัติรายการสำรองนี้ใช่หรือไม่</h4>
      </div>
        <div class="modal-body" id="modalLabelBody">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
              <div class="clearfix"></div>
              อนุมัติรายการสำรองนี้ใช่หรือไม่ </div>
          </div>
        </div>
        <div class="modal-footer"> 
        <a class="btn-primary btn pull-right btn-ok"  id="btnApprove" data-dismiss="modal"><i class="fa fa-check-square " ></i> อนุมัติ</a> 
        <a class="btn-danger btn  pull-right btn-cancel"  id="btnNotApprove" data-dismiss="modal"><i class="fa fa-times " ></i>  ไม่อนุมัติ</a> </div>
    </div>
  </div>
</div>
<!-- Modal for approve --> 
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
        
        <br />
        
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
                <h2>รายการจ่ายเงินวันนี้ </h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li> 
                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".modal-filter"><i class="fa fa-filter"></i> ตัวกรอง</button> --> 
                    </a>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="table-responsive">
                  <table class="table  ">
                    <thead>
                      <tr class="headings">
                        <th class="column-title">วันที่จ่ายเงิน </th>
                        <th class="column-title">แผนก </th>
                        <th class="column-title">ประเภท </th>
                        <th class="column-title">จำนวนเงิน (บาท) </th>
                        <th class="column-title">สถานะ </th>
                        <th class="column-title no-link last">&nbsp;</th>
                        </ tr>
                    </thead>
                    <tbody>
                      <?php 
							$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
							$result[reserveListGroupDay] = $db->select_query($sqlreserveListGroupDay);
							 $rows  = $db->rows($result[reserveListGroupDay]);
							while($array[reserveListGroupDay] = $db->fetch($result[reserveListGroupDay])){
					    ?>
                      <tr bgcolor="#DDD">
                        <td colspan="6" title="วันที่รับเงิน"><strong><i class="fa fa-calendar"></i>
                          <?=dateThai($array[reserveListGroupDay][docReceiveDate]);?>
                          </strong></td>
                      </tr>
                      <?
							$sqldocReceiveDate = "SELECT * FROM ".TB_CASHRESERVE.",".TB_EMPLOYEE.", ".TB_MULTIPURPOSE.", ".TB_POSITION.",  ".TB_DEPARTMENT."  WHERE   ".TB_EMPLOYEE.".posi_code=".TB_POSITION.".posi_code AND ".TB_POSITION.".dept_code= ".TB_DEPARTMENT.".dept_code AND emp_code =  docEmpcode AND docReserveType = mp_id AND  mp_type = 'cash_reserve'  AND  docReceiveDate =  '".$array[reserveListGroupDay][docReceiveDate]."'  AND docCancel = 'N'   AND docStatus != 'RA' AND  docStatus != 'EX'  ";
							if(isset($_POST[docReserveDateStart]) || isset($_POST[docReserveDateEnd])) {
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
							//just only Today
							$sqldocReceiveDate .= "AND docStatus = 'WR' AND docReceiveDate = '".date('Y-m-d')."'  ";
							 $sqldocReceiveDate .= " AND docEmpCode IN (".join(",",$arrEmpCode).")  ORDER  BY  docReceiveDate ";
							$result[reserveList] = $db->select_query($sqldocReceiveDate);
							//echo $rows = $db->rows($sqldocReceiveDate);
							while($array[reserveList] = $db->fetch($result[reserveList])){
								$rows++;
						 ?>
                      <tr class="docReserveTran">
                        <td title="แสดงรายละเอียด" class="reserveList" data-id="<?=$array[reserveList][docNo]?>"><span style="font-weight:bold; cursor:pointer;"> คุณ<?=$array[reserveList][emp_Fname]?>
                          <?=$array[reserveList][emp_Lname]?>
                          </span></td>
                        <td><?=$array[reserveList][dept_name]?></td>
                        <td><?=$array[reserveList][mp_title]?></td>
                        <td>฿
                          <?=number_format($array[reserveList][docReserveAmount],2);?></td>
                        <td><button type="button" class="btn btn-<?=$btnStatus[$array[reserveList][docStatus]]?> btn-xs">
                          <?php  
						  		if(!in_array($_SESSION["spi_emp"],$payerArray) ) {
							  	echo $reserveStatus1[$array[reserveList][docStatus]]; 
							  }
							  else { 
							  	echo  $reserveStatus2[$array[reserveList][docStatus]]; 
							  }  ?>
                          </button></td>
                        <?php  
						   //check in dept and in faction 
						 if($array[reserveList][docStatus] == "WR" AND in_array($_SESSION["spi_emp"],$payerArray)) {   ?>
                        <td><i class="fa fa-money payConfirm btnTools"  title="จ่ายเงิน" data-id="<?=$array[reserveList][docNo];?>"></i></td>
                        <? } ?>
                      </tr>
                      <? } 
					  
					   }    $db->closedb(); ?>
                      <?php if($rows==0) { ?>
                      <tr >
                        <td colspan="6" title=""><div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> </button>
                            <strong><i class="fa fa-info"></i> ไม่มีรายการจ่ายเงินวันนี้</strong> </div></td>
                      </tr>
                      <?
						  } ?>
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
<!-- jQuery autocomplete --> 
<script src="../vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script> 
<!-- starrr --> 
<script src="../vendors/starrr/dist/starrr.js"></script> 
<!-- Custom Theme Scripts --> 
<script src="../build/js/custom.min.js"></script>
<style>
	.btnTools {
		cursor:pointer;
	}
</style>
<!-- Parsley --> 
<script>
      $(document).ready(function() {
		  
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
		
 		$( ".payConfirm" ).click(function() {
			  var docNo =   $(this).attr("data-id");
			  
			 $( "#modalLabelTitle" ).html('<i class="fa fa-exclamation-circle " ></i> ยืนยันการจ่ายเงินใช่หรือไม่ ?');
			$( "#modalLabelBody" ).html("ยืนยันการจ่ายเงินใช่หรือไม่ ?");
			$( ".btn-ok" ).html('<i class="fa fa-check-square " ></i> ตกลง');
			$( ".btn-cancel" ).html('<i class="fa fa-times " ></i> ยกเลิก');
			$( ".modal-confirm" ).modal("show"); 
			$('.btn-ok').click(function() {
				
				 $.post( "server_php/savePaymoney.php", { docNo: docNo })
				  .done(function( data ) {
						messageAlert(data);
						//click event reload
						$( "#btnDialogOK" ).click(function() {
							location.reload();
						});				  
					});
				   //check approve limit 20k 50k 200k
			});
		});
      });
	  
	   function messageAlert(txt) {
		  $('#txtMessage').html(txt);  
		  $('#modal-messageDialog').modal('show');
	   }
	   
    </script>
</body>
</html>