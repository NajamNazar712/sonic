<?php

namespace App\Jobs;

use App\Http\Models\SMS;
use App\Http\Models\Telenor;
use App\Mail\Notifications;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ProcessOTPSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sms;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SMS $sms)
    {
        $this->queue = 'sms_otp';
        $this->sms = $sms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->sms->status < 2) {
                 $this->telenor($this->sms);
            }
        }
        catch(Exception $exception) {
            $to = ['muhammad.yousuf@trax.pk'];
            $subject = '[Error] SMS API';
            $body = 'Error Exception.<br/>' . json_encode($exception->getMessage());

            $mail = Mail::to($to)->send(new Notifications($subject, $body));
        }
    }

    private function telecard($sms) {
        try {
            $client = new Client(['base_uri' => 'https://bsms.telecard.com.pk/SMSPortal/Customer/ProcessSMS.aspx', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->get('', [
                'query' => [
                    'userid' => 'trax',
                    'pwd' => 'trax123',
                    'mobileno' => $sms->to,
                    'msg' => $sms->body
                ]
            ]);

            $response = $response->getBody()->getContents();

            if (substr($response, 0, 2) == 'OK') {
                $sms->status = 3;

                $sms->save();
            }
            else if ($response == 'Invalid Mobile Number Entered.') {
                $sms->status = 2;

                $sms->save();
            }
            else {
                $sms->status = 2;

                $sms->save();

                $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] SMS API';
                $body = 'Unrecognized Error in SMS API.<br/>SMS ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($response);

                $mail = Mail::to($to)->send(new Notifications($subject, $body));
            }
        } catch (RequestException $e) {
            $this->sms->status = 1;

            $this->sms->save();
        }
    }

    private function telenor($sms) {
        $sms->status = 1;

        $sms->save();

        $base_uri = 'https://telenorcsms.com.pk:27677/corporate_sms2/api/';

        $generate_session_id = FALSE;

        $telenor = Telenor::latest()->first();

        if ($telenor) {
            $now = Carbon::now();
            $last = Carbon::parse($telenor->created_at);

            $difference = $last->diffInMinutes($now);

            if ($difference >= 15) {
                $generate_session_id = TRUE;
            }
        }
        else {
            $generate_session_id = TRUE;
        }

        $send_sms = FALSE;

        if ($generate_session_id) {
            $result = $this->telenor_generate_session_id($base_uri, $sms);

            if ($result) {
                $send_sms = TRUE;
            }
        }
        else {
            if ($telenor->status == 1) {
                $send_sms = TRUE;
            }
        }

        if ($send_sms) {
            $this->telenor_sms($base_uri, $sms);
        }
    }

    private function telenor_generate_session_id($base_uri, $sms) {
        $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120, 'verify' => false]);

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
                $subject = '[Error] SMS API';
                $body = 'Error in Generate Session ID SMS API.<br/>Response Received: ' . json_encode($xml);

                $mail = Mail::to($to)->send(new Notifications($subject, $body));

                $telenor = Telenor::latest()->first();

                if ($telenor) {
                    $telenor->status = 0;

                    $telenor->save();
                }

                $sms->status = 1;

                $sms->save();

                return FALSE;
            }
        }
        catch (RequestException $e) {
            $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
            $subject = '[Error] SMS API';
            $body = 'Error in Generate Session ID SMS API.<br/>No Response';

            $mail = Mail::to($to)->send(new Notifications($subject, $body));

            $sms->status = 1;

            $sms->save();

            return FALSE;
        }
    }

    private function telenor_sms($base_uri, $sms, $retry = FALSE) {
        $telenor = Telenor::latest()->first();

        if ($telenor) {
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120, 'verify' => false]);

            try {
                $error = FALSE;

                $response = $client->get('sendsms.jsp', [
                    'query' => [
                        'session_id' => $telenor->session_id,
                        'to' => $sms->to,
                        'text' => $sms->body,
                        'mask' => 'TRAX',
                        'transaction_message' => 'true'
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
                }
                else {
                    $error = TRUE;
                }

                if ($error) {
                    $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                    $subject = '[Error] SMS API';
                    $body = 'Error in SMS SMS API.<br/>SMS ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($xml);

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
                $body = 'Error in SMS SMS API.<br/>SMS ID: ' . $sms->id . '<br/>No Response';

                $mail = Mail::to($to)->send(new Notifications($subject, $body));

                $sms->status = 1;

                $sms->save();
            }
        }
        else {
            // $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
            // $subject = '[Error] SMS API';
            // $body = 'Error in SMS SMS API.<br/>SMS ID: ' . $sms->id . '<br/>No Entry';

            // $mail = Mail::to($to)->send(new Notifications($subject, $body));

            // $sms->status = 1;

            // $sms->save();
        }
    }

    private function telenor_ping($base_uri) {
        $telenor = Telenor::latest()->first();

        if ($telenor) {
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120, 'verify' => false]);

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
                    $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                    $subject = '[Error] SMS API';
                    $body = 'Error in Ping SMS API.<br/>Response Received: ' . json_encode($xml);

                    $mail = Mail::to($to)->send(new Notifications($subject, $body));
                }
            }
            catch (RequestException $e) {
                $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                $subject = '[Error] SMS API';
                $body = 'Error in Ping SMS API.<br/>No Response';

                $mail = Mail::to($to)->send(new Notifications($subject, $body));
            }
        }
        else {
            // $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
            // $subject = '[Error] SMS API';
            // $body = 'Error in Ping SMS API.<br/>No Entry';

            // $mail = Mail::to($to)->send(new Notifications($subject, $body));
        }
    }
}
