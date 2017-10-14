
function myMap(latitude, longitude, searchval) {
	//alert("From map");
var mapOptions = {
    center: new google.maps.LatLng(latitude, longitude),
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.TERRAIN
}
var map = new google.maps.Map(document.getElementById("map"), mapOptions);
var marker = new google.maps.Marker({
      position: {lat: latitude, lng: longitude},
      map: map,
      icon: 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png',
      title: 'you are here!'
    });

    var marker;
  api.getEateries(latitude, longitude, searchval,function(data){
        //var op ='';
        if(data.result.length > 0)
        {
            for (idx in data.result) {  
               // alert(data.result[idx].Latitude);
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(data.result[idx].Latitude, data.result[idx].Longitude),
            map: map
          });
        } 
    }
 });


}
