<?php
use App\Defaults;
use App\Eateries;
ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

	function v1_geteateries($latitude,$longitude)
	{
		 $defaults = Defaults::all();    
         $search_radius = $defaults[0]->search_radius;
         $search_result_limit = $defaults[0]->search_result_limit;
    	 $sql  = "select id
            from eateries where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ";
        $result = DB::select( DB::raw($sql));
        $hotelsids = [];
         foreach($result as $result1)
    	{
        	$hotelsids[] = $result1->id; 
    	}
    	
    	$hotels = DB::table('eateries')
    		    ->select(DB::raw('eateries.*,round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(("'.$latitude.'" - abs(latitude)) * pi()/180 / 2),2) +  COS("'.$latitude.'" * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(("'.$longitude.'" - longitude) * pi()/180 / 2), 2) ))),2) as distance'))
    		   ->wherein('FHRSID',$hotelsids)
               ->orderby('distance','asc')
               ->limit($search_result_limit)
    		   ->get();

    	return $hotels;
	}

    function v1_geteaterydetailsbyid($id)
    {
        $result = Eateries::find($id);
        if($result <> null)
            $result->media = getImagesById($id);
        return $result;     
    }

    function getImagesById($id)
    {
         $images_array = array();
         $sql  = "select * from eateriesmedia where eateriesmedia.eatery_id=" . $id ." ";
         $image_result = DB::select( DB::raw($sql));
         $mediaDir = env('CONTENT_EATERY_IMAGE_PATH') . '/'. $id . '/';
         $image_index = 0;
         foreach ($image_result as $image) {
            $images_array['images'][$image_index]['media_name'] = $mediaDir . $image->media_name ;
            $image_index++;
         }
         return $images_array;
    }

	function v1_gettop10eateries($latitude,$longitude)
	{
		$sql  = 'select *,round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(("'.$latitude.'" - abs(latitude)) * pi()/180 / 2),2) +  COS("'.$latitude.'" * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(("'.$longitude.'" - longitude) * pi()/180 / 2), 2) ))),2) as distance from eateries  ORDER BY IFNULL(IsAssociated,0) DESC LIMIT 10';
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