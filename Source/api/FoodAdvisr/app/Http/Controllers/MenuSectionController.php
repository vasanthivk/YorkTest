<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Menu;
use App\MenuSection;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use File;
use Image;

class MenuSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,7,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,7,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,7,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,7,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,3);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,3);
        
        return $privileges;
     }

    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $itemcategories = DB::table('item_categories')
        ->select(DB::raw('*'))
        ->get();
         return View('itemcategory.index', compact('itemcategories'))         
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
        $itegroups = ItemGroups::all()->pluck('group_name','id');
        return View('itemcategory.create')          
        ->with('itegroups',$itegroups)
        ->with('privileges',$privileges);
    }

     private function saveLogoInTempLocation($file)
     {
        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_ITEM_CATEGORY_TEMP_PATH');
        $extension = $file->getClientOriginalExtension();
        $filename = $session_id . '.' . $extension;
        $upload_success = $file->move($tempdestinationPath, $filename);
        return $extension;
     }

     private function saveLogoInLogoPath($itemgropuid, $extension)
    {
        $session_id = Session::getId();
        $sourceDir = env('CONTENT_ITEM_CATEGORY_TEMP_PATH');
        $destinationDir = env('CONTENT_ITEM_CATEGORY_PATH');
        $success = File::copy($sourceDir . '//' . $session_id . '.' .  $extension, $destinationDir . '//' . $itemgropuid . '.' .  $extension);        
        try {
            $success = File::delete($sourceDir . '//' . $session_id . '.' .  $extension);     
        } catch (Exception $e) {
        }
        
        createThumbnailImage($destinationDir,$itemgropuid,$extension);
    }

    private function deleteLogo($itemgropuid, $extension)
    {
        $sourceDir = env('CONTENT_ITEM_CATEGORY_PATH');
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
            'category_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('itemcategory.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $itemcategory = new ItemCategories();
            $itemcategory->category_name = Input::get('category_name');
            $itemcategory->description = Input::get('description');
            $itemcategory->group_id = Input::get('group_id');
            $itemcategory->is_visible = 1;
            $itemcategory->display_order = Input::get('display_order');;
            $itemcategory->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemcategory->added_by = Session::get('user_id');
            $itemcategory->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemcategory->modified_by = Session::get('user_id');
             if($file <> null)
                $itemcategory->logo_extension = $extension; 
            $itemcategory->save();           

            if(!empty($extension))
            {
             $destinationDir = env('CONTENT_ITEM_CATEGORY_PATH');            
             $LogoPath=$destinationDir . '/' . $itemcategory->id . '.' .  $itemcategory->logo_extension;
             $itemcategory->img_url =  $LogoPath;
             $itemcategory->update();
            }
             if($file <> null)
                $this->saveLogoInLogoPath($itemcategory->id, $extension);

            $log = new Log();
            $log->module_id=7;
            $log->action='create';      
            $log->description='Item Category ' . $itemcategory->category_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('itemcategory.index')->with('success','Item Category Created Successfully!');
        
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
        $itemcategories = ItemCategories::find($id);
        $itegroups = ItemGroups::all()->pluck('group_name','id');
        return View('itemcategory.edit')          
        ->with('itegroups',$itegroups)
        ->with('itemcategories',$itemcategories)
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
            'category_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('itemcategory.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {  
            $itemcategory = ItemCategories::find($id);
             if($file <> null)
             {
            $success = File::delete($itemcategory->img_url);
            $LogoPath = env('CONTENT_ITEM_CATEGORY_PATH') . '/' . $itemcategory->id .  '_t.' . $itemcategory->logo_extension ;
            $delete=File::delete($LogoPath);
            }             
            
              if(empty($extension))
            {
                $destinationDir = env('CONTENT_ITEM_CATEGORY_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $itemcategory->logo_extension; 
            }
            else
            {
                $destinationDir = env('CONTENT_ITEM_CATEGORY_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $extension;
            }

           
            $itemcategory->category_name = Input::get('category_name');
            $itemcategory->description = Input::get('description');
            $itemcategory->group_id = Input::get('group_id');
            $itemcategory->is_visible = 1;
            $itemcategory->display_order = Input::get('display_order');;
            $itemcategory->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemcategory->added_by = Session::get('user_id');
            $itemcategory->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $itemcategory->modified_by = Session::get('user_id');
            $itemcategory->img_url = $LogoPath;
            $itemcategory->Update();           

            if($file <> null)
                $itemcategory->logo_extension = $extension;
            $itemcategory->update();          

            if($file <> null)
                $this->saveLogoInLogoPath($itemcategory->id, $extension);

            $log = new Log();
            $log->module_id=7;
            $log->action='update';      
            $log->description='Item Category ' . $itemcategory->group_name . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('itemcategory.index')->with('success','Item Category Updated Successfully!');
        
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
        $itemcategory = ItemCategories::find($id);
        if (is_null($itemcategory))
        {
         return Redirect::back()->with('warning','Item Category Details Are Not Found!');
        }
        else
        {
           ItemCategories::find($id)->delete();
          
             try {
                $this->deleteLogo($itemcategory->id, $itemcategory->logo_extension);
            } catch (Exception $e) {
            }

            $log = new Log();
            $log->module_id=7;
            $log->action='delete';      
            $log->description='Item Category '. $itemcategory->category_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Item Groups Deleted Successfully!');
        }
    }
}
