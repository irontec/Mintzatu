/**
 * Fichero javascript para la clase tabloncalendar,
 * 
 * 
 * @author Eider Bilbao <eider@irontec.com>
 * @version 1.0
 * @package karma
 **/
var temporizador = 0;
function addContextMenus(){
	var selectedDay = null;
	$("td[id^='tdDaysCell_']").contextMenu('myContextMenu', {
		bindingWay: 'like',
        menuStyle: {
          border: '1px solid #000',
          width: 'auto'
        },

        itemStyle : {
          border: 'none',
          padding: '3px',
          margin: 0
        },

        itemHoverStyle: {
          border: 'none'
        },
        
        onContextMenu: function(e) {
        	selectedDay = $(e.target);
        	return true;
        },
        
        onShowMenu: function(e, menu) {
        	var selD = $(e.target).parent("td");
        	if(selD.hasClass('conEvento') == false){
        		$("li[id^=vaciar_dia]", menu).remove();
        	}else{
        		$("li[id^=nuevo_evento]", menu).remove();
        	}
    		return menu;
    	},
        
        bindings: {
        	'ver_eventos_': function(t,ct, c) {
    			if(selectedDay.length>0){
        			mostarEventos(selectedDay,c);
        			return true;
        		}else{
        			return false;
        		}
      		},
			'nuevo_evento_': function(t,ct,c) {
      			if(selectedDay.length>0){
      				var fecha = selectedDay.attr("id");
        			nuevoEvento(fecha,c);
        			return true;
        		}else{
        			return false;
        		}
      		},
      		'vaciar_dia_': function(t,ct,c) {
      			if(selectedDay.length>0){
      				var fecha = selectedDay.attr("id");
        			vaciarDia(fecha,c);
        			return true;
        		}else{
        			return false;
        		}
      		}
        }

	});
}

function obtenerDiasConEventos(){
	var plt = $("#hddPlt").val();
	var datePrinc = $("#hddFldDatePrinc").val();
	var lavuelta;
	var thePlts = "";
	var theDatePrinc = "";
	//var condicion = "";
	var extradata = "";
	$(":hidden[id^='hddPlt_']").each(function(){
		var idPlt = $(this).attr("id").replace("hddPlt_","");
		thePlts += $(this).val()+"|";
		theDatePrinc += $("#hddFldDatePrinc_"+idPlt).val()+"|";
	});
	thePlts = thePlts.substring(0,thePlts.lastIndexOf("|"));
	theDatePrinc = theDatePrinc.substring(0,theDatePrinc.lastIndexOf("|"));
	if($("#idcond").length>0){
		var condicion = $("#"+$("#idcond").val()).val();
		extradata = "&condId="+condicion+"&condFld="+$("#idcond").val();
		
	}
	var opts =  "op=get_event_days";//&pl="+plt+"&dPrinc="+datePrinc";
	$.ajax({
		type: "POST",
		url: "./modules/tabloncalendar/ajax/ops_tabloncalendar.php",
		data: "op=get_event_days&pl="+thePlts+"&dPrinc="+theDatePrinc+extradata,
		dataType: "json",
		async: false,
		success: function(j){
			switch (j.error) {
				case 0:
					var aDays = new Array(); 
					for(var i=0; i<j.results.length;i++){
						var elem = j.results[i];
						aDays[i] = new Array();
						aDays[i][0] = elem.day;
						aDays[i][1] = elem.month;
						aDays[i][2] = elem.year;
					}
					lavuelta = aDays;
					break;
				default:
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},top:'2%',submit: function(v,m){ window.setTimeout(function(){$(self).parents("tr").removeClass('tmpOver');},5000); return true;}});
					lavuelta = false;
					break;
			}
		}
	});
	return lavuelta;
}



function mostarEventos(theDate){
	var plt = $("#hddPlt").val();
	var datePrinc = $("#hddFldDatePrinc").val();
	var fecha = theDate.attr("id");
	var modificado = false;
	var thePlts = "";
	var theDatePrinc = "";
	$(":hidden[id^='hddPlt_']").each(function(){
		var idPlt = $(this).attr("id").replace("hddPlt_","");
		thePlts += $(this).val()+"|";
		theDatePrinc += $("#hddFldDatePrinc_"+idPlt).val()+"|";
	});
	thePlts = thePlts.substring(0,thePlts.lastIndexOf("|"));
	theDatePrinc = theDatePrinc.substring(0,theDatePrinc.lastIndexOf("|"));
	var post_data = {
		op : 'ver_eventos',
		pl : thePlts,
		dPrinc : theDatePrinc,
		id : fecha,
		conds : []
	};
	$("#hiddenFields input:hidden").each(function(i) {
		post_data.conds.push({name:$(this).attr("name"),value:$(this).val()});
	});
	// data: "op=ver_eventos&pl="+thePlts+"&dPrinc="+theDatePrinc+"&id="+fecha+"&idCal="+$("#id_calendario").val(),
	$.ajax({
		type: "POST",
		url: "./modules/tabloncalendar/ajax/ops_tabloncalendar.php",
		data : post_data,
		dataType: "json",
		async: false,
		success: function(j){
			switch (j.error) {
				case 0:
					var listado ="";
					for(var i=0;i<j.results.length;i++){
						var elem = j.results[i];
						var fecha = elem.date;
						var plant = elem.plt;
						listado += '<tr>';
						var continuar = false;
						$.each(elem, function(j,item){
							switch(j){
								case 'id':
								case 'date':
								case 'plt':
									continuar = true;
									break;
								default:
									continuar = false;
									break;
							}
							if(continuar == false)	listado += '<td>'+item+'</td>';
						});
						listado += '<td><img src="./icons/delete.png" class = "delEvent" id="'+plant+'::'+elem.id+'"/></td></tr>';
					}
					var contenido = '<div id="events"><table><caption>'+j.titular+'</caption><thead><tr>';
					for(var i=0;i<j.cabeceras.length;i++){
						contenido += '<th>'+j.cabeceras[i]+'</th>';
					}
					contenido += '<th><acronym title="Eliminar evento">Elim.</acronym></th></thead><tbody>'+listado+'</tbody></table></div>';
					$.prompt(contenido,{
						loaded: function(){						
							$("img.delEvent").bind("click",function(){
								if (!confirm("¿Desea eliminar este evento?")) return;		
								var cual = $(this).attr("id");
								$.ajax({
									type: "POST",
									url: "./modules/tabloncalendar/ajax/ops_tabloncalendar.php",
									data: "op=delete_event&pl="+thePlts+"&dPrinc="+theDatePrinc+"&id="+cual,
									dataType: "json",
									async: false,
									success: function(j){
								//$.getJSON("./modules/tabloncalendar/ajax/ops_tabloncalendar.php",{op:'delete_event',id:cual, pl: plt, dPrinc: datePrinc},function(j) {
										switch (j.error) {
											case 0:
												$("img#"+cual).parent("td").parent("tr").remove();
												modificado = true;
												break;
											default:
												alert("Error Code ["+j.error+"]\n"+j.errorStr);
												break;
										}
									}
								});
								return false;
							});
						},
						submit: function(v,m){
							if(modificado)
								inicializarCalendario();
							return true;
						}
					});
					break;
				default:
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},top:'2%',submit: function(v,m){ window.setTimeout(function(){$(self).parents("tr").removeClass('tmpOver');},5000); return true;}});
					break;
			}
		}
	});
	return false;
}

function processNewSavingCalendar(js){
	switch(js.error) {
		case 0:
			inicializarCalendario();
			break;
		default:
			alert("Error Code ["+js.error+"]\n"+js.errorStr);
			break;
	}
}
function nuevoEvento(theDate,entidad){
	if($("#newonclick").val()=="true"){
		if($("#morethanonefield").val()=="true"){
			var plt = $("#hddPlt").val();
			var datePrinc = $("#hddFldDatePrinc").val();
			var fecha = theDate;
			var thePlts = "";
			var theDatePrinc = "";
			var aplt = entidad.split("::");
			thePlts = aplt[0];
			theDatePrinc = aplt[1];
			var opts = {op:'tablon',acc:'newfields',nodesdeTablon:'calendar',id:0, plCalendar:thePlts,idCalendar:fecha,dPrincCalendar:theDatePrinc};
			var oopts = {op:'tablon',acc:'new',nodesdeTablon:'calendar',id:0,plCalendar:thePlts,idCalendar:fecha,dPrincCalendar:theDatePrinc};
			$('#newInline').parents("li").trigger("click", [opts,oopts,processNewSavingCalendar]);
		}else{
			var fecha = theDate;
			var aplt = entidad.split("::");
			thePlts = aplt[0];
			theDatePrinc = aplt[1];
			var opts = {};
			opts['op'] = "newonclick";
			opts['plCalendar'] = thePlts;
			opts['idCalendar'] = fecha;
			opts['dPrincCalendar'] = theDatePrinc;
			$("#hiddenFields input[type=hidden]").each(function() {
				opts["COND__"+$(this).attr("name")] = $(this).val();
			})
			$.ajax({
				type: "POST",
				url: "./modules/tabloncalendar/ajax/ops_tabloncalendar.php",
				//data: "op=newonclick&plCalendar="+thePlts+"&idCalendar="+fecha+"&dPrincCalendar="+theDatePrinc,
				data: opts,
				dataType: "json",
				async: false,
				success: function(j){
					if(j.error!=0){
						$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},top:'2%',submit: function(v,m){ 
							window.setTimeout(function(){
								$(self).parents("tr").removeClass('tmpOver');
							},5000); 
							inicializarCalendario();
							return true;
						}});
					}else{
						$("#tdDaysCell_"+theDate).addClass('conEvento');
					}
				}
			});
		}
	}else{
		var plt = $("#hddPlt").val();
		var datePrinc = $("#hddFldDatePrinc").val();
		var fecha = theDate;
		var thePlts = "";
		var theDatePrinc = "";
		var aplt = entidad.split("::");
		thePlts = aplt[0];
		theDatePrinc = aplt[1];
		var opts = {op:'tablon',acc:'newfields',nodesdeTablon:'calendar',id:0, plCalendar:thePlts,idCalendar:fecha,dPrincCalendar:theDatePrinc};
		var oopts = {op:'tablon',acc:'new',nodesdeTablon:'calendar',id:0,plCalendar:thePlts,idCalendar:fecha,dPrincCalendar:theDatePrinc};
		$('#newInline').parents("li").trigger("click", [opts,oopts,processNewSavingCalendar]);
	}
}

function vaciarDia(theDate, entidad){
	var datos = entidad.split("::");
	var thePlts = datos[0];
	var fecha = theDate;
	var theDatePrinc = datos[1];
	var extradata = "";
	if($("#idcond").length>0){
		var condicion = $("#"+$("#idcond").val()).val();
		extradata = "&condId="+condicion+"&condFld="+$("#idcond").val();
	}
	if (!confirm("¿Desea eliminar todos los eventos de este dia?")) return;
	$.ajax({
		type: "POST",
		url: "./modules/tabloncalendar/ajax/ops_tabloncalendar.php",
		data: "op=vaciar_dia&id="+fecha+"&pl="+thePlts+"&dPrinc="+theDatePrinc+extradata,
		dataType: "json",
		async: false,
		success: function(j){
			switch (j.error) {
				case 0:
					//location.reload(true);
					//inicializarCalendario();
					$("#tdDaysCell_"+fecha).removeClass("conEvento");
					break;
				default:
					$.prompt("Error Code ["+j.error+"]\n"+j.errorStr,{buttons: {Ok: true},top:'2%',submit: function(v,m){}});
					break;
			}
		}
	});
	return false
}

function inicializarCalendario(){
	$.ajax({
		type: "POST",
		url: "./modules/tabloncalendar/ajax/ops_tabloncalendar.php",
		data: "op=redrawcalendar",
		dataType: "html",
		async: false,
		success: function(j){
			$("#tablonContainer").html(j);
			var eventDays;
			eventDays = obtenerDiasConEventos(eventDays);

			function formatEventDays(date) {
				$strReturn = "";
				if(typeof(eventDays) != "undefined"){
				    for (i = 0; i < eventDays.length; i++) {
				      if (date.getMonth() == (eventDays[i][1]-1)&& date.getDate() == eventDays[i][0] && date.getFullYear() == eventDays[i][2]){
				        return [true, "conEvento"];
				      }
				    }
				}
			    return [true, ''];
			}
			var selectedDay = null;
			var midatepickerr = $("#tabloncalendar").datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: false,
				changeYear: true,
				numberOfMonths: 12,
				showButtonPanel: true,
				changeFirstDay: false,
				onSelectUpdate: false,
				beforeShowDay: formatEventDays,
				onSelect: function(e){
					return false;
				},
				onChangeMonthYear: addContextMenus
			});
			addContextMenus();
		}
	});
	if($("#newonclick").val()=="true"){
		$("td[id^='tdDaysCell_']").bind('click',function(e){
			if($(this).hasClass('conEvento')){
				var $fecha = $(this).attr("id").replace("tdDaysCell_","");
				var $pltycampo = $("#hddFldDatePrinc_0").val();
				//nuevoEvento($fecha,$pltycampo);
				vaciarDia($fecha,$pltycampo);
			}else{
				var $fecha = $(this).attr("id").replace("tdDaysCell_","");
				var $pltycampo = $("#hddFldDatePrinc_0").val();
				nuevoEvento($fecha,$pltycampo);
			}
		});
	}
}

$(document).ready(function(){
	inicializarCalendario();
	
});
