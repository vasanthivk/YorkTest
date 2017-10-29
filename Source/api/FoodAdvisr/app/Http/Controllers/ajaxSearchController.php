<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Input;
use App\Eateries;
use App\BusinessType;
use App\Locations;
use App\EateriesMedia;
use Session;
use App\Log;
use File;
use Image;
use Carbon\Carbon;
use DateTimeZone;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

class ajaxSearchController extends Controller
{
    /*
     * Display a listing of the resource
     *
     * @return Response
     *
     * */
    public function ajaxSearchByResult(Request $request)
    {
        $search = Input::all();

        if(isset($search) && !empty($search))
        {
            $search_result = $search;
        }
        else{
            $search_result = 'London';
        }

        $all_eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.BusinessTypeID', '=', 'eateries.BusinessTypeID')
            ->leftjoin('cuisines', 'cuisines.id', '=', 'eateries.cuisines_ids')
            ->leftjoin('groups', 'groups.id', '=', 'eateries.GroupId')
            ->leftjoin('locations', 'locations.LocationID', '=', 'eateries.LocationID')
            ->where('eateries.BusinessName', 'like', $search_result)
                    ->orwhere('eateries.ContactNumber', 'like', $search_result)
                    ->orwhere('eateries.locality', 'like', $search_result)
                    ->orwhere('cuisine_name', 'like', $search_result)
                    ->orwhere('groups.Description', 'like', $search_result)
                    ->orwhere('locations.Description', 'like', $search_result)
            ->select(DB::raw('eateries.BusinessName,businesstype.Description as BusinessType,eateries.id,eateries.LogoExtension'))
            ->get();

        $result_html = '<div>';
        foreach($all_eateries as $eatery){
            $result_html .= '<div>'.$eatery->BusinessName.'</div>';
        }
        $result_html .='</div>';

        return $result_html;
    }
}
