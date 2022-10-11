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

@ob_start();
include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');


function AddCampo(){
	$s= new MySQL();
	$s->set_table($_REQUEST['tabela']);
	$s->set_colum(array($_REQUEST['coluna']	,'TEXT NOT NULL'));
	if($s->add_column()){
		echo true;
	};
}

function exclRegistro(){
	$s= new MySQL();
	$s->set_table(PREFIX_TABLES.'wslead_'.strtolower($_REQUEST['tabela']));
	$s->set_where('id="'.$_REQUEST['id'].'"');
	$s->exclui();
	echo true;
}

function exclTabela(){
	$s= new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_list_leads');
	$s->set_where('token="'.$_REQUEST['tabela'].'"');
	$s->debug(0);
	if(!$s->exclui()){};
	$s= new MySQL();
	$s->set_table(PREFIX_TABLES.'wslead_'.strtolower($_REQUEST['tabela']));
	$s->debug(0);
	if(!$s->exclui_table()){};
	echo true;
}


function exclCampo(){
	$s= new MySQL();
	$s->set_table($_REQUEST['tabela']);
	$s->set_colum($_REQUEST['coluna']);
	if($s->exclui_column()){
		echo true;
	};
}

function SalvaLead(){
	$form = array();
	parse_str($_REQUEST['form'],$form);
	if(empty($form['resposta_ao_usuario'])){$form['resposta_ao_usuario']=0;}else{$form['resposta_ao_usuario']=1;}
	if(empty($form['smtp_local'])){$form['smtp_local']='0';}else{$form['smtp_local']='1';}
	if(empty($form['server_ssl'])){$form['server_ssl']='0';}else{$form['server_ssl']='1';}
	if(empty($form['camp_mail_clt'])){$form['camp_mail_clt']='';}
	$Salva = new MySQL();
	$Salva->set_table(PREFIX_TABLES.'ws_list_leads');
	$Salva->set_where('token="'.$form['token'].'"');
	$Salva->set_update('title',$form['title']);
	$Salva->set_update('content',$form['content']);
	$Salva->set_update('finalidade',$form['finalidade']);
	$Salva->set_update('resposta_ao_usuario',$form['resposta_ao_usuario']);
	$Salva->set_update('smtp_local',$form['smtp_local']);
	$Salva->set_update('SMTPSecure',$form['SMTPSecure']);
	$Salva->set_update('host',$form['host']);
	$Salva->set_update('port',$form['port']);
	$Salva->set_update('server_ssl',$form['server_ssl']);
	$Salva->set_update('email_envio',$form['email_envio']);
	$Salva->set_update('pass',$form['pass']);
	$Salva->set_update('remetente',$form['remetente']);
	$Salva->set_update('assunto',$form['assunto']);
	$Salva->set_update('msng_resp',$form['msng_resp']);
	$Salva->set_update('camp_mail_clt',$form['camp_mail_clt']);
	$Salva->set_update('msng_resp_user',$form['msng_resp_user']);
	$Salva->set_update('remetente_name',$form['remetente_name']);
	$Salva->set_update('url_sucess',$form['url_sucess']);
	$Salva->set_update('url_error',$form['url_error']);
	$Salva->set_update('assunto_clt',$form['assunto_clt']);
	$Salva->salvar();
	echo "sucesso!";
}


function substituiThumb(){

	$U					= new MySQL();
	$U->set_table(PREFIX_TABLES.'ws_list_leads');
	$U->set_where('token="'.$_REQUEST['token'].'"');
	if($_REQUEST['type']=='topo_email'){
		$U->set_update('header_email',$_REQUEST['img']);
	}elseif($_REQUEST['type']=='footer_email'){
		$U->set_update('footer_email',$_REQUEST['img']);
	}
	if($U->salvar()){
		echo ws::rootPath.'ws-img/'.$_REQUEST['width'].'/'.$_REQUEST['height'].'/'.$_REQUEST['img'];
		exit;
	}
}


function AddLead(){
	$token = _token(PREFIX_TABLES.'ws_list_leads','token');
	$s= new MySQL();
	$s->set_table(PREFIX_TABLES.'wslead_'.strtolower($token));
	$s->create_table();
	$I 					= new MySQL();
	$I->set_table(PREFIX_TABLES.'ws_list_leads');
	$I->set_insert('token',$token);
	$I->set_insert('title','Nova campanha');
	$I->set_insert('content','Sem descrição...');
	$I->insert();
}











_session();
_exec($_REQUEST['function']);
?>