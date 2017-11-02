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
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}
{{Form::component('ahDate', 'components.form.date', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahReadonly', 'components.form.readonly', ['name', 'labeltext'=>null, 'value' => null])}}

{{ Form::open(array('method' => 'PUT', 'route' => array('dishes.update',$dish->id),'files'=>true)) }}
<input type="hidden" id="eatery_id" name="eatery_id" value="{{$eatery_id}}">
 <?php 
            if(isset($cuisines) || isset($lifestyle_choices))
            {       
             
            $cuisines_ids = Session::get('cuisines');
            $lifestyle_choices_ids = Session::get('lifestyle_choices');
            $allergens_contain_ids = Session::get('allergens_contain_ids');
            $applicabledays = Session::get('applicable_days');
            $allergens_may_contain = Session::get('allergens_may_contain');
            }
            $cuisines_ids = $dish->cuisines_ids;
            $cuisines_ids=explode(",",$cuisines_ids);
            $lifestyle_choices_ids = $dish->lifestyle_choices_ids;
            $lifestyle_choices_ids=explode(",",$lifestyle_choices_ids);
            $applicabledays = $dish->applicable_days;
            $applicabledays=explode(",",$applicabledays);
            $allergens_contain_ids = $dish->allergens_contain_ids;
            $allergens_contain_ids=explode(",",$allergens_contain_ids);
            $allergens_may_contain = $dish->allergens_may_contain;
            $allergens_may_contain=explode(",",$allergens_may_contain);

            ?>
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
            <div class="col-md-6">   
                <div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4">Groups :</label>
                     <div class="col-sm-8">
                            <select class="form-control" id="group_id" name="group_id">
                                <option value="0">Please select group</option>
                                @if(isset($groups) && !empty($groups) && $groups != '')
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}"  <?php 
                                $val = $group->id;
                                $res = $dish->group_id;
                               if($val == $res) 
                                {
                                  ?> 
                                  selected="selected"
                                  <?php 
                                } ?>>  
                                  {{ $group->description }}</option>
                                @endforeach
                                @endif
                            </select>
                       </div>
                </div>             
                {{ Form::ahSelect('menus_ids','Menu Name :',$dish->menus_ids,$menus) }}
                {{ Form::ahSelect('sections_ids','Section Name :',$dish->sections_ids,$menusection) }}
                {{ Form::ahSelect('subsections_ids','Sub Section Name :',null,$menusubsection) }}
                 {{ Form::ahText('dish_name','Dish Name :',$dish->dish_name,array('maxlength' => '100'))  }}
                 {{ Form::ahNumber('default_price','Price :',$dish->default_price,array('min'=>'0','maxlength' => '20'))  }}
                 {{ Form::ahTextarea('description','Description :',$dish->description,array('size' => '30x5'))  }}
                 {{ Form::ahSwitch('is_visible','Is Enabled :',null,$dish->is_visible) }}
                 {{ Form::ahSwitch('is_featured','Is Featured :',null,$dish->is_featured) }}
                 {{ Form::ahDate('valid_from','From :',$dish->valid_from) }}
                 {{ Form::ahDate('valid_till','To :', $dish->valid_till) }}
                  
                  <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Applicable Days :</label>
                    <div class="col-sm-8">
                        <select multiple name="applicable_days[]" class="form-control select">
                            <?php 
                                foreach ($applicable_days as $key => $value) { ?>
                                <option value="{{$key}}" <?php if(in_array($key, $applicabledays)){
                                    echo "selected";
                                }?>>{{$value}}</option>
                                <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
             
           
            <div class="form-group" style="margin:5px">
                  <label for="cuisine" class="control-label col-sm-4">Cuisines :</label>
                  <div class="col-md-8">
                      <select multiple name="cuisines_ids[]" data-live-search='true' class="form-control select">
                      @foreach($cuisines as $cuisine)
                          <option value="{{$cuisine->id}}" @if(isset($cuisines_ids)) @if(in_array($cuisine->id,$cuisines_ids)) selected="selected" @endif @endif>{{$cuisine->cuisine_name}}</option>
                      @endforeach
                      </select>
                  </div>
            </div>             
            <div class="form-group" style="margin:5px">
                  <label for="lifestyle_choices" class="control-label col-sm-4">Lifestyle Choices :</label>
                  <div class="col-md-8">
                      <select multiple name="lifestyle_choices_ids[]" id="lifestyle_choices_ids[]" class="form-control select">
                      @foreach($lifestyle_choices as $lifestyle_choice)
                      <option value="{{$lifestyle_choice->id}}" @if(isset($lifestyle_choices_ids)) @if(in_array($lifestyle_choice->id,$lifestyle_choices_ids)) selected="selected" @endif @endif>{{$lifestyle_choice->description}}</option>
                      @endforeach
                      </select>
                  </div>
            </div> 
            <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Allergens Contain :</label>

                    <div class="col-sm-8">
                        <select multiple name="allergens_contain_ids[]" data-live-search='true' class="form-control select">
                    @foreach($allergentypes as $allergen)
                          <option value="{{$allergen->ref}}" @if(isset($allergens_contain_ids)) @if(in_array($allergen->ref,$allergens_contain_ids)) selected="selected" @endif @endif>{{$allergen->title}}</option>
                      @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Allergens May Contain :</label>
                    <div class="col-sm-8">
                        <select multiple name="allergens_may_contain[]" data-live-search='true'
                                class="form-control select">
                            @foreach($allergentypes as $allergen)
                                 <option value="{{$allergen->ref}}" @if(isset($allergens_may_contain)) @if(in_array($allergen->ref,$allergens_may_contain)) selected="selected" @endif @endif>{{$allergen->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{ Form::ahSwitch('is_new','Is New :',null,$dish->is_new) }} 
                {{ Form::ahDate('new_till_date','New Till Date :', $dish->new_till_date) }}
                </br>
            </div>
            <div class="col-md-6">
                {{ Form::ahReadonly('eatery_id','Eatery :',$eateries->business_name,array('maxlength' => '100'))  }}
                <br/>
                <div class="form-group" style="margin:5px">
                    <label for="item_ingredients" class="control-label col-sm-4">Ingredients :</label>                  

                   <?php if(count($ingredients) > 0 ) {?>
                        <div class="col-sm-6 ingrediants">
                            @foreach($ingredients as $ingredient)
                        <input class="form-control" maxlength="100" name="item_ingredients[]" type="text" value="{{$ingredient->name}}" id="item_ingredients">
                         @endforeach
                    </div>
                    <?php } 
                    else {
                    ?>
                    <div class="col-sm-6 ingrediants">
                        <input class="form-control" maxlength="100" name="item_ingredients[]" type="text" value="" id="item_ingredients">
                    </div>
                    <?php } ?>
                    <div class="col-sm-2">
                        <a class="btn btn-primary" id="ingrediant_add">Add</a>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="col-sm-4 control-label">Nutrition :</label>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Total Fat :</label>
                    <div class="col-sm-8">
                        <input type="number" name="nutrition_fat" class="form-control" value="{{$dish->nutrition_fat}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Cholesterol :</label>
                    <div class="col-sm-8">
                        <input type="number" name="nutrition_cholesterol" class="form-control" value="{{$dish->nutrition_cholesterol}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Sugar :</label>

                    <div class="col-sm-8">
                        <input type="number" name="nutrition_sugar" class="form-control" value="{{$dish->nutrition_sugar}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Fibre :</label>

                    <div class="col-sm-8">
                        <input type="number" name="nutrition_fibre" class="form-control" value="{{$dish->nutrition_fibre}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Protein :</label>

                    <div class="col-sm-8">
                        <input type="number" name="nutrition_protein" class="form-control" value="{{$dish->nutrition_protein}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Saturated Fat :</label>

                    <div class="col-sm-8">
                        <input type="number" name="nutrition_saturated_fat" class="form-control" value="{{$dish->nutrition_saturated_fat}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Calories :</label>

                    <div class="col-sm-8">
                        <input type="number" name="nutrition_calories" class="form-control" value="{{$dish->nutrition_calories}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Carbs :</label>

                    <div class="col-sm-8">
                        <input type="number" name="nutrition_carbohydrates" class="form-control" value="{{$dish->nutrition_carbohydrates}}" min="0" step="0.0001"/>
                    </div>
                </div>
                <div class="form-group" style="margin:5px">
                    <label for="group_name" class="control-label col-sm-4">Upload Dish Image :</label>
                   <div class="col-md-8">
                    <div class="form-group">            
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php
                      $logo_path = '';
                     $no_image=env('NO_IMAGE');
                if(File::exists(env('CONTENT_ITEM_PATH') . '/' . $dish->id .  '.' . $dish->logo_extension))
                {
                    $logo_path = env('CONTENT_ITEM_PATH') . '/' . $dish->id .  '.' . $dish->logo_extension ;
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
                        <span class="btn btn-primary btn-file"><span class="fileinput-new">Change Logo</span><span class="fileinput-exists">Change Logo</span>
                        <input type="file" name="logo" id="logo">
                        </span>
                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>

                        </div>
                    </div>
                </div> 
                </div>
            </div>
            
            <div class="form-group">
                <div class="panel-footer">
                    <div class="col-md-6 col-md-offset-3">
                        {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
                        {{ link_to_route('dishes.index','Cancel',null, array('class' => 'btn btn-danger')) }}
                    </div>
                </div>
            </div>
     </div>
 </div>

<script>
        $(document).ready(function(){
             $('#ingrediant_add').on('click',function(){
           $('.ingrediants').append('<br/><input type="text" name="item_ingredients[]" value="" class="form-control" />');
        });
        $('.del_remove').on('click',function(){
           $(this).remove();
        });
        });
    </script>  

    <script type="text/javascript">
    $(document).ready(function(){   
    
        
        $("#menus_ids").change(function(){
          menuChange();
        });
        $("#sections_ids").change(function(){
            fillSubSections();
        });
        menuChange();
        var value ='<?php echo Session::get('sections_ids') ?>';
        $("select#sections_ids option") .each(function() {
            this.selected = (this.value == '<?php echo Session::get('sections_ids') ?>'); 
        });
        $("select#subsections_ids option") .each(function() { this.selected = (this.value == '<?php echo Session::get('subsections_ids') ?>'); });

    });
    function menuChange(){

  var id = $("#menus_ids").val();
            $.get("../../api/getmenusectionbymenuIds?menu_id="+id, function(data){
                $('#sections_ids').empty();
                var section_value ='<?php echo Session::get('section_ids') ?>';
                $.each(data, function(i, obj){
                    if( section_value == obj.sections_ids) {
                    $('#sections_ids').append($('<option selected>').text(obj.section_name).attr('value', obj.sections_ids)); 
                }
                else{
                 $('#sections_ids').append($('<option>').text(obj.section_name).attr('value', obj.sections_ids)); 

                }

                });
                fillSubSections();
            });

    }
    function fillSubSections()
        {
            var id = $("#sections_ids").val();
            $.get("../../api/getmenusubsectionbymenusection?section_id="+id, function(data){
                $('#subsections_ids').empty();
                var subsection_value ='<?php echo Session::get('subsections_ids') ?>';
                $.each(data, function(i, obj){
                    if( subsection_value == obj.subsections_ids) {
                         $('#subsections_ids').append($('<option selected>').text(obj.sub_section_name).attr('value', obj.subsections_ids));    

                    }
                    else 
                    {
                       $('#subsections_ids').append($('<option>').text(obj.sub_section_name).attr('value', obj.subsections_ids));       
                   }             
                });
            });
        }
</script>   
@endsection
