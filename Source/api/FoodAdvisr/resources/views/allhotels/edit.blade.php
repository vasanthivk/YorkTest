@extends('layouts.master')
@section('title')
Food Advisr-Hotels
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
{{Form::component('ahReadonly', 'components.form.readonly', ['name', 'labeltext'=>null, 'value' => null])}}

{{ Form::open(array('method' => 'PUT', 'route' => array('allhotels.update',$hotel->FHRSID),'files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahReadonly('FHRSID','FHRSID :',$hotel->FHRSID,array('min'=>'0','maxlength' => '20','max'=>'99999999999999999999'))  }}
		        {{ Form::ahText('LocalAuthorityBusinessID','Local Authority BusinessID :',$hotel->LocalAuthorityBusinessID,array('maxlength' => '100'))  }}
		        {{ Form::ahText('BusinessName','Business Name :',$hotel->BusinessName,array('maxlength' => '100'))  }}		
		        {{ Form::ahText('BusinessType','Business Type :',$hotel->BusinessType,array('maxlength' => '100'))  }}
		        {{ Form::ahNumber('BusinessTypeID','Business Type ID :',$hotel->BusinessTypeID,array('min'=>'0','maxlength' => '20','max'=>'99999999999999999999'))  }}
		        {{ Form::ahNumber('RatingValue','Rating Value :',$hotel->RatingValue,array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahText('RatingKey','Rating Key :',$hotel->RatingKey,array('maxlength' => '100'))  }}
		        {{ Form::ahDate('RatingDate','Rating Date :', $hotel->RatingDate) }}
		        {{ Form::ahNumber('LocalAuthorityCode','Local Authority Code :',$hotel->LocalAuthorityCode,array('min'=>'0','maxlength' => '5','max'=>'99999'))  }}
		        {{ Form::ahText('LocalAuthorityName','Local Authority Name :',$hotel->LocalAuthorityName,array("onchange"=>"getlatitudelongitude(this)",'maxlength'=> '1000'))  }}
		        {{ Form::ahText('LocalAuthorityWebSite','Local Authority WebSite :',$hotel->LocalAuthorityWebSite,array('maxlength' => '100'))  }}
		        </br>
		    </div>
		     <div class="col-md-6">
		        {{ Form::ahText('LocalAuthorityEmailAddress','Local Authority EmailAddress :',$hotel->LocalAuthorityEmailAddress,array('maxlength' => '100'))  }}		
		        {{ Form::ahText('SchemeType','SchemeType :',$hotel->SchemeType,array('maxlength' => '100'))  }}
		        {{ Form::ahNumber('NewRatingPending','New Rating Pending :',$hotel->NewRatingPending,array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahText('Longitude','Longitude :',$hotel->Longitude,array('maxlength' => '100'))  }}
		        {{ Form::ahText('Latitude','Latitude :',$hotel->Latitude,array('maxlength' => '100'))  }}
		        {{ Form::ahNumber('Hygiene','Hygiene :',$hotel->Hygiene,array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahNumber('Structural','Structural :',$hotel->Structural,array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        {{ Form::ahNumber('ConfidenceInManagement','Confidence In Management :',$hotel->ConfidenceInManagement,array('min'=>'0','maxlength' => '3','max'=>'999'))  }}
		        </br>
		         <body style="margin:0px; padding:0px;" onload="initialize_map()"> 
            <div id="map_canvas" style="width: 500px; height: 400px"></div> 
            </body>
		    </div>
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('allhotels.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 <script type="text/javascript">  
    function initialize_map() {
  
       geocoder = new google.maps.Geocoder();
       var myOptions = {
                   zoom: 10,
                   
                   mapTypeControl: true,
                   mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                   navigationControl: true,
                   mapTypeId: google.maps.MapTypeId.ROADMAP
             };
         map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
     
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
        map.setCenter(marker.getPosition());
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
      alert(address);
      findAddress(address);
    });   
    
    
  });
  
  </script>
  <script>
  	$('#LocalAuthorityName').locationpicker({
    // location: {latitude: 46.15242437752303, longitude: 2.7470703125},   
    radius: 300,
    inputBinding: {
          locationNameInput: $('#LocalAuthorityName')
    }
    });
</script>
    
@endsection