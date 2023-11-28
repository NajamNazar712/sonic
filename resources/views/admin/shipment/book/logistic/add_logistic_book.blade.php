@extends('admin.layout.master')

@section('title', 'Logistic Booking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">Logistic Booking</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('admin.shipment.book.international_store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="selected_service_type" id="selected_service_type" value="">

                                    <div class="row">
                                        <div class="col-md-12"><hr style="background-color: black;"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8" >
                                            <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Consignment #:</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Staff #</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Pickup Date</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Product</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder="">
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Service</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Customer</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Origin</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Destination</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Total Pieces</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Weight Bkg (KG)</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Weight Act (KG)</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Volumetric Weight (KG)</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group"> <h4 style="color: black"><b>Shipper Info</b></h4></div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Shipper Phone</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Shipper Name</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Shipper Address</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Zip/Postal Code</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group"> <h4 style="color: black"><b>Consignee Info</b></h4></div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Consignee Phone</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Consignee Name</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Consignee Address</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Zip/Postal Code</label>
                                                                    <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Pay Mode</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4    ">
                                                        <div class="form-group">
                                                            <label>Light/Heavy</label>
                                                            <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Handling Instructions</label>
                                                                <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                            </div>
                                                </div>
                                                <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Item Specification</label>
                                                                <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                            </div>
                                                </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group"> <h4 style="color: black"><b>Product Item Specification</b></h4></div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="item_box" style="border: 1px solid lightgrey; padding:10px;">
                                                        <div class="item_detail">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Product ID</label>
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                        <label>Pieces</label>
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Height (in)</label>
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Width (in)</label>
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Length (in)</label>
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Weight</label>
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                        <label>Action</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                        <button class="btn btn-danger btn-sm"><i class="la la-times m-0"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group" ><button class="btn btn-primary float-right">Add Row</button></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Route</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Ot Service (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Gst (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Decl. Value (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Product Service Charges (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Province Sales Tax (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Insurance (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Handling (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Total (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                   
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Others (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Fuel Charges (Rs.)</label>
                                                        <input type="text" name="" class="form-control " id="" placeholder=""  >
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" >
                                            <div class="row" >
                                                <div class="col-md-12" >
                                                    <div class="label_img">
                                                        <img id="myimage" src="https://s3-alpha-sig.figma.com/img/86e8/adaf/faac25d739496f20479b375198134418?Expires=1702252800&Signature=pJLjhK-vvxibwXmz-9zbvdcROb3MGCm9SCg-R--qEqkvvv2WFaghHEvz3PSg4wlUhuu5zVBmY-vIcoIYJnMhW2YPFA52R6Kj8DQM3AZxcW2aNbR94tgAuyus~7SHuVfzQwPn~3ldo5ruXWJWU1N0VuDQ6~VnQefUD9hjX1n7uW1eDy61D4W~Cc~j0BYYoUsYhJrWXAcHs7NG85hEBemIocGZM4f4eZHhIxqLH7H8SKbndWo-lyDBKit3KRl2g2Bb32YDkj1tw3SgAXwvktY7GTX~XWxrGwgCR~QEpszmLLgiM9tg~cJ0aR~sBofk5iASo0qNhsch8cVRRIy7AkuUOA__&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4" >
                                                        <hr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                            

                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group text-center">
                                            <button type="submit" name="book" id="sub_book" class="btn btn-primary" value="Book">Book</button>
                                            <button type="submit" name="book_and_print" id="sub_book_print" class="btn btn-primary ml-1" value="Book & Print">Book &amp; Print</button>
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <style>

        .label_img {
            max-width: 445px;
            height: 400px;
            background-color: red;
            position: relative;
        }
        .label_img img {
            width: 100%;
            height: 100%;
            /* object-fit: cover; Maintain image aspect ratio and cover the entire container */
        }
        .img-magnifier-glass{
            display: none;
            position: absolute;
            border: 1px solid black;
            /* border-radius: 50%; */
            cursor: none;
            box-shadow: 5px 5px 12px black;
            /*Set the size of the magnifier glass:*/
            width: 250px;
            height: 250px; 
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    
  <script>

    $(document).ready(function(){
        function magnify(imgID, zoom) {
            
            var img, glass, w, h, bw;
            img = document.getElementById(imgID);
           
            /*create magnifier glass:*/
            glass = document.createElement("DIV");
            glass.setAttribute("class", "img-magnifier-glass");
            /*insert magnifier glass:*/
            img.parentElement.insertBefore(glass, img);
            /*set background properties for the magnifier glass:*/
            glass.style.backgroundImage = "url('" + img.src + "')";
            glass.style.backgroundRepeat = "no-repeat";
            glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
            bw = 3;
            w = glass.offsetWidth / 2;
            h = glass.offsetHeight / 2;
            /*execute a function when someone moves the magnifier glass over the image:*/
            glass.addEventListener("mousemove", moveMagnifier);
            img.addEventListener("mousemove", moveMagnifier);
            /*and also for touch screens:*/
            glass.addEventListener("touchmove", moveMagnifier);
            img.addEventListener("touchmove", moveMagnifier);
            function moveMagnifier(e) {
             
                $(".img-magnifier-glass").css('display','block');
                var pos, x, y;
                /*prevent any other actions that may occur when moving over the image*/
                e.preventDefault();
                /*get the cursor's x and y positions:*/
                pos = getCursorPos(e);
                x = pos.x;
                y = pos.y;
                /*prevent the magnifier glass from being positioned outside the image:*/
                if (x > img.width - (w / zoom)) {x = img.width - (w / zoom);}
                if (x < w / zoom) {x = w / zoom;}
                if (y > img.height - (h / zoom)) {y = img.height - (h / zoom);}
                if (y < h / zoom) {y = h / zoom;}
                /*set the position of the magnifier glass:*/
                glass.style.left = (x - w) + "px";
                glass.style.top = (y - h) + "px";
                /*display what the magnifier glass "sees":*/
                glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
            }
            function getCursorPos(e) {
                var a, x = 0, y = 0;
                e = e || window.event;
                /*get the x and y positions of the image:*/
                a = img.getBoundingClientRect();
                /*calculate the cursor's x and y coordinates, relative to the image:*/
                x = e.pageX - a.left;
                y = e.pageY - a.top;
                /*consider any page scrolling:*/
                x = x - window.pageXOffset;
                y = y - window.pageYOffset;
                return {x : x, y : y};
            }
            }

            magnify("myimage", 2.5);

    });

    function hidemagnify(){
        $(".img-magnifier-glass").css('display','none');

    }
  </script>
@endsection