<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\ItemGroups;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use File;
use Image;
class ItemGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,6,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,6,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,6,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,6,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,3);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,3);
        
        return $privileges;
     }

    public function index()
    {
       if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $item_groups = DB::table('item_groups')
        ->select(DB::raw('*'))
        ->get();
         return View('itemgroups.index', compact('item_groups'))         
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
        return View('itemgroups.create')          
        ->with('privileges',$privileges);
    }

    private function saveLogoInTempLocation($file)
     {
        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_ITEM_GROUP_TEMP_PATH');
        $extension = $file->getClientOriginalExtension();
        $filename = $session_id . '.' . $extension;
        $upload_success = $file->move($tempdestinationPath, $filename);
        return $extension;
     }

     private function saveLogoInLogoPath($itemgropuid, $extension)
    {
        $session_id = Session::getId();
        $sourceDir = env('CONTENT_ITEM_GROUP_TEMP_PATH');
        $destinationDir = env('CONTENT_ITEM_GROUP_PATH');
        $success = File::copy($sourceDir . '//' . $session_id . '.' .  $extension, $destinationDir . '//' . $itemgropuid . '.' .  $extension);        
        try {
            $success = File::delete($sourceDir . '//' . $session_id . '.' .  $extension);     
        } catch (Exception $e) {
        }
        
        createThumbnailImage($destinationDir,$itemgropuid,$extension);
    }

    private function deleteLogo($itemgropuid, $extension)
    {
        $sourceDir = env('CONTENT_ITEM_GROUP_PATH');
        try {
            $success = File::delete($sourceDir . '//' . $itemgropuid . '.' .  $extension);        
        } catch (Exception $e) {
        }
        try {
            $success = File::delete($sourceDir . '//' . $itemgropuid . '_t.' .  $extension);        
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

        $this->validate($request, [
            'group_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('itemgroups.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $itemgroups = new ItemGroups();
            $itemgroups->group_name = Input::get('group_name');
            $itemgroups->is_visible = 1;
            $itemgroups->display_order = Input::get('display_order');;
            $itemgroups->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemgroups->added_by = Session::get('user_id');
            $itemgroups->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemgroups->modified_by = Session::get('user_id');
             if($file <> null)
                $itemgroups->logo_extension = $extension; 
            $itemgroups->save();           

            if(!empty($extension))
            {
             $destinationDir = env('CONTENT_ITEM_GROUP_PATH');            
             $LogoPath=$destinationDir . '/' . $itemgroups->id . '.' .  $itemgroups->logo_extension;
             $itemgroups->img_url =  $LogoPath;
             $itemgroups->update();
            }
             if($file <> null)
                $this->saveLogoInLogoPath($itemgroups->id, $extension);

            $log = new Log();
            $log->module_id=6;
            $log->action='create';      
            $log->description='Item Groups ' . $itemgroups->group_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('itemgroups.index')->with('success','Item Groups Created Successfully!');
        
        }
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
        if($privileges['Edit'] !='true')
            return Redirect::to('/');        
        $itemgroups = ItemGroups::find($id);
        return View('itemgroups.edit')
        ->with('itemgroups',$itemgroups)
        ->with('privileges',$privileges);
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

        $this->validate($request, [
            'group_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('itemgroups.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {  
            $itemgroups = ItemGroups::find($id);
             if($file <> null)
             {
            $success = File::delete($itemgroups->img_url);
            $LogoPath = env('CONTENT_ITEM_GROUP_PATH') . '/' . $itemgroups->id .  '_t.' . $itemgroups->logo_extension ;
            $delete=File::delete($LogoPath);
            }             
            
              if(empty($extension))
            {
                $destinationDir = env('CONTENT_ITEM_GROUP_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $itemgroups->logo_extension; 
            }
            else
            {
                $destinationDir = env('CONTENT_ITEM_GROUP_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $extension;
            }

           
            $itemgroups->group_name = Input::get('group_name');
            $itemgroups->is_visible = 1;
            $itemgroups->display_order = Input::get('display_order');;
            $itemgroups->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemgroups->added_by = Session::get('user_id');
            $itemgroups->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemgroups->modified_by = Session::get('user_id');
            $itemgroups->img_url = $LogoPath;
            $itemgroups->Update();           

            if($file <> null)
                $itemgroups->logo_extension = $extension;
            $itemgroups->update();          

            if($file <> null)
                $this->saveLogoInLogoPath($itemgroups->id, $extension);

            $log = new Log();
            $log->module_id=6;
            $log->action='update';      
            $log->description='Item Groups ' . $itemgroups->group_name . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('itemgroups.index')->with('success','Item Groups Updated Successfully!');
        
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
        $itemgroups = ItemGroups::find($id);
        if (is_null($itemgroups))
        {
         return Redirect::back()->with('warning','Item Groups Details Are Not Found!');
        }
        else
        {
           ItemGroups::find($id)->delete();

            try {
                $this->deleteLogo($itemgroups->id, $itemgroups->logo_extension);
            } catch (Exception $e) {
            }

            $log = new Log();
            $log->module_id=6;
            $log->action='delete';      
            $log->description='Item Groups '. $itemgroups->group_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Item Groups Deleted Successfully!');
        }
    }
}
