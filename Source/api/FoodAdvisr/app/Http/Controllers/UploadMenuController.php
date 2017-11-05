<?php

namespace App\Http\Controllers;

use App\Dishes;
use App\MenuSection;
use App\MenuSubSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Menu;
use App\LifestyleChoices;
use Redirect;
use Session;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use DB;
use Excel;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

class UploadMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request['search'])
            $searchvalue = $request['search'];
        else
            $searchvalue='';
        $search = '%' . $searchvalue . '%';

        $eateries = searchalleateries($searchvalue);
         return view('uploadmenu.index')
             ->with('eateries',$eateries)
             ->with('searchvalue',$searchvalue);
    }

    public function generateExcel(Request $request) 
    {
      $data = Menu::get()->toArray();
       Excel::create('Menu_'.Carbon::now(new DateTimeZone('Europe/London')),function($excel) use($data)
                {
                    $excel->sheet('Sheet 1',function($sheet) use($data){
                        $sheet->getCell('A1')->setValueExplicit('SlNo');
                        $sheet->getCell('B1')->setValueExplicit('dish_name');
                        $sheet->getCell('C1')->setValueExplicit('description');
                        $sheet->getCell('D1')->setValueExplicit('cuisines');
                        $sheet->getCell('E1')->setValueExplicit('lifestyle_choices');
                        $sheet->getCell('F1')->setValueExplicit('allergens_contain');
                        $sheet->getCell('G1')->setValueExplicit('ingredients_string');
                        $sheet->getCell('H1')->setValueExplicit('nutrition_fat');
                        $sheet->getCell('I1')->setValueExplicit('nutrition_cholesterol');
                        $sheet->getCell('J1')->setValueExplicit('nutrition_sugar');
                        $sheet->getCell('K1')->setValueExplicit('nutrition_fibre');
                        $sheet->getCell('L1')->setValueExplicit('nutrition_protein');
                        $sheet->getCell('M1')->setValueExplicit('nutrition_saturated_fat');
                        $sheet->getCell('N1')->setValueExplicit('nutrition_calories');
                        $sheet->getCell('O1')->setValueExplicit('nutrition_carbohydrates');
                        $sheet->getCell('P1')->setValueExplicit('nutrition_salt');
                        $sheet->getCell('Q1')->setValueExplicit('menus');
                        $sheet->getCell('R1')->setValueExplicit('sections');
                        $sheet->getCell('S1')->setValueExplicit('subsections');
                        $sheet->getCell('T1')->setValueExplicit('default_price');
                        $sheet->getCell('U1')->setValueExplicit('allergens_may_contain');
                    });
                })->export('xls');   
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
        $eatery_id  = $request['eatery_id'];
         $excelvalues = $request['import_file'];
         $file = explode(".", $_FILES['import_file']['name']);
         $allowed_extensions = array('csv','xls','xlsx');
         $extension = array_pop($file);
        if($eatery_id == 0){
            return back()->with('warning','Please Select the Eatery First!');
        }
         if (!in_array($extension, $allowed_extensions)) {
            return back()->with('warning','Only Allowed Excel File.');
         }
        if(empty($excelvalues))
        {
            return back()->with('warning','Please Upload Excel file.');
        }        
        if($request->hasFile('import_file'))
        {
            $path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {})->get();
            // return $data;
            if( $data->count()<1)
                return back()->with('warning','No records to import.');
            if(!empty($data) && $data[0]->count())
            {
                foreach ($data->toArray() as $key => $value)
                {
                    $dish = new Dishes();
                    $dish->dish_name = $value['dish_name'];
                    $dish->description = $value['description'];
                    $dish->cuisines_ids = $this->getCuisineIds($value['cuisines']);
                    
                    //$dish->lifestyle_choices_ids = $value['lifestyle_choices'];
                    $dish->lifestyle_choices_ids = $this->getLifestyleChoicesIds($value['lifestyle_choices']);

                    //$dish->allergens_contain_ids = $value['allergens_contain'];
                    $dish->allergens_contain_ids = $this->getAllergensContain($value['allergens_contain']);
                    $dish->ingredients_string = $value['ingredients_string'];
                    $dish->nutrition_fat = $value['nutrition_fat'];
                    $dish->nutrition_cholesterol = $value['nutrition_cholesterol'];
                    $dish->nutrition_sugar = $value['nutrition_sugar'];
                    $dish->nutrition_fibre = $value['nutrition_fibre'];
                    $dish->nutrition_protein = $value['nutrition_protein'];
                    $dish->nutrition_saturated_fat = $value['nutrition_saturated_fat'];
                    $dish->nutrition_calories = $value['nutrition_calories'];
                    $dish->nutrition_carbohydrates = $value['nutrition_carbohydrates'];
                    $dish->nutrition_salt = $value['nutrition_salt'];
                    //$menuid = $this->getMenus($value['menus'],$eatery_id);
                    $dish->menus_ids = $this->getMenus($value['menus'],$eatery_id);
                    //$menusection = $this->getMenuSection($value['sections'],$this->getMenus($value['menus'],$eatery_id),$eatery_id);
                    $dish->sections_ids = $this->getMenuSection($value['sections'],$this->getMenus($value['menus'],$eatery_id),$eatery_id);
                    //$menusubsection = $this->getMenuSubSection($value['subsections'],$this->getMenuSection($value['sections'],$this->getMenus($value['menus'],$eatery_id),$eatery_id),$eatery_id);
                    $dish->subsections_ids = $this->getMenuSubSection($value['subsections'],$this->getMenuSection($value['sections'],$this->getMenus($value['menus'],$eatery_id),$eatery_id),$eatery_id);
                    $dish->eatery_id = $eatery_id;
                    $dish->group_id = 0;
                    $dish->default_price = $value['default_price'];
                    $allergens_may_contain_detail = $value['allergens_may_contain'];
                    if(isset($allergens_may_contain_detail) && !empty($allergens_may_contain_detail)) 
                    {
                    $allergens_may_contain = implode(',',$allergens_may_contain_detail);
                    }
                    else
                    {
                    $allergens_may_contain = "0";
                    }
                    $dish->allergens_may_contain = $allergens_may_contain;
                    $dish->added_on = Carbon::now(new DateTimeZone('Europe/London'));
                    $dish->added_by = Session::get('user_id');
                    $dish->modified_by = Session::get('user_id');
                    $dish->modified_on = Carbon::now(new DateTimeZone('Europe/London'));
                    $dish->Save();

                }
            }
            return back()->with('success','Inserted Dishes Successfully.');
        }
    }
    private function getLifestyleChoicesIds($lifestyle_choices)
    {
        $myString = $lifestyle_choices;
        $LifeStyleChoicesIds = [];
        if( strpos($lifestyle_choices, ',') !== false )
        {
            $lifestyle_choices_list = explode(',', $myString);
            foreach($lifestyle_choices_list as $lifestyle_choices_value)
            {
                $LifeStyle = DB::table('lifestyle_choices')->where('description', $lifestyle_choices_value)->first();
                if($LifeStyle != null)
                {
                    $LifeStyleChoicesIds[] = $LifeStyle->id;
                }
            }
            $lifestyle_choices_ids = implode(",",$LifeStyleChoicesIds);
        }else{
            $lifestyle_choice_query = DB::table('lifestyle_choices')->where('description', $lifestyle_choices)->first();
            $lifestyle_choices_ids = $lifestyle_choice_query->id;
        }
        return $lifestyle_choices_ids;
    }

    private function getCuisineIds($cuisines)
    {
        $myString = $cuisines;
        $CuisineIds = [];
        if( strpos($cuisines, ',') !== false ) {
            $cuisines_list = explode(',', $myString);
            foreach($cuisines_list as $cuisines_values)
            {
                $CuisineTable = DB::table('cuisines')->where('cuisine_name',$cuisines_values)->first();
                if($CuisineTable != null){
                    $CuisineIds[] = $CuisineTable->id;
                }
            }
            $CuisineIds = implode(",",$CuisineIds);
        }
        else{
            $CuisineTable = DB::table('cuisines')->where('cuisine_name',$cuisines)->first();
            $CuisineIds = $CuisineTable->id;
        }
        return $CuisineIds;
    }

    private function getAllergensContain($allergens_contain)
    {
        $allergens_contain_ids =[];
        if( strpos($allergens_contain, ',') !== false ) {
            $allergens_contain_list = explode(', ', $allergens_contain);
            foreach($allergens_contain_list as $allergens_values)
            {
                $allergensTable = DB::table('allergens')->where('type','I')->where('title',$allergens_values)->first();
                if($allergensTable != null)
                {
                    $allergens_contain_ids[] = $allergensTable->ref;
                }
            }
            $allergens_contains = implode(",",$allergens_contain_ids);
        }
        else{
            $allergensTable = DB::table('allergens')->where('type','I')->where('title',$allergens_contain)->first();
            $allergens_contains = $allergensTable->ref;
        }
        return $allergens_contains;
    }

    private function getMenus($menus,$eateryId)
    {
        $menu_ids = [];
        if( strpos($menus, ',') !== false )
        {
            $menu_list = explode(', ', $menus);
            foreach($menu_list as $menu_values)
            {
                $menuTable = DB::table('menu')->where('menu',$menu_values)->where('company','FoodAdvisr')->where('eatery_id',$eateryId)->first();
                if($menuTable != null)
                {
                    $menu_ids = $menuTable->ref;
                }
                else
                {
                    $menu = new Menu();
                    $menu->company = "FoodAdvisr";
                    $menu->menu = $menu_values;
                    $menu->submenu="NULL";
                    $menu->description="NULL";
                    $menu->eatery_id = $eateryId;
                    $menu->group_id = 0;
                    $menu->sort_order=1;
                    $menu->is_visible=1;
                    $menu->save();
                    $menu_ids[] = $menu->ref;
                }
            }
            $menus_ids = implode(",",$menu_ids);
        }
        else{
            $menuTable = DB::table('menu')->where('menu',$menus)->where('company','FoodAdvisr')->where('eatery_id',$eateryId)->first();
            if($menuTable != null)
            {
                $menu_ids = $menuTable->ref;
            }
            else
            {
                $menu = new Menu();
                $menu->company = "FoodAdvisr";
                $menu->menu = $menus;
                $menu->submenu="NULL";
                $menu->description="NULL";
                $menu->eatery_id = $eateryId;
                $menu->group_id = 0;
                $menu->sort_order=1;
                $menu->is_visible=1;
                $menu->save();
                $menu_ids = $menu->ref;
            }
            $menus_ids = $menu_ids;
        }
        return $menus_ids;
    }

    private function getMenuSection($menusections,$menuid,$eateryId)
    {
        $menussections_ids = [];
        if( strpos($menusections, ',') !== false )
        {
            $menusection_list = explode(', ', $menusections);
            foreach($menusection_list as $menusection_values)
            {
                $menusectionTable = DB::table('menu_section')->where('section_name',$menusection_values)->where('menu_id',$menuid)->where('eatery_id',$eateryId)->first();
                if($menusectionTable != null)
                {
                    $menusections_ids = $menusectionTable->id;
                }
                else
                {
                    $menusection = new MenuSection();
                    $menusection->section_name = $menusection_values;
                    $menusection->description="NULL";
                    $menusection->menu_id=$menuid;
                    $menusection->eatery_id = $eateryId;
                    $menusection->group_id = 0;
                    $menusection->is_visible=1;
                    $menusection->display_order=1;
                    $menusection->added_on = Carbon::now(new DateTimeZone('Europe/London'));
                    $menusection->added_by = Session::get('user_id');
                    $menusection->modified_by = Session::get('user_id');
                    $menusection->modified_on = Carbon::now(new DateTimeZone('Europe/London'));
                    $menusection->save();
                    $menusections_ids[] = $menusection->id;
                }
            }
            $menussections_ids = implode(",",$menusections_ids);
        }
        else{
            $menusectionTable = DB::table('menu_section')->where('section_name',$menusections)->where('menu_id',$menuid)->where('eatery_id',$eateryId)->first();
            if($menusectionTable != null)
            {
                $menusections_ids = $menusectionTable->id;
            }
            else
            {
                $menusection = new MenuSection();
                $menusection->section_name = $menusections;
                $menusection->description="NULL";
                $menusection->menu_id=$menuid;
                $menusection->eatery_id = $eateryId;
                $menusection->group_id = 0;
                $menusection->is_visible=1;
                $menusection->display_order=1;
                $menusection->added_on = Carbon::now(new DateTimeZone('Europe/London'));
                $menusection->added_by = Session::get('user_id');
                $menusection->modified_by = Session::get('user_id');
                $menusection->modified_on = Carbon::now(new DateTimeZone('Europe/London'));
                $menusection->save();
                $menusections_ids[] = $menusection->id;
            }
            $menussections_ids = $menusections_ids;
        }

        return $menussections_ids;
    }

    private function getMenuSubSection($menusubsections,$menusection,$eateryId)
    {
        $menussubsections_ids = [];
        if( strpos($menusubsections, ',') !== false )
        {
            $menusubsection_list = explode(', ', $menusubsections);
            foreach($menusubsection_list as $menusubsection_values)
            {
                $menusubsectionTable = DB::table('menu_sub_section')->where('sub_section_name',$menusubsection_values)->where('section_id',$menusection)->where('eatery_id',$eateryId)->first();
                if($menusubsectionTable != null)
                {
                    $menusubsections_ids = $menusubsectionTable->id;
                }
                else
                {
                    $menusubsection = new MenuSubSection();
                    $menusubsection->sub_section_name = $menusubsection_values;
                    $menusubsection->description="NULL";
                    $menusubsection->section_id=$menusection;
                    $menusubsection->eatery_id = $eateryId;
                    $menusubsection->group_id = 0;
                    $menusubsection->is_visible=1;
                    $menusubsection->display_order=1;
                    $menusubsection->added_on = Carbon::now(new DateTimeZone('Europe/London'));
                    $menusubsection->added_by = Session::get('user_id');
                    $menusubsection->modified_by = Session::get('user_id');
                    $menusubsection->modified_on = Carbon::now(new DateTimeZone('Europe/London'));
                    $menusubsection->save();
                    $menusubsections_ids[] = $menusubsection->id;
                }
            }
            $menussubsections_ids = implode(",",$menusubsections_ids);
        }
        else{
            $menusubsectionTable = DB::table('menu_sub_section')->where('sub_section_name',$menusubsections)->where('section_id',$menusection)->where('eatery_id',$eateryId)->first();
            if($menusubsectionTable != null)
            {
                $menusubsections_ids = $menusubsectionTable->id;
            }
            else
            {
                $menusubsection = new MenuSubSection();
                $menusubsection->sub_section_name = $menusubsections;
                $menusubsection->description="NULL";
                $menusubsection->section_id=$menusection;
                $menusubsection->eatery_id = $eateryId;
                $menusubsection->group_id = 0;
                $menusubsection->is_visible=1;
                $menusubsection->display_order=1;
                $menusubsection->added_on = Carbon::now(new DateTimeZone('Europe/London'));
                $menusubsection->added_by = Session::get('user_id');
                $menusubsection->modified_by = Session::get('user_id');
                $menusubsection->modified_on = Carbon::now(new DateTimeZone('Europe/London'));
                $menusubsection->save();
                $menusubsections_ids = $menusubsection->id;
            }
            $menussubsections_ids = $menusubsections_ids;
        }


        return $menussubsections_ids;
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
    public function destroy($id)
    {
        //
    }
}
