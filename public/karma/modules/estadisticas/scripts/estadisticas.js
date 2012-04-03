$(document).ready(function(){
	$('object').hide("fast");


$('#mostrardst').bind("click", function (){
	$('#dst').show("fast");
	$('#queue').hide("fast");
	
		});

$('#mostrarqueue').bind("click", function (){
	$('#dst').hide("fast");
	$('#queue').show("fast");
	
		});
	
	});