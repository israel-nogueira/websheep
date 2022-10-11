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

############################################################################
# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
############################################################################
if(verifyUserLogin(true)==false){
	die("Error!");
};

function templateUser($_id_,$_nome_,$_sobrenome_,$_email_,$_thumb_){

	$template="";
	$template .= '<li class="parceirobase bg06 w2" id="'.$_id_.'">';
	if($_thumb_==""){
		$template .= '<div class="thumbuser bg02"><img src="./app/templates/img/websheep/Sem_avatar.png" /></div>';
	}else{
		$template .= '<div class="thumbuser bg02"><img src="./app/ws-modules/ws-users/upload/'.$_thumb_.'" width="65" height="39" /></div>';
	}
	$template .= ' <div class="dadosuser"><div class="w1 tituloUser">'.$_nome_.' '.$_sobrenome_.'</div>
					<div class="email w2">'.$_email_.'</div> </div><div class="acoes bg02">
					<div class="excluir legenda" legenda="Excluir usuário"></div>
					<div class="editar legenda" legenda="Editar permissões"></div>	
				</div>
		</li>';
	return $template;	

			
}
function addUser(){
			$user = new Session();
			$token = _token (PREFIX_TABLES."ws_usuarios",'token');
			$I 					= new MySQL();
			$I->set_table(PREFIX_TABLES.'ws_usuarios');
			$I->set_insert('id_criador',$user->get('id'));
			$I->set_insert('nome',$_REQUEST['nome']);
			$I->set_insert('email','myemail@domain.com');
			$I->set_insert('token',$token);

			if($I->insert()){
				$s 					= new MySQL();
				$s->set_table(PREFIX_TABLES.'ws_usuarios');
				$s->set_where('token="'.$token.'"');
				$s->select();
				$uret = $s->fetch_array[0];
				echo templateUser($uret['id'],$uret['nome'],$uret['sobrenome'],$uret['email'],$uret['avatar']);
			}else{
				echo false;
			}
}


function exclui_user(){
	$U					= new MySQL();
	$U->set_where('id="'.$_REQUEST['iD'].'"');
	$U->set_table(PREFIX_TABLES.'ws_usuarios');
	$U->set_update('ativo','0');
	if($U->salvar()){
		echo "1";
	}else{

		echo "0";
	};
}
function reloadUser()			{

			$user = new Session();
			$user_mysql 					= new MySQL();
			$user_mysql->set_table(PREFIX_TABLES.'ws_usuarios');
			$user_mysql->set_where('id="'.$_REQUEST['iD_user'].'"');
			$user_mysql->select();

			$cargo 					= new MySQL();
			$cargo->set_table(PREFIX_TABLES.'ws_usuarios');
			$cargo->set_where('id="'.$user_mysql->fetch_array[0]['id_cargo'].'"');
			$cargo->select();

		echo templateUser(
			$user_mysql->fetch_array[0]['id'],
			$user_mysql->fetch_array[0]['nome'],
			$user_mysql->fetch_array[0]['sobrenome'],
			$user_mysql->fetch_array[0]['email'],
			$user_mysql->fetch_array[0]['avatar']
			);
	}

function LoadDadosUser(){
	$user = new Session();

	if($_REQUEST['iD']=='undefined'){	echo "ID de usuario não encontrado.";	exit;	};

			$s 					= new MySQL();
			$s->set_table(PREFIX_TABLES.'ws_usuarios');
			$s->set_where('id="'.$_REQUEST['iD'].'"');
			$s->select();
			echo '<div id="DadosUserBase" class="content_2">
					<form action="./app/ws-modules/ws-users/upload_files.php?iD='.$s->fetch_array[0]['id'].'" method="post" enctype="multipart/form-data" name="formUpload" id="formUpload">
		          	<input type="file" id="botUpload" name="myfile" /><input type="submit" class="botao botupload" value="Fazer Upload" id="botaoUpload"></form>
					<form id="dadosUserForm">
					<div id="thumbUpload"></div>
					<div id="progress" class="bg06">
					<div id="bar" class="bg05"></div>
					</div>
					<div id="thumb">';
					if($s->fetch_array[0]['avatar']!=""){echo '<img src="./app/ws-modules/ws-users/upload/'.$s->fetch_array[0]['avatar'].'" width="200" height="200"/>';} 
			echo '	</div>
					<input name="nome" id="nome" value="'.$s->fetch_array[0]['nome'].'">
					<input name="id_user"  value="'.$s->fetch_array[0]['id'].'" hidden="true">
					<div id="cargo" >
					<select id="cargoUser" name="Cargo">
					<option>Selecione um cargo</option>';

					$cargo 					= new MySQL();
					$cargo->set_table(PREFIX_TABLES.'ws_cargos');
					$cargo->set_order('cargo ASC');
					$cargo->set_template('<option value="{{id}}" {{selected}}>{{cargo}}</option>');
					$cargo->select();
					foreach($cargo->fetch_array as $row) {
						if($s->fetch_array[0]['id_cargo']==$row['id']){$row["selected"]="selected";}else{$row["selected"]="";};
						echo $cargo->set_template($row);;
					};
				echo '</select>
					</div>
					<textarea id="descricao" name="Descricao" >'.$s->fetch_array[0]['descricao'].'</textarea>
					<div class="tit">Dados de contato:</div>
					<div class="box">';
						if($s->fetch_array[0]['id']!=$user->get('id') && $user->get('admin')=='1'){
								echo '<div>';
						}else{
								echo '<div style="display:none">';
						}
								if($s->fetch_array[0]['ativo']=='1'){$checked="checked";}else{$checked="";}
								echo '<input type="checkbox" id="ativo" name="ativo" '.$checked.'><label for="ativo">Usuário Ativo</label><br />';
								if($s->fetch_array[0]['admin']=='1'){$checked="checked";}else{$checked="";}
								echo '<input type="checkbox" id="admin" name="admin" '.$checked.'><label for="admin">Este usuário é administrador</label>';
								echo '<br />';
								echo '<select name="status" id="status_user" style="width:510px">';
								if($s->fetch_array[0]['id_status']=='0'){$selected="selected";}else{$selected="";}
								echo '<option value="0" '.$selected.'>Habilitado</option>';

								if($s->fetch_array[0]['id_status']=='1'){$selected="selected";}else{$selected="";}
								echo '<option value="1" '.$selected.'>Faze de avaliação</option>';
								
								if($s->fetch_array[0]['id_status']=='2'){$selected="selected";}else{$selected="";}
								echo '<option value="2" '.$selected.'>Bloqueado</option>';
								
								if($s->fetch_array[0]['id_status']=='3'){$selected="selected";}else{$selected="";}
								echo '<option value="3" '.$selected.'>Painel desativado</option>';
								echo '</select>
								<br><br><hr>';
								echo '</div>';


						echo '		Sobrenome: 					<input name="sobrenome" 		id="sobrenome" 	value="'.$s->fetch_array[0]['sobrenome'].'"><br />
									E-mail: 					<input name="Email" 			id="Email" 		value="'.$s->fetch_array[0]['email'].'"><br />
									Telefone:					<input name="Telefone" 			id="Telefone" 	value="'.$s->fetch_array[0]['telefone'].'"><br />
									Reside em: 					<input name="Reside"			id="Reside" 	value="'.$s->fetch_array[0]['endereco'].'"><br />
									Login de acesso: 			<input name="Login"				id="Login" 		value="'.$s->fetch_array[0]['login'].'"><br />
									Senha: 						<input name="SenhaWS" 			id="SenhaWS" 		value="" type="password" placeholder="Defina uma nova senha:"><br />
									CPF: 						<input name="CPF" 				id="CPF" 		value="'.$s->fetch_array[0]['CPF'].'"><br />
									RG: 						<input name="RG" 				id="RG" 		value="'.$s->fetch_array[0]['RG'].'"><br />
					</div>';
					if($s->fetch_array[0]['id']!=$user->get('id') && $user->get('admin')=='1'){
						echo '<div class="tit">Ele terá acesso a:</div>
							<div class="box">';
								$s 					= new MySQL();
								$s->set_table(PREFIX_TABLES.'ws_ferramentas');
								$s->select();
								foreach($s->fetch_array as $ferra){
									$perm_ferr 					= new MySQL();
									$perm_ferr->set_table(PREFIX_TABLES.'ws_user_link_ferramenta');
									$perm_ferr->set_where('id_user="'.$_REQUEST['iD'].'"');
									$perm_ferr->set_where('AND id_ferramenta="'.$ferra['id'].'"');
									$perm_ferr->select();
									if($perm_ferr->_num_rows!=0){$checked="checked";}else{$checked="";}


									
									echo '<input type="checkbox" id="'.$ferra['_tb_'].'" name="'.$ferra['_tb_'].'" '.$checked.'><label for="'.$ferra['_tb_'].'">'.$ferra['_tit_menu_'].'</label><br />';
									}
						echo '</div>';
						}
					echo '</form>';
}
function SalvadadosUser(){
	$user = new Session();
	if(isset($_REQUEST['PermTools']) && $_REQUEST['PermTools']!=""){$permissoes = explode(',',$_REQUEST['PermTools']);}else{$permissoes = array();}
	$D					= new MySQL();
	$D->set_table(PREFIX_TABLES.'ws_user_link_ferramenta');
	$D->set_where('id_user="'.$_REQUEST['idUser'].'"');
	$D->exclui();
	foreach($permissoes as $ferra){
		$I 					= new MySQL();
		$I->set_table(PREFIX_TABLES.'ws_user_link_ferramenta');
		$I->set_insert('id_user',$_REQUEST['idUser']);
		$I->set_insert('id_ferramenta',$ferra);
		$I->insert();
	}

	if(isset($_REQUEST['leitura']) && ($_REQUEST['leitura'])=='on')				{	$_REQUEST['leitura']='1';		}else{$_REQUEST['leitura']='0';}
	if(isset($_REQUEST['adminWS']) && ($_REQUEST['adminWS'])=='on')				{	$_REQUEST['adminWS']='1';		}else{$_REQUEST['adminWS']='0';}
	if(isset($_REQUEST['add_user']) && ($_REQUEST['add_user'])=='on')			{	$_REQUEST['add_user']='1';		}else{$_REQUEST['add_user']='0';}
	if(isset($_REQUEST['edit_only_own']) && ($_REQUEST['edit_only_own'])=='on')	{	$_REQUEST['edit_only_own']='1';	}else{$_REQUEST['edit_only_own']='0';}

	if(empty($_REQUEST['user_ativo']))			{	$_REQUEST['user_ativo']='1';}
	if(empty($_REQUEST['estado_do_usuario']))	{	$_REQUEST['estado_do_usuario']='5';}
	if(empty($_REQUEST['nome']))				{	$_REQUEST['nome']='user';}
	if(empty($_REQUEST['sobrenome']))			{	$_REQUEST['sobrenome']='user';}
	if(empty($_REQUEST['email']))				{	$_REQUEST['email']='myemail@domain.com';}
	if(empty($_REQUEST['login']))				{	$_REQUEST['login']='new_user';}
	if(empty($_REQUEST['senha']))				{	$_REQUEST['senha']='';}
	if(empty($_REQUEST['RG']))					{	$_REQUEST['RG']='';}
	if(empty($_REQUEST['CPF']))					{	$_REQUEST['CPF']='';}
	if(empty($_REQUEST['telefone']))			{	$_REQUEST['telefone']='';}
	if(empty($_REQUEST['endereco']))			{	$_REQUEST['endereco']='';}

	$U					= new MySQL();
	$U->set_where('id="'.$_REQUEST['idUser'].'"');
	$U->set_table(PREFIX_TABLES.'ws_usuarios');
	$U->set_update('ativo',$_REQUEST['user_ativo']);
	$U->set_update('id_status',$_REQUEST['estado_do_usuario']);
	$U->set_update('nome',$_REQUEST['nome']);
	$U->set_update('sobrenome',$_REQUEST['sobrenome']);
	$U->set_update('email',$_REQUEST['email']);
	$U->set_update('telefone',$_REQUEST['telefone']);
	$U->set_update('endereco',$_REQUEST['endereco']);
	$U->set_update('login',$_REQUEST['login']);
	if($_REQUEST['senha']!=""){$U->set_update('senha',_codePass($_REQUEST['senha']));};
	$U->set_update('CPF',$_REQUEST['CPF']);
	$U->set_update('RG',$_REQUEST['RG']);
	$U->set_update('admin',$_REQUEST['adminWS']);

	$U->set_update('add_user',$_REQUEST['add_user']);
	$U->set_update('edit_only_own',$_REQUEST['edit_only_own']);
	$U->set_update('leitura',$_REQUEST['leitura']);
	if($U->salvar()){
		echo "Usuário salvo com sucesso!";exit;
	};
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
