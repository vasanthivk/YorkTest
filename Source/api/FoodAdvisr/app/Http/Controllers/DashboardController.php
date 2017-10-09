<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Eateries;
use DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $establishment_count = DB::table('eateries')
            ->select(DB::raw('*'))
            ->count();
        $v1_getclicksbeforeassociated = v1_gettop5eateriesBeforeAssociated();
        $v1_gettop5eateriesAfterAssociated = v1_gettop5eateriesAfterAssociated();
        return view('dashboard.index', compact('establishment_count'))
        ->with('v1_getclicksbeforeassociated',$v1_getclicksbeforeassociated)
        ->with('v1_gettop5eateriesAfterAssociated',$v1_gettop5eateriesAfterAssociated);
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
