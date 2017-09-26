<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Nutrition;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
class NutritionController extends Controller
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
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,7);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,7);
        
        return $privileges;
     }

    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $nutritions = DB::table('nutrition')
        ->select(DB::raw('*,nutrition.nutrition_id as id'))
        ->get();
         return View('nutrition.index', compact('nutritions'))         
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
        return View('nutrition.create')          
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
            'title'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('nutrition.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $nutrition = new Nutrition();
            $nutrition->title =  Input::get('title');
            $nutrition->description =  Input::get('description');
            $nutrition->save();            

            $log = new Log();
            $log->module_id=7;
            $log->action='create';      
            $log->description='nutrition ' . $nutrition->title . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->nutrition=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('nutrition.index')->with('success','Nutrition Created Successfully!');
        
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
        $nutrition = nutrition::where('nutrition.nutrition_id',$id)->get();        
        return View('nutrition.edit')
        ->with('nutrition',$nutrition[0])
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
           'title'  => 'required']); 
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) 
        {
            return Redirect::route('nutrition.edit',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            nutrition::where('nutrition_id','=',$id)
             ->update(array('title'=> Input::get('title'),'description'=> Input::get('description')
                 ));

            $log = new Log();
            $log->module_id=7;
            $log->action='update';      
            $log->description='nutrition ' . Input::get('nutrition_name') . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->nutrition=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('nutrition.index')->with('success','Nutrition Updated Successfully!');
        
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
         $nutrition = Nutrition::where('nutrition_id','=',$id)->get();
         
        if (is_null($nutrition))
        {
         return Redirect::back()->with('warning','Nutrition Details Are Not Found!');
        }
        else
        {
           Nutrition::where('nutrition_id','=',$id)->delete();
            $log = new Log();
            $log->module_id=7;
            $log->action='delete';      
            $log->description='nutrition '. $nutrition[0]->title . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->nutrition=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Nutrition Deleted Successfully!');
        }
    }
}
