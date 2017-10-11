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
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center!important;">
                    <div class="widget-int num-count">{{$establishment_count}}</div>
                    <div class="widget-title">Total Eateries</div>
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
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center;">
                    <div class="widget-int num-count">{{$associatedeateries}}</div>
                    <div class="widget-title">Advisr OnBoarded</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget widget-default widget-item-icon" onclick="location.href='#';">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-o-down"></span>
                </div>                             
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center;">
                    <div class="widget-int num-count">{{$nonassociatedeateries}}</div>
                    <div class="widget-title">Yet To Be OnBoarded</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>
        </div>
        <div class="col-md-3">
              <div class="widget widget-default widget-item-icon" onclick="location.href='#';">
                <div class="widget-item-left">
                    <span class="fa fa-user"></span>
                </div>                             
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center;">
                    <div class="widget-int num-count">0</div>
                    <div class="widget-title">Registered Users</div>
                    <div class="widget-subtitle"></div>
                </div> 
            </div>  
        </div>                            
            <!-- END WIDGET MESSAGES -->
        </div>
        <div class="col-md-6">
                <div class="panel panel-default">                                
                    <div class="panel-body panel-body-table">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Top 5 Clicks On Advisr OnBoarded</th>          
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($v1_gettop5eateriesAfterAssociated as $gettop5eateriesAfterAssociated)
                                    <tr>                                                    
                                        <td style="font-size: 13px;">{{$gettop5eateriesAfterAssociated->BusinessName}}</td> 
                                    </tr>
                                    @endforeach   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="col-md-6">
                <div class="panel panel-default">                                
                    <div class="panel-body panel-body-table">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Top 5 Clicks On Yet To Be OnBoarded</th>          
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($v1_gettop5eateriesBeforeAssociated as $gettop5eateriesBeforeAssociated)
                                    <tr>                                                    
                                        <td style="font-size: 13px;">{{$gettop5eateriesBeforeAssociated->BusinessName}}</td> 
                                    </tr>
                                    @endforeach   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                           
                            <!-- START USERS ACTIVITY BLOCK -->
                            <div class="panel panel-default">
                                                              
                                <div class="panel-body padding-0" style="margin-left: 36px;">
                                     <br/><br/>
                                    <div class="chart-holder" id="chart_div" style="height: 300px;"></div>
                                    <br/>
                                </div>                                    
                            </div>
                            <!-- END USERS ACTIVITY BLOCK -->
                            
                        </div>
        </div>        
    </div>
          
@endsection

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {

        // var button = document.getElementById('change-chart');
        var chartDiv = document.getElementById('chart_div');

        var data = google.visualization.arrayToDataTable([
          ['Dates', 'Advisr OnBoarded'],
          @foreach($weeks as $week)
            ['{{$week[0]}}', {{$week[1]}}],
            @endforeach
        ]);

        var materialOptions = {
          width: 1050,
          colors: ['#1caf9a'],
          chart: {
            title: '',
            labelAngle: 45
          },
          series: {
            0: { axis: 'AdvisrOnBoarded' }, // Bind series 0 to an axis named 'distance'.
            1: { axis: 'Dates' } // Bind series 1 to an axis named 'brightness'.
          },
          axes: {
            y: {
              AdvisrOnBoarded: {label: 'Advisr OnBoarded'}, // Left y-axis.
              Dates: {side: 'right', label: 'apparent magnitude'},
              labelAngle: 45 // Right y-axis.
            }
          },
           axisX:{
   labelAngle: 50,
 }
          
        };

        function drawMaterialChart() {
          var materialChart = new google.charts.Bar(chartDiv);
          materialChart.draw(data, google.charts.Bar.convertOptions(materialOptions));
          button.innerText = 'Change to Classic';
          button.onclick = drawClassicChart;
        }       

        drawMaterialChart();
    };
    </script>   
