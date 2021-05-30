@extends('admin.layout.master')
@section('title','Shipper Insurance Report')

@section('content')
    <h1 class="mb-1">
        Shipper Insurance Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="track_form" class=" mb-1 justify-content-center" novalidate="novalidate">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <select name="shipper_name" id="shipper_name" class="form-control select2" >
                                    @foreach($shipper_name as $shipper)
                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Date From">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="Date To">
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-2">
                        <div class="form-group">
                            <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width search"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Insured</th>
                        <th class="border-primary border-darken-1">Charges</th>
                        <th class="border-primary border-darken-1">Insurance</th>
                        <th class="border-primary border-darken-1">Date</th>
                    </tr>
                    </thead>
                    <tfoot align="right">
                    <tr>
                        <th colspan="5"></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="ViewSKUModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewSKUModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Insurance Charges</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">

                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-grey" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">

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
        table tfoot tr th, table.dataTable tfoot tr th {
            padding-left: 0.5em;
            padding-right: 0.5em;
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
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.22/api/sum().js" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.shipper_insurance.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Shipper');
                            head.push('Insured');
                            head.push('Insurance');
                            head.push('Date');

                            var insurance_count = 0;

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.insurance);
                                row.push(values.total_insurance);
                                row.push(values.created_at);

                                body.push(row);
                                insurance_count+=values.total_shipments

                            });


                            footer.push('');
                            footer.push('Total');
                            footer.push('-');
                            footer.push('-');
                            footer.push(insurance_count);
                            footer.push('-');
                           
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head, footer: footer};
                }
            });

            var search_date_from = $('#track_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_to').pickadate('picker').set('min', $('#track_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_to = $('#track_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_from').pickadate('picker').set('max', $('#track_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Shipper Insurance Report',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                ajax: {
                    url: '{{ route('admin.reports.shipper_insurance.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.shipper_name = $('select[name="shipper_name"]').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'shId',
                order: [[6, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number_link', class: 'align-middle text-center tracking_number_link'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle text-center shipper'},
                    { data:'insurance' ,name: 'insurance', class: 'align-middle text-center insurance'},
                    {data: 'charges', name: 'si.price', class: 'align-middle text-center charges'},
                    {data: 'total_insurance', name: 'total_insurance', class: 'align-middle text-center total_insurance'},
                    { data:'created_at' ,name: 'created_at', class: 'align-middle created_at text-center'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // converting to interger to find total
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    var insurance_count = api
                        .column( 5 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );


                    // Update footer by showing the total with the reference of the column index
                    $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 1 ).footer() ).html();
                    $( api.column( 2 ).footer() ).html();
                    $( api.column( 3 ).footer() ).html();
                    $( api.column( 4 ).footer() ).html();
                    $( api.column( 5 ).footer() ).html(insurance_count);

                },

                initComplete: function() {

                    this.api().table().columns.adjust();
                },
            });


            $('#track_form #shipper_name').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Shipper Name',
                allowClear:true
            });


            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });

            $('#track_form').bind('submit', function (e) {
                e.preventDefault();

                table.draw();
            });

            $('#datatable tbody').on('click', 'tr td.charges button', function() {
                var tracking_number = table.row($(this).parents('tr')).data().tracking_number;

                $.ajax({
                    url: '{!! route('admin.reports.shipper_insurance.charges') !!}',
                    method: 'POST',
                    data: {
                        'tracking_number': tracking_number,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    if (data.status === 1) {

                        var html = '';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead><tr><th>S No.</th><th><strong>Shipping Mode</strong></th><th><strong>Range Up</strong></th><th><strong>Range Down</strong></th><th><strong>Charges</strong></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.insurance_charges, function (index, value) {
                            var ind = index + 1;
                            html += '<tr class=""><td>' + ind + '</td>';
                            if(value.shipping_mode_id === 1){
                                html += '<td>Overnight</td>';
                            }
                            else if(value.shipping_mode_id === 2){
                                html += '<td>Overland</td>';
                            }
                            else if(value.shipping_mode_id === 3){
                                html += '<td>Detain</td>';
                            }
                            else{
                                html += '<td>Same-day</td>';
                            }
                            html += '<td>' + value.range_up + '</td>';
                            html += '<td>' + value.range_down + '</td>';
                            html += '<td>' + value.charges + '</td></tr>';
                        });
                        html += '</tbody></table>';

                        $('#ViewSKUModal .modal-body').html(html);
                        $('#ViewSKUModal').modal('show');

                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });

        });

    </script>
@endsection