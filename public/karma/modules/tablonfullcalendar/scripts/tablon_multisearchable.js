var miniLoader = new Image();
miniLoader.src = "./icons/miniloader.gif";
var ml_changed = false;
$(function() {
	
	var t = $("#tablon");
	var opts = {op:'tablon_multisearchable'};
	opts['target_plt'] = $("#__mrs_target_plt").val();
	opts['target_selected_id'] = $("#__mrs_target_selected_id").val();
	opts['target_searchable_id'] = $("#__mrs_target_searchable_id").val();
	opts['target_selected_value'] = $("#__mrs_target_selected_value").val();	
	
	
	$('tr td.multiselect input[type=checkbox]',t).bind("change",function() {
		$(this).fadeOut("slow",function() {
			var tt  = $(this);
			tt.after("<img src=\""+miniLoader.src+"\" />");
			opts['target_searchable_value'] = tt.parent("td").attr("id").replace(/ms::/,'');
			opts['acc'] = tt.attr("checked")? "rel":"unrel";
			opts['value'] = tt.attr("value")? tt.attr("value"):false;
			$.getJSON("./modules/tablon/ajax/ops_tablon.php",opts,function(j) {
				if (j.error) {
					$.prompt("["+_(j.strError)+"]\n",{buttons: {Ok: true}});					
				}
				if (j.value) tt.val(j.value); 
				tt.attr("checked",j.state);
				tt.next("img").fadeOut("slow",function(){
					$(this).remove();
					tt.fadeIn("slow");
				});
			});
			
		});
	});
	
	

	$("#__multisearchable_showselected").bind("change",function() {
		if (ml_changed) return;
		ml_changed = true;
		$(this).unbind("change");
		$(this).next().after('<img src="./icons/miniloader.gif" /> ['+_('reloading')+']');
		var valor = ($(this).attr("checked"))? "1":"0";			
		$(this).attr('disabled','disabled');
		$("input[name=mrs_only_selected]").val(valor);
		$("#mrs_form_selected")[0].submit();
		//alert("go!");
		return false;	
	
	});

});
