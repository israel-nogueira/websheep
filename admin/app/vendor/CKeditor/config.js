/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.extraPlugins = 'doksoft_image';
	config.toolbar_name = [ [ 'doksoft_image'] ];
	domain = window.location.href.split('/admin/');
	config.doksoft_uploader_url = domain[0]+'/admin/includes/plugins/doksoft_uploader/uploader.php';
};

	 