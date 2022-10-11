<?

/*#####################################################################################################/  
*
*
*	Para melhor manutenção, acumulei todos os scripts de upload em um só arquivo
*
*
######################################################################################################*/
	
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}


	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

	##########################################################################################################  
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	##########################################################################################################
		include_once(INCLUDE_PATH.'/admin/app/lib/class-ws-v1.php');

	##########################################################################################################  
	# CRIA SESSÃO
	##########################################################################################################  
		$session = new session();

	##########################################################################################################  
	# Limpa as informações em cache sobre arquivos
	##########################################################################################################
		clearstatcache();
		
	##########################################################################################################  
	# ERROS DE UPLOAD (inativo temporariamente)
	##########################################################################################################			
		$errorUpload 	= Array();
		$errorUpload[1] = 'UPLOAD_ERR_INI_SIZE: The uploaded file exceeds the upload_max_filesize directive in php.ini.';
		$errorUpload[2] = 'UPLOAD_ERR_FORM_SIZE: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
		$errorUpload[3] = 'UPLOAD_ERR_PARTIAL: The uploaded file was only partially uploaded.';
		$errorUpload[4] = 'UPLOAD_ERR_NO_FILE: No file was uploaded.';
		$errorUpload[5] = 'UPLOAD_ERR_NO_TMP_DIR: Missing a temporary folder. Introduced in PHP 5.0.3.';
		$errorUpload[6] = 'UPLOAD_ERR_CANT_WRITE: Failed to write file to disk. Introduced in PHP 5.1.0.';
		$errorUpload[7] = 'UPLOAD_ERR_EXTENSION: A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.';
		$errorUpload[8] = 'UPLOAD_ERR_NO_FILES: No attachments are uploaded.';
	##########################################################################################################  
	# CASO NÃO EXISTA O DIRETÓRIO PADRÃO PARA UPLOAD, CRIAMOS
	##########################################################################################################	

		if(!file_exists(INCLUDE_PATH.'website/assets')){				mkdir(INCLUDE_PATH.'website/assets');				}		
		if(!file_exists(INCLUDE_PATH.'website/assets/upload-files')){	mkdir(INCLUDE_PATH.'website/assets/upload-files');	}		

	##########################################################################################################  
	# DEFINIMOS O LOCAL PARA O UPLOAD   
	##########################################################################################################
		define("UPLOAD_DIR",INCLUDE_PATH.'website/assets/upload-files');

	##########################################################################################################  
	# VERIFICAMOS SE O DIRETÓRIO EXISTE E ESTÁ EM CONDIÇÕES DE RECEBER UPLOADS   
	##########################################################################################################			
		if(is_dir(UPLOAD_DIR) && is_writable(UPLOAD_DIR)) {

			##########################################################################################################  
			# VERIFICAMOS SE EXISTE ARQUIVOS ANEXADOS  
			##########################################################################################################
			if(count($_FILES)>=1){

				##########################################################################################################  
				# MONTAMOS UM ARRAY QUE IRÁ RECEBER TODOS OS DADOS DE RETORNO  
				##########################################################################################################
				 $_RETURN_FILES = Array();

				##########################################################################################################  
				# EXTENSÕES PROIBIDAS  
				##########################################################################################################
				 $_EXTENSION_DENIED = Array("php","html","js","bat","sh","exe");

				##########################################################################################################  
				# VARREMOS OS ARQUIVOS  
				##########################################################################################################
				foreach ($_FILES as $key => $__FILE__) {
					##########################################################################################################  
					# CASO SEJA MULTIPLOS UIPLOADS  
					##########################################################################################################
					if(is_array($__FILE__['name'])){
						for ($i=0; $i < count($__FILE__['name']); $i++) { 
							$tmp_name 	= $__FILE__["tmp_name"][$i];
							$size 		= $__FILE__["size"][$i];
							$type		= $__FILE__["type"][$i];
							$nome 		= url_amigavel_filename($__FILE__["name"][$i]);
							$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
							$ext		= str_replace(array("jpeg"),array("jpg"),$ext);
							if(in_array($ext, $_EXTENSION_DENIED)) {
								echo json_encode(array('status'=>'falha','response'=>'Formato ilegal:"'.$ext.'"', 'error'=>'move_uploaded_file','linha'=>__LINE__)); 
								exit;
							}
							$token 		= md5(uniqid(rand(), true));
							##########################################################################################################  
							# Retorna TRUE se o arquivo com o nome filename foi enviado por POST HTTP  
							# Isto é útil para ter certeza que um usuário malicioso não está tentando levar o script a trabalhar 
							# em arquivos que não deve estar trabalhando --- por exemplo, /etc/passwd.
							##########################################################################################################
							if(is_uploaded_file($tmp_name)){

								##########################################################################################################  
								# MOVEMOS O ARQUIVO PARA O SERVIDOR
								##########################################################################################################  
							 	if(move_uploaded_file($tmp_name ,UPLOAD_DIR.'/'.$token.'.'.$ext)){

									##########################################################################################################  
							 		# GUARDAMOS AS VARIÁVEIS DO ARQUIVO UPADO NA ARRAY
									##########################################################################################################  
									$_RETURN_FILES[] = array(
										'status'=>'sucesso',
										'response'=>'Upload efetuado com sucesso!',
										'error'=>0,
										'file'=>array(
											'size'		=>$size,
											'type'		=>$type,
											'name'		=>$nome,
											'newName'	=>$token.'.'.$ext,
											'ext'		=>$ext,
											'token'		=>$token
										)
									);
								 }else{
									$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'move_uploaded_file','linha'=>__LINE__));
								}					
								
							}else{
								$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado','arquivo'=>$tmp_name, 'error'=>'is_uploaded_file','linha'=>__LINE__));
							}
						}
					}else{
					##########################################################################################################  
					# CASO SEJA UM ÚNICO UIPLOAD  
					##########################################################################################################

			        	$tmp_name 	= $__FILE__["tmp_name"];
			        	$size 		= $__FILE__["size"];
			        	$type		= $__FILE__["type"];
						$nome 		= url_amigavel_filename($__FILE__["name"]);
						$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
						$ext		= str_replace(array("jpeg"),array("jpg"),$ext);

						##########################################################################################################  
						# VERIFICA   
						##########################################################################################################

						if(in_array($ext, $_EXTENSION_DENIED)) {
							echo json_encode(array('status'=>'falha','response'=>'Formato ilegal:"'.$ext.'"', 'error'=>'move_uploaded_file','linha'=>__LINE__)); 
							exit;
						}

						$token 		= md5(uniqid(rand(), true));

						##########################################################################################################  
						# "is_uploaded_file" Retorna TRUE se o arquivo com o nome filename foi enviado por POST HTTP  
						# Isto é útil para ter certeza que um usuário malicioso não está tentando levar o script a trabalhar 
						# em arquivos que não deve estar trabalhando --- por exemplo, /etc/passwd.
						##########################################################################################################
						if(is_uploaded_file($tmp_name)){
							if(move_uploaded_file( $tmp_name ,UPLOAD_DIR.'/'.$token.'.'.$ext)){
								##########################################################################################################  
						 		# GUARDAMOS AS VARIÁVEIS DO ARQUIVO UPADO NA ARRAY
								##########################################################################################################
								$_RETURN_FILES[] = array(
									'status'=>'sucesso',
									'response'=>'Upload efetuado com sucesso!',
									'error'=>0,
									'file'=>array(
										'size'		=>$size,
										'type'		=>$type,
										'name'		=>$nome,
										'newName'	=>$token.'.'.$ext,
										'ext'		=>$ext,
										'token'		=>$token
									)
								);
							}else{
								$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'move_uploaded_file','linha'=>__LINE__));
							}
						}else{
							$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'is_uploaded_file','linha'=>__LINE__));
						}
					}
				}
			}else{
				$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Não existem arquivos anexados', 'error'=>$errorUpload[8],'linha'=>__LINE__));
			}
		}else{
			$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Diretório inexistente ou não permite esta ação', 'error'=>'is not writable (is_writable)','linha'=>__LINE__));
		}

/*##########################################################################################################  
*
*
*
*
*
*	DEPOIS DE EFETUADO O UPLOAD DOS ARQUIVOS, VAMOS TRATAR OS DADOS DA BASE
*
*
*
*
*
##########################################################################################################*/

##########################################################################################################
#  FUNÇÃO QUE ADICIONA O ARQUIVO A BIBLIOTECA
##########################################################################################################			
	function AddBiblioteca($FILE,$POST){
		if(empty($POST['token']) 		|| $POST['token']=="")			$POST['token'] 			= _token(PREFIX_TABLES . 'ws_biblioteca', 'token');
		if(empty($POST['token_group']) 	|| $POST['token_group']=="")	$POST['token_group'] 	= _token(PREFIX_TABLES . 'ws_biblioteca', 'token_group');
		if(empty($POST['tokenFile']) 	|| $POST['tokenFile']=="")		$POST['tokenFile'] 		= _token(PREFIX_TABLES . 'ws_biblioteca', 'tokenFile');

		$_biblioteca_ = new MySQL();
		$_biblioteca_->set_table(PREFIX_TABLES.'ws_biblioteca');
		$_biblioteca_->set_insert('filename',			$FILE['file']['name']);
		$_biblioteca_->set_insert('file',				$FILE['file']['newName']);
		$_biblioteca_->set_insert('token',				$FILE['file']['token']);
		$_biblioteca_->set_insert('type',				$FILE['file']['type']);
		$_biblioteca_->set_insert('upload_size',		$FILE['file']['size']);
		$_biblioteca_->set_insert('token_group',		$POST['token_group']);
		$_biblioteca_->set_insert('tokenFile',			$POST['tokenFile']);
		if(isset($POST['download']) && $POST['download']==1){
			$_biblioteca_->set_insert('download','1'); 
		}
		$_biblioteca_->insert();
	}
##########################################################################################################
#  VARREMOS OS ARQUIVOS ANEXADOS
##########################################################################################################
foreach($_RETURN_FILES AS $FILE){
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	$getSlug = new MySQL();
	$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
	$getSlug->set_where('id="' . $_POST['ws_id_ferramenta'] . '"');
	$getSlug->select();
	$_SLUG_ = $getSlug->fetch_array[0]['slug'];


	#################################################################################################
	# CADA MÓDULO POSSÚI UM CAMPO HIDDEN COM O NAME="TYPE" QUE VAI NOS DIZER QUE MÓDULO ELE PERTENCE
	#  VERIFICAMOS QUAL É O CAMPO E MANIPULAMOS A BASE DE DADOS CONFORME O ITEM INDICADO
	#################################################################################################

    if($_POST['type']=='item_detail_file'){		

		AddBiblioteca($FILE,$_POST);

		#------------------------------------------------------------------------------	
		$Old				= new MySQL();
		$Old->set_table(PREFIX_TABLES.$_SLUG_.'_files');
		$Old->set_where('token="'.$_POST['token'].'" ');
		$Old->set_where('AND painel="1" ');
		$Old->set_where('AND ws_draft="1"');
		$Old->set_where('AND id_item="'.$_POST['id_item'].'" ');
		$Old->set_where('AND ws_id_draft="'.$_POST['id_item'].'"');
		$Old->select();

		#------------------------------------------------------------------------------				
		if($Old->_num_rows==0){
			$up					= new MySQL();
			$up->set_table(PREFIX_TABLES.$_SLUG_.'_files');
			$up->set_insert('ws_id_ferramenta' 	,$_POST['ws_id_ferramenta']);
			$up->set_insert('painel'			,'1');
			$up->set_insert('ws_draft'			,'1');
			$up->set_insert('ws_id_draft'		,$_POST['id_item']);
			$up->set_insert('id_item'			,$_POST['id_item']);
			$up->set_insert('token'				,$_POST['token']);
			$up->set_insert('size_file'			,$FILE['file']['size']);
			$up->set_insert('file'				,$FILE['file']['newName']);
			$up->set_insert('filename'			,$FILE['file']['name']);
			if($_POST['download']==1){$up->set_insert('download',1);}
			$up->insert();
		}else{
			$U= new MySQL();
			$U->set_table(PREFIX_TABLES.$_SLUG_.'_files');
			$U->set_where('token			="'.$_POST['token'].'"'); 
			$U->set_where('AND ws_draft 	="1"');
			$U->set_where('AND painel		="1"');
			$U->set_where('AND ws_id_draft	="'.$_POST['id_item'].'"');
			$U->set_where('AND id_item		="'.$_POST['id_item'].'"');
			$U->set_update('file'			,$FILE['file']['newName']);
			$U->set_update('filename'		,$FILE['file']['name']);
			$U->set_update('size_file'		,$FILE['file']['size']);
			if($_POST['download']==1){$U->set_update('download',1); }else{$U->set_update('download',0); }
			$U->salvar();
		}

		##########################################################################################################
		#  VERIFICAMOS SE O ÍTEM TEM UM RASCUINHO
		##########################################################################################################				
		$verify_produto= new MySQL();
		$verify_produto->set_table(PREFIX_TABLES.$_SLUG_.'_item');
		$verify_produto->set_where('ws_draft="1"');
		$verify_produto->set_where('AND ws_id_draft="'.$_POST['id_item'].'"');
		$verify_produto->select();

		##########################################################################################################
		#  CASO NÃO TENHA, CRIA UM 
		##########################################################################################################
		if($verify_produto->_num_rows < 1) { criaRascunho($_POST['ws_id_ferramenta'],$_POST['id_item']);}

		##########################################################################################################
		#  SALVA NA BASE
		##########################################################################################################
		$U= new MySQL();
		$U->set_table(PREFIX_TABLES.$_SLUG_.'_item');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="'.$_POST['id_item'].'"');
		$U->set_update($_POST['mysql'],$FILE['file']['newName']);
		$U->salvar();
		echo json_encode(array('status'=>'sucesso','original'=>$FILE['file']['name'], 'filename'=>$FILE['file']['newName']));
		exit;

	}elseif($_POST['type']=='uploadBKP'){	

		$filename = INCLUDE_PATH.'ws-bkp/bkp_(uploaded)_'.date("Y-m-d_H-i-s").'.zip';
		rename(UPLOAD_DIR.'/'.$FILE['file']['newName'], $filename);
		echo json_encode(array('status'=>'sucesso'));
		exit;


		
	}elseif($_POST['type']=='item_detail_thumbnail'){	


		AddBiblioteca($FILE,$_POST);
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);

		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . $_SLUG_.'_item');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$U->set_update($_POST['mysql'], 	$FILE['file']['newName']);
		$U->salvar();

		$U = new MySQL();
		$U->set_table(PREFIX_TABLES .$_SLUG_.'_img');
		$U->set_where('token="' 	.$_POST['token'] . '"');
		$U->set_update('filename',	$FILE['file']['newName']);
		$U->set_update('painel', '1');
		$U->salvar();
		##################################################################################################
		echo json_encode(array(
				'nome' => $FILE['file']['newName'],
				'thumb' => ws::rootPath.'ws-img/' . $_POST['newwidth'] . '/' . $_POST['newheight'] . '/100/' . $FILE['file']['newName']
		));
	}elseif($_POST['type']=='avatar_galeria'){		

		#######################################################################################
		# ADD A IMAGEM NA BIBLIOTECA
		#######################################################################################
			AddBiblioteca($FILE,$_POST);

		#######################################################################################
		# CASO JÁ TENHA UM RASCUNHO DO AVATAR EXCLUI
		#######################################################################################
			$Old				= new MySQL();
			$Old->set_table(PREFIX_TABLES.$_SLUG_.'_img_gal');
			$Old->set_where('id_galeria="'.$_POST['id_gal'].'"');
			$Old->set_where('AND ws_draft="1"');
			$Old->set_where('AND avatar="1"');
			$Old->exclui();

		#######################################################################################
		# E ADICIONAMOS UM NOVO REGISTRO
		#######################################################################################
			$NEW_AVATAR					= new MySQL();
			$NEW_AVATAR->set_table(PREFIX_TABLES.$_SLUG_.'_img_gal');
			$NEW_AVATAR->set_insert('ws_id_ferramenta' ,	$_POST['ws_id_ferramenta']);
			$NEW_AVATAR->set_insert('id_galeria',			$_POST['id_gal']);
			$NEW_AVATAR->set_insert('ws_tool_item',			$_POST['id_item']);
			$NEW_AVATAR->set_insert('imagem',				$FILE['file']['newName']);
			$NEW_AVATAR->set_insert('filename',				$FILE['file']['name']);
			$NEW_AVATAR->set_insert('token',				$FILE['file']['token']);
			$NEW_AVATAR->set_insert('avatar','1');	
			$NEW_AVATAR->set_insert('ws_draft','1');	
			$NEW_AVATAR->insert();

		#######################################################################################
		# CAPTAMOS O AVATAR ATUAL
		#######################################################################################
			$AVATAR				= new MySQL();
			$AVATAR->set_table(PREFIX_TABLES.$_SLUG_.'_img_gal');
			$AVATAR->set_where('id_galeria="'.$_POST['id_gal'].'"');
			$AVATAR->set_where('AND ws_draft="0"');
			$AVATAR->set_where('AND avatar="1"');
			$AVATAR->select();

		#######################################################################################
		# AGORA VALIDAMOS COMO UM RASCUNHO DE AVATAR OU DELE MESMO
		#######################################################################################
			$AVATAR = ($AVATAR->_num_rows>0) ? $AVATAR->fetch_array[0]['id'] : 'command:id';

			$UPDATE_AVATAR				= new MySQL();
			$UPDATE_AVATAR->set_table(PREFIX_TABLES.$_SLUG_.'_img_gal');
			$UPDATE_AVATAR->set_where('id_galeria="'.$_POST['id_gal'].'"');
			$UPDATE_AVATAR->set_where('AND ws_draft="1"');
			$UPDATE_AVATAR->set_where('AND avatar="1"');
			$UPDATE_AVATAR->set_update('ws_id_draft',$AVATAR);
			$UPDATE_AVATAR->salvar();

		#######################################################################################
		# ATUALIZA O REGISTRO DO AVATAR NA GALERIA
		#######################################################################################
			$_update_gal_					= new MySQL();
			$_update_gal_->set_table(PREFIX_TABLES.$_SLUG_.'_gal');
			$_update_gal_->set_where('ws_draft="1" AND ws_id_draft="'.$_POST['id_gal'].'"');
			$_update_gal_->set_update('avatar',$FILE['file']['newName']);
			$_update_gal_->salvar();

		#######################################################################################
		# RETORNA THUMBNAIL
		#######################################################################################
			echo ws::rootPath.'ws-img/155/128/100/'.$FILE['file']['newName'];
			exit;
	}elseif($_POST['type']=='img_galeria'){				

		#################################################################################################
		# ADICIONA A BIBLIOTECA
		#################################################################################################
		AddBiblioteca($FILE,$_POST);
		$up					= new MySQL();
		$up->set_table(PREFIX_TABLES.$_SLUG_.'_img_gal');
		$up->set_insert('ws_draft'				,'1');
		$up->set_insert('titulo'				,'');
		$up->set_insert('texto'					,'');
		$up->set_insert('file'					,$FILE['file']['newName']);
		$up->set_insert('filename'				,$FILE['file']['name']);
		$up->set_insert('token'					,$FILE['file']['token']);
		$up->set_insert('ws_id_ferramenta' 		,$_POST['ws_id_ferramenta']);
		$up->set_insert('ws_tool_item'			,$_POST['id_item']);
		$up->set_insert('id_galeria'			,$_POST['id_galeria']);
		$up->set_insert('id_item'				,$_POST['id_item']);
		$up->insert();


		#################################################################################################
		# COMO É APENAS RASCUINHO, ELE SERÁ CÓPIA DELE MESMO
		#################################################################################################
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . $_SLUG_.'_img_gal');
		$Salva->set_where('ws_draft=1');
		$Salva->set_update('ws_id_draft','command:id');
		$Salva->salvar();

		#################################################################################################
		# SELECIONAMOS O ARQUIVO PARA PEGAR OS ID's
		#################################################################################################
		$get_ID					= new MySQL();
		$get_ID->set_table(PREFIX_TABLES.$_SLUG_.'_img_gal');
		$get_ID->set_where('file="'.$FILE['file']['newName'].'"');
		$get_ID->select();

		#################################################################################################
		# RETORNA A STRING DA GALERIA
		#################################################################################################
		echo "<li id='".$get_ID->fetch_array[0]['id']."'>	
				<div id='combo'>
					<div id='detalhes_img' class='bg02'>
					<spam ><img class='editar' 	legenda='Editar Informações'	src='./app/templates/img/websheep/layer--pencil.png'></spam>   
					<spam ><img class='excluir'	legenda='Excluir Imagem'		src='./app/templates/img/websheep/cross-button.png'></spam>   
				</div>
					<img class='thumb_exibicao' src='".ws::rootPath."ws-img/155/155/100/".$FILE['file']['newName']."'>
				</div>
			</li>";
	}elseif($_POST['type']=='ckEditor'){				

		#######################################################################################
		# ADD A IMAGEM NA BIBLIOTECA
		#######################################################################################
			AddBiblioteca($FILE,$_POST);
			echo ws::rootPath.'ws-img/0/0/100/'.$FILE['file']['newName'];
			exit;
	}elseif($_POST['type']=='leadCapture'){				


		#######################################################################################
		# ADD A IMAGEM NA BIBLIOTECA
		#######################################################################################
			AddBiblioteca($FILE,$_POST);
			
			$U = new MySQL();
			$U->set_table(PREFIX_TABLES . 'ws_list_leads');
			$U->set_where('token="' . $_POST['token'] . '"');
			if ($_POST['datatype'] == 'topo_email') {
					$U->set_update('header_email', $FILE['file']['newName']);
			} elseif ($_POST['datatype'] == 'footer_email') {
					$U->set_update('footer_email', $FILE['file']['newName']);
			}
			$U->salvar();
			echo json_encode(array(
					'nome' => $FILE['file']['newName'],
					'thumb' => ws::rootPath.'ws-img/'.$_POST['width'].'/'.$_POST['height'].'/100/'.$FILE['file']['newName']
			));			
	}elseif($_POST['type']=='avatar_categoria'){	


		#######################################################################################
		# ADD A IMAGEM NA BIBLIOTECA
		#######################################################################################
			AddBiblioteca($FILE,$_POST);
			

				$Old				= new MySQL();
				$Old->set_table(PREFIX_TABLES.$_SLUG_.'_img');
				$Old->set_where('id_cat="'.$_POST['id'].'"');
				$Old->set_where('AND avatar="1"');
				$Old->exclui();

				$up					= new MySQL();
				$up->set_table(PREFIX_TABLES.$_SLUG_.'_img');
				$up->set_insert('ws_id_ferramenta' ,$_POST['ws_id_ferramenta']);
				$up->set_insert('avatar',			'1');
				$up->set_insert('ws_tool_item'		,$_POST['id_item']);
				$up->set_insert('imagem'			,$FILE['file']['newName']);
				$up->set_insert('id_cat'			,$_POST['id']);
				$up->set_insert('filename'			,$FILE['file']['name']);
				$up->set_insert('token'				,$FILE['file']['token']);
				$up->insert();

				$Set_img = new MySQL();
				$Set_img->select('UPDATE `'.PREFIX_TABLES.$_SLUG_.'_img` SET  `ws_id_draft`=`id` WHERE(ws_draft=1 AND token="'.$FILE['file']['token'].'")');

				$up					= new MySQL();
				$up->set_table(PREFIX_TABLES.$_SLUG_.'_cat');
				$up->set_where('id="'.$_POST['id'].'"');
				$up->set_update('avatar',$FILE['file']['newName']);
				$up->salvar();

				echo ws::rootPath.'ws-img/281/0/100/'.$FILE['file']['newName'];

	}elseif($_POST['type']=='lista_arquivos_item'){		
		#######################################################################################
		# ADD A IMAGEM NA BIBLIOTECA
		#######################################################################################
			AddBiblioteca($FILE,$_POST);
			
			$up					= new MySQL();
			$up->set_table(PREFIX_TABLES.$_SLUG_.'_files');
			$up->set_insert('ws_id_ferramenta' ,$_POST['ws_id_ferramenta']);
			$up->set_insert('id_item',$_POST['id_item']);
			$up->set_insert('file',$FILE['file']['newName']);
			$up->set_insert('filename',$FILE['file']['name']);
			$up->set_insert('ws_draft',"1");
			$up->set_insert('ws_id_draft',$_POST['id_item']);			
			$up->set_insert('token',$token);
			$up->set_insert('size_file',$FILE['file']['size']);
			$up->insert();
	}elseif($_POST['type']=='img_inner_item'){		
			criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'],true);	
			#######################################################################################
			# ADD A IMAGEM NA BIBLIOTECA
			#######################################################################################
			AddBiblioteca($FILE,$_POST);
			
			$_insert_tab_img_					= new MySQL();
			$_insert_tab_img_->set_table(PREFIX_TABLES.$_SLUG_.'_img');
			$_insert_tab_img_->set_insert('ws_id_ferramenta' ,	$_POST['ws_id_ferramenta']);
			$_insert_tab_img_->set_insert('id_item',			$_POST['id_item']);
			$_insert_tab_img_->set_insert('ws_tool_item',		$_POST['id_item']);
			$_insert_tab_img_->set_insert('imagem',				$FILE['file']['newName']);
			$_insert_tab_img_->set_insert('filename',			$FILE['file']['name']);
			$_insert_tab_img_->set_insert('token',				$FILE['file']['token']);
			$_insert_tab_img_->set_insert('ws_draft','1');
			$_insert_tab_img_->set_insert('ws_id_draft',$_POST['id_item']);
			$_insert_tab_img_->insert();
	


			
			$Set_img = new MySQL();
			$Set_img->select('UPDATE `'.PREFIX_TABLES.$_SLUG_.'_img` SET  `ws_id_draft`=`id` WHERE(ws_draft=1 AND ws_tool_item="'.$_POST['id_item'].'")');

			$file					= new MySQL();
			$file->set_table(PREFIX_TABLES.$_SLUG_.'_img');
			$file->set_where('imagem="'.$FILE['file']['newName'].'"');
			$file->select();
			echo "<li id='".$file->fetch_array[0]['id']."'>	
					<div id='combo'>
						<div id='detalhes_img' class='bg02'><spam class='editar' legenda='Editar Informações'>✎</spam>   <spam class='excluir' legenda='Excluir Imagem'>✖</spam></div>
						<img src='".ws::rootPath."ws-img/155/155/".$FILE['file']['newName']."'>
					</div>
				</li>";
	}elseif($_POST['type']=='splashScreen'){			
		#######################################################################################
		# ADD A IMAGEM NA BIBLIOTECA
		#######################################################################################
		AddBiblioteca($FILE,$_POST);
			
		$U				= new MySQL();
		$U->set_table(PREFIX_TABLES.'setupdata');
		$U->set_where('id="1"');
		$U->set_update('splash_img',$FILE['file']['newName']);
		$U->salvar();
		echo json_encode(array(
            'nome' =>  $FILE['file']['name'],
            'thumb' => $FILE['file']['newName']
        ));
	}elseif($_POST['type']==''){	
	}elseif($_POST['type']==''){		
	}elseif($_POST['type']==''){		
	}elseif($_POST['type']==''){}
}