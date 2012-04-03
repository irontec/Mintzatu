/*
 * WYMeditor : what you see is What You Mean web-based editor
 * Copyright (c) 2005 - 2009 Jean-Francois Hovinne, http://www.wymeditor.org/
 * Dual licensed under the MIT (MIT-license.txt)
 * and GPL (GPL-license.txt) licenses.
 *
 * For further information visit:
 *        http://www.wymeditor.org/
 *
 * File Name:
 *        jquery.wymeditor.fullscreen.js
 *        Fullscreen plugin for WYMeditor
 *
 * File Authors:
 *        Luis Santos (luis.santos a-t openquest dotpt)
 */

//Extend WYMeditor

WYMeditor.editor.prototype.karma_slideshare = function(oo,inst) {
 
  var wym = this;
  var o = oo;

 //construct the button's html
  var html = "<li class='wym_tools_karma_slideshare'>"
         + "<a class="+ inst +" name='Slideshare' href='#'>"
         + "Slideshare"
         + "</a></li>";

  //add the button to the tools box
  jQuery(wym._box)
    .find(wym._options.toolsSelector + wym._options.toolsListSelector)
    .append(html);

  //handle click event
  jQuery(wym._box).find('li.wym_tools_karma_slideshare a.'+inst).click(function() {
  		var cargando = '<img src="cargando.gif" />';
  		/*$.prompt(cargando, {
  			loaded: function(){
  				$("#jqicontainer").width('0px'); 
  				$("#jqicontainer").height('0px'); 
  				$("#jqicontainer img").css('position','absolute');
  				},
  			overlayspeed: 1,
  			promptspeed: 1
  		});*/
		var secret = o.secret;
		var api = o.api;
		var username = o.username;
		var limit = parseInt(o.limit);
		var index = 0;
		var currentPage = 1;
		var o_wym = wym;
		var phpFile = "modules/tablon/scripts/wymeditor/plugins/karma/slideshare.php";
		var slidePhp = "modules/tablon/scripts/wymeditor/plugins/karma/slideshare.php";
		var phpSearch = "modules/tablon/scripts/wymeditor/plugins/karma/slideSearch.php";
		
		$.getJSON(phpFile +"?secret="+ secret +"&api_key="+ api +"&username="+ username +"&limit="+ limit ,function(data)
		{
			var count = data.Count;
			var slides = '<table id="slideshare"><caption>SlideShares de "'+ data.Name +'"</caption><tbody>';
			var html = '';
			for( var i in data.Slideshow)
			{
				html = data.Slideshow[i].Embed.substr(data.Slideshow[i].Embed.indexOf("value=")+6);
				html = html.substr(html, html.indexOf(" />"));
				slides += '<tr><td style="width:10px;"><input name="'+ data.Slideshow[i].URL +'" type="checkbox" id='+ html +' value='+ data.Slideshow[i].ThumbnailURL +' class="slideshare" /></td>';
				slides += '<td><img src="'+ data.Slideshow[i].ThumbnailSmallURL +'" /></td>';
				slides += '<td style="width:350px;">'+ data.Slideshow[i].Title +'</td></tr>';				
			}
			slides += '</table>';
			slides += '<img src="icons/arrow_left.png" id="anterior" class="pagGalery" />';
			slides += '<img src="icons/arrow_right.png" id="siguiente" class="pagGalery" />';
			
			var slideSearch = '<table id="slideSearch"><caption>Búsqueda en SlideShare</caption><tbody><tr><td>';
			slideSearch += '<input type="text" id="busqueda" value="" />';
			slideSearch += '<input type="submit" id="buscar" value="Buscar" style="width:18%;" /></td></tr>';
			slideSearch += '</table>';
			slideSearch += '<img src="icons/arrow_left.png" id="Anterior" class="pagGalery" />';
			slideSearch += '<img src="icons/arrow_right.png" id="Siguiente" class="pagGalery" />';			
			
			var dialog = {
				state0: 
				{
					html: slides,
					buttons: { Añadir: true, Cancelar: false, Buscar: 'buscar' },
					focus: 1,
					
					submit:function(v,m,f)
					{ 
						if(v==false)
						{
							return true;
						}
						else if(v==true)
						{
							return true;
						}
						else if(v=='buscar')
						{							
							$.prompt.goToState('state1');//go forward
						}
					}
				},
				state1: 
				{
					html: slideSearch,
					buttons: { Volver: 'volver', Cancelar: false, Añadir: true },
					submit:function(v,m,f)
					{
						if(v==false)
							return true;
						else if(v==true)
							return true;//go forward
						else if(v=='volver')
							$.prompt.goToState('state0');//go back
						return false; 
					}
				}
			}
			/*$.prompt.close();*/
			$.prompt(dialog,{
				overlayspeed: 'fast',
				buttons: {
						Ok: true,
						Cancel: false
				},
				loaded: function(){
					$(this).css('top','0');
					$("#anterior").hide();
					$("#Anterior").hide();
					$("#Siguiente").hide();
					$("#siguiente").show();
					if(count < limit)
					{
						$("#siguiente").hide();
					}
					$("#siguiente").click(function(){
						index += limit;
						$.getJSON(slidePhp +"?secret="+ secret +"&api_key="+ api +"&username="+ username +"&limit="+ limit +"&offset="+ index, function(data)
						{
							$.fn.Next(data);
						});
					});
					$("#anterior").click(function(){
						index -= limit;
						$.getJSON(slidePhp +"?secret="+ secret +"&api_key="+ api +"&username="+ username +"&limit="+ limit +"&offset="+ index, function(data)
						{
							$.fn.Next(data);
						});
					});
					$.fn.Next = function(data){
						var count = data.Count;						
						var html = '';
						var slides = '';
						for( var i in data.Slideshow)
						{
							html = data.Slideshow[i].Embed.substr(data.Slideshow[i].Embed.indexOf("value=")+6);
							html = html.substr(html, html.indexOf(" />"));
							slides += '<tr><td style="width:10px;"><input name="'+ data.Slideshow[i].URL +'" type="checkbox" id='+ html +' value='+ data.Slideshow[i].ThumbnailURL +' class="slideshare" /></td>';
							slides += '<td><img src="'+ data.Slideshow[i].ThumbnailSmallURL +'" /></td>';
							slides += '<td style="width:350px;">'+ data.Slideshow[i].Title +'</td></tr>';				
						}
						var numElems = ($("#slideshare tr").length)-1;
						$("#slideshare tr").each(function(i) {
							$(this).slideUp(function(){$(this).remove();});
							if (i == numElems) {
								$(slides).hide().appendTo($("#slideshare tbody")).slideDown();
							} 
							
						});
						if(index + limit >= count)
						{
							$("#siguiente").hide();
						}
						else
						{
							$("#siguiente").show();
						}
						
						if(index >= limit)
						{
							$("#anterior").show();
						}
						else
						{
							$("#anterior").hide();
						}
					};
					
					$.fn.Search = function(data){
						var count = data.Meta.TotalResults;						
						var html = '';
						var slides = '';
						for( var i in data.Slideshow)
						{
							html = data.Slideshow[i].Embed.substr(data.Slideshow[i].Embed.indexOf("value=")+6);
							html = html.substr(html, html.indexOf(" />"));
							slides += '<tr class="borrar"><td style="width:10px;"><input name="'+ data.Slideshow[i].URL +'" type="checkbox" id='+ html +' value='+ data.Slideshow[i].ThumbnailURL +' class="slideshare" /></td>';
							slides += '<td><img src="'+ data.Slideshow[i].ThumbnailSmallURL +'" /></td>';
							slides += '<td style="width:350px;">'+ data.Slideshow[i].Title +'</td></tr>';				
						}
						var numElems = ($("#slideSearch tr.borrar").length)-1;
						if(numElems == -1){
							$(slides).hide().appendTo($("#slideSearch tbody")).slideDown();
						}else{
							$("#slideSearch tr.borrar").each(function(i) {
								$(this).slideUp(function(){$(this).remove();});
								if (i == numElems) {
									$(slides).hide().appendTo($("#slideSearch tbody")).slideDown();
								}
							});
						}
						if( currentPage * limit >= count )
						{
							$("#Siguiente").hide();
						}
						else
						{
							$("#Siguiente").show();
						}
						
						if(currentPage * limit > limit)
						{
							$("#Anterior").show();
						}
						else
						{
							$("#Anterior").hide();
						}
					};
					$("#buscar:input").click(function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						currentPage = 1;
						$.getJSON(phpSearch +"?q="+ keys +"&secret="+ secret +"&api_key="+ api +"&items_per_page="+ limit +"&page="+ currentPage , function(data)
						{
							if( data.Slideshow )
							{
								$.fn.Search(data);
							}
							else
							{
								alert('No hay resultados para la busqueda especificada');
							}
						});
					});
					$("#Siguiente").click(function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						currentPage ++;
						$.getJSON(phpSearch +"?q="+ keys +"&secret="+ secret +"&api_key="+ api +"&items_per_page="+ limit +"&page="+ currentPage , function(data)
						{
							$.fn.Search(data);
						});
					});
					$("#Anterior").click(function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						currentPage --;
						$.getJSON(phpSearch +"?q="+ keys +"&secret="+ secret +"&api_key="+ api +"&items_per_page="+ limit +"&page="+ currentPage , function(data)
						{
							$.fn.Search(data);
						});
					});
				},
				callback:function(v,m){

               if(v==false) {
						//$.prompt.close();// No guardar
					} else {
						var slides ='';
					$(':checkbox:checked',m).each(function(i){
						slides += '<a href="'+ $(this).attr('name') +'"><img src="'+ $(this).val() +'" class="slideshare" id="'+$(this).attr('id')+'" style="border: 60px solid gray;" /></a>';   				
      			});
					if(slides != ''){     				
	    				o_wym.insert(slides);
    				}
					//$.prompt.close();
						}
					}
				}	
			);			
		
		});

		
		
		
    return;
  });
};