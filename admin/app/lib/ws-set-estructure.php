<?php
	#########################################################################
	#########################################################################
	#########################################################################
	#########################################################################
	#
	# 	CONSTRUINDO A ESTRUTURA DER PASTAS E ARQUIVOS BASICOS DO SISTEMA
	#
	#########################################################################
	#########################################################################
	#########################################################################
	#########################################################################

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP")){$path = substr(dirname($_SERVER['REQUEST_URI']),1);define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));}
		if(!defined("INCLUDE_PATH")) {$path= str_replace("\\","/",getcwd()); $includePath=implode(array_filter(explode("/",substr($path,0,strpos($path,'admin')))),"/");define("INCLUDE_PATH",$includePath);}

	##########################################################################################################
	# 	FUNÇÕES GLOBAIS DO SISTEMA
	##########################################################################################################
		include_once(INCLUDE_PATH.'admin/app/lib/ws-globals-functions.php');

	#########################################################################
	# CRIAMOS TODOS OS DIRETORIOS DO WEBSITE A SER MONTADO
	#########################################################################
		_excluiDir(INCLUDE_PATH.'ws-install-master');
		_mkdir('ws-cache',true);
		_mkdir('ws-bkp',true);
		_mkdir('ws-tmp',true);
		_mkdir('ws-shortcodes',true);
		_mkdir('website',true);
		_mkdir('website/includes',true);
		_mkdir('website/plugins',true);
		_mkdir('website/assets/css',true);
		_mkdir('website/assets/js',true);
		_mkdir('website/assets/img',true);
		_mkdir('website/assets/template',true);
		_mkdir('website/assets/fonts',true);
		_mkdir('website/assets/upload-files/thumbnail',true);
		_mkdir('website/assets/libraries',true);
		_copyFolder('admin/app/ws-modules/plugins', 'website/plugins',true);
		_excluiDir(INCLUDE_PATH.'ws-update');
		_copyFolder('admin/app/ws-modules/ws-update/ws-update', 'ws-update',true);

		_copy(INCLUDE_PATH."admin/app/lib/my-shortcode.php",INCLUDE_PATH."ws-shortcodes/my-shortcode.php",false);

//		_file_put_contents(INCLUDE_PATH.'website/includes/header.php', 'Header<hr>',false,true);
//		_file_put_contents(INCLUDE_PATH.'website/includes/erro404.php', 'ERRO 404!',false,true);
//		_file_put_contents(INCLUDE_PATH.'website/includes/inicio.php', 'Olá mundo!',false,true);
//		_file_put_contents(INCLUDE_PATH.'website/includes/footer.php', '<hr>Footer',false,true);
		_file_put_contents(INCLUDE_PATH.'website/assets/.htaccess', 'RewriteEngine Off',false);


	#########################################################################
	# CRIAMOS O HTACCES PADRÃO DO SISTEMA PARA O CAMINHO ROOT
	#########################################################################

		refresh_Path_AllFiles();

	######################################################################################################################################
	# CASO NÃO TENHA AINDA O ARQUIVOO NO LUGAR CERTO E ESTEJA FAZENDO UPDATE AO INVEZ DE INSTALL
	######################################################################################################################################
		$pathFile 	= INCLUDE_PATH.'ws-bkp/.htaccess';
		$conteudo 	= "<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteCond %{SCRIPT_FILENAME} !-f\nRewriteRule ^(.*)$ ws-download-template.php\n</IfModule>";

		$origem 	= INCLUDE_PATH."admin/app/lib/ws-download-template.php";
		$destino 	= INCLUDE_PATH."ws-bkp/ws-download-template.php";

		_copy($origem,$destino);
		_file_put_contents($pathFile,$conteudo);

