@extends('layouts.master')
@section('title')
FoodAdvisr-Dishes
@endsection
@section('module')
Dish
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
 {{Form::open(array('route' => 'items.store','files'=>true, 'role'=>'form', 'class'=>'form-horizontal', 'id'=>'wizard-validation'))}}
 <input type="hidden" name="eatery_id" value="{{$eatery_id}}">
<div class="form-group form-horizontal">
        <div class="panel panel-default">
            <br/>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div>
                        <h4>Add Dish</h4>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Dish Name</label>
                            <div class="col-md-4">
                                <input type="text" required="required" name="dish_name" class="form-control" value="">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Item Price</label>
                            <div class="col-md-4">
                                <input type="number" required="required" name="item_default_price" class="form-control" value="" min="0" step="0.01">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Item Description</label>
                            <div class="col-md-4">
                                <input type="text" name="item_description" class="form-control" value="">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <div class="row">
                                <?php
                                $no_image=env('NO_IMAGE');
                                ?>
                                <label class="col-md-2 control-label">Upload Item Image</label>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 130px; height: 120px;">
                                                <img src="../../<?php echo $no_image ?>" alt="..." style="width: 130px; height: 120px;">
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 130px; max-height: 120px;"></div>
                                            <div>
                                   <span class="btn btn-primary btn-file"><span class="fileinput-new">Select Image</span><span class="fileinput-exists">Change</span>
                                   <input type="file" name="logo" id="logo">
                                    </span>
                                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Is Enabled</label>
                            <div class="col-md-4">
                                <label class="switch">
                                    <input type="checkbox" name="is_visible" class="switch" value="1" checked/>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Date Range</label>
                            <div class="col-md-4">
                                <label class="col-md-1 control-label">From</label>
                                <input type="text" name="item_valid_from" class="form-control datepicker" value="">
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-1 control-label">to</label>
                                <input type="text" name="item_valid_till" class="form-control datepicker" value="">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Item Days</label>
                            <div class="col-md-5">
                                <select multiple name="item_applicable_days[]" class="form-control select">
                                    <option value="0" selected="selected">Sunday</option>
                                    <option value="1" selected="selected">Monday</option>
                                    <option value="2" selected="selected">Tuesday</option>
                                    <option value="3" selected="selected">Wednesday</option>
                                    <option value="4" selected="selected">Thursday</option>
                                    <option value="5" selected="selected">Friday</option>
                                    <option value="6" selected="selected">Saturday</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Cuisines</label>
                            <div class="col-md-5">
                                <select multiple name="cuisine_id[]"  required="required" class="form-control select">
                                    @foreach($cuisinetypes as $cuisine)
                                        <option value="{{$cuisine->id}}">{{$cuisine->cuisine_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Meat Content</label>
                            <div class="col-md-10">
                                <div class="form-group col-md-3">
                                    <input type="radio" name="meat_content_type" value="1" checked id="veg"/>
                                    <label class="control-label" for="veg">No Meat/Veg</label>
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="radio" name="meat_content_type" value="2" id="egg"/>
                                    <label class="control-label" for="egg">Contains Egg</label>
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="radio" name="meat_content_type" value="3" id="nonveg"/>
                                    <label class="control-label" for="nonveg">Contains Meat</label>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="control-label col-md-2">Ingrediants</label>
                            <a class="btn btn-primary col-md-1" id="ingrediant_add">Add</a>
                            <div class="col-md-3 ingrediants">
                                <input type="text" name="item_ingredients[]" value="" class="form-control" /><br/>
                            </div>
                        </div>
                        <hr/>
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
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Allergens Contain</label>
                            <div class="col-md-5">
                                <select multiple name="allergents_contain[]" class="form-control select">
                                    @foreach($allergenttypes as $allergent)
                                        <option value="{{$allergent->id}}">{{$allergent->allergent_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Allergens May Contain</label>
                            <div class="col-md-5">
                                <select multiple name="allergents_may_contain[]" class="form-control select">
                                    @foreach($allergenttypes as $allergent)
                                        <option value="{{$allergent->id}}">{{$allergent->allergent_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Nutrition Levels</label>
                        </div>
                        @foreach($nutritiontypes as $nutrition)
                            <div class="form-group">
                                <label class="control-label col-md-2">{{$nutrition->nutrition_type}}</label>
                                {{--<div class="col-md-2">
                                    <input type="number" name="nutrition_from[{{$nutrition->id}}]" nutrition_id="{{$nutrition->id}}" value="" class="form-control" min="0" max="100" step="1" />
                                </div>--}}
                                <div class="col-md-2">
                                    <input type="number" name="nutrition_to[{{$nutrition->id}}]" nutrition_id="{{$nutrition->id}}" value="" class="form-control" min="0" max="100" step="1" />
                                </div>
                            </div>
                        @endforeach
                        <hr/>
                        <div class="form-group">
                            <!--  <label class="col-md-2 control-label">Display Order</label> -->
                            <div class="col-md-2">
                                <input type="hidden" name="display_order" class="form-control" value="1" min="1" step="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection
