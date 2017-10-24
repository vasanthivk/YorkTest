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


    {{ Form::open(array('method' => 'PUT', 'route' => array('items.update',$items->item_id),'files'=>true)) }}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="btn-group pull-left">
                        <h4>Edit an Item</h4>
                    </div>
                </div>
                <div class="panel-body">
                    <input type="hidden" id="eatery_id" name="eatery_id" value="{{$eatery_id}}">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Select a Category</label>

                        <div class="col-md-5 itemcategory_div">
                            <select name="itemcategory" class="form-control select">
                                <option>Choose Item Category</option>
                                @foreach($category as $categories)
                                    <option value="{{$categories->category_id}}" @if($items->category_id == $categories->category_id)
                                            selected="selected" @endif>{{$categories->category_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Item Name</label>

                        <div class="col-md-4">
                            <input type="text" name="item_name" class="form-control" value="{{$items->item_name}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Item Price</label>

                        <div class="col-md-4">
                            <input type="number" name="item_default_price" class="form-control" value="{{$items->item_default_price}}" min="0"
                                   step="0.01">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Item Description</label>

                        <div class="col-md-4">
                            <input type="text" name="item_description" class="form-control" value="{{$items->item_description}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Upload Item Image</label>

                        <div class="col-md-10">
                            <input type="file" class="fileinput" name="filename1" id="filename1"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Is Enabled</label>

                        <div class="col-md-4">
                            <label class="switch">
                                <input type="checkbox" name="is_visible" class="switch" @if($items->is_visible == 1)value="1" checked@endif/>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Date Range</label>

                        <div class="col-md-4">
                            <label class="col-md-1 control-label">From</label>
                            <input type="text" name="item_valid_from" class="form-control datepicker" value="{{$items->item_valid_from}}">
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-1 control-label">to</label>
                            <input type="text" name="item_valid_till" class="form-control datepicker" value="{{$items->item_valid_till}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Item Days</label>

                        <div class="col-md-5">
                            <select multiple name="item_applicable_days" class="form-control select">
                                <option value="0">Sunday</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Meat Content</label>

                        <div class="col-md-10">
                            <div class="form-group col-md-3">
                                <input type="radio" name="meat_content_type" @if($items->meat_content_type == 1) checked="checked" @endif value="1" id="veg"/>
                                <label class="control-label" for="veg">No Meat/Veg</label>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="radio" name="meat_content_type" @if($items->meat_content_type == 2) checked="checked" @endif value="2" id="egg"/>
                                <label class="control-label" for="egg">Contains Egg</label>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="radio" name="meat_content_type" @if($items->meat_content_type == 3) checked="checked" @endif value="3" id="nonveg"/>
                                <label class="control-label" for="nonveg">Contains Meat</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Cuisine Type</label>

                        <div class="col-md-5">
                            <select multiple name="cuisine_id[]" class="form-control select">
                                @foreach($cuisinetypes as $cuisine)
                                    <option value="{{$cuisine->cuisine_id}}">{{$cuisine->cuisine_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-2">Ingrediants</label>
                        <a class="btn btn-primary col-md-1" id="ingrediant_add">Add</a>

                        <div class="col-md-3 ingrediants">
                            <input type="text" name="item_ingredients[]" value="" class="form-control"/><br/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Filters</label>

                        <div class="col-md-10">
                            <div class="form-group col-md-3">
                                <input type="checkbox" name="contains_nuts" value="1" id="contains_nuts"/>
                                <label class="control-label" for="contains_nuts">Contain Nuts</label>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="checkbox" name="dairy_free" value="1" id="dairy_free"/>
                                <label class="control-label" for="dairy_free">Dairy Free</label>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="checkbox" name="gluten_free" value="1" id="gluten_free"/>
                                <label class="control-label" for="gluten_free">Gluten Free</label>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="checkbox" name="vegan" value="1" id="vegan"/>
                                <label class="control-label" for="vegan">Vegan</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Allergents Contain</label>

                        <div class="col-md-5">
                            <select multiple name="allergents_contain[]" class="form-control select">
                                @foreach($allergenttypes as $allergent)
                                    <option value="{{$allergent->allergent_id}}">{{$allergent->allergent_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Allergents May Contain</label>

                        <div class="col-md-5">
                            <select multiple name="allergents_may_contain[]" class="form-control select">
                                @foreach($allergenttypes as $allergent)
                                    <option value="{{$allergent->allergent_id}}">{{$allergent->allergent_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Nutrition Levels</label>
                    </div>
                    @foreach($nutritiontypes as $nutrition)
                        <div class="form-group">
                            <label class="control-label col-md-2">{{$nutrition->nutrition_type}}</label>

                            <div class="col-md-2">
                                <input type="number" name="nutrition_from[{{$nutrition->nutrition_id}}]"
                                       nutrition_id="{{$nutrition->nutrition_id}}" value="" class="form-control" min="0"
                                       max="100" step="1"/>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="nutrition_to[{{$nutrition->nutrition_id}}]"
                                       nutrition_id="{{$nutrition->nutrition_id}}" value="" class="form-control" min="0"
                                       max="100" step="1"/>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label class="col-md-2 control-label">Display Order</label>

                        <div class="col-md-4">
                            <input type="number" name="display_order" class="form-control" value="" min="1" step="1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
    {{ link_to_route('items.index','Cancel',array('eatery_id'=>$eatery_id), array('class' => 'btn btn-danger')) }}


@endsection