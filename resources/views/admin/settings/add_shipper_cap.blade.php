@extends('admin.layout.master')

@section('title', 'Shipper Cap')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Shipper Cap
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Cap Limit</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="SubmitShipperCapModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SubmitShipperCapModal"
         aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">New Shipper Cap</h4>
                    </div>
                    <form method="post" id="submit_shipper_cap_form" action="{{route('admin.settings.shipper_cap.store')}}">
                        @csrf
                        <div class="modal-body">
                            
                            <div class="form-group">
                                <input type="text" name="shipper_cap_limit" id="shipper_cap_limit" class="form-control" placeholder="Shipper Cap Limit*" data-rule-required="true" data-msg-required="Shipper Cap is required" >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="submit_referral">Submit</button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Modal --}}

         <div class="modal fade text-left" id="EditShipperCapModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditShipperCapModal"
         aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">Edit Shipper Cap</h4>
                    </div>
                    <form method="post" id="edit_shipper_cap_form" action="{{route('admin.settings.shipper_cap.update')}}">
                        @csrf
                        <div class="modal-body">
                            
                            <input type="hidden" name="edit_shipper_cap_id" id="edit_shipper_cap_id">
                            <div class="form-group">
                                <input type="number" name="edit_shipper_cap_limit" id="edit_shipper_cap_limit" class="form-control" placeholder="Shipper Cap Limit*" data-rule-required="true" data-msg-required="Shipper Cap is required" >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="submit_referral">Update</button>
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
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            // $('#SubmitReferralModal').on('hidden.bs.modal', function () {
                
            //     $('#referral_code').val('');
                
            // });


              var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                        
                    @if (session('role_id') == 1 || in_array(942, session('permissions')) )
                    
                    {
                        text: '<i class="la la-plus"></i> Add',
                        className: 'btn btn-primary add_shipper_cap_btn',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#SubmitShipperCapModal').modal('show');
                            
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
                ajax: '{{ route('admin.settings.shipper_cap.list') }}',
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'setting_value', name: 'general_settings.setting_value', class: 'align-middle setting_value'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    // var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    // var departments_select = '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';
             
                   
                    if(this.api().rows().count() == 0){
                        $('.add_shipper_cap_btn').show();
                    }else {
                        $('.add_shipper_cap_btn').remove();
                    }
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        // if ($(header).is('.serial_number') || $(header).is('.action')) {
                        //     $(td).appendTo($(search));
                        // }
                        // else if($(header).is('.department')){
                        //     $(departments_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }else if ($(header).is('.status')) {
                        //     $(status).appendTo($(search))
                        //         .on('change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         }).wrap(td);
                        // }
                        // else {
                        //     var current = $(input).appendTo($(search)).on('change', function() {
                        //         column.search($(this).val(), false, false, true).draw();
                        //     }).wrap(td).after(icon);

                        //     if (column.search()) {
                        //         current.val(column.search());
                        //     }
                        // }
                    });
                    

                    // $('#status').prepend('<option value="" selected></option>').select2({
                    //     placeholder: "Select Status",
                    //     width:'100%',
                    //     containerCssClass: 'select-xs',
                    //     dropdownCssClass: 'form-control-sm p-0'
                    // });

                    this.api().table().columns.adjust();
                }
              });
            


            $('body').on('click','.edit_shipper_cap_btn',function(){
                var shipper_cap_id = $(this).data('target-id');

                  $.ajax({
                            url: '{!! route('admin.settings.shipper_cap.edit') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipper_cap_id': shipper_cap_id,
                            }
                    }).done(function(data){
                        if(data.status==0)
                        {
                            $("#edit_shipper_cap_id").val(data.shipper_cap.id);
                            $("#edit_shipper_cap_limit").val(data.shipper_cap.setting_value);
                            $("#EditShipperCapModal").modal('show');

                        }else{
                            	
			                scan_sound(2);
                            toastr.error('Something went wrong', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            });


            
            $( "#submit_shipper_cap_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
            });

              $("#edit_shipper_cap_form" ).validate({
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