<div class="card nbank" id="">
    <div class="card-header">
        <h3 class="card-title">New Bank</h3>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
        </div>
    </div>
            <div class="">
                <div class="row">
                    <div class="col-md-6">
                <div class="form-group">
                    <label for="bank_name">
                        Bank Name:
                        <span class="danger">*</span>
                    </label>
                    
                    <div>
                        <select name="temp_bank_name" class="select2 form-control required" style="width: 100%">
                            <option value="" selected>Select Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{$bank->id}}"  {{ old('bank_name[]') == $bank->id ? 'selected' : '' }} >{{$bank->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bank_branch">
                        Branch Name:
                        <span class="danger">*</span>
                    </label>
                    <input type="text" class="form-control required" value="{{ old('bank_branch[]') }}" name="temp_bank_branch" placeholder="Branch Name*">
                </div>
                <div class="form-group">
                    <label for="account_name">Account Number:
                        <span class="danger">*</span></label>
                    <input type="text" class="form-control required" value="{{ old('account_no[]') }}" name="temp_account_no" placeholder="Account Number*">
                </div>

            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="account_title">
                        Account Title:
                        <span class="danger">*</span>
                    </label>
                    <input type='text' class="form-control required" value="{{ old('account_title[]') }}" name="temp_account_title" placeholder="Account Title*">

                </div>

                <div class="form-group">
                    <label for="iban">
                        IBAN Number:
                        <span class="danger">*</span>
                    </label>
                    <input type="text" class="form-control required" placeholder="(e.g: PK-37-MEZN-0001-2201-0000-4069)" value="{{ old('iban_no[]') }}" name="temp_iban_no">
                </div>

                    <div class="form-group">

                        <label for="bank_city">Bank City:
                            <span class="danger">*</span>
                        </label>
                        <div>
                            <select name="temp_bank_city" class="select2 form-control required" style="width: 100%">
                                <option value="" selected>Select Bank City</option>
                                @foreach($all_cities as $bank_city)
                                   <option value="{{$bank_city->id}}"  {{ old('bank_city[]') == $bank_city->id ? 'selected' : '' }} >{{$bank_city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            </div>
        </div>
    </div>
    
</div>