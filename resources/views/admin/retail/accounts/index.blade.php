@extends('admin.layout.master')

@section('title', 'Retail Accounts')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Retail Accounts
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S.No.</th>
                                    <th class="border-primary border-darken-1 ">Account ID</th>
                                    <th class="border-primary border-darken-1 ">City</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Shipper Cell No.</th>
                                    <th class="border-primary border-darken-1">Shipper Address</th>
                                    <th class="border-primary border-darken-1">Added Date</th>
                                    <th class="border-primary border-darken-1">Document Status</th>
                                    <th class="border-primary border-darken-1">Actions</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="EditDetails" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditDetails"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Shipper Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_shipper_details_form" action="{{route('admin.retail.accounts.bank_info_update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="shipper_account_id" value="">
                    <div class="modal-body text-center">
                        <div class="row justify-content-center">
                            <div class="col-md-8 ">
                                <div class="col">
                                    <label for="iban" class="font-weight-bold mr-2">IBAN</label>
                                    <div class="form-group">
                                        <input type="text" name="iban" id="iban" class="form-control" placeholder="IBAN*" data-rule-required="true" data-msg-required="IBAN is required" maxlength="24">
                                        <span id="iban_error" class="danger" style="display: none;">IBAN length must be 24 characters</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="account_no" class="font-weight-bold mr-2">Account Number</label>
                                    <div class="form-group">
                                        <input type="text" name="account_no" id="account_no" class="form-control" placeholder="Shipper Account*" data-rule-required="true" data-msg-required="Account Number is required">
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="bank_info" class="font-weight-bold mr-2">Bank Name</label>

                                    <div class="form-group">
                                        <select name="bank_info" class="select2" id="bank_info" data-rule-required="true" data-msg-required="Bank Name is required">
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="col mt-1">
                                    <label for="cheque_image" class="font-weight-bold mr-2">
                                        Cheque Image:
                                    </label>
                                    <div class="form-group">
                                        <input class="form-control form-control-sm" type="file" name="cheque_image" id="cheque_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="ViewDetails" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewDetails"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Shipper Details </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body text-center">
                        <div class="row justify-content-center">
                            <div class="col-md-8 ">
                                <div class="col">
                                    <label for="iban" class="font-weight-bold mr-2">IBAN</label>
                                    <div class="form-group">
                                        <input type="text" name="view_iban" id="view_iban" class="form-control" placeholder="IBAN" disabled>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="account_no" class="font-weight-bold mr-2">Account Number</label>
                                    <div class="form-group">
                                        <input type="text" name="view_account_no" id="view_account_no" class="form-control" placeholder="Shipper Account" disabled>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="bank_info" class="font-weight-bold mr-2">Bank Name</label>
                                    <input type="text" name="view_bank_info" id="view_bank_info" class="form-control" placeholder="Bank" disabled>
                                </div>
                                <div class="col mt-1" id="image_button">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logs_modal" role="dialog" aria-labelledby="logs_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Admin Name</th>
                                <th>Changed Fields</th>
                                <th>Date of change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Log entries will be injected here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.retail.accounts.list') }}',
                        data: params,
                        success: function (result)
                        {
                            head = [];
                            head.push('S.No');
                            head.push('Account Id');
                            head.push('City');
                            head.push('Shipper');
                            head.push('Number');
                            head.push('Address');
                            head.push('Added At');
                            head.push('Document Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.city);
                                row.push(values.shipper);
                                row.push(values.number);
                                row.push(values.address);
                                row.push(values.added_at);
                                row.push(values.document_status);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                scrollX: false, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Retail Accounts',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o "></i> Excel',
                    },'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.retail.accounts.list') }}',

                },
                rowId: 'id',
                order: [[6, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'retail_shipper_infos.id', class: 'align-middle text_center id'},
                    {data: 'city', name: 'c.name', class: 'align-middle text_center city'},
                    {data: 'shipper', name: 'retail_shipper_infos.shipper_name', class: 'align-middle text_center shipper'},
                    {data: 'number', name: 'retail_shipper_infos.shipper_phone_no', class: 'align-middle text_center number'},
                    {data: 'address', name: 'retail_shipper_infos.shipper_address', class: 'align-middle text_center address'},
                    {data: 'added_at', name: 'retail_shipper_infos.created_at', class: 'text_center align-middle added_at'},
                    {data: 'document_status', name: 'retail_shipper_infos.completed_status', class: 'text_center align-middle document_status'},
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
                    // var type_select = '<select name="type_select" id="type_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
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
                    this.api().table().columns.adjust();
                }

            });

            $('#bank_info').prepend('<option value="" selected="selected"></option>').select2({
                dropdownParent: $('#edit_shipper_details_form'),
                width: '100%',
                placeholder: 'Select Bank*'
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('edit')) {
                        $.ajax({
                            url: '{!! route('admin.retail.accounts.bank_info') !!}',
                            method: 'POST',
                            data: {
                                'id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                        .done(function (data) {
                            if(data.status == 0){
                                var bank = data.details.bank_id;
                                var iban = data.details.iban;
                                var account_no = data.details.account_no;
                                $('#shipper_account_id').val(id);
                                if(bank != null){
                                    $('#bank_info').val(bank).trigger('change');
                                }
                                if(account_no != null){
                                    $('#account_no').val(data.details.account_no);
                                }if(iban != null){
                                    $('#iban').val(data.details.iban);
                                }
                                $('#EditDetails').modal('show');
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
                if ($(this).hasClass('view')) {
                        $.ajax({
                            url: '{!! route('admin.retail.accounts.bank_info') !!}',
                            method: 'POST',
                            data: {
                                'id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                        .done(function (data) {
                            if(data.status == 0){
                                $('#picture_div').remove();
                                var bank = data.details.bank_name;
                                var iban = data.details.iban;
                                var account_no = data.details.account_no;
                                var cheque_image = data.details.image;
                                $('#shipper_account_id').val(id);
                                if(bank != null){
                                    $('#view_bank_info').val(bank);
                                }
                                if(account_no != null){
                                    $('#view_account_no').val(data.details.account_no);
                                }if(iban != null){
                                    $('#view_iban').val(data.details.iban);
                                }if(cheque_image != null){
                                    $('#image_button').append(data.details.image);
                                }
                                $('#ViewDetails').modal('show');
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
                if($(this).hasClass('view_logs')) {
                    $.ajax({
                        url: "{{ route('admin.retail.view_logs') }}",
                        method: 'GET',
                        data: {
                            'id': id,
                            'screen_name' : 'Retail Accounts'
                        },
                        success: function (data) {
                            if (data.status == 0) {
                                var logs = data.logs;
                                var logContent = '';
                                if (logs.length > 0) {
                        
                                    logs.forEach(function (log) {
                                        let editedFieldsFormatted = log.data
                                            .split(', ')
                                            .map(field => {
                                                // Split the field at "->", keep the "->" and make the part after it bold
                                                let parts = field.split('->');
                                                if (parts.length > 1) {
                                                    return parts[0] + ' <strong>' + '-> ' + parts[1].trim() + '</strong>';
                                                }
                                                return field; // Return as is if "->" is not found
                                            })
                                            .join('<br>'); // Add line breaks between fields

                                        logContent += '<tr>';
                                        logContent += '<td>' + log.name + '</td>';
                                        logContent += '<td>' + editedFieldsFormatted + '</td>';
                                        logContent += '<td>' + log.created_at + '</td>';
                                        logContent += '</tr>';
                                    });

                                    $('#logs_modal table tbody').html(logContent);
                                    $('#logs_modal').modal('show');
                                } else {
                                    toastr.error('No logs found for this record.', 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            } else {
                                toastr.error(data.message, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        },
                        error: function () {
                            // Handle errors in the AJAX request
                            toastr.error('Something went wrong while retrieving logs.', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }


            });
            $('#edit_shipper_details_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var iban = $('#iban').val().replace(/\s+/g, '').toUpperCase();

                        // Check if IBAN length is not 24
                        if (iban.length !== 24) {
                            $('#iban_error').show(); // Display error message for invalid IBAN length
                            return false; // Prevent form submission
                        }
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Shipper Details!',
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
                        if(confirm){
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });

            $('#iban').inputmask({
                mask: 'R',
                repeat: 24,
                greedy: false,
                definitions: {
                    R: {
                        validator: '[a-zA-Z0-9]',
                    },
                },
            });

            $('#iban').on('input', function (e) {
                var iban = $(this).val().replace(/\s+/g, '').toUpperCase(); // Remove white spaces and convert to uppercase
                var defaultPrefix = 'PK';

                
                if (!iban.startsWith(defaultPrefix)) {
                    iban = defaultPrefix + iban.substring(defaultPrefix.length);
                }
                
                if (iban.length > 2 && !iban.startsWith(defaultPrefix)) {
                    $(this).val(defaultPrefix + iban.substring(defaultPrefix.length));
                } else {
                    $(this).val(iban);
                }

                if (iban.length < 24) {
                    $('#iban_error').show();
                    event.preventDefault();
                } else {
                    $('#iban_error').hide();
                }

            });
            
        });
        </script>
@endsection
