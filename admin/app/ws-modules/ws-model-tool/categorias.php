 <?
#################################################################################  
# LIMPA O CACHE INTERNO
#################################################################################
	clearstatcache();

############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

#################################################################################  
# INCLUI AS CLASSES  DO SISTEMA
#################################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

#################################################################################  
# INICIANDO A SESSÃO
#################################################################################
	$session = new session();

#################################################################################  
# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
#################################################################################
	verifyUserLogin();

#################################################################################  
# DEFINE O PATH QUE SERÁ USADO
#################################################################################
	define("PATH",'app/ws-modules/ws-model-tool');

#################################################################################  
# CAPTAMOS OS DADOS DA FERRAMENTA
#################################################################################
	$_FERRAMENTA_ = new MySQL();
	$_FERRAMENTA_->set_table(PREFIX_TABLES.'ws_ferramentas');
	$_FERRAMENTA_->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$_FERRAMENTA_->select();
	$_FERRAMENTA_ 	= @$_FERRAMENTA_->fetch_array[0];	
	$_SLUG_ 		= 	$_FERRAMENTA_['slug'];

	$_GET['token_group'] = (
		(empty($_GET['token_group'])) ? (
										 (
										 	empty($session->get('token_group'))) ? _token(PREFIX_TABLES."ws_biblioteca","token_group") : $session->get('token_group')
										 ) :$_GET['token_group']
										);
#################################################################################  
# CASO NÃO EXISTA UMA CATEGORIA VINCULADA OU NÍVEIS DEFINIDOS, FORÇAMOS O VALOR
#################################################################################
	if(empty($_GET['id_cat'])	)	{	$_GET['id_cat']			='0';			}
	if(empty($_GET['ws_nivel'])	)	{	$_GET['ws_nivel']		='0';			}
	if(empty($_GET['LIMIT']))		{	$_GET['LIMIT']			="50";			}
	if(empty($_GET['PAGE']))		{	$_GET['PAGE']			="1";			}

#################################################################################  
# PESQUISA MYSQL DAS CATEGORIAS
#################################################################################
	$_CATEGORIAS_ 					= new MySQL();
	$_CATEGORIAS_->set_table(PREFIX_TABLES.$_SLUG_.'_cat');
	$_CATEGORIAS_->set_where('ws_id_ferramenta="'.$_GET['ws_id_ferramenta'].'"');
	$_CATEGORIAS_->set_order('posicao',"ASC");
	$_CATEGORIAS_->debug(0);
	$_CATEGORIAS_->select();

#################################################################################  
# INICIA CLASS TEMPLATE
#################################################################################
	$_SET_TEMPLATE_INPUT                = new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-category-template.html', true);

#################################################################################  
# SETA DADOS BASICOS DAS CATEGORIAS
#################################################################################
	$_SET_TEMPLATE_INPUT->ws_rootPath   = ws::rootPath;
	$_SET_TEMPLATE_INPUT->ID_CAT       	= $_GET['id_cat'];
	$_SET_TEMPLATE_INPUT->WS_TOKENGROUP = $_GET['token_group'];
	$_SET_TEMPLATE_INPUT->PATH          = PATH;
	$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = $_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->TITLE  		= $_FERRAMENTA_['_tit_topo_'];
	$_GETVARS 							= $_GET;unset($_GETVARS['id_cat']);
	$_SET_TEMPLATE_INPUT->GETVARS  		= http_build_query($_GETVARS);

#################################################################################
#  BOTÃO PARA ADICIONAR CATEGORIAS 
#################################################################################
	$_SET_TEMPLATE_INPUT->block("BOT_ADD_GALERY");

#################################################################################
#  CASO TENHA LINK DE REFERENCIA 
#################################################################################
	$_SET_TEMPLATE_INPUT->LINK     = './'.PATH.'/itens.php?LIMIT='.$_GET['LIMIT'].'&PAGE='.$_GET['PAGE'].'&token_group='.$_GET['token_group'].'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->block("BOT_BACK");

	$_SET_TEMPLATE_INPUT->LIMIT 		= $_GET['LIMIT'];
	$_SET_TEMPLATE_INPUT->PAGE 			= $_GET['PAGE'];
#################################################################################
#  RETORNA AS CATEGORIAS 
#################################################################################
	if(isset($_CATEGORIAS_->_num_rows)){
		foreach($_CATEGORIAS_->fetch_array as $_cat_){
			$_SET_TEMPLATE_INPUT->LI_IMG 			= $_cat_['avatar']!='' ? ws::rootPath.'ws-img/0/42/100/'.$_cat_['avatar'] : ws::rootPath.'ws-img/42/42/100/avatar.png';
			$_SET_TEMPLATE_INPUT->LI_CAT 			= $_cat_['id'];
			$_SET_TEMPLATE_INPUT->LI_CAT_PAI	 	= $_GET['id_cat'];
			$_SET_TEMPLATE_INPUT->LI_TITULO 		= substr(strip_tags(urldecode($_cat_['titulo'])),0,100);
			$_SET_TEMPLATE_INPUT->LI_DESCRIPTION	= substr(strip_tags(urldecode($_cat_['texto'])),0,100);
			$_SET_TEMPLATE_INPUT->LI_GROUP	 		= $_GET['token_group'];
			$_SET_TEMPLATE_INPUT->block("ITEMCAT");
		}
	}

#################################################################################
#  RETORNA A STRING DO RESULTADO 
#################################################################################
$_SET_TEMPLATE_INPUT->block("BLOCK_CATEGORY");
$_SET_TEMPLATE_INPUT->show();
