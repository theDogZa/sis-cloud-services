<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LogApi;

use App\Models\User;

class LogApiController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        
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
            'org_code' => 1,  'isSuccess' => 1,  'request_id' => 1,  'created_at' => 1,  'created_uid' => 1, 'edge_user' => 1,'updated_at' => 1,
        ];
        $this->arrShowFieldView = [
            'org_code' => 1,  'isSuccess' => 1,  'request_id' => 1,  'created_at' => 0,   'created_uid' => 1, 'edge_user' => 1,'updated_at' => 1,
        ];
        
    }

    public function index(Request $request)
    {

        $compact = (object) array();

        $select = $this->_listToSelect($this->arrShowFieldIndex);

        $results = LogApi::select($select);
        if (!$request->user()->hasRole('developer')) {
            $results = $results->where('log_api.created_uid', '!=' , 1);
        }

        //------ search
        $input = (object) $request->all();
        if (count($request->all())) {
            
            if (@$input->search) {
                $results = $this->_easySearch($results, $input->search);
            } else {
                $results = $this->_advSearch($results, $input);
            }
        }
        if(@$input->sort){
            $sort = $input->sort;     
        }else{
            $sort = 'updated_at';
        }

        if(@$input->direction){   
            $direction = $input->direction;
        }else{
            $direction = 'desc';
        }
       
        $compact->search = (object) $request->all();
      
        $compact->collection = $results->sortable([$sort => $direction])->paginate(5);

        $compact->arrShowField = $this->arrShowFieldIndex;

        $this->_cLogSys($request);

        return view('_log_api.index', (array) $compact);
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
        $compact->logApi = LogApi::select($select)->findOrFail($id);

        $filename = $compact->logApi->request_id . ".json";

        $file = storage_path() . "/logs_request/" . $filename;
        if (file_exists($file)) {

            $content = File::get($file);
            $compact->logApi->log = json_decode($content);
        }

        $this->_cLogSys($request, $id);
      
        return view('_log_api.show', (array) $compact);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $response = (object) array();

        try {
            DB::beginTransaction();

            $logApi = LogApi::select('request_id')->findOrFail($id);
            $filename = $logApi->request_id . ".json";
            $filename = storage_path() . "/logs_request/" . $filename;
            if (file_exists($filename)) {
                File::delete($filename);
            }
            $this->_cLogSys($request, $id);

            LogApi::destroy($id);

            DB::commit();
            Log::info('Successful: LogApi:destroy : ', ['id' => $id]);
            $message = trans('core.message_del_success');
            $status = 'success';
            $title = 'Success';

        } catch (\Exception $e) {

            DB::rollback();
            Log::error('Error: LogApi:destroy :' . $e->getMessage());
            
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

    protected function _easySearch($results, $search = "")
    {
        $results = $results->orWhere('log_api.org_code', 'LIKE', '%' . @$search . '%');
        $results = $results->orWhere('log_api.isSuccess', 'LIKE', '%' . @$search . '%');
        $arrUserId = User::where('username', 'LIKE', "%" .  $search . "%")->pluck('id')->toArray();
        $results = $results->orwhereIn('log_api.created_uid', $arrUserId);
        return $results;
    }

    protected function _advSearch($results, $input)
    {
        if (@$input->org_code) {
            $results = $results->where('log_api.org_code', 'LIKE', "%" .  $input->org_code . "%");
        }
        if (@$input->isSuccess != null) {
            $results = $results->where('log_api.isSuccess',  $input->isSuccess);
        }
        if (@$input->created_uid) {
            $arrUserId = User::where('username', 'LIKE', "%" .  $input->created_uid . "%")->pluck('id')
            ->toArray();
            $results = $results->whereIn('log_api.created_uid', $arrUserId);
        }
        if (@$input->created_at_start && @$input->created_at_end) {
            $sd = date_create(@$input->created_at_start . ":00");
            $sDate = date_format($sd, "H:i:s");
            $ed = date_create(@$input->created_at_end . ":59");
            $eDate = date_format($ed, "H:i:s");
            $results = $results->whereBetween('log_api.created_at',  [$sDate, $eDate]);
        }
        return $results;
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
            if ($key != 'sort' && $key != 'direction' && $key != '_method' && $key != '_token') {
                
                $newRequest[$key] = $val;
            }
        }

        if ($id) {
            $select = $this->_listToSelect($this->arrShowFieldView);
            $data = LogApi::select($select)->findOrFail($id)->toArray();

            foreach ($data as $key => $val) {
                if ($key != 'active') {
                    $newData[$key] = $val;
                }
            }
        }
        $this->addLogSys($request, $newData, $newRequest);
    }
}
