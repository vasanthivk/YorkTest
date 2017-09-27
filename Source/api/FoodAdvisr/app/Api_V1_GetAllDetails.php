<?php
use App\Defaults;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

	function v1_gethotels($latitude,$longitude)
	{
		 $defaults = Defaults::all();    
         $search_radius = $defaults[0]->search_radius;
         $search_result_limit = $defaults[0]->search_result_limit;
    	 $sql  = "select FHRSID
            from establishment where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ";
        $result = DB::select( DB::raw($sql));
        $hotelsids = [];
         foreach($result as $result1)
    	{
        	$hotelsids[] = $result1->FHRSID; 
    	}
    	
    	$hotels = DB::table('establishment')
    		    ->select(DB::raw('establishment.*,round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(("'.$latitude.'" - abs(latitude)) * pi()/180 / 2),2) +  COS("'.$latitude.'" * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(("'.$longitude.'" - longitude) * pi()/180 / 2), 2) ))),2) as distance'))
    		   ->wherein('FHRSID',$hotelsids)
               ->orderby('distance','asc')
               ->limit($search_result_limit)
    		   ->get();    	
    	return $hotels;
	}

    function v1_gethoteldetailsbyid($fhrs_id)
    {
         $sql  = "select * from establishment where FHRSID=" . $fhrs_id ." ";
        $result = DB::select( DB::raw($sql));
        return $result;
    }

	function v1_gettop10hotels($latitude,$longitude)
	{
		$sql  = "select * from establishment LIMIT 10";
		$result = DB::select( DB::raw($sql));
		return $result;
	}

    function v1_getcategories()
    {
        $states =  DB::table('category')
                    ->select(DB::raw('category.category_id as id,category.category_name'))
                    ->get();
        return $states;
    }	
?>