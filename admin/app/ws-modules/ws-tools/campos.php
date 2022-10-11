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
	
	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'app/ws-modules/ws-tools');
		
	#####################################################  
	# LIMPA O CACHE INTERNO
	#####################################################
	clearstatcache();
	
	#####################################################  
	# CONTROLA O CACHE
	#####################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	#####################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	#####################################################
	ob_start();
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	#####################################################  
	# DEFINIMOS O ID DA FERRAMENTA
	#####################################################
	define("ws_id_ferramenta", $_GET['ws_id_ferramenta']);

	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# SEPARAMOS A VARIÁVEL DO SETUP DATA 
	#####################################################
	$setupdata = new MySQL();
	$setupdata->set_table(PREFIX_TABLES . 'setupdata');
	$setupdata->set_order('id', 'DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	
	#####################################################  
	# SELECIONAMOS A FERRAMENTA 
	#####################################################
	$FERRAMENTA = new MySQL();
	$FERRAMENTA->set_table(PREFIX_TABLES.'ws_ferramentas');
	$FERRAMENTA->set_where('id="'.ws_id_ferramenta.'"');
	$FERRAMENTA->debug(0);
	$FERRAMENTA->distinct();
	$FERRAMENTA->select();
	$FERRAMENTA = $FERRAMENTA->fetch_array[0];

	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tools/ws-tool-campos-template.html');

	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           		= new Template(TEMPLATE_LINK, true);

	######################################################################
	# SETAMOS AS VARIÁVEIS NECESSÁRIAS PARA O FUNCIONAMENTO DO MODULO 
	######################################################################
	$template->WS_ID_FERRAMENTA = ws_id_ferramenta;
	$template->ROOT_WEBSHEEP 	= ROOT_WEBSHEEP;
	$template->_tit_topo_ 		= $FERRAMENTA['_tit_topo_'];
	$template->PREFIX 			= $FERRAMENTA['_prefix_'];
	$template->legenda_1 		= ($FERRAMENTA['_fotos_']		=='1')	?	" legenda='Desabilitar imagens internas'" 		:" legenda='Habilitar imagens internas'" ;
	$template->legenda_2 		= ($FERRAMENTA['_galerias_']	=='1')	?	" legenda='Desabilitar galerias internas'" 		:" legenda='Habilitar galerias internas'";
	$template->legenda_3 		= ($FERRAMENTA['_files_']		=='1')	?	" legenda='Desabilitar listagem de arquivos'" 	:" legenda='Habilitar listagem de arquivos'";
	$template->class_1 			= ($FERRAMENTA['_fotos_']=='1') 		? 	" disabled" 	: 	"";
	$template->class_2 			= ($FERRAMENTA['_galerias_']=='1')		? 	" disabled" 	:	"";
	$template->class_3 			= ($FERRAMENTA['_files_']=='1')			? 	" disabled" 	:	"";
	$template->ENCODE_GOBACK 	= (isset($_GET['goback']) && $_GET['goback']!="") ? 'goback='.urlencode($_GET['goback']).'&' : null;

	######################################################################
	# SETAMOS O BLOCK E RETORNAMOS O HTML 
	######################################################################
	$template->block("CAMPOS");
	$template->show();