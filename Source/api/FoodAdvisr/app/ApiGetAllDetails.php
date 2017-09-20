<?php

	function gethoteldetailsbyid($fhrs_id)
	{
		 $sql  = "select * from establishment where FHRSID=" . $fhrs_id ." ";
    	$result = DB::select( DB::raw($sql));
    	return $result;
	}

	function gethotels($latitude,$longitude)
	{
		$search_radius = 4000;
    	 $sql  = "select FHRSID from establishment where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(Latitude)) * pi()/180 / 2),2) + 
                COS($latitude * pi()/180 ) * COS(abs(Latitude) * pi()/180) * POWER(SIN(($longitude - Longitude) * pi()/180 / 2), 2) )) 
                <= $search_radius) limit 99";
        $result = DB::select( DB::raw($sql));
        $hotelsids = [];
         foreach($result as $result1)
    	{
        	$hotelsids[] = $result1->FHRSID; 
    	}
    	
    	$hotels = DB::table('establishment')
    		    ->select(DB::raw('establishment.*,(((acos(sin(("'.$latitude.'"*pi()/180)) *
            sin((`latitude`*pi()/180))+cos(("'.$latitude.'"*pi()/180)) *
            cos((`latitude`*pi()/180)) * cos((("'.$longitude.'"- `longitude`)*
            pi()/180))))*180/pi())*60*1.1515
        ) as distance'))
    		   ->wherein('FHRSID',$hotelsids)
    		   ->get();    	
    	return $hotels;
	}
?>