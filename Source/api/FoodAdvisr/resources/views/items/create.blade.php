@extends('layouts.master')
@section('title')
Food Advisr-Item
@endsection
@section('module')
Item
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}

{{ Form::open(array('route' => 'items.store','files'=>true)) }}
<input type="hidden" id="hotel_id" name="hotel_id" value="{{$hotel_id}}"></input>
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahText('title','Title :','',array('maxlength' => '100'))  }}
		        {{ Form::ahTextarea('description','Description :','',array('maxlength' => '1000'))  }}
		        {{ Form::ahSelect('category_id','Category :','',$category) }}
		        {{ Form::ahSelect('is_visible','Is Visible :','1',array('1' => 'Active', '2' => 'Inactive')) }}
		        {{ Form::ahSelect('display_order','Display Order :','1',array('1' => 'Active', '2' => 'Inactive')) }}
		        </br>
		    </div>
		     
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('items.index','Cancel',array('hotel_id'=>$hotel_id), array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection