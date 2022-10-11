<?
#############################################################################
#	IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
#############################################################################
	ob_start();
	include_once(dirname(__FILE__).'/../lib/class-ws-v1.php');

	ob_end_clean();
	$branches = ws::get_github_branches();
	if($branches==false){
		header("HTTP/1.0 501 Internal Server Error");
		die();	
	}else{
		#############################################################################
		#	SETAMOS O HEADER PARA JSON
		#############################################################################
		header("Content-type:application/json");
	}
#############################################################################
#	TRATAMOS O JSON DAS BRANCHES E RETORNAMOS
#############################################################################
	$json = json_decode();
	echo json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);