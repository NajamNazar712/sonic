@extends('client.layout.master')

@section('title', 'Schedule Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Schedule Pickups
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <div class="container">
                                <div class="row justify-content-center">

                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                   <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_from_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_from_date" placeholder="Requested Date From">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                     <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_to_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_to_date" placeholder="Requested Date To">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="button" id="search_filter_btn"
                                                class="mb-1 btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search" style="margin-right: 10px"></i> Search
                                        </button>
                                    </div>
                                </div>


                            </div>

                           
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">ID</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                    <th class="border-primary border-darken-1">Date & Time</th>
                                    <th class="border-primary border-darken-1">Ask Time</th>
                                    <th class="border-primary border-darken-1">Shipments/Pieces</th>
                                    <th class="border-primary border-darken-1">Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Additional Services</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Scheduled Days</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Rider ID</th>
                                    <th class="border-primary border-darken-1">Assigned Courier</th>
                                    <th class="border-primary border-darken-1">Special Request</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

  <!---- start of show additional services modal---->
  <div class="modal fade text-left" id="AdditionalServiceModal" data-backdrop="static" tabindex="-1" role="dialog"
  aria-labelledby="AdditionalServiceModal"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
      <div class="modal-header bg-primary white">
          <h4 class="modal-title white">Additional Services</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
          <div class="container">
              <!-- start additional services -->
              {{-- <div class="d-flex justify-content-start vh-100 pl-1">
                      <b class="text-dark"> Additional Services </b>
              </div>--}}
              {{-- <div class="d-flex justify-content-start align-items-center">
                      <!-- start service list -->
                          <div class="services-list col-12 additionalservices">
                          
                          </div>
                      <!-- end service list -->
              </div>  --}}
              <table class="table table-bordered">
                      <thead>
                              <tr role="row" class="bg-primary white">
                                  <th class="border-primary border-darken-1">S. No.</th>
                                  <th class="border-primary border-darken-1">Service</th>
                                  <th class="border-primary border-darken-1">Qty</th>
                              </tr>
                      </thead>
                      <tbody>

                      </tbody>

                  </table>
              <!-- end additional services -->

              
          </div>
      </div>
  </div>
  </div>
</div>
<!----end of show additional services modal --->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }

        .legends {
            cursor: pointer;
        }

        .custom-nav {
           margin-left: 4px;
        }

        .custom-nav li {

           border: 1px solid #CCCCCC;
           border-radius: 4px;
        }

        .custom-nav li:first-child {

           margin-right: 4px !important;
        }

        .custom-nav li:last-child {

           margin-left: 4px !important;
        }

        .custom-nav .nav-item a.nav-link {

            color: #CCCCCC;
            border: 1px solid #CCCCCC !important;
        }

        .custom-nav .nav-item p {

            line-height: 1.4;
        }

        .custom-nav .nav-item a.active {

            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        .custom-nav .nav-item a:hover {
            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        /* start scheduled days area */

        /* Hide checkboxes */
        .scheduled_days_area input[type="checkbox"] {
        display: none;
        }

        /* Style labels for checkboxes */
        .scheduled_days_area input + label {
        display: inline-block;
        border: 1px solid #CCCCCC;
        background: #fff;
        padding: 5px 1px;
        color: #A3A3A3;
        border-radius: 5px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
        }

        /* Style the checkbox's unchecked state */
        .scheduled_days_area input:checked + label {
            background: #5587b4;
            border-color: #64A0D2;
            color: #fff;
        }

        /* Style the checkbox's unchecked state icon */
        .scheduled_days_area input:checked + label:before {
        font-size: 17px;
        position: absolute;
        left: 24px;
        top: 6px;
        opacity: 1;
        }

        /* .scheduled_days_area input + label:hover {
            background: #fff;
            border-color: #CCCCCC;
            color: #000;
        } */

        .scheduled_days_area input:not(:checked) + label:hover {
            background: #F7FAFC;
            border-color: #64A0D2;
            color: #64A0D2;
        }

        .scheduled_days_area .item-column {
            margin-right: 10px;
        }

        /* end scheduled days area */

        /* start addition services */

        .service-item {

            border: 1px solid #CCCCCC;
            border-radius: 4px;
            padding-top: 12px;
            padding-bottom: 12px;

        }

        .btn-service {
            border-radius: 50%;
            padding: 4px;
            width: 30px;
            height: 30px;
            transition: all 0.4s;
        }

        .btn-service:hover {

            background: #6496BE !important;
        }

        .btn-service:active,
        .btn-service:focus {

            background: #6496BE !important;
        }

        .service-item .custom-input-number[type="number"]::-webkit-inner-spin-button,
        .service-item .custom-input-number[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }

        /* end addition services */

    </style>
            <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

@endsection

@section('js')
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
        type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}?v=24052022" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function () {
    
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.pickup.schedule.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Pickup Request ID');
                            head.push('Requested Date');
                            head.push('Current Rider');
                            head.push('Last Rider');
                            head.push('Pickup Note ID');
                            head.push('Shipment(s) Booked');
                            head.push('Shipment(s) Rider Picked');
                            // head.push('Shipment(s) Received');
                            head.push('Shipper');
                            head.push('Territory');
                            head.push('Contact Person');
                            head.push('Vendor');
                            head.push('Brand Name');
                            head.push('Contact No(s).');
                            head.push('Address');
                            head.push('City');
                            head.push('Status');
                            head.push('Trax Reason');
                            head.push('Trax Remark(s)');
                            head.push('Shipper Remark(s)');
                            head.push('Rider Remark(s)');
                            head.push('Assigned Date');
                            head.push('Attempt Date');
                            head.push('Aging');
                            head.push('Attempt(s)');


                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.pickup_request_id);
                                row.push(values.requested_date);
                                row.push(values.current_rider);
                                row.push(values.last_rider);
                                row.push(values.pickup_note_id);
                                row.push(values.booked);
                                row.push(values.shipments_rider_picked);
                                // row.push(values.received);
                                row.push(values.shipper);
                                row.push(values.territory);
                                row.push(values.contact_person);
                                row.push(values.vendor_name);
                                row.push(values.brand_name);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.pickup_status);
                                row.push(values.trax_reason);
                                row.push(values.trax_remarks);
                                row.push(values.shipper_remarks);
                                row.push(values.rider_remarks);
                                row.push(values.assigned_date);
                                row.push(values.attempted_date);
                                row.push(values.aging);
                                row.push(values.attempts);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var days_map={
                1: 'Mo',
                2: 'Tu',
                3: 'We',
                4: 'Th',
                5: 'Fr',
                6: 'Sa',
            };
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
              
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Schedule Pickups',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
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
                ajax: {
                    url: '{{ route('cod.pickup.schedule.list') }}',
                    data: function (d) {
                        d.before_cut_off_time = $('#search_filter').val();
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                        d.pickup_status_id = $('#status_filter_input').val();
                    }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                   
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'pickup_requests.id',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'id', name: 'id', class: 'align-middle id'},
                    {data: 'pickup_request_id', name: 'pickup_request_id', class: 'align-middle pickup_request_id'},

                    {data: 'pickup_date', name: 'pickup_date', class: 'align-middle pickup_date'},
                    {data: 'time_range', name: 'time_range', class: 'align-middle ask_time'},
                    {data: 'shipment_pieces', name: 'shipment_pieces', class: 'align-middle shipments', orderable: false},
                    {data: 'weight', name: 'weight', class: 'align-middle weight'},
                    {data: 'services_count_btn', name: 'services_count', class: 'align-middle text-center services_count'},
                    {data: 'product', name: 'product', class: 'align-middle product'},
                    {data: 'shipper', name: 'shipper', class: 'align-middle shipper',render:function(data,type,row){
                        return row.user_id +'-'+ row.shipper;
                    }},
                    {data: 'days', name: 'days', class: 'align-middle text-center days',orderable: false,render:function(data,type,row){
                        var days_string=row.days.split(',').map(dayvalue=>days_map[parseInt(dayvalue)] || dayvalue);
                        days_string=days_string.join(' ,');
                        return days_string;
                    }},
                    
                    {data: 'hub', name: 'hub', class: 'align-middle station'},
                    {data: 'route_code', name: 'route_code', class: 'align-middle route_code'},
                    {data: 'rider_id', name: 'rider_id', class: 'align-middle rider_id'},
                    {data: 'current_rider', name: 'current_rider', class: 'align-middle current_rider'},
                    {data: 'special_request', name: 'special_request', class: 'align-middle special_request'},
                

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.days') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign').enable();
                    table.button('.update').enable();
                } else {
                    table.button('.assign').disable();
                    table.button('.update').disable();
                }
            });

        });
        function showadditionalservices(event,pickup_request_id){
            $("#AdditionalServiceModal table tbody").empty();
            $.ajax({
                url:'{{ route('cod.pickup.pickup_request_services') }}',
                method:'POST',
                data:{
                    'pickup_request_id':pickup_request_id,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
                
                if (data.status == 0) {
                            $.each(data.pickup_request_services,function(key,value) {
                              $("#AdditionalServiceModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle service_name">'+value.service_name+'</td><td class=" align-middle service_count">'+value.count+'</td></tr>')
                            });
                        } else {
                            toastr.error('No pickup address found!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                        });
                }
            })
           $("#AdditionalServiceModal").modal("show");
        }

    </script>
@endsection