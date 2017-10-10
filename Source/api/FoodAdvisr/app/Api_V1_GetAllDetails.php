<?php
use App\Defaults;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);
use App\Eateries;

	function v1_gethotels($latitude,$longitude)
	{
		 $defaults = Defaults::all();    
         $search_radius = $defaults[0]->search_radius;
         $search_result_limit = $defaults[0]->search_result_limit;
    	 $sql  = "select FHRSID
            from eateries where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ";
        $result = DB::select( DB::raw($sql));
        $hotelsids = [];
         foreach($result as $result1)
    	{
        	$hotelsids[] = $result1->FHRSID; 
    	}
    	
    	$hotels = DB::table('eateries')
    		    ->select(DB::raw('eateries.*,round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(("'.$latitude.'" - abs(latitude)) * pi()/180 / 2),2) +  COS("'.$latitude.'" * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(("'.$longitude.'" - longitude) * pi()/180 / 2), 2) ))),2) as distance'))
    		   ->wherein('FHRSID',$hotelsids)
               ->orderby('distance','asc')
               ->limit($search_result_limit)
    		   ->get();

    	return $hotels;
	}

    function v1_gethoteldetailsbyid($fhrs_id)
    {
        $sql  = "select * from eateries where FHRSID=" . $fhrs_id ." ";
        $result = DB::select( DB::raw($sql));
        return  $result;
        
    }

	function v1_gettop10hotels($latitude,$longitude)
	{
		$sql  = "select * from eateries LIMIT 10";
		$result = DB::select( DB::raw($sql));
		return $result;
	}

    function v1_addclickbeforeassociated($id)
    {
        DB::update('UPDATE eateries SET ClicksBeforeAssociated = IFNULL(ClicksBeforeAssociated,0) + 1 WHERE ID =  ?', [$id]);
    }

    function v1_addclickafterassociated($id)
    {
        DB::update('UPDATE eateries SET ClicksAfterAssociated = IFNULL(ClicksAfterAssociated,0) + 1 WHERE ID =  ?', [$id]);
    }

    // function v1_getclicksbeforeassociated($id)
    // {
    //     $sql = "select ClicksBeforeAssociated from eateries where id=".$id." and IFNULL(IsAssociated,0) = 0";
    //     $result = DB::select( DB::raw($sql));
    //     return $result;        
    // }

    // function v1_getclicksafterassociated($id)
    // {
    //     $sql  = "select ClicksAfterAssociated from eateries where id=".$id." and IFNULL(IsAssociated,0) = 1";
    //     $result = DB::select( DB::raw($sql));
    //     return $result;
    // }

    function v1_gettop5eateriesBeforeAssociated()
    {
        $result  = DB::table('eateries')
                ->select(DB::raw('BusinessName,ClicksBeforeAssociated'))
                ->orwhereNull('IsAssociated')
                ->Where('IsAssociated', '=', 0)
                ->Where('ClicksBeforeAssociated', '>', 0)
                ->orderby('ClicksBeforeAssociated','DESC')
                ->LIMIT(5)
                ->get();
        return $result;
    }

    function v1_gettop5eateriesAfterAssociated()
    {
       $result  = DB::table('eateries')
                ->select(DB::raw('BusinessName,ClicksAfterAssociated'))
                ->Where('IsAssociated', '=', 1)               
                ->orderby('ClicksAfterAssociated','DESC')
                ->LIMIT(5)
                ->get();
        return $result;
    }
?>