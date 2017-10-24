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

{{ Form::open(array('method' => 'PUT', 'route' => array('items.update',$items->id),'files'=>true)) }}
<input type="hidden" id="eatery_id" name="eatery_id" value="{{$eatery_id}}">
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
            <div class="col-md-10">
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Category :</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="group_id" name="group_id">
                            <option>Choose Item Category</option>
                            @foreach($category as $categories)
                            <option value="{{$categories->id}}" @if($items->category_id == $categories->id) selected="selected" @endif>{{$categories->category_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{ Form::ahText('item_name','Item Name :',$items->item_name,array('maxlength' => '100'))  }}
                <div class="form-group" style="margin:5px">
                <label for="group_name" class="control-label col-sm-4">Item Price :</label>
                        <div class="col-sm-8">
                            <input type="number" name="item_default_price" class="form-control" value="{{$items->item_default_price}}" min="0"
                                   step="0.01">
                        </div>
                </div> 
                <div class="form-group" style="margin:5px">
                <label for="group_name" class="control-label col-sm-4">Item Description :</label>
                        <div class="col-sm-8">
                           <input type="text" name="item_description" class="form-control" value="{{$items->item_description}}">
                        </div>
                </div>              
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Is Enabled :</label>
                    <div class="col-sm-8">
                        <label class="switch">
                             <input type="checkbox" name="is_visible" class="switch" value="{{$items->is_visible}}" checked/>
                              <span></span>
                        </label>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                <label for="group_name" class="control-label col-sm-4">From :</label>
                        <div class="col-sm-8">                           
                            <input type="text" name="item_valid_from" class="form-control datepicker" value="{{$items->item_valid_from}}">
                        </div>
                </div> 
                <div class="form-group" style="margin:5px">
                <label for="group_name" class="control-label col-sm-4">To :</label>
                        <div class="col-sm-8">                           
                            <input type="text" name="item_valid_till" class="form-control datepicker" value="{{$items->item_valid_till}}">
                        </div>
                </div> 
                <div class="form-group" style="margin:5px">
                <label for="group_name" class="control-label col-sm-4">Item Days :</label>
                        <div class="col-sm-8">                           
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
                <div class="form-group" style="margin:5px">
                         <label for="group_name" class="control-label col-sm-4">Meat Content :</label>
                      <div class="col-sm-8">      
                            <div class="form-group col-md-4">
                                <input type="radio" name="meat_content_type" @if($items->meat_content_type == 1) checked="checked" @endif value="1" id="veg"/>
                                <label class="control-label" for="veg">No Meat/Veg</label>
                            </div> 
                            <div class="form-group col-md-4">
                                <input type="radio" name="meat_content_type" @if($items->meat_content_type == 2) checked="checked" @endif value="2" id="egg"/>
                                <label class="control-label" for="egg">Contains Egg</label>
                            </div>
                            <div class="form-group col-md-4">
                                <input type="radio" name="meat_content_type" @if($items->meat_content_type == 3) checked="checked" @endif value="3" id="nonveg"/>
                                <label class="control-label" for="nonveg">Contains Meat</label>
                            </div>                           
                        </div>
                    </div> 
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Cuisine Type :</label>
                        <div class="col-sm-8">
                             <select multiple name="cuisine_id[]" class="form-control select">
                                    @foreach($cuisinetypes as $cuisine)
                                        <option value="{{$cuisine->id}}">{{$cuisine->cuisine_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Ingrediants :</label>
                        
                        <div class="col-sm-8 ingrediants">
                            <input type="text" name="item_ingredients[]" value="" class="form-control"/><br/>
                        </div>
                    </div>
                    <div class="form-group" style="margin:5px">
                         <label for="group_name" class="control-label col-sm-4">Filters :</label>
                      <div class="col-sm-8">      
                            <div class="form-group col-md-4">
                               <input type="checkbox" name="contains_nuts" value="1" id="contains_nuts"/>
                                <label class="control-label" for="contains_nuts">Contain Nuts</label>
                            </div> 
                            <div class="form-group col-md-4">
                                <input type="checkbox" name="dairy_free" value="1" id="dairy_free"/>
                                <label class="control-label" for="dairy_free">Dairy Free</label>
                            </div>
                            <div class="form-group col-md-4">
                                <input type="checkbox" name="gluten_free" value="1" id="gluten_free"/>
                                <label class="control-label" for="gluten_free">Gluten Free</label>
                            </div>
                            <div class="form-group col-md-4">
                                 <input type="checkbox" name="vegan" value="1" id="vegan"/>
                                <label class="control-label" for="vegan">Vegan</label>
                            </div>                           
                        </div>
                    </div>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Allergents Contain :</label>
                        <div class="col-sm-8">
                              <select multiple name="allergents_contain[]" class="form-control select">
                                @foreach($allergenttypes as $allergent)
                                    <option value="{{$allergent->allergent_id}}">{{$allergent->allergent_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Allergents May Contain :</label>
                        <div class="col-sm-8">
                             <select multiple name="allergents_may_contain[]" class="form-control select">
                                @foreach($allergenttypes as $allergent)
                                    <option value="{{$allergent->allergent_id}}">{{$allergent->allergent_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Display Order :</label>
                    <div class="col-sm-8">
                         <input type="number" name="display_order" class="form-control" value="{{$items->display_order}}" min="1" step="1">
                    </div>
                </div>
                </br>
            </div>
            
        <div class="form-group">
            <div class="panel-footer">
                <div class="col-md-6 col-md-offset-3">
                    {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
                    {{ link_to_route('items.index','Cancel',array('eatery_id'=>$eatery_id), array('class' => 'btn btn-danger')) }}
                </div>
            </div>
        </div>
     </div>
 </div>
 
@endsection