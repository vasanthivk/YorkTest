@extends('layouts.master')
@section('title')
FoodAdvisr-Sections
@endsection
@section('module')
{{$menus[0]->business_name}} - Section
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}

{{ Form::open(array('method' => 'PUT', 'route' => array('menusections.update',$menusections->id),'files'=>true)) }}
<input type="hidden" id="menu_id" name="menu_id" value="{{$menu_id}}"></input>
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		          {{ Form::ahText('section_name','Section Name:',$menusections->section_name,array('maxlength' => '100'))  }}
                  {{ Form::ahTextarea('description','Description :',$menusections->description,array('size' => '30x5'))  }}
                  {{ Form::ahSwitch('is_visible','Is Visible :',null,$menusections->is_visible) }} 
		        </br>
		    </div>
		      
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('menusections.index','Cancel',array('menu_id' => $menu_id), array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection