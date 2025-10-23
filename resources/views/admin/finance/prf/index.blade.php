@extends('admin.layout.master')
@section('title', 'Payment Requisition')


@section('content')
  <!-- resources/views/admin/requests/create.blade.php -->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
            <h1 class="mb-1">Payment Requisition Form</h1>

            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">

                        @include('admin.inc.messages')

                        <form id="finance_request_form" class="form-horizontal" method="POST" action="{{ route('admin.finance.prf.submit') }}" enctype="multipart/form-data" novalidate="novalidate">
                            {{ csrf_field() }}

                            <div class="row">
                                <!-- LEFT COLUMN -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Invoice or Document ID</label>
                                        <input type="text" name="invoice_id" class="form-control" placeholder="Invoice / Document ID*" data-rule-required="true" data-msg-required="Invoice ID is required">
                                    </div>

                                    <div class="form-group">
                                        <label>Payee Name</label>
                                        <input type="text" name="payee_name" class="form-control" placeholder="Payee Name*" data-rule-required="true">
                                    </div>

                                    <div class="form-group">
                                        <label>NTN / CNIC</label>
                                        <input type="text" name="ntn_cnic" class="form-control" placeholder="NTN / CNIC*" data-rule-required="true">
                                    </div>

                                    <div class="form-group">
                                        <label>Bank Name</label>
                                            <select name="bank_id" id="bank_name" class="select2 " data-rule-required="true" >
                                                @foreach($banks as $bank)
                                                    <option value="{{$bank->id}}">{{$bank->name}}</option>
                                                @endforeach
                                            </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Bank Account Title</label>
                                        <input type="text" name="bank_title" class="form-control" placeholder="Bank Account Title*" data-rule-required="true">
                                    </div>

                                    <div class="form-group">
                                        <label>Bank Account / IBAN</label>
                                        <input type="text" name="iban" class="form-control" placeholder="IBAN*" data-rule-required="true">
                                    </div>

                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="text" name="amount" id="amount" class="form-control text-right" placeholder="Amount*" data-rule-required="true" data-rule-number="true">
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 1</label>
                                        <input type="file" name="document1" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 2</label>
                                        <input type="file" name="document2" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 3</label>
                                        <input type="file" name="document3" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 4</label>
                                        <input type="file" name="document4" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Accounts of</label>
                                        <select name="account_of_id" id="account_of" class="select2 " data-rule-required="true" >
                                            @foreach($accounts as $account)
                                                <option value="{{$account->id}}">{{$account->name}}</option>
                                            @endforeach
                                        </select>
                                        
                                    </div>

                                    <!-- <div class="form-group">
                                        <label>Voucher No.</label>
                                        <input type="text" name="voucher_no" class="form-control" placeholder="Voucher No.">
                                    </div> -->

                                    <div class="form-group">
                                        <label>Related Department</label>
                                            <select name="related_department_id" id="related_department" class="select2" >
                                                @foreach($departments as $dpt)
                                                    <option value="{{$dpt->id}}">{{$dpt->name}}</option>
                                                @endforeach
                                            </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="6" placeholder="Description"></textarea>
                                    </div>

                                    <div class="form-group text-right mt-2">
                                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div> <!-- card-body -->
                </div> <!-- card-content -->
            </div> <!-- card -->
        </div> <!-- content-body -->
    </div> <!-- content-wrapper -->
</div> <!-- app-content -->



@endsection




@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    <style>
     
    </style>

@endsection


@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <!-- Validation Script -->
    <script type="text/javascript">

        $(document).ready(function () { 

            $('#bank_name').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Bank',
                width:'100%',
                allowClear:true
            });

            
            $('#account_of').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Account of',
                width:'100%',
                allowClear:true
            });

             $('#related_department').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Related Department',
                width:'100%',
                allowClear:true
            });

            $('#amount').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false,
                'min' :1
			});




            $('#finance_request_form').validate({
                errorClass: 'danger',
				successClass: 'success',
				normalizer: function(value) {
					return $.trim(value);
				},
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
                submitHandler: function(form) {
                    form.submit();
                }
            });

        });
  
    </script>

@endsection