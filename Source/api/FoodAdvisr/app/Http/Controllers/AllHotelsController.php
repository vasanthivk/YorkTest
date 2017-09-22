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
        $gethotels = DB::table('establishment')
                    ->select(DB::raw('establishment.LocalAuthorityCode'))
                    ->get();
        $locations = DB::table('establishment')
                    ->select(DB::raw('establishment.LocalAuthorityName as location_name,establishment.LocalAuthorityCode as id'))
                    ->orderby('location_name','asc')
                    ->pluck('location_name','id');

        if($location_id=='')
            $location_id = $gethotels[0]->LocalAuthorityCode;
        $all_hotels = DB::table('establishment')
            ->select(DB::raw('establishment.BusinessName,establishment.RatingValue,establishment.FHRSID as id'))
            ->where('establishment.LocalAuthorityCode','=',$location_id)
            ->get();
        return view('allhotels.index')
        ->with('all_hotels',$all_hotels)
        ->with('locations',$locations)
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
        return view('allhotels.create')
        ->with('privileges',$privileges);
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
         $this->validate($request,['FHRSID'  => 'required|unique:establishment','LocalAuthorityBusinessID'  => 'required','BusinessName'  => 'required','BusinessType'  => 'required','BusinessTypeID'  => 'required|numeric','RatingValue' =>'required|numeric','RatingKey' => 'required','RatingDate' => 'required','LocalAuthorityCode' => 'required|numeric','LocalAuthorityName'=>'required','LocalAuthorityWebSite'=>'required','LocalAuthorityEmailAddress' =>'required|email','SchemeType'=>'required','Longitude'=>'required','Latitude'=>'required','Hygiene'=>'required|numeric','Structural' => 'required|numeric','ConfidenceInManagement' => 'required|numeric']);        
        
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
            $establishment->LocalAuthorityBusinessID = Input::get('LocalAuthorityBusinessID');
            $establishment->BusinessName = Input::get('BusinessName');
            $establishment->BusinessType = Input::get('BusinessType');
            $establishment->BusinessTypeID = Input::get('BusinessTypeID');
            $establishment->RatingValue = Input::get('RatingValue');
            $establishment->RatingKey = Input::get('RatingKey');
            $establishment->RatingDate = Input::get('RatingDate');
            $establishment->LocalAuthorityCode = Input::get('LocalAuthorityCode');
            $establishment->LocalAuthorityName = Input::get('LocalAuthorityName');
            $establishment->LocalAuthorityWebSite = Input::get('LocalAuthorityWebSite');
            $establishment->LocalAuthorityEmailAddress = Input::get('LocalAuthorityEmailAddress');
            $establishment->SchemeType = Input::get('SchemeType');
            $establishment->NewRatingPending = Input::get('NewRatingPending');
            $establishment->Longitude = Input::get('Longitude');
            $establishment->Latitude = Input::get('Latitude');
            $establishment->Hygiene = Input::get('Hygiene');
            $establishment->Structural = Input::get('Structural');
            $establishment->ConfidenceInManagement = Input::get('ConfidenceInManagement');
            $establishment->Save();
            
            $log = new Log();
            $log->module_id=2;
            $log->action='create';      
            $log->description='Hotel ' . $establishment->BusinessName . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('allhotels.index')->with('success','Hotel Created Successfully!');
        
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
         $this->validate($request,['LocalAuthorityBusinessID'  => 'required','BusinessName'  => 'required','BusinessType'  => 'required','BusinessTypeID'  => 'required|numeric','RatingValue' =>'required|numeric','RatingKey' => 'required','RatingDate' => 'required','LocalAuthorityCode' => 'required|numeric','LocalAuthorityName'=>'required','LocalAuthorityWebSite'=>'required','LocalAuthorityEmailAddress' =>'required|email','SchemeType'=>'required','Longitude'=>'required','Latitude'=>'required','Hygiene'=>'required|numeric','Structural' => 'required|numeric','ConfidenceInManagement' => 'required|numeric']);        
        
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
            ->update(array('LocalAuthorityBusinessID'=> Input::get('LocalAuthorityBusinessID'),
                'BusinessName'=> Input::get('BusinessName'),
                'BusinessType'=> Input::get('BusinessType'),
                'BusinessTypeID'=> Input::get('BusinessTypeID'),
                'RatingValue'=> Input::get('RatingValue'),
                'RatingKey'=> Input::get('RatingKey'),
                'RatingDate'=> Input::get('RatingDate'),
                'LocalAuthorityCode'=> Input::get('LocalAuthorityCode'),
                'LocalAuthorityName'=> Input::get('LocalAuthorityName'),
                'LocalAuthorityWebSite'=> Input::get('LocalAuthorityWebSite'),
                'LocalAuthorityEmailAddress'=> Input::get('LocalAuthorityEmailAddress'),
                'SchemeType'=> Input::get('SchemeType'),
                'NewRatingPending'=> Input::get('NewRatingPending'),
                'Longitude'=> Input::get('Longitude'),
                'Hygiene'=> Input::get('Hygiene'),
                'Latitude'=> Input::get('Latitude'),
                'Structural'=> Input::get('Structural'),
                'ConfidenceInManagement'=> Input::get('ConfidenceInManagement')
            ));
            
            $log = new Log();
            $log->module_id=2;
            $log->action='update';      
            $log->description='Hotel ' . $input['BusinessName'] . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('allhotels.index')->with('success','Hotel Updated Successfully!');
        
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
         return Redirect::back()->with('warning','Hotel Details Are Not Found!');
        }
        else
        {
           Establishment::where('FHRSID','=',$id)->delete();

            $log = new Log();
            $log->module_id=2;
            $log->action='delete';      
            $log->description='Hotel '. $hotel[0]->BusinessName . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Hotel Deleted Successfully!');
        }
    }
}
