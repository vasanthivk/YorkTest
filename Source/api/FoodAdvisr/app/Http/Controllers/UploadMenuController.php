<?php

namespace App\Http\Controllers;

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
    public function index()
    {
         return view('uploadmenu.index');
    }

    public function generateExcel(Request $request) 
    {
      $data = Menu::get()->toArray();
       Excel::create('Menu_'.Carbon::now(new DateTimeZone('Asia/Kolkata')),function($excel) use($data) 
                {
                    $excel->sheet('Sheet 1',function($sheet) use($data){
                        $sheet->getCell('A1')->setValueExplicit('SlNo');
                        $sheet->getCell('B1')->setValueExplicit('ItemName');
                        $sheet->getCell('C1')->setValueExplicit('ItemDescription');
                        $sheet->getCell('D1')->setValueExplicit('ItemShortName');
                        $sheet->getCell('E1')->setValueExplicit('ContainsNuts');
                        $sheet->getCell('F1')->setValueExplicit('DairyFree');
                        $sheet->getCell('G1')->setValueExplicit('GlutenFree');
                        $sheet->getCell('H1')->setValueExplicit('Vegan');
                        $sheet->getCell('I1')->setValueExplicit('ItemDefaultPrice');
                        $sheet->getCell('J1')->setValueExplicit('IsDineIn');
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
                 $menu    = new Menu();
                    $menu->item_name = $value['itemname'];
                    $menu->item_description = $value['itemdescription'];
                    $menu->item_short_name = $value['itemshortname'];
                    $menu->contains_nuts = $value['containsnuts'];
                    $menu->dairy_free = $value['dairyfree'];
                    $menu->gluten_free = $value['glutenfree'];
                    $menu->vegan = $value['vegan'];
                    $menu->item_default_price = $value['itemdefaultprice'];
                    $menu->is_dinein = $value['isdinein'];
                    $menu->category_id = 1;
                    $menu->company_id = 1;
                    $menu->added_on = Carbon::now(new DateTimeZone('Asia/Kolkata'));
                    $menu->added_by = Session::get('user_id');
                    $menu->modified_by = Session::get('user_id');
                    $menu->Save();
                
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