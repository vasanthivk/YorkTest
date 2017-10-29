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
        $menu = DB::table('menu')
            ->select(DB::raw('ref,menu'))
            ->get();
        $menusection = DB::table('menu_section')
            ->select(DB::raw('id,section_name'))
            ->where('is_visible','=',1)
            ->get();
        $menusubsection = DB::table('menu_sub_section')
            ->select(DB::raw('id,sub_section_name'))
            ->where('is_visible','=',1)
            ->get();
        $cuisinetypes = DB::table('cuisines')
            ->select(DB::raw('id,cuisine_name'))
            ->where('is_enabled','=',1)
            ->get();
        $allergenttypes = DB::table('allergens')
            ->select(DB::raw('ref,title'))
            ->where('display','=',"yes")
            ->where('type','=',"I")
            ->get();
        $ingredients = DB::table('_product_ingredients')
            ->select(DB::raw('ref,name'))
            ->where('deleted','!=',"yes")
            ->get();
        $lifestyle_choices = DB::table('lifestyle_choices')
            ->select(DB::raw('id,description'))
            ->where('is_enabled','=','1')
            ->get();

        return View('dishes.create')
            ->with('privileges',$privileges)
            ->with('eatery_id',$eatery_id)
            ->with('menusection',$menusection)
            ->with('menusubsection',$menusubsection)
            ->with('menu',$menu)
            ->with('allergenttypes',$allergenttypes)
            ->with('ingredients',$ingredients)
            ->with('lifestyle_choices',$lifestyle_choices)
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
            $success = File::delete($sourceDir . '//' . $itemid . '_t.' .  $extension);
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
            'dish_name'  => 'required','default_price' => 'required','eatery_id' => 'required']);
        
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
            $dish = new Dishes();
            $dish->dish_name = Input::get('dish_name');
            $dish->description = Input::get('description');
            $dish->cuisines_ids = serialize(Input::get('cuisines_ids'));
            $dish->lifestyle_choices_ids = Input::get('lifestyle_choices_ids');
            $dish->allergens_contain_ids = serialize(Input::get('allergens_contain_ids'));
            $dish->ingredients_ids = serialize(Input::get('ingredients_ids'));
            $dish->menus_ids = Input::get('menus_ids');
            $dish->sections_ids = Input::get('sections_ids');
            $dish->subsections_ids = Input::get('subsections_ids');
            $dish->group_id = Input::get('group_id');
            $dish->eatery_id = Input::get('eatery_id');
            $dish->valid_from = date('Y-m-d',strtotime(Input::get('valid_from')));
            $dish->valid_till = date('Y-m-d',strtotime(Input::get('valid_till')));
            $dish->applicable_days = serialize(Input::get('applicable_days'));
            $dish->default_price = Input::get('default_price');
            if(isset($input['is_visible']) && !empty($input['is_visible'])){
                $dish->is_visible = 1;
            }
            else{
                $dish->is_visible = 0;
            }
            if(isset($input['is_featured']) && !empty($input['is_featured'])){
                $dish->is_featured = 1;
            }
            else{
                $dish->is_featured = 0;
            }
            $dish->allergens_may_contain = serialize(Input::get('allergens_may_contain'));
            if(isset($input['is_new']) && !empty($input['is_new'])){
                $dish->is_new = 1;
            }
            else{
                $dish->is_new = 0;
            }
            $dish->new_till_date = date('Y-m-d',strtotime(Input::get('new_till_date')));
            $dish->display_order =  Input::get('display_order');
            $dish->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $dish->added_by = Session::get('user_id');
            $dish->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $dish->modified_by = Session::get('user_id');
            if($file <> null)
                $dish->logo_extension = $extension;
            $dish->save();

           if(!empty($extension))
            {
             $destinationDir = env('CONTENT_ITEM_PATH');            
             $LogoPath=$destinationDir . '/' . $dish->id . '.' .  $dish->logo_extension;
                $dish->img_url =  $LogoPath;
                $dish->update();
            }
             if($file <> null)
                $this->saveLogoInLogoPath($dish->id, $extension);
            $log = new Log();
            $log->module_id=8;
            $log->action='create';      
            $log->description='Dish ' . $dish->dish_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('dishes.index',array('eatery_id' => $dish->eatery_id))->with('success','Dish Created Successfully!');
        
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
        $menu = DB::table('menu')
            ->select(DB::raw('ref,menu'))
            ->get();
        $menusection = DB::table('menu_section')
            ->select(DB::raw('id,section_name'))
            ->where('is_visible','=',1)
            ->get();
        $menusubsection = DB::table('menu_sub_section')
            ->select(DB::raw('id,sub_section_name'))
            ->where('is_visible','=',1)
            ->get();
        $cuisinetypes = DB::table('cuisines')
            ->select(DB::raw('id,cuisine_name'))
            ->where('is_enabled','=',1)
            ->get();
        $allergenttypes = DB::table('allergens')
            ->select(DB::raw('ref,title'))
            ->where('display','=',"yes")
            ->where('type','=',"I")
            ->get();
        $ingredients = DB::table('_product_ingredients')
            ->select(DB::raw('ref,name'))
            ->where('deleted','!=',"yes")
            ->get();
        $lifestyle_choices = DB::table('lifestyle_choices')
            ->select(DB::raw('id,description'))
            ->where('is_enabled','=','1')
            ->get();

        $dish = Dishes::find($id);
        $applicable_days = unserialize($dish->applicable_days);
        $cuisines_ids = unserialize($dish->cuisines_ids);
        $lifestyle_choices_ids = unserialize($dish->lifestyle_choices_ids);
        $ingredients_ids = unserialize($dish->ingredients_ids);
        $allergens_contain_ids = unserialize($dish->allergens_contain_ids);
        $menus_ids = unserialize($dish->menus_ids);
        $sections_ids = unserialize($dish->sections_ids);
        $subsections_ids = unserialize($dish->subsections_ids);
        $allergens_may_contain = unserialize($dish->allergens_may_contain);

        /*return $allergents_contain;*/

        return View('dishes.edit')
        ->with('menu',$menu)
        ->with('menusection',$menusection)
        ->with('menusubsection',$menusubsection)
        ->with('cuisinetypes',$cuisinetypes)
        ->with('allergenttypes',$allergenttypes)
        ->with('ingredients',$ingredients)
        ->with('lifestyle_choices',$lifestyle_choices)
        ->with('dish',$dish)
        ->with('eatery_id',$eatery_id)
        ->with('cuisines_ids',$cuisines_ids)
        ->with('lifestyle_choices_ids',$lifestyle_choices_ids)
        ->with('ingredients_ids',$ingredients_ids)
        ->with('allergens_contain_ids',$allergens_contain_ids)
        ->with('applicable_days',$applicable_days)
        ->with('menus_ids',$menus_ids)
        ->with('sections_ids',$sections_ids)
        ->with('subsections_ids',$subsections_ids)
        ->with('allergens_may_contain',$allergens_may_contain)
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
            $dish = Dishes::find($id);

             if($file <> null)
             {
            $success = File::delete($dish->img_url);
            $LogoPath = env('CONTENT_ITEM_PATH') . '/' . $dish->id .  '_t.' . $dish->logo_extension ;
            $delete=File::delete($LogoPath);
            }             
            
              if(empty($extension))
            {
                $destinationDir = env('CONTENT_ITEM_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $dish->logo_extension;
            }
            else
            {
                $destinationDir = env('CONTENT_ITEM_PATH');
                $LogoPath=$destinationDir . '/' . $id . '.' .  $extension;
            }

            $dish->dish_name = Input::get('dish_name');
            $dish->description = Input::get('description');
            $dish->cuisines_ids = serialize(Input::get('cuisines_ids'));
            $dish->lifestyle_choices_ids = Input::get('lifestyle_choices_ids');
            $dish->allergens_contain_ids = serialize(Input::get('allergens_contain_ids'));
            $dish->ingredients_ids = serialize(Input::get('ingredients_ids'));
            $dish->menus_ids = Input::get('menus_ids');
            $dish->sections_ids = Input::get('sections_ids');
            $dish->subsections_ids = Input::get('subsections_ids');
            $dish->group_id = Input::get('group_id');
            $dish->eatery_id = Input::get('eatery_id');
            $dish->valid_from = date('Y-m-d',strtotime(Input::get('valid_from')));
            $dish->valid_till = date('Y-m-d',strtotime(Input::get('valid_till')));
            $dish->applicable_days = serialize(Input::get('applicable_days'));
            $dish->default_price = Input::get('default_price');
            if(isset($input['is_visible']) && !empty($input['is_visible'])){
                $dish->is_visible = 1;
            }
            else{
                $dish->is_visible = 0;
            }
            if(isset($input['is_featured']) && !empty($input['is_featured'])){
                $dish->is_featured = 1;
            }
            else{
                $dish->is_featured = 0;
            }
            $dish->allergens_may_contain = serialize(Input::get('allergens_may_contain'));
            if(isset($input['is_new']) && !empty($input['is_new'])){
                $dish->is_new = 1;
            }
            else{
                $dish->is_new = 0;
            }
            $dish->new_till_date = date('Y-m-d',strtotime(Input::get('new_till_date')));
            $dish->display_order =  Input::get('display_order');
            $dish->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $dish->modified_by = Session::get('user_id');

            $dish->img_url = $LogoPath;
            $dish->Update();
         
            if($file <> null)
                $dish->logo_extension = $extension;
            $dish->update();

            if($file <> null)
                $this->saveLogoInLogoPath($dish->id, $extension);

            $log = new Log();
            $log->module_id=8;
            $log->action='update';      
            $log->description='items ' . $dish->dish_name . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('dishes.index',array('eatery_id' => $request['eatery_id']))->with('success','Dish Updated Successfully!');
        
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
         $dish = Dishes::where('id','=',$id)->get();
        if (is_null($dish))
        {
         return Redirect::back()->with('warning','Item Details Are Not Found!');
        }
        else
        {
           Dishes::where('id','=',$id)->delete();

            try {
                $this->deleteLogo($dish->img_url);
            } catch (Exception $e) {
            }

            $log = new Log();
            $log->module_id=8;
            $log->action='delete';      
            $log->description='Item '. $dish->dish_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Dish Deleted Successfully!');
        }
    }
}
