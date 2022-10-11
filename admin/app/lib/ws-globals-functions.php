<?
set_time_limit(0);

########################################################################
# DEFINIMOS O ROOT DO SISTEMA
########################################################################
	if(!defined("ROOT_WEBSHEEP"))	{	
		if(!defined("ROOT_WEBSHEEP"))	{
			$path = explode("/admin",substr(dirname($_SERVER['REQUEST_URI']),1));
			define('ROOT_WEBSHEEP',(($path[0]=="") ? "/" : '/'.$path[0].'/'));
		}
	}
	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}

#########################################################################
# EVITANDO CONFLITOS COM QUEBRA DE LINHA
#########################################################################
if (!defined('PHP_EOL')) {
	switch (strtoupper(substr(PHP_OS, 0, 3))) {
		case 'WIN':
			define('PHP_EOL', "\r\n");// Windows
			break;      
		case 'DAR':
			define('PHP_EOL', "\r");// Mac
			break;       
		default:
			define('PHP_EOL', "\n");// Unix
	}
}

#########################################################################
# COLOCAMOS UYM VALOR DEFAULT NA FUNÇÃO array_filter
#########################################################################
function _array_filter($array=array(),$filter="strlen") {
	return array_filter($array,$filter);
}

#########################################################################
# CAPTA UM CALL DE ERRO
#########################################################################
function videoData($url = null, $data = "player", $w = null, $h = null, $default = null, $autoplay = "0") {
	$types = array('site', 'url', 'title', 'description', 'image', 'player', 'id');
	if ($url == null || (!strpos($url, "youtube") && !strpos($url, "vimeo"))) {
		if ($default == null) {
			return _erro("Erro ba função videoData, permitido apenas link do youtube ou vímeo");
		} else {
			return $default;
		}
	} elseif (!in_array($data, $types)) {
		return _erro("Erro na função videoData.<br>Os valores permitidos: site, url, title, description, image e player");
	} else {
		$tags = get_meta_tags($url);
		if ($w == null && $h == null) {
			$w = (isset($tags['twitter:player:width'])) 	? $tags['twitter:player:width']		: 200;
			$h = (isset($tags['twitter:player:height'])) 	? $tags['twitter:player:height'] 	: 200;
		}
		if ($data == "player") {
			return '<iframe width="' . $w . '" height="' . $h . '" src="' . $tags['twitter:' . $data] . '?autoplay=' . $autoplay . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		} elseif ($data == "site") {
			$tags['twitter:' . $data] = str_replace(array(
				'@'
			), '', $tags['twitter:' . $data]);
			return $tags['twitter:' . $data];
		} elseif ($data == "id") {
			$tags['twitter:player'] = str_replace(array(
				'https://player.vimeo.com/video/',
				'https://www.youtube.com/embed/'
			), '', $tags['twitter:player']);
			return $tags['twitter:player'];
		} else {
			return $tags['twitter:' . $data];
		}
	}
}
#########################################################################
# CAPTA UM CALL DE ERRO
#########################################################################

function get_caller_info() {
    $c 		= '';
    $file 	= '';
    $func 	= '';
    $class 	= '';
    $trace 	= debug_backtrace();
    if (isset($trace[2])) {
        $file = $trace[1]['file'];
        $func = $trace[2]['function'];
        if ((substr($func, 0, 7) == 'include') || (substr($func, 0, 7) == 'require')) {
            $func = '';
        }
    } else if (isset($trace[1])) {
        $file = $trace[1]['file'];
        $func = '';
    }
    if (isset($trace[3]['class'])) {
        $class = $trace[3]['class'];
        $func = $trace[3]['function'];
        $file = $trace[2]['file'];
    } else if (isset($trace[2]['class'])) {
        $class = $trace[2]['class'];
        $func = $trace[2]['function'];
        $file = $trace[1]['file'];
    }
    if ($file != '') $file = basename($file);
    $c = $file . ": ";
    $c .= ($class != '') ? ":" . $class . "->" : "";
    $c .= ($func != '') ? $func . "(): " : "";
    return($c);
}

#########################################################################
# CRIAMOS O HTACCESS DO PAINEL
#########################################################################
	function refresh_Path_htaccess($get, $set){
		file_put_contents($set,str_replace("{{ROOT_WEBSHEEP}}",ROOT_WEBSHEEP,file_get_contents($get)));
	}

#########################################################################
# REFRESH PATH NO FUNCTIONS WS JS
#########################################################################
	function refresh_Path_functions_JS(){
		$pathModel 		= 	INCLUDE_PATH.'admin/app/templates/js/websheep/functionsws_model.js';
		$pathDestino 	= 	INCLUDE_PATH.'admin/app/templates/js/websheep/functionsws.js';
		file_put_contents($pathDestino, str_replace('//{{rootPath}},', 'rootPath:"'.ROOT_WEBSHEEP.'",', file_get_contents($pathModel)));
	}
#########################################################################
# REFRESH PATH NO FUNCTION WS EM PHP
#########################################################################
	function refresh_Path_functions_PHP(){
		$pathDestino 	= 	INCLUDE_PATH.'admin/app/lib/class-ws-v1.php';
		$htaccess 		=	array_filter(explode(PHP_EOL,file_get_contents($pathDestino)));
		$newClass		=	array();
		
		foreach ($htaccess as $key => $line) {
			if (strpos($line,"const includePath") >= 1) {
  				$line=  '		const includePath="'.INCLUDE_PATH.'";';
			}elseif (strpos($line,"const rootPath") >= 1) {
  				$line=  '		const rootPath="'.ROOT_WEBSHEEP.'";';
			}elseif (strpos($line,"const domain") >= 1) {
  				$line=  '		const domain="'.$_SERVER['HTTP_HOST'].'";';
			}
			$newClass[] =  $line;		
		}
		copy($pathDestino,$pathDestino.'.bkp.'.rand(0,999999999999));
		file_put_contents($pathDestino, implode(PHP_EOL,$newClass));
	}
	
#########################################################################
# ATUALIZA O PATH
#########################################################################
    function refresh_Path_AllFiles(){
		 refresh_Path_functions_JS();
		 // refresh_Path_functions_PHP();
		 refresh_Path_htaccess(INCLUDE_PATH.'admin/app/templates/txt/ws-first-htaccess-admin.txt',	INCLUDE_PATH.'admin/.htaccess');
		 refresh_Path_htaccess(INCLUDE_PATH.'admin/app/templates/txt/ws-first-htaccess.txt',		INCLUDE_PATH.'.htaccess');
    }

#####################################################################
# 	RETORNA ARQUIVOS CONFORMA O PATERN SETADO
#####################################################################
	function rsearch($folder, $pattern) {
		$dir 		= new RecursiveDirectoryIterator($folder);
		$ite 		= new RecursiveIteratorIterator($dir);
		$files 		= new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
		$fileList 	= array();
		foreach($files as $file) {$fileList[] = $file[0];}
		return $fileList;
	}

#########################################################################
# FUNÇÃO PARA CRIAR DIRETÓRIOS VERIFICANDO ANTES
#########################################################################
    function _mkdir($_SETPATH=null,$root=false){
    	if(strpos($_SETPATH,INCLUDE_PATH)>=0){ 
    		$_SETPATH = str_replace(INCLUDE_PATH,"",$_SETPATH);
    	}
        $upload_directories = explode('/',$_SETPATH);
        $createDirectory 	= array();
        foreach ($upload_directories as $upload_directory){
            if($upload_directory!=""){$createDirectory[]=$upload_directory;};
            $createDirectoryPath = implode('/',$createDirectory);
			$old = umask(0); 
			if($root==true){
				$dir_exist 	= file_exists(INCLUDE_PATH.$createDirectoryPath);
				$ir_dir 	= is_dir(INCLUDE_PATH.$createDirectoryPath);
				if(!$dir_exist || !$ir_dir){
					mkdir(INCLUDE_PATH.$createDirectoryPath,0775);
				}
			}else{
				$dir_exist 	= file_exists($createDirectoryPath);
				$ir_dir 	= is_dir($createDirectoryPath);
				if(!$dir_exist || !$ir_dir){
					mkdir($createDirectoryPath,0775);
				}        
			}        
			umask($old); 
		}
        return true;
    }

#####################################################################
# 	COPIA DE ARQUIVO COM BKP
#####################################################################
	function _file_put_contents($file,$content,$replace=true, $bkp=false){
		$bkp_date = date('Y_m_d_H_i_s_', time()).md5(microtime(true));

		if(file_exists($file) && is_file($file) && $replace==true && $bkp==true){
			if(copy($file, $file.'__bkp_'.$bkp_date)){
				if(file_put_contents($file, $content)){
					return true;
				}else{
					return false;
				};
			}else{
				return false;
			};
		}elseif(file_exists($file) && $replace==true && $bkp==false){
			if(file_put_contents($file, $content)){
				return true;
			}else{
				return false;
			};
		}elseif(file_exists($file) && $replace==false){
			return true;
		}else{
			if(file_put_contents($file, $content)){
				return true;
			}else{
				return false;
			};

		}
	}

#####################################################################
# 	RENOMEIA UM ARQUIVO COM BKP ANTES
#####################################################################
	function _rename($file,$newFile=null,$bkp=false){
		if($newFile==null){ $newFile = $file; }
		$bkp_date = date('Y_m_d_H_i_s_', time()).md5(microtime(true));
		if(file_exists($newFile) && is_file($file) && $bkp==true){
			if(copy($newFile, $newFile.'__bkp_'.$bkp_date)){
				if(rename($file, $newFile)){
					return true;
				}else{
					return false;
				};
			}else{
				return false;
			};
		}else{
			if(is_file($file)){
				if(rename($file, $newFile)){
					return true;
				}else{
					return false;
				};
			}else{
				return false;
			};
		}
	}

#####################################################################
# 	COPIA DE ARQUIVO COM BKP
#####################################################################
	function _copy($file,$newFile=null,$bkp=false){
    	if($newFile==null){ $newFile = $file; }
		$bkp_date = date('Y_m_d_H_i_s_', time()).md5(microtime(true));
		if(file_exists($newFile) && is_file($file) && $bkp==true){
			if(copy($newFile, $newFile.'__bkp_'.$bkp_date)){
				if(copy($file, $newFile)){
					return true;
				}else{
					return false;
				};
			}else{
				return false;
			};
		}else{
			if(is_file($file)){
				if(copy($file, $newFile)){
					return true;
				}else{
					return false;
				};
			}else{
				return false;
			};
		}

	}

#########################################################################
# COPIA UM DIRETÓRIO INTEIRO PARA OUTRO LOCAL
#########################################################################
	function _copyFolder($DirFont, $DirDest,$root=false) {
		if($root==true){
			$openDir = INCLUDE_PATH.$DirFont;
			if (!file_exists(INCLUDE_PATH.$DirDest))	_mkdir($DirDest,$root); 
		}else{
			$openDir = $DirFont;
			if (!file_exists($DirDest))					_mkdir($DirDest,$root); 
		}

		if ($dd = opendir($openDir)) {
			while ($Arq=readdir($dd)) {
				 if ($Arq != "." && $Arq != "..") {
				 	$PathIn  = "$DirFont/$Arq";
				 	$PathOut = "$DirDest/$Arq";

					if($root==true && is_dir(INCLUDE_PATH.$PathIn)){

					 	_copyFolder($PathIn,$PathOut,$root);

					}elseif($root==false && is_dir($PathIn)){

					 	_copyFolder($PathIn,$PathOut,$root);

					}elseif($root==true && is_file(INCLUDE_PATH.$PathIn)){

				 			copy(INCLUDE_PATH.$PathIn, INCLUDE_PATH.$PathOut);

					}elseif($root==false && is_file($PathIn)){

				 			copy($PathIn,$PathOut);
					}
				}
			}
			closedir($dd);
		}
	}

#####################################################################
# 	RETORNA ARQUIVOS CONFORMA O PATERN SETADO
#####################################################################
	function get_content_type($file) {
		$info = pathinfo($file);
		$content_types = array(
			"php"		=>"application/x-httpd-php",
			"ez"		=>"application/andrew-inset",
			"aw"		=>"application/applixware",
			"atom"		=>"application/atom+xml",
			"atomcat"	=>"application/atomcat+xml",
			"atomsvc"	=>"application/atomsvc+xml",
			"ccxml"		=>"application/ccxml+xml",
			"cdmia"		=>"application/cdmi-capability",
			"cdmic"		=>"application/cdmi-container",
			"cdmid"		=>"application/cdmi-domain",
			"cdmio"		=>"application/cdmi-object",
			"cdmiq"		=>"application/cdmi-queue",
			"cu"		=>"application/cu-seeme",
			"davmount"	=>"application/davmount+xml",
			"dbk"		=>"application/docbook+xml",
			"dssc"		=>"application/dssc+der",
			"xdssc"		=>"application/dssc+xml",
			"ecma"		=>"application/ecmascript",
			"emma"		=>"application/emma+xml",
			"epub"		=>"application/epub+zip",
			"exi"		=>"application/exi",
			"pfr"		=>"application/font-tdpfr",
			"gml"		=>"application/gml+xml",
			"gpx"		=>"application/gpx+xml",
			"gxf"		=>"application/gxf",
			"stk"		=>"application/hyperstudio",
			"ink"		=>"application/inkml+xml",
			"inkml"		=>"application/inkml+xml",
			"ipfix"		=>"application/ipfix",
			"jar"		=>"application/java-archive",
			"ser"		=>"application/java-serialized-object",
			"class"		=>"application/java-vm",
			"js"		=>"application/javascript",
			"json"		=>"application/json",
			"jsonml"	=>"application/jsonml+json",
			"lostxml"	=>"application/lost+xml",
			"hqx"		=>"application/mac-binhex40",
			"cpt"		=>"application/mac-compactpro",
			"mads"		=>"application/mads+xml",
			"mrc"		=>"application/marc",
			"mrcx"		=>"application/marcxml+xml",
			"ma"		=>"application/mathematica",
			"nb"		=>"application/mathematica", 
			"mb"		=>"application/mathematica",
			"mathml"	=>"application/mathml+xml",
			"mbox"		=>"application/mbox",
			"mscml"		=>"application/mediaservercontrol+xml",
			"metalink"	=>"application/metalink+xml",
			"meta4"		=>"application/metalink4+xml",
			"mets"		=>"application/mets+xml",
			"mods"		=>"application/mods+xml",
			"m21"		=>"application/mp21", 
			"mp21"		=>"application/mp21",
			"mp4s"		=>"application/mp4",
			"doc"		=>"application/msword", 
			"dot"		=>"application/msword",
			"mxf"		=>"application/mxf",
			"bin"		=>"application/octet-stream",
			"dms"		=>"application/octet-stream",
			"lrf"		=>"application/octet-stream",
			"mar"		=>"application/octet-stream",
			"so"		=>"application/octet-stream",
			"dist"		=>"application/octet-stream",
			"distz"		=>"application/octet-stream",
			"pkg"		=>"application/octet-stream",
			"bpk"		=>"application/octet-stream",
			"dump"		=>"application/octet-stream",
			"elc"		=>"application/octet-stream",
			"deploy"	=>"application/octet-stream",
			"oda"		=>"application/oda",
			"opf"		=>"application/oebps-package+xml",
			"ogx"		=>"application/ogg",
			"omdoc"		=>"application/omdoc+xml",
			"onetoc"	=>"application/onenote",
			"onetoc2"	=>"application/onenote",
			"onetmp"	=>"application/onenote",
			"onepkg"	=>"application/onenote",
			"oxps"		=>"application/oxps",
			"xer"		=>"application/patch-ops-error+xml",
			"pdf"		=>"application/pdf",
			"pgp"		=>"application/pgp-encrypted",
			"asc"		=>"application/pgp-signature", 
			"sig"		=>"application/pgp-signature",
			"prf"		=>"application/pics-rules",
			"p10"		=>"application/pkcs10",
			"p7m"		=>"application/pkcs7-mime",
			"p7c"		=>"application/pkcs7-mime",
			"p7s"		=>"application/pkcs7-signature",
			"p8"		=>"application/pkcs8",
			"ac"		=>"application/pkix-attr-cert",
			"cer"		=>"application/pkix-cert",
			"crl"		=>"application/pkix-crl",
			"pkipath"		=>"application/pkix-pkipath",
			"pki"		=>"application/pkixcmp",
			"pls"		=>"application/pls+xml",
			"ai"		=>"application/postscript",
			"eps"		=>"application/postscript", 
			"ps"		=>"application/postscript",
			"cww"		=>"application/prs.cww",
			"pskcxml"		=>"application/pskc+xml",
			"rdf"		=>"application/rdf+xml",
			"rif"		=>"application/reginfo+xml",
			"rnc"		=>"application/relax-ng-compact-syntax",
			"rl"		=>"application/resource-lists+xml",
			"rld"		=>"application/resource-lists-diff+xml",
			"rs"		=>"application/rls-services+xml",
			"gbr"		=>"application/rpki-ghostbusters",
			"mft"		=>"application/rpki-manifest",
			"roa"		=>"application/rpki-roa",
			"rsd"		=>"application/rsd+xml",
			"rss"		=>"application/rss+xml",
			"rtf"		=>"application/rtf",
			"sbml"		=>"application/sbml+xml",
			"scq"		=>"application/scvp-cv-request",
			"scs"		=>"application/scvp-cv-response",
			"spq"		=>"application/scvp-vp-request",
			"spp"		=>"application/scvp-vp-response",
			"sdp"		=>"application/sdp",
			"setpay"		=>"application/set-payment-initiation",
			"setreg"		=>"application/set-registration-initiation",
			"shf"		=>"application/shf+xml",
			"smi"		=>"application/smil+xml",
			"smil"		=>"application/smil+xml",
			"rq"		=>"application/sparql-query",
			"srx"		=>"application/sparql-results+xml",
			"gram"		=>"application/srgs",
			"grxml"		=>"application/srgs+xml",
			"sru"		=>"application/sru+xml",
			"ssdl"		=>"application/ssdl+xml",
			"ssml"		=>"application/ssml+xml",
			"tei"		=>"application/tei+xml",
			"teicorpus"		=>"application/tei+xml",
			"tfi"		=>"application/thraud+xml",
			"tsd"		=>"application/timestamped-data",
			"plb"		=>"application/vnd.3gpp.pic-bw-large",
			"psb"		=>"application/vnd.3gpp.pic-bw-small",
			"pvb"		=>"application/vnd.3gpp.pic-bw-var",
			"tcap"		=>"application/vnd.3gpp2.tcap",
			"pwn"		=>"application/vnd.3m.post-it-notes",
			"aso"		=>"application/vnd.accpac.simply.aso",
			"imp"		=>"application/vnd.accpac.simply.imp",
			"acu"		=>"application/vnd.acucobol",
			"atc"		=>"application/vnd.acucorp",
			"acutc"		=>"application/vnd.acucorp",
			"air"		=>"application/vnd.adobe.air-application-installer-package+zip",
			"fcdt"		=>"application/vnd.adobe.formscentral.fcdt",
			"fxp"		=>"application/vnd.adobe.fxp", 
			"fxpl"		=>"application/vnd.adobe.fxp",
			"xdp"		=>"application/vnd.adobe.xdp+xml",
			"xfdf"		=>"application/vnd.adobe.xfdf",
			"ahead"		=>"application/vnd.ahead.space",
			"azf"		=>"application/vnd.airzip.filesecure.azf",
			"azs"		=>"application/vnd.airzip.filesecure.azs",
			"azw"		=>"application/vnd.amazon.ebook",
			"acc"		=>"application/vnd.americandynamics.acc",
			"ami"		=>"application/vnd.amiga.ami",
			"apk"		=>"application/vnd.android.package-archive",
			"cii"		=>"application/vnd.anser-web-certificate-issue-initiation",
			"fti"		=>"application/vnd.anser-web-funds-transfer-initiation",
			"atx"		=>"application/vnd.antix.game-component",
			"mpkg"		=>"application/vnd.apple.installer+xml",
			"m3u8"		=>"application/vnd.apple.mpegurl",
			"swi"		=>"application/vnd.aristanetworks.swi",
			"iota"		=>"application/vnd.astraea-software.iota",
			"aep"		=>"application/vnd.audiograph",
			"mpm"		=>"application/vnd.blueice.multipass",
			"bmi"		=>"application/vnd.bmi",
			"rep"		=>"application/vnd.businessobjects",
			"cdxml"		=>"application/vnd.chemdraw+xml",
			"mmd"		=>"application/vnd.chipnuts.karaoke-mmd",
			"cdy"		=>"application/vnd.cinderella",
			"cla"		=>"application/vnd.claymore",
			"rp9"		=>"application/vnd.cloanto.rp9",
			"c4g"		=>"application/vnd.clonk.c4group",
			"c4d"		=>"application/vnd.clonk.c4group",
			"c4f"		=>"application/vnd.clonk.c4group",
			"c4p"		=>"application/vnd.clonk.c4group",
			"c4u"		=>"application/vnd.clonk.c4group",
			"c11amc"		=>"application/vnd.cluetrust.cartomobile-config",
			"c11amz"		=>"application/vnd.cluetrust.cartomobile-config-pkg",
			"csp"		=>"application/vnd.commonspace",
			"cdbcmsg"		=>"application/vnd.contact.cmsg",
			"cmc"		=>"application/vnd.cosmocaller",
			"clkx"		=>"application/vnd.crick.clicker",
			"clkk"		=>"application/vnd.crick.clicker.keyboard",
			"clkp"		=>"application/vnd.crick.clicker.palette",
			"clkt"		=>"application/vnd.crick.clicker.template",
			"clkw"		=>"application/vnd.crick.clicker.wordbank",
			"wbs"		=>"application/vnd.criticaltools.wbs+xml",
			"pml"		=>"application/vnd.ctc-posml",
			"ppd"		=>"application/vnd.cups-ppd",
			"car"		=>"application/vnd.curl.car",
			"pcurl"		=>"application/vnd.curl.pcurl",
			"dart"		=>"application/vnd.dart",
			"rdz"		=>"application/vnd.data-vision.rdz",
			"uvf"		=>"application/vnd.dece.data",
			"uvvf"		=>"application/vnd.dece.data",
			"uvd"		=>"application/vnd.dece.data",
			"uvvd"		=>"application/vnd.dece.data",
			"uvt"		=>"application/vnd.dece.ttml+xml",
			"uvvt"		=>"application/vnd.dece.ttml+xml",
			"uvx"		=>"application/vnd.dece.unspecified",
			"uvvx"		=>"application/vnd.dece.unspecified",
			"uvz"		=>"application/vnd.dece.zip",
			"uvvz"		=>"application/vnd.dece.zip",
			"fe_launch"		=>"application/vnd.denovo.fcselayout-link",
			"dna"		=>"application/vnd.dna",
			"mlp"		=>"application/vnd.dolby.mlp",
			"dpg"		=>"application/vnd.dpgraph",
			"dfac"		=>"application/vnd.dreamfactory",
			"kpxx"		=>"application/vnd.ds-keypoint",
			"ait"		=>"application/vnd.dvb.ait",
			"svc"		=>"application/vnd.dvb.service",
			"geo"		=>"application/vnd.dynageo",
			"mag"		=>"application/vnd.ecowin.chart",
			"nml"		=>"application/vnd.enliven",
			"esf"		=>"application/vnd.epson.esf",
			"msf"		=>"application/vnd.epson.msf",
			"qam"		=>"application/vnd.epson.quickanime",
			"slt"		=>"application/vnd.epson.salt",
			"ssf"		=>"application/vnd.epson.ssf",
			"es3"		=>"application/vnd.eszigno3+xml",
			"et3"		=>"application/vnd.eszigno3+xml",
			"ez2"		=>"application/vnd.ezpix-album",
			"ez3"		=>"application/vnd.ezpix-package",
			"fdf"		=>"application/vnd.fdf",
			"mseed"		=>"application/vnd.fdsn.mseed",
			"seed"		=>"application/vnd.fdsn.seed",
			"dataless"		=>"application/vnd.fdsn.seed",
			"gph"		=>"application/vnd.flographit",
			"ftc"		=>"application/vnd.fluxtime.clip",
			"fm"		=>"application/vnd.framemaker",
			"frame"		=>"application/vnd.framemaker",
			"maker"		=>"application/vnd.framemaker",
			"book"		=>"application/vnd.framemaker",
			"fnc"		=>"application/vnd.frogans.fnc",
			"ltf"		=>"application/vnd.frogans.ltf",
			"fsc"		=>"application/vnd.fsc.weblaunch",
			"oas"		=>"application/vnd.fujitsu.oasys",
			"oa2"		=>"application/vnd.fujitsu.oasys2",
			"oa3"		=>"application/vnd.fujitsu.oasys3",
			"fg5"		=>"application/vnd.fujitsu.oasysgp",
			"bh2"		=>"application/vnd.fujitsu.oasysprs",
			"ddd"		=>"application/vnd.fujixerox.ddd",
			"xdw"		=>"application/vnd.fujixerox.docuworks",
			"xbd"		=>"application/vnd.fujixerox.docuworks.binder",
			"fzs"		=>"application/vnd.fuzzysheet",
			"txd"		=>"application/vnd.genomatix.tuxedo",
			"ggb"		=>"application/vnd.geogebra.file",
			"ggt"		=>"application/vnd.geogebra.tool",
			"gex"		=>"application/vnd.geometry-explorer",
			"gre"		=>"application/vnd.geometry-explorer",
			"gxt"		=>"application/vnd.geonext",
			"g2w"		=>"application/vnd.geoplan",
			"g3w"		=>"application/vnd.geospace",
			"gmx"		=>"application/vnd.gmx",
			"kml"		=>"application/vnd.google-earth.kml+xml",
			"kmz"		=>"application/vnd.google-earth.kmz",
			"gqf"		=>"application/vnd.grafeq",
			"gqs"		=>"application/vnd.grafeq",
			"gac"		=>"application/vnd.groove-account",
			"ghf"		=>"application/vnd.groove-help",
			"gim"		=>"application/vnd.groove-identity-message",
			"grv"		=>"application/vnd.groove-injector",
			"gtm"		=>"application/vnd.groove-tool-message",
			"tpl"		=>"application/vnd.groove-tool-template",
			"vcg"		=>"application/vnd.groove-vcard",
			"hal"		=>"application/vnd.hal+xml",
			"zmm"		=>"application/vnd.handheld-entertainment+xml",
			"hbci"		=>"application/vnd.hbci",
			"les"		=>"application/vnd.hhe.lesson-player",
			"hpgl"		=>"application/vnd.hp-hpgl",
			"hpid"		=>"application/vnd.hp-hpid",
			"hps"		=>"application/vnd.hp-hps",
			"jlt"		=>"application/vnd.hp-jlyt",
			"pcl"		=>"application/vnd.hp-pcl",
			"pclxl"		=>"application/vnd.hp-pclxl",
			"sfd-hdstx"		=>"application/vnd.hydrostatix.sof-data",
			"mpy"		=>"application/vnd.ibm.minipay",
			"afp"		=>"application/vnd.ibm.modcap",
			"listafp"		=>"application/vnd.ibm.modcap",
			"list3820"		=>"application/vnd.ibm.modcap",
			"irm"		=>"application/vnd.ibm.rights-management",
			"sc"		=>"application/vnd.ibm.secure-container",
			"icc"		=>"application/vnd.iccprofile",
			"icm"		=>"application/vnd.iccprofile",
			"igl"		=>"application/vnd.igloader",
			"ivp"		=>"application/vnd.immervision-ivp",
			"ivu"		=>"application/vnd.immervision-ivu",
			"igm"		=>"application/vnd.insors.igm",
			"xpw"		=>"application/vnd.intercon.formnet",
			"xpx"		=>"application/vnd.intercon.formnet",
			"i2g"		=>"application/vnd.intergeo",
			"qbo"		=>"application/vnd.intu.qbo",
			"qfx"		=>"application/vnd.intu.qfx",
			"rcprofile"		=>"application/vnd.ipunplugged.rcprofile",
			"irp"		=>"application/vnd.irepository.package+xml",
			"xpr"		=>"application/vnd.is-xpr",
			"fcs"		=>"application/vnd.isac.fcs",
			"jam"		=>"application/vnd.jam",
			"rms"		=>"application/vnd.jcp.javame.midlet-rms",
			"jisp"		=>"application/vnd.jisp",
			"joda"		=>"application/vnd.joost.joda-archive",
			"ktz"		=>"application/vnd.kahootz",
			"ktr"		=>"application/vnd.kahootz",
			"karbon"		=>"application/vnd.kde.karbon",
			"chrt"		=>"application/vnd.kde.kchart",
			"kfo"		=>"application/vnd.kde.kformula",
			"flw"		=>"application/vnd.kde.kivio",
			"kon"		=>"application/vnd.kde.kontour",
			"kpr"		=>"application/vnd.kde.kpresenter",
			"kpt"		=>"application/vnd.kde.kpresenter",
			"ksp"		=>"application/vnd.kde.kspread",
			"kwd"		=>"application/vnd.kde.kword",
			"kwt"		=>"application/vnd.kde.kword",
			"htke"		=>"application/vnd.kenameaapp",
			"kia"		=>"application/vnd.kidspiration",
			"kne"		=>"application/vnd.kinar",
			"knp"		=>"application/vnd.kinar",
			"skp"		=>"application/vnd.koan",
			"skd"		=>"application/vnd.koan",
			"skt"		=>"application/vnd.koan",
			"skm"		=>"application/vnd.koan",
			"sse"		=>"application/vnd.kodak-descriptor",
			"lasxml"		=>"application/vnd.las.las+xml",
			"lbd"		=>"application/vnd.llamagraphics.life-balance.desktop",
			"lbe"		=>"application/vnd.llamagraphics.life-balance.exchange+xml",
			"123"		=>"application/vnd.lotus-1-2-3",
			"apr"		=>"application/vnd.lotus-approach",
			"pre"		=>"application/vnd.lotus-freelance",
			"nsf"		=>"application/vnd.lotus-notes",
			"org"		=>"application/vnd.lotus-organizer",
			"scm"		=>"application/vnd.lotus-screencam",
			"lwp"		=>"application/vnd.lotus-wordpro",
			"portpkg"		=>"application/vnd.macports.portpkg",
			"mcd"		=>"application/vnd.mcd",
			"mc1"		=>"application/vnd.medcalcdata",
			"cdkey"		=>"application/vnd.mediastation.cdkey",
			"mwf"		=>"application/vnd.mfer",
			"mfm"		=>"application/vnd.mfmp",
			"flo"		=>"application/vnd.micrografx.flo",
			"igx"		=>"application/vnd.micrografx.igx",
			"mif"		=>"application/vnd.mif",
			"daf"		=>"application/vnd.mobius.daf",
			"dis"		=>"application/vnd.mobius.dis",
			"mbk"		=>"application/vnd.mobius.mbk",
			"mqy"		=>"application/vnd.mobius.mqy",
			"msl"		=>"application/vnd.mobius.msl",
			"plc"		=>"application/vnd.mobius.plc",
			"txf"		=>"application/vnd.mobius.txf",
			"mpn"		=>"application/vnd.mophun.application",
			"mpc"		=>"application/vnd.mophun.certificate",
			"xul"		=>"application/vnd.mozilla.xul+xml",
			"cil"		=>"application/vnd.ms-artgalry",
			"cab"		=>"application/vnd.ms-cab-compressed",
			"xls"		=>"application/vnd.ms-excel",
			"xlm"		=>"application/vnd.ms-excel",
			"xla"		=>"application/vnd.ms-excel",
			"xlc"		=>"application/vnd.ms-excel",
			"xlt"		=>"application/vnd.ms-excel",
			"xlw"		=>"application/vnd.ms-excel",
			"xlam"		=>"application/vnd.ms-excel.addin.macroenabled.12",
			"xlsb"		=>"application/vnd.ms-excel.sheet.binary.macroenabled.12",
			"xlsm"		=>"application/vnd.ms-excel.sheet.macroenabled.12",
			"xltm"		=>"application/vnd.ms-excel.template.macroenabled.12",
			"eot"		=>"application/vnd.ms-fontobject",
			"chm"		=>"application/vnd.ms-htmlhelp",
			"ims"		=>"application/vnd.ms-ims",
			"lrm"		=>"application/vnd.ms-lrm",
			"thmx"		=>"application/vnd.ms-officetheme",
			"cat"		=>"application/vnd.ms-pki.seccat",
			"stl"		=>"application/vnd.ms-pki.stl",
			"ppt"		=>"application/vnd.ms-powerpoint",
			"pps"		=>"application/vnd.ms-powerpoint",
			"pot"		=>"application/vnd.ms-powerpoint",
			"ppam"		=>"application/vnd.ms-powerpoint.addin.macroenabled.12",
			"pptm"		=>"application/vnd.ms-powerpoint.presentation.macroenabled.12",
			"sldm"		=>"application/vnd.ms-powerpoint.slide.macroenabled.12",
			"ppsm"		=>"application/vnd.ms-powerpoint.slideshow.macroenabled.12",
			"potm"		=>"application/vnd.ms-powerpoint.template.macroenabled.12",
			"mpp"		=>"application/vnd.ms-project",
			"mpt"		=>"application/vnd.ms-project",
			"docm"		=>"application/vnd.ms-word.document.macroenabled.12",
			"dotm"		=>"application/vnd.ms-word.template.macroenabled.12",
			"wps"		=>"application/vnd.ms-works",
			"wks"		=>"application/vnd.ms-works",
			"wcm"		=>"application/vnd.ms-works",
			"wdb"		=>"application/vnd.ms-works",
			"wpl"		=>"application/vnd.ms-wpl",
			"xps"		=>"application/vnd.ms-xpsdocument",
			"mseq"		=>"application/vnd.mseq",
			"mus"		=>"application/vnd.musician",
			"msty"		=>"application/vnd.muvee.style",
			"taglet"		=>"application/vnd.mynfc",
			"nlu"		=>"application/vnd.neurolanguage.nlu",
			"ntf"		=>"application/vnd.nitf",
			"nitf"		=>"application/vnd.nitf",
			"nnd"		=>"application/vnd.noblenet-directory",
			"nns"		=>"application/vnd.noblenet-sealer",
			"nnw"		=>"application/vnd.noblenet-web",
			"ngdat"		=>"application/vnd.nokia.n-gage.data",
			"n-gage"		=>"application/vnd.nokia.n-gage.symbian.install",
			"rpst"		=>"application/vnd.nokia.radio-preset",
			"rpss"		=>"application/vnd.nokia.radio-presets",
			"edm"		=>"application/vnd.novadigm.edm",
			"edx"		=>"application/vnd.novadigm.edx",
			"ext"		=>"application/vnd.novadigm.ext",
			"odc"		=>"application/vnd.oasis.opendocument.chart",
			"otc"		=>"application/vnd.oasis.opendocument.chart-template",
			"odb"		=>"application/vnd.oasis.opendocument.database",
			"odf"		=>"application/vnd.oasis.opendocument.formula",
			"odft"		=>"application/vnd.oasis.opendocument.formula-template",
			"odg"		=>"application/vnd.oasis.opendocument.graphics",
			"otg"		=>"application/vnd.oasis.opendocument.graphics-template",
			"odi"		=>"application/vnd.oasis.opendocument.image",
			"oti"		=>"application/vnd.oasis.opendocument.image-template",
			"odp"		=>"application/vnd.oasis.opendocument.presentation",
			"otp"		=>"application/vnd.oasis.opendocument.presentation-template",
			"ods"		=>"application/vnd.oasis.opendocument.spreadsheet",
			"ots"		=>"application/vnd.oasis.opendocument.spreadsheet-template",
			"odt"		=>"application/vnd.oasis.opendocument.text",
			"odm"		=>"application/vnd.oasis.opendocument.text-master",
			"ott"		=>"application/vnd.oasis.opendocument.text-template",
			"oth"		=>"application/vnd.oasis.opendocument.text-web",
			"xo"		=>"application/vnd.olpc-sugar",
			"dd2"		=>"application/vnd.oma.dd2+xml",
			"oxt"		=>"application/vnd.openofficeorg.extension",
			"pptx"		=>"application/vnd.openxmlformats-officedocument.presentationml.presentation",
			"sldx"		=>"application/vnd.openxmlformats-officedocument.presentationml.slide",
			"ppsx"		=>"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
			"potx"		=>"application/vnd.openxmlformats-officedocument.presentationml.template",
			"xlsx"		=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			"xltx"		=>"application/vnd.openxmlformats-officedocument.spreadsheetml.template",
			"docx"		=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"dotx"		=>"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
			"mgp"		=>"application/vnd.osgeo.mapguide.package",
			"dp"		=>"application/vnd.osgi.dp",
			"esa"		=>"application/vnd.osgi.subsystem",
			"pdb"		=>"application/vnd.palm",
			"pqa"		=>"application/vnd.palm",
			"oprc"		=>"application/vnd.palm",
			"paw"		=>"application/vnd.pawaafile",
			"str"		=>"application/vnd.pg.format",
			"ei6"		=>"application/vnd.pg.osasli",
			"efif"		=>"application/vnd.picsel",
			"wg"		=>"application/vnd.pmi.widget",
			"plf"		=>"application/vnd.pocketlearn",
			"pbd"		=>"application/vnd.powerbuilder6",
			"box"		=>"application/vnd.previewsystems.box",
			"mgz"		=>"application/vnd.proteus.magazine",
			"qps"		=>"application/vnd.publishare-delta-tree",
			"ptid"		=>"application/vnd.pvi.ptid1",
			"qxd"		=>"application/vnd.quark.quarkxpress",
			"qxt"		=>"application/vnd.quark.quarkxpress",
			"qwd"		=>"application/vnd.quark.quarkxpress",
			"qwt"		=>"application/vnd.quark.quarkxpress",
			"qxl"		=>"application/vnd.quark.quarkxpress",
			"qxb"		=>"application/vnd.quark.quarkxpress",
			"bed"		=>"application/vnd.realvnc.bed",
			"mxl"		=>"application/vnd.recordare.musicxml",
			"musicxml"		=>"application/vnd.recordare.musicxml+xml",
			"cryptonote"		=>"application/vnd.rig.cryptonote",
			"cod"		=>"application/vnd.rim.cod",
			"rm"		=>"application/vnd.rn-realmedia",
			"rmvb"		=>"application/vnd.rn-realmedia-vbr",
			"link66"		=>"application/vnd.route66.link66+xml",
			"st"		=>"application/vnd.sailingtracker.track",
			"see"		=>"application/vnd.seemail",
			"sema"		=>"application/vnd.sema",
			"semd"		=>"application/vnd.semd",
			"semf"		=>"application/vnd.semf",
			"ifm"		=>"application/vnd.shana.informed.formdata",
			"itp"		=>"application/vnd.shana.informed.formtemplate",
			"iif"		=>"application/vnd.shana.informed.interchange",
			"ipk"		=>"application/vnd.shana.informed.package",
			"twd"		=>"application/vnd.simtech-mindmapper",
			"twds"		=>"application/vnd.simtech-mindmapper",
			"mmf"		=>"application/vnd.smaf",
			"teacher"		=>"application/vnd.smart.teacher",
			"sdkm"		=>"application/vnd.solent.sdkm+xml",
			"sdkd"		=>"application/vnd.solent.sdkm+xml",
			"dxp"		=>"application/vnd.spotfire.dxp",
			"sfs"		=>"application/vnd.spotfire.sfs",
			"sdc"		=>"application/vnd.stardivision.calc",
			"sda"		=>"application/vnd.stardivision.draw",
			"sdd"		=>"application/vnd.stardivision.impress",
			"smf"		=>"application/vnd.stardivision.math",
			"sdw"		=>"application/vnd.stardivision.writer",
			"vor"		=>"application/vnd.stardivision.writer",
			"sgl"		=>"application/vnd.stardivision.writer-global",
			"smzip"		=>"application/vnd.stepmania.package",
			"sm"		=>"application/vnd.stepmania.stepchart",
			"sxc"		=>"application/vnd.sun.xml.calc",
			"stc"		=>"application/vnd.sun.xml.calc.template",
			"sxd"		=>"application/vnd.sun.xml.draw",
			"std"		=>"application/vnd.sun.xml.draw.template",
			"sxi"		=>"application/vnd.sun.xml.impress",
			"sti"		=>"application/vnd.sun.xml.impress.template",
			"sxm"		=>"application/vnd.sun.xml.math",
			"sxw"		=>"application/vnd.sun.xml.writer",
			"sxg"		=>"application/vnd.sun.xml.writer.global",
			"stw"		=>"application/vnd.sun.xml.writer.template",
			"sus"		=>"application/vnd.sus-calendar",
			"susp"		=>"application/vnd.sus-calendar",
			"svd"		=>"application/vnd.svd",
			"sis"		=>"application/vnd.symbian.install",
			"sisx"		=>"application/vnd.symbian.install",
			"xsm"		=>"application/vnd.syncml+xml",
			"bdm"		=>"application/vnd.syncml.dm+wbxml",
			"xdm"		=>"application/vnd.syncml.dm+xml",
			"tao"		=>"application/vnd.tao.intent-module-archive",
			"pcap"		=>"application/vnd.tcpdump.pcap",
			"cap"		=>"application/vnd.tcpdump.pcap",
			"dmp"		=>"application/vnd.tcpdump.pcap",
			"tmo"		=>"application/vnd.tmobile-livetv",
			"tpt"		=>"application/vnd.trid.tpt",
			"mxs"		=>"application/vnd.triscape.mxs",
			"tra"		=>"application/vnd.trueapp",
			"ufd"		=>"application/vnd.ufdl",
			"ufdl"		=>"application/vnd.ufdl",
			"utz"		=>"application/vnd.uiq.theme",
			"umj"		=>"application/vnd.umajin",
			"unityweb"		=>"application/vnd.unity",
			"uoml"		=>"application/vnd.uoml+xml",
			"vcx"		=>"application/vnd.vcx",
			"vsd"		=>"application/vnd.visio",
			"vst"		=>"application/vnd.visio",
			"vss"		=>"application/vnd.visio",
			"vsw"		=>"application/vnd.visio",
			"vis"		=>"application/vnd.visionary",
			"vsf"		=>"application/vnd.vsf",
			"wbxml"		=>"application/vnd.wap.wbxml",
			"wmlc"		=>"application/vnd.wap.wmlc",
			"wmlsc"		=>"application/vnd.wap.wmlscriptc",
			"wtb"		=>"application/vnd.webturbo",
			"nbp"		=>"application/vnd.wolfram.player",
			"wpd"		=>"application/vnd.wordperfect",
			"wqd"		=>"application/vnd.wqd",
			"stf"		=>"application/vnd.wt.stf",
			"xar"		=>"application/vnd.xara",
			"xfdl"		=>"application/vnd.xfdl",
			"hvd"		=>"application/vnd.yamaha.hv-dic",
			"hvs"		=>"application/vnd.yamaha.hv-script",
			"hvp"		=>"application/vnd.yamaha.hv-voice",
			"osf"		=>"application/vnd.yamaha.openscoreformat",
			"osfpvg"		=>"application/vnd.yamaha.openscoreformat.osfpvg+xml",
			"saf"		=>"application/vnd.yamaha.smaf-audio",
			"spf"		=>"application/vnd.yamaha.smaf-phrase",
			"cmp"		=>"application/vnd.yellowriver-custom-menu",
			"zir"		=>"application/vnd.zul",
			"zirz"		=>"application/vnd.zul",
			"zaz"		=>"application/vnd.zzazz.deck+xml",
			"vxml"		=>"application/voicexml+xml",
			"wgt"		=>"application/widget",
			"hlp"		=>"application/winhlp",
			"wsdl"		=>"application/wsdl+xml",
			"wspolicy"		=>"application/wspolicy+xml",
			"7z"		=>"application/x-7z-compressed",
			"abw"		=>"application/x-abiword",
			"ace"		=>"application/x-ace-compressed",
			"dmg"		=>"application/x-apple-diskimage",
			"aab"		=>"application/x-authorware-bin",
			"x32"		=>"application/x-authorware-bin",
			"u32"		=>"application/x-authorware-bin",
			"vox"		=>"application/x-authorware-bin",
			"aam"		=>"application/x-authorware-map",
			"aas"		=>"application/x-authorware-seg",
			"bcpio"		=>"application/x-bcpio",
			"torrent"		=>"application/x-bittorrent",
			"blb"		=>"application/x-blorb",
			"blorb"		=>"application/x-blorb",
			"bz"		=>"application/x-bzip",
			"bz2"		=>"application/x-bzip2",
			"boz"		=>"application/x-bzip2",
			"cbr"		=>"application/x-cbr",
			"cba"		=>"application/x-cbr",
			"cbt"		=>"application/x-cbr",
			"cbz"		=>"application/x-cbr",
			"cb7"		=>"application/x-cbr",
			"vcd"		=>"application/x-cdlink",
			"cfs"		=>"application/x-cfs-compressed",
			"chat"		=>"application/x-chat",
			"pgn"		=>"application/x-chess-pgn",
			"nsc"		=>"application/x-conference",
			"cpio"		=>"application/x-cpio",
			"csh"		=>"application/x-csh",
			"deb"		=>"application/x-debian-package",
			"udeb"		=>"application/x-debian-package",
			"dgc"		=>"application/x-dgc-compressed",
			"dir"		=>"application/x-director",
			"dcr"		=>"application/x-director",
			"dxr"		=>"application/x-director",
			"cst"		=>"application/x-director",
			"cct"		=>"application/x-director",
			"cxt"		=>"application/x-director",
			"w3d"		=>"application/x-director",
			"fgd"		=>"application/x-director",
			"swa"		=>"application/x-director",
			"wad"		=>"application/x-doom",
			"ncx"		=>"application/x-dtbncx+xml",
			"dtb"		=>"application/x-dtbook+xml",
			"res"		=>"application/x-dtbresource+xml",
			"dvi"		=>"application/x-dvi",
			"evy"		=>"application/x-envoy",
			"eva"		=>"application/x-eva",
			"bdf"		=>"application/x-font-bdf",
			"gsf"		=>"application/x-font-ghostscript",
			"psf"		=>"application/x-font-linux-psf",
			"otf"		=>"application/x-font-otf",
			"pcf"		=>"application/x-font-pcf",
			"snf"		=>"application/x-font-snf",
			"ttf"		=>"application/x-font-ttf",
			"ttc"		=>"application/x-font-ttf",
			"pfa"		=>"application/x-font-type1",
			"pfb"		=>"application/x-font-type1",
			"pfm"		=>"application/x-font-type1",
			"afm"		=>"application/x-font-type1",
			"woff"		=>"application/x-font-woff",
			"arc"		=>"application/x-freearc",
			"spl"		=>"application/x-futuresplash",
			"gca"		=>"application/x-gca-compressed",
			"ulx"		=>"application/x-glulx",
			"gnumeric"		=>"application/x-gnumeric",
			"gramps"		=>"application/x-gramps-xml",
			"gtar"		=>"application/x-gtar",
			"hdf"		=>"application/x-hdf",
			"install"		=>"application/x-install-instructions",
			"iso"		=>"application/x-iso9660-image",
			"jnlp"		=>"application/x-java-jnlp-file",
			"latex"		=>"application/x-latex",
			"lzh"		=>"application/x-lzh-compressed",
			"lha"		=>"application/x-lzh-compressed",
			"mie"		=>"application/x-mie",
			"prc"		=>"application/x-mobipocket-ebook",
			"mobi"		=>"application/x-mobipocket-ebook",
			"application"		=>"application/x-ms-application",
			"lnk"		=>"application/x-ms-shortcut",
			"wmd"		=>"application/x-ms-wmd",
			"wmz"		=>"application/x-ms-wmz",
			"xbap"		=>"application/x-ms-xbap",
			"mdb"		=>"application/x-msaccess",
			"obd"		=>"application/x-msbinder",
			"crd"		=>"application/x-mscardfile",
			"clp"		=>"application/x-msclip",
			"exe"		=>"application/x-msdownload",
			"dll"		=>"application/x-msdownload",
			"com"		=>"application/x-msdownload",
			"bat"		=>"application/x-msdownload",
			"msi"		=>"application/x-msdownload",
			"mvb"		=>"application/x-msmediaview",
			"m13"		=>"application/x-msmediaview",
			"m14"		=>"application/x-msmediaview",
			"wmf"		=>"application/x-msmetafile",
			"wmz"		=>"application/x-msmetafile",
			"emf"		=>"application/x-msmetafile",
			"emz"		=>"application/x-msmetafile",
			"mny"		=>"application/x-msmoney",
			"pub"		=>"application/x-mspublisher",
			"scd"		=>"application/x-msschedule",
			"trm"		=>"application/x-msterminal",
			"wri"		=>"application/x-mswrite",
			"nc"		=>"application/x-netcdf",
			"cdf"		=>"application/x-netcdf",
			"nzb"		=>"application/x-nzb",
			"p12"		=>"application/x-pkcs12",
			"pfx"		=>"application/x-pkcs12",
			"p7b"		=>"application/x-pkcs7-certificates",
			"spc"		=>"application/x-pkcs7-certificates",
			"p7r"		=>"application/x-pkcs7-certreqresp",
			"rar"		=>"application/x-rar-compressed",
			"ris"		=>"application/x-research-info-systems",
			"sh"		=>"application/x-sh",
			"shar"		=>"application/x-shar",
			"swf"		=>"application/x-shockwave-flash",
			"xap"		=>"application/x-silverlight-app",
			"sql"		=>"application/x-sql",
			"sit"		=>"application/x-stuffit",
			"sitx"		=>"application/x-stuffitx",
			"srt"		=>"application/x-subrip",
			"sv4cpio"		=>"application/x-sv4cpio",
			"sv4crc"		=>"application/x-sv4crc",
			"t3"		=>"application/x-t3vm-image",
			"gam"		=>"application/x-tads",
			"tar"		=>"application/x-tar",
			"tcl"		=>"application/x-tcl",
			"tex"		=>"application/x-tex",
			"tfm"		=>"application/x-tex-tfm",
			"texinfo"		=>"application/x-texinfo",
			"texi"		=>"application/x-texinfo",
			"obj"		=>"application/x-tgif",
			"ustar"		=>"application/x-ustar",
			"src"		=>"application/x-wais-source",
			"der"		=>"application/x-x509-ca-cert",
			"crt"		=>"application/x-x509-ca-cert",
			"fig"		=>"application/x-xfig",
			"xlf"		=>"application/x-xliff+xml",
			"xpi"		=>"application/x-xpinstall",
			"xz"		=>"application/x-xz",
			"z1"		=>"application/x-zmachine",
			"z2"		=>"application/x-zmachine",
			"z3"		=>"application/x-zmachine",
			"z4"		=>"application/x-zmachine",
			"z5"		=>"application/x-zmachine",
			"z6"		=>"application/x-zmachine",
			"z7"		=>"application/x-zmachine",
			"z8"		=>"application/x-zmachine",
			"xaml"		=>"application/xaml+xml",
			"xdf"		=>"application/xcap-diff+xml",
			"xenc"		=>"application/xenc+xml",
			"xhtml"		=>"application/xhtml+xml",
			"xht"		=>"application/xhtml+xml",
			"xml"		=>"application/xml",
			"xsl"		=>"application/xml",
			"dtd"		=>"application/xml-dtd",
			"xop"		=>"application/xop+xml",
			"xpl"		=>"application/xproc+xml",
			"xslt"		=>"application/xslt+xml",
			"xspf"		=>"application/xspf+xml",
			"mxml"		=>"application/xv+xml",
			"xhvml"		=>"application/xv+xml",
			"xvml"		=>"application/xv+xml",
			"xvm"		=>"application/xv+xml",
			"yang"		=>"application/yang",
			"yin"		=>"application/yin+xml",
			"zip"		=>"application/zip",
			"adp"		=>"audio/adpcm",
			"au"		=>"audio/basic",
			"snd"		=>"audio/basic",
			"mid"		=>"audio/midi",
			"midi"		=>"audio/midi",
			"kar"		=>"audio/midi",
			"rmi"		=>"audio/midi",
			"m4a"		=>"audio/mp4",
			"mp4a"		=>"audio/mp4",
			"mpga"		=>"audio/mpeg",
			"mp2"		=>"audio/mpeg",
			"mp2a"		=>"audio/mpeg",
			"mp3"		=>"audio/mpeg",
			"m2a"		=>"audio/mpeg",
			"m3a"		=>"audio/mpeg",
			"oga"		=>"audio/ogg",
			"ogg"		=>"audio/ogg",
			"spx"		=>"audio/ogg",
			"s3m"		=>"audio/s3m",
			"sil"		=>"audio/silk",
			"uva"		=>"audio/vnd.dece.audio",
			"uvva"		=>"audio/vnd.dece.audio",
			"eol"		=>"audio/vnd.digital-winds",
			"dra"		=>"audio/vnd.dra",
			"dts"		=>"audio/vnd.dts",
			"dtshd"		=>"audio/vnd.dts.hd",
			"lvp"		=>"audio/vnd.lucent.voice",
			"pya"		=>"audio/vnd.ms-playready.media.pya",
			"ecelp4800"		=>"audio/vnd.nuera.ecelp4800",
			"ecelp7470"		=>"audio/vnd.nuera.ecelp7470",
			"ecelp9600"		=>"audio/vnd.nuera.ecelp9600",
			"rip"		=>"audio/vnd.rip",
			"weba"		=>"audio/webm",
			"aac"		=>"audio/x-aac",
			"aif"		=>"audio/x-aiff",
			"aiff"		=>"audio/x-aiff",
			"aifc"		=>"audio/x-aiff",
			"caf"		=>"audio/x-caf",
			"flac"		=>"audio/x-flac",
			"mka"		=>"audio/x-matroska",
			"m3u"		=>"audio/x-mpegurl",
			"wax"		=>"audio/x-ms-wax",
			"wma"		=>"audio/x-ms-wma",
			"ram"		=>"audio/x-pn-realaudio",
			"ra"		=>"audio/x-pn-realaudio",
			"rmp"		=>"audio/x-pn-realaudio-plugin",
			"wav"		=>"audio/x-wav",
			"xm"		=>"audio/xm",
			"cdx"		=>"chemical/x-cdx",
			"cif"		=>"chemical/x-cif",
			"cmdf"		=>"chemical/x-cmdf",
			"cml"		=>"chemical/x-cml",
			"csml"		=>"chemical/x-csml",
			"xyz"		=>"chemical/x-xyz",
			"bmp"		=>"image/bmp",
			"cgm"		=>"image/cgm",
			"g3"		=>"image/g3fax",
			"gif"		=>"image/gif",
			"ief"		=>"image/ief",
			"jpeg"		=>"image/jpeg",
			"jpg"		=>"image/jpeg",
			"jpe"		=>"image/jpeg",
			"ktx"		=>"image/ktx",
			"png"		=>"image/png",
			"btif"		=>"image/prs.btif",
			"sgi"		=>"image/sgi",
			"svg"		=>"image/svg+xml",
			"svgz"		=>"image/svg+xml",
			"tiff"		=>"image/tiff",
			"tif"		=>"image/tiff",
			"psd"		=>"image/vnd.adobe.photoshop",
			"uvi"		=>"image/vnd.dece.graphic",
			"uvvi"		=>"image/vnd.dece.graphic",
			"uvg"		=>"image/vnd.dece.graphic",
			"uvvg"		=>"image/vnd.dece.graphic",
			"sub"		=>"image/vnd.dvb.subtitle",
			"djvu"		=>"image/vnd.djvu",
			"djv"		=>"image/vnd.djvu",
			"dwg"		=>"image/vnd.dwg",
			"dxf"		=>"image/vnd.dxf",
			"fbs"		=>"image/vnd.fastbidsheet",
			"fpx"		=>"image/vnd.fpx",
			"fst"		=>"image/vnd.fst",
			"mmr"		=>"image/vnd.fujixerox.edmics-mmr",
			"rlc"		=>"image/vnd.fujixerox.edmics-rlc",
			"mdi"		=>"image/vnd.ms-modi",
			"wdp"		=>"image/vnd.ms-photo",
			"npx"		=>"image/vnd.net-fpx",
			"wbmp"		=>"image/vnd.wap.wbmp",
			"xif"		=>"image/vnd.xiff",
			"webp"		=>"image/webp",
			"3ds"		=>"image/x-3ds",
			"ras"		=>"image/x-cmu-raster",
			"cmx"		=>"image/x-cmx",
			"fh"		=>"image/x-freehand",
			"fhc"		=>"image/x-freehand",
			"fh4"		=>"image/x-freehand",
			"fh5"		=>"image/x-freehand",
			"fh7"		=>"image/x-freehand",
			"ico"		=>"image/x-icon",
			"sid"		=>"image/x-mrsid-image",
			"pcx"		=>"image/x-pcx",
			"pic"		=>"image/x-pict",
			"pct"		=>"image/x-pict",
			"pnm"		=>"image/x-portable-anymap",
			"pbm"		=>"image/x-portable-bitmap",
			"pgm"		=>"image/x-portable-graymap",
			"ppm"		=>"image/x-portable-pixmap",
			"rgb"		=>"image/x-rgb",
			"tga"		=>"image/x-tga",
			"xbm"		=>"image/x-xbitmap",
			"xpm"		=>"image/x-xpixmap",
			"xwd"		=>"image/x-xwindowdump",
			"eml"		=>"message/rfc822",
			"mime"		=>"message/rfc822",
			"igs"		=>"model/iges",
			"iges"		=>"model/iges",
			"msh"		=>"model/mesh",
			"mesh"		=>"model/mesh",
			"silo"		=>"model/mesh",
			"dae"		=>"model/vnd.collada+xml",
			"dwf"		=>"model/vnd.dwf",
			"gdl"		=>"model/vnd.gdl",
			"gtw"		=>"model/vnd.gtw",
			"mts"		=>"model/vnd.mts",
			"vtu"		=>"model/vnd.vtu",
			"wrl"		=>"model/vrml",
			"vrml"		=>"model/vrml",
			"x3db"		=>"model/x3d+binary",
			"x3dbz"		=>"model/x3d+binary",
			"x3dv"		=>"model/x3d+vrml",
			"x3dvz"		=>"model/x3d+vrml",
			"x3d"		=>"model/x3d+xml",
			"x3dz"		=>"model/x3d+xml",
			"appcache"		=>"text/cache-manifest",
			"ics"		=>"text/calendar",
			"ifb"		=>"text/calendar",
			"css"		=>"text/css",
			"csv"		=>"text/csv",
			"html"		=>"text/html",
			"htm"		=>"text/html",
			"n3"		=>"text/n3",
			"txt"		=>"text/plain",
			"text"		=>"text/plain",
			"conf"		=>"text/plain",
			"def"		=>"text/plain",
			"list"		=>"text/plain",
			"log"		=>"text/plain",
			"in"		=>"text/plain",
			"dsc"		=>"text/prs.lines.tag",
			"rtx"		=>"text/richtext",
			"sgml"		=>"text/sgml",
			"sgm"		=>"text/sgml",
			"tsv"		=>"text/tab-separated-values",
			"t"		=>"text/troff",
			"tr"		=>"text/troff",
			"roff"		=>"text/troff",
			"man"		=>"text/troff",
			"me"		=>"text/troff",
			"ms"		=>"text/troff",
			"ttl"		=>"text/turtle",
			"uri"		=>"text/uri-list",
			"uris"		=>"text/uri-list",
			"urls"		=>"text/uri-list",
			"vcard"		=>"text/vcard",
			"curl"		=>"text/vnd.curl",
			"dcurl"		=>"text/vnd.curl.dcurl",
			"scurl"		=>"text/vnd.curl.scurl",
			"mcurl"		=>"text/vnd.curl.mcurl",
			"sub"		=>"text/vnd.dvb.subtitle",
			"fly"		=>"text/vnd.fly",
			"flx"		=>"text/vnd.fmi.flexstor",
			"gv"		=>"text/vnd.graphviz",
			"3dml"		=>"text/vnd.in3d.3dml",
			"spot"		=>"text/vnd.in3d.spot",
			"jad"		=>"text/vnd.sun.j2me.app-descriptor",
			"wml"		=>"text/vnd.wap.wml",
			"wmls"		=>"text/vnd.wap.wmlscript",
			"s"		=>"text/x-asm",
			"asm"		=>"text/x-asm",
			"c"		=>"text/x-c",
			"cc"		=>"text/x-c",
			"cxx"		=>"text/x-c",
			"cpp"		=>"text/x-c",
			"h"		=>"text/x-c",
			"hh"		=>"text/x-c",
			"dic"		=>"text/x-c",
			"f"		=>"text/x-fortran",
			"for"		=>"text/x-fortran",
			"f77"		=>"text/x-fortran",
			"f90"		=>"text/x-fortran",
			"java"		=>"text/x-java-source",
			"opml"		=>"text/x-opml",
			"p"		=>"text/x-pascal",
			"pas"		=>"text/x-pascal",
			"nfo"		=>"text/x-nfo",
			"etx"		=>"text/x-setext",
			"sfv"		=>"text/x-sfv",
			"uu"		=>"text/x-uuencode",
			"vcs"		=>"text/x-vcalendar",
			"vcf"		=>"text/x-vcard",
			"3gp"		=>"video/3gpp",
			"3g2"		=>"video/3gpp2",
			"h261"		=>"video/h261",
			"h263"		=>"video/h263",
			"h264"		=>"video/h264",
			"jpgv"		=>"video/jpeg",
			"jpm"		=>"video/jpm",
			"jpgm"		=>"video/jpm",
			"mj2"		=>"video/mj2",
			"mjp2"		=>"video/mj2",
			"mp4"		=>"video/mp4",
			"mp4v"		=>"video/mp4",
			"mpg4"		=>"video/mp4",
			"mpeg"		=>"video/mpeg",
			"mpg"		=>"video/mpeg",
			"mpe"		=>"video/mpeg",
			"m1v"		=>"video/mpeg",
			"m2v"		=>"video/mpeg",
			"ogv"		=>"video/ogg",
			"qt"		=>"video/quicktime",
			"mov"		=>"video/quicktime",
			"uvh"		=>"video/vnd.dece.hd",
			"uvvh"		=>"video/vnd.dece.hd",
			"uvm"		=>"video/vnd.dece.mobile",
			"uvvm"		=>"video/vnd.dece.mobile",
			"uvp"		=>"video/vnd.dece.pd",
			"uvvp"		=>"video/vnd.dece.pd",
			"uvs"		=>"video/vnd.dece.sd",
			"uvvs"		=>"video/vnd.dece.sd",
			"uvv"		=>"video/vnd.dece.video",
			"uvvv"		=>"video/vnd.dece.video",
			"dvb"		=>"video/vnd.dvb.file",
			"fvt"		=>"video/vnd.fvt",
			"mxu"		=>"video/vnd.mpegurl",
			"m4u"		=>"video/vnd.mpegurl",
			"pyv"		=>"video/vnd.ms-playready.media.pyv",
			"uvu"		=>"video/vnd.uvvu.mp4",
			"uvvu"		=>"video/vnd.uvvu.mp4",
			"viv"		=>"video/vnd.vivo",
			"webm"		=>"video/webm",
			"f4v"		=>"video/x-f4v",
			"fli"		=>"video/x-fli",
			"flv"		=>"video/x-flv",
			"m4v"		=>"video/x-m4v",
			"mkv"		=>"video/x-matroska",
			"mk3d"		=>"video/x-matroska",
			"mks"		=>"video/x-matroska",
			"mng"		=>"video/x-mng",
			"asf"		=>"video/x-ms-asf",
			"asx"		=>"video/x-ms-asf",
			"vob"		=>"video/x-ms-vob",
			"wm"		=>"video/x-ms-wm",
			"wmv"		=>"video/x-ms-wmv",
			"wmx"		=>"video/x-ms-wmx",
			"wvx"		=>"video/x-ms-wvx",
			"avi"		=>"video/x-msvideo",
			"movie"		=>"video/x-sgi-movie",
			"smv"		=>"video/x-smv",
			"ice"		=>"x-conference/x-cooltalk"
		);
		if (empty($content_types[$info['extension']])){return 'text/html; charset=UTF-8';}
		return $content_types[$info['extension']];
	}

#####################################################################
# 	TRANSFORMA UM NUMERO EM PORCENTAGEM
#####################################################################
	function pxToPct($elemento=100, $total=100){

		return (100 / $total) * $elemento;
		// return floor((100 / $total) * $elemento);

	}

#####################################################################
# 	PRINT_R COM PRE
#####################################################################
	function print_pre($str){
		echo "<pre>";
		print_r($str);
		echo "</pre>";
	}
#####################################################################
# 	DECODE JSON
#####################################################################
	function json_decode_nice($json, $assoc = TRUE){
	    $json = str_replace("\n","\\n",$json);
	    $json = str_replace("\r","",$json);
	    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
	    $json = preg_replace('/(,)\s*}$/','}',$json);
	    return json_decode($json,$assoc);
	}


##########################################################################################################
# EXCLUI DIRETÓRIO INTEIRO
##########################################################################################################
	function _excluiDir($Dir,$return=true){
		if(file_exists($Dir) && is_dir($Dir)){
		   if ($dd = opendir($Dir)) {
		        while (false !== ($Arq = readdir($dd))) {
		            if($Arq != "." && $Arq != ".."){
		                $Path = "$Dir/$Arq";
		                if(is_dir($Path)){
		                    _excluiDir($Path,false);
		                }elseif(is_file($Path)){
		                    unlink($Path);
		                }
		            }
		        }
		        closedir($dd);
		    }
		    rmdir($Dir);
		}else{
			return false;
		}
		if($return==true){
			return true;
		}
	}

############################################################################################################################
#	CRIA UMA SENHA ALEATÓRIA
############################################################################################################################
	function createPass($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
	    $senha = "";
	    // Definindo o tamanho 0 não precisará fazer nada
	    if ($tamanho == 0) {
		return  $senha;
	    }
	    // Define o maximo entre 0 até N caracters possíveis caracteres
	    $max = 25;
	    $max = $maiusculas ? 51 : $max;
	    $max = $numeros ? 61 : $max;
	    $max = $simbolos ? 68 : $max;
	    for ($n = 0; $n < $tamanho; $n++) {
		$int = rand(0, $max); 
		// 00 - 25 = abcdefghijklmnopqrstuvwxyz // ASCII = 97-122
		$int += (((-1 - $int) & ($int - 26)) >> 8) & 97;
		$int -= (((25 - $int) & ($int - 52)) >> 8) & 26;
		// 52 - 61 = 1234567890                 // ASCII = 48-57
		$int -= (((51 - $int) & ($int - 62)) >> 8) & 4;
		// 62 - 68 = !@#$%*-                      // ASCII = 33-33 | 64-64 | 35-37 | 42-42 | 45-45
		$int -= (((61 - $int) & ($int - 63)) >> 8) & 29;
		$int -= (((63 - $int) & ($int - 67)) >> 8) & 29;
		$int -= (((66 - $int) & ($int - 68)) >> 8) & 25;
		$int -= (((67 - $int) & ($int - 69)) >> 8) & 23;
		$int += (((62 - $int) & ($int - 64)) >> 8) & 1;
		// 26 - 51 = ABCDEFGHIJKLMNOPQRSTUVWXYZ // ASCII = 65-90
		$int += (((-1 - $int) & ($int - 26)) >> 8) & 65;
		$senha .= \pack('C', $int);
	    }
	    return $senha;
	}


##########################################################################################################
# FORMATA STRING PARA CONSULTAS DE LIKE 
##########################################################################################################
	function _likeString($str) {
		$str = trim(strtolower($str));
		while (strpos($str,"  "))
			$str 					= str_replace("  "," ",$str);
			$caracteresPerigosos 	= array ("Ã","ã","Õ","õ","á","Á","é","É","í","Í","ó","Ó","ú","Ú","ç","Ç","à","À","è","È","ì","Ì","ò","Ò","ù","Ù","ä","Ä","ë","Ë","ï","Ï","ö","Ö","ü","Ü","Â","Ê","Î","Ô","Û","â","ê","î","ô","û","!","?",",","“","”","-","\"","\\","/");
			$caracteresLimpos    	= array ("a","a","o","o","a","a","e","e","i","i","o","o","u","u","c","c","a","a","e","e","i","i","o","o","u","u","a","a","e","e","i","i","o","o","u","u","A","E","I","O","U","a","e","i","o","u",".",".",".",".",".",".","." ,"." ,".");
			$str 					= str_replace($caracteresPerigosos,$caracteresLimpos,$str);
			$caractresSimples 		= array("a","e","i","o","u","c");
			$caractresEnvelopados 	= array("[a]","[e]","[i]","[o]","[u]","[c]");
			$str 					= str_replace($caractresSimples,$caractresEnvelopados,$str);
			$caracteresParaRegExp 	= array(
				"(a|ã|á|à|ä|â|&atilde;|&aacute;|&agrave;|&auml;|&acirc;|Ã|Á|À|Ä|Â|&Atilde;|&Aacute;|&Agrave;|&Auml;|&Acirc;)",
				"(e|é|è|ë|ê|&eacute;|&egrave;|&euml;|&ecirc;|É|È|Ë|Ê|&Eacute;|&Egrave;|&Euml;|&Ecirc;)",
				"(i|í|ì|ï|î|&iacute;|&igrave;|&iuml;|&icirc;|Í|Ì|Ï|Î|&Iacute;|&Igrave;|&Iuml;|&Icirc;)",
				"(o|õ|ó|ò|ö|ô|&otilde;|&oacute;|&ograve;|&ouml;|&ocirc;|Õ|Ó|Ò|Ö|Ô|&Otilde;|&Oacute;|&Ograve;|&Ouml;|&Ocirc;)",
				"(u|ú|ù|ü|û|&uacute;|&ugrave;|&uuml;|&ucirc;|Ú|Ù|Ü|Û|&Uacute;|&Ugrave;|&Uuml;|&Ucirc;)",
				"(c|ç|Ç|&ccedil;|&Ccedil;)" );
			$str = str_replace($caractresEnvelopados,$caracteresParaRegExp,$str);
			$str = utf8_decode(str_replace(" ",".*",$str));
			return $str;
	}

##########################################################################################################
# EXTRAI AS VARIÁVEIS
##########################################################################################################
	function _extract(){	
		if(extract($_REQUEST)){
			return true;
		}else{
			return false;
		}
	};

##########################################################################################################
# EXECUTA UMA FUNÇÃO PRÉ DEFINIDA
##########################################################################################################
	function _exec($fn){	
		###################################
		# listamos as funções criadas
		###################################
		$whitelist = get_defined_functions();
		if(in_array(strtolower($fn),$whitelist['user'])){
			if(_extract() && !empty($_REQUEST['function']) && !empty($fn) && function_exists($fn) ) call_user_func($fn);
		}
	}

##########################################################################################################
# CRIA UMA SENHA ALEATÓRIA
##########################################################################################################
	function _crypt($len = 32) {
		return substr(bin2hex(random_bytes(500)),0,$len);
	}

##########################################################################################################
# CRIA UMA SENHA ALEATÓRIA COM ASH
##########################################################################################################
	function _codePass($senha,$ash="aquiPODEserQUALQUERcoisaPOISéUMhash") 	{	
		$salt 		= md5($ash);
		$codifica 	= crypt($senha,$salt);
		$codifica 	= hash('sha512',$codifica);
		return $codifica;
	}

##########################################################################################################
# RETORNA STRING DE ERRO AO USUÁRIO
##########################################################################################################
   function _erro($error) {
        echo '<pre style="position: relative;color: #17968a;background-color: #c4e4e2;font-weight: bold;padding: 10px;text-align: center;font-size: 17px;">'.PHP_EOL.
				'! -- Internal WS Error -- ! '.PHP_EOL. 
				$error.PHP_EOL.
			"</pre>";
   }
##########################################################################################################
# SIMULA decodeURIcomponent DO JAVASCRIPT
##########################################################################################################
	function decodeURIcomponent($smth="")			{
		$smth = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($smth)); 
		$smth = str_replace(array("<",">"),array("&lt;","&gt;"),html_entity_decode($smth,null,'UTF-8'));
		return $smth ;
	}

##########################################################################################################
# SIMULA encodeURIComponent DO JAVASCRIPT POR CARACTERE ISOLADO
##########################################################################################################
	function encodeURIComponentbycharacter($char) {
		$array = array("+"=> "%20", "%21"=> "!", "%27"=> '"', "%28"=> "(", "%29"=> ")", "%2A"=> "*", "%7E"=> "~", "%80"=> "%E2%82%AC", "%81"=> "%C2%81", "%82"=> "%E2%80%9A", "%83"=> "%C6%92", "%84"=> "%E2%80%9E", "%85"=> "%E2%80%A6", "%86"=> "%E2%80%A0", "%87"=> "%E2%80%A1", "%88"=> "%CB%86", "%89"=> "%E2%80%B0", "%8A"=> "%C5%A0", "%8B"=> "%E2%80%B9", "%8C"=> "%C5%92", "%8D"=> "%C2%8D", "%8E"=> "%C5%BD", "%8F"=> "%C2%8F", "%90"=> "%C2%90", "%91"=> "%E2%80%98", "%92"=> "%E2%80%99", "%93"=> "%E2%80%9C", "%94"=> "%E2%80%9D", "%95"=> "%E2%80%A2", "%96"=> "%E2%80%93", "%97"=> "%E2%80%94", "%98"=> "%CB%9C", "%99"=> "%E2%84%A2", "%9A"=> "%C5%A1", "%9B"=> "%E2%80%BA", "%9C"=> "%C5%93", "%9D"=> "%C2%9D", "%9E"=> "%C5%BE", "%9F"=> "%C5%B8", "%A0"=> "%C2%A0", "%A1"=> "%C2%A1", "%A2"=> "%C2%A2", "%A3"=> "%C2%A3", "%A4"=> "%C2%A4", "%A5"=> "%C2%A5", "%A6"=> "%C2%A6", "%A7"=> "%C2%A7", "%A8"=> "%C2%A8", "%A9"=> "%C2%A9", "%AA"=> "%C2%AA", "%AB"=> "%C2%AB", "%AC"=> "%C2%AC", "%AD"=> "%C2%AD", "%AE"=> "%C2%AE", "%AF"=> "%C2%AF", "%B0"=> "%C2%B0", "%B1"=> "%C2%B1", "%B2"=> "%C2%B2", "%B3"=> "%C2%B3", "%B4"=> "%C2%B4", "%B5"=> "%C2%B5", "%B6"=> "%C2%B6", "%B7"=> "%C2%B7", "%B8"=> "%C2%B8", "%B9"=> "%C2%B9", "%BA"=> "%C2%BA", "%BB"=> "%C2%BB", "%BC"=> "%C2%BC", "%BD"=> "%C2%BD", "%BE"=> "%C2%BE", "%BF"=> "%C2%BF", "%C0"=> "%C3%80", "%C1"=> "%C3%81", "%C2"=> "%C3%82", "%C3"=> "%C3%83", "%C4"=> "%C3%84", "%C5"=> "%C3%85", "%C6"=> "%C3%86", "%C7"=> "%C3%87", "%C8"=> "%C3%88", "%C9"=> "%C3%89", "%CA"=> "%C3%8A", "%CB"=> "%C3%8B", "%CC"=> "%C3%8C", "%CD"=> "%C3%8D", "%CE"=> "%C3%8E", "%CF"=> "%C3%8F", "%D0"=> "%C3%90", "%D1"=> "%C3%91", "%D2"=> "%C3%92", "%D3"=> "%C3%93", "%D4"=> "%C3%94", "%D5"=> "%C3%95", "%D6"=> "%C3%96", "%D7"=> "%C3%97", "%D8"=> "%C3%98", "%D9"=> "%C3%99", "%DA"=> "%C3%9A", "%DB"=> "%C3%9B", "%DC"=> "%C3%9C", "%DD"=> "%C3%9D", "%DE"=> "%C3%9E", "%DF"=> "%C3%9F", "%E0"=> "%C3%A0", "%E1"=> "%C3%A1", "%E2"=> "%C3%A2", "%E3"=> "%C3%A3", "%E4"=> "%C3%A4", "%E5"=> "%C3%A5", "%E6"=> "%C3%A6", "%E7"=> "%C3%A7", "%E8"=> "%C3%A8", "%E9"=> "%C3%A9", "%EA"=> "%C3%AA", "%EB"=> "%C3%AB", "%EC"=> "%C3%AC", "%ED"=> "%C3%AD", "%EE"=> "%C3%AE", "%EF"=> "%C3%AF", "%F0"=> "%C3%B0", "%F1"=> "%C3%B1", "%F2"=> "%C3%B2", "%F3"=> "%C3%B3", "%F4"=> "%C3%B4", "%F5"=> "%C3%B5", "%F6"=> "%C3%B6", "%F7"=> "%C3%B7", "%F8"=> "%C3%B8", "%F9"=> "%C3%B9", "%FA"=> "%C3%BA", "%FB"=> "%C3%BB", "%FC"=> "%C3%BC", "%FD"=> "%C3%BD", "%FE"=> "%C3%BE", "%FF"=> "%C3%BF");
		if (array_key_exists($char, $array)) {
			return $array[$char];
		}else{
			return null;
		}
	}
###################################################################################
# LIMITA TEXTOS OU FRASES POR QUANTIDADE DE PALAVRAS
###################################################################################
	function limit_words($texto = null, $limite = 0, $end = "...") {
		$texto = explode(' ', ($texto));
		$texto = array_slice($texto, 0, $limite);
		return implode($texto, " ") . $end;
	}
###################################################################################
# RETORNA O PROTOCOLO DA URL
###################################################################################		
	function protocolURL() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol;
	}
###################################################################################
# RETORNA O NÚMERO EM FORMATADO FINANCEIRO 
###################################################################################
	function formatMoney($number, $fractional = false) {
		if ($fractional) {
			$number = sprintf('%.2f', $number);
		}
		while (true) {
			$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
			if ($replaced != $number) {
				$number = $replaced;
			} else {
				break;
			}
		}
		return $number;
	}

##########################################################################################################
# SIMULA encodeURIComponent DO JAVASCRIPT
##########################################################################################################
	function encodeURIComponent($string){
		$result = "";
		for ($i = 0; $i < strlen($string); $i++) {
			$result .= encodeURIComponentbycharacter(urlencode($string[$i]));
		}
		return $result;
	}

##########################################################################################################
# TRANSFORMA QUOTE EM ENTITIES
##########################################################################################################
	function quote2entities($string,$entities_type='number'){
	    $search                     = array("\"","'");
	    $replace_by_entities_name   = array("&quot;","&apos;");
	    $replace_by_entities_number = array("&#34;","&#39;");
	    $do = null;
	    if ($entities_type == 'number'){
	    	$do = str_replace($search,$replace_by_entities_number,$string);
	    }else if ($entities_type == 'name'){
	    	$do = str_replace($search,$replace_by_entities_name,$string);
	    }else{
	    	$do = addslashes($string);
	    }
	    return $do;
	}

##########################################################################################################
# GRAVA O ARQUIVO EM UMA CONSTANTE
##########################################################################################################
	function _define_page_($var, $page){
		ob_start();
		require_once $page; 
		define($var, ob_get_clean());
		ob_end_flush();
	}

##########################################################################################################
# CRIPTOGRAFA UMA STRING COM SENHA
##########################################################################################################
	function _encripta( $plaintext, $key){	
		$ivlen 			= openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv 			= openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac 			= hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		return base64_encode($iv.$hmac.$ciphertext_raw);
	}


##########################################################################################################
# ALGUNS SERVIDORES ESTÁ DESABILITADO ESSA FUNÇÃO NATIVA, ENTÃO REDESENHAMOS ELA
##########################################################################################################
	if(!function_exists('hash_equals')){
	    function hash_equals($str1, $str2){
	        if(strlen($str1) != strlen($str2)){
	            return false;
	        }else{
	            $res = $str1 ^ $str2;
	            $ret = 0;
	            for($i = strlen($res) - 1; $i >= 0; $i--){
	                $ret |= ord($res[$i]);
	            }
	            return !$ret;
	        }
	    }
	}

##########################################################################################################
# ALGUNS SERVIDORES ESTÁ DESABILITADO ESSA FUNÇÃO NATIVA, ENTÃO REDESENHAMOS ELA
##########################################################################################################
	if(!function_exists('glob_recursive')){
	    function glob_recursive($pattern, $flags = 0){
			$files = glob($pattern, $flags);
			foreach(glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
			{
				$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
			}
			return $files;
		}
	}

##########################################################################################################
# DESCRIPTOGRAFA UMA STRING COM SENHA
##########################################################################################################
	function _decripta( $ciphertext, $key) {	
		$c 					= base64_decode($ciphertext);
		$ivlen 				= openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv 				= substr($c, 0, $ivlen);
		$hmac 				= substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw		= substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac 			= hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		if (hash_equals($hmac, $calcmac)){
		    return $original_plaintext;
		}else{
		    return false;
		}
	}

##########################################################################################################
# RETORNA O PESO DE UM ARQUIVO FORMATADO
##########################################################################################################
	function _filesize($file, $size="M", $decimals=2, $dec_sep='.', $thousands_sep=','){
		if(file_exists($file)){
			$bytes = filesize($file);
		}elseif(is_numeric($file) || is_int($file)){
			$bytes = $file;
		}else{
			$bytes = 0;
		}
		if($bytes<1024){$size="B";}
		elseif($bytes<(1024*1024)){$size="K";}
		elseif($bytes<(1024*1024*1024)){$size="M";}
		elseif($bytes<(1024*1024*1024*1024)){$size="G";}
		elseif($bytes<(1024*1024*1024*1024*1024)){$size="T";}
		elseif($bytes<(1024*1024*1024*1024*1024*1024)){$size="P";}
		$sizes = 'BKMGTP';
		if (isset($size)){
			$factor = strpos($sizes, $size[0]);
		} else {
			$factor = floor((strlen($bytes) - 1) / 3);
			$size = $sizes[$factor];
		}
		return number_format($bytes/pow(1024, $factor), $decimals, $dec_sep, $thousands_sep).' '.$size;
	}

##########################################################################################################
# TRANSFORMA O NOME DE UM ARQUIVO EM URL AMIGÁVEL
##########################################################################################################
	function url_amigavel_filename($texto){
		$array1 = array("{","}","[","]","´","&",",","/"," ","á","à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
		$array2 = array("","","","","","e","","-","_","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" );
		return strtolower(str_replace( $array1, $array2, strtolower($texto)));
	}
##########################################################################################################
# TRANSFORMA STRING EM URL AMIGÁVEL
##########################################################################################################
	function url_amigavel($texto,$isso="",$porisso="") 					{
		$array1 = array("´","&",",",".","/"," ", "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
		$array2 = array("","e","", "","-","+","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c");
		$tratamento=strtolower(str_replace( $isso, $porisso, strtolower($texto)));
		return strtolower(str_replace( $array1, $array2, $tratamento));
	}

##########################################################################################################
# RETIRA ACENTOS DE UMA STRING
##########################################################################################################
	function retira_acentos($str,$espaco="")		{
		return strtr(
			utf8_decode($str),
			utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ '),
			'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'.$espaco
		);
	}

##########################################################################################################
# TRANSFORMA UMA STRING EM BINÁRIO
##########################################################################################################
	function _str_to_bin($text){
		$tm = strlen($text);
		$x = 0;
		for($i = 1;$i<=$tm;$i++){
			$letra[$i] = substr($texto,$x,1);
			$cod[$i] = ord($letra[$i]);
			$bin[$i] = str_pad(decbin($cod[$i]), 8, "0", STR_PAD_LEFT);
			$x++;
		}
		$a= 1;
		$binario = array();
		for($i = 1;$i <= $tm;$i++){
			if($a == 16) {
				$binario[]=$bin[$i]." ".PHP_EOL;
				$a=0;
			}else{
				$binario[]=$bin[$i]." ";
			}
			$a++;
		}
		return implode($binario,"");
	}

##########################################################################################################
# RETORNA O NEGATIVO DE UMA COR HEX
##########################################################################################################
	function color_inverse($color){
		$color = str_replace('#', '', $color);
		if (strlen($color) != 6){ 
			return '000000'; 
		}
		$rgb = '';
		for ($x=0;$x<3;$x++){
			$c = 255 - hexdec(substr($color,(2*$x),2));$c = ($c < 0) ? 0 : dechex($c);
			$rgb .= (strlen($c) < 2) ? '0'.$c : $c;
		}
		return '#'.$rgb;
	}

##########################################################################################################
# VERIFICA SE UM ARQUIVO EXTERNO EXISTE
##########################################################################################################
	function remoteFileExists($url){
		$curl = @curl_init($url);
		@curl_setopt($curl, CURLOPT_NOBODY, true);
		$result = @curl_exec($curl);$ret = false;
		if ($result !== false) {
			$statusCode = @curl_getinfo($curl, CURLINFO_HTTP_CODE);
			@curl_close($curl);
			if ($statusCode == 200) {
				return true;
			}else{
				return false;
			}
		}else{
			@curl_close($curl);
			return false;
		}
	}

#####################################################################
# 	COMPRIME OS ARQUIVOS DO SITE PARA GZIP
#####################################################################
	function gzipWebsite(){
		$ftp_files = rsearch(INCLUDE_PATH.'admin/',"/^.*\.(js|css)$/");
		foreach ($ftp_files as $key => $value) {
			$gzfile 		= $value . '.gz';
			$contentNormal 	= file_get_contents($value);
			$contentGzip 	= gzencode($contentNormal,9);
			file_put_contents($gzfile,$contentGzip);
			unlink($value);
		}
	}

#####################################################################
# 	ORGANIZA PATH E URLS
#####################################################################
		function normalizePath($path) {
			$parts    = array();
			$path     = str_replace('\\', '/', $path);
			$path     = preg_replace('/\/+/', '/', $path);
			$segments = explode('/', $path);
			$test     = '';
			foreach ($segments as $segment) {
				if ($segment != '.') {
					$test = array_pop($parts);
					if (is_null($test))
						$parts[] = $segment;
					else if ($segment == '..') {
						if ($test == '..')
							$parts[] = $test;
						
						if ($test == '..' || $test == '')
							$parts[] = $segment;
					} else {
						$parts[] = $test;
						$parts[] = $segment;
					}
				}
			}
			return implode('/', $parts);
		}

###################################################################################
# COMPARADOR DE VERSÕES
###################################################################################
		function compare_version($ver1, $ver2, $operator = null) {
			    $p = '#(\.0+)+($|-)#';
			    $ver1 = preg_replace($p, '', $ver1);
			    $ver2 = preg_replace($p, '', $ver2);
			    return isset($operator) ?  version_compare($ver1, $ver2, $operator) :  version_compare($ver1, $ver2);
		}
