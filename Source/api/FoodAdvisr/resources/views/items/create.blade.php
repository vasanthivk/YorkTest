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
 {{Form::open(array('route' => 'items.store','files'=>true, 'role'=>'form', 'class'=>'form-horizontal', 'id'=>'wizard-validation'))}}
 <input type="hidden" name="eatery_id" value="{{$eatery_id}}">
<div class="form-group form-horizontal">
        <div class="panel panel-default">
            <div class="col-md-12">
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
                                                <option value="{{$group->id}}">{{$group->group_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">( Or )</label>
                                    <div class="col-md-4">
                                            <input type="checkbox" id="new_group" name="new_group">
                                             Add a New Group
                                    </div>
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
                                    <div class="col-md-5 itemcategory_div">
                                        <select name="itemcategory"  class="form-control select">
                                            <option>Choose Item Category</option>
                                            @foreach($itemcategories as $categories)
                                                <option value="{{$categories->id}}">{{$categories->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">( Or )</label>
                                    <div class="col-md-4">
                                        <input type="checkbox" id="new_category" name="new_category">
                                        Add a New Category
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Category Name :</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="itemCategoryName" id="itemCategoryName" placeholder="Category Name">
                                    </div>
                                </div>
                            </div>
                            <div id="step-3">
                                <h4>Add an Item</h4>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Item Name</label>
                                    <div class="col-md-4">
                                        <input type="text" name="item_name" class="form-control" value="">
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Item Price</label>
                                    <div class="col-md-4">
                                        <input type="number" name="item_default_price" class="form-control" value="" min="0" step="0.01">
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
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Cuisines</label>
                                    <div class="col-md-5">
                                        <select multiple name="cuisine_id[]" class="form-control select">
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
                                            <input type="radio" name="meat_content_type" value="1" id="veg"/>
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
                                    <label class="col-md-2 control-label">Allergents Contain</label>
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
                                    <label class="col-md-2 control-label">Allergents May Contain</label>
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
                                    <div class="col-md-2">
                                        <input type="number" name="nutrition_from[{{$nutrition->id}}]" nutrition_id="{{$nutrition->id}}" value="" class="form-control" min="0" max="100" step="1" />
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="nutrition_to[{{$nutrition->id}}]" nutrition_id="{{$nutrition->id}}" value="" class="form-control" min="0" max="100" step="1" />
                                    </div>
                                </div>
                              @endforeach
                                <hr/>
                               <div class="form-group">
                                <label class="col-md-2 control-label">Display Order</label>
                                <div class="col-md-2">
                                    <input type="number" name="display_order" class="form-control" value="" min="1" step="1">
                                </div>
                             </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection

<script>
    $(document).ready(function(){
        $('#itemGroupName').attr('disabled','disabled');
        $('#itemCategoryName').attr('disabled','disabled');
        $('input[name="new_group"]').on('click',function(){
           if($(this).is(':checked')){
               $('#itemGroupName').removeAttr('disabled');
               $('select[name="itemgroup"]').attr('disabled','disabled');
               $('select[name="itemgroup"]').val();
               $('input[name="new_category"]').attr('checked','checked');
               $('#itemCategoryName').removeAttr('disabled');
               $('select[name="itemcategory"]').attr('disabled','disabled');
               $('.itemcategory_div').attr('style','display:none;');
               $('select[name="itemcategory"]').val();
               $('input[name="new_category"]').attr('style','display:none;');
           }else{
               $('#itemGroupName').attr('disabled','disabled');
               $('#itemGroupName').val();
               $('select[name="itemgroup"]').removeAttr('disabled');
               $('#itemCategoryName').attr('disabled','disabled');
               $('#itemCategoryName').val();
               $('select[name="itemcategory"]').removeAttr('disabled');
               $('.itemcategory_div').removeAttr('style');
               $('input[name="new_category"]').removeAttr('style');
               $('input[name="new_category"]').removeAttr('checked');
           }
        });

        $('input[name="new_category"]').on('click',function(){
            if($(this).is(':checked')){
                $('#itemCategoryName').removeAttr('disabled');
                $('select[name="itemcategory"]').attr('disabled','disabled');
                $('select[name="itemcategory"]').val();
            }else{
                $('#itemCategoryName').attr('disabled','disabled');
                $('#itemCategoryName').val();
                $('select[name="itemcategory"]').removeAttr('disabled');
            }
        });
        $('#ingrediant_add').on('click',function(){
           $('.ingrediants').append('<input type="text" name="ingrediant_names[]" value="" class="form-control" /><!--<a class="btn btn-danger  col-md-1 del_remove">X</a>--><br/>');
        });
        $('.del_remove').on('click',function(){
            alert();
           $(this).remove();
        });
    });
</script>