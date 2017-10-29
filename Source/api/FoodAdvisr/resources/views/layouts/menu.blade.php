<div class="page-sidebar">
                <!-- START X-NAVIGATION -->
                <ul class="x-navigation">
                    <li class="xn-logo">
                        <a href="/dashboard">FoodAdvisr</a>
                        <a href="#" class="x-navigation-control"></a>
                    </li>
                    <li class="xn-profile">
                        <a href="#" class="profile-mini">
                            <h3 style="color: white;padding-top: 9px;">FA</h3>
                        </a>                                                                    
                    </li>
                    <li class="{{ Request::segment(1) === 'dashboard' ? 'active' : null }}"><a href="{{action('DashboardController@index')}}"><span class="fa fa-dashboard"></span> <span class="xn-text">Dashboard</span></a>     
                    </li>
                   <!--  <li class="{{ Request::segment(1) === 'groups' ? 'active' : null }}"><a href="{{action('GroupsController@index')}}"><span class="fa fa-h-square"></span> <span class="xn-text">Groups</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'brands' ? 'active' : null }}"><a href="{{action('BrandsController@index')}}"><span class="fa fa-h-square"></span> <span class="xn-text">Brands</span></a>     
                    </li> -->
                        <?php
if(in_array(Session::get("role_id"),array(1)))
{ ?>
                     <li class="{{ Request::segment(1) === 'eateries' || Request::segment(1) === 'dishes' ? 'active' : null }}"><a href="{{action('EateriesController@index')}}"><span class="fa fa-etsy"></span> <span class="xn-text">Eateries</span></a>     
                    </li>
                     <?php } else {
                        $eateryid = Session::get("eatery_id");
                        ?>     
                   <!--   <li class="{{ Request::segment(1) === 'eateries' ? 'active' : null }}"><a href="{{action('EateriesController@index')}}"><span class="fa fa-cog"></span> <span class="xn-text">My Profile</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'items'? 'active' : null }}"><a href="{{action('ItemsController@index')}}?eatery_id={{$eateryid}}"><span class="fa fa-cutlery"></span>My Items</a></li> -->
                      <?php }  ?>  
                    <li class="xn-openable {{ Request::segment(1) === 'menu' || Request::segment(1) === 'menusections' || Request::segment(1) === 'menusubsections' ? 'active' : null }}">
                         <a href="#"><span class="fa fa-files-o"></span> <span class="xn-text">Manage Menu</span></a>
                        <ul>
                            <li class="{{ Request::segment(1) === 'menu'? 'active' : null }}"><a href="{{action('MenuController@index')}}"><span class="fa fa-cutlery"></span> Menu</a></li>
                            <li  class="{{ Request::segment(1) === 'menusections'? 'active' : null }}"><a href="{{action('MenuSectionController@index')}}"><span class="fa fa-cutlery"></span> Menu Sections</a></li>
                            <li  class="{{ Request::segment(1) === 'menusubsections'? 'active' : null }}"><a href="{{action('MenuSubSectionController@index')}}"><span class="fa fa-cutlery"></span> Menu Sub Sections</a></li>
                        </ul>
                    </li>  

     <?php
if(!in_array(Session::get("role_id"),array(2)))
{ ?>                 
                    <li class="{{ Request::segment(1) === 'uploadeatery' ? 'active' : null }}"><a href="{{action('FoodController@index')}}"><span class="fa fa-upload"></span> <span class="xn-text">Upload Eateries</span></a>     
                    </li> 
                    <!--  <li class="{{ Request::segment(1) === 'uploadmenu' ? 'active' : null }}"><a href="{{action('UploadMenuController@index')}}"><span class="fa fa-upload"></span> <span class="xn-text">Upload Menu</span></a>     
                    </li>  -->                                      
                    <li class="{{ Request::segment(1) === 'user' || Request::segment(1) === 'userpermissions' ? 'active' : null }}"><a href="{{action('UserController@index')}}"><span class="fa fa-users"></span> <span class="xn-text">User</span></a>     
                    </li>
                    <li class="{{ Request::segment(1) === 'privileges' ? 'active' : null }}"><a href="{{action('PrivilegesController@index')}}"><span class="fa fa-list"></span> <span class="xn-text">Privileges</span></a>     
                    </li>
                     <li class="{{ Request::segment(1) === 'logs' ? 'active' : null }}"><a href="{{action('LogsController@index')}}"><span class="fa fa-database"></span> <span class="xn-text">Logs</span></a>     
                    </li>
                     <li class="{{ Request::segment(1) === 'configuration' ? 'active' : null }}"><a href="{{action('ConfigurationController@index')}}"><span class="fa fa-database"></span> <span class="xn-text">Configuration</span></a>
                    </li>
<?php } ?>
                </ul>
            </div>