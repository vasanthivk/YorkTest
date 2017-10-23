
function myMap(latitude, longitude, searchval) {
	//alert("From map");
var mapOptions = {
    center: new google.maps.LatLng(latitude, longitude),
    zoom: 10,
    mapTypeId: google.maps.MapTypeId.TERRAIN
}
var map = new google.maps.Map(document.getElementById("map"), mapOptions);
var marker = new google.maps.Marker({
      position: {lat: latitude, lng: longitude},
      map: map,
      icon: 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png',
      title: 'you are here!'
    });

    var marker,idx;
  api.getEateries(latitude, longitude, searchval,function(data){
        if(data.result.length > 0)
        {
          for (idx = 0; idx < data.result.length; idx++) {  
              marker = new google.maps.Marker({
                position: new google.maps.LatLng(data.result[idx].Latitude, data.result[idx].Longitude),
                map: map
              });
            google.maps.event.addListener(marker, 'click', (function(marker, idx) {
              return function() {
                  var infowindow = new google.maps.InfoWindow({
                    content: '<div>' +
                              '<input type=hidden id="eateryId" value="' + data.result[idx].id + '" />' + 
                              '<input type=hidden id="eateryName" value="' + data.result[idx].BusinessName + '" />' + 
                              '<div>' +
                                '<div><b>'+ data.result[idx].BusinessName + '</b></div>' +
                              '</div>' +
                              '<div class="eatery-clear"></div>' +
                            '</div>'
                  });
                  infowindow.open(map, marker);
              }
          })(marker, idx));
        } 
      }
  });
  $( map ).width()=$( window ).width();
}
