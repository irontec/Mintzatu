
var cof ={
		mode : "textareas",
		relative_urls : true,
        remove_script_host : false,
		editor_selector : "tiny",
		language : js_language,
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimageG,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		theme_advanced_buttons1 : "formatselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,|,code,|,fullscreen", 
		
		//,styleselect,formatselect,fontselect,fontsizeselect",

		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,link,unlink,anchor,|,"+(disableTinyMCEmedia? "":"imageG,media,|,")+"forecolor,backcolor",
/*
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,link,unlink,anchor,|,imageG,media,|,mybutton",
r986*/
		theme_advanced_buttons3 : 0,
//		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
//		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
//		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
		
setup : function(ed) {
    ed.onBlur.add(function(ed, e) {
        contents = ed.getContent();
        idte = $(this).attr('id');
        $('textarea#'+idte).val(contents);
        $('textarea#'+idte).trigger('blur');
    }),
    ed.onChange.add(function(ed, e) {
        contents = ed.getContent();
        idte = $(this).attr('id');
        $('textarea#'+idte).val(contents);
        $('textarea#'+idte).trigger('blur');
    }),
	ed.onClick.add(function(ed) {
		try{
			var editors = $("#tablon_edit span.mceEditor.defaultSkin").find("table.mceLayout");
			if (editors.length>0){
				ew = $(editors[0]).width();
				w = editors.parents('td.tablon_edit').width();
				if (!editors.hasClass("reizedF")){
					editors.addClass("reizedF");
					editors.animate({'width': parseInt((w-60))+'px' },100);
				}
			}
		}catch(e){ }
	}),
	ed.addButton('mybutton', {
        title : 'Traducir contenidos',
        image : './modules/tablon/scripts/tiny_mce/plugins/tmp/img/logogoole.png',
        onclick : function() {
			// Add you own code to execute something on click
			ed.focus();
			var o = ed.selection.getContent({format : 'text'});
			ed.windowManager.open({
				file : './modules/tablon/scripts/tiny_mce/plugins/tmp/tmp.php?o='+o,
				width : 360 + ed.getLang('mybutton.delta_width', 0),
				height : 210 + ed.getLang('mybutton.delta_height', 0),
				inline : 1
			}, {
				plugin_url : './modules/tablon/scripts/tiny_mce/plugins/tmp/tmp.php?o='+o, // Plugin absolute URL
				some_custom_arg : o // Custom argument
			});

			
			
            /*ed.selection.setContent('<strong>Hello world!</strong>');*/
        }
    });


	

}
    		
	}; 


function loadTiny() {
	tinyMCE.init(cof);
}

loadTiny();