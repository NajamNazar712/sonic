@extends('admin.layout.master')

@section('title', 'Riders Incentive Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Riders Incentive Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Courier Type</th>
                                    <th class="border-primary border-darken-1">COD</th>
                                    <th class="border-primary border-darken-1">Shipment Weight</th>
                                    <th class="border-primary border-darken-1">Incentive/Shipment</th>
                                    <th class="border-primary border-darken-1">Added By</th>
                                    <th class="border-primary border-darken-1">Added At</th>
                                    <th class="border-primary border-darken-1">Updated by</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
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

    <div class="modal fade text-left" id="AddTierModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddTierModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Tier</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_commission_form" action="{{route('admin.settings.commission.add')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row justify-content-center">
                                <div class="col-6 form-group">
                                    <input  class="form-control" id="tier_name" name="tier_name" type="text" placeholder="Enter Tier Name"
                                            data-rule-required="true" data-msg-required="" />
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-6 form-group">
                                    <input  class="form-control" id="tier_commission" name="tier_commission"
                                            data-rule-required="true" data-msg-required="" placeholder="Enter Overall Commission" />
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-6 form-group">
                                    <fieldset class="form-group">
                                        <select name="tier_type" id="tier_type" class="form-control select2" data-rule-required="true" data-msg-required="">
                                            @foreach($rider_categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <label class="font-medium-2 font-weight-bold block">Relates to Sales Person</label>
                                    <div class="form-group">
                                        <label for="sales_person_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
                                        <input type="checkbox" name="sales_person_checkbox" id="sales_person_checkbox" class=" sales_person_checkbox" data-size="sm" data-switchery="true">
                                        <label for="sales_person_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button id="AddnewTier" type="submit" class="btn btn-primary btn-block">Add Tier</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade text-left" id="EditTierModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditTierModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Tier</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_commission_form" action="{{route('admin.settings.commission.edit')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row justify-content-center">
                                <div class="col-6" id="TierNameDiv">
                                    <input type="hidden" id="sales_tier_id" name="id">
                                    <input  class="form-control" id="edit_tier_name" name="tier_name" type="text" placeholder="Enter Tier Name"
                                            data-rule-required="true" data-msg-required="" />
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-6" id="TierCommissionDiv">
                                    <input  class="form-control" id="edit_tier_commission" name="tier_commission" type="text"
                                            data-rule-required="true" data-msg-required="" placeholder="Enter Overall Commission" />
                                </div>
                            </div>
                            <br>


                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <label class="font-medium-2 font-weight-bold block">Relates to Sales Person</label>
                                    <div class="form-group">
                                        <label for="sales_person_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>

                                        <input type="checkbox" name="sales_person_checkbox" id="edit_sales_person_checkbox" data-switchery="false" class="sales_person_checkbox" data-size="sm">
                                        <label for="sales_person_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                    </div>
                                </div>
                            </div>

                            <br><br>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button id="AddnewTier" type="submit" class="btn btn-primary btn-block">Edit Tier</button>
                                </div>
                            </div>

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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#tier_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Enter Tier Type*',
                dropdownParent:$('#add_commission_form')
            });

            $( "#add_commission_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'New Tier is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });



            $( "#edit_commission_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Responsible is being Edited!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });


            $('body').on('click','button.edit',function () {
                var id = $(this).parents('tr').attr('id');
                var type = table.row($(this).parents('tr')).data().type_id;
                var status = table.row($(this).parents('tr')).data().sales_status;

                $.ajax({
                    url: '{!! route('admin.settings.commission.details') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        $('#sales_tier_id').val(data.salesTiers.id);
                        $('#edit_tier_name').val(data.salesTiers.tier_name);
                        $('#edit_tier_commission').val(data.salesTiers.commission);

                        if (status == 1)
                        {
                            $('#edit_sales_person_checkbox').trigger('click');
                        }
                        $("#edit_tier_type").select2({
                            width:'100%',
                            class:'form-control',
                            dropdownParent:$('#edit_commission_form')
                        });

                        $('#edit_tier_type').val(type).trigger('change');

                        $('#EditTierModal').modal('show');

                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });

            });


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: '<i class="la la-cogs"></i> Add',
                    className: 'btn btn-primary add',
                    action: function (e, dt, node, config) {
                        $('#AddTierModal').modal('show');
                    }
                }, ,{
                    extend: 'excel',
                    title: 'Sales Tier',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.settings.hr.rider_incentive.list') }}',
                rowId: 'row_id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'rate_category', name: 'rc.name', class: 'align-middle rate_category'},
                    {data: 'payment_type', name: 'rspt.name', class: 'align-middle payment_type'},
                    {data: 'weight_range', name: 'rswr.name', class: 'align-middle weight_range'},
                    {data: 'value', name: 'riders_incentive_settings.value', class: 'align-middle value'},
                    {data: 'added_by', name: 'ab.name', class: 'align-middle added_by'},
                    {data: 'added_at', name: 'riders_incentive_settings.created_at', class: 'align-middle added_at'},
                    {data: 'last_updated_by', name: 'ub.name', class: 'align-middle last_updated_by'},
                    {data: 'updated_at', name: 'riders_incentive_settings.updated_at', class: 'align-middle updated_at'},
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
                    /*var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disable</option>' +
                        '<option value="1">Enable</option>' +
                        '</select>';*/
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        /*else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }*/
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    /*$("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });*/
                    this.api().table().columns.adjust();
                }
            });


            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if ($(this).hasClass('enable')) {
                    $.ajax({
                        url: '{!! route('admin.settings.commission.status') !!}',
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
                        url: '{!! route('admin.settings.commission.status') !!}',
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
            $('#EditTierModal').on('hidden.bs.modal', function() {
                $('#sales_tier_id').val('');
                $('#edit_tier_name').val('');
                $('#edit_tier_commission').val('');
                if($("#edit_sales_person_checkbox").is(":checked")){
                    $("#edit_sales_person_checkbox").trigger('click');
                }
            });
        });


    </script>
@endsection