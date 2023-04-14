@extends('admin.layout.master')

@section('title', 'Fintech Companies List')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Fintech Companies List
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Fintech Company Name</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Added by</th>
                                    <th class="border-primary border-darken-1">Added at</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                    <!-- <th class="border-primary border-darken-1"></th> -->
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddIncentiveModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddIncentiveModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Incentive Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">


                    {{-- <form id="add_incentive_form" action="{{route('admin.settings.hr.rider_incentive.store')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row justify-content-center">

                                <fieldset class="col-12 form-group">
                                    <select name="rider_category_select" id="rider_category_select" class="form-control select2" data-rule-required="true" data-msg-required="Category is required">
                                     
                                    </select>
                                </fieldset>

                                <fieldset class="col-12 form-group">
                                    <select name="delivery_payment_select" id="delivery_payment_select" class="form-control select2" data-rule-required="true" data-msg-required="Type is required">
                                    
                                    </select>
                                </fieldset>


                                <fieldset class="col-12 form-group">
                                    <select name="weight_range_select" id="weight_range_select" class="form-control select2" data-rule-required="true" data-msg-required="Range is required">
                                 
                                    </select>
                                </fieldset>

                                <fieldset class="col-12 form-group">
                                    <input type="text" class="form-control incentive_value" name="incentive_value" data-rule-required="true" data-msg-required="Incentive/Shipment is required" placeholder="Incentive/Shipment">
                                </fieldset>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-block">Add</button>
                                </div>
                            </div>

                        </div>
                    </form> --}}


                </div>
            </div>
        </div>
    </div>



    <div class="modal fade text-left" id="EditIncentiveModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditIncentiveModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Setting</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_incentive_form" action="{{route('admin.settings.hr.rider_incentive.update')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">


                            <div class="row justify-content-center">

                                <fieldset class="col-12 form-group">
                                    <select name="" id="edit_rider_category_select" class="form-control select2" data-rule-required="true" data-msg-required="Category is required" disabled>
                                     
                                    </select>
                                </fieldset>

                                <fieldset class="col-12 form-group">
                                    <select name="" id="edit_delivery_payment_select" class="form-control select2" data-rule-required="true" data-msg-required="Type is required" disabled>
                                      
                                    </select>
                                </fieldset>


                                <fieldset class="col-12 form-group">
                                    <select name="" id="edit_weight_range_select" class="form-control select2" data-rule-required="true" data-msg-required="Range is required" disabled>
                                     
                                    </select>
                                </fieldset>

                                <fieldset class="col-12 form-group">
                                    <input type="text" class="form-control incentive_value" name="edit_incentive_value" data-rule-required="true" data-msg-required="Incentive/Shipment is required" placeholder="Incentive/Shipment">
                                </fieldset>
                            </div>
                            <input type="hidden" name="incentive_setting_id" id="incentive_setting_id">
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-block">Update</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


{{-- Modal Start --}}


<div class="modal fade text-left" id="FintechCompanyStatus" data-backdrop="static" role="dialog" aria-labelledby=""
    aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
       <div class="modal-content">
           <div class="modal-header">
               <h4 class="modal-title">Enable/Disable Fintech Company </h4>
           </div>
               <div class="modal-body">
                    <div class="col text-center">
                        <label class="font-medium-2 font-weight-bold block">Are You Want to Enable Company? </label>
                        <div class="form-group">
                            <input type="hidden" name="userID" id="userID">
                            <label for="" class="font-medium-2 text-bold-600 mr-1">No</label>
                            <input type="checkbox"  id="user_fintech_charges_checkbox" class="switchery restrict_order_id_checkbox" data-size="sm" data-switchery="true">
                            <label for="" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                        </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button " class="btn btn-success"  onclick="save_changes()" >Submit</button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>
                    </div>
               </div>
               
          
       </div>
   </div>
</div>

{{-- Modal End --}}






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

    function save_changes(){
    var id = $("#userID").val();
    
    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Standard Fintech Charges!',
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
                            var fintechCharges = $("#user_fintech_charges_txtbox").val();
                            var userID = $("#userID").val();
                            if (document.getElementById('user_fintech_charges_checkbox').checked) {
                               var checkboxval = 'true';
                            }
                            else{
                                var checkboxval = 'false';
                            }
                          $.ajax({
                            type : 'GET',
                            url  : "{!! route('admin.settings.fintech_company_charges.status') !!}",
                            data : {id:id,checkboxval:checkboxval},
                            success:function(res){
                                if(res.status == '200'){
                                    toastr.success(res.message, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center',
                                });
                                $("#FintechCompanyStatus").modal('hide');
                                //window.location.reload();
                                }
                                else{
                                    toastr.error(res.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                                
                                }
                            }

                            });
                        }
                    });
    }

    function changefintechcompanystatus(event,id,status){
        if(status == '1'){
            if (document.getElementById('user_fintech_charges_checkbox').checked) {

            }
            else{
                $('#user_fintech_charges_checkbox').click();
            }
        }
        else{
            if (document.getElementById('user_fintech_charges_checkbox').checked) {
                $('#user_fintech_charges_checkbox').click();
            }
            else{
               
            }
        }
        $("#userID").val(id);
        $("#FintechCompanyStatus").modal('show');
    }


        $(document).ready(function() {
            $('#rider_category_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Category*',
                dropdownParent:$('#add_incentive_form')
            });

            $('#delivery_payment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Payment Type*',
                dropdownParent:$('#add_incentive_form')
            });

            $('#weight_range_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Weight Range*',
                dropdownParent:$('#add_incentive_form')
            });

            $( "#add_incentive_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'New Setting is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('input.incentive_value').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $( "#edit_incentive_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Setting is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#edit_rider_category_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Category*',
                dropdownParent:$('#edit_incentive_form')
            });

            $('#edit_delivery_payment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Payment Type*',
                dropdownParent:$('#edit_incentive_form')
            });

            $('#edit_weight_range_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Weight Range*',
                dropdownParent:$('#edit_incentive_form')
            });

            $('body').on('click','button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.settings.hr.rider_incentive.details') !!}',
                        data: {
                            'id': id,
                        }
                    }).done(function (data) {
                        if (data.status === 0) {
                            $('#incentive_setting_id').val(id);
                            $('#edit_rider_category_select').val(data.details.rider_category_id).trigger('change');
                            $('#edit_delivery_payment_select').val(data.details.rider_shipment_payment_type_id).trigger('change');
                            $('#edit_weight_range_select').val(data.details.rider_shipment_weight_range_id).trigger('change');
                            $('#edit_incentive_form input.incentive_value').val(data.details.value);
                            $('#EditIncentiveModal').modal('show');

                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }


                    });
                }

            });


            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.fintech_company_charges.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Fintech Company Name');
                            head.push('Status');
                            head.push('Added by');
                            head.push('Added at');
                            head.push('Updated by');
                            head.push('Updated at');
                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.company_name);
                                row.push(values.status);
                                row.push(values.admin1);
                                row.push(values.created_at);
                                row.push(values.admin2);
                                row.push(values.updated_at);
                 
                             
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
                    @if (session('role_id') == 1 || in_array(852, session('permissions')))   
                    {
                    text: '<i class="la la-plus"></i> Add Fintech Company',
                            className: 'btn btn-primary add_material',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                window.location = "{{route('admin.settings.fintech_company_charges.form')}}";
                         }
                    
                        },
                    @endif  
                        
                    
                    {
                    extend: 'excel',
                    title: 'Fintech Companies List',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.settings.fintech_company_charges.list') }}',
                rowId: 'row_id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'company_name', name: 'company_name', class: 'align-middle company_name'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'admin1', name: 'admin1', class: 'align-middle admin'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'admin2', name: 'admin2', class: 'align-middle admin'},
                    {data: 'updated_at', name: 'updated_at', class: 'align-middle created_at'},
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
      
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
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
            $('#EditIncentiveModal').on('hidden.bs.modal', function() {
                $('#incentive_setting_id').val('');
                $('#edit_rider_category_select').val('').trigger('change');
                $('#edit_delivery_payment_select').val('').trigger('change');
                $('#edit_weight_range_select').val('').trigger('change');
                $('#edit_incentive_form input.incentive_value').val('');
            });
        });


    </script>
@endsection