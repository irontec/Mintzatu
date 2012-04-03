 /* Fichero javascript para la clase tablon,
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

var transClicked = false;

var currentPlantilla = false;

var supertaBlon = function(){


	$('a.opts').Tooltip({ 
	    track: true, 
	    delay: 450, 
	    showURL: false, 
	    showBody: " - ", 
	    opacity: 0.85 
	});
	$('a.optsLink').Tooltip({ 
	    track: true, 
	    delay: 450, 
	    showURL: false, 
	    showBody: " - ", 
	    opacity: 0.85 
	});
	
	$('#tablonsearchnewtable img.helpsearch').Tooltip({ 
	    track: true, 
	    delay: 150, 
	    showURL: false, 
	    showBody: " - ", 
	    opacity: 0.85 
	});	
	
	/*
	 * Bindear al los botones de generadores de string aleatorios, la generación del string y la inclusión de ese string al valor del input relacionado (el inmediatamente anterios) 
	 */
	if($(".dorandomstring").length>0){
		$(".dorandomstring").livequery('click',function(e){
			var self = $(this);
			var patron = self.attr("rpatterns");
			var largura = self.attr("rlengrh");
			var cadena = randomString(largura,patron);
		    var donde = self.prev(":input");
		    donde.val(cadena);
		});
	}
	
	var processSaving = function(j,self) {
			switch(j.error) {
				case 0:
					
					if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
						location.reload(true);
					}
					if (!j.value.subfields) $(self).html(j.value);
					else {
						$(self).html(unescape(j.value.principal));
						
						$("img.mag",$(self)).bind("click",magImage).css("cursor","pointer").attr("title",_("clickagrandar"));
						if (j.value.subfields) {
							for (var idx in j.value.subfields) {
								if (document.getElementById(idx))
									document.getElementById(idx).innerHTML = unescape(j.value.subfields[idx]);
								// $("#"+idx).hmtl(j.value.subfields[idx]); =>
								// no funciona... caracteres especiales?
							}
						}
					}

				break;
				default:
					var yesObj = {};
					yesObj[_('Ok')] = true;
					if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
						$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj,submit: function(v,m){location.reload(true);}});
					}else{
						$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj});
						$(self).html(self.revert);
					}
				break;
			}
	
	};
	
	var processNewSaving = function(j,before) {
		
		switch(j.error) {
			case 0:
			
					if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
						var yesObj = {};
						yesObj[_('Ok')] = true;
						if (typeof(j.notifywheninsert) == 'string') $.prompt(j.notifywheninsert,{buttons: yesObj,submit: function(v,m){location.reload(true);}});
						else location.reload(true);
					}
					if (typeof before == "function"){
						
						before(j);
						break;
					}
					var modelTR = $('#trModel');
					var newTR = $(modelTR).clone(true);
					$(newTR).attr('id', unescape(j.idTR));
					if($(modelTR).prev().hasClass('par')) 
						$(newTR).addClass('impar');
					else
						$(newTR).addClass('par');
					
					$("td",$(newTR)).each(function() {
						if ($(this).attr('id')) {
							var newID = $(this).attr('id').replace(/%id%/g,j.id);
							$(this).attr('id',newID);
							
							if (j.values[newID]) $(this).html(unescape(j.values[newID]));
						}
						$(this).html($(this).html().replace(/%id%/g,unescape(j.id)));
					});
					$(newTR).removeClass("tablonClone");

					$(newTR).find(".deleteRow").bind("click",doDeleteRow);
					$(modelTR).before(newTR);
					dojeditables(newTR);
					//.css("cursor","pointer").attr("title",_("eliminar"))
					// $(".deleteRow",$(newTR)).bind("click",doDeleteRow);
					if(typeof calFunc == "function"){
						calFunc(j,$(newTR));
					}
					
			break;
			default:
				var yesObj = {};
				yesObj[_('Ok')] = true;
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj,submit: function(v,m){location.reload(true);}});
				}else{
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj});
					$(self).html(self.revert);
				}
			break;
		}
		
		var ie6 = (jQuery.browser.msie && jQuery.browser.version < 7);
		if(ie6)jQuery(window).unbind('scroll',ie6scroll);// ie6, remove the
															// scroll event
		// var jqif = fade = '<iframe src="" class="cargandoTablon"
		// id="cargandoTablon"></iframe>';
		var jqif = $("body").children('#cargandoTablon');
		jqif.fadeOut('fast',function(){
			jqif.remove();
		});
		$("body").css("cursor","default");
	};

	/*
	 * reProcessNewSaving
	 * Se llama al guardar un elemento dependiente de una plantilla externa (oPlt, fPlt)
	 * 
	 * j: Resultado de la llamada ajax
	 * elPrompt: Prompt donde actualizar el campo
	 * fName: Nombre del campo a actualizar
	 * before: función a llamar antes de guardar los datos.
	 */
	var reProcessNewSaving = function(j,elPrompt,fName,before) {
		
		switch(j.error) {
			case 0:
					if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
						location.reload(true);
					}
					if (typeof before == "function"){
						
						before(j);
						break;
					}
					var field = elPrompt.find("[name='"+fName+"']");
					
					var newoption = $("[name='id_book_type'] :last").clone(true); // WTFF? 
					newoption.attr("value",unescape(j.id));
					newoption.text(j.values[j.defaultFLD]);
					field.append(newoption);
					newoption.attr("selected","selected");
			break;
			default:
				var yesObj = {};
			    yesObj[_('Ok')] = true;
				if(typeof(j.refreshAfter) != 'undefined' && j.refreshAfter == true){
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj,submit: function(v,m){location.reload(true);}});
				}else{
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj});
					$(self).html(self.revert);
				}
			break;
		}
		
		var ie6 = (jQuery.browser.msie && jQuery.browser.version < 7);
		if(ie6)jQuery(window).unbind('scroll',ie6scroll);// ie6, remove the
															// scroll event
		// var jqif = fade = '<iframe src="" class="cargandoTablon"
		// id="cargandoTablon"></iframe>';
		var jqif = $("body").children('#cargandoTablon');
		jqif.fadeOut('fast',function(){
			jqif.remove();
		});
		$("body").css("cursor","default");
	};

	var saveEditable = function(value, settings) {
        var reqType = 'GET';
		switch (settings.type) {
			case "fsfilefs":
                var value = {
                        op:'jeditable',acc:'save',
                        'value[name]':$(".name",this).val(),
                        'value[tmp_name]':$(".tmp_name",this).val(),
                        'value[size]':$(".size",this).val(),
                        'value[type]':$(".type",this).val()
                        ,id:this.id
                };
                break;
            case "textarea":
            case "glocation":
            	reqType = 'POST';
			default:
			    var getOpts = 'op=jeditable&acc=save&id=' + this.id + '&';
			    var value = {value:value};
			    break;
		}
		var self = this;
		$(self).html(settings['indicator']);
		$("#hiddenFieldsUp input[type=hidden]").each(function() {
			if(typeof($(this).attr('onlyTriggers')) != 'undefined'&& $(this).attr('onlyTriggers') == "true"){
				getOpts += "CONDT__" + $(this).attr("name") + "=" + $(this).val() + '&';
			}else{
			    getOpts += "COND__" + $(this).attr("name") + "=" + $(this).val() + '&';
			}
		});
		
        $.ajax({
            type: reqType,
            url: "./modules/tablon/ajax/ops_tablon.php?" + getOpts,
            dataType: 'json',
            data: value,
            success: function(j) {
                processSaving(j,self);
            }
        });
	};
	
	var dojeditables = function(ambito){ 	
		$(".jdate",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    indicator : '<img src="icons/loader.gif">',
		    event   : "dblclick",
		    type : "date",
		    onblur: "",
		    loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"
		}).css("cursor","n-resize");
		
		
		$(".glocation",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    indicator : '<img src="icons/loader.gif">',
		    event   : "dblclick",
		    type : "glocation",
		    onblur: ""
		}).css("cursor","n-resize");
		
		//$(".hexcolor",$("td.hexcolor")).editable(saveEditable, {	   	
		$("td.hexcolor, td.hexcolor .hexcolor",ambito).not("[id$='%id%']").editable(saveEditable, {
		    indicator : '<img src="icons/loader.gif">',
		    event   : "dblclick",
		    type : "color",
		    onblur: "cancel",
		    loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"
		}).css("cursor","n-resize");
	
		$(".jdateESP",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    indicator : '<img src="icons/loader.gif">',
		    event   : "dblclick",
		    type : "dateTime",
		    onblur: "",
		    loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"
		}).css("cursor","n-resize");
		
		$(".jtime",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    indicator : '<img src="icons/loader.gif">',
		    event   : "dblclick",
		    type : "time"
		}).css("cursor","n-resize");
	
		$(".mappoint",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    indicator : '<img src="icons/loader.gif">',
		    event   : "dblclick",
		    type : "mappoint"
		}).css("cursor","n-resize");
		
		
		$.each( "text,textarea,fsfilefs".split(","), function(i,o){
			$(".editable[type='"+o+"']",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    	event   : "dblclick",
                idFld   : $(this ).attr("id"),
		    	type : o,
				indicator : '<img src="icons/loader.gif">',
				onblur : 'cancel',
		    	onblurlook : function () { look = ((transClicked)? true:false) ; transClicked = false; return look; }
			}).css("cursor","n-resize");
		});
	
		$.each("textareaTags,multiselect,multiselectnoreal,select".split(","), function(i,o){
			$(".editable[type='"+o+"']",ambito).not("[id$='%id%']").editable(saveEditable, {	   	
		    	event   : "dblclick",
		    	type : o,
		    	onblur: "cancel",
				indicator : '<img src="icons/loader.gif">',
				loadurl : "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load",
				onreset : function(ff,x){
		    		if($(x).find('.multiSelect').hasClass('focus')) return false;
		    		else return true;
		    	}
			}).css("cursor","n-resize");
		});
	
	
		$(".editable[type='ajaxfileupload']",ambito).not("[id$='%id%']").editable("./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=save&id=", {
		    indicator : '<img src="icons/loader.gif">',
	        type      : 'ajaxupload',
	        event   : "dblclick",
	        processSaving : processSaving      
	    }).css("cursor","n-resize");
	};
    
	dojeditables('body');
  
 	var doOpenView = function() {
 		var self=this;
 		$(self).parents("tr").addClass('tmpOver');
 		var opts = {op:'openView',acc:"openView",id:$(self).parents("tr").attr("id"),campos:$(self).attr('id')};
 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
 			var yesObj = {};
			yesObj[_('Ok')] = true;
 				switch (j.error) {
					case 0:
						txt="<div class=\"readerbox\">";
						res = j.result;
						for(var a in res){
							txt+="<div class=\"reader\">"+res[a]+"</div>";
						}
						txt+="</div>";
						
						$.prompt(txt,{buttons: yesObj,top:'2%',submit: function(v,m){ window.setTimeout(function(){$(self).parents("tr").removeClass('tmpOver');},5000); return true;}});
					break;
					default:
						$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj,top:'2%',submit: function(v,m){ window.setTimeout(function(){$(self).parents("tr").removeClass('tmpOver');},5000); return true;}});
					break;
				
				}
 		
 		});
 	};


 	var doDeleteRow = function() {
 		var self=this;
 		var reconfirm = false;
 		if ($('#tablon_edit').length==1) {
 			var nEntidad = ($('#tablon_edit').attr('genero')=='a')? 'esta':'este';
 			nEntidad += ' "'+$('#tablon_edit').attr('entidad')+'"';
 		} else {
 			var nEntidad = '"'+$(this).parents("tr").children("td:eq(1)").html()+'"';
 		}
 		if ($(this).attr("src").match(/undo\.png$/)) {
 			// var literal = '¿Seguro que desea deshacer eliminar '+nEntidad+'?'
 			var literal = _var('seguro_deseliminar',nEntidad);
 			var op = 'undelete';
 		} else {
 			$(this).parents("tr").children("td:eq(1)").html();
 			// var literal = '¿Seguro que desea eliminar '+nEntidad+'?';
 			var literal = _var('seguro_eliminar',nEntidad);
 			var op = 'delete';
 			if($(self).hasClass("reconfirm")){
 				reconfirm = true;
 				var literalr = _var('seguro_eliminar_descendientes',nEntidad);
 				/*$.prompt(literalr,{ buttons: yescancelObj,callback:function(){
 					if (!v) return;
 				}});*/
 			}
 		}
 		
 		var deletear = function(v,m){
 			if (!v) return;
 			var opts = {op:'tablon',acc:op,id:$(self).parents("tr").attr("id")};
 			$("#hiddenFieldsUp input[type=hidden]").each(function() {
 				//opts["COND__"+$(this).attr("name")] = $(this).val();
 				if(typeof $(this).attr('onlyTriggers') != undefined && $(this).attr('onlyTriggers') == "true"){
 					opts["CONDT__"+$(this).attr("name")] = $(this).val();
 				}else{
 					opts["COND__"+$(this).attr("name")] = $(this).val();
 				}
 			});
 		   	$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
				switch (j.error) {
					case 0:
						var yesObj = {};
						yesObj[_('Ok')] = true;
						if (op=='delete') {
							if(typeof(j.doundelete)!= 'undefined' && j.doundelete == 0){
								$(self).parents("tr").remove();
								return true; 
							}
							//donotdelete
							if(typeof(j.donotdelete)!= 'undefined' && j.donotdelete != 0){
								if(typeof(j.inuse)!='undefined' && j.inuse != 0){
									$.prompt("El registro no se ha elimado por que está en uso",{buttons: yesObj});
								}
								return true;
							}
							var src = './icons/undo.png';
							// var title = 'Deshacer Eliminar';
							var title = _('deseliminar');
							var textdecoration = 'line-through';
							var disabled = 'disabled';
						} else {
							var src = './icons/eraser.png';
							var title = _('eliminar');
							var textdecoration = 'none';
							var disabled = '';
						}
						if ($('#tablon_edit').length==1) {
							$("input,select,textarea,input:file",$(self).parents("table")).attr("disabled",disabled);
							$("span",$(self).parents("span")).html(title);
							$(".deleteRow",$(self).parents("tr")).attr("src",src).attr("title",title);
						} else {
							$("td",$(self).parents("tr")).css("text-decoration",textdecoration);
							$("input,select,textarea",$(self).parents("tr")).attr("disabled",disabled);
							$(".deleteRow",$(self).parents("tr")).attr("src",src).attr("title",title);
						}
					break;
					default:
						var yesObj = {};
					    yesObj[_('Ok')] = true;
						$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: yesObj});
					break;
				
				}
			});
 		   	return;
		};
		if ($(this).attr("nocheck")!="1"){
			/*prompt(literalr,{ buttons: yescancelObj,callback:function(){
					if (!v) return;
				}});*/
			
			var yescancelObj = {};
			yescancelObj[_('Si')] = true;
			yescancelObj[_('Cancelar')] = false;
			
			if(reconfirm == true){
				$.prompt(literal,{ buttons: yescancelObj,callback:function(v,m){
					if(v!=false)
						$.prompt(literalr,{ buttons: yescancelObj,callback:deletear});
					return;
				}});
			}else{
				$.prompt(literal,{ buttons: yescancelObj,callback:deletear});
			}
		}
		else deletear(true,'foo');
		$(this).attr("nocheck",0);
		
 	};
 	
 	   
 	$(".deleteRow").bind("click",doDeleteRow); //.attr("title",_("eliminar"))

 	/* MAXLENGTH DE SAFETEXT Y SAFETEXTAREA */
 	var maxlength = function(obj) {
 		var valor = obj.val();
 		var max = parseInt(obj.attr('maxlength'));
 		var len = valor.length;
 		//Si pasamos la longitud lo cortamos
		if(obj.val().length > max) {
			obj.val(obj.val().substring(0, max));
		}
 		var div = $('#maxlength');
 		if (div.length == 0) {
 			var $info = $('<div id="maxlength">'
 					+ '<span>Carácteres restantes:</span>&nbsp;<span class="res">' + (max - len) + '</span> de <span class="max">' + max + '</span>'
 					+ '</div>');
 			obj.before($info);
 		} else {
 			$('#maxlength .max').html(max);
 			$('#maxlength .res').html((max - len));
 		}
 	};

 	$('input[maxlength], textarea[maxlength]').live('blur', function(){
 		$('#maxlength').remove();
 	});

 	$('input[maxlength], textarea[maxlength]').live('focus', function(e) {
 		maxlength($(this));
 	});

 	$('input[maxlength], textarea[maxlength]').live('keyup', function(e) {
 		maxlength($(this));
 	});
 	/* MAXLENGTH DE SAFETEXT Y SAFETEXTAREA */

 	
	$('.deleteRow').Tooltip({ 
	    track: true, 
	    delay: 450, 
	    showURL: false, 
	    showBody: " - ", 
	    opacity: 0.85 
	});
 	
 	$(".deleteRow").parents('li').bind("click",doDeleteRow);
 	
 	
 	var doDuplicateRow = function() {
 		
 		var plantilla = $(this).attr('id');
 		var opts = {op:'tablon',acc:'copyfields',actRow: true,id:plantilla};
 		/*if ($("#tablononnew").length){
 			opts.tablononnew = $("#tablononnew").val(); 
 		}*/
 		var yesnoObj = {};
 		yesnoObj[_('Si')] = true;
 		yesnoObj[_('No')] = false;
 		$.prompt("Está seguro de crear a partir del actual?",{buttons: yesnoObj, submit:function(v,m){
 			if(v==false) return true;
 			$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,processNewSaving);
 			return true;
 		}});	
		
 	};
 	$(".duplicateEntity").bind("click",doDuplicateRow);
	$('.duplicateEntity').Tooltip({ 
		track: true, 
	    delay: 450, 
	    showURL: false, 
	    showBody: " - ", 
	    opacity: 0.85 
	});
 	$(".duplicateEntity").bind("click",doDuplicateRow);
 	
 	if($(".openView").length>0)
 		$(".openView").css("cursor","pointer").attr("title",_("openview")).bind("click",doOpenView);
 	
 	$("#chk_msMASTER").bind("change",function() {
 		if ($(this).is(":checked")) {
 			$('tr:not(.tablonClone) td.multiselect input:enabled').attr("checked","checked");
 		} else {
 			var yescancelObj = {};
			yescancelObj[_('Si')] = true;
			yescancelObj[_('Cancelar')] = false;
 			$.prompt(_('seguro_deseleccionar'),{ buttons: yescancelObj,callback:function(v,m){
 				if (!v) return;
 				$('tr:not(.tablonClone) td.multiselect input:enabled:not(.tablonClone)').removeAttr("checked");
 			}});
 		}
 	});
 	
 	function bindeoClickAOpts(aopts,link) {
 		if(link == false){
			var lngth = $('tr:not(.tablonClone) td.multiselect input:checked').length;
	 		if (lngth == 0) {
	 			$.prompt(_("no_filas_sel"));
	 			return false;
	 		}
 		}
 		var str = "";
 		for(i=0;i<lngth;i++) str += (((i!=0)? ',':'')+ $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parent("td").attr("id").replace(/ms::/g,""));
 		$(aopts).attr("href",$(aopts).attr("href").replace(/%id%/g,str));
 		return true;
	
	}
 	
	$("#optsTablon a.opts, .optsTablon a.opts").bind("click",function(){
		return bindeoClickAOpts($(this),false);
	});
	
	$("#optsTablon a.opts, .optsTablon a.opts").parents('li').bind('click',function(){ 
		var retornoTrigger = bindeoClickAOpts($(this).find('a'),false);
		if(retornoTrigger == true){
			document.location=$(this).find('a').attr('href');
			return true;
		}
		return false;
	});
	
	$("#optsTablon a.optsLink, .optsTablon a.optsLink").parents('li.backButton').bind('click',function(){
		var retornoTrigger = bindeoClickAOpts($(this).find('a'),true);
		if(retornoTrigger == true){
			document.location=$(this).find('a').attr('href');
			return true;
		}
		return false;
	});
	
	if($("[id=multiDelete]").length>0){
		$("[id=multiDelete]").parents("li").css("cursor","pointer").bind("click",function() {
			var entidad = $("#multiDelete").attr("objetoElim");
	 		var lngth = $('tr:not(.tablonClone) td.multiselect input:checked').length;
	 		if (lngth == 0) {
	 			$.prompt(_("no_filas_sel"));
	 			return;
	 		}
	 		if (lngth>1){
	 			var txt = _('eliminar_filas');
	 		}else{
	 			var txt = _('eliminar_fila');
	 		}
	 		txt += '<br /><ul>';
	 		var colT = 0;
	 		var cuantosTh = $("th").length;
	 		var texto = "";
	 		//tablon_tr_header
			while((($('th:eq('+colT+')').children("a").children("span.roll").length>0)?$('th:eq('+colT+')').children("a").children("span").text():$('th:eq('+colT+')').children("a").text()) != entidad && colT<=cuantosTh){
				colT++;
				texto = (($('th:eq('+colT+')').children("a").children("span.roll").length>0)?$('th:eq('+colT+')').children("a").children("span").text():$('th:eq('+colT+')').children("a").text());
			}
			if(texto!=entidad){
				colT=0;
			}
	 		for(i=0;i<lngth;i++) {
	 			var col = 1;
	 			if(colT == 0){
	 				while(($('td.multiselect input:checked:eq('+i+')').parents("tr").children("td:eq("+col+")").text()=="") && (col < cuantosTh)){
	 					col++;
	 				}
	 			}else{
	 				col = colT;
	 			}
	 			txt += '<li>'+$('td.multiselect input:checked:eq('+i+')').parents("tr").children("td:eq("+col+")").text()+'</li>';
	 		}
	 		txt += '</ul>';
	 		var yescancelObj = {};
			yescancelObj[_('Si')] = true;
			yescancelObj[_('Cancelar')] = false;
	 		$.prompt(txt,{ buttons: yescancelObj,callback:function(v,m){
				if (!v) return;
				for(i=0;i<lngth;i++) {
					var noCheck = true;
	 				$(".deleteRow",$('td.multiselect input:checked:eq('+i+')').parents("tr")).attr("nocheck",1).trigger("click");
			 	}
	 			
	 			return true;
	 		}});
	 	});
	}
 	var doCondition = function(){
		var self = $(this);
		var padre = self.parents("div.jqimessage");
		var nameBase = self.attr("name");
		var selected = self.val();
		var recursivo = new Array();
		var elPromp = self.parents("div#jqi");
		var contRec = 0;
		padre.find("tr").removeClass("condMostrado");
		if((self.attr("disabled") == false || !self.attr("disabled")) && self.attr("escondido")!=1){
			self.children("option").each(function() {
				var campos = new Array();
				if(typeof $(this).attr("id")!="undefined"){
					campos = $(this).attr("id").split("|");
				}
				if(campos.length>0){
					for(var i=0;i<campos.length;i++){
						if(campos[i] == "" || $("[name='"+campos[i]+"']").length == 0) continue;
						var objeto = padre.find("[name='"+campos[i]+"']");
						if(padre.find("[name='"+campos[i]+"']").parent("span").length>0){
							objeto = objeto.parent("span");
						}
						var tdPadre = objeto.parent("td");
						var trPadre = tdPadre.parent("tr");
						if ($(this).val()==selected){
							if($("[name='"+campos[i]+"']").length>0){
								tdPadre.children().attr("display","block").attr('escondido','0');
								//Si es multiselect ponemos todos los inputs con el escondido
								if (tdPadre.children('.multiSelectOptions').length > 0) {
									tdPadre.children('.multiSelectOptions').find('input').attr('escondido','0');
								}
								trPadre.addClass("condMostrado");
								trPadre.show();
								if(padre.find("[name='"+campos[i]+"']").hasClass("condicionante")){
									recursivo[contRec] = "[name='"+campos[i]+"']";
									contRec++;
								}
							}
						}
						else{
							if(!trPadre.hasClass("condMostrado")){
								tdPadre.children().attr("display","none").attr('escondido','1');
								//Si es multiselect ponemos todos los inputs con el escondido
								if (tdPadre.children('.multiSelectOptions').length > 0) {
									tdPadre.children('.multiSelectOptions').find('input').attr('escondido','1');
								}
								trPadre.hide();
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
	
 	if($('#newToline').length>0){
		$('#newToline').parent("li").css("cursor","pointer").bind("click",function() {
			var plantilla = $(this).attr('id');
	 		var opts = {op:'tablon',acc:'newfields',id:plantilla};
	 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(J) {
	 			switch(J.error) {
	 				case 0:
	 					var noEditables = new Array();
	 					var cont = 0;
	 					var txt = '<table>';
	 					for(var idx in J.fields){
	 						if(J.fields[idx].noEdit == true){
	 							noEditables[cont] = J.fields[idx].name;
	 							cont++;
	 						}
	 					}
	 						var opts = {op:'tablon',acc:'new',id:plantilla};
	 						var fields = {};
	 						var ret = true;
	 						for (var idx in J.fields) {
	 							var e = $("[name='"+J.fields[idx].name+"']",m);
	 							
	 							var vlr = e.val();
	 							if ((vlr=="")&& (J.fields[idx].req) && e.attr('escondido') != 1) {
	 								e.parent().children("input").css("border","dashed #f00 1px");
									ret = false;
	 							} else {
	 								if (J.fields[idx].clone) {
	 									
	 									aa = J.fields[idx].name.split('_');
	 									nextval = $("[name='"+aa[0]+"']",m).val();
	 									if (nextval!=vlr) {
	 									
	 										e.parent().append("<br /><small style=\"color:#f00;\">"+_('valores_no_coinciden')+"</small>");
	 									
	 										ret = false;
	 									}else{
	 									
	 										opts["FLD__"+aa[0]] = vlr;
	 									}
	 									
	 								}else{
	 									opts["FLD__"+J.fields[idx].name] = vlr;
	 								}
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
	 						var url = "./modules/tablon/ajax/ops_tablon.php";
			
							$.getJSON(url,opts,processNewSaving);
	 						return ret;
						
	 				break;
	 			}
	 		}); 	 		
		});
 	}

	
	var renewID=false;
	var velPrompt = false;
	
	/*
	 * :renew:
	 *  Se llama al querer crear una nueva opción dependiente de una plantilla externa (oPlt, fPlt). P.e. enumbdselectorcreate
	 *  
	 * :Params:
	 *  
	 * oPLT: Plantilla a usar
	 * velPrompt: Prompt de origen?
	 * e: evento
	 * fName: Nombre del campo dependiente
	 * oo: opciones para el segundo callback (también)
	 * p: función de callback para el segundo ajax (también)
	 */
	var renew = function(oPLT,velPrompt,e,fName,oo,p){
	 		var plantilla ="new::"+oPLT+"::0";
 			var opts = {op:'tablon',acc:'newfields',id:plantilla};
	 		if ($("#tablononnew").length){
	 			opts.tablononnew = $("#tablononnew").val(); 
	 		}
	 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(J) {
	 			switch(J.error) {
	 				case 0:
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
	 							var reqChar;
	 							reqChar = '';
	 							if(J.fields[idx].req == true)
	 							{
	 								reqChar = '*';
	 							}
		 						txt += '<tr><td>'+J.fields[idx].alias+ ' ' + reqChar + '</td><td>';
		 						txt += J.fields[idx].data;
		 						if(J.fields[idx].noEdit == true){
		 							noEditables[cont] = J.fields[idx].name;
		 							cont++;
		 						}
		 						txt += '</td></tr>';
		 						if(J.fields[idx].textoAyuda!==false && J.fields[idx].textoAyuda!==undefined){
		 							txt += '<tr><td colspan="2" class="textoMenor textoIndent">'+J.fields[idx].textoAyuda+'</td></tr>';
		 						}
	 						}
	 					}
	 					txt += '</table>';
	 					if(txtC == ""){
	 						txtC = '<table>';
	 						txtC += '<caption>';
	 	 					txtC += _str('Nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</caption>';
	 					}
	 					txt = txtC+txt;
	 					var selected = false;
	 					
	 					
	 					var bObj = {};
	 			    	bObj[_('Guardar')] = true;
	 			    	bObj[_('Cancelar')] = false;
	 			    	
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
		 						buttons: bObj,
		 						submit:function(v,m){
		 							if(oo != undefined){
		 								// Si se han especificado opciones diferentes para este ajax también
		 					 			var opts = oo;
		 					 		}else{
		 					 			var opts = {op:'tablon',acc:'new',id:plantilla};
		 					 		}
	 								if (!v) return true;
	 								var fields = {};
	 								var ret = true;
	 								for (var idx in J.fields) {
	 									var e = $("[name='"+J.fields[idx].name+"']",m);
	 									if (e.attr('escondido') == '1') {
	 										// Si el campo depende de otro, en este caso aunque el plt marque requerido, su valor no será necesaria. Lo indicamos mediente este nuevo campo de opts
	 										if(J.fields[idx].req) {
	 											opts["NoReqDepend__"+J.fields[idx].name] = "seguir";
	 										}
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
	 										if ($("textarea.tiny, textarea.custom_tiny",e.parents("td")).length>0) {
	 											e.parents("td").find("table:first").css("border","dashed #f00 1px");
	 										}else{
	 											e.parent().children(":input").css("border","dashed #f00 1px");
	 										}
											ret = false;
	 									} else{
	 										if ($("textarea.tiny, textarea.custom_tiny",e.parents("td")).length>0) {
	 											e.parents("td").find("table:first").css("border","");
	 										}else{
	 											e.parent().children(":input").css("border","");
	 										}
	 										
	 									}
	 									
	 									if (J.fields[idx].clone) {
	 											aa = J.fields[idx].name.split('_');
	 											nextval = $("[name='"+aa[0]+"']",m).val();
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
									//	opts["COND__"+$(this).attr("name")] = $(this).val();
	 								});
	 								
	 								var url = "./modules/tablon/ajax/ops_tablon.php";
					 				if ($("#tablononnew").length){
					 					opts.tablononnew = $("#tablononnew").val(); 
					 				}
	 								if (ret) {
	 									if ($("input:file",m).length>0) { // Ajax
																			// file
																			// Upload
	 										var aIds = new Array();
	 										$("input:file",m).each(function(index) {
	 											aIds[index] = $(this).attr("id");
	 											$(this).attr("name","FLD__"+$(this).attr("name"));
	 										});
	 										url += "?";
	 										for (var idx in opts) {
	 											if (idx=="value") continue;
												url += idx+'='+escape(opts[idx])+"&";
											}
											$.ajaxFileUpload({
												url: url,
												secureuri:false,
												fileElementId: aIds,
												dataType: 'json',
												success: function (j) {
												 	reProcessNewSaving(j,velPrompt,fName,function(j){
												 		
												 		renewID = j.id;
													 	velPrompt.find("button[value=true]").trigger('click');
												 	});
												 	/**/
												},
												error: function (data, status, e) {
													alert(e);
												}
					          				});
										} else { // No hay ficheros, no hay iFrame
											
											if(oo != undefined && p!= undefined){
												// Si se han especificado una función de 'retorno' diferente
				 					 			$.getJSON(url,opts,p);
				 					 		}else{
				 					 			$.getJSON(url,opts,function(j){
				 					 				/*console.log(j);*/
				 					 				reProcessNewSaving(j,velPrompt,fName);
				 					 				/*renewID = j.id;
												 	velPrompt.find("button[value=true]").trigger('click');*/
				 					 			});
				 					 		}
											
										}
	 									
									}
									return ret;
								}
	 					});
	 					elPrompt.find(".condicionante:enabled").each(doCondition);
						elPrompt.find(".condicionante").bind('change',doCondition);
						$('.timepick').timepicker({divId: "mytimepicker"});
						dodate();
						if($('.hexcolor').length>0) docolor();
						
					 	if($('.glocationField').length>0){
					 		$('.glocationField').each(function(){
					 			//$(this)
					 			doGlocation($(this));
					 		});
					 	}
					 	
						
						for(i=0;i<noEditables.length;i++){
	 						elPrompt.find("[name='"+noEditables[i]+"']").attr("display","none");
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
									multiple: false,
									mustMatch: false,
									autoFill: true
								});
						 	});
					 	}

						if(elPrompt.find('textarea.tiny, textarea.custom_tiny').length>0){
							loadTiny();
						}
						if($(".dorandomstring").length>0){
							$(".dorandomstring").bind('click',function(e){
								var self = $(this);
								var patron = self.attr("rpatterns");
								var largura = self.attr("rlengrh");
								var cadena = randomString(largura,patron);
							    var donde = self.prev(":input");
							    donde.val(cadena);
							});
						}

	 				break;
	 			}
	 		
	 	});
		
		
		
		
		
		
		
		
	};
	
	
	var oPLT=false;
	if($('[id=newInline]').length>0){
	 	$('[id=newInline]').parents("li").css("cursor","pointer").bind("click",function(e,o,oo,p) {
	 		
	 		/* Nuevo parámetros ocpionales que si no se le pasan, no funciona como simplemente módulo tablon
	 		 * Si tiene estos párametros es que algún otro módulo está reaprovechando esta utilidad pero con sus especificaciones:
	 		 * parámeto 'o': opciones a pasar en el primer ajax a ejecutar, newfields (plantillas, campos específicos, por ejemplo....)
	 		 * parámeto 'oo': opciones a pasar en el segundo ajax a ejecutar, new (formato de los datos, etc. por ejemplo)
	 		 * parámeto 'p': función a ejecutar tras el guardado de datos (en vez de el processNewSaving)
	 		 */
	 		var plantilla = $(this).attr('id');
	 		currentPlantilla = plantilla;
	 		if(o != undefined){
	 			// Si se han especificado opciones diferentes para el ajax
	 			var opts = o;
	 		}else{
	 			var opts = {op:'tablon',acc:'newfields',id:plantilla};
	 		}
	 		if ($("#tablononnew").length){
	 			opts.tablononnew = $("#tablononnew").val(); 
	 		}
	 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(J) {
	 			switch(J.error) {
	 				case 0:
	 					var noEditables = new Array();
	 					var cont = 0;
	 					var txtC = "";
	 					var txt = "";
	 					/*
	 					 * Por si se quiere especificar el caption desde el ajax
	 					 * 
	 					 * */
	 					for(var idx in J.fields){
	 						if(typeof(J.fields[idx].title)!= "undefined"){
	 	 						var txtC = '<table>';
	 							txtC += "<caption>"+J.fields[idx].title+"</caption>";
	 							txtC += J.fields[idx].data;
	 						}else{
	 							var reqChar;
	 							reqChar = '';
	 							if(J.fields[idx].req == true)
	 							{
	 								reqChar = '*';
	 							}
		 						txt += '<tr><td>'+J.fields[idx].alias+ ' ' + reqChar + '</td><td>';
		 						txt += J.fields[idx].data;
		 						if(J.fields[idx].noEdit == true){
		 							noEditables[cont] = J.fields[idx].name;
		 							cont++;
		 						}
		 						
		 						if(J.fields[idx].fplt != "undefined" && J.fields[idx].fplt != false){
		 							txt += '<img src="icons/add.png" class="new_value" fplt="'+J.fields[idx].fplt+'" data="'+J.fields[idx].name+'" alt="'+ _str('Crear_nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'"/>';
		 						}
		 						
		 						txt += '</td></tr>';
		 						if(J.fields[idx].textoAyuda!==false && J.fields[idx].textoAyuda!==undefined){
		 							txt += '<tr><td colspan="2" class="textoMenor textoIndent">'+J.fields[idx].textoAyuda+'</td></tr>';
		 						}
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
	 					
	 				
	 					if (J.ofields && J.opl != false) txt = txt+'<a id="create" >'+ _str('Crear_nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</a>';
	 					var selected = false;
	 					
	 					var bObj = {};
	 			    	bObj[_('Guardar')] = true;
	 			    	bObj[_('Cancelar')] = false;
	 			    	
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
		 						buttons: bObj,
		 						submit:function(v,m){
		 							if(o != undefined && oo != undefined){
		 								// Si se han especificado opciones diferentes para este ajax también
		 					 			var opts = oo;
		 					 		}else{
		 					 			var opts = {op:'tablon',acc:'new',id:plantilla};
		 					 		}
	 								if (!v) return true;
	 								var fields = {};
	 								var ret = true;
	 								for (var idx in J.fields) {
                                        var e = $("[name='"+J.fields[idx].name+"']",m);
                                        var vlr = e.val(); // Valor por defecto, ya trataremos los casos especiales luego.
                                        
                                        if(e.attr('escondido') == '1'){
                                            // Si el campo depende de otro, en este caso aunque el plt marque requerido, su valor no será necesaria. Lo indicamos mediente este nuevo campo de opts
                                            if (J.fields[idx].req) {
                                                opts["NoReqDepend__"+J.fields[idx].name] = "seguir";
                                            }
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
                                           //Si es de tipo multiselect* se recogen los id's de los checkeados separados por comas
                                           case 'multiselectnoreal':
                                           case 'multiselectng':
                                               var tmp_values = e.filter('[checked]');
                                               var cont = 0;
                                               vlr = '';
                                               while (cont < tmp_values.length){
                                                   if(cont == 0){
                                                       vlr = $(tmp_values[cont]).val();
                                                   }else{
                                                       vlr += ","+$(tmp_values[cont]).val();
                                                   }
                                                   cont++;
                                               }
                                               break;
                                        }	 								    

	 									if ((vlr=="")&& (J.fields[idx].req) && e.attr('escondido') != 1) {
	 										if ($("textarea.tiny, textarea.custom_tiny",e.parents("td")).length>0) {
	 											e.parents("td").find("table:first").css("border","dashed #f00 1px");
	 										}else{
	 											e.parent().children(":input").css("border","dashed #f00 1px");
	 										}
											ret = false;
	 									} else{
	 										if ($("textarea.tiny, textarea.custom_tiny",e.parents("td")).length>0) {
	 											e.parents("td").find("table:first").css("border","");
	 										}else{
	 											e.parent().children(":input").css("border","");
	 										}
	 										
	 									}
	 									
	 									if (J.fields[idx].clone) {
	 											aa = J.fields[idx].name.split('_');
	 											nextval = $("[name='"+aa[0]+"']",m).val();
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
	 								
	 								if (renewID){
	 									
	 									opts["FLD__"+J.fields[idx].name] = renewID;
	 									
	 								}
	 								
	 								$("#hiddenFields input[type=hidden]").each(function() {
 									if(typeof $(this).attr('onlyTriggers') != undefined && $(this).attr('onlyTriggers') == "true"){
 										opts["CONDT__"+$(this).attr("name")] = $(this).val();
 									}else{
										opts["COND__"+$(this).attr("name")] = $(this).val();
 									}
	 								});
	 								
	 								var url = "./modules/tablon/ajax/ops_tablon.php";
					 				if ($("#tablononnew").length){
					 					opts.tablononnew = $("#tablononnew").val(); 
					 				}
	 								if (ret) {
	 									if ($("input:file",m).length>0) { // Ajax
																			// file
																			// Upload
	 										var aIds = new Array();
	 										$("input:file",m).each(function(index) {
	 											aIds[index] = $(this).attr("id");
	 											$(this).attr("name","FLD__"+$(this).attr("name"));
	 										});
	 										
	 										url += "?";
	 										for (var idx in opts) {
	 											if (idx=="value") continue;
												url += idx+'='+escape(opts[idx])+"&";
											}
											$.ajaxFileUpload({
												url: url,
												secureuri:false,
												fileElementId: aIds,
												dataType: 'json',
												success: function (j) {
												 	processNewSaving(j); 
					
												},
												error: function (data, status, e) {
													alert(e);
												}
					          				});
										} else { // No hay ficheros, no hay iFRAME
											if(o != undefined && oo != undefined && p!= undefined){
												// Si se han especificado una función de 'retorno' diferente
				 					 			$.getJSON(url,opts,p);
				 					 		}else{
				 					 			$.getJSON(url,opts,processNewSaving);
				 					 		}
											
										}
									}
									return ret;
								}
	 					});
	 					
	 					velPrompt = elPrompt;
	 					
	 					elPrompt.find("#create").bind('click',function(){renew(oPLT,elPrompt);});
	 					elPrompt.find(".new_value").bind('click',function(e){renew($(this).attr("fplt"),elPrompt,e,$(this).attr("data"));});
	 					elPrompt.find(".condicionante:enabled").each(doCondition);
						elPrompt.find(".condicionante").bind('change',doCondition);
						$('.timepick').timepicker({divId: "mytimepicker"});
						dodate();
						if($('.hexcolor').length>0) docolor();
						if($('.glocationField').length>0){
					 		$('.glocationField').each(function(){
					 			//$(this)
					 			doGlocation($(this));
					 		});
					 	}
						
						for(i=0;i<noEditables.length;i++){
	 						elPrompt.find("[name='"+noEditables[i]+"']").parents("tr:first").remove();
	 					}
						if(elPrompt.find('select.multiselect').length>0){
							//elPrompt.find('select.multiselect').multiSelect({ oneOrMoreSelected: '*' });
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
									multiple: false,
									mustMatch: false,
									autoFill: true
								});
						 	});
					 	}
						if(elPrompt.find('.wymeditor').length>0){
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
							    			    $.each(o[perf].plugins,function(indice,valor) {
							    			    	plugins[indice] = valor;
							    			    });
							    			    
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
						}
						if(elPrompt.find('textarea.tiny, textarea.custom_tiny').length>0){
							loadTiny();
						}
						if($(".dorandomstring").length>0){
							$(".dorandomstring").bind('click',function(e){
								var self = $(this);
								var patron = self.attr("rpatterns");
								var largura = self.attr("rlengrh");
								var cadena = randomString(largura,patron);
							    var donde = self.prev(":input");
							    donde.val(cadena);
							});
						}
	
	 				break;
	 			}
	 		}); 	
	 	});
	}
	if($('#newInlineEdit').length>0){
	 	$('#newInlineEdit').parent("li").css("cursor","pointer").bind("click",function() {
	 		var idEdit = $(this).attr('id');
	 		var partes = idEdit.split("::");
	 		var plantilla = partes[0]+"::"+partes[1]+"::"+partes[4];
	 		var opts = {op:'tablon',acc:'newfields',id:plantilla};
	 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(J) {
	 			switch(J.error) {
	 				case 0:
	 					var noEditables = new Array();
	 					var cont = 0;
	 					var txt = '<table><caption>';
	 					txt += _str('Nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</caption>'; 
	 					for(var idx in J.fields){
 							var reqChar;
 							reqChar = '';
 							if(J.fields[idx].req == true)
 							{
 								reqChar = '*';
 							}
	 						txt += '<tr><td>'+J.fields[idx].alias+ ' ' + reqChar + '</td><td>';
	 						txt += J.fields[idx].data;
	 						if(J.fields[idx].noEdit == true){
	 							noEditables[cont] = J.fields[idx].name;
	 							cont++;
	 						}
	 						
	 						if(J.fields[idx].fplt != "undefined" && J.fields[idx].fplt != false){
	 							txt += '<img src="icons/add.png" class="new_value" fplt="'+J.fields[idx].fplt+'" data="'+J.fields[idx].name+'" alt="'+ _str('Crear_nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'"/>';
	 						}
	 						
	 						txt += '</td></tr>';
	 						if(J.fields[idx].textoAyuda!==false && J.fields[idx].textoAyuda!==undefined){
	 							txt += '<tr><td colspan="2" class="textoMenor textoIndent">'+J.fields[idx].textoAyuda+'</td></tr>';
	 						}
	 					}
	 					txt += '</table>';
	 					
	 					var bObj = {};
	 			    	bObj[_('Guardar')] = true;
	 			    	bObj[_('Cancelar')] = false;
	 					
	 					var elPrompt = $.prompt(txt,{loaded:function() {$("input:file",$(this)).customFile({width:230});},width:600, buttons: bObj,submit:function(v,m){
	 						
	 						if (!v) return true;
	 						var ie6 = (jQuery.browser.msie && jQuery.browser.version < 7);	
	 						var fade = '<div class="cargandoTablon" id="cargandoTablon"></div>';
							if((jQuery.browser.msie && jQuery('object, applet').length > 0) || ie6)
							fade = '<iframe src="" class="cargandoTablon" id="cargandoTablon"></iframe>';
							var jqif = $("body").prepend(fade).children('#cargandoTablon');
							var getfoffset = function(){ return (document.documentElement.scrollTop || document.body.scrollTop) + 'px'; };
							var getjoffset = function(){ return (document.documentElement.scrollTop || document.body.scrollTop) + Math.round(15 * (document.documentElement.offsetHeight || document.body.clientHeight) / 100) + 'px'; };
							var ie6scroll = function(){ jqif.css({ top: getfoffset() }); };
							
							jqif.css({ position: "absolute", height: (ie6)? "100%":$("body").height(), width: "100%", top: (ie6)? getfoffset():0, left: 0, right: 0, bottom: 0, zIndex: 998, display: "none", opacity: 0.6 });
							if(ie6) jQuery(window).scroll(ie6scroll);// ie6, add
																		// a scroll
																		// event to
																		// fix
																		// position:fixed
							jqif.fadeIn('fast');						
	 						$("body").css("cursor","progress");
	 						var opts = {op:'tablon',acc:'new',id:plantilla};
	 						var fields = {};
	 						var ret = true;
	 						for (var idx in J.fields) {
	 							var e = $("[name='"+J.fields[idx].name+"']",m);
	 							if(e.attr('escondido') == '1'){
									continue;
								}
	 							
	 							var vlr = e.val();
	 							if ((vlr=="")&& (J.fields[idx].req) && e.attr('escondido') != "1") {
	 								e.parent().children("input").css("border","dashed #f00 1px");
									ret = false;
	 							} else {
	 								if (J.fields[idx].clone) {
	 									
	 									aa = J.fields[idx].name.split('_');
	 									nextval = $("[name='"+aa[0]+"']",m).val();
	 									if (nextval!=vlr) {
	 									
	 										e.parent().append("<br /><small style=\"color:#f00;\">"+_('valores_no_coinciden')+"</small>");
	 									
	 										ret = false;
	 									}else{
	 									
	 										opts["FLD__"+aa[0]] = vlr;
	 									}
	 									
	 								}else{
	 									opts["FLD__"+J.fields[idx].name] = vlr;
	 								}
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
	 						var url = "./modules/tablon/ajax/ops_tablon.php";
	 						if (ret) {
	 							if ($("input:file",m).length>0) { // Ajax file
																	// Upload
	 								var aIds = new Array();
									$("input:file",m).each(function(index) {
										aIds[index] = $(this).attr("id");
										$(this).attr("name","FLD__"+$(this).attr("name"));
									});
	 								url += "?";
	 								for (var idx in opts) {
	 									if (idx=="value") continue;
										url += idx+'='+escape(opts[idx])+"&";
									}
									$.ajaxFileUpload({
										url: url,
										secureuri:false,
										fileElementId: aIds,
										dataType: 'json',
										success: function (j) {
										 	processNewSaving(j); 
	
										},
										error: function (data, status, e) {
											alert(e);
										}
					          		});
						          	
	 							
	 							
	 							} else {
	 								opts["id"] = idEdit;
									$.getJSON(url,opts,processNewSaving);
								}
							} 						
	
	 						return ret;
	 					}});
	 					elPrompt.find(".condicionante:enabled").each(doCondition);
	 					elPrompt.find(".condicionante").bind('change',doCondition);
						for(i=0;i<noEditables.length;i++){
	 						elPrompt.find("[name='"+noEditables[i]+"']").attr("display","none");
	 					}
						if(elPrompt.find('select.multiselect').length>0){
							//elPrompt.find('select.multiselect').multiSelect({ oneOrMoreSelected: '*' });
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
									multiple: false,
									mustMatch: false,
									autoFill: true
								});
						 	});
					 	}
	 				break;
	 			}
	 		}); 	
	 	});
	}
 	/* LANDER */
 	
 	if($('#newZip').length>0){
	 	$('#newZip').parents("li").bind("click",function() {
	 		var plantilla = $(this).attr('id');
	 		var opts = {op:'tablon',acc:'newfields',id:plantilla};
	 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(J) {
	 			switch(J.error) {
	 				case 0:
	 					var noEditables = new Array();
	 					var cont = 0;
	 					var txt = '<table><caption>';
	 					txt += _str('Nuevo_ZIP','',$("#tablonContainer table").attr("entidad"))+'</caption>'; 
	 					 
	 					for(var idx in J.fields){
	 						if (J.fields[idx].ftype=="text"||J.fields[idx].ftype=="textarea") continue;
 							var reqChar;
 							reqChar = '';
 							if(J.fields[idx].req == true)
 							{
 								reqChar = '*';
 							}
	 						txt += '<tr><td>'+J.fields[idx].alias+ ' ' + reqChar + '</td><td>';
	 						txt += J.fields[idx].data;
	 						if(J.fields[idx].noEdit == true){
	 							noEditables[cont] = J.fields[idx].name;
	 							cont++;
	 						}
	 						txt += '</td></tr>';
	 						if(J.fields[idx].textoAyuda!==false && J.fields[idx].textoAyuda!==undefined){
	 							txt += '<tr><td colspan="2" class="textoMenor textoIndent">'+J.fields[idx].textoAyuda+'</td></tr>';
	 						}
	 					}
	 					txt += '</table>';
	 					
	 					var bObj = {};
	 			    	bObj[_('Guardar')] = true;
	 			    	bObj[_('Cancelar')] = false;
	 			    	
	 					var elPrompt = $.prompt(txt,{loaded:function() {$("input:file",$(this)).customFile({width:230});},width:600, buttons: bObj,submit:function(v,m){
	 						
	 						if (!v) return true;
	 						var opts = {op:'tablon',acc:'newzip',id:plantilla};
	 						var fields = {};
	 						var ret = true;
	 						for (var idx in J.fields) {
	 							var e = $("[name='"+J.fields[idx].name+"']",m);
	 							
	 							var vlr = e.val();
	 							if ((vlr=="")&& (J.fields[idx].req) && e.attr('escondido') != 1) {
	 								e.parent().children("input").css("border","dashed #f00 1px");
									ret = false;
	 							} else {
	 								if (J.fields[idx].clone) {
	 									
	 									aa = J.fields[idx].name.split('_');
	 									nextval = $("[name='"+aa[0]+"']",m).val();
	 									if (nextval!=vlr) {
	 									
	 										e.parent().append("<br /><small style=\"color:#f00;\">"+_('valores_no_coinciden')+"</small>");
	 									
	 										ret = false;
	 									}else{
	 									
	 										opts["FLD__"+aa[0]] = vlr;
	 									}
	 									
	 								}else{
	 									opts["FLD__"+J.fields[idx].name] = vlr;
	 								}
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
	 						var url = "./modules/tablon/ajax/ops_tablon.php";
	 						if (ret) {
	 							if ($("input:file",m).length>0) { // Ajax file
																	// Upload
	 								var aIds = new Array();
									$("input:file",m).each(function(index) {
										aIds[index] = $(this).attr("id");
										$(this).attr("name","FLD__"+$(this).attr("name"));
									});
	 								url += "?";
	 								for (var idx in opts) {
	 									if (idx=="value") continue;
										url += idx+'='+escape(opts[idx])+"&";
									}
									$.ajaxFileUpload({
										url: url,
										secureuri:false,
										fileElementId: aIds,
										dataType: 'json',
										success: function (j) {
										 	
										 	for (var idx in j ){
										 		
										 		processNewSaving(j[idx]);
										 	
										 	}
										 	
										 	 
	
										},
										error: function (data, status, e) {
											alert(e);
										}
					          		});
						          	
	 							
	 							
	 							} else {
									$.getJSON(url,opts,processNewSaving);
								}
							} 						
	
	 						return ret;
	 					}});
	 					elPrompt.find(".condicionante:enabled").each(doCondition);
	 					elPrompt.find(".condicionante").bind('change',doCondition);
						for(i=0;i<noEditables.length;i++){
	 						elPrompt.find("[name='"+noEditables[i]+"']").attr("display","none");
	 					}
						
	 				break;
	 			}
	 		}); 	
	 	});
 	}


    // JABI
    if($('#newCMIS').length>0){
        var $srcNewCMIS = $("#newCMIS");
	 	$('#newCMIS').parents("li").bind("click",function() {
	 		var plantilla = $(this).attr('id');
            var targetField = $(this).attr('rel');
            var cmisProfile = $(this).attr("data-profile");

	 		var opts = {op:'tablon',acc:'newfields',id:plantilla};
	 		$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(J) {
                var found = false;
	 			if (J.error != 0) {
                    alert("Error recuperando los campos");
                    return;
                }

                var noEditables = new Array();
                var cont = 0;
                var txt = '<table><caption>';
                txt += $srcNewCMIS.text()+'</caption>'; 

                for(var idx in J.fields){
                    if (J.fields[idx].ftype=="text"||J.fields[idx].ftype=="textarea") continue;
                    var reqChar;
                    reqChar = '';
                    if(J.fields[idx].req == true) {
                        reqChar = '*';
                    }
                    txt += '<tr><td>'+J.fields[idx].alias+ ' ' + reqChar + '</td><td>';
                    if (J.fields[idx].name == targetField) {
                        txt += '<div class="fscmis" rel="'+targetField+'">' +
                                '<span class="selectfsfilefs">Selecciona un fichero</span><div class="fileTreeDemo"></div>';
                        txt += '<input type="hidden" name="'+targetField+'" val=""/>';
                        txt += '</div>';
                        found = true;

                    } else {
                        txt += J.fields[idx].data;
                    }

                    if(J.fields[idx].noEdit == true){
                        noEditables[cont] = J.fields[idx].name;
                        cont++;
                    }
                    txt += '</td></tr>';
                    if(J.fields[idx].textoAyuda!==false && J.fields[idx].textoAyuda!==undefined){
                        txt += '<tr><td colspan="2" class="textoMenor textoIndent">'+J.fields[idx].textoAyuda+'</td></tr>';
                    }
                } // For flds
                txt += '</table>';
                if (!found) {
                    alert("Configuración CMIS no encontrada.");
                    return;
                }
				var bObj = {};
		    	bObj[_('Guardar')] = true;
		    	bObj[_('Cancelar')] = false;
                var elPrompt = $.prompt(txt,{
                    loaded : function() {

                        $("input:file",$(this)).customFile({width:230});
                        $(".fscmis",$(this)).each(function() {
                                    var ffile = $(this);
                                    $(".fileTreeDemo",ffile)
                                        .bind("mousemove",function(e) {e.stopPropagation();})
                                        .css("height","300px").css("overflow","auto")
                                        .fileTree(
                                            {   
                                                root: '/',
                                                extraparams:{id:plantilla,fld:ffile.attr("rel")},
                                                script: './modules/tablon/ajax/ops_tablon.php?op=newcmis&cmis_profile='+cmisProfile+'&acc=list&id='+plantilla+'&fld='+targetField },
                                                function(file) {
                                                    // Funcion al seleccionar fichero
                                                    $("[name='"+targetField+"']",ffile).val(file);
                                                    selected = true;
                                                });
                        });
                    },
                    width:600,
                    buttons: bObj,
                    submit:function(v,m){
                        if (!v) return true;
                        var opts = {op:'tablon',acc:'new',id:plantilla,newcmis:true,cmis_profile:cmisProfile};
                        var fields = {};
                        var ret = true;
                        for (var idx in J.fields) {
                            var e = $("[name='"+J.fields[idx].name+"']",m);
                            var vlr = e.val();
                            if ((vlr=="")&& (J.fields[idx].req) && e.attr('escondido') != 1) {
                                e.parent().children("input").css("border","dashed #f00 1px");
                                ret = false;
                            } else {
                                if (J.fields[idx].clone) {
                                    aa = J.fields[idx].name.split('_');
                                    nextval = $("[name='"+aa[0]+"']",m).val();
                                    if (nextval!=vlr) {
                                        e.parent().append("<br /><small style=\"color:#f00;\">"+_('valores_no_coinciden')+"</small>");
                                        ret = false;
                                    }else{
                                        opts["FLD__"+aa[0]] = vlr;
                                    }
                                } else{
                                    opts["FLD__"+J.fields[idx].name] = vlr;
                                }
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
                        var url = "./modules/tablon/ajax/ops_tablon.php";
                        if (ret) {
                            if ($("input:file",m).length>0) { // Ajax file
                                                                // Upload
                                var aIds = new Array();
                                $("input:file",m).each(function(index) {
                                    aIds[index] = $(this).attr("id");
                                    $(this).attr("name","FLD__"+$(this).attr("name"));
                                });
                                url += "?";
                                for (var idx in opts) {
                                    if (idx=="value") continue;
                                    url += idx+'='+escape(opts[idx])+"&";
                                }
                                $.ajaxFileUpload({
                                    url: url,
                                    secureuri:false,
                                    fileElementId: aIds,
                                    dataType: 'json',
                                    success: function (j) {
                                        
                                        for (var idx in j ){
                                            
                                            processNewSaving(j[idx]);
                                        
                                        }
                                        
                                         

                                    },
                                    error: function (data, status, e) {
                                        alert(e);
                                    }
                                });
                            } else {
                                $.getJSON(url,opts,processNewSaving);
                            }
                        }
                        return ret;
                    }
                });
                
                elPrompt.find(".condicionante:enabled").each(doCondition);
                elPrompt.find(".condicionante").bind('change',doCondition);
                for(i=0;i<noEditables.length;i++){
                    elPrompt.find("[name='"+noEditables[i]+"']").attr("display","none");
                }
             }); // newfiles JSON
        }); // BindClick
    } // NewCMIS

 	
 	/* 
 	 * EIDER
 	 */
 	var oPLT=false;
 	if($('#newFrom').length>0){
	 	$('#newFrom').parents("li").css("cursor","pointer").bind("click",function(e) {
	 		var plantilla = $(this).attr('id');
	 		var opts = {op:'tablon',acc:'copyfields',id:plantilla};
	 		if ($("#tablononnew").length){
	 			opts.tablononnew = $("#tablononnew").val(); 
	 		}
	 		var yesnoObj = {};
	 		yesnoObj[_('Si')] = true;
	 		yesnoObj[_('No')] = false;
	 		$.prompt("Está seguro de crear "+$(this).children("a").text().toLowerCase()+"?",{buttons: yesnoObj, submit:function(v,m){
	 			if(v==false) return true;
	 			$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,processNewSaving);
	 			return true;
	 		}});	
	 	});
 	}
	
 	
 	if($('.dosomethingFLD').length>0){
 	    
	 	$('.dosomethingFLD').bind("click",function(){
	 		var self = $(this);
	 		var confirmt, fields = false;
	 		confirmt = self.attr('confirm');
	 		var fields = self.attr('fields');
	 		
	 		//value:$(this).parents("td").attr("id");
	 		var opts = {op:'tablon',acc:'dosomething',id:self.parents("td").attr("id")};
	 		var do_doSomething = function() {

	 		    if (arguments.length > 0) {
	 		        for (var idx in arguments[0]) {
    	                opts[arguments[0][idx].name] = arguments[0][idx].value;
	 		        }
	 		    }
	 		    
	 		    $.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(data){
	 	 				if (data.ret){
	 	 					$.prompt(data.ret,{
 	 							buttons: {
	 	 							Ok: true
 	 							}, 
 	 							submit: function(v,m){ 
 									return true;
 									} 
 	 							}
	 	 					);
	 	 				}
						
						//console.log(data.exec);
						if (data.exec) eval(data.exec);	 	 				
	 	 				
	 	 			});   
	 		};
	 		
	 		
	 		if ((confirmt) || (fields))  {
	 		    if (fields) {
	 		        var lit = confirmt? confirmt + '<br />':'';
	 		        
	 		        try {
	 		            var flds = eval('['+unescape(fields)+']');
                    } catch(e) {
                        	    alert("Algo anda mal, campo fields de dosomething mal formado... continua sin prompt");
	 		            do_doSomething();   
	 		        }
	 		        
	 		        for(var idx in flds[0]) {
	 		            lit += flds[0][idx]+':<br /><input type="text" name="'+idx+'" /><br /><br />';
	 		        }
	 		         
	 		    } else {
	 		        var lit = confirmt;
	 		    }
	 		    
	 		    var yesnoObj = {};
	 	 		yesnoObj[_('Si')] = true;
	 	 		yesnoObj[_('No')] = false;
	 	 		
	 			$.prompt(lit,{buttons: yesnoObj, submit:function(v,m){
	 	 			if(v==false) return true;
                    var args = (fields)? $("input[type=text]",m).serializeArray() : null;
	 	 			do_doSomething(args);
	 	 			return true;
	 	 		}});	
	 			
	 		} else {
	 		    do_doSomething();
	 		}
	 		
	 		return false;
	 	});
 	}
	
	if ($("a.opts.popup").length>0) { // opciones especiales tipo popup
		$("a.opts.popup").bind("click",function(e) {
			e.preventDefault();
			var that = $(this);
			var contenidoInit = '<table><caption>'+$("img",that).attr("alt")+'</caption>';
			var actions = {
				_CallIt : function(arg) {
					$.getJSON("./modules/tablon/ajax/ops_tablon.php",arg,actions._Process);
				},
				_Process : function(data) {
					if (data.exec) eval(data.exec); // :D con dos cojones
					if (!data.html) {
					    if(data.postexec) eval(data.postexec); //  con dos cojones también, cuando no queremos HTML de vuelta pero si ejecutar una acción 
					    return; // Si el servidor no devuelve html, salir.
					}
					var contenido = contenidoInit + '<tr><td colspan="2">'+((data.html)? data.html:"Respuesta NO COHERENTE del servidor.")+'</td></tr>';
					if (data.fields) {
						 // PHP nos devuelve campos, para preguntar al usuario.
						
						for(var idx in data.fields) {
							switch(typeof data.fields[idx]) {
								case "boolean":
									contenido += '<tr><td>'+idx+': </td><td><input type="text" name="'+idx+'" /></td></tr>';
								break;
								case "object":
									contenido += '<tr><td>'+idx+': </td><td><select name="'+idx+'">';
									for(var idx2 in data.fields[idx]) contenido += '<option value="'+idx2+'">'+data.fields[idx][idx2]+'</option>';
									contenido += '</select></td></tr>';
								break;
							}
						}

						contenido += '</table>';
						var yescancelObj = {};
						yescancelObj[_('Si')] = true;
						yescancelObj[_('Cancelar')] = false;
						$.prompt(contenido,{buttons: yescancelObj,submit : function(v,m) {
							if (!v) {
							    var cancel = (data.execCancel)? data.execCancel:false;
							    if (cancel) eval(cancel);
							    return true;
							}
							var opts2 = opts;
							opts2['data'] = true;
							$("input,select,textarea",m).each(function() {
								// posible bug si un campo se llama igual que cualquier variable que ya este...
								opts2[$(this).attr("name")] = escape($(this).val());
							});
							actions._CallIt(opts2);
							return true;
						}});
					} else {
						// PHP nos devuelve sólo HTML... mostrar y fuera.
						var accionPosterior = (data.postexec)? data.postexec:false;
						var yesObj = {};
						yesObj[_('Ok')] = true;
						$.prompt(contenido+'</table>',{buttons: yesObj,submit : function(v,m) {
							if (accionPosterior) eval(accionPosterior);
							return true;
						}});
					}
				}
			}; // actions

			var opts = {};
			opts['op'] = 'popup';
			opts['class'] = $(this).attr("rel");
			opts['id'] = $(this).attr("relid");

			actions._CallIt(opts);
		});
	} 

	
 	if($("img.mag").length>0)
 		$("img.mag").bind("click",magImage).css("cursor","pointer").attr("title",_("clickagrandar"));
 	
 	if($("#__user_filtrado").length>0)
 	    $("#__user_filtrado").bind("click",function() {
                $("#tablonsearchnew").slideDown();
                $(this).slideUp();
        }).css("cursor","pointer");
        
 	if($("#__hide_filtrado").length>0)
        $("#__hide_filtrado").bind("click",function() {
                $("#__user_filtrado").slideDown();
                $("#tablonsearchnew").slideUp();
                return false;
        }).css("cursor","pointer");
        
        if ($("#tablonsearchnew td.used").is("td")) $("#__user_filtrado").trigger("click");
 	
 		if ($('#tablonsearchnew select.multiselect').length>0) {
			//$('#tablonsearchnew select.multiselect').multiSelect({ oneOrMoreSelected: '*' });
 			$('#tablonsearchnew select.multiselect').each(function(){
				if($(this).attr('multiselectOnlyOne') != undefined && $(this).attr('multiselectOnlyOne') == "true"){
		 			$(this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
		 		}else{
		 			$(this).multiSelect({ oneOrMoreSelected: '*' });
		 		}
			});
		};

		/**
		 * Enums con autocomplete
		 */
		if ($('#tablonsearchnew select.autocomplete').length > 0) {
            $('#tablonsearchnew select.autocomplete').each(function(index, element){
                var data = [];
                var $select = $(element);
                var name = $select.attr('name');
                var $auto_input = $('<input type="text" name="' + name + '_autocomplete_helper" />').prependTo($select.parent());

                /**
                 * Preparamos un array con todas las opciones del select
                 */
                $select.children('option').each(function(index, cur_element) {
                    data[index] = {key: $(cur_element).val(), value: $(cur_element).text()};
                    if ($select.val() == $(cur_element).val()) {
                        $auto_input.val($(cur_element).text());
                    }
                });
                
                $auto_input.autocomplete_old(data, {
                        matchContains : true,
                        formatItem: function(item) {
                        return item.value;
                    }
                }).result(function (event, item) {
                    $select.val(item.key);
                    return item.key;
                });
                $select.hide();
            });
		}
	
 	
		if ($('#trans_result').length){
			$('#trans_switcher').bind('click',function(){
				if ($('#trans_container').hasClass('absolute_container')){
					$('#trans_container').removeClass('absolute_container');
					return false;
				}
				$('#trans_container').addClass('absolute_container');
				
			});
			
			var trans_idenField = $('#trans_idenField').val(); 
			var trans_matchField = $('#trans_matchField').val();
			var trans_from = $('#trans_from').val();
			var anyres = "";
			
			$('.trans').bind('dblclick',function(){
				transClicked = true;
				
				trans_lang = $(this).attr('lang'); 
				if(trans_lang=="jp") trans_lang="ja";
				
				totrans = $(this).parents('tr').children('.trans_idenField').html();
				
				anyres = totrans+": from "+trans_from+" to "+trans_lang+" ";
				
				$('#trans_result').html(totrans);
				$('#trans_result').translate(trans_from, trans_lang);
				//$('#trans_result').html(anyres);
				$(this).find('form').append('<button alt="asdghasdg" class="translatebutton" >&nbsp;</button>');
				self = $(this);
				save = $(this).find('form').find('input[type=submit]');
				//save.blur(function(){alert('f');return false;});
				$('.translatebutton').bind('click',function(){
					
					self.find('form').find('[name=value]').val($('#trans_result').html());
					$('.translatebutton').fadeOut('slow',function(){$('.translatebutton').remove();} );
					return false;
				});
				//console.log($('#takata').val());
			});
			
			$('.trans_edit_td1').bind('click',function(){
				transClicked = true;
				trans_lang = $(this).attr('lang'); 
				if(trans_lang=="jp") trans_lang="ja";
				
				//totrans = $(this).parents('table').children('.trans_idenField').html();
				var tmp = $('<div />');
				
				totrans = $(this).parents('table').find('.trans_idenField_edit_td2').find('.contValor').find(':input').val();
				tmp.html(totrans);
				totrans=tmp.text();
				anyres = totrans+": from "+trans_from+" to "+trans_lang+" ";
				$('#trans_result').html(totrans);
				$('#trans_result').translate(trans_from, trans_lang);
				//$('#trans_result').html(anyres);
				$(this).find('form').append('<button alt="asdghasdg" class="translatebutton" >&nbsp;</button>');
				self = $(this);
				save = $(this).find('form').find('input[type=submit]');
				//save.blur(function(){alert('f');return false;});
				$('.translatebutton').bind('click',function(){
					
					self.find('form').find('[name=value]').val($('#trans_result').html());
					$('.translatebutton').fadeOut('slow',function(){$('.translatebutton').remove();} );
					return false;
				});
				//console.log($('#takata').val());
			});

		}
		/*
		$('body').translate('en', 'es', {
			each: function(i){
				console.log( this.translation[i] ) // i==this.i
			}
		})
		*/
		
			
			
			
			
			
			
			
			
			var temp =false;
			
			if ($('.multiopsh').length>2){
				$('.multiopsh').bind('mouseout',function(){
					temp = window.setTimeout(function(){
						$('.optsMore').removeClass('clicked');
						$('#hiddenMultiops').slideUp();	
						$('#moreopts').attr('src',$('#moreopts').attr('csrc'));
					},500);
					
				}).bind('mouseover',function(){
					window.clearTimeout(temp);
				});
				
				$('.optsMore').bind('click',function(){
						alert("si");
					if ($(this).hasClass('clicked')){
						$(this).removeClass('clicked');
						$('#hiddenMultiops').slideUp();
						$('#moreopts').attr('src',$('#moreopts').attr('csrc'));
						return false;
					}
					$(this).addClass('clicked');
					$('#hiddenMultiops').slideDown();
					$('#moreopts').attr('src',$('#moreopts').attr('osrc'));
				});
			
			}
			
			/*
			 * Roll de las cabeceras, imágenes, etc...
			 */
			var rollables = $('.tablon').find('a').not('.opts').not('.nomove');
			rollables.each(function(i,v){
				var roll = false;
				var overA = false;
				var overACalled = false;
				var spanSize = $(this).find('span').width();
				$(this).bind('mouseover',function(){
					var self = $(this);
					if (!self.hasClass('changed')){
						var text = self.text();
						/*
						var img = '<img src="'+self.find('img').attr('src')+'" alt="'+self.find('img').attr('alt')+'" />';
						self.html(img+"<span class=\"roll\">"+text+"</span>");
						*/
						self.find('span').addClass('roll').css('display', 'block');
						self.addClass('changed');
					}
					
					overA = window.setTimeout(function(){
						if (!overACalled) {
							var newoff = 0;
							span = self.find('.roll');
							roll = window.setInterval(
									function(){
										span.css('margin-left','-'+newoff+'px');
										newoff++;
										if (newoff >= (spanSize)){	
											newoff = 0;		
										}
									},
									50
							);
						}
						overACalled = true;
					},200);
				}).bind('mouseout',function(){
				    /*
				     * Cuando salimos del lugar, tenemos que parar el scroll
				     */
					if($(this).hasClass('changed')) {
						$(this).removeClass('changed');
					}
					window.clearTimeout(overA);
					window.clearInterval(roll); 
					overACalled = false;
					span = $(this).find('.roll');
					span.css('margin-left','0px');
				}).find("span").css("display","block");
			});
			
			
			
			$('.buttonSlide').bind('click',function(){
				var self = $(this);
				var o = self.parents('tr.slidedTR').find('td.tablon_edit').find('.contValor');
				if (o.hasClass('opened')){
					o.removeClass('opened');
					self.attr('src',self.attr('osrc'));
				}else{
					o.addClass('opened');
					self.attr('src',self.attr('csrc'));
				}
			}).Tooltip({ 
			    track: true, 
			    delay: 450, 
			    showURL: false, 
			    showBody: " - ", 
			    opacity: 0.85 
				});
			
			
			/*$('tr.slidedTR td.tablon_edit').each(function(i){
				
				
			});*/
			
			$('tr.slidedTR td.tablon_edit:eq(0)').find('.contValor').addClass('opened');
			tmpb = $('tr.slidedTR td.cabecera:eq(0)').find('.buttonSlide');
			tmpb.attr('src',tmpb.attr('csrc'));
			
			
		$(".botonpaginado").bind("click",function(e) {
			e.stopPropagation();
			e.preventDefault();
			var o = $(this).prev("input[name='rutapaginado']");
			var ruta = o.val();
			var pag = o.prev("input[name='inputpaginado']").val();
			document.location = ruta+pag;
		
		
		});

		$("#manualLimit").bind("keydown",function(e) {
			var validKeys = [47,48,49,50,51,52,53,54,55,56,57,58,8,37,38,39,40,9,46,35,36,96,97,98,99,100,101,102,103,104,105];
			if (e.keyCode == 13) {
				document.location = document.location +"&manualLimit="+parseInt($(this).val());
				return;
			}
			if ($.inArray(e.keyCode,validKeys) == -1) {
			    e.preventDefault();
			} else {
				var mInput = $(this);
				window.setTimeout(function() {
					var fSearch = $("#tablonsearchnew").parents("form:eq(0)");	
					$("input[name='manualLimit']",fSearch).val(mInput.val());
				},400);
			}
		});
		
		/*
		 * Marcamos en rojo los valores requeridos que no están completados
		 */
		if ($('td.required').length > 0) {
		    
    		$("td.required").each(function() {
    		    if ($(this).html() === '') {
    		        $(this).css('background-color', '#ffaaaa');
    		    }
    		});
    
    		$("td.required").live('change', function() {
                if ($(this).html() === '') {
                    $(this).css('background-color', '#ffaaaa');
                } else {
                    $(this).css('background-color', '');
                }
    		});
		}

		
};
 
$(document).ready(supertaBlon);


var magImage = function(e) {
        e.stopPropagation();
        e.preventDefault();
        if ($(this).hasClass("filefs")) {
            var newSrc = $(this).parents("a:eq(0)").attr("href");   
        } else {
            var newSrc = $(this).attr("src").replace(/\?thumb/g,'');
        }
 		var im = new Image();
 		im.src = newSrc;
 		$(im).bind("load",function() {
 			$.prompt('<img class="bigThumb" src="'+newSrc+'" /><p style="position:absolute;bottom:-7px;left:5px;color:#A66;font-size:7px;">['+_('regenerar_cache')+']</p>',{ loaded:function() {$("p",$(this)).bind("click",function() {$(this).siblings().attr("src",$(this).siblings().attr("src")+"?nocache");}).css("cursor","pointer");} , width : this.width+30,top:"5%",buttons: { Ok: true }});	
		});
};



/*
 * 
 * 
 * Comento por qué lo estoy poniendo aquí:
 * 
 * por que no siempre se carga el tiny conf de por defecto.
 * y? se podría poner una clase en el <a> y bindear, en lugar de "href=javascript:" no? 
 * 
 */
function toggleEditor(id) {

	if (!tinyMCE.get(id))
		tinyMCE.execCommand('mceAddControl', false, id);
	else
		tinyMCE.execCommand('mceRemoveControl', false, id);

}
