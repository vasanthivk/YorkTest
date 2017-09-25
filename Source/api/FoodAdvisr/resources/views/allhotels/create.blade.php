@extends('layouts.master')
@section('title')
FoodAdvisr-Hotels
@endsection
@section('module')
Hotels
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahPassword', 'components.form.password', ['name', 'labeltext'=>null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahFile', 'components.form.file', ['name', 'labeltext'=>null,'value' =>null, 'attributes' => []])}}
{{Form::component('ahDate', 'components.form.date', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}

{{ Form::open(array('route' => 'allhotels.store','files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahNumber('FHRSID','FHRSID :','',array('min'=>'0','maxlength' => '20','max'=>'99999999999999999999'))  }}
		        {{ Form::ahText('LocalAuthorityBusinessID','Local Authority BusinessID :','',array('maxlength' => '100'))  }}
		        {{ Form::ahText('BusinessName','Business Name :','',array('maxlength' => '100'))  }}		
		        {{ Form::ahText('BusinessType','Business Type :','',array('maxlength' => '100'))  }}
		        {{ Form::ahNumber('BusinessTypeID','Business Type ID :','',array('min'=>'0','maxlength' => '20','max'=>'99999999999999999999'))  }}
		        {{ Form::ahNumber('RatingValue','Rating Value :','',array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahText('RatingKey','Rating Key :','',array('maxlength' => '100'))  }}
		        {{ Form::ahDate('RatingDate','Rating Date :', \Carbon\Carbon::now()) }}
		        {{ Form::ahNumber('LocalAuthorityCode','Local Authority Code :','',array('min'=>'0','maxlength' => '5','max'=>'99999'))  }}
		        {{ Form::ahText('LocalAuthorityName','Local Authority Name :','',array("onchange"=>"getlatitudelongitude(this)",'maxlength'=> '1000'))  }}
		        {{ Form::ahText('LocalAuthorityWebSite','Local Authority WebSite :','',array('maxlength' => '100'))  }}
		        </br>
		    </div>
		     <div class="col-md-6">
		        {{ Form::ahText('LocalAuthorityEmailAddress','Local Authority EmailAddress :','',array('maxlength' => '100'))  }}		
		        {{ Form::ahText('SchemeType','SchemeType :','',array('maxlength' => '100'))  }}
		        {{ Form::ahNumber('NewRatingPending','New Rating Pending :','',array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahText('Longitude','Longitude :','',array('maxlength' => '100'))  }}
		        {{ Form::ahText('Latitude','Latitude :','',array('maxlength' => '100'))  }}
		        {{ Form::ahNumber('Hygiene','Hygiene :','',array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahNumber('Structural','Structural :','',array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahNumber('ConfidenceInManagement','Confidence In Management :','',array('min'=>'0','maxlength' => '3','max'=>'999'))  }}

		        <body style="margin:0px; padding:0px;" onload="initialize_map()"> 
 					 <div id="canvas_create" style="width: 500px; height: 400px"></div> 
  
  				</body> 
		        </br>
		    </div>
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('allhotels.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
  <script type="text/javascript">  
    function initialize_map() {
  if(!!navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
                
                    var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    
                    var infowindow = new google.maps.InfoWindow({
                        map: map,
                        position: geolocate,
                        content:
                            '<h6>Your Current Location</h6>' +
                            '<h6>Latitude: ' + position.coords.latitude + '</h6>' +
                            '<h6>Longitude: ' + position.coords.longitude + '</h6>'
                    });
                    
                    map.setCenter(geolocate);
                    
                });
       geocoder = new google.maps.Geocoder();
       var myOptions = {
                   zoom: 18,
                   // center: new google.maps.LatLng(13.288828765662416, 80.945261001586914),
                   mapTypeControl: true,
                   mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                   navigationControl: true,
                   mapTypeId: google.maps.MapTypeId.ROADMAP
             };
        

        
        
        map = new google.maps.Map(document.getElementById("canvas_create"), myOptions);
     
         // initialize marker
       
      var marker = new google.maps.Marker({
        position: map.getCenter(),
        draggable: true,
        map: map
      });
      
      // intercept map and marker movements
      google.maps.event.addListener(map, "idle", function() {
        marker.setPosition(map.getCenter());
        
        var latitude = map.getCenter().lat().toFixed(6);
        var longitude = map.getCenter().lng().toFixed(6);
        document.getElementById("Latitude").value = latitude;
        document.getElementById("Longitude").value = longitude;
        google.maps.event.trigger(map, "resize");
      });
      google.maps.event.addListener(marker, "dragend", function(mapEvent) {
        map.panTo(mapEvent.latLng);
        var geocoder = new google.maps.Geocoder;
       geocoder.geocode({'location': mapEvent.latLng}, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            if (results[1]) {
       
               document.getElementById("LocalAuthorityName").value = results[1].formatted_address;
          
            }
        }
    });
      });
      
     
     findAddress(document.getElementById("LocalAuthorityName").value);
     }
    }
  
function findAddress(address) {
  if ((address != '') && geocoder) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
          if (results && results[0]
            && results[0].geometry && results[0].geometry.viewport)
            map.fitBounds(results[0].geometry.viewport);
        } else {
          alert("No results found");
        }
      } else {
        // alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
} 
  
</script>
  
  <script type="text/javascript">
  $( document ).ready(function() {
    $( "#country_name" ).blur(function() {
      var address = $("#country_name").val();
      findAddress(address);
    });
    $( "#state" ).blur(function() {
      var address = $("#country_name").val()+' '+$("#state").val();
      findAddress(address);
    });
    
    $( "#LocalAuthorityName" ).blur(function() {
      var address = $("#LocalAuthorityName").val()+' '+$("#state").val()+' '+$("#country_name").val();
      findAddress(address);
    });

    $( "#area" ).blur(function() {
      var address = $("#area").val()+' '+$("#LocalAuthorityName").val()+' '+$("#state").val()+' '+$("#country_name").val();
      findAddress(address);
    });

    $( "#street_name" ).blur(function() {
      var address = $("#street_name").val()+' '+$("#area").val()+' '+$("#LocalAuthorityName").val()+' '+$("#state").val()+' '+$("#country_name").val();
      findAddress(address);
    });   
    
    
  });
  
  </script>
     <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSENSL4rJZQIi_r7QukqAtsL-nz8tAZYE&callback=initialize_map">
</script> 
@endsection