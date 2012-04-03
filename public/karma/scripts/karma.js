
    var xtempX = 0
    var xtempY = 0

/*
 * Función que genera un strign aleatorio.
 * parámetros:
 *         ilen: largura deseada para el string generado
 *         level: posiciones del array de cadenas muestra de caracteres de las que se quieren obtener los caracteres del string final (separados por '_')
 *
 * retorno: string aleatorio de largura 'ilen'
 */
function randomString(ilen,level) {
    var validchars = new Array();
    validchars[1] = "0123456789";
    validchars[2] = "abcdfghjkmnpqrstvwxyz";
    validchars[3] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var niveles = level.split("_");
    var cadena = "";
    for(j=0;j<niveles.length;j++){
        cadena += validchars[niveles[j]];
    }
    var sRnd = '';
    for (var i=0; i<ilen; i++){
        var randomPoz = Math.floor(Math.random() * cadena.length);
        sRnd += cadena.substring(randomPoz,randomPoz+1);
    }
    return sRnd;
}

$.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return $.getUrlVars()[name];
  }

});


$(document).ready(function(){
    /*Mod de Gorka Rodrigo para desplegar la ayuda de la nueva karmaBar*/
    if ($('#ayudaContextual').length>0){
        var showMoreInfo = function(){
            $("#ayudaContextual").children(".helpContainer").find("#moreInfo").addClass('moreInfo_open');
            if($("#ayudaContextual").children(".helpContainer").find("#moreInfo").attr("strshow")){
                var txtNew = $("#ayudaContextual").children(".helpContainer").find("#moreInfo").attr("strshow");
                $("#ayudaContextual").children(".helpContainer").find("#moreInfo").text(txtNew);
            }
            $("#ayudaContextual").children(".helpContainer").find("#hiddenInfo").show();
        };
        var hideMoreInfo = function(){
            $("#ayudaContextual").children(".helpContainer").find("#moreInfo").removeClass('moreInfo_open');
            if($("#ayudaContextual").children(".helpContainer").find("#moreInfo").attr("strhide")){
                var txtNew = $("#ayudaContextual").children(".helpContainer").find("#moreInfo").attr("strhide");
                $("#ayudaContextual").children(".helpContainer").find("#moreInfo").text(txtNew);
            }
            $("#ayudaContextual").children(".helpContainer").find("#hiddenInfo").hide();
        };
        var showHelp = function(){
            $('#ayudaContextualIcon').addClass('ayudaContextual_open');
            $('#ayudaContextual').show();
        };
        var hideHelp = function(){
            $('#ayudaContextualIcon').removeClass('ayudaContextual_open');
            $('#ayudaContextual').hide();
        };

        $('#ayudaContextualIcon').bind('click',function(){
            if ($(this).hasClass('ayudaContextual_open')){
                hideHelp();
            }else showHelp();
        });

        $('#ayudaContextualIconCerrar').bind('click',function(){
                hideHelp();
         });

        if($("#ayudaContextual").children(".helpContainer").find("#moreInfo").length>0){
            $("#ayudaContextual").children(".helpContainer").find("#moreInfo").bind('click',function(){
                if($(this).hasClass("moreInfo_open")){
                    hideMoreInfo();
                }else{
                    showMoreInfo();
                }
            });
        }

        if($("#ayudaContextualIcon").hasClass("startOpened")){
            showHelp();
        }

	/* Kaian */
	$('#select_grupo_vinculado').bind('change', function(){
		var _form = $("<form />").attr({method:'post',action:window.document.location});
		$("<input />")
			.attr({type:'hidden',name:'empresa'})
			.val($(this).val())
			.appendTo(_form);

		_form.appendTo($("body"));
		_form.trigger("submit");

		return;		
	});

        /*$('#moreInfo').bind('click',function(){
            $(this).slideUp('fast');
            $('#ayudaContextualHidden').slideDown('fast');
        }).css('cursor','pointer');*/

    }
    /*Fin del Mod para la ayuda contextual de karmaBar*/




    if ($('#HelpBox').length>0){

        var showH = function(){
            $('#HelpIcon').addClass('open');
            $('#HelpBox').show();
        };
        var hideH = function(){
            $('#HelpIcon').removeClass('open');
            $('#HelpBox').hide();
        };

        $('#HelpIcon').bind('click',function(){
            if ($(this).attr('class')=='open'){
                hideH();
            }else{
                 showH();
             }
         }).Tooltip({
        track: true,
        delay: 450,
        showURL: false,
        showBody: " - ",
        opacity: 0.85
        });

        $('#moreInfo').bind('click',function(){
            $(this).slideUp('fast');
            $('#hiddenInfo').slideDown('fast');
        }).css('cursor','pointer');

    }


    var fpon = false;
    var exp_reg = /.*\?op\=([^$\&]*)/;
    var cadena = document.location;
    var m="";
    if ( m = exp_reg.exec(cadena)){
        var loc = m[1];
    }else{
        var loc = "default";
    }

    var menu_type = 0;

    var menu_hiddeable = 1;

    var mtout;

    var qopen;

    var fullPage = {
        on: function(){
                fpon = true;
                if ($.cookie(loc)==null) $.cookie(loc, 'On', { path: '/', expires: 1 });


                $('#karmaBarVMenu').show();


                $('#fpbo').attr('src','icons/window_nofullscreen.png');
                $('#karmaBar').css('padding-top',"2px");
                $('#karmaBar').css('padding-bottom',"0px");
                $('#karmaBarLogo img').css('height',"24px");
                $('#karmaBarOpts').css('padding-top',"9px");


                //$('#newMenu').hide();


                $('#quickMenu').hide();


                if (menu_type==0){
                    $('#newMenu').animate({ width: "hide", height: "hide"}, 790);
                    $('#main, #mainHeader').animate({ marginLeft: "0px"}, 1100 ,function(){
                        $('#cajaUsuario').animate({    width: "hide", height: "hide" },790);
                    });
                }

                if (menu_type==1){
                    $('#quickMenu').hide();
                    $('#main, #mainHeader').animate({ marginLeft: "0px"}, 100 ,function(){
                        $('#cajaUsuario').animate({    width: "hide", height: "hide" },790);
                    });
                }


                qopen=false;


        },
        faston: function(){
                fpon = true;
                if ($.cookie(loc)==null) $.cookie(loc, 'On', { path: '/', expires: 1 });

                $('#karmaBarVMenu').show();

                $('#karmaBar').css('padding-top',"2px").css('padding-bottom',"0px");
                $('#karmaBarLogo img').css('height',"24px");
                $('#karmaBarOpts').css('padding-top',"9px");
                $('#main, #mainHeader').css('margin-left','0px');


                $('#newMenu').hide();

                $('#quickMenu').hide();


                $('#cajaUsuario').hide();
                $('#fpbo').attr('src','icons/window_nofullscreen.png');

                qopen=false;

        },
        off: function(){
                fpon = false;
                $.cookie(loc, null, { path: '/' });

                $('#quickMenu').hide();
                $('#karmaBarVMenu').hide();



                $('#cajaUsuario').animate({ height: "show"},790);

                if (menu_type==0){
                    $('#newMenu').animate({width: "show",height: "show"}, 1100);
                    $('#main, #mainHeader').animate({marginLeft: "250px"}, 1100);
                }

                if (menu_type==1){
                    $('#quickMenu').show();
                    $('#main, #mainHeader').animate({marginLeft: "50px"}, 100);
                }


                $('#fpbo').attr('src','icons/window_fullscreen.png');


        }
    };




    var schmenu = $(".change_menu");



    function default_screen(){

        $('#quickMenu').hide();
        $('#newMenu').fadeIn();
        $('#main, #mainHeader').animate({marginLeft: "250px"}, 100,function(){ });

    }

    function full_screen(){

        $('#newMenu').fadeOut("fast",function(){
            $('#quickMenu').show();
            $('#main, #mainHeader').animate({marginLeft: "50px"}, 200,function(){ });
        });

    }

    schmenu.bind("click",function(){

        if (menu_type==0){
            menu_type = 1;

            $.cookie(loc+"mt", menu_type, { path: '/', expires: 1 });

            schmenu.addClass("selected");
            full_screen();


            return false;
        }

        if (menu_type==1){
            menu_type = 0;

            $.cookie(loc+"mt", menu_type, { path: '/', expires: 1 });
            schmenu.removeClass("selected");
            default_screen();

            return false;
        }

    });


    if ($.cookie(loc+"mt")!=null){

        if($.cookie(loc+"mt")==1){

            menu_type=0;
            menu_type = 1;

            $.cookie(loc+"mt", menu_type, { path: '/', expires: 1 });

            schmenu.addClass("selected");

            $('#newMenu').hide();

            $('#quickMenu').show();

            $('#main, #mainHeader').css('margin-left',"50px");


        }

    }



    $('#fpbo').bind('click',function(){
            if (fpon==false){
                fullPage.on();
            }else{
                fullPage.off();
            }
    }).Tooltip({
        track: true,
        delay: 450,
        showURL: false,
        showBody: " - ",
        opacity: 0.85
    });

    $('.tooltip').Tooltip({
        track: true,
        delay: 450,
        showURL: false,
        showBody: " - ",
        opacity: 0.85
    });



    if ($.cookie(loc)!=null){

//        fullPage.faston();
        fpon = true;
        $('#main, #mainHeader').css('margin-left','0px');
        $('#newMenu').hide();
        $('#quickMenu').hide();
        $('#cajaUsuario').hide();
        $('#fpbo').attr('src','icons/window_nofullscreen.png');
    }


        if ($.getUrlVar('embed')) { /* con parametro embed=true quitamos dejamos sólo el mainInner */
            /* TODO duplicado de faston, quiero pasar de cookie*/
            $('#karmaBar').hide();
            $('#main, #mainHeader').css('margin-left','0px');
            $('#newMenu').hide();
            $('#cajaUsuario').hide();
            $('#fpbo').attr('src','icons/window_nofullscreen.png');
            /* Otras cosas */
            $('#mainInner').css('padding','0 0 40px');
            $('#main h1:first, #mainHeader').hide();
            $('#pieDeKarma').hide();
            $('#quickMenu').hide();
            $('#newMigas').hide();
            $('#optsTablon').hide();
            $('.backButton').hide();
        }



    var F = false;
    var P = false;



    $(document).bind("keydown",function(event) {
        switch(event.which) {
            case 70:
                F = true;
            break;
        }
    });
    $(document).bind("keyup",function(event) {
        switch(event.which) {
            case 70:
                F = false;
            break;
        }
    });

    $(document).bind("keypress",function(event) {
        switch(event.which) {
            case 112:
                if (F==true){
                    if (fpon==false){
                        fullPage.on();
                        F = false;
                        P = false;
                    }else{
                        fullPage.off();
                        F = false;
                        P = false;
                    }
                }
            break;
        }
    });

    if($("h1#autoFullPage").length > 0 
        && $('#fpbo').attr('src') === './icons/window_fullscreen.png') {
        $('#fpbo').trigger('click');
    }

	

	
   $("ul:has(a.selected)",$("#newMenuUl, #quickMenuUl")).show().parent("li").children("img.more").attr("src","./icons/same.png");

    function menuHandler(event){
        //console.log(typeof event.target);
        var $target = $(event.target);
        if( $target.is("a") ){
            return true;
        }
        event.stopPropagation();
        if ($(this).hasClass('clicked')){
        	
            if ($(this).find('a.selected').length<1){
                $(this).children("ul").slideUp();
                $(this).children("img.more").attr("src","./icons/more.png");
                $(this).removeClass('clicked');
            }
        }else{
            $(this).addClass('clicked');
            if ($(this).find('a.selected').length<1)
                $(this).children("img.more").attr("src","./icons/less.png");
            $(this).children("ul").slideDown();
        }
        $("#newMenuUl, #quickMenuUl").children("li").not($(this)).each(function(){
            if ($(this).find('.selected').length>0){

            } else{
                $(this).children("ul").slideUp();
                $(this).children("img.more").attr("src","./icons/more.png");
                $(this).removeClass('clicked');
            }


        });
    }

    $("#newMenuUl, #quickMenuUl").children("li").bind('click',menuHandler);

    //$("#newMenuUl").children("li").children().unbind('click',menuHandler);

    if ($("#errorLog").html()!="") {

        if ($("#errorLog p.e_ko").length>0) {
            $("#erroresPendientes").show().bind("click",function() {
                $("#errorLog p.e_ok, #errorLog p.e_koerror").hide();
                $("#errorLog p.e_ko").show();
                $.prompt($("#errorLog").html(),{buttons:{Cerrar:true}});
            });
        }

        if ($("#errorLog p.e_koerror").length>0) {
            $("#megaErroresPendientes").show().bind("click",function() {
                $("#errorLog p.e_ok, #errorLog p.e_ko").hide();
                $("#errorLog p.e_koerror").show();
                $.prompt($("#errorLog").html(),{buttons:{Cerrar:true}});
            });
        }

        if ($("#errorLog p.e_ok").length>0) {
            $("#mensajesPendientes").show().bind("click",function() {
                $("#errorLog p.e_ko, #errorLog p.e_koerror").hide();
                $("#errorLog p.e_ok").show();
                $.prompt($("#errorLog").html(),{buttons:{Cerrar:true}});
            });
        }

        $.prompt($("#errorLog").html(),{buttons:{Cerrar:true}});
    }

    $(' #kmenu a, p.errorLog, #erroresPendientes, #mensajesPendientes, #megaErroresPendientes').Tooltip({
        track: true,
        delay: 450,
        showURL: false,
        showBody: " - ",
        opacity: 0.85
    }).css("cursor","help");
    $(' #newMenuUl a, p.head, span.change_menu ' ).Tooltip({
        track: true,
        delay: 450,
        showURL: false,
        showBody: " - ",
        opacity: 0.85
    });

    $('#quickMenuUl li li a' ).Tooltip({
        track: true,
        delay: 150,
        showURL: false,
        showBody: " - ",
        opacity: 0.85
    });


    $('.head:last').css('border-bottom','0px');


    var green = function(){$('#main h1, #mainHeader h1').addClass('s');$('a.selected').addClass('ss');};
    var red = function(){$('#main h1, #mainHeader h1').removeClass('s');$('a.selected').removeClass('ss');};
    var puff = function(){$('#newMenuUl a').bind('click',function(){ $(this).parents('li').hide("puff", {}, 800);return false; });$('.opts').bind('click',function(){ $(this).parents('table , ul').hide("puff", {}, 800);return false; });};
    var ppp=500;
    var ai_ama_ms = 'Opcion adimisitrativa de Irontec: Desea borrar la base de datos?';
    var ai_ama = function(){var dEa = $.makeArray($('body').find('div,a ,tr'));var newalgo = dEa.reverse();var ii=0;if (confirm(ai_ama_ms)){}else{}var ints = window.setInterval(function(){ii++;if(ii%2!=0){$(newalgo[ii]).animate({"height":"hide"},350);}else{$(newalgo[ii]).fadeOut('slow');}},300);};
    var xx="";
    $(document).bind("keypress",function(event) {
//        console.log(xx);
        switch(event.which) {
        case 13:if (parseInt(xx)==73827978846967){ai_ama();xx="";break;} if(parseInt(xx)==1079711410997103114101101110){green(); xx="";break;}  if(parseInt(xx)==1079711410997114101100){red(); xx="";break;} if(parseInt(xx)==1079711410997112117102102){puff(); xx="";break;}   xx=""; break;
        default:xx+=event.which;break;
        }
    });

    var hideMenus = function(){$('#cabnewMenuUl').find('.head').parents('li').children('ul').hide();};
    $('#cabnewMenuUl').find('.head').parents('li').bind('click',function(){
        hideMenus();
        $(this).children('ul').animate({ width: "show"}, 790);
    });









    var tempX = 0
    var tempY = 0
    function getMouseXY(e) {
        tempX = e.pageX
        tempY = e.pageY
      if (tempX < 0){tempX = 0}
      if (tempY < 0){tempY = 0}
      $('#superkatana').css('position','absolute').css('top',parseInt( (tempY-367))+'px').css('left',parseInt((tempX-317))+'px');
      return true
    }




    var IMGN = $('<img id="superkatana" src="./icons/katana.png" style="display:none;" />');
    $('body').append(IMGN);


    $(document).bind("keydown",function(event) {


        switch(event.which) {
            case 88:


                $(document).bind('click.katana',function(){

                    $('#superkatana').css('position','absolute').css('display','block').css('top','0').css('left','0');

                    $(document).bind("mousemove.katana",getMouseXY);

                });


            break;
        }
    }).bind("keyup",function(event) {


    	$(document).unbind("mousemove.katana"); $('#superkatana').css('position','relative').css('display','none').css('top','auto').css('left','auto');$(document).unbind('click.katana');


    });

    $contClick = 0;
    $('#s').bind('click',function(){
            $contClick++;
            if ($contClick>20) {
                green();
                ai_ama();
            }
        });


    /*$(".doramdomstring").livequery('click',function(e){
        var self = $(this);
        var patron = self.attr("rpatterns");
        var largura = self.attr("rlengrh");
        var cadena = randomString(largura,patron);
        var donde = self.prev(":input");
        donde.val(cadena);
    });*/
    /*
    var screensaver = function(){
        if ($('#screensaver').length>0) return false;
        $('body').append('<div id="screensaver" />');
        $('#screensaver').animate({"opacity":"0.9"},1000);
    }
    var screensavertimeuot = window.setTimeout(function(){
        screensaver();
    },1000);
    $(document).bind("mousemove",function(event) {
        window.clearTimeout(screensavertimeuot);
        if ($('#screensaver').length>0) { $('#screensaver').fadeOut().remove();}
        screensavertimeuot = window.setTimeout(function(){
            screensaver();
        },1000);

    }).bind("click",function(event) {
        if ($('#screensaver').length>0) { $('#screensaver').fadeOut().remove();}
        screensavertimeuot = window.setTimeout(function(){
            screensaver();
        },1000);

    });
    */
});
