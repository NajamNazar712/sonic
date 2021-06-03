@extends('admin.layout.master')

@section('title', 'Vehicle In Transit')

@section('content')
    <h1 class="mb-1">
        Vehicle In Transit
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-1 justify-content-center">
                    <div class="col-8 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            <div class="col-12">
                                <div  class="row">   
                                    <div class="col-12">
                                        <div class="form-group pb-1">
                                            <select name="route_management" class="select2" id="route_management" >
                                            @foreach($route_managements as $route_management)
                                                <option value="{{ $route_management->id }}">{{ $route_management->route_titke }}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-12">
                                <div  class="row">   
                                    <div class="col-12">
                                        <div class="form-group pb-1">
                                            <select name="fleet" class="select2" id="fleet" >
                                            @foreach($fleets as $fleet)  
                                                <option value="{{ $fleet->id }}">{{ $fleet->reg_number }}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                            </div>    
                            <div  class="row">   

                                <div class="col-12">
                                <!-- <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button> -->
                                    <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>

                            </div>
                       
                        </form>
                    </div>
                </div>
                <div class="" id="">
                    
                    <table class="table table-bordered datatable" id="datatable" style="width:100%;z-index: 3;">
                        <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Vehicle</th>
                                <th class="border-primary border-darken-1">Route Title</th>
                                <th class="border-primary border-darken-1">Master Cargo</th>
                                <th class="border-primary border-darken-1">Origin</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Driver</th>
                                <th class="border-primary border-darken-1">Vendor</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <div class="modal fade" id="bags_modal" data-backdrop="static" role="dialog" aria-labelledby="bags_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bags_modal_title">Master Cargo Bag(s)</h4>

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2 , #ff4961);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c  , #e0b668de);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-arrived_shipments {
            background-image: linear-gradient(45deg, #074077, #2fbef5);
            background-repeat: repeat-x;
        }
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
             padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            width:100%;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
                    box-shadow:  0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            width:auto; 
            border-bottom:none;
        }
        fieldset.scheduler-border .legend-scale ul {
            margin: 0;
            margin-bottom: 10px;
            padding: 0;
            float: left;
            list-style: none;
        }
        fieldset.scheduler-border .legend-scale ul li {
            font-size: 100%;
            list-style: none;
            margin-left: 0;
            line-height: 18px;
            margin-bottom: 3px;
        }
        fieldset.scheduler-border ul.legend-labels li span {
            display: block;
            float: left;
            height: 18px;
            width: 40px;
            margin-right: 5px;
            margin-left: 0;
            /* border: 1px solid #999; */
        }
        span{
            font-size: 15px;
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

<script type="text/javascript">
        $(document).ready(function () {
            function print(id) {
                console.log(id);
                $.ajax({
                    url: '{!! route('admin.master_cargo.in_transit.print') !!}',
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
            $('#search_form #route_management').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Route',
                allowClear:true
            });
            $('#search_form #fleet').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Vehicle',
                allowClear:true
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
                            url: '{{ route('admin.runner.intransit.list') }}',
                            data: params,
                            success: function (result) {
                                head = [];

                                head.push('S. No.');
                                head.push('Vehicle');
                                head.push('Route Title');
                                head.push('Master Cargo');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Driver');
                                head.push('Vendor');

                                $.each(result.data, function(index, values) {
                                    row = [];

                                    row.push(index + 1);
                                    row.push(values.vehicle);
                                    row.push(values.route_title);
                                    row.push(values.bags);
                                    row.push(values.origin);
                                    row.push(values.destination);
                                    row.push(values.driver_name);
                                    row.push(values.vendor);
                                    body.push(row);
                                });
                            },
                            async: false
                        });
                        UnblockPagePermanently();
                        return {body: body, header:head};
                }
            });
            
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Vehicle In Transit',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.runner.intransit.list') }}',
                    data: function (d) {
                        d.search_route_management = $('#route_management').val();
                        d.search_fleet = $('#fleet').val();
                    }
                },
                rowId: 'id',
                order: [[0, 'desc']],
                columns: [
                    {orderable: false, searchable: false,name: 'serial_number',class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'vehicle' ,name: 'f.reg_number', class: 'align-middle text-center vehicle'},
                    { data:'route_title' ,name: 'rm.route_title', class: 'align-middle route_title'},
                    // { data:'bags' ,name: 'bags', class: 'align-middle bags'},
                    {data: 'id_padded_link', name: 'master_cargoes.id', class: 'align-middle master_cargo_number'},

                    { data:'origin' ,name: 'or.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'des.name', class: 'align-middle destination'},
                    { data:'driver_name' ,name: 'master_cargoes.driver_name', class: 'align-middle driver_name'},
                    { data:'vendor' ,name: 'tmv.name', class: 'align-middle vendor'},
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

            
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click', 'tr td.bags button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bags_modal .modal-body').html('');
                $('#bags_modal').modal('show');
                $.ajax({
                    url: '{!! route('admin.master_cargo.in_transit.short_received_bags') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var bag_numbers = '';
                            $.each(data.bag_numbers, function(index, bag_number) {
                                bag_numbers += bag_number + '<br>';
                                $.each(data.tracking_numbers, function(index, tracking_numbers) {
                                    if(index == bag_number){
                                        $.each(tracking_numbers, function(tracking_index, tracking_number) {
                                                bag_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                        });
                                    }
                                });
                                bag_numbers += '<br>';
                            });
                            $('#bags_modal .modal-body').html(bag_numbers);
                            $('#info_modal').modal('show');
                        }
                    });
            });


            $('#datatable tbody').on('click','tr td.master_cargo_number button.print',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                console.log(id);
                print(id);
            });
        });

    </script>
@endsection