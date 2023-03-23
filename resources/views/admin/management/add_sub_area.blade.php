@extends('admin.layout.master')

@section('title', 'City Sub Area')

@section('content')
    <h1>City Sub Area</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

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
                                    <th class="border-primary border-darken-1" >Updated At</th>
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
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'city_id', name: 'city_id', class: 'align-middle city_id'},
                    {data: 'relocation_name', name: 'relocation_name', class: 'align-middle relocation_name', orderable: false, searchable: false},
                    {data: 'location', name: 'location', class: 'align-middle location', orderable: false, searchable: false},
                    {data: 'status', name: 'status', class: 'align-middle status', orderable: false, searchable: false},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'ch.created_at', class: 'align-middle updated_at'},
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
        });
        {{--$('#datatable tbody').on('click', 'tr td.modes button', function() {--}}
        {{--    var id = parseInt($(this).parents('tr').attr('id'));--}}

        {{--    $('#modes').modal('show');--}}

        {{--    $.ajax({--}}
        {{--        url: '{!! route('admin.management.shippingModes.ajax') !!}',--}}
        {{--        method: 'GET',--}}
        {{--        data: {--}}
        {{--            '_token': '{{ csrf_token() }}',--}}
        {{--            'id': id--}}
        {{--        }--}}
        {{--    })--}}
        {{--});--}}


        {{--$('body').on('click','#datatable tbody tr td.modes button',function () {--}}
        {{--    var id = parseInt($(this).parents('tr').attr('id'));--}}
        {{--    $('#modes .modal-body').html('');--}}
        {{--    $.ajax({--}}
        {{--        url: '{!! route('admin.management.shippingModes.ajax') !!}',--}}
        {{--        method: 'POST',--}}
        {{--        data: {--}}
        {{--            '_token': '{{ csrf_token() }}',--}}
        {{--            'id': id--}}
        {{--        }--}}
        {{--    })--}}
        {{--        .done(function(data) {--}}
        {{--            if (data.status == 1) {--}}
        {{--                var modes = '';--}}
        {{--                console.log(data);--}}
        {{--                if (data.shipping_mode) {--}}
        {{--                    $.each(data.shipping_mode, function(index, modes) {--}}
        {{--                        modes += 'modes<br>';--}}
        {{--                    });--}}
        {{--                }--}}
        {{--                $('#modes.modal-body').html(modes);--}}
        {{--                $('#modes').modal('show');--}}
        {{--            }--}}
        {{--        });--}}
        {{--});--}}

        $("#addCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');

            if(action == 'addcity'){
                $.get( "/admin/management/city/form", function( data ) {
                    $("#addCityDiv").html(data);
                });
            }

        });
        $("#addInternationalCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');

            if(action == 'addinternationalcity'){
                $.get( "/admin/management/international/city/form", function( data ) {
                    $("#addInternationalCityDiv").html(data);
                });
            }

        });
        $("#editCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');
            var id = $(e.relatedTarget).data('target-id');


            if(action == 'editcity'){
                $.get( "/admin/management/city/"+id+"/edit/form", function( data ) {
                    $("#editCityDiv").html(data);
                });
            }
        });
        $("#editInternationalCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');
            var id = $(e.relatedTarget).data('target-id');


            if(action == 'editinternationalcity'){
                $.get( "/admin/management/international/city/"+id+"/edit/form", function( data ) {
                    $("#editInternationalCityDiv").html(data);
                });
            }
        });
        $('body').on('click','#datatable tbody tr td.osa_list button',function () {
            var id = parseInt($(this).parents('tr').attr('id'));
            $('#osa_modal .modal-body').html('');
            $('#osa_modal').modal('show');

            $.ajax({
                url: '{!! route('admin.management.city.osa_list') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'city_id': id
                }
            })
                .done(function(data) {
                    console.log(data);
                    if (data) {

                        var html = '<table class="table">';
                        html += '<thead><tr><th>S.No</th><th>OSA Area</th><th>OSA Charges</th></tr></thead><tbody>';
                        var counter = 1;
                        $.each(data.osa_list, function(index, osa) {


                            html += '<tr>';
                            html += '<td>'+ counter +'</td>';
                            html += '<td>'+osa.osa_name+'</td>';
                            html += '<td>'+osa.osa_rate+'</td>';
                            html += '</tr>';
                            counter++;
                        });
                        html += '</tbody></table>';
                        $('#osa_modal .modal-body').html(html);
                    }
                });
        });
        $('body').on('click','.deactivate',function (e) {
            var id = $(this).data('target-id');
            var rel = $(this).attr('rel');
            var isHub = $(this).attr('hub');
            if(rel == 'cityInactive'){
                var atext = "Select Yes to Deactive this city!";
            }else{
                var atext = "Select Yes to active this city!";
            }

            if(isHub == 0){
                $('#city_active_form #cid').val(id);
                $('#city_active_form #cstatus').val(rel);
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
                        $('#city_active_form').submit();
                    }
                });
                // $('#ConfirmModalCity').modal('show');
            }else if(isHub == 1){
                if(rel == 'cityactive'){
                    $('#city_active_form #cid').val(id);
                    $('#city_active_form #cstatus').val(rel);
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
                            $('#city_active_form').submit();
                        }
                    });
                }else{
                    $.ajax({
                        url:'/admin/management/city/'+id+'/status/ajax',
                        type:'GET',
                        dataType:'json',
                        success:function (data) {
                            var name = [];
                            if(data.length > 0){
                                var comma = '';
                                $.each(data, function (index, value) {
                                    if(data.length != index+1){ comma = ", ";}else{
                                        comma = '';
                                    }
                                    name += value.name+comma;

                                });
                                swal({
                                    title: 'Please remove following cities from hub!',
                                    text: name,
                                    icon: 'info',
                                    buttons: {
                                        cancel: {
                                            text: 'Close',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        }
                                    },
                                    closeOnClickOutside: true,
                                    closeOnEsc: true
                                });

                            }else{
                                $('#city_active_form #cid').val(id);
                                $('#city_active_form #cstatus').val(rel);
                                if(rel == 'cityInactive'){
                                    var atext = "Select Yes to Deactive this Hub!";
                                }else{
                                    var atext = "Select Yes to active this Hub!";
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
                                        $('#city_active_form').submit();
                                    }
                                });
                            }

                        }
                    });
                }

            }

        });



    </script>

@endsection