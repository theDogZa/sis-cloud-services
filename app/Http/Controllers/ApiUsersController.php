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

class ApiUsersController extends Controller
{
  /**
   * Instantiate a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    //$this->middleware('auth');
    $this->middleware('RolePermission');
    Cache::flush();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: text/html');

    $this->arrShowFieldIndex = [
		 'username' => 1,  'password' => 1,  'active' => 1, 'description' =>	1	];
		$this->arrShowFieldFrom = [
		 'username' => 1,  'password' => 1,  'active' => 1, 'description' =>  1		];
		$this->arrShowFieldView = [
		 'username' => 1,  'password' => 1,  'active' => 1, 'description' =>  1		];
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
			'username' => 'required|string|max:255',
			'password' => 'required|string|max:255',
			'active' => 'required|string|max:255',
     
			//#Ex
			//'username' => 'required|string|max:20|unique:users,username,' . $data ['id'],
			//'email' => 'required|string|email|max:255|unique:users,email,' . $data ['id'],
			// 'password' => 'required|string|min:6|confirmed',
			//'password' => 'required|string|min:6',
		];
		
		$messages = [
			'username.required' => trans('ApiUser.username_required'),
			'password.required' => trans('ApiUser.password_required'),
			'active.required' => trans('ApiUser.active_required'),
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

    $results = ApiUser::select($select);

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

    $compact->collection = $results->sortable()->paginate(config('theme.paginator.paginate'));

    $compact->arrShowField = $this->arrShowFieldIndex;

    $this->addLogSys($request);

    return view('_api_users.index', (array) $compact);
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

      return view('_api_users.form', (array) $compact);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
   // dd($request->all());
   $this->validator($request->all())->validate();

    $input = (object) $request->except(['_token', '_method']);

    try {
      DB::beginTransaction();

      $ApiUser = new ApiUser;
      foreach ($input as $key => $v) {
        $ApiUser->$key = $v;
      }

      $ApiUser->password = Crypt::encrypt($input->password);
      $ApiUser->username = strtolower($input->username);
      $ApiUser->created_uid = Auth::id();
      $ApiUser->created_at = date("Y-m-d H:i:s");
      $ApiUser->save();

      DB::commit();
      Log::info('Successful: ApiUser:store : ', ['data' => $ApiUser]);
      $this->_cLogSys($request, $ApiUser->toArray());

      $message = trans('core.message_insert_success');
      $status = 'success';
      $title = 'Success';
    } catch (\Exception $e) {

      DB::rollback();
      Log::error('Error: ApiUser:store :' . $e->getMessage());
      $this->_cLogSys($request);

      $message = trans('core.message_insert_error');
      $status = 'error';
      $title = 'Error';
    }

    session(['noit_title' => $title]);
    session(['noit_message' => $message]);
    session(['noit_status' => $status]);

    return redirect()->route('api_users.index');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $id)
  {
    $select = $this->_listToSelect($this->arrShowFieldFrom);

    $compact = (object) array();
    $compact->arrShowField = $this->arrShowFieldFrom;
    $ApiUser = ApiUser::select($select)->findOrFail($id);

    $compact->ApiUser = $ApiUser;

    $this->_getDataBelongs($compact);

    $this->_cLogSys($request, $ApiUser->toArray());

    return view('_api_users.form_edit',$ApiUser, (array) $compact);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $select = $this->_listToSelect($this->arrShowFieldView);

    $compact = (object) array();
    $compact->arrShowField = $this->arrShowFieldView;
    $compact->ApiUser = ApiUser::select($select)->findOrFail($id);

    $compact->UsersMap = UsersMap::select('users.username', 'users.first_name', 'users.last_name')
    ->leftJoin('users', 'users_map.user_id', '=', 'users.id')
    ->where('users_map.api_user_id', $id)
    ->where('users_map.active', true)
    ->orderBy('users.username')
    ->get();
    
    $this->_getDataBelongs($compact);
    $this->_cLogSys($request);
    return view('_api_users.show', (array) $compact);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, $id) {
  
    
    $this->validator($request->all())->validate();

    $input = (object) $request->except(['_token', '_method']);
  
    try {
      DB::beginTransaction();

      $ApiUser = ApiUser::find($id);
      foreach ($input as $key => $v) {
        $ApiUser->$key = $v;
      }
      $ApiUser->password = Crypt::encrypt($input->password);
      $ApiUser->username = strtolower($input->username);
      $ApiUser->updated_uid = Auth::id();
      $ApiUser->updated_at = date("Y-m-d H:i:s");
      $ApiUser->save();

      DB::commit();
      Log::info('Successful: ApiUser:update : ', ['id' => $id, 'data' => $ApiUser]);
      $this->_cLogSys($request, $ApiUser->toArray());

      $message = trans('core.message_update_success');
      $status = 'success';
      $title = 'Success';
    } catch (\Exception $e) {

      DB::rollback();
      Log::error('Error: ApiUser:update :' . $e->getMessage());
      $this->_cLogSys($request);

      $message = trans('core.message_update_error');
      $status = 'error';
      $title = 'Error';
    }

    session(['noit_title' => $title]);
    session(['noit_message' => $message]);
    session(['noit_status' => $status]);

    return redirect()->route('api_users.index');
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
      $this->_cLogSysDel($request, $id);

      ApiUser::destroy($id);
      UsersMap::where('api_user_id', $id)->delete();

      DB::commit();
      Log::info('Successful: ApiUser:destroy : ', ['id' => $id]);

      $message = trans('core.message_del_success');
      $status = 'success';
      $title = 'Success';
    } catch (\Exception $e) {

      DB::rollback();
      Log::error('Error: ApiUser:destroy :' . $e->getMessage());
      $this->_cLogSysDel($request);

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
	      $results = $results->orWhere ('username', 'LIKE','%'. @$search.'%') ;
	      $results = $results->orWhere ('description', 'LIKE','%'. @$search.'%') ;
	      //$results = $results->orWhere ('ApiUsers.active', 'LIKE','%'. @$search.'%') ;
        return $results;
  }

  protected function _advSearch($results, $input){
        if(@$input->username){
          $results = $results->where('username', 'LIKE', "%" .  $input->username. "%" );
        }
        if(@$input->description){
          $results = $results->where('description', 'LIKE', "%" .  $input->description. "%" );
        }
        if(@$input->active){
          $results = $results->where('active',  $input->active);
        }
      return $results;
  }

  protected function _getDataBelongs($compact)
  {
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

  protected function _cLogSys($request, $data = [])
  {
    $newData = [];
    $newRequest = [];

    foreach ($request->all() as $key => $val) {
      if ($key != '_token' && $key != '_method' && $key != 'slug') {
        if ($key == 'active') {
          if ($val == 1) {
            $val = trans('api_users.active.text_radio.true');
          } else {
            $val = trans('api_users.active.text_radio.false');
          }
        }
        $newRequest[$key] = $val;
      }
    }

    foreach ($data as $key => $val) {
      if ($key != 'id' && $key != 'slug' && $key != 'created_uid' && $key != 'updated_uid' && $key != 'created_at' && $key != 'updated_at') {
        if ($key == 'active') {
          if ($val == 1) {
            $val = trans('api_users.active.text_radio.true');
          } else {
            $val = trans('api_users.active.text_radio.false');
          }
        }
        $newData[$key] = $val;
      }
    }
    $this->addLogSys($request, $newData, $newRequest);
  }

  protected function _cLogSysDel($request, $api_user_id = 0)
  {
    $newData = [];
    $newRequest = [];

    if ($api_user_id) {

      $Role = ApiUser::select('username')->findOrFail($api_user_id)->toArray();
      $newData['username'] = $Role['username'];

      $UsersMap = UsersMap::select('users.username')->where('api_user_id', $api_user_id)
        ->leftJoin('users', 'users_map.user_id', '=', 'users.id')->get();

      $a1 = [];
      $a2 = [];

      foreach ($UsersMap as $key => $val) {
        $a2[] = $val->username;
      }

      $newData['UsersMap'] = $a2;

    }

    foreach ($request->all() as $key => $val) {
      if ($key != '_token' && $key != '_method' && $key != 'slug') {
        if ($key == 'active') {
          if ($val == 1) {
            $val = trans('roles.active.text_radio.true');
          } else {
            $val = trans('roles.active.text_radio.false');
          }
        }
        $newRequest[$key] = $val;
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
 * File Create : 2021-03-08 09:54:49 *
 */