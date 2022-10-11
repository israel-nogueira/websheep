<?php
ob_start();
############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################

	if(!defined("ROOT_WEBSHEEP"))	{
		if(strpos($_SERVER['REQUEST_URI'],"ws-gz/")){
			$gzpath ="ws-gz";
		}else{
			$gzpath ="ws-gzip";
		}
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],$gzpath));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}

	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

###################################################################
# IMPORTA A CLASSE PADRÃO DO SISTEMA
###################################################################
include_once(INCLUDE_PATH.'ws-config.php');

#####################################################################
# 	RETORNA ARQUIVOS CONFORMA O PATERN SETADO
#####################################################################
include_once(INCLUDE_PATH.'admin/app/lib/ws-get-typeof.php');

###################################################################
# 	PEGAMOS A URL QUE FOI SETADA
###################################################################
	$REQUEST_URI	= explode('/',$_SERVER["REQUEST_URI"]);
	$BASE_FILE		= array_slice($REQUEST_URI,2);
###################################################################
# VERIFICAMOS DE ONDE ELE DEVE PUXAR O CONTEUDO: ADMIN OU WEBSITE
###################################################################
	if(isset($_GET['type']) && $_GET['type']=='website'){
		$BASE_FILE = INCLUDE_PATH.'website/'.implode('/',$BASE_FILE);
	}else{
		$BASE_FILE = INCLUDE_PATH.'admin/'.implode('/',$BASE_FILE);
	}

###################################################################
# LIMPA BUFFER PARA SETAR O NOVO HEADER
###################################################################
ob_end_clean();

###################################################################
# VERIFICAMOS DE ONDE ELE DEVE PUXAR O CONTEUDO: ADMIN OU WEBSITE
###################################################################
	@header('Content-Type:'.get_content_type($BASE_FILE));

	if (file_exists($BASE_FILE.'.gz') && @filemtime($BASE_FILE.'.gz') > @filemtime($BASE_FILE)) {
		echo gzdecode(file_get_contents($BASE_FILE.'.gz'));
	}else{
		$content = file_get_contents($BASE_FILE);
		file_put_contents($BASE_FILE.'.gz', gzencode($content));
		echo $content;
	};

exit;




