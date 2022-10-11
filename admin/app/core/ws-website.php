<? 

	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}
	if(!defined("INCLUDE_PATH")){
		define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));
	}
	if(file_exists(INCLUDE_PATH.'ws-config.php')){
		include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
		ws::init();
	}else{
		include_once(INCLUDE_PATH.'admin/app/core/init.php');
	}
