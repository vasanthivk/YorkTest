@extends('layouts.master')
@section('title')
FoodAdvisr-Upload Eatery
@endsection
@section('module')
Upload Eatery
@endsection

@section('content')
@include('components.message') 
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}

{{ Form::open(array('method' => 'GET','route' => 'uploadeatery.index')) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
           <div class="col-md-6">                            
               <div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4">Location :</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="location_id" name="location_id">
                            <option  selected disabled>Please select location</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                               <?php 
                                $val = $location_id;
                                $res = $location->id;
                               if($val == $res) 
                                {
                                  ?> selected="selected"
                                  <?php 
                                } ?>> 
                              {{ $location->location_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                      
               <br/>
            </div>
            <div class="col-md-4" style="padding-top: 5px;"> 
                 {{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
                </br>
            </div> 
     </div>
 </div>
 {{ Form::close() }}


<!-- {{ Form::open(array('route' => 'uploadeatery.store','files'=>true,'onClick'=> 'this.form.submit()')) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>			
		     <div class="col-md-6">              
               {{ Form::ahText('url','Url :','',array('maxlength' => '100'))  }}
                </br>
            </div>
            <div class="col-md-4" style="padding-top: 5px;"> 
                  {{ Form::submit('Upload', array('class' => 'btn btn-primary','id' => 'btnSubmit')) }}
                </br>
            </div> 
       
	 </div>
 </div>
 <div class="loading" align="center">
    Uploading Data...Please wait...<br />
    <br />
    <img src="loader.gif" alt="" />
</div> -->
 <style type="text/css">
    .modal
    {
        position: fixed;
        top: 0;
        left: 0;
        background-color: black;
        z-index: 99;
        opacity: 0.8;
        filter: alpha(opacity=80);
        -moz-opacity: 0.8;
        min-height: 100%;
        width: 100%;
    }
    .loading
    {
        font-family: Arial;
        font-size: 10pt;
        border: 0px solid #fff;
        width: 200px;
        height: 100px;
        display: none;
        position: fixed;
        background-color: White;
        z-index: 999;
    }
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
	function ShowProgress() {
		$("#btnSubmit").attr("disabled", true);
        setTimeout(function () {
            var modal = $('<div />');
            modal.addClass("modal");
            $('body').append(modal);
            var loading = $(".loading");
            loading.show();
            var top = Math.max($(window).height() / 2 - loading[0].offsetHeight / 2, 0);
            var left = Math.max($(window).width() / 2 - loading[0].offsetWidth / 2, 0);
            loading.css({ top: top, left: left });
        }, 200);
    }
    $('form').live("submit", function () {
        ShowProgress();
    });
</script>
@endsection