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

	############################################################################################################  
	# DEFINE O PATH DO MÓDULO 
	############################################################################################################
	define("PATH", 'app/ws-modules/ws-includes-js-css');
		
	############################################################################################################  
	# LIMPA O CACHE INTERNO
	############################################################################################################
	clearstatcache();
	
	############################################################################################################  
	# CONTROLA O CACHE
	############################################################################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	############################################################################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	############################################################################################################
	ob_start();
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	############################################################################################################  
	# CRIA SESSÃO
	############################################################################################################  
	_session();

	############################################################################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	############################################################################################################
	verifyUserLogin();
	
	############################################################################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	############################################################################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-includes-js-css/ws-tool-includes.html');

	############################################################################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	############################################################################################################
	$template           								= new Template(TEMPLATE_LINK, true);
	$template->TYPE 									= $_GET['type'];
	$template->includeJsCss_index_title 				= ws::getlang('includeJsCss>index>title');
	$template->includeJsCss_index_title_Module 			= ws::getlang('includeJsCss>index>titleModule','{TYPE}',$_GET['type']);
	$template->includeJsCss_index_modal_edit_access 	= ws::getlang('includeJsCss>index>modal>edit>access');
	$template->includeJsCss_index_modal_delete_content 	= ws::getlang('includeJsCss>index>modal>delete>content');
	$template->includeJsCss_index_modal_delete_delete 	= ws::getlang('includeJsCss>index>modal>delete>delete');
	$template->includeJsCss_index_modal_delete_cancel 	= ws::getlang('includeJsCss>index>modal>delete>cancel');

	############################################################################################################  
	# PESQUISAMOS NA BASE AS PÁGINAS CADASTRADAS
	############################################################################################################
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES."ws_pages");
	$s->set_where('type="path"');
	$s->select();

	############################################################################################################  
	# VARREMOS OS REGISTROS E RETORNAMOS O HTML
	############################################################################################################
	foreach ($s->fetch_array as $value) {
		$template->LI_ID = $value['id'];
		$template->LI_PATH = $value['path'];
		$template->block("LI_INCLUDE");
	}
	############################################################################################################  
	# SELECIONAMOS O BLOCO PRINCIPAL E RETORNAMOS O HTML
	############################################################################################################
	$template->block("INCLUDE_JS_CSS");
	$template->show();