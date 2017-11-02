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
use App\Groups;
use App\Eateries;
use File;
use Image;
use App\Category;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use App\ProductIngredients;

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
        $privileges['View']  = ValidateUserPrivileges($roleid,9,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,9,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,9,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,9,4);
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
        ->select(DB::raw('*,dishes.id as id,if(ifnull(dishes.is_visible,1)=1,"Visible","InVisible") as is_visible'))
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

        $menuAll = Menu::where('is_visible','=','1')->where('company','=','FoodAdvisr')->get();
        $menus = Menu::all()->where('is_visible','=','1')->where('company','=','FoodAdvisr')->pluck('menu','ref');
        $menus_count = $menus->count();
        if($menus_count == 0)
        {
            return Redirect::back()->with('warning','Please Add Menu Details While Adding Dish!');
        }
        $menusection=null;
        if($menus->count()>0)
        {
            $menusection = MenuSection::where('menu_id','=',$menuAll[0]->ref)->where('is_visible','=','1')->pluck('section_name','id');
        }
        $menusection_count = $menusection->count();
        if($menusection_count == 0)
        {
            return Redirect::back()->with('warning','Please Add Menu Section Details While Adding Dish!');
        }
        $menusubsection=null;
        if($menusection->count()>0)
        {
            $menusectionAll = MenuSection::where('menu_id','=',$menuAll[0]->ref)->where('is_visible','=','1')->get();
            $menusubsection = MenuSubSection::where('section_id','=',$menusectionAll[0]->id)->where('is_visible','=','1')->pluck('sub_section_name','id');
        }
        $menusubsection_count = $menusubsection->count();
        if($menusection_count == 0)
        {
            return Redirect::back()->with('warning','Please Add Menu Sub Section Details While Adding Dish!');
        }
        $allergentypes = DB::table('allergens')
            ->where('type','=',"I")
            ->select(DB::raw('ref,title'))
            ->get();        

        $cuisinetypes = 'select id,cuisine_name from cuisines where ifnull(is_enabled,0) = 1';
        $cuisines = DB::select( DB::raw($cuisinetypes));
        $lifestyle_choices_types = 'select id,description from lifestyle_choices where ifnull(is_enabled,0) = 1';
        $lifestyle_choices = DB::select( DB::raw($lifestyle_choices_types));
        $groups = Groups::all();
        if($request['search'])
            $searchvalue = $request['search'];
        else
            $searchvalue='';
        $search = '%' . $searchvalue . '%';
        
        $eateries = searchalleateries($searchvalue);
//return $ingredients;
        return View('dishes.create')
            ->with('privileges',$privileges)
            ->with('eatery_id',$eatery_id)
            ->with('menusection',$menusection)
            ->with('menusubsection',$menusubsection)
            ->with('menus',$menus)
            ->with('groups',$groups)
            ->with('eateries',$eateries)
            ->with('searchvalue',$searchvalue)
            ->with('allergentypes',$allergentypes)
            ->with('lifestyle_choices',$lifestyle_choices)
            ->with('cuisines',$cuisines);
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

        Session::put('cuisines',Input::get('cuisines_ids'));
        Session::put('cuisines',Input::get('cuisines_ids'));
        Session::put('lifestyle_choices',Input::get('lifestyle_choices_ids'));
        Session::put('menus_ids',Input::get('menus_ids'));
        Session::put('sections_ids',Input::get('sections_ids'));
        Session::put('subsections_ids',Input::get('subsections_ids'));
        Session::put('allergens_contain_ids',Input::get('allergens_contain_ids'));
        Session::put('applicable_days',Input::get('applicable_days'));
        Session::put('allergens_may_contain',Input::get('allergens_may_contain'));

        if ($input['eatery_id'] == 0) {
           return Redirect::back()->with('warning','Please Search Eateries And Select Aleast One Eatery!');
        }

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
            'dish_name'  => 'required','default_price' => 'required','cuisines_ids'=>'required','lifestyle_choices_ids'=>'required','allergens_contain_ids'=>'required','applicable_days'=>'required']);
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            $search = Input::get('search');
            return Redirect::route('dishes.create',array('search' => $search))
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {
            $ingredient_items = Input::get('item_ingredients');
            if(isset($ingredient_items) && !empty($ingredient_items)){
                foreach($ingredient_items as $ingredient){
                    $token = openssl_random_pseudo_bytes(3);
                    $token = bin2hex($token);
                    $ingre = new ProductIngredients();
                    $ingre->ref = $token;
                    $ingre->name = $ingredient;
                    $ingre->product_barcode = 'NULL';
                    $ingre->percentage = 'NULL';
                    $ingre->order_of_amount = '0';
                    $ingre->date_entered = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->date_modified = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->deleted = 'N';
                    $ingre->date_deleted = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->date_modified = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->save();
                    $ingredients_ids[] = $ingre->id;
                }
            }
            else{
                $ingredients_ids[] = array();
            }

            $allergens_may_contain_detail = Input::get('allergens_may_contain');

            if(isset($allergens_may_contain_detail) && !empty($allergens_may_contain_detail)) {
                $allergens_may_contain = implode(',',$allergens_may_contain_detail);
            }
            else{
                $allergens_may_contain[] = array();;
            }
            
            $dish = new Dishes();
            $dish->dish_name = Input::get('dish_name');
            $dish->description = Input::get('description');
            $dish->cuisines_ids = implode(',',Input::get('cuisines_ids'));
            $dish->lifestyle_choices_ids = implode(',',Input::get('lifestyle_choices_ids'));
            $dish->allergens_contain_ids = implode(',',Input::get('allergens_contain_ids'));
            $dish->allergens_may_contain = $allergens_may_contain;
            $dish->ingredients_ids = implode(',',$ingredients_ids);
            $dish->menus_ids = Input::get('menus_ids');
            $dish->sections_ids = Input::get('sections_ids');
            $dish->subsections_ids = Input::get('subsections_ids');
            $dish->group_id = (Input::get('group_id')== ''  ? '0' : Input::get('group_id'));
            $dish->eatery_id = Input::get('eatery_id');
            $dish->valid_from = date('Y-m-d',strtotime(Input::get('valid_from')));
            $dish->valid_till = date('Y-m-d',strtotime(Input::get('valid_till')));
            $dish->applicable_days = implode(',',Input::get('applicable_days'));
            $dish->default_price = Input::get('default_price');
            $dish->is_visible = (Input::get('is_visible')== ''  ? '0' : '1');
            $dish->is_featured = (Input::get('is_featured')== ''  ? '0' : '1');
            $dish->is_new = (Input::get('is_new')== ''  ? '0' : '1');
            $dish->new_till_date = date('Y-m-d',strtotime(Input::get('new_till_date')));
            $dish->nutrition_fat =  Input::get('nutrition_fat');
            $dish->nutrition_cholesterol =  Input::get('nutrition_cholesterol');
            $dish->nutrition_sugar =  Input::get('nutrition_sugar');
            $dish->nutrition_fibre =  Input::get('nutrition_fibre');
            $dish->nutrition_protein =  Input::get('nutrition_protein');
            $dish->nutrition_saturated_fat =  Input::get('nutrition_saturated_fat');
            $dish->nutrition_calories =  Input::get('nutrition_calories');
            $dish->nutrition_carbohydrates =  Input::get('nutrition_carbohydrates');
            $dish->nutrition_salt =  Input::get('nutrition_salt');
            $dish->display_order =  1;
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
            $log->module_id=9;
            $log->action='create';      
            $log->description='Dish ' . $dish->dish_name . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('dishes.index')->with('success','Dish Created Successfully!');
        
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
        $dish = Dishes::find($id);
        // $applicable_days = $dish->applicable_days;
        $cuisines_ids = $dish->cuisines_ids;
        $lifestyle_choices_ids = $dish->lifestyle_choices_ids;
        $ingredients_ids = $dish->ingredients_ids;
        // return $ingredients_ids;
        if(!empty(($ingredients_ids)))
        {
        $sql = 'select id,name from _product_ingredients where id in('.$ingredients_ids.')';
        $ingredients = DB::select( DB::raw($sql));
        }
        else{
            $ingredients = array();
        }
        
        $applicable_days = array('0'=>'Sunday','1'=>'Monday','2' => 'Tuesday','3'=>'Wednesday','4' => 'Thursday','5'=>'Friday','6'=> 'Saturday');
        $allergens_contain_ids = $dish->allergens_contain_ids;
        $menus_ids = $dish->menus_ids;
        $sections_ids = $dish->sections_ids;
        $subsections_ids = $dish->subsections_ids;
        $allergens_may_contain = $dish->allergens_may_contain;

        $menuAll = Menu::where('is_visible','=','1')->where('company','=','FoodAdvisr')->get();
        $menus = Menu::all()->where('is_visible','=','1')->where('company','=','FoodAdvisr')->pluck('menu','ref');
        $menusection=null;
        if($menus->count()>0)
            $menusection = MenuSection::where('menu_id','=',$menuAll[0]->ref)->where('is_visible','=','1')->pluck('section_name','id');

        $menusubsection=null;
        if($menusection->count()>0)
        {
            $menusectionAll = MenuSection::where('menu_id','=',$menuAll[0]->ref)->where('is_visible','=','1')->get();
            $menusubsection = MenuSubSection::where('section_id','=',$menusectionAll[0]->id)->where('is_visible','=','1')->pluck('sub_section_name','id');
        }
        
        $allergentypes = DB::table('allergens')
            ->where('type','=',"I")
            ->select(DB::raw('ref,title'))
            ->get();        

        $cuisinetypes = 'select id,cuisine_name from cuisines where ifnull(is_enabled,0) = 1';
        $cuisines = DB::select( DB::raw($cuisinetypes));
        $lifestyle_choices_types = 'select id,description from lifestyle_choices where ifnull(is_enabled,0) = 1';
        $lifestyle_choices = DB::select( DB::raw($lifestyle_choices_types));
        $groups = Groups::all();
        $eateries = Eateries::find($dish->eatery_id);

        return View('dishes.edit')
            ->with('eatery_id',$eatery_id)
            ->with('menusection',$menusection)
            ->with('menusubsection',$menusubsection)
            ->with('menus',$menus)
            ->with('eateries',$eateries)
            ->with('allergentypes',$allergentypes)
            ->with('lifestyle_choices',$lifestyle_choices)
            ->with('cuisines',$cuisines)
            ->with('dish',$dish)
            ->with('ingredients',$ingredients)
            ->with('groups',$groups)
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
        Session::put('cuisines',Input::get('cuisines_ids'));
        Session::put('lifestyle_choices',Input::get('lifestyle_choices_ids'));
        Session::put('menus_ids',Input::get('menus_ids'));
        Session::put('sections_ids',Input::get('sections_ids'));
        Session::put('subsections_ids',Input::get('subsections_ids'));
        Session::put('allergens_contain_ids',Input::get('allergens_contain_ids'));
        Session::put('applicable_days',Input::get('applicable_days'));
        Session::put('allergens_may_contain',Input::get('allergens_may_contain'));

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
              'dish_name'  => 'required','default_price' => 'required','cuisines_ids'=>'required','lifestyle_choices_ids'=>'required','allergens_contain_ids'=>'required','applicable_days'=>'required']);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) 
        {
            $eatery_id = $request['eatery_id'];
            return Redirect::route('dishes.edit',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            $dish = Dishes::find($id);

            $ingredient_items = Input::get('item_ingredients');
            if(isset($ingredient_items) && !empty($ingredient_items)){
                foreach($ingredient_items as $ingredient){
                    $token = openssl_random_pseudo_bytes(3);
                    $token = bin2hex($token);
                    $ingre = new ProductIngredients();
                    $ingre->ref = $token;
                    $ingre->name = $ingredient;
                    $ingre->product_barcode = 'NULL';
                    $ingre->percentage = 'NULL';
                    $ingre->order_of_amount = '0';
                    $ingre->date_entered = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->date_modified = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->deleted = 'N';
                    $ingre->date_deleted = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->date_modified = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $ingre->save();
                    $ingredients_ids[] = $ingre->id;
                }
            }
            else{
                $ingredients_ids[] = array();
            }

            $allergens_may_contain_detail = Input::get('allergens_may_contain');

            if(isset($allergens_may_contain_detail) && !empty($allergens_may_contain_detail)) {
                $allergens_may_contain = implode(',',$allergens_may_contain_detail);
            }
            else{
                $allergens_may_contain[] = array();;
            }
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
            $dish->cuisines_ids = implode(',',Input::get('cuisines_ids'));
            $dish->lifestyle_choices_ids = implode(',',Input::get('lifestyle_choices_ids'));
            $dish->allergens_contain_ids = implode(',',Input::get('allergens_contain_ids'));
            $dish->allergens_may_contain = $allergens_may_contain;
            $dish->ingredients_ids = implode(',',$ingredients_ids);
            $dish->menus_ids = Input::get('menus_ids');
            $dish->sections_ids = Input::get('sections_ids');
            $dish->subsections_ids = Input::get('subsections_ids');
            $dish->group_id = (Input::get('group_id')== ''  ? '0' : Input::get('group_id'));
            $dish->valid_from = date('Y-m-d',strtotime(Input::get('valid_from')));
            $dish->valid_till = date('Y-m-d',strtotime(Input::get('valid_till')));
            $dish->applicable_days = implode(',',Input::get('applicable_days'));
            $dish->default_price = Input::get('default_price');
            $dish->is_visible = (Input::get('is_visible')== ''  ? '0' : '1');
            $dish->is_featured = (Input::get('is_featured')== ''  ? '0' : '1');
            $dish->is_new = (Input::get('is_new')== ''  ? '0' : '1');
            $dish->new_till_date = date('Y-m-d',strtotime(Input::get('new_till_date')));
            $dish->nutrition_fat =  Input::get('nutrition_fat');
            $dish->nutrition_cholesterol =  Input::get('nutrition_cholesterol');
            $dish->nutrition_sugar =  Input::get('nutrition_sugar');
            $dish->nutrition_fibre =  Input::get('nutrition_fibre');
            $dish->nutrition_protein =  Input::get('nutrition_protein');
            $dish->nutrition_saturated_fat =  Input::get('nutrition_saturated_fat');
            $dish->nutrition_calories =  Input::get('nutrition_calories');
            $dish->nutrition_carbohydrates =  Input::get('nutrition_carbohydrates');
            $dish->nutrition_salt =  Input::get('nutrition_salt');
            $dish->display_order =  1;
            $dish->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $dish->added_by = Session::get('user_id');
            $dish->modified_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $dish->modified_by = Session::get('user_id');

            
             if($file <> null)
                $dish->logo_extension = $extension;
            $dish->update();                              

            if($file <> null)
                $this->saveLogoInLogoPath($dish->id, $extension);

            $log = new Log();
            $log->module_id=9;
            $log->action='update';      
            $log->description='Dish ' . $dish->dish_name . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('dishes.index')->with('success','Dish Updated Successfully!');
        
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
        $dish = Dishes::find($id);
        if (is_null($dish))
        {
         return Redirect::back()->with('warning','Dish Details Are Not Found!');
        }
        else
        {
           Dishes::find($id)->delete();

             try {
                $this->deleteLogo($dish->id, $dish->logo_extension);
            } catch (Exception $e) {
            }

            $log = new Log();
            $log->module_id=9;
            $log->action='delete';      
            $log->description='Dish '. $dish->dish_name . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Dish Deleted Successfully!');
        }
    }

     public function ajaxSectionByMenuId($menuids){
        $query = DB::table('menu_section')
            ->whereIn('menu_id',array($menuids))
            ->select(DB::raw('id,section_name'))
            ->get();

        return $query;
    }

    public function ajaxSubSectionBySectionId($sections_ids){
        $query = DB::table('menu_sub_section')
            ->whereIn('section_id',array($sections_ids))
            ->select(DB::raw('id,sub_section_name'))
            ->get();

        return $query;
    }

    public function ajaxIngredientSearch($search){
        $query = DB::table('_product_ingredients')
            ->where('name','like', '%' . $search . '%')
            ->select(DB::raw('ref,name'))
            ->get();

        return $query;
    }
}
