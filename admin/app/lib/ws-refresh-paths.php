<?

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP")){$path = substr(dirname($_SERVER['REQUEST_URI']),1);define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));}
		if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

	###################################################################
	# CASO EXISTA A CONFIGURAÇÃO, IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	###################################################################
		include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

	#######################################################################
	#	CASO NÃO EXISTA O HTACCESS
	#######################################################################
		if(!file_exists(INCLUDE_PATH.'.htaccess')){
			refresh_Path_AllFiles();
		}

	###################################################################
	#	VERIFICAMOS A VERSÃO DO SISTEMA
	###################################################################
		$NEW_VERSION = json_decode(file_get_contents(INCLUDE_PATH.'admin/app/templates/json/ws-update.json'));

	###################################################################
	#	VERIFICAMOS A VERSÃO DO SISTEMA ANTIGO INSTALADO
	###################################################################
		$ws_direct_access 				= new MySQL();
		$ws_direct_access->set_table(PREFIX_TABLES.'setupdata');
		$ws_direct_access->select();

	###################################################################
	#	VERIFICAMOS SE A VERSÃO DO SISTEMA ANTIGO É MENOR DO QUE A ATUAL
	###################################################################
		$old_version = (empty(@$ws_direct_access->obj[0]->system_version)) ? $NEW_VERSION->version : $ws_direct_access->obj[0]->system_version;
		$new_version = $NEW_VERSION->version;

	###################################################################
	#	CASO SEJA UMA VERSÃO MENOR, REGRAVA OS CAMINHOS
	###################################################################
		if(compare_version($old_version,$new_version,"<")){
			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . 'setupdata');
			$I->set_update('id', 1);
			$I->set_update('system_version',$NEW_VERSION->version);
			$I->salvar();
		}
	