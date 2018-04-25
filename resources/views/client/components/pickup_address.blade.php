<hr>
<div class="card naddress">
    <div class="card-header">
        <h3 class="card-title">New Address</h3>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content">
        <div class="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pickup_address">
                            Pickup Address :
                            <span class="danger">*</span>
                        </label>
                        <input type="text" class="form-control required" value="{{ old('pickup_address[]') }}" name="pickup_address[]">
                    </div>
                    <div class="form-group">
                        <label for="shipping_poc">
                            Person Of Contact :
                            <span class="danger">*</span>
                        </label>
                        <input type="text" class="form-control required" value="{{ old('shipping_poc[]') }}"  name="shipping_poc[]">
                    </div>
                    <div class="form-group">

                        <label for="url">Product Type :
                            <span class="danger">*</span>
                        </label>
                        <div>
                            <select name="product_type[]" class="select2 form-control required" style="width: 100%">
                                <option value="{{ old('product_type[]') }}" selected>Select Product Type</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->product_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shipping_phone">
                            Phone Number :
                            <span class="danger">*</span>
                        </label>
                        <input type="tel" class="form-control required" placeholder="(0345) 999-9999" value="{{ old('shipping_phone[]') }}" name="shipping_phone[]">
                    </div>
                    <div class="form-group">
                        <label for="shipping_email">Email :<span class="danger">*</span></label>
                        <input type="email" name="shipping_email[]" class="form-control required" value="{{ old('shipping_email[]') }}">
                    </div>
                    <div class="form-group">

                        <label for="shipping_city">Shipper City :
                            <span class="danger">*</span>
                        </label>
                        <div>
                            <select name="shipping_city[]" class="select2 form-control required" style="width: 100%">
                                <option value="{{ old('shipping_city[]') }}" selected>Select Shipper City</option>
                                @foreach($cities as $city)
                                    <option value="{{$city->city_code}}">{{$city->city_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>{{--row end--}}
        </div>{{--card body end--}}
    </div>
</div>