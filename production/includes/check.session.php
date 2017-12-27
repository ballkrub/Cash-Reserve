<?	
	$sess_userid = $_SESSION[sess_userid];
	$sess_username = $_SESSION[sess_username];
	
	if(empty($spi_username) && empty($sess_username)) {
		//$login="content_left_login.php";
		echo "<meta http-equiv=\"refresh\" content=\"0; URL = index.php\">"; 
	}
	else { }
	

	
?>