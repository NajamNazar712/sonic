@extends('admin.layout.master')

@section('title', 'Active Accounts List')

@section('content')
    <h1>Active Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">

                        @include('admin.inc.messages')

                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">City Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Phone Number</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Email Address</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Request Date</th>
                                        <th class="border-primary border-darken-1">Rate Added By</th>
                                        <th class="border-primary border-darken-1">Rate Approved By</th>
                                        <th class="border-primary border-darken-1">Account Activated By</th>
                                        <th class="border-primary border-darken-1">Account Activation Date</th>
                                        <th class="border-primary border-darken-1"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
    $(document).ready(function() {
       var table = $('#datatable').DataTable({
            dom: 'ltipr',
            scrollX: true,
            lengthMenu: [[25, 50, 100], [25, 50, 100]],
            pageLength: 25,
            pagingType: 'full_numbers',
            processing: true,
            serverSide: true,
            rowId: 'id',
            ajax: '{{ route('admin.accounts.active.ajax') }}',
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'id', name: 'id', class: 'align-middle account_id'},
                {data: 'name', name: 'name', class: 'align-middle company_name'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                {data: 'phone', name: 'phone', class: 'align-middle phone'},
                {data: 'address', name: 'address', class: 'align-middle address'},
                {data: 'email', name: 'email', class: 'align-middle email'},
                {data: 'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'created_at', name: 'users.created_at', class: 'align-middle created_at'},
                {data: 'added_by', name: 'rab.name', class: 'align-middle added_by'},
                {data: 'approved_by', name: 'rabb.name', class: 'align-middle approved_by'},
                {data: 'account_activated_by', name: 'rabba.name', class: 'align-middle account_activated_by'},
                {data: 'activated_date', name: 'users.activated_at', class: 'align-middle activated_date'},
                {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
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
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.action')  || $(header).is('.serial_number')) {
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
        $('body').on('change','.blacklist_reason',function() {
            $(this).val($(this).val().trim());
        });
        $('body').on('click','button.blacklist',function () {
            var id = $(this).parents('tr').attr('id');
            var status = $(this).attr('rel');
            swal({
                // title: 'Are You Sure?',
                text: 'Write a reason to blacklist this account!',
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "Write a reason",
                        class: "form-control blacklist_reason",
                    },
                },
                buttons: {
                    cancel: {
                        text: 'No',
                        value: false,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        closeModal: false
                    }
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                dangerMode: true
            }).then((value) => {
                    if (value) {
                        if (value === '') {
                            swal("You have not selected any reason!", {
                                icon: "warning",
                            });
                        } else {
                        if (id) {
                            $.ajax({
                                url: '{!! route('admin.accounts.status.block') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    'reason': value,
                                    'status': status,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                swal.close();
                                if (data.status === 1) {
                                    table.draw('false');
                                    swal.close();
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                }

                            });
                        }
                    }
                    }else{
                        swal.close();
                    }

            });


        });
        $('body').on('click','button.userenable',function () {
            var status  = "enable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to enable this account!',
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
                            url: '{!! route('admin.accounts.status.change') !!}',
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
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }

                        });
                    }
                }
            });

        });
        $('body').on('click','button.userdisable',function () {
            var status  = "disable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to Disable this account!',
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
                           url: '{!! route('admin.accounts.status.change') !!}',
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
                               toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                           }

                       });
                   }
               }
            });

        });
    });

</script>

@endsection

