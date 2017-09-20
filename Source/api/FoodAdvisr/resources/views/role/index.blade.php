@extends('layouts.master')
@section('title')
TSLamb Scheme-Role
@endsection
@section('module')
Role
@endsection

@section('content')
@include('components.message')	
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="btn-group pull-left">
                                    @if($privileges['Add']=='true')                    
                                        <a href="{{URL::to('role/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Role</a>
                                         @endif
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Role Name</th>
                                                <th>Role Type</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                              @foreach($roles as $role)
                                    <tr>
                                        <td>
                                            {{$role->name}}
                                        </td>
                                        <td>
                                            {{$role->role_type}}
                                        </td>
                                         <td width="15%">
                                <div >
                                      <div style="float:left;padding-right:10px;">
                                         @if($privileges['Delete']=='true')
                                        {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('role.destroy', $role->id))) }}
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