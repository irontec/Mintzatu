/*
 jQuery Time Picker v0.3
 Creado por Diego Lago (beosman@gmail.com).
 Copyright (C) 2008 Diego Lago (http://beosman.dyndns.org/proyectos/timepicker)
 Fecha: 2008-11-23
 Licencia: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 
 Requiere:	jQuery v1.2.6+ (http://jquery.com)
 			jQuery Mousewheel v3.0+ (http://www.ogonek.net/mousewheel/jquery-demo.html)
 			
 TODO: Reparar la posición en según el alto del input.
 */
 
var globalcounter = 0;
 
(function($) {
	/**
		Opciones:
			divId			: El id del div que contiene el reloj. Por defecto es "timepicker".
	*/
    jQuery.fn.timepicker = function(options) {
  
  		// Las opciones del TimePicker
  		this.options = options || {};
  		
  		var onClose = this.options.onClose; 
  		// El control donde asignar la hora
  		this.input = null;
  		// La hora calculada
    	this.hour = 0;
    	// Los minutos calculados
    	this.minute = 0;
    	
    	// Formatea un valor de hora y minuto poniendo un 0 delante si es menor que 10
    	function formatValue(value) {
    		if(value < 10) {
    			return "0" + value;
    		} else {
    			return value.toString();
    		}
    	}
    	
    	// Devuelve el siguiente o el anterior valor de la hora o los minutos
    	// dependiendo del parámetro 'delta' (puede ser positivo o negativo).
    	function getValue(elem,delta,isHour) {
    		var limit = (isHour ? 24 : 60);
    		if(delta > 0) {
    			delta = +1;
    		} else if(delta < 0) {
    			delta = -1;
    		}
    		var value = parseInt($(elem).html(),10);
    		value = value + delta;
    		if(value == -1) {
    			value = (limit-1);
    		}
    		value %= limit;
    		if(isHour) {
    			jQuery.fn.timepicker.hour = value;
    		} else {
    			jQuery.fn.timepicker.minute = value;
    		}
    		return formatValue(value);
    	}
    
    	// El ID del elemento HTML que dibuja la hora.
    	var domElementId = this.options.divId || "timepicker";
    	
    	// El elemento del DOM construido en HTML.
//    	var htmlDomElement = "<div id=\"" + domElementId + "\" style=\"position: absolute; display:none;\" class=\"timepicker\"><span class=\"hours\">" + formatValue(this.hour) + "</span>:<span class=\"minutes\">" + formatValue(this.minute) + "</span></div>";
    	var htmlDomElement = "<div id=\"" + domElementId + "\" style=\"position: absolute; display:none;\" class=\"timepicker\"><span class=\"hours\">" + formatValue(this.hour) + "</span><div class=\"wrapper\"><span class=\"moreHours mas\"></span><span class=\"lessHours menos\"></span></div><div class=\"wrapper\"><span>:</span></div><span class=\"minutes\">" + formatValue(this.minute) + "</span><div class=\"wrapper\"><span class=\"moreMins mas\"></span><span class=\"lessMins menos\"></span></div><div class=\"wrapper\"><img class=\"gorde\" src=\"./icons/history.png\" /></div></div>";
    	
    	// El elemento del DOM que es el reloj.
    	var domElement = $("div#" + domElementId).get();
    	
    	// Asigna los valores de la hora y los minutos a las variables de instancia.
    	function getInputTimeValues(elem) {
    		// cogemos los datos del input
    		var value = $(elem).val();
    		// hacemos un split por ":"
    		var time = value.split(":");
    		
    		// parseamos los valores de la hora para ver si son correctos
    		var hour = parseInt(time[0],10);
    		if(!isNaN(hour)) {
    			// si la hora es válida, la asignamos
    			jQuery.fn.timepicker.hour = hour;
    		} else {
    			// si la hora no es válida, ponemos la hora actual
    			jQuery.fn.timepicker.hour = (new Date()).getHours();
    		}
    		
    		// parseamos los valores de los minutos para ver si son correctos
    		var minute = parseInt(time[1],10);
    		if(!isNaN(minute)) {
    			// si los minutos son válidos, los asignamos
    			jQuery.fn.timepicker.minute = minute;
    		} else {
    			// si los minutos no son válidos, ponemos los minutos actuales
    			jQuery.fn.timepicker.minute = (new Date()).getMinutes();
    		}
    	}
    	
    	// Asigna los valores de hora y minutos a los 'span' del reloj.
    	function assignTimeValues() {
    		$(domElement).find("span.hours").html(formatValue(jQuery.fn.timepicker.hour));
    		$(domElement).find("span.minutes").html(formatValue(jQuery.fn.timepicker.minute));
    	}
    	
    	// Función ejecutada en el evento 'mousewheel' del span de las horas.
    	function onMouseWheelHours(event,delta) {
    		$(this).html(getValue(this,delta,true));
    		$(jQuery.fn.timepicker.input).val(jQuery.fn.timepicker.hour + ":" + formatValue(jQuery.fn.timepicker.minute));
    	}
    	
    	// Función ejecutada en el evento 'mousewheel' del span de los minutos.
    	function onMouseWheelMinutes(event,delta) {
    		$(this).html(getValue(this,delta,false));
    		$(jQuery.fn.timepicker.input).val(jQuery.fn.timepicker.hour + ":" + formatValue(jQuery.fn.timepicker.minute));
    	}
    	
    	// Si no existe el elento del DOM del reloj, se crea y se añade al 'body'
    	// del documento. Además, se asigna a la variable de instancia que lo
    	// guarda. También asigna los eventos de 'mousewheel'.
    	if(domElement == null || domElement == undefined || domElement == '') {
    		$("body").append(htmlDomElement);
    		
    		domElement = $("div#" + domElementId).get();
    		
    		$(domElement).find("span.hours").mousewheel(onMouseWheelHours,onMouseWheelHours,true);
    		
    		$(domElement).find("span.minutes").mousewheel(onMouseWheelMinutes,onMouseWheelMinutes,true);
    	}
    	
    	// Para cada elemento se asignan los eventos
    	this.each(function() {
    		// Cuando el componente recibe el foco
    		$(this).focus(function() {
    			jQuery.fn.timepicker.input = this;
    			getInputTimeValues(this);
    			assignTimeValues();
    			var offset = $(this).offset();
    			var height = $(this).height();
    			//$(domElement).css('z-index','12000').css("position","absolute").css("top",(offset.top + height + 5) + "px").css("left",offset.left + "px");
//    			$(domElement).css("position","absolute").css("top",(offset.top + height + 5) + "px").css("left",offset.left + "px");
    			$(domElement).css("position","absolute").css("top",(offset.top) + "px").css("left",offset.left + "px");
    			$(domElement).show();
    			$("*[class^='jqi']:not(.timepicker)", $('.jqifade') ).mousewheel(function(){return false;},function(){return false;},true);
    			$("*[class^='jqi']:not(.timepicker)", $('#jqi') ).mousewheel(function(){return false;},function(){return false;},true);
        		$('body').css('overflow','hidden');
    		});
    		
    		// Cuando el componente pierde el foco
    		$(this).blur(function() {
    			jQuery.fn.timepicker.input = null;
    			$(domElement).hide();
    			$("*[class^='jqi']:not(.timepicker)", $('.jqifade') ).mousewheel(function(){return true;},function(){return true;},true);
    			$("*[class^='jqi']:not(.timepicker)", $('#jqi') ).mousewheel(function(){return true;},function(){return true;},true);
        		$('body').css('overflow','auto');
        		//console.log(onClose);
        		if (typeof onClose =="function"){
        			
        			onClose($(this));
        		}
    		});
    		
    		$('.gorde').unbind().bind('click',function() {
    			
    			jQuery.fn.timepicker.input = null;
    			$(domElement).hide();
    			$("*[class^='jqi']:not(.timepicker)", $('.jqifade') ).mousewheel(function(){return true;},function(){return true;},true);
    			$("*[class^='jqi']:not(.timepicker)", $('#jqi') ).mousewheel(function(){return true;},function(){return true;},true);
        		$('body').css('overflow','auto');
        		//console.log(onClose);
        		if (typeof onClose =="function"){
        			
        			onClose($(this));
        		}
    		});
    		
    		
    		// Cuando el componente cambia (tecla presionada)
    		$(this).keyup(function() {
    			getInputTimeValues(this);
    			assignTimeValues();
    		});
    		
    		var Interval = false;
    		
    		$('span.moreHours , span.lessHours', $(domElement) ).unbind().bind('mouseover',function(){
    			var mn = $(this).is('.moreHours') ? '+1':'-1';
    			var offset = 300; 
    			Interval = window.setInterval(function(){
    				$(domElement).find("span.hours").html(getValue($(domElement).find("span.hours"),mn,true));
    				$(jQuery.fn.timepicker.input).val(jQuery.fn.timepicker.hour + ":" + formatValue(jQuery.fn.timepicker.minute));
    				offset = offset - 20;
    			},offset);
    			return false;
			}).bind('mouseout',function(){window.clearInterval(Interval);});
    		
    		$('span.moreMins , span.lessMins', $(domElement) ).unbind().bind('mouseover',function(){

    			var mn = ( ($(this).is('.moreMins')) ? '+1':'-1');
    			
    			
    			
    			var offset = 250;

    			Interval = window.setInterval(function(){
    				$(domElement).find("span.minutes").html(getValue($(domElement).find("span.minutes"),mn,false));
    				$(jQuery.fn.timepicker.input).val(jQuery.fn.timepicker.hour + ":" + formatValue(jQuery.fn.timepicker.minute));

    			},400);
    			return false;
			}).bind('mouseout',function(){window.clearInterval(Interval);});
    		
    	});
    
        return this;
    }
})(jQuery);
