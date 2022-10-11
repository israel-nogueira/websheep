<?php

	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

	$_FILE_GET_ 				= INCLUDE_PATH.'website/assets/upload-files/'.$_GET['filename'];
	
	$_ARQUIVO_ATUAL_ 		= basename($_FILE_GET_) == basename(__FILE__);
	$_FORA_DO_SITE 			= strpos($_FILE_GET_, '..');
	$_ARQUIVO_NAO_EXISTE 	= !file_exists($_FILE_GET_);
	$_HTACCESS 				= basename($_FILE_GET_) == '.htaccess';



	if($_HTACCESS || $_ARQUIVO_ATUAL_ || $_FORA_DO_SITE || $_ARQUIVO_NAO_EXISTE){
		die("Acesso bloqueado");
	}


	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . basename($_FILE_GET_) . "\""); 
	readfile($_FILE_GET_); 



?>