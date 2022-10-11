<?
############################################################################################
# 
# 	EXEMPLO BÁSICO DE UM RETORNO DE SHORT CODE
# 		utilização:     
#		<ws-shortcode function="my-shortcode" before="<strong>" after="</strong>">
#			Um conteúdo qualquer
#		</ws-shortcode>
#
#	Obrigatoriamente deverá ter um parametro function='' com o nome do arquivo
#	Nesse caso estamos utilizando function="my-shortcode"
#	Como padrão será retornado um objeto para manipulação  
#
#	$ws->attributes serão os atributos em tua TAG html
#	$ws->innertext Será o HTML dentro da tag
#
#	Exemplo abaixo: 
#
############################################################################################


echo $ws->attr->before.$ws->innertext.$ws->attr->after;