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

    function v1_addclickbeforeassociated($fhrs_id)
    {
        $sql  = "select count(*) as before_associated_count from eateries where FHRSID=".$fhrs_id." and IsAssociated IS NULL or  IsAssociated = 0";
        $result = DB::select( DB::raw($sql));

         $sql1  = "select ClicksBeforeAssociated from eateries where FHRSID=".$fhrs_id." and IsAssociated IS NULL or  IsAssociated = 0";
        $result1 = DB::select( DB::raw($sql1));

        if($result[0]->before_associated_count == 1)
        {
            $i = $result1[0]->ClicksBeforeAssociated;
            $i += 1;
             Eateries::where('FHRSID','=',$fhrs_id)
            ->update(array('ClicksBeforeAssociated'=> $i
                 ));
            return $i;
        }
        
    }

    function v1_addclickafterassociated($fhrs_id)
    {
        $sql  = "select count(*) as after_associated_count from eateries where FHRSID=".$fhrs_id." and IsAssociated = 1";
        $result = DB::select( DB::raw($sql));
        
         $sql1  = "select ClicksAfterAssociated from eateries where FHRSID=".$fhrs_id." and IsAssociated = 1";
        $result1 = DB::select( DB::raw($sql1));

        if($result[0]->after_associated_count == 1)
        {
            $i = $result1[0]->ClicksAfterAssociated;
            $i += 1;
             Eateries::where('FHRSID','=',$fhrs_id)
            ->update(array('ClicksAfterAssociated'=> $i
                 ));
            return $i;
        }
        
    }

    function v1_getclicksbeforeassociated($fhrs_id)
    {
        $sql = "select ClicksBeforeAssociated from eateries where FHRSID=".$fhrs_id." and IsAssociated IS NULL or  IsAssociated = 0";
        $result = DB::select( DB::raw($sql));
        return $result;        
    }

    function v1_getclicksafterassociated($fhrs_id)
    {
        $sql  = "select ClicksAfterAssociated from eateries where FHRSID=".$fhrs_id." and IsAssociated = 1";
        $result = DB::select( DB::raw($sql));
        return $result;
    }

    function v1_gettop5eateriesBeforeAssociated()
    {
        $result  = DB::table('eateries')
                ->select(DB::raw('BusinessName,ClicksBeforeAssociated'))
                ->whereNull('IsAssociated')
                ->orWhere('IsAssociated', '=', 0)
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