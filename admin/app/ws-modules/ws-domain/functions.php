<?php
	$time_limit   = ini_get('max_execution_time');
	$memory_limit = ini_get('memory_limit');
	set_time_limit(0);
	set_time_limit($time_limit);
	ini_set('memory_limit', $memory_limit);
	ini_set('memory_limit', '-1');
	
############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}


	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	$user = new Session();

	function save_ws_lang($lang="pt-BR"){
		$config = file_get_contents(INCLUDE_PATH.'ws-config.php');
		$linhas = explode(PHP_EOL,$config);
		$newDoc = array();
		$i 		= 0;
		foreach ($linhas as $key => $value) {
			if(strpos($value,'if(!defined("LANG"))')>-1){
				$newDoc[]='	if(!defined("LANG"))		define("LANG",			"'.$lang.'");';
			}else{
				$newDoc[]=$value;
			}
			$i++;
		}
		file_put_contents(INCLUDE_PATH.'ws-config.php', implode($newDoc,PHP_EOL));
	}


	function substituiTopo() {
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . 'setupdata');
		$U->set_where('id="1"');
		$U->set_update('imagem_topo', $_REQUEST['img']);
		if ($U->salvar()) {
			echo $_REQUEST['img'];
		}
	}
	function substituiSplash() {
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . 'setupdata');
		$U->set_where('id="1"');
		$U->set_update('splash_img', $_REQUEST['img'][0]);
		if ($U->salvar()) {
			echo $_REQUEST['img'][0];
		}
	}
	
	
	################################################# SPLIT FILES
	class PWS_file_splitter {
		protected $file_name;
		protected $target_folder;
		protected $piece_size;
		const BUFFER = 1024;
		public function PWS_file_splitter($file_name, $target = "downloads", $piece_size = 10) {
			$this->file_name     = $file_name;
			$this->target_folder = $target;
			$this->piece_size    = $piece_size;
			$this->split_now();
		}
		public function split_now() {
			$piece    = 1048576 * $this->piece_size;
			$current  = 0;
			$splitnum = 1;
			if (!file_exists($this->target_folder)) {
				if (!mkdir($this->target_folder)) {
					die("Ocorreu um erro ao criar o caminho de destino");
				}
			}
			if (!$handle = fopen($this->file_name, "rb")) {
				die("Erro ao abrir o arquivo $this->file_name para leitura!");
			}
			$base_filename = basename($this->file_name);
			$piece_name    = $this->target_folder . '/' . $base_filename . '.' . str_pad($splitnum, 3, "0", STR_PAD_LEFT);
			if (!$fw = fopen($piece_name, "w")) {
				die("Erro para abrir o arquivo $piece_name para leitura. Confira as permições da pasta de destino.");
			}
			while (!feof($handle) and $splitnum < 999) {
				if ($current < $piece) {
					if ($content = fread($handle, PWS_file_splitter::BUFFER)) {
						if (fwrite($fw, $content)) {
							$current += PWS_file_splitter::BUFFER;
						} else {
							die("Erro, o script não possui permissão de escrita na pasta destino.");
						}
					}
				} else {
					fclose($fw);
					$current = 0;
					$splitnum++;
					$piece_name = $this->target_folder . '/' . $base_filename . '.' . str_pad($splitnum, 3, "0", STR_PAD_LEFT);
					$fw         = fopen($piece_name, "w");
				}
			}
			fclose($fw);
			fclose($handle);
		}
		public function change_file_name($new_file_name) {
			$this->file_name = $new_file_name;
		}
		public function change_target($new_target) {
			$this->target_folder = $new_target;
		}
		public function change_piece_size($new_piece_size) {
			$this->piece_size = $new_piece_size;
		}
	}
	
	########################################################################################### JUNTA PARTES
	function juntaPartes($file = "", $diretorio = "./") {
		$folder   = opendir($diretorio);
		$splitnum = 1;
		$fp       = fopen($diretorio . "/" . $file, "w");
		while ($item = readdir($folder)) {
			if ($item != "." && $item != "..") {
				$arquivo = $diretorio . "/" . $file . "." . str_pad($splitnum, 3, "0", STR_PAD_LEFT);
				if (file_exists($arquivo)) {
					$conteudo = file_get_contents($arquivo);
					unlink($arquivo);
					fwrite($fp, $conteudo);
				}
				$splitnum++;
			}
		}
		fclose($fp);
	}
	###########################################################################################
	//http://websheep_producao.com/admin/app/ws-modules/ws-domain/functions.php?function=ClassFTP
	
	function ClassFTP() {
		include('./FtpClient.php');
		$ftp = new FtpClient();
		$ftp->connect('ftp.websheep.com.br');
		$ftp->login('admin@websheep.com.br', base64_decode('aXNyYWZlbGl6'));
		//$ftp->createDirectory('/public_html/__WS__FILES__');
		echo $ftp->verifyFiles('/');
		//echo "Fecha conexão...<br>";
		//echo "Conexão é (".$ftp->conection().")<br>";
		//echo "Agora é (".$ftp->conection(1).")<br>";
		//$ftp->createDirectory('/public_html/__WS__FILES__');
		//print_r($ftp->listDirectory('/public_html'));
		//$ftp->removeDirectory('/AAAAAAA');
		
		//$ftp->put('/public_html/__WS__FILES__/WS_FTP_BKP.zip.003', './splitFiles/WS_FTP_BKP.zip.003');
		//$ftp->delete($filename);
		//$ftp->removeDirectory($directoryName);
		//$ftp->allocate($filesize);
		//$ftp->rename($current, $new);
	}
	
	
	
	function enviaFTP() {
		include('./FtpClient.php');
		//Marcador de início
		$ftp = new FTPClient();
		$ftp->connect('ftp.websheep.com.br', false, 21, 50);
		$ftp->login('admin@websheep.com.br', base64_decode('aXNyYWZlbGl6'));
		$ftp->passive();
		$ftp->binary(true);
		//	$ftp->createDirectory('./public_html/__WS__FILES__');
		inicia:
		// ABRE A PASTA LOCAL COM OS ARQUIVOS, E FAZ O WHILE
		$folder = opendir('./splitFiles');
		while ($item = readdir($folder)) {
			if ($item != '.' && $item != '..') {
				$ftp->conection(1);
				$File_FTP        = './public_html/__WS__FILES__/' . $item;
				$File_LOCAL      = './splitFiles/' . $item;
				$size_ftp_file   = $ftp->size($File_FTP);
				$size_local_file = filesize($File_LOCAL);
				$ftp->allocate($size_local_file);
				if (!$ftp->verifyFiles($file) || $size_ftp_file != $size_local_file) {
					$ftp->put('/public_html/__WS__FILES__/' . $item, './splitFiles/' . $item);
				} else {
					echo "Já existe: " . $file . "<br>";
					unlink('./splitFiles/' . $item);
				}
			}
		}
		//FINALIZANDO O WHILE, ELE AGORA FAZ O WHILE NOVAMENTE NOS ARQUIVOS PARA VERIFICAR SE TODOS ESTÃO LÁ...
		while ($item = readdir($folder)) {
			if ($item != '.' && $item != '..') {
				$File_FTP        = './public_html/__WS__FILES__/' . $item;
				$File_LOCAL      = './splitFiles/' . $item;
				$size_ftp_file   = $ftp->size($File_FTP);
				$size_local_file = filesize($File_LOCAL);
				$full            = true;
				if (!$ftp->verifyFiles($file) || $size_ftp_file != $size_local_file) {
					sleep(3);
					goto inicia;
				}
			}
		}
		$ftp->put('/public_html/descompacta_WS_BKP.php', './descompacta_WS_BKP.php');
		
		/*
		$upload = ftp_put($conn_id,'/public_html/descompacta_WS_BKP.php', './descompacta_WS_BKP.php', FTP_BINARY);
		if($upload){
		echo "OK";
		}else{
		echo "FALHA DE CONEXÃO";
		goto inicia;
		}
		ftp_close($conn_id); 
		/**/
	}
	
	
	function VerificaPacote() {
		global $_dir_theme_;
		global $_files_theme_;
		lista_Dir_theme("./../../.././");
		echo json_encode(array(
			'file_exists' => file_exists('./FTP_Transfer.zip'),
			'pastas' => count($_dir_theme_),
			'arquivos' => count($_files_theme_)
		));
	}

	function salvaDominio() {
		$inputs = array();
		parse_str($_REQUEST['inputs'], $inputs);
		if (empty($inputs['SMTP_Auth'])) {
			$inputs['SMTP_Auth'] = '0';
		} else {
			$inputs['SMTP_Auth'] = '1';
		}
		if (empty($inputs['DomainStatus'])) {
			$inputs['DomainStatus'] = '0';
		} else {
			$inputs['DomainStatus'] = '1';
		}
		if (empty($inputs['congelaFull'])) {
			$inputs['congelaFull'] = '0';
		} else {
			$inputs['congelaFull'] = '1';
		}
		if (empty($inputs['DomainCache'])) {
			$inputs['DomainCache'] = '0';
		} else {
			$inputs['DomainCache'] = '1';
		}
		if (empty($inputs['ws_theme'])) {
			$inputs['ws_theme'] = '';
		}
		
		$S        = new MySQL();
		$S->table = PREFIX_TABLES . 'setupdata';
		$S->set_update('smtp_host'			, $inputs['SMTP_Host']);
		$S->set_update('smtp_port'			, $inputs['SMTP_Porta']);
		$S->set_update('smtp_auth'			, $inputs['SMTP_Auth']);
		$S->set_update('smtp_email'			, $inputs['SMTP_Email']);
		$S->set_update('smtp_senha'			, $inputs['SMTP_Senha']);
		$S->set_update('domain_status'		, $inputs['DomainStatus']);
		$S->set_update('hd'					, $inputs['hd']);
		$S->set_update('theme'				, $inputs['ws_theme']);
		$S->set_update('auto_save'			, $inputs['auto_save']);
		$S->set_update('congelaFull'		, $inputs['congelaFull']);
		$S->set_update('url_congelamento'	, $inputs['url_congelamento']);
		$S->set_update('ws_cache'			, $inputs['DomainCache']);

		$dir = (INCLUDE_PATH.'ws-cache');
		if ($inputs['DomainCache'] == '0') {
			if (file_exists($dir) && is_dir($dir)) {
				_excluiDir($dir);
			}
		} else {
			if (!file_exists($dir) && !is_dir($dir)) {
				mkdir($dir);
			}
		}
		if ($S->salvar()) {

			save_ws_lang($inputs['ws_lang']);

			echo "Ítem salvo com sucesso!";
			exit;
		} else {
			echo "Ops houve uma falha!";
			exit;
		}
	}
	
	
	/**
	PREPARA TRANSFERÊNCIA
	**/
	$_dir_theme_   = array();
	$_files_theme_ = array();

	function lista_Dir_theme($diretorio, $admin = 0) {
		global $_dir_theme_;
		global $_files_theme_;
		$folder = opendir($diretorio);
		while ($item = readdir($folder)) {
			if ($item == '.' || $item == '..') {
				continue;
			}
			if (is_dir($diretorio . '/' . $item)) {
				if ($admin == 1) {
					if ($diretorio . $item != './../../..admin' && $diretorio . $item != './../../../admin') {
						$newDir = str_replace(array(
							'./../../.././',
							'./../../../'
						), "", $diretorio . '/' . $item);
						array_push($_dir_theme_, $newDir);
						lista_Dir_theme($diretorio . '/' . $item);
					}
				} else {
					$newDir = str_replace(array(
						'./../../.././',
						'./../../../'
					), "", $diretorio . '/' . $item);
					array_push($_dir_theme_, $newDir);
					lista_Dir_theme($diretorio . '/' . $item);
				}
			} else {
				$newDir = str_replace(array(
					'./../../..//',
					'./../../.././',
					'./../../../'
				), "", $diretorio . '/' . $item);
				if ($newDir != ".htaccess" && $newDir != "index.php") {
					array_push($_files_theme_, $newDir);
				}
			}
		}
	}
	function delZIPtoFTP() {
		if (file_exists('./' . $_REQUEST['namefile'])) {
			unlink('./' . $_REQUEST['namefile']);
		}
	}
	
	function CompactarZIPtoFTP() {
		global $_dir_theme_;
		global $_files_theme_;
		global $_conectMySQLi_;
		$nome_bkp      = '_bkp_website_' . date("m-d-y") . ".zip";
		########################################################################################################################### compacta o Site e seu conteudo
		$_dir_theme_   = array();
		$_files_theme_ = array();
		lista_Dir_theme('./../../../', 1);
		lista_Dir_theme('./../../../admin/plugins');
		$tables   = array();
		$tables[] = 'ws_ferramentas';
		$tables[] = 'ws_pages';
		$tables[] = 'ws_biblioteca';
		$tables[] = 'ws_template';
		$tables[] = '_model_campos';
		$tables[] = '_model_cat';
		$tables[] = '_model_files';
		$tables[] = '_model_gal';
		$tables[] = '_model_img';
		$tables[] = '_model_img_gal';
		$tables[] = '_model_item';
		$tables[] = '_model_link_op_multiple';
		$tables[] = '_model_link_prod_cat';
		$tables[] = '_model_op_multiple';
		$return   = "";
		foreach ($tables as $table) {
			$result     = mysqli_query($_conectMySQLi_, 'SELECT * FROM ' . ws::preventMySQLInject($table));
			$num_fields = mysqli_num_fields($result);
			$return .= 'DROP TABLE IF EXISTS ' . $table . ';';
			$row2 = mysqli_fetch_row(mysqli_query($_conectMySQLi_, 'SHOW CREATE TABLE ' . ws::preventMySQLInject($table)));
			$return .= PHP_EOL . $row2[1] . ";" . PHP_EOL;
			for ($i = 0; $i < $num_fields; $i++) {
				while ($row = mysqli_fetch_row($result)) {
					$return .= 'INSERT INTO ' . $table . ' VALUES(';
					for ($j = 0; $j < $num_fields; $j++) {
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_replace("\n", PHP_EOL, $row[$j]);
						if (isset($row[$j])) {
							$return .= '"' . $row[$j] . '"';
						} else {
							$return .= '""';
						}
						if ($j < ($num_fields - 1)) {
							$return .= ',';
						}
					}
					$return .= ");" . PHP_EOL;
				}
			}
			$return .= PHP_EOL . PHP_EOL . PHP_EOL;
		}
		$z     = new ZipArchive();
		$criou = $z->open('./' . $nome_bkp, ZipArchive::CREATE);
		if ($criou === true) {
			$z->addFromString('setup.sql', $return);
			$z->addEmptyDir('admin');
			$z->addEmptyDir('admin/plugins');
			$z->addEmptyDir('admin/ws-modules');
			$z->addEmptyDir('admin/app/ws-modules/ws-model-tool');
			$z->addEmptyDir('admin/app/ws-modules/ws-model-tool/uploads');
			$z->addFile('./img/go-backup-icon.jpg', 'screenshot.jpg');
			$ws_biblioteca = new MySQL();
			$ws_biblioteca->set_table(PREFIX_TABLES . 'ws_biblioteca');
			$ws_biblioteca->select();
			foreach ($_dir_theme_ as $dir) {
				$z->addEmptyDir($dir);
			}
			foreach ($_files_theme_ as $file) {
				$z->addFile('./../../../' . $file, $file);
			}
			foreach ($ws_biblioteca->fetch_array as $tabela) {
				$paste_image = '_modulo_/uploads/' . $tabela['file'];
				$z->addFile('./../' . $paste_image, $paste_image);
			}
			$z->close();
		}
		
		$indexZip = '<?
				if (class_exists("ZipArchive")) { 
					$zip = new ZipArchive;
					$zip->open("./WS_FTP_BKP.zip");
					$zip->extractTo("./");
					$zip->close();
					header("Location: /");
				 }else{print "Não existe a classe ZipArchive";} 
			?>';
		
		########################################################################################################################### compacta apenas WS
		$_dir_theme_   = array();
		$_files_theme_ = array();
		lista_Dir_theme(INCLUDE_PATH.'admin');
		$path   = "./splitFiles";
		$folder = opendir($path);
		while ($item = readdir($folder)) {
			if ($item != "." && $item != "..") {
				unlink($path . '/' . $item);
			}
		}
		if (file_exists('./WS_FTP_BKP.zip')) {
			unlink('./WS_FTP_BKP.zip');
		}
		$z     = new ZipArchive();
		$criou = $z->open('./WS_FTP_BKP.zip', ZipArchive::CREATE);
		if ($criou === true) {
			$z->addEmptyDir('admin');
			foreach ($_dir_theme_ as $dir) {
				$z->addEmptyDir($dir);
			}
			foreach ($_files_theme_ as $file) {
				if (dirname($file) != 'admin/app/ws-modules/ws-model-tool/uploads' && $file != "admin/app/ws-modules/ws-domain/" . $nome_bkp) {
					$z->addFile('./../../../' . $file, $file);
				}
			}
			$z->addFile('./' . $nome_bkp, INCLUDE_PATH.'ws-bkp/' . $nome_bkp);
			$z->addFromString(INCLUDE_PATH.'firstaccess', '1');
		}
		//	$z->addFromString('index.php', $indexZip);
		
		if ($z->close()) {
			unlink('./' . $nome_bkp);
			sleep(0.5);
			rename('./WS_FTP_BKP.zip', $nome_bkp);
			echo json_encode(array(
				'diretorios' => count($_dir_theme_),
				'arquivos' => count($_files_theme_),
				'namefile' => $nome_bkp,
				'pesoZip' => filesize('./' . $nome_bkp)
			));
			//	$my_splitter = new PWS_file_splitter("./WS_FTP_BKP.zip","./splitFiles",10);
		}
	}
	######################################################################
	_session();
	_exec($_REQUEST['function']);