@extends('admin.layout.master')

@section('title', 'Multiple IBAN Number Change Report')

@section('content')
    <h1 class="mb-1">
        Multiple IBAN Number Change Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-5">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_iban_no" id="search_iban_no" placeholder="Search IBAN Number">
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                 @foreach($shippers as $shipper)
                                   <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                     @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S.No.</th>
                        <th class="border-primary border-darken-1">Account ID</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Bank Name</th>
                        <th class="border-primary border-darken-1">Branch Name</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Account Title</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">IBAN No.</th>
                        <th class="border-primary border-darken-1">Default</th>
                        <th class="border-primary border-darken-1">Bank Added at</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // function print(id) {
            //     $.ajax({
            //         url: '{!! route('admin.cargo.in_transit.print') !!}',
            //         method: 'POST',
            //         data: {
            //             'id': id,
            //             '_token': '{{ csrf_token() }}'
            //         }
            //     })
            //         .done(function(data) {
            //             var tab = window.open('', '_blank');

            //             if(!tab) {
            //                 swal({
            //                     title: 'Popup Blocker Enabled!',
            //                     text: 'Please add this site to your exception list.',
            //                     icon: 'error',
            //                     closeOnClickOutside: false,
            //                     closeOnEsc: false
            //                 });
            //             }
            //             else {
            //                 tab.document.write(data);
            //                 tab.document.close();
            //                 tab.focus();
            //             }
            //         });
            // }
            
            // $('#search_iban_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipper',
                width:'100%',
                allowClear:true
            });        
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
             jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                 if ( this.context.length ) {
                     blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.multiple_iban.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Account ID.');
                            head.push('Shipper Name');
                            head.push('Bank Name');
                            head.push('Branch Name');
                            head.push('City');
                            head.push('Account Title');
                            head.push('Account No.');
                            head.push('IBAN No.');
                            head.push('Default');
                            head.push('Bank Added at');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account_id);
                                row.push(values.shipper);
                                row.push(values.bankname);
                                row.push(values.bank_branch);
                                row.push(values.city);
                                row.push(values.account_title);
                                row.push(values.account_no);
                                row.push(values.iban);
                                row.push(values.default);
                                row.push(values.bank_added_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
               dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'User IBAN Number Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
               lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.multiple_iban.list') }}',
                    data: function (d) {
                        d.search_iban_no = $('#search_iban_no').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'account_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'account_id', name: 'user_bank_infos.id', class: 'align-middle account_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'bankname', name: 'bl.name', class: 'align-middle bankname'},
                    {data: 'bank_branch', name: 'user_bank_infos.bank_branch', class: 'align-middle bank_branch'},
                    {data: 'city', name: 'oc.name', class: 'align-middle text-center city'},
                    {data: 'account_title', name: 'user_bank_infos.account_title', class: 'align-middle account_title'},
                    {data: 'account_no', name: 'user_bank_infos.account_no', class: 'align-middle account_no'},
                    {data: 'iban', name: 'user_bank_infos.iban', class: 'align-middle iban'},
                    {data: 'default', name: 'user_bank_infos.default_bank', class: 'align-middle default'},
                    {data: 'bank_added_at', name: 'user_bank_infos.created_at', class: 'align-middle bank_added_at'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });


    </script>
@endsection