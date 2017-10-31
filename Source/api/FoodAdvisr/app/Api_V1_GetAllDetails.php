<?php
use App\Defaults;
use App\Eateries;
use App\UserFavouriteEateries;
use App\User;
use App\Feedback;
use App\Dishes;
use App\Menu;
use App\MenuSection;
use App\MenuSubSection;
use App\AppUsersDelete;
use Carbon\Carbon;
use App\AppCustomers;

ini_set('memory_limit', '5048M');
ini_set('max_execution_time', 5000);

	function v1_geteateries($latitude,$longitude,$cuisines_ids,$lifestyle_choices_ids)
	{
		 $defaults = Defaults::all();    
         $search_radius = $defaults[0]->search_radius;
         $search_result_limit = $defaults[0]->search_result_limit;
         $associated_sql  = "select id, fhrsid, business_name, address, logo_path, is_associated , longitude, latitude,clicks_after_associated, foodadvisr_overall_rating,cuisines_ids,lifestyle_choices_ids,
                    round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) 
                    +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) 
                    * pi()/180 / 2), 2) ))),2) as distance
                    from eateries where id  in (select id from eateries
                    where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) +
                    COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ) and is_associated = 1 ";
        if(isset($cuisines_ids) && !empty($cuisines_ids) && $cuisines_ids != NULL){
            $associated_sql  .= " and cuisines_ids in(".$cuisines_ids.")";
        }
        if(isset($lifestyle_choices_ids) && !empty($lifestyle_choices_ids) && $lifestyle_choices_ids != NULL){
            $associated_sql  .= " and lifestyle_choices_ids in(".$lifestyle_choices_ids.")";
        }
        $associated_sql  .= "  order by distance asc
                    limit $search_result_limit";
         $eateries_associated = DB::select( DB::raw($associated_sql));

        $unassociated_sql  = "select id, fhrsid, business_name, address, logo_path, is_associated, longitude, latitude,clicks_after_associated, foodadvisr_overall_rating,cuisines_ids,lifestyle_choices_ids,
                    round((6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2)
                    +  COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude)
                    * pi()/180 / 2), 2) ))),2) as distance
                    from eateries where id  in (select id from eateries
                    where (6371*0.621371 * 2 * ASIN(SQRT( POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2),2) +
                    COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ) and ifnull(is_associated,0) = 0  order by distance asc
                    limit 5";

        $eateries_unassociated = DB::select( DB::raw($unassociated_sql));

        $eateries = array_merge($eateries_associated,$eateries_unassociated);

        return $eateries;
	}

    function v1_geteaterydetailsbyid($id)
    {
        $result = Eateries::find($id);
        if($result <> null)
        {
            $result->distance = getDistanceById($id);
            $result->media = getImagesById($id);
            $result->menutypes = getmenutypes($id);
            $result->sectiontypes = getmenusections($id);
            $result->subsectiontypes = getmenusubsections($id);
            $result->menu = geteaterymenu($id);
        }
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

    function v1_addclickbeforeassociated($id)
    {
        DB::update('UPDATE eateries SET clicks_before_associated = IFNULL(clicks_before_associated,0) + 1 WHERE ID =  ?', [$id]);
    }

    function v1_addclickafterassociated($id)
    {
        DB::update('UPDATE eateries SET clicks_after_associated = IFNULL(clicks_after_associated,0) + 1 WHERE ID =  ?', [$id]);
    }

    function v1_gettop5eateriesBeforeAssociated()
    {
        $result  = DB::table('eateries')
                ->select(DB::raw('business_name,clicks_before_associated'))
                ->orwhereNull('is_associated')
                ->Where('is_associated', '=', 0)
                ->Where('clicks_before_associated', '>', 0)
                ->orderby('clicks_before_associated','DESC')
                ->LIMIT(5)
                ->get();
        return $result;
    }

    function v1_gettop5eateriesAfterAssociated()
    {
       $result  = DB::table('eateries')
                ->select(DB::raw('business_name,clicks_after_associated'))
                ->Where('is_associated', '=', 1)
                ->orderby('clicks_after_associated','DESC')
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
        $user_count = AppCustomers::where('id',$userid)->count();
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
        $user_count = AppCustomers::where('id',$userid)->count();
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
           $result = DB::table('user_favourite_eateries')->where('userid', $userid)->where('eatery_id',$eatery_id)->delete();
            return 'Removed Favourite Eatery Successfully';
        }
    }

    function v1_getfavouriteeateries($userid)
    {
        $sql  = "select eatery_id from user_favourite_eateries where userid='". $userid."'";
        $result = DB::select( DB::raw($sql));
        return $result;
    }

    function v1_removefavouriteeateries($userid,$eatery_id)
    {
     $user_remove_count = AppUsersDelete::where('userid',$userid)
        ->where('eatery_id',$eatery_id)->count();
      if($user_remove_count == 0)
      {
       $removefavouriteeateries = new AppUsersDelete();
       $removefavouriteeateries->userid = $userid; 
       $removefavouriteeateries->eatery_id = $eatery_id;
       $removefavouriteeateries->deleted_on =  Carbon::now(new DateTimeZone('Europe/London'));
       $removefavouriteeateries->save();
       return 'Removed Favourite Eatery Successfully';
       }
       else 
        return 'Already removed';

    }

    function getmenutypes($id)
    {
        $sql = 'select ifnull(menus_ids,"0") as menus_ids from dishes where eatery_id=' . $id . ' and is_visible=1';
        $sql_result = DB::select(DB::raw($sql));
        $menu_result=array();
        $menu_id=array();
        if($sql_result != 0){
            foreach ($sql_result as $menus) {
                $menu_ids = $menus->menus_ids;
                $menu_id[] = $menu_ids;
            }
            $menu_ids = implode(',', $menu_id);
            $menu_idss = array_unique(explode(",", $menu_ids));
            $i = 0;
            foreach ($menu_idss as $menus_id) {
                $menu_group_result = DB::table('menu')
                    ->where('ref', '=', $menus_id)
                    ->select(DB::raw('ref as id,menu as menu_name'))
                    ->get();
                foreach ($menu_group_result as $result) {
                    $menu_result[$i]['menu_id'] = $result->id;
                    $menu_result[$i]['menu_name'] = $result->menu_name;
                }
                $i++;
            }
        }else{
            $menu_result = array();
        }

        return $menu_result;
    }

    function getmenusections($id)
    {
        $sql = 'select ifnull(sections_ids,"0") as sections_ids from dishes where eatery_id=' . $id . ' and is_visible=1';
        $sql_result = DB::select(DB::raw($sql));
        $section_result = array();
        $section_id=array();
        $temp_id = 0;
        if($sql_result != 0){
            foreach ($sql_result as $section) {
                $section_ids = $section->sections_ids;
                $section_id[] = $section_ids;
            }
            $section_ids = implode(',', $section_id);
            $sectionsids = array_unique(explode(',', $section_ids));
            $i = 0;

            foreach ($sectionsids as $section_detail) {
                $section_group_result = DB::table('menu_section')
                    ->where('id', '=', $section_detail)
                    ->select(DB::raw('id,menu_id,section_name'))
                    ->get();

                foreach ($section_group_result as $result) {
                    $section_result[$i]['section_id'] = $result->id;
                    $section_result[$i]['menu_id'] = $result->menu_id;
                    $section_result[$i]['section_name'] = $result->section_name;
                }
                $i++;
            }
        }else{
            $section_result = array();
        }

        return $section_result;
    }

    function getmenusubsections($id)
    {
        $sql = 'select ifnull(subsections_ids,"0") as subsections_ids from dishes where eatery_id=' . $id . ' and is_visible=1';
        $sql_result = DB::select(DB::raw($sql));
        $subsection_result = array();
        $subsection_id=array();
        if($sql_result != 0){
            $temp_id = 0;
            foreach ($sql_result as $subsection) {
                $subsection_ids = $subsection->subsections_ids;
                $subsection_id[] = $subsection_ids;
            }
            $subsection_ids = implode(',', $subsection_id);
            $subsectionsids = array_unique(explode(',', $subsection_ids));
            $i = 0;

            foreach ($subsectionsids as $subsection_detail) {
                $subsection_group_result = DB::table('menu_sub_section')
                    ->where('id', '=', $subsection_detail)
                    ->select(DB::raw('id,section_id,sub_section_name'))
                    ->get();

                foreach ($subsection_group_result as $result) {
                    $subsection_result[$i]['subsection_id'] = $result->id;
                    $subsection_result[$i]['section_id'] = $result->section_id;
                    $subsection_result[$i]['subsection_name'] = $result->sub_section_name;
                }
                $i++;
            }
        }
        else{
            $subsection_result = array();
        }

        return $subsection_result;

    }
    function geteaterymenu($id)
    {
        $menu = DB::table('dishes')
            ->join('eateries', 'dishes.eatery_id', '=', 'eateries.id')
            ->where('dishes.eatery_id', '=', $id)
            ->where('dishes.is_visible', '=', '1')
            ->select(DB::raw('eateries.business_name,dishes.id as dish_id,dishes.dish_name,dishes.description,dishes.img_url,dishes.cuisines_ids,dishes.lifestyle_choices_ids,dishes.allergens_contain_ids,dishes.ingredients_ids,dishes.default_price,dishes.menus_ids,dishes.sections_ids,dishes.subsections_ids'))
            ->get();


        return $menu;

    }

    function v1_addfeedbackeateries($feedback)
    {
        $userid = $feedback['userid'];
        $eatery_id = $feedback['eatery_id'];
        $email = $feedback['email'];
        $message = $feedback['message'];
        $msgdate = $feedback['msgdate'];
        $response = $feedback['response'];
        $rating = $feedback['rating'];
        $resptime = $feedback['resptime'];
        $version = $feedback['version'];
        $device = $feedback['device'];
        $os = $feedback['os'];
        $osversion = $feedback['osversion'];
        $model = $feedback['model'];
        $maker = $feedback['maker'];
        $user_count = AppCustomers::where('id',$userid)->count();
        if($user_count == 0)
            return -2002;
        $eatery_count = Eateries::where('id',$eatery_id)->count();
        if($eatery_count == 0)
            return -2001;

        if(isset($feedback) && !empty($feedback))
        {
            $feedback = new Feedback();
            $feedback->userid = $userid;
            $feedback->eatery_id = $eatery_id;
            $feedback->email = $email;
            $feedback->message = $message;
            $feedback->msgdate = $msgdate;
            $feedback->response = $response;
            $feedback->rating = $rating;
            $feedback->resptime = $resptime;
            $feedback->version = $version;
            $feedback->device = $device;
            $feedback->os = $os;
            $feedback->osversion = $osversion;
            $feedback->model = $model;
            $feedback->maker = $maker;
            $feedback->save();
            return 'Added Eatery Feedback Successfully';
        }
        else
            return -2003;
    }


?>