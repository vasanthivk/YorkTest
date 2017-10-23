<!-- START WIDGETS -->                    
    <div class="row">
        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon" onclick="location.href='#';">
                <div class="widget-item-left">
                    <span class="fa fa-user"></span>
                </div>                             
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center!important;">
                    <div class="widget-int num-count">42</div>
                    <div class="widget-title">New Customers</div>
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
                    <div class="widget-int num-count">$ 23</div>
                    <div class="widget-title">Average Ticket Size</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget widget-default widget-item-icon" onclick="location.href='#';">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-o-up"></span>
                </div>                             
                <div class="widget-data" style="margin-top: 19px;margin-right: 9px;text-align: center;">
                    <div class="widget-int num-count">40</div>
                    <div class="widget-title">Average order per day</div>
                    <div class="widget-subtitle"></div>
                </div>      
                
            </div>
        </div>
        {{--<div class="col-md-3">
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
        </div> --}}
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body padding-0">
                    <h3 style="margin-top: 24px;text-align: center;">Restaurants Rating</h3>
                    <div class="chart-holder" id="most_popular_items" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body padding-0">
                    <h3 style="margin-top: 24px;text-align: center;">Top 10 Items Ordered</h3>
                    <div class="chart-holder" id="most_popular_items" style="height: 350px;">
                        <div class="panel-body panel-body-table">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <tr>
                                        <td style="font-size: 13px;">1</td>
                                        <td style="font-size: 13px;">Chicken Biryani</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">2</td>
                                        <td style="font-size: 13px;">Veg Biryani</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">3</td>
                                        <td style="font-size: 13px;">Egg Biryani</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">4</td>
                                        <td style="font-size: 13px;">Corn Soup</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">5</td>
                                        <td style="font-size: 13px;">Veg Salad</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">6</td>
                                        <td style="font-size: 13px;">Samosa</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">7</td>
                                        <td style="font-size: 13px;">Veg Manchuria</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">8</td>
                                        <td style="font-size: 13px;">Chicken Manchuria</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">9</td>
                                        <td style="font-size: 13px;">Fish Salad</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px;">10</td>
                                        <td style="font-size: 13px;">Gulab Jamun</td>
                                    </tr>

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

        </div>
        </div>
       <!--  <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body padding-0" style="margin-left: 10px;">
                    <h3 style="margin-top: 24px;text-align: center;">Registered Users Report</h3>
                    <div class="chart-holder" id="donutchart2" style="height: 300px;"></div>
                    <br/>
                </div>
            </div>
        </div> -->
    </div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {

        // var button = document.getElementById('change-chart');
        var chartDiv = document.getElementById('chart_div');

        var data = google.visualization.arrayToDataTable([
          ['', 'Advisr OnBoarded'],
          @foreach($date_wise_onboard as $onboard)
            ['{{$onboard->AssociatedOn}}', {{$onboard->Total}}],
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
            0: { color: '#DA70D6' },
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

     <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],                  
          ['Chicken Biryani',    40],
          ['Veg Biryani',    35],
          ['Egg Biryani',    32],
          ['Corn Soup',    28],
          ['Veg Salad',    23],
          ['Samosa',    20],
          ['Veg Manchuria',    15],
          ['Chicken Manchuria',    12],
          ['Fish Salad',    8],
          ['Gulab Jamun',    5]
        ]);

        var options = {
          title: '',
          pieHole: 0.4,
           slices: {
             0: { color: '#20B2AA' },
               1: { color: '#DA70D6' },
               2: { color: '#fea223' },
               3: { color: '#1caf9a' },
               4: { color: '#483D8B' },
               5: { color: '#2F8F4F' },
               6: { color: '#2E4F2D' },
               7: { color: '#2F4F33' },
               8: { color: '#2F4F5F' },
               9: { color: '#2F4F7E' }
          }
        };

        var chart = new google.visualization.PieChart(document.getElementById('most_popular_items'));
        chart.draw(data, options);
      }
    </script>

    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {

        // var button = document.getElementById('change-chart');
        var chartDiv = document.getElementById('donutchart2');

        var data = google.visualization.arrayToDataTable([
          ['', 'Registered Users'],
            ['2017-08-28', 6],
            ['2017-09-15', 4],        
            ['2017-10-15', 10],
            ['2017-10-16', 5]
            
        ]);

        var materialOptions = {
          width: 500,
          colors: ['#FFD700'],
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
   