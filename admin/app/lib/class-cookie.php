<?
class wsCookie{
  const Session   = null;
  const um        = 86400;
  const sete      = 604800;
  const trinta    = 2592000;
  const semestre  = 15811200;
  const umano     = 31536000;
  const Lifetime  = -1;
  static public function issetCookie($name){return isset($_COOKIE[$name]);}
  static public function emptyCookie($name){return empty($_COOKIE[$name]);}
  static public function getCookie($name,$default=''){return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default);}
  static public function setCookie($name, $value, $expiry = self::um, $path = '/', $domain = false){
    $retval = false;
    if (!headers_sent()){
      if ($domain === false)
        $domain = $_SERVER['HTTP_HOST'];
      if ($expiry === -1)
        $expiry = 1893456000;
      elseif (is_numeric($expiry))
        $expiry += time();
      else
      $expiry = strtotime($expiry);
      $retval = @setcookie($name, $value, $expiry, $path, $domain);
      if ($retval)$_COOKIE[$name] = $value;}
    return $retval;
  }
  static public function deleteCookie($name, $path = '/', $domain = false, $remove_from_global = false){
    $retval = false;
    if (!headers_sent()){
      if ($domain === false)
        $domain = $_SERVER['HTTP_HOST'];
        $retval = setcookie($name,'',time()-3600, $path, $domain);
      if($remove_from_global)unset($_COOKIE[$name]);}
    return $retval;
  }
}

?>