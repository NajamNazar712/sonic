@extends('admin.layout.master')

@section('title', 'Shipment Receive Details')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Shipement Received Details
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">

                            <form id="payment_form" class="form-horizontal" method="POST" action="{{ route('admin.management.shipment_received.excel_upload') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="receiver_detials" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/shipment receive details.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">Tracking No. </th>
                                    <th class="border-primary border-darken-1">Receiver Name</th>
                                    <th class="border-primary border-darken-1">Receiver Cnic</th>
                                    <th class="border-primary border-darken-1">Receiver Relationship</th>
                                    <th class="border-primary border-darken-1">Created at</th>
                                    <th class="border-primary border-darken-1">Created By</th>
                                    <!-- <th class="border-primary border-darken-1"></th> -->
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- modal start --}}


<div class="modal fade" id="invalid_shipments" role="dialog" aria-labelledby="delivered_shipments_title" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="delivered_shipments_title">Invalide Shipments</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-centered">
                    <thead>
                        <tr>
                            <td>Tracking ID</td>
                        </tr>
                    </thead>
                    <tbody id="tablebody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- model end --}}

@endsection



@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')


@if (Session::has('error'))
@php
 $invalid_shipment = session('invalid_shipment');
@endphp
<script>
var invalidShipment = @json($invalid_shipment);
for(let x of invalidShipment){
    $("#tablebody").append(`
        <tr>
            <td>${x[0]}</td>
        </tr>
    `);
}
$('#invalid_shipments').modal('show');
</script>    
@endif
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

    

    


        $(document).ready(function() {
            $('#rider_category_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Category*',
                dropdownParent:$('#add_incentive_form')
            });

            $('#delivery_payment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Payment Type*',
                dropdownParent:$('#add_incentive_form')
            });

            $('#weight_range_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Weight Range*',
                dropdownParent:$('#add_incentive_form')
            });

            $( "#add_incentive_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'New Setting is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('input.incentive_value').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $( "#edit_incentive_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Setting is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#edit_rider_category_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Category*',
                dropdownParent:$('#edit_incentive_form')
            });

            $('#edit_delivery_payment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Payment Type*',
                dropdownParent:$('#edit_incentive_form')
            });

            $('#edit_weight_range_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Weight Range*',
                dropdownParent:$('#edit_incentive_form')
            });

            $('body').on('click','button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id) {
                    $.ajax({
                        url: '""',
                        data: {
                            'id': id,
                        }
                    }).done(function (data) {
                        if (data.status === 0) {
                            $('#incentive_setting_id').val(id);
                            $('#edit_rider_category_select').val(data.details.rider_category_id).trigger('change');
                            $('#edit_delivery_payment_select').val(data.details.rider_shipment_payment_type_id).trigger('change');
                            $('#edit_weight_range_select').val(data.details.rider_shipment_weight_range_id).trigger('change');
                            $('#edit_incentive_form input.incentive_value').val(data.details.value);
                            $('#EditIncentiveModal').modal('show');

                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }


                    });
                }

            });


            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ '' }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Fintech Company Name');
                            head.push('Status');
                            head.push('Added by');
                            head.push('Added at');
                            head.push('Updated by');
                            head.push('Updated at');
                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.company_name);
                                row.push(values.status);
                                row.push(values.admin1);
                                row.push(values.created_at);
                                row.push(values.admin2);
                                row.push(values.updated_at);
                 
                             
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
                    title: 'Fintech Companies List',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{'' }}',
                rowId: 'id',
                order: [[6, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'company_name', name: 'company_name', class: 'align-middle company_name'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'admin1', name: 'admin1', class: 'align-middle admin'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'admin2', name: 'admin2', class: 'align-middle admin'},
                    {data: 'updated_at', name: 'updated_at', class: 'align-middle created_at'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                    var status_select = '<select name="status_select" id="status_select" style="height:30px; width:150px;" class="select2 form-control">' +
                        '<option value="1">Enable</option>' +
                        '<option value="0">Disable</option>' +
                        '</select>';
      
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
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
                        placeholder: "Select Category",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });


            
            
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if ($(this).hasClass('enable')) {
                    $.ajax({
                        url: '{{''}}',
                        method: 'POST',
                        data: {
                            'id': id,
                            'status': 1,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                table.draw(false);

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
                else if ($(this).hasClass('disable')) {
                    $.ajax({
                        url: '{{''}}',
                        method: 'POST',
                        data: {
                            'id': id,
                            'status': 0,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                table.draw(false);

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }

            });
            $('#EditIncentiveModal').on('hidden.bs.modal', function() {
                $('#incentive_setting_id').val('');
                $('#edit_rider_category_select').val('').trigger('change');
                $('#edit_delivery_payment_select').val('').trigger('change');
                $('#edit_weight_range_select').val('').trigger('change');
                $('#edit_incentive_form input.incentive_value').val('');
            });

            $('#datatable tbody').on('click', 'tr td.action a.status', function() {
                var fintechCharges = $("#user_fintech_charges_txtbox").val();
                var userID = $("#userID").val();
                var id = $(this).parents('tr').attr('id');
             
                $.ajax({
                    type : 'GET',
                    url  : "{{''}}",
                    data : {id:id},
                    success:function(res){
                        if(res.status == '200'){
                            toastr.success(res.message, 'Success!', {
                            positionClass: 'toast-bottom-center',
                            containerId: 'toast-bottom-center',
                        });
                        $("#FintechCompanyStatus").modal('hide');
                        
                        table.draw(true);
                        }
                        else{
                            toastr.error(res.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        
                        }
                    }
                });
            });
        });


    </script>
@endsection