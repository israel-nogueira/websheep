<?
    $time_limit 	= ini_get('max_execution_time');
    $memory_limit 	= ini_get('memory_limit');
    set_time_limit(0);
    set_time_limit($time_limit);
    ini_set('memory_limit', $memory_limit); 
    ini_set('memory_limit', '-1');   
    function juntaPartes($file="", $diretorio="./"){
		$folder = opendir($diretorio);
		$splitnum = 1;
		$fp = fopen($diretorio."/".$file,"w");
		while ($item = readdir($folder)){if ($item != "." &&  $item != ".."){
			$arquivo = $diretorio."/".$file.".".str_pad($splitnum, 3, "0", STR_PAD_LEFT);
			if(file_exists($arquivo)){
				$conteudo = file_get_contents($arquivo);
				unlink($arquivo);
				fwrite($fp, $conteudo);
			}
			$splitnum++;
		}}
		fclose($fp);
	}
	juntaPartes('FTP_Transfer.zip', "./__WS__FILES__");
?>