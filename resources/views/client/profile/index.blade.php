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
                                    {{--<p>Macaroon candy canes tootsie roll wafer lemon drops liquorice--}}
                                    {{--jelly-o tootsie roll cake. Marzipan liquorice soufflé cotton--}}
                                    {{--candy jelly cake jelly-o sugar plum marshmallow. Dessert--}}
                                    {{--cotton candy macaroon chocolate sugar plum cake donut.</p>--}}
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
                                                <td><b>URL</b></td>
                                                <td>{{$user->url}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Product Type</b></td>
                                                <td>{{$product_name}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>City</b></td>
                                                <td>{{$user->city->name}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Nature Of Account</b></td>
                                                <td>{{$user->account_type->name}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>API Key</b></td>
                                                <td>{{$user->api_token}}</td>
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
                                                <th class="border-primary border-darken-1">Email Address</th>
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
                                        <table class="table" style="font-size: 14px">
                                            <thead>
                                            <tr>
                                                {{--<th>Firstname</th>--}}
                                                {{--<th>Lastname</th>--}}
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><b>Bank Name</b></td>
                                                <td>{{$user->bank->bank->name}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Bank Branch</b></td>
                                                <td>{{$user->bank->bank_branch}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Bank City</b></td>
                                                <td>{{$user->bank->city->name}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Account Number</b></td>
                                                <td>{{$user->bank->account_no}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Account Title</b></td>
                                                <td>{{$user->bank->account_title}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>IBAN Number</b></td>
                                                <td>{{$user->bank->iban}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Payment Cycle</b></td>
                                                <td>{{$user->bank->payment_cycle}}</td>
                                            </tr>
                                            @if($user->account_type_id == 2)
                                            <tr>
                                                <td><b>Invoicing Cycle</b></td>
                                                <td>{{$user->bank->invoicing->name}}</td>
                                            </tr>
                                            @if($user->bank->invoicing_cycle_id != 2)
                                            <tr>
                                                <td><b>Generation Date</b></td>
                                                <td>{{$user->bank->generation_date}}</td>
                                            </tr>
                                            @endif

                                                <tr>
                                                    <td><b>Billing Person Name</b></td>
                                                    <td>{{$user->bank->billing_person_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Billing Person Phone</b></td>
                                                    <td>{{$user->bank->billing_person_phone}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Billing Person Email</b></td>
                                                    <td>{{$user->bank->billing_person_email}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Billing Address</b></td>
                                                    <td>{{$user->bank->billing_address}}</td>
                                                </tr>
                                            @endif
                                            </tbody>
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
                                                    <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone Number is required" value="{{$user->phone}}" name="phone" required>
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
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <button id="cancel-button" type="button" class="btn btn-warning mr-1">
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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>




    <script type="text/javascript">
        $(document).ready(function() {

            $('#edit-1').click(function () {
                $("#main-form").show();
                $("#tabs").hide();
            });
            $('#edit-2').click(function () {
                $("#main-form").show();
                $("#tabs").hide();
            });
            $('#cancel-button').click(function () {
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
                $("#tabs").show();
            });



            // $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
            $("input[name='phone'],input[name='phone2']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            // $("input[name='ntn_no']").inputmask({'mask': "9999999-9", 'clearIncomplete': true});



            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
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
                    {data: 'email', name: 'email'},
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


            });


            $('#city_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select City',
                dropdownParent:$('#add_pickup_form')
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


        });
    </script>
@endsection