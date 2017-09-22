@extends('layouts.master')
@section('title')
Food Advisr-Dashboard
@endsection
@section('module')
Dashboard
@endsection

@section('content')

<!-- START WIDGETS -->                    
    <div class="row">
        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon" onclick="location.href='/allhotels';">
                <div class="widget-item-left">
                    <span class="fa fa-h-square"></span>
                </div>                             
                <div class="widget-data">
                    <div class="widget-int num-count">{{$establishment_count}}</div>
                    <div class="widget-title">Hotels</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>                            
            <!-- END WIDGET MESSAGES -->
        </div>
    </div>
          
@endsection