@extends('admin.layout.master')

@section('title', 'Set Commission')

@section('content')
    <h1>Set Commission</h1>

       <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row shipperhead mt-2">
                    <h5>Commission Set For : {{$user_names}} </h5>
                    </div>
                   
                    <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.settings.commission.set_commission.submit')}}" method="post" novalidate="novalidate">
                    @csrf
                    <input type="hidden" name="user_ids" value="{{ $ids }}"/>
                    
                            <div class="row justify-content-center mt-2" id="commission_div">
                            
                                <div class="form-group row">
                                    <label class="col-md-4 label-control" for="commission">Total Commission</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Total Commission" id="commission_max" name="commission_max" value="{{$commission_percentage}}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div id="add_user_commission_form" class="form mb-1 justify-content-center">
                                        <div class="row justify-content-center">
                                            <div class="col-2 form-group">
                                                <select name="sales_tier" class="select2" id="sales_tier_select" data-rule-required="true" data-msg-required="Sales Tier is required">
                                                    @foreach($sales_tiers as $tier)
                                                        <option value="{{ $tier->id }}" type="{{$tier->tier_type}}" sales="{{$tier->sales_status}}">{{ $tier->tier_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-3 form-group">
                                                <input type="text" id="external_person_name" name="external_person_name" class="form-control" placeholder="External Tier Person Name" disabled data-rule-required="true" data-msg-required="Person Name is required">
                                            </div>
                                            <div class="col-2 form-group">
                                                <select name="user" class="select2" id="user_select" data-rule-required="true" data-msg-required="User is required" disabled>
                                                </select>
                                            </div>
                                            <div class="col-3 form-group">
                                                <div class="input-group form-group">
                                                    <input type="text" id="user_commission" class="form-control commission" placeholder="User Commission" name="user_commission" data-rule-required="true" data-msg-required="User Commission is required">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-1 form-group">
                                                <button type="button" class="btn btn-primary" id="commission_add_button">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                        <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">User Name</th>
                                            <th class="border-primary border-darken-1">Tier</th>
                                            <th class="border-primary border-darken-1">Commission Percentage</th>
                                            <th class="border-primary border-darken-1"></th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <input type="hidden" value="0" name="total_commission" id="total_commission">
                                        <tr><th colspan="3" style="text-align:right" rowspan="1">Total Commission:</th><th rowspan="1" colspan="2"><span id="total_commission_value">0</span>%</th></tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="text-center mt-2">
                                    <div class="form-group">

                                        <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Submit</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                  </div>
            </div>
        </div> 

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        .hide{
            display:none;
        }
        .shipperhead{
            padding-left:30px;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
         $('#addRatesSubmit').on('click', function () {
            $('#add_user_commission_form').remove();
        });
        $(document).ready(function () {
            //sales tier
            /* *********************
                Move this block before submit and remove add form on submit
             ****************/
       
            var selected_users = [];
            var users = @json($users);

            var users_data = $.map(users, function (obj) {
                obj.id = obj.id || obj.text;
                return obj;
            });
            $('#user_select').prepend('<option value="" selected></option>').select2({
                placeholder: "Select User",
                width:'100%'
            }).bind('change', function () {
                var id = $(this).val();

                var index = $.inArray(id, selected_users);
                if (index !== -1) {
                    var error = 'User previously selected!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    $('#user_select').val(null).trigger('change');
                }
            });
            $('#user_select').select2({data:users_data,placeholder:'Select User'});

            $('#sales_tier_select').prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Tier",
                width:'100%'
            }).bind('change', function () {
                $('#user_select').attr('disabled', true);
                $('#external_person_name').attr('disabled', true);
                var type = $(this).find(":selected").attr('type');
               if(type == 1){
                   $('#user_select').attr('disabled', false);
               }else{
                   $('#external_person_name').attr('disabled', false);
               }

            });

            var commission_max = $('#commission_max').val();
            $('.commission').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': commission_max,
            });

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                paging: false,
                ordering:false,
                sorting:false,
                bInfo:false,
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    { name: 'user_name', class: 'align-middle user_name'},
                    { name: 'tier', class: 'align-middle tier'},
                    { name: 'commission_percentage', class: 'align-middle commission_percentage'},
                    { name: 'action', class: 'align-middle action'}

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
            });

            $('body').on('change','#external_person_name',function() {
                $(this).val($(this).val().trim());
            });

            var row = 1;
            var selected_commission = 0;
            function add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission){
                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove"><i class="la la-close"></i></a>';
                var tier = '<div><input type="hidden" name="tier_id['+ row +']"  value="'+ tier_id +'">'+ tier_name +'</div>';
                if(tier_type == 1){
                    var name = '<div><input type="hidden" name="user_id['+ row +']"  value="'+ user_id +'">'+ user_name +'</div>';
                }else{
                    var name = '<div><input type="hidden" name="user_id['+ row +']"  value="'+ user_name +'">'+ user_name +'</div>';
                }
                var commission_percentage = '<div><input type="hidden" name="commission_percentage['+ row +']"  value="'+ commission +'">'+ commission +'%</div>';
                table.row.add([row,name,tier,commission_percentage,remove]).node().id = row;
                table.draw(false);
                if(tier_type == 1){
                    selected_users.push(user_id);
                }
                $('#commission_add_button').attr('disabled', false);
                $('#total_commission_value').html(selected_commission);
                $('#total_commission').val(selected_commission);
                row++;
            }
            function roundToTwo(num) {
                return +(Math.round(num + "e+2")  + "e-2");
            }
            $('#commission_add_button').on('click', function () {
                var commission = parseFloat($('#user_commission').val());
                var this_btn = $(this);

                var flag = true;
                var type = $('#sales_tier_select').find(":selected").attr('type');
                // if(!$('#sales_tier_select').valid()){
                //     flag = false;
                // }
                // if(type == 1){
                //     if(!$('#user_select').valid()){
                //         flag = false;
                //     }
                // }
                // if(type == 2){
                //     if(!$('#external_person_name').valid()){
                //         flag = false;
                //     }
                // }
                // if(!$('#user_commission').valid()){
                //     flag = false;
                // }

                if(flag){
                    if(commission <= commission_max){
                        selected_commission = roundToTwo(selected_commission + commission);
                        commission_max = commission_max - commission;
                        this_btn.attr('disabled', true);
                        var user_id = '';
                        var user_name = '';
                        var tier_id = '';
                        var tier_name = '';
                        var tier_type = '';
                        tier_id = $('#sales_tier_select').val();
                        tier_name = $('#sales_tier_select').find(":selected").text();
                        tier_type = $('#sales_tier_select').find(":selected").attr('type');
                        if(tier_type == 1){
                            user_id = $('#user_select').val();
                            user_name = $('#user_select').find(":selected").text();
                        }else{
                            user_name = $('#external_person_name').val();
                        }

                        add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission);
                        $('#sales_tier_select').val(null).trigger('change');
                        $('#user_select').val(null).trigger('change');
                        $('#user_select').attr('disabled', true);
                        $('#external_person_name').val('');
                        $('#external_person_name').attr('disabled', true);
                        $('#user_commission').val('');

                    }else{
                        var error = 'Selected Commission value exceeds!';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });
            $('#datatable tbody').on('click', 'tr td.action a.remove', function() {
                var id = $(this).parents('tr').attr('id');

                var user_id = $('input[name="user_id['+ id +']"]').val();
                if(user_id){
                    var index = $.inArray(user_id, selected_users);
                    if (index !== -1) {
                        selected_users.splice(index, 1);
                    }
                }
                var commission =  parseFloat($('input[name="commission_percentage['+ id +']"]').val());
                commission_max = roundToTwo(commission_max + commission);
                selected_commission = roundToTwo(selected_commission - commission);
                $('#total_commission_value').html(selected_commission);
                $('#total_commission').val(selected_commission);
                table.row( $(this).parents('tr') ).remove().draw();
            });
            //sales tier end

        });

</script>
    @endsection            