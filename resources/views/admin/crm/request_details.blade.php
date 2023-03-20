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
                            @if(($crm_details['status_id'] == 2))
                                @if((session('role_id') == 1 || in_array(352, session('permissions'))))
                                    <button type="button" class="btn @if($escalation_status_flag == true) btn-danger @else btn-primary @endif width-10-per" id="halt_start_escalation" value="@if($escalation_status_flag == true) 0 @else 1 @endif"><span
                                                class="d-none d-lg-block" style="color: white">@if($escalation_status_flag == true) Halt Escalation @else Start Escalation @endif</span></button>
                                @endif
                                @if((session('role_id') == 1 || in_array(353, session('permissions'))))
                                    @if($escalation_log_flag == true)
                                        <button type="button" class="btn btn-primary width-10-per" id="escalate"><span
                                                    class="d-none d-lg-block" style="color: white">Escalate</span></button>
                                    @endif
                                @endif
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
                                            @if(!empty($crm_historiescount))
                                            <tr>
                                                <th scope="row">Case Nature History</th>
                                                <td class="name">
                                                    <h5 class="mb-0"><u><a  href="#" id="ceditRequestModal" data-toggle="modal" data-target="#myModal">{{str_pad($crm_historiescount+1, 4, '0', STR_PAD_LEFT)}}</a></u></h5>
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
                                                    <th scope="row">Tagged Manual</th>
                                                    <td class="name">
                                                        @if($tagged_name != null)
                                                            <h5 class="mb-0">{{$tagged_name}}</h5>
                                                        @else
                                                            <h5 class="mb-0">-</h5>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Tagged KAE</th>
                                                    <td class="name">
                                                        @if($tagged_kae_name != null)
                                                            <h5 class="mb-0">{{$tagged_kae_name}}</h5>
                                                        @else
                                                            <h5 class="mb-0">-</h5>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Tagged Operation</th>
                                                    <td class="name">
                                                        @if($tagged_operation_name != null)
                                                            <h5 class="mb-0">{{$tagged_operation_name}}</h5>
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
                                                    <h5 class="mb-0">{{strip_tags($crm_details->description)}}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Insurance</th>
                                                <td class="name">
                                                        <h5 class="mb-0">{{$insurance}}</h5>
                                                </td>
                                            </tr>
                                            @if(!empty($approvers))
                                               <tr>
                                                    <th scope="row">Special Request </th>
                                                    <td class="name">
                                                            <h5 class="mb-0">{{$approvers->admin}} {{$approvers->percentage == '' ? '' : ' ('.$approvers->percentage.')'}}</h5>
                                                    </td>
                                                </tr>
                                                @if($approvers->percentage != '')
                                                    <tr>
                                                        <th scope="row">Adjusted Percentage</th>
                                                        <td class="name">
                                                                <h5 class="mb-0">{{$approvers->percentage}}</h5>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Special Request Status</th>
                                                        <td class="name">
                                                                <h5 class="mb-0">Approved</h5>
                                                        </td>
                                                    </tr>
                                                
                                            @endif
                                            @endif
                                            
                                            </tbody>
                                        </table>
                                        <div class="row justify-content-center">
                                            @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')) || (($tag_check['crm_request_tagging_type_id'] == 1 && $tag_check['tagged_id'] == $tag_permission) || ($tag_check['crm_request_tagging_type_id'] == 2 && $tag_check['tagged_id'] == Auth::id()) || $escalation_tagged_check == true || (in_array(session('role_id'), [8, 9 ,10]) && (in_array($crm_details->shipment->pickup_address->city->hub_id, session('hubs')) || in_array($crm_details->shipment->consignee_city->hub_id, session('hubs'))))))
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
                                                                @if(session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id() || in_array(184, session('permissions')) || (($tag_check['crm_request_tagging_type_id'] == 1 && $tag_check['tagged_id'] == $tag_permission) || ($tag_check['crm_request_tagging_type_id'] == 2 && $tag_check['tagged_id'] == Auth::id()) || $escalation_tagged_check == true) || (in_array(session('role_id'), [8, 9 ,10]) && (in_array($crm_details->shipment->pickup_address->city->hub_id, session('hubs')) || in_array($crm_details->shipment->consignee_city->hub_id, session('hubs')))))
                                                                    <button id="valid" type="submit"
                                                                            class="btn btn-success mr-1">
                                                        <span class="d-none d-lg-block">
                                                        Resolve
                                                        </span>
                                                                    </button>
                                                                @endif
                                                            @elseif($crm_details['status_id'] == 4 && (in_array(186, session('permissions')) || session('role_id') == 1 || session('role_id') == 6 || $crm_details->agent['id'] == Auth::id()))
                                                            <input type="hidden" id="valid_close_reason" name="valid_close_reason"
                                                                   value="0">    
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
                                                        <input type="hidden" id="close_reason_status" name="close_reason_status"
                                                                   value="0">
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
                                                        <input type="hidden" id="close_reason" name="close_reason"
                                                                   value="1">
                                                            @if($crm_details['status_id'] == 3)
                                                            <input type="hidden" id="resolved_close_val" name="resolved_close_val"
                                                                   value="0">
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
                                            @if ($crm_details['case_nature_id'] == 4)
                                                @if (session('role_id') == 1 || in_array(523, session('permissions')))
                                                    <button id="special_request" class="btn btn-primary ml-1"><span class="d-none d-lg-block">Special Request</span></button>
                                                @endif
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
                                                                     class="chat admin {{($comment->comment_type == 1)? 'internal':'' }} {{($comment->comment_type == 2)? 'rider':'' }} {{($comment->comment_type == 3)? 'consignee':'' }} ">

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
                                                                            <p>{!! nl2br($comment->comment) !!}</p>
                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>
                                                                                <div id="updated_by_div_{{$comment->id}}">
                                                                                    @if($comment->comment_updated_by != null && $comment->comment_updated_at != null)
                                                                                        <small>Updated by: {{$comment->comment_updated_by->name}} ({{$comment->comment_updated_at}})</small>
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
                                                                            <p>{!! nl2br($comment->comment) !!}</p>
                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                @if($comment->comment_type == 2)
                                                                    <div id="chat_{{$comment->id}}"
                                                                         class="chat admin rider">

                                                                        <div class="chat-avatar">
                                                                            <div class="badge block badge-admin">
                                                                                <i class="la la-user font-medium-2"></i>{{$comment->rider->name}}
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
                                                                                <p>{!! nl2br($comment->comment) !!}</p>
                                                                                <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    @endif


                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </div>
                                            </section>

                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()) || ($crm_details->launched_by == 0 && $crm_details->launched_by_id == Auth::id()) || in_array(201, session('permissions')) || ($sale_person && $sale_person->admin_id == Auth::id()) || (in_array(session('role_id'), [8, 9 ,10]) && (in_array($crm_details->shipment->pickup_address->city->hub_id, session('hubs')) || in_array($crm_details->shipment->consignee_city->hub_id, session('hubs')))))
                                                <section class="chat-app-form">
                                                    <form class="chat-app-input row" id="chat_form">
                                                        <fieldset
                                                                class="form-group position-relative has-icon-left col-9 m-0">
                                                            <input type="hidden" id="last_comment_id"
                                                                   value="{{$last_comment_id}}">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>

                                                            {{--<input type="text" class="form-control" id="chat_input"--}}
                                                                   {{--placeholder="Type your message">--}}
                                                            <textarea id="chat_input" class="form-control height-200" placeholder="Type your message" @if($crm_details->case_nature_id != 3) @if(($crm_details->shipment->shipment_type == 1 && session('department_id') == 8)) disabled @endif @endif></textarea>
                                                        </fieldset>
                                                        <div class="display-inline-block col-3">
                                                            <fieldset
                                                                    class="form-group has-icon-left m-0 mb-1 ml-2">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-purple chat_send" to="1"><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Internal</span>
                                                                </button>
                                                            </fieldset>
                                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()) || in_array(201, session('permissions')))
                                                                @if($crm_details->case_nature_id == 4 && (session('role_id') == 1 || in_array(543, session('permissions'))))
                                                                    <div class="form-group mt-1" style="float: left;">
                                                                        <input type="checkbox" id="email_check" name="email_check">
                                                                    </div>
                                                                @endif
                                                                <fieldset
                                                                        class="form-group position-relative has-icon-left mb-1 ml-2">
                                                                    <button id="chat_send" type="button"
                                                                            class="btn btn-block btn-outline-primary chat_send" to="0">
                                                                        <i class="la la-paper-plane-o d-lg-none"></i>
                                                                        <span class="">Shipper</span>
                                                                    </button>
                                                                </fieldset>
                                                                <fieldset
                                                                        class="form-group position-relative has-icon-left mb-1 ml-2">
                                                                    <button id="chat_send" type="button"
                                                                            class="btn btn-block btn-outline-teal chat_send" to="3">
                                                                        <i class="la la-paper-plane-o d-lg-none"></i>
                                                                        <span class="">Consignee</span>
                                                                    </button>
                                                                </fieldset>
                                                            @endif
                                                            <fieldset
                                                                    class="form-group has-icon-left ml-2">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-outline-dark chat_send" to="2"><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Rider</span>
                                                                </button>
                                                            </fieldset>

                                                            <fieldset class="form-group has-icon-left m-0 mb-1 ml-2" style="float: right;">
                                                                <div class="form-group">
                                                                    <label for="sms_check" class="font-medium-2 text-bold-600 mr-1">Send SMS</label>
                                                                    <input type="checkbox" name="sms_check" id="sms_check" class="switchery sms_check" data-size="sm" data-switchery="true">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </form>
                                                    @if($crm_details->case_nature_id == 4 && (session('role_id') == 1 || in_array(543, session('permissions'))))
                                                        <label><i>Please select checkbox next to Shipper button to send the comment via email to shipper.</i></label>
                                                    @endif
                                                </section>
                                            <div class="row justify-content-center mt-1">
                                                <div class="col-2">
                                                    <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_image', ['id' => $crm_details->id])}}" target="_blank">View Product</a></button>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-primary ml-1"><a class="white" href="{{route('admin.crm.claim.invoice_image', ['id' => $crm_details->id])}}" target="_blank">View Invoice</a></button>
                                                </div>
                                                <div class="col-3">
                                                    <button class="btn btn-social btn-primary mb-1 ml-1" type="button" id="image_upload_btn"><span class="la la-picture-o"></span>Image Upload</button>
                                                </div>
                                                <div class="row">
                                                @if ($crm_details->damage_product_picture != null && $crm_details->product_packaging_picture != null && $crm_details->actual_product_picture != null)
                                                <div class="col-3 mr-2">
                                                    <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.damage_product_image', ['id' => $crm_details->id])}}" target="_blank">View Damage Product</a></button>
                                                </div>
                                            </div>
                                                <div class="row justify-content-center mt-1">
                                                <div class="col-4 mr-2">
                                                    <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_packaging_image', ['id' => $crm_details->id])}}" target="_blank">View Product Packaging</a></button>
                                                </div>
                                                <div class="col-4 ml-4">
                                                    <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.actual_product_image', ['id' => $crm_details->id])}}" target="_blank">View Actual Product</a></button>
                                                </div>
                                                    @endif
                                                </div>
                                                    <div class="row">
                                                    @if ($crm_details->missing_product_picture != null && $crm_details->product_packaging_picture_for_content_short != null && $crm_details->actual_product_picture_for_content_short != null)
                                                    <div class="col-3 mr-2">
                                                        <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.missing_product_image', ['id' => $crm_details->id])}}" target="_blank">View Missing Product</a></button>
                                                    </div>
                                                            <div class="col-3 mr-1">
                                                                <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.actual_product_image_for_content_short', ['id' => $crm_details->id])}}" target="_blank">View Actual Product</a></button>
                                                            </div>
                                                    <div class="col-3">
                                                        <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_packaging_image_for_content_short', ['id' => $crm_details->id])}}" target="_blank">View Product Packaging</a></button>
                                                    </div>
                                                    @endif
                                                        </div>
                                                </div>
                                            @elseif(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || ($crm_details->launched_by == 0 && $crm_details->launched_by_id == Auth::id()) || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ) || $escalation_tagged_check == true)) || ($sale_person && $sale_person->admin_id == Auth::id()))
                                                <section class="chat-app-form">
                                                    <form class="chat-app-input row" id="chat_form">
                                                        <fieldset
                                                                class="form-group position-relative has-icon-left col-9 m-0">
                                                            <input type="hidden" id="last_comment_id"
                                                                   value="{{$last_comment_id}}">
                                                            <div class="form-control-position">
                                                                <i class="la la-chevron-right"></i>
                                                            </div>
                                                            {{--<input type="text" class="form-control" id="chat_input"--}}
                                                                   {{--placeholder="Type your message">--}}
                                                            <textarea id="chat_input" class="form-control height-200" placeholder="Type your message"   @if($crm_details->case_nature_id != 3) @if(($crm_details->shipment->shipment_type == 1 && session('department_id') == 8)) disabled @endif @endif></textarea>
                                                        </fieldset>
                                                        <div class="display-inline-block col-3">
                                                            <fieldset
                                                                    class="form-group position-relative has-icon-left m-0 mb-1">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-purple chat_send" to="1"><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Internal</span>
                                                                </button>
                                                            </fieldset>
                                                            @if(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ) || $escalation_tagged_check == true)))
                                                                @if($crm_details->case_nature_id == 4 && (session('role_id') == 1 || in_array(543, session('permissions'))))
                                                                    <div class="form-group mt-1" style="float: left;">
                                                                        <input type="checkbox" id="email_check" name="email_check">
                                                                    </div>
                                                                @endif
                                                            <fieldset
                                                                    class="form-group position-relative has-icon-left m-0">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-outline-teal chat_send" to="0">
                                                                    <i class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Shipper</span>
                                                                </button>
                                                            </fieldset>
                                                            <fieldset
                                                                    class="form-group position-relative has-icon-left m-0">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-outline-cyan chat_send" to="3">
                                                                    <i class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Consignee</span>
                                                                </button>
                                                            </fieldset>
                                                            @endif
                                                            <fieldset
                                                                    class="form-group has-icon-left">
                                                                <button id="chat_send" type="button"
                                                                        class="btn btn-block btn-outline-dark chat_send" to="2"><i
                                                                            class="la la-paper-plane-o d-lg-none"></i>
                                                                    <span class="">Rider</span>
                                                                </button>
                                                            </fieldset>

                                                            <fieldset class="form-group has-icon-left m-0 mb-1 ml-2" style="float: right;">
                                                                <div class="form-group">
                                                                    <label for="sms_check" class="font-medium-2 text-bold-600 mr-1">Send SMS</label>
                                                                    <input type="checkbox" name="sms_check" id="sms_check" class="switchery sms_check" data-size="sm" data-switchery="true">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        @if($crm_details->case_nature_id == 4 && (session('role_id') == 1 || in_array(543, session('permissions'))))
                                                            <label><i>Please select checkbox next to Shipper button to send the comment via email to shipper.</i></label>
                                                        @endif
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
                                                            @elseif($tagging_history->crm_request_tagging_type_id == 4 || $tagging_history->crm_request_tagging_type_id == 5)
                                                                @if ($tagging_history->user)
                                                                    <td>{{$tagging_history->user->name}}</td>
                                                                @endif
                                                                <td>Auto Tag</td>
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

                                @if(count($crm_escalation_tagging_history) > 0)
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3>Escalation Taging</h3>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                    <tr class="border-bottom-active border-custom-color">
                                                        <th>S No.</th>
                                                        <th>Role | Department</th>
                                                        <th>Hub</th>
                                                        <th>Tagged Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($crm_escalation_tagging_history as $index => $escalation_tagging_history)
                                                        @php $index++; @endphp
                                                        <tr class="border-bottom-success border-custom-color">
                                                            <td>{{$index}}</td>
                                                            <td>{{$escalation_tagging_history->role->name}} | {{$escalation_tagging_history->role->department->name}}</td>
                                                            @if($escalation_tagging_history->hub != NULL)
                                                                <td>{{$escalation_tagging_history->hub->name}}</td>
                                                            @else
                                                                <td>-</td>
                                                            @endif
                                                            <td>{{$escalation_tagging_history->created_at}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(count($crm_sms_history) > 0)
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3>SMS History</h3>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                    <tr class="border-bottom-active border-custom-color">
                                                        <th>S No.</th>
                                                        <th>Agent Name</th>
                                                        <th>Massage</th>
                                                        <th>Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($crm_sms_history as $index => $history)
                                                        @php $index++; @endphp
                                                        <tr class="border-bottom-success border-custom-color">
                                                            <td>{{$index}}</td>
                                                            <td>{{$history->agent}}</td>
                                                            <td>{{$history->massage}}</td>
                                                            <td>{{$history->created_at}}</td>
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
                                            <div class="">
                                                <select id="admin_tag_department"
                                                        class="form-control  select2">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}"> {{$department->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-1">
                                                <select id="admin_tag_hub"
                                                        class="form-control select2">
                                                    @foreach($hubs as $hub)
                                                        <option value="{{$hub->id}}"> {{$hub->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-1">
                                                <select name="tag_admin" id="tag_admin" class="form-control select2">

                                                </select>
                                            </div>
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
        <div class="modal fade text-left" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editRequestModal"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white">Case Nature History</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <table class="table table-bordered" id="crm_image_view_table" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
    
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Case Nature</th>
                                <th class="border-primary border-darken-1">Case Nature Type</th>
    
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($crm_histories))
                                @foreach($crm_histories as $index => $history)
                                    @php $index++; @endphp
                                    <tr class="border-bottom-success border-custom-color">
                                        <td>{{$index}}</td>
                                        <td>{{$history->casenature}}</td>
                                        <td>{{$history->casenaturetype}}</td>
                                    </tr>
                                @endforeach
                            @endif

                                @if(!empty($crm_details->case_nature_type_id))
                                    <tr class="border-bottom-success border-custom-color">
                                        <td><b>{{$crm_details->nature->name}}</b></td>
                                        <td><b>{{$crm_details->nature_type->type}}</b></td>
                                    </tr>
                                @endif
                                </tbody>
                        </table>
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
        <div class="modal fade text-left" id="CloseReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CloseReasonModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Who’s at Fault</h4>
                </div>
                <input type="hidden" name="close_reason_crm_ids" id="close_reason_crm_ids" value="0">
                <div class="modal-body">
                    <select name="closed_reason_status" id="closed_reason_status" class="form-control select2">
                        @foreach($closed_reason_statuses as $closed_reason_status)
                            <option value="{{ $closed_reason_status->id }}" > {{ $closed_reason_status->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="closed_reason_submit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


            <div class="card">
                <div class="card-body text-center">
                    <h2>Feedback</h2>
                    @if(isset($crm_details->feedback))
                    <div class="feedback">
                        @foreach($ratings as $rating)
                            @if($crm_details->feedback->rating_id === $rating->id)
                                <div class="item">
                                    <label for="{{ $rating->id }}" title="{{ $rating->name }}">
                                        <input class="radio" type="radio" name="feedback" id="{{ $rating->id }}" value="{{ $rating->id }}" checked="checked" alt="{{ $rating->name }}" disabled>
                                        <span>{{$rating->code}}</span>
                                    </label>
                                </div>
                            @else
                                <div class="item">
                                    <label for="{{ $rating->id }}" title="{{ $rating->name }}">
                                        <input class="radio" type="radio" name="feedback" id="{{ $rating->id }}" value="{{ $rating->id }}" disabled>
                                        <span>{{$rating->code}}</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach

                    </div>
                    @else
                        <h3>No ratings yet.</h3>
                    @endif
                </div>
            </div>


        @if($escalation_log_flag == true)
            <div class="modal fade text-left" id="escalateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="escalateModal" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content ">
                        <div class="modal-header">
                            <h4 class="modal-title">Escalate</h4>
                        </div>
                        <div class="modal-body text-center">
                            <form id="escalate_submit_form" method="post">
                                @method('POST')
                                @csrf
                                <div class="row justify-content-center">
                                    <div class="col-11">
                                        <fieldset class="form-group">
                                            <input type="hidden" id="escalate_crm_request_id" value="{{$crm_details->id}}">
                                            <input type="hidden" id="escalation_tagging_id" name="escalation_tagging_id"
                                                   value="{{$escalation_tagging_id}}">
                                            <select name="crm_escalation_level" id="crm_escalation_level" class="form-control select2">
                                                @foreach($crm_escalation_levels as $crm_escalation_level)
                                                    <option value="{{$crm_escalation_level->id}}"> {{$crm_escalation_level->name}} </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success width-25-per" id="escalateSubmit">Escalate</button>
                            <button type="button" class="btn btn-info width-25-per" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>


    <div class="modal fade text-left" id="image_upload_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="image_upload_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">CRM Image Upload</h4>

                </div>
                <div class="modal-body  text-center">
                    <table class="table table-bordered" id="crm_image_view_table" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Date Added</th>
                            <th class="border-primary border-darken-1">Image</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>


                    <form id="crm_upload_form" class="form" action="{{route('admin.crm.request.image_submit')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="image_crm_request_id" id="image_crm_request_id"/>
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
                                <button id="CRMImageSubmitButton" type="submit" class="btn btn-primary btn-block" disabled>Upload</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="special_request_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="special_request_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Special Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body  text-center">
                    @if ($special_request_agent == null)
                    <form action="{{route('admin.crm.request.special_request_appvove')}}" method="post">
                        @csrf
                        <input type="hidden" name="request_id" value="{{$crm_details->id}}">

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Admin</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($approvers))
                                   
                                @endif
                                @foreach ($sepcial_request_admins as $special_admin)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                @if(!empty($approvers))
                                                <input class="form-check-input" type="radio" value="{{$special_admin->id}}" name="admin" {{$special_admin->id == $approvers->id ? 'checked' : ' '}}>
                                                @else
                                                    <input class="form-check-input" type="radio" value="{{$special_admin->id}}" name="admin" >
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{$special_admin->name}}</td>
                                    </tr>
                                @endforeach
                            
                            </tbody>
                        </table>

                        <div class="row justify-content-center mt-2 ml-2">
                            <div class="col-4">
                                <button id="special_request_btn" type="submit" class="btn btn-primary btn-block">Request</button>
                            </div>
                        </div>
                    </form>
                    @elseif($special_request_agent != session('id'))
                    <form action="{{route('admin.crm.request.special_request_appvove')}}" method="post">
                        @csrf
                        <input type="hidden" name="request_id" value="{{$crm_details->id}}">

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Admin</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($approvers))
                                   
                                @endif
                                @foreach ($sepcial_request_admins as $special_admin)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                @if(!empty($approvers))
                                                <input class="form-check-input" type="radio" value="{{$special_admin->id}}" name="admin" {{$special_admin->id == $approvers->id ? 'checked' : ' '}}>
                                                @else
                                                    <input class="form-check-input" type="radio" value="{{$special_admin->id}}" name="admin" >
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{$special_admin->name}}</td>
                                    </tr>
                                @endforeach
                            
                            </tbody>
                        </table>

                        <div class="row justify-content-center mt-2 ml-2">
                            <div class="col-4">
                                <button id="special_request_btn" type="submit" class="btn btn-primary btn-block">Request</button>
                            </div>
                        </div>
                    </form>
                    @endif

                    @if ($special_request_agent != null)
                    <hr>
                    <form class="mb-2" action="{{route('admin.crm.request.special_request_adjusted')}}" method="post">
                        @csrf
                        <input type="hidden" name="special_request_agent_id" value="{{$special_request_agent}}">
                        <input type="hidden" name="crm_request_id" value="{{$crm_details->id}}">

                        <div class="row justify-content-center mt-2 ml-2">
                            <div class="col-4">
                                <input type="text" id="adjusted_persentage" name="adjusted_persentage" class="form-control">
                            </div>
                            <div class="col-4">
                                <button id="special_request_approve_btn" type="submit" class="btn btn-primary btn-block">Approve</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    @endif
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

        .feedback .radio:checked ~ span {
            filter: grayscale(0);
            font-size: 4rem;
        }
        .feedback .radio:hover ~ span {
            filter: grayscale(0);
            font-size: 4rem;
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
            $('#claim_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('#adjusted_persentage').inputmask({
                'alias': 'percentage',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 100.00
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

            $("#tag_department , #admin_tag_department").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#tag_hub , #admin_tag_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width: '100%',
                dropdownParent: $('#tagModal')
            });
            $("#closed_reason_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reason",
                width:'100%',
                dropdownParent:$('#CloseReasonModal')
            });
            $("#admin_tag_department , #admin_tag_hub").on('change',function (){
                let dept = $("#admin_tag_department").val();
                let hub = $("#admin_tag_hub").val();

                if(dept != "" && hub != "")
                {
                    $.ajax({
                        url: '{!! route('admin.crm.tag.get_admins') !!}',
                        method: 'POST',
                        data: {
                            'hub': hub,
                            'dept': dept,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 0) {
                                $("#tag_admin").html("<option value='' selected></option>");
                                $.each(data.admins,function (i,admin) {
                                    $("#tag_admin").append("<option value='"+admin.id+"'>"+admin.name+"</option>");
                                });

                                $("#tag_admin").trigger('change');
                            }
                            else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }
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
                $('#admin_tag_hub').val('').trigger('change');
                $('#admin_tag_department').val('').trigger('change');
                $('#admin_tag_div').addClass('d-none');
                $('#department_tag_div').addClass('d-none');
            });
            $('#tag_adminSubmit').on('click', function () {
                var type = parseInt($('#tag_type').val());
                var tag_hub = null;
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
            $('.chat_send').on('click', function () {
                var flag = true;
                var comment = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');
                $('#chat_input').val('');
                var request_id = '{{$crm_details->id}}';
                var internal_switch = parseInt($(this).attr('to'));
                var internal_class = '';
                var email_check = '';
                if($('#email_check').is(":checked")){
                    email_check = true;
                }
                else{
                    email_check = false;
                }

                var sms_check = '';
                if($('#sms_check').is(":checked")){
                    sms_check = true;
                }
                else{
                    sms_check = false;
                }


                if (internal_switch == 1) {
                    internal_class = 'internal';
                }else if(internal_switch === 2){
                    internal_class = 'rider';
                } else if(internal_switch === 3){
                    internal_class = 'consignee';
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
                            'internal_switch': internal_switch,
                            'email_check': email_check,
                            'sms_check': sms_check
                        }
                    }).done(function (data) {
                        if (data.status) {

                            // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            var user = '{{Auth::user()->name}}';
                            // if($('div.chat:last-child').hasClass('admin')) {
                            //     var html = '<div class="chat-content"><p>' + comment + '</p></div>';
                            //     $('div.chat:last-child').find('.chat-body').append(html);
                            // }else{
                            if (internal_switch == 1) {
                                var html = '<div class="chat admin ' + internal_class + '"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                            }else if(internal_switch == 2){
                                var html = '<div class="chat admin ' + internal_class + '"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                            }else if(internal_switch == 3){
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
                                } else if(data.comment.comment_type == 1) {
                                    var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                } else if(data.comment.comment_type == 3) {
                                    var html = '<div class="chat admin consignee"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                } else{
                                    var html = '<div class="chat admin rider"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
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
                                if(data.comment.comment_type == 0){
                                    if ($('div.chat:last-child').hasClass('substitute-user')) {
                                        var html = '<div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div>';
                                        $('div.chat:last-child').find('.chat-body').append(html);
                                    } else {
                                        var html = '<div class="chat chat-left substitute-user"><div class="chat-avatar"><div class="badge block badge-substitute-user"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';
                                        $('section.chat-app-window .chats').append(html);
                                    }
                                }else{
                                    var html = '<div class="chat admin rider"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';

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

            $("#crm_escalation_level").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Escalation",
                width: '100%',
                dropdownParent: $('#escalateModal')
            });
            $('#halt_start_escalation').on('click', function (e) {
                e.preventDefault();
                var request_id = @json($crm_details->id);
                var status = $(this).val();
                if(status == 0){
                    var status_text = 'Halt';
                }
                else{
                    var status_text = 'Start';
                }
                swal({
                    text: 'Are you sure, you want to '+status_text+' Escalation of this Request?',
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
                }).then(function(confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.crm.escalation_status') !!}',
                            method: 'POST',
                            data: {
                                'crm_request_id': request_id,
                                'status': status,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 0) {
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
                            });
                    }
                });
            });
            $('#escalate').on('click', function (e) {
                e.preventDefault();
                $('#escalateModal').modal('show');
            });
            $('#escalateSubmit').on('click', function (e) {
                e.preventDefault();
                var crm_request_id = $('#escalate_crm_request_id').val();
                var escalation_tagging_id = $('#escalation_tagging_id').val();
                var selected_escalation = $('#crm_escalation_level').val();
                var flag = true;
                if(selected_escalation == null || selected_escalation == ''){
                    var error = "Please select Escalation";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    flag = false
                }
                if(flag == true){
                    swal({
                        text: 'Are you sure, you want to Escalate this Request?',
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
                    }).then(function(confirm) {
                        if (confirm){
                            swal({
                                title: 'Please Wait!',
                                text: 'Escalation is in process!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            $.ajax({
                                url: '{!! route('admin.crm.escalate') !!}',
                                method: 'POST',
                                data: {
                                    'crm_request_id': crm_request_id,
                                    'escalation_tagging_id': escalation_tagging_id,
                                    'selected_escalation': selected_escalation,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function(data) {
                                    if (data.status == 0) {
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
                                });
                        }
                    });
                }
            });


            $('#valid_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var close_reason = $('#valid_close_reason').val();
                    var crm_request_id = $('#crm_request_id').val();
                    if(close_reason == 1){
                        $.ajax({
                        url: '{!! route('admin.crm.close_reason') !!}',
                        method: 'POST',
                        data: {
                            'crm_request_id': crm_request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                        }).done(function (data) {
                            if(data.status == 1){
                                $('#CloseReasonModal').modal('show');
                            }else{
                                form.submit();
                            }
                        });
                    }else{
                        form.submit();
                    }
                    
                }
            });
            // $('#valid_form').on('submit', function (e) {
            //     blockPagePermanently();
            // });


            $('#invalid_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var close_reason = $('#close_reason').val();
                    var crm_request_id = $('#crm_request_id').val();
                    if(close_reason == 1){
                        $.ajax({
                        url: '{!! route('admin.crm.close_reason') !!}',
                        method: 'POST',
                        data: {
                            'crm_request_id': crm_request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                        }).done(function (data) {
                            if(data.status == 1){
                                $('#CloseReasonModal').modal('show');
                            }else{
                                form.submit();
                            }
                        });
                    }else{
                        form.submit();
                    }
                    
                }
            });
            // $('#invalid_form').on('submit', function (e) {
                
            //     var close_reason = $('#close_reason').val();
            //     if(close_reason == 1){

            //     }
            //     blockPagePermanently();
            // })

            @foreach($comments as $comment)
            @if($comment->comment_by == 0)
            @if($comment->comment_type == 0 && (session('role_id') == 1 || in_array(310, session('permissions'))))
            $('#edit_comment_{{$comment->id}}').on('click', function (e) {
                var comment_id = $(this).attr("value");
                e.preventDefault();
                var cmt = @json($comment->comment);
                var text_edit  = "Are you sure, you want to edit this comment as Internal? \n \t "+cmt;
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

            var images_count = {{ $crm_images_count }};
            var rows_count = 0;
            var selected_rows = [];
            $('#image_upload_btn').on('click', function () {
                $('#image_upload_btn').attr('disabled', true);
                var crm_request_id = $('#crm_request_id').val();
                if(crm_request_id){
                    $('#image_crm_request_id').val(crm_request_id);
                    $.ajax({
                        url: '{!! route('admin.crm.request.image_details') !!}',
                        method: 'POST',
                        data: {
                            'crm_request_id': crm_request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(data.status == 0){
                            var image_html = '';
                            $.each(data.images, function (index, image) {
                                index++;
                                var img = '<a class="btn btn-sm btn-outline-info align-middle" href="' + image.image + '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                               
                                image_html += '<tr id="' + image.id + '"><td>' + index + '</td><td>' + image.date + '</td><td>' + img + '</td></tr>';
                            });
                            $('#crm_image_view_table tbody').append(image_html);
                            $('#image_upload_modal').modal('show');
                        }else if(data.status == 2){
                            var image_html = '<tr><td colspan="4">No Images found!</td></tr>';

                            $('#crm_image_view_table tbody').append(image_html);
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
            var crm_image_table;
            function add_row() {
                var tr_id = $('#image_upload_table tbody tr').attr('id');
                if (typeof tr_id !== typeof undefined && tr_id !== false) {
                    var new_img_rows = $('#image_upload_table tbody tr').length;
                    new_img_rows = images_count + new_img_rows;
                    if(new_img_rows >= 2){
                        $('#image_upload_table .img_add_btn').attr('disabled', true);
                        return false;
                    }
                }

                rows_count++;

                var crm_image = '<input class="form-control form-control-sm" type="file" name="crm_image_'+rows_count+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">';
                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                }
                crm_image_table.row.add([0, crm_image,remove]).node().id = rows_count;
                crm_image_table.draw(true);
                $('#CRMImageSubmitButton').attr('disabled', false);
                selected_rows.push(rows_count);
            }
            crm_image_table = $('#image_upload_table').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary img_add_btn',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {
                        if(images_count < 2){
                            add_row();
                        }
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
                    var info = crm_image_table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {

                    // this.api().table().columns.adjust();
                }
            });
            $('#crm_image_view_table').on('click','a.remove_row', function () {
                var row_id = $(this).parents('tr').attr('id');
                var crm_request_id = $('#image_crm_request_id').val();
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
                                    'crm_image_id': row_id,
                                    'crm_request_id':crm_request_id,
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
                crm_image_table.row( $(this).parents('tr') ).remove().draw();
            });
            $('#crm_upload_form').validate({

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
                $('#image_crm_request_id').val('');
                crm_image_table.clear();
                crm_image_table.draw();
                selected_rows = [];
                rows_count = 0;
                $('#crm_image_view_table tbody').html('');
            });

            $('#special_request').on('click',function () {
              $('#special_request_modal').modal('show');
            });

            $('#special_request_modal').on('hide.bs.modal', function (e) {
                $('.form-check-input').prop('checked', false);
            });
            $('#CloseReasonModal').on('hide.bs.modal', function (e) {
                $('#closed_reason_status').val('').trigger('change');
            });

            $('#closed_reason_submit').on('click',function () {
                //mark_close
                
                $('#close_reason_status').val($('#closed_reason_status').val());
                $('#CloseReasonModal').modal('hide');
                $('#close_reason').val("0");
                $('form#invalid_form').submit();
            });

            $('#resolved_close').on('click',function () {
                //mark_close
                $('#resolved_close_val').val("1");
                form.submit();
            });

        });



    </script>
@endsection