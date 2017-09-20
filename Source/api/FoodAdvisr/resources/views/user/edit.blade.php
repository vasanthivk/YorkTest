@extends('layouts.master')
@section('title')
TSLamb Scheme-User
@endsection
@section('module')
User
@endsection

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahPassword', 'components.form.password', ['name', 'labeltext'=>null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahFile', 'components.form.file', ['name', 'labeltext'=>null,'value' =>null, 'attributes' => []])}}
{{Form::component('ahReadonly', 'components.form.readonly', ['name', 'labeltext'=>null, 'value' => null])}}
{{ Form::open(array('method' => 'PUT', 'route' => array('user.update',$user->id),'files'=>true)) }}
<div class="form-group form-horizontal">
		<div class="panel panel-default">
		</br>
			<div class="col-md-6">
		        {{ Form::ahText('login','Login :',$user->login,array('maxlength' => '100'))  }}               
                {{ Form::ahSelect('employee_id','Name:',$user->name,$employees) }}
                {{ Form::ahSelect('mobile_no','Mobile No :',$user->mobile_no,$employee_mobile) }}
                {{ Form::ahSelect('role_id','Role :',$user->role_id,$role) }}
                {{ Form::ahSelect('status','Status :',$user->status,array('1' => 'Active', '2' => 'Inactive')) }}
		        </br>
		    </div>
		    
	    <div class="form-group">
		    <div class="panel-footer">
		        <div class="col-md-6 col-md-offset-3">
		            {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		            {{ link_to_route('user.index','Cancel',null, array('class' => 'btn btn-danger')) }}
		        </div>
		    </div>
	    </div>
	 </div>
 </div>
 <script>
       $(document).ready(function(){    
    
        
        $("#employee_id").change(function(){
          empChange();
        });
       
        empChange();
        var value ='<?php echo Session::get('mobile_no') ?>';
        $("select#mobile_no option") .each(function() {
            this.selected = (this.value == '<?php echo Session::get('mobile_no') ?>'); 
        });

    }); 
       
    function empChange(){

  var id = $("#employee_id").val();
            $.get("../../api/getMobilenumberbyid?employee_id="+id, function(data){
                $('#mobile_no').empty();
                var emp_value ='<?php echo Session::get('mobile_no') ?>';
                $.each(data, function(i, obj){
                    if( emp_value == obj.mobile_no) {
                    $('#mobile_no').append($('<option selected>').text(obj.mobile_no).attr('value', obj.mobile_no)); 
                }
                else{
                 $('#mobile_no').append($('<option>').text(obj.mobile_no).attr('value', obj.mobile_no)); 

                }

                });
            
            });

    }
</script>
@endsection