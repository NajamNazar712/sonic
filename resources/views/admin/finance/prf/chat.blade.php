@extends('admin.layout.master')

@section('title', 'Payment Requisition Chat View')
@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <h1>Payment Requisition Chat View</h1>
                <div class="content-body">
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('admin.inc.messages')
                                <div class="row mb-2">
                                  
                                    <div class="col-5"> 
                                        <table class="table table-bordered table-lg">
                                            <tbody class="list">
                                            
                                            <tr>
                                                <th scope="row">Request ID</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->id}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Invoice No</th>
                                                <td class="name" id="status">
                                                    <h5 class="mb-0">{{ $requisition->invoice_no }}</h5>
                                                </td>
                                            </tr>

                                             <tr>
                                                <th scope="row">On Account Of</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{ $requisition->account_of_name }}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Payee Name</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{ $requisition->payee_name }}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Amount</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->amount}}</h5>
                                                </td>
                                            </tr>
                                           <tr>
                                                <th scope="row">Requester Name</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->requester_name}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Requester Department</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->requester_department}}</h5>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Related Department</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->requested_for_department}}</h5>
                                                </td>
                                            </tr>
                                          
                                            <tr>
                                                <th scope="row">Description</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->description}}</h5>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Status</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->status_name}}</h5>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Created At</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$requisition->created_at}}</h5>
                                                </td>
                                            </tr>
                                            
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="col-7">
                                        <div class="content-body chat-application">
                                            <section
                                                    class="chat-app-window vertical-scroll scroll-example height-700 ps-container ps-theme-dark ps-active-y always-visible">
                                                <div class="chats">
                                                    @if(!empty($comments))

                                                        @foreach($comments as $comment)
                                                            
                                                            <div id="chat_{{$comment->id}}"
                                                                    class="chat {{($comment->commenter_id == Auth::id() ) ? 'admin': 'chat-left' }} ">

                                                                <div class="chat-avatar">
                                                                    <div class="badge block badge-admin">
                                                                        <i class="la la-user font-medium-2"></i>{{($comment->commenter_id == Auth::id() ) ? 'You': $comment->name }} 
                                                                    </div>
                                                                </div>

                                                                <div class="chat-body">
                                                                    <div class="chat-content text-left">
                                                                       
                                                                        <p>{!! nl2br($comment->comment) !!}</p>
                                                                        <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created))}} ({{$comment->created}})</small>
                                                                      
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        @endforeach
                                                    @endif

                                                </div>
                                            </section>

                                            <section class="chat-app-form pb-0">
                                                <form class="chat-app-input row" id="chat_form">
                                                    <fieldset
                                                            class="form-group position-relative has-icon-left col-9 m-0">
                                                        <input type="hidden" id="last_comment_id"
                                                                value="{{$last_comment_id}}">
                                                        <textarea id="chat_input" class="form-control height-200 summernote" placeholder="Type your message" ></textarea>
                                                    </fieldset>
                                                    <div class="display-inline-block col-3">
                                                        <fieldset
                                                                class="form-group position-relative has-icon-left m-0 mb-1">
                                                            <button id="chat_send" type="button"
                                                                    class="btn btn-block btn-purple chat_send" to="1"><i
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
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/chat-application.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/summernote/summernote.css')}}">


    <style>
        .note-editable {
            text-transform: lowercase;
        }
        .note-editable:first-letter {
            text-transform: capitalize;
        }
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

        .chat-application .chat-app-form {
            position: relative;
    padding: 20px 10px;
    background-color: #edeef0;
    overflow: hidden;
    /* height: 280px; */
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
        .chat-application .chats .admin.rider .chat-content {
            color: #ffffff;
            background-color: #18374A;
        }

        .chat-application .chats .admin.rider .chat-body .chat-content:before {
            border-left-color: #18374A;
        }
        .chat-application .chats .admin.consignee .chat-content {
            color: #ffffff;
            background-color: #008080;
        }

        .chat-application .chats .admin.consignee .chat-body .chat-content:before {
            border-left-color: #008080;
        }

        .feedback {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        .feedback .item {
            width: 90px;
            height: 90px;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
        }
        .feedback .radio {
            display: none;
        }
        .feedback .radio ~ span {
            font-size: 3rem;
            filter: grayscale(100);
            cursor: pointer;
            transition: 0.3s;
        }

        .feedback .radio:hover ~ span {
            filter: grayscale(0);
            font-size: 4rem;
        }

        .selectize-control {
            width: 500px !important;
        }

        .select-checkbox{
            border-color: #64a0d2;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
        tr.highalert_row{
            background-color: #ff6326;
            color: whitesmoke;
        }
        tr.highalert_row a{
            color: whitesmoke;
        }
        .select2-search__field{
            width: 140px !important;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 48px !important;      
            max-height: 100px !important;   
            overflow-y: auto !important;   
            padding-bottom: 5px;
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
    <script src="{{asset('app-assets/js/scripts/summernote/summernote.js')}}" type="text/javascript"></script>

    
    <script type="text/javascript">
        $(document).ready(function () {

            $('.summernote').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                ],

            });


            var selectedValue = parseInt($('input[type="radio"]:checked').val());

            $('input[type="radio"]').each(function() {
                var radioValue = parseInt($(this).val());

                if (radioValue <= selectedValue) {
                    $(this).prop('disabled', true);
                        $(this).next('span').css({
                            filter: 'grayscale(0)',
                            fontSize: '4rem'
                        });
                    
                } else {
                    $(this).prop('disabled', false);
                }
            });

        
            $('#chat_form').on('submit', function (e) {
                e.preventDefault();
            });
            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
            });

        
            $('.chat_send').on('click', function () {
                var flag = true;
                var comment = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');

                $('#chat_input').val('');
                var request_id = '{{$requisition->id}}';
                if (comment == '') {
                    flag = false;
                    toastr.error("Please Enter Comment first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if (flag) {
                    $.ajax({
                        url: '{!! route('admin.finance.prf.comment.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment': comment,
                            'request_id': request_id,
                        }
                    }).done(function (data) {
                        if (data.status) {

                            var user = '{{Auth::user()->name}}';
                            var html = '<div class="chat admin"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left "><p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';

                            $('section.chat-app-window .chats').append(html);
                            $('#last_comment_id').val(data.last_comment_id);
                
                            $(".summernote").summernote("code", "");

                            updateScroll();
                        }
                    });
                }
            });
           
            setInterval(function () {
                var last_comment_id = parseInt($('#last_comment_id').val());
                var request_id = '{{$requisition->id}}';
                get_latest_comment(last_comment_id, request_id);
            }, 10000);

            function get_latest_comment(comment_id, request_id) {
                if (comment_id) {
                    $.ajax({
                        url: '{!! route('admin.finance.prf.comment.list') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment_id': comment_id,
                            'request_id': request_id
                        }
                    }).done(function (data) {
                        if (data.status && data.status && data.comment) {

                            var html = '<div class="chat chat-left"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>'+ data.comment.name +'</div></div><div class="chat-body"><div class="chat-content text-left "><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';

                            $('section.chat-app-window .chats').append(html);
                            $('#last_comment_id').val(data.comment.id);
                            updateScroll();
                        }
                    });
                }
            }

            function updateScroll() {
                const container = document.querySelector('.chat-app-window');
                container.scrollTop = $('.chat-app-window')[0].scrollHeight;

            }

            updateScroll();
        
        });

    </script>
@endsection