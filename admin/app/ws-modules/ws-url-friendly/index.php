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
	define("PATH", 'app/ws-modules/ws-url-friendly');
		
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
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-url-friendly/ws-tool-index.html');
	
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

	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           		= new Template(TEMPLATE_LINK, true);

	$template->urlFriendly_title 					= ws::getLang("urlFriendly>title");
	$template->urlFriendly_configurationData 		= ws::getLang("urlFriendly>configurationData");
	$template->urlFriendly_addaURL 					= ws::getLang("urlFriendly>addaURL");
	$template->urlFriendly_modal_Adding 			= ws::getLang("urlFriendly>modal>Adding");
	$template->urlFriendly_modal_save_contentLine1 	= ws::getLang("urlFriendly>modal>save>contentLine1");
	$template->urlFriendly_modal_save_contentLine2 	= ws::getLang("urlFriendly>modal>save>contentLine2");
	$template->urlFriendly_modal_save_button1 		= ws::getLang("urlFriendly>modal>save>button1");
	$template->urlFriendly_modal_save_button2 		= ws::getLang("urlFriendly>modal>save>button2");
	$template->urlFriendly_modal_rearrangingItems 	= ws::getLang("urlFriendly>modal>rearrangingItems");
	$template->urlFriendly_topAlert_save_sucess 	= ws::getLang("urlFriendly>topAlert>save_sucess");
	$template->urlFriendly_modal_delete_content 	= ws::getLang("urlFriendly>modal>delete>content");
	$template->urlFriendly_modal_delete_button1 	= ws::getLang("urlFriendly>modal>delete>button1");
	$template->urlFriendly_modal_delete_button2 	= ws::getLang("urlFriendly>modal>delete>button2");


	#####################################################  
	# PESQUISAMOS NA BASE OS REGISTROS DE URL'S CUSTOM 
	#####################################################
	$includes = new MySQL();
	$includes->set_table(PREFIX_TABLES."ws_pages");
	$includes->set_where("type='custom'");
	$includes->select();

	#####################################################  
	# AGORA VARREMOS OS RESULTADOS 
	#####################################################
	foreach($includes->fetch_array as $item){
		$readonly 	= ($item['type']=='system' || $item['type']=='path') 	? 'readonly="readonly"' :"";
		$ClassOnly 	= ($item['type']=='system' || $item['type']=='path') 	? 'ClassOnly' :"";
		$close 		= ($item['type']=='system' || $item['type']=='path') 	? '' :'<div legenda="Excluir URL" class="excluir"></div>';
		$value 		= ($item['type']=='system') 							? $item['title'] :$item['file'];
		$template->LI_TYPE			= $item['type'];
		$template->LI_ID			= $item['id'];
		$template->LI_PATH			= $item['path'];
		$template->LI_VALUE			= $value;
		$template->LI_CLASS_ONLY	= $ClassOnly;
		$template->LI_READ_ONLY		= $readonly;
		$template->LI_CLOSE			= $close;
		$template->block("URLHTACCESS");
	}
	$template->PATH 			= PATH;

	#############################################################
	# FINALIZAMOS O ARQUIVO, SETAMOS O BLOCO E RETORNAMOS O HTML 
	#############################################################
	$template->block("URLAMIGAVEL");
	$template->show();
