
@extends('admin.layout.master')
@section('title','Edit Sister Accounts')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <h1 class="mb-1">
        Edit Sister Accounts
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form action="#" id="edit_account_form">
                    <div class="row justify-content-center align-items-center mb-2">
                        <div class="col-3">
                            <fieldset>
                                <input type="text" class="form-control" placeholder="Enter Account No*" id="enter_account_number">
                            </fieldset>
                        </div>
                        <div>
                            <button type="submit" id="add_account" class="mr-1 btn btn-primary btn-min-width"> Add</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Company Name</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Contact Name</th>
                        <th class="border-primary border-darken-1">Phone No</th>
                        <th class="border-primary border-darken-1">Company Address</th>
                        <th class="border-primary border-darken-1">Email</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Account Tagged To</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

                <form id="merge_account_form" class="" method="post" action="{{ route('admin.accounts.sister_account.edit.submit') }}" novalidate="novalidate">
                    {{ csrf_field() }}
                    <div class="row justify-content-center">
                        <input type="hidden" name="account_ids" id="account_ids">
                        <div class="form-group col-3">
                            <input type="hidden" name="merged_id" value="{{$group_name->id}}">
                            <input type="text" name="group_name" class="form-control" value="{{$group_name->name}}" placeholder="Group Name*" id="group_name" data-rule-required="true" data-msg-required="Group Name is required">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block btn-min-width">Merge Accounts</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function() {
            $('#enter_account_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            var merged_accounts = @json($merged_accounts);
            var account_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                // buttons: false,
                scrollX: true,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'account_no', class: 'align-middle account_no', orderable: false},
                    {name: 'company_name', class: 'align-middle company_name', orderable: false},
                    {name: 'city', class: 'align-middle city', orderable: false},
                    {name: 'contact_name', class: 'align-middle contact_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'email', class: 'align-middle email', orderable: false},
                    {name: 'product_type', class: 'align-middle product_type', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'tagged_to', class: 'align-middle tagged_to', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            var count = 0;
            $.each(merged_accounts, function(index, account) {
                count = count + 1;
                if(count == 1){
                    var action = '';
                }
                else{
                    var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove_account"><i class="la la-close"></i></a>';
                }

                var is_indexed = $.inArray(account.id, account_ids);
                if(is_indexed === -1) {
                    var account_id = ''+account.id+'';
                    var account_id_pad = account_id.padStart(6, '0');

                    var status_no = account.status;
                    var status = '';
                    if(status_no == 0){
                        status = 'Request Received';
                    }
                    else if(status_no == 1){
                        status = 'Rates Added';
                    }
                    else if(status_no == 2){
                        status = 'Pending for Activation';
                    }
                    else{
                        status = 'Active';
                    }

                    var row = table.row.add([count, account_id_pad, account.company_name, account.city, account.contact_name, account.phone, account.address, account.email, account.product_type, status, account.tagged_to, action]).node().id = account.id;
                    account_ids.push(account.id);
                    table.draw();
                }
            });




            $('#edit_account_form').on('submit',function (e) {
                e.preventDefault();
                var account_id = $('#enter_account_number').val();
                if(account_id != null && account_id != '') {
                    $('#add_account').attr('disabled', true);
                    blockPagePermanently();
                    $.ajax({
                        url:'{{route('admin.accounts.sister_account.info')}}',
                        type:'POST',
                        data: {
                            'id':account_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        $('#enter_account_number').val('');
                            if (data.status === 1) {
                                UnblockPagePermanently();
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            } else {
                                var is_indexed = $.inArray(data.info.id, account_ids);
                                if(is_indexed === -1) {
                                    var rowNo = table.rows().count() + 1;
                                    var new_account_id = '' + data.info.id + '';
                                    var new_account_id_pad = new_account_id.padStart(6, '0');
                                    var status_no = data.info.status;
                                    var status = '';
                                    if (status_no == 0) {
                                        status = 'Request Received';
                                    }
                                    else if (status_no == 1) {
                                        status = 'Rates Added';
                                    }
                                    else if (status_no == 2) {
                                        status = 'Pending for Activation';
                                    }
                                    else {
                                        status = 'Active';
                                    }
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove_account"><i class="la la-close"></i></a>';
                                    var row = table.row.add([rowNo, new_account_id_pad, data.info.company_name, data.info.city, data.info.contact_name, data.info.phone, data.info.address, data.info.email, data.info.product_type, status, data.info.tagged_to, remove]).node().id = data.info.id;
                                    table.draw();
                                    account_ids.push(data.info.id);
                                }
                                else{
                                    var error = 'Account ID already entered!';
                                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        UnblockPagePermanently();
                        $('#add_account').attr('disabled', false);
                        console.log(account_ids);
                    });
                }
                else{
                    var error = "Please enter Account No.";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                console.log(account_ids);
            });

            $('body').on('click','a.remove_account',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, account_ids);

                if (index !== -1) {
                    account_ids.splice(index, 1);
                }

                table.row( $(this).parents('tr') ).remove().draw();
            });

            $('#merge_account_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    var group_name = $('#group_name').val();
                    $('#merge_account_form input#account_ids').val(account_ids);
                    swal({
                        title: 'Please Wait!',
                        text: 'Accounts is being updated!',
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