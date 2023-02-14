@extends('admin.layout.master')

@section('title', 'Employee Penalty')

@section('content')
    <h1>Employee Penalty</h1>

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
                                        <th class="border-primary border-darken-1">ID</th>
                                        <th class="border-primary border-darken-1">Employee ID</th>
                                        <th class="border-primary border-darken-1">Employee Name</th>
                                        <th class="border-primary border-darken-1">Designation</th>
                                        <th class="border-primary border-darken-1">Department</th>
                                        <th class="border-primary border-darken-1">Employee Type</th>
                                        <th class="border-primary border-darken-1">Availed Leave</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">No of Late</th>
                                        <th class="border-primary border-darken-1">Requested Date</th>
                                        <th class="border-primary border-darken-1">Updated By</th>
                                        <th class="border-primary border-darken-1">Updated At</th>
                                        <th class="border-primary border-darken-1">Deduction Count</th>
                                        <th class="border-primary border-darken-1">Reject Reason</th>
                                        <th class="border-primary border-darken-1">Leave Without Pay</th>
                                        <th class="border-primary border-darken-1">Leave Deduction</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="rejectEmployeeLateModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="rejectEmployeeLateModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Reject</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.employee_penalty.reject')}}"
                          class="form-horizontal mb-1 justify-content-center" method="POST" id="rejectConfirmationForm"
                          novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="reject_confirmation_id" id="reject_confirmation_id">
                        <input type="hidden" name="reject_by" id="reject_by">
                        <input type="hidden" name="reject_by_id" id="reject_by_id">

                        <div class="row mb-2 justify-content-center">
                           
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Reason</label>
                                    <textarea class="form-control" name="reject_reason" id="reject_reason" cols="30" rows="10" data-rule-required="true" data-msg-required="Reason is required"></textarea>
                                   
                                </div>
                            </div>
                          

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary btn-min-width">Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="duplicate_modal" data-backdrop="static" role="dialog" aria-labelledby="duplicate_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">Late Record</h4>

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

    <div class="modal fade text-left" id="deductionEmployeeLateModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="deductionEmployeeLateModal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Deduction on Late</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.human_resource.employee_penalty.deduction_store')}}"
                          class="form-horizontal mb-1 justify-content-center" method="POST" id="approveConfirmationForm"
                          novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="approve_confirmation_id" id="approve_confirmation_id">
                        <input type="hidden" name="approve_by" id="approve_by">
                        <input type="hidden" name="deduction_count" id="deduction_count">
                        <input type="hidden" name="salary_deduction_count" id="salary_deduction_count">
                        <input type="hidden" name="employee_id" id="employee_id">
                        <input type="hidden" name="line_manager_id" id="line_manager_id">

                        <div class="row mb-2 justify-content-center">
                            <div class="col-12">
                                <div class="deduction_table">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2 justify-content-center">
                            <div class="col-6">
                                <div class="form-group">
                                    <select class="form-control select_deduction" id="select_deduction" name="select_deduction"> 
                                    <option value="0">Select Type of Deduction</option>  
                                    <option value="deduction_quota">Deduction from Quota</option>  
                                    <option value="deduction_salary">Deduction from Salary</option>  
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="employee_attendence_qouta">
                        </div>
                       
                    </form>
                </div>
            </div>
        </div>
    </div>

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
            var late_count = null;
            var deduction_count = null;
            var available_leave_quota = null;
            var employee_id = null;
            var approve_by = null;
            var line_manger = null;

            $('#probation_end_date').pickadate({
                    firstDay: 1,
                    format:'yyyy-mm-dd',
                    clear: '',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd',
                    hiddenSuffix: '_formatted',
                    // min: '{{ Carbon\Carbon::now()}}',

                });
                // probation_end_date.set('disable');
            $('#probation_end_date').attr('disabled', true);
            $('#salary_increment_input').attr('disabled', true);

            


            $('#search_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Staff',
                width:'100%',
                allowClear:true
            });
        
            $('#search_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Employee ID',
                width:'100%',
                allowClear:true
            });
         


        


            // probation_end_date
         


            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.employee_penalty.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('ID');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Employee Type');
                            head.push('Availed Leave');
                            head.push('Status');
                            head.push('No of Late');
                            head.push('Requested Date');
                            head.push('Updated By');
                            head.push('Updated At');
                            head.push('Deduction Count');
                            head.push('Reject Reason');
                            head.push('Leave Without Pay');
                            head.push('Leave Deduction');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.trax_id);
                                row.push(values.employee_name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.employee_type);
                                row.push(values.leave_availed);
                                row.push(values.status);
                                row.push(values.no_of_late_excel);
                                row.push(values.requested_date);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                                row.push(values.deduction_count);
                                row.push(values.reject_reason);
                                row.push(values.leave_without_pay);
                                row.push(values.leave_deduction);
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
                        title: 'Employee Late Deduction Record',
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
                    url: '{{ route('admin.human_resource.employee_penalty.list') }}',
                    data: function (d) {

                        d.search_admin = $('#search_admin').val();
                        d.search_trax_id = $('#search_trax_id').val();
                    }
                },
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'id', name: 'employee_penalties.id', class: 'align-middle id', searchable: false},
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id', searchable: false},
                    {data: 'employee_name', name: 'a.name', class: 'align-middle name', searchable: false},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'employee_type', name: 'el.employee_type_id ', class: 'align-middle employee_type'},
                    {data: 'leave_availed', name: 'leave_availed', class: 'align-middle leave_availed'},
                    {data: 'status', name: 'ls.name', class: 'align-middle status'},
                    {data: 'no_of_late', name: 'no_of_late', class: 'align-middle no_late'},
                    {data: 'requested_date', name: 'employee_penalties.created_at', class: 'align-middle requested_date'},
                    {data: 'updated_by', name: 'lm.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'employee_penalties.updated_at', class: 'align-middle updated_at'},
                    {data: 'deduction_count', name: 'deduction_count', class: 'align-middle deduction_count'},
                    {data: 'reject_reason', name: 'reject_reason', class: 'align-middle reject_reason'},
                    {data: 'leave_without_pay', name: 'leave_without_pay', class: 'align-middle leave_without_pay'},
                    {data: 'leave_deduction', name: 'leave_deduction', class: 'align-middle leave_deduction'},
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

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.trax_id')) {
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
          
           
            

   

            

            $("#editConfirmationForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Probation is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#datatable tbody').on('click','.add_probation_form', function() {
              
              var trax_id =  $(this).closest('tr').find('.trax_id').text();
              var id = parseInt($(this).parents('tr').attr('id'));
              $('#employee_confirmation_id').val(id);

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

                         $('#evaluation_modal .modal-body .employee_information tbody ').html(html);
                     }
                  });

              $('#evaluation_modal').modal('show');
          });

            $('#datatable tbody').on('click','.view_probation_form', function() {

                // var trax_id =  $(this).closest('tr').find('.trax_id').text();
                var id = $(this).data('target-id');
                

                $.ajax({
                    url: '{!! route('admin.human_resource.employee_confirmation.view') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'confirmation_id': id
                    }
                })
                    .done(function(data) {
                        if(data.status == 1){
                            console.log(data.details.rating);
                            console.log($('.view_job_knowledge')[0]);
                            
                            $('.view_job_knowledge')[data.details.rating.job-1].checked = true;
                            $(' #view_job_comments').val(data.details.rating.job_comments);

                            $('.view_work_quality')[data.details.rating.quality-1].checked = true;
                            $('#view_quality_comment').val(data.details.rating.quality_comments);

                            $('.view_attendance')[data.details.rating.attendance-1].checked = true;
                            $('#view_attendance_comment').val(data.details.rating.attendance_comments);

                            $('.view_initiative')[data.details.rating.initiative-1].checked = true;
                            $('#view_initiative_comment').val(data.details.rating.initiative_comments);

                            $('.view_communication')[data.details.rating.communication-1].checked = true;
                            $('#view_communication_comment').val(data.details.rating.communication_comments);

                            $('.view_dependability')[data.details.rating.dependability-1].checked = true;
                            $('#view_dependability_comment').val(data.details.rating.dependability_comments);

                            $('input[name="view_rating_comment').val(data.details.rating.overall_rating);
                            $('#view_evaluation_comment').val(data.details.rating.evaluation_comments);

                            
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

                            $('#view_evaluation_modal .modal-body .employee_information tbody ').html(html);
                        }
                    });

                $('#view_evaluation_modal').modal('show');
            });
          
          
           

            $('body').on('click', '.reject_lm', function (e) {
                line_manger = $(this).attr('data-target-line-manger');
                var id = $(this).data('target-id');
                $('#reject_confirmation_id').val(id);
                $('#reject_by').val('lm');
                $('#reject_by_id').val(line_manger);
                $('#rejectEmployeeLateModal').modal('show');
            });
          
            $('body').on('click', '.deduction_modal', function (e) {
                var id = $(this).data('target-id');
                // $('#approve_confirmation_id').val(id);
                // $('#approve_by').val('hod');
                
                $('#deductionEmployeeLateModal').modal('show');
            });
            $('body').on('click', 'button.deduction_modal',  function(){
            var id = $(this).parents('tr').attr('id');
            employee_id = $(this).attr("data-target-id");
            var no_of_late = $('.duplicate_modal').text();
            var no_of_deduction = Math.floor(no_of_late/3);
            late_count = no_of_late;
            deduction_count = no_of_deduction;
            if(id){
                $.ajax({
                    url: '{!! route('admin.human_resource.employee_penalty.deduction') !!}',
                    data: {employee_id}
                })
               
                .done(function(data) {
                   $('#deductionEmployeeLateModal').modal('show');
                    
                   available_leave_quota = data.info.availble_qouate;

                   var html = '<div class="row">';
                   
                    html += '<div class="table-responsive col-sm-6">';
                    html += '<table class="table">';
                    html += '<tr>';
                    html += '<th>Available Leave</th>';
                    html += '<td>'+ data.info.availble_qouate +'</td>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<th>Availed Leave</th>';
                    html += '<td>'+ data.info.available_leaves +'</td>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<th>Remaining Leave</th>';
                    html += '<td>'+ data.info.remaing_leaves +'</td>';
                    html += '</tr>';
                    html += '</table>';
                    html += '</div>'; 
                    html += '<div class="table-responsive col-sm-6">';
                    html += '<table class="table">';
                    html += '<tr>';    
                    html += '<th>No of Lates</th>';
                    html += '<td>'+ no_of_late +'</td>';
                    html += '</tr>';    
                    html += '<tr>';    
                    html += '<th>Deduction</th>';
                    html += '<td>'+ no_of_deduction +'</td>';
                    html += '</tr>';    
                    html += '</table>';
                    html += '</div>'; 
                    html += '</div>'; 
                    $('#deductionEmployeeLateModal .deduction_table').html(html);
                });
                
            }
            });
            $('#rejectEmployeeLateModal').on('hide.bs.modal', function () {
                $('#reject_confirmation_id').val('');
                $('#reject_by').val('');
                $('#reject_reason').val('');
            });
            $('#editEmployeeConfirmationModal').on('hide.bs.modal', function () {
                $('#confirmation_id').val('');
                $('#employee_name').val('');
                $('#employee_id').val('');
                $('#designation').val('');
                $('#department').val('');
                $('#joining_date').val('');
                $('#probation_end_date').val('');
                $('#add_reason').val('');
                
            });
            $('#select_deduction').on('change',function()
            {
                
                var id = $(this).data('target-id');
                $('#approve_confirmation_id').val(id);
                $('#approve_by').val('lm');

                // late_count , deduction_count, available_leave_quota Initialize globaly 
                line_manager_id = $('.deduction_modal').attr('data-target-line-manger');
                
                if(late_count != null && deduction_count != null)
                {
                    var deduction_select = $('#select_deduction').val();
                    if(deduction_select == 'deduction_quota')
                    {
                        $("#deduction_count").val(deduction_count);
                        $("#employee_id").val(employee_id);
                        $("#line_manager_id").val(line_manager_id);
                        if(deduction_count > available_leave_quota)
                        {
                            var exceed_count = deduction_count - available_leave_quota;
                            var deduction_quota_count = deduction_count - exceed_count;
                            var deduction_salary_count = exceed_count;
                             $("#salary_deduction_count").val(deduction_salary_count);
                            var html = '<div class="row">';
                                    html += '<div class="table-responsive col-sm-12">';
                                        html += '<table class="table">';
                                            html += '<tr>';
                                                html += '<th>Sr.no</th>';
                                                html += '<th>Description</th>';
                                                html += '<th>Deduction Count</th>';
                                            html += '</tr>';
                                            html += '<tr>';
                                                html += '<td>1</td>';
                                                html += '<td>Deduction from Leave Quota</td>';
                                                html += '<td> ' + deduction_quota_count + '</td>';
                                            html += '</tr>';
                
                                            html += '<tr>';
                                                html += '<td>2</td>';
                                                html += '<td>Deduction from Salary</td>';
                                                html += '<td> ' + deduction_salary_count + '</td>';
                                            html += '</tr>';
                                        html += '</table>';
                                    html += '</div>'; 
                                html += '</div>'; 
                        }

                        else{
                            
                            var html = '<div class="row">';
                                    html += '<div class="table-responsive col-sm-12">';
                                        html += '<table class="table">';
                                            html += '<tr>';
                                                html += '<th>Sr.no</th>';
                                                html += '<th>Description</th>';
                                                html += '<th>Deduction Count</th>';
                                            html += '</tr>';
                                            html += '<tr>';
                                                html += '<td>1</td>';
                                                html += '<td>Deduction from Leave Quota</td>';
                                                html += '<td> ' + deduction_count + '</td>';
                                            html += '</tr>';
                                        html += '</table>';
                                    html += '</div>'; 
                                html += '</div>'; 
                        }
                    }
                    else if(deduction_select == 'deduction_salary')
                    {
                        $("#salary_deduction_count").val(deduction_count);
                        $("#employee_id").val(employee_id);
                        $("#line_manager_id").val(line_manager_id);
                        var html = '<div class="row">';
                                    html += '<div class="table-responsive col-sm-12">';
                                        html += '<table class="table">';
                                            html += '<tr>';
                                                html += '<th>Sr.no</th>';
                                                html += '<th>Description</th>';
                                                html += '<th>Deduction Count</th>';
                                            html += '</tr>';
                                            html += '<tr>';
                                                html += '<td>1</td>';
                                                html += '<td>Deduction from Salary</td>';
                                                html += '<td> ' + deduction_count + '</td>';
                                            html += '</tr>';
                                        html += '</table>';
                                    html += '</div>'; 
                                html += '</div>'; 
                    }

                    html += `<div class="row justify-content-center"> <div class="col-md-4 text-center"> <button type="button" data-dismiss="modal" class="btn btn-light btn-min-width">Close</button> <button type="submit" name="edit" class="btn btn-primary btn-min-width approve_lm">Approve</button></div> </div>`;

                    $('#deductionEmployeeLateModal .employee_attendence_qouta').html(html);
                }
                
                
            });
           
            $('body').on('click', 'button.duplicate_modal',  function(){
            var id = $(this).parents('tr').attr('id');
            var employee_id = $(this).attr("data-user_id");
           
            if(employee_id){
                $.ajax({
                    url: '{!! route('admin.human_resource.employee_penalty.duplicate') !!}',
                    data: {employee_id}
                })
                .done(function(data) {
                   $('#duplicate_modal').modal('show');
                    var html = '<table class="table table-bordered">';
                        html+='<thead><th>Date</th><th>Clock_in</th></thead>';
                    $.each(data.info, function(index, value) {
                        
                        html+= '<tr><td>'+ value.attendance_date +'</td><td>'+ value.clock_in +'</td></tr>';
                    });
                    $('#duplicate_modal .modal-body').html(html);
                });
            }
            });
        });
    </script>

@endsection