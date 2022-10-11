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
	# FORMATA O CAMINHO ROOT
	############################################################################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

	############################################################################################################  
	# DEFINE O PATH DO MÓDULO 
	############################################################################################################
	define("PATH", 'app/ws-modules/ws-password-protected-files');
		
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
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-password-protected-files/ws-key-file-indexs.html');

	############################################################################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	############################################################################################################
	$template           								= new Template(TEMPLATE_LINK, true);
	$template->PATH 									= PATH;
	$template->keyFile_index_tableColunms_nameFile 		= ws::getLang("keyFile>index>tableColunms>nameFile");
	$template->keyFile_index_tableColunms_type 			= ws::getLang("keyFile>index>tableColunms>type");
	$template->keyFile_index_tableColunms_size 			= ws::getLang("keyFile>index>tableColunms>size");
	$template->keyFile_index_tableColunms_uploadDate 	= ws::getLang("keyFile>index>tableColunms>uploadDate");
	$template->keyFile_index_tableColunms_link 			= ws::getLang("keyFile>index>tableColunms>link");
	$template->keyFile_index_modal_loading 				= ws::getLang("keyFile>index>modal>loading");
	$template->keyFile_index_mainTitle 					= ws::getLang("keyFile>index>mainTitle");
	$template->keyFile_index_edit 						= ws::getLang("keyFile>index>edit");
	$template->keyFile_index_amount 					= ws::getLang("keyFile>index>amount");

	############################################################################################################  
	# PESQUISAMOS NA BASE OS ARQUIVOS LIBERADOS PARA DOWNLOAD  
	############################################################################################################
	$files = new MySQL();
	$files->set_table(PREFIX_TABLES."ws_biblioteca");
	$files->set_where("download='1'");
	$files->set_where("AND tokenFile<>''");
	$files->set_Order("id","DESC");
	$files->select();
	$keys = array();

	############################################################################################################  
	# VARREMOS A BASE, E CASO NÃO ESTEJA NA ARRAY $keys, ADICIONAMOS O ARQUIVO  
	############################################################################################################
	$AllFiles = array();
	foreach ($files->fetch_array as $file) {
	//	if(!in_array($file['tokenFile'],$keys)){
	//		array_push($keys,$file['tokenFile']);
			$AllFiles[]= $file;
	//	}
	}

	############################################################################################################  
	# VARREMOS A ARRAY LISTADA ACIMA E ADICIONAMOS O ARQUIVO EM UMA ARRAY  
	############################################################################################################
	foreach ($AllFiles as $file) {
		$template->LI_FILENAME 		= $file['filename'];
		$template->LI_TYPE 			= $file['type'];
		$template->LI_SIZE 			= _filesize($file['upload_size']);
		$template->LI_SAVED 		= $file['saved'];
		$template->LI_ID 			= $file['id'];
		$template->LI_TOKEN_FILE 	= $file['tokenFile'];
		$template->block("TBODY");
	}

	############################################################################################################  
	# SETAMOS O BLOCO PRINCIPAL E RETORNAMOS O HTML   
	############################################################################################################
	$template->block("KEYFILE");
	$template->show();