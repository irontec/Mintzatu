 /**
 * Fichero javascript para la clase tablon_edit,
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
var doCondition = function(){
		var self = $(this);
		var padre = self.parents("div.jqimessage");
		var nameBase = self.attr("name");
		var selected = self.val();
		var recursivo = new Array();
		var elPromp = self.parents("div#jqi");
		var contRec = 0;
		padre.find("tr").removeClass("condMostrado");
		if(self.attr("disabled") == false){
			self.children("option").each(function() {
				var campos = new Array();
				if(typeof $(this).attr("id")!="undefined"){
					campos = $(this).attr("id").split("|");
				}
				if(campos.length>0){
					for(var i=0;i<campos.length;i++){
						if(campos[i] == "" || $("[name='"+campos[i]+"']").length == 0) {
                            continue;
                        }
						if ($(this).val()==selected){
							if($("[name='"+campos[i]+"']").length>0){
								padre.find("[name='"+campos[i]+"']").parent("td").children().attr("display","block").attr('escondido','0');
								padre.find("[name='"+campos[i]+"']").parent("td").parent("tr").addClass("condMostrado");
								padre.find("[name='"+campos[i]+"']").parent("td").parent("tr").show();
								if(padre.find("[name='"+campos[i]+"']").hasClass("condicionante")){
									recursivo[contRec] = "[name='"+campos[i]+"']";
									contRec++;
								}
							}
						}
						else{
							if(!padre.find("[name='"+campos[i]+"']").parent("td").parent("tr").hasClass("condMostrado")){
								padre.find("[name='"+campos[i]+"']").parent("td").children().attr("display","none").attr('escondido','1');
								//padre.find("[name='"+campos[i]+"']").parent("td").children().attr("disabled","true").attr('escondido','1');
								padre.find("[name='"+campos[i]+"']").parent("td").parent("tr").hide();
							}
						}
					}
				}
			});
			if(recursivo.length>0){
				for(i=0;i<recursivo.length;i++){
					padre.find(recursivo[i]).each(doCondition);
				}
			}
		}
	};

var newFieldsAction = function(J,inchk,acc,datos){
	//,{op:'tablon_mass',acc:acc,value:datos},"doSaveMass('"+j+"','"+inchk+"','"+acc+"')","cancelAction("+inchk+")"
	var noEditables = new Array();
	var cont = 0;
	var txtC = "";
	var txt = "";
	/*
	 * Por si se quiere especificar el caption desde el ajax
	 * */
	for(var idx in J.fields){
		if(typeof(J.fields[idx].title)!= "undefined"){
			var txtC = '<table>';
			txtC += "<caption>"+J.fields[idx].title+"</caption>";
			txtC += J.fields[idx].data;
		}else{
			txt += '<tr><td>'+J.fields[idx].alias+'</td><td>';
			txt += J.fields[idx].data;
			if(J.fields[idx].noEdit == true){
				noEditables[cont] = J.fields[idx].name;
				cont++;
			}
			txt += '</td></tr>';
		}
	}
	txt += '</table>';
	if(txtC == ""){
		txtC = '<table>';
		txtC += '<caption>';
		txtC += _str('Nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</caption>';
	}
	txt = txtC+txt;
	var otxt = "";
	var tmpotxt = "";
	if (J.ofields){
		oPLT = J.opl;
	}
	
	if (J.ofields&&J.opl!="false"){
	    txt = txt+'<a id="create" >'+ _str('Crear_nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</a>';
    }
	var selected = false;
	var elPrompt = $.prompt(txt,{
			loaded:function() {
					$("input:file",$(this)).customFile({width:230});
					$(".fsfilefs",$(this)).each(function() {
						var ffile = $(this);
						
						$(".fileTreeDemo",ffile).bind("mousemove",function(e) {e.stopPropagation();}).css("height","300px").css("overflow","auto").fileTree(
							{ root: '/',extraparams:{id:plantilla,fld:ffile.attr("rel")},script: './modules/tablon/ajax/ops_tablon.php?op=fsfilefs&acc=list' },
							function(file) {
							// Funcion al seleccionar
							// fichero
							var aFile = file.split(";");
							$(".tmp_name",ffile).val(aFile[0]);
							$(".name",ffile).val(aFile[1]);
							$(".size",ffile).val(aFile[2]);
							$(".type",ffile).val(aFile[3]);
							selected = true;
						});
				}); // each fileTreeDemo

			},
			width:600,
			buttons: { Guardar: true, Cancelar: false },
			submit:function(v,m){
				
		 		var opts = {op:'tablon',acc:'new',id:J.idPlt};
				
				if (!v){
					inchk.removeAttr("checked");
			 		inchk.fadeOut("slow",function() {
			 			inchk.removeAttr("disabled");
					});
			 		inchk.next("img").fadeOut("slow",function(){
			 			$(this).remove();
			 			inchk.fadeIn("slow");
			 		});
					return true;
				}
				
				var fields = {};
				var ret = true;
				for (var idx in J.fields) {
					var e = $("[name="+J.fields[idx].name+"]",m);
					if(e.attr('escondido') == '1'){
						// Si el campo depende de otro, en este caso aunque el plt marque requerido, su valor no será necesaria. Lo indicamos mediente este nuevo campo de opts
						opts["NoReqDepend__"+J.fields[idx].name] = "seguir";
						continue;
					}
					switch(J.fields[idx].ftype) {
					case "fsfilefs":
						var _n = J.fields[idx].name;
						opts["FLD__"+_n+"[name]"] = $("#"+_n,m).val();
						opts["FLD__"+_n+"[tmp_name]"] = $("#"+_n+"_tmp_name",m).val();
						opts["FLD__"+_n+"[size]"] = $("#"+_n+"_size",m).val();
						opts["FLD__"+_n+"[type]"] = $("#"+_n+"_type",m).val();
						continue;
					break;
				
				}
					var vlr = e.val();
					if ((vlr=="")&& (J.fields[idx].req) && e.attr('escondido') != 1) {
						e.parent().children(":input").css("border","dashed #f00 1px");
					ret = false;
					} else{
						e.parent().children(":input").css("border","");
					}
					
					if (J.fields[idx].clone) {
							aa = J.fields[idx].name.split('_');
							nextval = $("[name="+aa[0]+"]",m).val();
							if (nextval!=vlr) {
									e.parent().append("<br /><small style=\"color:#f00;\">"+_('valores_no_coinciden')+"</small>");
									ret = false;
							} else {
									opts["FLD__"+aa[0]] = vlr;
							}
					} else {
							opts["FLD__"+J.fields[idx].name] = vlr;
					}
					
					
					
				}
				
				$("#hiddenFields input[type=hidden]").each(function() {
					opts["COND__"+$(this).attr("name")] = $(this).val();
				});
				
				var url = "./modules/tablon/ajax/ops_tablon.php";
 				if ($("#tablononnew").length){
 					opts.tablononnew = $("#tablononnew").val();
 				}
				if (ret) {
					if ($("input:file",m).length>0) { // Ajax
													// file
													// Upload
						$("input:file",m).each(function() {
							$(this).attr("name","FLD__"+$(this).attr("name"));
						});
						
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
							fileElementId: $("input:file",m).attr("id"),
							dataType: 'json',
							success: function (j) {
								processNewSavingMass(j,inchk);
							},
							error: function (data, status, e) {
								alert(e);
							}
	      				});
					} else { // No hay ficheros, no hay
								// iFRAME
						$.getJSON(url,opts,function(j){
							processNewSavingMass(j,inchk);
						});
						
					}
				}
				return ret;
		}
	});
	
	velPrompt = elPrompt;
	
	elPrompt.find("#create").bind('click',function(){renew(oPLT,elPrompt);});
	elPrompt.find(".condicionante:enabled").each(doCondition);
	elPrompt.find(".condicionante").bind('change',doCondition);
	$('.timepick').timepicker({divId: "mytimepicker"});
	dodate();
	
	for(i=0;i<noEditables.length;i++){
		elPrompt.find("[name='"+noEditables[i]+"']").parent("td").parent("tr").css("display","none");
	}
	if(elPrompt.find('select.multiselect').length>0){
		elPrompt.find('select.multiselect').each(function(){
			if($(this).attr('multiselectOnlyOne') != undefined && $(this).attr('multiselectOnlyOne') == "true"){
				$(this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
			}else{
				$(this).multiSelect({ oneOrMoreSelected: '*' });
			}
		});
		window.setTimeout(function(){elPrompt.find('.multiSelect').multiSelectOptionsHide();},400);
	}
	if(elPrompt.find(".tags").length>0){
			elPrompt.find(".tags").each(function(){
		 		$(this).autocomplete_old('./modules/tablon/ajax/ops_tablon.php?op=tag_autocomplete&acc=load&autocompletetab='+$(this).attr('autocompletetab')+'&autocompletefield='+$(this).attr('autocompletefield'), {
					multiple: true,
					mustMatch: false,
					autoFill: true
				});
		 	});
	}
	if(elPrompt.find(".enumbdtext").length>0){
		elPrompt.find(".enumbdtext").each(function(){
	 		$(this).autocomplete_old('./modules/tablon/ajax/ops_tablon.php?op=tag_autocomplete&acc=load&autocompletetab='+$(this).attr('autocompletetab')+'&autocompletefield='+$(this).attr('autocompletefield'), {
				multiple: true,
				mustMatch: false,
				autoFill: true
			});
	 	});
 	}

	if(elPrompt.find('textarea.tiny, textarea.custom_tiny').length>0){
		tinyMCE.init(cof);
	}
};

var processNewSavingMass = function(j,self,before) {
	switch(j.error) {
		case 0:
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					location.reload(true);
				}
				if (typeof before == "function"){
					
					before(j);
					break;
				}
			
				self.parent('td').attr('id',self.parent('td').attr('id').replace(/::nulo::/,"::"+j.id+"::"));
				if(self.parent('td').parent("tr").children("td[relattr='true']").length>0){
					
					var newValor = j.id;
					self.parent('td').parent("tr").children("td[relattr='true']").each(function(){
						var nuevaID = $(this).attr("id").replace(/::nulo$/,"::"+newValor);
						$(this).attr("id",nuevaID);

						$(this).html(j.values[nuevaID]);

					});
				}
				self.parent('td').parent("tr").children("td[relattr='true'][eseditable='true']").addClass("editable");
				doEditables();
				
				$(self).removeAttr("disabled");
				$(self).next("img").fadeOut("slow",function(){
					$(this).remove();
					$(self).fadeIn("slow");
				});
		break;
		default:
			if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
				$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},submit: function(v,m){location.reload(true);}});
			}else{
				$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
				$(self).html(self.revert);
			}
			$(self).removeAttr('checked');
			$(self).removeAttr("disabled");
			$(self).next("img").fadeOut("slow",function(){
				$(this).remove();
				$(self).fadeIn("slow");
			});
		break;
	}

	var ie6 = (jQuery.browser.msie && jQuery.browser.version < 7);
	if(ie6){
	    jQuery(window).unbind('scroll',ie6scroll);// ie6, remove the
	}
														// scroll event
	// var jqif = fade = '<iframe src="" class="cargandoTablon"
	// id="cargandoTablon"></iframe>';
	var jqif = $("body").children('#cargandoTablon');
	jqif.fadeOut('fast',function(){
		jqif.remove();
	});
	$("body").css("cursor","default");
};

var doSaveMass = function(j,inchk,acc){
	if(j.error){
		inchk.removeAttr('checked');
		$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
	}else{
		if (!j.ret) {
			inchk.removeAttr('checked');
			$.prompt('error',{buttons: {Ok: true}});
		} else {
			inchk.parent('td').attr('id',j.ret);
			if(inchk.parent('td').parent("tr").children("td[relattr='true']").length>0){
				var partesId = j.ret.split("::");
				var newValor = partesId[3];
				var cont=0;
				inchk.parent('td').parent("tr").children("td[relattr='true']").each(function(){
					$(this).attr("id",$(this).attr("id").replace(/::(\d+|nulo)$/,"::"+newValor));
				});
			}
			if(acc=="insert"){
				inchk.parent('td').parent("tr").children("td[relattr='true'][eseditable='true']").addClass("editable");
				doEditables();
			}else{
				if(inchk.parent('td').parent("tr").children("td[relattr='true']").hasClass('editable')){
					inchk.parent('td').parent("tr").children("td[relattr='true']").removeClass("editable").css("cursor","auto").html("");
					inchk.parent('td').parent("tr").children("td[relattr='true'][eseditable='true']").editable("destroy");
				}
			}
		}
	}
	inchk.next("img").fadeOut("slow",function(){
		$(this).remove();
		inchk.fadeIn("slow");
	});
};

var processSaving = function(j,self) {
	switch(j.error) {
		case 0:
			if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
				location.reload(true);
			}
			if (!j.value.subfields) {
			    $(self).html(j.value);
		    } else {
				$(self).html(unescape(j.value.principal));
				
				$("img.mag",$(self)).bind("click",magImage).css("cursor","pointer").attr("title",_("clickagrandar"));
				if (j.value.subfields) {
					for (var idx in j.value.subfields) {
						if (document.getElementById(idx)) {
						    document.getElementById(idx).innerHTML = unescape(j.value.subfields[idx]);
                            // $("#"+idx).hmtl(j.value.subfields[idx]); =>
                            // no funciona... caracteres especiales?
					    }
					}
				}
			}
			break;
		default:
			if (typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true) {
				$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},submit: function(v,m){location.reload(true);}});
			} else {
				$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
				$(self).html(self.revert);
			}
		break;
	}

};

var saveEditable = function(value,settings) {
	switch (settings.type) {
		case "fsfilefs":
			var opts = {op:'jeditable',acc:'save',
				'value[name]':$(".name",this).val(),
				'value[tmp_name]':$(".tmp_name",this).val(),
				'value[size]':$(".size",this).val(),
				'value[type]':$(".type",this).val()
			,id:this.id};
		break;
		default:
			var opts = {op:'jeditable',acc:'save',value:value,id:this.id,dsdmass:true};
		break;
	}
	var self = this;
	$(self).html(settings['indicator']);
	
	
	$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
		processSaving(j,self);
	});
};

var doEditables = function(){
	$(".jdate").editable(saveEditable, {
	    indicator : '<img src="icons/loader.gif">',
	    event   : "dblclick",
	    placeholder: 'dobleclick para editar',
	    type : "date",
	    onblur: "",
	    loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"
	}).css("cursor","n-resize");
	
	$("td.hexcolor, td.hexcolor .hexcolor").editable(saveEditable, {
	    indicator : '<img src="icons/loader.gif">',
	    event   : "dblclick",
	    type : "color",
	    onblur: "cancel",
	    loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"
	}).css("cursor","n-resize");

	$(".jdateESP").editable(saveEditable, {
	    indicator : '<img src="icons/loader.gif">',
	    event   : "dblclick",
	    placeholder: 'dobleclick para editar',
	    type : "dateTime",
	    onblur: "",
	    loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"
	}).css("cursor","n-resize");
	
	$(".jtime").editable(saveEditable, {
	    indicator : '<img src="icons/loader.gif">',
	    event   : "dblclick",
	    placeholder: 'dobleclick para editar',
	    type : "time"
	}).css("cursor","n-resize");
	
	$.each( "text,textarea,fsfilefs".split(","), function(i,o){
		$(".editable[type='"+o+"']").editable(saveEditable, {
	    	event   : "dblclick",
		    placeholder: 'dobleclick para editar',
	    	type : o,
			indicator : '<img src="icons/loader.gif">',
			onblur : 'cancel',
			onblurlook : function () { look = ((transClicked)? true:false) ; transClicked = false; return look; }
		}).css("cursor","n-resize");
	});

	$.each("textareaTags,multiselect,multiselectnoreal,select".split(","), function(i,o){
		$(".editable[type='"+o+"']").editable(saveEditable, {
	    	event   : "dblclick",
		    placeholder: 'dobleclick para editar',
	    	type : o,
			indicator : '<img src="icons/loader.gif">',
			loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load",
			onreset : function(ff,x){
	    		if($(x).find('.multiSelect').hasClass('focus')) {
                    return false;
                } else {
                    return true;
                }
	    	}
		}).css("cursor","n-resize");
	});
	
	$(".editable[type='ajaxfileupload']").editable("./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=save&id=", {
	    indicator : '<img src="icons/loader.gif">',
        type      : 'ajaxupload',
        event   : "dblclick",
	    placeholder: 'dobleclick para editar',
        processSaving : processSaving
    }).css("cursor","n-resize");
};

var miniLoader = new Image();
miniLoader.src = "./icons/miniloader.gif";

 
$(function(){

	doEditables();
	
	var  totales =0;
	var  sels =0;
 
 	$('td.multiselect input').each(function(i) {
 			if ($(this).is(":checked")) {
 			    sels++;
		    }
 			totales ++;
 	});
 	
 	if (totales==sels) {
 	    $("#chk_msMASTERMASS").attr("checked","checked");
    }
 	
	$('td.multiselect input').bind('change',function(){
		/*
		 * newFieldsAction(J,oo,p)
		 * */
		var inchk = $(this);
		inchk.attr("disabled","disabled");
		var acc = (inchk.is(":checked")? "insert":"delete");
		inchk.after("<img src=\""+miniLoader.src+"\" />");
		var datos = inchk.parent('td').attr('id');
		var aDatos = datos.split("::");
		if(acc == 'insert'){
			var opts = {op:'tablon_mass',acc:'requireddata',value:datos};
			$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
				if(j.error){
					inchk.removeAttr("checked").removeAttr("disabled");
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true}});
					return;
				}else if(j.fields){
					newFieldsAction(j,inchk,acc,datos);
				}else{
					var opts = {op:'tablon_mass',acc:acc,value:datos};
					inchk.fadeOut("slow",function() {
						$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
							doSaveMass(j,inchk,acc);
			    		});
						inchk.removeAttr("disabled");
					});
				}
			});
		}else{
			var opts = {op:'tablon_mass',acc:acc,value:datos};
			inchk.fadeOut("slow",function() {
				$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
					doSaveMass(j,inchk,acc);
	    		});
				inchk.removeAttr("disabled");
			});
		}
 	});

 	$("#chk_msMASTERMASS").bind("change",function() {
 		if ($(this).is(":checked")) {
 			$.prompt("¿Desea seleccionar todas las filas?",{ buttons: { Ok: true, Cancel: false },callback:function(v,m){
 				if (!v) {
 					$("#chk_msMASTERMASS").removeAttr("checked");
 					return;
 				}
 				var hasta = $('td.multiselect input:not(:checked)').length;
 				//var i = 0;
 				var str = "";
 				$('td.multiselect input:not(:checked)').each(function(i) {
 					//str += ((i!=0)? ',':'');
 					//i++;
 					var inchk = $(this);
 					inchk.attr("checked","checked");
 					inchk.attr("disabled","disabled");
 					var acc = "insert";
 					inchk.after("<img src=\""+miniLoader.src+"\" />");
 					var opts = {op:'tablon_mass',acc:acc,value:inchk.parent('td').attr('id')};
 					inchk.fadeOut("slow",function() {
 						$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
 			    			if (!j.ret) {
 			    				$.prompt('error',{buttons: {Ok: true}});
 							} else {
 								inchk.parent('td').attr('id',j.ret);
 								if(j.req == true){
	 			    				var aId = j.ret.split("::");
	 			    				var aIds = inchk.parent('td').attr('id').split("::");
	 			    				//str += aId[3];
	 			    				var valAct = $("#almacen").val()+""+((i!=0)? ',':'')+""+aId[3];
	 			    				$("#almacen").val(valAct);
	 	 							if(i==(hasta-1)){
	 	 	 							$('#masseditar').attr("href",$('#masseditar').attr("href").replace(/%id%/g,$("#almacen").val()));
	 	 	 	 		 				$("#almacen").val("");
	 	 	 	 		 				$('#masseditar').trigger('click');
	 	 	 	 		 				document.location=$('#masseditar').attr('href');
	 	 	 	 		 				//return true;
	 	 	 						}
 								}
 								inchk.parent('td').parent("tr").children("td[relattr='true'][eseditable='true']").addClass("editable");
 			    				doEditables();
 							}
 							inchk.next("img").fadeOut("slow",function(){
 								$(this).remove();
 								inchk.fadeIn("slow");
 							});
 			    		});
 					});
 					inchk.removeAttr("disabled");
 				});
			}});

 		} else {
 		 	$.prompt("¿Desea deseleccionar todas las filas?",{ buttons: { Ok: true, Cancel: false },callback:function(v,m){
 				if (!v) {
	 				$("#chk_msMASTERMASS").attr("checked","checked");
 					return;
 				}
 				
				$('td.multiselect input:checked').each(function() {
						$(this).removeAttr("checked").trigger("change");
				});
			}});
 		}
 	});
 	
});
