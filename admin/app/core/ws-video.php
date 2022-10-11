<?
	error_reporting(E_ALL) ;

	############################################################################################################################################
	# DEFINIMOS O ROOT DO SISTEMA
	############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
		$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'ws-video'));
		$path = implode(array_filter(explode('/',$path)),"/");
		define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
	}

	if(!defined("INCLUDE_PATH")){define("INCLUDE_PATH",str_replace("\\","/",substr(realpath(__DIR__),0,strrpos(realpath(__DIR__),'admin'))));}



	include_once(INCLUDE_PATH.'admin/app/lib/class-ws-v1.php');
	
	$_temp_ = new MySQL();
	$_temp_->set_table(PREFIX_TABLES.'ws_video');
	$_temp_->set_where('keyaccess="'.ws::urlPath(2,false).'"');
	$_temp_->set_where('AND creation > NOW() - INTERVAL 3 SECOND');
	$_temp_->select();
	if($_temp_->_num_rows==1){
		$tags 		= 	get_meta_tags($_temp_->obj[0]->linkvideo); 
		$id_video	=	basename($tags['twitter:player']);
		$urlSite	=	$tags['twitter:site'];
		if($urlSite=="@youtube"){
			$querys = parse_url($tags['twitter:url']);
			$querys = $querys['query'];
			parse_str($querys,$query);
			parse_str(file_get_contents("http://youtube.com/get_video_info?video_id=".$query['v']),$info);
			$streams = $info['url_encoded_fmt_stream_map']; //the video's location info
			$streams = explode(',',$streams);
			$mime = null;
			foreach($streams as $stream){
		   		parse_str($stream,$data); //decode the stream
		    	$data['type'] = explode(';',$data['type']);
		     	$data['type'] =  $data['type'][0];
		   		$mime =  $data['type'];
			    if($mime == 'video/mp4'){ 
			    	$_URL_VIDEO = $data['url'];
			       	break;
			    }
			}
		}elseif($urlSite=="@vimeo"){
			$json = (json_decode(file_get_contents('http://player.vimeo.com/video/'.$id_video.'/config')));
			$resol = 0;
			$mime = null;
			foreach ($json->request->files->progressive as $value) {
				 if($value->quality > $resol){
					$mime = $value->mime;
				 	$resol = (int)str_replace('p','',$value->quality);
				 	$_URL_VIDEO = $value->url;
				 }
			}
		}

		$_excl_ = new MySQL();
		$_excl_->set_table(PREFIX_TABLES.'ws_video');
		$_excl_->set_where('keyaccess="'.ws::urlPath(2,false).'"');
		$_excl_->exclui();
		header('Content-Type: '.$mime);
		readfile($_URL_VIDEO);

	}else{
		echo "Acesso inválido!";
		$_excl_ = new MySQL();
		$_excl_->set_table(PREFIX_TABLES.'ws_video');
		$_excl_->set_where('keyaccess="'.ws::urlPath(2,false).'"');
		$_excl_->set_where('OR creation > NOW() - INTERVAL 3 SECOND');
		$_excl_->exclui();
	}
	exit;
?>