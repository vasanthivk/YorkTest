@extends('layouts.master')
@section('title')
EduCare-Privileges
@endsection
@section('module')
Privileges
@endsection

{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}

@section('content')
@include('components.message')
<div class='row'>
<div class='col-md-12'>
    <form class="form-inline" action="{{action('PrivilegesController@index')}}" method="GET">
         {{ Form::ahSelect('role','Role :',$selectedrole,$roles) }}
        <button type="submit" class="btn btn-primary" style="margin-left:34px;">Submit</button>
    </form>
</div>
</div>
<section>
<div> 
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th></th>
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
                            {{ Form::submit('Deny', array('class' => 'btn btn-danger btn-xs')) }}
                        {{ Form::close() }}
                        </td>
                    @else 
                        <td style="text-align:center">
                        {{ Form::open(array('onsubmit' => 'return confirm("Are you sure you want to allow?")','method' => 'post', 'route' => array('allowprivileges', $selectedrole, $module->id, $privilege->id))) }}
                            {{ Form::submit('Allow', array('class' => 'btn btn-success btn-xs')) }}
                        {{ Form::close() }}
                        </td>
                    @endif
                @endforeach

            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
