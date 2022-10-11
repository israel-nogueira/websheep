<?php
#####################################################  controla o CACHE
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
	define("PATH", 'app/ws-modules/ws-model-tool');
	clearstatcache();
	
##################################################################################
# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
##################################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

##################################################################################
# INICIA SESSÃO
##################################################################################
	_session();

##################################################################################
# DELETA O CACHE INTERNO E CRIA UM RASCUNHO DO ÍTEM
##################################################################################
	clearstatcache();
	criaRascunho($_GET['ws_id_ferramenta'],$_GET['id_item']);

##################################################################################
# INVOCA A CLASSE DO TEMPLATE
##################################################################################
	$template = new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-files.html', true);
	
#####################################################  
# Ajusta variaveis GET
#####################################################
	if(empty($_GET['id_item']) || $_GET['id_item']==""){$_GET['id_item']=0;}
	if(empty($_GET['token_group']) || $_GET['token_group']=="" ) $_GET['token_group'] = _token(PREFIX_TABLES."ws_biblioteca","token_group");

#####################################################  
# CAPTAMOS O SLUG DA FERRAMENTA 
####################################################
	$_FERRAMENTA_ = new MySQL();
	$_FERRAMENTA_->set_table(PREFIX_TABLES.'ws_ferramentas');
	$_FERRAMENTA_->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$_FERRAMENTA_->select();
	$_FERRAMENTA_ 	= @$_FERRAMENTA_->fetch_array[0];	
	$_SLUG_ 		= 	$_FERRAMENTA_['slug'];
 
#####################################################  
# SETAMOS O ARQUIVO 
####################################################
	$template->ID_ITEM 			= $_GET['id_item'];
	$template->WS_NIVEL 		= $_GET['ws_nivel'];
	$template->WS_ID_FERRAMENTA = $_GET['ws_id_ferramenta'];
	$template->TOKEN_GROUP 		= $_GET['token_group'];
	$template->PATH 			= PATH;

	if(criaRascunho($_GET['ws_id_ferramenta'],$_GET['id_item'])){
		$template->block("TOP_ALERT_RASCUNHO");
	}

	if(empty($_GET['direct'])){ 
		$template->block("BOT_BACK");
	}

	$draft				= new MySQL();
	$draft->set_table(PREFIX_TABLES.$_SLUG_."_item");
	$draft->set_where('ws_draft="1"');
	$draft->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	$draft->select();

	$s 					= new MySQL();
	$s->set_table(PREFIX_TABLES.$_SLUG_.'_files');
	if((isset($_GET['original']) && $_GET['original']=='true')|| $draft->_num_rows==0){
		$s->set_where('id_item="'.$_GET['id_item'].'"');
	}else{
		$s->set_where('ws_draft="1"');
		$s->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	}
	$s->set_order('posicao','ASC');
	$s->select();
	foreach($s->fetch_array as $_files_){
			$template->LI_ID 	=	$_files_['id'];
			$template->NOME 	=	$_files_['filename'];
			$template->ARQUIVO 	=	$_files_['file'];
			$template->LI_TOKEN =	$_files_['token'];
			$template->PESO 	=	_filesize($_files_['size_file']);
			$template->block("BLOCK_FILES_ITEM");
	}

	$s 					= new MySQL();
	$s->set_table(PREFIX_TABLES."ws_ferramentas");
	$s->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$s->select();
	$template->EXTENCOES =	"'".str_replace(',',"','",$s->fetch_array[0]['_extencao_'])."'";
	$template->block("FILES");
	$template->show();
