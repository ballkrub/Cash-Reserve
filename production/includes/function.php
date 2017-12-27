<?php

	function sqmeter_to_rai($sqmeter){
	
		$area1600 = $sqmeter/1600;
		$rai = floor($area1600);
		$nganx4 =  ($area1600-$rai)*4;
		$ngan = floor($nganx4);	
		$tarang_var = ($nganx4 - $ngan )*100;
		
		echo $rai.'-'.$ngan.'-'.$tarang_var;
	}
	
	function tis620_to_utf8($text) {
		$utf8 = "";
		for ($i = 0; $i < strlen($text); $i++) {
		$a = substr($text, $i, 1);
		$val = ord($a);
		
		if ($val < 0x80) {
		$utf8 .= $a;
		} elseif ((0xA1 <= $val && $val < 0xDA) || (0xDF <= $val && $val <= 0xFB)) {
		$unicode = 0x0E00+$val-0xA0; $utf8 .= chr(0xE0 | ($unicode >> 12));
		$utf8 .= chr(0x80 | (($unicode >> 6) & 0x3F));
		$utf8 .= chr(0x80 | ($unicode & 0x3F));
		}
		}
		return $utf8;
	}
	function utf8_to_tis620($string) {
	  $str = $string;
	  $res = "";
	  for ($i = 0; $i < strlen($str); $i++) {
		if (ord($str[$i]) == 224) {
		  $unicode = ord($str[$i+2]) & 0x3F;
		  $unicode |= (ord($str[$i+1]) & 0x3F) << 6;
		  $unicode |= (ord($str[$i]) & 0x0F) << 12;
		  $res .= chr($unicode-0x0E00+0xA0);
		  $i += 2;
		} else {
		  $res .= $str[$i];
		}
	  }
	  return $res;
	}
	
	function dateThai($date){
		if($date=="0000-00-00") { 
			echo '-';
		}else {
			$_month_name = array("01"=>"มกราคม","02"=>"กุมภาพันธ์","03"=>"มีนาคม","04"=>"เมษายน","05"=>"พฤษภาคม","06"=>"มิถุุนายน","07"=>"กรกฎาคม","08"=>"สิงหาคม","09"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
			$yy=substr($date,0,4);$mm=substr($date,5,2);$dd=substr($date,8,2);$time=substr($date,11,8);
			$yy+=543;
			$dateT=intval($dd)." ".$_month_name[$mm]." ".$yy." ".$time;
			return $dateT;
		}
	}
	
	function sqVartoRai($sqVar) {
		$sumRai = floor($sqVar/400);
		$sumNgan = floor(($sqVar-($sumRai*400))/100);
		$sumSqVar = $sqVar-(($sumNgan *100)+($sumRai*400));
		return $sumRai."-".$sumNgan."-".number_format($sumSqVar,2);
	}
	
	function dateTimeThai($datetime_obj) {
		$dateTimeArr = explode(" ",$datetime_obj);
		return "วันที่ ".dateThai($dateTimeArr[0])." เวลา ".$dateTimeArr[1];
	}
	
	function newid($tb,$key,$word,$db,$number){
		$sql="select $key from $tb order by $key";
		$result=mysql_db_query($db,$sql);
		$num=0;
		while($num1= mysql_fetch_array($result)) {
			$num=$num1[$key];
		}
		$num=substr($num,1,$number);
		$num=$num*1+1;
		$id=$word.str_pad($num,$number, "0", STR_PAD_LEFT);
		return $id;
	}
	
	function date_diff12($start_date){
		
		if($start_date=="0000-00-00") { 
			echo '-';
		} else {
		
			//$start_date =  $row_Recordset1['emp_start'];      //รูปแบบการเก็บค่าข้อมูลวันเกิด
			$today = date("Y-m-d");   //จุดต้องเปลี่ยน
			list($byear, $bmonth, $bday)= explode("-",$start_date);       //จุดต้องเปลี่ยน
			list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
		///	list($leaveyear, $leavemonth, $leavetday)= explode("-",$leave_work);                //จุดต้องเปลี่ยน
			$mstartWork = mktime(0, 0, 0, $bmonth, $bday, $byear); 
			$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
			//$mleave = mktime(0, 0, 0, $leavemonth, $leavetday, $leaveyear );
			
			$mage = ($mnow - $mstartWork);
			
			//echo "วันเกิด $birthday"."<br>\n";
			//echo "วันที่ปัจจุบัน $today"."<br>\n";
			//echo "รับค่า $mage"."<br>\n";
			$u_y=date("Y", $mage)-1970;
			$u_m=date("m",$mage)-1;
			$u_d=date("d",$mage)-1;
			
			
			echo $u_y."  ปี ".$u_m ." เดือน ".$u_d ." วัน ";
			
		}
	}
	
	function calWorkingDayByDate($startDate,$range,$holiday) {
		//getArrayoffVacation();
		$numDay = 0;
		$next1Days = strtotime($startDate);
		//$next1Days = strtotime("+1 day", $next1Days);
		do {
			$next1Days = strtotime("+1 day", $next1Days);
			if(date("w",$next1Days)!=0 AND !in_array(date('Y-m-d', $next1Days),$holiday)) {
				$numDay++;
			}
		} while($numDay<$range);
		return date('Y-m-d', $next1Days);
	}
	
	function getArrayoffVacation() {
		$arrVacation = array();
		
		$sql = "SELECT vcn_date FROM ".TB_VACATION." WHERE  ";
		$result[vacation] = $db->select_query($sql);
		while($array[vacation] = $db->fetch($result[vacation])) {
			array_push($arrVacation,$array[vacation][vcn_date]);
		}
		return $arrVacation;
	}
	
	function getUserIP() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'Unknown IP Address';
	
		return $ipaddress;
	}

	
	
?>