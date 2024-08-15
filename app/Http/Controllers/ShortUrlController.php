<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\ShortUrl;

class ShortUrlController extends Controller
{
    static function make_short_url($tracking_number) {
        
        $site_url = "https://sonic.pk/";
        $long_url = "https://trax.pk/tracking/?tracking_number=$tracking_number";
		$random_param = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(3/strlen($x)) )),1,3);


        $latest_id = ShortUrl::latest('id')->first();
        if($latest_id != null) {
            $latest_id = $latest_id->id + 1;
        } else {
            $latest_id = 1;
        }

        $check_record = ShortUrl::where('tracking_number', $tracking_number)->first();

        if(empty($check_record)) {

            $check_record = new ShortUrl;
            $check_record->long_url = $long_url;
            $check_record->site_url = $site_url;
            $check_record->tiny_url = $random_param . $latest_id;
            $check_record->custom_param = 0;
            $check_record->tracking_number = $tracking_number;
            $check_record->source = 0; 
            $check_record->medium = 0;
            $check_record->description = null;  
            $check_record->save();

            $result =  $check_record->site_url . $check_record->tiny_url;
            return $result;
        } else {
            $result =  $check_record->site_url . $check_record->tiny_url;
            return $result;
        }
    }

    public function get_actual_url($tiny_url) {
       
        $long_url = ShortUrl::where('tiny_url', $tiny_url);
        if($long_url->exists()) {
            $long_url = $long_url->latest()->first();
            $long_url = $long_url->long_url;
            return redirect()->away($long_url);
        } else {
            if($tiny_url == 'admin') {
                return redirect()->route('admin.login');
            } 
            if($tiny_url == 'cod') {
                return redirect()->route('cod.login');
            } 
            if($tiny_url == 'agent') {
                return redirect()->route('agent.login');
            } 
            if($tiny_url == 'retail') {
                return redirect()->route('retail.login');
            } 
            else {
                return redirect()->away('https://trax.pk');
            }
        }
    }
}
