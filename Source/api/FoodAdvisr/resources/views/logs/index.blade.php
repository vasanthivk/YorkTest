@extends('layouts.master')
@section('title')
Food Advisr-Logs
@endsection
@section('module')
Logs
@endsection

@section('content')
@include('components.message')
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

  {{ Form::open(array('method' => 'GET','route' => 'logs.index')) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
           <div class="col-md-6">              
                 {{ Form::ahSelect('role','Role :',$selectedrole,$roles) }}
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
                                
                                
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                    <thead>
                                            <tr>
                                                <th>Created On</th>
                                                <th>Module Name</th>
                                                <th>Action</th>
                                                <th>Name</th>
                                                <th>Description</th>                           
                                            </tr>
                                        </thead>                                        
                                        <tbody>
                                     @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            {{$log->created_on}}
                                        </td>
                                        <td>
                                            {{$log->module_name}}
                                        </td>                                        
                                        <td>
                                            {{$log->action}}
                                        </td>
                                        <td>{{$log->name}}</td>
                                        <td>{{$log->description}}</td>          
                                    </tr>
                                    @endforeach
                                    </tbody>
                                    </table>                                    
                                    
                                </div>
                               
                            </div>
    					</div>
    				</div>       
            
                                
@endsection