@extends('layouts.master')
@section('title')
Food Advisr-Configuration
@endsection
@section('module')
Configuration
@endsection

@section('content')
@include('components.message')        

{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahDate', 'components.form.date', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahCheckbox', 'components.form.checkbox', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{ Form::model($defaults, array('method' => 'PATCH', 'route' => array('configuration.update', $defaults->id))) }}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}

 <div class="form-group form-horizontal">
    <div class="panel panel-default">
    </br>
      <div class="col-md-6">
       {{ Form::ahNumber('search_radius','Search Radius :',$defaults->search_radius,array('maxlength' => '1','min'=>'0','max'=>'9')) }}  
       {{ Form::ahNumber('search_result_limit','Search Result Limit :',$defaults->search_result_limit,array('maxlength' => '2','min'=>'0','max'=>'99')) }}
       {{ Form::ahCheckbox('allow_create_logs','Allow Create Logs :',null,$defaults->allow_create_logs ) }}
       {{ Form::ahCheckbox('allow_edit_logs','Allow Edit Logs :',null,$defaults->allow_edit_logs ) }}
       {{ Form::ahCheckbox('allow_delete_logs','Allow Delete Logs :',null,$defaults->allow_delete_logs ) }}      
       {{ Form::ahNumber('log_max_days','Log Max Days :',$defaults->log_max_days,array('maxlength' => '2','min'=>'0','max'=>'99')) }} 
            </br>
        </div>
        
      <div class="form-group">
        <div class="panel-footer">
            <div class="col-md-6 col-md-offset-3">
                {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
            </div>
        </div>
      </div>
   </div>
 </div>     
{{ Form::close() }}
@endsection
