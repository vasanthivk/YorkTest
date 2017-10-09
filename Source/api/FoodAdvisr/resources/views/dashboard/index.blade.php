@extends('layouts.master')
@section('title')
FoodAdvisr-Dashboard
@endsection
@section('module')
Dashboard
@endsection

@section('content')

<!-- START WIDGETS -->                    
    <div class="row">
        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon" onclick="location.href='/eateries';">
                <div class="widget-item-left">
                    <span class="fa fa-h-square"></span>
                </div>                             
                <div class="widget-data">
                    <div class="widget-int num-count">{{$establishment_count}}</div>
                    <div class="widget-title">Eateries</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>                            
            <!-- END WIDGET MESSAGES -->
        </div>
        <div class="col-md-3">
           
        </div>
        <div class="col-md-3">
            
        </div>
        <div class="col-md-3">
                 
                
            </div>                            
            <!-- END WIDGET MESSAGES -->
        </div>
        <div class="col-md-6">
            <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="50%">Clicks Before Associated</th>
                                                    <th width="20%">No Of Clicks</th>                        
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @foreach($v1_getclicksbeforeassociated as $getclicksbeforeassociated)
                                                <tr>                                                    
                                                    <td><strong>{{$getclicksbeforeassociated->BusinessName}}</strong></td> 
                                                    <td><strong>{{$getclicksbeforeassociated->ClicksBeforeAssociated}}</strong></td> 
                                                </tr>
                                                 @endforeach   
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
            <div class="col-md-6">
            <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="50%">Clicks After Associated</th>
                                                    <th width="20%">No Of Clicks</th>                        
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @foreach($v1_gettop5eateriesAfterAssociated as $gettop5eateriesAfterAssociated)
                                                <tr>                                                    
                                                    <td><strong>{{$gettop5eateriesAfterAssociated->BusinessName}}</strong></td> 
                                                    <td><strong>{{$gettop5eateriesAfterAssociated->ClicksBeforeAssociated}}</strong></td> 
                                                </tr>
                                                 @endforeach   
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
        </div>
    </div>
          
@endsection