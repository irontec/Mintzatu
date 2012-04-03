/*
 * Plugin de Youtube para poder añadir videos
 *  
 */

//Extend WYMeditor
WYMeditor.editor.prototype.karma_youtube = function(oo,inst) {
  var wym = this;
 //construct the button's html
  var html = "<li class='wym_tools_karma_youtube'>"
         + "<a class="+ inst  +" name='Video de Youtube' href='#'>"
         + "Video de Youtube"
         + "</a></li>";
  var o = oo;
		
  //add the button to the tools box
  jQuery(wym._box)
    .find(wym._options.toolsSelector + wym._options.toolsListSelector)
    .append(html);
    		
  //handle click event
  jQuery(wym._box).find('li.wym_tools_karma_youtube a.'+inst).click(function() 
  {
		var cargando = '<img src="cargando.gif" />';
  		/*$.prompt(cargando, {
  			loaded: function(){
  				$("#jqicontainer").width('1px'); 
  				$("#jqicontainer").height('1px'); 
  				$("#jqicontainer img").css('position','absolute');
  				},
  			
  			overlayspeed: 1,
  			promptspeed: 1
  		});*/
		var conf='';		
		conf += '&start-index='+o['start-index']+'&max-results='+o['max-results'];
		var next = 1;
		var start = o['start-index'];
		var max = o['max-results'];
		var userName = o.user;
    	var o_wym = wym;
    	$.getScript("http://gdata.youtube.com/feeds/base/users/" + userName + "/uploads?alt=json-in-script&callback=$.karma_youtube_callback"+conf);
   	

			
   	$.karma_youtube_callback = function(data)
   	{
			var html = ''; 
			var videos = '<table id="youtube"><caption>Videos del canal de "'+ userName +'"</caption>';
			var total = data.feed.openSearch$totalResults.$t;
			var totalPages = parseInt(total) / parseInt(max);
			for(var i in data.feed.entry)
			{	
				html = data.feed.entry[i].link[0].href.substr(data.feed.entry[i].link[0].href.indexOf('=')+1);
				html = html.substr( html , html.indexOf('&'));
				videos += '<tr><td style="width:20px"><input name="youtube" type="checkbox" id='+ html +' value='+ html +' class="youtube" /></td>';
				videos += '<td><img src="http://i.ytimg.com/vi/'+ html +'/2.jpg" /></td>';
				videos += '<td style="width:350px;">'+data.feed.entry[i].title.$t+'</td></tr>';
			}
			
			videos += '</table>';
			videos += '<img src="icons/arrow_left.png" id="anterior" class="pagGalery" />';
			videos += '<img src="icons/arrow_right.png" id="siguiente" class="pagGalery" />';
			videos += '<span id="page" style="float:right">Página <select id="pages"></select></span>';

			var busqueda = '<table id="youtube_bus"><caption>Búsqueda en Youtube</caption><tr><td>';
			busqueda += '<input type="text" id="busqueda" value="" />';
			busqueda += '<input type="submit" id="buscar" value="Buscar" style="width:18%;" /></td></tr>';
			busqueda += '</table>';
			busqueda += '<img src="icons/arrow_left.png" id="prev" class="pagGalery" />';
			busqueda += '<img src="icons/arrow_right.png" id="next" class="pagGalery" />';
			busqueda += '<span id="pageSearch" style="float:right">Página <select id="pagebus"></select></span>';

			var currentBus = 1;
			
			var dialog = {
				state0: 
				{
					html: videos,
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
					html: busqueda,
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

			
			$.prompt(dialog,
			{
				
				overlayspeed: 'fast',
				loaded:function(){
					$(this).css('top','0');
					var toti = Math.ceil(totalPages);		
					for (var i=1 ; i<= toti; i++)
					{
						$("#pages").append('<option value="'+ i +'">'+ i +'</option>');
					}
					$("#pageSearch").hide();
					$("#prev").hide();
					$("#next").hide();				
					$("#anterior").hide();
					if( total <= max ) {
						$("#siguiente").hide();
					}
					$.karma_youtube_callback_two = function(data)
					{
						var videos = '';
						if(!data.feed.entry){
							$("#siguiente").hide();
							exit();
						}
						for(var i in data.feed.entry)
						{	
							html = data.feed.entry[i].link[0].href.substr(data.feed.entry[i].link[0].href.indexOf('=')+1);
							html = html.substr( html , html.indexOf('&'));
							videos += '<tr><td><input name="youtube" type="checkbox" id='+ html +' value='+ html +' class="youtube" /></td>';
							videos += '<td><img src="http://i.ytimg.com/vi/'+ html +'/2.jpg" /></td>';
							videos += '<td style="width:350px;">'+data.feed.entry[i].title.$t+'</td></tr>';
						}
						var numElems = ($("#youtube tr").length)-1;
						$("#youtube tr").each(function(i) {
							$(this).slideUp(function(){$(this).remove();});
							if (i == numElems) {
								$(videos).hide().appendTo($("#youtube")).slideDown();
							} 
							
						});
						var paginaActual = parseInt(next);
						paginaActual += parseInt(max);
						paginaActual -= 1;
						paginaActual = paginaActual / parseInt(max);
						$('select[id=pages]').val(paginaActual);
						var suma = 0;
						suma += parseInt(next);
						suma += parseInt(max);
						if((suma) < total ){
							$("#siguiente").show();
						}else{
							$("#siguiente").hide();
						}
						if( next > max){
							$("#anterior").show();
						}else{
							$("#anterior").hide();
						}
					}
					$.karma_youtube_search = function(data)
					{
						var videos = '';
						var totalBus = data.feed.openSearch$totalResults.$t;
						if(totalBus > 0)
						{
							if(totalBus > 1000)
							{
								var pageBusq = 1000 / parseInt(max);
							}
							else
							{
								var pageBusq = parseInt(totalBus) / parseInt(max);
							}
							pageBusq = Math.ceil(pageBusq);
							$('select[id=pagebus]').find('option').remove();
							for (var i=1 ; i<= pageBusq; i++)
							{
								$("#pagebus").append('<option value="'+ i +'">'+ i +'</option>');
							}
						}
						else
						{
							alert("No hay resultados, prueba de nuevo");
							return;
						}
						for(var i in data.feed.entry)
						{	
							html = data.feed.entry[i].link[0].href.substr(data.feed.entry[i].link[0].href.indexOf('=')+1);
							html = html.substr( html , html.indexOf('&'));
							videos += '<tr class="borrar"><td><input name="youtube" type="checkbox" id='+ html +' value='+ html +' class="youtube" /></td>';
							videos += '<td><img src="http://i.ytimg.com/vi/'+ html +'/2.jpg" /></td>';
							videos += '<td style="width:350px;">'+data.feed.entry[i].title.$t+'</td></tr>';
						}
						var numElems = ($("#youtube_bus tr.borrar").length)-1;
						if(numElems == -1){
							$(videos).hide().appendTo($("#youtube_bus")).slideDown();
						}else{								
							$("#youtube_bus tr.borrar").each(function(i) {
								$(this).slideUp(function(){$(this).remove();});
								if (i == numElems) {
									$(videos).hide().appendTo($("#youtube_bus")).slideDown();
								} 
							});
						}
						$("#pageSearch").show();
						var busActual = parseInt(currentBus);
						busActual += parseInt(max);
						busActual -= 1;
						busActual = busActual / parseInt(max);
						$('select[id=pagebus]').val(busActual);
						if((parseInt(currentBus) + parseInt(max)) < totalBus && (parseInt(currentBus) + parseInt(max)) < 1000 ){
							$("#next").show();
						}else{
							$("#next").hide();
						}
						
						if( currentBus > max){
							$("#prev").show();
						}else{
							$("#prev").hide();
						}
					}
					$("#pages").live('change',function(){
						var paginaActual= $(this).val();
						paginaActual -= 1;					
						next = (parseInt(paginaActual) * parseInt(max));
						next += 1;
						$.getScript("http://gdata.youtube.com/feeds/base/users/" + userName + "/uploads?alt=json-in-script&callback=$.karma_youtube_callback_two&max-results="+max+"&start-index="+next);
					});
					$("#pagebus").find('option').live('click',function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						var busqActual= $(this).val();
						busqActual -= 1;					
						currentBus = (parseInt(busqActual) * parseInt(max));
						currentBus += 1;
						$.getScript("http://gdata.youtube.com/feeds/api/videos?alt=json-in-script&vq="+keys+"&orderby=published&start-index="+currentBus+"&max-results="+max+"&callback=$.karma_youtube_search");
					});
					$("#siguiente").click(function(){
						next += parseInt(max);
						$.getScript("http://gdata.youtube.com/feeds/base/users/" + userName + "/uploads?alt=json-in-script&callback=$.karma_youtube_callback_two&max-results="+max+"&start-index="+next);
					});
					$("#anterior").click(function(){						
						next -= parseInt(max);
						$.getScript("http://gdata.youtube.com/feeds/base/users/" + userName + "/uploads?alt=json-in-script&callback=$.karma_youtube_callback_two&max-results="+max+"&start-index="+next);	
					});
					$("#buscar:input").click(function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						currentBus = 1;
						$.getScript("http://gdata.youtube.com/feeds/api/videos?alt=json-in-script&vq="+keys+"&orderby=published&start-index=1&max-results="+max+"&callback=$.karma_youtube_search");
					});
					$("#next").click(function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						currentBus += parseInt(max);
						$.getScript("http://gdata.youtube.com/feeds/api/videos?alt=json-in-script&vq="+keys+"&orderby=published&start-index="+currentBus+"&max-results="+max+"&callback=$.karma_youtube_search");
					});
					$("#prev").click(function(){
						var keys = $("#busqueda").val().replace(/ /g,'+');
						currentBus -= parseInt(max);
						$.getScript("http://gdata.youtube.com/feeds/api/videos?alt=json-in-script&vq="+keys+"&orderby=published&start-index="+currentBus+"&max-results="+max+"&callback=$.karma_youtube_search");
					});
									
				}, 
				callback:function(v,m)
				{
	             if(v==false) 
	             {
						/*$.prompt.close();*/
					 } 
					 else 
					 {
						var video='';
						$(':checkbox:checked',m).each(function(i){
							video += '<a class="youtube" href="http://www.youtube.com/v/'+ $(this).val() +'" id="'+ $(this).val() +'"><img src="http://i.ytimg.com/vi/'+$(this).val()+'/0.jpg" class="youtube" /></a>';   				
						});
						if(video != ''){     				
		    				o_wym.insert(video);
						    o_wym.update();
	    				}
						/*$.prompt.close();*/
							
					 }
				 }
	
			});

		}


    return;
  });
};
