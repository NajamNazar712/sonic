@extends('admin.layout.master')

@section('title', 'Riders Request')

@section('content')
    <h1>Riders Request</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Rider Name</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Status</th>
{{--                                    <th class="border-primary border-darken-1"></th>--}}
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div style="display: none;">
                        <form id="rider_active_form" action="{{route('admin.management.rider.status')}}" method="post" class="mt-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="cid" id="cid">
                            <input type="hidden" name="status" id="cstatus">
                            <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                            <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.riders.rider_request.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Trax ID');
                            head.push('City Name');
                            head.push('Hub Name');
                            head.push('Rider Name');
                            head.push('Phone No.');
                            head.push('CNIC');
                            head.push('Address');
                            head.push('Type');
                            head.push('Route');
                            head.push('Category');
                            head.push('Added On');
                            head.push('Status');
                            head.push('Created By');
                            head.push('Updated By');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.city);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.phone);
                                row.push(values.cnic);
                                row.push(values.address);
                                row.push(values.rider_type);
                                row.push(values.route);
                                row.push(values.category);
                                row.push(values.created_at);
                                row.push(values.status);
                                row.push(values.created_by);
                                row.push(values.updated_by);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table =  $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Blacklisted Riders',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.riders.rider_request.list') }}',
                order: [[4, 'desc']],
                rowId : 'rider_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'cnic', name: 'cnic', class: 'align-middle cnic'},
                    {data: 'phone_no', name: 'phone_no', class: 'align-middle phone_no'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'updated_at', name: 'updated_at', class: 'align-middle updated_at'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    // {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Pending</option>' +
                        '<option value="1">Processed</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    $("#type_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Rider Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.sms').enable();
                }
                else {
                    table.button('.sms').disable();
                }
            });

            $("#addRider").on("show.bs.modal", function(e) {
                $.get( "/admin/management/riders/add", function( data ) {
                    $("#addRiderDiv").html(data);
                    var html = '<input name="rider_type" value="1" type="hidden">';
                    $('#addRiderForm').append(html);
                });
            });

            $('body').on('click','.deactivate',function (e) {
                var id = $(this).data('target-id');
                var rel = $(this).attr('rel');

                $('#rider_active_form #cid').val(id);
                $('#rider_active_form #cstatus').val(rel);
                if(rel == 'riderApprove'){
                    var atext = "Select Yes to Approve this Rider!";
                }
                swal({
                    title: 'Are You Sure?',
                    text: atext,
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $('#rider_active_form').submit();
                    }
                });

            });

            $("#addRider").on("show.bs.modal", function(e) {
                $.get( "/admin/management/riders/add", function( data ) {
                    $("#addRiderDiv").html(data);
                    var html = '<input name="rider_type" value="1" type="hidden">';
                    $('#addRiderForm').append(html);
                });
            });

            $('body').on('click','button.blacklist',function (e) {
                var rider_id = $(this).parents('tr').attr('id');

                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to UnBlock this Rider!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {

                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.management.riders.rider_blacklist') !!}',
                            method: 'POST',
                            data: {
                                'rider_id': rider_id,
                                'action':'unblock',
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status == 0){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            table.draw(false);
                            UnblockPagePermanently();
                        });
                    }
                });

            });

        });
    </script>

@endsection