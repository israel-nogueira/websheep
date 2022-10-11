<?
############################################################################################
#	@title ShortCode  padrão
#	@tag01 Tag inserida antes
#	@tag02 tag inserida após
# 
# 	EXEMPLO BÁSICO DE UM RETORNO DE SHORT CODE
# 		utilização:     
#		<ws-shortcode function="basic" class="minha classe">
#			Um conteúdo qualquer
#		</ws-shortcode>
#
#	Obrigatoriamente deverá ter um parametro function='' com o nome da sua função
#	Nesse caso estamos utilizando function="basic"
#	Como padrão será retornado uma array para manipulação  
#
#	array['attributes'] serão os atributos em tua TAG html
#	array['innertext'] Será o HTML dentro da tag
#
#	Exemplo abaixo: 
############################################################################################
function basic($params){
	return '<strong class="'.$params['attributes']['class'].'">'.$params['innertext']."</strong>";
}

 	

?>