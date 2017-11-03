<?php

namespace App\Http\Controllers;

use App\Dishes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Menu;
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
                        $sheet->getCell('D1')->setValueExplicit('cuisines_ids');
                        $sheet->getCell('E1')->setValueExplicit('lifestyle_choices_ids');
                        $sheet->getCell('F1')->setValueExplicit('allergens_contain_ids');
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
                        $sheet->getCell('Q1')->setValueExplicit('menus_ids');
                        $sheet->getCell('R1')->setValueExplicit('sections_ids');
                        $sheet->getCell('S1')->setValueExplicit('subsections_ids');
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
        if( $data->count()<=1)
            return back()->with('warning','No records to import.');
        if(!empty($data) && $data[0]->count()){
            foreach ($data->toArray() as $key => $value) 
            {        
                $dish = new Dishes();
                $dish->dish_name = $value['dish_name'];
                $dish->description = $value['description'];
                $dish->cuisines_ids = $value['cuisines_ids'];
                $dish->lifestyle_choices_ids = $value['lifestyle_choices_ids'];
                $dish->allergens_contain_ids = $value['allergens_contain_ids'];
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
                $dish->menus_ids = $value['menus_ids'];
                $dish->sections_ids = $value['sections_ids'];
                $dish->subsections_ids = $value['subsections_ids'];
                $dish->eatery_id = $eatery_id;
                $dish->default_price = $value['default_price'];
                $dish->allergens_may_contain = $value['allergens_may_contain'];
                $dish->added_on = Carbon::now(new DateTimeZone('Europe/London'));
                $dish->added_by = Session::get('user_id');
                $dish->modified_by = Session::get('user_id');
                $dish->modified_on = Carbon::now(new DateTimeZone('Europe/London'));
                $dish->Save();
                
            }
        }
        return back()->with('success','Inserted Menu successfully.'); 
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
