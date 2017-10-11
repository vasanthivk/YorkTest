<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use DB;
use App\User;
use App\Role;
use App\Company;
use Input;
use Session;
use App\Log;
use File;
use Image;
use Carbon\Carbon;
use DateTimeZone;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */   

    private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,4,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,4,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,4,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,4,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,5);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,4);
        
        return $privileges;
     }

    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $users = DB::table('user')
               ->join('role', 'role.id', '=', 'user.role_id')
                ->select(DB::raw('user.*,role.name as role_name,if(ifnull(user.status,1)=1,"Active","Inactive") as status'))
                ->get();  
                // return $users;     
         return View::make('user.index', compact('users'))         
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
        $role = Role::all()->pluck('name','id');
       
        return View::make('user.create')
        ->with('role',$role)            
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
            'login'  => 'required|unique:user','password'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('user.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $user = new user();
            $user->login =  Input::get('login');
            $user->name =  Input::get('name');
            $user->password =  Input::get('password');
            $user->role_id =  Input::get('role_id'); 
            $user->status =  Input::get('status');
            $user->mobile_no =  Input::get('mobile_no');
            $user->save();            

            $log = new Log();
            $log->module_id=4;
            $log->action='create';      
            $log->description='User ' . $user->name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('user.index')->with('success','User Created Successfully!');
        
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
        $user = User::find($id);
        $role = Role::all()->pluck('name','id');       
        
        return View::make('user.edit', compact('user'))
        ->with('role',$role)
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

         $file = array_get($input,'logo');
        $extension = '';
        if($file <> null)
            $extension = $this->saveLogoInTempLocation($file);

         $this->validate($request, [
            'login'  => 'required']);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) 
        {
            return Redirect::route('user.edit',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            
            $user = User::find($id);
            $user->login =  Input::get('login');
            $user->name =  Input::get('name');
            $user->role_id =  Input::get('role_id'); 
            $user->status =  Input::get('status');
            $user->mobile_no =  Input::get('mobile_no');
            $user ->update();

            $log = new Log();
            $log->module_id=4;
            $log->action='update';      
            $log->description='User ' . $user->name . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('user.index')->with('success','User Updated Successfully!');
        
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
        $user = User::find($id);       
       
        if (is_null($user))
        {
         return Redirect::back()->with('warning','User Details Are Not Found!');
        }
        else
        {
           user::find($id)->delete();
            $log = new Log();
            $log->module_id=4;
            $log->action='delete';      
            $log->description='User '. $user->name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','User Deleted Successfully!');
        }
    }
}
