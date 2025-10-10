@extends('admin.layout.master')

@section('title', 'Trax Center')

@section('content')
    <h1 class="mb-1">
        Trax Center
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Center Name</th>
                        <th class="border-primary border-darken-1">Center Number</th>
                        <th class="border-primary border-darken-1">Email</th>
                        <th class="border-primary border-darken-1">CNIC</th>
                        <th class="border-primary border-darken-1">Default Hub</th>
                        <th class="border-primary border-darken-1">Created Date/Time</th>
                        <th class="border-primary border-darken-1">Updated Date/Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Store Code</th>
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

    <div class="modal fade" id="add_trax_center" role="dialog" aria-labelledby="add_trax_center_title" aria-hidden="true">
        <div class="modal-dialog add_trax_center_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Add Trax Center</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_trax_center_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.trax_center.add') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field()  }}
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Trax Center Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-remote="{{ route('admin.retail.trax_center.name') }}" data-msg-remote="Name must be unique">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="phone_number" id="phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" autocomplete="nope">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="cnic" id="cnic" class="form-control cnic" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
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
                                    <div class="input-group mb-2">
                                        <input type="text" name="insurance" id="insurance" class="form-control insurance" placeholder="Insurance*"  value="" max="100" min="1"
                                               data-rule-required="true" data-msg-required="Insurance is required">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="text" name="discount" id="discount" class="form-control discount" placeholder="Discount"  value="" max="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" name="advance_amount" id="advance_amount" class="form-control advance_amount" placeholder="Advance Amount*" data-rule-required="true" data-msg-required="Advance Amount is required" pattern="[0-9]{1,8}" maxlength="10">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="rental" id="rental" class="form-control rental" placeholder="Rental Amount*" data-rule-required="true" data-msg-required="Rental Amount is required" pattern="[0-9]{1,7}" maxlength="9">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="landlord_name" id="landlord_name" class="form-control landlord_name" placeholder="Landlord Name*" data-rule-required="true" data-msg-required="Landlord name is required">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="landlord_contact_number" id="landlord_contact_number" class="form-control landlord_contact_number" placeholder="Landlord Contact Number*" data-rule-required="true" data-msg-required="Landlord contact number is required">
                                    </div>

                                    <div class="form-group">
                                        <textarea name="shop_address" id="shop_address" class="form-control shop_address" placeholder="Shop Address*" rows="4" cols="50"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="agreement_start_date" id="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Agreement Start date*" data-rule-required="true" data-msg-required="Agreement Start date is required">
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="agreement_end_date" id="delivery_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Agreement End date*" data-rule-required="true" data-msg-required="Agreement End     date is required">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="attachment_1">Agreement File*</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept=".doc,.docx,.pdf" data-rule-required="true" data-msg-required="Atleast 1 attachment is required">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_2">Landlord CNIC front picture</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2" accept="image/*,.doc,.docx,.pdf">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_3">Landlord CNIC back picture</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3" accept="image/*,.doc,.docx,.pdf">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_4">Location pictures</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4" accept="image/*,.doc,.docx,.pdf">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_5">Attachment 5</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5" accept="image/*,.doc,.docx,.pdf">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_trax_center" role="dialog" aria-labelledby="edit_trax_center_title" aria-hidden="true">
        <div class="modal-dialog edit_trax_center_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_remarks_title">Edit Trax Center</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_trax_center_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.trax_center.edit') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field()  }}
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="trax_center_id" id="trax_center_id" value="">
                                    <div class="form-group">
                                        <input type="text" name="name" id="edit_name" class="form-control" placeholder="Trax Center Name*" data-rule-required="true" data-msg-required="Name is required" value="">
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
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" name="advance_amount" id="advance_amount_edit" class="form-control advance_amount" placeholder="Advance Amount*" data-rule-required="true" data-msg-required="Advance Amount is required" value="" pattern="[0-9]{1,8}" maxlength="10">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="rental" id="rental_edit" class="form-control rental" placeholder="Rental Amount" data-rule-required="true" data-msg-required="Rental Amount is required" value="" pattern="[0-9]{1,7}" maxlength="9">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="landlord_name" id="landlord_name_edit" class="form-control landlord_name" placeholder="Landlord Name*" data-rule-required="true" data-msg-required="Landlord name is required" value="">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="landlord_contact_number" id="landlord_contact_number_edit" class="form-control landlord_contact_number" placeholder="Landlord Contact Number*" data-rule-required="true" data-msg-required="Landlord contact number is required" value="">
                                    </div>

                                    <div class="form-group">
                                        <textarea name="shop_address" id="shop_address_edit" class="form-control shop_address" placeholder="Shop Address*" rows="4" cols="50" value=""></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="agreement_start_date" id="edit_delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Agreement Start date*" data-rule-required="true" data-msg-required="Agreement Start date is required">
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="agreement_end_date" id="edit_delivery_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Agreement End date*" data-rule-required="true" data-msg-required="Agreement End date is required">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="attachment_1">Agreement File</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1_edit" accept=".doc,.docx,.pdf">
                                        <a id="attachment_1_filename" target="_blank"></a>
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_2">Landlord CNIC front picture</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2_edit" accept="image/*,.doc,.docx,.pdf">
                                        <a id="attachment_2_filename" target="_blank"></a>
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_3">Landlord CNIC back picture</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3_edit" accept="image/*,.doc,.docx,.pdf">
                                        <a id="attachment_3_filename" target="_blank"></a>
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_4">Location pictures</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4_edit" accept="image/*,.doc,.docx,.pdf">
                                        <a id="attachment_4_filename" target="_blank"></a>
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_5">Attachment 5</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5_edit" accept="image/*,.doc,.docx,.pdf">
                                        <a id="attachment_5_filename" target="_blank"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary edit" value="Add">Edit</button>
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
        .add_trax_center_modal, .edit_trax_center_modal{
            max-width: 1300px;
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
            $('.phone_number, .landlord_contact_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('.cnic').inputmask({
                'mask': '99999-9999999-9',
                'clearIncomplete': true
            });

            $('#add_trax_center_form #hub').prepend('<option value="" selected="selected"></option>').select2({
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

            $('#advance_amount, #advance_amount_edit').on('input', function() {
                let value = $(this).val();
                value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas every three digits
                $(this).val(value);
            });

            $('#rental, #rental_edit').on('input', function() {
                let value = $(this).val();
                value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas every three digits
                $(this).val(value);
            });

            $('#add_trax_center_form #delivery_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#add_trax_center_form #delivery_date_to').pickadate('picker').set('min', $('#add_trax_center_form #delivery_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $('#add_trax_center_form #delivery_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#add_trax_center_form #delivery_date_from').pickadate('picker').set('min', $('#add_trax_center_form #delivery_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            $('#edit_trax_center_form #delivery_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#add_trax_center_form #delivery_date_to').pickadate('picker').set('min', $('#add_trax_center_form #delivery_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $('#edit_trax_center_form #delivery_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#add_trax_center_form #delivery_date_from').pickadate('picker').set('min', $('#add_trax_center_form #delivery_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.retail.trax_center.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Center Name');
                            head.push('Center Number');
                            head.push('Email');
                            head.push('CNIC');
                            head.push('Default Hub');
                            head.push('Created Date/Time');
                            head.push('Updated Date/Time');
                            head.push('Updated By');
                            head.push('Status');
                            head.push('Store Code');
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
                        @if (session('role_id') == 1 || in_array(433, session('permissions')))
                    {
                        text: 'Add Trax Center',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_trax_center').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Trax Center',
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
                    url: '{{ route('admin.retail.trax_center.list') }}',
                },
                order: [[7, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'name' ,name: 'retail_trax_centers.name', class: 'align-middle text-center name'},
                    { data:'phone_no' ,name: 'retail_trax_centers.phone_no', class: 'align-middle text-center phone_no'},
                    { data:'email' ,name: 'retail_trax_centers.email', class: 'align-middle text-center email'},
                    { data:'cnic' ,name: 'retail_trax_centers.cnic', class: 'align-middle text-center cnic'},
                    { data:'default_hub' ,name: 'c.name', class: 'align-middle text-center default_hub'},
                    { data:'created' ,name: 'retail_trax_centers.created_at', class: 'align-middle text-center created_at'},
                    { data:'updated' ,name: 'retail_trax_centers.updated_at', class: 'align-middle text-center updated_at'},
                    { data:'updated_by' ,name: 'a.name', class: 'align-middle text-center updated_by'},
                    { data:'status' ,name: 'retail_trax_centers.status', class: 'align-middle text-center status'},
                    { data:'code' ,name: 'retail_trax_centers.code', class: 'align-middle text-center code'},
                    { data:'location' ,name: 'location', class: 'align-middle text-center location', orderable: false, searchable: false},
                    { data:'discount' ,name: 'discount', class: 'align-middle text-center discount'},
                    { data:'insurance' ,name: 'retail_trax_centers.insurance', class: 'align-middle text-center insurance'},
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
                    url: '{!! route('admin.retail.trax_center.status') !!}',
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
                    url: '{!! route('admin.retail.trax_center.status') !!}',
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
                            'screen_name' : 'Retail Center'
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

            $('#add_trax_center').on('hide.bs.modal', function () {
                $('#hub').val(null).trigger('change');
                $('#name').val('');
                $('#phone_number').val('');
                $('#email').val('');
                $('#cnic').val('');
                $('#lat').val('');
                $('#long').val('');
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

                $('#trax_center_id').val(id);
                $('#edit_name').val(name);
                $('#edit_phone_number').val(phone_no);
                $('#edit_cnic').val(cnic);
                $('#edit_email').val(email);
                $('#edit_lat').val(lat);
                $('#edit_long').val(long);
                $('#edit_discount').val(discount);
                $('#edit_insurance').val(insurance);

                $('#edit_remarks_title').text('Edit Trax Center ' + name);
                $('#edit_trax_center').modal('show');

                // AJAX to show trax center attachments
                $.ajax({
                    type: "GET",
                    url: '{{ route('admin.retail.trax_center.trax_center_edit_attachment') }}',
                    data: { trax_center_id: id },
                    success: function (response) {
                        $('#advance_amount_edit').val(response.data.advance_amount);
                        $('#rental_edit').val(response.data.rental);
                        $('#landlord_name_edit').val(response.data.landlord_name);
                        $('#landlord_contact_number_edit').val(response.data.landlord_contact_number);
                        $('#shop_address_edit').val(response.data.shop_address);

                        var agreement_start_date = response.data.agreement_start_date;
                        var agreement_end_date = response.data.agreement_end_date;

                        if (agreement_start_date) {
                            $('#edit_delivery_date_from').val(agreement_start_date);
                        } else {
                            $('#edit_delivery_date_from').val('');
                        }

                        if (agreement_end_date) {
                            $('#edit_delivery_date_to').val(agreement_end_date);
                        } else {
                            $('#edit_delivery_date_to').val('');
                        }

                        for (var i = 1; i <= 5; i++) {
                            var attachmentKey = 'attachment_' + i;
                            var attachmentFileName = response.data[attachmentKey];
                            if (attachmentFileName) {
                                var attachmentURL = '/storage/trax_center_attachments/trax_center_attachment_' + i + '/' + attachmentFileName;
                                var attachmentLink = $('<a>').attr('href', attachmentURL).attr('target', '_blank').text(attachmentFileName);
                                $('#attachment_' + i + '_filename').html(attachmentLink);
                            } else {
                                $('#attachment_' + i + '_filename').text('No attachment');
                            }
                        }
                    }
                });
            });

            $('#add_trax_center_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                    error.addClass('w-100').appendTo(element.parents('.input-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Trax Center is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('#edit_trax_center_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                    error.addClass('w-100').appendTo(element.parents('.input-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Trax Center is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });
        });
    </script>
@endsection