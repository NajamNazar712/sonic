<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteImage;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeBankInformation;
use App\Http\Models\HR\EmployeeEducationalBackground;
use App\Http\Models\HR\EmployeeEmployementHistory;
use App\Http\Models\HR\EmployeeMedicalInformation;
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

            $admin_attendance = EmployeeAttendance::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $attendance_date)
                ->where('employee_type', 1);
            $admin_attendance_action = new EmployeeAttendanceActionLog();
            if ($admin_attendance->exists()) {
                $admin_attendance = $admin_attendance->first();
            } else {
                $admin_attendance = new EmployeeAttendance();
                $admin_attendance->employee_id = $admin_id;
                $admin_attendance->employee_type = 1;
                $admin_attendance->attendance_date = $attendance_date;
            }
            if ($request->action == 1) {
                $admin_attendance->clock_in = $attendance_time;
                $admin_attendance->clock_in_latitude = $request->latitude;
                $admin_attendance->clock_in_longitude = $request->longitude;
                $admin_attendance->save();

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
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

                $admin_attendance_action->employee_id = $admin_id;
                $admin_attendance_action->employee_type = 1;
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
            $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', $admin_id)
                ->whereDate('action_date', $request->attendance_date)
                ->where('employee_type', 1)
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

            $admin_attendance = EmployeeAttendance::where('admin_id', $admin_id)
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

    public function admin_signup(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                //Employees
                'name' => ['nullable'],
                'employee_gender_id' => ['nullable'],
                'city_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'cnic_no' => ['nullable', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'guardian_name' => ['nullable'],
                'religion_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_religions,id'],
                'nationality_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_nationalities,id'],
                'domicile_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_domiciles,id'],
                'marital_status_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_marital_statuses,id'],
                'blood_group_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_blood_groups,id'],
                'personal_email' => ['nullable', 'email'],
                'address' => ['nullable'],
                'emergency_contact' => ['nullable'],
                'cnic_issue_date' => ['nullable'],
                'cnic_expiry_date' => ['nullable'],
                'designation_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:employee_designations,id'],
                'department_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:admin_departments,id'],
                'zone_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:zones,id'],
                'official_email' => ['nullable', 'email'],
                'official_phone_number' => ['nullable', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
                'sonic_id' => ['nullable'],
                'place_of_birth' => ['nullable', 'integer', 'digits_between:1,10', 'exists:cities,id'],
                'date_of_birth' => ['nullable'],
                'pin' => ['nullable', 'integer', 'digits:4'],

                //EducationalDetails
                'institute_name' => ['nullable'],
                'degree' => ['nullable'],
                'grade' => ['nullable'],
                'passing_year' => ['nullable'],

                //BankInformation
                'iban' => ['nullable'],
                'branch_name' => ['nullable'],
                'bank_id' => ['nullable'],
                'account_no' => ['nullable'],
                'branch_code' => ['nullable'],
                'account_title' => ['nullable'],

                //Attachments
                'cv' => ['nullable','mimes:png,jpeg,jpg,pdf'],
                'academic_credentials' => ['mimes:png,jpeg,jpg,pdf'],
                'cnic' => ['nullable','mimes:png,jpeg,jpg,pdf'],
                'photo' => ['nullable','mimes:png,jpeg,jpg,pdf'],
                'experience_certificates' => ['mimes:png,jpeg,jpg,pdf'],
                'pay_slip' => ['mimes:png,jpeg,jpg,pdf'],
                'nikkah_nama' => ['mimes:png,jpeg,jpg,pdf'],
                'cnic_spouse' => ['mimes:png,jpeg,jpg,pdf'],
                'bform' => ['mimes:png,jpeg,jpg,pdf'],
                'cnic_nominee' => ['nullable','mimes:png,jpeg,jpg,pdf'],
                'utility_bill' => ['nullable','mimes:png,jpeg,jpg,pdf'],
                'affidavit' => ['nullable','mimes:png,jpeg,jpg,pdf'],
                'cheque' => ['nullable','mimes:png,jpeg,jpg,pdf'],

                //EmploymentHistory
                'employment_history' => ['nullable', 'array'],
                'employment_history.*.company_name' => ['nullable'],
                'employment_history.*.designation' => ['nullable'],
                'employment_history.*.from' => ['nullable'],
                'employment_history.*.to' => ['nullable'],
                'employment_history.*.reason' => ['nullable'],

                //MedicalDetails
                'medical_details' => ['nullable', 'array'],
                'medical_details.*.member_name' => ['nullable'],
                'medical_details.*.relationship_id' => ['nullable'],
                'medical_details.*.dob' => ['nullable'],
                'medical_details.*.marital_status_id' => ['nullable'],


            ];
            $response = ['status' => 1];
            $message = 'Unknown';

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $message = 'Error(s) in Input';
                $response['errors'] = $validate->errors();
            } else {
                $admin = Admin::where('phone_number', $request->input('phone_number'))
                    ->orWhere('cnic', $request->input('cnic_no'));

                //Check Admin Already Exist
                if ($admin->exists()) {
                    $admin = $admin->first();
                    if ($admin->phone_no == $request->input('phone_number') && $admin->cnic == $request->input('cnic_no')) {
                        $message = "Phone Number & CNIC Already Exists";

                    } else if ($admin->phone_no == $request->input('phone_number')) {
                        $message = "Phone Number Already Exist";

                    } else if ($admin->cnic == $request->input('cnic_no')) {
                        $message = "CNIC Already Exist";
                    }
                } else {
                        try {
                            $employee_request = new Employee();
                            $employee_request->name = $request->name;
                            $employee_request->employee_gender_id = $request->employee_gender_id;
                            $employee_request->city_id = $request->city_id;
                            $employee_request->cnic = $request->cnic_no;
                            $employee_request->phone_number = $request->phone_number;
                            $employee_request->employee_type_id = 1;
                            $employee_request->status_id = 2;
                            $employee_request->guardian_name = $request->guardian_name;
                            $employee_request->religion_id = $request->religion_id;
                            $employee_request->nationality_id = $request->nationality_id;
                            $employee_request->domicile_id = $request->domicile_id;
                            $employee_request->marital_status_id = $request->marital_status_id;
                            $employee_request->blood_group = $request->blood_group_id;
                            $employee_request->personal_email = $request->personal_email;
                            $employee_request->address = $request->address;
                            $employee_request->emergency_contact = $request->emergency_contact;
                            $employee_request->cnic_issue_date = $request->cnic_issue_date;
                            $employee_request->cnic_expiry_date = $request->cnic_expiry_date;
                            $employee_request->designation_id = $request->designation_id;
                            $employee_request->department_id = $request->department_id;
                            $employee_request->zone_id = $request->zone_id;
                            $employee_request->official_email = $request->official_email;
                            $employee_request->official_phone_number = $request->official_phone_number;
                            $employee_request->sonic_id = $request->sonic_id;
                            $employee_request->place_of_birth = $request->place_of_birth;
                            $employee_request->date_of_birth = $request->date_of_birth;
                            $employee_request->pin = $request->pin;
                            $employee_request->save();

                            foreach ($request->employment_history as $employment_history) {
                                $history = new EmployeeEmployementHistory();
                                $history->employee_id = $employee_request->id;
                                $history->name = $employment_history['company_name'];
                                $history->designation = $employment_history['designation'];
                                $history->from = $employment_history['from'];
                                $history->to = $employment_history['to'];
                                $history->reason = $employment_history['reason'];
                                $history->save();
                            }

                            foreach ($request->medical_details as $medical_detail) {
                                $medical_info = new EmployeeMedicalInformation();
                                $medical_info->employee_id = $employee_request->id;
                                $medical_info->name = $medical_detail['member_name'];
                                $medical_info->relationship_id = $medical_detail['relationship_id'];
                                $medical_info->date_of_birth = $medical_detail['dob'];
                                $medical_info->marital_status = $medical_detail['marital_status_id'];
                                $medical_info->save();
                            }

                            $employee_education = new EmployeeEducationalBackground();
                            $employee_education->employee_id = $employee_request->id;
                            $employee_education->name = $request->institute_name;
                            $employee_education->degree = $request->degree;
                            $employee_education->grade = $request->grade;
                            $employee_education->passing_year = $request->passing_year;
                            $employee_education->save();

                            $employee_bank_info = new EmployeeBankInformation();
                            $employee_bank_info->employee_id = $employee_request->id;
                            $employee_bank_info->account_title = $request->account_title;
                            $employee_bank_info->branch_code = $request->branch_code;
                            $employee_bank_info->account_no = $request->account_no;
                            $employee_bank_info->bank_id = $request->bank_id;
                            $employee_bank_info->branch_name = $request->branch_name;
                            $employee_bank_info->iban = $request->iban;
                            $employee_bank_info->save();


                            $date = Carbon::now()->format('Y_m_d');
                            $attachments = new EmployeeAttachment();
                            $attachments->employee_id = $employee_request->id;

                            if ($request->hasFile('cv')) {
                                $file = $request->file('cv');
                                $filename = 'cv_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->cv = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('cnic')) {
                                $file = $request->file('cnic');
                                $filename = 'cnic_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->cnic = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('photo')) {
                                $file = $request->file('photo');
                                $filename = 'photo_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->photo = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('academic_credentials')) {
                                $file = $request->file('academic_credentials');
                                $filename = 'academic_credentials_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->academic = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('experience_certificates')) {
                                $file = $request->file('experience_certificates');
                                $filename = 'experience_certificates_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->experience = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('pay_slip')) {
                                $file = $request->file('pay_slip');
                                $filename = 'pay_slip_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->last_pay_slip = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('nikkah_nama')) {
                                $file = $request->file('nikkah_nama');
                                $filename = 'nikkah_nama_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->nikkah_nama = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('cnic_spouse')) {
                                $file = $request->file('cnic_spouse');
                                $filename = 'cnic_spouse_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->cnic_spouse = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('bform')) {
                                $file = $request->file('bform');
                                $filename = 'child_b_form_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->child_b_form = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('cnic_nominee')) {
                                $file = $request->file('cnic_nominee');
                                $filename = 'cnic_nominee_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->cnic_nominee = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('utility_bill')) {
                                $file = $request->file('utility_bill');
                                $filename = 'utility_bill_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->utility_bill = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('affidavit')) {
                                $file = $request->file('affidavit');
                                $filename = 'affidavit_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->affidavit = $directory . '/' . $filename;
                            }

                            if ($request->hasFile('cheque')) {
                                $file = $request->file('cheque');
                                $filename = 'cheque_' . $date . '.' . $file->extension();
                                $directory = 'employee_directory/employee_' . $employee_request->id . '';
                                Storage::disk('public')->putFileAs($directory, $file, $filename);
                                $attachments->cheque = $directory . '/' . $filename;
                            }
                            $attachments->save();

                            $response['status'] = 0;
                            $message = 'Request Has Been Submitted and Pending for Approval';
                        } catch (Exception $ex) {
                            $response['message'] = $ex;
                        }
                    }
                }
            }
        else {
            $message = 'Post Method is Required';
        }
        $response['message'] = $message;
        return response()->json($response);
    }


}
