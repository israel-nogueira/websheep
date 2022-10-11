<?php 
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT, -1');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
$css= "http://".$_SERVER['HTTP_HOST'].str_replace("/stylesheet.php","",$_SERVER['PHP_SELF']);
header("Content-type: text/css");