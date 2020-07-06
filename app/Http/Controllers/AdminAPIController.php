<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\ReturnNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAPIController extends Controller
{
    private $names = [
        'email_address' => 'Email Address',
        'password' => 'Password'
    ];

    private $messages = [
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'image' => ':attribute must be an Image.'
    ];

    public function login(Request $request) {
        $rules = [
            'email_address' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $user = Admin::where('email', $request->input('email_address'));
            if ($user->exists()) {
                $user = $user->first();
                if(Hash::check($request->input('password')) == $user->password){
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;

                    if ($user->api_token) {
                        $information['api_token'] = $user->api_token;
                    }
                    else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $user->api_token = $api_token;

                        $user->save();

                        $information['api_token'] = $api_token;
                    }

                    return response()->json(['status' => 0, 'message' => 'Logged In Succesfully', 'information' => $information]);
                }else{
                    return response()->json(['status' => 1, 'message' => 'Invalid Password']);
                }
            }
            else {
                return response()->json(['status' => 1, 'message' => 'Wrong Email/Password!']);
            }
        }
    }

    public function return_note_summary(){
        $return_note = ReturnNote::where('status', 0);
        if($return_note->exists()){
            $return_note = $return_note->get();

        }
    }
}
