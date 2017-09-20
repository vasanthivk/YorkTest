<div class="form-group" style="margin:5px">
        {{ Form::label($name, $labeltext, ['class' => 'control-label col-sm-4']) }}
    <div class="col-sm-8">
        <?php
        if($value == null || $value == '')
            $value=' ';
        ?>
        {{ Form::label($name,$value ,['class' => 'control-label','style'=> 'text-align:left']) }}
    </div>
</div>