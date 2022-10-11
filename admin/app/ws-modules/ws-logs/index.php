<?php
	ob_start();

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}
	############################################################################################################################################

	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	_session();
	clearstatcache();

	if(empty($_GET['LIMIT']))	{$_GET['LIMIT']=30;}
	if(empty($_GET['PAGE']))	{$_GET['PAGE']=1;}

	ob_end_clean();

	define("PATH", 'app/ws-modules/ws-logs');

	$_SET_TEMPLATE_INPUT                								= new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-logs/ws-log.html', true);
	$_SET_TEMPLATE_INPUT->PATH          								= PATH;
	// $_SET_TEMPLATE_INPUT->ID_CAT        								= 0;
	// $_SET_TEMPLATE_INPUT->ID_FERRAMENTA 								= ID_FERRAMENTA;
	// $_SET_TEMPLATE_INPUT->WS_NIVEL      								= WS_NIVEL;
	// $_SET_TEMPLATE_INPUT->TOKEN_GROUP   								= @$_GET['token_group'];
	// $_SET_TEMPLATE_INPUT->modulo_itens_buttons_back 					= ws::getLang("modulo>itens>buttons>back");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_back_content 			= ws::getLang("modulo>itens>modal>back>content");
	// $_SET_TEMPLATE_INPUT->modulo_itens_buttons_category 				= ws::getLang("modulo>itens>buttons>category");
	// $_SET_TEMPLATE_INPUT->modulo_itens_buttons_addItem 				= ws::getLang("modulo>itens>buttons>addItem");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_loading_content 		= ws::getLang("modulo>itens>modal>loading>content");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_addItem_content 		= ws::getLang("modulo>itens>modal>addItem>content");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_content 			= ws::getLang("modulo>itens>modal>delete>content");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_bot1 			= ws::getLang("modulo>itens>modal>delete>bot1");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_bot2 			= ws::getLang("modulo>itens>modal>delete>bot2");
	// $_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_beforeContent	= ws::getLang("modulo>itens>modal>delete>beforeContent");
	// $_SET_TEMPLATE_INPUT->modulo_itens_topAlert_sucessDelete 		= ws::getLang("modulo>itens>topAlert>sucessDelete");
	$_SET_TEMPLATE_INPUT->TITULO_TOOL   								= "LOG's";
	$_SET_TEMPLATE_INPUT->LIMIT 										= $_GET['LIMIT'];
	$_SET_TEMPLATE_INPUT->PAGE 											= $_GET['PAGE'];
	$_SET_TEMPLATE_INPUT->LIMIT											= $_GET['LIMIT'];

	########################################################################
	# LIMIT DOS RESULTADOS
	########################################################################
		$_atual_page_ = $_GET['PAGE'];
		$_max_posts_  = $_GET['LIMIT'];
		$_set_limit   = ($_atual_page_ * $_max_posts_) - $_max_posts_;
		if ($_atual_page_ == 0 || $_atual_page_ == 1) {
			$_set_limit = $_max_posts_;
		} else {
			$_set_limit = $_set_limit . ',' . $_max_posts_;
		}

		$itens = new MySQL();
		$itens->set_table(PREFIX_TABLES . "ws_log ");
		$itens->set_limit($_set_limit);


		########################################################################
		# PAGINAÇÃO
		########################################################################

			$totalResults = new MySQL();
			$totalResults->set_table(PREFIX_TABLES . "ws_log ");
			$totalResults->set_colum('id');
			$totalResults->select();
			$_total_posts_ 		= $totalResults->_num_rows;
			$_total_paginas_	= @ceil($_total_posts_/$_max_posts_); 
			for($i=1; $i<=$_total_paginas_;++$i){
				$_SET_TEMPLATE_INPUT->COUNT = $i;
				$_SET_TEMPLATE_INPUT->ACTIVE = ($i==$_GET['PAGE'])? "active":"";
				$_SET_TEMPLATE_INPUT->block("COUNTPAGE");
			}


	########################################################################
	# LIKE SEARCH 
	########################################################################

	if(isset($_GET['keywork'])){
		$campo = new MySQL();
		$campo->set_table(PREFIX_TABLES . "_model_campos");
		$campo->set_where('ws_id_ferramenta="'.$_GET['ws_id_ferramenta']. '"');
		$campo->select();
		foreach ($campo->fetch_array as $item) {
			if($item['coluna_mysql']!=""){
				$itens->like($item['coluna_mysql'],'%'.strtolower(trim($_GET['keywork'])).'%');
			}
		}
	}



	########################################################################
	# INICIAMOS O FOREACH PARA EXIBIR O RESULTADO 
	########################################################################
	$itens->select();
	foreach ($itens->fetch_array as $item) {
		$_SET_TEMPLATE_INPUT->td_type	=	$item['type'];
		$_SET_TEMPLATE_INPUT->td_class	= 	$item['type'];	


		if($item['type']=="warn"){	$_SET_TEMPLATE_INPUT->ICON='fa fa-warning';};
		if($item['type']=="error"){	$_SET_TEMPLATE_INPUT->ICON='fa fa-times-circle';};
		if($item['type']=="log"){	$_SET_TEMPLATE_INPUT->ICON='fa fa-times-circle';};
		if($item['type']=="info"){	$_SET_TEMPLATE_INPUT->ICON='fa fa-info-circle';};



		$_SET_TEMPLATE_INPUT->td_file	=	str_replace(ws::protocolURL().DOMINIO,"",$item['url']);
		$_SET_TEMPLATE_INPUT->td_line	=	$item['linha'];
		$_SET_TEMPLATE_INPUT->td_msn	=	$item['mensagem'];
		$_SET_TEMPLATE_INPUT->block("TR_ITEM");		
	}
	$_SET_TEMPLATE_INPUT->block("ITEM_LIST");
	$_SET_TEMPLATE_INPUT->show();
?>
