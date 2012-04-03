var newcal={};

var bindEventBox = function(){
	
	$('.eventBox').bind('click',function(){
		$('body').append('<div id="jqifade" class="jqifade" style="position: absolute; height: '+$(document).height()+'px ; width: 100%; top: 0pt; left: 0pt; right: 0pt; bottom: 0pt; z-index: 999; opacity: 0.6;background:#fff;"/>');
		$('#tablon tr.hidentr').each(function(){$(this).hide();});
		var oatr = $(this).attr('oatr');
		var tabletrid = $(this).attr('tabletrid');
		$('.'+oatr).css('border','1px solid #ffc000');
		$('#tablon tr[id="'+tabletrid+'"]').show();
		$('#tablonwrapper').fadeIn();
		$('#wrclose').bind('click',function(){ 
			$('#tablonwrapper').fadeOut('fast',function(){ $('#jqifade').hide(); $('#jqifade').remove(); $('.'+oatr).css('border','none');  }); 
		});
	});	
};

function calFunc(j,tr){
	tr.addClass('hidentr').hide();
	
	
		var tit = $('#princField').val().replace(/%ID%/g,j.id);
		

		//if (j.values[newID]) alert(unescape(j.values[newID]));
		
	html = '';	
	html+= '<div style="display:block;background:#efefef;float:left;" ';
	html+= ' class="eventBox e_'+j.id+'" oatr ="e_'+j.id+'" tabletrid="'+unescape(j.idTR)+'"   '; 
	html+= '>'+j.values[tit];
	//html+= ;
	html+= '</div>';
		

	$('.dateinfo').each(function(o){
		if (o>=newcal.ini && o<=newcal.end){
			$(this).after(html);
		}
	});
	bindEventBox();
}


$(document).ready(function(){
	var hour = $('.rhour').html();
	var min = $('.rmin').html();
	var sec = $('.rsec').html();
	/*var clock = window.setInterval(function(){
		if (hour==23&&min==59&&sec==59){
			hour = -1;
		}
		if (min==59&&sec==59){
			min = -1;
			hour = parseInt(hour)+1;
		}
		if (sec==59){
			sec = -1;
			min = parseInt(min)+1;
		}
		sec = parseInt(sec)+1;
		$('.rhour').html( ((hour<10)? "0"+hour:hour ));
		$('.rmin').html(((min<10)? "0"+min:min ));
		$('.rsec').html(((sec<10)? "0"+sec:sec));
	},1000);
*/
	
	

	$('body').append('<div id="cursortooltip" style="position:absolute;display:none;z-index:1000;top:0:left:0;background:#eee;" > Time interval: <br />from <span id="fromt" > <br />from</span> <br /> to  <br /><span id="tot" ></span> </div>');
	
	$().mousemove(function(e){
		$('#cursortooltip').css('left',e.pageX+10).css('top',e.pageY+10);
	}); 
					
	var bindAddEntry = function(){
		$('#addEntry').css('cursor','pointer').bind('click',function(){
			var box = $('.dateinfo');
			var start = false;
			var end = false;
			box.css('background','#dbdbdb').css('cursor','pointer').bind('click',function(){
				fvalue = $(this).attr('value');
				evalue = $(this).attr('endvalue');
				if (start){
					box.unbind('click');
					box.unbind('mouseover');
					end = evalue;
					
					tmpid = $(this).attr('id');
					a = tmpid.split('sp_');
					
					newcal.end = a[1];
					

					

					$('#cursortooltip').fadeOut();
					window.setTimeout(function(){ 
						
						$('#newInline').parent('li').trigger('click');
						var inter = window.setInterval(function(){
							if ($('input[name='+$('#startField').val()+']').length>=1){
								$('input[name='+$('#startField').val()+']').parents('tr').hide();
								$('input[name='+$('#startField').val()+']').val(start);
								$('input[name='+$('#endField').val()+']').parents('tr').hide();
								$('input[name='+$('#endField').val()+']').val(end);
								window.clearInterval(inter);
							}
						
							
						},200);
						
					
					},200);
					window.setTimeout(function(){ box.css('background','#fff'); },1200);
				}
				if (!start) {
						start=fvalue;
						$('#tot').html(evalue);
						$('#fromt').html(fvalue);
						$('#cursortooltip').fadeIn();
						id = $(this).attr('id'); 
						var ini = id.split('sp_');
						ini = ini[1]; 
						newcal.ini = ini;
				}

				
				$(this).css('background','#ffc0cc');
				box.bind('mouseover',function(){
					if (end) return false;
					evalue = $(this).attr('endvalue');
					$('#tot').html(evalue);
					tmpid = $(this).attr('id');
					a = tmpid.split('sp_');
					newcal.end = a[1];
					box.each(function(o){
						if (o>=ini && o<=a[1]){
							$(this).css('background','#ffc0cc');
						}else{
							if(o!=ini){
								$(this).css('background','#dbdbdb');
							}
						}
					});
				});
				
			});
		});
	};
	

	var hideTablon = function() {
		$('#optsTablon').hide();
		$('#tablon').wrap('<div id="tablonwrapper" ></div>');
		$('#tablonwrapper').hide();
		$('#tablonwrapper').prepend('<div class="title" >Edit Field</div><img src="./icons/cancel.png" id="wrclose" />');
		$('#tablon tr.hidentr').each(function(){$(this).hide();});
		bindEventBox();
		$('.tooltip').Tooltip({ 
		    track: true, 
		    delay: 450, 
		    showURL: false, 
		    showBody: " - ", 
		    opacity: 0.85 
		}).css("cursor","pointer");
		$('.ltooltip, .eventBox').Tooltip({ 
			track: true, 
		    delay: 450, 
		    showURL: false, 
		    showBody: " - ", 
		    opacity: 0.85 
		}).css("cursor","help");
	}
	
	var bindAjaxButtons = function (){
		$('a.ajaxLoad').bind('click',function(){
			$('body').css('cursor','wait');
			var url = $(this).attr('href');
			$('.calendarContainer').hide('fast',function(){
				window.setTimeout(function(){
					$.getJSON(url,{ajax:true},function(data){
						if (data.html){
							$('.calendarContainer').html(data.html);
							bindAjaxButtons();
							supertaBlon();
							hideTablon();
							bindAddEntry();
							$('.calendarContainer').slideDown('fast');
							$('body').css('cursor','default');
						}
						
					});
				},300);
				
				
			});

			return false;
		});
		
		
	};

	bindAjaxButtons();
	hideTablon();
	bindAddEntry();

});