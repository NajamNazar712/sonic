@extends('admin.layout.master')

@section('title', 'Canvas Bag')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Canvas Bag
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Canvas Bag No#</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Active Date / Time</th>
                                    <th class="border-primary border-darken-1">Remark</th>
                                    <th class="border-primary border-darken-1">Inactive Date / Time</th>
                                    <th class="border-primary border-darken-1">User</th>
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
    </div>



    <div class="modal fade" id="AddSackBagModal" data-backdrop="static" role="dialog" aria-labelledby="AddSackBagModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Canvas Bag</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="errormessage">

                    </div>
                    
                    <form method="post" id="add_sack_bag_form"
                        action="{{ route('admin.cargo_manifest.bags.sack_bag.store') }}"
                        class="form-horizontal mb-1" novalidate="novalidate" onkeydown="return event.key != 'Enter';">
                        @csrf
                        <input type="hidden" id="sack_bag_no_check">
                        <div class="row">
                            <div class="col-md-12">
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
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                    <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">Canvas Bag No#</th>
                                            <th class="border-primary border-darken-1">Remark</th>
                                            <th class="border-primary border-darken-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sackbag_detail">
                                        <tr>
                                            <td><input id="sack_bag_no_id" type="text" name="sack_bag_no[]" class="form-control" onchange="sack_bag_check_zero(this)" ></td>
                                            <td><input type="text" name="remarks[]" class="form-control" ></td>
                                            <td><span class="btn btn-danger" id="remove_row">x</span></td>
                                        </tr>
                                    </tbody>
                                    
                                </table>
                                
                           </div>
                        </div>
                        <div class="form-group ml-1">
                            {{-- <button type="button" id="addrow" class="btn btn-success ">Add Row</button> --}}

                            <button type="submit" name="add" class="btn btn-primary ml-2">Submit</button>
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



            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([934], session('permissions'))) !== 0)

                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add New',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            $("#add_sack_bag_form")[0].reset();
                            $("#sackbag_detail tr:not(:first-child)").empty();
                            $("#add_sack_bag_form select").val(null).trigger('change.select2');
                            $('#AddSackBagModal').modal('show');
                         
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        filename: 'Canvas Bag Report',
                        title: '',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        className:'btn btn-primary',
                        // Exclude the last column (Actions)
                        exportOptions: {
                            columns: ':not(:last-child)'
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
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {data: 'sack_bag_no', name: 'sack_bag_no', class: 'align-middle sack_bag_no'},
                    {data: 'origin', name: 'origin', class: 'align-middle origin'},
                    {data: 'active_time', name: 'active_time', class: 'align-middle active_time'},
                    {data: 'remarks', name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {data: 'inactive_time', name: 'inactive_time', class: 'align-middle inactive_time'},
                    {data: 'user_id', name: 'user_id', class: 'align-middle user_id'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'action', class: 'align-middle action', name:'action'}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();
                    });
                    this.api().table().columns.adjust();
                }
            });
            $("#add_sack_bag_form").validate({
                    errorClass: "danger",
                    successClass: 'success',
                    errorPlacement: function (error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function (form) {
                        if($("#sack_bag_no_check").val()!=1)
                        {
                            form.submit();
                        }else{
                            scan_sound(2);
                            toastr.error("Canvas Bag No# already exist!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                });
            
                // add sackbag_no in detail on scan
                $("#add_sack_bag_form #sackbag_detail").on("keydown","tr", function(e) {
                    if(e.which == 13 || e.keyCode == 13) {
                        var tr_index = $(this).index();
                        $(".errormessage").empty();
                        var sack_bag_no = $(this).find("#sack_bag_no_id").val();
                        if(sack_bag_no!='')
                        {
                            sack_bag_no_check(sack_bag_no,tr_index);
                        } else{
                            scan_sound(2);
                            toastr.error("Please fill Canvas Bag No!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                });

            $('body').on('click','#remove_row',function(){
                $(this).closest('tr').remove();
            });

            $('#datatable').on('click', '.status_sack_bag', function(event){
                let sackBagId = $(this).data('id');
                let newStatus = $(this).data('status');
                $.ajax({
                    url: "{{ route('admin.sack_bag.update_sack_bag_status') }}",
                    method: "POST",
                    data: {
                        id: sackBagId,
                        status: newStatus,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                        }
                        $('#datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON?.error || 'Failed to update sack bag status.';
                        toastr.error(errorMsg, 'Error', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });

            });
        });

        //this function check sackbag_no not duplicate on screen frontend 
        var current_sack_bag_no=Array(); 
        function sack_bag_check_zero(sack_bag_no)
        {   
            var sackbag_no=0;
            current_sack_bag_no = [];
            $("#sackbag_detail tr").each(function(index, element) {
                sackbag_no = $(element).find("#sack_bag_no_id").val();
                if(current_sack_bag_no.includes(sackbag_no))
                {   
                    scan_sound(2);
                    toastr.error("Canvas Bag Already Added!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});  scan_sound(2);
                    $(sack_bag_no).closest('tr').remove();
                    $("#sack_bag_no_check").val(1);
                } else{
                    current_sack_bag_no.push(sackbag_no);
                    $("#sack_bag_no_check").val(0);
                }
            });
        }

        // this function check input sack bag no exist in database 
        function sack_bag_no_check(sack_bag_no,index)
        {
            $("#sack_bag_no_check").val(0);
            $.ajax({
                url: '{!! route('admin.cargo_manifest.bags.sack_bag.sack_bag_check') !!}',
                method: 'POST',
                data: {
                    'sack_bag_no': sack_bag_no,
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function(data){
                if(data.error)
                {
                    setTimeout(() => {
                        $(".errormessage").empty();
                    }, 2500);
                    $(".errormessage").append('<div class="alert alert-danger">'+data.error+'</div>');
                    $(".errormessage").show();
                    $("#sack_bag_no_check").val(1);
                    $("#sack_bag_no_id").eq(index).val('');
                } else {
                    if( $("#sack_bag_no_check").val() == 0)
                    {
                        var row='<tr><td><input type="text" id="sack_bag_no_id" name="sack_bag_no[]" class="form-control" onchange="sack_bag_check_zero(this)"></td><td><input type="text" name="remarks[]" class="form-control"></td><td><span class="btn btn-danger" id="remove_row">x</span></td></tr>';
                        var $row = $(row);
                        $("#sackbag_detail").append($row);
                        $row.find('#sack_bag_no_id').focus();
                    }
                }
            });
        }

    </script>
@endsection