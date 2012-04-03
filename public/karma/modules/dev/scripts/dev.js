$(document).ready(function(){


var table = "";
$("#tableSelect").bind('change',function(){
	table = $(this).val();
	cargarTabla();
});

var cargarTabla = function(){

	var opts = {op:'cargarTabla',val:table};
	$.getJSON("./modules/dev/ajax/dev.php",opts,function(j) {
		if(!j.error){
			$("#detalles").html(j.ret);
		
		}
	
	});
}
	



});