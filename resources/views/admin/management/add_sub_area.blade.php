@extends('admin.layout.master')

@section('title', 'City Sub Area')

@section('content')
    <h1>City Sub Area</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @if (session('role_id') == 1 || count(array_intersect([276, 321], session('permissions'))) !== 0)

                        <form id="add_city_form" class="row p-1 mb-2" method="post" onsubmit="event.preventDefault()">
                            @csrf
                            <div class="col-3">
                                <fieldset class="form-group">
                                    <select name="city_id" id="city_id" class="form-control select2"  required data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-3">
                                <fieldset class="form-group">
                                    <input type="text" required name="name" id="name" class="form-control name" placeholder="Name">
                                </fieldset>
                            </div>

                            <div class="col-3">

                                <fieldset class="form-group">
                                    <select name="report_location_id" id="report_location_id" class="form-control select2" required  data-rule-required="true" data-msg-required="This field is required">
                                        <option value="">Select Reporting Location</option>
                                        @foreach($reporting_locations as $rl)
                                            <option value="{{$rl->id}}">{{$rl->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>


                            <div class="col-2">
                                <button type="submit" id="add_city" class="mr-1 mb-1 btn btn-primary btn-min-width">Add</button>
                            </div>
                        </form>
                    @endif
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="width: 100%;z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1" >S No.</th>
                                    <th class="border-primary border-darken-1" >Area Name</th>
                                    <th class="border-primary border-darken-1" >Hub</th>
                                    <th class="border-primary border-darken-1" >Reporting Location</th>
                                    <th class="border-primary border-darken-1" >Map</th>
                                    <th class="border-primary border-darken-1" >Status</th>
                                    <th class="border-primary border-darken-1" >Updated By</th>
                                    <th class="border-primary border-darken-1" >Created At</th>
                                    <th class="border-primary border-darken-1" >Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="city_area_edit_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal1"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="edit_city_form" class="row p-1 mb-2" method="post" onsubmit="event.preventDefault()" style="display: contents">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit City Area</h4>
                </div>
                <div class="modal-body">
                        <div class="row">

                        @csrf
                        <input type="hidden" id="city_area_id" name="id">
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="city_id" id="city_id_edit" class="form-control select2"  required data-rule-required="true" data-msg-required="This field is required">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-4">
                            <fieldset class="form-group">
                                <input type="text" required name="name" id="name_edit" class="form-control name" placeholder="Name">
                            </fieldset>
                        </div>

                        <div class="col-4">

                            <fieldset class="form-group">
                                <select name="report_location_id" id="report_location_id_edit" class="form-control select2" required  data-rule-required="true" data-msg-required="This field is required">
                                    <option value="">Select Reporting Location</option>
                                    @foreach($reporting_locations as $rl)
                                        <option value="{{$rl->id}}">{{$rl->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" >Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/checkbox-radio.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#city_id').select2({
                width:'100%',
                placeholder:"Select City",
                allowClear:true,
                dropdownParent:$('#add_city_form')
            });

            $('#report_location_id').select2({
                width:'100%',
                placeholder:"Select Reporting Location",
                allowClear:true,
                dropdownParent:$('#add_city_form')
            });

            $('#city_id_edit').select2({
                width:'100%',
                placeholder:"Select City",
                allowClear:false,
            });

            $('#report_location_id_edit').select2({
                width:'100%',
                placeholder:"Select Reporting Location",
                allowClear:false,
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.city.ajax') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('City Name');
                            head.push('City Code');
                            head.push('City ID');
                            head.push('Hub Name');
                            head.push('Hub Code');
                            head.push('Iata Code');
                            head.push('Zone');
                            head.push('Business Category');
                            head.push('GC Area');
                            head.push('Attempt Tat');
                            head.push('Status');
                            head.push('Updated By');
                            head.push('Updated At');
                            head.push('Address');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.city_code);
                                row.push(values.city_id);
                                row.push(values.hub);
                                row.push(values.hub_id);
                                row.push(values.iata_code);
                                row.push(values.zone);
                                row.push(values.business_category);
                                row.push(values.gc_area);
                                row.push(values.attempt_tat);
                                row.push(values.status);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                                row.push(values.address);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table =  $('.datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || in_array(89, session('permissions')))
                buttons: [
                {
                    extend: 'excel',
                    title: 'City Management',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
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
                    url: '{{ route('admin.management.add_city_sub_area_ajax') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.city_id = '{{$city_id}}';
                    }
                },
                rowId: 'id',
                order: [[7, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'relocation_name', name: 'rl.name', class: 'align-middle relocation_name'},
                    {data: 'location', name: 'location', class: 'align-middle location', orderable: false, searchable: false},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'admin_name', name: 'a.name', class: 'align-middle admin_name'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
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
                        '<option value="0">Inactive</option>' +
                        '<option value="1">Active</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.location')  || $(header).is('.hub_location') || $(header).is('.latitude') || $(header).is('.longitude')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        } else {
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
                    // $("#shipping_mode_type").prepend('<option value="" selected></option>').select2({
                    //     placeholder: "Select Shipping Mode Type",
                    //     width:'100%',
                    //     containerCssClass: 'select-xs',
                    //     dropdownCssClass: 'form-control-sm p-0'
                    // });
                    $("#gc_area_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select GC Area",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('input.icheck').iCheck({
                checkboxClass: 'icheckbox_squaret-red',
                radioClass: 'iradio_square-red'
            });

            $("#add_city_form").submit(function(e) {

                var form = $(this).serialize();
                $.ajax({
                    url: '{{ route('admin.management.city_sub_area_post') }}',
                    method: 'POST',
                    data: form,
                    })
                    .done(function (data) {
                        if(data.status === 1){

                            table.draw();
                            swal({
                                title: 'Success',
                                text:'Success',
                                icon: 'success',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                        }else{
                            var log = "";
                            $.each(data.errors, function (i,val) {
                                log += val + '<br>'; // use HTML tag to add line break
                            });

                            content = document.createElement('div');
                            content.innerHTML = log;

                            swal({
                                title: 'Error',
                                content:content, // wrap the content inside a <div> tag
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                    });
                });


            $("#edit_city_form").submit(function(e) {

                var form = $(this).serialize();
                $.ajax({
                    url: '{{ route('admin.management.city_sub_area_post') }}',
                    method: 'POST',
                    data: form,
                    })
                    .done(function (data) {
                        if(data.status === 1){

                            table.draw();
                            swal({
                                title: 'Success',
                                text:'Success',
                                icon: 'success',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            $('#city_area_edit_modal').modal('hide');

                        }else{
                            var log = "";
                            $.each(data.errors, function (i,val) {
                                log += val + '<br>'; // use HTML tag to add line break
                            });

                            content = document.createElement('div');
                            content.innerHTML = log;

                            swal({
                                title: 'Error',
                                content:content, // wrap the content inside a <div> tag
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                    });
                });



              $('body').on('click','.active_sub_area',function () {
                var id = $(this).parents('tr').attr('id');
                var status = $(this).attr('rel');
                swal({
                    title: 'Are You Sure?',
                    text: `${(status) == 1 ? 'Select Yes to Active this Area!' :' Select Yes to  Deactivate this Area!'}`,
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
                })
                    .then(function (confirm) {
                        if (confirm) {

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $.ajax({
                                url: '{!!  route('admin.management.city_area_status')!!}',
                                method: 'POST',
                                data: {'status':status,'id':id},
                            })
                                .done(function (data) {
                                    if(data.status === 1) {
                                        table.draw();
                                        swal({
                                            title: 'Success',
                                            text: 'Success',
                                            icon: 'success',
                                            closeOnClickOutside: false,
                                            closeOnEsc: false
                                        });

                                    }
                                });

                        }
                    });
            });


            });

        $('#city_area_edit_modal').on('shown.bs.modal',function (e) {
            var id = $(e.relatedTarget).data('target-id');
            var city_id = $(e.relatedTarget).data('target-city_id');
            var report_location_id = $(e.relatedTarget).data('target-report_location_id');
            var name = $(e.relatedTarget).data('target-name');
            $('#city_id_edit').val(city_id).trigger('change');
            $('#report_location_id_edit').val(report_location_id).trigger('change');
            $('#name_edit').val(name);
            $('#city_area_id').val(id);

        });




    </script>

@endsection