@extends('layouts.master')
@section('title')
Food Advisr-Categories
@endsection
@section('module')
Categories
@endsection

@section('content')
@include('components.message')	
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="btn-group pull-left">
                                    @if($privileges['Add']=='true') 
                                        <a href="{{URL::to('category/create')}}" class="btn btn-info"><i class="fa fa-edit"></i>Add Category</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Category Name</th>                                  
                                                <th>Edit/Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                    @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            {{$category->category_name}}
                                        </td>       
                                        <td width="30%">
                                            <div >
                                                <div style="float:left;padding-right:10px;">
                                                 @if($privileges['Edit']=='true')
                                                {{ link_to_route('category.edit','Edit',array($category->id), array('class' => 'btn btn-info')) }}
                                                @endif 
                                                </div>
                                                <div style="float:left;padding-right:10px;">
                                                   @if($privileges['Delete']=='true')
                                                    {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to delete?")','method' => 'DELETE', 'route' => array('category.destroy', $category->id))) }}
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