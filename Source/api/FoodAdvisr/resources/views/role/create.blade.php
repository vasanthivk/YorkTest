@extends('layouts.master')
@section('title')
TSLamb Scheme-Role
@endsection
@section('module')
Role
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

{{ Form::open(array('route' => 'role.store','files'=>true)) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
            <div class="col-md-6">
                {{ Form::ahText('name','Role Name:','',array('maxlength' => '100')) }}
                {{ Form::ahSelect('role_type','Role Type','1',array('1' => 'User', '2' => 'Operator')) }}
                </br>
            </div>
             
        <div class="form-group">
            <div class="panel-footer">
                <div class="col-md-6 col-md-offset-3">
                    {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
                    {{ link_to_route('role.index','Cancel',null, array('class' => 'btn btn-danger')) }}
                </div>
            </div>
        </div>
     </div>
 </div>

@endsection