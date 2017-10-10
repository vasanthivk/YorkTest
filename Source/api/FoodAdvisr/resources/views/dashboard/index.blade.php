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
                    <span class="fa fa-etsy"></span>
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
           <div class="widget widget-default widget-item-icon" onclick="location.href='#';">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-o-up"></span>
                </div>                             
                <div class="widget-data">
                    <div class="widget-int num-count">{{$associatedeateries}}</div>
                    <div class="widget-title">Associated Eateries</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget widget-default widget-item-icon" onclick="location.href='#';">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-o-down"></span>
                </div>                             
                <div class="widget-data">
                    <div class="widget-int num-count">{{$nonassociatedeateries}}</div>
                    <div class="widget-title">None Associated Eateries</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>
        </div>
        <div class="col-md-3">
                 
                
            </div>                            
            <!-- END WIDGET MESSAGES -->
        </div>
        <div class="col-md-4">
            <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th >Top 5 Associated Eateries</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($v1_gettop5eateriesAfterAssociated as $gettop5eateriesAfterAssociated)
                            <tr>                                                    
                                <td><strong>{{$gettop5eateriesAfterAssociated->BusinessName}}</strong></td> 
                            </tr>
                                @endforeach   
                        </tbody>
                    </table>
                </div>
        </div> 
            <div class="col-md-4">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th >Top 5 None Associated Eateries</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($v1_gettop5eateriesBeforeAssociated as $gettop5eateriesBeforeAssociated)
                            <tr>                                                    
                                <td><strong>{{$gettop5eateriesBeforeAssociated->BusinessName}}</strong></td> 
                            </tr>
                                @endforeach   
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
    </div>
          
@endsection