var cate = false;

$(document).ready(function() {

	var writeCats = function(c){
		select = '<select name="cat" id="cat" >';
		select3 = '<select name="cat3" id="cat3" >';
		html = '<ul>';
		for (var i in c){ 
			select+=' <option value="'+c[i]+'" ';
			if (cate==c[i]){
				select+=' selected="selected" ';
			}
			select+=' >'+c[i]+'</option>';
			select3+=' <option value="'+c[i]+'" >'+c[i]+'</option>';
			html+= '<li><a href="" class="catlink" >'+c[i]+'</a> <a href="'+c[i]+'" class="delcatlink" >borrar</a></li>';	
		}
		select+='</select>';
		select3+='</select>';
		html+= '</ul>';
		$('#galleryselect').html(select);	
		$('#galleryselect3').html(select3);
		$('#galleryselect2').html(html);
		$('.catlink').unbind('click').bind('click',function(){return false;});
		$('.delcatlink').unbind('click').bind('click',function(){ delCat($(this)); return false;});
		$('#submitc').unbind('click').bind('click',function(){ newCat();  return false;});
		$('#submitimg').unbind('click').bind('click',function(){ bindUimgs();  return false;});
		
		$('#cat').unbind('change').bind('change',function(){
			cate = $(this).val(); 
			$('#gallery > img').fadeOut('fast',function(){
					$(this).remove();
					
			});
			loadGallery({w:'all',c:cate});
			return false;
		});
	};
	
	var bindUimgs = function(){
	
            $.ajaxFileUpload({
                url: "ajax_save.php?c="+$('#cat3').val(),
                secureuri:false,
                fileElementId: 'fileToUpload',
                dataType: 'json',
                success: function (j) {
                	if (!j.error){
                		$('#submitimg').after('<span id="resultadop" ><br /><br />'+j.result+"</span>");
						window.setTimeout(function(){ $('#resultadop').fadeOut('slow',function(){ $(this).remove();}); },2000);
                		$('#gr').append('<img src="'+j.route+'" class="previmg"/>');
        			}else{
						alert(j.error);
					}
                },
                error: function (data, status, e) {
                    alert(e);
                }
            });
	
	
	};
            
            
	var newCat = function(){
		$.getJSON("ajax_gallery.php",{w:'newCat',val:$('#newcategory').val()},function(data){
			if (!data.error){
				$('#submitc').after('<span id="resultadop" ><br /><br />'+data.result+"</span>");
				window.setTimeout(function(){ $('#resultadop').fadeOut('slow',function(){ $(this).remove();}); },2000);
			}else{
				alert(data.error);
			}
		});	
		
	};
	
	var delCat = function(t){
		if (confirm('borrar la entrada?')){
			$.getJSON("ajax_gallery.php",{w:'delCat',val:t.attr('href')},function(data){
				if (!data.error){
					t.prev('a').wrap('<strike>');
				}else{
					alert(data.error);
				}
			});	
		}
	};	
	
	var writeImgs = function(c,p,svname){
		for (var i in c){
			$('#gallery').append('<img src="'+p+c[i]+'" class="preimg"/>');
		}
		$('.preimg').bind('click',function(){
			$('#src').val(svname+$(this).attr('src'));
			ImageDialog.showPreviewImage(svname+$(this).attr('src'))
		});
	};

	var loadGallery = function(w) {
		$.getJSON("ajax_gallery.php",w,function(data){
			if (!data.error){
				if(data.categories) writeCats(data.categories);
				if(data.images) writeImgs(data.images,data.path,data.svname);
			}
		});		
	};



	loadGallery({w:'all'});
	$('#recargar').bind('click',function(){loadGallery({w:'all'}); return false;});
	
});