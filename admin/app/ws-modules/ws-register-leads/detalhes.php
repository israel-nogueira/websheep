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
	define("PATH", 'app/ws-modules/ws-register-leads');
		
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
	$session = new session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# GERA O TOKEN GROUP PARA UPLOAD
	#####################################################
	if(empty($session->get('token_group'))){
		$session->set('token_group',_crypt());
	}
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_list_leads');
	$s->set_where('token="'.$_GET['lead'].'"');
	$s->select();
	$lead = $s->obj[0];

	$_COLUNAS_ = new MySQL();
	$_COLUNAS_->set_table(strtolower(PREFIX_TABLES.'wslead_'.$_GET['lead']));
	$_COLUNAS_->show_columns();

	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-register-leads/ws-tool-leads-detalhes.html');
	$template           				= new Template(TEMPLATE_LINK, true);

	#####################################################  
	# DEFINIMOS AS VARIÁVEIS NECESSÁRIAS PARA O MÓDULO 
	#####################################################
	$template->PATH 					= 'app/ws-modules/ws-register-leads';
	$template->DOMINIO 					= ws::protocolURL().DOMINIO;
	$template->ws_rootPath				= ws::rootPath;
	$template->lead_token			 	= strtolower($lead->token);
	$template->lead_title				= $lead->title;
	$template->lead_content			 	= $lead->content;
	$template->lead_SMTPSecure			= $lead->SMTPSecure;
	$template->lead_host				= $lead->host;
	$template->lead_resposta_ao_usuario = ($lead->resposta_ao_usuario 	==1)	? 'checked="true"': "";
	$template->lead_smtp_local 			= ($lead->smtp_local 			==1)	? 'checked="true"': "";
	$template->lead_server_ssl  		= ($lead->server_ssl 			==1)	? 'checked="true"': "";
	$template->lead_email_envio			= $lead->email_envio;
	$template->lead_pass			 	= $lead->pass;
	$template->lead_remetente_name		= $lead->remetente_name;
	$template->lead_remetente			= $lead->remetente;
	$template->lead_assunto			 	= $lead->assunto;
	$template->lead_assunto_clt			= $lead->assunto_clt;
	$template->lead_msng_resp_user		= $lead->msng_resp_user;
	$template->lead_url_sucess			= $lead->url_sucess;
	$template->lead_url_error			= $lead->url_error;
	$template->lead_header_email		= $lead->header_email;
	$template->lead_footer_email		= $lead->footer_email;
	$template->lead_camp_mail_clt		= $lead->camp_mail_clt;
	$template->lead_finalidade			= $lead->finalidade;
	$template->lead_msng_resp		 	= $lead->msng_resp;
	$template->lead_port		 		= $lead->port;
	$template->lead_id			 		= $lead->id;
	$template->token_group 				= ($session->get('token_group') != null && $session->get('token_group')!="" ) ? $session->get('token_group'):_crypt();
	
	#####################################################  
	# BLOCO DE TRADUÇÃO
	#####################################################
	// $template->ws_includePath				=	ws::includePath;
	$template->Leads_Details_BackList		=	ws::getLang("Leads>Details>BackList");
	$template->Leads_Details_SaveConfig		=	ws::getLang("Leads>Details>SaveConfig");
	$template->Leads_Details_URLSend		=	ws::getLang("Leads>Details>URLSend");
	$template->Leads_Details_EditFieldForm	=	ws::getLang("Leads>Details>EditFieldForm");
	$template->Leads_Details_EditField		=	ws::getLang("Leads>Details>EditField");
	$template->Leads_Details_DataLead		=	ws::getLang("Leads>Details>DataLead");
	$template->Leads_Details_RegisterName	=	ws::getLang("Leads>Details>RegisterName");
	$template->Leads_Details_Title			=	ws::getLang("Leads>Details>Title");
	$template->Leads_Details_ShortDesc		=	ws::getLang("Leads>Details>ShortDesc");
	$template->Leads_Details_LeadConfig		=	ws::getLang("Leads>Details>LeadConfig");
	$template->Leads_Details_AboutForm		=	ws::getLang("Leads>Details>AboutForm");
	$template->Leads_Details_UserEmail		=	ws::getLang("Leads>Details>UserEmail");	
	$template->Leads_Details_JustSave		=	ws::getLang("Leads>Details>JustSave");	
	$template->Leads_Details_JustSend		=	ws::getLang("Leads>Details>JustSend");	
	$template->Leads_Details_SaveSend		=	ws::getLang("Leads>Details>SaveSend");	
	$template->Leads_Details_UserResponse	=	ws::getLang("Leads>Details>UserResponse");	
	$template->Leads_Details_SelectField	=	ws::getLang("Leads>Details>SelectField");	
	$template->Leads_Details_ServerConfig	=	ws::getLang("Leads>Details>ServerConfig");	
	$template->Leads_Details_SMTPSecure		=	ws::getLang("Leads>Details>SMTPSecure");	
	$template->Leads_Details_Host			=	ws::getLang("Leads>Details>Host");	
	$template->Leads_Details_Port			=	ws::getLang("Leads>Details>Port");	
	$template->Leads_Details_SMTPAuth		=	ws::getLang("Leads>Details>SMTPAuth");	
	$template->Leads_Details_SenderEmail	=	ws::getLang("Leads>Details>SenderEmail");		
	$template->Leads_Details_Password		=	ws::getLang("Leads>Details>Password");	
 	$template->Leads_Details_SMTPDomain		=	ws::getLang("Leads>Details>SMTPDomain");
	$template->Leads_Details_SMTPex			=	ws::getLang("Leads>Details>SMTPex");			
	$template->Leads_Details_Portex			=	ws::getLang("Leads>Details>Portex");			
	$template->Leads_Details_ReqAuth		=	ws::getLang("Leads>Details>ReqAuth");
	$template->Leads_Details_SendersName	=	ws::getLang("Leads>Details>SendersName");
	$template->Leads_Details_ReceiveMail	=	ws::getLang("Leads>Details>ReceiveMail");
	$template->Leads_Details_Subject		=	ws::getLang("Leads>Details>Subject");
	$template->Leads_Details_SubjectUser	=	ws::getLang("Leads>Details>SubjectUser");
	$template->Leads_Details_AdminMessage	=	ws::getLang("Leads>Details>AdminMessage");
	$template->Leads_Details_UserMessage	=	ws::getLang("Leads>Details>UserMessage");
	$template->Leads_Details_URLSucess		=	ws::getLang("Leads>Details>URLSucess");
	$template->Leads_Details_URLError		=	ws::getLang("Leads>Details>URLError");
	$template->Leads_Details_NoAjax			=	ws::getLang("Leads>Details>NoAjax");
	$template->Leads_Details_ImageTop		=	ws::getLang("Leads>Details>ImageTop");
	$template->Leads_Details_Signature		=	ws::getLang("Leads>Details>Signature");
	$template->Leads_Details_MyComputer		=	ws::getLang("Leads>Details>MyComputer");
	$template->Leads_Details_Library		=	ws::getLang("Leads>Details>Library");
	$template->Leads_Details_Modal_AccessData		=	ws::getLang("Leads>Details>Modal>AccessData");	
	$template->Leads_Details_Modal_Backing			=	ws::getLang("Leads>Details>Modal>Backing");	
	$template->Leads_Details_Modal_Saving			=	ws::getLang("Leads>Details>Modal>Saving");	
	$template->Leads_Details_Modal_Sucess			=	ws::getLang("Leads>Details>Modal>Sucess");	
	$template->Leads_Details_Modal_ProcessImage		=	ws::getLang("Leads>Details>Modal>ProcessImage");	
	
 
	############################################################### 
	# LISTA AS COLUNAS CADASTRADAS PARA DEFINIR O CAMPO DE EMAIL 
	###############################################################
	foreach($_COLUNAS_->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$template->OPT_COLUMN = $coluna['Field'];
			$template->block("COLUMNS"); 
		}
	};

	##################################################################
	# RETORNAMOS AS COLUNAS NOVAMENTE PARA INSERÇÃO NO EMAIL AO ADMIN 
	##################################################################
	foreach($_COLUNAS_->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$template->A_FIELD = $coluna['Field'];
			$template->block("FIELD_MSN_ADM"); 
		}
	};

	#####################################################################
	# RETORNAMOS AS COLUNAS NOVAMENTE PARA INSERÇÃO NO EMAIL AO USUÁRIO 
	#####################################################################
	foreach($_COLUNAS_->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$template->A_FIELD = $coluna['Field'];
			$template->block("FIELD_MSN_USER"); 
		}
	};

	#####################################################################
	# FINALIZAMOS O ARQUIVO, SETAMOS O BLOCO E RETORNAMOS O HTML  
	#####################################################################
	$template->block("DETALHES_LEAD"); 
	$template->show(); 
