@extends('admin.layout.master')
@section('title', 'Edit Payment Requisition')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
            <h1 class="mb-1">Edit Payment Requisition</h1>

            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">

                        @include('admin.inc.messages')

                        <form id="finance_request_form" class="form-horizontal" 
                              method="POST" 
                              action="{{ route('admin.finance.prf.update', $requisition->id) }}" 
                              enctype="multipart/form-data" novalidate="novalidate">
                                  {{ csrf_field() }}
                            <!-- @method('PUT') -->

                            <div class="row">
                                <!-- LEFT COLUMN -->
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label>Invoice or Document ID</label>
                                        <input type="text" name="invoice_id" value="{{ old('invoice_id', $requisition->invoice_no) }}" 
                                               class="form-control" placeholder="Invoice / Document ID*" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Payee Name</label>
                                        <input type="text" name="payee_name" value="{{ old('payee_name', $requisition->payee_name) }}" 
                                               class="form-control" placeholder="Payee Name*" required>
                                    </div>

                                    <div class="form-group">
                                        <label>NTN / CNIC</label>
                                        <input type="text" name="ntn_cnic" value="{{ old('ntn_cnic', $requisition->ntn_cnic) }}" 
                                               class="form-control" placeholder="NTN / CNIC*" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <select name="bank_id" id="bank_name" class="select2" required>
                                            <option value="">Select Bank</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ $requisition->bank_id == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Bank Account Title</label>
                                        <input type="text" name="bank_title" value="{{ old('bank_title', $requisition->bank_title) }}" 
                                               class="form-control" placeholder="Bank Account Title*" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Bank Account / IBAN</label>
                                        <input type="text" name="iban" value="{{ old('iban', $requisition->iban) }}" 
                                               class="form-control" placeholder="IBAN*" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="text" name="amount" id="amount" value="{{ old('amount', $requisition->amount) }}" 
                                               class="form-control text-right" placeholder="Amount*" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 1</label>
                                        <input type="file" name="document1" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                        @if($requisition->document1)
                                            <small>
                                                <a href="{{ asset($requisition->document1) }}" target="_blank">
                                                    View existing file
                                                </a>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 2</label>
                                        <input type="file" name="document2" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                        @if($requisition->document2)
                                            <small>
                                                <a href="{{ asset('uploads/payment_requisitions/'.$requisition->document2) }}" target="_blank">
                                                    View existing file
                                                </a>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 3</label>
                                        <input type="file" name="document3" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                        @if($requisition->document3)
                                            <small>
                                                <a href="{{ asset('uploads/payment_requisitions/'.$requisition->document3) }}" target="_blank">
                                                    View existing file
                                                </a>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Document 4</label>
                                        <input type="file" name="document4" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                        @if($requisition->document4)
                                            <small>
                                                <a href="{{ asset('uploads/payment_requisitions/'.$requisition->document4) }}" target="_blank">
                                                    View existing file
                                                </a>
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label>Accounts of</label>
                                        <select name="account_of_id" id="account_of" class="select2" required>
                                            <option value="">Select Account of</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ $requisition->account_of_id == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="hidden" name="related_department_id" value="{{ $requisition->related_department_id }}">

                                    <div class="form-group">
                                        <label>Related Department</label>
                                        <select name="related_department_id" id="related_department" class="select2" disabled>
                                            <option value="">Select Related Department</option>
                                            @foreach($departments as $dpt)
                                                <option value="{{ $dpt->id }}" {{ $requisition->related_department_id == $dpt->id ? 'selected' : '' }}>
                                                    {{ $dpt->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="6" placeholder="Description">{{ old('description', $requisition->description) }}</textarea>
                                    </div>

                                    <div class="form-group text-right mt-2">
                                        <button type="submit" class="btn btn-primary">UPDATE</button>
                                        <a href="{{ route('admin.finance.prf.index') }}" class="btn btn-secondary">CANCEL</a>
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
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endsection


@section('js')
<script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $('#bank_name, #account_of, #related_department').select2({
        placeholder: 'Select option',
        width: '100%',
        allowClear: true
    });

    $('#amount').inputmask({
        'alias': 'integer',
        'allowMinus': false,
        'allowPlus': false,
        'min': 1
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
