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
                                            <div class="row">
                                                <div class="col-md-12 label_img">
                                                    <img src="https://s3-alpha-sig.figma.com/img/86e8/adaf/faac25d739496f20479b375198134418?Expires=1702252800&Signature=pJLjhK-vvxibwXmz-9zbvdcROb3MGCm9SCg-R--qEqkvvv2WFaghHEvz3PSg4wlUhuu5zVBmY-vIcoIYJnMhW2YPFA52R6Kj8DQM3AZxcW2aNbR94tgAuyus~7SHuVfzQwPn~3ldo5ruXWJWU1N0VuDQ6~VnQefUD9hjX1n7uW1eDy61D4W~Cc~j0BYYoUsYhJrWXAcHs7NG85hEBemIocGZM4f4eZHhIxqLH7H8SKbndWo-lyDBKit3KRl2g2Bb32YDkj1tw3SgAXwvktY7GTX~XWxrGwgCR~QEpszmLLgiM9tg~cJ0aR~sBofk5iASo0qNhsch8cVRRIy7AkuUOA__&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4" >
                                                    <hr>
                                                </div>
                                              <div class="loupe"></div>
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
            height: 500px;
            background-color: red;
            position: relative;
        }

        .label_img img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Maintain image aspect ratio and cover the entire container */
        }

        .loupe {
            display: none;
            position: absolute;
            width: 200px;
            height: 200px;
            border: 1px solid black;
            box-shadow: 5px 5px 12px black;
            background: rgba(0, 0, 0, 0.25);
            cursor: crosshair;
            overflow: hidden;
        }

        .loupe img {
            position: absolute;
            right: 0;
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
   $(document).ready(function () {
    var $loupe = $(".loupe"),
        loupeWidth = $loupe.outerWidth(),
        loupeHeight = $loupe.outerHeight(),
        $img;

    $(document).on("mouseenter", ".label_img", function (e) {
        var $currImage = $(this),
            $img = $("<img/>")
                .attr("src", $("img", this).attr("src"))
                .css({ width: $currImage.outerWidth() * 2, height: $currImage.outerHeight() * 2 });

        $loupe.html($img).fadeIn(100);

        function moveHandler(e) {
            var imageOffset = $currImage.offset(),
                fx = imageOffset.left - loupeWidth / 2,
                fy = imageOffset.top - loupeHeight / 2,
                fh = imageOffset.top + $currImage.outerHeight() + loupeHeight / 2,
                fw = imageOffset.left + $currImage.outerWidth() + loupeWidth / 2;

            $loupe.css({
                left: e.pageX - loupeWidth / 2,
                top: e.pageY - loupeHeight / 2
            });

            var loupeOffset = $loupe.offset(),
                lx = loupeOffset.left,
                ly = loupeOffset.top,
                lw = lx + loupeWidth,
                lh = ly + loupeHeight,
                bigy = (ly - loupeHeight / 4 - fy) * 2,
                bigx = (lx - loupeWidth / 4 - fx) * 2;

            $img.css({ left: -bigx, top: -bigy });

            if (lx < fx || lh > fh || ly < fy || lw > fw) {
                // $loupe.fadeOut(100);
            }
        }

        $(document).on("mousemove", moveHandler);
    });
});

  </script>
@endsection