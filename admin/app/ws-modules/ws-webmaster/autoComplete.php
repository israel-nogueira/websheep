 <?php
function mostraDirJson($dir, $oq = ""){
	global $output;
    if (is_dir($dir)) {
        $dh = opendir($dir);
        while ($diretorio = readdir($dh)) {
            if ($diretorio != '..' && $diretorio != '.' && @filetype($dir.$diretorio) == "dir") {
                if (is_dir($dir.$diretorio)) {
                	$output .= '	,{"value":"'.str_replace('./../../../website/', '',$dir.$diretorio).'","meta":"Folder"}';
                    mostraDirJson($dir.$diretorio . "/");
       			}
        	}
    	}
	}
}
function getSnap($str){return str_replace(array(PHP_EOL,'"',"	"),array('\n','\"',"   "),$str);}
	$colunasToll = array();
	$campos= new MySQL();
	$campos->set_table(PREFIX_TABLES.'_model_campos');
	$campos->set_where('coluna_mysql<>""');
	$campos->select();

	foreach ($campos->fetch_array as $value) {$colunasToll[$value['ws_id_ferramenta']][]=$value['coluna_mysql']; }
	$slugs= new MySQL();
	$slugs->set_table(PREFIX_TABLES.'ws_ferramentas');
	$slugs->set_where('slug<>""');
	$slugs->select();
	$tagHTML = array();
	$tagHTML='<!-- TAG WS HTML5 -->
				<ws-tool data-slug="" data-type="" data-limit="" data-colum="" data-distinct="" data-utf8="" data-url="" data-order="" data-category="" data-galery="" data-item="" data-where=""		data-innerItem="" 		data-filter="">
				<!-- template -->
				</ws>';
			$class='#Class WS Tool PHP:
					$Tool= new WS();
					$pesquisa = $Tool
								->setSlug("")
								->setType("")
								->limit(1)
								->innerCategory(0)
								->innerItem(0)
								->item(0)
								->setTemplate("")
								->innerGalery(0)
								->setWhere("")
								->go();
					print_r($pesquisa->obj);
					print_r($pesquisa->_num_rows);
					foreach($pesquisa->obj as $data){
						echo "<div>".$data->institucional_title."</div>";
						echo "<div>".$data->institucional_content."</div>";
					}
				?>';
$template='#Class Template											
#Referência: https://github.com/raelgc/template
	$tpl = new Template("hello.html");
	$tpl->NAME="Jhon";
	if($tpl->exists("NAME")) $tpl->NAME = "Jhon";
	$tpl->block("BLOCK_NAME");
	$tpl->show();';

$cookie='#Class Cookie
	$type 	= "cookie"; 									// "cookie" ou "session"
	$secury = 1; 											// criptografado ou normal
	$senha 	= "senha123";									// chave da criptografia
	$prefix = "_x_";										// prefixo do cookie
	$cookie = new session($type,$secury,$senha,$prefix);	// inicia a classe
	$cookie->set("loginCookie", "myvalue");				// seta o valor da sessão ou do cookie
	$cookie->get("loginCookie");							// resgata o valor
	$cookie->finish();										// finaliza sessão ou exclui os cookies
	echo _erro($erro);';


$img='<img src="'.ws::rootPath.'ws-img/100/100/100/{colum}"/>';

$paginate='<ws-paginate data-slug="" data-type="" data-max="" data-atual="" data-html="" data-number="" data-active=""></ws-paginate>';

$browser='#Class Browser
	# https://github.com/cbschuld/class-browser.php
	$browser = new Browser();
	if( $browser->getBrowser() == Browser::BROWSER_FIREFOX && $browser->getVersion() >= 2 ) {
	    echo "You have FireFox version 2 or greater";
	}';

$leadCapture='<!-- TAG CADASTROS -->
<form id="ws_send">
		<input type="submit" value="Submit">
</form>
<div id="ws_response"></div>
<script>
	$("#ws_send").submit(function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url:"'.ws::rootPath.'ws-leads/Wg4na0S861lngYdUYyKqbY5lUz0",
			data: $("#ws_send").serialize(),
			async: true,
			beforeSend: function(data) {	$("#ws_response").prepend("1 <br>");	},
			ajaxSend: function(data) {		$("#ws_response").prepend("2 <br>");	},
			success: function(data) {		$("#ws_response").prepend("3 <br>");	},
			error: function(data) {			$("#ws_response").prepend("4 <br>");	},
			complete: function(data) {		$("#ws_response").prepend("5 <br>");	}
		}).done(function(data) {			
			$("#ws_response").prepend(data);	
		});
		return false;
	})
</script>';


$output = ''
		.' [ '
		.'	{"value":"ws::includePath", "meta":"Path"}'
		.'	,{"value":"ws::rootPath", "meta":"Path"}'
		.'	,{"value":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut odio ipsum. Vivamus placerat placerat efficitur. Duis eget justo eu dui euismod lobortis. Aliquam vel molestie augue. Donec venenatis, nibh in cursus scelerisque, lacus ipsum dictum est, eu facilisis justo nibh ac lectus. Vivamus facilisis felis metus, vitae condimentum neque suscipit ut. Curabitur vestibulum egestas ultrices. Fusce tempor lacus nulla, in volutpat urna ullamcorper vitae. Nullam nec iaculis elit. Morbi vel hendrerit eros. Integer varius porttitor nibh non euismod. Proin vel tellus sed tellus sodales laoreet sit amet non ex. Fusce consectetur et dui quis finibus. Donec varius purus imperdiet ipsum semper sagittis. Ut dui lorem, ultrices eu aliquam at, rhoncus id tellus.\nSed vulputate vehicula est, sed cursus erat fringilla ut. Quisque bibendum luctus turpis non accumsan. Etiam suscipit nisi convallis quam suscipit ornare. Integer non ornare orci. Sed id purus tortor. Sed non leo vitae velit fermentum cursus eu et quam. Ut fermentum ut urna eget varius. Pellentesque venenatis condimentum risus sed tincidunt. Donec euismod semper ex, ut suscipit neque blandit ut. Maecenas lobortis leo sed dolor rutrum eleifend. Donec suscipit, massa id cursus suscipit, felis leo hendrerit mauris, at faucibus purus quam eget dui. Cras maximus ligula eu odio convallis, nec volutpat nulla tincidunt. Suspendisse maximus scelerisque dui, vel venenatis est facilisis quis.\nAliquam fringilla condimentum rutrum. Curabitur fermentum, purus ut venenatis tincidunt, sapien urna vulputate neque, a pretium ex ex eget magna. Aliquam erat volutpat. Nulla nunc nunc, ornare ut dolor vitae, sodales molestie felis. Sed blandit dignissim diam ullamcorper ullamcorper. Donec nec risus arcu. Duis tempor libero at ipsum elementum, vel viverra sapien tincidunt. Integer in quam urna. Aliquam bibendum nulla non mattis sollicitudin.\nNullam molestie volutpat nibh, sit amet sodales sapien laoreet nec. Donec pellentesque at tortor eu posuere. Praesent malesuada, ex in blandit congue, nunc diam gravida tellus, iaculis pharetra purus magna sit amet est. Duis placerat id magna in bibendum. Nunc a leo et purus finibus tempor. Maecenas quis ex at nunc hendrerit pulvinar. Cras tempor purus ac sapien maximus, eu consequat sem ultrices. Phasellus vitae sapien nibh. Duis varius hendrerit enim eu mollis. Proin congue id nunc sed ullamcorper. Ut sed venenatis massa, id pellentesque mi. In condimentum nulla at nisl porta, nec congue eros mattis. Cras posuere ligula ut ex efficitur hendrerit. Sed ut orci accumsan, tincidunt neque finibus, lacinia dui.\nEtiam accumsan dui ut ligula vulputate ornare. Praesent posuere arcu non arcu pretium gravida. Cras blandit consectetur leo, ut condimentum mauris elementum et. Vivamus dictum ante sit amet nisi vulputate, vulputate sollicitudin mauris commodo. Sed consectetur nisi ut dignissim bibendum. Integer ultrices gravida felis vitae fermentum. Donec eget ligula hendrerit tellus malesuada hendrerit. Praesent sed finibus urna. Morbi laoreet eget quam id ultrices.\n", "meta":"Class WS"}'
		.'	,{"value":"ws::Lipsum(10,\"word\",\"<b>$1</b>\");", "meta":"Class WS"}'
		.'	,{"value":"ws::Lipsum(10,\"sentence\",[\"article\", \"p\",\"a\"]);", "meta":"Class WS"}'
		.'	,{"value":"ws::Lipsum(10,\"paragraphs\",\"p\");", "meta":"Class WS"}'
		.'	,{"value":"ws::Less(\"arquivo.less\",\"arquivo.css\");", "meta":"Class WS"}'
		.'	,{"value":"ws::urlPath(int);", "meta":"Class WS"}'
		.'	,{"value":"ws::urlAmigavel(str);", "meta":"Class WS"}'
		.'	,{"value":"ws::urlAmigavel(str);", "meta":"Class WS"}'
		.'	,{"value":"ws::AnalyticsCode(\"UA-xxxxxxxx-x\");","meta":"Class WS"}'
		.'	,{"value":"ws::facebookSDK(\"id\");","meta":"Class WS"}'
		.'	,{"value":"ws::blockZoom();","meta":"Class WS"}'
		.'	,{"value":"ws::activeImgResponsive();","meta":"Class WS"}'
		.'	,{"value":"ws::metaTags();","meta":"Class WS"}'
		.'	,{"value":"ws::script(\"file.js\",\"id\");","meta":"Class WS"}'
		.'	,{"value":"ws::style(\"file.css\",\"mediaquery\");", "meta":"Class WS"}'
		.'	,{"value":"ws::getVimeoYoutubeDirectLink(\"https://youtube.com/watch?v=_ID_VIDEO_\");","meta":"Class WS"}'
		.'	,{"value":"'.getSnap($img).'", "meta":"Class WS"}'
		.'	,{"value":"'.getSnap($tagHTML).'", "meta":"Class WS"}'
		.'	,{"value":"'.getSnap($class).'", 	"meta":"Class WS"}'
		.'	,{"value":"'.getSnap($template).'", "meta":"Class WS"}'
		.'	,{"value":"'.getSnap($cookie).'", "meta":"Class WS"}'
		.'	,{"value":"'.getSnap($browser).'", "meta":"Class WS"}'
		.'	,{"value":"'.getSnap($paginate).'", "meta":"Class WS"}'
		.'	,{"value":"'.getSnap($leadCapture).'", "meta":"Class WS"}';

		mostraDirJson('./../../../website/');



		foreach ($campos->fetch_array as $value) {$output .= ',{"value":"'.$value['name'].'",		"meta":"Colum HTML5"}'; }

		foreach ($slugs->fetch_array as $value) {
			$out='';
			$out .= "# Colunas: ".$value['slug'].'																	'.PHP_EOL;
			if(isset($colunasToll[$value['id']])){foreach ($colunasToll[$value['id']] as $colum) {$out .= '$data->'.$colum.PHP_EOL; } }
			$out .= PHP_EOL."<!-- Colunas: ".$value['slug'].' -->'.PHP_EOL;
			if(isset($colunasToll[$value['id']])){foreach ($colunasToll[$value['id']] as $colum) {$out .= "{{".$colum.'}}'.PHP_EOL; } }
			$output .=  ',{"value":"'.str_replace(array(PHP_EOL,'"',"	"),array('\n','\"',"   "),$out).'","meta":"HTML5"}';
		}
		foreach ($slugs->fetch_array as $value) {$output .=  ',{"value":"Slug: '.$value['slug'].'","meta":"Slug"}'; }
$output .=']';

file_put_contents('./autoComplete.json',$output);
