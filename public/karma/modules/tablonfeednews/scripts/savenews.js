$(document).ready(function(){


	

	var values = {};
	
	var loadValues = function(t){
		values['ajax'] = "ajax";
		values['load'] = t.attr('href');
		idF = t.parent('td').attr('id');
		values['title'] = $('#title_'+idF).children('a').html();
		values['link'] = $('#title_'+idF).children('a').attr('href');
		values['pubdate'] = $('#pubdate_'+idF).html();
		values['description'] = $('#description_'+idF).html();
		values['key'] = idF;
		values['ref'] = t.attr('ref');
	};

	var formSubmit = function(t){
		loadValues(t);
		
		$.getJSON("modules/tablonfeednews/ajax/ajaxFeed.php",values,function(data){
			if(data.error){
				alert(data.error);
			
			}
				res = data.key;
				
				$("#title_"+res).html("<strike>"+$("#title_"+res).html()+"</strike>");
				return false;
			
		});	
	};
	
	


	$('.feedButtom').bind('click',function(){
	
		formSubmit($(this));
		return false;
	});
	
	
});