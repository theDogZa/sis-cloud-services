<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\ApiConfig;

class MailController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     *  sent Mail Error Itme Not found in Inventory System.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    function sentMail_call_api($data = [], $mailto)
    {

        $arrmailto = explode(",", $mailto);

        //$subject =  trans('mail.callapi.subject', ['org_code' => $data['org_code']]);
        $subject = "API CLOUD : New Call API : Org Code ". $data['org_code'];
 
        $formMail = config('mail.from.address');
        $toMail = $arrmailto;
       
        $compact = [];
        $compact['org_code'] = $data['org_code'];
        $compact['id'] = $data['id'];

        Mail::send('_emails.callapi', $compact, function ($message) use ($formMail, $toMail, $subject) {
            $message->from($formMail);
            $message->to($toMail);
            $message->subject($subject);
        });
        
        return true;
    }
}
