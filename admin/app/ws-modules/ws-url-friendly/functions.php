<?
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
	ob_start();
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	ob_end_clean();
	function SalvaPaths() {
		$inputs = array();
		parse_str($_REQUEST['Formulario'], $inputs);
		$setupdata = new MySQL();
		$setupdata->set_table(PREFIX_TABLES . 'setupdata');
		$setupdata->set_where('id="1"');
		$setupdata->set_update('url_initPath', $inputs['url_initPath']);
		$setupdata->set_update('url_setRoot', $inputs['url_setRoot']);
		$setupdata->set_update('url_set404', $inputs['url_set404']);
		$setupdata->set_update('url_plugin', $inputs['url_plugin']);
		if (isset($inputs['url_ignore_add']) && $inputs['url_ignore_add'] == "0") {
			$setupdata->set_update('url_ignore_add', '1');
		} else {
			$setupdata->set_update('url_ignore_add', '0');
		}
		if ($setupdata->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	function createHTACCESS() {
		$includes = new MySQL();
		$includes->set_table(PREFIX_TABLES . "ws_pages");
		$includes->set_where("(type='system' OR type='custom') AND path<>'' AND file<>''");
		$includes->select();
		$INCLUDES =PHP_EOL;
		foreach ($includes->fetch_array as $item) {
			$INCLUDES .= '		RewriteRule '.$item['path'] . '	./..' . $item['file'] . ' [L]'.PHP_EOL;
		}
		$modelo 	= INCLUDE_PATH.'admin/app/templates/txt/ws-model-htaccess.txt';
		$original 	= INCLUDE_PATH.'.htaccess';
		$editado = str_replace(
				array(
					'{{INCLUDES}}',
					'{{INCLUDE_PATH}}',
					'{{ROOT_WEBSHEEP}}'
				), 
				array(
					$INCLUDES,
					INCLUDE_PATH,
					ROOT_WEBSHEEP
				), 
				file_get_contents(
					$modelo
				)
			);
		file_put_contents($original,$editado);		
	}
	
	function addRules($urlPath = "", $nameFile = "") {
		$NewPage = new MySQL();
		$NewPage->set_table(PREFIX_TABLES . 'ws_pages');
		if ($urlPath != "" && $nameFile != "") {
			$NewPage->set_Insert('path', $urlPath);
			$NewPage->set_Insert('file', $nameFile);
		}
		$NewPage->set_Insert('type', 'custom');
		$NewPage->insert();
	}

	function salvaRules() {
		$idPath   = $_REQUEST['idPath'];
		$urlPath  = $_REQUEST['urlPath'];
		$nameFile = $_REQUEST['nameFile'];
		$type     = $_REQUEST['type'];
		$NewPage  = new MySQL();
		$NewPage->set_table(PREFIX_TABLES . 'ws_pages');
		$NewPage->set_where('id="' . $idPath . '"');
		$NewPage->set_update('path', $urlPath);
		if ($type == "custom") {
			$NewPage->set_update('file', $nameFile);
		}
		$NewPage->salvar();
		createHTACCESS();
		exit;
	}
	function addFile() {
		$token = _token(PREFIX_TABLES . 'ws_pages', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_pages');
		$I->set_insert('token', $token);
		$I->set_insert('type', 'include');
		if ($I->insert()) {
			echo "sucesso";
		}
	}
	function excluiRegistro() {
		$I = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_pages');
		$I->set_where('id="' . $_REQUEST['idPath'] . '"');
		$I->exclui();
		createHTACCESS();
	}
	
	
	
	
	
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	_session();
	_exec($_REQUEST['function']);
?>

