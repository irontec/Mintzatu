
 
$(document).ready(function(){

	$(".tablon_multiedit").each(function(){
		var _self = $(this);
		_self.bind('click',function(e){
			e.preventDefault();
			e.stopPropagation();
			var TRs =  $('#tablon tr:not(.tablonClone) td.multiselect input:checked');
			var lngth = TRs.length;
	 		if (lngth == 0) {
	 			$.prompt("No hay filas seleccionadas.");
	 			return false;
	 		}
	 		firstid = $("td.multiselect:eq(0)",$(TRs[0]).parents("tr")).attr("id").replace(/ms::/,"");
	 		
	 		var campo = _self.attr('campo');
			var title = _self.attr('title');
			var plt = _self.attr('plt');
			var removeAfterUpdate = _self.attr('removeafterupdate');
			
			url = "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"+"&id="+plt+'::'+campo+'::'+firstid;

			texto = _self.html();
			$.ajax({
			         type : "select",
			         url  : url,
			         data : "",
			         async : false,
						dataType: "json",
						success: function(json) {
							// El dia que no sea una select, habrá risas...
							var select = $('<select>');
							select.attr('id','tmselect_'+campo);
							//_self.parent('li').append(select);
							for (var key in json) {
								if ('selected' == key) {
									continue;
								} 
								var option = $('<option>').val(key).append(json[key]);
								select.append(option); 	 
							}
	                    
							$.prompt(
	                    	"<table><tr><td>"+texto+"</td><td  id=\"msel\"></td></tr></table>",
	                    	{
	                    	loaded:function() {
										$('#msel').append(select)
								},
	                    	width:600,
	                    	buttons: { Guardar: true, Cancelar: false },
	                    	submit:function(v,m){
	                    			if (!v) return true;
	                    			var valor = $('#tmselect_'+campo).val();
						var showValor = $('#tmselect_'+campo+' option[value='+valor+']').text();
	                    			var txt = '¿Desea realizar "'+title+'" ('+showValor+') de la'+((lngth>1)? 's':'')+' siguiente'+((lngth>1)? 's':'')+' fila'+((lngth>1)? 's':'')+'?<br /><ul>';
										var contAux = contExtra = 0; // Solo mostramos las primeras 8 filas.
							 			for(var i=0;i<lngth;i++) {
											contAux++;
											if (contAux<=8) {
											    var tdContenido = $("td:not(.numline,.multiselect)",$(TRs[i]).parents("tr"));
											    txt += '<li>';
											    var cont = 0;
											    for(var ii=0;ii<tdContenido.length;ii++) {
											         var t = $(tdContenido[ii]).text();
											         if (t!= '') {
											                 cont++;
											                 txt += t;
											                 if (cont == 2) break; 
											                 txt += ' / ';
											         }
											    }
											    txt += '</li>';
											} else contExtra++;
							 			}
										if (contExtra > 0) txt += '<li>(y otras '+contExtra+' filas más)</li>';
							 			txt += '</ul>';
										$.prompt(
					                    	txt,
					                    	{width:600,
					                    	buttons: { si: true, no: false },
					                    	submit:function(v,m){
					      	              			if (!v) return true;
					   	                 			var cont = lngth;
						                    			var url = "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=save"+"&value="+valor+"&patron="+plt+'::'+campo+'::';
					                 				 	for(var i=0;i<lngth;i++){
										 						url += '&id[]=' + $("td.multiselect:eq(0)",$(TRs[i]).parents("tr")).attr("id").replace(/ms::/,"");
										 					}
																	
																$.ajax( {
																		type : "select",
												         			url  : url,
												         			data : "",
												         			async : false,
												         			dataType: "json",
												         			success: function(data) {
												 										for (var i in data.changedIDs) {
													         							if (removeAfterUpdate=="true") {
													         									$("[id='"+plt+'::'+data.changedIDs[i]+"']").remove(); // EL TR DIRECTAMENTE
													         							} else {
													         									if (typeof (data.valor) == "array") {
													         											var _v = data.valor[data.changedIDs[i]];
													         									} else {
													         											var _v = data.valor
																									}
																									$("[id='"+plt+'::'+campo+'::'+id+"']").html(json[_v]);
																							}
																						}
																						if (data.error) {
																							$.prompt("Error Code ["+data.error+"]<br />"+data.errorStr,{buttons: {Ok: true},submit: function(v,m){return true;}});
																						} else {
																							$.prompt(data.changedIDs.length +" registros actualizados.");
																						}
																					return true;
																		}
																}); // AJAX
												return true;
												} // SUBMIT
												
										}); // PROMPT
								return true;								
								} // SUBMIT
							}); // PROMPT
						} // SUCCESS
					}); //AJAX
				return false;
		}); // bind click
	}); // each 
	
	
	$(".tablon_multiedit").parents('li').bind('click',function(){ $(this).find(".tablon_multiedit").trigger('click'); });

}); // document ready
