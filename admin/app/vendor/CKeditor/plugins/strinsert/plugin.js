/**
 * @license Copyright © 2013 Stuart Sillitoe <stuart@vericode.co.uk>
 * This work is mine, and yours. You can modify it as you wish.
 *
 * Stuart Sillitoe
 * stuartsillitoe.co.uk
 *
 */

CKEDITOR.plugins.add('strinsert',{
	requires : ['richcombo'],
	init : function( editor ){
		var strings = window.comboPlugins;
		editor.ui.addRichCombo('strinsert',{
			label: 		'Plugins',
			title: 		'Inserir um plugin',
			voiceLabel: 'Inserir um plugin',
			className: 	'',
			multiSelect:false,
			panel:{
				css: [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
				voiceLabel: editor.lang.panelVoiceLabel
			},
			init: function(){
				this.startGroup( "Insert Content" );
				for (var i in strings){
					this.add(strings[i][0], strings[i][1], strings[i][2]);
				}
			},
			onClick: function( value ){
				editor.focus();
				editor.fire( 'saveSnapshot' );
				editor.insertHtml(value);
				editor.fire( 'saveSnapshot' );
			}
		});
	}
});