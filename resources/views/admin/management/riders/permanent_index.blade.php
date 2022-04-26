@extends('admin.layout.master')

@section('title', 'Permanent Riders')

@section('content')
<h1>Permanent Riders</h1>

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
                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1">S No.</th>
                                <th class="border-primary border-darken-1">Employee ID.</th>
                                <th class="border-primary border-darken-1">City</th>
                                <th class="border-primary border-darken-1">Zone</th>
                                <th class="border-primary border-darken-1">Hub</th>
                                <th class="border-primary border-darken-1">Name</th>
                                <th class="border-primary border-darken-1">Phone No</th>
                                <th class="border-primary border-darken-1">CNIC</th>
                                <th class="border-primary border-darken-1">Address</th>
                                <th class="border-primary border-darken-1">Route</th>
                                <th class="border-primary border-darken-1">Main Category</th>
                                <th class="border-primary border-darken-1">Incentive Amount</th>
                                <th class="border-primary border-darken-1">Sub-Category</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Created By</th>
                                <th class="border-primary border-darken-1">Created At</th>
                                <th class="border-primary border-darken-1">Updated By</th>
                                <th class="border-primary border-darken-1">Updated At</th>
                                <th class="border-primary border-darken-1"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div style="display: none;">
                    <form id="rider_active_form" action="{{route('admin.management.riders.status')}}" method="post" class="mt-2">
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

@if (session('role_id') == 1 || in_array(383, session('permissions')))
    <div class="modal fade" id="send_sms_modal" role="dialog" aria-labelledby="send_sms_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form-horizontal" method="POST" action="{{ route('admin.management.riders.send_sms') }}" novalidate="novalidate">
                    {{ csrf_field() }}

                    <div class="modal-header">
                        <h4 class="modal-title" id="send_custom_sms_title">Send SMS</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="selected_riders" id="selected_riders">
                        <div class="form-group">
                            <label>Body</label>
                            <textarea type="text" id="sms_body" name="body" class="form-control body" placeholder="Body*" data-rule-required="true" data-msg-required="Body is required"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="d-inline-block">
                                <label>No. Of SMS</label>
                                <span id="n_sms"></span>
                            </div>
                            <div class="d-inline-block pull-right">
                                <label>No. of Characters </label>
                                <span id="l_sms"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="send" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

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
            var selected_rows = [];
            @if (session('role_id') == 1 || in_array(383, session('permissions')))
                autosize($('#send_sms_modal .body')[0]);
            @endif
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.riders.permanent.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee ID');
                            head.push('City Name');
                            head.push('Zone');
                            head.push('Hub Name');
                            head.push('Rider Name');
                            head.push('Phone No.');
                            head.push('CNIC');
                            head.push('Address');
                            head.push('Route');
                            head.push('Main Category');
                            head.push('Incentive Amount');
                            head.push('Sub-Category');
                            head.push('Status');
                            head.push('Created By');
                            head.push('Created At');
                            head.push('Updated By');
                            head.push('Updated At');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.city);
                                row.push(values.zone);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.phone);
                                row.push(values.cnic);
                                row.push(values.address);
                                row.push(values.route);
                                row.push(values.main_category);
                                row.push(values.incentive_amount);
                                row.push(values.category);
                                row.push(values.status);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
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
                    @if (session('role_id') == 1 || in_array(97, session('permissions')))
                    {
                        text: '<i class="la la-motorcycle"></i> Add Rider',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#addRider').modal('show');

                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(383, session('permissions')))
                    {
                        text: '<i class="la la-envelope"></i> Send SMS',
                        className: 'btn btn-primary sms',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                $('#send_sms_modal').modal('show');
                                $('#selected_riders').val(selected_rows);
                                $('#n_sms').text(0);
                                $('#l_sms').text(0);
                                $('#sms_body').val('');

                            }
                        }
                    },
                    @endif
                    {
                        extend: 'excel',
                        title: 'Permanent Riders',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.sms').enable();
                                }
                            });
                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.sms').disable();
                                    }
                                }
                            });
                        }
                    },
                    'reset'],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.riders.permanent.list') }}',
                order: [[15, 'desc']],
                rowId : 'rider_id',
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'trax_id', name: 'riders.trax_id', class: 'align-middle trax_id'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'hub', name: 'c.name', class: 'align-middle hub'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle name'},
                    {data: 'phone', name: 'riders.phone', class: 'align-middle phone'},
                    {data: 'cnic', name: 'riders.cnic', class: 'align-middle cnic'},
                    {data: 'address', name: 'riders.address', class: 'align-middle address'},
                    {data: 'route', name: 'route', class: 'align-middle route'},
                    {data: 'main_category', name: 'rider_main_categories.id', class: 'align-middle text-center main_category'},
                    {data: 'incentive_amount', name: 'riders.incentive_amount', class: 'align-middle text-center incentive_amount'},
                    {data: 'category', name: 'rider_categories.id', class: 'align-middle category'},
                    {data: 'status', name: 'riders.status', class: 'align-middle status'},
                    {data: 'created_by', name: 'cb.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'updated_by', name: 'ub.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'riders.updated_at', class: 'align-middle updated_at'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.rider_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Inactive</option>' +
                        '<option value="1">Active</option>' +
                        '</select>';
                    var category_select = '<select name="category_select" id="category_select" class="select2 form-control"></select>';
                    var main_category_select = '<select name="main_category_select" id="main_category_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.category')) {
                            $(category_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.main_category')) {
                        $(main_category_select).appendTo($(search))
                            .on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $categories !!}, function (obj) {
                        obj.text = obj.name; // replace pk with your identifier

                        return obj;
                    });

            $("#category_select").prepend('<option value="" selected></option>').select2({
                data:data1,
                placeholder: "Select Sub-Category",
                width:'100%',
                containerCssClass: 'select-xs',
                dropdownCssClass: 'form-control-sm p-0'
            });

            var data = $.map({!! $main_categories !!}, function (obj) {
                obj.text = obj.name; // replace name with the property used for the text

                return obj;
            });
            $("#main_category_select").prepend('<option value="" selected></option>').select2({
                data:data,
                placeholder: "Select Main Category",
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
                $.get( "/admin/management/riders/add/1", function( data ) {
                    $("#addRiderDiv").html(data);
                    var html = '<input name="rider_type" value="1" type="hidden">';
                    $('#addRiderForm').append(html);
                });
            });
            $("#editRider").on("show.bs.modal", function(e) {

                var id = $(e.relatedTarget).data('target-id');

                $.get( "/admin/management/riders/"+id+"/edit/1", function( data ) {
                    $("#editRiderDiv").html(data);
                    var html = '<input name="rider_type" value="1" type="hidden">';
                    $('#editRiderForm').append(html);
                });

            });

            $('body').on('click', '.rejoin', function (e) {
                var id = $(this).data('target-id');
                console.log(id);
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes To Rejoin Rider!',
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
                        swal({
                            title: 'Please Wait!',
                            text: 'Rider is being Rejoin',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.management.riders.rejoin') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click','.deactivate',function (e) {
                var id = $(this).data('target-id');
                var rel = $(this).attr('rel');

                $('#rider_active_form #cid').val(id);
                $('#rider_active_form #cstatus').val(rel);
                if(rel == 'riderInactive'){
                    var atext = "Select Yes to Deactive this Rider!";
                }else{
                    var atext = "Select Yes to active this Rider!";
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
            $('body').on('click','button.incentive',function (e) {
                var rider_id = $(this).parents('tr').attr('id');

                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make this Rider as Incentive',
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
                            url: '{!! route('admin.management.riders.incentive') !!}',
                            method: 'POST',
                            data: {
                                'rider_id': rider_id,
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

            $('body').on('click','button.blacklist',function (e) {
                var rider_id = $(this).parents('tr').attr('id');

                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make this Rider as Blacklist',
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
                                'action':'block',
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
            @if (session('role_id') == 1 || in_array(383, session('permissions')))
            var char_per_sms = 250;
            $('#sms_body').on('keypress copy paste',function (e) {
                var sms = $(this).val().length;
                var no_of_sms = 0;
                no_of_sms = sms / char_per_sms;
                no_of_sms = Math.ceil(no_of_sms);
                $('#n_sms').text(no_of_sms);
                $('#l_sms').text(sms);

            });

            $('#send_sms_modal form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
            @endif

        });
    </script>

@endsection