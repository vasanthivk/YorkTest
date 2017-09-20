<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Privileges;
use App\RoleModulePrivileges;
use App\Module;
use App\Role;
use View;
use Input;
use DB;
use DateTimeZone;

class PrivilegesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request['role'])
            $selectedrole = $request['role'];
        else
            $selectedrole='';
         $allroles = Role::all();
        // $roles = $allroles->lists('name','id');
        $roles = Role::select(DB::raw('concat (name," (",case role_type  when  1 then \'User\' else \'Operator\' end,")") as name,id'))->pluck('name', 'id');
        $modules = Module::all();
        $privileges = Privileges::all();
        if($selectedrole=='')
            $selectedrole = $allroles[0]->id;
        $rolemoduleprivileges = RoleModulePrivileges::where('role_id','=',$selectedrole)->get();
        return View::make('privileges.matrix')
        ->with('roles',$roles)
        ->with('modules',$modules)
        ->with('privileges',$privileges)
        ->with('rolemoduleprivileges',$rolemoduleprivileges)
        ->with('selectedrole',$selectedrole);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$role_id,$module_id,$privilege_id)
    {
        
           //
       
    }
    public function privilegesmatrix(Request $request)
    {
        if($request['role'])
            $selectedrole = $request['role'];
        else
            $selectedrole='';
         $allroles = Role::all();
        // $roles = $allroles->lists('name','id');
        $roles = Role::select(DB::raw('concat (name," (",case role_type  when  1 then \'User\' else \'Operator\' end,")") as name,id'))->lists('name', 'id');
        $modules = Module::all();
        $privileges = Privileges::all();
        if($selectedrole=='')
            $selectedrole = $allroles[0]->id;

        $rolemoduleprivileges = RoleModulePrivileges::where('role_id','=',$selectedrole)->get();

        return View::make('privileges.matrix')
        ->with('roles',$roles)
        ->with('modules',$modules)
        ->with('privileges',$privileges)
        ->with('rolemoduleprivileges',$rolemoduleprivileges)
        ->with('selectedrole',$selectedrole);
    }
    public function allowprivileges(Request $request,$role_id, $module_id,$privilege_id)
    {
        $privileges = new RoleModulePrivileges();
       
        $privileges->role_id =  $role_id;
        $privileges->module_id =  $module_id;
        $privileges->privilege_id =  $privilege_id;
        $privileges->save();
        $request->session()->flash('alert-success', '');
        return Redirect::back()->with('success','Privileges Successfully Allowed!');
        
    }
    public function denyprivileges(Request $request,$role_id,$module_id,$privilege_id)
    {
        DB::table('rolemoduleprivileges')
            ->where('role_id', $role_id)
            ->where('module_id',$module_id)
            ->where('privilege_id',$privilege_id)
            ->delete();
        return Redirect::back()->with('warning','Privileges Successfully Deny!');
    }
}
