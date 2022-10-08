<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ApiConfig;
use App\Models\LogApi;
use App\Services\GuzzleHttp;
use App\Models\UsersMap;

use App\Http\Controllers\MailController;

class ApiTenantController extends Controller
{
    public function __construct()
    {
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

    public function checkOrgCode(Request $request, $id = 0)
    {
        $response = (object)[];
        $logapi = LogApi::select('id')->where('org_code', $id)->where('isSuccess',1)->first();

        if (@isset($logapi->id)) {
            $response->message = "is use";
            $response->data = $logapi->id;
        } else {
            $response->message = "not is use";
            $response->data = null;
        }

        $response->code = 200;

        return  base64_encode(json_encode((array)$response));
    }

    public function callMethod(Request $request, $id = 0)
    {

        $response = (object)[];
        if ($id == 1) {
            $response = $this->getToken($request);
        } elseif ($id == 2) {
            $response = $this->getUrlByCusCode($request);
        } elseif ($id == 3) {
            $response = $this->getVdcRollup($request);
        } elseif ($id == 4) {
            $response = $this->getOVDC($request);
        } elseif ($id == 5) {
            $response = $this->getNetworkProfile($request);
        } elseif ($id == 6) {
            sleep(5);
            $response = $this->getPrimaryEdgeCluster($request);
        } elseif ($id == 7) {
            sleep(5);
            $response = $this->putPrimaryEdgeCluster($request);
        } elseif ($id == 8) {
            sleep(5);
            $response = $this->getPrimaryEdgeCluster($request);
        }
        return base64_encode(json_encode((array)$response));
    }

    private function getToken(Request $request)
    {
        $credentials = $request['data']['code'];
        $orgCode = $request['data']['orgCode'];
        $requestId = $request['data']['requestid'];
        $config = ApiConfig::select('val')->where('code', 'IPAPI')->first();

        $response = (object)[];

        $arrReq = (object)array();

       // $arrReq->url = config('cloudapi.api.get_token.url');
        //$arrReq->url = $config->val;
        $arrReq->url = 'https://'.$config->val. config('cloudapi.api.get_token.url');
        
        $arrReq->headers = (object)array();
        $arrReq->headers->host = config('cloudapi.api.get_token.host');
        $arrReq->headers->accept = config('cloudapi.api.get_token.accept');
        $arrReq->headers->authorization = ['Basic ' . $credentials];
       // dd($arrReq);
        $api = new GuzzleHttp;
        $apiResponse = (object)$api->post($arrReq);
        Log::channel('SiSCloud')->info('info: start : -------------------------', ['auth' => $credentials]);
       
        if ($apiResponse->code == 200) {
            $response->code = 200;
            $response->message = "";
            $response->data = $apiResponse->data->getHeaderLine('X-VMWARE-VCLOUD-ACCESS-TOKEN');
            $response->raw = $apiResponse->data->getBody()->getContents();
            Log::channel('SiSCloud')->info('Successful: getToken : ', ['code' => $apiResponse->code, 'message' => $apiResponse->data->getHeaderLine('X-VMWARE-VCLOUD-ACCESS-TOKEN')]);
        } else {
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            $response->raw = $apiResponse->data;
            Log::channel('SiSCloud')->error('Error: getToken :', ['code' => $apiResponse->code, 'data' => $apiResponse->data]);
        }

        /*
        * --- start api to log file 
        */
        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.get_token.name');
        $this->filesLog($requestId, $datafile);

        /*
        * --- start api to db 
        */
        $userApi = UsersMap::select('api_users.username')
        ->leftJoin('api_users', 'users_map.api_user_id', '=', 'api_users.id')
        ->where('users_map.user_id', Auth::id())
        ->where('api_users.active', true)
        ->first();

        $LogApi = new LogApi;
        $LogApi->org_code = $orgCode;
        $LogApi->request_id = $requestId;
        $LogApi->created_uid = Auth::id();
        $LogApi->edge_user = $userApi->username;
        $LogApi->created_at = date("Y-m-d H:i:s");
        $LogApi->save();

        /*
        * --- start sent mail
        */
        $config = ApiConfig::select('val')->where('code', 'MCAT')->first();
        if($config->val){
            $Mail = new MailController;
            $Mail->sentMail_call_api($LogApi, $config->val);
        }

        return $response;
    }

    private function getUrlByCusCode(Request $request)
    {
        $token = $request['data']['token'];
        $orgCode = $request['data']['orgCode'];
        $requestId = $request['data']['requestid'];
        $config = ApiConfig::select('val')->where('code', 'IPAPI')->first();

        $response = (object)[];
        $arrReq = (object)array();

        //$arrReq->url = config('cloudapi.api.get_url_by_cuscode.url');
        //$arrReq->url = $config->val;
        $arrReq->url = 'https://'.$config->val. config('cloudapi.api.get_url_by_cuscode.url');
      
        $arrReq->headers = (object)array();
        $arrReq->headers->host = config('cloudapi.api.get_url_by_cuscode.host');
        $arrReq->headers->accept = config('cloudapi.api.get_url_by_cuscode.accept');
        $arrReq->headers->authorization = ['Bearer ' . $token];

        $api = new GuzzleHttp;
        $apiResponse = (object)$api->get($arrReq);

        if ($apiResponse->code == 200) {
            $mainKey = 'Org';
            $keyName = 'name';
            $responseXML = $apiResponse->data->getBody()->getContents();
            $responseArray = $this->_xmlToArray($responseXML);
            $responseOrg = $this->_arrayToArrayKey($responseArray, $mainKey, $keyName);

            if (isset($responseOrg[$orgCode])) {
                $response->code = 200;
                $response->message = "";
                $response->data = $responseOrg[$orgCode]['href'];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->info('Successful: getUrlByCusCode : ', ['code' => $orgCode, 'message' => $responseOrg[$orgCode]['href'], 'raw' => base64_encode(json_encode((array)$response->raw))]);
            } else {
                $response->code = 901;
                $response->message = $orgCode . " Not found";
                $response->data = [];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->error('Error: getUrlByCusCode :', ['code' => $orgCode, 'data' => $response->raw]);
            }
        } else { // apiResponse => error code

            // $response->code = $apiResponse->code;
            // $response->message = $apiResponse->message;
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            $response->raw = $apiResponse->data;
            Log::channel('SiSCloud')->error('Error: getUrlByCusCode :', ['code' => $apiResponse->code, 'data' => $response->raw]);
        }

        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.get_url_by_cuscode.name');
        $this->filesLog($requestId, $datafile);
        return $response;
    }

    private function getVdcRollup(Request $request)
    {
        $token = $request['data']['token'];
        $url = $request['data']['url'];
        $requestId = $request['data']['requestid'];

        $response = (object)[];
        $arrReq = (object)array();

        $arrReq->url = $url;
        $arrReq->headers = (object)array();
        $arrReq->headers->host = config('cloudapi.api.get_url_by_vdcrollup.host');
        $arrReq->headers->accept = config('cloudapi.api.get_url_by_vdcrollup.accept');
        $arrReq->headers->authorization = ['Bearer ' . $token];

        $api = new GuzzleHttp;
        $apiResponse = (object)$api->get($arrReq);

        if ($apiResponse->code == 200) {
            $mainKey = 'Link';
            $keyName = 'rel';
            $responseXML = $apiResponse->data->getBody()->getContents();
            $responseArray = $this->_xmlToArray($responseXML);
            $responseArrKey = $this->_arrayToArrayKey($responseArray, $mainKey, $keyName);

            if (isset($responseArrKey['rollup'])) {
                $response->code = 200;
                $response->message = "";
                $response->data = $responseArrKey['rollup']['href'];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->info('Successful: getVdcRollup : ', ['code' => $apiResponse->code, 'data' => $responseArrKey['rollup']['href'], 'raw' => base64_encode(json_encode((array)$response->raw))]);
            } else {
                $response->code = 901;
                $response->message =  "vdcRollup Not found";
                $response->data = [];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->error('Error: getVdcRollup :', ['code' => $apiResponse->code, 'data' => $response->raw]);
            }
        } else { // apiResponse => error code

            // $response->code = $apiResponse->code;
            // $response->message = $apiResponse->message;
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            Log::channel('SiSCloud')->error('Error: getVdcRollup :', ['code' => $apiResponse->code, 'data' => $apiResponse->message]);
        }

        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.get_url_by_vdcrollup.name');
        $this->filesLog($requestId, $datafile);

        return $response;
    }

    private function getOVDC(Request $request)
    {

        $config = ApiConfig::select('val')->where('code', 'OVDC')->first();

        $token = $request['data']['token'];
        // $orgCode = $request['data']['orgCode']. config('cloudapi.api.get_url_by_OVDC.orgCode');
        $orgCode = $request['data']['orgCode'] . $config->val;
        $url = $request['data']['url'];
        $requestId = $request['data']['requestid'];

        $response = (object)[];
        $arrReq = (object)array();

        $arrReq->url = $url;
        $arrReq->headers = (object)array();
        $arrReq->headers->host = config('cloudapi.api.get_url_by_OVDC.host');
        $arrReq->headers->accept = config('cloudapi.api.get_url_by_OVDC.accept');
        $arrReq->headers->authorization = ['Bearer ' . $token];

        $api = new GuzzleHttp;
        $apiResponse = (object)$api->get($arrReq);

        if ($apiResponse->code == 200) {
            $mainKey = 'OrgVdcReference';
            $keyName = 'name';
            $responseXML = $apiResponse->data->getBody()->getContents();
            $responseArray = $this->_xmlToArray($responseXML);

            $responseArrKey = $this->_arrayToArrayKey($responseArray, $mainKey, $keyName);
// dd($responseArrKey, $orgCode, '1103-0001-OVDC', $responseArrKey['1103-0001-OVDC']);
            if (isset($responseArrKey[$orgCode])) {
                $response->code = 200;
                $response->message = "";
                $response->data = $responseArrKey[$orgCode]['href'];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->info('Successful: getOVDC : ', ['code' => $apiResponse->code, 'orgCode' => $orgCode, 'data' => $responseArrKey[$orgCode]['href']]);
            } else {
                $response->code = 901;
                $response->message = $orgCode . " Not found";
                $response->data = [];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->error('Error: getOVDC :', ['code' => $apiResponse->code, 'orgCode' => $orgCode, 'data' => $response->raw]);
            }
        } else { // apiResponse => error code

            // $response->code = $apiResponse->code;
            // $response->message = $apiResponse->message;
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            Log::channel('SiSCloud')->error('Error: getOVDC :', ['code' => $apiResponse->code, 'data' => $apiResponse->message, 'raw' => base64_encode(json_encode((array)$response->raw))]);
        }

        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.get_url_by_OVDC.name');
        $this->filesLog($requestId, $datafile);

        return $response;
    }

    private function getNetworkProfile(Request $request)
    {
        $token = $request['data']['token'];
        $url = $request['data']['url'];
        $requestId = $request['data']['requestid'];

        $response = (object)[];
        $arrReq = (object)array();

        $arrReq->url = $url;
        $arrReq->headers = (object)array();
        $arrReq->headers->host = config('cloudapi.api.get_url_by_network_profile.host');
        $arrReq->headers->accept = config('cloudapi.api.get_url_by_network_profile.accept');
        $arrReq->headers->authorization = ['Bearer ' . $token];

        $api = new GuzzleHttp;
        $apiResponse = (object)$api->get($arrReq);

        if ($apiResponse->code == 200) {
            $mainKey = 'Link';
            $keyName = 'rel';
            $responseXML = $apiResponse->data->getBody()->getContents();
            $responseArray = $this->_xmlToArray($responseXML);
            $responseArrKey = $this->_arrayToArrayKey($responseArray, $mainKey, $keyName);

            if (isset($responseArrKey['down:vdcNetworkProfile'])) {
                $response->code = 200;
                $response->message = "";
                $response->data = $responseArrKey['down:vdcNetworkProfile']['href'];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->info('Successful: getNetworkProfile : ', ['code' => $response->code, 'data' => $responseArrKey['down:vdcNetworkProfile']['href']]);
            } else {
                $response->code = 901;
                $response->message =  "NetworkProfile Not found";
                $response->data = [];
                $response->raw = $responseXML;
                Log::channel('SiSCloud')->error('Error: getNetworkProfile :', ['code' => $apiResponse->code, 'data' => $response->raw]);
            }
        } else { // apiResponse => error code

            // $response->code = $apiResponse->code;
            // $response->message = $apiResponse->message;
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            Log::channel('SiSCloud')->error('Error: getNetworkProfile :', ['code' => $apiResponse->code, 'data' => $apiResponse->message]);
        }

        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.get_url_by_network_profile.name');
        $this->filesLog($requestId, $datafile);

        return $response;
    }

    private function getPrimaryEdgeCluster(Request $request)
    {
        $token = $request['data']['token'];
        $url = $request['data']['url'];
        $requestId = $request['data']['requestid'];

        $response = (object)[];
        $arrReq = (object)array();

        $arrReq->url = $url;
        $arrReq->headers = (object)array();
        $arrReq->headers->host = config('cloudapi.api.get_primary_edge_cluster.host');
        $arrReq->headers->accept = config('cloudapi.api.get_primary_edge_cluster.accept');
        $arrReq->headers->authorization = ['Bearer ' . $token];

        $api = new GuzzleHttp;
        $apiResponse = (object)$api->get($arrReq);

        if ($apiResponse->code == 200) {

            $bodyResponse = $apiResponse->data->getBody();
            $arrResponse = (array)json_decode($bodyResponse);

            if (array_key_exists("primaryEdgeCluster", $arrResponse)) {
                $response->code = $apiResponse->code;
                $response->message = "";
                $response->data = $arrResponse['primaryEdgeCluster'];
                $response->raw =  (string)$bodyResponse;

                Log::channel('SiSCloud')->info('Successful: getPrimaryEdgeCluster : ', ['code' => $response->code, 'data' => $arrResponse['primaryEdgeCluster']]);
                //  dd($arrResponse, $arrResponse['primaryEdgeCluster'], $arrResponse['primaryEdgeCluster']->name);
            } else {
                $response->code = 901;
                $response->message =  "primaryEdgeCluster Not found";
                $response->data = [];
                $response->raw = '"' . (string)$bodyResponse . '"';
                Log::channel('SiSCloud')->error('Error: getPrimaryEdgeCluster :', ['code' => $apiResponse->code, 'data' => $response->raw]);
            }
        } else { // apiResponse => error code

            // $response->code = $apiResponse->code;
            // $response->message = $apiResponse->message;
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            Log::channel('SiSCloud')->error('Error: getPrimaryEdgeCluster :', ['code' => $apiResponse->code, 'data' => $apiResponse->message]);
        }

        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.get_primary_edge_cluster.name');
        $this->filesLog($requestId, $datafile);

        if (array_key_exists("primaryEdgeCluster", $arrResponse)) {
            if (@$arrResponse['primaryEdgeCluster']->name == "GDC-ESG-Cluster") {
                /*
                * --- end api to update db 
                */

                $LogApi = LogApi::where('request_id', $requestId)
                    ->update([
                        'isSuccess' => true,
                        'updated_uid' => Auth::id(),
                        'updated_at' =>  date("Y-m-d H:i:s"),
                    ]);
            }
        }
        return $response;
    }

    private function putPrimaryEdgeCluster(Request $request)
    {
        $config = ApiConfig::select('val')->where('code', 'PECID')->first();

        $token = $request['data']['token'];
        $url = $request['data']['url'];
        $requestId = $request['data']['requestid'];

        $response = (object)[];
        $arrReq = (object)array();

        // $arrId['id'] = config('cloudapi.api.put_primary_edge_cluster.cluster_id');
        $arrId['id'] = $config->val;
        $objId['primaryEdgeCluster'] = $arrId;

        $headers = [
            "accept" => config('cloudapi.api.put_primary_edge_cluster.accept'),
            "Host" => config('cloudapi.api.put_primary_edge_cluster.host'),
            "Content-Type" => config('cloudapi.api.put_primary_edge_cluster.contentType'),
            "Content-Length" => config('cloudapi.api.put_primary_edge_cluster.contentLength'),
            "authorization" => ['Bearer ' . $token]
        ];

        $arrReq->url = $url;
        $arrReq->headers = $headers;
        //         $arrReq->body = '{
        // "primaryEdgeCluster": {
        // "id": "' . config('cloudapi.api.put_primary_edge_cluster.cluster_id') . '"
        // }
        // }';
        $arrReq->body = '{
"primaryEdgeCluster": {
"id": "' . $config->val . '"
}
}';
        $api = new GuzzleHttp;
        $apiResponse = (object)$api->put($arrReq);

        if ($apiResponse->code == 202) {

            $response->code = 200;
            $response->message = "";
            $response->data = "";
            Log::channel('SiSCloud')->info('Successful: putPrimaryEdgeCluster : ', ['code' => $apiResponse->code, 'data' => $response->data]);
        } else { // apiResponse => error code

            $message = substr(explode("response", $apiResponse->message)[1], 1);
            $message = str_replace('"', ' ', substr(explode(",", $message)[0], 2));

            // $response->code = $apiResponse->code;
            // $response->message = $message;
            $response = (object)$this->_htmlStatusCode($apiResponse->code);
            $response->data = [];
            Log::channel('SiSCloud')->error('Error: putPrimaryEdgeCluster :', ['code' => $apiResponse->code, 'data' => $apiResponse->message]);
        }

        $datafile = (object)[];
        $datafile = $response;
        $datafile->apiName = config('cloudapi.api.put_primary_edge_cluster.name');
        $this->filesLog($requestId, $datafile);

        return $response;
    }

    private function _xmlToArray($xml = "")
    {
        return json_decode(json_encode((array)simplexml_load_string($xml)), true);
    }

    private function _arrayToArrayKey($array = [], $mainKey = '', $keyName = '')
    {
        $response = [];

        if (count($array[$mainKey]) == 1) {
            $responseArrKey = $array[$mainKey]['@attributes'];
            $response[$responseArrKey[$keyName]] = $responseArrKey;
        } else {
            foreach ($array[$mainKey] as $v) {
                $v = $v['@attributes'];
                $response[$v[$keyName]] = $v;
            }
        }
        return $response;
    }

    private function _htmlStatusCode($code = "", $str = "")
    {
        $response = [];
        switch ($code) {

            case 35:
                $response["status"] = 35;
                $response["message"] = "Error 35 : SSL_ERROR_SYSCALL";
                break;
            case 200:
                $response["status"] = 200;
                $response["message"] = "Successful";
                break;
            case 201;
                $response["status"] = 201;
                $response["message"] = "";
                break;
            case 202:
                $response["status"] = 200;
                $response["message"] = "Created";
                break;
            case 400;
                $response["status"] = 400;
                $response["message"] = "Error 400 : Bad Request";
                break;
            case 401;
                $response["status"] = 401;
                $response["message"] = "Error 401 : Unauthorized";
                break;
            case 403;
                $response["status"] = 403;
                $response["message"] = "Error 403 : Forbidden";
                break;
            case 404;
                $response["status"] = 404;
                $response["message"] = "Error 404 : Not Found";
                break;
            case 405;
                $response["status"] = 405;
                $response["message"] = "Error 405 : Method Not Allowed";
                break;
            case 409;
                $response["status"] = 409;
                $response["message"] = "Error 409 : Conflict";
                break;
            case 411;
                $response["status"] = 411;
                $response["message"] = "Error 411 : Length Required";
                break;
            case 412;
                $response["status"] = 412;
                $response["message"] = "Error 412 : Precondition Failed";
                break;
            case 429;
                $response["status"] = 429;
                $response["message"] = "Error 429 : Too Many Requests";
                break;
            case 500;
                $response["status"] = 500;
                $response["message"] = "Error 500 : Internal Server Error";
                break;
            case 503;
                $response["status"] = 503;
                $response["message"] = "Error 503 : Service Unavailable";
                break;
            default:
                $response["status"] = $code;
                $response["message"] = "Error " . $code . " : api error unknown";
        }
        return $response;
    }

    private function filesLog($requestId, $data = [], $type = 'update')
    {
        $d = [];
        $filename = $requestId . ".json";

        $file = storage_path() . "/logs_request/" . $filename;
        if (file_exists($file)) {

            $content = File::get($file);
            $d = json_decode($content);
        }

        array_push($d, json_encode($data));

        Storage::disk('logs_request')->put($filename, json_encode((array) $d));
    }
}
