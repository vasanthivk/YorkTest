@extends('layouts.master')
@section('title')
FoodAdvisr-Eateries
@endsection
@section('module')
Eateries
@endsection

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

{{ Form::open(array('method' => 'PUT', 'route' => array('eateries.update',$eateries->id),'files'=>true)) }}
<div class="form-group form-horizontal">
    <div class="panel panel-default">
    </br>
      <div class="col-md-6">
            {{ Form::ahNumber('FHRSID','FHRSID :',$eateries->FHRSID,array('min'=>'0','maxlength' => '20','max'=>'99999999999999999999'))  }}
            {{ Form::ahText('LocalAuthorityBusinessID','Business ID :',$eateries->LocalAuthorityBusinessID,array('maxlength'=> '1000'))  }}
            {{ Form::ahText('BusinessName','Business Name :',$eateries->BusinessName,array('maxlength'=> '1000'))  }}
            {{ Form::ahSelect('BusinessTypeID','Business Type :',$eateries->BusinessTypeID,$businesstypes)  }}
            {{ Form::ahTextarea('Address','Address :',$eateries->Address,array("onchange"=>"getlatitudelongitude(this)",'size' => '30x5'))  }}
             {{ Form::ahSelect('LocationID','Location :',$eateries->LocationId,$locations,array("onchange"=>"ChooseContact(this)"))  }}
             {{ Form::ahNumber('ContactNumber','Contact Number :',$eateries->ContactNumber,array('min'=>'0','maxlength' => '12','max'=>'999999999999'))  }}
            {{ Form::ahText('WebSite','WebSite :',$eateries->WebSite,array('maxlength' => '100'))  }}
            {{ Form::ahText('EmailId','EmailId :',$eateries->EmailId,array('maxlength' => '100'))  }}    
            {{ Form::ahText('Longitude','Longitude :',$eateries->Longitude,array("readonly"=>"true"))  }}
            {{ Form::ahText('Latitude','Latitude :',$eateries->Latitude,array("readonly"=>"true"))  }}
            {{ Form::ahCheckbox('IsAssociated','Is Associated :',null,$eateries->IsAssociated) }}     
            {{ Form::ahDate('AssociatedOn','Associated On :', $eateries->AssociatedOn) }} 
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
                if(File::exists(env('CONTENT_EATERY_LOGO_PATH') . '/' . $eateries->id .  '.' . $eateries->LogoExtension))
                {
                    $logo_path = env('CONTENT_EATERY_LOGO_PATH') . '/' . $eateries->id .  '.' . $eateries->LogoExtension ;
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
                      height: 100px;
                      width: 456px;
                      margin: 0 10px;
                    }
                 </style>
                <div class="col-md-6">                    
                  <?php
                $session_id = Session::getId();
                $path = env('CONTENT_EATERY_IMAGE_PATH') . '//'. $eateries->id;
                ?>
              </div>
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
            </div>
          </div>
          <input id="searchInput" name="searchInput" class="input-controls" type="text" placeholder="Enter a location">
              <div id="map" style="margin-left: 27px;width: 433px; height: 250px;"></div>
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
   var latlng = new google.maps.LatLng(<?php echo floatval($eateries->Latitude); ?>,<?php echo floatval($eateries->Longitude); ?>);
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
   document.getElementById('Address').value = address;
   document.getElementById('Latitude').value = lat;
   document.getElementById('Longitude').value = lng;
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