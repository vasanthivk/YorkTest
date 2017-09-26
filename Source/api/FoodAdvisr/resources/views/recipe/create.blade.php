@extends('layouts.master')
@section('title')
FoodAdvisr-Recipe
@endsection
@section('module')
Recipe
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}

{{ Form::open(array('route' => 'recipe.store','files'=>true)) }}
<input type="hidden" id="item_id" name="item_id" value="{{$item_id}}"></input>
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahSelect('ingredient_id','Ingredients :','',$ingredients) }}
		        </br>
		    </div>
		     
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('recipe.index','Cancel',array('item_id'=>$item_id), array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection