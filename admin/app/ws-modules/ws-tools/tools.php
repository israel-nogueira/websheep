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
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tools/ws-tool-tools-template.html');
	
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
	$template->ROOT_WEBSHEEP						= ROOT_WEBSHEEP;
	$template->ToolsManager_Title					= ws::getLang('ToolsManager>Title');
	$template->ToolsManager_Back					= ws::getLang('ToolsManager>Back');
	$template->ToolsManager_ImportTool				= ws::getLang('ToolsManager>ImportTool');
	$template->ToolsManager_CreateTool				= ws::getLang('ToolsManager>CreateTool');
	$template->ToolsManager_ExportAll				= ws::getLang('ToolsManager>ExportAll');
	$template->ToolsManager_DeleteAll				= ws::getLang('ToolsManager>DeleteAll');
	$template->ToolsManager_pathTools				= ws::getLang('ToolsManager>pathTools');
	$template->ToolsManager_Clear					= ws::getLang('ToolsManager>Clear');
	$template->ToolsManager_Export					= ws::getLang('ToolsManager>Export');
	$template->ToolsManager_Move					= ws::getLang('ToolsManager>Move');
	$template->ToolsManager_Access					= ws::getLang('ToolsManager>Access');
	$template->ToolsManager_Inputs					= ws::getLang('ToolsManager>Inputs');
	$template->ToolsManager_Select					= ws::getLang('ToolsManager>Select');
	$template->ToolsManager_Modal_Backing			= ws::getLang('ToolsManager>Modal>Backing');
	$template->ToolsManager_Modal_RepoTool			= ws::getLang('ToolsManager>Modal>RepoTool');
	$template->ToolsManager_Modal_RepoFail			= ws::getLang('ToolsManager>Modal>RepoFail');
	$template->ToolsManager_Modal_ExportTool		= ws::getLang('ToolsManager>Modal>ExportTool');
	$template->ToolsManager_Modal_ExportTools		= ws::getLang('ToolsManager>Modal>ExportTools');
	$template->ToolsManager_Modal_DeleteTool		= ws::getLang('ToolsManager>Modal>DeleteTool');
	$template->ToolsManager_Modal_ExportThe			= ws::getLang('ToolsManager>Modal>ExportThe');
	$template->ToolsManager_Modal_DeleteThe			= ws::getLang('ToolsManager>Modal>DeleteThe');
	$template->ToolsManager_Modal_TheTools			= ws::getLang('ToolsManager>Modal>TheTools');
	$template->ToolsManager_Modal_AreYouSure		= ws::getLang('ToolsManager>Modal>AreYouSure');
	$template->ToolsManager_Modal_SureTool			= ws::getLang('ToolsManager>Modal>SureTool');
	$template->ToolsManager_Modal_WaitDel			= ws::getLang('ToolsManager>Modal>WaitDel');
	$template->ToolsManager_Modal_AllRegister		= ws::getLang('ToolsManager>Modal>AllRegister');
	$template->ToolsManager_Modal_MakeBkpDel		= ws::getLang('ToolsManager>Modal>MakeBkpDel');
	$template->ToolsManager_Modal_JustDel			= ws::getLang('ToolsManager>Modal>JustDel');
	$template->ToolsManager_Modal_FileName			= ws::getLang('ToolsManager>Modal>FileName');
	$template->ToolsManager_Modal_Cancel			= ws::getLang('ToolsManager>Modal>Cancel');
	$template->ToolsManager_Modal_ButtonLink		= ws::getLang('ToolsManager>Modal>ButtonLink');
	$template->ToolsManager_Modal_LoadEditor		= ws::getLang('ToolsManager>Modal>LoadEditor');
	$template->ToolsManager_Modal_Loading			= ws::getLang('ToolsManager>Modal>Loading');
	$template->ToolsManager_Modal_Verifying			= ws::getLang('ToolsManager>Modal>Verifying');
	$template->ToolsManager_Modal_SelectField		= ws::getLang('ToolsManager>Modal>SelectField');
	$template->ToolsManager_Modal_Title				= ws::getLang('ToolsManager>Modal>Title');
	$template->ToolsManager_Modal_Text				= ws::getLang('ToolsManager>Modal>Text');
	$template->ToolsManager_Modal_FileName			= ws::getLang('ToolsManager>Modal>FileName');
	$template->ToolsManager_Modal_Avatar			= ws::getLang('ToolsManager>Modal>Avatar');
	$template->ToolsManager_Modal_Image				= ws::getLang('ToolsManager>Modal>Image');
	$template->ToolsManager_Modal_Uploaded			= ws::getLang('ToolsManager>Modal>Uploaded');
	$template->ToolsManager_Modal_SavedOK			= ws::getLang('ToolsManager>Modal>SavedOK');
	$template->ToolsManager_Modal_ChooseApp			= ws::getLang('ToolsManager>Modal>ChooseApp');
	$template->ToolsManager_Modal_ColumnPrefix		= ws::getLang('ToolsManager>Modal>ColumnPrefix');
	$template->ToolsManager_Modal_AppName			= ws::getLang('ToolsManager>Modal>AppName');
	$template->ToolsManager_Modal_SlugTool			= ws::getLang('ToolsManager>Modal>SlugTool');
	$template->ToolsManager_Modal_Example			= ws::getLang('ToolsManager>Modal>Example');
	$template->ToolsManager_Modal_DoubleTool		= ws::getLang('ToolsManager>Modal>DoubleTool');
	$template->ToolsManager_Modal_SelectTool		= ws::getLang('ToolsManager>Modal>SelectTool');
	$template->ToolsManager_Modal_PleaseFill		= ws::getLang('ToolsManager>Modal>PleaseFill');
	$template->ToolsManager_Modal_CreateOk			= ws::getLang('ToolsManager>Modal>CreateOk');
	$template->ToolsManager_Modal_ClearTool			= ws::getLang('ToolsManager>Modal>ClearTool');
	$template->ToolsManager_Modal_YesSure			= ws::getLang('ToolsManager>Modal>YesSure');
	$template->ToolsManager_Modal_NoSorry			= ws::getLang('ToolsManager>Modal>NoSorry');
	$template->ToolsManager_Modal_WaitClean			= ws::getLang('ToolsManager>Modal>WaitClean');
	$template->ToolsManager_Modal_ToolCleaned		= ws::getLang('ToolsManager>Modal>ToolCleaned');
	$template->ToolsManager_Modal_OkThanks			= ws::getLang('ToolsManager>Modal>OkThanks');
	$template->ToolsManager_Modal_WaitDel			= ws::getLang('ToolsManager>Modal>WaitDel');
	$template->ToolsManager_Modal_AndAllReg			= ws::getLang('ToolsManager>Modal>AndAllReg');
	$template->ToolsManager_Modal_DelOk				= ws::getLang('ToolsManager>Modal>DelOk');
	$template->ToolsManager_Modal_DelTool			= ws::getLang('ToolsManager>Modal>DelTool');
	$template->ToolsManager_Modal_MakeDel			= ws::getLang('ToolsManager>Modal>MakeDel');
	$template->ToolsManager_Modal_JustDel			= ws::getLang('ToolsManager>Modal>JustDel');
	// $template->ToolsManager_Modal_MySQLPrefix		= ws::getLang('ToolsManager>Modal>MySQLPrefix');
	$template->ToolsManager_Modal_Slug				= ws::getLang('ToolsManager>Modal>Slug');
	$template->ToolsManager_Modal_Group				= ws::getLang('ToolsManager>Modal>groupTool');
	$template->ToolsManager_Modal_Group_Buttom		= ws::getLang('ToolsManager>Modal>groupToolButtom');
	$template->ToolsManager_Modal_Group_Title		= ws::getLang('ToolsManager>Modal>selectGroupTitle');
	$template->ToolsManager_Modal_NewNameTool		= ws::getLang('ToolsManager>Modal>NewNameTool');
	$template->ToolsManager_Modal_Install			= ws::getLang('ToolsManager>Modal>Install');
	// $template->ToolsManager_Modal_ExistPrefix		= ws::getLang('ToolsManager>Modal>ExistPrefix');
	$template->ToolsManager_Modal_ExistSlug			= ws::getLang('ToolsManager>Modal>ExistSlug');
	$template->ToolsManager_Modal_ToolInstall		= ws::getLang('ToolsManager>Modal>ToolInstall');
	$template->ToolsManager_Modal_ErrorInstall		= ws::getLang('ToolsManager>Modal>ErrorInstall');
	$template->ToolsManager_Modal_rootGroup		= ws::getLang('ToolsManager>ToolDetails>rootGroup');
	$template->PATH 								= PATH;


	#####################################################  
	# VERIFICA SE TEM LINK DE RETORNO
	#####################################################
	if(isset($_GET['goback'])){
		$template->GOBACK = $_GET['goback'];
		$template->block("GOBACKBT");
		$template->block("GOBACKJS");
		$template->LINK_DETALHES = '?ws_id_ferramenta="+id+"&goback='.ROOT_WEBSHEEP.'admin/app/ws-modules/ws-tools/tools.php'.urlencode("?").'plugin=true'.urlencode("&").'goback='.$_GET['goback'];
	}else{
		$template->LINK_DETALHES = '?ws_id_ferramenta="+id+"';
		$template->clear("GOBACKBT");
		$template->clear("GOBACKJS");
	}


	#####################################################  
	# PUXAMOS DA BASE TODAS AS FERRAMENTAS
	#####################################################
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES."ws_ferramentas");
	$s->set_where('App_Type="1"');
	$pl = (isset($_GET['plugin']) && $_GET['plugin']=="true")?  1 : 0 ;
	$s->set_where('AND  _plugin_="'.$pl.'"');
	$s->set_order('posicao','ASC');
	$s->select();

	#####################################################  
	# RETORNAMOS O TEMPLATE DA FERRAMENTA
	#####################################################
	foreach($s->fetch_array as $ferramenta){
		$class= ($ferramenta['_plugin_']=='1') ? "plugin": "";
		$template->LI_ID 		= $ferramenta['id'];
		$template->LI_CLASS 	= $class;
		$template->LI_SLUG 		= $ferramenta['slug'];
		$template->LI_PREFIX 	= $ferramenta['_prefix_'];
		$template->LI_ID 		= $ferramenta['id'];
		$template->LI_TOKEN 	= $ferramenta['token'];
		$template->LI_TITLE 	= $ferramenta['_tit_menu_'];
		$template->block("TOOL");
	}

	#####################################################  
	# BUSCAMOS NA BASE AGORA AS FERRAMENTAS PARA CLONAGEM
	#####################################################
	$optTool 				= array();
	$optCloneTool 			= array();
	$toolsModel 		= new MySQL();
	$toolsModel->set_table(PREFIX_TABLES.'ws_ferramentas');
	$toolsModel->set_order('_tit_menu_','ASC');
	$toolsModel->set_where('App_Type="1"');
	#$toolsModel->set_where('AND clone_tool="0"');
	$toolsModel->select();
	$template->MODAL_PLUGIN = (isset($_GET['plugin']) && $_GET['plugin']=="true") ? "1" : "0";	

	foreach ($toolsModel->fetch_array as $value) {
		if($value['clone_tool']=="0"){
			$optTool[] 		= "<option value='".$value['id']."'>".$value['_tit_menu_']."</option>";
		}else{
			$optCloneTool[] = "<option value='".$value['id']."'>".$value['_tit_menu_']."</option>";
		}
	}


	$template->MODAL_STR 	=	'<optgroup label=\"Ferramentas criadas\">'.implode($optTool).'</optgroup><optgroup label=\"Ferramentas clonadas\">'.implode($optCloneTool).'</optgroup>';
	$template->GET_PLUGIN 	= 	(isset($_GET['plugin']) && $_GET['plugin']=="true") ? "1" : "0";


	#####################################################  
	# PUXAMOS TODOS OS GRUPOS
	#####################################################
	$_GROUPS = new MySQL();
	$_GROUPS->set_table(PREFIX_TABLES."ws_path_tools");
	$_GROUPS->set_order('posicao','ASC');
	$_GROUPS->select();

	#####################################################  
	# RETORNAMOS O TEMPLATE DOS GRUPOS
	#####################################################
	foreach($_GROUPS->fetch_array as $_GROUP){
		$template->id 			= $_GROUP['id'];
		$template->LABEL_GROUP 	= $_GROUP['path_name'];
		$template->block("CATEGORIAS");
	}

	#########################################################################
	# CASO O MÓDULO SEJA ACESSADO APENAS PARA INSTALAÇÃO RETORNAMOS A FUNÇÃO
	#########################################################################
	if(isset($_GET['install']) && $_GET['install']=='true'){
		$template->block('INSTALLfn');
		$template->block('INSTALL');
	}

	#####################################################  
	# FINALIZAMOS O MÓDULO E RETORNAMOS O TEMPLATE HTML
	#####################################################
	$template->block('TOOL_MODEL');
	$template->show();