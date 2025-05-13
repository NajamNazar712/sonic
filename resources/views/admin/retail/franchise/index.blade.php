@extends('admin.layout.master')

@section('title', 'Franchise')

@section('content')

    <style>
        .add_franchise_modal, .edit_franchise_modal {
            max-width: 1300px;
        }
    </style>

    <h1 class="mb-1">
        Franchise
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">Phone Number</th>
                        <th class="border-primary border-darken-1">Email</th>
                        <th class="border-primary border-darken-1">CNIC</th>
                        <th class="border-primary border-darken-1">Default Hub</th>
                        <th class="border-primary border-darken-1">Created Date/Time</th>
                        <th class="border-primary border-darken-1">Updated Date/Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Franchise Code</th>
                        <th class="border-primary border-darken-1">Location</th>
                        <th class="border-primary border-darken-1">Discount</th>
                        <th class="border-primary border-darken-1">Insurance</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_franchise" role="dialog" aria-labelledby="add_franchise_title" aria-hidden="true">
        <div class="modal-dialog add_franchise_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Add Franchise</h4>

                    <button type="button" class="close modal_close_btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_franchise_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.franchise.add') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field()  }}

                        <div class="container">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Franchise Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-remote="{{ route('admin.retail.franchise.name') }}" data-msg-remote="Name must be unique">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="phone_number" id="phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" value="" autocomplete="nope">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="cnic" id="cnic" class="form-control cnic" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
                                    </div>

                                    <div class="col text-center">
                                        <div class="form-group">
                                            <label for="cnic_status" class="mr-1">CNIC Active</label>
                                            <input type="checkbox" id="cnic_status" name="cnic_status" class="switchery" data-color="success" data-size="sm">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <select name="hub" id="hub" class="form-control select2" data-rule-required="true" data-msg-required="Default Hub is required">
                                            @foreach($hubs as $hub)
                                                <option value="{{$hub->id}}"> {{$hub->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="lat" id="lat" class="form-control lat" placeholder="Latitude*" data-rule-required="true" data-msg-required="Latitude is required">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="long" id="long" class="form-control long" placeholder="Longitude*" data-rule-required="true" data-msg-required="Longitude is required">
                                    </div>
                                    {{-- <div class="form-group">
                                        <input type="number" name="discount" id="discount" class="form-control discount" placeholder="Discount" max="100">
                                    </div>--}}
            
                                    <div class="input-group mb-2 form-group">
                                        <input type="text" name="insurance" id="insurance" class="form-control insurance" placeholder="Insurance*"  value="" max="100" min="1"
                                            data-rule-required="true" data-msg-required="Insurance is required">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" name="discount" id="discount" class="form-control discount" placeholder="Discount"  value="" max="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
            
                                    {{-- <div class="input-group mb-2">
                                        <input type="text" name="franchise_gst" id="commission_percentage" class="form-control commission_percentage" placeholder="GST*"  value="" max="100" data-rule-required="true" data-msg-required="GST is required">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div> --}}
            
                                    <div class="input-group mb-2">
                                        <input type="text" name="franchise_withholding" id="withholding_tax_percentage" class="form-control withholding_tax_percentage" placeholder="Withholding Tax*"  value="" max="100" data-rule-required="true" data-msg-required="Withholding Tax is required">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
            
                                    <div class="input-group mb-2">
                                        <input type="number" name="franchise_deduction" id="deduction_percentage" class="form-control deduction_percentage" placeholder="Deduction"  value="" max="100" data-rule-required="false" data-msg-required="Commission Deduction is required">
                                        {{-- <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"></span>
                                        </div> --}}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <h2>Commission</h2>
                                    <div class="row mt-2">
                                        <div class="col-5">
                                            <div class="form-group">
                                                <select name="retail_shipping_mode_id[]" id="retail_shipping_mode_id" class="select2 form-control retail_shipping_mode_id" data-rule-required="true" data-msg-required="Please choose a Product">
                                                    @foreach($shipping_modes as $shipping_mode)
                                                        <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-5">
                                            <div class="form-group">
                                                <div class="input-group mb-2">
                                                    <input type="text" name="product_percentage[]" id="product_percentage" class="form-control product_percentage" placeholder="Commission"  value="" max="100">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <input type="button" class="btn btn-primary" id="retail_product_add_btn" value="Add">
                                        </div>
                                    </div>

                                    <span id="error_message" class="text-danger"></span>

                                    <div class="row" id="tableRow" style="display: none;">
                                        <div class="col">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Selected Option</th>
                                                        <th>Commission Percentage</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="attachment_1">Franchise Agreement*</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept=".doc,.docx,.pdf" data-rule-required="true" data-msg-required="Atleast 1 attachment is required">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_2">Cheque Images</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2" accept=".doc,.docx,.pdf">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_3">Locaiton Pictures</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3" accept="image/*,.doc,.docx,.pdf">
                                    </div>

                                    <div class="form-group">
                                        <label for="attachment_4">Miscellaneous</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4" accept="image/*,.doc,.docx,.pdf">
                                    </div>

                                    <div class="form-group">
                                        <label for="attachment_5">Attachment 5</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5" accept="image/*,.doc,.docx,.pdf">
                                    </div>

                                    <div class="mt-4">
                                        <div class="form-group">
                                            <input type="text" name="security_deposit" id="security_deposit" class="form-control security_deposit" placeholder="Security Deposit*" value="" data-rule-required="true" data-msg-required="Security Deposit is required">
                                        </div>
    
                                        <div class="form-group">
                                            <input type="text" name="license_fees" id="license_fees" class="form-control license_fees" placeholder="License Fees*" value="" data-rule-required="true" data-msg-required="License Fees is required">
                                        </div>

                                        <div class="form-group">
                                            <select name="bank_id" id="bank_id" class="form-control select2" data-rule-required="true" data-msg-required="Please select a bank" required>
                                                @foreach ($bank_list as $list)
                                                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="text" name="security_cheque_number" id="security_cheque_number" class="form-control" placeholder="Security Deposit Cheque Number*" value="" data-rule-required="true" data-msg-required="Security Deposit Cheque Number is required">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="license_cheque_number" id="license_cheque_number" class="form-control" placeholder="License Fees Cheque Number*" value="" data-rule-required="true" data-msg-required="License Fees Cheque Number is required">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Save</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_franchise" role="dialog" aria-labelledby="edit_franchise_title" aria-hidden="true">
        <div class="modal-dialog edit_franchise_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_remarks_title">Edit Franchise</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_franchise_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.franchise.edit') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field()  }}
                        <div class="row">
                            <div class="col-6">
                                <input type="hidden" name="franchise_id" id="franchise_id" value="">
                                <div class="form-group">
                                    <input type="text" name="name" id="edit_name" class="form-control" placeholder="Franchise Name*" data-rule-required="true" data-msg-required="Name is required" value="">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="phone_number" id="edit_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required" value="">
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" value="">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="cnic" id="edit_cnic" class="form-control cnic" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required" value="">
                                </div>

                                <div class="col text-center">
                                    <div class="form-group">
                                        <label for="edit_cnic_status" class="mr-1">CNIC Active</label>
                                        <input type="checkbox" id="edit_cnic_status" name="cnic_status" class="switchery" data-color="success" data-size="sm" data-switchery="true">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="lat" id="edit_lat" class="form-control lat" placeholder="Latitude*" data-rule-required="true" data-msg-required="Latitude is required" value="">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="long" id="edit_long" class="form-control long" placeholder="Longitude*" data-rule-required="true" data-msg-required="Longitude is required" value="">
                                </div>

                                <div class="input-group mb-2">
                                    <input type="text" name="edit_insurance" id="edit_insurance" class="form-control edit_insurance" placeholder="Insurance*"  value="" max="100"
                                        data-rule-required="true" data-msg-required="Insurance is required" min="1">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="text" name="discount" id="edit_discount" class="form-control edit_discount" placeholder="Discount"  value="" max="100">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>

                                {{-- <div class="input-group mb-2">
                                    <input type="text" name="franchise_gst" id="commission_percentage_edit" class="form-control commission_percentage" placeholder="GST*"  value="" max="100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="GST">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div> --}}

                                <div class="input-group mb-2">
                                    <input type="text" name="franchise_withholding" id="withholding_tax_percentage_edit" class="form-control withholding_tax_percentage" placeholder="Withholding Tax*"  value="" max="100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Withholding Tax">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>

                                <div class="input-group mb-2">
                                    <input type="text" name="franchise_deduction" id="deduction_percentage_edit" class="form-control deduction_percentage" placeholder="Commission GST Deduction*"  value="" max="100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Deduction">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <h2>Commission</h2>
                                <div class="row mt-2">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <select name="retail_shipping_mode_id[]" id="retail_shipping_mode_id_edit" class="select2 form-control retail_shipping_mode_id_edit" data-rule-required="true" data-msg-required="Please choose a shipping mode">
                                                @foreach($shipping_modes as $shipping_mode)
                                                    <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <div class="form-group">
                                            <div class="input-group mb-2">
                                                <input type="text" name="product_percentage[]" id="product_percentage_edit" class="form-control product_percentage_edit" placeholder="Commission"  value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <input type="button" class="btn btn-primary" id="edit_retail_product_add_btn" value="Add">
                                    </div>
                                </div>
                                <span id="error_message_edit" class="text-danger"></span>
        
                                <div class="row" id="editTableRow" style="display: none;">
                                    <div class="col">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Selected Option</th>
                                                    <th>Commission Percentage</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="editTableBody"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="attachment_1">Franchise Agreement*</label>
                                    <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept=".doc,.docx,.pdf">
                                    <a id="attachment_1_filename" target="_blank"></a>
                                </div>
        
                                <div class="form-group">
                                    <label for="attachment_2">Cheque Images</label>
                                    <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2" accept=".doc,.docx,.pdf">
                                    <a id="attachment_2_filename" target="_blank"></a>
                                </div>
        
                                <div class="form-group">
                                    <label for="attachment_3">Locaiton Pictures</label>
                                    <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3" accept="image/*,.doc,.docx,.pdf">
                                    <a id="attachment_3_filename" target="_blank"></a>
                                </div>
        
                                <div class="form-group">
                                    <label for="attachment_4">Miscellaneous</label>
                                    <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4" accept="image/*,.doc,.docx,.pdf">
                                    <a id="attachment_4_filename" target="_blank"></a>
                                </div>
        
                                <div class="form-group">
                                    <label for="attachment_5">Attachment 5</label>
                                    <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5" accept="image/*,.doc,.docx,.pdf">
                                    <a id="attachment_5_filename" target="_blank"></a>
                                </div>

                                <div class="">
                                    <div class="form-group">
                                        <input type="text" name="security_deposit" id="edit_security_deposit" class="form-control edit_security_deposit" placeholder="Security Deposit*"  value="" data-rule-required="true" data-msg-required="Security Deposit is required">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="license_fees" id="edit_license_fees" class="form-control edit_license_fees" placeholder="License Fees*"  value="" data-rule-required="true" data-msg-required="License Fees is required">
                                    </div>

                                    <div class="form-group">
                                        <select name="bank_id" id="edit_bank_id" class="form-control select2" data-rule-required="true" data-msg-required="Please select a bank" required>
                                            @foreach ($bank_list as $list)
                                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="text" name="security_cheque_number" id="edit_security_cheque_number" class="form-control edit_cheque_number" placeholder="Security Deposit Cheque Number*" value="" data-rule-required="true" data-msg-required="Cheque Number is required">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="license_cheque_number" id="edit_license_cheque_number" class="form-control edit_cheque_number" placeholder="License Fees Cheque Number*" value="" data-rule-required="true" data-msg-required="Cheque Number is required">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary edit" value="Add">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logs_modal" role="dialog" aria-labelledby="logs_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Admin Name</th>
                                <th>Changed Fields</th>
                                <th>Date of change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Log entries will be injected here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#bank_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select a Bank*',
                width: '100%',
                allowClear: true
            });

            $('.phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('.cnic').inputmask({
                'mask': '99999-9999999-9',
                'clearIncomplete': true
            });

            $('#add_franchise_form #hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Default Hub',
                allowClear:true
            });

            $('.lat').inputmask({
                'alias': 'decimal',
                'allowMinus': true,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 6,
            });
            $('.long').inputmask({
                'alias': 'decimal',
                'allowMinus': true,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 6,
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.retail.franchise.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Name');
                            head.push('Phone Number');
                            head.push('Email');
                            head.push('CNIC');
                            head.push('Default Hub');
                            head.push('Created Date/Time');
                            head.push('Updated Date/Time');
                            head.push('Updated By');
                            head.push('Status');
                            head.push('Franchise Code');
                            head.push('Discount');
                            head.push('Insurance');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.phone_no);
                                row.push(values.email);
                                row.push(values.cnic);
                                row.push(values.default_hub);
                                row.push(values.created);
                                row.push(values.updated);
                                row.push(values.updated_by);
                                row.push(values.status);
                                row.push(values.code);
                                row.push(values.discount);
                                row.push(values.insurance);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                // autoWidth: false,
                buttons: [
                        @if (session('role_id') == 1 || in_array(434, session('permissions')))
                    {
                        text: 'Add Franchise',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_franchise').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Franchise',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.retail.franchise.list') }}',
                },
                order: [[7, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'name' ,name: 'retail_franchises.name', class: 'align-middle text-center name'},
                    { data:'phone_no' ,name: 'retail_franchises.phone_no', class: 'align-middle text-center phone_no'},
                    { data:'email' ,name: 'retail_franchises.email', class: 'align-middle text-center email'},
                    { data:'cnic' ,name: 'retail_franchises.cnic', class: 'align-middle text-center cnic'},
                    { data:'default_hub' ,name: 'c.name', class: 'align-middle text-center default_hub'},
                    { data:'created' ,name: 'retail_franchises.created_at', class: 'align-middle text-center created_at'},
                    { data:'updated' ,name: 'retail_franchises.updated_at', class: 'align-middle text-center updated_at'},
                    { data:'updated_by' ,name: 'a.name', class: 'align-middle text-center updated_by'},
                    { data:'status' ,name: 'retail_franchises.status', class: 'align-middle text-center status'},
                    { data:'code' ,name: 'retail_franchises.code', class: 'align-middle text-center code'},
                    { data:'location' ,name: 'location', class: 'align-middle text-center location', orderable: false, searchable: false},
                    { data:'discount' ,name: 'retail_franchises.discount', class: 'align-middle text-center discount'},
                    { data:'insurance' ,name: 'retail_franchises.insurance', class: 'align-middle text-center insurance'},
                    { data:'action' ,name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false},
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
                        '<option value="1">Active</option>' +
                        '<option value="0">In-Active</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.location')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.retail.franchise.status') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'status': 1
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.disable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.retail.franchise.status') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'status': 0
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.view_logs', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if($(this).hasClass('view_logs')) {
                    $.ajax({
                        url: "{{ route('admin.retail.view_logs') }}",
                        method: 'GET',
                        data: {
                            'id': id,
                            'screen_name' : 'Retail Franchise'
                        },
                        success: function (data) {
                            if (data.status == 0) {
                                var logs = data.logs;
                                var logContent = '';
                                if (logs.length > 0) {
                        
                                    logs.forEach(function (log) {
                                        let editedFieldsFormatted = log.data
                                            .split(', ')
                                            .map(field => {
                                                // Split the field at "->", keep the "->" and make the part after it bold
                                                let parts = field.split('->');
                                                if (parts.length > 1) {
                                                    return parts[0] + ' <strong>' + '-> ' + parts[1].trim() + '</strong>';
                                                }
                                                return field; // Return as is if "->" is not found
                                            })
                                            .join('<br>'); // Add line breaks between fields

                                        logContent += '<tr>';
                                        logContent += '<td>' + log.name + '</td>';
                                        logContent += '<td>' + editedFieldsFormatted + '</td>';
                                        logContent += '<td>' + log.created_at + '</td>';
                                        logContent += '</tr>';
                                    });

                                    $('#logs_modal table tbody').html(logContent);
                                    $('#logs_modal').modal('show');
                                } else {
                                    toastr.error('No logs found for this record.', 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            } else {
                                toastr.error(data.message, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        },
                        error: function () {
                            // Handle errors in the AJAX request
                            toastr.error('Something went wrong while retrieving logs.', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });

            $('#add_franchise').on('hide.bs.modal', function () {
                $('#hub').val(null).trigger('change');
                $('#name').val('');
                $('#phone_number').val('');
                $('#email').val('');
                $('#cnic').val('');
                $('#lat').val('');
                $('#long').val('');
                $('#discount').val('');
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var name = table.row($(this).parents('tr')).data().name;
                var phone_no = table.row($(this).parents('tr')).data().phone_no;
                var cnic = table.row($(this).parents('tr')).data().cnic;
                var email = table.row($(this).parents('tr')).data().email;
                var default_hub_id = table.row($(this).parents('tr')).data().default_hub_id;
                var lat = table.row($(this).parents('tr')).data().location_latitude;
                var long = table.row($(this).parents('tr')).data().location_longitude;
                var discount = table.row($(this).parents('tr')).data().discount;
                var insurance = table.row($(this).parents('tr')).data().insurance;

                $('#franchise_id').val(id);
                $('#edit_name').val(name);
                $('#edit_phone_number').val(phone_no);
                $('#edit_cnic').val(cnic);
                $('#edit_email').val(email);
                $('#edit_lat').val(lat);
                $('#edit_long').val(long);
                $('#edit_discount').val(discount);
                $('#edit_insurance').val(insurance);

                $('#edit_remarks_title').text('Edit Franchise ' + name);

                // show the franchise retail charges
                $.ajax({
                    type: "GET",
                    url: '{{ route('admin.retail.franchise.retail_product_charges') }}',
                    data: { franchise_id: id },
                    success: function (response) {
                        $('#deduction_percentage_edit').val(response.data.franchise_deduction);
                        $('#withholding_tax_percentage_edit').val(response.data.franchise_withholding);
                        $('#edit_security_deposit').val(response.data.security_deposit);
                        $('#edit_license_fees').val(response.data.license_fees);
                        $('#edit_bank_id').val(response.data.bank_id);
                        $('#edit_security_cheque_number').val(response.data.security_cheque_number);
                        $('#edit_license_cheque_number').val(response.data.license_cheque_number);
                        // $('#deduction_percentage_edit').val(response.data.franchise_deduction);
                    }
                });

                $.ajax({
                    type: "GET",
                    url: '{{ route('admin.retail.franchise.retail_product_attachments') }}',
                    data: { franchise_id: id },
                    success: function (response) {
                        if (response.data) {
                            var franchiseId = response.data.franchise_id;
                            if (franchiseId === id) {
                                for (var i = 1; i <= 5; i++) {
                                    var attachmentKey = 'attachment_' + i;
                                    var attachmentFileName = response.data[attachmentKey];
                                    if (attachmentFileName) {
                                        var attachmentURL = '/storage/franchise_product_attachment_' + i + '/' + attachmentFileName;
                                        var attachmentLink = $('<a>').attr('href', attachmentURL).attr('target', '_blank').text(attachmentFileName);
                                        $('#attachment_' + i + '_filename').html(attachmentLink);
                                    } else {
                                        $('#attachment_' + i + '_filename').text('No attachment');
                                    }
                                }
                            }
                        }
                    }
                });

                // show cnic status
                $.ajax({
                    type: "GET",
                    url: '{{ route('admin.retail.franchise.cnic_status') }}',
                    data: { franchise_id: id },
                    success: function(response) {
                        var response = response.data;
                        var cnic_status = response.cnic_status;
                        var edit_cnic_status = $('#edit_cnic_status');
                        if (cnic_status == 1 && !edit_cnic_status.is(':checked')) {
                            edit_cnic_status.trigger('click');
                        } else if (cnic_status != 1 && edit_cnic_status.is(':checked')) {
                            edit_cnic_status.trigger('click');
                        }
                    }
                });

                $('#edit_franchise').modal('show');
            });

            $('#add_franchise_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                    error.addClass('w-100').appendTo(element.parents('.input-group'));
                },
                submitHandler: function(form) {
                    var retailShippingIds = [];
                    var productPercentages = [];
                    $("#tableBody").find("tr").each(function() {
                        var selectedOption = $(this).find("td:first").text();
                        var productPercentage = $(this).find("td:nth-child(2)").text();
                        retailShippingIds.push(selectedOption);
                        productPercentages.push(productPercentage);
                    });
                    $(form).append("<input type='hidden' name='retail_shipping_mode_id' value='" + JSON.stringify(retailShippingIds) + "'>");
                    $(form).append("<input type='hidden' name='product_percentage' value='" + JSON.stringify(productPercentages) + "'>");

                    swal({
                        title: 'Please Wait!',
                        text: 'Franchise is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('#edit_franchise_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                    error.addClass('w-100').appendTo(element.parents('.input-group'));
                },
                submitHandler: function(form) {
                    var retailShippingIdsEdit = [];
                    var productPercentagesEdit = [];
                    $("#editTableBody").find("tr").each(function() {
                        var selectedOptionEdit = $(this).find("td:first").text();
                        var productPercentageEdit = $(this).find("td:nth-child(2)").text();
                        retailShippingIdsEdit.push(selectedOptionEdit);
                        productPercentagesEdit.push(productPercentageEdit);
                    });
                    $(form).append("<input type='hidden' name='retail_shipping_mode_id' value='" + JSON.stringify(retailShippingIdsEdit) + "'>");
                    $(form).append("<input type='hidden' name='product_percentage' value='" + JSON.stringify(productPercentagesEdit) + "'>");

                    swal({
                        title: 'Please Wait!',
                        text: 'Franchise is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            // reset modal on close
            function resetModal() {
                $("#product_percentage").val("");
                $("#product_percentage_edit").val("");
                $("#retail_shipping_mode_id").val("1").trigger("change");
                $("#retail_shipping_mode_id_edit").val("1").trigger("change");
                $("#error_message").hide();
                $("#error_message_edit").hide();
                $("#product_percentage").removeAttr("required");
                $("#product_percentage_edit").removeAttr("required");
                $("#tableBody").empty();
                $("#tableBodyEdit").empty();
                $("#tableRow").hide();
                $("#tableRowEdit").hide();
                var edit_cnic_status = $('#edit_cnic_status');
                if (edit_cnic_status.is(':checked')) {
                    edit_cnic_status.trigger('click');
                }
            }

            $("#retail_product_add_btn").on('click', function (event) {
                var selectedOption = $("#retail_shipping_mode_id option:selected").text();
                var productPercentage = $("#product_percentage").val();

                if (productPercentage.trim() === '' || !$.isNumeric(productPercentage)) {
                    $("#error_message").text("Please enter a valid product percentage.").show();
                    $("#product_percentage").attr("required", true);
                } else {
                    $("#error_message").hide();
                    $("#product_percentage").removeAttr("required");
                    var isDuplicate = false;
                    $("#tableBody").find("tr").each(function() {
                        if ($(this).find("td:first").text() === selectedOption) {
                            isDuplicate = true;
                            return false; // Exit the loop if a duplicate is found
                        }
                    });

                    if (isDuplicate) {
                        $("#error_message").text("Error: Cannot add same product.").show();
                    } else {
                        var newRow = $("<tr><td>" + selectedOption + "</td><td>" + productPercentage + "%</td><td><button class='btn btn-danger btn-sm remove-item'>Remove</button></td></tr>");
                        $("#tableBody").append(newRow);
                        newRow.find('.remove-item').click(function() {
                            $(this).closest("tr").remove();
                            if ($("#tableBody").find("tr").length === 0) {
                                $("#tableRow").hide();
                            }
                        });
                        $("#tableRow").show();
                        $("#product_percentage").val('');
                    }
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function(event) {
                var selectedOption = $("#retail_shipping_mode_id_edit option:selected").text();
                var productPercentage = $(".product_percentage_edit").val();
                var franchiseId = $("#franchise_id").val();
                // send ajax request to fetch data
                $.ajax({
                    type: "GET",
                    url: '{{ route('admin.retail.franchise.retail_product_percentage') }}',
                    data: { franchise_id: franchiseId },
                    success: function (response) {
                        if (response.data.length > 0) {
                            $('#editTableBody').empty();
                            $.each(response.data, function(index, item) {
                                var row = "<tr><td>" + item.selected_option + "</td><td>" + item.product_percentage + "</td><td><button class='btn btn-danger btn-sm remove-btn-edit-form'>Remove</button></td></tr>";
                                $("#editTableBody").append(row);
                            });
                            $("#editTableRow").show();
                        }
                        else {
                            $("#editTableRow").hide();
                        }
                    }
                });

                $('#editTableBody').on('click', '.remove-btn-edit-form', function() {
                    $(this).closest('tr').remove();
                });
            });

            $("#edit_retail_product_add_btn").on('click', function (event) {
                var selectedOptionEdit = $("#retail_shipping_mode_id_edit option:selected").text();
                var productPercentageEdit = $("#product_percentage_edit").val();

                if (productPercentageEdit.trim() === '' || !$.isNumeric(productPercentageEdit) || productPercentageEdit > 100 ) {
                    $("#error_message_edit").text("Please enter a valid product percentage.").show();
                    $("#product_percentage_edit").attr("required", true);
                } else {
                    $("#error_message_edit").hide();
                    $("#product_percentage_edit").removeAttr("required");
                    var isDuplicate = false;
                    $("#editTableBody").find("tr").each(function() {
                        if ($(this).find("td:first").text() === selectedOptionEdit) {
                            isDuplicate = true;
                            return false;
                        }
                    });

                    if (isDuplicate) {
                        $("#error_message_edit").text("Error: Cannot add same product.").show();
                    } else {
                        var newRow = $("<tr><td>" + selectedOptionEdit + "</td><td>" + productPercentageEdit + "%</td><td><button class='btn btn-danger btn-sm remove-item'>Remove</button></td></tr>");
                        $("#editTableBody").append(newRow);
                        newRow.find('.remove-item').click(function() {
                            $(this).closest("tr").remove();
                            if ($("#editTableBody").find("tr").length === 0) {
                                $("#editTableRow").hide();
                            }
                        });
                        $("#editTableRow").show();
                        $("#product_percentage_edit").val('');
                    }
                }
            });

            $(".modal_close_btn").click(function() {
                resetModal();
            });

            $('#security_deposit, #edit_security_deposit').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('#license_fees, #edit_license_fees').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            
        });

    </script>
@endsection