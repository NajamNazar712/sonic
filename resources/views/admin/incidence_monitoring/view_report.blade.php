@extends('admin.layout.master')

@section('title', 'Request Details')
@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
                        Request Details ({{str_pad($report->id, 6, '0', STR_PAD_LEFT)}})
                        @if(($report->status_id == 1))
                            (Open)
                        @elseif(($report->status_id == 2))
                            (Under Action)
                        @elseif(($report->status_id == 3))
                            (Close)
                        @endif
                       
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
                                                                <td class="align-middle">Regional Manger</td>
                                                            </tr>
                                                            <tr style="color:#fff;" class="btn-dark">
                                                                <td class="align-middle">Zonal Manager</td>
                                                            </tr>
                                                            <tr style="color:#fff;" class="btn-primary">
                                                                <td class="align-middle">Operations</td>
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
                                                <th scope="row">Station</th>
                                                <td class="name">
                                                        <h5 class="mb-0">{{$report->station->name}}</h5>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th scope="row">Monitoring Area</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$report->monitoring_area->name}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Time Slot</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$report->time_from}} - {{$report->time_to}}</h5>
                                                </td>
                                            </tr>
                                           <tr>
                                                <th scope="row">Case Nature</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$report->case_nature->name}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Observations</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$report->observation}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">NC Level</th>
                                                <td class="name">
                                                        <h5 class="mb-0">{{$report->nc_level->name}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Tagged To</th>
                                                <td class="name">
                                                    <h5 class="mb-0">
                                                        @php
                                                            $i=1;
                                                        @endphp
                                                        @foreach ($report->tagged_persons as $tagged_person)
                                                            @php ++$i; @endphp
                                                            @if ($i<=count($report->tagged_persons))
                                                                {{$tagged_person->admin->name}},     
                                                            @else
                                                                {{$tagged_person->admin->name}}
                                                            @endif
                                                        @endforeach
                                                    </h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Tagging Date</th>
                                                <td class="name" id="status">
                                                    <h5 class="mb-0">{{$report->tagging_date}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Clips Link</th>
                                                <td class="name">
                                                    
                                                    <h5 class="mb-0">
                                                        <a class="btn btn-md  align-middle" href="{{$report->clip_link}}" target="_blank">{{$report->clip_link}}</a>
                                                        </h5>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <div class="row justify-content-center">
                                            @if(session('role_id') == 1 || in_array(541, session('permissions')) )
                                                <div class="text-center">
                                                    <form id="status_update_form" method="post"
                                                          action="{{route('admin.incidence_monitoring.update_status')}}">
                                                        @csrf
                                                        <input type="hidden" id="req_id" name="req_id"
                                                               value="{{$report->id}}">
                                                        <input type="hidden" id="req_status" name="req_status">
                                                        @if($report->status_id == 1)
                                                                <button id="under_action" type="submit"
                                                                        class="btn btn-warning mr-1">
                                                                    <span class="d-none d-lg-block">
                                                                        Under Action
                                                                    </span>
                                                                </button>

                                                        @elseif($report->status_id == 2)
                                                            <button id="close" name="resolved_close" type="submit" class="btn btn-danger mr-3">
                                                                <span class="d-none d-lg-block">
                                                                    Close
                                                                </span>
                                                            </button>
                                                                
                                                        @elseif($report->status_id == 3)
                                                               
                                                            <button id="open" type="submit"
                                                            class="btn btn-success mr-1">
                                                                <span class="d-none d-lg-block">
                                                                    Reopen
                                                                </span>
                                                            </button>
                                                        @endif
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-7">
                                        <div class="content-body chat-application">
                                            <section
                                                    class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible">
                                                <div class="chats">
                                                    @if(count($report->comments) > 0)

                                                        @foreach($report->comments as $comment)
                                                                <div id="chat_{{$comment->id}}"
                                                                     class="chat {{($comment->comment_by == 1) ? 'regional_manger' : '' }} {{($comment->comment_by == 0) ? 'zonal_manager' : '' }}">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-admin">
                                                                            <i class="la la-user font-medium-2"></i>
                                                                            @if($comment->comment_by_id == Auth::id())
                                                                                YOU
                                                                            @else
                                                                                {{$comment->commenter->name}}
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

                                            @if(session('role_id') == 1 || in_array(586, session('permissions')) )
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
                                            @endif
                                                <div class="row justify-content-center mt-1">
                                                    @if(session('role_id') == 1 || in_array(540, session('permissions')) )
                                                        <div class="col-4">
                                                            <button class="btn btn-social btn-primary mb-1 ml-1" type="button" id="image_upload_btn"><span class="la la-picture-o"></span>Image Upload</button>
                                                        </div>
                                                    @endif
                                                </div>
                                        </div>

                                    </div>

                                </div>

                                

                                @if(count($report->status_history) > 0)
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
                                                        <th>Marked By</th>
                                                        <th>Status Assigned Date</th>
                                                        <th>Request ID</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $index=0;   @endphp
                                                    @foreach($report->status_history as $status_history)
                                                        @php $index++; @endphp
                                                        <tr class="border-bottom-success border-custom-color">
                                                            <td>{{$index}}</td>
                                                            <td>{{$status_history->status->status}}</td>
                                                            <td>{{$status_history->admin->name}}</td>
                                                            <td>{{$status_history->created_at}}</td>
                                                            <td>{{str_pad($report->id, 6, '0', STR_PAD_LEFT)}}</td>
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

    <div class="modal fade text-left" id="image_upload_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="image_upload_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Image Upload</h4>

                </div>
                <div class="modal-body  text-center">
                    <table class="table table-bordered" id="image_view_table" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Date Added</th>
                            <th class="border-primary border-darken-1">Image</th>
                            <th class="border-primary border-darken-1">Added By</th>

                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>


                    <form id="image_upload_form" class="form" action="{{route('admin.incidence_monitoring.image_submit')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="image_request_id" id="image_request_id"/>
                        <input type="hidden" name="selected_ids" id="selected_ids"/>
                        <table class="table table-bordered datatable" id="image_upload_table" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Image</th>
                                <th class="border-primary border-darken-1"></th>

                            </tr>
                            </thead>
                        </table>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="" type="button" class="btn btn-danger btn-block" data-dismiss="modal">Close</button>
                            </div>
                            <div class="col-3">
                                <button id="ImageSubmitButton" type="submit" class="btn btn-primary btn-block" disabled>Upload</button>
                            </div>

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

        .chat-application .chats .regional_manger .chat-content {
            color: #ffffff;
            background-color: #ab45d7;
        }

        .chat-application .chats .regional_manger .chat-body .chat-content:before {
            border-left-color: #ab45d7;
        }
        .chat-application .chats .zonal_manager .chat-content {
            color: #ffffff;
            background-color: #18374A;
        }

        .chat-application .chats .zonal_manager .chat-body .chat-content:before {
            border-left-color: #18374A;
        }
        #image_upload_btn{
            padding-right: 12px; 
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
           
            $('#open').on('click', function (e) {
                $('#req_status').val(1);  
           
                $('#status_update_form').validate({

                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Status is being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                    }
                    });
                
            });
            $('#close').on('click', function (e) {
                $('#req_status').val(3);  
           
                $('#status_update_form').validate({

                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Status is being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                    }
                    });
                
            });
            $('#under_action').on('click', function (e) {
                $('#req_status').val(2);  
           
                $('#status_update_form').validate({

                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Status is being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                    }
                    });
                
            });
            $('#chat_form').on('submit', function (e) {
                e.preventDefault();
            });

            $('#chat_send').on('click',function (){
                var flag = true;
                var comment = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');
                $('#chat_input').val('');
                var request_id = '{{$report->id}}';

                if (comment == '') {
                    flag = false;
                    toastr.error("Please Enter Comment first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if (flag) {
                    $.ajax({
                        url: '{!! route('admin.incidence_monitoring.comment.add') !!}',
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

            function last_comment_edit(last_comment, comment){
                $('#edit_comment_' + last_comment).on('click', function (e) {
                var comment_id = $(this).attr("value");
                e.preventDefault();
                var text_edit  = "Are you sure, you want to edit this comment as Internal? \n \t "+comment;
                swal({
                    text: text_edit,
                    icon: 'info',
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
                }).then(function(confirm) {
                    if(confirm){
                        swal({
                            title: 'Please Wait!',
                            text: 'Comment is being updated.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $.ajax({
                            url: '{!! route('admin.crm.comment.edit') !!}',
                            method: 'POST',
                            data: {
                                'comment_id': last_comment,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    $('#edit_comment_' + last_comment).remove();
                                    $('#chat_' + last_comment).addClass('internal');
                                    $('#updated_by_div_' + last_comment).append('<small>Updated by: ' + data.updated_by + ' (' + data.updated_at + ')</small>');
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                            });
                    }
                });
            });
            }
            
         
            function get_latest_comment(request_id) {
                    $.ajax({
                        url: '{!! route('admin.incidence_monitoring.comment.get') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'request_id': request_id
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            html = "";
                            console.log(data.comments);
                            $(data.comments).each(function (i,comment){
                                let type = '';
                                let commenter = '';
                               
                                if(comment.comment_by == 0)
                                {
                                    type = "zonal_manager";
                                }
                                else if(comment.comment_by == 1)
                                {
                                    type = "regional_manger";
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
            

            function updateScroll() {
                const container = document.querySelector('.chat-app-window');
                container.scrollTop = $('.chat-app-window')[0].scrollHeight;

            }

            updateScroll();


            var images_count = {{ $images_count }};
            var rows_count = 0;
            var selected_rows = [];
            $('#image_upload_btn').on('click', function () {
                $('#image_upload_btn').attr('disabled', true);
                var request_id = '{{$report->id}}';
                if(request_id){
                    $('#image_request_id').val(request_id);
                    $.ajax({
                        url: '{!! route('admin.incidence_monitoring.image_details') !!}',
                        method: 'POST',
                        data: {
                            'request_id': request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        console.log(data);
                        if(data.status == 0){
                            var image_html = '';
                            $.each(data.images, function (index, image) {
                                index++;
                                var img = '<a class="btn btn-sm btn-outline-info align-middle" href="' + image.image + '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                                image_html += '<tr id="' + image.id + '"><td>' + index + '</td><td>' + image.date + '</td><td>' + img + '</td><td>' + image.added_by + '</td></tr>';
                            });
                            $('#image_view_table tbody').append(image_html);
                            $('#image_upload_modal').modal('show');
                        }else if(data.status == 2){
                            var image_html = '<tr><td colspan="4">No Images found!</td></tr>';

                            $('#image_view_table tbody').append(image_html);
                            $('#image_upload_modal').modal('show');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        $('#image_upload_btn').attr('disabled', false);

                    });
                }
            });
            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));
            var image_table;
            function add_row() {
                var tr_id = $('#image_upload_table tbody tr').attr('id');
                if (typeof tr_id !== typeof undefined && tr_id !== false) {
                    var new_img_rows = $('#image_upload_table tbody tr').length;
                    new_img_rows = images_count + new_img_rows;
                    // if(new_img_rows >= 2){
                    //     $('#image_upload_table .img_add_btn').attr('disabled', true);
                    //     return false;
                    // }
                }

                rows_count++;

                var image = '<input class="form-control form-control-sm" type="file" name="image_'+rows_count+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">';
                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                }
                image_table.row.add([0, image,remove]).node().id = rows_count;
                image_table.draw(true);
                $('#ImageSubmitButton').attr('disabled', false);
                selected_rows.push(rows_count);
            }
            image_table = $('#image_upload_table').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary img_add_btn',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {
                        console.log(images_count);
                        // if(images_count < 2){
                            add_row();
                        // }
                    }
                }],
                ordering:false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'image', class: 'align-middle image form-group'},
                    {name: 'action', class: 'align-middle action'},
                ],

                rowCallback: function(row, data, index) {
                    var info = image_table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {

                    // this.api().table().columns.adjust();
                }
            });
            $('#image_view_table').on('click','a.remove_row', function () {
                var row_id = $(this).parents('tr').attr('id');
                var request_id = $('#image_request_id').val();
                var current = $(this);
                if(row_id){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes if you want to delete this image!',
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
                        if (confirm) {
                            $.ajax({
                                url: '{!! route('admin.crm.request.image_delete') !!}',
                                method: 'POST',
                                data: {
                                    'image_id': row_id,
                                    'request_id':request_id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if(data.status == 0){
                                    images_count = images_count - 1;
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    current.parents('tr').remove();
                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                        }
                    });
                }
            });

            $('body').on('click', 'a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_rows);

                if (index !== -1) {
                    selected_rows.splice(index, 1);
                }
                image_table.row( $(this).parents('tr') ).remove().draw();
            });
            $('#image_upload_form').validate({

                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    $('#selected_ids').val(selected_rows);
                    swal({
                        title: 'Please Wait!',
                        text: 'Image is being uploaded!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });
            $('#image_upload_modal').on('hidden.bs.modal', function () {
                $('#image_request_id').val('');
                image_table.clear();
                image_table.draw();
                selected_rows = [];
                rows_count = 0;
                $('#image_view_table tbody').html('');
            });
            

        });



    </script>
@endsection