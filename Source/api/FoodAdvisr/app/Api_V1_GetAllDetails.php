<?php
use App\Defaults;
use App\Eateries;
use App\UserFavouriteEateries;
use App\User;
use App\Dishes;
use App\Menu;
use App\MenuSection;
use App\MenuSubSection;

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
            $result->menutypes = getmenutypes($id);
            $result->sectiontypes = getmenusections($id);
            $result->subsectiontypes = getmenusubsections($id);
            $result->menu = geteaterymenu2($id);
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

function getmenutypes($id)
{
    $sql = 'select ifnull(menus_ids,"0") as menus_ids from dishes where eatery_id=' . $id . ' and is_visible=1';
    $sql_result = DB::select(DB::raw($sql));
    $menu_result=array();
    $menu_id='';
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
                ->where('id', '=', $menus_id)
                ->select(DB::raw('id,menu_name'))
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
    $section_id='';
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
                ->select(DB::raw('id,section_name'))
                ->get();

            foreach ($section_group_result as $result) {
                $section_result[$i]['section_id'] = $result->id;
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
    $subsection_id='';
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
                ->select(DB::raw('id,sub_section_name'))
                ->get();

            foreach ($subsection_group_result as $result) {
                $subsection_result[$i]['subsection_id'] = $result->id;
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
        ->join('menu', 'dishes.menus_ids', '=', 'menu.id')
        ->join('menu_section', 'dishes.sections_ids', '=', 'menu_section.id')
        ->leftjoin('menu_sub_section', 'dishes.subsections_ids', '=', 'menu_sub_section.id')
        ->join('cuisines', 'dishes.cuisines_ids', '=', 'cuisines.id')
        ->leftjoin('allergen_types', 'dishes.allergens_contain_ids', '=', 'allergen_types.id')
        ->join('eateries', 'dishes.eatery_id', '=', 'eateries.id')
        ->where('dishes.eatery_id', '=', $id)
        ->where('dishes.is_visible', '=', '1')
        ->where('menu_section.is_visible', '=', '1')
        /*->groupBy('menu.id,menu_section.id,menu_sub_section.id')
        ->orderBy('menu.display_order,menu_section.display_order,menu_sub_section.display_order')*/
        ->select(DB::raw('eateries.BusinessName,menu.menu_name,menu_section.section_name,menu_sub_section.sub_section_name,dishes.id as dish_id,dishes.dish_name,dishes.description,dishes.img_url,dishes.cuisines_ids,dishes.lifestyle_choices_ids,dishes.allergens_contain_ids,dishes.ingredients_ids,dishes.default_price'))
        ->get();

    foreach($menu as $dish_cuisine)
    {
        $menu_cuisines[$dish_cuisine->dish_id] = $dish_cuisine->cuisines_ids;
        $menu_cuisine_explode[$dish_cuisine->dish_id] = explode(',',$menu_cuisines[$dish_cuisine->dish_id]);
        foreach($menu_cuisine_explode[$dish_cuisine->dish_id] as $cus)
        {
            $cuisine_details[$dish_cuisine->dish_id][$cus] = DB::table('cuisines')
                ->where('id','=',$cus)
                ->select(DB::raw('cuisine_name'))
                ->get();
            /*$cuisine_details[$cus] = DB::table('cuisines')
                ->where('id','=',$cus)
                ->select(DB::raw('cuisine_name'))
                ->get();*/
        }
    }

    foreach($menu as $dish_life_style)
    {
        $life_style[$dish_life_style->dish_id] = $dish_life_style->lifestyle_choices_ids;
        $life_style_explode[$dish_life_style->dish_id] = explode(',',$life_style[$dish_life_style->dish_id]);
        foreach($life_style_explode[$dish_life_style->dish_id] as $cus)
        {
            $life_style_details[$dish_life_style->dish_id][$cus] = DB::table('lifestyle_choices')
                ->where('id','=',$cus)
                ->select(DB::raw('description'))
                ->get();
            /*$life_style_details[$cus] = DB::table('lifestyle_choices')
                ->where('id','=',$cus)
                ->select(DB::raw('description'))
                ->get();*/
        }
    }

    foreach($menu as $dish_allergens_contain_ids)
    {
        $allergens_contain[$dish_allergens_contain_ids->dish_id] = $dish_allergens_contain_ids->allergens_contain_ids;
        $allergens_contain_explode[$dish_allergens_contain_ids->dish_id] = explode(',',$allergens_contain[$dish_allergens_contain_ids->dish_id]);
        foreach($allergens_contain_explode[$dish_allergens_contain_ids->dish_id] as $cus)
        {
            $allergens_contain_details[$dish_allergens_contain_ids->dish_id][$cus] = DB::table('allergen_types')
                ->where('id','=',$cus)
                ->select(DB::raw('allergen_type'))
                ->get();
            /*$allergens_contain_details[$cus] = DB::table('allergen_types')
                ->where('id','=',$cus)
                ->select(DB::raw('allergen_type'))
                ->get();*/
        }
    }

    foreach($menu as $val){
        $dish_details['BusinessName'] = $val->BusinessName;
        $dish_details['menu_name'] = $val->menu_name;
        $dish_details['section_name'] = $val->section_name;
        $dish_details['sub_section_name'] = $val->sub_section_name;
        $dish_details['dish_id'] = $val->dish_id;
        $dish_details['dish_name'] = $val->dish_name;
        $dish_details['description'] = $val->description;
        $dish_details['img_url'] = $val->img_url;
        $dish_details['ingredients_ids'] = $val->ingredients_ids;
        $dish_details['default_price'] = $val->default_price;
    }

    $dish_details['cuisine_details'] = $cuisine_details;
    $dish_details['lifestyle_choise'] = $life_style_details;
    $dish_details['allergens'] = $allergens_contain_details;



    /*
    foreach($menu as $dish_ingredients_ids){
        $menu_ingredients_ids = $dish_ingredients_ids->ingredients_ids;
        $menu_ingredients_ids[]=$menu_ingredients_ids;
    }*/


    return $dish_details;
    return $menu;

}
function geteaterymenu2($id)
{
    $menu = DB::table('dishes')
        ->join('eateries', 'dishes.eatery_id', '=', 'eateries.id')
        ->where('dishes.eatery_id', '=', $id)
        ->where('dishes.is_visible', '=', '1')
        ->select(DB::raw('eateries.BusinessName,dishes.id as dish_id,dishes.dish_name,dishes.description,dishes.img_url,dishes.cuisines_ids,dishes.lifestyle_choices_ids,dishes.allergens_contain_ids,dishes.ingredients_ids,dishes.default_price,dishes.menus_ids,dishes.sections_ids,dishes.subsections_ids'))
        ->get();


    return $menu;

}
?>