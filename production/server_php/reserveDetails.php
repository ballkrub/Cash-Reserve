<?php
	require_once("../includes/config.in.php");
	require_once("../includes/class.mysql.php");	
	require_once("../includes/function.php");
	$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
	$sql = "SELECT * FROM ".TB_CASHRESERVE.",".TB_EMPLOYEE.", ".TB_MULTIPURPOSE."  WHERE emp_code =  docEmpcode AND docReserveType = mp_id AND  mp_type = 'cash_reserve'  AND  docNo =  '".$_POST[docNo]."' ";
	$result[reserveDetails] = $db->select_query($sql);
	$array[reserveDetails] = $db->fetch($result[reserveDetails]);
	

?>
<table class="table table-striped">
  <tbody>
    <tr>
      <td>วันที่สำรองเงิน</td>
      <td><?=dateThai($array[reserveDetails][docReserveDate]);?></td>
    </tr>
    <tr>
      <td>วันที่รับเงิน</td>
      <td><?=dateThai($array[reserveDetails][docReceiveDate]);?></td>
    </tr>
    <tr>
      <td>ผู้สำรองเงิน</td>
      <td><?=$array[reserveDetails][emp_Fname]?> <?=$array[reserveDetails][emp_Lname]?></td>
    </tr>
    <tr>
      <td>ประเภทของการสำรองเงิน</td>
      <td><?=$array[reserveDetails][mp_title]?></td>
    </tr>
    <tr>
      <td>วัตถุประสงค์ของการสำรองเงิน</td>
      <td><?=$array[reserveDetails][docReserveFor]?></td>
    </tr>
    <tr>
      <td>จำนวนเงิน (บาท)</td>
      <td><?=number_format($array[reserveDetails][docReserveAmount],2);?></td>
    </tr>
    <tr>
      <td>สถานะ</td>
      <td><button type="button" class="btn btn-<?=$btnStatus[$array[reserveDetails][docStatus]]?> btn-xs">
	   <?php  
			if(!in_array($_SESSION["spi_emp"],$payerArray) ) {
			echo $reserveStatus1[$array[reserveDetails][docStatus]]; 
		  }
		  else { 
			echo  $reserveStatus2[$array[reserveDetails][docStatus]]; 
		  }  ?>
      </button></td>
    </tr>
    <?php if($array[reserveDetails][docStatus]=="EX") {   ?> 
     <tr>
      <td>สาเหตุของของการยกเลิก</td>
      <td>
	  	<?php 	
				if($array[reserveDetails][docCancel]=='Y') {
					if($array[reserveDetails][docApprovalEmpcode]!=0) {
						echo 'รายการนี้ไม่ได้รับการอนุมัติจากผู้บังคับบัญชา';
					}else {
						echo 'รายการนี้ถูกยกเลิกเองโดยผู้ทำรายการ';
					}
				}else {
					echo 'ไม่มารับเงินภายในวันที่รับเงินตามกำหนดไว้ในรายการ';
				}
	  ?></td>
    </tr>
    <? } ?>
    <?php if($array[reserveDetails][docStatus]=="WR" || $array[reserveDetails][docStatus]=="WRE"  || $array[reserveDetails][docStatus]=="RA"  || $array[reserveDetails][docStatus]=="ORE" ) {   ?> 
	 <tr>
      <td>ผู้อนุมัติ</td>
      <td>คุณ<?php 
	  	 $sql = "SELECT * FROM ".TB_EMPLOYEE."   WHERE emp_code =  '".$array[reserveDetails][docApprovalEmpcode]."' ";
		$result[empName] = $db->select_query($sql);
		$array[empName] = $db->fetch($result[empName]);
		echo $array[empName][emp_Fname]." ".$array[empName][emp_Lname]; ?></td>
    </tr>
     <tr>
      <td>อนุมัติเวลา</td>
      <td><?=dateThai($array[reserveDetails][docApproveTime]);?></td>
    </tr>
	<?   } ?>
    <?php if($array[reserveDetails][docStatus]=="WRE" || $array[reserveDetails][docStatus]=="RA"   || $array[reserveDetails][docStatus]=="ORE") {   ?> 
	 <tr>
      <td>ผู้จ่ายเงิน</td>
      <td>คุณ<?php 
	  	 $sql = "SELECT * FROM ".TB_EMPLOYEE."   WHERE emp_code =  '".$array[reserveDetails][docCheckerEmpcode]."' ";
		$result[empName] = $db->select_query($sql);
		$array[empName] = $db->fetch($result[empName]);
		echo $array[empName][emp_Fname]." ".$array[empName][emp_Lname]; ?></td>
    </tr>
     <tr>
      <td>เวลารับเงิน</td>
      <td><?=dateThai($array[reserveDetails][docCheckedTime]);?></td>
    </tr>
     <tr>
      <td>กำหนดเวลาคืนเงิน</td>
      <td>
	  <?=dateThai($array[reserveDetails][docRefundDate]);?>
      </td>
    </tr>
    <?php if($array[reserveDetails][docRenew]=="Y") {   ?> 
     <tr>
      <td><i class="fa fa-warning"></i><span style="color:#F00;"> ต่ออายุกำหนดคืนเงิน</span></td>
      <td>
	  <?php
	    $sql = "SELECT * FROM ".TB_CASHRESERVERENEW." WHERE renewDocNo = '".$_POST[docNo]."' ORDER BY renewTimes ";
	   $result[reserveRenew] = $db->select_query($sql);
	   while($array[reserveRenew] = $db->fetch($result[reserveRenew])) { ?>
        ครั้งที่ <?=$array[reserveRenew][renewTimes]?> วันที่ <?=dateThai($array[reserveRenew][renewDatetime]);?>  <br/>
        <?=$array[reserveRenew][renewReason]?><br/>
        <? } ?>
       </td>
    </tr>
    
	<? 
	}  
	
	} ?>
     <?php if($array[reserveDetails][docStatus]=="RA") {   ?> 
     <tr>
      <td>เวลาคืนเงิน</td>
      <td><?=dateThai($array[reserveDetails][docCheckedTime]);?></td>
    </tr>
	<?   }  ?>
    
  </tbody>
</table>
