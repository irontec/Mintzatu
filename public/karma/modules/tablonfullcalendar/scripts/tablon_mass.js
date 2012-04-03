 /**
 * Fichero javascript para la clase tablon_edit,
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

var miniLoader = new Image();
miniLoader.src = "./icons/miniloader.gif";

 
$(function(){
 var  totales =0;
 var  sels =0;
 	$('td.multiselect input').each(function(i) {
 			if($(this).attr("checked")==true) sels++; 
 			totales ++;
 	});
 	if(totales==sels)  $("#chk_msMASTERMASS").attr("checked","checked");
 	
	$('td.multiselect input').bind('change',function(){
 		var acc = ($(this).attr("checked")? "insert":"delete");
		var inchk = $(this);
		inchk.fadeOut("slow",function() {
			inchk.after("<img src=\""+miniLoader.src+"\" />");
			var opts = {op:'tablon_mass',acc:acc,value:inchk.parent('td').attr('id')};
 	    	$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
    			if (!j.ret) {
    				$.prompt('error',{buttons: {Ok: true}});
				} else {
    				inchk.parent('td').attr('id',j.ret);
				}
				inchk.next("img").fadeOut("slow",function(){
					$(this).remove();
					inchk.fadeIn("slow");
				});	
    		});  
		});
 	});

 	$("#chk_msMASTERMASS").bind("change",function() {
 		if ($(this).attr("checked")==true) {
 			$.prompt("¿Desea seleccionar todas las filas?",{ buttons: { Ok: true, Cancel: false },callback:function(v,m){
 				if (!v) {
 					$("#chk_msMASTERMASS").attr("checked","")
 					return;
 				}
				$('td.multiselect input:not(:checked)').each(function() {
					$(this).attr("checked",true).trigger("change");
				});
			}});

 		} else {
 		 	$.prompt("¿Desea deseleccionar todas las filas?",{ buttons: { Ok: true, Cancel: false },callback:function(v,m){
 				if (!v) {
	 				$("#chk_msMASTERMASS").attr("checked","checked")
 					return;
 				}
 				
				$('td.multiselect input:checked').each(function() {
						$(this).attr("checked",'').trigger("change");
				});
			}});
 		}
 	});
 	
});