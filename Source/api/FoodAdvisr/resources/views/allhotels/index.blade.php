@extends('layouts.master')
@section('title')
Food Advisr-Hotels
@endsection
@section('module')
Hotels
@endsection

@section('content')
@include('components.message')
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

{{ Form::open(array('method' => 'GET','route' => 'allhotels.index')) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
           <div class="col-md-6">              
                {{ Form::ahSelect('location_id','Local Authority Name :','',$locations)  }}
                </br>
            </div>
            <div class="col-md-4" style="padding-top: 5px;"> 
                 {{ Form::submit('Show', array('class' => 'btn btn-primary')) }}
                </br>
            </div> 
     </div>
 </div>
 {{ Form::close() }}
 
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">
                                @if($privileges['Add']=='true')           
                                    <div class="btn-group pull-left">                      
                                        <a href="{{URL::to('allhotels/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Hotel</a>
                                    </div>
                                    @endif
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Business Name</th>    
                                                <th>Rating Value</th>
                                                <th>Edit/Delete</th>              
                                            </tr>
                                        </thead>
                                        <tbody>
                                              @foreach($all_hotels as $hotel)
                                    <tr>
                                        <td>
                                            {{$hotel->BusinessName}}
                                        </td>                                        
                                        <td>
                                            {{$hotel->RatingValue}}
                                        </td>
                                         <td width="30%">
                                            <div >
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('allhotels.edit','Edit',array($hotel->id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('allhotels.destroy', $hotel->id))) }}
                                                    <button type="submit" class="btn btn-danger btn-xs pull-right" style="font-size: 11px;padding: 4px 12px;">Delete</button>
                                                    {{ Form::close() }}
                                                   @endif
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