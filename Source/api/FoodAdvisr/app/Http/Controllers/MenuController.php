<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Menu;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use File;
use Image;

class MenuController extends Controller
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
        $menus = DB::table('menu')
        ->select(DB::raw('menu.ref as id,menu.menu,menu.submenu,if(ifnull(menu.is_visible,1)=1,"Visible","InVisible") as status'))
        ->get();
         return View('menu.index', compact('menus'))         
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
        return View('menu.create')          
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
            'menu'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('menu.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {   
       
            $menu = new Menu();
            $menu->menu =  Input::get('menu');
            $menu->submenu =  (Input::get('submenu')=='' ? ' ' : Input::get('submenu'));
            $menu->description =  (Input::get('description')=='' ? ' ' : Input::get('description'));
            $menu->is_visible =  (Input::get('is_visible')== ''  ? '0' : '1');
            $menu->sort_order =  1;
            $menu->save();         

            $log = new Log();
            $log->module_id=6;
            $log->action='create';      
            $log->description='Menu ' . $menu->menu . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menu.index')->with('success','Menu Created Successfully!');
        
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
        $menu = Menu::where('ref','=',$id)->get();
        return View('menu.edit')
        ->with('menu',$menu[0])
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
            'menu'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('menu.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {   
            Menu::where('ref','=',$id)
            ->update(array(
                'menu'=> Input::get('menu'),
                'submenu'=> (Input::get('submenu')=='' ? ' ' : Input::get('submenu')),
                'description'=> (Input::get('description')=='' ? ' ' : Input::get('description')),
                'is_visible'=> (Input::get('is_visible')== ''  ? '0' : '1'),
                'sort_order'=> 1
            ));

            $log = new Log();
            $log->module_id=6;
            $log->action='update';      
            $log->description='Menu ' . Input::get('menu') . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menu.index')->with('success','Menu Updated Successfully!');
        
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
        $menu =  Menu::where('ref','=',$id)->get();
        if (is_null($menu))
        {
         return Redirect::back()->with('warning','Menu Details Are Not Found!');
        }
        else
        {
           Menu::where('ref','=',$id)->delete();

            $log = new Log();
            $log->module_id=6;
            $log->action='delete';      
            $log->description='Menu '. $menu[0]->menu . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Menu Deleted Successfully!');
        }
    }
}
