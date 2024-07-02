@extends('retail.layout.master')

@section('title', 'Prepaid Flyer Requests')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <h1 class="mb-1">
                Prepaid Flyer Requests
                </h1>
            </div>
            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('retail.inc.messages')
                        <div class="row mb-2 justify-content-center">
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Search Date (From)" data-value="">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Search Date (To)" data-value="">
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>
                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Flyer Type</th>
                                <th class="border-primary border-darken-1">Flyer Size</th>
                                <th class="border-primary border-darken-1">Trax Centre</th>
                                <th class="border-primary border-darken-1">Qty</th>
                                <th class="border-primary border-darken-1">Amount</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Total CN Number Used</h4>

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
    </div>
    <!--Shipments popup -->

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    @endsection

@section('js')
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#to_date').pickadate('picker').set('min', $('#from_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#from_date').pickadate('picker').set('max', $('#to_date').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('retail.flyer_shipper.other_list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Total CN Number Used');
                            head.push('Retail Note');
                            head.push('Trax Center/Franchise');
                            head.push('Booking Code');
                            head.push('Staff Name');
                            head.push('Booking Date');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.total_shipments);
                                row.push(values.performa_no);
                                row.push(values.category);
                                row.push(values.booking_code);
                                row.push(values.user);
                                row.push(values.booking_date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Parcel Receiving',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('retail.flyer_shipper.other_list') }}',
                    data: function (d) {
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
            
                columns: [
            { data: 'id', name: 'id' },
            { data: 'shipper_name', name: 'shipper_name' },
            { data: 'package_type', name: 'package_type' },
            { data: 'size', name: 'size' },
            { data: 'center_name', name: 'center_name' },
            { data: 'qty', name: 'qty' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        
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


                       
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        
                    });
                   

                    
                    this.api().table().columns.adjust();
                }
            });


            var route = '{!! route('retail.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_button button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('retail.flyer_shipper.other_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'performa_no': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.status == 1) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                            $('#shipments_modal').modal('show');
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.performa_button button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                print(id);
            });
            function print(id) {
                $.ajax({
                    url: '{!! route('retail.flyer_shipper.other_print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data) {
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

            $('#search_filter_btn').on('click', function(){
                table.draw(true);
            })

            $('#datatable tbody').on('click', '#rejected', function(event) {
                var id = $(this).val(); // Get the value of the rejected button
                $.ajax({
                    url: '{!! route('retail.flyer_shipper.changestatus') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'retail_shipper_request_id': id,
                        'status_type':'Rejected'
                    }
                })
                .done(function(data) {
                    if (data) {
                      if(data.status==0){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        table.draw();
                      }
                    }
                });
              
            
            });

            $('#datatable tbody').on('click', '#confirmed', function(event) {
                var id = $(this).val(); // Get the value of the rejected button
                $.ajax({
                    url: '{!! route('retail.flyer_shipper.changestatus') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'retail_shipper_request_id': id,
                        'status_type':'Confirmed'
                    }
                })
                .done(function(data) {
                    if (data) {
                      if(data.status==0){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        table.draw();
                      }
                    }
                });
            });

           
            $('#datatable tbody').on('click', '#completed', function(event) {
                var id = $(this).val(); // Get the value of the rejected button
                $.ajax({
                    url: '{!! route('retail.flyer_shipper.changestatus') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'retail_shipper_request_id': id,
                        'status_type':'Completed'
                    }
                })
                .done(function(data) {
                    if (data) {
                      if(data.status==0){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        table.draw();
                      }
                    }
                });
            });

           

        });
    </script>
@endsection