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

############################################################################
# IMPORTAMOS A CLASSE DO SISTEMA
############################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	$session = new session();
	$session->set('_PATCH_', 'app/ws-modules/ws-webmaster'); 
	include("./autoComplete.php");

?>
<!-- 
	<script src="<?=ws::rootPath?>admin/app/templates/js/formatCode/javascriptobfuscator_unpacker.js"></script>
	<script src="<?=ws::rootPath?>admin/app/templates/js/formatCode/urlencode_unpacker.js"></script>
	<script src="<?=ws::rootPath?>admin/app/templates/js/formatCode/p_a_c_k_e_r_unpacker.js"></script>
	<script src="<?=ws::rootPath?>admin/app/templates/js/formatCode/myobfuscate_unpacker.js"></script>
 -->
<link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">
<script type="text/javascript">
	include_css('<?=ws::rootPath?>admin/app/templates/css/websheep/ws-modules/ws-webmaster/style.min.css?<?=md5(uniqid(rand(), true))?>',  	'css_mod', 'All');
</script>
<style type="text/css">
	#container {
		z-indexms-touch-action: none;
		overflow: inherit!important;
	}
	#divEditor{
		font-family: 'Source Code Pro', monospace;
		font-size: 15px;
		background-color: #292931;
	}

/* ################################################################################################## */
	.nave_folders{overflow: hidden!important;}
	#mode_chosen .chosen-single {
		display: none;
	}
	#bkpsFile_chosen .chosen-single {
		display: none;
	}
	.chosen-container .chosen-results li.highlighted {
		background: none;
		background-color: rgba(255, 255, 255, 0.07);
		text-shadow: none;
	}
	.chosen-container-active.chosen-with-drop .chosen-single {
		background: none;
		border: 0;
		box-shadow: none;
		height: 33px;
	}
	.chosen-container .chosen-drop {
		background: none;
		border: 0;
		box-shadow: inset 0 0 1px rgba(255, 255, 255, 0.3);
		text-shadow: 0 0 0 #000;
		background-color: rgb(36, 36, 36);
		border: solid 1px #1B1919;
	}
	.chosen-container .ace_string {
		color: #ceb56c!important;
	}
	.chosen-container-single .chosen-single {
		background: none;
		border: 0;
		box-shadow: none;
		text-shadow: 0 0 0 #000;
		color: #CCC;
	}
	.ace_print-margin {display: none;}


</style>
<div class="c"></div>
<div class="nave_menu recolhido bg02 w1" style="z-index: 2;">
	<div class="ul_icons">
		<div id="" class="optEditor">
			File
			<div class="ul">
					<div id="novoArquivo">New</div>
					<div id="loadfile">Load 			<span>Ctrl+o</span></div>
					<div id="salvarArquivo">Save 	 	<span>Ctrl+s</span></div>
					<div id="exclFile">Delete this file</div>
			</div>
		</div>
		<div id="" class="optEditor">
			Folder
			<div class="ul">
					<div id="novodir">New folder</div>
					<div id="exclFolder">Delete folder</div>
			</div>
		</div>
		<div id="" class="optEditor">
			Insert
			<div class="ul">
					<div id="addToll"  			style="display:block">Adicionar uma Ferramenta <span>Ctrl+'</span> </div>
					<div id="plugin" 			style="display:block">Adicionar um Plugin <span>Ctrl+1</span> </div>
					<div id="addPagination"  	style="display:block">Inserir Paginação </div>
					<div id="addSendForm"		style="display:block">Adicionar formulário/cadastro</div>
					<div id="templateBootstrap"	style="display:block">Adicionar template Bootstrap</div>
			</div>
		</div>
		<div id="formatHTML" class="optEditor">
			Format HTML 
		</div>
		<div id="maximize" class="optEditor">
			<i class="fa fa-television"></i>
			FullScreen
		</div>
		<div id="popup" class="optEditor">
			<i class="fa fa-television"></i>
			Nova Janela
			<span style="color:#60ff00;font-size:10px;margin-top:-15px;position:absolute;float:right;right:-30px;letter-spacing:2px;">New!</span>
		</div>
	 </div>
	<form>
		<select id="bkpsFile" style="width:310px;">
			<option>Controle de versão</option>
		</select>
		<select id="mode" style="width:130px;">
		<? 
			$dh=opendir( './src-min-noconflict'); 
			while($arquivo=readdir($dh)){ 
				$nameFile=strpos($arquivo, 'mode-'); 
				if($nameFile>-1){ 
					echo PHP_EOL.'<option value="'.str_replace(array('.js','mode-'),'',$arquivo).'">'.str_replace(array('.js','mode-'),'',$arquivo).'</option>'; 
				} 
			}
		?> 
		</select>
	</form>
</div>
<div class="nave_menu fileTabContainer recolhido bg02 w1">
	<div class="container">
		<div class="new"></div>
	</div>
</div>
<div id="nave_folders" class="nave_folders recolhido">
<?php
	function CriaPastas($dir,$oq=0){
		if (is_dir($dir)) {
			$dh = opendir($dir);
			while($diretorio = readdir($dh)){
				if($diretorio != '..' && $diretorio != '.' && is_dir($dir.'/'.$diretorio)){


				$dataPath = '/'.implode(array_filter(explode('/',str_replace(INCLUDE_PATH.'website',"",$dir.'/'.$diretorio))),"/");


					if(!file_exists($dir.'/'.$diretorio.'/.htaccess')){ file_put_contents($dir.'/'.$diretorio.'/.htaccess',"#".PHP_EOL."#".PHP_EOL."#Exclua apenas se souber o que está fazendo.".PHP_EOL."#".PHP_EOL."#".PHP_EOL."RewriteEngine off");}
					echo '<div class="w1 folder_alert folder" data-folder="'.$dataPath.'">'.$diretorio."</div>".PHP_EOL;
					// echo '<div class="w1 folder_alert folder" data-new="/'.$dataPath.'" data-folder="'.str_replace(INCLUDE_PATH.'website',"",$dir.'/'.$diretorio).'">'.$diretorio."</div>".PHP_EOL;
					
					echo "<div class='w1 container'>".PHP_EOL;
					CriaPastas($dir.'/'.$diretorio."/",$oq);
					if($oq==1 || $oq==true) MostraFiles($dir.'/'.$diretorio."/");
					echo "</div>".PHP_EOL;
				};
			};
		};
	};
	function MostraFiles($dir){
		$dh = opendir($dir);
		while($arquivo = readdir($dh)){
			if($arquivo != '..' && $arquivo != '.' && !is_dir($dir.$arquivo)){
				$ext = explode('.',$arquivo);
				$ext = @$ext[1];
				if(	isset($ext)		&&($ext=="txt" 	||$ext=="htm" 	||$ext=="html" 	||$ext=="xhtml" 	||$ext=="xml" 	||$ext=="js"	 	||$ext=="json" 	||$ext=="php" 	||$ext=="css" 	||$ext=="less" 	||$ext=="sass" 	||$ext=="htaccess"||$ext=="key" 	||$ext=="asp" 	||$ext=="aspx" 	||$ext=="net" 	||$ext=="conf" 	||$ext=="ini" 	||$ext=="sql" 	||$ext=="as" 		||$ext=="htc" 	|| $ext=="--")){
					

					$dataNewPath = implode(_array_filter(explode('/',$dir.$arquivo)),"/");

					if(substr($dir,-1)!="/"){$dir=$dir.'/';}

					echo '	<div class="w1 file '.$ext.' multiplos" data-id="null" data-file="'.$dataNewPath.'">'.$arquivo."</div>".PHP_EOL;
				
				};
			};
		};
	};
	CriaPastas(INCLUDE_PATH.'website',true);
	MostraFiles(INCLUDE_PATH.'website');
?>
</div>

<div id="palco" 			class="palco_02 recolhido">
<div id="divEditor" 		class="recolhido"></div>
<div id="divListRight">
	<div class="aba">
		<div class="local"></div>
		<div class="remote"></div>
	</div>
	<div class="tick"></div>
	<div class="balao">
		<div class="title w1"></div>
		<div class="avatar"></div>
	</div>
	<div class="containerRight">
	<?
		if(refreshJsonPluginsList()){
			$json 	= json_decode(file_get_contents(ws::includePath.'admin/app/templates/json/ws-plugin-list.json'));
			foreach ($json as $value) {

				if($value->menu=='editor' || @in_array('editor', $value->menu)){
					echo '<div class="itemLI" data-miniature="'.ws::rootPath.$value->realPath.'/'.@$value->miniature.'" data-fullPath="'.ws::includePath.'website/'.$value->realPath.'">
							<div class="avatar"><img src="'.ws::rootPath.$value->realPath.'/'.$value->avatar.'"/></div>
							<div class="title w1">'.$value->pluginName.'</div>
							<div class="description w1">'.ws::limit_words($value->description,15,'...').'</div>
						 </div>';
				}
			}
		};
	?>

	</div>
</div>

<script type="text/javascript">
	$(window).unbind('keydown').bind('keydown', function(event) {
		if (event.ctrlKey || event.metaKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
				case 'à':
					event.preventDefault();
					$("#addToll").click();
					break;
				case '1':
					event.preventDefault();
					$("#plugin").click();
					break;
				case 's':
					event.preventDefault();
					$("#salvarArquivo").click();
					break;
				case 'o':
					event.preventDefault();
					$("#loadfile").click();
					break;
			}
		}
	});
	$(document).ready(function() {
		ws.preload.open({string:"Carregando API..."})
		$.getScript('<?=ws::rootPath?>admin/app/vendor/ace/src-min-noconflict/ace.js', function() {
			$.getScript('<?=ws::rootPath?>admin/app/vendor/ace/src-min-noconflict/ext-language_tools.js', function() {
					ws.preload.close();
					window.refreshFolders = function() {
						$.ajax({
							type: "POST",
							url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
							data: {'function': 'refreshFolders','var':0},
							beforeSend: function() {
								ws.preload.open();
							}
						}).done(function(data) {
							$("#nave_folders").html(data)
							window.refreshClick();
							ws.preload.close()
						})
					}			
					window.funcTabs = function() {
						$(".fileTabContainer .fileTab").unbind("tap click").bind("tap click", function() {
							$(".fileTab").removeClass("active");
							$(this).addClass("active");
							window.pathFile = $(this).attr("data-pathFile");
							window.loadFile = $(this).attr("data-loadFile");
							window.tokenFile = $(this).attr("data-token");
							window.htmEditor.setSession(window.listFilesWebmaster[window.tokenFile].session);
						})
						$(".fileTabContainer .fileTab .close").unbind("tap click").bind("tap click", function() {
							var aba = $(this);
							if ($(aba).parent().hasClass("unsave")) {
								confirma({
									conteudo: "Houve alterações neste arquivo, gostaria de salvar antes de fechar?",
									width: 500,
									posFn: function() {},
									Init: function() {},
									posClose: function() {},
									bot1: "Salvar e fechar",
									bot2: "Apenas fechar",
									drag: false,
									botclose: 1,
									newFun: function() {
										window.closeToSave = true;
										setTimeout(function() {
											$("#salvarArquivo").click();
											$("#Balao_ToolType").remove();
										}, 1000)
									},
									onCancel: function() {
										if (window.pathFile == $(aba).parent().attr("data-pathFile") && window.loadFile == $(aba).parent().attr("data-loadFile")) {
											delete window.listFilesWebmaster[$(aba).parent().attr("data-token")];
											$(aba).parent().remove();
											$("#Balao_ToolType").remove();
											window.resizeTab()
											if ($(".fileTabContainer .fileTab").length) {
												$(".fileTabContainer .fileTab:last-child").click();
											} else {
												window.pathFile = null;
												window.loadFile = null;
												window.htmEditor.setValue("");
											}
											window.resizeDesktop()
										} else {
											delete window.listFilesWebmaster[$(aba).parent().attr("data-token")];
											$(aba).parent().remove();
											$("#Balao_ToolType").remove()
											window.resizeTab()
											if (!$(".fileTabContainer .fileTab").length) {
												window.pathFile = null;
												window.loadFile = null;
												window.htmEditor.setValue("");
											}
											window.resizeDesktop();
										}
									},
									onClose: function() {},
									Callback: function() {},
									ErrorCheck: function() {},
									Check: function() {
										return true
									}
								})
							} else {
								if (window.pathFile == $(aba).parent().attr("data-pathFile") && window.loadFile == $(aba).parent().attr("data-loadFile")) {
									delete window.listFilesWebmaster[$(aba).parent().attr("data-token")];
									$(aba).parent().remove();
									$("#Balao_ToolType").remove()
									window.resizeTab()
									if ($(".fileTabContainer .fileTab").length) {
										$(".fileTabContainer .fileTab:last-child").click();
									} else {
										window.pathFile = null;
										window.loadFile = null;
										window.htmEditor.setValue("");
									}
									window.resizeDesktop();
								} else {
									delete window.listFilesWebmaster[$(aba).parent().attr("data-token")];
									$(aba).parent().remove();
									$("#Balao_ToolType").remove()
									window.resizeTab()
									if (!$(".fileTabContainer .fileTab").length) {
										window.pathFile = null;
										window.loadFile = null;
										window.htmEditor.setValue("");
									}
									window.resizeDesktop();
								}
							}
						})
					};
					window.addTab = function($newTokenFile, $pathFile, $loadFile, $saved) {
						out("<?=ws::includePath?>website  --  "+$pathFile+" - "+$loadFile+" - "+$saved);
						if (!$('.fileTabContainer .fileTab[data-full-path-file="' + $pathFile + $loadFile + '"]').length) {
							$(".tabSortable.fileTab.loader").remove();
							$(".fileTab").removeClass("active");

							$(".fileTabContainer .container").prepend('<div '+
								'legenda="' +$pathFile.replace("<?=ws::includePath.'website'?>", "")+'" '+ 
								'class="tabSortable fileTab active ' + $saved + '" '+
								'data-token="' + $newTokenFile + '" '+
								'data-saved="true" '+
								'data-full-path-file="' + $pathFile.replace($loadFile,"")+$loadFile+'" '+
								'data-pathFile="' + $pathFile.replace($loadFile,"") + '" '+
								'data-loadFile="' + $loadFile + '"> '+
								'<div class="str">' + 
									$pathFile.replace("<?=ws::includePath?>website", "")+
								'</div><div class="close"></div><div class="tab_shadow"></div><textarea style="display:none"></textarea> </div>');
							if ($(".fileTabContainer .container.ui-sortable").length) {
								$(".fileTabContainer .container").sortable("destroy");
							}
							$(".fileTabContainer .container").sortable({
								axis: "x",
								start: function(e, ui) {
								//	out($(ui.placeholder))
								},
								beforeStop: function(e, ui) {
								//	out($(ui.placeholder))
								}
							});
							$(".fileTabContainer .container").disableSelection();
						};
						window.funcTabs();
						window.resizeTab();
					}
					window.resizeTab = function() {
						var qtdd = $(".fileTabContainer .container .fileTab").length;
						var W_Aba = $(".fileTabContainer .fileTab").outerWidth();
						var W_Container = $(".fileTabContainer").width();
						var total_W_Abas = W_Aba * qtdd;
						$('.fileTabContainer .fileTab').attr("style", "width:calc((100% / " + qtdd + ") - " + (47) + "px)");
						$("*[legenda]").LegendaOver();
					}
					window.resizeDesktop = function() {
						window.htmEditor.resize();
					}
					ace.config.set('basePath', '<?=ws::rootPath?>admin/app/vendor/ace/src-min-noconflict');// SETA LOCAL DOS ARQUIVOS DO EDITOR
					window.htmEditor = ace.edit("divEditor");

					window.htmEditor.setTheme("ace/theme/monokai");
					// window.htmEditor.setTheme("ace/theme/websheep.0.3");
					window.htmEditor.getSession().setMode("ace/mode/php");








					window.htmEditor.setShowPrintMargin(true); // mostra linha ativa atual
					window.htmEditor.setHighlightActiveLine(true); // mostra linha ativa atual
					window.htmEditor.setShowInvisibles(0); // frufru de tabulações
					window.htmEditor.getSession().setUseSoftTabs(false); // usar tabs ao invez de espaço
					//	window.htmEditor.setOptions({maxLines: Infinity,minLines: 1});
					window.htmEditor.setDisplayIndentGuides(true);
					window.htmEditor.getSession().setUseWrapMode(true);
					setTimeout(function() {




						var wsCompleter = {
							getCompletions: function(editor, session, pos, prefix, callback) {
								if (prefix.length === 0) {
									callback(null, []);
									return
								}
								$.getJSON("<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/autoComplete.json", function(wordList) {
									callback(null, wordList.map(function(ea) {
										return {
											name: ea.value,
											value: ea.value,
											meta: ea.meta
										}
									}));
								})
							}
						}
						var langTools = ace.require("ace/ext/language_tools");
						langTools.setCompleters([langTools.snippetCompleter, langTools.textCompleter, wsCompleter])
						window.htmEditor.setOptions({
							enableBasicAutocompletion: false,
							enableSnippets: true,
							enableLiveAutocompletion: true
						});
						$('.chosen-results,.nave_folders').perfectScrollbar({suppressScrollX: true});
						window.htmEditor.getSession().getUndoManager().reset();

						window.htmEditor.initDestaqueWsTags = function(){
							$('.ace_line:not(.ws-tags):has(.ace_meta.ace_tag.ace_tag-name.ace_xml:contains("ws-"))').addClass('ws-tags');
							$('.ace_line:not(.ws-tags):has(.ace_php_tag:contains("ws-"))').addClass('ws-tags');
							$('.ace_line:not(.ws-colunm-tag):has(.ace_comment.ace_xml:contains("{{"))').addClass('ws-colunm-tag');
							// $('#divEditor span:contains("{{")').css({"color":"#000"})
						}
					

						$(".ace_scrollbar.ace_scrollbar-v").scroll(function(){ window.htmEditor.initDestaqueWsTags();})
						$( "#divEditor" ).mousemove(function( event ) {window.htmEditor.initDestaqueWsTags();})

						window.htmEditor.on('change', function() {	
							setTimeout(function(){window.htmEditor.initDestaqueWsTags()},20)
							window.htmEditor.resize();
							$("#Balao_TollType").remove();

							if ($(document.activeElement).closest("div").attr("id") == "divEditor") {
								if (Object.keys(window.listFilesWebmaster).length) {
									window.listFilesWebmaster[window.tokenFile].saved = 'unsave';
								}
								$('.fileTab[data-full-path-file="' + window.pathFile + window.loadFile + '"]').removeClass('saved').addClass('unsave');
							}
						})
						if (!window.listFilesWebmaster) {
							window.listFilesWebmaster = Object();
						} else {
							$.each(window.listFilesWebmaster, function(index) {
								window.addTab(window.listFilesWebmaster[index].newTokenFile, window.listFilesWebmaster[index].pathFile, window.listFilesWebmaster[index].file, window.listFilesWebmaster[index].saved)
							});
							$('.fileTabContainer .fileTab.active').click();
						}
						<?
						// CASO TENHA ALGUM PATH DE ARQUIVO PARA ABRIR "LOAD" 
						if (isset($_GET['get_file']) && $_GET['get_file'] != ""): ?> 
							setTimeout(function() {
								var openDirectFile = "<?=ws::includePath.'website/'.$_GET['get_file']?>";
								var VerifyOptExist = $(".file[data-file='"+openDirectFile+"']").length;
								if(VerifyOptExist>0){ 
									$(".file[data-file='"+openDirectFile+"']").click();
								}else{ 
									TopAlert({mensagem: "Este arquivo não existe",type: 2});
								}
							},500); 
						<? endif; ?>
					}, 1000);
					$("#Balao_TollType").remove();
					sanfona('.folder');
					$('.folder').click(function() {
						setTimeout(function() {
							$('.nave_folders,.chosen-results').perfectScrollbar('update');
						}, 500)
					})


					$( ".containerRight .itemLI" ).unbind("mouseenter").mouseenter(function() {
						var miniature = $(this).data("miniature");
						var title = $(this).find(".title").html();
						$("#divListRight .balao .avatar").html('<img src="'+miniature+'"/>');
						$("#divListRight .balao .title").html(title);

						$("#divListRight .tick" ).css({top:( $(this).offset().top - (115 - ( $(this).height()/2 )))}).show();
						if(document.mouse_y <= ($("#divListRight").position().top + 150) ){
							$("#divListRight .balao" ).css({top:0}).show();
						}else if(document.mouse_y >= ($("#divListRight").position().top+$("#divListRight").height() - 150)){
							$("#divListRight .balao" ).css({top:(($("#divListRight").position().top+$("#divListRight").height() - 300))}).show();
						}else{
							$("#divListRight .balao" ).css({top:(document.mouse_y - 200)}).show();
						}
					}).unbind("click tap press").bind("click tap press",function(){
						var FullPath = $(this).data('fullpath');
						ws.preload.open()
						$("#palco").removeClass("listRight");
						$.ajax({
							type: "POST",
							url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
							data: {
								'function': 'getShortCodesPlugin',
								'path': FullPath
							}
						}).done(function(data) {
							window.htmEditor.insert(data)
							ws.preload.close()
						});
					})
					$( ".containerRight" ).unbind("mouseleave").mouseleave(function() {
						$("#divListRight .balao, #divListRight .tick" ).hide();
					});



					$("*[legenda]").LegendaOver();
					$("#bkpsFile").chosen({
						disable_search_threshold: 0
					}).change(function(e) {
						var token = $('#bkpsFile').chosen().val();
						if (token != "") {
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {'function': 'loadFileBKP', token:token,pathFile:window.pathFile.replace(window.loadFile, "/") ,filename: window.loadFile},
								beforeSend: function() {
									ws.preload.open();
								}
							}).done(function(data) {
								ws.preload.close()
								eval(data);
							})
						}
					});
					//####################################################################################  ALTERANDO O MODO E O ESTILO DO EDITOR
					$('#mode').chosen({
						disable_search_threshold: 5
					}).change(function(e) {
						e.preventDefault();
						var theme = $('#mode').chosen().val();
						window.htmEditor.setMode("ace/mode/" + theme);
					}).find("option[value='php']").attr("selected", "true").trigger("chosen:updated");
					setTimeout(function() {
							$('#sintaxy').unbind('tap click hover').bind('tap click', function() {
								$("#mode_chosen").addClass('chosen-container-active').addClass('chosen-with-drop');
								setTimeout(function() {$("#mode_chosen").find('.chosen-drop').mousedown();}, 500);
							})
							$('#version').unbind('tap click hover').bind('tap click', function() {
								$("#bkpsFile_chosen").addClass('chosen-container-active').addClass('chosen-with-drop');
								setTimeout(function() {
									$("#bkpsFile_chosen").find('.chosen-drop').mousedown();
								}, 500);
							})
						}, 1000)
						//####################################################################################  se alterar, precisa mudar no functiuons tb na função de checkin
					window.refreshClick = function() {
						$("#Balao_TollType").remove();
						$('.nave_folders,.chosen-results').perfectScrollbar('update');
						$(".container .new").unbind('tap press click').bind('tap press click', function() {
							$('#novoArquivo').click();
						})
						$('#addPagination').unbind('tap press click').bind('tap press click', function() {
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {'function': 'InsertPagination'},
								beforeSend: function() {
									ws.preload.open()
								}
							}).done(function(data) {
									ws.preload.close()
									confirma({
										conteudo: data,
										width: 700,
										height: 500,
										bot1: 'Inserir Código',
										bot2: 'Cancelar',
										drag: 0,
										botclose: 0,
										Check: function() {
											// if( $("#formTags input[name='typeCode'][type='radio']:checked").length==0 || $("#formTags input[type='radio']:checked").length==0 || $("#shortcodes").val() == "") {
											// 	return false;
											// } else {
											// 	return true;
											// }
												return true;
										},
										ErrorCheck: function() {
												ws.confirm({
													conteudo:'<span style="color:#cd3333">Preencha tipo e ferramenta desejados</span>',
													width: 500,
													height: 'auto',
													posFn: function() {},
													Init: function() {},
													posClose: function() {},
													bots: [
														{
																id			: "aceitar",
																label		: "Ok",
																class		: "",
																style 		: "",
																css 		: {"color":"#FFF","backgroundColor":"#cd3333"},
																ErrorCheck	: function() {},
																Check 		: function() {return true},
																action		: function(){},
														}
													],
													idModal: "ws_error_confirm",
													divScroll: "body",
													divBlur: "#menu_tools,#container,#header",
												})
										},
										posFn: function() {
											window.htmEditorPagination = ace.edit("editorHTML");
											window.htmEditorPagination.setTheme("ace/theme/websheep.0.3");
											window.htmEditorPagination.getSession().setMode("ace/mode/php");
											window.htmEditorPagination.setHighlightActiveLine(true);
											window.htmEditorPagination.setShowInvisibles(0);
											window.htmEditorPagination.getSession().setUseSoftTabs(false);
											window.htmEditorPagination.getSession().setUseWrapMode(true);
											window.htmEditorPagination.setOptions({maxLines: Infinity,minLines: 1});
											$(".chosen-results,.nave_folders").perfectScrollbar({suppressScrollX: true });
											window.htmEditorPagination.getSession().on('change', function(e) {
												$("textarea[name='editorHTML']").val(window.htmEditorPagination.getSession().getValue())
											})
											window.htmEditorPagination.getSession().setValue(decodeURIComponent($("textarea[name='editorHTML']").val()))
											window.htmEditorCountPage = ace.edit("editorCOUNT");
											window.htmEditorCountPage.setTheme("ace/theme/websheep.0.3");
											window.htmEditorCountPage.getSession().setMode("ace/mode/php");
											window.htmEditorCountPage.setHighlightActiveLine(true);
											window.htmEditorCountPage.setShowInvisibles(0);
											window.htmEditorCountPage.getSession().setUseWrapMode(true);
											window.htmEditorCountPage.getSession().setUseSoftTabs(false);
											$(".chosen-results,.nave_folders").perfectScrollbar({suppressScrollX: true });
											window.htmEditorCountPage.getSession().on('change', function(e) {
												$("textarea[name='editorCOUNT']").val(window.htmEditorCountPage.getSession().getValue())
											})
											window.htmEditorCountPage.getSession().setValue(decodeURIComponent($("textarea[name='editorCOUNT']").val()))
											window.htmEditorCountPage.setOptions({minLines:1});
											window.htmEditorCountPageActive = ace.edit("editorCOUNTactive");
											window.htmEditorCountPageActive.setTheme("ace/theme/websheep.0.3");
											window.htmEditorCountPageActive.getSession().setMode("ace/mode/php");
											window.htmEditorCountPageActive.setHighlightActiveLine(true);
											window.htmEditorCountPageActive.setShowInvisibles(0);
											window.htmEditorCountPageActive.getSession().setUseWrapMode(true);
											window.htmEditorCountPageActive.getSession().setUseSoftTabs(false);
											$(".chosen-results,.nave_folders").perfectScrollbar({suppressScrollX: true });
											window.htmEditorCountPageActive.getSession().on('change', function(e) {
												$("textarea[name='editorCOUNTactive']").val(window.htmEditorCountPageActive.getSession().getValue())
											})
											window.htmEditorCountPageActive.getSession().setValue(decodeURIComponent($("textarea[name='editorCOUNTactive']").val()))
											window.htmEditorCountPageActive.setOptions({
												maxLines: Infinity,
												minLines: 1
											});
										},
										newFun: function() {
											$.ajax({
												type: "POST",
												url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
												data: {'function': 'InsertPaginationCampos',form:$("#formTags").serialize()},
												beforeSend: function() {
													ws.preload.open()
												}
											}).done(function(data) {
													ws.preload.close()
													window.htmEditor.insert(data)
													$("#ws_confirm").remove();
													$("#body").removeClass("scrollhidden");
													$("*").removeClass("blur");

											})
										}
									})
							})
						})
						$('#formatHTML').unbind('tap press click').bind('tap press click', function() {
							confirma({
								conteudo: '<iframe id="bootstrap" src="<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/formatter/index.php" width="100%" height="100%" scrolling="no"></iframe>',
								width: 600,
								height:370,
								bot1: 'Formatar',
								bot2: 'Cancelar',
								drag: 0,
								botclose: 0,
								posFn: function() {},
								newFun: function() {
										$('#bootstrap')[0].contentWindow.beautify();
								}
							})

							//window.htmEditor.setValue(ws.string.formatHTML(window.htmEditor.getValue()));
						})
						$('#templateBootstrap').unbind('tap press click').bind('tap press click', function() {
							confirma({
								conteudo: '<iframe id="bootstrap" src="<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/templateBootstrap/index.php" width="100%" height="100%"></iframe>',
								width: 'calc(100% - 100px)',
								height: 'calc(100% - 100px)',
								bot1: 'Inserir',
								bot2: 'Cancelar',
								drag: 0,
								botclose: 0,
								posFn: function() {},
								newFun: function() {
									var $less = $("#bootstrap").contents().find(".html .output_container pre.mixins").text();
									var $code = $("#bootstrap").contents().find(".html .output_container pre.markup").text();
									var $insert = "<!--  LESS MIXINS --> \n " + $less + " \n <!--  END LESS MIXINS --> \n " + $code
									if ($less != "") {
										window.htmEditor.insert($insert);
									} else {
										window.htmEditor.insert($code);
									}
									$("#ws_confirm").remove();
									$("#body").removeClass("scrollhidden");
									$("*").removeClass("blur");
								}
							})
						})
						$('#addSendForm').unbind('tap press click').bind('tap press click', function() {
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {'function': 'InsertCodeForm'},
								beforeSend: function() {
									ws.preload.open();
								}
							}).done(function(data) {
									ws.preload.close()
									confirma({
										conteudo: data,
										width: 600,
										bot1: 'Inserir formulário',
										bot2: 'Cancelar',
										drag: 0,
										botclose: 0,
										Check: function() {
											// if (!$("#formTags input[type='radio']:checked").val() || $("#shortcodes").val() == "") {
											// 	return false;
											// } else {
											// 	return true;
											// }
												return true;
										},
										ErrorCheck: function() {
											TopAlert({
												mensagem: "Preencha tipo de envio desejado",
												type: 2
											});
										},
										posFn: function() {},
										newFun: function() {
											$.ajax({
												type: "POST",
												url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
												data: {'function': 'InsertCodeFormCampos',form:$("#formTags").serialize()},
												beforeSend: function() {
													ws.preload.open()
												}
											}).done(function(data) {
												ws.preload.close()
												window.htmEditor.insert(data)
												$("#ws_confirm").remove();
												$("#body").removeClass("scrollhidden");
												$("*").removeClass("blur");
											})
										}
									})
							})
						})
						$('#addToll').unbind('tap press click').bind('tap press click', function() {
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {'function': 'InsertCode'},
								beforeSend: function() {
									ws.preload.open();
								}
							}).done(function(data) {
									ws.preload.close()
									confirma({
										conteudo: data,
										width: 600,
										bot1: 'Inserir Código',
										bot2: 'Cancelar',
										drag: 0,
										botclose: 0,
										Check: function() {
											// if( $("#formTags input[name='typeCode'][type='radio']:checked").length==0 || $("#formTags input[type='radio']:checked").length==0 || $("#shortcodes").val() == "") {
											// 	return false;
											// } else {
											// 	return true;
											// }
											return true;
										},
										ErrorCheck: function() {
														ws.confirm({
															conteudo:'<span style="color:#cd3333">Preencha tipo e ferramenta desejados</span>',
															width: 500,
															height: 'auto',
															posFn: function() {},
															Init: function() {},
															posClose: function() {},
															bots: [
																{
																		id			: "aceitar",
																		label		: "Ok",
																		class		: "",
																		style 		: "",
																		css 		: {"color":"#FFF","backgroundColor":"#cd3333"},
																		ErrorCheck	: function() {},
																		Check 		: function() {return true},
																		action		: function(){},
																}
															],
															idModal: "ws_error_confirm",
															divScroll: "body",
															divBlur: "#menu_tools,#container,#header",
														})


										},
										posFn: function() {},
										newFun: function() {
											$.ajax({
												type: "POST",
												url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
												data: {'function': 'InsertCodeCampos',form:$("#formTags").serialize()},
												beforeSend: function() {
													ws.preload.open()
												}
											}).done(function(data) {
												ws.preload.close()
												window.htmEditor.insert(data)
												$("#ws_confirm").remove();
												$("#body").removeClass("scrollhidden");
												$("*").removeClass("blur");
												



											})
										}
									})
							});
						})
						$('#plugin').unbind('tap press click').bind('tap press click', function() {
							if ($("#palco").hasClass('listRight')) {
								$("#palco, #divEditor").removeClass("listRight")
							} else {
								$("#palco, #divEditor").addClass("listRight")
							}
						})

						$('#loadfile').unbind('tap click').bind('tap click', function() {
							if ($("#palco").hasClass('recolhido')) {
								$("#palco, #divEditor").removeClass("recolhido");
								$("#palco, #divEditor").addClass("expandido");
							} else {
								$("#palco, #divEditor").addClass("recolhido");
								$("#palco, #divEditor").removeClass("expandido");
							}
							if (!$(".nave_folders").hasClass('recolhido')) {
								$(".nave_folders").addClass("recolhido")
							} else {
								$(".nave_folders").removeClass("recolhido")
							}
							if (!$(".nave_menu").hasClass('recolhido')) {
								$(".nave_menu").addClass("recolhido")
							} else {
								$(".nave_menu").removeClass("recolhido")
							}
						})
						$('#novoArquivo').unbind('tap click').bind('tap click', function() {
							ws.preload.open({string:"Aguarde..."})

							window.pathFile = $(this).data("file");
							window.id_file_open = $(this).data("id");
				
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {
									'function': 'ListFolderNewFile',
									'pathFile':0
								},
								beforeSend: function() {
									ws.preload.open();
								}
							}).done(function(data) {
								ws.preload.close();
								confirma({
									conteudo: data,
									width: 500,
									bot1: "Criar arquivo",
									bot2: "Cancelar",
									drag: false,
									newFun: function() {
													var newFile = $("input.path").val();
													$.ajax({
														type: "POST",
														url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
														data: {
															'function': 'createFile',
															'newFile':newFile
														},
														beforeSend: function() {
															ws.preload.open()
														}
													}).done(function(data) {
														if (data != "falha") {
															eval(data)
															window.refreshFolders();
														}
													});
									},
									posFn:function(){
										sanfona('.folder_alert');
										$(".nave_folders .folder_alert").bind("click tap press",function(){
											$(".nave_folders .folder_alert").css({"background-color":"transparent",color:"#9e9e9e","font-weight":500}).removeClass("selectExclude");
											$(this).css({"background-color":"#d4e1f4",color:"#497bbe","font-weight":600}).addClass("selectExclude")
											$(".ws_confirm_conteudo .inputText.path").val($(this).data("folder"))
										})
									}
								})
							});
						});
						$('#novodir').unbind('tap click').bind('tap click', function() {
							window.pathFile = $(this).data("file");
							window.id_file_open = $(this).data("id");
							
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {'function': 'ListFolderNewFolder','pathFile':0},
								beforeSend: function() {
									ws.preload.open()
								}
							}).done(function(data) {							
									ws.preload.close()
									confirma({
										conteudo: data,
										width: 500,
										bot1: "Criar arquivo",
										bot2: "Cancelar",
										drag: false,
										posFn:function() {
												$(".nave_folders .folder").click(function(){
													$(".ws_confirm_conteudo.w1 .inputText.path").val($(this).data("folder"))
												})
										},
										newFun: function() {
											var newFile = $("input.path").val();
											$.ajax({
												type: "POST",
												url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
												data: {'function': 'createFolder','newFile':newFile},
												beforeSend: function() {
													ws.preload.open()
												}
											}).done(function(data) {
													ws.preload.close()
												if (data == "sucesso") {
													window.refreshFolders();
													TopAlert({mensagem: "Diretório criado com sucesso!",type: 3});
												}else{
													TopAlert({mensagem:data,type: 2});
												}
											});
										},
										onCancel: function() {}
									})
							});
						});
						$('#exclFile').unbind('tap click').bind('tap click', function() {
							confirma({
								conteudo: "Você tem <b>CERTEZA</b> de que gostaria de excluir esse arquivo?<br><div class='bg08' style='position: relative;margin: 10px;padding: 20px;color: #F00;'>1 • Todos os BKPs deste arquivo também serão apagados.<br>2 • E lembre-se, esta ação <b>NÃO</b> terá mais volta.</div>",
								width: 500,
								bot1: "Sim tenho certeza",
								bot2: "Cancelar",
								drag: false,
								newFun: function() {
									$.ajax({
										type: "POST",
										url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
										data: {
											'function': 'exclui_file',
											'pathFile': window.pathFile,
											'loadFile':window.loadFile
										},
										beforeSend: function() {
											ws.preload.open()	
										}
									}).done(function(data) {
										out(data)
										 eval(data);
										 window.refreshFolders();
										 ws.preload.close()
									});
								}
							})
						});
						$('#exclFolder').unbind('tap click').bind('tap click', function() {
							
							ws.preload.open();
							window.pathFile 	= $(this).data("file");
							window.id_file_open = $(this).data("id");

							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								data: {
									'function': 'ListFolderExclFolder',
									'_excl_dir_': 0
								},
								beforeSend: function() {
									ws.preload.open()
								}
							}).done(function(data) {
									ws.preload.close()
									confirma({
										conteudo: data,
										width: 500,
										bot1: "Excluir folder",
										bot2: "Cancelar",
										drag: false,
										newFun: function() {
											var excluiFolder = $('.selectExclude').data('folder');
											setTimeout(function() {
												confirma({
													conteudo: "Você tem <b>CERTEZA</b> de que quer excluir o diretório: <br> <b>"+excluiFolder+"?<br><div class=\"bg08\" style=\"padding: 10px 60px; margin: 10px; color: #D80000;\">Lembre-se, esta ação <b>NÃO</b> terá mais volta.</div>",
													width: 500,
													bot1: "Sim tenho certeza",
													bot2: "Cancelar",
													drag: false,
													newFun: function() {
															$.ajax({
																type: "POST",
																url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
																data: {
																	'function': '_excl_dir_',
																	'exclFolder': excluiFolder
																},
																beforeSend: function() {
																	ws.preload.open()
																}
															}).done(function(data) {
																ws.preload.close()
																if(data==1){
																	window.refreshFolders();
																	TopAlert({mensagem: "Diretório excluído com sucesso!",type:3});
																}else{
																	TopAlert({mensagem:data,type:2});
																}
															})
													},
													posFn:function() {}
												})
											}, 500)
										},
										posFn:function(){
											sanfona('.folder_alert');
											$(".nave_folders .folder_alert").bind("click tap press",function(){
												$(".nave_folders .folder_alert").css({"background-color":"transparent",color:"#9e9e9e","font-weight":500}).removeClass("selectExclude");
												$(this).css({"background-color":"#d4e1f4",color:"#497bbe","font-weight":600}).addClass("selectExclude")
												$(".nave_folders .container").css({"background-color":"transparent",color:"#9e9e9e","font-weight":500});
												$(this).next().css({"background-color":"#d4e1f4",color:"#497bbe","font-weight":600})												
											})
										}
									})
							});
						});
						$('.nave_folders .file').unbind('tap click').bind('tap click', function() {
							window.pathFile = $(this).data("file").split("\\").join("/");
							$("#exclFile").show();
							//$("#palco, #divEditor,.nave_folders,.nave_menu").addClass("recolhido")
							$("#loadfile").click();
							if (!$('.fileTabContainer .fileTab[data-full-path-file="' + window.pathFile + '"]').length) {
									$.ajax({
										type: "POST",
										url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
										data: {
											'function': 'loadFile',
											'pathFile': window.pathFile
										},
										beforeSend: function() {
											ws.preload.open();
										}
									}).done(function(data) {
											eval(data);
											ws.preload.close();

									});
							} else {
								$('.fileTabContainer .fileTab[data-full-path-file="' + window.pathFile + '"]').click();
							}
						});
						$('#maximize').unbind('tap click').bind('tap click', function() {
							if($("#container").hasClass("fullscreen")){
								$("#container").removeClass("fullscreen")
						    if (document.cancelFullScreen) {  
						      document.cancelFullScreen();  
						    } else if (document.mozCancelFullScreen) {  
						      document.mozCancelFullScreen();  
						    } else if (document.webkitCancelFullScreen) {  
						      document.webkitCancelFullScreen();  
						    }  
							}else{
								$("#container").addClass("fullscreen")
								if ((document.fullScreenElement && document.fullScreenElement !== null) || (!document.mozFullScreen && !document.webkitIsFullScreen)) {
									if (document.documentElement.requestFullScreen) {  
										document.documentElement.requestFullScreen();  
									} else if (document.documentElement.mozRequestFullScreen) {  
										document.documentElement.mozRequestFullScreen();  
									} else if (document.documentElement.webkitRequestFullScreen) {  
										document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
									}  
								} 
							}
						})

						if(window.location.pathname.split('/').splice(-2).join("/")=="popup/code-editor"){
							$('#popup').hide();
						}else{
							$('#popup').unbind('tap click').bind('tap click', function() {
								if(!ws.exists.dom(".fileTab.unsave")){
									$(".fileTab .close").click();
									var w 			= (screen.width/2);
									var h 			= (screen.height/2);
									ws.popup("./popup/code-editor", w, h, 'yes' );
									document.location.reload(true);
								}else{
									confirma({
										conteudo: "Houve alterações em um ou mais arquivos, salve antes de continuar",
										width: 500,
										posFn: function() {},
										Init: function() {},
										posClose: function() {},
										bot1: false,
										bot2: false,
										drag: false,
										botclose: 1,
										newFun: function() {}
									})
								}
							})	
						}

						$('#salvarArquivo').unbind('tap click').bind('tap click', function() {
							var ConteudoDoc = encodeURIComponent(window.htmEditor.getValue())
							var GET = "pathFile=" + window.pathFile.replace(window.loadFile,"") + "&filename=" + window.loadFile + "&token=" + window.newTokenFile + "&ConteudoDoc=" + ConteudoDoc;
							$.ajax({
								type: "POST",
								url: "<?=ws::rootPath?>admin/app/ws-modules/ws-webmaster/functions.php",
								beforeSend:function(){
										ws.preload.open({string:"Salvando..."})
								},
								data: {
									'function'	: 'geraBKPeAplica',
									'bkp'		: 'false',
									'GET'		:  GET,
								}
							}).done(function(e) {
									out(e);
									ws.preload.close()
									if (e == "sucesso") {
										window.listFilesWebmaster[window.newTokenFile].saved = 'saved';
										$('.fileTab[data-full-path-file="' + window.pathFile + window.loadFile + '"]').removeClass('unsave').addClass('saved');
										if (window.closeToSave == true) {
											window.closeToSave = false;
											$('.fileTab[data-full-path-file="' + window.pathFile+window.loadFile + '"] .close').click();
										}
										TopAlert({
											mensagem: "Arquivo atualizado com sucesso!",
											type: 3
										});
									};
							});
						});
						$(window).resize(function() {
							window.htmEditor.resize();
							$('.nave_folders,.chosen-results').perfectScrollbar('update');
						});
						$('#container').perfectScrollbar("destroy");
					}
					window.refreshClick();
					//#################################################################################################################################################
			});
		});
	})
</script>
</div>
<div class="c"></div>
</div>
