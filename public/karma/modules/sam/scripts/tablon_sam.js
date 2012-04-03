/**
 Requires: 
 	- ui.tabs
 	- jquery-impromptu

 */

function select_permisos(){
	/*var inp = document.getElementById("auto_compl_permisos");

	inp.onkeyup = function() {
		var sele = document.getElementById("permisos");
		var re = new RegExp(inp.value,"i");
		for (var i=0;(op = sele.getElementsByTagName("option")[i]);i++) {
			if (op.innerHTML.match(re)) {
				sele.selectedIndex=i;
				op.setAttribute("class","shown");
			} else {
				op.setAttribute("class","hidden");
			}
		}


	};*/
	var inp = $("#auto_compl_permisos");
	inp.keyup(function() {
		var sele = $("#permisos");
		var re = new RegExp(inp.val(),"i");
		var texto = inp.val();
		window.setTimeout(function(){
			if(texto == inp.val()){
				if(texto == ""){
					sele.children("option").attr("selected","");
					sele.children("option").attr("class","shown")
				}else{
					sele.children("option").each(function(i, elem){
						if($(elem).text().match(re)){
							$(elem).attr("class","shown")
							$(elem).attr("selected","selected");
						}else {
							$(elem).attr("class","hidden");
							$(elem).attr("selected","");
						}
					});
				}
			}
		},150);
	});
}
function gestion_permisos(id,tab,fld){
	var contenido = '\
	<div id="perms" tab="'+tab+'" fld="'+fld+'">\
		<table>\
			<thead>\
			<tr>\
				<th><acronym title="Permisos">P</acronym></th><th><acronym title="Ambito">A</acronym></th><th><acronym title="Denominación">Denominación</acronym></th><th><acronym title="Padres">P</acronym></th><th><acronym title="Hijos">H</acronym></th><th><acronym title="Acción">Acc.</acronym></th>\
			</tr>\
			</thead>\
			<tbody>\
			</tbody>\
		</table>\
		<a href="#" id="nuevosPermisos" onclick=\'javascript:abrir_popup_permisos('+id+',"#jqi")\'>Añadir permiso</a>\
	</div>';

	$.prompt(contenido,{showClose:true});
	
	actualizar_permisos(id,tab,fld);
}

function abrir_popup_permisos(id, contenedor){
	var contenido = '\
		<div id="div_tipos_permiso">\
			<div id="tipo_permiso">\
				<label>Ámbito:</label>\
				<select id="tipo_perm" name="tipo_perm">\
					<option value="">--Seleccionar--</option>\
					<option value="nodo">Permiso por Nodo</option>\
					<option value="nivel">Permiso por Nivel</option>\
					<option value="gNodos">Permiso por Grupo de Nodos</option>\
					<option value="gPersonas">Permiso por Grupo de Personas</option>\
				</select>\
			</div>\
			<label for="auto_compl_permisos">Buscar permiso:</label> <input type="text" id="auto_compl_permisos"/>\
			<div id="lista_permisos">\
				<select id="permisos" size="10" multiple="multiple" name="permisos">\
				</select>\
			</div>\
		</div>\
		<div id="propiedades_permiso">\
			<p>Acción:</p>\
			<ul>\
				<li><input id="negado" name="negado" type="radio" checked="checked" value="0"/> <label>Permitir:</label></li>\
				<li><input id="negado" name="negado" type="radio" value="1"/> <label>Denegar:</label></li>\
			</ul>\
			<input id="id_gestionado" type="hidden" value="'+id+'"/>\
		</div>\
		';
	$.prompt(contenido,{
		prefix: "perm_window", 
		container: contenedor,
		callback: anadir_permisos,
		buttons: {
			Ok: true,
			Cancel: false
		}
	});
	$("#tipo_perm").change(function(){
		padre_hijos = '\
			<div id="propiedades_nodo">\
				<p>Aplicar también a:</p>\
				<ul>\
					<li><input id="padres" name="padres" type="checkbox"/> <label>Nodos Padre:</label></li>\
					<li><input id="hijos" name="hijos" type="checkbox"/> <label>Nodos Hijo:</label></li>\
				</ul>\
			</div>\
		';
		if(typeof($("#tipo_perm").attr("value")) != "undefined"){
			dibujar_opciones_permisos($("#tipo_perm").attr("value"), "#lista_permisos");
			if($("#tipo_perm").attr("value") == "nodo"){
				$("#propiedades_permiso").append(padre_hijos);
			}else{
				$("#propiedades_nodo").remove();
			}
			
		}
		else{
			$("#permisos").empty();
		}
	});
	select_permisos();
}

function arbolOpciones(data,espacios){
	contenido = "";
	
	$.each(data, function(i,permiso){
		contenido += '<option value="'+permiso.id+'" class="shown">'+espacios+"-"+permiso.iden+'</option>';
		if (permiso.hijos){
			contenido += arbolOpciones(permiso.hijos, espacios+"&nbsp;&nbsp;&nbsp;&nbsp;");
		}
	});
	
	return contenido;
}

function dibujar_opciones_permisos(tipoPermiso, contenedor){
	var opts = {op:'get_permissions',type:tipoPermiso};
	$(contenedor).empty();
	$(contenedor).append('<div class="cargando"> <img alt="cargando..." src="icons/loader.gif" /> Cargando lista de permisos...</div>');
	$.getJSON("./modules/sam/ajax/ops_sam.php",opts,function(j) {
		if (j.error==0) {
			var contenido = '<select id="permisos" name="permisos" multiple="multiple" size="10">';
			if (tipoPermiso == 'nodo') contenido += arbolOpciones(j.permisos,"");
			else{
				$.each(j.permisos, function(i,permiso){
					contenido += '<option value="'+permiso.id+'" class="shown">'+permiso.iden+'</option>'; 
				});
			}
			contenido += '</select>';
		} else {
			var contenido = "Error recuperando permisos ["+j.error+"]"; 
		}
		
		$(contenedor).empty();
		$(contenedor).append(contenido);
	});
}


function actualizar_permisos(id,tab,fld) {
	var opts = {op:'get_entity_permissions',tab:tab,fld:fld,id:id};
	$.getJSON("./modules/sam/ajax/ops_sam.php",opts,function(j) {
		if (j.error==0) {
			$("#perms table tbody").empty();
			$.each(j.permisos, function(i,n){
				(n.negativo == 1)?img_neg="p_negativo.png":img_neg="p_positivo.png";
				switch(n.tipo){
					case "nodo":
						img_tipo="nodo.png";
						alt_tipo="Permiso de nodo";
						break;
					case "nivel":
						img_tipo="nivel.png";
						alt_tipo="Permiso de nivel";
						break;
					case "gNodos":
						img_tipo="grupo_nodos.png";
						alt_tipo="Permiso de grupo de niveles";
						break;
					case "gPersonas":
						img_tipo="grupo_personas.png";
						alt_tipo="Permiso de grupo de jerarquias";
						break;
					default:
						img_tipo="indef.png";
						alt_tipo="indef.";
				}
				tr = "<tr><td><img src='icons/"+img_neg+"' alt='"+img_neg+"' /></td><td><img src='icons/"+img_tipo+"' alt='"+img_tipo+"' title='"+alt_tipo+"' /></td><td>"+n.nombre+"</td>";
				tr += "<td><img src='./icons/"+((n.padres=="1")? "apply.png' alt='si'":"delete.png' alt='no'")+" /></td>";
				tr += "<td><img src='./icons/"+((n.hijos=="1")? "apply.png' alt='si'":"delete.png' alt='no'")+" /></td>";
				tr += "<td><img class='delete' src='./icons/eraser.png' iden='"+n.id_permiso+"' /></td></tr>";
				
				$("#perms table tbody:eq(0)").append(tr);
				
			});
			$("img.delete",$("#perms")).css({"cursor":"pointer","display":"inline"}).bind("click",function() {
				if (!confirm("¿Desea eliminar este permiso?")) return;					
				$.getJSON("./modules/sam/ajax/ops_sam.php",{op:'delete_permision',tab:tab,fld:fld,id_permiso:$(this).attr("iden")},function() {
						actualizar_permisos(id,tab,fld);
				});
			});
		}
	});
}

function anadir_permiso(id, id_permiso_sam, tipo, negado, padres, hijos){
	
	var opts = {op:'add_permission',tab:$("#perms").attr("tab"),fld:$("#perms").attr("fld"),id:id,type:tipo,negado:negado,padres:padres,hijos:hijos,id_permiso_sam:id_permiso_sam};
	$("#perms table tbody:eq(0)").html("<tr><td colspan='5'><img src='./icons/loader.gif' /></td></tr>");
	$.getJSON("./modules/sam/ajax/ops_sam.php",opts,function(j) {
		if (j.error==0) actualizar_permisos(id,$("#perms").attr("tab"),$("#perms").attr("fld"));
		else alert("[Error "+j.error+"]\n"+j.errorStr);
	});
}

function anadir_permisos(v,m){
	var padres = 0;
	var hijos = 0;
	if(v){
		var grupo = m.find("#tipo_perm").attr("value");
		if (grupo=="nodo") {
			padres = m.find("#padres:checked").length;
			hijos = m.find("#hijos:checked").length;
		}
		var id = m.find("#id_gestionado").attr("value");
		var negado = m.find("input[@name='negado']:checked").attr("value");
		$.each(m.find("#permisos option.shown:selected"), function(i,option){
				anadir_permiso(id,option.value,grupo,negado,padres, hijos);
		});
	}
}


$(document).ready(function(){

	$(".menuSamPerm").bind("click",function() {
		var re = new RegExp("tab\:([^\:]+)\:(.*)\:");
  		var m = re.exec($(this).attr("class"));
  		if (m == null) return true;
  		if (m.length!=3) return true;
  		var tab = m[1];
  		var fld = m[2];
  		re = new RegExp("=([0-9]+)$");
  		m = re.exec($(this).attr("href"));
  		if (m.length!=2) return true;
  		var id = m[1];
  		gestion_permisos(id,tab,fld);
		return false;
	});

});
