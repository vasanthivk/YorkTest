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
use App\MenuSubSection;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use File;
use Image;

class MenuSubSectionController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $menusubsections = DB::table('menu_sub_section')
        ->select(DB::raw('menu_sub_section.*,if(ifnull(menu_sub_section.is_visible,1)=1,"Visible","InVisible") as status'))
        ->get();
         return View('menusubsections.index', compact('menusubsections'))         
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
        $sections = MenuSection::pluck('section_name','id');
        return View('menusubsections.create')  
        ->with('sections',$sections)
        ->with('privileges',$privileges);
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

        $this->validate($request, [
            'sub_section_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('menusubsections.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $menusubsection = new MenuSubSection();
            $menusubsection->sub_section_name = Input::get('sub_section_name');
            $menusubsection->description = Input::get('description');
            $menusubsection->section_id = Input::get('section_id');
            $menusubsection->is_visible = (Input::get('is_visible')== ''  ? '0' : '1');
            $menusubsection->display_order = 1;
            $menusubsection->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusubsection->added_by = Session::get('user_id');
            $menusubsection->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusubsection->modified_by = Session::get('user_id');            
            $menusubsection->save();           

            $log = new Log();
            $log->module_id=7;
            $log->action='create';      
            $log->description='Menu Sub Section ' . $menusubsection->sub_section_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menusubsections.index')->with('success','Menu Sub Section Created Successfully!');
        
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
        $menusubsections = MenuSubSection::find($id);
        $sections = MenuSection::pluck('section_name','id');
        return View('menusubsections.edit')          
        ->with('menusubsections',$menusubsections)
        ->with('sections',$sections)
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

        $this->validate($request, [
            'sub_section_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('menusubsections.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $menusubsection = MenuSubSection::find($id);
            $menusubsection->sub_section_name = Input::get('sub_section_name');
            $menusubsection->description = Input::get('description');
            $menusubsection->section_id = Input::get('section_id');
            $menusubsection->is_visible = (Input::get('is_visible')== ''  ? '0' : '1');
            $menusubsection->display_order = 1;
            $menusubsection->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusubsection->added_by = Session::get('user_id');
            $menusubsection->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusubsection->modified_by = Session::get('user_id');            
            $menusubsection->save();           

            $log = new Log();
            $log->module_id=7;
            $log->action='create';      
            $log->description='Menu Sub Section ' . $menusubsection->sub_section_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menusubsections.index')->with('success','Menu Sub Section Created Successfully!');
        
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
         $menusubsections = MenuSubSection::find($id);
        if (is_null($menusubsections))
        {
         return Redirect::back()->with('warning','Menu Sub Section Details Are Not Found!');
        }
        else
        {
           MenuSubSection::find($id)->delete();
          
            $log = new Log();
            $log->module_id=7;
            $log->action='delete';      
            $log->description='Menu Sub Section '. $menusubsections->sub_section_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Menu Sub Section Deleted Successfully!');
        }
    }
}
