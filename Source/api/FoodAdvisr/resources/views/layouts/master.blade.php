<!DOCTYPE html>
<html lang="en">
    <head>        
        <title>@yield('title')</title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" /> 
        <meta name="csrf-token" content="{{ csrf_token() }}" />         
        <link rel="icon" type="image/png" sizes="32x32" href="../../favicon-32x32.png">      
        <link rel="stylesheet" type="text/css" id="theme" href="../../css/theme-blue.css"/>
        <link href="../../css/bootstrap-imageupload.css" rel="stylesheet">
        <link href="../../css/jasny-bootstrap.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <style>
    #save {
    visibility:hidden;
}
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(../../img/ibt-process-42.gif) center no-repeat #fff;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>
        $(window).load(function() { 
        $(".se-pre-con").fadeOut("slow");;
    });
</script>
    <body class="preload">
    <div class="se-pre-con"></div>
    <?php   
  if(Session::get("role_id")!== null)
{
    
    ?>
        <div class="page-container">
            @include('layouts.menu')
            <div class="page-content">
                <!-- START X-NAVIGATION VERTICAL -->
                <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                    <!-- TOGGLE NAVIGATION -->
                    <li class="xn-icon-button">
                        <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
                    </li>
                    <!-- END TOGGLE NAVIGATION -->
                    <!-- SEARCH -->
                    <!-- <li class="xn-search">
                        <form role="form">
                            <input type="text" name="search" placeholder="Search..."/>
                        </form>
                    </li> -->
                    <li class="xn-icon-button pull-right">
                        <a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span></a>                        
                    </li> 
                      <!-- <li class="xn-icon-button pull-right">
                        <a href="/profile"><span class="fa fa-user"></span></a>                        
                    </li>  -->                          
                    <!-- END SEARCH -->
                    <!-- SIGN OUT -->
                    
                </ul>
                <!-- END X-NAVIGATION VERTICAL -->                     

                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a>@yield('module')</a></li>
                </ul>
                <!-- END BREADCRUMB -->                       
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">                    
                    @yield('content')
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->

        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to log out?</p>                    
                        <p>Press No if youwant to continue work. Press Yes to logout current user.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <a href="/" class="btn btn-success btn-lg">Yes</a>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->

        <!-- START PRELOADS -->
        <audio id="audio-alert" src="../../audio/alert.mp3" preload="auto"></audio>
        <audio id="audio-fail" src="../../audio/fail.mp3" preload="auto"></audio>
        <!-- END PRELOADS -->                  
    <?php   
}
else
{ ?>
<script>
window.location.href="/";
</script>
<?php  }
  ?>               
    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="../../js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="../../js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='../../js/plugins/icheck/icheck.min.js'></script>        
        <script type="text/javascript" src="../../js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
       <!--  <script type="text/javascript" src="../../js/plugins/scrolltotop/scrolltopcontrol.js"></script> -->
        
        
        <script type="text/javascript" src="../../js/plugins/tableexport/tableExport.js"></script>
        <script type="text/javascript" src="../../js/plugins/tableexport/jquery.base64.js"></script>
        <script type="text/javascript" src="../../js/plugins/tableexport/html2canvas.js"></script>
        <script type="text/javascript" src="../../js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
        <script type="text/javascript" src="../../js/plugins/tableexport/jspdf/jspdf.js"></script>
        <script type="text/javascript" src="../../js/plugins/tableexport/jspdf/libs/base64.js"></script>    

        <script type="text/javascript" src="../../js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="../../js/plugins/morris/morris.min.js"></script>       
        <script type="text/javascript" src="../../js/plugins/rickshaw/d3.v3.js"></script>
        <script type="text/javascript" src="../../js/plugins/rickshaw/rickshaw.min.js"></script>
        <script type='text/javascript' src='../../js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
        <script type='text/javascript' src='../../js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>                
        <script type='text/javascript' src='../../js/plugins/bootstrap/bootstrap-datepicker.js'></script>                
        <script type="text/javascript" src="../../js/plugins/owl/owl.carousel.min.js"></script>                 
        
        <script type="text/javascript" src="../../js/plugins/moment.min.js"></script>
        <script type="text/javascript" src="../../js/plugins/daterangepicker/daterangepicker.js"></script>
        <!-- END THIS PAGE PLUGINS-->        
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap-file-input.js"></script>
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap-select.js"></script>
        <script type="text/javascript" src="../../js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
        <script type="text/javascript" src="../../js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="../../js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
        <script type="text/javascript" src="../../js/plugins/codemirror/codemirror.js"></script>        
        <script type='text/javascript' src="../../js/plugins/codemirror/mode/htmlmixed/htmlmixed.js"></script>
        <script type='text/javascript' src="../../js/plugins/codemirror/mode/xml/xml.js"></script>
        <script type='text/javascript' src="../../js/plugins/codemirror/mode/javascript/javascript.js"></script>
        <script type='text/javascript' src="../../js/plugins/codemirror/mode/css/css.js"></script>
        <script type='text/javascript' src="../../js/plugins/codemirror/mode/clike/clike.js"></script>
        <script type='text/javascript' src="../../js/plugins/codemirror/mode/php/php.js"></script>    

        <script type="text/javascript" src="../../js/plugins/summernote/summernote.js"></script>
        <!-- START TEMPLATE -->
        <!-- <script type="text/javascript" src="../js/settings.js"></script> -->
        
        <script type="text/javascript" src="../../js/plugins.js"></script>        
        <script type="text/javascript" src="../../js/actions.js"></script>
        <script type="text/javascript" src="../../js/demo_tables.js"></script>    
        <script type="text/javascript" src="../../js/demo_dashboard.js"></script>
        <script src="../js/bootstrap-imageupload.js"></script>
        <script type="text/javascript" src="../../js/jasny-bootstrap.js"></script>
        <script type="text/javascript" src="../../js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script>
            var editor = CodeMirror.fromTextArea(document.getElementById("codeEditor"), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "application/x-httpd-php",
                indentUnit: 4,
                indentWithTabs: true,
                enterMode: "keep",
                tabMode: "shift"                                                
            });
            editor.setSize('100%','420px');


             function getlatitudelongitude(inputAddress)
        {
            var geocoder = new google.maps.Geocoder();
            var address = inputAddress.value;

            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    $("#locationmessage").html('');
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    document.getElementById("Latitude").value = latitude;
                    document.getElementById("Longitude").value = longitude;
                }
                else {
                    document.getElementById("Latitude").value = "";
                    document.getElementById("Longitude").value = "";
                    if($("#LocalAuthorityName").val().trim() != "") {
                        $("#errorMessage").css("display", "block");
                        $("#locationmessage").html('Invalid Location');
                        $("#locationmessage").css('color', 'red');
                    }
                }
            });
        }
        </script>
       
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->         
    </body>
</html>