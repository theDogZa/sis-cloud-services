<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViewLogsController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

        // $this->middleware('RolePermission');
        // $this->middleware(function ($request, $next) {
        //     $this->addLogSys($request);
        //     return $next($request);
        // });

        Cache::flush();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: text/html');
        $this->arrShowFieldSysLogs = [
            'username' => 1, 'ip' => 1, 'date' => 1,  'uri' => 1, 'action' => 1, 'methods' => 1, 'response_code' => 1, 'detial' => 1
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sysLogs(Request $request)
    {
        $dir = "/appsyslog/";
        
        $compact = (object) array();

        $date = $request['date'];

        if(!$date) $date = date('Y-m-d');

        $fileName = "syslog-". $date.".log";
       
        $file = storage_path() . $dir. $fileName;

        $content = File::get($file);

        foreach (explode("\n", $content) as $key => $line) {

            if (strpos($line, "#$#")) {
                $isShow = true;
                $newData = [];
                list($time, $name, $data) = explode("#", $line);

                $newData = json_decode($data);

                if (!$request->user()->hasRole('developer')) {
                    if($newData->username == 'dev'){
                        $isShow = false;
                    }
                }

                if($isShow == true){
                    $paraUri = explode("/", $newData->uri);
                    if($paraUri[0] != 'api'){
                        if(count($paraUri)>1){
                            if(count((array)$newData->parameters)>0){
                                $arrPara = (array)$newData->parameters;
                                foreach($paraUri as $key => $paRa){
                                    if($key > 0){
                                        $nParaUrl = str_replace(['{', '}'], '', $paRa);
                                        
                                        if(@isset($arrPara[$nParaUrl])){
                                            $newData->url = $paraUri[0]. '/'.$arrPara[$nParaUrl];
                                        }
                                    }
                                }
                            }
                        }else{
                            $newData->url = $newData->uri;
                        }
                    }else{
                        $newData->url = $newData->uri;
                    }

                    $arrayLog[] = $newData;
                }
                
            }
        }
        krsort($arrayLog);

        $compact->arrShowField = $this->arrShowFieldSysLogs;

        $compact->collection = $arrayLog;
       
        $compact->total = count($arrayLog);

        if (is_dir(storage_path().$dir)) {

            if ($dh = opendir(storage_path().$dir)) {
                $arrDate = [];
                while (($file = readdir($dh)) !== false) {
                    
                    if($file != '.' &&  $file != '..'&&  $file != ''){
                        $aFn = explode("syslog-", $file);
                        $arrDate[] = explode(".log", $aFn[1])[0];
                    }         
                }
                krsort($arrDate);           
                closedir($dh);
            }
            $compact->selectDate = $arrDate;
            $compact->sDate = $date;
        }
        return view('_view_logs.syslogs', (array) $compact);
    }

    public function sysLogsUser(Request $request)
    {
        $dir = "/appsyslog/";

        $compact = (object) array();
        $arrSearch = [];
        $arrayLog = [];
        $date = '';

        if (count($request->all())) {
            $input = (object) $request->all();
            if (@$input->search) {
                $date = $request['search'];
            } else {
                $arrSearch = $input;
                $date = $input->date;
            }
        }
        
        if (!$date) $date = date('Y-m-d');

        $fileName = "syslog-" . $date . ".log";
       
        $file = storage_path() . $dir . $fileName;

        $content = File::get($file);
        
        foreach (explode("\n", $content) as $key => $line) {

            if (strpos($line, "#$#")) {
                $isShow = true;
                $newData = [];
                list($time, $name, $data) = explode("#$#", $line);

                $newData = json_decode($data,true);
         
                if(!empty($arrSearch)){
                    if ($this->_advSearch($newData, $arrSearch)) {
                        $isShow = true;
                  
                    }else{
                        $isShow = false;
                        
                    }
                    //dd($this->_advSearch($newData, $arrSearch), $isShow);
                }else{
                    $isShow == true;
                }             

                if (!$request->user()->hasRole('developer')) {
                    if (@$newData->username == 'dev') {
                        $isShow = false;
                    }
                }

                if ($isShow == true) {
                    if(@$newData->methods[0] == 'GET'){
                        $paraUri = explode("/", $newData->uri);
                        if ($paraUri[0] != 'api') {
                            if (count($paraUri) > 1) {
                                if (count((array)$newData->parameters) > 0) {
                                    $arrPara = (array)$newData->parameters;
                                    foreach ($paraUri as $key => $paRa) {
                                        if ($key > 0) {
                                            $nParaUrl = str_replace(['{', '}'], '', $paRa);                                        
                                            if (@isset($arrPara[$nParaUrl])) {
                                                $newData->url = str_replace($paRa, $arrPara[$nParaUrl], $newData->uri);
                                            }
                                        }
                                    }
                                }else{
                                    $newData->url = $newData->uri;
                                }
                            } else {
                                $newData->url = $newData->uri;
                            }
                        } else {
                            $newData->url = $newData->uri;    
                        }
                    }elseif(@$newData->methods[0] == 'DELETE' || @$newData->methods[0] == 'PUT'){
                        $paraUri = explode("/", $newData->uri);
                            $arrPara = (array)$newData->parameters;
                            foreach ($paraUri as $key => $paRa) {
                                if ($key > 0) {
                                    $nParaUrl = str_replace(['{', '}'], '', $paRa);                                        
                                    if (@isset($arrPara[$nParaUrl])) {
                                        $newData->url = str_replace($paRa, $arrPara[$nParaUrl], $newData->uri);
                                    }
                                }
                            }
                    }else{
                        if(@$newData->uri == 'login'){
                            @$newData->url = $newData->uri;
                        }else{
                          
                            if (@$newData->action == 'api.orgcode') {
                                $newData->url = "";
                                $arrPara = (array)$newData->parameters;
                                $arrReq['orgcode'] = $arrPara['id'];
                                $newData->request = (object) $arrReq;

                            } else {
                                $paraUri = explode("/", @$newData->uri);
                                if ($paraUri[0] != 'api') {
                                   //@$newData->url = null;
                                }else{
                                    $newData = [];
                                }
                            }                       
                        }                      
                    }
                    if(!empty($newData)){
                        $arrayLog[] = $newData; 
                    }
                }
            }
        }
        if(!empty($arrayLog)){
            krsort($arrayLog);
        }
        $arrResponseTxt[200] = '(200) OK';
        $arrResponseTxt[400] = '(400) Bad Request';
        $arrResponseTxt[401] = '(401) Unauthorized';
        $arrResponseTxt[402] = '(402) Payment Required';
        $arrResponseTxt[403] = '(403) Forbidden';
        $arrResponseTxt[404] = '(404) Not Found';
        $arrResponseTxt[405] = '(405) Method Not Allowed';

        $compact->arrResponseTxt = $arrResponseTxt;

        $compact->arrShowField = $this->arrShowFieldSysLogs;

        $compact->collection = $arrayLog;

        $compact->total = count($arrayLog);

        $compact->search = (object) $request->all();

        if (is_dir(storage_path() . $dir)) {

            if ($dh = opendir(storage_path() . $dir)) {
                $arrDate = [];
                $strDate = "";
                while (($file = readdir($dh)) !== false) {

                    if ($file != '.' &&  $file != '..' &&  $file != '') {
                        $aFn = explode("syslog-", $file);
                        $arrDate[] = explode(".log", $aFn[1])[0];
                        $strDate = $strDate.'"'. explode(".log", $aFn[1])[0] . '",';
                    }
                }
                krsort($arrDate);
                closedir($dh);
            }
            $compact->selectDate = $arrDate;
            $compact->sDate = $date;
            $compact->strDate = substr_replace($strDate, "", -1);

            $arrMapAction = [];
            $arrMapAction['index'] = trans('view_logs.action.index');
            $arrMapAction['edit'] = trans('view_logs.action.edit');
            $arrMapAction['update'] = trans('view_logs.action.update');
            $arrMapAction['update_permissions_roles'] = trans('view_logs.action.update_permissions_roles');
            $arrMapAction['list_permissions'] = trans('view_logs.action.list_permissions');
            $arrMapAction['update_user_roles'] = trans('view_logs.action.update_user_roles');
            $arrMapAction['show'] = trans('view_logs.action.show');
            $arrMapAction['create'] = trans('view_logs.action.create');
            $arrMapAction['del'] = trans('view_logs.action.del');
            $arrMapAction['show'] = trans('view_logs.action.show');
            $arrMapAction['store'] = trans('view_logs.action.store');
            $arrMapAction['storeRoles'] = trans('view_logs.action.storeRoles');
            $arrMapAction['destroy'] = trans('view_logs.action.destroy');
            $arrMapAction['orgcode'] = trans('view_logs.action.orgcode');
            $arrMapAction['call'] = trans('view_logs.action.call');
            
            $arrMapMenu = [];
            $arrMapMenu['rolesPermission'] = 'Roles';
            $arrMapMenu['userRoles'] = 'Roles';
            $arrMapMenu['log_api'] = 'Log Tenants';
            $arrMapMenu['api'] = 'EDGE Cluster';
            $arrMapMenu['openTenants'] = 'EDGE Cluster';
            $arrMapMenu['users_permissions'] = 'users permissions';
            $arrMapMenu['api_users'] = 'EDGE Users';

            $compact->mapAction = $arrMapAction;
            $compact->mapMenu = $arrMapMenu;
        }
        return view('_view_logs.syslogs_user', (array) $compact);
    }

    protected function _advSearch($results, $input){
        
        $chk = true;
        
        if(@$input->ip && @$input->username){
            if(!str_contains(@$results->ip, @$input->ip) || !str_contains(@$results->username, @$input->username)){
                $chk = false;
            }
        }elseif(@$input->ip && !@$input->username) {
            if (!str_contains(@$results->ip, @$input->ip)) {
                $chk = false;
            }
           
        }elseif(!@$input->ip && @$input->username) {
            if (!str_contains(strtolower(@$results->username), strtolower(@$input->username))) {
                $chk = false;
            }
        }

        return $chk;
    }

}
