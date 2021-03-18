<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Attendance\AdminAttendance;
use App\Http\Models\Admin\Attendance\AdminAttendanceActionLog;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminAPIController extends Controller
{
    private $names = [
        'email_address' => 'Email Address',
        'password' => 'Password',
        'attendance_date' => 'Attendance Date',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'action' => 'Action',
        'from_date' => 'From Date'

    ];

    private $messages = [
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'image' => ':attribute must be an Image.'
    ];

    public function verify(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'API Key is Valid']);
    }

    public function login(Request $request)
    {
        $rules = [
            'email_address' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user = Admin::where('email', $request->input('email_address'));
            if ($user->exists()) {
                $user = $user->first();
                if($user->status == 0){
                    return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                }
                if (Hash::check($request->input('password'), $user->password)) {
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['role'] = 'staff';

                    if ($user->api_token) {
                        $information['api_token'] = $user->api_token;
                    } else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $user->api_token = $api_token;

                        $user->save();

                        $information['api_token'] = $api_token;
                    }

                    return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Password']);
                }
            } else {
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
                        }
                        else{
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
                                    $exists = Storage::disk('public')->exists('uploads/return_notes/'.$return_note_image->image);
                                    if($exists){
                                        $img_url = asset('storage/uploads/return_notes/'.$return_note_image->image);
                                    }
                                    else{
                                        $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note_image->image);
                                        if($exists){
                                            $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note_image->image, now()->addMinutes(5));
                                        }
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

    public function history_update_image(Request $request)
    {
        $rules = [
            'added_at' => ['required'],
            'return_note_id' => ['required', 'integer', 'digits_between:1,10', 'exists:return_notes,id'],
            'pictures' => ['array','nullable'],
            'pictures.*image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
            'old_image_ids' => ['array', 'min:0']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        $admin_id = $request->admin_id;
        $old_image_ids = array();
        $old_image_ids = ($request->old_image_ids != '')? $request->old_image_ids:[];
        $return_note_id = $request->return_note_id;
        $return_note = ReturnNote::find($return_note_id);
        if ($return_note) {
            $present = false;

            $pictures = array();
            if($request->has('pictures')){
                $pictures = $request->pictures;
            }
            $return_note_image_ids = ReturnNoteImage::where('return_note_id', $return_note_id)->pluck('id')->toArray();
            if (count($return_note_image_ids) > 0) {
                foreach ($return_note_image_ids as $image_id) {
                    if (!in_array($image_id, $old_image_ids)) {
                        $return_note_image = ReturnNoteImage::where('id', $image_id)->first();
                        if ($return_note_image) {
                            $url = 'uploads/return_notes/' . $return_note_image->image;
                            if (file_exists($url)) {
                                File::delete($url);
                            }
                            else {
                                $exists = Storage::disk('public')->exists('uploads/return_notes/'.$return_note_image->image);
                                if($exists){
                                    Storage::disk('public')->delete('uploads/return_notes/'.$return_note_image->image);
                                }else{
                                    $exists = Storage::disk('s3')->exists('return_note_images/' . $return_note_image->image);
                                    if ($exists) {
                                        Storage::disk('s3')->delete('return_note_images/' . $return_note_image->image);
                                    }
                                }
                            }
                            ReturnNoteImage::where('id', $image_id)->delete();
                        }
                    }
                }

                $present = true;
            }
            if (count($pictures) > 0) {
                foreach ($pictures as $picture) {
                    $image = $picture;
                    $extension = 'png';
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $generated_image_name = $time . $random . $admin_id . '.' . $extension;
                    Storage::disk('public')->put('uploads/return_notes/' . $generated_image_name, file_get_contents($image));
                    $return_note_image = new ReturnNoteImage();
                    $return_note_image->return_note_id = $return_note_id;
                    $return_note_image->image = $generated_image_name;
                    $return_note_image->save();
                }

                $present = true;
            }

            if ($present && in_array($return_note->status, [1, 3])) {
                $return_note->updated_by = $admin_id;
                $return_note->status = 1;
                $return_note->save();
            }

            return response()->json(['status' => 0, 'success' => 'Image insert successfully!']);
        }
        return response()->json(['status' => 1, 'error' => 'Return Note not found!']);
    }

    public function mark_attendance(Request $request)
    {

        $rules = [
            'attendance_date' => ['required'],
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'action' => ['required', 'integer', 'digits_between:1,10', 'exists:attendance_actions,id'],
        ];

        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $attendance_datetime = Carbon::parse($request->attendance_date)->format('Y-m-d H:i:s');
            $attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
            $attendance_time = Carbon::parse($request->attendance_date)->format('H:i:s');

            $admin_attendance = AdminAttendance::where('admin_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date);
            $admin_attendance_action = new AdminAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new AdminAttendance();
                $admin_attendance->admin_id = $admin_id;
                $admin_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $admin_attendance->clock_in = $attendance_time;
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->save();

                $admin_attendance_action->admin_id = $admin_id;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = $attendance_datetime;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->save();

                return response()->json(['status' => 0, 'message' => 'Clocked-In Successfully', 'response' => $admin_attendance_action]);
            } elseif ($request->action == 2) {
                $admin_attendance->clock_out = $attendance_time;
                $admin_attendance->clock_out_latitude = $request->latitude;
                $admin_attendance->clock_out_longitude = $request->longitude;
                $admin_attendance->save();

                $admin_attendance_action->admin_id = $admin_id;
                $admin_attendance_action->action_id = $request->action;
                $admin_attendance_action->action_date = $attendance_datetime;
                $admin_attendance_action->latitude = $request->latitude;
                $admin_attendance_action->longitude = $request->longitude;
                $admin_attendance_action->save();
                return response()->json(['status' => 0, 'message' => 'Clocked-Out Successfully', 'response' => $admin_attendance_action]);
            }

            return response()->json(['status' => 1, 'message' => 'Failed']);
        }

    }

    public function attendance_details(Request $request)
    {
        $rules = [
            'attendance_date' => ['required']
        ];
        $admin_id = $request->admin_id;
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $admin_attendance_action = AdminAttendanceActionLog::where('admin_id', $admin_id)
                ->whereDate('action_date', $request->attendance_date)
                ->select('action_id', 'action_date', 'latitude', 'longitude')
                ->orderBy('action_date', 'ASC');
            if ($admin_attendance_action->exists()) {
                $admin_attendance_action = $admin_attendance_action->get();
                return response()->json(['status' => 0, 'attendance_details' => $admin_attendance_action]);
            }
            return response()->json(['status' => 0, 'attendance_details' => []]);
        }
    }

    public function attendance_history(Request $request)
    {
        $rules = [
            'from_date' => ['required'],
            'to_date' => ['nullable']
        ];
        $admin_id = $request->admin_id;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $admin_attendance = AdminAttendance::where('admin_id', $admin_id)
                ->orderBy('attendance_date', 'ASC');

            if ($to_date != null) {
                $admin_attendance = $admin_attendance->whereBetween('attendance_date', [$from_date, $to_date]);
            } else {
                $admin_attendance = $admin_attendance->whereDate('attendance_date', $from_date);
            }

            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->get();
                return response()->json(['status' => 0, 'history_details' => $admin_attendance]);
            }
            return response()->json(['status' => 1, 'message' => "No Details Found"]);
        }
    }


}
