/**
 * $Id: editor_plugin_src.js 677 2008-03-07 13:52:41Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.AdvancedImageGTablePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceadvimageGTable', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
					file : url + '/image.htm',
					width : 680 + parseInt(ed.getLang('advimageGTable.delta_width', 0)),
					height : 505 + parseInt(ed.getLang('advimageGTable.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
				
								
			});

			// Register buttons
			ed.addButton('imageGTable', {
				title : 'Galeria de Imagenes',
				cmd : 'mceadvimageGTable',
				image: 	url + '/img/sample.gif'
			});
		},

		getInfo : function() {
			return {
				longname : 'Galer�a de Im�genes',
				author : 'Lander Ontoria Gardeazabal',
				authorurl : 'http://irontec.com',
				infourl : 'http://code.irontec.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('advimageGTable', tinymce.plugins.AdvancedImageGTablePlugin);
})();