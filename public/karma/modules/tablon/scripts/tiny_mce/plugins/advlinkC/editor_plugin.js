(function() {
	tinymce.create('tinymce.plugins.AdvancedLinkCPlugin', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mceAdvLinkC', function() {
				var se = ed.selection;

				// No selection and not in link
				if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
					return;

				ed.windowManager.open({
					file : url + '/linkC.htm',
					width : 480 + parseInt(ed.getLang('advlinkC.delta_width', 0)),
					height : 400 + parseInt(ed.getLang('advlinkC.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('linkC', {
				title : 'advlinkC.link_desc',
				cmd : 'mceAdvLinkC',
				image: 	url + '/img/linkC.gif'
			});

			ed.addShortcut('ctrl+k', 'advlinkC.advlink_desc', 'mceAdvLinkC');

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('linkC', co && n.nodeName != 'A');
				cm.setActive('linkC', n.nodeName == 'A' && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'Advanced link',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('advlinkC', tinymce.plugins.AdvancedLinkCPlugin);
})();