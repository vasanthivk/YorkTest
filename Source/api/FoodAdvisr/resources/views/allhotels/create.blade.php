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
		        {{ Form::ahText('LocalAuthorityName','Local Authority Name :','',array('maxlength' => '100'))  }}
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
 
@endsection