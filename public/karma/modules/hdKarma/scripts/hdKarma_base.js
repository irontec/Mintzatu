var hdbase = {
	selectedDir : 0,
	emptyUL : '<ul style="display: none;"><li id="placeholder" class="last"><span>placeholder</span></li></ul>',
	loadObjs : function() {
		hdbase.button = $("#hdFire");
		$("#hd_list span.folder").livequery("click",function() {
			$("#hd_list span.folder").removeClass("selected");
			$(this).addClass("selected");
			hdbase.selectedDir = $(this).parent("li").attr("id").replace(/[^0-9]+/,'');
			$("#hd_content").html($(this).parent("li").attr("id"));
			return false;
		});
		
		$("#hd_list input.hd_newFolder").livequery("keypress",function(e) {
			switch(e.keyCode) {
				case 27: // ESC
					//console.log($(this));
					$(this).css("color","#f00").attr("value"," ").val("");
					$(this).parents("span").removeClass("folder").addClass("folderhidden");
					
					
					//$("div:eq(0)",$(hdbase.getcurrentfolderid())).trigger("click");
				return false;
				case 13: // ENTER
					var nCarpeta = $(this).val();
					var idPadre = hdbase.selectedDir;
					$.get("./modules/hdKarma/ajax/ops_dirs.php",{op:'new',idpadre:idPadre,n:nCarpeta},function(r) {
						var pLi = $("#hd_folder_"+hdbase.selectedDir);
						pLi.removeClass("collapsable").addClass("hasChildren expandable");
						$("ul",pLi).remove();
						pLi.html(pLi.html()+hdbase.emptyUL);
						pLi.trigger("click");
						if ($.trim(r)!="ok") alert($.trim(r));
					});
				return false;
			}
	
		});
	
	},
	button : false,
	baseHTML : "<div id='hdKarma_carga' class='tablon'><table><caption>Disco Duro</caption></table>\
		<div id='hd_content'></div>\
		<div id='hd_list'><ul class='filetree'></ul></div>\
		<div id='hd_options'></div></div>",
	showHD : function() {
		
		$.prompt(hdbase.baseHTML,{
			draggable : false,
			buttons: {
				Cerrar: "cancelar"
			},
			loaded : function() {
				$("#hd_list ul:eq(0)").treeview({
					add: function(a) {alert(a);},
					url: "./modules/hdKarma/ajax/ops_dirs.php?op=doList"
				});
				$("#hd_options").load("./modules/hdKarma/ajax/ops_dirs.php?op=doOptions",function() {
					$("#hd_newFolder").bind("click",hdbase.activateNewFolder);
									
				});

			},
			submit : function(v,m){
				switch(v) {
					case 'cancelar':
						$("#hdUpload").fileUploadClearQueue();
						loadingPanel = false;
					return true;
				};
			}		
		});
	
	},
	getcurrentfolderid : function() {
		if (hdbase.selectedDir == 0) return "#hd_folder_new_0";
		else return "#hd_folder_"+hdbase.selectedDir;
	},
	activateNewFolder : function() {
		
		var current = $(hdbase.getcurrentfolderid());
		var currentUL = $("ul:eq(0)",current);
		
		$.get("./modules/hdKarma/ajax/ops_dirs.php?op=doList&new=1",function(c) {
			
			$("#hd_list ul:eq(0)").treeview({add:);
		});
		return false;
		var curDiv = $("div:eq(0)",current);
		if ((!curDiv.hasClass("collapsable-hitarea")) && (hdbase.selectedDir!=0)) { // Si no est� desplegado...
			curDiv.trigger("click");
			$("span.folderhidden",current).removeClass("folderhidden").addClass("folder");
			$("span input",current).focus();

		} else {
			$("span.folderhidden",current).removeClass("folderhidden").addClass("folder");
			$("span input",current).focus();
		}
	
	},
	foo : true
	
};



$(function(){
	hdbase.loadObjs();
	hdbase.button.bind("click",hdbase.showHD);
});

