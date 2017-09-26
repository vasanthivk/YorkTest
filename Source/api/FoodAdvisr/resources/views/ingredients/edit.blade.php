@extends('layouts.master')
@section('title')
Food Advisr-Ingredients
@endsection
@section('module')
Ingredients
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}


{{ Form::open(array('method' => 'PUT', 'route' => array('ingredients.update',$ingredients->ingredient_id),'files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		         {{ Form::ahText('title','Title :',$ingredients->title,array('maxlength' => '100'))  }}
		        {{ Form::ahTextarea('description','Description :',$ingredients->description,array('maxlength' => '1000'))  }}
		        {{ Form::ahSelect('is_visible','Is Visible :',$ingredients->is_visible,array('1' => 'Active', '2' => 'Inactive')) }}
		        </br>
		    </div>
		     
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('ingredients.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection