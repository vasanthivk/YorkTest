@extends('layouts.master')
@section('title')
FoodAdvisr-Menus
@endsection
@section('module')
Menu
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

{{ Form::open(array('route' => 'menu.store','files'=>true)) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
            <div class="col-md-6">
                {{ Form::ahText('menu','Menu Name:','',array('maxlength' => '100'))  }}
                {{ Form::ahTextarea('description','Description :','',array('size' => '30x5'))  }}
                <div class="form-group" style="margin:5px">
                    <label for="location_id" class="control-label col-sm-4">Groups :</label>
                     <div class="col-sm-8">
                            <select class="form-control" id="group_id" name="group_id">
                                <option value="0">Please select group</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}"> 
                                  {{ $group->description }}</option>
                                @endforeach
                            </select>
                       </div>
                </div>
                {{ Form::ahSwitch('is_visible','Is Visible :',null) }}
                </br>
            </div>
            <div class="col-md-6">
                 <i onclick='window.open("../../searcheateries", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=100, left=500, width=600, height=450")'><span class="label label-warning label-form" style="font-size: 20px;padding: 14px 13px;">Eateries</span></i>                 
            </div>
        <div class="form-group">
            <div class="panel-footer">
                <div class="col-md-6 col-md-offset-3">
                    {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
                    {{ link_to_route('menu.index','Cancel',null, array('class' => 'btn btn-danger')) }}
                </div>
            </div>
        </div>
     </div>
 </div>
 {{ Form::close() }}

@endsection