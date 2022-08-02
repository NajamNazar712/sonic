@extends('admin.layout.master')

@section('title', 'Employee Confirmation')

@section('content')
    <h1>Employee Confirmation</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                        <div class="col-6 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_admin" id="search_admin" class="form-control select2">
                                                    @foreach($admins as $admin)
                                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                      
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_trax_id" id="search_trax_id" class="form-control select2">
                                                    @foreach($trax_ids as $trax_id)
                                                        <option value="{{$trax_id}}">{{$trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        
                                        <div class="col-2 mt-1">
                                            <div class="form-group">
                                                <button type="button" id="search_filter_btn"
                                                        class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                                    Search
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Probation Form</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Employee Name</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Employee Type</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Date of Joining</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Increment</th>
                                    <th class="border-primary border-darken-1">Probation Peroid End Date</th>
                                    <th class="border-primary border-darken-1">Approve Reason</th>
                                    <th class="border-primary border-darken-1">Reject Reason</th>
                                    <th class="border-primary border-darken-1">Approve By Line Manager</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Approve By HOD</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Approve By HR</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="evaluation_modal" data-backdrop="static" role="dialog" aria-labelledby="evaluation_modal" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="delivered_shipments_modal_title">Employee Evaluation</h4>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="row justify-content-center">
                                <h4>Employee Information</h4>
                            </div>
                            <div class="row justify-content-center p-1">
                                <table class="table table-sm table-bordered border employee_information">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Employee Id</th>
                                        <th>Job Title</th>
                                        <th>Department</th>
                                        <th>Location</th>
                                        <th>Manager</th>
                                        <th>Review Period</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row justify-content-center">
                                <h4>Rating</h4>
                            </div>
                            <div class="row justify-content-center p-1">
                                <table class="table table-sm table-bordered border employee_rating">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>1 - Poor</th>
                                        <th>2 - Fair</th>
                                        <th>3 - Satisfactory</th>
                                        <th>4 - Good</th>
                                        <th>5 - Excellent</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Staff',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });
            $('#search_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Employee ID',
                width:'100%',
                allowClear:true
            });
            $('#search_cnic').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search CNIC',
                width:'100%',
                allowClear:true
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.adjustment.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Employee Type');
                            head.push('CNIC');
                            head.push('Date of Joining');
                            head.push('Status');
                            head.push('Increment');
                            head.push('Probation Peroid End Date');
                            head.push('Approve Reason');
                            head.push('Reject Reason');
                            head.push('Approve By Line Manager');
                            head.push('Updated At');
                            head.push('Approve By HOD');
                            head.push('Updated At');
                            head.push('Approve By HR');
                            head.push('Updated At');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.confirmation_status);
                                row.push(values.cnic);
                                row.push(values.joining_date);
                                row.push(values.status);
                                row.push(values.increment);
                                row.push(values.probation_end_date);
                                row.push(values.approve_reason);
                                row.push(values.reject_reason);
                                row.push(values.approve_by_lm);
                                row.push(values.approve_by_lm_at);
                                row.push(values.approve_by_hod);
                                row.push(values.approve_by_hod_at);
                                row.push(values.approve_by_hr);
                                row.push(values.approve_by_hr_at);
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
                        title: 'Employee Attendance Adjustments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.human_resource.employee_confirmation.list') }}',
                    data: function (d) {

                        d.search_admin = $('#search_admin').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_trax_id = $('#search_trax_id').val();
                    }
                },
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    
                    {data: 'probation_form', name: 'probation_form', class: 'align-middle probation_form', searchable: false},
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id', searchable: false},
                    {data: 'name', name: 'a.name', class: 'align-middle name', searchable: false},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'confirmation_status', name: 'a.confirmation_status', class: 'align-middle employee_type'},
                    {data: 'cnic', name: 'a.cnic', class: 'align-middle cnic', searchable: false},
                    {data: 'joining_date', name: 'a.joining_date', class: 'align-middle joining_date', orderable: false, searchable: false},
                    {data: 'status', name: 'sn.id', class: 'align-middle status'},
                    {data: 'increment', name: 'employee_confirmations.increment', class: 'align-middle increment', orderable: false},
                    {data: 'probation_end_date', name: 'employee_confirmations.probation_end_date', class: 'align-middle probation_end_date', orderable: false},
                    {data: 'approve_reason', name: 'employee_confirmations.approve_reason', class: 'align-middle approve_reason', orderable: false},
                    {data: 'reject_reason', name: 'employee_confirmations.employee_attendance_adjustments.rejected_reason', class: 'align-middle reject_reason', orderable: false},
                    {data: 'approve_by_lm', name: 'lm.name', class: 'align-middle approve_by_lm'},
                    {data: 'approve_by_lm_at', name: 'employee_confirmations.approve_by_lm_at', class: 'align-middle approve_by_lm_at'},
                    {data: 'approve_by_hod', name: 'hod.name', class: 'align-middle approve_by_hod'},
                    {data: 'approve_by_hod_at', name: 'employee_confirmations.approve_by_hod_at', class: 'align-middle approve_by_hod_at'},
                    {data: 'approve_by_hr', name: 'hr.name', class: 'align-middle approve_by_hr'},
                    {data: 'approve_by_hr_at', name: 'employee_confirmations.approve_by_hr_at', class: 'align-middle approve_by_hr_at'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false }
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var status_filter = '<select name="status_filter" id="status_filter" class="select2 form-control"></select>';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var employee_type = '<select name="employee_type" id="employee_type" class="select2 form-control">' +
                        '<option value="1">Permanent</option>' +
                        '<option value="2">Probation</option>' +
                        '</select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.name') || $(header).is('.trax_id') || $(header).is('.cnic') || $(header).is('.probation_form')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.employee_type')) {
                            $(employee_type).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.status')) {
                            $(status_filter).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    $('#employee_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Employee Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data = $.map({!! $employee_confirmation_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status_filter').prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });

         /*   $('body').on('click', '#datatable .add_probation_form', function () {*/
            $('#datatable tbody').on('click','.add_probation_form', function() {
              
                var trax_id =  $(this).closest('tr').find('.trax_id').text();
                var id = parseInt($(this).parents('tr').attr('id'));

                $.ajax({
                    url: '{!! route('admin.human_resource.employee_confirmation.get_info') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'trax_id': trax_id
                    }
                })
                    .done(function(data) {
                       if(data.status == 1){
                           var html = $('.employee_information tbody');
                          /* html += '<thead><tr><th>S.No</th><th>OSA Area</th><th>OSA Charges</th></tr></thead><tbody>';*/

                               html += '<tr>';
                               html += '<td>'+ data.details.name +'</td>';
                               html += '<td>'+ data.details.trax_id +'</td>';
                               html += '<td>'+ data.details.designation +'</td>';
                               html += '<td>'+ data.details.department +'</td>';
                               html += '<td>'+ data.details.city +'</td>';
                               html += '<td>'+ data.details.manager +'</td>';
                               html += '<td>'+ data.details.review_period +'</td>';
                               html += '</tr>';
                       
                           //html += '</tbody></table>';
                           $('#evaluation_modal .modal-body .employee_information tbody ').html(html);
                       }
                    });

                $('#evaluation_modal').modal('show');
            });




        });
    </script>

@endsection