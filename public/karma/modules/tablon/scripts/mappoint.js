  var geocoder;
  var map;
  
  var mapResults = 0;
  
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }

  function codeAddress() {
    var address = document.getElementById("address").value;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        
        mapResults = results[0];
        
        var marker = new google.maps.Marker({
            map: map, 
            position: results[0].geometry.location
        });
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }


  var doMapJson = function(obj, ret){
	  url  = './modules/tablon/ajax/ops_tablon.php?op=mappoint';
	  var obj = obj;
	  
	  
	  
	  na = obj.find('input.mapInput').attr('name');
	  if (obj.attr("id")) id = obj.attr("id");
	  else id = currentPlantilla+'::'+na; 
	  
	  
	  $.getJSON(url, {id:id, mappoint : ret}, function(data){
		  if (data.ret.error) {
			  $.prompt(data.ret.error,{buttons: {Ok: true}});
			  
		  }
		  if (data.ret.success) {
			  d = data.ret;
			  var intab = false;
			  var intabdata = "";
			  for (var a in d.point) {
				 o = document.getElementById(""+d.locate.replace("%s",a));
				 
				 if (o) {
					 if ($(o).hasClass("intab")) {
						 intab = true;
						 intabdata = a+"="+d.point[a];
						 $(o).append(d.point[a]);
					 } else if ($(o).hasClass("tablon_edit")) {
						 /*intab = true;
						 intabdata = a+"="+d.point[a];
						 $(o).append(d.point[a]);*/
						 console.log(d.point);
					 } else {
						 if (intab==true) {
							 intabdata= intabdata+"||"+a+"="+d.point[a];
						 }
						 $(o).html(d.point[a]);
					 }
				 } else {
					 
					 if (obj.find('input.mapInput').hasClass("intab")) {
						 
						 intab = true;
						 intabdata+= intabdata+"||"+a+"="+d.point[a];
					 }
					 if (intab==true) {
						 //intabdata= intabdata+"||"+a+"="+d.point[a];
					 }
				 }
			  }
			  
			  obj.find('input.mapInput').val(d.id);
			  if (intab==true) {
				  obj.find('input.mapInput').val(intabdata);
			  }
		  }
	  });
	  return true;
  };
  

$(document).ready(function(){
	
	
	
	var mapButton = $('<button class="mapButton">view map</button>'); 
	
	/*$("td.mappoint").each(function(i){
		var self = $(this);
		$(self[i]).append(mapButton);
		
	});*/
	
	$('img.mapButtonStart').live('click', function(){
		
		var lt =  $(this).attr('latlng').split(',');
		
		
		
		
		
		var htmlInM = '' +
		'<div id="map_canvas" style="height:300px;width:600px;"></div>' ;

	
		$.prompt(
			'<div id="mapBox" >'+htmlInM+'</div>',
			{
				loaded : function() {
					geocoder = new google.maps.Geocoder();
				    var latlng = new google.maps.LatLng(lt[0], lt[1]);
				    var myOptions = {
				      zoom: 8,
				      center: latlng,
				      mapTypeId: google.maps.MapTypeId.ROADMAP
				    }
				    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				    
				    var marker = new google.maps.Marker({
			            map: map, 
			            position: latlng
			        });
				},
				buttons:
				{
					Ok:true,
					
				},
				draggable:false,
				submit : function(ret) {
					
					return true;
				}
			});			
		
		
		
		
		
		
	});
	
	
	$('img.mapButton').live('click', function(){
		
		
		var self = $(this);
		var selftd = self.parents("td");
		var htmlInM = '' +
			'<div>' +
			'<input id="address" type="textbox" value="">' +
			'<input type="button" value="Geocode" onclick="codeAddress()">' +
			'</div>' +
			'<div id="map_canvas" style="height:300px;width:600px;"></div>' ;

		
		$.prompt(
				'<div id="mapBox" >'+htmlInM+'</div>',
				{
					loaded : function() {
						initialize();
					},
					buttons:
					{
						Save:true,
						Close:false
					},
					draggable:false,
					submit : function(ret) {
						if(ret) {
							res = mapResults.address_components;
							resgeo = mapResults.geometry.location;
							l = res.length;
							var returnData = {};
							for (i=0; i<l ;i++) {
								var result = res[i];
								key = result.types[0];
								if (i==0) {
									result['lat'] = resgeo.lat();
									result['lng'] = resgeo.lng();
								}
								returnData[key] = result;
							}
							returnData['request'] = res[0];
							
							o = doMapJson(selftd, returnData);

							if (o) return true;
							
							return false;
							
												
						}
						return true;
					}
				});		
            
        	
	});
	
	
	
	
	
});