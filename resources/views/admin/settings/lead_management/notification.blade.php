@extends('admin.layout.master')

@section('title', 'Leads Notification')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Leads Notification 
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Type</th>
                                    <th class="border-primary border-darken-1">Updated at</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <div class="modal fade text-left" id="EditNotificationModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit Notification</h4>
                </div>
                <form method="post" id="notification_edit" action="{{route('admin.settings.lead_notification.update')}}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf

                <div class="modal-body">
                    <input type="hidden" name="selected_ids" id="selected_ids"/>
                    <input type="hidden" name="lead_notification_id" id="lead_notification_id">
                    <div class="modal-body">
                        <div class="form-group email">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control subject" placeholder="Subject*" data-rule-required="true" data-msg-required="Subject is required" data-rule-field="true">
                        </div>

                        <div class="form-group">
                            <label>Body</label>
                            <textarea type="text" name="body" class="form-control body" placeholder="Body*" data-rule-required="true" data-msg-required="Body is required" data-rule-field="true"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Fields</label>
                            <div class="fields">
                            </div>
                        </div>
                        
                        <div class="form-group d-none" id="attachment">
                            <label>Attachments</label>
                            <div class="attachments">
                                <table class="table table-bordered" id="image_view_table" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
            
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Date Added</th>
                                        <th class="border-primary border-darken-1">Attachment</th>
                                        <th class="border-primary border-darken-1">Remove</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <table class="table table-bordered datatable" id="image_upload_table" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
        
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Attachment</th>
                                        <th class="border-primary border-darken-1"></th>
        
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="edit_agentSubmit">Update</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
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


@endsection

@section('js')
<script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            var selected_rows = [];

            var notification_image_table;

            var valid_fields = [];
				autosize($('#notification_edit .body')[0]);


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    'reset'
                    ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: '{{ route('admin.settings.lead_notification.list') }}',
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'name', name: 'lead_notifications.name', class: 'align-middle name'},
					{data: 'type', name: 'lead_notifications.type_id', class: 'align-middle type'},
					{data: 'updated_at', name: 'lead_notifications.updated_at', class: 'align-middle updated_at'},
					{data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data: 'status', name: 'lead_notifications.status', class: 'align-middle status'},
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
                    var departments_select = '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';
                    
                    var status = '<select name="status" id="status" class="select2 form-control">';
                        status +='<option value="1">Enable</option>';
                        status +='<option value="0">Disable</option>';
                        status +='</select>';

                    var type_select = '<select name="status" id="type_select" class="select2 form-control">';
                        type_select +='<option value="1">Email</option>';
                        type_select +='<option value="2">SMS</option>';
                        type_select +='</select>';
                   
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.department')){
                            $(departments_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }else if ($(header).is('.type')) {
                            $(type_select).appendTo($(search))
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
                    

                    $('#status').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#type_select').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            
            
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var notification_type = parseInt($(this).parents('tr').attr('data-type'));

                $.ajax({
                    url:'{!! route("admin.settings.lead_notification.data") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    console.log(data);
							$('#EditNotificationModal #lead_notification_id').val(id);

                    // $('#edit_zone_id').val(data.zone_id);
                    // $('#lead_tagging_id').val(data.lead_tagging_id).change();
                    if (notification_type == 1) {
								$('#EditNotificationModal .email').removeClass('d-none');

								$('#EditNotificationModal .subject').val(data.subject);
							}
							else {
								$('#EditNotificationModal .email').addClass('d-none');

								$('#EditNotificationModal .subject').val('');
							}

							$('#EditNotificationModal .body').val(data.body);

							$('#EditNotificationModal .fields').html('');

							valid_fields = [];

							$.each(data.fields, function(index, field) {
								$('#EditNotificationModal .fields').append('<span class="d-inline-block mb-1 mr-1 bg-info text-highlight white">[' + field + ']</span>');
                                console.log(field);
								valid_fields.push(field);
							});
                            if(id == 1){
                                $('#attachment').removeClass('d-none');
                                var images_count = 0;
                                var rows_count = 0;
                                var image_html = '';
                                
                                $.each(data.attachments, function (index, attachment) {
                                    index++;
                                    var img = '<a class="btn btn-sm btn-outline-info align-middle" href="' + attachment.image + '" target="_blank"><i class="la la-lg la-file align-middle"></i> <span class="align-middle">View</span></a>';
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                                    image_html += '<tr id="' + attachment.id + '"><td>' + index + '</td><td>' + attachment.date + '</td><td>' + img + '</td><td>' + remove + '</td></tr>';
                                });
                                $('#image_view_table tbody').append(image_html);
                                
                                function add_row() {
                                    var tr_id = $('#image_upload_table tbody tr').attr('id');
                                    if (typeof tr_id !== typeof undefined && tr_id !== false) {
                                        var new_img_rows = $('#image_upload_table tbody tr').length;
                                        new_img_rows = images_count + new_img_rows;
                                        if(new_img_rows >= 3){
                                            $('#image_upload_table .img_add_btn').attr('disabled', true);
                                            return false;
                                        }
                                    }

                                    rows_count++;

                                    var crm_image = '<input class="form-control form-control-sm" type="file" name="notification_image_'+rows_count+'" data-rule-extension="jpeg|jpg|png|pdf|doc" data-msg-extension="Only file with extension jpeg, jpg, png, doc or pdf allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Attachment is required">';
                                    if(rows_count == 1){
                                        var remove = '';
                                    }else{
                                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                                    }
                                    notification_image_table.row.add([0, crm_image,remove]).node().id = rows_count;
                                    notification_image_table.draw(true);
                                    $('#CRMImageSubmitButton').attr('disabled', false);
                                    selected_rows.push(rows_count);
                                }
                                notification_image_table = $('#image_upload_table').DataTable({
                                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                                    buttons:[{
                                        title: 'Add Row',
                                        className: 'btn btn-primary img_add_btn',
                                        text: '<i class="la la-plus"></i> Add Row',
                                        action:function (e) {
                                            if(images_count < 2){
                                                add_row();
                                            }
                                        }
                                    }],
                                    ordering:false,
                                    paging:false,
                                    columns: [
                                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                        {name: 'image', class: 'align-middle image form-group'},
                                        {name: 'action', class: 'align-middle action'},
                                    ],

                                    rowCallback: function(row, data, index) {
                                        var info = notification_image_table.page.info();

                                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                    },
                                    initComplete: function() {

                                        // this.api().table().columns.adjust();
                                    }
                                });
                                $('#image_view_table').on('click','a.remove_row', function () {
                                    var row_id = $(this).parents('tr').attr('id');
                                    var notification_id = $('#lead_notification_id').val();
                                    var current = $(this);
                                    if(row_id){
                                        swal({
                                            title: 'Are You Sure?',
                                            text: 'Select Yes if you want to delete this attachment!',
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
                                                $.ajax({
                                                    url: '{!! route('admin.settings.lead_notification.delete_image') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        'image_id': row_id,
                                                        'notification_id':notification_id,
                                                        '_token': '{{ csrf_token() }}'
                                                    }
                                                }).done(function (data) {
                                                    console.log(data);
                                                    if(data.status == 0){
                                                        images_count = images_count - 1;
                                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                                        current.parents('tr').remove();
                                                    }else{
                                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });

                                $('body').on('click', 'a.remove_row',function () {
                                    var rid = parseInt($(this).parents('tr').attr('id'));
                                    var index = $.inArray(rid, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }
                                    notification_image_table.row( $(this).parents('tr') ).remove().draw();
                                });
                            }else{
                                $('#attachment').addClass('d-none');

                            }
                    $('#EditNotificationModal').modal('show');
                    

                    });
                
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.delete', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                swal({
                                text: 'Are you sure, you want to Delete?',
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
                            }).then(function(confirm) {
                                         $.ajax({
                                            url:'{!! route("admin.settings.auto_tagging.delete") !!}',
                                            method: 'POST',
                                            data: {
                                                'id': id,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                            table.draw();
                                        });
                            });

                
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable_disable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                                         $.ajax({
                                            url:'{!! route("admin.settings.lead_notification.enable_disable") !!}',
                                            method: 'POST',
                                            data: {
                                                'id': id,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                            table.draw();
                                        });

                
            });

            

                $('#notification_edit').on('shown.bs.modal', function (e) {
				    autosize.update($('#notification_edit .body')[0]);
                })

                $.validator.addMethod('field', function(value, element) {
                    var valid = true;
                    var entered_fields = value.match(/[^[\]]+(?=])/g);
                    $.each(entered_fields, function(index, field) {
                        if ($.inArray(field, valid_fields) === -1) {
                            valid = false;
                            return valid;
                        }
                    });
                    return valid;
                }, 'One or more invalid Field(s) entered');

                
                $( "#notification_edit" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#selected_ids').val(selected_rows);

                    form.submit();    
                }
                
                });

                $('#EditNotificationModal').on('hidden.bs.modal', function () {
                    $('#lead_notification_id').val('');
                    notification_image_table.clear();
                    notification_image_table.draw();
                    selected_rows = [];
                    rows_count = 0;
                //     $("#image_upload_table").dataTable().clear();
                // $("#image_upload_table").dataTable().draw();
                // $('#image_upload_table tbody').html('');
                $("#image_upload_table").dataTable().fnDestroy();
                    $('#image_view_table tbody').html('');
                });
        });
    </script>
@endsection