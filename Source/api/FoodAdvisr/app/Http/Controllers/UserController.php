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
use App\District;
use App\Mandal;
use App\Village;
use App\Society;
use App\Employee;
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
        $privileges['View']  = ValidateUserPrivileges($roleid,6,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,6,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,6,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,6,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,5);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,6);
        
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
        $employee_count = Employee::all()->count();
        if($employee_count == 0)
        {
           return Redirect::route('user.index')->with('warning','Please add the employee while adding the user!');
        }
        $districtsAll = District::all();
        $districts = District::all()->pluck('name','id');
        $mandals=null;
        if($districts->count()>0)
            $mandals = Mandal::where('district_id','=',$districtsAll[0]->id)->pluck('name','id');

        $mandalsAll=null;
        $villages=null;
        if($mandals->count()>0)
        {
            $mandalsAll = Mandal::where('district_id','=',$districtsAll[0]->id)->get();
            $villages = Village::where('mandal_id','=',$mandalsAll[0]->id)->pluck('name','id');
        }
        $societies=null;
        if($villages->count()>0)
        {
            $villagesAll = Village::where('mandal_id','=',$mandalsAll[0]->id)->get();
            $societies = Society::where('village_id','=',$villagesAll[0]->id)->pluck('name','id');
        }
        $role = Role::all()->pluck('name','id');
        $employeesAll = Employee::all();
        $employees = Employee::all()->pluck('employee_name','id');
        $employee_mobile=null;
        if($employees->count()>0)
            $employee_mobile = Employee::where('id','=',$employeesAll[0]->id)->pluck('mobile','id');
        return View::make('user.create')
        ->with('role',$role)
        ->with('employees',$employees)
        ->with('employee_mobile',$employee_mobile) 
        ->with('districts',$districts)
        ->with('mandals',$mandals)
        ->with('villages',$villages)
        ->with('societies',$societies)       
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
        Session::put('mobile_no',Input::get('mobile_no'));        
        Session::put('district_id',Input::get('district_id'));
        Session::put('mandal_id',Input::get('mandal_id'));
        Session::put('village_id',Input::get('village_id'));
        Session::put('society_id',Input::get('society_id'));
        $this->validate($request, [
            'login'  => 'required|unique:user','password'  => 'required']);
        $employee = Employee::find($input['employee_id']);        
        $user = User::where('name','=',$employee->employee_name)->where('mobile_no','=',$employee->mobile)->count();
        if($user > 0)
        {
           return Redirect::back()->with('warning','Already Created With This User.Please Choose Another User or Update The Role With The Same User!')
            ->withInput();
        }
        
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
            $employee = Employee::find($input['employee_id']);
            $user = new user();
            $user->login =  Input::get('login');
            $user->password =  Input::get('password');
            $user->name =  ucwords($employee->employee_name);
            $user->role_id =  Input::get('role_id');
            // $user->society_id =  Input::get('society_id');
            // $user->village_id =  Input::get('village_id');
            // $user->mandal_id =  Input::get('mandal_id');
            // $user->district_id =  Input::get('district_id');
            $user->mobile_no =  $employee->mobile;
            $user->status =  Input::get('status');
            $user->save();
            $employee->role = $user->role_id;
            $employee ->update();

            $log = new Log();
            $log->module_id=6;
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
        $employee_count = Employee::all()->count();
        if($employee_count == 0)
        {
           return Redirect::route('user.index')->with('warning','Please add the employee while adding the user!');
        }
        $user = User::find($id);
        $role = Role::all()->pluck('name','id');
        $districts = District::where('id','=',$user->district_id)->pluck('name','id');
        $mandals= Mandal::where('id','=',$user->mandal_id)->pluck('name','id');
        $villages = Village::where('id','=',$user->village_id)->pluck('name','id');
        $societies = Society::where('id','=',$user->society_id)->pluck('name','id');
        $employeesAll = Employee::all();
        $employees = Employee::where('employee_name','=',$user->name)->where('mobile','=',$user->mobile_no)->pluck('employee_name','id');
        $employee_mobile = Employee::where('employee_name','=',$user->name)->where('mobile','=',$user->mobile_no)->pluck('mobile','id');


        return View::make('user.edit', compact('user'))
        ->with('role',$role)
         ->with('districts',$districts)
        ->with('mandals',$mandals)
        ->with('villages',$villages)
        ->with('societies',$societies)        
        ->with('privileges',$privileges)
         ->with('employees',$employees)
         ->with('employee_mobile',$employee_mobile);
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
          Session::put('mobile_no',Input::get('mobile_no')); 
          Session::put('district_id',Input::get('district_id'));
        Session::put('mandal_id',Input::get('mandal_id'));
        Session::put('village_id',Input::get('village_id'));
        Session::put('society_id',Input::get('society_id'));
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
            
            
            $employee = Employee::find($input['employee_id']);
            $user = User::find($id);
            $user->login =  Input::get('login');
            $user->name =  ucwords($employee->employee_name);
            $user->role_id =  Input::get('role_id');
            // $user->society_id =  Input::get('society_id');
            // $user->village_id =  Input::get('village_id');
            // $user->mandal_id =  Input::get('mandal_id');
            // $user->district_id =  Input::get('district_id');
            $user->mobile_no =  $employee->mobile;
            $user->status =  Input::get('status');
            $user->update();
            $employee->role = $user->role_id;
            $employee ->update();

            $log = new Log();
            $log->module_id=6;
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
        $empcount = Employee::Where('employee_name',$user->name)->where('mobile','=',$user->mobile_no)->count();
        if($empcount > 0)
        {
            return Redirect::back()->with('warning','User Can Not Delete Since Employee Delete!');            
        }
        if (is_null($user))
        {
         return Redirect::back()->with('warning','User Details Are Not Found!');
        }
        else
        {
           user::find($id)->delete();
           $log = new Log();
            $log->module_id=6;
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
