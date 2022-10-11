<?php
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
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	_session();
	clearstatcache();
	#####################################################  CONFIGURA DADOS GERAIS
	define("PATH",'app/ws-modules/ws-model-tool');
	$template 						=	new Template(INCLUDE_PATH.'admin/app/templates/html/ws-central-bkp.html', true);
	$template->BOT_RESTAURAR_BKP	=	_getLangMsn("ws000048");
	$template->TITULO				=	_getLangMsn('ws000049');
	$template->LABEL_BOT_TOP		=	_getLangMsn('ws000050');
	$template->PATH					=	PATH;
	if(!file_exists(ws::includePath.'/ws-bkp')){ 
		mkdir(ws::includePath.'/ws-bkp');
	}
	$dh = opendir(ws::includePath.'/ws-bkp');
	while($diretorio = readdir($dh)){
		if($diretorio != '..' && $diretorio != '.' && substr($diretorio,-3)=="zip"){
			$newSplashScreen= "";			
			$template->titulo			=	$diretorio;
			$template->label			=	_getLangMsn('ws000048');
				$fileZip = ws::includePath.'/ws-bkp/'.$diretorio;
				$zip = new ZipArchive();
				if($zip->open($fileZip) === TRUE ){
				    if(strlen($zip->getFromName('ws-description.txt'))){
				  		$ws_description 		= explode(PHP_EOL,$zip->getFromName('ws-description.txt'));
				  		$title 					= array_slice($ws_description,0,1);
				  		$description 			= implode(array_slice($ws_description,1),"<br>");
				  		$template->titulo 		= $title[0];
				    	$template->description 	= $description;
				    }else{
						$template->clear("description");
				    }
				    if(strlen($zip->getFromName('ws-thumb.jpg'))){
				  		$base64 = 'data:image/jpeg;base64,'.base64_encode($zip->getFromName('ws-thumb.jpg'));
						$template->ClasseThumb	= 'minThumb';
						$template->newSplashScreen	= $base64;
						$template->block("avatarTemplate");
				    }else{
						$template->clear("ClasseThumb");
						$template->clear("avatarTemplate");
						$template->clear("newSplashScreen");
				    }
					$zip->close();
				}else{
					$template->clear("avatarTemplate");
					$template->clear("newSplashScreen");
				}
			########################################################### pega a miniatura do zip
			$template->block("BLOCK_TEMPLATES");
		}
	}

$template->ws_rootPath = ws::rootPath;
$template->block("BLOCK_CENTRAL_BKP");
$template->show();
