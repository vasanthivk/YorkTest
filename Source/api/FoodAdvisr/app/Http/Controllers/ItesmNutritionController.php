<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Items;
use App\Recipe;
use App\Category;
use App\Ingredients;
use App\ItemNutritions;
use App\Nutrition;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
class ItesmNutritionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,10,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,10,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,10,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,10,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,10);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,10);
        
        return $privileges;
     }

    public function index(Request $request)
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $item_id = $request['item_id'];
        $item_nutritions = DB::table('item_nutritions')
                ->join('nutrition', 'nutrition.nutrition_id', '=', 'item_nutritions.nutrition_id')
                ->join('items', 'items.item_id', '=', 'item_nutritions.item_id')
                ->select(DB::raw('*,nutrition.title as nutrition,item_nutritions.item_nutrition_id as id,items.FHRSID as hotel_id'))
                ->where('item_nutritions.item_id','=',$item_id)
                ->get();  
          
         return View('itemnutritions.index', compact('item_nutritions'))         
        ->with('privileges',$privileges)
        ->with('item_id',$item_id);
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
        $item_id = $request['item_id'];
        $nutrition = Nutrition::all()->pluck('title','nutrition_id');

        return View('itemnutritions.create')          
        ->with('privileges',$privileges)
        ->with('nutrition',$nutrition)
        ->with('item_id',$item_id);
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
        $this->validate($request, []);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            $item_id = $request['item_id'];
            return Redirect::route('itemnutritions.create',array('item_id' => $item_id))
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $itemnutritions = new ItemNutritions();
            $itemnutritions->nutrition_id =  Input::get('nutrition_id');
            $itemnutritions->item_id =  $request['item_id'];
            $itemnutritions->save();            
            $nutrition = Nutrition::where('nutrition_id','=',$itemnutritions->nutrition_id)->get();
            
            $log = new Log();
            $log->module_id=10;
            $log->action='create';      
            $log->description='Item Nutritions ' . $nutrition[0]->title . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('itemnutritions.index',array('item_id' => $request['item_id']))->with('success','Item Nutritions Created Successfully!');
        
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
        $item_id = $request['item_id'];
        $category = Category::all()->pluck('category_name','category_id');        
        $items = Items::where('items.item_id',$id)->get();
        return View('items.edit')
        ->with('items',$items[0])
        ->with('item_id',$item_id)
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
            $item_id = $request['item_id'];
            return Redirect::route('items.edit',$id,array('item_id' => $item_id))
                ->withInput()
                ->withErrors($validator)
                ->with('warning', 'There were validation errors');
        }
        else
        {   
            Items::where('item_id','=',$id)
             ->update(array('title'=> Input::get('title'),'description'=> Input::get('description'),'is_visible'=> Input::get('is_visible'),'FHRSID'=> $request['item_id'],'category_id'=>Input::get('category_id'),'display_order' => Input::get('display_order')
                 ));

            $log = new Log();
            $log->module_id=10;
            $log->action='update';      
            $log->description='Recipe ' . Input::get('title') . ' is updated';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('items.index',array('item_id' => $request['item_id']))->with('success','Item Updated Successfully!');
        
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
         $itemnutritions = ItemNutritions::where('item_nutrition_id','=',$id)->get();
 
        if (is_null($itemnutritions))
        {
         return Redirect::back()->with('warning','Item Nutritions Details Are Not Found!');
        }
        else
        {
           ItemNutritions::where('item_nutrition_id','=',$id)->delete();
           $nutrition = Nutrition::where('nutrition_id','=',$itemnutritions[0]->nutrition_id)->get();
            
            $log = new Log();
            $log->module_id=10;
            $log->action='delete';      
            $log->description='Item Nutritions '. $nutrition[0]->title . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Item Nutritions Deleted Successfully!');
        }
    }
}
