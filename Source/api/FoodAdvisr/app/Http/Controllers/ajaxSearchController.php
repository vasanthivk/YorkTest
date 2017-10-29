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
use App\Eateries;
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

        $all_eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.BusinessTypeID', '=', 'eateries.BusinessTypeID')
            ->leftjoin('cuisines', 'cuisines.id', '=', 'eateries.cuisines_ids')
            ->leftjoin('brands', 'brands.id', '=', 'eateries.BrandId')
            ->leftjoin('groups', 'groups.id', '=', 'eateries.GroupId')
            ->leftjoin('locations', 'locations.LocationID', '=', 'eateries.LocationID')
            ->where(function ($query) use ($search){
                $query->where('eateries.BusinessName', 'like', $search)
                    ->orwhere('eateries.ContactNumber', 'like', $search)
                    ->orwhere('eateries.locality', 'like', $search)
                    ->orwhere('cuisine_name', 'like', $search)
                    ->orwhere('groups.Description', 'like', $search)
                    ->orwhere('locations.Description', 'like', $search);
            })
            ->select(DB::raw('eateries.BusinessName,businesstype.Description as BusinessType,eateries.id,eateries.LogoExtension'))
            ->get();

        return $all_eateries;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
