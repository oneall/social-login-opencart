<?php

	$rand = mt_rand(99999, 9999999);

	$oasl_widget = array();
	$oasl_widget[] = " <div id='oneall_container_".$rand."'></div>";
	$oasl_widget[] = " <script type='text/javascript'>";
	$oasl_widget[] = "(function() {";
	$oasl_widget[] = "  $('#oneall_container_".$rand."').load('index.php?route=extension/module/oneall/loadWidget');";
	$oasl_widget[] = " })();";
	$oasl_widget[] = " </script>";
	
	// Display Widget   	 
	echo "\n\t" . implode ("\n\t", $oasl_widget)."\n";

?>