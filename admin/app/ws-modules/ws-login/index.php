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
	
	############################################################################################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	error_reporting(E_ALL); 
	$licenseData =json_decode(file_get_contents(INCLUDE_PATH.'admin/app/templates/json/ws-update.json'));
?>

<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link type="image/x-icon" href="img/favicon.ico" rel="shortcut icon" />
<meta name="viewport"  content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/fontes/fonts.css" 											type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/ws-modules/ws-login/estrutura.css" 						type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/ws-modules/ws-login/desktop.css?v=?<?php echo rand(0,999999)?>" 	type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/chosen/chosen.css" 											type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/theme_blue.min.css" 									type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/funcionalidades.css" 								type="text/css" media="all" />
<script type = 'text/javascript' src="<?=ROOT_WEBSHEEP?>admin/app/vendor/jquery/2.2.0/jquery.min.js"									id="jquery"></script>
<script type = 'text/javascript' src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/funcionalidades.js" 							id="funcionalidades"></script>
<link href="https://fonts.googleapis.com/css?family=Titillium+Web:300,400,600,700" rel="stylesheet">
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<style type="text/css">
iframe{
	position: relative;
	width: 190px;
	height: 75px;
	float: left;
	top: -15px;
}
.g-recaptcha div:first-child{
    position: relative;
    float: left;
    width: 100%!important;
    height: 100%!important;
}
</style>
<body id="body"> 
<div id='avisoTopo'></div>
<div id="container">
<div id="login">
	<div id="logomarca">
        <spam id="h3"><?php echo $licenseData->version?></spam>
        <img src="<?=ROOT_WEBSHEEP?>admin/app/templates/img/websheep/logoIcon.png">
    </div>
	<form  id="formulario">
		<input 		class="inputText" name="usuario"  		value=""			placeholder="Usuario:"/></input>
		<input  	class="inputText" name="senha" 			value=""			placeholder="Senha:" type="password"/></input>
		<input  	class="inputText" name="function" 		value="login" 		hidden="true"> 	
		<div 		id="iniciarsessao_disabled" class="botaodisabled "><?=ws::getlang("login>loading");?></div>
		<?if(defined('RECAPTCHA') && RECAPTCHA!=""):?>
			<div class="g-recaptcha" data-sitekey="<?=RECAPTCHA?>" data-callback="wsSetLogin" data-expired-callback="dataexpiredcallback" style="position:relative;float:left;width: 180px;height: 43px;margin-right: 10px;overflow: hidden;border: solid 5px #2e5994;"></div> 
			<button 	id="iniciarsessao" type="submit" class='botao ' ><?=ws::getlang("login>buttomBeginSession");?></button>
		<?else:?>
			<button 	id="iniciarsessao" type="submit" class='botao ' style=" width: 400px;" ><?=ws::getlang("login>buttomBeginSession");?></button>
		<?endif;?>
		<div 		id="" class="w1 esqueci">
			<a href="#" id="esqueceu" class="email"><?=ws::getlang("login>recoverPassword");?></a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?=$licenseData->license?>" target="_blank" class="politica"><?=ws::getlang("login>privacypolicy");?></a>
		</div>
		<a id="tokenRequest"></a>

	</form>
	<div class="c"></div> 
</div>
<script type="application/javascript">
		$('#iniciarsessao_disabled').hide()
		function dataexpiredcallback(a){ alert(a); }
		function wsSetLogin(a){
			$("#iniciarsessao").unbind('click tap press').bind('click tap press',function(e) {
				e.preventDefault();
				$('#iniciarsessao_disabled').show();
				$.ajax({
					type: "POST",
					async: true,
					url: "<?=ROOT_WEBSHEEP?>admin/app/ws-modules/ws-login/functions.php",
					data:{'function':'login','form':$("#formulario").serialize()},
					beforeSend: function() {
						$("#iniciarsessao").hide('fast')
						$("#iniciarsessao_disabled").show('fast')
						confirma({width: "auto", conteudo: " <?=ws::getlang("login>loading");?><div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"<?=ROOT_WEBSHEEP?>admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>", drag: false, bot1: 0, bot2: 0 })
					}
				}).done(function(e){
					console.log(e);
					// if(e.trim() =='ok'){
					// 	window.location.reload();
					// }else{
					// 	$("#ws_confirm").fadeOut('fast',function(){
					// 		$("#ws_confirm").remove();
					// 		$("*").removeClass("blur");
					// 	});
					// 	$("#iniciarsessao").show('fast');
					// 	$("#iniciarsessao_disabled").hide('fast');
					// 	alert(e)
					// }
				})
			})
		}
		<?if(defined('RECAPTCHA') && RECAPTCHA!=""):?>
			$("#formulario").submit(function(e) { e.preventDefault();alert("<?=ws::getlang("login>responseToken");?>");return false;})
		<?else:?>
			wsSetLogin();
		<?endif;?>



		$("#esqueceu").click(function(e) {
			e.preventDefault();
			confirma({
				conteudo:'<?=ws::getlang("login>modalRecover");?><br><input id="emailUser" class="inputText" placeholder="Seu e-mail:" style="padding: 10px 30px; width:90%;margin: 10px;">',
				posFn:function(e){},
				newFun:function(){
					$email = $("#emailUser").val();
					setTimeout(function(){
						confirma({conteudo:"<?=ws::getlang("login>recovering");?>", bot2:false, bot1:false})

						$.ajax({
							type: "POST",
							url: "<?=ROOT_WEBSHEEP?>admin/app/ws-modules/ws-login/functions.php",
							data: {
								'function': 'enviaemail',
								'mail': $email
							}
						}).done(function(data) {
								confirma({conteudo:data, bot2:false, bot1:"ok"}) 
						});


					},500)
				},
				width:600,
				height:'auto',
				divScroll:"#body",
				drag:false,
				bot2:'Cancelar',
				bot1:"Enviar senha por email",
				Callback:function(e){},
				check:function(e){return true}
			})
		})
		$("#politica").click(function(e) {
			e.preventDefault();
			window.open("/admin/LICENSE.md","_blank");

		})

		$("#tokenRequest").click(function(e) {
			e.preventDefault();  
			confirma({
				conteudo:<? echo "'"
								.ws::getlang("login>newPass")
								.'<br>'
								.'<form id="getRequest">'
									.'<input id="" 			class="inputText" 	name="tokenRequest" type="hidden" value="'.@$_GET['tokenRequest'].'">'
									.'<input id="newPass" 	class="inputText" 	name="newPass" 		type="password" placeholder="Nova senha:" style="padding: 10px 30px; width:90%;margin: 10px;"><br>'
									.'<input id="newPass2" 	class="inputText" 	name="newPass2" 	type="password" placeholder="Digite novamente sua senha:" style="padding: 10px 30px; width:90%;margin:0 10px;">'
								.'</form>'
								."'";?>,
				posFn:function(e){},
				newFun:function(){
					var formulario = $("#getRequest").serialize();
					setTimeout(function(){
						confirma({conteudo:"Solicitando alterações...", bot2:false, bot1:false, drag:false, newFun:function(){}})
						$.ajax({
							type: "GET",
							async: true,
							url: "<?=ROOT_WEBSHEEP?>admin/app/ws-modules/ws-login/functions.php",
							data:{'function':'setNewPass','form':formulario}
						}).done(function(e){
							if(e==true){
								setTimeout(function(){
									confirma({conteudo:"<?=ws::getlang("login>passwordChangedSuccessfully")?>", botclose:true, bot2:false, bot1:false, drag:false, onClose:function(){window.location = "/admin";}})
								},500)
							}else{alert(e)}
						})
					},500)
				},
				width:600,
				drag:false,
				bot1:"Cadastrar",
				bot2:'Cancelar',
				Callback:function(e){},
				ErrorCheck:function(e){TopAlert({mensagem:"<?=ws::getlang("login>passwordsDoNotMatch")?>",type:2}) },
				Check:function(e){if($("#newPass").val()!=$("#newPass2").val()){return false;}else{return true;} }
			})	
			return false;
		})



		<?if(isset($_GET['tokenRequest']) && $_GET['tokenRequest']!=""):?>
					$.ajax({
						type: "GET",
						async: true,
						url: "<?=ROOT_WEBSHEEP?>admin/app/ws-modules/ws-login/functions.php",
						data:{'function':'verifyToken','tokenRequest':'<?=$_GET['tokenRequest']?>'}
					}).done(function(e){
						if(e==true){
							$("#tokenRequest").click();
						}else{
							confirma({
								conteudo:"<?=ws::getlang("login>invalidOrExpiredToken")?>", 
								bot2:"Cancelar", 
								bot1:'<?=ws::getlang("login>ButtomSendAnotherLink")?>',
								newFun:function(){
									setTimeout(function(){
										$("#esqueceu").click();
									},500)
								}
							})
						}
					});
		<?endif;?>


</script>
</div>
</body>
</html>