<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Establishment;
use App\Eateries;
use Redirect;
use Session;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use DB;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getPrivileges()
     {
        $roleid = Session::get("role_id");
        $privileges['Upload']  = ValidateUserPrivileges($roleid,3,1);//role, module, privilege
        
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,5);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,6);
        
        return $privileges;
     }

    public function index()
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $location_id = Input::get('location_id');       
        $locations = DB::table('locations')
                    ->select(DB::raw('locations.Description as location_name,locations.LocationID as id'))
                    ->orderby('location_name','asc')
                    ->get();
        $all_eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.BusinessTypeID', '=', 'eateries.BusinessTypeID')
            ->select(DB::raw('eateries.FHRSID,eateries.Latitude,eateries.Longitude'))
            ->where('eateries.LocationID','=',$location_id)
            ->limit(2000)
            ->get();
          
            // return $all_eateries;
            foreach ($all_eateries as $key => $value) {

                $lat = $value->Latitude; //latitude
                $lng = $value->Longitude; //longitude
                if($lat != null && $lng != null)                
                {
                $address = getaddress($lat,$lng);
                if($address)
                {
                    Eateries::where('FHRSID','=',$value->FHRSID)
                 ->update(array('Address'=> $address
                ));
                }
            }
               
            }
             
        return view('uploadhotel.index')
        ->with('privileges',$privileges)
        ->with('location_id',$location_id)
        ->with('locations',$locations);
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
        $input = Input::all();
        $url_last = substr($input['url'], -4);
        $this->validate($request, [
            'url'  => 'required']);
        if($url_last == '.xml')
        {
        $xml = simplexml_load_file($input['url']);// print_r($xml);
        $data = array();
        $data = $xml->EstablishmentCollection->EstablishmentDetail;
        foreach ($data as $key => $value) 
        {
           $establishment_count = DB::table('establishment')
            ->select(DB::raw('*'))
            ->where('establishment.FHRSID','=',$value->FHRSID)
            ->count();
            if($establishment_count == 0)
            {
            $establishment = new Establishment();
            $establishment->FHRSID = $value->FHRSID;
            $establishment->LocalAuthorityBusinessID = $value->LocalAuthorityBusinessID;
            $establishment->BusinessName = $value->BusinessName;
            $establishment->BusinessType = $value->BusinessType;
            $establishment->BusinessTypeID = $value->BusinessTypeID;
            $establishment->RatingValue = $value->RatingValue;
            $establishment->RatingKey = $value->RatingKey;
            $establishment->RatingDate = $value->RatingDate;
            $establishment->LocalAuthorityCode = $value->LocalAuthorityCode;
            $establishment->LocalAuthorityName = $value->LocalAuthorityName;
            $establishment->LocalAuthorityWebSite = $value->LocalAuthorityWebSite;
            $establishment->LocalAuthorityEmailAddress = $value->LocalAuthorityEmailAddress;
            $establishment->SchemeType = $value->SchemeType;
            $establishment->NewRatingPending = $value->NewRatingPending;
            $establishment->Longitude = $value->Geocode->Longitude;
            $establishment->Latitude = $value->Geocode->Latitude;
            $establishment->Hygiene = $value->Scores->Hygiene;
            $establishment->Structural = $value->Scores->Structural;
            $establishment->ConfidenceInManagement = $value->Scores->ConfidenceInManagement;
            $establishment->Save();
            }
            else
            {
               Establishment::where('FHRSID','=',$value->FHRSID)
            ->update(array('LocalAuthorityBusinessID'=> $value->LocalAuthorityBusinessID,
                'BusinessName'=> $value->BusinessName,
                'BusinessType'=> $value->BusinessType,
                'BusinessTypeID'=> $value->BusinessTypeID,
                'RatingValue'=> $value->RatingValue,
                'RatingKey'=> $value->RatingKey,
                'RatingDate'=> $value->RatingDate,
                'LocalAuthorityCode'=> $value->LocalAuthorityCode,
                'LocalAuthorityName'=> $value->LocalAuthorityName,
                'LocalAuthorityWebSite'=> $value->LocalAuthorityWebSite,
                'LocalAuthorityEmailAddress'=> $value->LocalAuthorityEmailAddress,
                'SchemeType'=> $value->SchemeType,
                'NewRatingPending'=> $value->NewRatingPending,
                'Longitude'=> $value->Geocode->Longitude,
                'Hygiene'=> $value->Scores->Hygiene,
                'Latitude'=> $value->Geocode->Latitude,
                'Structural'=> $value->Scores->Structural,
                'ConfidenceInManagement'=> $value->Scores->ConfidenceInManagement,
            ));
            } 
           
        }
      }
      else
      {
        return Redirect::back()->with('warning','Required Only XML Url!')
        ->withInput(); 
      }
            $log = new Log();
            $log->module_id=3;
            $log->action='upload';      
            $log->description='Hotel ' . $value->LocalAuthorityName . ' is uploaded';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::back()->with('success','Hotels Uploaded Successfully!');
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
