
 
$(document).ready(function(){

	$(".tablon_multiedit").each(function(){
		var _self = $(this);
		_self.bind('click',function(){
			var lngth = $('tr:not(.tablonClone) td.multiselect input:checked').length;
	 		if (lngth == 0) {
	 			$.prompt("No hay filas seleccionadas.");
	 			return false;
	 		}
	 		var str = "";
	 		f=false;
	 		for(i=0;i<lngth;i++){
	 			if (f==false) {
	 				firstid = $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parent("td").attr("id").replace(/ms::/,"");
	 				f=true;
	 			}
	 			str += (((i!=0)? ',':'')+ $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parent("td").attr("id").replace(/ms::/,""));
	 		}	
	 		//document.title = _self.attr("href").replace(/%id%/,str);
			campo = _self.attr('campo');
			title = _self.attr('title');
			plt = _self.attr('plt');
			url = "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=load"+"&id="+plt+'::'+campo+'::'+firstid;
			texto = _self.html();
			$.ajax({
			         type : "select",
			         url  : url,
			         data : "",
			         async : false,
			         dataType: "json",
			         success: function(json) {
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
	                    	{loaded:function() {
	                    			$('#msel').append(select)
	                    				},
	                    	width:600,
	                    	buttons: { Guardar: true, Cancelar: false },
	                    	submit:function(v,m){
	                    			if (!v) return true;
	                    			var valor = $('#tmselect_'+campo).val();
	                    			var txt = '¿Desea realizar "'+title+'" de la'+((lngth>1)? 's':'')+' siguiente'+((lngth>1)? 's':'')+' fila'+((lngth>1)? 's':'')+'?<br /><ul>';
							 		for(i=0;i<lngth;i++) {
										txt += '<li>'+$('td.multiselect input:checked:eq('+i+')').parents("tr").children("td:eq(1)").text()+'</li>';
							 		}
							 		txt += '</ul>';
										$.prompt(
					                    	txt,
					                    	{width:600,
					                    	buttons: { si: true, no: false },
					                    	submit:function(v,m){
					                    			if (!v) return true;
				                 				 	for(i=0;i<lngth;i++){
									 					id = $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parent("td").attr("id").replace(/ms::/,"");
									 					url = "./modules/tablon/ajax/ops_tablon.php?op=jeditable&acc=save"+"&value="+valor+"&id="+plt+'::'+campo+'::'+id;
														$.ajax({ type : "select",
													         url  : url,
													         data : "",
													         async : false,
													         dataType: "json",
													         success: function(data) {
													         	$("[id='"+plt+'::'+campo+'::'+id+"']").html(json[valor]);
													         }
													         });
													}
				
													return true;
					                    		}
					                    	}
					                    );

									return true;
	                    		}
	                    	}
	                    );
	
	                    	

	                    
	                    
			         }
			});
			
			
			return false;
		});
	
	});

});
