@extends('admin.layout.master')

@section('title', 'User Profile')

@section('content')

    <h1 class="mb-1">
        Shipper Profile
    </h1>
    <div class="card">
        <div class="card-header">
            {{--<h4 class="card-title">User Profile</h4>--}}
            @include('admin.inc.messages')
        </div>
        <div class="card-content">
            <div id="tabs" class="card-body">
                {{--<p>Use <code>.nav-justified</code> class to set tabs justified.</p>--}}
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" aria-controls="active" aria-expanded="true">Profile Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="link-tab" data-toggle="tab" href="#link" aria-controls="link" aria-expanded="false">Shipping Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="linkOpt-tab" data-toggle="tab" href="#linkOpt" aria-controls="linkOpt">Bank Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="linkEmail-tab" data-toggle="tab" href="#linkEmail" aria-controls="linkEmail">Notification Emails</a>
                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                    <div role="tabpanel" class="tab-pane active" id="active" aria-labelledby="active-tab" aria-expanded="true">
                        <div class="table-responsive">
                            <br>
                            <table class="table" style="font-size: 14px">
                                <thead>
                                <tr>
                                    {{--<th>Firstname</th>--}}
                                    {{--<th>Lastname</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><b>Company Name</b></td>
                                    <td>{{$user->name}}</td>
                                </tr>
                                @if($user->brand_name != null)
                                    <tr>
                                        <td><b>Brand Name</b></td>
                                        <td>{{$user->brand_name}}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><b>Email Address</b></td>
                                    <td>{{$user->email}}</td>
                                </tr>
                                <tr>
                                    <td><b>Address</b></td>
                                    <td>{{$user->address}}</td>
                                </tr>
                                <tr>
                                    <td><b>Person of Contact</b></td>
                                    <td>{{$user->poc}}</td>
                                </tr>
                                <tr>
                                    <td><b>Phone Number 1</b></td>
                                    <td>{{$user->phone}}</td>
                                </tr>
                                <tr>
                                    <td><b>Phone Number 2</b></td>
                                    <td>{{$user->phone2}}</td>
                                </tr>
                                <tr>
                                    <td><b>CNIC Number</b></td>
                                    <td>{{$user->cnic}}</td>
                                </tr>
                                <tr>
                                    <td><b>NTN Number</b></td>
                                    <td>{{$user->ntn_no}}</td>
                                </tr>
                                <tr>
                                    <td><b>STRN Number</b></td>
                                    <td>{{$user->strn_no}}</td>
                                </tr>
                                <tr>
                                    <td><b>URL</b></td>
                                    <td>{{$user->url}}</td>
                                </tr>
                                <tr>
                                    <td><b>Product Type</b></td>
                                    <td>{{$product_name}}</td>
                                </tr>
                                @if($user->product_id == 24)
                                    <tr>
                                        <td><b>Product Name</b></td>
                                        <td>{{$user->other_product_name}}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><b>City</b></td>
                                    <td>{{$user->city->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nature Of Account</b></td>
                                    <td>{{$user->account_type->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Average Shipments</b></td>
                                    <td>{{$user->average_shipments}}@if($average_shipment_duration != null) / {{$average_shipment_duration->name}}@endif</td>
                                </tr>
                                @if($reference)
                                    <tr>
                                        <td><b>Reference</b></td>
                                        <td>{{$reference->name}}</td>
                                    </tr>
                                @endif
                                @if($user->segment_id != null)
                                    <tr>
                                        <td><b>Segment</b></td>
                                        <td>{{$user->segment->name}}
                                        </td>
                                    </tr>
                                @endif
                                @if($user->sub_segment_id != null)
                                    <tr>
                                        <td><b>Sub Segment</b></td>
                                        <td>{{$user->sub_segment->name}}
                                        </td>
                                    </tr>
                                @endif                              
                                <tr>
                                    <td><b>API Key</b></td>
                                    <td>{{$user->api_token}}</td>
                                </tr>                             
                                <tr>
                                    <td><b>Payment Cycle</b></td>
                                    <td>
                                        @if(isset($user->payment_cycle))
                                            {{ $user->payment_cycle->name }}
                                        @else
                                            No payment cycle found
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Payment Cycle Days</b></td>
                                    <td>
                                      {{ $days }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        @if (session('role_id') == 1 || in_array(111, session('permissions')))
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="edit-1" type="button" class="btn btn-primary btn-block">Edit</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="tab-pane" id="link" role="tabpanel" aria-labelledby="link-tab" aria-expanded="false">


                        <div class="table-responsive">
                            <br>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S.No</th>
                                    <th class="border-primary border-darken-1">Pickup Address ID</th>
                                    <th class="border-primary border-darken-1">Pickup Address</th>
                                    <th class="border-primary border-darken-1">Person of Contact</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Brand Name</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Email Address</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                    <div class="tab-pane" id="linkOpt" role="tabpanel" aria-labelledby="linkOpt-tab" aria-expanded="false">
                        <div class="table-responsive">
                            <br>
                            <table class="table" style="font-size: 14px">
                                <thead>
                                <tr>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><b>Bank Name</b></td>
                                    <td>{{$user_bank_default->bank->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Bank Branch</b></td>
                                    <td>{{$user_bank_default->bank_branch}}</td>
                                </tr>
                                <tr>
                                    <td><b>Bank City</b></td>
                                    <td>{{$user_bank_default->city->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Account No.</b></td>
                                    <td>{{$user_bank_default->account_no}}</td>
                                </tr>
                                <tr>
                                    <td><b>Account Title</b></td>
                                    <td>{{$user_bank_default->account_title}}</td>
                                </tr>
                                <tr>
                                    <td><b>IBAN Number</b></td>
                                    <td>{{$user_bank_default->iban}}</td>
                                </tr>
                                <tr>
                                    <td><b>Payment Cycle</b></td>
                                    <td>{{ optional($user->payment_cycle)->name }}</td>
                                </tr>



                                @if($user->account_type_id == 2)
                                    <tr>
                                        <td><b>Invoicing Cycle</b></td>
                                        <td>{{$user_bank_default->invoicing->name}}</td>
                                    </tr>
                                    @if($user_bank_default->invoicing_cycle_id != 2 || $user_bank_default->invoicing_cycle_id != 4)
                                        <tr>
                                            <td><b>Generation Date</b></td>
                                            <td>{{$user_bank_default->generation_date}}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><b>Billing Person Name</b></td>
                                        <td>{{$user_bank_default->billing_person_name}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Billing Person Phone</b></td>
                                        <td>{{$user_bank_default->billing_person_phone}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Billing Person Email</b></td>
                                        <td>{{$user_bank_default->billing_person_email}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Billing Address</b></td>
                                        <td>{{$user_bank_default->billing_address}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <br>
                        @if (session('role_id') == 1 || in_array(112, session('permissions')))
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="edit-2" type="button" class="btn btn-primary btn-block">Edit</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="tab-pane" id="linkEmail" role="tabpanel" aria-labelledby="linkEmail-tab" aria-expanded="false">
                        <div class="mt-2">
                            <div class="row">
                                <div class="col">
                                    <p class="font-large-2">Emails List</p>
                                </div>
                                <div class="col text-right">
                                    @if(count($emails) > 0)
                                        <button type="button" class="btn btn-primary round btn-min-width mr-1 mt-2 editEmail">
                                            <i class="la la-edit"></i>
                                            Edit</button>

                                    @else
                                        <button type="button" class="btn btn-primary round btn-min-width mr-1 mt-2 addEmail">
                                            <i class="la la-plus"></i>
                                            Add</button>

                                    @endif

                                </div>
                            </div>

                            <ul class="list-group">
                                @if(count($emails) > 0)
                                    @foreach($emails as $email)
                                        <li class="list-group-item">{{$email->email}}</li>
                                    @endforeach
                                @else
                                    <li class="list-group-item">No Emails Found</li>
                                @endif

                            </ul>

                        </div>
                    </div>
                </div>
            </div>


            <div class="card-body">
                <div class="card-text">

                </div>
                <form id="profile-form" class="form form-horizontal" style="display: none" method="post" action="{{route('admin.accounts.update.profile')}}">
                    @csrf
                    <div class="form-body">
                        <h4 class="form-section">Profile Info</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Company Name:</label>
                                        {{--<span class="danger">*</span>--}}
                                        @if(session('role_id') == 1 || in_array(250, session('permissions')))
                                            <input type="text" minlength="3" id="name" class="form-control border-primary" data-rule-remote="{{ route('cod.check.name', ['id' => $user->id,'name'=>$user->name]) }}" data-msg-remote="Company Name must be unique" data-rule-required="true" data-msg-required="Company Name is required" value="{{$user->name}}" name="name" required>
                                        @else
                                            <input type="text" minlength="3" id="name" class="form-control border-primary" data-rule-remote="{{ route('cod.check.name', ['id' => $user->id,'name'=>$user->name]) }}" data-msg-remote="Company Name must be unique" data-rule-required="true" data-msg-required="Company Name is required" value="{{$user->name}}" name="name" required readonly>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Person of Contact:</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="poc"  data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters" class="form-control border-primary" value="{{$user->poc}}" name="poc" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Email Address</label>
                                        <span class="danger">*</span>
                                        <input type="email" id="email" class="form-control border-primary" data-rule-remote="{{ route('cod.check.email', ['id' => $user->id,'email'=>$user->email]) }}" data-msg-remote="Email must be unique" data-rule-required="true" data-msg-required="Email address is required" value="{{$user->email}}" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Address:</label>
                                        <span class="danger">*</span>
                                        <textarea type="text" id="address" class="form-control border-primary" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters" data-rule-required="true" data-msg-required="Address is required" value="{{$user->address}}" name="address" required>{{$user->address}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Phone Number 1</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone number is required" value="{{$user->phone}}" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label>Phone Number 2:</label>
                                        <input type="text" id="phone2" class="form-control border-primary" value="{{$user->phone2}}" name="phone2">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>CNIC No.</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="cnic" class="form-control border-primary" data-rule-required="true" data-msg-required="CNIC Number is required" value="{{$user->cnic}}" name="cnic" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label>NTN Number</label>
                                        <input type="text" id="ntn_no" class="form-control border-primary" value="{{$user->ntn_no}}" name="ntn_no">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label>URL</label>
                                        <input type="text" data-rule-maxlength="190" data-msg-maxlength="URL can be maximum 190 characters" id="url" class="form-control border-primary" value="{{$user->url}}" name="url">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="form-group col-md-9">
                                        <label>Product Type</label>
                                        <span class="danger">*</span>
                                        <select name="product_id" id="product_id" data-rule-required="true" data-msg-required="Product Type is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($products as $single_product)
                                                <option value="{{$single_product->id}}" {{ $user->product_id == $single_product->id ? 'selected' : '' }} >{{$single_product->product_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row {{ ($user->product_id != 24)? 'd-none':'' }}" id="product_name_div">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-9">
                                                <label>Product Name</label>
                                                <span class="danger">*</span>
                                                <input type="text" id="product_name" class="form-control border-primary" value="{{$user->other_product_name}}" name="product_name" data-msg-required="Product Name is required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label>Password:</label>
                                        <input type="password" id="password" class="form-control border-primary" value="" name="password">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>City</label>
                                        <span class="danger">*</span>
                                        <select name="city_id" id="city_id" data-rule-required="true" data-msg-required="City is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($all_cities as $city)
                                                <option value="{{$city->id}}" {{ $user->city_id == $city->id ? 'selected' : '' }} >{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="form-group col-md-9">
                                    <label for="segments">Segments:
                                        <span class="danger">*</span>
                                    </label>

                                    <select name="segment_id" id="segment_id" class="select2 form-control required" style="width: 100%">
                                        @foreach($segments as $segment)
                                            @if ($user->segment_id == $segment->id)
                                              <option value="{{$segment->id}}" selected>{{$segment->name}}</option>
                                            @else
                                                <option value="{{$segment->id}}">{{$segment->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="form-group col-md-9">
                                    <label for="sub_segments">Sub Segments:
                                        <span class="danger">*</span>
                                    </label>

                                    <select name="sub_segment_id" id="sub_segment_id" class="select2 form-control required" style="width: 100%" >
                                        @if ($sub_segments->count() > 0)
                                            @foreach($sub_segments as $sub_segment)
                                                @if ($sub_segment->id == $user->sub_segment_id)
                                                <option value="{{$sub_segment->id}}" selected>{{$sub_segment->name}}</option>
                                                    
                                                @else
                                                    
                                                <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-9">
                                    <label>STRN Number</label>
                                    <input type="text" id="strn_no" class="form-control border-primary" value="{{$user->strn_no}}" name="strn_no">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-9">
                                    <label>Brand Name</label>
                                    <input type="text" id="brand_name" class="form-control border-primary" value="{{$user->brand_name}}" name="brand_name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-9">
                                    <label>Expected Average Shipments:
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" id="avg_shipments" class="form-control border-primary" value="{{$user->average_shipments}}" name="avg_shipments">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="form-group col-md-9">
                                    <label for="average_shipment_duration_id">Expected Average Shipment Duration:
                                        <span class="danger">*</span>
                                    </label>

                                    <select name="average_shipment_duration_id" id="average_shipment_duration_id" class="select2 form-control required" style="width: 100%" >
                                        @if ($average_shipment_durations_cycle->count() > 0)
                                            @foreach($average_shipment_durations_cycle as $asdc)
                                                @if ($asdc->id == $user->average_shipment_duration_id)
                                                <option value="{{$asdc->id}}" selected>{{$asdc->name}}</option>
                                                    
                                                @else
                                                    
                                                <option value="{{$asdc->id}}">{{$asdc->name}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        </div>
                        <div class="form-actions right">
                            <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </div>
                </form>

                <form id="bank-form" class="form form-horizontal" style="display: none" method="post" action="{{route('admin.accounts.update.bank')}}">
                    @csrf
                    <div class="form-body">

                        <h4 class="form-section">Bank Info </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Bank Name</label>
                                        <span class="danger">*</span>
                                        <select name="bank_name" id="bank_name" data-rule-required="true" data-msg-required="Bank name is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($banks as $bank)
                                                <option value="{{$bank->id}}"  {{ $user_bank_default->bank_name == $bank->id ? 'selected' : '' }} >{{$bank->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Bank Branch</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="bank_branch" data-rule-maxlength="190" data-msg-maxlength="Bank Branch can be maximum 190 characters" class="form-control border-primary" data-rule-required="true" data-msg-required="Bank branch is required" value="{{$user_bank_default->bank_branch}}" name="bank_branch" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Account Number</label>
                                        <span class="danger">*</span>
                                        <input id="account_no" class="form-control border-primary" data-rule-maxlength="190" data-msg-maxlength="Account Number can be maximum 190 characters" type="text" value="{{$user_bank_default->account_no}}" data-rule-required="true" data-msg-required="Account Number is required" name="account_no"  required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Account Title</label>
                                        <span class="danger">*</span>
                                        <input id="account_title" class="form-control border-primary" type="text" data-rule-maxlength="190" data-msg-maxlength="Account Title can be maximum 190 characters" value="{{$user_bank_default->account_title}}" data-rule-required="true" data-msg-required="Account Title is required" name="account_title"  required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>IBAN Number</label>
                                        <span class="danger">*</span>
                                        
                                        <input id="iban" class="form-control border-primary" type="text" value="{{$user_bank_default->iban}}" data-msg-required="IBAN Number is required" name="iban" required>
                                        <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">
                                        <span id="iban_no_error" class="danger" style="display: none;">IBAN Number must be of 24 Characters</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label>Bank City</label>
                                        <span class="danger">*</span>
                                        <select name="bank_city" id="bank_city" data-rule-required="true" data-msg-required="Bank City is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($all_cities as $bank_city)
                                                <option value="{{$bank_city->id}}"  {{ $user_bank_default->city_id == $bank_city->id ? 'selected' : '' }}  >{{$bank_city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($user->account_type_id == 2)
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="form-group col-md-9">
                                            <label>Invoicing Cycle</label>
                                            <span class="danger">*</span>
                                            <select name="invoicing_cycle_id" id="invoicing_cycle" data-rule-required="true" data-msg-required="Invoicing Cycle is required" class="select2 form-control required">
                                                @foreach($invoicing_cycle as $cycle)
                                                    <option value="{{$cycle->id}}" {{ ($user_bank_default->invoicing_cycle_id != null && $user_bank_default->invoicing_cycle_id == $cycle->id) ? 'selected' : '' }}>{{$cycle->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div id="generation_div" class="d-none"></div>
                                </div>




                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="form-group col-md-9">
                                            <label>Billing Person Name</label>
                                            <span class="danger">*</span>
                                            <input type="text" id="billing_person_name" data-rule-maxlength="190" data-msg-maxlength="Billing Person Name can be maximum 190 characters" class="form-control border-primary" data-rule-required="true" data-msg-required="Billing Person Name is required" value="{{$user_bank_default->billing_person_name}}" name="billing_person_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="form-group col-md-9">
                                            <label>Billing Person Phone</label>
                                            <span class="danger">*</span>
                                            <input type="text" id="billing_person_phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Billing Person Phone is required" value="{{$user_bank_default->billing_person_phone}}" name="billing_person_phone" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="form-group col-md-9">
                                            <label>Billing Person Email</label>
                                            <span class="danger">*</span>
                                            <input type="email" id="billing_person_email" class="form-control border-primary" data-rule-required="true" data-msg-required="Billing Person Name is required" value="{{$user_bank_default->billing_person_email}}" name="billing_person_email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="form-group col-md-9">
                                            <label>Billing Address</label>
                                            <span class="danger">*</span>
                                            <input type="text" id="billing_address" data-rule-maxlength="190" data-msg-maxlength="Billing Address can be maximum 190 characters" class="form-control border-primary" data-rule-required="true" data-msg-required="Billing Address is required" value="{{$user_bank_default->billing_address}}" name="billing_address" required>
                                        </div>
                                    </div>
                                </div>

                            @endif
                        </div>
                    </div>
                    <div class="form-actions center">
                        <button id="cancel-button-bank" type="button" class="btn btn-warning mr-1">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    {{--Edit Email Modal--}}
    <div class="modal fade text-left" id="EditEmailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditEmails"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Notification Emails</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_notification_emails" action="{{route('admin.accounts.edit.emails')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <input type="hidden" value="{{$user->id}}" name="edit_shipper_id">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 form-group">
                                    <input name="email_address" id="edit_email_address" class="email_address" data-tags-input-name="email_address" data-rule-required="true" data-msg-required="Email Address is required" value="{{$email_ids}}">

                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="editEmails" type="submit" class="btn btn-primary btn-block">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Edit Email Modal--}}
    {{--Add Email Modal--}}
    <div class="modal fade text-left" id="AddEmailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddEmails"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Notification Emails</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_notification_emails" action="{{route('admin.accounts.add.emails')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <input type="hidden" value="{{$user->id}}" name="add_shipper_id">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 form-group">
                                    <input name="email_address" id="add_email_address" class="email_address" data-tags-input-name="email_address" data-rule-required="true" data-msg-required="Email Address is required" value="">

                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="addEmails" type="submit" class="btn btn-primary btn-block">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Add Email Modal--}}
    

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/selectize/selectize.css')}}">

    <style>
        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('#product_id').select2({
                width: '100%',
            }).bind('select2:select', function(){
                var product_id = $(this).val();
                if(product_id == 24){
                    $('#product_name_div').removeClass('d-none');
                    $('#product_name').attr('data-rule-required', true);
                }else{
                    $('#product_name_div').addClass('d-none');
                    $('#product_name').attr('data-rule-required', false);
                }
            });
            $('#city_id').select2({
                width: '100%',
            });
            $('#bank_name').select2({
                width: '100%',
            });
            $('#bank_city').select2({
                width: '100%',
            });
            $('#segments').select2({
                width: '100%',
            });
            $('#sub_segments').select2({
                width: '100%',
            });
            $('#average_shipment_duration_id').select2({
                width: '100%',
            });
            
            var weekly = [1, 2, 3, 4, 5, 6, 7];
            var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
            var cycle = '{!! $user_bank_default->invoicing_cycle_id !!}';
            cycle = parseInt(cycle);
            $('#invoicing_cycle').val(cycle);


            // $('#generation_date').select2({
            //     width: '100%',
            // });
            function generation(id,bool) {

                 if(id == 1 || id == 3) {
                     var gdate = parseInt('{!! $user_bank_default->generation_date !!}');
                     html = '<div class="form-group row"><div class="form-group col-md-9"><label>Generation Date</label><span class="danger">*</span><select name="generation_date" id="generation_date" data-rule-required="true" data-msg-required="Generation Date is required" class="select2 form-control required"><option value=""></option></select></div></div>';
                     $('#generation_div').html(html);

                     $('#generation_div').removeClass('d-none');

                     if(id == 3) {
                         $('#generation_date').prepend('<option value="" selected="selected"></option>').select2({
                             data: monthly,
                             width: '100%',
                             placeholder: 'Select Date',
                         });
                     }
                     else if(id == 1){
                         $('#generation_date').prepend('<option value="" selected="selected"></option>').select2({
                             data: weekly,
                             width: '100%',
                             placeholder: 'Select Date',
                         });
                     }
                     if(bool == 2){
                         $('#generation_date').val(gdate).trigger('change');
                     }
                 }
            }


            $('#edit-1').click(function () {
                $("#profile-form").show();
                $("#tabs").hide();
            });
            $('#edit-2').click(function () {
                $("#bank-form").show();
                $("#tabs").hide();
                generation(cycle,2);
            });

         
            $('#invoicing_cycle').select2({
                width: '100%',
            }).bind('change', function () {

                if (this.value == 1) {

                    var weekly = [1, 2, 3, 4, 5, 6, 7];
                    $('#generation_div').removeClass('d-none');
                    $('#generation_date').removeClass('d-none');
                    $('#generation_date').addClass('required');
                    $('#generation_date').empty().trigger('change');
                    generation(this.value,1);
                    $('#generation_date').select2({data: weekly, placeholder: 'Select Date'});
                } else if (this.value == 3) {

                    var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
                    $('#generation_div').removeClass('d-none');
                    $('#generation_date').removeClass('d-none');
                    $('#generation_date').addClass('required');
                    $('#generation_date').empty().trigger('change');
                    generation(this.value,1);
                    $('#generation_date').select2({data: monthly, placeholder: 'Select Date'});
                } else if (this.value == 2 || this.value == 4) {
                    $('#generation_div').addClass('d-none');
                    $('#generation_date').addClass('d-none');
                    $('#generation_date').removeClass('required');
                    generation(this.value,1);
                }
            });
            

            $('#segment_id').select2({
                placeholder: "Select Segment",
                width:'100%',
            }).bind('change', function() {
                var id = $(this).val();
                $(this).valid();
                console.log(id);
                $.ajax({
                    url: '{!! route('cod.get_sub_segment') !!}',
                    method: 'POST',
                    data: {
                        'segment_id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    console.log(data);
                   if (data.status == 0) {
                    // $('#sub_segment_id').prop("disabled", false);
                       $('#sub_segment_id').children().remove();
                                    $('#sub_segment_id').prepend('<option value="" selected="selected"></option>')
                                $.each(data.sub_segments, function (index, sub_segments) {
                                    $('#sub_segment_id').append('<option value="'+sub_segments.id+'" id="trax_center">'+sub_segments.name+'</option>')
                                });
                   }
                });
            });

            // $('#parent_product_id').select2({
            //     placeholder: "Select Parent Product Type",
            //     width:'100%',
            // }).bind('change', function() {
            //     var id = $(this).val();
            //     $.ajax({
            //         url: '{!! route('cod.get_products') !!}',
            //         method: 'GET',
            //         data: {
            //             'parent_product_id': id,
            //             '_token': '{{ csrf_token() }}'
            //         }
            //     }).done(function (data) {
            //         console.log(data);
            //        if (data.status == 0) {
            //         // $('#sub_segment_id').prop("disabled", false);
            //            $('#product_id').children().remove();
            //                         $('#product_id').prepend('<option value="" selected="selected"></option>')
            //                     $.each(data.products, function (index, products) {
            //                         $('#product_id').append('<option value="'+products.id+'" id="trax_center">'+products.product_name+'</option>')
            //                     });
            //        }
            //     });
            // });

            $('#sub_segment_id').select2({
                placeholder: "Select Sub Segment",
                width:'100%',
            });

            
            $('#cancel-button-profile').click(function () {
                $("#profile-form").hide();
                $("#profile-form").validate().resetForm();
                $("#profile-form")[0].reset();
                $("#profile-form").find(".danger").removeClass("danger");
                $("#city_id").val("{{$user->city_id}}").trigger('change');
                $("#product_id").val("{{$user->product_id}}").trigger('change');
                $("#segment_id").val(segment_id).trigger('change');
                $("#sub_segment_id").val(sub_segment_id).trigger('change');
                $("#tabs").show();
            });
            $('#cancel-button-bank').click(function () {
                $("#bank-form").hide();
                $("#bank-form").validate().resetForm();
                $("#bank-form")[0].reset();
                $("#bank-form").find(".danger").removeClass("danger");
                $("#bank_name").val("{{$bank->id}}").trigger('change');
                $("#bank_city").val("{{$bank_city->id}}").trigger('change');
                $("#payment_mode").val("{{$user_bank_default->payment_mode}}").trigger('change');
                $("#tabs").show();
                generation(cycle)
            });

            $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
            $("input[name='phone'],input[name='phone2']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $("input[name='billing_person_phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $("input[name='ntn_no']").inputmask({'mask': "*******-*", 'clearIncomplete': true});

            
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: false, scrollY: '500px',
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                ajax: '{{route('admin.accounts.get.pickups',['user_id'=>$user->id])}}',
                rowId: 'id',
                order : [1,'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'id'},
                    {data: 'pickup_address', name: 'pickup_address'},
                    {data: 'poc', name: 'poc'},
                    {data: 'vendor', name: 'vendor'},
                    {data: 'pickup_brand_name', name: 'pickup_brand_name'},
                    {data: 'phone', name: 'phone'},
                    {data: 'city_name', name: 'c.name'},
                    {data: 'email', name: 'email'},
                    {data: 'status', name: 'status',class:'status',orderable: false}
                ],
                rowCallback: function(row, data, index) {

                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="2">Default Address</option>' +
                        '<option value="0">Disabled</option>' +
                        '<option value="1">Enabled</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });


            $( "#profile-form" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    form.submit();

                }
            });

           
            $( "#bank-form" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    form.submit();

                }
            });

            $('body').on('click','button.addEmail', function () {
                $('#AddEmailsModal').modal('show');
                var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';
                var add_select = $('#add_notification_emails #add_email_address').selectize({
                    placeholder: 'Email Addresses*',
                    delimiter: ',',
                    createOnBlur: true,
                    preload: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function(dropdown) {
                        dropdown.remove();
                    },

                    create: function(input) {
                        if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                        var error = "Invalid Email Address!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        return false;
                    }

                });
            });


            $('body').on('click','button.editEmail', function () {
                $('#EditEmailsModal').modal('show');
                var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';
                var select = $('#edit_notification_emails #edit_email_address').selectize({
                    placeholder: 'Email Addresses*',
                    delimiter: ',',
                    createOnBlur: true,
                    preload: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function(dropdown) {
                        dropdown.remove();
                    },

                    create: function(input) {
                        if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                        var error = "Invalid Email Address!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        return false;
                    }

                });
            });


            $('#addEmails').on('click', function (e) {
                e.preventDefault();
                var emails = $('#AddEmailsModal #add_email_address').val();
                if(emails != ''){
                    $('form#add_notification_emails').submit();
                }else{
                    var error = "No Email Address selected, Please select at-least one email address!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('#editEmails').on('click', function (e) {
                e.preventDefault();
                var emails = $('#EditEmailsModal #edit_email_address').val();
                if(emails != ''){
                    $('form#edit_notification_emails').submit();
                }else{
                    var error = "No Email Address selected, Please select at-least one email address!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            })

            $('#iban').inputmask({
                mask: 'R',
                repeat: 24,
                greedy: false,
                definitions: {
                    R: {
                        validator: '[a-zA-Z0-9]',
                    },
                },
            });

            $('#iban').on('input', function (e) {
                var iban = $(this).val().replace(/\s+/g, '').toUpperCase(); // Remove white spaces and convert to uppercase
                var defaultPrefix = 'PK';

                if (!iban.startsWith(defaultPrefix)) {
                    iban = defaultPrefix + iban.substring(defaultPrefix.length);
                }

                if (iban.length > 2 && !iban.startsWith(defaultPrefix)) {
                    $(this).val(defaultPrefix + iban.substring(defaultPrefix.length));
                } else {
                    $(this).val(iban);
                }

                if (iban.length !== 24 || !iban.startsWith(defaultPrefix)) {
                    $('#iban_no_error').show();
                } else {
                    $('#iban_no_error').hide();
                }
            });

        });
    </script>
@endsection