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
	define("PATH", 'app/ws-modules/ws-password-protected-files');
		
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
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-password-protected-files/ws-key-file-links.html');

	############################################################################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	############################################################################################################
	$template           												= new Template(TEMPLATE_LINK, true);
	$template->PATH 													= PATH;
	$template->ROOT_PATH												= ws::rootPath;
	$template->TOKEN_FILE 												= $_GET['tokenfile'];
	$template->DOMINIO  												= DOMINIO;
	$template->keyFile_links_mainTitle 									= ws::getLang("keyFile>links>mainTitle");
	$template->keyFile_links_backButton 								= ws::getLang("keyFile>links>backButton");
	$template->keyFile_links_createLinkButton 							= ws::getLang("keyFile>links>createLinkButton");
	$template->keyFile_links_failCreateLink 							= ws::getLang("keyFile>links>failCreateLink");
	$template->keyFile_links_modal_save_saving 							= ws::getLang("keyFile>links>modal>save>saving");
	$template->keyFile_links_topAlert_sucessCreateLink 					= ws::getLang("keyFile>links>topAlert>sucessCreateLink");
	$template->keyFile_links_topAlert_settingsIncorrectly 				= ws::getLang("keyFile>links>topAlert>settingsIncorrectly");
	$template->keyFile_links_modal_delete_content 						= ws::getLang("keyFile>links>modal>delete>content");
	$template->keyFile_links_modal_delete_bot1 							= ws::getLang("keyFile>links>modal>delete>bot1");
	$template->keyFile_links_modal_delete_bot2 							= ws::getLang("keyFile>links>modal>delete>bot2");
	$template->keyFile_links_modal_delete_before 						= ws::getLang("keyFile>links>modal>delete>before");
	$template->keyFile_links_modal_addLink_content 						= ws::getLang("keyFile>links>modal>addLink>content");
	$template->keyFile_links_backButtonBefore 							= ws::getLang("keyFile>links>backButtonBefore");
	



	############################################################################################################  
	# PESQUISAMOS A BASE DE DADOS O ARQUIVO  
	############################################################################################################
	$ws_biblioteca = new MySQL();
	$ws_biblioteca->set_table(PREFIX_TABLES.'ws_biblioteca');
	$ws_biblioteca->set_Limit(1);
	$ws_biblioteca->set_Order("id","DESC");
	$ws_biblioteca->set_where('tokenFile="'.$_GET['tokenfile'].'"');
	$ws_biblioteca->select();

	############################################################################################################  
	# SETAMOS O NOME DO ARQUIVO  
	############################################################################################################
	$template->FILENAME 	= $ws_biblioteca->obj[0]->filename;

	############################################################################################################  
	# AGORA PUXAMOS DA BASE AS CHAVES DE ACESSO A ESSE ARQUIVO  
	############################################################################################################
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_keyfile as keyFile ');
	$s->set_colum('keyFile.id,keyFile.tokenFile,keyFile.active,keyFile.disableToDown,keyFile.refreshToDown,keyFile.keyaccess,keyFile.expire');
	$s->set_where('keyFile.tokenFile="'.$_GET['tokenfile'].'"');
	$s->select();
	foreach($s->fetch_array as $img){ 
		$template->LI_ID_LINK			=$img['id'];
		$template->LI_TOKEN_FILE		=$img['tokenFile'];
		$template->LI_ACTIVE			=$img['active'];
		$template->LI_DISABLE_TO_DOWN	=$img['disableToDown'];
		$template->LI_REFRESH_TO_DOWN	=$img['refreshToDown'];
		$template->LI_LEY_ACCESS		=$img['keyaccess'];
		$template->LI_EXPIRE			=$img['expire'];
		$template->block("LI_KEYLINKS");
	}

	############################################################################################################  
	# FINALIZAMOS A CLASSE E PRINTAMOS O HTML  
	############################################################################################################
	$template->block("KEYLINKS");
	$template->show();