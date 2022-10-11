$(document).ready(function(){
	/*############################################################################################################################################*/
	//	DEFINIMOS O PATH PADRÃO DOS ARQUIVOS DE EDITOR DE TEXTO CKEDITOR
	/*############################################################################################################################################*/
	var CKEDITOR_BASEPATH = window.CKEDITOR_BASEPATH ='app/vendor/CKeditor/';

	//############################################################################################################################################
	//#  PRELOADER  
	//############################################################################################################################################
        function preload() {
		manifest = [
			//######################################################################################################################
			//#  IMAGENS  
			//######################################################################################################################									
			{src:ws.rootPath+"admin/app/templates/img/websheep/Splash.jpg",										id:"image", 		include:false},
			{src:ws.rootPath+"admin/app/templates/img/websheep/SplashScreen.png",								id:"image", 		include:false},
			{src:ws.rootPath+"admin/app/templates/img/websheep/icones_editor.png",								id:"image", 		include:false},
			{src:ws.rootPath+"admin/app/templates/img/websheep/icons_topo_tools.png",							id:"image", 		include:false},
			{src:ws.rootPath+"admin/app/templates/img/websheep/sem_img_G.jpg",									id:"image", 		include:false},
			//######################################################################################################################
			//#  CSS  
			//######################################################################################################################
			{src:ws.rootPath+"admin/app/templates/css/websheep/reset.css",										id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/websheep/funcionalidades.css",							id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/fontes/fonts.css",										id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/websheep/estrutura.min.css",								id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/websheep/desktop.min.css?v=0.1",							id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/websheep/theme_blue.min.css",								id:"CSS",			include:true},
			{src:ws.rootPath+"admin/app/templates/css/jquery-ui/1.12.1/jquery-ui.min.css",						id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/chosen/chosen.css",										id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/cleditor/jquery.cleditor.css",							id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/perfect-scrollbar/perfect-scrollbar.min.css",				id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.plugin.tags.css",							id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.core.css",								id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.plugin.arrow.css",						id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.plugin.autocomplete.css",					id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.plugin.clear.css",						id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.plugin.focus.css",						id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/textext/textext.plugin.prompt.css",						id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/colorpicker/colorpicker.css",								id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/templates/css/colorpicker/layout.css?v=0.1",							id:"CSS", 			include:true},
			{src:ws.rootPath+"admin/app/vendor/intro.js-2.5.0/introjs.min.css",									id:"CSS",			include:true},
			{src:ws.rootPath+"admin/app/vendor/grapes-js/grapes.min.css",										id:"CSS",			include:true},
			{src:ws.rootPath+"admin/app/vendor/grapes-js/grapesjs-preset-webpage.min.css",						id:"CSS",			include:true},
			//######################################################################################################################
			//#  AJAVASCRIPT  
			//######################################################################################################################
			{src:ws.rootPath+"admin/app/vendor/jquery/3.3.1/jquery.min.js", 									id:"javascript", 	include:true},
			{src:ws.rootPath+"admin/app/vendor/intro.js-2.5.0/intro.min.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/jquery-ui/1.10.3/jquery.min.js", 								id:"javascript", 	include:true},
			{src:ws.rootPath+"admin/app/vendor/BrowserDetect/BrowserDetect.min.js",								id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/perfect-scrollbar/perfect-scrollbar.min.js", 					id:"javascript", 	include:true},
			{src:ws.rootPath+"admin/app/vendor/chosen/chosen.jquery.js", 										id:"javascript", 	include:true},
			{src:ws.rootPath+"admin/app/vendor/dataTableJquery/data-table.jquery.min.js",						id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/RightClick/rightClick.min.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/jquery-animate/jquery.animate-colors.min.js",					id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/AjaxForm/AjaxForm.min.js",										id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/jquery-form/jquery.form.js",										id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/highcharts/stock/highstock.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/beautify-html/beautify-html.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/highcharts/stock/modules/exporting.js",							id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/highcharts/highcharts.js",										id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/highcharts/modules/exporting.js",								id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/highcharts/highcharts-more.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/colorpicker/colorpicker.min.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/colorpicker/eye.min.js",											id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/colorpicker/utils.min.js",										id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/colorpicker/layout.min.js",										id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.core.min.js",									id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.tags.min.js",								id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.suggestions.min.js",						id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.prompt.min.js",							id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.focus.min.js",							id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.filter.min.js",							id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.clear.min.js",							id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.autocomplete.min.js",						id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.arrow.min.js",							id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/textext/textext.plugin.ajax.min.js",								id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/CKeditor/ckeditor.js",											id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/grapes-js/grapes.min.js",										id:"javascript",	include:true},
			{src:ws.rootPath+"admin/app/vendor/split/split.js",													id:"javascript",	include:true},
			// {src:ws.rootPath+"admin/app/vendor/artyom/artyom.min.js",										id:"javascript",	include:true},
			// {src:ws.rootPath+"admin/app/templates/js/websheep/ws-dolly.js", 									id:"javascript", 	include:true},
			{src:ws.rootPath+"admin/app/templates/js/websheep/funcionalidades.js", 								id:"javascript", 	include:true},
			{src:ws.rootPath+"admin/app/vendor/require/require.js", 											id:"javascript", 	include:true},
		];
		preload = new createjs.LoadQueue (false ,null, true);
		preload.on("fileload",	onLoad);
		preload.on("progress", 	onProgress	);
		preload.on("complete", 	onComplete	,true);
		preload.on("error", 	onError		);
		preload.setMaxConnections(1);
		preload.loadManifest(manifest,[loadNow=false]);
        }
        function onError(event) {
        	//var div = event.item.id;
        	console.error(event);
        }
        function onLoad(event) {
        		window.loader_obj = event.item.src;
        		window.loader_ext = event.item.id;
        		window.loader_tag = event.result;
        		if(event.item.src==ws.rootPath+"admin/app/templates/img/websheep/SplashScreen.png"){	
        			$("#preloadWS #splashWS").css("background-image","url("+ws.rootPath+"admin/app/templates/img/websheep/SplashScreen.png)");
        		}
				if((event.item.id=='javascript' || event.item.id=='CSS') && event.item.include== true){$('html head').prepend(event.result);}
				if(event.item.id=='image'){}
        }
		function onProgress(event) {
			var perc = Math.floor(preload.progress*100);
			$("#preloadWS #splashWS #pct #bar").css({'width':perc+'%'});
			// $("#preloadWS #splashWS #pct #number").css({'left':perc+'%'}).html(perc+'%');
		}
        function onComplete(event) {init(event);}
		preload();
		//############################################################################################################################################
		//  END PRELOADER  
		//############################################################################################################################################
		function init(event){

			//############################################################################################################################################
			//  INICIANDO MÓDULO DE VOZ  
			//############################################################################################################################################
			// ws.alert.top({
			// 	mensagem:"O WebSheep agora tem o seu próprio assistente virtual.(beta) <a id='TopAlertAtivarVoz'><b>Ative agora</b></a> ",
			// 	clickclose:true,
			// 	height:20,
			// 	timer:7000,
			// 	posFn:function(){ $("#TopAlertAtivarVoz").bind("click tap press",function(){wsAssistent.init();})},
			// 	styleText:"color:#ffffff",
			// 	background:"#94ca4f",
			// 	bottomColor:"#000"
			// })

			if(typeof directAccess!='undefined'){
				if(directAccess.oldVersion.deprecated==true){
					ws.alert.top({
						mensagem:"A sua versão do painel está obsoleta. <a id='update'><b>Faça update agora</b></a> ",
						clickclose:true,
						height:20,
						timer:7000,
						posFn:function(){ 
							$("#update").bind("click tap press",function(){
								$(".updateRestore").click();						
							})
						},
						styleText:"color:#ffffff",
						background:"#bc0d0d",
						bottomColor:"#000"
					})
				}
				if(directAccess.newUpdate==true){
					ws.alert.top({
						mensagem:"Oba! O WebSheep disponibilizou uma atualização do painel! <a id='update'><b>Faça update agora</b></a> ",
						clickclose:true,
						height:20,
						timer:10000,
						posFn:function(){ 
							$("#update").bind("click tap press",function(){
								$(".updateRestore").click();						
							})
						},
						styleText:"color:#ffffff;text-shadow: -1px -1px 1px #3a5104;",
						background:"#75a700",
						bottomColor:"#000"
					})
				}
			}
			window.CloseMenu = function(){
				$("#menu_tools,#container").removeClass("open").addClass("closed"); 
				$("#menu_tools .FolderOpen").click();
				$('#menu_tools').animate({scrollTop: 0}, 200);
				setTimeout(function(){if (typeof window.resizeDesktop === "function"){window.resizeDesktop();}},200);
				setTimeout(function(){if (typeof window.resizeAbas === "function"){window.resizeAbas();}},1000);
			}
			window.OpenMenu = function(){
				$("#menu_tools,#container").removeClass("closed").addClass("open"); 
				setTimeout(function(){if (typeof window.resizeDesktop === "function"){window.resizeDesktop();}},200);
				setTimeout(function(){if (typeof window.resizeAbas === "function"){window.resizeAbas();}},1000);
			}
			window.CloseMenu();
			window.refreshMenuDesktop = function(){
				$.ajax({
					beforeSend:function(){
						$("#menu_tools").html("").css({"backgroundImage":'url('+ws.rootPath+'admin/app/templates/img/websheep/preloaderMenuLeft.png")'});
					},
					type: "POST",
					url: ws.rootPath+"admin/app/ws-modules/ws-tools/functions.php",
					data:{"function":"refreshMenuDesktop"}
				}).done(function(data) {
					$("#menu_tools").html(data).css({"backgroundImage":"none"});
					$('body').unbind("click contextmenu  tap press mousedown").bind("click contextmenu  tap press mousedown", function (e) {
						if (!$("#menu_tools").has(e.target).length && e.target != $("#menu_tools")[0] && !$(e.target).is( "#menu_mobile" )){window.CloseMenu();}
					});
					window.sanfona({
						cabecalho:"#menu_tools .path",
						initOpen:function(e){
							if($(e).find('.icon').hasClass("fa-folder")){
								$(e).find('.icon').removeClass("fa-folder").addClass("fa-folder-open");
							}
						},
						initClose:function(e){
							if($(e).find('.icon').hasClass("fa-folder-open")){
								$(e).find('.icon').removeClass("fa-folder-open").addClass("fa-folder");
							}
						},
						finishOpen:function(e){
							$('#menu_tools').perfectScrollbar('update');
						},
						finishClose:function(e){
							$('#menu_tools').perfectScrollbar('update');
						}
					});
					$(".ferramenta_especial,.ferramenta_especial.innerOpt.tool").unbind("click tap press").bind("click tap press",function(e) {
						var title 			= $(this).data('title-loading');
						var get 			= $(this).data('get');
						var path 			= $(this).data('path');
						var id_ferramenta 	= $(this).data('ferramenta');
						var tokenAccess 	= $(this).data('tokenAccess');
						console.log("====>",tokenAccess)
						if(id_ferramenta=="-1"){
							e.preventDefault();
							window.open(path, '_blank');
							return false;
						}
						if(id_ferramenta=="iframe"){
							e.preventDefault();

							if(tokenAccess==1){
								$.ajax({
									type: "POST",
									url: ws.rootPath+"admin/app/ws-modules/ws-tools/functions.php",
									data:{"function":"returnTokenAccess"}
								}).done(function(data) {
									$('#conteudo').html('<iframe src="'+path+'?tokenAccess='+data+'" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;margin:0;"></iframe>');
								});
							}else{
								$('#conteudo').html('<iframe src="'+path+'" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;margin:0;"></iframe>');
							}



							return false;
						}
						confirma({
							conteudo:title+"</b><div class=\'preloaderupdate\' style=\'position: absolute;width: 30px;height: 30px;left: 230px;top: 68px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
							drag:false,
							bot1:0,bot2:0,
							posFn:function(){
								$("#ws_confirm #body #recusar").hide();
								$.ajax({
								  type: "POST",
								  url: path+"?ws_id_ferramenta="+id_ferramenta+"&"+get,
								  data:"id_ferramenta="+id_ferramenta+"&"+get
								}).done(function(data) {
									$("#ws_confirm").remove();
									$("#body").removeClass("scrollhidden");
									$("*").removeClass("blur");
									$("#conteudo").html(data);
									window.CloseMenu()
								});
							}
						});		
						return false;
					});

					$("li.plugin a").unbind("click tap press").bind("click tap press",function(e) {
									e.preventDefault()
									var titulo 				= $(this).html();
									var id_ferramenta 		= $(this).data('id');
									var href 				= $(this).attr('href');				
									var path 				= $(this).data('path');				
									var page 				= $(this).data('file');				
									var dataType 			= $(this).data('type');
									var ferramenta 			= $(this).data('ferramenta');
									var dataW 				= $(this).data('w');
									var dataH 				= $(this).data('h');
									var plugin 				= $(this).data('plugin');
									var outuptPluigin 		= {'filename':href,'type':dataType,'dataW':dataW,'dataH':dataH}
									var tokenAccess 		= $(this).data('tokenaccess');

									if(plugin=="0"){
										confirma({
											width:"auto",
											conteudo:"  {dashboard_loading}<br> <strong>"+titulo+"</strong>...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 68px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
											drag:false,
											posFn:function(){
													$("#ws_confirm").css("line-height","20px");
													$( "#conteudo" ).load( page+"?ws_id_ferramenta="+ferramenta, function( response, status, xhr ) {
														$("#ws_confirm").remove();
														$("#body").removeClass("scrollhidden");
														$("*").removeClass("blur");
														window.CloseMenu();
													});
											},
											bot1:0,
											bot2:0
										})
									}else if(plugin=="1"){
										if(tokenAccess==1){
											$.ajax({
												type: "POST",
												url: ws.rootPath+"admin/app/ws-modules/ws-tools/functions.php",
												data:{"function":"returnTokenAccess"}
											}).done(function(data) {
												href = href+"?TokenAccess="+data;
												if(dataType=="popup"){
													popup(href,dataW,dataH);
												}else{
													loadPluginFile({'filename':href, 'type':dataType,'dataW':dataW,'dataH':dataH})
												}
											});
										}else{
											if(dataType=="popup"){
												popup(href,dataW,dataH);
											}else{
												loadPluginFile({'filename':href, 'type':dataType,'dataW':dataW,'dataH':dataH})
											}
										}
									}
									return false;
					});


					$(".reportBugs").unbind("click tap press").bind("click tap press",function(e) {
						e.preventDefault();
						$('#conteudo').html('<iframe src="//api.websheep.com.br/report-bugs/" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;margin:0;"></iframe>');
						return false;
					});

					$(".reportBugs").unbind("click tap press").bind("click tap press",function(e) {
						e.preventDefault();
						$('#conteudo').html('<iframe src="//api.websheep.com.br/report-bugs/" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;margin:0;"></iframe>');
						return false;
					});


					$(".biblioteca").unbind("click tap press").bind("click tap press",function(e) {
						e.preventDefault();
						abreBiblioteca({
							admin:1,
							multiple:1,
							type:'all',
							posFn:function(){},
							onSelect:function(){}
						})
						return false;
					});
					$("#bkp").unbind("click tap press").bind("click tap press",function(e) {
						window.bkpws = "";
						confirma({
							conteudo:"{dashboard_modal_bkp_content}",
							width:600,
							bot1:"{dashboard_modal_bkp_bot1}",
							bot2:"{dashboard_modal_bkp_bot2}",
							divScroll:"body",
							divBlur:"body #container",
							Callback:function(){
								functions({
									patch:ws.rootPath+"admin/app/ws-modules/ws-tools",
									funcao:"bkpWS",
									Sucess:function(e){
										var setBkpWS = setInterval(function(){
											window.bkpws = e;
											if(window.bkpws!=""){clearInterval(setBkpWS);}
										},1000);
									}
								})
								setTimeout(function(){
									confirma({
										conteudo:"{dashboard_modal_bkp_ridingBKP}",
										width:300,
										bot1:0,
										bot2:0,
										divScroll:"body",
										divBlur:"body #container",
										Callback:function(){}
									})
								},500);
								var verificaBkpWS = setInterval(function(){
										if(window.bkpws!=""){
											clearInterval(verificaBkpWS);
											$("#ws_confirm").hide("fast");
											$("body #container").removeClass('blur');
											window.location= ""+window.bkpws;
									}
								},1000);
							}
						})
					});
					$(".bloqueado").unbind("click tap press").bind("click tap press",function(e) {
						TopAlert({
							mensagem:"{dashboard_topAlert_content}",
							clickclose:true,
							height:20,
							timer:4000,
							type:2
						})
					})	
					$("#clicklogout").unbind("click tap press").bind("click tap press",function(e) {

						var dashboard_modal_logOut_bot1 	= $(this).data("modal-logout-bot1");
						var dashboard_modal_logOut_bot2 	= $(this).data("modal-logout-bot2");
						var dashboard_modal_logOut_content 	= $(this).data("modal-logout-content");
						var dashboard_modal_logOut_loading	= $(this).data("modal-logout-loading");

						confirma({
							conteudo:dashboard_modal_logOut_content,
							width:600,
							bot1:dashboard_modal_logOut_bot1,
							bot2:dashboard_modal_logOut_bot2,
							divScroll:"body",
							divBlur:"body #container",
							Callback:function(){
									$.ajax({
										type: "POST",
										async: true,
										url: ws.rootPath+"admin/app/ws-modules/ws-login/functions.php",
										data:{'function':'logout'},
										beforeSend: function() {
											$("#iniciarsessao").hide('fast')
											$("#iniciarsessao_disabled").show('fast')
											setTimeout(function(){
												confirma({width: "auto", conteudo: dashboard_modal_logOut_loading+"<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>", drag: false, bot1: 0, bot2: 0 })
											},1000)
										}
									}).done(function(e){
										document.cookie.split(";").forEach(function(c) { 
											document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
										}); 
										window.location.reload();
									})
							},
							bot1:'Sim quero sair!',
							bot2:"Ops, cliquei errado"
						})
					});
				})			
			}
			window.refreshMenuDesktop();
			$("#preloadWS").hide("fade", {}, 1000);
			// RightClick.init('html');// tira click direito
			$(".ws_menu .folder").unbind("click contextmenu  tap press mousedown").bind("click contextmenu  tap press mousedown",function(){
				$('.ws_menu_popup').fadeOut('fast');
				$(this).parent().find('.ws_menu_popup').fadeIn('fast');
			})
			$(".ws_menu_popup li").unbind("click contextmenu  tap press mousedown").bind("click contextmenu  tap press mousedown",function(){
				$(this).find('a').click();
				$('.ws_menu_popup').fadeOut('fast');
			})


			$('body,iframe').unbind("click contextmenu  tap press mousedown").bind("click contextmenu  tap press mousedown", function (e) {
				if (!$("#menu_tools").has(e.target).length && e.target != $("#menu_tools")[0] && !$(e.target).is( "#menu_mobile" )){
					if(typeof(window.CloseMenu)=="function"){window.CloseMenu();}
				}
			});
		window.onblur   = function() {window.CloseMenu();} 
		$('#menu_tools').unbind('hover').hover(function() {
			window.OpenMenu()
		},function() {
			if(document.activeElement.nodeName=="IFRAME"){
				window.CloseMenu();
			}
		})
		$("#menu_mobile").unbind("click tap press").bind("click tap press",function(){
			if($("#menu_tools").hasClass("open")){
				window.CloseMenu()
			}else{
				window.OpenMenu()
			}
		})

		$('#menu_tools,#container').bind("mouseover,wheel,touchstart,touchmove,wheel,touchstart,touchmove", function(e){
			e.stopPropagation();
			e.preventDefault();    
		}).perfectScrollbar({suppressScrollX: true}); 


		$(window).resize(function(){$('#menu_tools,#container').perfectScrollbar('update');})

		$('*[legenda]').LegendaOver();

		//############################################################################

		if(typeof(directAccess) !== 'undefined' && directAccess != null && typeof directAccess.id_tool == 'number' && directAccess.LoadDirectTool != 'null'){
		 	var linkAccess  = null;
		 	if(directAccess.type_obj=='item' && directAccess.id_item==0){
			 	var linkAccess = ws.rootPath+"admin/app/ws-modules/ws-model-tool/index.php?back=false&ws_id_ferramenta="+directAccess.id_tool;
		 	}
		 	if(directAccess.type_obj=='detail' && directAccess.id_item>0){
			 	var linkAccess = ws.rootPath+"admin/app/ws-modules/ws-model-tool/detalhes.php?back=false&ws_id_ferramenta="+directAccess.id_tool+"&id_item="+directAccess.id_item;
		 	}
		 	if(directAccess.type_obj=='img' && directAccess.id_item>0){
			 	var linkAccess = ws.rootPath+"admin/app/ws-modules/ws-model-tool/imagens.php?back=false&ws_nivel=-1&id_cat=0&ws_id_ferramenta="+directAccess.id_tool+"&id_item="+directAccess.id_item;
		 	}
		 	if(directAccess.type_obj=='gal' && directAccess.id_item>0){
			 	var linkAccess = ws.rootPath+"admin/app/ws-modules/ws-model-tool/galerias.php?back=false&ws_nivel=-1&id_cat=0&ws_id_ferramenta="+directAccess.id_tool+"&id_item="+directAccess.id_item;
		 	}
		 	if(directAccess.type_obj=='img_gal' && directAccess.id_item>0 && directAccess.id_gal>0){
			 	var linkAccess = ws.rootPath+"admin/app/ws-modules/ws-model-tool/galeria_fotos.php?back=false&ws_id_ferramenta="+directAccess.id_tool+"&id_item="+directAccess.id_item+"&id_galeria="+directAccess.id_gal;
		 	}
		 	if(directAccess.type_obj=='files' && directAccess.id_item>0){
			 	var linkAccess = ws.rootPath+"admin/app/ws-modules/ws-model-tool/files.php?direct=true&ws_nivel=-1&ws_id_ferramenta="+directAccess.id_tool+"&id_item="+directAccess.id_item;
		 	}



		 	if(linkAccess!=null){
				confirma({
					width:"auto",
					conteudo:"...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 68px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
					drag:false,
					bot1:0,bot2:0,
					posFn:function(){
							$("#ws_confirm").css("line-height","20px");
							$( "#conteudo" ).load(linkAccess, function( response, status, xhr ) {
								$("#ws_confirm").remove(); $("#body").removeClass("scrollhidden"); $("*").removeClass("blur"); window.CloseMenu();
							});
					}
				})
		 	}
		 }


		if(typeof(directAccess) !== 'undefined' && directAccess.LoadDirectTool != 'null'){
				confirma({
					width:"auto",
					conteudo:"...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 68px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
					drag:false,
					bot1:0,bot2:0,
					posFn:function(){
							$("#ws_confirm").css("line-height","20px");
								$( "#conteudo" ).load(directAccess.LoadDirectTool, function( response, status, xhr ) {
									$("#ws_confirm").remove(); $("#body").removeClass("scrollhidden"); $("*").removeClass("blur"); window.CloseMenu();
								});
					}
				})



		}




		//############################################################################









	};
});


