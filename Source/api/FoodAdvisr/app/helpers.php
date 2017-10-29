<?php
use App\RoleModulePrivileges;
use App\Defaults;
use App\Log;
use Carbon\Carbon;

function ValidateUserPrivileges($role_id, $module_id, $privilege_id)
{
    $count  = DB::table('rolemoduleprivileges') 
                    ->where('role_id','=',$role_id)
                    ->where('module_id','=',$module_id)
                    ->where('privilege_id','=',$privilege_id)
                    ->count();
    if($count>0)
        return "true";
    else
        return "false";                  
}
function createLog($log)
{
    $defaults = Defaults::all();

    $validate = true;
    if($log->action == 'create')
    {
        if($defaults[0]->allow_create_logs<>'1')
            $validate = false;
    }
    elseif($log->action == 'update')
    {
        if($defaults[0]->allow_edit_logs<>'1')
            $validate = false;
    }
    elseif($log->action ==  'delete')
    {
        if($defaults[0]->allow_delete_logs<>'1')
            $validate = false;
    }
    
    if($validate)
    {
        $objLog = new Log();
        $objLog->module_id  =$log->module_id;   
        $objLog->created_on  =$log->created_on;
        $objLog->user_id =   $log->user_id;
        $objLog->action      =$log->action;
        $objLog->category    =$log->category;
        $objLog->description =$log->description;
        $objLog->log_type    =$log->log_type;
        $objLog->save();
    }
}

function createThumbnailImage($sourceDir,$identity,$extension)
{
    $info = getimagesize($sourceDir . '//' . $identity . '.' .  $extension);

    if ($info['mime'] == 'image/jpeg') 
    {
        $quality = 60;
        $image = imagecreatefromjpeg($sourceDir . '//' . $identity . '.' .  $extension);
    }
    elseif ($info['mime'] == 'image/gif')
    { 
        $quality = 60;
        $image = imagecreatefromgif($sourceDir . '//' . $identity . '.' .  $extension);
    }
    elseif ($info['mime'] == 'image/png')
    { 
        $quality = 30;
        $image = imagecreatefrompng($sourceDir . '//' . $identity . '.' .  $extension);
    }                 

    $dest_image = $sourceDir . '//' . $identity . '_t.' .  $extension;
    $width =ImageSx($image);
    $height = ImageSy($image);
    $dst = ImageCreateTrueColor($width,$height);
    imagecopyresampled($dst,$image,0,0,0,0,$width,$height,$width,$height);

    imagejpeg($dst,$dest_image, $quality);
}

function getaddress($lat,$lng)
{
$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&key=AIzaSyAo-1vLBBuUJNBS7wOfJpnlsmtHpahSDz4';
$json = @file_get_contents($url);
$data=json_decode($json);
$status = $data->status;
if($status=="OK")
return $data->results;
else
return 0;
}

function getLastNDays($days, $format = 'Y-m-d'){
    $m = date("m"); $de= date("d"); $y= date("Y");
    $dateArray = array();
    for($i=0; $i<=$days-1; $i++){
        $dateArray[] = Carbon::now()->addDays(-1 * $i)->formatLocalized('%Y-%m-%d');; 
    }
    return array_reverse($dateArray);
}

function getEateryByLocation($location_id){
    $eateries =  DB::table('eateries')
                ->where('LocationID','=',$location_id)
                ->select(DB::raw('eateries.id as eatery_id,eateries.BusinessName as eatery_name'))
                ->get();
    return $eateries;
}