<?php

	############################################################################################################################################
	# ESTE É PRATICAMENTE O 1° ARQUIVO A SER ABERTO NO SISTEMA, QUE É A TELA DE INSTALAÇÃO
	############################################################################################################################################
	#
	#			SIM!!! AINDA NÃO ESTÁ EM MVC !
	#			Em breve cuidarei disso!
	#
	############################################################################################################################################	
	############################################################################################################################################	



	############################################################################################################################################
	# 	FUNÇÕES GLOBAIS DO SISTEMA
	############################################################################################################################################
		 include_once(realpath(dirname(__FILE__)).'/../lib/ws-globals-functions.php');

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
			$path = implode(array_filter(explode('/',$path)),"/");
			define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
		}

		if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",normalizePath(realpath(dirname(__FILE__)).'/../../../'));}
		if(!defined("ADMIN_PATH")){define("ADMIN_PATH",basename(str_replace(['\\','/app/core'],['/',''],dirname(__FILE__))));}
		
	############################################################################################################################################
	# CASO ESTE ARQUIVO SEJA INVOCADO COM A FUNÇÃO DE INSTALAÇÃO EXECUTA
	############################################################################################################################################
		if (isset($_POST['function']) && $_POST['function'] == "createWsConfig") {

			############################################################################################################################################
			# TRANSFORMA O POST DO FORMULARIO EM ARRAY
			############################################################################################################################################
				parse_str($_POST['form'], $data);

			############################################################################################################################################
			# SEPARA AS VARIÁVEIS PARA GRAVAÇÃO DO ARQUIVO
			############################################################################################################################################
				$isso= Array(
					"{DOMINIO}",
					"{PREFIX_TABLES}",
					"{NOME_BD}",
					"{USUARIO_BD}",
					"{SENHA_BD}",
					"{SERVIDOR_BD}",
					"{RECAPTCHA}",
					"{LANG}",
					'{{domain}}',
					'{{INCLUDE_PATH}}',
					'{{ROOT_WEBSHEEP}}',
					);
				$porisso= Array(
					str_replace(PHP_EOL, "", $data['DOMINIO']),
					str_replace(PHP_EOL, "", strtolower($data['PREFIX_TABLES'])),
					str_replace(PHP_EOL, "", strtolower($data['NOME_BD'])),
					str_replace(PHP_EOL, "", $data['USUARIO_BD']),
					str_replace(PHP_EOL, "", $data['SENHA_BD']),
					str_replace(PHP_EOL, "", strtolower($data['SERVIDOR_BD'])),
					str_replace(PHP_EOL, "", $data['RECAPTCHA']),
					$data['LANG'],
					$_SERVER['HTTP_HOST'],
					INCLUDE_PATH,
					ROOT_WEBSHEEP,
				);
				
				$isso_Password = array(
									'{ID_SESS}',
									'{NAME_SESS}',
									'{TOKEN_DOMAIN}',
									'{TOKEN_ACCESS}',
									'{TOKEN_USER}',
									'{AUTH_KEY}',
									'{SECURE_AUTH_KEY}',
									'{LOGGED_IN_KEY}',
									'{NONCE_KEY}',
									'{AUTH_SALT}',
									'{SECURE_AUTH_SALT}',
									'{LOGGED_IN_SALT}',
									'{NONCE_SALT}'
								);
				$ashTokens     = Array();

			############################################################################################################################################
			# GERA OS TOKENS DE ACESSO DO CONFIG
			############################################################################################################################################
			foreach ($isso_Password as $value) {
				$ashTokens[] = createPass(rand(15, 20));
			}

			##########################################################################################################################################
			# PEGAMOS A STRING O ARQUIVO CONFIG BASE
			##########################################################################################################################################
			$wsConfigDefault         = file_get_contents(INCLUDE_PATH.'/'.ADMIN_PATH.'/app/config/ws-config-default.php');

			##########################################################################################################################################
			# FORMATAMOS O CONTEUDO DELE
			##########################################################################################################################################
			$wsConfigDefaultFormated = str_replace($isso, $porisso, $wsConfigDefault);
			$wsConfigDefaultFormated = str_replace($isso_Password, $ashTokens, $wsConfigDefaultFormated);

			#######################################################################################################################################
			# GRAVAMOS O NOVO ARQUIVO ws-config.php E RETORNAMOS O RESULTADO
			#######################################################################################################################################
			if (!file_put_contents(INCLUDE_PATH.'ws-config.php', $wsConfigDefaultFormated)) {
				echo json_encode(array(
					'status' => 'falha',
					'resposta' => 'Não foi possível gravar o ws-config.php'
				));
			} else {
				echo json_encode(array(
					'status' => 'sucesso',
					'resposta' => 'ws-config.php criado com sucesso!'
				));
			}
			exit;
		}


	############################################################################################################################################
	# CASO ESTE ARQUIVO SEJA INVOCADO COM A FUNÇÃO DE VALIDAÇÃO DE CONEXÃO DO MYSQL
	############################################################################################################################################
		if (isset($_POST['function']) && $_POST['function'] == "testMySQL") {
			@mysqli_connect($_POST['SERVIDOR_BD'], $_POST['USUARIO_BD'], $_POST['SENHA_BD'], $_POST['NOME_BD']);
			if (mysqli_connect_errno()){
				echo "0";
			} else {
				echo "1";
			}
			exit;
		}

	############################################################################################################################################
	# IMPORTAMOS AGORA O ARQUIVO RESPONSAVEL POR CRIAR TODA ESTRUTURA DE DIRETORIOS E ARQUIVOS INICIAIS
	############################################################################################################################################
		include_once(realpath(dirname(__FILE__)).'/../lib/ws-set-estructure.php');

?>
<html lang="pt-br" class='bgradial01' id="html">
<head>
<meta charset="UTF-8">
<link type="image/x-icon" href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/img/websheep/favicon.ico" rel="shortcut icon" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link	type="text/css" media="all"		rel="stylesheet" 						href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/css/websheep/estrutura.min.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/css/websheep/desktop.min.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/css/websheep/install.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/css/websheep/funcionalidades.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/css/fontes/fonts.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/css/websheep/theme_blue.min.css" />
<script type = 'text/javascript' 												 src="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/vendor/jquery/2.2.0/jquery.min.js"></script>
<script type = 'text/javascript' 												 src="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/js/websheep/funcionalidades.js"></script>

<script type = 'text/javascript'>
	$(document).ready(function(){
		$("input[readonly]").css({color:"#CCC"});
		$("input[data-conect='mysql']").on("keyup",function(){
			var NOME_BD 		=	$("input[name='NOME_BD']").val();
			var USUARIO_BD 		=	$("input[name='USUARIO_BD']").val();
			var SENHA_BD 		=	$("input[name='SENHA_BD']").val();
			var SERVIDOR_BD 	=	$("input[name='SERVIDOR_BD']").val();
			$.ajax({
				type: "POST",cache: false,url: "<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/core/ws-setup.php",
				data: {function:"testMySQL",NOME_BD:NOME_BD,USUARIO_BD:USUARIO_BD,SENHA_BD:SENHA_BD,SERVIDOR_BD:SERVIDOR_BD,},
				error: function (xhr, ajaxOptions, thrownError) {alert(xhr.status);alert(thrownError);}
			}).done(function(data) { 
				console.info(data);
				if(data=='1'){
					$("#formulario").removeClass("mysqlFail")
					$("input[name='NOME_BD'],input[name='USUARIO_BD'],input[name='SENHA_BD'],input[name='SERVIDOR_BD']").css({borderColor:"#b0d000",paddingLeft:33,'background-image':"url('<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/img/websheep/tick-circle.png')",'background-position':10,'background-repeat':"no-repeat"})
				}else{
					$("#formulario").addClass("mysqlFail")
					$("input[name='NOME_BD'],input[name='USUARIO_BD'],input[name='SENHA_BD'],input[name='SERVIDOR_BD']").css({borderColor:"#d03b00",paddingLeft:33,'background-image':"url('<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/img/websheep/cross.png')",'background-position':10,'background-repeat':"no-repeat"})
				}
			});
		})
		window.criaWSConf = function(){
			var formulario = $("#formulario").serialize();
			$.ajax({
				type: "POST",
				cache: false,
				url: "<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/core/ws-setup.php",
			    beforeSend:function(){confirma({width:"auto",conteudo:"  Criando ws-config...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/img/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",drag:false,bot1:0,bot2:0})},
				data: {function:"createWsConfig", form:formulario},
			}).done(function(data) {
						objJSON = JSON.parse(data)
						if(objJSON.status=="sucesso"){
							$.ajax({
									type: "POST",
									cache: false,
									beforeSend:function(){confirma({width:"auto",conteudo:"  Configurando MySQL...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",drag:false,bot1:0,bot2:0})},
									url: "<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/ws-modules/ws-tools/functions.php",
									data: {function:"installSQLInit",formulario:formulario},
									error: function (xhr, ajaxOptions, thrownError) {alert(xhr.status);alert(thrownError);}
								}).done(function(data) {
									console.log(data);
									
									// if(data=="sucesso"){
									// 	location.reload();
									// }else{
									// 	confirma({
									// 		conteudo:data,
									// 		bot1:'Ok',
									// 		bot2:false,
									// 		drag:false,
									// 		botclose:false,
									// 		width:500
									// 	})
									// }
								});

						}else{
							console.error(objJSON);
						}
			})
		}

		$('#vamosla').click(function(){
			var formulario 		= 	$("#formulario").serialize();
			var NOME_BD 		=	$("input[name='NOME_BD']").val();
			var USUARIO_BD 		=	$("input[name='USUARIO_BD']").val();
			var SENHA_BD 		=	$("input[name='SENHA_BD']").val();
			var SERVIDOR_BD 	=	$("input[name='SERVIDOR_BD']").val();
			var CLIENT_NAME 	=	$("input[name='CLIENT_NAME']").val();
			var LOG_WEBMASTER 	=	$("input[name='LOG_WEBMASTER']").val();
			var PASS_WEBMASTER 	=	$("input[name='PASS_WEBMASTER']").val();

			if(CLIENT_NAME==""){		$("input[name='CLIENT_NAME']").addClass("error"); 		return false;}
			if(LOG_WEBMASTER==""){		$("input[name='LOG_WEBMASTER']").addClass("error"); 	return false;}
			if(PASS_WEBMASTER==""){		$("input[name='PASS_WEBMASTER']").addClass("error"); 	return false;}
			if(NOME_BD==""){			$("input[name='NOME_BD']").addClass("error"); 			return false;}
			if(USUARIO_BD==""){			$("input[name='USUARIO_BD']").addClass("error"); 		return false;}
			if(SERVIDOR_BD==""){		$("input[name='SERVIDOR_BD']").addClass("error"); 		return false;}

			if($("#formulario").hasClass("mysqlFail")){
				alert("Os dados de conexão a base estão incorretos.")
			}else{
				window.criaWSConf();
			}
		});
		$("#formulario input").focus(function() {
			$(this).removeClass("error");
		});
	});
</script>

<body id="body">
	<div id='avisoTopo'></div>
	<div id="container" style="width: 100%; left: 0; position: fixed; top: 0; height: 100%; overflow: auto;"> 
	<div id="conteudo">
		<div class="w1" style="border:solid 1px #CCC;position: relative;transform: translate(-50%,0);left: 50%;padding: 30px;width: calc(100% - 100px );float: left;top: 10px;">
			<div id='step0' style="position: relative;float: left;text-align: center;">

				<img src="<?=ROOT_WEBSHEEP.ADMIN_PATH?>/app/templates/img/websheep/logo_ws_install.jpg" style="width: 80px;">

				<div class="c"></div>
				<strong style="font-family: 'Titillium Web', sans-serif;font-size: 30px;line-height">Bem-Vindo(a) ao WebSheep!</strong><br>
				Notamos que você ainda nao tem um arquivo <strong>ws-config.php</strong><br>
				Nele irá todos os dados do servidor e banco de dados. preencha o formulário e clique em avançar.
				</p>
				<hr style="margin:20px;">
					<form id="formulario" class="mysqlFail">
						<div class="label" style="width: 100%;">Nome do cliente licenciado:</div>
						<div class="c"></div>
						<input	name="CLIENT_NAME" 	value="" placeholder="ex: Empresa LTDA" style="width: 100%;">


						<div class="c"></div>
						<div class="label" style="width: 50%;">Login do webmaster:</div>
						<div class="label" style="width: 50%;">Senha do webmaster:</div>
						<input	name="LOG_WEBMASTER" 	value="" 					placeholder="Login:" style="width: calc(50% - 5px);margin-left: 0;">
						<input	name="PASS_WEBMASTER" 	value="" 	type="password" placeholder="Senha:" style="width: calc(50% - 5px);margin-right: 0;">


						<div class="c"></div>
						<div class="label" style="width: 50%;">Nome do banco MySQL</div>
						<div class="label" style="width: 50%;">Nome do usuário MySQL</div>
						<input	data-conect="mysql" name="NOME_BD" 					value="" 		style="width: calc(50% - 5px);margin-left: 0;">
						<input	data-conect="mysql" name="USUARIO_BD" 				value="root" 	style="width: calc(50% - 5px);margin-right: 0;">


						<div style="width: 33%;" class="label">Senha do MySQL</div>
						<div style="width: 33%;" class="label">Nome do servidor MySQL</div>
						<div style="width: 33%;" class="label">Prefixo das tabelas</div>
						<input	style="width: calc(33.3333% - 5px);margin-left: 0;" 	data-conect="mysql" name="SENHA_BD"		type="password" value="">
						<input	style="width: calc(33.3333% - 5px);margin-right: 0;margin-left: 0" 				data-conect="mysql" name="SERVIDOR_BD"	value="localhost">
						<input	style="width: calc(33.3333% - 5px);margin-right: 0;" name="PREFIX_TABLES"	value="">
						<input	type="hidden" name="DOMINIO" 				value="<?= $_SERVER['HTTP_HOST'] ?>">
						<input	type="hidden" name="DOMINIO_SEC" 			value="<?= $_SERVER['HTTP_HOST'] ?>">



						<div style="width: 50%;text-align:left;" class="label">Token do Recaptcha do Google</div>
						<div style="width: 50%;text-align:left;" class="label">Idioma do sistema</div>
						<input	name="RECAPTCHA" value="" style="width: calc(50% - 5px);margin-left: 0;">
						<select name="LANG" style="width: calc(50% - 5px);margin-right: 0;"><?
							$pasta = INCLUDE_PATH.'/'.ADMIN_PATH.'/app/config/lang';
							if(is_dir($pasta)){
								$dh = opendir($pasta);
								while($diretorio = readdir($dh)){
									if($diretorio != '..' && $diretorio != '.'){
										$lang = str_replace('.json',"",$diretorio);
										echo '<option value="'.$lang.'">'.$lang.'</option>';
									}
								}
							}
							?>
						</select>
						<input	type="hidden" name="TOKEN_ACCESS" 			value="<?= _crypt() ?>"		readonly>
						<input	type="hidden" name="TOKEN_USER" 			value="<?= _crypt() ?>"		readonly>
						<input	type="hidden" name="TOKEN_DOMAIN" 			value="<?= _crypt() ?>" 	readonly>
						<input	type="hidden" name="ID_SESS" 				value="<?= _crypt(6)?>" 	readonly>
						<input	type="hidden" name="NAME_SESS" 				value="<?= _crypt(6)?>" 	readonly>
						<input	type="hidden" name="_PATCH_ADMIN_" 			value="<?=ADMIN_PATH?>"		readonly>
					</form>
					<div class="c"></div>
					<div class="step botao vamosla" id="vamosla" style="width: 100%;">Criar ws-config.php e continuar a instalação</div>
			</div>
		</div>
	</div>
</div>
</body>