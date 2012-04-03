$(document).ready(function() {
	var upButton = $("#hdUpload");
	upButton.disableTextSelect();
	
	var loadingPanel = false;
	
	var startloadingPanel = function() {
		loadingPanel = true;
		$.prompt("<div id='hdKarma_carga' class='tablon'><table style='width:600px;'><caption>Cargando Ficheros</caption></table><div id='commonOps'><input type='checkbox' id='common' /><span>Mover todos</span><span id='commonDir'></span></div></div>",{
			draggable : false,
			buttons: {
				Subir: "true",
				Cancelar: "cancelar"
			},
			submit: function(v,m){
				switch(v) {
					case 'cancelar':
						$("#hdUpload").fileUploadClearQueue();
						loadingPanel = false;
					return true;
					case 'true':
						$("#hdKarma_carga table tr").each(function() {
							var idQueue = $(this).attr("id").replace(/^q_/,'');
							$("td.opts",$(this)).removeClass("eliminar_queue").addClass("progress").html("subiendo fichero...");
						});
						$("#hdUpload").fileUploadStart();
						$("#jqi_state0_buttonSubir").hide();
					return false;	
				};
			}		
		});
		
	};
	
	var addFileToPanel = function(q,f) {
		var byteSize = Math.round(f.size / 1024 * 100) * .01;
		var suffix = 'KB';
		if (byteSize > 1000) {
			byteSize = Math.round(byteSize *.001 * 100) * .01;
			suffix = 'MB';
		}
		var sizeParts = byteSize.toString().split('.');
		if (sizeParts.length > 1) byteSize = sizeParts[0] + '.' + sizeParts[1].substr(0,2);
		else byteSize = sizeParts[0];
		if (f.name.length > 20) fileName = f.name.substr(0,20) + '...';
		else fileName = f.name;
		$("#hdKarma_carga table").append("<tr id='q_"+q+"'><td class='fName'>"+fileName+"&nbsp;<span class='fsize'>("+byteSize + suffix+")</span></td><td class='porcentaje'>0%</td><td class='eliminar_queue opts'>Eliminar de la cola&nbsp;<img src='./icons/_delete.png' alt='Borrar este archivo'></td></tr>");
	};
	

	$("td.eliminar_queue").livequery("click",function() {
		if (confirm("¿Esta seguro de que desea no subir este fichero?")) {
			var oTR = $(this).parent("tr");
			idQueue = oTR.attr("id").replace(/^q_/,'');
			upButton.fileUploadCancel(idQueue);
			oTR.fadeOut("slow",function() {
				$(this).remove();
				if ($("#hdKarma_carga table tr").length == 0) {
					loadingPanel = false;
					$.prompt.close();
				}
			});
 			
		}
	
	});

	$("select.listadoDirs").livequery("change",function() {
		var val = $(this).val();
		var neww = /^new_([0-9]*)/;
		var rNew = neww.exec(val);
		if (rNew && rNew.length == 2) {
			$(this).hide();
			$(this).after("<input type='text' class='inp_nuevoDir' idpadre='"+rNew[1]+"' />");
			$(this).next("input").focus();			
		}
		
	});
	
	$("input.inp_nuevoDir").livequery("keypress",function(e) {
		switch(e.keyCode) {
			case 27: // ESC
				$(this).prev("select").show();
				$(this).remove();		
			return false;
			case 13: // ENTER
				var nCarpeta = $(this).val();
				var idPadre = $(this).attr("idpadre");
				$.get("./modules/hdKarma/ajax/ops_dirs.php",{op:'new',idpadre:idPadre,n:nCarpeta},function(r) {
					$(this).prev("select").show();
					$(this).remove();		
					if ($.trim(r)!="ok") alert($.trim(r));
				});
				loadSelects();					
			return false;
		}
	
	});


	$("#common").livequery("change",function() {
		if ($(this).is(":checked")) {
			$("#hdKarma_carga td.opts select").attr("disabled","disabled");
			$("#commonDir select").attr("disabled","");
		} else {
			$("#hdKarma_carga td.opts select").attr("disabled","");
			$("#commonDir select").attr("disabled","disabled");
		}
	
	});

	$("#showNewDirs").livequery("click",function() {
		$("#hdKarma_carga option.newdir").css("display","block");
	},function() {
		$("#hdKarma_carga option.newdir").css("display","block");
	});

	var updateProgress = function(e,q,f,d) {
		$("#q_"+q+" td.porcentaje").html(d.percentage+'%');
		$("#q_"+q+" td.fName").css("background-position",parseInt((300*d.percentage)/100)+'px 0');
	};

	var loadSelects = function() {
		$("#hdKarma_carga td.opts").load("./modules/hdKarma/ajax/ops_dirs.php?op=doCombo")
		$("#commonDir").load("./modules/hdKarma/ajax/ops_dirs.php?op=doCombo",function() {
			$("#commonOps select").attr("disabled","disabled");
		});
		
	}


	var doComplete = function(e,q,f,d) {
		// TODO -- coger el id del fichero de la vuelta, y pasarselo al ops_dirs
		$("#commonOps").slideDown("fast");
		
		loadSelects();
		
	};
	
	var AllComplete = function(e,d) {
		
	
	};
	
	var setAsError = function(e,q,f,d) {
		if (d.text == undefined) return false;
		$("#q_"+q+" td.opts").html("ERROR "+d.type+" ("+d.text+")");
		return false; 
	};
	
	
	upButton.fileUpload(
			{
            'uploader'	: './modules/hdKarma/swf/uploader.swf',
            'script'		: '/eventia/karma/modules/hdKarma/ajax/upload.php',
            'scriptAccess':'always',
				'scriptData': {'session_name': $.cookie('PHPSESSID')},
				'multi'		: true,
            'auto'		: false,
            'width'		: '30',
            'height'		: '24',
            'buttonImg'	: './icons/clip.png',
            'rollover'	: true,
            'wmode'		: 'transparent',
	         'onInit'		: function() {
	           		upButton.css("border","0").css("padding","0 0 2px 0").css("background","none").html(flashElement);
					return false;
				},
				'onProgress' : function(e,q,f,d) {
					updateProgress(e,q,f,d);
					return false;
				},
				'onSelect' : function(e,q,f) {
					if (!loadingPanel) startloadingPanel();
					addFileToPanel(q,f);	
					return false;
				},
				'onComplete': function(e,q,f,d) {
					doComplete(e,q,f,d);
					return false;
				},
				'onAllComplete' : function(e,d) {
					AllComplete(e,d);
					return false;
				},
				'onError' : function(e,q,f,d) {
					setAsError(e,q,f,d);
					return false;
				},
				'onCancel' : function(e,q,f,d) {
					setAsError(e,q,f,d);
					return false;
				},
				'onClearQueue' : function() {
					return false;
				}
			}
	);
	

});
