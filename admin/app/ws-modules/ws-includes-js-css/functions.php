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

	ob_start();
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	ob_end_clean();
	function OrdenaItem()
	{
		foreach($_REQUEST['positions'] as $key)
		{
			$setupdata = new MySQL();
			$setupdata->set_table(PREFIX_TABLES . 'ws_link_url_file');
			$setupdata->set_where('id="' . $key['dataid'] . '"');
			$setupdata->set_update('position', $key['position']);
			$setupdata->salvar();
		}
	}
	function salvar()
	{
		$setupdata = new MySQL();
		$setupdata->set_table(PREFIX_TABLES . 'ws_link_url_file');
		$setupdata->set_where('id="' . $_REQUEST['id_file'] . '"');
		$setupdata->set_update('include_media', $_REQUEST['inputMedia']);
		$setupdata->set_update('include_id', $_REQUEST['inputID']);
		$setupdata->salvar();
	}
	function incluir2()
	{
		foreach($_REQUEST['file'] as $file)
		{
			$ext     = explode('.', $file);
			$ext     = end($ext);
			$NewPage = new MySQL();
			$NewPage->set_table(PREFIX_TABLES . 'ws_link_url_file');
			$NewPage->set_Insert('file', $file);
			$NewPage->set_Insert('id_url', $_REQUEST['idPage']);
			$NewPage->set_Insert('ext', $ext);
			$NewPage->insert();
		}
	}
	function incluir(){
		$_REQUEST['file'] = trim($_REQUEST['file']);
		$token            = _token(PREFIX_TABLES . 'ws_link_url_file', 'token');
		$ext              = explode('.', $_REQUEST['file']);
		$ext              = end($ext);

		$verify = new MySQL();
		$verify->set_table(PREFIX_TABLES.'ws_link_url_file');
		$verify->set_where('id_url="'. 	$_REQUEST['idPage'] . '"');
		$verify->set_where('AND file="'.	$_REQUEST['file'].'"');
		$verify->set_where('AND ext="'.	$ext.'"');
		$verify->set_order('position', 'ASC');
		$verify->select();

		if($verify->_num_rows>=1){exit;}

		$NewPage          = new MySQL();
		$NewPage->set_table(PREFIX_TABLES . 'ws_link_url_file');
		$NewPage->set_Insert('file', $_REQUEST['file']);
		$NewPage->set_Insert('id_url', $_REQUEST['idPage']);
		$NewPage->set_Insert('ext', $ext);
		$NewPage->set_Insert('token', $token);
		if($NewPage->insert())
		{
			$template   = new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-includes-js-css/ws-tool-urls-js-css.html', true);
			$SelectPage = new MySQL();
			$SelectPage->set_table(PREFIX_TABLES . 'ws_link_url_file');
			$SelectPage->set_where('id_url="' . $_REQUEST['idPage'] . '"');
			$SelectPage->set_where('AND ext="' . $ext . '"');
			$SelectPage->set_order('position', 'ASC');
			$SelectPage->select();
			foreach($SelectPage->fetch_array as $value)
			{
				$template->ID = $value['id'];
				$template->clear('CLASS');
				$template->clear('CLASS_INCLUDE');
				$template->FILE    = $value['file'];
				$template->URL     = $value['id_url'];
				$template->DISPLAY = "block";
				$template->block("GETFILE");
			}
			$template->show();
		}
		else
		{
			echo "falha";
		}
	}
	function excluir()
	{
		$SelectPage = new MySQL();
		$SelectPage->set_table(PREFIX_TABLES . 'ws_link_url_file');
		$SelectPage->set_where('id="' . $_REQUEST['id_file'] . '"');
		$SelectPage->select();
		$ext = $SelectPage->fetch_array[0]['ext'];

		#---------------------------------------------------------

		$I   = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_link_url_file');
		$I->set_where('id="' . $_REQUEST['id_file'] . '"');
		if($I->exclui())
		{
			$template   = new Template(INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-includes-js-css/ws-tool-urls-js-css.html', true);
			$SelectPage = new MySQL();
			$SelectPage->set_table(PREFIX_TABLES . 'ws_link_url_file');
			$SelectPage->set_where('id_url="' . $_REQUEST['id_url'] . '"');
			$SelectPage->set_where('AND ext="' . $ext . '"');
			$SelectPage->set_order('position', 'ASC');
			$SelectPage->select();
			foreach($SelectPage->fetch_array as $value)
			{
				$template->ID = $value['id'];
				$template->clear('CLASS');
				$template->FILE    = $value['file'];
				$template->URL     = $value['id_url'];
				$template->DISPLAY = "block";
				$template->block("GETFILE");
			}
			$template->show();
		}
		else
		{
			echo "falha";
		}
	}
	_session();
	_exec($_REQUEST['function']);
?>

