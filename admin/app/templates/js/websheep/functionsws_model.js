/*! 

	WebSheep Functions v0.3.3
	Development: WebSheep Tecnologia Integrada
	Compressed: {dataMinifiq}

	##################### Insert the code right after the <body>  ##########################

	<div id="ws-root"></div>
	<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "/admin/app/templates/js/websheep/functionsws.min.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'websheep-functions'));
	</script>
	
	#######################################################################################

	ws.info;
	ws.init();
	ws.set.obj("teste","123");
	ws.get.obj.teste;
	ws.set.clipboard("teste");
	ws.verify.remoteFileExists("url");
	ws.verify.jquery();
	ws.form.jquery();
	ws.input('#input').is_email();
	ws.input('#input').is_blank();

	ws 
		.form 
		.sendLeads("djfghkdjghk") 
		.setForm("#form") 
		.setCaptcha("inputCaptcha") 
		.beforeSend(function(){}) 
		.ajaxSend(function(){}) 
		.success(function(){}) 
		.error(function(){}) 
		.complete(function(){}) 
		.uploadProgress(function(pct,total){}) 
		.go()

	ws
		.ajax('./app/templates/txt/ws-model-htaccess.txt').method("POST")
		.onBeforeSend(function(){console.log("onBeforeSend")})
		.onSucess(function(){console.log("onSucess")})
		.onSend(function(){console.log("onSend")})
		.onError(function(){console.log("erro!")})
		.onDone(function(e){console.log(e)})
		.go()

	ws.mouse.setTarget("*").setCursor("pointer");
	ws.mouse.setTarget("*").disableContextMenu();
	ws.mouse.setTarget("*").disableDrop();

	ws.encode.crypt("string","password")
	ws.encode.utf8("string")
	ws.encode.base64("string")
	ws.encode.md5("string")
	ws.decode.crypt("string","password")
	ws.decode.utf8("string","password")
	ws.decode.base64("string","password")

	ws.exists.file("http://urlfile");
	ws.exists.dom("#div");
	ws.load.json("url").onLoad(function(e){}).go();
	ws.load.script("url");
	ws.state().listener(function(e){}).setPath("url/url/url").push();

	ws.alert.top({
				mensagem	: "Mensagem padrão",
				clickclose	: true,
				height		: 20,
				position: "fixed",
				botClose: null,
				onClose: function() {},
				posFn: function() {},
				timeoutFn: function() {},
				timer: 3000,
				type: null,
				styleText: null,
				classText: null,
				color: "#E04E1F",
				background: "#F3DB7A",
				bottomColor: "#F5C814"
			});

			ws.accordion({
				cabecalho: "#div",
				closeOthers: true,
				closeBefore: true,
				beforeInit: function(){return true;},
				initOpen: function(){},
				initClose: function(){},
				finishOpen: function(){},
				finishClose: function(){}
			})

			ws.confirm({
				conteudo:'',
				width: 'calc(100% - 20px)',
				maxWidth: '1000px',
				minWidth: '300px',
				height: 'auto',
				maxHeight: 'initial',
				minHeight: 'unset',
				mleft: 0,
				mtop: 0,
				posFn: function() {},
				Init: function() {},
				posClose: function() {},
				bots: [
					// {
					// 		id			: "aceitar",
					// 		label		: "Aceitar",
					// 		class		: "",
					// 		style 		: "",
					// 		css 		: {"color":"#000"},
					//		ErrorCheck	: function() {},
					//		Check 		: function() {return true},
					// 		action		: function(){console.log("1111111")},
					// }
				],
				bot1: false,
				cancel: false,
				idModal: "ws_confirm",
				divScroll: "body",
				divBlur: "#menu_tools,#container,#header",
				drag: true,
				botclose: true,
				newFun: function() {},
				onCancel: function() {},
				onClose: function() {},
				Callback: function() {},
				ErrorCheck: function() {},
				Check: function() {
					return true
				}
			})

*/


var ws = new Object();
ws = {
	//{{rootPath}},
	info: {
		version: "0.3.4",
		compatible: "6.2+",
		creator: "WebSheep Tecnology"
	},
	error:function(a){
		throw new Error(a)
	},
	init: function() {
		if(ws.verify.jquery() == false && ws.insert.js(ws.rootPath+"admin/app/vendor/jquery/2.2.0/jquery.min.js", "jQuery", true) == true) {
			ws.log.info("Jquery 2.2.0 instalado");
		}
		if(ws.$("#style_ws").length == 0 && ws.insert.css(ws.rootPath+"admin/app/templates/css/websheep/funcionalidades.css", "style_ws", "All") == true) {
			ws.log.info("Style Importado");
		}
	},
	extend:function(defaults,config){
		return Object.assign({}, defaults, config);
	},
	get: {
		obj:{},
		clipboard:[],
		domAttr:function(element){
			if(!ws.exists.dom(element)){ws.log.error("ws.get.domAttr("+element+")");return false;}
       		var attributes 	= {}; 
       		var element 	= "#answer-2048786";
			var attrs 		=  ws.$(element)[0].attributes;
			if(ws.$(element)[0].hasAttributes()){
				for(var i = attrs.length - 1; i >= 0; i--) {
					attributes[attrs[i].name]= attrs[i].value;
				}
			}
      		 return attributes;
      	}
	},
	set: {
		obj: function(newVal, valor) {
			ws.get.obj[newVal] = valor;
		},
		clipboard:function(html){
			var tempInput 	= document.createElement("input");
			tempInput.value = html;
			tempInput.style = "position:fixed;left:-90000px;top:-90000px";
			document.body.appendChild(tempInput);
			tempInput.select();
			document.execCommand("copy");
			document.body.removeChild(tempInput);
			ws.get.clipboard.push(html);
			return true;
		},
		log:function(opcoes){
			var options = ws.extend({
				url: ws.rootPath+'/admin/app/ws-modules/ws-logs/functions.php',
				function: 'addLog',
				ws_author: 0,
				id_user: 2,
				id_ferramenta: 3,
				id_item: 4,
				dataregistro: 5,
				titulo: 6,
				descricao: 7,
				detalhes: 8,
				tabela: 9,
				type: 'system',
			}, opcoes);
			$.ajax({
				url: options.url,
				type:'POST',
				data: options,
				success: function(e) {
					console.log("sucess!")
					console.log(e)
				},
				error: function(e) {
					console.log("Error!")
					console.log(e)
				}
			});
		}
	},
	state:function(){
		return {
			thisPath: null,
			thisListener: null,
			setPath:function(Path){
				this.thisPath = Path;
				return this;
			},
			listener:function(fn){
				thisWS 				= this;
				// thisWS.thisListener = fn
				// window.onpopstate 	= function (event) {thisWS.thisListener(thisWS.thisPath)}
				return this
			},
			push:function(){
				thisWS 				= this;
			    window.history.pushState({}, this.thisPath, '/'+this.thisPath);
			    // thisWS.thisListener(this.thisPath)
				return this
			},
			replace:function(str){
				thisWS 				= this;
				// thisWS.thisListener = fn
			    window.history.replaceState({}, thisWS.thisPath, '/'+thisWS.thisPath);
			    // thisWS.thisListener(thisWS.thisPath)
				return this
			}
		}
	},

	verify: {
		remoteFileExists:function(url){
		    var http = new XMLHttpRequest();
		    http.open('HEAD', url, false);
		    http.send();
		    return http.status != 404;
		},
		jquery: function() {
			if(!window.jQuery && typeof jQuery === 'undefined') {
				console.error("ERRO: Jquery necessário");
				return false;
			} else {
				console.info("Jquery instalado: " + window.$.prototype.jquery.split(" ")[0]);
				return true;
			}
		}
	},
	js: function(id = null) {return;},
	css: function(id) {},
	audio:{
		speak: function(data="") {
				var w = ($(window).width()/2) - ($( "#dolly" ).width()/2);
				var h = ($(window).height()/2)- ($( "#dolly" ).height()/2);
				$( "#dolly" ).animate({left:w,top: h,},300, function() {
					responsiveVoice.speak(data, "Brazilian Portuguese Female",{rate: 1.2,pitch:1,volume:5,onstart:function(){

						},onend:function(){
							$( "#dolly" ).animate({left:window.dollyposition.left,top: window.dollyposition.top},300)
						}
					});
				})
		}
	},
	ajax:function(url){
		return {
			url: url,
			thisMethod: "POST",
			thisBeforeSend: null,
			thisAfterSend: null,
			thisSend: null,
			thisSucess: null,
			thisError: null,
			thisDone: null,
			thisResponseText: null,
			thisResponseURL: null,
			method:function(e){			this.thisMethod		=e;return this;},
			onBeforeSend:function(e){	this.thisBeforeSend	=e;return this;},
			onAfterSend:function(e){	this.thisAfterSend	=e;return this;},
			onSend:function(e){			this.thisSend		=e;return this;},
			onSucess:function(e){		this.thisSucess		=e;return this;},
			onError:function(e){		this.thisError		=e;return this;},
			onDone:function(e){			this.thisDone		=e;return this;},
			go:function(){
				if(this.thisMethod != 'POST' && this.thisMethod != 'GET') {
					ws.log.error("ws->ajax->method : Valor invalido");return false;
				}
				if(this.url == '' || this.url == null || this.url == 'undefined') {
					ws.log.error("ws->ajax(null) : Valor invalido");return false;
				}
				var this_xhr	= this;
				var xhr			= new XMLHttpRequest();
				xhr.onreadystatechange = function() {
	   				if (xhr.readyState==1 && typeof this_xhr.thisSend == "function" ) {
						this_xhr.thisSend(xhr);
					}
					if(xhr.readyState==3  && typeof this_xhr.thisAfterSend == "function"){	
						this_xhr.thisAfterSend(xhr);
					}
					if(xhr.status === 200 && xhr.readyState==4   && typeof this_xhr.thisSucess == "function"){	
						this_xhr.thisSucess(xhr);
					}
					if(xhr.readyState==xhr.DONE && typeof this_xhr.thisDone == "function"){	
						this_xhr.thisDone(xhr);
					}
				}
				if(typeof this.thisBeforeSend == "function") {this.thisBeforeSend();}
				xhr.open(this.thisMethod,this.url, true);
				xhr.send();	
				return this;	
			},
			responseText:function(e){	return this.thisResponseText;},
			responseURL:function(e){	return this.thisResponseURL;}
		}
	},
	form: {
		sendLeads: function(token = null) {
			return {
				form: null,
				token: token,
				thisBefore: null,
				thisajaxSend: null,
				thissuccess: null,
				thiserror: null,
				thiscomplete: null,
				thisUploadProgress: null,
				thisdone: null,
				verifyFn: null,
				thisCaptcha: null,
				errCa: null,
				setForm: function(data) {
					this.form = data;
					return this;
				},
				errorCaptcha: function(data) {
					this.errCa = data;
					return this;
				},
				setCaptcha: function(data) {
					if(ws.$(data).length) {
						this.thisCaptcha = data;
					} else {
						ws.log.error("ws->form->sendLeads->setCaptcha : input inválido ou inexistente");
						return false;
					}
					return this;
				},
				verify: function(data) {
					this.verifyFn = data;
					return this;
				},
				setToken: function(data) {
					this.token = data;
					return this;
				},
				beforeSend: function(data) {
					this.thisBefore = data;
					return this;
				},
				ajaxSend: function(data) {
					this.thisajaxSend = data;
					return this;
				},
				success: function(data) {
					this.thissuccess = data;
					return this;
				},
				error: function(data) {
					this.thiserror = data;
					return this;
				},
				complete: function(data) {
					this.thiscomplete = data;
					return this;
				},
				uploadProgress: function(data) {
					this.thisUploadProgress = data;
					return this;
				},
				go: function(data) {
					var escope_this = this;
					if(!ws.exists.dom(this.form)) {
						ws.log.error("ws->form->sendLeads : Formulário inválido ou inexistente");
						return false;
					}
					if(this.token == null) {
						ws.log.error("ws->form->sendLeads->Token : Valor 'null' é invalido");
						return false;
					}
					if(escope_this.thiserror !== null && typeof escope_this.thiserror !== "function") {
						ws.log.error("ws->form->sendLeads->error : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}
					if(escope_this.thisBefore !== null && typeof escope_this.thisBefore !== "function") {
						ws.log.error("ws->form->sendLeads->beforeSend : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}
					if(escope_this.thisUploadProgress !== null && typeof escope_this.thisUploadProgress !== "function") {
						ws.log.error("ws->form->sendLeads->uploadProgress : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}
					if(escope_this.thiscomplete !== null && typeof escope_this.thiscomplete !== "function") {
						ws.log.error("ws->form->sendLeads->complete : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}

					function verifyCaptcha(envia) {
						var http = new XMLHttpRequest();
						var url = ws.includePath+"/ws-leads/";
						var params = "typeSend=captcha&keyCode=" + ws.$(escope_this.thisCaptcha)[0].value;
						http.open("POST", url, true);
						http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						http.onreadystatechange = function() {
							if(http.readyState == 4) {
								if(http.status == 200) {
									//if (typeof envia == "function") {envia(http);}
								}
							}
						}
						http.send(params);
					}

					function veryFyCaptcha(){
						$(escope_this.form).unbind("submit").bind("submit",function(e){
							e.preventDefault();
							var http = new XMLHttpRequest();
							var url = ws.includePath+"/ws-leads/";
							var params = "typeSend=captcha&keyCode=" + ws.$(escope_this.thisCaptcha)[0].value;
							http.open("POST", url, true);
							http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							http.send(params);
							http.onreadystatechange = function() {
								if(http.readyState == 4) {
									if(http.status == 200) {
										if(http.responseText==0 || http.responseText=="0"){
											escope_this.errCa()

											if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}

										}else{
											goForm(true);
										}
									}
								}
							}
							return false;
						})
					}

					function goForm(direct=false){
						$(escope_this.form).unbind("submit").ajaxForm({
							type: "POST",
							forceSync:true,
							error: function(error) {
								if(escope_this.thiserror !== null) {
									escope_this.thiserror(error)
									if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
								}
							},
							beforeSerialize: function($form, options) { 
									 if(escope_this.verifyFn != null) {
									 	var response = escope_this.verifyFn();
									 	if(escope_this.thiserror != null && response != true) {
									 		escope_this.thiserror(response);
									 		if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
									 		return false;
									 	}
									 }
							},
							beforeSubmit: function(xhr) {},
							beforeSend: function(xhr) {
								if(escope_this.thisBefore !== null) {
									escope_this.thisBefore(xhr)
									return false;
								}										
							},
							uploadProgress: function(event, position, total, percentComplete, myForm) {
								if(escope_this.thisUploadProgress !== null) {
									escope_this.thisUploadProgress(percentComplete,total)
								}
							},
							complete: function(e) {
								if(escope_this.thiscomplete !== null) {
									escope_this.thiscomplete(e);
									if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
								}
							}
						})
						if(direct==true){$(escope_this.form).submit();}
					} 

					if(window.ajaxFormInclude != true) {
						ws.load.script({
							file: ws.rootPath+'/admin/ws-modules/ws-register-leads/AjaxForm.min.js',
							return: function() {
								if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
							}
						})
					}else{
						if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
					}
				},
			};
		},
		input: function(input) {
			if(typeof input !== "string") {
				ws.log.error("ws->form->input : entrada inválida, utilize strings como selectores '#' ou '.'");
				return false;
			}
			if(!ws.exists.dom(input)) {
				ws.log.error("ws->form->input : input inválido ou inexistente");
				return false;
			}
			return {
				thisinput: input,
				is_email: function() {
					var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if(ws.$(this.thisinput).length) {
						this.thisinput = ws.$(this.thisinput)[0].value;
					}
					return regex.test(this.thisinput);
				},
				is_blank: function() {
					if(ws.$(this.thisinput).length) {
						this.thisinput = ws.$(this.thisinput)[0].value;
					}
					if(this.thisinput == "") {
						return true;
					} else {
						return false;
					}
				},
			}
		}
	},
	cookie: {
		set: function(name, value, days) {
			if(days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				var expires = "; expires=" + date.toGMTString();
			} else var expires = "";
			document.cookie = name + "=" + value + expires + "; path=/";
		},
		get: function(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while(c.charAt(0) == ' ') c = c.substring(1, c.length);
				if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		},
		erase: function(name) {
			ws.cookie.set(name, "", -1);
		}
	},
	insert: {
		css: function(documento, id, Media) {
			if(ws.$('#' + id).length) {
				ws.$('#' + id).remove()
			}
			var script = document.createElement('link');
			script.id = id;
			script.rel = 'stylesheet';
			script.type = 'text/css';
			script.media = Media;
			script.href = documento;
			var s = document.getElementsByTagName('link')[0];
			s.parentNode.insertBefore(script, s);
			return true;
		},
		js: function(documento, id = null, reload = false) {
			if(id == null) {
				ws.log.error("ws.insert.js: faltou id");
				return false;
			}
			if(!reload) {
				reload = false;
			}
			if(!ws.$('script#' + id).length) {
				var script = document.createElement('script');
				script.id = id;
				script.type = 'text/javascript';
				script.src = documento;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(script, s);
			} else if(ws.$('script#' + id).length && reload == true) {
				ws.log.info("Substituindo arquivo .JS: \n  #" + id + " : " + documento)
				$('script#' + id).remove();
				var script = document.createElement('script');
				script.id = id;
				script.type = 'text/javascript';
				script.src = documento;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(script, s);
			}
			return true;
		}
	},
	mouse: function(input) {
		return {
			cursor: "default",
			target: "*",
			setTarget: function(target = null) {
				this.target = target;
				return this;
			},
			disableContextMenu: function(action) {
				ws.verify.jquery();
				$(this.target).bind("contextmenu", function(event) {
					event.preventDefault();
					action();
				});
				return this;
			},
			setCursor: function(cursor = "*") {
				ws.verify.jquery();
				if(cursor == "*") {
					ws.log.warn("Não foi setado o target. Por default está como '*' ")
				}
				$(this.target).css({
					'cursor': cursor
				});
				return this;
			},
			disableDrop: function(action) {
				ws.verify.jquery();
				$(this.target).on({
					dragover: function(e) {
						e.preventDefault();
						action();
						return false;
					},
					drop: function(e) {
						e.preventDefault();
						action();
						return false;
					}
				});
			}
		}
	},
	encode: {
		crypt: function(plaintext, password) {
			if(typeof password=='undefined'){password='123';}
		    if (plaintext.length == 0) return ('');
		    var v = ws.string.strToLongs(ws.encode.utf8(plaintext));
		    if (v.length <= 1) v[1] = 0;
		    var k = ws.string.strToLongs(ws.encode.utf8(password).slice(0, 16));
		    var n = v.length;
		    var z = v[n - 1],
		        y = v[0],
		        delta = 0x9E3779B9;
		    var mx, e, q = Math.floor(6 + 52 / n),
		        sum = 0;
		    while (q-- > 0) {
		        sum += delta;
		        e = sum >>> 2 & 3;
		        for (var p = 0; p < n; p++) {
		            y = v[(p + 1) % n];
		            mx = (z >>> 5 ^ y << 2) + (y >>> 3 ^ z << 4) ^ (sum ^ y) + (k[p & 3 ^ e] ^ z);
		            z = v[p] += mx;
		        }
		    }
		    var ciphertext = ws.string.longsToStr(v);
		    return ws.encode.base64(ciphertext);
		},
		utf8: function(e) {
			e = e.replace(/\r\n/g, "\n");
			var t = "";
			for(var n = 0; n < e.length; n++) {
				var r = e.charCodeAt(n);
				if(r < 128) {
					t += String.fromCharCode(r)
				} else if(r > 127 && r < 2048) {
					t += String.fromCharCode(r >> 6 | 192);
					t += String.fromCharCode(r & 63 | 128)
				} else {
					t += String.fromCharCode(r >> 12 | 224);
					t += String.fromCharCode(r >> 6 & 63 | 128);
					t += String.fromCharCode(r & 63 | 128)
				}
			}
			return t;
		},
		base64: function(e) {
			var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var t = "";
			var n, r, i, s, o, u, a;
			var f = 0;
			e = ws.encode.utf8(e);
			while(f < e.length) {
				n = e.charCodeAt(f++);
				r = e.charCodeAt(f++);
				i = e.charCodeAt(f++);
				s = n >> 2;
				o = (n & 3) << 4 | r >> 4;
				u = (r & 15) << 2 | i >> 6;
				a = i & 63;
				if(isNaN(r)) {
					u = a = 64
				} else if(isNaN(i)) {
					a = 64
				}
				t = t + _keyStr.charAt(s) + _keyStr.charAt(o) + _keyStr.charAt(u) + _keyStr.charAt(a)
			}
			return t;
		},
		md5:function(s){function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]| (G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k="",F="",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F="0"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,"n");var d="";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()}
	},
	decode: {
		crypt : function(ciphertext, password) {
			if(typeof password=='undefined'){password='123';}
		    if (ciphertext.length == 0) return ('');
		    var v = ws.string.strToLongs(ws.decode.base64(ciphertext));
		    var k = ws.string.strToLongs(ws.decode.utf8(password).slice(0, 16));
		    var n = v.length;
		    var z = v[n - 1],
		        y = v[0],
		        delta = 0x9E3779B9;
		    var mx, e, q = Math.floor(6 + 52 / n),
		        sum = q * delta;
		    while (sum != 0) {
		        e = sum >>> 2 & 3;
		        for (var p = n - 1; p >= 0; p--) {
		            z = v[p > 0 ? p - 1 : n - 1];
		            mx = (z >>> 5 ^ y << 2) + (y >>> 3 ^ z << 4) ^ (sum ^ y) + (k[p & 3 ^ e] ^ z);
		            y = v[p] -= mx;
		        }
		        sum -= delta;
		    }
		    var plaintext = ws.string.longsToStr(v);
		    plaintext = plaintext.replace(/\0+$/, '');
		    return ws.decode.utf8(plaintext);
		},
		utf8: function(e) {
			var t = "";
			var n = 0;
			var r = c1 = c2 = 0;
			while(n < e.length) {
				r = e.charCodeAt(n);
				if(r < 128) {
					t += String.fromCharCode(r);
					n++
				} else if(r > 191 && r < 224) {
					c2 = e.charCodeAt(n + 1);
					t += String.fromCharCode((r & 31) << 6 | c2 & 63);
					n += 2
				} else {
					c2 = e.charCodeAt(n + 1);
					c3 = e.charCodeAt(n + 2);
					t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
					n += 3
				}
			}
			return t;
		},
		base64: function(e) {
			var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var t = "";
			var n, r, i;
			var s, o, u, a;
			var f = 0;
			e = e.replace(/[^A-Za-z0-9\+\/\=]/g, "");
			while(f < e.length) {
				s = _keyStr.indexOf(e.charAt(f++));
				o = _keyStr.indexOf(e.charAt(f++));
				u = _keyStr.indexOf(e.charAt(f++));
				a = _keyStr.indexOf(e.charAt(f++));
				n = s << 2 | o >> 4;
				r = (o & 15) << 4 | u >> 2;
				i = (u & 3) << 6 | a;
				t = t + String.fromCharCode(n);
				if(u != 64) {
					t = t + String.fromCharCode(r)
				}
				if(a != 64) {
					t = t + String.fromCharCode(i)
				}
			}
			t = ws.decode.utf8(t);
			return t;
		}
	},
	preload:{
		open:function(opcoes){
			var options = ws.extend({string: "",preload:"modal_preload"}, opcoes);
			if(options.string==""){
					var heightModal = "10px";
					var topGif = "35px";
			}else{
					var topGif = "53px";
					var heightModal = "auto";
			}
			ws.confirm({
				conteudo:options.string+"<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top:"+topGif+";background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
				idModal:options.preload,
				width:250,
				botclose: false,
				height: heightModal,
				maxHeight: 600,
				minHeight: 0
			});
		},
		close:function(opcoes){
				var options = ws.extend({preload:"modal_preload"}, opcoes);
				$("#"+options.preload).remove();
				if(!ws.exists.dom('.ws_popup_confirm')){
					$("#ws_confirm").remove();
					$("#body").removeClass("scrollhidden");
					$("*").removeClass("blur");
				}
		}
	},
	alert: {
		top: function(opcoes) {
			ws.verify.jquery();
			var options = ws.extend({
				mensagem	: "Mensagem padrão",
				clickclose	: true,
				height		: 20,
				position: "fixed",
				botClose: null,
				onClose: function() {},
				posFn: function() {},
				timeoutFn: function() {},
				timer: 3000,
				type: null,
				styleText: null,
				classText: null,
				color: "#E04E1F",
				background: "#F3DB7A",
				bottomColor: "#F5C814",
			}, opcoes);

			if(!ws.exists.dom("#avisoTopo")){$("body").prepend('<div id="avisoTopo"></div>');}

			if(options.clickclose == true) {
				$('#avisoTopo').unbind("click").click(function() {
					$(this).animate({
						height: 0,
						"padding": 0
					}, 200, 'linear');
					options.onClose();
				})
			}
			clearTimeout(window.recolheTopAlert);
			$('#avisoTopo').animate({
				height: 0,
				'padding': 0
			}, 200, 'linear', function() {
				if(options.type == 1) {
					options.color 		= "#E04E1F";
					options.background 	= "#F3DB7A";
					options.bottomColor = "#F5C814";
				}
				if(options.type == 2) {
					options.color 		= "#FFF";
					options.background 	= "#D4250D";
					options.bottomColor = "#990600";
				}
				if(options.type == 3) {
					options.color 		= "#FFF";
					options.background 	= "#85BE47";
					options.bottomColor = "#439900";
				}
				if(options.type == 4) {
					options.color 		= "#FFF";
					options.background 	= "#61A8D8";
					options.bottomColor = "#003D99";
				}
				$('#avisoTopo').css({
					"top": 0,
					"lef": 0,
					"width": "100%",
					"zIndex": 1000,
					"position": options.position,
					"background": options.background,
					"color": options.color,
					"border-bottom-color": options.bottomColor,
					"overflow": "hidden"
				});
				if(options.styleText == null && options.classText == null) {
					$('#avisoTopo').html(options.mensagem);
				} else {
					if(options.styleText == null) {
						options.styleText = "";
					} else {
						options.styleText = options.styleText.split('"').join('\"');
					}
					if(options.classText == null) {
						options.classText = "";
					} else {
						options.classText = options.classText.split('"').join('\"');
					}
					$('#avisoTopo').html("<div class=\"" + options.classText + "\" style=\"" + options.styleText + "\">" + options.mensagem + "</div>");
				}
				if(options.botclose != null && options.botclose != false) {

					$(options.botclose).unbind("click").click(function() {
						$("#avisoTopo").animate({
							height: 0,
							"padding": 0
						}, 200, 'linear');
						options.onClose()
					})
				}
				options.posFn();
				$('#avisoTopo').animate({
					height: options.height,
					"padding": 10
				}, 200, 'linear');
				window.recolheTopAlert = setTimeout(function() {
					$('#avisoTopo').animate({
						height: 0,
						"padding": 0
					}, 200, 'linear');
					options.timeoutFn();
				}, options.timer);
			});
		}
	},
	plugin: {
		contents: {}
	},
	exists: {
		file: function(opcoes) {
			ws.verify.jQuery();
			var options = ws.extend({
				file: null,
				success: function(e) {},
				error: function(e) {}
			}, opcoes);
			$.ajax({
				url: options.file,
				type: 'HEAD',
				success: function(e) {
					options.success(e)
				},
				error: function(e) {
					options.error(e)
				}
			});
		},
		dom: function(selector,debug) {
			if(!debug){debug=false;}
			if(typeof selector !== "string") {
				if(debug==true){
					ws.log.error("Entrada inválida, utilize strings como selectores '#' ou '.'");
				}
				return false;
			}

			if(ws.$(selector).length) {
				return true;
			} else {
				return false;
			}
		}
	},
	load: {
		json: function(url) {
			return{
				thisurl:url,
				thisonLoad:null,
				thisMethod: "POST",
				method:function(e){this.thisMethod=e;return this;},
				onLoad:function(e){
					if(typeof e =="function"){
						this.thisonLoad = e;
					}else{
						ws.log.error("load->json->onLoad : Valor invalido");
						return false;
					}
					return this;
				},
				url:function(e){
					if(typeof e =="string"){
						this.thisurl = e;
					}else{
						ws.log.error("load->json->setUrl : Valor invalido");
						return false;
					}
					return this;
				},
				go:function(){
					var this_ws = this;
					if(typeof this_ws.thisurl !="string"){
						ws.log.error("load->json(null) ou  load->json->setUrl(null) : Valor invalido");
						return false;
					}
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.open(this_ws.thisMethod, this_ws.thisurl, true);
					xmlhttp.onreadystatechange = function() {
					    if (xmlhttp.readyState == 4) {
					        if(xmlhttp.status == 200) {
					            var obj = JSON.parse(xmlhttp.responseText);
					            this_ws.thisonLoad(obj);
					         }
					    }
					};
					xmlhttp.send(null);
					return this;
				}

			}
		},
		script: function (source) {
			var js_script = document.createElement('script');
			js_script.type = "text/javascript";
			js_script.src = source;
			js_script.async = true;
			document.getElementsByTagName('head')[0].appendChild(js_script);
		}
	},
	searchList: function() {
		/*					ws.verify.jquery();
							var options = ws.extend({
								input 		: $(""),
								container 	: $(""),
								element 	: $(""),
								noResult 	: $(""),
							}, opcoes)


					  $(options.input).keyup(function(){
						var searchFTP = options.container;
						var texto 		= $(this).val().toUpperCase();
						var textSearch  = $(searchFTP).text().split("	").join("").split("\n").join("").split(" ").join("\n").toUpperCase()
						var resultGeral = textSearch.indexOf(texto);
						
						if(resultGeral > 0 && texto!="") {
							$(options.element).each(function(){
								var thistext 	= $(this).text().split("	").join("").split("\n").join("").split(" ").join("\n").toUpperCase();
								var resultado 	= thistext.indexOf(texto);
								if(resultado <= 0) {
								$(options.noResult).hide();
								$(this).hide();
								}else {
								$(this).show();
								}else{

								}
							}); 
						}else{
							$(options.noResult).show();
							$(searchFTP+" li").show();
						}              
					  });
		*/
	},
	is_on_screen: function(element) {
		var win = $(window);
		var viewport = {
			top: win.scrollTop(),
			left: win.scrollLeft()
		};
		viewport.right = viewport.left + win.width();
		viewport.bottom = viewport.top + win.height();
		var bounds = element.offset();
		bounds.right = bounds.left + element.outerWidth();
		bounds.bottom = bounds.top + element.outerHeight();
		return(!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
	},
	popup: function(url, w, h, s = "yes") {
		var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
		var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
		var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
		var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
		var left = ((width / 2) - (w / 2)) + dualScreenLeft;
		var top = ((height / 2) - (h / 2)) + dualScreenTop;
		var newWindow = window.open(url, null, 'scrollbars=' + s + ', width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	},
	getUrlVars: function(url) {
		url = url.replace(/&amp;/g, "ws_amp");
		var vars = {};
		var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
			vars[key] = value.replace(/ws_amp/g, "&amp;");
		});
		return vars;
	},
	downloadFile: function(opcoes) {
		var options = ws.extend({
			typeSend: "GET",
			file: null,
			newfile: null,
			abort: function(e) {},
			error: function(e) {},
			load: function(e) {},
			finish: function(e) {},
			progress: function(e) {}
		}, opcoes);
		if(options.file == null) {
			alert("Por favor, dê um nome ao arquivo...");
			return false
		}
		$.ajax({
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				xhr.responseType = "arraybuffer";
				xhr.addEventListener("abort", function() {
					options.abort()
				})
				xhr.addEventListener("error", function() {
					options.error()
				})
				xhr.addEventListener("loadend", function() {
					options.finish()
				})
				xhr.addEventListener("load", function() {
					var file_type = xhr.getResponseHeader('Content-Type');
					var disposition = xhr.getResponseHeader('Content-Disposition');
					if(disposition && disposition.indexOf('attachment') !== -1) {
						var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
						var matches = filenameRegex.exec(disposition);
						if(matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
					} else {
						filename = options.file.replace(/^.*[\\\/]/, '')
					}
					window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder || window.MSBlobBuilder;
					window.URL = window.URL || window.webkitURL;
					var arrayBufferView = new Uint8Array(this.response);
					var blob = new Blob([arrayBufferView], {
						type: file_type
					});
					var urlCreator = window.URL || window.BlobBuilder;
					var imageUrl = urlCreator.createObjectURL(blob);
					var a = document.createElement("a");
					document.body.appendChild(a);
					a.href = imageUrl;
					if(options.newfile != null) {
						a.download = options.newfile;
					} else {
						a.download = filename;
					}
					a.click();
					options.load()
				}, false);
				xhr.addEventListener("progress", function(evt) {
					if(evt.lengthComputable) {
						var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
						console.log(percentComplete)
						options.progress(percentComplete)
					}
				}, false);
				return xhr;
			},
			type: options.typeSend,
			url: options.file
		});
	},
	accordion: function(opcoes) {
		if(typeof opcoes === 'string') {
			var options = ws.extend({
				cabecalho: opcoes,
				closeOthers: true,
				closeBefore: true,
				beforeInit: function(){return true;},
				initOpen: null,
				initClose: null,
				finishOpen: null,
				finishClose: null,
			}, null);
		} else {
			var options = ws.extend({
				beforeInit: function(){return true;},
				closeOthers: true,
				closeBefore: true,
				cabecalho: null,
				initOpen: null,
				initClose: null,
				finishOpen: null,
				finishClose: null,
			}, opcoes);
		}
		if(options.closeBefore==true){$(options.cabecalho).next().slideUp("fast").removeClass('accordion-open');}

		/*########################################################*/
			function engineAccordion(cabecalho, container){

				if(options.closeOthers==true){
						if($(cabecalho).next().hasClass('accordion-open')){

							if(options.initClose!=null){options.initClose($(cabecalho),$(container))}
							$(options.cabecalho).next().slideUp("fast",function(){
								if(options.finishClose!=null){options.finishClose()}
							}).removeClass('accordion-open');;		
						}else{
							if(options.initClose!=null){options.initClose()}
							$(options.cabecalho).next().slideUp("fast",function(){
								if(options.finishClose!=null){options.finishClose()}
							}).removeClass('accordion-open');
							if(options.initOpen!=null){options.initOpen($(cabecalho),$(container))}
							$(cabecalho).next().slideDown("slow", function() {
							 	if(options.finishOpen!=null){options.finishOpen($(cabecalho),$(container))}
							}).addClass("accordion-open");
						}
				}else{

					if($(cabecalho).next().hasClass('accordion-open')){
						if(options.initClose!=null){options.initClose($(cabecalho),$(container))}
						$(cabecalho).next().slideUp("fast",function(){
							if(options.finishClose!=null){options.finishClose($(cabecalho),$(container))}
						}).removeClass('accordion-open');		
					}else{

						if(options.initOpen!=null){options.initOpen($(cabecalho),$(container))}
						$(cabecalho).next().slideDown("slow", function() {
						 	if(options.finishOpen!=null){options.finishOpen($(cabecalho),$(container))}
						}).addClass("accordion-open");
					}
				}
			}	
		$(options.cabecalho).unbind('click tap press').bind("click tap press", function() {
			var cabecalho 	= this;
			var container 	= $(this).next()
			if(options.beforeInit==true || options.beforeInit(cabecalho,container)){
				engineAccordion(cabecalho, container);
			}
		});
	},
	string: {
		strToLongs: function(s) {
		    var l = new Array(Math.ceil(s.length / 4));
		    for (var i = 0; i < l.length; i++) {
		        l[i] = s.charCodeAt(i * 4) + (s.charCodeAt(i * 4 + 1) << 8) + (s.charCodeAt(i * 4 + 2) << 16) + (s.charCodeAt(i * 4 + 3) << 24);
		    }
		    return l; 
		},
		longsToStr: function(l) {
		    var a = new Array(l.length);
		    for (var i = 0; i < l.length; i++) {
		        a[i] = String.fromCharCode(l[i] & 0xFF, l[i] >>> 8 & 0xFF, l[i] >>> 16 & 0xFF, l[i] >>> 24 & 0xFF);
		    }
		    return a.join('');
		},
		formatHTML: function(html) {
			function getIndent(level) {
				var result = '',
					i = level * 4;
				if(level < 0) {
					throw "Level is below 0";
				}
				while(i--) {
					result += ' ';
				}
				return result;
			}
			html = html.trim();
			var result = '',
				indentLevel = 0,
				tokens = html.split(/</);
			for(var i = 0, l = tokens.length; i < l; i++) {
				var parts = tokens[i].split(/>/);
				if(parts.length === 2) {
					if(tokens[i][0] === '/') {
						indentLevel--;
					}
					result += getIndent(indentLevel);
					if(tokens[i][0] !== '/') {
						indentLevel++;
					}
					if(i > 0) {
						result += '<';
					}
					result += parts[0].trim() + ">\n";
					if(parts[1].trim() !== '') {
						result += getIndent(indentLevel) + parts[1].trim().replace(/\s+/g, ' ') + "\n";
					}
					if(parts[0].match(/^(img|hr|br)/)) {
						indentLevel--;
					}
				} else {
					result += getIndent(indentLevel) + parts[0] + "\n";
				}
			}
			return result;
		},
		slashes: {
			add: function(str) {
				str = str.replace(/\\/g, '\\\\');
				str = str.replace(/\'/g, '\\\'');
				str = str.replace(/\"/g, '\\"');
				str = str.replace(/\0/g, '\\0');
				return str;
			},
			remove: function(str) {
				str = str.replace(/\\'/g, '\'');
				str = str.replace(/\\"/g, '"');
				str = str.replace(/\\0/g, '\0');
				str = str.replace(/\\\\/g, '\\');
				return str;
			}
		},
		random:function(length){
			if(!length){var length = 20;}
		 	return Array(length).fill("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz").map(function(x) { return x[Math.floor(Math.random() * x.length)] }).join('');
		}



	},
	confirm: function(opcoes) {
		ws.verify.jquery();
		var options = ws.extend({
			conteudo:'',
			width: 'calc(100% - 20px)',
			maxWidth: '1000px',
			minWidth: '300px',
			height: 'auto',
			maxHeight: 'initial',
			minHeight: 'unset',
			mleft: 0,
			mtop: 0,
			posFn: function() {},
			Init: function() {},
			posClose: function() {},
			bots: [
				// {
				// 		id			: "aceitar",
				// 		label		: "Aceitar",
				// 		class		: "",
				// 		style 		: "",
				// 		css 		: {"color":"#000"},
				//		ErrorCheck	: function() {},
				//		Check 		: function() {return true},
				// 		action		: function(){console.log("1111111")},
				// }
			],
			bot1: false,
			cancel: false,
			idModal: "ws_confirm",
			divScroll: "body",
			divBlur: "#menu_tools,#container,#header",
			drag: true,
			botclose: true,
			newFun: function() {},
			onCancel: function() {},
			onClose: function() {},
			Callback: function() {},
			ErrorCheck: function() {},
			Check: function() {
				return true
			}
		}, opcoes)
		options.Init();
		var BotClose = ""
		var ArryBotoes = "";
		var largBot = (100 / options.bots.length);
		var marBots = (options.bots.length * 5);
		var index_highest = 1000;
		$(".ws_popup_confirm").each(function() {
			var index_current = parseInt($(this).css("zIndex"), 10);
			if(index_current >= index_highest) {
				index_highest = index_current + 1;
			}
		});
		// MONTA OS BOTÕES DO ALERTA
		$.each(options.bots, function(index, value) {
			if(value.id){
				var id = value.id;
			}else{
				var id = "botConfirma_" + index + "_" + index_highest;
			}
			if(!value.class || value.class=="undefined"){value.class="";}
			if(!value.style || value.style=="undefined"){value.style="";}
			ArryBotoes += "<div id='" + id + "' class='botao " + value.class + "' style='width:calc(" + largBot + "% - 6px);margin: 0 2px;float: left;position: relative;padding: 10px 0;" + value.style + "'>" + value.label + "</div>\n";
		});
		// SE TIVER BOTÕES:
		if(options.bots.length > 0) {
			options.bot1 = false;
			options.cancel = false;
			var Botoes = "<div id='bottons' class='bottons'>" + ArryBotoes + "</div>";
		} else {
			if(options.bot1 == false) {
				var botao1 = ""
			} else {
				var botao1 = "<div id='aceitar' class='botao aceitar'>" + options.bot1 + "</div>"
			}
			if(options.cancel == false) {
				var botao2 = ""
			} else {
				var botao2 = "<div id='recusar' class='recusar'>" + options.cancel + "</div>"
			}
			if(options.bot1 == false && options.cancel == false) {
				var Botoes = "";
			} else {
				var Botoes = "<div id='bottons' class='bottons'>" + botao1 + botao2 + "</div>";
			}
			// if(options.bot1 == false && options.cancel == false && options.botFechar == false) {
			// 	var BotFechar = "<div id='close' class='botao close' >x</div>";
			// }
		}

		if($.type(options.idModal) === "string") {
			if(options.idModal.indexOf("#") == 0) {
				options.idModal = options.idModal.slice(1);
			} else {
				options.idModal = options.idModal;
			}
		} else {
			return false;
		}

		if(options.botclose == true) {
			var BotFechar = "<div class='botao close'>x</div>"
		} else {
			var BotFechar = ""
		}
		$("#" + options.idModal).remove();
		$('body').prepend("<div id='" + options.idModal + "' class='ws_popup_confirm' style='opacity:1;width:100%;height:100%;z-index:" + index_highest + "!important'><div class='body'>" + BotFechar+"<div class='ws_confirm_conteudo w1'>" + options.conteudo + "</div>" + Botoes + "</div></div>");
		$("#" + options.idModal + " .body").css({
			"width": options.width,
			"max-width": options.maxWidth,
			"min-width": options.minWidth,
			"height": options.height,
			"max-height": options.maxHeight,
			"min-height": options.minHeight
		});
		if(options.cancel == false) {
			$("#" + options.idModal + " .aceitar").css({
				"left": '50%',
				"transform": "translateX(-50%)"
			});
		}
		$(options.divScroll).addClass("scrollhidden");
		$("#" + options.idModal).fadeIn('fast', function() {});
		var closed = false
		options.posFn();
		$(options.divBlur).addClass("blur");
		$("#" + options.idModal + " .body").css({
			"cursor": 'default'
		})

		function closeAlert() {
			closed = true;
			$(options.divScroll).removeClass("scrollhidden");
			$(options.divBlur).removeClass("blur");
			$("#" + options.idModal).animate({ opacity: 0 }, 200, 'linear', function() { $("#" + options.idModal).remove() });
			options.posClose();
		}
		if($("#" + options.idModal + " .close").length > 0) {
			$("#" + options.idModal + " .close").click(function() {
				options.onClose();
				closeAlert();
			});
		}
		if(options.bots.length > 0) {
			$.each(options.bots, function(index, value) {
				if(value.id){
					var idBot = value.id;
				}else{
					var idBot = "botConfirma_" + index + "_" + index_highest;
				}
				if(value.css) {
					$("#"+idBot).css(value.css)
				}
				if(value.style) {
					var atualStyle = $("#"+idBot).attr("style");
					$("#"+idBot).attr("style", atualStyle+value.style);
				}
				$("#"+idBot).unbind("click").bind("click", function() {
					if(typeof(value.Check) == 'function') {
						if(value.Check() === true) {
							value.action();
							closeAlert();
						} else {
							value.ErrorCheck();
							return false;
						}
					} else {
						value.action();
						closeAlert();
					}
				});
			});
		} else {
			$("#" + options.idModal + " .recusar").click(function() {
				options.onCancel();
				closeAlert();
			});
			$("#" + options.idModal + " .aceitar").click(function() {
				if(options.Check() == true) {
					options.newFun();
					options.Callback();
					closeAlert();
				} else {
					options.ErrorCheck();
				}
			});
		}
	},
	getObjects: function(obj, key, val) {
		var objects = [];
		for(var i in obj) {
			if(!obj.hasOwnProperty(i)) continue;
			if(typeof obj[i] == 'object') {
				objects = objects.concat(getObjects(obj[i], key, val));
			} else if(i == key && obj[key] == val) {
				objects.push(obj);
			}
		}
		return objects;
	},
	copyObject: function (mainObj) {
		return JSON.parse(JSON.stringify(mainObj));
	},

	json_convert: function(e) {
		var arrayData, objectData;
		arrayData = e.serializeArray();
		objectData = {};
		$.each(arrayData, function() {
			var value;
			if(e.value != null) {
				value = e.value;
			} else {
				value = '';
			}
			if(objectData[e.name] != null) {
				if(!objectData[e.name].push) {
					objectData[e.name] = [objectData[e.name]];
				}
				objectData[e.name].push(value);
			} else {
				objectData[e.name] = value;
			}
		});
		return objectData;
	},
	$:function(selector, el){
	    return document.querySelectorAll(selector);
	},
	log: {
		log: function(message) {
			console.log(message);
		},
		error: function(message) {
			console.error(message);
		},
		info: function(message) {
			console.info(message);
		},
		warn: function(message) {
			console.warn(message);
		},
		clear: function() {
			console.clear();
		}
	},

	ace:{
		data:{
			timeout:{},
			template:{
				session:{
					id				: null,
					aceSess			: null,
					atual			: false,
					pathFile		: null,
					filename		: null,
					original		: null,
					stringFile		: null,
					extension		: null,
					setReadOnly		: false,
					status			: '',
				},
				instance:{
					atual:false,
					id:null,
					config:{
						engine				: null,
						enableTabs			: true,
						containerTabs		: null,
						dragableTabs		: false,
						templateTabs		: `<div class="ws-editor-aba"><div class="ws-editor-status"></div><div class="ws-editor-label">{string}</div><div class="ws-editor-close"></div></div>`,
						classDisabledTabs	: "disabled",
						classHoverTabs		: "hover",
						classActiveTabs		: "active",
					},
					sessions:[]
				},
			},	
			instances:[]
		},
		focus:function(){

		},
		abas:{
			setActive:function(id_instancia,id_session){
				// instancia = ws.ace.data.instances[id_instancia];
				// ws.$(instancia.config.containerTabs+" .ws-editor-aba").forEach(function(item,index){
				// 	if ( item.classList.contains('active')) {
				// 		item.classList.remove("active");
				// 	}
				// })
			},
			refreshFunctions:function(index_instancia){
				instancia = ws.ace.data.instances[index_instancia];
				instancia.sessions.forEach(function(item,index){
					if(instancia.config.enableTabs==true){
						document.getElementById(instancia.id+"_"+item.id).onclick = function(event) {
							ws.$(instancia.config.containerTabs+" .ws-editor-aba").forEach(function(item,index){
								if ( item.classList.contains('active')) {
									item.classList.remove("active");
								}
							})
							var tabAtual 	= this;
							var sessionTab 	= tabAtual.getAttribute("data-session");
							var instanceTab = tabAtual.getAttribute("data-instance");
							ws.ace.define.atualSession(instanceTab,sessionTab)
							tabAtual.classList.add("active");
						}
					}
				})	
			},
			refreshAbas:function(index_instancia){
				if(typeof index_instancia=="number"){
					var instance 		= ws.ace.data.instances[index_instancia];
					var config 			= instance.config;
				}else if(typeof index_instancia =="string"){
					var instance 		= ws.ace.get.instance(index_instancia)[0];
					var config 			= instance.config;
				}else{
					ws.log.error("Variável invalida: ws.ace.refreshAbas(string)");
					return false;
				}
				if(config.enableTabs==true){
					if(ws.exists.dom(config.containerTabs)){
						//repositorio das strings
						var allTabs 		= [];
						//capta o template das abas
						var templateTabs 	= config.templateTabs;
						//varremos as sessões da instancia atual
						instance.sessions.forEach(function(item,index){

							
							//CRIAMOS O ELEMENTO DA ABA
							var doc = new DOMParser().parseFromString(templateTabs.replace('{string}',item.id), 'text/html');
							var div = doc.body.firstChild;

							//ADICIONAMOS OS ATRIBUTOS
							div.setAttribute("id",  ws.ace.data.instances[index_instancia].id+"_"+item.id);
							div.setAttribute("data-session",  item.id);
							div.setAttribute("data-instance", index_instancia);
							console.log(item.status)
							div.classList.add(item.status);
							//E TRANSFORMAMOS ELE EM STRING NA ARRAY
							allTabs.push(div.outerHTML);
						})					
						// insere as abas
						var		el 	= ws.$(config.containerTabs);
								el[0].innerHTML= allTabs.join("\n");
								ws.ace.abas.refreshFunctions(index_instancia);	

								

					}else{
						ws.error("Elemento não encontrado \"containerAbas\": ",config.containerTabs);
					}
				}		
			},
		},
		set:{
			config:{
				instance:function(instance,config){
					if(typeof instance!="string" || typeof config!="object"){
						ws.log.error("Variável invalida: ws.ace.set.config.instance(string,object)");
						return false;
					}
					var newTemplate = ws.copyObject(ws.ace.data.template.instance.config);
					var newConfig 	= ws.extend(newTemplate,config);
					var inst 		= ws.ace.get.instance(instance)[0];
					inst.config 	= newConfig;
				},
				session:function(session,config,instancia){
					if(typeof session!="string" || typeof config!="object"){
						ws.log.error("Variável invalida: ws.ace.set.config.session(string,object)");
						return false;
					}
					var inst 		= ws.ace.get.session(instancia,session);
				}
			},
			instance: function(opt,config) {
				if(typeof opt!="string"){
					ws.log.error("Variável invalida: ws.ace.set.instance(string)");
					return false;
				}
				// ao setar uma nova instancia, caso não exista, cria uma nova 
				if (!ws.ace.verify.instance(opt)) {
					var newInstance 	= ws.copyObject(ws.ace.data.template.instance)
						newInstance.id 	= opt;
						ws.ace.data.instances.push(newInstance);
				}else{
					ws.error("Já existe uma instancia com o ID: \""+opt+"\" ")
				}
				ws.ace.define.atualInstance(opt);
				if(typeof config=="object"){
					ws.ace.set.config.instance(opt,config);
					ws.ace.get.atualInstance()[0].config.engine.on("focus", function(){
						console.info("Definindo instancia \""+opt+"\" como atual")
						ws.ace.define.atualInstance(opt)
					})
				}
			},
			session: function(instance,session) {
				if(!instance && !session){
					ws.log.error("Inserir INDEX ws.ace.set.session(instance,session)");
					ws.log.warn("Caso não tenha a instance, capta instance atual");
					return;
				}
				if(!session){
					session 	= instance;
					instance 	= ws.ace.get.atualInstance()[0].id;
				}
				if(typeof instance!="string"){
					ws.log.error("Variável invalida: ws.ace.set.session(instance(string),session(object))");
					return false;
				}
				if(typeof session!="object"){
					ws.log.error("Variável invalida: ws.ace.set.session(instance(string),session(object))");
					return false;
				}

				var	session = ws.copyObject(ws.extend(ws.ace.data.template.session, session));
				if(session.id==null){ session.id = ws.string.random(20); }
		 		var session_id 	= session.id;
				// Cria uma nova sessão 
				if (ws.ace.verify.session({instance:instance,session:session_id})==false) {
					// captamos os dados da sessão selecionada
				 		var instancia 			= ws.ace.get.instance(instance);
				 		var obj_instancia 		= instancia[0];
				 		var index_instancia 	= instancia[1];

						if(session.extension=="txt"){session.extension="text";}
						if(session.extension=="js"){session.extension="javascript";}
						if(session.aceSess==null){ 
							// SETAMOS  O ACE EDITOR
							session.aceSess 	= ace.createEditSession(session.stringFile, "ace/mode/" + session.extension);
						}

						// INSERIMOS NA CLASSE
						ws.ace.data.instances[index_instancia].sessions.push(session)

						// JÁ CRIAMOS COMO SESSÃO ATUAL
						ws.ace.define.atualSession(obj_instancia.id,session_id)


						// agora que criou a sessão verificamos se a instancia possúi abas habilitadas 
						if(ws.ace.data.instances[index_instancia].config.enableTabs==true){
							ws.ace.abas.refreshAbas(index_instancia);
						}


				}else{
					//caso já tenha uma sessão com esse id
					ws.log.error("Já existe uma sessão com esse ID: ",session_id);
				}
			},
		},
		verify:{
			instance:function(opt){
				if(typeof opt=='number'){
					if(typeof ws.ace.data.instances[opt]=='object'){
						return true;
					}else{
						return false;
					}
				}else if (typeof opt=='string'){
					var retorno = false;
					ws.ace.data.instances.forEach(function(item,index){
						if(ws.ace.data.instances[index].id == opt){
							retorno = true;
							return false;
						}
					})
					return retorno;
				}else{
					console.error("Variável inválida em ws.ace.verify.instance(",opt,")");
					return false;
				}
			},
			session:function(options){
				var options = ws.extend({instance:null,session:null},options);
				if(	
					options.instance==null					|| 
					options.session==null					|| 
					typeof options.instance	!=	'string'	|| 
					typeof options.session	!=	'string' 
					){
					ws.error("Inserir INDEX ws.ace.verify.session({instance:STRING,session:STRING})");
				}

				var instance = ws.ace.get.instance(options.instance)[0];

				if(typeof instance != "object"){
					ws.log.info("Não encontramos essa instancia para validar a sessão."); 
					return false;
				}
				var session  = ws.ace.get.session(instance.id,options.session);
				if(typeof session != "object"){
					return false;
				}
				if(typeof session == "object"){
					return true;
				}
			}
		},
		define:{
			atualInstance: function(opt) {
					var exists = false;
					ws.ace.data.instances.forEach(function(item,index){
						var objeto = ws.ace.data.instances[index]
						if(objeto.id == opt){
							objeto.atual = true;
							exists = true;
						}else{
							objeto.atual = false;
						}
					})
					if(exists==false){
						ws.log.error("Não existe uma sessão com esse ID: "+opt);
						return false;
					}else{
						return true;
					}
			},


			atualSession: function(instance,session) {
				if(!instance && !session){
					ws.log.error("Inserir INDEX ws.ace.set.atualSession(instance,session)");
					ws.log.warn("Caso não tenha a instance, capta instance atual");
					return;
				}
				if(typeof instance	!=	'string' || typeof session	!=	'string'){
					ws.log.error("Variáveis inválidas: ws.ace.set.atualSession(string,string)",instance,session);
					return;
				}
				indexInstance 	= ws.ace.get.instance(instance);
				indexSession 	= ws.ace.get.session(instance,session);
				if(ws.ace.data.instances.length==0){
					ws.error("Não existe uma instâncias ativas");
					return false;
				}
				if(ws.ace.data.instances[indexInstance[1]].sessions.length==0){
					ws.error("Não existe sessões nessa instancia: "+indexInstance[1]);
					return false;
				}
				ws.ace.data.instances.forEach(function(item,index){ 
					item.atual = false;
					item.sessions.forEach(function(sess,idx){ 
						sess.atual = false;
					})
				})

				var instancia 									= ws.ace.data.instances[indexInstance[1]];
					instancia.atual 							= true
					instancia.sessions[indexSession[1]].atual 	= true
				var sessaoAce 									= instancia.sessions[indexSession[1]].aceSess;
				var engine 										= instancia.config.engine;
					engine.setSession(sessaoAce);
			}

			,
		},
		get:{
			instances: function(){return ws.ace.data.instances;},
			instance: function(a){
				if(!a){
					return ws.ace.get.atualInstance()[0];
				}else{
					ws.ace.data.instances.forEach(function(item,index){
						 if(item.id==a || index==a){
							a = [item,index];
							return false;
						 }
					})
					return a;
				}
			},
			sessions: function(instance){

				if(!instance ){
					var instance = ws.ace.get.atualInstance()[1];
				}
				return ws.ace.data.instances[instance].sessions;
			},
			session: function(instance,session){
				// verifica se tem pelo menos 1 valor preenchido
				if(!instance && !session ){ws.log.error("Insira um índice: ws.ace.get.session(instance,session)")}
				// validamos a instancia
				if(!session){
					instance 	= ws.ace.get.atualInstance()[0];
				}else{
					if(typeof instance=="string"){
						instance 	= ws.ace.get.instance(instance)[0];
					}else if(typeof instance=="number"){
						instance 	= ws.ace.data.instances[instance];
					}else{
						instance 	= ws.ace.get.atualInstance()[0];
					}
				}
				// validamos a sessão
				if(!session){ var session = instance; }else{var session = session;}
				//verifica se existe sessões antes de varrer
				if(typeof instance!="object"){
					ws.log.error("A Instância invalida.");
					return false
				}
				if(instance.sessions.length<1){
					ws.log.error("A Instância \""+instance.id+"\" possúi (0) sessões.");
					return false
				}
				if(typeof session=="string"){
					retorno = false;
					instance.sessions.forEach(function(item,index){
						 if(item.id==session){
							retorno = [item,index];
							return false;
						 }
					})
					return retorno;
				}else if(typeof instance.sessions[session]=="object"){
					return instance.sessions[session];
				}else{
					ws.log.error("Insira um índice válido: ws.ace.get.session((string|number),(string))")
					return false;
				}
			},
			atualInstance: function(){
				var retorno = {}
				ws.ace.data.instances.forEach(function(item,index){
					 if(item.atual==true){
						retorno = [item,index];
						return false;
					 }
				})
				return retorno;
			},
			atualSession: function(){
				var retorno = {}
				ws.ace.get.atualInstance()[0].sessions.forEach(function(item,index){
					 if(item.atual==true){
						retorno = [item,index];
						return false;
					 }
				})
				return retorno;
			}
		},
		delete:{},
	}
}
/*

ws.log.clear();
ws.ace.set.instance("primeiraInstancia");
ws.ace.set.config.instance("primeiraInstancia",{containerAbas:".containerAba1"});

ws.ace.set.instance("outraInstancia");
ws.ace.set.config.instance("outraInstancia",{containerAbas:".containerAba2"});


ws.ace.set.session('outraInstancia',{id:"opt1"})
ws.ace.set.session('outraInstancia',{id:"opt2"})
ws.ace.set.session('outraInstancia',{id:"opt3"})

ws.ace.set.session("primeiraInstancia",{id:"opt1"})
ws.ace.set.session("primeiraInstancia",{id:"opt2"})
ws.ace.set.session("primeiraInstancia",{id:"opt3"})
ws.ace.set.session("primeiraInstancia",{id:"opt4"})
ws.ace.set.session("primeiraInstancia",{id:"opt5"})


//ws.ace.set.config.instance("minha-instancia1",{id:"session01"});
//ws.ace.set.config.session("quem-somos",{id:"session-quem-somos-new-id"});

//ws.ace.set.session({id: "quem-somos"})
// ws.ace.set.session({id: "produtos"})
// ws.ace.set.session({id: "aaaaa"})
// ws.ace.set.session({id: "bbbbb"})

/*
	ws.ace.get.instances();
	ws.ace.get.sessions();
	ws.ace.define.atualSession("quem-somos")
	ws.ace.get.sessions();
	ws.ace.define.atualSession("dfkgjlsdjg")
*/