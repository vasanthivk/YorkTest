@extends('layouts.master')
@section('title')
FoodAdvisr-Eateries
@endsection
@section('module')
Eateries
@endsection

@section('content')
@include('components.message')
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

@if(Session::get("role_id")==1)
{{ Form::open(array('method' => 'GET','route' => 'eateries.index')) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
           <div class="col-md-6">                            
               <div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4">Location :</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="location_id" name="location_id">
                            <option  selected disabled>Please select location</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                               <?php 
                                $val = $location_id;
                                $res = $location->id;
                               if($val == $res) 
                                {
                                  ?> selected="selected"
                                  <?php 
                                } ?>> 
                              {{ $location->location_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                      
               <br/>
            </div>
            <div class="col-md-4" style="padding-top: 5px;"> 
                 {{ Form::submit('Show', array('class' => 'btn btn-primary')) }}
                </br>
            </div> 
     </div>
 </div>
 {{ Form::close() }}
 @endif
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">
                                @if($privileges['Add']=='true')           
                                    <div class="btn-group pull-left">                      
                                        <a href="{{URL::to('eateries/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Eateries</a>
                                    </div>
                                    @endif
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Business Name</th>
                                                <th>Business Type</th>  
                                                <th>Edit/Delete</th>              
                                            </tr>
                                        </thead>
                                        <tbody>
                                              @foreach($all_eateries as $eatery)
                                    <tr>
                                        <td>
                                            
                        <?php
                      $logo_path = '';
                     $no_image=env('NO_IMAGE');
                if(File::exists(env('CONTENT_EATERY_LOGO_PATH') . '/' . $eatery->id .  '_t.' . $eatery->LogoExtension))
                {
                    $logo_path = env('CONTENT_EATERY_LOGO_PATH') . '/' . $eatery->id .  '_t.' . $eatery->LogoExtension ;
                 ?>
                            
                                <img src="../../<?php echo $logo_path ?>" class="img-circle" alt="..." style="width: 40px; height: 40px;">
                               
                            <?php } else { ?>
                           
                                <img src="../../<?php echo $no_image ?>" alt="..." class="img-circle" style="width: 40px; height: 40px;">
                              
                             <?php } ?>

                        </div>
                                        </td>
                                        <td>
                                            {{$eatery->BusinessName}}
                                        </td> 
                                        <td>
                                            {{$eatery->BusinessType}}
                                        </td>                                        
                                         <td width="30%">
                                            <div>
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('eateries.edit','Edit',array($eatery->id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('eateries.destroy', $eatery->id))) }}
                                                    <button type="submit" class="btn btn-danger btn-xs pull-right" style="font-size: 11px;padding: 4px 12px;">Delete</button>
                                                    {{ Form::close() }}
                                                   @endif
                                                </div>
                                                <div class="btn-group pull-left">
                                                    <a href="../../items?eatery_id={{$eatery->id}}" class="btn btn-success">Items</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach      
                                        </tbody>
                                    </table>                                    
                                    
                                </div>
                            </div>
    					</div>
    				</div> 
@endsection