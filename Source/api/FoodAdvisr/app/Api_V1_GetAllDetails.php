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
                    COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ) and is_associated = 1 and is_enabled = 1 ";
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
                    COS($latitude * pi()/180 ) * COS(abs(latitude) * pi()/180) * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) ))  <= $search_radius) ) and ifnull(is_associated,0) = 0 and is_enabled = 1 order by distance asc
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
            $result->menus = getmenubygroupid($result->group_id,$id);
            $result->dishes = geteaterydishes($id);
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
                ->where('is_enabled','=', 1)
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
                ->where('is_enabled','=', 1)
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

    function getmenubygroupid($groupid,$eateryid){
        $menu_details = DB::table('menu')
            ->select(DB::raw('ref,menu,description'))
            ->where('group_id','=',$groupid)
            ->orWhere('eatery_id','=',$eateryid)
            ->get();
        $menuDetails = [];

        foreach($menu_details as $menu){
            $menus = new Menu();
            $menus->id = $menu->ref;
            $menus->menu_name = $menu->menu;
            $menus->menu_description = $menu->description;
            $menu_section_details = DB::table('menu_section')
                ->select(DB::raw('id,section_name,description'))
                ->where('menu_id','=',$menu->ref)
                ->get();
            $section_array = [];
            foreach($menu_section_details as $section){
                $menu_section = [];
                $menu_section = new MenuSection();
                $menu_section->section_id = $section->id;
                $menu_section->section_name = $section->section_name;
                $menu_section->description = $section->description;

                $menu_sub_section_details = DB::table('menu_sub_section')
                    ->select(DB::raw('id,sub_section_name,description'))
                    ->where('section_id','=',$section->id)
                    ->get();
                $sub_section_array = [];
                foreach($menu_sub_section_details as $sub_section){
                    $menu_sub_section = new MenuSubSection();
                    $menu_sub_section->sub_section_id = $sub_section->id;
                    $menu_sub_section->sub_section_name = $sub_section->sub_section_name;
                    $menu_sub_section->description = $sub_section->description;

                    $sub_section_array[] = $menu_sub_section;
                }
                $menu_section['sub_sections'] = $sub_section_array ;
                $section_array[] = $menu_section;
            }
            $menus['sections'] = $section_array;
            $menuDetails[] = $menus;
        }

       return $menuDetails;
    }
    function geteaterydishes($eateryid)
    {
        $dish_details = DB::table('dishes')
            ->where('dishes.eatery_id', '=', $eateryid)
            ->where('dishes.is_visible', '=', '1')
            ->select(DB::raw('dishes.id as dish_id,dishes.dish_name,dishes.description,dishes.img_url,dishes.cuisines_ids,dishes.lifestyle_choices_ids,dishes.allergens_contain_ids,dishes.ingredients_ids,dishes.default_price,dishes.menus_ids,dishes.sections_ids,dishes.subsections_ids'))
            ->get();

        $dish_array = [];
        foreach($dish_details as $dishes){
            $dish = new Dishes();
            $dish->dish_id = $dishes->dish_id;
            $dish->dish_name = $dishes->dish_name;
            $dish->description = $dishes->description;
            $dish->img_url = $dishes->img_url;
            $dish->default_price = $dishes->default_price;
            $dish->menus_ids = $dishes->menus_ids;
            $dish->sections_ids = $dishes->sections_ids;
            $dish->subsections_ids = $dishes->subsections_ids;
            $dish_array[] = $dish;
        }


        return $dish_array;

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