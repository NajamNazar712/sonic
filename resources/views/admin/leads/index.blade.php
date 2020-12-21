@extends('admin.layout.master')

@section('title', 'Leads Management')

@section('content')
    <h1 class="mb-1">
        Leads Management
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Lead ID</th>
                        <th class="border-primary border-darken-1">Contact Person</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Phone No</th>
                        <th class="border-primary border-darken-1">Email Address</th>
                        <th class="border-primary border-darken-1">Message</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">Sale Person Tagged</th>
                        <th class="border-primary border-darken-1">Reference Person</th>
                        <th class="border-primary border-darken-1">Lead Status</th>
                        <th class="border-primary border-darken-1">Aging</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="ForwardLeadModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ForwardLeadModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Forward Lead</h4>
                </div>
                <div class="modal-body">
                    <div class="col mb-1">
                        <select name="sale_person" id="saletag1" class="form-control select2">
                            @foreach($sale_name as $sn)
                                <option value="{{ $sn->id }}"> {{ $sn->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select name="reference_person" id="reference_person" class="form-control select2">
                            @foreach($sale_name as $sn)
                                <option value="{{ $sn->id }}"> {{ $sn->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="ForwardLeadSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="DetailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_remarks_modal" role="dialog" aria-labelledby="add_remarks_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_remarks_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            <input type="text" name="add_remarks" id="add_remarks" class="form-control add_remarks" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required">

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add Remarks</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="add_status_modal" role="dialog" aria-labelledby="add_status_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Update Status</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_status_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            <select name="update_lead_status" id="update_lead_status" class="form-control select2">
                                @foreach($lead_statuses as $lead_status)
                                    <option value="{{ $lead_status->id }}" > {{ $lead_status->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Update</button>
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
    <style type="text/css">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.requests.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Lead ID');
                            head.push('Contact Person');
                            head.push('City');
                            head.push('Phone No');
                            head.push('Email Address');
                            head.push('Requested Date/Time');
                            head.push('Sale Person Tagged');
                            head.push('Lead Status');
                            head.push('Aging');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.created_at);
                                row.push(values.city);
                                row.push(values.total_quantity);
                                row.push(values.amount);
                                row.push(values.address);
                                row.push(values.mode);
                                row.push(values.status);
                                row.push(values.remarks);
                                row.push(values.requested_by);
                                row.push(values.aging);
                                row.push(values.confirmed_aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                        @if (session('role_id') == 1 || in_array(361, session('permissions')))
                    {
                        text: 'Bulk Tagging',
                        className: 'btn btn-primary bulk_tagging',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                $('#SalesTagModal').modal('show');
                            }else{
                                var error = "Account Not selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                        @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_tagging').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_tagging').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Lead Management',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.leads.list') }}',
                rowId: 'lead_id',
                order: [[9, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'lead_id', name: 'leads.id', class: 'align-middle lead_id'},
                    {data: 'contact_person', name: 'leads.contact_person', class: 'align-middle contact_person'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'phone_number', name: 'leads.phone_number', class: 'align-middle phone_number'},
                    {data: 'email_address', name: 'leads.email_address', class: 'align-middle email_address'},
                    {data: 'message', name: 'leads.message', class: 'align-middle message'},
                    {data: 'requested_date', name: 'leads.requested_date', class: 'align-middle requested_date'},
                    {data: 'sale_person', name:'sp.name', class: 'align-middle sale_person'},
                    {data: 'reference_person', name:'rp.name', class: 'align-middle sale_person'},
                    {data: 'status', name: 'ls.id', class: 'align-middle status'},
                    {data: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                    {data: 'updated_by', name: 'ub.name', class: 'align-middle updated_by'},
                    {data: 'action', name: 'action', class: 'align-middle action',orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.aging')) {
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
                    var data1 = $.map({!! $statuses !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $statuses !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                console.log(id);
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_tagging').enable();

                }
                else {
                    table.button('.bulk_tagging').disable();
                }
            });

            $("#saletag").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Person",
                width:'100%',
                dropdownParent:$('#SalesTagModal')
            });
            $("#saletag1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Person",
                width:'100%',
                dropdownParent:$('#ForwardLeadModal')
            });
            $("#reference_person").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reference Person",
                width:'100%',
                dropdownParent:$('#ForwardLeadModal')
            });
            $('#salesTagSubmit').on('click',function () {
                var assign = parseInt($('#saletag').val());
                swal({
                    text: 'Are you sure, you want to Tag?',
                    icon: 'info',
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
                }).then(function(confirm) {
                    if (confirm) {
                        if (assign) {
                            $.ajax({
                                url: '{!! route('admin.leads.tag_sale_person') !!}',
                                method: 'POST',
                                data: {
                                    'sale_person': assign,
                                    'lead_ids[]': selected_rows,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function (data) {
                                    if (data.status == 1) {
                                        $('#SalesTagModal').modal('hide');
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
                                    selected_rows = [];

                                    table.rows().deselect();
                                    $('#saletag').val('').trigger('change');
                                    $('#SalesTagModal').modal('hide');
                                    table.draw(true);
                                    table.button('.bulk_tagging').disable();

                                });
                        } else {
                            var error = "Lead Not Selected!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                });
            });
            var status_lead_id = null;
            var remark_lead_id = null;
            var forward_lead_id = null;

            $('body').on('click','#datatable .forward_lead',function(){
                forward_lead_id = parseInt($(this).parents('tr').attr('id'));
                $('#ForwardLeadModal').modal('show');
            });
            $('#ForwardLeadSubmit').on('click',function () {
                var tag = parseInt($('#saletag1').val());
                var refer_person = parseInt($('#reference_person').val());
                if(tag && refer_person){
                    $.ajax({
                        url: '{!! route('admin.leads.tag_sale_person') !!}',
                        method: 'POST',
                        data: {
                            'sale_person': tag,
                            'reference_person': refer_person,
                            'lead_ids[]': forward_lead_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if(data.status){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#saletag1').val('').trigger('change');
                            $('#reference_person').val('').trigger('change');
                            $('#ForwardLeadModal').modal('hide');
                            forward_lead_id = null;
                            table.draw(true);
                        });
                }else{
                    if(!tag){
                        var error = "Sales Person Not Selected!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!refer_person){
                        var error = "Reference Person Not Selected!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }

            });

            $('body').on('click','#datatable .lead_log',function(){
                var lead_id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.leads.lead_log') !!}',
                    method: 'POST',
                    data: {
                        'lead_id': lead_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        var html = '<div class="col">';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead><tr><th>S No.</th><th><strong>Lead ID</strong></th><th><strong>Contact Person</strong></th><th><strong>Phone Number</strong></th><th><strong>Sales Person</strong></th><th><strong>Reference Person</strong></th><th><strong>Lead Status</strong></th><th><strong>Updated By</strong></th><th><strong>Updated At</strong></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.leads, function(index, value) {
                            var ind = index+1;
                            html += '<tr class=""><td>' + ind + '</td>';
                            html += '<td>' + value.lead_id + '</td>';
                            html += '<td>' + value.contact_person + '</td>';
                            html += '<td>' + value.phone_number + '</td>';
                            html += '<td>' + value.sales_person + '</td>';
                            html += '<td>' + value.reference_person + '</td>';
                            html += '<td>' + value.status + '</td>';
                            html += '<td>' + value.updated_by + '</td>';
                            html += '<td>' + value.updated_at + '</td></tr>';
                        });
                        html += '</tbody></table></div>';
                        var header = '<h4 class="modal-title" id="">Lead Logs</h4>';
                        $('#DetailsModal .header').html(header);
                        $('#DetailsModal .modal-body').html(html);
                        $('#DetailsModal').modal('show');
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });

            $('body').on('click','#datatable .view_remarks',function(){
                var lead_id = parseInt($(this).parents('tr').attr('id'));
                var link = '{{ route('admin.leads.view_remarks', ["id" => 0]) }}';

                window.location = link.substr(0, link.lastIndexOf('/')) + '/' + lead_id;
            });

            $("#update_lead_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Status",
                width:'100%',
                dropdownParent:$('#add_status_modal')
            });

            $('body').on('click','#datatable .update',function(){
                status_lead_id = parseInt($(this).parents('tr').attr('id'));
                $('#add_status_modal').modal('show');
            });

            $('#add_status_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    var new_status = $('#update_lead_status').val();
                    if(new_status){
                        blockPagePermanently();
                        $.ajax({
                            url:"{{route('admin.leads.add_status')}}",
                            method:'POST',
                            data:{
                                'lead_id':status_lead_id,
                                'status':new_status,
                                '_token':'{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            UnblockPagePermanently();
                            $('#add_status_modal').modal('hide');
                            new_status = null;
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            table.draw();
                        });
                    }
                    else{
                        var error = 'Status not Selected!';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });

            $('#add_status_modal').on('hide.bs.modal', function () {
                $('#update_lead_status').val('').trigger('change');
            });

            $('body').on('click','#datatable .add_remarks',function(){
                remark_lead_id = parseInt($(this).parents('tr').attr('id'));
                $('#add_remarks_modal').modal('show');
            });

            $('#add_remarks_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    var remarks = $('#add_remarks').val();
                    if(remark_lead_id != null){
                        blockPagePermanently();
                        $.ajax({
                            url:"{{route('admin.leads.add_remarks')}}",
                            method:'POST',
                            data:{
                                'lead_id':remark_lead_id,
                                'remarks':remarks,
                                '_token':'{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            UnblockPagePermanently();
                            $('#add_remarks_modal').modal('hide');
                            remark_lead_id = null;
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        });
                    }
                    else{
                        var error = 'Invalid Lead ID!';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });

            $('#add_remarks_modal').on('hide.bs.modal', function () {
                $('#add_remarks_form input.add_remarks').val('');
            });
        });

    </script>
@endsection