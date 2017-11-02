@extends('layouts.master')
@section('title')
FoodAdvisr-Menu Sections
@endsection
@section('module')
{{$menus[0]->business_name}} - Menu Section
@endsection

@section('content')
@include('components.message')	
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="btn-group pull-left">
                                    @if($privileges['Add']=='true') 
                                        <a href="../../menusections/create?menu_id={{$menu_id}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Menu Section</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Section Name</th>
                                                <th>Status</th>            
                                                <th>Edit/Delete</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                    @foreach($menusections as $section)
                                    <tr>
                                        <td>
                                            {{$section->section_name}}
                                        </td>   
                                        <td>
                                            {{$section->status}}
                                        </td>    
                                        <td width="30%">
                                            <div >
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('menusections.edit','Edit',array($section->id,'menu_id' => $menu_id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('menusections.destroy', $section->id))) }}
                                                    <button type="submit" class="btn btn-danger btn-xs pull-right" style="font-size: 11px;padding: 4px 12px;">Delete</button>
                                                    {{ Form::close() }}
                                                   @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group pull-left">
                                                    <a href="../../menusubsections?section_id={{$section->id}}" class="btn btn-success">Menu Sub Section</a>
                                                </div>
                                        </td>
                                         </tr>
                                    @endforeach
                                        </tbody>
                                    </table>                                    
                                    
                                </div>
                            </div>
                            <div class="btn-group pull-left">
                                   
                                        <a href="../../menu" class="btn btn-primary"><i class="fa fa-arrow-left"></i>Back To Menu</a>
                                       
                                    </div>
    					</div>
    				</div>        
@endsection