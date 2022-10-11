<?
#############################################################################
#	IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
#############################################################################
	ob_start();
	include_once(dirname(__FILE__).'/../lib/class-ws-v1.php');
	ob_end_clean();
	$url = ws::urlPath(0,false,'array');


#############################################################################
#	TRATAMOS O JSON DOS COMMITS E RETORNAMOS
#############################################################################


	if(count($url)==2){
		$commits = ws::get_github_commits('/'.$url[2]);
		if($commits==false){
			header("HTTP/1.0 501 Internal Server Error");
			die();	
		}else{
			$json = json_decode($commits);
		}
	}else{
		$commits = ws::get_github_commits();
		if($commits==false){
			header("HTTP/1.0 501 Internal Server Error");
			die();	
		}else{
			$json = json_decode($commits);
		}
		$json = json_decode(ws::get_github_commits());
	}



#############################################################################
#	SETAMOS O HEADER PARA JSON
#############################################################################
	header("Content-type:application/json");
	echo json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>