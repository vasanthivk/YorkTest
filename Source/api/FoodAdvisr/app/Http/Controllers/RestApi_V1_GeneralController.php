<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Responses;
use App\Http\Controllers\Controller;

class RestApi_V1_GeneralController extends Controller
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

    public function V1_GetHotels(Request $request)
    {
    	 $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
		 	$request = json_decode($postdata);
		 	$latitude = $request->{'latitude'};
            $longitude = $request->{'longitude'};            
	        $v1_gethotels = v1_gethotels($latitude,$longitude);
	        $data = array('status' => 0,'message' => 'Success','result' => $v1_gethotels);
	        return $this->appendHeaders($data);
     	}
     	else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

    public function V1_GetHotelDetailsById(Request $request)
    {
         $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
            $request = json_decode($postdata);
            $fhrs_id = $request->{'fhrs_id'};
            $v1_gethoteldetailsbyid = v1_gethoteldetailsbyid($fhrs_id);
            return $v1_gethoteldetailsbyid;
            $data = array('status' => 0,'message' => 'Success','result' => $v1_gethoteldetailsbyid);
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

    public function V1_GetTop10Hotels(Request $request)
    {
    	 $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
		 	$request = json_decode($postdata);
		 	$latitude = $request->{'latitude'};
            $longitude = $request->{'longitude'};            
	        $v1_gettop10hotels = v1_gettop10hotels($latitude,$longitude);
	        $data = array('status' => 0,'message' => 'Success','result' => $v1_gettop10hotels);
	        return $this->appendHeaders($data);
     	}
     	else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

     public function V1_GetCategories(Request $request)
    {
        return $this->appendHeaders(v1_getcategories());
    }   
    
}