<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Input;
use App\Establishment;
use Session;
use App\Log;
use File;
use Image;
use Carbon\Carbon;
use DateTimeZone;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

class AllHotelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,2,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,2,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,2,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,2,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,5);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,4);
        
        return $privileges;
     }

    public function index()
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $location_id = Input::get('location_id');       
        $locations = DB::table('establishment')
                    ->select(DB::raw('DISTINCT establishment.LocalAuthorityName as location_name,establishment.LocalAuthorityCode as id'))
                    ->orderby('location_name','asc')
                    ->get();
        $BusinessType = array("Hotel/bed & breakfast/guest house","Other catering premises",
"Restaurant/Cafe/Canteen","Pub/bar/nightclub","Takeaway/sandwich shop","Mobile caterer","Hospitals/Childcare/Caring Premises","Retailers - other","Retailers - supermarkets/hypermarkets");   

        if($location_id=='')
        {
            $all_hotels = DB::table('establishment')
            ->select(DB::raw('establishment.BusinessName,establishment.BusinessType,establishment.RatingValue,establishment.FHRSID as id'))
            ->where('establishment.LocalAuthorityCode','=','')
            ->get();
        }
        else{
        $all_hotels = DB::table('establishment')
            ->select(DB::raw('establishment.BusinessName,establishment.BusinessType,establishment.RatingValue,establishment.FHRSID as id'))
            ->where('establishment.LocalAuthorityCode','=',$location_id)
            ->wherein('establishment.BusinessType',$BusinessType)
            ->get();
        }
        
        return view('allhotels.index')
        ->with('all_hotels',$all_hotels)
        ->with('locations',$locations)
        ->with('location_id',$location_id)
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
        if($privileges['Add'] !='true')    
            return Redirect::to('/');
        $businesstypes = 'select BusinessType from establishment where BusinessType in("Hotel/bed & breakfast/guest house","Other catering premises","Restaurant/Cafe/Canteen","Pub/bar/nightclub",
            "Takeaway/sandwich shop","Mobile caterer","Hospitals/Childcare/Caring Premises","Retailers - other","Retailers - supermarkets/hypermarkets") group by BusinessType;';
        $result = DB::select( DB::raw($businesstypes));

        return view('allhotels.create')
        ->with('privileges',$privileges)
        ->with('result',$result[0]);
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
         $this->validate($request,['FHRSID'  => 'required|unique:establishment','BusinessName'  => 'required','BusinessType'  => 'required','LocalAuthorityName'=>'required','LocalAuthorityWebSite'=>'required','LocalAuthorityEmailAddress' =>'required|email','Longitude'=>'required','Latitude'=>'required','address' => 'required','mobile_no' => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('allhotels.create')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            $establishment = new Establishment();
            $establishment->FHRSID = Input::get('FHRSID');
            $establishment->BusinessName = Input::get('BusinessName');
            $establishment->BusinessType = Input::get('BusinessType');
            $establishment->BusinessTypeID = Input::get('BusinessTypeID');
            $establishment->LocalAuthorityName = Input::get('LocalAuthorityName');
            $establishment->LocalAuthorityWebSite = Input::get('LocalAuthorityWebSite');
            $establishment->LocalAuthorityEmailAddress = Input::get('LocalAuthorityEmailAddress');
            $establishment->Longitude = Input::get('Longitude');
            $establishment->Latitude = Input::get('Latitude');
            $establishment->address = Input::get('address');
            $establishment->mobile_no = Input::get('mobile_no');
            $establishment->Save();
            
            $log = new Log();
            $log->module_id=2;
            $log->action='create';      
            $log->description='Eateries ' . $establishment->BusinessName . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('allhotels.index')->with('success','Eateries Created Successfully!');
        
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
        $hotel =  Establishment::where('FHRSID','=',$id)->get();
        return View('allhotels.edit')
        ->with('hotel',$hotel[0]);
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
         $this->validate($request,['BusinessName'  => 'required','BusinessType'  => 'required','LocalAuthorityName'=>'required','LocalAuthorityWebSite'=>'required','LocalAuthorityEmailAddress' =>'required|email','Longitude'=>'required','Latitude'=>'required','address' => 'required','mobile_no' => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            return Redirect::route('allhotels.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {    
            Establishment::where('FHRSID','=',$id)
            ->update(array(
                'BusinessName'=> Input::get('BusinessName'),
                'BusinessType'=> Input::get('BusinessType'),
                'LocalAuthorityName'=> Input::get('LocalAuthorityName'),
                'LocalAuthorityWebSite'=> Input::get('LocalAuthorityWebSite'),
                'LocalAuthorityEmailAddress'=> Input::get('LocalAuthorityEmailAddress'),
                'Longitude'=> Input::get('Longitude'),
                'Latitude'=> Input::get('Latitude'),
                'address'=> Input::get('address'),
                'mobile_no'=> Input::get('mobile_no')
            ));
            
            $log = new Log();
            $log->module_id=2;
            $log->action='update';      
            $log->description='Eateries ' . $input['BusinessName'] . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('allhotels.index')->with('success','Eateries Updated Successfully!');
        
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
        $hotel =  Establishment::where('FHRSID','=',$id)->get();
        if (is_null($hotel))
        {
         return Redirect::back()->with('warning','Eateries Details Are Not Found!');
        }
        else
        {
           Establishment::where('FHRSID','=',$id)->delete();

            $log = new Log();
            $log->module_id=2;
            $log->action='delete';      
            $log->description='Eateries '. $hotel[0]->BusinessName . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Eateries Deleted Successfully!');
        }
    }
}
