<?php

	#####################################################  
	# FORMATA O CAMINHO ROOT
	#####################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

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
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tools/ws-path-tools-template.html');
	
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
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           							= new Template(TEMPLATE_LINK, true);
 	$template->ROOT_WEBSHEEP						= ws::rootPath;
	$template->ToolsManager_Title					= ws::getLang('ToolsManager>PathTools>Title');
	$template->ToolsManager_Back					= ws::getLang('ToolsManager>PathTools>Back');
	$template->ToolsManager_Add						= ws::getLang('ToolsManager>PathTools>AddField');
	$template->ToolsManager_Backing					= ws::getLang('ToolsManager>PathTools>Backing');
	$template->ToolsManager_RepoPath				= ws::getLang('ToolsManager>PathTools>RepoPath');
	$template->ToolsManager_RepoFail				= ws::getLang('ToolsManager>PathTools>RepoFail');
	$template->ToolsManager_Save					= ws::getLang('ToolsManager>PathTools>Save');
	$template->ToolsManager_Delete					= ws::getLang('ToolsManager>PathTools>Delete');
	$template->ToolsManager_Move					= ws::getLang('ToolsManager>PathTools>Move');
	$template->ToolsManager_Label_Confirm_Delete	= ws::getLang('ToolsManager>PathTools>ModalDelete>Label');
	$template->ToolsManager_Confirm_Confirm_Delete	= ws::getLang('ToolsManager>PathTools>ModalDelete>Confirm');
	$template->ToolsManager_Cancel_Confirm_Delete	= ws::getLang('ToolsManager>PathTools>ModalDelete>Cancel');
	$template->ToolsManager_Confirm_Sucess			= ws::getLang('ToolsManager>PathTools>ModalDelete>Sucess');
	$template->ToolsManager_Confirm_Fail			= ws::getLang('ToolsManager>PathTools>ModalDelete>Fail');
	$template->ToolsManager_addGroup_Sucess			= ws::getLang('ToolsManager>PathTools>addGroup>Sucess');
	$template->ToolsManager_addGroup_Fail			= ws::getLang('ToolsManager>PathTools>addGroup>Fail');
	$template->ToolsManager_SaveSucess				= ws::getLang('ToolsManager>PathTools>SaveSucess');
	$template->ToolsManager_SaveFail				= ws::getLang('ToolsManager>PathTools>SaveFail');

	#####################################################  
	# PUXAMOS DA BASE TODAS AS FERRAMENTAS
	#####################################################
	$path = new MySQL();
	$path->set_table(PREFIX_TABLES."ws_path_tools");
	$path->set_order('posicao','ASC');
	$path->select();

	#####################################################  
	# RETORNAMOS O TEMPLATE DA FERRAMENTA
	#####################################################
	foreach($path->fetch_array as $ferramenta){
		$template->LI_ID 			= $ferramenta['id'];
		$template->LI_PATH_NAME 	= $ferramenta['path_name'];
		$template->block("PATHS");
	}

	#####################################################  
	# FINALIZAMOS O MÓDULO E RETORNAMOS O TEMPLATE HTML
	#####################################################
	$template->block('TOOL_MODEL');
	$template->show();