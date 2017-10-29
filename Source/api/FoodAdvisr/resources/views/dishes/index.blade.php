@extends('layouts.master')
@section('title')
FoodAdvisr-Dishes
@endsection
@section('module')
    {{$eatery_details[0]->BusinessName}} - Menu Details
@endsection

@section('content')
@include('components.message')  
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="btn-group pull-left">
                                    @if($privileges['Add']=='true') 
                                        <a href="dishes/create?eatery_id={{$eatery_id}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Dish</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Description</th>   
                                                <th>Is Visible</th>                                  
                                                <th>Edit/Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                    @foreach($dishes as $dish)
                                    <tr>
                                        <td>
                                            {{$dish->dish_name}}
                                        </td> 
                                        <td width="40%">
                                            {{$dish->description}}
                                        </td> 
                                        <td>
                                            {{$dish->is_visible}}
                                        </td>       
                                        <td width="30%">
                                            <div >
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('dishes.edit','Edit',array($dish->id,'eatery_id' => $eatery_id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('dishes.destroy', $dish->id))) }}
                                                    <button type="submit" class="btn btn-danger btn-xs pull-right" style="font-size: 11px;padding: 4px 12px;">Delete</button>
                                                    {{ Form::close() }}
                                                   @endif
                                                </div>
                                               <!--  <div style="float:left;padding-right:10px;">
                                                    <a href="../../recipe?eatery_id={{$eatery_id}}" class="btn btn-success">Recipe</a>
                                                </div> -->
                                              <!--  <div style="float:left;padding-right:10px;">
                                                    <a href="../../itemnutritions?eatery_id={{$eatery_id}}" class="btn btn-primary">Nutritions</a>
                                                </div> -->
                                            </div>
                                        </td>
                                         </tr>
                                    @endforeach
                                        </tbody>
                                    </table>                                    
                                    
                                </div>
                            </div>
                            @if(Session::get("role_id")==1)
                            <div class="panel-heading">
        <div class="btn-group pull-left">
            <a href="{{URL::to('eateries')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>Back To Eateries</a>
        </div>
    </div>   
    @endif 
                        </div>
                    </div>        
@endsection