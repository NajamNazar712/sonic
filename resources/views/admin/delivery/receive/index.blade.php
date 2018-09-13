
@extends('admin.layout.master')
@section('title','Receive Deliveries')

@section('content')
    <h1 class="mb-1">
        Receive Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')



                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Scan Delivery Note Number" name="scan_delivery_note" id="scan_delivery_note">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number" name="search_tracking" id="search_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>


                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Route</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Total COD</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.receive.list') }}',
                    data: function (d) {
                        d.delivery_note_number = $('#scan_delivery_note').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'delivery_note_id',
                order: [[2, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle delivery_note'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count' ,name: 'shipments_count', class: 'align-middle shipments_count'},
                    { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
                    { data:'created_at' ,name: 'delivery_notes.created_at', class: 'align-middle created_at'},
                    { data:'amount' ,name: 'delivery_notes.total_cod_amount', class: 'align-middle amount'},
                    { data:'dn_status' ,name: 'dn_status', class: 'align-middle dn_status',orderable: false, searchable: false},
                    {data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false}
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

                        if ($(header).is('.serial_number') || $(header).is('.dn_status') || $(header).is('.action')) {
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

            $('#search_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 10) {
                    table.draw();
                }
            });

            $('#scan_delivery_note').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                table.draw();
            });


            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            $('body').on('click','.printdeliverynote',function () {
                var deliverynote = $(this).parents('tr').attr('id');
                print(deliverynote);
            });

            {{--$('#scan_tracking').on('change',function () {--}}
                {{--var scan = $(this);--}}
                {{--var tracking = $(this).val();--}}
                {{--var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;--}}
                {{--if(numberRegex.test(tracking)) {--}}

                    {{--var url = "{{route("admin.delivery.receive.status","id")}}";--}}
                    {{--url = url.replace('id',tracking);--}}

                    {{--window.location.href = url;--}}
                {{--}else{--}}
                    {{--scan.val('');--}}
                {{--}--}}
            {{--});--}}


        });
    </script>
@endsection