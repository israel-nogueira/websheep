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

##################################################################################
# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
##################################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

##################################################################################
# INICIA SESSÃO
##################################################################################
	_session();

##################################################################################
# DELETA O CACHE INTERNO E CRIA UM RASCUNHO DAS GALERIAS
##################################################################################
	clearstatcache();
	criaRascunho($_GET['ws_id_ferramenta'],$_GET['id_item'],true);

##################################################################################
# DEFINE VARIAVEIS GET 
##################################################################################
	if(empty($_GET['id_cat'])){
		$_GET['id_cat']=0;
	}

##################################################################################
# DEFINE AS VARIAVEIS 
##################################################################################	
	@define("ID_CAT"			,		$_GET['id_cat']);
	@define("ID_ITEM"			,		$_GET['id_item']);
	@define("TOKEN_GROUP"		,		$_GET['token_group']);

#####################################################  
# CAPTAMOS O SLUG DA FERRAMENTA 
####################################################
	$_FERRAMENTA_ = new MySQL();
	$_FERRAMENTA_->set_table(PREFIX_TABLES.'ws_ferramentas');
	$_FERRAMENTA_->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$_FERRAMENTA_->select();
	$_FERRAMENTA_ 	= @$_FERRAMENTA_->fetch_array[0];	
	$_SLUG_ 		= 	$_FERRAMENTA_['slug'];
 

##################################################################################
# INVOCA A CLASSE DO TEMPLATE
##################################################################################
	$_SET_TEMPLATE_INPUT = new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-galerias-template.html', true);
	if(isset($_GET['back']) && $_GET['back']=='true'){ 
		$_SET_TEMPLATE_INPUT->block('BOT_BACK');
	}

	$_SET_TEMPLATE_INPUT->ws_rootPath 			= ws::rootPath;
	$_SET_TEMPLATE_INPUT->TOKEN_GROUP 			= TOKEN_GROUP;
	$_SET_TEMPLATE_INPUT->_TITULO_FERRAMENTA_ 	= "Galerias de fotos";//$_SESSION['_TITULO_FERRAMENTA_'];
	$_SET_TEMPLATE_INPUT->PATCH 				= 'app/ws-modules/ws-model-tool';
	$_SET_TEMPLATE_INPUT->ID_ITEM 				= ID_ITEM;
	$_SET_TEMPLATE_INPUT->WS_ID_FERRAMENTA 		= $_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->BACK 					= $_GET['back'];


##########################################################################################################
# VERIFICA SE JÁ TEM RASCUNHO
##########################################################################################################
	$draft				= new MySQL();
	$draft->set_table(PREFIX_TABLES.$_SLUG_."_item");
	$draft->set_where('ws_draft="1"');
	$draft->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	$draft->select();

	####################################################################################
	# VERIFICAMOS SE EXISTE ALGUM ÍTEM COM A POSIÇÃO ZERO
	####################################################################################
	$verifica_order_original = new MySQL();
	$verifica_order_original->set_table(PREFIX_TABLES . $_SLUG_."_gal ");
	$verifica_order_original->set_where('posicao="0"');
	$verifica_order_original->set_where(' AND ws_draft="0" ');
	$verifica_order_original->set_colum('id');
	$verifica_order_original->select();

	####################################################################################
	# SE SIM, REORDENAMOS A TABELA INTEIRA
	####################################################################################
	if($verifica_order_original->_num_rows>0){
		$Ordena = new MySQL();
		$Ordena->select('SET @pos:=0;');
		$Ordena->select('UPDATE '.PREFIX_TABLES.$_SLUG_.'_gal SET posicao=( SELECT @pos := @pos + 1 ) WHERE(ws_draft=0) ORDER BY posicao ASC;');
	}
	####################################################################################
	# VERIFICAMOS SE EXISTE ALGUM ÍTEM COM A POSIÇÃO ZERO
	####################################################################################
	$verifica_order_rascunho = new MySQL();
	$verifica_order_rascunho->set_table(PREFIX_TABLES . $_SLUG_."_gal ");
	$verifica_order_rascunho->set_where('posicao="0"');
	$verifica_order_rascunho->set_where(' AND ws_draft="1" ');
	$verifica_order_rascunho->set_colum('id');
	$verifica_order_rascunho->select();

	####################################################################################
	# SE SIM, REORDENAMOS A TABELA INTEIRA
	####################################################################################
	if($verifica_order_rascunho->_num_rows>0){
		$Ordena = new MySQL();
		$Ordena->select('SET @pos:=0;');
		$Ordena->select('UPDATE '.PREFIX_TABLES.$_SLUG_.'_gal SET posicao=( SELECT @pos := @pos + 1 ) WHERE(ws_draft=1) ORDER BY posicao ASC;');
	}





	$s 					= new MySQL();
	$s->set_table(PREFIX_TABLES.$_SLUG_.'_gal');
	$s->set_order('posicao','ASC');
	$s->set_where('id_item="'.$_GET['id_item'].'"');
	$s->set_where('OR ws_id_draft="'.$_GET['id_item'].'"');
	$_SET_TEMPLATE_INPUT->IF_ORIGINAL 	=  'false';

	$s->select();

##########################################################################################################
# VERIFICA SE JÁ TEM RASCUNHO
##########################################################################################################
	foreach($s->fetch_array as $img){ 
		$_SET_TEMPLATE_INPUT->DRAFT 	= $img['ws_draft'];
		$_SET_TEMPLATE_INPUT->POSICAO 	= $img['posicao'];

		if($img['ws_draft']=='1'){
			$_SET_TEMPLATE_INPUT->ID_GAL 	= $img['ws_id_draft'];
		}else{
			$_SET_TEMPLATE_INPUT->ID_GAL 	= $img['id'];
		}


		$_SET_TEMPLATE_INPUT->AVATAR	= ($img['avatar']=="") ? 'indefinida.png' : $img['avatar'].'?'._crypt();
		$_SET_TEMPLATE_INPUT->PATCH 	= 'app/ws-modules/ws-model-tool';
		$_SET_TEMPLATE_INPUT->ID_ITEM 	= $_GET['id_item'];
		$_SET_TEMPLATE_INPUT->block("BLOCK_GALERIA");
	 }

##################################################################################
# PRINTAMOS O RESULTADO FINAL NA TELA
##################################################################################

		$_SET_TEMPLATE_INPUT->block("GALERIAS");
		$_SET_TEMPLATE_INPUT->show();		
