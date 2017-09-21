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

{{ Form::open(array('method' => 'GET','route' => 'hotel.index')) }}
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
                                   <!--  <div class="btn-group pull-left">                      
                                        <a href="{{URL::to('hotel/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Hotel</a>
                                    </div> -->
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Business Name</th>    
                                                <th>Rating Value</th>                
                                            </tr>
                                        </thead>
                                        <tbody>
                                              @foreach($hotels as $hotel)
                                    <tr>
                                        <td>
                                            {{$hotel->BusinessName}}
                                        </td>                                        
                                        <td>
                                            {{$hotel->RatingValue}}
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