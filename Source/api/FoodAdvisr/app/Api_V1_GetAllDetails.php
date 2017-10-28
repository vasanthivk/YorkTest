<?php
use App\Defaults;
use App\Eateries;
use App\UserFavouriteEateries;
use App\User;

ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

	function v1_geteateries($latitude,$longitude,$cuisines_ids,$lifestyle_choices_ids)
	{
		 $defaults = Defaults::all();    
         $search_radius = $defaults[0]->search_radius;
         $search_result_limit = $defaults[0]->search_result_limit;
         $sql  = "select id, FHRSID, BusinessName, Address, LogoPath, IsAssociated, Longitude, Latitude,ClicksAfterAssociated, FoodAdvisrOverallRating,cuisines_ids,lifestyle_choices_ids, 
                    round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) 
                    +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) 
                    * pi()/180 / 2), 2) ))),2) as distance
                    from eateries where id  in (select id from eateries where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) )  order by IsAssociated desc, distance asc
                    limit $search_result_limit";                    
         $eateries = DB::select( DB::raw($sql));              
    	return $eateries;
	}

    function v1_geteaterydetailsbyid($id)
    {
        $result = Eateries::find($id);
        if($result <> null)
            $result->distance = getDistanceById($id);
            $result->media = getImagesById($id);
            // $result->cuisines_ids = getCusinesById($result->cuisines_ids);
            // $result->lifestyle_choices_ids = getLifestyleChoicesById($result->lifestyle_choices_ids);
        return $result;     
    }

    function getDistanceById($id)
    {
       $result = Eateries::find($id);
       $get_distance = DB::table('eateries')
                ->select(DB::raw('round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(("'.$result->Latitude.'" - abs(latitude)) * pi()/180 / 2),2) +  COS("'.$result->Latitude.'" * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(("'.$result->Longitude.'" - longitude) * pi()/180 / 2), 2) ))),2) as distance'))
               ->where('id','=',$id)
               ->get();
       return $get_distance[0]->distance; 
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

    function getCusinesById($ids)
    {
         $sql  = "select * from cuisines where id in(" . $ids ." )";
         $cuisines_result = DB::select( DB::raw($sql));
         return $cuisines_result;
    }

    function getLifestyleChoicesById($ids)
    {
         $sql  = "select * from lifestyle_choices where id in(" . $ids ." )";
         $lifestyle_choices_result = DB::select( DB::raw($sql));
         return $lifestyle_choices_result;
         
    }

	function v1_gettop10eateries($latitude,$longitude)
	{
		$sql  = 'select id, FHRSID, BusinessName, Address, LogoPath, IsAssociated, Longitude, Latitude, FoodAdvisrOverallRating,round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(("'.$latitude.'" - abs(latitude)) * pi()/180 / 2),2) +  COS("'.$latitude.'" * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(("'.$longitude.'" - longitude) * pi()/180 / 2), 2) ))),2) as distance from eateries  ORDER BY id LIMIT 10';
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

    function v1_getcuisines()
    {
        $sql  = 'select * from cuisines where ifnull(is_enabled,0) = 1';
        $result = DB::select( DB::raw($sql));
        return $result;
    }

    function v1_lifestylechoices()
    {
        $sql  = 'select * from lifestyle_choices where ifnull(is_enabled,0) = 1';
        $result = DB::select( DB::raw($sql));
        return $result;
    }

     function v1_getnutritions()
    {
        $sql  = 'select * from nutrition_types where ifnull(is_enabled,0) = 1';
        $result = DB::select( DB::raw($sql));
        return $result;
    }

     function v1_getallergens()
    {
        $sql  = 'select * from allergen_types where ifnull(is_enabled,0) = 1';
        $result = DB::select( DB::raw($sql));
        return $result;
    }

    function v1_addtofavouriteeatery($userid, $eatery_id)
    {
        $user_count = User::where('id',$userid)->count();
        if($user_count == 0)
            return -2002;
        $eatery_count = Eateries::where('id',$eatery_id)->count();
        if($eatery_count == 0)
            return -2001;
            
        $user_insert_count = UserFavouriteEateries::where('userid',$userid)
        ->where('eatery_id',$eatery_id)->count();
        if($user_insert_count == 0)
        {
            $userfavouriteeateries = new UserFavouriteEateries();
            $userfavouriteeateries->userid = $userid; 
            $userfavouriteeateries->eatery_id = $eatery_id;
            $userfavouriteeateries->save();
            return 'Added Favourite Eatery Successfully';
        }
        else
            return -2003;
    }

    function v1_removefromfavouriteeatery($userid, $eatery_id)
    {
        $user_count = User::where('id',$userid)->count();
        if($user_count == 0)
            return -2005;
        $eatery_count = Eateries::where('id',$eatery_id)->count();
        if($eatery_count == 0)
            return -2004;

        $user_remove_count = UserFavouriteEateries::where('userid',$userid)
        ->where('eatery_id',$eatery_id)->count();
        if($user_remove_count == 0)
            return -2006;
        else
        {
            $sql  = "delete from user_favourite_eateries where userid='" . $userid . "' and eatery_id=" . $eatery_id;
            $result = DB::delete( DB::raw($sql,[$userid, $eatery_id]));
            return 'Removed Favourite Eatery Successfully';
        }
    }

    function v1_getfavouriteeateries($userid)
    {
        $sql  = "select * from user_favourite_eateries where userid='". $userid."'";
        $result = DB::select( DB::raw($sql));
        return $result;
    }
?>