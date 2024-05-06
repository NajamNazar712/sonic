@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')

    <div class="card">
        @include('client.inc.messages')
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <h1 class="mb-5">Welcome to Sonic..</h1>
            </div>

            <div class="card-content">
                <div class="card-body">
                    <form id="registership" action="{{ route('cod.register.submit') }}"
                        method="post" class="steps-validation wizard-circle"
                        enctype="multipart/form-data">
                        <!-- Step 1 -->
                        @csrf
                        @method('post')
                        @if ($lead != null)
                            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                        @endif
                        <h6>Profile Information</h6>
                        @include('client.inc.messages')
                        <input type="hidden" name="wordpress_account" value="1">

                        <fieldset>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">
                                            Company Name:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            value="{{ $lead->company_name }}" name="name" readonly>
                                        <span name="cname" class="danger" for="name"
                                            style="display: none;">Atleast 3 Characters
                                            Required</span>
                                        <span name="ename" class="danger" for="name"
                                            style="display: none;">Company Name Already
                                            Exists</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shipper_poc">
                                            Person of Contact:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            placeholder="Person Name (Alphabet Only)"
                                            name="shipper_poc"
                                            value="{{ $lead->contact_person }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_address">Company Address:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            name="company_address"
                                            value="{{ $lead->business_address }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number 1:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            
                                            name="phone"
                                            value="{{ $lead->phone_number }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cnic">CNIC Number: <span class="danger">*</span></label>
                                        <input type="text" class="form-control required" placeholder="XXXXX-1234567-X" value="{{ $lead->cnic_number }}" name="cnic" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone2">Phone Number 2:</label>
                                        <input type="text" class="form-control"
                                            placeholder="0345-9999999 / 0213-9999999"
                                            value="{{ old('phone2') }}" name="phone2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ntn_no">NTN Number:</label>
                                        <input type="text" class="form-control"
                                            placeholder="(e.g: 1234567-8)"
                                            value="{{ $lead->ntn_number }}" name="ntn_no" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="strn_no">STRN Number:</label>
                                        <input type="text" class="form-control"
                                            placeholder="(e.g: 1234567891234)"
                                            value="{{ old('strn_no') }}" name="strn_no">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="url">URL:</label>
                                        <span class="danger">*</span>
                                        <input type="text" class="form-control required"
                                            value="{{ old('url') }}" name="url"
                                            placeholder="Webiste / Facebook Page">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="shipper_city">City:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="shipper_city" id="shipper_city"
                                                class="select2 form-control required readonly-overlay"
                                                style="width: 100%">
                                                @foreach ($all_cities as $city)
                                                    <option value="{{ $city->id }}"
                                                        {{ old('shipper_city') == $city->id ? 'selected' : '' }} >
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="readonly-overlay"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nature_of_account">Nature Of Account:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="nature_of_account"
                                                id="nature_of_account"
                                                class="select2 form-control required">
                                                @foreach ($account_types as $type)
                                                    <option value="{{ $type->id }}" {{ $user->account_type_id == $type->id ? 'selected' : '' }}> {{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="shipper_product_type">Product Type:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="shipper_product_type"
                                                id="shipper_product_type"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ old('shipper_product_type') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 d-none" id="product_name_div">
                                            <div class="form-group">
                                                <label for="product_name">Product Name:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control"
                                                        value="{{ old('product_name') }}"
                                                        name="product_name"
                                                        placeholder="Product Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="average_shipment">Expected Average Shipments:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <input type="text" class="form-control required"
                                                value="{{ $lead->average_shipment_per_week }}"
                                                name="average_shipment"
                                                placeholder="Expected Average Shipments" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="average_shipment_duration">Expected Average
                                            Shipment Duration:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="average_shipment_duration"
                                                id="average_shipment_duration"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($average_shipment_durations as $average_shipment_duration)
                                                    @if ($average_shipment_duration->id==3)
                                                        <option
                                                            value="{{ $average_shipment_duration->id }}"
                                                            {{ old('average_shipment_duration') == $average_shipment_duration->id ? 'selected' : '' }} selected>
                                                            {{ $average_shipment_duration->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Reference:</label>
                                        <span class="danger">*</span>
                                        <div>
                                            <select name="reference" id="reference"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($references as $reference)
                                                    <option value="{{ $reference->id }}"
                                                        {{ old('reference') == $reference->id ? 'selected' : '' }}>
                                                        {{ $reference->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="sale_person_div">
                                    <div class="form-group">
                                        <label for="sale_person">Sale Person:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="sale_person" id="sale_person"
                                                class="select2 form-control required"
                                                style="width: 100%"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_name">Brand Name:
                                        </label>
                                        <div>
                                            <input type="text" class="form-control"
                                                value="{{ old('brand_name') }}" name="brand_name"
                                                placeholder="Brand Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="segments">Segments:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="segments" id="segments"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($segments as $segment)
                                                    <option value="{{ $segment->id }}">
                                                        {{ $segment->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sub_segments">Sub Segments:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="sub_segments" id="sub_segments"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="referral">Referral:
                                        </label>
                                        <div>
                                            <input type="text" class="form-control"
                                                value="{{ old('referral') }}" name="referral"
                                                placeholder="Referral Code"
                                                data-rule-remote="{{ route('cod.referral.valid') }}"
                                                data-msg-remote="Referral Code not found">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_cycles">Payment Cycles:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="payment_cycles" id="payment_cycles" required
                                                class="select2 form-control"
                                                style="width: 100%">
                                                @foreach ($payment_cycles as $payment_cycle)
                                                    @if ($payment_cycle->id != 1)
                                                        <option value="{{ $payment_cycle->id }}">
                                                            {{ $payment_cycle->name }}
                                                        </option>
                                                    @endif
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div id="checkboxContainer" class="d-none">
                                    </div>

                                    <span class="text-danger d-none" id="payment_cycle_msg"></span>
                                    <span class="text-danger d-none" id="msg_payment">Maximum Selected</span>

                                    <div>
                                        <input type="hidden" id="fortnite_val" name="fortnite" value="">
                                        <label class="d-none" id="label">Day 1</label>
                                        <select name="fornite" id="fornite"
                                            class="select2 form-control d-none"
                                            style="width: 100%">

                                            <option value="none">Please Select Day</option>
                                            @for ($i = 1; $i < 14; $i++)
                                                <option value="{{ $i }}">
                                                    {{ $i . ' day' }}</option>
                                            @endfor
                                        </select>
                                    </div>


                                    <div class="mt-2">
                                        <label class="d-none" id="label_2">Day 2</label>
                                        <select name="fornite_2" id="fornite_2"
                                            class="select2 form-control d-none"
                                            style="width: 100%">
                                        </select>
                                    </div>

                                    <div>
                                        <select name="monthly" id="monthly"
                                            class="select2 form-control d-none"
                                            style="width: 100%">
                                            <option value="none">Please Select Day</option>

                                            @for ($i = 1; $i < 29; $i++)
                                                <option value="{{ $i }}">
                                                    {{ $i . ' day' }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                </div>

                                <input type="hidden" name="selected_days" value="" id="selected_days">
                            </div>
                        </fieldset>
                        <!-- Step 2 -->
                        <h6>Shipping Information</h6>
                        <fieldset>
                            <div class="row position-relative vertical-scroll" id="shipInfo"
                                style="height: 385px;overflow: auto;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pickup_address">
                                            Pickup Address:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" id="pickup_address"
                                            class="form-control required"
                                            value="{{ old('pickup_address.0') }}"
                                            name="pickup_address[]" placeholder="Pickup Address">
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_phone">
                                            Phone Number:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" id="pickup_phone"
                                            class="form-control required"
                                            placeholder="0345-9999999" name="shipping_phone[]"
                                            value="{{ old('shipping_phone.0') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_poc">
                                            Person of Contact:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" id="pickup_poc"
                                            class="form-control required"
                                            placeholder="Person Name"
                                            value="{{ old('shipping_poc.0') }}"
                                            name="shipping_poc[]">
                                    </div>
                                    <div class="form-group">

                                        <label for="url">Product Type:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="product_type[]" id="product_select"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ collect(old('product_type'))->contains($product->id) ? 'selected' : '' }}>
                                                        {{ $product->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shipping_phone">
                                            Brand Name:
                                        </label>
                                        <input type="text" id="pickup_brand_name"
                                            class="form-control" placeholder="Brand Name"
                                            name="pickup_brand_name[]"
                                            value="{{ old('pickup_brand_name.0') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_phone">
                                            Email Address:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="email" name="shipping_email[]"
                                            placeholder="abc@example.com"
                                            value="{{ old('shipping_email.0') }}"
                                            class="form-control required">
                                    </div>
                                    <div class="form-group">

                                        <label for="shipping_city">Shipper City:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="shipping_city[]" id="shipping_city"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($pickup_city_list as $pickup_city)
                                                    <option value="{{ $pickup_city->id }}"
                                                        {{ collect(old('shipping_city'))->contains($pickup_city->id) ? 'selected' : '' }}>
                                                        {{ $pickup_city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add more addresses --}}
                                {{-- Accordion --}}
                                <div class="col-12" id="newAddress">

                                    <!--To find if the form was submitted showing validation error
                             it will not work by defualt -->
                                    @if (old('pickup_address'))
                                        @php($i = 1)
                                    @else
                                        @php($i = 0)
                                    @endif
                                    @while (old('pickup_address.' . $i) != null)

                                        <div class="card naddress"
                                            id="shipping_{{ $i }}">
                                            <div class="card-header">
                                                <h4 class="card-title">New Address</h4>
                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li><a data-action="close"><i
                                                                    class=""></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-content">
                                                <div class="">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="pickup_address">
                                                                    Pickup Address:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control required"
                                                                    value="{{ old('pickup_address.' . $i) }}"
                                                                    name="pickup_address[]"
                                                                    placeholder="Pickup Address">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="shipping_poc">
                                                                    Person of Contact:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control required"
                                                                    value="{{ old('shipping_poc.' . $i) }}"
                                                                    name="shipping_poc[]">
                                                            </div>
                                                            <div class="form-group">

                                                                <label for="url">Product Type:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <div>
                                                                    <select name="product_type[]"
                                                                        class="select2 form-control required"
                                                                        style="width: 100%">
                                                                        @foreach ($products as $product)
                                                                            <option
                                                                                value="{{ $product->id }}"
                                                                                {{ collect(old('product_type.' . $i))->contains($product->id) ? 'selected' : '' }}>
                                                                                {{ $product->product_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="shipping_phone">
                                                                    Phone Number:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control required"
                                                                    placeholder="0399-9999999"
                                                                    value="{{ old('shipping_phone.' . $i) }}"
                                                                    name="shipping_phone[]">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="shipping_email">Email
                                                                    Address:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="email"
                                                                    name="shipping_email[]"
                                                                    class="form-control required"
                                                                    value="{{ old('shipping_email.' . $i) }}"
                                                                    placeholder="abc@example.com">
                                                            </div>
                                                            <div class="form-group">

                                                                <label for="shipping_city">Shipper
                                                                    City:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <div>
                                                                    <select name="shipping_city[]"
                                                                        class="select2 form-control required"
                                                                        style="width: 100%">
                                                                        @foreach ($pickup_city_list as $city)
                                                                            <option
                                                                                value="{{ $city->id }}"
                                                                                {{ collect(old('shipping_city.' . $i))->contains($city->id) ? 'selected' : '' }}>
                                                                                {{ $city->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>{{-- row end --}}
                                                </div>{{-- card body end --}}
                                            </div>
                                        </div>
                                        @php($i++)
                                    @endwhile
                                </div>{{-- column end --}}
                                {{-- Accordion --}}
                                <div class="col-12">

                                    <button id="addMoreAddress" type="button"
                                        class="btn btn-primary btn-min-width mr-1 mb-1"><i
                                            class="la la-plus"></i>&nbsp Add Pickup
                                        Locations</button>

                                </div>

                            </div>


                        </fieldset>
                        <!-- Step 3 -->
                        <h6>Bank Information</h6>
                        <fieldset>
                            <div class="row position-relative vertical-scroll" id="bankInfo"
                                style="height: 385px;overflow: auto;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_name">
                                            Bank Name:
                                            <span class="danger">*</span>
                                        </label>

                                        <div>
                                            <select name="bank_name[]" id="bank_name"
                                                class="select2 form-control required"
                                                style="width: 100%">

                                                @foreach ($banks as $bank)
                                                    <option value="{{ $bank->id }}"
                                                        {{ old('bank_name.0') == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="bank_branch">
                                            Branch Name:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            value="{{ old('bank_branch.0') }}"
                                            name="bank_branch[]" placeholder="Branch Name*">
                                    </div>
                                    <div class="form-group">
                                        <label for="account_name">Account Number:
                                            <span class="danger">*</span></label>
                                        <input type="text" class="form-control required"
                                            value="{{ old('account_no.0') }}" name="account_no[]"
                                            placeholder="Account Number*">
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account_title">
                                            Account Title:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type='text' class="form-control required"
                                            value="{{ old('account_title.0') }}"
                                            name="account_title[]" placeholder="Account Title*">

                                    </div>

                                    <div class="form-group">
                                        <label for="iban"> IBAN Number: <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" placeholder="(e.g: PK37MEZN0001220100004069)" value="{{  old('iban_no.0') }}" name="iban_no[]" id="iban_no" data-rule-maxlength="24" data-rule-maxlength-message="Max character length 24">
                                        <span id="iban_no_error" class="danger" style="display: none;">IBAN Number must be of 24 characters</span>
                                    </div>

                                    <div class="form-group">

                                        <label for="bank_city">Bank City:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="bank_city[]" id="bank_city"
                                                class="select2 form-control required"
                                                style="width: 100%">
                                                @foreach ($all_cities as $bank_city)
                                                    <option value="{{ $bank_city->id }}"
                                                        {{ old('bank_city.0') == $bank_city->id ? 'selected' : '' }}>
                                                        {{ $bank_city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div id="multiple_banks_section">
                                        @if (old('bank_name'))
                                            @php($b = 1)
                                        @else
                                            @php($b = 0)
                                        @endif
                                        @while (old('bank_name.' . $b) != null)
                                            <div class="card nbank" id="">
                                                <div class="card-header">
                                                    <h3 class="card-title">New Bank</h3>
                                                    <div class="heading-elements">
                                                        <ul class="list-inline mb-0">
                                                            <li><a data-action="close"><i class=""></i></a> </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bank_name"> Bank Name: <span class="danger">*</span> </label>
                                                                <div>
                                                                    <select name="bank_name[]" class="select2 form-control required" style="width: 100%">
                                                                        @foreach ($banks as $bank)
                                                                            <option
                                                                                value="{{ $bank->id }}"
                                                                                selected="{{ collect(old('bank_name.' . $b))->contains($bank->id) ? 'selected' : '' }}">
                                                                                {{ $bank->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="bank_branch"> Branch Name: <span class="danger">*</span> </label>
                                                                <input type="text" class="form-control required" value="{{ old('bank_branch.' . $b) }}" name="bank_branch[]" placeholder="Branch Name*">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="account_name">Account Number: <span class="danger">*</span></label>
                                                                <input type="text" class="form-control required" value="{{ old('account_no.' . $b) }}" name="account_no[]" placeholder="Account Number*">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="account_title"> Account Title: <span class="danger">*</span> </label>
                                                                <input type='text' class="form-control required" value="{{ old('account_title.' . $b) }}" name="account_title[]" placeholder="Account Title*">
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="iban"> IBAN Number: <span class="danger">*</span> </label>
                                                                <input type="text" class="form-control required" placeholder="(e.g: PK37MEZN0001220100004069)" value="{{ old('iban_no.' . $b) }}" name="iban_no[]" id="iban_no">
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="bank_city">Bank City: <span class="danger">*</span> </label>
                                                                <div>
                                                                    <select name="bank_city[]" id="bank_city" class="select2 form-control required" style="width: 100%">
                                                                        @foreach ($all_cities as $bank_city)
                                                                            <option
                                                                                value="{{ $bank_city->id }}"
                                                                                {{ collect(old('bank_city.' . $b))->contains($bank_city->id) ? 'selected' : '' }}>
                                                                                {{ $bank_city->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @php($b++)
                                        @endwhile

                                    </div>
                                </div>

                            </div>

                            <div class="row" id="more_banks_btn_div">
                                <div class="col-12">
                                    <button id="addMoreBanks" type="button"
                                        class="btn btn-primary btn-min-width mr-1 mb-1"><i
                                            class="la la-plus"></i>&nbsp; Add More Banks</button>
                                </div>

                            </div>
                            <div id="billing_information_div" class="row d-none">
                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label for="cycle_of_invoicing">Cycle Of Invoicing:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            <select name="cycle_of_invoicing"
                                                id="cycle_of_invoicing"
                                                class="select2 form-control required">
                                                @foreach ($invoicing_cycle as $cycle)
                                                    <option value="{{ $cycle->id }}"
                                                        {{ old('cycle_of_invoicing') == $cycle->id ? 'selected' : '' }}>
                                                        {{ $cycle->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">

                                    <div class="form-group" id="generation_div">

                                        <label for="generation_date">Generation Date:
                                            <span class="danger">*</span>
                                        </label>
                                        <div>
                                            {{--  <select name="generation_date" id="generation_date" class="select2 form-control required" style="width: 100%"></select> --}}
                                            <select name="generation_date" id="generation_date"
                                                class="select2 form-control d-none"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_branch">
                                            Billing Person Name:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            value="{{ old('billing_person_name') }}"
                                            name="billing_person_name"
                                            placeholder="Billing Person Name*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_branch">
                                            Billing Person Phone:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" id="billing_phone"
                                            class="form-control required"
                                            value="{{ old('billing_person_phone') }}"
                                            name="billing_person_phone"
                                            placeholder="Billing Person Phone*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_branch">
                                            Billing Person Email:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="email" class="form-control required"
                                            value="{{ old('billing_person_email') }}"
                                            name="billing_person_email"
                                            placeholder="abc@mail.com*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_branch">
                                            Billing Address:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control required"
                                            value="{{ old('billing_address') }}"
                                            name="billing_address" placeholder="Billing Address*">
                                    </div>

                                </div>

                            </div>

                        </fieldset><!-- Step 4 -->
                        <h6>Documents Attachment</h6>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cnic_front_image">
                                            Picture of CNIC (Front):
                                            <span class="danger">*</span>
                                        </label>
                                        <input class="form-control form-control-sm  required"
                                            type="file" name="cnic_front_image"
                                            id="cnic_front_image"
                                            data-rule-extension="jpeg|jpg|png"
                                            data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                            data-rule-accept="image/*"
                                            data-msg-accept="Only Image file allowed"
                                            data-rule-maxsize="2097152"
                                            data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                    </div>
                                    <div class="form-group">
                                        <label for="cnic_back_image">
                                            Picture of CNIC (Back):
                                            <span class="danger">*</span>
                                        </label>
                                        <input class="form-control form-control-sm  required"
                                            type="file" name="cnic_back_image"
                                            id="cnic_back_image"
                                            data-rule-extension="jpeg|jpg|png"
                                            data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                            data-rule-accept="image/*"
                                            data-msg-accept="Only Image file allowed"
                                            data-rule-maxsize="2097152"
                                            data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                    </div>
                                    <div class="form-group">
                                        <label for="blank_cheque_image">
                                            Picture of Blank cheque:
                                            <span class="danger">*</span>
                                        </label>
                                        <input class="form-control form-control-sm  required"
                                            type="file" name="blank_cheque_image"
                                            id="blank_cheque_image"
                                            data-rule-extension="jpeg|jpg|png"
                                            data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                            data-rule-accept="image/*"
                                            data-msg-accept="Only Image file allowed"
                                            data-rule-maxsize="2097152"
                                            data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                        <!-- Step 5 -->
                        <h6>Add Rates</h6>

                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                    
                                        <div class="card-header">
                                            <h2 class="font-large-1">{{$shipper->name}}
                                                <div class="badge badge-success pull-right">Reimbursement Account</div>
                                            </h2>
                                            @include('admin.inc.messages')
                                        </div>
                    
                                            <div class="card-content">
                    
                                                <div id="" class="card-header border-success">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h3 class="display-inline card-title lead success">Rush</h3>
                                                            @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                                                <label class="display-inline ml-1">Make Default</label>
                                                                <input type="checkbox" name="on_default" id="on_default"
                                                                       class="switchery on_default" data-size="xs" data-switchery="true">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <a href="javascript:void(0);" class="pull-right" id="on_main_switch"><input
                                                                        name="on_main_switch" type="checkbox"
                                                                        class="switchery on-main-switch" data-size="sm"/></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="overnight" class="card border-success hide"
                                                     aria-expanded="true">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                           
                    
                    
                                                            <div class="weight-addition-overnight">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <h3 class="card-title">Weight Charges</h3>
                                                                    </div>
                    
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Up</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Down</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Weight Addition</label>
                                                                    </div>
                                                                    <div class="col-2 text-center">
                                                                        <label class="card-title">KG Range</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Local Charges</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class A</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class B</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class C</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class D</label>
                                                                    </div>
                                                                    <div class="col-1"></div>
                                                                </div>
                                                                @foreach($weight[1] as $index => $onweight)
                                                                    <div class="row" id="on_weight_row0">
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="on_range_up{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->range_up}}"
                                                                                       @if($index == 0) data-rule-min="{{$on}}"
                                                                                       data-msg-min="Minimum chargeable weight can not be less than {{$on}}"
                                                                                       @endif name="on_wa_range_up[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="on_range_down{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->range_down}}"
                                                                                       name="on_wa_range_down[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <div class="form-group " style="padding-top: 8px;">
                                                                                <input type="checkbox" id="OvernightSwitch{{$index}}"
                                                                                       class="switchery weightAdditionOvernight"
                                                                                       data-color="success" data-size="sm"
                                                                                       name="on_wa_switch[{{$index}}]"
                                                                                       @if($onweight->weight_addition == 1) checked @endif>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2 text-center">
                    
                                                                            <fieldset style="padding-top: 5px;">
                                                                                <div class="input-group input-group-sm form-group">
                                                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                                                           {{--value="0.5"--}} {{--disabled--}} data-bts-button-down-class="btn btn-success"
                                                                                           data-bts-button-up-class="btn btn-success"
                                                                                           name="on_wa_spkg[{{$index}}]"
                                                                                           data-rule-required="true"
                                                                                           data-msg-required="This field is required"
                                                                                           value="@if($onweight->kg_range > 0.5){{$onweight->kg_range}} @else 0.5 @endif"
                                                                                           @if($onweight->weight_addition == 0) disabled @endif>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->local_or_6hr}}"
                                                                                       name="on_wa_local_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->national_charges_class_0}}"
                                                                                       name="on_class_0_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->national_charges_class_1}}"
                                                                                       name="on_class_1_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->national_charges_class_2}}"
                                                                                       name="on_class_2_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$onweight->national_charges_class_3}}"
                                                                                       name="on_class_3_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-1">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 on_weight_close"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                    
                                                                    </div>
                                                                @endforeach
                                                            </div>{{--weight addition div--}}
                                                            <div>
                                                                <button type="button" class="btn btn-outline-success mr-1"
                                                                        title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i>
                                                                </button>
                                                            </div>
                                                            <div class="row mt-2">
                    
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Replacement</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$shippingType[1][0]->replacement_charges}}"
                                                                                   name="on_replacement_charges">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Try &amp; Buy</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$shippingType[1][0]->try_and_buy_charges}}"
                                                                                   name="on_tnb_charges">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6 text-center">
                                                                    <div class="row">
                                                                        <div class="col-3 mt-1">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <label class="card-title mr-1">DWS Weight </label>
                                                                                    <input type="checkbox" name="on_dws" id="on_dws"
                                                                                           class="switchery on_dws" data-size="xs"
                                                                                           data-switchery="true" checked>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <select name="on_dws_weight" id="on_dws_weight"
                                                                                            class="form-control">
                                                                                        <option value="1">High</option>
                                                                                        <option value="2">Low</option>
                                                                                    </select>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                    
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="on_cash_handling_switch"
                                                                               class="switchery cashChargesOvernight" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                    
                                                            <div class="cash-handling-div-overnight slabs">
                                                                @foreach($cashHandling[1] as $index => $cash)
                                                                    <div class="row">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="on_cash_range_up[{{$index}}]"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       type="text" class="form-control numeric"
                                                                                       value="{{$cash->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="on_cash_range_down[{{$index}}]"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       type="text" class="form-control numeric"
                                                                                       value="{{$cash->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="on_cash_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        @if($index>0)
                                                                            <div class="col">
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 on_weight_close"><i
                                                                                            class=""></i></span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="cash-handling-btn-overnight">
                                                                <button id="addMoreSlabs" type="button" class="btn btn-outline-success mr-1"
                                                                        title="Add more slabs"><i class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                    
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Insurance Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="on_insurance_charges_switch"
                                                                               class="switchery insuranceChargesOvernight"
                                                                               data-color="success" data-size="sm"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                                                            <div class="insurance-charges-div-overnight slabs">
                                                                @foreach($insuranceCharges[1] as $index => $insurance)
                                                                    <div class="row" id="on_insurance_handle_0">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="on_ins_range_up[{{$index}}]"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       type="text" class="form-control numeric"
                                                                                       value="{{$insurance->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="on_ins_range_down[{{$index}}]"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       type="text" class="form-control numeric"
                                                                                       value="{{$insurance->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="on_ins_charges[{{$index}}]"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       type="text" class="form-control dec-percent"
                                                                                       value="{{$insurance->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        @if($index>0)
                                                                            <div class="col">
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 on_weight_close"><i
                                                                                            class=""></i></span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="insurance-charges-btn-overnight">
                                                                <button id="addMoreSlabsInsurance" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Return Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="on_return_switch"
                                                                               class="switchery returnChargesOvernight" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row return-charges-div-overnight justify-content-center">
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">Local Charges</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount" name="on_return_local_charges"
                                                                               value="{{$returnCharges[1][0]->local}}">
                                                                    </fieldset>
                                                                </div>
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class A</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount" name="on_return_class_0_charges"
                                                                               value="{{$returnCharges[1][0]->national_charges_class_0}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class B</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="on_return_class_1_charges"
                                                                               value="{{$returnCharges[1][0]->national_charges_class_1}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class C</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="on_return_class_2_charges"
                                                                               value="{{$returnCharges[1][0]->national_charges_class_2}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class D</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="on_return_class_3_charges"
                                                                               value="{{$returnCharges[1][0]->national_charges_class_3}}">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                    
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Fuel Surcharge</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="overnight_fuel_switch"
                                                                               class="switchery fuelSurchargeOvernight" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row fuel-surcharge-div-overnight">
                    
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <input type="text" class="form-control "
                                                                                   name="overnight_fuel_surcharge" data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$fuelCharges[1][0]->fuel_surcharge}}" readonly>
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                    
                                                            </div>
                    
                    
                                                            <hr>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="" class="card-header mt-1 border-success">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h3 class="display-inline card-title lead success">Saver Plus</h3>
                                                            @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                                                <label class="display-inline ml-1">Make Default</label>
                                                                <input type="checkbox" name="ol_default" id="ol_default"
                                                                       class="switchery ol_default" data-size="xs" data-switchery="true">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <a id="ol_main_switch" href="javascript:void(0);" class="pull-right"><input
                                                                        name="ol_main_switch" type="checkbox" id=""
                                                                        class="switchery ol-main-switch" data-size="sm"/></a>
                                                        </div>
                                                    </div>
                    
                                                </div>
                                                <div id="overland" class="border-success no-border-top card hide" aria-expanded="false">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                          
                                                            <div class="weight-addition-overland">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <h3 class="card-title">Weight Charges</h3>
                                                                    </div>
                    
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Up</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Down</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Weight Addition</label>
                                                                    </div>
                                                                    <div class="col-2 text-center">
                                                                        <label class="card-title">KG Range</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Local Charges</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class A</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class B</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class C</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class D</label>
                                                                    </div>
                                                                    <div class="col-1"></div>
                                                                </div>
                                                                @foreach($weight[2] as $index => $olweight)
                                                                    <div class="row">
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="ol_range_up{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->range_up}}"
                                                                                       @if($index == 0) data-rule-min="{{$ol}}"
                                                                                       data-msg-min="Minimum chargeable weight can not be less than {{$ol}}"
                                                                                       @endif name="ol_wa_range_up[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="ol_range_down{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->range_down}}"
                                                                                       name="ol_wa_range_down[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <div class="form-group " style="padding-top: 8px;">
                                                                                <input type="checkbox" id="OverlandSwitch{{$index}}"
                                                                                       class="switchery weightAdditionOverland"
                                                                                       data-color="success" data-size="sm"
                                                                                       name="ol_wa_switch[{{$index}}]"
                                                                                       @if($olweight->weight_addition == 1) checked @endif>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2 text-center">
                    
                                                                            <fieldset style="padding-top: 5px;">
                                                                                <div class="input-group input-group-sm form-group">
                                                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                                                           {{--value="0.5"--}}  data-bts-button-down-class="btn btn-success"
                                                                                           data-bts-button-up-class="btn btn-success"
                                                                                           name="ol_wa_spkg[{{$index}}]"
                                                                                           data-rule-required="true"
                                                                                           data-msg-required="This field is required"
                                                                                           value="@if($olweight->kg_range > 0.5){{$olweight->kg_range}} @else 0.5 @endif"
                                                                                           @if($olweight->weight_addition == 0) disabled @endif>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->local_or_6hr}}"
                                                                                       name="ol_wa_local_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->national_charges_class_0}}"
                                                                                       name="ol_class_0_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->national_charges_class_1}}"
                                                                                       name="ol_class_1_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->national_charges_class_2}}"
                                                                                       name="ol_class_2_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$olweight->national_charges_class_3}}"
                                                                                       name="ol_class_3_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-1">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>{{--Row--}}
                                                                @endforeach
                                                            </div>{{--weight addition div--}}
                                                            <div>
                                                                <button type="button" class="btn btn-outline-success mr-1"
                                                                        title="Add more slabs" id="overland_weightadd"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Replacement</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   name="ol_replacement_charges"
                                                                                   value="{{$shippingType[2][0]->replacement_charges}}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Try &amp; Buy</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   name="ol_tnb_charges"
                                                                                   value="{{$shippingType[2][0]->try_and_buy_charges}}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6 text-center">
                                                                    <div class="row">
                                                                        <div class="col-3 mt-1">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <label class="card-title mr-1">DWS Weight </label>
                                                                                    <input type="checkbox" name="ol_dws" id="ol_dws"
                                                                                           class="switchery ol_dws" data-size="xs"
                                                                                           data-switchery="true" checked>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <select name="ol_dws_weight" id="ol_dws_weight"
                                                                                            class="form-control">
                                                                                        <option value="1">High</option>
                                                                                        <option value="2">Low</option>
                                                                                    </select>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                    
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="ol_cash_handling_switch"
                                                                               class="switchery cashChargesOverland" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                    
                                                            <div class="cash-handling-div-overland slabs">
                                                                @foreach($cashHandling[2] as $index => $cash)
                                                                    <div class="row" id="ol_cash_handle_0">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="ol_cash_range_up[{{$index}}]" type="text"
                                                                                       class="form-control  numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="ol_cash_range_down[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="ol_cash_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            @if($index>0)
                                                                                <span class="ol_row_delete rounded btn-sm-width mr-1 mb-1"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="cash-handling-btn-overland">
                                                                <button id="overlandaddMoreSlabs" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                    
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Insurance Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="ol_insurance_charges_switch"
                                                                               class="switchery insuranceChargesoverland"
                                                                               data-color="success" data-size="sm"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                                                            <div class="insurance-charges-div-overland slabs">
                                                                @foreach($insuranceCharges[2] as $index => $ol_insurance)
                                                                    <div class="row">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="ol_ins_range_up[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$ol_insurance->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="ol_ins_range_down[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$ol_insurance->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="ol_ins_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$ol_insurance->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 ol_row_delete"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="insurance-charges-btn-overland">
                                                                <button id="oladdMoreSlabsInsurance" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Return Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="ol_return_switch"
                                                                               class="switchery returnChargesOverland" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row return-charges-div-overland">
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">Local Charges</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount" name="ol_return_local_charges"
                                                                               value="{{$returnCharges[2][0]->local}}">
                                                                    </fieldset>
                                                                </div>
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class A</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount" name="ol_return_class_0_charges"
                                                                               value="{{$returnCharges[2][0]->national_charges_class_0}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class B</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="ol_return_class_1_charges"
                                                                               value="{{$returnCharges[2][0]->national_charges_class_1}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class C</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="ol_return_class_2_charges"
                                                                               value="{{$returnCharges[2][0]->national_charges_class_2}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class D</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="ol_return_class_3_charges"
                                                                               value="{{$returnCharges[2][0]->national_charges_class_3}}">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Fuel Surcharge</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="overland_fuel_switch"
                                                                               class="switchery fuelSurchargeOverland" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row fuel-surcharge-div-overland">
                    
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <input type="text" class="form-control "
                                                                                   name="overland_fuel_surcharge" data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$fuelCharges[2][0]->fuel_surcharge}}" readonly>
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                    
                                                            </div>
                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="" class="card-header mt-1 border-success">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h3 class="display-inline card-title lead success">Swift</h3>
                                                            @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                                                <label class="display-inline ml-1">Make Default</label>
                                                                <input type="checkbox" name="det_default" id="det_default"
                                                                       class="switchery det_default" data-size="xs" data-switchery="true">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <a id="detain_main_switch" href="javascript:void(0);" class="pull-right"><input
                                                                        name="detain_main_switch" type="checkbox" id=""
                                                                        class="switchery detain-main-switch" data-size="sm"/></a>
                                                        </div>
                                                    </div>
                    
                                                </div>
                                                <div id="detain" class="border-success no-border-top card hide"
                                                     aria-expanded="false">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            
                                                            <div class="weight-addition-detain">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <h3 class="card-title">Weight Charges</h3>
                                                                    </div>
                    
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Up</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Down</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Weight Addition</label>
                                                                    </div>
                                                                    <div class="col-2 text-center">
                                                                        <label class="card-title">KG Range</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Local Charges</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class A</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class B</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class C</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">National Charges Class D</label>
                                                                    </div>
                                                                    <div class="col-1"></div>
                                                                </div>
                                                                @foreach($weight[3] as $index => $detweight)
                                                                    <div class="row">
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="detain_range_up{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->range_up}}"
                                                                                       @if($index == 0) data-rule-min="{{$det}}"
                                                                                       data-msg-min="Minimum chargeable weight can not be less than {{$det}}"
                                                                                       @endif name="detain_wa_range_up[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="detain_range_down{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->range_down}}"
                                                                                       name="detain_wa_range_down[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <div class="form-group " style="padding-top: 8px;">
                                                                                <input type="checkbox" id="DetainSwitch{{$index}}"
                                                                                       class="switchery weightAdditionDetain"
                                                                                       data-color="success" data-size="sm"
                                                                                       name="detain_wa_switch[{{$index}}]"
                                                                                       @if($detweight->weight_addition == 1) checked @endif>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2 text-center">
                    
                                                                            <fieldset style="padding-top: 5px;">
                                                                                <div class="input-group input-group-sm form-group">
                                                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                                                           data-bts-button-down-class="btn btn-success"
                                                                                           data-bts-button-up-class="btn btn-success"
                                                                                           name="detain_wa_spkg[{{$index}}]"
                                                                                           value="@if($detweight->kg_range > 0.5){{$detweight->kg_range}} @else 0.5 @endif"
                                                                                           @if($detweight->weight_addition == 0) disabled @endif>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->local_or_6hr}}"
                                                                                       name="detain_wa_local_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->national_charges_class_0}}"
                                                                                       name="detain_class_0_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->national_charges_class_1}}"
                                                                                       name="detain_class_1_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->national_charges_class_2}}"
                                                                                       name="detain_class_2_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$detweight->national_charges_class_3}}"
                                                                                       name="detain_class_3_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-1">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                    
                                                                    </div>{{--Row--}}
                                                                @endforeach
                                                            </div>{{--weight addition div--}}
                                                            <div>
                                                                <button type="button" class="btn btn-outline-success mr-1"
                                                                        title="Add more slabs" id="detain_weightadd"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Replacement</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   name="detain_replacement_charges"
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$shippingType[3][0]->replacement_charges}}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Try &amp; Buy</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   name="detain_tnb_charges" data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$shippingType[3][0]->try_and_buy_charges}}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6 text-center">
                                                                    <div class="row">
                                                                        <div class="col-3 mt-1">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <label class="card-title mr-1">DWS Weight </label>
                                                                                    <input type="checkbox" name="detain_dws" id="detain_dws"
                                                                                           class="switchery detain_dws" data-size="xs"
                                                                                           data-switchery="true" checked>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <select name="detain_dws_weight" id="detain_dws_weight"
                                                                                            class="form-control">
                                                                                        <option value="1">High</option>
                                                                                        <option value="2">Low</option>
                                                                                    </select>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                    
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="detain_cash_handling_switch"
                                                                               class="switchery cashChargesDetain" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                    
                                                            <div class="cash-handling-div-detain slabs">
                                                                @foreach($cashHandling[3] as $index => $cash)
                                                                    <div class="row">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="detain_cash_range_up[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="detain_cash_range_down[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2">
                                                                            <fieldset class="form-group">
                                                                                <input name="detain_cash_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            @if($index>0)
                                                                                <span class="detain_row_delete rounded btn-sm-width mr-1 mb-1"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="cash-handling-btn-detain">
                                                                <button id="detainaddMoreSlabs" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                    
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Insurance Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="detain_insurance_charges_switch"
                                                                               class="switchery insuranceChargesdetain" data-color="success"
                                                                               data-size="sm"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                                                            <div class="insurance-charges-div-detain slabs">
                                                                @foreach($insuranceCharges[3] as $index => $det_insurance)
                                                                    <div class="row">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="detain_ins_range_up[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$det_insurance->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="detain_ins_range_down[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$det_insurance->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2">
                                                                            <fieldset class="form-group">
                                                                                <input name="detain_ins_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$det_insurance->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 detain_row_delete"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="insurance-charges-btn-detain">
                                                                <button id="detainaddMoreSlabsInsurance" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Return Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="detain_return_switch"
                                                                               class="switchery returnChargesDetain" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row return-charges-div-detain">
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">Local Charges</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount"
                                                                               name="detain_return_local_charges"
                                                                               value="{{$returnCharges[3][0]->local}}">
                                                                    </fieldset>
                                                                </div>
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class A</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount"
                                                                               name="detain_return_class_0_charges"
                                                                               value="{{$returnCharges[3][0]->national_charges_class_0}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class B</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="detain_return_class_1_charges"
                                                                               value="{{$returnCharges[3][0]->national_charges_class_1}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class C</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="detain_return_class_2_charges"
                                                                               value="{{$returnCharges[3][0]->national_charges_class_2}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges Class D</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control dec-percent"
                                                                               name="detain_return_class_3_charges"
                                                                               value="{{$returnCharges[3][0]->national_charges_class_3}}">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                    
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Fuel Surcharge</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="detain_fuel_switch"
                                                                               class="switchery fuelSurchargeDetain" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row fuel-surcharge-div-detain">
                    
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <input type="text" class="form-control "
                                                                                   name="detain_fuel_surcharge" data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$fuelCharges[3][0]->fuel_surcharge}}" readonly>
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                    
                                                            </div>
                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="" class="card-header mt-1 border-success">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h3 class="display-inline card-title lead success">Sameday</h3>
                                                            @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                                                <label class="display-inline ml-1">Make Default</label>
                                                                <input type="checkbox" name="sameday_default" id="sameday_default"
                                                                       class="switchery sameday_default" data-size="xs"
                                                                       data-switchery="true">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <a id="sameday_main_switch" href="javascript:void(0);" class="pull-right"><input
                                                                        name="sameday_main_switch" type="checkbox" id=""
                                                                        class="switchery sameday-main-switch" data-size="sm"/></a>
                                                        </div>
                                                    </div>
                    
                                                </div>
                                                <div id="sameday" class="border-success no-border-top card hide">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                          
                                                            <div class="weight-addition-sameday">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <h3 class="card-title">Weight Charges</h3>
                                                                    </div>
                    
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Up</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Range Down</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Weight Addition</label>
                                                                    </div>
                                                                    <div class="col-2 text-center">
                                                                        <label class="card-title">KG Range</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">6hr Charges</label>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <label class="card-title">Sameday Charges</label>
                                                                    </div>
                                                                    <div class="col-1"></div>
                    
                                                                </div>
                                                                @foreach($weight[4] as $index => $sameweight)
                                                                    <div class="row">
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="sameday_range_up{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$sameweight->range_up}}"
                                                                                       @if($index == 0) data-rule-min="{{$same_day}}"
                                                                                       data-msg-min="Minimum chargeable weight can not be less than {{$same_day}}"
                                                                                       @endif name="sameday_wa_range_up[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="sameday_range_down{{$index}}"
                                                                                       class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$sameweight->range_down}}"
                                                                                       name="sameday_wa_range_down[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                    
                                                                            <div class="form-group " style="padding-top: 8px;">
                                                                                <input type="checkbox" id="SamedaySwitch{{$index}}"
                                                                                       class="switchery weightAdditionSameday"
                                                                                       data-color="success" data-size="sm"
                                                                                       name="sameday_wa_switch[{{$index}}]"/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2 text-center">
                    
                                                                            <fieldset style="padding-top: 5px;">
                                                                                <div class="input-group input-group-sm form-group">
                                                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                                                           data-rule-required="true"
                                                                                           data-msg-required="This field is required"
                                                                                           value="0.5" disabled
                                                                                           data-bts-button-down-class="btn btn-success"
                                                                                           data-bts-button-up-class="btn btn-success"
                                                                                           name="sameday_wa_spkg[{{$index}}]">
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$sameweight->local_or_6hr}}"
                                                                                       name="sameday_wa_local_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$sameweight->national_charges_class_0}}"
                                                                                       name="sameday_class_0_charges[{{$index}}]">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-1">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>{{--Row--}}
                                                                @endforeach
                                                            </div>{{--weight addition div--}}
                                                            <div>
                                                                <button type="button" class="btn btn-outline-success mr-1"
                                                                        title="Add more slabs" id="sameday_weightadd"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Replacement</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   name="sameday_replacement_charges"
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$shippingType[4][0]->replacement_charges}}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-3 text-center">
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Try & Buy</span>
                                                                            </div>
                                                                            <input type="text" class="form-control percent"
                                                                                   name="sameday_tnb_charges" data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$shippingType[4][0]->try_and_buy_charges}}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6 text-center">
                                                                    <div class="row">
                                                                        <div class="col-3 mt-1">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <label class="card-title mr-1">DWS Weight </label>
                                                                                    <input type="checkbox" name="sameday_dws"
                                                                                           id="sameday_dws" class="switchery sameday_dws"
                                                                                           data-size="xs" data-switchery="true" checked>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <fieldset>
                                                                                <div class="input-group form-group">
                                                                                    <select name="sameday_dws_weight"
                                                                                            id="sameday_dws_weight" class="form-control">
                                                                                        <option value="1">High</option>
                                                                                        <option value="2">Low</option>
                                                                                    </select>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                    
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="sameday_cash_handling_switch"
                                                                               class="switchery cashChargesSameday" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                    
                                                            <div class="cash-handling-div-sameday slabs">
                                                                @foreach($cashHandling[4] as $index => $cash)
                                                                    <div class="row">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="sameday_cash_range_up[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="sameday_cash_range_down[{{$index}}]"
                                                                                       type="text" class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="sameday_cash_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$cash->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            @if($index>0)
                                                                                <span class="sameday_row_delete rounded btn-sm-width mr-1 mb-1"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="cash-handling-btn-sameday">
                                                                <button id="samedayaddMoreSlabs" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                    
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Insurance Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="sameday_insurance_charges_switch"
                                                                               class="switchery insuranceChargessameday"
                                                                               data-color="success" data-size="sm"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Up</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Range Down</label>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                </div>
                                                            </div>
                                                            <div class="insurance-charges-div-sameday slabs">
                                                                @foreach($insuranceCharges[4] as $index => $same_insurance)
                                                                    <div class="row">
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="sameday_ins_range_up[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$same_insurance->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="sameday_ins_range_down[{{$index}}]" type="text"
                                                                                       class="form-control numeric"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$same_insurance->range_down}}">
                                                                            </fieldset>
                                                                        </div>
                    
                                                                        <div class="col-md-2 text-center">
                                                                            <fieldset class="form-group">
                                                                                <input name="sameday_ins_charges[{{$index}}]" type="text"
                                                                                       class="form-control dec-percent"
                                                                                       data-rule-required="true"
                                                                                       data-msg-required="This field is required"
                                                                                       value="{{$same_insurance->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col">
                                                                            @if($index>0)
                                                                                <span class= rounded btn-sm-width mr-1 mb-1 sameday_row_delete"><i
                                                                                            class=""></i></span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="insurance-charges-btn-sameday">
                                                                <button id="samedayaddMoreSlabsInsurance" type="button"
                                                                        class="btn btn-outline-success mr-1" title="Add more slabs"><i
                                                                            class="la la-plus"></i></button>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Return Charges</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="sameday_return_switch"
                                                                               class="switchery returnChargesSameday" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row return-charges-div-sameday">
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">Local Charges</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount"
                                                                               name="sameday_return_local_charges"
                                                                               value="{{$returnCharges[4][0]->local}}">
                                                                    </fieldset>
                                                                </div>
                    
                                                                <div class="col text-center">
                                                                    <label class="card-title">National Charges</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true"
                                                                               data-msg-required="This field is required"
                                                                               class="form-control amount"
                                                                               name="sameday_return_class_0_charges"
                                                                               value="{{$returnCharges[4][0]->national_charges_class_0}}">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                    
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <h3 class="card-title">Fuel Surcharge</h3>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group ">
                                                                        <input type="checkbox" name="sameday_fuel_switch"
                                                                               class="switchery fuelSurchargeSameday" data-color="success"
                                                                               data-size="sm" checked/>
                                                                    </div>
                                                                </div>
                                                            </div>
                    
                                                            <div class="row fuel-surcharge-div-sameday">
                    
                                                                <div class="col-md-2 text-center">
                                                                    <label class="card-title">Charges</label>
                                                                    <fieldset>
                                                                        <div class="input-group form-group">
                                                                            <input type="text" class="form-control "
                                                                                   name="sameday_fuel_surcharge" data-rule-required="true"
                                                                                   data-msg-required="This field is required"
                                                                                   value="{{$fuelCharges[4][0]->fuel_surcharge}}" readonly>
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                    
                                                            </div>
                    
                    
                                                  
                                                        </div>
                                                    </div>
                                                </div>
                    
    
                    
                                            </div>
                                    </div>
                                </div>
                            </div>
                    
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

   
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        #generation_date_root .picker__holder {
            bottom: 0;
            margin-bottom: 42px;
        }


        /* Style the label to make it look like a button */
        #checkboxContainer label {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Style the checkbox to be hidden */
        #checkboxContainer input[type="checkbox"] {
            display: none;
        }

        /* Style the label when the checkbox is checked */
        #checkboxContainer input[type="checkbox"]:checked+label {
            background-color: #56e73c;
        }

        .readonly-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: transparent;
            pointer-events: none; /* Allows clicks to fall through */
            z-index: 999; /* Ensure overlay is on top */
            }

        .hide {
            display: none;
        }
    </style>
    
@endsection
@section('js')

<script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/extensions/jquery.steps.min.js')}}" type="text/javascript"></script>
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/forms/wizard-steps.js')}}" type="text/javascript"></script>
<!-- BEGIN MODERN JS-->

<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}"></script>

<script>
    //$('.pickadate').pickadate();
    $(document).ready(function() {
        $('input[type="checkbox"]').addClass('d-none');
        $('input[name="on_main_switch"]').removeClass('d-none');
        $('input[name="ol_main_switch"]').removeClass('d-none');
        $('input[name="detain_main_switch"]').removeClass('d-none');
        $('input[name="sameday_main_switch"]').removeClass('d-none');
        $('input[name="warehouse_main_switch"]').removeClass('d-none');

        $('#shipper_city').select2({
            width: '100%',
            placeholder: 'Select City',
        }).bind('change', function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    url: '{!! route('cod.wordpress.salesPerson') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data) {
                    if (data.status == 0) {
                        $('#sale_person').empty();

                        $.each(data.sale_persons, function(key, value) {
                            var newOption = "<option value=" + value.id + ">" + value
                                .name + "</option>";
                            $('#sale_person').append(newOption);
                        });
                        $('#sale_person').val('').trigger('change');

                        @if ($lead != null)
                            @if ($lead->sale_person_id != null)
                                $('#sale_person').val({{ $lead->sale_person_id }}).trigger(
                                    'change');
                            @endif
                        @endif
                    } else {
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });

            }



        });


        @if ($lead != null)
            $('#shipper_city').val({{ $lead->city_id }}).trigger('change');
        @endif

        $('#generation_date').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Date',
            // dropdownParent:$('#registership')
        });

        //multiple banks
        $('select[name="bank_name[]"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Bank',
        });

        $('#payment_cycles').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Payment Cycle',

        });

        $('#bank_name').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Bank',

        });
        $('#nature_of_account').select2({
            width: '100%',
            placeholder: 'Select Nature of Account',

        }).bind('change', function() {

            if (this.value == 2) {
                $('#billing_information_div').removeClass('d-none');
                $('#more_banks_btn_div').addClass('d-none');
            } else {

                $('#more_banks_btn_div').removeClass('d-none');
                $('#billing_information_div').addClass('d-none');
            }
        });

        $('#bank_city').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Bank City',

        });

        $('select[name="bank_city[]"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Bank City',

        });

        $('select[name="shipping_city[]"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Pickup City',

        });
        $('select[name="product_type[]"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Product Type',
            // dropdownParent:$('#registership')
        });
        $('select[name="shipper_product_type"]').prepend('<option value="" selected="selected"></option>')
            .select2({
                placeholder: 'Select Product Type',
                // dropdownParent:$('#registership')
            }).bind('select2:select', function() {
                var product_type_id = $(this).val();
                if (product_type_id == 24) {
                    $('#product_name_div').removeClass('d-none');
                    $('#product_name').addClass('required');
                } else {
                    $('#product_name_div').addClass('d-none');
                    $('#product_name').removeClass('required');

                }
            });
        $('select[name="average_shipment_duration"]')
            .select2({
                placeholder: 'Select Duration',
                // dropdownParent:$('#registership')
            });
        $('select[name="reference"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Reference',
            // dropdownParent:$('#registership')
        });
        $('select[name="sale_person"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Sale Person',
            // dropdownParent:$('#registership')
        });
        $('select[name="sub_segments"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Sub Segments',
        });
        $('select[name="segments"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Segment',
        }).bind('change', function() {
            var id = $(this).val();
            $(this).valid();
            $.ajax({
                url: '{!! route('cod.wordpress.get_sub_segment') !!}',
                method: 'POST',
                data: {
                    'segment_id': id,
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function(data) {
                if (data.status == 0) {
                    $('#sub_segments').children().remove();
                    $('#sub_segments').prepend('<option value="" selected="selected"></option>')
                    $.each(data.sub_segments, function(index, sub_segments) {
                        $('#sub_segments').append('<option value="' + sub_segments.id +
                            '" id="trax_center">' + sub_segments.name + '</option>')
                    });
                }
            });
        });



        $("input[name='average_shipment']").inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false
        });
        $('#peye').on('mousedown', function() {
            $('input[name="password"]').attr('type', 'text')
        }).on('mouseup', function() {
            $('input[name="password"]').attr('type', 'password')
        });
        $("input[name='cnic']").inputmask({
            'mask': "99999-9999999-9",
            'clearIncomplete': true
        });
      

        $('#shipInfo').perfectScrollbar({
            suppressScrollX: true,
            theme: 'dark',
            wheelPropagation: true
        });
        $('#shipInfo').perfectScrollbar('update');
        $('#bankInfo').perfectScrollbar({
            suppressScrollX: true,
            theme: 'dark',
            wheelPropagation: true
        });
        $('#bankInfo').perfectScrollbar('update');

        var banks_count = parseInt('{{ $b }}');
        $('body').on('click', '#addMoreBanks', function() {

            $.get('new/bank', function(bView) {
                $('#multiple_banks_section').append(bView);
            }).done(function() {
                var bcc = $('.card.nbank').length;
                var bid = $('.card.nbank').eq(bcc - 1);
                var banking_div = banks_count + 1;
                bid.attr('id', 'banking_' + banking_div);
                $('#banking_' + banking_div + ' h3.card-title').text('Bank ' + banking_div);
                var innerdivcount = banks_count + 1;
                var temp_bank_name = $('#banking_' + banking_div +
                    ' select[name="temp_bank_name"]');
                temp_bank_name.attr('name', 'bank_name[' + innerdivcount + ']');
                var temp_bank_city = $('#banking_' + banking_div +
                    ' select[name="temp_bank_city"]');
                temp_bank_city.attr('name', 'bank_city[' + innerdivcount + ']');
                var temp_bank_branch = $('#banking_' + banking_div +
                    ' input[name="temp_bank_branch"]');
                temp_bank_branch.attr('name', 'bank_branch[' + innerdivcount + ']');
                var temp_account_no = $('#banking_' + banking_div +
                    ' input[name="temp_account_no"]');
                temp_account_no.attr('name', 'account_no[' + innerdivcount + ']');
                var temp_account_title = $('#banking_' + banking_div +
                    ' input[name="temp_account_title"]');
                temp_account_title.attr('name', 'account_title[' + innerdivcount + ']');
                var temp_iban_no = $('#banking_' + banking_div + ' input[name="temp_iban_no"]');
                temp_iban_no.attr('name', 'iban_no[' + innerdivcount + ']');
                $('#bankInfo').stop().animate({
                    scrollTop: $('#bankInfo')[0].scrollHeight
                }, 2000);
                $('#banking_' + banking_div + ' .select2').select2({});


                $('#banking_' + banking_div + ' a[data-action="close"]').on('click',
            function() {
                    $(this).closest('.card').remove();
                    $('#bankInfo').perfectScrollbar('update');
                });
                banks_count++;
            });
        });


        var count = parseInt('{{ $i }}');
        $('body').on('click', '#addMoreAddress', function() {
            $.get('new/address', function(data) {
                $('#newAddress').append(data);

            }).done(function() {
                var cc = $('.card.naddress').length;
                var nid = $('.card.naddress').eq(cc - 1);
                count++;
                nid.attr('id', 'shipping_' + count);
                $('#shipping_' + count + ' h3.card-title').text('Address ' + count);
                //becasuse count is starting from 0 and 0 index is there by default for following values
                var innerdivcount = count + 1;
                var temp_pickupaddress = $('#shipping_' + count +
                    ' input[name="temp_pickupaddress"]');
                temp_pickupaddress.attr('name', 'pickup_address[' + innerdivcount + ']');
                var temp_shipping_poc = $('#shipping_' + count +
                    ' input[name="temp_shipping_poc"]');
                temp_shipping_poc.attr('name', 'shipping_poc[' + innerdivcount + ']');
                var temp_product_type = $('#shipping_' + count +
                    ' select[name="temp_product_type"]');
                temp_product_type.attr('name', 'product_type[' + innerdivcount + ']');
                var temp_shipping_phone = $('#shipping_' + count +
                    ' input[name="temp_shipping_phone"]');
                temp_shipping_phone.attr('name', 'shipping_phone[' + innerdivcount + ']');
                var temp_pickup_brand_name = $('#shipping_' + count +
                    ' input[name="temp_pickup_brand_name"]');
                temp_pickup_brand_name.attr('name', 'pickup_brand_name[' + innerdivcount + ']');
                var temp_shipping_email = $('#shipping_' + count +
                    ' input[name="temp_shipping_email"]');
                temp_shipping_email.attr('name', 'shipping_email[' + innerdivcount + ']');
                var temp_shipping_city = $('#shipping_' + count +
                    ' select[name="temp_shipping_city"]');
                temp_shipping_city.attr('name', 'shipping_city[' + innerdivcount + ']');

                $('#shipInfo').stop().animate({
                    scrollTop: $('#shipInfo')[0].scrollHeight
                }, 2000);
                $("input[name='shipping_phone[]']").inputmask({
                    'mask': "9999-9999999",
                    'clearIncomplete': true
                });

                $('#shipping_' + count + ' .select2').select2({
                    // dropdownParent:$('#registership')
                });
                $('#shipping_' + count + ' a[data-action="close"]').on('click', function() {
                    //  $(this).closest('.card').removeClass().slideUp('fast'); // comenting this because display none will allow values to be posted
                    $(this).closest('.card').remove();
                    $('#shipInfo').perfectScrollbar('update');

                });


            });


        });
        $('body').on('change', 'input[name="name"]', function() {
            var name = $(this).val();
            var error = 0;
            var err0 = $('span[name=cname]');
            var err = $('span[name=ename]');
            if (name.length < 3) {
                err0.css('display', 'block');
                error = 1;
            } else {
                error = 0;
                err0.css('display', 'none');

            }
            if (error == 0) {
                $.ajax({
                    url: "name/match/{name}",
                    type: 'GET',
                    data: {
                        name: name
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            err.css('display', 'block');

                        } else if (data.status == 1) {
                            err.css('display', 'none');
                        }

                    }
                });
            }

        });

        $('#cycle_of_invoicing').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Cycle Of Invoicing',

        }).bind('change', function() {

            if (this.value == 1) {
                var weekly = [1, 2, 3, 4, 5, 6, 7];
                $('#generation_div').removeClass('d-none');
                $('#generation_date').removeClass('d-none');
                $('#generation_date').addClass('required');
                $('#generation_date').empty().trigger('change');
                $('#generation_date').select2({
                    data: weekly,
                    placeholder: 'Select Date'
                });
            } else if (this.value == 3) {
                var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28
                ];
                $('#generation_div').removeClass('d-none');
                $('#generation_date').removeClass('d-none');
                $('#generation_date').addClass('required');
                $('#generation_date').empty().trigger('change');
                $('#generation_date').select2({
                    data: monthly,
                    placeholder: 'Select Date'
                });
            } else if (this.value == 2 || this.value == 4) {
                $('#generation_div').addClass('d-none');
                $('#generation_date').addClass('d-none');
                $('#generation_date').removeClass('required');
            }

        });


        // $('#shipper_city').on('change',function () {
        //     }).done(function (data) {
        //         if(data.status == 0){
        //             $('#sale_person').empty();
        //
        //             $.each(data.sizes,function (key,value) {
        //                 var type_size = parseInt(type_id+value.id);
        //
        //                 var index = $.inArray(type_size, already_selected_size);
        //
        //                 if(index === -1){
        //         }
        //
        //
        // });

        // $('a[href="#next"]').on('click',function(e){
        //     // $("#registership").validate().element("");
        // });
        // $( 'a[href="#next"]' ).dblclick(function() {
        //     alert( "Handler for .dblclick() called." );
        // });

        // $('body').on('dblclick', 'a[href="#next"]', function(e) {
        //     e.preventDefault();
        //     // alert('You skipped one step');
        //     //$('a[href="#previous"]').trigger('click');
        // });

    });



    var days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    var selectedValues = []; // Create an array to store selected values
    for (var i = 0; i < days.length; i++) {
        var day = days[i];
        var checkboxId = day;
        var labelId = "label_" + day;
        var value = parseInt([i], 10) + 1;

        var checkbox = $("<input>", {
            type: "checkbox",
            id: checkboxId,
            value: value,
        });

        var label = $("<label>", {
            for: checkboxId,
            text: day
        });

        $("#checkboxContainer").append(checkbox);
        $("#checkboxContainer").append(label);
    }


    var numSelected = 1;
    var maxSelections_1 = 1; //Weekly
    var maxSelections_2 = 2; //Twice A Week
    var maxSelections_3 = 3; //Thrice A Week

    function handleCheckboxSelection(numSelectedVar, maxSelectionsVar) {
        return function() {
            var checkbox = $(this);
            var value = checkbox.val()
            if (checkbox.is(':checked')) {
                if (numSelectedVar <= maxSelectionsVar) {
                    numSelectedVar++;
                    selectedValues.push(value);
                    $('#selected_days').val(selectedValues);

                } else {
                    checkbox.prop('checked', false);
                    $('#msg_payment').removeClass("d-none");
                }
            } else {
                numSelectedVar--;
                $('#msg_payment').addClass("d-none");
                var index = selectedValues.indexOf(value);
                if (index !== -1) {
                    selectedValues.splice(index, 1);
                    $('#selected_days').val(selectedValues);

                }
            }
        }
    }

    $('#payment_cycles').on('change', function() {
        $("#checkboxContainer input[type='checkbox']").prop('checked', false);
        $('#msg_payment').addClass("d-none");
        selectedValues = [];
        $('#payment_cycle_msg').text('')
        var id = $(this).val();
        if (id == 4 || id == 5 || id == 2) {
            $("#checkboxContainer").removeClass("d-none");
            $('#fornite').addClass('d-none')
            $('#fornite_2').addClass('d-none');
            $('#monthly').addClass('d-none');
            $('#label').addClass('d-none');
            $('#label_2').addClass('d-none');
            $('#fornite').removeAttr('name');
            $('#monthly').removeAttr('name');
            $('#fornite_2').removeAttr('name');

            if (id == 4) {//Twice A Week
                $("#checkboxContainer input[type='checkbox']").off('click').on('click', handleCheckboxSelection(
                    numSelected, maxSelections_2));
            } else if (id == 5) {//Thrice A Week
                $("#checkboxContainer input[type='checkbox']").off('click').on('click', handleCheckboxSelection(
                    numSelected, maxSelections_3));
            } else if (id == 2) {//Weekly
                $("#checkboxContainer input[type='checkbox']").off('click').on('click', handleCheckboxSelection(
                    numSelected, maxSelections_1));
            }
        } else if (id == 6) {
            $("#checkboxContainer").addClass("d-none");
            $('#fornite').removeClass('d-none')
            $('#label').removeClass('d-none')
            $('#monthly').addClass('d-none');

        } else if (id == 3) {
            $('#selected_days').removeAttr('name');
            $('#monthly').removeClass('d-none')
            $("#checkboxContainer").addClass("d-none");
            $('#fornite').addClass('d-none');
            $('#label').addClass('d-none');
            $('#fornite_2').addClass('d-none')
            $('#label_2').addClass('d-none');

        } else {
            $("#checkboxContainer").addClass("d-none");
            $('#fornite').addClass('d-none');
            $('#fornite_2').addClass('d-none');
            $('#monthly').addClass('d-none');
            $('#label_2').addClass('d-none');
            $('#label').addClass('d-none')

        }
    });
    $('#fornite').on('change', function() {
        var fornite = parseInt($(this).val(), 10);
        var fornite_2 = fornite + 15;
        var value = $(this).val() + ',' + fornite_2;
        var option = $('<option></option>').attr('value', fornite_2).text(fornite_2 + " Days");
        $("#fornite_2").empty().append(option);
        $('#fornite_2').removeClass('d-none');
        $('#label_2').removeClass('d-none');
        $('#fornite_2').removeAttr('name');
        $('#fornite').removeAttr('name');
        $('#msg_payment').addClass("d-none");
        $('#fortnite_val').val(value);
    });

    $('#iban_no').inputmask({
            mask: 'R',
            repeat: 24,
            greedy: false,
            definitions: {
                R: {
                    validator: '[a-zA-Z0-9]',
                },
            },
        });

        $('#iban_no').on('input', function (e) {
            var iban = $(this).val().replace(/\s+/g, '').toUpperCase(); // Remove white spaces and convert to uppercase
            var defaultPrefix = 'PK';

            if (!iban.startsWith(defaultPrefix)) {
                iban = defaultPrefix + iban.substring(defaultPrefix.length);
            }

            if (iban.length > 2 && !iban.startsWith(defaultPrefix)) {
                $(this).val(defaultPrefix + iban.substring(defaultPrefix.length));
            } else {
                $(this).val(iban);
            }

            if (iban.length !== 24 || !iban.startsWith(defaultPrefix)) {
                $('#iban_no_error').show();
            } else {
                $('#iban_no_error').hide();
            }
        });

        $('body').on('change', '#rate_remarks', function () {
                $(this).val($(this).val().trim());
            });
            $('#on_dws_weight').select2({
                placeholder: "Select Weight Type",
                width: '100%'
            });
            $('#ol_dws_weight').select2({
                placeholder: "Select Weight Type",
                width: '100%'
            });
            $('#detain_dws_weight').select2({
                placeholder: "Select Weight Type",
                width: '100%'
            });
            $('#sameday_dws_weight').select2({
                placeholder: "Select Weight Type",
                width: '100%'
            });
            $("#on_dws").on('change', function () {
                if ($("#on_dws").is(":checked")) {
                    $('#on_dws_weight').attr('disabled', false);
                } else {
                    $('#on_dws_weight').attr('disabled', true);
                }
            });
            $("#ol_dws").on('change', function () {
                if ($("#ol_dws").is(":checked")) {
                    $('#ol_dws_weight').attr('disabled', false);
                } else {
                    $('#ol_dws_weight').attr('disabled', true);
                }
            });

            $("#detain_dws").on('change', function () {
                if ($("#detain_dws").is(":checked")) {
                    $('#detain_dws_weight').attr('disabled', false);
                } else {
                    $('#detain_dws_weight').attr('disabled', true);
                }
            });

            $("#sameday_dws").on('change', function () {
                if ($("#sameday_dws").is(":checked")) {
                    $('#sameday_dws_weight').attr('disabled', false);
                } else {
                    $('#sameday_dws_weight').attr('disabled', true);
                }
            });
            $("#on_default").on('change', function () {
                if ($("#ol_default").is(":checked")) {
                    $("#ol_default").trigger('click');
                }
                if ($("#det_default").is(":checked")) {
                    $("#det_default").trigger('click');
                }
                if ($("#sameday_default").is(":checked")) {
                    $("#sameday_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $("#ol_default").on('change', function () {
                if ($("#on_default").is(":checked")) {
                    $("#on_default").trigger('click');
                }
                if ($("#det_default").is(":checked")) {
                    $("#det_default").trigger('click');
                }
                if ($("#sameday_default").is(":checked")) {
                    $("#sameday_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $("#det_default").on('change', function () {
                if ($("#on_default").is(":checked")) {
                    $("#on_default").trigger('click');
                }
                if ($("#ol_default").is(":checked")) {
                    $("#ol_default").trigger('click');
                }
                if ($("#sameday_default").is(":checked")) {
                    $("#sameday_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $("#sameday_default").on('change', function () {
                if ($("#on_default").is(":checked")) {
                    $("#on_default").trigger('click');
                }
                if ($("#ol_default").is(":checked")) {
                    $("#ol_default").trigger('click');
                }
                if ($("#det_default").is(":checked")) {
                    $("#det_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $(".touchspin-color").trigger("touchspin.updatesettings", {min: 0.5, step: 0.5, decimals: 2});

            // var on_main_switch = document.querySelector('#on_main_switch');
            $('#on_main_switch').on('change', function () {

                var onmainswitch = document.querySelector('.switchery.on-main-switch');
                if (onmainswitch.checked === true) {
                    $('#overnight').slideDown('slow');

                } else if (onmainswitch.checked === false) {
                    $('#overnight').slideUp('slow');


                }
            });

            $('#ol_main_switch').on('change', function () {

                var olmainswitch = document.querySelector('.switchery.ol-main-switch');
                if (olmainswitch.checked === true) {
                    $('#overland').slideDown('slow');

                } else if (olmainswitch.checked === false) {
                    $('#overland').slideUp('slow');


                }
            });
            $('#detain_main_switch').on('change', function () {
                var detainmainswitch = document.querySelector('.switchery.detain-main-switch');
                if (detainmainswitch.checked === true) {
                    $('#detain').slideDown('slow');

                } else if (detainmainswitch.checked === false) {
                    $('#detain').slideUp('slow');


                }
            });
            $('#sameday_main_switch').on('change', function () {
                var samedaymainswitch = document.querySelector('.switchery.sameday-main-switch');
                if (samedaymainswitch.checked === true) {
                    $('#sameday').slideDown('slow');

                } else if (samedaymainswitch.checked === false) {
                    $('#sameday').slideUp('slow');


                }
            });


        

        $('.decimal').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 100000
        }).prop('readOnly', true);
        $('.amount').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000000.00
        }).prop('readOnly', true);

        $('.percent').inputmask({
            'alias': 'numeric',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 500
        }).prop('readOnly', true);
        $('.numeric').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 1000000
        }).prop('readOnly', true);
        $('.dec-percent').inputmask("Regex", {
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
        }).prop('readOnly', true);
        // $('.decpercent').inputmask({
        //     regex: '^\\d{1,9}(\\.\\d{1,2})?%?$',
        // });

        //Inputmask({ regex: "\\d{1,9}(\\.\\d{1,2})?%?$" }).mask('.decpercent');
        $(".daterange").daterangepicker();

        //Overnight
        // var clickCheckbox = Array.prototype.slice.call(document.querySelector('.switchery.weightAdditionOvernight'));
        //var clickCheckbox = document.querySelector('.switchery.weightAdditionOvernight');
        var cashhandlingswitch = document.querySelector('.switchery.cashChargesOvernight');
        var insuranceChargesSwitch = document.querySelector('.switchery.insuranceChargesOvernight');
        var returnChargesSwitch = document.querySelector('.switchery.returnChargesOvernight');
        var fuelChargesSwitch = document.querySelector('.switchery.fuelSurchargeOvernight');
        var packagingChargesSwitch = document.querySelector('.switchery.packagingChargesSwitch');


        if (insuranceChargesSwitch.checked === false) {

            $('.insurance-charges-div-overnight').find('input').prop('disabled', true);
            $('.insurance-charges-btn-overnight').find('button').prop('disabled', true);

        }


        $('.weightAdditionOvernight').on('change', function () {
            var wid = $(this).attr('id');
            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });


        function masks() {

            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 100000
            });
            $('.amount').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });
            $('.dec-percent').inputmask("Regex", {
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
            });
        }

        $('body').on('click', '.on_weight_close', function () {
            $(this).parent().parent().remove();
        });

        // Variables For Discount Weight Charges
        var on_discount_weight = 0;
        var on_discount_weight_charges = [];
        var ol_discount_weight = 0;
        var ol_discount_weight_charges = [];
        var d_discount_weight = 0;
        var d_discount_weight_charges = [];
        var sd_discount_weight = 0;
        var sd_discount_weight_charges = [];

        // Switches For Discount Weight Charges
        var on_discount_weight_switch = document.querySelector('#on_discount_destination_wise_weight_switch');
        var ol_discount_weight_switch = document.querySelector('#ol_discount_destination_wise_weight_switch');
        var d_discount_weight_switch = document.querySelector('#d_discount_destination_wise_weight_switch');
        var sd_discount_weight_switch = document.querySelector('#sd_discount_destination_wise_weight_switch');

        //Activate Discount Functions
        on_discount_weight_switch.onchange = function () {
            if (on_discount_weight_switch.checked === true) {
                $("#discount_on_weight_destination_row").removeClass("d-none");
                $("#discount_on_weight_destination_btn_row").removeClass("d-none");
            } else if (on_discount_weight_switch.checked === false) {
                $("#discount_on_weight_destination_row").addClass("d-none");
                $("#discount_on_weight_destination_btn_row").addClass("d-none");

            }
        };

        ol_discount_weight_switch.onchange = function () {
            if (ol_discount_weight_switch.checked === true) {
                $("#discount_ol_weight_destination_row").removeClass("d-none");
                $("#discount_ol_weight_destination_btn_row").removeClass("d-none");
            } else if (ol_discount_weight_switch.checked === false) {
                $("#discount_ol_weight_destination_row").addClass("d-none");
                $("#discount_ol_weight_destination_btn_row").addClass("d-none");

            }
        };

        d_discount_weight_switch.onchange = function () {
            if (d_discount_weight_switch.checked === true) {
                $("#discount_d_weight_destination_row").removeClass("d-none");
                $("#discount_d_weight_destination_btn_row").removeClass("d-none");
            } else if (d_discount_weight_switch.checked === false) {
                $("#discount_d_weight_destination_row").addClass("d-none");
                $("#discount_d_weight_destination_btn_row").addClass("d-none");

            }
        };

        sd_discount_weight_switch.onchange = function () {
            if (sd_discount_weight_switch.checked === true) {
                $("#discount_sd_weight_destination_row").removeClass("d-none");
                $("#discount_sd_weight_destination_btn_row").removeClass("d-none");
            } else if (sd_discount_weight_switch.checked === false) {
                $("#discount_sd_weight_destination_row").addClass("d-none");
                $("#discount_sd_weight_destination_btn_row").addClass("d-none");

            }
        };

        // Functions For Discount Weight Charges Start

        //OverNight/Rush Functions
        $('body').on('click', '#discount_waddition_destination_btn', function () {
                on_discount_weight_charges[on_discount_weight] = 1;
                var html = `<div id="discount_on_container_div${on_discount_weight}">
                                <div class="row mt-2">
                                    <div class="col-2">
                                        <h3>Select Destination City</h3>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group card border-success p-2">
                                            <select name="discount_on_destination[${on_discount_weight}]" id="discount_on_destination${on_discount_weight}" class="form-control select2 validated on_unique_destination">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <span class= rounded btn-lg mt-1 mr-1 mb-1 discount_wremove_destination_btn" data-id="${on_discount_weight}"><i class=""></i></span>
                                    </div>
                                </div>
                                <div id="discount_on_weight_container_div${on_discount_weight}">
                                    <div class="row">
                                        <div class="col text-center">
                                            <label class="card-title">Range Up</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Range Down</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Weight Addition</label>
                                        </div>
                                        <div class="col-2 text-center">
                                            <label class="card-title">KG Range</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Charges</label>
                                        </div>
                                        <div class="col-1"></div>
                                    </div>
                                    <div class="row" id="discount_on_weight_row${on_discount_weight}_0">
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_on_range_up${on_discount_weight}_0"
                                                   class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   data-rule-min="0.1"
                                                   data-msg-min="Minimum chargeable weight can not be less than 0.1"
                                                   name="discount_on_wa_range_up[${on_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_on_range_down${on_discount_weight}_0"
                                                       class="form-control decimal validated"
                                                       data-rule-required="true"
                                                       data-msg-required="This field is required"
                                                       value=""
                                                       name="discount_on_wa_range_down[${on_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <div class="form-group " style="padding-top: 8px;">
                                                <input type="checkbox" id="discount_OvernightSwitch${on_discount_weight}_0"
                                                       class="switchery discountweightAdditionOvernight validated"
                                                       data-color="success" data-size="sm"
                                                       name="discount_on_wa_switch[${on_discount_weight}][0]">
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">
                                            <fieldset style="padding-top: 5px;">
                                                <div class="input-group input-group-sm form-group">
                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                        id="discount_on_wa_spkg${on_discount_weight}_0"
                                                        data-bts-button-down-class="btn btn-success validated"
                                                        data-bts-button-up-class="btn btn-success"
                                                        name="discount_on_wa_spkg[${on_discount_weight}][0]"
                                                        data-rule-required="true"
                                                        data-msg-required="This field is required"
                                                        value="0.5"
                                                        disabled>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   name="discount_on_wa_local_charges[${on_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-success mr-1 discount_waddition_btn"
                                            title="Add more slabs" data-id="${on_discount_weight}"><i class="la la-plus"></i>
                                    </button>
                                </div>
                            </div>`;
                $("#discount_on_weight_destination_row").append(html);

                $("#discount_on_destination"+on_discount_weight).prepend('<option selected="" value=""></option>').select2({
                    width: '100%',
                    placeholder: "Select Destination",
                });

                var switches = document.querySelector('#discount_OvernightSwitch'+on_discount_weight+'_0');
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
                $("#discount_on_wa_spkg"+on_discount_weight+"_0").TouchSpin({
                    min: 0.5,
                    max: 100,
                    step: 0.5,
                    decimals: 2,
                    buttondown_class: "btn btn-success",
                    buttonup_class: "btn btn-success",
                    buttondown_txt: '<i class="ft-minus"></i>',
                    buttonup_txt: '<i class="ft-plus"></i>'
                });

                masks();

                switches.onchange = function () {

                    if (switches.checked === true) {
                        $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                    } else if (switches.checked === false) {
                        $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                    }
                };
                $("#discount_on_container_div" + on_discount_weight + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                on_discount_weight++;
        });

        $("body").on('click','.discount_wremove_destination_btn',function(){
            var id = $(this).attr('data-id');
            $("#discount_on_container_div"+id).remove();
        });

        $('body').on('click', '.discount_waddition_btn', function () {
            var parent_count = $(this).attr('data-id');
            var current_top_id = $(this).parent().prev().find('.row').last().attr('id').split(parent_count+"_")[1];
            var child_count = on_discount_weight_charges[parent_count];
            on_discount_weight_charges[parent_count] += 1;
            var on_range_down = parseFloat($('#discount_on_range_down' + parent_count+'_'+current_top_id).val());
            var on_new_range_down = on_range_down + 0.01;
            let html = `<div class="row" id="discount_on_weight_row${parent_count}_${child_count}">
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_on_range_up${parent_count}_${child_count}"
                                       class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value="${on_new_range_down}"
                                       name="discount_on_wa_range_up[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_on_range_down${parent_count}_${child_count}"
                                           class="form-control decimal validated"
                                           data-rule-required="true"
                                           data-msg-required="This field is required"
                                           value=""
                                           name="discount_on_wa_range_down[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <div class="form-group " style="padding-top: 8px;">
                                    <input type="checkbox" id="discount_OvernightSwitch${parent_count}_${child_count}"
                                           class="switchery discountweightAdditionOvernight validated"
                                           data-color="success" data-size="sm"
                                           name="discount_on_wa_switch[${parent_count}][${child_count}]">
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <fieldset style="padding-top: 5px;">
                                    <div class="input-group input-group-sm form-group">
                                        <input type="text" class="touchspin-color input-sm spkg"
                                            id="discount_on_wa_spkg${parent_count}_${child_count}"
                                            data-bts-button-down-class="btn btn-success validated"
                                            data-bts-button-up-class="btn btn-success"
                                            name="discount_on_wa_spkg[${parent_count}][${child_count}]"
                                            data-rule-required="true"
                                            data-msg-required="This field is required"
                                            value="0.5"
                                            disabled>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value=""
                                       name="discount_on_wa_local_charges[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col-1">
                                <span  class= rounded btn-sm-width mr-1 mb-1 discount_on_weight_close" data-id="${child_count}" data-parent_id="${parent_count}"><i class=""></i></span>
                            </div>
                        </div>`;
            $('#discount_on_weight_container_div'+parent_count).append(html);
            var switches = document.querySelector('#discount_OvernightSwitch'+parent_count+'_'+child_count);
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_on_wa_spkg"+parent_count+"_"+child_count).TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();
            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_on_weight_row" + parent_count + "_"+child_count+" .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
        });

        $('body').on('click','.discount_on_weight_close',function(){
            var parent_count = $(this).attr('data-parent_id');
            var child_count = $(this).attr('data-id');
            $("#discount_on_weight_row"+parent_count+"_"+child_count).remove();
        });

        //Overland/Saver Plus Functions
        $('body').on('click', '#discount_ol_waddition_destination_btn', function () {
            ol_discount_weight_charges[ol_discount_weight] = 1;
            var html = `<div id="discount_ol_container_div${ol_discount_weight}">
                                <div class="row mt-2">
                                    <div class="col-2">
                                        <h3>Select Destination City</h3>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group card border-success p-2">
                                            <select name="discount_ol_destination[${ol_discount_weight}]" id="discount_ol_destination${ol_discount_weight}" class="form-control select2 validated ol_unique_destination">
                                                @foreach($cities as $city)
            <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
            </select>
        </div>
    </div>
    <div class="col-1">
        <span class= rounded btn-lg mt-1 mr-1 mb-1 discount_ol_wremove_destination_btn" data-id="${ol_discount_weight}"><i class=""></i></span>
                                    </div>
                                </div>
                                <div id="discount_ol_weight_container_div${ol_discount_weight}">
                                    <div class="row">
                                        <div class="col text-center">
                                            <label class="card-title">Range Up</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Range Down</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Weight Addition</label>
                                        </div>
                                        <div class="col-2 text-center">
                                            <label class="card-title">KG Range</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Charges</label>
                                        </div>
                                        <div class="col-1"></div>
                                    </div>
                                    <div class="row" id="discount_ol_weight_row${ol_discount_weight}_0">
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_ol_range_up${ol_discount_weight}_0"
                                                   class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   data-rule-min="0.1"
                                                   data-msg-min="Minimum chargeable weight can not be less than 0.1"
                                                   name="discount_ol_wa_range_up[${ol_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_ol_range_down${ol_discount_weight}_0"
                                                       class="form-control decimal validated"
                                                       data-rule-required="true"
                                                       data-msg-required="This field is required"
                                                       value=""
                                                       name="discount_ol_wa_range_down[${ol_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <div class="form-group " style="padding-top: 8px;">
                                                <input type="checkbox" id="discount_OverlandSwitch${ol_discount_weight}_0"
                                                       class="switchery discountweightAdditionOverland validated"
                                                       data-color="success" data-size="sm"
                                                       name="discount_ol_wa_switch[${ol_discount_weight}][0]">
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">
                                            <fieldset style="padding-top: 5px;">
                                                <div class="input-group input-group-sm form-group">
                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                        id="discount_ol_wa_spkg${ol_discount_weight}_0"
                                                        data-bts-button-down-class="btn btn-success validated"
                                                        data-bts-button-up-class="btn btn-success"
                                                        name="discount_ol_wa_spkg[${ol_discount_weight}][0]"
                                                        data-rule-required="true"
                                                        data-msg-required="This field is required"
                                                        value="0.5"
                                                        disabled>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   name="discount_ol_wa_local_charges[${ol_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-success mr-1 discount_ol_waddition_btn"
                                            title="Add more slabs" data-id="${ol_discount_weight}"><i class="la la-plus"></i>
                                    </button>
                                </div>
                            </div>`;
            $("#discount_ol_weight_destination_row").append(html);

            $("#discount_ol_destination"+ol_discount_weight).prepend('<option selected="" value=""></option>').select2({
                width: '100%',
                placeholder: "Select Destination",
            });

            var switches = document.querySelector('#discount_OverlandSwitch'+ol_discount_weight+'_0');
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_ol_wa_spkg"+ol_discount_weight+"_0").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_ol_container_div" + ol_discount_weight + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            ol_discount_weight++;
        });

        $("body").on('click','.discount_ol_wremove_destination_btn',function(){
            var id = $(this).attr('data-id');
            $("#discount_ol_container_div"+id).remove();
        });

        $('body').on('click', '.discount_ol_waddition_btn', function () {
            var parent_count = $(this).attr('data-id');
            var current_top_id = $(this).parent().prev().find('.row').last().attr('id').split(parent_count+"_")[1];
            var child_count = ol_discount_weight_charges[parent_count];
            ol_discount_weight_charges[parent_count] += 1;
            var ol_range_down = parseFloat($('#discount_ol_range_down' + parent_count+'_'+current_top_id).val());
            var ol_new_range_down = ol_range_down + 0.01;
            let html = `<div class="row" id="discount_ol_weight_row${parent_count}_${child_count}">
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_ol_range_up${parent_count}_${child_count}"
                                       class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value="${ol_new_range_down}"
                                       name="discount_ol_wa_range_up[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_ol_range_down${parent_count}_${child_count}"
                                           class="form-control decimal validated"
                                           data-rule-required="true"
                                           data-msg-required="This field is required"
                                           value=""
                                           name="discount_ol_wa_range_down[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <div class="form-group " style="padding-top: 8px;">
                                    <input type="checkbox" id="discount_OverlandSwitch${parent_count}_${child_count}"
                                           class="switchery discountweightAdditionOverland validated"
                                           data-color="success" data-size="sm"
                                           name="discount_ol_wa_switch[${parent_count}][${child_count}]">
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <fieldset style="padding-top: 5px;">
                                    <div class="input-group input-group-sm form-group">
                                        <input type="text" class="touchspin-color input-sm spkg"
                                            id="discount_ol_wa_spkg${parent_count}_${child_count}"
                                            data-bts-button-down-class="btn btn-success validated"
                                            data-bts-button-up-class="btn btn-success"
                                            name="discount_ol_wa_spkg[${parent_count}][${child_count}]"
                                            data-rule-required="true"
                                            data-msg-required="This field is required"
                                            value="0.5"
                                            disabled>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value=""
                                       name="discount_ol_wa_local_charges[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col-1">
                                <span  class= rounded btn-sm-width mr-1 mb-1 discount_ol_weight_close" data-id="${child_count}" data-parent_id="${parent_count}"><i class=""></i></span>
                            </div>
                        </div>`;
            $('#discount_ol_weight_container_div'+parent_count).append(html);
            var switches = document.querySelector('#discount_OverlandSwitch'+parent_count+'_'+child_count);
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_ol_wa_spkg"+parent_count+"_"+child_count).TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();
            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_ol_weight_row" + parent_count + "_"+child_count+" .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
        });

        $('body').on('click','.discount_ol_weight_close',function(){
            var parent_count = $(this).attr('data-parent_id');
            var child_count = $(this).attr('data-id');
            $("#discount_ol_weight_row"+parent_count+"_"+child_count).remove();
        });

        //Detain/Swift Function
        $('body').on('click', '#discount_d_waddition_destination_btn', function () {
            d_discount_weight_charges[d_discount_weight] = 1;
            var html = `<div id="discount_d_container_div${d_discount_weight}">
                                <div class="row mt-2">
                                    <div class="col-2">
                                        <h3>Select Destination City</h3>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group card border-success p-2">
                                            <select name="discount_d_destination[${d_discount_weight}]" id="discount_d_destination${d_discount_weight}" class="form-control select2 validated d_unique_destiantion">
                                                @foreach($cities as $city)
            <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
            </select>
        </div>
    </div>
    <div class="col-1">
        <span class= rounded btn-lg mt-1 mr-1 mb-1 discount_d_wremove_destination_btn" data-id="${d_discount_weight}"><i class=""></i></span>
                                    </div>
                                </div>
                                <div id="discount_d_weight_container_div${d_discount_weight}">
                                    <div class="row">
                                        <div class="col text-center">
                                            <label class="card-title">Range Up</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Range Down</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Weight Addition</label>
                                        </div>
                                        <div class="col-2 text-center">
                                            <label class="card-title">KG Range</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Charges</label>
                                        </div>
                                        <div class="col-1"></div>
                                    </div>
                                    <div class="row" id="discount_d_weight_row${d_discount_weight}_0">
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_d_range_up${d_discount_weight}_0"
                                                   class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   data-rule-min="0.1"
                                                   data-msg-min="Minimum chargeable weight can not be less than 0.1"
                                                   name="discount_d_wa_range_up[${d_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_d_range_down${d_discount_weight}_0"
                                                       class="form-control decimal validated"
                                                       data-rule-required="true"
                                                       data-msg-required="This field is required"
                                                       value=""
                                                       name="discount_d_wa_range_down[${d_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <div class="form-group " style="padding-top: 8px;">
                                                <input type="checkbox" id="discount_DetainSwitch${d_discount_weight}_0"
                                                       class="switchery discountweightAdditionDetain validated"
                                                       data-color="success" data-size="sm"
                                                       name="discount_d_wa_switch[${d_discount_weight}][0]">
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">
                                            <fieldset style="padding-top: 5px;">
                                                <div class="input-group input-group-sm form-group">
                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                        id="discount_d_wa_spkg${d_discount_weight}_0"
                                                        data-bts-button-down-class="btn btn-success validated"
                                                        data-bts-button-up-class="btn btn-success"
                                                        name="discount_d_wa_spkg[${d_discount_weight}][0]"
                                                        data-rule-required="true"
                                                        data-msg-required="This field is required"
                                                        value="0.5"
                                                        disabled>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   name="discount_d_wa_local_charges[${d_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-success mr-1 discount_d_waddition_btn"
                                            title="Add more slabs" data-id="${d_discount_weight}"><i class="la la-plus"></i>
                                    </button>
                                </div>
                            </div>`;
            $("#discount_d_weight_destination_row").append(html);

            $("#discount_d_destination"+d_discount_weight).prepend('<option selected="" value=""></option>').select2({
                width: '100%',
                placeholder: "Select Destination",
            });

            var switches = document.querySelector('#discount_DetainSwitch'+d_discount_weight+'_0');
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_d_wa_spkg"+d_discount_weight+"_0").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_d_container_div" + d_discount_weight + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            d_discount_weight++;
        });

        $("body").on('click','.discount_d_wremove_destination_btn',function(){
            var id = $(this).attr('data-id');
            $("#discount_d_container_div"+id).remove();
        });

        $('body').on('click', '.discount_d_waddition_btn', function () {
            var parent_count = $(this).attr('data-id');
            var current_top_id = $(this).parent().prev().find('.row').last().attr('id').split(parent_count+"_")[1];
            var child_count = d_discount_weight_charges[parent_count];
            d_discount_weight_charges[parent_count] += 1;
            var d_range_down = parseFloat($('#discount_d_range_down' + parent_count+'_'+current_top_id).val());
            var d_new_range_down = d_range_down + 0.01;
            let html = `<div class="row" id="discount_d_weight_row${parent_count}_${child_count}">
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_d_range_up${parent_count}_${child_count}"
                                       class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value="${d_new_range_down}"
                                       name="discount_d_wa_range_up[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_d_range_down${parent_count}_${child_count}"
                                           class="form-control decimal validated"
                                           data-rule-required="true"
                                           data-msg-required="This field is required"
                                           value=""
                                           name="discount_d_wa_range_down[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <div class="form-group " style="padding-top: 8px;">
                                    <input type="checkbox" id="discount_DetainSwitch${parent_count}_${child_count}"
                                           class="switchery discountweightAdditionDetain validated"
                                           data-color="success" data-size="sm"
                                           name="discount_d_wa_switch[${parent_count}][${child_count}]">
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <fieldset style="padding-top: 5px;">
                                    <div class="input-group input-group-sm form-group">
                                        <input type="text" class="touchspin-color input-sm spkg"
                                            id="discount_d_wa_spkg${parent_count}_${child_count}"
                                            data-bts-button-down-class="btn btn-success validated"
                                            data-bts-button-up-class="btn btn-success"
                                            name="discount_d_wa_spkg[${parent_count}][${child_count}]"
                                            data-rule-required="true"
                                            data-msg-required="This field is required"
                                            value="0.5"
                                            disabled>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value=""
                                       name="discount_d_wa_local_charges[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col-1">
                                <span  class= rounded btn-sm-width mr-1 mb-1 discount_d_weight_close" data-id="${child_count}" data-parent_id="${parent_count}"><i class=""></i></span>
                            </div>
                        </div>`;
            $('#discount_d_weight_container_div'+parent_count).append(html);
            var switches = document.querySelector('#discount_DetainSwitch'+parent_count+'_'+child_count);
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_d_wa_spkg"+parent_count+"_"+child_count).TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();
            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_d_weight_row" + parent_count + "_"+child_count+" .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
        });

        $('body').on('click','.discount_d_weight_close',function(){
            var parent_count = $(this).attr('data-parent_id');
            var child_count = $(this).attr('data-id');
            $("#discount_d_weight_row"+parent_count+"_"+child_count).remove();
        });

        //SameDay Function
        $('body').on('click', '#discount_sd_waddition_destination_btn', function () {
            sd_discount_weight_charges[sd_discount_weight] = 1;
            var html = `<div id="discount_sd_container_div${sd_discount_weight}">
                                <div class="row mt-2">
                                    <div class="col-2">
                                        <h3>Select Destination City</h3>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group card border-success p-2">
                                            <select name="discount_sd_destination[${sd_discount_weight}]" id="discount_sd_destination${sd_discount_weight}" class="form-control select2 validated sd_unique_destination">
                                                @foreach($cities as $city)
            <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
            </select>
        </div>
    </div>
    <div class="col-1">
        <span class= rounded btn-lg mt-1 mr-1 mb-1 discount_sd_wremove_destination_btn" data-id="${sd_discount_weight}"><i class=""></i></span>
                                    </div>
                                </div>
                                <div id="discount_sd_weight_container_div${sd_discount_weight}">
                                    <div class="row">
                                        <div class="col text-center">
                                            <label class="card-title">Range Up</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Range Down</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Weight Addition</label>
                                        </div>
                                        <div class="col-2 text-center">
                                            <label class="card-title">KG Range</label>
                                        </div>
                                        <div class="col text-center">
                                            <label class="card-title">Charges</label>
                                        </div>
                                        <div class="col-1"></div>
                                    </div>
                                    <div class="row" id="discount_sd_weight_row${sd_discount_weight}_0">
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_sd_range_up${sd_discount_weight}_0"
                                                   class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   data-rule-min="0.1"
                                                   data-msg-min="Minimum chargeable weight can not be less than 0.1"
                                                   name="discount_sd_wa_range_up[${sd_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" id="discount_sd_range_down${sd_discount_weight}_0"
                                                       class="form-control decimal validated"
                                                       data-rule-required="true"
                                                       data-msg-required="This field is required"
                                                       value=""
                                                       name="discount_sd_wa_range_down[${sd_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <div class="form-group " style="padding-top: 8px;">
                                                <input type="checkbox" id="discount_SamedaySwitch${sd_discount_weight}_0"
                                                       class="switchery discountweightAdditionSameday validated"
                                                       data-color="success" data-size="sm"
                                                       name="discount_sd_wa_switch[${sd_discount_weight}][0]">
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">
                                            <fieldset style="padding-top: 5px;">
                                                <div class="input-group input-group-sm form-group">
                                                    <input type="text" class="touchspin-color input-sm spkg"
                                                        id="discount_sd_wa_spkg${sd_discount_weight}_0"
                                                        data-bts-button-down-class="btn btn-success validated"
                                                        data-bts-button-up-class="btn btn-success"
                                                        name="discount_sd_wa_spkg[${sd_discount_weight}][0]"
                                                        data-rule-required="true"
                                                        data-msg-required="This field is required"
                                                        value="0.5"
                                                        disabled>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col text-center">
                                            <fieldset class="form-group">
                                                <input type="text" class="form-control decimal validated"
                                                   data-rule-required="true"
                                                   data-msg-required="This field is required"
                                                   value=""
                                                   name="discount_sd_wa_local_charges[${sd_discount_weight}][0]">
                                            </fieldset>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-success mr-1 discount_sd_waddition_btn"
                                            title="Add more slabs" data-id="${sd_discount_weight}"><i class="la la-plus"></i>
                                    </button>
                                </div>
                            </div>`;
            $("#discount_sd_weight_destination_row").append(html);

            $("#discount_sd_destination"+sd_discount_weight).prepend('<option selected="" value=""></option>').select2({
                width: '100%',
                placeholder: "Select Destination",
            });

            var switches = document.querySelector('#discount_SamedaySwitch'+sd_discount_weight+'_0');
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_sd_wa_spkg"+sd_discount_weight+"_0").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_sd_container_div" + sd_discount_weight + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            sd_discount_weight++;
        });

        $("body").on('click','.discount_sd_wremove_destination_btn',function(){
            var id = $(this).attr('data-id');
            $("#discount_sd_container_div"+id).remove();
        });

        $('body').on('click', '.discount_sd_waddition_btn', function () {
            var parent_count = $(this).attr('data-id');
            var current_top_id = $(this).parent().prev().find('.row').last().attr('id').split(parent_count+"_")[1];
            var child_count = sd_discount_weight_charges[parent_count];
            sd_discount_weight_charges[parent_count] += 1;
            var sd_range_down = parseFloat($('#discount_sd_range_down' + parent_count+'_'+current_top_id).val());
            var sd_new_range_down = sd_range_down + 0.01;
            let html = `<div class="row" id="discount_sd_weight_row${parent_count}_${child_count}">
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_sd_range_up${parent_count}_${child_count}"
                                       class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value="${sd_new_range_down}"
                                       name="discount_sd_wa_range_up[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" id="discount_sd_range_down${parent_count}_${child_count}"
                                           class="form-control decimal validated"
                                           data-rule-required="true"
                                           data-msg-required="This field is required"
                                           value=""
                                           name="discount_sd_wa_range_down[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <div class="form-group " style="padding-top: 8px;">
                                    <input type="checkbox" id="discount_SamedaySwitch${parent_count}_${child_count}"
                                           class="switchery discountweightAdditionSameday validated"
                                           data-color="success" data-size="sm"
                                           name="discount_sd_wa_switch[${parent_count}][${child_count}]">
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <fieldset style="padding-top: 5px;">
                                    <div class="input-group input-group-sm form-group">
                                        <input type="text" class="touchspin-color input-sm spkg"
                                            id="discount_sd_wa_spkg${parent_count}_${child_count}"
                                            data-bts-button-down-class="btn btn-success validated"
                                            data-bts-button-up-class="btn btn-success"
                                            name="discount_sd_wa_spkg[${parent_count}][${child_count}]"
                                            data-rule-required="true"
                                            data-msg-required="This field is required"
                                            value="0.5"
                                            disabled>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col text-center">
                                <fieldset class="form-group">
                                    <input type="text" class="form-control decimal validated"
                                       data-rule-required="true"
                                       data-msg-required="This field is required"
                                       value=""
                                       name="discount_sd_wa_local_charges[${parent_count}][${child_count}]">
                                </fieldset>
                            </div>
                            <div class="col-1">
                                <span  class= rounded btn-sm-width mr-1 mb-1 discount_sd_weight_close" data-id="${child_count}" data-parent_id="${parent_count}"><i class=""></i></span>
                            </div>
                        </div>`;
            $('#discount_sd_weight_container_div'+parent_count).append(html);
            var switches = document.querySelector('#discount_SamedaySwitch'+parent_count+'_'+child_count);
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $("#discount_sd_wa_spkg"+parent_count+"_"+child_count).TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();
            switches.onchange = function () {

                if (switches.checked === true) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            };
            $("#discount_sd_weight_row" + parent_count + "_"+child_count+" .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
        });

        $('body').on('click','.discount_sd_weight_close',function(){
            var parent_count = $(this).attr('data-parent_id');
            var child_count = $(this).attr('data-id');
            $("#discount_sd_weight_row"+parent_count+"_"+child_count).remove();
        });

        // Functions For Discount Weight Charges End

        var count = 3;
        $('body').on('click', '#waddition_btn', function () {
            var new_count = count - 1;
            var row_count = new_count + 1;
            var on_range_down = parseFloat($('#on_range_down' + new_count).val());
            var on_new_range_down = on_range_down + 0.01;
            console.log(new_count, row_count, on_range_down, on_new_range_down);
            let htmdiv = '<div class="row" id="on_weight_row' + count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="on_range_up' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="' + on_new_range_down + '" name="on_wa_range_up[' + count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="on_range_down' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_down[' + count + ']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOvernight' + count + '" data-color="success" data-size="sm" name="on_wa_switch[' + count + ']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="on_wa_spkg[' + count + ']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_local_charges[' + count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_0_charges[' + count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_1_charges[' + count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_2_charges[' + count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_3_charges[' + count + ']"></fieldset></div><div class="col-1">\n' +
                '<span  class= rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class=""></i></span></div></div>';
            $('.weight-addition-overnight').append(htmdiv);
            var switches = document.querySelector('.switchery.weightAdditionOvernight' + count);
            var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            switches.onchange = function () {

                if (switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#on_weight_row" + count + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            count++;
        });
        // var switchery = new Switchery('.switchery.weightAddition'+count, { color: '#37BC9B' });


        //addMoreSlabs
        var on_slab_count = 5;
        $('body').on('click', '#addMoreSlabs', function () {
            let htmdiv = '<div class="row" id="on_insurance_handle_' + on_slab_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_cash_range_up[' + on_slab_count + ']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_cash_range_down[' + on_slab_count + ']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_cash_charges[' + on_slab_count + ']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated"></fieldset></div><div class="col">\n' +
                '<span class= rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class=""></i></span></div></div>';
            $('.cash-handling-div-overnight').append(htmdiv);
            masks();
            $("#on_insurance_handle_" + on_slab_count + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();
            on_slab_count++;
        });
        //add more slabs insurance
        var on_ins_count = 3;
        $('body').on('click', '#addMoreSlabsInsurance', function () {
            let htmdiv = '<div class="row" id="on_insurance_charge_' + on_ins_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_ins_range_up[' + on_ins_count + ']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_ins_range_down[' + on_ins_count + ']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_ins_charges[' + on_ins_count + ']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class= rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class=""></i></span></div></div>';
            $('.insurance-charges-div-overnight').append(htmdiv);
            masks();
            $("#on_insurance_charge_" + on_ins_count + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            on_ins_count++;
        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitch.onchange = function () {
            if (cashhandlingswitch.checked === true) {
                $('.cash-handling-div-overnight').find('input').prop('disabled', false);
                $('.cash-handling-btn-overnight').find('button').prop('disabled', false);
            } else if (cashhandlingswitch.checked === false) {
                $('.cash-handling-div-overnight').find('input').prop('disabled', true);
                $('.cash-handling-btn-overnight').find('button').prop('disabled', true);

            }
        };

        // InsuranceOvernight
        insuranceChargesSwitch.onchange = function () {

            if (insuranceChargesSwitch.checked === true) {

                $('.insurance-charges-div-overnight').find('input').prop('disabled', false);
                $('.insurance-charges-btn-overnight').find('button').prop('disabled', false);
            } else if (insuranceChargesSwitch.checked === false) {

                $('.insurance-charges-div-overnight').find('input').prop('disabled', true);
                $('.insurance-charges-btn-overnight').find('button').prop('disabled', true);

            }
        };
        // Return Overnight
        returnChargesSwitch.onchange = function () {
            if (returnChargesSwitch.checked === true) {
                $('.return-charges-div-overnight').find('input').prop('disabled', false);
            } else if (returnChargesSwitch.checked === false) {
                $('.return-charges-div-overnight').find('input').prop('disabled', true);

            }
        };
        fuelChargesSwitch.onchange = function () {
            if (fuelChargesSwitch.checked === true) {
                $('.fuel-surcharge-div-overnight').find('input').prop('disabled', false);
            } else if (fuelChargesSwitch.checked === false) {
                $('.fuel-surcharge-div-overnight').find('input').prop('disabled', true);

            }
        };
        // Packaging Charges Overnight
        // packagingChargesSwitch.onchange = function () {
        //     if(packagingChargesSwitch.checked === true){
        //         $('#packaging_material_charges_div').slideDown('slow');
        //         $('.packaging-charges-div').find('input').prop('disabled',false);
        //     }else if(packagingChargesSwitch.checked === false){
        //         $('#packaging_material_charges_div').slideUp('slow');
        //         $('.packaging-charges-div').find('input').prop('disabled',true);
        //
        //     }
        // };

        {{--        @if(count($packaging_material_types) > 0)--}}


        {{--            @foreach($packaging_material_types as $index => $type)--}}
        {{--            var PackageSwitch = [];--}}
        {{--            var type_id_{{$index}} = '{{$type->id}}';--}}
        {{--            var type_id = '{{$type->id}}';--}}
        {{--            PackageSwitch[type_id] = document.querySelector('.packaging_type_'+type_id);--}}
        {{--            PackageSwitch[type_id].onchange = function () {--}}

        {{--                    if ($(this).is(':checked') === true) {--}}
        {{--                        $('#package_type_'+type_id_{{$index}}).slideDown('slow');--}}

        {{--                    } else if ($(this).is(':checked') === false) {--}}
        {{--                        $('#package_type_'+type_id_{{$index}}).slideUp('slow');--}}

        {{--                    }--}}
        {{--            };--}}

        {{--            @endforeach--}}

        {{--        @endif--}}


        //Overland
        //var weightAdditionOverland = document.querySelector('.switchery.weightAdditionOverland0');
        var cashhandlingswitchOverland = document.querySelector('.switchery.cashChargesOverland');
        var insuranceChargesSwitchOverland = document.querySelector('.switchery.insuranceChargesoverland');
        var returnChargesSwitchOverland = document.querySelector('.switchery.returnChargesOverland');

        var fuelChargesSwitchOL = document.querySelector('.switchery.fuelSurchargeOverland');

        $('.weightAdditionOverland').on('change', function () {
            var wid = $(this).attr('id');

            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });

        //Overland


        var overland_count = 2;
        $('body').on('click', '#overland_weightadd', function () {
            var new_count = overland_count;
            var row_count = new_count + 1;
            var ol_range_down = parseFloat($('#ol_range_down' + new_count).val());
            var ol_new_range_down = ol_range_down + 0.01;

            let htmdiv1 = '<div class="row" id="ol_weight_row' + overland_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="ol_range_up' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="' + ol_new_range_down + '" name="ol_wa_range_up[' + overland_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="ol_range_down' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_down[' + overland_count + ']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOverland' + overland_count + '" data-color="success" data-size="sm" name="ol_wa_switch[' + overland_count + ']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg[' + overland_count + ']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_local_charges[' + overland_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_0_charges[' + overland_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_1_charges[' + overland_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_2_charges[' + overland_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_3_charges[' + overland_count + ']"></fieldset></div><div class="col-1">\n' +
                '<span class= rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class=""></i></span></div></div>';
            $('.weight-addition-overland').append(htmdiv1);
            var ol_weight_switches = document.querySelector('.switchery.weightAdditionOverland' + overland_count);
            var switchery = new Switchery(ol_weight_switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });
            masks();
            ol_weight_switches.onchange = function () {

                if (ol_weight_switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (ol_weight_switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#ol_weight_row" + overland_count + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            overland_count++;
        });

        //addMoreSlabs
        $('body').on('click', '.ol_weight_close', function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click', '.ol_row_delete', function () {
            $(this).parent().parent().remove();
        });
        var ol_slab_count = 5;
        $('body').on('click', '#overlandaddMoreSlabs', function () {
            let htmdiv = '<div class="row" id="ol_cash_handle_' + ol_slab_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_cash_range_up[' + ol_slab_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_cash_range_down[' + ol_slab_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_cash_charges[' + ol_slab_count + ']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="ol_row_delete rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.cash-handling-div-overland').append(htmdiv);
            ol_slab_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //add more slabs insurance
        var ol_ins_count = 3;
        $('body').on('click', '#oladdMoreSlabsInsurance', function () {
            let htmdiv = '<div class="row" id="ol_insurance_charge_' + ol_ins_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_ins_range_up[' + ol_ins_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_ins_range_down[' + ol_ins_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_ins_charges[' + ol_ins_count + ']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span  class="ol_row_delete rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.insurance-charges-div-overland').append(htmdiv);
            ol_ins_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitchOverland.onchange = function () {
            if (cashhandlingswitchOverland.checked === true) {
                // $('.cash-handling-div').
                $('.cash-handling-div-overland').find('input').prop('disabled', false);
                $('.cash-handling-btn-overland').find('button').prop('disabled', false);
            } else if (cashhandlingswitchOverland.checked === false) {
                $('.cash-handling-div-overland').find('input').prop('disabled', true);
                $('.cash-handling-btn-overland').find('button').prop('disabled', true);

            }
        };

        if (insuranceChargesSwitchOverland.checked === false) {
            $('.insurance-charges-div-overland').find('input').prop('disabled', true);
            $('.insurance-charges-btn-overland').find('button').prop('disabled', true);

        }


        // InsuranceOvernight
        insuranceChargesSwitchOverland.onchange = function () {
            if (insuranceChargesSwitchOverland.checked === true) {
                // $('.cash-handling-div').
                $('.insurance-charges-div-overland').find('input').prop('disabled', false);
                $('.insurance-charges-btn-overland').find('button').prop('disabled', false);
            } else if (insuranceChargesSwitchOverland.checked === false) {
                $('.insurance-charges-div-overland').find('input').prop('disabled', true);
                $('.insurance-charges-btn-overland').find('button').prop('disabled', true);

            }
        };
        // Return Overnight
        returnChargesSwitchOverland.onchange = function () {
            if (returnChargesSwitchOverland.checked === true) {
                $('.return-charges-div-overland').find('input').prop('disabled', false);
            } else if (returnChargesSwitchOverland.checked === false) {
                $('.return-charges-div-overland').find('input').prop('disabled', true);

            }
        };
        fuelChargesSwitchOL.onchange = function () {
            if (fuelChargesSwitchOL.checked === true) {
                $('.fuel-surcharge-div-overland').find('input').prop('disabled', false);
            } else if (fuelChargesSwitchOL.checked === false) {
                $('.fuel-surcharge-div-overland').find('input').prop('disabled', true);

            }
        };

        //overland end
        //detain
        //var weightAdditionDetain = document.querySelector('.switchery.weightAdditionDetain0');
        var cashhandlingswitchDetain = document.querySelector('.switchery.cashChargesDetain');
        var insuranceChargesSwitchDetain = document.querySelector('.switchery.insuranceChargesdetain');
        var returnChargesSwitchDetain = document.querySelector('.switchery.returnChargesDetain');

        var fuelChargesSwitchDetain = document.querySelector('.switchery.fuelSurchargeDetain');

        $('.weightAdditionDetain').on('change', function () {
            var wid = $(this).attr('id');
            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });

        //detain


        var detain_count = 1;
        $('body').on('click', '#detain_weightadd', function () {
            var new_count = detain_count;
            var row_count = new_count + 1;
            var detain_range_down = parseFloat($('#detain_range_down' + new_count).val());
            var detain_new_range_down = detain_range_down + 0.01;

            let htmdiv1 = '<div class="row" id="detain_weight_row' + detain_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="detain_range_up' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="' + detain_new_range_down + '" name="detain_wa_range_up[' + detain_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="detain_range_down' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_range_down[' + detain_count + ']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain' + detain_count + '" data-color="success" data-size="sm" name="detain_wa_switch[' + detain_count + ']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="detain_wa_spkg[' + detain_count + ']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_local_charges[' + detain_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_0_charges[' + detain_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_1_charges[' + detain_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_2_charges[' + detain_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_3_charges[' + detain_count + ']"></fieldset></div><div class="col-1">\n' +
                '<span id="detain_weight_close" class= rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.weight-addition-detain').append(htmdiv1);
            var detain_weight_switches = document.querySelector('.switchery.weightAdditionDetain' + detain_count);
            var switchery = new Switchery(detain_weight_switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });
            masks();
            detain_weight_switches.onchange = function () {

                if (detain_weight_switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (detain_weight_switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#detain_weight_row" + detain_count + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            detain_count++;
        });

        //addMoreSlabs
        $('body').on('click', '.detain_weight_close', function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click', '.detain_row_delete', function () {
            $(this).parent().parent().remove();
        });
        var detain_slab_count = 5;
        $('body').on('click', '#detainaddMoreSlabs', function () {
            let htmdiv = '<div class="row" id="detain_cash_handle_' + detain_slab_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_cash_range_up[' + detain_slab_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_cash_range_down[' + detain_slab_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_cash_charges[' + detain_slab_count + ']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="detain_row_delete rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.cash-handling-div-detain').append(htmdiv);
            detain_slab_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //add more slabs insurance
        var detain_ins_count = 3;
        $('body').on('click', '#detainaddMoreSlabsInsurance', function () {
            let htmdiv = '<div class="row" id="detain_insurance_charge_' + detain_ins_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_ins_range_up[' + detain_ins_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_ins_range_down[' + detain_ins_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_ins_charges[' + detain_ins_count + ']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span  class="detain_row_delete rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.insurance-charges-div-detain').append(htmdiv);
            detain_ins_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitchDetain.onchange = function () {
            if (cashhandlingswitchDetain.checked === true) {
                // $('.cash-handling-div').
                $('.cash-handling-div-detain').find('input').prop('disabled', false);
                $('.cash-handling-btn-detain').find('button').prop('disabled', false);
            } else if (cashhandlingswitchDetain.checked === false) {
                $('.cash-handling-div-detain').find('input').prop('disabled', true);
                $('.cash-handling-btn-detain').find('button').prop('disabled', true);

            }
        };
        if (insuranceChargesSwitchDetain.checked === false) {
            $('.insurance-charges-div-detain').find('input').prop('disabled', true);
            $('.insurance-charges-btn-detain').find('button').prop('disabled', true);

        }

        // InsuranceOvernight
        insuranceChargesSwitchDetain.onchange = function () {
            if (insuranceChargesSwitchDetain.checked === true) {
                // $('.cash-handling-div').
                $('.insurance-charges-div-detain').find('input').prop('disabled', false);
                $('.insurance-charges-btn-detain').find('button').prop('disabled', false);
            } else if (insuranceChargesSwitchDetain.checked === false) {
                $('.insurance-charges-div-detain').find('input').prop('disabled', true);
                $('.insurance-charges-btn-detain').find('button').prop('disabled', true);

            }
        };
        // Return Overnight
        returnChargesSwitchDetain.onchange = function () {
            if (returnChargesSwitchDetain.checked === true) {
                $('.return-charges-div-detain').find('input').prop('disabled', false);
            } else if (returnChargesSwitchDetain.checked === false) {
                $('.return-charges-div-detain').find('input').prop('disabled', true);

            }
        };
        fuelChargesSwitchDetain.onchange = function () {
            if (fuelChargesSwitchDetain.checked === true) {
                $('.fuel-surcharge-div-detain').find('input').prop('disabled', false);
            } else if (fuelChargesSwitchDetain.checked === false) {
                $('.fuel-surcharge-div-detain').find('input').prop('disabled', true);

            }
        };

        //Detain end
        //sameday start
        //var weightAdditionSameday = document.querySelector('.switchery.weightAdditionSameday0');
        var cashhandlingswitchSameday = document.querySelector('.switchery.cashChargesSameday');
        var insuranceChargesSwitchSameday = document.querySelector('.switchery.insuranceChargessameday');
        var returnChargesSwitchSameday = document.querySelector('.switchery.returnChargesSameday');
        var fuelChargesSwitchSameday = document.querySelector('.switchery.fuelSurchargeSameday');

        $('.weightAdditionSameday').on('change', function () {
            var wid = $(this).attr('id');

            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });
        // weightAdditionSameday.onchange = function () {
        //     if (weightAdditionSameday.checked === true) {
        //         // $(this).next('.spkg').attr('disabled','');
        //         // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
        //         $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
        //
        //     } else if (weightAdditionSameday.checked === false) {
        //         $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
        //
        //     }
        // };
        //detain


        var sameday_count = 3;
        $('body').on('click', '#sameday_weightadd', function () {
            var new_count = sameday_count - 1;
            var row_count = new_count + 1;
            var sameday_range_down = parseFloat($('#sameday_range_down' + new_count).val());
            var sameday_new_range_down = sameday_range_down + 0.01;

            let htmdiv1 = '<div class="row" id="sameday_weight_row' + sameday_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="sameday_range_up' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="' + sameday_new_range_down + '" name="sameday_wa_range_up[' + sameday_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="sameday_range_down' + row_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_down[' + sameday_count + ']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain' + sameday_count + '" data-color="success" data-size="sm" name="sameday_wa_switch[' + sameday_count + ']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg[' + sameday_count + ']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_local_charges[' + sameday_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_class_0_charges[' + sameday_count + ']"></fieldset></div><div class="col-1">\n' +
                '<span id="sameday_weight_close" class= rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class=""></i></span></div></div>';
            $('.weight-addition-sameday').append(htmdiv1);
            var sameday_weight_switches = document.querySelector('.switchery.weightAdditionDetain' + sameday_count);
            var switchery = new Switchery(sameday_weight_switches, {disabled: false, color: '#37BC9B', size: 'small'});
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });
            masks();
            sameday_weight_switches.onchange = function () {

                if (sameday_weight_switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (sameday_weight_switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#sameday_weight_row" + sameday_count + " .validated").each(function () {
                $(this).rules("add", {
                    required: true,
                });

            });
            sameday_count++;
        });

        //addMoreSlabs
        $('body').on('click', '.sameday_weight_close', function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click', '.sameday_row_delete', function () {
            $(this).parent().parent().remove();
        });
        var sameday_slab_count = 5;
        $('body').on('click', '#samedayaddMoreSlabs', function () {
            let htmdiv = '<div class="row" id="sameday_cash_handle_' + sameday_slab_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_cash_range_up[' + sameday_slab_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_cash_range_down[' + sameday_slab_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_cash_charges[' + sameday_slab_count + ']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="sameday_row_delete rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.cash-handling-div-sameday').append(htmdiv);
            sameday_slab_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //add more slabs insurance
        var sameday_ins_count = 3;
        $('body').on('click', '#samedayaddMoreSlabsInsurance', function () {
            let htmdiv = '<div class="row" id="sameday_insurance_charge_' + sameday_ins_count + '">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_ins_range_up[' + sameday_ins_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_ins_range_down[' + sameday_ins_count + ']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_ins_charges[' + sameday_ins_count + ']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span  class="sameday_row_delete rounded btn-sm-width mr-1 mb-1"><i class=""></i></span></div></div>';
            $('.insurance-charges-div-sameday').append(htmdiv);
            sameday_ins_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitchSameday.onchange = function () {
            if (cashhandlingswitchSameday.checked === true) {
                // $('.cash-handling-div').
                $('.cash-handling-div-sameday').find('input').prop('disabled', false);
                $('.cash-handling-btn-sameday').find('button').prop('disabled', false);
            } else if (cashhandlingswitchSameday.checked === false) {
                $('.cash-handling-div-sameday').find('input').prop('disabled', true);
                $('.cash-handling-btn-sameday').find('button').prop('disabled', true);

            }
        };
        if (insuranceChargesSwitchSameday.checked === false) {
            $('.insurance-charges-div-sameday').find('input').prop('disabled', true);
            $('.insurance-charges-btn-sameday').find('button').prop('disabled', true);

        }

        // InsuranceOvernight
        insuranceChargesSwitchSameday.onchange = function () {
            if (insuranceChargesSwitchSameday.checked === true) {
                // $('.cash-handling-div').
                $('.insurance-charges-div-sameday').find('input').prop('disabled', false);
                $('.insurance-charges-btn-sameday').find('button').prop('disabled', false);
            } else if (insuranceChargesSwitchSameday.checked === false) {
                $('.insurance-charges-div-sameday').find('input').prop('disabled', true);
                $('.insurance-charges-btn-sameday').find('button').prop('disabled', true);

            }
        };
        // Return Overnight
        returnChargesSwitchSameday.onchange = function () {
            if (returnChargesSwitchSameday.checked === true) {
                $('.return-charges-div-sameday').find('input').prop('disabled', false);
            } else if (returnChargesSwitchSameday.checked === false) {
                $('.return-charges-div-sameday').find('input').prop('disabled', true);

            }
        };
        fuelChargesSwitchSameday.onchange = function () {
            if (fuelChargesSwitchSameday.checked === true) {
                $('.fuel-surcharge-div-sameday').find('input').prop('disabled', false);
            } else if (fuelChargesSwitchSameday.checked === false) {
                $('.fuel-surcharge-div-sameday').find('input').prop('disabled', true);

            }
        };

        //Detain end
        //end sameday
        //for discounts Overnight
        var ondiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOvernight'));


        ondiscountSwitch[0].onchange = function () {
            ONdiscount(ondiscountSwitch[0]);
        };
        ondiscountSwitch[1].onchange = function () {
            ONdiscount(ondiscountSwitch[1]);
        };
        ondiscountSwitch[2].onchange = function () {
            ONdiscount(ondiscountSwitch[2]);
        };
        ondiscountSwitch[3].onchange = function () {
            ONdiscount(ondiscountSwitch[3]);
        };
        // ondiscountSwitch[4].onchange = function () {
        //     ONdiscount(ondiscountSwitch[4]);
        // };
        // $.each(ondiscountSwitch,function () {
        //     console.log('heeee');
        // });
        function ONdiscount(eve) {
            if (eve.checked === true) {

                $(eve).parent().parent().next().prop('disabled', false);
                $('input[name="on_discount_title"]').prop('disabled', false);
                $('input[name="on_daterange"]').prop('disabled', false);

            } else if (eve.checked === false) {
                $(eve).parent().parent().next().prop('disabled', true);

                if (ondiscountSwitch[0].checked === true || ondiscountSwitch[1].checked === true || ondiscountSwitch[2].checked === true || ondiscountSwitch[3].checked === true || ondiscountSwitch[4].checked === true) {
                    $('input[name="on_discount_title"]').prop('disabled', false);
                    $('input[name="on_daterange"]').prop('disabled', false);
                } else {
                    $('input[name="on_discount_title"]').prop('disabled', true);
                    $('input[name="on_daterange"]').prop('disabled', true);
                }

            }
        }

        //for discounts Overland
        var overlandDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOverland'));


        overlandDiscountSwitch[0].onchange = function () {
            OLdiscount(overlandDiscountSwitch[0]);
        };
        overlandDiscountSwitch[1].onchange = function () {
            OLdiscount(overlandDiscountSwitch[1]);
        };
        overlandDiscountSwitch[2].onchange = function () {
            OLdiscount(overlandDiscountSwitch[2]);
        };
        overlandDiscountSwitch[3].onchange = function () {
            OLdiscount(overlandDiscountSwitch[3]);
        };
        // overlandDiscountSwitch[4].onchange = function () {
        //     OLdiscount(overlandDiscountSwitch[4]);
        // };
        function OLdiscount(eveOver) {
            if (eveOver.checked === true) {

                $(eveOver).parent().parent().next().prop('disabled', false);
                $('input[name="ol_discount_title"]').prop('disabled', false);
                $('input[name="ol_daterange"]').prop('disabled', false);

            } else if (eveOver.checked === false) {
                $(eveOver).parent().parent().next().prop('disabled', true);

                if (overlandDiscountSwitch[0].checked === true || overlandDiscountSwitch[1].checked === true || overlandDiscountSwitch[2].checked === true || overlandDiscountSwitch[3].checked === true /*|| overlandDiscountSwitch[4].checked === true*/) {
                    $('input[name="ol_discount_title"]').prop('disabled', false);
                    $('input[name="ol_daterange"]').prop('disabled', false);
                } else {
                    $('input[name="ol_discount_title"]').prop('disabled', true);
                    $('input[name="ol_daterange"]').prop('disabled', true);
                }

            }
        }

        //for discounts Overland
        var detainDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesDetain'));


        detainDiscountSwitch[0].onchange = function () {
            Detaindiscount(detainDiscountSwitch[0]);
        };
        detainDiscountSwitch[1].onchange = function () {
            Detaindiscount(detainDiscountSwitch[1]);
        };
        detainDiscountSwitch[2].onchange = function () {
            Detaindiscount(detainDiscountSwitch[2]);
        };
        detainDiscountSwitch[3].onchange = function () {
            Detaindiscount(detainDiscountSwitch[3]);
        };
        // detainDiscountSwitch[4].onchange = function () {
        //     Detaindiscount(detainDiscountSwitch[4]);
        // };
        function Detaindiscount(eveDet) {
            if (eveDet.checked === true) {

                $(eveDet).parent().parent().next().prop('disabled', false);
                $('input[name="detain_discount_title"]').prop('disabled', false);
                $('input[name="detain_daterange"]').prop('disabled', false);

            } else if (eveDet.checked === false) {
                $(eveDet).parent().parent().next().prop('disabled', true);

                if (detainDiscountSwitch[0].checked === true || detainDiscountSwitch[1].checked === true || detainDiscountSwitch[2].checked === true || detainDiscountSwitch[3].checked === true /*|| detainDiscountSwitch[4].checked === true*/) {
                    $('input[name="detain_discount_title"]').prop('disabled', false);
                    $('input[name="detain_daterange"]').prop('disabled', false);
                } else {
                    $('input[name="detain_discount_title"]').prop('disabled', true);
                    $('input[name="detain_daterange"]').prop('disabled', true);
                }

            }
        }

        //for discounts Overland
        var samedayDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesSameday'));


        samedayDiscountSwitch[0].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[0]);
        };
        samedayDiscountSwitch[1].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[1]);
        };
        samedayDiscountSwitch[2].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[2]);
        };
        samedayDiscountSwitch[3].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[3]);
        };
        // samedayDiscountSwitch[4].onchange = function () {
        //     SamedayDiscount(samedayDiscountSwitch[4]);
        // };
        function SamedayDiscount(eveSameday) {
            if (eveSameday.checked === true) {

                $(eveSameday).parent().parent().next().prop('disabled', false);
                $('input[name="sameday_discount_title"]').prop('disabled', false);
                $('input[name="sameday_daterange"]').prop('disabled', false);

            } else if (eveSameday.checked === false) {
                $(eveSameday).parent().parent().next().prop('disabled', true);

                if (samedayDiscountSwitch[0].checked === true || samedayDiscountSwitch[1].checked === true || samedayDiscountSwitch[2].checked === true || samedayDiscountSwitch[3].checked === true /*|| samedayDiscountSwitch[4].checked === true*/) {
                    $('input[name="sameday_discount_title"]').prop('disabled', false);
                    $('input[name="sameday_daterange"]').prop('disabled', false);
                } else {
                    $('input[name="sameday_discount_title"]').prop('disabled', true);
                    $('input[name="sameday_daterange"]').prop('disabled', true);
                }

            }
        }


        //Warehousing

        $('#warehouse_main_switch').on('change', function () {
            var warehousemainswitch = document.querySelector('.switchery.warehouse-main-switch');
            if (warehousemainswitch.checked === true) {
                $('#warehousing').slideDown('slow');

            } else if (warehousemainswitch.checked === false) {
                $('#warehousing').slideUp('slow');


            }
        });
        var weekly = [1, 2, 3, 4, 5, 6, 7];
        var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];

        $('#invoicing_cycle_select').prepend('<option value="" selected></option>').select2({
            placeholder: "Select Invoicing Cycle",
            width: '100%'
        });
        var current_selection = null;
        var storage_type_selected = [];
        var storage_type_data = @json($storage_types);

        var storage_data = $.map(storage_type_data, function (obj) {
            obj.id = obj.id;
            obj.text = obj.name;
            return obj;
        });


        $('select[name="storage_type[0]"]').prepend('<option value="" selected="selected"></option>').select2({
            data: storage_data,
            width: '100%',
            placeholder: 'Select Storage Type'
        }).bind('select2:select', function () {

            $(this).parents('div.storage_type_row').find('span#storage_type_add').removeClass('d-none');
            $('input[name="storage_type[0]"]').val($(this).val());


        });

        var PPCSwitch = document.querySelector('.switchery.PPCSwitch');
        PPCSwitch.onchange = function () {
            if (PPCSwitch.checked === true) {
                $('input[name="ppc_charges"]').prop('disabled', false);
            } else if (PPCSwitch.checked === false) {
                $('input[name="ppc_charges"]').prop('disabled', true);
            }
        };
        var PSFSwitch = document.querySelector('.switchery.PSFSwitch');
        PSFSwitch.onchange = function () {
            if (PSFSwitch.checked === true) {
                $('input[name="psf_charges"]').prop('disabled', false);
            } else if (PSFSwitch.checked === false) {
                $('input[name="psf_charges"]').prop('disabled', true);
            }
        };

        var StorageSwitch = document.querySelector('.switchery.storageCharges');
        StorageSwitch.onchange = function () {
            if (StorageSwitch.checked === true) {
                $('#wms_storage_types_div select, #wms_storage_types_div input').prop('disabled', false);
            } else if (StorageSwitch.checked === false) {
                $('#wms_storage_types_div select, #wms_storage_types_div input').prop('disabled', true);
            }
        };

        var LabellingSwitch = document.querySelector('.switchery.labellingSwitch');
        LabellingSwitch.onchange = function () {
            if (LabellingSwitch.checked === true) {
                $('input[name="labelling_charges"]').prop('disabled', false);
            } else if (LabellingSwitch.checked === false) {
                $('input[name="labelling_charges"]').prop('disabled', true);
            }
        };


        var storage_type_rows = 1;

        $('body').on('click', '#storage_type_add', function () {
            var previous_row = storage_type_rows - 1;

            $(this).addClass('d-none');
            var previous_select = $('select[name="storage_type[' + previous_row + ']"]');
            storage_type_selected.push(previous_select.val());
            previous_select.prop('disabled', true);
            var storage_data_new = $.map(storage_type_data, function (obj) {
                var current_id = obj.id.toString();
                var index = $.inArray(current_id, storage_type_selected);

                if (index === -1) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                }

            });
            if (storage_data_new.length !== 0) {
                var htmldiv = '<div class="row storage_type_row" id="storage_type_row' + storage_type_rows + '">\n' +
                    '                                                <input id="storage_type_input' + storage_type_rows + '" type="hidden" name="storage_type[' + storage_type_rows + ']" value=""><div class="col-md-2 st_select">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <select class="select2 form-control storage_type" name="storage_type[' + storage_type_rows + ']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="storage_type_charges[' + storage_type_rows + ']" type="text" class="form-control validated" data-rule-required="true" data-msg-required="Charges are required" placeholder="Charges">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '<div class="col"><span id="storage_type_add" class="btn btn-sm btn-outline-primary d-none"><i class="la la-check"></i></span>\n' +
                    '<span row="' + storage_type_rows + '" class="storage_type_row_delete btn btn-sm btn-outline-danger"><i class="la la-trash"></i></span></div>';

                $('#wms_storage_types_div').append(htmldiv);


                var last_id = storage_type_rows;
                $('select[name="storage_type[' + storage_type_rows + ']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: storage_data_new,
                    width: '100%',
                    placeholder: 'Select Storage Type'
                }).on('change', function () {
                    var selected_id = $(this).val();
                    $(this).parents('div.storage_type_row').find('span#storage_type_add').removeClass('d-none');
                    $(this).parents('div.storage_type_row').find('input#storage_type_input' + last_id).val(selected_id);
                });
                $('input[name="storage_type_charges[' + storage_type_rows + ']"]').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'min': 0,
                    'max': 1000000
                });

                storage_type_rows++;
            }


        });

        $('body').on('click', 'span.storage_type_row_delete', function () {
            var row_id = $(this).attr('row');
            var row_selector = $('#storage_type_row' + row_id);
            var selected = $('input[name="storage_type[' + row_id + ']"]').val();
            if (selected !== '') {
                var index = $.inArray(selected, storage_type_selected);
                if (index !== -1) {
                    storage_type_selected.splice(index, 1);
                }
            }
            row_selector.remove();
        });

        var packing_sizes = @json($packaging_material_type_sizes);
        var packing_type_rows = 1;
        var packing_type_selected = [];
        var packing_size_selected = [];
        var packing_material_data = @json($packaging_material_types);
        var packing_data = $.map(packing_material_data, function (obj) {
            obj.id = obj.id;
            obj.text = obj.type;
            return obj;
        });

        $('select[name="packing_type[0]"]').prepend('<option value="" selected="selected"></option>').select2({
            data: packing_data,
            width: '100%',
            placeholder: 'Select Packing Type'
        }).bind('select2:select', function () {
            var packing_id = $(this).val();
            $('input[name="packing_type[0]"]').val(packing_id);

            var packing_sizes_data = $.map(packing_sizes[packing_id], function (obj) {
                obj.id = obj.id;
                obj.text = obj.size;
                return obj;
            });
            $('select[name="packing_size[0]"]').empty().select2({
                data: packing_sizes_data,
                placeholder: 'Select Packing Size'
            }).val(null).trigger('change');
        });


        $('select[name="packing_size[0]"]').select2({
            width: '100%',
            placeholder: 'Select Packing Size'
        }).bind('select2:select', function () {
            var packing_size = $(this).val();
            $('input[name="packing_size[0]"]').val(packing_size);
            $(this).parents('div.packing_type_row').find('span#packing_type_add').removeClass('d-none');

        });


        var packingSwitch = document.querySelector('.switchery.packingCharges');
        packingSwitch.onchange = function () {
            if (packingSwitch.checked === true) {
                $('#wms_packing_charges_div input, #wms_packing_charges_div select').prop('disabled', false);
            } else if (packingSwitch.checked === false) {
                $('#wms_packing_charges_div input, #wms_packing_charges_div select').prop('disabled', true);
            }
        };

        var packing_data = $.map(packing_material_data, function (obj) {
            obj.id = obj.id;
            obj.text = obj.type;
            return obj;
        });

        $('body').on('click', '#packing_type_add', function () {
            var previous_row = packing_type_rows - 1;
            $(this).addClass('d-none');

            var previous_type = $('select[name="packing_type[' + previous_row + ']"]');
            var previous_size = $('select[name="packing_size[' + previous_row + ']"]');
            // packing_type_selected.push(previous_type.val());
            packing_size_selected.push(previous_size.val());
            previous_type.prop('disabled', true);
            previous_size.prop('disabled', true);

            // var packing_size_data_new = $.map(packing_material_data, function (obj) {
            //     var current_id = obj.id.toString();
            //     var index = $.inArray(current_id, packing_type_selected);
            //     if(index === -1){
            //         obj.id = obj.id;
            //         obj.text = obj.type;
            //         return obj;
            //     }  
            // });

            if (packing_data.length !== 0) {
                var htmldiv = '<div class="row packing_type_row" id="packing_type_row' + packing_type_rows + '">\n' +
                    '                                                <input id="packing_type_input' + packing_type_rows + '" type="hidden" name="packing_type[' + packing_type_rows + ']" value=""><div class="col-md-2">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <select class="select2 form-control storage_type" name="packing_type[' + packing_type_rows + ']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <input id="packing_size_input' + packing_type_rows + '" type="hidden" name="packing_size[' + packing_type_rows + ']" value=""><div class="col-md-2">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <select class="select2 form-control storage_type" name="packing_size[' + packing_type_rows + ']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                    '                                                    </fieldset></div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="packing_charges[' + packing_type_rows + ']" type="text" class="form-control validated" data-rule-required="true" data-msg-required="Charges are required" placeholder="Charges">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '<div class="col"><span id="packing_type_add" class="btn btn-sm btn-outline-primary d-none"><i class="la la-check"></i></span>\n' +
                    '<span row="' + packing_type_rows + '" class="packing_type_row_delete btn btn-sm btn-outline-danger"><i class="la la-trash"></i></span></div></div>';

                $('#wms_packing_charges_div').append(htmldiv);


                var last_id = packing_type_rows;
                $('select[name="packing_type[' + packing_type_rows + ']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: packing_data,
                    width: '100%',
                    placeholder: 'Select Packing Type'
                }).on('change', function () {
                    var packing_id = $(this).val();
                    $(this).parents('div.packing_type_row').find('input#packing_type_input' + last_id).val(packing_id);
                    var packing_sizes_data = $.map(packing_sizes[packing_id], function (obj) {
                        var current_id = obj.id.toString();
                        var index = $.inArray(current_id, packing_size_selected);
                        if (index === -1) {
                            obj.id = obj.id;
                            obj.text = obj.size;
                            return obj;
                        }
                    });
                    $('select[name="packing_size[' + last_id + ']"]').empty().select2({
                        data: packing_sizes_data,
                        placeholder: 'Select Storage Size'
                    }).val(null).trigger('change');
                });
                $('select[name="packing_size[' + packing_type_rows + ']"]').select2({
                    width: '100%',
                    placeholder: 'Select Packing Type'
                }).on('change', function () {
                    var packing_size = $(this).val();
                    $(this).parents('div.packing_type_row').find('input#packing_size_input' + last_id).val(packing_size);
                    $(this).parents('div.packing_type_row').find('span#packing_type_add').removeClass('d-none');
                });
                $('input[name="packing_charges[' + packing_type_rows + ']"]').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                    'max': 1000000.00
                });

                packing_type_rows++;
            }


        });

        $('body').on('click', 'span.packing_type_row_delete', function () {
            var row_id = $(this).parent().parent().attr('row');
            var size_selected = $('select[name="packing_size[' + row_id + ']"]').val();
            if (size_selected !== '') {
                var index = $.inArray(size_selected, packing_size_selected);
                if (index !== -1) {
                    packing_size_selected.splice(index, 1);
                }
            }
            $(this).parent().parent().remove();
        })


        $('#ratesAdditionForm').on('keypress', function (e) {
            if (e.which == 13 || e.keyCode == 13) {
                e.preventDefault();
            }
        });


        //Origin And Destination Hubs Start
        $('#on_origin_hubs').select2({
            width: '100%',
            placeholder: "Origin(s)",
            allowClear: true
        });

        $('#on_destination_hubs').select2({
            width: '100%',
            placeholder: "Destination(s)",
            allowClear: true
        });

        $('#ol_origin_hubs').select2({
            width: '100%',
            placeholder: "Origin(s)",
            allowClear: true
        });

        $('#ol_destination_hubs').select2({
            width: '100%',
            placeholder: "Destination(s)",
            allowClear: true
        });

        $('#detain_origin_hubs').select2({
            width: '100%',
            placeholder: "Origin(s)",
            allowClear: true
        });

        $('#detain_destination_hubs').select2({
            width: '100%',
            placeholder: "Destination(s)",
            allowClear: true
        });

        $('#sameday_origin_hubs').select2({
            width: '100%',
            placeholder: "Origin(s)",
            allowClear: true
        });

        $('#sameday_destination_hubs').select2({
            width: '100%',
            placeholder: "Destination(s)",
            allowClear: true
        });


        //Origin And Destination Hubs End


        var overnightSwitch = document.querySelector('.switchery.on-main-switch');
        var overlandSwitch = document.querySelector('.switchery.ol-main-switch');
        var detainSwitch = document.querySelector('.switchery.detain-main-switch');
        var samedaySwitch = document.querySelector('.switchery.sameday-main-switch');


        $('#addRatesSubmit').on('click', function () {
            $('#add_user_commission_form').remove();
        });
        $("#ratesAdditionForm").validate({
            errorClass: "danger",
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function (form) {
                if (overnightSwitch.checked == true || overlandSwitch.checked == true || detainSwitch.checked == true || samedaySwitch.checked == true) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    $('#add_user_commission_form').remove();
                    swal({
                        title: 'Please Wait!',
                        text: 'Your rates are being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                } else {
                    swal({
                        title: 'No Shipping Mode Selected!',
                        text: 'At-least one shipping mode is required!',
                        icon: 'warning'
                    });

                }
            }
        });

        $.validator.addMethod("ol_unique_destination", function (value, element) {
            var parentForm = $(element).closest('form');
            var timeRepeated = 0;
            if (value != '') {
                $(parentForm.find('.ol_unique_destination')).each(function () {
                    if ($(this).val() === value && value != 0) {
                        timeRepeated++;
                    }
                });
            }
            return timeRepeated === 1 || timeRepeated === 0;

        }, "Destination Can Not Be Duplicate");

        $.validator.addMethod("on_unique_destination", function (value, element) {
            var parentForm = $(element).closest('form');
            var timeRepeated = 0;
            if (value != '') {
                $(parentForm.find('.on_unique_destination')).each(function () {
                    if ($(this).val() === value && value != 0) {
                        timeRepeated++;
                    }
                });
            }
            return timeRepeated === 1 || timeRepeated === 0;

        }, "Destination Can Not Be Duplicate");

        $.validator.addMethod("d_unique_destination", function (value, element) {
            var parentForm = $(element).closest('form');
            var timeRepeated = 0;
            if (value != '') {
                $(parentForm.find('.d_unique_destination')).each(function () {
                    if ($(this).val() === value && value != 0) {
                        timeRepeated++;
                    }
                });
            }
            return timeRepeated === 1 || timeRepeated === 0;

        }, "Destination Can Not Be Duplicate");

        $.validator.addMethod("sd_unique_destination", function (value, element) {
            var parentForm = $(element).closest('form');
            var timeRepeated = 0;
            if (value != '') {
                $(parentForm.find('.sd_unique_destination')).each(function () {
                    if ($(this).val() === value && value != 0) {
                        timeRepeated++;
                    }
                });
            }
            return timeRepeated === 1 || timeRepeated === 0;

        }, "Destination Can Not Be Duplicate");

        

</script>
@endsection