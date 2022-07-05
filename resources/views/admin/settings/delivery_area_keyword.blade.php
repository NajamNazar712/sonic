@extends('admin.layout.master')

@section('title', 'Delivery Area Keyword')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Delivery Area Keyword
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Area Name</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Added By</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
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

    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {



            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.delivery_area_keyword.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Area Name');
                            head.push('City');
                            head.push('Added By');
                            head.push('Updated By');
                            head.push('Updated At');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.area_name);
                                row.push(values.city_name);
                                row.push(values.added_by);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                                row.push(values.status);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(748, session('permissions')))
                    
                    {
                        text: '<i class="la la-plus"></i> Add Area',
                        className: 'btn btn-primary add_area',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            var redirect = '{!! url('/admin') !!}';
                            
                            var url = redirect + '/settings/delivery_area_keyword/add';

                            window.location = url;
                            
                        }
                    },
                    @endif
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
                ajax: '{{ route('admin.settings.delivery_area_keyword.list') }}',
                rowId: 'id',
                order: [[0, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'area_name', name: 'delivery_location_mappings.name', class: 'align-middle area_name'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'added_by', name: 'ab.name', class: 'align-middle added_by'},
                    {data: 'updated_by', name: 'ub.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'delivery_location_mappings.updated_at', class: 'align-middle updated_at'},
                    {data: 'status', name: 'delivery_location_mappings.status', class: 'align-middle status'},
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
                    var status = '<select name="status" id="status" class="select2 form-control">' +
                        '<option value="1">Enable</option>' +
                        '<option value="0">Disable</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
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
                    this.api().table().columns.adjust();
                }
            });

           
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable_disable', function() {
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
                            url:'{!! route("admin.settings.delivery_area_keyword.enable_disable") !!}',
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.delete', function() {

            });

            
            $( "#add_admin" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
                });

        });
    </script>
@endsection