@extends('layouts.master')
@section('title')
FoodAdvisr-Sub Sections
@endsection
@section('module')
{{$eatery->business_name}} - {{$menus[0]->menu}} - {{$menusections->section_name}} - Sub Section
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}

{{ Form::open(array('method' => 'PUT', 'route' => array('menusubsections.update',$menusubsections->id),'files'=>true)) }}
<input type="hidden" id="section_id" name="section_id" value="{{$section_id}}"></input>
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahText('sub_section_name','Sub Section Name :',$menusubsections->sub_section_name,array('maxlength' => '100'))  }}
                {{ Form::ahTextarea('description','Description :',$menusubsections->description,array('size' => '30x5'))  }}
                {{ Form::ahSwitch('is_visible','Is Visible :',null,$menusubsections->is_visible) }}
		        </br>
		    </div>
		      
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('menusubsections.index','Cancel',array('section_id' => $section_id), array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection