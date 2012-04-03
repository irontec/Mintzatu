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
WYMeditor.editor.prototype.karma_flickr = function(oo, inst) {
  var wym = this;
  var o = oo;

 //construct the button's html
  var html = "<li class='wym_tools_karma_flickr'>"
         + "<a class="+ inst +" title='Flickr' name='Flickr' href='#'>"
         + "Flickr"
         + "</a></li>";

  //add the button to the tools box
  jQuery(wym._box)
    .find(wym._options.toolsSelector + wym._options.toolsListSelector)
    .append(html);

  //handle click event
    jQuery(wym._box).find('li.wym_tools_karma_flickr a.'+inst).click(function() {
    	var cargando = '<img src="cargando.gif" />';
  		/*$.prompt(cargando, {
  			loaded: function(){
  				$("#jqicontainer").width('1px'); 
  				$("#jqicontainer").height('1px'); 
  				$("#jqicontainer img").css('position','absolute');
  				},
  			
  			overlayspeed: 1
  		});*/
      var user = o.user;
    	var api = o['api_key'];
    	var conf = '';
		conf += '&page='+o['page']+'&per_page='+o['per_page'];
		var count = o['per_page'];
    	var o_wym = wym;
		var userPhoto = "http://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos";
		var searchPhoto = "http://api.flickr.com/services/rest/?method=flickr.photos.search";
		var getSizes = "http://api.flickr.com/services/rest/?method=flickr.photos.getSizes";
		
		$.getScript(userPhoto +"&user_id="+ user +"&api_key="+ api +"&format=json&jsoncallback=karma_flickr_callback"+ conf );
		karma_flickr_callback = function(data)
		{
			if(data.stat == 'fail'){
				alert(data.message);
				return false;
			}
			var fotos = '<table id="flickr"><caption>Fotos de Flickr</caption><tbody>';
			var total = data.photos.pages;
			var current = data.photos.page;
			var currentPage = 1;
			for(var i in data.photos.photo)
			{
				var html = 'http://farm'+ data.photos.photo[i].farm +'.static.flickr.com/'+ data.photos.photo[i].server +'/'+ data.photos.photo[i].id +'_'+ data.photos.photo[i].secret;
				//fotos += '<tr><td><input name="flickr" type="checkbox" id="'+ data.photos.photo[i].id +'" value='+ html +' class="flickr" /></td>';
				fotos += '<tr id="'+ data.photos.photo[i].id +'"><td><a href="#" id="'+ data.photos.photo[i].id +'"><img src="'+ html +'_s.jpg" /></a></td>';
				fotos += '<td style="width:300px;">'+ data.photos.photo[i].title +'</td></tr>';
			}
			fotos += '</tbody></table>';
			fotos += '<img src="icons/arrow_left.png" id="anterior" class="pagGalery" />';
			fotos += '<img src="icons/arrow_right.png" id="siguiente" class="pagGalery" />';
			fotos += '<img src="icons/arrow_left.png" id="atras" style="cursor:pointer" />';
			fotos += '<span id="page" style="float:right">Página <select id="pages"></select></span>';
			
			var busqueda = '<table id="flickr_bus"><caption>Búsqueda en Flickr</caption><tbody><tr><td>';
			busqueda += '<input type="text" id="buscar" value="" />';
			busqueda += '<input type="submit" id="busqueda" value="Buscar" style="width:18%;" /></td></tr>';
			busqueda += '</table>';
			busqueda += '<img src="icons/arrow_left.png" id="prev" class="pagGalery" />';
			busqueda += '<img src="icons/arrow_right.png" id="next" class="pagGalery" />';
			busqueda += '<img src="icons/arrow_left.png" id="volver" class="pagGalery" />';
			busqueda += '<span id="pageBus" style="float:right">Página <select id="pagebus"></select></span>';
			
			var dialog = {
				state0: 
				{
					html: fotos,
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
							//$("#youtube_bus tr").remove();
							//$("#busqueda").val('');
							//$("#next").hide();
							//$("#prev").hide();
							$.prompt.goToState('state0');//go back
						return false; 
					}
				}
			}			
			
			//$.prompt.close();
	    	$.prompt(dialog,{
	    		overlayspeed: 'fast',
	    		buttons: 
	    		{
	    			Añadir: true,
	    			Cancelar: false
	    		},
	    		loaded:function()
	    		{
	    			$(this).css('top','0');
					$("#next").hide();
					$("#prev").hide();
					$("#pageBus").hide();
					$("#page").hide();	    			
	    			$("#siguiente").hide();
	    			$("#anterior").hide();
	    			$("#atras").hide();
	    			$("#volver").hide();
	    			if(current < total){
	    				$("#siguiente").show();
	    				$("#page").show();
	    			}
					for(i=1; i<= total; i++)
					{
						$("#pages").append('<option value="'+ i +'">'+ i +'</option>');
					}
					karma_flickr_search = function(data)
	    			{
	    				$("#pageBus").show();
	    				var fotos;
	    				var totalBus = data.photos.pages;
	    				$("#volver").hide();
	    				$('select[id=pagebus]').find('option').remove();
	    				if (totalBus>100) totalBus=100;
						for (var a=1 ; a<= totalBus; a++)
						{
							$("#pagebus").append('<option value="'+ a +'">'+ a +'</option>');
						}
	    				for(var i in data.photos.photo)
						{
							var html = 'http://farm'+ data.photos.photo[i].farm +'.static.flickr.com/'+ data.photos.photo[i].server +'/'+ data.photos.photo[i].id +'_'+ data.photos.photo[i].secret;
							//fotos += '<tr><td><input name="flickr" type="checkbox" id="'+ data.photos.photo[i].id +'" value='+ html +' class="flickr" /></td>';
							fotos += '<tr id="'+ data.photos.photo[i].id +'" class="borrar"><td><a href="#" id="'+ data.photos.photo[i].id +'" class="flickra"><img src="'+ html +'_s.jpg" /></a></td>';
							fotos += '<td style="width:300px;">'+ data.photos.photo[i].title +'</td></tr>';
							
						}
						fotos += '</tbody></table>';
						var numElems = ($("#flickr_bus tr.borrar").length)-1;
						if(numElems == -1){
							$(fotos).hide().appendTo($("#flickr_bus tbody")).slideDown();
						}else{
							$("#flickr_bus tr.borrar").each(function(i) {
								$(this).slideUp(function(){$(this).remove();});
								if (i == numElems) {
									$(fotos).hide().appendTo($("#flickr_bus tbody")).slideDown();
								} 
								
							});
						}
						$("#pagebus").val(currentPage);
						if(currentPage < totalBus)
						{
							$("#next").show();
						}	
						else
						{
							$("#next").hide();
						}
						if(currentPage > 1)
						{
							$("#prev").show();
						}
						else
						{
							$("#prev").hide();
						}
					}					
					
					karma_flickr_next = function(data)
	    			{
	    				var fotos;
	    				for(var i in data.photos.photo)
						{
							var html = 'http://farm'+ data.photos.photo[i].farm +'.static.flickr.com/'+ data.photos.photo[i].server +'/'+ data.photos.photo[i].id +'_'+ data.photos.photo[i].secret;
							//fotos += '<tr><td><input name="flickr" type="checkbox" id="'+ data.photos.photo[i].id +'" value='+ html +' class="flickr" /></td>';
							fotos += '<tr id="'+ data.photos.photo[i].id +'"><td><a href="#" id="'+ data.photos.photo[i].id +'" class="flickra"><img src="'+ html +'_s.jpg" /></a></td>';
							fotos += '<td style="width:300px;">'+ data.photos.photo[i].title +'</td></tr>';
							
						}
						fotos += '</tbody></table>';
						var numElems = ($("#flickr tr").length)-1;
						$("#flickr tr").each(function(i) {
							$(this).slideUp(function(){$(this).remove();});
							if (i == numElems) {
								$(fotos).hide().appendTo($("#flickr tbody")).slideDown();
							} 
							
						});
						$("#pages").val(current);
						if(current < total)
						{
							$("#siguiente").show();
						}	
						else
						{
							$("#siguiente").hide();
						}
						if(current > 1)
						{
							$("#anterior").show();
						}
						else
						{
							$("#anterior").hide();
						}
					}
					$("#pages").live('change',function(){
						current = $(this).val();
						$.getScript(userPhoto + "&user_id="+ user +"&api_key="+ api +"&per_page="+ count +"&page="+ current +"&format=json&jsoncallback=karma_flickr_next");
					});
					$("#pagebus").find('option').live('click',function(){
						var keys = $("#buscar").val().replace(/ /g,'+');
						currentPage = $(this).val();
						$.getScript(searchPhoto +"&api_key="+ api +"&text="+ keys +"&per_page="+ count +"&page="+ currentPage +"&format=json&jsoncallback=karma_flickr_search");
					});
	    			$("#siguiente").click(function(){
		    			current ++;
		    			$.getScript(userPhoto +"&user_id="+ user +"&api_key="+ api +"&per_page="+ count +"&page="+ current +"&format=json&jsoncallback=karma_flickr_next");
		    			
	    			});
	    			$("#anterior").click(function(){
		    			current --;
		    			$.getScript(userPhoto +"&user_id="+ user +"&api_key="+ api +"&per_page="+ count +"&page="+ current +"&format=json&jsoncallback=karma_flickr_next");
		    			
	    			});
	    			$("#atras").click(function(){
	    				$("#atras").hide();
	    				$.getScript(userPhoto +"&user_id="+ user +"&api_key="+ api +"&per_page="+ count +"&page="+ current +"&format=json&jsoncallback=karma_flickr_next");
	    			});
	    			$("#busqueda:input").click(function(){
						var keys = $("#buscar").val().replace(/ /g,'+');
						currentPage = 1;
						$.getScript(searchPhoto +"&api_key="+ api +"&text="+ keys +"&per_page="+ count +"&page="+ currentPage +"&format=json&jsoncallback=karma_flickr_search");
					});
					$("#next").click(function(){
						var keys = $("#buscar").val().replace(/ /g,'+');
						currentPage ++;
						$.getScript(searchPhoto +"&api_key="+ api +"&text="+ keys +"&per_page="+ count +"&page="+ currentPage +"&format=json&jsoncallback=karma_flickr_search");
					});
					$("#prev").click(function(){
						var keys = $("#buscar").val().replace(/ /g,'+');
						currentPage --;
						$.getScript(searchPhoto +"&api_key="+ api +"&text="+ keys +"&per_page="+ count +"&page="+ currentPage +"&format=json&jsoncallback=karma_flickr_search");
					});
					$("#volver").click(function(){
						var keys = $("#buscar").val().replace(/ /g,'+');
						$.getScript(searchPhoto +"&api_key="+ api +"&text="+ keys +"&per_page="+ count +"&page="+ currentPage +"&format=json&jsoncallback=karma_flickr_search");
					});
	    			$("#flickr a").live('click', function(){
	    				$("#atras").hide();
	    				$("#siguiente").hide();
	    				$("#anterior").hide();
	    				$("span#page").hide();
	    				var currentId = $(this).attr('id');
	    				$.getScript(getSizes +"&api_key="+ api +"&photo_id="+ currentId +"&format=json&jsoncallback=karma_flickr_sizes");
	    				karma_flickr_sizes = function(data)
						{
							var sizes;
							sizes = '<tr id="borrame"><td>';
							for(var a in data.sizes.size)
							{
								sizes += '<input class="radioGalery" id="radio" type="radio" name="'+ currentId +'" value="'+ data.sizes.size[a].source +'">'+ data.sizes.size[a].label +' '+ data.sizes.size[a].height +'x'+ data.sizes.size[a].width + '<br />'; 
							}
							sizes += '</td></tr>';
					 		
							var Elems = ($("#flickr tr[id!="+ currentId +"]").length)-1;
							$("#flickr tr[id!="+ currentId +"]").each(function(i) {
								$(this).slideUp(function(){$(this).remove();});
								if (i == Elems) {
									$("#borrame").remove();
					 				$(sizes).hide().appendTo($("#flickr tbody")).slideDown();
								} 
							});
							$("#atras").show();		
						}
	    			});
	    			$("#flickr_bus a").live('click', function(){
	    				$("#volver").hide();
	    				$("#next").hide();
	    				$("#prev").hide();
	    				var currentId = $(this).attr('id');
	    				$.getScript(getSizes +"&api_key="+ api +"&photo_id="+ currentId +"&format=json&jsoncallback=karma_flickr_sizes");
	    				karma_flickr_sizes = function(data)
						{
							var sizes;
							sizes = '<tr id="borrame" class="borrar"><td>';
							for(var a in data.sizes.size)
							{
								sizes += '<input class="radioGalery" id="radio" type="radio" name="'+ currentId +'" value="'+ data.sizes.size[a].source +'">'+ data.sizes.size[a].label +' '+ data.sizes.size[a].height +'x'+ data.sizes.size[a].width + '<br />'; 
							}
							sizes += '</td></tr>';
							var Elems = ($("#flickr_bus tr[id!="+ currentId +"][class='borrar']").length)-1;
							$("#flickr_bus tr[id!="+ currentId +"][class='borrar']").each(function(i) {
								$(this).slideUp(function(){$(this).remove();});
								if (i == Elems) {
									$("#borrame").remove();
					 				$(sizes).hide().appendTo($("#flickr_bus tbody")).slideDown();
								} 
							});
							
							$("#volver").show();		
						}
	    			});						

	    		},
	    		callback:function(v,m)
	    		{
	            if(v==false) {
	            	
	            	//$.prompt.close();
	    			// No guardar
	    			} else {
	    				var foto = '';
	    				$(':radio:checked',m).each(function(i){
							foto += '<img src="'+$(this).val()+'" class="flickr" />';   				
	    				});
						if(foto != ''){     				
		    				o_wym.insert(foto);
						o_wym.update();
	    				}
						//$.prompt.close();
	    			}
	    		}
	    	});
			
		}
    return;
    });
};
