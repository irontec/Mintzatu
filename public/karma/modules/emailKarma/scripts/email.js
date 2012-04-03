var buttons = {header:"#EmailKarmaButton"};
var autoto = false;

var emailsys = {
	tt:function(){ tinyMCE.init(mailconf); },
	load:function(){
		emailsys.binds();
		
		if($('#multiSendEmail').length>0){
			$('#multiSendEmail').parents('li').bind('click',function(){
				var self = $(this);
				var lngth = $('tr:not(.tablonClone) td.multiselect input:checked').length;
		 		if (lngth == 0) {
		 			
		 			$.prompt(_("no_filas_sel"));
		 			return false;
		 		}
		 		f=false;
		 		emailsys.preloadedTO = {};
		 		for(i=0;i<lngth;i++){
		 			if (f==false) {
		 				firstid = $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parent("td").attr("id").replace(/ms::/,"");
		 				f=true;
		 			}
		 			/*(((i!=0)? ',':'')+ $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parent("td").attr("id").replace(/ms::/,""));*/
		 			emailsys.preloadedTO[i] = $('tr:not(.tablonClone) td.multiselect input:checked:eq('+i+')').parents("tr").find('.emailField').text();
		 		}	
		 		autoto = true;
		 		
		 		emailsys.openLayer(true);
		 		
			});
		}
		
	/*	$(buttons.header).trigger("click");*/
	}
	,
	binds : function(){
		$(buttons.header).bind('click',function(){emailsys.openLayer(false)});
		
		
	}
	,
	loads : function(){
		//tinyMCE.init(mailconf);
		$(".autocomplete_to").autocomplete_old(
				"./modules/emailKarma/ajax/ops_emailKarma.php?ajax=true&load=contacts_autocomplete&mod=contacts",{
				multiple: true,
				mustMatch: false,
				autoFill: true
		});
		
		emailsys.bindopts();
		
		if (emailsys.preloadedTO&&autoto==true){
			var val = "";
			for (var a in emailsys.preloadedTO){
				val+= emailsys.preloadedTO[a] ;
				val+=", "; 
			}
			$(".autocomplete_to").val(val);
		}
		$('body').css('cursor','default');
	}
	,
	bindopts : function(){
		$('#save').bind('click',function(){emailsys.sendEmail('save')});
		$('#send').bind('click',function(){emailsys.sendEmail('send')});
		return false;
	}
	,
	formValues : {}
	,
	sendEmail : function(q){
		$('#emailForm').find('select,input[type=text],textarea').each(function(){
			var self = $(this);
			emailsys.formValues[self.attr('name')] = self.val();
		});
		
		$.ajax({type:"POST",url:"./modules/emailKarma/ajax/ops_emailKarma.php?ajax=true&load=savemail&action="+q,data:emailsys.formValues,dataType:"json",
			success:function(data){
				if (err = data.formaterrors){
					for (var a in err){
						tmperr = err[a];
						$('#emailForm').find('*[name='+a+']').after('<br class="errorspan" /><span class="errorspan" >'+tmperr+'</span>');
					}
					window.setTimeout(function(){$('.errorspan').fadeOut('slow',function(){$(this).remove();});},2500);
				}
				if (data.save){
					$('.tabcontainer').before('<br class="errorspan" /><h2 class="errorspan" >saved</h2>');
					window.setTimeout(function(){$('.errorspan').fadeOut('slow',function(){$(this).remove();});},2500);
					
				}
				if (data.send){
					$('.tabcontainer').before('<br class="errorspan" /><h2 class="errorspan" >Success</h2>');
					window.setTimeout(function(){$('.errorspan').fadeOut('slow',function(){$(this).remove();}); $('.tabbuttom[mod=fast_mail]').removeClass('selected').trigger('click'); },2500);
				}
			}
		});
		
		
	}
	,
	openLayer : function(o){
		autoto = o;
		var opts = {ajax:true,load:"init"};
		$.getJSON("./modules/emailKarma/ajax/ops_emailKarma.php",opts,function(data) {
			if (data.error){
				txt = '<div id="emailGeneral">';
				txt+= '<h1>Karma Email System</h1>';
				txt+= '<div class="emailGeneral"><p>'+data.error+'</p></div>';
				txt+= '</div>';
			}else{
				txt = '<div id="emailGeneral">';
				txt+= '<h1>Karma Email System</h1>';
				txt+= '<div id="tabs" >'+data.menu+'<div id="acontens">'+data.html+'</div></div>';
				txt+= '</div>';
			}
			$.prompt(txt,{
				buttons: {Close:true},
				draggable : false,
				loaded: function(){ 
					emailsys.loads();
					emailsys.tt();		
					$('.tabbuttom').each(function(i){
						$(this).bind('click',emailsys.loadWindow);
					});
					//tinyMCE.init(mailconf);
					
				},
				submit: function(v,m){return true}		
			});
		});
	}
	,
	loadWindow : function(){
		var self=$(this); 
		if (!self.hasClass('selected')){
			$('body').css('cursor','wait');
			$('.tabbuttom').removeClass('selected');
			self.addClass('selected');
			var opts = {ajax:true,load:"mod",mod:self.attr('mod')};
			if (emailsys.load_id_email!==false){
				opts.load_id = emailsys.load_id_email;
				emailsys.load_id_email=false;
			}
			$.getJSON("./modules/emailKarma/ajax/ops_emailKarma.php",opts,function(data) {
				if (data.error){
					txt  = data.error;
				}
				txt = data.html;
				$('.tabcontainer').fadeOut('slow',function(){
					$(this).remove();
					$('#acontens').append(txt);
					emailsys.loads();
					if (data.jsCallback){
							eval(data.jsCallback+"()");
					}
					//tinyMCE.init(mailconf);
				});
			});
		}
		
	}
	,
	storageBind : function() {
		$(".load_email").unbind('click').bind('click',function(){
			emailsys.load_id_email = $(this).attr('id_email');
			$('.tabbuttom[mod=fast_mail]').trigger('click');
			
			
		}).Tooltip({ 
		    track: true, 
		    delay: 450, 
		    showURL: false, 
		    showBody: " - ", 
		    opacity: 0.85 
		});
	}
	,
	load_id_email:false,
	preloadedTO : false
};

$(document).ready(emailsys.load);