function GetCurrentLocation(){
 
  navigator.geolocation.getCurrentPosition(onSuccess, onError,{enableHighAccuracy: true});

}
function onSuccess(position) {
     // alert("position.coords.latitude" + position.coords.latitude);
       var myLatlng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
         var geocoder = new google.maps.Geocoder;
           geocoder.geocode({'location': myLatlng}, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                  //alert(results[1].formatted_address);
                 document.getElementById("eatery-search").value =  results[1].formatted_address; 
                  eaterySearch(); //Reset
                }
            }
        });
      
    }

    // onError Callback receives a PositionError object
    //
    function onError(error) {
        // alert('code: '    + error.code    + '\n' +
        //       'message: ' + error.message + '\n');
    }
