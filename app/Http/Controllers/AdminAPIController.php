<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    public function verify(Request $request) {
        return response()->json(['status' => 0, 'message' => 'API Key is Valid']);
    }

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
                if(Hash::check($request->input('password'), $user->password)){
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

    public function return_note_details(Request $request){
        $admin_id = $request->admin_id;
        $return_note_id = $request->return_note_id;
        if($return_note_id){
            $return_note = ReturnNote::find($return_note_id);
            if($return_note){
                if($return_note->status == 1 || $return_note->status == 3){
                    if($return_note->image !== null){
                        $details = array();
                        $img_url = '';
                        $url = 'uploads/return_notes/' . $return_note->image;
                        if(file_exists($url)){
                            $img_url = asset('uploads/return_notes/' . $return_note->image);

                        }else{
                            $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note->image);
                            if($exists){
                                $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note->image, now()->addMinutes(5));
                            }
                        }
                        $details['return_note_id'] = $return_note_id;
                        $details['images'] = array('id' => 0,'image'=> $img_url);
                        return response()->json(['status' => 0, 'message' => 'Images found', 'information' => $details]);
                    }else{
                        $return_note_images = ReturnNoteImage::where('return_note_id', $return_note_id);
                        if($return_note_images->exists()){
                            $return_note_images = $return_note_images->get();
                            $details = array();
                            foreach ($return_note_images as $return_note_image) {
                                $img_url = '';
                                $url = 'uploads/return_notes/' . $return_note_image->image;
                                if(file_exists($url)){
                                    $img_url = asset('uploads/return_notes/' . $return_note_image->image);
                                }else{
                                    $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note_image->image);
                                    if($exists){
                                        $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note_image->image, now()->addMinutes(5));
                                    }
                                }
                                $details['images'][] = array('id' => $return_note_image->id,'image'=> $img_url);
                            }
                            return response()->json(['status' => 0, 'message' => 'Images found', 'information' => $details]);
                        }else{
                            return response()->json(['status' => 0, 'message' => 'No images found!', 'information' => '']);
                        }
                    }
                }else{
                    return response()->json(['status' => 1, 'message' => 'Return Note not ready for image upload!']);
                }

            }else{
                return response()->json(['status' => 1, 'message' => 'Return Note Image not found!']);
            }
        }else {
            return response()->json(['status' => 1, 'message' => 'No return note scanned!']);
        }

    }
    public function history_update_image(Request $request){
        $return_note_id = $request->return_note_id;
        $image = $request->image;
        if($image == 0){
            $return_note = ReturnNote::find($return_note_id);
            if($return_note){
                $return_note->image = NULL;
                $return_note->save();
                return response()->json(['status' => 0, 'success' => 'Image Insert successfully!']);
            }
            return response()->json(['status' => 1, 'error' => 'Return Note not found!']);
        }else{
            ReturnNoteImage::insert(['return_note_id' => 'return_note_id','image' =>'$image']);
            return response()->json(['status' => 0, 'success' => 'Image insert successfully!']);
        }
        return response()->json(['status' => 1, 'error' => 'Image not found!']);
    }
}
