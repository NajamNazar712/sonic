@extends('admin.layout.master')

@section('title', 'User Profile')

@section('content')

    <h1 class="mb-1">
        User Profile
    </h1>
    <div class="card">
        <div class="card-header">
            {{--<h4 class="card-title">User Profile</h4>--}}
            @include('admin.inc.messages')
        </div>
        <div class="card-content">
            <div id="tabs" class="card-body">
                {{--<p>Use <code>.nav-justified</code> class to set tabs justified.</p>--}}
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" aria-controls="active" aria-expanded="true">Profile Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="link-tab" data-toggle="tab" href="#link" aria-controls="link" aria-expanded="false">Shipping Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="linkOpt-tab" data-toggle="tab" href="#linkOpt" aria-controls="linkOpt">Bank Information</a>
                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                    <div role="tabpanel" class="tab-pane active" id="active" aria-labelledby="active-tab" aria-expanded="true">
                        {{--<p>Macaroon candy canes tootsie roll wafer lemon drops liquorice--}}
                            {{--jelly-o tootsie roll cake. Marzipan liquorice soufflé cotton--}}
                            {{--candy jelly cake jelly-o sugar plum marshmallow. Dessert--}}
                            {{--cotton candy macaroon chocolate sugar plum cake donut.</p>--}}
                        <div class="table-responsive">
                            <br>
                            <table class="table" style="font-size: 14px">
                                <thead>
                                <tr>
                                    {{--<th>Firstname</th>--}}
                                    {{--<th>Lastname</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><b>Company Name</b></td>
                                    <td>{{$user->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Email Address</b></td>
                                    <td>{{$user->email}}</td>
                                </tr>
                                <tr>
                                    <td><b>Address</b></td>
                                    <td>{{$user->address}}</td>
                                </tr>
                                <tr>
                                    <td><b>Person of Contact</b></td>
                                    <td>{{$user->poc}}</td>
                                </tr>
                                <tr>
                                    <td><b>Phone Number 1</b></td>
                                    <td>{{$user->phone}}</td>
                                </tr>
                                <tr>
                                    <td><b>Phone Number 2</b></td>
                                    <td>{{$user->phone2}}</td>
                                </tr>
                                <tr>
                                    <td><b>CNIC Number</b></td>
                                    <td>{{$user->cnic}}</td>
                                </tr>
                                <tr>
                                    <td><b>NTN Number</b></td>
                                    <td>{{$user->ntn_no}}</td>
                                </tr>
                                <tr>
                                    <td><b>URL</b></td>
                                    <td>{{$user->url}}</td>
                                </tr>
                                <tr>
                                    <td><b>Product Type</b></td>
                                    <td>{{$product_name}}</td>
                                </tr>
                                <tr>
                                    <td><b>City</b></td>
                                    <td>{{$user->city->name}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        @if (session('role_id') == 1 || in_array(110, session('permissions')))
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="edit-1" type="button" class="btn btn-primary btn-block">Edit</button>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="tab-pane" id="link" role="tabpanel" aria-labelledby="link-tab" aria-expanded="false">
                        {{--<div class="table-responsive">--}}
                            {{--<table class="table" style="font-size: 14px">--}}
                                {{--<thead>--}}
                                {{--<tr>--}}
                                    {{--<th>ID</th>--}}
                                    {{--<th>Address</th>--}}
                                    {{--<th>POC</th>--}}
                                    {{--<th>Phone</th>--}}
                                    {{--<th>Email</th>--}}
                                    {{--<th>Status</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tbody>--}}
                                {{--@foreach($user->shipping as $pickup)--}}
                                    {{--<tr>--}}
                                        {{--<td>{{$pickup->id}}</td>--}}
                                        {{--<td>{{$pickup->pickup_address}}</td>--}}
                                        {{--<td>{{$pickup->poc}}</td>--}}
                                        {{--<td>{{$pickup->phone}}</td>--}}
                                        {{--<td>{{$pickup->email}}</td>--}}
                                        {{--<td>{{$pickup->default_address==1 ? "Default Address | " : ""}}--}}
                                        {{--{{$pickup->status==1 ? "Enabled" : "Disabled"}}</td>--}}
                                    {{--</tr>--}}
                                {{--@endforeach--}}
                                {{--</tbody>--}}
                            {{--</table>--}}
                        {{--</div>--}}

                        <div class="table-responsive">
                            <br>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">

                                    <th class="border-primary border-darken-1">S.No</th>
                                    <th class="border-primary border-darken-1">Pickup Address ID</th>
                                    <th class="border-primary border-darken-1">Pickup Address</th>
                                    <th class="border-primary border-darken-1">Person of Contact</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Email Address</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                    <div class="tab-pane" id="linkOpt" role="tabpanel" aria-labelledby="linkOpt-tab" aria-expanded="false">
                        <div class="table-responsive">
                            <br>
                            <table class="table" style="font-size: 14px">
                                <thead>
                                <tr>
                                    {{--<th>Firstname</th>--}}
                                    {{--<th>Lastname</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><b>Bank Name</b></td>
                                    <td>{{$user->bank->bank->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Bank Branch</b></td>
                                    <td>{{$user->bank->bank_branch}}</td>
                                </tr>
                                <tr>
                                    <td><b>Account No.</b></td>
                                    <td>{{$user->bank->account_no}}</td>
                                </tr>
                                <tr>
                                    <td><b>Account Title</b></td>
                                    <td>{{$user->bank->account_title}}</td>
                                </tr>
                                <tr>
                                    <td><b>IBAN Number</b></td>
                                    <td>{{$user->bank->iban}}</td>
                                </tr>
                                <tr>
                                    <td><b>Payment Mode</b></td>
                                    <td>{{$user->bank->payment_mode}}</td>
                                </tr>
                                <tr>
                                    <td><b>Payment Cycle</b></td>
                                    <td>{{$user->bank->payment_cycle}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        @if (session('role_id') == 1 || in_array(111, session('permissions')))
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="edit-2" type="button" class="btn btn-primary btn-block">Edit</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                {{--<div class="row justify-content-center">--}}
                    {{--<div class="col-3">--}}
                        {{--<button type="button" class="btn btn-success btn-block">Edit</button>--}}
                    {{--</div>--}}
                {{--</div>--}}

            </div>


            <div class="card-body">
                <div class="card-text">

                </div>
                <form id="profile-form" class="form form-horizontal" style="display: none" method="post" action="{{route('admin.accounts.update.profile')}}">
                    @csrf
                    <div class="form-body">
                        <h4 class="form-section">Profile Info</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput1">Company Name:</label>
                                        <span class="danger">*</span>
                                        <input type="text" minlength="3" id="name" class="form-control border-primary" data-rule-remote="{{ route('cod.check.name', ['id' => $user->id,'name'=>$user->name]) }}" data-msg-remote="Company Name must be unique" data-rule-required="true" data-msg-required="Company Name is required" value="{{$user->name}}" name="name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label  for="userinput2">Person of Contact:</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="poc"  data-rule-required="true" data-msg-required="Person of Contact is required" class="form-control border-primary" value="{{$user->poc}}" name="poc" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput3">Email Address</label>
                                        <span class="danger">*</span>
                                        <input type="email" id="email" class="form-control border-primary" data-rule-remote="{{ route('cod.check.email', ['id' => $user->id,'email'=>$user->email]) }}" data-msg-remote="Email must be unique" data-rule-required="true" data-msg-required="Email address is required" value="{{$user->email}}" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput4">Address:</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="address" class="form-control border-primary" data-rule-required="true" data-msg-required="Address is required" value="{{$user->address}}" name="address" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput3">Phone Number 1</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone number is required" value="{{$user->phone}}" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label for="userinput4">Phone Number 2:</label>
                                        <input type="text" id="phone2" class="form-control border-primary" value="{{$user->phone2}}" name="phone2">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput3">CNIC No.</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="cnic" class="form-control border-primary" data-rule-required="true" data-msg-required="CNIC Number is required" value="{{$user->cnic}}" name="cnic" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label for="userinput4">NTN Number</label>
                                        <input type="text" id="ntn_no" class="form-control border-primary" value="{{$user->ntn_no}}" name="ntn_no">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label for="userinput3">URL</label>
                                        <input type="text" id="userinput9" class="form-control border-primary" value="{{$user->url}}" name="url">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput3">Product Type</label>
                                        <span class="danger">*</span>
                                        <select name="product_id" id="product_id" data-rule-required="true" data-msg-required="Product Type is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($products as $single_product)
                                                <option value="{{$single_product->id}}" {{ $user->product_id == $single_product->id ? 'selected' : '' }} >{{$single_product->product_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <label for="userinput3">Password:</label>
                                        <input type="password" id="userinput9" class="form-control border-primary" value="" name="password">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput3">City</label>
                                        <span class="danger">*</span>
                                        <select name="city_id" id="city_id" data-rule-required="true" data-msg-required="City is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($all_cities as $city)
                                                <option value="{{$city->id}}" {{ $user->city_id == $city->id ? 'selected' : '' }} >{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions right">
                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                             Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>

                <form id="bank-form" class="form form-horizontal" style="display: none" method="post" action="{{route('admin.accounts.update.bank')}}">
                    @csrf
                    <div class="form-body">

                        <h4 class="form-section">Bank Info </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput5">Bank Name</label>
                                        <span class="danger">*</span>
                                        <select name="bank_name" id="bank_name" data-rule-required="true" data-msg-required="Bank name is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($banks as $bank)
                                                <option value="{{$bank->id}}"  {{ $user->bank->bank_name == $bank->id ? 'selected' : '' }} >{{$bank->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput6">Bank Branch</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="bank_branch" class="form-control border-primary" data-rule-required="true" data-msg-required="Bank branch is required" value="{{$user->bank->bank_branch}}" name="bank_branch" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label >Account Number</label>
                                        <span class="danger">*</span>
                                        <input id="account_no" class="form-control border-primary" type="text" value="{{$user->bank->account_no}}" data-rule-required="true" data-msg-required="Account Number is required" name="account_no"  required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label >Payment Cycle</label>
                                        <span class="danger">*</span>
                                        <select name="payment_cycle" id="payment_cycle" data-rule-required="true" data-msg-required="Payment Cycle is required" class="select2 form-control required" style="width: 100%">
                                            <option value="daily" {{ $user->bank->payment_cycle == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="weekly" {{ $user->bank->payment_cycle == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="fortnight" {{ $user->bank->payment_cycle == 'fortnight' ? 'selected' : '' }}>Fortnight</option>
                                            <option value="monthly" {{ $user->bank->payment_cycle == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput8">Account Title</label>
                                        <span class="danger">*</span>
                                        <input id="account_title" class="form-control border-primary" type="text" value="{{$user->bank->account_title}}" data-rule-required="true" data-msg-required="Account Title is required" name="account_title"  required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput8">IBAN Number</label>
                                        <span class="danger">*</span>
                                        <input id="iban" class="form-control border-primary" type="text" value="{{$user->bank->iban}}" data-rule-required="true" data-msg-required="IBAN Number is required" name="iban" required>
                                        <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">
                                        {{--<input type="hidden" name="user_id" value="{{$user->id}}">--}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput8">Bank City</label>
                                        <span class="danger">*</span>
                                        <select name="bank_city" id="bank_city" data-rule-required="true" data-msg-required="Bank City is required" class="select2 form-control required" style="width: 100%">
                                            @foreach($all_cities as $bank_city)
                                                <option value="{{$bank_city->id}}"  {{ $user->bank->city_id == $bank_city->id ? 'selected' : '' }}  >{{$bank_city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="userinput5">Payment Mode</label>
                                        <span class="danger">*</span>
                                        <select name="payment_mode" id="payment_mode" data-rule-required="true" data-msg-required="Payment Mode is required" class="select2 form-control required" style="width: 100%">
                                            <option value="ibft" {{ $user->bank->payment_mode == 'ibft' ? 'selected' : '' }}>IBFT Reimbursements</option>
                                            <option value="invoices" {{ $user->bank->payment_mode == 'invoices' ? 'selected' : '' }}>Invoices</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions right">
                        <button id="cancel-button-bank" type="button" class="btn btn-warning mr-1">
                             Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                             Update
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function() {

            $('#product_id').select2({
                width: '100%',
            });
            $('#city_id').select2({
                width: '100%',
            });
            $('#bank_name').select2({
                width: '100%',
            });
            $('#bank_city').select2({
                width: '100%',
            });
            $('#payment_cycle').select2({
                width: '100%',
            });
            $('#payment_mode').select2({
                width: '100%',
            });
            
            $('#edit-1').click(function () {
                $("#profile-form").show();
                $("#tabs").hide();
            });
            $('#edit-2').click(function () {
                $("#bank-form").show();
                $("#tabs").hide();
            });
            $('#cancel-button-profile').click(function () {
                $("#profile-form").hide();
                $("#profile-form").validate().resetForm();
                $("#profile-form")[0].reset();
                $("#profile-form").find(".danger").removeClass("danger");
                $("#name").val("");
                $("#name").val("{{$user->name}}");
                $("#address").val("");
                $("#address").val("{{$user->address}}");
                $("#email").val("");
                $("#email").val("{{$user->email}}");
                $("#cnic").val("");
                $("#cnic").val("{{$user->cnic}}");
                $("#phone").val("");
                $("#phone").val("{{$user->phone}}");
                $("#phone2").val("");
                $("#phone2").val("{{$user->phone2}}");
                $("#poc").val("");
                $("#poc").val("{{$user->poc}}");
                $("#tabs").show();
            });
            $('#cancel-button-bank').click(function () {
                $("#bank-form").hide();
                $("#bank-form").validate().resetForm();
                $("#bank-form")[0].reset();
                $("#bank-form").find(".danger").removeClass("danger");
                $("#bank_branch").val("");
                $("#bank_branch").val("{{$user->bank->bank_branch}}");
                $("#account_no").val("");
                $("#account_no").val("{{$user->bank->account_no}}");
                $("#account_title").val("");
                $("#account_title").val("{{$user->bank->account_title}}");
                $("#iban").val("");
                $("#iban").val("{{$user->bank->iban}}");
                $("#tabs").show();
            });

            $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
            $("input[name='phone'],input[name='phone2']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $("input[name='ntn_no']").inputmask({'mask': "9999999-9", 'clearIncomplete': true});

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                processing: true,
                serverSide: true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                ajax: '{{route('admin.accounts.get.pickups',['user_id'=>$user->id])}}',
                rowId: 'id',
                order : [1,'desc'],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'id'},
                    {data: 'pickup_address', name: 'pickup_address'},
                    {data: 'poc', name: 'poc'},
                    {data: 'phone', name: 'phone'},
                    {data: 'city_name', name: 'c.name'},
                    {data: 'email', name: 'email'},
                    {data: 'action', name: 'action',class:'action',orderable: false,}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }
            });


            $( "#profile-form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                        form.submit();

                }
            });
            $( "#bank-form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    form.submit();

                }
            });

            $('body').on('change','#profile-form input',function() {
                $(this).val($(this).val().trim());
            });
            $('body').on('change','#bank-form input',function() {
                $(this).val($(this).val().trim());
            });



        });
    </script>
@endsection