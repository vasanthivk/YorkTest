@extends('layouts.master')
@section('title')
FoodAdvisr-Dashboard
@endsection
@section('module')
Dashboard
@endsection

@section('content')

@if(Session::get("role_id")==1)
    <!--Super Admin -->
    @include('dashboard.foodadvisrdashboard')
@elseif(Session::get("role_id")==2) 
    <!--admin-->
    @include('dashboard.eaterydashboard')  
    @endif  
    
@endsection

