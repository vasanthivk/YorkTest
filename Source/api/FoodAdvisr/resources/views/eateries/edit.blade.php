@extends('layouts.master')
@if(Session::get("role_id")==1)
@section('title')
FoodAdvisr-Eateries
@endsection
@section('module')
Eatery
@endsection
@elseif(Session::get("role_id")==2) 
@section('title')
FoodAdvisr-My Profile
@endsection
@section('module')
My Profile
@endsection
@endif 

@section('content')
@include('components.message')
{{Form::component('ahText', 'components.form.text', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahPassword', 'components.form.password', ['name', 'labeltext'=>null, 'attributes' => []])}}
{{Form::component('ahSelect', 'components.form.select', ['name', 'labeltext'=>null, 'value' => null,'valuearray' => [], 'attributes' => []])}}
{{Form::component('ahTextarea', 'components.form.textarea', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahNumber', 'components.form.number', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahFile', 'components.form.file', ['name', 'labeltext'=>null,'value' =>null, 'attributes' => []])}}
{{Form::component('ahDate', 'components.form.date', ['name', 'labeltext'=>null, 'value' => null, 'attributes' => []])}}
{{Form::component('ahReadonly', 'components.form.readonly', ['name', 'labeltext'=>null, 'value' => null])}}
{{Form::component('ahCheckbox', 'components.form.checkbox', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}
{{Form::component('ahSwitch', 'components.form.switch', ['name', 'labeltext'=>null, 'value' => null, 'checkstatus' => false, 'attributes' => []])}}

{{ Form::open(array('method' => 'PUT', 'route' => array('eateries.update',$eateries->id),'files'=>true)) }}
<div class="form-group form-horizontal">
    <div class="panel panel-default">
    </br>
      <div class="col-md-6">
            <div class="form-group" style="margin:5px">
                <label for="group_id" class="control-label col-sm-4">Groups :</label>
                <div class="col-sm-8">
                    <select class="form-control" data-live-search='true' id="group_id" name="group_id">
                      <option value="0">Please Select Group</option>
                      @foreach($groups as $group)
                      <option value="{{$group->id}}" <?php 
                      $res = $group->id;
                      $db_res = $eateries->group_id;
                      if($res == $db_res) echo 'selected="selected"' ?>>{{$group->description}}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="form-group" style="margin:5px">
                <label for="brand_id" class="control-label col-sm-4">Brands :</label>
                <div class="col-sm-8">
                    <select class="form-control" data-live-search='true' id="brand_id" name="brand_id">
                      <option value="0">Please Select Brand</option>
                      @foreach($brands as $brand)
                      <option value="{{$brand->id}}" <?php 
                      $res = $brand->id;
                      $db_res = $eateries->brand_id;
                      if($res == $db_res) echo 'selected="selected"' ?>>{{$group->description}}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            {{ Form::ahNumber('fhrsid','FHRSID :',$eateries->fhrsid,array('min'=>'0','maxlength' => '20','max'=>'99999999999999999999'))  }}
            {{ Form::ahText('local_authority_business_id','Business ID :',$eateries->local_authority_business_id,array('maxlength'=> '1000'))  }}
            {{ Form::ahText('business_name','Business Name :',$eateries->business_name,array('maxlength'=> '1000'))  }}
            {{ Form::ahSelect('business_type_id','Business Type :',$eateries->business_type_id,$businesstypes)  }}
            {{ Form::ahSelect('location_id','Location :',$eateries->location_id,$locations,array("onchange"=>"ChooseContact(this)"))  }}
            {{ Form::ahTextarea('address','Address :',$eateries->address,array("onchange"=>"getlatitudelongitude(this)",'size' => '30x5'))  }}
            {{ Form::ahText('postal_code','Zip :',$eateries->postal_code,array('maxlength'=> '1000'))  }}
            {{ Form::ahNumber('contact_number','Contact Number :',$eateries->contact_number,array('min'=>'0','maxlength' => '12','max'=>'999999999999'))  }}
            {{ Form::ahText('website','WebSite :',$eateries->website,array('maxlength' => '100'))  }}
            {{ Form::ahText('email_id','EmailId :',$eateries->email_id,array('maxlength' => '100'))  }}
            {{ Form::ahText('longitude','Longitude :',$eateries->longitude,array("readonly"=>"true"))  }}
            {{ Form::ahText('latitude','Latitude :',$eateries->latitude,array("readonly"=>"true"))  }}
            {{ Form::ahSwitch('is_associated','Is Associated :',null,$eateries->is_associated) }}
            {{ Form::ahDate('associated_on','Associated On :', $eateries->associated_on) }}
            <?php 
            $cuisines_ids = $eateries->cuisines_ids;
            $cuisines_ids=explode(",",$cuisines_ids);
            $lifestyle_choices_ids = $eateries->lifestyle_choices_ids;
            $lifestyle_choices_ids=explode(",",$lifestyle_choices_ids);
            ?>
            <div class="form-group" style="margin:5px">
                  <label for="cuisine" class="control-label col-sm-4">Cuisines :</label>
                  <div class="col-md-8">
                      <select multiple name="cuisines_ids[]" data-live-search='true' class="form-control select">
                      @foreach($cuisines as $cuisine)
                          <option value="{{$cuisine->id}}" @if(isset($cuisines_ids)) @if(in_array($cuisine->id,$cuisines_ids)) selected="selected" @endif @endif>{{$cuisine->cuisine_name}}</option>
                      @endforeach
                      </select>
                  </div>
            </div>             
            <div class="form-group" style="margin:5px">
                  <label for="lifestyle_choices" class="control-label col-sm-4">Lifestyle Choices :</label>
                  <div class="col-md-8">
                      <select multiple name="lifestyle_choices_ids[]" id="lifestyle_choices_ids[]" class="form-control select">
                      @foreach($lifestyle_choices as $lifestyle_choice)
                      <option value="{{$lifestyle_choice->id}}" @if(isset($lifestyle_choices_ids)) @if(in_array($lifestyle_choice->id,$lifestyle_choices_ids)) selected="selected" @endif @endif>{{$lifestyle_choice->description}}</option>
                      @endforeach
                      </select>
                  </div>
            </div> 
            </br>
        </div>
        <div class="col-md-6">
         <div class="module-wrapper col-lg-12 col-md-10 col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-md-4">
                    <div class="form-group">            
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php
                      $logo_path = '';
                     $no_image=env('NO_IMAGE');
                if(File::exists(env('CONTENT_EATERY_LOGO_PATH') . '/' . $eateries->id .  '.' . $eateries->logo_extension))
                {
                    $logo_path = env('CONTENT_EATERY_LOGO_PATH') . '/' . $eateries->id .  '.' . $eateries->logo_extension ;
                 ?>
                            <div class="fileinput-new thumbnail" style="width: 130px; height: 111px;">
                            <a>
                                <img src="../../<?php echo $logo_path ?>" alt="..." style="width: 130px; height: 102px;">
                                </a>
                            </div>
                            <?php } else { ?>
                            <div class="fileinput-new thumbnail" style="width: 130px; height: 111px;">
                            <a>
                                <img src="../../<?php echo $no_image ?>" alt="..." style="width: 130px; height: 111px;">
                                </a>
                            </div>
                             <?php } ?>
                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 130px; max-height: 111px;"></div>
                    <div>
                        <span class="btn btn-primary btn-file"><span class="fileinput-new">Change Logo</span><span class="fileinput-exists">Change Logo</span>
                        <input type="file" name="logo" id="logo">
                        </span>
                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>

                        </div>
                    </div>
                </div>                 
                 <div class="col-md-6">  
                  <h4>Images</h4>
                  {{ Form::ahFile('imagefile1',' ', array("accept"=>"image/*")) }}
                  {{ Form::ahFile('imagefile2',' ', array("accept"=>"image/*")) }}
                  {{ Form::ahFile('imagefile3',' ', array("accept"=>"image/*")) }}
                </div>
                <style type="text/css">
                  #imagesMain{
                      display: flex;
                      justify-content: center;
                    }
                    #imagesMain img{
                      height: 142px;
                      width: 456px;
                      margin: 0 10px;
                      margin-left: 6px;
                    }
                 </style>
                <div class="col-md-6">                    
                  <?php
                $session_id = Session::getId();
                $path = env('CONTENT_EATERY_IMAGE_PATH') . '//'. $eateries->id;
                ?>
              </div>
              <?php $res = count($fileslist); 
              if ($res != 0) {             
              ?>
              <div class="widget widget-default widget-carousel">             
                <div class="owl-carousel" id="owl-example">
                    @foreach($fileslist as $file )
                    <?php
                    $filename = $path . '/' . $file ;
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    ?>
                  <div id="imagesMain">                                                      
                    <div class="widget-title">
                      @foreach($eateriesmedia as $media)
                        @if($media->media_name == $file)                          
                         <a href="../../destroyeateryimageedit/{{$file}}" class="fa fa-times" style="float: right;"></a> 
                     <img src="../../<?php echo $filename ?>" alt="...">
                      @break
                        @endif
                    @endforeach
                   </div>
                </div> 
                 @endforeach               
                </div>                        
                </div> 
                <?php } ?>  
            </div>
          </div>
          <input id="searchInput" name="searchInput" class="input-controls" type="text" placeholder="Enter a location">
              <div id="map" style="margin-left: 27px;width: 488px;height: 197px;"></div>
              </br>
        </div>
      <div class="form-group">
        <div class="panel-footer">
            <div class="col-md-6 col-md-offset-3">
                {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
                {{ link_to_route('eateries.index','Cancel',null, array('class' => 'btn btn-danger')) }}
            </div>
        </div>
      </div>
   </div>
 </div>
 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDQRQHxDzP0SoX_WMbskBK3OOr5qT3QK08&libraries=places"></script>
  <script>
/* script */
//  function ChooseContact(data) {

// var location = document.getElementById ("searchInput").value = data.value;
// }

function initialize() {
   var latlng = new google.maps.LatLng(<?php echo floatval($eateries->latitude); ?>,<?php echo floatval($eateries->longitude); ?>);
    var map = new google.maps.Map(document.getElementById('map'), {
      center: latlng,
      zoom: 16
    });
    var marker = new google.maps.Marker({
      map: map,
      position: latlng,
      draggable: true,
      anchorPoint: new google.maps.Point(0, -29)
   });
    var input = document.getElementById('searchInput');
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var geocoder = new google.maps.Geocoder();
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);
    var infowindow = new google.maps.InfoWindow();   
    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            alert("Autocomplete's returned place contains no geometry");
            return;
        }
  
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
       
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);          
    
        bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
        infowindow.setContent(place.formatted_address);
        infowindow.open(map, marker);
       
    });
    // this function will work on marker move event into map 
    google.maps.event.addListener(marker, 'dragend', function() {
        geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {        
              bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
              infowindow.setContent(results[0].formatted_address);
              // infowindow.open(map, marker);
          }
        }
        });
    });
}
function bindDataToForm(address,lat,lng){
   document.getElementById('address').value = address;
   document.getElementById('latitude').value = lat;
   document.getElementById('longitude').value = lng;
}
google.maps.event.addDomListener(window, 'load', initialize);
</script> 

<style type="text/css">
    .input-controls {
      margin-top: 10px;
      border: 1px solid transparent;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      height: 32px;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchInput {
      background-color: #fff;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      margin-left: 12px;
      padding: 0 11px 0 13px;
      text-overflow: ellipsis;
      width: 50%;
    }
    #searchInput:focus {
      border-color: #4d90fe;
    }
</style>
@endsection