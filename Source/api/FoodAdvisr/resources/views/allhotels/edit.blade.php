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
		        {{ Form::ahText('LocalAuthorityName','Local Authority Name :',$hotel->LocalAuthorityName,array('maxlength' => '100'))  }}
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
		        <div id="map" style="width: 500px;height: 350px;"></div>
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
 <script>
      function initMap() {
        var uluru = {lat: <?php echo $hotel->Latitude; ?>, lng: <?php echo $hotel->Longitude; ?>};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 17,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyD5F_aHW5hjzw-Cqt91YUcr8N8WCnnyQ&callback=initMap">
    </script>
@endsection