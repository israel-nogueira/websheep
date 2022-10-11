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


	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'app/ws-modules/ws-hd-management');
		
	#####################################################  
	# LIMPA O CACHE INTERNO
	#####################################################
	clearstatcache();
	
	#####################################################  
	# CONTROLA O CACHE
	#####################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	#####################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	#####################################################
	ob_start();
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-tool-hd.html');
	
	#####################################################  
	# SEPARAMOS A VARIÁVEL DO SETUP DATA 
	#####################################################
	$setupdata = new MySQL();
	$setupdata->set_table(PREFIX_TABLES . 'setupdata');
	$setupdata->set_order('id', 'DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	
	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           					= new Template(TEMPLATE_LINK, true);
	$template->hdPanel_title 				= ws::getLang("hdPanel>title");
	$template->hdPanel_consumptionPerTool 	= ws::getLang("hdPanel>consumptionPerTool");
	$template->hdPanel_SituationOfTheHD 	= ws::getLang("hdPanel>SituationOfTheHD");
	$template->hdPanel_overviewOfAllFiles 	= ws::getLang("hdPanel>overviewOfAllFiles");
	$template->hdPanel_Library 				= ws::getLang("hdPanel>Library");
	$template->hdPanel_consuming 			= ws::getLang("hdPanel>consuming");
	$template->hdPanel_SpaceOccupied 		= ws::getLang("hdPanel>SpaceOccupied"); 
	$template->hdPanel_table_originalName 	= ws::getLang("hdPanel>table>originalName");
	$template->hdPanel_table_fileName 		= ws::getLang("hdPanel>table>fileName");
	$template->hdPanel_table_size 			= ws::getLang("hdPanel>table>size");
	$template->hdPanel_table_changed 		= ws::getLang("hdPanel>table>changed");
	$template->hdPanel_table_extension 		= ws::getLang("hdPanel>table>extension");
	$template->hdPanel_table_path 			= ws::getLang("hdPanel>table>path");


	####################################################################################  
	# VARREMOS TODOS OS ARQUIVOS DO DIRETÓRIO WEBSITE E GUARDAMOS OS VALORES NA VARIÁVEL
	####################################################################################
	$_pesoTotalFilesSite = 0;
	$_ArquivosFTP_		 = array();
	function FTP_FILES_SIZE($dir,$oq =""){
		global $_pesoTotalFilesSite;
		global $_ArquivosFTP_;
		if (is_dir($dir)) {
			$dh = opendir($dir);
			while($diretorio = readdir($dh)){
				if($diretorio != '..' && $diretorio != '.' && is_dir($dir.'/'.$diretorio)){
						FTP_FILES_SIZE($dir.'/'.$diretorio."/");
				}elseif($diretorio != '..' && $diretorio != '.' && !is_dir($dir.'/'.$diretorio)){
						$peso = @filesize($dir.'/'.$diretorio);
						$_ArquivosFTP_[]= array('<b>Arquivo:</b>'.str_replace(INCLUDE_PATH.'website/',"",$dir.'/'.$diretorio), $peso);
						$_pesoTotalFilesSite +=  $peso;
					};
			};
		};
	};
	FTP_FILES_SIZE(INCLUDE_PATH.'website/');

	#####################################################  
	# DAMOS UM SELECT NA TABALEA DA BIBLIOTECA  
	#####################################################
	$BD_BIBLIOTECA= new MySQL(); 
	$BD_BIBLIOTECA->set_table(PREFIX_TABLES.'ws_biblioteca'); 
	$BD_BIBLIOTECA->set_colum('id');
	$BD_BIBLIOTECA->set_colum('filename');
	$BD_BIBLIOTECA->set_colum('file');
	$BD_BIBLIOTECA->set_colum('type');
	$BD_BIBLIOTECA->set_colum('upload_size');
	$BD_BIBLIOTECA->set_colum('DATE_FORMAT(saved, "%d/%m/%Y %H:%i")	AS uploaded');
	$BD_BIBLIOTECA->select();
	$biblioteca = array();

	##############################################################  
	#  VARREMOS  A BIBLIOTECA E GUARDAMOS OS ARQUIVOS EM UMA ARRAY 
	##############################################################
	foreach ($BD_BIBLIOTECA->fetch_array as $img) {
		$ext = explode(".",$img['filename']);
		$ext = explode("@",end($ext));
		$ext = $ext[0];
		$biblioteca[] = array('filename'=>$img['filename'],'file'=>$img['file'],'upload_size'=>_filesize($img['upload_size']),'uploaded'=>$img['uploaded'],'ext'=>$ext);
	}

	############################################################################## 
	#  VARREMOS NOVAMENTE O DIRETÓRIO WEBSITE E GUARDAMOS OS ARQUIVOS EM UMA ARRAY 
	##############################################################################
	$FilesFTP = array();
	function FullFilesFTP($dir,$oq =""){
		global $FilesFTP;
		if (is_dir($dir)) {
			$dh = opendir($dir);
			while($diretorio = readdir($dh)){
				if($diretorio != '..' && $diretorio != '.' && is_dir($dir.'/'.$diretorio)){
					FullFilesFTP($dir.'/'.$diretorio);
				}elseif($diretorio != '..' && $diretorio != '.' && !is_dir($dir.'/'.$diretorio)){
		 			$peso = @filesize($dir.'/'.$diretorio);
		 			$ext = explode(".",basename($diretorio));
					$ext = explode("@",end($ext));
					$ext = $ext[0];
		 			$FilesFTP[]= array(
		 				'file'=>		str_replace(INCLUDE_PATH.'website/',"",$dir.'/'.$diretorio),
		 				'filename'=>	str_replace(INCLUDE_PATH.'website/',"",$dir.'/'.$diretorio), 
		 				'upload_size'=>	_filesize($peso),
		 				'uploaded'=>	@date("d/m/Y H:i",@filemtime($dir.'/'.$diretorio)), 
						'ext'=>			$ext
		 			);
				}
			}
		}
	}
	FullFilesFTP(INCLUDE_PATH.'website/');

	#######################################################################  
	#  VARREMOS A ARRAY DO FTP E RETORNAMOS O TEMPLATE 
	#######################################################################
	foreach ($FilesFTP as $file) {
			$template->li_filename		=	$file['filename'];
			$template->li_file			=	$file['file'];
			$template->li_upload_size	=	$file['upload_size'];
			$template->li_uploaded 		=	$file['uploaded'];
			$template->li_ext 			=	$file['ext'];
			$template->block('TABLE');
	}

	#######################################################################  
	#  VARREMOS NOVAMENTE A BIBLIOTECA E SOMAMOS OS VALORES 
	#######################################################################
		$peso_files = 0;
		foreach ($BD_BIBLIOTECA->fetch_array as $file){
			$pesoAtualFile = @filesize('./../ws-model-tool/uploads/'.$file['file']);
			$peso_files = $peso_files+($pesoAtualFile);
		};

		$peso_files = $peso_files+$_pesoTotalFilesSite;
		$total      = ($setupdata['hd']*1024)*1024;
		$total      = (($total/1024)/1024);
		$filesSize  = round((($peso_files/1024)/1024),2);
		$parcial    = $total/4;

	#######################################################################  
	#  SETAMOS OS VALORES E RETORNAMOS O TEMPLATE 
	#######################################################################
		$template->TOTAL 		= $total;
		$template->PARCIAL 		= $parcial;
		$template->PARCIALx2 	= $parcial*2;
		$template->PARCIALx3 	= $parcial*3;
		$template->PARCIALx3_2 	= (($parcial)*3)+(($parcial)/2);
		$template->PARCIALx4 	= $parcial*4;
		$template->FILE_SIZE 	= $filesSize;

		$template->block('HD');
		$template->show();

