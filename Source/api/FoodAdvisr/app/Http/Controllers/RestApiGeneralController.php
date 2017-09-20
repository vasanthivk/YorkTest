<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Responses;
use App\Http\Controllers\Controller;

class RestApiGeneralController extends Controller
{
    public  function appendHeaders($object)
    {
    	$headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        return response()->json($object,200,$headers);
    }


    public function GetHotelDetailsById(Request $request)
    {
    	 $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
		 	$request = json_decode($postdata);
            $fhrs_id = $request->{'fhrs_id'};
	        $gethoteldetailsbyid = gethoteldetailsbyid($fhrs_id);
	        $data = array('status' => 0,'message' => 'Success','result' => $gethoteldetailsbyid);
	        return $this->appendHeaders($data);
     	}
     	else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

    public function GetHotels(Request $request)
    {
    	 $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
		 	$request = json_decode($postdata);
		 	$latitude = $request->{'latitude'};
            $longitude = $request->{'longitude'};            
	        $gethotels = gethotels($latitude,$longitude);
	        $data = array('status' => 0,'message' => 'Success','result' => $gethotels);
	        return $this->appendHeaders($data);
     	}
     	else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }  
}
