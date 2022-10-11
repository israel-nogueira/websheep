<?
	set_time_limit(0); 
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'ws-img'));
			$path = implode(array_filter(explode('/',$path)),"/");
			define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
		}
		if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

	################################################################################
	# IMPORTAMOS A CLASSE THUMB CANVAS
	################################################################################
		ob_start();
		include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');

	################################################################################
	# EXPLODIMOS A URL DA IMAGEM
	################################################################################
		$URL = ws::urlPath(0,0,'array');

	################################################################################
	# AGORA TRATAMOS A ARRAY COM OS DADOS NECESSÁRIOS
	################################################################################	
		if (count($URL) >= 5) {
			$vars['largura'] = $URL[2];
			$vars['altura']  = $URL[3];
			$vars['q']       = $URL[4];
			$vars['imagem']  = $URL[5];

		} elseif (count($URL) == 4) {
			$vars['largura'] = $URL[2];
			$vars['altura']  = $URL[3];
			if (is_numeric($URL[4])) {
				$vars['q']      = $URL[4];
				$vars['imagem'] = null;
			} else {
				$vars['q']      = null;
				$vars['imagem'] = $URL[4];
			}
		} elseif (count($URL) == 3) {
			$vars['q']       = null;
			$vars['largura'] = $URL[2];
			if (is_numeric($URL[3])) {
				$vars['altura'] = $URL[3];
				$vars['imagem'] = null;
			} else {
				$vars['altura'] = 0;
				$vars['imagem'] = $URL[3];
			}
		} elseif (count($URL) == 2) {
			$vars['q']       = null;
			if (is_numeric($URL[2])) {
				$vars['largura'] = $URL[2];
				$vars['altura']  = 0;
				$vars['imagem']  = null;
			} else {
				$vars['imagem']  = $URL[2];
				$vars['altura']  = 0;
				$vars['largura'] = 0;
			}
		} else {
			$vars['imagem']  = null;
			$vars['altura']  = 0;
			$vars['largura'] = 0;
			$vars['q']       = null;
		}

	################################################################################
	# RETIRAMOS O @2X PARA NÃO DAR CONFLITO COM TEMPLATES RESPONSIVOS
	################################################################################
		$vars['imagem'] = str_replace("@2x", "", $vars['imagem']);
		
	################################################################################
	# DEFINE O PATH QUE IRÁ BUSCAR AS IMAGENS
	################################################################################
		$pathUpload = INCLUDE_PATH.'website/assets/upload-files/';
		
	################################################################################
	# CASO NÃO EXISTA A IMAGEM, SUBSTITUIMOS PELA IMAGEM PADRÃO DO SISTEMA
	################################################################################
		if ($vars['imagem'] == null || !file_exists($pathUpload . $vars['imagem'])) {
			$OriginalExists=false;
			$vars['imagem'] = INCLUDE_PATH.'admin/app/templates/img/websheep/no-img.png';
		} else {
			$OriginalExists=true;
			$vars['imagem'] = $pathUpload . $vars['imagem'];
		}

	################################################################################
	# DEFINE O TAMANHO DA IMAGEM
	################################################################################
		$filesize = getimagesize($vars['imagem']);
		if ($vars['altura'] == 0 && $vars['largura'] == 0) {
			$vars['largura'] = $filesize[0];
			$vars['altura']  = $filesize[1];
		}
	
	################################################################################
	# VERIFICA A EXTENÇÃO DO ARQUIVO
	################################################################################
		$extencao = explode(".",$vars['imagem']);
		$extencao = end($extencao);

	################################################################################
	# DEFINE A QUALIDADE DA IMAGEM
	################################################################################	
	if ($extencao == 'jpg') {
		if (empty($vars['q'])) {
			$vars['q'] = '100';
		}
	}
	if ($extencao == 'png') {
		if (empty($vars['q'])) {
			$vars['q'] = '9';
		}
	}
	if ($extencao == 'gif') {
		if (empty($vars['q'])) {
			$vars['q'] = '100';
		}
	}
	
	################################################################################
	# CAMINHO ROOT DO ARQUIVO TRATADO
	################################################################################
		$_root = INCLUDE_PATH.'website';
		$local = $_root.'/assets/upload-files/thumbnail';
		if(!file_exists($local)){@mkdir($local);}
		$newName = $local.'/'.$vars['largura'] . '-' . $vars['altura'] . '-' . $vars['q'] . '-' . basename($vars['imagem']);
		
	################################################################################
	# VERIFICA SE A THUMB NÃO EXISTE  
	################################################################################
	if (!file_exists($newName)) {
		################################################################################
		# CRIA THUMB E RETORNA
		################################################################################
		$thumb = new thumb($vars['imagem']);
		$thumb->setDimensions(array($vars['largura'],$vars['altura']));
		if($OriginalExists==false){
			$thumb->setNewThumb(null);
		}else{
			$thumb->setNewThumb($newName);
		}
		$thumb->setJpegQuality(100);										
		$thumb->setPngQuality(9);
		$thumb->setGifQuality(100);
		$thumb->crop=true;
		$thumb->forceDownload(false);
		$thumb->showBrowser(true);
		$thumb->process();

	} else {
		################################################################################
		# MONTAMOS O HEADER
		################################################################################
		 if ($extencao == 'jpg') {
		 	header("Content-type: image/jpeg");
		 } elseif ($extencao == 'png') {
		 	header("Content-type: image/png");
		 } elseif ($extencao == 'gif') {
		 	header("Content-type: image/gif");
		 }
		################################################################################
		# RETTORNA A IMAGEM
		################################################################################
		 	$handle = fopen($newName, "rb");
			echo stream_get_contents($handle);
			fclose($handle);
		################################################################################
	}
	exit(0);