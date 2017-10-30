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

{{ Form::open(array('method' => 'PUT', 'route' => array('dishes.update',$dish->id),'files'=>true)) }}
<input type="hidden" id="eatery_id" name="eatery_id" value="{{$eatery_id}}">
<div class="form-group form-horizontal">
        <div class="panel panel-default">
            <div class="col-md-10">
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Menu :</label>
                    <div class="col-sm-8">
                        <select multiple class="form-control select" data-live-search='true' id="menus_ids"  name="menus_ids">
                            <option>Choose Menu</option>
                            @foreach($menus as $menu)
                                <option value="{{$menu->ref}}"  @if(isset($menus_ids) && !empty($menus_ids)) @if(in_array($menu->ref,array($menus_ids))) selected="selected" @endif @endif  >{{$menu->menu}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Section :</label>
                    <div class="col-sm-8">
                        <select multiple class="form-control select" data-live-search='true' id="sections_ids"  name="sections_ids">
                            <option>Choose Section</option>
                            @foreach($menusection as $section)
                                <option value="{{$section->id}}" @if(isset($sections_ids) && !empty($sections_ids)) @if(in_array($section->id,array($sections_ids))) selected="selected" @endif @endif >{{$section->section_naem}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Sub Section :</label>
                    <div class="col-sm-8">
                        <select multiple class="form-control select" data-live-search='true' id="subsections_ids"  name="subsections_ids">
                            <option>Choose Sub-Section</option>
                            @foreach($menusubsection as $subsection)
                                <option value="{{$subsection->id}}" @if(isset($subsections_ids) && !empty($subsections_ids)) @if(in_array($subsection->id,array($subsections_ids))) selected="selected" @endif @endif >{{$subsection->sub_section_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                {{ Form::ahText('dish_name','Dish Name :',$dish->dish_name,array('maxlength' => '100'))  }}
                <hr/>
                {{ Form::ahNumber('default_price','Price :',$dish->default_price,array('maxlength' => '100','min' => '0', 'step' => '0.01'))  }}
                <hr/>
                {{ Form::ahTextarea('description','Description :',$dish->description,array())  }}
                <hr/>
                <div class="form-group">
                    <label for="group_name" class="control-label col-sm-4">Upload Dish Image :</label>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php
                                $logo_path = '';
                                $no_image=env('NO_IMAGE');
                                if(File::exists(env('CONTENT_ITEM_PATH') . '/' . $dish->id .  '.' . $dish->LogoExtension))
                                {
                                $logo_path = env('CONTENT_ITEM_PATH') . '/' . $dish->id .  '.' . $dish->LogoExtension ;
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
                                <div class="fileinput-new thumbnail" style="width: 130px; height: 111px;">
                                    <a>
                                        <img src="../../<?php echo $no_image ?>" alt="..." style="width: 130px; height: 111px;">
                                    </a>
                                </div>
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
                {{ Form::ahSwitch('is_visible','Is Enabled :',null,array('checked')) }}
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="col-sm-4 control-label">Date Range</label>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">From :</label>
                    <div class="col-sm-4">
                        <input type="text" name="valid_from" class="form-control datepicker" value="{{$dish->valid_from}}">
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">To :</label>
                    <div class="col-sm-4">
                        <input type="text" name="valid_till" class="form-control datepicker" value="{{$dish->valid_till}}">
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Applicable Days :</label>
                    <div class="col-sm-8">
                        <select multiple name="applicable_days[]" class="form-control select">
                            <option value="0"  @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(0,array($applicable_days))) selected="selected" @endif @endif>Sunday</option>
                            <option value="1" @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(1,array($applicable_days))) selected="selected" @endif @endif>Monday</option>
                            <option value="2"  @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(2,array($applicable_days))) selected="selected" @endif @endif>Tuesday</option>
                            <option value="3"  @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(3,array($applicable_days))) selected="selected" @endif @endif>Wednesday</option>
                            <option value="4"  @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(4,array($applicable_days))) selected="selected" @endif @endif>Thursday</option>
                            <option value="5"  @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(5,array($applicable_days))) selected="selected" @endif @endif>Friday</option>
                            <option value="6"  @if(isset($applicable_days) && !empty($applicable_days)) @if(in_array(6,array($applicable_days))) selected="selected" @endif @endif>Saturday</option>
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Life Style Choices :</label>
                    <div class="col-sm-8">
                        <select multiple name="lifestyle_choices_ids[]" class="form-control select">
                            @foreach($lifestyle_choices as $lifestyle)
                                <option value="{{$lifestyle->id}}" @if(isset($lifestyle_choices_ids) && !empty($lifestyle_choices_ids)) @if(in_array($lifestyle->id,array($lifestyle_choices_ids))) selected="selected" @endif @endif>{{$lifestyle->description}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Ingredients :</label>
                    <div class="col-sm-8">
                        <table>
                            @foreach($ingredients as $ingredient)
                                <tr>
                                    <td><input type="hidden" value="{{$ingredient->id}}" name="ingredients_ids[]" class="form-control ingredients_ids col-md-4" /></td>
                                    <td><input type="text" value="{{$ingredient->name}}" name="" class="form-control ingredients_ids col-md-4" /></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><a class="btn btn-primary" id="add_ingredient">Add</a></td>
                                <td><input type="text" value="" name="new_ingredients_ids[]" class="form-control ingredients_ids col-md-4" /></td>
                            </tr>
                        </table>
                        <table id="new_ingredients_ids">

                        </table>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Cuisine Type :</label>
                    <div class="col-sm-8">
                        <select multiple name="cuisines_ids[]" data-live-search='true' class="form-control select">
                            @foreach($cuisinetypes as $cuisine)
                                <option value="{{$cuisine->id}}"  @if(isset($cuisines_ids) && !empty($cuisines_ids)) @if(in_array($cuisine->id,array($cuisines_ids))) selected="selected" @endif @endif>{{$cuisine->cuisine_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Allergens Contain :</label>
                    <div class="col-sm-8">
                        <select multiple name="allergens_contain_ids[]" data-live-search='true' class="form-control select">
                            @foreach($allergentypes as $allergen)
                                <option value="{{$allergen->ref}}"  @if(isset($allergens_contain_ids) && !empty($allergens_contain_ids)) @if(in_array($allergen->ref,array($allergens_contain_ids))) selected="selected" @endif @endif>{{$allergen->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Allergens May Contain :</label>
                    <div class="col-sm-8">
                        <select multiple name="allergents_may_contain[]" data-live-search='true' class="form-control select">
                            @foreach($allergentypes as $allergen)
                                <option value="{{$allergen->ref}}"  @if(isset($allergens_may_contain) && !empty($allergens_may_contain)) @if(in_array($allergen->ref,array($allergens_may_contain))) selected="selected" @endif @endif>{{$allergen->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                {{ Form::ahSwitch('is_featured','Is Featured :',null,array('checked')) }}
                <hr/>
                {{ Form::ahSwitch('is_new','Is New :',null,array('checked')) }}
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">New Till Date :</label>
                    <div class="col-sm-4">
                        <input type="text" name="new_till_date" class="form-control datepicker" value="{{$dish->new_till_date}}">
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="col-sm-4 control-label">Nutrition :</label>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Total Fat :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_fat" class="form-control" value="{{$dish->nutrition_fat}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Cholesterol :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_cholesterol" class="form-control" value="{{$dish->nutrition_cholesterol}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Sugar :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_sugar" class="form-control" value="{{$dish->nutrition_sugar}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Fibre :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_fibre" class="form-control" value="{{$dish->nutrition_fibre}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Protein :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_protein" class="form-control" value="{{$dish->nutrition_protein}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Saturated Fat :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_saturated_fat" class="form-control" value="{{$dish->nutrition_saturated_fat}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Calories :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_calories" class="form-control" value="{{$dish->nutrition_calories}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Carbs :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_carbohydrates" class="form-control" value="{{$dish->nutrition_carbohydrates}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Salt :</label>

                    <div class="col-sm-4">
                        <input type="number" name="nutrition_salt" class="form-control" value="{{$dish->nutrition_salt}}" min="0" step="0.0001"/>
                    </div>
                </div>

                <!-- <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Display Order :</label> -->
                <!--  <div class="col-sm-8"> -->
                <input type="hidden" name="display_order" class="form-control" value="1" min="1" step="1">
                <!--   </div>
              </div> -->
                <!--  <hr/> -->

            </div>
            
            <div class="form-group">
                <div class="panel-footer">
                    <div class="col-md-6 col-md-offset-3">
                        {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
                        {{ link_to_route('dishes.index','Cancel',array('eatery_id'=>$eatery_id), array('class' => 'btn btn-danger')) }}
                    </div>
                </div>
            </div>
     </div>
 </div>
 
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>