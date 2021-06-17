@extends('admin.layout.master')

@section('title', 'FTL Request ('.str_pad($ftl->id,3, '0', STR_PAD_LEFT).')')
@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
                        FTL Request ({{str_pad($ftl->id, 3, '0', STR_PAD_LEFT)}})
                    </h1>
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('admin.inc.messages')
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li class="primary border-primary round"><a data-action="collapse">Legend <i class="ft-minus"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-content collapse">
                                                <div class="card-body p-1">
                                                    <h4 class=" info">Legend</h4>
                                                    <table class="table mb-0">
                                                        <tbody>
                                                            <tr style="color:#fff;" class="btn-purple">
                                                                <td class="align-middle">NOCR</td>
                                                            </tr>
                                                            <tr style="color:#fff;" class="btn-primary">
                                                                <td class="align-middle">Sales</td>
                                                            </tr>
                                                            <tr style="color:#fff;" class="btn-dark">
                                                                <td class="align-middle">Finance</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <table class="table table-bordered table-lg">
                                            <tbody class="list">
                                                <tr>
                                                    <th scope="row">Shipper</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">
                                                            @if($ftl->shipper_id == null)
                                                                {{$ftl->shipper_name}}
                                                            @else
                                                                {{$ftl->shipper}}
                                                            @endif
                                                            @if($ftl->status_id != 5)
                                                                <button data-toggle="modal" data-target="#EditShipperModal" class="btn btn-sm btn-info pull-right">Edit</button>
                                                            @endif
                                                        </h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Sales Person</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$ftl->sale_person}}</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Origin</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$ftl->origin}}</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Destination</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$ftl->destination}}</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Weight</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$ftl->weight}}</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Required Vehicle</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$ftl->vehicle}}</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Quantity</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$ftl->quantity}}</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Date</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{date('Y-m-d',strtotime($ftl->date))}}</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="row border-accent-2 justify-content-center">
                                            <div class="text-center col-12">
                                                <form id="update_ftl_request_form" method="post" action="{{route('admin.ftl.request.update.status',$ftl->id)}}">
                                                    @csrf
                                                    <div class="row mb-2">
                                                            <div class="col-6">
                                                                <label for="" class="pull-left font-weight-bold">Select Vendor</label>
                                                                <select name="vendor" id="vendor" class="form-control select2" data-rule-required="true" data-msg-required="Vendor is required">
                                                                    @foreach($vendors as $vendor)
                                                                        <option value="{{$vendor->id}}"> {{$vendor->name}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="freight_cost" class="pull-left font-weight-bold">Freight Cost</label>
                                                                <input type="text" name="freight_cost" id="freight_cost" value="{{$ftl->freight_cost}}" class="form-control to_calc_total_cost" placeholder="Freight Cost" data-rule-required="true" data-msg-required="Freight Cost is required" data-rule-min="0.1" data-msg-min="Freight Cost can not be less than 0.1">
                                                            </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4">
                                                            <input type="text" class="form-control" id="other_cost" placeholder="Other Cost">
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" id="other_cost_type" class="form-control" placeholder="Other Cost Type">
                                                        </div>
                                                        <div class="col-2">
                                                            <button type="button" id="add_other_cost" class="btn btn-info"><i class="fa fa-plus-circle"></i>Add</button>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                       <div class="col-8 offset-2">
                                                            <table class="table table-bordered table-lg" id="cost_table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Amount</th>
                                                                        <th>Cost Type</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                       </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <label for="" class="pull-left font-weight-bold">Total Cost</label>
                                                            <input type="text" readonly name="total_cost" class="form-control" id="total_cost" placeholder="Total Cost">
                                                        </div>
                                                        <div class="col-6">
                                                            <label for="freight_charges" class="pull-left font-weight-bold">Freight Charges</label>
                                                            <input type="text" name="freight_charges" id="freight_charges" value="{{$ftl->freight_charges}}" data-rule-required="true" data-msg-required="Freight Charges is required" class="form-control" placeholder="Freight Charges">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <label for="" class="pull-left font-weight-bold">GST</label>
                                                            <input type="text" name="gst" readonly class="form-control" id="gst" placeholder="GST">
                                                        </div>
                                                        <div class="col-6">
                                                            <label for="" class="pull-left font-weight-bold">Total Charges</label>
                                                            <input type="text" readonly name="total_charges" id="total_charges" class="form-control" placeholder="Total Charges">
                                                        </div>
                                                    </div>
                                                    @if($ftl->status_id != 5 && (session('role_id') == 1 || in_array(514,session('permissions'))))
                                                        <button type="submit" name="btn" value="Update" class="btn btn-secondary mr-1">
                                                            <span class="d-none d-lg-block">
                                                                Update
                                                            </span>
                                                        </button>
                                                    @endif
                                                    @if($ftl->status_id == 2 && (session('role_id') == 1 || in_array(515,session('permissions'))))
                                                        <button type="submit" name="btn" value="Approve" class="btn btn-success mr-1">
                                                            <span class="d-none d-lg-block">
                                                                Approve
                                                            </span>
                                                        </button>
                                                        <button type="submit" name="btn" value="Reject" class="btn btn-danger mr-1">
                                                            <span class="d-none d-lg-block">
                                                                Reject
                                                            </span>
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="content-body chat-application">
                                            <section
                                                    class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible">
                                                <div class="chats">
                                                    @if(!empty($comments))

                                                        @foreach($comments as $comment)
                                                                <div id="chat_{{$comment->id}}"
                                                                     class="chat {{($comment->comment_by == 1) ? 'operation' : '' }} {{($comment->comment_by == 2) ? 'finance' : '' }} ">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-admin">
                                                                            <i class="la la-user font-medium-2"></i>
                                                                            @if($comment->commenter_id == Auth::id())
                                                                                YOU
                                                                            @else
                                                                                {{$comment->commenter}}
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content text-left">
                                                                            <p>{!! $comment->comment !!}</p>
                                                                            <small>{{$comment->created_at}}</small>
                                                                            <div id="updated_by_div_{{$comment->id}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                        @endforeach
                                                    @endif

                                                </div>
                                            </section>


                                                <section class="chat-app-form">
                                                    <form class="chat-app-input row" id="chat_form">
                                                        <fieldset
                                                                class="form-group position-relative has-icon-left col-10 m-0">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>
                                                            <textarea id="chat_input" class="form-control height-200" placeholder="Type your message"></textarea>
                                                        </fieldset>
                                                        <div class="display-inline-block col-2">
                                                            <fieldset
                                                                    class="form-group has-icon-left m-0 mb-1">
                                                                <button  type="button" id="chat_send"
                                                                        class="btn btn-block btn-outline-success" ><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Send</span>
                                                                </button>
                                                            </fieldset>
                                                        </div>
                                                    </form>
                                                </section>
                                        </div>

                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-6 offset-3">
                                        <h3>Status History</h3>
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                <tr class="border-bottom-active border-custom-color">
                                                    <th>S No.</th>
                                                    <th>Status</th>
                                                    <th>Admin Name</th>
                                                    <th>Status Updated Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($ftl_status_history as $index => $history)
                                                    @php $index++; @endphp
                                                    <tr class="border-bottom-success border-custom-color">
                                                        <td>{{$index}}</td>
                                                        <td>{{$history->status}}</td>
                                                        <td>{{$history->admin}}</td>
                                                        <td>{{$history->updated_at}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    @if($ftl->status_id != 5)
        <div class="modal fade text-left" id="EditShipperModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditShipperModal"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white">Edit Shipper</h4>

                    </div>
                    <div class="modal-body  text-center">
                        <form id="edit_shipper_form" class="form" action="{{route('admin.ftl.request.update.shipper',$ftl->id)}}" method="post" novalidate="novalidate">
                            @csrf
                            <fieldset class="form-group">
                                <select name="shipper" id="shipper" class="form-control select2" data-rule-required="true" data-msg-required="Shipper is required">
                                    @foreach($shippers as $shipper)
                                        <option data-sale_person="{{$shipper->sale_person_id}}"  value="{{$shipper->id}}"> {{$shipper->name}} </option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <fieldset class="form-group">
                                <select class="form-control select2" name="sale_person" id="sale_person" data-rule-required="true" data-msg-required="Sale Person is required">
                                    @foreach($sale_persons as $sale_person)
                                        <option value="{{$sale_person->id}}">{{$sale_person->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="" type="button" class="btn btn-danger btn-block" data-dismiss="modal">Close</button>
                                </div>
                                <div class="col-3">
                                    <button type="submit" class="btn btn-primary btn-block">Update</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/chat-application.css')}}">
    <style>
        .half-margin {
            margin: 8px 0 0 0 !important;
        }

        .btn-purple {
            background-color: #ab45d7;
        }

        .chat-content p {
            word-break: break-word;
        }

        .chat-application .chat-app-window {
            padding: 20px 10px;
        }

        .badge.badge-admin {
            background-color: #edeef0;
            color: #000;
        }

        .badge.badge-substitute-user {
            background-color: deepskyblue;
        }

        .chat-application .chats .substitute-user .chat-body .chat-content {
            background-color: deepskyblue;
        }

        .chat-application .chats .chat-left .chat-content {
            text-align: left;
            float: left;
            margin: 0 0 10px 20px;
            color: #ffffff;
            background-color: #1e9ff2;
        }

        .chat-application .chats .chat-left .chat-content:before {
            border-right-color: #1e9ff2;
        }

        .chat-application .chats .chat-left.substitute-user .chat-content:before {
            border-right-color: deepskyblue;
        }

        .height-430 {
            height: 430px !important;
        }

        .table tr th, .table tr td {
            vertical-align: middle !important;
        }

        .chat-application .chats .operation .chat-content {
            color: #ffffff;
            background-color: #ab45d7;
        }

        .chat-application .chats .operation .chat-body .chat-content:before {
            border-left-color: #ab45d7;
        }
        .chat-application .chats .finance .chat-content {
            color: #ffffff;
            background-color: #18374A;
        }

        .chat-application .chats .finance .chat-body .chat-content:before {
            border-left-color: #18374A;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/ui/scrollable.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#edit_shipper_form #shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Shipper',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#EditShipperModal')
            }).bind('change', function () {
                var sale_person = $(this).find(':selected').attr('data-sale_person');
                if (sale_person != undefined) {
                    $('#edit_shipper_form #sale_person').val(sale_person).trigger('change');
                } else {
                    $('#edit_shipper_form #sale_person').val("").trigger('change');
                }

            });

            $('#edit_shipper_form #sale_person').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Sale Person',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#EditShipperModal')
            });


            $('#update_ftl_request_form #vendor').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Vendor',
                width: '100%',
                allowClear: true,
            });

            $('#update_ftl_request_form #freight_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
            });

            $('#update_ftl_request_form #other_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
            });

            $('#update_ftl_request_form #freight_charges').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
            });

            $("#update_ftl_request_form").validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.col-6'));
                },
            });

            $('#edit_shipper_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });


            $("#update_ftl_request_form #add_other_cost").on('click',function (){
                var other_cost = $('#update_ftl_request_form #other_cost').val();
                var other_cost_type = $('#update_ftl_request_form #other_cost_type').val();
                if(other_cost == '')
                {
                    toastr.error('Other Cost Can not be empty', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    return;
                }
                if(other_cost_type == '')
                {
                    toastr.error('Other Cost Type Can not be empty', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    return;
                }
                add_cost(other_cost,other_cost_type);
                calc_total_cost();

            });

            $(document).on('click',"#update_ftl_request_form #cost_table tbody .remove_cost",function (){
                $(this).closest("tr").remove();
                calc_total_cost();
            });

            $("#update_ftl_request_form #freight_cost").on('keyup',function (){
                calc_total_cost();
            });

            $("#update_ftl_request_form #freight_charges").on('keyup',function (){
               calc_gst();
            });

            @if($ftl->vendor_id != null)
                $("#update_ftl_request_form #vendor").val("{{$ftl->vendor_id}}").trigger('change');
            @endif

            @foreach($ftl_costs as $ftl_cost)
            add_cost("{{$ftl_cost->amount}}","{{$ftl_cost->cost_type}}");
            @endforeach

            calc_total_cost();

            calc_gst();

            function add_cost(other_cost,other_cost_type)
            {
                var html = "<tr>" +
                    "<td>" + other_cost + "<input type='hidden' name='other_cost[]' value='"+other_cost+"' class='to_calc_total_cost'> </td>" +
                    "<td>" + other_cost_type + "<input type='hidden' name='other_cost_type[]' value='"+other_cost_type+"'></td>" +
                    "<td><button type='button' class='btn btn-danger btn-sm remove_cost'><i class='la la-close'></i></button></td>" +
                    "</tr>";

                $("#update_ftl_request_form #cost_table tbody").append(html);
                $('#update_ftl_request_form #other_cost').val('');
                $('#update_ftl_request_form #other_cost_type').val('');
            }

            function calc_total_cost()
            {
                var total_cost = 0;
                $(".to_calc_total_cost").each(function (){
                    if(!Number.isNaN(parseFloat($(this).val()))) {
                        total_cost += parseFloat($(this).val());
                    }
                });
                $("#update_ftl_request_form #total_cost").val(total_cost);
            }

            function calc_gst()
            {
                var gst = "{{$ftl->gst ?? 0}}";
                var freight_charges = parseFloat($("#update_ftl_request_form #freight_charges").val());
                if(Number.isNaN(freight_charges))
                {
                    freight_charges = 0;
                }
                var gst_calc = gst * freight_charges;
                $("#update_ftl_request_form #gst").val(gst_calc);
                cal_total_charges(gst_calc,freight_charges);
            }

            function cal_total_charges(gst,freight_charges)
            {
                $("#update_ftl_request_form #total_charges").val(gst + freight_charges);
            }

            $('#chat_form').on('submit', function (e) {
                e.preventDefault();
            });

            $('#chat_send').on('click',function (){
                var flag = true;
                var comment = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');
                $('#chat_input').val('');
                var request_id = '{{$ftl->id}}';

                if (comment == '') {
                    flag = false;
                    toastr.error("Please Enter Comment first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if (flag) {
                    $.ajax({
                        url: '{!! route('admin.ftl.request.comment.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment': comment,
                            'request_id': request_id,
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            get_latest_comment(request_id);
                            updateScroll();
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            });

            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
            });

            function updateScroll() {
                const container = document.querySelector('.chat-app-window');
                container.scrollTop = $('.chat-app-window')[0].scrollHeight;
            }

            function get_latest_comment(request_id) {
                    $.ajax({
                        url: '{!! route('admin.ftl.request.comment.get') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'request_id': request_id
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            html = "";
                            $(data.comments).each(function (i,comment){
                                let type = '';
                                let commenter = '';
                                if(comment.comment_by == 1)
                                {
                                    type = "operation";
                                }
                                else if(comment.comment_by == 2)
                                {
                                    type = "finance";
                                }
                                else{
                                    type = "";
                                }
                                if(comment.commenter_id == {{Auth::id()}})
                                    commenter = "YOU";
                                else {
                                    commenter = comment.commenter;
                                }
                                html += `<div id="chat_${comment.id}" class="chat ${type} ">
                                                <div class="chat-avatar">
                                                     <div class="badge block badge-admin">
                                                        <i class="la la-user font-medium-2"></i>${commenter}
                                                     </div>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content text-left">
                                                        <p>${comment.comment}</p>
                                                        <small>${comment.created_at}</small>
                                                    </div>
                                                </div>
                                          </div>`;
                            });
                            $('section.chat-app-window .chats').html(html);
                            updateScroll();
                        }
                    });
            }

            setInterval(function () {
                var request_id = '{{$ftl->id}}';
                get_latest_comment(request_id);
            }, 10000);

        });
    </script>
@endsection