@extends('admin.layout.master')

@section('title', 'Star Shippers')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Star Shippers
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Sales Person</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Territory</th>
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
        <div class="modal fade text-left" id="AddStarShipper" data-backdrop="static" tabindex="-1" role="dialog"
             aria-labelledby="AddStarShipper"
             aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">Add Star Shippers</h4>
                    </div>
                    <form method="post" id="star_shipper_add" action="{{route('admin.settings.star_shippers.add')}}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group" id="select_shipper">
                                <select name="star_shipper_id" id="star_shipper_id" class="form-control select2"
                                        data-rule-required="true" data-msg-required="City is required">--}}
                                    @foreach($shippers as $shipper)
                                        <option value="{{ $shipper->id }}"> {{ $shipper->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="assign_star_shipper">Add</button>
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function () {

            $('#AddStarShipper').on('hidden.bs.modal', function () {
                $('#star_shipper_id').val('').trigger('change.select2');
            });

            var star_shipper_obj = [];
            star_shipper_obj.length = 0;

            $('#star_shipper_id').prepend('<option selected></option>').select2({
                width: '100%',
                placeholder: "Select Shipper",
                allowClear: true,
                dropdownParent:$('#star_shipper_add'),
                data: star_shipper_obj
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        {{--@if (session('role_id') == 1 || in_array(698, session('permissions')))--}}
                    {
                        text: '<i class="la la-plus"></i> Add',
                        className: 'btn btn-primary tag_agents',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddStarShipper').modal('show');

                        }
                    },
                    {{--@endif--}}
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
                ajax: '{{ route('admin.settings.auto_tag_territories.list') }}',
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'agent_name', name: 'ad.name', class: 'align-middle agent_name'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'territory_name', name: 't.name', class: 'align-middle territory_name'},
                    {data: 'status', name: 'auto_tag_territories.status', class: 'align-middle status'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center align-middle action p-1',
                        orderable: false,
                        searchable: false
                    }
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
                    var departments_select = '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';

                    var status = '<select name="status" id="status" class="select2 form-control">';
                    status += '<option value="1">Enable</option>';
                    status += '<option value="0">Disable</option>';
                    status += '</select>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.department')) {
                            $(departments_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });


                    $('#status').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('#star_shipper_add').on('submit', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                }).done(function (res) {

                    if (res.status === '1')
                    {
                        toastr.success(res.success, 'Success!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    else
                    {
                        toastr.error(res.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    table.draw();
                });

                $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.delete', function () {
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
                    }).then(function (confirm) {
                        $.ajax({
                            url: '{!! route("admin.settings.auto_tagging.delete") !!}',
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
                $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable_disable', function () {
                    var id = parseInt($(this).parents('tr').attr('id'));
                    $.ajax({
                        url: '{!! route("admin.settings.auto_tag_territories.enable_disable") !!}',
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
        });
    </script>
@endsection