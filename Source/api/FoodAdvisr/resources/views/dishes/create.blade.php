@extends('layouts.master')
@section('title')
    FoodAdvisr-Items
@endsection
@section('module')
    Dish
@endsection

@section('content')


    {{ Form::open(array('route' => 'dishes.store','files'=>true)) }}
    <input type="hidden" id="eatery_id" name="eatery_id" value="{{$eatery_id}}">
    <div class="form-group form-horizontal">
        <div class="panel panel-default">
            <div class="col-md-10">
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Menu :</label>

                    <div class="col-sm-8">
                        <select multiple class="form-control select" data-live-search='true' id="menus_ids"
                                name="menus_ids">
                            <option>Choose Menu</option>
                            @foreach($menus as $menu)
                                <option value="{{$menu->ref}}">{{$menu->menu}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Section :</label>

                    <div class="col-sm-8">
                        <select multiple class="form-control select" data-live-search='true' id="sections_ids"
                                name="sections_ids">
                            <option>Choose Section</option>
                            @foreach($menusection as $section)
                                <option value="{{$section->id}}">{{$section->section_naem}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Sub Section :</label>

                    <div class="col-sm-8">
                        <select multiple class="form-control select" data-live-search='true' id="subsections_ids"
                                name="subsections_ids">
                            <option>Choose Sub-Section</option>
                            @foreach($menusubsection as $subsection)
                                <option value="{{$subsection->id}}">{{$subsection->sub_section_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Dish Name :</label>

                    <div class="col-sm-8">
                        <input type="text" value="" name="dish_name" class="form-control"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Price :</label>

                    <div class="col-sm-8">
                        <input type="number" value="" name="default_price" class="form-control" min="0" step="0.01"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Description :</label>

                    <div class="col-sm-8">
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label for="group_name" class="control-label col-sm-4">Upload Dish Image :</label>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php
                                $logo_path = '';
                                $no_image = env('NO_IMAGE');?>
                                <div class="fileinput-new thumbnail" style="width: 130px; height: 111px;">
                                    <a>
                                        <img src="../../<?php echo $no_image ?>" alt="..."
                                             style="width: 130px; height: 111px;">
                                    </a>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"
                                     style="max-width: 130px; max-height: 111px;"></div>
                                <div>
                        <span class="btn btn-primary btn-file"><span class="fileinput-new">Change Image</span><span
                                    class="fileinput-exists">Change</span>
                        <input type="file" name="logo" id="logo">
                        </span>
                                    <a href="#" class="btn btn-default fileinput-exists"
                                       data-dismiss="fileinput">Remove</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-2 control-label">Is Enabled</label>

                    <div class="col-md-10">
                        <label class="switch">
                            <input type="checkbox" name="is_visible" checked value="1"/>
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
                        <input type="text" name="valid_from" class="form-control datepicker" value=""/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">To :</label>

                    <div class="col-sm-4">
                        <input type="text" name="valid_till" class="form-control datepicker" value=""/>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Applicable Days :</label>

                    <div class="col-sm-8">
                        <select multiple name="applicable_days[]" class="form-control select">
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
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Life Style Choices :</label>

                    <div class="col-sm-8">
                        <select multiple name="lifestyle_choices_ids[]" class="form-control select">
                            @foreach($lifestyle_choices as $lifestyle)
                                <option value="{{$lifestyle->id}}">{{$lifestyle->description}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Ingredients :</label>

                    <div class="col-sm-8">
                        <select multiple name="ingredients_ids[]" data-live-search='true' class="form-control select">
                            @foreach($ingredients as $ingredient)
                                <option value="{{$ingredient->ref}}">{{$ingredient->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Cuisine Type :</label>

                    <div class="col-sm-8">
                        <select multiple name="cuisines_ids[]" data-live-search='true' class="form-control select">
                            @foreach($cuisinetypes as $cuisine)
                                <option value="{{$cuisine->id}}">{{$cuisine->cuisine_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Allergens Contain :</label>

                    <div class="col-sm-8">
                        <select multiple name="allergens_contain_ids[]" data-live-search='true'
                                class="form-control select">
                            @foreach($allergentypes as $allergen)
                                <option value="{{$allergen->ref}}">{{$allergen->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Allergens May Contain :</label>

                    <div class="col-sm-8">
                        <select multiple name="allergents_may_contain[]" data-live-search='true'
                                class="form-control select">
                            @foreach($allergentypes as $allergen)
                                <option value="{{$allergen->ref}}">{{$allergen->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr/>

                <div class="form-group">
                    <label class="col-md-2 control-label">Is Featured</label>

                    <div class="col-md-10">
                        <label class="switch">
                            <input type="checkbox" name="is_featured" checked value="1"/>
                            <span></span>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-2 control-label">Is New</label>

                    <div class="col-md-10">
                        <label class="switch">
                            <input type="checkbox" name="is_new" checked value="1"/>
                            <span></span>
                        </label>
                    </div>
                </div>
                <hr/>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">New Till Date :</label>

                    <div class="col-sm-4">
                        <input type="text" name="new_till_date" class="form-control datepicker" value="">
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
                        {{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
                        {{ link_to_route('dishes.index','Cancel',array('eatery_id'=>$eatery_id), array('class' => 'btn btn-danger')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection