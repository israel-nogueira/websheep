<?
	########################################################################
	# IMPORTAMAMOS A CLASSE INTERNA 
	########################################################################
	$r = $_SERVER["DOCUMENT_ROOT"];$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;
	include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/app/lib/class-ws-v1.php');
	session_name('_WS_');
	session_regenerate_id();
	session_id($_COOKIE['ws_session']);
    session_start();
	if(isset($_GET['session'])){print_r($_SESSION);	exit;}


	########################################################################
	# ARRAYS COM OS VALORES PRE DEFINIDOS DO SISTEMA 
	########################################################################
	$artigos 			= 	array("pra","para","mim","e","o","ao","aos","à","às","da","das","de","do","dos","a","as","na","nas","os","no","nos","num","nuns","em","numa","numas","um","uns","dum","duns","uma","umas","duma","dumas");
	$DICIONARIO 		= 	array("quem seriam","quem seria","quem são","quem foram","quem é","quem foi","quem será","o que é","o que quer dizer","o que significa","que significa","qual é o significado","qual o significado de","qual o significado", "qual é o significado de");
	$init 				=	array("bore","bori","dale","dolly","dori","dolly","dolly","dolly","dolly","dolly","italy","dolly" ,"idole","dali","idade","darling","dog","vale","lei");
	$response 			=	array("sim?","o que?","olá?","pois não?","estou ouvindo","diga?");
	$Cancel				=	array("cancelar","cancela","cancele","esquece","esqueça","deixa pra lá","parar","pare","abortar","aborte");
	$responseCancel 	=	array("ordem cancelada","comando cancelado","ok","ok, parei.","tá bom");

	usort($DICIONARIO, function($a, $b){return (strlen($a)  < strlen($b)); }); 

	########################################################################
	# FUNÇÕES PRE DEFINIDAS 
	########################################################################
	function search_in_Wiki(){
		$url='http://pt.wikipedia.org/w/api.php?action=query&prop=extracts|info&exintro&titles='.urlencode($_POST['search']).'&format=json&explaintext&redirects&inprop=url';
		$json = file_get_contents($url);
		$json = json_decode($json, TRUE);
		$result = array();
			foreach($json['query']['pages'] as $page){
				$result["title"]	=  (empty($page['title']))?"Sem titulo..":$page['title'];
				$result["extract"]	= (empty($page['extract']))?"Sem descrição..":$page['extract'];
				$result["fullurl"]	= @$page['fullurl'];
				break;
			};
		 return $result;
	}

	function search_in_google(){
		$url 	= 'https://www.googleapis.com/customsearch/v1?q='.urlencode($_POST['search']).'&hl=pt&alt=json&key=AIzaSyDB2CL5jO7KZ51ibfJ_hPM0uBCI_SsMiJA&cx=007902768968091743701:mbho57c0nru';
		$url 	= file_get_contents($url);
		$result = json_decode($url,TRUE);

		if(isset($_POST['modal']) && $_POST['modal']==true){
			$modal = '<div style="position: relative; overflow: auto; height: 400px; margin-bottom: -55px;background-color: #FFF;">';
			foreach ($result['items'] as $key => $value) {
				$link = parse_url($value['link']);
				$modal .= '<div style="padding-bottom: 20px;overflow: hidden;">';
				$modal .= '<a href="'.$value['link'].'" target="_blank" style="text-decoration: none; ">';
				$modal .='	<div class="w1" style="text-align: left;font-weight: 600;padding: 4px 10px;font-size: 18px;color: #2752d2;">'.$value['title'].'</div>';
				$modal .='	<div class="w1" style="text-align: left;font-weight: 400;padding: 3px 10px;font-size: 14px;color: #019e00;margin-top: -6px;width: 1000px">'.$link['host'].'<span style="color: #aac3aa;">'.$link['path'].'</span></div>';
				$modal .='	<div class="w1" style="text-align: left;font-weight: 300;padding: 4px 10px;font-size: 16px;color: #001;margin-top: -4px;">'.strip_tags($value['snippet']).'</div>';
				$modal .= "</a>";
				$modal .= "</div>";
			}
			$modal .= "</div>";
			$modal .= "<script>";
			$modal .= "wsAssistent.speak('Encontrei ".count($result['items'])." resultados',false,false);";
			$modal .= "</script>";
			 $modal =  str_replace(array(PHP_EOL,"\n","\r"),"",addslashes($modal));
			echo 'ws.confirm({conteudo:"'.$modal.'"});';
		}else{
			return $result['items'];
		}
	}

	function search_in_internet(){
			$wikki 		= search_in_Wiki();
			if($wikki['extract']=="Sem descrição.."){
				search_in_google();
			}else{
				$link = parse_url($wikki['fullurl']);
				$modal = '<div style="position: relative;overflow: auto;background-color: #FFF;height: calc(100% + 60px);">';
				$modal .= '		<div style="padding-bottom: 20px;">';
				$modal .= '			<a href="'.$wikki['fullurl'].'" target="_blank" style="text-decoration: none; ">';
				$modal .='			<div class="w1" style="text-align: left;font-weight: 600;padding: 4px 10px;font-size: 18px;color: #2752d2;">'.$wikki['title'].'</div>';
				$modal .='			<div class="w1" style="text-align: left;font-weight: 400;padding: 3px 10px;font-size: 14px;color: #019e00;margin-top: -6px;width: 1000px">'.$link['host'].'<span style="color: #aac3aa;">'.$link['path'].'</span></div>';
				$modal .='			<div class="w1" style="text-align: left;font-weight: 300;padding: 4px 10px;font-size: 16px;color: #001;margin-top: -4px;">'.str_replace(".",".<br>",$wikki['extract']).'</div>';
				$modal .= "			</a>";
				$modal .= "		</div>";
				$modal .= "</div>";
				$modal .= "<script>";
				$modal .= "wsAssistent.speak('Encontrei a seguinte definição',false,false);";
				$modal .= "</script>";
				$modal =  str_replace(array(PHP_EOL,"\n","\r"),"",addslashes($modal));
				echo 'ws.confirm({conteudo:"'.$modal.'",width:"calc(100% - 150px)", height:"calc(100% - 180px)"});';
			}
	}


	function speak($string,$open=true,$close=true) {
		$open 	= ($open) 	? "true" : "false";
		$close 	= ($close) 	? "true" : "false";
		return 'wsAssistent.speak("'.addslashes($string).'",'.$open.','.$close.');';
	}

	function zeraTudo(){
		$_SESSION['speakDolly'] = null;
	}

	function meaning(){
		speak("funciona!",true,true);
	}

	function console_log($string){
		echo 'console.log("'.$string.'");';
	}

	function retira_artigos($array){
		global $artigos;
		$new_array = (is_array($array)) ? $array : explode(' ',$array);
		$wheres = array();
		foreach ($new_array as $value) {
			if(!in_array($value,$artigos) && $value!=""){
				$wheres[] = $value;
			}
		}
		return (is_array($array)) ? $wheres : implode($wheres,' ');
	}

	function criaModalDesconexo($array){
		global $artigos;
		$new_array = (is_array($array)) ? $array : explode(' ',$array);
		$wheres = array();
		foreach ($new_array as $value) {
			if(!in_array($value,$artigos) && $value!=""){
				$wheres[] = $value;
			}
		}
		return (is_array($array)) ? $wheres : implode($wheres,' ');
	}
	function processaFrase($text,$search,$array_local){
		global $artigos;
		$_LOCAL = array();
		$_ACAO 	= array();

		$local 	= explode(",",strtolower($array_local['local'])); 
		$acao 	= explode(",",strtolower($array_local['acao'])); 
		$search = strtolower($search);

		foreach ($text as $itemLocal) {
			 if(in_array(trim(strtolower($itemLocal)),$local)){
			 	$_LOCAL[] = strtolower($itemLocal);
			 }
		}

		foreach ($text as $itemAcao) {
			 if(in_array(trim(strtolower($itemAcao)),$acao)){
			 	$_ACAO[] = $itemAcao;
			 }
		}


		$posLocal = (count($_LOCAL)>0) 	? strpos(strtolower($search), $_LOCAL[0]) 	: -1;
		$posAcao  = (count($_ACAO)>0) 	? strpos(strtolower($search), $_ACAO[0]) 	: -1;
		# SE TIVER APENAS AÇÃO
		if($posAcao>-1 && $posLocal==-1){
				$strelen 		= strlen($_ACAO[0]);
				$strPos 		= strpos($search, $_ACAO[0]);
				$substr 		= substr($search,$strPos+$strelen);
				$trim 			= trim($substr);
				$substantivos 	= retira_artigos($trim);
				echo '/*'.__LINE__.':'.$substantivos.'*/'.PHP_EOL.PHP_EOL;

				return $substantivos;
				# SE TIVER APENAS LOCAL
		}elseif($posLocal>-1 && $posAcao==-1){

			# se não tiver ação
			if($posLocal==0){
				$strelen 		= strlen($_LOCAL[0]);
				$substr 		= substr($search,$strelen);
				$trim 			= trim($substr);
				$substantivos 	= retira_artigos($trim);
				echo '/*'.__LINE__.':'.$substantivos.'*/'.PHP_EOL.PHP_EOL;
				return $substantivos;
			}else{
				$strelen 		= strlen($_LOCAL[0]);
				$substr 		= substr($search,$posLocal+$strelen);
				$trim 			= trim($substr);
				$substantivos 	= retira_artigos($trim);
				echo '/*'.__LINE__.':'.$substantivos.'*/'.PHP_EOL.PHP_EOL;
				return (array(retira_artigos($search),$substantivos));
			}

		}elseif($posLocal<$posAcao){

			# se o local for menor do que a ação
			$strlen = strlen($_ACAO[0]);
			$substr = substr($search,$posAcao+$strlen);
			$trim 	= trim($substr);

			echo '/*'.__LINE__.':'.$trim.'*/'.PHP_EOL.PHP_EOL;
			return retira_artigos($trim);

		}elseif($posAcao<$posLocal){
			# se a ação for menor do que o local 
			# verificamos os comandos entre eles
			$stringPosAction 	= 	trim(substr($search,$posAcao+strlen($_ACAO[0])));
			$stringMiolo 		=	trim(substr($stringPosAction,0,strpos($stringPosAction,$_LOCAL[0])));
			$stringResto 		= 	trim(substr($search,(strpos($search,$_LOCAL[0])+strlen($_LOCAL[0]))));
			#explode em array 
			$arrayMiolo 		= 	explode(' ',$stringMiolo);
			$arrayResto 		= 	explode(' ',$stringResto);


			# se se não existe ações definidas entre eles... 
			if(retira_artigos($stringMiolo)==""){
					# verifica se existe comandos sobressalentes, se existir retorna, caso contrario dá erro 
					echo '/*'.__LINE__.':'.retira_artigos($stringResto).'*/'.PHP_EOL.PHP_EOL;
					return ((retira_artigos($stringResto)!="") ? retira_artigos($stringResto) : null);
			# se existe um miolo 
			}elseif(retira_artigos($stringMiolo)!=""){
					# caso exista apenas o miolo retorna
					# caso exista um sobressalente, retorna um array com 3 opções, miolo, restante, miolo + restante 
					if(retira_artigos($stringResto)!=""){
						#tiramos as strings que se repetem

						$todos = array_unique(explode(' ',retira_artigos($stringMiolo).' '.retira_artigos($stringResto)));
						$miolo = array_unique(explode(' ',retira_artigos($stringMiolo)));
						$resto = array_unique(explode(' ',retira_artigos($stringResto)));

						return (array(implode($miolo,' '),implode($resto,' '),implode($todos,' ')));
					}else{
						return ($stringMiolo);
					}
			}
		}
	}


	########################################################################
	# CASO NÃO SEJA UMA CONVERSA, SEJA APENAS UMA FUNÇÃO A SER EXECUTADA
	########################################################################
	if(isset($_POST['function'])){
		_exec(@$_POST['function']);
		exit;
	}


	########################################################################
	# DEFINE SE AINDA ESTÁ ESCUTANDO OU NÃO 
	########################################################################
	$_SESSION['speakDolly']['listen']		=(empty($_SESSION['speakDolly']['listen'])) 		? null 	: $_SESSION['speakDolly']['listen'];
	$_SESSION['speakDolly']['continue']		=(empty($_SESSION['speakDolly']['continue'])) 		? null 	: $_SESSION['speakDolly']['continue'];
	$_SESSION['speakDolly']['desconexo']	=(empty($_SESSION['speakDolly']['desconexo'])) 		? null 	: $_SESSION['speakDolly']['desconexo'];
	$_SESSION['speakDolly']['dualidade']	=(empty($_SESSION['speakDolly']['dualidade'])) 		? null 	: $_SESSION['speakDolly']['dualidade'];

	$search = str_replace(array('.',',',';')," ",$_POST['search']);
	$text 	= explode(" ",$search);

	########################################################################
	# CANCELAR VEM ANTES DE TUDO
	########################################################################

	if(in_array(strtolower($search),$Cancel) && $_SESSION['speakDolly']['listen']==true){
		zeraTudo();
		$total = array_rand($responseCancel,1);
		echo speak($responseCancel[$total],true,false);
		exit;	
	}

	########################################################################
	# CASO SEJA CONTINUAÇÃO
	########################################################################
	if($_SESSION['speakDolly']['continue']==true)	{goto continueDolly;	}
	if($_SESSION['speakDolly']['desconexo']==true)	{goto desconexo;		}
	if($_SESSION['speakDolly']['dualidade']==true)	{goto dualidade;		}




	if(in_array(strtolower($search),$init) && $_SESSION['speakDolly']['listen']==null){
		$_SESSION['speakDolly']['listen'] = true;
		$total = array_rand($response,1);
		echo speak($response[$total],true,false);
		exit;	
	}


	if(in_array(strtolower($search),$init) && $_SESSION['speakDolly']['listen']==true){
		echo speak("EU JÁ ESTOU TE ESCUTANDO.",true,false);
		echo speak("CONTINUE.",true,false);
		exit;	
	}


	########################################################################
	# CASO ESTEJA OUVINDO, INICIA A PESQUISA
	########################################################################

	if($_SESSION['speakDolly']['listen']==true){



	########################################################################
	# CASO ESTEJA OUVINDO, INICIA A PESQUISA
	########################################################################
		foreach ($DICIONARIO as $key => $value) {
			if(substr($search,0,strlen($value))==$value){
				$parametros = trim(substr($search,(strpos($search,$value)+strlen($value))));
				$parametros = explode(' ',$parametros);
				if(count($parametros)>1){
					$parametros = implode($parametros," ");
					echo 'wsAssistent.exec("pesquise na internet '.$parametros. '");';
				}else{
					$searchDolly= new MySQL();
					$searchDolly->set_table(PREFIX_TABLES.'ws_dolly_dicionario');
					$searchDolly->set_where('palavra="'.$parametros[0].'"');
					$searchDolly->select();
					if($searchDolly->_num_rows>0){
						echo speak($searchDolly->fetch_array[0]['palavra'].'.',true,false);
						echo speak($searchDolly->fetch_array[0]['classe'].'.',false,false);
						echo speak($searchDolly->fetch_array[0]['curto'].'.',false,false);
						echo speak(strip_tags($searchDolly->fetch_array[0]['longo']).'.',false,true);
						exit;
					}else{
						echo 'wsAssistent.exec("pesquise na internet '.$parametros[0]. '");';
					}
				}
				exit;
			}
		}


		$searchDolly= new MySQL();
		$searchDolly->set_table(PREFIX_TABLES.'ws_dolly_fn');
		$wheres = array();
		foreach ($text as $key => $value) {
			if(!in_array($value,$artigos) && $value!=""){
				$wheres[]=("(acao like '%".$value."%' OR local like '%".$value."%')");
			}
		}

		$searchDolly->set_where(implode($wheres,"OR"));
		$searchDolly->select();

		console_log($searchDolly->query);

		if($searchDolly->_num_rows==0){
			echo speak("Estranho, mas não entendi o seu comando.",true,false);
			echo speak("Tente falar com outras palavras.",false,false);
			# não zera o cache...
		}elseif($searchDolly->_num_rows==1){

			$_SESSION['speakDolly']['mysql'] = $searchDolly->fetch_array[0];
			$confirma = $searchDolly->fetch_array[0]['confirma']; 

			if($confirma!=""){
				echo speak($confirma,true,false);
				$_SESSION['speakDolly']['continue'] = true;
				exit;
				continueDolly:
				if($search=="sim"){
					echo $_SESSION['speakDolly']['mysql']['codigo'];
					zeraTudo();
				}elseif($search=="não"){
					echo speak("ok",true,true);
					zeraTudo();
				}else{
					echo speak("Não entendi, diga sim ou não.",true,false);
				}
				exit;
			}else{


				$result 	= processaFrase($text,$search,$searchDolly->fetch_array[0]);
				$desconexo 	= $searchDolly->fetch_array[0]['desconexo'];

				
				if(is_array($result)){
					echo speak("Desculpe, não entendi direito.",true,false);	
					echo speak($desconexo,false,false);	


					confirmaDesconexo:

					echo 'ws.confirm({conteudo:\'<div style="margin-bottom: -30px;">';
					foreach ($result as $key => $value) { echo '<div class="DollyDesconexo" style="padding: 10px 20px;text-align:left;"><span>•</span> <b>'.$value.'</b></div>';}
					echo '<div>\',height:"auto",onClose:function(){
							wsAssistent.exec("cancelar");
						},posFn:function(){
							$(".DollyDesconexo").click(function(){
								var frase = $(this).find("b").text();
								$("#ws_confirm").remove();
								$("#body").removeClass("scrollhidden");
								$("*").removeClass("blur");
								wsAssistent.exec(frase);
							})
						}})';
					$_SESSION['speakDolly']['dualidade'] 		= false;
					$_SESSION['speakDolly']['desconexo'] 		= true;
					$_SESSION['speakDolly']['mysql'] 			= $searchDolly->fetch_array;
					exit;
					desconexo:
					echo str_replace('{search}', $search,$_SESSION['speakDolly']['mysql'][0]['codigo']);
					zeraTudo();

				}else{
					if($result!=null){
						echo str_replace('{search}', $result,$searchDolly->fetch_array[0]['codigo']);
					}
					zeraTudo();
					exit;
				};
			}

		}elseif($searchDolly->_num_rows>1){

			$functions = array();
			$desconexo = $searchDolly->fetch_array[0]['desconexo'];
			echo speak("Desculpe.",true,false);
			echo speak("Você quer fazer o quê?",true,false);

			$itemOpt = 0;
			foreach ($searchDolly->fetch_array as $value) {		
					echo speak($value['descricao']."?",true,false);
					$functions[] = '<div class="DollyDesconexo" style="padding: 10px 20px;text-align:left;" data-opt="'.$itemOpt.'"><span>•</span> <b>'.$value['descricao'].'</b></div>';
					$itemOpt++;
			}
			echo 'ws.confirm({conteudo:\''.implode($functions,'').'\',height:38,onClose:function(){
					wsAssistent.exec("cancelar");
				},posFn:function(){
					$(".DollyDesconexo").click(function(){
						 var frase 	= $(this).find("b").text();
						 var id 	= $(this).data("opt");
						wsAssistent.functions.returnFn({search:frase,id:id});
					})
				}})';

			$_SESSION['speakDolly']['dualidade'] 	= true;
			$_SESSION['speakDolly']['text'] 		= $text;
			$_SESSION['speakDolly']['search'] 		= $search;
			$_SESSION['speakDolly']['mysql'] 		= $searchDolly->fetch_array;

			exit;
			dualidade:
			foreach ($_SESSION['speakDolly']['mysql'] as $value) {
				if(strtolower($value['descricao'])==strtolower($search)){
					$_SESSION['speakDolly']['dualidade'] = false;
					$searchDolly = (object)array();
					$searchDolly->fetch_array 	= array($_SESSION['speakDolly']['mysql'][$_POST['id']]);
					$result 					= processaFrase($_SESSION['speakDolly']['text'],$_SESSION['speakDolly']['search'],$value);	
					$desconexo 					= $value['desconexo'];


					if(is_array($result)){
						goto confirmaDesconexo;
					}else{
						speak("até aqui ok!");
					}


					exit;
				}
			}

			echo speak("Desculpe, não entendi, fale novamente ou clique em uma das opções",false,false);	

			zeraTudo();

			exit;
		}
	}









