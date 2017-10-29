<?php

namespace App\Http\Controllers;

use App\MenuSubSection;
use App\MenuSection;
use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Dishes;
use File;
use Image;
use App\Category;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;

ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

class DishesController extends Controller
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
        $dishes = DB::table('dishes')
        ->select(DB::raw('*,dishes.id as id,if(ifnull(dishes.is_visible,1)=1,"Active","Inactive") as is_visible'))
        ->where('dishes.eatery_id','=',$eatery_id)
        ->get();
        $eatery_details = DB::table('eateries')
        ->select(DB::raw('*'))
        ->where('id','=',$eatery_id)
        ->get();
         return View('dishes.index', compact('dishes'))         
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
        $menus = DB::table('menu')
            ->select(DB::raw('id,menu_name'))
            ->where('is_visible','=',1)
            ->get();
        $menu_sections = DB::table('menu_section')
            ->select(DB::raw('id,section_name'))
            ->where('is_visible','=',1)
            ->get();
        $menu_sub_sections = DB::table('menu_sub_section')
            ->select(DB::raw('id,sub_section_name'))
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
        $allergentypes = DB::table('allergen_types')
            ->select(DB::raw('id,allergen_type'))
            ->where('is_enabled','=',1)
            ->get();

        return View('dishes.create')
            ->with('privileges',$privileges)
            ->with('eatery_id',$eatery_id)
            ->with('menu_sub_sections',$menu_sub_sections)
            ->with('menu_sections',$menu_sections)
            ->with('menus',$menus)
            ->with('nutritiontypes',$nutritiontypes)
            ->with('allergentypes',$allergentypes)
            ->with('cuisinetypes',$cuisinetypes);
    }

     private function saveLogoInTempLocation($file)
     {
        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_ITEM_TEMP_PATH');
        $extension = $file->getClientOriginalExtension();
        $filename = $session_id . '.' . $extension;
        $upload_success = $file->move($tempdestinationPath, $filename);
        return $extension;
     }

     private function saveLogoInLogoPath($itemid, $extension)
    {
        $session_id = Session::getId();
        $sourceDir = env('CONTENT_ITEM_TEMP_PATH');
        $destinationDir = env('CONTENT_ITEM_PATH');
        $success = File::copy($sourceDir . '//' . $session_id . '.' .  $extension, $destinationDir . '//' . $itemid . '.' .  $extension);        
        try {
            $success = File::delete($sourceDir . '//' . $session_id . '.' .  $extension);     
        } catch (Exception $e) {
        }
        
        createThumbnailImage($destinationDir,$itemid,$extension);
    }

    private function deleteLogo($itemid, $extension)
    {
        $sourceDir = env('CONTENT_ITEM_PATH');
        try {
            $success = File::delete($sourceDir . '//' . $itemid . '.' .  $extension);        
        } catch (Exception $e) {
        }
        try {
            $success = File::delete($sourceDir . '//' . $itemidaq . '_t.' .  $extension);        
        } catch (Exception $e) {
        }
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
       return $input;
        $file_size = $_FILES['logo']['size'];
        if($file_size > 2097152)
        {
            return Redirect::back()->with('warning','File size must be less than 2 MB!')
                ->withInput();
        }

        $file = array_get($input,'logo');
        $extension = '';
        if($file <> null)
            $extension = $this->saveLogoInTempLocation($file);


        $this->validate($request, [
            'item_name'  => 'required','item_default_price' => 'required','eatery_id' => 'required']);
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            $eatery_id = $request['eatery_id'];
            return Redirect::route('dishes.create',array('eatery_id' => $eatery_id))
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
            $items->item_ingredients = serialize(Input::get('item_ingredients'));
            $items->allergents_contain = serialize(Input::get('allergents_contain'));
            $items->allergents_may_contain = serialize(Input::get('allergents_may_contain'));
            $items->nutrition_levels = serialize(Input::get('nutrition_to'));
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
            if($file <> null)
                $items->logo_extension = $extension;
            $items->save();

           if(!empty($extension))
            {
             $destinationDir = env('CONTENT_ITEM_PATH');            
             $LogoPath=$destinationDir . '/' . $items->id . '.' .  $items->logo_extension;
             $items->img_url =  $LogoPath;
             $items->update();
            }
             if($file <> null)
                $this->saveLogoInLogoPath($items->id, $extension);
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
        $item_applicable_days = unserialize($items->item_applicable_days);
        $cuisine_id = unserialize($items->cuisine_id);
        $item_ingredients = unserialize($items->item_ingredients);
        $allergents_contain = unserialize($items->allergents_contain);
        $allergents_may_contain = unserialize($items->allergents_may_contain);
        $nutrition_levels = unserialize($items->nutrition_levels);

        /*return $allergents_contain;*/

        return View('items.edit')
        ->with('items',$items)
        ->with('eatery_id',$eatery_id)
        ->with('category',$category)
        ->with('cuisinetypes',$cuisinetypes)
        ->with('nutritiontypes',$nutritiontypes)
        ->with('allergenttypes',$allergenttypes)
        ->with('item_applicable_days',$item_applicable_days)
        ->with('cuisine_id',$cuisine_id)
        ->with('item_ingredients',$item_ingredients)
        ->with('allergents_contain',$allergents_contain)
        ->with('allergents_may_contain',$allergents_may_contain)
        ->with('nutrition_levels',$nutrition_levels)
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

        $file_size = $_FILES['logo']['size'];
        if($file_size > 5097152)
        {
            return Redirect::back()->with('warning','File size must be less than 2 MB!');
        }

        $file = array_get($input,'logo');
        $extension = '';
        if($file <> null)
            $extension = $this->saveLogoInTempLocation($file);


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

             if($file <> null)
             {
            $success = File::delete($items->img_url);
            $LogoPath = env('CONTENT_ITEM_PATH') . '/' . $items->id .  '_t.' . $items->logo_extension ;
            $delete=File::delete($LogoPath);
            }             
            
              if(empty($extension))
            {
                $destinationDir = env('CONTENT_ITEM_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $items->logo_extension; 
            }
            else
            {
                $destinationDir = env('CONTENT_ITEM_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $extension;
            }


            $items->eatery_id = Input::get('eatery_id');
            $items->item_name = Input::get('item_name');
            $items->item_default_price = Input::get('item_default_price');
            $items->item_description = Input::get('item_description');
            $items->item_valid_from = date('Y-m-d',strtotime(Input::get('item_valid_from')));
            $items->item_valid_till = date('Y-m-d',strtotime(Input::get('item_valid_till')));
            $items->item_applicable_days = serialize(Input::get('item_applicable_days'));
            $items->cuisine_id = serialize(Input::get('cuisine_id'));
            $items->item_ingredients = serialize(Input::get('item_ingredients'));
            $items->allergents_contain = serialize(Input::get('allergents_contain'));
            $items->allergents_may_contain = serialize(Input::get('allergents_may_contain'));
            $items->nutrition_levels = serialize(Input::get('nutrition_to'));
            $items->meat_content_type = Input::get('meat_content_type');
            $items->category_id = Input::get('category_id');
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

            $items->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $items->modified_by = Session::get('user_id');

            $items->img_url = $LogoPath;
            $items->Update();
         
            if($file <> null)
                $items->logo_extension = $extension;
            $items->update();          

            if($file <> null)
                $this->saveLogoInLogoPath($items->id, $extension);

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
         $items = Items::where('id','=',$id)->get();
        if (is_null($items))
        {
         return Redirect::back()->with('warning','Item Details Are Not Found!');
        }
        else
        {
           Items::where('id','=',$id)->delete();

            try {
                $this->deleteLogo($items->img_url);
            } catch (Exception $e) {
            }

            $log = new Log();
            $log->module_id=8;
            $log->action='delete';      
            $log->description='Item '. $items->item_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Item Deleted Successfully!');
        }
    }
}
