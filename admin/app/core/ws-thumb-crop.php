<?php
	ini_set('display_errors', '0');

	include('./classes/class-thumbnails.php');
	
$imagem		=$_REQUEST['img'];
$largura	=$_REQUEST['w'];
$altura		=$_REQUEST['h'];

if($largura==0 && $altura==0){
	$filesize = getimagesize($imagem);
	$largura = $filesize[0];
	$altura = $filesize[1];
}



$extencao = substr($imagem,-3);
if($extencao=='jpg'){$q='100';};
if($extencao=='png'){$q='9';};
if($extencao=='gif'){$q='100';};
if(!empty($_REQUEST['q'])){$q=$_REQUEST['q'];}
$pasta="";
$thumb = new thumb($imagem); 						//link ou resource da imagem original
$thumb->setDimensions(array($largura,$altura)); 	//largura e altura da thumb, aceita arrays multidimensionais
$thumb->setFolder($pasta); 							//caso queira que a thumb seja salva numa pasta
$thumb->sufix=true; 								//caso queira setar um sufixo -> imagem-750x320
$thumb->setJpegQuality($q);							//qualidade JPG (0-100)
$thumb->setPngQuality($q); 							//qualidade do PNG (0-9)
$thumb->setGifQuality($q);							//qualidade do GIF (0-100)
$thumb->crop=true;									//se a imagem deverá ser cropada ou não
$thumb->forceDownload(false);						//true para setar a thumb para download
$thumb->showBrowser(true);							//true para setar a thumb para mostrar no navegador
$thumb->process();
?>