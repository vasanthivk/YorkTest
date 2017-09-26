@extends('layouts.master')
@section('title')
FoodAdvisr-Menu
@endsection
@section('module')
Menu
@endsection

@section('content')
@include('components.message')
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

 
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">
                                <div class="panel-heading">
                               
                                </div>
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Edit/Delete</th>              
                                            </tr>
                                        </thead>
                                        <tbody>
                                             
                                    <tr>
                                        <td>
                                            
                                        </td>                                         
                                         <td width="30%">
                                            
                                        </td>
                                    </tr>
                                    
                                        </tbody>
                                    </table>                                    
                                    
                                </div>
                            </div>
    					</div>
    				</div> 
@endsection