<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

use SimpleXML;

use GuzzleHttp\Client;

class SMSController extends Controller
{
    static public function sms($notification_event_type, $reference_id) {
      $message = 'Dear MYF,' . PHP_EOL . 'Your shipment has been booked under Tracking Number: 101101000010' . PHP_EOL . PHP_EOL . 'Regards,' . PHP_EOL . 'Trax Logistics' . PHP_EOL . '03111555065';

      $client = new Client(['base_uri' => 'http://sms.its.com.pk/api/', 'http_errors' => FALSE]);

      $response = $client->get('', [
        'query' => [
          'username' => 'trax',
          'password' => '123456',
          'receiver' => '923332252466',
          'msgdata' => $message
        ]
      ]);

      // $response = simplexml_load_string($response->getBody()->getContents());

      // if ($response->errorno == 0) {}
    }
}