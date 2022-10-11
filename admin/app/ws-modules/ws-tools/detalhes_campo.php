<?php
	##########################################################################  
	# FORMATA O CAMINHO ROOT
	##########################################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;
	
	##########################################################################  
	# DEFINE O PATH DO MÓDULO 
	##########################################################################
	define("PATH", 'app/ws-modules/ws-hd-management');
	
	##########################################################################  
	# LIMPA O CACHE INTERNO
	##########################################################################
	clearstatcache();
	
	##########################################################################  
	# CONTROLA O CACHE
	##########################################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
		if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}
	
	##########################################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	##########################################################################
	ob_start();
	include(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	##########################################################################  
	# CRIA SESSÃO
	##########################################################################  
	// _session();
	$session = new session();
	
	##########################################################################  
	# 
	##########################################################################
	define("ws_id_ferramenta", $_POST['ws_id_ferramenta']);
	$session->set('ws_id_ferramenta', ws_id_ferramenta);

	##################################################################################################
	# CAPTAMOS O SLUG DA FERRAMENTA	
	##################################################################################################
	$getSlug = new MySQL();
	$getSlug->set_table(PREFIX_TABLES . "ws_ferramentas");
	$getSlug->set_where('id="' . ws_id_ferramenta . '"');
	$getSlug->select();
	$slug = $getSlug->fetch_array[0]['slug'];


	##########################################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	##########################################################################
	verifyUserLogin();
	
	##########################################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	##########################################################################  
	define("TEMPLATE_LINK", INCLUDE_PATH.'admin/app/templates/html/ws-modules/ws-tools/ws-tool-details-campos-template.html');
	
	##########################################################################  
	# SEPARAMOS A VARIÁVEL DO SETUP DATA 
	##########################################################################
	$setupdata = new MySQL();
	$setupdata->set_table(PREFIX_TABLES . 'setupdata');
	$setupdata->set_order('id', 'DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = @$setupdata->fetch_array[0];
	
	
	$CAMPOS = PREFIX_TABLES . $slug .'_campos';
	$CAMPO  = new MySQL();
	$CAMPO->set_table($CAMPOS);
	$CAMPO->set_where('token="' . $_POST['token'] . '"');
	$CAMPO->select();
	$CAMPO = $CAMPO->fetch_array[0];

	##########################################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	##########################################################################
	$template = new Template(TEMPLATE_LINK, true);
	
	##########################################################################  
	# SEPARAMOS AS VARIÁVEIS NECESSÁRIAS PARA O TEMPLATE 
	##########################################################################
	$template->TOKEN               = $_POST['token'];
	$template->WS_ID_FERRAMENTA    = ws_id_ferramenta;
	$template->CAMPOS              = PREFIX_TABLES . $slug .'_campos';
	$template->TYPE                = $CAMPO['type'];
	$template->LISTA_TABELA        = $CAMPO['listaTabela'];
	$template->LEGENDA             = $CAMPO['legenda'];
	$template->LABEL               = $CAMPO['label'];

	$template->COLUNA_MYSQL        = ($_POST['prefix'] != "" && substr($CAMPO['coluna_mysql'], 0, strlen($_POST['prefix'])) == $_POST['prefix']) ? substr($CAMPO['coluna_mysql'], strlen($_POST['prefix'])) : $CAMPO['coluna_mysql'];
	$template->LABEL_SUP           = $CAMPO['labelSup'];
	$template->PLACE               = $CAMPO['place'];
	$template->RADIO_CAT1          = ($CAMPO["filtro"] == "cat") ? "checked" : "";
	$template->RADIO_ITEM1         = ($CAMPO["filtro"] == "item") ? "checked" : "";
	$template->RADIO_MULTIPLE1     = ($CAMPO["multiple"] == "1") ? "checked" : "";
	$template->RADIO_MULTIPLE2     = ($CAMPO["multiple"] == "0") ? "checked" : "";
	$template->DOWNLOAD            = ($CAMPO['download'] == "1") ? "checked" : "";
	$template->REFERENCIA          = $CAMPO['referencia'];
	$template->CAT_REFERENCIA      = $CAMPO['cat_referencia'];
	$template->LARGURA             = $CAMPO['largura'];
	$template->ALTURA              = $CAMPO['altura'];
	$template->FILTRO              = $CAMPO['filtro'];
	$template->BG                  = $CAMPO['background'];
	$template->CARACTERES          = $CAMPO['caracteres'];
	$template->VALUES_OPT          = $CAMPO['values_opt'];
	$template->RUA                 = $CAMPO['rua'];
	$template->CIDADE              = $CAMPO['cidade'];
	$template->UF                  = $CAMPO['uf'];
	$template->PAIS                = $CAMPO['pais'];
	$template->CEP                 = $CAMPO['cep'];
	$template->BAIRRO              = $CAMPO['bairro'];
	$template->COLOR               = $CAMPO['color'];

	$template->VALUES_OPT_CHECKBOX 			= (isset($CAMPO['values_opt']) && $CAMPO['values_opt'] == "1") 	? "checked" : "";
	$template->VALUES_CHECKBOX_TOP_SUBTITLE = (isset($CAMPO['labelTop']) && $CAMPO['labelTop'] == "1") 		? "checked" : "";

	$template->DISABLED            = ($CAMPO['disabled'] == "1") ? "checked" : "";
	$template->PASSWORD            = ($CAMPO['password'] == "1") ? "checked" : "";
	$template->NUMERICO            = ($CAMPO['numerico'] == "1") ? "checked" : "";
	$template->CALENDARIO          = ($CAMPO['calendario'] == "1") ? "checked" : "";
	$template->FINANCEIRO          = ($CAMPO['financeiro'] == "1") ? "checked" : "";
	$template->EDITOR              = ($CAMPO['editor'] == "1") ? "checked" : "";
	$template->AUTOSIZE            = ($CAMPO['autosize'] == "1") ? "checked" : "";
	$template->UPLOAD              = ($CAMPO['upload'] == "1") ? "checked" : "";
	$template->CARACTERES_COUNT    = ($CAMPO['caracteres'] == "") ? "999999" : $CAMPO['caracteres'];
	$template->SINT_SELECT         = ($CAMPO['sintaxy'] != "") ? '$("#sintaxy option[value=\'' . $CAMPO['sintaxy'] . '\']").attr("selected","selected")' : "";
	
	################################################################	
	############## BEGIN TRANLATIONS ###############################
	################################################################
	
	$template->FieldDetails_Description				= ws::getLang('ToolsManager>FieldDetails>Description');
	$template->FieldDetails_EnteringKeywords		= ws::getLang('ToolsManager>FieldDetails>EnteringKeywords');
	$template->FieldDetails_ID						= ws::getLang('ToolsManager>FieldDetails>ID');
	$template->FieldDetails_FieldTitle				= ws::getLang('ToolsManager>FieldDetails>FieldTitle');
	$template->FieldDetails_NameTableItems			= ws::getLang('ToolsManager>FieldDetails>NameTableItems');
	$template->FieldDetails_LegendBalloon			= ws::getLang('ToolsManager>FieldDetails>LegendBalloon');
	$template->FieldDetails_LegendEqual				= ws::getLang('ToolsManager>FieldDetails>LegendEqual');
	$template->FieldDetails_SimpleInput				= ws::getLang('ToolsManager>FieldDetails>SimpleInput');
	$template->FieldDetails_PlaceHolder				= ws::getLang('ToolsManager>FieldDetails>PlaceHolder');
	$template->FieldDetails_SuperiorLabel			= ws::getLang('ToolsManager>FieldDetails>SuperiorLabel');
	$template->FieldDetails_FieldLabel				= ws::getLang('ToolsManager>FieldDetails>FieldLabel');
	$template->FieldDetails_MaxCharacter			= ws::getLang('ToolsManager>FieldDetails>MaxCharacter');
	$template->FieldDetails_Mask					= ws::getLang('ToolsManager>FieldDetails>Mask');
	$template->FieldDetails_FieldAddress			= ws::getLang('ToolsManager>FieldDetails>FieldAddress');
	$template->FieldDetails_FieldDisabled			= ws::getLang('ToolsManager>FieldDetails>FieldDisabled');
	$template->FieldDetails_FieldPassword			= ws::getLang('ToolsManager>FieldDetails>FieldPassword');	
	$template->FieldDetails_FieldNumber				= ws::getLang('ToolsManager>FieldDetails>FieldNumber');
	$template->FieldDetails_FieldDate				= ws::getLang('ToolsManager>FieldDetails>FieldDate');	
	$template->FieldDetails_FieldMoney				= ws::getLang('ToolsManager>FieldDetails>FieldMoney');
	$template->FieldDetails_CaseAddress				= ws::getLang('ToolsManager>FieldDetails>CaseAddress');	
	$template->FieldDetails_SelectField				= ws::getLang('ToolsManager>FieldDetails>SelectField');
	$template->FieldDetails_Street					= ws::getLang('ToolsManager>FieldDetails>Street');	
	$template->FieldDetails_City					= ws::getLang('ToolsManager>FieldDetails>City');
	$template->FieldDetails_State					= ws::getLang('ToolsManager>FieldDetails>State');
	$template->FieldDetails_Neighborhood			= ws::getLang('ToolsManager>FieldDetails>Neighborhood');
	$template->FieldDetails_Country					= ws::getLang('ToolsManager>FieldDetails>Country');
	$template->FieldDetails_PostalCode				= ws::getLang('ToolsManager>FieldDetails>PostalCode');
	$template->FieldDetails_UniqueIdentifier		= ws::getLang('ToolsManager>FieldDetails>UniqueIdentifier');
	$template->FieldDetails_SuperiorLabelCheck 		= ws::getLang('ToolsManager>FieldDetails>SuperiorLabelCheck');
	$template->FieldDetails_SuperiorLabelTextArea 	= ws::getLang('ToolsManager>FieldDetails>SuperiorLabelTextArea');
	$template->FieldDetails_MaxLenght				= ws::getLang('ToolsManager>FieldDetails>MaxLenght');
	$template->FieldDetails_DefaultColor			= ws::getLang('ToolsManager>FieldDetails>DefaultColor');
	$template->FieldDetails_TextColor				= ws::getLang('ToolsManager>FieldDetails>TextColor');
	$template->FieldDetails_FieldBackgroud			= ws::getLang('ToolsManager>FieldDetails>FieldBackgroud');
	$template->FieldDetails_EnabledEditor			= ws::getLang('ToolsManager>FieldDetails>EnabledEditor');
	$template->FieldDetails_AutoSize				= ws::getLang('ToolsManager>FieldDetails>AutoSize');
	$template->FieldDetails_EnabledUpload			= ws::getLang('ToolsManager>FieldDetails>EnabledUpload');
	$template->FieldDetails_RadioboxDesc			= ws::getLang('ToolsManager>FieldDetails>RadioboxDesc');
	$template->FieldDetails_WhatGroup				= ws::getLang('ToolsManager>FieldDetails>WhatGroup');
	$template->FieldDetails_LabelRadio				= ws::getLang('ToolsManager>FieldDetails>LabelRadio');
	$template->FieldDetails_FreeImage				= ws::getLang('ToolsManager>FieldDetails>FreeImage');
	$template->FieldDetails_NonGalery				= ws::getLang('ToolsManager>FieldDetails>NonGalery');
	$template->FieldDetails_Identifier				= ws::getLang('ToolsManager>FieldDetails>Identifier');
	$template->FieldDetails_PowerEditor				= ws::getLang('ToolsManager>FieldDetails>PowerEditor');	
	$template->FieldDetails_WhatSyntax				= ws::getLang('ToolsManager>FieldDetails>WhatSyntax');	
	$template->FieldDetails_CheckboxDesc			= ws::getLang('ToolsManager>FieldDetails>CheckboxDesc');	
	$template->FieldDetails_LabelCheckBox			= ws::getLang('ToolsManager>FieldDetails>LabelCheckBox');	
	$template->FieldDetails_SelectBoxDesc			= ws::getLang('ToolsManager>FieldDetails>SelectBoxDesc');	
	$template->FieldDetails_SuperiorLabelSelec		= ws::getLang('ToolsManager>FieldDetails>SuperiorLabelSelec');	
	$template->FieldDetails_SelectMultiple			= ws::getLang('ToolsManager>FieldDetails>SelectMultiple');	
	$template->FieldDetails_NotSaveSelect			= ws::getLang('ToolsManager>FieldDetails>NotSaveSelect');	
	$template->FieldDetails_SelMultDesc1			= ws::getLang('ToolsManager>FieldDetails>SelMultDesc1');	
	$template->FieldDetails_SelMultDesc2			= ws::getLang('ToolsManager>FieldDetails>SelMultDesc2');	
	$template->FieldDetails_SeparatorDesc			= ws::getLang('ToolsManager>FieldDetails>SeparatorDesc');	
	$template->FieldDetails_Label					= ws::getLang('ToolsManager>FieldDetails>Label');	
	$template->FieldDetails_GalButtonDesc1			= ws::getLang('ToolsManager>FieldDetails>GalButtonDesc1');	
	$template->FieldDetails_GalButtonDesc2			= ws::getLang('ToolsManager>FieldDetails>GalButtonDesc2');	
	$template->FieldDetails_MakeReference			= ws::getLang('ToolsManager>FieldDetails>MakeReference');	
	$template->FieldDetails_ChooseTool				= ws::getLang('ToolsManager>FieldDetails>ChooseTool');
	$template->FieldDetails_WhatListLink			= ws::getLang('ToolsManager>FieldDetails>WhatListLink');	
	$template->FieldDetails_SpecificCategory 		= ws::getLang('ToolsManager>FieldDetails>SpecificCategory');
	$template->FieldDetails_WhatLink				= ws::getLang('ToolsManager>FieldDetails>WhatLink');	
	$template->FieldDetails_Categories				= ws::getLang('ToolsManager>FieldDetails>Categories');
	$template->FieldDetails_LinkMultiple			= ws::getLang('ToolsManager>FieldDetails>LinkMultiple');	
	$template->FieldDetails_Multiple				= ws::getLang('ToolsManager>FieldDetails>Multiple');	
	$template->FieldDetails_Unitary					= ws::getLang('ToolsManager>FieldDetails>Unitary');	
	$template->FieldDetails_AccessButton			= ws::getLang('ToolsManager>FieldDetails>AccessButton');
	$template->FieldDetails_FileButton1				= ws::getLang('ToolsManager>FieldDetails>FileButton1');
	$template->FieldDetails_FileButton2				= ws::getLang('ToolsManager>FieldDetails>FileButton2');
	$template->FieldDetails_PhotoButton1			= ws::getLang('ToolsManager>FieldDetails>PhotoButton1');
	$template->FieldDetails_PhotoButton2			= ws::getLang('ToolsManager>FieldDetails>PhotoButton2');
	$template->FieldDetails_FileUpload1				= ws::getLang('ToolsManager>FieldDetails>FileUpload1');
	$template->FieldDetails_FileUpload2				= ws::getLang('ToolsManager>FieldDetails>FileUpload2');
	$template->FieldDetails_FileAuthorized			= ws::getLang('ToolsManager>FieldDetails>FileAuthorized');
	$template->FieldDetails_IFrame					= ws::getLang('ToolsManager>FieldDetails>IFrame');
	$template->FieldDetails_Name					= ws::getLang('ToolsManager>FieldDetails>Name');
	$template->FieldDetails_URL						= ws::getLang('ToolsManager>FieldDetails>URL');
	$template->FieldDetails_Variable				= ws::getLang('ToolsManager>FieldDetails>Variable');
	$template->FieldDetails_GET						= ws::getLang('ToolsManager>FieldDetails>GET');
	$template->FieldDetails_Date					= ws::getLang('ToolsManager>FieldDetails>Date');
	$template->FieldDetails_HowIframe				= ws::getLang('ToolsManager>FieldDetails>HowIframe');
	$template->FieldDetails_AudioPlayer				= ws::getLang('ToolsManager>FieldDetails>AudioPlayer');
	$template->FieldDetails_AudioField				= ws::getLang('ToolsManager>FieldDetails>AudioField');
	$template->FieldDetails_AudioLabelSup			= ws::getLang('ToolsManager>FieldDetails>AudioLabelSup');
	$template->FieldDetails_VideoPlayer				= ws::getLang('ToolsManager>FieldDetails>VideoPlayer');
	$template->FieldDetails_VideoField				= ws::getLang('ToolsManager>FieldDetails>VideoField');
	$template->FieldDetails_SimpleLabel				= ws::getLang('ToolsManager>FieldDetails>SimpleLabel');
	$template->FieldDetails_ColorPicker				= ws::getLang('ToolsManager>FieldDetails>ColorPicker');
	$template->FieldDetails_ColorPickerName			= ws::getLang('ToolsManager>FieldDetails>ColorPickerName');
	$template->FieldDetails_ColorPickerLabel		= ws::getLang('ToolsManager>FieldDetails>ColorPickerLabel');
	$template->FieldDetails_EnableTopCaption		= ws::getLang('ToolsManager>FieldDetails>EnableTopCaption');
	
	################################################################	
	############## END   TRANLATIONS ###############################
	################################################################
	
	$grupos       = array();
	$coluna_mysql = new MySQL();
	$coluna_mysql->set_table(PREFIX_TABLES . $slug .'_campos');
	$coluna_mysql->set_colum('coluna_mysql');
	$coluna_mysql->set_where('coluna_mysql<>"" AND type="radiobox"');
	$coluna_mysql->distinct();
	$coluna_mysql->select();
	foreach ($coluna_mysql->fetch_array as $grupo) {
		$grupos[] = $grupo['coluna_mysql'];
	}
	$template->CHECK_GROUP = '"' . implode($grupos, '","') . '"';


	$FERRAMENTA = new MySQL();
	$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
	$FERRAMENTA->set_where('App_Type="1"');
	$FERRAMENTA->select();
	$SELECTLINKTOOL = array();
	foreach ($FERRAMENTA->fetch_array as $item) {
		$SELECTLINKTOOL[] = "<option value='" . $item['id'] . "' " . (($CAMPO['values_opt'] == $item['id']) ? "selected" : "") . ">" . $item['_tit_menu_'] . "</option>";
	}
	$template->SELECT_LINK_TOOL = (count($SELECTLINKTOOL) > 0) ? implode($SELECTLINKTOOL) : "";
	
	$FERRAMENTA = new MySQL();
	$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
	$FERRAMENTA->set_where('App_Type="1"');
	$FERRAMENTA->select();
	$SELECT_INNER_TOOL = array();
	foreach ($FERRAMENTA->fetch_array as $item) {
		$SELECT_INNER_TOOL[] = "<option value='" . $item['id'] . "' " . (($CAMPO['values_opt'] == $item['id']) ? "selected" : "") . ">" . $item['_tit_menu_'] . "</option>";
	}
	$template->SELECT_INNER_TOOL = (count($SELECT_INNER_TOOL) > 0) ? implode($SELECT_INNER_TOOL) : "";
	
	$grupos       = array();
	$coluna_mysql = new MySQL();
	$coluna_mysql->set_table(PREFIX_TABLES . $slug .'_campos');
	$coluna_mysql->set_colum('coluna_mysql');
	$coluna_mysql->set_where('coluna_mysql<>""');
	$coluna_mysql->set_where('AND (type="input" OR type="textarea")');
	$coluna_mysql->set_where('AND ws_id_ferramenta="' . ws_id_ferramenta . '"');
	$coluna_mysql->distinct();
	$coluna_mysql->select();
	$coluna_mysql_array = Array();
	foreach ($coluna_mysql->fetch_array as $grupo) {
		$coluna_mysql_array[] = '<option value="' . $grupo['coluna_mysql'] . '">' . $grupo['coluna_mysql'] . '</option>';
	}
	$template->GMAPS_INPUT = (count($coluna_mysql_array) > 0) ? implode($coluna_mysql_array) : "";
	
	$grupos       = array();
	$coluna_mysql = new MySQL();
	$coluna_mysql->set_table(PREFIX_TABLES . $slug .'_campos');
	$coluna_mysql->set_colum('coluna_mysql');
	$coluna_mysql->set_where('coluna_mysql<>""');
	$coluna_mysql->set_where('AND type="radiobox"');
	$coluna_mysql->set_where('AND ws_id_ferramenta="' . ws_id_ferramenta . '"');
	$coluna_mysql->distinct();
	$coluna_mysql->select();
	$coluna_radiobox_array = Array();
	foreach ($coluna_mysql->fetch_array as $grupo) {
		if ($_POST['prefix'] != "" && substr($grupo['coluna_mysql'], 0, strlen($_POST['prefix'])) == $_POST['prefix']) {
			$coluna_radiobox_array[] = substr($grupo['coluna_mysql'], strlen($_POST['prefix']));
		} else {
			$coluna_radiobox_array[] = $grupo['coluna_mysql'];
		}
	}
	$template->RADIOBOX_INPUTS = (count($coluna_mysql_array) > 0) ? '"' . implode($coluna_radiobox_array, '","') . '"' : "";
	
	####################################################################################
	#	VERIFICAMOS O TIPO DO CAMPO E RETORNAMOS O HTML
	####################################################################################
	if ($CAMPO['type'] == 'key_works') {
		$template->block('KEY_WORKS');
	} elseif ($CAMPO['type'] == 'input') {
		$template->block('INPUT');
	} elseif ($CAMPO['type'] == 'textarea') {
		$template->block('TEXTAREA');
	} elseif ($CAMPO['type'] == 'radiobox') {
		$template->block('RADIOBOX');
	} elseif ($CAMPO['type'] == 'editor') {
		$template->block('INPUT_EDITOR');
	} elseif ($CAMPO['type'] == 'check') {
		$template->block('CHECKBOX');
	} elseif ($CAMPO['type'] == 'selectbox') {
		$template->block('SELECTBOX');
	} elseif ($CAMPO['type'] == 'multiple_select') {
		$template->block('MULTIPLE_SELECT');
	} elseif ($CAMPO['type'] == 'separador') {
		$template->block('SEPARADOR');
	} elseif ($CAMPO['type'] == 'bt_galerias') {
		$template->block('BT_GALERIAS');
	} elseif ($CAMPO['type'] == 'linkTool') {
		$template->block('LINK_TOOL');
	} elseif ($CAMPO['type'] == '_ferramenta_interna_') {
		$template->block('FERRAMENTA_INTERNA');
	} elseif ($CAMPO['type'] == 'bt_files') {
		$template->block('BT_FILES');
	} elseif ($CAMPO['type'] == 'bt_fotos') {
		$template->block('BT_FOTOS');
	} elseif ($CAMPO['type'] == 'file') {
		$template->block('FILE');
	} elseif ($CAMPO['type'] == 'iframe') {
		$template->block('IFRAME');
	} elseif ($CAMPO['type'] == 'playerMP3') {
		$template->FILTRO = ($CAMPO["filtro"] == "") ? 'playlist' : $CAMPO["filtro"];
		$template->block('PLAYER_MP3');
	} elseif ($CAMPO['type'] == 'playerVideo') {
		$template->block('PLAYER_VIDEO');
	}  elseif ($CAMPO['type'] == 'colorpicker') {
		$template->block('COLOR_PICKER');
	} elseif ($CAMPO['type'] == 'thumbmail') {
		$template->block('THUMBNAIL');
	}elseif ($CAMPO['type'] == 'label') {
		$template->block('INPUT_LABEL');

	}

	##########################################################################  
	# FINALIZAMOS O PHP E RETORNAMOS O HTML 
	##########################################################################
	$template->show();