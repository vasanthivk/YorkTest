<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Eateries;
use DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $weeks = [];
        for ($ind = 7; $ind >= 0; $ind--)
        {
            $week = [];
            $date  = Carbon::now()->addWeeks(-1 * $ind)->formatLocalized('%Y-%m-%d');
            $week[] =  $date;
            $week[] = DB::table('eateries')
                ->select(DB::raw('*'))
                ->where('AssociatedOn' ,'<=', $date)
                ->count();                
            $weeks[] = $week ;
        }

        
        $sql = 'select count(AssociatedOn) as Total,AssociatedOn  from eateries where AssociatedOn>0 and IsAssociated=1 group by AssociatedOn';
        $date_wise_onboard = DB::select( DB::raw($sql));

        $establishment_count = DB::table('eateries')
            ->select(DB::raw('*'))
            ->count();
        
        $registered_count = DB::table('user')
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

        $v1_gettop5eateriesBeforeAssociated = DB::table('eateries')
                ->select(DB::raw('BusinessName,ClicksBeforeAssociated'))
                ->Where('IsAssociated', '=', 0)
                ->orWhereNull('IsAssociated')
                ->Where('ClicksBeforeAssociated', '>', 0)
                ->orderby('ClicksBeforeAssociated','DESC')
                ->LIMIT(5)
                ->get();
        
        $v1_gettop5eateriesAfterAssociated = DB::table('eateries')
                ->select(DB::raw('BusinessName,ClicksAfterAssociated'))
                ->Where('IsAssociated', '=', 1)               
                ->orderby('ClicksAfterAssociated','DESC')
                ->LIMIT(5)
                ->get();

        $sql = 'select CEILING(FoodAdvisrOverallRating) as FoodAdvisrOverallRating, count(*) as Total from eateries where ifnull(FoodAdvisrOverallRating,0)>0 and IsAssociated=1 group by CEILING(FoodAdvisrOverallRating)';
        $foodadvisroverallratings = DB::select( DB::raw($sql));

        $sql = 'select sum(ClicksAfterAssociated) as ClicksAfterAssociated from eateries';
        $totalviews = DB::select( DB::raw($sql));
       
        return view('dashboard.index', compact('establishment_count'))
        ->with('v1_gettop5eateriesBeforeAssociated',$v1_gettop5eateriesBeforeAssociated)
        ->with('nonassociatedeateries',$nonassociatedeateries)
        ->with('associatedeateries',$associatedeateries)
        ->with('v1_gettop5eateriesAfterAssociated',$v1_gettop5eateriesAfterAssociated)
        ->with('weeks',$weeks)
        ->with('foodadvisroverallratings',$foodadvisroverallratings)
        ->with('registered_count',$registered_count)
        ->with('date_wise_onboard',$date_wise_onboard)
        ->with('totalviews',$totalviews);
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
