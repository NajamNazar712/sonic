@extends('client.layout.master')

@section('title', 'Request Details')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
                        Request Details ({{str_pad($crm_details->id, 6, '0', STR_PAD_LEFT)}})
                    </h1>

                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('client.inc.messages')
                                <div class="row">
                                    <div class="col-6">
                                        <table class="table table-bordered table-lg">
                                            <tbody class="list">
                                                <tr>
                                                    <th scope="row">Tracking Number</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$crm_details->shipment->tracking_number}}</h5>
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
                                                        <h5 class="mb-0">{{$crm_details->nature_type->type}}</h5>
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
                                                    <td class="name">
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
                                                    <th scope="row">Description</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">{{$crm_details->description}}</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                            <div class="content-body chat-application">
                                                <section class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible" style="height: 400px; overflow-y: hidden;" >
                                                    <div class="chats">
                                                        @if(!empty($comments))
                                                            @php
                                                            $shipper_flag = true;
                                                            $admin_flag = true;
                                                            $sub_flag = true;
                                                            @endphp
                                                        @foreach($comments as $comment)
                                                            @if($comment->comment_by == 0)
                                                                <div class="chat chat-left admin">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block {{($admin_flag)? 'badge-admin':''}}">
                                                                            <i class="la la-user font-medium-2"></i>Agent
                                                                        </div>
                                                                    </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content">
                                                                            <p>{{$comment->comment}}</p>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                    @php
                                                                        $admin_flag = false;
                                                                        $shipper_flag = true;
                                                                        $sub_flag = true;
                                                                    @endphp
                                                                @elseif($comment->comment_by == 1)
                                                                <div class="chat shipper">
                                                                    @if($shipper_flag)
                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-info">
                                                                            <i class="la la-user font-medium-2"></i>You
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                    <div class="chat-body">
                                                                        <div class="chat-content {{($shipper_flag == false)? 'mr-3':'' }}">
                                                                            <p>{{$comment->comment}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                    @php
                                                                        $admin_flag = true;
                                                                        $shipper_flag = false;
                                                                        $sub_flag = true;
                                                                    @endphp
                                                                @else
                                                                    <div class="chat substitute-user">
                                                                        @if($shipper_flag)
                                                                            <div class="chat-avatar">
                                                                                <div class="badge block badge-substitute-user">
                                                                                    <i class="la la-user font-medium-2"></i>You
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="chat-body">
                                                                            <div class="chat-content {{($sub_flag == false)? 'mr-3':'' }}">
                                                                                <p>{{$comment->comment}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                        $admin_flag = true;
                                                                        $shipper_flag = true;
                                                                        $sub_flag = false;
                                                                    @endphp
                                                            @endif
                                                        @endforeach
                                                        @endif

                                                    </div>
                                                </section>
                                                <section class="chat-app-form">
                                                    <form class="chat-app-input d-flex" id="chat_form">
                                                        <fieldset class="form-group position-relative has-icon-left col-10 m-0">
                                                            <input type="hidden" id="last_comment_id" value="{{$last_comment_id}}">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>
                                                            <input type="text" class="form-control" id="chat_input" placeholder="Type your message">
                                                        </fieldset>
                                                        <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                                                            <button id="chat_send" type="button" class="btn btn-block btn-info" ><i class="la la-paper-plane-o d-lg-none"></i>
                                                                <span class="d-none d-lg-block">Send</span>
                                                            </button>
                                                        </fieldset>
                                                    </form>
                                                </section>
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

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/chat-application.css')}}">
    <style>
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
        table, table.dataTable {
             font-size: 14px !important;
        }
        .height-430 {
            height: 430px !important;
        }
        .table tr th, .table tr td {
            vertical-align: middle !important;
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
            $('#chat_form').on('submit',function (e) {
                e.preventDefault();
            });
            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
            });
            $('#chat_form').keypress(function (e) {
                if (e.keyCode == 13) {
                    $('#chat_send').trigger('click');
                }
            });
            $('#chat_send').on('click', function () {
                var flag = true;
                var comment = $('#chat_input').val();
                var request_id = '{{$crm_details->id}}';
                if(comment == ''){
                    flag = false;
                    toastr.error("Please Enter Comment first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if(flag){
                    $.ajax({
                        url: '{!! route('cod.crm.comment.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment': comment,
                            'request_id':request_id
                        }
                    }).done(function (data) {
                        if(data.status){
                            // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            var user = '{{session('user_type')}}';
                            var shipper = 'You';
                            if(user == 1){
                                if($('div.chat:last-child').hasClass('shipper')) {
                                    var html = '<div class="chat-content"><p>' + comment + '</p></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                }else{
                                    var html = '<div class="chat shipper"><div class="chat-avatar"><div class="badge block badge-info"><i class="la la-user font-medium-2"></i>'+ shipper +'</div></div><div class="chat-body"><div class="chat-content"><p>' + comment + '</p></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }
                            }else{
                                if($('div.chat:last-child').hasClass('substitute-user')) {
                                    var html = '<div class="chat-content"><p>' + comment + '</p></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                }else{
                                    var html = '<div class="chat substitute-user"><div class="chat-avatar"><div class="badge block badge-substitute-user"><i class="la la-user font-medium-2"></i>'+ shipper +'</div></div><div class="chat-body"><div class="chat-content"><p>' + comment + '</p></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }
                            }

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

            },4000);
            @endif
            function get_latest_comment(comment_id,request_id) {
                if(comment_id){
                    $.ajax({
                        url: '{!! route('cod.crm.comment.get') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment_id':comment_id,
                            'request_id': request_id
                        }
                    }).done(function (data) {
                        if(data.status){
                            if(data.comment.comment_by === 0){
                                if($('div.chat:last-child').hasClass('admin')){
                                    var html = '<div class="chat-content mr-3"><p>'+ data.comment.comment +'</p></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                }else{
                                    var html = '<div class="chat chat-left admin"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>Agent</div></div><div class="chat-body"><div class="chat-content"><p>' + data.comment.comment + '</p></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }
                            }else if(data.comment.comment_by === 2){
                                if($('div.chat:last-child').hasClass('substitute-user')) {
                                    var html = '<div class="chat-content"><p>' + data.comment.comment + '</p></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                }else{
                                    var html = '<div class="chat substitute-user"><div class="chat-avatar"><div class="badge block badge-substitute-user"><i class="la la-user font-medium-2"></i>'+ data.name +'</div></div><div class="chat-body"><div class="chat-content"><p>' + data.comment.comment + '</p></div></div></div>';
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

            setInterval(function () {
                window.location.reload();
            }, 500000);
        });

    </script>
@endsection