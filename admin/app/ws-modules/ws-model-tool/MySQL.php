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

############################################################################################################################################
	include_once(INCLUDE_PATH.'ws-config.php');
?>
<iframe id="painelMySQL" src="<?=ROOT_WEBSHEEP.'admin/app/ws-modules/phpMyAdmin/index.php'?>" style="top: 0;position: relative; width: 100%;height: calc(100% - 56px);"></iframe>
