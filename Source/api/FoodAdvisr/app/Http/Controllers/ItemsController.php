<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Company;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,5,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,5,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,5,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,5,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,5);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,5);
        
        return $privileges;
     }

    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $companies = DB::table('company')
        ->select(DB::raw('*'))
        ->get();
         return View('company.index', compact('companies'))         
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
        return View('company.create')          
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
            'company_name'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('company.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $company = new Company();
            $company->company_name =  Input::get('company_name');
            $company->save();            

            $log = new Log();
            $log->module_id=5;
            $log->action='create';      
            $log->description='Company ' . $company->company_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('company.index')->with('success','Company Created Successfully!');
        
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
        $company = Company::find($id);
        return View('company.edit', compact('company'))
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
            'company_name'  => 'required']);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) 
        {
            return Redirect::route('company.edit',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            $company = Company::find($id);
            $company->company_name =  Input::get('company_name');
            $company ->update();

            $log = new Log();
            $log->module_id=5;
            $log->action='update';      
            $log->description='Company ' . $company->company_name . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('company.index')->with('success','Company Updated Successfully!');
        
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
         $company = Company::find($id);
        if (is_null($company))
        {
         return Redirect::back()->with('warning','User Details Are Not Found!');
        }
        else
        {
           Company::find($id)->delete();
            $log = new Log();
            $log->module_id=5;
            $log->action='delete';      
            $log->description='Company '. $company->company_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Company Deleted Successfully!');
        }
    }
}
