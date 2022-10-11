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
	define("PATH", './app/ws-modules/ws-register-leads');
		
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
	# CRIA SESSÃO
	#####################################################  
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-register-leads/ws-tool-leads-index.html');

	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           	= new Template(TEMPLATE_LINK, true);
	$template->DOMINIO 		= ws::protocolURL().DOMINIO;
	$template->PATH 		= './app/ws-modules/ws-register-leads';
		
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_list_leads');
	$s->select();
	foreach($s->fetch_array as $img){ 
		$template->LI_TOKEN = strtolower($img['token']);
		$template->LI_TITLE = $img['title'];
		$template->LI_ID 	= $img['id']; 
		$template->block('LI_LEAD'); 
	}
	
	#####################################################  
	# BLOCO DE TRADUÇÃO
	#####################################################
	
	$template->ws_includePath						=	ws::rootPath;
	$template->Leads_Index_FormRegister				=	ws::getLang("Leads>Index>FormRegister");
	$template->Leads_Index_CreateLink				=	ws::getLang("Leads>Index>CreateLink");
	$template->Leads_Index_ViewRegister				=	ws::getLang("Leads>Index>ViewRegister");
	$template->Leads_Index_Edit						=	ws::getLang("Leads>Index>Edit");
	$template->Leads_Index_Delete					=	ws::getLang("Leads>Index>Delete");
	$template->Leads_Index_Modal_LoadRegister		=	ws::getLang("Leads>Index>Modal>LoadRegister");
	$template->Leads_Index_Modal_AreSure			=	ws::getLang("Leads>Index>Modal>AreSure");
	$template->Leads_Index_Modal_NotBack			=	ws::getLang("Leads>Index>Modal>NotBack");
	$template->Leads_Index_Modal_Delete				=	ws::getLang("Leads>Index>Modal>Delete");
	$template->Leads_Index_Modal_Cancel				=	ws::getLang("Leads>Index>Modal>Cancel");
	$template->Leads_Index_Modal_DeleteLink			=	ws::getLang("Leads>Index>Modal>DeleteLink");
	$template->Leads_Index_Modal_CreateLead			=	ws::getLang("Leads>Index>Modal>CreateLead");

	

	#####################################################  
	# FINALIZA O ARQUIVO, PUXA O BLOCO E RETORNA O HTML 
	#####################################################
	$template->block('LEAD_CAPTURE'); 
	$template->show(); 