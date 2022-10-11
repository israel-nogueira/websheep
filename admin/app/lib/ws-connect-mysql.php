<?PHP
$_conectMySQLi_ 	=	mysqli_connect(SERVIDOR_BD, USUARIO_BD, SENHA_BD,NOME_BD);
mysqli_query($_conectMySQLi_,'SET NAMES "utf8"'); 
mysqli_query($_conectMySQLi_,'SET character_set_connection=utf8'); 
mysqli_query($_conectMySQLi_,'SET character_set_client=utf8'); 
mysqli_query($_conectMySQLi_,'SET character_set_results=utf8');  
mysqli_query($_conectMySQLi_,'NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES');  
if (mysqli_connect_errno()) {printf("FALHA DE CONEXÃO: %s\n", mysqli_connect_error());exit();}