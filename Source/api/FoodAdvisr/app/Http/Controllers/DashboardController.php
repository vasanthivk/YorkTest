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
                ->where('associated_on' ,'<=', $date)
                ->count();                
            $weeks[] = $week ;
        }

        $sql = 'select count(associated_on) as Total,associated_on  from eateries where associated_on>0 and is_associated=1 and is_enabled=1 group by associated_on';
        $date_wise_onboard = DB::select( DB::raw($sql));

        $establishment_count = DB::table('eateries')
            ->select(DB::raw('*'))
            ->where('is_enabled','=',1)
            ->count();
        
        $registered_count = DB::table('person')
            ->select(DB::raw('*'))
            ->count();
                
        $sql = 'select count(*) as Total from eateries where is_associated = 0 or isnull(is_associated)  and is_enabled=1';
        $nonassociatedeateries = DB::select( DB::raw($sql));
        
        $associatedeateries = DB::table('eateries')
            ->select(DB::raw('*'))
            ->Where('is_associated', '=', 1)
             ->where('is_enabled','=',1)
            ->count();

        $v1_gettop5eateriesBeforeAssociated = DB::table('eateries')
                ->select(DB::raw('business_name,clicks_before_associated'))
                ->Where('is_associated', '=', 0)
                ->orWhereNull('is_associated')
                ->Where('clicks_before_associated', '>', 0)
                 ->where('is_enabled','=',1)
                ->orderby('clicks_before_associated','DESC')
                ->LIMIT(5)
                ->get();
        
        $v1_gettop5eateriesAfterAssociated = DB::table('eateries')
                ->select(DB::raw('business_name,clicks_after_associated'))
                ->Where('is_associated', '=', 1)
                 ->where('is_enabled','=',1)
                ->orderby('clicks_after_associated','DESC')
                ->LIMIT(5)
                ->get();

        $sql = 'select CEILING(foodadvisr_overall_rating) as FoodAdvisrOverallRating, count(*) as Total from eateries where ifnull(foodadvisr_overall_rating,0)>0 and is_associated=1 and is_enabled=1 group by CEILING(foodadvisr_overall_rating)';
        $foodadvisroverallratings = DB::select( DB::raw($sql));

        $sql = 'select sum(clicks_after_associated) as ClicksAfterAssociated from eateries where is_enabled=1';
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
