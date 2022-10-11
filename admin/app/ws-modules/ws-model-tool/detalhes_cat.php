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

	#####################################################  FUNÇÕES DO MODULO
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

	#####################################################
	_session();
	clearstatcache();
	$cat_referencia 				=  $_GET['id_cat'];

	$_FERRAMENTA_ = new MySQL();
	$_FERRAMENTA_->set_table(PREFIX_TABLES.'ws_ferramentas');
	$_FERRAMENTA_->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$_FERRAMENTA_->select();
	$_FERRAMENTA_ 	= @$_FERRAMENTA_->fetch_array[0];	
	$_SLUG_ 		= 	$_FERRAMENTA_['slug'];


	#################################################################################  
	# INICIA CLASS TEMPLATE
	#################################################################################
	$_CATEGORIA = new MySQL();
	$_CATEGORIA->set_table(PREFIX_TABLES.$_SLUG_.'_cat');
	$_CATEGORIA->set_where('id="'.$cat_referencia.'"');
	$_CATEGORIA->select();
	$_CATEGORIA = $_CATEGORIA->obj[0];

	if(empty($_GET['LIMIT']))	{$_GET['LIMIT']="50";}
	if(empty($_GET['PAGE']))	{$_GET['PAGE']="1";}

	#################################################################################  
	# INICIA CLASS TEMPLATE
	#################################################################################
	if(empty($_GET['token_group'])) {
		$_GET['token_group'] = _token(PREFIX_TABLES."ws_biblioteca","token_group");
	}
	$_SET_TEMPLATE_INPUT 						= new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-category-datails-template.html', true);
	
	$_SET_TEMPLATE_INPUT->ws_rootPath 			= ws::rootPath;
	$_SET_TEMPLATE_INPUT->TOKEN_GROUP 			= $_GET['token_group'];
	$_SET_TEMPLATE_INPUT->WS_ID_FERRAMENTA 		= $_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->DESCRIPTION 			= stripslashes(urldecode($_CATEGORIA->texto));
	$_SET_TEMPLATE_INPUT->TITULO 				= $_CATEGORIA->titulo;
	$_SET_TEMPLATE_INPUT->ID_CAT 				= $_CATEGORIA->id;
	$_SET_TEMPLATE_INPUT->PATH 					= 'app/ws-modules/ws-model-tool';
	$_SET_TEMPLATE_INPUT->CAT_AVATAR 			= $_CATEGORIA->avatar;
	$_SET_TEMPLATE_INPUT->CAT_PAI 				= $_CATEGORIA->id_cat;
	$_SET_TEMPLATE_INPUT->PAGE 					= $_GET['PAGE'];
	$_SET_TEMPLATE_INPUT->LIMIT  				= $_GET['LIMIT'];

	$listIDCat 									= 	array();
	$listCat 									= 	array();

	#################################################################################  
	# FUNÇÃO foreachCat  É A FUNÇÃO RECURSIVA ONDE PEGAMOS AS CATEGORIAS
	#################################################################################$listIDCat 						= 	array();
function foreachCat ($cat){
		global $_SET_TEMPLATE_INPUT;
		global $cat_referencia;
		global $listCat;
		global $listIDCat;
		global $_SLUG_;
		$cat_foreach						= 	new MySQL();
		$cat_foreach->set_table(PREFIX_TABLES.$_SLUG_.'_cat');
		$cat_foreach->set_order('titulo ASC');
		$cat_foreach->set_where('id_cat="'.$cat.'"');
		$cat_foreach->set_where('AND ws_id_ferramenta="'.$_GET['ws_id_ferramenta'].'"');
		$cat_foreach->select();
		foreach($cat_foreach->fetch_array as $item){
			$select="";
			if($cat_referencia==$item['id_cat']){
			    $select="selected";
			    $listIDCat[] = $item['id'];
			}
			if( $item['id']==$cat_referencia){
					$listIDCat[] = $item['id'];
			}
			if(in_array($item['id_cat'],$listIDCat)){
				$_SET_TEMPLATE_INPUT->DISABLED 	= 'disabled';
					$listIDCat[] = $item['id'];

				if( $item['id']==$cat_referencia){
						$listIDCat[] = $item['id'];
				}
			}else{
				$_SET_TEMPLATE_INPUT->clear("DISABLED");
			}
			$listCat[] = $item['titulo'];
			$_SET_TEMPLATE_INPUT->IDCAT 	= $item['id'];
			$_SET_TEMPLATE_INPUT->SELECT 	= $select;
			$_SET_TEMPLATE_INPUT->LABEL 	= implode($listCat,' > ');
			$_SET_TEMPLATE_INPUT->block("OPT_CAT");
			foreachCat($item['id']);

			$listCat = $listIDCat =array();
		}
	}
	#################################################################################  
	# LISTAMOS AGORA NO COMBOBOX AS CATEGORIAS
	#################################################################################
		 foreachCat(0);
	#################################################################################  
	# PRINTA O HTML MONTADO
	#################################################################################
		$_SET_TEMPLATE_INPUT->block('CAT_DETAILS');
		$_SET_TEMPLATE_INPUT->show();


?>