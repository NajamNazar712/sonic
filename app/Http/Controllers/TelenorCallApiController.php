<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipment;
use App\Http\Models\Telenor;
use App\Http\Models\TelenorApiError;
use App\Http\Models\TelenorCallResponse;
use App\Http\Models\TelenorCallSession;
use App\Mail\Notifications;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TelenorCallApiController extends Controller
{
    static private function telenor_generate_session_id($base_uri, $calls, $multiple) {
        $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);

        try {
            $error = FALSE;

            $response = $client->get('auth.jsp', [
                'query' => [
                    'msisdn' => '923477459355',
                    'password' => 'Traxrobocall0345'
                ]
            ]);

            $xml = json_decode(json_encode(simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);

            if ($xml['response'] == 'OK') {
                TelenorCallSession::truncate();

                $telenor = new TelenorCallSession();

                $telenor->session_id = $xml['data'];

                $telenor->save();

                return TRUE;
            }
            else if ($xml['response'] == 'Error') {
                $error = TRUE;
            }
            else {
                $error = TRUE;
            }

            if ($error) {
                $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] CALL API';
                $body = 'Error in Generate Session ID CALL API.<br/>Response Received: ' . json_encode($xml);

                $mail = Mail::to($to)->send(new Notifications($subject, $body));

                $telenor = Telenor::latest()->first();

                if ($telenor) {
                    $telenor->status = 0;

                    $telenor->save();
                }


                $error_id = 2;
                if($multiple == 1){
                    foreach ($calls as $call){
                        $call->status = 3;
                        $call->error_id = $error_id;
                        $call->save();
                    }
                }
                else{
                    $calls->status = 3;
                    $calls->error_id = $error_id;
                    $calls->save();
                }

                return FALSE;
            }
        }
        catch (RequestException $e) {
            $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] CALL API';
            $body = 'Error in Generate Session ID CALL API.<br/>No Response';

            $mail = Mail::to($to)->send(new Notifications($subject, $body));


            $error_id = 2;
            $call->status = 3;
            $call->error_id = $error_id;

            $call->save();

            return FALSE;
        }
    }
    static private function telenor_ping($base_uri) {
        $telenor = TelenorCallSession::latest()->first();

        if ($telenor) {
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);

            try {
                $error = FALSE;

                $response = $client->get('ping.jsp', [
                    'query' => [
                        'session_id' => $telenor->session_id
                    ]
                ]);

                $xml = json_decode(json_encode(simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);

                if ($xml['response'] == 'OK') {
                    $telenor->touch();
                }
                else {
                    $error = TRUE;
                }

                if ($error) {
                    $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                    $subject = '[Error] CALL API';
                    $body = 'Error in Ping CALL API.<br/>Response Received: ' . json_encode($xml);

                    $mail = Mail::to($to)->send(new Notifications($subject, $body));
                }
            }
            catch (RequestException $e) {
                $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] CALL API';
                $body = 'Error in Ping CALL API.<br/>No Response';

                $mail = Mail::to($to)->send(new Notifications($subject, $body));
            }
        }
        else {
            $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] CALL API';
            $body = 'Error in Ping CALL API.<br/>No Entry';

            $mail = Mail::to($to)->send(new Notifications($subject, $body));
        }
    }

    //Feedback Call
    static public function call($date, $start_date, $end_date, $void_shipments){
        $shipments = Shipment::join('shipments_journey as sj', 'sj.shipment_id', '=', 'shipments.id')
            ->select('shipments.id', 'shipments.tracking_number', 'shipments.consignee_phone_number_1 as phone_number')
            ->where('sj.shipper_status_id', DB::raw(14))
            ->where('sj.verification', DB::raw(1))
            ->where('sj.created_at', '>=', $start_date)
            ->where('sj.verification', '<=', $end_date)
            ->where('shipments.user_id', 3324)
            ->whereNotIn('shipments.id', $void_shipments);
        $api_errors = TelenorApiError::pluck('code', 'id')->toArray();
        $phone_numbers = '';
        if($shipments->exists()){
            $shipments = $shipments->get();
            foreach ($shipments as $key => $shipment){
                $phone_number = str_replace('-', '', $shipment->phone_number);
                $phone_number_length = strlen($phone_number);
                $phone_number_start = substr($phone_number, 0, 2);
                $check = false;
                if($phone_number_start == 03 && $phone_number_length == 11){
                    $telenor_call_response[$key] = TelenorCallResponse::where('shipment_id', $shipment->id)->whereDate('created_at', $date);
                    if($telenor_call_response[$key]->exists()){
                        $telenor_call_response[$key] = $telenor_call_response[$key]->first();
                    }
                    else{
                        $telenor_call_response[$key] = new TelenorCallResponse();
                        $telenor_call_response[$key]->shipment_id = $shipment->id;
                        $telenor_call_response[$key]->tracking_number = $shipment->tracking_number;
                        $telenor_call_response[$key]->save();
                    }
                    if($phone_numbers == ''){
                        $phone_numbers = $phone_number;
                    }
                    else{
                        $phone_numbers = $phone_numbers . ',' . $phone_number;
                    }
                    $check = true;
                }
            }
            if($check){
                self::telenor($telenor_call_response, $phone_numbers, $api_errors, NULL);
            }
        }
    }
    static private function telenor($calls, $phone_numbers, $api_errors, $response) {
        $base_uri = 'https://telenorcsms.com.pk:27677/corporate_sms2/api/';

        $generate_session_id = FALSE;

        $telenor = TelenorCallSession::latest()->first();
        if ($telenor) {
            $now = Carbon::now();
            $last = Carbon::parse($telenor->created_at);

            $difference = $last->diffInMinutes($now);

            if ($difference >= 25) {
                $generate_session_id = TRUE;
            }
        }
        else {
            $generate_session_id = TRUE;
        }
        if($response == NULL){
            $send_call = FALSE;

            if ($generate_session_id) {
                $result = self::telenor_generate_session_id($base_uri, $calls, 1);

                if ($result) {
                    $send_call = TRUE;
                }
            }
            else {
                if ($telenor->status == 1) {
                    $send_call = TRUE;
                }
            }

            if ($send_call) {
                self::telenor_call($base_uri, $phone_numbers, $calls, $api_errors);
            }
        }
        else{
            $get_call_response = FALSE;

            if ($generate_session_id) {
                $result = self::telenor_generate_session_id($base_uri, $calls, 0);

                if ($result) {
                    $get_call_response = TRUE;
                }
            }
            else {
                if ($telenor->status == 1) {
                    $get_call_response = TRUE;
                }
            }

            if ($get_call_response) {
                self::telenor_call_response($base_uri, $calls, $api_errors);
            }
        }
    }
    static private function telenor_call($base_uri, $phone_numbers, $calls, $api_errors, $retry = FALSE) {
        $telenor = TelenorCallSession::latest()->first();

        if ($telenor) {
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            try {
                $error = FALSE;
                $destination = $phone_numbers;
                $response = $client->get('make_feedback_call.jsp', [
                    'query' => [
                        'session_id' => $telenor->session_id,
                        'to' => $destination,
                        'file_id' => 3536,
                        'max_retries' => 1,
                        'valid_options' => 1,
                        'valid_feedback_file_id' => 3537,
                        'invalid_feedback_file_id' => 3538
                    ]
                ]);

                $xml = json_decode(json_encode(simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);
                if ($xml['response'] == 'OK') {
                    $data = explode(',', $xml['data']);
                    $index = 0;
                    foreach ($calls as $call){
                        $call->status = 1;
                        $call->call_id = $data[$index];
                        $call->save();
                        $index++;
                    }

                    self::telenor_ping($base_uri);
                }
                else if ($xml['response'] == 'Error') {
                    $error = TRUE;
                }
                else if ($xml['data'] == 'Error 102') {
                    if (!$retry) {
                        $result = self::telenor_generate_session_id($base_uri, $calls);

                        if ($result) {
                            self::telenor_call($base_uri, $phone_numbers, $calls, $api_errors, TRUE);
                        }
                        else {
                            $error = TRUE;
                        }
                    }
                    else {
                        $error = TRUE;
                    }
                }
                else {
                    $error = TRUE;
                }

                if ($error) {
                    $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                    $subject = '[Error] CALL API';
                    foreach ($calls as $call) {
                        $body = 'Error in CALL API.<br/>CALL ID: ' . $call->id . '<br/>Response Received: ' . json_encode($xml);

                        $mail = Mail::to($to)->send(new Notifications($subject, $body));

                        $telenor->status = 0;

                        $telenor->save();
                        $error_id = array_search($xml['data'], $api_errors);
                        $call->status = 3;
                        $call->error_id = $error_id;
                        $call->error_code = $xml['data'];
                        $call->save();
                    }
                }
            }
            catch (RequestException $e) {
                $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] CALL API';
                foreach ($calls as $call) {
                    $body = 'Error in CALL API.<br/>CALL ID: ' . $call->id . '<br/>No Response';

                    $mail = Mail::to($to)->send(new Notifications($subject, $body));

                    $error_id = array_search($xml['data'], $api_errors);
                    $call->status = 3;
                    $call->error_id = $error_id;
                    $call->error_code = $xml['data'];
                    $call->save();
                }
            }
        }
        else {
            $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] CALL API';
            foreach ($calls as $call) {
                $body = 'Error in CALL API.<br/>CALL ID: ' . $call->id . '<br/>No Entry';

                $mail = Mail::to($to)->send(new Notifications($subject, $body));

                $error_id = 1;
                $call->status = 3;
                $call->error_id = $error_id;
                $call->save();
            }
        }
    }
    //Feedback Call

    //Get Feedback Response
    static public function response_call($date){
        $telenor_call_responses = TelenorCallResponse::whereIn('status', [1,2])->whereNotNull('call_id')
            ->where(function ($query) {
                $query->whereNotIn('response', [1, 2])
                    ->orWhereNull('response');
            })->whereDate('created_at', $date);
        $api_errors = TelenorApiError::pluck('code', 'id')->toArray();
        if($telenor_call_responses->exists()){
            $telenor_call_responses = $telenor_call_responses->get();
            foreach ($telenor_call_responses as $telenor_call_response){
                self::telenor($telenor_call_response, NULL, $api_errors, 1);
            }
        }
    }
    static private function telenor_call_response($base_uri, $call, $api_errors, $retry = FALSE) {
        $telenor = TelenorCallSession::latest()->first();
        if ($telenor) {
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            try {
                $error = FALSE;
                $response = $client->get('querycall.jsp', [
                    'query' => [
                        'session_id' => $telenor->session_id,
                        'call_id' => $call->call_id
                    ]
                ]);

                $xml = json_decode(json_encode(simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);
//                dd($xml);
                if ($xml['response'] == 'OK') {
                    if($xml['data']['status'] == 1){
                        if($xml['data']['optionSelected'] == 1){
                            $option_selected = 1;
                        }
                        elseif ($xml['data']['optionSelected'] >= 2){
                            $option_selected = 2;
                        }
                        else{
                            $option_selected = 3;
                        }
                    }
                    else{
                        $option_selected = 3;
                    }
                    $call->status = 2;
                    $call->response = $option_selected;
                    $call->phone_number = $xml['data']['msisdn'];
                    $call->response_status = $xml['data']['status'];

                    $call->save();

                    self::telenor_ping($base_uri);
                }
                else if ($xml['response'] == 'Error') {
                    $error = TRUE;
                }
                else if ($xml['data'] == 'Error 102') {
                    if (!$retry) {
                        $result = self::telenor_generate_session_id($base_uri, $call);

                        if ($result) {
                            self::telenor_call_response($base_uri, $call, $api_errors, TRUE);
                        }
                        else {
                            $error = TRUE;
                        }
                    }
                    else {
                        $error = TRUE;
                    }
                }
                else {
                    $error = TRUE;
                }

                if ($error) {
                    $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                    $subject = '[Error] CALL API';
                    $body = 'Error in CALL API.<br/>CALL ID: ' . $call->id . '<br/>Response Received: ' . json_encode($xml);

                    $mail = Mail::to($to)->send(new Notifications($subject, $body));

                    $telenor->status = 1;

                    $telenor->save();


                    $error_id = array_search($xml['data'], $api_errors);
                    $call->status = 3;
                    $call->error_id = $error_id;
                    $call->error_code = $xml['data'];
                    $call->save();
                }
            }
            catch (RequestException $e) {
                $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] CALL API';
                $body = 'Error in CALL API.<br/>CALL ID: ' . $call->id . '<br/>No Response';

                $mail = Mail::to($to)->send(new Notifications($subject, $body));


                $error_id = array_search($xml['data'], $api_errors);
                $call->status = 3;
                $call->error_id = $error_id;
                $call->error_code = $xml['data'];
                $call->save();
            }
        }
        else {
            $to = ['asad.ahsan@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] CALL API';
            $body = 'Error in CALL API.<br/>CALL ID: ' . $call->id . '<br/>No Entry';

            $mail = Mail::to($to)->send(new Notifications($subject, $body));


            $error_id = 1;
            $call->status = 3;
            $call->error_id = $error_id;
            $call->save();
        }
    }
    //Get Feedback Response
}
