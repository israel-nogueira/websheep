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

include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'.PHP_EOL;
$s 					= new MySQL();
$s->set_table(PREFIX_TABLES.'ws_pages');
$s->set_where('type="path"');
$s->set_where('AND sitemap_xml<>""');
$s->select();
$s->_num_rows;
foreach ($s->fetch_array as $value) {
############################################################################################################### FOREACH NÁS PÁGINAS 
	if($value['typeList']==""){
		$value['typeList']="item";
	}
	$Paths = explode("/",$value['sitemap_xml']);
	$processaURL = array();
	foreach ($Paths as $path) { 
		if(strpos($path,',')){
			$path = preg_replace('/{{(.*?)}}/', '$1', $path);
			$string = explode(',',$path);
			if(count($string)==2){
					$path = 'ws_function:'.$string[1].'("{{'.$string[0].'}}")';
			}elseif(count($string)>2){
				$str = str_replace("(this)", "{{".$string[0]."}}", implode(array_slice($string,2),','));
				$path =  'ws_function:'.$string[1].'('.$str.');';
			}
		}else{
			$path = preg_replace('/{{(.*?)}}/', 'ws_replace:$1', $path);
		}
		$processaURL[] = $path;
	}

	############################################################################################################################## ABRE A FERRAMENTA 
	$_TOOL_ = new MySQL();
	$_TOOL_->set_table(PREFIX_TABLES.'_model_'.$value['typeList']);// abre o tipo da pesquisa
	$_TOOL_->set_where('ws_id_ferramenta="'.$value['tool_master'].'"');
	$_TOOL_->select();
	foreach ($_TOOL_->fetch_array as $tool_value) {
		############################################################################################################################## FOREACH NOS ITENS PARA RETORNAR A URL INTEIRA

		$arrayNewURL = array();
		foreach ($processaURL as $urlPath) {

			################################################################################################################### FOREACH NOS PATHS, E VERIFICA SE TEM REPLACE OU FUNCTION
		if(strpos($urlPath,'ws_replace:')>-1){
				$urlPath = $tool_value[str_replace('ws_replace:', '',$urlPath)];
				$arrayNewURL[]= $urlPath;
			}elseif(strpos($urlPath,'ws_function:')>-1){
				$urlPath = str_replace('ws_function:', '',$urlPath);
				$tira2 	=  	preg_replace('/(.*?){{(.*?)}}(.*?)/', '$2', $urlPath);
				$tira3 	=  	preg_replace('/(.*?){{(.*?)}}(.*?)/', '$3', $urlPath);
				$coluna = 	str_replace(array($tira3), '', $tira2);
				$valor 	= 	$tool_value[$coluna];	
				$funcao = 	str_replace('{{'.$coluna.'}}', $valor, $urlPath);
				// eval ('$result='.$funcao.';');
				$arrayNewURL[]= $funcao();
			}else{
				$arrayNewURL[]= $urlPath;
			}
			################################################################################################################### FIM
		}

		echo "<url>".PHP_EOL;
		echo "<loc>".DOMINIO.'/'.implode($arrayNewURL,'/').'</loc>'.PHP_EOL;
		//echo '<lastmod>2005-01-01</lastmod>'.PHP_EOL;
		echo '<changefreq>daily</changefreq>'.PHP_EOL;
		echo '<priority>0.5</priority>'.PHP_EOL;
		echo '</url>'.PHP_EOL;

		############################################################################################################################## FIM 
	}
	############################################################################################################################## FIM 
}
############################################################################################################## FIM 

$s 					= new MySQL();
$s->set_table(PREFIX_TABLES.'ws_biblioteca');
$s->set_where('type="image/jpeg"');
$s->set_where('OR type="image/jpg"');
$s->set_where('OR type="image/png"');
$s->set_where('OR type="image/gif"');
$s->select();
echo PHP_EOL.'<url>'.PHP_EOL;
echo ' <loc>'.DOMINIO.'</loc>';
foreach ($s->fetch_array as $value) {
echo'<image:image>'.PHP_EOL;
echo '  <image:loc>'.DOMINIO.'/admin/app/ws-modules/ws-model-tool/uploads/'.$value['file'].'</image:loc>'.PHP_EOL;
echo '</image:image>'.PHP_EOL;
}
	echo '</url>'.PHP_EOL;


/*

*/
echo '</urlset>'.PHP_EOL;


?>
