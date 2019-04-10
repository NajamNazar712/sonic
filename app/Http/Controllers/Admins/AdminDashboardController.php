<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\AdminLogs;
use App\Http\Models\CityDelivery;
use App\Http\Models\BanksList;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\Rates\HistoryBookingTypeCharges;
use App\Http\Models\Rates\HistoryCashHandlingCharge;
use App\Http\Models\Rates\HistoryDiscountCharge;
use App\Http\Models\Rates\HistoryFuelSurcharge;
use App\Http\Models\Rates\HistoryInsuranceCharge;
use App\Http\Models\Rates\HistoryPackagingCharge;
use App\Http\Models\Rates\HistoryRateStatus;
use App\Http\Models\Rates\HistoryReturnCharge;
use App\Http\Models\Rates\HistoryWeightCharge;
use App\Http\Models\Rates\PendingRateStatus;
use App\Http\Models\Rates\RateHistory;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShipperNotificationEmail;
use App\Http\Models\WalkInCities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\BookingType;
use App\Http\Models\Rates\PendingCashHandlingCharge;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\Admin\Admin;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\PackagingCharge;
use App\Http\Models\Rates\PendingFuelSurcharge;
use App\Http\Models\Rates\PendingInsuranceCharge;
use App\Http\Models\Rates\PendingPackagingCharge;
use App\Http\Models\Product;
use App\Http\Models\Rider;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;
use App\Http\Models\PickupType;
use App\Http\Models\WeightCharge;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\ReturnCharge;
use App\Http\Models\DiscountCharge;
use App\Http\Models\Rates\PendingWeightCharge;
use App\Http\Models\Rates\PendingBookingTypeCharges;
use App\Http\Models\Rates\PendingReturnCharge;
use App\Http\Models\Rates\PendingDiscountCharge;
use App\Http\Models\RateStatus;
use App\Http\Models\ShippingMode;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
class AdminDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        $stats = array();
        $graph = array();
        $graph_dates = array();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        $stats['total'] = Shipment::whereBetween('created_at',[$thirtyDays,$today]);
        $stats['booked'] = Shipment::where('shipper_status_id',1)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['canceled'] = Shipment::where('shipper_status_id',17)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['received'] = Shipment::whereIn('shipper_status_id',[2,3,4])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['delivered'] = Shipment::whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['return'] = Shipment::whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['pending'] = Shipment::whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49])->whereBetween('created_at',[$thirtyDays,$today]);

        if (session('role_id') != 1) {
            $stats['total'] = $stats['total']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['booked'] = $stats['booked']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });$stats['canceled'] = $stats['canceled']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['received'] = $stats['received']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['delivered'] = $stats['delivered']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['return'] = $stats['return']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['pending'] = $stats['pending']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
        }

        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['pending'] = number_format($stats['pending']->count());

        $graph_dates['current'] = Carbon::now();
        $graph_dates['old_date'] = Carbon::now()->subDays(29);
        for ($counter = 29; $counter >= 0; $counter--) {
            $date = Carbon::now()->subDays($counter);
            $comparison_date = $date->toDateString();
            $graph['dates'][] = $date->format('d M');

            $booked = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',1);
            $received = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id',[2,3,4]);
            $cancelled = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',17);
            $delivered = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47]);
            $pending = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19]);
            $return = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46]);

            if (session('role_id') != 1) {
                $booked = $booked->where(function($query) {
                    $query->whereHas('pickup_address.city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    })->orWhereHas('consignee_city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    });
                });

                $received = $received->where(function($query) {
                    $query->whereHas('pickup_address.city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    })->orWhereHas('consignee_city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    });
                });
                $cancelled = $cancelled->where(function($query) {
                    $query->whereHas('pickup_address.city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    })->orWhereHas('consignee_city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    });
                });

                $delivered = $delivered->where(function($query) {
                    $query->whereHas('pickup_address.city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    })->orWhereHas('consignee_city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    });
                });

                $return = $return->where(function($query) {
                    $query->whereHas('pickup_address.city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    })->orWhereHas('consignee_city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    });
                });

                $pending = $pending->where(function($query) {
                    $query->whereHas('pickup_address.city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    })->orWhereHas('consignee_city', function ($sub_query) {
                        $sub_query->whereIn('hub_id', session('hubs'));
                    });
                });
            }

            $graph['booked'][] = $booked->count();
            $graph['received'][] = $received->count();
            $graph['cancelled'][] = $cancelled->count();
            $graph['delivered'][] = $delivered->count();
            $graph['pending'][] = $pending->count();
            $graph['return'][] = $return->count();
        }
        $shippers = User::where('status',3)->where('blacklist',0)->select('id','name')->get();
        $cities = City::where('status',1)->select('id','name')->get();
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();
        // return $cities;
        return view('admin.dashboard')->with(['stats'=>$stats,'graph'=>$graph,'dates'=>$graph_dates,'cities'=>$cities,'shippers'=>$shippers,'shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status]);
    }
    public function statistics_search(Request $request){
//        return $request;
        $graph = array();
        $destination = $request->destination;
        $shipper = $request->shipper;
        $current_date = $request->current_date;
        $old_date = $request->old_date;
        $date = $old_date;
        $dates = array();
        $dates[] = $date;
        while ($date != $current_date) {
            $date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
            $dates[] = $date;
        }

        if(($destination != '') && ($shipper != '')){
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination, 'shipper_status_id' => 1]);
                $received = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination])->whereIn('shipper_status_id', [2, 3, 4]);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination])->where('shipper_status_id', 17);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination])->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
                $pending = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination])->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19]);
                $return = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination])->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46]);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $received = $received->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $return = $return->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $pending = $pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['received'][] = $received->count();
                $graph['canceled'][] = $canceled->count();
                $graph['delivered'][] = $delivered->count();
                $graph['pending'][] = $pending->count();
                $graph['return'][] = $return->count();
            }
        }else if(($destination == '') && ($shipper != '')){
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id',1);
                $received = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [2, 3, 4]);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 17);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
                $pending = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19]);
                $return = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46]);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $received = $received->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $return = $return->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $pending = $pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['received'][] = $received->count();
                $graph['canceled'][] = $canceled->count();
                $graph['delivered'][] = $delivered->count();
                $graph['pending'][] = $pending->count();
                $graph['return'][] = $return->count();
            }
        }else if(($destination != '') && ($shipper == '')){
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination])->where('shipper_status_id',1);
                $received = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination])->whereIn('shipper_status_id', [2, 3, 4]);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination])->where('shipper_status_id', 17);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination])->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
                $pending = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination])->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19]);
                $return = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination])->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46]);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $received = $received->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $return = $return->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $pending = $pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['received'][] = $received->count();
                $graph['canceled'][] = $canceled->count();
                $graph['delivered'][] = $delivered->count();
                $graph['pending'][] = $pending->count();
                $graph['return'][] = $return->count();
            }
        }else{
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',1);
                $received = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [2, 3, 4]);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 17);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
                $pending = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19]);
                $return = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46]);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $received = $received->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $return = $return->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $pending = $pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['received'][] = $received->count();
                $graph['canceled'][] = $received->count();
                $graph['delivered'][] = $delivered->count();
                $graph['pending'][] = $pending->count();
                $graph['return'][] = $return->count();
            }
        }


        return response()->json(['status'=>1,'graph'=>$graph]);
    }
    public function orders_list(Request $request)
    {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->select(['shipments_journey.remarks as cancellation_remarks', 'si.description as product_description','shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.tracking_number as tracking','shipments.order_id','u.id as account_no','u.name as shipper','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','p.product_name as product_type','shipments.created_at as booking_date','shipments.special_instructions as instructions','shipments.shipper_status_id', 'sps.name as payment_status','ssr.name as reason', 'shipments.booking_type_id', 'usi.poc','shipments_journey.shipper_status_id as status_id'])
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))->orWhereIn('dc.hub_id', session('hubs'));
            });
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('account_no', function ($shipment) {
                return str_pad($shipment->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->filterColumn('u.id', function ($query, $keyword) {
                return $query->where('u.id', '=', $keyword);
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('phone',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->editColumn('cancellation_remarks',function ($shipments){
                if($shipments->cancellation_remarks != null && $shipments->status_id == 17){
                    return $shipments->cancellation_remarks;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $keyword = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                            ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                    });
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('bt.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('payment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sps.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('product_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('p.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action',function ($shipments) {
                if ($shipments->shipper_status_id != 17 && $shipments->shipper_status_id > 1) {
                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>
                            </div>
                        </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if ($request->get('booking_from_date') && $request->get('booking_to_date')) {
            $from = $request->get('booking_from_date');
            $to = $request->get('booking_to_date');
            $datatable->whereBetween('shipments.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
    public function ecommerce(){
        return view('admin.ecommerce');
    }
//    public function orders_list(){
//        return view('admin.dashboard');
//    }
    public function orderPending(){
        return view('admin.pending_booked_orders');
    }
    public function pendingAccountsList(){
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('ar.department_id',7)->get();
        $products = Product::select('id','product_name')->get();
        return view('admin.accounts.pending_accounts_list')->with(['products'=>$products,'sale_name'=>$salesperson]);
    }
    public function activeAccountsList(){
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('ar.department_id',7)->get();
        $products = Product::select('id','product_name')->get();
        return view('admin.accounts.active_accounts_list')->with(['products'=>$products,'sale_name'=>$salesperson]);

    }
    public function blockAccountsList(){
        return view('admin.accounts.block_accounts_list');
    }
    public function UserStatus(Request $request){
//        return $request;
        $id = $request->shid; //shipper id
        $status = $request->status;
//        return $request;
        if($status == 'activate'){
            $user = User::find($id);
            if($user->status == 2){
                $now = Carbon::now();
                $action = User::where('id',$id)->update(['status'=>3,'account_activated_by'=>Auth::id(),'activated_at'=>$now]);
                if($action == 1){
                    NotificationsController::send(1, $id);

                    return redirect()->route('admin.accounts.active')->with('success', 'User is activated.');
                }else{
                    return back()->with('danger', 'There is some problem please try again.');
                }
            }else{
                return back()->with('danger', 'This user\'s rates are not set.');
            }
        }

    }
    public function tagSubmit(Request $request){
        $tag_id = $request->admin_id;
        $shipper_id = $request->shipper_id;
        $user = User::find($shipper_id);
        $shipper_hub_id = $user->city->hub_id;
        if(AdminHub::where('admin_id',$tag_id)->where('hub_id',$shipper_hub_id)->exists()){
            if(!SalePersonTag::where(['admin_id'=>$tag_id,'user_id'=>$shipper_id,'status'=>0])->exists()){
                $shipper_data =SalePersonTag::where('user_id',$shipper_id)->where('status',0)->get();
                if($shipper_data->count() > 0){
                    SalePersonTag::where('user_id',$shipper_id)->where('status',0)->update(['status' => 1]);
                }
                $sale_person_tag = new SalePersonTag();
                $sale_person_tag->admin_id=$tag_id;
                $sale_person_tag->user_id=$shipper_id;
                $sale_person_tag->save();
            }
            else{
                return ['status'=>0,'error'=>"Shipper is already assigned to Tagged Sales Person!"];
            }

            return ['status'=>1,'success'=>"Shipper Hub is assigned to Tagged Sales Person!"];
        }
        else{
            return ['status'=>0,'error'=>"Shipper Hub is not assigned to Tagged Sales Person!"];

        }

    }
    public function rejectReasonSubmit(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $reject_reason = $request->rejected_reason;
        User::where('id',$shipper_id)->update(['rejected_reason'=>$reject_reason, 'rate_status'=>2]);
        return ['success' => 'Rates has been rejected!'];
    }
    public function UserStatusBlock(Request $request){
        $user_id = $request->id;
        $reason = $request->reason;
        $status = $request->status;
        $user = User::where('id',$user_id);
        if($user->exists()){
            $user = $user->first();
            if($status == 'block'){
                if($user->blacklist == 0){
                    $user->blacklist = 1;
                    $user->blacklist_reason = $reason;
                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User added to the blacklist!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is already in blacklist!"]);
                }
            }else if($status == 'unblock'){
                if($user->blacklist == 1){
                    $user->blacklist = 0;
                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User removed from the blacklist!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is not in the blacklist!"]);
                }
            }

        }else{
            return response()->json(['status'=>0,'error'=>"User doesn\'t exist!"]);
        }
    }
    public function UserStatusChange(Request $request){
        $user_id = $request->id;
        $status = $request->status;
        $user = User::where('id',$user_id);
        if($user->exists()){
            $user = $user->first();
            if($status == 'enable'){
                if($user->status == 4){
                    $user->status = 3;
                    $user->disable_remarks = null;
                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User is now enabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is already enabled!"]);
                }
            }else if($status == 'disable'){
                if($user->status == 3){
                    $user->status = 4;
                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User is now disabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is already disabled!"]);

                }
            }
        }else{
            return response()->json(['status'=>0,'error'=>"User doesn\'t exist!"]);
        }
    }
    public function get_shipment_charges(Request $request){
        $shipment_id = $request->shipment_id;
        $shipment = Shipment::find($shipment_id);
        $returnHTML = view('admin/components/shipment_charges')->with(['shipment'=>$shipment])->render();
        return response()->json($returnHTML);
    }
    public function shipper_recall(Request $request) {
        $shipment_ids = $request->shipment_ids;

        if (!empty($shipment_ids)) {
            $valid = FALSE;

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && $shipment->shipper_status_id == 2 && !$shipment->packaging_material_request) {
                    $valid = TRUE;

                    $shipment->shipper_status_id = 20;
                    $shipment->consignee_status_id = 20;

                    $shipment->save();

                    ShipmentsJourneyController::add($shipment_id, 50, 50, NULL, NULL, NULL, Auth::id());

                    ShipmentsJourneyController::add($shipment_id, 20, 20, NULL, NULL, NULL, Auth::id());

                    NotificationsController::send(15, 0, $shipment_id);
                    NotificationsController::send(16, 0, $shipment_id);

                    ShipmentChargesController::return($shipment_id);

                    AdminFinanceController::add_payment($shipment_id, 1);
                }
            }

            if ($valid) {
                return ['status' => 0, 'success' => 'Shipment(s) has been marked as Return Confirm due to Shipper Recall'];
            }
            else {
                return ['status' => 1, 'error' => 'No Valid Shipment(s) were Selected'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewBankInfo($id){
        $user = User::find($id);
        $bank = $user->bank;
//        return $bank;
        $returnHTML = view('admin/components/bank')->with(['bank'=>$bank,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShippingInfo($id){
        $user = User::find($id);
        $shipping = $user->shipping()->where('hidden',0)->get();
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShipperRates($id){
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    public function addRatesView($id){
        $user = User::find($id);
        if(!RateStatus::where('user_id', $user->id)->exists()) {
            $weight = StandardWeightCharge::all()->groupBy('shipping_mode_id');
            $bookingType = StandardBookingTypeCharge::all()->groupBy('shipping_mode_id');
            $cash = StandardCashHandlingCharge::all()->groupBy('shipping_mode_id');
            $insurance = StandardInsuranceCharge::all()->groupBy('shipping_mode_id');
            $return = StandardReturnCharge::all()->groupBy('shipping_mode_id');
            $fuel = StandardFuelSurcharge::all()->groupBy('shipping_mode_id');
            $packaging = StandardPackagingCharge::all()->groupBy('shipping_mode_id');
            return view('admin.accounts.add_rates')->with(['shipper' => $user, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging]);
        }
        return redirect()->back()->with('error','User rates not found!');
    }
//    public function salesTag(Request $name)
//    {
//        $salesperson = admins::join('admin_department as ad', 'admins.role_id', '=', 'ad.role_id')
//            ->join('admin_roles as ar', 'ar.department_id', '=', 7)->get();
//    }

    public function viewRates($id){
        $user = User::find($id);
        $switches = RateStatus::all()->where('user_id',$id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
        $weight = WeightCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
//        $cash = '';
        $bookingType = BookingTypeCharges::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $cash = CashHandlingCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $insurance = InsuranceCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $return = ReturnCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $fuel = FuelSurcharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $packaging = PackagingCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $discount = DiscountCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
//        return $discount;
        return view('admin.accounts.view_rates')->with(['shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging,'discountCharges'=>$discount]);

    }


    public function editRatesView($id){
        $user = User::find($id);
        if ((($user['rate_status']>=0) && $user['status']==1)||(($user['rate_status']==0) && $user['status']==3)) {
            $switches = RateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
            $weight = WeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        $cash = '';
            $bookingType = BookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = CashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = InsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = ReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = FuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $packaging = PackagingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = DiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_status = $user['rate_status'];
        }
        elseif(($user['rate_status']>=1) && $user['status']==3){
            $switches = PendingRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
            $weight = PendingWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        $cash = '';
            $bookingType = PendingBookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = PendingCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = PendingInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = PendingReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = PendingFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $packaging = PendingPackagingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = PendingDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_status = $user['rate_status'];
        }
        else {
            return redirect(route('admin.accounts.pending'));
        }
//        return $discount;
        return view('admin.accounts.edit_rates')->with(['shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging,'discountCharges'=>$discount, 'rate_status'=>$rate_status]);

    }

    public function editRates(Request $request, $id){
        $user = User::find($id);
        if ($user['status']!=3) {
            $messages = [
                'on_wa_range_up.*.required' => 'The overnight range up field is required.',
                'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
                'on_wa_range_down.*.required' => 'The overnight range down field is required.',
                'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
                'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
                'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
                'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
                'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
                'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
                'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
                'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
                'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
                'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
                'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
                'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
                //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
                'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
                'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
                'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
                'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
                'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
                //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
                'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
                'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
                'on_return_class_0_charges.*.numeric' => 'The overnight class A return charges field must be numeric.',
                'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
                'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
                'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
                'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
                'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
                'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
                'on_flyer_sm.required_if' => 'The overnight small flyer field is required',
                'on_flyer_sm.numeric' => 'The overnight small flyer field must be numeric',
                'on_flyer_md.required_if' => 'The overnight meduim flyer field is required',
                'on_flyer_md.numeric' => 'The overnight medium flyer field must be numeric',
                'on_flyer_lg.required_if' => 'The overnight large flyer field is required',
                'on_flyer_lg.numeric' => 'The overnight large flyer field must be numeric',
                'on_flyer_box.required_if' => 'The overnight box flyer field is required',
                'on_flyer_box.numeric' => 'The overnight box flyer field must be numeric',
                'on_discount_title.required_if' => 'The overnight discount title field must be required',
                'on_daterange.required_if' => 'The overnight discount date range field must be required',
                'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
//            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
                'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
//            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
                'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
//            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
                'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
//            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
                'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
//            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
                'on_discount_title.required_with' => 'The overnight discount title field is required',
                'on_daterange.required_with' => 'The overnight discount date field is required',
                //overland starts
                'ol_wa_range_up.*.required' => 'The overland range up field is required.',
                'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
                'ol_wa_range_down.*.required' => 'The overland range down field is required.',
                'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
                'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
                'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
                'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
                'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
                'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
                'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
                'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
                'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
                'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
                'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
                'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
                //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
                'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
                'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
                'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
                'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
                'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
                //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
                'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
                'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
                'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
                'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
                'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
                'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
                'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
                'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
                'ol_flyer_sm.required_if' => 'The overland small flyer field is required',
                'ol_flyer_sm.numeric' => 'The overland small flyer field must be numeric',
                'ol_flyer_md.required_if' => 'The overland meduim flyer field is required',
                'ol_flyer_md.numeric' => 'The overland medium flyer field must be numeric',
                'ol_flyer_lg.required_if' => 'The overland large flyer field is required',
                'ol_flyer_lg.numeric' => 'The overland large flyer field must be numeric',
                'ol_flyer_box.required_if' => 'The overland box flyer field is required',
                'ol_flyer_box.numeric' => 'The overland box flyer field must be numeric',
                'ol_discount_title.required_if' => 'The overland discount title field must be required',
                'ol_daterange.required_if' => 'The overland discount date range field must be required',
                'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
//            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
                'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
//            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
                'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
//            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
                'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
//            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
                'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
//            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
                'ol_discount_title.required_with' => 'The overland discount title field is required',
                'ol_daterange.required_with' => 'The overland discount date field is required',
                //overland end and detain starts
                'detain_wa_range_up.*.required' => 'The detain range up field is required.',
                'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
                'detain_wa_range_down.*.required' => 'The detain range down field is required.',
                'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
                'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
                'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
                'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
                'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
                'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
                'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
                'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
                'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
                'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
                'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
                'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
                //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
                'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
                'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
                'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
                'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
                'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
                //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
                'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
                'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
                'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
                'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
                'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
                'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
                'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
                'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
                'detain_flyer_sm.required_if' => 'The detain small flyer field is required',
                'detain_flyer_sm.numeric' => 'The detain small flyer field must be numeric',
                'detain_flyer_md.required_if' => 'The detain meduim flyer field is required',
                'detain_flyer_md.numeric' => 'The detain medium flyer field must be numeric',
                'detain_flyer_lg.required_if' => 'The detain large flyer field is required',
                'detain_flyer_lg.numeric' => 'The detain large flyer field must be numeric',
                'detain_flyer_box.required_if' => 'The detain box flyer field is required',
                'detain_flyer_box.numeric' => 'The detain box flyer field must be numeric',
                'detain_discount_title.required_if' => 'The detain discount title field must be required',
                'detain_daterange.required_if' => 'The detain discount date range field must be required',
                'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
//            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
                'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
//            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
                'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
//            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
                'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
//            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
                'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
//            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
                'detain_discount_title.required_with' => 'The detain discount title field is required',
                'detain_daterange.required_with' => 'The detain discount date field is required',
                //detain ends and sameday starts
                'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
                'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
                'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
                'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
                'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
                'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
                'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
                'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
                'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
                'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
                'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
                'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
                'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
                'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
                'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
                'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
                'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
                'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
                'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
                'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
                // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
                'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
                'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
                'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
                'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
                'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
                //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
                'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
                'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
                'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
                'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
                'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
                'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
                'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
                'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',
                'sameday_flyer_sm.required_if' => 'The sameday small flyer field is required',
                'sameday_flyer_sm.numeric' => 'The sameday small flyer field must be numeric',
                'sameday_flyer_md.required_if' => 'The sameday meduim flyer field is required',
                'sameday_flyer_md.numeric' => 'The sameday medium flyer field must be numeric',
                'sameday_flyer_lg.required_if' => 'The sameday large flyer field is required',
                'sameday_flyer_lg.numeric' => 'The sameday large flyer field must be numeric',
                'sameday_flyer_box.required_if' => 'The sameday box flyer field is required',
                'sameday_flyer_box.numeric' => 'The sameday box flyer field must be numeric',
                'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
                'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
                'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
//            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
                'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
//            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
                'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
//            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
                'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
//            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
                'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
//            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
                'sameday_discount_title.required_with' => 'The sameday discount title field is required',
                'sameday_daterange.required_with' => 'The sameday discount date field is required',
                //sameday ends
            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $on_validations = [
                    'on_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'on_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'on_wa_local_charges.*' => 'required|numeric',
                    'on_class_0_charges.*' => 'required|numeric',
                    'on_class_1_charges.*' => 'required',
                    'on_class_2_charges.*' => 'required',
                    'on_class_3_charges.*' => 'required',
                    'on_wa_spkg.*' => 'numeric',
                    'on_replacement_charges' => 'required|numeric',
                    'on_tnb_charges' => 'required|numeric',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',
                    'on_flyer_sm' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_md' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_lg' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_box' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on'
                ];
            }
            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ol_validations = [
                    'ol_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'ol_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'ol_wa_local_charges.*' => 'required|numeric',
                    'ol_class_0_charges.*' => 'required|numeric',
                    'ol_class_1_charges.*' => 'required',
                    'ol_class_2_charges.*' => 'required',
                    'ol_class_3_charges.*' => 'required',
                    'ol_wa_spkg.*' => 'numeric',
                    'ol_replacement_charges' => 'required|numeric',
                    'ol_tnb_charges' => 'required|numeric',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',
                    'ol_flyer_sm' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_md' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_lg' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_box' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',
                ];
            }
            //overland
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $detain_validations = [
                    'detain_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'detain_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'detain_wa_local_charges.*' => 'required|numeric',
                    'detain_class_0_charges.*' => 'required|numeric',
                    'detain_class_1_charges.*' => 'required',
                    'detain_class_2_charges.*' => 'required',
                    'detain_class_3_charges.*' => 'required',
                    'detain_wa_spkg.*' => 'numeric',
                    'detain_replacement_charges' => 'required|numeric',
                    'detain_tnb_charges' => 'required|numeric',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',
                    'detain_flyer_sm' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_md' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_lg' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_box' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',
                ];
            }
            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $sameday_validations = [
                    'sameday_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'sameday_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'sameday_wa_local_charges.*' => 'required|numeric',
                    'sameday_class_0_charges.*' => 'required|numeric',
                    'sameday_wa_spkg.*' => 'numeric',
                    'sameday_replacement_charges' => 'required|numeric',
                    'sameday_tnb_charges' => 'required|numeric',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                    'sameday_flyer_sm' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_md' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_lg' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_box' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',
                ];
            }

            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }


            if ($request->on_rate_record != null) {
                RateStatus::where('id', $request->on_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => ($request->has('on_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('on_packaging_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'status' => ($request->has('on_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                    'packaging_charges' => ($request->has('on_packaging_switch')) ? 1 : 0
                ]);
            }
            if ($request->ol_rate_record != null) {
                RateStatus::where('id', $request->ol_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('ol_packaging_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                    'packaging_charges' => ($request->has('ol_packaging_switch')) ? 1 : 0
                ]);
            }
            if ($request->det_rate_record != null) {
                RateStatus::where('id', $request->det_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('detain_packaging_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                    'packaging_charges' => ($request->has('detain_packaging_switch')) ? 1 : 0
                ]);
            }
            if ($request->same_rate_record != null) {
                RateStatus::where('id', $request->same_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('sameday_packaging_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                    'packaging_charges' => ($request->has('sameday_packaging_switch')) ? 1 : 0
                ]);
            }


            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->get();

                if (!$ONRateAlready->isEmpty()) {

                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_weight_record)->delete();
                    foreach ($request->on_weight_record as $index => $on_weight_record) {
                        if ($request->has('on_wa_switch')) {
                            if (array_key_exists($index, $request->on_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('on_wa_spkg')) {
                            if (array_key_exists($index, $request->on_wa_spkg)) {
                                $wa_spkg[$index] = $request->on_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
//                        return $request->on_class_0_charges[$index];
                        if ($request->on_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->on_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_wa_range_up[$index],
                                    'range_down' => $request->on_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->on_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->on_class_0_charges[$index],
                                    'national_charges_class_1' => $request->on_class_1_charges[$index],
                                    'national_charges_class_2' => $request->on_class_2_charges[$index],
                                    'national_charges_class_3' => $request->on_class_3_charges[$index]
                                ]);
                        }
                        if ($request->on_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_wa_range_up[$index],
                                'range_down' => $request->on_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->on_wa_local_charges[$index],
                                'national_charges_class_0' => $request->on_class_0_charges[$index],
                                'national_charges_class_1' => $request->on_class_1_charges[$index],
                                'national_charges_class_2' => $request->on_class_2_charges[$index],
                                'national_charges_class_3' => $request->on_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->on_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->on_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_cash_record)->delete();

                        foreach ($request->on_cash_record as $index => $on_cash_record) {
                            if ($request->on_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->on_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                            if ($request->on_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_insurance_record)->delete();
                        foreach ($request->on_insurance_record as $insurance => $on_insurance_record) {
                            if ($request->on_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->on_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_ins_range_up[$insurance],
                                    'range_down' => $request->on_ins_range_down[$insurance],
                                    'charges' => $request->on_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->on_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_ins_range_up[$insurance],
                                    'range_down' => $request->on_ins_range_down[$insurance],
                                    'charges' => $request->on_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                        if ($request->on_return_record != null) {
                            ReturnCharge::where(['id' => $request->on_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0'=> $request->on_return_class_0_charges,
                                'national_charges_class_1'=> $request->on_return_class_1_charges,
                                'national_charges_class_2'=> $request->on_return_class_2_charges,
                                'national_charges_class_3'=> $request->on_return_class_3_charges
                            ]);
                        } elseif ($request->on_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0'=> $request->on_return_class_0_charges,
                                'national_charges_class_1'=> $request->on_return_class_1_charges,
                                'national_charges_class_2'=> $request->on_return_class_2_charges,
                                'national_charges_class_3'=> $request->on_return_class_3_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                        if ($request->on_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->on_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'fuel_surcharge' => $request->overnight_fuel_surcharge
                            ]);
                        } elseif ($request->on_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'fuel_surcharge' => $request->overnight_fuel_surcharge
                            ]);
                        }

                    }
                    //Packaging Charges
                    if ($request->has('on_packaging_switch') && $request->on_packaging_switch == 'on') {
                        if ($request->on_packaging_record != null) {
                            PackagingCharge::where(['id' => $request->on_packaging_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'sm_flyer' => $request->on_flyer_sm,
                                'md_flyer' => $request->on_flyer_md,
                                'lg_flyer' => $request->on_flyer_lg,
                                'box_flyer' => $request->on_flyer_box
                            ]);
                        } else {
                            PackagingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'sm_flyer' => $request->on_flyer_sm,
                                'md_flyer' => $request->on_flyer_md,
                                'lg_flyer' => $request->on_flyer_lg,
                                'box_flyer' => $request->on_flyer_box
                            ]);
                        }

                    }
                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                        $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : 0;
//                    $discount_weight = $request->on_discount_weight_rate;
                    }
                    if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                        $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : 0;
                    }
                    if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : 0;
                    }
                    if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                        $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : 0;
                    }
                    if ($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->on_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->on_discount_record != null) {
                            DiscountCharge::where(['id' => $request->on_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'title' => $request->on_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->on_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'title' => $request->on_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }
                //dd($weightAlready);
            }

            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->get();

                if (!$ONRateAlready->isEmpty()) {

                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_weight_record)->delete();
                    foreach ($request->ol_weight_record as $index => $ol_weight_record) {
                        if ($request->has('ol_wa_switch')) {
                            if (array_key_exists($index, $request->ol_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('ol_wa_spkg')) {
                            if (array_key_exists($index, $request->ol_wa_spkg)) {
                                $wa_spkg[$index] = $request->ol_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        if ($request->ol_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->ol_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_wa_range_up[$index],
                                    'range_down' => $request->ol_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->ol_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->ol_class_0_charges[$index],
                                    'national_charges_class_1' => $request->ol_class_1_charges[$index],
                                    'national_charges_class_2' => $request->ol_class_2_charges[$index],
                                    'national_charges_class_3' => $request->ol_class_3_charges[$index]
                                ]);
                        }
                        if ($request->ol_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_wa_range_up[$index],
                                'range_down' => $request->ol_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->ol_wa_local_charges[$index],
                                'national_charges_class_0' => $request->ol_class_0_charges[$index],
                                'national_charges_class_1' => $request->ol_class_1_charges[$index],
                                'national_charges_class_2' => $request->ol_class_2_charges[$index],
                                'national_charges_class_3' => $request->ol_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->ol_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->ol_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_cash_record)->delete();

                        foreach ($request->ol_cash_record as $index => $ol_cash_record) {
                            if ($request->ol_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->ol_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                            if ($request->ol_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_insurance_record)->delete();
                        foreach ($request->ol_insurance_record as $insurance => $ol_insurance_record) {
                            if ($request->ol_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->ol_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_ins_range_up[$insurance],
                                    'range_down' => $request->ol_ins_range_down[$insurance],
                                    'charges' => $request->ol_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->ol_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_ins_range_up[$insurance],
                                    'range_down' => $request->ol_ins_range_down[$insurance],
                                    'charges' => $request->ol_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                        if ($request->ol_return_record != null) {
                            ReturnCharge::where(['id' => $request->ol_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0'=> $request->ol_return_class_0_charges,
                                'national_charges_class_1'=> $request->ol_return_class_1_charges,
                                'national_charges_class_2'=> $request->ol_return_class_2_charges,
                                'national_charges_class_3'=> $request->ol_return_class_3_charges
                            ]);
                        } elseif ($request->ol_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0'=> $request->ol_return_class_0_charges,
                                'national_charges_class_1'=> $request->ol_return_class_1_charges,
                                'national_charges_class_2'=> $request->ol_return_class_2_charges,
                                'national_charges_class_3'=> $request->ol_return_class_3_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        if ($request->ol_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->ol_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'fuel_surcharge' => $request->overland_fuel_surcharge
                            ]);
                        } elseif ($request->ol_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'fuel_surcharge' => $request->overland_fuel_surcharge
                            ]);
                        }

                    }
                    //Packaging Charges
                    if ($request->has('ol_packaging_switch') && $request->ol_packaging_switch == 'on') {
                        if ($request->ol_packaging_record != null) {
                            PackagingCharge::where(['id' => $request->ol_packaging_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'sm_flyer' => $request->ol_flyer_sm,
                                'md_flyer' => $request->ol_flyer_md,
                                'lg_flyer' => $request->ol_flyer_lg,
                                'box_flyer' => $request->ol_flyer_box
                            ]);
                        } else {
                            PackagingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'sm_flyer' => $request->ol_flyer_sm,
                                'md_flyer' => $request->ol_flyer_md,
                                'lg_flyer' => $request->ol_flyer_lg,
                                'box_flyer' => $request->ol_flyer_box
                            ]);
                        }

                    }
                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : 0;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : 0;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : 0;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : 0;
                    }
                    if ($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->ol_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->ol_discount_record != null) {
                            DiscountCharge::where(['id' => $request->ol_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'title' => $request->ol_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->ol_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'title' => $request->ol_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }
                //dd($weightAlready);
            }

            //detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->get();

                if (!$ONRateAlready->isEmpty()) {

                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_weight_record)->delete();
                    foreach ($request->detain_weight_record as $index => $detain_weight_record) {
                        if ($request->has('detain_wa_switch')) {
                            if (array_key_exists($index, $request->detain_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('detain_wa_spkg')) {
                            if (array_key_exists($index, $request->detain_wa_spkg)) {
                                $wa_spkg[$index] = $request->detain_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        if ($request->detain_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->detain_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_wa_range_up[$index],
                                    'range_down' => $request->detain_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->detain_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->detain_class_0_charges[$index],
                                    'national_charges_class_1' => $request->detain_class_1_charges[$index],
                                    'national_charges_class_2' => $request->detain_class_2_charges[$index],
                                    'national_charges_class_3' => $request->detain_class_3_charges[$index]
                                ]);
                        }
                        if ($request->detain_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_wa_range_up[$index],
                                'range_down' => $request->detain_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->detain_wa_local_charges[$index],
                                'national_charges_class_0' => $request->detain_class_0_charges[$index],
                                'national_charges_class_1' => $request->detain_class_1_charges[$index],
                                'national_charges_class_2' => $request->detain_class_2_charges[$index],
                                'national_charges_class_3' => $request->detain_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->detain_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->detain_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_cash_record)->delete();

                        foreach ($request->detain_cash_record as $index => $detain_cash_record) {
                            if ($request->detain_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->detain_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                            if ($request->detain_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_insurance_record)->delete();
                        foreach ($request->detain_insurance_record as $insurance => $detain_insurance_record) {
                            if ($request->detain_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->detain_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_ins_range_up[$insurance],
                                    'range_down' => $request->detain_ins_range_down[$insurance],
                                    'charges' => $request->detain_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->detain_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_ins_range_up[$insurance],
                                    'range_down' => $request->detain_ins_range_down[$insurance],
                                    'charges' => $request->detain_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                        if ($request->detain_return_record != null) {
                            ReturnCharge::where(['id' => $request->detain_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0'=> $request->detain_return_class_0_charges,
                                'national_charges_class_1'=> $request->detain_return_class_1_charges,
                                'national_charges_class_2'=> $request->detain_return_class_2_charges,
                                'national_charges_class_3'=> $request->detain_return_class_3_charges
                            ]);
                        } elseif ($request->detain_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0'=> $request->detain_return_class_0_charges,
                                'national_charges_class_1'=> $request->detain_return_class_1_charges,
                                'national_charges_class_2'=> $request->detain_return_class_2_charges,
                                'national_charges_class_3'=> $request->detain_return_class_3_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        if ($request->detain_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->detain_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'fuel_surcharge' => $request->detain_fuel_surcharge
                            ]);
                        } elseif ($request->detain_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'fuel_surcharge' => $request->detain_fuel_surcharge
                            ]);
                        }

                    }
                    //Packaging Charges
                    if ($request->has('detain_packaging_switch') && $request->detain_packaging_switch == 'on') {
                        if ($request->detain_packaging_record != null) {
                            PackagingCharge::where(['id' => $request->detain_packaging_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'sm_flyer' => $request->detain_flyer_sm,
                                'md_flyer' => $request->detain_flyer_md,
                                'lg_flyer' => $request->detain_flyer_lg,
                                'box_flyer' => $request->detain_flyer_box
                            ]);
                        } else {
                            PackagingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'sm_flyer' => $request->detain_flyer_sm,
                                'md_flyer' => $request->detain_flyer_md,
                                'lg_flyer' => $request->detain_flyer_lg,
                                'box_flyer' => $request->detain_flyer_box
                            ]);
                        }

                    }
                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                        $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : 0;
//                    $discount_weight = $request->detain_discount_weight_rate;
                    }
                    if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                        $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : 0;
                    }
                    if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : 0;
                    }
                    if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                        $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : 0;
                    }
                    if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->detain_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->detain_discount_record != null) {
                            DiscountCharge::where(['id' => $request->detain_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'title' => $request->detain_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->detain_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'title' => $request->detain_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }
                //dd($weightAlready);
            }

            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->get();

                if (!$ONRateAlready->isEmpty()) {

                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_weight_record)->delete();
                    foreach ($request->sameday_weight_record as $index => $sameday_weight_record) {
                        if ($request->has('sameday_wa_switch')) {
                            if (array_key_exists($index, $request->sameday_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('sameday_wa_spkg')) {
                            if (array_key_exists($index, $request->sameday_wa_spkg)) {
                                $wa_spkg[$index] = $request->sameday_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        if ($request->sameday_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->sameday_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_wa_range_up[$index],
                                    'range_down' => $request->sameday_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                                    'national_charges_class_1' => 0,
                                    'national_charges_class_2' => 0,
                                    'national_charges_class_3' => 0
                                ]);
                        }
                        if ($request->sameday_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_wa_range_up[$index],
                                'range_down' => $request->sameday_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                                'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->sameday_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->sameday_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_cash_record)->delete();

                        foreach ($request->sameday_cash_record as $index => $sameday_cash_record) {
                            if ($request->sameday_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->sameday_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                            if ($request->sameday_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_insurance_record)->delete();
                        foreach ($request->sameday_insurance_record as $insurance => $sameday_insurance_record) {
                            if ($request->sameday_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->sameday_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_ins_range_up[$insurance],
                                    'range_down' => $request->sameday_ins_range_down[$insurance],
                                    'charges' => $request->sameday_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->sameday_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_ins_range_up[$insurance],
                                    'range_down' => $request->sameday_ins_range_down[$insurance],
                                    'charges' => $request->sameday_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                        if ($request->sameday_return_record != null) {
                            ReturnCharge::where(['id' => $request->sameday_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                                'national_charges_class_1'=> 0,
                                'national_charges_class_2'=> 0,
                                'national_charges_class_3'=> 0
                            ]);
                        } elseif ($request->sameday_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                                'national_charges_class_1'=> 0,
                                'national_charges_class_2'=> 0,
                                'national_charges_class_3'=> 0
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        if ($request->sameday_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->sameday_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'fuel_surcharge' => $request->sameday_fuel_surcharge
                            ]);
                        } elseif ($request->sameday_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'fuel_surcharge' => $request->sameday_fuel_surcharge
                            ]);
                        }

                    }
                    //Packaging Charges
                    if ($request->has('sameday_packaging_switch') && $request->sameday_packaging_switch == 'on') {
                        if ($request->sameday_packaging_record != null) {
                            PackagingCharge::where(['id' => $request->sameday_packaging_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'sm_flyer' => $request->sameday_flyer_sm,
                                'md_flyer' => $request->sameday_flyer_md,
                                'lg_flyer' => $request->sameday_flyer_lg,
                                'box_flyer' => $request->sameday_flyer_box
                            ]);
                        } else {
                            PackagingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'sm_flyer' => $request->sameday_flyer_sm,
                                'md_flyer' => $request->sameday_flyer_md,
                                'lg_flyer' => $request->sameday_flyer_lg,
                                'box_flyer' => $request->sameday_flyer_box
                            ]);
                        }

                    }
                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                        $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : 0;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                    }
                    if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                        $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : 0;
                    }
                    if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : 0;
                    }
                    if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                        $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : 0;
                    }
                    if ($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->sameday_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->sameday_discount_record != null) {
                            DiscountCharge::where(['id' => $request->sameday_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'title' => $request->sameday_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->sameday_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'title' => $request->sameday_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }
                //dd($weightAlready);
            }
            User::where('id',$id)->update(['rate_status'=>1]);
            if($request->authorize == 1){
                User::where('id',$id)->update(['rate_status'=>0,'status'=>2,'rates_authorized_by'=>Auth::id()]);
                return redirect(route('admin.accounts.pending'))->with('success','User is now authorized.');
            }

            return redirect()->back()->with('success', 'All Rates are updated');
        }

        if ($user['status'] == 3) {
            $messages = [
                'on_wa_range_up.*.required' => 'The overnight range up field is required.',
                'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
                'on_wa_range_down.*.required' => 'The overnight range down field is required.',
                'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
                'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
                'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
//            'on_class_0_charges.*.numeric' => 'The overnight national charges field must be numeric.',
                'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
                'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
                'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
                'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
                'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
                'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
                'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
                'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
                'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
                //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
                'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
                'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
                'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
                'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
                'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
                //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
                'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
                'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
                'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
                'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
                'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
                'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
                'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
                'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
                'on_flyer_sm.required_if' => 'The overnight small flyer field is required',
                'on_flyer_sm.numeric' => 'The overnight small flyer field must be numeric',
                'on_flyer_md.required_if' => 'The overnight meduim flyer field is required',
                'on_flyer_md.numeric' => 'The overnight medium flyer field must be numeric',
                'on_flyer_lg.required_if' => 'The overnight large flyer field is required',
                'on_flyer_lg.numeric' => 'The overnight large flyer field must be numeric',
                'on_flyer_box.required_if' => 'The overnight box flyer field is required',
                'on_flyer_box.numeric' => 'The overnight box flyer field must be numeric',
                'on_discount_title.required_if' => 'The overnight discount title field must be required',
                'on_daterange.required_if' => 'The overnight discount date range field must be required',
                'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
//            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
                'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
//            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
                'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
//            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
                'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
//            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
                'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
//            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
                'on_discount_title.required_with' => 'The overnight discount title field is required',
                'on_daterange.required_with' => 'The overnight discount date field is required',
                //overland starts
                'ol_wa_range_up.*.required' => 'The overland range up field is required.',
                'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
                'ol_wa_range_down.*.required' => 'The overland range down field is required.',
                'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
                'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
                'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
                'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
                'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
                'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
                'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
                'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
                'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
                'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
                'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
                'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
                //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
                'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
                'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
                'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
                'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
                'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
                //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
                'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
                'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
                'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
                'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
                'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
                'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
                'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
                'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
                'ol_flyer_sm.required_if' => 'The overland small flyer field is required',
                'ol_flyer_sm.numeric' => 'The overland small flyer field must be numeric',
                'ol_flyer_md.required_if' => 'The overland meduim flyer field is required',
                'ol_flyer_md.numeric' => 'The overland medium flyer field must be numeric',
                'ol_flyer_lg.required_if' => 'The overland large flyer field is required',
                'ol_flyer_lg.numeric' => 'The overland large flyer field must be numeric',
                'ol_flyer_box.required_if' => 'The overland box flyer field is required',
                'ol_flyer_box.numeric' => 'The overland box flyer field must be numeric',
                'ol_discount_title.required_if' => 'The overland discount title field must be required',
                'ol_daterange.required_if' => 'The overland discount date range field must be required',
                'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
//            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
                'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
//            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
                'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
//            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
                'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
//            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
                'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
//            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
                'ol_discount_title.required_with' => 'The overland discount title field is required',
                'ol_daterange.required_with' => 'The overland discount date field is required',
                //overland end and detain starts
                'detain_wa_range_up.*.required' => 'The detain range up field is required.',
                'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
                'detain_wa_range_down.*.required' => 'The detain range down field is required.',
                'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
                'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
                'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
                'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
                'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
                'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
                'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
                'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
                'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
                'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
                'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
                'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
                //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
                'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
                'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
                'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
                'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
                'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
                //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
                'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
                'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
                'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
                'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
                'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
                'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
                'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
                'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
                'detain_flyer_sm.required_if' => 'The detain small flyer field is required',
                'detain_flyer_sm.numeric' => 'The detain small flyer field must be numeric',
                'detain_flyer_md.required_if' => 'The detain meduim flyer field is required',
                'detain_flyer_md.numeric' => 'The detain medium flyer field must be numeric',
                'detain_flyer_lg.required_if' => 'The detain large flyer field is required',
                'detain_flyer_lg.numeric' => 'The detain large flyer field must be numeric',
                'detain_flyer_box.required_if' => 'The detain box flyer field is required',
                'detain_flyer_box.numeric' => 'The detain box flyer field must be numeric',
                'detain_discount_title.required_if' => 'The detain discount title field must be required',
                'detain_daterange.required_if' => 'The detain discount date range field must be required',
                'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
//            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
                'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
//            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
                'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
//            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
                'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
//            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
                'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
//            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
                'detain_discount_title.required_with' => 'The detain discount title field is required',
                'detain_daterange.required_with' => 'The detain discount date field is required',
                //detain ends and sameday starts
                'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
                'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
                'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
                'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
                'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
                'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
                'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
                'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
                'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
                'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
                'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
                'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
                'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
                'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
                'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
                'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
                'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
                'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
                'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
                'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
                // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
                'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
                'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
                'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
                'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
                'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
                //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
                'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
                'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
                'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
                'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
                'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
                'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
                'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
                'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',
                'sameday_flyer_sm.required_if' => 'The sameday small flyer field is required',
                'sameday_flyer_sm.numeric' => 'The sameday small flyer field must be numeric',
                'sameday_flyer_md.required_if' => 'The sameday meduim flyer field is required',
                'sameday_flyer_md.numeric' => 'The sameday medium flyer field must be numeric',
                'sameday_flyer_lg.required_if' => 'The sameday large flyer field is required',
                'sameday_flyer_lg.numeric' => 'The sameday large flyer field must be numeric',
                'sameday_flyer_box.required_if' => 'The sameday box flyer field is required',
                'sameday_flyer_box.numeric' => 'The sameday box flyer field must be numeric',
                'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
                'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
                'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
//            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
                'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
//            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
                'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
//            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
                'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
//            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
                'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
//            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
                'sameday_discount_title.required_with' => 'The sameday discount title field is required',
                'sameday_daterange.required_with' => 'The sameday discount date field is required',
                //sameday ends
            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $on_validations = [
                    'on_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'on_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'on_wa_local_charges.*' => 'required|numeric',
                    'on_class_0_charges.*' => 'required|numeric',
                    'on_class_1_charges.*' => 'required',
                    'on_class_2_charges.*' => 'required',
                    'on_class_3_charges.*' => 'required',
                    'on_wa_spkg.*' => 'numeric',
                    'on_replacement_charges' => 'required|numeric',
                    'on_tnb_charges' => 'required|numeric',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',
                    'on_flyer_sm' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_md' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_lg' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_box' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on'
                ];
            }
            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ol_validations = [
                    'ol_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'ol_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'ol_wa_local_charges.*' => 'required|numeric',
                    'ol_class_0_charges.*' => 'required|numeric',
                    'ol_class_1_charges.*' => 'required',
                    'ol_class_2_charges.*' => 'required',
                    'ol_class_3_charges.*' => 'required',
                    'ol_wa_spkg.*' => 'numeric',
                    'ol_replacement_charges' => 'required|numeric',
                    'ol_tnb_charges' => 'required|numeric',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',
                    'ol_flyer_sm' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_md' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_lg' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_box' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',
                ];
            }
            //overland
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $detain_validations = [
                    'detain_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'detain_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'detain_wa_local_charges.*' => 'required|numeric',
                    'detain_class_0_charges.*' => 'required|numeric',
                    'detain_class_1_charges.*' => 'required',
                    'detain_class_2_charges.*' => 'required',
                    'detain_class_3_charges.*' => 'required',
                    'detain_wa_spkg.*' => 'numeric',
                    'detain_replacement_charges' => 'required|numeric',
                    'detain_tnb_charges' => 'required|numeric',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',
                    'detain_flyer_sm' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_md' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_lg' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_box' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',
                ];
            }
            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $sameday_validations = [
                    'sameday_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'sameday_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'sameday_wa_local_charges.*' => 'required|numeric',
                    'sameday_class_0_charges.*' => 'required|numeric',
                    'sameday_wa_spkg.*' => 'numeric',
                    'sameday_replacement_charges' => 'required|numeric',
                    'sameday_tnb_charges' => 'required|numeric',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                    'sameday_flyer_sm' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_md' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_lg' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_box' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',
                ];
            }

            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }

            PendingRateStatus::where('user_id', $id)->delete();
            PendingWeightCharge::where('user_id', $id)->delete();
            PendingBookingTypeCharges::where('user_id', $id)->delete();
            PendingCashHandlingCharge::where('user_id', $id)->delete();
            PendingInsuranceCharge::where('user_id', $id)->delete();
            PendingReturnCharge::where('user_id', $id)->delete();
            PendingFuelSurcharge::where('user_id', $id)->delete();
            PendingPackagingCharge::where('user_id', $id)->delete();
            PendingDiscountCharge::where('user_id', $id)->delete();

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $ONRateAlready = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 1)->get();
                if ($ONRateAlready->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => ($request->has('on_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
//                   'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('on_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch = array();
                    $wa_spkg = array();
                    foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                        if ($request->has('on_wa_switch')) {
                            if (array_key_exists($index, $request->on_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('on_wa_spkg')) {
                            if (array_key_exists($index, $request->on_wa_spkg)) {
                                $wa_spkg[$index] = $request->on_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $request->on_wa_range_up[$index],
                            'range_down' => $request->on_wa_range_down[$index],
                            'weight_addition' => $wa_switch[$index],
                            'spkg' => $wa_spkg[$index],
                            'local_or_6hr' => $request->on_wa_local_charges[$index],
                            'national_charges_class_0' => $request->on_class_0_charges[$index],
                            'national_charges_class_1' => $request->on_class_1_charges[$index],
                            'national_charges_class_2' => $request->on_class_2_charges[$index],
                            'national_charges_class_3' => $request->on_class_3_charges[$index]
                        ]);

                    }

                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'replacement_charges' => $request->on_replacement_charges,
                        'try_and_buy_charges' => $request->on_tnb_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        foreach ($request->on_cash_range_up as $ind => $on_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_cash_range_up[$ind],
                                'range_down' => $request->on_cash_range_down[$ind],
                                'charges' => $request->on_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_ins_range_up[$insurance],
                                'range_down' => $request->on_ins_range_down[$insurance],
                                'charges' => $request->on_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $request->on_return_local_charges,
                            'national_charges_class_0'=> $request->on_return_class_0_charges,
                            'national_charges_class_1'=> $request->on_return_class_1_charges,
                            'national_charges_class_2'=> $request->on_return_class_2_charges,
                            'national_charges_class_3'=> $request->on_return_class_3_charges
                        ]);
                    }
                    //Return Charges
                    if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $request->overnight_fuel_surcharge
                        ]);
                    }
                    //Packaging Charges
                    if ($request->has('on_packaging_switch') && $request->on_packaging_switch == 'on') {
                        PendingPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'sm_flyer' => $request->on_flyer_sm,
                            'md_flyer' => $request->on_flyer_md,
                            'lg_flyer' => $request->on_flyer_lg,
                            'box_flyer' => $request->on_flyer_box
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                        $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                    }
                    if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                        $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                    }
                    if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                    }
                    if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                        $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                    }
                    if ($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->on_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $request->on_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                }
                //dd($weightAlready);
            }
            //Overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {

                $OLRatePresent = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 2)->get();

                if ($OLRatePresent->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('ol_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch_overland = array();
                    $wa_spkg_overland = array();
                    foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                        if ($request->has('ol_wa_switch')) {
                            if (array_key_exists($index, $request->ol_wa_switch)) {
                                $wa_switch_overland[$index] = 1;
                            } else {
                                $wa_switch_overland[$index] = 0;
                            };
                        } else {
                            $wa_switch_overland[$index] = 0;
                        }
                        if ($request->has('ol_wa_spkg')) {
                            if (array_key_exists($index, $request->ol_wa_spkg)) {
                                $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                            } else {
                                $wa_spkg_overland[$index] = 0;
                            };
                        } else {
                            $wa_spkg_overland[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $request->ol_wa_range_up[$index],
                            'range_down' => $request->ol_wa_range_down[$index],
                            'weight_addition' => $wa_switch_overland[$index],
                            'spkg' => $wa_spkg_overland[$index],
                            'local_or_6hr' => $request->ol_wa_local_charges[$index],
                            'national_charges_class_0' => $request->ol_class_0_charges[$index],
                            'national_charges_class_1' => $request->ol_class_1_charges[$index],
                            'national_charges_class_2' => $request->ol_class_2_charges[$index],
                            'national_charges_class_3' => $request->ol_class_3_charges[$index]
                        ]);
                    }


                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'replacement_charges' => $request->ol_replacement_charges,
                        'try_and_buy_charges' => $request->ol_tnb_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_cash_range_up[$ind],
                                'range_down' => $request->ol_cash_range_down[$ind],
                                'charges' => $request->ol_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                        foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_ins_range_up[$insurance],
                                'range_down' => $request->ol_ins_range_down[$insurance],
                                'charges' => $request->ol_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $request->ol_return_local_charges,
                            'national_charges_class_0'=> $request->ol_return_class_0_charges,
                            'national_charges_class_1'=> $request->ol_return_class_1_charges,
                            'national_charges_class_2'=> $request->ol_return_class_2_charges,
                            'national_charges_class_3'=> $request->ol_return_class_3_charges
                        ]);
                    }
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $request->overland_fuel_surcharge
                        ]);
                    }
                    //Packaging Charges
                    if ($request->has('ol_packaging_switch') && $request->ol_packaging_switch == 'on') {
                        PendingPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'sm_flyer' => $request->ol_flyer_sm,
                            'md_flyer' => $request->ol_flyer_md,
                            'lg_flyer' => $request->ol_flyer_lg,
                            'box_flyer' => $request->ol_flyer_box
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                    }
                    if ($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->ol_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $request->ol_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                }

            }
            //Detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

                $DetainRatePresent = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

                if ($DetainRatePresent->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('detain_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch_detain = array();
                    $wa_spkg_detain = array();
                    foreach ($request->detain_wa_range_up as $index => $detain_wa_range_up) {
                        if ($request->has('detain_wa_switch')) {
                            if (array_key_exists($index, $request->detain_wa_switch)) {
                                $wa_switch_detain[$index] = 1;
                            } else {
                                $wa_switch_detain[$index] = 0;
                            };
                        } else {
                            $wa_switch_detain[$index] = 0;
                        }
                        if ($request->has('detain_wa_spkg')) {
                            if (array_key_exists($index, $request->detain_wa_spkg)) {
                                $wa_spkg_detain[$index] = $request->detain_wa_spkg[$index];
                            } else {
                                $wa_spkg_detain[$index] = 0;
                            };
                        } else {
                            $wa_spkg_detain[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_wa_range_up[$index],
                            'range_down' => $request->detain_wa_range_down[$index],
                            'weight_addition' => $wa_switch_detain[$index],
                            'spkg' => $wa_spkg_detain[$index],
                            'local_or_6hr' => $request->detain_wa_local_charges[$index],
                            'national_charges_class_0' => $request->detain_class_0_charges[$index],
                            'national_charges_class_1' => $request->detain_class_1_charges[$index],
                            'national_charges_class_2' => $request->detain_class_2_charges[$index],
                            'national_charges_class_3' => $request->detain_class_3_charges[$index]
                        ]);
                    }


                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'replacement_charges' => $request->detain_replacement_charges,
                        'try_and_buy_charges' => $request->detain_tnb_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_cash_range_up[$ind],
                                'range_down' => $request->detain_cash_range_down[$ind],
                                'charges' => $request->detain_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                        foreach ($request->detain_ins_range_up as $insurance => $detain_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_ins_range_up[$insurance],
                                'range_down' => $request->detain_ins_range_down[$insurance],
                                'charges' => $request->detain_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $request->detain_return_local_charges,
                            'national_charges_class_0'=> $request->detain_return_class_0_charges,
                            'national_charges_class_1'=> $request->detain_return_class_1_charges,
                            'national_charges_class_2'=> $request->detain_return_class_2_charges,
                            'national_charges_class_3'=> $request->detain_return_class_3_charges
                        ]);
                    }
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $request->detain_fuel_surcharge
                        ]);
                    }
                    //Packaging Charges
                    if ($request->has('detain_packaging_switch') && $request->detain_packaging_switch == 'on') {
                        PendingPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'sm_flyer' => $request->detain_flyer_sm,
                            'md_flyer' => $request->detain_flyer_md,
                            'lg_flyer' => $request->detain_flyer_lg,
                            'box_flyer' => $request->detain_flyer_box
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                        $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : null;
//                    $discount_weight = $request->detain_discount_weight_rate;
                    }
                    if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                        $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : null;
                    }
                    if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : null;
                    }
                    if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                        $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : null;
                    }
                    if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->detain_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $request->detain_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                }

            }
            //Sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {

                $SamedayRatePresent = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 4)->get();
                if ($SamedayRatePresent->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('sameday_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch_sameday = array();
                    $wa_spkg_sameday = array();
                    foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                        if ($request->has('sameday_wa_switch')) {
                            if (array_key_exists($index, $request->sameday_wa_switch)) {
                                $wa_switch_sameday[$index] = 1;
                            } else {
                                $wa_switch_sameday[$index] = 0;
                            };
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        }
                        if ($request->has('sameday_wa_spkg')) {
                            if (array_key_exists($index, $request->sameday_wa_spkg)) {
                                $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                            } else {
                                $wa_spkg_sameday[$index] = 0;
                            };
                        } else {
                            $wa_spkg_sameday[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $request->sameday_wa_range_up[$index],
                            'range_down' => $request->sameday_wa_range_down[$index],
                            'weight_addition' => $wa_switch_sameday[$index],
                            'spkg' => $wa_spkg_sameday[$index],
                            'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                            'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                            'national_charges_class_1' => 0,
                            'national_charges_class_2' => 0,
                            'national_charges_class_3' => 0
                        ]);
                    }


                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'replacement_charges' => $request->sameday_replacement_charges,
                        'try_and_buy_charges' => $request->sameday_tnb_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_cash_range_up[$ind],
                                'range_down' => $request->sameday_cash_range_down[$ind],
                                'charges' => $request->sameday_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                        foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_ins_range_up[$insurance],
                                'range_down' => $request->sameday_ins_range_down[$insurance],
                                'charges' => $request->sameday_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $request->sameday_return_local_charges,
                            'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                            'national_charges_class_1'=> 0,
                            'national_charges_class_2'=> 0,
                            'national_charges_class_3'=> 0
                        ]);
                    }
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $request->sameday_fuel_surcharge
                        ]);
                    }
                    //Packaging Charges
                    if ($request->has('sameday_packaging_switch') && $request->sameday_packaging_switch == 'on') {
                        PendingPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'sm_flyer' => $request->sameday_flyer_sm,
                            'md_flyer' => $request->sameday_flyer_md,
                            'lg_flyer' => $request->sameday_flyer_lg,
                            'box_flyer' => $request->sameday_flyer_box
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                        $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                    }
                    if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                        $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                    }
                    if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                    }
                    if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                        $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                    }
                    if ($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->sameday_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $request->sameday_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                }
            }
            //dd($weightAlready);

            if ($request->approve == 1) {
                $user = User::find($id);

                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 1])->first()) {

                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 2])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 3])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 4])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }

                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($packagings = PackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($packagings as $packaging) {
                        HistoryPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'sm_flyer' => $packaging['sm_flyer'],
                            'md_flyer' => $packaging['md_flyer'],
                            'lg_flyer' => $packaging['lg_flyer'],
                            'box_flyer' => $packaging['box_flyer']
                        ]);
                    }
                }
                if($packagings = PackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($packagings as $packaging) {
                        HistoryPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'sm_flyer' => $packaging['sm_flyer'],
                            'md_flyer' => $packaging['md_flyer'],
                            'lg_flyer' => $packaging['lg_flyer'],
                            'box_flyer' => $packaging['box_flyer']
                        ]);
                    }
                }
                if($packagings = PackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($packagings as $packaging) {
                        HistoryPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'sm_flyer' => $packaging['sm_flyer'],
                            'md_flyer' => $packaging['md_flyer'],
                            'lg_flyer' => $packaging['lg_flyer'],
                            'box_flyer' => $packaging['box_flyer']
                        ]);
                    }
                }
                if($packagings = PackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($packagings as $packaging) {
                        HistoryPackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'sm_flyer' => $packaging['sm_flyer'],
                            'md_flyer' => $packaging['md_flyer'],
                            'lg_flyer' => $packaging['lg_flyer'],
                            'box_flyer' => $packaging['box_flyer']
                        ]);
                    }
                }
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                $s = RateStatus::where(['user_id' => $id ])->first();
                RateHistory::create([
                    'user_id' => $id,
                    'updated_by' => $user['rates_updated_by'],
                    'approved_by' => $user['rates_authorized_by'],
                    'from_date' => $s['created_at'],
                    'to_date' => Carbon::now()
                ]);

                RateStatus::where('user_id', $id)->delete();
                WeightCharge::where('user_id', $id)->delete();
                BookingTypeCharges::where('user_id', $id)->delete();
                CashHandlingCharge::where('user_id', $id)->delete();
                InsuranceCharge::where('user_id', $id)->delete();
                ReturnCharge::where('user_id', $id)->delete();
                FuelSurcharge::where('user_id', $id)->delete();
                PackagingCharge::where('user_id', $id)->delete();
                DiscountCharge::where('user_id', $id)->delete();

                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 1])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 2])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 3])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 4])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendinginsurance = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingpackagings = PendingPackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingpackagings as $pendingpackaging) {
                        PackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'sm_flyer' => $pendingpackaging['sm_flyer'],
                            'md_flyer' => $pendingpackaging['md_flyer'],
                            'lg_flyer' => $pendingpackaging['lg_flyer'],
                            'box_flyer' => $pendingpackaging['box_flyer']
                        ]);
                    }
                }
                if($pendingpackagings = PendingPackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingpackagings as $pendingpackaging) {
                        PackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'sm_flyer' => $pendingpackaging['sm_flyer'],
                            'md_flyer' => $pendingpackaging['md_flyer'],
                            'lg_flyer' => $pendingpackaging['lg_flyer'],
                            'box_flyer' => $pendingpackaging['box_flyer']
                        ]);
                    }
                }
                if($pendingpackagings = PendingPackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingpackagings as $pendingpackaging) {
                        PackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'sm_flyer' => $pendingpackaging['sm_flyer'],
                            'md_flyer' => $pendingpackaging['md_flyer'],
                            'lg_flyer' => $pendingpackaging['lg_flyer'],
                            'box_flyer' => $pendingpackaging['box_flyer']
                        ]);
                    }
                }
                if($pendingpackagings = PendingPackagingCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingpackagings as $pendingpackaging) {
                        PackagingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'sm_flyer' => $pendingpackaging['sm_flyer'],
                            'md_flyer' => $pendingpackaging['md_flyer'],
                            'lg_flyer' => $pendingpackaging['lg_flyer'],
                            'box_flyer' => $pendingpackaging['box_flyer']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                PendingRateStatus::where('user_id', $id)->delete();
                PendingWeightCharge::where('user_id', $id)->delete();
                PendingBookingTypeCharges::where('user_id', $id)->delete();
                PendingCashHandlingCharge::where('user_id', $id)->delete();
                PendingInsuranceCharge::where('user_id', $id)->delete();
                PendingReturnCharge::where('user_id', $id)->delete();
                PendingFuelSurcharge::where('user_id', $id)->delete();
                PendingPackagingCharge::where('user_id', $id)->delete();
                PendingDiscountCharge::where('user_id', $id)->delete();
                User::where('id', $id)->update(['rate_status' => 0, 'rates_authorized_by' => Auth::id()]);
                return redirect(route('admin.accounts.active'))->with('success', 'User Rates is now approved.');
            }
            User::where('id', $id)->update(['rate_status' => 1, 'rates_updated_by' => Auth::id()]);
            return redirect()->back()->with('success', 'All Rates are updated');
        }
    }
    /**
     * @param Request $request
     * @param $id
     * @return int
     */
    public function addRates(Request $request, $id){

        $messages = [
            'on_wa_range_up.*.required' => 'The overnight range up field is required.',
            'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
            'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
            'on_wa_range_down.*.required' => 'The overnight range down field is required.',
            'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
            'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
            'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
            'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
            'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
            'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
            'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
            'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
            'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
            'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
//            'on_class_0_charges.*.numeric' => 'The overnight national charges field must be numeric.',
            'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
            'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
            'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
            'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
            'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
            'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
            'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
            'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
            'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
            //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
            'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
            'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
            'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
            'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
            'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
            //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
            'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
            'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
            'on_return_class_0_charges.*.numeric' => 'The overnight class A return charges field must be numeric.',
            'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
            'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
            'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
            'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
            'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
            'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'on_flyer_sm.required_if' => 'The overnight small flyer field is required',
            'on_flyer_sm.numeric' => 'The overnight small flyer field must be numeric',
            'on_flyer_md.required_if' => 'The overnight meduim flyer field is required',
            'on_flyer_md.numeric' => 'The overnight medium flyer field must be numeric',
            'on_flyer_lg.required_if' => 'The overnight large flyer field is required',
            'on_flyer_lg.numeric' => 'The overnight large flyer field must be numeric',
            'on_flyer_box.required_if' => 'The overnight box flyer field is required',
            'on_flyer_box.numeric' => 'The overnight box flyer field must be numeric',
            'on_discount_title.required_if' => 'The overnight discount title field must be required',
            'on_daterange.required_if' => 'The overnight discount date range field must be required',
            'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
//            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
            'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
//            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
            'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
//            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
            'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
//            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
            'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
//            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
            'on_discount_title.required_with'=>'The overnight discount title field is required',
            'on_daterange.required_with'=>'The overnight discount date field is required',
            //overland starts
            'ol_wa_range_up.*.required' => 'The overland range up field is required.',
            'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
            'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
            'ol_wa_range_down.*.required' => 'The overland range down field is required.',
            'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
            'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
            'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
            'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
            'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
            'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
            'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
            'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
            'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
            'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
            'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
            'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
            'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
            'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
            'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
            'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
            'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
            'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
            'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
            //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
            'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
            'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
            'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
            'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
            'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
            //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
            'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
            'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
            'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
            'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
            'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
            'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
            'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
            'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'ol_flyer_sm.required_if' => 'The overland small flyer field is required',
            'ol_flyer_sm.numeric' => 'The overland small flyer field must be numeric',
            'ol_flyer_md.required_if' => 'The overland meduim flyer field is required',
            'ol_flyer_md.numeric' => 'The overland medium flyer field must be numeric',
            'ol_flyer_lg.required_if' => 'The overland large flyer field is required',
            'ol_flyer_lg.numeric' => 'The overland large flyer field must be numeric',
            'ol_flyer_box.required_if' => 'The overland box flyer field is required',
            'ol_flyer_box.numeric' => 'The overland box flyer field must be numeric',
            'ol_discount_title.required_if' => 'The overland discount title field must be required',
            'ol_daterange.required_if' => 'The overland discount date range field must be required',
            'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
//            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
            'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
//            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
            'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
//            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
            'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
//            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
            'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
//            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
            'ol_discount_title.required_with'=>'The overland discount title field is required',
            'ol_daterange.required_with'=>'The overland discount date field is required',
            //overland end and detain starts
            'detain_wa_range_up.*.required' => 'The detain range up field is required.',
            'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
            'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
            'detain_wa_range_down.*.required' => 'The detain range down field is required.',
            'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
            'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
            'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
            'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
            'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
            'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
            'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
            'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
            'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
            'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
            'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
            'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
            'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
            'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
            'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
            'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
            'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
            'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
            'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
            //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
            'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
            'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
            'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
            'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
            'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
            //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
            'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
            'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
            'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
            'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
            'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
            'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
            'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
            'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_flyer_sm.required_if' => 'The detain small flyer field is required',
            'detain_flyer_sm.numeric' => 'The detain small flyer field must be numeric',
            'detain_flyer_md.required_if' => 'The detain meduim flyer field is required',
            'detain_flyer_md.numeric' => 'The detain medium flyer field must be numeric',
            'detain_flyer_lg.required_if' => 'The detain large flyer field is required',
            'detain_flyer_lg.numeric' => 'The detain large flyer field must be numeric',
            'detain_flyer_box.required_if' => 'The detain box flyer field is required',
            'detain_flyer_box.numeric' => 'The detain box flyer field must be numeric',
            'detain_discount_title.required_if' => 'The detain discount title field must be required',
            'detain_daterange.required_if' => 'The detain discount date range field must be required',
            'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
//            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
            'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
//            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
            'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
//            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
            'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
//            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
            'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
//            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
            'detain_discount_title.required_with'=>'The detain discount title field is required',
            'detain_daterange.required_with'=>'The detain discount date field is required',
            //detain ends and sameday starts
            'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
            'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
            'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
            'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
            'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
            'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
            'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
            'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
            'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
            'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
            'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
            'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
            'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
            'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
            'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
            'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
            'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
            'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
            'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
            'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
            // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
            'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
            'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
            'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
            'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
            'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
            //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
            'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
            'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
            'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
            'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
            'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
            'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
            'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
            'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',
            'sameday_flyer_sm.required_if' => 'The sameday small flyer field is required',
            'sameday_flyer_sm.numeric' => 'The sameday small flyer field must be numeric',
            'sameday_flyer_md.required_if' => 'The sameday meduim flyer field is required',
            'sameday_flyer_md.numeric' => 'The sameday medium flyer field must be numeric',
            'sameday_flyer_lg.required_if' => 'The sameday large flyer field is required',
            'sameday_flyer_lg.numeric' => 'The sameday large flyer field must be numeric',
            'sameday_flyer_box.required_if' => 'The sameday box flyer field is required',
            'sameday_flyer_box.numeric' => 'The sameday box flyer field must be numeric',
            'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
            'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
            'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
//            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
            'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
//            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
            'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
//            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
            'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
//            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
            'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
//            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
            'sameday_discount_title.required_with'=>'The sameday discount title field is required',
            'sameday_daterange.required_with'=>'The sameday discount date field is required',
            //sameday ends
        ];

        $validations = array();
        $on_validations = array();
        $ol_validations = array();
        $detain_validations = array();
        $sameday_validations = array();

        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $on_validations = [
                'on_wa_range_up.*' => 'required|numeric|between:0,10000',
                'on_wa_range_down.*' => 'required|numeric|between:0,10000',
                'on_wa_local_charges.*' => 'required|numeric',
                'on_class_0_charges.*' => 'required|numeric',
                'on_class_1_charges.*' => 'required',
                'on_class_2_charges.*' => 'required',
                'on_class_3_charges.*' => 'required',
                'on_wa_spkg.*'=>'numeric',
                'on_replacement_charges'=>'required|numeric',
                'on_tnb_charges'=>'required|numeric',
                'on_cash_range_up.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*'=>'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*'=>'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_0_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_1_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_2_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_3_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'overnight_fuel_surcharge'=>'required_if:overnight_fuel_switch,==,on|numeric',
                'on_flyer_sm'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_md'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_lg'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_box'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_discount_title'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_daterange'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_discount_weight_rate'=>'required_if:on_discount_weight_switch,==,on',
                'on_discount_cash_rate'=>'required_if:on_discount_cash_switch,==,on',
                'on_discount_insurance_rate'=>'required_if:on_discount_insurance_switch,==,on',
                'on_discount_return_rate'=>'required_if:on_discount_return_switch,==,on',
                'on_discount_packaging_rate'=>'required_if:on_discount_packaging_switch,==,on'
            ];
        }
        //overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $ol_validations = [
                'ol_wa_range_up.*' => 'required|numeric|between:0,10000',
                'ol_wa_range_down.*' => 'required|numeric|between:0,10000',
                'ol_wa_local_charges.*' => 'required|numeric',
                'ol_class_0_charges.*' => 'required|numeric',
                'ol_class_1_charges.*' => 'required',
                'ol_class_2_charges.*' => 'required',
                'ol_class_3_charges.*' => 'required',
                'ol_wa_spkg.*'=>'numeric',
                'ol_replacement_charges'=>'required|numeric',
                'ol_tnb_charges'=>'required|numeric',
                'ol_cash_range_up.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*'=>'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*'=>'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_0_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_1_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_2_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_3_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'overland_fuel_surcharge'=>'required_if:overland_fuel_switch,==,on|numeric',
                'ol_flyer_sm'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_md'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_lg'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_box'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_discount_title'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_daterange'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_discount_weight_rate'=>'required_if:ol_discount_weight_switch,==,on',
                'ol_discount_cash_rate'=>'required_if:ol_discount_cash_switch,==,on',
                'ol_discount_insurance_rate'=>'required_if:ol_discount_insurance_switch,==,on',
                'ol_discount_return_rate'=>'required_if:ol_discount_return_switch,==,on',
                'ol_discount_packaging_rate'=>'required_if:ol_discount_packaging_switch,==,on',
            ];
        }
        //overland
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $detain_validations = [
                'detain_wa_range_up.*' => 'required|numeric|between:0,10000',
                'detain_wa_range_down.*' => 'required|numeric|between:0,10000',
                'detain_wa_local_charges.*' => 'required|numeric',
                'detain_class_0_charges.*' => 'required|numeric',
                'detain_class_1_charges.*' => 'required',
                'detain_class_2_charges.*' => 'required',
                'detain_class_3_charges.*' => 'required',
                'detain_wa_spkg.*'=>'numeric',
                'detain_replacement_charges'=>'required|numeric',
                'detain_tnb_charges'=>'required|numeric',
                'detain_cash_range_up.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*'=>'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*'=>'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_0_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_1_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_2_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_3_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_fuel_surcharge'=>'required_if:detain_fuel_switch,==,on|numeric',
                'detain_flyer_sm'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_md'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_lg'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_box'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_discount_title'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_daterange'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_discount_weight_rate'=>'required_if:detain_discount_weight_switch,==,on',
                'detain_discount_cash_rate'=>'required_if:detain_discount_cash_switch,==,on',
                'detain_discount_insurance_rate'=>'required_if:detain_discount_insurance_switch,==,on',
                'detain_discount_return_rate'=>'required_if:detain_discount_return_switch,==,on',
                'detain_discount_packaging_rate'=>'required_if:detain_discount_packaging_switch,==,on',
            ];
        }
        //sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $sameday_validations = [
                'sameday_wa_range_up.*' => 'required|numeric|between:0,10000',
                'sameday_wa_range_down.*' => 'required|numeric|between:0,10000',
                'sameday_wa_local_charges.*' => 'required|numeric',
                'sameday_class_0_charges.*' => 'required|numeric',
                'sameday_wa_spkg.*'=>'numeric',
                'sameday_replacement_charges'=>'required|numeric',
                'sameday_tnb_charges'=>'required|numeric',
                'sameday_cash_range_up.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*'=>'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*'=>'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_0_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_1_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_2charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_3_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge'=>'required_if:sameday_fuel_switch,==,on|numeric',
                'sameday_flyer_sm'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_md'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_lg'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_box'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_discount_title'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate'=>'required_if:sameday_discount_weight_switch,==,on',
                'sameday_discount_cash_rate'=>'required_if:sameday_discount_cash_switch,==,on',
                'sameday_discount_insurance_rate'=>'required_if:sameday_discount_insurance_switch,==,on',
                'sameday_discount_return_rate'=>'required_if:sameday_discount_return_switch,==,on',
                'sameday_discount_packaging_rate'=>'required_if:sameday_discount_packaging_switch,==,on',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }

        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $ONRateAlready = RateStatus::where('user_id',$id)->where('shipping_mode_id',1)->get();

            if($ONRateAlready->isEmpty()) {
                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'status'=> ($request->has('on_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('on_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('on_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('on_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('on_packaging_switch'))? 1:0
                ]);
                $wa_switch = array();
                $wa_spkg = array();
                foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                    if($request->has('on_wa_switch')) {
                        if (array_key_exists($index, $request->on_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('on_wa_spkg')) {
                        if (array_key_exists($index, $request->on_wa_spkg)) {
                            $wa_spkg[$index] = $request->on_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0;
                        };
                    }else{
                        $wa_spkg[$index] = 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'range_up' => $request->on_wa_range_up[$index],
                        'range_down' => $request->on_wa_range_down[$index],
                        'weight_addition' => $wa_switch[$index],
                        'spkg' => $wa_spkg[$index],
                        'local_or_6hr' => $request->on_wa_local_charges[$index],
                        'national_charges_class_0' => $request->on_class_0_charges[$index],
                        'national_charges_class_1' => $request->on_class_1_charges[$index],
                        'national_charges_class_2' => $request->on_class_2_charges[$index],
                        'national_charges_class_3' => $request->on_class_3_charges[$index]
                    ]);

                }

                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'replacement_charges'=>$request->on_replacement_charges,
                    'try_and_buy_charges'=>$request->on_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on'){
                    foreach ($request->on_cash_range_up as $ind => $on_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'range_up'=> $request->on_cash_range_up[$ind],
                            'range_down'=> $request->on_cash_range_down[$ind],
                            'charges'=> $request->on_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on'){
                    foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'range_up'=> $request->on_ins_range_up[$insurance],
                            'range_down'=> $request->on_ins_range_down[$insurance],
                            'charges'=> $request->on_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('on_return_switch') && $request->on_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'local'=> $request->on_return_local_charges,
                        'national_charges_class_0'=> $request->on_return_class_0_charges,
                        'national_charges_class_1'=> $request->on_return_class_1_charges,
                        'national_charges_class_2'=> $request->on_return_class_2_charges,
                        'national_charges_class_3'=> $request->on_return_class_3_charges
                    ]);
                }
                //Return Charges
                if($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'fuel_surcharge'=> $request->overnight_fuel_surcharge
                    ]);
                }
                //Packaging Charges
                if($request->has('on_packaging_switch') && $request->on_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'sm_flyer'=> $request->on_flyer_sm,
                        'md_flyer'=> $request->on_flyer_md,
                        'lg_flyer'=> $request->on_flyer_lg,
                        'box_flyer'=> $request->on_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on'){
                    $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                }
                if($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on'){
                    $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                }
                if($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on'){
                    $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'title'=> $request->on_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            //dd($weightAlready);
        }
        //Overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){

            $OLRatePresent = RateStatus::where('user_id',$id)->where('shipping_mode_id',2)->get();

            if($OLRatePresent->isEmpty()) {
                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'status'=> ($request->has('ol_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('ol_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('ol_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('ol_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overland_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('ol_packaging_switch'))? 1:0
                ]);
                $wa_switch_overland = array();
                $wa_spkg_overland = array();
                foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                    if($request->has('ol_wa_switch')) {
                        if (array_key_exists($index, $request->ol_wa_switch)) {
                            $wa_switch_overland[$index] = 1;
                        } else {
                            $wa_switch_overland[$index] = 0;
                        };
                    }else{
                        $wa_switch_overland[$index] = 0;
                    }
                    if($request->has('ol_wa_spkg')) {
                        if (array_key_exists($index, $request->ol_wa_spkg)) {
                            $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                        } else {
                            $wa_spkg_overland[$index] = 0;
                        };
                    }else{
                        $wa_spkg_overland[$index] = 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'range_up' => $request->ol_wa_range_up[$index],
                        'range_down' => $request->ol_wa_range_down[$index],
                        'weight_addition' => $wa_switch_overland[$index],
                        'spkg' => $wa_spkg_overland[$index],
                        'local_or_6hr' => $request->ol_wa_local_charges[$index],
                        'national_charges_class_0' => $request->ol_class_0_charges[$index],
                        'national_charges_class_1' => $request->ol_class_1_charges[$index],
                        'national_charges_class_2' => $request->ol_class_2_charges[$index],
                        'national_charges_class_3' => $request->ol_class_3_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'replacement_charges'=>$request->ol_replacement_charges,
                    'try_and_buy_charges'=>$request->ol_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on'){
                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'range_up'=> $request->ol_cash_range_up[$ind],
                            'range_down'=> $request->ol_cash_range_down[$ind],
                            'charges'=> $request->ol_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on'){
                    foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'range_up'=> $request->ol_ins_range_up[$insurance],
                            'range_down'=> $request->ol_ins_range_down[$insurance],
                            'charges'=> $request->ol_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('ol_return_switch') && $request->ol_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'local'=> $request->ol_return_local_charges,
                        'national_charges_class_0'=> $request->ol_return_class_0_charges,
                        'national_charges_class_1'=> $request->ol_return_class_1_charges,
                        'national_charges_class_2'=> $request->ol_return_class_2_charges,
                        'national_charges_class_3'=> $request->ol_return_class_3_charges
                    ]);
                }
                if($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'fuel_surcharge'=> $request->overland_fuel_surcharge
                    ]);
                }
                //Packaging Charges
                if($request->has('ol_packaging_switch') && $request->ol_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'sm_flyer'=> $request->ol_flyer_sm,
                        'md_flyer'=> $request->ol_flyer_md,
                        'lg_flyer'=> $request->ol_flyer_lg,
                        'box_flyer'=> $request->ol_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on'){
                    $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                }
                if($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on'){
                    $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                }
                if($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on'){
                    $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'title'=> $request->ol_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }

        }
        //Detain
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

            $DetainRatePresent = RateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

            if ($DetainRatePresent->isEmpty()) {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges'=> ($request->has('detain_fuel_switch'))? 1:0,
                    'packaging_charges' => ($request->has('detain_packaging_switch')) ? 1 : 0
                ]);
                $wa_switch_detain = array();
                $wa_spkg_detain = array();
                foreach ($request->detain_wa_range_up as $index => $detain_wa_range_up) {
                    if ($request->has('detain_wa_switch')) {
                        if (array_key_exists($index, $request->detain_wa_switch)) {
                            $wa_switch_detain[$index] = 1;
                        } else {
                            $wa_switch_detain[$index] = 0;
                        };
                    } else {
                        $wa_switch_detain[$index] = 0;
                    }
                    if ($request->has('detain_wa_spkg')) {
                        if (array_key_exists($index, $request->detain_wa_spkg)) {
                            $wa_spkg_detain[$index] = $request->detain_wa_spkg[$index];
                        } else {
                            $wa_spkg_detain[$index] = 0;
                        };
                    } else {
                        $wa_spkg_detain[$index] = 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'range_up' => $request->detain_wa_range_up[$index],
                        'range_down' => $request->detain_wa_range_down[$index],
                        'weight_addition' => $wa_switch_detain[$index],
                        'spkg' => $wa_spkg_detain[$index],
                        'local_or_6hr' => $request->detain_wa_local_charges[$index],
                        'national_charges_class_0' => $request->detain_class_0_charges[$index],
                        'national_charges_class_1' => $request->detain_class_1_charges[$index],
                        'national_charges_class_2' => $request->detain_class_2_charges[$index],
                        'national_charges_class_3' => $request->detain_class_3_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'replacement_charges' => $request->detain_replacement_charges,
                    'try_and_buy_charges' => $request->detain_tnb_charges
                ]);
                //Cash handling Charges
                if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                    foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_cash_range_up[$ind],
                            'range_down' => $request->detain_cash_range_down[$ind],
                            'charges' => $request->detain_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                    foreach ($request->detain_ins_range_up as $insurance => $detain_ins_range_up) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_ins_range_up[$insurance],
                            'range_down' => $request->detain_ins_range_down[$insurance],
                            'charges' => $request->detain_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                    ReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'local' => $request->detain_return_local_charges,
                        'national_charges_class_0'=> $request->detain_return_class_0_charges,
                        'national_charges_class_1'=> $request->detain_return_class_1_charges,
                        'national_charges_class_2'=> $request->detain_return_class_2_charges,
                        'national_charges_class_3'=> $request->detain_return_class_3_charges
                    ]);
                }
                if($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'fuel_surcharge'=> $request->detain_fuel_surcharge
                    ]);
                }
                //Packaging Charges
                if ($request->has('detain_packaging_switch') && $request->detain_packaging_switch == 'on') {
                    PackagingCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'sm_flyer' => $request->detain_flyer_sm,
                        'md_flyer' => $request->detain_flyer_md,
                        'lg_flyer' => $request->detain_flyer_lg,
                        'box_flyer' => $request->detain_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                    $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : null;
//                    $discount_weight = $request->detain_discount_weight_rate;
                }
                if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                    $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : null;
                }
                if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                    $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                    $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                    $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : null;
                }
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->detain_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'title' => $request->detain_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

            }

        }
        //Sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){

            $SamedayRatePresent = RateStatus::where('user_id',$id)->where('shipping_mode_id',4)->get();
            if($SamedayRatePresent->isEmpty()) {
                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'status'=> ($request->has('sameday_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('sameday_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('sameday_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('sameday_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('sameday_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('sameday_packaging_switch'))? 1:0
                ]);
                $wa_switch_sameday = array();
                $wa_spkg_sameday = array();
                foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                    if($request->has('sameday_wa_switch')) {
                        if (array_key_exists($index, $request->sameday_wa_switch)) {
                            $wa_switch_sameday[$index] = 1;
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        };
                    }else{
                        $wa_switch_sameday[$index] = 0;
                    }
                    if($request->has('sameday_wa_spkg')) {
                        if (array_key_exists($index, $request->sameday_wa_spkg)) {
                            $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                        } else {
                            $wa_spkg_sameday[$index] = 0;
                        };
                    }else{
                        $wa_spkg_sameday[$index] = 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'range_up' => $request->sameday_wa_range_up[$index],
                        'range_down' => $request->sameday_wa_range_down[$index],
                        'weight_addition' => $wa_switch_sameday[$index],
                        'spkg' => $wa_spkg_sameday[$index],
                        'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                        'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                        'national_charges_class_1' => 0,
                        'national_charges_class_2' => 0,
                        'national_charges_class_3' => 0
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'replacement_charges'=>$request->sameday_replacement_charges,
                    'try_and_buy_charges'=>$request->sameday_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on'){
                    foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'range_up'=> $request->sameday_cash_range_up[$ind],
                            'range_down'=> $request->sameday_cash_range_down[$ind],
                            'charges'=> $request->sameday_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on'){
                    foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'range_up'=> $request->sameday_ins_range_up[$insurance],
                            'range_down'=> $request->sameday_ins_range_down[$insurance],
                            'charges'=> $request->sameday_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'local'=> $request->sameday_return_local_charges,
                        'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                        'national_charges_class_1'=> 0,
                        'national_charges_class_2'=> 0,
                        'national_charges_class_3'=> 0
                    ]);
                }
                if($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'fuel_surcharge'=> $request->sameday_fuel_surcharge
                    ]);
                }
                //Packaging Charges
                if($request->has('sameday_packaging_switch') && $request->sameday_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'sm_flyer'=> $request->sameday_flyer_sm,
                        'md_flyer'=> $request->sameday_flyer_md,
                        'lg_flyer'=> $request->sameday_flyer_lg,
                        'box_flyer'=> $request->sameday_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on'){
                    $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                }
                if($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on'){
                    $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                }
                if($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on'){
                    $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'title'=> $request->sameday_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
        }
        User::where('id',$id)->update(['status'=>1,'rates_added_by'=>Auth::id()]);


        return redirect(route('admin.accounts.pending'))->with('success','All Rates are added');
    }

    public function activeAccountListAjax(){		$users = User::join('cities', 'users.city_id', '=', 'cities.id')
           ->leftjoin('products as p','p.id','=','users.product_id')
           ->leftjoin('admins as rab','rab.id','=','users.rates_added_by')
           ->leftjoin('admins as rabna','rabna.id','=','users.rates_updated_by')
           ->leftjoin('admins as rabb','rabb.id','=','users.rates_authorized_by')
           ->leftjoin('admins as rabba','rabba.id','=','users.account_activated_by')
           ->leftjoin('sale_person_tags as spt', function ($join) {
               $join->on('spt.user_id', '=', 'users.id')
                   ->leftjoin('admins as ad','ad.id','=','spt.admin_id')
                   ->where('spt.status','=',0);
           })
           ->select(['users.disable_remarks as disable_remarks','users.rejected_reason as rejected_reason','users.rate_status as rate_status','users.id','ad.name as admin_tag_id', 'users.name','cities.name as city' ,'users.poc','users.phone','users.address', 'users.email','p.product_name as product_type','rab.name as added_by','rabna.name as updated_by','users.created_at','rabb.name as approved_by','rabba.name as account_activated_by','users.activated_at as activated_date','users.status','users.account_type_id'])->whereIn('users.status',[3,4])->where('blacklist',0);

        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }

        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }

        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('rate_status',function ($users){
                if($users->rate_status == 0){
                    return "Approved";
                }elseif($users->rate_status == 1){
                    return "Requested";
                }
                else{
                    return "Rejected";
                }
            })->editColumn('disable_remarks',function ($users){
                if($users->disable_remarks != null){
                    return $users->disable_remarks;
                }
                else{
                    return "-";
                }
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('status',function ($users){
                if($users->status == 3){
                    return "Enable";
                }else{
                    return "Disable";
                }
            })
            ->editColumn('rejected_reason',function ($users){
                if($users->rejected_reason != null && $users->rate_status==2){
                    return $users->rejected_reason;
                }else{
                    return "-";
                }
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword == 3 || $keyword == 4) {
                    $query->where('users.status', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('product_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('p.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';
                if($result->status == 3 && (session('role_id') == 1 || session('role_id') == 4))
                {
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#SalesTagModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Sales Person</div></button>';
                }
                if($result->account_type_id == 1){
                    if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.edit.rates', ['id'=> $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                    }
                    if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.view.rates', ['id'=> $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                    }
                }
                else{
                    if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id'=> $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                    }
                    if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.view.rates', ['id'=> $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                    }
                }
                if ($result->blacklist == 0 && (session('role_id') == 1 || in_array(14, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">Block</div></button>';
                }

                if (session('role_id') == 1 || in_array(13, session('permissions'))) {
                    if ($result->status == 3) {
                        $dropdown .= '<button type="button" class="dropdown-item userdisable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-minus"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    }
                    else {
                        $dropdown .= '<button type="button" class="dropdown-item userenable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus"></i></div><div class="col-9 offset-1">Enable</div></button>';

                    }
                }

                if(session('role_id') == 1 || in_array(110, session('permissions')))
                {
                    $dropdown .= '<button onclick="location.href=\'' . route('admin.accounts.view.profile', ['id'=> $result->id]) . '\'" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                }

                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;
            })
            ->make(true);

    }


    public function pendingAccountListAjax(){

        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('products','products.id','=','users.product_id')
            ->leftjoin('admins as rab','rab.id','=','users.rates_added_by')
            ->leftjoin('admins as rabb','rabb.id','=','users.rates_authorized_by')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad','ad.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->select(['users.rate_status as rate_status','users.rejected_reason as rejected_reason','users.id','ad.name as admin_tag_id', 'users.name', 'cities.name as city' ,'users.poc','users.phone','users.address','users.status', 'users.email','users.created_at','products.product_name as product_type','users.blacklist','rab.name as rates_added_by','rabb.name as rates_authorized_by','users.account_type_id'])->whereIn('users.status',[0,1,2])->where('blacklist',0);

        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('rejected_reason',function ($users){
                if($users->rejected_reason != null && $users->rate_status==2){
                    return $users->rejected_reason;
                }else{
                    return "-";
                }
            })
            ->editColumn('rate_status',function ($users){
                if($users->rate_status == 2) {
                    return "Rejected";
                }
                else if($users->rate_status == 1) {
                    return "Requested";
                }
                else if($users->rate_status == 0 && $users->status==2){
                    return "Authorized";
                }
                else if($users->rate_status == 0 && $users->status==1) {
                    return "Requested";
                }
            })
            ->editColumn('status', function ($users) {
                return $users->status == 0? 'Request Received': ($users->status == 1? 'Rates Added' : ($users->status == 2? 'Pending for Activation':''));
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword == 0 || $keyword == 1 || $keyword == 2) {
                    $query->where('users.status', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('product_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('products.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $sale_check= SalePersonTag::where('user_id',$result->id)->first();
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';
                if(session('role_id') == 1 || ($result->status == 0 && session('role_id') == 4))
                {
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#SalesTagModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Sales Person</div></button>';
                }
                if($result->status == 2 && (session('role_id') == 1 || in_array(9, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item active_account" rel="activate" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Account</div></button>';

                }
                if($sale_check != null && $result->status != 2) {
                    if($result->account_type_id == 1){
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions')))) {
                            if($result->status != 2) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.add.rates', ['id' => $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                            }
                        } else {
                            if (session('role_id') == 1 || in_array(6, session('permissions'))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.edit.rates', ['id' => $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                    }else{
                        if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions')))) {
                            if($result->status == 0) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.add.rates', ['id' => $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                            }
                        } else {
                            if (session('role_id') == 1 || in_array(6, session('permissions'))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                    }


                }
                if($result->account_type_id == 1){
                    if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.view.rates', ['id' => $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                    }
                }else{
                    if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.view.rates', ['id' => $result->id]) . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                    }
                }
                if($result->blacklist == 0 && (session('role_id') == 1 || in_array(10, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">Block</div></button>';
                }


                if(session('role_id') == 1 || in_array(110, session('permissions')))
                {
                    $dropdown .= '<button onclick="location.href=\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\'" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                }

                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;
            })
            ->make(true);

    }
    public function blockAccountListAjax(){
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->select(['users.id', 'users.name', 'cities.name as city' ,'users.poc','users.phone','users.address', 'users.email','users.blacklist_reason as reason'])->where('blacklist',1);

        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->addColumn("action", function ($result) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';

                if (session('role_id') == 1 || in_array(16, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="unblock"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus "></i></div><div class="col-9 offset-1">Unblock</div></button>';
                }


                if(session('role_id') == 1 || in_array(110, session('permissions')))
                {
                    $dropdown .= '<button onclick="location.href=\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\'" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                }

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->make(true);

    }

    //User Profile Methods

    public function userProfile($id)
    {
        $user = User::find($id);
        $product = Product::find($user->product_id);
        $products = Product::all();
        $banks = BanksList::all();
        $invoicing_cycle = InvoicingCycle::all();
        $city_list = City::where('status',1)->get();
        $emails = ShipperNotificationEmail::where('user_id',$user->id)->select('email')->get();
        $email_ids = ShipperNotificationEmail::where('user_id',$user->id)->pluck('email')->toArray();
        $email_ids = implode(',', $email_ids);
        return view('admin.accounts.profile')->with(['user'=>$user,'product_name'=>$product->product_name,'banks'=>$banks,'all_cities'=>$city_list,'products'=>$products,'invoicing_cycle' => $invoicing_cycle , 'emails' => $emails, 'email_ids' => $email_ids]);
    }


    public function updateProfile(Request $request)
    {

        $user_id = $request->user_id;

        //1 for Admin, 0 for User

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'address'=>'required|string|max:255',
            'poc'=>'required|string|max:255',
            'phone'=>'required|string|max:255',
            'cnic'=>'required|string|max:255',
        ]);


        if($request->password=="" || $request->password==null)
        {
            User::where('id',$user_id)->update(['name'=>$request->name,'poc'=>$request->poc,'email'=>$request->email,'address'=>$request->address,'phone'=>$request->phone,'phone2'=>$request->phone2,'cnic'=>$request->cnic,
                'ntn_no'=>$request->ntn_no,'updated_by_type'=>1,'updated_by_id'=>Auth::id(),'city_id'=>$request->city_id,
                'url'=>$request->url,'product_id'=>$request->product_id]);
            AdminLogs::create([
                'admin_id'=>Auth::id(),
                'user_id'=>$user_id

            ]);
        }
        else
        {
            User::where('id',$user_id)->update(['name'=>$request->name,'poc'=>$request->poc,'email'=>$request->email,'address'=>$request->address,'phone'=>$request->phone,'phone2'=>$request->phone2,'cnic'=>$request->cnic,
                'ntn_no'=>$request->ntn_no,"password"=>Hash::make($request->password),'updated_by_type'=>1,'updated_by_id'=>Auth::id(),'city_id'=>$request->city_id,
                'url'=>$request->url,'product_id'=>$request->product_id]);
        }

        return redirect()->back()->with(['success'=>"Profile Information Successfully Updated"]);
    }

    public function updateBankInfo(Request $request)
    {

        $user_id = $request->user_id;
        $user = User::find($user_id);
        //1 for Admin, 0 for User

        $request->validate([
            'bank_name'=>'required|max:255',
            'bank_branch'=>'required|string|max:255',
            'account_no'=>'required|string|max:255',
            'account_title'=>'required|string|max:255',
            'iban'=>'required|string|max:255',
            'payment_cycle'=>'required|string|max:255'
        ]);


        if($user->account_type_id == 1){
            UserBankInfo::where('user_id',$user_id)->update(['bank_branch'=>$request->bank_branch,'bank_name'=>$request->bank_name,'account_no'=>$request->account_no,
                'account_title'=>$request->account_title,'iban'=>$request->iban,'city_id'=>$request->bank_city,'payment_cycle'=>$request->payment_cycle]);

        }else{
            $generation_date = null;
            if($request->invoicing_cycle_id == 2){
                $generation_date = null;
            }else{
                $generation_date = $request->generation_date;
            }
            UserBankInfo::where('user_id',$user_id)->update([
                    'bank_branch'=>$request->bank_branch,
                    'bank_name'=>$request->bank_name,
                    'account_no'=>$request->account_no,
                    'account_title'=>$request->account_title,
                    'iban'=>$request->iban,
                    'city_id'=>$request->bank_city,
                    'payment_cycle'=>$request->payment_cycle,
                    'invoicing_cycle_id' => $request->invoicing_cycle_id,
                    'generation_date' => $generation_date,
                    'billing_person_name' => $request->billing_person_name,
                    'billing_person_phone' => $request->billing_person_phone,
                    'billing_person_email' => $request->billing_person_email,
                    'billing_address' => $request->billing_address
            ]);
        }
        AdminLogs::create([
            'admin_id' => Auth::id(),
            'user_id' => $user_id
        ]);
        return redirect()->back()->with(['success'=>"Bank Information Successfully Updated"]);
    }



    public function getPickups(Request $request)
    {
        $pickups = UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
            ->select(['user_shipping_infos.id as id','user_shipping_infos.pickup_address as pickup_address','user_shipping_infos.poc as poc','user_shipping_infos.phone as phone','user_shipping_infos.email as email','user_shipping_infos.status as status','user_shipping_infos.default_address as default_address','user_shipping_infos.user_id as user_id','c.name as city_name'])
            ->where('user_id',$request->user_id)
            ->where('hidden', 0);

        return Datatables::of($pickups)
            ->addColumn("status", function ($result) {
                if($result->default_address==1)
                {
                    $status =  "Default Address";
                }
                elseif($result->status==1)
                {
                    $status =  "Enabled";
                }
                elseif($result->status==0)
                {
                    $status =  "Disabled";
                }
                return $status;
            })
            ->filterColumn('status',function($query,$keyword){
                if ($keyword != '') {
                    if($keyword == 2){
                        $query->where('user_shipping_infos.default_address',1);
                    }else{
                        $query->where('user_shipping_infos.status',$keyword);
                    }
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }


    public function cityView(Request $request){
//        $req = $request->route();
//        $uri_path = $req->getPath();
//        $uri_parts = explode('/', $uri_path);
//        $uri_tail = end($uri_parts);
//        return $uri_tail;
//        $hubs = City::where('hub',1)->get();
//        return $hubs[0]->id;
        return view('admin.management.city_management');
    }
    public function cityListAjax(){
        $cities = City::join('cities as h' ,'cities.hub_id', '=' , 'h.id')
            ->join('zones as z', 'cities.zone_id', '=', 'z.id')
            ->select(['cities.id as city_id','cities.name as name' ,'h.name as hub','cities.hub_id','z.name as zone','cities.hub as isHub','cities.status as status']);

        return Datatables::of($cities)
            ->editColumn('status', function ($cities) {
                return ($cities->status == 1)? 'Active': 'Inactive';
            })
//        ->filterColumn('status', function($query, $keyword) {
//            $keyword = strtolower($keyword);
//
//            if (strpos('active', $keyword) !== FALSE) {
//                $query->where('cities.status', '=', 1);
//            }
//            else if (strpos('inactive', $keyword) !== FALSE) {
//                $query->where('cities.status', '=', 0);
//            }
//            else {
//                $query->whereRaw('false');
//            }
//        })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([90, 91], session('permissions'))) !== 0) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                    if (session('role_id') == 1 || in_array(90, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->city_id . ' rel="editcity" data-toggle="modal" data-target="#editCity"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update City Status</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(91, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->city_id . ' rel="cityInactive" hub=' . $result->isHub . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate City</div></button>';
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->city_id . ' rel="cityactive" hub=' . $result->isHub . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate City</div></button>';
                        }
                    }

                    $dropdown .= '
                    </div>
                  </div>
                ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }

    public function getCityForm(){
        $hubs = City::where('hub',1)->where('status',1)->get();
        $zones = Zone::all();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::where('id','!=',4)->get();
        return view('admin.management.add_city_form')->with(['hubs'=>$hubs,'zones' => $zones, 'shippingMode'=>$shippingMode,'bookings'=>$booking]);
    }
    public function getEditCityForm($id){
//        return $id;
        $city = City::find($id);
        if($city->hub == 1){
            $cityhub = '';
            $isHub = 1;
        }else{
            $cityhub = City::select(['id','name'])->where('id',$city->hub_id)->get();
            $isHub = 0;

        }
        $delivery_array = CityDelivery::where('city_id',$city->id)->select(['booking_type_id','shipping_mode_id'])->get();
//        $delivery_row_id = CityDelivery::where('city_id',$city->id)->select('id')->get();
        $delivery = array();
        foreach ($delivery_array as $delivery_details) {
            $delivery[$delivery_details['booking_type_id']][] = $delivery_details['shipping_mode_id'];
        }


        $hubs = City::where('hub',1)->where('status',1)->get();
        $zones = Zone::all();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::where('id','!=',4)->get();
        $walk_in_city = WalkInCities::where('city_id',$city['id'])->first();
        return view('admin.management.edit_city_form')->with(['hubs'=>$hubs, 'zones' => $zones, 'shippingMode'=>$shippingMode,'bookings'=>$booking,'isHub'=>$isHub,'city'=>$city,'delivery'=>$delivery,'cityhub'=>$cityhub, 'walk_in_city' => $walk_in_city]);

    }

    public function updateCity(Request $request,$id){
        if($request->postType == 'city'){

            City::where('id',$id)->update([
                'name'=>$request->cityName,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=>City::find($request->hubs)->zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1
            ]);

            $walk_in_city = WalkInCities::where('city_id',$id)->update([
                'pickup'=>($request->has('walk_in_pickup'))? 1:0,
                'delivery'=>($request->has('walk_in_delivery'))? 1:0,
            ]);

            CityDelivery::where('city_id',$id)->delete();

            foreach ($request->updatedelivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }

            return redirect()->back()->with('success','City updated successfully');
        }elseif($request->postType == 'hub'){
            City::where('id',$id)->update([
                'name'=>$request->cityName,
                'hub'=>1,
                'hub_id'=>$id,
                'zone_id'=>$request->zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1
            ]);

            $walk_in_city = WalkInCities::where('city_id',$id)->update([
                'pickup'=>($request->has('walk_in_pickup'))? 1:0,
                'delivery'=>($request->has('walk_in_delivery'))? 1:0,
            ]);

            CityDelivery::where('city_id',$id)->delete();

            foreach ($request->updatedelivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }

            return redirect()->back()->with('success','Hub/city updated successfully');
        }
    }
    //update city end
    public function addCityHub(Request $request){

        if($request->postType == 'city'){

            $city = City::create([
                'name'=>$request->cityName,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=>City::find($request->hubs)->zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1
            ]);

            $walk_in_city = WalkInCities::create([
                'city_id'=>$city->id,
                'pickup'=>($request->has('walk_in_pickup'))? 1:0,
                'delivery'=>($request->has('walk_in_delivery'))? 1:0,
            ]);

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$city->id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }

            return redirect()->back()->with('success','City added successfully');
        }elseif($request->postType == 'hub'){
            $city = City::create([
                'name'=>$request->cityName,
                'hub'=>1,
                'zone_id'=>$request->zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1
            ]);

            $walk_in_city = WalkInCities::create([
                'city_id'=>$city->id,
                'pickup'=>($request->has('walk_in_pickup'))? 1:0,
                'delivery'=>($request->has('walk_in_delivery'))? 1:0,
            ]);

            City::where('id',$city->id)->update(['hub_id'=>$city->id]);

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$city->id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }
            return redirect()->back()->with('success','Hub city added successfully');
        }
    }

    public function CityStatus(Request $request){
        $id = $request->cid; //city id
        $status = $request->status;
        if($status == 'cityInactive'){
            $city = City::find($id);
            if($city->status == 1 && $city->hub == 1){
                $citylist = City::where('hub_id',$id)->where('id','!=',$id)->get();
                if(count($citylist) > 0){

                    return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
                }elseif (count($citylist) == 0){
                    $action = City::where('id',$city->id)->update(['status'=>0]);
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }
            }elseif ($city->status == 1){
                $action = City::where('id',$city->id)->update(['status'=>0]);
                if($action == 1){
                    return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
                }else{
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }

            }
        }elseif($status == 'cityactive'){
            $city = City::find($id);
            if($city->status == 0){
                $action = City::where('id',$city->id)->update(['status'=>1]);
                if($action == 1){
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');
                }else{
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }
            }else{
                return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');

            }
        }
        return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');
    }

    public function CityStatusCheck($id){
        $hubs = City::select('name')->where('hub_id',$id)->where('id','!=',$id)->get();

        return response()->json($hubs);
    }
    //route management
    public function routeView(){
        return view('admin.management.route_management');
    }
    public function routeListAjax(){
        $routes = Route::join('cities','routes.city_id','=','cities.id')
            ->select(['cities.name as city','routes.id','routes.code as code','routes.start','routes.end','routes.junction','routes.status as status','routes.created_at']);

        if (session('role_id') != 1) {
            $routes = $routes->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($routes)
            ->editColumn('status', function ($routes) {
                return ($routes->status == 0)? 'Inactive': 'Active';
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('active', $keyword) !== FALSE) {
                    $query->where('routes.status', '=', 1);
                }
                else if (strpos('inactive', $keyword) !== FALSE) {
                    $query->where('routes.status', '=', 0);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([94, 95], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(94, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->id . ' rel="editroute" data-toggle="modal" data-target="#editRoute"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Route</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(95, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Route</div></button>';
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Route</div></button>';
                        }
                    }

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function addRouteView(){
        $city = City::select(['id','name'])->where('status',1)->get();
        return view('admin.management.add_route_form')->with('cities',$city);
    }
    public function addRouteDetails(Request $request){
//        return $request;
        $validations = [
            'city_id'=>'required|numeric',
            'route_code'=>'required',
            'start'=>'required',
            'end'=>'required',
            'junction'=>'required'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        Route::create([
            'city_id'=>$request->city_id,
            'code'=>$request->route_code,
            'start'=>$request->start,
            'end'=>$request->end,
            'junction'=>$request->junction,
            'status'=>1
        ]);
        return redirect()->back()->with('success','Route added successfully');
    }
    public function editRouteView($id){
        $citylist = City::select(['id','name'])->where('status',1)->get();
        $route = Route::find($id);

        return view('admin.management.edit_route_form')->with(['route_id'=>$id,'cities'=>$citylist,'route'=>$route]);
    }
    public function editRouteDetails(Request $request, $id){
        $validations = [
            'city_id'=>'required|numeric',
            'route_code'=>'required',
            'start'=>'required',
            'end'=>'required',
            'junction'=>'required'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        Route::where('id',$id)->update([
            'city_id'=>$request->city_id,
            'code'=>$request->route_code,
            'start'=>$request->start,
            'end'=>$request->end,
            'junction'=>$request->junction,
            'status'=>1
        ]);
        return redirect()->back()->with('success','Route updated successfully');
    }
    public function routeStatus(Request $request){
        $id = $request->cid;
        $status = $request->status;
//        return $id;
        if($status == 'routeActive'){
            $route = Route::where('id',$id)->update(['status'=>1]);
            if($route){
                return redirect()->back()->with('success','Route is activated successfully');
            }
        }else if($status == 'routeInactive'){
            Route::where('id',$id)->update(['status'=>0]);
            return redirect()->back()->with('success','Route is now inactive');

        }

    }
    public function riderView(){
        $category = RiderCategory::all();
        return view('admin.management.rider_management')->with(['categories'=>$category]);
    }
    public function riderListAjax(){
        $rider = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('routes','routes.id','=','riders.route_id')
            ->join('rider_categories','rider_categories.id','=','riders.rider_category_id')
            ->select(['cities.name as city','riders.id as rider_id','riders.id','riders.name as rider','riders.phone','riders.cnic','riders.address','routes.code as route','routes.start','routes.end','rider_categories.name as category','riders.status as status','riders.created_at']);

        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0)? 'Inactive': 'Active';
            })

            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if (session('role_id') == 1 || count(array_intersect([98, 99], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(98, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $rider->id . ' rel="editRider" data-toggle="modal" data-target="#editRider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Rider</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                        if ($rider->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                        }
                    }

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function addRiderView(){
        $city = City::select(['id','name'])->where('status',1)->get();
        $category = RiderCategory::all();
        return view('admin.management.add_rider_form')->with(['cities'=>$city,'categories'=>$category]);
    }
    public function categoryListAjax(Request $request){
        $city_id = $request->id;
        $route = Route::select(['id','code','start','end'])->where('city_id',$city_id)->where('status',1);

        if (session('role_id') != 1) {
            $route = $route->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $route = $route->get();

        return response()->json($route);
    }
    public function addRiderDetails(Request $request){

        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'rider_category'=>'required|numeric'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $rider = Rider::create([
            'city_id'=>$request->city_id,
            'name'=>$request->rider_name,
            'phone'=>$request->phone,
            'cnic'=>$request->cnic,
            'address'=>$request->address,
            'route_id'=>$request->route_id,
            'rider_category_id'=>$request->rider_category,
            'status'=>1
        ]);
        if($rider){
            return redirect()->back()->with('success','Rider added successfully');
        }

    }
    public function editRiderView($id){
        $city = City::select(['id','name'])->where('status',1)->get();
        $category = RiderCategory::all();
        $rider = Rider::find($id);
        $route = Route::where('city_id',$rider->city_id)->get();
        return view('admin.management.edit_rider_form')->with(['rider_id'=>$id,'cities'=>$city,'categories'=>$category,'rider'=>$rider,'routes'=>$route]);
    }
    public function editRiderDetails(Request $request,$id){
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'rider_category'=>'required|numeric'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $rider = Rider::where('id',$id)->update([
            'city_id'=>$request->city_id,
            'name'=>$request->rider_name,
            'phone'=>$request->phone,
            'cnic'=>$request->cnic,
            'address'=>$request->address,
            'route_id'=>$request->route_id,
            'rider_category_id'=>$request->rider_category
        ]);
        if($rider){
            return redirect()->back()->with('success','Rider updated successfully');
        }
    }

    public function riderStatus(Request $request){
        $id = $request->cid;
        $status = $request->status;
        if($status == 'riderActive'){
            $rider = Rider::where('id',$id)->update(['status'=>1]);
            if($rider){
                return redirect()->back()->with('success','Rider is activated successfully');
            }
        }else if($status == 'riderInactive'){
            $rider =Rider::where('id',$id)->update(['status'=>0]);
            if($rider){
                return redirect()->back()->with('success','Route is now inactive');
            }

        }

    }

    public function add_notification_emails(Request $request){
        $emails = $request->email_address;
        if($emails != ''){
            $email_address = explode(',', $emails);
            $user = $request->add_shipper_id;
            ShipperNotificationEmail::where('user_id',$user)->delete();
            foreach ($email_address as $email){

                $shipper_notification_email = new ShipperNotificationEmail();
                $shipper_notification_email->user_id = $user;
                $shipper_notification_email->email = $email;
                $shipper_notification_email->save();

            }
            return redirect()->back()->with('success', 'Email Address Added.');

        }
        else{
            return back()->with('danger', 'There is no email selected!');
        }
    }
    public function edit_notification_emails(Request $request){

        $emails = $request->email_address;
        if($emails != ''){
            $email_address = explode(',', $emails);
            $user = $request->edit_shipper_id;
            foreach ($email_address as $email){
                if(!ShipperNotificationEmail::where('user_id',$user)->where('email','=',$email)->exists()){
                    $shipper_notification_email = new ShipperNotificationEmail();
                    $shipper_notification_email->user_id = $user;
                    $shipper_notification_email->email = $email;
                    $shipper_notification_email->save();
                }
            }
            ShipperNotificationEmail::where('user_id',$user)->whereNotIn('email',$email_address)->delete();
            return redirect()->back()->with('success', 'Email Address updated.');

        }
        else{
            return back()->with('danger', 'There is no email selected!');
        }
    }
}

