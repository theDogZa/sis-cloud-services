<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\ApiConfig;
use App\Models\User;
use App\Models\UsersPermission;
use App\Models\UsersRole;
use App\Models\LogApi;

class UsersController extends Controller
{
  /**
   * Instantiate a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {

    $this->middleware('RolePermission');
    // $this->middleware(function ($request, $next) {
    //   $this->addLogSys($request);
    //   return $next($request);
    // });
    Cache::flush();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: text/html');

    $this->arrShowFieldIndex = [
		 'username' => 1,  'first_name' => 1,  'last_name' => 1,  'email' => 1,  'email_verified_at' => 0,  'password' => 0,  'auth_code' => 0,  'active' => 1,  'activated' => 1,  'remember_token' => 0,  'last_login' => 0, 		];
		$this->arrShowFieldFrom = [
		 'username' => 1,  'first_name' => 1,  'last_name' => 1,  'email' => 1,  'email_verified_at' => 0,  'password' => 1,  'auth_code' => 0,  'active' => 1,  'activated' => 1,  'remember_token' => 0,  'last_login' => 0, 		];
		$this->arrShowFieldView = [
     'username' => 1,  'first_name' => 1,  'last_name' => 1,  'email' => 1,  'email_verified_at' => 0,  'password' => 0,  'auth_code' => 0,  'active' => 1,  'activated' => 1,  'remember_token' => 0,  'last_login' => 1, 		];
  }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
		$rules = [
			'username' => 'required|string|max:255|unique:users',
			'email' => 'required|string|max:255|unique:users',
			 'password' => 'required|string|max:255',
			//#Ex
			//'username' => 'required|string|max:20|unique:users,username,' . $data ['id'],
			//'email' => 'required|string|email|max:255|unique:users,email,' . $data ['id'],
			// 'password' => 'required|string|min:6|confirmed',
			//'password' => 'required|string|min:6',
		];
		
		$messages = [
			'username.required' => trans('User.username_required'),
			'email.required' => trans('User.email_required'),
			// 'password.required' => trans('User.password_required'),
			// 'active.required' => trans('User.active_required'),
			// 'activated.required' => trans('User.activated_required'),
			//#Ex
			//'email.unique'  => 'Email already taken' ,
			//'username.unique'  => 'Username "' . $data['username'] . '" already taken',
			//'email.email' =>'Email type',
		];

		return Validator::make($data,$rules,$messages);
	}

  public function index(Request $request)
  {
    $compact = (object) array();

    $select = $this->_listToSelect($this->arrShowFieldIndex);

    $results = User::select($select);

    if (!$request->user()->hasRole('developer')) {
      $results->where('id', '!=', 1);
    }
    //------ search
    if (count($request->all())) {
      $input = (object) $request->all();
      if(@$input->search){
        $results = $this->_easySearch($results, $input->search);
      }else{
        $results = $this->_advSearch($results, $input);
      }  
    }
 
    $compact->search = (object) $request->all();

    $this->_getDataBelongs($compact);
    //-----

    $compact->arrIsUse = LogApi::select('created_uid', 'request_id')->pluck('request_id', 'created_uid');
    $compact->collection = $results->sortable()->paginate(config('theme.paginator.paginate'));

    $compact->arrShowField = $this->arrShowFieldIndex;

    $this->_cLogSys($request);

    return view('_users.index', (array) $compact);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
      $compact = (object) array();
      $compact->arrShowField = $this->arrShowFieldFrom;

      $this->_getDataBelongs($compact);

      $this->_cLogSys($request);

      return view('_users.form', (array) $compact);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->validator($request->all())->validate();

    $input = (object) $request->except(['_token', '_method']);

    try {
      DB::beginTransaction();

      //$config = ApiConfig::select('val')->where('code', 'UNLI')->first();

      $user = new User;
      foreach ($input as $key => $v) {
        $user->$key = $v;
      }
      // if(@$input->username &&$input->password) {
      //  // $code = $input->username. "@system:". $input->password;
      //   $code = $input->username . $config->val . $input->password;
      //   $user->auth_code = base64_encode($code);
      // }
      $user->password = Hash::make($input->password);
      // $user->created_uid = Auth::id();
      $user->created_at = date("Y-m-d H:i:s");
      $user->save();

      DB::commit();
      Log::info('Successful: User:store : ', ['data' => $user]);

      $this->_cLogSys($request, $user->id);

      $message = trans('core.message_insert_success');
      $status = 'success';
      $title = 'Success';
    } catch (\Exception $e) {

      DB::rollback();
      Log::error('Error: User:store :' . $e->getMessage());

      $this->_cLogSys($request);

      $message = trans('core.message_insert_error');
      $status = 'error';
      $title = 'Error';
    }

    session(['noit_title' => $title]);
    session(['noit_message' => $message]);
    session(['noit_status' => $status]);

    return redirect()->route('users.index');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    if (!$request->user()->hasRole('developer') && $id == 1) {
      abort(404);
    }
    $select = $this->_listToSelect($this->arrShowFieldFrom);

    $compact = (object) array();
    $compact->arrShowField = $this->arrShowFieldFrom;
    $user = User::select($select)->findOrFail($id);

    $compact->user = $user;

    $this->_getDataBelongs($compact);

    $this->_cLogSys($request, $id);

    return view('_users.form',$user, (array) $compact);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {

    if (!$request->user()->hasRole('developer') && $id == 1) {
      abort(404);
    }

    $select = $this->_listToSelect($this->arrShowFieldView);

    $compact = (object) array();
    $compact->arrShowField = $this->arrShowFieldView;
    $compact->user = User::select($select)->findOrFail($id);
    $this->_getDataBelongs($compact);

    $this->_cLogSys($request);

    return view('_users.show', (array) $compact);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, $id) {
  
    //$this->validator($request->all())->validate();

    $input = (object) $request->except(['_token', '_method']);
    try {
      DB::beginTransaction();

      $config = ApiConfig::select('val')->where('code', 'UNLI')->first();

      $user = User::find($id);
      foreach ($input as $key => $v) {
        $user->$key = $v;
      }
      // if (@$input->username && $user->password) {
      //   $code = $input->username . $config->val . $user->password;
      //   $user->auth_code = base64_encode($code);
      // }
      //$user->updated_uid = Auth::id();
      $user->updated_at = date("Y-m-d H:i:s");
      $user->save();

      DB::commit();
      Log::info('Successful: User:update : ', ['id' => $id, 'data' => $user]);

      $this->_cLogSys($request, $id);

      $message = trans('core.message_update_success');
      $status = 'success';
      $title = 'Success';
    } catch (\Exception $e) {

      DB::rollback();
      Log::error('Error: User:update :' . $e->getMessage());

      $this->_cLogSys($request);

      $message = trans('core.message_update_error');
      $status = 'error';
      $title = 'Error';
    }

    session(['noit_title' => $title]);
    session(['noit_message' => $message]);
    session(['noit_status' => $status]);

    return redirect()->route('users.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id) {

    $response = (object) array();

    try {
      DB::beginTransaction();

      $isDelUsersPermission = UsersPermission::where('user_id', $id)->delete();
      $isDelUsersRole = UsersRole::where('user_id', $id)->delete();
     
      $this->_cLogSys($request, $id);

      User::destroy($id);

      DB::commit();
      Log::info('Successful: user:destroy : ', ['id' => $id]);

      
      $message = trans('core.message_del_success');
      $status = 'success';
      $title = 'Success';
    } catch (\Exception $e) {

      DB::rollback();
      Log::error('Error: user:destroy :' . $e->getMessage());

      $this->_cLogSys($request);

      $message = trans('core.message_del_error');
      $status = 'error';
      $title = 'Error';
    }

    $response->title = $title;
    $response->status = $status;
    $response->message = $message;

    return (array) $response;

  }

  /**
   * Field list To Select data form db 
   *
   * @param  array  $arrField
   * @return array select data
   */
  protected function _listToSelect($arrField)
  {
    $select[] = 'id';
    foreach ($arrField as $key => $val) {
      if ($val == 1) {
        $select[] = $key;
      }
    }
    return $select;
  }

  protected function _easySearch($results, $search=""){
    
    $results->where(function ($results) use ($search) {
      
      return $results->orWhere ('Users.username', 'LIKE','%'. @$search.'%')
                    ->orWhere('Users.first_name', 'LIKE', '%' . @$search . '%')
                    ->orWhere('Users.last_name', 'LIKE', '%' . @$search . '%')
                    ->orWhere ('Users.email', 'LIKE','%'. @$search.'%');
        
    });
	      // $results = $results->orWhere ('Users.username', 'LIKE','%'. @$search.'%') ;
	      // $results = $results->orWhere ('Users.first_name', 'LIKE','%'. @$search.'%') ;
	      // $results = $results->orWhere ('Users.last_name', 'LIKE','%'. @$search.'%') ;
	      // $results = $results->orWhere ('Users.email', 'LIKE','%'. @$search.'%') ;
	      // $results = $results->orWhere ('Users.email_verified_at', 'LIKE','%'. @$search.'%') ;
	      // $results = $results->orWhere ('Users.password', 'LIKE','%'. @$search.'%');
	      // $results = $results->orWhere ('Users.auth_code', 'LIKE','%'. @$search.'%');
	      // $results = $results->orWhere ('Users.active', 'LIKE','%'. @$search.'%');
	      // $results = $results->orWhere ('Users.activated', 'LIKE','%'. @$search.'%');
	      // $results = $results->orWhere ('Users.remember_token', 'LIKE','%'. @$search.'%');
	      // $results = $results->orWhere ('Users.last_login', 'LIKE','%'. @$search.'%') ;
        return $results;
  }

  protected function _advSearch($results, $input){
        if(@$input->username){
          $results = $results->where('Users.username', 'LIKE', "%" .  $input->username. "%" );
        }
        if(@$input->first_name){
          $results = $results->where('Users.first_name', 'LIKE', "%" .  $input->first_name. "%" );
        }
        if(@$input->last_name){
          $results = $results->where('Users.last_name', 'LIKE', "%" .  $input->last_name. "%" );
        }
        if(@$input->email){
          $results = $results->where('Users.email', 'LIKE', "%" .  $input->email. "%" );
        }
        if(@$input->email_verified_at_start && @$input->email_verified_at_end){
          $sd = date_create(@$input->email_verified_at_start . ":00");
          $sDate = date_format($sd, "H:i:s");
          $ed = date_create(@$input->email_verified_at_end . ":59");
          $eDate = date_format($ed, "H:i:s");
          $results = $results->whereBetween('Users.email_verified_at',  [$sDate, $eDate]);
        }
        if(@$input->password){
          $results = $results->where('Users.password', 'LIKE', "%" .  $input->password. "%" );
        }
        if(@$input->auth_code){
          $results = $results->where('Users.auth_code', 'LIKE', "%" .  $input->auth_code. "%" );
        }
        if(@$input->active != null){
          $results = $results->where('Users.active',  $input->active);
        }
        if(@$input->activated != null){
          $results = $results->where('Users.activated',  $input->activated);
        }
        if(@$input->remember_token){
          $results = $results->where('Users.remember_token', 'LIKE', "%" .  $input->remember_token. "%" );
        }
        if(@$input->last_login_start && @$input->last_login_end){
          $sd = date_create(@$input->last_login_start . ":00");
          $sDate = date_format($sd, "H:i:s");
          $ed = date_create(@$input->last_login_end . ":59");
          $eDate = date_format($ed, "H:i:s");
          $results = $results->whereBetween('Users.last_login',  [$sDate, $eDate]);
        }
      return $results;
  }

  protected function _getDataBelongs($compact)
  {
  }

  protected function _cLogSys($request, $id = '')
  {
    $newData = [];
    $newRequest = [];

    foreach ($request->all() as $key => $val) {
      if ($key != '_token' && $key != '_method' && $key !='password' && $key != 'password_confirmation') {
        if ($key == 'active') {
          if ($val == 1) {
            $val = trans('users.active.text_radio.true');
          } else {
            $val = trans('users.active.text_radio.false');
          }
        }
        if ($key == 'activated') {
          if ($val == 1) {
            $val = trans('users.activated.text_radio.true');
          } else {
            $val = trans('users.activated.text_radio.false');
          }
        }
        $newRequest[$key] = $val;
      }
    }

    if ($id) {
      $select = $this->_listToSelect($this->arrShowFieldView);
      $data = User::select($select)->findOrFail($id)->toArray();

      foreach ($data as $key => $val) {
        if ($key == 'active') {
          if ($val == 1) {
            $val = trans('users.active.text_radio.true');
          } else {
            $val = trans('users.active.text_radio.false');
          }  
        }
        if ($key == 'activated') {
          if ($val == 1) {
            $val = trans('users.activated.text_radio.true');
          } else {
            $val = trans('users.activated.text_radio.false');
          }
        }
        $newData[$key] = $val;

        
      }
    }
    $this->addLogSys($request, $newData, $newRequest);
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
 * File Create : 2020-09-18 17:11:34 *
 */