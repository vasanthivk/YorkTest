<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Ingredients;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
class IngredientsController extends Controller
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
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,6);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,6);
        
        return $privileges;
     }

    public function index()
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $ingredients = DB::table('ingredients')
        ->select(DB::raw('*,ingredients.ingredient_id as id,if(ifnull(ingredients.is_visible,1)=1,"Active","Inactive") as is_visible'))
        ->get();
         return View('ingredients.index', compact('ingredients'))         
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
        return View('ingredients.create')          
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
            return Redirect::route('ingredients.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $ingredients = new Ingredients();
            $ingredients->title =  Input::get('title');
            $ingredients->description =  Input::get('description');
            $ingredients->is_visible =  Input::get('is_visible');
            $ingredients->save();            

            $log = new Log();
            $log->module_id=6;
            $log->action='create';      
            $log->description='ingredients ' . $ingredients->title . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->ingredients=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('ingredients.index')->with('success','Ingredients Created Successfully!');
        
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
        $ingredients = Ingredients::where('ingredients.ingredient_id',$id)->get();
        
        return View('ingredients.edit')
        ->with('ingredients',$ingredients[0])
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
            return Redirect::route('ingredients.edit',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            Ingredients::where('ingredient_id','=',$id)
             ->update(array('title'=> Input::get('title'),'description'=> Input::get('description'),'is_visible'=> Input::get('is_visible')
                 ));

            $log = new Log();
            $log->module_id=6;
            $log->action='update';      
            $log->description='ingredients ' . Input::get('ingredients_name') . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->ingredients=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('ingredients.index')->with('success','Ingredients Updated Successfully!');
        
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
         $ingredients = Ingredients::where('ingredient_id','=',$id)->get();
         
        if (is_null($ingredients))
        {
         return Redirect::back()->with('warning','ingredients Details Are Not Found!');
        }
        else
        {
           Ingredients::where('ingredient_id','=',$id)->delete();
            $log = new Log();
            $log->module_id=6;
            $log->action='delete';      
            $log->description='ingredients '. $ingredients[0]->title . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->ingredients=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Ingredients Deleted Successfully!');
        }
    }
}
