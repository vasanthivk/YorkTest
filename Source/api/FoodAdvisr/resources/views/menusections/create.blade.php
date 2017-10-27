@extends('layouts.master')
@section('title')
FoodAdvisr-Item Category
@endsection
@section('module')
Item Category
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}


{{ Form::open(array('route' => 'itemcategory.store','files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahText('category_name','Category Name :','',array('maxlength' => '100'))  }}
            {{ Form::ahText('description','Description :','',array('maxlength' => '100'))  }}
             {{ Form::ahSelect('group_id','Group Name :','',$itegroups)  }}
		       <div class="form-group" style="margin:5px">
        			<label for="category_name" class="control-label col-sm-4">Is Enabled :</label>
    				<div class="col-sm-8">
        				<label class="switch">
                             <input type="checkbox" name="is_visible" class="switch" value="1" checked/>
                              <span></span>
                        </label>
    				</div>
				</div>
				<div class="form-group" style="margin:5px">
        			<label for="category_name" class="control-label col-sm-4">Display Order :</label>
    				<div class="col-sm-8">
        				 <input type="number" name="display_order" class="form-control" value="" min="1" step="1">
    				</div>
				</div>
		        </br>
		    </div>
		     <div class="row">         
          <?php                
                $no_image=env('NO_IMAGE');
                ?>
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
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('itemcategory.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection