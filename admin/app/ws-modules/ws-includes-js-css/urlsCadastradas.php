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

	############################################################################################################  
	# DEFINE O PATH DO MÓDULO 
	############################################################################################################
	define("PATH", 'app/ws-modules/ws-includes-js-css');
		
	############################################################################################################  
	# LIMPA O CACHE INTERNO
	############################################################################################################
	clearstatcache();
	
	############################################################################################################  
	# CONTROLA O CACHE
	############################################################################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	############################################################################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	############################################################################################################
	ob_start();
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	############################################################################################################  
	# CRIA SESSÃO
	############################################################################################################  
	_session();

	############################################################################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	############################################################################################################
	verifyUserLogin();
	
	############################################################################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	############################################################################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-includes-js-css/ws-tool-urls-js-css.html');

	############################################################################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	############################################################################################################
	$template           										= new Template(TEMPLATE_LINK, true);
	$template->PATH 											= 'app/ws-modules/ws-includes-js-css';
	$template->ID_PATH 											= $_GET['idPath'];
	$template->includeJsCss_includeFiles_moduleTitle 			= ws::getlang("includeJsCss>includeFiles>moduleTitle","{PATH_FILE}",$_GET['Path']);
	$template->includeJsCss_includeFiles_moduleTitle 			= ws::getlang("includeJsCss>includeFiles>moduleTitle");
	$template->includeJsCss_includeFiles_inFTP 					= ws::getlang("includeJsCss>includeFiles>inFTP");
	$template->includeJsCss_includeFiles_LinkedURL				= ws::getlang("includeJsCss>includeFiles>linkedURL");
	$template->includeJsCss_includeFiles_search					= ws::getlang("includeJsCss>includeFiles>search");
	$template->includeJsCss_includeFiles_modal_sortItem			= ws::getlang("includeJsCss>includeFiles>modal>sortItem");
	$template->includeJsCss_includeFiles_modal_addingFile		= ws::getlang("includeJsCss>includeFiles>modal>addingFile");
	$template->includeJsCss_includeFiles_modal_savingInput		= ws::getlang("includeJsCss>includeFiles>modal>savingInput");
	$template->includeJsCss_includeFiles_modal_deletingInput	= ws::getlang("includeJsCss>includeFiles>modal>deletingInput");

	############################################################################################################  
	# FUNÇÃO QUE VARRE RECURSIVAMENTE AS PASTAS 
	############################################################################################################
	function glob_recursive($pattern, $flags = 0){
		$files = glob($pattern, $flags);
		foreach(glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
		{
			$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
		}
		return $files;
	}

	############################################################################################################  
	# CRIAMOS 2 ARRAY QUE IRÃO RECEBER OS ARQUIVOS 
	############################################################################################################
	$ArquivosFTP          = Array();
	$ArquivosUsados       = Array();

	############################################################################################################  
	# AGORA INCLUÍMOS NELAS OS ARQUIVOS DO FTP 
	############################################################################################################
	$GetFilesFTP = glob_recursive(INCLUDE_PATH.'website/*.' . $_GET['type'], $flags = 0);
	foreach($GetFilesFTP as $value){
		$ArquivosFTP[] = str_replace(INCLUDE_PATH.'website', "", $value);
	}

	############################################################################################################  
	# SELECIONAMOS NA BASE DE DADOS AS URLS JÁ GRAVADAS 
	############################################################################################################
	$GetFilesLink         = new MySQL();
	$GetFilesLink->set_table(PREFIX_TABLES . 'ws_link_url_file');
	$GetFilesLink->set_where('id_url="' . $_GET['idPath'] . '"');
	$GetFilesLink->set_where('AND ext="' . $_GET['type'] . '"');
	$GetFilesLink->set_order('position', 'ASC');
	$GetFilesLink->select();

	############################################################################################################  
	# VARREMOS A BASE E RETORNAMOS OS ARQUIVOS GRAVADOS NA ARRAY 
	############################################################################################################
	foreach($GetFilesLink->fetch_array as $value){
		$ArquivosUsados[] = str_replace(INCLUDE_PATH.'website', "", $value['file']);
	}

	############################################################################################################  
	# VARREMOS A ARRAY COM OS ARQUIVOS DO FTP, SE ELE ESTIVER TAMBÉM NA ARRAY DA BASE RETORNA O HTML 
	############################################################################################################
	foreach($ArquivosFTP as $value)	{
		$hidden = "";
		$value  = str_replace(INCLUDE_PATH.'website', '', $value);
		$hidden = "";
		if(in_array($value, $ArquivosUsados)){
			$hidden = "opacity: 0.3;";
			$botInclude = "display:none;";
		}else{
			$botInclude = "display:block;";
		}
		$template->CLASS			= "insertFile";
		$template->ID				= $value;
		$template->FILE				= $value;
		$template->URL				= $_GET['idPath'];
		$template->DISPLAY			= $hidden;
		$template->CLASS_INCLUDE	= $botInclude;
		$template->block("GETFILE_FTP");
	}

	############################################################################################################  
	# VARREMOS O ARRAY DA BASE DE DADOS E TRAZEMOS OS ARQUIVOS CADASTRADOS JÁ 
	############################################################################################################
	foreach($GetFilesLink->fetch_array as $value){
		$hidden						= '';
		$value						= str_replace(INCLUDE_PATH.'website', '', $value);
		$template->ID				= $value['id'];
		$template->FILE				= $value['file'];
		$template->URL				= $value['id_url'];
		$template->DISPLAY			= 'block';
		$template->CLASS_INCLUDE	= 'display:none';
		$template->clear('CLASS');
		$template->block("GETFILE_BASIC");
	}

	############################################################################################################  
	# PUXAMOS O BLOCO CENTRAL DO TEMPLATE E RETORNAMOS O HTML
	############################################################################################################
	$template->block("URLS_CADASTRADAS");
	$template->show();