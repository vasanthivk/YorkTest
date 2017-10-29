<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Redirect;
use Input;
use DB;
use Session;
use DateTimeZone;


class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
       return View::make('login.login');
    }
    public function validateuser(Request $request)
    {
        $input = Input::all();
        $this->validate($request, [
            //'email'  => 'required|string','password'=>'required'
        ]);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return View::make('login.login')
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors.');


        }
        $logindetails = DB::table('person')
            ->where('person.email','=', Input::get("login"))
            ->where('person.password','=', Input::get("password"))
            ->get();

        $login = DB::table('person')
            ->where('person.email','=', Input::get("login"))
            ->where('person.password','=', Input::get("password"))
            ->count();

        $loginerr = DB::table('person')
            ->where('person.email','=', Input::get("login"))
            ->count();

        if($loginerr > 0)
        {

            $passerr = DB::table('person')
                ->where('person.email','=', Input::get("login"))
                ->where('person.password','=', Input::get("password"))
                ->count();

            if($passerr > 0)
            {

                $allowerr = DB::table('person')
                    ->where('person.email','=', Input::get("login"))
                    ->where('person.password','=', Input::get("password"))
                    ->whereNotIn('person.roles',[5])
                    ->count();

                if($allowerr > 0)
                {

                    if ($login == 1)
                    {


                        if($logindetails[0]->roles ==1)
                        {
                            Session::put("user_id",$logindetails[0]->ref);
                            Session::put("role_id",$logindetails[0]->roles);
                            Session::put("name",$logindetails[0]->firstnames);
                            Session::put("eatery_id",'');
                            Session::put("mobile_no",$logindetails[0]->mobileno);

                            return Redirect::route('dashboard.index');
                        }
                        else {
                            return \Redirect::back()->withErrors( 'In active user')
                                ->withInput();
                        }
                    }
                    else
                    {
                        return \Redirect::back()->withErrors( 'Invalid User details')
                            ->withInput();
                    }
                }
                else{
                    return \Redirect::back()->withErrors( 'Not allowed user')
                        ->withInput();
                } }
            else{
                return \Redirect::back()->withErrors( 'Please Enter Correct Password')
                    ->withInput();
            } }
        else{
            return \Redirect::back()->withErrors( 'Please Enter Correct Login')
                ->withInput();
        }

    }
    
    public function validateuser2(Request $request)
    {
       $input = Input::all();
         $this->validate($request, [
            'login'  => 'required|string','password'=>'required'
        ]);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            return View::make('login.login')
            ->withInput()
            ->withErrors($validator)
            ->with('errors', 'There were validation errors.');


        }
            $logindetails = DB::table('user')
            ->where('user.login','=', Input::get("login"))
            ->where('user.password','=', Input::get("password"))       
            ->get();  
                                                 
            $login = DB::table('user')
            ->where('user.login','=', Input::get("login"))
            ->where('user.password','=', Input::get("password"))           
            ->count();

           $loginerr = DB::table('user')
            ->where('user.login','=', Input::get("login"))
            ->count();

    if($loginerr > 0)
    {
          
        $passerr = DB::table('user')
            ->where('user.login','=', Input::get("login"))
            ->where('user.password','=', Input::get("password"))
            ->count();

    if($passerr > 0)
    {

            $allowerr = DB::table('user')
            ->where('user.login','=', Input::get("login"))
            ->where('user.password','=', Input::get("password"))
            ->whereNotIn('user.role_id',[5])
            ->count();

            if($allowerr > 0)
            {

            if ($login == 1)
            {

                
              if($logindetails[0]->status ==1)
                {
                    Session::put("user_id",$logindetails[0]->id);
                    Session::put("role_id",$logindetails[0]->role_id);
                    Session::put("name",$logindetails[0]->name);
                    Session::put("eatery_id",$logindetails[0]->eatery_id);
                    Session::put("mobile_no",$logindetails[0]->mobile_no);                   
                
                    return Redirect::route('dashboard.index');
                }
                else {
                 return \Redirect::back()->withErrors( 'In active user') 
                 ->withInput();
                 }
            }         
         else 
            {
                return \Redirect::back()->withErrors( 'Invalid User details')
                ->withInput();            
            }
            }
             else{
                return \Redirect::back()->withErrors( 'Not allowed user')
                ->withInput();
            } }
            else{
                return \Redirect::back()->withErrors( 'Please Enter Correct Password')
                ->withInput();
            } }
            else{
                return \Redirect::back()->withErrors( 'Please Enter Correct Login')
                ->withInput();
            }
           
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgot(Request $request)
    {
        $input = Input::all();
         $this->validate($request, [
            'login'  => 'required|string','password'=>'required'
        ]);
        $rules = array('');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            return View::make('login.forgot')
            ->withInput()
            ->withErrors($validator)
            ->with('errors', 'There were validation errors.');
        }
        $login = DB::table('user')
            ->where('user.login','=', Input::get("login"))           
            ->count();
        if($login == 1)
        {
           DB::table('user')
            ->where('login', Input::get("login"))
            ->update(['password' => Input::get("password")]);
           return redirect('/login')->with('success','Updated Password Successfully!');
        }
        else{
             return \Redirect::back()->withErrors( 'Invalid Login')
                ->withInput();  
        }   
    }
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
