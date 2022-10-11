<?php
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
			$path = implode(array_filter(explode('/',$path)),"/");
			define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
		}
	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}
	############################################################################################################################################
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	$tmp_name 			= 	$_FILES['arquivo']["tmp_name"];
	$size 				= 	$_FILES['arquivo']["size"];
	$type				= 	$_FILES['arquivo']["type"];
	$nome 				= 	url_amigavel_filename($_FILES['arquivo']["name"]);
	$ext				= 	strtolower(substr($nome,(strripos($nome,'.')+1)));
	$ext				= 	str_replace(array("jpeg"),array("jpg"),$ext);
	$token 				= 	md5(uniqid(rand(), true));
	$nome_novo 			=	strtolower($token.'.'.$ext);
	$rota_server_img 	=	INCLUDE_PATH.'website/assets/upload-files/'.$nome_novo;
	$rota_access_img 	=	ROOT_WEBSHEEP.'assets/upload-files/'.$nome_novo;


	if(!file_exists(INCLUDE_PATH.'website/assets')){				mkdir(INCLUDE_PATH.'website/assets');					}
	if(!file_exists(INCLUDE_PATH.'website/assets/upload-files')){	mkdir(INCLUDE_PATH.'website/assets/upload-files');	}	


	if(move_uploaded_file( $tmp_name ,$rota_server_img)){
			$_biblioteca_					= new MySQL();
			$_biblioteca_->set_table(PREFIX_TABLES.'ws_biblioteca');
			$_biblioteca_->set_insert('file',		$nome_novo);
			$_biblioteca_->set_insert('filename',	$nome);
			$_biblioteca_->set_insert('token',		$token);
			$_biblioteca_->set_insert('type',		$type);
			$_biblioteca_->set_insert('upload_size',filesize($rota_server_img));
			$_biblioteca_->insert();

			ob_start();
				$thumb = new thumb($rota_server_img);
				$thumb->setDimensions(array(200,200));
				$thumb->setNewThumb($rota_server_img);
				$thumb->setJpegQuality(100);										
				$thumb->setPngQuality(9);
				$thumb->setGifQuality(100);
				$thumb->crop=true;
				$thumb->forceDownload(false);
				$thumb->showBrowser(false);
				$thumb->process();
			ob_clean();
			die($rota_access_img);
		}





















	
?>