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
	define("PATH", 'app/ws-modules/ws-includes');
		
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
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-includes/ws-tool-index-template.html');
	
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
	$template           										= new Template(TEMPLATE_LINK, true);
	$template->url_initPath 									= $setupdata["url_initPath"];
	$template->url_setRoot 										= $setupdata["url_setRoot"];
	$template->url_set404 										= $setupdata["url_set404"];
	$template->url_plugin 										= $setupdata["url_plugin"];
	$template->url_ignore_add 									= ($setupdata['url_ignore_add']=='1') ? "checked" : "";
	$template->PATH 											= PATH;
	$template->urlIncludes_TopTitle 							= ws::getlang('urlIncludes>topTitle');
	$template->urlIncludes_save 								= ws::getlang('urlIncludes>save');
	$template->urlIncludes_configuration_data 					= ws::getlang('urlIncludes>configuration_data');
	$template->urlIncludes_add_an_include 						= ws::getlang('urlIncludes>add_an_include');
	$template->urlIncludes_file_include	 						= ws::getlang('urlIncludes>file_include');
	$template->urlIncludes_basic_data_home_url 					= ws::getlang('urlIncludes>basicData>homeUrl');
	$template->urlIncludes_basic_data_files_directory 			= ws::getlang('urlIncludes>basicData>filesDirectory');
	$template->urlIncludes_basic_data_error_404_file 			= ws::getlang('urlIncludes>basicData>error404File');
	$template->urlIncludes_basic_data_plugins_folder 			= ws::getlang('urlIncludes>basicData>pluginsFolder');
	$template->urlIncludes_basic_data_skip_final_paths 			= ws::getlang('urlIncludes>basicData>skipFinalPaths');
	$template->urlIncludes_basic_data_ignore 					= ws::getlang('urlIncludes>basicData>ignore');
	$template->urlIncludes_modal_rearranging_items 				= ws::getlang('urlIncludes>modal>rearrangingItems');
	$template->urlIncludes_modal_adding 						= ws::getlang('urlIncludes>modal>adding');
	$template->urlIncludes_modal_save_like_to_do_what 			= ws::getlang('urlIncludes>modal>save>likeToDoWhat');
	$template->urlIncludes_modal_save_record_in_the_database 	= ws::getlang('urlIncludes>modal>save>recordInTheDatabase');
	$template->urlIncludes_modal_save_rename_file_and_create 	= ws::getlang('urlIncludes>modal>save>renameFileAndCreate');
	$template->urlIncludes_modal_save_process 					= ws::getlang('urlIncludes>modal>save>process');
	$template->urlIncludes_modal_save_cancel 					= ws::getlang('urlIncludes>modal>save>cancel');
	$template->urlIncludes_modal_save_sucess 					= ws::getlang('urlIncludes>modal>save>sucess'); 
	$template->urlIncludes_modal_save_sure_to_save 				= ws::getlang('urlIncludes>modal>save>sureToSave');
	$template->urlIncludes_modal_save_save 						= ws::getlang('urlIncludes>modal>save>save');
	$template->urlIncludes_modal_save_savesettings_saved 		= ws::getlang('urlIncludes>modal>save>settingsSaved');
	$template->urlIncludes_modal_delete_content 				= ws::getlang('urlIncludes>modal>delete>content');
	$template->urlIncludes_modal_delete_only_the_record 		= ws::getlang('urlIncludes>modal>delete>onlyTheRecord');
	$template->urlIncludes_modal_delete_delete_file_as_well 	= ws::getlang('urlIncludes>modal>delete>deleteFileAsWell');
	$template->urlIncludes_modal_program_loading_editor 		= ws::getlang('urlIncludes>modal>program>loadingEditor');
	$template->urlIncludes_modal_helpme_empty_url 				= ws::getlang('urlIncludes>modal>helpme>emptyUrl');
	$template->urlIncludes_modal_helpme_set_root 				= ws::getlang('urlIncludes>modal>helpme>setRoot');
	$template->urlIncludes_modal_helpme_set_404 				= ws::getlang('urlIncludes>modal>helpme>set404');
	$template->urlIncludes_modal_helpme_path_plugins 			= ws::getlang('urlIncludes>modal>helpme>pathPlugins');
	$template->urlIncludes_modal_helpme_ignore_add 				= ws::getlang('urlIncludes>modal>helpme>ignoreAdd');
	$template->urlIncludes_modal_helpme_add_file 				= ws::getlang('urlIncludes>modal>helpme>addFile');
	$template->urlIncludes_modal_helpme_navigate 				= ws::getlang('urlIncludes>modal>helpme>navigate');
	$template->urlIncludes_modal_helpme_helpme 					= ws::getlang('urlIncludes>modal>helpme>helpme');
	$template->urlIncludes_modal_helpme_next 					= ws::getlang('urlIncludes>modal>helpme>next');
	$template->urlIncludes_modal_helpme_previous				= ws::getlang('urlIncludes>modal>helpme>previous');
	$template->urlIncludes_include_nameFile 					= ws::getlang('urlIncludes>include>nameFile');
	$template->urlIncludes_include_save 						= ws::getlang('urlIncludes>include>save');
	$template->urlIncludes_include_edit 						= ws::getlang('urlIncludes>include>edit');
	$template->urlIncludes_include_delete 						= ws::getlang('urlIncludes>include>delete');
	$template->urlIncludes_process_nav 							= ws::getlang('urlIncludes>processNav');

	#####################################################  
	# BUSCAMOS NA BASE OS ARQUIVOS INCLUÍDOS 
	#####################################################
	$includes = new MySQL();
	$includes->set_table(PREFIX_TABLES."ws_pages");
	$includes->set_where("type='include'");
	$includes->set_order("posicao","ASC");
	$includes->select();

	#########################################################################  
	# VERIFICAMOS SE O FATOR DE NAVEGAÇÃO ESTÁ EM 1° LUGAR E PUXA O TEMPLATE 
	#########################################################################
	$LI_OPT_ARRAY = Array();
	$LI_OPT = new Template(TEMPLATE_LINK, true);
	$LI_OPT->urlIncludes_process_nav 		= ws::getlang('urlIncludes>processNav');
	$LI_OPT->urlIncludes_include_nameFile 	= ws::getlang('urlIncludes>include>nameFile');
	$LI_OPT->urlIncludes_include_save 		= ws::getlang('urlIncludes>include>save');
	$LI_OPT->urlIncludes_include_edit 		= ws::getlang('urlIncludes>include>edit');
	$LI_OPT->urlIncludes_include_delete 	= ws::getlang('urlIncludes>include>delete');
	$LI_OPT->urlIncludes_process_nav 		= ws::getlang('urlIncludes>processNav');


	if($setupdata["processoURL"]==0 ){
		$template->block("PROCESSO_NAVE");
		$LI_OPT_ARRAY[] = $LI_OPT->parse();
	}

	#########################################################################  
	# VARREMOS OS INCLUDES  
	#########################################################################
	$i = 0;
	foreach($includes->fetch_array as $item){
		#########################################################################  
		# CASO O FATOR DE NAVEGAÇÃO SEJA IGUAL AO "I" PUXAMOS PRA ARRAY  
		#########################################################################
		if($setupdata["processoURL"]==$i){
			$LI_OPT = new Template(TEMPLATE_LINK, true);
			$LI_OPT->urlIncludes_process_nav 		= ws::getlang('urlIncludes>processNav');
			$LI_OPT->urlIncludes_include_nameFile 	= ws::getlang('urlIncludes>include>nameFile');
			$LI_OPT->urlIncludes_include_save 		= ws::getlang('urlIncludes>include>save');
			$LI_OPT->urlIncludes_include_edit 		= ws::getlang('urlIncludes>include>edit');
			$LI_OPT->urlIncludes_include_delete 	= ws::getlang('urlIncludes>include>delete');
			$LI_OPT->block("PROCESSO_NAVE");
	 		$LI_OPT_ARRAY[] = $LI_OPT->parse();
		}
		#########################################################################  
		# IMPORTA PARA ARRAY O INCLUDE  
		#########################################################################
		$LI_OPT = new Template(TEMPLATE_LINK, true);
		$LI_OPT->urlIncludes_process_nav 		= ws::getlang('urlIncludes>processNav');
		$LI_OPT->urlIncludes_include_nameFile 	= ws::getlang('urlIncludes>include>nameFile');
		$LI_OPT->urlIncludes_include_save 		= ws::getlang('urlIncludes>include>save');
		$LI_OPT->urlIncludes_include_edit 		= ws::getlang('urlIncludes>include>edit');
		$LI_OPT->urlIncludes_include_delete 	= ws::getlang('urlIncludes>include>delete');
		$LI_OPT->LI_ID			=	$item['id'];
		$LI_OPT->LI_VALUE		=	$item['file'];
		$LI_OPT->LI_DATA_FILE	=	'./../../../website/'.$item['file'];
	 	$LI_OPT->block("INCLUDE");
	 	$LI_OPT_ARRAY[] = $LI_OPT->parse();
	 	$i++;
	}
	#########################################################################  
	# VERIFICAMOS SE O PROCESSO ESTÁ POR ÚLTIMO E IMPORTA PRA ARRAY  
	#########################################################################
	if($setupdata["processoURL"]==$i){
		$LI_OPT = new Template(TEMPLATE_LINK, true);
		$LI_OPT->urlIncludes_include_nameFile 	= ws::getlang('urlIncludes>include>nameFile');
		$LI_OPT->urlIncludes_include_save 		= ws::getlang('urlIncludes>include>save');
		$LI_OPT->urlIncludes_include_edit 		= ws::getlang('urlIncludes>include>edit');
		$LI_OPT->urlIncludes_include_delete 	= ws::getlang('urlIncludes>include>delete');
		$LI_OPT->urlIncludes_process_nav 		= ws::getlang('urlIncludes>processNav');
		$LI_OPT->block("PROCESSO_NAVE");
		$LI_OPT_ARRAY[] = $LI_OPT->parse();
	}
	#########################################################################  
	# SETAMOS A ARRAY PARA O TEMPLATE BASE E BLOCAMOS O UL  
	#########################################################################
	$template->LI_TEMPLATE = implode($LI_OPT_ARRAY);
	$template->block("UL_URLS");

	#########################################################################  
	# FINALIZAMOS O ARQUIVO, SETAMOS O TEMPLATE E PRINTAMOS O HTML  
	#########################################################################
	$template->block("INCLUDES");
	$template->show();