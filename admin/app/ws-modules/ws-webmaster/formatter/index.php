<?

############################################################################################################################################
# DEFINIMOS O ROOT DO SISTEMA
############################################################################################################################################
	if(!defined("ROOT_WEBSHEEP"))	{
	$path = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'admin'));
	$path = implode(array_filter(explode('/',$path)),"/");
	define('ROOT_WEBSHEEP',(($path=="") ? "/" : '/'.$path.'/'));
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="UTF-8">
	<link rel="shortcut icon" href="//dpidudyah7i0b.cloudfront.net/favicon.ico" type="image/x-icon">
	<link href="//dpidudyah7i0b.cloudfront.net/devops/plugins/bootstrap/bootstrap.css" rel="stylesheet">
	<link href="//dpidudyah7i0b.cloudfront.net/devops/css/style.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/codemirror.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/formatting.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/beautify-html.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/beautify.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/beautify-css.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/javascriptobfuscator_unpacker.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/urlencode_unpacker.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/p_a_c_k_e_r_unpacker.js"></script>
	<script src="<?=ROOT_WEBSHEEP?>admin/app/vendor/formatCode/myobfuscate_unpacker.js"></script>
</head>

<body>
	<!--End Header-->
	<!--Start Container-->
		<div class="row">
			<div>
				<div class="box-content" style="margin-top: 0;top: -20px;position: relative;padding: 20px;width: 100%;left: 0;float: left;">
					<form>
						<div class="row">
							<div class="col-sm-4 col-md-3 col-lg-2">
								<select name="tabsize" id="tabsize" class="form-control">
									<option value="1">Formatar com TAB</option>
									<option value="2">Formatar com 2 espaços</option>
									<option value="3">Formatar com 3 espaços</option>
									<option value="4">Formatar com 4 espaços</option>
									<option value="8">Formatar com 8 espaços</option>
								</select>
								<br />
								<select name="max-preserve-newlines" id="max-preserve-newlines" class="form-control">
									<option value="1">Permitir 1 linha depois das tags</option>
									<option value="2">Permitir 2 linhas depois das tags</option>
									<option value="5">Permitir 5 linhas depois das tags</option>
									<option value="10">Permitir 10 linhas depois das tags</option>
									<option value="0">Permitir ilimitadas linhas depois das tags</option>
									<option value="-1">Remover todas as linhas extras</option>
								</select>
								<br />

								<select name="wrap-line-length" id="wrap-line-length" class="form-control">
									<option value="0">Não quebrar linhas</option>
									<option value="40">Quebrar linhas depois de 40 caracteres</option>
									<option value="70">Quebrar linhas depois de 70 caracteres</option>
									<option value="80">Quebrar linhas depois de 80 caracteres</option>
									<option value="110">Quebrar linhas depois de 110 caracteres</option>
									<option value="120">Quebrar linhas depois de 120 caracteres</option>
									<option value="160">Quebrar linhas depois de 160 caracteres</option>
								</select>
								<br />

								<select id="brace-style" class="form-control">
									<option value="collapse">Chaves '{' na mesma linha</option>
									<option value="expand">Pular linha nas chaves '{' </option>
									<option value="end-expand">Pular linha nas chaves '{' no final</option>
								</select>
								<br />

								<p style="margin:6px 0 0 0" class="pull-left"><b><u>&lt;style/&gt;, &lt;script/&gt; dentro do HTML:</u></b>
								</p>
								<select id="indent-scripts" class="form-control">
									<option value="keep">Pular indentação padrão</option>
									<option value="normal">Indentação normal</option>
									<option value="separate">Indentação separada</option>
								</select>
								<label style="width: 100%;" for="end-with-newline">
									<input type="checkbox" id="end-with-newline"> Nova linha após o script e style?
								</label>
								<label style="width: 100%;" for="keep-array-indentation">
									<input type="checkbox" id="keep-array-indentation"> Manter indentação de arrays?
								</label>
								<label style="width: 100%;" for="break-chained-methods">
									<input type="checkbox" id="break-chained-methods"> Nova linha em funções encadeadas?
								</label>
								<label style="width: 100%;" for="space-before-conditional">
									<input type="checkbox" id="space-before-conditional"> Espaço em condicionais: "if(x)" / "if (x)"</label>
								<label style="width: 100%;" for="unescape-strings">
									<input type="checkbox" id="unescape-strings"> Unescape caracteres especiais?
								</label>
							</div>
						</div>
					</form>
				</div>


				<script>
								function beautify () {
									var source = window.top.htmEditor.getSession().getValue();
									var output = "";
									var opts = {};
									opts.indent_size = $('#tabsize').val();
									opts.indent_char = opts.indent_size == 1 ? '\t' : ' ';
									opts.max_preserve_newlines = $('#max-preserve-newlines').val();
									opts.preserve_newlines = opts.max_preserve_newlines !== "-1";
									opts.keep_array_indentation = $('#keep-array-indentation').prop('checked');
									opts.break_chained_methods = $('#break-chained-methods').prop('checked');
									opts.indent_scripts = $('#indent-scripts').val();
									opts.brace_style = $('#brace-style').val();
									opts.space_before_conditional = $('#space-before-conditional').prop('checked');
									opts.unescape_strings = $('#unescape-strings').prop('checked');
									opts.jslint_happy = $('#jslint-happy').prop('checked');
									opts.end_with_newline = $('#end-with-newline').prop('checked');
									opts.wrap_line_length = $('#wrap-line-length').val();
									output = html_beautify(source, opts);

									// if (looks_like_html(source)) {
									// output = css_beautify(source);
									// source = unpacker_filter(source);
									// output = js_beautify(source, opts);
									//   }
									window.top.htmEditor.getSession().setValue(output)
								}
								function looks_like_html(source) {
									var trimmed = source.replace(/^[ \t\n\r]+/, '');
									var comment_mark = '<' + '!-' + '-';
									return(trimmed && (trimmed.substring(0, 1) === '<' && trimmed.substring(0, 4) !== comment_mark));
								}

					$(function() {
						$("#cap").bind("click tap press",function(){
							beautify();
						})
								$('select').change(beautify);
							
							});



				</script>

</body>

</html>