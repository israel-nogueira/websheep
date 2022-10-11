<?
exit;
//Listando tabelas:
$D			= new MySQL();
$D->show_table();
print_r($D->fetch_array);
//Listando as colunas de uma tabela:

$D			= new MySQL();
$D->set_table('tabela');
$D->show_columns();
print_r($D->fetch_array);
// Exclui uma tabela:
$s= new MySQL();
$s->set_table('tabela');
$s->exclui_table();
// Criando uma tabela:
$s= new MySQL();
$s->set_table('tabela');
$s->set_colum(array('id' 		,'int(50) NOT NULL AUTO_INCREMENT'));
$s->set_colum(array('coluna1'	,'varchar(20) NOT NULL'));
$s->set_colum(array('coluna2' 	,'varchar(200) NOT NULL'));
$s->create_table();
// Adicionando colunas a uma tabela:
$s= new MySQL();
$s->set_table('tabela');
$s->set_colum(array('id' 		,'int(50) NOT NULL AUTO_INCREMENT'));
$s->set_colum(array('coluna1'	,'varchar(20) NOT NULL'));
$s->set_colum(array('coluna2' 	,'varchar(200) NOT NULL'));
$s->add_column();
// Excluir colunas a uma tabela:
$s= new MySQL();
$s->set_table('tabela');
$s->set_colum('id');
$s->set_colum('coluna1');
$s->set_colum('coluna2');
$s->exclui_column();
##################################################  VERIFICAR SE TABELA EXISTE (retorna true / false )
$s= new MySQL();
$s->set_table('tabela');
echo $s->verify();
##################################################  VERIFICAR SE COLUNA EXISTE EM UMA TABELA (retorna true / false )
$s= new MySQL();
$s->set_table('tabela');
$s->set_colum('coluna');
echo $s->verify();


################################################# Imagens

$s 		= new MySQL();
$s->set_slug('string');
$s->set_gal();
$s->set_img();
$s->select();

################################################# SELECT
$s 					= new MySQL();
//  setamos uma ou mais colunas
$s->set_table('tabela_a as apelido_a');
//  Caso nao tenha nenhuma coluna, valor padrão é *
$s->set_colum('coluna1');
$s->set_colum('coluna2');
//  ORDER BY
$s->set_order('id','ASC');
// WHERE - colocar OR ou AND caso tenha mais que uma condicional
$s->set_where('id="25"');
$s->set_where('AND id="11"');
$s->set_where('OR id="2"');
//SELECT DISTINCT - eliminamos resultados duplicados
$s->distinct();
//função php utf8_encode ou utf8_decode ( encode ou decode )
$s->utf8('encode');
//função php urlencode ou urldecode ( encode ou decode )
$s->url('encode');
//adicionar ou retirar barra invertida das strings ( strip ou add )
$s->slashes('strip');
// Habilita ou desabilita erros da query " or die() " (true ou false   0 ou 1)
$s->debug(false);
// definimos um template para ser resgatado posteriormente com o nome da coluna MySQL dentro de {{}}
$s->set_template("blablabla {{coluna}} nonono <b>{{coluna}}</b> blablabla<br>");
// limita a busca, 1 - num inicial e 2- quantidade que será retornada
$s->set_limit(1,2);
// LIKE, para pesquisa ( (1= like 2=NOT LIKE),coluna, keywork )
$s->like (1,'nome','%string%');
// FUNÇÕES DE JOIN - pode-se adicionar multiplos joins
$s->join('tipo','tabela as apelido','where');
$s->join('INNER',	'tabela_b as apelido_b',	'apelido_b.id = apelido_a.id');
$s->join('LEFT',	'tabela_c as apelido_c',	'apelido_c.campo = apelido_b.campo');
// e finalmente damos o select para executar a query
$s->select();
// depois do select, retorna a query executada
print_r($s->output());
// depois do select, retorna numero de resultados
$s->_num_rows;
// depois do select, retorna o resultado da query em forma de array
print_r($s->fetch_array);
// depois do select, é possivel resgatar um item expecífico da array:
echo $s->fetch_array[1]['coluna'];
echo $s->fetch_array[2]['coluna'];
//Listando os resultados.
foreach ($s->fetch_array as $value) {
	echo $value['coluna1'];
	echo $value['coluna2'];
	echo $value['coluna3'];
}
// Para resgatar o template é assim.
$s->get_template($s->fetch_array[0]);
// Ou se estiver em um foreach.
foreach ($s->fetch_array as $value) {
$s->get_template($value);
}
// Alterar um dado antes de retornar o template.
foreach ($s->fetch_array as $value) {
	if($value['coluna']==""){$value['coluna']="nova variavel";}
	$s->get_template($value);
}
##################################################### INSERT
$I 					= new MySQL();
//seta a tabela
$I->set_table('usuarios');
// seta as colunas com os valores
$I->set_insert('coluna1','string');
$I->set_insert('coluna2','string');
$I->set_insert('coluna3','string');
// formata as entradas
$I->utf8('encode');
$I->url('encode');
$I->slashes('strip');
// caso tenha duplicidade ele ignora e nao retorna nada
$I->ignore();
// insere
$I->insert();
##################################################### ON DUPLICATE
$I 					= new MySQL();
//seta a tabela
$I->set_table('usuarios');
// seta as colunas com os valores
$I->set_insert('id','1');
$I->set_insert('coluna1','string');
// caso existir duplicidade, ele substitui o valor da coluna para..
$I->on_duplicate('id=id+1');
// e finalmente insere
$I->insert();
##################################################### INSERT OR REPLACE
// Caso se nao existir duplicidade, adiciona, se tiver substitui
$I 					= new MySQL();
//seta a tabela
$I->set_table('usuarios');
// seta as colunas com os valores
$I->set_insert('id','1');
$I->set_insert('coluna1','string');
// insere
$I->insert_or_replace();
##################################################### UPDATE
$U					= new MySQL();
$U->set_table('usuarios');
$U->set_where('id="50"');
$U->set_update('coluna1','var');
$U->set_update('coluna2','var');
$U->set_update('coluna3','var');
//formata as entradas
$U->utf8('encode');
$U->url('encode');
$U->slashes('strip');
// salva
$U->salvar();
##################################################### EXCLUIR
$D			= new MySQL();
$D->set_where('id="50"');
$D->set_table('usuarios');
$D->exclui();