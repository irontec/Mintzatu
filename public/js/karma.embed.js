$(document).ready(function(){
	var i = 1;
	$(document).find('img.slideshare').each(function(){
		$(this).parent().attr('id','slide'+i);
		var url = $(this).attr('id');
		var id = $(this).parent().attr('id');
		swfobject.embedSWF(url,id,"500", "355", "9.0.0");
		i++;
	});
	$(document).find('a.youtube').each(function(){
		var url = $(this).attr('href');
		var id = $(this).attr('id');
		swfobject.embedSWF(url,id, "550", "335", "9.0.0");
	});
});

var scrollSpeed = 70; 		// Speed in milliseconds
var step = 1; 				// How many pixels to move per step
var current = 0;			// The current pixel row
var imageHeight = 270;		// Background image height
var headerHeight = 80;		// How tall the header is.
var restartPosition = -(imageHeight - headerHeight);
function scrollBg(){
	//Go to next pixel row.
	current -= step;
	
	if (current == restartPosition){
		clearInterval(init);
	}
	//Set the CSS of the header.
	$('.headImg1, .headImg2, .headImg3, .headImg4').css("background-position","0px "+current+"px");
}

var init = setInterval("scrollBg()", scrollSpeed);