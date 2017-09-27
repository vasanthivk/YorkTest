
function myMap() {
	//alert("From map");
var mapOptions = {
    center: new google.maps.LatLng(51.5, -0.12),
    zoom: 4,
    mapTypeId: google.maps.MapTypeId.TERRAIN
}
var map = new google.maps.Map(document.getElementById("map"), mapOptions);
}
