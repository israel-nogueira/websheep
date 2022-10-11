<?

	error_reporting(E_ALL);
	function minify_javascript($code, $level = 'SIMPLE_OPTIMIZATIONS'){
		try {
			$ch = curl_init('http://closure-compiler.appspot.com/compile'); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=' . $level . '&js_code=' . urlencode($code));
			$minified = curl_exec($ch);
			curl_close($ch);
		} catch (Exception $e) {
			$minified = $code;
		}
		return $minified;
	}
	$funcionalidades 	= file_get_contents("./funcionalidades.js");
	$FNWS 				= str_replace("{dataMinifiq}",date("Y-m-d H:i:s"),file_get_contents("./functionsws.js"));
	file_put_contents('./funcionalidades.min.js',minify_javascript($funcionalidades));
	file_put_contents('./functionsws.min.js',minify_javascript($FNWS));