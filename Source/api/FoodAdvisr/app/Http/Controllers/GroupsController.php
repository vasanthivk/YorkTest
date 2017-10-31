<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Groups;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,3,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,3,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,3,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,3,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,3);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,3);
        
        return $privileges;
     }

    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $groups = DB::table('groups')
        ->select(DB::raw('*'))
        ->get();
         return View('groups.index', compact('groups'))         
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
        return View('groups.create')          
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
            'description'  => 'required']);
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('groups.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $groups = new Groups();
            $groups->Description =  Input::get('description');
            $groups->save();            

            $log = new Log();
            $log->module_id=3;
            $log->action='create';      
            $log->description='Groups ' . $groups->description . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('groups.index')->with('success','Groups Created Successfully!');
        
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
        $groups = Groups::find($id);
        return View('groups.edit')
        ->with('groups',$groups)
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
            'description'  => 'required']);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) 
        {
            return Redirect::route('groups.edit',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            $groups = Groups::find($id);
            $groups->Description =  Input::get('description');
            $groups->save(); 

            $log = new Log();
            $log->module_id=3;
            $log->action='update';      
            $log->description='Groups ' . $groups->description . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('groups.index')->with('success','Groups Updated Successfully!');
        
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
         $groups = Groups::find($id);
        if (is_null($groups))
        {
         return Redirect::back()->with('warning','Groups Details Are Not Found!');
        }
        else
        {
            Groups::find($id)->delete();
            $log = new Log();
            $log->module_id=3;
            $log->action='delete';      
            $log->description='Groups '. $groups->description . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Groups Deleted Successfully!');
        }
    }
}
