@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Completed Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="sdn_form" action="{{route('admin.delivery.completed.sdn.create.submit')}}" method="post">
                    @csrf
                    <input type="hidden" name="sdn_dncc_ids" id="sdn_dncc_ids">
                    <input type="hidden" name="sdn_hub_id" id="sdn_hub_id">
                    <input type="hidden" name="sdn_delivered_shipments" id="sdn_delivered_shipments">
                    <input type="hidden" name="sdn_count" id="sdn_count">

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">DNCC No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Route</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Shipments Delivered</th>
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        {{--<th class="border-primary border-darken-1">Emergency Amount</th>--}}
                        {{--<th class="border-primary border-darken-1">Total Amount</th>--}}
                        <th class="border-primary border-darken-1">Remarks</th>
                    </tr>
                    </thead>
                </table>

                <hr>

                    <div class="row">
                        <div class="col">
                            <div class="form form-horizontal">
                                <h3 class="form-section"><i class="la la-clipboard"></i>Deposit Details</h3>
                            </div>
                        </div>

                    </div>

                <div class="row mb-2">
                    <div class="col-2 text-center">
                        <h4 class="primary border-bottom">{{$hub_name}}</h4>
                    </div>
                    <div class="col-2 text-center">
                        <h4 class="primary border-bottom">{{ucfirst(Auth::user()->name)}}</h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-3">
                        <fieldset>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Total DNCC Amount</span>
                                </div>
                                <input type="text" class="form-control" name="total_dncc_amount" id="dncc_amount" readonly placeholder="Total DNCC Amount">
                            </div>
                        </fieldset>
                    </div>

                    {{--<div class="col-3">--}}
                        {{--<fieldset>--}}
                            {{--<div class="input-group">--}}
                                {{--<div class="input-group-prepend">--}}
                                    {{--<span class="input-group-text">Total Emergency Amount</span>--}}
                                {{--</div>--}}
                                {{--<input type="text" class="form-control" name="total_expenses" value="0" id="total_expense" readonly placeholder="Total Emergency Amount">--}}
                            {{--</div>--}}
                        {{--</fieldset>--}}
                    {{--</div>--}}
                    {{--<div class="col-3">--}}
                        {{--<fieldset>--}}
                            {{--<div class="input-group">--}}
                                {{--<div class="input-group-prepend">--}}
                                    {{--<span class="input-group-text">Total Net Amount</span>--}}
                                {{--</div>--}}
                                {{--<input type="text" class="form-control" name="total_amount" value="0" id="total_amount" readonly placeholder="Total Net Amount">--}}
                            {{--</div>--}}
                        {{--</fieldset>--}}
                    {{--</div>--}}
                    <div class="col-3">
                        <select name="bank_select" class="form-control select2" id="banks_list">
                            @foreach($banks_list as $banks)
                            <option value="{{$banks->id}}">{{$banks->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-2">
                        <button id="sdnSubmit" type="submit" class="btn btn-primary btn-block">Confirm</button>

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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#banks_list').select2({
                placeholder: 'Select a company bank'
            });
            var dncc_ids = [];
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
                ajax: '{{ route('admin.delivery.completed.dncc.list') }}',
                rowId: 'delivery_note_id',
                order: [[2, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note_id' ,name: 'delivery_note_id', class: 'align-middle text-center delivery_note'},
                    { data:'hub' ,name: 'hub', class: 'align-middle hub'},
                    { data:'rider' ,name: 'rider', class: 'align-middle rider'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count' ,name: 'shipments_count', class: 'align-middle shipments_count'},
                    { data:'delivered_shipments' ,name: 'delivered_shipments', class: 'align-middle delivered_shipments'},
                    { data:'received_cod_amount' ,name: 'received_cod_amount', class: 'align-middle received_cod_amount'},
                    // { data:'expense' ,name: 'expense', class: 'align-middle expense'},
                    // { data:'net_amount' ,name: 'net_amount', class: 'align-middle net_amount'},
                    { data:'remarks' ,name: 'remarks', class: 'align-middle remarks'},
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
                    $('.numeric').inputmask({
                        'alias': 'decimal',
                        'allowMinus': false,
                        'allowPlus': false,
                        'rightAlign': false,
                        'digits': 2,
                        'min': 0.00,
                        'max': 1000000000.00
                    });
                    //all dncc ids
                    var count_rows = table.data().count();
                    for(var i = 0;i<count_rows;i++){
                        id = table.row( i ).id();
                        dncc_ids.push(id);
                    }
                    $('#sdn_dncc_ids').val(dncc_ids);
                    //total amount received
                    table.column('.received_cod_amount', {
                        page: 'current'
                    }).every(function() {
                        var sum = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);

                        $('#dncc_amount').val(sum);
                    });
                    //total delivered shipments count
                    table.column('.delivered_shipments', {
                        page: 'current'
                    }).every(function() {
                        var sum = this
                            .data()
                            .reduce(function (a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);

                        $('#sdn_delivered_shipments').val(sum);
                    });
                    //table dncc count
                    var dncc_count = table.rows().count();
                    $('#sdn_count').val(dncc_count);
                    var first_hub_id = table.row('tr:eq(0)').nodes();
                    if($(first_hub_id).data('hub')){
                        $('#sdn_hub_id').val($(first_hub_id).data('hub'));
                    }

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.expense') || $(header).is('.serial_number') || $(header).is('.net_amount')) {
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


            var sum = 0;
            var total_net_amount = 0;
            $('body').on('change','#expense',function (event) {
                var net = 0;
                var expenseHandle = event.target;
                var exp = $(expenseHandle);
                var expense = parseInt(exp.val()) || 0;
                var row = exp.parents('tr').attr('id');
                var dncc_amount = parseInt(exp.parents('tr').find('td.received_cod_amount').text()) || 0;
                var net_amount = exp.parents('tr').find('td.net_amount').text();
                var netamount = exp.parents('tr').find('td.net_amount input.net_amount');
                var net = dncc_amount - expense;
                netamount.val(net);
                var sum_expense = 0;
                $(this).parents('tbody').find('tr td.expense input').each(function() {
                    var exp_val = parseInt($(this).val()) || 0;
                    sum_expense += exp_val;
                });
                $('#total_expense').val(sum_expense);
                var sum_net_amount = 0;
                $(this).parents('tbody').find('tr td.net_amount input').each(function() {
                    var net_val = parseInt($(this).val()) || 0;
                    sum_net_amount += net_val;
                });
                $('#total_amount').val(sum_net_amount);
            });
            function number_format(n){
                var value = n.toLocaleString(
                    undefined, // leave undefined to use the browser's locale,
                    // or use a string like 'en-US' to override it.
                    { minimumFractionDigits: 2 }
                );
                return value;
            }
            // $('#sdn_form').bind('submit',function (event) {
            //    event.preventDefault();
            //    var hubid = $('#sdn_hub_id').val();
            //     $( 'input.remarks' ).rules( "add", {
            //         required: true,
            //     });
            //    $('input.remarks').valid();
            //
            //
            //     });

                // this.submit();
            $('body').on('change','td.remarks input',function() {
                $(this).val($(this).val().trim());
            });
            $( "#sdn_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.remarks'));
                },
                submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Station deposit note is being created!',
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