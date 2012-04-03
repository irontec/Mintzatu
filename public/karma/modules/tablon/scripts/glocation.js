  var geocoder;
  var map;
  
  var mapResults = 0;
  
  function glocation_init(jsonObj) {
    geocoder = new google.maps.Geocoder();
    
    
    if (jsonObj) {
    	
    	loc = jsonObj.geometry.location;
    	
    
    }else {
    	loc = {latitude: '43.2478897' ,longitude: '-2.9090668'};
    }
    
    var latlng = new google.maps.LatLng(loc.latitude, loc.longitude);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("glocationMapBoxIn"), myOptions);
    if (jsonObj) {
    	
    	
    	var marker = new google.maps.Marker({
            map: map, 
            position: latlng
        });
    }
    
  }


  $("#address").live("keypress",function(e)  { if (e.keyCode == 13) codeAddress();  });

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
  
// DIGA????? si no tenemos consola, no funciona?? porque? :)     
//        if (typeof console != "undefined") {
        	try{
        		//console.log(mapResults);
        		results[0].geometry.location.latitude = results[0].geometry.location.lat();
        		results[0].geometry.location.longitude = results[0].geometry.location.lng();
        	} catch (e) {}
        	
  //      }
        
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }








function doGlocation(input)
{
	//input.hide();
	//if ($(input.val()).val())
	var glocationButton = $('<span class="glocationButton" >&nbsp;</span>');
	input.before(glocationButton);
	var input = input;
	
	
	
	glocationButton.bind('click', function(){
		var self = $(this);
		var inputEl = self.next('.glocationField ');
		var jSonValue = inputEl.val();
		var jsonObj;
		
		
		if ($.trim(jSonValue)!="") {
			jsonObj = JSON.parse(jSonValue);
		}
		
		
		
		var htmlInM = '' +
		'<div id="addressBox" >' +
		'<input id="address" type="textbox" value="">' +
		'<input type="button" value="Geocode" onclick="codeAddress()">' +
		'</div>' +
		'<div id="glocationMapBoxIn" style="height:300px;width:600px;"></div>' ;

	
		$.prompt(
			'<div id="glocationMapBox" >'+htmlInM+'</div>',
			{
				loaded : function() {
				
				
				
				
					glocation_init(jsonObj);
					
					
					
				},
				buttons:
				{
					Ok:true,
					
				},
				draggable:false,
				submit : function(ret) {
					
					if (mapResults =="0") {
						
						jsonStr = JSON.stringify(jsonObj);
					}else {
					
						jsonStr = JSON.stringify(mapResults);
					}
					
					
					
					
					inputEl.val(jsonStr);
					input.trigger("blur");
					input.parents("form").trigger("submit");	
					return true;
				}
			});
		
		
		
	});
	
	
	
	

	
	
}

