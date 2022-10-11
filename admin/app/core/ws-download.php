<?
############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'ws-download'));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}

	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	$login 		= false;
	$_url_ 		= ws::urlPath(2,false);

	# VERIFICA SE EXISTE URL
	if($_url_){
		$_url_ 	= explode('!',$_url_);
		$direto = isset($_url_[2]) && $_url_[2]=="direct=true";

		# SE NAO EXISTIR LINK
		$LinkFile = new MySQL();
		$LinkFile->set_limit(1);
		$LinkFile->set_table(PREFIX_TABLES.'ws_keyfile');
		$LinkFile->set_where(' active=1 ');
		$LinkFile->set_where( 'AND tokenFile="'.@$_url_[0].'"');
		$LinkFile->set_where(' AND (expire IS NULL OR expire="0000-00-00" OR expire > (now() - INTERVAL 1 DAY))');
		$LinkFile->select();
		if($LinkFile->_num_rows==0){goto arquivoNull; exit;}

		# SE NAO EXISTIR ARQUIVO
		$biblioteca = new MySQL();
		$biblioteca->set_limit(1);
		$biblioteca->set_Order("id","DESC");
		$biblioteca->set_table(PREFIX_TABLES.'ws_biblioteca');
		$biblioteca->set_where('tokenFile="'.$LinkFile->obj[0]->tokenFile.'"');
		$biblioteca->select();
		if($biblioteca->_num_rows==0){goto arquivoNull;	exit;}


		# SE NAO EXISTIR CHAVE DE ACESSO
		$KeyLink = new MySQL();
		$KeyLink->set_limit(1);
		$KeyLink->set_table(PREFIX_TABLES.'ws_keyfile');
		$KeyLink->set_where(' active=1 ');
		$KeyLink->set_where( 'AND tokenFile="'.@$_url_[0].'"');
		$KeyLink->set_where( 'AND keyaccess="'.@$_url_[1].'"');
		$KeyLink->set_where(' AND (expire IS NULL OR expire="0000-00-00" OR expire > (now() - INTERVAL 1 DAY))');
		$KeyLink->select();


		# SE APENAS TIVER O TOKEN + CHAVE + DIRECT
		if(count($_url_)>=2  && $KeyLink->_num_rows==1  && $direto){	goto direct; 		exit;}

		# SE APENAS TIVER O TOKEN SEM A CHAVE
		if(count($_url_)==1){ goto getPass;exit;}

		# SE APENAS TIVER O TOKEN COM A CHAVE
		if(count($_url_)>=2  && $KeyLink->_num_rows==1){				goto botDownload; 	exit;}

		# SE APENAS TIVER O TOKEN COM A CHAVE
		if(count($_url_)>=2  && $KeyLink->_num_rows==0){				goto arquivoNull; 	exit;}

	}else{
		goto arquivoNull;exit;
	}
exit;
######################################################
direct:
		$file 			= INCLUDE_PATH.'website/assets/upload-files/'.$biblioteca->obj[0]->file;
		if(!file_exists($file)){goto arquivoNull;exit;}
		$upload_size    	=  _filesize($biblioteca->obj[0]->upload_size);
		$fileName   		=  $biblioteca->obj[0]->filename;
		$mime_type   		=  $biblioteca->obj[0]->type;
		header('Content-Type: ' .$mime_type);
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($file));
		header('Accept-Ranges: bytes');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Pragma: public');
		header('Cache-Control:');
		readfile($file);
		$update = new MySQL();
		$update->set_table(PREFIX_TABLES.'ws_keyfile');
		$update->set_where('keyaccess="'.$biblioteca->obj[0]->keyaccess.'"'); 
		$update->set_where('AND tokenFile="'.$biblioteca->obj[0]->tokenFile.'"'); 
		$update->set_update('accessed',($biblioteca->obj[0]->accessed + 1));
		if($biblioteca->obj[0]->disableToDown=="1"){$update->set_update('active','0'); }
		if(($biblioteca->obj[0]->accessed=="0" && $biblioteca->obj[0]->refreshToDown=="1") || $biblioteca->obj[0]->refreshToDown=="2"){$update->set_update('keyaccess',_codePass(_crypt())); }
		$update->salvar();
exit;
botDownload:
?>
<html lang="pt-br">
<head>
	<title>Download: <?=$biblioteca->obj[0]->filename?></title>
	<meta charset="UTF-8">
	<link type="image/x-icon" href="img/favicon.ico" rel="shortcut icon" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/fontes/fonts.css" 									type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/reset.css" 										type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/desktop.css?<?=rand(0,999999)?>" 				type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/estrutura.css" 									type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/theme_blue.min.css" 							type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/funcionalidades.css" 						type="text/css" media="all" />
	<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/vendor/jquery/2.2.0/jquery.min.js"							id="jquery"></script>
	<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/funcionalidades.js" 				id="funcionalidades"></script>
	<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/functionsws.js" 					id="functionsws"></script>
	<style>
		.formularioDownload{
			margin-left:0!important;
			position: absolute!important;
			left: 50%!important;
			transform: translate(-50%, -50%)!important;
			width: 700px!important;
			top: 50%!important;
		}
	</style>
</head>
<body id="body"> 
<div id="avisoTopo"></div>
<div id="container">
<div id="login">
	<form  id="formulario" class="formularioDownload">
		<div style="margin-bottom: 20px;background-color: rgba(255, 255, 255, 0.42);padding: 20px;line-height: 23px;">
			<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">Nome do arquivo:</h1>	<span class="w2" style="color: #2e5994;"	><?=$biblioteca->obj[0]->filename?>		</span><br> 
			<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">Tamanho:</h1>			<span class="w2" style="color: #2e5994;"	><?=_filesize($biblioteca->obj[0]->upload_size)?>	</span><br>
			<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">Tipo do arquivo:</h1>	<span class="w2" style="color: #2e5994;"	><?=$biblioteca->obj[0]->type?>			</span>
			<br>
			<br>

<!-- 			<div class="w2" style="color: #2e5994;position: relative;height: 30px;word-wrap: break-word;padding-top: 10px;">
				<a 
					class="botao" 
					style="padding: 10px 70px;text-decoration: none;"
					href="<?='//'.DOMINIO.'/'.ws::urlPath(1).'/'.$KeyLink->obj[0]->tokenFile?>" 
					target="_blank"
				>Abrir link em nova janela</a>
				<a 
					class="botao" 
					style="padding: 10px 70px;text-decoration: none;"
					href="<?='//'.DOMINIO.'/'.ws::urlPath(1).'/'.$KeyLink->obj[0]->tokenFile?>!<?=$KeyLink->obj[0]->keyaccess?>" 
					target="_blank"
				>Abrir link em nova janela</a>
			</div>
 -->


		<div class="preloader bg01" style="display:none;position: relative;height: 15px;background-color: #FFF;margin-bottom: 10px;">
			<div class="progresssBar bg05" style="-webkit-transition: all 100ms ease; -moz-transition: all 100ms ease; -ms-transition: all 100ms ease; -o-transition: all 100ms ease; transition: all 100ms ease;position: relative;height: 6px;background-color: #FFF;top: 1px;margin: 3px;"></div>
			<div class="pct w1" style="text-align: center; margin-top: 10px; "></div>
		</div>
		<div class="c"></div>
		<button 	id="downloadFileBtn" 		type="submit" class="botao">Download</button>
	</form>
	<div class="c"></div>
</div>
<script type="application/javascript">
		$("#downloadFileBtn").click(function(e){
			e.preventDefault();
			var locationFile ='<?=ROOT_WEBSHEEP?>ws-download/<?=$_url_[0]?>!<?=@$_url_[1]?>!direct=true';
			$("#downloadFileBtn").hide();
			$(".preloader").show();
			ws.downloadFile({
				typeSend 	:"GET",
				file 		:locationFile,
				newfile 	:"<?=$biblioteca->obj[0]->filename?>",
				load 		:function(e){$("#downloadFileBtn").show();$(".preloader").hide()},
				finish 		:function(e){$('.progresssBar').css({'width':"0%"}) },
				progress 	:function(e){
					$('.progresssBar').css({'width':e+"%"});
					$('.pct').html(e+"%");				 
				}
			});
			return false;
	})
</script>
</div>
</body>
</html>
<?
exit;
arquivoNull:
		?>
	<html lang="pt-br">
	<head>
		<title>Erro de download</title>
		<meta charset="UTF-8">
		<link type="image/x-icon" href="img/favicon.ico" rel="shortcut icon" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/fontes/fonts.css" 									type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/reset.css" 								type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/desktop.css?<?=rand(0,999999)?>" 			type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/estrutura.css" 							type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/theme_blue.min.css" 							type="text/css" media="all" />
	<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/funcionalidades.css" 							type="text/css" media="all" />
	<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/vendor/jquery/2.2.0/jquery.min.js"						id="jquery"></script>
	<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/funcionalidades.js" 						id="funcionalidades"></script>
	<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/functionsws.js" 					id="functionsws"></script>

		<style>
			.formularioDownload{
				margin-left:0!important;
				position: absolute!important;
				left: 50%!important;
				transform: translate(-50%, -50%)!important;
				width: 490px!important;
				top: 50%!important;
			}
		</style>
	</head>
	<body id="body"> 
		<div id="avisoTopo"></div>
		<div id="container">
			<div id="login">
				<form  id="formulario" class="formularioDownload">
					<div style="margin-bottom: 20px;background-color: rgba(255, 255, 255, 0.42);padding: 20px;">
						<div class="w1" style="margin-bottom:20px;font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">
						<i class="fa fa-exclamation-triangle" style="font-size: 30px; color: #f00; "></i>
						Ops, pode ter acontecido os seguintes erros:</div>
						<div class="c"></div>
						<div class="w2" style="color: #2e5994;">• Usuário não autorizado/logado</div>
						<div class="w2" style="color: #2e5994;">• Link expirado</div>
						<div class="w2" style="color: #2e5994;">• Link removido ou desativado</div>
						<div class="w2" style="color: #2e5994;">• Arquivo inexistente</div>
						<div class="w2" style="color: #2e5994;">• Token do arquivo inválido</div>
						<div class="w2" style="color: #2e5994;">• Chave de acesso inválida</div>
					</div>
				</form>
				<div class="c"></div>
			</div>
		</div>
	</body>
	</html>
<?
exit;
getPass:
?>
<html lang="pt-br">
<head>
<title>Download: <?=$biblioteca->obj[0]->filename?></title>
<meta charset="UTF-8">
<link type="image/x-icon" href="img/favicon.ico" rel="shortcut icon" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/fontes/fonts.css" 									type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/reset.css" 								type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/desktop.css?<?=rand(0,999999)?>" 			type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/ws-login/estrutura.css" 							type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/theme_blue.min.css" 							type="text/css" media="all" />
<link rel="stylesheet" href="<?=ROOT_WEBSHEEP?>admin/app/templates/css/websheep/funcionalidades.css" 							type="text/css" media="all" />
<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/vendor/jquery/2.2.0/jquery.min.js"						id="jquery"></script>
<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/funcionalidades.js" 						id="funcionalidades"></script>
<script type="text/javascript" src="<?=ROOT_WEBSHEEP?>admin/app/templates/js/websheep/functionsws.js" 					id="functionsws"></script>
<style>
	.formularioDownload{
		margin-left:0!important;
		position: absolute!important;
		left: 50%!important;
		transform: translate(-50%, -50%)!important;
		width: 400px!important;
		top: 50%!important;
	}
</style>
</head>
<body id="body"> 
<div id="avisoTopo"></div>
<div id="container">
<div id="login">
	<form  id="formulario" class="formularioDownload">
		<div style="margin-bottom: 20px;background-color: rgba(255, 255, 255, 0.42);padding: 20px;">
			<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">Nome do arquivo:</h1>	<span class="w2" style="color: #2e5994;"	><?=$biblioteca->obj[0]->filename?>		</span><br> 
			<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">Tamanho:</h1>			<span class="w2" style="color: #2e5994;"	><?=_filesize($biblioteca->obj[0]->upload_size)?>	</span><br>
			<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;">Tipo do arquivo:</h1>	<span class="w2" style="color: #2e5994;"	><?=$biblioteca->obj[0]->type?>			</span><br>		
		</div>
		<h1 class="w1" style="font-family:'Titillium Web', sans-serif;float: left;margin-right: 10px;font-weight: 600;color: #3e536f;margin-bottom: 10px;text-align: center;width: 100%;">Por favor, digite a chave de acesso:</h1>
		<input 		id="serialKey" class="inputText" name="cod"  		value=""			placeholder="Key:"/></input>

		<div class="c"></div>
		<div class="preloader bg01" style="display:none;position: relative;height: 15px;background-color: #FFF;margin-bottom: 10px;">
			<div class="progresssBar bg05" style="-webkit-transition: all 100ms ease; -moz-transition: all 100ms ease; -ms-transition: all 100ms ease; -o-transition: all 100ms ease; transition: all 100ms ease;position: relative;height: 6px;background-color: #FFF;top: 1px;margin: 3px;"></div>
			<div class="pct w1" style="text-align: center; margin-top: 10px; "></div>
		</div>
		<div class="c"></div>
		<button 	id="downloadFileBtn" type="submit" class="botao">Download</button>
	</form>
	<div class="c"></div>
</div>
<script type="application/javascript">
	$("#downloadFileBtn").click(function(e){
			e.preventDefault();
			var locationFile ='<?=ROOT_WEBSHEEP?>ws-download/<?=$_url_[0]?>!'+$("#serialKey").val().replace("!","")+'!direct=true';
			$("#downloadFileBtn").hide()
			$(".preloader").show()
			ws.downloadFile({
				typeSend:"GET",
				file:locationFile,
				newfile:"<?=$biblioteca->obj[0]->filename?>",
				load:function(e){$("#downloadFileBtn").show();$(".preloader").hide()},
				finish:function(e){$('.progresssBar').css({'width':"0%"}) },
				progress:function(e){
					$('.progresssBar').css({'width':e+"%"});
					$('.pct').html(e+"%");				 
				}
			});
			return false;
	})


</script>
</div>
</body>
</html>



<?exit;
