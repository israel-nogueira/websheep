<?
	############################################################################################################################################
	############################################################################################################################################
	############################################################################################################################################
	#
	#
	# FUNÇÕES GLOBAIS DO SISTEMA QUE NECESSITAM DO MYSQLI FUNCTIONANDO
	#
	#
	############################################################################################################################################
	############################################################################################################################################
	############################################################################################################################################

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
			$path = implode(array_filter(explode('/',$path)),"/");
			define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
		}

		if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

	##########################################################################################################
	# 	FUNÇÕES GLOBAIS DO SISTEMA
	##########################################################################################################
		include_once(INCLUDE_PATH.'admin/app/lib/ws-globals-functions.php');
		
	##########################################################################################################
	# 	INCLUIMOS A SESSÃO
	##########################################################################################################
		include_once(INCLUDE_PATH.'admin/app/lib/class-session.php');
		
	##########################################################################################################
	# RETORNA UM TOKEN INÉDITO NA COLUNA SETADA 
	##########################################################################################################
		function _token($tabela,$coluna,$type="all"){
			$tk 					=	_crypt($type);
			$setToken				= 	new MySQL();
	 		$setToken->set_table($tabela);
			$setToken->set_where($coluna.'="'.$tk.'"');
			$setToken->select();
			if($setToken->_num_rows!=0){
				$tk = _crypt();
				_token($tabela,$coluna);
			}else{
				return $tk;
			}
		}

	##########################################################################################################
	#	CASO ESTEJA LOGADO DIRETAMENTE COM SERIALKEY
	##########################################################################################################
		function verifyUserLogin($return = false) {
			if (ws::urlPath(3, false)) {
				$keyAccess = ws::getTokenRest(ws::urlPath(3, false), false);
			} elseif (ws::urlPath(2, false)) {
				$keyAccess = ws::getTokenRest(ws::urlPath(2, false), false);
			}else{
				$keyAccess = false;
			}
			
			$log_session = new session();
			if ((SECURE == TRUE && $keyAccess == false) && ($log_session->verifyLogin() != true)) {
				$log_session->finish();
				if ($return == true) {
					return false;
				} else {
				echo '<script>
							document.cookie.split(";").forEach(function(c) {document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");}); 
							if(window.location.pathname=="'.ws::rootPath.'admin/"){
								window.top.location.reload();
							}else{
								window.top.location = "'.ws::rootPath.'admin/";
							}
					</script>';
					exit;
				}
			} else {
				if ($return == true) {
					return true;
				}
			}
		}
			
	##########################################################################################################
	#	FUNÇÃO QUE APLICA O RASCUNHO DO ÍTEM
	##########################################################################################################
		function descartaRascunho($ws_id_ferramenta,$id_item,$apenasAplica=false){
			global $_conectMySQLi_;
				$getSlug = new MySQL();
				$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
				$getSlug->set_where('id="'.$ws_id_ferramenta.'"');
				$getSlug->select();
				$_SLUG_ = $getSlug->fetch_array[0]['slug'];


			##########################################################################################################
			# EXCLUI O RASCUNHO DO ÍTEM
			##########################################################################################################
			if($apenasAplica==false){
				$get_draft				= new MySQL();
				$get_draft->set_table(PREFIX_TABLES.$_SLUG_."_item");
				$get_draft->set_where('ws_draft="1"');
				$get_draft->set_where('AND ws_id_draft="'.$id_item.'"');
				$get_draft->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$get_draft->exclui();
			}
			##########################################################################################################
			# EXCLUI OS REGISTROS DAS IMAGENS DO ÍTEM ORIGINAL
			##########################################################################################################
				$ExclIMGs				= new MySQL();
				$ExclIMGs->set_table(PREFIX_TABLES.$_SLUG_."_img");
				$ExclIMGs->set_where('ws_draft="1"');
				$ExclIMGs->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$ExclIMGs->set_where('AND id_item="'.$id_item.'"');						
				$ExclIMGs->exclui();

			##########################################################################################################
			# EXCLUI AS GALERIAS ORIGINAIS
			##########################################################################################################
				$ExclGal = new MySQL();
				$ExclGal->set_table(PREFIX_TABLES.'_model_gal');
				$ExclGal->set_where('ws_draft="1"');
				$ExclGal->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$ExclGal->set_where('AND id_item="'.$id_item.'"');
				$ExclGal->exclui();
			##########################################################################################################
			# EXCLUI AS IMAGENS DAS GALERIAS ORIGINAIS
			##########################################################################################################
				$ExclGal = new MySQL();
				$ExclGal->set_table(PREFIX_TABLES.'_model_img_gal');
				$ExclGal->set_where('ws_draft="1" AND ws_id_ferramenta="'.$ws_id_ferramenta.'" AND id_item="'.$id_item.'"');
				$ExclGal->exclui();

			##########################################################################################################
			# EXCLUI OS REGISTROS DOS ARQUIVOS DO ÍTEM ORIGINAL
			##########################################################################################################
				$ExclFiles				= new MySQL();
				$ExclFiles->set_table(PREFIX_TABLES.$_SLUG_."_files");
				$ExclFiles->set_where('ws_draft="1"');
				$ExclFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$ExclFiles->set_where('AND id_item="'.$id_item.'"');
				$ExclFiles->exclui();

			##########################################################################################################
			# EXCLUI OS REGISTROS DOS RELACIONAMENTOS ORIGINAIS
			##########################################################################################################
				$ExclLink				= new MySQL();
				$ExclLink->set_table(PREFIX_TABLES.$_SLUG_."_link_prod_cat");
				$ExclLink->set_where(' ws_draft="1" ');
				$ExclLink->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$ExclLink->set_where('AND id_item="'.$id_item.'"');
				$ExclLink->exclui();
			##########################################################################################################
			# EXCLUI OS REGISTROS DOS RELACIONAMENTOS ORIGINAIS
			##########################################################################################################
				$Set_Link				= new MySQL();
				$Set_Link->set_table(PREFIX_TABLES.'ws_link_itens');
				$Set_Link->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'" AND id_item="'.$id_item.'"');
				$Set_Link->exclui();

			##########################################################################################################
				criaRascunho($ws_id_ferramenta,$id_item,$apenasAplica);
				return true;
		}

		function aplicaRascunhoPorTabela($tabela,$id_item){
					##########################################################################################################
					# IMAGENS
					##############################################################################################################
						$RASCUNHO				= new MySQL();
						$RASCUNHO->set_table($tabela);
						$RASCUNHO->set_where('ws_draft="1" AND ws_tool_item="'.$id_item.'"');
						$RASCUNHO->select();

						$ORIGINAL				= new MySQL();
						$ORIGINAL->set_table($tabela);
						$ORIGINAL->set_where('ws_draft="0" AND ws_tool_item="'.$id_item.'"');
						$ORIGINAL->select();

					##########################################################################################################
					# CASO EXISTA UM RASCUNHO...
					##########################################################################################################
						if($RASCUNHO->_num_rows>0){
							##########################################################################################################
							# EXCLUI OS REGISTROS ORIGINAIS
							##########################################################################################################
							$exclui_img				= new MySQL();
							$exclui_img->set_table($tabela);
							$exclui_img->set_where('ws_draft="0" AND ws_tool_item="'.$id_item.'"');
							$exclui_img->exclui();
							##########################################################################################################
							# SUBSTITUI PELOS RASCUNHOS
							##########################################################################################################
							$Set_img = new MySQL();
							$Set_img->select('UPDATE `'.$tabela.'` SET  `id`=`ws_id_draft`, `ws_draft`=0, `ws_id_draft`=0 WHERE(ws_draft="1" AND ws_tool_item="'.$id_item.'")');
						}


		} 	

	##########################################################################################################
	#	FUNÇÃO QUE APLICA O RASCUNHO DO ÍTEM
	##########################################################################################################
		function aplicaRascunho($ws_id_ferramenta,$id_item,$apenasAplica=false){
				global $_conectMySQLi_;

				$getSlug = new MySQL();
				$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
				$getSlug->set_where('id="'.$ws_id_ferramenta.'"');
				$getSlug->select();
				$_SLUG_ = $getSlug->fetch_array[0]['slug'];

		
				##########################################################################################################
				# SELECIONA O RASCUNHO A SER APLICADO
				##########################################################################################################
				$ITEM_RASCUNHO				= new MySQL();
				$ITEM_RASCUNHO->set_table(PREFIX_TABLES.$_SLUG_."_item");
				$ITEM_RASCUNHO->set_where('ws_draft="1"');
				$ITEM_RASCUNHO->set_where('AND ws_id_draft="'.$id_item.'"');
				$ITEM_RASCUNHO->select();

				##########################################################################################################
				# CASO EXISTA UM RASCUNHO...
				##########################################################################################################
				if($ITEM_RASCUNHO->_num_rows>0){ 
					##########################################################################################################
					# EXCLUIMOS O ITEM ORIGINAL
					##########################################################################################################
					$get_draft				= new MySQL();
					$get_draft->set_table(PREFIX_TABLES.$_SLUG_."_item");
					$get_draft->set_where('id="'.$id_item.'"');
					$get_draft->exclui();

					##########################################################################################################
					# E SUBSTITUIMOS O ID DO RASCUNHO PARA O ID DO ITEM EXCLUIDO
					##########################################################################################################
					$Set_img = new MySQL();
					$Set_img->set_table(PREFIX_TABLES.$_SLUG_.'_item');
					$Set_img->set_where('id="'.$ITEM_RASCUNHO->fetch_array[0]['id'].'"');
					$Set_img->set_update("ws_draft",0);
					$Set_img->set_update("ws_id_draft",0);
					$Set_img->set_update("ws_tool_item",0);
					$Set_img->set_update("id",$id_item);
					$Set_img->salvar();
				}

				aplicaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_."_img",			$id_item);
				aplicaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_."_gal",			$id_item);
				aplicaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_."_img_gal",		$id_item);
				aplicaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_."_files",			$id_item);
				aplicaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_."_link_prod_cat",	$id_item);
				aplicaRascunhoPorTabela(PREFIX_TABLES.'ws_link_itens',	$id_item);
				return true;
		}

	##########################################################################################################
	#	FUNÇÃO QUE CRIA O RASCUNHO DO ÍTEM
	##########################################################################################################
		function criaRascunhoPorTabela($tabela,$id_item){


			$getOriginal				= new MySQL();
			$getOriginal->set_table($tabela);
			$getOriginal->set_where('ws_draft="0"');
			$getOriginal->set_where('AND ws_tool_item="'.$id_item.'"');
			$getOriginal->set_order('id','DESC');
			$getOriginal->select();

			$getDraft				= new MySQL();
			$getDraft->set_table($tabela);
			$getDraft->set_where('ws_draft="1"');
			$getDraft->set_where('AND ws_tool_item="'.$id_item.'"');
			$getDraft->select();
			


			if($getDraft->_num_rows<1 && $getOriginal->_num_rows>0){
				$getItemOriginal = new MySQL();
				##########################################################################################################
				# APAGAMOS A TABELA TEM E CRIAMOS NOVAMENTE COMO UM CLONE DAS IMAGENS DA FERRAMENTA
				##########################################################################################################
				$getItemOriginal->select('DROP TABLE IF EXISTS tmp');
				$getItemOriginal->select('CREATE TABLE tmp SELECT * FROM '.$tabela.' WHERE ws_tool_item='.$id_item);

				##########################################################################################################
				# REFAZEMOS OS IDS A PARTIR DO ULTIMO IDA DA TABELA DE IMAGENS ORIGINAIS
				##########################################################################################################
				$getItemOriginal->select('SET @pos:='.$getOriginal->fetch_array[0]['id'].';');
				$getItemOriginal->select('UPDATE tmp SET ws_draft=1, ws_id_draft=id, id=( SELECT @pos := @pos + 1 );');

				##########################################################################################################
				# E INSERIMOS TODOS OS DADOS NA TABELA ORIGINAL PEGANDO OS DADOS DA TABELA tmp
				##########################################################################################################
				$getItemOriginal->select('INSERT INTO '.$tabela.' SELECT * FROM tmp');
			}
		}


		function criaRascunho($ws_id_ferramenta=0,$id_item=null, $imagens=false){
				global $_conectMySQLi_;

				##########################################################################################################
				# CAPTAMOS O SLUG DA FERRAMENTA PARA ASSIM SETAR O NOME DA TABELA A SER MANIPULADA
				##########################################################################################################
				$getSlug = new MySQL();
				$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
				$getSlug->set_where('id="'.$ws_id_ferramenta.'"');
				$getSlug->select();
				$_SLUG_ = $getSlug->fetch_array[0]['slug'];

				##########################################################################################################
				# VERIFICA SE JÁ EXISTE ALGUM RASCUNHO
				##########################################################################################################
				$getDraft				= new MySQL();
				$getDraft->set_table(PREFIX_TABLES.$_SLUG_."_item");
				$getDraft->set_where('ws_draft="1"');
				$getDraft->set_where('AND ws_tool_item="'.$id_item.'"');
				$getDraft->select();

				##########################################################################################################
				# CASO NÃO EXISTA RASCUNHO NENHUM
				##########################################################################################################

				if($getDraft->_num_rows<1){
					##########################################################################################################
					# CAPTAMOS O ULTIMO ID INSERIDO NA TABELA
					##########################################################################################################
					$lastID				= new MySQL();
					$lastID->set_table(PREFIX_TABLES.$_SLUG_."_item");
					$lastID->set_order('id','DESC');
					$lastID->set_limit(1);
					$lastID->select();

					$getItemOriginal = new MySQL();
					##########################################################################################################
					# EXCLUIMOS A TABELA TMP
					##########################################################################################################					
					$getItemOriginal->select('DROP TABLE IF EXISTS tmp');

					##########################################################################################################
					# CRIAMOS UMA NOVA TABELA TMP INSERINDO O REGISTRO CLONADO DO ITEM A SER CRIADO O RASCUNHO
					##########################################################################################################
					$getItemOriginal->select('CREATE TABLE tmp SELECT * FROM '.PREFIX_TABLES.$_SLUG_.'_item WHERE id='.$id_item);

					##########################################################################################################
					# AGORA MUDAMOS OS ID'S PARA CARACTERIZAR UM RASCUNHO, E MUDAMOS O ID PARA NÃO DAR CONFLITO
					##########################################################################################################
					$getItemOriginal->select('UPDATE tmp SET id='.($lastID->fetch_array[0]['id']+1).', ws_draft=1 ,ws_tool_item='.$id_item.', ws_id_draft='.$id_item);
					
					##########################################################################################################
					# FINALMENTE INSERIMOS O NOSSO RASCUNHO
					##########################################################################################################
					$getItemOriginal->select('INSERT INTO '.PREFIX_TABLES.$_SLUG_.'_item SELECT * FROM tmp');
				}
				# NAS PROXIMAS TABELAS E FEITO PELA FUNÇÃO, POIS PODE OU NÃO TER RASCUNHO
				# AO CONTRARIO DO ÍTEM QUE É NECESSARIO TER
				criaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_.'_img',$id_item);
				criaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_.'_gal',$id_item);
				criaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_.'_img_gal',$id_item);
				criaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_.'_files',$id_item);
				criaRascunhoPorTabela(PREFIX_TABLES.$_SLUG_.'_link_prod_cat',$id_item);
				criaRascunhoPorTabela(PREFIX_TABLES.'ws_link_itens',$id_item);

			##########################################################################################################
			# FIM (apenas se ñ tiover rascunho do ítem)
			##########################################################################################################
			return true;
		}

	##########################################################################################################
	# FUNÇÃO QUE CRIA O JSON COM A LISTA DOS PLUGINS INSTALADOS
	##########################################################################################################
		function refreshJsonPluginsList(){
			$setupdata 	= new MySQL();
			$setupdata->set_table(PREFIX_TABLES.'setupdata');
			$setupdata->set_order('id','DESC');
			$setupdata->set_limit(1);
			$setupdata->debug(0);
			$setupdata->select();
			$setupdata = $setupdata->fetch_array[0];
			//################################################################################################
			$_path_plugin_ = INCLUDE_PATH.'website/'.$setupdata['url_plugin']; 
			$json_plugins = array();
			if(is_dir($_path_plugin_)){
				$dh = opendir($_path_plugin_);
				while($diretorio = readdir($dh)){
					if($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess' ){
						$phpConfig 	= $_path_plugin_.'/'.$diretorio.'/plugin.config.php';
						if(file_exists($phpConfig)){
							ob_start();
							@include($phpConfig);
							$jsonRanderizado=ob_get_clean();
							$contents=$plugin;
						}
						$itemArray = Array();
						if(file_exists($_path_plugin_.'/'.$diretorio.'/active')){
							@$contents->{'active'}="yes";
						}else{
							@$contents->{'active'}="no";
						}
						$contents->{'realPath'}=str_replace(INCLUDE_PATH.'website/','',$_path_plugin_).'/'.$diretorio;
						//################################################################################################
						$json_plugins[] = $contents;
					}
				}
			}
			file_put_contents(INCLUDE_PATH.'admin/app/templates/json/ws-plugin-list.json', json_encode($json_plugins,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
			return true;
		}

	##########################################################################################################
	# INSTALA UMA FERRAMENTA EXTERNA
	##########################################################################################################
		function installExternalTool(){

			##########################################################################################################
			# VERIFICA SE EXISTE UM SLUG COM O MESMO NOME JÁ
			##########################################################################################################
			$verifySug = new MySQL();
			$verifySug->select('SELECT * FROM '.PREFIX_TABLES.'ws_ferramentas WHERE(slug="'.$_POST['slugTool'].'")');
			if($verifySug->_num_rows>0){
				return json_encode(
							array(
								'status'=>'falha',
								'content'=>'Ops! Verificamos que o SLUG <strong>"'.$_POST['slugTool'].'"</strong> Já esta cadastrado no sistema',
								'colunas'=>""
							)
						);
				exit;
			}

			##########################################################################################################
			# VERIFICA SE EXISTE DIFERENÇA ENTRE AS COLUNAS DO WS_FERRAMENTAS
			##########################################################################################################
			$colunasSystem = new MySQL();
			$colunasSystem->select('SHOW COLUMNS FROM '.PREFIX_TABLES.'ws_ferramentas');
		    $colunas_system 		= array();
			foreach($colunasSystem->fetch_array as $colum){ $colunas_system[] = $colum['Field'];};
			$colunas_ferramenta 	= explode(',',$_POST['system']);

			$ColunasAdicionais		= array_diff($colunas_ferramenta,$colunas_system);
			$FaltaColunas 			= array_diff($colunas_system,$colunas_ferramenta);

			if(count($FaltaColunas)>0){
				return json_encode(
							array(
								'status'=>'falha',
								'content'=>'Ops! Verificamos que esta ferramenta necessita de algumas colunas na tabela <strong>'.PREFIX_TABLES.'ws_ferramentas</strong>:',
								'colunas'=>implode($FaltaColunas," , "),
							)
						);
				exit;
			}
			if(count($ColunasAdicionais)>0){
				return json_encode(
							array(
								'status'=>'falha',
								'content'=>'Ops! Verificamos que esta ferramenta tem algumas colunas adicionais na tabela da ferramenta <strong>ws_ferramentas</strong>:',
								'colunas'=>implode($ColunasAdicionais," , "),
							)
						);
				exit;
			};


			$user 	= new session();
			$binary = new Base2n(6);
			$json   = str_replace(
						array('{PREFIX_TABLES}','{SLUG}','{TITULO}','{WS_AUTHOR}','{USER}'), 
						array(PREFIX_TABLES,$_POST['slugTool'],$_POST['nameTool'],$user->get('id'),$user->get('id')),
						$binary->decode($_POST['SQL'])
					);

			$mysqli = new mysqli(SERVIDOR_BD, USUARIO_BD, SENHA_BD, NOME_BD);
			if (mysqli_multi_query($mysqli,$json)) {
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

			return json_encode(
							array(
								'status'=>'sucesso',
								'content'=>"",
								'colunas'=>""
							)
						);

		}

	##########################################################################################################
	# VERIFICA SE A COLUNA DA TABELA ÍTEM EXISTE, SE EXISTE CRIA OUTRO NOME NOME_1 NOME_2 NOME_3 
	##########################################################################################################
		function duplicateColumName($colunaVerificar){
			$i=2;
			$colunasAtuais = array();
			$D = new MySQL();
			$D->set_table(PREFIX_TABLES.'_model_item');
			$D->show_columns();
			foreach ($D->fetch_array as $coluna){$colunasAtuais[] =$coluna['Field'];};
			verificaNovamente:
			if(!in_array($colunaVerificar, $colunasAtuais)){
				//	CASO NAO EXISTA NENHUMA COLUNA COM ESSE NOME ADD NA TABELA
				return $colunaVerificar;exit;
			}else{ //CASO JÁ EXISTA
				//final com o i
				$str = '_'.$i;
				//final atual da coluna
				$finalAtual = substr($colunaVerificar,-strlen($str));
				//Nome da coluna sem o i
				$colunName  = substr($colunaVerificar,0,-strlen($str));
				//verifica se é uma coluna já duplicada, com final _(int)  se for aumenta um valor e verifica
				if($finalAtual==$str){
					$i = $i+1;
					$colunaVerificar = $colunName.'_'.$i;
				}else{
					//se nao for duplicado ou com valor numerico, adiciona _2
					$colunaVerificar = $colunaVerificar.'_'.$i;
				}
			}
			goto verificaNovamente;
		}

	##########################################################################################################
	# VERIFICA SE UMA TABELA EXISTE
	##########################################################################################################
		function _verifica_tabela($tabela) {
				global $_conectMySQLi_;
				while ($row = mysqli_fetch_row(mysqli_query($_conectMySQLi_, "SHOW TABLES"))) {
						if ($tabela == $row[0]) {
								return false;
								exit;
						}
				}
				return true;
				exit;
		}

	##########################################################################################################
	# EXECUTA ARQUIVO SQL
	##########################################################################################################
		function exec_SQL($filename=null){
			global $_conectMySQLi_;
			if(file_exists($filename)){
				$templine = '';
				$filename 	= file_get_contents($filename);
				$filename 	= str_replace('{_prefix_}',PREFIX_TABLES,$filename);
		 		$filename   = str_replace(array("\n","\r" ,PHP_EOL),PHP_EOL, $filename); 
		 		$lines 		= explode(PHP_EOL,$filename);
				foreach($lines as $line_num => $line) {
					if (substr($line, 0, 2) != '--' && $line != '') {
						$templine .= $line;
						if (substr(trim($line), -1, 1) == ';') {
							mysqli_query($_conectMySQLi_,$templine) or die("Erro em gravar banco de dados: \n :".PHP_EOL.mysqli_error() );
							$templine = '';
						}
					}
				}
				return true;
			}elseif(is_string($filename)){
				$templine 	= '';
				$filename 	= str_replace('{_prefix_}',PREFIX_TABLES,$filename);
		 		$filename   = str_replace(array("\n","\r" ,PHP_EOL),PHP_EOL, $filename); 
		 		$lines 		= explode(PHP_EOL,$filename);
				foreach($lines as $line_num => $line) {
					if (substr($line, 0, 2) != '--' && $line != '') {
						$templine .= $line;
						if (substr(trim($line), -1, 1) == ';') {
							mysqli_query($_conectMySQLi_,$templine) or die("Erro em gravar banco de dados: \n :".mysqli_error().PHP_EOL.'Comando: '.$templine );
							$templine = '';
						}
					}
				}
				return true;

			}
		}


	##########################################################################################################
	# A PARTIR DAQUI AS FUNÇÕES EM BREVE ESTARÃO OBSOLETAS
	##########################################################################################################

		function _set_session($id){
				// ob_start();
				// if(empty($_SESSION) && session_id()!=$id){
				// 	ini_set('session.cookie_secure',	1);
				// 	ini_set('session.cookie_httponly',	1);
				// 	ini_set('session.cookie_lifetime', "432000");
				// 	ini_set("session.gc_maxlifetime",	"432000");
				// 	ini_set("session.use_trans_sid", 	0);
				// 	ini_set('session.use_cookies', 	1);
				// 	ini_set('session.use_only_cookies', 1);
				// 	ini_set('session.name', 			'_WS_');
				// 	session_cache_expire("432000");
				// 	session_cache_limiter('private');
				// 	session_id($id);
				// 	session_name($id);
				// 	session_start();
				// }
		};

		function _session(){
			if(isset($_COOKIE['ws_session'])){
				session_name('_WS_');
				@session_id($_COOKIE['ws_session']);
				@session_start(); 
				@session_regenerate_id();
			};
		};