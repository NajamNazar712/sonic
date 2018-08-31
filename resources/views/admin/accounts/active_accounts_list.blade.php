@extends('admin.layout.master')

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
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">City Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Phone Number</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Email Address</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Action</th>
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
            border-color: #666EE8;
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
            fixedHeader: {
                header: true,
                headerOffset: $('.header-navbar').height()
            },
            lengthMenu: [[25, 50, 100], [25, 50, 100]],
            pageLength: 25,
            stateSave: true,
            pagingType: 'full_numbers',
            processing: true,
            serverSide: true,
            rowId: 'id',
            ajax: '{{ route('admin.accounts.active.ajax') }}',
            columns: [
                {data: 'id', name: 'id', class: 'account_id'},
                {data: 'name', name: 'name', class: 'company_name'},
                {data: 'city', name: 'cities.name', class: 'city'},
                {data: 'poc', name: 'poc', class: 'contact_person'},
                {data: 'phone', name: 'phone', class: 'phone'},
                {data: 'address', name: 'address', class: 'address'},
                {data: 'email', name: 'email', class: 'email'},
                {data: 'status', name: 'status', class: 'status'},
                {data: 'action', name: 'action', class: 'action', orderable: false, searchable: false}
            ],
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.action')) {
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
            }
        });
        $('body').on('click','a.blacklist',function () {
            var id = $(this).parents('tr').attr('id');
            var status = $(this).attr('rel');
            if(id){
                $.ajax({
                    url: '{!! route('admin.accounts.status.block') !!}',
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
        });
        $('body').on('click','a.userenable',function () {
            var status  = "enable";
            var id = $(this).parents('tr').attr('id');
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
        });
        $('body').on('click','a.userdisable',function () {
            var status  = "disable";
            var id = $(this).parents('tr').attr('id');
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
        });
    });

</script>

@endsection

