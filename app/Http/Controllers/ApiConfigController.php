<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\ApiConfig;

class ApiConfigController extends Controller
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
        // $this->middleware(function ($request, $next) {
        //     $this->addLogSys($request);
        //     return $next($request);
        // });
        Cache::flush();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: text/html');

        $this->arrShowFieldIndex = [
            'code' => 0,  'type' => 0, 'isRequest' => 0,  'name' => 1,  'des' => 1,  'val' => 1,
        ];
        $this->arrShowFieldFrom = [
            'code' => 0,  'type' => 0, 'isRequest' => 1, 'name' => 1,  'des' => 1,  'val' => 1,
        ];
        $this->arrShowFieldView = [
            'code' => 0,  'type' => 0, 'isRequest' => 0, 'name' => 1,  'des' => 1,  'val' => 1,
        ];
    }

    public function index(Request $request)
    {
        $compact = (object) array();

        $select = $this->_listToSelect($this->arrShowFieldIndex);

        $results = ApiConfig::select($select);

        //------ search
        // if (count($request->all())) {
        //     $input = (object) $request->all();
        //     if (@$input->search) {
        //         $results = $this->_easySearch($results, $input->search);
        //     } else {
        //         $results = $this->_advSearch($results, $input);
        //     }
        // }
        // $compact->search = (object) $request->all();

        // $this->_getDataBelongs($compact);
        //-----

        $compact->collection = $results->sortable()->paginate(config('theme.paginator.paginate'));
        $compact->arrShowField = $this->arrShowFieldIndex;

        $this->_cLogSys($request);

        return view('_api_config.index', (array) $compact);
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
        $ApiConfig = ApiConfig::select($select)->findOrFail($id);

        $compact->ApiConfig = $ApiConfig;

        $this->_cLogSys($request, $id);

        return view('_api_config.form', $ApiConfig, (array) $compact);
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
        $compact->ApiConfig = ApiConfig::select($select)->findOrFail($id);

        $this->_cLogSys($request);

        return view('_api_config.show', (array) $compact);
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

        //$this->validator($request->all())->validate();

        $input = (object) $request->except(['_token', '_method']);

        try {
            DB::beginTransaction();

            $ApiConfig = ApiConfig::find($id);
            foreach ($input as $key => $v) {
                $ApiConfig->$key = $v;
            }
            $ApiConfig->updated_uid = Auth::id();
            $ApiConfig->updated_at = date("Y-m-d H:i:s");
            $ApiConfig->save();

            DB::commit();
            Log::info('Successful: config:update : ', ['id' => $id, 'data' => $ApiConfig]);
 
            $this->_cLogSys($request, $id);

            $message = trans('core.message_update_success');
            $status = 'success';
            $title = 'Success';

        } catch (\Exception $e) {

            DB::rollback();
            Log::error('Error: config:update :' . $e->getMessage());

            $this->_cLogSys($request);

            $message = trans('core.message_update_error');
            $status = 'error';
            $title = 'Error';
        }

        session(['noit_title' => $title]);
        session(['noit_message' => $message]);
        session(['noit_status' => $status]);

        return redirect()->route('config.index');
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

    protected function _cLogSys($request, $id = '')
    {
        $newData = [];
        $newRequest = [];

        foreach ($request->all() as $key => $val) {          
            $newRequest[$key] = $val;
        }

        if($id){
            $select = $this->_listToSelect($this->arrShowFieldView);
            $data = ApiConfig::select($select)->findOrFail($id)->toArray();

            foreach ($data as $key => $val) {
                if ($key != 'code') {    
                    $newData[$key] = $val;
                }
            }
        }
        $this->addLogSys($request, $newData, $newRequest);
    }

}
