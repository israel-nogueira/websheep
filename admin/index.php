<?php

/*
########################################################################################
#     SEJA BEM VINDO AO WEBSHEEP!
########################################################################################
Este arquivo é parte do Websheep CMS
Websheep é um software livre; você pode redistribuí-lo e/ou
modificá-lo dentro dos termos da Licença Pública Geral GNU como
publicada pela Fundação do Software Livre (FSF); na versão 3 da
Licença, ou qualquer versão posterior.

Este programa é distribuído na esperança de que possa ser  útil,
mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO
a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR.

Veja a Licença Pública Geral GNU para maiores detalhes.
Você deve ter recebido uma cópia da Licença Pública Geral GNU junto
com este programa, Se não, veja <http://www.gnu.org/licenses/>.
 */
ob_start();

############################################################################################################################################
# DEFINIMOS TEMPORARIAMENTE O ROOT DO SISTEMA
############################################################################################################################################
$_PATHADMIN = realpath(dirname(__FILE__));

############################################################################################################################
# CASO NÃO TENHA SIDO VERIFICADO OU SEJA UMA NOVA INSTALAÇÃO/UPDATE IMPORTA VERIFICAÇÃO DO SERVIDOR
############################################################################################################################
if (
    !file_exists($_PATHADMIN . '/app/config/ws-server-ok')
) {
    include_once $_PATHADMIN . '/app/config/ws-verify-server.php';
}

############################################################################################################################################
# IMPORTAMOS AS FUNÇÕES GLOBAIS
############################################################################################################################################
include_once $_PATHADMIN . '/app/lib/ws-globals-functions.php';

############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################
if (!defined("ROOT_WEBSHEEP")) {
    define('ROOT_WEBSHEEP', "/");
    // $path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
    // $path = implode(array_filter(explode('/',$path)),"/");
    // define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}

if (!defined("INCLUDE_PATH")) {define("INCLUDE_PATH", normalizePath(realpath(dirname(__FILE__)) . '/../'));}

############################################################################################################################
# CASO NÃO EXISTA O 'ws-config.php' IMPORTA A TELA DE SETUP
############################################################################################################################
if (!file_exists($_PATHADMIN . '/../ws-config.php')) {
    include_once $_PATHADMIN . '/app/core/ws-setup.php';
    exit;
}

############################################################################################################################################
# ANTES DE TUDO, VERIFICA SE JÁ TEMOS AS VARIÁVEIS NO HTACCESS E SE ESTÃO CORRETAS
############################################################################################################################################
include_once $_PATHADMIN . '/app/lib/ws-refresh-paths.php';

############################################################################################################################
# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
############################################################################################################################
include_once $_PATHADMIN . '/app/lib/class-ws-v1.php';

############################################################################################################################
#    CASO SEJA O 1° ACESSO, IMPORTA A TELA DE INSTALAÇÃO
############################################################################################################################
if (file_exists($_PATHADMIN . '/app/config/firstacess') && file_get_contents($_PATHADMIN . '/app/config/firstacess') == 'true') {
    include_once $_PATHADMIN . '/app/core/ws-install.php';exit;
}

############################################################################################################################
#    CASO ESTEJA LOGADO DIRETAMENTE COM ACCESSKEY
############################################################################################################################
if (ws::urlPath(2, false)) {
    $keyAccess = ws::getTokenRest(ws::urlPath(2, false), false);

    ####################################################################################################################
    #    CASO O ACCESSKEY ESTEJA LIGADA DIRETAMENTE A UM ELEMENTO
    #    Por segurança, só libera o acesso se tiver o keyAccess nas duas tabelas
    ####################################################################################################################
    $ws_direct_access = new MySQL();
    $ws_direct_access->set_table(PREFIX_TABLES . 'ws_direct_access');
    $ws_direct_access->set_where('keyaccess="' . ws::urlPath(2, false) . '"');
    $ws_direct_access->select();
    $_num_rows = $ws_direct_access->_num_rows;
    $authKey = (isset($_num_rows) && $_num_rows > 0 && $keyAccess) ? true : false;
} else {
    $authKey = false;
}

############################################################################################################################
#    CASO ESTEJA LOGADO IMPORTAMOS O DESKTOP
############################################################################################################################
session::__start();
$_existe_key_verdadeira = (isset($authKey) && $authKey == true);
$_esta_logado = (session::__verifyLog() == true && session::ws_log() != null);
$_SEGURO = (SECURE == false);
$_SEGURO = session::usuario();

// 	  [id] => 1
// [id_status] => 0
// [token] => YxPAXLzwlRvq2bw7FNbg
// [nome] => Administrador
// [usuario] => admin
// [admin] => 1
// [ativo] => 1
// [add_user] => 0
// [edit_only_own] => 0
// [leitura] => 0
// [hora] => 1637691316
// [ws_log] => 1
//  echo '<pre>';
//  print_r(session::__getAllSessions());
//  	echo '</pre>';
//exit();

if ($_existe_key_verdadeira || $_esta_logado || $_SEGURO) {
    include_once $_PATHADMIN . '/app/core/ws-dashboard.php';
    exit;
}

############################################################################################################################
#    CASO ESTEJA OFFLINE JÁ DIRECIONA PRO LOGIN
############################################################################################################################
include_once $_PATHADMIN . '/app/ws-modules/ws-login/index.php';
