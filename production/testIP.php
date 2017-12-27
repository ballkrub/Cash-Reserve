<?php 
	//TEST
	$externalContent = file_get_contents('http://checkip.dyndns.com/');
	preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
	echo $externalIp = $m[1];
?>