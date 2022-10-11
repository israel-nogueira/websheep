<?php
	###########################################################################
	#	TEMPO DE EXECUÇÃO ILIMITADO
	###########################################################################
	ini_set('max_execution_time',0);

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'ws-update'));
			$path = implode(array_filter(explode('/',$path)),"/");
			define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
		}

		if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'ws-update'))));}
	
	#####################################################  
	# LIMPA O CACHE INTERNO
	#####################################################
	clearstatcache();
	
	#####################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	#####################################################
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();


	#####################################################  
	# IMPORTA CLASS DO TEMPLATE
	#####################################################
	$menu_dashboard = new Template(INCLUDE_PATH.'ws-update/ws-update.html', true);

	####################################################################################
	#	AQUI ELE DESCOMPACTA O DIRETÓRIO COM TODOS OS ARQUIVOS NA RAIZ E DÁ UM REFRESH  
	####################################################################################
	if((isset($_GET['install']) && @$_GET['install']=="install")){

		function ws_delete_dir($Dir){
			if(file_exists($Dir) && is_dir($Dir)){
			   if ($dd = opendir($Dir)) {
			        while (false !== ($Arq = readdir($dd))) {
			            if($Arq != "." && $Arq != ".."){
			                $Path = "$Dir/$Arq";
			                if(is_dir($Path)){
			                    _excluiDir($Path);
			                }elseif(is_file($Path)){
			                    unlink($Path);
			                }
			            }
			        }
			        closedir($dd);
			    }
			    rmdir($Dir);
			}else{
				return false;
			}
		}

		echo '<script>console.log("install")</script>'.PHP_EOL;
		verifyAdmin:
		if(file_exists("./../admin")){ 
			ws_delete_dir("./../admin"); 
		}
		if(!file_exists("./../admin")){
			$zip = new ZipArchive();
			if ($zip->open("./../ws-update.zip")) {
				if($zip->extractTo("./../")){
					$folderName = "CMS-".str_replace(".zip","",basename($_GET['githubFile']));
					rename("./../".$folderName.'/admin','./../admin'); 
					ws_delete_dir("./../".$folderName);
					$zip->close();
					unlink("./../ws-update.zip");
					echo "<script>window.top.location.reload();</script>";
				};
				exit; 
			}
		}else{
			sleep(1);
			goto verifyAdmin;
		}
		echo "ok!";
		exit;
	}

	####################################################################################
	#	PERGUNTA AO USUARIO SE TEM CERTEZA QUE QUER INSTALAR  
	####################################################################################
	if((isset($_GET['install']) && @$_GET['install']=="fire")):?>
		<script>
			window.parent.$("#loader,.logo").hide();
			window.parent.$("#botao").unbind("click press tap").bind("click press tap",function(){
				window.parent.$("#iframe").attr("src","./../ws-update/ws-install.php?install=install&githubFile=<?=$_GET['githubFile']?>");
				window.parent.$("#botao").hide();
				window.parent.$(".comboCentral").hide();
				window.parent.$(".preloader").show();
			}).html("Instalar WebSheep!").show();
		</script>

	<? 
	exit;
	endif;

	####################################################################################
	#	FAZ DOWNLOAD DO GITHUB VIA STREAM RETORNANDO PROGRESSBAR  
	####################################################################################
	if((isset($_GET['githubFile']) && @$_GET['githubFile']!="") && (isset($_GET['type']) &&  @$_GET['type']=="progressBar") ):?>
	<?
		$total_size = 0;
		function stream_notification_callback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) {
			global $total_size;
		    switch($notification_code) {
		        case STREAM_NOTIFY_FILE_SIZE_IS:
					$total_size = $bytes_max;
		            echo '<script>console.log("Fazendo download");window.parent.$(".comboCentral .logo").text("Fazendo download...");</script>'.PHP_EOL;
		            break;
		        case STREAM_NOTIFY_PROGRESS:
		        	$pct = @round((100 / $total_size) * $bytes_transferred);
		            echo '<script>console.log("'.$pct.'%");document.getElementById("progressBar").style.width="'.$pct.'%";</script>'.PHP_EOL;
		            break;
		    }
		}
		if(file_exists("./../ws-update.zip")){ unlink("./../ws-update.zip"); }
		echo '<div id="progressBar" style="	-webkit-transition: all 500ms ease-out; -moz-transition: all 500ms ease-out; -ms-transition: all 500ms ease-out; -o-transition: all 500ms ease-out; transition: all 500ms ease-out; position: fixed; background-color: #4f6cc6; height: 100%; width:0%; margin: 0; padding: 0; top: 0; left: 0; "></div>';
		$ctx = stream_context_create();
		stream_context_set_params($ctx, array("notification" => "stream_notification_callback"));
		file_put_contents("./../ws-update.zip",file_get_contents($_GET['githubFile'], false, $ctx));
		echo "<script>window.location='./ws-install.php?install=fire&githubFile=".$_GET['githubFile']."';</script>";
		exit;
endif;


#####################################################  
# VERIFICA SE O ARQUIVO ESTA SENDO ACESSADO VIA MENU
#####################################################
ws::getTokenRest(@$_GET['ASH_UPDATE']);

$menu_dashboard->block('BODY');
$menu_dashboard->show();


