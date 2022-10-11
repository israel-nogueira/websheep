<?
/*	Este é o arquivo base de um plugin;	
	Nele você poderá buscar todas as variáveis do seu plugin utilizando objetos simples por exemplo:
	Para buscar as variáveis no shortcode incluso basta buscar por: 
	$ws->vars

	Para buscar as variáveis que  você incluiu 
	no JSON ou no PHP de configuração, basta buscar por: 
	$ws->json
	Aqui você pode fazer pesquisar en ferramentas, utilizar as tags <ws> ou as classes padrões do sistema

	Para acessar diretamente um arquivo pela URL, utilize o seguinte path:
	http://exemplo.com/<?=$ws->rootPath?>/arquivo

	Por exemplo, precisar mostrar uma imagem:
	http://exemplo.com/<?=$ws->rootPath?>/img/avatar.jpg

	Caso esse arquivo seja exibido separadamente em outra janela ou popup, será necessário descomentar as 2 linhas a seguir:
*/


// include($_SERVER["DOCUMENT_ROOT"]."/admin/app/lib/class-ws-v1.php");
// ws::processPluginData();


?>
<h1>Painel do novo plugin</h1>