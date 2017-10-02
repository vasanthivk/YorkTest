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
        $result['hotel'] = DB::select( DB::raw($sql));
        $menu_details['categories'] = v1_gethotelbyitemdetails($fhrs_id);
        $json_string = json_decode(json_encode($result + $menu_details));
        return  $json_string;
        
    }

	function v1_gettop10hotels($latitude,$longitude)
	{
		$sql  = "select * from establishment LIMIT 10";
		$result = DB::select( DB::raw($sql));
		return $result;
	}

    function v1_gethotelbyitemdetails($hotel_id)
    {
        $most_loved_array = array();
        $categories_query = 'select cat.category_id ,cat.category_name
        from category as cat
        inner join items as items
        on cat.category_id = items.category_id
        where items.FHRSID = "'.$hotel_id.'"  and items.is_visible = 1       
        order by items.display_order;';
        return 
        $categories_result = app('db')->select($categories_query);
        $x = 0;
        foreach ($categories_result as $result) {
            $most_loved_array[$x]['category_id'] = $result->category_id;
            $most_loved_array[$x]['category_name'] = $result->category_name;
            $x++;
        }

        return $most_loved_array;
    }
?>