<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Input;
use App\Eateries;
use App\BusinessType;
use App\Locations;
use App\EateriesMedia;
use Session;
use App\Log;
use File;
use Image;
use Carbon\Carbon;
use DateTimeZone;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);


class EateriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,2,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,2,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,2,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,2,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,5);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,4);
        
        return $privileges;
     }

    public function index()
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $location_id = Input::get('location_id');       
        $locations = DB::table('locations')
                    ->select(DB::raw('locations.Description as location_name,locations.LocationID as id'))
                    ->orderby('location_name','asc')
                    ->get();

        if($location_id=='')
        {
            $all_eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.BusinessTypeID', '=', 'eateries.BusinessTypeID')
            ->select(DB::raw('eateries.BusinessName,businesstype.Description as BusinessType,eateries.id,eateries.LogoExtension'))
            ->where('eateries.LocationID','=','')
            ->get();
        }
        else{
        $all_eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.BusinessTypeID', '=', 'eateries.BusinessTypeID')
            ->select(DB::raw('eateries.BusinessName,businesstype.Description as BusinessType,eateries.id,eateries.LogoExtension'))
            ->where('eateries.LocationID','=',$location_id)
            ->get();
        }
        
        return view('eateries.index')
        ->with('all_eateries',$all_eateries)
        ->with('locations',$locations)
        ->with('location_id',$location_id)
         ->with('privileges',$privileges);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        if($privileges['Add'] !='true')    
            return Redirect::to('/');
        $businesstypes = BusinessType::all()->pluck('Description','BusinessTypeID');
        $locations = Locations::all()->pluck('Description','Description');

        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_EATERY_IMAGE_TEMP_PATH') . '\\'. $session_id;
        File::makeDirectory($tempdestinationPath, 0775, true, true);
        $fileslist = [];
        $filesInFolder = File::files($tempdestinationPath);
        foreach($filesInFolder as $path)
        {
             $fileslist[] = pathinfo($path)['basename'];
        }

        return view('eateries.create')
        ->with('privileges',$privileges)
        ->with('businesstypes',$businesstypes)
        ->with('locations',$locations)
        ->with('fileslist',$fileslist);
    }


    private function saveLogoInTempLocation($file)
     {
        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_EATERY_LOGO_TEMP_PATH');
        $extension = $file->getClientOriginalExtension();
        $filename = $session_id . '.' . $extension;
        $upload_success = $file->move($tempdestinationPath, $filename);
        return $extension;
     }

     private function saveImageInTempLocation($file)
     {
        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_EATERY_IMAGE_TEMP_PATH') . '\\'. $session_id;
        File::makeDirectory($tempdestinationPath, 0775, true, true);
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid('', true) . '.' . $extension;
        while (true) {
            $filename = uniqid('', true) . '.' . $extension;
            if (!file_exists($tempdestinationPath . '\\' . $filename)) break;
        }
        $upload_success = $file->move($tempdestinationPath, $filename);
     }

      private function saveEateryMedia($eateryid)
    {
        $session_id = Session::getId();
        $sourceDir = env('CONTENT_EATERY_IMAGE_TEMP_PATH') . '\\'. $session_id;
        $destinationDir= env('CONTENT_EATERY_IMAGE_PATH') . '\\'. $eateryid;
        $success = File::deleteDirectory($destinationDir);        
        $success = File::copyDirectory($sourceDir, $destinationDir);
        $success = File::deleteDirectory($sourceDir);        
    }

    private function saveLogoInLogoPath($eateryid, $extension)
    {
        $session_id = Session::getId();
        $sourceDir = env('CONTENT_EATERY_LOGO_TEMP_PATH');
        $destinationDir = env('CONTENT_EATERY_LOGO_PATH');
        $success = File::copy($sourceDir . '//' . $session_id . '.' .  $extension, $destinationDir . '//' . $eateryid . '.' .  $extension);        
        try {
            $success = File::delete($sourceDir . '//' . $session_id . '.' .  $extension);     
        } catch (Exception $e) {
        }
        
        createThumbnailImage($destinationDir,$eateryid,$extension);
    }

    private function deleteLogo($eateryid, $extension)
    {
        $sourceDir = env('CONTENT_EATERY_LOGO_PATH');
        try {
            $success = File::delete($sourceDir . '//' . $eateryid . '.' .  $extension);        
        } catch (Exception $e) {
        }
        try {
            $success = File::delete($sourceDir . '//' . $eateryid . '_t.' .  $extension);        
        } catch (Exception $e) {
        }
    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = Input::all();        

        $file_size = $_FILES['logo']['size'];       
        if($file_size > 2097152)
            {
                 return Redirect::back()->with('warning','File size must be less than 2 MB!')
                 ->withInput();
            }
      
        $file = array_get($input,'logo');
        $extension = '';
        if($file <> null)
            $extension = $this->saveLogoInTempLocation($file);

         $file = array_get($input,'imagefile1');
        if($file <> null)
            $this->saveImageInTempLocation($file);

        $file = array_get($input,'imagefile2');
        if($file <> null)
            $this->saveImageInTempLocation($file);

        $file = array_get($input,'imagefile3');
        if($file <> null)
            $this->saveImageInTempLocation($file);

        $this->validate($request,['FHRSID'  => 'required|unique:eateries','BusinessName'  => 'required','WebSite'=>'required','EmailId' =>'required|email','Longitude'=>'required','Latitude'=>'required','Address' => 'required','ContactNumber' => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('eateries.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {   
            $location_id = Locations::where('Description','=',$input['LocationID'])->get();
             
            $eateries = new Eateries();
            $eateries->FHRSID = Input::get('FHRSID');
            $eateries->BusinessName = Input::get('BusinessName');
            $eateries->LocalAuthorityBusinessID = Input::get('LocalAuthorityBusinessID');
            $eateries->BusinessTypeID = Input::get('BusinessTypeID');
            $eateries->Address = Input::get('Address');
            $eateries->ContactNumber = Input::get('ContactNumber');
            $eateries->WebSite = Input::get('WebSite');
            $eateries->EmailId = Input::get('EmailId');
            $eateries->LocationId = $location_id[0]['LocationID'];
            $eateries->Longitude = Input::get('Longitude');
            $eateries->Latitude = Input::get('Latitude');
            $eateries->CreatedOn = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $eateries->IsAssociated =  (Input::get('IsAssociated')== ''  ? '0' : '1');
            $eateries->AssociatedOn = Input::get('AssociatedOn');
             if($file <> null)
                $eateries->LogoExtension = $extension;        
            $eateries->save();

            $this->saveEateryMedia($eateries->id);
            $this->saveEateryMediaInDB($eateries->id);

            if(!empty($extension))
            {
             $destinationDir = env('CONTENT_EATERY_LOGO_PATH');            
             $LogoPath=$destinationDir . '/' . $eateries->id . '.' .  $eateries->LogoExtension;
             $eateries->LogoPath =  $LogoPath;
             $eateries->update();
            }
             if($file <> null)
                $this->saveLogoInLogoPath($eateries->id, $extension);
            
            $log = new Log();
            $log->module_id=2;
            $log->action='create';      
            $log->description='Eateries ' . $eateries->BusinessName . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('eateries.index')->with('success','Eateries Created Successfully!');
        
        }
    }

    private function saveEateryMediaInDB($eateryid)
     {
        $session_id = Session::getId();
        $mediaDir = env('CONTENT_EATERY_IMAGE_PATH') . '\\'. $eateryid;
        $filesInFolder = File::files($mediaDir);
        foreach($filesInFolder as $path)
        {
            $filename = pathinfo($path)['basename'];
            $mediacount = DB::table('eateriesmedia')
            ->where('media_name','=',$filename)
            ->where('eatery_id','=',$eateryid)
            ->count();
            if($mediacount == 0)
            {
                $eateriesmedia = new EateriesMedia();
                $eateriesmedia->eatery_id=$eateryid;
                $extension = strtolower(pathinfo($path)['extension']);                
                $eateriesmedia->media_name=pathinfo($path)['basename'];
                $eateriesmedia->save();
            }
        }
        $eateriesmedias = EateriesMedia::all()->where('eatery_id',$eateryid);
        foreach ($eateriesmedias as $eateriesmedia) {
            if(!file_exists($mediaDir . '\\' . $eateriesmedia->media_name)) {
                    EateriesMedia::find($eateriesmedia->id)->delete();
            }  
        }

     }
    
     public function destroyeateryimageedit($imagename)
    {
       $eatery = EateriesMedia::where('media_name',$imagename)->get();
              
       $session_id = Session::getId();
       $myfile = env('CONTENT_EATERY_IMAGE_TEMP_PATH') . '\\'. $session_id . '\\' . $imagename;
        if (File::exists($myfile))
            {
                File::delete($myfile);
            }        
        $sourceDir= env('CONTENT_EATERY_IMAGE_PATH') . '\\'. $eatery[0]->eatery_id . '\\' . $imagename;
        if (File::exists($sourceDir))
            {
                File::delete($sourceDir);
            }  
        EateriesMedia::where('media_name',$imagename)->delete();
        return Redirect::back();
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $eateries =  Eateries::find($id);
        $businesstypes = BusinessType::all()->pluck('Description','BusinessTypeID');
        $locations = Locations::all()->pluck('Description','LocationID');

        $session_id = Session::getId();
        $sourceDir = env('CONTENT_EATERY_IMAGE_PATH') . '\\'. $id;
        $destinationDir = env('CONTENT_EATERY_IMAGE_TEMP_PATH') . '\\'. $session_id;
        if(Session::get('isimagedelete') == '')
        {
            $success = File::deleteDirectory($destinationDir);        
            $success = File::copyDirectory($sourceDir, $destinationDir);
        }
        else
        {
            Session::put('isimagedelete','');
        }       
        
        $fileslist = [];
        $filesInFolder = File::files($destinationDir);
        foreach($filesInFolder as $path)
        {
             //$fileslist[] = pathinfo($path)['dirname'].'/'.pathinfo($path)['basename'];
             $fileslist[] = pathinfo($path)['basename'];
        }

        $eateriesmedia = EateriesMedia::where('eatery_id',$id)->get();

        return View('eateries.edit')
        ->with('privileges',$privileges)
        ->with('eateriesmedia',$eateriesmedia)
        ->with('eateries',$eateries)
        ->with('businesstypes',$businesstypes)
        ->with('locations',$locations)
        ->with('fileslist',$fileslist);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $input = Input::all();

       $file_size = $_FILES['logo']['size'];
        if($file_size > 5097152)
            {
                 return Redirect::back()->with('warning','File size must be less than 2 MB!');
            }

        $file = array_get($input,'logo');
        $extension = '';
        if($file <> null)
            $extension = $this->saveLogoInTempLocation($file);

         $file = array_get($input,'imagefile1');
        if($file <> null)
            $this->saveImageInTempLocation($file);

        $file = array_get($input,'imagefile2');
        if($file <> null)
            $this->saveImageInTempLocation($file);

        $file = array_get($input,'imagefile3');
        if($file <> null)
            $this->saveImageInTempLocation($file);
      
        $this->validate($request,['FHRSID'  => 'required','BusinessName'  => 'required','WebSite'=>'required','EmailId' =>'required|email','Longitude'=>'required','Latitude'=>'required','Address' => 'required','ContactNumber' => 'required']);         
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('eateries.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $location_id = Locations::where('Description','=',$input['LocationID'])->get();

            $eateries = Eateries::find($id);

            if($file <> null)
             {
            $success = File::delete($eateries->LogoPath);
            $LogoPath = env('CONTENT_EATERY_LOGO_PATH') . '/' . $eateries->id .  '_t.' . $eateries->LogoExtension ;
            $delete=File::delete($LogoPath);
            }             
            
              if(empty($extension))
            {
                $destinationDir = env('CONTENT_EATERY_LOGO_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $eateries->LogoExtension; 
            }
            else
            {
                $destinationDir = env('CONTENT_EATERY_LOGO_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $extension;
            }

            $eateries->FHRSID = Input::get('FHRSID');
            $eateries->BusinessName = Input::get('BusinessName');
            $eateries->LocalAuthorityBusinessID = Input::get('LocalAuthorityBusinessID');
            $eateries->BusinessTypeID = Input::get('BusinessTypeID');
            $eateries->Address = Input::get('Address');
            $eateries->ContactNumber = Input::get('ContactNumber');
            $eateries->WebSite = Input::get('WebSite');
            $eateries->EmailId = Input::get('EmailId');
            $eateries->LocationId = Input::get('LocationID');
            $eateries->Longitude = Input::get('Longitude');
            $eateries->Latitude = Input::get('Latitude');
            $eateries->CreatedOn = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $eateries->LogoPath =  $LogoPath;
            $eateries->IsAssociated =  (Input::get('IsAssociated')== ''  ? '0' : '1');
            $eateries->AssociatedOn = Input::get('AssociatedOn');
            
            if($file <> null)
                $eateries->LogoExtension = $extension;
            $eateries->update();          

            if($file <> null)
                $this->saveLogoInLogoPath($eateries->id, $extension);

            $this->saveEateryMedia($eateries->id);
            $this->saveEateryMediaInDB($eateries->id);

            $log = new Log();
            $log->module_id=2;
            $log->action='update';      
            $log->description='Eateries ' . $eateries->BusinessName . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('eateries.index')->with('success','Eateries Updated Successfully!');
        
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eateries = Eateries::find($id);
        if (is_null($eateries))
        {
         return Redirect::back()->with('warning','Eateries Details Are Not Found!');
        }
        else
        {
           Eateries::find($id)->delete();
           EateriesMedia::where('eatery_id',$id)->delete();

           try {
                $sourceDir = env('CONTENT_EATERY_IMAGE_TEMP_PATH') . '\\'. Session::getId();
                $success = File::deleteDirectory($sourceDir);        
                $sourceDir= env('CONTENT_EATERY_IMAGE_PATH') . '\\'. $id;
                $success = File::deleteDirectory($sourceDir);  
            } catch (Exception $e) {
            }

            $log = new Log();
            $log->module_id=2;
            $log->action='delete';      
            $log->description='Eateries '. $eateries->BusinessName . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Eateries Deleted Successfully!');
        }
    }
}
