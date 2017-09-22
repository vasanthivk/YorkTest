<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Input;
use Session;
use App\Log;
use Carbon\Carbon;
use App\Defaults;
use DB;
use DateTimeZone;
class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
        $defaults = DB::table('defaults')                
                ->select(DB::raw('defaults.*')) 
                ->where('defaults.id','=',1)          
                ->get();
      
        if (is_null($defaults))
        {
            return Redirect::route('configuration.index')
                ->with('message','There were validation errors');
        }
        else 
         return View::make('configuration.index')
                ->with('defaults',$defaults[0]);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $template_sms_otp = Input::get('template_sms_otp');
        $template_forgotpassword = Input::get('template_forgotpassword');
        
        if(strpos($template_sms_otp,'%OTP%') != true)
        {
            return Redirect::back()->with('success','Please Mention The  %OTP%!')
            ->withInput();
        }
        if(strpos($template_forgotpassword,'%PWD%') != true)
        {
            return Redirect::back()->with('success','Please Mention The %PWD%!')
            ->withInput();
        }
        else
        {
       $rules = array('search_radius'=>'required|numeric','search_result_limit'=>'required|numeric','template_sms_otp'=>'required','template_forgotpassword'=>'required','log_max_days'=>'required|numeric');

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) 
        { 
            return Redirect::route('configuration.index',$id)
                ->withInput()
                ->withErrors($validator)
                ->with('errors', 'There were validation errors');
        }
        else
        {
            $defaults = Defaults::find($id);           
            $defaults->search_radius =  Input::get('search_radius');
            $defaults->search_result_limit =  Input::get('search_result_limit');
            $defaults->template_sms_otp =  Input::get('template_sms_otp');
            $defaults->template_forgotpassword =  Input::get('template_forgotpassword');
            $defaults->allow_create_logs =  (Input::get('allow_create_logs')== ''  ? '0' : '1');
            $defaults->allow_edit_logs =  (Input::get('allow_edit_logs')== ''  ? '0' : '1');
            $defaults->allow_delete_logs =  (Input::get('allow_delete_logs')== ''  ? '0' : '1');
            $defaults->log_max_days =  Input::get('log_max_days');
            $defaults->update();
             return Redirect::back()->with('success','Configuration details are updated successfully!');           
            }
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
        //
    }
}
