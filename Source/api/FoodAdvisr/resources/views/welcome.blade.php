<!DOCTYPE html>
<html lang="en">
<head>
  <title>FoodAdvisr</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <form action="{{URL::to('store')}}" class="form-horizontal" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-group">
     <h2 style="text-align: center;">Food Advisr(<?php echo $establishment_count; ?>)</h2>
    </div>
    <div class="form-group">
       <label for="inputdefault">Url</label>
      <input class="form-control" id="inputdefault" type="text" name="url" required>
    </div>
      <button type="submit" class="btn btn-default">Submit</button>
  </form>
</div>

</body>
</html>
