$(document).ready(function(){
	
	
	
	$('.thumb').find('img').bind('click',function(){
		trval = $(this).parents('.thumb').attr('trval');
		$('body').scrollTo( $('[id='+trval+']') , 300, {offset:-50});
		$('[id='+trval+']').addClass('tmpOver');
		window.setTimeout(function(){
			$('.tmpOver').removeClass('tmpOver');
		},2500);
		
		
		
	});
	
	
	
	
	
	
	
	
	
	
});