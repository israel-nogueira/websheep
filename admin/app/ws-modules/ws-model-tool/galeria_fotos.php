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
	$session = new session();

##################################################################################
# DELETA O CACHE INTERNO E CRIA UM RASCUNHO DO ÍTEM
##################################################################################
	clearstatcache();
	criaRascunho($_GET['ws_id_ferramenta'],$_GET['id_item'],true);


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
# DEFINE AS VARIAVEIS 
##################################################################################	
	@define("WS_ID_FERRAMENTA"	,$_GET['ws_id_ferramenta']);
	@define("_ID_GALERIA_"		,$_GET['id_galeria']);
	@define("ID_ITEM"			,$_GET['id_item']);
	@define("BACK"				,$_GET['back']);
	@define("TOKEN_GROUP"		,$_GET['token_group']);
	@define("TITULO_FERRAMENTA"	,$session->get('_TITULO_FERRAMENTA_'));
	@define("PATCH"				,'app/ws-modules/ws-model-tool');









	####################################################################################
	# VERIFICAMOS SE EXISTE ALGUM ÍTEM COM A POSIÇÃO ZERO
	####################################################################################
	$verifica_order_original = new MySQL();
	$verifica_order_original->set_table(PREFIX_TABLES . $_SLUG_."_img_gal ");
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
		$Ordena->select('UPDATE '.PREFIX_TABLES.$_SLUG_.'_img_gal SET posicao=( SELECT @pos := @pos + 1 ) WHERE(ws_draft=0) ORDER BY posicao ASC;');
	}
	####################################################################################
	# VERIFICAMOS SE EXISTE ALGUM ÍTEM COM A POSIÇÃO ZERO
	####################################################################################
	$verifica_order_rascunho = new MySQL();
	$verifica_order_rascunho->set_table(PREFIX_TABLES . $_SLUG_."_img_gal ");
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
		$Ordena->select('UPDATE '.PREFIX_TABLES.$_SLUG_.'_img_gal SET posicao=( SELECT @pos := @pos + 1 ) WHERE(ws_draft=1) ORDER BY posicao ASC;');
	}



























##################################################################################
# PESQUISA NA BASE DE DADOS AS IMAGENS DA GALERIA
##################################################################################
	$S					= new MySQL();
	$S->set_table(PREFIX_TABLES .$_SLUG_. '_img_gal');
	$S->set_where('id_galeria="'._ID_GALERIA_.'"');
	$S->set_order('posicao','ASC');
	$S->select();	

##################################################################################
# INVOCA A CLASSE DO TEMPLATE
##################################################################################
	$_SET_TEMPLATE_INPUT = new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-galeria-fotos-template.html', true);

##################################################################################
# SETAMOS AS VARIAVEIS AO TEMPLATE
##################################################################################
	$_SET_TEMPLATE_INPUT->ws_rootPath 			= ws::rootPath;
	$_SET_TEMPLATE_INPUT->TOKEN_GROUP 			= TOKEN_GROUP;
	$_SET_TEMPLATE_INPUT->WS_ID_FERRAMENTA 		= WS_ID_FERRAMENTA;
	$_SET_TEMPLATE_INPUT->_TITULO_FERRAMENTA_ 	= TITULO_FERRAMENTA;
	$_SET_TEMPLATE_INPUT->PATCH 				= PATCH;
	$_SET_TEMPLATE_INPUT->ID_ITEM 				= ID_ITEM;
	$_SET_TEMPLATE_INPUT->_ID_GALERIA_ 			= _ID_GALERIA_;
	$_SET_TEMPLATE_INPUT->BACK 					= BACK;
	if(BACK=="false"){
		$_SET_TEMPLATE_INPUT->clear("BOTBACK");
	}else{
		$_SET_TEMPLATE_INPUT->block("BOTBACK");
	}
##################################################################################
# VARREMOS A BASE RETORNANDO O TEMPLATE DE CADA IMAGEM
##################################################################################
	foreach($S->fetch_array as $img){
		if($img['ws_draft']=='1'){
			$_SET_TEMPLATE_INPUT->CLASS_DRAFT="1";
		}else{
			$_SET_TEMPLATE_INPUT->CLASS_DRAFT="0";
		}
		$_SET_TEMPLATE_INPUT->ID_LI 	= $img['id'];
		$_SET_TEMPLATE_INPUT->FILE_LI 	= $img['file'];
		$_SET_TEMPLATE_INPUT->block("IMAGEM");
	}


##################################################################################
# PRINTAMOS O RESULTADO FINAL NA TELA
##################################################################################
	$_SET_TEMPLATE_INPUT->block("GALERIA_IMAGENS");
	$_SET_TEMPLATE_INPUT->show();


?>