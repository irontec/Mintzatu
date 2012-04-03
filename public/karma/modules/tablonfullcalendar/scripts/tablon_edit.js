 /**
 * Fichero javascript para la clase tablon_edit,
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
 
 $(document).ready(function(){
 	 
	var doSave = function() {
    	var self = this;
    	if($(this).attr('escondido') == '1'){
			return false;
		}
		var opts = {op:'tablon_edit',acc:'checkMD5',value:$(this).val(),md5:$(this).parents("td").attr("md5")};
		
		
		idte = $(this).attr('id');
        
        
        
		if ($('textarea#'+idte).attr('class')=="tiny"||$(self).parents("td").attr('type')=="multiselect") {
					if ($(self).parents("td").attr('type')=="multiselect"){
						$(self).parents("td").find("img.autosaver").css("display","inline");
    					$(self).parents("td").children(".opsSave").fadeIn("slow");
					}else{
						$(self).parents("td").find("img.autosaver,img.undo").css("display","inline");
    					$(self).parents("td").children(".opsSave").fadeIn("slow");
    				}
			
		}else{
	    	$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
	    		if (!j.ret) {
	    			if ( ($("#autosaveButton:checked").length == 1) && ($(self).parents("td").attr("type")!="ajaxfileupload") ) {
						$(self).parents("td:not(type=ajaxfileupload)").find("img.autosaver").trigger("click");
	    			} else {
	    				
	    				$(self).parents("td").find("img.autosaver,img.undo").css("display","inline");
	    				$(self).parents("td").children(".opsSave").fadeIn("slow");
	    			}
	    		} else {
	    			$(self).parents("td").children(".opsSave").fadeOut("slow",function() {
	    				$(this).children("img.autosaver,img.undo").css("display","none");
	    			});
	    		}
	    	});   
    	} 	
    };
    
   $("td.tablon_edit input,td.tablon_edit textarea,td.tablon_edit select ").not(".multiselect").bind("blur",doSave);
   
   
	   
   
   
	$("td.tablon_edit input:file").customFile();
	
	var doCondition = function(e){
		var cnd = typeof e.target;
		var self = $(this);
		var idBase = self.parents("td").attr("id");
		var selected = self.val();
		var recursivo = new Array();
		var contRec = 0;
		var mostrados = new Array();
		$("tr.condMostrado").removeClass("condMostrado");
		if(self.attr("disabled") == false || !self.attr("disabled")){
			self.children("option").each(function() {
				var campos = new Array();
				if(typeof $(this).attr("id")!="undefined"){
					campos = $(this).attr("id").split("|");
				}
				if(campos.length>0){
					for(var i=0;i<campos.length;i++){
						var id_destino = idBase.replace(/([^:]+)::([^:]+)::([^:]+)/,"$1::"+campos[i]+"::$3");
						if ($(this).val()==selected && id_destino.match(/([^:]+)::([^:]+)::([^:]+)/)){
							if($(document.getElementById(id_destino))){
								$(document.getElementById(id_destino)).children("div.contValor").children().attr("display","block").attr('escondido','0');
								$(document.getElementById(id_destino)).parent("tr").addClass("condMostrado");
								$(document.getElementById(id_destino)).parent("tr").show();
								if($(document.getElementById(id_destino)).find('.condicionante').length>0){
									recursivo[contRec] = $(document.getElementById(id_destino));
									contRec++;
								}
							}
						}
						else{
							if(!$(document.getElementById(id_destino)).parent("tr").hasClass("condMostrado")){
								$(document.getElementById(id_destino)).children("div.contValor").children().attr("display","none").attr('escondido','1');
								$(document.getElementById(id_destino)).parent("tr").hide();
							}
							
						}
					}
				}
			});
			if(recursivo.length>0){
				for(i=0;i<recursivo.length;i++){
					recursivo[i].find('.condicionante').each(doCondition);
				}
			}
		}
	};
	$("td.tablon_edit .condicionante:enabled").each(doCondition);	
	$("td.tablon_edit .condicionante").bind('change',doCondition);
	
		
	var processDoSaving = function(j,self,type) {
		switch(j.error) {
			case 0:
				if (!j.value.principal) val = j.value;
				else val = j.value.principal;
				var td = $(self).parents("td");
				td.attr("md5",j.md5);
							
				if ($(".contValor",td).length>0) {
					$(".updatedValue",td).html(unescape(val));
					$("img.mag",td).bind("click",magImage).css("cursor","pointer").attr("title","click para agrandar");
					fil = $("input:file",td);
					
					if ($("input:file",td).length>0) $("input:file",td).clearInterval();
					
				} 	else td.find(type).val(val);
				
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$("#saveAllButton").trigger('click');
					location.reload(true);
				}
				
				td.find("img.loader").fadeOut("slow",function() {
					td.find(".info").css("display","inline").find("span").html(j.msg);
					window.setTimeout(function() {
						td.children(".opsSave").fadeOut("fast",function() {
							$(this).children(".info").css("display","none");
						});
					},2000);
				});	
			break;
			default:
				$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
				$(self).parents("td").children(".cancel,.opsSave,.loader").fadeOut("slow");
			break;
		}
		$(self).sendind = true;
	};
	
	$("img.autosaver").bind("click",function() {
		this.sending = true;
		var self = this;
		var type = (($(this).parents("td").attr("type")=="text")||($(this).parents("td").attr("type")=="password"))? "input":$(this).parents("td").attr("type");
		
		if(type == "textareaTags") var type = "textarea";
		if(type == "multiselect") var type = "input";
		
		if($(this).parents("td").find(type).attr('escondido') == '1'){
			return false;
		}
		var opts = {op:'tablon_edit',acc:'save',value:$(this).parents("td").find(type).val(),id:$(this).parents("td").attr("id")};
 		if ($(this).parents("td").find(type).hasClass("required") && $(this).parents("td").find(type).val() == ""){
			$(this).parents("td").find(type).css("border","dashed #f00 1px");
			return false;
		}else{
			$(this).parents("td").find(type).css("border","");
		} 
		if($(this).attr("class")=="autosaver clone") {
 			$(".rinfo").html('');
 			prevlr = $("input",$(document.getElementById($(this).parents('td').attr('id')+"_clone"))).val();
 			vlr =  $(this).parents("td").find(type).val();
 			if (vlr!=prevlr) {
 				
 				$("input",$(document.getElementById($(this).parents('td').attr('id')+"_clone"))).parents("td").append('<div class="rinfo" style="color:red;">los valores no coinciden</div>');
 			
 				return false;
 			}
 		}
 		$(self).parents("td").find("img.autosaver,img.undo").fadeOut("slow");
 					
 		$(this).parents("td").find("img.loader").css("display","inline");
 		$(this).parents("td").children(".opsSave").fadeIn("slow");
 		var url = "./modules/tablon/ajax/ops_tablon.php";
 		if ($(this).parents("td").attr("type")=="ajaxfileupload") {
 			url += "?";
 			for (var idx in opts) {
 				if (idx=="value") continue;
				url += idx+'='+escape(opts[idx])+"&";
			}
           $.ajaxFileUpload({
              url: url,
              secureuri:false,
              fileElementId: $("input:file",$(this).parents("td")).attr("id"),
              dataType: 'json',
              success: function (j) {processDoSaving(j,self,type); },
              error: function (data, status, e) {
                  alert(e);
              }
          });
		
		} else {
			idte = $(this).attr('id');
			if ($("textarea.tiny",$(this).parents("td")).length>0) {
				url += "?op=tablon_edit&acc=save&id="+opts.id+"&";			
		 		$.post(url,opts,function(content) { eval("var j="+content+";") ; processDoSaving(j,self,type);});
		 	} else {		 	
				$.getJSON(url,opts,function(j) {processDoSaving(j,self,type);});
			}
		}
	});
	
	$("img.undo").bind("click",function() {
		var self = this;
		var type = ($(this).parents("td").attr("type")=="text")? "input":$(this).parents("td").attr("type");
		var opts = {op:'tablon_edit',acc:'undoEdit',id:$(this).parents("td").attr("id")};
		$(self).parents("td").find("img.autosaver,img.undo").fadeOut("slow",function() {
			$(this).parents("td").find("img.loader").css("display","inline");
 			$(this).parents("td").children(".opsSave").fadeIn("slow");
 			
 			$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
 				
 				switch(j.error) {
					case 0:
						$(self).parents("td").attr("md5",j.md5).parents("td").find(type).val(j.value);
						$(self).parents("td").find("img.loader").fadeOut("slow",function() {
							$(this).parents("td").find(".info").css("display","inline").find("span").html(j.msg);
							window.setTimeout(function() {
								$(self).parents("td").children(".opsSave").fadeOut("slow",function() {
									$(this).children(".info").css("display","none");
								});
							},2000);
						});
							
					break;
					default:
						$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
						$(self).parents("td").children(".cancel").fadeOut("slow");
					break;
				}
 			
 			});
		
		});
	
	});
	
	$("#saveAllButton").bind("click",function() {
		$("img.autosaver:visible").trigger("click");
	});
 	$('.deleteRow').parent("span").bind("click",function() {
 		$(this).children("img").trigger("click");
 	}).css("cursor","pointer"); 
 	
 	var doHide = function() {
 		var opts = {op:'tablon_edit',acc:'hiddenCond',id:$(this).attr("id")};
		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
			for (var idx in j.hideFields) $("#"+j.fileds[idx]).parent("tr").slideUp();
			for (var idx in j.showFields) $("#"+j.fileds[idx]).parent("tr").slideDown();
 		});
 	
 	};
 	
 	$("td[doHide]").each(doHide);
 	
 	if($(".tags").length>0){
	 	$(".tags").each(function(){
	 		
	 		$(this).autocomplete_old('./modules/tablon/ajax/ops_tablon.php?op=tag_autocomplete&acc=load&autocompletetab='+$(this).attr('autocompletetab')+'&autocompletefield='+$(this).attr('autocompletefield'), {
				multiple: true,
				mustMatch: false,
				autoFill: true
			});
	 	
	 	
	 	});
 	}
 	if($('select.multiselect').length>0){
 		$('select.multiselect').multiSelect({ oneOrMoreSelected: '*' });
 	}
 	
});
