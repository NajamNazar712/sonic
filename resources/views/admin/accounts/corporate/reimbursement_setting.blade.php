@extends('admin.layout.master')

@section('title', 'Corporate Reimbursement Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Corporate Reimbursement Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="settings_form" class="form-horizontal text-center" method="POST" action="" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row justify-content-center">
                                    <div class="col-8 offset-2">
                                        <div class="form-group">
                                            <h2 class="display-inline ml-1 pull-left">
                                                Corporate Reimbursement<br>
                                                <small class="font-small-3 text-info">(Any change will get effected from 1st of the Next Month)</small>
                                            </h2>
                                            <input type="checkbox" name="status_on" id="status_on" {{$setting->setting_display == 1 ? "Checked" : ""}} class="switchery status_on" data-size="xl" data-switchery="true">
                                        </div>
                                    </div>
                                    <div class="col-3 mt-2">
                                        @if($setting->status == 1 && (session('role_id') == 1 || in_array(599, session('permissions'))))
                                            <button type="button" class="btn btn-success approve">Approve</button>
                                            <button type="button" class="btn btn-danger reject">Reject</button>
                                        @endif
                                        @if($setting->status == 2)
                                            <button type="button" class="btn btn-primary update">Update</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            @if($setting->status == 1 && session('role_id') == 1 || in_array(599, session('permissions')))
            $(".approve").on('click',function (){
               $url = '{{route("admin.corporate.reimbursement_setting.approve",$id)}}';

                swal({
                    text: 'Are you sure, you want to approve this?',
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
                    if(confirm)
                    {
                        $("#settings_form").attr('action',$url);
                        $("#settings_form").submit();
                    }
                });
            });

            $(".reject").on('click',function (){
                $url = '{{route("admin.corporate.reimbursement_setting.reject",$id)}}';

                swal({
                    text: 'Are you sure, you want to reject this?',
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
                    if(confirm)
                    {
                        $("#settings_form").attr('action',$url);
                        $("#settings_form").submit();
                    }
                });
            });
            @endif
            @if($setting->status == 2)
            $(".update").on('click',function (){
                $url = '{{route("admin.corporate.reimbursement_setting.store",$id)}}';

                swal({
                    text: 'Are you sure, you want to update this?',
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
                    if(confirm)
                    {
                        $("#settings_form").attr('action',$url);
                        $("#settings_form").submit();
                    }
                });
            });
            @endif
        });
    </script>
@endsection