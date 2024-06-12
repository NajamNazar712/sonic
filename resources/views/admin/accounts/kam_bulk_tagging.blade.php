@extends('admin.layout.master')

@section('title', 'KAM Bulk Tagging & De-Tagging')

@section('content')
    <h1>KAM Bulk Tagging & De-Tagging</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
    
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row px-1">
                                <form id="upload_shippers_form" class="form-horizontal w-100 p-2" method="POST" action="{{ route('admin.accounts.kam_bulk_tagging.update') }}" novalidate="novalidate" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 ml-5">
                                            <div class="form-group">
                                                <label class="mr-2 font-medium-4"><b>Tag </b></label>
                                                <input type="checkbox" name="tag" class="switchery" data-size="md" data-switchery="true">
                                                <label class="ml-2 font-medium-4"><b>De-Tag </b></label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                        <div class="col-md-2 justify-content-end">
                                            <div class="form-group text-right">
                                                <a href="{{ asset('file/KAM Bulk Tagging Template.xlsx') }}" class="btn btn-secondary btn-block"><i class="la la-download"></i> Download Template</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="file" name="ids" class="border-primary rounded" style="padding: 6px" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary mb-2">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
<script src="{{ asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#upload_shippers_form').validate({
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
        });
    });
</script>

@endsection

