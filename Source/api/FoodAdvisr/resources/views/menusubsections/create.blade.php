@extends('layouts.master')
@section('title')
FoodAdvisr-Sub Sections
@endsection
@section('module')
{{$eatery->business_name}} - Sub Sections
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}


{{ Form::open(array('route' => 'menusubsections.store','files'=>true)) }}
<input type="hidden" id="section_id" name="section_id" value="{{$section_id}}"></input>
<input type="hidden" id="eatery_id" name="eatery_id" value="{{$menusections->eatery_id}}"></input>
<input type="hidden" id="group_id" name="group_id" value="{{$menusections->group_id}}"></input>
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahText('sub_section_name','Sub Section Name :','',array('maxlength' => '100'))  }}
                {{ Form::ahTextarea('description','Description :','',array('size' => '30x5'))  }}
                {{ Form::ahSwitch('is_visible','Is Visible :',null) }} 
		        </br>
		    </div>
		     
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('menusubsections.index','Cancel',array('section_id' => $section_id), array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection