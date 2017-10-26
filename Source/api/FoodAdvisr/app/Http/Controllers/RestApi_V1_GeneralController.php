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

    public function V1_GetEateries(Request $request)
    {
    	 $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
		 	$request = json_decode($postdata);
		 	$latitude = $request->{'latitude'};
            $longitude = $request->{'longitude'};            
	        $v1_geteateries = v1_geteateries($latitude,$longitude);
	        $data = array('status' => 0,'message' => 'Success','result' => $v1_geteateries);
	        return $this->appendHeaders($data);
     	}
     	else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

    public function V1_GetEateryDetailsById(Request $request)
    {
         $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
            $request = json_decode($postdata);
            $id = $request->{'id'};
            $v1_geteaterydetailsbyid = v1_geteaterydetailsbyid($id);
            $data = array('status' => 0,'message' => 'Success','result' => $v1_geteaterydetailsbyid);
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

    public function V1_GetTop10Eateries(Request $request)
    {
    	 $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
		 	$request = json_decode($postdata);
		 	$latitude = $request->{'latitude'};
            $longitude = $request->{'longitude'};            
	        $v1_gettop10eateries = v1_gettop10eateries($latitude,$longitude);
	        $data = array('status' => 0,'message' => 'Success','result' => $v1_gettop10eateries);
	        return $this->appendHeaders($data);
     	}
     	else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        }
    }

    public function V1_AddClickBeforeAssociated(Request $request)
    {
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
            $request = json_decode($postdata);
            $id = $request->{'id'};
            $data = array('status' => 0,'message' => 'Success','result' =>  v1_addclickbeforeassociated($id));
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        } 
    }

    public function V1_AddClickAfterAssociated(Request $request)
    {
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) 
        {
            $request = json_decode($postdata);
            $id = $request->{'id'};
            $data = array('status' => 0,'message' => 'Success','result' => v1_addclickafterassociated($id));
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => '201','message' => 'Invalid Inputdata','result' => -1000);
            return $this->appendHeaders($data);
        } 
    }

    public function V1_GetTop5EateriesBeforeAssociated(Request $request)
    {
        $v1_gettop5eateriesBeforeAssociated = v1_gettop5eateriesBeforeAssociated();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_gettop5eateriesBeforeAssociated);
        return $this->appendHeaders($data);
    }

    public function V1_GetTop5EateriesAfterAssociated(Request $request)
    {
        $v1_gettop5eateriesAfterAssociated = v1_gettop5eateriesAfterAssociated();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_gettop5eateriesAfterAssociated);
        return $this->appendHeaders($data);
    }

    public function GetEateryByLocation(Request $request)
    {
        $location_id = $request['location_id'];

        if($location_id==null || $location_id=='')
        {
            $data = array('status' => '201','message' => 'Invalid Location id');
            return $this->appendHeaders($data);
        }
        return $this->appendHeaders(getEateryByLocation($location_id));
    }

    public function V1_GetCuisines(Request $request)
    {
        $v1_getcuisines = v1_getcuisines();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_getcuisines);
        return $this->appendHeaders($data);
    }

    public function V1_LifeStyleChoices(Request $request)
    {
        $v1_lifestylechoices = v1_lifestylechoices();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_lifestylechoices);
        return $this->appendHeaders($data);
    }
    
}
