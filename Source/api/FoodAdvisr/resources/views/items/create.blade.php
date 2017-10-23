@extends('layouts.master')
@section('title')
FoodAdvisr-Items
@endsection
@section('module')
Items
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
<div class="row">
    <div class="col-md-12">
        {{Form::open(array('route' => 'items.store','files'=>true, 'role'=>'form', 'class'=>'form-horizontal', 'id'=>'wizard-validation'))}}
        <div class="panel panel-default">
            <div class="block">
                <div class="wizard show-submit wizard-validation">
                    <ul>
                        <li>
                            <a href="#step-1">
                                <span class="stepNumber">1</span>
                                <span class="stepDesc">Add a Group</span>
                            </a>
                        </li>
                        <li>
                            <a href="#step-2">
                                <span class="stepNumber">2</span>
                                <span class="stepDesc">Add a Category</span>
                            </a>
                        </li>
                        <li>
                            <a href="#step-3">
                                <span class="stepNumber">3</span>
                                <span class="stepDesc">Add an Item</span>
                            </a>
                        </li>
                    </ul>

                    <div id="step-1">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Select a Group</label>
                            <div class="col-md-5">
                                <select name="itemgroup"  class="form-control select">
                                    <option>Choose Item Group</option>
                                    @foreach($itemgroups as $group)
                                        <option value="{{$group->group_id}}">{{$group->group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">( Or ) Add a New Group</label>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Group Name :</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="itemGroupName" id="itemGroupName" placeholder="Group Name">
                            </div>
                        </div>
                    </div>

                    <div id="step-2">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Select a Category</label>
                            <div class="col-md-5">
                                <select name="itemcategory"  class="form-control select">
                                    <option>Choose Item Category</option>
                                    @foreach($itemcategories as $categories)
                                        <option value="{{$categories->category_id}}">{{$categories->category_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">( Or ) Add a New Category</label>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Category Name :</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="itemCategoryName" id="itemCategoryName" placeholder="Category Name">
                            </div>
                        </div>
                    </div>
                    <div id="step-3">

                        <div class="form-group">
                            <label class="col-md-2 control-label">Select a Category</label>

                        </div>

                    </div>
                </div>
    </div>
            </div>
        {{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}

        </div>
    </div>
<script>
    $(document).ready(function(){
        if($('select[name="itemgroup"]').val() != ''){
            $('#itemGroupName').attr('disabled','disabled');
        }
        else if($('#itemGroupName').val() != ''){
            $('select[name="itemgroup"]').attr('disabled','disabled');
        }
    });
</script>
{{--{{ Form::open(array('route' => 'items.store','files'=>true)) }}
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
 </div>--}}

@endsection