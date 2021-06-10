@extends('admin.layout.master')

@section('title', 'FTL Request ('.str_pad($ftl->id,4, '0', STR_PAD_LEFT).')')
@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <h1 class="mb-1">
                        FTL Request ({{str_pad($ftl->id, 4, '0', STR_PAD_LEFT)}})
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
                                                    <th scope="row">Shipper</th>
                                                    <td class="name">
                                                        <h5 class="mb-0">
                                                            @if($ftl->shipper_id == null)
                                                                {{$ftl->shipper_name}}
                                                            @else
                                                                {{$ftl->shipper}}
                                                            @endif
                                                            @if($ftl->status_id != 5)
                                                                <button data-toggle="modal" data-target="#EditShipperModal" class="btn btn-info pull-right">Edit</button>
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
                                                        <h5 class="mb-0">{{$ftl->date}}</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="row border-accent-2 justify-content-center">
                                            <div class="text-center col-12">
                                                <form id="update_ftl_request_form" method="post" action="">
                                                    @csrf
                                                    <div class="row mb-2">
                                                            <div class="col-6">
                                                                <select name="vendor" id="vendor" class="form-control select2" data-rule-required="true" data-msg-required="Vendor is required">
                                                                    @foreach($vendors as $vendor)
                                                                        <option value="{{$vendor->id}}"> {{$vendor->name}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="text" name="freight_cost" id="freight_cost" class="form-control" placeholder="Freight Cost">
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
                                                            <button type="button" class="btn btn-info"><i class="fa fa-plus-circle"></i>Add</button>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                       <div class="col-8 offset-2">
                                                            <table class="table table-bordered table-lg">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Amount</th>
                                                                        <th>Cost Type</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                       </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <input type="text" readonly name="total_cost" class="form-control" id="total_cost" placeholder="Total Cost">
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" name="freight_charges" id="freight_charges" class="form-control" placeholder="Freight Charges">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <input type="text" name="gst" readonly class="form-control" id="gst" placeholder="GST">
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" readonly name="total_charges" id="total_charges" class="form-control" placeholder="Total Charges">
                                                        </div>
                                                    </div>
                                                    @if($ftl->status_id != 5)
                                                        <button type="submit" name="btn" value="Update" class="btn btn-secondary mr-1">
                                                            <span class="d-none d-lg-block">
                                                                Update
                                                            </span>
                                                        </button>
                                                    @endif
                                                    @if($ftl->status_id == 2)
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
{{--                                    <div class="col-7">--}}
{{--                                        <div class="content-body chat-application">--}}
{{--                                            <section--}}
{{--                                                    class="chat-app-window vertical-scroll scroll-example height-430 ps-container ps-theme-dark ps-active-y always-visible">--}}
{{--                                                <div class="chats">--}}
{{--                                                    @if(!empty($comments))--}}

{{--                                                        @foreach($comments as $comment)--}}
{{--                                                            @if($comment->comment_by == 0)--}}
{{--                                                                <div id="chat_{{$comment->id}}"--}}
{{--                                                                     class="chat admin {{($comment->comment_type == 1)? 'internal':'' }} {{($comment->comment_type == 2)? 'rider':'' }} ">--}}

{{--                                                                    <div class="chat-avatar">--}}
{{--                                                                        <div class="badge block badge-admin">--}}
{{--                                                                            <i class="la la-user font-medium-2"></i>{{$comment->admin->name}}--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}

{{--                                                                    <div class="chat-body">--}}
{{--                                                                        <div class="chat-content text-left">--}}
{{--                                                                            @if($comment->comment_type == 0 && (session('role_id') == 1 || in_array(310, session('permissions'))))--}}
{{--                                                                                <button type="button" class="border-0" id="edit_comment_{{$comment->id}}" value="{{$comment->id}}"><i class="ft-edit"></i></button>--}}
{{--                                                                            @endif--}}
{{--                                                                            <p>{!! $comment->comment !!}</p>--}}
{{--                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>--}}
{{--                                                                            <div id="updated_by_div_{{$comment->id}}">--}}
{{--                                                                                @if($comment->comment_updated_by != null && $comment->comment_updated_at != null)--}}
{{--                                                                                    <small>Updated by: {{$comment->updated_by_admin->name}} ({{$comment->comment_updated_at}})</small>--}}
{{--                                                                                @endif--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}

{{--                                                                </div>--}}

{{--                                                            @elseif($comment->comment_by == 1)--}}
{{--                                                                <div class="chat chat-left shipper">--}}

{{--                                                                    <div class="chat-avatar">--}}
{{--                                                                        <div class="badge block badge-info">--}}
{{--                                                                            <i class="la la-user font-medium-2"></i>--}}
{{--                                                                            @if($shipper != null)--}}
{{--                                                                                {{$shipper}}--}}
{{--                                                                            @else--}}
{{--                                                                                Shipper--}}
{{--                                                                            @endif--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}

{{--                                                                    <div class="chat-body">--}}
{{--                                                                        <div class="chat-content text-left">--}}
{{--                                                                            <p>{!! $comment->comment !!}</p>--}}
{{--                                                                            <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            @else--}}
{{--                                                                @if($comment->comment_type == 2)--}}
{{--                                                                    <div id="chat_{{$comment->id}}"--}}
{{--                                                                         class="chat admin rider">--}}

{{--                                                                        <div class="chat-avatar">--}}
{{--                                                                            <div class="badge block badge-admin">--}}
{{--                                                                                <i class="la la-user font-medium-2"></i>{{$comment->rider->name}}--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}

{{--                                                                        <div class="chat-body">--}}
{{--                                                                            <div class="chat-content text-left">--}}
{{--                                                                                <p>{!! $comment->comment !!}</p>--}}
{{--                                                                                <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}

{{--                                                                    </div>--}}
{{--                                                                @else--}}
{{--                                                                    <div class="chat chat-left substitute-user">--}}

{{--                                                                        <div class="chat-avatar">--}}
{{--                                                                            <div class="badge block badge-substitute-user">--}}
{{--                                                                                <i class="la la-user font-medium-2"></i>--}}
{{--                                                                                @if($shipper != null)--}}
{{--                                                                                    {{$shipper}}--}}
{{--                                                                                @else--}}
{{--                                                                                    Shipper--}}
{{--                                                                                @endif--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}

{{--                                                                        <div class="chat-body">--}}
{{--                                                                            <div class="chat-content text-left">--}}
{{--                                                                                <p>{!! $comment->comment !!}</p>--}}
{{--                                                                                <small>{{str_replace("after", "ago", \Carbon\Carbon::now()->diffForHumans($comment->created_at))}} ({{$comment->created_at}})</small>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}

{{--                                                                @endif--}}


{{--                                                            @endif--}}
{{--                                                        @endforeach--}}
{{--                                                    @endif--}}

{{--                                                </div>--}}
{{--                                            </section>--}}

{{--                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()) || ($crm_details->launched_by == 0 && $crm_details->launched_by_id == Auth::id()) || in_array(201, session('permissions')) || ($sale_person && $sale_person->admin_id == Auth::id()) || (in_array(session('role_id'), [8, 9 ,10]) && (in_array($crm_details->shipment->pickup_address->city->hub_id, session('hubs')) || in_array($crm_details->shipment->consignee_city->hub_id, session('hubs')))))--}}
{{--                                                <section class="chat-app-form">--}}
{{--                                                    <form class="chat-app-input row" id="chat_form">--}}
{{--                                                        <fieldset--}}
{{--                                                                class="form-group position-relative has-icon-left col-10 m-0">--}}
{{--                                                            <input type="hidden" id="last_comment_id"--}}
{{--                                                                   value="{{$last_comment_id}}">--}}
{{--                                                            <div class="form-control-position">--}}
{{--                                                                <i class="la la-chevron-right"></i>--}}
{{--                                                            </div>--}}
{{--                                                            --}}{{--<input type="text" class="form-control" id="chat_input"--}}
{{--                                                            --}}{{--placeholder="Type your message">--}}
{{--                                                            <textarea id="chat_input" class="form-control height-150" placeholder="Type your message"></textarea>--}}
{{--                                                        </fieldset>--}}
{{--                                                        <div class="display-inline-block col-2">--}}
{{--                                                            <fieldset--}}
{{--                                                                    class="form-group has-icon-left m-0 mb-1">--}}
{{--                                                                <button id="chat_send" type="button"--}}
{{--                                                                        class="btn btn-block btn-purple chat_send" to="1"><i--}}
{{--                                                                            class="la la-paper-plane-o d-lg-none"></i>--}}
{{--                                                                    <span class="">Internal</span>--}}
{{--                                                                </button>--}}
{{--                                                            </fieldset>--}}
{{--                                                            @if(session('role_id') == 1 || session('role_id') == 6 || ($crm_details->status_id == 1 &&  $crm_details->agent_id == Auth::id()) || in_array(201, session('permissions')))--}}
{{--                                                                <fieldset--}}
{{--                                                                        class="form-group position-relative has-icon-left mb-1">--}}
{{--                                                                    <button id="chat_send" type="button"--}}
{{--                                                                            class="btn btn-block btn-outline-primary chat_send" to="0">--}}
{{--                                                                        <i class="la la-paper-plane-o d-lg-none"></i>--}}
{{--                                                                        <span class="">Shipper</span>--}}
{{--                                                                    </button>--}}
{{--                                                                </fieldset>--}}
{{--                                                            @endif--}}
{{--                                                            <fieldset--}}
{{--                                                                    class="form-group has-icon-left">--}}
{{--                                                                <button id="chat_send" type="button"--}}
{{--                                                                        class="btn btn-block btn-outline-dark chat_send" to="2"><i--}}
{{--                                                                            class="la la-paper-plane-o d-lg-none"></i>--}}
{{--                                                                    <span class="">Rider</span>--}}
{{--                                                                </button>--}}
{{--                                                            </fieldset>--}}

{{--                                                        </div>--}}
{{--                                                    </form>--}}
{{--                                                </section>--}}
{{--                                                <div class="row justify-content-center mt-1">--}}
{{--                                                    <div class="col-2">--}}
{{--                                                        <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_image', ['id' => $crm_details->id])}}" target="_blank">View Product</a></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="col-2">--}}
{{--                                                        <button class="btn btn-primary ml-1"><a class="white" href="{{route('admin.crm.claim.invoice_image', ['id' => $crm_details->id])}}" target="_blank">View Invoice</a></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="col-3">--}}
{{--                                                        <button class="btn btn-social btn-primary mb-1 ml-1" type="button" id="image_upload_btn"><span class="la la-picture-o"></span>Image Upload</button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="row">--}}
{{--                                                        @if ($crm_details->damage_product_picture != null && $crm_details->product_packaging_picture != null && $crm_details->actual_product_picture != null)--}}
{{--                                                            <div class="col-3 mr-2">--}}
{{--                                                                <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.damage_product_image', ['id' => $crm_details->id])}}" target="_blank">View Damage Product</a></button>--}}
{{--                                                            </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="row justify-content-center mt-1">--}}
{{--                                                        <div class="col-4 mr-2">--}}
{{--                                                            <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_packaging_image', ['id' => $crm_details->id])}}" target="_blank">View Product Packaging</a></button>--}}
{{--                                                        </div>--}}
{{--                                                        <div class="col-4 ml-4">--}}
{{--                                                            <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.actual_product_image', ['id' => $crm_details->id])}}" target="_blank">View Actual Product</a></button>--}}
{{--                                                        </div>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                    <div class="row">--}}
{{--                                                        @if ($crm_details->missing_product_picture != null && $crm_details->product_packaging_picture_for_content_short != null && $crm_details->actual_product_picture_for_content_short != null)--}}
{{--                                                            <div class="col-3 mr-2">--}}
{{--                                                                <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.missing_product_image', ['id' => $crm_details->id])}}" target="_blank">View Missing Product</a></button>--}}
{{--                                                            </div>--}}
{{--                                                            <div class="col-3 mr-1">--}}
{{--                                                                <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.actual_product_image_for_content_short', ['id' => $crm_details->id])}}" target="_blank">View Actual Product</a></button>--}}
{{--                                                            </div>--}}
{{--                                                            <div class="col-3">--}}
{{--                                                                <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_packaging_image_for_content_short', ['id' => $crm_details->id])}}" target="_blank">View Product Packaging</a></button>--}}
{{--                                                            </div>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            @elseif(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || ($crm_details->launched_by == 0 && $crm_details->launched_by_id == Auth::id()) || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ) || $escalation_tagged_check == true)) || ($sale_person && $sale_person->admin_id == Auth::id()))--}}
{{--                                                <section class="chat-app-form">--}}
{{--                                                    <form class="chat-app-input row" id="chat_form">--}}
{{--                                                        <fieldset--}}
{{--                                                                class="form-group position-relative has-icon-left col-10 m-0">--}}
{{--                                                            <input type="hidden" id="last_comment_id"--}}
{{--                                                                   value="{{$last_comment_id}}">--}}
{{--                                                            <div class="form-control-position">--}}
{{--                                                                <i class="la la-chevron-right"></i>--}}
{{--                                                            </div>--}}
{{--                                                            --}}{{--<input type="text" class="form-control" id="chat_input"--}}
{{--                                                            --}}{{--placeholder="Type your message">--}}
{{--                                                            <textarea id="chat_input" class="form-control height-150" placeholder="Type your message"></textarea>--}}
{{--                                                        </fieldset>--}}
{{--                                                        <div class="display-inline-block col-2">--}}
{{--                                                            <fieldset--}}
{{--                                                                    class="form-group position-relative has-icon-left m-0 mb-1">--}}
{{--                                                                <button id="chat_send" type="button"--}}
{{--                                                                        class="btn btn-block btn-purple chat_send" to="1"><i--}}
{{--                                                                            class="la la-paper-plane-o d-lg-none"></i>--}}
{{--                                                                    <span class="">Internal</span>--}}
{{--                                                                </button>--}}
{{--                                                            </fieldset>--}}
{{--                                                            @if(session('role_id') == 1 || session('role_id') == 6 || (($crm_details->status_id == 2 || $crm_details->status_id == 3) &&  ($crm_details->agent_id == Auth::id() || (!empty($crm_tagging) ? ($crm_tagging->crm_request_tagging_type_id == 1)? $crm_tagging->tagged_id == session('department_id'): $crm_tagging->tagged_id == Auth::id() : false ) || $escalation_tagged_check == true)))--}}
{{--                                                                <fieldset--}}
{{--                                                                        class="form-group position-relative has-icon-left m-0">--}}
{{--                                                                    <button id="chat_send" type="button"--}}
{{--                                                                            class="btn btn-block btn-outline-primary chat_send" to="0">--}}
{{--                                                                        <i class="la la-paper-plane-o d-lg-none"></i>--}}
{{--                                                                        <span class="">Shipper</span>--}}
{{--                                                                    </button>--}}
{{--                                                                </fieldset>--}}
{{--                                                            @endif--}}
{{--                                                            <fieldset--}}
{{--                                                                    class="form-group has-icon-left">--}}
{{--                                                                <button id="chat_send" type="button"--}}
{{--                                                                        class="btn btn-block btn-outline-dark chat_send" to="2"><i--}}
{{--                                                                            class="la la-paper-plane-o d-lg-none"></i>--}}
{{--                                                                    <span class="">Rider</span>--}}
{{--                                                                </button>--}}
{{--                                                            </fieldset>--}}
{{--                                                        </div>--}}
{{--                                                    </form>--}}
{{--                                                </section>--}}
{{--                                                <div class="row justify-content-center mt-1">--}}
{{--                                                    <div class="col-2">--}}
{{--                                                        <button class="btn btn-primary"><a class="white" href="{{route('admin.crm.claim.product_image', ['id' => $crm_details->id])}}" target="_blank">View Product</a></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="col-2">--}}
{{--                                                        <button class="btn btn-primary ml-1"><a class="white" href="{{route('admin.crm.claim.invoice_image', ['id' => $crm_details->id])}}" target="_blank">View Invoice</a></button>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}

{{--                                        </div>--}}

{{--                                    </div>--}}
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
        });
            // $('#claim_product_cost').inputmask({
            //     'alias': 'decimal',
            //     'allowMinus': false,
            //     'allowPlus': false,
            //     'rightAlign': false,
            //     'digits': 2,
            //     'min': 0.00,
            //     'max': 1000000.00
            // });

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
        {{--    $('.tracking_number').inputmask({--}}
        {{--        'alias': 'integer',--}}
        {{--        'allowMinus': false,--}}
        {{--        'allowPlus': false,--}}
        {{--        dropdownParent:$('#edit_request_form')--}}
        {{--    });--}}
        {{--    $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({--}}
        {{--        width:'100%',--}}
        {{--        placeholder:"Select Case Nature",--}}
        {{--        allowClear:true,--}}
        {{--        dropdownParent:$('#edit_request_form')--}}
        {{--    }).bind('change', function () {--}}
        {{--        var id = parseInt($(this).val());--}}
        {{--        if(id === 1){--}}
        {{--            $('#request_service').addClass('d-none');--}}
        {{--            $('#request_complaints').removeClass('d-none');--}}
        {{--            $('#description_div').removeClass('d-none');--}}
        {{--            $('#request_feedback').addClass('d-none');--}}
        {{--            $('#editRequest').removeClass('d-none');--}}
        {{--            $('#request_claims').addClass('d-none');--}}
        {{--        }else if(id === 2){--}}
        {{--            $('#request_complaints').addClass('d-none');--}}
        {{--            $('#request_service').removeClass('d-none');--}}
        {{--            $('#description_div').removeClass('d-none');--}}
        {{--            $('#request_feedback').addClass('d-none');--}}
        {{--            $('#editRequest').removeClass('d-none');--}}
        {{--            $('#request_claims').addClass('d-none');--}}
        {{--        } else if(id === 4){--}}
        {{--            $('#request_complaints').addClass('d-none');--}}
        {{--            $('#request_service').addClass('d-none');--}}
        {{--            $('#request_feedback').addClass('d-none');--}}
        {{--            $('#request_claims').removeClass('d-none');--}}
        {{--            $('#description_div').addClass('d-none');--}}
        {{--            $('#editRequest').removeClass('d-none');--}}
        {{--        }else{--}}
        {{--            $('#request_complaints').addClass('d-none');--}}
        {{--            $('#request_service').addClass('d-none');--}}
        {{--            $('#description_div').addClass('d-none');--}}
        {{--            $('#editRequest').addClass('d-none');--}}
        {{--        }--}}
        {{--    });$('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({--}}
        {{--        width:'100%',--}}
        {{--        placeholder:"Select Complaint Type",--}}
        {{--        allowClear:true,--}}
        {{--        dropdownParent:$('#edit_request_form')--}}
        {{--    });--}}
        {{--    $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({--}}
        {{--        width:'100%',--}}
        {{--        placeholder:"Select Claim Type",--}}
        {{--        allowClear:true,--}}
        {{--        dropdownParent:$('#edit_request_form')--}}
        {{--    });--}}
        {{--    $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({--}}
        {{--        width:'100%',--}}
        {{--        placeholder:"Select Request Type",--}}
        {{--        allowClear:true,--}}
        {{--        dropdownParent:$('#edit_request_form')--}}
        {{--    });--}}
        {{--    $('#edit_request').on('click', function(){--}}
        {{--        @if (isset($crm_details->shipment->tracking_number))--}}
        {{--        var tracking_no = @json($crm_details->shipment->tracking_number);--}}
        {{--        $('.tracking_number').val(tracking_no);--}}
        {{--        $('.tracking_number').attr('disabled', true);--}}
        {{--        @endif--}}

        {{--        $('#editRequestModal').modal('show');--}}
        {{--    });--}}
        {{--    $("#tag_admin").prepend('<option value="" selected></option>').select2({--}}
        {{--        placeholder: "Select User",--}}
        {{--        width: '100%',--}}
        {{--        dropdownParent: $('#tagModal')--}}
        {{--    });--}}

        {{--    $("#tag_department").prepend('<option value="" selected></option>').select2({--}}
        {{--        placeholder: "Select Department",--}}
        {{--        width: '100%',--}}
        {{--        dropdownParent: $('#tagModal')--}}
        {{--    });--}}

        {{--    $("#tag_hub").prepend('<option value="" selected></option>').select2({--}}
        {{--        placeholder: "Select Hub",--}}
        {{--        width: '100%',--}}
        {{--        dropdownParent: $('#tagModal')--}}
        {{--    });--}}

        {{--    $("#tag_type").prepend('<option value="" selected></option>').select2({--}}
        {{--        placeholder: "Select Type",--}}
        {{--        width: '100%',--}}
        {{--        dropdownParent: $('#tagModal')--}}
        {{--    }).bind('change', function () {--}}
        {{--        var id = parseInt($(this).val());--}}
        {{--        if (id === 1) {--}}
        {{--            $('#admin_tag_div').addClass('d-none');--}}
        {{--            $('#department_tag_div').removeClass('d-none');--}}
        {{--        } else if (id === 2) {--}}
        {{--            $('#department_tag_div').addClass('d-none');--}}
        {{--            $('#admin_tag_div').removeClass('d-none');--}}
        {{--        } else {--}}
        {{--            $('#admin_tag_div').addClass('d-none');--}}
        {{--            $('#department_tag_div').addClass('d-none');--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $('#tag').on('click', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--        $('#tagModal').modal('show');--}}
        {{--    });--}}
        {{--    $('#un_tag').on('click', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--        swal({--}}
        {{--            text: 'Are you sure, you want to un tag this Request?',--}}
        {{--            icon: 'info',--}}
        {{--            buttons: {--}}
        {{--                cancel: {--}}
        {{--                    text: 'No',--}}
        {{--                    value: null,--}}
        {{--                    visible: true,--}}
        {{--                    closeModal: true,--}}
        {{--                },--}}
        {{--                confirm: {--}}
        {{--                    text: 'Yes',--}}
        {{--                    value: true,--}}
        {{--                    visible: true,--}}
        {{--                    closeModal: true--}}
        {{--                }--}}
        {{--            },--}}
        {{--            closeOnClickOutside: false,--}}
        {{--            closeOnEsc: false,--}}
        {{--            dangerMode: true--}}
        {{--        }).then(function(confirm) {--}}
        {{--            if(confirm){--}}
        {{--                swal({--}}
        {{--                    title: 'Please Wait!',--}}
        {{--                    text: 'Request is being un tagged.',--}}
        {{--                    icon: 'info',--}}
        {{--                    buttons: false,--}}
        {{--                    closeOnClickOutside: false,--}}
        {{--                    closeOnEsc: false--}}
        {{--                });--}}
        {{--                $.ajax({--}}
        {{--                    url: '{!! route('admin.crm.in_process.un_tag') !!}',--}}
        {{--                    method: 'POST',--}}
        {{--                    data: {--}}
        {{--                        'multiple': 0,--}}
        {{--                        'crm_request_id': $('#crm_request_id').val(),--}}
        {{--                        '_token': '{{ csrf_token() }}'--}}
        {{--                    }--}}
        {{--                })--}}
        {{--                    .done(function (data) {--}}
        {{--                        if (data.status == 0) {--}}
        {{--                            toastr.success(data.success, 'Success!', {--}}
        {{--                                positionClass: 'toast-bottom-center',--}}
        {{--                                containerId: 'toast-bottom-center'--}}
        {{--                            });--}}
        {{--                            setTimeout(function () {--}}
        {{--                                window.location.reload();--}}
        {{--                            }, 2000);--}}
        {{--                        }--}}
        {{--                        else {--}}
        {{--                            toastr.error(data.error, 'Error!', {--}}
        {{--                                positionClass: 'toast-top-center',--}}
        {{--                                containerId: 'toast-top-center'--}}
        {{--                            });--}}
        {{--                        }--}}
        {{--                        swal.close();--}}
        {{--                    });--}}
        {{--            }--}}
        {{--        });--}}
        {{--    });--}}
        {{--    $('#tagModal').on('hide.bs.modal', function (e) {--}}
        {{--        $('#tag_type').val('').trigger('change');--}}
        {{--        $('#admin_tag_div').addClass('d-none');--}}
        {{--        $('#department_tag_div').addClass('d-none');--}}
        {{--    });--}}
        {{--    $('#tag_adminSubmit').on('click', function () {--}}
        {{--        var type = parseInt($('#tag_type').val());--}}
        {{--        var tag_hub = null;--}}
        {{--        if (type === 1) {--}}
        {{--            var tag = parseInt($('#tag_department').val());--}}
        {{--            tag_hub = parseInt($('#tag_hub').val());--}}
        {{--            if(!tag_hub){--}}
        {{--                tag_hub = null;--}}
        {{--            }--}}
        {{--        }--}}
        {{--        else if (type === 2) {--}}
        {{--            var tag = parseInt($('#tag_admin').val());--}}
        {{--        }--}}
        {{--        if (tag) {--}}
        {{--            $('#tag_adminSubmit').attr('disabled', true);--}}
        {{--            swal({--}}
        {{--                title: 'Please Wait!',--}}
        {{--                text: 'Request is being tagged.',--}}
        {{--                icon: 'info',--}}
        {{--                buttons: false,--}}
        {{--                closeOnClickOutside: false,--}}
        {{--                closeOnEsc: false--}}
        {{--            });--}}
        {{--            $.ajax({--}}
        {{--                url: '{!! route('admin.crm.tag') !!}',--}}
        {{--                method: 'POST',--}}
        {{--                data: {--}}
        {{--                    'tagged_id': tag,--}}
        {{--                    'tagged_hub': tag_hub,--}}
        {{--                    'crm_request_id': $('#crm_request_id').val(),--}}
        {{--                    'prev_status': $('#prev_status').val(),--}}
        {{--                    'crm_request_tagging_type_id': type,--}}
        {{--                    '_token': '{{ csrf_token() }}'--}}
        {{--                }--}}
        {{--            })--}}
        {{--                .done(function (data) {--}}
        {{--                    if (data.status == 0) {--}}
        {{--                        $('#tagModal').modal('hide');--}}
        {{--                        toastr.success(data.success, 'Success!', {--}}
        {{--                            positionClass: 'toast-bottom-center',--}}
        {{--                            containerId: 'toast-bottom-center'--}}
        {{--                        });--}}
        {{--                        setTimeout(function () {--}}
        {{--                            window.location.reload();--}}
        {{--                        }, 2000);--}}
        {{--                    }--}}
        {{--                    else {--}}
        {{--                        toastr.error(data.error, 'Error!', {--}}
        {{--                            positionClass: 'toast-top-center',--}}
        {{--                            containerId: 'toast-top-center'--}}
        {{--                        });--}}
        {{--                    }--}}
        {{--                    swal.close();--}}
        {{--                    $('#tag_adminSubmit').attr('disabled', false);--}}
        {{--                });--}}
        {{--        }--}}
        {{--        else {--}}
        {{--            if (type === 1) {--}}
        {{--                var error = "Department Not Selected!";--}}
        {{--            }--}}
        {{--            else if (type === 2) {--}}
        {{--                var error = "User Not Selected!";--}}
        {{--            }--}}
        {{--            else {--}}
        {{--                error = "Type Not Selected!";--}}
        {{--            }--}}
        {{--            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--        }--}}

        {{--    });--}}

        {{--    $('#chat_form').on('submit', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--    });--}}
        {{--    $('body').on('change', '#chat_form input', function () {--}}
        {{--        $(this).val($(this).val().trim());--}}
        {{--    });--}}

        {{--    function last_comment_edit(last_comment, comment){--}}
        {{--        $('#edit_comment_' + last_comment).on('click', function (e) {--}}
        {{--            var comment_id = $(this).attr("value");--}}
        {{--            e.preventDefault();--}}
        {{--            var text_edit  = "Are you sure, you want to edit this comment as Internal? \n \t "+comment;--}}
        {{--            swal({--}}
        {{--                text: text_edit,--}}
        {{--                icon: 'info',--}}
        {{--                buttons: {--}}
        {{--                    cancel: {--}}
        {{--                        text: 'No',--}}
        {{--                        value: null,--}}
        {{--                        visible: true,--}}
        {{--                        closeModal: true,--}}
        {{--                    },--}}
        {{--                    confirm: {--}}
        {{--                        text: 'Yes',--}}
        {{--                        value: true,--}}
        {{--                        visible: true,--}}
        {{--                        closeModal: true--}}
        {{--                    }--}}
        {{--                },--}}
        {{--                closeOnClickOutside: false,--}}
        {{--                closeOnEsc: false,--}}
        {{--                dangerMode: true--}}
        {{--            }).then(function(confirm) {--}}
        {{--                if(confirm){--}}
        {{--                    swal({--}}
        {{--                        title: 'Please Wait!',--}}
        {{--                        text: 'Comment is being updated.',--}}
        {{--                        icon: 'info',--}}
        {{--                        buttons: false,--}}
        {{--                        closeOnClickOutside: false,--}}
        {{--                        closeOnEsc: false--}}
        {{--                    });--}}
        {{--                    $.ajax({--}}
        {{--                        url: '{!! route('admin.crm.comment.edit') !!}',--}}
        {{--                        method: 'POST',--}}
        {{--                        data: {--}}
        {{--                            'comment_id': last_comment,--}}
        {{--                            '_token': '{{ csrf_token() }}'--}}
        {{--                        }--}}
        {{--                    })--}}
        {{--                        .done(function (data) {--}}
        {{--                            if (data.status == 0) {--}}
        {{--                                $('#edit_comment_' + last_comment).remove();--}}
        {{--                                $('#chat_' + last_comment).addClass('internal');--}}
        {{--                                $('#updated_by_div_' + last_comment).append('<small>Updated by: ' + data.updated_by + ' (' + data.updated_at + ')</small>');--}}
        {{--                                toastr.success(data.success, 'Success!', {--}}
        {{--                                    positionClass: 'toast-bottom-center',--}}
        {{--                                    containerId: 'toast-bottom-center'--}}
        {{--                                });--}}
        {{--                            }--}}
        {{--                            else {--}}
        {{--                                toastr.error(data.error, 'Error!', {--}}
        {{--                                    positionClass: 'toast-top-center',--}}
        {{--                                    containerId: 'toast-top-center'--}}
        {{--                                });--}}
        {{--                            }--}}
        {{--                            swal.close();--}}
        {{--                        });--}}
        {{--                }--}}
        {{--            });--}}
        {{--        });--}}
        {{--    }--}}
        {{--    $('.chat_send').on('click', function () {--}}
        {{--        var flag = true;--}}
        {{--        var comment = $('#chat_input').val().replace(/(?:\r\n|\r|\n)/g, '<br/>');--}}
        {{--        $('#chat_input').val('');--}}
        {{--        var request_id = '{{$crm_details->id}}';--}}
        {{--        var internal_switch = parseInt($(this).attr('to'));--}}
        {{--        var internal_class = '';--}}

        {{--        if (internal_switch == 1) {--}}
        {{--            internal_class = 'internal';--}}
        {{--        }else if(internal_switch === 2){--}}
        {{--            internal_class = 'rider';--}}
        {{--        } else {--}}
        {{--            internal_class = '';--}}
        {{--        }--}}
        {{--        if (comment == '') {--}}
        {{--            flag = false;--}}
        {{--            toastr.error("Please Enter Comment first!", 'Error!', {--}}
        {{--                positionClass: 'toast-top-center',--}}
        {{--                containerId: 'toast-top-center'--}}
        {{--            });--}}
        {{--        }--}}
        {{--        if (flag) {--}}
        {{--            $.ajax({--}}
        {{--                url: '{!! route('admin.crm.comment.add') !!}',--}}
        {{--                method: 'POST',--}}
        {{--                data: {--}}
        {{--                    '_token': '{{ csrf_token() }}',--}}
        {{--                    'comment': comment,--}}
        {{--                    'request_id': request_id,--}}
        {{--                    'internal_switch': internal_switch--}}
        {{--                }--}}
        {{--            }).done(function (data) {--}}
        {{--                if (data.status) {--}}

        {{--                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
        {{--                    var user = '{{Auth::user()->name}}';--}}
        {{--                    // if($('div.chat:last-child').hasClass('admin')) {--}}
        {{--                    //     var html = '<div class="chat-content"><p>' + comment + '</p></div>';--}}
        {{--                    //     $('div.chat:last-child').find('.chat-body').append(html);--}}
        {{--                    // }else{--}}
        {{--                    if (internal_switch == 1) {--}}
        {{--                        var html = '<div class="chat admin ' + internal_class + '"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                    }else if(internal_switch == 2){--}}
        {{--                        var html = '<div class="chat admin ' + internal_class + '"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                    } else {--}}
        {{--                        var last_comment = data.last_comment_id;--}}

        {{--                        var html ='<div id="chat_' + last_comment + '" class="chat admin"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left">';--}}
        {{--                        @if(session('role_id') == 1 || in_array(310, session('permissions')))--}}
        {{--                            html += '<button type="button" class="border-0" id="edit_comment_' + last_comment + '" value="' + last_comment + '"><i class="ft-edit"></i></button>';--}}
        {{--                        @endif--}}
        {{--                            html += '<p>' + comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small><div id="updated_by_div_' + last_comment + '"></div></div></div>';--}}
        {{--                    }--}}
        {{--                    $('section.chat-app-window .chats').append(html);--}}

        {{--                    // }--}}

        {{--                    $('#last_comment_id').val(data.last_comment_id);--}}
        {{--                    last_comment_edit(last_comment, comment);--}}

        {{--                    updateScroll();--}}
        {{--                }--}}
        {{--            });--}}
        {{--        }--}}
        {{--    });--}}
        {{--    @if($crm_details->status_id != 4)--}}
        {{--    setInterval(function () {--}}
        {{--        var last_comment_id = parseInt($('#last_comment_id').val());--}}
        {{--        var request_id = '{{$crm_details->id}}';--}}
        {{--        get_latest_comment(last_comment_id, request_id);--}}
        {{--    }, 10000);--}}

        {{--    @endif--}}
        {{--    function get_latest_comment(comment_id, request_id) {--}}
        {{--        if (comment_id) {--}}
        {{--            $.ajax({--}}
        {{--                url: '{!! route('admin.crm.comment.get') !!}',--}}
        {{--                method: 'POST',--}}
        {{--                data: {--}}
        {{--                    '_token': '{{ csrf_token() }}',--}}
        {{--                    'comment_id': comment_id,--}}
        {{--                    'request_id': request_id--}}
        {{--                }--}}
        {{--            }).done(function (data) {--}}
        {{--                if (data.status) {--}}
        {{--                    var user = data.comment.comment_by;--}}
        {{--                    var name = data.name;--}}
        {{--                    if (user == 0) {--}}
        {{--                        if (data.comment.comment_type == 0) {--}}

        {{--                            var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>You</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                        } else if(data.comment.comment_type == 1) {--}}
        {{--                            var html = '<div class="chat admin internal"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                        }else{--}}
        {{--                            var html = '<div class="chat admin rider"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                        }--}}
        {{--                        $('section.chat-app-window .chats').append(html);--}}

        {{--                    } else if (user == 1) {--}}
        {{--                        if ($('div.chat:last-child').hasClass('shipper')) {--}}
        {{--                            var html = '<div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now  ({{Carbon\Carbon::now()}})</small></div>';--}}
        {{--                            $('div.chat:last-child').find('.chat-body').append(html);--}}
        {{--                        } else {--}}
        {{--                            var html = '<div class="chat chat-left shipper"><div class="chat-avatar"><div class="badge block badge-info"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                            $('section.chat-app-window .chats').append(html);--}}
        {{--                        }--}}
        {{--                    } else {--}}
        {{--                        if(data.comment.comment_type == 0){--}}
        {{--                            if ($('div.chat:last-child').hasClass('substitute-user')) {--}}
        {{--                                var html = '<div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div>';--}}
        {{--                                $('div.chat:last-child').find('.chat-body').append(html);--}}
        {{--                            } else {--}}
        {{--                                var html = '<div class="chat chat-left substitute-user"><div class="chat-avatar"><div class="badge block badge-substitute-user"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}
        {{--                                $('section.chat-app-window .chats').append(html);--}}
        {{--                            }--}}
        {{--                        }else{--}}
        {{--                            var html = '<div class="chat admin rider"><div class="chat-avatar"><div class="badge block badge-admin"><i class="la la-user font-medium-2"></i>' + name + '</div></div><div class="chat-body"><div class="chat-content text-left"><p>' + data.comment.comment + '</p><small>just now ({{Carbon\Carbon::now()}})</small></div></div></div>';--}}

        {{--                            $('section.chat-app-window .chats').append(html);--}}
        {{--                        }--}}


        {{--                    }--}}
        {{--                    $('#last_comment_id').val(data.comment.id);--}}
        {{--                    updateScroll();--}}
        {{--                }--}}
        {{--            });--}}
        {{--        }--}}
        {{--    }--}}

        {{--    function updateScroll() {--}}
        {{--        const container = document.querySelector('.chat-app-window');--}}
        {{--        container.scrollTop = $('.chat-app-window')[0].scrollHeight;--}}

        {{--    }--}}

        {{--    updateScroll();--}}
        {{--    $( "#edit_request_form" ).bind('submit', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--        var case_nature_id = parseInt($('#case_nature_select').val());--}}
        {{--        var tracking_number = $('.tracking_number').val();--}}
        {{--        var nature_flag = true;--}}
        {{--        if(case_nature_id === 1 || case_nature_id === 2){--}}
        {{--            if(case_nature_id === 1) {--}}
        {{--                var case_nature_complaint_id = $('#case_nature_complaints').val();--}}
        {{--                var description = $('#description').val();--}}
        {{--                if (!case_nature_complaint_id) {--}}
        {{--                    nature_flag = false;--}}
        {{--                    var error = "Please select Complaint type!";--}}
        {{--                    toastr.error(error, 'Error!', {--}}
        {{--                        positionClass: 'toast-top-center',--}}
        {{--                        containerId: 'toast-top-center'--}}
        {{--                    });--}}
        {{--                }--}}
        {{--                if (!description) {--}}
        {{--                    nature_flag = false;--}}
        {{--                    var error = "Please Enter Description!";--}}
        {{--                    toastr.error(error, 'Error!', {--}}
        {{--                        positionClass: 'toast-top-center',--}}
        {{--                        containerId: 'toast-top-center'--}}
        {{--                    });--}}
        {{--                }--}}
        {{--                if(!tracking_number){--}}
        {{--                    nature_flag = false;--}}
        {{--                    var error = "Tracking Number Required!";--}}
        {{--                    toastr.error(error, 'Error!', {--}}
        {{--                        positionClass: 'toast-top-center',--}}
        {{--                        containerId: 'toast-top-center'--}}
        {{--                    });--}}
        {{--                }--}}
        {{--            }--}}
        {{--            else if(case_nature_id === 2){--}}
        {{--                var case_nature_complaint_id = $('#case_nature_requests').val();--}}
        {{--                var description = $('#description').val();--}}
        {{--                if(!case_nature_complaint_id){--}}
        {{--                    nature_flag = false;--}}
        {{--                    var error = "Please select Request type!";--}}
        {{--                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                }--}}
        {{--                if (!description) {--}}
        {{--                    nature_flag = false;--}}
        {{--                    var error = "Please Enter Description!";--}}
        {{--                    toastr.error(error, 'Error!', {--}}
        {{--                        positionClass: 'toast-top-center',--}}
        {{--                        containerId: 'toast-top-center'--}}
        {{--                    });--}}
        {{--                }--}}
        {{--                if(!tracking_number){--}}
        {{--                    nature_flag = false;--}}
        {{--                    var error = "Tracking Number Required!";--}}
        {{--                    toastr.error(error, 'Error!', {--}}
        {{--                        positionClass: 'toast-top-center',--}}
        {{--                        containerId: 'toast-top-center'--}}
        {{--                    });--}}
        {{--                }--}}
        {{--            }--}}
        {{--            if(nature_flag){--}}
        {{--                $('#editRequest').attr('disabled',true);--}}
        {{--                $.ajax({--}}
        {{--                    url: '{!! route('admin.crm.request.edit') !!}',--}}
        {{--                    method: 'POST',--}}
        {{--                    data: {--}}
        {{--                        '_token': '{{ csrf_token() }}',--}}
        {{--                        'tracking_number': $('.tracking_number').val(),--}}
        {{--                        'request_id': $('#request_id').val(),--}}
        {{--                        'case_nature_id' : case_nature_id,--}}
        {{--                        'complaint_id' : case_nature_complaint_id,--}}
        {{--                        'description' : description--}}
        {{--                    }--}}
        {{--                })--}}
        {{--                    .done(function(data) {--}}
        {{--                        if(data.status == 0){--}}
        {{--                            toastr.success(data.success, 'Success!', {--}}
        {{--                                positionClass: 'toast-bottom-center',--}}
        {{--                                containerId: 'toast-bottom-center'--}}
        {{--                            });--}}
        {{--                            setTimeout(function(){--}}
        {{--                                window.location.reload(1);--}}
        {{--                            }, 1500);--}}
        {{--                        }--}}
        {{--                        else {--}}
        {{--                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                        }--}}

        {{--                        $('#editRequestModal').modal('hide');--}}
        {{--                        $('#editRequest').attr('disabled',false);--}}
        {{--                    });--}}
        {{--            }--}}
        {{--        }--}}
        {{--        else if(case_nature_id === 4){--}}
        {{--            var nature_flag = true;--}}
        {{--            var case_nature_claim_id = $('#case_nature_claim').val();--}}
        {{--            var product_cost = $('#claim_product_cost').val();--}}
        {{--            var check_product_picture = $('#product_picture').val();--}}
        {{--            var check_invoice_picture = $('#invoice_picture').val();--}}
        {{--            $('#tracking_number').val(tracking_number);--}}
        {{--            // $('#case_nature_id').val(case_nature_id);--}}
        {{--            // $('#complaint_id').val(case_nature_claim_id);--}}
        {{--            var formData = new FormData($('#edit_request_form')[0]);--}}
        {{--            if(!case_nature_claim_id){--}}
        {{--                nature_flag = false;--}}
        {{--                var error = "Please select Claim type!";--}}
        {{--                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--            }--}}
        {{--            if(!check_product_picture){--}}
        {{--                nature_flag = false;--}}
        {{--                var error = "Please attach Product Picture!";--}}
        {{--                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--            }--}}
        {{--            if(!product_cost){--}}
        {{--                nature_flag = false;--}}
        {{--                var error = "Please enter Product Cost!";--}}
        {{--                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--            }--}}
        {{--            if(!check_invoice_picture){--}}
        {{--                nature_flag = false;--}}
        {{--                var error = "Please attach Invoice Picture!";--}}
        {{--                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--            }--}}
        {{--            if(!tracking_number){--}}
        {{--                nature_flag = false;--}}
        {{--                var error = "Tracking Number Required!";--}}
        {{--                toastr.error(error, 'Error!', {--}}
        {{--                    positionClass: 'toast-top-center',--}}
        {{--                    containerId: 'toast-top-center'--}}
        {{--                });--}}
        {{--            }--}}
        {{--            if(nature_flag){--}}
        {{--                $('#editRequestModal').attr('disabled',true);--}}
        {{--                $.ajax({--}}
        {{--                    url: '{!! route('admin.crm.request.edit') !!}',--}}
        {{--                    method: 'POST',--}}
        {{--                    enctype: 'multipart/form-data',--}}
        {{--                    data: formData,--}}
        {{--                    dataType: 'json',--}}
        {{--                    processData: false,--}}
        {{--                    contentType: false,--}}
        {{--                })--}}
        {{--                    .done(function(data) {--}}
        {{--                        if(data.status == 0){--}}
        {{--                            toastr.success(data.success, 'Success!', {--}}
        {{--                                positionClass: 'toast-bottom-center',--}}
        {{--                                containerId: 'toast-bottom-center'--}}
        {{--                            });--}}
        {{--                            setTimeout(function(){--}}
        {{--                                window.location.reload(1);--}}
        {{--                            }, 1500);--}}
        {{--                        }--}}
        {{--                        else {--}}
        {{--                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                        }--}}
        {{--                        $('#editRequestModal').modal('hide');--}}
        {{--                        $('#editRequest').attr('disabled',false);--}}
        {{--                    });--}}
        {{--            }--}}

        {{--        }--}}
        {{--    });--}}
        {{--    $('#editRequestModal').on('hide.bs.modal', function (e) {--}}
        {{--        $('#edit_request_form')[0].reset();--}}
        {{--        $('#case_nature_complaints').val('').trigger('change');--}}
        {{--        $('#case_nature_select').val('').trigger('change');--}}
        {{--        $('#case_nature_requests').val('').trigger('change');--}}
        {{--        $('.tracking_number').val('');--}}
        {{--        $('#request_complaints').addClass('d-none');--}}
        {{--        $('#request_service').addClass('d-none');--}}
        {{--        $('#description_div').addClass('d-none');--}}
        {{--        $('#request_claims').addClass('d-none');--}}
        {{--        $('#case_nature_claim').val('').trigger('change');--}}
        {{--        $('#claim_channel').val('').trigger('change');--}}
        {{--        $('#claim_product_cost').val('');--}}
        {{--    });--}}

        {{--    $("#crm_escalation_level").prepend('<option value="" selected></option>').select2({--}}
        {{--        placeholder: "Select Escalation",--}}
        {{--        width: '100%',--}}
        {{--        dropdownParent: $('#escalateModal')--}}
        {{--    });--}}
        {{--    $('#halt_start_escalation').on('click', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--        var request_id = @json($crm_details->id);--}}
        {{--        var status = $(this).val();--}}
        {{--        if(status == 0){--}}
        {{--            var status_text = 'Halt';--}}
        {{--        }--}}
        {{--        else{--}}
        {{--            var status_text = 'Start';--}}
        {{--        }--}}
        {{--        swal({--}}
        {{--            text: 'Are you sure, you want to '+status_text+' Escalation of this Request?',--}}
        {{--            icon: 'warning',--}}
        {{--            buttons: {--}}
        {{--                cancel: {--}}
        {{--                    text: 'No',--}}
        {{--                    value: null,--}}
        {{--                    visible: true,--}}
        {{--                    closeModal: true,--}}
        {{--                },--}}
        {{--                confirm: {--}}
        {{--                    text: 'Yes',--}}
        {{--                    value: true,--}}
        {{--                    visible: true,--}}
        {{--                    closeModal: true--}}
        {{--                }--}}
        {{--            },--}}
        {{--            closeOnClickOutside: false,--}}
        {{--            closeOnEsc: false,--}}
        {{--            dangerMode: true--}}
        {{--        }).then(function(confirm) {--}}
        {{--            if (confirm) {--}}
        {{--                $.ajax({--}}
        {{--                    url: '{!! route('admin.crm.escalation_status') !!}',--}}
        {{--                    method: 'POST',--}}
        {{--                    data: {--}}
        {{--                        'crm_request_id': request_id,--}}
        {{--                        'status': status,--}}
        {{--                        '_token': '{{ csrf_token() }}'--}}
        {{--                    }--}}
        {{--                })--}}
        {{--                    .done(function(data) {--}}
        {{--                        if (data.status == 0) {--}}
        {{--                            toastr.success(data.success, 'Success!', {--}}
        {{--                                positionClass: 'toast-bottom-center',--}}
        {{--                                containerId: 'toast-bottom-center'--}}
        {{--                            });--}}
        {{--                            setTimeout(function(){--}}
        {{--                                window.location.reload(1);--}}
        {{--                            }, 1500);--}}
        {{--                        }--}}
        {{--                        else {--}}
        {{--                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                        }--}}
        {{--                    });--}}
        {{--            }--}}
        {{--        });--}}
        {{--    });--}}
        {{--    $('#escalate').on('click', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--        $('#escalateModal').modal('show');--}}
        {{--    });--}}
        {{--    $('#escalateSubmit').on('click', function (e) {--}}
        {{--        e.preventDefault();--}}
        {{--        var crm_request_id = $('#escalate_crm_request_id').val();--}}
        {{--        var escalation_tagging_id = $('#escalation_tagging_id').val();--}}
        {{--        var selected_escalation = $('#crm_escalation_level').val();--}}
        {{--        var flag = true;--}}
        {{--        if(selected_escalation == null || selected_escalation == ''){--}}
        {{--            var error = "Please select Escalation";--}}
        {{--            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--            flag = false--}}
        {{--        }--}}
        {{--        if(flag == true){--}}
        {{--            swal({--}}
        {{--                text: 'Are you sure, you want to Escalate this Request?',--}}
        {{--                icon: 'warning',--}}
        {{--                buttons: {--}}
        {{--                    cancel: {--}}
        {{--                        text: 'No',--}}
        {{--                        value: null,--}}
        {{--                        visible: true,--}}
        {{--                        closeModal: true,--}}
        {{--                    },--}}
        {{--                    confirm: {--}}
        {{--                        text: 'Yes',--}}
        {{--                        value: true,--}}
        {{--                        visible: true,--}}
        {{--                        closeModal: true--}}
        {{--                    }--}}
        {{--                },--}}
        {{--                closeOnClickOutside: false,--}}
        {{--                closeOnEsc: false,--}}
        {{--                dangerMode: true--}}
        {{--            }).then(function(confirm) {--}}
        {{--                if (confirm){--}}
        {{--                    swal({--}}
        {{--                        title: 'Please Wait!',--}}
        {{--                        text: 'Escalation is in process!',--}}
        {{--                        icon: 'info',--}}
        {{--                        buttons: false,--}}
        {{--                        closeOnClickOutside: false,--}}
        {{--                        closeOnEsc: false--}}
        {{--                    });--}}
        {{--                    $.ajax({--}}
        {{--                        url: '{!! route('admin.crm.escalate') !!}',--}}
        {{--                        method: 'POST',--}}
        {{--                        data: {--}}
        {{--                            'crm_request_id': crm_request_id,--}}
        {{--                            'escalation_tagging_id': escalation_tagging_id,--}}
        {{--                            'selected_escalation': selected_escalation,--}}
        {{--                            '_token': '{{ csrf_token() }}'--}}
        {{--                        }--}}
        {{--                    })--}}
        {{--                        .done(function(data) {--}}
        {{--                            if (data.status == 0) {--}}
        {{--                                toastr.success(data.success, 'Success!', {--}}
        {{--                                    positionClass: 'toast-bottom-center',--}}
        {{--                                    containerId: 'toast-bottom-center'--}}
        {{--                                });--}}
        {{--                                setTimeout(function(){--}}
        {{--                                    window.location.reload(1);--}}
        {{--                                }, 1500);--}}
        {{--                            }--}}
        {{--                            else {--}}
        {{--                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                            }--}}
        {{--                        });--}}
        {{--                }--}}
        {{--            });--}}
        {{--        }--}}
        {{--    });--}}


        {{--    $('#valid_form').on('submit', function (e) {--}}
        {{--        blockPagePermanently();--}}
        {{--    });--}}
        {{--    $('#invalid_form').on('submit', function (e) {--}}
        {{--        blockPagePermanently();--}}
        {{--    })--}}

        {{--    @foreach($comments as $comment)--}}
        {{--    @if($comment->comment_by == 0)--}}
        {{--    @if($comment->comment_type == 0 && (session('role_id') == 1 || in_array(310, session('permissions'))))--}}
        {{--    $('#edit_comment_{{$comment->id}}').on('click', function (e) {--}}
        {{--        var comment_id = $(this).attr("value");--}}
        {{--        e.preventDefault();--}}
        {{--        var cmt = @json($comment->comment);--}}
        {{--        var text_edit  = "Are you sure, you want to edit this comment as Internal? \n \t "+cmt;--}}
        {{--        swal({--}}
        {{--            text: text_edit,--}}
        {{--            icon: 'info',--}}
        {{--            buttons: {--}}
        {{--                cancel: {--}}
        {{--                    text: 'No',--}}
        {{--                    value: null,--}}
        {{--                    visible: true,--}}
        {{--                    closeModal: true,--}}
        {{--                },--}}
        {{--                confirm: {--}}
        {{--                    text: 'Yes',--}}
        {{--                    value: true,--}}
        {{--                    visible: true,--}}
        {{--                    closeModal: true--}}
        {{--                }--}}
        {{--            },--}}
        {{--            closeOnClickOutside: false,--}}
        {{--            closeOnEsc: false,--}}
        {{--            dangerMode: true--}}
        {{--        }).then(function(confirm) {--}}
        {{--            if(confirm){--}}
        {{--                swal({--}}
        {{--                    title: 'Please Wait!',--}}
        {{--                    text: 'Comment is being updated.',--}}
        {{--                    icon: 'info',--}}
        {{--                    buttons: false,--}}
        {{--                    closeOnClickOutside: false,--}}
        {{--                    closeOnEsc: false--}}
        {{--                });--}}
        {{--                $.ajax({--}}
        {{--                    url: '{!! route('admin.crm.comment.edit') !!}',--}}
        {{--                    method: 'POST',--}}
        {{--                    data: {--}}
        {{--                        'comment_id': comment_id,--}}
        {{--                        '_token': '{{ csrf_token() }}'--}}
        {{--                    }--}}
        {{--                })--}}
        {{--                    .done(function (data) {--}}
        {{--                        if (data.status == 0) {--}}
        {{--                            $('#edit_comment_{{$comment->id}}').remove();--}}
        {{--                            $('#chat_{{$comment->id}}').addClass('internal');--}}
        {{--                            $('#updated_by_div_{{$comment->id}}').append('<small>Updated by: ' + data.updated_by + ' (' + data.updated_at + ')</small>');--}}
        {{--                            toastr.success(data.success, 'Success!', {--}}
        {{--                                positionClass: 'toast-bottom-center',--}}
        {{--                                containerId: 'toast-bottom-center'--}}
        {{--                            });--}}
        {{--                        }--}}
        {{--                        else {--}}
        {{--                            toastr.error(data.error, 'Error!', {--}}
        {{--                                positionClass: 'toast-top-center',--}}
        {{--                                containerId: 'toast-top-center'--}}
        {{--                            });--}}
        {{--                        }--}}
        {{--                        swal.close();--}}
        {{--                    });--}}
        {{--            }--}}
        {{--        });--}}
        {{--    });--}}
        {{--    @endif--}}
        {{--    @endif--}}
        {{--    @endforeach--}}

        {{--    var images_count = {{ $crm_images_count }};--}}
        {{--    var rows_count = 0;--}}
        {{--    var selected_rows = [];--}}
        {{--    $('#image_upload_btn').on('click', function () {--}}
        {{--        $('#image_upload_btn').attr('disabled', true);--}}
        {{--        var crm_request_id = $('#crm_request_id').val();--}}
        {{--        if(crm_request_id){--}}
        {{--            $('#image_crm_request_id').val(crm_request_id);--}}
        {{--            $.ajax({--}}
        {{--                url: '{!! route('admin.crm.request.image_details') !!}',--}}
        {{--                method: 'POST',--}}
        {{--                data: {--}}
        {{--                    'crm_request_id': crm_request_id,--}}
        {{--                    '_token': '{{ csrf_token() }}'--}}
        {{--                }--}}
        {{--            }).done(function (data) {--}}

        {{--                if(data.status == 0){--}}
        {{--                    var image_html = '';--}}
        {{--                    $.each(data.images, function (index, image) {--}}
        {{--                        index++;--}}
        {{--                        var img = '<a class="btn btn-sm btn-outline-info align-middle" href="' + image.image + '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';--}}
        {{--                        var remove = '';--}}
        {{--                        remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';--}}
        {{--                        image_html += '<tr id="' + image.id + '"><td>' + index + '</td><td>' + image.date + '</td><td>' + img + '</td><td>' + remove + '</td></tr>';--}}
        {{--                    });--}}
        {{--                    $('#crm_image_view_table tbody').append(image_html);--}}
        {{--                    $('#image_upload_modal').modal('show');--}}
        {{--                }else if(data.status == 2){--}}
        {{--                    var image_html = '<tr><td colspan="4">No Images found!</td></tr>';--}}

        {{--                    $('#crm_image_view_table tbody').append(image_html);--}}
        {{--                    $('#image_upload_modal').modal('show');--}}
        {{--                }else{--}}
        {{--                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                }--}}
        {{--                $('#image_upload_btn').attr('disabled', false);--}}

        {{--            });--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $.validator.addMethod('maxsize', function(value, element, params) {--}}
        {{--        if ($(element).attr('type') === 'file') {--}}
        {{--            if (element.files && element.files.length) {--}}
        {{--                for (var c = 0; c < element.files.length; c++) {--}}
        {{--                    if (element.files[c].size > params) {--}}
        {{--                        return false;--}}
        {{--                    }--}}
        {{--                }--}}
        {{--            }--}}
        {{--        }--}}

        {{--        return true;--}}
        {{--    }, $.validator.format("File Size must not exceed {0} bytes."));--}}
        {{--    var crm_image_table;--}}
        {{--    function add_row() {--}}
        {{--        var tr_id = $('#image_upload_table tbody tr').attr('id');--}}
        {{--        if (typeof tr_id !== typeof undefined && tr_id !== false) {--}}
        {{--            var new_img_rows = $('#image_upload_table tbody tr').length;--}}
        {{--            new_img_rows = images_count + new_img_rows;--}}
        {{--            if(new_img_rows >= 2){--}}
        {{--                $('#image_upload_table .img_add_btn').attr('disabled', true);--}}
        {{--                return false;--}}
        {{--            }--}}
        {{--        }--}}

        {{--        rows_count++;--}}

        {{--        var crm_image = '<input class="form-control form-control-sm" type="file" name="crm_image_'+rows_count+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">';--}}
        {{--        if(rows_count == 1){--}}
        {{--            var remove = '';--}}
        {{--        }else{--}}
        {{--            var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';--}}

        {{--        }--}}
        {{--        crm_image_table.row.add([0, crm_image,remove]).node().id = rows_count;--}}
        {{--        crm_image_table.draw(true);--}}
        {{--        $('#CRMImageSubmitButton').attr('disabled', false);--}}
        {{--        selected_rows.push(rows_count);--}}
        {{--    }--}}
        {{--    crm_image_table = $('#image_upload_table').DataTable({--}}
        {{--        dom: '<"d-inline-block"l><"pull-right"B>tipr',--}}
        {{--        buttons:[{--}}
        {{--            title: 'Add Row',--}}
        {{--            className: 'btn btn-primary img_add_btn',--}}
        {{--            text: '<i class="la la-plus"></i> Add Row',--}}
        {{--            action:function (e) {--}}
        {{--                if(images_count < 2){--}}
        {{--                    add_row();--}}
        {{--                }--}}
        {{--            }--}}
        {{--        }],--}}
        {{--        ordering:false,--}}
        {{--        paging:false,--}}
        {{--        columns: [--}}
        {{--            {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},--}}
        {{--            {name: 'image', class: 'align-middle image form-group'},--}}
        {{--            {name: 'action', class: 'align-middle action'},--}}
        {{--        ],--}}

        {{--        rowCallback: function(row, data, index) {--}}
        {{--            var info = crm_image_table.page.info();--}}

        {{--            $('td:eq(0)', row).html(index + 1 + info.page * info.length);--}}

        {{--        },--}}
        {{--        initComplete: function() {--}}

        {{--            // this.api().table().columns.adjust();--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $('#crm_image_view_table').on('click','a.remove_row', function () {--}}
        {{--        var row_id = $(this).parents('tr').attr('id');--}}
        {{--        var crm_request_id = $('#image_crm_request_id').val();--}}
        {{--        var current = $(this);--}}
        {{--        if(row_id){--}}
        {{--            swal({--}}
        {{--                title: 'Are You Sure?',--}}
        {{--                text: 'Select Yes if you want to delete this image!',--}}
        {{--                icon: 'warning',--}}
        {{--                buttons: {--}}
        {{--                    cancel: {--}}
        {{--                        text: 'No',--}}
        {{--                        value: null,--}}
        {{--                        visible: true,--}}
        {{--                        closeModal: true,--}}
        {{--                    },--}}
        {{--                    confirm: {--}}
        {{--                        text: 'Yes',--}}
        {{--                        value: true,--}}
        {{--                        visible: true,--}}
        {{--                        closeModal: true--}}
        {{--                    }--}}
        {{--                },--}}
        {{--                closeOnClickOutside: false,--}}
        {{--                closeOnEsc: false,--}}
        {{--                dangerMode: true--}}
        {{--            }).then(function (confirm) {--}}
        {{--                if (confirm) {--}}
        {{--                    $.ajax({--}}
        {{--                        url: '{!! route('admin.crm.request.image_delete') !!}',--}}
        {{--                        method: 'POST',--}}
        {{--                        data: {--}}
        {{--                            'crm_image_id': row_id,--}}
        {{--                            'crm_request_id':crm_request_id,--}}
        {{--                            '_token': '{{ csrf_token() }}'--}}
        {{--                        }--}}
        {{--                    }).done(function (data) {--}}
        {{--                        if(data.status == 0){--}}
        {{--                            images_count = images_count - 1;--}}
        {{--                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
        {{--                            current.parents('tr').remove();--}}
        {{--                        }else{--}}
        {{--                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
        {{--                        }--}}
        {{--                    });--}}
        {{--                }--}}
        {{--            });--}}
        {{--        }--}}
        {{--    });--}}

        {{--    $('body').on('click', 'a.remove_row',function () {--}}
        {{--        var rid = parseInt($(this).parents('tr').attr('id'));--}}
        {{--        var index = $.inArray(rid, selected_rows);--}}

        {{--        if (index !== -1) {--}}
        {{--            selected_rows.splice(index, 1);--}}
        {{--        }--}}
        {{--        crm_image_table.row( $(this).parents('tr') ).remove().draw();--}}
        {{--    });--}}
        {{--    $('#crm_upload_form').validate({--}}

        {{--        errorClass: 'danger',--}}
        {{--        successClass: 'success',--}}
        {{--        normalizer: function(value) {--}}
        {{--            return $.trim(value);--}}
        {{--        },--}}
        {{--        errorPlacement: function(error, element) {--}}
        {{--            error.addClass('w-100').appendTo(element.parent('.form-group'));--}}
        {{--        },--}}
        {{--        submitHandler: function(form) {--}}
        {{--            $(form).find('button[type=submit]').attr('disabled', 'disabled');--}}
        {{--            $('#selected_ids').val(selected_rows);--}}
        {{--            swal({--}}
        {{--                title: 'Please Wait!',--}}
        {{--                text: 'Image is being uploaded!',--}}
        {{--                icon: 'info',--}}
        {{--                buttons: false,--}}
        {{--                closeOnClickOutside: false,--}}
        {{--                closeOnEsc: false--}}
        {{--            });--}}
        {{--            form.submit();--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $('#image_upload_modal').on('hidden.bs.modal', function () {--}}
        {{--        $('#image_crm_request_id').val('');--}}
        {{--        crm_image_table.clear();--}}
        {{--        crm_image_table.draw();--}}
        {{--        selected_rows = [];--}}
        {{--        rows_count = 0;--}}
        {{--        $('#crm_image_view_table tbody').html('');--}}
        {{--    });--}}

        {{--    $('#special_request').on('click',function () {--}}
        {{--        $('#special_request_modal').modal('show');--}}
        {{--    });--}}

        {{--    $('#special_request_modal').on('hide.bs.modal', function (e) {--}}
        {{--        $('.form-check-input').prop('checked', false);--}}
        {{--    });--}}

        {{--});--}}



    </script>
@endsection