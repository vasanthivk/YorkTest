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
        $menusections = DB::table('menu_section')
        ->select(DB::raw('menu_section.*,if(ifnull(menu_section.is_visible,1)=1,"Visible","InVisible") as status'))
        ->get();
         return View('menusections.index', compact('menusections'))         
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
        $menus = DB::table('menu')
            ->where('is_visible','=','1')
            ->where('company','=','FoodAdvisr')
            ->select(DB::raw('menu.menu,menu.ref as id'))
            ->pluck('menu','id');
        $menus_count = $menus->count();
        if($menus_count == 0)
        {
            return Redirect::back()->with('warning','Please Add Menu Details While Adding Menu Section!');
        }
        return View('menusections.create')          
        ->with('menus',$menus)
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
            'section_name'  => 'required']);        
        
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
            $menusection = new MenuSection();
            $menusection->section_name = Input::get('section_name');
            $menusection->description = Input::get('description');
            $menusection->menu_id = Input::get('menu_id');
            $menusection->is_visible = (Input::get('is_visible')== ''  ? '0' : '1');
            $menusection->display_order = 1;
            $menusection->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusection->added_by = Session::get('user_id');
            $menusection->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusection->modified_by = Session::get('user_id');            
            $menusection->save();           

            $log = new Log();
            $log->module_id=7;
            $log->action='create';      
            $log->description='Menu Section ' . $menusection->section_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menusections.index')->with('success','Menu Section Created Successfully!');
        
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
        $menusections = MenuSection::find($id);
        $menus = DB::table('menu')
        ->where('is_visible','=','1')
        ->where('company','=','FoodAdvisr')
        ->select(DB::raw('menu.menu,menu.ref as id'))
        ->pluck('menu','id');
        return View('menusections.edit')          
        ->with('menusections',$menusections)
        ->with('menus',$menus)
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
            'section_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('menusections.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {  
            $menusection = MenuSection::find($id);            
            $menusection->section_name = Input::get('section_name');
            $menusection->description = Input::get('description');
            $menusection->menu_id = Input::get('menu_id');
            $menusection->is_visible = (Input::get('is_visible')== ''  ? '0' : '1');
            $menusection->display_order = 1;
            $menusection->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusection->added_by = Session::get('user_id');
            $menusection->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $menusection->modified_by = Session::get('user_id'); 
            $menusection->Update();           

            $log = new Log();
            $log->module_id=7;
            $log->action='update';      
            $log->description='Menu Section ' . $menusection->section_name . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menusections.index')->with('success','Menu Section Updated Successfully!');
        
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
        $menusections = MenuSection::find($id);
        if (is_null($menusections))
        {
         return Redirect::back()->with('warning','Menu Section Details Are Not Found!');
        }
        else
        {
           MenuSection::find($id)->delete();
          
            $log = new Log();
            $log->module_id=7;
            $log->action='delete';      
            $log->description='Menu Section '. $menusections->section_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Menu Section Deleted Successfully!');
        }
    }
}
