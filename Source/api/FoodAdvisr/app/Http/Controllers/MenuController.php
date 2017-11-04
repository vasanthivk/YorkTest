<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Menu;
use App\Eateries;
use App\Groups;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use File;
use Image;

class MenuController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getPrivileges()
    {
        $roleid = Session::get("role_id");
        $privileges['View']  = ValidateUserPrivileges($roleid,6,1);  //role, module, privilege
        $privileges['Add']  = ValidateUserPrivileges($roleid,6,2);
        $privileges['Edit']  = ValidateUserPrivileges($roleid,6,3);
        $privileges['Delete']  = ValidateUserPrivileges($roleid,6,4);
        // $privileges['Approve']  = ValidateUserPrivileges(1,7,3);
        // $privileges['Reject']  = ValidateUserPrivileges(1,7,3);

        return $privileges;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        $menus = DB::table('menu')
        ->leftjoin('groups', 'groups.id', '=', 'menu.group_id')
        ->join('eateries', 'eateries.id', '=', 'menu.eatery_id')
        ->select(DB::raw('menu.ref as id,menu.menu,menu.submenu,if(ifnull(menu.is_visible,1)=1,"Visible","InVisible") as status,eateries.business_name,groups.description'))
        ->where('menu.company','=','FoodAdvisr')
        ->get();
        
         return View('menu.index', compact('menus'))         
        ->with('privileges',$privileges);
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        // $eateries = Eateries::all()->pluck('business_name','id');
       if($request['search'])
            $searchvalue = $request['search'];
        else
            $searchvalue='';
        $search = '%' . $searchvalue . '%';
        
        $eateries = searchalleateries($searchvalue);

        $groups = Groups::all();
        return View('menu.create')          
        ->with('privileges',$privileges)
         ->with('eateries',$eateries)
        ->with('searchvalue',$searchvalue)
        ->with('groups',$groups);
    }

    public function searcheateries(Request $request)
   {
     if($request['search'])
            $searchvalue = $request['search'];
        else
            $searchvalue='';
        $search = '%' . $searchvalue . '%';

        if($searchvalue =='')
        {
            $eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.business_type_id', '=', 'eateries.business_type_id')
            ->select(DB::raw('eateries.business_name,businesstype.description as business_type,eateries.id,eateries.logo_extension'))
            ->where('eateries.location_id','=','')
            ->get();
        }
        else{
            
        $eateries = DB::table('eateries')
            ->join('businesstype', 'businesstype.business_type_id', '=', 'eateries.business_type_id')
             ->leftjoin('cuisines', 'cuisines.id', '=', 'eateries.cuisines_ids')
             ->leftjoin('groups', 'groups.id', '=', 'eateries.group_id')
             ->leftjoin('locations', 'locations.location_id', '=', 'eateries.location_id')
             ->where(function ($query) use ($search){
                    $query->where('eateries.business_name', 'like', $search)
                            ->orwhere('eateries.locality', 'like', $search)
                            ->orwhere('groups.description', 'like', $search)
                            ->orwhere('locations.description', 'like', $search);
                })
            ->select(DB::raw('eateries.id,eateries.business_name'))
             ->where('eateries.is_enabled','=',1)
            ->limit(10)
            ->get();
        }

         return View('menu.search')          
        ->with('eateries',$eateries)
        ->with('searchvalue',$searchvalue);

   }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = Input::all();
        
        if ($input['eatery_id'] == 0) {
           return Redirect::back()->with('warning','Please Search Eateries And Select Aleast One Eatery!')
           ->withInput();
        }
        $this->validate($request, [
            'menu'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            $search = Input::get('search');
            return Redirect::route('menu.create',array('search' => $search))
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {   
       
            $menu = new Menu();
            $menu->menu =  Input::get('menu');
            $menu->company =  'FoodAdvisr';
            $menu->eatery_id =  (Input::get('eatery_id')== ''  ? '0' : Input::get('eatery_id'));
            $menu->group_id =  (Input::get('group_id')== ''  ? '0' : Input::get('group_id'));
            $menu->submenu =  'NULL';
            $menu->description =  (Input::get('description')=='' ? ' ' : Input::get('description'));
            $menu->is_visible =  (Input::get('is_visible')== ''  ? '0' : '1');
            $menu->sort_order =  1;
            $menu->save();         

            $log = new Log();
            $log->module_id=6;
            $log->action='create';      
            $log->description='Menu ' . $menu->menu . ' is created';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menu.index')->with('success','Menu Created Successfully!');
        
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
         if ( !Session::has('user_id') || Session::get('user_id') == '' )
            return Redirect::to('/');
        $privileges = $this->getPrivileges();
        if($privileges['Edit'] !='true')
            return Redirect::to('/');        
        $menu = Menu::where('ref','=',$id)->get();       
        $eateries = Eateries::find($menu[0]->eatery_id);
        $groups = Groups::all();
        return View('menu.edit')
        ->with('menu',$menu[0])
        ->with('eateries',$eateries)
        ->with('groups',$groups)
        ->with('privileges',$privileges);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = Input::all();       

        $this->validate($request, [
            'menu'  => 'required']);        
        
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        {
            
            return Redirect::route('menu.edit')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {   
            Menu::where('ref','=',$id)
            ->update(array(
                'menu'=> Input::get('menu'),
                'company'=>'FoodAdvisr',
                'submenu'=> 'NULL',
                'group_id' => (Input::get('group_id')== ''  ? '0' : Input::get('group_id')),
                'description'=> (Input::get('description')=='' ? ' ' : Input::get('description')),
                'is_visible'=> (Input::get('is_visible')== ''  ? '0' : '1'),
                'sort_order'=> 1
            ));

            $log = new Log();
            $log->module_id=6;
            $log->action='update';      
            $log->description='Menu ' . Input::get('menu') . ' is updated';
            $log->created_on=  Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get('user_id'); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
        return Redirect::route('menu.index')->with('success','Menu Updated Successfully!');
        
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu =  Menu::where('ref','=',$id)->get();
        if (is_null($menu))
        {
         return Redirect::back()->with('warning','Menu Details Are Not Found!');
        }
        else
        {
           Menu::where('ref','=',$id)->delete();

            $log = new Log();
            $log->module_id=6;
            $log->action='delete';      
            $log->description='Menu '. $menu[0]->menu . ' is Deleted';
            $log->created_on= Carbon::now(new DateTimeZone('Asia/Kolkata'));
            $log->user_id=Session::get("user_id"); 
            $log->category=1;    
            $log->log_type=1;
            createLog($log);
           return Redirect::back()->with('warning','Menu Deleted Successfully!');
        }
    }
}
