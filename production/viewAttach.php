<?php    
	require_once("includes/config.in.php");
	require_once("includes/class.mysql.php");	
	if(isset($_GET[docNo])) {
		$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
		$sql = "SELECT * FROM ".TB_CASHRESERVE." WHERE docNo =   '".$_GET[docNo]."'  LIMIT 0,1 ";
		$result[docAttachFile] = $db->select_query($sql);
		$array[docAttachFile] = $db->fetch($result[docAttachFile]);
		//header("Content-Disposition: attachment; filename='. $array[docAttachFile][docAttach]  ' ");    
		//header("Content-type: application/".$resFiles[0]->file_type);
		switch( $array[docAttachFile][docAttachType] ){
			case "pdf" :  header("Content-type: application/pdf"); echo '1234'; break;
			case "png" :  header("Content-type: image/png");  break;
			case "jpg" :  header("Content-type: image/jpg");  break;
			default :header("Content-type: application/".$array[docAttachFile][docAttachType]); break;
		}
		//header("Content-Disposition: attachment; filename=$_GET[docNo]");
		echo  $array[docAttachFile][docAttach];
		$db->closedb();       
	}
?>