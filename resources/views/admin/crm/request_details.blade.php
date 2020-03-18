@extends('admin.layout.master')

@section('title', 'Request Details')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
                        {{--                        {{dd($crm_details['status_id'])}}--}}
                        Request Details ({{str_pad($crm_details->id, 6, '0', STR_PAD_LEFT)}})
                        @if(($crm_details['status_id'] == 1))
                            (Launched)
                        @endif
                        @if(($crm_details['status_id'] == 2))
                            (In-process)
                        @endif
                        @if(($crm_details['status_id'] == 3))
                            (Resolved)
                        @endif
                        @if(($crm_details['status_id'] == 4))
                            (Closed)
                        @endif
                        @if(($crm_details['status_id'] == 5))
                            (Re-Open)
                        @endif
                        <div class="text-right mb-1">
                            @if(($crm_details['status_id'] == 2 || $crm_details['status_id'] == 3) && (session('role_id') == 1 || $crm_details->agent['id'] == Auth::id() || in_array(185, session('permissions'))))
                                <button type="button" class="btn btn-primary width-10-per" id="tag"><span
                                            class="d-none d-lg-block" style="color: white">Tag</span></button>
                            @endif
                            @if(($crm_details['status_id'] == 2) && (session('role_id') == 1 || $crm_details->agent['id'] == Auth::id() || in_array(309, session('permissions'))))
                                <button type="button" class="btn btn-primary width-10-per" id="un_tag"><span
                                            class="d-none d-lg-block" style="color: white">Un Tag</span></button>
                            @endif
                            @if((session('role_id') == 1 || in_array(213, session('permissions'))))
                                <button type="button" class="btn btn-primary width-10-per" id="edit_request"><span
                                            class="d-none d-lg-block" style="color: white">Edit Request</span></button>
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
                                            @if(!empty($crm_details->shipment_id))
                                            <tr>
                                                <th scope="row">Tracking Number</th>
                                                <td class="name">
                                                        <h5 class="mb-0"><u><a href='{{route('admin.tracking.index')}}?tracking_number={{$crm_details->shipment->tracking_number}}' class='tracking' target='_blank'>{{$crm_details->shipment->tracking_number}}</a></u></h5>
                                                </td>
                                            </tr>
                                            @endif
                                            @if(!empty($shipment_status))
                                            <tr>
                                                <th scope="row">Shipment Status</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$shipment_status}}</h5>
                                                </td>
                                            </tr>
                                            @endif
                                            @if(!empty($shipment_status_date))
                                            <tr>
                                                <th scope="row">Shipment Status Updated At</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$shipment_status_date}}</h5>
                                                </td>
                                            </tr>
                                            @endif
                                            @if(!empty($shipper))
                                            <tr>
                                                <th scope="row">Shipper Name</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$shipper}}</h5>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th scope="row">Case Nature</th>
                                                <td class="name">
                                                    <h5 class="mb-0">{{$crm_details->nature->name}}</h5>
                                                </td>
                                            </tr>
                                            @if(!empty($crm_details->case_nature_type_id))
                                            <tr>
                                                <th scope="row">Case Nature Type</th>
                                                <td class="name">
                                                        <h5 class="mb-0">{{$crm_details->nature_type->type}}</h5>
                                                </td>
                                            </tr>
                                            @endif
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
                                                    @if($agent != null)
                                                        <h5 class="mb-0">{{$agent}}</h5>
                                                    @else
                                                        <h5 class="mb-0">-</h5>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($crm_details['status_id'] == 2 || $crm_details['status_id'] == 3)
                                                <tr>
                                                    <th scope="row">Tagged To</th>
                                                    <td class="name">
                                                        @if($tagged_name != null)
                                                            <h5 class="mb-0">{{$tagged_name}}</h5>
                                                        @else
                                                            <h5 class="mb-0">-</h5>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                                <tr>
                                                    <th scope="row">Arrival Date</th>
                                                    <td class="name">
                                                        @if($arrival_date != '')
                                                            <h5 class="mb-0">{{$arrival_date}}</h5>
                                                        @else
                                                            <h5 class="mb-0">-</h5>
                                                        @endif
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
                                        <div class="row justify-content-center">
                                            @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')) || (($tag_check['crm_request_tagging_type_id'] == 1 && $tag_check['tagged_id'] == $tag_permission) || ($tag_check['crm_request_tagging_type_id'] == 2 && $tag_check['tagged_id'] == Auth::id())))
                                                <div class="text-center">
                                                    <form id="valid_form" method="post"
                                                          action="{{route('admin.crm.valid')}}">
                                                        @csrf
                                                        <input type="hidden" id="req_id" name="req_id"
                                                               value="{{$crm_details->id}}">
                                                        <input type="hidden" id="prev_status" name="prev_status"
                                                               value="{{$crm_details->status_id}}">
                                                        @if($crm_details['status_id'] != 3)
                                                            @if($crm_details['status_id'] == 1 ||$crm_details['status_id'] == 5)
                                                                <button id="valid" type="submit"
                                                                        class="btn btn-success mr-1">
                                                    <span class="d-none d-lg-block">
                                                        Valid
                                                    </span>
                                                                </button>
                                                            @elseif($crm_details['status_id'] == 2)
                                                                @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')) || (($tag_check['crm_request_tagging_type_id'] == 1 && $tag_check['tagged_id'] == $tag_permission) || ($tag_check['crm_request_tagging_type_id'] == 2 && $tag_check['tagged_id'] == Auth::id())))
                                                                    <button id="valid" type="submit"
                                                                            class="btn btn-success mr-1">
                                                        <span class="d-none d-lg-block">
                                                        Resolve
                                                        </span>
                                                                    </button>
                                                                @endif
                                                            @elseif($crm_details['status_id'] == 4 && (in_array(186, session('permissions')) || session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id()))
                                                                <button id="valid" type="submit"
                                                                        class="btn btn-success mr-1">
                                                    <span class="d-none d-lg-block">
                                                    Re-Open
                                                    </span>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </form>
                                                </div>
                                            @endif
                                            @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')))
                                                <div class="text-center">
                                                    <form id="invalid_form" method="post"
                                                          action="{{route('admin.crm.invalid')}}">
                                                        @csrf
                                                        <input type="hidden" id="req_id" name="req_id"
                                                               value="{{$crm_details->id}}">
                                                        <input type="hidden" id="prev_status" name="prev_status"
                                                               value="{{$crm_details->status_id}}">
                                                        @if($crm_details['status_id'] == 1 ||$crm_details['status_id'] == 2 ||$crm_details['status_id'] == 5)
                                                            <input type="hidden" id="close" name="close"
                                                               value="0">
                                                        @else
                                                            <input type="hidden" id="close" name="close"
                                                                   value="1">
                                                        @endif
                                                        @if($crm_details['status_id'] != 4)
                                                            @if($crm_details['status_id'] == 3)
                                                                <button id="resolved_close" name="resolved_close" type="submit" class="btn btn-danger mr-3">
                                                                    <span class="d-none d-lg-block">
                                                                        Close
                                                                    </span>
                                                                </button>
                                                            @endif
                                                            <button id="invalid" type="submit" class="btn btn-danger">
                                                                <span class="d-none d-lg-block">
                                                                    @if($crm_details['status_id'] == 1 ||$crm_details['status_id'] == 2 ||$crm_details['status_id'] == 3 || $crm_details['status_id'] == 5)
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
                                    </div>
                                    <div class="col-7">
                                        <div class="content-body chat-application">
                                            <section
                                                    class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible">
                                                <div class="chats">
                                                    @if(!empty($comments))

                                                        @foreach($comments as $comment)
                                                            @if($comment->comment_by == 0)
                                                                <div id="chat_{{$comment->id}}"
                                                                     class="chat admin {{($comment->comment_type == 1)? 'internal':'' }}">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-admin">
                                                                            <i class="la la-user font-medium-2"></i>{{$comment->admin->name}}
                                                                        </div>
                                                                    </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content text-left">
                                                                            @if($comment->comment_type == 0 && (session('role_id') == 1 || in_array(310, session('permissions'))))
                                                                                <button type="button" class="border-0" id="edit_comment_{{$comment->id}}" value="{{$comment->id}}"><i class="ft-edit"></i></button>
                                                                            @endif
                                                                            <p>{!! $comment->comment !!}</p>
                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>
                                                                                <div id="updated_by_div_{{$comment->id}}">
                                                                                    @if($comment->comment_updated_by != null && $comment->comment_updated_at != null)
                                                                                        <small>Updated by: {{$comment->updated_by_admin->name}} ({{$comment->comment_updated_at}})</small>
                                                                                    @endif
                                                                                </div>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            @elseif($comment->comment_by == 1)
                                                                <div class="chat chat-left shipper">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-info">
                                                                            <i class="la la-user font-medium-2"></i>
                                                                            @if($shipper != null)
                                                                                {{$shipper}}
                                                                            @else
                                                                                Shipper
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content text-left">
                                                                            <p>{!! $comment->comment !!}</p>
                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="chat chat-left substitute-user">

                                                                    <div class="chat-avatar">
                                                                        <div class="badge block badge-substitute-user">
                                                                            <i class="la la-user font-medium-2"></i>
                                                                            @if($shipper != null)
                                                                                {{$shipper}}
                                                                            @else
                                                                                Shipper
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="chat-body">
                                                                        <div class="chat-content text-left">
                                                                            <p>{!! $comment->comment !!}</p>
                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </div>
                                            </section>

                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()) || ($crm_details->launched_by == 0 && $crm_details->launched_by_id == Auth::id()) || in_array(201, session('permissions')) || ($sale_person && $sale_person->admin_id == Auth::id()))
                                                <section class="chat-app-form">
                                                    <form class="chat-app-input row" id="chat_form">
                                                        <fieldset
                                                                class="form-group position-relative has-icon-left col-10 m-0">
                                                            <input type="hidden" id="last_comment_id"
                                                                   value="{{$last_comment_id}}">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>
                                                            {{--<input type="text" class="form-control" id="chat_input"--}}
                                                                   {{--placeholder="Type your message">--}}
                                                            <textarea id="chat_input" class="form-control height-100" placeholder="Type your message" row="4"></textarea>
                                                        </fieldset>
                                                        <div class="display-inline-block col-2">
                                                            <fieldset
                                                                    class="form-group has-icon-left m-0 mb-1">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-purple chat_send" to="1"><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Internal</span>
                                                                </button>
                                                            </fieldset>
                                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()) || in_array(201, session('permissions')))
                                                                <fieldset
                                                                        class="form-group position-relative has-icon-left m-0">
                                                                    <button id="chat_send" type="button"
                                                                            class="btn btn-block btn-default chat_send" to="0">
                                                                        <i class="la la-paper-plane-o d-lg-none"></i>
                                                                        <span class="">Shipper</span>
                                                                    </button>
                                                                </fieldset>
                                                            @endif
                                                        </div>
                                                    </form>
                                                </section>
                                            <div class="row justify-content-center mt-1">
                                                <div class="col-2">
                                                    <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_image', ['id' => $crm_details->id])}}" target="_blank">View Product</a></button>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-primary ml-1"><a class="white" href="{{route('admin.crm.claim.invoice_image', ['id' => $crm_details->id])}}" target="_blank">View Invoice</a></button>
                                                </div>
                                            </div>
                                            @elseif(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || ($crm_details->launched_by == 0 && $crm_details->launched_by_id == Auth::id()) || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ))) || ($sale_person && $sale_person->admin_id == Auth::id()))
                                                <section class="chat-app-form">
                                                    <form class="chat-app-input row" id="chat_form">
                                                        <fieldset
                                                                class="form-group position-relative has-icon-left col-10 m-0">
                                                            <input type="hidden" id="last_comment_id"
                                                                   value="{{$last_comment_id}}">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>
                                                            {{--<input type="text" class="form-control" id="chat_input"--}}
                                                                   {{--placeholder="Type your message">--}}
                                                            <textarea id="chat_input" class="form-control height-100" placeholder="Type your message"></textarea>
                                                        </fieldset>
                                                        <div class="display-inline-block col-2">
                                                            <fieldset
                                                                    class="form-group position-relative has-icon-left m-0 mb-1">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-purple chat_send" to="1"><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Internal</span>
                                                                </button>
                                                            </fieldset>
                                                            @if(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ))))
                                                            <fieldset
                                                                    class="form-group position-relative has-icon-left m-0">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-default chat_send" to="0">
                                                                    <i class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Shipper</span>
                                                                </button>
                                                            </fieldset>
                                                            @endif
                                                        </div>
                                                    </form>
                                                </section>
                                                <div class="row justify-content-center mt-1">
                                                    <div class="col-2">
                                                        <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_image', ['id' => $crm_details->id])}}" target="_blank">View Product</a></button>
                                                    </div>
                                                    <div class="col-2">
                                                        <button class="btn btn-primary ml-1"><a class="white" href="{{route('admin.crm.claim.invoice_image', ['id' => $crm_details->id])}}" target="_blank">View Invoice</a></button>
                                                    </div>
                                                </div>
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
                                                        <th>Request ID</th>
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
                                                                @if($status_history->status_id == 5)
                                                                    <td>{{$shipper}} (Shipper)</td>
                                                                @else
                                                                    <td>-</td>
                                                                @endif
                                                            @endif
                                                            <td>{{$status_history->created_at}}</td>
                                                            <td>{{str_pad($status_history->crm_request_id, 6, '0', STR_PAD_LEFT)}}</td>
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
                                                            @elseif($tagging_history->crm_request_tagging_type_id == 2)
                                                                <td>{{$tagging_history->user->name}}</td>
                                                                <td>{{$tagging_history->tagging->name}}</td>
                                                            @else
                                                                <td>-</td>
                                                                <td>Un Tagged</td>
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
        <div class="modal fade text-left" id="tagModal" data-backdrop="static" tabindex="-1" role="dialog"
             aria-labelledby="tagModal"
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
                                        <input type="hidden" id="prev_status" name="prev_status"
                                               value="{{$crm_details->status_id}}">
                                        <select name="tag_type" id="tag_type" class="form-control select2">
                                            @foreach($types as $type)
                                                <option value="{{$type->id}}"> {{$type->name}} </option>
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
                                                    <option value="{{$admin->id}}"> {{$admin->name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-none" id="department_tag_div">
                                            <div class="">
                                                <select name="tag_department" id="tag_department"
                                                        class="form-control  select2">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}"> {{$department->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-1">
                                                <select name="tag_hub" id="tag_hub"
                                                        class="form-control select2">
                                                    @foreach($hubs as $hub)
                                                        <option value="{{$hub->id}}"> {{$hub->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
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
        <div class="modal fade text-left" id="editRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editRequestModal"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white">Edit Request</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="edit_request_form" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="text" name="request_id" id="request_id" class="hidden" value="{{$crm_details->id}}">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-4">
                                        <fieldset class="form-group">
                                            <input type="text" name="tracking_number" class="form-control tracking_number"
                                                   placeholder="Tracking Number*" data-tags-input-name="tracking_number"
                                                   data-rule-required="true" data-msg-required="Tracking Number is required">
                                            <input type="hidden" name="tracking_number" id="tracking_number" value="">
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                                @foreach($case_nature as $nature)
                                                    <option value="{{$nature->id}}">{{$nature->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="complaints d-none" id="request_complaints">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <fieldset class="form-group">
                                                <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">
                                                    @foreach($case_nature_complaints as $complaints)
                                                        @if($crm_details->case_nature_type_id != $complaints->id)
                                                            <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="service d-none" id="request_service">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <fieldset class="form-group">
                                                <select name="case_nature_request" id="case_nature_requests" class="form-control select2">
                                                    @foreach($case_nature_service_requests as $service)
                                                        @if($crm_details->case_nature_type_id != $service->id)
                                                            <option value="{{$service->id}}">{{$service->type}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-none" id="description_div">
                                    <div class="row justify-content-center">
                                        <div class="col-8">
                                            <fieldset class="form-group">
                                                <textarea class="form-control" name="description" id="description" rows="5" placeholder="Enter Description Here...">{{$crm_details->description}}</textarea>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="claims d-none" id="request_claims">
                                    <input type="hidden" name="case_nature_id" id="case_nature_id">
                                    <input type="hidden" name="complaint_id" id="complaint_id">
                                    <input type="hidden" name="channel_id" id="channel_id">
                                    <div class="row justify-content-center">
                                        <div class="col-8">
                                            <fieldset class="form-group">
                                                <select name="case_nature_claim" id="case_nature_claim" class="form-control select2">
                                                    @foreach($case_nature_type_claims as $claim)
                                                        <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-8">
                                            <fieldset class="form-group">
                                                <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Product Cost">
                                            </fieldset>
                                        </div>
                                        <div class="col-8 text-left">
                                            <fieldset class="form-group">
                                                <label for="product_picture"><b>Product Picture:</b></label>
                                                <input class="form-control form-control-sm" type="file" name="product_picture" id="product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                            </fieldset>
                                        </div>
                                        <div class="col-8 text-left">
                                            <fieldset class="form-group">
                                                <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                                <input class="form-control form-control-sm" type="file" name="invoice_picture" id="invoice_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <button id="editRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#claim_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });

            {{--$('#valid').on('click', function (e) {--}}
            {{--e.preventDefault();--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.crm.valid') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'_token': '{{ csrf_token() }}',--}}
            {{--'id': $('#req_id').val(),--}}
            {{--'prev_status' : $('#prev_status').val()--}}
            {{--}--}}
            {{--})--}}
            {{--.done(function(data) {--}}
            {{--if (data.status == 0) {--}}
            {{--toastr.success(data.success, 'Marked!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

            {{--}--}}
            {{--else {--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--});--}}
            {{--});--}}

            {{--$('#invalid').on('click', function (e) {--}}
            {{--e.preventDefault();--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.crm.invalid') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'_token': '{{ csrf_token() }}',--}}
            {{--'id': $('#req_id').val(),--}}
            {{--'prev_status' : $('#prev_status').val()--}}
            {{--}--}}
            {{--})--}}
            {{--.done(function(data) {--}}
            {{--if (data.status == 0) {--}}
            {{--toastr.success(data.success, 'Marked!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--else {--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--});--}}
            {{--});--}}
            $('.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                dropdownParent:$('#edit_request_form')
            });
            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#edit_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#description_div').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#editRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#description_div').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#editRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                } else if(id === 4){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#request_claims').removeClass('d-none');
                    $('#description_div').addClass('d-none');
                    $('#editRequest').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#description_div').addClass('d-none');
                    $('#editRequest').addClass('d-none');
                }
            });$('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#edit_request_form')
            });
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#edit_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#edit_request_form')
            });
            $('#edit_request').on('click', function(){
                @if (isset($crm_details->shipment->tracking_number))
                    var tracking_no = @json($crm_details->shipment->tracking_number);
                    $('.tracking_number').val(tracking_no);
                    $('.tracking_number').attr('disabled', true);
                @endif

                $('#editRequestModal').modal('show');
            });
            $("#tag_admin").prepend('<option value="" selected></option>').select2({
                placeholder: "Select User",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#tag_department").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#tag_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#tag_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Type",
                width: '100%',
                dropdownParent: $('#tagModal')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if (id === 1) {
                    $('#admin_tag_div').addClass('d-none');
                    $('#department_tag_div').removeClass('d-none');
                } else if (id === 2) {
                    $('#department_tag_div').addClass('d-none');
                    $('#admin_tag_div').removeClass('d-none');
                } else {
                    $('#admin_tag_div').addClass('d-none');
                    $('#department_tag_div').addClass('d-none');
                }
            });
            $('#tag').on('click', function (e) {
                e.preventDefault();
                $('#tagModal').modal('show');
            });
            $('#un_tag').on('click', function (e) {
                e.preventDefault();
                swal({
                    text: 'Are you sure, you want to un tag this Request?',
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
                            text: 'Request is being un tagged.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $.ajax({
                            url: '{!! route('admin.crm.in_process.un_tag') !!}',
                            method: 'POST',
                            data: {
                                'multiple': 0,
                                'crm_request_id': $('#crm_request_id').val(),
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
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
            $('#tagModal').on('hide.bs.modal', function (e) {
                $('#tag_type').val('').trigger('change');
                $('#admin_tag_div').addClass('d-none');
                $('#department_tag_div').addClass('d-none');
            });
            $('#tag_adminSubmit').on('click', function () {
                var type = parseInt($('#tag_type').val());
                var tag_hub = null;
                console.log(1);
                console.log(tag_hub);
                if (type === 1) {
                    var tag = parseInt($('#tag_department').val());
                    tag_hub = parseInt($('#tag_hub').val());
                    if(!tag_hub){
                        tag_hub = null;
                    }
                }
                else if (type === 2) {
                    var tag = parseInt($('#tag_admin').val());
                }
                if (tag) {
                    $('#tag_adminSubmit').attr('disabled', true);
                    swal({
                        title: 'Please Wait!',
                        text: 'Request is being tagged.',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    $.ajax({
                        url: '{!! route('admin.crm.tag') !!}',
                        method: 'POST',
                        data: {
                            'tagged_id': tag,
                            'tagged_hub': tag_hub,
                            'crm_request_id': $('#crm_request_id').val(),
                            'prev_status': $('#prev_status').val(),
                            'crm_request_tagging_type_id': type,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 0) {
                                $('#tagModal').modal('hide');
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                            else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            swal.close();
                            $('#tag_adminSubmit').attr('disabled', false);
                        });
                }
                else {
                    if (type === 1) {
                        var error = "Department Not Selected!";
                    }
                    else if (type === 2) {
                        var error = "User Not Selected!";
                    }
                    else {
                        error = "Type Not Selected!";
                    }
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            });

            $('#chat_form').on('submit', function (e) {
                e.preventDefault();
            });
            $('body').on('change', '#chat_form input', function () {
                $(this).val($(this).val().trim());
            });

            function last_comment_edit(last_comment, comment){
                $('#edit_comment_' + last_comment).on('click', function (e) {
                var comment_id = $(this).attr("value");
                e.preventDefault();
                swal({
                    text: 'Are you sure, you want to edit this comment as Internal?\n\t "' + comment + '"',
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
            $('.chat_send').on('click', function () {
                var flag = true;
                var comment = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');
                $('#chat_input').val('');
                var request_id = '{{$crm_details->id}}';
                var internal_switch = parseInt($(this).attr('to'));
                var internal_class = '';
                if (internal_switch) {
                    internal_class = 'internal';
                } else {
                    internal_class = '';
                }
                if (comment == '') {
                    flag = false;
                    toastr.error("Please Enter Comment first!", 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                if (flag) {
                    $.ajax({
                        url: '{!! route('admin.crm.comment.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment': comment,
                            'request_id': request_id,
                            'internal_switch': internal_switch
                        }
                    }).done(function (data) {
                        if (data.status) {
                            // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            var user = '{{Auth::user()->name}}';
                            // if($('div.chat:last-child').hasClass('admin')) {
                            //     var html = '<div class="chat-content"><p>' + comment + '</p></div>';
                            //     $('div.chat:last-child').find('.chat-body').append(html);
                            // }else{
                            if (internal_switch) {
                                var html = '<div class="chat admin ' + internal_class + '"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                            } else {
                                var last_comment = data.last_comment_id;

                                var html ='<div id="chat_' + last_comment + '" class="chat admin"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left">';
                                @if(session('role_id') == 1 || in_array(310, session('permissions')))
                                    html += '<button type="button" class="border-0" id="edit_comment_' + last_comment + '" value="' + last_comment + '"><i class="ft-edit"></i></button>';
                                @endif
                                html += '<p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small><div id="updated_by_div_' + last_comment + '"></div></div></div>';
                            }
                            $('section.chat-app-window .chats').append(html);

                            // }

                            $('#last_comment_id').val(data.last_comment_id);
                            last_comment_edit(last_comment, comment);

                            updateScroll();
                        }
                    });
                }
            });
            @if($crm_details->status_id != 4)
            setInterval(function () {
                var last_comment_id = parseInt($('#last_comment_id').val());
                var request_id = '{{$crm_details->id}}';
                get_latest_comment(last_comment_id, request_id);
            }, 10000);

            @endif
            function get_latest_comment(comment_id, request_id) {
                if (comment_id) {
                    $.ajax({
                        url: '{!! route('admin.crm.comment.get') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'comment_id': comment_id,
                            'request_id': request_id
                        }
                    }).done(function (data) {
                        if (data.status) {
                            var user = data.comment.comment_by;
                            var name = data.name;
                            if (user == 0) {
                                if (data.comment.comment_type == 0) {

                                    var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                } else {
                                    var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                }
                                $('section.chat-app-window .chats').append(html);

                            } else if (user == 1) {
                                if ($('div.chat:last-child').hasClass('shipper')) {
                                    var html = '<div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now  ({{Carbon\Carbon::now()}})</small></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                } else {
                                    var html = '<div class="chat chat-left shipper"><div class="chat-avatar"><div class="badge block badge-info"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }
                            } else {
                                if ($('div.chat:last-child').hasClass('substitute-user')) {
                                    var html = '<div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div>';
                                    $('div.chat:last-child').find('.chat-body').append(html);
                                } else {
                                    var html = '<div class="chat chat-left substitute-user"><div class="chat-avatar"><div class="badge block badge-substitute-user"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                    $('section.chat-app-window .chats').append(html);
                                }

                            }
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
            $( "#edit_request_form" ).bind('submit', function (e) {
                e.preventDefault();
                var case_nature_id = parseInt($('#case_nature_select').val());
                var tracking_number = $('.tracking_number').val();
                var nature_flag = true;
                if(case_nature_id === 1 || case_nature_id === 2){
                    if(case_nature_id === 1) {
                        var case_nature_complaint_id = $('#case_nature_complaints').val();
                        var description = $('#description').val();
                        if (!case_nature_complaint_id) {
                            nature_flag = false;
                            var error = "Please select Complaint type!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!description) {
                            nature_flag = false;
                            var error = "Please Enter Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if(!tracking_number){
                            nature_flag = false;
                            var error = "Tracking Number Required!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                    else if(case_nature_id === 2){
                        var case_nature_complaint_id = $('#case_nature_requests').val();
                        var description = $('#description').val();
                        if(!case_nature_complaint_id){
                            nature_flag = false;
                            var error = "Please select Request type!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if (!description) {
                            nature_flag = false;
                            var error = "Please Enter Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if(!tracking_number){
                            nature_flag = false;
                            var error = "Tracking Number Required!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                    if(nature_flag){
                        $('#editRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('admin.crm.request.edit') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'tracking_number': $('.tracking_number').val(),
                                'request_id': $('#request_id').val(),
                                'case_nature_id' : case_nature_id,
                                'complaint_id' : case_nature_complaint_id,
                                'description' : description
                            }
                        })
                            .done(function(data) {
                                if(data.status == 0){
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                    setTimeout(function(){
                                        window.location.reload(1);
                                    }, 1500);
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }

                                $('#editRequestModal').modal('hide');
                                $('#editRequest').attr('disabled',false);
                            });
                    }
                }
                    else if(case_nature_id === 4){
                        var nature_flag = true;
                        var case_nature_claim_id = $('#case_nature_claim').val();
                        var product_cost = $('#claim_product_cost').val();
                        var check_product_picture = $('#product_picture').val();
                        var check_invoice_picture = $('#invoice_picture').val();
                        $('#tracking_number').val(tracking_number);
                        // $('#case_nature_id').val(case_nature_id);
                        // $('#complaint_id').val(case_nature_claim_id);
                        var formData = new FormData($('#edit_request_form')[0]);
                        if(!case_nature_claim_id){
                            nature_flag = false;
                            var error = "Please select Claim type!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!check_product_picture){
                            nature_flag = false;
                            var error = "Please attach Product Picture!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!product_cost){
                            nature_flag = false;
                            var error = "Please enter Product Cost!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!check_invoice_picture){
                            nature_flag = false;
                            var error = "Please attach Invoice Picture!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!tracking_number){
                            nature_flag = false;
                            var error = "Tracking Number Required!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if(nature_flag){
                            $('#editRequestModal').attr('disabled',true);
                            $.ajax({
                                url: '{!! route('admin.crm.request.edit') !!}',
                                method: 'POST',
                                enctype: 'multipart/form-data',
                                data: formData,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                            })
                                .done(function(data) {
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                        setTimeout(function(){
                                            window.location.reload(1);
                                        }, 1500);
                                    }
                                    else {
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                    $('#editRequestModal').modal('hide');
                                    $('#editRequest').attr('disabled',false);
                                });
                        }

                    }
            });
            $('#editRequestModal').on('hide.bs.modal', function (e) {
                $('#edit_request_form')[0].reset();
                $('#case_nature_complaints').val('').trigger('change');
                $('#case_nature_select').val('').trigger('change');
                $('#case_nature_requests').val('').trigger('change');
                $('.tracking_number').val('');
                $('#request_complaints').addClass('d-none');
                $('#request_service').addClass('d-none');
                $('#description_div').addClass('d-none');
                $('#request_claims').addClass('d-none');
                $('#case_nature_claim').val('').trigger('change');
                $('#claim_channel').val('').trigger('change');
                $('#claim_product_cost').val('');
            });
        });

        $('#valid_form').on('submit', function (e) {
            blockPagePermanently();
        });
        $('#invalid_form').on('submit', function (e) {
            blockPagePermanently();
        })

        @foreach($comments as $comment)
            @if($comment->comment_by == 0)
                @if($comment->comment_type == 0 && (session('role_id') == 1 || in_array(310, session('permissions'))))
                    $('#edit_comment_{{$comment->id}}').on('click', function (e) {
                        var comment_id = $(this).attr("value");
                        e.preventDefault();
                        swal({
                            text: 'Are you sure, you want to edit this comment as Internal?\n\t "{{$comment->comment}}"',
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
                                        'comment_id': comment_id,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function (data) {
                                        if (data.status == 0) {
                                            $('#edit_comment_{{$comment->id}}').remove();
                                            $('#chat_{{$comment->id}}').addClass('internal');
                                            $('#updated_by_div_{{$comment->id}}').append('<small>Updated by: ' + data.updated_by + ' (' + data.updated_at + ')</small>');
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
                @endif
            @endif
        @endforeach

    </script>
@endsection