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
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-plugins.html');
	
	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'app/ws-modules/ws-model-tool');
	
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
	$template           						= new Template(TEMPLATE_LINK, true);
	$template->Plugins_Loading					= ws::getLang('Plugins>Loading');
	$template->Plugins_PluginsWebsheep 			= ws::getLang('Plugins>PluginsWebsheep');
	$template->Plugins_CreateNewPlugin			= ws::getLang('Plugins>CreateNewPlugin');
	$template->Plugins_CofigureTool				= ws::getLang('Plugins>CofigureTool');
	$template->Plugins_On						= ws::getLang('Plugins>On');
	$template->Plugins_Off 						= ws::getLang('Plugins>Off');
	$template->Plugins_Version 					= ws::getLang('Plugins>Version');
	$template->Plugins_Author 					= ws::getLang('Plugins>Author');
	$template->Plugins_Path 					= ws::getLang('Plugins>Path');
	$template->Plugins_Keycode 					= ws::getLang('Plugins>Keycode');
	$template->Plugins_Modal_DoYouWantDelete	= ws::getLang('Plugins>Modal>DoYouWantDelete');
	$template->Plugins_Modal_ActionNotBack 		= ws::getLang('Plugins>Modal>ActionNotBack');
	$template->Plugins_Modal_Yes 				= ws::getLang('Plugins>Modal>Yes');
	$template->Plugins_Modal_Cancel 			= ws::getLang('Plugins>Modal>Cancel');
	$template->Plugins_Modal_Excluding 			= ws::getLang('Plugins>Modal>Excluding');
	$template->Plugins_Modal_Loading 			= ws::getLang('Plugins>Modal>Loading');
	$template->Plugins_Modal_NotExist 			= ws::getLang('Plugins>Modal>NotExist');
	$template->Plugins_Modal_CreatingPlugin 	= ws::getLang('Plugins>Modal>CreatingPlugin');
	$template->PATH     = PATH;

	#####################################################  
	# CAPTAMOS O CAMINHO DOS PLUGINS INSTALADOS 
	#####################################################
	$PLUGIN_PATH = ws::includePath.'website/'.$setupdata['url_plugin'];
	
	#####################################################  
	# VERIFICA SE O CAMINHO CADASTRADO É UM DIRETÓRIO  
	#####################################################
	if (is_dir($PLUGIN_PATH)) {
		$dh = opendir($PLUGIN_PATH);

		#####################################################  
		# VARREMOS O DIRETÓRIO DOS PLUGINS  
		#####################################################
		while ($diretorio = readdir($dh)) {
			
			#####################################################  
			# VERIFICA SE O CAMINHO CADASTRADO É UM DIRETÓRIO  
			#####################################################
			if ($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess') {
				
				################################################################  
				# CASO EXISTA O ARQUIVO  "plugin.config.php"
				################################################################
				if (is_dir($PLUGIN_PATH.'/'.$diretorio)) {
					$phpConfig = $PLUGIN_PATH.'/'.$diretorio . '/plugin.config.php';
					if(file_exists($phpConfig)){
						ob_start();
						@include($phpConfig);
						$jsonRanderizado = ob_get_clean();
						$contents        = $plugin;
						###################################################################################################  
						# VERIFICAMOS SE EXISTEM ALGUNS ATRIBUTOS NECESSÁRIO 
						###################################################################################################
						$template->ClasseThumb = "minThumb";
						
						if (isset($contents->PluginPath)) {
							$template->PluginPath = ws::rootPath.$setupdata['url_plugin'] . '/' . $contents->PluginPath;
							$template->dataPath   = ws::rootPath.$setupdata['url_plugin'] . '/' . $contents->PluginPath;
						} else {
							if (empty($contents)) {
								$contents = new stdClass();
							}
							$contents->PluginPath = $diretorio;
							$template->PluginPath = ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio;
							$template->dataPath   = ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio;
						}
						if (isset($contents->pluginName)) {
							$template->pluginName = $contents->pluginName;
						} else {
							$template->pluginName = "Plugin WebSheep";
						}
						if (isset($contents->description)) {
							$template->description = $contents->description;
						} else {
							$contents->description = "Descrição de um novo plugin";
						}
						if (isset($contents->author)) {
							$template->author = $contents->author;
						} else {
							$template->clear("author");
						}
						if (isset($contents->version)) {
							$template->version = $contents->version;
						} else {
							$template->clear("version");
						}
						if (isset($contents->minWsVersion)) {
							$template->minWsVersion = $contents->minWsVersion;
						} else {
							$template->clear("minWsVersion");
						}
						if (isset($contents->avatar) && $contents->avatar != "" && file_exists($PLUGIN_PATH . '/' . $diretorio . '/' . $contents->avatar)) {
							$template->avatar = ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->avatar;
						} else {
							$template->avatar = ws::rootPath."admin/app/templates/img/websheep/engrenagem_plugin.png";
						}

						###################################################################################################  
						# VERIFICAMOS A FORMA QUE SERÁ ABERTO O PLUGIN 
						###################################################################################################
						$innerload = array();
						if (isset($contents->innerload) && count($contents->innerload) >= 1) {
							foreach ($contents->innerload as $key => $value) {
								if (!filter_var($value[0], FILTER_VALIDATE_URL) === false) {
									if ($value[1] == "_self") {
										$target = "_self";
									} else {
										$target = "_blank";
									}
									$innerload[] = '<a href="' . $value[0] . '" 	class="botInnerPluginURL" target="' . $target . '">' . $key . '</a>';
								} else {
									if (empty($value[1]) || @$value[1] == "inner" || @$value[1] == "iframe") {
										$innerload[] = '<a href="' .ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0] . '" 	class="botInnerPlugin" target="inner">' . $key . '</a>';
									} elseif ($value[1] == "_self" || $value[1] == "_blank") {
										$link        = ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0];
										$innerload[] = '<a href="' . $link . '" 	class="botInnerPlugin" target="' . $value[1] . '">' . $key . '</a>';
									} elseif (@$value[1] == "modal") {
										if (empty($value[2])) {
											$value[2] = 500;
										}
										if (empty($value[3])) {
											$value[3] = 500;
										}
										$innerload[] = '<a href="'.ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0] . '" 	class="botInnerPlugin" data-w="' . $value[2] . '" data-h="' . $value[3] . '" target="modal">' . $key . '</a>';
									} elseif (@$value[1] == "popup") {
										if (empty($value[3])) {
											$value[3] = 500;
										}
										if (empty($value[2])) {
											$value[2] = 500;
										}
										$innerload[] = '<a href="' .ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0] . '" 	class="botInnerPlugin" data-w="' . $value[2] . '" data-h="' . $value[3] . '" target="popup">' . $key . '</a>';
									}
								}
							}
							$template->innerload = implode($innerload, " | ");
						} else {
							$template->clear('innerload');
						}
						$links = array();

						###################################################################################################  
						# VERIFICAMOS OS LINKS CADASTRADOS 
						###################################################################################################
						if (isset($contents->links) && count($contents->links) >= 1) {
							foreach ($contents->links as $key => $value) {
								if (!filter_var($value[0], FILTER_VALIDATE_URL) === false) {
									if ($value[1] == "_self") {
										$target = "_self";
									} else {
										$target = "_blank";
									}
									$links[] = '<a href="' . $value[0] . '" 	class="botInnerPluginURL" target="' . $target . '">' . $key . '</a>';
								} else {
									if (empty($value[1]) || @$value[1] == "inner") {
										$links[] = '<a href="' .ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0] . '" 	class="botInnerPlugin" target="inner">' . $key . '</a>';
									} elseif ($value[1] == "_self" || $value[1] == "_blank") {
										$link    = '' .ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0];
										$links[] = '<a href="' . $link . '" 	class="botInnerPlugin" target="' . $value[1] . '">' . $key . '</a>';
									} elseif (@$value[1] == "modal") {
										if (empty($value[2])) {
											$value[2] = 500;
										}
										if (empty($value[3])) {
											$value[3] = 500;
										}
										$links[] = '<a href="' .ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0] . '" 	class="botInnerPlugin" data-w="' . $value[2] . '" data-h="' . $value[3] . '" target="modal">' . $key . '</a>';
									} elseif (@$value[1] == "popup") {
										if (empty($value[3])) {
											$value[3] = 500;
										}
										if (empty($value[2])) {
											$value[2] = 500;
										}
										$links[] = '<a href="' .ws::rootPath.$setupdata['url_plugin'] . '/' . $diretorio . '/' . $value[0] . '" 	class="botInnerPlugin" data-w="' . $value[2] . '" data-h="' . $value[3] . '" target="popup">' . $key . '</a>';
									}
								}
							}
							$template->links = implode($links, " | ");
						} else {
							$template->clear('links');
						}
						
						###################################################################################################  
						# CAPTAMOS O CÓDIGO FDO PLUGIN 
						###################################################################################################
						if (isset($contents->keycode) && $contents->keycode != "") {
							$template->keycode = $contents->keycode;
						} else {
							$template->keycode = "0";
						}
						###################################################################################################  
						# VERIFICAMOS SE O PLUGIN ESTA ATIVO OU NÃO 
						###################################################################################################
						if (file_exists($PLUGIN_PATH . '/' . $diretorio . '/active')) {
							$template->classActive = 'enabled';
						} else {
							$template->classActive = 'disabled';
						}
						################################################################  
						# RETORNAMOS O <LI> DO PLUGIN
						################################################################
						$template->block("BLOCK_PLUGIN");				
					}
				}
			}
		}
	}
	################################################################  
	# CARREGA O CONTEUDO DO BLOCO
	################################################################
	$template->block("BLOCK_MODULE_PLUGINS");
	
	################################################################  
	# PRINTA O HTML DO MODULO
	################################################################
	$template->show();