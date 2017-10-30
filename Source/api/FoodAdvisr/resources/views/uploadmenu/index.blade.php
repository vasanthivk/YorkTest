@extends('layouts.master')
@section('title')
FoodAdvisr-Upload Menu
@endsection
@section('module')
Upload Menu
@endsection

@section('content')
@include('components.message') 
{{Form::component('ahFile', 'components.form.file', ['name', 'labeltext'=>null,'value' =>null, 'attributes' => []])}}


<div class="form-group form-horizontal">
    <div class="panel panel-default" style="padding: 15px 26px 10px 74px;">
        <div class="col-md-3">
           <div class="btn-group pull-right" style="padding-right: 10px;">
                <a href="/generateExcel">
                    <button type="submit" class="btn btn-primary btn-xs pull-right" style="font-size: 11px;padding: 4px 12px;"><span class="fa fa-download"></span>Download Menu Template
                    </button>
                </a>
            </div>
        </div>       
        <div class="col-md-3">
        </div>       
        <div class="col-md-3">            
        </div>
    </div>
</div>

{{ Form::open(array('route' => 'uploadmenu.store','files'=>true)) }}
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group form-horizontal">
    <div class="panel panel-default" style="padding: 15px 26px 10px 74px;">
        <div class="col-md-3">
            <div class="form-group" style="margin:5px;">
                <input name="import_file" type="file">                
            </div>
        </div>       
        <div class="col-md-3">
            {{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
        </div>       
        <div class="col-md-3">            
        </div>
    </div>
</div>
{{ Form::close() }}

@endsection