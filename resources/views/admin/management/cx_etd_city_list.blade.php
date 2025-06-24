    @extends('admin.layout.master')

@section('title', 'Cx ETD City List')

@section('content')
    <h1>Cx ETD City List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    
                    <div class="card-content">
                        <div class="col mt-3">
                                <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                                    <input type="hidden" id="search_cx_city" name="search_cx_city" value="1">

                                    {{-- Search date from filter --}}
                                    {{-- <div class="col-4">
                                        <div class="form-group col">
                                            <select name="search_city_type_etd_id" id="search_city_type_etd_id"
                                                    class="select2 form-control">
                                                @foreach($cityType as $typEtd)
                                                    <option value="{{$typEtd->id}}">{{$typEtd->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}

                                    
                                    {{-- Search btn --}}
                                    {{-- <div class="col-2">
                                        
                                            <button type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                        
                                    </div> --}}
                                </form>
                            </div>
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1" >S No.</th>
                                    <th class="border-primary border-darken-1" >City Name</th>
                                    <th class="border-primary border-darken-1" >City Code</th>
                                    <th class="border-primary border-darken-1" >City ID</th>
                                    <th class="border-primary border-darken-1" >Hub Name</th>
                                    <th class="border-primary border-darken-1" >Hub Code</th>
                                    <th class="border-primary border-darken-1" >Iata Code</th>
                                    <th class="border-primary border-darken-1" >Zone</th>
                                    <th class="border-primary border-darken-1" >Province</th>
                                    <th class="border-primary border-darken-1" >Businees Category</th>
                                    <th class="border-primary border-darken-1" >City Type ETD</th>
                                    <th class="border-primary border-darken-1" >GC Area</th>
                                    <th class="border-primary border-darken-1" >Attempt Tat</th>
                                    <th class="border-primary border-darken-1" >Status</th>
                                    <th class="border-primary border-darken-1" >Created By</th>
                                    <th class="border-primary border-darken-1" >Created At</th>
                                    <th class="border-primary border-darken-1" >Updated By</th>
                                    <th class="border-primary border-darken-1" >Updated At</th>
                                    <th class="border-primary border-darken-1" >Location</th>
                                    <th class="border-primary border-darken-1" >Hub Location</th>
                                    <th class="border-primary border-darken-1" >OSA</th>
                                    <th class="border-primary border-darken-1" >Address</th>
                                    <th class="border-primary border-darken-1" >Booking Enable Status</th>
                                    <th class="border-primary border-darken-1" >Status Logs</th>
                                    <th class="border-primary border-darken-1" >Booking Enable/Disable Logs</th>

                                    <th class="border-primary border-darken-1" ></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="modal fade" id="modes" role="dialog" aria-labelledby="shipping_mode_title" aria-hidden="true">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="shipping_mode_title">Shipping Mode(s)</h4>

                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
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

    <style>
        .select2-container .select2-search__field {
            width: 100% !important;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/checkbox-radio.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var selected_rows = [];
           
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
                            head.push('Province');
                            head.push('Business Category');
                            head.push('City Type ETD');
                            head.push('GC Area');
                            head.push('Attempt Tat');
                            head.push('Status');
                            head.push('Created By');
                            head.push('Created At');
                            head.push('Updated By');
                            head.push('Updated At');
                            head.push('Address');
                            head.push('Booking Enable Status');
                            head.push('Status Logs');
                            head.push('Booking Enable/Disable Logs');


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
                                row.push(values.province_name);
                                row.push(values.business_category);
                                row.push(values.city_type_etd);
                                row.push(values.gc_area);
                                row.push(values.attempt_tat);
                                row.push(values.status);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.updated);
                                row.push(values.address);
                                row.push(values.booking_enable_status);
                                row.push(values.status_change_logs_count);
                                row.push(values.booking_status_change_logs_count);

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
                    },

                        'reset'],
                @else
                buttons: [{
                    extend: 'excel',
                    title: 'City Management',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'],
                @endif
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                serverSide: true,
                ajax: {
					url: '{{ route('admin.management.city.ajax') }}',
					method: 'GET',
					data: function (d) {
						d._token = '{{ csrf_token() }}'; 
                        d.search_cx_city = $('#search_cx_city').val(); //
					}
				},
               rowId: 'id',
                order: [[15, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'cities.name', class: 'align-middle city'},
                    {data: 'city_code', name: 'cities.city_code', class: 'align-middle city_code'},
                    {data: 'city_id', name: 'cities.id', class: 'align-middle city_id'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'hub_id', name: 'cities.hub_id', class: 'align-middle hub_id'},
                    {data: 'iata_code', name: 'cities.iata_code', class: 'align-middle iata_code'},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'province_name', name: 'provinces.name', class: 'align-middle province_name'},
                    {data: 'business_category', name: 'bc.id', class: 'align-middle business_category'},
                    {data: 'city_type_etd', name: 'cetds.id', class: 'align-middle city_type_etd'},
                    {data: 'gc_area', name: 'cities.gc_area', class: 'align-middle gc_area'},
                    {data: 'attempt_tat', name: 'cities.attempt_tat', class: 'align-middle attempt_tat'},
                    {data: 'status', name: 'cities.status', class: 'align-middle status'},
                    {data: 'created_by', name: 'c.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'updated', name: 'ch.created_at', class: 'align-middle updated_at'},
                    {data: 'location', name: 'location', class: 'align-middle location', orderable: false, searchable: false},
                    {data: 'hub_location', name: 'hub_location', class: 'align-middle hub_location', orderable: false, searchable: false},
                    {data: 'osa_list', name: 'osa_list', class: 'align-middle osa_list', orderable: false, searchable: false},
                    {data: 'address', name: 'cities.address', class: 'align-middle address'},
                    {data: 'booking_enable_status', name: 'cities.booking_enable_status', class: 'align-middle booking_enable_status'},                   
                    {data: 'status_logs', name: 'status_logs', class: 'align-middle status_logs'},
                    {data: 'booking_enable_disable_logs', name: 'booking_enable_disable_logs', class: 'align-middle booking_enable_disable_logs'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
               rowCallback: function(row, data, index) {
                   var info = table.page.info();

                   $('td:eq(1)', row).html(index + 1 + info.page * info.length);

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
                   var gc_area_select = '<select name="gc_area_select" id="gc_area_select" class="select2 form-control">' +
                       '<option value="0">No</option>' +
                       '<option value="1">Yes</option>' +
                       '</select>';

                   var business_category = '<select name="business_category" id="business_category" class="select2 form-control"></select>';
                   var city_type_etd = '<select name="city_type_etd" id="city_type_etd" class="select2 form-control"></select>';
                   this.api().columns().every(function(column_id) {
                       var column = this;
                       var header = column.header();
             
                       if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.location')  || $(header).is('.hub_location') || $(header).is('.latitude') || $(header).is('.longitude')) {
                           $(td).appendTo($(search));
                       }else if($(header).is('.status')){
                           $(status_select).appendTo($(search))
                               .on( 'change', function () {
                                   column.search($(this).val(), false, false, true).draw();
                               } ).wrap(td);
                       }
                       else if($(header).is('.gc_area')){
                           $(gc_area_select).appendTo($(search))
                               .on( 'change', function () {
                                   column.search($(this).val(), false, false, true).draw();
                               } ).wrap(td);
                       }else if($(header).is('.business_category')){
                           $(business_category).appendTo($(search))
                               .on( 'change', function () {
                                   column.search($(this).val(), false, false, true).draw();
                               }).wrap(td);
                       }else if($(header).is('.city_type_etd')){
                           $(city_type_etd).appendTo($(search))
                               .on( 'change', function () {
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
                   var data = $.map({!! $business_categories !!}, function (obj) {
                       obj.id = obj.id;
                       obj.text = obj.name;

                       return obj;
                   });
                   $("#business_category").prepend('<option value="" selected></option>').select2({
                       data: data,
                       placeholder: "Select Business Category",
                       width:'100%',
                       containerCssClass: 'select-xs',
                       dropdownCssClass: 'form-control-sm p-0'
                   });
                   var data = $.map({!! $cityType !!}, function (obj) {
                       obj.id = obj.id;
                       obj.text = obj.name;

                       return obj;
                   });
                   $("#city_type_etd").prepend('<option value="" selected></option>').select2({
                       data: data,
                       placeholder: "Select City Etd Type",
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
        $("#editCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');
            var id = $(e.relatedTarget).data('target-id');
            

            if(action == 'editCity'){
                $.get( "/admin/management/city/"+id+"/edit/form/"+1+"", function( data ) {
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

        $(document).off('click.statusLogs').on('click.statusLogs', '.status_logs, .booking_enable_disable_logs', function (event) {
            const button = $(event.currentTarget); 
            const cityId = button.data('id');
            const type = button.data('type');

            if(cityId && type){

                let baseUrl = @json(route('admin.management.get_city_status_logs', ['cityId' => 'CITY_ID_PLACEHOLDER']));
                let url = baseUrl.replace('CITY_ID_PLACEHOLDER', cityId) + '?type=' + type;

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        let rows = '';
                        response.forEach((log, index) => {
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${log.city_name}</td>
                                    <td>${log.new_status ? 'Enabled' : 'Disabled'}</td>
                                    <td>${log.changed_at}</td>
                                    <td>${log.updated_by_name}</td>
                                </tr>
                            `;
                        });

                        $('#statusLogModalTitle').text(
                            type === 'city' ? 'City Enable/Disable Logs' : 'Booking Enable/Disable Logs'
                        );

                        $('#statusLogTableBody').html(rows);
                        $('#statusLogModal').modal('show');
                    },
                    error: function () {
                        console.log('error');
                    }
                });
            }
        });

    </script>

@endsection