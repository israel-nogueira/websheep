<?
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
			$path = implode(array_filter(explode('/',$path)),"/");
			define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
		}

		if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}


	###############################################################################################################################
	#  GERANDO ASH PARA DOWNLOAD DE TEMPLATE
	###############################################################################################################################
	function importTemplate() {
		$remote_file = trim($_REQUEST['urlFile']);
		$local_file = INCLUDE_PATH."ws-bkp/".basename($remote_file).".zip";
		if(file_put_contents($local_file, file_get_contents($remote_file))){
			echo (filesize($local_file) > 0)? 1 : 0;
		}else{
			echo 0;				
		}
	 }

	###############################################################################################################################
	#  GERANDO ASH PARA DOWNLOAD DE TEMPLATE
	###############################################################################################################################
	function createAuthToken() {
		$Filename 		= $_POST["Filename"];
		$expireToken 	= $_POST["expireToken"]." minutes";
		$getTokenRest 	= ws::setTokenRest($expireToken);
		$now     		= date("Y-m-d H:i:s");
		$timeout 		= date("Y-m-d H:i:s", strtotime('+' . $expireToken, strtotime(date("Y-m-d H:i:s"))));
		$ws_auth_template = new MySQL();
		$ws_auth_template->set_table(PREFIX_TABLES . 'ws_auth_template');
		$ws_auth_template->set_insert('token', 			$getTokenRest);
		$ws_auth_template->set_insert('filename', 		$Filename);
		$ws_auth_template->set_insert('ws_timestamp', 	$now);
		$ws_auth_template->set_insert('expire', 		$timeout);		
		$ws_auth_template->insert();
		echo ws::protocolURL().DOMINIO.ROOT_WEBSHEEP.'ws-bkp/'.$getTokenRest;
		exit;
	}
	
	###############################################################################################################################
	#  HABILITANDO E DESABILITANDO PLUGINS
	#  Todos os plugins ativos tem um arquivo em seu diretorio chamado "active"  
	#  sem extenção nem nada, apenas o valor true dentro dele
	#  quando excluído, o plugin não é exibido 
	###############################################################################################################################
	function disEnabledPlugin() {
		global $user;
		$_ROOT =  substr(ws::includePath,0,strpos(ws::includePath,ws::rootPath)).ws::rootPath.'website/'. str_replace(ws::rootPath,"",$_REQUEST['pathPlugin']);
		$key        = $_ROOT.'/active';
		$phpConfig  = $_ROOT.'/plugin.config.php';


		if (file_exists($phpConfig)) {
			@include($phpConfig);
			$contents        = $plugin;
		} 

		if (file_exists($key)) {
			unlink($key);
			ws::insertLog($user->get('id'),0 ,0,"Plugin","Desabilitou plugin",$_REQUEST['pathPlugin'] ,"","system");
			ob_end_clean();
			echo "off";
			exit;
		} else {
			ws::insertLog($user->get('id'),0 ,0,"Plugin","Abilitou plugin",$_REQUEST['pathPlugin'] ,"","system");
			file_put_contents($key, "true");
			ob_end_clean();
			echo "on";
			exit;
		}
	}
	
	###############################################################################################################################
	#  EXCLUÍNDO PLUGINS
	#  Ao excluir o plugin, todos os arquivos dentro dele também serão excluídos 
	###############################################################################################################################
	function excluiPlugin() {
		global $user;
		function ExcluiDir($Dir) {
			global $user;
			$dd = opendir($Dir);
			while (false !== ($Arq = readdir($dd))) {
				if ($Arq != "." && $Arq != "..") {
					$Path = "$Dir/$Arq";
					if (is_dir($Path)) {
						ExcluiDir($Path);
					} elseif (is_file($Path)) {
						if (!unlink($Path)) {
							_erro("ops, houve um erro!" . __LINE__);
						}
					}
				}
			}
			closedir($dd);
			chmod($Dir, 0777);
			if (!rmdir($Dir)) {
				_erro("ops, houve um erro!" . __LINE__);
			}
		}
		ExcluiDir(INCLUDE_PATH.'website/' . $_REQUEST['path']);
		//id_user,id_ferramenta ,id_item, titulo,descricao,detalhes,tabela,$type
		ws::insertLog($user->get('id'),0 ,0,"Plugin","Excluiu plugin",$_REQUEST['path'] ,"","system");

	}
	
	###############################################################################################################################
	#  ADICIONANDO PLUGINS
	#  Ao adicionar um plugin, ele copia de dentro do painel um plugin básico de início para pasta padrão   
	###############################################################################################################################
	function AddPlugin() {
		$getPath = new MySQL();
		$getPath->set_table(PREFIX_TABLES . 'setupdata');
		$getPath->set_limit(1);
		$getPath->select();
		$getPath = $getPath->fetch_array[0]['url_plugin'];
		_copyFolder('/admin/app/ws-modules/plugins/padrao', '/website/' . $getPath . '/padrao_' . date('d-m-Y_H-i'));
		//id_user,id_ferramenta ,id_item, titulo,descricao,detalhes,tabela,$type
		ws::insertLog($user->get('id'),0 ,0,"Plugin","Criou um novo plugin",$_REQUEST['path'] ,"","system");
		exit;
	}
	
	###############################################################################################################################
	# VINCULANDO UM ÍTEM A OUTROS ÍTENS OU CATEGORIAS
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function vinculaItemOuCategorias() {
		global $_SLUG_;

		$variaveis = array();
		parse_str($_POST['categorias'], $variaveis);

		$CAMPO_DATA = new MySQL();
		$CAMPO_DATA->set_table(PREFIX_TABLES .$_SLUG_.'_campos');
		$CAMPO_DATA->set_where('id_campo="' . $_POST['idCampo'] . '"');
		$CAMPO_DATA->select();
		$CAMPO_DATA = $CAMPO_DATA->fetch_array[0];

		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
		$categorias->set_where(' id_item="' . $_POST['id_item'] . '"');
		$categorias->set_where(' AND ws_id_ferramenta="'.$_POST['ws_id_ferramenta'].'" ');
		$categorias->set_where(' AND ws_id_ferramenta_link="'.$_POST['ws_id_ferramenta_link'].'" ');
		$categorias->set_where(' AND ws_draft="1" ');
		$categorias->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
		$categorias->exclui();
		$output = array();

		if ($CAMPO_DATA['filtro'] == 'item') {
			foreach ($variaveis as $key => $value) {
				$new_key           = str_replace('_cat_', '', $key);
				$output[]          = $new_key;
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
				$insert_categorias->set_insert('id_item', 						$_POST['id_item']);
				$insert_categorias->set_insert('ws_id_ferramenta', 				$_REQUEST['ws_id_ferramenta']);
				$insert_categorias->set_insert('ws_id_ferramenta_link', 		$_REQUEST['ws_id_ferramenta_link']);
				$insert_categorias->set_insert('id_item_link', 					$new_key);
				$insert_categorias->set_insert('ws_draft', 						'1');
				$insert_categorias->set_insert('ws_id_draft', 					$_POST['id_item']);
				$insert_categorias->insert();
			}
		}

		if ($CAMPO_DATA['filtro'] == 'cat') {
			foreach ($variaveis as $key => $value) {
				$new_key           = str_replace('_cat_', '', $key);
				$output[]          = $new_key;
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
				$insert_categorias->set_insert('ws_id_ferramenta', $_POST['ws_id_ferramenta']);
				$insert_categorias->set_insert('ws_id_ferramenta_link', $_POST['ws_id_ferramenta_link']);
				$insert_categorias->set_insert('id_item', $_POST['id_item']);
				$insert_categorias->set_insert('id_cat_link', $new_key);
				$insert_categorias->set_insert('ws_draft', '1');
				$insert_categorias->set_insert('ws_id_draft', $_POST['id_item']);
				$insert_categorias->insert();
			}
		}
		$insert_categorias = new MySQL();
		$insert_categorias->set_table(PREFIX_TABLES .$_SLUG_. '_item');
		$insert_categorias->set_where('id="' . $_POST['id_item'] . '"');
		$insert_categorias->set_where(' AND ws_draft="1"');
		$insert_categorias->set_where(' AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$insert_categorias->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
		$insert_categorias->set_update($CAMPO_DATA['coluna_mysql'], implode($output, ','));
		$insert_categorias->salvar();
		echo "sucesso";
		exit;
	}
	
	###############################################################################################################################
	# RETORNANDO OS ÍTENS OU CATEGORIAS PARA SEREM VINCULADOS AO ÍTEM EM QUESTÃO
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function returnItensVinculados() {
		global $_SLUG_;
		if (criaRascunho($_REQUEST['ws_id_ferramenta'], $_POST['id_item'])) {
			echo '
					<script>
						TopAlert({
							mensagem:"<i class=\'fa fa-info-circle\'></i> Para que você possa editar este conteúdo de forma segura, foi gerado um rascunho do ítem.<br>Para aplicar as alterações, será necessário salvar e aplicar o rascunho nos ítens.",
							clickclose:true,
							height:40,
							timer:10000,
							type:1,
						})
					</script>
		';
		}

		$CAMPO_DATA = new MySQL();
		$CAMPO_DATA->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$CAMPO_DATA->set_where('id_campo="' . $_POST['idCampo'] . '"');
		$CAMPO_DATA->select();
		$CAMPO_DATA = $CAMPO_DATA->fetch_array[0];

		$WS_FERRAMENTA = new MySQL();
		$WS_FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$WS_FERRAMENTA->set_where('id="' . $CAMPO_DATA['values_opt'] . '"');
		$WS_FERRAMENTA->select();

		$_SLUG_LINK = $WS_FERRAMENTA->fetch_array[0]['slug'];
		$_ID_LINK 	= $WS_FERRAMENTA->fetch_array[0]['id'];
				echo '<input id="id_link_obj" type="hidden" value="'.$_ID_LINK.'"/>';


		if ($CAMPO_DATA['filtro'] == 'item') {

			$LINKPRODCAT = new MySQL();
			$LINKPRODCAT->set_table(PREFIX_TABLES . $_SLUG_LINK.'_item as tabela_modelo');
			$LINKPRODCAT->set_where('tabela_modelo.ws_id_ferramenta="' . $CAMPO_DATA['values_opt'] . '"');
			$LINKPRODCAT->set_where('AND tabela_modelo.ws_id_draft=0');

			if (trim($CAMPO_DATA['cat_referencia'])!= '') {
				$LINKPRODCAT->join(" INNER ", PREFIX_TABLES . $_SLUG_.'_link_prod_cat as linkCat ', ' tabela_modelo.id=linkCat.id_item AND linkCat.id_cat="' . $CAMPO_DATA['cat_referencia'] . '"');
			}
			$LINKPRODCAT->select();


			foreach ($LINKPRODCAT->fetch_array as $item) {
				$verify = new MySQL();
				$verify->set_table(PREFIX_TABLES . 'ws_link_itens');
				$verify->set_where('id_item="' . $_POST['id_item'] . '"');
				$verify->set_where(' AND id_item_link="' . $item['id'] . '"');
				$verify->set_where(' AND ws_draft=1');
				$verify->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
				$verify->select();
				if ($verify->_num_rows >= 1) {
					$select = "checked";
				} else {
					$select = "";
				}
				echo '<label class="categoria">' . $item[$CAMPO_DATA['referencia']];
				echo '<input name="' . $item['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
			}
		}
		if ($CAMPO_DATA['filtro'] == 'cat') {
			function foreachCat($categoria, $listCat,$ws_id_ferramenta) {
				global $user;
				global $_SLUG_;
				global $_SLUG_LINK;
				$cat_foreach = new MySQL();
				$cat_foreach->set_table(PREFIX_TABLES . $_SLUG_LINK.'_cat');
				$cat_foreach->set_order('titulo', 'ASC');
				$cat_foreach->set_where('id_cat="' . $categoria . '"');
				$cat_foreach->set_where('AND ws_id_ferramenta="' . $ws_id_ferramenta . '"');
				$cat_foreach->select();
				foreach ($cat_foreach->fetch_array as $cat) {
					$verify = new MySQL();
					$verify->set_table(PREFIX_TABLES . 'ws_link_itens');
					$verify->set_where('id_item="' . $_POST['id_item'] . '"');
					$verify->set_where('AND id_cat_link="' . $cat['id'] . '"');
					$verify->set_where(' AND ws_draft=1');
					$verify->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
					$verify->select();
					if ($verify->_num_rows >= 1) {
						$select = "checked";
					} else {
						$select = "";
					}
					$listCat[] = $cat['titulo'];
					echo '<label class="categoria">' . implode($listCat, ' > ') . '<input name="_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
					foreachCat($cat['id'], $listCat);
					$listCat = array();
				}
			}
			$categorias = new MySQL();
			$categorias->set_table(PREFIX_TABLES . $_SLUG_LINK.'_cat');
			$categorias->set_order('titulo', 'ASC');
			$categorias->set_where('ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
			if ($CAMPO_DATA['cat_referencia'] != "") {
				$categorias->set_where('AND id_cat="' . $CAMPO_DATA['cat_referencia'] . '"');
			} else {
				$categorias->set_where('AND id_cat="0"');
			}
			$categorias->select();
			foreach ($categorias->fetch_array as $cat) {
				$select = "";
				$verify = new MySQL();
				$verify->set_table(PREFIX_TABLES . 'ws_link_itens');
				$verify->set_where('id_item="' . $_POST['id_item'] . '"');
				$verify->set_where('AND id_cat_link="' . $cat['id'] . '"');
				$verify->set_where(' AND ws_draft=1');
				$verify->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
				$verify->select();
				if ($verify->_num_rows >= 1) {
					$select = "checked";
				} else {
					$select = "";
				}
				echo '<label class="categoria">' . $cat['titulo'] . ' <input name="_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>';
				$listCat[] = $cat['titulo'];
				foreachCat($cat['id'], $listCat);
				$listCat = array();
			}
		}
	}
	
	###############################################################################################################################
	# FUNÇÃO DESATIVADA TEMPORARIAMENTE PARA FUTURAS MELHORIAS
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function SaveLiveEditor() {
		global $user;
		global $_conectMySQLi_;
		global $_SLUG_;


		$sucesso          = false;
		$_POST['content'] = mysqli_real_escape_string($_conectMySQLi_, urldecode($_POST['content']));
		$linkVideo        = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->debug(0);
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_. '_files');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_img');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		echo $sucesso;
		exit;
	}
	
	###############################################################################################################################
	# SALVA A URL FORMATADA DO VÍDEO DO VÍMEO OU YOUTUBE 
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function saveURLvideo() {
		global $user;
		global $_SLUG_;

		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		//	$linkVideo->set_where('id="'.$_POST['id_item'].'"');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$linkVideo->set_update($_POST['coluna'], $_POST['urlVideo']);
		if ($linkVideo->salvar()) {
			$urlVideo   = $_POST['urlVideo'];
			$urlExplode = parse_url($urlVideo);
			$query      = array();
			parse_str(@$urlExplode['query'], $query);
			$urlExplode['query'] = $query;
			if ($urlExplode['host'] == "youtube.com" || $urlExplode['host'] == "www.youtube.com") {
				$urlThumb = "http://img.youtube.com/vi/" . $urlExplode['query']['v'] . "/hqdefault.jpg";
			} elseif ($urlExplode['host'] == "vimeo.com" || $urlExplode['host'] == "www.vimeo.com") {
				$url      = 'http://vimeo.com/api/v2/video' . $urlExplode['path'] . '.php';
				$contents = @file_get_contents($url);
				$array    = @unserialize(trim($contents));
				$urlThumb = @$array[0]['thumbnail_large'];
			}
			echo $urlThumb;
			exit;
		} else {
			echo "falha!";
			exit;
		}
	}
	
	###############################################################################################################################
	# SALVA A URL FORMATADA DO MP3 E RETORNA O TEMPLATE DO PLAYER 
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function saveURLmp3() {
		global $user;
		global $_SLUG_;
		#####################################################################################
		# CRIA O RASCUNHO CASO NAO TENHA
		#####################################################################################
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);
		#####################################################################################
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$linkVideo->set_update($_POST['coluna'], $_POST['urlMP3']);
		if ($linkVideo->salvar()) {
			$urlParsed = parse_url($_POST['urlMP3']);
			$options   = array(
				CURLOPT_SSL_VERIFYHOST 	=> false,
				CURLOPT_SSL_VERIFYPEER 	=> false,
				CURLOPT_RETURNTRANSFER 	=> true, // return web page
				CURLOPT_HEADER 			=> false, // don't return headers
				CURLOPT_FOLLOWLOCATION 	=> true, // follow redirects
				CURLOPT_ENCODING 		=> "", // handle all encodings
				CURLOPT_USERAGENT 		=> "WebSheep", // who am i
				CURLOPT_AUTOREFERER 	=> true, // set referer on redirect
				CURLOPT_CONNECTTIMEOUT 	=> 120, // timeout on connect
				CURLOPT_TIMEOUT 		=> 120, // timeout on response
				CURLOPT_MAXREDIRS 		=> 10 // stop after 10 redirects
			);
			$ch        = curl_init($_POST['urlMP3']);
			curl_setopt_array($ch, $options);
			$content = curl_exec($ch);
			$err     = curl_errno($ch);
			$errmsg  = curl_error($ch);
			$header  = curl_getinfo($ch);
			curl_close($ch);
			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $content;
			$html              = new DOMDocument();
			@$html->loadHTML($content);
			foreach ($html->getElementsByTagName('meta') as $meta) {
				if ($urlParsed['host'] == 'mixcloud.com' || $urlParsed['host'] == 'www.mixcloud.com') {
					if ($meta->getAttribute('name') == 'twitter:player') {
						$template = str_replace('', '', $meta->getAttribute('content'));
						break;
					}
				} elseif ($urlParsed['host'] == 'soundcloud.com' || $urlParsed['host'] == 'www.soundcloud.com') {
					if ($meta->getAttribute('property') == 'twitter:player') {
						$template = str_replace('visual=true', 'visual=false', $meta->getAttribute('content'));
						break;
					}
				}
			}
			echo $template;
			exit;
		} else {
			echo "falha!";
			exit;
		}
	}
	
	###############################################################################################################################
	# RETORNA O MODAL COM O FORMULARIO PARA DEFINIÇÃO DA URL DO SOUNDCLOUD OU  MIXCLOUD
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function getURLmp3() {
		global $_SLUG_;
		global $user;
		$token = $_POST['token'];
		$campo = new MySQL();
		$campo->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$campo->set_where('token="' . $token . '"');
		$campo->select();
		$coluna    = $campo->fetch_array[0]['coluna_mysql'];
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		//		$linkVideo->set_where('id="'.$_POST['id_item'].'"');
		$linkVideo->select();
		echo "<div>
			<div>Digite a URL da sua música (SoundCloud ou MixCloud):</div>
			<form id='formLinkVideo'>
				<input name='ws_id_ferramenta' value='" . $_POST['ws_id_ferramenta'] . "' type='hidden'/>
				<input name='coluna' value='" . $coluna . "' type='hidden'/>
				<input name='id_item' value='" . $_POST['id_item'] . "' type='hidden'/>
				<input id='urlPath' class='inputText' name='urlMP3' value='" . @$linkVideo->fetch_array[0][$coluna] . "' style='padding:10px 20px;width:calc(100% - 20px);margin-top: 13px;'/>
			<form>
		</div>";
	}
	
	###############################################################################################################################
	# RETORNA O MODAL COM O FORMULARIO PARA DEFINIÇÃO DA URL DO YOUTUBE OU VIMEO
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################	
	function getURLvideo() {
		global $_SLUG_;
		global $user;
		if (criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'])) {
			echo '
				<script>
					TopAlert({
						mensagem:"<i class=\'fa fa-info-circle\'></i> Para que você possa editar este conteúdo de forma segura, foi gerado um rascunho do ítem.<br>Para aplicar as alterações, será necessário salvar e aplicar o rascunho nos ítens.",
						clickclose:true,
						height:40,
						timer:10000,
						type:1,
					})
				</script>
				';
		}
		$token = $_POST['token'];
		$campo = new MySQL();
		$campo->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$campo->set_where('token="' . $token . '"');
		$campo->select();
		$coluna    = $campo->fetch_array[0]['coluna_mysql'];
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		//$linkVideo->set_where('id="'.$_POST['id_item'].'"');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$linkVideo->select();
		echo "<div>
			<div>Digite a URL do seu video (Youtube ou Vimeo):</div>
			<form id='formLinkVideo'>
				<input name='coluna' value='" . $coluna . "' type='hidden'/>
				<input name='id_item' value='" . $_POST['id_item'] . "' type='hidden'/>
				<input name='ws_id_ferramenta' value='" . $_POST['ws_id_ferramenta'] . "' type='hidden'/>
				<input class='inputText' name='urlVideo' value='" . $linkVideo->fetch_array[0][$coluna] . "' style='padding:10px 20px;width:calc(100% - 20px);margin-top: 13px;'/>
			<form>
			<div>";
	}
	
	###############################################################################################################################
	# SALVANDO O TÍTULO PADRÃO DA PÁGINA   
	###############################################################################################################################
	function salvaTitlePage() {
		global $_SLUG_;
		global $user;
		global $_conectMySQLi_;
		$titulo  = mysqli_real_escape_string($_conectMySQLi_, $_POST['titulo']);
		$id_page = $_POST['id_page'];
		if ($id_page == '0') {
			$Salva_Titulo = new MySQL();
			$Salva_Titulo->set_table(PREFIX_TABLES . 'setupdata');
			$Salva_Titulo->set_where('id="1"');
			$Salva_Titulo->set_update('title_root', $titulo);
			if ($Salva_Titulo->Salvar()) {

				//id_user,id_ferramenta ,id_item, titulo,descricao,detalhes,tabela,$type
				ws::insertLog($user->get('id'),0,0,"Alterou título padrão do website","Alterou o título padrão do website", "Alterou para:".$_POST['titulo'] ,PREFIX_TABLES."setupdata","update");

				echo "sucesso";
			}
		} else {
			$Salva_Titulo = new MySQL();
			$Salva_Titulo->set_table(PREFIX_TABLES . 'ws_pages');
			$Salva_Titulo->set_where('id="' . $id_page . '"');
			$Salva_Titulo->set_update('title_page', $titulo);
			if ($Salva_Titulo->Salvar()) {
				ws::insertLog($user->get('id'),0,$id_page,"Alterou título de uma página","Alterou título de uma página", "Alterou para:".$_POST['titulo'] ,PREFIX_TABLES."ws_pages","update");
				echo "sucesso";
			}
		}
	}
	
	###############################################################################################################################
	# EXCLUINDO UMA META TAG   
	###############################################################################################################################
	function exclMetaTag() {
		global $user;
		global $_SLUG_;
		$FERRA = new MySQL();
		$FERRA->set_table(PREFIX_TABLES . 'meta_tags');
		$FERRA->set_where('id="' . $_POST['idMeta'] . '"');
		if ($FERRA->exclui()) {
			ws::insertLog($user->get('id'),0,$id_page,"Alterou título de uma página","Alterou título de uma página", "Alterou para:".$_POST['titulo'] ,PREFIX_TABLES."meta_tags","delete");
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# EXCLUINDO VÁRIAS META TAG'S   
	###############################################################################################################################
	function exclMultiMetaTag() {
		global $user;
		global $_SLUG_;
		$FERRA = new MySQL();
		$FERRA->set_table(PREFIX_TABLES . 'meta_tags');
		$FERRA->set_where('id<>"" AND (id="' . implode($_POST['metas'], '" OR id="') . '")');
		if ($FERRA->exclui()) {
			echo "sucesso";
			ws::insertLog($user->get('id'),0,0,"Excluiu MetaTags","Excluiu MetaTags", "Excluiu MetaTags" ,PREFIX_TABLES."meta_tags","delete");
			exit;
		}else{
			ws::insertLog($user->get('id'),0,0,"Falha em excluir MetaTags","Falha em excluir MetaTags", "Falha em excluir MetaTags" ,PREFIX_TABLES."meta_tags","error");
		}
	}
	
	###############################################################################################################################
	# SALVA VÁRIAS META TAG'S   
	###############################################################################################################################
	function salvaMetaTag() {
		global $user;
		global $_SLUG_;
		$FERRA = new MySQL();
		$FERRA->set_table(PREFIX_TABLES . 'meta_tags');
		$FERRA->set_where('id="' . $_POST['idMeta'] . '"');
		$FERRA->set_update('type', $_POST['type']);
		$FERRA->set_update('type_content', $_POST['type_content']);
		$FERRA->set_update('content', $_POST['content']);
		if ($FERRA->Salvar()) {
			echo "sucesso";
			ws::insertLog($user->get('id'),0,$_POST['idMeta'],"Salvou/Alterou MetaTags","Salvou/Alterou MetaTags", "Salvou/Alterou MetaTags" ,PREFIX_TABLES."meta_tags","update");
			exit;
		}
	}
	
	###############################################################################################################################
	# ADICIONANDO META TAG'S A UMA PÁGINA   
	###############################################################################################################################
	function addMetaTag() {
		global $user;
		global $_SLUG_;
		$twitter   = array();
		$twitter[] = "twitter:card";
		$twitter[] = "twitter:site";
		$twitter[] = "twitter:creator";
		$twitter[] = "twitter:title";
		$twitter[] = "twitter:description";
		$twitter[] = "twitter:image";
		$basic     = array();
		$basic[]   = "pragma";
		$basic[]   = "author";
		$basic[]   = "robots";
		$basic[]   = "language";
		$basic[]   = "description";
		$basic[]   = "keywords";
		$og        = array();
		$og[]      = "og:title";
		$og[]      = "og:type";
		$og[]      = "og:url";
		$og[]      = "og:description";
		$og[]      = "og:image";
		$og[]      = "og:site_name";
		$og[]      = "fb:app_id";
		$og[]      = "og:video";
		$og[]      = "og:locale";
		$og[]      = "og:audio";
		$blank     = array();
		$blank[]   = "";
		if ($_POST['TypeMedia'] == "blank") {
			$insert_categorias = new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
			$insert_categorias->set_insert('id_page', $_POST['id_page']);
			if (!$insert_categorias->insert()) {
				echo "falha";
				exit;
			}
			echo "sucesso";
			ws::insertLog($user->get('id'),0,0,"Adicionou MetaTags","Adicionou MetaTags", "Adicionou MetaTags" ,PREFIX_TABLES."meta_tags","insert");

			exit;
		} elseif ($_POST['TypeMedia'] == "basic") {
			foreach ($basic as $basic) {
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
				$insert_categorias->set_insert('id_page', $_POST['id_page']);
				$insert_categorias->set_insert('tag', 'meta');
				$insert_categorias->set_insert('type', 'name');
				$insert_categorias->set_insert('type_content', $basic);
				if (!$insert_categorias->insert()) {
					ws::insertLog($user->get('id'),0,0,"Falha ao add metaTags","Falha ao add metaTags", "Falha ao add metaTags" ,PREFIX_TABLES."meta_tags","error");
					echo "falha";
					exit;
				}
			}
			ws::insertLog($user->get('id'),0,0,"Adicionou MetaTags","Adicionou MetaTags", "Adicionou MetaTags" ,PREFIX_TABLES."meta_tags","insert");
			echo "sucesso";
			exit;
		} elseif ($_POST['TypeMedia'] == "og") {
			foreach ($og as $og) {
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
				$insert_categorias->set_insert('id_page', $_POST['id_page']);
				$insert_categorias->set_insert('tag', 'meta');
				$insert_categorias->set_insert('type', 'property');
				$insert_categorias->set_insert('type_content', $og);
				if (!$insert_categorias->insert()) {
					ws::insertLog($user->get('id'),0,0,"Falha ao add metaTags","Falha ao add metaTags", "Falha ao add metaTags" ,PREFIX_TABLES."meta_tags","error");
					echo "falha";
					exit;
				}
			}
			ws::insertLog($user->get('id'),0,0,"Adicionou MetaTags","Adicionou MetaTags", "Adicionou MetaTags" ,PREFIX_TABLES."meta_tags","insert");
			echo "sucesso";
			exit;
		} elseif ($_POST['TypeMedia'] == "twitter") {
			foreach ($twitter as $twitter) {
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
				$insert_categorias->set_insert('id_page', $_POST['id_page']);
				$insert_categorias->set_insert('tag', 'meta');
				$insert_categorias->set_insert('type', 'name');
				$insert_categorias->set_insert('type_content', $twitter);
				if (!$insert_categorias->insert()) {
					ws::insertLog($user->get('id'),0,0,"Falha ao add metaTags","Falha ao add metaTags", "Falha ao add metaTags" ,PREFIX_TABLES."meta_tags","error");
					echo "falha";
					exit;
				}
			}
			ws::insertLog($user->get('id'),0,0,"Adicionou MetaTags","Adicionou MetaTags", "Adicionou MetaTags" ,PREFIX_TABLES."meta_tags","insert");
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# ADICIONANDO META TAG'S A UMA PÁGINA   
	###############################################################################################################################
	function returnCategorias() {
		global $user;
		global $_SLUG_;
		if (criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'])) {
			echo '
			<script>
				TopAlert({
					mensagem:"<i class=\'fa fa-info-circle\'></i> Para que você possa editar este conteúdo de forma segura, foi gerado um rascunho do ítem.<br>Para aplicar as alterações, será necessário salvar e aplicar o rascunho nos ítens.",
					clickclose:true,
					height:40,
					timer:10000,
					type:1,
				})
			</script>
			';
		}
		function foreachCat($categoria, $listCat) {
			global $user;
			global $_SLUG_;
			$cat_foreach = new MySQL();
			$cat_foreach->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
			$cat_foreach->set_order('titulo', 'ASC');
			$cat_foreach->set_where('id_cat="' . $categoria . '"');
			$cat_foreach->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
			$cat_foreach->select();
			foreach ($cat_foreach->fetch_array as $cat) {
				$verify = new MySQL();
				$verify->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
				$verify->set_where('id_cat="' . $cat['id'] . '"');
				$verify->set_where('AND id_item="' . $_POST['id_item'] . '"');
				$verify->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
				$verify->set_where('AND ws_draft="1"');
				$verify->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
				$verify->select();
				if ($verify->_num_rows >= 1) {
					$select = "checked";
				} else {
					$select = "";
				}
				$listCat[] = $cat['titulo'];
				echo '<label class="categoria">' . implode($listCat, ' > ') . '<input name="' . $cat['ws_nivel'] . '_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
				foreachCat($cat['id'], $listCat);
				$listCat = array();
			}
		}
		$LINKPRODCAT = new MySQL();
		$LINKPRODCAT->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
		//	$LINKPRODCAT->set_where('id_item="'.$_POST['id_item'].'"');
		$LINKPRODCAT->set_where('ws_draft="1"');
		$LINKPRODCAT->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$LINKPRODCAT->select();
		$listCat    = array();
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$categorias->set_order('titulo', 'ASC');
		$categorias->set_where('id_cat="0"');
		$categorias->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$categorias->select();
		$verify = new MySQL();
		$verify->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
		$verify->set_where('id_cat="0"');
		$verify->set_where('AND id_item="' . $_POST['id_item'] . '"');
		$verify->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$verify->set_where('AND ws_draft="1"');
		$verify->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$verify->select();
		if ($verify->_num_rows >= 1) {
			$select = "checked";
		} else {
			$select = "";
		}
		echo '<label class="categoria">Nivel zero <input name="0_cat_0" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
		foreach ($categorias->fetch_array as $cat) {
			$verify = new MySQL();
			$verify->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
			$verify->set_where('id_cat="' . $cat['id'] . '"');
			$verify->set_where('AND id_item="' . $_POST['id_item'] . '"');
			$verify->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
			$verify->set_where('AND ws_draft="1"');
			$verify->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
			$verify->select();
			if ($verify->_num_rows >= 1) {
				$select = "checked";
			} else {
				$select = "";
			}
			echo '<label class="categoria">#' . $cat['id'] . ' - ' . urldecode($cat['titulo']) . ' <input name="' . $cat['ws_nivel'] . '_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
			$listCat[] = $cat['titulo'];
			foreachCat($cat['id'], $listCat,$_POST['ws_id_ferramenta']);
			$listCat = array();
		}
	}
		
	###############################################################################################################################
	# VINCULA UM ÍTEM A UMA OU MAIS  CATEGORIA   
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function vinculaCategorias() {
		global $user;
		global $_SLUG_;
		$variaveis = array();
		parse_str($_POST['categorias'], $variaveis);
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
		$categorias->set_where('id_item="' . $_POST['id_item'] . '"');
		$categorias->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$categorias->set_where('AND ws_draft="1"');
		$categorias->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$categorias->exclui();
		foreach ($variaveis as $key => $value) {
			$explode           = explode('_', $key);
			$insert_categorias = new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
			$insert_categorias->set_insert('id_item', $_POST['id_item']);
			$insert_categorias->set_insert('ws_nivel', $explode[0]);
			$insert_categorias->set_insert('id_cat', $explode[2]);
			$insert_categorias->set_insert('ws_id_ferramenta', $_POST['ws_id_ferramenta']);
			$insert_categorias->set_insert('ws_draft', "1");
			$insert_categorias->set_insert('ws_id_draft', $_POST['id_item']);
			$insert_categorias->insert();
		}
		echo "sucesso";
		exit;
	}
	
	###############################################################################################################################
	# RETORNA O COMBO PARA UPLOAD DE IMAGENS NO EDITOR DE TEXTO   
	# Função padrão para o campo interno do ítem
	
	###############################################################################################################################
	function ReturnUploadCKEditor() {
		echo '<form id="formUploadCKeditor" action="/admin/app/core/ws-upload-files.php" method="post" enctype="multipart/form-data" name="formUpload">
					<input name="type" type="hidden" value="ckEditor">
					<input name="token_group" 	type="hidden" value="'.$_POST['token_group'].'">
					<input name="token" 		type="hidden" value="'._token(PREFIX_TABLES . 'ws_biblioteca', 'token').'">

					<input id="inputFile" input name="arquivo" accept="image/jpg,image/png,image/jpeg,image/gif" id="myfileCKEditor" type="file"  style="display:none">
					<button type="submit" id="enviar_arquivos" style="dispaly:none;"></button>
				</form>
				<div id="btSelectFile" class="botao" style="position: relative;float: left;padding: 10px 50px;left:50%;transform:translateX(-50%);">Selecionar imagem</div>
				<div id="uploadBarContent" class="bg01" style="height: 18px;position: relative;float: left;padding: 2px 20px;width: calc(100% - 60px);left: 50%;transform: translateX(-50%);margin-top: 18px;margin-bottom: -57px;">
					<div id="uploadBar" class="bg05" style="color:#FFF;text-shadow:none;width:0%;overflow:hidden;"></div>
			</div>';
	}
	
	###############################################################################################################################
	# SALVA ESTILOS INTERNOS DO EDITOR CKEDITOR    
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function salvaEstilo() {
		global $user;
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->set_update('stylejson', urlencode($_REQUEST['json']));
		if ($SETUP->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# RETORNA ESTILOS INTERNOS DO EDITOR CKEDITOR  
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function ReturnEstiloPadrao() {
		global $user;
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		echo '<style>' . '.ps-container > .ps-scrollbar-x-rail,.ps-container > .ps-scrollbar-y-rail {opacity: 0.8;}' . '</style>' . '<textarea id="stylesCSS" style="display:none;">' . urldecode($SETUP['stylejson']) . '</textarea>' . '<div id="ace_stylesCSS" style="text-shadow: none; text-align: left; height: 100%;left: 20px;width: calc(100% - 50px);0% - 13px);margin-top: 0px; font-size: 17px;float: left;"></div>' . '';
	}
	
	###############################################################################################################################
	# SALVA CSS INTERNO DO EDITOR CKEDITOR    
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function salvaCss() {
		global $user;
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->set_update('stylecss', urlencode($_REQUEST['css']));
		if ($SETUP->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	###############################################################################################################################
	# RETORNA CSS INTERNO DO EDITOR CKEDITOR    
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function ReturnCSSPadrao() {
		global $user;
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		echo '<style>' . '.ps-container > .ps-scrollbar-x-rail,.ps-container > .ps-scrollbar-y-rail {opacity: 0.8;}' . '</style>' . '<textarea id="stylesCSS" name="stylecss" style="display:none;">' . retira_acentos(urldecode($SETUP['stylecss'])) . '</textarea>' . '<div id="ace_stylesCSS" style="text-shadow: none; text-align: left; height: 100%;left: 20px;width: calc(100% - 50px);0% - 13px);margin-top: 0px; font-size: 17px;float: left;"></div>' . '';
	}
	
	
	###############################################################################################################################
	# VERIFICA SE O ARQUIVO DO PLUGIN EXISTE OU NÃO PARA SER EXECUTADO    
	###############################################################################################################################
	function loadInfosPlugin() {
		global $user;

		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		$pathPlugins = ws::includePath.'website/'.substr($_REQUEST['dataFile'], strpos($_REQUEST['dataFile'],$SETUP['url_plugin']), strlen($_REQUEST['dataFile']));
		ob_end_clean();
		if (file_exists($pathPlugins)) {
			echo 'true';
		} else {
			echo 'false';
		}
		exit;
	}
	
	###############################################################################################################################
	# ATUALIZA O AVATAR DE UMA CATEGORIA    
	###############################################################################################################################
	function reloadThumbCategoria() {
		global $user;
		global $_SLUG_;
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$U->set_where('id="' . $_REQUEST['idCat'] . '"');
		$U->set_update('avatar', $_REQUEST['avatar']);
		if ($U->salvar()) {
			echo ws::rootPath.'ws-img/281/0/100/' . $_REQUEST['avatar'];
		}
	}
	
	###############################################################################################################################
	# ATUALIZA O AVATAR DE UMA GALERIA    
	###############################################################################################################################
	function substituiThumbGaleria() {
		global $user;
		global $_SLUG_;
		if (is_array($_REQUEST['img'])) {
			$_REQUEST['img'] = trim($_REQUEST['img'][0]);
		}
		$U = new MySQL();  
		$U->set_table(PREFIX_TABLES.$_SLUG_.'_gal');
		$U->set_where('id="' . $_REQUEST['idItem'] . '"');
		$U->set_update($_REQUEST['coluna'], trim($_REQUEST['img']));
		if ($U->salvar()) {
			echo trim($_REQUEST['img']);
			exit;
		}
	}
	
	###############################################################################################################################
	# ATUALIZA UMA IMAGEM DE UM ÍTEM
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function substituiThumb() {
		global $user;
		global $_SLUG_;

		criaRascunho($_POST['ws_id_ferramenta'],$_POST['idItem']);
		if (is_array($_POST['img'])) {
			$_POST['img'] = trim($_POST['img'][0]);
		}
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		//$U->set_where('id="'.$_REQUEST['idItem'].'"');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="' . $_POST['idItem'] . '"');
		$U->set_update($_POST['coluna'], trim($_POST['img']));
		if ($U->salvar()) {
			ob_end_clean();
			echo trim($_POST['img']);
			exit;
		}
	}
	
	###############################################################################################################################
	# EXCLUI UMA IMAGEM DE UM ÍTEM
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function excluiThumb() {
		global $user;
		global $_SLUG_;
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['idItem']);
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="' . $_POST['idItem'] . '"');
		$U->set_update($_POST['coluna'], '');
		if ($U->salvar()) {
			echo true;
		}
	}
	###############################################################################################################################
	# ADICIONANDO IMAGENS AO ÍTEM PUXANDO DE DENTRO DA BIBLIOTECA
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function addImagensBibliotecaItem() {
		global $user;
		global $_SLUG_;

		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'],true);

		$up = new MySQL();
		$up->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$up->set_where('file=""');
		foreach ($_REQUEST['img'] as $imagem) {
			$up->set_where('OR file="' . $imagem . '"');
		}
		$up->select();
		foreach ($up->fetch_array as $imagem) {
			$up = new MySQL();
			$up->set_table(PREFIX_TABLES . $_SLUG_.'_img');
			$up->set_insert('ws_draft', '1');
			$up->set_insert('ws_id_ferramenta', 	$_POST['ws_id_ferramenta']);
			$up->set_insert('ws_id_draft', 			$_POST['id_item']);
			$up->set_insert('id_item',			 	$_POST['id_item']);
			$up->set_insert('ws_tool_id', 			$_POST['id_item']);
			$up->set_insert('ws_tool_item', 		$_POST['id_item']);
			//$up->set_insert('id_cat', 				$_POST['id_cat']);
			$up->set_insert('imagem', $imagem['file']);
			$up->set_insert('filename', $imagem['filename']);
			$up->set_insert('token', $imagem['token']);
			$up->insert();
		}
		echo true;
		exit;
	}
	
	###############################################################################################################################
	# ADICIONANDO IMAGENS A UMA GALERIA DE FOTOS PUXANDO DE DENTRO DA BIBLIOTECA
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function addImagensBibliotecaGaleriaInterna() {
		global $user;
		global $_SLUG_;
		$up = new MySQL();
		$up->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$up->set_colum('file');
		$up->set_colum('token');
		$up->set_colum('filename');
		$up->set_where('file=""');
		$up->distinct();
		foreach ($_REQUEST['img'] as $imagem) {
			$up->set_where('OR file="' . $imagem . '"');
		}
		$up->select();
		foreach ($up->fetch_array as $imagem) {
			$up = new MySQL();
			$up->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
			$up->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
			$up->set_insert('file', '' . $imagem['file']);
			$up->set_insert('filename', $imagem['filename']);
			$up->set_insert('token', $imagem['token']);
			$up->set_insert('id_item', $_REQUEST['id_item']);
			$up->set_insert('id_galeria', $_REQUEST['id_galeria']);
			$up->set_insert('ws_draft', '1');
			$up->set_insert('ws_id_draft', $_REQUEST['id_item']);
			if ($up->insert()) {
				$file = new MySQL();
				$file->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
				$file->set_where('file="' . $imagem['file'] . '"');
				$file->select();
				echo "<li id='" . $file->fetch_array[0]['id'] . "'>	
						<div id='combo'>
							<div id='detalhes_img' class='bg02'>
							<span><img class='editar' 	legenda='Editar Informações'	src='./app/templates/img/websheep/layer--pencil.png'></span>   
							<span><img class='excluir'	legenda='Excluir Imagem'		src='./app/templates/img/websheep/cross-button.png'></span>   
							</div>
							<img class='thumb_exibicao' src='".ws::rootPath."ws-img/155/155/100/" . $file->fetch_array[0]['file'] . "'>
						</div>
					</li>";
			}
		}
	}
	
	
	###############################################################################################################################
	# RETORNA COMBO PARA EDIÇÃO DE UM SELECTBOX 
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function edita_selectbox() {
		global $user;
		global $_SLUG_;
		$campos = new MySQL();
		$campos->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$campos->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$campos->select();
		echo '<div id="campos_BG">
				<form id="form_dados">
						<input  name="cargo" id="cargoInput" value=""/>
				</form>
				<div id="salvar" class="w1">>Salvar</div>
				<div id="excluir" class="w1">>Excluir</div>
		</div>';
		exit;
	}
	
	###############################################################################################################################
	# ORGANIZA A ORDEM DOS ARQUIVOS INTERNOS     
	###############################################################################################################################
	function ordena_files() {
		global $user;
		global $_SLUG_;
		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . $_SLUG_. '_files');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	###############################################################################################################################
	# ORGANIZA A ORDEM DAS IMAGENS INTERNAS     
	###############################################################################################################################	
	function ordena_fotos_imgs() {
		global $user;
		global $_SLUG_;

		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;

		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES.$_SLUG_.'_img');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	
	###############################################################################################################################
	# RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UM ARQUIVO INTERNO DO ITEM  
	###############################################################################################################################
	function dados_file() {
		global $_SLUG_;
		$idFile = $_REQUEST['idFile'];
		$token  = $_REQUEST['token'];
		$Dados  = new MySQL();
		$Dados->set_table(PREFIX_TABLES . $_SLUG_. '_files');
		$Dados->set_where('id=' . $idFile);
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$url       = $Dados->fetch_array[0]['url'];
		$download  = $Dados->fetch_array[0]['download'];

		echo '<form id="form-img" id-img="' . $idFile . '" style="padding: 0 20px;" >
			<input 		id="titulo" 	name="titulo" 		class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem">
			<textarea 	id="textAreaInput" 	name="descricao" 	class="inputText">' . stripslashes(urldecode($descricao)) . '</textarea>
			<input 		id="url" 			name="url" 				class="inputText" value="' . $url . '"	placeholder="Link de Direcionamento"style="margin-top: 10px;" >
			<input 		id="token" 			name="token" 		type="hidden" value="' . $token . '" >
			<label>';
		if ($download == 1) {
			$download = 'checked="true"';
		}
		echo '<input id="download" name="download" type="checkbox" ' . $download . ' style="position:relative;width:fit-content;">
				Habilitar para download
			</label>
			</form>';
	}
	
	
	###############################################################################################################################
	# SALVA DADOS DE UM ARQUIVO INTERNO DO ITEM  
	###############################################################################################################################
	function SalvarDadosFiles() {
		global $_SLUG_;
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES.$_SLUG_. '_files');
		$Salva->set_where('id=' . $_POST['idFile']);
		$Salva->set_update('titulo', $_POST['titulo']);
		$Salva->set_update('texto', urlencode($_POST['texto']));
		$Salva->set_update('url', urlencode($_POST['url']));
		$Salva->set_update('download', $_POST['download']);

		$SalvaBiblio = new MySQL();
		$SalvaBiblio->set_table(PREFIX_TABLES.'ws_biblioteca');
		$SalvaBiblio->set_where('token="'.$_POST['token'].'"');
		$SalvaBiblio->set_update('download', $_POST['download']);
		$SalvaBiblio->salvar();
		if ($Salva->salvar()) {
			echo "Ítem salvo com sucesso!";
			exit;
		} else {
			_erro("Falha ao salvar arquivo.");
			exit;
		}
	}
	
	###############################################################################################################################
	# EXCLUI UM ARQUIVO INTERNO DO ITEM  
	###############################################################################################################################
	function ExcluiFile() {
		global $_SLUG_;
		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . $_SLUG_. '_files');
		$Dados->set_where('id='.$iDimg);
		$Dados->select();
	
		$arquivo    = $Dados->obj[0]->file;
		$token      = $Dados->obj[0]->token;
		$Biblioteca = new MySQL();
		$Biblioteca->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$Biblioteca->set_where('tokenFile="' . $token . '"');
		$Biblioteca->exclui();
		$ws_keyfile = new MySQL();
		$ws_keyfile->set_table(PREFIX_TABLES . 'ws_keyfile');
		$ws_keyfile->set_where('tokenFile="' . $token . '"');
		$ws_keyfile->exclui();
		$model_files = new MySQL();
		$model_files->set_table(PREFIX_TABLES . $_SLUG_. '_files');
		$model_files->set_where('token="' . $token . '"');
		$model_files->exclui();
		@unlink('./uploads/' . $arquivo);
		@unlink('./../../../website/assets/upload-files/' . $arquivo);
		echo "Sucesso em excluir o arquivo";
		exit;
	}
	
	###############################################################################################################################
	# RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UMA IMAGEM INTERNA DO ITEM  
	###############################################################################################################################
	function dados_img() {
		global $_SLUG_;
		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . $_SLUG_.'_img');
		$Dados->set_where('id=' . $iDimg);
		$Dados->url('decode');
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$url       = $Dados->fetch_array[0]['url'];
		$avatar    = $Dados->fetch_array[0]['avatar'];
		if ($avatar == '1') {
			$avatar = 'checked';
		} else {
			$avatar = '';
		}
		echo '<form id="form-img" id-img="' . $iDimg . '" >
			<input 		id="titulo" 		name="titulo" 			class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem" style="width: 488px;left: 0;padding: 10px;margin-bottom: 20px;">
			<textarea 	id="textarea" 		name="descricao" 		class="inputText">' . stripslashes(urldecode($descricao)) . '</textarea>
			<input 		id="url" 			name="url" 				class="inputText" value="' . $url . '"	placeholder="Link de Direcionamento" style="width: 488px;left: 0;padding: 10px;margin-top: 20px;">
			</form>
			<script>
					CKEDITOR.replace( "textarea", {
						forcePasteAsPlainText	:true,
						fillEmptyBlocks:false,
						basicEntities:false,
						entities_greek:false, 
						entities_latin:false, 
						entities_additional:"",
						enterMode: 2,
						toolbarStartupExpanded: 0,
						entities: 0,
						forceSimpleAmpersand: 1,
						allowedContent: true,
						toolbarGroups: [{ name: "basicstyles" },{ name: "links" }]});
					setTimeout(function(){$(".cke_button[title=\"Add ShortCode\"] .cke_button_label").show();},500)
					reloadFunctions();
					$("#checkbox_avatar").LegendaOver();
			</script>';
	}
	
	###############################################################################################################################
	# SALVA INFORMAÇÕES DE UMA IMAGEM INTERNA DO ITEM  
	###############################################################################################################################
	function SalvarDadosImg() {
		global $_SLUG_;
		if ($_REQUEST['avatar'] == 'on') {
			$_REQUEST['avatar'] = '1';
		} else {
			$_REQUEST['avatar'] = '0';
		}
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_img');
		$Salva->set_where('id="' . 		@$_REQUEST['iDimg'] . '"');
		$Salva->set_update('titulo', 	@$_REQUEST['titulo']);
		$Salva->set_update('texto', 	@$_REQUEST['texto']);
		$Salva->set_update('url', 		@$_REQUEST['url']);
		$Salva->set_update('avatar', 	@$_REQUEST['avatar']);
		if ($Salva->salvar()) {
			echo "sucesso";
			exit;
		} else {
			_erro("Falha ao salvar imagem, erro:" . __LINE__);
		}
	}
	
	###############################################################################################################################
	# EXCLUI UMA IMAGEM INTERNA DO ITEM  
	###############################################################################################################################
	function ExcluiImgm() {
		global $_SLUG_;
		$iDimg = $_REQUEST['iDimg'];
		$D     = new MySQL();
		$D->set_table(PREFIX_TABLES . $_SLUG_.'_img');
		$D->set_where('id=' . $iDimg);
		$D->exclui();
	}
	
	###############################################################################################################################
	# ADICIONA UMA CATEGORIA A FERRAMENTA  
	###############################################################################################################################
	function addCategoria() {
		global $_SLUG_;
		$token = _token(PREFIX_TABLES . $_SLUG_.'_cat', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$I->set_insert('token', $token);
		$I->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
		$I->set_insert('id_cat', $_REQUEST['id_cat']);
		$I->set_insert('titulo', 'Nova categoria');
		if ($I->insert()) {
			echo json_encode(array(
				'resposta' => 'sucesso'
			));
		} else {
			echo json_encode(array(
				'resposta' => 'falha'
			));
		}
	}
	
	###############################################################################################################################
	# REORGANIZA O POSICIONAMENTO DAS CATEGORIAS   
	###############################################################################################################################
	function OrdenaCategoria() {
		global $_SLUG_;
		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	###############################################################################################################################
	# SALVA INFORMAÇÕES DE UMA CATEGORIA  
	###############################################################################################################################
	function SalvarDadosCategoria() {
		global $_SLUG_;
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$Salva->set_where('id=' . $_POST['iCat']);
		$Salva->set_update('id_cat', stripslashes(strip_tags(urldecode($_POST['categoryTop']))));
		$Salva->set_update('titulo', stripslashes(strip_tags(urldecode($_POST['titulo']))));
		$Salva->set_update('texto', stripslashes(strip_tags(urldecode($_POST['texto']))));
		$Salva->set_update('ws_protect', stripslashes(strip_tags(urldecode($_POST['ws_protect']))));
		$Salva->set_update('url', urlencode($_POST['url']));
		if ($Salva->salvar()) {
			echo "sucesso!";
		} else {
			echo "Falha!";
		}
	}
	
	###############################################################################################################################
	###############################################################################################################################
	###############################################################################################################################  EXCLUSÃO DE DADOS
	###############################################################################################################################
	###############################################################################################################################
	
	function excl_arquivos($id) {
		global $_SLUG_;
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$geral->set_where('type="file" OR type="bt_files"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$mysql_files = new MySQL();
			$mysql_files->set_table(PREFIX_TABLES . $_SLUG_. '_files');
			$mysql_files->set_where('id_item="' . $id . '"');
			$mysql_files->select();
			foreach ($mysql_files->fetch_array as $file) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . $_SLUG_. '_files');
				$exclui->set_where('id="' . $file['id'] . '"');
				if ($exclui->exclui()) {
				}
			}
		}
	}
	function excl_img($id) {
		global $_SLUG_;
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$geral->set_where('type="thumbmail" OR type="bt_fotos"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . $_SLUG_.'_img');
			$img_prod->set_where('id_item="' . $id . '"');
			$img_prod->set_where('OR ws_id_draft="' . $id . '"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_img');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_img_gal($id) {
		global $_SLUG_;
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$geral->set_where('type="bt_galerias"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
			$img_prod->set_where('id_galeria="' . $id . '"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_img_cat($id) {
		global $_SLUG_;
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$geral->set_where('type="thumbmail" OR type="bt_fotos"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . $_SLUG_.'_img');
			$img_prod->set_where('id_cat="' . $id . '"');
			$img_prod->set_where('AND avatar="1"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				//@unlink('./uploads/'.$imgp['imagem']);
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_img');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_gal($id) {
		global $_SLUG_;
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$geral->set_where('type="bt_galerias"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
			$img_prod->set_where('id_item="' . $id . '"');
			$img_prod->set_where('OR ws_id_draft="' . $id . '"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				//if(@unlink('./uploads/'.$imgp['avatar'])){}
				excl_img_gal($imgp['id']);
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_prod($id) {
		global $_SLUG_;
		$session = new session();
		@ob_start();
		$produto = new MySQL();
		$produto->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$produto->set_where('id_cat="' . $id . '"');
		$produto->set_order('posicao', 'ASC');
		$produto->select();
		//excl_links($id);
		foreach ($produto->fetch_array as $prod) {
			excl_img($prod['id']);
			excl_gal($prod['id']);
			excl_arquivos($prod['id']);
			if ($session->get('_NIVEIS_') >= 1) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
				$exclui->set_where('id_item="' . $id . '"');
				$exclui->set_where('OR ws_id_draft="' . $id . '"');
				$exclui->exclui();
			}
			$exclui = new MySQL();
			$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_item');
			$exclui->set_where('id="' . $prod['id'] . '"');
			$exclui->set_where('OR ws_id_draft="' . $prod['id'] . '"');
			if ($exclui->exclui()) {
				@ob_end_clean();
			}
		}
	}
	function excl_links($id) {
		global $_SLUG_;
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
		$categorias->set_where('id_item="' . $id . '"');
		$categorias->set_where('OR id_item_link="' . $id . '"');
		$categorias->set_where('OR ws_id_draft="' . $id . '"');
		$categorias->exclui();
	}
	function excl_links_cats($id) {
		global $_SLUG_;
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
		$categorias->set_where('id_item="' . $id . '"');
		$categorias->set_where('OR id_cat_link="' . $id . '"');
		$categorias->set_where('OR ws_id_draft="' . $id . '"');
		$categorias->exclui();
	}
	function excl_produto() {
		global $_SLUG_;
		$session = new session();
		excl_img($_REQUEST['id_item']);
		excl_gal($_REQUEST['id_item']);
		excl_arquivos($_REQUEST['id_item']);
		//excl_links($_REQUEST['id_item']);
		if ($session->get('_NIVEIS_') >= 1) {
			$excluiNIVEIS = new MySQL();
			$excluiNIVEIS->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
			$excluiNIVEIS->set_where('id_item="' . $_REQUEST['id_item'] . '"');
			$excluiNIVEIS->set_where('OR ws_id_draft="' . $_REQUEST['id_item'] . '"');
			$excluiNIVEIS->exclui();
		}
		$excluiITEM = new MySQL();
		$excluiITEM->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$excluiITEM->set_where('id="' . $_REQUEST['id_item'] . '"');
		$excluiITEM->set_where('OR ws_id_draft="' . $_REQUEST['id_item'] . '"');
		if ($excluiITEM->exclui()) {
			echo "sucesso";
			exit;
		} else {
			echo "falha!";
			exit;
		}
	}
	function excl_cat($id) {
		global $_SLUG_;
		@ob_start();
		$categoria = new MySQL();
		$categoria->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$categoria->set_where('id="' . $id . '"');
		$categoria->select();
		excl_links_cats($id);
		$id_cat = new MySQL();
		$id_cat->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$id_cat->set_where('id_cat="' . $id . '"');
		$id_cat->debug(0);
		$id_cat->select();
		// verifica as sub categorias  e aplica a função nelas
		foreach ($id_cat->fetch_array as $gal) {
			excl_cat($gal['id']);
		}
		// exclui relações da categoria com os produtos
		$exclui = new MySQL();
		$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_link_prod_cat');
		$exclui->set_where('id_cat="' . $id . '"');
		if ($exclui->exclui()) {
			@ob_end_clean();
		}
		// exclui as imagens da categoria e vamos pro produto
		excl_img_cat($id);
		excl_prod($id);
		// exclui o registro
		$exclui = new MySQL();
		$exclui->set_table(PREFIX_TABLES . $_SLUG_.'_cat');
		$exclui->set_where('id="' . $id . '"');
		if ($exclui->exclui()) {
			@ob_end_clean();
		}
	}
	function ExcluiCategoria() {
		global $_SLUG_;
		$id_cat = $_REQUEST['id_cat'];
		excl_cat($id_cat);
		@ob_end_clean();
		exit;
	}
	
	###############################################################################################################################
	#	ADICIONA UM ÍTEM A FERRAMENTA
	###############################################################################################################################
	function addItem() {
		global $user;
		global $_SLUG_;
		$token = _token(PREFIX_TABLES . $_SLUG_.'_item', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$I->set_insert('token', $token);
		$I->set_insert('ws_draft', 0);
		$I->set_insert('ws_id_ferramenta', 	$_POST['ws_id_ferramenta']);
		$I->set_insert('id_cat', 			$_POST['id_cat']);
		$I->set_insert('ws_nivel', 			$_POST['ws_nivel']);
		$I->set_insert('ws_author', 		$user->get('id'));
		if ($I->insert()) {
			// die($token);
			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . $_SLUG_.'_item');
			$I->set_where('token="' . $token . '"');
			$I->select();
			$id = $I->fetch_array[0]['id'];
			echo json_encode(array(
				'resposta' => 'sucesso',
				'id' => $id
			));
		} else {
			echo json_encode(array(
				'resposta' => 'falha'
			));
		}
	}


	###############################################################################################################################
	#	REPOSICIONA A ORDEM DOS ÍTENS (desabilitado temporariamente)
	###############################################################################################################################
	function OrdenaItem() {
		global $_SLUG_;
		$array_pos      = $_REQUEST['posicoes'];
		$array_id       = $_REQUEST['ids'];
		$i              = 0;
		sort($array_pos,SORT_NUMERIC);
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_item');
			$Salva->set_where('id="' . $array_id[$i] . '"');
			$Salva->set_update('posicao',$array_pos[$i]);
			$Salva->salvar();
		 	++$i;
		}
		die('sucesso');
	}
	
	###############################################################################################################################
	#	ADICIONA UMA GALERTIA DE IMAGENS AO ÍTEM
	###############################################################################################################################
	function addGaleria() {
		global $_SLUG_;
		$_TABELA_ 	= PREFIX_TABLES . $_SLUG_.'_gal';
		$token 		= _token($_TABELA_, 'token');

		$INSERT     = new MySQL();
		$INSERT->set_table($_TABELA_);
		$INSERT->set_insert('token', $token);
		$INSERT->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
		$INSERT->set_insert('id_item', $_REQUEST['id_item']);
		$INSERT->set_insert('ws_draft', "1");
		$INSERT->set_insert('avatar', "default.png");
		$INSERT->set_insert('ws_tool_item',$_REQUEST['id_item']);
		$INSERT->set_insert('ws_tool_id', $_REQUEST['id_item']); 
		$INSERT->insert();

		$getLastID     = new MySQL();
		$getLastID->set_table($_TABELA_);
		$getLastID->set_order('id',"DESC");
		$getLastID->set_limit(1);
		$getLastID->select();
		$getLastID = $getLastID->fetch_array[0]['id'];

		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
		$Salva->set_where('id="' . $getLastID . '"');
		$Salva->set_update('ws_id_draft','command:id');
		$Salva->salvar();

	 	$Ordena->select('SET @pos:=0;');
	 	$Ordena->select('UPDATE '.PREFIX_TABLES.$_SLUG_.'_gal SET posicao=( SELECT @pos := @pos + 1 ) WHERE(ws_draft=1) ORDER BY posicao ASC;');

	}
	
	###############################################################################################################################
	#	ORGANIZA E REPOSICIONA AS GALERIAS DE UM ÍTEM
	###############################################################################################################################
	function OrdenaGaleria() {
		global $_SLUG_;
		$array_pos      = $_REQUEST['posicoes'];
		$array_id       = $_REQUEST['ids'];
		$i              = 0;
		sort($array_pos,SORT_NUMERIC);
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
			$Salva->set_where('id="' . $array_id[$i] . '"');
			$Salva->set_update('posicao',$array_pos[$i]);
			$Salva->salvar();
		 	++$i;
		}
		die('sucesso');
	}
	
	###############################################################################################################################
	#	RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UMA GALERIA
	###############################################################################################################################
	function dados_galeria() {
		global $_SLUG_;

		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
		$Dados->set_where('id=' . $iDimg);
		$Dados->select();
		$titulo    = (urldecode($Dados->fetch_array[0]['titulo']));
		$descricao = (urldecode($Dados->fetch_array[0]['texto']));
		$url       = (urldecode($Dados->fetch_array[0]['url']));
		$avatar    = $Dados->fetch_array[0]['avatar'];
		


		echo '	<form id="form-img" id-img="' . $iDimg . '" >
				<img src="'.ws::rootPath.'ws-img/320/375/100/' . $avatar . '" style="width:320px; height:375px; float: left;margin-left: 20px;border: solid 1px rgba(0, 0, 0, 0.09);">
				<input 		id="titulo" 	name="titulo" 		class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem" style="padding:10px;width:444px;">
				<textarea 	id="textarea" 	name="descricao" 	class="inputText">' . $descricao. '</textarea>
				<input 		id="url" 		name="url" 			class="inputText" value="' . $url . '" placeholder="Url:" style="padding:10px;width:440px;margin-top: 10px;">
				</form>
				<script>
				setTimeout(function(){
					CKEDITOR.replace( "textarea", {forcePasteAsPlainText	:true,fillEmptyBlocks:false,basicEntities:false,entities_greek:false, entities_latin:false, entities_additional:"",toolbarGroups: [{ name: "basicstyles" },{ name: "links" }]});
				},500)
					// reloadFunctions();
				</script>';
	}
	
	###############################################################################################################################
	#	SALVA OS DADOS DE UMA GALERIA
	###############################################################################################################################
	function SalvarDadosGalerias() {
		global $_SLUG_;

		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
		$Salva->set_where('id=' . $_REQUEST['iDimg']);
		$Salva->set_update('titulo', $_REQUEST['titulo']);
		$Salva->set_update('texto', urlencode($_REQUEST['texto']));
		$Salva->set_update('url', urlencode($_REQUEST['url']));

		if ($Salva->salvar()) {
			$dado = new MySQL();
			$dado->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
			$dado->set_where('id=' . $_REQUEST['iDimg']);
			$dado->select();
			if ($dado->fetch_array[0]['avatar'] == '') {
				$avatar = '/admin/app/templates/img/websheep/avatar.png';
			} else {
				$avatar = $dado->fetch_array[0]['avatar'];
			}
			echo "<img class='avatar' src='".ws::rootPath."ws-img/40/40/60/" . $avatar . "'>
		<span class='titulo_item w1'>" . urldecode($dado->fetch_array[0]['titulo']) . "</span>
		<span class='desc_item w2'>" . urldecode($dado->fetch_array[0]['texto']) . "...</span>
		<div id='combo'>
		<div id='detalhes_img' class='bg02'>
			<span><img class='mover_item' 		src='./app/templates/img/websheep/arrow-move.png'>	</span>
			<span><img class='galeria'			src='./app/templates/img/websheep/images.png'>		</span>
			<span><img class='editar' 			src='./app/templates/img/websheep/layer--pencil.png'>	</span>
			<span><img class='excluir'			src='./app/templates/img/websheep/cross-button.png'>	</span>
		</div>
			<form name='formUpload' class='formUploadGaleria' action='./app/core/upload_files.php' method='post' enctype='multipart/form-data' name='formUpload'>
				<input name='arquivo' id='myfile' type='file' style='display:none'/>
				<input name='_c_' hidden='true' value='" . $dado->fetch_array[0]['id'] . "'/>
				<input name='_t_' hidden='true' value='".PREFIX_TABLES.$_SLUG_."_gal'/>
				<button type='submit' class='enviar_arquivos' style='display:none'></button>
			</form>
		</div>";
		} else {
			_erro("Falha ao salvar estado personalizado.");
		}
	}
	
	###############################################################################################################################
	#	EXCLUI UMA GALERIA
	###############################################################################################################################
	function ExcluiGaleria() {
		global $_SLUG_;

		$iDgaleria = $_REQUEST['iDgaleria'];
		$IMG       = new MySQL();
		$IMG->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
		$IMG->set_where('id_galeria="' . $iDgaleria . '"');
		$IMG->select();
		foreach ($IMG->fetch_array as $img) {
			$D = new MySQL();
			$D->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
			$D->set_where('id=' . $img['id']);
			if ($D->exclui()) {
				// unlink('./uploads/'.$img['imagem']);
			}
		}
		$Gal = new MySQL();
		$Gal->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
		$Gal->set_where('id=' . $iDgaleria);
		$Gal_thb = new MySQL();
		$Gal_thb->set_table(PREFIX_TABLES . $_SLUG_.'_gal');
		$Gal_thb->set_where('id=' . $iDgaleria);
		$Gal_thb->select();
		$Gal->exclui();
		//unlink('./uploads/'.$Gal_thb->fetch_array[0]['avatar']);
	}
	
	###############################################################################################################################
	#	RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UMA IMAGEM INTERNA DE UMA GALERIA
	###############################################################################################################################
	function dados_img_gal() {
		global $_SLUG_;

		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
		$Dados->set_where('id=' . $iDimg);
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$imagem    = $Dados->fetch_array[0]['file'];
		$url       = $Dados->fetch_array[0]['url'];
		echo '<form id="form-img" id-img="' . $iDimg . '" >
			<img src="'.ws::rootPath.'ws-img/320/320/100/' . $imagem . '" style="width:320px; height:320px; float: left;margin-left: 20px;border: solid 1px rgba(0, 0, 0, 0.09);">
			<input 		id="titulo" 	name="titulo" 		class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem">
			<textarea 	id="textarea" 	name="descricao" 	class="inputText"style="width:320px;">' . stripslashes(urldecode($descricao)) . '</textarea>
			</form>
			<script>
				CKEDITOR.replace( "textarea", {
					forcePasteAsPlainText	:true,
					fillEmptyBlocks:false,
					basicEntities:false,
					entities_greek:false, 
					entities_latin:false, 
					enterMode: CKEDITOR.ENTER_BR,
					shiftEnterMode: CKEDITOR.ENTER_BR,
					entities_additional:"",
					autoParagraph:false,
					toolbarGroups: [{ name: "basicstyles" },{ name: "links" }]});
				setTimeout(function(){$(".cke_button[title=\"Add ShortCode\"] .cke_button_label").show();},500)
				reloadFunctions();
			</script>';
	}
	
	###############################################################################################################################
	#	SALVA AS INFORMAÇÕES DE UMA IMAGEM INTERNA DE GALERIA
	###############################################################################################################################
	function SalvarDados() {
		global $_SLUG_;

		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
		$Salva->set_where('id=' . $_REQUEST['idImg']);
		$Salva->set_update('titulo', $_REQUEST['titulo']);
		$Salva->set_update('texto', urlencode($_REQUEST['texto']));
		// $Salva->set_update('url', urlencode($_REQUEST['url']));
		if ($Salva->salvar()) {
			echo 'sucesso';
			exit;
		} else {
			_erro("Falha ao salvar estado personalizado.");
		}
	}
	
	###############################################################################################################################
	#	EXCLUI UMA IMAGEM INTERNA DE UMA GALERIA
	###############################################################################################################################
	function ExcluiImagem_gal() {
		global $_SLUG_;

		$iDimg = $_REQUEST['iDimg'];
		$D     = new MySQL();
		$D->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
		$D->set_where('id=' . $iDimg);
		$D->exclui();
		exit;
	}
	
	###############################################################################################################################
	#	POSICIONA E REORGANIZA AS FOTOS INTERNAS DE UMA GALERIA
	###############################################################################################################################
	function ordena_fotos() {
		global $_SLUG_;

		$i        = 1;
		foreach ($_REQUEST['ids'] as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			$Salva->salvar();
			++$i;
		}
	}
	
	###############################################################################################################################
	#	CASO A FERRAMENTA NÃO TENHA NÍVEIS E SEJA APENAS UMA GALERIA DE IMAGENS, AO INVEZ DE SALVAR O ÍTEM, ELE PUBLICA AS IMAGENS
	###############################################################################################################################
	function PublicaRascunhoImagens() {
		if (aplicaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'], true)) {
			criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);
			echo "Ítem salvo com sucesso!";
		}
	}
	###############################################################################################################################
	#	CASO A FERRAMENTA NÃO TENHA NÍVEIS E SEJA APENAS UMA GALERIA DE IMAGENS, AO INVEZ DE SALVAR O ÍTEM, ELE PUBLICA AS IMAGENS
	###############################################################################################################################
	function DescartaRascunhoImagens(){
		if (descartaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'], true)) {
			echo "Descartado com sucesso!";
		}
	}
	###############################################################################################################################
	#	CASO A FERRAMENTA NÃO TENHA NÍVEIS E SEJA APENAS UMA GALERIA DE IMAGENS, AO INVEZ DE SALVAR O ÍTEM, ELE PUBLICA AS IMAGENS
	###############################################################################################################################
	function DescartaRascunhoItens(){
		if (descartaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'], false)) {
			echo "Descartado com sucesso!";
		}
	}

	
	##########################################################################################################
	# 	PUBLICA UM RASCUNHO DE UM ÍTEM
	##########################################################################################################
	function PublicaRascunho() {
		global $user;
		$vars = $_POST;

		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $vars['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$_SLUG_ = $getSlug->fetch_array[0]['slug'];

		if (SalvaDetalhes($_POST)) {
			if (aplicaRascunho($vars['ws_id_ferramenta'], $vars['id_item'])) {
				echo "Ítem salvo com sucesso!";
				//id_user,id_ferramenta ,id_item, titulo,descricao,detalhes,tabela,$type
				ws::insertLog($user->get('id'),$vars['ws_id_ferramenta'],$vars['id_item'],__LINE__,0,"Salvou/Alterou um ítem","Salvou/Alterou um ítem", "Salvou/Alterou um ítem" ,PREFIX_TABLES.$_SLUG_."_item","update");

			} else {
				echo "Falha em publicar rascunho!";
				//id_user,id_ferramenta ,id_item, titulo,descricao,detalhes,tabela,$type
				ws::insertLog($user->get('id'),$vars['ws_id_ferramenta'],$vars['id_item'],__LINE__,0,"Falha ao publicar rascunho","Falha ao publicar rascunho", "Falha ao publicar rascunho" ,PREFIX_TABLES.$_SLUG_."_item","error");
			}
		} else {
			echo "Falha em salvar rascunho!";
			//id_user,id_ferramenta ,id_item, titulo,descricao,detalhes,tabela,$type
			ws::insertLog($user->get('id'),$vars['ws_id_ferramenta'],$vars['id_item'],__LINE__,0,"Falha ao salvar rascuinho","Falha ao salvar rascuinho", "Falha ao salvar rascuinho" ,PREFIX_TABLES.$_SLUG_."_item","error");

		}
	}
	
	##########################################################################################################
	# SALVA RASCUINHO DE UM ÍTEM
	##########################################################################################################
	function SalvaDetalhes($vars = null) {
		global $_conectMySQLi_;
		global $_SLUG_;

		$_POST = ($vars != null) ? $vars : $_POST;
		
		$id_item 			= @$_POST['id_item'];
		$function 			= @$_POST['function'];
		$ws_session			= @$_POST['ws_session'];
		$ws_id_ferramenta 	= @$_POST['ws_id_ferramenta'];
		$id_item       		= @$_POST['id_item'];
		$function 			= @$_POST['function'];
		$_link_opt_cat_ 	= @$_POST['_link_opt_cat_'];

		unset($_POST['ws_log']);
		unset($_POST['ws_session']);
		unset($_POST['ws_id_ferramenta']);
		unset($_POST['id_item']);
		unset($_POST['function']);
		unset($_POST['_link_opt_cat_']);

		##########################################################################################################
		# PEGAMOS A FERRAMENTA
		##########################################################################################################
		$FERRAMENTA = new MySQL();
		$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$FERRAMENTA->set_where('id="' . $ws_id_ferramenta . '"');
		$FERRAMENTA->select();
		$FERRAMENTA = @$FERRAMENTA->fetch_array[0];

		##########################################################################################################
		# SEPARAMOS OS CAMPOS DESTE ÍTEM
		##########################################################################################################
		$campos     = new MySQL();
		$campos->set_table(PREFIX_TABLES . $_SLUG_ .'_campos');
		$campos->set_order("posicao", "ASC");
		$campos->set_where('ws_id_ferramenta="' . $ws_id_ferramenta . '"');
		$campos->select();

		##########################################################################################################
		# CRIAMOS O RASCUNHO CASO NÃO TENHA
		##########################################################################################################
		criaRascunho($ws_id_ferramenta, $id_item);

		##########################################################################################################
		# SELECIONA AGORA O RASCUNHO QUE SERÁ COLOCADO AS INFORMAÇÕES 
		##########################################################################################################
		$_SALVAR_ = new MySQL();
		$_SALVAR_->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$_SALVAR_->set_where('ws_id_draft="' . $id_item . '"');
		$_SALVAR_->set_where('AND ws_id_ferramenta="' . $ws_id_ferramenta . '"');



		foreach ($_POST as $KEY => $POST) {
			$campos = new MySQL();
			$campos->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
			$campos->set_where('coluna_mysql="' . $KEY . '"');
			$campos->select();
			$TYPE = @$campos->fetch_array[0]['type'];
			if ($TYPE == 'key_works') {
				if (is_array($POST) == 1) {
					$_SALVAR_->set_update($KEY, implode($POST, ","));
				} else {
					$_SALVAR_->set_update($KEY, $POST);
				}
			} elseif ($TYPE == 'multiple_select') {
				if (is_array($POST)) {
					$VALOR = implode($POST, "[-]");
					$_SALVAR_->set_update($KEY, $VALOR);
				} else {
					$_SALVAR_->set_update($KEY, $POST);
				}
			} elseif ($TYPE == "keyworks") {
				if (is_array($POST)) {
					$_SALVAR_->set_update($KEY, implode($POST, ","));
				} else {
					$_SALVAR_->set_update($KEY, $POST);
				}
			} else {
				$_SALVAR_->set_update($KEY, mysqli_real_escape_string($_conectMySQLi_, urldecode($POST)));
			}
		}
		if ($vars != null) {
			if (count($_POST) >= 1) {
				if ($_SALVAR_->salvar()) {
					return true;
					exit;
				} else {
					return false;
					exit;
				}
			} else {
				return true;
				exit;
			}
		} else {
			if (count($_POST) >= 1) {
				if ($_SALVAR_->salvar()) {
					echo 'Ítem salvo com sucesso!';
					exit;
				} else {
					echo "Ops! Houve um arro ao salvar";
					exit;
				}
			} else {
				echo 'Ítem salvo com sucesso!';
				exit;
			}
		}
	}
	
	##########################################################################################################
	# RETORNA MODAL COM FORMULARIO DE EDIÇÃO DE UM SELECTBOX
	##########################################################################################################
	function editaCamposSelect() {
		global $_SLUG_;
		$keyarray      = array();
		$KeyAfinidades = new MySQL();
		$KeyAfinidades->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$KeyAfinidades->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$KeyAfinidades->select();
		$opcoes = explode("|", $KeyAfinidades->fetch_array[0]['values_opt']);
		foreach ($opcoes as $op) {
			$orderarray[] = $op;
		}
		//sort($orderarray);
		$opcoes = implode($orderarray, "|");
		echo '
		Aperte a tecla "Enter" para cadastrar as palavras separadamente:
		<div class="c"></div>
		<textarea id="textarea" rows="1" style="position: absolute; width: 489px; left: 0px;"></textarea>
	   <script type="text/javascript">
	    var Added	=	false;
	    setTimeout(function(){
		    $("#textarea").textext({
		            plugins : "tags",
		            tagsItems: ["';
		echo str_replace("|", '","', $opcoes);
		echo '"],
		             ext: {
			            tags: {
			            		onEnterKeyPress: function(tags){
			            			Added=true
						            $.fn.textext.TextExtTags.prototype.onEnterKeyPress.apply(this, arguments);
				                },
			            		addTags: function(tags){
						            $.fn.textext.TextExtTags.prototype.addTags.apply(this, arguments);
						            var teste = arguments;

						            if(Added==true){
					                   functions({
											patch:"' . $_REQUEST['path'] . '",
											funcao:"add_key_Select_Options",
											vars:"tags="+tags+"&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '&ws_id_ferramenta='.$_REQUEST['ws_id_ferramenta'].'",
											Sucess:function(e){
												if(tags!=""){
													 var newOption = $(e);
													$("#' . $_REQUEST['id_campo'] . '").empty();
													$("#' . $_REQUEST['id_campo'] . '").append(newOption);
												}
											}
										})
						            }
				                },

				            	removeTag:function(tags){
				                   	$.fn.textext.TextExtTags.prototype.removeTag.apply(this, arguments);
				                    functions({
										patch:"' . $_REQUEST['path'] . '",
										funcao:"remove_key_Select_Options",
										vars:"tags="+tags[0].innerText+"&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
										Sucess:function(e){
												var newOption = $(e);
												$("#' . $_REQUEST['id_campo'] . '").empty();
												$("#' . $_REQUEST['id_campo'] . '").append(newOption);
										}
									})
				            	}
							}
						}

			}).bind("removeTag", function(e,tag,value){
		        alert(tag.data);
		    })
		},500);
		</script>';
	}
	
	##########################################################################################################
	# FUNÇÃO QUE REMOVE UMA OPÇÃO E DÁ UPDATE AO SELECTOBOX NO ÍTEM
	##########################################################################################################
	function remove_key_Select_Options() {
		global $_SLUG_;

		$D = new MySQL();
		$D->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$D->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$D->select();
		$opcoes = $D->fetch_array[0]['values_opt'];
		$opcoes = explode("|", $opcoes);
		sort($opcoes);
		foreach ($opcoes as $op) {
			if ($op != $_REQUEST['tags']) {
				$novasOpcoes[] = $op;
			}
		}
		$opcoes = implode($novasOpcoes, "|");
		$S      = new MySQL();
		$S->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$S->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$S->set_update('values_opt', $opcoes);
		if ($S->salvar()) {
			$O = new MySQL();
			$O->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
			$O->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
			$O->select();
			$_values = $O->fetch_array[0]['values_opt'];
			$opcoes  = explode('|', $_values);
			sort($opcoes);
			foreach ($opcoes as $op) {
				$R = new MySQL();
				$R->set_table(PREFIX_TABLES . $_SLUG_.'_item');
				$R->set_where('id="' . $_REQUEST['id_item'] . '"');
				$R->select();
				if (urldecode($R->fetch_array[0][$O->fetch_array[0]['coluna_mysql']]) == $op) {
					$chek = 'selected';
				} else {
					$chek = '';
				}
				if ($op != "") {
					echo '<option name="' . $O->fetch_array[0]['name'] . '" value="' . $op . '" >' . $op . '</option>';
				}
			}
		}
	}
	
	##########################################################################################################
	# FUNÇÃO QUE ADICIONA UMA OPÇÃO E DÁ UPDATE AO SELECTOBOX NO ÍTEM
	##########################################################################################################
	function add_key_Select_Options() {
		global $_SLUG_;

		$O = new MySQL();
		$O->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$O->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$O->select();
		$_values       = $O->fetch_array[0]['values_opt'];
		$opcoes_values = explode('|', $_values);
		sort($opcoes_values);
		$S = new MySQL();
		$S->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
		$S->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$opcoes_atuais[] = $_REQUEST['tags'];
		sort($opcoes_atuais);
		foreach ($opcoes_values as $opv) {
			if ($opv != "") {
				$opcoes_atuais[] = $opv;
			}
		}
		if ($_REQUEST['tags'] != "") {
			$S->set_update('values_opt', implode($opcoes_atuais, "|"));
		}
		if ($S->salvar()) {
			$O = new MySQL();
			$O->set_table(PREFIX_TABLES . $_SLUG_. '_campos');
			$O->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
			$O->select();
			$_values = $O->fetch_array[0]['values_opt'];
			$opcoes  = explode('|', $_values);
			sort($opcoes);
			foreach ($opcoes as $op) {
				$R = new MySQL();
				$R->set_table(PREFIX_TABLES . $_SLUG_.'_item');
				$R->set_where('id="' . $_REQUEST['id_item'] . '"');
				$R->select();
				if (urldecode($R->fetch_array[0][$O->fetch_array[0]['coluna_mysql']]) == $op) {
					$chek = 'selected';
				} else {
					$chek = '';
				}
				echo '<option name="' . $O->fetch_array[0]['name'] . '" value="' . $op . '" >' . $op . '</option>';
			}
		}
	}
	
	##########################################################################################################
	# RETORNA MODAL COM FORMULARIO DE EDIÇÃO DE UM MULTIPLE SELECTBOX
	##########################################################################################################
	function edita_select_box_multiple() {
		global $_SLUG_;

		$multiple = array();
		$multiple = new MySQL();
		$multiple->set_table(PREFIX_TABLES . $_SLUG_. '_op_multiple');
		$multiple->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$multiple->select();
		echo '
			Aperte a tecla "Enter" para cadastrar as palavras separadamente:
			<div class="c"></div>
			<textarea id="textarea" rows="1" style="position: absolute; width: 489px; left: 0px;"></textarea>
		   <script type="text/javascript">
		    var Added	=	false;
		    setTimeout(function(){
			    $("#textarea").textext({
			            plugins : "tags",
			            tagsItems: ["';
		foreach ($multiple->fetch_array as $op) {
			$orderarray[] = @$op['label'];
		}
		echo @implode($orderarray, '","');
		echo '"],
	             ext: {
		            tags: {
		            		onEnterKeyPress: function(tags){
		            			Added=true
					            $.fn.textext.TextExtTags.prototype.onEnterKeyPress.apply(this, arguments);
			                },
		            		addTags: function(tags){
					            $.fn.textext.TextExtTags.prototype.addTags.apply(this, arguments);
					            var teste = arguments;
					            if(Added==true){
				                	   functions({
										patch:"' . $_REQUEST['path'] . '",
										funcao:"add_opt_Select_Options_multiple",
										vars:"tags="+tags+"&ws_id_ferramenta=' . $_REQUEST['ws_id_ferramenta'] . '&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
										Sucess:function(e){
											if(tags!=""){
												var newOption = $(e);
												$("#' . $_REQUEST['id_campo'] . '").empty();
												$("#' . $_REQUEST['id_campo'] . '").prepend(newOption);
												setTimeout(function(){$("#' . $_REQUEST['id_campo'] . '").trigger("chosen:updated");},500);
											}
										}
									})
					            }
			                },

			            	removeTag:function(tags){
			                   	$.fn.textext.TextExtTags.prototype.removeTag.apply(this, arguments);

			                    functions({
									patch:"'.$_REQUEST['path'].'",
									funcao:"remove_opt_Select_Options_multiple",
									vars:"ws_id_ferramenta='.$_REQUEST['ws_id_ferramenta'].'&path='.$_REQUEST['path'].'&tags="+tags[0].innerText+"&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
									Sucess:function(e){
											var newOption = $(e);
											$("#' . $_REQUEST['id_campo'] . '").empty();
											$("#' . $_REQUEST['id_campo'] . '").prepend(newOption);
											setTimeout(function(){$("#' . $_REQUEST['id_campo'] . '").trigger("chosen:updated");},500);

									}
								})
			            	}
						}
					}

		}).bind("removeTag", function(e,tag,value){
	        alert(tag.data);
	    })
			},500);


		</script>';
	}
	
	##########################################################################################################
	# FUNÇÃO QUE ADICIONA UMA OPÇÃO E DÁ UPDATE AO MULTIPLE SELECTOBOX NO ÍTEM
	##########################################################################################################
	function add_opt_Select_Options_multiple() {
		global $_SLUG_;


		$_TAGS_ = (strpos($_REQUEST['tags'],',')) ? explode(',',$_REQUEST['tags']) : array($_REQUEST['tags']);

		foreach ($_TAGS_ as $value) {
			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . $_SLUG_. '_op_multiple');
			$I->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
			$I->set_insert('id_ferramenta', $_REQUEST['ws_id_ferramenta']);
			$I->set_insert('id_item', $_REQUEST['id_item']);
			$I->set_insert('id_campo', $_REQUEST['id_campo']);
			$I->set_insert('label', $value);
			$I->insert();
		}

		$labels = array();
		$labels = new MySQL();
		$labels->set_table(PREFIX_TABLES . $_SLUG_. '_op_multiple');
		$labels->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$labels->select();
		foreach ($labels->fetch_array as $op) {
			$selected = new MySQL();
			$selected->set_table(PREFIX_TABLES . $_SLUG_ .'_link_op_multiple');
			$selected->set_where('id_opt="' . $op['id'] . '"');
			$selected->set_where('AND id_campo="' . $_REQUEST['id_campo'] . '"');
			$selected->set_where('AND id_item="' . $_REQUEST['id_item'] . '"');
			$selected->set_where('AND id_ferramenta="' . $_REQUEST['ws_id_ferramenta'] . '"');
			$selected->set_insert('AND ws_draft', '1');
			$selected->select();
			if ($selected->_num_rows >= 1) {
				$chek = "selected";
			} else {
				$chek = "";
			}
			echo '<option id="' . $op['id'] . '" value="' . $op['label'] . '" ' . $chek . '>' . $op['label'] . '</option>';
		}
	}
	
	##########################################################################################################
	# FUNÇÃO QUE REMOVE UMA OPÇÃO E DÁ UPDATE AO MULTIPLE SELECTOBOX NO ÍTEM
	##########################################################################################################
	function remove_opt_Select_Options_multiple() {
		global $_SLUG_;

		$labels = array();
		$labels = new MySQL();
		$labels->set_table(PREFIX_TABLES . $_SLUG_. '_op_multiple');
		$labels->set_where('label="' . $_REQUEST['tags'] . '"');
		$labels->select();

		foreach ($labels->fetch_array as $op) {
			$linkLabels = new MySQL();
			$linkLabels->set_table(PREFIX_TABLES . $_SLUG_ .'_link_op_multiple');
			$linkLabels->set_where('id_opt="' . $op['id'] . '"');
			$linkLabels->set_where('AND ws_draft="1"');
			$linkLabels->exclui();
			$excl_label = new MySQL();
			$excl_label->set_table(PREFIX_TABLES . $_SLUG_. '_op_multiple');
			$excl_label->set_where('id="' . $op['id'] . '"');
			$excl_label->exclui();
		}
		$labels = array();
		$labels = new MySQL();
		$labels->set_table(PREFIX_TABLES . $_SLUG_. '_op_multiple');
		$labels->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$labels->select();
		foreach ($labels->fetch_array as $op) {
			$selected = new MySQL();
			$selected->set_table(PREFIX_TABLES . $_SLUG_ .'_link_op_multiple');
			$selected->set_where('id_opt="' . $op['id'] . '"');
			$selected->set_where('AND id_campo="' . $_REQUEST['id_campo'] . '"');
			$selected->set_where('AND id_item="' . $_REQUEST['id_item'] . '"');
			$selected->set_where('AND id_ferramenta="' . $_REQUEST['ws_id_ferramenta'] . '"');
			$selected->set_where('AND ws_draft="1"');
			$selected->select();
			if ($selected->_num_rows >= 1) {
				$chek = "selected";
			} else {
				$chek = "";
			}
			echo '<option id="' . $op['id'] . '" value="' . $op['label'] . '" ' . $chek . '>' . $op['label'] . '</option>';
		}
	}
	
	##########################################################################################################
	# AGORA QUE TODAS AS FUNÇÕES JÁ FORAM DEFINIDAS, DAMOS START AO BUFFER E INCLUÍMOS A FUNÇÃO BASE DO SISTEMA
	##########################################################################################################
	clearstatcache();
	ob_start();
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

	##########################################################################################################	
	# INICIA A SESSÃO
	##########################################################################################################
	$user = new Session();

	##########################################################################################################
	# CAPTAMOS O SLUG DA FERRAMENTA
	##########################################################################################################
	if(isset($_REQUEST['ws_id_ferramenta'])){
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$_SLUG_ = $getSlug->fetch_array[0]['slug'];
	}else{
		$_SLUG_ = null;
	}



	##########################################################################################################
	# EXECUTA A FUNÇÃO REQUERIDA VIA AJAX
	##########################################################################################################
	_exec(@$_REQUEST['function']);
?>