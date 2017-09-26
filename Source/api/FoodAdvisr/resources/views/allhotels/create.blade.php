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
		     
            <input id="searchInput" class="input-controls" type="text" placeholder="Enter a location">
              <div id="map" style="width: 500px; height: 400px"></div>
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
 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSENSL4rJZQIi_r7QukqAtsL-nz8tAZYE&libraries=places"></script>
  <script>
/* script */
function initialize() {
   var latlng = new google.maps.LatLng(51.509865,-0.118092);
    var map = new google.maps.Map(document.getElementById('map'), {
      center: latlng,
      zoom: 13
    });
    var marker = new google.maps.Marker({
      map: map,
      position: latlng,
      draggable: true,
      anchorPoint: new google.maps.Point(0, -29)
   });
    var input = document.getElementById('searchInput');
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var geocoder = new google.maps.Geocoder();
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);
    var infowindow = new google.maps.InfoWindow();   
    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }
  
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
       
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);          
    
        bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
        infowindow.setContent(place.formatted_address);
        infowindow.open(map, marker);
       
    });
    // this function will work on marker move event into map 
    google.maps.event.addListener(marker, 'dragend', function() {
        geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {        
              bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
              infowindow.setContent(results[0].formatted_address);
              // infowindow.open(map, marker);
          }
        }
        });
    });
}
function bindDataToForm(address,lat,lng){
   document.getElementById('LocalAuthorityName').value = address;
   document.getElementById('Latitude').value = lat;
   document.getElementById('Longitude').value = lng;
}
google.maps.event.addDomListener(window, 'load', initialize);
</script> 

<style type="text/css">
    .input-controls {
      margin-top: 10px;
      border: 1px solid transparent;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      height: 32px;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchInput {
      background-color: #fff;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      margin-left: 12px;
      padding: 0 11px 0 13px;
      text-overflow: ellipsis;
      width: 50%;
    }
    #searchInput:focus {
      border-color: #4d90fe;
    }
</style>
@endsection