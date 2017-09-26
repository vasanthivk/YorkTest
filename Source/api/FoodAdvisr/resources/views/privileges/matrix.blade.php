@extends('layouts.master')
@section('title')
FoodAdvisr-Privileges
@endsection
@section('module')
Privileges
@endsection

@section('content')
@include('components.message')

{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

  {{ Form::open(array('method' => 'GET','route' => 'privileges.index')) }}
<div class="form-group form-horizontal">
        <div class="panel panel-default">
        </br>
           <div class="col-md-6">              
                 {{ Form::ahSelect('role','Role :',$selectedrole,$roles) }}
                </br>
            </div>
            <div class="col-md-4" style="padding-top: 5px;"> 
                 {{ Form::submit('Show', array('class' => 'btn btn-primary')) }}
                </br>
            </div> 
     </div>
 </div>
  {{ Form::close() }} 
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped table-actions">
                                    <thead>
                                            <tr>
                                                <th style="text-align:center">Module</th>
                                                @foreach($privileges as $privilege)
                                                <th style="text-align:center">{{$privilege->privilege}}</th>
                                                @endforeach
                                            </tr>
                                        </thead>                                        
                                        <tbody>
            @foreach($modules as $module)
            <tr>
                <th>{{$module->module_name}}</th>
                @foreach($privileges as $privilege)
                <?php $hasPrivileges = false; ?>
                    @foreach($rolemoduleprivileges as $rolemoduleprivilege)
                        @if($module->id == $rolemoduleprivilege->module_id && $privilege->id == $rolemoduleprivilege->privilege_id)
                        <?php $hasPrivileges = true; ?>
                        @endif
                    @endforeach
                
                    @if($hasPrivileges)
                        <td style="text-align:center">
                        {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to deny?")','method' => 'post', 'route' => array('denyprivileges', $selectedrole, $module->id, $privilege->id ))) }}
                            {{ Form::submit('Deny', array('class' => 'btn btn-danger btn-xl')) }}
                        {{ Form::close() }}
                        </td>
                    @else 
                        <td style="text-align:center">
                        {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to allow?")','method' => 'post', 'route' => array('allowprivileges', $selectedrole, $module->id, $privilege->id))) }}
                            {{ Form::submit('Allow', array('class' => 'btn btn-success btn-xl')) }}
                        {{ Form::close() }}
                        </td>
                    @endif
                @endforeach

            </tr>
            @endforeach
        </tbody>
                                    </table>                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>        
                                
@endsection