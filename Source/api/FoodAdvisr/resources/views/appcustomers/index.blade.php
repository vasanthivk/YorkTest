@extends('layouts.master')
@section('title')
FoodAdvisr-Customers
@endsection
@section('module')
Customers
@endsection

@section('content')
@include('components.message')	
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default">                               
                                <div class="panel-body">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Email</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                    @foreach($appcustomers as $customer)
                                    <tr>
                                        <td>
                                            
                                        </td>       
                                        <td>{{$customer->email}}</td>
                                        <td>{{$customer->status}}</td>          
                                         </tr>
                                    @endforeach
                                        </tbody>
                                    </table>                                    
                                    
                                </div>
                            </div>
    					</div>
    				</div>        
@endsection