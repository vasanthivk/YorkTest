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
            <div class="col-md-10">
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Category :</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="category_id" name="category_id">
                            <option>Choose Item Category</option>
                            @foreach($category as $categories)
                            <option value="{{$categories->id}}" @if($items->category_id == $categories->id) selected="selected" @endif>{{$categories->category_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                {{ Form::ahText('item_name','Item Name :',$items->item_name,array('maxlength' => '100'))  }}
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Item Price :</label>
                    <div class="col-sm-8">
                        <input type="number" name="item_default_price" class="form-control" value="{{$items->item_default_price}}" min="0"
                               step="0.01">
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Item Description :</label>
                    <div class="col-sm-8">
                       <input type="text" name="item_description" class="form-control" value="{{$items->item_description}}">
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label for="group_name" class="control-label col-sm-4">Upload Item Image :</label>
                    <div class="col-md-4">
                    <div class="form-group">            
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php
                      $logo_path = '';
                     $no_image=env('NO_IMAGE');
                if(File::exists(env('CONTENT_ITEM_PATH') . '/' . $items->id .  '.' . $items->logo_extension))
                {
                    $logo_path = env('CONTENT_ITEM_PATH') . '/' . $items->id .  '.' . $items->logo_extension;
                 ?>
                            <div class="fileinput-new thumbnail" style="width: 130px; height: 111px;">
                            <a>
                                <img src="../../<?php echo $logo_path ?>" alt="..." style="width: 130px; height: 102px;">
                                </a>
                            </div>
                            <?php } else { ?>
                            <div class="fileinput-new thumbnail" style="width: 130px; height: 111px;">
                            <a>
                                <img src="../../<?php echo $no_image ?>" alt="..." style="width: 130px; height: 111px;">
                                </a>
                            </div>
                             <?php } ?>
                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 130px; max-height: 111px;"></div>
                    <div>
                        <span class="btn btn-primary btn-file"><span class="fileinput-new">Change Image</span><span class="fileinput-exists">Change</span>
                        <input type="file" name="logo" id="logo">
                        </span>
                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>

                        </div>
                    </div>
                </div>  
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Is Enabled :</label>
                    <div class="col-sm-8">
                        <label class="switch">
                             <input type="checkbox" name="is_visible" class="switch" value="{{$items->is_visible}}" checked/>
                              <span></span>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="col-sm-4 control-label">Date Range</label>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">From :</label>
                    <div class="col-sm-4">
                        <input type="text" name="item_valid_from" class="form-control datepicker" value="{{$items->item_valid_from}}">
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">To :</label>
                    <div class="col-sm-4">
                        <input type="text" name="item_valid_till" class="form-control datepicker" value="{{$items->item_valid_till}}">
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                <label for="group_name" class="control-label col-sm-4">Item Days :</label>
                        <div class="col-sm-8">                           
                            <select multiple name="item_applicable_days[]" class="form-control select">
                                <option value="0" @if(in_array(0,$item_applicable_days)) selected="selected" @endif>Sunday</option>
                                <option value="1" @if(in_array(1,$item_applicable_days)) selected="selected" @endif>Monday</option>
                                <option value="2" @if(in_array(2,$item_applicable_days)) selected="selected" @endif>Tuesday</option>
                                <option value="3" @if(in_array(3,$item_applicable_days)) selected="selected" @endif>Wednesday</option>
                                <option value="4" @if(in_array(4,$item_applicable_days)) selected="selected" @endif>Thursday</option>
                                <option value="5" @if(in_array(5,$item_applicable_days)) selected="selected" @endif>Friday</option>
                                <option value="6" @if(in_array(6,$item_applicable_days)) selected="selected" @endif>Saturday</option>
                            </select>
                        </div>
                </div>
                <hr/>
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
                <hr/>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Cuisine Type :</label>
                        <div class="col-sm-8">
                             <select multiple name="cuisine_id[]" class="form-control select">
                                    @foreach($cuisinetypes as $cuisine)
                                        <option value="{{$cuisine->id}}" @if(in_array($cuisine->id,$cuisine_id)) selected="selected" @endif>{{$cuisine->cuisine_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                <hr/>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Ingrediants :</label>
                        
                        <div class="col-sm-8 ingrediants">
                            {{--@foreach($item_ingredients as $ingredient)--}}
                            <input type="text" name="item_ingredients[]" value="{{--{{$ingredient}}--}}" class="form-control"/><br/>
                            {{--@endforeach--}}
                        </div>
                    </div>
                <hr/>
                    <div class="form-group" style="margin:5px">
                         <label for="group_name" class="control-label col-sm-4">Filters :</label>
                      <div class="col-sm-8">      
                            <div class="form-group col-md-4">
                               <input type="checkbox" name="contains_nuts" @if($items->contains_nuts == 1) checked @endif value="{{$items->contains_nuts}}" id="contains_nuts"/>
                                <label class="control-label" for="contains_nuts">Contain Nuts</label>
                            </div> 
                            <div class="form-group col-md-4">
                                <input type="checkbox" name="dairy_free"  @if($items->dairy_free == 1) checked @endif value="{{$items->dairy_free}}" id="dairy_free"/>
                                <label class="control-label" for="dairy_free">Dairy Free</label>
                            </div>
                            <div class="form-group col-md-4">
                                <input type="checkbox" name="gluten_free" @if($items->gluten_free == 1) checked @endif value="{{$items->gluten_free}}" id="gluten_free"/>
                                <label class="control-label" for="gluten_free">Gluten Free</label>
                            </div>
                            <div class="form-group col-md-4">
                                 <input type="checkbox" name="vegan" @if($items->vegan == 1) checked @endif value="{{$items->vegan}}" id="vegan"/>
                                <label class="control-label" for="vegan">Vegan</label>
                            </div>                           
                        </div>
                    </div>
                <hr/>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Allergents Contain :</label>
                        <div class="col-sm-8">
                              <select multiple name="allergents_contain[]" class="form-control select">
                                @foreach($allergenttypes as $allergent)
                                    <option value="{{$allergent->id}}" @if(in_array($allergent->id,$allergents_contain)) selected="selected" @endif>{{$allergent->allergent_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                <hr/>
                    <div class="form-group" style="margin:5px">
                        <label for="group_name" class="control-label col-sm-4">Allergents May Contain :</label>
                        <div class="col-sm-8">
                             <select multiple name="allergents_may_contain[]" class="form-control select">
                                @foreach($allergenttypes as $allergent)
                                    <option value="{{$allergent->id}}" @if(in_array($allergent->id,$allergents_may_contain)) selected="selected" @endif>{{$allergent->allergent_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                <hr/>
                    
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Display Order :</label>
                    <div class="col-sm-8">
                         <input type="number" name="display_order" class="form-control" value="{{$items->display_order}}" min="1" step="1">
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-2 control-label">Nutrition Levels</label>
                </div>
                @foreach($nutritiontypes as $nutrition)
                    <div class="form-group">
                        <label for="group_name"  class="control-label col-sm-4">{{$nutrition->nutrition_type}}</label>
                        <div class="col-sm-4">
                            <input type="number" name="nutrition_from[{{$nutrition->id}}]" nutrition_id="{{$nutrition->id}}" value="" class="form-control" min="0" max="100" step="1" />
                        </div>
                        <div class="col-sm-4">
                            <input type="number" name="nutrition_to[{{$nutrition->id}}]" nutrition_id="{{$nutrition->id}}" value="" class="form-control" min="0" max="100" step="1" />
                        </div>
                    </div>
                @endforeach
                <br/>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>