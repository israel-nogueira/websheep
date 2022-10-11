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
	define("PATH", 'app/ws-modules/ws-hd-management');
		
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
	// _session();
	$session = new session();
	define("ws_id_ferramenta", $_GET['ws_id_ferramenta']);
	
	$session->get('ws_id_ferramenta',ws_id_ferramenta);

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tools/ws-tool-datails-template.html');
	
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
	

	#########################################################################  
	# SELECIONAMOS NA BASE DE DADOS A FERRAMENTA COM O id "ws_id_ferramenta" 
	#########################################################################
	$FERRAMENTA					= 	new MySQL();
	$FERRAMENTA->set_table(PREFIX_TABLES.'ws_ferramentas');
	$FERRAMENTA->set_order('posicao','ASC');
	$FERRAMENTA->set_where('id="'.ws_id_ferramenta.'"');
	$FERRAMENTA->debug(0);
	$FERRAMENTA->select();
	$FERRAMENTA = $FERRAMENTA->fetch_array[0];
	$SLUG 		= $FERRAMENTA['slug'];


	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           										=	new Template(TEMPLATE_LINK, true);
	$template->PATCH 											=	"app/ws-modules/ws-tools";
	$template->WS_ID_FERRAMENTA 								=	ws_id_ferramenta;
	$template->ROOT_WEBSHEEP 									=	ROOT_WEBSHEEP;
	$template->ToolsManager_ToolDetails_mainTitle				=	ws::getLang("ToolsManager>ToolDetails>mainTitle");
	$template->ToolsManager_ToolDetails_BackToPluginsButton		=	ws::getLang("ToolsManager>ToolDetails>BackToPluginsButton");
	$template->ToolsManager_ToolDetails_BackToToolsButton		=	ws::getLang("ToolsManager>ToolDetails>BackToToolsButton");
	$template->ToolsManager_ToolDetails_saveTool				=	ws::getLang("ToolsManager>ToolDetails>saveTool");
	$template->ToolsManager_ToolDetails_label_slugTool			=	ws::getLang("ToolsManager>ToolDetails>label>slugTool");
	$template->ToolsManager_ToolDetails_label_nameTool			=	ws::getLang("ToolsManager>ToolDetails>label>nameTool");
	$template->ToolsManager_ToolDetails_label_prefixColunm		=	ws::getLang("ToolsManager>ToolDetails>label>prefixColunm");
	$template->ToolsManager_ToolDetails_label_description		=	ws::getLang("ToolsManager>ToolDetails>label>description");
	$template->ToolsManager_ToolDetails_toolType				=	ws::getLang("ToolsManager>ToolDetails>toolType");
	$template->ToolsManager_ToolDetails_label_anItem			=	ws::getLang("ToolsManager>ToolDetails>label>anItem");
	$template->ToolsManager_ToolDetails_label_list				=	ws::getLang("ToolsManager>ToolDetails>label>list");
	$template->ToolsManager_ToolDetails_label_category			=	ws::getLang("ToolsManager>ToolDetails>label>category");
	$template->ToolsManager_ToolDetails_columnsWillBeDisplayed	=	ws::getLang("ToolsManager>ToolDetails>columnsWillBeDisplayed");
	$template->ToolsManager_ToolDetails_toolIsPartOfAPlugin		=	ws::getLang("ToolsManager>ToolDetails>toolIsPartOfAPlugin");
	$template->ToolsManager_ToolDetails_AddJs_title				=	ws::getLang("ToolsManager>ToolDetails>AddJs>title");
	$template->ToolsManager_ToolDetails_AddJs_opt1				=	ws::getLang("ToolsManager>ToolDetails>AddJs>opt1");
	$template->ToolsManager_ToolDetails_AddJs_opt2				=	ws::getLang("ToolsManager>ToolDetails>AddJs>opt2");
	$template->ToolsManager_ToolDetails_AddJs_opt3				=	ws::getLang("ToolsManager>ToolDetails>AddJs>opt3");
	$template->ToolsManager_ToolDetails_AddJs_opt4				=	ws::getLang("ToolsManager>ToolDetails>AddJs>opt4");
	$template->ToolsManager_ToolDetails_chosen_maxSelect		=	ws::getLang("ToolsManager>ToolDetails>chosen>maxSelect");
	$template->ToolsManager_ToolDetails_modal_back				=	ws::getLang("ToolsManager>ToolDetails>modal>back");
	$template->ToolsManager_ToolDetails_modal_loading			=	ws::getLang("ToolsManager>ToolDetails>modal>loading");
	$template->ToolsManager_ToolDetails_modal_save_content		=	ws::getLang("ToolsManager>ToolDetails>modal>save>content");
	$template->ToolsManager_ToolDetails_modal_save_bot1			=	ws::getLang("ToolsManager>ToolDetails>modal>save>bot1");
	$template->ToolsManager_ToolDetails_modal_save_bot2			=	ws::getLang("ToolsManager>ToolDetails>modal>save>bot2");
	$template->ToolsManager_ToolDetails_TopAlert_setSlug		=	ws::getLang("ToolsManager>ToolDetails>TopAlert>setSlug");
	$template->ToolsManager_ToolDetails_TopAlert_setTitle		=	ws::getLang("ToolsManager>ToolDetails>TopAlert>setTitle");
	$template->ToolsManager_ToolDetails_TopAlert_setMySQL		=	ws::getLang("ToolsManager>ToolDetails>TopAlert>setMySQL");
	$template->ToolsManager_ToolDetails_modal_saving			=	ws::getLang("ToolsManager>ToolDetails>modal>saving");
	$template->ToolsManager_ToolDetails_modal_save_sucess		=	ws::getLang("ToolsManager>ToolDetails>modal>save>sucess");
	$template->ToolsManager_ToolDetails_AddHTML					=	ws::getLang("ToolsManager>ToolDetails>AddHTML");
	$template->ToolsManager_ToolDetails_MaxItem					=	ws::getLang("ToolsManager>ToolDetails>MaxItem");
	$template->ToolsManager_ToolDetails_groupOwned				=	ws::getLang("ToolsManager>ToolDetails>groupOwned");
	$template->ToolsManager_ToolDetails_rootGroup				=	ws::getLang("ToolsManager>ToolDetails>rootGroup");


	

	$template->max_item 										=	stripcslashes($FERRAMENTA['max_item']);
	$template->_js_ 											=	stripcslashes($FERRAMENTA['_js_']);
	$template->html_item										=	stripcslashes($FERRAMENTA['html_item']);
	$template->html_img											=	stripcslashes($FERRAMENTA['html_img']);
	$template->html_gal											=	stripcslashes($FERRAMENTA['html_gal']);
	$template->html_img_gal										=	stripcslashes($FERRAMENTA['html_img_gal']);
	$template->html_cat											=	stripcslashes($FERRAMENTA['html_cat']);
	$template->html_file										=	stripcslashes($FERRAMENTA['html_file']);


	$template->SLUG 											=	$FERRAMENTA['slug'];
	$template->_prefix_ 										=	$FERRAMENTA['_prefix_'];
	$template->_tit_menu_ 										=	$FERRAMENTA['_tit_menu_'];
	$template->_desc_ 											=	$FERRAMENTA['_desc_'];
	$template->checked1 										=	$FERRAMENTA['_niveis_']=="-1"		? "checked" : "";
	$template->checked2 										=	$FERRAMENTA['_niveis_']=="0"		? "checked" : "";
	$template->checked3 										=	$FERRAMENTA['_niveis_']=="1"		? "checked" : "";
	$template->_exec_js_1 										=	$FERRAMENTA['_exec_js_']=="osdois" 	? "checked" :"" ;
	$template->_exec_js_2 										=	$FERRAMENTA['_exec_js_']=="abrir" 	? "checked" :"" ;
	$template->_exec_js_3 										=	$FERRAMENTA['_exec_js_']=="salvar" 	? "checked" :"" ;
	$template->_exec_js_4 										=	$FERRAMENTA['_exec_js_']=="nada" 	? "checked" :"" ;
	$template->_plugin_ 										=	$FERRAMENTA['_plugin_'] =="1" 		? "checked" :"" ;

	#####################################################  
	# VERIFICAMOS SE EXISTE UM LINK DE RETORNO SETADO 
	#####################################################
	$goback = 0;
	if(isset($_GET['goback'])){
		$template->GOBACK_ENCODE 	= urlencode($_GET['goback']);
		$template->goback 			= $_GET['goback'];
		$template->block("GOBACK");
	}else{
		$template->clear("GOBACK");
		$template->block("VOLTAR");
	}

	#####################################################  
	# SELECIONAMOS OS CAMPOS DA FERRAMENTA 
	#####################################################
	$detalhes		= 	new MySQL();
	$detalhes->set_table(PREFIX_TABLES.$SLUG.'_campos');
	$detalhes->set_order('posicao','ASC');
//	$detalhes->set_colum('coluna_mysql,listaTabela');
	$detalhes->set_where('coluna_mysql<>""');
	$detalhes->set_where('AND listaTabela<>""');
	$detalhes->set_where('AND ws_id_ferramenta="'.ws_id_ferramenta.'"');
	$detalhes->debug(0);
	$detalhes->distinct();
	$detalhes->select();


	#####################################################  
	# SELECIONAMOS OS CAMPOS DA FERRAMENTA 
	#####################################################
	$groups		= 	new MySQL();
	$groups->set_table(PREFIX_TABLES.'ws_path_tools');
	$groups->set_order('posicao','ASC');
	$groups->select();
	$groups_num_rows= 	new MySQL();
	$groups_num_rows->set_table(PREFIX_TABLES.'ws_link_path_tools');
	$groups_num_rows->set_where('id_tool="'.ws_id_ferramenta.'" AND id_path="0"');
	$groups_num_rows->select();
	if($groups_num_rows->_num_rows>0){
		$template->checked_0='checked="checked"';
	}else{
		$template->checked_0='';
	}

	foreach($groups->fetch_array as $group){
			$groups_num_rows= 	new MySQL();
			$groups_num_rows->set_table(PREFIX_TABLES.'ws_link_path_tools');
			$groups_num_rows->set_where('id_tool="'.ws_id_ferramenta.'" AND id_path="'.$group['id'].'"');
			$groups_num_rows->select();
			if($groups_num_rows->_num_rows>0){
				$template->checked='checked="checked"';
			}else{
				$template->checked='';
			}


			$template->id=$group['id'];
			$template->GRUPO_NAME=$group['path_name'];
			$template->block('LIST_GROUP');


		
	}





	#########################################################################  
	# VARREMOS OS CAMPOS, E RETORNAMOS O TEMPLATE COM OS CAMPOS SELECIONADOS 
	#########################################################################
	$camposAutoComplete = array();
	foreach($detalhes->fetch_array as $return){
		$teste = explode(',',$FERRAMENTA['det_listagem_item']);
		$return['selectmulti']		=	in_array($return['coluna_mysql'],$teste) ? "selected" : "";
		$template->coluna_mysql		=	$return['coluna_mysql'];
		$template->selectmulti		=	$return['selectmulti'];
		$template->listaTabela 		=	$return['listaTabela'];
		$camposAutoComplete[] 		=  '{value: "{{{_}'.substr($return['coluna_mysql'],strlen($FERRAMENTA['_prefix_'])).'}}", meta:"Ferramenta"}' ;
		$template->block('OPT_LIST_TABLE');
	}

		$camposAutoComplete[] 		=  '{value: "{{{_}img_count}}",meta:"Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}titulo}}",meta:"Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}texto}}",meta:"Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}url}}",meta:"Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}avatar}}",meta:"Galerias"}' ;

		$camposAutoComplete[] 		=  '{value: "{{{_}titulo}}",meta:"Imagens Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}texto}}",meta:"Imagens Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}url}}",meta:"Imagens Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}token}}",meta:"Imagens Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}filename}}",meta:"Imagens Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}posicao}}",meta:"Imagens Galerias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}file}}",meta:"<img> Imagens Galerias"}' ;


		$camposAutoComplete[] 		=  '{value: "{{{_}titulo}}",meta:"Imagens"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}texto}}",meta:"Imagens"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}url}}",meta:"Imagens"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}filename}}",meta:"Imagens"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}token}}",meta:"Imagens"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}imagem}}",meta:"Imagens"}' ;

		$camposAutoComplete[] 		=  '{value: "{{{_}titulo}}",meta:"Categorias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}texto}}",meta:"Categorias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}avatar}}",meta:"Categorias"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}token}}",meta:"Categorias"}' ;

		$camposAutoComplete[] 		=  '{value: "{{{_}posicao}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}uploaded}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}titulo}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}url}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}texto}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}file}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}filename}}",meta:"Files"}' ;
		$camposAutoComplete[] 		=  '{value: "{{{_}token}}",meta:"Files"}' ;





	$template->coluna_mysql_auto_complete		= implode($camposAutoComplete, ',');
	$template->block('CAMPOSTOOL');
	

	#########################################################################  
	# FINALIZA ARQUIVO, SELECIONA O BLOCO E RETORNA O HTML 
	#########################################################################
	$template->block('DETALHES');
	$template->show();