<div class="form-group" style="margin:5px">
        {{ Form::label($name, $labeltext, ['class' => 'control-label col-sm-4']) }}
    <div class="col-sm-8">
    	 <label class="switch">
        {{ Form::checkbox($name, $value, $checkstatus ,$attributes) }}
         <span></span>
         </label>
    </div>
</div>