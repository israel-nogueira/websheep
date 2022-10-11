<?
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
	# SETAMOS O PATH DO MÓDULO
	#####################################################
	define("PATH", 'app/ws-modules/ws-model-tool');

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
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-meta-tags.html');

	#####################################################  
	# SELECIONA A PÁGINA COM O ID MANDADO VIA GET 
	#####################################################
	$ws_pages					= new MySQL();
	$ws_pages->set_table(PREFIX_TABLES.'ws_pages');
	$ws_pages->set_where('id="'.@$_GET['id_page'].'"');
	$ws_pages->set_limit(1);
	$ws_pages->select();

	##########################################################  
	# CASO NÃO TENHA PÁGINA COM ESSE ID, GERA TÍTULO PADRÃO
	##########################################################
	$pageNumrow = $ws_pages->_num_rows;
	if($pageNumrow>0){
		$pageName = $ws_pages->fetch_array[0]['title'];
	}else{
		$pageName = "Default";
	}

	##########################################################  
	# DAMOS INICIO A CLASSE TEMPLATE
	##########################################################
	$_SET_TEMPLATE_INPUT = new Template(TEMPLATE_LINK, true);
	$_SET_TEMPLATE_INPUT->PAGE_NAME 		= $pageName;
	$_SET_TEMPLATE_INPUT->ID_PAGE			= $_GET['id_page'];
	$_SET_TEMPLATE_INPUT->PATH				= PATH;

	################################################################################  
	# CASO SEJA UMA PÁGINA 0, OU SEJA GENÉRICA SELECIONA O PADRÃO DO SISTEMA
	################################################################################
	if($_GET['id_page']=='0'){
		$insert_categorias=new MySQL();
		$insert_categorias->set_table(PREFIX_TABLES.'setupdata');
		$insert_categorias->set_where('id="1"');
		$insert_categorias->Select();
		$titulo_page = str_replace('"','&quot;',$insert_categorias->fetch_array[0]['title_root']);
	}else{
		$insert_categorias=new MySQL();
		$insert_categorias->set_table(PREFIX_TABLES.'ws_pages');
		$insert_categorias->set_where('id="'.$_GET['id_page'].'"');
		$insert_categorias->Select();
		$titulo_page = 	str_replace('"','&quot;',$insert_categorias->fetch_array[0]['title_page']);
	}

	################################################################################  
	# TÍTULO DA PÁGINA
	################################################################################
	$_SET_TEMPLATE_INPUT->TITLE_PAGE		= $titulo_page;

	################################################################################  
	# SELECIONA A TABELA E FOREACH NA TABELA RETORNANDO AS METATAGS
	################################################################################
		$ws_pages					= new MySQL();
		$ws_pages->set_table(PREFIX_TABLES.'meta_tags');
		$ws_pages->set_where('id_page="'.$_GET['id_page'].'"');
		$ws_pages->set_order('id',"DESC");
		$ws_pages->select();
		foreach ($ws_pages->fetch_array as $value) {
			$_SET_TEMPLATE_INPUT->LI_TYPE       	= $value['type'];
			$_SET_TEMPLATE_INPUT->LI_TYPE_CONTENT	= $value['type_content'];
			$_SET_TEMPLATE_INPUT->LI_CONTENT		= $value['content'];
			$_SET_TEMPLATE_INPUT->LI_ID				= $value['id'];
			$_SET_TEMPLATE_INPUT->block("LIMETA");
		}

	################################################################################  
	# FINALMENTE MOSTRA O HTML MONTADO
	################################################################################
	$_SET_TEMPLATE_INPUT->block("METATAGS");
	$_SET_TEMPLATE_INPUT->show();
