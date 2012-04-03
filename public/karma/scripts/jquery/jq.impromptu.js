/*
 * jQuery Impromptu
 * By: Trent Richardson [http://trentrichardson.com]
 * Version 2.4
 * Last Modified: 3/22/2009
 * 
 * Copyright 2009 Trent Richardson
 * Dual licensed under the MIT and GPL licenses.
 * http://trentrichardson.com/Impromptu/GPL-LICENSE.txt
 * http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
 * 
 */
(function($){
	$.prompt=function(m,o){
		o=$.extend({},$.prompt.defaults,o);
		$.prompt.currentPrefix=o.prefix;
		var ie6=($.browser.msie&&$.browser.version<7);
		var b=$(document.body);
		var w=$(window);
		var msgbox='<div class="'+o.prefix+'box" id="'+o.prefix+'box"';

		if (o.width!=false) {msgbox += ' style="width:'+o.width+'px;"';}

		msgbox += '>';
		if(o.useiframe&&(($('object, applet').length>0)||ie6))
			msgbox+='<iframe src="javascript:;" class="'+o.prefix+'fade" id="'+o.prefix+'fade"></iframe>';
		else{
			if(ie6)
				$('select').css('visibility','hidden');
			msgbox+='<div class="'+o.prefix+'fade" id="'+o.prefix+'fade"></div>';
		}
		msgbox+='<div class="'+o.prefix+'" id="'+o.prefix+'"><div class="'+o.prefix+'container" id="'+o.prefix+'container">';
		if(o.showClose=== true)
			msgbox+='<div class="'+o.prefix+'close">X</div>';
		msgbox+='<div id="'+o.prefix+'states"></div>';+'</div></div></div>';
		var jqib=$(msgbox).appendTo(b);
		var jqi=jqib.children('#'+o.prefix);
		var jqif=jqib.children('#'+o.prefix+'fade');
		if(m.constructor==String){
			m={state0:{html:m,buttons:o.buttons,focus:o.focus,submit:o.submit}};
		}
		var states="";
		$.each(m,function(statename,stateobj){
			stateobj=$.extend({},$.prompt.defaults.state,stateobj);
			m[statename]=stateobj;
			states+='<div id="'+o.prefix+'_state_'+statename+'" class="'+o.prefix+'_state" style="display:none;"><div class="'+o.prefix+'message">'+stateobj.html+'</div><div class="'+o.prefix+'buttons">';
			
			$.each(stateobj.buttons,function(k,v){
				showValue = k;
				//if (o.translation[k]) showValue = o.translation[k];
				states+='<button name="'+o.prefix+'_'+statename+'_button'+k+'" id="'+o.prefix+'_'+statename+'_button'+k+'" value="'+v+'">'+showValue+'</button>';
			});
			states+='</div></div>';
		});
		jqi.find('#'+o.prefix+'states').html(states).children('.'+o.prefix+'_state:first').css('display','block');
		jqi.find('.'+o.prefix+'buttons:empty').css('display','none');
		$.each(m,function(statename,stateobj){
			var state=jqi.find('#'+o.prefix+'_state_'+statename);
			state.children('.'+o.prefix+'buttons').children('button').click(function(){
				var msg=state.children('.'+o.prefix+'message');
				var clicked=stateobj.buttons[$(this).text()];
				var forminputs={};
				$.each(jqi.find('#'+o.prefix+'states :input').serializeArray(),function(i,obj){
					if(forminputs[obj.name]==undefined)
						forminputs[obj.name]=obj.value;
					else 
						if(typeof forminputs[obj.name]==Array)
							forminputs[obj.name].push(obj.value);
						else 
							forminputs[obj.name]=[forminputs[obj.name],obj.value];
				});
				if(stateobj.submit(clicked,msg,forminputs))
					removePrompt(true,clicked,msg,forminputs);
			});
			state.find('.'+o.prefix+'buttons button:eq('+stateobj.focus+')').addClass(o.prefix+'defaultbutton');
		});
		var ie6scroll=function(){
			jqib.css({top:w.scrollTop()});
		};
		var fadeClicked=function(){
			if(o.persistent){
				var i=0;
				jqib.addClass(o.prefix+'warning');
				var intervalid=setInterval(function(){
					jqib.toggleClass(o.prefix+'warning');
					if(i++>1){
						clearInterval(intervalid);
						jqib.removeClass(o.prefix+'warning');
					}
				},100);
			}else 
				removePrompt();
		};
		var escapeKeyClosePrompt=function(e){
			var key=(window.event)?event.keyCode:e.keyCode;
			if(key==27)
				removePrompt();
		};
		var positionPrompt=function(){
			jqib.css({position:(ie6)?"absolute":"fixed",height:w.height(),width:"100%",top:(ie6)?w.scrollTop():0,left:0,right:0,bottom:0});
			jqif.css({position:"absolute",height:w.height(),width:"100%",top:0,left:0,right:0,bottom:0});
			jqi.css({position:"absolute",top:o.top,left:"50%",marginLeft:((jqi.outerWidth()/2)*-1)});
		};
		var stylePrompt=function(){
			jqif.css({zIndex:o.zIndex,display:"none",opacity:o.opacity});
			jqi.css({zIndex:o.zIndex+1,display:"none"});
			jqib.css({zIndex:o.zIndex});
		};
		var removePrompt=function(callCallback,clicked,msg,formvals){
			jqi.remove();
			if(ie6)
				b.unbind('scroll',ie6scroll);
				w.unbind('resize',positionPrompt);
				jqif.fadeOut(o.overlayspeed,function(){
					jqif.unbind('click',fadeClicked);
					jqif.remove();
					if(callCallback)
						o.callback(clicked,msg,formvals);
					jqib.unbind('keypress',escapeKeyClosePrompt);
					jqib.remove();
					if(ie6&&!o.useiframe)
						$('select').css('visibility','visible');
				});
		};
		positionPrompt();
		stylePrompt();
		if(ie6)
			w.scroll(ie6scroll);
		jqif.click(fadeClicked);
		w.resize(positionPrompt);
		jqib.keypress(escapeKeyClosePrompt);
		jqi.find('.'+o.prefix+'close').click(removePrompt);
		jqif.fadeIn(o.overlayspeed);
		jqi[o.show](o.promptspeed,o.loaded);
		//jqi.find('#'+o.prefix+'states .'+o.prefix+'_state:first .'+o.prefix+'defaultbutton').focus();
		jqi.find("input:eq(0)").focus();
		if (o.draggable) jqi.draggable();
		return jqib;
	};
	$.prompt.defaults={
		prefix:'jqi',
		buttons:{Ok:true},
		translation:{},
		width:false,
		loaded:function(){},
		submit:function(){return true;},
		callback:function(){},
		opacity:0.6,
		zIndex:999,
		overlayspeed:'slow',
		promptspeed:'fast',
		draggable:true,
		show:'show',
		focus:0,
		useiframe:false,
		top:"16%",
		showClose:false,
		persistent:true,
		state:{
			html:'',
			buttons:{Ok:true},
			focus:0,
			submit:function(){return true;}
		}
	};
	$.prompt.currentPrefix=$.prompt.defaults.prefix;
	$.prompt.setDefaults=function(o){
		$.prompt.defaults=$.extend({},$.prompt.defaults,o);
	};
	$.prompt.setStateDefaults=function(o){
		$.prompt.defaults.state=$.extend({},$.prompt.defaults.state,o);
	};
	$.prompt.getStateContent=function(state){
		return $('#'+$.prompt.currentPrefix+'_state_'+state);
	};
	$.prompt.goToState=function(state){
		$('.'+$.prompt.currentPrefix+'_state').slideUp('slow');
		$('#'+$.prompt.currentPrefix+'_state_'+state).slideDown('slow',function(){
			$(this).find('.'+$.prompt.currentPrefix+'defaultbutton').focus();
		});
	};
	$.prompt.nextState=function(){
		var next=$('.'+$.prompt.currentPrefix+'_state:visible').next();
		$('.'+$.prompt.currentPrefix+'_state').slideUp('slow');
		next.slideDown('slow',function(){
			$(this).find('.'+$.prompt.currentPrefix+'defaultbutton').focus();
		});
	};
	$.prompt.prevState=function(){
		var next=$('.'+$.prompt.currentPrefix+'_state:visible').prev();
		$('.'+$.prompt.currentPrefix+'_state').slideUp('slow');
		next.slideDown('slow',function(){
			$(this).find('.'+$.prompt.currentPrefix+'defaultbutton').focus();
		});
	};
	$.prompt.close=function(){
		$('#'+$.prompt.currentPrefix+'box').fadeOut('fast',function(){
			$(this).remove();
		});
	};
})(jQuery);

