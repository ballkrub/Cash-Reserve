<ul class="nav side-menu">
  <li><a href="index.php" title="ใบสำรองเงิน"><i class="fa fa-file-text-o"></i> ใบสำรองเงิน </a>
  <li><a href="reserveCashList.php" title="รายการใบสำรองเงิน"><i class="fa fa-list"></i> รายการใบสำรองเงิน </a>
  <li><a href="reserveCashExpiredList.php" title="รายการใบสำรองเงินถูกยกเลิก"><i class="fa fa-list"></i> รายการใบสำรองเงินถูกยกเลิก </a>
  <?php if(in_array($_SESSION["spi_emp"],$payerArray)) { ?>
  <li><a href="reserveCashListToday.php" title="รายการจ่ายเงินวันนี้"><i class="fa fa-bullhorn"></i> รายการจ่ายเงินวันนี้ </a>
   <li><a href="reserveType.php" title="ประเภทการสำรองเงิน"><i class="fa fa-th-list"></i> ประเภทการสำรองเงิน </a>
  <?php  } ?>
  <!--
   <li><a href="reserveCashReport.php" title="รายงานการสำรองเงิน"><i class="fa fa-bar-chart"></i> รายงานการสำรองเงิน </a>
   <li><a href="reserveCalendar.php" title="ตารางปฏิทิน"><i class="fa fa-calendar"></i> ตารางปฏิทิน </a>
   -->
</ul>