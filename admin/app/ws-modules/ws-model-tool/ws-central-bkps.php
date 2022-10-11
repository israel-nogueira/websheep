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
	# IMPORTA CLASSE PADRÃO DO SISTEMA
	##########################################################################################################
	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	_session();
	clearstatcache();

	##########################################################################################################
	# DEFINE O PATH DO MÓDULO
	##########################################################################################################
	define("PATH",'app/ws-modules/ws-model-tool');

	#####################################################  
	# PEGA O SETUP DATA
	#####################################################  
	$SETUP = new MySQL();
	$SETUP->set_table(PREFIX_TABLES . 'setupdata');
	$SETUP->set_where('id="1"');
	$SETUP->debug(0);
	$SETUP->select();
	$SETUP      = $SETUP->fetch_array[0];

	##########################################################################################################
	# IMPORTA A CLASSE DE TEMPLATE
	##########################################################################################################	
	$template 						=	new Template(INCLUDE_PATH.'admin/app/templates/html/ws-central-bkp.html', true);

	##########################################################################################################
	# DEFINIMOS AS STRINGS PRINCIPAIS PUXANDO DO JSON
	##########################################################################################################
	$template->PATH												=	PATH;
	$template->ROOT_WEBSHEEP									=	ROOT_WEBSHEEP;
	$template->ws_rootPath										=	ws::rootPath;
	$template->centralBkp_download								=	ws::getlang('centralBkp>download');
	$template->centralBkp_title									=	ws::getlang('centralBkp>title');
	$template->centralBkp_exportBackup							= 	ws::getlang('centralBkp>exportBackup');;
	$template->centralBkp_importBackup							= 	ws::getlang('centralBkp>importBackup');;
	$template->centralBkp_uploadBKP								=	ws::getlang('centralBkp>uploadBKP');
	$template->centralBkp_createBackup							=	ws::getlang('centralBkp>createBackup');
	$template->centralBkp_loading								=	ws::getlang('centralBkp>loading');
 	$template->centralBkp_mycomputer							=	ws::getlang('centralBkp>mycomputer');
	$template->centralBkp_library								=	ws::getlang('centralBkp>library');
	$template->centralBkp_delete								=	ws::getlang('centralBkp>delete');
	$template->centralBkp_creating								=	ws::getlang('centralBkp>creating');
	$template->centralBkp_export								=	ws::getlang('centralBkp>export');
	$template->centralBkp_cancel								=	ws::getlang('centralBkp>cancel');
	$template->centralBkp_error									=	ws::getlang('centralBkp>error');
	$template->centralBkp_restoration							=	ws::getlang('centralBkp>restoration');
	$template->centralBkp_update								=	ws::getlang('centralBkp>update');
	$template->centralBkp_refresh								=	ws::getlang('centralBkp>refresh');
	$template->centralBkp_recharge								=	ws::getlang('centralBkp>recharge');		
	$template->centralBkp_restore								=	ws::getlang('centralBkp>restore');	
	$template->centralBkp_placeholderTitle						=	ws::getlang('centralBkp>placeholderTitle');				
	$template->centralBkp_descrition							=	ws::getlang('centralBkp>descrition');				
	$template->centralBkp_creatingatemplate						=	ws::getlang('centralBkp>creatingatemplate');
	$template->centralBkp_importing								=	ws::getlang('centralBkp>importing');
	$template->centralBkp_invalidLink							=	ws::getlang('centralBkp>invalidLink');
	$template->centralBkp_restoreModal_aboutTheFiles			=	ws::getlang('centralBkp>restoreModal>aboutTheFiles');
	$template->centralBkp_restoreModal_restore100				=	ws::getlang('centralBkp>restoreModal>restore100');
	$template->centralBkp_restoreModal_applyReplace				=	ws::getlang('centralBkp>restoreModal>applyReplace');
	$template->centralBkp_restoreModal_applyIgnore				=	ws::getlang('centralBkp>restoreModal>applyIgnore');
	$template->centralBkp_restoreModal_tools					=	ws::getlang('centralBkp>restoreModal>tools');
	$template->centralBkp_restoreModal_dataBase					=	ws::getlang('centralBkp>restoreModal>dataBase');
	$template->centralBkp_restoreModal_dontTouchDataBase		=	ws::getlang('centralBkp>restoreModal>dontTouchDataBase');
	$template->centralBkp_restoreModal_basicStructure			=	ws::getlang('centralBkp>restoreModal>basicStructure');
	$template->centralBkp_restoreModal_rememberIf				=	ws::getlang('centralBkp>restoreModal>rememberIf');
	$template->centralBkp_importModal_title						=	ws::getlang('centralBkp>importModal>title');
	$template->centralBkp_importModal_bots_cancel				=	ws::getlang('centralBkp>importModal>bots>cancel');
	$template->centralBkp_importModal_bots_import				=	ws::getlang('centralBkp>importModal>bots>import');
	$template->centralBkp_exportModal_title						=	ws::getlang('centralBkp>exportModal>title');
	$template->centralBkp_exportModal_sucessCopy				=	ws::getlang('centralBkp>exportModal>sucessCopy');
	$template->centralBkp_exportModal_bots_createCode			=	ws::getlang('centralBkp>exportModal>bots>createCode');
	$template->centralBkp_exportModal_bots_cancel				=	ws::getlang('centralBkp>exportModal>bots>cancel');
	$template->centralBkp_exportModal_bots_copyCode				=	ws::getlang('centralBkp>exportModal>bots>copyCode');

	##########################################################################################################
	# CASO NÃO TENHA O DIRETÓRIO, CRIA A PASTA PADRÃO DE BKP
	##########################################################################################################
	if(!file_exists(INCLUDE_PATH.'ws-bkp')){mkdir(INCLUDE_PATH.'ws-bkp');}

	##########################################################################################################
	# FAZ O WHILE NAS PASTAS LISTADAS E RERTORNA O TEMPLATE COM O NOME E LINK
	##########################################################################################################
	$dh = opendir(INCLUDE_PATH.'ws-bkp');
	while($diretorio = readdir($dh)){
		if($diretorio != '..' && $diretorio != '.' && substr($diretorio,-3)=="zip"){		
			$newSplashScreen 			= "";
			$template->dataFILE			= $diretorio;
			$newSplashScreen 			= "";
			$template->titulo			= $diretorio;
			$template->label			= ws::getlang('centralBkp>restore');
			##########################################################################################################
			# ABRIMOS O ZIP PARA VERIFICAR O CONTEÚDO
			##########################################################################################################
				$fileZip = INCLUDE_PATH.'ws-bkp/'.$diretorio;
				$zip = new ZipArchive();
				if($zip->open($fileZip) === TRUE ){

					##########################################################################################################
					# VERIFICA SE EXISTE O ws-info.json
					##########################################################################################################
					if(strlen($zip->getFromName('ws-info.json'))){
							######################################################################################
					  		$contents 				= json_decode(trim($zip->getFromName('ws-info.json')),true);
					  		$thumb 					= base64_encode(@$zip->getFromName(@$contents['thumb']));
					  		$template->titulo 		= $contents['title'];
					    	$template->description 	= $contents['content'];
							########################################################### pega a miniatura do zip
							
						    if($contents['thumb']!='ws-img/200/200/null'){
								$template->ClasseThumb		= 'minThumb';
								$template->newSplashScreen	= 'data:image/jpeg;base64,'.$thumb;
								$template->block("avatarTemplate");
						    }else{
								$template->ClasseThumb		= 'minThumb';
								$template->newSplashScreen	= $contents['thumb'];
								$template->block("avatarTemplate");
								$template->clear("newSplashScreen");
							}

				    }else{
						$template->clear("ClasseThumb");
						$template->clear("description");
				    }
					$zip->close();
				}else{
					$template->clear("ClasseThumb");
					$template->clear("description");
					$template->clear("avatarTemplate");
					$template->clear("newSplashScreen");
				}
			########################################################### GERA A ASH DE TRANSFERENCIA
			$template->block("BLOCK_TEMPLATES");
		}
	}

	##########################################################################################################
	# RETORNA O HTML DO TEMPLATE NA TELA
	##########################################################################################################
	$template->block("BLOCK_CENTRAL_BKP");
	$template->show();









