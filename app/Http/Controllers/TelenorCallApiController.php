<?php

namespace App\Http\Controllers;

use App\Http\Models\Telenor;
use App\Mail\Notifications;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TelenorCallApiController extends Controller
{
    public function call(){

    }
    
    private function telenor_generate_session_id($base_uri, $call) {
        $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);

        try {
            $error = FALSE;

            $response = $client->get('auth.jsp', [
                'query' => [
                    'msisdn' => '923426687475',
                    'password' => 'T3l3n0rPassw0rd333'
                ]
            ]);

            $xml = json_decode(json_encode(simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);

            if ($xml['response'] == 'OK') {
                Telenor::truncate();

                $telenor = new Telenor();

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
                $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] CALL API';
                $body = 'Error in Generate Session ID CALL API.<br/>Response Received: ' . json_encode($xml);

                $mail = Mail::to($to)->send(new Notifications($subject, $body));

                $telenor = Telenor::latest()->first();

                if ($telenor) {
                    $telenor->status = 0;

                    $telenor->save();
                }

                $call->status = 1;

                $call->save();

                return FALSE;
            }
        }
        catch (RequestException $e) {
            $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] CALL API';
            $body = 'Error in Generate Session ID CALL API.<br/>No Response';

            $mail = Mail::to($to)->send(new Notifications($subject, $body));

            $call->status = 1;

            $call->save();

            return FALSE;
        }
    }
    private function telenor($call) {
        $base_uri = 'https://telenorcsms.com.pk:27677/corporate_sms2/api/';

        $generate_session_id = FALSE;

        $telenor = Telenor::latest()->first();

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

        $send_call = FALSE;

        if ($generate_session_id) {
            $result = $this->telenor_generate_session_id($base_uri, $call);

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
            $this->telenor_call($base_uri, $call);
        }
    }

    private function telenor_call($base_uri, $sms, $retry = FALSE) {
        $telenor = Telenor::latest()->first();

        if ($telenor) {
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);

            try {
                $error = FALSE;

                $response = $client->get('sendsms.jsp', [
                    'query' => [
                        'session_id' => $telenor->session_id,
                        'to' => $sms->to,
                        'text' => $sms->body,
                        'mask' => 'TRAX'
                    ]
                ]);

                $xml = json_decode(json_encode(simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);

                if ($xml['response'] == 'OK') {
                    $sms->status = 3;

                    $sms->save();

                    $this->telenor_ping($base_uri);
                }
                else if ($xml['response'] == 'Error') {
                    if ($xml['data'] == 'Error 201' || $xml['data'] == 'Error 101' || $xml['data'] == 'Error 502') {
                        $sms->status = 2;

                        $sms->save();
                    }
                    else {
                        $error = TRUE;
                    }
                }
                else if ($xml['data'] == 'Error 102') {
                    if (!$retry) {
                        $result = $this->telenor_generate_session_id($base_uri, $sms);

                        if ($result) {
                            $this->telenor_sms($base_uri, $sms, TRUE);
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
                    $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                    $subject = '[Error] SMS API';
                    $body = 'Error in CALL API.<br/>CALL ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($xml);

                    $mail = Mail::to($to)->send(new Notifications($subject, $body));

                    $telenor->status = 0;

                    $telenor->save();

                    $sms->status = 1;

                    $sms->save();
                }
            }
            catch (RequestException $e) {
                $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] SMS API';
                $body = 'Error in CALL API.<br/>CALL ID: ' . $sms->id . '<br/>No Response';

                $mail = Mail::to($to)->send(new Notifications($subject, $body));

                $sms->status = 1;

                $sms->save();
            }
        }
        else {
            $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] SMS API';
            $body = 'Error in CALL API.<br/>CALL ID: ' . $sms->id . '<br/>No Entry';

            $mail = Mail::to($to)->send(new Notifications($subject, $body));

            $sms->status = 1;

            $sms->save();
        }
    }
}
