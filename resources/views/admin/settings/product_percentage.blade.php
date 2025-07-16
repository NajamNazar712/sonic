@extends('admin.layout.master')

@section('title', 'Product Tax Percentage')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Product Tax Percentage
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.product_tax.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        @foreach($data as $d)
                                            <div class="form-group">
                                                <label style="font-weight: 600;">{{ $d->name }}</label>
                                                <div class="row">
                                                    {{-- Tax Percentage --}}
                                                    <div class="col-md-6">
                                                        <label>Tax Percentage</label>
                                                        <div class="input-group mb-2">
                                                            <input type="text"
                                                                name="tax_percentage[{{ $d->id }}]"
                                                                class="form-control text-center"
                                                                placeholder="Tax %"
                                                                data-rule-required="true"
                                                                data-msg-required="Required"
                                                                value="{{ $d->tax_percentage }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- SST Percentage --}}
                                                    <div class="col-md-6">
                                                        <label>SST Percentage</label>
                                                        <div class="input-group mb-2">
                                                            <input type="text"
                                                                name="sst_percentage[{{ $d->id }}]"
                                                                class="form-control text-center"
                                                                placeholder="SST %"
                                                                data-rule-required="true"
                                                                data-msg-required="Required"
                                                                value="{{ $d->sst_percentage }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                </div>
                            </div>
                            <div class="row mt-4 text-center">
                                <div class="col-6 form-group">
                                    <label class="mr-2 font-medium-3"><b>Exclude Specific Shippers:</b></label>
                                    <input type="checkbox" name="all_shipper_toggle_wht" id="all_shipper_toggle_wht" class="switchery all_shipper_toggle" data-size="sm" data-switchery="true">
                                    <label class="ml-2 font-medium-3"><b>Include Specific Shippers:</b></label>
                                </div>

                                <div class="col-6 form-group">
                                    <label class="mr-2 font-medium-3"><b>Exclude Specific Shippers:</b></label>
                                    <input type="checkbox" name="all_shipper_toggle_sst" id="all_shipper_toggle_sst" class="switchery all_shipper_toggle" data-size="sm" data-switchery="true">
                                    <label class="ml-2 font-medium-3"><b>Include Specific Shippers:</b></label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6 form-group" id="excluded_users_container_wht">
                                    <label class="mr-2 font-medium-2"><b>WHT</b></label>
                                    <select name="excluded_users_wht[]" id="excluded_users_wht" class="form-control select2" multiple="multiple">
                                       @foreach($shippers as $shipper)
                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 form-group" id="excluded_users_container_sst">
                                    <label class="mr-2 font-medium-2"><b>COD SST </b></label>
                                    <select name="excluded_users_sst[]" id="excluded_users_sst" class="form-control select2" multiple="multiple">
                                        @foreach($shippers as $shipper)
                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mt-3 mb-5 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">Update</button>
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
            $('#excluded_users_wht').select2({
                placeholder:'Select Only Shippers',
                width:'100%',
                allowClear:true
            });
            $('#excluded_users_sst').select2({
                placeholder:'Select Only Shippers',
                width:'100%',
                allowClear:true
            });
            $('#settings_form .class').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });
            @if(count($excluded_users_wht) > 0)
                var ids = @json($excluded_users_wht);
                $('#excluded_users_wht').val(ids).trigger('change');
            @endif
            @if(count($excluded_users_sst) > 0)
                var ids = @json($excluded_users_sst);
                $('#excluded_users_sst').val(ids).trigger('change');
            @endif
        });
    </script>
@endsection