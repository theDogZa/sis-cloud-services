<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\ApiUser;
use App\Models\UsersMap;

class ChkApiUsersController extends Controller
{
  /**
   * Instantiate a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
    //$this->middleware('RolePermission');
    Cache::flush();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: text/html');

  }

  public function checkUsername(Request $request)
  {
    $response = (object)[];
    $chk = false;
    $user = ApiUser::select('id')->where('username', $request->username)->first();

    if (!isset($user->id)) {
      $chk = true;
    }
    $response->code = 200;
    $response->message = $chk;

    return base64_encode(json_encode((array)$response));
  }

  public function checkPassword(Request $request)
  {
   
    $response = (object)[];
    $chk = false;
    $user = ApiUser::select('id','password')
    ->where('username', $request->username)
    //->where('password', Crypt::encrypt($request->password))
    ->first();

    if (isset($user->id) && (Crypt::decrypt($user->password) == $request->password)) {

      $chk = true;
    }
    
    $response->code = 200;
    $response->message = $chk;

    return base64_encode(json_encode((array)$response));
  }

}

/** 
 * CRUD Laravel
 * Master ฺBY Kepex  =>  https://github.com/kEpEx/laravel-crud-generator
 * Modify/Update BY PRASONG PUTICHANCHAI
 * 
 * Latest Update : 09/09/2020 10:32
 * Version : ver.1.00.00
 *
 * File Create : 2021-03-08 09:54:49 *
 */