<?php
########################################################################################
# DEFINIMOS O ROOT DO SISTEMA
########################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
		$path = implode(array_filter(explode('/',$path),'strlen'),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : trim('/'.$path.'/')));
	}
	
	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

########################################################################################
# IMPORTA CLASSES NECESSÁRIAS PARA O SISTEMA
########################################################################################
	include_once(INCLUDE_PATH.'ws-config.php');
	include_once(INCLUDE_PATH.'admin/app/lib/ws-connect-mysql.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-mysql.php');
	include_once(INCLUDE_PATH.'admin/app/lib/ws-globals-functions.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-functions.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-template.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-cookie.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-session.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-controller.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-browser.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-simple-html-dom.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-base2n.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-canvas.class.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-htmlprocess.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-lipsum.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-mobile-detect.php');
	include_once(INCLUDE_PATH.'admin/app/lib/class-thumbnails.php');
	include_once(INCLUDE_PATH.'admin/app/vendor/PHPMailer/PHPMailerAutoload.php');
########################################################################################
# INICIAMOS A CLASSE BASE DO SISTEMA
########################################################################################
	class ws extends wsconfig {
		public function __construct() {
			$this->dataRelType          = "item";
			$this->setpag               = null;
			$this->result               = null;
			$this->utf8                 = null;
			$this->url                  = null;
			$this->aliasStr             = '';
			$this->iditem               = null;
			$this->distinct             = 0;
			$this->draft                = false;
			$this->num_rows             = 0;
			$this->templateName         = '';
			$this->ws_slug				= '';
			$this->ColumOrder           = '';
			$this->OrderColum           = '';
			$this->setwheref            = '';
			$this->ws_id_ferramenta     = '';
			$this->ws_prefix_ferramenta = '';
			$this->UseTable             = '';
			$this->query                = '';
			$this->thisType             = '';
			$this->template             = '';
			$this->return_template      = '';
			$this->sql                  = '';
			$this->limit                = '';
			$this->colum                = " tabela_modelo.* ";
			$this->likeString           = array();
			$this->obj                  = array();
			$this->dataRelLinker        = array();
			$this->dataRelLinked        = array();
			$this->templates            = array();
			$this->filterColum          = array();
			$this->filterFn             = array();
			$this->filterVars           = array();
			$this->setItem              = array();
			$this->setInnerItem         = array();
			$this->setCat               = array();
			$this->setGal               = array();
			$this->wseditor             = array();
			$this->wsLinkFile           = array();
			$this->setcolum             = array();
			$this->setupdatecolum       = array();
			$this->InnerTypes           = array(
				"cat",
				"item",
				"gal",
				"img",
				"file",
				"img_gal"
			);
		}
		static function getFullPath(){			return INCLUDE_PATH;}
		static function getRootPath(){			return ROOT_WEBSHEEP;}
		static function getFullSitePath(){		return ws::getFullPath().'/website/';}
		static function create_thumbnail( $file, $w=0, $h=0,$q=null){
			$extensao 	= explode(".",basename($file));
			$ext 		= end($extensao);	
			if($ext=='png' && $q>9){$q=9;}
			if($q>100 || $q==null){$q=100;}

			$originalName 	= ws::includePath.'website/assets/upload-files/'.$file;
			$newName 		= $w . '-' . $h . '-' . $q . '-' . basename($file);
			$saveName 		= ws::includePath.'website/assets/upload-files/thumbnail/'.$newName;
			$w = ($w==null) ? getimagesize(ws::includePath.'website/assets/upload-files/'.$file)[0] : 0;
			$h = ($h==null) ? getimagesize(ws::includePath.'website/assets/upload-files/'.$file)[1] : 0;


			$img = new canvas();
			if (file_exists($saveName) || $img->carrega($originalName)->redimensiona($w, $h, 'crop')->grava($saveName, $q)) {
					$array = (object) array(
						'status' 		=> "sucess",
						'original' 		=> $file,
						'originalPath' 	=> dirname($file),
						'originalName' 	=> basename($file),
						'newPath' 		=> $newName,
						'base64' 		=> base64_encode(file_get_contents(ws::includePath.'website/assets/upload-files/thumbnail/'.$newName)),
						'rootPath' 		=> ws::rootPath.'assets/upload-files/thumbnail/'.$newName,
						'includePath' 	=> ws::includePath.'website/assets/upload-files/thumbnail/'.$newName
					);
					return $array;
			 }else{
				$array =(object) array(
						'status' 		=> "fail",
						'original' 		=> null,
						'originalPath' => null,
						'originalName' => null,
						'newPath' 		=> null,
						'base64' 		=> null,
						'rootPath' 		=> null,
						'includePath' 	=> null
					);
				return $array;
			 }
		}
		static function fb_count_comments($domain = null) {
			if ($domain == null) {
				$domain = ws::protocolURL() . DOMINIO . '/' . ws::urlPath();
			}
			return '<span class="fb-comments-count" data-href="' . $domain . '"></span>';
		}
		static function fb_comments($vars = null) {
			if ($vars == null) {
				return '<div class="fb-comments" data-href="' . ws::protocolURL() . DOMINIO . '/' . ws::urlPath() . '" data-width="100%" data-numposts="5"></div>';
			} elseif (!is_array($vars)) {
				return _erro(ws::GetDebugError(debug_backtrace(), 'Dados inválidos -> ws::fb_comments(array())'));
			} else {
				$dataHref = ws::protocolURL() . DOMINIO . '/' . ws::urlPath();
				$newvars  = array();
				$newvars  = array();
				foreach ($vars as $key => $value) {
					if ($key == "data-href") {
						$dataHref = $value;
					} elseif ($key == "class") {
						$newclass[] = $value;
					} else {
						$newvars[] = $key . '="' . $value . '"';
					}
				}
				return '<div class="fb-comments ' . implode($newclass, ' ') . '" data-href="' . $dataHref . '" ' . implode($newvars, " ") . '></div>';
			}
		}
		static function fb_sdk($id = null, $version = 'v2.0') {
			if ($id != null) {
				$idFb = '&appId=' . $id;
			} else {
				$idFb = '';
			}
			return '(function(d, s, id) {' . '	var js, fjs = d.getElementsByTagName(s)[0];' . '	if (d.getElementById(id)) return;' . '	js = d.createElement(s); js.id = id;' . '	js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=' . $version . '' . $idFb . '";' . '	fjs.parentNode.insertBefore(js, fjs);' . '}(document, "script","facebook-jssdk"));';
		}
		static function execCode($code,$return=false) {
		    $tmp = tmpfile();
		    $tmpf = stream_get_meta_data($tmp);
		    $tmpf = $tmpf ['uri'];
		    fwrite($tmp,$code );
			if($return==false){
				include($tmpf);
			    fclose ( $tmp );
			}elseif($return==true){
			    ob_start(); 
			    include($tmpf); 
			    $content = ob_get_clean(); 
			    fclose ( $tmp );
			    return  $content;
			}
		}
		public function insertVal($colum = null, $value = null) {
			$this->setinsertcolum[] = array(
				$colum,
				$value
			);
			return $this;
		}
		public function insert() {
			if (count($this->setinsertcolum) == 0) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Campos inexistentes ->insertVal"));
			} else {
				
				$token  = _token($this->UseTable, 'token');
				$_temp_ = new MySQL();
				$_temp_->set_table($this->UseTable);
				$_temp_->set_insert('ws_id_ferramenta', $this->ws_id_ferramenta);
				
				if ($this->draft == false) {
					$_temp_->set_insert('token', $token);
					foreach ($this->setinsertcolum as $value) {
						if ($this->UseTable == PREFIX_TABLES .$this->ws_slug."_item") {
							$_temp_->set_insert($this->ws_prefix_ferramenta . $value[0], $value[1]);
						} else {
							$_temp_->set_insert($value[0], $value[1]);
						}
					}
					$_temp_->insert();
				} else {
					$_temp_->set_insert('token', $token);
					$_temp_->insert();
					$getToken = new MySQL();
					$getToken->set_table($this->UseTable);
					$getToken->set_where('token="' . $token . '"');
					$getToken->select();
					$_temp_ = new MySQL();
					$_temp_->set_table($this->UseTable);
					$_temp_->set_insert('token', $token);
					$_temp_->set_insert('ws_id_ferramenta', $this->ws_id_ferramenta);
					$_temp_->set_insert('ws_id_draft', $getToken->fetch_array[0]['id']);
					$_temp_->set_insert('ws_draft', 1);
					foreach ($this->setinsertcolum as $value) {
						if ($this->UseTable == PREFIX_TABLES .$this->ws_slug."_item") {
							$_temp_->set_insert($this->ws_prefix_ferramenta . $value[0], $value[1]);
						} else {
							$_temp_->set_insert($value[0], $value[1]);
						}
					}
					$_temp_->insert();
				}
			}
			return $this;
		}
		######################################
		#	UPDATE DA VARIÁVEL
		######################################
		public function updateVal($colum = null, $value = null) {
			$this->setupdatecolum[] = array($colum,$value);
			return $this;
		}
		######################################
		#	PREPARA INPUT PARA BASE
		######################################
		public static function preventMySQLInject($string){
			global $_conectMySQLi_;
		    // $script = array('OR','FROM','SELECT','INSERT','DELETE','WHERE','DROP TABLE','SHOW TABLES','*','--','=');
	        // // $string = (!get_magic_quotes_gpc()) ? addslashes(str_ireplace($script,"",$string)) : str_ireplace($script,"",$string);
	        // $string = str_ireplace($script,"",$string);
	        return mysqli_real_escape_string($_conectMySQLi_,$string);
		}
		###################################################################################
		#
		###################################################################################
		static	function closureCompilerJs($code, $level = 'SIMPLE_OPTIMIZATIONS'){
			if(file_exists($code)){$code=file_get_contents($code);}
			try {
				$ch = curl_init('http://closure-compiler.appspot.com/compile'); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=' . $level . '&js_code=' . urlencode($code));
				$minified = curl_exec($ch);
				curl_close($ch);
			} catch (Exception $e) {
				$minified = $code;
			}
			return $minified;
		}
		###################################################################################
		#
		###################################################################################
		static function getlang($path = null, $isso = "", $porisso = "") {
			$json  = str_replace(array(PHP_EOL, "\n", "\r"), "", file_get_contents(ws::includePath.'admin/app/config/lang/' . LANG . '.json'));
			$obj   = json_decode($json, TRUE);
			$paths = explode(">", str_replace(" ","",$path));
			if ($path == null) {
				$content = "Invalid path ws::getlang( ? )";
			} elseif ($obj == '') {
				$content = "JSON error";
			} else {
				foreach ($paths as $value) {
					if (empty($obj[trim($value)]) && empty($content[trim($value)])) {
						$content = "Path " . $path . " does not exist";
						break;
					} else {
						$content = (empty($content)) ? $obj[trim($value)] : $content[trim($value)];
					}
				}
			}
			return str_replace($isso, $porisso, $content);
		}
		###################################################################################
		#
		###################################################################################
		public function save() {
			if (count($this->setupdatecolum) == 0) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Campos inexistentes ->updateVal"));
				exit;
			} else {
				$_temp_ = new MySQL();
				$_temp_->set_table($this->UseTable);
				if ($this->draft == false) {
					$_temp_->set_where('ws_draft=1');
					$_temp_->set_where('AND ws_id_draft="' . $this->iditem . '"');
				} else {
					$_temp_->set_where('id="' . $this->iditem . '"');
				}
				
				foreach ($this->setupdatecolum as $value) {
					if ($this->UseTable == PREFIX_TABLES .$this->ws_slug."_item") {
						$_temp_->set_update($this->ws_prefix_ferramenta . $value[0], $value[1]);
					} else {
						$_temp_->set_update($value[0], $value[1]);
					}
				}
				
				if ($_temp_->salvar()) {
					return true;
				} else {
					return false;
				}
			}
		}
		###################################################################################
		#
		###################################################################################
		static function insertLog($id_user="",$id_ferramenta="" ,$id_item="",$linha="",$coluna="",$type="",$url="",$titulo="",$mensagem=""){
			global $_conectMySQLi_;
			##############################################################################
			# INSERIMOS UM REGISTRO DE LOG
			##############################################################################
			$_temp_ = new MySQL();
			$_temp_->set_table(PREFIX_TABLES.'ws_log');
			$_temp_->set_insert('id_user',		$id_user);
			$_temp_->set_insert('id_ferramenta',$id_ferramenta);
			$_temp_->set_insert('id_item',		$id_item);
			$_temp_->set_insert('linha',		$linha);
			$_temp_->set_insert('coluna',		$coluna);
			$_temp_->set_insert('type',			$type);
			$_temp_->set_insert('url',			$url);
			$_temp_->set_insert('titulo',		$titulo);
			$_temp_->set_insert('mensagem',		mysqli_real_escape_string($_conectMySQLi_,$mensagem));
			$_temp_->debug(0);
			$_temp_->insert();
		}	
		###################################################################################
		# CRIAMOS UM TOKEN DE AUTORIZAÇÃO PARA VALIDAR QUALQUER COISA 
		###################################################################################
		static function setTokenRest($timeout = "5 seconds") {
			$Formats = array("seconds", "minutes", "hours", "days", "months", "years");
			if (is_string($timeout)) {
				if (strpos(trim($timeout), " ")) {
					$timeout = explode(' ', $timeout);
					if ((is_numeric($timeout[0]) || is_int($timeout[0])) && in_array($timeout[1], $Formats)) {
						$timeout = $timeout[0] . ' ' . $timeout[1];
					} else {
						die("Formatos aceitaveis: seconds, minutes, hours, days, months, years");
					}
				} else {
					if (is_numeric($timeout)) {
						$timeout = $timeout . ' seconds';
					} else {
						die("Formato de tempo não permitido");
					}
				}
			} else {
				die("Formato de tempo não permitido");
			}
			$now     = date("Y-m-d H:i:s");
			$timeout = date("Y-m-d H:i:s", strtotime('+' . $timeout, strtotime(date("Y-m-d H:i:s"))));
			$setTokenRest = _token(PREFIX_TABLES . 'ws_auth_token', 'token');
			$_temp_       = new MySQL();
			$_temp_->set_table(PREFIX_TABLES . 'ws_auth_token');
			$_temp_->set_insert('token', $setTokenRest);
			$_temp_->set_insert('ws_timestamp', $now);
			$_temp_->set_insert('expire', $timeout);		
			if ($_temp_->insert()) {
				return $setTokenRest;
			} else {
				return null;
			}
		}
		###################################################################################
		# AGORA VALIDAMOS O TOKEN CRIADO...E VEMOS SE ELE É VALIDO!
		###################################################################################
		static function getTokenRest($setTokenRest,$die=true,$firstAccess=false) {
			##############################################################################
			# BUSCAMOS NA BASE UM TOKEN DENTRO DO PRAZO ESTABELECIDO
			##############################################################################
			$_temp_ = new MySQL();
			$_temp_->set_table(PREFIX_TABLES . 'ws_auth_token');
			$_temp_->set_where('token="' . $setTokenRest . '"');
			$_temp_->set_where('AND  NOW() < expire');
			$_temp_->select();
				
			###################################################################################
			# EXCLUI QUALQUER TOKEN QUE ESTEJA EXPIRADO
			###################################################################################
			$_EXCL_ = new MySQL();
			$_EXCL_->set_table(PREFIX_TABLES . 'ws_auth_token');
			$_EXCL_->set_where('NOW() > expire');
			$_EXCL_->exclui();
			
			###################################################################################
			# CASO SEJA PROGRAMADO PARA EXPIRAR AO ACESSAR
			###################################################################################
			if($firstAccess==true){
				$_EXCL_ = new MySQL();
				$_EXCL_->set_table(PREFIX_TABLES . 'ws_auth_token');
				$_EXCL_->set_where('token="' . $setTokenRest . '"');
				$_EXCL_->exclui();
			}
			###################################################################################
			# VERIFICA SE EXISTE O TOKEN NA BASE, EXCLUI TODOS TOKENS VENCIDOS E RETORNA TRUE
			###################################################################################
			if ($_temp_->_num_rows >= 1) {
				return true;
			} else {
				if($die==true){
					die("Not found token access or expired");
				}else{
					return false;
				}
			}
			###################################################################################
		}
		###################################################################################
		# LIMITA TEXTOS OU FRASES POR QUANTIDADE DE PALAVRAS
		###################################################################################
		static function limit_words($texto = null, $limite = 0, $end = "...") {
			$texto = explode(' ', ($texto));
			$texto = array_slice($texto, 0, $limite);
			return implode($texto, " ") . $end;
		}
		###################################################################################
		# RETORNA O PROTOCOLO DA URL
		###################################################################################		
		public static function protocolURL() {
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			return $protocol;
		}
		###################################################################################
		# RETORNA O NÚMERO EM FORMATADO FINANCEIRO 
		###################################################################################
		public static function formatMoney($number, $fractional = false) {
			if ($fractional) {
				$number = sprintf('%.2f', $number);
			}
			while (true) {
				$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
				if ($replaced != $number) {
					$number = $replaced;
				} else {
					break;
				}
			}
			return $number;
		}
		###################################################################################
		# RETORNA A URL DO ARQUIVO MP4 DO VÍDEO DO VIMEO OU YOUTUBE  
		###################################################################################
		public static function getVimeoYoutubeDirectLink($url = "", $secure = true) {
			if (!is_string($url)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Isso não é uma string ->	ws::getVimeoYoutubeDirectLink('" . $url . "',true)"));
				exit;
			}
			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: URL inválida ->	ws::getVimeoYoutubeDirectLink('" . $url . "',true)"));
				exit;
			}
			if (strpos('list=', $url) > -1) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Não é permitido link de playList  ->	ws::getVimeoYoutubeDirectLink('" . $url . "',true)"));
			}
			$query = array();
			
			if ($secure == true) {
				$keyCode = _codePass(_crypt());
				$_temp_  = new MySQL();
				$_temp_->set_table(PREFIX_TABLES . 'ws_video');
				$_temp_->set_insert('linkvideo', $url);
				$_temp_->set_insert('keyaccess', $keyCode);
				$_temp_->insert();
				$_URL_VIDEO = ws::protocolURL() . DOMINIO .ws::rootPath. "ws-video/" . $keyCode . '/sample.mp4';
			} else {
				$tags     = get_meta_tags($url);
				$id_video = basename($tags['twitter:player']);
				$urlSite  = $tags['twitter:site'];
				if ($urlSite == "@youtube") {
					$querys = parse_url($tags['twitter:url']);
					$querys = $querys['query'];
					parse_str($querys, $query);
					parse_str(file_get_contents("http://youtube.com/get_video_info?video_id=" . $query['v']), $info);
					$streams = $info['url_encoded_fmt_stream_map'];
					$streams = explode(',', $streams);
					$mime    = null;
					foreach ($streams as $stream) {
						parse_str($stream, $data); //decode the stream
						$data['type'] = explode(';', $data['type']);
						$data['type'] = $data['type'][0];
						$mime         = $data['type'];
						if ($mime == 'video/mp4') {
							$_URL_VIDEO = $data['url'];
							break;
						}
					}
				} elseif ($urlSite == "@vimeo") {
					$json  = (json_decode(file_get_contents('http://player.vimeo.com/video/' . $id_video . '/config')));
					$resol = 0;
					$mime  = null;
					foreach ($json->request->files->progressive as $value) {
						if ($value->quality > $resol) {
							$mime       = $value->mime;
							$resol      = (int) str_replace('p', '', $value->quality);
							$_URL_VIDEO = $value->url;
						}
					}
				}
			}
			return ($_URL_VIDEO);
		}
		###################################################################################
		#
		###################################################################################
		static function restore_colunms_tool($id_tool = null) {
			##################################################################################
			# INCLUIMOS O SCRIPT DE UPDATE DI MYSQL...
			##################################################################################
			include(INCLUDE_PATH.'admin/app/ws-modules/ws-update/ws_update.php');
			##################################################################################
			# EXECUTAMOS O MYSQLI
			##################################################################################
			$mysqli = new mysqli(SERVIDOR_BD, USUARIO_BD, SENHA_BD, NOME_BD);
			if (mysqli_multi_query($mysqli, $GLOBALS["ConfigSQL"])) {
				do {
					if ($result = $mysqli->store_result()) {
						while ($row = $result->fetch_row()) {
							$resultado = 1;
						}
						$result->free();
					}
					if ($mysqli->more_results()) {
					}
				} while ($mysqli->more_results() && $mysqli->next_result());
			}
			


			##########################################################################################
			#  DATA E HORA DO INSTALAÇÃO/UPDATE   
			##########################################################################################
			// $updateDateTime = new DateTime("now"); 
			// $updateDateTime = $updateDateTime->format( 'Y-m-d H:i:sP' );
			// $I = new MySQL();
			// $I->set_table(PREFIX_TABLES . 'setupdata');
			// $I->set_where('id=1');
			// $I->set_update('data_update',$updateDateTime);
			// $I->debug(0);
			// $I->salvar();


			$_SLUG = new MySQL();
			$_SLUG->debug(0);
			$_SLUG->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$_SLUG->set_where('id="' . $id_tool.'"');
			$_SLUG->select();
			$_SLUG = $_SLUG->fetch_array[0]['slug'];


			##################################################################################
			# AGORA SELECIONAMOS OS CAMPOS DA FERRAMENTA
			##################################################################################
			$campos = new MySQL();
			$campos->set_table(PREFIX_TABLES .$_SLUG."_campos");
			$campos->set_where('ws_id_ferramenta="' . $id_tool . '"');
			$campos->select();
			
			##################################################################################
			# VARREMOS OS CAMPOS
			##################################################################################
			foreach ($campos->fetch_array as $campo) {
				if (!empty($campo['coluna_mysql'])) {
					$s = new MySQL();
					$s->set_table(PREFIX_TABLES .$_SLUG."_item");
					$s->debug(0);
					if ($campo['type'] == 'check') {
						$s->set_colum(array(
							$campo['coluna_mysql'],
							' BOOLEAN NULL DEFAULT FALSE'
						));
					} elseif ($campo['type'] == 'radio' || $campo['type'] == 'select') {
						$s->set_colum(array(
							$campo['coluna_mysql'],
							'varchar	(300) 	NULL DEFAULT "" '
						));
					} elseif ($campo['type'] == 'multiple_select') {
						$s->set_colum(array(
							$campo['coluna_mysql'],
							'varchar	(300) 	NULL DEFAULT "" '
						));
					} else {
						if ($campo['caracteres'] == "0" || $campo['caracteres'] == "") {
							$s->set_colum(array(
								$campo['coluna_mysql'],
								'LONGTEXT NULL DEFAULT ""'
							));
						} else {
							$s->set_colum(array(
								$campo['coluna_mysql'],
								'varchar (' . $campo['caracteres'] . ') NULL DEFAULT "" '
							));
						}
					}
					$colunm = ws::preventMySQLInject($campo['coluna_mysql']);
					$col 	= @mysqli_query($_conectMySQLi_,"SELECT ".$colunm. " FROM ".PREFIX_TABLES.$_SLUG."_item");
					if (!$col) {
						$s->add_column();
					}
				}
			}
		}
		###################################################################################
		#
		###################################################################################
		static function updateTool($id_tool = null) {
			$session = new session();
			##################################################################################
			# grava na sessão o ID da ferramenta
			##################################################################################
			if ($id_tool != null) {
				$session->set('ws_id_ferramenta',$id_tool);
			}
			
			##################################################################################
			# grava na sessão o PATH da ferramenta
			##################################################################################
			$session->set('PATCH','app/ws-modules/ws-model-tool');
			
			##################################################################################
			# SELECT NA TABELA DAS FERRAMENTAS
			##################################################################################
			$_tool_ = new MySQL();
			$_tool_->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$_tool_->set_where('id="' . $id_tool . '"');
			$_tool_->debug(0);
			$_tool_->select();
			
			##################################################################################
			# grava na sessão os nomes das tabelas títulos etc (temporário)
			##################################################################################
			$TABELA                          = @$_tool_->fetch_array[0];
			$session->set('_TITULO_FERRAMENTA_', $TABELA['_tit_menu_']);
			$session->set('_TABELA_','_model');
			$session->set('PATCH',$TABELA['_patch_']);
			$session->set('_TITULO_MENU_',$TABELA['_tit_menu_']);
			$session->set('_TITULO_FERRAMENTA_',$TABELA['_tit_topo_']);
			$session->set('_NIVEIS_',$TABELA['_niveis_']);
			$session->set('_FOTOS_',$TABELA['_fotos_']);
			$session->set('_GALERIAS_',$TABELA['_galerias_']);
			$session->set('_ARQUIVOS_',$TABELA['_files_']);
			
			##################################################################################
			# CASO A FERRAMENTA TENHA SIDO ALTERADA E NÃO SALVA...
			##################################################################################
			if ($TABELA['_alterado_'] == "1") {
				
				##################################################################################
				# RESTAURAMOS AS COLUNAS QUE SE PERDERAM OU NAO FORAM CRIADAS
				##################################################################################
				ws::restore_colunms_tool($id_tool);
				
				##################################################################################
				# SALVAMOS A FERRAMENTA AGORA COMO ALTERADO=0
				##################################################################################
				$s = new MySQL();
				$s->debug(0);
				$s->set_table(PREFIX_TABLES . 'ws_ferramentas');
				$s->set_where('id="' . $session->get('ws_id_ferramenta').'"');
				$s->set_update('_alterado_', '0');
				$s->salvar();
			}
		}
		###################################################################################
		#
		###################################################################################
		static function urlPath($node = null, $debug = true,$type="string") {
			if(ws::rootPath=="/"){
				$_REQUEST_URI = $_SERVER['REQUEST_URI'];
			}else{
				$_REQUEST_URI = str_replace(ws::rootPath, "/",$_SERVER['REQUEST_URI']);
			}
			if (is_string($node)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Isso não é um número ->	ws::urlPath('" . $node . "')"));
				exit;
			} elseif ($node == null || $node == 0) {
				$_REQUEST_URI = (strpos($_REQUEST_URI,"?")) ? explode('?', $_REQUEST_URI) : array($_REQUEST_URI); 
				 if($type=='array'){
						return array_filter(explode('/',$_REQUEST_URI[0]),"strlen");
				 }elseif($type=='get'){
				 	if(count($_REQUEST_URI[1])>=1){ 
				 		parse_str($_REQUEST_URI[1], $parse);
				 		return $parse;
				 	}else{ 
						_erro(ws::GetDebugError(debug_backtrace(), "Erro: Não existe variáveis GET nessa URL ->	ws::urlPath('" . $node . "')"));
				 	}
				 }else{
				 	return implode(array_filter(explode('/',$_REQUEST_URI[0]),'strlen'),'/');
				 }
			} else {
				$_REQUEST_URI = (strpos($_REQUEST_URI,"?")) ? explode('?', $_REQUEST_URI) : array($_REQUEST_URI); 
				$_URL         = array_filter(explode('/',$_REQUEST_URI[0]),'strlen');
				if($debug==false){ 
					if(isset($_URL[($node)])){ 
						return $_URL[($node)];
					}else{ 
						return false;
					}
				}else{ 
					return $_URL[($node)];
				}
			}
		}
		###################################################################################
		#
		###################################################################################
		public function draft($var = false) {
			$this->draft = $var;
			return $this;
		}
		###################################################################################
		#
		###################################################################################
		public function StartTemplate($name) {
			$this->templateName = $name;
			ob_start();
			return $this;
		}
		###################################################################################
		#
		###################################################################################
		public function EndTemplate() {
			$conteudo                             = ob_get_contents();
			$this->templates[$this->templateName] = $conteudo;
			ob_end_clean();
			return $this;
		}
		###################################################################################
		#
		###################################################################################
		public function getTemplates($name) {
			$layout = (string) $this->templates[$name];
			return $layout;
		}
		###################################################################################
		#
		###################################################################################
		static function sessionStart() {
			session_start();
		}
		###################################################################################
		#
		###################################################################################
		static function init() {
			// VERIFICA SE EXISTE INCLUDES DE PLUGINS
			$controller = new controller();
			$setupdata = new MySQL();
			$setupdata->set_table(PREFIX_TABLES . 'setupdata');
			$setupdata->set_order('id', 'DESC');
			$setupdata->set_limit(1);
			$setupdata->debug(0);
			$setupdata->select();
			$setupdata 	= $setupdata->fetch_array[0];
			$GETS 		= json_encode($_GET);

			// GERA O NOME DO CACHE
			$urlCache  = ($_SERVER['REQUEST_URI']=='/') ? 
							strtolower(str_replace(array('/','+','='),'-',base64_encode($setupdata['url_initPath'].$GETS))).'.php' : 
							strtolower(str_replace(array('/','+','='),'-',base64_encode($_SERVER['REQUEST_URI'].$GETS))).'.php';


			// VERIFICA NO SISTEMA SE O CACHE ESTÁ HABILITADO  E QUE CACHE EXISTA O ARQUIVO E INSERE
			if ($setupdata['ws_cache'] == '1' && file_exists(ws::includePath.'ws-cache/'.$urlCache)) {
				ob_end_clean();
				//	echo PHP_EOL.PHP_EOL.'<script type="text/javascript" src="'.ws::rootPath.'admin/app/templates/js/websheep/ws-client-side-record.js"></script>'.PHP_EOL.PHP_EOL;
				header('Content-Type: text/html; charset=utf-8');
				include(ws::includePath.'ws-cache/'.$urlCache);
				exit;
			}
			


			ob_start();
			$setupdata['url_plugin'] = $setupdata['url_plugin'];
			$dh                      = opendir(ws::includePath.'website/' . $setupdata['url_plugin']);


			while ($diretorio = readdir($dh)) {
				if ($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess') {
					if (file_exists(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/active')) {
						$phpConfig = ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/plugin.config.php';
						if (file_exists($phpConfig)) {
							ob_start();
							@include($phpConfig);
							$jsonRanderizado = ob_get_clean();
							$contents        = $plugin;
						}
						if (isset($contents->globalphp) && $contents->globalphp != "") {
							if (is_string($contents->globalphp)) {
								if (file_exists(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp)) {
									include(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp);
								} else {
									_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp . '</i>');
								}
							} elseif (is_array($contents->globalphp)) {
								if (count($contents->globalphp) == 1) {
									if (file_exists(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0])) {
										include(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0]);
									} else {
										_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0] . '</i>');
									}
								} elseif (count($contents->globalphp) == 2 && $contents->globalphp[1] == "before") {
									if (file_exists(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0])) {
										include(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0]);
									} else {
										_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0] . '</i>');
									}
								} elseif (count($contents->globalphp) > 2) {
									foreach ($contents->globalphp as $value) {
										if (is_string($value)) {
											$file = $value;
										} else {
											$file = $value[0];
										}
										if (is_string($value) || empty($value[1]) || $value[1] == 'before') {
											if (file_exists(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $file)) {
												include(ws::includePath.'website/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $file);
											} else {
												_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $file . '</i>');
											}
										}
									}
								}
							}
						}
					}
				}
			}
			/*########################################*/
				$setupdata = new MySQL();
				$setupdata->set_table(PREFIX_TABLES . 'setupdata');
				$setupdata->select();
				$setup = $setupdata->fetch_array[0];
			/*########################################*/
				if ($setup['domain_status'] == 0 && $setup['congelaFull'] == 1) {
					header('Content-Type: text/html; charset=utf-8');
					$controller->includeFile($setup['url_congelamento']);
					exit;
				}
				if ($setup['domain_status'] == 0 && $setup['congelaFull'] == 0 && ws::urlPath() == ws::rootPath) {
					header('Content-Type: text/html; charset=utf-8');
					$controller->includeFile($setup['url_congelamento']);
					exit;
				}
			/*########################################*/
			$_temp_ = new MySQL();
			$_temp_->set_table(PREFIX_TABLES . 'ws_pages');
			$_temp_->set_where('type="path"');
			$_temp_->select();
			
			if ($setup['url_setRoot'] != "") {
				$controller->setRoot($setup['url_setRoot']);
			}
			if ($setup['url_initPath'] != "") {
				$controller->initPath($setup['url_initPath']);
			}
			
			if ($setup['url_set404'] == "") {
				$controller->set404('erro404.php');
			} else {
				$controller->set404($setup['url_set404']);
			}
			foreach ($_temp_->fetch_array as $page) {
				if ($page['path'] != "") {
					$controller->setPath($page['file'], $page['path']);
				}
			}
			if ($setup['url_ignore_add'] == 1) {
				$controller->ignoreAdd();
			}
			$includes = new MySQL();
			$includes->set_table(PREFIX_TABLES . "ws_pages");
			$includes->set_where("type='include'");
			$includes->set_order("posicao", "ASC");
			$includes->select();
			$i = 0;

			foreach ($includes->fetch_array as $item) {
				if ($setupdata->fetch_array[0]["processoURL"] == $i) {
					$controller->go();
				}


				if ($item['file'] != "") {
					if ($i == 0) {
						$controller->includeFile($item['file'], 'first');
					} else {
						$controller->includeFile($item['file']);
					}
					$i++;
				}
			}
			if ($setupdata->fetch_array[0]["processoURL"] == $i || $i == 0) {
				$controller->go();
			}
			// VERIFICA SE EXISTE INCLUDES DE PLUGINS
			$setupdata = new MySQL();
			$setupdata->set_table(PREFIX_TABLES . 'setupdata');
			$setupdata->set_order('id', 'DESC');
			$setupdata->set_limit(1);
			$setupdata->debug(0);
			$setupdata->select();
			$setupdata = $setupdata->fetch_array[0];
			$path      = 'website/' . $setupdata['url_plugin'];
			
			$dh        = opendir(ws::includePath.'website/' . $setupdata['url_plugin']);
			while ($diretorio = readdir($dh)) {
				if ($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess') {
					if (file_exists('./../' . $path . '/' . $diretorio . '/active')) {
						$jsonConfig = ws::includePath.'website/' . $path . '/' . $diretorio . '/plugin.config.json';
						$phpConfig  = ws::includePath.'website/' . $path . '/' . $diretorio . '/plugin.config.php';
						if (file_exists($phpConfig)) {
							ob_start();
							@include($phpConfig);
							$jsonRanderizado = ob_get_clean();
							$contents        = $plugin;
						} elseif (file_exists($jsonConfig)) {
							$contents = json_decode(file_get_contents($jsonConfig));
						}
						if (isset($contents->globalphp) && $contents->globalphp != "") {
							if (is_array($contents->globalphp)) {
								if (count($contents->globalphp) == 1) {
									if (file_exists(ws::includePath.'website/' . $path . '/' . $diretorio . '/' . $contents->globalphp)) {
										include(ws::includePath.'website/' . $path . '/' . $diretorio . '/' . $contents->globalphp);
									} else {
										_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp . '</i>');
									}
								} elseif (count($contents->globalphp) == 2 && /*$contents->globalphp[1]=="before" ||*/ $contents->globalphp[1] == "after") {
									if (file_exists(ws::includePath.'website/' . $path . '/' . $diretorio . '/' . $contents->globalphp[0])) {
										include(ws::includePath.'website/' . $path . '/' . $diretorio . '/' . $contents->globalphp[0]);
									} else {
										_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->globalphp[0] . '</i>');
									}
								} else {
									foreach ($contents->globalphp as $value) {
										if (is_string($value)) {
											$file = $value;
										} else {
											$file = $value[0];
										}
										if (is_array($value) && count($value) == 2 && $value[1] == 'after') {
											if (file_exists(ws::includePath.'website/' . $path . '/' . $diretorio . '/' . $file)) {
												include(ws::includePath.'website/' . $path . '/' . $diretorio . '/' . $file);
											} else {
												_erro('Um plugin chamado "' . $contents->pluginName . '" está tentando inserir um arquivo inexistente:<br><i>/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $file . '</i>');
											}
										}
									}
								}
							}
						}
					}
				}
			}
			
			
			###################################################################
			// GUARDA TODO BUFFER EM UMA VARIÁVEL
			$_outPutCache = ob_get_contents();
			###################################################################
			$_outPutCache = str_replace('{{rootPath}}', 	ws::rootPath, $_outPutCache);
			$_outPutCache = str_replace('{{includePath}}',	ws::includePath, $_outPutCache);
			$_outPutCache = str_replace('{{domain}}',		DOMINIO, $_outPutCache);
			$_outPutCache = str_replace('</ws-tool>','',$_outPutCache);
			$_outPutCache = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $_outPutCache); 

			###################################################################
			function ReplaceImages($urlFull) { 
				$tagIMG      = $urlFull[0];
				$urlOriginal = $urlFull[1];
				$_URL_WSIMG = substr($urlOriginal,strpos($urlOriginal, "ws-img")+7);
				$_PATH_WSIMG = substr($urlOriginal,0,strpos($urlOriginal, "ws-img")+7);
				if (ws::rootPath.'ws-img/' == $_PATH_WSIMG) {
					$urlOriginalSubs = substr($urlOriginal, 8);
					$arrauURL        = explode("/", $urlOriginalSubs);
					if (count($arrauURL) == 4) {
						$fileInfo = getimagesize(ws::includePath.'website/assets/upload-files/' . end($arrauURL));
						if ($arrauURL[0] == '0') {
							$arrauURL[0] = $fileInfo[0];
						}
						if ($arrauURL[1] == '0') {
							$arrauURL[1] = $fileInfo[1];
						}
						if ($arrauURL[2] == '0') {
							$ext = substr($arrauURL[2],-3);
							if($ext== "jpg" || $ext== "jpeg" || $ext== "gif"){$arrauURL[2] = "100";}
							elseif($ext== "png"){$arrauURL[2] = "9";}
						}
						$newURL = $fileInfo[0] . '-' . $fileInfo[1] . '-' . $fileInfo[2] . '-' .  end($arrauURL);
					} elseif (count($arrauURL) == 3) {
						$fileInfo = getimagesize(ws::includePath.'website/assets/upload-files/' . end($arrauURL));
						if ($arrauURL[0] == '0') {
							$arrauURL[0] = $fileInfo[0];
						}
						if ($arrauURL[1] == '0') {
							$arrauURL[1] = $fileInfo[1];
						}
						$ext = substr($arrauURL[2],-3);
						if($ext== "jpg" || $ext== "jpeg" || $ext== "gif"){$qldd = "100";}
						elseif($ext== "png"){$qldd = "9";}
						$newURL = $arrauURL[0] . '-' . $arrauURL[1] . '-'.$qldd.'-' .  end($arrauURL);
					} elseif (count($arrauURL) == 2) {
						$fileInfo = getimagesize(ws::includePath.'website/assets/upload-files/' . end($arrauURL));
						if ($arrauURL[0] == '0') {
							$arrauURL[0] = $fileInfo[0];
						}
						if ($arrauURL[1] == '0') {
							$arrauURL[1] = $fileInfo[1];
						}
						$ext = substr($arrauURL[1],-3);
						if($ext== "jpg" || $ext== "jpeg" || $ext== "gif"){$qldd = "100";}elseif($ext== "png"){$qldd = "9";}
						$newURL = $arrauURL[0] . '-' . $fileInfo[1] . '-'.$qldd.'-' .  end($arrauURL);
						
					} else {
						$ext = substr($arrauURL[0],-3);
						if($ext== "jpg" || $ext== "jpeg" || $ext== "gif"){$qldd = "100";}elseif($ext== "png"){$qldd = "9";}
						$fileInfo = getimagesize(ws::includePath.'website/assets/upload-files/' . $arrauURL[0]);
						$newURL   = $fileInfo[0] . '-' . $fileInfo[1] . '-'.$qldd.'-' .  end($arrauURL);
					}

						$size = explode('-',$newURL);
						$_root_new_thumb 	=	ws::rootPath.'assets/upload-files/thumbnail/'. $newURL;
						$_thumb_original	=	ws::includePath.'website/assets/upload-files/'.end($arrauURL);
						$_set_thumb_process	=	ws::includePath.'website/assets/upload-files/thumbnail/'.$newURL;

						################################################################################
						# PROCESSA AS MINIATURAS
						################################################################################
						ob_start();
						$thumb = new thumb($_thumb_original);
						$thumb->setDimensions(array($size[0],$size[1]));
						$thumb->setNewThumb($_set_thumb_process);
						$thumb->setJpegQuality(100);										
						$thumb->setPngQuality(9);
						$thumb->setGifQuality(100);
						$thumb->crop=true;
						$thumb->forceDownload(false);
						$thumb->showBrowser(false);
						$thumb->process();
						ob_clean();
						################################################################################
						return str_replace($urlOriginal, $_root_new_thumb, $tagIMG);
				} else {
					return $tagIMG;
				}
			}
			// APAGA O BUFFER DE SAÍDA
			ob_end_clean();
			
			// VERIFICA NO SISTEMA SE A PÁGINA EXISTE E SE É PRA GERAR CACHE 
			 if (
			 	$setupdata['ws_cache'] == 1 && 
			 	$controller->createCache == 1 && 
			 	!file_exists(ws::includePath.'ws-cache/'.$urlCache)
			 ) {
			
				// SUBSTITUI AS TAGS DE IMAGEM PROCESSADA E RETORNA A URL DIRETA DO ARQUIVO
				$_outPutCacheHTML = preg_replace_callback('/<*img[^>]*src*=*["\']?([^"\']*)/i', 		"ReplaceImages", $_outPutCache);
				$_outPutCacheHTML = preg_replace_callback('/<*source[^>]*srcset*=*["\']?([^"\']*)/i', 	"ReplaceImages", $_outPutCacheHTML);
				$_outPutCacheHTML = preg_replace_callback('/url\([\'\"]?([^\"\'\)]+)([\"\']?\))/i', 	"ReplaceImages", $_outPutCacheHTML);
				// PESQUISA NA BASE OS DADOS DA URL ATUAL
				$returnRealPath= $controller->returnRealPath();

				// GRAVA NA BASE O LINK
				$I 					= new MySQL();
				$I->set_table(PREFIX_TABLES.'ws_link_cache_file_url');
					$data_url_atual 	= new MySQL();
					$data_url_atual->set_table('ws_pages');
					$data_url_atual->set_where('path="'.$returnRealPath.'"');
					$data_url_atual->set_where(' AND type="path"');
					$data_url_atual->set_limit(1);
					$data_url_atual->select();
					$I->select('INSERT  INTO ws_link_cache_file_url ( id_url,filename,request_uri ) select  "'.$data_url_atual->fetch_array[0]['id'].'" as id_url,"'.$urlCache.'" as filename,"'.$_SERVER['REQUEST_URI'].'" as request_uri 
						FROM DUAL  WHERE NOT EXISTS (SELECT request_uri FROM ws_link_cache_file_url   
						WHERE (request_uri="'.$_SERVER['REQUEST_URI'].'" OR filename="'.$urlCache.'"))');
					
				// GRAVA O ARQUIVO COM O NOME CORRETO
				 file_put_contents(ws::includePath.'ws-cache/'.$urlCache, $_outPutCacheHTML);
			}
				 // die(ws::includePath.'ws-cache/'.$urlCache);
			//	echo PHP_EOL.PHP_EOL.'<script type="text/javascript" src="'.ws::rootPath.'admin/app/templates/js/websheep/ws-client-side-record.js"></script>'.PHP_EOL.PHP_EOL;
			 header('Content-Type: text/html; charset=utf-8');
			 echo $_outPutCache;
			ob_end_flush();
		}
		###################################################################################
		#
		###################################################################################
		public static function getPublicLinkDownload($referencia = "") {
			$SerialKeyFiles = new MySQL();
			$SerialKeyFiles->set_table(PREFIX_TABLES . ' ws_keyfile as keyFile ');
			$SerialKeyFiles->set_order('createin', 'DESC');
			$SerialKeyFiles->set_colum('keyFile.tokenFile');
			$SerialKeyFiles->set_colum('keyFile.keyaccess');
			$SerialKeyFiles->set_colum('keyFile.expire');
			$SerialKeyFiles->set_colum('keyFile.active');
			$SerialKeyFiles->set_colum('keyFile.accessed');
			$SerialKeyFiles->join('INNER', PREFIX_TABLES . 'ws_biblioteca biblioteca', '(biblioteca.tokenFile=keyFile.tokenFile) AND (biblioteca.filename="' . $referencia . '" OR biblioteca.file="' . $referencia . '" OR biblioteca.tokenFile="' . $referencia . '" OR biblioteca.upload_size="' . $referencia . '")');
			$SerialKeyFiles->select();
			$linkFileList = Array();
			foreach ($SerialKeyFiles->obj as $link) {
				$linkFileList[] = (object) array(
					'accessed' => $link->accessed,
					'expire' => $link->expire,
					'active' => $link->active,
					'tokenFile' => $link->tokenFile,
					'keyaccess' => $link->keyaccess,
					'linkPublic' => 	"http://" .DOMINIO.ws::rootPath."ws-download/" . $link->tokenFile,
					'linkPrivate' => 	"http://" .DOMINIO.ws::rootPath."ws-download/" . $link->tokenFile . '!' . $link->keyaccess,
					'direct' => 		"http://" .DOMINIO.ws::rootPath."ws-download/" . $link->tokenFile . '!' . $link->keyaccess . '!direct=true'
				);
			}
			return $linkFileList;
		}
		###################################################################################
		#
		###################################################################################
		public static function wsInclude($include, $process = 1) {
			ob_start();
			include($include);
			$get_contents = ob_get_clean();
			$get_contents = str_replace('{{rootPath}}', 	ws::rootPath, $get_contents);
			$get_contents = str_replace('{{includePath}}',	ws::includePath, $get_contents);
			$get_contents = str_replace('{{domain}}',		DOMINIO, $get_contents);

			#######################################################################
			# ESTAVA DANDO BUG EM ARQUIVOS VAZIOS RECÉM CRIADOS, ENTÃO VERIFICAMOS 
			#######################################################################
			if ($get_contents!=""){
				echo htmlProcess::processHTML($get_contents);
			}
		}
		###################################################################################
		#
		###################################################################################
		static function GetDebugError($dados, $plus = "") {
			return ("<br>" . $plus . "<br><br><hr style='border-bottom: dashed 1px;'><br><b>Arquivo:</b>" . $dados[0]['file'] . '<br><b>Linha</b>: ' . $dados[0]['line'] . ' <br><b>Função:</b>' . $dados[0]['class'] . $dados[0]['type'] . $dados[0]['function'] . '("' . implode($dados[0]['args'], ',') . '")<br><br><hr style="border-bottom: dashed 1px;"><br><hr>');
		}
		###################################################################################
		#
		###################################################################################
		static function Lipsum($count = 10, $obj = 'paragraphs', $format = "", $linha = __LINE__, $file = __FILE__) {
			$lipsum = new Lipsum();
			if ($obj != 'word' && $obj != 'sentence' && $obj != 'paragraphs') {
				_erro(ws::GetDebugError(debug_backtrace(), "Variável inválida na classe:<br>Use: 'word','sentence' ou 'paragraphs'."));
				exit;
			}
			if ($obj == 'word') {
				return $lipsum->words($count, $format);
			}
			if ($obj == 'sentence') {
				return $lipsum->sentences($count, $format);
			}
			if ($obj == 'paragraphs') {
				return $lipsum->paragraphs($count, $format);
			}
		}
		###################################################################################
		#
		###################################################################################
		static function style($file = null, $cache = 1, $media = "all") {
			if ($cache == 0) {
				$cache = "?" . rand(0, 999999999);
			} else {
				$cache = "";
			}
			if (!is_string($file)) {
				echo "<script> alert('Isso não é um arquivo: \\n ws::style(\"" . $file . "\");');</script>" . PHP_EOL;
				exit;
			}
			if (!file_exists('./../website/' . $file) && !file_exists('./../website' . $file)) {
				echo "<script> alert('Aqruivo inexistente: \\n ws::style(\"" . $file . "\");');</script>" . PHP_EOL;
				exit;
			}
			echo '	<link	type="text/css" media="' . $media . '" rel="stylesheet" href="' . $file . $cache . '" />' . PHP_EOL;
		}
		###################################################################################
		#
		###################################################################################
		static function script($file = null, $id = null, $cache = 1) {
			if (!is_string($file)) {
				echo "<script> alert('Isso não é um arquivo: \\n ws::script(\"" . $file . "\");');</script>" . PHP_EOL;
				exit;
			}
			if (!file_exists('./../website/' . $file) && !file_exists('./../website' . $file)) {
				echo "<script> alert('Aqruivo inexistente: \\n ws::script(\"" . $file . "\");');</script>" . PHP_EOL;
				exit;
			}
			if ($cache == 0) {
				$cache = "?" . rand(0, 999999999);
			} else {
				$cache = "";
			}
			echo '<script type = "text/javascript" ';
			if ($id != null) {
				echo ' id="' . $id . '" ';
			}
			echo 'src="' . $file . $cache . '"></script>' . PHP_EOL;
		}
		#############################################################################################
		# RETORNA JSON COM AS BRANCHES DO GITHUB
		#############################################################################################
		static function get_github_branches() {
			$url = 'https://api.github.com/repos/websheep/cms/branches';
			if(remoteFileExists($url)==true){
				$branches = curl_init();
				curl_setopt($branches, CURLOPT_URL, $url);
				curl_setopt($branches, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($branches, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($branches, CURLOPT_USERAGENT, "https://api.github.com/meta");
				$json_branches = curl_exec($branches);
				curl_close($branches);
				return ($json_branches);
			}else{
				return false;
			}

		}
		#############################################################################################
		# RETORNA JSON COM OS COMMITS DO GITHUB
		#############################################################################################
		static function get_github_commits($brance='') {
			$url = 'https://api.github.com/repos/websheep/CMS/commits'.$brance;
			if(remoteFileExists($url)==true){
				$commits = curl_init();
				curl_setopt($commits, CURLOPT_URL,$url);
				curl_setopt($commits, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($commits, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($commits, CURLOPT_USERAGENT, "https://api.github.com/meta");
				$json_commits = curl_exec($commits);
				curl_close($commits);
				return ($json_commits);
			}else{
				return false;
			}
		}
		#############################################################################################
		# 
		#############################################################################################
		static function AnalyticsCode($id = null) {
			if (!is_string($id)) {
				echo "alert('Por favor, insira o seu ID do Analytics válido: \\n ws::Analytics(\"UA-xxxxxxxx-x\");');";
				exit;
			}
			echo "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		  ga('create', '" . $id . "', 'auto');
		  ga('send', 'pageview', location.pathname);" . PHP_EOL;
		}
		#############################################################################################
		# 
		#############################################################################################
		static function charset($charset = null) {
			if ($charset == null) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Por favor, coloque uma codificação válida -> ws::charset(string)'));
				exit;
			}
			echo '<meta charset="' . $charset . '">' . PHP_EOL;
		}
		#############################################################################################
		# 
		#############################################################################################
		static function urlAmigavel($string = null, $encode = null) {
			if($string==null){
				_erro(ws::GetDebugError(debug_backtrace(),'Por favor, coloque uma codificação válida -> ws::urlAmigavel(string) "'.$string.'"'));
				exit;
			}
			if ($encode == 'decode') {
				$string = url_amigavel(urldecode($string));
			} elseif ($encode == 'encode') {
				$string = url_amigavel(urlencode($string));
			} else {
				$string = url_amigavel($string);
			}
			return $string;
		}
		#############################################################################################
		# 
		#############################################################################################
		static function favicon($favicon = null) {
			if ($favicon == null) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Por favor, coloque uma URL válida como favicon -> ws::favicon(string)'));
				exit;
			} elseif (!file_exists(INCLUDE_PATH.'website/'. $favicon)) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Erro: Este arquivo não existe -> ws::favicon("' . $favicon . '")'));
				exit;
			}
			echo '<link type="image/x-icon" href="' . $favicon . '" rel="shortcut icon" />' . PHP_EOL;
		}
		
		#############################################################################################
		# 
		#############################################################################################
		static function initPath($root = true) {
			if ($root == "" || !is_string($root) || $root == null) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Por favor, coloque uma URL válida de inicio ->ws::initPath(string)'));
				exit;
			} elseif ($_SERVER['REQUEST_URI'] == "" || $_SERVER['REQUEST_URI'] == "/") {
				header('Location: /' . $root);
			}
		}
		static function thumbmail($imagem = null, $w = null, $h = null, $newPath = null) {
			if (is_array($imagem)) {
				$arrayIMG = array();
				if ($w == null) {
					$loading = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
				} else {
					$loading = $w;
				}
				foreach ($imagem as $value) {
					if (count($value) < 2) {
						_erro(ws::GetDebugError(debug_backtrace(), "Erro: Faltou um valor em:   ws::thumbmail(array(['media','imagem', 'w','h','q']),'loading');"));
						exit;
					} else {
						$media = $value[0];
						$img   = $value[1];
						$w     = (isset($value[2])) ? $value[2] : 0;
						$h     = (isset($value[3])) ? $value[3] : 0;
						$q     = (isset($value[4])) ? "&q=" . $value[4] : '';
						if ($w == "0" && $h == "0") {
							$img = ws::includePath.'admin/app/ws-modules/ws-model-tool/uploads/' . $img;
						} else {
							$img = ws::includePath.'admin/app/core/ws-thumb-crop.php?img=../app/ws-modules/ws-model-tool/uploads/' . $img . "&w=" . $w . "&h=" . $h . $q;
						}
						$arrayIMG[] = $media . ':' . $img;
					}
				}
				return '<img src="' . $loading . '" data-src="' . implode($arrayIMG, ',') . '" />';
			} else {
				if ($w == null)
					$w = '0';
				if ($h == null)
					$h = '0';
				if ($imagem == null) {
					$imagem = "../../../img/no-img.png";
					//_erro(ws::GetDebugError(debug_backtrace(),"Erro: Faltou um valor em:   ws::get_img('arquivo',$w,$h);"));
					//exit;
				}
				if ($newPath == null) {
					if ($w == "0" && $h == "0") {
						return ws::includePath.'admin/app/ws-modules/ws-model-tool/uploads/' . $imagem;
					} else {
						return ws::includePath.'admin/app/core/ws-thumb-crop.php?img=../app/ws-modules/ws-model-tool/uploads/' . $imagem . "&w=" . $w . "&h=" . $h;
					}
				} else {
					if ($w == "0" && $h == "0") {
						return $newPath . $imagem;
					} else {
						return ws::includePath.'admin/app/core/ws-thumb-crop.php?img=../../' . $newPath . '/' . $imagem . "&w=" . $w . "&h=" . $h;
					}
				}
				
				
				
			}
		}
		static function Less($less, $css) {
			if (!class_exists('lessc')) {
				include(INCLUDE_PATH.'admin/app/lib/class-lessc-inc.php');
			}
			if (!file_exists(INCLUDE_PATH.'website/' . $less)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Este arquivo não existe -> ws::Less('" . $less . "')"));
				exit;
			}
			if (filemtime($less) > @filemtime($css) || !file_exists($css)) {
				lessc::ccompile($less, $css);
			}
			return $this;
		}
		
		
		
		public function slug($slug) {
			global $_conectMySQLi_;
			$pesquisa = mysqli_query($_conectMySQLi_, 'SELECT * FROM ' . PREFIX_TABLES . 'ws_ferramentas WHERE slug LIKE "' . $slug . '" ') or die(_erro(ws::GetDebugError(debug_backtrace(), mysqli_error($_conectMySQLi_))));
			$row    = mysqli_num_rows($pesquisa);
			$result = mysqli_fetch_array($pesquisa);
			
			if (!is_string($slug)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Ops, isso não é um slug"));
				exit;
			} elseif ($row > 1) {
				_erro(ws::GetDebugError(debug_backtrace(), "Existe uma duplicidade com esse slug"));
				exit;
			} elseif ($row < 1) {
				_erro(ws::GetDebugError(debug_backtrace(), "Não existe um slug com o titulo'" . $slug . "' "));
				exit;
			}
			$this->ws_slug     			= $slug;
			$this->ws_id_ferramenta     = $result['id'];
			$this->ws_prefix_ferramenta = $result['_prefix_'];
			return $this;
		}
		
		public static function audioData($url = null, $type = "player", $w = null, $h = null, $size = 'minimo', $theme = 'light', $auto_play = 'false', $default = '') {
			$types = array(
				'player',
				'list',
				'site',
				'title',
				'description',
				'height',
				'width',
				'image'
			);
			if ($url == null || $url == "") {
				return '<div style="width:' . $w . 'px;height:' . $h . 'px;">' . $default . '</div>';
			} elseif (!strpos($url, "soundcloud") && !strpos($url, "mixcloud")) {
				return _erro("Erro ba função ws::audioData, permitido apenas link do soundcloud ou mixcloud");
			} elseif (!in_array($type, $types)) {
				return _erro("Erro na função ws::audioData.<br>Os valores permitidos: 'player','site','title','description','height','width','image'");
			} else {
				$urlParsed = parse_url($url);
				$html      = new DOMDocument();
				@$html->loadHTML(file_get_contents($url));
				$metaTags = array();
				if (strpos($url, 'mixcloud.com')) {
					foreach ($html->getElementsByTagName('meta') as $meta) {
						if ($meta->getAttribute('name') == 'twitter:player') {
							$metaTags["player"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'og:site_name') {
							$metaTags["site"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'og:title') {
							$metaTags["title"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'og:description') {
							$metaTags["description"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('name') == 'twitter:player:height') {
							$metaTags["height"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('name') == 'twitter:player:width') {
							$metaTags["width"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('name') == 'twitter:image') {
							$metaTags["image"] = $meta->getAttribute('content');
						}
					}
					if ($theme == "light") {
						$metaTags["player"] = $metaTags["player"] . '&light=1';
					}
					if ($auto_play == "true") {
						$metaTags["player"] = $metaTags["player"] . '&autoplay=1';
					}
					if ($size == "minimo" || $size == "classic") {
						$metaTags["player"] = $metaTags["player"] . '&hide_cover=1';
					}
					if ($size == "minimo") {
						$metaTags["player"] = $metaTags["player"] . '&mini=1';
					}
					if ($size == "widget") {
						$metaTags["player"] = str_replace('hide_cover=1', 'hide_cover=0', $metaTags["player"]);
					}
					if ($size == "list") {
						$metaTags["player"] = str_replace('hide_tracklist=1', 'hide_cover=0', $metaTags["player"]);
					}
					
					
				} elseif (strpos($url, 'soundcloud.com')) {
					foreach ($html->getElementsByTagName('meta') as $meta) {
						if ($meta->getAttribute('property') == 'twitter:player') {
							$metaTags["player"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'twitter:site') {
							$metaTags["site"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'twitter:title') {
							$metaTags["title"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'twitter:description') {
							$metaTags["description"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'twitter:player:height') {
							$metaTags["height"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'twitter:player:width') {
							$metaTags["width"] = $meta->getAttribute('content');
						}
						if ($meta->getAttribute('property') == 'twitter:image') {
							$metaTags["image"] = $meta->getAttribute('content');
						}
					}
					
					if ($theme == "dark") {
						$metaTags["player"] = $metaTags["player"] . '&amp;color=000000';
					}
					if ($auto_play == "true") {
						$metaTags["player"] = str_replace('auto_play=false', 'auto_play=true', $metaTags["player"]);
					}
					if ($size == "minimo" || $size == "classic" || $size == "list") {
						$metaTags["player"] = str_replace('visual=true', 'visual=false', $metaTags["player"]);
					}
				}
				if ($w == null && $h == null) {
					$w = $metaTags['width'];
					$h = $metaTags['height'];
				}
				if ($type == "player") {
					$metaTags["player"] = str_replace('origin=twitter', 'origin=website', $metaTags["player"]);
					return '<iframe width="' . $w . '" height="' . $h . '" src="' . $metaTags['player'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				} elseif ($type == "site") {
					return str_replace(array(
						'@'
					), '', $metaTags['site']);
				} else {
					return $metaTags[$type];
				}
			}
		}
		public function alias($alias = null) {
			$this->aliasStr = $alias;
			return $this;
		}
		public function paginate($pag = 1, $limit = 10) {
			$this->setpag   = array();
			$this->setpag[] = $pag;
			$this->setpag[] = $limit;
			return $this;
		}
		// TRAZER ITENS QUE O ID X  É LINKADO
		public function linker($type) {
			$this->dataRelLinker[] = $type;
			return $this;
		}
		//TRAZER ITENS LINKADO AO ID X
		public function linked($type) {
			$this->dataRelLinked[] = $type;
			return $this;
		}
		public function linktype($type) {
			$this->dataRelType = $type;
			return $this;
		}
		public static function videoData($url = null, $data = "player", $w = null, $h = null, $default = null, $autoplay = "0") {
			$types = array('site', 'url', 'title', 'description', 'image', 'player', 'id');
			if ($url == null || (!strpos($url, "youtube") && !strpos($url, "vimeo"))) {
				if ($default == null) {
					return _erro("Erro ba função ws::videoData, permitido apenas link do youtube ou vímeo");
				} else {
					return $default;
				}
			} elseif (!in_array($data, $types)) {
				return _erro("Erro na função ws::videoData.<br>Os valores permitidos: site, url, title, description, image e player");
			} else {
				$tags = get_meta_tags($url);
				if ($w == null && $h == null) {
					$w = $tags['twitter:player:width'];
					$h = $tags['twitter:player:height'];
				}
				if ($data == "player") {
					return '<iframe width="' . $w . '" height="' . $h . '" src="' . $tags['twitter:' . $data] . '?autoplay=' . $autoplay . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				} elseif ($data == "site") {
					$tags['twitter:' . $data] = str_replace(array(
						'@'
					), '', $tags['twitter:' . $data]);
					return $tags['twitter:' . $data];
				} elseif ($data == "id") {
					$tags['twitter:player'] = str_replace(array(
						'https://player.vimeo.com/video/',
						'https://www.youtube.com/embed/'
					), '', $tags['twitter:player']);
					return $tags['twitter:player'];
				} else {
					return $tags['twitter:' . $data];
				}
			}
		}
		public function innercategory($id, $cond = '=') {
			
			if (!is_numeric($id) && !is_int($id) && !is_array($id)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Ops, isso não é um ítem:  ->innercategory(" . $id . ")"));
			} else {
				if (is_int($id) || is_numeric($id)) {
					$this->setCat[] = 'linkCat.id_cat' . $cond . $id;
				}
				
				if (is_array($id)) {
					$newID = array();
					foreach ($id as $value) {
						$this->setCat[] = 'linkCat.id_cat' . $cond . $value;
					}
				}
			}
			
			return $this;
		}
		public function innerItem($id) {
			if (!is_numeric($id) && !is_int($id) && !is_array($id)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Ops, isso não é um id de ítem"));
			} else {
				$id = (int) $id;
				if (is_int($id)) {
					$this->setInnerItem[] = 'tabela_modelo.id_item="' . $id . '"';
				}
				if (is_array($id)) {
					foreach ($id as $value) {
						$this->setInnerItem[] = 'tabela_modelo.id_item="' . $value . '"';
					}
				}
			}
			return $this;
		}
		public function galery($id) {
			if (!is_int($id) && !is_array($id)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Ops, isso não é um ítem"));
			} else {
				$this->UseTable = PREFIX_TABLES . $this->ws_slug.'_img_gal';
				if (is_int($id)) {
					$this->setGal[] = 'tabela_modelo.id_galeria="' . $id . '"';
				}
				if (is_array($id)) {
					foreach ($id as $value) {
						$this->setGal[] = 'tabela_modelo.id_galeria="' . $value . '"';
					}
				}
			}
			return $this;
		}
		public function limit($init = null, $finit = null) {
			if ($init == null && $finit = null && $this->debug == true) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Valor set_limit indefinido'));
				exit;
			}
			if ($finit == null) {
				$this->limit = $init;
			} else {
				$this->limit = $init . ", " . $finit . '';
			}
			return $this;
		}
		public function setTemplate($slug = null) {
			if ($slug == null) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Valor setTemplate indefinido ou vazio'));
			}
			$this->template = $slug;
			return $this;
		}
		public function like($coluna = null, $palavra_chave = null) {
			if ($coluna == null && $palavra_chave == null) {
				_erro(ws::GetDebugError(debug_backtrace(), 'Função LIKE com valor da coluna e palavra chave estão indefinidos ou vazios'));
				exit;
			}
			if (is_string($coluna) && $palavra_chave == null) {
				if (strpos($coluna, ',')) {
					$explodestring      = explode(',', $coluna);
					$coluna             = $explodestring[0];
					$palavra_chave      = implode(array_slice($explodestring, 1), ',');
					$this->likeString[] = array(
						'tabela_modelo.' . $this->ws_prefix_ferramenta . $coluna,
						$palavra_chave
					);
				} else {
					_erro(ws::GetDebugError(debug_backtrace(), 'Função LIKE com parâmetros incorretos'));
					exit;
				}
			} else {
				$this->likeString[] = array(
					'tabela_modelo.' . $this->ws_prefix_ferramenta . $coluna,
					$palavra_chave
				);
			}
			return $this;
		}
		public function get_template($retorno) {
			$b = $c = array();
			if ($this->aliasStr != "" && substr($this->aliasStr, -1) != ".") {
				$this->aliasStr = $this->aliasStr . '.';
			}
			$ALIAS = $this->aliasStr;
			
			$a = explode("{{" . $this->aliasStr, $this->template);
			foreach ($a as $str) {
				if (stripos($str, '}}')) {
					$key    = substr($str, 0, stripos($str, '}}'));
					$newKey = explode(',', $key);
					$COLUNA = (
									$newKey[0]			!= "id" 		 
									&& $newKey[0] 		!= "ws_count" 	
									&& $newKey[0] 		!= "ws_total" 	
									&& $this->thisType 	!= "img" 		
									&& $this->thisType 	!= "gal" 
									&& $this->thisType 	!= "cat" 
									&& $this->thisType 	!= "file" 
									&& $this->thisType 	!= "img_gal" 
								) ? $this->ws_prefix_ferramenta . $newKey[0] : $newKey[0];

					if (count($newKey) == 1) {
						$b[] = "{{".$ALIAS.$key."}}";
						$c[] = @$retorno[$COLUNA];
						// se tiver 2 parâmetros
					} elseif (count($newKey) >= 2) {
						$verify = implode(array_slice($newKey, 1), ',');
						if (count($newKey) == 2) {
							if (is_numeric(@$newKey[1]) || is_int(@$newKey[1])) {
								$b[] = "{{".$ALIAS.$key."}}";
								$c[] = substr(strip_tags(str_replace("_ws_php_eol_", PHP_EOL, @$retorno[$COLUNA])), 0, $newKey[1]);
							} else {
								$result = str_replace("_ws_php_eol_", PHP_EOL, $newKey[1](@$retorno[$COLUNA]));
								$b[]    = "{{".$ALIAS.$key."}}";
								$c[]    = $result;
							}
						// se for o campo 1° e depois a função depois parametros
						} elseif (count($newKey) > 2) {
							$function 	= $newKey[1];
							$newArrayKeys = array_slice($newKey, 2);
							foreach ($newArrayKeys as $key => $value) {
								if(is_string($value) && !is_numeric($value)){
									$newArrayKeys[$key]  = str_replace("(this)", @$retorno[$COLUNA],$value);
								}else{
									$newArrayKeys[$key]  = $value;
								}
							}
							$b[] = "{{".$ALIAS.implode($newKey,',')."}}";
							$c[] = call_user_func_array($function,$newArrayKeys);
						}
					}
				}
			}
			$processo = str_replace($b, $c, $this->template);
			return stripcslashes($processo);
		}
		public function distinct($a = 1) {
			$this->distinct = $a;
			return $this;
		}
		public function utf8($encode) {
			$this->utf8 = $encode;
			return $this;
		}
		public function url($encode) {
			$this->url = $encode;
			return $this;
		}
		public function where($Where) {
			$this->setwheref .= $Where;
			return $this;
		}
		public function order($coluna = null, $order = null) {
			if (is_null($coluna) && is_null($order)) {
				_erro(ws::GetDebugError(debug_backtrace(), ""));
			}
			
			if (is_string($coluna) && is_null($order)) {
				if (strpos($coluna, ',')) {
					$colunm = explode(',', $coluna);

					$coluna = $colunm[0];
					$order  = $colunm[1];


				} else {
					$order = "asc";
				}
			}
			$not_prefix = array("id", "posicao", "ws_timespam", "token");
			
			
			$this->OrderColum = $order;
			
			if (in_array($coluna, $not_prefix) || $this->thisType != "item") {
				$this->ColumOrder = $coluna;
			} else {
				$this->ColumOrder = $this->ws_prefix_ferramenta . $coluna;
			}
			return $this;
		}
		public function filter($coluna = null, $filtro = null, $vars = "") {
			if ($coluna == null || $filtro == null) {
				_erro(ws::GetDebugError(debug_backtrace(), "Por favor, defina um valor para o filtro"));
				exit;
			}
			if (!is_string($coluna) || !is_string($filtro)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Valor incorreto, insira uma coluna e um filtro ex: ('coluna','utf8_encode'"));
				exit;
			}
			$this->filterColum[] = $coluna;
			$this->filterFn[]    = $filtro;
			$this->filterVars[]  = $vars;
			return $this;
		}
		public function go($debug = 0) {
			if ($this->thisType == "") {
				_erro(ws::GetDebugError(debug_backtrace(), "Erro: Por favor, defina o que você busca:   \$class->type()	'cat','item','gal','img','file' ou 'img_gal';"));
				exit;
			}
			$set_where = "";
			$where     = array();
			$where[]   = 'tabela_modelo.ws_id_ferramenta="' . $this->ws_id_ferramenta . '"';
			if (count($this->setInnerItem) >= 1) {
				$where[] = '(' . implode($this->setInnerItem, " OR ") . ')';
			}
			if (count($this->setItem) >= 1) {
				$where[] = '(' . implode($this->setItem, " OR ") . ')';
			}
			if (count($this->setCat) >= 1) {
				$where[] = '(' . implode($this->setCat, " OR ") . ") ";
			}
			if (count($this->setGal) >= 1) {
				$where[] = '(' . implode($this->setGal, " OR ") . ") ";
			}
			if ($this->thisType == 'img') {
				$set_where .= " AND avatar='0' AND painel='0' ";

			} elseif ($this->thisType == 'file') {
				$set_where .= " AND painel='0' ";
			}
			if ($this->setwheref != '') {
				$set_where = $this->setwheref . '  AND ' . implode($where, " AND ");
			} else {
				$set_where = implode($where, " AND ");
			}
			###################################################################################################
			# INICIA A BUSCA NA TABEÇA
			###################################################################################################
			$_busca_ = new MySQL();
			$_busca_->set_table($this->UseTable . ' as tabela_modelo ');
			
			if (count($this->setcolum) == 0 && $this->thisType != "item") {
				
			} elseif (count($this->setcolum) > 0 && $this->thisType != "item") {
				$_busca_->set_colum($this->colum);
			} else {
				
				$s = new MySQL();
				$s->set_table(PREFIX_TABLES .$this->ws_slug.'_campos');
				$s->set_where('ws_id_ferramenta="' . $this->ws_id_ferramenta . '"');
				$s->set_where('AND name<>""');
				$s->select();
				if (count($this->setCat) >= 1) {
					$_busca_->set_colum('DISTINCT(tabela_modelo.id) as id');
				}
				if ($this->thisType == "item") {
					foreach ($this->setcolum as $value) {
						$_busca_->set_colum($value);
					}
				}
				
				$_busca_->set_colum('tabela_modelo.posicao');
				$_busca_->set_colum('tabela_modelo.id');
				$_busca_->set_colum('tabela_modelo.token');
				$_busca_->set_colum('tabela_modelo.ws_author');
				$_busca_->set_colum('tabela_modelo.ws_id_ferramenta');
				$_busca_->set_colum('tabela_modelo.ws_timespam');
				foreach ($s->fetch_array as $value) {
					$_busca_->set_colum('tabela_modelo.' . $value['name']);
				}
				foreach ($this->wseditor as $value) {
					$_busca_->set_colum($value);
				}
			}
			if ($this->thisType == 'gal') {
				if (count($this->setcolum) > 0) {
					$_busca_->set_colum('(SELECT COUNT(*) FROM '.PREFIX_TABLES .$this->ws_slug.'_img_gal WHERE tabela_modelo.id = '.PREFIX_TABLES .$this->ws_slug.'_img_gal.id_galeria) as img_count');
				} else {
					$_busca_->set_colum('*, (SELECT COUNT(*) FROM '.PREFIX_TABLES .$this->ws_slug.'_img_gal WHERE tabela_modelo.id = '.PREFIX_TABLES .$this->ws_slug.'_img_gal.id_galeria) as img_count');
				}
			}
			if ($this->ColumOrder != null && $this->OrderColum != null) {
				$_busca_->set_order($this->ColumOrder, $this->OrderColum);
			}
			
			if ($this->thisType == 'item' && count($this->setCat) >= 1) {
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_link_prod_cat as linkCat ', ' tabela_modelo.id=linkCat.id_item ');
				
			} elseif ($this->thisType == 'gal' && count($this->setCat) >= 1) {
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_item as linkIntem ', ' tabela_modelo.id_item=linkIntem.id ');
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_link_prod_cat as linkCat ', ' linkIntem.id=linkCat.id_item ');
				
			} elseif ($this->thisType == 'img_gal' && count($this->setCat) >= 1) {
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_gal as linkGal', 'tabela_modelo.id_galeria=linkGal.id');
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_item as linkIntem', 'tabela_modelo.id_item=linkIntem.id');
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_link_prod_cat as linkCat', 'linkIntem.id=linkCat.id_item');
				
			} elseif ($this->thisType == 'img' && count($this->setCat) >= 1) {
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_item as linkIntem', 'tabela_modelo.id_item=linkIntem.id');
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_link_prod_cat as linkCat', 'linkIntem.id=linkCat.id_item');
				
				
			} elseif ($this->thisType == 'file' && count($this->setCat) >= 1) {
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_item as linkIntem', 'tabela_modelo.id_item=linkIntem.id');
				$_busca_->join(" INNER ", PREFIX_TABLES . $this->ws_slug.'_link_prod_cat as linkCat', 'linkIntem.id=linkCat.id_item');
				
			}
			
			
			
			//	if($this->thisType=='file' && count($this->setInnerItem)>=1 	){	
			
			//	$_busca_->join(" INNER ", PREFIX_TABLES.$this->ws_slug.'_item as linkIntem',			'tabela_modelo.id_item=linkIntem.id');
			//	}
			
			###########################################################################
			# CASO TENHA LINK ENTRE PRODUTOS
			###########################################################################
			
			
			if ((count($this->dataRelLinker) > 0 && count($this->dataRelLinked) == 0) || (count($this->dataRelLinker) == 0 && count($this->dataRelLinked) > 0)) {
				if ($this->dataRelType == "item") {
					if (count($this->dataRelLinker) > 0) {
						if ($this->draft == false) {
							$_busca_->join(" INNER ", PREFIX_TABLES . 'ws_link_itens as linkRel', 'tabela_modelo.ws_draft="0" AND tabela_modelo.ws_id_draft="0" AND linkRel.id_item_link =tabela_modelo.id 	AND  linkRel.id_item="' . implode('" OR linkRel.id_item="', $this->dataRelLinker) . '"');
						} else {
							$_busca_->join(" INNER ", PREFIX_TABLES . 'ws_link_itens as linkRel', 'tabela_modelo.ws_draft="1" AND linkRel.id_item_link =tabela_modelo.id 	AND  linkRel.id_item="' . implode('" OR linkRel.id_item="', $this->dataRelLinker) . '"');
						}
					}
					if (count($this->dataRelLinked) > 0) {
						if ($this->draft == false) {
							$_busca_->join(" INNER ", PREFIX_TABLES . 'ws_link_itens as linkRel', 'tabela_modelo.ws_draft="0" AND tabela_modelo.ws_id_draft="0" AND 	linkRel.id_item=tabela_modelo.id 		AND  linkRel.id_item_link="' . implode('" OR linkRel.id_item_link="', $this->dataRelLinked) . '"');
						} else {
							$_busca_->join(" INNER ", PREFIX_TABLES . 'ws_link_itens as linkRel', 'tabela_modelo.ws_draft="1" AND linkRel.id_item=tabela_modelo.id 		AND  linkRel.id_item_link="' . implode('" OR linkRel.id_item_link="', $this->dataRelLinked) . '"');
						}
					}
				} elseif ($this->dataRelType == "cat") {
					if (count($this->dataRelLinker) > 0) {
						$_busca_->join(" INNER ", PREFIX_TABLES . 'ws_link_itens as linkRel', 'linkRel.id_item_link=tabela_modelo.id 	AND  linkRel.id_item="' . implode('" OR linkRel.id_item="', $this->dataRelLinker) . '"');
					}
					if (count($this->dataRelLinked) > 0) {
						$_busca_->join(" INNER ", PREFIX_TABLES . 'ws_link_itens as linkRel', 'linkRel.id_item=tabela_modelo.id 		AND  linkRel.id_item_link="' . implode('" OR linkRel.id_item_link="', $this->dataRelLinked) . '"');
					}
				}
			}
			$_busca_->set_where($set_where);
			###########################################################################
			# FILTRA OS RASCUNHOS
			###########################################################################
			if ($this->thisType == 'item' || $this->thisType == 'img' || $this->thisType == 'gal' || $this->thisType == 'img_gal') {
				if ($this->draft == false) {
					$_busca_->set_where(' AND tabela_modelo.ws_draft="0" AND tabela_modelo.ws_id_draft="0"');
				} else {
					$_busca_->set_where(' AND tabela_modelo.ws_draft="1"');
				}
			}
			###########################################################################
			# RETIRA OS ERROS DE MYSQL
			###########################################################################
			$_busca_->debug(0);
			if ($this->distinct == 1) {
				$_busca_->distinct();
			}
			if ($this->utf8 != null) {
				$_busca_->utf8($this->utf8);
			}
			if ($this->url != null) {
				$_busca_->url($this->url);
			} //decode
			
			##########################################################################################
			########################################################################################## PAGINAÇÃO
			##########################################################################################
			if ($this->setpag != null) {
				$_atual_page_ = $this->setpag[0];
				$_max_posts_  = $this->setpag[1];
				$_set_limit   = ($_atual_page_ * $_max_posts_) - $_max_posts_;
				if ($_atual_page_ == 0 || $_atual_page_ == 1) {
					$_set_limit = $_max_posts_;
				} else {
					$_set_limit = $_set_limit . ',' . $_max_posts_;
				}
				$_busca_->set_limit($_set_limit);
			} elseif ($this->setpag == null && $this->limit != "") {
				$_set_limit = $this->limit;
				$_busca_->set_limit($_set_limit);
			}
			##########################################################################################
			########################################################################################## LIKE
			##########################################################################################
			if (count($this->likeString) > 0) {
				foreach ($this->likeString as $value) {
					$_busca_->like($value[0], '%' . $value[1] . '%');
				}
			}
			##########################################################################################
			########################################################################################## SELECT
			##########################################################################################
			$_busca_->select();
			$this->sql       = $_busca_->output();
			$this->_num_rows = $_busca_->_num_rows;
			
			$filtrosLenght = count($this->filterColum);
			$arrayFetchL   = $this->_num_rows;
			if ($filtrosLenght) {
				for ($i = 0; $i < $arrayFetchL; $i++) {
					for ($f = 0; $f < $filtrosLenght; $f++) {
						$getColum = $this->filterColum[$f];
						$funct    = $this->filterFn[$f];
						$vars     = $this->filterVars[$f];
						$original = $_busca_->fetch_array[$i][$getColum];
						if ($vars != "") {
							$original = str_replace("(this)", $original, $vars);
							$original = explode(',', $original);
							$original = implode($original, '","');
						}
						$_busca_->fetch_array[$i][$getColum]	= $funct($original);
					}
				}
			}
			###############################################################################
			# INSERIMOS AS TAGS ADICIONAIS
			###############################################################################
	 	  	  $i=0; 
		      foreach ($_busca_->fetch_array as $key => $value) { 
		      	$value["ws_count"] = $key;
		      	$value["ws_total"] = $this->_num_rows;
		        $_busca_->fetch_array[$key] = $value; 
		        ++$i; 
		      } 
	 	  	  $i=0; 
		      foreach ($_busca_->obj as $key => $value) { 
		      	$value->{"ws_count"} = $key;
		      	$value->{"ws_total"} = $this->_num_rows;
		        $_busca_->obj[$key] = $value; 
		        ++$i; 
		      } 
			###############################################################################
			
			if ($this->template == "") {
				array_map(array(__CLASS__, 'process_array_newprefix'), $_busca_->fetch_array);
				array_map(array(__CLASS__,'process_obj_newprefix'), $_busca_->obj);
			} else {
				
				if (count($_busca_->fetch_array) == 1) {
					$this->result .= $this->get_template($_busca_->fetch_array[0]);
				} else {
					array_map(function($a) {
						$this->result .= $this->get_template($a);
					},$_busca_->fetch_array);
				}
			}
			
			return $this;
		}
	    public function process_obj_newprefix($fetch) { 
	      $newColum = Array(); 
	      $i=1; 
	      foreach ($fetch as $key => $value) { 
	        $colum_verify = substr($key, 0, strlen($this->ws_prefix_ferramenta)); 
	        if ($colum_verify == $this->ws_prefix_ferramenta && $key != "id" && $this->thisType == "item") { 
	          $key = substr($key, strlen($this->ws_prefix_ferramenta), strlen($key)); 
	        } 
	        $newColum[$key] 		= $value; 
	        ++$i; 
	      } 
	      $this->obj[] = (Object) $newColum; 
	    } 
	    public function process_array_newprefix($fetch) { 
	      $newColum = Array(); 
	      $i=1; 
	      foreach ($fetch as $key => $value) { 
	        $colum_verify = substr($key, 0, strlen($this->ws_prefix_ferramenta)); 
	        if ($colum_verify == $this->ws_prefix_ferramenta && $key != "id" && $this->thisType == "item") { 
	          $key = substr($key, strlen($this->ws_prefix_ferramenta), strlen($key)); 
	        } 
	        $newColum[$key] = $value; 
	        ++$i; 
	      } 
	      $this->result[] = $newColum; 
	    } 
		public function colum($COLUMNS = "") {
			$conditions = array(
				"COUNT"
			);
			if (empty($this->setcolum)) {
				$this->setcolum = array();
			}
			
			
			if (is_array($COLUMNS)) {
				_erro(ws::GetDebugError(debug_backtrace(), "colum() permite apenas string"));
			} else {
				
				$condicao = strpos($COLUMNS, "(");
				$as       = strpos($COLUMNS, "as");
				if ($condicao > 0) {
					if ($as > 0) {
						$as    = explode("as", $COLUMNS);
						$COND  = $as[0];
						$alias = $as[1];
						foreach ($conditions as $condition) {
							$cond = substr($COND, 0, $condicao);
							if (in_array($cond, $conditions)) {
								$this->setcolum[] = $cond . '(tabela_modelo.' . $this->ws_prefix_ferramenta . str_replace(array(
									$cond . "(",
									")",
									' as' . $alias
								), "", $COLUMNS) . ') as ' . $this->ws_prefix_ferramenta . str_replace(" ", "", $alias);
							}
						}
					} else {
						foreach ($conditions as $condition) {
							$cond = substr($COLUMNS, 0, $condicao);
							if (in_array($cond, $conditions)) {
								$this->setcolum[] = $cond . '(tabela_modelo.' . $this->ws_prefix_ferramenta . str_replace(array(
									$cond . "(",
									")"
								), "", $COLUMNS) . ')';
							}
						}
					}
				} else {
					$this->setcolum[] = ' tabela_modelo.' . $COLUMNS . ' ';
				}
			}
			
			$this->colum = implode($this->setcolum, ' , ');
			
			
			if ($this->colum == "") {
				$this->colum = "tabela_modelo.* ";
			}
			return $this;
		}
		public function item($id) {
			$this->iditem = $id;
			if (!is_numeric($id) && !is_int($id) && !is_array($id)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Ops, isso não é um ítem"));
			} else {
				$id = (int)$id;
				if (is_int($id)) {
					$this->setItem[] = 'tabela_modelo.id="' . $id . '"';
				}
				if (is_array($id)) {
					foreach ($id as $value) {
						$this->setItem[] = 'tabela_modelo.id="' . $value . '"';
					}
				}
			}			
			return $this;
		}
		public function type($type) {

			if (!is_string($type)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Ops, isso não é um tipo"));
			} elseif (!in_array($type, $this->InnerTypes)) {
				_erro(ws::GetDebugError(debug_backtrace(), "Tipo expecificaado incorreto!"));
			} else {
				if ($type == "cat") {
					$this->UseTable = PREFIX_TABLES .$this->ws_slug."_cat";
				} elseif ($type == "item") {
					$this->UseTable = PREFIX_TABLES .$this->ws_slug."_item";
				} elseif ($type == "gal") {
					$this->UseTable = PREFIX_TABLES .$this->ws_slug."_gal";
				} elseif ($type == "img") {
					$this->UseTable = PREFIX_TABLES .$this->ws_slug."_img";
				} elseif ($type == "img_gal") {
					$this->UseTable = PREFIX_TABLES .$this->ws_slug."_img_gal";
				} elseif ($type == "file") {
					$this->UseTable = PREFIX_TABLES .$this->ws_slug."_files";
				}
				$this->thisType = $type;
			}
			return $this;
		}
	}