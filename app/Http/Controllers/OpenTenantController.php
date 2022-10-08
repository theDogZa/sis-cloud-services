<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use App\Models\ApiConfig;
use App\Models\UsersMap;

class OpenTenantController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('RolePermission');
        $this->middleware(function ($request, $next) {
            $this->addLogSys($request);
            return $next($request);
        });
       
        Cache::flush();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: text/html');
        ini_set('max_execution_time', 300);
    }

    public function index(Request $request)
    {
        $compact = (object)[];
        $compact->collection = config('cloudapi.api');

        $userApi = UsersMap::select('api_users.username', 'api_users.password')
        ->leftJoin('api_users', 'users_map.api_user_id', '=', 'api_users.id')
        ->where('users_map.user_id', Auth::id())
        ->where('api_users.active', true)
        ->first();

        $config = ApiConfig::select('val')->where('code', 'UNLI')->first();

        $auth_code = "";
        if (@$userApi->username && $userApi->password) {
            // $code = $input->username. "@system:". $input->password;
            $code = $userApi->username . $config->val . Crypt::decrypt($userApi->password);
            $auth_code = base64_encode($code);
        }
        $compact->code = $auth_code;
        
        return view('_apiCloud.form', (array) $compact);
    }
}
