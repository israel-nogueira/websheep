<?php
	$file_url = $_SERVER['DOCUMENT_ROOT'].'/website/assets/upload-leads-files/'.$_GET['filename'];
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . $_GET['newname'] . "\""); 
	readfile($file_url); 
?>