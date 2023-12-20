@extends('admin.layout.master')

@section('title', 'Sack Bag')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Sack Bag
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Sack Bag No#</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Remark</th>
                                    <th class="border-primary border-darken-1">User</th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="AddSackBagModal" data-backdrop="static" role="dialog" aria-labelledby="AddSackBagModal"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Sack Bag</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form method="post" id="add_sack_bag_form"
                        action="{{ route('admin.cargo_manifest.bags.sack_bag.store') }}"
                        class="form-horizontal mb-1" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{-- <label>Sack Bag No#</label> --}}
                                    <input type="text"  name="sack_bag_no" class="form-control" placeholder="Sack Bag No" data-rule-required="true" data-msg-required="Sack Bag No is required">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select name="origin" id="origin_select" class="form-control select2"
                                      data-rule-required="true" data-msg-required="Origin is required">
                                        @foreach($origins as $origin)
                                            <option value="{{ $origin->id }}">{{ $origin->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text"  name="remarks" class="form-control" placeholder="Remarks">
                                </div>
                            </div>
                        </div>
                   
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
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
    
                /* color: #64A0D2 !important; */
                border: 1px solid #64A0D2 !important;
                /* background-color: #F7FAFC !important; */
                color: #fff!important;
                background: #5587b4!important;
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
            .delay_time{
                background-color: #8fc5ea;
                /* background-color: #9fa1ae; */
                color: white;
                /* background-color: #FF0000; */
                /* background-color: #FFA500; */
            }
            .status_tab_active{
                color: #fff;
                background-color: #649bc8;
            }
    
        </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function () {
            $('#estimated_weight').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
            });
            
            $('#origin_select').prepend('<option value="" selected="selected">Select Origin</option>').select2({
                placeholder: 'Select Origin',
                width: '100%',
                dropdownParent:$('#AddSackBagModal')
            });

            // jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
            //     if (this.context.length) {
            //         body = [];
            //         var params = table.ajax.params();
            //         params.start = 0;
            //         params.length = -1;
            //         params.excel = true;
            //         var jsonResult = $.ajax({
            //             url: '{{ route('admin.cargo_manifest.bags.sack_bag.list') }}',
            //             data: params,
            //             success: function (result) {
            //                 head = [];

            //                 head.push('S.No');
            //                 head.push('Pickup Request ID');
            //                 head.push('Pickup Date');
            //                 head.push('Ask Time');
            //                 head.push('Shipments/Pieces');
            //                 head.push('Weight (KG)');
            //                 head.push('Additional Services');
            //                 head.push('Status');
            //                 head.push('Product');
            //                 head.push('Service');
            //                 head.push('Shipment Type');
            //                 head.push('Shipper');
            //                 head.push('Contact Person');
            //                 head.push('Contact No(s).');
            //                 head.push('Address');
            //                 head.push('City');
            //                 head.push('Route Code');
            //                 head.push('Route Rider');
            //                 head.push('Route Rider Phone');
            //                 head.push('Assigned Courier');
            //                 head.push('Assigned Courier Phone');
                     
            //                 // head.push('Current Rider');
            //                 // head.push('Last Rider');
            //                 // head.push('Pickup Note ID');
            //                 // head.push('Shipment(s) Booked');
            //                 // head.push('Shipment(s) Rider Picked');
            //                 // head.push('Shipment(s) Received');
                        
            //                 // head.push('Territory');
                         
            //                 // head.push('Vendor');
            //                 // head.push('Brand Name');
                        
                        
            //                 // head.push('Trax Reason');
            //                 // head.push('Trax Remark(s)');
            //                 // head.push('Shipper Remark(s)');
            //                 // head.push('Rider Remark(s)');
            //                 // head.push('Assigned Date');
            //                 // head.push('Attempt Date');
            //                 // head.push('Aging');
            //                 // head.push('Attempt(s)');


            //                 $.each(result.data, function (index, values) {
                            
            //                     row = [];

            //                     row.push(index + 1);
            //                     row.push(values.pickup_request_id);
            //                     row.push(values.pickup_date);
            //                     row.push(values.time_range);
            //                     // row.push(values.last_rider);
            //                     // row.push(values.pickup_note_id);
            //                     // row.push(values.booked);
            //                     // row.push(values.shipments_rider_picked);
            //                     // row.push(values.received);
            //                     row.push(values.shipment_pieces);
            //                     row.push(values.weight);
            //                     row.push(values.services_count);
            //                     row.push(values.status);
            //                     row.push(values.product);
            //                     row.push(values.service);
            //                     row.push(values.shippment_type);
            //                     row.push(values.shipper);
            //                     // row.push(values.territory);
            //                     row.push(values.contact_person);
            //                     // row.push(values.vendor_name);
            //                     // row.push(values.brand_name);
            //                     row.push(values.contact_number);
            //                     row.push(values.address);
            //                     row.push(values.city);
            //                     row.push(values.route_code);
            //                     row.push((values.rider_id!=null?values.rider_id + '-' + values.rider_name:''));
            //                     row.push(values.rider_phone);
            //                     row.push((values.current_rider_id !=null?values.current_rider_id + '-' + values.current_rider:''));
            //                     row.push(values.current_rider_phone);
            //                     // row.push(values.pickup_status);
            //                     // row.push(values.trax_reason);
            //                     // row.push(values.trax_remarks);
            //                     // row.push(values.shipper_remarks);
            //                     // row.push(values.rider_remarks);
            //                     // row.push(values.assigned_date);
            //                     // row.push(values.attempted_date);
            //                     // row.push(values.aging);
            //                     // row.push(values.attempts);


            //                     body.push(row);
            //                 });
            //             },
            //             async: false
            //         });

            //         return {body: body, header: head};
            //     }
            // });


            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([18, 19], session('permissions'))) !== 0)

                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add New',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            $("#add_sack_bag_form")[0].reset();
                            $("#add_sack_bag_form select").val(null).trigger('change.select2');
                            $('#AddSackBagModal').modal('show');
                         
                        }
                    },
                
                    'reset'
                ],
                @else
                buttons: [
                    'reset'
                ],
                @endif
                scrollX: true, scrollY: '500px',
                // select: {
                //     info: false,
                //     style: 'multi',
                //     selector: 'td.select-checkbox',
                //     className: 'selected bg-primary bg-lighten-5 primary'
                // },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.cargo_manifest.bags.sack_bag.list') }}',
                    // data: function (d) {
                    //     d.before_cut_off_time = $('#search_filter').val();
                    //     d.requested_from_date = $('#requested_from_date').val();
                    //     d.requested_to_date = $('#requested_to_date').val();
                    //     d.pickup_status_id = $('#status_filter_input').val();
                    //     d.pickup_reason_id = $('#status_filter_input_reason').val();
                    // }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'id',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'sack_bag_no', name: 'sack_bag_no', class: 'align-middle sack_bag_no'},
                    {data: 'origin', name: 'origin', class: 'align-middle origin'},
                    {data: 'remarks', name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {data: 'user_id', name: 'user_id', class: 'align-middle user_id'},
                 
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    // var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    // var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    // var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    // var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        
                        // if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                        //     $(td).appendTo($(search));
                        // } else {
                        //     var current = $(input).appendTo($(search)).on('change', function () {
                        //         column.search($(this).val(), false, false, true).draw();
                        //     }).wrap(td).after(icon);

                        //     if (column.search()) {
                        //         current.val(column.search());
                        //     }
                        // }
                    });

                    this.api().table().columns.adjust();
                }

            });

 

         

          
            
            // $('#AddRemarksModal').on('hidden.bs.modal', function () {
            //     $('#add_remark').val('');
            //     $("#remark_pickup_request_id").val('');
            // });
            $("#add_sack_bag_form").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });


          
        
           
            // increment and decrement buttons

            $('.quantity').TouchSpin({
                min: 0,
                max: 1000,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#add_pickup_request').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
              
                submitHandler: function(form) {
                    $('#add_pickup_request button#add').prop('disabled', true);
                    // $('#product_select').attr('disabled', false);
                    if($('.apply-checked:checked').length>0 || $("#regular_pickup").val()==1){
                        swal({
                            title: 'Please Wait!',
                            text: 'Pickup request is being added!',
                            icon: 'info',
                        buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                    }else{
                        toastr.error('Select Days in Schedule', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    }
                  
               
                }
            });

            $('.apply-checked').change(function() {

                if ($(this).is(':checked')) {

                    $(this).attr('checked', 'checked');

                } else {

                    $(this).removeAttr('checked');
                }
            });

        });
        
  
    </script>
@endsection