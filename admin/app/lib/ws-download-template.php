<?php 
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'ws-bkp'))));}

	############################################################################################################################
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	############################################################################################################################
	include_once (INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

	############################################################################################################################
	# SEPARAMOS O TOKEN DE AUTENTICAÇÃO
	############################################################################################################################
	$_URL_TOKEN =explode("/",ws::urlPath());
	$_URL_TOKEN =end($_URL_TOKEN);


	###################################################################################
	# EXCLUI QUALQUER TOKEN QUE ESTEJA EXPIRADO NA TABELA DE TEMPLATES
	###################################################################################
	$_EXCL_ = new MySQL();
	$_EXCL_->set_table(PREFIX_TABLES . 'ws_auth_template');
	$_EXCL_->set_where('NOW() > expire');
	$_EXCL_->exclui();
	
	###################################################################################
	# CASO EXISTA UM TOKEN DE ACESSO
	###################################################################################
	if(null != $_URL_TOKEN ){
		$_VERIFY_ = new MySQL();
		$_VERIFY_->set_table(PREFIX_TABLES . 'ws_auth_template');
		$_VERIFY_->set_where('token="'.$_URL_TOKEN.'"');
		$_VERIFY_->select();

		########################################################################################
		# E VERIFICAMOS SE ELE É VÁLIDO NO SISTEMA E SE O TOKEN EXISTE NA TABELA DOS ARQUIVOS
		########################################################################################
		if(ws::getTokenRest($_URL_TOKEN,false,false) && $_VERIFY_->_num_rows == 1){
			$file_url = INCLUDE_PATH.'ws-bkp/'.$_VERIFY_->fetch_array[0]['filename'];
			###################################################################################
			# LIBERA O ARQUIVO PARA DOWNLOAD
			###################################################################################
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="'.basename($file_url).'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($file_url));
			header('Accept-Ranges: bytes');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Pragma: public');
			header('Cache-Control:');
			readfile($file_url);
			exit;
		}else{
			###################################################################################
			# RETORNA O ERRO
			###################################################################################
			header("HTTP/1.0 500 Internal Server Error");
			die();
		}
	}else{
		###################################################################################
		# RETORNA O ERRO
		###################################################################################
			header("HTTP/1.0 500 Internal Server Error");
			die();
	}


















