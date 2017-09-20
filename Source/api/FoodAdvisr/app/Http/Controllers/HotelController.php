<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Establishment;
use DB;
use Input;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $location_id = Input::get('location_id');
        $allhotels = Establishment::all();
        $locations = DB::table('establishment')
                    ->select(DB::raw('establishment.LocalAuthorityCode,establishment.LocalAuthorityName'))
                    ->orderby('establishment.LocalAuthorityName','asc')
                    ->pluck('LocalAuthorityName','LocalAuthorityCode');
        if($location_id=='')
            $location_id = $allhotels[0]->LocalAuthorityCode;
        $hotels = DB::table('establishment')
            ->select(DB::raw('*'))
            ->where('establishment.LocalAuthorityCode','=',$location_id)
            ->get();
        return view('hotel.index',compact('hotels'))
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
        //
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
