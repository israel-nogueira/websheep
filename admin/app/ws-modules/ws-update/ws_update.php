<?
error_reporting( E_ALL ); 
#############################################################################################
# DEFINIMOS O ROOT DO SISTEMA
#############################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}

	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

####################################################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');	

############################################### URL AMIGAVEIS PADRÃO ###############################

	$RewriteRule 	= array();
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-class.php$'				, 'filePath'=>'/admin/app/lib/class-ws-v1.php'						, 'type'=>'system'	,'alias'=>'ws-class'				, 'title'=>'Include das funções do painel');            
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-includes/(.*)$'			, 'filePath'=>'/admin/includes/$1'									, 'type'=>'system'	,'alias'=>'ws-includes'				, 'title'=>'Include do sistema');                    	
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-ace-editor/(.*)$'		, 'filePath'=>'/admin/app/ws-modules/ws-webmaster/src-min-noconflict/$1'	, 'type'=>'system'	,'alias'=>'ws-ace-editor'			, 'title'=>'Editor de código');                     
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-video/(.*)$'			, 'filePath'=>'/admin/app/core/ws-video.php'						, 'type'=>'system'	,'alias'=>'ws-video'				, 'title'=>'URL padrão para os vídeos do sistema');     
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-download/(.*)$'			, 'filePath'=>'/admin/app/core/ws-download.php'						, 'type'=>'system'	,'alias'=>'ws-download'				, 'title'=>'URL padrão para download dos arquivos'); 
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-leads/(.*)$'			, 'filePath'=>'/admin/app/core/ws-leads.php'						, 'type'=>'system'	,'alias'=>'ws-leads'				, 'title'=>'URL padrão para cadastros de formularios'); 
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-img/(.*)$'				, 'filePath'=>'/admin/app/core/ws-img.php'							, 'type'=>'system'	,'alias'=>'ws-img'					, 'title'=>'URL padrão para as imagens do sistema');     
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-download-now/(.*)$'		, 'filePath'=>'/admin/app/core/ws-download-now.php?filename=$1'		, 'type'=>'system'	,'alias'=>'ws-download-now'			, 'title'=>'URL padrão para download de arquivos');      
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-rest/(.*)$'				, 'filePath'=>'/admin/app/core/ws-rest.php$1'						, 'type'=>'system'	,'alias'=>'ws-rest'					, 'title'=>'URL padrão para requisições ao BD');      
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-gz/(.*)$'				, 'filePath'=>'/admin/app/core/ws-gz.php?type=admin'				, 'type'=>'system'	,'alias'=>'ws-gz'					, 'title'=>'Processa arquivos .gz');      
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-gzip/(.*)$'				, 'filePath'=>'/admin/app/core/ws-gz.php?type=website'				, 'type'=>'system'	,'alias'=>'ws-gzip'					, 'title'=>'Processa arquivos .gz no website');      
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-commits$'				, 'filePath'=>'/admin/app/core/ws-commits.php'						, 'type'=>'system'	,'alias'=>'ws-commits'				, 'title'=>'Captura todos os commits do sistema');      
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-branches$'				, 'filePath'=>'/admin/app/core/ws-branches.php'						, 'type'=>'system'	,'alias'=>'ws-branches'				, 'title'=>'Captura todos os branches do sistema');      
	$RewriteRule[] 	= array('urlAmigavel'=>'^ws-php-info$'				, 'filePath'=>'/admin/app/config/ws-php-info.php'					, 'type'=>'system'	,'alias'=>'ws-php-info'				, 'title'=>'Retorna as configurações do php');      

	############################################### SEPARA AS TABELAS EXISTENTES ###############################

	$GLOBALS["ConfigSQL"] = "";
	function add_if_not_exist($tabela_setada,$coluna_setada,$tipo_setado){
			$GLOBALS["ConfigSQL"] .= "SET @s = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".PREFIX_TABLES.$tabela_setada."' AND table_schema = DATABASE() AND column_name = '".$coluna_setada."') > 0,'SELECT 1','ALTER TABLE ".PREFIX_TABLES.$tabela_setada." ADD ".$coluna_setada." ".$tipo_setado."')); PREPARE stmt FROM @s;EXECUTE stmt;DEALLOCATE PREPARE stmt;";
	}
	function CreateTableIfNotExist($tabela_setada){
			$GLOBALS["ConfigSQL"] .=  "CREATE TABLE IF NOT EXISTS ".PREFIX_TABLES.$tabela_setada." (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	}

	############################################### SEPARA AS TABELAS EXISTENTES ###############################

	$old_tables=array();
	global $_conectMySQLi_;
	$query 			= 'SHOW TABLES';
	$fetch_array 	= array();
	$consulta 		= mysqli_query($_conectMySQLi_,$query)or die (_erro(mysqli_error()));
	while ($row 	= mysqli_fetch_assoc($consulta)) {$fetch_array[]= $row;}
	foreach ($fetch_array as $tab) { 
		$old_tables[]=$tab['Tables_in_'.NOME_BD];
	}


// ###############################################################################################################
// ######################### 		LINK FERRAMENTA COM A URL DA PÁGINA  		###################################
// ###############################################################################################################

	CreateTableIfNotExist('ws_link_cache_file_tool');
	add_if_not_exist("ws_link_cache_file_tool",			'id_tool',		'varchar(500) 	NULL DEFAULT ""');
	add_if_not_exist("ws_link_cache_file_tool",			'id_url',		'varchar(500) 	NULL DEFAULT ""');


// ###############################################################################################################
// ######################### 		LINK DA URL COM O ARQUIVO DE CACHE  		##################################
// ###############################################################################################################

	CreateTableIfNotExist('ws_link_cache_file_url');
	add_if_not_exist("ws_link_cache_file_url",			'id_url',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist("ws_link_cache_file_url",			'filename',		'varchar(500) 	NULL DEFAULT ""');
	add_if_not_exist("ws_link_cache_file_url",			'request_uri',	'varchar(800) 	NULL DEFAULT ""');


###############################################################################################################
#######################################			LINK DE EXPORTAÇÃO DE BACKUP  		###########################
###############################################################################################################
	CreateTableIfNotExist('ws_auth_template');
	add_if_not_exist('ws_auth_template',			'token',		'varchar(500) 	NULL DEFAULT ""');
	add_if_not_exist("ws_auth_template",			'filename',		'varchar(500) 	NULL DEFAULT ""');
	add_if_not_exist("ws_auth_template",			'ws_timestamp',	'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist("ws_auth_template",			'expire',		'DATETIME 	NOT NULL');


###############################################################################################################
#######################################			TOKEN PARA VALIDAÇÃO				###########################
###############################################################################################################
	CreateTableIfNotExist('ws_auth_token');
	add_if_not_exist('ws_auth_token',			'token',			'varchar(500) 	NULL DEFAULT ""');
	add_if_not_exist("ws_auth_token",			'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist("ws_auth_token",			'expire',			'DATETIME 	NOT NULL');

###############################################################################################################
################################# 	LINK DE INCLUDE DE ARQUIVOS (CSS/JS) COM A URL 	###########################
###############################################################################################################
	CreateTableIfNotExist('ws_link_url_file');
	add_if_not_exist('ws_link_url_file',	'ws_author',			'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_link_url_file',	'position',				'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_link_url_file',	'id_url',				'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_link_url_file',	'file',					'varchar(500) 		NULL DEFAULT ""');
	add_if_not_exist('ws_link_url_file',	'include_media',		'varchar(500) 		NULL DEFAULT ""');
	add_if_not_exist('ws_link_url_file',	'include_id',			'varchar(500) 		NULL DEFAULT ""');
	add_if_not_exist('ws_link_url_file',	'position',				'int(11) 		NOT NULL DEFAULT 0');
	add_if_not_exist('ws_link_url_file',	'ext',					'varchar(5) 		NULL DEFAULT ""');
	add_if_not_exist('ws_link_url_file',	'token',				'varchar(500) 		NULL DEFAULT ""');


###############################################################################################################
################################################# 	LEAD CAPTURE 	###########################################
###############################################################################################################
	CreateTableIfNotExist('ws_list_leads');
	add_if_not_exist('ws_list_leads',			'ws_author',			'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_list_leads',			'title',				'varchar(350) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'token',				'varchar(150) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'content',				'varchar(350) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'cad_table',			'varchar(350) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'finalidade',			'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'resposta_ao_usuario',	'int(1) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_list_leads',			'smtp',					'int(1) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_list_leads',			'smtp_local',			'int(1) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_list_leads',			'host',					'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'port',					'int(3) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_list_leads',			'SMTPSecure',			'varchar(10)		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'server_ssl',			'int(1) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_list_leads',			'email_envio',			'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'pass',					'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'remetente',			'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'remetente_name',		'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'assunto',				'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'assunto_clt',			'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'header_email',			'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'footer_email',			'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'camp_mail_clt',		'varchar(200) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'msng_resp',			'TEXT 		 		NULL');
	add_if_not_exist('ws_list_leads',			'msng_resp_user',		'TEXT 		 		NULL');
	add_if_not_exist('ws_list_leads',			'url_sucess',			'varchar(400) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'url_error',			'varchar(400) 		NULL DEFAULT ""');
	add_if_not_exist('ws_list_leads',			'assunto_clt',			'varchar(400) 		NULL DEFAULT ""');
	add_if_not_exist("ws_list_leads",			'ws_timestamp',			'TIMESTAMP 		NOT NULL DEFAULT CURRENT_TIMESTAMP');

###############################################################################################################
################################################# 	META TAGS 	###############################################
###############################################################################################################
	CreateTableIfNotExist('meta_tags');
	add_if_not_exist('meta_tags',				'ws_author',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('meta_tags',				'id_page',			'int(12) 		NOT NULL default "0"');
	add_if_not_exist('meta_tags',				'tag',				'varchar(350)		NULL default ""');
	add_if_not_exist('meta_tags',				'type' ,			'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'type_content' ,	'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'content',			'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'href',				'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'sizes',			'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'title',			'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'type',				'varchar(350) 		NULL default ""');
	add_if_not_exist('meta_tags',				'media',			'varchar(350) 		NULL default ""');
	add_if_not_exist("meta_tags",				'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');


###############################################################################################################
############################################# UPDATE NAS TABELAS DO SISTEMA ###################################
###############################################################################################################
	CreateTableIfNotExist('ws_template');
	add_if_not_exist('ws_template',				'ws_author',	'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_template',				'slug',			'varchar(250) 	NULL default ""');
	add_if_not_exist('ws_template',				'template',		'LONGTEXT 		NULL default NULL');
	add_if_not_exist('ws_template',				'obs' ,			'varchar(350) 	NULL default ""');
	add_if_not_exist('ws_template',				'token',		'varchar(150) 	NULL default ""');
	add_if_not_exist("ws_template",				'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');


###############################################################################################################
###################################################### WS VÍDEOS ##############################################
###############################################################################################################
	CreateTableIfNotExist('ws_video');
	add_if_not_exist('ws_video',				'ws_author',	'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_video',				'linkvideo',	'varchar(250) 	NULL default ""');
	add_if_not_exist('ws_video',				'keyaccess',	'varchar(350)	NULL default ""');
	add_if_not_exist('ws_video',				'creation',		'timestamp 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

##############################################################################################################
######################################### ACESSO DIRETO A FERRAMENTA #########################################
##############################################################################################################
	CreateTableIfNotExist('ws_direct_access');
	add_if_not_exist('ws_direct_access', 		'type_obj',		'varchar(250) 	NULL default "item"');
	add_if_not_exist('ws_direct_access', 		'id_tool',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_direct_access', 		'id_item',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_direct_access', 		'id_gal',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_direct_access'	,		'ws_author',	'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_direct_access'	,		'createin',		'timestamp		NOT NULL DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist('ws_direct_access'	,		'keyaccess',	'varchar(128) 	NULL default ""' );
	add_if_not_exist('ws_direct_access'	,		'expire',		'DATE 			NULL');
	// item // detalhes // categorias // galerias // imagens // arquivos

###############################################################################################################
############################################# BKP WebSheep ###################################
###############################################################################################################
	CreateTableIfNotExist('bkp_ws');
	add_if_not_exist('bkp_ws',				'ws_author',	'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('bkp_ws',				'file',			'varchar(250) 	NULL default ""');
	add_if_not_exist('bkp_ws',				'id_criador',	'varchar(200) 	NULL default ""');
	add_if_not_exist('bkp_ws',				'obs' ,			'varchar(350) 	NULL default ""');
	add_if_not_exist('bkp_ws',				'token',		'varchar(150) 	NULL default ""');
	add_if_not_exist('bkp_ws',				'criacao',		'timestamp 	NOT NULL DEFAULT CURRENT_TIMESTAMP');


###############################################################################################################
################################################	ws_user_link_ferramenta		###############################
###############################################################################################################
	CreateTableIfNotExist('ws_user_link_ferramenta');
	add_if_not_exist('ws_user_link_ferramenta',		'ws_author',		'int(11)		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_user_link_ferramenta',		'id_user',			'int(11)		NOT NULL default "0"');
	add_if_not_exist('ws_user_link_ferramenta',		'id_ferramenta',	'int(11)		NOT NULL default "0"');
	add_if_not_exist("ws_user_link_ferramenta",		'ws_timestamp',		'TIMESTAMP		NOT NULL DEFAULT CURRENT_TIMESTAMP');


###############################################################################################################
####################################################	ws_link_itens		###################################
###############################################################################################################
	CreateTableIfNotExist('ws_link_itens');
	add_if_not_exist("ws_link_itens",		'ws_tool_item',			'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist("ws_link_itens",		'ws_id_ferramenta',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist("ws_link_itens",		'ws_id_ferramenta_link','int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist("ws_link_itens",		'ws_author',			'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist("ws_link_itens",		'ws_draft' ,			'BOOLEAN  		NOT NULL DEFAULT FALSE');
	add_if_not_exist("ws_link_itens",		'ws_id_draft' ,			'INT(11)			NULL DEFAULT 0');	
	add_if_not_exist('ws_link_itens',		'id_item' ,				'int(11) 		NOT NULL default "0"');
	add_if_not_exist('ws_link_itens',		'id_item_link' ,		'int(11) 		NOT NULL default "0"');
	add_if_not_exist('ws_link_itens',		'id_cat_link' ,			'int(11) 		NOT NULL default "0"');
	add_if_not_exist("ws_link_itens",		'ws_timestamp' ,		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');


###############################################################################################################
################################################	ws_ferramentas		#######################################
###############################################################################################################
	
	CreateTableIfNotExist('ws_ferramentas');
	add_if_not_exist('ws_ferramentas'		,'max_item',			'int(11) 				NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_ferramentas'		,'ws_author',			'int(11) 				NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_ferramentas'		,'clone_tool',			'int(11) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'App_Type', 			'int(11) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_prefix_', 			'varchar(200) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_exec_js_', 			'varchar(100) 			NOT NULL default "nada"');
	add_if_not_exist('ws_ferramentas'		,'_js_', 				'LONGTEXT 					NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'html_item', 				'LONGTEXT 				NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'html_cat', 				'LONGTEXT 				NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'html_img', 				'LONGTEXT 				NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'html_gal', 				'LONGTEXT 				NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'html_img_gal', 			'LONGTEXT 				NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'html_file', 				'LONGTEXT 				NULL default NULL');
	add_if_not_exist('ws_ferramentas'		,'slug', 				'varchar(100) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'item_type', 			'varchar(50) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_tit_menu_', 			'varchar(255) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_desc_', 				'varchar(255) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_tit_topo_', 			'varchar(255) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_patch_',				'varchar(255) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_pasta_especial_',	'varchar(255) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'token',				'varchar(255) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'det_listagem_item',	'varchar(200) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_exclude_', 			'int(1) 				NOT NULL default TRUE');
	add_if_not_exist('ws_ferramentas'		,'_alterado_', 			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_rel_prod_cat_', 		'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_tool_pai_', 			'int(11) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_menu_popup_', 		'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_grupo_pai_', 		'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_esconde_topo_', 		'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_plugin_', 			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'posicao', 			'int(11) 				NOT NULL default "0"');
	add_if_not_exist('ws_ferramentas'		,'_niveis_', 			'int(11) 				NOT NULL default "0"');
	add_if_not_exist('ws_ferramentas'		,'_id_unico_', 			'int(11) 				NOT NULL default "0"');
	add_if_not_exist('ws_ferramentas'		,'_keywords_', 			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'avatar',				'varchar(200) 				NULL default ""');
	add_if_not_exist('ws_ferramentas'		,'_afinidades_', 		'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_selos_', 			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_fotos_', 			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_galerias_',			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_files_',				'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_videos_',			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_arquivos_',			'int(1) 				NOT NULL default FALSE');
	add_if_not_exist('ws_ferramentas'		,'_extencao_',			'varchar(255)				NULL default ""');
	add_if_not_exist("ws_ferramentas"		,'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist("ws_ferramentas"		,'image_tool',			'LONGTEXT  NULL default NULL');


###############################################################################################################
################################################	ws_ferramentas		#######################################
###############################################################################################################
	CreateTableIfNotExist('ws_link_path_tools');
	add_if_not_exist('ws_link_path_tools'		,'id_tool',			'int(11) 				NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_link_path_tools'		,'id_path',			'int(11) 				NOT NULL DEFAULT FALSE');

###############################################################################################################
################################################	ws_ferramentas		#######################################
###############################################################################################################
	CreateTableIfNotExist('ws_path_tools');
	add_if_not_exist('ws_path_tools'		,'path_name',			'varchar(200) 			NULL default ""');
	add_if_not_exist('ws_path_tools'		,'posicao',				'int(11) 				NOT NULL default FALSE');



###############################################################################################################
################################################	ws_usuarios		###########################################
###############################################################################################################

	CreateTableIfNotExist('ws_usuarios');
	add_if_not_exist('ws_usuarios'			,'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_usuarios'			,'id_criador'			,'int(11) 		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'id_cargo'				,'int(11) 		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'add_user'				,'int(1)		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'edit_only_own '		,'int(1)		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'webmaster'			,'int(1)		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'admin'				,'int(1)		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'nome'					,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'sobrenome'			,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'email'				,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'telefone'				,'varchar(30) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'endereco'				,'varchar(500) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'CPF'					,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'RG'					,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'descricao'			,'varchar(500) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'login'				,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'usuario'				,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'senha'				,'varchar(300) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'token'				,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'sessao'				,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'avatar'				,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'id_status'			,'int(11) 		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'ativo'				,'int(1) 		NULL default "1"');
	add_if_not_exist('ws_usuarios'			,'leitura'				,'int(1) 		NULL default "0"');
	add_if_not_exist('ws_usuarios'			,'tokenRequest'			,'varchar(100) 	NULL default ""');
	add_if_not_exist('ws_usuarios'			,'tokenRequestTime'		,'datetime');
	add_if_not_exist('ws_usuarios'			,'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');


###############################################################################################################
################################################	ws_log	###################################################
###############################################################################################################
	CreateTableIfNotExist('ws_log');
	add_if_not_exist('ws_log'		,'id_user'			,'int(11) 			NOT	 NULL default 0');
	add_if_not_exist('ws_log'		,'id_ferramenta'	,'int(11) 			NOT	 NULL default 0');
	add_if_not_exist('ws_log'		,'id_item'			,'int(11) 			NOT	 NULL default 0');
	add_if_not_exist('ws_log'		,'linha'			,'int(11) 			NOT	 NULL default 0');
	add_if_not_exist('ws_log'		,'coluna'			,'int(11) 			NOT	 NULL default 0');
	add_if_not_exist('ws_log'		,'dataregistro'		,'timestamp			DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist('ws_log'		,'type'				,'varchar(300) 		NULL default ""');
	add_if_not_exist('ws_log'		,'url'				,'varchar(300) 		NULL default ""');
	add_if_not_exist('ws_log'		,'titulo'			,'varchar(300) 		NULL default ""');
	add_if_not_exist('ws_log'		,'mensagem'			,'varchar(300) 		NULL default ""');
	


###############################################################################################################
################################################	ws_webmaster		#######################################
###############################################################################################################

	CreateTableIfNotExist('ws_webmaster');
	add_if_not_exist('ws_webmaster'			,'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_webmaster'			,'path' 				,'varchar(200) 	NULL	default ""');
	add_if_not_exist('ws_webmaster'			,'original' 			,'varchar(200)	NULL 	default ""');
	add_if_not_exist('ws_webmaster'			,'bkpfile' 				,'varchar(200)	NULL 	default ""');
	add_if_not_exist('ws_webmaster'			,'created' 				,'timestamp		NOT NULL 	DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist('ws_webmaster'			,'responsavel' 			,'int(11) 		NULL 	default "0"');
	add_if_not_exist('ws_webmaster'			,'token' 				,'varchar(200) 	NULL	default ""');
###############################################################################################################
################################################	BIBLIOTECA		###########################################
###############################################################################################################

	CreateTableIfNotExist('ws_biblioteca');
	add_if_not_exist('ws_biblioteca'	,'ws_author',		'int(11) 		NOT NULL DEFAULT FALSE'					);
	add_if_not_exist('ws_biblioteca'	,'filename'	 		,'varchar(200) 	NULL default ""'					);
	add_if_not_exist('ws_biblioteca'	,'file'	 			,'varchar(200) 	NULL default ""'					);
	add_if_not_exist('ws_biblioteca'	,'token'	 		,'varchar(200) 	NULL default ""'					);
	add_if_not_exist('ws_biblioteca'	,'tokenFile'	 	,'varchar(200) 	NULL default ""'					);
	add_if_not_exist('ws_biblioteca'	,'type'	 			,'varchar(100) 	NULL default ""'					);
	add_if_not_exist('ws_biblioteca'	,'saved' 			,'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'		);
	add_if_not_exist('ws_biblioteca'	,'token_group' 		,'varchar(200) 	NULL default ""'					);
	add_if_not_exist('ws_biblioteca'	,'upload_size'		,'int(30) 		NULL default "0"'					);
	add_if_not_exist('ws_biblioteca'	,'download' 		,'int(1) 		NULL default "0"'					);

###############################################################################################################
################################################	KEY DOWNLOADS			###########################################
###############################################################################################################

	CreateTableIfNotExist('ws_keyfile');
	add_if_not_exist('ws_keyfile'	,		'ws_author',		'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_keyfile'	,		'tokenFile'	 		,'varchar(128) 	NULL default ""');
	add_if_not_exist('ws_keyfile'	,		'active'	 		,'int(1) 		NULL default "1"');
	add_if_not_exist('ws_keyfile'	,		'disableToDown'	 	,'int(1) 		NULL default "0"');
	add_if_not_exist('ws_keyfile'	,		'refreshToDown'	 	,'int(1) 		NULL default "0"');
	add_if_not_exist('ws_keyfile'	,		'createin'	 		,'timestamp		NOT NULL DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist('ws_keyfile'	,		'expire'	 		,'DATE 			NULL');
	add_if_not_exist('ws_keyfile'	,		'keyaccess' 		,'varchar(128) 	NULL default ""' );
	add_if_not_exist('ws_keyfile'	,		'accessed' 			,'int(1) 		NULL default "0"' );

###############################################################################################################
################################################	Setupdata		###########################################
###############################################################################################################

	CreateTableIfNotExist('setupdata');
	add_if_not_exist('setupdata'	,'keyaccess_mysql' 		,'varchar(200) 	NULL DEFAULT ""');
	add_if_not_exist('setupdata'	,'auto_save' 			,'int(11) NOT NULL DEFAULT "2"');
	add_if_not_exist('setupdata'	,'title_root'			,'varchar(200) 	NULL DEFAULT ""');
	add_if_not_exist('setupdata'	,'lang'					,'varchar(20) NOT NULL DEFAULT "pt"');
	add_if_not_exist('setupdata'	,'system_version'		,'varchar(20) 	NULL default ""');
	add_if_not_exist('setupdata'	,'client_name' 			,'varchar(200) 	NULL default ""');
	add_if_not_exist('setupdata'	,'plane_id' 			,'int(11) NOT NULL default "0"');
	add_if_not_exist('setupdata'	,'plan_name' 			,'varchar(200) 	NULL default ""');
	add_if_not_exist('setupdata'	,'hd' 					,'varchar(200) NOT NULL DEFAULT "600"');
	add_if_not_exist('setupdata'	,'domain_status' 		,'tinyint(1) NOT NULL DEFAULT "1"');
	add_if_not_exist('setupdata'	,'token' 				,'varchar(200) 	NULL default ""');
	add_if_not_exist('setupdata'	,'dominio' 				,'varchar(200) 	NULL default ""');
	add_if_not_exist('setupdata'	,'id_empresa' 			,'int(11) NOT NULL default "0" COMMENT "Agencia / Programador"');
	add_if_not_exist('setupdata'	,'id_responsavel' 		,'int(11) NOT NULL default "0" COMMENT "Quem Cadastrou"');
	add_if_not_exist('setupdata'	,'id_cliente' 			,'int(11) NOT NULL default "0" COMMENT "Dono do Dominio"');
	add_if_not_exist('setupdata'	,'ip' 					,'float NOT NULL default 0');
	add_if_not_exist('setupdata'	,'plano' 				,'int(11) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'data_criacao' 		,'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
	add_if_not_exist('setupdata'	,'data_update' 			,'DATETIME NOT NULL');
	add_if_not_exist('setupdata'	,'url_exclusiva' 		,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'valor_pag' 			,'varchar(50) 	NULL DEFAULT ""');
	add_if_not_exist('setupdata'	,'prest_pag' 			,'varchar(50) 	NULL default ""');
	add_if_not_exist('setupdata'	,'forma_pag' 			,'int(2) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'fee_pag' 				,'varchar(50) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'venc_pag' 			,'int(2) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'resp_pag' 			,'tinyint(1) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'protocolo' 			,'int(1) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'url_ftp' 				,'varchar(200) 	NULL default ""');
	add_if_not_exist('setupdata'	,'login_ftp' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'pass_ftp' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'porta_ftp' 			,'int(11) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'url_ignore_add' 		,'BOOLEAN NOT NULL DEFAULT FALSE');
	add_if_not_exist('setupdata'	,'nome_bd' 				,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'url_bd' 				,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'log_bd' 				,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'pass_bd' 				,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'smtp_host' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'smtp_port' 			,'int(5) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'smtp_auth' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'smtp_email' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'smtp_secure' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'smtp_senha' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'imagem_topo' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'splash_img' 			,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'theme' 				,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'stylecss' 			,'LONGTEXT  NULL default NULL');
	add_if_not_exist('setupdata'	,'stylejson' 			,'LONGTEXT  NULL default NULL');
	add_if_not_exist('setupdata'	,'path_plug'			,'varchar(300) NOT NULL default "plugins"');
	add_if_not_exist('setupdata'	,'url_initPath'		 	,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'url_setRoot'		 	,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'url_set404'		 	,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'url_active'			,'BOOLEAN NOT NULL DEFAULT FALSE');
	add_if_not_exist('setupdata'	,'processoURL'			,'int(10) NOT NULL default 0');
	add_if_not_exist('setupdata'	,'url_ignoreAdd'		,'BOOLEAN NOT NULL DEFAULT FALSE');
	add_if_not_exist('setupdata'	,'url_congelamento'	 	,'varchar(300) 	NULL default ""');
	add_if_not_exist('setupdata'	,'url_plugin'	 		,'varchar(300) NOT NULL default "plugins"');
	add_if_not_exist('setupdata'	,'congelaFull'			,'BOOLEAN NOT NULL DEFAULT FALSE');
	add_if_not_exist('setupdata'	,'ws_cache'				,'BOOLEAN NOT NULL DEFAULT FALSE');
	add_if_not_exist('setupdata'	,'lastShaGH' 			,'varchar(256) NOT NULL default ""');
	add_if_not_exist('setupdata'	,'lastCommit' 			,'varchar(256) NOT NULL default ""');
	
	// para as urls amigaveis
	###############################################################################################################
	################################################	ws_pages		###########################################
	###############################################################################################################
	
	CreateTableIfNotExist('ws_pages');
	add_if_not_exist('ws_pages'		,'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist('ws_pages'		,'id'					,'int(11) NOT NULL');
	add_if_not_exist('ws_pages'		,'id_tool'				,'int(11) NOT NULL default 0');
	add_if_not_exist('ws_pages'		,'title'				,'varchar(200) 	NULL default ""');
	add_if_not_exist('ws_pages'		,'file'					,'varchar(300) 	NULL default ""');
	add_if_not_exist('ws_pages'		,'path'					,'varchar(500) 	NULL default ""');
	add_if_not_exist('ws_pages'		,'token'				,'varchar(200) 	NULL default ""');
	add_if_not_exist('ws_pages'		,'type'					,'varchar(10) NOT NULL DEFAULT "path"');
	add_if_not_exist('ws_pages'		,'posicao'				,'int(11) NOT NULL default 0');
	add_if_not_exist('ws_pages'		,'title_page'			,'varchar(350)		NULL default ""');
	add_if_not_exist('ws_pages'		,'sitemap_xml'			,'varchar(500)		NULL default ""');
	add_if_not_exist('ws_pages'		,'tool_master'			,'varchar(500)		NULL default ""');
	add_if_not_exist('ws_pages'		,'typeList'				,'varchar(10)		NULL default ""');
	add_if_not_exist('ws_pages'		,'alias'				,'varchar(350)		NULL default ""');

	###############################################################################################################
	################################################	FERRAMENTAS		###########################################
	###############################################################################################################
	$tab = "_model";
	CreateTableIfNotExist($tab."_cat");
	add_if_not_exist($tab."_cat",	'ws_author'			,'int(11) 			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_cat",	'id_cat'			,'int(11)  			NOT NULL default "0"');
	add_if_not_exist($tab."_cat",	'ws_type'			,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_cat",	'ws_protect'		,'BOOLEAN 				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_cat",	'ws_id_ferramenta'	,'int 		(11)  		NOT NULL default "0"');
	add_if_not_exist($tab."_cat",	'ws_tool_id'		,'int 		(11)  		NOT NULL default "0"');
	add_if_not_exist($tab."_cat",	'ws_tool_item'		,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_cat",	'ws_nivel'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_cat",	'posicao'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_cat",	'avatar'			,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_cat",	'filename'			,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_cat",	'titulo'			,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_cat",	'token'				,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_cat",	'texto'				,'text 					NULL DEFAULT NULL');
	add_if_not_exist($tab."_cat",	'url'				,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_cat",	'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');
	CreateTableIfNotExist($tab."_item");
	
	add_if_not_exist($tab."_item",	'ws_author'			,'int(11) 				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_item",	'ws_type'			,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_item",	'ws_protect'		,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_item",	'ws_draft'			,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_item",	'ws_id_draft'		,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'ws_id_ferramenta'	,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'ws_tool_id'		,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'ws_tool_item'		,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'id_cat'			,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'ws_nivel'			,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'posicao'			,'INT(11) 					NULL DEFAULT 0');
	add_if_not_exist($tab."_item",	'avatar'			,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_item",	'token',			'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_item",	'ws_timespam'		,'TIMESTAMP 			NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_gal");
	add_if_not_exist($tab."_gal",	'ws_author'				,'int(11) 			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_gal",	'ws_draft'				,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_gal",	'ws_id_draft'			,'INT(11) 				NULL DEFAULT 0');
	add_if_not_exist($tab."_gal",	'ws_type'				,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_gal",	'ws_id_ferramenta', 	'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'ws_tool_id', 			'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'ws_tool_item', 		'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'ws_nivel', 			'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'id_cat', 				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'id_item',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'posicao', 				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_gal",	'avatar', 				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_gal",	'filename',			 	'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_gal",	'titulo', 				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_gal",	'token', 				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_gal",	'texto', 				'text 				NULL DEFAULT NULL');
	add_if_not_exist($tab."_gal",	'url', 					'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_gal",	'ws_timestamp',			'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_img_gal");
	add_if_not_exist($tab."_img_gal",	'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_img_gal",	'ws_draft'			,'BOOLEAN  		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_img_gal",	'ws_id_draft'		,'INT(11) 			NULL DEFAULT 0');
	add_if_not_exist($tab."_img_gal",	'ws_type'			,'BOOLEAN  		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_img_gal",	'ws_id_ferramenta','int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'ws_tool_id',' 		int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'ws_tool_item',' 	int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'id_item','			int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'id_galeria','		int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'id_cat','			int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'posicao',' 		int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'ws_nivel',' 		int 	(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'titulo',' 			varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img_gal",	'url',' 			varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img_gal",	'texto',' 			text 				NULL DEFAULT NULL');
	add_if_not_exist($tab."_img_gal",	'imagem','	 		varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img_gal",	'filename',' 		varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img_gal",	'file',' 			varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img_gal",	'avatar',' 			int 	(1)  	NOT NULL default 0');
	add_if_not_exist($tab."_img_gal",	'token',' 			varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img_gal",	'ws_timestamp',			'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_files");
	add_if_not_exist($tab."_files",	'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_files",	'ws_draft'				,'BOOLEAN  			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_files",	'ws_id_draft'			,'INT(11) 				NULL DEFAULT 0');
	add_if_not_exist($tab."_files",	'ws_type'				,'BOOLEAN  			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_files",	'ws_id_ferramenta',		'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'ws_tool_id',			'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'ws_tool_item',			'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'id_item',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'id_cat',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'ws_nivel',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'posicao', 				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'titulo', 				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_files",	'painel', 				'BOOLEAN		 	NOT NULL default "0"');
	add_if_not_exist($tab."_files",	'url', 					'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_files",	'texto', 				'text 					NULL DEFAULT NULL');
	add_if_not_exist($tab."_files",	'file', 				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_files",	'filename', 			'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_files",	'token', 				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_files"	,'size_file',			'int		(30) 	NOT NULL default 0');
	add_if_not_exist($tab."_files"	,'download',			'int		(1) 	NOT NULL default 0');
	add_if_not_exist($tab."_files",	'uploaded', 			'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_img");
	add_if_not_exist($tab."_img",	'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_img",	'ws_draft'				,'BOOLEAN  			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_img",	'ws_id_draft'			,'INT(11) 			NULL DEFAULT 0');
	add_if_not_exist($tab."_img",	'ws_type'				,'BOOLEAN  			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_img",	'avatar',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'ws_id_ferramenta',		'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'ws_tool_id',			'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'ws_tool_item',			'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'id_item',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'id_cat',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'ws_nivel',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'posicao',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'painel',				'int 		(11)  	NOT NULL default 0');
	add_if_not_exist($tab."_img",	'titulo',				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img",	'url',					'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img",	'texto',				'text 				NULL DEFAULT NULL');
	add_if_not_exist($tab."_img",	'imagem',				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img",	'filename',				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img",	'token',				'varchar	(300) 		NULL default ""');
	add_if_not_exist($tab."_img",	'ws_timestamp',			'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_op_multiple");
	add_if_not_exist($tab."_op_multiple",		'ws_author'			,'int(11) 				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_op_multiple",		'ws_id_ferramenta',	'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_op_multiple",		'ws_tool_id',		'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_op_multiple",		'ws_tool_item',		'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_op_multiple",		'ws_nivel',			'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_op_multiple",		'id_ferramenta',	'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_op_multiple",		'id_item',			'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_op_multiple",		'id_campo',			'varchar	(300)  			NULL default ""');
	add_if_not_exist($tab."_op_multiple",		'label',			'varchar	(300)  			NULL default ""');
	add_if_not_exist($tab."_op_multiple",		'token',			'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_op_multiple",		'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_link_op_multiple");

	add_if_not_exist($tab."_link_op_multiple",	'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_link_op_multiple",	'ws_draft'			,'BOOLEAN  				NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_link_op_multiple",	'ws_id_ferramenta'	,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'ws_tool_id'		,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'ws_tool_item'		,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'ws_nivel'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'id_ferramenta'		,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'id_item'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'id_opt'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_op_multiple",	'id_campo',			'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_link_op_multiple",	'token',			'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_link_op_multiple",	'ws_timestamp',		'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');


	CreateTableIfNotExist($tab."_link_prod_cat");
	add_if_not_exist($tab."_link_prod_cat",		'ws_author'			,'int(11) 		NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_link_prod_cat",		'ws_draft'			,'BOOLEAN  			NOT NULL DEFAULT FALSE');
	add_if_not_exist($tab."_link_prod_cat",		'ws_id_draft'		,'INT(11) 			NULL DEFAULT 0');
	add_if_not_exist($tab."_link_prod_cat",		'id_cat'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_prod_cat",		'ws_id_ferramenta'	,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_prod_cat",		'id_item'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_prod_cat",		'ws_tool_id'		,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_prod_cat",		'ws_tool_item'		,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_prod_cat",		'ws_nivel'			,'int 		(11)  		NOT NULL default 0');
	add_if_not_exist($tab."_link_prod_cat",		'token'				,'varchar	(300) 			NULL default ""');
	add_if_not_exist($tab."_link_prod_cat",		'ws_timestamp'		,'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	CreateTableIfNotExist($tab."_campos");
	add_if_not_exist($tab."_campos",	'ws_id_ferramenta' 	,			"int 		(11)  		NOT NULL DEFAULT 0"		);
	add_if_not_exist($tab."_campos",	'posicao' 			,			"int 		(11)  		NOT NULL DEFAULT 0"		);
	add_if_not_exist($tab."_campos",	'multiple'			,			"BOOLEAN 				NOT NULL DEFAULT FALSE"	);
	add_if_not_exist($tab."_campos",	'financeiro'		,			"BOOLEAN 				NOT NULL DEFAULT FALSE"	);
	add_if_not_exist($tab."_campos",	'disabled'			,			"BOOLEAN 				NOT NULL DEFAULT FALSE"	);
	add_if_not_exist($tab."_campos",	'password'			,			"BOOLEAN 				NOT NULL DEFAULT FALSE"	);
	add_if_not_exist($tab."_campos",	'numerico'			,			"BOOLEAN 				NOT NULL DEFAULT FALSE"	);
	add_if_not_exist($tab."_campos",	'autosize' 			,			'BOOLEAN 				NOT NULL DEFAULT FALSE'	);
	add_if_not_exist($tab."_campos",	'upload'			,			'BOOLEAN 				NOT NULL DEFAULT FALSE'	);
	add_if_not_exist($tab."_campos",	'calendario'		,			'BOOLEAN 				NOT NULL DEFAULT FALSE'	);
	add_if_not_exist($tab."_campos",	'labelTop'			,			'BOOLEAN 				NOT NULL DEFAULT TRUE'	);
	add_if_not_exist($tab."_campos",	'styles' 			,			'varchar 	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'label' 			,			'varchar 	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'token' 			,			'varchar 	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'listaTabela'		,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'labelSup'			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'place' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'name' 				,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'legenda' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'coluna_mysql' 		,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'caracteres' 		,			'varchar	(20) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'type' 				,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'id_campo' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'editor' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'largura' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'altura' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'filtro' 			,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'values_opt' 		,			'varchar	(300) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'background' 		,			'varchar	(7) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'color' 			,			'varchar	(7) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'sintaxy' 			,			'varchar	(20) 			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'referencia' 		,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'cat_referencia' 	,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'rua'				,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'cidade'			,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'uf'				,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'pais'				,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'cep'				,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'bairro'			,			'varchar	(300)			NULL default ""'	);
	add_if_not_exist($tab."_campos",	'download'			,			'BOOLEAN 				NOT NULL DEFAULT FALSE'	);
	add_if_not_exist($tab."_campos",	'ws_timestamp'		,			'TIMESTAMP 	NOT NULL DEFAULT CURRENT_TIMESTAMP');

	if(isset($_GET['debug']) && $_GET['debug']==1){echo '<pre>'; echo str_replace(';', ';'.PHP_EOL, $GLOBALS["ConfigSQL"]); exit;}
?>
