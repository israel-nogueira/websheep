<?
	error_reporting(E_ALL);


	function returnTokenAccess() {
		die(ws::setTokenRest("3 seconds"));
	}

	function validateTokenAccess() {
		print_r($_GET);
		//	if(empty($_GET)) die("Var GET é necessária");
		//	$tk 			= (isset($_GET['TokenAccess']))	? $_GET['TokenAccess'] 	: null;
		//	$die 			= (isset($_GET['die']))			? $_GET['die'] 			: true;
		//	$firstAccess 	= (isset($_GET['firstAccess']))	? $_GET['firstAccess'] 	: true;
		//	die(getTokenRest($tk,$die,$firstAccess));
	}

	function gravaThumbTool() {
		$s = new MySQL();
		$s->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$s->set_where('id="' . $_POST['ws_id_ferramenta'] . '"');
		$s->set_update('image_tool', $_POST['image']);
		$s->salvar();
	}
	function refreshMenuDesktop() {
		echo "<!-- Insere menu principal -->".PHP_EOL;
		include(INCLUDE_PATH.'admin/app/core/ws-menu-dashboard.php');
	}
	function installUpdateZip() {
		$file = './../../../' . $_REQUEST['file'];
		$zip  = new ZipArchive();
		if ($zip->open($file) === TRUE) {
			if ($zip->extractTo('./../../../')) {
				@unlink($file);
				echo 'sucesso';
			} else {
				echo 'falha';
			}
			$zip->close();
			exit;
		} else {
			echo "falha em abrir o arquivo!";
		}
	}
	function returnFileInnerPlugin() {
		###################################################################################
		#  SEPARAMOS O PATH DO PLUGIN
		###################################################################################
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP	= $SETUP->fetch_array[0];

		###################################################################################
		#  CAMINHO ABSOLUTO DO ARQUIVO REQUERIDO
		###################################################################################
		$dir_plugin   = explode("/",str_replace($SETUP['url_plugin'].'/',"",dirname(substr($_REQUEST['page'],strpos($_REQUEST['page'],$SETUP['url_plugin'])))));
		$pathPlugins  = ws::includePath.'website/'.substr($_REQUEST['page'], strpos($_REQUEST['page'],$SETUP['url_plugin']), strlen($_REQUEST['page']));
		$_ConfigPHP_  = ws::includePath.'website/'.$SETUP['url_plugin'].'/'.$dir_plugin[0].'/plugin.config.php';

		###################################################################################
		#  VERIFICAMOS SE TEM VARIÁVEIS GET, SE TIVER JUNTA A VARIÁVEL GET
		###################################################################################
		$filename     = explode('?', $pathPlugins);
		if (count($filename)==2) {
			parse_str($filename[1], $output);
			$_GET = $_GET + $output;
		}
		$filename = $filename[0];


		if (!file_exists($filename)) {
			echo "Ops, parece que o arquivo <b>'" . ws::rootPath.str_replace(ws::includePath.'website/',"",$filename). "'</b> não existe...";
			exit;
		} else {
			ob_start();
			include($_ConfigPHP_);
			$contents             = $plugin;
			$contents->pathPlugin = $SETUP['url_plugin'];
			$contents->fullPath   = dirname($_ConfigPHP_);  
			include($filename);
			$jsonRanderizado = ob_get_clean();
			echo $jsonRanderizado;
		}
	}
	function liveEditorActive() {
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		@setcookie('liveEditorActive', 'true', (time() + (24 * 3600)), '/;samesite=strict');
		sleep(2);
		echo $SETUP['url_initPath'];
		exit;
	}
	function detroyEditorActive() {
		unset($_COOKIE['liveEditorActive']);
		setcookie('liveEditorActive', null, -1, '/;samesite=strict');
		echo true;
		exit;
	}
	function installToolPanel() {
		echo installExternalTool();exit;
	}
	function exportToolFile($ID_TOOL = null, $multiple = false, $encode = false, $type='echo') {

		##############################################################################################
		# VERIFICA SE VEM O ID POR PARAMETRO OU POST
		##############################################################################################
		if ($ID_TOOL == null) {
			$ID_TOOL = $_REQUEST['id_tool'];
		}

		##############################################################################################
		# MONTAMOS AS VARIÁVEIS
		##############################################################################################
		$TOOL             = new MySQL();
		$getItemOriginal    = new MySQL();
		$getColums        = new MySQL();
		$getCampos        = new MySQL();
		$OUTPUT           = array();
		$SQL            = array();
		$SYSTEM           = array();


		##############################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA PELO ID PARA MONTAR O NOME DAS TABELAS
		##############################################################################################
		$TOOL->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$TOOL->set_where('id="' . $ID_TOOL . '"');
		$TOOL->select();
		$__SLUG__ = $TOOL->fetch_array[0]['slug'];  
		if(isset($_REQUEST['type'])) $type=$_REQUEST['type'];
		if (empty($_REQUEST['toolToPlugin'])) $_REQUEST['toolToPlugin'] = "off";

		##########################################################################################################
		# CRIAMOS AGORA O SHOW CREATE TABLE DAS TABELAS 
		##########################################################################################################
		$tables = array("_campos","_cat","_files","_gal","_img","_img_gal","_item","_link_op_multiple","_link_prod_cat","_op_multiple");
		foreach ($tables as $value) {
			$ColunasItem = new MySQL();
			$ColunasItem->select('SHOW CREATE TABLE '.PREFIX_TABLES.$__SLUG__.$value);
			$OUTPUT[]= str_replace('CREATE TABLE `'.PREFIX_TABLES.$__SLUG__, 'CREATE TABLE IF NOT EXISTS `{PREFIX_TABLES}{SLUG}', $ColunasItem->fetch_array[0]['Create Table']).';';
		}

		##########################################################################################################
		# EXCLUIMOS A TABELA TMP
		##########################################################################################################          
		$getItemOriginal->select('DROP TABLE IF EXISTS tmp');
		$getItemOriginal->select('CREATE TABLE tmp SELECT * FROM '.PREFIX_TABLES.'ws_ferramentas WHERE id='.$ID_TOOL);
		$getItemOriginal->select('SELECT * FROM tmp');


		$getItemOriginal->fetch_array[0]['_tit_menu_']    = "{TITULO}";
		$getItemOriginal->fetch_array[0]['ws_author']     = "{WS_AUTHOR}";
		$getItemOriginal->fetch_array[0]['slug']      = "{SLUG}";
		$getItemOriginal->fetch_array[0]['_prefix_']    = "";

		$colunasSystem = new MySQL();
		$colunasSystem->select('SHOW COLUMNS FROM '.PREFIX_TABLES.'ws_ferramentas');
		$SYSTEM_TOOL = array();
		foreach($colunasSystem->fetch_array as $colum){ $SYSTEM_TOOL[] = $colum['Field'];};

		$OUTPUT[]=  'INSERT INTO `{PREFIX_TABLES}ws_ferramentas`  ( `' . implode(array_slice($SYSTEM_TOOL, 1), '`,`' ). '`) VALUES( \'' . implode(array_slice($getItemOriginal->fetch_array[0], 1), "','") . '\');'.PHP_EOL;
		
	

		$COLUNAS_CAMPOS = array();
		$colunasTool = new MySQL();
		$colunasTool->select('SHOW COLUMNS FROM '.PREFIX_TABLES.$__SLUG__.'_campos');
		foreach($colunasTool->fetch_array as $colum){ $COLUNAS_CAMPOS[] = $colum['Field'];};
		$getCampos->select('SELECT * FROM '.PREFIX_TABLES.$__SLUG__.'_campos');
		foreach ($getCampos->fetch_array as $value) {
			$OUTPUT[]= 'INSERT INTO `{PREFIX_TABLES}{SLUG}_campos`  ( `' . implode(array_slice($COLUNAS_CAMPOS,1), '`,`' ) . '`) VALUES((select max(id) from {PREFIX_TABLES}ws_ferramentas),\'' . implode(array_slice($value, 2), "','") . '\');'.PHP_EOL;    
		}


		$OUTPUT[]= 'INSERT INTO `ws_user_link_ferramenta`  (`ws_author`,`id_user`,`id_ferramenta`) VALUES({WS_AUTHOR},{USER},(select max(id) from {PREFIX_TABLES}ws_ferramentas));'.PHP_EOL;    
	
		$binary 		= new Base2n(6);
		$json   		= $binary->encode(implode($OUTPUT,PHP_EOL));
		$encode       	= false;
		$jsonName     	= 'importedTools/' . $TOOL->fetch_array[0]['slug'] . '.ws';
		$SQL    		=   array(
								'name'			=> $TOOL->fetch_array[0]['_tit_menu_'],
								'description'	=> $TOOL->fetch_array[0]['_desc_'],
								'avatar'		=> $TOOL->fetch_array[0]['avatar'],
								'system'		=> $SYSTEM_TOOL,
								'sql'			=> $json
							);
		if ($multiple   == true) {
			return $SQL;
			exit;
		}else{
			$SQL      = array($SQL);
			if($type=="return") {
				return $SQL;
			}else{
				echo json_encode($SQL, JSON_PRETTY_PRINT);
			}
		}
		exit;
	}
	function export_All_Tool_File() {
		$ferramentas = $_REQUEST['tools'];
		$newTool     = Array();
		foreach ($ferramentas as $value) {
			$newTool[] = exportToolFile($value, true,false,"return");
		}
		echo json_encode($newTool, JSON_PRETTY_PRINT);
		exit;
	}
	function exclude_All_Tool_File() {
		$ferramentas = $_REQUEST['tools'];
		$newTool     = Array();
		foreach ($ferramentas as $value) {
			excluiFerramenta($value);
		}
		echo "sucesso";
		exit;
	}
	function ckeditorcss() {
		header("Content-type: text/css;");
		if ($_REQUEST['color'] == "") {
			$_REQUEST['color'] = "000000";
		}
		if ($_REQUEST['background'] == "") {
			$_REQUEST['background'] = "FFFFFF";
		}
		$reset = 'html, body, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, font, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,form, label,fieldset,legend,table, caption, tbody, tfoot, thead, tr, th, td {margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;}body {line-height: 1;}ol, ul {list-style: none;}ol.envieForm {list-style: decimal;}blockquote, q {quotes: none;}blockquote:before, blockquote:after,q:before, q:after {content:"";content: none;}:focus {outline: 0;}ins {text-decoration: none;}del {text-decoration: line-through;}table {border-collapse: collapse;border-spacing: 0;}*{margin: 0;padding: 0;border: 0;outline: 0;}
				body{
					font-family: Arial, sans-serif, Verdana, "Trebuchet MS";
					font-size: 12px;
					margin: 20px;
					background-color:#' . $_REQUEST['background'] . ';
					color:#' . $_REQUEST['color'] . ';
					line-height: 16px;
				}
				p{
					margin: 10px 0;
				}
				table,td,tr{border:solid 1px;}
				';
		echo retira_acentos(urldecode($reset));
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		echo retira_acentos(urldecode($SETUP['stylecss']));
		exit;
	}
	function ckeditorjson() {
		header('Content-type: application/x-javascript');
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		echo urldecode($SETUP['stylejson']);
		exit;
	}
	function DebugSintax() {
		$file = './../../../' . $_REQUEST['file'];
		if (file_exists($file)) {
			file_put_contents($file, urldecode($_REQUEST['code']));
		}
	}

	function _excl_dir_($Dir) {
		$condicional_files = $Dir != './../../app/ws-modules/ws-model-tool/uploads' && $Dir != './../../app/ws-modules/_bkpws_/arquivos' && $Dir != './../../app/ws-modules/ws-users/upload';
		$condicional_Path  = $Dir != './../../app/ws-modules/ws-model-tool' && $Dir != './../../app/ws-modules/_bkpws_' && $Dir != './../../app/ws-modules/ws-users' && $Dir != './../../ws-modules';
		if ($dd = opendir($Dir)) {
			while (false !== ($Arq = readdir($dd))) {
				if ($Arq != "." && $Arq != "..") {
					$Path = "$Dir/$Arq";
					if (is_dir($Path)) {
						ExcluiDir($Path);
					} elseif (is_file($Path)) {
							unlink($Path);
					}
				}
			}
			closedir($dd);
		}
		if ($condicional_files && $condicional_Path) {
			rmdir($Dir);
		}
	}
	function galeriaGeralImagensTextarea() {
		echo '<div class="galeria_img_textarea">';
		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$t_ferramentas->select();
		foreach ($t_ferramentas->fetch_array as $img) {
			echo '<div class="imagem" data-img="'.ws::rootPath.'website/assets/upload-files/' . $img['file'] . '">
						<img src="'.ROOT_WEBSHEEP.'ws-img/150/170/50/' . $img['file'] . '">
				 </div>';
		}
		echo "</div>";
	}
	function excluirBibliotecaSelecionada() {
		$imagem = new MySQL();
		$imagem->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$imagem->set_where('file="' . implode($_REQUEST['selectAntigo'], '" OR file="') . '"');
		if ($imagem->exclui()) {
			foreach ($_REQUEST['selectAntigo'] as $value) {
				$file = ws::rootPath.'website/assets/upload-files/' . $value;
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
	}
	function galeriaGeralImagens() {
		$style_img   = "";
		$style_file  = "";
		$style_radio = "";
		if ($_REQUEST['type'] == "img") {
			$style_img   = "display:block";
			$style_file  = "display:none";
			$style_radio = "display:none";
		} elseif ($_REQUEST['type'] == "file") {
			$style_img   = "display:none";
			$style_file  = "display:block";
			$style_radio = "display:none";
		} elseif ($_REQUEST['type'] == "all") {
			$style_radio = "display:block";
			$style_img   = "display:block";
			$style_file  = "display:none";
		}
		if (@$_REQUEST['multiple'] == "0") {
			$style_excluir = "display:none";
		} else {
			$style_excluir = "display:block";
		}
		echo '<div class="combo-Folder-Left-Biblioteca">';
		echo '<div id="selectTypeFile" style="padding: 10px;margin-bottom: 10px;' . $style_radio . '">
					<label style="padding: 13px 5px;"><input  type="radio"  name="typeFile" value="img" checked="true">Imagens</label>
						<label style="padding: 13px 5px;"><input  type="radio"  name="typeFile" value="file">Arquivos</label>
				</div>';
		
		echo '<div class="botao2 excluirBibliotecaSelecionada" style="padding: 10px;margin-bottom: 10px;' . $style_excluir . '" >Excluir selecionadas</div>';
		echo '<div class="c"></div>';
		$imagem = new MySQL();
		$imagem->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$imagem->set_where('token_group=""');
		$imagem->select();
		echo '<div class="all bg01" style="clear: both;text-align:left;position:relative;padding:10px;cursor:pointer;width: calc(100% - 22px);' . $style_img . '">Todas imagens</div>';
		if ($imagem->_num_rows >= 1) {
			echo '<div class="semgrupo bg01" style="text-align:left;position:relative;padding:10px;cursor:pointer;float: left;width: 195px;' . $style_img . '">Imagens sem grupo</div>';
		}
		$anos = new MySQL();
		$anos->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$anos->set_colum('DATE_FORMAT(saved, "%Y") as ano');
		$anos->distinct();
		$anos->select();
		foreach ($anos->fetch_array as $ano) {
			echo '<div class="c"></div>';
			echo '<div class="ano bg01"    style="cursor: pointer;text-align: left;position: relative;padding: 10px;">Ano: ' . $ano['ano'] . '</div>';
			echo '<div class="container_year bg02" style="position: relative;padding: 4px;float: left;widt;width: calc(100% - 10px);display: none;">';
			$mes = new MySQL();
			$mes->set_table(PREFIX_TABLES . 'ws_biblioteca');
			$mes->set_colum('DATE_FORMAT(saved, "%M") as mes');
			$mes->set_where('DATE_FORMAT(saved, "%Y")="' . $ano['ano'] . '"');
			$mes->set_where('AND token_group<>""');
			$mes->distinct();
			$mes->select();
			foreach ($mes->fetch_array as $mes) {
				echo '<div class="c"></div>';
				echo '<div class="ano bg01"       style="text-align: left;position: relative;padding: 10px;cursor: pointer;">' . $mes['mes'] . '</div>';
				echo '<div class="container_mes bg03"   style="text-align:left;display: none;position: relative;padding: 4px;float: left;widt;width: calc(100% - 10px);">';
				$dias = new MySQL();
				$dias->set_table(PREFIX_TABLES . 'ws_biblioteca');
				$dias->set_colum('DATE_FORMAT(saved, "%d")  as  dias');
				$dias->set_where('DATE_FORMAT(saved, "%Y")    ="' . $ano['ano'] . '"');
				$dias->set_where('AND DATE_FORMAT(saved, "%M")  ="' . $mes['mes'] . '"');
				$dias->set_where('AND token_group<>""');
				$dias->distinct();
				$dias->select();
				foreach ($dias->fetch_array as $dias) {
					echo '<div class="c"></div>';
					echo '<div class="dia bg01"    style="position: relative;padding: 10px;cursor: pointer;;text-align: left">Dia ' . $dias['dias'] . '</div>';
					echo '<div class="container_dia bg02" style="position: relative;padding: 10px;float: left;width: calc(100% - 22px);">';
					$uploads = new MySQL();
					$uploads->set_table(PREFIX_TABLES . 'ws_biblioteca');
					$uploads->set_colum('token_group');
					$uploads->set_where('token_group<>""');
					$uploads->set_where('AND DATE_FORMAT(saved, "%d")   ="' . $dias['dias'] . '"');
					$uploads->set_where('AND DATE_FORMAT(saved, "%Y")   ="' . $ano['ano'] . '"');
					$uploads->set_where('AND DATE_FORMAT(saved, "%M") ="' . $mes['mes'] . '"');
					$uploads->distinct();
					$uploads->select();
					$i = 1;
					foreach ($uploads->fetch_array as $uploads) {
						echo '<div class="c"></div>';
						echo '<div class="grupo bg01"   data-grupo="' . $uploads['token_group'] . '" style="position: relative;padding: 10px;cursor: pointer;">Upload ' . $i . '</div>';
						$i++;
					}
					echo '</div>';
				}
				echo '</div>';
			}
			echo '</div>';
		}
		echo '</div>';
		
		echo '<div class="galeria_img_textarea" style="' . $style_img . '">';
		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$t_ferramentas->set_order('saved', 'DESC');
		$t_ferramentas->set_colum('DATE_FORMAT(saved, "%H:%i:%S") as  horas');
		$t_ferramentas->set_colum('file');
		$t_ferramentas->set_colum('filename');
		$t_ferramentas->set_colum('saved');
		$t_ferramentas->set_colum('token_group');
		$t_ferramentas->set_where('type="image/jpg"');
		$t_ferramentas->set_where('OR type="image/jpeg"');
		$t_ferramentas->set_where('OR type="image/png"');
		$t_ferramentas->set_where('OR type="image/gif"');
		$t_ferramentas->select();
		foreach ($t_ferramentas->fetch_array as $img) {
			echo '<div class="imagem" legenda="<b>Filename:</b>' . $img['filename'] . '<br><b>Upload:</b> ' . $img['horas'] . '" data-group="' . $img['token_group'] . '" data-img="' . $img['file'] . '"><img src="'.ROOT_WEBSHEEP.'ws-img/170/150/100/' . $img['file'] . '"></div>';
		}
		echo "</div>";
		
		echo '
				<style>
					.galeria_file_textarea tr:nth-child(even){
						background-color:#e7ecef;
					}
					.galeria_file_textarea tr.active{
						border: solid 1px #76c338;
						background-color: rgba(117, 152, 26, 0.17);
						color: #44861c;
					}
					.galeria_file_textarea tr{
						height: 35px;
					}
					.galeria_file_textarea td {
							padding: 13px;
					}
					.dataTables_length{
						color: #FFF;
						position: relative;
						height: 40px;
						float: right;
						width: 170px;
					}
					.lengthLabel{
						color: #FFF;
						
					}
					.dataTables_length label select{
						position: absolute;
						left: 0;
						padding: 7px;
						width: calc(100% - 14px);
						margin: 3px;
						margin-left: 9px;
						}
					.dataTables_info{
						display:none;
					}
					.dataTables_filter{
						color:#FFF;
						position: relative;
						height: 40px;
						float: left;
						width: calc(100% - 10px);
					}
					.dataTables_filter label input{
						position: absolute;
						left: 0;
						padding: 7px;
						width: 100%;
						margin: 4px;
						top: 0;
						border: solid 1px #3e6ca9;
						}
				</style>
						<div  class="galeria_file_textarea" style="' . $style_file . '">
							<table id="data-tbl-simple" class="data-tbl-simple table table-bordered w1" style="width: 100%;">
								<thead class="cabecalhoTable">
									<tr style="color:#FFF;height:30px;">
										<td  style="padding: 10px;padding-left: 20px;padding-right: 0;color:#FFF;" class="bg05">Original</td>
										<td  style="padding: 10px;padding-left: 20px;padding-right: 0;color:#FFF;" class="bg05">Filename</td>
										<td  style="padding: 10px;padding-left: 20px;padding-right: 0;color:#FFF;" class="bg05">Tipo</td>
										<td  style="padding: 10px;padding-left: 20px;padding-right: 0;color:#FFF;" class="bg05">Peso</td>
										<td  style="padding: 10px;padding-left: 20px;padding-right: 0;color:#FFF;" class="bg05">Data de Upload</td>
										<td  style="padding: 10px;padding-left: 20px;padding-right: 0;color:#FFF;" class="bg05">Extensão</td>
									</tr>
								</thead>
								<tbody>';
		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$t_ferramentas->set_colum('id');
		$t_ferramentas->set_colum('filename');
		$t_ferramentas->set_colum('file');
		$t_ferramentas->set_colum('type');
		$t_ferramentas->set_colum('upload_size');
		$t_ferramentas->set_colum('DATE_FORMAT(saved, "%d/%m/%Y %H:%i") AS uploaded');
		$t_ferramentas->set_where('type<>"image/jpg"');
		$t_ferramentas->set_where('AND type<>"image/jpeg"');
		$t_ferramentas->set_where('AND type<>"image/png"');
		$t_ferramentas->set_where('AND type<>"image/gif"');
		$t_ferramentas->select();
		foreach ($t_ferramentas->fetch_array as $img) {
			$ext = explode(".", $img['filename']);
			echo '<tr class="item" data-img="' . $img['file'] . '">   
												<td>' . $img['filename'] . '</td>   
												<td>' . $img['file'] . '</td>   
												<td>' . $img['type'] . '</td> 
												<td>' . $img['upload_size'] . '</td>  
												<td>' . $img['uploaded'] . '</td> 
												<td>' . end($ext) . '</td> 
										</tr>';
		}
		
		echo '</tbody> 
						</table>';
		echo "</div>";
		echo '<script>
							sanfona(".ano,.mes,.dia");
							$("*[legenda]").LegendaOver();
							$(".grupo").unbind("click tap press").bind("click tap press",function(){
								var grupo= $(this).data("grupo");
								window.imgSelectedBiblioteca = Array();
								$(".galeria_img_textarea .imagem").removeClass("active");
								$(".galeria_img_textarea .imagem").hide("fast");
								$(".galeria_img_textarea .imagem[data-group=\'"+grupo+"\']").show("fast");
							})
							$(".semgrupo").unbind("click tap press").bind("click tap press",function(){
								window.imgSelectedBiblioteca = Array();
								$(".galeria_img_textarea .imagem").removeClass("active");
								$(".galeria_img_textarea .imagem").show("fast");
							})
							$(".all").unbind("click tap press").bind("click tap press",function(){
								window.imgSelectedBiblioteca = Array();
								$(".galeria_img_textarea .imagem").show("fast");
							})
							$(".excluirBibliotecaSelecionada").unbind("click tap press").bind("click tap press",function(){
									var selectAntigo = window.imgSelectedBiblioteca;
									TopAlert({mensagem: "Você tem certeza de que gostaria de excluir essas imagens?<span id=\"excluiTop\" class=\"botao2\" style=\"padding:7px 15px\">Sim</span> ou <span id=\"cancelaTop\" class=\"botao3\" style=\"padding:7px 15px\">Não</span> ?",
												type: 4,
												timer:15000,
												clickclose:false,
												botClose:"#excluiTop",
												onClose:function(){},
												posFn:function(){
													$("#excluiTop").unbind("click tap press").bind("click tap press",function(){
														window.DesativaTabela()
														var verifyBD = $.ajax({
															type: "POST",
															sync: true,
															url: "./app/ws-modules/ws-tools/functions.php",
															data: { function: "excluirBibliotecaSelecionada", selectAntigo:selectAntigo}
														}).done(function(msg) {
															window.imgSelectedBiblioteca = Array();
															$(".galeria_img_textarea .active,.galeria_file_textarea .active").remove();
															window.AtivaTabela()
														});
													})
												},
												timer: 10000,
												type: 1
											})
								})
							$("#selectTypeFile input").change(function(){
								var typeSelected = $("#selectTypeFile input:checked").val();
									window.imgSelectedBiblioteca = Array();
									$(".galeria_img_textarea .imagem,.galeria_file_textarea tr.item").removeClass("active");
								if(typeSelected=="img"){
									$(".all,.ano,.semgrupo").show();
									$(".galeria_img_textarea").show();
									$(".galeria_file_textarea").hide();
								}else{
									$(".all,.ano,.semgrupo").hide();
									$(".ano").next().removeClass("SanfonaOpen").hide();
									$(".galeria_img_textarea").hide();
									$(".galeria_file_textarea").show();
								}
							});';
		
		if ($_REQUEST['type'] == "file") {
			echo '$(".all,.ano").hide();$(".ano").next().removeClass("SanfonaOpen").hide();';
		}
		
		echo '
						//=============================================================================================
						//=============================================================================================

						jQuery.extend(jQuery.fn.dataTableExt.oSort, {
								"datetime-us-pre": function (a) {
										var b = a.match(/(\d{1,2})\/(\d{1,2})\/(\d{2,4}) (\d{1,2}):(\d{1,2}) (am|pm|AM|PM|Am|Pm)/),
												month = b[1],
												day = b[2],
												year = b[3],
												hour = b[4],
												min = b[5],
												ap = b[6].toLowerCase();

										if (hour == \'12\') {
												hour = \'0\';
												if (ap == \'pm\') {
														hour = parseInt(hour, 10) + 12;
												}

												if (year.length == 2) {
														if (parseInt(year, 10) < 70) {
																year = \'20\' + year;
														}
														else {
																year = \'19\' + year;
														}
												}
												if (month.length == 1) {
														month = \'0\' + month;
												}
												if (day.length == 1) {
														day = \'0\' + day;
												}
												if (hour.length == 1) {
														hour = \'0\' + hour;
												}
												if (min.length == 1) {
														min = \'0\' + min;
												}

												var tt = year + month + day + hour + min;
												return tt;
										}
								},

								"datetime-us-asc": function (a, b) {
												return a - b;
								},

								"datetime-us-desc": function (a, b) {
										return b - a;
								}
						});

						jQuery.fn.dataTableExt.aTypes.unshift(
								function (sData) {
										if (sData !== null && sData.match(/\d{1,2}\/\d{1,2}\/\d{2,4} \d{1,2}:\d{1,2} (am|pm|AM|PM|Am|Pm)/)) {
												return \'datetime-us\';
										}
										return null;
								}
						);
						//=============================================================================================
						//=============================================================================================
					//	window.AtivaTabela    = function (){$("#data-tbl-simple").dataTable({"bAutoWidth": false,"sPaginationType": "full_numbers","autoWidth": false,"bPaginate": false,"columnDefs": [{ type: \'datetime-us\', target:[4] }]})}
					//	window.DesativaTabela   = function (){$("#data-tbl-simple").dataTable().fnDestroy();}
					//	window.AtivaTabela();
						</script>';
	}
	function returnCamposTool() {
		$t_page = new MySQL();
		$t_page->set_table(PREFIX_TABLES .$slug. '_campos');
		$t_page->set_where('ws_id_ferramenta="' . $_REQUEST['idTool'] . '"');
		$t_page->set_where('AND name <>""');
		$t_page->select();
		foreach ($t_page->fetch_array as $item) {
			echo '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
		}
		exit;
	}
	function returnPaths() {
		$t_page = new MySQL();
		$t_page->set_table(PREFIX_TABLES . 'ws_pages');
		$t_page->set_where('id="' . $_REQUEST['id'] . '"');
		$t_page->select();

		$formulario = "
		<form id='form_new_name' style='width:500px' data-idPage='".$_REQUEST['id']."'>
			<div class='w1' style='text-aling:left'>".ws::getlang("pages>modalDetailsPage>title")."</div>
			<input name='titulo_page' style='padding: 10px 20px;margin: 10px;width: calc(100% - 20px);' class='inputText'   value='" . $t_page->fetch_array[0]['title'] . "'></input>
			<div class='w1' style='text-aling:left'>Nome do arquivo:</div>
			<input name='oldName' type='hidden' class='inputText'     value='" . $t_page->fetch_array[0]['file'] . "' placeholder='ex: includes/arquivo.php'></input>
			<input name='file' style='padding: 10px 20px;margin: 10px;    width: calc(100% - 20px);' class='inputText'    value='" . $t_page->fetch_array[0]['file'] . "' placeholder='ex: includes/arquivo.php'></input>
			<label class='w1' style='cursor:pointer;text-aling:left;padding: 0px 20px;'><input type='radio' value='renomear'  name='renomear'/>".ws::getlang("pages>modalDetailsPage>rename")."</label>
			<label class='w1' style='cursor:pointer;text-aling:left;padding: 0px 20px;'><input type='radio' value='novo'  name='renomear'> ".ws::getlang("pages>modalDetailsPage>newFile")."</label>
			<div class='c' style='margin-bottom:20px'></div>
			<div class='w1' style='text-aling:left'>URL de acesso:</div>
			<input name='path' style='padding: 10px 20px;margin: 10px;    width: calc(100% - 20px);' class='inputText'  value='" . $t_page->fetch_array[0]['path'] . "' placeholder='ex: folder1/folder2/folder3'></input>
			<div class='c' style='margin-bottom:20px'></div>
			<div class='w1' style='text-aling:left'>".ws::getlang("pages>modalDetailsPage>siteMap")."</div>";
		$formulario .= "<select id='tools' name='toolMaster' style='position: relative;float: left;margin: 10px;padding: 10px;width: 45%;'><option value=''></option>";
		$tools = new MySQL();
		$tools->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$tools->set_where('App_Type="1"');
		$tools->select();
		
		foreach ($tools->fetch_array as $item) {
			if ($item['id'] == $t_page->fetch_array[0]['tool_master']) {
				$select = "selected";
			} else {
				$select = "";
			}
			$formulario .= '<option value="' . $item['id'] . '" ' . $select . '>' . $item['_tit_topo_'] . '</option>';
		}
		$formulario .= "</select>";
		$formulario .= "<select id='campos' style='position: relative;float: left;margin: 10px;padding: 10px;width: 45%;'>";
		
		if ($t_page->fetch_array[0]['tool_master'] != "") {
			$campos = new MySQL();
			$campos->set_table(PREFIX_TABLES .$slug. '_campos');
			$campos->set_where('ws_id_ferramenta="' . $t_page->fetch_array[0]['tool_master'] . '"');
			$campos->set_where('AND name <>""');
			$campos->select();
			foreach ($campos->fetch_array as $campo) {
				$formulario .= '<option value="' . $campo['name'] . '">' . $campo['name'] . '</option>';
			}
		}
		
		$formulario .= "</select>
			<input name='sitemap_xml' id='path' style='padding: 10px 20px;margin: 10px;    width: calc(100% - 20px);' class='inputText' value='" . $t_page->fetch_array[0]['sitemap_xml'] . "' placeholder='ex: pasta1/pasta2/pasta3'></input>

			<label class='w1' style='cursor:pointer;text-aling:left;padding:5px;'><input type='radio' value='item'    name='typeList'/> ".ws::getlang("pages>modalDetailsPage>type>item")."</label>
			<label class='w1' style='cursor:pointer;text-aling:left;padding:5px;'><input type='radio' value='cat'     name='typeList'>  ".ws::getlang("pages>modalDetailsPage>type>category")."</label>
			<label class='w1' style='cursor:pointer;text-aling:left;padding:5px;'><input type='radio' value='gal'     name='typeList'>  ".ws::getlang("pages>modalDetailsPage>type>Gallery")."</label>
			<label class='w1' style='cursor:pointer;text-aling:left;padding:5px;'><input type='radio' value='img'     name='typeList'>  ".ws::getlang("pages>modalDetailsPage>type>Image")."</label>
			<label class='w1' style='cursor:pointer;text-aling:left;padding:5px;'><input type='radio' value='img_gal'   name='typeList'>  ".ws::getlang("pages>modalDetailsPage>type>GalleryImage")."</label>
			<label class='w1' style='cursor:pointer;text-aling:left;padding:5px;'><input type='radio' value='files'   name='typeList'>  ".ws::getlang("pages>modalDetailsPage>type>File")."</label>
			<div class='c' style='margin-bottom:20px'></div>
			<script>$('input[type=\"radio\"][value=\"" . $t_page->fetch_array[0]['typeList'] . "\"]').click();</script>
		</form>";
		echo str_replace(PHP_EOL, "", $formulario);
	}
	function template($id, $_tit_menu_, $_token_) {
		$retorno = "<div class='subtool ' data-id='" . $id . "' data-token='" . $_token_ . "'>
				<div>
					<div class='w1 titulo'>" . $_tit_menu_ . "</div>
					<div id='combo'>
						<div id='detalhes_img'>
							<span><img style='top:5px;position:relative;'   class='limpar legenda'    legenda='Limpar ferramenta'       src='./app/templates/img/websheep/vassoura.png'></span>
							<span><img                    class='mover_item legenda mover_sub_item'   legenda='Mover'             src='./app/templates/img/websheep/arrow-move.png'></span>
							<span><img                    class='editar legenda'    legenda='Editar'            src='./app/templates/img/websheep/layer--pencil.png'></span>
							<span><img                    class='exportar legenda'  legenda='Exportar ferramenta'     src='./app/templates/img/websheep/export_tool.png'></span>
							<span><img                    class='excluir legenda'   legenda='<img class=\"editar\"      src=\"./app/templates/img/websheep/exclamation.png\" style=\"position: absolute;margin-top: -2px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Excluir'    src='./app/templates/img/websheep/cross-button.png'></span>
					</div>
				</div>
			</div>";
		return $retorno;
	}
	// foi colocado aqui para que nao dê conflito  no mesmo arquivo do notify
	function verifica_notify() {
		$s = new MySQL();
		$s->set_table(PREFIX_TABLES . 'notificacoes');
		$s->set_where('visualizado="0"');
		$s->set_where('AND excluido="0"');
		$s->select();

		$session = new session();
		$session->set('logs',$s->_num_rows);
	}
	function listnerNotify() {
		set_time_limit(0);
		while (true) {
			clearstatcache();
			$old_rows   = isset($_POST['old_rows']) ? (int) $_POST['old_rows'] : 'null';
			$_new_data_ = new MySQL();
			$_new_data_->set_table(PREFIX_TABLES . 'ws_log');
			$_new_data_->set_order('id', 'DESC');
			$_new_data_->select();
			$new_rows = $_new_data_->_num_rows;
			if ($old_rows != $new_rows) {
				$user_mysql = new MySQL();
				$user_mysql->set_table(PREFIX_TABLES . 'ws_usuarios');
				$user_mysql->set_where('id="' . $_new_data_->fetch_array[0]['id_user'] . '"');
				$user_mysql->debug(0);
				$user_mysql->select();
				echo json_encode(array(
					'old_rows' => $new_rows,
					'resposta' => '<strong>' . $user_mysql->fetch_array[0]['nome'] . '</strong> ' . $_new_data_->fetch_array[0]['titulo']
				));
				break;
			} else {
				sleep(2);
				continue;
			}

		}
	}
	function limparFerramenta() {

		$id_ferramenta      = $_REQUEST['ws_id_ferramenta'];
		$getSlug        = new MySQL();
		$getSlug        ->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug        ->set_where('id="' . $id_ferramenta . '"');
		$getSlug        ->select();
		$slug           = $getSlug->fetch_array[0]['slug'];
		$clear  = array();
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_item;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_cat;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_gal;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_files;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_img_gal;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_img;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_link_prod_cat;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_op_multiple;";
		$clear[]= 'TRUNCATE TABLE '.PREFIX_TABLES.$slug . "_link_op_multiple;";

		foreach ($clear as $value) {
			$clearTable = new MySQL();
			$clearTable->select($value);
		}
	}

	function limpaFolder() {
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$U->set_where('_grupo_pai_="' . $_REQUEST['id_folder'] . '"');
		$U->set_update('_grupo_pai_', '0');
		$U->salvar();
	}


	function excluiFolder() {
		$selectFile = new MySQL();
		$selectFile->set_table(PREFIX_TABLES . 'ws_pages');
		$selectFile->set_where('id_tool="' . $_REQUEST['id_folder'] . '"');
		$selectFile->select();
		
		$_exc_ferramenta_ = new MySQL();
		$_exc_ferramenta_->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$_exc_ferramenta_->set_where('id="' . $selectFile->fetch_array[0]['id_tool']. '"');
		$_exc_ferramenta_->exclui();
		
		$_exc_ferramenta_ = new MySQL();
		$_exc_ferramenta_->set_table(PREFIX_TABLES . 'ws_pages');
		$_exc_ferramenta_->set_where('id="' . $_REQUEST['id_folder'] . '"');
		$_exc_ferramenta_->exclui();
		if ($_REQUEST['preservar'] == 'false') {
			@unlink(INCLUDE_PATH.'website/includes/' . $selectFile->fetch_array[0]['file']);
		}
	}
	
	##################################################################################################
	# FUNÇÃO QUE EXCLUI UMA FERRAMENTA  
	##################################################################################################
	function excluiFerramenta($id_ferra = "") {
		if ($id_ferra == "") {
			$id_ferramenta = $_REQUEST['ws_id_ferramenta'];
		} else {
			$id_ferramenta = $id_ferra;
		}
		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];

		##################################################################################################
		# EXCLUIMOS TODAS AS TABELAS DA FERRAMENTA  
		##################################################################################################
		$duplica = new MySQL();
		$duplica->set_table(PREFIX_TABLES.$slug.'_campos');
		$duplica->set_table(PREFIX_TABLES.$slug.'_cat');
		$duplica->set_table(PREFIX_TABLES.$slug.'_files');
		$duplica->set_table(PREFIX_TABLES.$slug.'_gal');
		$duplica->set_table(PREFIX_TABLES.$slug.'_img');
		$duplica->set_table(PREFIX_TABLES.$slug.'_img_gal');
		$duplica->set_table(PREFIX_TABLES.$slug.'_item');
		$duplica->set_table(PREFIX_TABLES.$slug.'_link_op_multiple');
		$duplica->set_table(PREFIX_TABLES.$slug.'_link_prod_cat');
		$duplica->set_table(PREFIX_TABLES.$slug.'_op_multiple');
		$duplica->exclui_table();

		##################################################################################################
		# EXCLUIMOS O ACESSO DE USUARIOS, LINK DOS GRUPOS E A PRÓPRIA FERRAMENTA  
		##################################################################################################
		$_exclui_acessos_ = new MySQL();
		$_exclui_acessos_->set_table(PREFIX_TABLES . 'ws_user_link_ferramenta');
		$_exclui_acessos_->set_where('id_ferramenta="' . $id_ferramenta . '"');
		$_exclui_acessos_->exclui();
		
		$delete_group = new MySQL();
		$delete_group->set_table(PREFIX_TABLES.'ws_link_path_tools');
		$delete_group->set_where('id_tool='.$id_ferramenta);
		$delete_group->exclui();

		$_exc_ferramenta_ = new MySQL();
		$_exc_ferramenta_->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$_exc_ferramenta_->set_where('id="' . $id_ferramenta . '"');
		$_exc_ferramenta_->exclui();

		die("sucesso");

	}
	function _file_($token, $w, $id = '') {
		return '<li class="file_input resize_x" data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="descricao" data-content="Upload de arquivo" >Upload de arquivo:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _input_($token, $w, $label = '', $id = '') {
		return '<li class="inputTextModel resize_x" data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>inputText:</b>' . $label . '"></div>
					<div class="descricao" data-content="Input Text simples" >Input Text simples:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _linkTool_($token, $w, $label = '', $id = '') {
		return '<li class="linkToolModel resize_x" data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="Link Tool"></div>
					<div class="descricao" data-content="Link para outra ferramenta">Link para outra ferramenta:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _colorpicker_($token, $w, $label = '', $id = '') {
		return '<li class="inputColorModel resize_x" data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="descricao" data-content="ColorPicker" >ColorPicker:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _key_works_($token, $label = '', $id = '') {
		return '<li class="key_works" data-token="' . $token . '" data-id="' . $id . '">
					<div class="hand descricao" data-content="Keywork">Keywork:' . $id . '</div>
					<div class="edit" legenda="Editar"></div>
					<div class="close"></div>
				</li>';
	}
	function _separador_($token, $label = '', $id = '') {
		return '<li class="separador" data-token="' . $token . '" data-id="' . $id . '">
					<div class="hand descricao" data-content="Separador">Separador:</div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>Label:</b>' . $label . '"></div>
					<div class="close"></div>
				</li>';
	}
	function _quebra_($token, $id = '') {
		return '<li class="quebra_de_linha " data-token="' . $token . '" data-id="' . $id . '">
					<div class="hand">Quebra de linha:</div>
					<div class="close"></div>
				</li>';
	}
	function _check_($token, $w, $h, $label = '', $id = '') {
		return '<li class="checkboxModel resize_x" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>CheckBox:</b>' . $label . '"></div>
					<div class="descricao" data-content="CheckBox">CheckBox:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _radio_($token, $w, $h, $label = '', $id = '') {
		return '<li class="RadioBoxModel resize_x" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>CheckBox:</b>' . $label . '"></div>
					<div class="descricao" data-content="RadioBox">RadioBox:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _textarea_($token, $w, $h, $label = '', $id = '') {
		return '<li class="resizeTextArea resize_x_y" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="descricao" data-content="Textarea">Textarea:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _vazio_($token, $w, $h, $id = '') {
		return '<li class="EspacoVazio resize_x_y" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="close"></div>
					<div class="descricao hand" data-content="Espaço vazio">Espaço vazio</div>
					<div class="close"></div>
				</li>';
	}
	function _iframe_($token, $w, $h, $id = '') {
		return '<li class="iframeDiv resize_x_y" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="edit" legenda="Editar"></div>
					<div class="descricao hand" data-content="iFrame">iFrame:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _label_($token, $w, $label = '', $id = '') {
		return '<li class="label_text resize_x" data-token="' . $token . '" style="width:' . $w . '" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>Label:</b> ' . $label . '"></div>
					<div class="descricao" data-content="Label simples">Label simples:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _selectbox_($token, $w, $h, $label = '', $id = '') {
		return '<li class="Selectbox resize_x" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>SelectBox:</b>' . $label . '"></div>
					<div class="descricao" data-content="SelectBox">SelectBox:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _multiple_select_($token, $w, $h, $label = '', $id = '') {
		return '<li class="multiple_select resize_x" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="info" legenda="<b>Multi SelectBox:</b>' . $label . '"></div>
					<div class="descricao" data-content="Multi SelectBox">Multi SelectBox:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _avatar_($token, $w, $h, $id = '') {
		return '<li class="resize_x_y avatar_model " data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="descricao" data-content="Avatar">Avatar:' . $id . '</div>
					<div class="close"></div>
				</li>';
	}
	function _thumbmail_($token, $w, $h, $id = '') {
		return '<li class="resize_x_y imagem_model " data-token="' . $token . '" style="width:' . $w . 'px;height:' . ($h - 1) . 'px!important" data-id="' . $id . '">
						<div class="hand" legenda="Mover ítem"></div>
						<div class="edit" legenda="Editar"></div>
						<div class="info" legenda="<b>textarea:</b>"></div>
						<div class="descricao" data-content="Imagem">Imagem:' . $id . '</div>
						<div class="close"></div>
				</li>';
	}
	function _playerMP3_($token, $w, $h, $id = '') {
		return '<li class="mp3Player playerMP3_model " data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
						<div class="hand" legenda="Mover ítem"></div>
						<div class="edit" legenda="Editar"></div>
						<div class="descricao" data-content="Player MP3">Player MP3:' . $id . '</div>
						<div class="close"></div>
				</li>';
	}
	function _playerVideo_($token, $w, $h, $id = '') {
		return '<li class="aspectRatio video_model hand" data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
						<div class="hand" legenda="Mover ítem"></div>
						<div class="edit" legenda="Editar"></div>
						<div class="descricao" data-content="Vídeo">Vídeo:' . $id . '</div>
						<div class="close"></div>
				</li>';
	}
	function _editor_($token, $w, $h, $id = '') {
		return '
				<li class="resize_x_y editor_model " data-token="' . $token . '" style="width:' . $w . 'px;height:' . $h . 'px!important" data-id="' . $id . '">
						<div class="hand" legenda="Mover ítem"></div>
						<div class="edit" legenda="Editar"></div>
						<div class="descricao" data-content="Editor de código">Editor de código:' . $id . '</div>
						<div class="close"></div>
				</li>';
	}
	function _fotos_($token, $w, $label = "", $id = '') {
		return '
				<li class="resize_x botao_model " data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					' . $label . '
				</li>';
	}
	function _galerias_($token, $w, $id = '') {
		return '<li class="resize_x botao_model " data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					Galerias de fotos
				</li>';
	}
	function _files_($token, $w, $id = '') {
		return '<li class="resize_x botao_model " data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					Listagem de arquivos
				</li>';
	}
	function _ferramenta_interna_($token, $w, $label = "Feramenta interna", $id = '') {
		/*  return '<li class="iframeDiv resize_y" data-token="'.$token.'" style="width:992px;height:'.$h.'px!important" data-id="'.$id.'">
		<div class="edit" legenda="Editar"></div>
		<div class="descricao hand">'.$label.'</div>
		<div class="close"></div>
		</li>';*/
		return '
				<li class="resize_x botao_model " data-token="' . $token . '" style="width:' . $w . 'px" data-id="' . $id . '">
					<div class="hand" legenda="Mover ítem"></div>
					<div class="edit" legenda="Editar"></div>
					<div class="close"></div>
					' . $label . '
				</li>';
		/**/
	}
	
	//###############################################################################################LISTA TODOS OS CAMPOS
	function listaCampos() {

		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];



		$campos        = new MySQL();
		$campos->set_table(PREFIX_TABLES . $slug . '_campos');
		$campos->set_order('posicao', 'ASC');
		$campos->set_where('ws_id_ferramenta="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$campos->select();
		
		
		foreach ($campos->fetch_array as $camp) {
			$token = $camp['token'];
			$item  = "";
			if ($camp['type'] == 'quebra') {
				$item = _quebra_($token, 'Quebra de linha');
			}
			if ($camp['type'] == 'separador') {
				$item = _separador_($token, $camp["label"], 'Separador');
			}
			if ($camp['type'] == 'key_works') {
				$item = _key_works_($token, $camp["label"], $camp['id_campo']);
			}
			if ($camp['type'] == 'vazio') {
				$item = _vazio_($token, $camp['largura'], $camp['altura'], 'Espaço vazio');
			}
			if ($camp['type'] == 'iframe') {
				$item = _iframe_($token, $camp['largura'], $camp['altura'], $camp['id_campo']);
			}
			if ($camp['type'] == 'editor') {
				$item = _editor_($token, $camp['largura'], $camp['altura'], $camp['id_campo']);
			}
			if ($camp['type'] == 'linkTool') {
				$item = _linkTool_($token, $camp['largura'], $camp['place'], $camp['id_campo']);
			}
			if ($camp['type'] == 'input') {
				$item = _input_($token, $camp['largura'], $camp['place'], $camp['id_campo']);
			}
			if ($camp['type'] == 'colorpicker') {
				$item = _colorpicker_($token, $camp['largura'], $camp['place'], $camp['id_campo']);
			}
			if ($camp['type'] == 'textarea') {
				$item = _textarea_($token, $camp['largura'], $camp['altura'], $camp['place'], $camp['id_campo']);
			}
			if ($camp['type'] == 'check') {
				$item = _check_($token, $camp['largura'], $camp['altura'], $camp['label'], $camp['id_campo']);
			}
			if ($camp['type'] == 'radiobox') {
				$item = _radio_($token, $camp['largura'], $camp['altura'], $camp['label'], $camp['id_campo']);
			}
			if ($camp['type'] == 'label') {
				$item = _label_($token, $camp['largura'], $camp['label'], $camp['label']);
			}
			if ($camp['type'] == 'selectbox') {
				$item = _selectbox_($token, $camp['largura'], $camp['altura'], '', $camp['id_campo']);
			}
			if ($camp['type'] == 'multiple_select') {
				$item = _multiple_select_($token, $camp['largura'], $camp['altura'], '', $camp['id_campo']);
			}
			if ($camp['type'] == 'file') {
				$item = _file_($token, $camp['largura'], $camp['id_campo']);
			}
			if ($camp['type'] == 'thumbmail') {
				$item = _thumbmail_($token, $camp['largura'], $camp['altura'], $camp['id_campo'], $camp['id_campo']);
			}
			if ($camp['type'] == 'playerMP3') {
				$item = _playerMP3_($token, $camp['largura'], $camp['altura'], $camp['label'], $camp['id_campo']);
			}
			if ($camp['type'] == 'playerVideo') {
				$item = _playerVideo_($token, $camp['largura'], $camp['altura'], $camp['label'], $camp['id_campo']);
			}
			if ($camp['type'] == '_ferramenta_interna_') {
				$item = _ferramenta_interna_($token, $camp['largura'], $camp['label'], $camp['id_campo']);
			}
			if ($camp['type'] == 'bt_fotos') {
				$item = _fotos_($token, $camp['largura'], $camp['label'], '');
			}
			if ($camp['type'] == 'bt_galerias') {
				$item = _galerias_($token, $camp['largura'], '');
			}
			if ($camp['type'] == 'bt_files') {
				$item = _files_($token, $camp['largura'], '');
			}
			echo $item;
		}
	}
	//############################################################################################### REDIMENCIONA CAMPO
	function resizeCampos() {

		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$t_ferramentas->set_where('id="' . $_POST['ws_id_ferramenta'] . '"');
		$t_ferramentas->select();
		$slug = $t_ferramentas->fetch_array[0]['slug'];



		$U = new MySQL();
		$U->set_table(PREFIX_TABLES .$slug. '_campos');
		$U->set_where('token="' . $_REQUEST['token'] . '"');
		$U->set_update('largura', $_REQUEST['largura']);
		$U->set_update('altura', $_REQUEST['altura']);
		if ($U->salvar()) {
			echo "sucesso";
			exit;
		} else {
			echo "falha";
			exit;
		}
	}
	//############################################################################################### REPOSICIONA FERRAMENTA
	function ordenaFerramenta() {
		$i = 1;
		foreach ($_REQUEST['posicoes'] as $token) {
			if ($token) {
				$U = new MySQL();
				$U->set_table(PREFIX_TABLES . 'ws_ferramentas');
				$U->set_where('token="' . $token . '"');
				$U->set_update('posicao', $i);
				$U->salvar();
				$U = new MySQL();
				$U->set_table(PREFIX_TABLES . 'ws_pages');
				$U->set_where('token="' . $token . '"');
				$U->set_update('posicao', $i);
				$U->salvar();     
				$i++;
			}
		}
		echo 'sucesso';
		exit;
	} 
	//############################################################################################### RENOMEIA PATH DAS FERRAMENTAS
	function saveLabelPathTool() {
		$i = 1;
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . 'ws_path_tools');
		$U->set_where('id="' . $_POST['id'] . '"');
		$U->set_update('path_name', $_POST['path_name'] );
		$U->salvar();
		echo 'sucesso';
		exit;
	}
	//############################################################################################### EXCLUI PATH DAS FERRAMENTAS
	function excluiPathTool() {
		$i = 1;
		$delete_group= new MySQL();
		$delete_group->set_table(PREFIX_TABLES . 'ws_path_tools');
		$delete_group->set_where('id="' . $_POST['id'] . '"');
		$delete_group->exclui();

		$delete_link = new MySQL();
		$delete_link->set_table(PREFIX_TABLES.'ws_link_path_tools');
		$delete_link->set_where('id_path='.$_POST['id']);
		$delete_link->exclui();



		echo 'sucesso';
		exit;
	}
	//############################################################################################### EXCLUI PATH DAS FERRAMENTAS
	function addPathTool() {
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . 'ws_path_tools');
		$U->set_insert('path_name', '');
		$U->insert();
		echo 'sucesso';
		exit;
	}
	//############################################################################################### REPOSICIONA PATHS DA FERRAMENTA
	function ordenaPathsFerramenta() {
		$i = 1;
		foreach ($_REQUEST['posicoes'] as $id) {
			if ($id) {
				$U = new MySQL();
				$U->set_table(PREFIX_TABLES . 'ws_path_tools');
				$U->set_where('id="' . $id . '"');
				$U->set_update('posicao', $i);
				$U->salvar();
				$i++;
			}
		}
		echo 'sucesso';
		exit;
	}
	//############################################################################################### REPOSICIONA CAMPO
	function ordenaCampos() {

		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$t_ferramentas->set_where('id="' . $_POST['ws_id_ferramenta'] . '"');
		$t_ferramentas->select();
		$slug = $t_ferramentas->fetch_array[0]['slug'];

		$tabela_campos = PREFIX_TABLES .$slug. '_campos';
		
		$i = 1;
		foreach ($_REQUEST['posicoes'] as $token) {
			if ($token) {
				$U = new MySQL();
				$U->set_table($tabela_campos);
				$U->set_where('token="' . $token . '"');
				$U->set_update('posicao', $i);
				$U->salvar();
				$i++;
			}
		}
		echo 'sucesso';
		exit;
	}
	//##################################################################    HABILITA LISTAGEM DE ARQUIVOS
	function habilita_files_interna() {

		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];


		$token         = _token(PREFIX_TABLES . $slug. '_campos', 'token');
		//========================================================= verifica se ja existe o botão de fotos
		$t_campos      = new MySQL();
		$t_campos->set_table(PREFIX_TABLES .$slug. '_campos');
		$t_campos->set_where('type="bt_files"');
		$t_campos->set_where('AND ws_id_ferramenta="' . $id_ferramenta . '"');
		$t_campos->select();
		$tabela_campos = PREFIX_TABLES .$slug . "_campos";
		//========================================================= caso nao tenha ainda
		if ($t_campos->_num_rows == 0) {
			//============================== habilita com valor 1
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_files_', '1');
			$t_ferramentas->salvar();
			//============= Adiciona o botão na tabela de campos
			$t_campos = new MySQL();
			$t_campos->set_table(PREFIX_TABLES .$slug. '_campos');
			$t_campos->set_insert('type', 'bt_files');
			$t_campos->set_insert('token', $token);
			$t_campos->set_insert('largura', '200');
			$t_campos->set_insert('label', 'Listagem de arquivos');
			$t_campos->set_insert('ws_id_ferramenta', $id_ferramenta);
			$t_campos->insert();
			echo json_encode(array(
				'resposta' => 'sucesso',
				'item' => "$('#ul_resize').prepend('" . _files_($token, '200', 'Listagem de arquivos') . "');",
				'habdesab' => '$(botao).addClass("disabled").attr("legenda","Desabilitar arquivos internos").LegendaOver();'
			));
		} else {
			//============================== habilita com valor 0
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_files_', '0');
			$t_ferramentas->salvar();
			//============= Pega o valor do token atual do bt existente
			$t_campos_a = new MySQL();
			$t_campos_a->set_table($tabela_campos);
			$t_campos_a->set_where('ws_id_ferramenta="' . $id_ferramenta . '"');
			$t_campos_a->set_where('AND type="bt_files"');
			$t_campos_a->select();
			//============= Adiciona o botão na tabela de campos
			$t_campos = new MySQL();
			$t_campos->set_table($tabela_campos);
			$t_campos->set_where('type="bt_files"');
			$t_campos->set_where('AND ws_id_ferramenta="' . $id_ferramenta . '"');
			$t_campos->exclui();
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_alterado_', '1');
			$t_ferramentas->salvar();
			
			echo json_encode(array(
				'resposta' => 'sucesso',
				'item' => "$('#ul_resize li[data-token=" . $t_campos_a->fetch_array[0]['token'] . "]').remove();",
				'habdesab' => '$(botao).removeClass("disabled").attr("legenda","Habilitar arquivos internos").LegendaOver();'
			));
			
		}
	}
	//##################################################################    HABILITA GAL INTERNA
	function habilita_gal_interna() {
		
		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$id_ferramenta = $_REQUEST['ws_id_ferramenta'];
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $id_ferramenta . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];

		//======================================================== verifica a ferramenta e separa a tabela campos
		$tabela_campos = PREFIX_TABLES .$slug. '_campos';
		$token    = _token($tabela_campos, 'token');
		//========================================================= verifica se ja existe o botão de fotos
		$t_campos = new MySQL();
		$t_campos->set_table($tabela_campos);
		$t_campos->set_where('type="bt_galerias"');
		$t_campos->set_where('AND ws_id_ferramenta="' . $id_ferramenta . '"');
		$t_campos->select();
		
		//========================================================= caso nao tenha ainda
		if ($t_campos->_num_rows == 0) {
			//============================== habilita com valor 1
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_galerias_', '1');
			$t_ferramentas->salvar();
			//============= Adiciona o botão na tabela de campos
			$t_campos = new MySQL();
			$t_campos->set_table($tabela_campos);
			$t_campos->set_insert('type', 'bt_galerias');
			$t_campos->set_insert('token', $token);
			$t_campos->set_insert('largura', '200');
			$t_campos->set_insert('label', 'Adicionar Galerias');
			$t_campos->set_insert('ws_id_ferramenta', $id_ferramenta);
			$t_campos->insert();
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_alterado_', '1');
			$t_ferramentas->salvar();
			
			echo json_encode(array(
				'resposta' => 'sucesso',
				'item' => "$('#ul_resize').prepend('" . _galerias_($token, '200', 'Adicionar galerias') . "');",
				'habdesab' => '$(botao).addClass("disabled").attr("legenda","Desabilitar imagens internas").LegendaOver();'
			));
		} else {

			//============================== habilita com valor 0
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_galerias_', '0');
			$t_ferramentas->salvar();

			//============= Pega o valor do token atual do bt existente
			$t_campos_a = new MySQL();
			$t_campos_a->set_table($tabela_campos);
			$t_campos_a->set_where('type="bt_galerias"');
			$t_campos_a->set_where('AND ws_id_ferramenta="' . $id_ferramenta . '"');
			$t_campos_a->select();

			//============= Adiciona o botão na tabela de campos
			$t_campos = new MySQL();
			$t_campos->set_table($tabela_campos);
			$t_campos->set_where('type="bt_galerias"');
			$t_campos->exclui();
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_alterado_', '1');
			$t_ferramentas->salvar();
			echo json_encode(array(
				'resposta' => 'sucesso',
				'item' => "$('#ul_resize li[data-token=" . $t_campos_a->fetch_array[0]['token'] . "]').remove();",
				'habdesab' => '$(botao).removeClass("disabled").attr("legenda","Habilitar imagens internas").LegendaOver();'
			));
		}
	}
	
	//##################################################################    ADICIONA CAMPO
	function habilita_img_interna() {
		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$id_ferramenta = $_REQUEST['ws_id_ferramenta'];

		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $id_ferramenta . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];
		
		$tabela_campos = PREFIX_TABLES .$slug. '_campos';
		$token         = _token($tabela_campos, 'token');
		//========================================================= verifica se ja existe o botão de fotos
		$t_campos      = new MySQL();
		$t_campos->set_table($tabela_campos);
		$t_campos->set_where('ws_id_ferramenta="' . $id_ferramenta . '"');
		$t_campos->set_where('AND type="bt_fotos"');
		$t_campos->select();
		//========================================================= caso nao tenha ainda
		if ($t_campos->_num_rows == 0) {
			//============================== habilita com valor 1
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_fotos_', '1');
			$t_ferramentas->salvar();
			//============= Adiciona o botão na tabela de campos
			$t_campos = new MySQL();
			$t_campos->set_table($tabela_campos);
			$t_campos->set_insert('type', 'bt_fotos');
			$t_campos->set_insert('token', $token);
			$t_campos->set_insert('largura', '200');
			$t_campos->set_insert('label', 'Adicionar imagens');
			$t_campos->set_insert('ws_id_ferramenta', $id_ferramenta);
			if ($t_campos->insert()) {
				$t_ferramentas = new MySQL();
				$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
				$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
				$t_ferramentas->set_update('_alterado_', '1');
				$t_ferramentas->salvar();
				
				echo json_encode(array(
					'resposta' => 'sucesso',
					'item' => "$('#ul_resize').prepend('" . _fotos_($token, '200', 'Adicionar imagens') . "');",
					'habdesab' => '$(botao).addClass("disabled").attr("legenda","Desabilitar imagens internas").LegendaOver();'
				));
			}
		} else {
			//============================== habilita com valor 0
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_fotos_', '0');
			$t_ferramentas->salvar();
			//============= Pega o valor do token atual do bt existente
			$t_campos_a = new MySQL();
			$t_campos_a->set_table($tabela_campos);
			$t_campos_a->set_where('type="bt_fotos"');
			$t_campos_a->set_where('AND ws_id_ferramenta="' . $id_ferramenta . '"');
			$t_campos_a->select();
			//============= Adiciona o botão na tabela de campos
			$t_campos = new MySQL();
			$t_campos->set_table($tabela_campos);
			$t_campos->set_where('type="bt_fotos"');
			$t_campos->set_where('AND ws_id_ferramenta="' . $id_ferramenta . '"');
			if ($t_campos->exclui()) {
				$t_ferramentas = new MySQL();
				$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
				$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
				$t_ferramentas->set_update('_alterado_', '1');
				$t_ferramentas->salvar();
				echo json_encode(array(
					'resposta' => 'sucesso',
					'item' => "$('#ul_resize li[data-token=" . @$t_campos_a->fetch_array[0]['token'] . "]').remove();",
					'habdesab' => '$(botao).removeClass("disabled").attr("legenda","Habilitar imagens internas").LegendaOver();'
				));
			}
		}
	}

	//###############################################################################################	ADICIONA CAMPO
	
	function addCampo() {
		if ($_REQUEST['type'] == "selecione"){exit;}

		$type          = $_REQUEST['type'];
		$id_ferramenta = $_REQUEST['ws_id_ferramenta'];
		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$slug 			= $getSlug->fetch_array[0]['slug'];
		$token			= _token(PREFIX_TABLES .$slug. '_campos', 'token');
		$Dados			= new MySQL();
		$Dados->set_table(PREFIX_TABLES .$slug. '_campos');
		$Dados->set_insert('ws_id_ferramenta', $id_ferramenta);
		$Dados->set_insert('type', $type);
		$Dados->set_insert('token', $token);
		$Dados->set_insert('largura', '83');
		if ($Dados->insert()) {
			sleep(0.5);
			$t_campo_added = new MySQL();
			$t_campo_added->set_table(PREFIX_TABLES .$slug. '_campos');
			$t_campo_added->set_where('token', '"' . $token . '"');
			$t_campo_added->select();
			
			$camp = @$t_campo_added->fetch_array[0];
			if ($type == 'quebra') {
				$item = _quebra_($token, $camp['id_campo']);
			}
			if ($type == 'separador') {
				$item = _separador_($token, $camp['id_campo']);
			}
			if ($type == 'key_works') {
				$item = _key_works_($token, $camp['id_campo']);
			}
			if ($type == 'vazio') {
				$item = _vazio_($token, '75', '200');
			}
			if ($type == 'iframe') {
				$item = _iframe_($token, '75', '200', $camp['id_campo']);
			}
			if ($type == 'textarea') {
				$item = _textarea_($token, '75', '200', $camp['id_campo']);
			}
			if ($type == 'linkTool') {
				$item = _linkTool_($token, '75', '', $camp['id_campo']);
			}
			if ($type == 'input') {
				$item = _input_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'colorpicker') {
				$item = _colorpicker_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'check') {
				$item = _check_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'radiobox') {
				$item = _radio_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'selectbox') {
				$item = _selectbox_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'multiple_select') {
				$item = _multiple_select_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'avatar') {
				$item = _avatar_($token, '75', '40', $camp['id_campo']);
			}
			if ($type == 'file') {
				$item = _file_($token, '75', $camp['id_campo']);
			}
			if ($type == 'label') {
				$item = _label_($token, '75', $camp['id_campo']);
			}
			if ($type == 'thumbmail') {
				$item = _thumbmail_($token, '75', '200', 'thumbmail', $camp['id_campo']);
			}
			if ($type == 'playerMP3') {
				$item = _playerMP3_($token, '995', '137', 'thumbmail', $camp['id_campo']);
			}
			if ($type == 'playerVideo') {
				$item = _playerVideo_($token, '325', '180', 'Player', $camp['id_campo']);
			}
			if ($type == '_ferramenta_interna_') {
				$item = _ferramenta_interna_($token, '75', $camp['id_campo']);
			}
			if ($type == 'editor') {
				$item = _editor_($token, '200', '200', $camp['id_campo']);
			}
			echo json_encode(array(
				'resposta' => 'sucesso',
				'item' => $item,
				'token' => $token
			));
		}
	}
	function returnCampos() {
		$tool     = $_REQUEST['tool'];
		$type     = $_REQUEST['type'];
		$selected = $_REQUEST['selected'];

		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $tool . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];



		if ($type == "item") {
			$camposItem = new MySQL();
			$camposItem->set_table(PREFIX_TABLES .$slug. '_campos');
			$camposItem->set_where('ws_id_ferramenta="' . $tool . '"');
			$camposItem->select();
			foreach ($camposItem->fetch_array as $item) {
				$select = "";
				if ($selected == $item['name']) {
					$select = "selected";
				}
				echo "<option value='" . $item['name'] . "' " . $select . ">" . $item['name'] . "</option>" . PHP_EOL;
			}
			exit;
		}
		if ($type == "cat") {
			$select = "";
			if ($item['name'] == 'titulo') {
				$select = "selected";
			}
			echo "<option value='titulo' " . $select . ">Título</option>" . PHP_EOL;
			if ($item['name'] == 'texto') {
				$select = "selected";
			}
			echo "<option value='texto' " . $select . ">Texto</option>" . PHP_EOL;
			if ($item['name'] == 'url') {
				$select = "selected";
			}
			echo "<option value='url' " . $select . ">URL</option>" . PHP_EOL;
		}
	}
	function return_campos_cat_referencia() {
		$listCat        = array();
		$categorias     = new MySQL();
		$tool           = $_REQUEST['tool'];
		$cat_referencia = $_REQUEST['cat_referencia'];

		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $tool . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];



		function foreachCat($cat, $listCat, $cat_referencia, $tool) {
			$cat_foreach = new MySQL();
			$cat_foreach->set_table(PREFIX_TABLES .$slug. '_cat');
			$cat_foreach->set_order('titulo ASC');
			$cat_foreach->set_where('id_cat="' . $cat . '"');
			$cat_foreach->set_where('AND ws_id_ferramenta="' . $tool . '"');
			$cat_foreach->select();
			foreach ($cat_foreach->fetch_array as $item) {
				$select = "";
				if ($cat_referencia == $item['id']) {
					$select = "selected";
				}
				$listCat[] = $item['titulo'];
				echo "<option value='" . $item['id'] . "' " . $select . ">" . implode($listCat, ' > ') . "</option>" . PHP_EOL;
				foreachCat($item['id'], $listCat, $cat_referencia, $tool);
				$listCat = array();
			}
		}
		$categorias->set_table(PREFIX_TABLES .$slug. '_cat');
		$categorias->set_order('titulo ASC');
		$categorias->set_where('id_cat="0"');
		$categorias->set_where('AND ws_id_ferramenta="' . $tool . '"');
		$categorias->select();
		echo "<option value=''>Não vincular a nenhuma categoria</option>" . PHP_EOL;
		foreach ($categorias->fetch_array as $item) {
			if ($cat_referencia == $item['id']) {
				$select = "selected";
			} else {
				$select = "";
			}
			echo "<option value='" . $item['id'] . "' " . $select . ">" . $item['titulo'] . "</option>" . PHP_EOL;
			$listCat[] = $item['titulo'];
			foreachCat($item['id'], $listCat, $cat_referencia, $tool);
			$listCat = array();
		}
	}

	####################################################################################
	# FUNÇÃO QUE SALVA E ADICIONA O CAMPO NAS COLUNAS DA TABELA DA FERRAMENTA 
	####################################################################################

	function salvaCampo() {
		$inputs = array();
		parse_str($_REQUEST['inputs'], $inputs);

		####################################################################################
		# CAPTAMOS DADOS DA FERRAMENTA 
		####################################################################################
		$FERRAMENTA = new MySQL();
		$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$FERRAMENTA->set_where('id="' . $inputs['ws_id_ferramenta'] . '"');
		$FERRAMENTA->select();

		$slug     	= $FERRAMENTA->fetch_array[0]['slug'];
		$_prefix_   = $FERRAMENTA->fetch_array[0]['_prefix_'];

		####################################################################################
		# SEPARA AS COLUNAS DA TABELA ITEM EM UM ARRAY 
		####################################################################################
		$colunas 			= Array();
		$COLUNAS_TOOL       = new MySQL();
		$COLUNAS_TOOL->set_table(PREFIX_TABLES .$slug. '_item');
		$COLUNAS_TOOL->show_columns();
		foreach ($COLUNAS_TOOL->fetch_array as $coluna) {$colunas[] = $coluna['Field'];}
		
		####################################################################################
		# SELECIONAMOS O CAMPO PELO SEU TOKEN (pois ele nao existe como id ainda)
		####################################################################################
		$VerificaToken = new MySQL();
		$VerificaToken->set_table(PREFIX_TABLES .$slug. '_campos');
		$VerificaToken->set_where('token="' . $inputs['token'] . '"');
		$VerificaToken->select();

		####################################################################################
		# CASO O TYPE DO CAMPO NÃO SEJA NENHUM DESSES, ELE O SETA COMO NOA COLUNA
		####################################################################################    
		if (!in_array($inputs['type'], array('bt_fotos', 'bt_galerias', 'bt_files', 'vazio', 'quebra', 'separador', 'label'))) {
			$inputs['identificador'] = $_prefix_ . $inputs['identificador'];
		} else {
			$inputs['identificador'] = "";
		}

		####################################################################################
		# INICIA O PROCESSO DE "SALVAMENTO"
		####################################################################################
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES .$slug. '_campos');
		$Salva->set_where('token="' . $inputs['token'] . '"');
		
		####################################################################################
		# TRATAMOS OS CAMPOS QUE ESTÃO VAZIOS NO FORMULARIO E NAO APARECEM NO POST
		####################################################################################
		$inputs['desabilitado'] = (@$inputs['desabilitado'] == "on")  ? '1' : "0";
		$inputs['numerico']  	= (@$inputs['numerico']   	== "on")  ? '1' : "0";
		$inputs['financeiro']   = (@$inputs['financeiro']   == "on")  ? '1' : "0";
		$inputs['editor']     	= (@$inputs['editor']     	== "on")  ? '1' : "0";
		$inputs['autosize']   	= (@$inputs['autosize']   	== "on")  ? '1' : "0";
		$inputs['upload']     	= (@$inputs['upload']     	== "on")  ? '1' : "0";
		$inputs['calendario']   = (@$inputs['calendario']   == "on")  ? '1' : "0";
		$inputs['password']   	= (@$inputs['password']   	== "on")  ? '1' : "0";
		$inputs['download']   	= (@$inputs['download']   	== "on")  ? '1' : "0";
		$inputs['labelTop']   	= (@$inputs['labelTop']   	== "on")  ? '1' : "0";
		$inputs['multiple']   	= (@$inputs['multiple']   	== "on")  ? '1' : "0";
		$inputs['caracteres']   = (@$inputs['caracteres']   ==  "" )  ? '150' : $inputs['caracteres'];


		$Salva->set_update('id_campo',      ''.@$inputs['identificador']);
		$Salva->set_update('name',        ''.@$inputs['identificador']);
		$Salva->set_update('coluna_mysql',    ''.@$inputs['identificador']);
		$Salva->set_update('place',       ''.@$inputs['place']);
		$Salva->set_update('caracteres',    ''.@$inputs['caracteres']);
		$Salva->set_update('legenda',       ''.@$inputs['legenda']);
		$Salva->set_update('filtro',      ''.@$inputs['filtro']);
		$Salva->set_update('disabled',      ''.@$inputs['desabilitado']);
		$Salva->set_update('numerico',      ''.@$inputs['numerico']);
		$Salva->set_update('financeiro',    ''.@$inputs['financeiro']);
		$Salva->set_update('editor',      ''.@$inputs['editor']);
		$Salva->set_update('label',       ''.@$inputs['label']);
		$Salva->set_update('background',    ''.@$inputs['background']);
		$Salva->set_update('color',       ''.@$inputs['color']);
		$Salva->set_update('values_opt',    ''.@$inputs['values_opt']);
		$Salva->set_update('sintaxy',       ''.@$inputs['sintaxy']);
		$Salva->set_update('autosize',      ''.@$inputs['autosize']);
		$Salva->set_update('upload',      ''.@$inputs['upload']);
		$Salva->set_update('referencia',    ''.@$inputs['referencia']);
		$Salva->set_update('cat_referencia',  ''.@$inputs['cat_referencia']);
		$Salva->set_update('rua',         ''.@$inputs['rua']);
		$Salva->set_update('cidade',      ''.@$inputs['cidade']);
		$Salva->set_update('uf',        ''.@$inputs['uf']);
		$Salva->set_update('pais',        ''.@$inputs['pais']);
		$Salva->set_update('cep',         ''.@$inputs['cep']);
		$Salva->set_update('bairro',      ''.@$inputs['bairro']);
		$Salva->set_update('calendario',    ''.@$inputs['calendario']);
		$Salva->set_update('password',      ''.@$inputs['password']);
		$Salva->set_update('listaTabela',     ''.@$inputs['listaTabela']);
		$Salva->set_update('labelSup',      ''.@$inputs['labelSup']);
		$Salva->set_update('download',      ''.@$inputs['download']);
		$Salva->set_update('multiple',      ''.@$inputs['multiple']);
		$Salva->set_update('labelTop',      ''.@$inputs['labelTop']);
		

		####################################################################################
		# AGORA VERIFICAMOS SE ESSA COLUNA MYSQL JÁ EXISTE NA TABELA
		####################################################################################
		$VerificaMySQL = new MySQL();
		$VerificaMySQL->set_table(PREFIX_TABLES .$slug. '_campos');
		$VerificaMySQL->set_where('coluna_mysql="' . $inputs['identificador'] . '"');
		$VerificaMySQL->select();
		

		if (
			in_array($inputs['identificador'], $colunas) && 
			$VerificaToken->fetch_array[0]['coluna_mysql'] == $inputs['identificador'] &&
			$VerificaMySQL->_num_rows > 0 &&
			$VerificaToken->fetch_array[0]['caracteres'] != $inputs['caracteres']
		){
			####################################################################################################################
			# CASO ESSA COLUNA JÁ EXISTA, VERIFICA SE TEM ALTERAÇÕES NO VARCHAR
			####################################################################################################################
			if($inputs['type'] == 'input' && $VerificaToken->fetch_array[0]['caracteres'] != $inputs['caracteres'] && $inputs['identificador']!="separador"){
				$addColumn = new MySQL();
				$addColumn->select('ALTER TABLE '.PREFIX_TABLES. $slug.'_item MODIFY '.@$inputs['identificador'].' varchar('.$inputs['caracteres'].');'); 
			}
			####################################################################################################################

		} elseif ( in_array($inputs['identificador'], $colunas) && $inputs['type']!="radiobox" && $VerificaToken->fetch_array[0]['coluna_mysql'] == "") {

			// echo json_encode(array(
			// 	'status'  => false,
			// 	'response'  => 'Ops! A coluna '.str_replace($_prefix_, "", $inputs['identificador']) . ' já existe nessa ferramenta',
			// 	'id_campo'  => null,
			// 	'token'   => null
			// ));
			// exit;
			//
		} 
		elseif (in_array($inputs['identificador'], $colunas) &&  $VerificaMySQL->_num_rows > 0 && $inputs['type']!="radiobox") {
			// echo json_encode(array(
			// 	'status'  => false,
			// 	'response'  => 'Ops! A coluna ' . $inputs['identificador'] . ' já existe nessa ferramenta',
			// 	'id_campo'  => null,
			// 	'token'   => null
			// ));
			// exit;
			
		} elseif (!in_array($inputs['identificador'], $colunas)) {

			if ($inputs['type'] == 'check') {
				$typeColum =  ' BOOLEAN NULL DEFAULT FALSE';
			} elseif ($inputs['type'] == 'textarea' || $inputs['type'] == 'editor') {
				$typeColum =  ' TEXT NULL';
			} else {
				$typeColum = ' varchar(' . @$inputs['caracteres'] . ') NULL DEFAULT NULL ';
			}
			if(strlen($inputs['identificador'])){
				$addColumn = new MySQL();
				$addColumn->select('ALTER TABLE '.PREFIX_TABLES. $slug.'_item ADD '.@$inputs['identificador'].' '.$typeColum.';');  
			}
		}
		if ($Salva->salvar()) {
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $inputs['ws_id_ferramenta'] . '"');
			$t_ferramentas->set_update('_alterado_', '1');
			$t_ferramentas->salvar();
			echo json_encode(array(
				'status' => true,
				'response' => 'Coluna ' .str_replace($_prefix_, "", $inputs['identificador'])  . ' salva com sucesso!',
				'id_campo' => $VerificaToken->fetch_array[0]['id_campo'],
				'token' => $inputs['token']
			));
			exit;
		} else {
			echo json_encode(array(
				'status' => false,
				'response' => 'Ops! Houve um erro desconhecido ao salvar a coluna ' . str_replace($_prefix_, "", $inputs['identificador']) ,
				'id_campo' => null,
				'token' => null
			));
			exit;
		}
		exit;
	}


	function excluicampo() {
		global $_conectMySQLi_;
		######################################################################################################
		$id_ferramenta = $_REQUEST['ws_id_ferramenta'];
		##################################################################################################
		# CAPTAMOS O SLUG DA FERRAMENTA 
		##################################################################################################
		$getSlug = new MySQL();
		$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
		$getSlug->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$getSlug->select();
		$slug = $getSlug->fetch_array[0]['slug'];

		
		$D = new MySQL();
		$D->set_table(PREFIX_TABLES .$slug. '_campos');
		$D->set_where('token="' . $_REQUEST['token'] . '"');
		$D->select();
		$Campo_Item = $D->fetch_array[0]['coluna_mysql'];
		
		$VerifyColunas = new MySQL();
		$VerifyColunas->set_table(PREFIX_TABLES .$slug. "_campos");
		$VerifyColunas->set_where('coluna_mysql="' . $Campo_Item . '"');
		$VerifyColunas->select();
		
		######################################################################################################
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES .$slug. '_campos');
		$geral->set_where('type="multiple_select"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$Opcoes = new MySQL();
			$Opcoes->set_table(PREFIX_TABLES . $slug.'_op_multiple');
			$Opcoes->set_where('id_campo="' . $D->fetch_array[0]['id_campo'] . '"');
			$Opcoes->debug(0);
			$Opcoes->exclui();
			$Opcoes = new MySQL();
			$Opcoes->set_table(PREFIX_TABLES . $slug.'_link_op_multiple');
			$Opcoes->set_where('id_campo="' . $D->fetch_array[0]['id_campo'] . '"');
			$Opcoes->debug(0);
			$Opcoes->exclui();
		}
		
		if ($VerifyColunas->_num_rows == 1) {
			$query = @mysqli_query($_conectMySQLi_, "ALTER TABLE " . PREFIX_TABLES . $slug."_item DROP " . $Campo_Item);
		}
		$D = new MySQL();
		$D->set_table(PREFIX_TABLES .$slug. "_campos");
		$D->set_where('token="' . $_REQUEST['token'] . '"');
		if ($D->exclui()) {
			echo "sucesso";
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $id_ferramenta . '"');
			$t_ferramentas->set_update('_alterado_', '1');
			$t_ferramentas->salvar();
			exit;
		}
	}
	
	/**
	
	END INPUTS
	
	*/
	
	function curl_info($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$content = curl_exec($ch);
		$info    = curl_getinfo($ch);
		return $info;
	}
	function situationpanel() {
		$session= new session();
		echo @$session->get("lisence_situacao");
		exit;
	}
	function returnrefer() {
		print_r($_SERVER);
		exit;
	}
	
	function configPaths($RewriteRule = array()) {
		foreach ($RewriteRule as $value) {
			$VerifyPathWsImg = new MySQL();
			$VerifyPathWsImg->set_table(PREFIX_TABLES . 'ws_pages');
			$VerifyPathWsImg->set_where('alias="' . $value['alias'] . '"');
			$VerifyPathWsImg->select();
			if ($VerifyPathWsImg->_num_rows == 0) {
				$I = new MySQL();
				$I->set_table(PREFIX_TABLES . 'ws_pages');
				$I->set_insert('title', $value['title']);
				$I->set_insert('type', $value['type']);
				$I->set_insert('path', $value['urlAmigavel']);
				$I->set_insert('file', $value['filePath']);
				$I->set_insert('alias', $value['alias']);
				$I->set_insert('token', _token(PREFIX_TABLES . 'ws_pages', 'token'));
				$I->insert();
			}
		}
	}
	/**
	##########################################################################################################################################################################################
	################################################################################### SETUP ################################################################################################
	##########################################################################################################################################################################################
	**/
	function installSQLInit() {
		
		global $_conectMySQLi_;
		$resultado = 0;
		include(INCLUDE_PATH.'admin/app/ws-modules/ws-update/ws_update.php');
		
		
		$mysqli = new mysqli(SERVIDOR_BD, USUARIO_BD, SENHA_BD, NOME_BD);
		if (mysqli_multi_query($mysqli, $GLOBALS["ConfigSQL"])) {
			do {
				if ($result = $mysqli->store_result()) {
					while ($row = $result->fetch_row()) {
						$resultado += 1;
					} // necessário para que complete o script antes do codigo continuar 
					$result->free();
				}
				if ($mysqli->more_results()) {
				}
			} while ($mysqli->more_results() && $mysqli->next_result());
		}
		
		
		configPaths($RewriteRule);
		
		##########################################################################################
		#  VERSÃO DO SISTEMA   
		##########################################################################################
		$localVersion  = json_decode(file_get_contents(INCLUDE_PATH.'admin/app/templates/json/ws-update.json'));
		
		##########################################################################################
		#  DATA E HORA DO INSTALAÇÃO/UPDATE   
		##########################################################################################
		$updateDateTime = new DateTime("now"); 
		$updateDateTime = $updateDateTime->format( 'Y-m-d H:i:s' );

		$I = new MySQL(); 
		$I->set_table(PREFIX_TABLES . 'setupdata');
		$I->set_insert('id', 1);
		$I->set_insert('system_version',$localVersion->version);
		$I->set_insert('data_update',$updateDateTime);

		//####################################### apenas se for o setup inicial
		
		if (isset($_REQUEST['formulario'])) {
			$_getInput = array();
			parse_str($_REQUEST['formulario'], $_getInput);
			$I->set_insert('client_name', $_getInput['CLIENT_NAME']);
			$I->set_insert('nome_bd', $_getInput['NOME_BD']);
			$I->set_insert('url_bd', $_getInput['SERVIDOR_BD']);
			$I->set_insert('log_bd', $_getInput['USUARIO_BD']);
			$I->set_insert('pass_bd', $_getInput['SENHA_BD']);
			$I->set_insert('url_initPath', 'inicio');
			$I->set_insert('url_setRoot', 'includes');
			$I->set_insert('url_set404', 'erro404.php');
			$I->set_insert('processoURL', '1');
		}
		$I->debug(0);
		$I->insert_or_replace();
		$VerifyStyle = new MySQL();
		$VerifyStyle->set_table(PREFIX_TABLES . 'setupdata');
		$VerifyStyle->set_where('id="1"');
		$VerifyStyle->select();
		
		
		$styles = new MySQL();
		$styles->set_table(PREFIX_TABLES . 'setupdata');
		$styles->set_where('id="1"');
		
		$stylejson  =   (empty(@$VerifyStyle->fetch_array[0]['stylejson'])  || null== @$VerifyStyle->fetch_array[0]['stylejson'])   ?   ""  :   $VerifyStyle->fetch_array[0]['stylejson'];  
		$stylecss   =   (empty(@$VerifyStyle->fetch_array[0]['stylecss'])   || null== @$VerifyStyle->fetch_array[0]['stylecss'])  ?   ""  :   $VerifyStyle->fetch_array[0]['stylecss'];

		if ($stylejson == "") {
			$json = "" . "[" . PHP_EOL . "  {" . PHP_EOL . "    name: 'Exemplo de style'," . PHP_EOL . "    element: 'h1'," . PHP_EOL . "   styles: { color: 'Blue' } ," . PHP_EOL . "    attributes: { 'class': 'myClass' }" . PHP_EOL . " }" . PHP_EOL . "]" . PHP_EOL;
			$styles->set_update('stylejson', $json);
		}
		if ($stylecss == "") {
			$styles->set_update('stylecss', ".myClass{" . PHP_EOL . " background: #e03953;" . PHP_EOL . "}");
		}
		
		if ($stylejson == "" || $stylecss == "") {
			$styles->salvar();
		}
		
		
		if(isset($_getInput['LOG_WEBMASTER']) && $_getInput['PASS_WEBMASTER']){

				// $usuario  = (get_magic_quotes_gpc()) ? trim(stripslashes($_getInput['LOG_WEBMASTER'])) : trim($_getInput['LOG_WEBMASTER']);
				$usuario  = trim($_getInput['LOG_WEBMASTER']);
				$usuario  = mysqli_real_escape_string($_conectMySQLi_,$usuario);
				$senha    = _codePass($_getInput['PASS_WEBMASTER']);

			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . 'ws_usuarios');
			$I->set_insert('id', 1);
			$I->set_insert('nome', 'Administrador');
			$I->set_insert('sobrenome', 'WebSheep');
			$I->set_insert('email', 'suporte@websheep.ws');
			$I->set_insert('login', $usuario);
			$I->set_insert('admin', '1');
			$I->set_insert('usuario', $usuario);
			$I->set_insert('senha', $senha);
			$I->set_insert('token', TOKEN_USER);
			$I->set_insert('id_status', 0);
			$I->set_insert('ativo', 1);
			$I->debug(0);
			$I->insert_or_replace();
		}

		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$t_ferramentas->debug(0);
		$t_ferramentas->select();


		$first = INCLUDE_PATH.'admin/app/config/firstacess';
		if (!file_exists($first) || file_put_contents($first,"false")) {
			echo "sucesso";
		} else {
			echo "Falha ao excluir o FirstAccess!";
		}
		exit;
	}
	
	/**
	#######################################  UPDATE   ###################################################################################################################################################
	**/
	function updateSQLInit() {
		global $_conectMySQLi_;
		@unlink(INCLUDE_PATH.'admin/app/config/firstacess');
		include(INCLUDE_PATH.'admin/app/ws-modules/ws-update/ws_update.php');
		$mysqli = new mysqli(SERVIDOR_BD, USUARIO_BD, SENHA_BD, NOME_BD);
		if (mysqli_multi_query($mysqli, $GLOBALS["ConfigSQL"])) {
			do {
				if ($result = $mysqli->store_result()) {
					while ($row = $result->fetch_row()) {
						########################################################################################################
						# ESSE =1 É PARA QUE O SCRIPT PROCESSE CADA LAÇO DO WHILLE SEM PULAR (sim ele pula o processo ñ sei pq)
						########################################################################################################
						$resultado = 1;
					} 
					$result->free();
				}
				if ($mysqli->more_results()) {
				}
			} while ($mysqli->more_results() && $mysqli->next_result());
		}

		$NEW_VERSION = json_decode(file_get_contents(INCLUDE_PATH.'admin/app/templates/json/ws-update.json'));

		$I = new MySQL();
		$I->set_table(PREFIX_TABLES . 'setupdata');
		$I->set_update('id', 1);
		$I->set_update('system_version',$NEW_VERSION->version);
		configPaths($RewriteRule);
		if ($I->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	function excluiCache() {
		global $_conectMySQLi_;
 
			// $File_exclude = new MySQL();
			// $File_exclude->set_table(PREFIX_TABLES . 'ws_link_cache_file_url');
			// $File_exclude->set_where('id<>"0"');
			// $File_exclude->exclui();
			// exit;

			$File_exclude = new MySQL();
			$File_exclude->set_table(PREFIX_TABLES . 'ws_link_cache_file_url');
			$File_exclude->set_where('id="' . $_POST['id_file'] . '"');
			$File_exclude->select();
			$file = $File_exclude->fetch_array[0]['filename'];

			$File_exclude = new MySQL();
			$File_exclude->set_table(PREFIX_TABLES . 'ws_link_cache_file_url');
			$File_exclude->set_where('id="' . $_POST['id_file'] . '"');
			@unlink(INCLUDE_PATH.'/ws-cache/'.$file);

			if($File_exclude->exclui()){
				die('ok');
			}else{
				die('0');
			};
		exit;
	}
	function salvaNomeFolder() {
		global $_conectMySQLi_;
		$_getInput = array();
		parse_str($_REQUEST['formulario'], $_getInput);
		$Rename = new MySQL();
		$Rename->set_table(PREFIX_TABLES . 'ws_pages');
		$Rename->set_where('id_tool="' . $_REQUEST['id'] . '"');
		$Rename->set_where('AND file="' . $_getInput['oldName'] . '"');
		$Rename->select();
		

		$content_newPage = '<meta charset="utf-8"/>
			<link rel="stylesheet" href="<?=ws::rootPath?>admin/app/templates/css/fontes/fonts.css">
			<div style="float:left;position:absolute;left:50%;transform: translate(-50%,-50%);width: auto;top: 50%;border-left: solid 10px #00939d;padding-left: 10px;">
				<span style="font-family:\'Titillium Web\';font-weight:100;float:left;width:100%;color:#00939d;font-size: 17px;text-align: left;">
					<strong>Título da página:</strong> '.ws::rootPath.'includes/'.$_getInput['oldName'].'<br>
					<strong>Local do arquivo:</strong> '.DOMINIO.'/'.ws::rootPath.$_getInput['file'].'<br>
					<strong>Criado em:</strong>'.date("d/m/Y às H:i:s").'<br>
				</span> 
			</div>';


		if ($Rename->_num_rows == 1 && $_getInput['renomear'] == 'renomear') {
			$oldFile = INCLUDE_PATH.'website/includes/' . $_getInput['oldName'];
			$newName = INCLUDE_PATH.'website/includes/' . $_getInput['file'];
			if (!file_exists($oldFile)) {
				echo "OPA! Não existe um arquivo com esse nome no servidor para renomear.";
				exit;
			}
			rename($oldFile, $newName);
			
		} elseif ($_getInput['renomear'] == 'novo') {
			if (file_exists(INCLUDE_PATH.'website/includes/' . $_getInput['file'])) {
				echo "OPA! Já existe um arquivo com esse nome no servidor.";
				exit;
			} else {
				file_put_contents(INCLUDE_PATH.'website/includes/' . $_getInput['file'], $content_newPage);
			}
		}
		
		
		$I = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$I->set_where('id="' . $_REQUEST['id'] . '"');
		$I->set_update('_tit_menu_', $_getInput['titulo_page']);
		$I->set_update('_tit_topo_', $_getInput['titulo_page']);
		
		$NewPage = new MySQL();
		$NewPage->set_table(PREFIX_TABLES . 'ws_pages');
		$NewPage->set_where('id_tool="' . $_REQUEST['id'] . '"');
		$NewPage->set_update('title', $_getInput['titulo_page']);
		$NewPage->set_update('file', $_getInput['file']);
		$NewPage->set_update('path', $_getInput['path']);
		$NewPage->set_update('sitemap_xml', mysqli_real_escape_string($_conectMySQLi_, $_getInput['sitemap_xml']));
		$NewPage->set_update('tool_master', $_getInput['toolMaster']);
		if (isset($_getInput['typeList'])) {
			$NewPage->set_update('typeList', $_getInput['typeList']);
		}
		$NewPage->salvar();
		$I->salvar();
		echo "sucesso";
		exit;
	}
	
	function templateCampo($id, $label, $place, $name, $legenda, $coluna_mysql, $caracteres, $type, $id_campo, $editor, $largura, $altura, $filtro, $values_opt, $financeiro, $disabled) {
		if ($editor == '1') {
			$editor = 'checked';
		}
		if ($financeiro == '1') {
			$financeiro = 'checked';
		}
		if ($disabled == '1') {
			$disabled = 'checked';
		}
		if ($numerico == '1') {
			$numerico = 'checked';
		}
		$retorno = "<li id='" . $id . "' class='campos'>";
		$retorno .= " <select class='inputText itens_ferramenta legenda' legenda='Tipo de campo'><option></option>";
		if ($type == "separador") {
			$select = "selected";
		} else {
			$select = "";
		}
		$retorno .= "<option value='separador' $select>____separador_____</option>";
		if ($type == "input") {
			$select = "selected";
		} else {
			$select = "";
		}
		$retorno .= "<option value='input' " . $select . ">Input Text</option>";
		if ($type == "textarea") {
			$select = "selected";
		} else {
			$select = "";
		}
		$retorno .= "<option value='textarea' $select>Text Area</option>";
		if ($type == "check") {
			$select = "selected";
		} else {
			$select = "";
		}
		$retorno .= "<option value='check' $select>Checkbox</option>";
		if ($type == "radio") {
			$select = "selected";
		} else {
			$select = "";
		}
		$retorno .= "<option value='radio' $select>Radio Box</option>";
		if ($type == "select") {
			$select = "selected";
		} else {
			$select = "";
		}
		$retorno .= "<option value='select' $select>Selectbox</option>";
		$retorno .= "</select>";
		$retorno .= "
						<input value='" . $id_campo . "'  class='inputText itens_ferramenta legenda'  legenda='ID do campo'>
						<input value='" . $name . "' class='inputText itens_ferramenta legenda'  legenda='Name do input'>
						<input value='" . $place . "' class='inputText itens_ferramenta legenda'  legenda='Placeholder'>
						<div class='c'></div>
						<input value='" . $legenda . "' class='inputText itens_ferramenta legenda'  legenda='Legenda do Balão'>
						<input value='" . $caracteres . "' type='number' class='inputText itens_ferramenta legenda'  legenda='Quantidades de caracteres'>
						<input value='" . $coluna_mysql . "' class='inputText itens_ferramenta legenda'  legenda='Coluna MySQL'>
						<input value='" . $label . "' class='inputText itens_ferramenta legenda'  legenda='Titulo do SEPARADOR'>
						<input value='" . $largura . "' type='number' class='inputText itens_ferramenta legenda'  legenda='Largura do campo'>
						<input value='" . $altura . "' type='number' class='inputText itens_ferramenta legenda'  legenda='Altura do campo'>
						<input value='" . $filtro . "'  class='inputText itens_ferramenta legenda'  legenda='Filtro de input<br>ex: 99/999/999'>
						<input value='" . $values . "'  class='inputText itens_ferramenta legenda'  legenda='Opções de Radiobox ou Selectbox<br>Separe por |   (  Shift +\  )'>
						<input " . $editor . "    type='checkbox' class='checkbox inputText itens_ferramenta legenda editor' legenda='Editor HTML de texto'>
						<input " . $financeiro . "    type='checkbox' class='checkbox inputText itens_ferramenta legenda financeiro' legenda='Mascara Financeira'>
						<input " . $disabled . "    type='checkbox' class='checkbox inputText itens_ferramenta legenda disabled' legenda='Desabilitado para edição'>
						<input " . $numerico . "    type='checkbox' class='checkbox inputText itens_ferramenta legenda numerico' legenda='Campo numérico'>
						<input " . $itens_basicos . "     type='checkbox' class='checkbox inputText itens_ferramenta legenda itens_basicos' legenda='Campo numérico'>";
		$retorno .= "
						<div class='c'>
							<div id='combo'>
									<div id='detalhes_img' class='bg02'>
									<spam ><img class='mover_item legenda' legenda='Mover a posição do ítem'  src='./app/templates/img/websheep/arrow-move.png'></spam>
									<spam ><img class='editar legenda' legenda='Editar Informações'       src=./app/templates/img/websheep/layer--pencil.png'></spam>
									<spam ><img class='salvar legenda' legenda='Salvar ferramenta'        src=./app/templates/img/websheep/accept.png'></spam>
									<spam ><img class='excluir legenda' legenda='<img class=\"editar\"      src=\"./app/templates/img/websheep/exclamation.png\" style=\"position: absolute;margin-top: -2px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Excluir ferramenta'   src='./app/templates/img/websheep/cross-button.png'></spam>
							</div>
					</div>
				</li>";
		return $retorno;
	}
	
	function addFerramenta() {
		$token = _token(PREFIX_TABLES . 'ws_ferramentas', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$I->set_insert('token', $token);
		if ($I->insert()) {

			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$I->set_where('token="' . $token . '"');
			$I->select();

			$grupos = new MySQL();
			$grupos->set_table(PREFIX_TABLES . 'ws_link_path_tools');
			$grupos->set_insert('id_tool', $I->fetch_array[0]['id']);
			$grupos->set_insert('id_path', '0');
			$grupos->insert();
			echo template($I->fetch_array[0]['id'], $I->fetch_array[0]['_tit_menu_'], $I->fetch_array[0]['token']);
		}
	}
	function addFerramentaPlugin() {
		$token = _token(PREFIX_TABLES . 'ws_ferramentas', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$I->set_insert('token', $token);
		$I->set_insert('_plugin_', '1');
		if ($I->insert()) {
			echo 'sucesso';
			exit;
		}
	}
	function addApp() {
		$_getInput = array();
		$isso      = array();
		$porisso   = array();
		parse_str($_REQUEST['AppData'], $_getInput);


		// CASO A FERRAMENTA SEJA UM CLONE
		$_PREFIXO_ = $_getInput['prefix'];

		$complementoTitulo = "";
		if ($_getInput['ToolModel'] != '0') {
			$ORIGINAL = new MySQL();
			$ORIGINAL->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$ORIGINAL->set_where('id="' . $_getInput['ToolModel'] . '"');
			$ORIGINAL->select();
			$ORIGINAL          = $ORIGINAL->fetch_array[0];
			$complementoTitulo = ' (' . $ORIGINAL['_tit_topo_'] . ')';
		}
		$tokenNovaFerramenta = _token(PREFIX_TABLES . 'ws_ferramentas', 'token');
		$I                   = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$I->set_insert('token', $tokenNovaFerramenta);
		$I->set_insert('App_Type', 1);
		$I->set_insert('App_Type', 1);
		$I->set_insert('_prefix_', $_PREFIXO_);
		// $I->set_insert('_grupo_pai_', $_getInput['page']);
		$I->set_insert('_tit_menu_', $_getInput['title'] . $complementoTitulo);
		$I->set_insert('_tit_topo_', $_getInput['title']);
		$I->set_insert('slug', $_getInput['slug']);
		#################################################################################### CASO A FERRAMENTA SEJA UM CLONE
		if ($_getInput['ToolModel'] != '0') {
			$I->set_insert('_exec_js_', $ORIGINAL['_exec_js_']);
			$I->set_insert('_js_', @$ORIGINAL['_js_']);
			$I->set_insert('item_type', $ORIGINAL['item_type']);
			$I->set_insert('_patch_', $ORIGINAL['_patch_']);
			$I->set_insert('_pasta_especial_', $ORIGINAL['_pasta_especial_']);
			$I->set_insert('det_listagem_item', $ORIGINAL['det_listagem_item']);
			$I->set_insert('_alterado_', $ORIGINAL['_alterado_']);
			$I->set_insert('_rel_prod_cat_', $ORIGINAL['_rel_prod_cat_']);
			$I->set_insert('_tool_pai_', $ORIGINAL['_tool_pai_']);
			$I->set_insert('_menu_popup_', $ORIGINAL['_menu_popup_']);
			$I->set_insert('_esconde_topo_', $ORIGINAL['_esconde_topo_']);
			$I->set_insert('_plugin_', $ORIGINAL['_plugin_']);
			$I->set_insert('posicao', $ORIGINAL['posicao']);
			$I->set_insert('_niveis_', $ORIGINAL['_niveis_']);
			$I->set_insert('_id_unico_', $ORIGINAL['_id_unico_']);
			$I->set_insert('_keywords_', $ORIGINAL['_keywords_']);
			$I->set_insert('avatar', $ORIGINAL['avatar']);
			$I->set_insert('_afinidades_', $ORIGINAL['_afinidades_']);
			$I->set_insert('_selos_', $ORIGINAL['_selos_']);
			$I->set_insert('_fotos_', $ORIGINAL['_fotos_']);
			$I->set_insert('_galerias_', $ORIGINAL['_galerias_']);
			$I->set_insert('_files_', $ORIGINAL['_files_']);
			$I->set_insert('_videos_', $ORIGINAL['_videos_']);
			$I->set_insert('_arquivos_', $ORIGINAL['_arquivos_']);
			$I->set_insert('_extencao_', $ORIGINAL['_extencao_']);
			$I->set_insert('clone_tool', $_getInput['ToolModel']);
		}
		####################################################################################
		if ($I->insert()) {
			sleep(1);
			// CLONE CLONAR COPIAR
			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$I->set_where('token="' . $tokenNovaFerramenta . '"');
			$I->select();
			$NovaFerramenta = $I->fetch_array[0];
			$original       = explode(',', $NovaFerramenta['det_listagem_item']);
			
			####################################################################################
			# AGORA CRIAMOS AS TABELAS DA FERRAMENTA    
			####################################################################################

			$models = array('_campos','_cat','_files','_gal','_img','_img_gal','_item','_link_op_multiple','_link_prod_cat','_op_multiple');
			$baseTable = PREFIX_TABLES.$_getInput['slug'];
			$duplica = new MySQL();
			foreach ($models as $model) {
				$duplica->select('CREATE TABLE IF NOT EXISTS `'.$baseTable.$model.'` SELECT * FROM _model'.$model.';');
				$duplica->select('ALTER TABLE `'.$baseTable.$model.'` MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY;');
				$duplica->select('ALTER TABLE `'.$baseTable.$model.'` ADD INDEX(`id`);');
				$duplica->select('ALTER TABLE `'.$baseTable.$model.'` ADD UNIQUE(`id`);');
			}
			if ($_getInput['ToolModel'] != '0') {
				$campo = new MySQL();
				$campo->set_table(PREFIX_TABLES.$_getInput['slug'].'_campos');
				$campo->set_where('ws_id_ferramenta="' . $_getInput['ToolModel'] . '"');
				$campo->select();
				foreach ($campo->fetch_array as $colunaMySQL) {
					
					$colunaName = "";
					if ($colunaMySQL['coluna_mysql'] != "") {
						# VERIFICA AGORA SE A COLUNA TEM O PROFIXO DA FERRAMENTA ORIGINAL
						if (substr($colunaMySQL['coluna_mysql'], 0, strlen($ORIGINAL['_prefix_'])) == $ORIGINAL['_prefix_']) {
							# SUBSTITUI O PREFIXO ANTIGO PELO NOVO
							$colunaName = $_PREFIXO_ . substr($colunaMySQL['coluna_mysql'], strlen($ORIGINAL['_prefix_']));
						} else {
							# APENAS ADICIONA O NOVO PREFIXO
							$colunaName = $_PREFIXO_ . $colunaMySQL['coluna_mysql'];
						}
						# SÓ DUPLICA NOME SE NÃO FOR RADIOBOX
						if ($colunaMySQL['type'] != "radiobox") {
							$colunaName = duplicateColumName($colunaName);
						}
						
						$isso[]    = $colunaMySQL['coluna_mysql'];
						$porisso[] = $colunaName;
					}
					
					$token    = _token(PREFIX_TABLES.$_getInput['slug'].'_campos', 'token');
					$addCampo = new MySQL();
					$addCampo->set_table(PREFIX_TABLES.$_getInput['slug'].'_campos');
					$addCampo->set_insert('ws_id_ferramenta', $NovaFerramenta['id']);
					$addCampo->set_insert('posicao', $colunaMySQL['posicao']);
					$addCampo->set_insert('financeiro', $colunaMySQL['financeiro']);
					$addCampo->set_insert('disabled', $colunaMySQL['disabled']);
					$addCampo->set_insert('numerico', $colunaMySQL['numerico']);
					$addCampo->set_insert('autosize', $colunaMySQL['autosize']);
					$addCampo->set_insert('upload', $colunaMySQL['upload']);
					$addCampo->set_insert('calendario', $colunaMySQL['calendario']);
					$addCampo->set_insert('label', $colunaMySQL['label']);
					$addCampo->set_insert('token', $token);
					$addCampo->set_insert('place', $colunaMySQL['place']);
					$addCampo->set_insert('legenda', $colunaMySQL['legenda']);
					
					if ($colunaMySQL['type'] != "bt_files" && $colunaMySQL['type'] != "bt_fotos" && $colunaMySQL['type'] != "label" && $colunaMySQL['type'] != "quebra" && $colunaMySQL['type'] != "vazio") {
						$addCampo->set_insert('name', $colunaName);
						$addCampo->set_insert('coluna_mysql', $colunaName);
						$addCampo->set_insert('id_campo', $colunaName);
					} else {
						$addCampo->set_insert('name', $colunaMySQL['name']);
						$addCampo->set_insert('id_campo', $colunaMySQL['id_campo']);
					}
					$addCampo->set_insert('caracteres', $colunaMySQL['caracteres']);
					$addCampo->set_insert('type', $colunaMySQL['type']);
					$addCampo->set_insert('editor', $colunaMySQL['editor']);
					$addCampo->set_insert('largura', $colunaMySQL['largura']);
					$addCampo->set_insert('altura', $colunaMySQL['altura']);
					$addCampo->set_insert('filtro', $colunaMySQL['filtro']);
					$addCampo->set_insert('values_opt', $colunaMySQL['values_opt']);
					$addCampo->set_insert('background', $colunaMySQL['background']);
					$addCampo->set_insert('color', $colunaMySQL['color']);
					$addCampo->set_insert('sintaxy', $colunaMySQL['sintaxy']);
					$addCampo->set_insert('referencia', $colunaMySQL['referencia']);
					$addCampo->set_insert('cat_referencia', $colunaMySQL['cat_referencia']);
					$addCampo->set_insert('rua', $colunaMySQL['rua']);
					$addCampo->set_insert('cidade', $colunaMySQL['cidade']);
					$addCampo->set_insert('uf', $colunaMySQL['uf']);
					$addCampo->set_insert('pais', $colunaMySQL['pais']);
					$addCampo->set_insert('cep', $colunaMySQL['cep']);
					$addCampo->set_insert('bairro', $colunaMySQL['bairro']);
					$addCampo->set_insert('listaTabela', $colunaMySQL['listaTabela']);
					$addCampo->set_insert('labelSup', $colunaMySQL['labelSup']);
					if ($addCampo->insert()) {
						if ($colunaMySQL['coluna_mysql'] != "" && $colunaMySQL['type'] != "bt_files" && $colunaMySQL['type'] != "bt_fotos" && $colunaMySQL['type'] != "label" && $colunaMySQL['type'] != "quebra" && $colunaMySQL['type'] != "vazio") {
							$s = new MySQL();
							$s->set_table(PREFIX_TABLES.$_getInput['slug'].'_item');
							$s->set_colum($colunaName);
							if (!$s->verify()) {
								$AddColum = new MySQL();
								$AddColum->set_table(PREFIX_TABLES.$_getInput['slug'].'_item');
								if ($colunaMySQL['type'] == 'check') {
									$AddColum->set_colum(array(
										$colunaName,
										' BOOLEAN DEFAULT FALSE'
									));
								} elseif ($colunaMySQL['type'] == 'textarea' || $colunaMySQL['type'] == 'editor') {
									$AddColum->set_colum(array(
										$colunaName,
										' TEXT NULL DEFAULT NULL'
									));
								} else {
									$AddColum->set_colum(array(
										$colunaName,
										' varchar(300) DEFAULT NULL '
									));
								}
								$AddColum->add_column();
							}
						}
					}
				}
			}
			
			

			
			$i = 0;
			foreach ($original as $key => $value) {
				$a = 0;
				foreach ($isso as $issoKey => $issoValue) {
					if ($original[$i] == $issoValue) {
						$original[$i] = $porisso[$a];
					}
					$a++;
				}
				$i++;
			}
			$updateListagem = new MySQL();
			$updateListagem->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$updateListagem->set_where('token="' . $tokenNovaFerramenta . '"');
			$updateListagem->set_update('det_listagem_item', implode($original, ','));
			$updateListagem->salvar();

			if(isset($_REQUEST['group'])){
				$_GROUPS  = array_unique($_REQUEST['group']);
				foreach ($_GROUPS as  $group) {
					$insert_to_group = new MySQL();
					$insert_to_group->set_table(PREFIX_TABLES . 'ws_link_path_tools');
					$insert_to_group->set_insert('id_tool', $NovaFerramenta['id']);
					$insert_to_group->set_insert('id_path', $group);
					$insert_to_group->insert();
				}
			}else{
					$grupos = new MySQL();
					$grupos->set_table(PREFIX_TABLES . 'ws_link_path_tools');
					$grupos->set_insert('id_tool', $NovaFerramenta['id']);
					$grupos->set_insert('id_path', '0');
					$grupos->insert();
			}

			echo template($NovaFerramenta['id'], $NovaFerramenta['_tit_menu_'], $NovaFerramenta['token']);
		}
	}
	function addPage() {
		$_getInput = array();
		parse_str($_REQUEST['formulario'], $_getInput);
		$_getInput['file'] = str_replace('/', '_', $_getInput['file']);
		$token             = _token(PREFIX_TABLES . 'ws_ferramentas', 'token');
		$_TOOL             = new MySQL();
		$_TOOL->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$_TOOL->set_insert('token', $token);
		$_TOOL->set_insert('_menu_popup_', '1');
		$_TOOL->set_insert('_tit_topo_', $_getInput['titulo_page']);
		$_TOOL->set_insert('_tit_menu_', $_getInput['titulo_page']);
		$Page = new MySQL();
		$Page->set_table(PREFIX_TABLES . 'ws_pages');
		$Page->set_insert('token', $token);
		$Page->set_insert('title', $_getInput['titulo_page']);
		$Page->set_insert('file', $_getInput['file']);
		$Page->set_insert('path', $_getInput['path']);
		$Page->set_insert('token', $token);
		$Page->set_insert('type', 'path');
		$path = explode('/', $_getInput['file']);


		$content_newPage = '<meta charset="utf-8"/>
			<link rel="stylesheet" href="<?=ws::rootPath?>admin/app/templates/css/fontes/fonts.css">
			<div style="float:left;position:absolute;left:50%;transform: translate(-50%,-50%);width: auto;top: 50%;border-left: solid 10px #00939d;padding-left: 10px;">
				<span style="font-family:\'Titillium Web\';font-weight:100;float:left;width:100%;color:#00939d;font-size: 17px;text-align: left;">
					<strong>Título da página:</strong> '.$_getInput['titulo_page'].'<br>
					<strong>Local do arquivo:</strong> '.ws::rootPath.'includes/'.$_getInput['file'].'<br>
					<strong>URL de acesso:</strong> '.DOMINIO.'/'.ws::rootPath.$_getInput['path'].'<br>
					<strong>Criado em:</strong>'.date("d/m/Y às H:i:s").'<br>
				</span> 
			</div>';


		if (count($path) > 1) {
			$file = implode(array_slice($path, -1), '');
			$path = array_slice($path, 0, -1);
			if (strpos($file, '.') != -1) {
				$listPath = array();
				foreach ($path as $value) {
					$listPath[] = $value;
					$newPath    = INCLUDE_PATH.'website/includes/' . implode($listPath, '/');
					if (!file_exists($newPath)) {
						mkdir($newPath);
					}
				}
				if (!file_exists($newPath . "/" . $file)) {
					file_put_contents($newPath . "/" . $file, "");
				}
			}
		} else {
			$file = implode(array_slice($path, -1), '');
			if (!file_exists(INCLUDE_PATH.'website/includes')) {
				mkdir(INCLUDE_PATH.'website/includes');
			}
			
			if (!file_exists(INCLUDE_PATH.'website/includes/'. $file)) {
				file_put_contents(INCLUDE_PATH.'website/includes/' . $file,$content_newPage);
			}
		}
	
		if ($_TOOL->insert()) {
			$_TOOL = new MySQL();
			$_TOOL->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$_TOOL->set_where('token="' . $token . '"');
			$_TOOL->select();
			$Page->set_insert('id_tool', $_TOOL->fetch_array[0]['id']);
			$Page->insert();
			echo "sucesso";
			exit;
		}
	}
	function salvaFerramenta() {
		global    $_conectMySQLi_;
		$session  = new session();
		$Salva    = new MySQL();
		$Salva->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$Salva->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
		$_getInput = array();
		parse_str($_REQUEST['inputs'], $_getInput);

		if (empty($_getInput['_menu_popup_'])) {
			$_getInput['_menu_popup_'] = '0';
		} else {
			$_getInput['_menu_popup_'] = '1';
		}

		if (empty($_getInput['max_item'])) {
			$_getInput['max_item'] = '0';
		} 

		if (empty($_getInput['_keywords_'])) {
			$_getInput['_keywords_'] = '0';
		} else {
			$_getInput['_keywords_'] = '1';
		}
		if (empty($_getInput['_fotos_'])) {
			$_getInput['_fotos_'] = '0';
		} else {
			$_getInput['_fotos_'] = '1';
		}
		if (empty($_getInput['_galerias_'])) {
			$_getInput['_galerias_'] = '0';
		} else {
			$_getInput['_galerias_'] = '1';
		}
		if (empty($_getInput['_videos_'])) {
			$_getInput['_videos_'] = '0';
		} else {
			$_getInput['_videos_'] = '1';
		}
		if (empty($_getInput['_arquivos_'])) {
			$_getInput['_arquivos_'] = '0';
		} else {
			$_getInput['_arquivos_'] = '1';
		}
		if (empty($_getInput['_rel_prod_cat_'])) {
			$_getInput['_rel_prod_cat_'] = '0';
		} else {
			$_getInput['_rel_prod_cat_'] = '1';
		}
		
		if (empty($_getInput['_esconde_topo_'])) {
			$_getInput['_esconde_topo_'] = '0';
		} else {
			$_getInput['_esconde_topo_'] = '1';
		}
		
		if (empty($_getInput['_plugin_'])) {
			$_getInput['_plugin_'] = '0';
		} else {
			$_getInput['_plugin_'] = '1';
		}
		
		// $Salva->set_update('_grupo_pai_', 0);
		$Salva->set_update('max_item', $_getInput['max_item']);
		$Salva->set_update('_prefix_', $_getInput['_prefix_']);
		$Salva->set_update('_menu_popup_', $_getInput['_menu_popup_']);
		$Salva->set_update('_tit_menu_', $_getInput['_tit_menu_']);
		$Salva->set_update('_tit_topo_', $_getInput['_tit_menu_']);
		$Salva->set_update('_patch_', $_getInput['_patch_']);
		$Salva->set_update('_niveis_', $_getInput['_niveis_']);
		$Salva->set_update('_keywords_', $_getInput['_keywords_']);
		$Salva->set_update('_rel_prod_cat_', $_getInput['_rel_prod_cat_']);
		// $Salva->set_update('avatar', $_getInput['identificadoravatar']);
		$Salva->set_update('det_listagem_item', $_getInput['det_listagem_item']);
		$Salva->set_update('_esconde_topo_', $_getInput['_esconde_topo_']);
		$Salva->set_update('_plugin_', $_getInput['_plugin_']);
		$Salva->set_update('slug', $_getInput['_slug_']);
		$Salva->set_update('_exec_js_', $_getInput['_exec_js_']);
		$Salva->set_update('_desc_', $_getInput['_desc_']);
		$Salva->set_update('_js_', mysqli_real_escape_string($_conectMySQLi_, @$_REQUEST['_js_']));


		$Salva->set_update('html_item',   mysqli_real_escape_string($_conectMySQLi_, str_replace("{{",'{{{_}',@$_REQUEST['html_item'])));
		$Salva->set_update('html_cat',    mysqli_real_escape_string($_conectMySQLi_, str_replace("{{",'{{{_}',@$_REQUEST['html_cat'])));
		$Salva->set_update('html_img',    mysqli_real_escape_string($_conectMySQLi_, str_replace("{{",'{{{_}',@$_REQUEST['html_img'])));
		$Salva->set_update('html_gal',    mysqli_real_escape_string($_conectMySQLi_, str_replace("{{",'{{{_}',@$_REQUEST['html_gal'])));
		$Salva->set_update('html_img_gal',mysqli_real_escape_string($_conectMySQLi_, str_replace("{{",'{{{_}',@$_REQUEST['html_img_gal'])));
		$Salva->set_update('html_file',   mysqli_real_escape_string($_conectMySQLi_, str_replace("{{",'{{{_}',@$_REQUEST['html_file'])));


		#######################################################################################
		# APAGA TODOS OS REGISTROS DA FERRAMENTA NOS VINCULOS
		#######################################################################################
		$t_ferramentas = new MySQL();
		$t_ferramentas->set_table(PREFIX_TABLES . 'ws_link_path_tools');
		$t_ferramentas->set_where('id_tool="'.$_REQUEST['ws_id_ferramenta'].'"');
		$t_ferramentas->exclui();

		#######################################################################################
		# AGORA GRAVA UM POR UM NOVAMENTE
		#######################################################################################
		if(@$_REQUEST['groups']!== null){
			foreach ($_REQUEST['groups'] as $value) {
				$grupos = new MySQL();
				$grupos->set_table(PREFIX_TABLES . 'ws_link_path_tools');
				$grupos->set_insert('id_tool', $_REQUEST['ws_id_ferramenta']);
				$grupos->set_insert('id_path', $value);
				$grupos->insert();
			}
		}

		if ($Salva->salvar()) {
			// DEPOIS QUE GRAVA, CRIA-SE TODAS AS TABELAS MYSQL DA FERRAMENTA
			$t_ferramentas = new MySQL();
			$t_ferramentas->set_table(PREFIX_TABLES . 'ws_ferramentas');
			$t_ferramentas->set_where('id="' . $_REQUEST['ws_id_ferramenta'] . '"');
			$t_ferramentas->set_update('_alterado_', '1');
			$t_ferramentas->salvar();
			$session->set('ws_id_ferramenta',$_REQUEST['ws_id_ferramenta']);
			ws::updateTool($_REQUEST['ws_id_ferramenta']);
			echo "sucesso";
			exit;
		} else {
			_erro("Falha ao salvar ferramenta.");
			exit;
		}
	}
	
	
	/*######################################################################################## BKP ############################################################## */
	function apliqueTheme() {
		$extractPath = INCLUDE_PATH;
		$_root       = $extractPath;
		$arquivo     = INCLUDE_PATH.'ws-bkp/' . $_REQUEST['dataFile'];
		$website     = $extractPath . "/website";
		
		if ($_REQUEST['type'] == "full") {
			if (file_exists($website)) {
				_excluiDir(INCLUDE_PATH.'website');
				echo 'excluiu tudo...' . PHP_EOL;
			}
			$zip = new ZipArchive();
			if ($zip->open($arquivo)) {
				$zip->extractTo($_root);
				$zip->close();
				echo 'EXTRAIU tudo...' . PHP_EOL;
			}
		} elseif ($_REQUEST['type'] == "replace") {
			$zip = new ZipArchive();
			if ($zip->open($arquivo)) {
				$zip->extractTo($_root);
				$zip->close();
				echo 'substituiu tudo...' . PHP_EOL;
			}
		} elseif ($_REQUEST['type'] == "jump") {
			$files = array();
			$zip   = new ZipArchive();
			if ($zip->open($arquivo)) {
				$zip->extractTo($_root, 'setup.sql');
				for ($i = 0; $i < $zip->numFiles; $i++) {
					$filename = $zip->getNameIndex($i);
					if (!file_exists('./../../../' . $filename)) {
						$zip->extractTo($extractPath, $filename);
					}
				}
			}
			$zip->close();
			echo 'pulou tudo...' . PHP_EOL;
		} elseif ($_REQUEST['type'] == 'none') { // 
			$zip = new ZipArchive();
			if ($zip->open($arquivo)) {
				$MySQL = $zip->getFromName('setup.sql');
				$zip->extractTo($_root, 'setup.sql');
				$zip->close();
				echo 'setup.sql...' . PHP_EOL;
			}
		} elseif ($_REQUEST['type'] == 'tools') {
			$zip = new ZipArchive();
			if ($zip->open($arquivo)) {
				$tool = $zip->getFromName('ws-tools.ws');
				$zip->close();
				echo $tool;
				exit;
			}
		}
		$zip = new ZipArchive();
		if ($zip->open($arquivo)) {
			$setup     = $zip->getFromName('ws-setup.sql');
			$structure = $zip->getFromName('ws-structure.sql');
			$website   = $zip->getFromName('ws-website.sql');
			$zip->close();
		}
		if ($_REQUEST['dataBase'] == 'none') {
			echo 'none...' . PHP_EOL;
		} elseif ($_REQUEST['dataBase'] == 'basic') {
			echo 'basic...' . PHP_EOL;
			exec_SQL($setup);
		} elseif ($_REQUEST['dataBase'] == 'tools') {
			echo 'tools...' . PHP_EOL;
			exec_SQL($setup);
			exec_SQL($structure);
		} elseif ($_REQUEST['dataBase'] == 'content') {
			echo 'setup...' . PHP_EOL;
			exec_SQL($setup);
			echo 'structure...' . PHP_EOL;
			exec_SQL($structure);
			echo 'website...' . PHP_EOL;
			exec_SQL($website);
		}
		
		ob_end_clean();
		echo 1;
		exit;
	}
	
	$_dir_theme_   = array();
	$_files_theme_ = array();
	function createTheme() {
		global $_conectMySQLi_;
		global $_dir_theme_;
		global $_files_theme_;
		
		$TOOL = new MySQL();
		$TOOL->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$TOOL->set_where('App_Type="1"');
		$TOOL->select();
		
		$newTool = Array();
		foreach ($TOOL->fetch_array as $key) {
			$newTool[] = exportToolFile($key['id'], true,false,"return");
		}
		$return         = "";
		$contentMySQL   = "";
		$MySQLStructure = "";
		$ferramentas    = "";
		$tables         = array();
		$D              = new MySQL();
		$D->show_table();
		foreach ($D->fetch_array as $value) {
			$tables[] = $value[0];
		}
		lista_Dir_theme(INCLUDE_PATH.'website');
		foreach ($tables as $table) {
			########################################################################
			# VERIFICA SE SÃO TABELAS QUE NÃO PODEMOS MEXER
			########################################################################
			if ($table != PREFIX_TABLES . "setupdata" && $table != PREFIX_TABLES . "ws_usuarios" && $table != PREFIX_TABLES . "ws_user_link_ferramenta") {
				$result          = mysqli_query($_conectMySQLi_, 'SELECT * FROM ' . $table);
				$num_fields      = mysqli_num_fields($result);
				$_table_new_name = '{_prefix_}' . substr($table, strlen(PREFIX_TABLES));
				$return .= 'DROP TABLE IF EXISTS ' . $_table_new_name . ';';
				$row2 = mysqli_fetch_row(mysqli_query($_conectMySQLi_, 'SHOW CREATE TABLE ' . $table));
				$return .= PHP_EOL . str_replace($table, $_table_new_name, $row2[1]) . ";" . PHP_EOL;
				for ($i = 0; $i < $num_fields; $i++) {
					while ($row = mysqli_fetch_row($result)) {
						########################################################################
						# CASO SEJA CONTEÚDO ESTRUTURAL DO SITE
						########################################################################
						if ($_table_new_name == "{_prefix_}ws_biblioteca" || $_table_new_name == "{_prefix_}ws_ferramentas" || $_table_new_name == "{_prefix_}ws_pages" || $_table_new_name == "{_prefix_}_$slug.campos" || $_table_new_name == "{_prefix_}ws_link_url_file" || $_table_new_name == "{_prefix_}ws_webmaster") {
							$linha = 'INSERT IGNORE INTO ' . $_table_new_name . ' VALUES(';
							for ($j = 0; $j < $num_fields; $j++) {
								$row[$j] = addslashes($row[$j]);
								$row[$j] = str_replace("\n", PHP_EOL, $row[$j]);
								if (isset($row[$j])) {
									$linha .= '"' . $row[$j] . '"';
								} else {
									$linha .= '""';
								}
								if ($j < ($num_fields - 1)) {
									$linha .= ',';
								}
							}
							$linha .= ");";
							$MySQLStructure .= str_replace(array(PHP_EOL,"\n","\r"), "", $linha) . PHP_EOL;
						}     
						
						########################################################################
						# AGORA O RESTANTE É CONTEUDO INSERIDO
						########################################################################
						if ($_table_new_name != "{_prefix_}ws_ferramentas" && $_table_new_name != "{_prefix_}ws_pages" && $_table_new_name != "{_prefix_}_$slug.campos" && $_table_new_name != "{_prefix_}ws_link_url_file" && $_table_new_name != "{_prefix_}ws_webmaster") {
							$linha = 'INSERT IGNORE INTO ' . $_table_new_name . ' VALUES(';
							for ($j = 0; $j < $num_fields; $j++) {
								$row[$j] = addslashes($row[$j]);
								$row[$j] = str_replace("\n", PHP_EOL, $row[$j]);
								if (isset($row[$j])) {
									$linha .= '"' . $row[$j] . '"';
								} else {
									$linha .= '""';
								}
								if ($j < ($num_fields - 1)) {
									$linha .= ',';
								}
							}
							$linha .= ");";
							$contentMySQL .= str_replace(array(
								PHP_EOL,
								"\n",
								"\r"
							), "", $linha) . PHP_EOL;
						}
					}
				}
				$return .= PHP_EOL . PHP_EOL . PHP_EOL;
			}
		}
		
		if (!file_exists(INCLUDE_PATH.'ws-bkp')) {
			mkdir(INCLUDE_PATH.'ws-bkp');
		}
		$fileNameZipBKP = INCLUDE_PATH.'ws-bkp/bkp_(' . count($_files_theme_) . '_files)_' . date("Y-m-d_H-i-s") . '.zip';
		echo $fileNameZipBKP.PHP_EOL;



		$z = new ZipArchive();
		$criou = $z->open($fileNameZipBKP, ZipArchive::CREATE);
		if ($criou === true) {
			foreach ($_dir_theme_ as $dir) {
				$z->addEmptyDir('website/' . $dir);
			}
			foreach ($_files_theme_ as $file) {
				$z->addFile(INCLUDE_PATH.'website/' . $file, 'website/' . $file);
			}


			if ($_POST['avatar'] == "") {
				$contens = '{"title":"' . $_POST['title'] . '","content":"' . $_POST['content'] . '","thumb":"'.ROOT_WEBSHEEP.'ws-img/200/200/null","files":"' . count($_files_theme_) . '","type":"files,tools,content"}';
			} else {
				$contens = '{"title":"' . $_POST['title'] . '","content":"' . $_POST['content'] . '","thumb":"' . basename($_POST['avatar']) . '","files":"' . count($_files_theme_) . '","type":"files,tools,content"}';
				$z->addFromString(basename($_POST['avatar']), file_get_contents(INCLUDE_PATH.'website/assets/upload-files/'.basename($_POST['avatar'])));
			}


			$z->addFromString('ws-setup.sql', $return);
			$z->addFromString('ws-website.sql', $contentMySQL);
			$z->addFromString('ws-structure.sql', $MySQLStructure);
			$z->addFromString('ws-tools.ws', base64_encode(json_encode($newTool, JSON_PRETTY_PRINT)));
			$z->addFromString('ws-info.json', $contens);
			$z->close();
			echo "sucesso!";
		} else {
			echo "erro!";
		}
	}
	function lista_Dir_theme($diretorio) {
		global $_dir_theme_;
		global $_files_theme_;
		$folder = opendir($diretorio);
		while ($item = readdir($folder)) {
			if ($item == '.' || $item == '..') {
				continue;
			}
			if (is_dir($diretorio . '/' . $item)) {
				$newDir = str_replace(array(
					INCLUDE_PATH.'website/'
				), "", $diretorio . '/' . $item);
				array_push($_dir_theme_, $newDir);
				lista_Dir_theme($diretorio . '/' . $item);
			} else {
				$newDir = str_replace(array(
					INCLUDE_PATH.'website/'
				), "", $diretorio . '/' . $item);
				array_push($_files_theme_, $newDir);
			}
		}
	}
	
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	define('ROOT_WEBSHEEP','/');
	// if(!defined("ROOT_WEBSHEEP")) {
	// 	// $path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	// 	// $path = explode('/',$path);
	// 	// $path = array_filter($path);
	// 	// $path = implode($path,"/");
	// 	define('ROOT_WEBSHEEP',$_POST['ROOT_WEBSHEEP']);
	// }

	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}
		
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	$session = new session();
	if (isset($_REQUEST['function'])) {
		_exec($_REQUEST['function']);
	}