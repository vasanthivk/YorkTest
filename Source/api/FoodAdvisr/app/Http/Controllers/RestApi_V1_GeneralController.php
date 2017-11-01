<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Responses;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTimeZone;

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
            $cuisines_ids = $request->{'cuisines_ids'};                        
            $lifestyle_choices_ids = $request->{'lifestyle_choices_ids'};
	        $v1_geteateries = v1_geteateries($latitude,$longitude,$cuisines_ids,$lifestyle_choices_ids);
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

    public function GetMenusectionByMenuIds(Request $request)
    {
        $menu_id = $request['menu_id'];

        if($menu_id==null || $menu_id=='')
        {
            $data = array('status' => '201','message' => 'Invalid Menu id');
            return $this->appendHeaders($data);
        }
        return $this->appendHeaders(getMenusectionByMenuIds($menu_id));
    }

     public function GetMenuSubsectionByMenuSection(Request $request)
    {
        $section_id = $request['section_id'];

        if($section_id==null || $section_id=='')
        {
            $data = array('status' => '201','message' => 'Invalid Section id');
            return $this->appendHeaders($data);
        }
        return $this->appendHeaders(getMenusubsectionByMenuSection($section_id));
    }

    public function V1_GetCuisines(Request $request)
    {
        $v1_getcuisines = v1_getcuisines();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_getcuisines);
        return $this->appendHeaders($data);
    }

    public function V1_GetLifeStyleChoices(Request $request)
    {
        $v1_lifestylechoices = v1_lifestylechoices();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_lifestylechoices);
        return $this->appendHeaders($data);
    }

    public function V1_GetNutritions(Request $request)
    {
        $v1_getnutritions = v1_getnutritions();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_getnutritions);
        return $this->appendHeaders($data);
    }

    public function V1_GetAllergens(Request $request)
    {
        $v1_getallergens = v1_getallergens();
        $data = array('status' => 0,'message' => 'Success','result' => $v1_getallergens);
        return $this->appendHeaders($data);
    }

     public function V1_AddToFavouriteEatery(Request $request)
    {
         $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $email = $request->{'email'};
            if($email == '')
            {
               $data = array('status' => -201,'message' => 'Invalid User Id');
                return $this->appendHeaders($data);
            }
            $eatery_id = $request->{'eatery_id'};
            if($eatery_id == '')
            {
                $data = array('status' => -202,'message' => 'Invalid Eatery Id');
                return $this->appendHeaders($data);
            }
            $returnvalue =  v1_addtofavouriteeatery($email, $eatery_id);

            $message = "";
            if($returnvalue == -2001)
                $message = "Eatery details are not exists";
            elseif($returnvalue == -2002)
                $message = "User details are not exists";
            elseif($returnvalue == -2003)
                $message = "Eatery already added in favourite list";
            else
                $message = "Success";
            if (in_array($returnvalue, array(-2001,-2002,-2003), true ) )
            { 
            $data = array('status' => $returnvalue,'message' => $message,'result' => null);
            return $this->appendHeaders($data);
            }
            $data = array('status' => 0,'message' => $message,'result' => $returnvalue);
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => -1000,'message' => 'Invalid Inputdata','result' => null);
            return $this->appendHeaders($data);
        }
    }

     public function V1_RemoveFromFavouriteEatery(Request $request)
    {
         $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $email = $request->{'email'};
            if($email == '')
            {
               $data = array('status' => -203,'message' => 'Invalid User Id');
                return $this->appendHeaders($data);
            }
            $eatery_id = $request->{'eatery_id'};
            if($eatery_id == '')
            {
                $data = array('status' => -204,'message' => 'Invalid Eatery Id');
                return $this->appendHeaders($data);
            }
            $returnvalue =  v1_removefromfavouriteeatery($email, $eatery_id);

            $message = "";
            if($returnvalue == -2004)
                $message = "Eatery details are not exists";
            elseif($returnvalue == -2005)
                $message = "User details are not exists";
            elseif($returnvalue == -2006)
                $message = "Eatery already removed from favourite list";
            else
                $message = "Success";
            if (in_array($returnvalue, array(-2004,-2005,-2006), true ) )
            { 
            $data = array('status' => $returnvalue,'message' => $message,'result' => null);
            return $this->appendHeaders($data);
            }
            $data = array('status' => 0,'message' => $message,'result' => $returnvalue);
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => -1000,'message' => 'Invalid Inputdata','result' => null);
            return $this->appendHeaders($data);
        }
    }

    public function V1_GetFavouriteEateries(Request $request)
    {
         $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $userid = $request->{'userid'};
            if($userid == '')
            {
               $data = array('status' => -205,'message' => 'Invalid User Id');
                return $this->appendHeaders($data);
            }           
            $returnvalue =  v1_getfavouriteeateries($userid);
            $data = array('status' => 0,'message' => 'Success','result' => $returnvalue);
            return $this->appendHeaders($data);
         }
        else
        {
            $data = array('status' => -1000,'message' => 'Invalid Inputdata','result' => null);
            return $this->appendHeaders($data);
        }
    }

    public function V1_RemoveFavouriteEateries(Request $request)
    {
          $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $userid = $request->{'userid'};
            if($userid == '')
            {
               $data = array('status' => -206,'message' => 'Invalid User Id');
                return $this->appendHeaders($data);
            }
            $eatery_id = $request->{'eatery_id'};
            if($eatery_id == '')
            {
                $data = array('status' => -207,'message' => 'Invalid Eatery Id');
                return $this->appendHeaders($data);
            }
            $returnvalue =  v1_removefavouriteeateries($userid, $eatery_id);
           
            $data = array('status' => 0,'message' => 'Success','result' => $returnvalue);
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => -1000,'message' => 'Invalid Inputdata','result' => null);
            return $this->appendHeaders($data);
        }
    }

    public function V1_AddFeedbackEatery(Request $request)
    {
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $feedback['userid'] = $request->{'userid'};
            $feedback['email'] = $request->{'email'};
            $feedback['message'] = $request->{'message'};
            $feedback['msgdate'] = Carbon::now(new DateTimeZone('Europe/London'));
            $feedback['response'] = $request->{'response'};
            $feedback['rating'] = $request->{'rating'};
            $feedback['eatery_id'] = $request->{'eatery_id'};
            $feedback['resptime'] = Carbon::now(new DateTimeZone('Europe/London'));
            $feedback['version'] = $request->{'version'};
            $feedback['device'] = $request->{'device'};
            $feedback['os'] = $request->{'os'};
            $feedback['osversion'] = $request->{'osversion'};
            $feedback['model'] = $request->{'model'};
            $feedback['maker'] = $request->{'maker'};
            if($feedback == array())
            {
                $data = array('status' => -207,'message' => 'Invalid User Id');
                return $this->appendHeaders($data);
            }
            $returnvalue =  v1_addfeedbackeateries($feedback);
            $data = array('status' => 0,'message' => 'Success','result' => $returnvalue);
            return $this->appendHeaders($data);
        }
        else
        {
            $data = array('status' => -1000,'message' => 'Invalid Inputdata','result' => null);
            return $this->appendHeaders($data);
        }
    }
}
