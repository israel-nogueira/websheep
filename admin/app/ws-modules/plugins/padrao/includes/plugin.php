<?
/*
	Este é o arquivo base de um plugin;	
	Nele você poderá buscar todas as variáveis do seu plugin utilizando objetos simples por exemplo:
	Para buscar as variáveis no shortcode incluso basta buscar por: 
	$ws->vars->sua_variavel

	Para buscar as variáveis que  você incluiu no PHP de configuração, basta buscar por: 
	$ws->json

	Path de instalação do plugin:
	$ws->pathPlugin

	Caso queira dar um include em um aruqivo php do ROOT, utilize o seguinte path:
	$ws->includePath

	Para acessar diretamente um arquivo pela URL, utilize o seguinte path:
	$ws->rootPath

	Por exemplo, precisar mostrar uma imagem:
	<?=$ws->rootPath?>/assets/images/avatar.jpg
	<?=$ws->rootPath?>/assets/css/style.css

	Caso esse arquivo seja exibido separadamente em outra janela ou popup, será necessário descomentar as 2 linhas a seguir:
*/
?>
<style>
	.boxPlugin{
		position: relative;
		background: #609bbf;
		color: #FFF;
		float: left;
		padding: 10px;
		font-size: 12px;
		text-align: center;
		width:<?=$ws->vars->width?>px;
	}
</style>
<div class="boxPlugin">
	<img src="<?=$ws->rootPath.$ws->pathPlugin?>/assets/images/avatar.png">
	<?=$ws->innertext?>	
</div>


