<?php

namespace App\Http\Controllers;

use App\ItemCategories;
use App\ItemGroups;
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
        ->select(DB::raw('*,items.id as id,if(ifnull(items.is_visible,1)=1,"Active","Inactive") as is_visible'))
        ->where('items.eatery_id','=',$eatery_id)
        ->get();
        $eatery_details = DB::table('eateries')
        ->select(DB::raw('*'))
        ->where('id','=',$eatery_id)
        ->get();
         return View('items.index', compact('items'))         
        ->with('privileges',$privileges)
        ->with('eatery_details',$eatery_details)
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
        $eatery_id = $request['eatery_id'];
        $privileges = $this->getPrivileges();
        $itemgroups = DB::table('item_groups')
            ->select(DB::raw('id,group_name'))
            ->where('is_visible','=',1)
            ->get();
        $itemcategories = DB::table('item_categories')
            ->select(DB::raw('id,category_name'))
            ->where('is_visible','=',1)
            ->get();
        $cuisinetypes = DB::table('cuisines')
            ->select(DB::raw('id,cuisine_name'))
            ->where('is_enabled','=',1)
            ->get();
        $nutritiontypes = DB::table('nutrition_types')
            ->select(DB::raw('id,nutrition_type'))
            ->where('is_enabled','=',1)
            ->get();
        $allergenttypes = DB::table('allergent_types')
            ->select(DB::raw('id,allergent_type'))
            ->where('is_enabled','=',1)
            ->get();

        return View('items.create')
            ->with('privileges',$privileges)
            ->with('eatery_id',$eatery_id)
            ->with('itemcategories',$itemcategories)
            ->with('itemgroups',$itemgroups)
            ->with('nutritiontypes',$nutritiontypes)
            ->with('allergenttypes',$allergenttypes)
            ->with('cuisinetypes',$cuisinetypes);
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
            'item_name'  => 'required']);
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        /*echo '<pre>';
        print_r($input);
        echo '</pre>';*/
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


            if(isset($input['itemGroupName']) && !empty($input['itemGroupName'])){
                $itemgroups = new ItemGroups();
                $itemgroups->group_name = Input::get('itemGroupName');
                $itemgroups->is_visible = 1;
                $itemgroups->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                $itemgroups->added_by = Session::get('user_id');
                $itemgroups->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                $itemgroups->modified_by = Session::get('user_id');
                $itemgroups->save();
                $itemgroup_id = $itemgroups->id;
            }
            else if(isset($input['itemgroup']) && !empty($input['itemgroup'])) {
                $itemgroup_id = $input['itemgroup'];
            }
            if(isset($input['itemCategoryName']) && !empty($input['itemCategoryName'])){
                $itemcategories = new ItemCategories();
                $itemcategories->category_name = Input::get('itemCategoryName');
                $itemcategories->group_id = $itemgroup_id;
                $itemcategories->is_visible = 1;
                $itemcategories->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                $itemcategories->added_by = Session::get('user_id');
                $itemcategories->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                $itemcategories->modified_by = Session::get('user_id');
                $itemcategories->save();
                $itemcategory_id = $itemcategories->id;
            }
            else if(isset($input['itemcategory']) && !empty($input['itemcategory'])) {
                $itemcategory_id = $input['itemcategory'];
            }
            $items = new Items();
            $items->eatery_id = Input::get('eatery_id');
            $items->item_name = Input::get('item_name');
            $items->item_default_price = Input::get('item_default_price');
            $items->item_description = Input::get('item_description');
            $items->item_valid_from = date('Y-m-d',strtotime(Input::get('item_valid_from')));
            $items->item_valid_till = date('Y-m-d',strtotime(Input::get('item_valid_till')));
            $items->item_applicable_days = serialize(Input::get('item_applicable_days'));
            $items->cuisine_id = serialize(Input::get('cuisine_id'));
            $items->item_ingredients = serialize(Input::get('ingrediant_names'));
            $items->allergents_contain = serialize(Input::get('allergents_contain'));
            $items->allergents_may_contain = serialize(Input::get('allergents_may_contain'));
            $items->meat_content_type = Input::get('meat_content_type');
            $items->category_id = $itemcategory_id;
            if(isset($input['contains_nuts']) && !empty($input['contains_nuts'])){
                $items->contains_nuts = 1;
            }
            else{
                $items->contains_nuts = 0;
            }

            if(isset($input['dairy_free']) && !empty($input['dairy_free'])){
                $items->dairy_free = 1;
            }
            else{
                $items->dairy_free = 0;
            }
            if(isset($input['gluten_free']) && !empty($input['gluten_free'])){
                $items->gluten_free = 1;
            }
            else{
                $items->gluten_free = 0;
            }
            if(isset($input['vegan']) && !empty($input['vegan'])){
                $items->vegan = 1;
            }
            else{
                $items->vegan = 0;
            }
            if(isset($input['is_visible']) && !empty($input['is_visible'])){
                $items->is_visible = 1;
            }
            else{
                $items->is_visible = 0;
            }
            $items->display_order =  Input::get('display_order');
            $items->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $items->added_by = Session::get('user_id');
            $items->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $items->modified_by = Session::get('user_id');
            $items->save();            

            $log = new Log();
            $log->module_id=8;
            $log->action='create';      
            $log->description='items ' . $items->item_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('items.index',array('eatery_id' => $items->eatery_id))->with('success','Item Created Successfully!');
        
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
        $category = DB::table('item_categories')
            ->select(DB::raw('id,category_name'))
            ->where('is_visible','=',1)
            ->get();
        $cuisinetypes = DB::table('cuisines')
            ->select(DB::raw('id,cuisine_name'))
            ->where('is_enabled','=',1)
            ->get();
        $nutritiontypes = DB::table('nutrition_types')
            ->select(DB::raw('id,nutrition_type'))
            ->where('is_enabled','=',1)
            ->get();
        $allergenttypes = DB::table('allergent_types')
            ->select(DB::raw('id,allergent_type'))
            ->where('is_enabled','=',1)
            ->get();
        $items = Items::find($id);
        return View('items.edit')
        ->with('items',$items)
        ->with('eatery_id',$eatery_id)
        ->with('category',$category)
        ->with('cuisinetypes',$cuisinetypes)
        ->with('nutritiontypes',$nutritiontypes)
        ->with('allergenttypes',$allergenttypes)
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
            'item_name'  => 'required']);
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
            $items = Items::find($id);
            $items->eatery_id = Input::get('eatery_id');
            $items->item_name = Input::get('item_name');
            $items->item_default_price = Input::get('item_default_price');
            $items->item_description = Input::get('item_description');
            $items->item_valid_from = date('Y-m-d',strtotime(Input::get('item_valid_from')));
            $items->item_valid_till = date('Y-m-d',strtotime(Input::get('item_valid_till')));
            $items->item_applicable_days = serialize(Input::get('item_applicable_days'));
            $items->cuisine_id = serialize(Input::get('cuisine_id'));
            $items->item_ingredients = serialize(Input::get('ingrediant_names'));
            $items->allergents_contain = serialize(Input::get('allergents_contain'));
            $items->allergents_may_contain = serialize(Input::get('allergents_may_contain'));
            $items->meat_content_type = Input::get('meat_content_type');
            $items->Update();

            $log = new Log();
            $log->module_id=8;
            $log->action='update';      
            $log->description='items ' . $items->item_name . ' is updated';
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
