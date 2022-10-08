<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Models\UsersMap;
use App\Models\User;
use App\Models\ApiUser;

class UserMapController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('RolePermission');
        // $this->middleware(function ($request, $next) {
        //   $this->addLogSys($request);
        //   return $next($request);
        // });
        Cache::flush();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: text/html');
    }
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listByApiUser(Request $request, $id)
    {
        $compact = (object) array();
        $compact->api_user_id = $id;

        $compact->ApiUser = ApiUser::select('username')->findOrFail($id);

        $Users = User::select('id', 'username', 'first_name', 'last_name')->where('active', true)->orderBy('username', 'ASC');

        if (!$request->user()->hasRole('developer')) {
            $Users->where('id', '!=', 1);
        }

        $compact->collection = $Users->get();

        $compact->UsersMap = UsersMap::where('api_user_id', $id)
        ->where('active', true)
        ->pluck('id', 'user_id')
        ->toArray();

        $compact->UsersMapIsUse = UsersMap::where('api_user_id', '!=', $id)
        ->where('active', true)
        ->pluck('id', 'user_id')
        ->toArray();

       // DD($compact->UsersMap, $compact->UsersMapIsUse);
       // $this->_cLogSys($request, $id);


        return view('_users_map.list_users', (array) $compact);
    }

    public function storeApiUser(Request $request)
    {
        $response = (object) array();
        try {
            DB::beginTransaction();

            $isDelUsersMap = UsersMap::where('api_user_id', $request->api_user_id)->delete();

            foreach ($request->users as $k => $v) {
                $UsersMap = new UsersMap;
                $UsersMap->api_user_id = $request->api_user_id;
                $UsersMap->user_id = $v;
                $UsersMap->active = 1;
                $UsersMap->created_uid = Auth::id();
                $UsersMap->created_at = date("Y-m-d H:i:s");
                $UsersMap->save();
            }

            DB::commit();
            Log::info('Successful: UsersMap:storeApiUser : ', ['api_user_id' => $request->api_user_id]);

            $message = trans('core.message_update_success');
            $status = 'success';
            $title = 'Success';
        } catch (\Exception $e) {

            DB::rollback();
            Log::error('Error: UsersMap:storeApiUser :' . $e->getMessage());

            $message = trans('core.message_update_error');
            $status = 'error';
            $title = 'Error';
        }

        session(['noit_title' => $title]);
        session(['noit_message' => $message]);
        session(['noit_status' => $status]);

        return redirect()->route('api_users.index');
    }
    
}
