
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

    var marker,idx;
  api.getEateries(latitude, longitude, searchval,function(data){
        //var op ='';
        if(data.result.length > 0)
        {
            for (idx = 0; idx < data.result.length; idx++) {  
               // alert(data.result[idx].Latitude);
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(data.result[idx].Latitude, data.result[idx].Longitude),
            map: map
          });
          google.maps.event.addListener(marker, 'click', (function(marker, idx) {
            return function() {
          if(data.result[idx].IsAssociated == 1)
            {
          var infowindow = new google.maps.InfoWindow({
             content: '<div class="act-eatery">' +
                      '<input type=hidden id="eateryId" value="' + data.result[idx].id + '" />' + 
                      '<div>' +
                        '<div class="act-eatery-logo" ><img class="act-eatery-logopath" src="' + appSettings.mediaPath + data.result[idx].LogoPath + '"></img></div>' +
                        '<div class="act-eatery-name"><b>'+ data.result[idx].BusinessName + '</b><br/>'+ '<div class="act-action-div"><div class="act-eatery-distance">'+ data.result[idx].distance+'m'+ '&nbsp&nbsp&nbsp|'+'</div>'+'<div class="act-eatery-image"> <img class="act-eatery-image" src="img/foodadvisr-green.png"/></div></div>' +'</div>' +
                      '</div>' +
                      '<div class="eatery-clear"></div>' +
                    '</div>'
          });
          infowindow.open(map, marker);
        }
        else{
           var infowindow = new google.maps.InfoWindow({
             content: '<div class="in-act-eatery">' +
                      '<input type=hidden id="eateryId" value="' + data.result[idx].id + '" />' + 
                      '<input type=hidden id="eateryName" value="' + data.result[idx].BusinessName + '" />' + 
                      '<div>' +
                        '<div class="in-act-eatery-logo" ><img class="act-eatery-logopath" src="../img/thumb.svg"></img></div>' + 
                        '<div class="in-act-eatery-name"><b>'+ data.result[idx].BusinessName + '</b><br/>' +/* (data.result[idx].Address==null?'':data.result[idx].Address)+*/'<div class="act-action-div"><div class="act-eatery-distance">'+ data.result[idx].distance+'m'+'&nbsp&nbsp&nbsp|'+'</div>'+'<div class="act-eatery-image"> <img class="in-act-eatery-image" src="img/foodadvisr-green.png"/></div></div>' +'</div>' + '</div>' +
                      '</div>' +
                      '<div class="eatery-clear"></div>' +
                    '</div>'
          });
          infowindow.open(map, marker);
        }
            }
      })(marker, idx));
        } 
    }
 });
$( map ).width()=$( window ).width();

}
