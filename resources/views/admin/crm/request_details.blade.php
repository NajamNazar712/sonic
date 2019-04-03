@extends('admin.layout.master')

@section('title', 'Request Details')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
{{--                        {{dd($tag_permission)}}--}}
                        Request Details ({{str_pad($crm_details->id, 6, '0', STR_PAD_LEFT)}})
                        <div class="text-right mb-1">
                            @if($crm_details['status_id'] == 2 && (session('role_id') == 1 || $crm_details->agent['id'] == Auth::id() || in_array(185, session('permissions'))))
                                <button type="button" class="btn btn-primary width-10-per" id="tag"><span class="d-none d-lg-block" style="color: white">Tag</span></button>
                            @endif

                        </div>
                    </h1>
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('admin.inc.messages')
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <table class="table table-bordered table-lg">
                                            <tbody class="list">
                                            <tr>
                                                <th scope="row">Tracking Number</th>
                                                <td class="name">

                                                    @if(!empty($crm_details->shipment_id))
                                                    <h5 class="mb-0">{{$crm_details->shipment->tracking_number}}</h5>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Case Nature</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$crm_details->nature->name}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Case Nature Type</th>
                                                <td class="name">
                                                    @if(!empty($crm_details->case_nature_type_id))
                                                    <h5 class="mb-0">{{$crm_details->nature_type->type}}</h5>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Channel</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$crm_details->channel->channel}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Status</th>
                                                <td class="name" id="status">
                                                    <h5 class="mb-0">{{$crm_details->request_status->name}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Launched By</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$launched_by}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Launched Date</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$crm_details->created_at}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Agent</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$agent}}</h5>
                                                </td>
                                            </tr>
                                            @if($crm_details['status_id'] == 2)
                                                <tr>
                                                    <th scope="row">Tagged To</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$tagged_name}}</h5>
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th scope="row">Description</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$crm_details->description}}</h5>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')) || (($tag_check['crm_request_tagging_type_id'] == 1 && $tag_check['tagged_id'] == $tag_permission) || ($tag_check['crm_request_tagging_type_id'] == 2 && $tag_check['tagged_id'] == Auth::id())))
                                            <div class="text-center">
                                                <form id="valid_invalid">
                                                    <input type="hidden" id="req_id" value="{{$crm_details->id}}">
                                                    <input type="hidden" id="prev_status" value="{{$crm_details->status_id}}">
                                                    @if($crm_details['status_id'] != 3)
                                                        @if($crm_details['status_id'] == 1 ||$crm_details['status_id'] == 5)
                                                            <button id="valid" type="submit" class="btn btn-success mr-1 width-20-per" >
                                                                    <span class="d-none d-lg-block">
                                                                        Valid
                                                                    </span>
                                                            </button>
                                                        @elseif($crm_details['status_id'] == 2)
                                                            @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')) || (($tag_check['crm_request_tagging_type_id'] == 1 && $tag_check['tagged_id'] == $tag_permission) || ($tag_check['crm_request_tagging_type_id'] == 2 && $tag_check['tagged_id'] == Auth::id())))
                                                                <button id="valid" type="submit" class="btn btn-success mr-1 width-20-per" >
                                                                        <span class="d-none d-lg-block">
                                                                        Resolve
                                                                        </span>
                                                                </button>
                                                            @endif
                                                        @elseif($crm_details['status_id'] == 4|| in_array(186, session('permissions')))
                                                            <button id="valid" type="submit" class="btn btn-success mr-1 width-20-per" >
                                                                    <span class="d-none d-lg-block">
                                                                    Re-Open
                                                                    </span>
                                                            </button>
                                                            @endif
                                                        @endif
                                                        @if($crm_details['status_id'] != 4 && $crm_details['status_id'] != 2)
                                                            <button id="invalid" type="submit" class="btn btn-danger width-20-per" >
                                                        <span class="d-none d-lg-block">
                                                            @if($crm_details['status_id'] == 1 ||$crm_details['status_id'] == 5)
                                                                Invalid
                                                            @else
                                                                Close
                                                            @endif
                                                            </span>
                                                            </button>
                                                        @endif
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-7">
                                        <div class="content-body chat-application">
                                            <section class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible"  >
                                                <div class="chats">
                                                    @if(!empty($comments))

                                                        @foreach($comments as $comment)
                                                            @if($comment->comment_by == 0)
                                                                <div id="chat_{{$comment->id}}" class="chat admin {{($comment->comment_type == 1)? 'internal':'' }}">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-admin">
                                                                            <i class="la la-user font-medium-2"></i>{{$comment->admin->name}}
                                                                        </div>
                                                                    </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content">
                                                                            <p>{{$comment->comment}}</p>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            @elseif($comment->comment_by == 1)
                                                                <div class="chat chat-left shipper">

                                                                        <div class="chat-avatar">
                                                                            <div class="badge block badge-info">
                                                                                <i class="la la-user font-medium-2"></i>Shipper
                                                                            </div>
                                                                        </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content">
                                                                            <p>{{$comment->comment}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="chat chat-left substitute-user">

                                                                        <div class="chat-avatar">
                                                                            <div class="badge block badge-substitute-user">
                                                                                <i class="la la-user font-medium-2"></i>Shipper
                                                                            </div>
                                                                        </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content">
                                                                            <p>{{$comment->comment}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </div>
                                            </section>

                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()))
                                            <section class="chat-app-form">
                                                <form class="chat-app-input d-flex" id="chat_form">
                                                    <fieldset class="form-group position-relative has-icon-left col-8 m-0">
                                                        <input type="hidden" id="last_comment_id" value="{{$last_comment_id}}">
                                                        <div class="form-control-position">
                                                            <i class="la la-chevron-right"></i>
                                                        </div>
                                                        <input type="text" class="form-control" id="chat_input" placeholder="Type your message">
                                                    </fieldset>
                                                    <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                                                        <button id="chat_send" type="button" class="btn btn-block btn-purple chat_send" to="1"><i class="la la-paper-plane-o d-lg-none"></i>
                                                            <span class="">Internal</span>
                                                        </button>
                                                    </fieldset>
                                                    <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                                                        <button id="chat_send" type="button" class="btn btn-block btn-default chat_send" to="0" ><i class="la la-paper-plane-o d-lg-none"></i>
                                                            <span class="">Shipper</span>
                                                        </button>
                                                    </fieldset>
                                                </form>
                                            </section>
                                                @elseif(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ))))
                                                <section class="chat-app-form">
                                                    <form class="chat-app-input d-flex" id="chat_form">
                                                        <fieldset class="form-group position-relative has-icon-left col-8 m-0">
                                                            <input type="hidden" id="last_comment_id" value="{{$last_comment_id}}">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>
                                                            <input type="text" class="form-control" id="chat_input" placeholder="Type your message">
                                                        </fieldset>
                                                        <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                                                            <button id="chat_send" type="button" class="btn btn-block btn-purple chat_send" to="1"><i class="la la-paper-plane-o d-lg-none"></i>
                                                                <span class="">Internal</span>
                                                            </button>
                                                        </fieldset>
                                                        <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                                                            <button id="chat_send" type="button" class="btn btn-block btn-default chat_send" to="0" ><i class="la la-paper-plane-o d-lg-none"></i>
                                                                <span class="">Shipper</span>
                                                            </button>
                                                        </fieldset>
                                                    </form>
                                                </section>
                                            @endif

                                        </div>

                                    </div>
                                </div>

                                @if(count($crm_agent_history) > 0)
                                    <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h3>Agent History</h3>
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                <tr class="border-bottom-active border-custom-color">
                                                    <th>S No.</th>
                                                    <th>Agent Name</th>
                                                    <th>Agent Assigned Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($crm_agent_history as $index => $history)
                                                    @php $index++; @endphp
                                                <tr class="border-bottom-success border-custom-color">
                                                    <td>{{$index}}</td>
                                                    <td>{{$history->agent->name}}</td>
                                                    <td>{{$history->created_at}}</td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(count($crm_status_history) > 0)
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3>Status History</h3>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                    <tr class="border-bottom-active border-custom-color">
                                                        <th>S No.</th>
                                                        <th>Status Name</th>
                                                        <th>Agent</th>
                                                        <th>Status Assigned Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($crm_status_history as $index => $status_history)
                                                        @php $index++; @endphp
                                                        <tr class="border-bottom-success border-custom-color">
                                                            <td>{{$index}}</td>
                                                            <td>{{$status_history->status->name}}</td>
                                                            @if($status_history->agent_id != null)
                                                                <td>{{$status_history->agent->name}}</td>
                                                            @else
                                                                <td>-</td>
                                                            @endif
                                                            <td>{{$status_history->created_at}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(count($crm_tagging_history) > 0)
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3>Tagging History</h3>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                    <tr class="border-bottom-active border-custom-color">
                                                        <th>S No.</th>
                                                        <th>Name</th>
                                                        <th>Tagged Type</th>
                                                        <th>Tagged Date</th>
                                                        <th>Tagged By</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($crm_tagging_history as $index => $tagging_history)
                                                        @php $index++; @endphp
                                                        <tr class="border-bottom-success border-custom-color">
                                                            <td>{{$index}}</td>
                                                            @if($tagging_history->crm_request_tagging_type_id == 1)
                                                                <td>{{$tagging_history->department->name}}</td>
                                                                <td>{{$tagging_history->tagging->name}}</td>
                                                                @else
                                                                <td>{{$tagging_history->user->name}}</td>
                                                                <td>{{$tagging_history->tagging->name}}</td>
                                                            @endif
                                                            <td>{{$tagging_history->created_at}}</td>
                                                            <td>{{$tagging_history->agent->name}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <section>
        <div class="modal fade text-left" id="tagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="tagModal"
             aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Tag</h4>
                    </div>
                    <div class="modal-body text-center">
                        <form id="tag_submit_form" method="post">
                            @method('POST')
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-11">
                                    <fieldset class="form-group">
                                        <input type="hidden" id="crm_request_id" value="{{$crm_details->id}}">
                                        <select name="tag_type" id="tag_type" class="form-control select2">
                                            @foreach($types as $type)
                                                <option value="{{$type->id}}" > {{$type->name}} </option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <div class="d-none" id="admin_tag_div">
                                            <select name="tag_admin" id="tag_admin" class="form-control select2">
                                                @foreach($admins as $admin)
                                                    <option value="{{$admin->id}}" > {{$admin->name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-none" id="department_tag_div">
                                            <select name="tag_department" id="tag_department" class="form-control  select2">
                                                @foreach($departments as $department)
                                                    <option value="{{$department->id}}" > {{$department->name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success width-25-per" id="tag_adminSubmit">Tag</button>
                        <button type="button" class="btn btn-info width-25-per" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/chat-application.css')}}">
    <style>
        .half-margin{
            margin: 8px 0 0 0 !important;
        }
        .btn-purple{
            background-color: #ab45d7;
        }
        .chat-content p{
            word-break: break-word;
        }
        .chat-application .chat-app-window {
            padding: 20px 10px;
        }
        .badge.badge-admin{
            background-color: #edeef0;
            color:#000;
        }
        .badge.badge-substitute-user{
            background-color: deepskyblue;
        }
        .chat-application .chats .substitute-user .chat-body .chat-content{
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
        .chat-application .chats .admin .chat-content {
            color: #000000;
            background-color: #edeef0;
        }
        .chat-application .chats .admin .chat-body .chat-content:before {
            border-left-color: #edeef0;
        }
        .height-430 {
            height: 430px !important;
        }
        .table tr th, .table tr td {
            vertical-align: middle !important;
        }
        .chat-application .chats .admin.internal .chat-content {
            color: #ffffff;
            background-color: #ab45d7;
        }
        .chat-application .chats .admin.internal .chat-body .chat-content:before {
            border-left-color: #ab45d7;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/ui/scrollable.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#valid').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{!! route('admin.crm.valid') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': $('#req_id').val(),
                        'prev_status' : $('#prev_status').val()
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            if (data.marked_status == 2) {
                                $('#status').html('<h4>In-Process</h4>');
                            }
                            else if (data.marked_status == 3) {
                                $('#status').html('<h4>Resolved</h4>');
                            }
                            else if (data.marked_status == 5) {
                                $('#status').html('<h4>Re-Open</h4>');
                            }

                            toastr.success(data.success, 'Marked!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            setTimeout(function(){
                                window.location.reload();
                            }, 1000);
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                });

            $('#invalid').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{!! route('admin.crm.invalid') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': $('#req_id').val(),
                        'prev_status' : $('#prev_status').val()
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            $('#status').html('<h4>Closed</h4>');
                            toastr.success(data.success, 'Marked!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            setTimeout(function(){
                                window.location.reload(1);
                            }, 1000);
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                });

            $("#tag_admin").prepend('<option value="" selected></option>').select2({
                placeholder: "Select User",
                width:'100%',
                dropdownParent:$('#tagModal')
            });

            $("#tag_department").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department",
                width:'100%',
                dropdownParent:$('#tagModal')
            });

            $("#tag_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Type",
                width:'100%',
                dropdownParent:$('#tagModal')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#admin_tag_div').addClass('d-none');
                    $('#department_tag_div').removeClass('d-none');
                }else if(id === 2){
                    $('#department_tag_div').addClass('d-none');
                    $('#admin_tag_div').removeClass('d-none');
                }else{
                    $('#admin_tag_div').addClass('d-none');
                    $('#department_tag_div').addClass('d-none');
                }
            });
            $('#tag').on('click', function (e) {
                e.preventDefault();
                $('#tagModal').modal('show');
            });
            $('#tagModal').on('hide.bs.modal', function (e) {
                $('#tag_type').val('').trigger('change');
                $('#admin_tag_div').addClass('d-none');
                $('#department_tag_div').addClass('d-none');
            });
            $('#tag_adminSubmit').on('click',function () {
                var type = parseInt($('#tag_type').val());
                if(type === 1) {
                    var tag = parseInt($('#tag_department').val());
                }
                else if(type === 2){
                    var tag = parseInt($('#tag_admin').val());
                }
                if(tag){
                    $.ajax({
                        url: '{!! route('admin.crm.tag') !!}',
                        method: 'POST',
                        data: {
                            'tagged_id': tag,
                            'crm_request_id': $('#crm_request_id').val(),
                            'crm_request_tagging_type_id': type,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if(data.status == 0){
                                $('#tagModal').modal('hide');
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                setTimeout(function(){
                                    window.location.reload(1);
                                }, 2500);
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
                else{
                    if(type === 1) {
                        var error = "Department Not Selected!";
                    }
                    else if(type === 2) {
                        var error = "User Not Selected!";
                    }
                    else{
                        error = "Type Not Selected!";
                    }
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            });

            $('#chat_form').on('submit',function (e) {
                e.preventDefault();
            });
            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
            });
            $('.chat_send').on('click', function () {
                var flag = true;
                var comment = $('#chat_input').val();
                var request_id = '{{$crm_details->id}}';
                var internal_switch_check = document.querySelector('.switchery.on-internal-chat');
                var internal_switch = parseInt($(this).attr('to'));
                var internal_class = '';
                if(internal_switch){
                    internal_class = 'internal';
                }else{
                    internal_class = '';
                }
                if(comment == ''){
                    flag = false;
                    toastr.error("Please Enter Comment first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if(flag){
                    $.ajax({
                        url: '{!! route('admin.crm.comment.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment': comment,
                            'request_id':request_id,
                            'internal_switch': internal_switch
                        }
                    }).done(function (data) {
                        if(data.status){
                            // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            var user = '{{Auth::user()->name}}';
                            // if($('div.chat:last-child').hasClass('admin')) {
                            //     var html = '<div class="chat-content"><p>' + comment + '</p></div>';
                            //     $('div.chat:last-child').find('.chat-body').append(html);
                            // }else{
                            if(internal_switch){
                                var html = '<div class="chat admin '+ internal_class +'"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content"><p>' + comment + '</p></div></div></div>';
                            }else{
                                var html = '<div class="chat admin"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content"><p>' + comment + '</p></div></div></div>';
                            }
                            $('section.chat-app-window .chats').append(html);

                            // }


                            $('#chat_input').val('');
                            $('#last_comment_id').val(data.last_comment_id);

                            updateScroll();
                        }
                    });
                }
            });
            @if($crm_details->status_id != 4)
            setInterval(function () {
                var last_comment_id = parseInt($('#last_comment_id').val());
                var request_id = '{{$crm_details->id}}';
                get_latest_comment(last_comment_id,request_id);
            },10000);
            @endif
            function get_latest_comment(comment_id,request_id) {
                if(comment_id){
                    $.ajax({
                        url: '{!! route('admin.crm.comment.get') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment_id':comment_id,
                            'request_id': request_id
                        }
                    }).done(function (data) {
                        if(data.status){
                            var user = data.comment.comment_by;
                            var name = data.name;
                            if(user == 0){
                                if(data.comment.comment_type == 0){

                                    var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>'+ name +'</div></div><div class="chat-body"><div class="chat-content"><p>' + data.comment.comment + '</p></div></div></div>';
                                    }else{
                                        var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content"><p>' + data.comment.comment + '</p></div></div></div>';
                                    }
                                    $('section.chat-app-window .chats').append(html);

                            }else if(user == 1){
                                if($('div.chat:last-child').hasClass('shipper')) {
                                    var html = '<div class="chat-content"><p>' + data.comment.comment + '</p></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                }else{
                                    var html = '<div class="chat chat-left shipper"><div class="chat-avatar"><div class="badge block badge-info"><i class="la la-user font-medium-2"></i>'+ name +'</div></div><div class="chat-body"><div class="chat-content"><p>' + data.comment.comment + '</p></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }
                            }else{
                                if($('div.chat:last-child').hasClass('substitute-user')) {
                                    var html = '<div class="chat-content"><p>' + data.comment.comment + '</p></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                }else{
                                    var html = '<div class="chat chat-left substitute-user"><div class="chat-avatar"><div class="badge block badge-substitute-user"><i class="la la-user font-medium-2"></i>'+ name +'</div></div><div class="chat-body"><div class="chat-content"><p>' + data.comment.comment + '</p></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }

                            }
                            $('#last_comment_id').val(data.comment.id);
                            updateScroll();
                        }
                    });
                }
            }
            function updateScroll(){
                const container = document.querySelector('.chat-app-window');
                container.scrollTop = $('.chat-app-window')[0].scrollHeight;

            }
            updateScroll();

            // setInterval(function () {
            //     window.location.reload();
            // }, 500000);
        });

    </script>
@endsection