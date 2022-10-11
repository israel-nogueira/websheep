<?
	ob_start();
	include(__DIR__.'/../../lib/ws-globals-functions.php');
	include(__DIR__.'/../../lib/class-ws-v1.php');


	$list = glob_recursive(INCLUDE_PATH.'website/assets/js/*.js');
	$listJS = array();
	foreach($list as $js){
		$listJS[] = str_replace(INCLUDE_PATH.'website',"", $js);
	};

	$list = glob_recursive(INCLUDE_PATH.'website/assets/css/*.css');
	$listCSS= array();
	foreach($list as $css){
		$listCSS[] = str_replace(INCLUDE_PATH.'website',"", $css);
	};

	$list = glob_recursive(__DIR__.'/plugins/*.json');
	$template_blocks = array();
	foreach ($list as $value) {
		$json = json_decode(file_get_contents($value));
		$str =   '{'.PHP_EOL
				.'	id:"'.$json[0]->hash.'",'.PHP_EOL
				.'	label:"'.$json[0]->name.'",'.PHP_EOL
				.'	content:"'.addslashes($json[0]->html).'",'.PHP_EOL
				.'	category:"Plugins",'.PHP_EOL
				.'	attributes: {'.PHP_EOL
				.'		"class":"plugin-externo",'.PHP_EOL
				.'		"data-name":"'.addslashes($json[0]->name).'",'.PHP_EOL
				.'		"data-description":"'.addslashes($json[0]->description).'",'.PHP_EOL
				.'		"data-avatar":"'.addslashes($json[0]->avatar).'",'.PHP_EOL
				.'		"data-author":"'.addslashes($json[0]->author).'",'.PHP_EOL
				.'		"data-version":"'.addslashes($json[0]->version).'"'.PHP_EOL
				.'	}'
				.'}';
		$template_blocks[] = $str;
	}
	if(count($template_blocks)>0){$template_blocks = implode($template_blocks,',').',';}else{$template_blocks="";}



?>


	<style>
	::-webkit-scrollbar {
		width:  0.3em;
		height: 0.3em;
	}
	::-webkit-scrollbar-track {
		background-color: #0f141a;
	}
	::-webkit-scrollbar-thumb {
		background: #4a5c6d;
	}
	::-webkit-scrollbar-corner,::-webkit-scrollbar-thumb:window-inactive {
		background: #4a5c6d;
	}

	.api {
		height: 100%;
		border: 0;
		border-radius: 4px;
		background-color: #252f3b;
	}

	.example-4, .example-5 {
		height: 400px;
	}

	.split p {
		padding: 20px;
	}

	.split {
		-webkit-box-sizing: border-box;
			 -moz-box-sizing: border-box;
						box-sizing: border-box;

		overflow-y: auto;
		overflow-x: hidden;
	}

	.gutter {
		background-color: #13171e;
		background-repeat: no-repeat;
		background-position: 50%;
	}

	.gutter.gutter-horizontal {
		background-image: url('http://nathancahill.github.io/Split.js/grips/vertical.png');
		cursor: ew-resize;
	}

	.gutter.gutter-vertical {
		background-image: url('http://nathancahill.github.io/Split.js/grips/horizontal.png');
		cursor: ns-resize;
	}

	.split.split-horizontal, .gutter.gutter-horizontal {
		height: 100%;
		float: left;
	}

/*#################################### GRAPESJS ########################################### */
	.gjs-one-bg {
		background-color: #252f3b;
	}
	.gjs-cv-canvas{
		left: 50%;
		transform: translateX(-50%);
		height: calc(100% - 10px);
		top: 2px;
		width: 100%;
    }
	.gjs-layers {
		position: relative;
		height: auto;
	}
	#graspePrincipal{}
	#menu-graspe-topo{
		position: relative;
		float: inherit;
		background-color: #252f3b;
		height: 40px;
	}
	#panel_devices{
		float: right;
		right: 0px;
		position: relative;
	}
	.gjs-block .gjs-block-label{
		font-family: 'Titillium Web',sans-serif;
		padding: 4px;
		text-align: left;
		padding: 4px 21px;
		text-align: left;
		font-size: 13px;
		font-weight: 100;
	}
	.gjs-block{
		width: 100%;
		height: auto;
		min-height: 0;
		margin: 0;
		border: 0;
		padding: 4px;
   		border-bottom: dotted 1px #4a5c6d;
	}
/*#######################################  ACE EDITOR ################################################# */
	.aceEditorHTML,.aceEditorCSS,.aceEditorJS{
		position: inherit;
		overflow: auto;
		top: 0;
		width: 100%;
		height: calc(100% - 30px);
	}
	#aceEditorHTML,#aceEditorCSS,#aceEditorJS{
		position: relative;
		top: 0;
		left: 0;
		width: 100%;
		min-height: 100%!important;
	}
	.studioweb,#layers,#styles{
		font-family: 'Titillium Web',sans-serif;
	}
	#assets .file{
		position: relative;
		padding: 10px;
		color: #CCC;
		border-bottom: dotted 1px #394453;
		cursor: default;
	}
	#assets .cabecalho{
		cursor: pointer;
		position: relative;
		float: inherit;
		height: 20px;
		background-color: #212a35;
		color: #b8c1cc;
		padding: 10px;
		border-bottom: solid 1px #1a212a;
	}
	.containerAba{
		position: relative;
		height: 30px;
		box-shadow: inset 0px 0px 20px rgb(0, 0, 0);
		background-color: #00000078;
	}
	.ws-editor-aba.active {
		background-color: #3e576f;
		color: #FFF;
	}

	#base-editor-1{
		overflow: hidden;
	}
	#base-editor-1 #aceEditorHTML{
		position: relative;
	}

	.ws-editor-aba.active 	.ws-editor-status{background-position-x: -24px;}
	.ws-editor-aba.preload 	.ws-editor-status{background-position-x: -17px;}
	.ws-editor-aba.unsave 	.ws-editor-status{background-position-x: -8px;}

	.ws-editor-aba .ws-editor-status {
		position: absolute;
		top: 7px;
		left: 6px;
		background-image: url(./app/templates/img/websheep/tabsave.png);
		background-position-x: 0;
		width: 7px;
		height: 7px;
	}

	.ws-editor-aba .ws-editor-label {
		position: relative;
		top: -3px;
	}
	.ws-editor-aba {
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		cursor: pointer;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
		color: #6c93b8;
		background-color: #293747;
		position: relative;
		float: left;
		height: 11px;
		width: auto;
		top: 7px;
		padding: 6px 10px;
		padding-left: 20px;
		left: 8px;
		box-shadow: inset 0px -5px 10px 0px #000000a1;
	}

</style>



		<div class="studioweb">
				<div id="avatar-plugin-externo" style="position: absolute;left: 243px;z-index: 30;width: 260px;background-color: #000;height: 250px;top: 10px;padding: 10px;">
					<div class="avatar"><img src="" width="260" /></div>
					<div class="name" style="color: #FFF; font-size: 20px; text-align: left; margin: 10px; "></div>
					<div class="description" style="color: #FFF; font-size: 15px; text-align: left; margin: 10px; ">></div>
				</div>

				<div class="api">
					<div id="left" class="split split-horizontal">
						<div id="base-left-1" class="split split-vertical">
							<div id="blocks"></div>
						</div>
						<div id="base-left-2" class="split split-vertical">
							<div id="layers"></div>
						</div>
					</div>
					<div id="center" class="split split-horizontal">
						<div id="base-center-1" class="split split-vertical">
							<div id="menu-graspe-topo">
								<div id="basic_function"></div>
								<div id="panel_devices"></div>							
							</div>
							<div id="graspePrincipal"></div>
						</div>



						<div id="base-center-2" class="split split-vertical">

							<div id="base-editor-1" class="split split-horizontal">
								<div class="containerAba containerAba1"></div>
								<div class="aceEditorHTML">
									<div id="aceEditorHTML"></div>
								</div>
							</div>
							<div id="base-editor-2" class="split split-horizontal">
								<div id="base-editor-3" class="split split-vertical">
									<div class="containerAba containerAba2"></div>
									<div class="aceEditorCSS">
										<div id="aceEditorCSS"></div>
									</div>
								</div>
							</div>

						</div>



					</div>       
					<div id="right" class="split split-horizontal">
						<div id="base-right-1" class="split split-vertical">
							<div id="styles"></div>
						</div>
						<div id="base-right-2" class="split split-vertical">
							<div id="assets">
								<div class="cabecalho">PHP</div>
								<div class="grupo">
									<div class="file">assets/css/file.php</div>
									<div class="file">assets/css/file.php</div>
									<div class="file">assets/css/file.php</div>
									<div class="file">assets/css/file.php</div>
									<div class="file">assets/css/file.php</div>
									<div class="file">assets/css/file.php</div>
								</div>
								<div class="cabecalho">Styles</div>
								<div class="grupo">
									<div class="file">assets/css/file.css</div>
									<div class="file">assets/css/file.css</div>
									<div class="file">assets/css/file.css</div>
									<div class="file">assets/css/file.css</div>
									<div class="file">assets/css/file.css</div>
									<div class="file">assets/css/file.css</div>
								</div>
								<div class="cabecalho">Javascript</div>
								<div class="grupo">
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
								</div>
								<div class="cabecalho">Imagens</div>
								<div class="grupo">
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
									<div class="file">assets/js/file.js</div>
								</div>

							</div>
						</div>
					</div>
				</div>
		</div>
 <script>
 	/*##################################################################################*/
 	/*												SPLIT 								*/
 	/*##################################################################################*/
		 Split(['#left', '#center', '#right'], {
				sizes: [13, 74,13],
				minSize: [250,600,250],
				gutterSize:5,
				direction: 'horizontal'

		});
		 Split(['#base-center-1','#base-center-2'], {
				sizes: [99,1],
				gutterSize:5,
				direction: 'vertical'
		});
		 Split(['#base-left-1','#base-left-2'], {
				sizes: [50,50],
				gutterSize:5,
				direction: 'vertical'
		});
		Split(['#base-right-1','#base-right-2'], {
				sizes: [50,50],
				gutterSize:5,
				direction: 'vertical'
		});

		Split(['#base-editor-1','#base-editor-2'], {
				sizes: [50,50],
				gutterSize:5,
				direction: 'horizontal'
		});


	/*###################################### GRASPE ####################################*/



var configCanvas = {
				styles: [
    				'//fonts.googleapis.com/css?family=Montserrat:400,700,200',
    				'//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css',
    				'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/css/bootstrap.min.css',
    				'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/css/now-ui-kit.css?v=1.2.0',
    				'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/css/demo.css'
				],
				scripts: [
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/core/jquery.3.2.1.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/core/popper.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/core/bootstrap.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/moment.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/bootstrap-switch.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/bootstrap-tagsinput.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/bootstrap-selectpicker.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/jasny-bootstrap.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/nouislider.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/plugins/bootstrap-datetimepicker.min.js',
					'//demos.creative-tim.com/marketplace/now-ui-kit-pro/assets/js/now-ui-kit.js?v=1.2.0'
				]
			}


		window.graspejs = grapesjs.init({
			container: '#graspePrincipal',
			fromElement: true,
			height: 'calc(100% - 40px)',
			width: '100%',
			canvas: configCanvas,
			storageManager: {
				id: 'ws-',
				type: 'local',
				autosave: true,
				//autoload: true,
				stepsBeforeSave: 1,
				storeComponents: 1,
				storeStyles: 1,
				storeHtml: 1,
				storeScripts: 1,
				storeCss: 1,
				contentTypeJson: true,
				//urlLoad: url,
				params: {}
			},
			panels: {
				defaults: []
			},
			layerManager: {
				appendTo: '#layers'
			},
			blockManager: {
				appendTo: '#blocks',
				blocks: [ <?=$template_blocks?>
				{
					id: 'teste', // id is mandatory
					label: 'TESTE', // You can use HTML/SVG inside labels
					category:"Default",
					attributes: { class: 'gjs-block-section'},
					content: ``,
				},{
					id: 'section', // id is mandatory
					label: '<b>Section</b>', // You can use HTML/SVG inside labels
					category:"Default",
					attributes: {
						class: 'gjs-block-section'
					},
					content: `<section>
								<h1>This is a simple title</h1>
								<div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
								</section>`,
				}, {
					id: 'text',
					label: 'Text',
					category:"Default",
					content: '<div data-gjs-type="text">Insert your text here</div>',
				}, {
					category:"Default",
					id: 'image',
					label: 'Image',
					// Select the component once it's dropped
					select: true,
					// You can pass components as a JSON instead of a simple HTML string,
					// in this case we also use a defined component type `image`
					content: {
						type: 'image'
					},
					// This triggers `active` event on dropped components and the `image`
					// reacts by opening the AssetManager
					activate: true,
				}]
			},
			traitManager: {
				appendTo: '#styles',
			},
			selectorManager: {
				appendTo: '#styles'
			},
			styleManager: {
				appendTo: '#styles',
				sectors: [{
					name: 'Dimension',
					open: false,
					// Use built-in properties
					buildProps: ['width', 'min-height', 'padding'],
					// Use `properties` to define/override single property
					properties: [{
						// Type of the input,
						// options: integer | radio | select | color | slider | file | composite | stack
						type: 'integer',
						name: 'The width', // Label for the property
						property: 'width', // CSS property (if buildProps contains it will be extended)
						units: ['px', '%'], // Units, available only for 'integer' types
						defaults: 'auto', // Default value
						min: 0, // Min value, available only for 'integer' types
					}]
				}, {
					name: 'Extra',
					open: false,
					buildProps: ['background-color', 'box-shadow', 'custom-prop'],
					properties: [{
						id: 'custom-prop',
						name: 'Custom Label',
						property: 'font-size',
						type: 'select',
						defaults: '32px',
						// List of options, available only for 'select' and 'radio'  types
						options: [{
								value: '12px',
								name: 'Tiny'
							},
							{
								value: '18px',
								name: 'Medium'
							},
							{
								value: '32px',
								name: 'Big'
							},
						],
					}]
				}]
			},


			deviceManager: {
			devices: [
			{
			    name: 'XL',
			    width: '1200px',
			    widthMedia: '1200px',
			  },{
			    name: 'LG',
			    width: '992px',
			    widthMedia: '992px',
			  },{
			    name: 'MD',
			    width: '768px',
			    widthMedia: '768px',
			  },{
			    name: 'SM',
			    width: '576px',
			    widthMedia: '576px',
			  }, {
			    name: 'XS',
			    width: '360px',
			    widthMedia: '360px',
			  }]
			},
			panels: {
				defaults: [{
				  id: 'basic-actions',
				  el: '#basic_function',
				  buttons: [
						{
						id: 'panel-switcher',
						el: '.panel__switcher',
						buttons: [{
							id: 'show-layers',
							active: true,
							label: 'Layers',
							command: 'show-layers',
							togglable: false,
						}, {
							id: 'show-style',
							active: true,
							label: 'Styles',
							command: 'show-styles',
							togglable: false,
						}]
					},

				    {
				      id: 'visibility',
				      active: true, // active by default
				      className: 'btn-toggle-borders',
				      label: '<i class="material-icons">flip_to_front</i>',
				      command: 'sw-visibility', // Built-in command
				    }, {
				      id: 'export',
				      className: 'btn-open-export',
				      label: 'Exp',
				      command: 'export-template',
				      context: 'export-template', // For grouping context of buttons from the same panel
				    }, {
				      id: 'show-json',
				      className: 'btn-show-json',
				      label: 'JSON',
				      context: 'show-json',
				      command(editor) {
				        editor.Modal.setTitle('Components JSON')
				          .setContent(`<textarea style="width:100%; height: 250px;">
				            ${JSON.stringify(editor.getComponents())}
				          </textarea>`).open();
				      }
				    },
				  ],
				},
				{
					id: 'panel-devices',
					el: '#panel_devices',
					buttons: [
							{
									id: 'XL',
									label: '<i class="material-icons">desktop_windows</i>',
									command: function(){
										window.graspejs.setDevice('XL')
									},
									active: true,
									togglable: false,
							}, 					{
									id: 'LG',
									label: '<i class="material-icons">laptop_mac</i>',
									command: function(){
										window.graspejs.setDevice('LG')
									},
									active: true,
									togglable: false,
							}, 
							{
									id: 'MD',
									label: '<i class="material-icons">tablet</i>',
									command: function(){
										window.graspejs.setDevice('MD')
									},
									togglable: false,
							}, 
							{
									id: 'SM',
									label: '<i class="material-icons">tablet_mac</i>',
									command: function(){
										window.graspejs.setDevice('SM')
									},
									togglable: false,
							}, 
							{
									id: 'XS',
									label: '<i class="material-icons">phone_iphone</i>',
									command: function(){
										window.graspejs.setDevice('XS')
									},
									togglable: false,
							}
						],
				}]
			},

		});
		

		window.graspejs.on('load',function() {

			$('.gjs-blocks-c .plugin-externo').unbind( "mouseover").bind( "mouseover", function(){
				var item = $(this)
				var offetTop = $(item).offset().top
				var divLeft = $('#left').width()
				if(offetTop >=($('.studioweb').height() - $('#avatar-plugin-externo').height())){
					offetTop = $('.studioweb').height() - $('#avatar-plugin-externo').height();
				}
				$("#avatar-plugin-externo .avatar img").attr("src",$(this).data('avatar'))
				$("#avatar-plugin-externo .name").html($(this).data('name'))
				$("#avatar-plugin-externo .description").html($(this).data('description'))
				$('#avatar-plugin-externo').css({top:(offetTop - 50),left:divLeft}).show();

			}).unbind( "mouseleave").bind( "mouseleave", function(event){
				$('#avatar-plugin-externo').hide();
				
			}).unbind( "dragstart").bind( "dragstart", function(event){
				var dragIcon 	= document.createElement('img')
				dragIcon.width 	= 50
				dragIcon.height	= 50
				dragIcon.src 	= $(this).data('avatar')
				if (event.originalEvent.dataTransfer.setDragImage) {
					event.originalEvent.dataTransfer.setDragImage(dragIcon,0,0);
				}else {
					console.error ("2 Your browser does not support the setDragImage method.");
				}
			})
			ws.accordion({
				cabecalho: "#assets .cabecalho",
				closeOthers: true,
				closeBefore: true,
				beforeInit: function(){return true;},
				initOpen: function(){},
				initClose: function(){},
				finishOpen: function(){},
				finishClose: function(){}
			})
        });









// Define commands
window.graspejs.Commands.add('show-layers', {
  getRowEl(editor) { return window.graspejs.getContainer().closest('.editor-row'); },
  getLayersEl(row) { return row.querySelector('.layers-container') },
  run(editor, sender) {
    const lmEl = this.getLayersEl(this.getRowEl(editor));
    lmEl.style.display = '';
  },
  stop(editor, sender) {
    const lmEl = this.getLayersEl(this.getRowEl(editor));
    lmEl.style.display = 'none';
  },
});
window.graspejs.Commands.add('show-styles', {
  getRowEl(editor) { return editor.getContainer().closest('.editor-row'); },
  getStyleEl(row) { return row.querySelector('.styles-container') },
  run(editor, sender) {
    const smEl = this.getStyleEl(this.getRowEl(editor));
    smEl.style.display = '';
  },
  stop(editor, sender) {
    const smEl = this.getStyleEl(this.getRowEl(editor));
    smEl.style.display = 'none';
  },
});



var storageManager = window.graspejs.StorageManager;




	/*###################################### ACE EDITOR ####################################*/
	$.getScript('./app/vendor/ace/src-min-noconflict/ace.js', function() {
		$.getScript('./app/vendor/ace/src-min-noconflict/ext-language_tools.js', function() {
			ace.config.set('basePath', './app/vendor/ace/src-min-noconflict'); // SETA LOCAL DOS ARQUIVOS DO EDITOR


			window.htmEditor = ace.edit("aceEditorHTML");
			window.htmEditor.setTheme("ace/theme/monokai");
			window.htmEditor.getSession().setMode("ace/mode/html");
			window.htmEditor.setShowPrintMargin(true); // mostra linha ativa atual
			window.htmEditor.setHighlightActiveLine(true); // mostra linha ativa atual
			window.htmEditor.setShowInvisibles(0); // frufru de tabulações
			window.htmEditor.getSession().setUseSoftTabs(false); // usar tabs ao invez de espaço
			window.htmEditor.setDisplayIndentGuides(true);
			window.htmEditor.getSession().setUseWrapMode(true);
			// window.htmEditor.setOptions({maxLines: Infinity});
			window.htmEditor.getSession().on('change', function() {});
			window.htmEditor.setOptions({
					fontSize: "16px",
					maxLines: Infinity,
					autoScrollEditorIntoView: true,
					showPrintMargin: false,
				})

			window.assetsEditor = ace.edit("aceEditorCSS");
			window.assetsEditor.setTheme("ace/theme/monokai");
			window.assetsEditor.getSession().setMode("ace/mode/html");
			window.assetsEditor.setShowPrintMargin(true); // mostra linha ativa atual
			window.assetsEditor.setHighlightActiveLine(true); // mostra linha ativa atual
			window.assetsEditor.setShowInvisibles(0); // frufru de tabulações
			window.assetsEditor.getSession().setUseSoftTabs(false); // usar tabs ao invez de espaço
			window.assetsEditor.setDisplayIndentGuides(true);
			window.assetsEditor.getSession().setUseWrapMode(false);
			window.assetsEditor.setOptions({maxLines: Infinity});
			window.assetsEditor.getSession().on('change', function() {});
			window.assetsEditor.setOptions({fontSize: "16px"});
			window.assetsEditor.setOptions({
				fontSize: "16px",
				maxLines: Infinity,
				autoScrollEditorIntoView: true,
				showPrintMargin: false,
			});
			var temp = {js:[],css:[]};
			$.each(configCanvas.styles,function(a,b){
				var string = '<link rel="stylesheet" href="{link}" />';
				temp.css.push(string.replace('{link}',b))
			})
			$.each(configCanvas.scripts,function(a,b){
				var string = '<script src="{link}"><\/script>';
				temp.js.push(string.replace('{link}',b))
			})


		})
	})


//
//localStorage['ws-html']
//localStorage['ws-styles']
//localStorage['ws-css']

/*
	// UPDATE ACE EDITOR COM O CONTEUDO DO CANVAS 
	window.graspejs.on('storage:store', function(e) {
		var htmlCode = window.style_html(e.html);
		window.htmEditor.setValue(htmlCode)
	})

	// UPDATE DO CANVAS PELO ACE EDITOR 
	window.graspejs.setComponents(window.htmEditor.getSession().getValue())

	// CAPTA O HTML PURO DO CANVAS

	//window.graspejs.Canvas.getBody().ownerDocument
	//window.graspejs.store();


      window.style_html(new XMLSerializer().serializeToString(window.graspejs.Canvas.getBody().ownerDocument));


undefined
xml2string()



*/








</script>

