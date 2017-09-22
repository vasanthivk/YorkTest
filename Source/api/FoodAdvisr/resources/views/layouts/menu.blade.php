<div class="page-sidebar">
                <!-- START X-NAVIGATION -->
                <ul class="x-navigation">
                    <li class="xn-logo">
                        <a href="/dashboard">Food Advisr</a>
                        <a href="#" class="x-navigation-control"></a>
                    </li>
                    <li class="xn-profile">
                        <a href="#" class="profile-mini">
                            <h3 style="color: white;padding-top: 9px;">FA</h3>
                        </a>                                                                    
                    </li>
                    <li class="{{ Request::segment(1) === 'dashboard' ? 'active' : null }}"><a href="{{action('DashboardController@index')}}"><span class="fa fa-dashboard"></span> <span class="xn-text">Dashboard</span></a>     
                    </li>
                     <li class="{{ Request::segment(1) === 'company' ? 'active' : null }}"><a href="{{action('CompanyController@index')}}"><span class="fa fa-university"></span> <span class="xn-text">Company</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'gethotel' ? 'active' : null }}"><a href="{{action('HotelController@index')}}"><span class="fa fa-h-square"></span> <span class="xn-text">Hotels</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'uploadhotel' ? 'active' : null }}"><a href="{{action('FoodController@index')}}"><span class="fa fa-upload"></span> <span class="xn-text">Upload Hotels</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'user' || Request::segment(1) === 'userpermissions' ? 'active' : null }}"><a href="{{action('UserController@index')}}"><span class="fa fa-users"></span> <span class="xn-text">User</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'privileges' ? 'active' : null }}"><a href="{{action('PrivilegesController@index')}}"><span class="fa fa-list"></span> <span class="xn-text">Privileges</span></a>     
                    </li>
                     <li class="{{ Request::segment(1) === 'logs' ? 'active' : null }}"><a href="{{action('LogsController@index')}}"><span class="fa fa-database"></span> <span class="xn-text">Logs</span></a>     
                    </li>
                </ul>
            </div>