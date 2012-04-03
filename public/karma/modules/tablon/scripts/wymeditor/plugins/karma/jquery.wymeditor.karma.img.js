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

WYMeditor.editor.prototype.karma_img = function(o) {
 
  var wym = this;
  var path = 'modules/tablon/scripts/wymeditor/plugins/karma/getImages.php';
  var path_img = 'images';
  if (typeof(o.path) !== 'undefined') {
	  path_img = o.path;
  }
  
  this.karma_img_sizes = function() {
	  var name = arguments[2];
	  var category = arguments[1];
	  if (arguments.length) {
		  if (arguments[0] == 'sizes') {
			  $.getJSON(path+"?sizes", function(data){
				  var sizeScreen = '<div id="images"><table><tr><td><img src="../' + path_img + '/'+ category +'/150_150/'+ name +'" /></td></tr>';
				  for ( var a in data.sizes ) {
					  sizeScreen += '<tr><td><input class="radioGalery" type="radio" name="image" id="sizeRad" value="' + path_img + '/'+ category +'/'+ data.sizes[a].width +'_'+ data.sizes[a].height +'/'+ name +'">'+data.sizes[a].nombre+'  '+ data.sizes[a].width +'x'+ data.sizes[a].height +'</input></td></tr>';
				  }
				  sizeScreen += '</table></div>';
				  $('#Galeria #images').remove();
				  $('#Galeria').append(sizeScreen);
			  });
		  }
	  }
  }
  
  this.karma_img_siguientes = function() {
	  if (arguments.length) {
		  if (arguments[0] === "categoria" && typeof(arguments[2]) != 'undefined') {
			  $.getJSON(path+"?category="+arguments[1]+"&pag="+arguments[2], function(data){
				  var nextimg = '<div id="images"><table id="imaGal"><tr>';
				  for ( var i in data.img ) {
					  if ( i == "0" || i == '5') {nextimg += '<tr>';}
					  nextimg += '<td><img class="img" src="../' + path_img + '/'+ data.cat +'/150_150/'+ data.img[i].nombre_img +'" id="'+data.img[i].nombre_img+'" value="'+data.cat+'" /><td>';
					  if ( i == "4" || i == '9') {nextimg += '</tr>';}
				  }
				  nextimg += '</table><div id="pagGalery" class="galeriaPaginado">';
				  if ( data.pag > 1 ) nextimg += '<img src="icons/prevPage.png" id="prev" />';//'<input id="prev" type="submit" value="Anterior" style="width:80px;" />';
				  if ( data.pages > data.pag ) nextimg += '<img src="icons/nextPage.png" id="next" />';//'<input id="next" type="submit" value="Siguiente" style="width:80px;" />';
				  nextimg += '</div></div>';
				  $('#Galeria #images').remove();
				  $('#Galeria').append(nextimg);
				  $('#next').bind('click', function(){
					  var next = data.pag;
					  next++;
					  wym.karma_img_listado_imgs("categoria",data.img[0].idCategoria,next);
				  });
				  $('#prev').bind('click', function(){
					  var prev = data.pag;
					  prev--;
					  wym.karma_img_listado_imgs("categoria",data.img[0].idCategoria,prev);
				  });
				  $('img.img').bind('click', function(){
					  wym.karma_img_sizes('sizes',$(this).attr('value'),$(this).attr('id'));
				  });
			  });
		  }
	  }
  }
 
  this.karma_img_listado_imgs = function() {
	  
	  if (arguments.length) {
		  if (arguments[0] === true) {
			  $.getJSON(path, function(data){
				  var categorias = '<table id="Galeria"><caption>Galería de Imagenes</caption><tr><td><ul id="categories">';
				  for ( var i in data.cat ) {
					  categorias += '<li id="'+ data.cat[i].idCategoria +'">'+ data.cat[i].nombre +'</li>';
			  	  }
				  categorias += '</ul></td></tr></table>';
			  	  wym.karma_img_dibujar(categorias);
			  });
		  }
		  if (arguments[0] === "categoria" && typeof(arguments[2]) == 'undefined') {
			  $.getJSON(path+"?category="+arguments[1], function(data){
				  var images = '<div id="images"><table id="imaGal"><tr>';
				  for ( var i in data.img ) {
					  if ( i == "0" || i == '5') {images += '<tr>';}
					  images += '<td><img class="img" src="../' + path_img + '/'+ data.cat_url +'/150_150/'+ data.img[i].nombre_img +'" id="'+data.img[i].nombre_img+'" value="'+data.cat_url+'" /><td>';
					  if ( i == "4" || i == '9') {images += '</tr>';}
				  }
				  images += '</table><div id="pagGalery" class="galeriaPaginado">';
				  if ( data.pages > data.pag ) images += '<img src="icons/nextPage.png" id="next" />';//'<input id="next" type="submit" value="Siguiente" style="width:80px;" />';
				  images += '</div>';
				  $('#Galeria').find('#images').remove();
				  $('#Galeria').append(images);
				  $('#next').bind('click', function(){
					  var next = data.pag;
					  next++;
					  wym.karma_img_siguientes("categoria",data.img[0].idCategoria,next);
				  });
				  $('img.img').bind('click', function(){
					  wym.karma_img_sizes('sizes',$(this).attr('value'),$(this).attr('id'));
				  });
			  });
		  }

		  if (arguments[0] === "categoria" && typeof(arguments[2]) != 'undefined') {
			  $.getJSON(path+"?category="+arguments[1]+"&pag="+arguments[2], function(data){
				  var nextimg = '<div id="images"><table id="imaGal"><tr>';
				  for ( var i in data.img ) {
					  if ( i == "0" || i == '5') {nextimg += '<tr>';}
					  nextimg += '<td><img class="img" src="../' + path_img + '/'+ 'images/'+ data.cat_url +'/150_150/'+ data.img[i].nombre_img +'" id="'+data.img[i].nombre_img+'" value="'+data.cat_url+'" /><td>';
					  if ( i == "4" || i == '9') {nextimg += '</tr>';}
				  }
				  nextimg += '</table><div id="pagGalery" class="galeriaPaginado">';
				  if ( data.pag > 1 ) nextimg += '<img src="icons/prevPage.png" id="prev" />';//'<input id="prev" type="submit" value="Anterior" style="width:80px;" />';
				  if ( data.pages > data.pag ) nextimg += '<img src="icons/nextPage.png" id="next" />';//'<input id="next" type="submit" value="Siguiente" style="width:80px;" />';
				  nextimg += '</div></div>';
				  $('#Galeria #images').remove();
				  $('#Galeria').append(nextimg);
				  $('#next').bind('click', function(){
					  var next = data.pag;
					  next++;
					  wym.karma_img_siguientes("categoria",data.img[0].idCategoria,next);
				  });
				  $('#prev').bind('click', function(){
					  var prev = data.pag;
					  prev--;
					  wym.karma_img_siguientes("categoria",data.img[0].idCategoria,prev);
				  });
				  $('img.img').bind('click', function(){
					  wym.karma_img_sizes('sizes',$(this).attr('value'),$(this).attr('id'));
				  });
			  });
		  }
			  	
	  }
	  
	  
	  
  };
  
  this.karma_img_dibujar = function(campos) {
		$.prompt(campos,{
			buttons:
			{
    			Añadir: true,
    			Cancelar: false
    		},
    		loaded: function(){
    			$('#categories li').live('click', function(){
    				var cat = $(this).attr('id');
    				wym.karma_img_listado_imgs("categoria",cat);
    			});
    			$('#jqi').css({"left":"20%","top":"5%","margin-left":"0"});
    			
    		},
			callback:function(v,m)
			{
				if(v==false) {
				} else {
					var img = '';
					$(':radio:checked',m).each(function(i){
					img += '<img src="'+$(this).val()+'" />';
					wym.insert(img);
					wym.update();
				});
				}
			}
		});
  }
  
 //construct the button's html
  var html = "<li class='wym_tools_karma_img'>"
         + "<a name='Nueva Imagen' href='#'>"
         + "Nueva Imagen"
         + "</a></li>";

  //add the button to the tools box
  jQuery(wym._box)
    .find(wym._options.toolsSelector + wym._options.toolsListSelector)
    .append(html);

  //handle click event
  jQuery(wym._box).find('li.wym_tools_karma_img a').click(function() {
	  	wym.karma_img_listado_imgs(true);
	  	return;
  });
    
    
};