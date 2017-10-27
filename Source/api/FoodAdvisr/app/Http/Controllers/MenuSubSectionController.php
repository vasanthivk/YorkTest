<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Input;
use App\Menu;
use App\MenuSection;
use App\MenuSubSection;
use App\Log;
use Carbon\Carbon;
use DateTimeZone;
use File;
use Image;

class MenuSubSectionController extends Controller
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
        return view('menu.index');
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

    private function saveLogoInTempLocation($file)
    {
        $session_id = Session::getId();
        $tempdestinationPath = env('CONTENT_ITEM_GROUP_TEMP_PATH');
        $extension = $file->getClientOriginalExtension();
        $filename = $session_id . '.' . $extension;
        $upload_success = $file->move($tempdestinationPath, $filename);
        return $extension;
    }

    private function saveLogoInLogoPath($itemgropuid, $extension)
    {
        $session_id = Session::getId();
        $sourceDir = env('CONTENT_ITEM_GROUP_TEMP_PATH');
        $destinationDir = env('CONTENT_ITEM_GROUP_PATH');
        $success = File::copy($sourceDir . '//' . $session_id . '.' .  $extension, $destinationDir . '//' . $itemgropuid . '.' .  $extension);
        try {
            $success = File::delete($sourceDir . '//' . $session_id . '.' .  $extension);
        } catch (Exception $e) {
        }

        createThumbnailImage($destinationDir,$itemgropuid,$extension);
    }

    private function deleteLogo($itemgropuid, $extension)
    {
        $sourceDir = env('CONTENT_ITEM_GROUP_PATH');
        try {
            $success = File::delete($sourceDir . '//' . $itemgropuid . '.' .  $extension);
        } catch (Exception $e) {
        }
        try {
            $success = File::delete($sourceDir . '//' . $itemgropuid . '_t.' .  $extension);
        } catch (Exception $e) {
        }
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
