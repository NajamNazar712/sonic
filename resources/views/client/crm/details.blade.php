@extends('client.layout.master')

@section('title', 'Request Details')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
                        Request Details
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
                                                    <th scope="row">Case Nature</th>
                                                    <td class="name">
                                                        <h4>{{$crm_details->nature->name}}</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Case Nature Type</th>
                                                    <td class="name">
                                                        <h4>{{$crm_details->nature_type->type}}</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Channel</th>
                                                    <td class="name">
                                                        <h3>{{$crm_details->channel->channel}}</h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Status</th>
                                                    <td class="name">
                                                        <h3>{{$crm_details->request_status->name}}</h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Launched By</th>
                                                    <td class="name">
                                                        <h3>{{$launched_by->name}}</h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Launched Date</th>
                                                    <td class="name">
                                                        <h3>{{$crm_details->created_at}}</h3>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                            <div class="content-body chat-application">
                                                <section class="chat-app-window">
                                                    {{--<div class="badge badge-default mb-1">Chat History</div>--}}
                                                    <div class="chats">
                                                        @if(!empty($comments))
                                                        @foreach($comments as $comment)
                                                            @if($comment->comment_by == 0)
                                                                <div class="chat admin">
                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-success">
                                                                            <i class="la la-user font-medium-2"></i>{{$comment->admin->name}}
                                                                        </div>


                                                                    </div>
                                                                    <div class="chat-body">
                                                                        <div class="chat-content">
                                                                            <p>{{$comment->comment}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @else
                                                                <div class="chat chat-left shipper">
                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-info">
                                                                            <i class="la la-user font-medium-2"></i>{{Auth::user()->name}}
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
                                                            <button id="chat_send" type="button" class="btn btn-info" ><i class="la la-paper-plane-o d-lg-none"></i>
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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#chat_form').on('submit',function (e) {
                e.preventDefault();
            });
            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
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
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            if($('div.chat:last-child').hasClass('shipper')){
                                var html = '<div class="chat-content"><p>'+ comment +'</p></div>';
                                $('div.chat:last-child').find('.chat-body').append(html);
                            }else{
                                var shipper = '{{Auth::user()->name}}';
                                var html = '<div class="chat chat-left shipper"><div class="chat-avatar"><div class="badge block badge-info"><i class="la la-user font-medium-2"></i>'+ shipper +'</div></div><div class="chat-body"><div class="chat-content"><p>' + comment + '</p></div></div></div>';
                                $('section.chat-app-window .chats').append(html);
                            }
                            $('#chat_input').val('');
                        }
                    });
                }
            });

            setTimeout(function () {
                var last_comment_id = parseInt($('#last_comment_id').val());
                var request_id = '{{$crm_details->id}}';
                get_latest_comment(last_comment_id,request_id);
            },4000);
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
                        console.log(data)
                    });
                }
            }
        });
    </script>
@endsection