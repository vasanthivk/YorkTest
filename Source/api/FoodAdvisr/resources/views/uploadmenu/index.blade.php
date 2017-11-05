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
{{ Form::open(array('method' => 'GET','route' => 'uploadmenu.index')) }}
<div class="form-group form-horizontal">
    <div class="panel panel-default">
        </br>
        <div class="col-md-8">
            <div class="form-group" style="margin:5px">
                <label for="location_id" class="control-label col-sm-4"></label>
                <div class="input-group push-down-10">
                    <span class="input-group-addon"><span class="fa fa-info-circle fa-1x" title='Eatery Names,Locations,Zip'></span></span>
                    <input type="text" class="form-control" name="search" id="search" placeholder="Search...." value="{{$searchvalue}}"/>
                    <div id="searchresult"></div>
                    <div class="input-group-btn">
                        <button class="btn btn-primary">Search Eateries</button>
                    </div>
                </div>
            </div>
            <br/>
        </div>
        <div class="col-md-4" style="padding-top: 5px;">

            </br>
        </div>
    </div>
</div>
{{ Form::close() }}

{{ Form::open(array('route' => 'uploadmenu.store','files'=>true)) }}
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group form-horizontal">
    <div class="panel panel-default" style="padding: 15px 26px 10px 74px;">
        <div class="col-md-6">
            <div class="form-group" style="margin:5px">
                <label for="eatery_id" class="control-label col-sm-4">Eateries :</label>
                <div class="col-sm-8">
                    <select class="form-control" id="eatery_id" name="eatery_id">
                        <option value="0">Please select eatery</option>
                        @foreach($eateries as $eatery)
                            <option value="{{$eatery->id}}">{{$eatery->business_name.' - '.$eatery->address}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
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