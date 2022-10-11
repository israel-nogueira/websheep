<?
error_reporting(E_ALL) ;
#################################################################################################################################
   $quantidade_letras   = isset($_GET["qttd"])      ? $_GET["qttd"]     : 6;
   $fontSize            = isset($_GET["size"])      ? $_GET["size"]     : 22;
   $hex                 = isset($_GET["hex"])       ? $_GET["hex"]     : array("0b589f","103557","6295c4","7b97b2");
   $font                = isset($_GET["font"])      ? $_GET["font"]     : array('./AD_Nautilus.ttf');
   $entreLetras         = isset($_GET["entreletras"]) ? $_GET["entreletras"]  : -5;
   $angulos             = isset($_GET["ang"])         ? $_GET["ang"]          :  25; 
   $sobrasImg           = isset($_GET["sobras"])    ? $_GET["sobras"]     : 25; 
   $lower               = true; 
   #################################################################################################################################
   $palavra             = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz23456789"),0,($quantidade_letras)); 
function applyNoise() { 
    $this->_noiseImage = imagecreatetruecolor($this->_width, $this->_height); 
    for ($i = 0; $i < $this->_width; $i++) 
      for ($j = 0; $j < $this->_height; $j++) 
      { 
        $rand = rand(0, 255); 
        $colour = imagecolorallocate($this->_noiseImage, $rand, $rand, $rand); 
        imagesetpixel($this->_noiseImage, $i, $j, $colour); 
      } 
  }  

function _hexdec_($corsim) {
   $corsim = str_replace("#", "", $corsim);
   if(strlen($corsim) == 3) {
      $r = hexdec(substr($corsim,0,1).substr($corsim,0,1));
      $g = hexdec(substr($corsim,1,1).substr($corsim,1,1));
      $b = hexdec(substr($corsim,2,1).substr($corsim,2,1));
   } else {
      $r = hexdec(substr($corsim,0,2));
      $g = hexdec(substr($corsim,2,2));
      $b = hexdec(substr($corsim,4,2));
   }
   return array($r, $g, $b);
}
$w    = ($quantidade_letras * $fontSize)+($quantidade_letras * $entreLetras)+$sobrasImg;
$h    = ($fontSize*2)+$sobrasImg;


$img  =imagecreatetruecolor($w,$h);
imagealphablending($img,false);
$col  =imagecolorallocatealpha($img,255,255,255,127);
imagefilledrectangle($img,0,0,$w,$h,$col);
imagealphablending($img,true);

function createLine($Qtdd=50,$alpha=array(20,127)){
   global $img;
   global $hex;
   global $h;
   global $w;
   for ($i=0;$i<$Qtdd;$i++){
      $randColors = (int)rand(0,count($hex)-1);
      $rgbReal    = _hexdec_($hex[$randColors]);
      $color      = imagecolorallocatealpha($img, $rgbReal[0], $rgbReal[1], $rgbReal[2],rand($alpha[0],$alpha[1]));
      imageline($img,mt_rand(0,$w),mt_rand(0,$h),mt_rand(0,$w),mt_rand(0,$h),$color);
   }
}
// cria sujeira de fundo
createLine($Qtdd=50,$alpha=array(20,127));
for($i = 1; $i <= $quantidade_letras; $i++){ 
   $randArray = (int)rand(0,count($font)-1);
   $randColors = (int)rand(0,count($hex)-1);
   $rgbReal    = _hexdec_($hex[$randColors]);
   $color      = imagecolorallocate($img, $rgbReal[0], $rgbReal[1], $rgbReal[2]);
   $captchaKey = ' '.substr($palavra,($i-1),1).' ';
   imagettftext($img,$fontSize,rand(-$angulos,$angulos),(($fontSize+$entreLetras)*($i-0.8)),($fontSize+($fontSize/2)),$color,$font[$randArray],$captchaKey);   
}
createLine($Qtdd=50,$alpha=array(70,127));
################################################## HEADERS
if($lower==true){
   $palavra = str_replace(" ","",strtolower($palavra)); 
}else{
   $palavra = str_replace(" ","",$palavra); 
}



include_once('./class-ws-v1.php');
@session_name('_WS_');
@session_id($_COOKIE['_WS_']);
@session_start();
$_SESSION['ws-captcha'] = _encripta($palavra,"ws-captcha-keycode"); 


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: image/png');
imagealphablending($img,false);
imagesavealpha($img,true);
imagepng($img);

################################################## aqui salva na sessão a palavra