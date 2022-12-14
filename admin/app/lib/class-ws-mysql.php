<?
class MySQL {
			function __construct(){
				$this->like					=(object) array();
				$this->InsertVars			=array();
				$this->Insert_like			=array();
				$this->Insert_Update		=array();
				$this->fetch_array			=array();
				$this->obj					=array();
				$this->setcolum				='';
				$this->debug				=true;
				$this->decode				=false;
				$this->utf8					='none';
				$this->url 					='none';
				$this->slashes 				='none';
				$this->rell 				='';
				$this->ignore 				=array();
				$this->on_duplicate			=array();
				$this->DISTINCT 			='';
				$this->where 				= "";
				$this->query 				='';
				$this->template 			='';
				$this->replace_isso 		=array();
				$this->replace_porisso		=array();
				$this->InnerTypes			=array("cat","item","gal","img","file");
				$this->where 				=null;
				$this->set_where_not_exist	=null;
				$this->primarykey			=null;
				$this->slug 				=null;
				$this->galeria				=null;
				$this->settable				=null;
				$this->tabletoll			=null;
				$this->setwhere 			=null;
				$this->where_item 			=null;
				$this->table 				=null;
			}
				//ALTER TABLE `ws_usuarios` MAX_ROWS=98281474976710655 AVG_ROW_LENGTH=1000000000;
			static function infoTables(){
				$sql = '
				SELECT CONCAT(table_schema, ".", table_name),
				CONCAT(ROUND(( Max_data_length ) / ( 1024 * 1024 * 1024 ), 2), "G") 			max_data,
				CONCAT(ROUND(( data_length + index_length ) / ( 1024 * 1024 * 1024 ), 2), "G") total_size,
				ROUND(index_length / data_length, 2)                                          idxfrac
				FROM   information_schema.TABLES
				ORDER  BY data_length + index_length DESC
				LIMIT  10;';
				$sizes= new MySQL();
				$sizes->select($sql);
				return $sizes;
			}

			########################################################################################### SET INFORMATIONS
			public function set_template($template)  {$this->template = $template;}
			public function set_colum($COLUMNS=array())  {				
				if(empty($this->setcolum)){$this->setcolum = array();}

				if(is_array($COLUMNS)){
					$newColl=array();
					foreach($COLUMNS as $col){
						if(isset($col) && $col!="" ){
							$newColl[] = $col;
						}
					}
					$this->setcolum[] = $newColl;
				}elseif(is_string($COLUMNS) && $COLUMNS!=""){
					$this->setcolum[] = $COLUMNS;
				}

				if(is_array($this->setcolum)){
					$this->colum = @implode(',',$this->setcolum);// coloquei @ n??o porque da erro, mas aparece um NOTICE chato...
				}else{
					$this->colum = $this->setcolum;
				}
				if($this->colum==""){$this->colum="*";}
			}
			public function set_table($TABLES)  {
				if(empty($this->settable) || $this->settable==null ){$this->settable = array();}
				if(is_string($this->settable)){$this->settable = [$this->settable];}
				array_push($this->settable,$TABLES);

				$this->table = implode(',',$this->settable);
			}

			public function set_where($WHERES)  {
				if(empty($this->setwhere) || $this->setwhere==null){$this->setwhere = array();}
				$this->setwhere[] = $WHERES;
				$this->where =implode(',',$this->setwhere);
			}
			public function set_where_not_exist()  {$this->set_where_not_exist = true;}


			public function set_order($colum=null,$order=null)  	{
				if($colum==null && $order==null) _erro('Valor set_order indefinido');
				if(empty($this->setorder)){$this->setorder = array();}
				if($colum!=null && $order==null) {
					$this->setorder[] = $colum;
				}else{
					$this->setorder[] = $colum.' '.$order;					
				}
				$this->order = ' ORDER BY '.implode(',',$this->setorder);
			}

			public function set_group($colum)  			{$this->group = ' GROUP BY '.$colum;}
			public function set_limit($init=null,$finit=null)  			{
				if($init==null && $finit=null && $this->debug==true){
					_erro('Valor set_limit indefinido');
					exit;
				}
				if($finit==null){
					$this->limit = ' LIMIT '.$init.';';
				}else{
					$this->limit = ' LIMIT '.$init.", ".$finit.'; ';
				}
			}
			public function set_insert($colum,$var){
				if($this->utf8 		=='encode')	$var 	= utf8_encode($var);
				if($this->utf8 		=='decode')	$var 	= utf8_decode($var);
				if($this->url 		=='encode')	$var 	= urlencode($var);
				if($this->url 		=='decode')	$var 	= urldecode($var);
				if($this->slashes 	=='strip')	$var 	= stripslashes($var);
				if($this->slashes 	=='add')	$var	= addslashes($var);

				
				$this->InsertVars[$colum]=$var;
			}
			public function set_update($colum,$var){
				if($this->utf8 		=='encode')	$var 	= utf8_encode($var);
				if($this->utf8 		=='decode')	$var 	= utf8_decode($var);
				if($this->url 		=='encode')	$var 	= urlencode($var);
				if($this->url 		=='decode')	$var 	= urldecode($var);
				if($this->slashes 	=='strip')	$var 	= stripslashes($var);
				if($this->slashes 	=='add')	$var	= addslashes($var);

				if(is_string($var)){
					if(substr($var,0,8)=="command:"){
						$var= substr($var,8);
					}else{
						$var='"'.$var.'"';
					}
				}else{
					$var= $var;
				}



				array_push($this->Insert_Update , $colum.'='.$var );
			}
			#####################################################################################
			public function debug($bolean){$this->debug=$bolean;	}
			public function ignore($dados){$this->ignore='ignore';	}
			public function utf8($var){		$this->utf8=$var;		}
			public function url($var){		$this->url=$var;		}
			public function slashes($var){	$this->slashes=$var;	}
			public function on_duplicate($dados){$this->on_duplicate=' ON DUPLICATE KEY UPDATE '.$dados.' ';}
			public function distinct() {$this->DISTINCT=' DISTINCT ';}
			public function join($join="LEFT", $tabela, $on){ $this->rell .=$join.' JOIN '.$tabela.' ON '.$on;}
			public function like ($coluna, $palavra_chave){
				global $_conectMySQLi_;
				array_push ($this->Insert_like , ' LOWER('.$coluna.') LIKE  "'.$palavra_chave.'"' );
			}
			public function REGEXP($coluna, $palavra_chave){
				array_push ($this->Insert_like , $coluna.' REGEXP "'._likeString($palavra_chave).'"' );
			}
			public function get_template($retorno){
				$b = $c = array();
				$a = explode("{{",$this->template);
				foreach ($a as $str){
					if(stripos($str,'}}')){
						$d 		= explode("}}",$str);
						$key 			= $d[0];

						$newKey = explode(',', $key);
						if (count($newKey) == 1) {
							$b[] 	="{{".$key."}}";
							$c[] = $retorno[$key];

						} elseif (count($newKey) >= 2) {
							$verify = implode(',',array_slice($newKey, 1));


							if (count($newKey) == 2) {

									if (is_numeric(@$newKey[1]) || is_int(@$newKey[1])) {
										$b[] = "{{".$key."}}";
										$c[] = substr(strip_tags(@$retorno[$key]), 0, $newKey[1]);
									} else {
										$result = $newKey[1](@$retorno[$key]);
										$b[]    = "{{".$key."}}";
										$c[] = call_user_func_array($newKey[1],array(@$retorno[$newKey[0]]));
									}
							} elseif (count($newKey) > 2) {
								$function 	= $newKey[1];
								$newArrayKeys = array_slice($newKey, 2);
								foreach ($newArrayKeys as $key => $value) {
									if(is_string($value) && !is_numeric($value)){
										$newArrayKeys[$key]  = str_replace("(this)", @$retorno[$key],$value);
									}else{
										$newArrayKeys[$key]  = $value;
									}
								}
								$b[] = "{{".implode(',',$newKey)."}}";
								$c[] = call_user_func_array($function,$newArrayKeys);
							}
						}

					}
				};
				return str_replace($b,$c , $this->template);


			}
			###################################################################################################################################################
			public function show_table()  {
				global $_conectMySQLi_;
				$this->query='SHOW TABLES FROM `'.NOME_BD.'`';
				if($this->debug==true)	{
					$consulta = mysqli_query($_conectMySQLi_,$this->query)or die (_erro(mysqli_error()));
				}else{
					$consulta = @mysqli_query($_conectMySQLi_,$this->query);
				}
				while ($row = mysqli_fetch_array($consulta)) {$this->fetch_array[]= $row;}
				$this->output = $this->query;
			}
			public function primary_key($id)  {$this->primarykey=' PRIMARY KEY ('.$id.')';}
			public function create_table()  {
				global $_conectMySQLi_;
				if($this->table==null && $this->debug==true)		{echo _erro('Sete uma tabela...');exit;}
			//	if(empty($this->setcolum) && $this->debug==true)	{echo _erro('Sete uma coluna...');exit;}
				if($this->primarykey==null)	{$this->primarykey=' PRIMARY KEY (id)';}
				$this->query ="CREATE TABLE IF NOT EXISTS ";
				$this->query .=$this->table." (\n";
				$colunas = array();
				$colunas[]='`id` int(50) NOT NULL AUTO_INCREMENT';
				if(is_array($this->setcolum)){
					foreach ($this->setcolum as $value) { $colunas[] ='`'.$value[0].'` '.$value[1]; } 
				}
				$this->query .=implode(",\n",$colunas); $this->query .=", \n".$this->primarykey;
				$this->query .=' ) ENGINE=MyISAM  AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;';
				if($this->debug==true)	{$consulta = mysqli_query($_conectMySQLi_,$this->query)or die (_erro(mysqli_error()));}else{$consulta = @mysqli_query($_conectMySQLi_,$this->query);}
				$this->output = $this->query;

			}
			public function exclui_table()  {
				global $_conectMySQLi_;
				if($this->table==null && $this->debug==true)	{echo _erro('Sete uma tabela 1...');exit;}
				$this->query='DROP TABLE IF EXISTS '.$this->table;
				if($this->debug==true)	{$consulta = mysqli_query($_conectMySQLi_,$this->query)or die (_erro(mysqli_error()));}else{$consulta = @mysqli_query($_conectMySQLi_,$this->query);}
				$this->output = $this->query;
			}
			public function show_columns()  {
				global $_conectMySQLi_;
				$this->query='SHOW COLUMNS FROM ';
				if($this->table!=null){$this->query .= $this->table;}
				if($this->debug==true)	{$consulta = mysqli_query($_conectMySQLi_,$this->query)or die (_erro(mysqli_error()));}else{$consulta = @mysqli_query($_conectMySQLi_,$this->query);}
				while ($row = mysqli_fetch_assoc($consulta)) {$this->fetch_array[]= $row;}
				$this->output = $this->query;
			}
			public function output()  {return $this->query;}
			public function view($name)  {$this->query .='CREATE OR REPLACE DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW '.$name.' AS  ';}
			public function exclui_column()  {
				global $_conectMySQLi_;
				if($this->table==null && $this->debug==true)	{echo _erro('Sete uma tabela...');exit;}
				if(empty($this->setcolum) && $this->debug==true)	{echo _erro('Sete uma coluna...');exit;}

				if(is_array($this->setcolum)){$this->setcolum = 'DROP `'.implode('`, DROP `',$this->setcolum).'` ';}

				$this->query ='ALTER TABLE `'.$this->table.'` '.$this->setcolum.'; ';
				if($this->debug==true)	{
					$consulta = mysqli_query($_conectMySQLi_,$this->query)or die (_erro(mysqli_error()));
				}else{
					$consulta = @mysqli_query($_conectMySQLi_,$this->query);
				}
				$this->output = $this->query;
				return true;
			}
			public function rename_column()  {
				global $_conectMySQLi_;
				if($this->table==null && $this->debug==true)	{echo _erro('Sete uma tabela...');exit;}
				if(empty($this->setcolum) && $this->debug==true)	{echo _erro('Sete uma coluna...');exit;}

				$this->setcolum = 'CHANGE '.implode(', CHANGE ',$this->setcolum);
				$this->query ='ALTER TABLE '.$this->table.' '.$this->setcolum.' ';
				if($this->debug==true)	{
					$consulta = mysqli_query($_conectMySQLi_,$this->query)or die (_erro(mysqli_error()));
				}else{
					$consulta = @mysqli_query($_conectMySQLi_,$this->query);
				}
				$this->output = $this->query;
				return true;
			}
			public function verify()  {
				global $_conectMySQLi_;
				if(($this->table!=null && $this->table!="") &&(!empty($this->setcolum) && $this->setcolum!="")){
					if(is_array($this->table)){			$this->table = implode('',$this->table);		}
					if(is_array($this->setcolum)){		$this->setcolum = implode('',$this->setcolum);	}
					$result 		= mysqli_query($_conectMySQLi_,"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".$this->table."' AND column_name='".$this->setcolum."'");
					$tableExists 	= mysqli_num_rows($result) ;
					if($tableExists>0){$tableExists=true;}else{$tableExists=false;} return $tableExists;
				}elseif($this->table!=null && $this->table!=""){
					$result = mysqli_query($_conectMySQLi_,"SHOW TABLES LIKE '".$this->table."' ");
					$tableExists = mysqli_num_rows($result)>0; return $tableExists;
				}else{
					_erro("Favor setar uma tabela ou tabela + coluna");
					return false;
				}
				return true;
			}
			public function add_column()  {
				global $_conectMySQLi_;
				if($this->table==null && $this->debug==true)	{echo _erro('Sete uma tabela...');exit;}
				if(empty($this->setcolum) && $this->debug==true)	{echo _erro('Sete uma coluna...');exit;}
				if(is_array($this->setcolum)){
					$rows = array();
					foreach($this->setcolum as $col){
						$col =implode(' ',$col);
						if(isset($col) && $col!="" && $col!=" " ){	$rows[] = $col;}
					}
					$query = implode(', ADD COLUMN ',$rows);
				}else{
					$query = '`'.$this->setcolum.'`';
				}
				$this->query ='ALTER TABLE `'.$this->table.'` ADD COLUMN '.$query.';';
				if($this->debug==true)	{
					 if(@mysqli_query($_conectMySQLi_,$this->query)){
					 	$this->error=null;
					 	return true;
					 	exit;
					 }else{
					 	$this->error=mysqli_error($_conectMySQLi_);
					 	return false;
					 	exit;
					 };
				}else{
					if(@mysqli_query($_conectMySQLi_,$this->query)){
					 	$this->error=null;
						return true;
					 	exit;
					}else{
					 	$this->error=mysqli_error($_conectMySQLi_);
						return false;
					 	exit;
					};
				}
				$this->output = $this->query;
			}

			public function string_replace($value,$key){
				$value = str_replace($this->replace_isso, $this->replace_porisso, $value);
			}

			public function get_query($debug=false)  {
				global $_conectMySQLi_;
				$this->query ='SELECT ';				
				if(!empty($this->DISTINCT))	{$this->query .= $this->DISTINCT;}
				if(!empty($this->colum))	{$this->query .= $this->colum;}else{$this->query .=' * ';} 	$this->query .= ' FROM ';
				if($this->table!=null)	{$this->query .= $this->table;}
				if(!empty($this->rell) && !empty($this->rell)){ $this->query .= $this->rell.'  ';}
				if(count($this->Insert_like)>0){$array_like = implode(' OR ',$this->Insert_like) ;}else{$array_like = "";}
				if( $this->where!=""  ||  (count($this->Insert_like) > 0 ))	{
					if($this->where!="" && $array_like!="" ){$this->where = $this->where." AND ";}
					if($this->set_where_not_exist==true){$not=" NOT EXISTS ";}else{$not="";}
					$this->query .= ' WHERE'.$not.'('.$this->where.'('.$array_like.'))';
					$this->query = str_replace('())',')',$this->query);
				}
				if(!empty($this->group))	{$this->query .= ' GROUP BY '.$this->group.' ';}
				if(!empty($this->order))	{$this->query .= $this->order.' ';}
				if(!empty($this->limit))	{$this->query .= $this->limit;}
				return $this->query;			
			}

			public function process_result($result)  {
				while($row = mysqli_fetch_assoc($result)){
					if($this->utf8 		=='encode')	{$row 	= array_map('utf8_encode',	$row);}
					if($this->utf8 		=='decode')	{$row 	= array_map('utf8_decode',	$row);}
					if($this->url 		=='encode')	{$row 	= array_map('urlencode',	$row);}
					if($this->url 		=='decode')	{$row 	= array_map('urldecode',	$row);}
					if($this->slashes 	=='strip')	{$row 	= array_map('stripslashes',	$row);}
					if($this->slashes 	=='add')	{$row	= array_map('addslashes',	$row);}
					$this->fetch_array[]					= str_replace($this->replace_isso, $this->replace_porisso, $row);
					$this->obj[] 							= (object)str_replace($this->replace_isso, $this->replace_porisso, $row);
				}
			}

			public function select($script=null)  {
				global $_conectMySQLi_;
				if($script!=null){
					if($this->debug==true)	{
					//	echo PHP_EOL.$script.PHP_EOL;
						$consulta = mysqli_query($_conectMySQLi_,$script) or trigger_error(mysqli_error($_conectMySQLi_));
					//	echo PHP_EOL.PHP_EOL.PHP_EOL;
					}else{
						$consulta = @mysqli_query($_conectMySQLi_,$script);
					}
					$this->_num_rows= @mysqli_num_rows($consulta);
					if($consulta && $this->_num_rows>0){
						$this->process_result($consulta);
					}else{
						$this->fetch_array=array();
					}
				}else{
					if($this->debug==true)	{
						$consulta = mysqli_query($_conectMySQLi_,$this->get_query()) or trigger_error(mysqli_error($_conectMySQLi_));
					}else{
						$consulta = @mysqli_query($_conectMySQLi_,$this->get_query());
					}
					$this->_num_rows= @mysqli_num_rows($consulta);

					if($consulta && $this->_num_rows>0){
						$this->process_result($consulta);
					}else{
						$this->fetch_array=array();
					}
					$this->output = $this->query;
				}
				return true;
			}



			public function insert_or_replace(){
				$keyvalue=array();
				foreach ($this->InsertVars as $key => $value) {
					if(is_string($value)){
						if(substr($value,0,8)=="command:"){
							$keyvalue[]= $key.'='.substr($value,8);
						}else{
							$keyvalue[]= $key.'="'.$value.'"';
						}
					}else{
						$keyvalue[]= $key."=".$value;
					}
				}
				$this->on_duplicate=' ON DUPLICATE KEY UPDATE '.implode(',',$keyvalue);
				$this->insert();
			}
			public function insert(){
				global $_conectMySQLi_;
				$this->query='INSERT ';
				if(!empty($this->ignore)){$this->query .= $this->ignore;}
				$this->query .=' INTO ';
				if($this->table!=null)	{$this->query .= $this->table;}else{exit;}
				if(count($this->InsertVars)>0){
					$keyvalue=array();
					foreach ($this->InsertVars as $key => $value) {
						if(is_string($value)){
							if(substr($value,0,8)=="command:"){
								$keyvalue[]= substr($value,8);
							}else{
								$keyvalue[]='"'.$value.'"';
							}
						}else{
							$keyvalue[]= $value;
						}
					}
					$this->query .= ' ( '.implode(',',array_keys($this->InsertVars)).' ) ';

				}else{exit;};
				$this->query .= ' VALUES ';
				if(count($this->InsertVars)>0){
					$this->query .= ' ('.implode(',',$keyvalue).') ';

				}else{exit;};



				if($this->set_where_not_exist==true){$not=" NOT EXISTS ";}else{$not="";}
				if(!empty($this->where))	{$this->query .= ' WHERE'.$not.'('.$this->where.')';}
				if(!empty($this->on_duplicate))	{$this->query .= $this->on_duplicate;}
				if($this->debug==true)	{
					if(mysqli_query($_conectMySQLi_,$this->query) or trigger_error(mysqli_error($_conectMySQLi_))){return true;}
				}else{
					if(@mysqli_query($_conectMySQLi_,$this->query)){return true;};
				}
			}
			public function salvar() {
				global $_conectMySQLi_;
				$this->query='UPDATE ';
				if($this->table!=null)	{$this->query .= $this->table;}else{exit;}
				$this->query .= ' SET ';
				if(count($this->Insert_Update)>0){


					$this->query .= implode(',',$this->Insert_Update);
				}else{
					return true;
				};

				if($this->set_where_not_exist==true){$not=" NOT EXISTS ";}else{$not="";}
				if(!empty($this->where))	{$this->query .= ' WHERE'.$not.'('.$this->where.')';}
				if($this->debug==true)	{
					if(mysqli_query($_conectMySQLi_,$this->query) or trigger_error(mysqli_error($_conectMySQLi_))){return true;}
				}else{
					if(@mysqli_query($_conectMySQLi_,$this->query)){return true;}
				}
			}
			public function exclui(){
				global $_conectMySQLi_;
				$this->query='DELETE FROM ';
				if(!empty($this->table))	{$this->query .= $this->table;}else{exit;}
				if(!empty($this->where))	{$this->query .= ' WHERE ('.$this->where.')';}else{exit;}
				if($this->debug==true)	{
					if(mysqli_query($_conectMySQLi_,$this->query) or trigger_error(mysqli_error($_conectMySQLi_))){return true;}
				}else{
					if(@mysqli_query($_conectMySQLi_,$this->query)){return true;}
				}
				echo $this->query;
			}
	}



?>