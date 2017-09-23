<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Users;
use View;
use Input;
use DB;
use App\Role;
use Session;
use App\Log;
use Carbon\Carbon;
use Lang;
use DateTimeZone;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function index(Request $request)
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
       
        $selectedrole = Input::get('role');
        $allroles = Role::all();
        $roles = Role::select(DB::raw('concat (name," (",case role_type  when  1 then \'User\' else \'Operator\' end,")") as name,id'))->pluck('name', 'id');        
        if($selectedrole=='')
            $selectedrole = $allroles[0]->id;
        $logs = DB::table('log')
                ->join('module', 'module.id', '=', 'log.module_id')
                ->leftjoin('user', 'user.id', '=', 'log.user_id')
                ->select(DB::raw('log.*,module_name,name,user.role_id'))
                ->Where('user.role_id','=',$selectedrole)
                ->orderBy('created_on', 'desc')
                ->get();
         
        return View::make('logs.index', compact('logs'))
        ->with('roles',$roles)
        ->with('selectedrole',$selectedrole);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
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
    public function destroy(Request $request, $id)
    {
      $log = Log::find($id);

    $request->session()->flash('alert-success', Lang::get('labels.successfullyyourdetailsdeleted'));
     $log = new Log();
            $log->module_id=4;
            $log->action='delete';      
            $log->description='logs ' . $log->module_id . ' is deleted';

            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->operator_id=Session::get("operator_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);

    
       Log::find($id)->delete();
            return Redirect::route('log.index');
    }
}
