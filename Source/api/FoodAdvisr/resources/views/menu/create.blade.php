@extends('layouts.master')
@section('title')
FoodAdvisr-Menus
@endsection
@section('module')
Menu
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

{{ Form::open(array('method' => 'GET','route' => 'menu.create')) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
           <div class="col-md-8">                            
               <div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4"></label>
                        <div class="input-group push-down-10">
                            <span class="input-group-addon"><span class="fa fa-info-circle fa-1x" title='Eatery Names,Locations,Zip'></span></span>                            
                            <input type="text" class="form-control" name="search" id="search" placeholder="Search...." value="{{$searchvalue}}"/>
                            <div id="searchresult"></div>
                            <div class="input-group-btn">
                                <button class="btn btn-primary">Search Eateries</button>
                            </div>
                        </div>  
                </div>
               <br/>
            </div>
            <div class="col-md-4" style="padding-top: 5px;"> 
                
                </br>
            </div> 
     </div>
 </div>
 {{ Form::close() }}

{{ Form::open(array('route' => 'menu.store','files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
                <div class="form-group" style="margin:5px">
                    <label for="eatery_id" class="control-label col-sm-4">Eateries :</label>
                     <div class="col-sm-8">
                            <select class="form-control" id="eatery_id" name="eatery_id">
                                <option value="0">Please select eatery</option>
                                @foreach($eateries as $eatery)
                                 <option value="{{$eatery->id}}">{{$eatery->business_name .' - '.$eatery->address}}</option>
                                 @endforeach
                            </select>
                       </div>
                </div>
		        {{ Form::ahText('menu','Menu Name :','',array('maxlength' => '100'))  }}
            	{{ Form::ahTextarea('description','Description :','',array('size' => '30x5'))  }}
            	<div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4">Groups :</label>
	            	 <div class="col-sm-8">
	                        <select class="form-control" id="group_id" name="group_id">
	                            <option value="0">Please select group</option>
	                            @foreach($groups as $group)
	                            <option value="{{ $group->id }}"> 
	                              {{ $group->description }}</option>
	                            @endforeach
	                        </select>
	                   </div>
                </div>
		        {{ Form::ahSwitch('is_visible','Is Visible :',null,1) }}
				</br>
		    </div>	   
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('menu.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 {{ Form::close() }}

@endsection