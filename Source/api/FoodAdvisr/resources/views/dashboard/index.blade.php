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
              <div class="widget widget-default widget-item-icon" onclick="location.href='/user';">
                <div class="widget-item-left">
                    <span class="fa fa-user"></span>
                </div>                             
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center;">
                    <div class="widget-int num-count">{{$registered_count}}</div>
                    <div class="widget-title">Registered Users</div>
                    <div class="widget-subtitle"></div>
                </div> 
            </div>  
        </div> 
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body padding-0" style="margin-left: 36px;margin-top: 22px;">
                    <h3>OnBoarded Restaurants</h3>
                    <br/>
                    <div class="chart-holder" id="chart_div" style="height: 300px;"></div>
                    <br/>
                </div>                                    
            </div>
        </div> 
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body padding-0">
                    <h3 style="margin-top: 24px;text-align: center;">Restaurants Rating</h3>
                    <div class="chart-holder" id="donutchart" style="height: 317px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body padding-0" style="margin-left: 10px;">
                    <h3 style="margin-top: 24px;text-align: center;">Registered Users Report</h3>
                    <div class="chart-holder" id="donutchart1" style="height: 300px;"></div>
                    <br/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">                                
                <div class="panel-body panel-body-table">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center;font-size: 16px;">Most Viewed Restaurants Advisr OnBoarded</th>          
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach($v1_gettop5eateriesAfterAssociated as $gettop5eateriesAfterAssociated)
                                <tr>                                                    
                                    <td style="font-size: 13px;">{{$gettop5eateriesAfterAssociated->BusinessName}}</td> 
                                    <td style="font-size: 13px;"><span class="fa fa-eye" style="color: #1caf9a"></span> {{$gettop5eateriesAfterAssociated->ClicksAfterAssociated}} Views</td> 
                                </tr>
                                    @endforeach   
                            </tbody>
                           <!--  <thead>
                                <tr>
                                    <th colspan="2" style="text-align: right;font-size: 14px;"><a href="#">More..</a></th>
                                </tr>
                            </thead> -->
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
                                    <th colspan="2" style="text-align: center;font-size: 16px;">Most Clicked Restaurants Yet To Be OnBoarded</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach($v1_gettop5eateriesBeforeAssociated as $gettop5eateriesBeforeAssociated)
                                <tr>                                                    
                                    <td style="font-size: 13px;">                               {{$gettop5eateriesBeforeAssociated->BusinessName}}
                                    </td>
                                    <td style="font-size: 13px;"><span class="fa fa-hand-pointer-o"></span>                                {{$gettop5eateriesBeforeAssociated->ClicksBeforeAssociated}} Clicks
                                    </td> 
                                </tr>
                                    @endforeach   
                            </tbody>
                            <!-- <thead>
                                <tr>
                                    <th colspan="2" style="text-align: right;font-size: 14px;"><a href="#">More..</a></th>
                                </tr>
                            </thead> -->
                        </table>
                    </div>
                </div>
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
          ['', 'Advisr OnBoarded'],
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
          legend: {position: 'none'},
          series: {
            0: { axis: 'AdvisrOnBoarded' }, // Bind series 0 to an axis named 'distance'.
            1: { axis: '' } // Bind series 1 to an axis named 'brightness'.
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

    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['FoodAdvisr Overall Rating', 'Total'],
          @foreach($foodadvisroverallratings as $foodadvisroverallrating)
            ['{{$foodadvisroverallrating->FoodAdvisrOverallRating}} Star', {{$foodadvisroverallrating->Total}} ],
            @endforeach
        ]);

        var options = {
          title: '',
          pieHole: 0.4,
          // legend: {position: 'none'}, //to hide
           slices: {
            0: { color: '#33414e' },
            1: { color: '#fea223' },
            2: { color: '#1caf9a' },
            3: { color: '#483D8B' },
            4: { color: '#2F4F4F' }
          }
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
      }
    </script>

   <!--  <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],                  
          ['Registered Users',    {{$registered_count}}]
        ]);

        var options = {
          title: '',
          pieHole: 0.7,
           slices: {
            0: { color: '#20B2AA' }
          }
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart1'));
        chart.draw(data, options);
      }
    </script> -->

    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {

        // var button = document.getElementById('change-chart');
        var chartDiv = document.getElementById('donutchart1');

        var data = google.visualization.arrayToDataTable([
          ['', 'Registered Users'],
            ['2017-09-14', 0],         
            ['2017-09-21', 0],
            ['2017-10-05', 0],
            ['2017-10-12', 1]
            
        ]);

        var materialOptions = {
          width: 500,
          colors: ['#2F4F4F'],
          chart: {
            title: '',
            labelAngle: 45
          },
          legend: {position: 'none'},
          series: {
            0: { axis: 'RegisteredUsers' }, // Bind series 0 to an axis named 'distance'.
            1: { axis: 'Dates' } // Bind series 1 to an axis named 'brightness'.
          },
          axes: {
            y: {
              RegisteredUsers: {label: 'Registered Users'}, // Left y-axis.
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
   