@extends('layouts.master')
@section('title')
FoodAdvisr-Menus
@endsection
@section('module')
Menus
@endsection

@section('content')
@include('components.message')	
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="btn-group pull-left">
                                    @if($privileges['Add']=='true') 
                                        <a href="{{URL::to('menu/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Menu</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Menu Name</th>
                                                <th>Group</th>
                                                <th>Eatery</th>
                                                <th>Status</th>
                                                <th>Edit/Delete</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                    @foreach($menus as $menu)
                                    <tr>
                                        <td>
                                            {{$menu->menu}}
                                        </td>  
                                        <td>{{$menu->description}}</td> 
                                        <td>{{$menu->business_name}}</td>  
                                        <td>{{$menu->status}}</td>
                                        <td width="20%">
                                            <div >
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('menu.edit','Edit',array($menu->id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('menu.destroy', $menu->id))) }}
                                                    <button type="submit" class="btn btn-danger btn-xs pull-right" style="font-size: 11px;padding: 4px 12px;">Delete</button>
                                                    {{ Form::close() }}
                                                   @endif
                                                </div>
                                            </div>
                                        </td>
                                          <td>
                                            <div class="btn-group pull-left">
                                                    <a href="../../menusections?menu_id={{$menu->id}}" class="btn btn-success">Menu Section</a>
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