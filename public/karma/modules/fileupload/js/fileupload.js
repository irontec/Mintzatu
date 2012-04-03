$(document).ready(function() {

    var tForm = $('#uploadIt').parents("form:eq(0)");
    var ext = $("input[name=ext]",tForm).val().split(",");
    var tElem = $("#uploadIt");

    var addMsg = function(msg,type,oLi) {
        if (typeof oLi == 'undefined') {
            alert(msg);
        } else {
            var targetLi = $(oLi);
        }
        if ( (typeof msg =='object') && msg[0] && msg[1] ) {
            type = msg[0];
            msg = msg[1];
        }

        var cName = '';
        
        switch(type) {
            case "error":
            case "warn":
            case "ok":
            case "question":
            case "info":
                cName = type;
            break;                    

        }
        
        targetLi.append("<p class='"+cName+"'>"+msg+"</p>");
        
    };

    var processPostProccesor = function(json,$li) {

        if ($("p:last",$li).hasClass("info")) $("p:last",$li).remove();

        if (typeof json.ret == 'string') {
            addMsg(json.ret, json.success,$li);
        } else {
            for(var i in json.ret) {
                addMsg(json.ret[i], json.success,$li);
            }   
        }

        if (typeof json.ops == 'object') {
            var query = '';
            for(var indice in json.ops) {
                query += '<a href="" class="token" rel="'+json.ops[indice]+'">'+indice+'</a>';
            }
            if (query == '') return;
            if ($("p.question",$li).length == 0) {
                addMsg(query, 'question',$li);
            } else {
                $("p.question:last",$li).append(query);
            }

            $("a.token",$li).one('click',function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).parent("p").slideUp(function() { $(this).remove();});
                callPostProccesor($li,$(this).attr("rel"));
            });
        }
    };

    var callPostProccesor = function(oLi,token) {
        $li = $(oLi);
        
        var data = {
            postProcess : true,
            fname : $li.data("fname"),
            hash : $li.data("hash")
        };
        
        if (typeof token != 'undefined') {
                data.token = token;
        }
        addMsg("<span class='qq-upload-spinner'></span>Procesando (Puede tardar varios minutos)",'info',$li);
        $.ajax({
            url:tForm.attr("action"),
            dataType:'json',
            cache : false,
            data : data,
            type : 'GET',
            success: function(j) {
                processPostProccesor(j,$li);
            }
        });
    };

    var uploader = new qq.FileUploader({
        element: tElem[0],
        action: tForm.attr("action"),
        params: {upload:true},
        allowedExtensions: ext,
        sizeLimit: parseInt($("input[name=max]",tForm).val()),
        minSizeLimit:parseInt($("input[name=min]",tForm).val()),
        debug: false,
        multiple: false,
        onSubmit : function() {},
        onComplete: function(id, fileName, responseJSON){
            // viene con el error procesadito.... mu rico, very rich.
            var oLi = uploader._getItemByFileId(id);
            
            
            if ($("input[name=toBeProcessed]",tForm).val() == '1') {
                $(oLi).data("fname",responseJSON.name).data("hash",responseJSON.hash);
                addMsg("Fichero subido Correctamente. Comenzando el procesamiento.","warn",oLi)
                callPostProccesor(oLi);
        
            } else {
                addMsg("Fichero subido Correctamente.","ok",oLi)
            }
            

        },
        onCancel: function(id, fileName){
            addMsg("Subida cancelada","error");
        },
        messages: {
            typeError: "{file} no tiene una extensión válida. Solo se permiten las siguientes extensiones: {extensions}",
            sizeError: "{file} es demasiado grande. El tamaño máimo es {sizeLimit}.",
            minSizeError: "{file} es demasiado pequeño. El tamaño mínimo es {minSizeLimit}.",
            emptyError: "{file} está vacío. Por favor, selecciona un fichero válido.",
            onLeave: "Se están subiendo ficheros, si abandonas la página, la subida se cancelará."            
        },
        showMessage: function(message,oLi){
            addMsg(message,"error",oLi);
        },
        template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-drop-area"><span>Arrastra los ficheros hasta aquí para comenzar la subida.</span></div>' +
                '<div class="qq-upload-button">'+$("input[name=action]",tForm).val()+'</div>' +
                '<ul class="qq-upload-list"></ul>' + 
             '</div>',
        fileTemplate: '<li>' +
                '<span class="qq-upload-file"></span>' +
                '<span class="qq-upload-spinner"></span>' +
                '<span class="qq-upload-size"></span>' +
                '<a class="qq-upload-cancel" href="#">Cancelar</a>' +
                '<span class="qq-upload-failed-text">Error</span>' +
            '</li>',         
    });           



});
