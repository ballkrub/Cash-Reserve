<?php

	@session_start();
    if( empty($_SESSION[spi_emp]) ){ header("location: ../../login.php"); }	
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");

	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	$sql = "SELECT mp_id,mp_title FROM ".TB_MULTIPURPOSE."  WHERE mp_type = 'cash_reserve'  ";
	$result[reserveReason] = $db->select_query($sql);
	
	//select type in database
	$arrayType = array();
	 $sql = "SELECT DISTINCT(docReserveType) FROM ".TB_CASHRESERVE."  WHERE  1  ";
	$result[getDocType] = $db->select_query($sql);
	while($array[getDocType] = $db->fetch($result[getDocType])){
		array_push($arrayType,$array[getDocType][docReserveType]);
	}
	
	
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
<div class="modal fade modal-addReserveType" id="modal-addReserveType" role="dialog" aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> </button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus-circle"></i> เพิ่มประเภทรายการสำรองเงิน</h4>
      </div>
      <form action="saveReserveType.php" id="demo-form" method="post" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="clearfix"></div>
       <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
              <label for="fullname">ชื่อประเภท * :</label>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 form-group ">
              <input type="text" class="form-control "  id="mp_title"  name="mp_title"  data-parsley-required-message="กรุณาใส่ชื่อประเภท" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" >บันทึก</button>
      </div>
      </form>
    </div>
  </div>
</div>
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
                <h2>ประเภทรายการสำรองเงิน </h2>
                <ul class="nav navbar-right panel_toolbox">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".modal-addReserveType"><i class="fa fa-plus-circle"></i> เพิ่มรายการ</button>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="table-responsive">
                   <table class="table table-striped">
                    <thead>
                      <tr class="headings">
                        <th class="column-title">ชื่อประเภทสำรองเงิน </th>
                        <th class="column-title no-link last">&nbsp;</th>
                        </ tr>
                    </thead>
                    <tbody>
                      <?php 
							while($array[reserveReason] = $db->fetch($result[reserveReason])){
						 ?>
                      <tr class="docReserveTran">
                        <td title="แสดงรายละเอียด" ><span style="cursor:pointer;">
                          <?=$array[reserveReason][mp_title]?>
                          </span></td>
                        <td align="right"> 
                        <?php if(!in_array($array[reserveReason][mp_id],$arrayType)) { ?>
                         <button type="button" class="btn btn-warning btn-xs btn-delete" data-id="<?=$array[reserveReason][mp_id];?>" title="ลบประเภทการสำรองเงิน"> <i class="fa fa-trash"></i> </button>
                        <? } ?> 
                       </td>
                      </tr>
                      <? }    $db->closedb(); ?>
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
<script>
  $(document).ready(function() {
		$( ".btn-delete" ).click(function() {
			var r = confirm("ยืนยันการลบประเภทใบสำรองเงินใช่หรือไม่ ?");
			if (r == true) {
				 var mp_id =   $(this).attr("data-id");
				 $.post( "server_php/deleteReserveType.php", { mp_id: mp_id })
				  .done(function( data ) {
					alert( "Data Loaded: " + data );
					location.reload();
				  });
				   //check approve limit 20k 50k 200k
			} else {
				
			}
		});
		
		
	 $.listen('parsley:field:validate', function() {
          validateFront();
        });
		
        $('#demo-form .btn').on('click', function() {
          $('#demo-form').parsley().validate();
          validateFront();
        });
        var validateFront = function() {
          if (true === $('#demo-form').parsley().isValid()) {
            $('.bs-callout-info').removeClass('hidden');
            $('.bs-callout-warning').addClass('hidden');
          } else {	
            $('.bs-callout-info').addClass('hidden');
            $('.bs-callout-warning').removeClass('hidden');
          }
        };

  });
</script>
</body>
</html>