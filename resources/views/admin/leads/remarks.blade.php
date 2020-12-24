@extends('admin.layout.master')

@section('title', 'Leads Management')

@section('content')
    <h1 class="mb-1">
        Lead ({{str_pad($lead->id, 3, '0', STR_PAD_LEFT)}}) View Remarks
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">
                    <div class="col-5">
                        <table class="table table-bordered table-lg">
                            <tbody class="list">
                            <tr>
                                <th scope="row">Lead ID</th>
                                <td class="name">
                                    <h5 class="mb-0">{{str_pad($lead->id, 3, '0', STR_PAD_LEFT)}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Contact Person</th>
                                <td class="name">
                                    <h5 class="mb-0">{{$lead->contact_person}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Phone No</th>
                                <td class="name" id="status">
                                    <h5 class="mb-0">{{$lead->phone_number}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Email Address</th>
                                <td class="name">
                                    <h5 class="mb-0">{{$lead->email_address}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Requested Date/Time</th>
                                <td class="name">
                                    <h5 class="mb-0">{{$lead->requested_date}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Sale Person Tagged</th>
                                <td class="name">
                                    @if($lead->sale_person_id != null)
                                        <h5 class="mb-0">{{$lead->sales_person->name}}</h5>
                                    @else
                                        <h5 class="mb-0">-</h5>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Lead Status</th>
                                <td class="name">
                                    <h5 class="mb-0">{{$lead->status->name}}</h5>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-7">
                        <div class="content-body chat-application">
                            <section
                                    class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible">
                                <div class="chats">
                                    @if(!empty($details))
                                        @foreach($details as $detail)
                                            <div id="chat_{{$detail->id}}"
                                                 class="chat">

                                                <div class="chat-avatar">
                                                    <div class="badge block badge-admin">
                                                        <i class="la la-user font-medium-2"></i>{{$detail->admin->name}}
                                                    </div>
                                                </div>

                                                <div class="chat-body">
                                                    <div class="chat-content text-left">
                                                        <p>{!! $detail->remarks !!}</p>
                                                        <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($detail->updated_at))}} ({{$detail->updated_at}})</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </section>
                            <section class="chat-app-form">
                                <form class="chat-app-input row" id="chat_form">
                                    <fieldset class="form-group position-relative has-icon-left col-10 m-0">
                                        <div class="form-control-position">
                                            <i class="la la-chevron-right"></i>
                                        </div>
                                        <textarea id="chat_input" class="form-control height-150" placeholder="Enter Remarks"></textarea>
                                    </fieldset>
                                    <div class="display-inline-block col-2">
                                        <fieldset
                                                class="form-group position-relative has-icon-left m-0 mb-1">
                                            <button id="chat_send" type="button"
                                                    class="btn btn-block btn-purple chat_send" to="1"><i
                                                        class="la la-paper-plane-o d-lg-none"></i>
                                                <span class="">Submit</span>
                                            </button>
                                        </fieldset>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div></div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/ui/scrollable.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            function updateScroll() {
                const container = document.querySelector('.chat-app-window');
                container.scrollTop = $('.chat-app-window')[0].scrollHeight;
            }

            $('#chat_form').on('submit', function (e) {
                e.preventDefault();
            });
            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
            });

            $('.chat_send').on('click', function () {
                var flag = true;
                var remarks = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');
                $('#chat_input').val('');
                var remark_lead_id = '{{$lead->id}}';
                if (remarks == '') {
                    flag = false;
                    toastr.error("Please Enter Remarks first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if (flag) {
                    $.ajax({
                        url:"{{route('admin.leads.add_remarks')}}",
                        method:'POST',
                        data:{
                            'lead_id':remark_lead_id,
                            'remarks':remarks,
                            '_token':'{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            var html = '<div class="chat"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + remarks + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                            $('section.chat-app-window .chats').append(html);
                            updateScroll();
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });
            updateScroll();
        });
    </script>
@endsection