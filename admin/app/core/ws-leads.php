<?
/*	
	if(ws::urlPath(3,0)=="xls"){
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES.' ws_list_leads');
		$Dados->set_where('token="'.ws::urlPath(2,0).'"');
		$Dados->select();

		$result = new MySQL();
		$result->set_table(PREFIX_TABLES.'wslead_'.ws::urlPath(2,0));
		$result->select();

		$colunas			= new MySQL();
		$colunas->set_table(PREFIX_TABLES.'wslead_'.ws::urlPath(2,0));
		$colunas->show_columns();
		$filename = 'data_export_'.ws::urlAmigavel($Dados->obj[0]->title).'-'.date("Y-m-d-H-i-s").'.xls';
		$columns= array();

		foreach ($colunas->fetch_array as $coluna) {$columns[]= $coluna['Field']; }
		$html = '';
		$html .= '<table>';
		$html .= '<tr>';
		$html .= '<td colspan="'.count($colunas->fetch_array).'" height="50" style="background-color: #70bba9;color:#FFF;text-align: center;font-weight: bolder;font-family: sans-serif;font-size: 20px;">'.$Dados->obj[0]->title.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		foreach ($colunas->fetch_array as $coluna) {
			$html .= '<td><b>'.$coluna['Field'].'</b></td>';
		}
		$html .= '</tr>';
		foreach ($result->fetch_array as $value) {
			$html .= '<tr>';
			foreach ($colunas->fetch_array as $coluna) { $html .= '<td>'.$value[$coluna['Field']].'</td>';}
			$html .= '</tr>';
		}
		header("Content-type: 	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment; filename="file.xlsx"'); 
		header('Cache-Control: max-age=0'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public');
		header("Content-Disposition: attachment; filename={$filename}"); 
		header("Expires: 0");
		echo $html;
		exit;
	}
	if(ws::urlPath(3,0)=="csv"){

		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES.' ws_list_leads');
		$Dados->set_where('token="'.ws::urlPath(2,0).'"');
		$Dados->select();
		$filename = 'data_export_'.ws::urlAmigavel($Dados->obj[0]->title).'-'.date("Y-m-d-H-i-s").'.csv';

		$s = new MySQL();
		$s->set_table(PREFIX_TABLES.'wslead_'.ws::urlPath(2,0));
		$s->select();
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		$array = $s->fetch_array;
		if (count($array) == 0) {return null; }
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($array)));
		foreach ($array as $row) {fputcsv($df, $row);}
		fclose($df);
		echo ob_get_clean();
		exit;
	}
	if(ws::urlPath(3,0)=="txt"){
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES.' ws_list_leads');
		$Dados->set_where('token="'.ws::urlPath(2,0).'"');
		$Dados->select();

		$result = new MySQL();
		$result->set_table(PREFIX_TABLES.'wslead_'.ws::urlPath(2,0));
		$result->select();
		$colunas			= new MySQL();
		$colunas->set_table(PREFIX_TABLES.'wslead_'.ws::urlPath(2,0));
		$colunas->show_columns();
		$arquivo = ws::urlAmigavel($Dados->obj[0]->title).'.xls';
		$columns= array();
		$filename = 'data_export_'.ws::urlAmigavel($Dados->obj[0]->title).'-'.date("Y-m-d-H-i-s").'.txt';
		$now = gmdate("D, d M Y H:i:s");
		 header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		 header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		 header("Last-Modified: {$now} GMT");
		 header("Content-Type: application/force-download");
		 header("Content-Type: application/octet-stream");
		 header("Content-Type: application/download");
		 header("Content-Disposition: attachment;filename={$filename}");
		 header("Content-Transfer-Encoding: binary");
		ob_start();
		foreach ($result->fetch_array as $result) {
			foreach ($colunas->fetch_array as $value) {
				echo  "\n\r[".$value['Field']."]: \n\r";	
				echo 	$result[$value['Field']]."\n\r"; 
			}
			echo "\n\r====================================================================================================================\n\r";
		}
		echo ob_get_clean();
		exit;
	}
*/	

############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'ws-leads'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}


	error_reporting(E_ALL) ;
	$TYPE_SEND = $_POST;
	$TYPE_SEND = $_REQUEST;


	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	if(isset($TYPE_SEND['typeSend']) && $TYPE_SEND['typeSend']=='captcha'){
		@session_name('_WS_');@session_id($_COOKIE['_WS_']);@session_start(); 
		$codeCaptcha = trim(_decripta(@$_SESSION['ws-captcha'] ,'ws-captcha-keycode'));
		if($TYPE_SEND['keyCode']==$codeCaptcha){ echo 1;}else{echo 0;};
		exit;
	}
	if(empty($TYPE_SEND)){_erro('Desculpe, dados inválidos ou inexistentes... '); exit; }
	if(count($_FILES)>=1){
		if(!file_exists(INCLUDE_PATH.'website/assets')){						mkdir(INCLUDE_PATH.'website/assets');				}		
		if(!file_exists(INCLUDE_PATH.'website/assets/upload-leads-files')){	mkdir(INCLUDE_PATH.'website/assets/upload-leads-files');	}	
		foreach ($_FILES as $key => $__FILE__) {
			if(is_array($__FILE__['name'])){
				$linkName 	= array();
				for ($i=0; $i < count($__FILE__['name']); $i++) { 
		        	$tmp_name 	= $__FILE__["tmp_name"][$i];
		        	$size 		= $__FILE__["size"][$i];
		        	$type		= $__FILE__["type"][$i];
					$nome 		= url_amigavel_filename($__FILE__["name"][$i]);
					$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
					$ext		= str_replace(array("jpeg"),array("jpg"),$ext);
					$token 		= md5(uniqid(rand(), true));
					 if(move_uploaded_file( $tmp_name ,$_SERVER['DOCUMENT_ROOT']."/website/assets/upload-leads-files/".$token.'.'.$ext)){
					 	$linkName[] ="<a class='downloadFile' href='".$_SERVER['HTTP_HOST']."/admin/ws-modules/ws-register-leads/download.php?filename=".$token.".".$ext."&newname=".$nome."' target='_blank'>".$nome."</a>";
					}						
				}
				$TYPE_SEND[$key] = implode($linkName,'<br>');
			}else{
	        	$tmp_name 	= $__FILE__["tmp_name"];
	        	$size 		= $__FILE__["size"];
	        	$type		= $__FILE__["type"];
				$nome 		= url_amigavel_filename($__FILE__["name"]);
				$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
				$ext		= str_replace(array("jpeg"),array("jpg"),$ext);
				$token 		= md5(uniqid(rand(), true));
				 if(move_uploaded_file( $tmp_name ,$_SERVER['DOCUMENT_ROOT']."/website/assets/upload-leads-files/".$token.'.'.$ext)){
				 	$TYPE_SEND[$key] ="<a class='downloadFile' href='/admin/ws-modules/ws-register-leads/download.php?filename=".$token.".".$ext."&newname=".$nome."' target='_blank'>".$nome."</a>";
				}
			}
		}
	};

	@header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	@header("Cache-Control: post-check=0, pre-check=0", false);
	@header("Pragma: no-cache");

	$LeadsToken = strtolower(ws::urlPath(2));
	$type_send 	= "ajax";
	parse_str($TYPE_SEND['form'],$TYPE_SEND);

	if(isset($TYPE_SEND['typeSend'])){
		$type_send=$TYPE_SEND['typeSend'];
		unset($TYPE_SEND['typeSend']);
	}

	$_FORM = $TYPE_SEND;

	############################################################################################
	############################################################################################ VERIFICA SE TEM ALGUM LEAD COM ESSE TOKEN
	############################################################################################ E GRAVA OS DADOS NA VARIÁVEL

	$s = new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_list_leads');
	$s->set_where('token="'.$LeadsToken.'"');
	$s->select();
	if($s->_num_rows==0){_erro('Desculpe, token inválido ou inexistente'); exit; }
	$_LEAD = $s->obj[0];
	define("topoEmail"		,$_LEAD->header_email);
	define("assEmail"		,$_LEAD->footer_email);
	define("pathImg"		,'../../website/assets/upload-files');
	define("Remetente"		,$_LEAD->remetente);
	define("NomeRemetente"	,$_LEAD->remetente_name);


	#########################################################################################################################
	######################################################################################################################### 
	#########################################################################################################################
	function redirect($vars, $url) {
	  $html = "<html><body><form id='form' action='$url' method='post'>";
	  foreach ($vars as $key => $value) {$html .= "<input type='hidden' name='$key' value='$value'>"; }
	  $html .= "</form><script>document.getElementById('form').submit();</script>";
	  $html .= "</body></html>";
	  print($html);
	}
		######################################################################################################################### PUXA CLASSE
		if($_LEAD->smtp_local==1){
			$local = new MySQL();
			$local->set_table(PREFIX_TABLES.'setupdata');
			$local->select();
			$local=$local->obj[0];
			define("Host"			,$local->smtp_host);
			define("Username"		,$local->smtp_email);
			define("Password"		,$local->smtp_senha);
			define("Port"			,$local->smtp_port);
			define("SMTPSecure"	,$local->smtp_secure);
			if($local->smtp_auth==1) {define("SMTPAuth",true); }else{define("SMTPAuth",false); }
		}else{
			define("Host"			,$_LEAD->host);
			define("Username"		,$_LEAD->email_envio);
			define("Password"		,$_LEAD->pass);
			define("Port"			,$_LEAD->port);
			define("SMTPSecure"	,$_LEAD->SMTPSecure);
			if($_LEAD->server_ssl==1) {define("SMTPAuth",true); }else{define("SMTPAuth",false); }
	}
#########################################################################################################################
#########################################################################################################################
	
if($_LEAD->finalidade=='Apenas enviar email'){				goto enviaEmail;};
if($_LEAD->finalidade=='Apenas gravar na base'){			goto gravaNaBase;};
	gravaNaBase:
	$getPOSTs=array();
	$local = new MySQL();
	$local->set_table(PREFIX_TABLES.'wslead_'.$LeadsToken);
	$local->show_columns();
	foreach($local->fetch_array as $coluna){if($coluna['Field']!="id"){$getPOSTs[]=$coluna['Field'];}};
	##################################################### INSERT
	$I 					= new MySQL();
	$I->set_table(PREFIX_TABLES.'wslead_'.$LeadsToken);
	foreach($getPOSTs as $coluna){
		if(isset($_FORM[$coluna])){
			$I->set_insert($coluna,$_FORM[$coluna]);
		} 
	}
	//$I->debug(0);
	if($I->insert()){
			if($_LEAD->finalidade=='Apenas gravar na base'){
				if($type_send=='html'){redirect($_FORM, $_LEAD->url_sucess); exit;}
				if($type_send=='ajax'){echo true; exit;}
			}
			if($_LEAD->finalidade=='Enviar e-mail e gravar na base'){goto enviaEmail;}
	}else{
		if($_LEAD->finalidade=='Apenas gravar na base'){
			if($type_send=='html'){redirect($_FORM, $_LEAD->url_error); exit;}
			if($type_send=='ajax'){echo "Error: Falha ao salvar na base!"; exit;}
		}
		exit;
	};


enviaEmail:
	$getPOSTs=array();
	$local = new MySQL();
	$local->set_table(PREFIX_TABLES.'wslead_'.$LeadsToken);
	$local->show_columns();
	foreach($local->fetch_array as $coluna){if($coluna['Field']!="id"){$getPOSTs[]=$coluna['Field'];}};
	$isso			=array();
	$porissoIsso	=array();
	foreach($getPOSTs as $coluna){if(isset($_FORM[$coluna])){$isso[]='['.$coluna.']';$porissoIsso[]= $_FORM[$coluna];}}






	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->SMTPDebug 	= 0;
	$mail->Port 		= Port;
	if(SMTPSecure!="") $mail->SMTPSecure 	= SMTPSecure;
	$mail->Host 		= Host; 
	$mail->SMTPAuth 	= SMTPAuth;
	$mail->Username 	= Username;
	$mail->Password 	= Password;
	$mail->charSet 		= 'UTF-8';	
	$mail->Debugoutput = 'html'; 

    $mail->XMailer="";
    $mail->IsHTML(true);
    $mail->Priority = 1;
    $mail->AddCustomHeader("X-MSMail-Priority: High");
    $mail->AddCustomHeader("Importance: High");
    
	$mail->setFrom(Username, NomeRemetente);
	$mail->addAddress(Remetente,NomeRemetente);
	$mail->Subject =  $_LEAD->assunto;
	$mail->AltBody = strip_tags(utf8_decode(str_replace($isso, $porissoIsso,$_LEAD->msng_resp)));
	$mail->AddEmbeddedImage(INCLUDE_PATH.'website/assets/upload-files/'.topoEmail, "topo", topoEmail);
	$mail->AddEmbeddedImage(INCLUDE_PATH.'website/assets/upload-files/'.assEmail,  "assinatura", assEmail);
	$mensagem ="";
	if($_LEAD->header_email!="") 	$mensagem  .=  utf8_decode("<img src='cid:topo'><br>").PHP_EOL;
									$mensagem  .=  utf8_decode(str_replace($isso, $porissoIsso,$_LEAD->msng_resp)).PHP_EOL;
	if($_LEAD->footer_email!="")  	$mensagem  .=  utf8_decode("<img src='cid:assinatura'>");
	$mail->msgHTML(str_replace(PHP_EOL,"",$mensagem));
	$mail->Body= str_replace(PHP_EOL,"",$mensagem);



	#########################################################################################################################
	######################################################################################################################### RESPOSTA AO USUARIO
	#########################################################################################################################
	if ($mail->send()) {
			if($_LEAD->resposta_ao_usuario==1){
				$mailResp = new PHPMailer;
				$mailResp->IsSMTP();
				$mailResp->SMTPDebug 	= 0;
				$mailResp->Port 		= Port;
				if(SMTPSecure!="")	$mailResp->SMTPSecure 	= SMTPSecure;
				$mailResp->Host 		= Host; 
				$mailResp->SMTPAuth 	= SMTPAuth;
				$mailResp->Username 	= Username;
				$mailResp->Password 	= Password;
				$mailResp->charSet 		= 'UTF-8';	
				$mailResp->Debugoutput 	= 'html'; 
				$mailResp->setFrom(Username,NomeRemetente);
				$mailResp->addAddress($_FORM[$_LEAD->camp_mail_clt],NomeRemetente);
				$mailResp->Subject = utf8_decode($_LEAD->assunto_clt);
				$mailResp->AltBody = strip_tags(str_replace($isso, $porissoIsso,$_LEAD->msng_resp_user));
				$mailResp->AddEmbeddedImage(INCLUDE_PATH.'website/assets/upload-files/'.topoEmail, "topo", topoEmail);
				$mailResp->AddEmbeddedImage(INCLUDE_PATH.'website/assets/upload-files/'.assEmail,  "assinatura", assEmail);
				$mensagem ="";
				if($_LEAD->header_email!="") 	$mensagem  .=  utf8_decode("<img src='cid:topo'><br>").PHP_EOL;
												$mensagem  .=  utf8_decode(str_replace($isso, $porissoIsso,$_LEAD->msng_resp_user)).PHP_EOL;
				if($_LEAD->footer_email!="")  	$mensagem  .=  utf8_decode("<img src='cid:assinatura'>");
				$mailResp->msgHTML(str_replace(PHP_EOL,"",$mensagem));
				$mailResp->Body = (str_replace(PHP_EOL,"",$mensagem));
				if (!$mailResp->send()) {
					echo "Error 2: " . $mailResp->ErrorInfo;
					exit; 
				}
			}

		if($type_send=='html'){redirect($_FORM, $_LEAD->url_sucess);exit; }
		if($type_send=='ajax'){echo true;exit; }

 }else{
	if($_LEAD->url_error!=""){redirect($_FORM, $_LEAD->url_error);exit;}
	echo "Error 1: " . $mail->ErrorInfo;
	exit;
 }



?>