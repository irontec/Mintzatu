

function bind_directory(t){

	t.find('li.directory > a').contextMenu({
		menu: 'myMenu'
	}, function(action, el, pos) {
		rel = $(el).attr("rel");
		var element = $(el).parent('li'); 
		var expanded = element.hasClass("expanded");
		var action=action;
		var minitxurro = $.base64Decode ( txurro );
		var u_url = location.href+"&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&sop="+action+"&rel="+rel+"&sid="+sid+"&skipbrowser=true";
		
		var settings = {
				flash_url : "modules/vhd/Flash/swfupload.swf",
				upload_url: location.protocol+"//"+location.host+location.pathname+"modules/vhd/ajax/ajax.php"+location.search+"&sop="+action+"&rel="+rel+"&sid="+sid+"&skipbrowser=true&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&",
				/*crossdomain_url : location.protocol+"//"+minitxurro+"@"+location.host+location.pathname+"modules/vhd/ajax/crossdomain.xml",*/
				post_params: {"PHPSESSID" : sid},
				file_size_limit : "300 MB",
				file_types : "*.*",
				file_types_description : "All Files",
				file_upload_limit : 100,
				file_queue_limit : 0,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button settings
				button_image_url: "modules/vhd/css/images/XPButtonUploadText_61x22.png",
				button_width: "61",
				button_height: "22",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: '',
				button_text_style: ".theFont { font-size: 16; background:none; } ",
				button_text_left_padding: 12,
				button_text_top_padding: 3,


				
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete	// Queue plugin event
				
				
			};						
		
		
		if (action=="new_file"){
			
			var txt = '<table>';
				txt+= "<caption>Subir ficheros</caption>";
				txt+= '</table>';
				
				txt+= '<div id="swf_container">';

				/*txt+= '<form id="" action="'+location.protocol+"//"+location.host+location.pathname+"modules/vhd/ajax/ajax.php"+location.search+"&sop="+action+"&rel="+rel+"&sid="+sid+"&skipbrowser=true&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&"+'" method="post" enctype="multipart/form-data">';
				txt+= '<div class="fieldset flash" id="fsUploadProgress">';
				txt+= '<span class="legend">Upload Queue</span>';
				txt+= '</div>';
				txt+= '<div id="divStatus">0 Files Uploaded</div>';
				txt+= '<div>';
				
				txt+= '<input type="file" name="Filedata" />';
				
				txt+= '<input type="submit" />';
				txt+= '</div>';
				txt+= '</form>';*/					
				
				txt+= '<form id="form1" action="index.php" method="post" enctype="multipart/form-data">';
				txt+= '<div class="fieldset flash" id="fsUploadProgress">';
				txt+= '</div>';
				txt+= '<div id="divStatus">0 Files Uploaded</div>';
				txt+= '<div>';
				txt+= '<span id="spanButtonPlaceHolder"></span>';
				txt+= '<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 22px;" />';
				txt+= '</div>';
				txt+= '</form></div>'; 							
				
			
				var elPrompt = 
					$.prompt(txt,{ loaded:function() { 
						
						swfu = new SWFUpload(settings);
						
						
					},
						width:600,
						buttons: { ok: false },
						submit:function(v,m){
							
			 				ee = element.parents('li').eq(0);
					 			
				 				
				 				//var ee = element.parent('ul').prev("a").parent("li");
			 					var ee = element;
				 				
				 				if (ee.hasClass("expanded")){
					 				ee.children('a').trigger(superevent);
					 				ee.children('a').trigger(superevent);
					 			}else{
					 				ee.children('a').trigger(superevent);
					 			}
							
								if (!v) return true;
								
								
						}
					});
				
			
			
			return;
		}
		
		
		
		
		
		
		
		
		
		
		$.getJSON(location.href+"&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&rel="+rel, 
				function(J) {
			
					
			
 					var noEditables = new Array();
 					var cont = 0;
 					var txtC = "";
 					var txt = "";

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
	 					if (action!="delete"){
	 						txtC += 'Directorio</caption>';
	 					}else{
	 						txtC += "Borrar?";
	 					}
	 					
	 				}
	 				txt = txtC+txt;
	 				var otxt = "";
	 				var tmpotxt = "";
	 				if (J.ofields){
	 	 				oPLT = J.opl;
	 	 			}
	 					
	 				
	 				if (J.ofields && J.opl != false) txt = txt+'<a id="create" >'+ _str('Crear_nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</a>';
	 				var selected = false;
	 				
	 				var elPrompt = $.prompt(txt,{
	 						loaded:function() {
						
	
								},
								width:600,
								buttons: { Guardar: true, Cancelar: false },
								submit:function(v,m){
										
										if (!v) return true;
										var fields = {};
										
										var opts = {};
										
										var ret = true;
		 								for (var idx in J.fields) {
		 									var e = $("[name="+J.fields[idx].name+"]",m);
		 									if(e.attr('escondido') == '1'){
		 										// Si el campo depende de otro, en este caso aunque el plt marque requerido, su valor no será necesaria. Lo indicamos mediente este nuevo campo de opts
		 										if(J.fields[idx].req){
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
		 										if ($("textarea.tiny",e.parents("td")).length>0) {
		 											e.parents("td").find("table:first").css("border","dashed #f00 1px");
		 										}else{
		 											e.parent().children(":input").css("border","dashed #f00 1px");
		 										}
												ret = false;
		 									} else{
		 										if ($("textarea.tiny",e.parents("td")).length>0) {
		 											e.parents("td").find("table:first").css("border","");
		 										}else{
		 											e.parent().children(":input").css("border","");
		 										}
		 										
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

					 					 		$.getJSON(u_url,opts,function(){
					 					 			
					 					 			if(action == 'edit'||action == 'delete') {
					 					 			
					 					 				ee = element.parents('li').eq(0);
					 					 			
					 					 				
					 					 				var ee = element.parent('ul').prev("a").parent("li");
					 					 				
					 					 				
					 					 				if (ee.hasClass("expanded")){
 					 					 				ee.children('a').trigger(superevent);
 					 					 				ee.children('a').trigger(superevent);
 					 					 			}else{
 					 					 				ee.children('a').trigger(superevent);
 					 					 			}
					 					 				
					 					 			}else{
					 					 			
	 					 					 			if (expanded){
	 					 					 				element.children('a').trigger(superevent);
	 					 					 				element.children('a').trigger(superevent);
	 					 					 			}else{
	 					 					 				element.children('a').trigger(superevent);
	 					 					 			}
					 					 			}
					 					 			
					 					 		});
												
											}
										}
										return ret;
										
										
								}
						});
				
				
				
					
					
					
				}
		);

		
		
	
	});	
	
	
	t.find('li.directory span input:checkbox').bind('change',function(){
	
		
		var self = $(this);
		
		self.hide();
		self.parent('label').addClass("wait");
		
		if (self.attr("name")=="write"&&self.is(':checked')){
			
			self.parents('.treeopts').find("input[name=\"read\"]").not(":checked").hide();
			self.parents('.treeopts').find("input[name=\"read\"]").not(":checked").parent('label').addClass("wait");
			
		}

		if (self.attr("name")=="read"&&!(self.is(':checked'))){
			
			self.parents('.treeopts').find("input[name=\"write\"]").hide();
			self.parents('.treeopts').find("input[name=\"write\"]").parent('label').addClass("wait");
			
		}
		
		if (self.is(':checked')){
			
			self.parents('li').children('span.treeopts').find("input[name=\"read\"]").not(":checked").attr('checked','checked').trigger('change');
			
		}
		
		var url = location.href+"&dir_action="+self.attr('id')+"_"+((self.is(':checked'))? "1":"0" )+"&ajax=true&action=vhd_file_tree_subaction&subaction=set_perm";
			
		
		$.getJSON(url, function (data) {
			window.setTimeout(function(){
				self.show();
				self.parent('label').removeClass("wait");
				
				if (self.attr("name")=="write"&&self.is(':checked')){
					
					self.parents('.treeopts').find("input[name=\"read\"]").not(":checked").show();
					self.parents('.treeopts').find("input[name=\"read\"]").not(":checked").parent('label').removeClass("wait");
					self.parents('.treeopts').find("input[name=\"read\"]").not(":checked").attr("checked","checked");
					
				}
				if (self.attr("name")=="read"&&!(self.is(':checked'))){
					
					self.parents('.treeopts').find("input[name=\"write\"]").show();
					self.parents('.treeopts').find("input[name=\"write\"]").parent('label').removeClass("wait");
					self.parents('.treeopts').find("input[name=\"write\"]").attr("checked","");
					
				}
				
			},650);
			
		} );

		
		
	});
	
}



function bind_file(t){

	t.find('li.file > a').contextMenu({
		menu: 'myMenu2'
	}, function(action, el, pos) {
		rel = $(el).attr("rel");
		fileid = $(el).attr("id");
		var element = $(el).parent('li'); 
		var expanded = element.hasClass("expanded");
		var action=action;
		var minitxurro = $.base64Decode ( txurro );
		
		var u_url = location.href+"&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&sop="+action+"&rel="+rel+"&sid="+sid+"&skipbrowser=true&file="+fileid;
		
		if (action=="download"){
		
			window.location = location.href+"&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&rel="+rel+"&file="+fileid;
			
			return false;
		}
		
		$.getJSON(location.href+"&ajax=true&action=vhd_file_tree_subaction&subaction="+action+"&rel="+rel+"&file="+fileid, 
				function(J) {
			
					
			
 					var noEditables = new Array();
 					var cont = 0;
 					var txtC = "";
 					var txt = "";

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
	 					if (action!="delete"){
	 						txtC += 'Directorio</caption>';
	 					}else{
	 						txtC += "Borrar?";
	 					}
	 					
	 				}
	 				txt = txtC+txt;
	 				var otxt = "";
	 				var tmpotxt = "";
	 				if (J.ofields){
	 	 				oPLT = J.opl;
	 	 			}
	 					
	 				
	 				if (J.ofields && J.opl != false) txt = txt+'<a id="create" >'+ _str('Crear_nuev', $("#tablonContainer table").attr("genero"),$("#tablonContainer table").attr("entidad"))+'</a>';
	 				var selected = false;
	 				
	 				var elPrompt = $.prompt(txt,{
	 						loaded:function() {
						
	
								},
								width:600,
								buttons: { Guardar: true, Cancelar: false },
								submit:function(v,m){
										
										if (!v) return true;
										var fields = {};
										
										var opts = {};
										
										var ret = true;
		 								for (var idx in J.fields) {
		 									var e = $("[name="+J.fields[idx].name+"]",m);
		 									if(e.attr('escondido') == '1'){
		 										// Si el campo depende de otro, en este caso aunque el plt marque requerido, su valor no será necesaria. Lo indicamos mediente este nuevo campo de opts
		 										if(J.fields[idx].req){
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
		 										if ($("textarea.tiny",e.parents("td")).length>0) {
		 											e.parents("td").find("table:first").css("border","dashed #f00 1px");
		 										}else{
		 											e.parent().children(":input").css("border","dashed #f00 1px");
		 										}
												ret = false;
		 									} else{
		 										if ($("textarea.tiny",e.parents("td")).length>0) {
		 											e.parents("td").find("table:first").css("border","");
		 										}else{
		 											e.parent().children(":input").css("border","");
		 										}
		 										
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

					 					 		$.getJSON(u_url,opts,function(){
					 					 			
					 					 			if(action == 'edit'||action == 'delete'||action == 'delete_file') {
					 					 			
					 					 				ee = element.parents('li').eq(0);
					 					 			
					 					 				
					 					 				var ee = element.parent('ul').prev("a").parent("li");
					 					 				
					 					 				
					 					 				
					 					 				
					 					 			if (ee.hasClass("expanded")){
 					 					 				ee.children('a').trigger(superevent);
 					 					 				ee.children('a').trigger(superevent);
 					 					 			}else{
 					 					 				ee.children('a').trigger(superevent);
 					 					 			}
					 					 				
					 					 			}else{
					 					 			
	 					 					 			if (expanded){
	 					 					 				element.children('a').trigger(superevent);
	 					 					 				element.children('a').trigger(superevent);
	 					 					 			}else{
	 					 					 				element.children('a').trigger(superevent);
	 					 					 			}
					 					 			}
					 					 			
					 					 		});
												
											}
										}
										return ret;
										
										
								}
						});
				
				
				
					
					
					
				}
		);

		
		
	
	});	
	
	
}

var superevent = "click"; 

$(document).ready(function(){
	
	
	$('#vhd_tree').fileTree(
		{	
		root: 'root/', 
		script: location.href+"&ajax=true&action=vhd_file_tree", 
		folderEvent: superevent,		 
		expandSpeed: 750, 
		collapseSpeed: 750, 
		multiFolder: true,
		callback: function(t){
						
					var t = t;
			
					bind_directory(t);
					
					bind_file(t);
					
					
				}
		
		}, 
		function(file) { 
			
			
			
		}
	);
	
	
});
