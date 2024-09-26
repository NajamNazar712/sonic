<?php

namespace App\Http\Controllers\Admins;
use App\Http\Models\HR\Employee;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Models\Segment;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
use App\DailyVisit;
use Illuminate\Support\Facades\Log;
use PHPExcel_Style_Fill;
use App\Http\Models\City;
use App\Http\Models\Region;
use App\Http\Models\ZoneRegion;
use App\Http\Models\Zone;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Models\CityArea;
use App\Http\Models\CrmAgent;
use App\Http\Models\Shipment;
use App\Http\Models\BanksList;
use App\RiderWiseDeliveryNote;
use App\Http\Models\WeightType;
use App\Http\Models\Admin\Admin;
use Yajra\Datatables\Datatables;
use App\Http\Models\CRM\CRMCount;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\AgentDay;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\InsuranceCharge;
use App\SpecialApprovalRequestAdmin;
use function GuzzleHttp\Promise\all;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Validator;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\BusinessCategory;
use App\Http\Models\MultipleSaleLead;
use App\Http\Models\ShipmentsJourney;
use App\RiderWiseDeliveryNoteSummary;
use App\Http\Models\Admin\AgentDayLog;
use App\RiderWiseDeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\SubCategorySegment;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\Admin\OSAChargesLog;
use App\Http\Models\Admin\SalePersonTag;
use Illuminate\Support\Facades\Response;
use App\Http\Models\Admin\FintechCompany;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequestRating;
use App\Http\Models\ShipmentStatusReason;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\ReturnRevertLog;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\StationRecoveryReport;
use App\Http\Models\V2Pickup\V2PickupNote;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Http\Models\Admin\DailyVisitRating;
use App\Http\Models\CRM\CrmRequestFeedback;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\ShipmentScanningJourney;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\TraxPayTransaction;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\Excel_reports\Debriefing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Http\Models\Admin\AgentCallMonitoring;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\Admin\FintechPaymentDetails;
use App\Http\Models\Admin\MasterCargo\BagStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Models\StationRecoveryReportDeposit;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\Admin\MonthClosingResponsible;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\Admin\OrdinaryDiscrepancyReport;
use App\Http\Models\CorporateDefaultInsuranceCharge;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
// use App\Http\Controllers\Admins\AdminReportsController;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransaction;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\IssueSackBagOrigin;
use App\Http\Models\Admin\OneLink\OneLinkOutForDeliveryShipmentPayment;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Traits\RvTrait;
use App\RvAgentCallHistory;
use DateTime;
use App\Http\Models\Notification;

class AdminRvReportsController extends Controller
{
    use RvTrait;
    public function __construct()
    {
        $this->middleware('auth:admin');

        // $this->middleware('Permission');
    }

    public function botRvCallRecord(){
        return view('admin.reports.bot_rvr.index');
    }

    public function botRvCallRecordList(){
        $rv_report = RvShipmentAssignAgentDetails::where('agent_id',4620)->get();
    }
}
