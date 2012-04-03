var KARMA_LANG = "es";


var literales = {
			"Nuev":[{"es":"Nuev","en":"New","eu":"Gehitu"}],
			"Si":[{"es":"Si","en":"Yes","eu":"Bai"}],
			"Ok":[{"es":"Ok","en":"Ok","eu":"Ok"}],
			"No":[{"es":"No","en":"No","eu":"Ez"}],
			"Crear_nuev":[{"es":"Crear nuev","en":"Create new","eu":"berria sortu"}],
			"Guardar":[{"es":"Guardar","en":"Save","eu":"Gorde"}],
			"Cancelar":[{"es":"Cancelar","en":"Cancel","eu":"Ezeztatu"}],
			"eliminar":[{"es":"Eliminar","en":"Remove","eu":"Ezabatu"}],
			"deseliminar":[{"es":"Deshacer Eliminar","en":"Undo Remove","eu":"Ezabatu Desegin"}],
			"clickagrandar":[{"es":"click para agrandar","en":"Click to make it bigger","eu":"Egin klik handitzeko"}],
			"seguro_eliminar":[{"es":"¿Seguro que desea eliminar %var%?","en":"Are you sure you want remove %var%?","eu":"%var% ezabatu nahi duzu?"}],
			"seguro_eliminar_descendientes":[{"es":"Al eliminar %var% eliminará también<br/>todas sus relaciones y/o descendientes.<br/>¿Seguro que desea eliminar %var%?","en":"If you delete %var% all its relations<br/>and/or descendant information will be deleted too.<br/>Are you sure you want remove %var%?","eu":"%var% ezabatuz gero, bere erlazio eta/edo<br/>ondorengo informaizo guztia ere ezabatuko da.<br/>%var% ezabatu nahi duzu?"}],
			"seguro_deseliminar":[{"es":"¿Seguro que desea deshacer eliminar %var%?","en":"Are you sure you want undo remove %var%?","eu":"%var% ezabatu, desegin nahi dozu?"}],
			"seguro_deseleccionar":[{"es":"¿Desea deseleccionar todas las filas?","en":"Are you sure you want unselect all rows?","eu":"Lerro guztiak desaukeratu nahi dozu?"}],
			"eliminar_filas":[{"es":"¿Desea eliminar las siguientes filas?","en":"Do you want remove this rows?","eu":"Lerro hauek ezabatu nahi dozuz?"}],
			"eliminar_fila":[{"es":"¿Desea eliminar la siguiente fila?","en":"Do you want remove this row?","eu":"Lerro hau ezabatu nahi dozu?"}],
			"valores_no_coinciden":[{"es":"los valores no coinciden","en":"The values are not equals","eu":"Ez dira bardinak"}],
			"Nuevo_ZIP":[{"es":"Nuevo Zip de ","en":"New Zip of","eu":"Zip Berria"}],
			"regenerar_cache":[{"es":"Regenerar Cache","en":"Regenerate Cache","eu":"Katxea berregin"}],
			"no_filas_sel":[{"es":"No hay filas seleccionadas.","en":"There are not files selected.","eu":"Ez dago errenkadarik aukeratuta."}],
			"dobleclick":[{"es":"dobleclick para editar","en":"DoubleClick to edit","eu":"klik Bikoitza editatzeko"}],
			"seleccionatodo":[{"es":"Selecciona todos.","en":"Select All.","eu":"Dana aukeratu."}],
			"selecciona_ops":[{"es":"Selecciona opciones.","en":"Select Options.","eu":"aukera hartu."}],
			"seleccionado":[{"es":"seleccionado.","en":"selected.","eu":"aukeratuta."}],
			"duplicate":[{"es":"La fila ya está seleccionada.","en":"Row already selected.","eu":"Lerroa iadanik badago hautatua."}],
			"reloading":[{"es":"Recargando...","en":"Reloading...","eu":"Birkargatzen..."}],
			"seguro_salir":[{"es":"Algunos campos no están salvados.","en":"Some fields have not been saved","eu":"Eremu batzuk ez dira gorde!"}]
			
};

function str_replace(busca, repla, orig)
{
	str 	= new String(orig);

	rExp	= "/"+busca+"/g";
	rExp	= eval(rExp);
	newS	= String(repla);

	str = new String(str.replace(rExp, newS));

	return str;
}


var _l = {
		setLang : function(l){
			KARMA_LANG = l;
		},
		g : "",
		literal : function(l,g){
			if (g!=false) {
				this.g = g;
			}
			if (indice = this.exist(l)){
				if (retval = eval("literales."+l+"[0]['"+KARMA_LANG+"']")){
					return this.freturn(retval);
				}else{
					//console.log("['KarmaLiterales'] No se ha encontrado el literal "+l+" traducido.");
					return this.freturn(l);
				}
			}else{
				//console.log("['KarmaLiterales'] No existe el literal "+l+".");
				return this.freturn(l);
			}
		},
		literalstr : function(l,g,ll){
			switch (KARMA_LANG){
				case "eu": 
					str = ll+" "+this.literal(l,g);
				break;
				default: 
					str = this.literal(l,g)+" "+ll;
				break; 
			}
			return str;
		},	
		literalvar : function(l,v){
			str = str_replace('%var%',v,this.literal(l));
			return str;
		},				
		exist : function(l){
			if (lit=eval("literales."+l)) return true;
			else return false;
		},
		freturn : function(l){
				if (KARMA_LANG=="es"&&this.g) {
					return l+this.g;
				}
				return l;
		}
};

var _ = function (l,g){
	return _l.literal(l,g);
};
var _str = function (l,g,ll){
	return _l.literalstr(l,g,ll);
};
var _var = function (l,v){
	return _l.literalvar(l,v);
};

$(document).ready(function(){

	 _l.setLang($('#KARMA_LANG').val());
});
