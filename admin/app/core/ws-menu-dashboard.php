<?
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}

	#####################################################  
	# FUNÇÕES DO MODULO
	#####################################################  
	$user = new Session();

	#####################################################  
	# GET SETUP DATA
	#####################################################
	$setupdata = new MySQL();
	$setupdata->set_table(PREFIX_TABLES . 'setupdata');
	$setupdata->set_order('id', 'DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	
	#####################################################  
	# DEFINE PATH DOS PLUGINS
	#####################################################
	define("PLUGIN_PATH", INCLUDE_PATH.'website/'.$setupdata['url_plugin']);
	
	#####################################################  
	# IMPORTA CLASS DO TEMPLATE
	#####################################################
	$menu_dashboard 				= new Template(INCLUDE_PATH.'admin/app/templates/html/ws-dashboard-menu.html', true);

	##########################################################################################
	#  ANTES DE MONTAR O MENU, VERIFICAMOS A VERSÃO O PAINEL   
	##########################################################################################
	$remoteVersion = json_decode(@file_get_contents("https://raw.githubusercontent.com/websheep/cms/master/admin/app/templates/json/ws-update.json"));
	$localVersion  = json_decode(file_get_contents(INCLUDE_PATH.'admin/app/templates/json/ws-update.json'));
	 if($remoteVersion && version_compare($localVersion->version,$remoteVersion->version)==-1){
	 	$menu_dashboard->newVersion 		= $remoteVersion->version;
	 	$menu_dashboard->newVersionContent 	= implode($remoteVersion->features,"<br>");	
	 	$menu_dashboard->block('NEW_VERSION');
	 } 

	###############################################################################################################  
	# PLUGINS:  Varre os diretórios dos plugins, separa a configuração de cada um deles, e printa a opção no menu 
	###############################################################################################################	
	if (is_dir(PLUGIN_PATH)) {
		$dh = opendir(PLUGIN_PATH);
		while ($diretorio = readdir($dh)) {
			if ($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess') {
				if (file_exists(PLUGIN_PATH . '/' . $diretorio . '/active')) {
					$phpConfig = PLUGIN_PATH . '/' . $diretorio . '/plugin.config.php';
					if (file_exists($phpConfig)) {
						ob_start();
						@include($phpConfig);
						$jsonRanderizado = ob_get_clean();
						$contents        = $plugin;
					}
					if (isset($contents)) {
						if ((isset($contents->menu) && ((is_array($contents->menu) && in_array("lateral", $contents->menu)) || $contents->menu == "lateral")) && isset($contents->painel) && $contents->painel != "") {

							if (isset($contents->loadType) && is_array($contents->loadType)) {
								$dataType = $contents->loadType[0];
								$dataW    = $contents->loadType[1];
								$dataH    = $contents->loadType[2];
							} else {
								$dataType 	= $contents->loadType;
								$dataW 		= 500;
								$dataH 		= 500;
							}
							if (filter_var($contents->painel, FILTER_VALIDATE_URL) === FALSE) {
								$link = ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->painel;
							} else {
								$link = $contents->painel;
							}
							
							########################################################################################################  
							# GUARDA AS INFORMAÇÕES NO TEMPLATE QUE SERÁ UTILIZADO LOGO A FRENTE
							########################################################################################################
							$menu_dashboard->PATH  = $link;
							$menu_dashboard->W     = @$dataW;
							$menu_dashboard->H     = @$dataH;
							$menu_dashboard->TYPE  = $dataType;


							if(isset($contents->icon) && $contents->icon!="" &&	file_exists(ws::includePath.'website/'.$setupdata['url_plugin'] . '/' . $diretorio.'/'.$contents->icon)){
								$menu_dashboard->ICON 	= ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio.'/'.$contents->icon;
							}else{
								$menu_dashboard->clear("ICON");
							}

							$menu_dashboard->LABEL = $contents->pluginName;
							$menu_dashboard->block("PLUGIN");
						}
					}
				}
			}
		}
	}
		
	########################################################################################################  
	# FERRAMENTAS
	########################################################################################################
	$_WS_TOOL_ = new MySQL();
	$_WS_TOOL_->set_table(PREFIX_TABLES . 'ws_ferramentas as tools');
	$_WS_TOOL_->set_colum('tools.id as id_tool');
	$_WS_TOOL_->set_colum('tools._tit_topo_ as titulo');
	$_WS_TOOL_->set_order('posicao', 'ASC');
	$_WS_TOOL_->set_where('_plugin_="0"');
	$_WS_TOOL_->set_where('AND App_Type="1"');
	
	if (SECURE!=FALSE && @$user->get('admin') ==0) {
		$_WS_TOOL_->join(' INNER ', PREFIX_TABLES . 'ws_user_link_ferramenta as link', 'link.id_ferramenta=tools.id AND link.id_user="' . $user->get('id') . '"');
	}
	$_WS_TOOL_->select();
	

	########################################################################################################  
	# GRUPOS
	########################################################################################################
	$_WS_PATH_ = new MySQL();
	$_WS_PATH_->set_table(PREFIX_TABLES . '	ws_path_tools');
	$_WS_PATH_->set_order('posicao','ASC');
	$_WS_PATH_->select();


	########################################################################################################  
	# FOREACH NOS PATHS 
	########################################################################################################
	foreach ($_WS_PATH_->fetch_array as $path) {
		$menu_dashboard->label_path    = $path['path_name'];

		foreach ($_WS_TOOL_->fetch_array as $inner_menu) {
			########################################################################################################  
			# VERIFICAMOS SE ELAS ESTÃO CADASTRADAS NO GRUPO ATUAL
			########################################################################################################
			$_WS_PATH_ = new MySQL();
			$_WS_PATH_->set_table(PREFIX_TABLES . '	ws_link_path_tools');
			$_WS_PATH_->set_where('id_tool="'.$inner_menu['id_tool'].'" AND id_path="'.$path['id'].'"');
			$_WS_PATH_->select();
			if($_WS_PATH_->_num_rows>0){
				$menu_dashboard->ID    = $inner_menu['id_tool'];
				$menu_dashboard->LABEL = $inner_menu['titulo'];
				$menu_dashboard->block("INNER_TOOL");
			}
		}
		$menu_dashboard->block("PATH_TOOL");
	}

	########################################################################################################  
	# FOREACH NAS FERRAMENTAS CRIADAS E GUARDAMOS OS DADOS NO TEMPLATE 
	########################################################################################################

	foreach ($_WS_TOOL_->fetch_array as $tool) {
		########################################################################################################  
		# VERIFICAMOS SE ELAS ESTÃO CADASTRADAS NO GRUPO ATUAL
		########################################################################################################
		$_WS_PATH_ = new MySQL();
		$_WS_PATH_->set_table(PREFIX_TABLES . '	ws_link_path_tools');
		$_WS_PATH_->set_where('id_tool="'.$tool['id_tool'].'" AND id_path="0"');
		$_WS_PATH_->select();
		if($_WS_PATH_->_num_rows>0){
			$menu_dashboard->ID    = $tool['id_tool'];
			$menu_dashboard->LABEL = $tool['titulo'];
			$menu_dashboard->block("TOOL");
		}
	}

	if ( SECURE==FALSE || $user->get('admin')==1) {
		$menu_dashboard->block("ADMIN");
	}

	###################################################################
	# é necessário ter a string contatenada no inicio do ROOT_WEBSHEEP,
	# ñ sei porque ele triplica o valor da constante
	# quando coloco a string no final do ROOT_WEBSHEEP ele triplica msm assim.
	###################################################################
	$menu_dashboard->ROOT_WEBSHEEP 					= ''.ROOT_WEBSHEEP; 
	###################################################################

	$menu_dashboard->label_newversion 				= ws::getlang('dashboard>NewVersion');
	$menu_dashboard->label_paginas 					= ws::getlang('dashboard>lateralMenu>ManagePages');
	$menu_dashboard->label_ferramentas 				= ws::getlang('dashboard>lateralMenu>MyTools>main');
	$menu_dashboard->label_gerenciar_ferramentas 	= ws::getlang('dashboard>lateralMenu>MyTools>manage');
	$menu_dashboard->label_links			 		= ws::getlang('dashboard>lateralMenu>URLsIncludes>main');
	$menu_dashboard->label_url_includes	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>navigation');
	$menu_dashboard->label_url_htaccess	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>htaccess');
	$menu_dashboard->label_include_css	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>css');
	$menu_dashboard->label_include_js	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>js');
	$menu_dashboard->label_plugin 					= ws::getlang('dashboard>lateralMenu>Plugins>main');
	$menu_dashboard->label_gerenciar_plugin 		= ws::getlang('dashboard>lateralMenu>Plugins>manage');
	$menu_dashboard->label_plugin_instalado 		= ws::getlang('dashboard>lateralMenu>Plugins>installedPlugins');
	$menu_dashboard->label_editor	 				= ws::getlang('dashboard>lateralMenu>CodeEditor');
	$menu_dashboard->CodeVisualEditor	 			= ws::getlang('dashboard>lateralMenu>CodeVisualEditor');
	$menu_dashboard->webmaster	 					= ws::getlang('dashboard>lateralMenu>webmaster');

	$menu_dashboard->label_Conf_Painel				= ws::getlang('dashboard>lateralMenu>PanelConfiguration');
	$menu_dashboard->label_Download_Senha			= ws::getlang('dashboard>lateralMenu>DownloadPassword');
	$menu_dashboard->label_Cadastros				= ws::getlang('dashboard>lateralMenu>AdditionalRegistrations');
	$menu_dashboard->label_BKP	 					= ws::getlang('dashboard>lateralMenu>BKPCentral');
	$menu_dashboard->label_Usuarios					= ws::getlang('dashboard>lateralMenu>Users');
	$menu_dashboard->label_Biblioteca				= ws::getlang('dashboard>lateralMenu>ImageLibrary');
	$menu_dashboard->label_hd 						= ws::getlang('dashboard>lateralMenu>HDManagement');
	$menu_dashboard->logRecords 					= ws::getlang('dashboard>lateralMenu>logRecords');
	$menu_dashboard->label_logout					= ws::getlang('dashboard>lateralMenu>Logout');
	// $menu_dashboard->label_reportBugs				= ws::getlang('dashboard>lateralMenu>reportBugs');
	// $menu_dashboard->label_update					= ws::getlang('dashboard>lateralMenu>update');

	$menu_dashboard->dashboard_modal_logOut_content	= ws::getlang('dashboard>modal>logOut>content');
	$menu_dashboard->dashboard_modal_logOut_bot1	= ws::getlang('dashboard>modal>logOut>bot1');
	$menu_dashboard->dashboard_modal_logOut_bot2	= ws::getlang('dashboard>modal>logOut>bot2');

	$menu_dashboard->label_logout_loading			= ws::getlang('dashboard>modal>logOut>loading');
	$menu_dashboard->ASH_UPDATE						= ws::setTokenRest('2 days');

	$menu_dashboard->block("MENU_DASHBOARD");
	$menu_dashboard->show();