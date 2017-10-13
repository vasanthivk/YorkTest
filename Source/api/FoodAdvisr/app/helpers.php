<?php
use App\RoleModulePrivileges;
use App\Defaults;
use App\Log;

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

function ValidateForms($formnofrom,$formnoto,$role)
{
  $sql = "select count(*) as cnt from agent where '" . $formnofrom . "' between form_no_from and form_no_to and role='".$role."'";
        $result = DB::select( DB::raw($sql));
        if($result[0]->cnt > 0)
            return false;   

 $sql = "select count(*) as cnt from agent where '" . $formnoto . "' between form_no_from and form_no_to and role='".$role."'";
        $result = DB::select( DB::raw($sql));
        if($result[0]->cnt > 0)
            return false; 

$sql = "select count(*) as cnt from agent where form_no_from between  '" . $formnofrom . "' and '" . $formnoto . "' and role='".$role."'";
        $result = DB::select( DB::raw($sql));
        if($result[0]->cnt > 0)
            return false; 

$sql = "select count(*) as cnt from agent where form_no_to between  '" . $formnofrom . "' and '" . $formnoto . "'  and role='".$role."'";
        $result = DB::select( DB::raw($sql));
        if($result[0]->cnt > 0)
            return false; 

 return true;     
}

function getaddress($lat,$lng)
{
$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&key=AIzaSyAxEE_-gCyfZl77zAbqUHvLcH-9XmiVJFQ';
$json = @file_get_contents($url);
$data=json_decode($json);
$status = $data->status;
if($status=="OK")
return $data->results[0]->formatted_address;
else
return 0;
}