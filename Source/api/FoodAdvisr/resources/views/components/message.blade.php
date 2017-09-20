<div class="col-md-8">
@if ($errors->all())
    <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">×</button>
        @foreach ($errors->all() as $error)
            {{ $error }}<br>        
        @endforeach
    </div>
@elseif( Session::has( 'success' ))
    <div class="alert alert-success">  {{ Session::get( 'success' ) }}
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    </div>
    @elseif( Session::has( 'warning' ))
    <div class="alert alert-danger">{{ Session::get( 'warning' ) }}
      <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
@endif
</div>