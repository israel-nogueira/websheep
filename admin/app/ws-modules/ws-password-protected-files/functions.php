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

function getInfoLink(){

		$link = new MySQL();
		$link->set_table(PREFIX_TABLES.'ws_keyfile');
		$link->set_where('id="'.$_REQUEST['idLink'].'"');
		$link->select();
		if($link->obj[0]->active==1){			$active				='checked="true"';	}else{	$active="";				}
		if($link->obj[0]->disableToDown==1){	$disableToDown		='checked="true"';	}else{	$disableToDown="";		}
		if($link->obj[0]->refreshToDown==1){	$refreshToFirstDown	='checked="true"';	}else{	$refreshToFirstDown="";	}
		if($link->obj[0]->refreshToDown==2){	$refreshToAllDown	='checked="true"';	}else{	$refreshToAllDown="";	}


		echo '<form id="formLink"><div style="position: relative;float: left;height: 278px;width: calc(100% - 20px);padding: 10px;">'
			.'<div style="font-size: 30px; margin: -10px 0px; ">'.ws::getLang("keyFile>links>modal>details>mainTitle").'</div>'
			.'<input type="hidden" name="tokenFile" value="'.$link->obj[0]->tokenFile.'">'
			.'<div class="c"></div><br>'
			.'<input name="idLink" type="hidden" value="'.$_REQUEST['idLink'].'"/>'
			.'<label class="label" style="position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 11px 10px;">'
			.'<i class="fa fa-external-link" style="top: 1px; position: relative; margin-right: 7px; "></i>'
			.'<a class="AlinkAccess" href="http://'.DOMINIO.ROOT_WEBSHEEP.'ws-download/'.$link->obj[0]->tokenFile.'" target="_Blank" style="text-decoration: none; color: #4678b9; font-weight: bold; "> » '.ws::getLang("keyFile>links>modal>details>link").'</a><br>'
			.'</label>'
			.'<label class="label" style="position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 11px 10px;">'
			.'<i class="fa fa-external-link" style="top: 1px; position: relative; margin-right: 7px; "></i>'
			.'<a class="AlinkAccessKey" href="http://'.DOMINIO.ROOT_WEBSHEEP.'ws-download/'.$link->obj[0]->tokenFile.'!'.$link->obj[0]->keyaccess.'" target="_Blank" style="text-decoration: none; color: #4678b9; font-weight: bold; "> » '.ws::getLang("keyFile>links>modal>details>linkKey").'</a>'
			.'<input type="hidden" class="inputlinkAccessKey" name="inputlinkAccessKey" value="http://'.DOMINIO.ROOT_WEBSHEEP.'ws-download/'.$link->obj[0]->tokenFile.'!'.$link->obj[0]->keyaccess.'"/>'
			.'<i class="icon-refresh" legenda="Atualizar serialKey" style="position: absolute; float: right; right: 10px; cursor: pointer; font-weight: bold; color: #38629b; "></i>'
			.'</label>'
			.'<label class="label" style="cursor: pointer;position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 10px;">'
			.'<input name="linkActive" type="checkbox" '.$active.'> '.ws::getLang("keyFile>links>modal>details>activeLink").'</label>'
			.'<label class="label" style="cursor: pointer;position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 10px;">'
			.'<input name="disableToDown" type="checkbox" '.$disableToDown.'>'.ws::getLang("keyFile>links>modal>details>disableLinkFirstAccess").'</label>'
			.'<label class="label" style="cursor: pointer;position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 10px;">'
			.'<input name="refreshToFirstDown" type="checkbox" '.$refreshToFirstDown.'>'.ws::getLang("keyFile>links>modal>details>refreshLinkFirstAccess").'</label>'
			.'<label class="label" style="cursor: pointer;position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 10px;">'
			.'<input name="refreshToAllDown" type="checkbox" '.$refreshToAllDown.'>'.ws::getLang("keyFile>links>modal>details>refreshLinkWithEveryDownload").'</label>'
			.'<label class="label" style="position: relative;text-align: left;float: left;width: calc(100% - 20px);height: 13px;border-bottom: dotted 1px #4E5157 !important;padding: 11px 10px;">'
			.ws::getLang("keyFile>links>modal>details>expireIn").' <input name="expire" value="'.$link->obj[0]->expire.'" legenda="'.ws::getLang("keyFile>links>modal>details>formatDate").'" class="inputText" type="date" placeHolder="____-__-__"  style="padding: 6px 10px; top: -11px; position: relative; float: right;"/></label>'
			.'</div></form>';
}

function salvaLink(){
	$form = array();
	parse_str($_REQUEST['form'],$form);
	$refreshLink=0;
	if(empty($form['linkActive'])){				$form['linkActive']	=0;		}else{	$form['linkActive']=1;}
	if(empty($form['disableToDown'])){			$form['disableToDown']	=0;	}else{	$form['disableToDown']=1;}
	if(isset($form['refreshToFirstDown'])){		$refreshLink=1;	}
	if(isset($form['refreshToAllDown'])){		$refreshLink=2;	}

	$data = explode('/',$form['expire']);
	$Salva = new MySQL();
	$Salva->set_table(PREFIX_TABLES.'ws_keyfile');
	$Salva->set_where('id="'.$form['idLink'].'"');
	$Salva->set_update('tokenFile',$form['tokenFile']);
	$Salva->set_update('active',$form['linkActive']);
	$Salva->set_update('expire',$form['expire']);
	$Salva->set_update('disableToDown',$form['disableToDown']);
	$Salva->set_update('refreshToDown',$refreshLink);
	$Salva->salvar();
	echo json_encode(array(
		'resposta'=>'sucesso',
		'inputlinkAccessKey'=>$form['inputlinkAccessKey']
		));
}

function AtualizaKey(){
	$newCode = _codePass(_crypt());
	$I= new MySQL();
	$I->set_table(PREFIX_TABLES.'ws_keyfile');
	$I->set_where('id="'.$_REQUEST['idLink'].'"');
	$I->set_update('keyaccess',$newCode);
	$I->set_update('accessed','0');
	if($I->salvar()){
		echo $newCode;
	}else{
		echo "falha";
	};
	exit;
}

function exclLink(){
	$s= new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_keyfile');
	$s->set_where('id="'.$_REQUEST['idLink'].'"');
	$s->exclui();
	echo true;
}

function AddLink(){
	$s= new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_keyfile');
	$s->set_insert('keyaccess',_codePass(_crypt()));
	$s->set_insert('tokenFile',$_REQUEST['tokenFile']);
	$s->insert();
}
_session();
_exec($_REQUEST['function']);
?>