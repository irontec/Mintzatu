var cate = false;

$(document).ready(function() {
	var ajaxpath;
	var config;
	
	var writeCats = function(c){
		select = '<select name="cat" id="cat" >';
		select3 = '<select name="cat3" id="cat3" >';
		html = '<ul>';
		for (var i in c){ 
			var aC = c[i];
			select+=' <option value="'+aC.idG+'" ';
			if (cate==aC.idG){
				select+=' selected="selected" ';
			}
			select+=' >'+aC.nameG+'</option>';
			select3+=' <option value="'+aC.idG+'" >'+aC.nameG+'</option>';
			html+= '<li><a href="" class="catlink" >'+aC.nameG+'</a> <a href="'+aC.idG+'" class="delcatlink" >borrar</a></li>';	
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
                url: "?action=save&config="+config+"&c="+$('#cat3').val(),
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
		$.getJSON("ajax_options.php",{w:'newCat',action:'gallery',config: config,val:$('#newcategory').val()},function(data){
			if (!data.error){
				$('#submitc').after('<span id="resultadop" ><br /><br />'+data.result+"</span>");
				window.setTimeout(function(){ $('#resultadop').fadeOut('slow',function(){ $(this).remove();}); },2000);
			}else{
				alert(data.error);
			}
		});	
		
	};
	
	var delCat = function(t){
		if (confirm('Borrar la entrada?\nSe perderan todas las imagenes que contenga.')){
			$.getJSON("ajax_options.php",{w:'delCat',action: 'gallery',config: config,val:t.attr('href')},function(data){
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
			var aC = c[i];
			$('#gallery').append('<img src="'+p+aC['img']+'" class="preimg" alt="'+aC['alt']+'" longdesc="'+aC['longDesc']+'"/>');
		}
		$('.preimg').bind('click',function(){
			$('#src').val(svname+$(this).attr('src'));
			$('#alt').val(svname+$(this).attr('alt'));
			$('#longdesc').val(svname+$(this).attr('longdesc'));
			ImageDialogGTable.showPreviewImage(svname+$(this).attr('src'))
		});
	};

	var loadGallery = function(w) {
		var editorId = tinyMCEPopup.editor.id;
		if(!tinyMCEPopup.getParam("advimageGTable_source_values") || tinyMCEPopup.getParam("advimageGTable_source_values").length<1 || tinyMCEPopup.getParam("advimageGTable_source_values")==""){
			var ajaxpath = "ajax_options.php";
		}else{
			var ajaxpath = tinyMCEPopup.getParam("advimageGTable_source_values");
		}
		if(!tinyMCEPopup.getParam("advimageGTable_cfg") || tinyMCEPopup.getParam("advimageGTable_cfg").length<1 || tinyMCEPopup.getParam("advimageGTable_cfg")==""){
			config = "";
		}else{
			config = tinyMCEPopup.getParam("advimageGTable_cfg");
		}
		$.getJSON(ajaxpath+"?action=gallery&config="+config+"&editor="+editorId,w,function(data){
			if (!data.error){
				if(data.categories) writeCats(data.categories);
				if(data.images) writeImgs(data.images,data.path,data.svname);
			}
		});		
	};

	loadGallery({w:'all'});
	$('#recargar').bind('click',function(){loadGallery({w:'all'}); return false;});
	
});