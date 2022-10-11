<?php
	#####################################################  controla o CACHE
	@header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	@header("Cache-Control: no-store, no-cache, must-revalidate");
	@header("Cache-Control: post-check=0, pre-check=0", false);
	@header("Pragma: no-cache");
	define("PATH", 'app/ws-modules/ws-model-tool');
	clearstatcache();
/*
PAGE=1
&LIMIT=50
&token_group=OP49Q4O5fB0AiogGCU7lLKHyMhh1
&ws_id_ferramenta=9
&id_cat=0
&ws_nivel=0
&id_item=10

*/
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

	#####################################################  FUNÇÕES DO MODULO
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	_session();
	$session = new session();
	#####################################################  
	#DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-details-new-template.html');

	#####################################################  
	#DEFINE O LINK DO TEMPLATE DOS INPUTS DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_INPUT_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tool-details-inputs-template.html');

	######################################################
	######################################################
	######################################################
	######################################################
	$session->set('id_cat', empty($_GET['id_cat']) ? "1" : $_GET['id_cat']);
	$session->set('ws_nivel', empty($_GET['ws_nivel']) ? "1" : $_GET['ws_nivel']);

	define("ID_FERRAMENTA", $_GET['ws_id_ferramenta']);

	#####################################################  
	# GRAVAMOS A LARGURA DO PALCO BASE
	#####################################################
	define("WSTAGE",1022);

	if(empty($_GET['LIMIT'])){			$_GET['LIMIT']="50";														}
	if(empty($_GET['PAGE'])){			$_GET['PAGE']="1";															}
	if(empty($_GET['token_group'])){	$_GET['token_group']=_token(PREFIX_TABLES."ws_biblioteca","token_group");	}


	#####################################################  
	# 	PEGA O SETUP DATA
	#####################################################  
	$SETUP = new MySQL();
	$SETUP->set_table(PREFIX_TABLES . 'setupdata');
	$SETUP->set_where('id="1"');
	$SETUP->debug(0);
	$SETUP->select();
	$SETUP      = $SETUP->fetch_array[0];
	#####################################################  
	#    SELECIONA A FERRAMENTA SETADA
	#####################################################  
	$FERRAMENTA = new MySQL();
	$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
	$FERRAMENTA->set_where('id="' . ID_FERRAMENTA . '"');
	$FERRAMENTA->debug(0);
	$FERRAMENTA->select();
	$_FERRAMENTA_  	= $FERRAMENTA->fetch_array[0];
	$_SLUG_ 		= $_FERRAMENTA_['slug'];



	#####################################################
	#    SETA NA SESSÃO O ID DO ÍTEM
	#####################################################
	$session->set('id_item',$_GET['id_item']);	

	##########################################################################################################
	# 	SEPARAMOS OS CAMPOS DESTE ÍTEM
	##########################################################################################################
	$campos = new MySQL();
	$campos->set_table(PREFIX_TABLES . $_SLUG_.'_campos');
	$campos->set_order("posicao", "ASC");
	$campos->set_where('ws_id_ferramenta="' . ID_FERRAMENTA . '"');
	$campos->select();

	##########################################################################################################
	# VERIFICA SE JÁ TEM RASCUNHO
	##########################################################################################################
	$draft = new MySQL();
	$draft->set_table(PREFIX_TABLES . $_SLUG_ . "_item");
	$draft->set_where('ws_draft="1"');
	$draft->set_where('AND ws_id_draft="' . $session->get('id_item') . '"');
	$draft->select();


	##########################################################################################################
	#     SELECIONA O ÍTEM
	##########################################################################################################
	$produto = new MySQL();
	$produto->set_table(PREFIX_TABLES . $_SLUG_. "_item");

	// caso nao exista rascunho ou seja pedido a visualização do arquivo original    
	if((empty($_GET['original']) || $_GET['original'] == 'false')) {
		criaRascunho(ID_FERRAMENTA,$_GET['id_item']);
		$produto->set_where('ws_draft="1"');
		$produto->set_where('AND ws_id_draft="' . $session->get('id_item') . '"');
		$_IF_ORIGINAL 		= 'false';

	} else {

		$produto->set_where('id="' . $session->get('id_item') . '"');
		$_IF_ORIGINAL 		= 'true';

	}

	// separamos os campos necessários desta ferramenta
	foreach($campos->fetch_array as $value) {
		$produto->set_colum($value['coluna_mysql']);
	}

	$produto->set_colum('token');
	$produto->select();
	$produto = @$produto->fetch_array[0];

	##########################################################################################################
	# caso nao tenha nenhum ítem setado, pega o ID 1    
	##########################################################################################################
		if($session->get('id_cat') == 'null') {
			$session->set('id_cat','1');

		} elseif($session->get('id_cat') == 'nullback') {
			$session->set('id_cat',$produto['id_cat']);
		}

		if($_FERRAMENTA_['_niveis_'] >= 0) {
			$link_back = './' . PATH . '/itens.php?LIMIT='.$_GET['LIMIT'].'&PAGE='.$_GET['PAGE'].'&token_group='.$_GET['token_group'].'&ws_id_ferramenta='.ID_FERRAMENTA;
		}
	##########################################################################################################
	# SEPARA TODOS OS CAMPOS DE
	##########################################################################################################
		$typeColumns = array();
		foreach($campos->fetch_array as $a)
			$typeColumns[] = '"id":"' . $a['id_campo'] . '","financeiro":"' . $a['financeiro'] . '","type":"' . $a['type'] . '","coluna_mysql":"' . $a['coluna_mysql'] . '","editor":"' . $a['editor'] . '","name":"' . $a['name'] . '"';
		$typeColumns = '{' . implode($typeColumns, '},{') . '}';
	##########################################################################################################
	# SEPARA TODOS PLUGINS
	##########################################################################################################
		$path        = INCLUDE_PATH.'website/' . $SETUP['url_plugin'];
		$dh          = @opendir('./../../../' . $path);
		$short       = array();
		while($diretorio = @readdir($dh)) {
			if($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess') {
				$jsonConfig = $path . '/' . $diretorio . '/plugin.config.json';
				$phpConfig  = $path . '/' . $diretorio . '/plugin.config.php';
				if(file_exists($phpConfig)) {
					ob_start();
					@include($phpConfig);
					$jsonRanderizado = ob_get_clean();
					$contents        = $plugin;
				} elseif(file_exists($jsonConfig)) {
					$contents = json_decode(file_get_contents($jsonConfig));
				}
				if((isset($contents->menu) && is_array($contents->menu) && in_array("textarea", $contents->menu))) {
					$hortcode = 'window.comboPlugins.push([\'[ws]{"slug":"' . @$contents->slug . '"';
					$arrReq   = array();
					foreach($contents->requiredData as $req) {
						if(is_array($req)) {
							$r    = array_slice($req, 1);
							$data = array();
							foreach($r as $d) {
								if(is_string($d)) {
									$data[] = '"' . $d . '"';
								} else {
									$data[] = $d;
								}
							}
							if(count($data) > 1) {
								$data = '[' . implode($data, ',') . ']';
							} else {
								$data = implode($data);
							}
							$arrReq[] = '"' . $req[0] . '":' . $data;
						} else {
							$arrReq[] = '"' . $req . '":""';
						}
					}
					$hortcode .= ',' . implode($arrReq, ",");
					$hortcode .= '}[/ws]\', "' . $contents->pluginName . '","' . $contents->pluginName . '"';
					$hortcode .= ']);';
					$short[] = $hortcode;
				}
			}
		}
		$comboPlugins  = Implode($short, PHP_EOL);
	##########################################################################################################
	# STYLE PERSONALIZADO DO CKEDITOR
	##########################################################################################################
		$styleCKEditor = urldecode($SETUP['stylejson']);
		if($styleCKEditor == "") {
			$styleCKEditor = "[];";
		} else {
			$styleCKEditor = urldecode($styleCKEditor);
		}
	##########################################################################################################
	# DEFINIMOS AS CONSTANTES UTILIZADAS NO PAINEL
	##########################################################################################################
		define("ID_ITEM", $session->get('id_item'));
		define("ID_CAT", '');
		define("WS_NIVEL", $session->get('ws_nivel'));
	/*##############################################################################################################################*/
	/*##############################################################################################################################*/
	/* -- ! LISTA OS CAMPOS NECESSÁRIOS NESSE MÓDULO ! -- */
	/*##############################################################################################################################*/
	/*##############################################################################################################################*/
	
		$_IPUNT_CAMPOS = "";
		foreach($campos->fetch_array as $k) {
			//#####################################################################        iFrame
			if($k['type'] == 'iframe') {

				$camposArray = array();
				foreach($campos->fetch_array as $campoMySQL) {
					if($k['coluna_mysql'] != $campoMySQL['coluna_mysql'] && $campoMySQL['coluna_mysql'] != "") {
						$camposArray[] = 'ws[' . $campoMySQL['coluna_mysql'] . ']=' . urlencode($produto[$campoMySQL['coluna_mysql']]);
					}
				}
				$camposArray[]                      = 'ws[ferramenta]=' . ID_FERRAMENTA;
				$camposArray[]                      = 'ws[nivel]=' . WS_NIVEL;
				$camposArray[]                      = 'ws[cat]=' . ID_CAT;
				$camposArray[]                      = 'ws[item]=' . ID_ITEM;
				$vars                               = implode($camposArray, '&');
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct($k['largura'],WSTAGE);
				$_SET_TEMPLATE_INPUT->WIDTH_ADD     = pxToPct(($k['largura'] + 20),WSTAGE);;
				$_SET_TEMPLATE_INPUT->NAME          = $k['name'];
				$_SET_TEMPLATE_INPUT->HEIGHT        = $k['altura'];
				$_SET_TEMPLATE_INPUT->VALUE_OPT     = (strpos($k['values_opt'], "?") === false) ? $k['values_opt'] . '?' . $vars : $k['values_opt'] . '&' . $vars;
				$_SET_TEMPLATE_INPUT->block("BLOCK_INPUT_IFRAME");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        Key Works
			if($k['type'] == 'key_works') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$D                                  = new MySQL();
				$D->set_table(PREFIX_TABLES . $_SLUG_.'_item');
				if((isset($_GET['original']) && $_GET['original'] == 'true') || $draft->_num_rows == 0) {
					$D->set_where('id="' . ID_ITEM . '"');
				} else {
					$D->set_where('ws_draft="1"');
					$D->set_where('AND ws_id_draft="' . ID_ITEM . '"');
				}
				$D->select();
				$arraykey   = explode(',', $D->fetch_array[0][$k['coluna_mysql']]);
				$_new_array = array();
				foreach($arraykey as $keywork) {
					if(!empty($keywork)) {
						$_new_array[] = '"' . str_replace(array('"'), array("'"), $keywork) . '"';
					}
				}
				$_SET_TEMPLATE_INPUT->TOKEN    = $k['token'];
				$_SET_TEMPLATE_INPUT->MYSQL    = $k['coluna_mysql'];
				$_SET_TEMPLATE_INPUT->ID       = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->LABEL    = $k['label'];
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->TAGS     = implode(',', $_new_array);
				$_SET_TEMPLATE_INPUT->block("BLOCK_KEYWORKS_ADD");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        thumbmail
			if($k['type'] == 'thumbmail') {
				$recuow= 2; /* X */ $recuoh=-20; $margin = 3;
				$_SET_TEMPLATE_INPUT         = new Template(TEMPLATE_INPUT_LINK, true);			
				$width = ($k['largura'] - $recuow - $margin);


				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->TOKEN  		= $k['token'];
				$_SET_TEMPLATE_INPUT->MYSQL  		= $k['coluna_mysql'];
				$_SET_TEMPLATE_INPUT->WIDTH  		= pxToPct($width,WSTAGE);
				$_SET_TEMPLATE_INPUT->DATA_WIDTH  	= $width;
				$_SET_TEMPLATE_INPUT->HEIGHT 		= $k['altura'] - $recuoh - $margin;
				$_SET_TEMPLATE_INPUT->SRC    		= ws::rootPath.'ws-img/' . ($k['largura'] - $recuow - $margin) . '/' . ($k['altura'] - $recuoh - $margin) . '/100/' . $produto[$k['coluna_mysql']];
				$_SET_TEMPLATE_INPUT->block("BLOCK_THUMBMAIL");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        PLAYER MP3
			if($k['type'] == 'playerMP3') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}
				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$recuow                             = 14;
				$recuoh                             = 9;
				$_SET_TEMPLATE_INPUT->TOKEN         = $k['token'];
				$_SET_TEMPLATE_INPUT->MYSQL         = $k['coluna_mysql'];
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura'] - $recuow),WSTAGE);
				$_SET_TEMPLATE_INPUT->HEIGHT        = $k['altura'] + $recuoh;
				$_SET_TEMPLATE_INPUT->LABELSUP      = $k['labelSup'];
				if(isset($produto[$k['coluna_mysql']]) && $produto[$k['coluna_mysql']] != "") {
				
					$url       = $produto[$k['coluna_mysql']];
					$options   = array(
						CURLOPT_SSL_VERIFYHOST 	=> false,
						CURLOPT_SSL_VERIFYPEER 	=> false,
						CURLOPT_RETURNTRANSFER 	=> true, 
						CURLOPT_HEADER 			=> false, 
						CURLOPT_FOLLOWLOCATION 	=> true, 
						CURLOPT_ENCODING 		=> "", 	
						CURLOPT_USERAGENT 		=> "WebSheep",
						CURLOPT_AUTOREFERER 	=> true, 
						CURLOPT_CONNECTTIMEOUT 	=> 120, 
						CURLOPT_TIMEOUT 		=> 120,	
						CURLOPT_MAXREDIRS 		=> 10 
					);
					$ch        = curl_init($url);
					curl_setopt_array($ch, $options);
					$content = curl_exec($ch);
					$err     = curl_errno($ch);
					$errmsg  = curl_error($ch);
					$header  = curl_getinfo($ch);
					curl_close($ch);
					$header['errno']   = $err;
					$header['errmsg']  = $errmsg;
					$header['content'] = $content;
					$urlParsed 			= parse_url($url);
					$html              = new DOMDocument();
					libxml_use_internal_errors(true);
					$html->loadHTML($content);
					foreach ($html->getElementsByTagName('meta') as $meta) {
						if ($urlParsed['host'] == 'mixcloud.com' || $urlParsed['host'] == 'www.mixcloud.com') {
							if ($meta->getAttribute('name') == 'twitter:player') {
								$template = str_replace('', '', $meta->getAttribute('content'));
								$_SET_TEMPLATE_INPUT->URL = $template;
								break;
							}
						} elseif ($urlParsed['host'] == 'soundcloud.com' || $urlParsed['host'] == 'www.soundcloud.com') {
							if ($meta->getAttribute('property') == 'twitter:player') {
								$template = str_replace('visual=true', 'visual=false', $meta->getAttribute('content'));
								$_SET_TEMPLATE_INPUT->URL = $template;
								break;
							}
						}
					}
				} else {
					$_SET_TEMPLATE_INPUT->clear('URL');
				}
				$_SET_TEMPLATE_INPUT->block("BLOCK_PLAYER_MP3");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        PLAYER VIDEO
			if($k['type'] == 'playerVideo') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}
				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$recuow                             = 4;
				$recuoh                             = 9;
				if(isset($produto[$k['coluna_mysql']]) && $produto[$k['coluna_mysql']] != "") {
					$urlVideo   = $produto[$k['coluna_mysql']];
					$urlExplode = parse_url($urlVideo);
					$query      = array();
					parse_str(@$urlExplode['query'], $query);
					$urlExplode['query'] = $query;
					if($urlExplode['host'] == "youtube.com" || $urlExplode['host'] == "www.youtube.com") {
						$urlThumb = "http://img.youtube.com/vi/" . $urlExplode['query']['v'] . "/hqdefault.jpg";
					} elseif($urlExplode['host'] == "vimeo.com" || $urlExplode['host'] == "www.vimeo.com") {
						$url      = 'http://vimeo.com/api/v2/video' . $urlExplode['path'] . '.php';
						$contents = @file_get_contents($url);
						$array    = @unserialize(trim($contents));
						$urlThumb = $array[0]['thumbnail_large'];
					}
					$size = @getimagesize($urlThumb);
					$w    = $size[0];
					$h    = $size[1];
					if($w > $h) {
						$_SET_TEMPLATE_INPUT->styleThumb = "height:calc(100% + 10px);width:auto;";
					} else {
						$_SET_TEMPLATE_INPUT->styleThumb = "height:auto;width:calc(100% + 10px);";
					}
					$_SET_TEMPLATE_INPUT->SRC = $urlThumb;
				} else {
					$_SET_TEMPLATE_INPUT->clear('styleThumb');
					$_SET_TEMPLATE_INPUT->clear('SRC');
				}
				$_SET_TEMPLATE_INPUT->TOKEN    = $k['token'];
				$_SET_TEMPLATE_INPUT->MYSQL    = $k['coluna_mysql'];
				$_SET_TEMPLATE_INPUT->WIDTH    = pxToPct(($k['largura'] - $recuow),WSTAGE);
				$_SET_TEMPLATE_INPUT->HEIGHT   = $k['altura'] + $recuoh;
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_PLAYER_VIDEO");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
				/**/
			}
			//#####################################################################        UPLOAD ARQUIVO
			if($k['type'] == 'file') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$Old                                = new MySQL();
				$Old->set_table(PREFIX_TABLES . $_SLUG_ . '_files');
				$Old->set_where('token="' . $k['token'] . '"');
				$Old->set_where('AND painel="1"');
				$Old->set_where('AND id_item="' . ID_ITEM . '"');
				$Old->select();
				$recuow = 18;
				if($Old->_num_rows != 0) {
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $Old->fetch_array[0]['filename'] . '"';
				} else {
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				}
				$_SET_TEMPLATE_INPUT->TOKEN    = $k['token'];
				$_SET_TEMPLATE_INPUT->MYSQL    = $k['coluna_mysql'];
				$_SET_TEMPLATE_INPUT->LABEL    = $k['label'];
				$_SET_TEMPLATE_INPUT->DOWNLOAD = $k['download'];
				$_SET_TEMPLATE_INPUT->WIDTH    = pxToPct(($k['largura'] - $recuow),WSTAGE);
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_UPLOAD_FILE");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        LABEL SIMPLES
			if($k['type'] == 'label') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";
				}else{
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";
				}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->LABEL         = $k['label'];
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura'] - 20),WSTAGE);
				$_SET_TEMPLATE_INPUT->block("BLOCK_LABEL");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        ESPAÇO EM VAZIU
			if($k['type'] == 'vazio') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->LABEL         = $k['label'];
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura']),WSTAGE);
				$_SET_TEMPLATE_INPUT->HEIGHT        = $k['altura'] + 18;
				$_SET_TEMPLATE_INPUT->block("BLOCK_VAZIO");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//##################################################################### QUEBRA DE LINHA
			if($k['type'] == 'quebra') {
				$_SET_TEMPLATE_INPUT = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->block("BLOCK_BR");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################SEPARADOR
			if($k['type'] == 'separador') {
				$_SET_TEMPLATE_INPUT        = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->LABEL = $k['label'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_SEPARADOR");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################COLORPICKER
			if($k['type'] == 'colorpicker') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				if($k['legenda'] != '')
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				else
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				$_SET_TEMPLATE_INPUT->TOKEN    = $k['token'];
				$_SET_TEMPLATE_INPUT->MYSQL    = $k['coluna_mysql'];
				$_SET_TEMPLATE_INPUT->LABEL    = $k['label'];
				$_SET_TEMPLATE_INPUT->WIDTH    = pxToPct(($k['largura']),WSTAGE);
				$_SET_TEMPLATE_INPUT->COR      = $produto[$k['coluna_mysql']];
				$_SET_TEMPLATE_INPUT->HEIGHT   = $k['altura'] - 12;
				$_SET_TEMPLATE_INPUT->ID_CAMPO = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_COLORPICKER");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################    INPUT TEXT
			if($k['type'] == 'input') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->TYPE          = 'type="text"';
				if($k['legenda'] != '')
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				else
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				if($k['disabled'] == '1')
					$_SET_TEMPLATE_INPUT->DISABLED = 'readonly="readonly"';
				else
					$_SET_TEMPLATE_INPUT->clear('DISABLED');
				if($k['financeiro'] == '1')
					$_SET_TEMPLATE_INPUT->FINANCEIRO = 'financeiro';
				else
					$_SET_TEMPLATE_INPUT->clear('FINANCEIRO');
				if($k['filtro'] != '')
					$_SET_TEMPLATE_INPUT->FILTRO = 'filtro="' . $k['filtro'] . '"';
				else
					$_SET_TEMPLATE_INPUT->clear('FILTRO');
				if($k['numerico'] == '1')$_SET_TEMPLATE_INPUT->TYPE = 'type="number"';
				if($k['calendario'] == "1")$_SET_TEMPLATE_INPUT->TYPE = 'type="date"';
				if($k['password'] == "1") $_SET_TEMPLATE_INPUT->TYPE = 'type="password"';
				$_SET_TEMPLATE_INPUT->ID         = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->NAME       = $k['name'];
				$_SET_TEMPLATE_INPUT->PLACE      = $k['place'];
				$_SET_TEMPLATE_INPUT->LABELSUP   = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->LABEL      = $k['label'];
				$_SET_TEMPLATE_INPUT->CARACTERES = $k['caracteres'];
				$_SET_TEMPLATE_INPUT->WIDTH      = pxToPct(($k['largura']+ 9),WSTAGE);


				$_SET_TEMPLATE_INPUT->VALUE      = str_replace('"', "'", urldecode($produto[$k['coluna_mysql']]));

				if($k['values_opt'] == "on") {
					$_SET_TEMPLATE_INPUT->inputs = "";
					if($k['rua'] != "") {
						$_SET_TEMPLATE_INPUT->inputs .= 'if(addressType=="route"){                         $("#' . $k['rua'] . '").val(cont);        }' . PHP_EOL;
					}
					if($k['cidade'] != "") {
						$_SET_TEMPLATE_INPUT->inputs .= 'if(addressType=="locality"){                     $("#' . $k['cidade'] . '").val(cont);    }' . PHP_EOL;
					}
					if($k['uf'] != "") {
						$_SET_TEMPLATE_INPUT->inputs .= 'if(addressType=="administrative_area_level_1"){     $("#' . $k['uf'] . '").val(cont);        }' . PHP_EOL;
					}
					if($k['pais'] != "") {
						$_SET_TEMPLATE_INPUT->inputs .= 'if(addressType=="country"){                         $("#' . $k['pais'] . '").val(cont);        }' . PHP_EOL;
					}
					if($k['cep'] != "") {
						$_SET_TEMPLATE_INPUT->inputs .= 'if(addressType=="postal_code"){                     $("#' . $k['cep'] . '").val(cont);        }' . PHP_EOL;
					}
					if($k['bairro'] != "") {
						$_SET_TEMPLATE_INPUT->inputs .= '$("#' . $k['bairro'] . '").val(place.vicinity);' . PHP_EOL;
					}
					//$_SET_TEMPLATE_INPUT->block("GOOGLE_API_PLACE");
				} else {
					$_SET_TEMPLATE_INPUT->clear("inputs");
				}
				;
				$_SET_TEMPLATE_INPUT->block("BLOCK_INPUT_TEXT");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################    TEXTAREA
			if($k['type'] == 'textarea') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ 
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";
				}else{
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";
				}
				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->TOKEN         = _crypt();
				$_SET_TEMPLATE_INPUT->GROUP   		= $_GET['token_group'];
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$recuoh                             = -50;
				if($k['disabled'] == '1'){
					$_SET_TEMPLATE_INPUT->DISABLED = 'readonly="readonly"';
				}else{
					$_SET_TEMPLATE_INPUT->clear('DISABLED');
				}
				if($k['legenda'] != ""){
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				}else{
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				}
				if($k['editor'] == '1'){
					$_SET_TEMPLATE_INPUT->EDITOR = '1';
				}else{
					$_SET_TEMPLATE_INPUT->EDITOR = '0';
				}
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->LABEL    = $k['label'];

				if(empty($k['background'])) {
					$k['background'] = "#FFF";
				}
				if($k['caracteres'] == "") {
					$_SET_TEMPLATE_INPUT->CARACTERES = '9999999';
				} else {
					$_SET_TEMPLATE_INPUT->CARACTERES = $k['caracteres'];
				}

				$_SET_TEMPLATE_INPUT->WIDTH        = pxToPct($k['largura']-1,WSTAGE);
				$_SET_TEMPLATE_INPUT->HEIGHT       = $k['altura'] + $recuoh;
				$_SET_TEMPLATE_INPUT->INPUT_WIDTH  = $k['largura'];
				$_SET_TEMPLATE_INPUT->INPUT_HEIGHT = $k['altura'] + 3;
				$_SET_TEMPLATE_INPUT->ID           = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->NAME         = $k['name'];
				$_SET_TEMPLATE_INPUT->PLACE        = $k['place'];
				$_SET_TEMPLATE_INPUT->BACKGROUND   = str_replace("#", "", $k['background']);
				$_SET_TEMPLATE_INPUT->COLOR        = str_replace("#", "", $k['color']);
				$_SET_TEMPLATE_INPUT->CONTEUDO     = str_replace("\n","ws_eol",addslashes(urldecode($produto[$k['coluna_mysql']])));
				$_SET_TEMPLATE_INPUT->block("BLOCK_TEXTAREA");
				//$_SET_TEMPLATE_INPUT->IF_ORIGINAL 	= $_IF_ORIGINAL;

				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################    SINTAXY
			if($k['type'] == 'editor') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ 
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";
				}else{
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";
				}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$recuoh                             = 7;
				$recuow                             = 0;
				$_SET_TEMPLATE_INPUT->HEIGHT        = $k['altura'] + $recuoh;
				$_SET_TEMPLATE_INPUT->INPUT_WIDTH   = pxToPct($k['largura']+ $recuow,WSTAGE);
				$_SET_TEMPLATE_INPUT->INPUT_HEIGHT  = $k['altura'] + 3;
				$_SET_TEMPLATE_INPUT->ID            = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->NAME          = $k['name'];
				$_SET_TEMPLATE_INPUT->SINTAXY       = $k['sintaxy'];
				$_SET_TEMPLATE_INPUT->LABELSUP      = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->CONTEUDO      = mysqli_real_escape_string($_conectMySQLi_, $produto[$k['coluna_mysql']]);
				$_SET_TEMPLATE_INPUT->block("BLOCK_SINTAXY");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################    BOTÃO ARQUIVOS INTERNOS
			if($_FERRAMENTA_['_files_'] == "1" && $k['type'] == 'bt_files') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura']- 22),WSTAGE);
				$_SET_TEMPLATE_INPUT->LABEL         = $k['label'];
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = $session->get('ws_nivel');
				$_SET_TEMPLATE_INPUT->block("BLOCK_BOT_FILE");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################    BOTÃO GALERIAS DE FOTOS
			if($_FERRAMENTA_['_galerias_'] == "1" && $k['type'] == 'bt_galerias') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->GROUP       	= $_GET['token_group'];
				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura']- 22),WSTAGE);
				$_SET_TEMPLATE_INPUT->LABEL         = $k['label'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_BOT_GALERIAS");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        BOTÃO DE FOTOS
			if($_FERRAMENTA_['_fotos_'] == "1" && $k['type'] == 'bt_fotos') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->GROUP       	= $_GET['token_group'];
				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura']- 22),WSTAGE);
				$_SET_TEMPLATE_INPUT->LABEL         = $k['label'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_BOT_FOTOS");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################            CHECK BOX
			if($k['type'] == 'check') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				if($k['disabled'] == '1')
					$_SET_TEMPLATE_INPUT->DISABLED = 'readonly="readonly"';
				else
					$_SET_TEMPLATE_INPUT->clear('DISABLED');
				if($k['legenda'] != '')
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				else
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				if($produto[$k['coluna_mysql']] == '1') {
					$_SET_TEMPLATE_INPUT->CHECKED = 'checked';
					$_SET_TEMPLATE_INPUT->VALUE   = "1";
				} else {
					$_SET_TEMPLATE_INPUT->clear('CHECKED');
					$_SET_TEMPLATE_INPUT->VALUE = "0";
				}
				$_SET_TEMPLATE_INPUT->ID       = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->WIDTH    = pxToPct(($k['largura']),WSTAGE);
				$_SET_TEMPLATE_INPUT->NAME     = $k['name'];
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->LABEL    = $k['label'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_CHECKBOX");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        RADIO BOX
			if($k['type'] == 'radiobox') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				if(urldecode($produto[$k['coluna_mysql']]) == $k['label'])
					$_SET_TEMPLATE_INPUT->CHECK = 'checked="true"';
				else
					$_SET_TEMPLATE_INPUT->clear('CHECK');
				if($k['legenda'] != '')
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				else
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				if($k['disabled'] == '1')
					$_SET_TEMPLATE_INPUT->DISABLED = 'readonly';
				else
					$_SET_TEMPLATE_INPUT->clear('DISABLED');
				$_SET_TEMPLATE_INPUT->WIDTH    = pxToPct(($k['largura'] + 5),WSTAGE);
				$_SET_TEMPLATE_INPUT->NAME     = $k['name'];
				$_SET_TEMPLATE_INPUT->PLACE    = $k['place'];
				$_SET_TEMPLATE_INPUT->LABEL    = $k['label'];
				$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_RADIOBOX");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
			//#####################################################################        SELECT BOX
			if($k['type'] == 'selectbox') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);

				if($k['labelTop']==0){ 
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";
				}else{
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";
				}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct($k['largura'],WSTAGE);
				$_SET_TEMPLATE_INPUT->ID            = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->NAME          = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->PLACE         = $k['place'];
				$_SET_TEMPLATE_INPUT->LABELSUP      = $k['labelSup'];
				if($k['disabled'] == '1')
					$_SET_TEMPLATE_INPUT->DISABLED = 'readonly="readonly"';
				else
					$_SET_TEMPLATE_INPUT->clear('DISABLED');
				if($k['legenda'] != '')
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				else
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				$opcoes = explode('|', $k['values_opt']); //sort($opcoes);
				foreach($opcoes as $key => $opcoes_label) {
					if($opcoes_label != "") {
						$_SET_TEMPLATE_INPUT->NAMEOPT = $k['name'];
						$_SET_TEMPLATE_INPUT->OP      = $opcoes_label;
						if(urldecode($produto[$k['coluna_mysql']]) == $opcoes_label)
							$_SET_TEMPLATE_INPUT->CHECKOPT = 'selected';
						else
							$_SET_TEMPLATE_INPUT->clear('CHECKOPT');
						$_SET_TEMPLATE_INPUT->block("BLOCK_SELECTBOX_OPT");
					}
				}
				$_SET_TEMPLATE_INPUT->block("BLOCK_SELECTBOX");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
		//#####################################################################        SELECT BOX COM ÍTENS DE OUTRA FERRAMENTA
		//#####################################################################        LINK TOOL
			if($k['type'] == 'linkTool') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ 
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";
				}else{
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";
				}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->ID            = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->PLACE         = $k['label'];

				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura']),WSTAGE);
				if($k['multiple'] == "0") {
					$_SET_TEMPLATE_INPUT->LABELSUP = $k['labelSup'];
					if($k['filtro'] == "item") {

						$FERRAMENTA_LINK = new MySQL();
						$FERRAMENTA_LINK->set_table(PREFIX_TABLES . 'ws_ferramentas');
						$FERRAMENTA_LINK->set_where('id="' . $k['values_opt'] . '"');
						$FERRAMENTA_LINK->debug(0);
						$FERRAMENTA_LINK->select();
						$FERRAMENTA_LINK  	= $FERRAMENTA_LINK->fetch_array[0]['slug'];

						$toolLink = new MySQL();
						$toolLink->set_table(PREFIX_TABLES . $FERRAMENTA_LINK.'_item');
						$toolLink->set_where('ws_id_ferramenta="' . $k['values_opt'] . '"');
						$toolLink->set_where('AND ws_draft="0"');
						$toolLink->set_where('AND id<>"'.$session->get('id_item').'"');
						if($k['cat_referencia'] != "") {
							$toolLink->set_where('AND id_cat="' . $k['cat_referencia'] . '"');
						}
						$toolLink->select();
						foreach($toolLink->fetch_array as $item) {
							$_SET_TEMPLATE_INPUT->NAMEOPT = $k['id_campo'];
							$_SET_TEMPLATE_INPUT->VALUE   = $item['id'];
							$_SET_TEMPLATE_INPUT->LABEL   = urldecode($item[$k['referencia']]);
							if($produto[$k['coluna_mysql']] == $item['id']){
								$_SET_TEMPLATE_INPUT->CHECKOPT = 'selected';
							}else{
								$_SET_TEMPLATE_INPUT->clear('CHECKOPT');
							}
							$_SET_TEMPLATE_INPUT->block("BLOCK_SELECTBOX_OPT_TOOL");
						}
						$_SET_TEMPLATE_INPUT->block("BLOCK_SELECTBOX_LINK_TOOL");
					}elseif($k['filtro'] == "cat") {
						$toolLink = new MySQL();
						$toolLink->set_table(PREFIX_TABLES . $_SLUG_ . '_cat');
						$toolLink->set_where('id_cat="' . $k['cat_referencia'] . '"');
						$toolLink->set_where('AND ws_id_ferramenta="' . $k['values_opt'] . '"');
						$toolLink->select();
						foreach($toolLink->fetch_array as $item) {
							$_SET_TEMPLATE_INPUT->NAMEOPT = $item['id'];
							$_SET_TEMPLATE_INPUT->VALUE   = $item['id'];
							$_SET_TEMPLATE_INPUT->LABEL   = urldecode($item['titulo']);
							if($produto[$k['coluna_mysql']] == $item['id']){
								$_SET_TEMPLATE_INPUT->CHECKOPT = 'selected';
							}else{
								$_SET_TEMPLATE_INPUT->clear('CHECKOPT');
							}
							$_SET_TEMPLATE_INPUT->block("BLOCK_SELECTBOX_OPT_TOOL");
						}
					}
				} else {
					$_SET_TEMPLATE_INPUT->WIDTH = pxToPct(($k['largura'] - 20),WSTAGE);
					$_SET_TEMPLATE_INPUT->block("BLOCK_SELECTBOX_TOOL_LINK");
				}

				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
		//####################################################    BOTÃO LINK DE FERRAMENTA
			if($k['type'] == '_ferramenta_interna_') {

				$_SET_TEMPLATE_INPUT = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ 
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";
				}else{
					$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";
				}
				if($k['legenda'] != '') {
					$_SET_TEMPLATE_INPUT->LEGENDA = 'legenda="' . $k['legenda'] . '"';
				} else {
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				}
				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				$_SET_TEMPLATE_INPUT->WIDTH         = pxToPct(($k['largura'] - 13),WSTAGE);
				$_SET_TEMPLATE_INPUT->LABEL         = $k['label'];
				$_SET_TEMPLATE_INPUT->ID            = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = $session->get('ws_nivel');
				$_SET_TEMPLATE_INPUT->VALUE         = $k['values_opt'];
				$_SET_TEMPLATE_INPUT->block("BLOCK_BOT_TOOLS");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
		//#####################################################################        MULTIPLOS SELECT BOX
			if($k['type'] == 'multiple_select') {
				$_SET_TEMPLATE_INPUT                = new Template(TEMPLATE_INPUT_LINK, true);
				if($k['labelTop']==0){ $_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:none;";}else{$_SET_TEMPLATE_INPUT->LABEL_TOP_INPUT="display:block;";}

				$_SET_TEMPLATE_INPUT->ID_ITEM       = ID_ITEM;
				$_SET_TEMPLATE_INPUT->PATH          = PATH;
				$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = ID_FERRAMENTA;
				$_SET_TEMPLATE_INPUT->WS_NIVEL      = WS_NIVEL;
				if($k['disabled'] == '1') {
					$_SET_TEMPLATE_INPUT->DISABLED = 'readonly="readonly"';
				} else {
					$_SET_TEMPLATE_INPUT->clear('DISABLED');
				}
				if($k['legenda'] != '') {
					$_SET_TEMPLATE_INPUT->LEGENDA = $k['legenda'];
					$_SET_TEMPLATE_INPUT->block("BLOCK_MULTIMPLE_SELECTBOX_LEGENDA");
				} else {
					$_SET_TEMPLATE_INPUT->clear('LEGENDA');
				}
				
				$_SET_TEMPLATE_INPUT->WIDTH        = pxToPct(($k['largura'] + 4),WSTAGE);
				$_SET_TEMPLATE_INPUT->WIDTH_SELECT = $k['largura'] - 50;
				$_SET_TEMPLATE_INPUT->PLACE        = $k['place'];
				$_SET_TEMPLATE_INPUT->ID           = $k['id_campo'];
				$_SET_TEMPLATE_INPUT->LABELSUP     = $k['labelSup'];
				$op_ordem                          = explode(',', $produto[$k['coluna_mysql']]);
				foreach($op_ordem as $opcao) {
					$mult_ordem = new MySQL();
					$mult_ordem->set_table(PREFIX_TABLES . $_SLUG_ .'_op_multiple');
					$mult_ordem->set_where('label="' . $opcao . '"');
					$mult_ordem->select();
					if(count($mult_ordem->fetch_array) > 0) {
						$selecionados                  = $mult_ordem->fetch_array[0];
						$_SET_TEMPLATE_INPUT->OP_ID    = $selecionados['id'];
						$_SET_TEMPLATE_INPUT->OP_LABEL = $selecionados['label'];
						$_SET_TEMPLATE_INPUT->OP_CHECK = "selected";
						$_SET_TEMPLATE_INPUT->block("BLOCK_MULTIMPLE_SELECTBOX_OP");
					}
				}
				$multiple = new MySQL();
				$multiple->set_table(PREFIX_TABLES . $_SLUG_ .'_op_multiple');
				$multiple->set_where('id_campo="' . $k['id_campo'] . '"');
				$multiple->set_order('label', 'ASC');
				$multiple->select();
				foreach($multiple->fetch_array as $op) {
					if(!in_array($op['label'], $op_ordem)) {
						$_SET_TEMPLATE_INPUT->OP_CHECK = "";
						$_SET_TEMPLATE_INPUT->OP_ID    = $op['id'];
						$_SET_TEMPLATE_INPUT->OP_LABEL = $op['label'];
						$_SET_TEMPLATE_INPUT->block("BLOCK_MULTIMPLE_SELECTBOX_OP");
					}
				}
				$_SET_TEMPLATE_INPUT->block("BLOCK_MULTIPLE_SELECTBOX");
				$_IPUNT_CAMPOS .= $_SET_TEMPLATE_INPUT->parse();
			}
		}


	##########################################################################################################
	# DEFINIMOS AS CONSTANTES UTILIZADAS NO PAINEL
	##########################################################################################################

		$_SET_TEMPLATE                 	= new Template(TEMPLATE_LINK, true);
		$_SET_TEMPLATE->STYLE_CKEDITOR 	= $styleCKEditor;
		$_SET_TEMPLATE->COMBO_PLUGINS  	= $comboPlugins;
		$_SET_TEMPLATE->TYPE_COLUMNS 	= $typeColumns;
		$_SET_TEMPLATE->GROUP 			= $_GET['token_group'];
		$_SET_TEMPLATE->TOKEN 			=_crypt();
		$_SET_TEMPLATE->ID_ITEM        	= ID_ITEM;
		$_SET_TEMPLATE->PATH           	= PATH;
		$_SET_TEMPLATE->ID_FERRAMENTA  	= ID_FERRAMENTA;
		
		$_SET_TEMPLATE->TITULO_TOOL = $_FERRAMENTA_['_tit_topo_'];
		if($_FERRAMENTA_['_niveis_'] >= 1) {
			$_SET_TEMPLATE->block("BLOCK_ADD_CAT");
		}
		if(isset($link_back)) {
			$_SET_TEMPLATE->LINK_BACK = $link_back;
			$_SET_TEMPLATE->block("BLOCK_LINK_BACK");
		} else {
			$_SET_TEMPLATE->clear("LINK_BACK");
		}
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . $_SLUG_.'_campos');
		$geral->set_where('type="bt_fotos"');
		$geral->select();
		if($geral->_num_rows >= 1) {
			$avatar = new MySQL();
			$avatar->set_table(PREFIX_TABLES . $_SLUG_.'_img');
			$avatar->set_where('id_item="' . ID_ITEM . '"');
			$avatar->set_where('AND avatar="1"');
			$avatar->select();
		}
		if($_FERRAMENTA_['_exec_js_'] == 'salvar' || $_FERRAMENTA_['_exec_js_'] == 'osdois') {
			$_SET_TEMPLATE->EXEC_JS_1 = stripcslashes($_FERRAMENTA_['_js_']);
		} else {
			$_SET_TEMPLATE->clear('EXEC_JS_1');
		}
		if($_FERRAMENTA_['_exec_js_'] == 'abrir' || $_FERRAMENTA_['_exec_js_'] == 'osdois') {
			$_SET_TEMPLATE->EXEC_JS_2 = stripcslashes($_FERRAMENTA_['_js_']);
		} else {
			$_SET_TEMPLATE->clear('EXEC_JS_2');
		}

	##########################################################################################################

	$_SET_TEMPLATE->URL_LOAD 		= './' . PATH . '/detalhes.php?'.http_build_query($_GET);
	$_SET_TEMPLATE->IF_ORIGINAL 	= $_IF_ORIGINAL;
	$_SET_TEMPLATE->CLASS_ORIGINAL 	= ($_IF_ORIGINAL=='true') ? "original" : "draft";


	$_SET_TEMPLATE->TOKEN 			= $produto['token'];
	$_SET_TEMPLATE->INPUTS_DETAILS 	= $_IPUNT_CAMPOS;
	$_SET_TEMPLATE->block("BLOCK_INPUTS_DETAILS");
	$_SET_TEMPLATE->block("BLOCK_WS_DETAILS");
	$_SET_TEMPLATE->show();
?>

