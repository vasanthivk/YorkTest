@extends('layouts.master')
@section('title')
FoodAdvisr-Users
@endsection
@section('module')
Users
@endsection

@section('content')
@include('components.message')	
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="btn-group pull-left">
                                    @if($privileges['Add']=='true') 
                                        <a href="{{URL::to('user/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add User</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Email</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Mobile No</th>
                                                <th>Status</th>
                                                <th>Edit/Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            {{$user->email}}
                                        </td>
                                        <td>
                                            {{$user->firstnames}}
                                        </td>
                                        <td>
                                            {{$user->role_name}}
                                        </td>
                                         <td>
                                            {{$user->mobileno}}
                                        </td>
                                        <td>
                                            {{$user->status}}
                                        </td>                                       
                                        <td width="30%">
                                            <div >
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('user.edit','Edit',array($user->id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('user.destroy', $user->id))) }}
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