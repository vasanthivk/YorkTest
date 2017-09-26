<!DOCTYPE html>
<html lang="en" class="body-full-height">
    <head>        
        <title>FoodAdvisr</title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" /> 
        <meta name="csrf-token" content="{{ csrf_token() }}" />       
        <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">    
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-blue.css"/>
    </head>
    <body>        
        <div class="login-container">        
            <div class="login-box animated fadeInDown">
                <div style="color: white;font-size: 25px;text-align: center;padding-bottom: 10px;">FoodAdvisr</div>
                <div class="login-body">
                    <div class="login-title" style="text-align: center;"><strong>Forgot Password</strong> </div>
                    <div class="col-md-12">
@if ($errors->all())
    <div class="alert alert-danger">
    
        @foreach ($errors->all() as $error)
            {{ $error }}<br>        
        @endforeach
    </div>
@elseif( Session::has( 'success' ))
    <div class="alert alert-success">  {{ Session::get( 'success' ) }}
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    </div>
    @elseif( Session::has( 'warning' ))
    <div class="alert alert-danger">{{ Session::get( 'warning' ) }}
      <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
@endif
</div>
                    <form action="{{URL::to('forgot')}}" class="form-horizontal" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" name="login" id="login" class="form-control" placeholder="Login" maxlength="100" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" maxlength="100" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <a href="/login" class="btn btn-link btn-block">Go To Login Page</a>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info btn-block">Change Password</button>
                        </div>
                    </div> 
                    <div class="form-group">
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-4">
                           
                        </div>
                        <div class="col-md-4">                            
                            
                        </div>
                    </div>
                                                          
                    </form>
                </div>
                <div class="login-footer" style="text-align: center;">                    
                        &copy; <?php echo date("Y"); ?> FoodAdvisr                    
                </div>
            </div>
            
        </div>
        
    </body>
</html>