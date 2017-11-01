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
use App\Eateries;
use App\Locations;
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
        $users = DB::table('person')
               ->join('role', 'role.id', '=', 'person.roles')
                ->select(DB::raw('person.*,role.name as role_name,if(ifnull(person.status,1)=1,"Active","Inactive") as status'))
                ->get();
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
            'email'  => 'required|email|unique:person','password'  => 'required']);        
        
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
            $token = openssl_random_pseudo_bytes(12);
            $token = bin2hex($token);
            $person = new User();
            $person->ref =  $token;
            $person->email =  Input::get('email');
            $person->firstnames =  (Input::get('firstnames')== ''  ? ' ' : Input::get('firstnames'));
            $person->surname =  (Input::get('surname')== ''  ? ' ' : Input::get('surname'));
            $person->password =  password_hash(Input::get('password'), PASSWORD_DEFAULT);
            $person->roles =  Input::get('roles'); 
            $person->status =  Input::get('status');
            $person->mobileno =  (Input::get('mobileno')== ''  ? ' ' : Input::get('mobileno'));
            $person->pin =  ' ';
            $person->hash =  ' ';
            $person->adminroles =  ' ';
            $person->lastloginstate =  ' ';
            $person->lastlogintime =  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $person->failedlogins =  0;
            $person->secret =  ' ';
            $person->date_created =  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $person->created_by =  Input::get('roles');
            $person->date_modified =   Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $person->modified_user_id =  Input::get('roles');
            $person->settings =  ' ';
            $person->save();            

            $log = new Log();
            $log->module_id=4;
            $log->action='create';      
            $log->description='User ' . $person->firstnames . ' is created';
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

         $this->validate($request, [
            'email'  => 'required']);
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
            
            $person = User::find($id);
            $token = openssl_random_pseudo_bytes(12);
            $token = bin2hex($token);
            $person->ref =  $token;
            $person->email =  Input::get('email');
            $person->firstnames =  (Input::get('firstnames')== ''  ? ' ' : Input::get('firstnames'));
            $person->surname =  (Input::get('surname')== ''  ? ' ' : Input::get('surname'));
            $person->password =  password_hash(Input::get('password'), PASSWORD_DEFAULT);
            $person->roles =  Input::get('roles'); 
            $person->status =  Input::get('status');
            $person->mobileno =  (Input::get('mobileno')== ''  ? ' ' : Input::get('mobileno'));
            $person->pin =  ' ';
            $person->hash =  ' ';
            $person->adminroles =  ' ';
            $person->lastloginstate =  ' ';
            $person->lastlogintime =  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $person->failedlogins =  0;
            $person->secret =  ' ';
            $person->date_created =  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $person->created_by =  Input::get('roles');
            $person->date_modified =   Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $person->modified_user_id =  Input::get('roles');
            $person->settings =  ' ';
            $person ->update();

            $log = new Log();
            $log->module_id=4;
            $log->action='update';      
            $log->description='User ' . $person->firstnames . ' is updated';
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
            $log->description='User '. $user->firstnames . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','User Deleted Successfully!');
        }
    }
}
