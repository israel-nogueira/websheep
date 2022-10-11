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

	class htmlProcess{
		public function __construct(){}
		public static function process_tag_ws_paginate	($key=null){
			$outertext 	= self::beaultyHTML($key->outertext());
			$innertext 	= self::beaultyHTML($key->innertext());
			$html 		= str_get_html($outertext);
			$atributos 	= $html->root->children[0]->attr;
			if(
				(empty($atributos['slug'])		|| $atributos['slug']=="") 		|| 
				(empty($atributos['type'])		|| $atributos['type']=="") 		|| 
				(empty($atributos['max'])		|| $atributos['max']=="")		|| 
				(empty($atributos['atual'])		|| $atributos['atual']=="")		|| 
				(empty($atributos['number'])	|| $atributos['number']=="") 	|| 
				(empty($atributos['active'])	|| $atributos['active']=="")){
					return 	_erro('Preencha todas as datas da TAG:<br>'.htmlentities('<ws-paginate slug="" type="" max="" atual="" prev="" next="" number="" active=""></ws-paginate>'));
			}else{
				$proibido 		= array('max','atual','html','number','active');
				$classReturn 	= array();
				$classReturn[] 	= '$Tool=new WS();$result=$Tool';
				foreach ($atributos as $key => $value) {
					#caso queira retornar algo pelo PHP 
					if(substr($value,0,6)=="return" ){ $value = eval(trim($value.';'));}
					if(!in_array($key,$proibido)){
						$classReturn[] = (is_numeric($value)) ? $key.'('.$value.')' : $key.'("'.$value.'")';
					}
				}
				$classReturn[] = 'go();';
				eval(implode($classReturn, '->'));
				$_max_posts_   		= $atributos['max'];
				if(strpos($atributos['atual'],":")){
					$newAttr = explode(':',$atributos['atual']);
					if(is_array($newAttr) && $newAttr[0]=="url" && isset($newAttr[1]) && is_numeric($newAttr[1])){
							$atributos['atual']= ws::urlPath((int)$newAttr[1]);					
					}
				}
				$_atual_page_  		= (substr($atributos['atual'],0,6)=="return") ? eval(trim($atributos['atual'].';')) : $atributos['atual'];
				$_total_posts_ 		= $result->_num_rows;
				$_total_paginas_	= ceil($_total_posts_ / $_max_posts_); 
				$data_html 			= $atributos['html'];
				$numbers 			= "";
				for($i=1; $i<=$_total_paginas_;++$i){
					if($i==$_atual_page_){
						$numbers .= str_replace(array('{{i}}','{{pages}}'),array($i,'!ERRO!'), $atributos['active']);
					}else{
						$numbers .= str_replace(array('{{i}}','{{pages}}'),array($i,'!ERRO!'), $atributos['number']);
					}
				}
				if($_atual_page_==1){
					if($_total_paginas_ > $_atual_page_){
						$data_html = str_replace(array("{{next}}",'{{i}}')	,	 array(($_atual_page_+1),'!ERRO!'), 	$data_html);
						$data_html = preg_replace("/{{lastClass:(.*)}}/isU",'', $data_html);
					}else{
						$data_html = preg_replace("/{{lastClass:(.*)}}/isU",'$1', $data_html);
						$data_html = str_replace(array("{{next}}",'{{i}}')	,	 array(($_atual_page_),'!ERRO!'), 	$data_html);
					}

					$data_html = preg_replace("/{{firstClass:(.*)}}/isU",'$1', $data_html);
					$data_html = str_replace(array("{{prev}}",'{{i}}')	,	 array('1','!ERRO!','',''), $data_html);
				}elseif($_atual_page_==$_total_paginas_){
					$data_html = preg_replace("/{{lastClass:(.*)}}/isU",'$1', $data_html);
					$data_html = preg_replace("/{{firstClass:(.*)}}/isU",'', $data_html);
					$data_html = str_replace(array("{{prev}}",'{{i}}')	,	 array(($_atual_page_-1),'!ERRO!'), 	$data_html);
					$data_html = str_replace(array("{{next}}",'{{i}}')	,	 array($_total_paginas_, '!ERRO!'), 	$data_html);
				}else{
					$data_html = preg_replace("/{{firstClass:(.*)}}/isU",'', $data_html);
					$data_html = preg_replace("/{{lastClass:(.*)}}/isU",'', $data_html);
					$data_html = str_replace(array("{{prev}}",'{{i}}')	,	 array(($_atual_page_-1),'!ERRO!'), 	$data_html);
					$data_html = str_replace(array("{{next}}",'{{i}}')	,	 array(($_atual_page_+1),'!ERRO!'), 	$data_html);
				}
				$data_html = str_replace("{{first}}"	,	  "1"				, 	$data_html);
				$data_html = str_replace("{{last}}"		,	   $_total_paginas_	, 	$data_html);
				$data_html = str_replace("{{pages}}"	,	   $numbers			, 	$data_html);
				$urlsSetadas = substr($data_html,strpos($data_html,'{{url:')+6);
				$urlsSetadas = explode("{{url:",$urlsSetadas);
				foreach ($urlsSetadas as $key => $urlsSet) {
					$urlsSet = explode("}}",$urlsSet);
					$data_html = str_replace('{{url:'.$urlsSet[0].'}}',ws::urlPath((int)$urlsSet[0]), $data_html);
				}
				return $data_html;
			}
		}
		public static function MetaTagStringProcess 	($string){
				$string 			= str_replace(array('DOMAIN','DOMINIO'),DOMINIO,$string);
				$titulo_page_root 	= self::getSetupData();
				$titulo_page_root 	= $titulo_page_root['title_root'];
				$string 			= str_replace('',$titulo_page_root,$string);
				$string 			= str_replace('{url}',ws::urlPath(),$string);



				if(@strpos('{',$string)!=-1 && @strpos('}',$string)!=-1){
					$a = explode("{",$string);
					$isso 	= array();
					$porisso 	= array();
					foreach ($a as $str){
						if(stripos($str,'}')){
							$d 		= explode("}",$str);
							$ws 		= $d[0];
							$ws 		= explode(',', $ws);
							############################################
							if(count($ws)==3){	
								$slug 	= $ws[0];
								$type 	= $ws[1];
								$colum 	= $ws[2];
								$lenght	= null;
								$Tool= new WS(); 
								if(strpos($colum,'::length:')){
									$lexplode 	= explode('::length:', $colum);
									$colum 	    = $lexplode[0];
									$lenght 	= $lexplode[1];
								}
								if($lenght!= null){
									$porisso[] = substr(str_replace(array('&nbsp;',PHP_EOL,"\n","\r"),'_ws_php_eol_',strip_tags($Tool->slug($slug)->limit(1)->type($type)->go()->result[$colum])),0,$lenght);
									}else{
									$porisso[] = str_replace(array('&nbsp;',PHP_EOL,"\n","\r"),'_ws_php_eol_',strip_tags($Tool->slug($slug)->limit(1)->type($type)->go()->result[$colum]));
								}
							}elseif(count($ws)==4){	
								$slug 	= $ws[0];
								$type 	= $ws[1];
								$colum 	= $ws[2];
								$where 	= $ws[3];
								$lenght	= null;
								if(strpos($colum,'::length:')){
									$lexplode 	= explode('::length:', $colum);
									$colum 		= $lexplode[0];
									$lenght 	= $lexplode[1];
								}
								if(strpos($where,'url:')){
									$url = explode('url:', $where);
									$path = ws::urlPath((int)$url[1]);
									$where = $url[0].'"'.$path.'"';
								}
								$Tool= new WS(); 
								if($lenght!= null){
									$porisso[] = substr(str_replace(array('&nbsp;',PHP_EOL,"\n","\r"),'_ws_php_eol_',strip_tags($Tool->slug($slug)->where($where)->limit(1)->type($type)->go()->result[0][$colum])),0,$lenght);
								}else{
									$porisso[] = str_replace(array('&nbsp;',PHP_EOL,"\n","\r"),'_ws_php_eol_',strip_tags($Tool->slug($slug)->where($where)->limit(1)->type($type)->go()->result[0][$colum]));
								}
							}else{	
								$porisso[] = ws::GetDebugError(debug_backtrace(),"Variável inválida: Use {slug,type,colum,where}");
							}
							$isso[] 		= '{'.$d[0].'}';
						}
					}
					return str_replace($isso,$porisso,$string);
				}else{
					return $string;
				}
		}
		public static function process_tag_ws_metatags	($key=null){
			$metaTag 	= "";
			$URL_BROWSER = explode('/',ws::urlPath());
			$insert_categorias=new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES.'ws_pages');
			$insert_categorias->set_where('path<>"" AND type="path"');
			$insert_categorias->select();

			// VERIFICO QUAL É A PÁGINA QUE ESTA NO BROWSER E SEPARO NO $id_page;
			foreach ($insert_categorias->fetch_array as $value) {
				$ID_URL			= $value['id'];
				$URL_BD 		= explode('/',$value['path']);
				$response 		= 'true';
				$i 				= 0;
				if(count($URL_BROWSER)==count($URL_BD)){
					$verify 		= array();
					foreach ($URL_BROWSER as $valBrowser) {
						$PATH 		= $valBrowser;
						$PATHBD 	= $URL_BD[$i];
						$igual		= (substr($PATHBD,0,1)!='^' && !strpos($PATHBD,'|') && $PATHBD !='*' && $PATHBD==$valBrowser);
						$diferente 	= (substr($PATHBD,0,1)=='^' && substr($PATHBD,1)!=$valBrowser);
						$qqr_coisa	= $PATHBD=='*';
						$ou			= strpos($PATHBD,'|');
						$opts		= explode('|',$URL_BD[$i]);
						$ou			= $ou && in_array($valBrowser, $opts);
						if(!$igual && !$diferente && !$qqr_coisa && !$ou){$verify[]=0;}else{$verify[]=1;}
						$i++;
					}
					if(!in_array(0, $verify)){ $id_page = $value['id'];break;}
				}
			}
			// caso não tenha página cadastrada, puxa o default
			$titulo_page_root 	=	self::getSetupData();
			$titulo_page_root 	=	$titulo_page_root['title_root'];

			if(@empty($id_page)){
				$metaTag .= '<title>'.self::MetaTagStringProcess($titulo_page_root)."</title>";
				$Meta_Defaul=new MySQL();
				$Meta_Defaul->set_table(PREFIX_TABLES.'meta_tags');
				$Meta_Defaul->set_where('id_page="0"');
				$Meta_Defaul->select();
				foreach ($Meta_Defaul->fetch_array as $value) {
					$tag 			= $value['tag'];
					$type 			= $value['type'];
					$typeContent 	= $value['type_content'];
					$content 		= $value['content'];
					$href			= $value['href'];
					$sizes 			= $value['sizes'];
					$title 			= $value['title'];
					$media 			= $value['media'];
										$metaTag .= '<meta '.$type.'="'.$typeContent.'"';
					if($content!=""){	$metaTag .=' content="'.self::MetaTagStringProcess($content).'" ';	}
					if($href!=""){		$metaTag .=' href="'.self::MetaTagStringProcess($href).'" ';			}
					if($sizes!=""){		$metaTag .=' sizes="'.self::MetaTagStringProcess($sizes).'" ';		}
					if($title!=""){		$metaTag .=' title="'.self::MetaTagStringProcess($title).'" ';		}
					if($media!=""){		$metaTag.='StringProcess media="'.self::MetaTagStringProcess($media).'" ';		}
					$metaTag .="/>".PHP_EOL;
				}
			}else{
				$titulo_page=new MySQL();
				$titulo_page->set_table(PREFIX_TABLES.'ws_pages');
				$titulo_page->set_where(PREFIX_TABLES.'ws_pages.id="'.$id_page.'"');
				$titulo_page->Select();
				$titPage = $titulo_page->fetch_array[0]['title_page'];
				if($titPage==""){
					$metaTag .= '<title> '.self::MetaTagStringProcess($titulo_page_root)."</title>";	
				}else{
					$metaTag .= '<title> '.self::MetaTagStringProcess($titPage)."</title>";
				}

				// verifica agora se tem as tags default cadastradas, se não tiver ele coloca... 
				$Meta_Defaul=new MySQL();
				$Meta_Defaul->set_table(PREFIX_TABLES.'meta_tags');
				$Meta_Defaul->set_where('id_page="0"');
				$Meta_Defaul->select();
				foreach ($Meta_Defaul->fetch_array as $value) {
					$verifyMeta=new MySQL();
					$verifyMeta->set_table(PREFIX_TABLES.'meta_tags');
					$verifyMeta->set_where('id_page="'.$id_page.'"');
					$verifyMeta->set_where('AND tag="'.$value['tag'].'"');
					$verifyMeta->set_where('AND type="'.$value['type'].'"');
					$verifyMeta->set_where('AND type_content="'.$value['type_content'].'"');
					$verifyMeta->select();
					if($verifyMeta->_num_rows ==0){
						$tag 					= 	$value['tag'];
						$type 					= 	$value['type'];
						$typeContent 			= 	$value['type_content'];
						$content 				= 	$value['content'];
						$href					= 	$value['href'];
						$sizes 					= 	$value['sizes'];
						$title 					= 	$value['title'];
						$media 					= 	$value['media'];
													$metaTag .= '<meta '.$type.'="'.$typeContent.'"';
						if($content!=""){			$metaTag .=' content="'.self::MetaTagStringProcess($content).'" ';}
						if($href!=""){				$metaTag .=' href="'.self::MetaTagStringProcess($href).'" ';}
						if($sizes!=""){				$metaTag .=' sizes="'.self::MetaTagStringProcess($sizes).'" ';}
						if($title!=""){				$metaTag .=' title="'.self::MetaTagStringProcess($title).'" ';}
						if($media!=""){				$me.='StringProcess media="'.self::MetaTagStringProcess($media).'" ';}
						$metaTag .="/>".PHP_EOL;
					};
				}
				// AGORA FAZ O WHILLE NAS TAGS DA PÁGINA
				$MetasPage=new MySQL();
				$MetasPage->set_table(PREFIX_TABLES.'meta_tags');
				$MetasPage->set_where('id_page="'.$id_page.'"');
				$MetasPage->select();
				foreach ($MetasPage->fetch_array as $value) {
					$tag 			= $value['tag'];
					$type 			= $value['type'];
					$typeContent 	= $value['type_content'];
					$content 		= $value['content'];
					$href			= $value['href'];
					$sizes 			= $value['sizes'];
					$title 			= $value['title'];
					$media 			= $value['media'];
										$metaTag .= '<meta '.$tag.' '.$type.'="'.$typeContent.'"';

					if($content!=""){	$metaTag .=' content="'.			self::MetaTagStringProcess($content).'" ';}
					if($href!=""){		$metaTag .=' href="'.				self::MetaTagStringProcess($href).'" ';}
					if($sizes!=""){		$metaTag .=' sizes="'.				self::MetaTagStringProcess($sizes).'" ';}
					if($title!=""){		$metaTag .=' title="'.				self::MetaTagStringProcess($title).'" ';}
					if($media!=""){		$metaTag.='StringProcess media="'.	self::MetaTagStringProcess($media).'" ';}
					$metaTag .="/>".PHP_EOL;
				}
			}
			return $metaTag;
		}
		public static function process_ws_php($key=null){
			$outertext 		= $key->outertext();
			$innertext 		= $key->innertext();
			$atributos 		= $key->attr;
			$content 		= ws::execCode('<? '.self::beaultyHTML($innertext).' ?>',true);
			return $content;
		}
		public static function process_ws_shortcode($key=null){
			$outertext 		= $key->outertext();
			$innertext 		= $key->innertext();
			$atributos 		= $key->attr;
			$ws 			= (object)array();
			$ws->attr 		= (object)$atributos;
			$ws->innertext 	= $innertext;
			unset($ws->attr->function);
			if(isset($atributos['function'])){
				$file = INCLUDE_PATH.'ws-shortcodes/'.$atributos['function'].'.php';
				if(!file_exists($file)){
					$wsReturn = "<pre>! shortcodes não existe - ".$atributos['function'].".php</pre>";
				}else{
					ob_start();
					include($file);
					$wsReturn = ob_get_contents();
					ob_end_clean();
				}
			}else{
				$wsReturn = _erro(ws::GetDebugError(debug_backtrace(),'Por favor, insira o atributo function="" na tag [ws-shortcode]'));
			};
			return $wsReturn;
		}
		public static function process_tag_ws_no_result	($key=null){
			$outertext = $key->outertext();
			$innertext = $key->innertext();
			$atributos = $key->attr;
			return $outertext;
		}
		public static function getSetupData(){
			$setupdata = new MySQL();
			$setupdata->set_table(PREFIX_TABLES.'setupdata');
			$setupdata->set_order('id','DESC');
			$setupdata->set_limit(1);
			$setupdata->select();
			return $setupdata->fetch_array[0];
		}
		public static function process_tag_ws_scripts($key=null){
			if(ws::urlPath()==""){ 
				$urlIncludes=self::getSetupData(); 
				$urlIncludes=$urlIncludes['url_initPath']; 
			}else{ $urlIncludes = ws::urlPath(); }
			$controller  = new controller();
			$urlRealPath = $controller->returnRealPath();
			$get_ID_page = new MySQL();
			$get_ID_page->set_table(PREFIX_TABLES.'ws_pages');
			$get_ID_page->set_colum('id');
			$get_ID_page->set_where('path="'.$urlRealPath.'"');
			$get_ID_page->select();
			$get_ID_page = $get_ID_page->fetch_array[0]['id'];
			$insert_categorias=new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES.'ws_link_url_file');
			$insert_categorias->set_where('id_url="'.$get_ID_page.'"');
			$insert_categorias->set_where('AND ext="js"');
			$insert_categorias->set_order('position',"ASC");
			$insert_categorias->select();
			$csss = Array();
			foreach($insert_categorias->fetch_array as $js){
				$id 	= ($js['include_id']=="")?'':' id="'.$js['include_id'].'"';
				$jss[] = '<script type="text/javascript" src="'.$js['file'].'"'.$id.'></script>';
			};

			return (count(@$jss)>=1) ? implode($jss,PHP_EOL) : "";
		}
		public static function process_tag_ws_style($key=null){
			if(ws::urlPath()==""){ 
				$urlIncludes=self::getSetupData(); 
				$urlIncludes=$urlIncludes['url_initPath']; 
			}else{ $urlIncludes = ws::urlPath(); }
			$controller  = new controller();
			$urlRealPath = $controller->returnRealPath();
			$get_ID_page = new MySQL();
			$get_ID_page->set_table(PREFIX_TABLES.'ws_pages');
			$get_ID_page->set_colum('id');
			$get_ID_page->set_where('path="'.$urlRealPath.'"');
			$get_ID_page->select();
			$get_ID_page = $get_ID_page->fetch_array[0]['id'];
			$insert_categorias=new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES.'ws_link_url_file');
			$insert_categorias->set_where('id_url="'.$get_ID_page.'"');
			$insert_categorias->set_where('AND ext="css"');
			$insert_categorias->set_order('position',"ASC");
			$insert_categorias->select();
			$csss = Array();
			foreach($insert_categorias->fetch_array as $css){
				$id 	= ($css['include_id']=="") 		? '' : ' id="'.$css['include_id'].'"';
				$media 	= ($css['include_media']=="") 	? '' : ' media="'.$css['include_media'].'"';
				$csss[] = '<link rel="stylesheet" type="text/css" href="'.$css['file'].'"'.$id.''.$media.' >';
			};
			return implode($csss,PHP_EOL);
		}
		public static function returnNoResult($key=null){
				$Newkey 		= str_get_html($key);
				$Newkey 		= $Newkey->find('ws-no-result',0);
				if(count($Newkey)>0){
					$innertext 		= $Newkey->innertext();
					return $innertext;
				}
		}
		public static function clearNoResult($template=null){
			$Newkey 		= str_get_html($template);
			if(count($Newkey->find('ws-no-result'))>0){
				foreach ($Newkey->find('ws-no-result') as $key){ 	$key->outertext = "";break;}
				return $Newkey->outertext;
			}else{
				return $template;
			}
		}
		public static function process_tag_ws_img($key=null){
			// $Newkey 			= self::minify_html($key->outertext());
			// $Newkey 			= str_get_html($Newkey);
			// $Newkey 			= $Newkey->find('ws-tool',0);
			// $outertext 		= $key->outertext();
			// $template  		= $key->innertext();
			// $atributos 		= $key->attr;
			// $return_Tool 	= self::returnClassToolWS($atributos)->sql;
			return null;
		}
		public static function process_tag_ws_query($key=null){
			$Newkey 		= self::minify_html($key->outertext());
			$Newkey 		= str_get_html($Newkey);
			$Newkey 		= $Newkey->find('ws-tool',0);
			$outertext 		= $key->outertext();
			$template  		= $key->innertext();
			$atributos 		= $key->attr;
			$return_Tool 	= self::returnClassToolWS($atributos)->sql;
			return $return_Tool;
		}
		public static function process_tag_ws_repeat($key=null){
			$type 			= array('words','sentence','sentences','paragraphs','paragraph','img');
			$outertext 		= $key->outertext();
			$innertext 		= $key->innertext();
			$atributos 		= $key->attr;
			$loop 			= empty($atributos['loop']) ? 1 : $atributos['loop'];

			$ws_biblioteca=new MySQL();
			$ws_biblioteca->set_table(PREFIX_TABLES.'ws_biblioteca');
			$ws_biblioteca->set_colum('file');
			$ws_biblioteca->select();
			$biblioteca = Array();
			foreach ($ws_biblioteca->fetch_array as $value) {$biblioteca[]=$value['file'];}
			if(array_key_exists("loop",$atributos)){ unset($atributos['loop']);};
			$template 		= array();

			for ($i=0; $i < $loop; $i++) { 
					$lipsum 		= new Lipsum();
					$isso 			= array();
					$porisso 		= array();
					foreach ($atributos as $key => $value) {
						if(strpos($value,",")!==false){
							$vars 			= explode(',', $value);
						//	if(in_array($vars[0],$type)){
								$isso[] 		= "{{".$key."}}";
								if($vars[0]!="img"){
									$lip=$lipsum->$vars[0]($vars[1]); 
									$porisso[] 		=  $lip;
								}else{
									$porisso[] 		="imagem";
								}
							// }else{
							// 	die("Valores inválidos em <ws-lipsum ".$key."='".$vars[0]."'> Valores permitidos:'".implode($type,"','")."'");
							// }
						}else{
							if(in_array($value,$type)){
								if($value!="img"){
									$lip 			=  $lipsum->$value(1);
									$isso[] 		= "{{".$key."}}";
									$porisso[] 		=  $lip;
								}else{
									$isso[] 		= "{{".$key."}}";
									$porisso[] 		=  $biblioteca[rand(0,$ws_biblioteca->_num_rows-1)];
								}
							}else{
								die("Valores inválidos em <ws-lipsum ".$key."='".$value."'> Valores permitidos:'".implode($type,"','")."'");
							}
						}
					}
					$template[] = str_replace($isso, $porisso,$innertext);
			}
			return implode($template);
		}

		public static function process_tag_get($key=null){
			$outertext 		= $key->outertext();
			$innertext 		= $key->innertext();
			$atributos 		= $key->attr;

			foreach ($atributos as $key => $value) {
				if(isset($_GET[$key])){
					return $_GET[$key];
					break;
				}
			}
			return null;
		}
		public static function process_tag_post($key=null){
			$outertext 		= $key->outertext();
			$innertext 		= $key->innertext();
			$atributos 		= $key->attr;
			foreach ($atributos as $key => $value) {
				return $_POST[$key];
				break;
			}
			return null;
		}
		public static function process_tag_device($key=null){
			$outertext 		= $key->outertext();
			$innertext 		= $key->innertext();
			$atributos 		= $key->attr;
			$detect 		= new Mobile_Detect;
			$detect 		= ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'desktop');
			$detectmin 		= new Mobile_Detect;
			$detectmin 		= ($detectmin->isMobile() ? ($detectmin->isTablet() ? 't' : 'p') : 'd');
			 if(isset($atributos['view'])){
				$view = $atributos['view'];
				$pos  = !(strpos($view, $detectmin) === false);
			 	if($pos){
					return $outertext;		
				}else{
					return "";		
				}
			}elseif(isset($atributos[$detect]) && ($atributos[$detect]=='true' || $atributos[$detect]=='1')){
				return $outertext;
			}else{
			 	return "";
			 }
		}

		public static function minify_html($html){
			return str_replace(array(PHP_EOL,"\n","\r",'&nbsp;'),'_ws_php_eol_',$html);
		}
		public static function beaultyHTML($html=null){
				return str_replace(array('_ws_php_eol_','<!-- [ws] -->'),array(PHP_EOL,""), $html);
		}
		public static function process_tag_ws_tool($key=null){
			
			$Newkey 		= self::minify_html($key->outertext());
			$Newkey 		= str_get_html($Newkey);
			$Newkey 		= $Newkey->find('ws-tool',0);
			$outertext 		= $key->outertext();
			$template  		= $key->innertext();
			$atributos 		= $key->attr;
			$classWS 		= self::returnClassToolWS($atributos);
			$return_Tool 	= $classWS->result;

			 $keyNoResult 		= $Newkey->find('ws-no-result',0);
			 if(count($return_Tool)==0 && @count($keyNoResult)>0 && gettype($keyNoResult)!=NULL){
				$innertext 		= $keyNoResult->innertext();
				return $innertext;
			}else{
				$result = self::returnClassToolWS($atributos,self::clearNoResult($template))->result;
				$result = self::beaultyHTML($result);
				return "<!-- [ws] -->".$result;
			 }
		}
		public static function returnClassToolWS($atributos,$template=null,$encadeado=true){
			$classReturn 	= Array();
			$FirstSlug 		= "";
			$evalClass 		= new WS();
			foreach ($atributos as $key => $value) {
				# para evitar templates nos atributos (temporário)
				$chaves = strpos($value,'{{');
				if(is_bool($chaves) && $chaves==false) {
					#caso queira retornar algo pelo PHP 
					if(substr($value,0,6)=="return" ){ $value =  ws::execCode('<? '.trim($value.';').' ?>');}
					#para posicionar o slug antes de tudo (pra captar outros dados antes de processar)
					if($value!=""){
						if($key=="slug" && $FirstSlug==""){ 
							$evalClass->slug($value);
						}else{ 

							if($key=="paginate") {
								if(strpos($value,',') != false){							
									$value = explode(',',trim($value));
									$evalClass->$key($value[0],$value[1]);
								}else{
									$evalClass->$key((int)$value);
								}
							}else{
								$evalClass->$key($value);
							}				
						}
					}
				}
			}

			if($template!=null){ 		
				$evalClass->setTemplate(addslashes(self::minify_html($template)))->go();
			}else{
				$evalClass->go();
			}

			return $evalClass;
		}
		public static function process_tag_ws_plugin($key=null){
			$outertext 	= $key->outertext();
			$key 		= str_get_html($outertext);
			$key 		= $key->find('ws-plugin',0);
			$innertext = $key->innertext();
			$atributos = (Object)Array();
			$setupdata = self::getSetupData();
			foreach ($key->attr as $key => $value) {
				if(substr($value,0,6)=="return" ){ $value = eval(trim($value.';'));}
				if(substr($value,0,6)==='array('){
					eval("\$result=$value;");
					$atributos->{$key}= $result;
				}else{
					$atributos->{$key}= $value;
				}
			}
			ob_start(); @include(INCLUDE_PATH.'website/'.$setupdata['url_plugin'].'/'.$atributos->path.'/plugin.config.php'); ob_get_clean();
			$ws = (object) array(
							'pathPlugin'		=>	$setupdata['url_plugin'].'/'.$atributos->path
							,'rootPath'			=>	ws::rootPath
							,'includePath'		=>	ws::includePath
							,'outertext'		=>	self::beaultyHTML($outertext)
							,'innertext'		=>	self::beaultyHTML($innertext)
							,'config' 			=> 	$plugin 
							,'vars' 			=>	$atributos
						);
			ob_start(); @include(INCLUDE_PATH.'website/'.$setupdata['url_plugin'].'/'.$atributos->path.'/'.$plugin->plugin); $content=ob_get_clean();
			return $content;
		}
		public static function processHTML($htmlStr=null){
			$html 		= str_get_html(self::minify_html($htmlStr));
			$metaTags 	= "";
			

			$a 		= 'get';			if((@$html->find($a))	>0)	{ foreach (@$html->find($a) as $key){ 		$key->outertext = self::process_tag_get($key);}}
			$a 		= 'post';			if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) as $key){ 		$key->outertext = self::process_tag_post($key);}}
			$a 		= 'ws-device';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) as $key){ 		$key->outertext = self::process_tag_device($key);}}
			$a 		= 'ws-nocode';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) as $key){ 		$key->outertext = "";													}}
			$a 		= 'ws-lipsum';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) as $key){ 		$key->outertext = self::process_tag_ws_repeat($key);}}
			################################################################################################################################################################################# 
			# self::processHTML -> Pois é recursivo; 
			################################################################################################################################################################################# 
			$a 		= 'ws-tool';	
			if(count(@$html->find($a))	>0)	{ 
				foreach (@$html->find($a) as $key){ 
					$tagProcess 	= self::process_tag_ws_tool($key);
					$outertext 		= self::processHTML($tagProcess);
					$key->outertext = $outertext;
				}
			}
			################################################################################################################################################################################# 
			$a 		= 'ws-img';			if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) 	as $key){ 	$key->outertext = self::process_tag_ws_img($key);			}}
			$a 		= 'ws-query';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) 	as $key){ 	$key->outertext = self::process_tag_ws_query($key);			}}
			$a 		= 'ws-scripts';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) 	as $key){ 	$key->outertext = self::process_tag_ws_scripts($key);		}}
			$a 		= 'ws-style';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a) 	as $key){ 	$key->outertext = self::process_tag_ws_style($key);			}}
			$a 		= 'ws-paginate';	if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a)		as $key){ 	$key->outertext = self::process_tag_ws_paginate($key);		}}
			$a 		= 'ws-metatags'; 	if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a)		as $key){ 	$key->outertext = self::process_tag_ws_metatags(); 			}}
			$a 		= 'ws-search';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a)  	as $key){ 	$key->outertext = self::process_tag_ws_search($key);		}}
			$a 		= 'ws-plugin';		if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a)  	as $key){ 	$key->outertext = self::process_tag_ws_plugin($key);		}}
			$a 		= 'ws-shortcode';	if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a)  	as $key){ 	$key->outertext = self::process_ws_shortcode($key);			}}
			$a 		= 'ws-php';			if(count(@$html->find($a))	>0)	{ foreach (@$html->find($a)  	as $key){ 	$key->outertext = self::process_ws_php($key);				}}

			return 	self::beaultyHTML($html);	
		}
	}
?>
