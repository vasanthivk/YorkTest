<div class="form-group" style="margin:5px">
        {{ Form::label($name, $labeltext, ['class' => 'control-label col-sm-4']) }}
    <div class="col-sm-8">
        {{ Form::checkbox($name, $value, $checkstatus ,$attributes) }}
    </div>
</div>