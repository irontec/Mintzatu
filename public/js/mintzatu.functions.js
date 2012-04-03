jQuery().ready(function(){
	$('.addLaguna').tooltip();
	
	var loading;
	
	var footer = {
		items: 4,
		position: 0,
		total: $('#destacados_footer li').length,
		maxPixel: 0,
		width: 0,
		init: function() {
			var o = $('#destacados_footer li:eq(0)');
			this.width = parseInt(o.css('width')) + parseInt(o.css('margin-right')) + parseInt(o.css('margin-left')) + parseInt(o.css('padding-right')) + parseInt(o.css('padding-left')) + parseInt(o.css('border-left-width')) + parseInt(o.css('border-right-width'));
			this.maxPixel = (this.total - footer.items) * this.width;
			$('#destacados').delegate('a.pag', 'click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if ($(this).hasClass('left')) {
					footer.moveLeft();
				} else {
					footer.moveRight();
				}
			});
		},
		moveLeft: function() {
			if (this.position == 0) {
				this.moveFijo(-this.maxPixel);
				this.position = this.total - footer.items;
				return;
			}
			this.move('+');
			this.position--;
		},
		moveRight: function(){
			if (this.position == (this.total - footer.items)) {
				this.moveFijo(0);
				this.position = 0;
				return;
			}
			this.move('-');
			this.position++;
		},
		move: function(move) {
			$('#destacados_footer').animate({
				left: move + '=' + this.width
			}, 500);
		},
		moveFijo: function(pixel) {
			$('#destacados_footer').animate({
				left: pixel + 'px'
			}, 500);
		}
	};

	footer.init();
	
    var baseUrl = $('base').attr('href');
    
    /*Distantzia Kalkulatu*/
    function calculateDistance(glatlng1)
    {
    	try 
        {
            navigator.geolocation.getCurrentPosition(function(puntua){
                var glatlng2 = new GLatLng(puntua.coords.latitude, puntua.coords.longitude);
                var km = glatlng1.distanceFrom(glatlng2, 6378.1).toFixed(1);
                if (km < 10) {
                	window.location.href = baseUrl + 'lekuak/check/lekua/' + $('#urlLeku').text();
                } else {
                	loading.dialog('close');
                	$('<div><br /><p>Puntu honetatik hurrunegi zaude bertan zaudela esateko! Tranpak egiten ari zara?</p></div>').dialog({
                    	modal:true,
                    	title:'<span class="fav">&nbsp;</span>Adi!',
                    	resizable:false,
                    	buttons: {
                            'ados': function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                }
            });
        }
        catch (error)
        {
            alert(error);
        }
    }
    
    /* Norbaitekin zaudela esan */
    $('.pertsonaPastila').on('click', '#checkBerria', function(e){
    	e.preventDefault();
    	e.stopPropagation();
    	var $enlace = $(this);
    	if ($(this).attr('rel') != 'checked') {
    		$('<div><br />Pertsona honekin zaudela esateko, zu ere hemen egon behar zara.</div>').dialog({
            	modal:true,
            	title:'<span class="fav">&nbsp;</span> Mintzatu',
            	resizable:false,
            	buttons: {
                    'ados': function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
    	} else {
    		$.getJSON($(this).attr('href'), function(result) {
                if (result.success) {
                	$('.tooltip').hide();
                	$enlace.replaceWith('<img class="addLaguna" title="Berarekin Nago" src="'+ baseUrl +'/img/lagunaRekinON.png" />');
                	$('<div><br />'+result.mezua+'</div>').dialog({
                    	modal:true,
                    	title:'<span class="fav">&nbsp;</span> Mintzatu',
                    	resizable:false,
                    	buttons: {
                            'ados': function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                } else {
                	alert("SUCCESS FALSE");
                }
            });
    	}
    });
    
    /* Leku batean chekcin egin */
    $('#main').on('click','.chekcIn', function(e){
    	e.preventDefault();
        if ($(this).text() == 'Hemen Zaude') {
            return;
        }
        loading = $('<div><br /><img id="loading" src="'+baseUrl+'/img/favbig.png" /></div>').dialog({
        	modal:true,
        	title:'<span class="fav">&nbsp;</span> Zure kokapenaren zain ... ',
        	resizable:false,
        	height:125
        });
        $.getJSON(baseUrl + 'lekuak/datuak/zein/' + $('#urlLeku').text() + '?format=json', function(result) {
            var glatlng1 = new GLatLng(result.json[0].latitudea, result.json[0].longitudea);
            calculateDistance(glatlng1);
        });
    });
    
    /* Lekuko Irudiak igoteko Form */
    $('#main').on('click','#irudiaIgo', function(e){
        e.preventDefault();
        if ($('#irudiaIgoForm').is(':visible')) {
            $('#irudiaIgoForm').slideUp(1000);
        } else {
            $('#irudiaIgoForm').slideDown(1000);
        }
    });

    
    /* Dialog erako mezuak idazteko */
    $("#dialog-mezua").dialog({
    	modal:true,
    	title:'<span class="fav">&nbsp;</span>Mezua',
    	resizable:false,
    	buttons: {
            'ados': function() {
                $( this ).dialog( "close" );
            }
        }
    });
    
    /* Formulakiko data margozteko */
    if ($('.datePicker').length > 0) {
        $.datepicker.setDefaults( $.datepicker.regional[ "eu" ] );
        var data = $('.datePicker').attr('value');
        if (data.length > 0) {
            data = data.replace(/-/g,'/');
        } else {
            gaur = new Date();
            data = gaur.getFullYear()+"/"+(("0" + (gaur.getMonth()+1)).slice(-2))+"/"+gaur.getDate();
        }
        $('.datePicker').datepicker({
            changeYear: true,
            buttonImageOnly: true,
            dateFormat: 'yy/mm/dd',
            yearRange: '1910:2011',
            defaultDate: data
        });
    }
    
    /* Lagunen Bilaketa kutxako testua kendu edo ipintzeko */
    if ($('#lagunBilaketa input[id="pertsona"]').length > 0) {
        $('#lagunBilaketa input[id="pertsona"]').focus(function(){
            if ($(this).val() == 'lagunak bilatu') {
                $(this).val('');
            }
        });
        $('#lagunBilaketa input[id="pertsona"]').focusout(function(){
            if($(this).val() == ''){
                $(this).val('lagunak bilatu');
            }
        });
    }
    
    /* Lekuen Bilaketa kutxako testua kendu edo ipintzeko */
    if ($('#lekuBilaketa input[id="lekuak"]').length > 0) {
        $('#lekuBilaketa input[id="lekuak"]').focus(function(){
            if ($(this).val() == 'lekuak bilatu') {
                $(this).val('');
            }
        });
        $('#lekuBilaketa input[id="lekuak"]').focusout(function(){
            if($(this).val() == ''){
                $(this).val('lekuak bilatu');
            }
        });
    }
    
    /* Lekuen Berrien Bilaketa kutxako testua kendu edo ipintzeko */
    if ($('#mapa-bilatu input[name="helbidea"]').length > 0) {
        $('#mapa-bilatu input[name="helbidea"]').focus(function(){
            if ($(this).val() == 'sartu helbidea...') {
                $(this).val('');
            }
        });
        $('#mapa-bilatu input[name="helbidea"]').focusout(function(){
            if($(this).val() == ''){
                $(this).val('sartu helbidea...');
            }
        });
    }
    
    $.currentMap = {};
    
    $.myMaps = {};
    $.myMaps.panToID = function(id) {
    	if ($.currentMap.map && $.currentMap.markers[id]!=undefined) {
    		
    		console.log($.myMaps.mID);
    		if ($.myMaps.mID) 
    			$.currentMap.markers[$.myMaps.mID].closeInfoWindow() ;
    		
    		$.myMaps.mID = id;
    		
    		$.currentMap.map.setZoom(9);
        	window.setTimeout(function(){
        		$.currentMap.map.panTo($.currentMap.markers[id].getLatLng());
        	}, 300);
        	window.setTimeout(function(){
        		$.currentMap.map.setZoom(14);
        	}, 600);
        	window.setTimeout(function(){
        		GEvent.trigger($.currentMap.markers[id], 'click');
        	}, 900);
    	}
    };
    
    /* Mapa erakutsi */
    function load(mapaIzena) {
        var map = new GMap2(document.getElementById(mapaIzena));
        $.currentMap.map = map;
        $.currentMap.markers = {};
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        switch (mapaIzena) {
        case 'lekuedit-map':
            $.getJSON(baseUrl + 'lekuak/datuak/zein/' + $('#urlLeku').text() + '?format=json', function(result) {
            	var center = new GLatLng(result.json[0].latitudea,result.json[0].longitudea);
                map.setCenter(center, 15);
                geocoder = new GClientGeocoder();
                var icon = new GIcon(G_DEFAULT_ICON);
                icon.image = baseUrl + 'img/pick.png';
                icon.iconSize = new GSize(39, 34);
                icon.shadow = '';
                var marker = new GMarker(center, 
                    {
                        title: result.json[0].izena,
                        icon: icon,
                        draggable: true
                    });  
                
                map.addOverlay(marker);

                GEvent.addListener(marker, "dragend", function() {
                    var pt = marker.getPoint();
                    map.panTo(pt);
                    datuakSartu(marker.getLatLng());
                });

                GEvent.addListener(map, "moveend", function() {
                    map.clearOverlays();
                    var center = map.getCenter();
                    var marker = new GMarker(center, {draggable: true, icon:icon});
                    map.addOverlay(marker);

                    GEvent.addListener(marker, "dragend", function() {
                        var pt = marker.getPoint();
                        map.panTo(pt);
                        datuakSartu(marker.getLatLng());
                    });

                });
            });
            break;
        case 'leku-map':
            $.getJSON(baseUrl + 'lekuak/datuak/zein/' + $('#urlLeku').text() + '?format=json', function(result) {
                var center = new GLatLng(result.json[0].latitudea,result.json[0].longitudea);
                map.setCenter(center, 15);
                var center = new GLatLng(result.json[0].latitudea,result.json[0].longitudea);
                var icon = new GIcon(G_DEFAULT_ICON);
                icon.image = baseUrl + 'lekuak/kategoria-irudia/kategoria/'+result.json[0].kategoria.url+'/neurria/mapa';

                icon.iconSize = new GSize(39, 34);
                icon.shadow = '';
                var marker = new GMarker(center, 
                    {
                        title: result.json[0].izena,
                        icon: icon
                    });  
                GEvent.addListener(marker, 'click', function() {
                    marker.openInfoWindowHtml(
                        '<span class="helbidea"><strong>Helbidea:</strong> '+result.json[0].helbidea+'</span><br />'+
                        '<span class="herria">'+result.json[0].herria+'</span>'
                    );
                });
                map.addOverlay(marker);
            });
            break;
        case 'main-map':
            var center = new GLatLng(43.00,-2.61);
            map.setCenter(center, 1);
            $.getJSON(baseUrl + 'lekuak/datuak/zein/denak?format=json', function(result) {
                $.each(result.json, function(i,m){
                    var center = new GLatLng(m.latitudea,m.longitudea);
                    var icon = new GIcon(G_DEFAULT_ICON);
                    icon.image = baseUrl + 'lekuak/kategoria-irudia/kategoria/'+m.kategoria.url+'/neurria/mapa';
                    icon.iconSize = new GSize(39, 34);
                    icon.shadow = '';
                    var marker = new GMarker(center, 
                        {
                            title: m.izena,
                            icon: icon
                        });
                    $.currentMap.markers[m.id_lekua] = marker;
                    GEvent.addListener(marker, 'click', function() {
                        marker.openInfoWindowHtml(
                            '<h3><a href="'+ baseUrl + 'lekuak/ikusi/lekua/'+m.url+'">'+m.izena+'</a></h3>' +
                            '<span class="kategoria">'+m.kategoria.izena+'</span><br />' +
                            '<span class="helbidea"><strong>Helbidea:</strong> '+m.helbidea+'</span><br />'+
                            '<span class="herria">'+m.herria+'</span>'
                        );
                    });
                    map.addOverlay(marker);
                });
                $('body').on('click', '#panTo', function(e){
                	loading = $('<div><br /><img id="loading" src="'+baseUrl+'/img/favbig.png" /></div>').dialog({
                    	modal:true,
                    	title:'<span class="fav">&nbsp;</span> Zure kokapenaren zain ... ',
                    	resizable:false,
                    	height:125
                    });
                	e.stopPropagation();
                	e.preventDefault();
                	if ($.cookie("position")) {
                		var position = $.cookie('position');
                		position = JSON.parse(position);
                		panPos(position.lat,position.lng);
                		window.setTimeout(function(){
                			loading.dialog('close');
                		}, 600);
                	} else {
                		navigator.geolocation.getCurrentPosition(function(position){
                			loading.dialog('close');
	                		panPos(position.coords.latitude,position.coords.longitude);
	                		var pos = {'lat' : position.coords.latitude, 'lng' : position.coords.longitude};
	                		$.cookie('position', JSON.stringify(pos), { expires: 1 });
	                	});
                	}
                });
                
                $('body').on('click', '.mapan', function(e){
                	//$.myMaps.panToID($(this).data('point'));
                	var mPoint = $(this).data('point');
                	var $map = $('#main-map');
                	var $tmp = $('<div class="mark"/>');
                	$tmp.insertBefore($map);
                	$('<div id="mapa-egalaria"></div>').dialog({
                    	modal:true,
                    	title:'<span class="fav">&nbsp;</span>Mapan ikusi!',
                    	resizable:false,
                    	width: $('#main-map').width()+($('#main-map').width()*0.1),
                    	height: $('#main-map').height() + ($('#main-map').height()*0.5),
                    	buttons: {
                            "ados": function() {
                                $( this ).dialog( "close" );
                                $( this ).remove();
                            }
                        },
                        open: function(event, ui) {
                        	$map.appendTo($('#mapa-egalaria'));
                        	$.myMaps.panToID(mPoint);
                        },
                        close: function(event, ui) {
                        	$map.insertBefore($tmp);
                        	
                        	$tmp.remove();
                        }
                    });
                	
                });
                
            });
            function panPos(lat, lng) {
            	var point = new GLatLng(lat,lng); //Makes a latlng
            	map.setZoom(9);
            	window.setTimeout(function(){
            		map.panTo(point);
            	}, 300);
            	window.setTimeout(function(){
            		map.setZoom(14);
            	}, 600);
            }
            geocoder = new GClientGeocoder();
            break;
        case 'map':
            var center = new GLatLng(43.00,-2.61);
            map.setCenter(center, 8);
            geocoder = new GClientGeocoder();
            break;
        }
    }


    
    /* Leku berria sortzeko Maparen kontrola */
    function showAddress(address) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        if (geocoder) {
            geocoder.getLatLng(
                address,    
                function(point) {
                    if (!point) {
                        map.clearOverlays();
                        var center = new GLatLng(43.00,-2.61);
                        map.setCenter(center, 8);
                        
                        $('<div><br /><p>Bilatzen ari zaren lekua ez da existitzen, saiatu berriro</p></div>').dialog({
                        	modal:true,
                        	title:'<span class="fav">&nbsp;</span>Adi!',
                        	resizable:false,
                        	buttons: {
                                'ados': function() {
                                    $( this ).dialog( "close" );
                                }
                            }
                        });
                        
                    } else {
                        map.clearOverlays();
                        map.setCenter(point, 14);
                        var marker = new GMarker(point, {draggable: true});  
                        map.addOverlay(marker);
                        datuakSartu(marker.getLatLng());
    
                        GEvent.addListener(marker, "dragend", function() {
                            var pt = marker.getPoint();
                            map.panTo(pt);
                            datuakSartu(marker.getLatLng());
                        });
    
                        GEvent.addListener(map, "moveend", function() {
                            map.clearOverlays();
                            var center = map.getCenter();
                            var marker = new GMarker(center, {draggable: true});
                            map.addOverlay(marker);
    
                            GEvent.addListener(marker, "dragend", function() {
                                var pt = marker.getPoint();
                                map.panTo(pt);
                                datuakSartu(marker.getLatLng());
                            });
       
                        });
    
                    }
                }
            );
        }
    }
    
    /* Maparen puntuko datuak formulakira pasatzeko funtzioa */
    function datuakSartu(helbidea) {
        geocoder.getLocations(helbidea, function(erantzuna){
        	var aDetails = erantzuna.Placemark[0].AddressDetails;
        	
        	var aArea = aDetails.Country.AdministrativeArea;
        	
        	$('#latitudea').val(erantzuna.Placemark[0].Point.coordinates[1]);
        	$('#longitudea').val(erantzuna.Placemark[0].Point.coordinates[0]);
	    	$('#estatua').val(aDetails.Country.CountryName);
        	if (aArea.SubAdministrativeArea) {
                $('#probintzia').val(aArea.SubAdministrativeArea.SubAdministrativeAreaName);
        	    if (typeof(aArea.SubAdministrativeArea.Locality.DependentLocality) !== 'undefined') {
                    $('#herria').val(aArea.SubAdministrativeArea.Locality.DependentLocality.DependentLocalityName);
                    if (aArea.SubAdministrativeArea.Locality.DependentLocality.Thoroughfare) {
                    	$('#helbidea').val(aArea.SubAdministrativeArea.Locality.DependentLocality.Thoroughfare.ThoroughfareName);
                    }
                    if (aArea.SubAdministrativeArea.Locality.DependentLocality.PostalCode) {
                    	$('#postakodea').val(aArea.SubAdministrativeArea.Locality.DependentLocality.PostalCode.PostalCodeNumber);
                    }
                } else {
                    $('#herria').val(aArea.SubAdministrativeArea.Locality.LocalityName);
                    if (aArea.SubAdministrativeArea.Locality.Thoroughfare) {
                    	$('#helbidea').val(aArea.SubAdministrativeArea.Locality.Thoroughfare.ThoroughfareName);
                    }
                    if (aArea.SubAdministrativeArea.Locality.PostalCode) {
                    	$('#postakodea').val(aArea.SubAdministrativeArea.Locality.PostalCode.PostalCodeNumber);
                    }
                }		
        	} else {
        		$('#probintzia').val(aArea.AdministrativeAreaName);
        	    if (typeof(aArea.Locality.DependentLocality) !== 'undefined') {
                    $('#herria').val(aArea.Locality.DependentLocality.DependentLocalityName);
                    if (aArea.Locality.DependentLocality.Thoroughfare) {
                    	$('#helbidea').val(aArea.Locality.DependentLocality.Thoroughfare.ThoroughfareName);
                    }
                    if (aArea.Locality.DependentLocality.PostalCode) {
                    	$('#postakodea').val(aArea.Locality.DependentLocality.PostalCode.PostalCodeNumber);
                    }
                } else {
                    $('#herria').val(aArea.Locality.LocalityName);
                    if (aArea.Locality.Thoroughfare) {
                    	$('#helbidea').val(aArea.Locality.Thoroughfare.ThoroughfareName);
                    }
                    if (aArea.Locality.PostalCode) {
                    	$('#postakodea').val(aArea.Locality.PostalCode.PostalCodeNumber);
                    }
                }
        	}
        });
        
        if (!$('#jarraitu').length) {
            $('#map').after('<button id="jarraitu" style="float:left;">Jarraitu</button>');
        }
        
        $('#main').on('click','#jarraitu', function(){
            $("html, body").animate({ scrollTop: $(document).height() }, 2000);
            $('#lekuBerria').slideDown(2000);
            $(this).hide();
        });
    }
    
    if($('.main-map').length) {
        load($('.main-map').attr('id'));
        if ($('#mapa-bilatu').length) {
            $('#mapa-bilatu').bind('submit', function(e){
                showAddress(this.helbidea.value);
                e.stopPropagation();
                e.preventDefault();
            });
        }
    }
    
        $(document).find('a.youtube').each(function(){
                var url = $(this).attr('href');
                var id = $(this).attr('id');
                swfobject.embedSWF(url,id, "550", "335", "9.0.0");
        });
    
    
        
        
        $('.laster').on('click', function(e){
        	e.preventDefault();
        	e.stopPropagation();
        	
        	var txt = "<p>Laster zure Android edo iPhone mugikorretarako Mintzatu aplikazioa jaitsiko ahal izango duzu... momentuz leku osotik nabigatu ahal duzu zure mugikorrarekin.</p><p style=\"text-align:center;\"><img src=\""+baseUrl+'/img/mintzatuMobileWeb.jpg'+"\"/></p>";//
        	
        	
        	$('<div><br />'+txt+'</div>').dialog({
            	modal:true,
            	title:'<span class="fav">&nbsp;</span>Laster Mintzatu Mobile!',
            	resizable:false,
            	width:540,
            	buttons: {
                    'ados': function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        	return false;
        });
        
        $('a.confirm').on('click', function(e){
        
        	e.preventDefault();
        	e.stopPropagation();
        	
        	var esteka = $(this).attr('href');
        	var txt = '<p>Ziur zaude?</p>';
        	
        	$('<div><br />'+txt+'</div>').dialog({
            	modal:true,
            	title:'<span class="fav">&nbsp;</span>Adi!',
            	resizable:false,
            	buttons: {
            		'Bai': function() {
                        $( this ).dialog( "close" );
                        document.location=esteka;
                    },
        			'Ez': function() {
    					$( this ).dialog( "close" );
    				}
                }
            });
        	
        	/*var txt = "<p>Laster zure Android edo iPhone mugikorretarako Mintzatu aplikazioa jaitsiko ahal izango duzu... momentuz leku osotik nabigatu ahal duzu zure mugikorrarekin.</p><p style=\"text-align:center;\"><img src=\""+baseUrl+'/img/mintzatuMobileWeb.jpg'+"\"/></p>";//
        	
        	
        	$('<div><br />'+txt+'</div>').dialog({
            	modal:true,
            	title:'<span class="fav">&nbsp;</span>Laster Mintzatu Mobile!',
            	resizable:false,
            	width:540,
            	buttons: {
                    'ados': function() {
                        $( this ).dialog( "close" );
                    }
                }
            });*/
        	return false;
        });
        
             
});