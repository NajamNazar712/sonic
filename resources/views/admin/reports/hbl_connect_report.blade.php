@extends('admin.layout.master')

@section('title', 'Hbl Connect Report')

@section('content')
    <h1 class="mb-1">
        Hbl Konnect Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                 
                    <div class="col-3 mb-1">
                        <input type="text" name="delivery_note_id" class="form-control w-100 delivery_note_id" placeholder="Delivery Note ID" data-tags-input-name="delivery_note_id">
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left date_css">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">
                        </div>
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left date_css">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>
                </form>

                <div id="table">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">COD Amount</th>
                            <th class="border-primary border-darken-1">Delivery Note ID</th>
                            <th class="border-primary border-darken-1">Transaction ID</th>
                            <th class="border-primary border-darken-1">Created at</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>
        .date_css{
            height:40px;
            margin: 1px; 
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
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

          

            $('#search_form input.delivery_note_id').focus();
            $('#search_form input.delivery_note_id').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });


            
            $('#to_date').on('change', function() {
                $('#to_date-error').hide();
                $('#to_date').removeClass('danger');

            })


            $('#from_date').on('change', function() {
                $('#from_date-error').hide();
                $('#from_date').removeClass('danger');

            })
           

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var date_limit = '{{ Carbon\Carbon::now()->toDateString() }}';
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                max: new Date(date_limit),
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form').validate({
                
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#table').removeClass('d-none');
                    table.draw(true);
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
                        url: '{{ route('admin.reports.hbl_konnect.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                         

                            head.push('S. No.');
                            head.push('COD Amount');
                            head.push('Delivery Note ID');
                            head.push('Transaction ID');
                            head.push('Created at');
                            


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.amount);
                                row.push(values.delivery_note_id);
                                row.push(values.transaction_id);
                                row.push(values.created_at);
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
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Hbl Konnect Reports',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: [50, 0],
                ajax:{
                    url: '{{ route('admin.reports.hbl_konnect.list') }}',
                    
                    data: function (d) {
                        d.delivery_note_id = $('.delivery_note_id').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'amount' ,name: 'amount', class: 'align-middle text-center transaction_authentication_id'},
                    { data:'delivery_note_id' ,name: 'delivery_note_id', class: 'align-middle text-center delivery_note_id'},
                    { data:'transaction_id' ,name: 'transaction_id', class: 'align-middle text-center transaction_amount'},
                    { data:'created_at' ,name: 'created_at', class: 'align-middle text-center created_at'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            

        });

    </script>
@endsection