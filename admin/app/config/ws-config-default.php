<?
	ob_start();
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

/**
 * As configurações básicas do WebSHeep
 *
 * O script de criação ws-config.php usa esse arquivo durante a instalação.
 * Você não precisa user o site, você pode apenas preencher manualmente os valores e salvar o arquivo como ws-config.php
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo das tabelas da base de dados
 */

	if(!defined("DOMINIO"))		define("DOMINIO",		"{DOMINIO}");
	if(!defined("LANG"))		define("LANG", 			"{LANG}");
	if(!defined("CHARSET"))		define("CHARSET", 		"utf-8");
	if(!defined("TIMEZONE"))	define("TIMEZONE", 		"America/Sao_Paulo");

####################################################################################
# DEFINIMOS O PATH DE INSTALAÇÃO DO WEBSITE
####################################################################################
	class wsconfig{
		const adminPath 	='admin';
		const includePath 	='{{INCLUDE_PATH}}';
		const rootPath 		='{{ROOT_WEBSHEEP}}';
		const domain 		="{{domain}}";
	}

 
####################################################################################
# UTILIZE FALSE ESTRITAMENTE PARA DESENVOLVIMENTO!!!!
####################################################################################
	if(!defined("SECURE"))		define("SECURE", TRUE);  //FALSE ESTRITAMENTE PARA DESENVOLVIMENTO!!!!

######################################################################
# Aqui vai o token do seu site para o Recaptcha do Google
# https://www.google.com/recaptcha/intro/invisible.html
# Clique no botão azul escrito [GET A KEY] 
######################################################################
	if(!defined("RECAPTCHA"))		define("RECAPTCHA", 		"{RECAPTCHA}");  

######################################################################
# Aqui vai o token do seu site para a API do GoogleFonts
# https://developers.google.com/fonts/docs/developer_api?hl=pt
######################################################################	
	if(!defined("GOOGLE_FONTS_KEY"))define("GOOGLE_FONTS_KEY", 	"");  

######################################################################	
	if(!defined("PREFIX_TABLES"))	define("PREFIX_TABLES", 	"{PREFIX_TABLES}");
	if(!defined("NOME_BD"))			define("NOME_BD", 			"{NOME_BD}");
	if(!defined("USUARIO_BD"))		define("USUARIO_BD",		"{USUARIO_BD}");
	if(!defined("SENHA_BD"))		define("SENHA_BD",			"{SENHA_BD}");
	if(!defined("PHP_MY_ADMIN"))	define("PHP_MY_ADMIN",		"{PHP_MY_ADMIN}");
	if(!defined("SERVIDOR_BD"))		define("SERVIDOR_BD", 		"{SERVIDOR_BD}");
	if(!defined("ADMIN_PATH"))		define("ADMIN_PATH", 		"admin");
	if(!defined("ADMIN_SALT_PASS_SESSION"))	define("ADMIN_SALT_PASS_SESSION", 		"mpiuht450g87h540ghw0gh90h54g");

############################################
#	DEFINE CHARSET
############################################
@header('Content-Type: charset=utf-8'); 

/**#@+
 *
 * Chaves únicas de autenticação e salts.
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link api.websheep.com.br/ws-config-secret-key}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. 
 * Isto irá forçar todos os usuários a fazerem login novamente.
 *
 */
if(!defined("LOGGED_IN_SALT"))		define('LOGGED_IN_SALT',	'{LOGGED_IN_SALT}');
if(!defined("ID_SESS"))				define("ID_SESS",			'{ID_SESS}');

if(!defined("NAME_SESS"))			define("NAME_SESS",			'{NAME_SESS}');
if(!defined("TOKEN_DOMAIN"))		define("TOKEN_DOMAIN",		'{TOKEN_DOMAIN}');
if(!defined("TOKEN_ACCESS"))		define("TOKEN_ACCESS",		'{TOKEN_ACCESS}');
if(!defined("TOKEN_USER"))			define("TOKEN_USER",		'{TOKEN_USER}');
if(!defined("AUTH_KEY"))			define('AUTH_KEY',			'{AUTH_KEY}');
if(!defined("SECURE_AUTH_KEY"))		define('SECURE_AUTH_KEY',	'{SECURE_AUTH_KEY}');
if(!defined("LOGGED_IN_KEY"))		define('LOGGED_IN_KEY',		'{LOGGED_IN_KEY}');
if(!defined("NONCE_KEY"))			define('NONCE_KEY',			'{NONCE_KEY}');
if(!defined("AUTH_SALT"))			define('AUTH_SALT',			'{AUTH_SALT}');
if(!defined("SECURE_AUTH_SALT"))	define('SECURE_AUTH_SALT',	'{SECURE_AUTH_SALT}');
if(!defined("NONCE_SALT"))			define('NONCE_SALT',		'{NONCE_SALT}');


############################################
#	VERSÃO DO PHP REQUERIDO
############################################
if(!defined("php_version")) 	{
	define('php_version',file_get_contents(wsconfig::includePath.wsconfig::adminPath.'/app/templates/txt/ws-php-version.txt'));
}

############################################
#	DEFINE O IDIOMA DO ADMIN
############################################
// TRADUÇÃO ANTIGA
if( !defined( '__LANG__' ) ){
	define( '__LANG__', str_replace(array(PHP_EOL,"\n","\r"),"",file_get_contents(wsconfig::includePath.wsconfig::adminPath.'/app/templates/json/ws-lang.json')));
}

// TRADUÇÃO NOVA (em desenvolvimento ainda)
if( !defined( 'wslang' ) )	define( 'wslang', str_replace(array(PHP_EOL,"\n","\r"),"",file_get_contents(wsconfig::includePath.wsconfig::adminPath.'/app/config/lang/'.LANG.'.json')) );

####################################################################################
# VERIFICAMOS SE O ACESSO ESTA SENDO FEITO PELO NOSSO SERVIDOR
####################################################################################	
	if ("POST" == $_SERVER["REQUEST_METHOD"]) {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
			if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){ 
				exit("CSRF protection in POST request");
			}
		}else{
			exit("CSRF protection in POST request");
		}
	}


















