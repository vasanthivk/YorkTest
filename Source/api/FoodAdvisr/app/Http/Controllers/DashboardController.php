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

        $nonassociatedeateries = DB::table('eateries')
            ->select(DB::raw('*'))
            ->whereNull('IsAssociated')
            ->orWhere('IsAssociated', '=', 0)
            ->count();

        $associatedeateries = DB::table('eateries')
            ->select(DB::raw('*'))
            ->Where('IsAssociated', '=', 1)                              
            ->count();

        // $nonassociatedeateries = DB::table('eateries')
        //         ->select(DB::raw('count(*) as ClicksBeforeAssociated'))
        //         ->whereNull('IsAssociated')
        //         ->orWhere('IsAssociated', '=', 0)
        //         ->get();

        // $associatedeateries = DB::table('eateries')
        //         ->select(DB::raw('count(*) as ClicksAfterAssociated'))
        //         ->Where('IsAssociated', '=', 1)                              
        //         ->get();

        $v1_getclicksbeforeassociated = DB::table('eateries')
                ->select(DB::raw('BusinessName,ClicksBeforeAssociated'))
                ->whereNull('IsAssociated')
                ->orWhere('IsAssociated', '=', 0)
                ->Where('ClicksBeforeAssociated', '>', 0)
                //->groupby('ClicksBeforeAssociated','BusinessName')
                ->orderby('ClicksBeforeAssociated','DESC')
                ->LIMIT(5)
                ->get();
        
        $v1_gettop5eateriesAfterAssociated = v1_gettop5eateriesAfterAssociated();
        
        return view('dashboard.index', compact('establishment_count'))
        ->with('v1_getclicksbeforeassociated',$v1_getclicksbeforeassociated)
        ->with('nonassociatedeateries',$nonassociatedeateries)
        ->with('associatedeateries',$associatedeateries)
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
