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
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	#####################################################	
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

	#####################################################  
	# INICIA SESSÃO
	#####################################################
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();

	#####################################################  
	# PUXA DA BASE OS DADOS DO DOMINDIO
	#####################################################
	$domain					= new MySQL();
	$domain->set_table(PREFIX_TABLES.'setupdata');
	$domain->set_order('id','DESC');
	$domain->set_limit(1);
	$domain->select();

	#####################################################  
	# LIMPA CACHE INTERNO
	#####################################################	
	clearstatcache();

	#####################################################  
	# IMPORTA O LINK DO TEMPLATE 
	#####################################################  
	$_TEMPLATE            		= new Template(INCLUDE_PATH.'admin/app/templates/html/ws-module-domain-template.html', true);
	$_TEMPLATE->ROOT_WEBSHEEP 	= ROOT_WEBSHEEP;

	#####################################################  
	# PRIMEIRO DEFINIMOS OS DADOS DA BASE 
	#####################################################

	// $_TEMPLATE->ws_rootPath 		= ws::rootPath;
	$_TEMPLATE->hd 					= $domain->fetch_array[0]['hd'];
	$_TEMPLATE->smtp_host 			= $domain->fetch_array[0]['smtp_host'];
	$_TEMPLATE->smtp_port 			= $domain->fetch_array[0]['smtp_port'];
	$_TEMPLATE->smtp_auth 			= ($domain->fetch_array[0]['smtp_auth']==1) 		? 'checked="checked"' :'';
	$_TEMPLATE->smtp_secure 		= $domain->fetch_array[0]['smtp_secure'];
	$_TEMPLATE->smtp_email 			= $domain->fetch_array[0]['smtp_email'];
	$_TEMPLATE->smtp_senha 			= $domain->fetch_array[0]['smtp_senha'];

	$_TEMPLATE->domain_status 		= ($domain->fetch_array[0]['domain_status']==1) 	? 'checked="checked"' :'';
	$_TEMPLATE->congelaFull 		= ($domain->fetch_array[0]['congelaFull']==1) 		? 'checked="checked"' :'';
	$_TEMPLATE->ws_cache 			= ($domain->fetch_array[0]['ws_cache']==1)		 	? 'checked="checked"' :'';

	$_TEMPLATE->url_congelamento 	= $domain->fetch_array[0]['url_congelamento'];
	$_TEMPLATE->auto_save 			= $domain->fetch_array[0]['auto_save'];
	$_TEMPLATE->splash_img 			= $domain->fetch_array[0]['splash_img'];

	#####################################################  
	# AGORA DEFINIMOS OS TEXTOS PADRÕES 
	#####################################################
	$_TEMPLATE->domainPanel_Password				= ws::getLang("domainPanel>Password");
	$_TEMPLATE->domainPanel_HDspace					= ws::getLang("domainPanel>HDspace");
	$_TEMPLATE->domainPanel_WsLang					= ws::getLang("domainPanel>WsLang");
	$_TEMPLATE->domainPanel_Port					= ws::getLang("domainPanel>Port");
	$_TEMPLATE->domainPanel_AccountDetails			= ws::getLang("domainPanel>AccountDetails");
	$_TEMPLATE->domainPanel_Server					= ws::getLang("domainPanel>Server");
	$_TEMPLATE->domainPanel_DefaultEmail			= ws::getLang("domainPanel>DefaultEmail");
	$_TEMPLATE->domainPanel_RequiresAuth			= ws::getLang("domainPanel>RequiresAuth");
	$_TEMPLATE->domainPanel_DomainStatus			= ws::getLang("domainPanel>DomainStatus");
	$_TEMPLATE->domainPanel_ActiveDomain			= ws::getLang("domainPanel>ActiveDomain");
	$_TEMPLATE->domainPanel_FreezeAllPages			= ws::getLang("domainPanel>FreezeAllPages");
	$_TEMPLATE->domainPanel_FreezingURL				= ws::getLang("domainPanel>FreezingURL");
	$_TEMPLATE->domainPanel_AutoSave				= ws::getLang("domainPanel>AutoSave");
	$_TEMPLATE->domainPanel_ForceFileCache			= ws::getLang("domainPanel>ForceFileCache");
	$_TEMPLATE->domainPanel_DefaultMetaTags			= ws::getLang("domainPanel>DefaultMetaTags");
	$_TEMPLATE->domainPanel_ConfigureMetaTags		= ws::getLang("domainPanel>ConfigureMetaTags");
	$_TEMPLATE->domainPanel_SplashScreen			= ws::getLang("domainPanel>SplashScreen");
	$_TEMPLATE->domainPanel_UpdateData				= ws::getLang("domainPanel>UpdateData");
	$_TEMPLATE->domainPanel_modal_BeforeProceed		= ws::getLang("domainPanel>modal>BeforeProceed");
	$_TEMPLATE->domainPanel_modal_Prepare			= ws::getLang("domainPanel>modal>Prepare");
	$_TEMPLATE->domainPanel_modal_Saving			= ws::getLang("domainPanel>modal>Saving");
	$_TEMPLATE->domainPanel_modal_Checking			= ws::getLang("domainPanel>modal>Checking");
	$_TEMPLATE->domainPanel_modal_SeparatingFiles	= ws::getLang("domainPanel>modal>SeparatingFiles");
	$_TEMPLATE->domainPanel_modal_CompactingFiles	= ws::getLang("domainPanel>modal>CompactingFiles");
	$_TEMPLATE->domainPanel_modal_JustALittleBit	= ws::getLang("domainPanel>modal>JustALittleBit");
	$_TEMPLATE->domainPanel_modal_TakingAWhile		= ws::getLang("domainPanel>modal>TakingAWhile");
	$_TEMPLATE->domainPanel_modal_WereAlmostThere	= ws::getLang("domainPanel>modal>WereAlmostThere");
	$_TEMPLATE->domainPanel_modal_Wait				= ws::getLang("domainPanel>modal>Wait");
	$_TEMPLATE->domainPanel_modal_OneMoment			= ws::getLang("domainPanel>modal>OneMoment");
	$_TEMPLATE->domainPanel_modal_FinalSettings		= ws::getLang("domainPanel>modal>FinalSettings");
	$_TEMPLATE->domainPanel_modal_Finishing			= ws::getLang("domainPanel>modal>Finishing");
	$_TEMPLATE->domainPanel_modal_ClosingFile		= ws::getLang("domainPanel>modal>ClosingFile");
	$_TEMPLATE->domainPanel_modal_CheckingForError	= ws::getLang("domainPanel>modal>CheckingForError");
	$_TEMPLATE->domainPanel_modal_EverythingOk		= ws::getLang("domainPanel>modal>EverythingOk");
	$_TEMPLATE->domainPanel_modal_AllocatingRecords	= ws::getLang("domainPanel>modal>AllocatingRecords");
	$_TEMPLATE->domainPanel_modal_PleaseWait		= ws::getLang("domainPanel>modal>PleaseWait");
	$_TEMPLATE->domainPanel_modal_Processing		= ws::getLang("domainPanel>modal>Processing");
	$_TEMPLATE->domainPanel_modal_WeFound			= ws::getLang("domainPanel>modal>WeFound");
	$_TEMPLATE->domainPanel_modal_DirectoriesAnd	= ws::getLang("domainPanel>modal>DirectoriesAnd");
	$_TEMPLATE->domainPanel_modal_FilesYourServer	= ws::getLang("domainPanel>modal>FilesYourServer");
	$_TEMPLATE->domainPanel_modal_CanWeStartCompac	= ws::getLang("domainPanel>modal>CanWeStartCompac");
	$_TEMPLATE->domainPanel_modal_Compact			= ws::getLang("domainPanel>modal>Compact");
	$_TEMPLATE->domainPanel_modal_Compacting		= ws::getLang("domainPanel>modal>Compacting");
	$_TEMPLATE->domainPanel_modal_Directories		= ws::getLang("domainPanel>modal>Directories");
	$_TEMPLATE->domainPanel_modal_Files				= ws::getLang("domainPanel>modal>Files");
	$_TEMPLATE->domainPanel_modal_FileWeight		= ws::getLang("domainPanel>modal>FileWeight");
	$_TEMPLATE->domainPanel_modal_Download			= ws::getLang("domainPanel>modal>Download");
	$_TEMPLATE->domainPanel_modal_Downloading		= ws::getLang("domainPanel>modal>Downloading");
	$_TEMPLATE->domainPanel_modal_BKPAlready		= ws::getLang("domainPanel>modal>BKPAlready");
	$_TEMPLATE->domainPanel_modal_GenerateNew		= ws::getLang("domainPanel>modal>GenerateNew");
	$_TEMPLATE->domainPanel_modal_UseTheSame		= ws::getLang("domainPanel>modal>UseTheSame");
	$_TEMPLATE->domainPanel_modal_ItemSavedSucces	= ws::getLang("domainPanel>modal>ItemSavedSucces");

	#####################################################  
	# CAPTAMOS AS TRADUÇÕES NO SERVIDOR 
	#####################################################
	$pasta = INCLUDE_PATH.'admin/app/config/lang';
	if(is_dir($pasta)){
		$dh = opendir($pasta);
		while($diretorio = readdir($dh)){
			if($diretorio != '..' && $diretorio != '.'){
				$lang = str_replace('.json',"",$diretorio);
				$_TEMPLATE->LANG = $lang;
				$_TEMPLATE->block("BLOCK_WS_LANG");
			}
		}
	}


	$_TEMPLATE->ATUAL_LANG = LANG;

	#####################################################  
	# RETIRNAMOS A STRING DO HTML 
	#####################################################
	$_TEMPLATE->block("BLOCK_DOMAIN");
	$_TEMPLATE->show();
