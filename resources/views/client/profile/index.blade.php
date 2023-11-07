@extends('client.layout.master')

@section('title', 'User Profile')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    User Information
                </h1>
                <div class="card">
                    @include('client.inc.messages')
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
                                            @if($user->brand_name != NULL)
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

                                            
                                            {{--@if($user->account_type_id == 2)
                                                <tr>
                                                    <td style="vertical-align: middle;"><b>Invoice Grouping</b></td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <label for="switchery" class="font-medium-2 text-bold-600 mr-1">Single</label>
                                                            @if($user->invoice_group_by)
                                                                <input type="checkbox" id="invoice_group_switch" data-size="xs" class="switchery igb-switch" checked/>
                                                            @else
                                                                <input type="checkbox" id="invoice_group_switch" data-size="xs" class="switchery igb-switch"/>
                                                            @endif
                                                            <label for="switchery" class="font-medium-2 text-bold-600 ml-1">Origin Wise</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif--}}
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
                                    <div class="row justify-content-center">
                                        <div class="col-3">
                                            <button id="edit-1" type="button" class="btn btn-primary btn-block">Edit</button>
                                        </div>
                                    </div>
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
                                                <th class="border-primary border-darken-1">Phone Number</th>
                                                <th class="border-primary border-darken-1">City</th>
                                                <th class="border-primary border-darken-1">City Area</th>
                                                <th class="border-primary border-darken-1">Email Address</th>
                                                <th class="border-primary border-darken-1">Brand Name</th>
                                                <th class="border-primary border-darken-1">Status</th>
                                                <th class="border-primary border-darken-1"></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="linkOpt" role="tabpanel" aria-labelledby="linkOpt-tab" aria-expanded="false">
                                    <div class="table-responsive">
                                        <br>
                                        <table class="table table-bordered datatable" id="bank_datatable" style="z-index: 3;">
                                            <thead>
                                            <tr role="row" class="bg-primary white">

                                                <th class="border-primary border-darken-1">S.No</th>
                                                <th class="border-primary border-darken-1">Bank Name</th>
                                                <th class="border-primary border-darken-1">Bank Branch</th>
                                                <th class="border-primary border-darken-1">Bank City</th>
                                                <th class="border-primary border-darken-1">Account Number</th>
                                                <th class="border-primary border-darken-1">Account Title</th>
                                                <th class="border-primary border-darken-1">IBAN No.</th>
                                                <th class="border-primary border-darken-1"></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
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
                            <form id="main-form" class="form form-horizontal" style="display: none" method="post" action="{{route('cod.update.profile')}}">
                                @csrf
                                <div class="form-body">
                                    <h4 class="form-section">Profile Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>Person of Contact:</label>
                                                    <span class="danger">*</span>
                                                    <input type="text" id="poc" class="form-control border-primary" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters" data-rule-required="true" data-msg-required="Person of Contact is required" value="{{$user->poc}}" name="poc" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>Phone Number 1:</label>
                                                    <span class="danger">*</span>
                                                    <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone Number is required" value="{{$user->phone}}" name="phone" required data-rule-remote="{{ route('cod.profile.shipper_phone_unique', ['id' => $user->id]) }}" data-msg-remote="Phone must be unique">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>Phone Number 2:</label>
                                                    <input type="text" id="phone2" class="form-control border-primary"  value="{{$user->phone2}}" name="phone2">
                                                    {{--<input type="hidden" name="user_id" value="{{$user->id}}">--}}
                                                </div>
                                            </div>
                                        </div>
                                        @if(session('user_type') == 1)
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>Email Address:</label>
                                                        <span class="danger">*</span>
                                                        <input type="email" id="email" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone Number is required" value="{{$user->email}}" name="email" required>
                                                        {{--<input type="hidden" name="user_id" value="{{$user->id}}">--}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if(session('user_type') == 1)
                                        <div class="row">
                                            <div class="col-2">
                                                <button id="edit-3" type="button" class="btn btn-primary btn-block">Change Password</button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-actions right">
                                        <button id="cancel-button" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <form id="password-form" class="form form-horizontal" style="display: none" method="post" action="{{route('cod.update.profile.password')}}">
                                @csrf
                                <div class="form-body">
                                    <h4 class="form-section">Change Password</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label for="password">Enter Password:<span class="danger">*</span>
                                                    </label>
                                                    <div class="form-group position-relative">
                                                        <input type="password" class="form-control required" id="new_password" placeholder="Minimum 6 Character" value="" name="password" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 Characters">
                                                        <div class="form-control-position" id="peye">
                                                            <i class="la la-eye success"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label for="password">Confirm Password:<span class="danger">*</span>
                                                    </label>
                                                    <div class="form-group position-relative">
                                                        <input type="password" class="form-control required" id="confirm_password" placeholder="Minimum 6 Character" value="" name="confirm_password">
                                                        <div class="form-control-position" id="cpeye">
                                                            <i class="la la-eye success"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <button id="cancel-button2" type="button" class="btn btn-warning mr-1">
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



            </div>
        </div>
    </div>


    {{--Add Stock Modal--}}
    <div class="modal fade text-left" id="AddPickup" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddPickup"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Pickup Address</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_pickup_form" action="{{route('cod.add.pickup')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            {{--<div class="row justify-content-center">--}}
                            {{--<div class="col-4 form-group">--}}
                            {{--<input type="text" name="invoice_number" id="add_stock_invoice" class="form-control" placeholder="Invoice Number *" data-rule-required="true" data-msg-required="This field is required">--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="row">
                                <div class="col-6 form-group">
                                    <textarea type="text" name="pickup_address" id="pickup_address" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters" class="form-control numeric flyer" data-rule-required="true" data-msg-required="Pickup Address is required" placeholder="Address" required></textarea>
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="pickup_brand_name" id="pickup_brand_name" class="form-control numeric flyer"  placeholder="Brand Name" >
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="phone" id="phone" class="form-control numeric flyer" data-rule-required="true" data-msg-required="Phone Number is required" placeholder="Phone Number" required>
                                </div>
                                
                                <div class="col-6 form-group">
                                    <input type="text" name="poc" id="poc" class="form-control numeric flyer" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters" data-rule-required="true" data-msg-required="Person of Contact is required" placeholder="Person of Contact" required>
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="vendor" id="vendor" class="form-control numeric flyer" data-rule-maxlength="100" data-msg-maxlength="Vendor can be maximum 100 characters" placeholder="Vendor">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="email" name="email" id="add_stock_boxes" class="form-control numeric flyer" data-rule-required="true" data-msg-required="Email Address is required" placeholder="Email Address" required>
                                </div>
                                <div class="col-6 form-group">
                                    <select name="city_id" id="city_id" class="select2 form-control required" data-rule-required="true" data-msg-required="City is required" style="width: 100%" required>
                                        @foreach($pickup_city_list as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddPickup" type="submit" class="btn btn-primary btn-block">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditPickup" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditPickup"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Pickup Address</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_pickup_form" action="{{route('cod.edit.pickup')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row">
                                <input type="hidden" name="id" id="edit_user_shipping_info_id" value="">
                                <div class="col-6 form-group">
                                    <textarea type="text" name="pickup_address" id="edit_pickup_address" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters" class="form-control numeric flyer" data-rule-required="true" data-msg-required="Pickup Address is required" placeholder="Address" required></textarea>
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="pickup_brand_name" id="edit_pickup_brand_name" class="form-control numeric flyer"  placeholder="Brand Name" >
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="phone" id="edit_phone" class="form-control numeric flyer" data-rule-required="true" data-msg-required="Phone Number is required" placeholder="Phone Number" required>
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="poc" id="edit_poc" class="form-control numeric flyer" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters" data-rule-required="true" data-msg-required="Person of Contact is required" placeholder="Person of Contact" required>
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="vendor" id="edit_vendor" class="form-control numeric flyer" data-rule-maxlength="100" data-msg-maxlength="Vendor can be maximum 100 characters" placeholder="Vendor">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="email" name="email" id="edit_email" class="form-control numeric flyer" data-rule-required="true" data-msg-required="Email Address is required" placeholder="Email Address" required>
                                </div>
                                <div class="col-6 form-group">
                                    <select name="city_id" id="edit_city_id" class="select2 form-control required" data-rule-required="true" data-msg-required="City is required" style="width: 100%" required>
                                        @foreach($pickup_city_list as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="editPickup" type="submit" class="btn btn-primary btn-block">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Add Stock Modal--}}

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
                    <form id="edit_notification_emails" action="{{route('cod.edit.emails')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 form-group">
                                    <input name="email_address" id="email_address" class="email_address" data-tags-input-name="email_address" data-rule-required="true" data-msg-required="Email Address is required" value="{{$email_ids}}">

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
                    <form id="add_notification_emails" action="{{route('cod.add.emails')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 form-group">
                                    <input name="email_address" id="email_address" class="email_address" data-tags-input-name="email_address" data-rule-required="true" data-msg-required="Email Address is required" value="">

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
    <!-- default modal -->
    <div class="modal fade text-left" id="DefaultBankModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DefaultBankModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Make Default Bank</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="default_bank_form" action="{{route('cod.default.bank')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-5 text-center">
                                    <div class="form-group">
                                        <label for="default_type_checkbox" class="font-medium-2 text-bold-600 mr-1">Permanent</label>
                                        <input type="checkbox" name="default_type_checkbox" id="default_type_checkbox" class="switchery default_type_checkbox" data-color="info" data-size="sm" data-switchery="true">
                                        <label for="default_type_checkbox" class="font-medium-2 text-bold-600 ml-1">Temporary</label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="bank_info_id" id="bank_info_id">
                            <div class="row mb-2 d-none" id="day_select_div">
                                <div class="col-12 form-group">
                                    <select name="day_select" id="day_select" class="select2 form-control required" data-rule-required="true" data-msg-required="Day is required" style="width: 100%" required>
                                        @for($i = 1; $i < 31; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
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
    <!-- Add Pin modal -->
    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Enter Verification Pin</h4>
                </div>

                <div class="modal-body">
                    <input name="pincode" id="pincode" class="form-control" maxlength="4" placeholder="Enter Pin Code"/>
                    <input type="hidden" id="code" name="code"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="verify_pincode">Verify</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- default modal -->
    <!-- ADD Bank Modal -->
    <div class="modal fade text-left" id="AddBankModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddBankModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Add Bank</h4>
                </div>

                <form id="add_bank_form" action="{{route('cod.add.bank')}}" method="post">
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="bank_select" id="bank_select" class="select2 form-control" data-rule-required="true" data-msg-required="Bank is required">

                                            @foreach($banks as $bank)
                                                <option value="{{$bank->id}}">{{$bank->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="bank_branch" placeholder="Branch Name*" data-rule-required="true" data-msg-required="Branch Name is required">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control required" name="account_no" placeholder="Account Number*" data-rule-required="true" data-msg-required="Account No. is required">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type='text' class="form-control" name="account_title" placeholder="Account Title*" data-rule-required="true" data-msg-required="Account Title is required">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="(e.g: PK37MEZN0001220100004069)" name="iban_no" data-rule-required="true" data-msg-required="IBAN is required" data-rule-maxlength="24">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">

                                        <select name="bank_city" id="bank_city" class="select2 form-control" data-rule-required="true" data-msg-required="Bank City is required">
                                            @foreach($cities_list as $bank_city)
                                                <option value="{{$bank_city->id}}">{{$bank_city->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="addBank">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADD Bank Modal -->
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/selectize/selectize.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/switchery.min.css')}}">


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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/switchery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/switchery.min.js')}}" type="text/javascript"></script>




    <script type="text/javascript">
        $(document).ready(function() {
            $('#pincode').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 4,
                'min': 0.00,
                'max': 1000000.00
            });
            $('#edit-1').click(function () {
                $("#main-form").show();
                $("#tabs").hide();
                $("#password-form").hide();
            });
            $('#edit-2').click(function () {
                $("#main-form").show();
                $("#tabs").hide();
                $("#password-form").hide();
            });
            $('#edit-3').click(function () {
                $("#password-form").show();
                $("#main-form").hide();
                $("#tabs").hide();
            });

            $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});
            $('#cpeye').on('mousedown',function(){$('input[name="confirm_password"]').attr('type','text')}).on('mouseup',function(){$('input[name="confirm_password"]').attr('type','password')});
            $('#cancel-button').click(function () {
                $("#password-form").hide();
                $("#main-form").hide();
                $("#main-form").validate().resetForm();
                $("#main-form")[0].reset();
                $("#main-form").find(".danger").removeClass("danger");
                $("#phone").val("");
                $("#phone").val("{{$user->phone}}");
                $("#phone2").val("");
                $("#phone2").val("{{$user->phone2}}");
                $("#poc").val("");
                $("#poc").val("{{$user->poc}}");
                $("#email").val("");
                $("#email").val("{{$user->email}}");
                $("#new_password").val("");
                $("#tabs").show();
            });

            $('#cancel-button2').click(function () {
                $("#password-form").hide();
                $("#main-form").hide();
                $("#main-form").validate().resetForm();
                $("#main-form")[0].reset();
                $("#main-form").find(".danger").removeClass("danger");
                $("#phone").val("");
                $("#phone").val("{{$user->phone}}");
                $("#phone2").val("");
                $("#phone2").val("{{$user->phone2}}");
                $("#poc").val("");
                $("#poc").val("{{$user->poc}}");
                $("#email").val("");
                $("#email").val("{{$user->email}}");
                $("#new_password").val("");
                $("#tabs").show();
            });



            // $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
            $("input[name='phone'],input[name='phone2']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            // $("input[name='ntn_no']").inputmask({'mask': "9999999-9", 'clearIncomplete': true});



            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add Pickup Address',
                        className: 'btn btn-primary add_stock',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddPickup').modal('show');
                            $("#add_pickup_form").validate().resetForm();
                            $("#add_pickup_form")[0].reset();
                            $("#add_pickup_form").find(".danger").removeClass("danger");
                        }
                    }
                ],
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                ajax: '{{route('cod.get.pickups',['user_id'=>$user->id])}}',
                rowId: 'id',
                order:[1,'desc'],
                columns: [
                    {orderable: false,searchable: false,data: 'serial_number',  name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'id'},
                    {data: 'pickup_address', name: 'pickup_address'},
                    {data: 'poc', name: 'poc'},
                    {data: 'vendor', name: 'vendor'},
                    {data: 'phone', name: 'phone'},
                    {data: 'city_name', name: 'c.name'},
                    {data: 'city_area_name', name: 'ca.name'},
                    {data: 'email', name: 'email'},
                    {data: 'pickup_brand_name', name: 'pickup_brand_name'},
                    {data: 'status',orderable: false, name: 'status',class:'status'},
                    {data: 'action',orderable: false, name: 'action',class:'action'}
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
            $('#link-tab').on('click', function () {
                table.columns.adjust().draw();
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('enable')) {
                    var status  = "enable";
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to enable this Pickup Address',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if(confirm){
                            if(id){
                                $.ajax({
                                    url: '{!! route('cod.change.pickup.status') !!}',
                                    method: 'POST',
                                    data: {
                                        'id':id,
                                        'status':status,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function (data) {
                                    if(data.status === 1){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }

                                });
                            }
                        }
                    });
                }
                else if ($(this).hasClass('disable')) {
                    var status  = "disable";
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Disable this Pickup Address',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if(confirm){
                            if(id){
                                $.ajax({
                                    url: '{!! route('cod.change.pickup.status') !!}',
                                    method: 'POST',
                                    data: {
                                        'id':id,
                                        'status':status,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function (data) {
                                    if(data.status == 1){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }

                                });
                            }
                        }
                    });

                }



                if ($(this).hasClass('default')) {
                    var status  = "default";
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to make this default Pickup Address',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if(confirm){
                            if(id){
                                $.ajax({
                                    url: '{!! route('cod.change.pickup.status') !!}',
                                    method: 'POST',
                                    data: {
                                        'id':id,
                                        'status':status,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function (data) {
                                    if(data.status == 1){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }

                                });
                            }
                        }
                    });

                }

                if ($(this).hasClass('edit')) {
                    var id = parseInt($(this).parents('tr').attr('id'));
                    var pickup_address = table.row($(this).parents('tr')).data().pickup_address;
                    var pickup_brand_name = table.row($(this).parents('tr')).data().pickup_brand_name;
                    
                    var phone_number = table.row($(this).parents('tr')).data().phone;
                    var poc = table.row($(this).parents('tr')).data().poc;
                    var vendor = table.row($(this).parents('tr')).data().vendor;
                    var email_address = table.row($(this).parents('tr')).data().email;
                    var city_id = table.row($(this).parents('tr')).data().city_id;

                    $('#edit_user_shipping_info_id').val(id);
                    $('#edit_pickup_address').val(pickup_address);
                    $('#edit_pickup_brand_name').val(pickup_brand_name);
                    
                    $('#edit_phone').val(phone_number);
                    $('#edit_poc').val(poc);
                    $('#edit_vendor').val(vendor);
                    $('#edit_email').val(email_address);
                    $('#edit_city_id').val(city_id).trigger('change');
                    $('#EditPickup').modal('show');
                    
                }
            });


            $('#city_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select City',
                dropdownParent:$('#add_pickup_form')
            });
            $('#edit_city_id').select2({
                placeholder:'Select City',
                dropdownParent:$('#edit_pickup_form')
            });


            $( "#add_pickup_form" ).validate({
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
            $( "#edit_pickup_form" ).validate({
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
            $( "#main-form" ).validate({
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
            $( "#password-form" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var new_password = $('#new_password').val();
                    var confirm_password = $('#confirm_password').val();
                    if(new_password === confirm_password){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update password',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function (confirm) {
                            if(confirm){
                                form.submit();
                            }
                        });
                    }
                    else{
                        var error = "The password and confirmation password do not match";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                }
            });

            $('body').on('click','button.addEmail', function () {
                $('#AddEmailsModal').modal('show');
                var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';
                var select = $('#add_notification_emails #email_address').selectize({
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
                var select = $('#edit_notification_emails #email_address').selectize({
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
                var emails = $('#AddEmailsModal #email_address').val();
                if(emails != ''){
                    $('form#add_notification_emails').submit();
                }else{
                    var error = "No Email Address selected, Please select at-least one email address!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('#editEmails').on('click', function (e) {
                e.preventDefault();
                var emails = $('#EditEmailsModal #email_address').val();
                if(emails != ''){
                    $('form#edit_notification_emails').submit();
                }else{
                    var error = "No Email Address selected, Please select at-least one email address!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            var default_type_checkbox = document.querySelector('.switchery.default_type_checkbox');
            var default_type_checkbox_init = new Switchery(default_type_checkbox);
            $('.default_type_checkbox').on('change',function(){

                var dtc = document.querySelector('.switchery.default_type_checkbox');
                if (dtc.checked === true) {
                    $('#day_select_div').removeClass('d-none');

                } else if (dtc.checked === false) {
                    $('#day_select_div').addClass('d-none');
                }
            });

            $('#day_select').prepend('<option value="" selected></option>').select2({
                placeholder: "Select Day",
                width:'100%'
            });
            var btable = $('#bank_datatable').DataTable({
                // dom:'ltipr',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if(session('account_type') == 1)
                    {
                        text: '<i class="la la-cancel"></i> Add Bank',
                        className: 'btn btn-primary add_bank',
                        enabled: true,
                        action: function (e, dt, node, config) {

                            $('#AddBankModal').modal('show');

                            /**/

                        }
                    }
                    @endif
                ],
                scrollX: true, scrollY: '500px',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                ajax: '{{route('cod.get.banks',['user_id'=>$user->id])}}',
                rowId: 'bank_row_id',
                order:[1,'desc'],
                columns: [
                    {orderable: false,searchable: false,data: 'serial_number',  name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'bank_name', name: 'bl.name'},
                    {data: 'bank_branch', name: 'user_bank_infos.bank_branch'},
                    {data: 'city', name: 'c.name'},
                    {data: 'account_no', name: 'user_bank_infos.account_no'},
                    {data: 'account_title', name: 'user_bank_infos.account_title'},
                    {data: 'iban',orderable: false, name: 'user_bank_infos.iban',class:'status'},
                    {data: 'action',orderable: false, name: 'action',class:'action'}
                ],
                rowCallback: function(row, data, index) {
                    var info = btable.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
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

                    this.api().table().columns.adjust();
                }
            });
            $('#linkOpt-tab').on('click', function () {
                btable.columns.adjust().draw();
            });
            $('#bank_datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('default')) {
                    $('#bank_info_id').val(id);
                    $('#DefaultBankModal').modal('show');

                }


            });
            $( "#default_bank_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your default bank is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();


                }
            });

            $("#bank_select").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Bank",
                width:'100%',
                dropdownParent:$('#AddBankModal')
            });
            $("#bank_city").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Bank City",
                width:'100%',
                dropdownParent:$('#AddBankModal')
            });


            $( "#add_bank_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#AddBankModal').modal('hide');
                    $('#demoModal').modal('show');
                    $.ajax({
                        url: "{{ route('cod.verify.pin.code') }}",
                        type:'post',
                        data:{action:'verify_pincode', '_token':"{{ csrf_token() }}"},
                        success:function(data){
                            $('#code').val(JSON.parse(data).code);
                        },
                        error: function(data){

                        }
                    });

                    $(document).on('click', '#verify_pincode', function(){
                        var pincode = $('#pincode').val();
                        console.log(pincode);
                        if(!pincode)
                        {
                            toastr.info('Please input Pin code', 'Info!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        else{
                            var code    = $('#code').val();
                            if(pincode == code)
                            {
                                $('#AddBankModal').modal('show');
                                swal({
                                    title: 'Please Wait!',
                                    text: 'Your bank is being added!',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });

                                form.submit();
                            }
                            else if(verify_pincode != pincode){
                                toastr.error('Verify Code Does not Matched!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    });

                }
            });

            $('#showBankModel').click(function () {
                $('#AddBankModal').modal('show')
            });
            /*var invoice_switch = document.querySelector('.igb-switch');
            var switchery = new Switchery(invoice_switch,{ size: 'small'});

            $('#invoice_group_switch').on('change',function(){
                var invoice_switch_btn = document.querySelector('.switchery.igb-switch');
                if (invoice_switch_btn.checked === true) {
                    update_invoicing_sort(true);

                } else if (invoice_switch_btn.checked === false) {
                    update_invoicing_sort(false);
                }
            });

            function update_invoicing_sort(action){
                $.ajax({
                    url: '{!! route('cod.update_invoice_sort') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'action': action,
                    }
                }).done(function(data){
                    if(data.status){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            }*/


        });
    </script>
@endsection