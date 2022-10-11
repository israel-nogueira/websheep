$(document).ready(function() {
	//#####################################################################
	// AJUSTA A LARGURA DAS ABAS 
	//#####################################################################
	window.resizeAbas = function() {
		var w_editor = $("#webmaster_abas").width();
		var fator = $("#webmaster_abas .aba").length;
		var padding = 31
		$("#webmaster_abas .aba").width(((w_editor / fator) - padding))
	}
	//###################################################################
	// AJUSTA TUDO QUANDO REDIMENCIONA O MENU ESQUERDO 
	//###################################################################
	window.resizeEditor = function() {
		widthLMenu = $("#webmaster_menu_left").width();
		var w_rmenu = $("#webmaster_menu_right").width();
		var h_rmenu = $("#webmaster_menu_right").height();
		var h_rmenuBody = $("#webmaster_menu_right .containerScroll").height();
		if (h_rmenuBody > h_rmenu) {
			$("#webmaster_container").css({
				"width": "calc(100% - " + (widthLMenu + 90) + "px)"
			})
		} else {
			$("#webmaster_container").css({
				"width": "calc(100% - " + (widthLMenu + 70) + "px)"
			})
		}
		setTimeout(window.resizeAbas(), 400);
	}
	//###################################################################
	// AJUSTA TUDO QUANDO REDIMENCIONA O MENU DIREITO 
	//###################################################################
	window.resizeRighttMenu = function(widthRMenu,func) {
		if (widthRMenu < 91) {
			$("#webmaster_menu_right").addClass("closed")
			$("#webmaster_menu_right").removeClass("opened")
		} else {
			$("#webmaster_menu_right").removeClass("closed")
			$("#webmaster_menu_right").addClass("opened")
		}

		$("#webmaster_menu_right_body").html('');
		$("#webmaster_menu_right").stop().animate({
			width: (widthRMenu - 6)
		}, 1000, 'easeOutBounce', function() {
			if (widthRMenu < 91) {
				$('#webmaster_menu_right .close').hide('slow');
			}else{
				if(typeof func=="function"){
					func();
				}
				$('#webmaster_menu_right .close').show('slow');

			}
			window.resizeEditor();
		});
		setTimeout(window.resizeAbas(), 600)
	}
	//###################################################################
	// FECHA O MENU DIREITO 
	//###################################################################
	window.closeRighttMenu = function() {
		var h_rmenu = $("#webmaster_menu_right").height();
		var h_rmenuBody = $("#webmaster_menu_right .containerScroll").height();
		if (h_rmenuBody > h_rmenu) {
			window.resizeRighttMenu(90,null)
		} else {
			window.resizeRighttMenu(70,null)
		}
	}
	//###################################################################
	//################################################################### EXECUÇÕES
	//###################################################################

	$("#webmaster_menu_right .close").unbind('click tap press').bind('tap click press', function() {
		window.closeRighttMenu()
	})
	//###################################################################
	//  BOTÃO DE SALVAR ARQUIVO ATUAL
	//###################################################################
	$("#saveFile").unbind("click tap press").bind("click tap press", function(e) {
		e.preventDefault();
		if(window.ws_webmaster.totalSess()>0){
			window.ws_webmaster.set.engine(window.htmEditor);
			var token 		= window.ws_webmaster.atualSession.token;
			var pathFile 	= window.ws_webmaster.atualSession.pathFile;
			var content 	= window.ws_webmaster.content();
			window.salva_arquivo(token, pathFile, content);
		}else{
			ws.alert.top({
				mensagem: "Não existe arquivos abertos ou sessões iniciadas no WebSheep.",
				timer: 3000,
				color: "#FFF",
				background: "#c73131",
				bottomColor: "#c73131",
				styleText: "color:#FFF"
			});
		}
	})
	//###################################################################
	//  DELETANDO ARQUIVO ATUAL
	//###################################################################
	$("#deleteFile").unbind("click tap press").bind("click tap press", function(e) {
		e.preventDefault();
		if(window.ws_webmaster.totalSess()>0){
			window.ws_webmaster.set.engine(window.htmEditor);
			window.deleta_arquivo(window.ws_webmaster.atualSession.pathFile);
		}else{
			ws.alert.top({
				mensagem: "Não existe arquivos abertos ou sessões iniciadas no WebSheep.",
				timer: 3000,
				color: "#FFF",
				background: "#c73131",
				bottomColor: "#c73131",
				styleText: "color:#FFF"
			});
		}
	})
	//###################################################################
	// AJUSTA TUDO QUANDO REDIMENCIONA A JANELA 
	//###################################################################
	$(window).unbind('resize').bind('resize', function() {
		var h_rmenu = $("#webmaster_menu_right").height();
		var h_rmenuBody = $("#webmaster_menu_right .containerScroll").height();
		if (h_rmenuBody > h_rmenu) {
			$("#webmaster_menu_right").addClass('paddingScroll');
		} else {
			$("#webmaster_menu_right").removeClass('paddingScroll');
		}
		if ($("#webmaster_menu_right").hasClass('closed')) {
			if (h_rmenuBody > h_rmenu) {
				window.resizeRighttMenu(90,null);
			} else {
				window.resizeRighttMenu(70,null);
			}
		}
		$('.scrollbar,#webmaster_menu_right_icon').perfectScrollbar("update")
		setTimeout(window.resizeAbas(), 1000)
	})
	//###################################################################
	// ATIVA O SCROLL BAR
	//###################################################################
	$('.scrollbar,#webmaster_menu_right_icon').perfectScrollbar({
		suppressScrollX: true
	});
	// $('#webmaster_menu_right_icon').perfectScrollbar({
	// 	suppressScrollX: true,
	// 	handlers: 'drag-thumb',
	// });
	//###################################################################
	// HABILITA O RESIZE DO MENU ESQUERDO
	//###################################################################
	$("#webmaster_menu_left").resizable({
		stop: function(event, ui) {
			window.resizeEditor()
		},
		resize: function(event, ui) {
			widthMenu = ui.size.width;
		},
		helper: "ui-resizable-helper",
		maxWidth: 500,
		minWidth: 100,
		animate: false,
		animateDuration: 200,
		animateEasing: "linear"
	});
	//###################################################################
	// DRAG AND DROP DAS ABAS
	//###################################################################
	$("#webmaster_abas").sortable({
		axis: "x",
		containment: "parent",
		start: function(event, ui) {},
		stop: function(event, ui) {
			window.resizeAbas();
		},
	})
	//###################################################################
	// INICIANDO ACE EDITOR 
	//###################################################################
	$.getScript('./app/vendor/ace/src-min-noconflict/ace.js', function() {
		$.getScript('./app/vendor/ace/src-min-noconflict/ext-language_tools.js', function() {
			ace.config.set('basePath', './app/vendor/ace/src-min-noconflict'); // SETA LOCAL DOS ARQUIVOS DO EDITOR
			window.htmEditor = ace.edit("webmaster_editor");
			window.htmEditor.setTheme("ace/theme/monokai");
			window.htmEditor.getSession().setMode("ace/mode/html");
			window.htmEditor.setShowPrintMargin(true); // mostra linha ativa atual
			window.htmEditor.setHighlightActiveLine(true); // mostra linha ativa atual
			window.htmEditor.setShowInvisibles(0); // frufru de tabulações
			window.htmEditor.getSession().setUseSoftTabs(false); // usar tabs ao invez de espaço
			window.htmEditor.setDisplayIndentGuides(true);
			window.htmEditor.getSession().setUseWrapMode(true);
		//	window.htmEditor.setOptions({maxLines: window.htmEditor.session.getLength()});
		})
	})
	//###################################################################
	//  FUNÇÃO QUE APLICA AS FUNÇÕES DE CLICK DAS ABAS
	//###################################################################
	window.functionAbas = function() {
		$('#webmaster_abas .aba').unbind("click tap press").bind("click tap press", function() {
			if ($(this).is('.ui-draggable-dragging')) {
				return;
			}
			window.ws_webmaster.set.engine(window.htmEditor)
			var token = $(this).data('token')
			window.ws_webmaster.apply.session(token);
			$('#webmaster_abas .aba').removeClass('active')
			$(this).addClass('active');
		})
		$("#webmaster_abas .aba i.icon-close").unbind("click tap press").bind("click tap press", function(e) {
			e.preventDefault();
			window.closeAba($(this).parent().data('token'))
			return false;
		})
	}
	//###################################################################
	//	FUNÇÃO APAGA TODAS AS ABAS E REMONTA ELAS CONFORME A 
	//	SESSÃO ATUAL DE ARQUIVOS NA MEMÓRIA
	//###################################################################
	window.refreshAbas = function(active) {
		$('#webmaster_abas').html("");
		if (window.ws_webmaster.sessions) {
			$.each(window.ws_webmaster.sessions, function(i) {
				var obj = window.ws_webmaster.sessions[i];
				$('#webmaster_abas').append('<div title="' + obj.pathFile + '" class="aba" data-token="' + obj.token + '"><div class="label"><img class="status" src=""/> ' + obj.filename + '</div><i class="icon-close" title="Fechar"></i></div>')
				window.resizeAbas();
				window.functionAbas();
				window.updateStatusAba(obj.token, obj.unsave);
				$(".aba[data-token='" + window.ws_webmaster.atualSession.token + "']").click()
			})
		}
	}
	//###################################################################
	//  FUNÇÃO QUE DEFINE A COR DA BOLINHA DE ESTATUS DA ABA DO ARQUIVO
	//###################################################################
	window.updateStatusAba = function(token, status) {
		if (status == false) {
			var png = 'status.png';
		} else {
			var png = 'status-busy.png';
		}
		$(".aba[data-token='" + token + "'] .label img").attr("src", './app/templates/img/websheep/' + png)
	}
	//###################################################################
	//  FUNÇÃO QUE SALVA O ARQUIVO NO SERVIDOR
	//###################################################################
	window.salva_arquivo = function(token, pathFile, content) {
		$.ajax({
			type: "POST",
			async: true,
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'saveFile',
				'pathFile': pathFile,
				'conteudo': content
			},
			success: function(data) {}
		}).done(function(data) {
			if (window.ws_webmaster.verifySession(token)) {
				window.ws_webmaster.sessions[token].unsave = false;
				window.ws_webmaster.update(token);
				window.updateStatusAba(token, false);
			}
			ws.alert.top({
				mensagem: "Arquivo salvo com sucesso!",
				timer: 3000,
				color: "#FFF",
				background: "#9ee651",
				bottomColor: "#3a763e",
				styleText: "color:#000"
			});
		})
	}
	//###################################################################
	//  FUNÇÃO QUE EXCLUI O ARQUIVO NO SERVIDOR
	//###################################################################
	window.deleta_arquivo = function(pathFile) {
		ws.confirm({
			conteudo: '<span style="color:#000">Tem certeza de que gostaria <br> de excluir o arquivo <b>' + pathFile.replace(window.INCLUDE_PATH+'website',"") + '</b>?</span>',
			width: '400px',
			maxWidth: '400px',
			mleft: 0,
			minWidth: '400px',
			mtop: 0,
			bots: [{
				id: "aceitar",
				label: "Sim, excluir",
				class: "",
				style: "color: rgb(255, 255, 255);background-color: #ae4131;",
				css: {},
				ErrorCheck: function() {},
				Check: function() {
					return true
				},
				action: function() {
					$.ajax({
						type: "POST",
						async: true,
						url: "./app/ws-modules/ws-webmaster/functions.php",
						data: {
							'function': 'deleteFile',
							'pathFile': pathFile
						},
						success: function(data) {}
					}).done(function(data) {
						var data = $.parseJSON(data);
						if(data.status==1){
							$("*[data-token='" + data.token + "']").remove();
							if(window.ws_webmaster.verifySession(data.token)){
								window.ws_webmaster.delete.session(data.token);
								$(".aba[data-token='" + window.ws_webmaster.atualSession.token + "']").click();
							}
							ws.alert.top({
								mensagem: "Aqruivo excluído com sucesso!",
								timer: 3000,
								background: "#8bc34a",
								styleText: "color:#000",
								bottomColor: "#000"
							});
						}else{
							ws.alert.top({
								mensagem: "Ops, houve alguma falha ao excluir arquivo",
								timer: 3000,
								color: "#FFF",
								background: "#c73131",
								bottomColor: "#c73131",
								styleText: "color:#FFF"
							});
						}
					})
				},
			}, {
				id: "cancelar",
				label: "Ops, cliquei errado",
				class: "",
				style: "color: rgb(255, 255, 255);background-color: #7f7f7f;",
				css: {},
				ErrorCheck: function() {},
				Check: function() {
					return true
				},
				action: function() {
					ws.alert.top({
						mensagem: "Ufa! Essa foi por pouco hein?",
						timer: 3000,
						background: "#F3DB7A",
						bottomColor: "#F5C814"
					});
				},
			}],
			drag: false,
			botclose: false,
			newFun: function() {},
			onCancel: function() {},
			onClose: function() {},
			Callback: function() {},
			ErrorCheck: function() {},
			Check: function() {
				return true
			}
		})
	}
	//###################################################################
	//  FUNÇÃO QUE FECHA A ABA E EXCLUI O ARQUIVO APENAS DA SESSÃO
	//###################################################################
	window.closeAba = function(token) {
		if (window.ws_webmaster.sessions[token].unsave == true) {
			ws.confirm({
				conteudo: '<span style="color:#000;">Ops! O arquivo ' + window.ws_webmaster.sessions[token].filename + " possui alterações.</span>",
				width: '400px',
				maxWidth: '400px',
				minWidth: '400px',
				height: 'auto',
				maxHeight: 'initial',
				minHeight: 'unset',
				mleft: 0,
				mtop: 0,
				posFn: function() {},
				Init: function() {},
				posClose: function() {},
				bots: [{
					id: "aceitar",
					label: "Salvar arquivo",
					style: "color: rgb(255, 255, 255);background-color: #1a608f;",
					action: function() {
						var tk = window.ws_webmaster.sessions[token].token;
						var pathFile = window.ws_webmaster.sessions[token].pathFile;
						var content = window.ws_webmaster.sessions[token].contentChanged;
						window.salva_arquivo(tk, pathFile, content);
						$(".aba[data-token='" + token + "']").remove();
						window.ws_webmaster.delete.session(token);
						$(".aba[data-token='" + window.ws_webmaster.atualSession.token + "']").click();
					},
				}, {
					id: "fechar",
					label: "Apenas fechar",
					style: "color: rgb(255, 255, 255);background-color: #1a808f;",
					action: function() {
						$(".aba[data-token='" + token + "']").remove();
						window.ws_webmaster.delete.session(token);
						$(".aba[data-token='" + window.ws_webmaster.atualSession.token + "']").click();
					},
				}, {
					id: "cancelar",
					label: "Ops, cliquei errado",
					style: "color: rgb(255, 255, 255);background-color: #5e89a6;",
					Check: function() {
						return true
					},
					action: function() {
						ws.alert.top({
							mensagem: "Ufa! Essa foi por pouco hein?",
							timer: 3000,
							background: "#F3DB7A",
							bottomColor: "#F5C814"
						});
					},
				}],
				drag: false,
				botclose: false
			})
		} else {
			$(".aba[data-token='" + token + "']").remove()
			window.ws_webmaster.delete.session(token);
			$(".aba[data-token='" + window.ws_webmaster.atualSession.token + "']").click()
		}
	}
	//###################################################################
	// FUNÇÃO QUE INICIALIZA O CLICK DOS ARQUIVOS NA LATERAL ESQUERDA
	//###################################################################
	window.initClickExplorer = function() {
		$(".item.file").unbind("click tap press").bind("click tap press", function() {
			window.ws_webmaster.set.engine(window.htmEditor);
			var basename = $(this).data("basename");
			var path = $(this).data("path");
			var token = $(this).data("token");
			if ($('.aba [data-token="' + token + '"]').length) {
				$('.aba [data-token="' + token + '"]').click();
			} else {
				$.ajax({
					type: "POST",
					async: true,
					url: "./app/ws-modules/ws-webmaster/functions.php",
					data: {
						'function': 'loadFile',
						'path': path
					},
					success: function(data) {}
				}).done(function(data) {
					window.ws_webmaster.set.session({
						token: token,
						pathFile: path,
						filename: basename,
						stringFile: data,
						extension: basename.split('.').pop(),
					})
					window.refreshAbas(token);
					window.ws_webmaster.apply.session(token)
				})
			}
		})


		$(".bot_fn .deleteFolder").unbind("click tap press").bind("click tap press", function(e) {
				e.preventDefault();
				var path = $(this).parent().parent().data("path");
				window.deletaFolder(path)
				return false;
		})
		$(".bot_fn .deleteFile").unbind("click tap press").bind("click tap press", function(e) {
				e.preventDefault();
				var path = $(this).parent().parent().data("path")
				window.deleta_arquivo(path)
				return false;
		})
	}
	//###################################################################
	// EXCLUI DIRETÓRIO INTEIRO
	//###################################################################
	window.deletaFolder = function(path){
		ws.confirm({
				conteudo: 	'<div style="position: relative; font-size: 30px; font-weight: bold;color:#b82222; ">ATENÇÃO!</div><br>'+ 
							'Você tem <b>CERTEZA</b> que deseja <b>EXCLUIR</b> este diretório?<br>'+
							'<div style="position: relative; padding: 10px; background-color: #eadb98; margin: 7px 20px; border: solid 1px #eac78b; color: #aa5033; ">' + path.replace(window.INCLUDE_PATH+"website","") +'</div> <br>'+
							'<div style="color:#b41818;font-weight: 600;">Obs.: Todos os diretórios e arquivos <br>'+
							'dentro deste folder serão excluídos permanentemente<br>'+
							' e esta ação não terá volta!</div>',
				width: '400px',
				maxWidth: '400px',
				minWidth: '400px',
				height: 'auto',
				maxHeight: 'initial',
				minHeight: 'unset',
				mleft: 0,
				mtop: 0,
				posFn: function() {},
				Init: function() {},
				posClose: function() {},
				bots: [{
					id: "aceitar",
					label: "Excluir folder",
					style: "color: rgb(255, 255, 255);background-color: #b82222;",
					action: function() {
						ws.confirm({
							idModal: "get_password",
							conteudo: 	'Por favor, digite sua senha de administrador<br><input id="passUserDelFolder" class="inputText" type="password" style="position: relative; padding: 10px; margin-top: 20px; width: calc(100% - 10px); ">',
							width: '400px',
							maxWidth: '400px',
							minWidth: '400px',
							mleft: 0,
							mtop: 0,
							bots: [{
								id: "aceitar",
								label: "Excluir folder",
								style: "color: rgb(255, 255, 255);background-color: #b82222;",
								action: function() {
									$.ajax({
										type: "POST",
										async: true,
										url: "./app/ws-modules/ws-webmaster/functions.php",
										data: {
											'function': 'deleteFolder',
											'path': path,
											'password': $("#passUserDelFolder").val(),
										},
										success: function(data) {}
									}).done(function(data) {
										var data = $.parseJSON(data);
										if(data.status==1){
											ws.alert.top({
												mensagem: data.description,
												timer: 3000,
												color: "#FFF",
												background: "#9ee651",
												bottomColor: "#3a763e",
												styleText: "color:#000"
											});
											$.each($('.aba[title*="'+path+'"]'),function(){ 
												window.ws_webmaster.delete.session($(this).data("token"));
											})
											window.refreshAbas();
											window.refreshExplorerFilesLeft();
										}else{
											ws.alert.top({
												mensagem: data.description,
												timer: 3000,
												color: "#FFF",
												background: "#c73131",
												bottomColor: "#c73131",
												styleText: "color:#FFF"
											});
										}
									})
								},
							}, {
								id: "cancelar",
								label: "Cancelar",
								style: "color: rgb(255, 255, 255);background-color: #6caa17;",
								action: function() {
									ws.alert.top({
										mensagem: "Ufa! Essa foi por pouco hein?",
										timer: 3000,
										background: "#F3DB7A",
										bottomColor: "#F5C814"
									});
								},
							}],
							drag: false,
							botclose: false
						})
					},
				}, {
					id: "cancelar",
					label: "Ops, cliquei errado",
					style: "color: rgb(255, 255, 255);background-color: #6caa17;",
					action: function() {
						ws.alert.top({
							mensagem: "Ufa! Essa foi por pouco hein?",
							timer: 3000,
							background: "#F3DB7A",
							bottomColor: "#F5C814"
						});
					},
				}],
				drag: false,
				botclose: false
			})
	}
	//###################################################################
	// HABILITA A FUNÇÃO ACCORDION  E AS FUNÇÕES DE CLICK DO MENU ESQUERDO
	//###################################################################
	window.enableAccordion = function(cabecalho) {
		ws.accordion({
			closeBefore: false,
			closeOthers: false,
			beforeInit: function(a, b) {
				var container = $(b);
				if ($(b).text() == "") {
					var path = $(container).data('path');
					$.ajax({
						type: "POST",
						async: true,
						url: "./app/ws-modules/ws-webmaster/functions.php",
						data: {
							'function': 'get_left_folders',
							'path': path
						},
						success: function(data) {}
					}).done(function(data) {
						$(container).html(data)
						window.enableAccordion(cabecalho);
						window.initClickExplorer();
						$("*[legenda]").LegendaOver();
					})
					return true
				} else {
					return true
				}
			},
			cabecalho: cabecalho,
			initOpen: function(a, b) {},
			initClose: function(a, b) {},
			finishOpen: function(a, b) {
				$('.scrollbar,#webmaster_menu_right_icon').perfectScrollbar("update")
			},
			finishClose: function(a, b) {
				$('.scrollbar,#webmaster_menu_right_icon').perfectScrollbar("update")
			},
		})
	}

	//###################################################################
	// CLICK NOVO FOLDER 
	//###################################################################
	$("#CDNJS").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'returnCDNJS'
			},
			beforeSend: function() {
				ws.preload.open({string:"Carregando CNDJS"})
			}
		}).done(function(data) {
			ws.preload.close()
			console.log(data)
			window.resizeRighttMenu(600,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// CLICK NOVO FOLDER 
	//###################################################################
	$("#newFolder").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'plugin_new_folder'
			},
			beforeSend: function() {
				ws.preload.open({string:"Carregando..."})
			}
		}).done(function(data) {
			ws.preload.close()
			window.resizeRighttMenu(600,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// ADICIONA FERRAMENTA 
	//###################################################################
	$("#tools").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {'function': 'plugin_insert_tool'},
			beforeSend: function() {
				ws.preload.open({string:"Carregando ferramentas..."})
			}
		}).done(function(data) {
			ws.preload.close();
			window.resizeRighttMenu(600,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// ADICIONA FERRAMENTA 
	//###################################################################
	$("#plugins").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {'function': 'returnListPlugins'},
			beforeSend: function() {
				ws.preload.open({string:"Carregando plugins..."})
			}
		}).done(function(data) {
			ws.preload.close()
			window.resizeRighttMenu(600,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// ADICIONA FERRAMENTA 
	//###################################################################
	$("#shortcodes").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {'function': 'returnShortCodeList'},
			beforeSend: function() {
				ws.preload.open({string:"Carregando shortCode"})
			}
		}).done(function(data) {
			ws.preload.close();
			window.resizeRighttMenu(600,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// CLICK NOVO ARQUIVO 
	//###################################################################
	$("#newFile").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'plugin_new_file',
				'path': window.INCLUDE_PATH + 'website'
			},
			beforeSend: function() {
				ws.preload.open({string:"Carregando arquivos"})
			}
		}).done(function(data) {
			ws.preload.close()
			window.resizeRighttMenu(600,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// BOOTSTRAP 
	//###################################################################
	$("#BootstrapEditor").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		window.resizeRighttMenu(600,function(){
			$("#webmaster_menu_right_body").html('<iframe style="position: relative; width: 100%; height: 100%;" src="./app/vendor/bootstrapBuilder/index.html"></iframe>')
		});
		return false;
	});
	//###################################################################
	// IMPORTANDO FONTES DO GOOGLE 
	//###################################################################
	$("#GoogleFonts").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			beforeSend:function(){ws.preload.open({string:"Carregando Google Fonts"})},
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'returnGoogleFonts',
			}
		}).done(function(data) {
			ws.preload.close();
			window.resizeRighttMenu(700,function(){$("#webmaster_menu_right_body").html(data)});
		})
		return false;
	});
	//###################################################################
	// IMPORTANDO FONTES DO GOOGLE 
	//###################################################################
	$("#GridBootstrap").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			beforeSend:function(){ws.preload.open({string:"Carregando Grid Bootstrap"})},
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'returnGridBootstrap',
			}
		}).done(function(data) {
			ws.preload.close();
			var w = $("#webmaster_desktop").width();
			window.resizeRighttMenu(w,function(){
				$("#webmaster_menu_right_body").html(data)
			});
		})
		return false;
	});
	//###################################################################
	// PAGINAÇÃO 
	//###################################################################
	$("#paginate").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			beforeSend:function(){ws.preload.open({string:"Carregando paginação...."})},
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'template_paginate',
			}
		}).done(function(data) {
			ws.preload.close();
			window.resizeRighttMenu(600,function(){
				$("#webmaster_menu_right_body").html(data)
			});
		})
		return false;
	});

	//###################################################################
	// INSERINDO FORMULARIOS 
	//###################################################################
	$("#forms").unbind('click tap press').bind('click tap press',function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			beforeSend:function(){ws.preload.open({string:"Carregando formulários"})},
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'InsertCodeForm',
			}
		}).done(function(data) {
			ws.preload.close();
			window.resizeRighttMenu(600,function(){
				$("#webmaster_menu_right_body").html(data)
			});
		})
		return false;
	});



	//###################################################################
	// CARREGA A 1° LISTA DE ARQUIVOS E FOLDERS DO FTP 
	//###################################################################
	window.refreshExplorerFilesLeft = function(){
		$.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-webmaster/functions.php",
			data: {
				'function': 'get_left_folders',
				'path': window.INCLUDE_PATH + 'website'
			},
			beforeSend: function() {
				ws.preload.open({string:"Carregando arquivos do servidor"})
			}
		}).done(function(data) {
			ws.preload.close()
			$("#webmaster_menu_left .container-scroll").html(data);
			window.enableAccordion('#webmaster_menu_left .item.folder');
			window.initClickExplorer();
			$("*[legenda]").LegendaOver();
		})
	}
	//###########################################################################################################
	// CLASSE DO EDITOR
	//###########################################################################################################
	if (typeof window.ws_webmaster != "object") {
		window.ws_webmaster = {
			atualSession: {},
			sessions: {},
			engine: null,
			totalSess: function() {
				return Object.keys(window.ws_webmaster.sessions).length;
			},
			verifySession: function(token) {
				if (typeof window.ws_webmaster.sessions[token] == "object") {
					return true;
				} else {
					return false;
				}
			},
			update: function(token) {
				window.ws_webmaster.sessions[token].stringFile = window.ws_webmaster.sessions[token].contentChanged;
			},
			content: function() {
				return window.ws_webmaster.engine.getValue();
			},
			set: {
				atualSession: function(opt) {
					window.ws_webmaster.atualSession = opt
				},
				engine: function(opt) {
					window.ws_webmaster.engine = opt;
				},
				session: function(opcoes) {
					var options = ws.extend({
						token: null,
						pathFile: '/undefined/undefined.php',
						filename: 'undefined.php',
						stringFile: 'undefined',
						extension: 'php',
						setReadOnly: false,
						contentChanged: null,
						unsave: false,
					}, opcoes);
					// só para iniciar
					options.contentChanged = options.stringFile;
					if (window.ws_webmaster.engine == null) {
						console.error("Por favor setar uma uma engine:", "window.ws_webmaster.set.engine(ace)");
						return false;
					}
					var my_session = {}
					my_session[options.token] = options;

					if(options.extension=="txt"){options.extension="text";}
					if(options.extension=="js"){options.extension="javascript";}

					my_session[options.token].session = ace.createEditSession(options.stringFile, "ace/mode/" + options.extension);
					if (!window.ws_webmaster.sessions[options.token]) {
						window.ws_webmaster.sessions[options.token] = options
					}
					window.ws_webmaster.atualSession = my_session[options.token]
					return window.ws_webmaster.sessions[options.token];
				}
			},
			delete: {
				session: function(obj) {
					delete window.ws_webmaster.sessions[obj];
					$.each(window.ws_webmaster.sessions, function(i) {
						window.ws_webmaster.atualSession = window.ws_webmaster.sessions[i];
						return false
					})
					if (obj == window.ws_webmaster.atualSession.token || Object.keys(window.ws_webmaster.sessions).length == 0) {
						window.ws_webmaster.atualSession = {};
						window.ws_webmaster.engine.setValue("<? \n // welcome to WebSHeep!");
					}
					return;
				}
			},
			apply: {
				session: function(token) {
					if (window.ws_webmaster.engine == null) {
						console.error("Por favor setar uma uma engine:", "window.ws_webmaster.set.engine(ace)");
						return false;
					}
					if (Object.keys(window.ws_webmaster.sessions).length > 0) {
						window.ws_webmaster.set.atualSession(window.ws_webmaster.sessions[token])
						window.ws_webmaster.engine.setSession(window.ws_webmaster.sessions[token].session);
					}
				}
			},
		}
	}
window.keyEvents = function() {
		$(document).keydown(function(event) {
			console.log(event.which)
			if(event.keyCode == 27) {
				//ESC
				event.preventDefault();
				window.closeRighttMenu();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 80) {
				// ctrol 'P' paginate
				event.preventDefault();
				$("#paginate").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 89) {
				// ctrol 'y' config	
				event.preventDefault();	
				$("#config").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 68) {
				// ctrol 'D' deletar	
				event.preventDefault();	
				$("#deleteFile").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 83) {
				// ctrol 'S' salvar	
				event.preventDefault();
				$("#saveFile").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 192) {
				// ctrol (') ferramentas	
				event.preventDefault();
				$("#tools").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 69) {
				// ctrol (E) FOLDER						
				event.preventDefault();
				$("#newFolder").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 49) {
				// ctrol '1' Plugin	
				event.preventDefault();
				$("#plugins").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 78) {
				// ctrol 'N' novo arquivo	
				event.preventDefault();
				$("#newFile").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 71) {
				// ctrol 'G' CDNJS cdn	
				event.preventDefault();
				$("#CDNJS").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 72) {
				// ctrol 'H' ShortCode	
				event.preventDefault();
				$("#shortcodes").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 81) {
				// ctrol 'Q' formulario	
				event.preventDefault();
				$("#forms").click();
				return false;
			}else if((event.ctrlKey || event.metaKey) && event.which == 74) {
				// ctrol 'H' FONTS	
				event.preventDefault();
				$("#GoogleFonts").click();
				return false;

			}else if((event.ctrlKey || event.metaKey) && event.which == 50) {
				// ctrol '2' Bootstrap	
				event.preventDefault();
				$("#GridBootstrap").click();
				return false;
			}	
	});
}


	//###########################################################################################################
	// CONTAMOS 0.5 SEGUNDOS PARA INICIALIZAR TUDO 
	//###########################################################################################################
	setTimeout(function() {
		$(window).trigger('resize');
		window.refreshExplorerFilesLeft();
		var sessao_atual = null;
		if (typeof window.ws_webmaster.atualSession == "undefined") {
			$.each(window.ws_webmaster.sessions, function(i) {
				sessao_atual = window.ws_webmaster.sessions[i].token;
				return false
			})
		} else if(typeof window.ws_webmaster.atualSession.token != "undefined") {
			sessao_atual = window.ws_webmaster.atualSession.token
			window.ws_webmaster.set.engine(window.htmEditor)
			window.ws_webmaster.apply.session(sessao_atual);
			window.refreshAbas(sessao_atual);
			window.htmEditor.on('change', function() {
				if (Object.keys(window.ws_webmaster.sessions).length > 0) {
					if (window.ws_webmaster.sessions[window.ws_webmaster.atualSession.token].stringFile == window.ws_webmaster.content()) {
						window.updateStatusAba(window.ws_webmaster.atualSession.token, false);
						window.ws_webmaster.sessions[window.ws_webmaster.atualSession.token].unsave = false;
					} else {
						window.updateStatusAba(window.ws_webmaster.atualSession.token, true);
						window.ws_webmaster.sessions[window.ws_webmaster.atualSession.token].unsave = true;
					}
					window.ws_webmaster.sessions[window.ws_webmaster.atualSession.token].contentChanged = window.ws_webmaster.content();
				}
			})
		}
		window.keyEvents();
	}, 500)
})