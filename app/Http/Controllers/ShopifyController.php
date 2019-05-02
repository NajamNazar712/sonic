<?php

namespace App\Http\Controllers;

use App\Http\Models\ShopifyUser;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ShopifyController extends Controller
{
    public function access(Request $request)
    {
        return $request;
        $shopUrl = $request->shop;

        if($shopUrl)
        {
            $shop = Shop::where('myshopify_domain' , $shopUrl)->first();
            if($shop)
            {
                session([

                    'shopifyId' => $shop->shopify_id,
                    'myshopifyDomain' => $shop->domain,
                    'accessToken' => $shop->access_token

                ]);

                return view('home.index' , ['shop' => $shop , 'settings' => $shop->settings]);
            }
            else{
                $shopify = $this->shopify->setShopUrl($shopUrl);
                return redirect()->to($shopify->getAuthorizeUrl(config('shopify.scope') , config('shopify.redirect_uri')));
            }
        }

        $minutes = 60;
        $apiKey = env('SHOPIFY_API_KEY', '5127fc6f16d1cf268bcde127f12ad64e');
        $apiSecret = env('SHOPIFY_API_SECRET', '6dc9ba3103a79c82a942eac59321f7da');
        $scopes = 'read_products,read_orders,write_orders,read_inventory';
        //$forwardingAddress = "https://7020b6ef.ngrok.io"; //env('APP_URL', 'https://7020b6ef.ngrok.io'); // Replace this with your HTTPS Forwarding address
        $state = Str::random(10);
        $redirectUri = secure_url('/shopify/callback/');

        if(isset($request->shop))
        {
            $installUrl = 'https://' . $request->shop .'/admin/oauth/authorize?client_id=' . $apiKey .'&scope=' . $scopes .'&state=' . $state .'&redirect_uri=' . $redirectUri;
            Cookie::queue('state', $state, $minutes);
            return redirect($installUrl);

        }
        else{

            return view('shopify.index');
        }
//            die("Bad Parameters!");
    }

    public function callback(Request $request)
    {
        $apiKey = env('SHOPIFY_API_KEY', '5127fc6f16d1cf268bcde127f12ad64e');
        $apiSecret = env('SHOPIFY_API_SECRET', '6dc9ba3103a79c82a942eac59321f7da');
        $shopify_app_name = 'laravel-embedded-app';
        $accessToken="";

        if($request->state!=$request->cookie('state'))
            die('Cant verify');
        //$accessTokenRequestUrl = 'http://webhook.site/4086f374-0bb2-4a52-906a-c60de85fe1cd';
        $accessTokenRequestUrl =  'https://' . $request->shop . '/admin/oauth/access_token';
        $client = new Client(['base_uri' => $accessTokenRequestUrl, 'http_errors' => FALSE, 'connect_timeout' => 15, 'timeout' => 30]);

        $response = $client->post('', [
            'form_params' => [
                "client_id" => $apiKey,
                "client_secret" => $apiSecret,
                "code" => $request->code
            ]
        ]);

//        $r = $client->request('POST', $accessTokenRequestUrl,[
//            'json' => $accessTokenPayload]);
        $res = json_decode($response->getBody()->getContents());
        $accessToken = $res->access_token;
        $this->save($request->shop, $accessToken);
        $shop_url =  'https://' . $request->shop . '/admin/app/'.$shopify_app_name;
//        return redirect()

        return redirect ($shop_url);
//               $authClient = new Client(['headers' => ['X-Shopify-Access-Token' => $accessToken]]);
//              $response = $authClient->request('GET','https://' . $request->shop . '/admin/shop.json');
//             return $response->getBody()->getContents();

    }
    public function app(){
        return view('shopify.index');
    }

    public function save($shop, $access_token){
        $shopify = new ShopifyUser();
        $shopify->name = $shop;
        $shopify->access_token = $access_token;
        $shopify->save();
    }

    public function orders(Request $request){
        return $request;
//        $authClient = new Client(['headers' => ['X-Shopify-Access-Token' => $accessToken]]);
//        $response = $authClient->request('GET','https://' . $request->shop . '/admin/shop.json');
//        return $response->getBody()->getContents();
    }
}
