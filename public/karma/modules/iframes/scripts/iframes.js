$(document).ready(function(){
	var altIframe = $("#altIframe").val();
	if(altIframe.indexOf("%")!= -1){
		altIframe = parseInt(altIframe.replace("%",""));
		//$("iframe.iframe").css('height',$("body:first").height()*altIframe/100);
		var altura = ($(document).height()-($("#pie").height()-$("#pieMainInner").height()-$("h1:first").height())-240)*altIframe/100;
		$("iframe.iframe").css('height',altura);
	}	
});