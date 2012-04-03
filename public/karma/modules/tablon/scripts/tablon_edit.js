 /**
 * Fichero javascript para la clase tablon_edit,
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
 $(document).ready(function(){
     
    $(window).bind("beforeunload",function() {
        if ($("img.autosaver:visible").length > 0) {
            return _("seguro_salir");
        }
    });
     
	 docolor();

	/*
	 * Bindear al los botones de generadores de string aleatorios, la generación del string y la inclusión de ese string al valor del input relacionado (el inmediatamente anterior)
	 */
	if($(".dorandomstring").length>0){
		$(".dorandomstring").bind('click',function(e){
			var self = $(this);
			var patron = self.attr("rpatterns");
			var largura = self.attr("rlengrh");
			var cadena = randomString(largura,patron);
		    var donde = self.prev(":input");
                        if (!donde.length)
                                donde = self.parents("td").find(":input").first();
		    donde.val(cadena);
		});
	}
	
	// Muestra el icono de guardado (disquete) si se ha modificado algún dato
	var doSave = function(e, conlive) {
		if(conlive != undefined){
			var self = e.target;
		}else{
			var self = this;
		}
    	if($(self).attr('escondido') == '1'){
			return false;
		}
    	
    	if($(self).parents("td").attr('type') == 'select'){
    		var valor = $(self).children("option[value="+$(self).val()+"]").text();
    	}else{
    		var valor = $(self).val();
    	}

        // Se envia el nuevo valor y el md5 de su valor original al servidor para ver si se ha modificado el dato.
		var opts = {op:'tablon_edit',acc:'checkMD5',value:valor,md5:$(self).parents("td").attr("md5")};

		var idte = $(self).attr('id');

		// En estos casos se muestra el disquete de guardar siempre, ya que la comprobación del md5 no es posible ¿?
		if ((idte != '') &&
            ($('#'+idte).hasClass('wymeditor')
//            || $('#'+idte).hasClass('tiny')  /* por usar attr previamente, no funcionaba bien. Bug solucionado por lander */
//            || $('#'+idte).hasClass('custom_tiny')  /* por usar attr previamente, no funcionaba bien. Bug solucionado por lander */
            || $(self).parents("td").attr('type')=="multiselect"
    		|| $(self).parents("td").attr('type')=="multiselectnoreal"
    		|| $(self).parents("td").attr('type')=="multiselectng"
    		|| $(self).hasClass("glocationField"))
        ) {
			if ($(self).parents("td").attr('type')=="multiselect"
		        ||$(self).parents("td").attr('type')=="multiselectnoreal"
	            ||$(self).parents("td").attr('type')=="multiselectng"
            ){
				$(self).parents("td").find("img.autosaver").css("display","inline");
    			$(self).parents("td").children(".opsSave").fadeIn("slow");
			}else{
				$(self).parents("td").find("img.autosaver,img.undo").css("display","inline");
    			$(self).parents("td").children(".opsSave").fadeIn("slow");
    		}
			
			if(($('textarea#'+ idte).hasClass('tiny'))
		        ||($('textarea#'+ idte).hasClass('custom_tiny'))
			) {
		        if($("table:first",$(self).parents("td")).css("border") == "1px dashed rgb(255, 0, 0)") {
		            $("table:first",$(self).parents("td")).css("border","");
		        }
			} else if($(self).css("border") == "1px dashed rgb(255, 0, 0)") {
			    $(self).css("border","");
			}
		} else {
		    // Enviamos los datos para comprobar su md5

	    	$.getJSON("./modules/tablon/ajax/ops_tablon.php", opts, function(j) {

	    		if (!j.ret) {
	    			if (($("#autosaveButton:checked").length == 1) && ($(self).parents("td").attr("type")!="ajaxfileupload") ) {
						$(self).parents("td:not(type=ajaxfileupload)").find("img.autosaver").trigger("click",['desdeAutosaver']);
	    			} else {
	    				$(self).parents("td").find("img.autosaver,img.undo").css("display","inline");
	    				$(self).parents("td").children(".opsSave").fadeIn("slow");
	    			}
	    		} else {
	    			$(self).parents("td").children(".opsSave").fadeOut("slow",function() {
	    				$(this).children("img.autosaver,img.undo").css("display","none");
	    				if($(self).css("border") == "1px dashed rgb(255, 0, 0)"){
	    				    $(self).css("border","");
    				    }
	    			});
	    		}
	    	});
    	}
    }; //Fin doSave
    
	$("td.tablon_edit input,td.tablon_edit textarea,td.tablon_edit select")
		.not(":file")
		.not(".multiselect")
		.not(".multiselectnoreal")
		.bind("focusout", doSave)
		.bind("blur", doSave);


	$("td.tablon_edit select").bind("change", doSave);
	$("td.tablon_edit input:file, td.tablon_edit input.uploadField, td.tablon_edit input.multiSelect").livequery("focusout", function(e){
		doSave(e,true);
	});

	$("td.tablon_edit input:file").bind('click', doSave);
	
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
		if((self.attr("disabled") == false || !self.attr("disabled")) && self.attr("escondido")!=1){
			self.children("option").each(function() {
				var campos = new Array();
				if(typeof $(this).attr("id")!="undefined"){
					campos = $(this).attr("id").split("|");
				}
				if(campos.length>0){
					for(var i=0;i<campos.length;i++){
						var id_destino = idBase.replace(/([^:]+)::([^:]+)::([^:]+)/,"$1::"+campos[i]+"::$3");
						if($("[id='"+id_destino+"']").length>0){
							var type = $("[id='"+id_destino+"']").attr("type");
							if ($(this).val()==selected && id_destino.match(/([^:]+)::([^:]+)::([^:]+)/)){
								//if($(document.getElementById(id_destino))){
									if(type=="ajaxfileupload"){
										$(document.getElementById(id_destino)).children("div.contValor").find("span").attr('escondido','0');
									}else{
										$(document.getElementById(id_destino)).children("div.contValor").find(type).attr("display","block").attr('escondido','0');
									}
									$(document.getElementById(id_destino)).parent("tr").addClass("condMostrado");
									$(document.getElementById(id_destino)).parent("tr").show();
									if($(document.getElementById(id_destino)).find('.condicionante').length>0){
										recursivo[contRec] = $(document.getElementById(id_destino));
										contRec++;
									}
								//}
							}else{
								if(!$(document.getElementById(id_destino)).parent("tr").hasClass("condMostrado")){
									if(type=="ajaxfileupload"){
										$(document.getElementById(id_destino)).children("div.contValor").find("span").attr('escondido','1');
									}else{
										$(document.getElementById(id_destino)).children("div.contValor").find(type).attr("display","none").attr('escondido','1');
									}
									
									$(document.getElementById(id_destino)).parent("tr").hide();
								}
								
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
	}; //Fin doCondition
	$("td.tablon_edit .condicionante:enabled").each(doCondition);
	$("td.tablon_edit .condicionante").bind('change',doCondition);
	
		
	var processDoSaving = function(j,self,type) {
		switch(j.error) {
			case 0:
				/*if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					location.reload(true);
				}*/
				if (!j.value.principal){
					val = j.value;
				}else{
					val = j.value.principal;
				}
				
				var td = $(self).parents("td");
				td.attr("md5",j.md5);
							
				// Si existe un elemento de tipo updatedValue, se actualiza con el valor de "val"
				if ($(".contValor > .updatedValue",td).length>0) {
					$(".updatedValue",td).html(unescape(val));
					$("img.mag",td).bind("click",magImage).css("cursor","pointer").attr("title","click para agrandar");
					fil = $("input:file",td);

					if(unescape(val) != ""){
							td.find("img.delete").fadeIn("slow");
					}
					if ($("input:file",td).length>0){
					    $("input:file",td).clearInterval();
				    }
				}else{
					td.find(type).val(val);
				}
				
				td.find("img.loader").fadeOut("slow",function() {
					td.find(".info").css("display","inline").find("span").html(j.msg);
					window.setTimeout(function() {
						td.find(".info").css("display","none");
					},2000);
				});
			
				
				if (j.valueAux) {
					for (var aa in j.valueAux ) {
						$(document.getElementById(aa)).html(j.valueAux[aa]);
					}
				}
				
				
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$("img.undo:hidden").each(function(){
						if($(this).parents("td").attr("type")!="ajaxfileupload"){
							$(this).trigger('click');
						}
					});
				}
				
			break;
			default:
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},submit: function(v,m){
						$("img.undo:hidden").each(function(){
							$(this).trigger('click');
						});
						/*if($(self).parents("td").find(type).hasClass("condicionante")){
							console.log("condicionante..."+$(self).parents("td").find(type));
							
						}*/
						return true;
					}});
				}else{
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
					$(self).parents("td").children(".opsSave").children(".cancel,.opsSave,.loader").fadeOut("slow");
					
				}
			break;
		}
		$(self).sendind = true;
	}; //Fin processDoSaving
	
	   
	  
	// Dejo fxautosaver accesible desde $ para que esté accesible desde otros contextos -- tablon.js por ejemplo para el tema del preview
	var fxautosaver = function(obj){

		obj.sending = true;
		var self = obj;
		var type = (($(obj).parents("td").attr("type")=="text")||($(obj).parents("td").attr("type")=="password"))? "input":$(obj).parents("td").attr("type");
		
		var values;
		var regex = /.+::(.+)::.+/ ;
		var id_data = $(obj).parents("td").attr("id").replace(regex,"$1");

        switch (type) {
            case 'textareaTags':
                type = 'textarea';
                break;
            case 'multiselect':
            case 'multiselectnoreal':
                type = 'input';
                break;
        }
        
        //En el multiselectng recorremos todas las opciones para recoger las checkeadas
        if (type == 'multiselectng') {
            type = 'checkbox';
            var tmp_values = $(obj).parents("td").find("[name="+id_data+"]:checked");
            var cont = 0;
            while (cont < tmp_values.length){
                if(cont == 0){
                    values = $(tmp_values[cont]).val();
                }else{
                    values += "," + $(tmp_values[cont]).val();
                }
                cont++;
            }
        } else {
            //Recogemos los datos del campo a guardar
            values = $(obj).parents("td").find(type).val();
        }
            
		
		if($(obj).parents("td").find('div textarea.wymeditor').length>0){
		    values = $.wymeditors($(obj).parents("td").find('div textarea.wymeditor').attr('instance')).xhtml();
		}
		
		if($(obj).parents("td").find(type).attr('escondido') == '1'){
			return false;
		}
		
		var url = "./modules/tablon/ajax/ops_tablon.php";
		var opts = {op:'tablon_edit',acc:'save',value:values,id:$(obj).parents("td").attr("id")};
		
		// Mod para preview.
		if (arguments.length>1) {
			// Funcion invocada con mas de un parámetro...
			if ( arguments[1] && (typeof arguments[1] == "function") ) {
				return arguments[1]($(obj),url,opts);
			}
		}

		
		
		$("#hiddenFields input[type=hidden]").each(function() {
			//opts["COND__"+$(this).attr("name")] = $(this).val();
			if(typeof $(this).attr('onlyTriggers') != undefined && $(this).attr('onlyTriggers') == "true"){
				opts["CONDT__"+$(this).attr("name")] = $(this).val();
			}else{
				opts["COND__"+$(this).attr("name")] = $(this).val();
			}
		});
		
 		if ($(obj).parents("td").find(type).hasClass("required") && $(obj).parents("td").find(type).val() == ""){
 			if ($("textarea.tiny, textarea.custom_tiny",$(obj).parents("td")).length>0) {
 				$(obj).parents("td").find("table:first").css("border","dashed #f00 1px");
 			} else {
 			    $(obj).parents("td").find(type).css("border","dashed #f00 1px");
		    }
			return false;
		}else{
			if ($("textarea.tiny, textarea.custom_tiny",$(obj).parents("td")).length>0) {
				$(obj).parents("td").find("table:first").css("border","");
 			} else {
 			    $(obj).parents("td").find(type).css("border","");
		    }
		}
		if($(obj).attr("class")=="autosaver clone") {
 			$(".rinfo").html('');
 			prevlr = $("input",$(document.getElementById($(obj).parents('td').attr('id')+"_clone"))).val();
 			vlr =  $(obj).parents("td").find(type).val();
 			if (vlr!=prevlr) {
 				
 				$("input",$(document.getElementById($(obj).parents('td').attr('id')+"_clone"))).parents("td").append('<div class="rinfo" style="color:red;">los valores no coinciden</div>');
 			
 				return false;
 			}
 		}
 		$(self).parents("td").find("img.autosaver,img.undo").fadeOut("slow");
 		$(obj).parents("td").find("img.loader").css("display","inline");
 		$(obj).parents("td").children(".opsSave").fadeIn("slow");
 		
 		if ($(obj).parents("td").attr("type")=="ajaxfileupload") {
 			url += "?";
 			for (var idx in opts) {
 				if (idx=="value") {
                    continue;
                }
				url += idx+'='+escape(opts[idx])+"&";
			}
           $.ajaxFileUpload({
              url: url,
              secureuri:false,
              fileElementId: $("input:file",$(obj).parents("td")).attr("id"),
              dataType: 'json',
              success: function (j) {processDoSaving(j,self,type); },
              error: function (data, status, e) {
                  alert(e);
              }
          });
		
		} else {
			idte = $(obj).attr('id');
			if ($("textarea.tiny, textarea.custom_tiny",$(obj).parents("td")).length>0
					|| $("textarea.wymeditor",$(obj).parents("td")).length>0
					|| $("textarea.defaultTextarea",$(obj).parents("td")).length>0
					|| $("input.glocationField",$(obj).parents("td")).length>0
					) {
				url += "?op=tablon_edit&acc=save&id="+opts.id+"&";
		 		$.post(url,opts,function(content) { eval("var j="+content+";") ; processDoSaving(j,self,type);});
		 	} else {
				$.getJSON(url,opts,function(j) {processDoSaving(j,self,type);});
			}
		}
	}; //Fin fxautosaver
	
	$("img.autosaver").bind("click",function(e,c) {
		var soyyo = this;
		if(typeof(c) != 'undefined' && (c=='desdeAll' || c!='autosaveButton')){
			fxautosaver(soyyo);
			return true;
		}
		if(typeof(askWhenUpdateEdit) != 'undefined' && askWhenUpdateEdit==true){
    		$.prompt("¿Está seguro de guardar los cambios?",{
    		    buttons: {
    		        Guardar: true,
    		        Cancelar: false
    		    },
    		    loaded: function(){
		            $(".jqidefaultbutton").focus();
	            },
    		    submit:function(v,m){
        			if(v==true){
        				fxautosaver(soyyo);
        				return true;
        			} else {
                        return true;
                    }
    		    }
		    });
		}else{
			fxautosaver(soyyo);
		}
		
	});
	
	$('#autosaveLI').bind('click',function(){
	   if ($(this).hasClass('seleced')){
		   $(this).removeClass('seleced');
		   $(this).find('img').attr('src',$(this).find('img').attr('noselsrc'));
		   $('#autosaveButton').removeAttr('checked');
	   }else{
		   $(this).addClass('seleced');
		   $(this).find('img').attr('src',$(this).find('img').attr('selsrc'));
		   $('#autosaveButton').attr('checked','checked');
	   }
	});
	
	
	$("img.undo").bind("click",function() {
		var self = this;
		var type = ($(this).parents("td").attr("type")=="text")? "input":$(this).parents("td").attr("type");
		var opts = {op:'tablon_edit',acc:'undoEdit',id:$(this).parents("td").attr("id")};
		$(self).parents("td").find("img.autosaver,img.undo").fadeOut("slow");
		$(this).parents("td").find("img.loader").css("display","inline");
		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
			switch(j.error) {
				case 0:
					var valor = (type=="input" && j.value==null)?"":j.value;
					$(self).parents("td").attr("md5",j.md5).find(type).val(valor);
					if($(self).parents("td").find(type).css("border")=="1px dashed rgb(255, 0, 0)"){
					    $(self).parents("td").find(type).css("border","");
				    }
					if ($("textarea.tiny, textarea.custom_tiny",$(self).parents("td")).length>0) {
						if($.trim(j.value)==""){
							$(self).parents("td").find("iframe").contents().find(".mceContentBody").html('<p><br mce_bogus="1"></p>');
						}else{
							$(self).parents("td").find("iframe").contents().find(".mceContentBody").html(j.value);
						}
						if($(self).parents("td").find("table:first").css("border")=="1px dashed rgb(255, 0, 0)"){
						    $(self).parents("td").find("table:first").css("border","");
					    }
					}else{

					}
					if($(self).parents("td").find(type).hasClass("condicionante")){
						$(self).parents("td").find(type).each(doCondition);
					}
					$(self).parents("td").find("img.loader").fadeOut("slow",function(){
						$(this).parents("td").find(".info").css("display","inline").find("span").html(j.msg);
						window.setTimeout(function() {
							$(self).parents("td").children(".opsSave").fadeOut("slow");
							$(self).parents("td").find(".loader").css({'display' : 'none'});
							$(self).parents("td").find(".info").css({'display' : 'none'});
						},2000);
					});
					
						
				break;
				default:
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
					$(self).parents("td").children(".cancel").fadeOut("slow");
				break;
			}
		});
	}); //Fin click de img.undo
	
	$("#saveAllButton").bind("click",function() {
		if(typeof(askWhenUpdateEdit) != 'undefined' && askWhenUpdateEdit==true){
    		$.prompt("¿Está seguro de guardar los cambios?",{
    		    buttons: {
    		        Guardar: true,
    		        Cancelar: false
		        },
		        loaded: function(){
	                $(".jqidefaultbutton").focus();
                },
		        submit:function(v,m){
        			if(v==true){
        				$("img.autosaver:visible").trigger("click",['desdeAll']);
        				return true;
        			}else{
        				return true;
        			}
        		}
	        });
		}else{
			$("img.autosaver:visible").trigger("click",['desdeAll']);
		}
		if ($("#tablon_edit").children("tbody").children("tr").children("td:not(.cabecera)").find(":enabled[class='required'][value='']").length>0){
			$("#tablon_edit").children("tbody").children("tr").children("td:not(.cabecera)").find(":enabled[class='required'][value='']").css("border","dashed #f00 1px");
			return false;
		}
	});
 	$('.deleteRow').parent("span").bind("click",function() {
 		$(this).children("img").trigger("click");
 	});
 	
 	var doHide = function() {
 		var opts = {op:'tablon_edit',acc:'hiddenCond',id:$(this).attr("id")};
		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
			for (var idx in j.hideFields) $("#"+j.fileds[idx]).parent("tr").slideUp();
			for (var idx in j.showFields) $("#"+j.fileds[idx]).parent("tr").slideDown();
 		});
 	
 	};
 	
 	$("td[doHide]").each(doHide);
 	
 	var autocompleteScript = './modules/tablon/ajax/ops_tablon.php';
 	
 	if($(".tags").length>0){
	 	$(".tags").each(function(){
	 		if (tmp = $(this).attr('autocompleteScript')) {
	 			autocompleteScript = tmp;
	 		}
	 		$(this).autocomplete_old(autocompleteScript+'?op=tag_autocomplete&acc=load&autocompletetab='+$(this).attr('autocompletetab')+'&autocompletefield='+$(this).attr('autocompletefield'), {
				multiple: true,
				mustMatch: false,
				autoFill: true
			});
	 	
	 	
	 	});
 	}
 	if($(".enumbdtext").length>0){
	 	$(".enumbdtext").each(function(){
	 		
	 		$(this).autocomplete_old('./modules/tablon/ajax/ops_tablon.php?op=tag_autocomplete&acc=load&autocompletetab='+$(this).attr('autocompletetab')+'&autocompletefield='+$(this).attr('autocompletefield'), {
				multiple: false,
				mustMatch: true,
				autoFill: true
			});
	 	
	 	
	 	});
 	}
 	
 	if($('.glocationField').length>0){
 		$('.glocationField').each(function(){
 			//$(this)
 			doGlocation($(this));
 		});
 	}
 	
 	if($('select.multiselect').length>0){
 		$('select.multiselect').each(function(){
 			if($(this).attr('multiselectOnlyOne') != undefined && $(this).attr('multiselectOnlyOne') == "true"){
 	 			$(this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
 	 		}else{
 	 			$(this).multiSelect({ oneOrMoreSelected: '*' });
 	 		}
 		});
 	}
 	if($('select.multiselectnoreal').length>0){
 		$('select.multiselectnoreal').each(function(){
			if($(this).attr('multiselectOnlyOne') != undefined && $(this).attr('multiselectOnlyOne') == "true"){
	 			$(this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
	 		}else{
	 			$(this).multiSelect({ oneOrMoreSelected: '*' });
	 		}
		});
 	}

	$("#tablon_edit input[type=text]:not(.date-pick, .uploadField, .multiSelect, .date-pickESP, .glocationField)").resizable({ handles: 'e',minWidth:250});
	// Fix necesario para los campos ocultos que luego se muestran tras aplicar el resizable.... dareles anchura y altura por que sino aparacen como cuadrados de 7x8 px...
	$("#tablon_edit input[type=text].ui-resizable").css("minWidth",250).css("minHeight",20).parent("div").css("height",28).css("minWidth",257);
	$("#tablon_edit textarea:not(.tiny, .wymeditor, .custom_tiny)").resizable({ handles: 's,e',minWidth:250});
	//Para los input con maxlength quitamos el height del div padre
	$("#tablon_edit input[maxlength].ui-resizable").parent('div').css("height",'');
	

	var processDoDeleteImage = function(j,self) {
		switch(j.error) {
			case 0:
				if (!j.value.principal){
					val = j.value;
				}else{
					val = j.value.principal;
				}

				var td = $(self).parents("td");
				
				td.attr("md5",j.md5);
				td.find("img.loader").fadeOut("slow",function() {
					td.find(".info").css("display","inline").find("span").html(j.msg);
					window.setTimeout(function() {
						td.children(".opsSave").fadeOut("fast",function() {
							$(this).children(".info").css("display","none");
						});
					},2000);
				});

				// Si existe un elemento de tipo updatedValue (campo de tipo image...), se actualiza con el valor de "val"
				if ($(".contValor > .updatedValue",td).length>0) {
					$(".updatedValue",td).html(unescape(val));
					$("img.mag",td).bind("click",magImage).css("cursor","pointer").attr("title","click para agrandar");
					fil = $("input:file",td);
					
					if ($("input:file",td).length>0){
					    $("input:file",td).clearInterval();
				    }
				}else{
					td.find(type).val(val);
				}
				
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$("img.undo:hidden").each(function(){
						if($(this).parents("td").attr("type")!="ajaxfileupload"){
						    $(this).trigger('click');
					    }
					});
				}
			break;
			default:
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},submit: function(v,m){
						$("img.undo:hidden").each(function(){
							$(this).trigger('click');
						});
						return true;
					}});
				}else{
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
					$(self).parents("td").children(".opsSave").children(".cancel,.opsSave,.loader").fadeOut("slow");
					
				}
			break;
		}
		$(self).sendind = true;
	}; //Fin processDoDelete
	
	var eliminarImagen = function(obj){
		obj.sending = true;
		var regex = /.+::(.+)::.+/ ;
		var id_data = $(obj).parents("td").attr("id").replace(regex,"$1");
		var opts = {
			op:'tablon_edit',
			acc:'deleteImage',
			id:$(obj).parents("td").attr("id")
		};
		
 		$(obj).parents("td").find("img.delete").fadeOut("slow");
 		$(obj).parents("td").find("img.loader").css("display","inline");
 		$(obj).parents("td").children(".opsSave").fadeIn("slow");
 		var url = "./modules/tablon/ajax/ops_tablon.php";
		$.getJSON(url, opts, function(j) {processDoDeleteImage(j,obj);});
	}; //eliminarImagen
	
	if($('.wymeditor').length>0){
		$.getJSON("./modules/tablon/ajax/ops_tablon.php?op=wymeditor&acc=configuracion",{},function(o) {
			if(o.error){
				alert(o.error);
			}
			
	        $('.wymeditor').each(function(){ 
	        	var perfil = $(this).attr('rel');
	        	if(perfil == '') alert('Debes añadir un perfil en el campo "'+ $(this).attr('name') +'", revisa el PLT');
	        	var p = false;
	        	for (var perf in o) {
	        		if(perf == perfil) {
	        			p = true;
	        			var classesItem ='[';
	    			    for (var a in o[perf].wymeditor.classesItems)
	    			    {
	    			        classesItem += '{';
	    			        for (var i in o[perf].wymeditor.classesItems[a])
	    			        {
	    			            classesItem += "'"+i+"':'"+o[perf].wymeditor.classesItems[a][i]+"',";
	    			        }
	    			        classesItem += '},';
	    			    }
	    			    classesItem += ']';
	    			    
	    			    var editorStyle ='[';
	    			    for (var a in o[perf].wymeditor.editorStyles)
	    			    {
	    			        editorStyle += '{';
	    			        for (var i in o[perf].wymeditor.editorStyles[a])
	    			        {
	    			            editorStyle += "'"+i+"':'"+o[perf].wymeditor.editorStyles[a][i]+"',";
	    			        }
	    			        editorStyle += '},';
	    			    }
	    			    editorStyle += ']';
	    			    
	    			    var toolsItem ='[';
	    			    for (var a in o[perf].wymeditor.toolsItems)
	    			    {
	    			        toolsItem += '{';
	    			        for (var i in o[perf].wymeditor.toolsItems[a])
	    			        {
	    			            toolsItem += "'"+i+"':'"+o[perf].wymeditor.toolsItems[a][i]+"',";
	    			        }
	    			        toolsItem += '},';
	    			    }
	    			    toolsItem += ']';
	    			    
	    			    var contPlugin = 0;
	    			    var plugins = {};
	    			    if (o[perf].plugins) {
    	    			    $.each(o[perf].plugins,function(indice,valor) {
    	    			    	plugins[indice] = valor;
    	    			    	$.getScript('modules/tablon/scripts/wymeditor/plugins/karma/jquery.wymeditor.' + valor.replace('_', '.') + '.js');
    	    			    });
	    			    }
	    			    
	        		}
	        	}
	        	if(p == false) alert('No existe el perfil "'+ perfil +'", revisa el archivo de configuración');
	        	
	        	if(typeof(toolsItem) != 'undefined') {
		        	$(this).wymeditor({
		            //stylesheet: "modules/tablon/scripts/wymeditor/"+o.wymeditor.stylesheet,
		            lang: o[perf].wymeditor.lang,
		            classesItems: classesItem,
		            editorStyles: editorStyle,
		            toolsItems: toolsItem,
		    		postInit: function(wym) {
		        		$.each(plugins,function(instancia,plugin) {
		        			wym[plugin](o[perfil][instancia][0],instancia);	
		        		});
		        		
		        		wym.hovertools();
			        	wym.initResize();
			        	$(".ui-wrapper").css('overflow','visible');
			        	jQuery(wym._box).find(wym._options.containersSelector)
		                .removeClass('wym_dropdown')
		                .addClass('wym_panel')
		                .find('h2 > span')
		                .remove();
			        	
			        }
				    });
	        	} else {
	        	}
	        });
	        
	        
		});

		$(window).bind("resize",function() {
			$('.wymeditor').parents("div.ui-wrapper").css({"width":"98%","padding-right":"50%"});
		});

	}
	$('.wymeditor').parents('div.contValor').css('width','98%'); //Para resizear al tamaño de pantalla el wym_box de XHTML
	
	$("img.delete").bind("click",function(e,c) {
		var soyyo = this;
		$.prompt("¿Está seguro de querer eliminar el dato?",{
			buttons: {
				Borrar: true,
				Cancelar: false
			},
			loaded: function(){
				$(".jqidefaultbutton").focus();
			},
			submit:function(v,m){
				if(v==true){
					eliminarImagen(soyyo);
					return true;
				}else{
					return true;
				}
			}
		});
		
	});
	
	$("img.delete").each(function(){
		var realImgVar = $(this).parents("td").find("img.mag").attr("alt");
		var realFileVar = $(this).parents("td").find("div.updatedValue a sup").text();
		if((typeof(realFileVar) != "undefined" && realFileVar != "") || (typeof(realImgVar) != "undefined" && realImgVar != ""))
		{
			$(this).fadeIn("slow");
		}
	});


	$("a.html_preview").bind("click",function(e) {
		e.preventDefault();
		
		fxautosaver(this,function(obj,url,opts) {
			var overlay = $("<div class='overlay'/>").appendTo("body").css({backgroundColor:'#000',opacity:'0.6',width:'100%',height:$(document).height(),position:'absolute',top:'0',left:'0'});
			var posX = ($(document).width()/2) - 550;
			var ifr = $("<iframe id='dstPreview' name='dstPreview' ></iframe>")
				.css({width:"1100px",height:"90%",display:'none',position:'absolute',top:'0',left:posX+'px',margin:'auto'})
				.appendTo($("body"))
				.slideDown('2000');
			
			url += "?op=tablon_edit&acc=save&id="+opts.id+"&preview=1&";

		 	if ($("textarea.tiny, textarea.custom_tiny",obj.parents("td")).length>0) {
				var str = "<form action='"+url+"' method='post' target='dstPreview'>";
				for (var idx in opts) {
					str += "<textarea name='"+idx+"'>"+opts[idx]+"</textarea>";
				}
				str += "</form>";
				$(str).css("display","none").appendTo("body").trigger("submit");
		 	
		 	} else {
			 	for (var idx in opts) {
					url += idx+'='+escape(opts[idx])+"&";
				}
		 		ifr.attr("src",url);
		 	}


			var closing = $("<span><strong>[ CERRAR ]</strong></SPAN>")
		 			.css({position:'fixed',top:'2px',right:'3px',background:'#f00',color:'#fff',cursor:'pointer'})
		 			.bind("click",function() {
						$(document).unbind("keyup.paraIFRAME");
		 				ifr.prev("div.overlay").fadeOut("slow");
		 				ifr.slideUp(function() {
		 					$(this).contents().find("body").html("");
		 					var that = $(this);
		 					window.setTimeout(function() {
		 						that.prev("div.overlay").remove();
		 						that.remove();
		 					},200);
		 				});
		 			});
		 		

			$(document).bind("keyup.paraIFRAME",function(e) {
					if (e.keyCode == 27) {
						closing.trigger("click");
		 			}
		 	});
		 	
		 	ifr.bind("load",function() {
		 		$(this).contents().find("body")
		 			.css("cursor","help")
		 			.bind("click",function(e) {
		 				e.preventDefault();
		 			})
		 			.append(closing)
		 			.focus();
		 	});
			
		});
		
		
	});
 	
});
