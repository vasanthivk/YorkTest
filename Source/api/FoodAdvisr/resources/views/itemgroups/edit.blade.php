@extends('layouts.master')
@section('title')
FoodAdvisr-Item Groups
@endsection
@section('module')
Item Groups
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}


{{ Form::open(array('method' => 'PUT', 'route' => array('itemgroups.update',$itemgroups->id),'files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahText('group_name','Group Name :',$itemgroups->group_name,array('maxlength' => '100'))  }}
		        <div class="form-group" style="margin:5px">
        			<label for="group_name" class="control-label col-sm-4">Is Enabled :</label>
    				<div class="col-sm-8">
        				<label class="switch">
                             <input type="checkbox" name="is_visible" class="switch" value="{{$itemgroups->is_visible}}" checked/>
                              <span></span>
                        </label>
    				</div>
				</div>
				<div class="form-group" style="margin:5px">
        			<label for="group_name" class="control-label col-sm-4">Display Order :</label>
    				<div class="col-sm-8">
        				 <input type="number" name="display_order" class="form-control" value="{{$itemgroups->display_order}}" min="1" step="1">
    				</div>
				</div>
		        </br>
		    </div>
		     <div class="row">
            <div class="col-md-4">
                    <div class="form-group">            
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php
                      $logo_path = '';
                     $no_image=env('NO_IMAGE');
                if(File::exists(env('CONTENT_ITEM_GROUP_PATH') . '/' . $itemgroups->id .  '.' . $itemgroups->logo_extension))
                {
                    $logo_path = env('CONTENT_ITEM_GROUP_PATH') . '/' . $itemgroups->id .  '.' . $itemgroups->logo_extension;
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
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('itemgroups.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 
@endsection