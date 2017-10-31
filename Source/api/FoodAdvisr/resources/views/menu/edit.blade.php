@extends('layouts.master')
@section('title')
FoodAdvisr-Menu
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


{{ Form::open(array('method' => 'PUT', 'route' => array('menu.update',$menu->ref),'files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
                {{ Form::ahText('menu','Menu Name:',$menu->menu,array('maxlength' => '100'))  }}
                {{ Form::ahTextarea('description','Description :',$menu->description,array('size' => '30x5'))  }}
                <div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4">Groups :</label>
	            	 <div class="col-sm-8">
	                        <select class="form-control" id="group_id" name="group_id">
	                            <option value="0">Please select group</option>
	                            @foreach($groups as $group)
	                            <option value="{{ $group->id }}"  <?php 
                                $val = $group->id;
                                $res = $menu->group_id;
                               if($val == $res) 
                                {
                                  ?> 
                                  selected="selected"
                                  <?php 
                                } ?>>  
	                              {{ $group->Description }}</option>
	                            @endforeach
	                        </select>
	                   </div>
                </div>
                {{ Form::ahSwitch('is_visible','Is Visible :',null,$menu->is_visible) }} 
		    </div>
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('menu.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection