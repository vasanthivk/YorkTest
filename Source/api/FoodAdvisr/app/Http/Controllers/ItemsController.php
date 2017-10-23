<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Items;
use App\Category;
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
        $privileges['View']  = ValidateUserPrivileges($roleid,8,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,8,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,8,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,8,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,8);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,8);
        
        return $privileges;
     }

    public function index(Request $request)
    {

         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $eatery_id = $request['eatery_id'];
        $items = DB::table('items')
        ->select(DB::raw('*,items.item_id as id,if(ifnull(items.is_visible,1)=1,"Active","Inactive") as is_visible'))
        ->where('items.eatery_id','=',$eatery_id)
        ->get();        
         return View('items.index', compact('items'))         
        ->with('privileges',$privileges)
        ->with('eatery_id',$eatery_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $itemgroups = DB::table('item_groups')
            ->select(DB::raw('group_id,group_name'))
            ->get();
        $itemcategories = DB::table('item_categories')
            ->select(DB::raw('category_id,category_name'))
            ->get();
        return View('items.create')
            ->with('privileges',$privileges)
            ->with('itemcategories',$itemcategories)
            ->with('itemgroups',$itemgroups);
        $eatery_id = $request['eatery_id'];
        $category = Category::all()->pluck('category_name','category_id');

        return View('items.create')          
        ->with('privileges',$privileges)
        ->with('category',$category)
        ->with('eatery_id',$eatery_id);
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
            $eatery_id = $request['eatery_id'];
            return Redirect::route('items.create',array('eatery_id' => $eatery_id))
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $items = new Items();
            $items->title =  Input::get('title');
            $items->description =  Input::get('description');
            $items->FHRSID =  $request['eatery_id'];
            $items->category_id =  Input::get('category_id');
            $items->is_visible =  Input::get('is_visible');
            $items->display_order =  Input::get('display_order');
            $items->save();            

            $log = new Log();
            $log->module_id=8;
            $log->action='create';      
            $log->description='items ' . $items->title . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('items.index',array('eatery_id' => $items->FHRSID))->with('success','Item Created Successfully!');
        
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
    public function edit(Request $request,$id)
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        if($privileges['Edit'] !='true')
            return Redirect::to('/');
        $eatery_id = $request['eatery_id'];
        $category = Category::all()->pluck('category_name','category_id');        
        $items = Items::where('items.item_id',$id)->get();
        return View('items.edit')
        ->with('items',$items[0])
        ->with('eatery_id',$eatery_id)
        ->with('category',$category)
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
            $eatery_id = $request['eatery_id'];
            return Redirect::route('items.edit',$id,array('eatery_id' => $eatery_id))
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            Items::where('item_id','=',$id)
             ->update(array('title'=> Input::get('title'),'description'=> Input::get('description'),'is_visible'=> Input::get('is_visible'),'FHRSID'=> $request['eatery_id'],'category_id'=>Input::get('category_id'),'display_order' => Input::get('display_order')
                 ));

            $log = new Log();
            $log->module_id=8;
            $log->action='update';      
            $log->description='items ' . Input::get('title') . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('items.index',array('eatery_id' => $request['eatery_id']))->with('success','Item Updated Successfully!');
        
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
         $items = Items::where('item_id','=',$id)->get();
        if (is_null($items))
        {
         return Redirect::back()->with('warning','Item Details Are Not Found!');
        }
        else
        {
           Items::where('item_id','=',$id)->delete();
            $log = new Log();
            $log->module_id=8;
            $log->action='delete';      
            $log->description='Item '. $items->title . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Item Deleted Successfully!');
        }
    }
}
