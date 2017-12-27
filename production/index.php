<?php
	@session_start();
    if( empty($_SESSION[spi_emp]) ){ header("location: ../../login.php"); }	
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	require_once("includes/function.php");
	//get Maximum reserve

	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	$sql = "SELECT user_level,mp_title as maximumCash FROM ".TB_USER.",".TB_MULTIPURPOSE."  WHERE emp_code = '".$_SESSION[spi_emp]."' AND mp_type =  'cash_reserve_lv' AND mp_id_key = user_level ";
	$result[user_level] = $db->select_query($sql);
	$array[user_level] = $db->fetch($result[user_level]);
	$maximumCash = $array[user_level][maximumCash];
	$db->closedb();
	$dateTodate = date('Y-m-d');
    $dateTodate = strtotime($dateTodate);
    $next3date = strtotime("+3 day", $dateTodate);
	//get holiday
	$arrayHoliday = array();
	$sql = "SELECT vcn_date FROM ".TB_VACATION."WHERE 1 ";
	$result[vacation] = $db->select_query($sql);
	while($array[vacation] = $db->fetch($result[vacation])) {
		array_push($arrayHoliday,$array[vacation][vnc_date]);
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
        <button type="button" class="btn btn-primary btn-ok" data-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for message dialog --> 
<!-- Modal for confirm reserveCash -->
<div class="modal fade " id="modal-confirm" role="dialog" aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> </button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-info"></i> </h4>
      </div>
      <div class="modal-body">
  ยืนยันการสำรองเงินจำนวนเงิน <strong><span id="lbAmount" style="color:#FF0004"></span></strong> บาท รับเงินวันที่ <span id="lbReceiveDate"> </span> ใช่หรือไม่ ? <br />
  * ใบสำรองเงินใบนี้จะหมดอายุในวันที่  <span id="lbExpireDate" style="color:#FF0004"> <?=calWorkingDayByDate(date('Y-m-d'),7,$arrayHoliday)?> </span> หากไม่มีการอนุมัติ
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-primary btnConfirm  pull-right" data-dismiss="modal"> <i class="fa fa-check-square " ></i>  ยืนยัน</button>
        <button type="button" class="btn btn-danger  pull-right" data-dismiss="modal"><i class="fa fa-times " ></i>  ยกเลิก</button>
      </div>
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
                <h2>ใบสำรองเงิน <small>*กรุณากรอกข้อมูลให้ครบถ้วน</small></h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content"> 
                
                <!-- start form for validation -->
                <form id="demo-form" action="#" method="post" enctype="multipart/form-data"  data-parsley-validate>
                  <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                  <label for="docReserveDate">วันที่สำรองเงิน * :</label>
                  <input class="date-picker form-control col-md-7 col-xs-12 " name="docReserveDate" id="docReserveDate" readonly  maxlength="10" required="required" type="text"  validateDate="true"  data-parsley-required-message="กรุณาใส่วันที่สำรองเงิน" value="<?=date('Y-m-d');?>" >
                  <div class="clearfix"></div>
                  <label for="docReceiveDate">วันที่รับเงิน * :</label>
                  <input class="date-picker form-control col-md-7 col-xs-12" name="docReceiveDate" id="docReceiveDate"  readonly maxlength="10"  required="required" type="text" validateDate="true"  data-parsley-required-message="กรุณาใส่วันที่รับเงิน" value="<?=calWorkingDayByDate(date('Y-m-d'),3,$arrayHoliday)?>"  data-parsley-workingday="" data-parsley-workingday-message="กรุณาเลือกวันทำการบริษัทฯ">
                  <div class="clearfix"></div>
                  <label for="docReserveType">วัตถุประสงค์ของการสำรองเงิน:</label>
                  <select id="docReserveType"  name="docReserveType" class="form-control" required  data-parsley-required-message="กรุณาเลือกวัตถุประสงค์ของการสำรองเงิน">
                    <option value="">เลือกวัตถุประสงค์ของการสำรองเงิน</option>
                    <?php 
						$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
						 $sql = "SELECT mp_id,mp_title FROM ".TB_MULTIPURPOSE."  WHERE mp_type = 'cash_reserve'  ";
						$result[reserveReason] = $db->select_query($sql);
						while($array[reserveReason] = $db->fetch($result[reserveReason])){
					?>
                    <option value="<?=$array[reserveReason][mp_id]?>">
                    <?=$array[reserveReason][mp_title]?>
                    </option>
                    <?php } 
				 	 $db->closedb();
				  ?>
                  </select>
                  <div class="clearfix"></div>
                  <label for="docReserveFor">รายละเอียด :</label>
                  <textarea id="docReserveFor"  name="docReserveFor"  required class="form-control"  data-parsley-trigger="keyup" data-parsley-minlength="10"  data-parsley-minlength-message="กรุณาใส่ข้อมูลอย่างน้อย 10 ตัวอักษร" data-parsley-validation-threshold="10" data-parsley-required-message="กรุณาใส่รายละเอียด"></textarea>
                  <br/>
                  <label for="docReserveAmount">จำนวนเงินที่สำรอง * :</label>
                  <input id="docReserveAmount"  name="docReserveAmount" class="form-control col-md-7 col-xs-12" required="required" type="text" data-parsley-type="number" data-parsley-max="200000" data-parsley-max-message="กรุณาใส่จำนวนเงินที่สำรองได้ไม่เกิน 200,000 บาท" data-parsley-required-message="กรุณาใส่จำนวนเงิน" data-parsley-type-message="กรุณาใส่ข้อมูลเป็นตัวเลข" min="100" maxlength="8" onKeyPress="CheckNum()">
                  <div class="clearfix"></div>
                  <br/>
                  <label for="docReserveAttach">แนบไฟล์ (กรุณาแนบไฟล์ขนาดไม่เกิน 2 Mb). :</label>
    			  <input type="file" name="docReserveAttach" id="docReserveAttach" data-parsley-max-file-size="2048" />
                  <div class="clearfix"></div>
                  <br/>
                  <button type="submit" class="btn btn-primary">บันทึก</button>
                </form>
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
<!-- Custom Function --> 
<script src="js/function.js"></script> 
<!-- Parsley --> 
<script>
  	  var arrayHoliday;
	  $(document).ready(function() {
		 //alert(getWorkingDays(begin, end)); // result = 12 days
		 $.post( "server_php/getHoliday.php", { 
			}).done(function( data ) {
				//console.log(data);
				arrayHoliday  = data.split(",");
		  });
		  
		 $('#docReceiveDate').daterangepicker({
			  singleDatePicker: true,
			  format: 'YYYY-MM-DD',
			  minDate: "<?=calWorkingDayByDate(date('Y-m-d'),3,$arrayHoliday)?>",
			  calender_style: "picker_4",
			}, function(start, end, label) {
			   //  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			   //change recieve date
        });
	

		 function noSunday(date){ 
			 return [date.getDay() != 0, ''];
		  }; 
		 
		 window.Parsley.addValidator('maxFileSize', {
		  validateString: function(_value, maxSize, parsleyInstance) {
			if (!window.FormData) {
			  alert('You are making all developpers in the world cringe. Upgrade your browser!');
			  return true;
			}
			var files = parsleyInstance.$element[0].files;
			return files.length != 1  || files[0].size <= maxSize * 1024;
		  },
		  requirementType: 'integer',
		  messages: {
			en: 'กรุณาแนบไฟล์ขนาดไม่เกิน %s Kb'
		  }
		});
		
		
		  window.Parsley.addValidator('workingday', {
				validateString: function(value) {
					var checkDate = new Date(value);
					if(checkDate.getDay() == 0 ||  arrayHoliday.indexOf(value) >= 0){  //check sunday
						return false;
					}else {
						return true;
					}
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
		
		//click for post
		 $(' .btnConfirm').on('click', function() {
          	//post a
			/*
			 //$.post( "server_php/saveReserveCash.php", { 
			 $.post( "server_php/testFiles.php", { 
			 	docReserveDate: $('#docReserveDate').val(), 
				docReceiveDate: $('#docReceiveDate').val() ,
				docReserveType: $('#docReserveType').val() ,
				docReserveFor: $('#docReserveFor').val() ,
				docReserveAmount: $('#docReserveAmount').val() ,
				docReserveAttach: $( '#docReserveAttach' )[0].files[0]
				})
			  .done(function( data ) {
					messageAlert(data);
					//click event reload
					$( ".btn-ok" ).click(function() {
						window.location = "reserveCashList.php";
					});
			  });*/
			   var fd = new FormData();    
                fd.append( 'docReserveAttach', $('#docReserveAttach')[0].files[0]);
				fd.append( 'docReserveDate', $('#docReserveDate').val());
				fd.append( 'docReceiveDate', $('#docReceiveDate').val());
				fd.append( 'docReserveType', $('#docReserveType').val());
				fd.append( 'docReserveFor', $('#docReserveFor').val());
				fd.append( 'docReserveAmount', $('#docReserveAmount').val());
				$.ajax({
					url:  "server_php/saveReserveCash.php",
					type: "POST",
					data:  fd,
					contentType: false,
					cache: false,
					processData:false,
					success: function(data){
						messageAlert(data);
						$( ".btn-ok" ).click(function() {
							window.location = "reserveCashList.php";
						});
					}           
				});
			
       	 }); 
      });


	$('#demo-form').parsley().on('field:validated', function() {
			var ok = $('.parsley-error').length === 0;
			$('.bs-callout-info').toggleClass('hidden', !ok);
			$('.bs-callout-warning').toggleClass('hidden', ok);
		  })
		  .on('form:submit', function() {
			  //check confirm
			  $('#lbAmount').html(addCommas($('#docReserveAmount').val()) );
			  $('#lbReceiveDate').html(  $('#docReceiveDate').val()  );
			  $('#modal-confirm').modal('show');
			  return false; 
			   //$('#modal-confirm').modal('show');
			 	// Don't submit form for this demo
	});
		  
		
	function CheckNum(){
		if (event.keyCode < 48 || event.keyCode > 57){
			  event.returnValue = false;
		}
	}
		
   	  function messageAlert(txt) {
		  $('#txtMessage').html(txt);  
		  $('#modal-messageDialog').modal('show');
	   }
	   
		function addCommas(nStr)
		{
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
</script> 
<style type="text/css">
	.inputfile {
		width: 0.1px;
		height: 0.1px;
		opacity: 0;
		overflow: hidden;
		position: absolute;
		z-index: -1;
	}
	
	.inputfile + label {
    font-size: 1.25em;
    font-weight: 700;
    color: white;
    background-color: black;
    display: inline-block;
}

.inputfile:focus + label,
.inputfile + label:hover {
    background-color: red;
}
</style>
</body>
</html>