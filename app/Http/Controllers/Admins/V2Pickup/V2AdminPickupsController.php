<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\Http\Models\Admin\BookingSmsForShippers;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\ShipmentsEstimatedWeight;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\City;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\InternationalShipment;
use App\Http\Models\PickupAction;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\Shipper\User;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestRiderStatus;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\V2Pickup\V2PickupRequestStatus;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\V2Pickup\V2RiderPickupActionLog;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class V2AdminPickupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }
    public function pending_index()
    {
        if (Auth::id() == 3) {
    ShipmentChargesController::fuel_surcharge(17157579);
ShipmentChargesController::fuel_surcharge(17154829);
ShipmentChargesController::fuel_surcharge(17166169);
ShipmentChargesController::fuel_surcharge(17140089);
ShipmentChargesController::fuel_surcharge(17068990);
ShipmentChargesController::fuel_surcharge(17144998);
ShipmentChargesController::fuel_surcharge(17161812);
ShipmentChargesController::fuel_surcharge(17164501);
ShipmentChargesController::fuel_surcharge(17163208);
ShipmentChargesController::fuel_surcharge(17167283);
ShipmentChargesController::fuel_surcharge(17145095);
ShipmentChargesController::fuel_surcharge(17162036);
ShipmentChargesController::fuel_surcharge(17173427);
ShipmentChargesController::fuel_surcharge(17154763);
ShipmentChargesController::fuel_surcharge(17145039);
ShipmentChargesController::fuel_surcharge(17164315);
ShipmentChargesController::fuel_surcharge(17136479);
ShipmentChargesController::fuel_surcharge(17163197);
ShipmentChargesController::fuel_surcharge(17162239);
ShipmentChargesController::fuel_surcharge(17163199);
ShipmentChargesController::fuel_surcharge(17150221);
ShipmentChargesController::fuel_surcharge(17162596);
ShipmentChargesController::fuel_surcharge(17162130);
ShipmentChargesController::fuel_surcharge(17126478);
ShipmentChargesController::fuel_surcharge(17150009);
ShipmentChargesController::fuel_surcharge(17145067);
ShipmentChargesController::fuel_surcharge(17148536);
ShipmentChargesController::fuel_surcharge(17169027);
ShipmentChargesController::fuel_surcharge(17147301);
ShipmentChargesController::fuel_surcharge(17148509);
ShipmentChargesController::fuel_surcharge(17166314);
ShipmentChargesController::fuel_surcharge(17161850);
ShipmentChargesController::fuel_surcharge(17148533);
ShipmentChargesController::fuel_surcharge(17126075);
ShipmentChargesController::fuel_surcharge(17166398);
ShipmentChargesController::fuel_surcharge(17149994);
ShipmentChargesController::fuel_surcharge(17155931);
ShipmentChargesController::fuel_surcharge(17166963);
ShipmentChargesController::fuel_surcharge(17106391);
ShipmentChargesController::fuel_surcharge(17168481);
ShipmentChargesController::fuel_surcharge(17169475);
ShipmentChargesController::fuel_surcharge(17168603);
ShipmentChargesController::fuel_surcharge(17145217);
ShipmentChargesController::fuel_surcharge(17167664);
ShipmentChargesController::fuel_surcharge(17162485);
ShipmentChargesController::fuel_surcharge(17167696);
ShipmentChargesController::fuel_surcharge(17169443);
ShipmentChargesController::fuel_surcharge(17145459);
ShipmentChargesController::fuel_surcharge(17169250);
ShipmentChargesController::fuel_surcharge(17167822);
ShipmentChargesController::fuel_surcharge(17170860);
ShipmentChargesController::fuel_surcharge(17169544);
ShipmentChargesController::fuel_surcharge(17159973);
ShipmentChargesController::fuel_surcharge(17145336);
ShipmentChargesController::fuel_surcharge(17157363);
ShipmentChargesController::fuel_surcharge(17126060);
ShipmentChargesController::fuel_surcharge(17167186);
ShipmentChargesController::fuel_surcharge(17119349);
ShipmentChargesController::fuel_surcharge(17126108);
ShipmentChargesController::fuel_surcharge(17106627);
ShipmentChargesController::fuel_surcharge(17148428);
ShipmentChargesController::fuel_surcharge(17148652);
ShipmentChargesController::fuel_surcharge(17119494);
ShipmentChargesController::fuel_surcharge(17148512);
ShipmentChargesController::fuel_surcharge(17172525);
ShipmentChargesController::fuel_surcharge(17148561);
ShipmentChargesController::fuel_surcharge(17148651);
ShipmentChargesController::fuel_surcharge(17148659);
ShipmentChargesController::fuel_surcharge(17148650);
ShipmentChargesController::fuel_surcharge(17172391);
ShipmentChargesController::fuel_surcharge(17172418);
ShipmentChargesController::fuel_surcharge(17148468);
ShipmentChargesController::fuel_surcharge(17148579);
ShipmentChargesController::fuel_surcharge(17172438);
ShipmentChargesController::fuel_surcharge(17148657);
ShipmentChargesController::fuel_surcharge(17140238);
ShipmentChargesController::fuel_surcharge(17156078);
ShipmentChargesController::fuel_surcharge(17152070);
ShipmentChargesController::fuel_surcharge(17167526);
ShipmentChargesController::fuel_surcharge(17113756);
ShipmentChargesController::fuel_surcharge(17170816);
ShipmentChargesController::fuel_surcharge(17136033);
ShipmentChargesController::fuel_surcharge(17158326);
ShipmentChargesController::fuel_surcharge(17168749);
ShipmentChargesController::fuel_surcharge(17154845);
ShipmentChargesController::fuel_surcharge(17169482);
ShipmentChargesController::fuel_surcharge(17164123);
ShipmentChargesController::fuel_surcharge(17156609);
ShipmentChargesController::fuel_surcharge(17171836);
ShipmentChargesController::fuel_surcharge(17159674);
ShipmentChargesController::fuel_surcharge(17170843);
ShipmentChargesController::fuel_surcharge(17154365);
ShipmentChargesController::fuel_surcharge(17169791);
ShipmentChargesController::fuel_surcharge(17170844);
ShipmentChargesController::fuel_surcharge(17170941);
ShipmentChargesController::fuel_surcharge(17158021);
ShipmentChargesController::fuel_surcharge(17170841);
ShipmentChargesController::fuel_surcharge(17165891);
ShipmentChargesController::fuel_surcharge(17160838);
ShipmentChargesController::fuel_surcharge(17170875);
ShipmentChargesController::fuel_surcharge(17170626);
ShipmentChargesController::fuel_surcharge(17169872);
ShipmentChargesController::fuel_surcharge(17142199);
ShipmentChargesController::fuel_surcharge(17167862);
ShipmentChargesController::fuel_surcharge(17152665);
ShipmentChargesController::fuel_surcharge(17168366);
ShipmentChargesController::fuel_surcharge(17169489);
ShipmentChargesController::fuel_surcharge(17169477);
ShipmentChargesController::fuel_surcharge(17169464);
ShipmentChargesController::fuel_surcharge(17169444);
ShipmentChargesController::fuel_surcharge(17170180);
ShipmentChargesController::fuel_surcharge(17169913);
ShipmentChargesController::fuel_surcharge(17170409);
ShipmentChargesController::fuel_surcharge(17168462);
ShipmentChargesController::fuel_surcharge(17170131);
ShipmentChargesController::fuel_surcharge(17169526);
ShipmentChargesController::fuel_surcharge(17169396);
ShipmentChargesController::fuel_surcharge(17169337);
ShipmentChargesController::fuel_surcharge(17170365);
ShipmentChargesController::fuel_surcharge(17170042);
ShipmentChargesController::fuel_surcharge(17169998);
ShipmentChargesController::fuel_surcharge(17170205);
ShipmentChargesController::fuel_surcharge(17166012);
ShipmentChargesController::fuel_surcharge(17139461);
ShipmentChargesController::fuel_surcharge(17157008);
ShipmentChargesController::fuel_surcharge(17117829);
ShipmentChargesController::fuel_surcharge(17091866);
ShipmentChargesController::fuel_surcharge(17166001);
ShipmentChargesController::fuel_surcharge(17165985);
ShipmentChargesController::fuel_surcharge(17165995);
ShipmentChargesController::fuel_surcharge(17165999);
ShipmentChargesController::fuel_surcharge(17168028);
ShipmentChargesController::fuel_surcharge(17124002);
ShipmentChargesController::fuel_surcharge(17097042);
ShipmentChargesController::fuel_surcharge(17097041);
ShipmentChargesController::fuel_surcharge(17156322);
ShipmentChargesController::fuel_surcharge(17097100);
ShipmentChargesController::fuel_surcharge(17123984);
ShipmentChargesController::fuel_surcharge(17097108);
ShipmentChargesController::fuel_surcharge(17166173);
ShipmentChargesController::fuel_surcharge(17123987);
ShipmentChargesController::fuel_surcharge(17123983);
ShipmentChargesController::fuel_surcharge(16862253);
ShipmentChargesController::fuel_surcharge(17123986);
ShipmentChargesController::fuel_surcharge(17097062);
ShipmentChargesController::fuel_surcharge(17164816);
ShipmentChargesController::fuel_surcharge(17097098);
ShipmentChargesController::fuel_surcharge(17097066);
ShipmentChargesController::fuel_surcharge(17097106);
ShipmentChargesController::fuel_surcharge(17168025);
ShipmentChargesController::fuel_surcharge(17142512);
ShipmentChargesController::fuel_surcharge(17167327);
ShipmentChargesController::fuel_surcharge(17078247);
ShipmentChargesController::fuel_surcharge(17166008);
ShipmentChargesController::fuel_surcharge(17165986);
ShipmentChargesController::fuel_surcharge(17165991);
ShipmentChargesController::fuel_surcharge(17168253);
ShipmentChargesController::fuel_surcharge(17080536);
ShipmentChargesController::fuel_surcharge(17097109);
ShipmentChargesController::fuel_surcharge(17079219);
ShipmentChargesController::fuel_surcharge(17097107);
ShipmentChargesController::fuel_surcharge(17165978);
ShipmentChargesController::fuel_surcharge(17161687);
ShipmentChargesController::fuel_surcharge(17165992);
ShipmentChargesController::fuel_surcharge(17156217);
ShipmentChargesController::fuel_surcharge(17051216);
ShipmentChargesController::fuel_surcharge(17165997);
ShipmentChargesController::fuel_surcharge(17166010);
ShipmentChargesController::fuel_surcharge(17166000);
ShipmentChargesController::fuel_surcharge(17165983);
ShipmentChargesController::fuel_surcharge(17091835);
ShipmentChargesController::fuel_surcharge(17165984);
ShipmentChargesController::fuel_surcharge(17165998);
ShipmentChargesController::fuel_surcharge(17139465);
ShipmentChargesController::fuel_surcharge(17140445);
ShipmentChargesController::fuel_surcharge(17117823);
ShipmentChargesController::fuel_surcharge(17166003);
ShipmentChargesController::fuel_surcharge(17165982);
ShipmentChargesController::fuel_surcharge(17156049);
ShipmentChargesController::fuel_surcharge(17117838);
ShipmentChargesController::fuel_surcharge(17166004);
ShipmentChargesController::fuel_surcharge(17139472);
ShipmentChargesController::fuel_surcharge(17097068);
ShipmentChargesController::fuel_surcharge(17157578);
ShipmentChargesController::fuel_surcharge(17128914);
ShipmentChargesController::fuel_surcharge(17128873);
ShipmentChargesController::fuel_surcharge(17101226);
ShipmentChargesController::fuel_surcharge(17154838);
ShipmentChargesController::fuel_surcharge(17149756);
ShipmentChargesController::fuel_surcharge(17160924);
ShipmentChargesController::fuel_surcharge(17149972);
ShipmentChargesController::fuel_surcharge(17159207);
ShipmentChargesController::fuel_surcharge(17156260);
ShipmentChargesController::fuel_surcharge(17163522);
ShipmentChargesController::fuel_surcharge(17154828);
ShipmentChargesController::fuel_surcharge(17148503);
ShipmentChargesController::fuel_surcharge(17126065);
ShipmentChargesController::fuel_surcharge(17119314);
ShipmentChargesController::fuel_surcharge(17148558);
ShipmentChargesController::fuel_surcharge(17151432);
ShipmentChargesController::fuel_surcharge(17148406);
ShipmentChargesController::fuel_surcharge(17148575);
ShipmentChargesController::fuel_surcharge(17170482);
ShipmentChargesController::fuel_surcharge(17158984);
ShipmentChargesController::fuel_surcharge(17158999);
ShipmentChargesController::fuel_surcharge(17156750);
ShipmentChargesController::fuel_surcharge(17156683);
ShipmentChargesController::fuel_surcharge(17146607);
ShipmentChargesController::fuel_surcharge(17063244);
ShipmentChargesController::fuel_surcharge(17170158);
ShipmentChargesController::fuel_surcharge(17167262);
ShipmentChargesController::fuel_surcharge(17154892);
ShipmentChargesController::fuel_surcharge(17167134);
ShipmentChargesController::fuel_surcharge(17169222);
ShipmentChargesController::fuel_surcharge(17156779);
ShipmentChargesController::fuel_surcharge(17169217);
ShipmentChargesController::fuel_surcharge(17126540);
ShipmentChargesController::fuel_surcharge(17169218);
ShipmentChargesController::fuel_surcharge(17126533);
ShipmentChargesController::fuel_surcharge(17172151);
ShipmentChargesController::fuel_surcharge(17172095);
ShipmentChargesController::fuel_surcharge(17171922);
ShipmentChargesController::fuel_surcharge(17172037);
ShipmentChargesController::fuel_surcharge(17171952);
ShipmentChargesController::fuel_surcharge(17172964);
ShipmentChargesController::fuel_surcharge(17171889);
ShipmentChargesController::fuel_surcharge(17172208);
ShipmentChargesController::fuel_surcharge(17171852);
ShipmentChargesController::fuel_surcharge(17152047);
ShipmentChargesController::fuel_surcharge(17131006);
ShipmentChargesController::fuel_surcharge(17113576);
ShipmentChargesController::fuel_surcharge(17154957);
ShipmentChargesController::fuel_surcharge(17149540);
ShipmentChargesController::fuel_surcharge(17159554);
ShipmentChargesController::fuel_surcharge(17079177);
ShipmentChargesController::fuel_surcharge(17079202);
ShipmentChargesController::fuel_surcharge(17172300);
ShipmentChargesController::fuel_surcharge(17171659);
ShipmentChargesController::fuel_surcharge(17172131);
ShipmentChargesController::fuel_surcharge(17173389);
ShipmentChargesController::fuel_surcharge(17172023);
ShipmentChargesController::fuel_surcharge(17172983);
ShipmentChargesController::fuel_surcharge(17172276);
ShipmentChargesController::fuel_surcharge(17173417);
ShipmentChargesController::fuel_surcharge(17171856);
ShipmentChargesController::fuel_surcharge(17172791);
ShipmentChargesController::fuel_surcharge(17171961);
ShipmentChargesController::fuel_surcharge(17171874);
ShipmentChargesController::fuel_surcharge(17171792);
ShipmentChargesController::fuel_surcharge(17172194);
ShipmentChargesController::fuel_surcharge(17132090);
ShipmentChargesController::fuel_surcharge(17079285);
ShipmentChargesController::fuel_surcharge(17132073);
ShipmentChargesController::fuel_surcharge(17079246);
ShipmentChargesController::fuel_surcharge(17132113);
ShipmentChargesController::fuel_surcharge(17132127);
ShipmentChargesController::fuel_surcharge(17132096);
ShipmentChargesController::fuel_surcharge(17132081);
ShipmentChargesController::fuel_surcharge(17149414);
ShipmentChargesController::fuel_surcharge(17149373);
ShipmentChargesController::fuel_surcharge(17126614);
ShipmentChargesController::fuel_surcharge(17155700);
ShipmentChargesController::fuel_surcharge(17162132);
ShipmentChargesController::fuel_surcharge(17114449);
ShipmentChargesController::fuel_surcharge(17161958);
ShipmentChargesController::fuel_surcharge(17155531);
ShipmentChargesController::fuel_surcharge(17161545);
ShipmentChargesController::fuel_surcharge(17156108);
ShipmentChargesController::fuel_surcharge(17141359);
ShipmentChargesController::fuel_surcharge(17149295);
ShipmentChargesController::fuel_surcharge(17171153);
ShipmentChargesController::fuel_surcharge(17171154);
ShipmentChargesController::fuel_surcharge(17161711);
ShipmentChargesController::fuel_surcharge(17147076);
ShipmentChargesController::fuel_surcharge(17171165);
ShipmentChargesController::fuel_surcharge(17171151);
ShipmentChargesController::fuel_surcharge(17171162);
ShipmentChargesController::fuel_surcharge(17137358);
ShipmentChargesController::fuel_surcharge(17141593);
ShipmentChargesController::fuel_surcharge(17145853);
ShipmentChargesController::fuel_surcharge(17170531);
ShipmentChargesController::fuel_surcharge(17138754);
ShipmentChargesController::fuel_surcharge(17147764);
ShipmentChargesController::fuel_surcharge(17170262);
ShipmentChargesController::fuel_surcharge(17165600);
ShipmentChargesController::fuel_surcharge(17141643);
ShipmentChargesController::fuel_surcharge(17169616);
ShipmentChargesController::fuel_surcharge(17164566);
ShipmentChargesController::fuel_surcharge(17168190);
ShipmentChargesController::fuel_surcharge(17164005);
ShipmentChargesController::fuel_surcharge(17096423);
ShipmentChargesController::fuel_surcharge(17157772);
ShipmentChargesController::fuel_surcharge(17161519);
ShipmentChargesController::fuel_surcharge(17160667);
ShipmentChargesController::fuel_surcharge(17144965);
ShipmentChargesController::fuel_surcharge(17151252);
ShipmentChargesController::fuel_surcharge(17169308);
ShipmentChargesController::fuel_surcharge(17159390);
ShipmentChargesController::fuel_surcharge(17159323);
ShipmentChargesController::fuel_surcharge(17156819);
ShipmentChargesController::fuel_surcharge(17097896);
ShipmentChargesController::fuel_surcharge(17169199);
ShipmentChargesController::fuel_surcharge(17157229);
ShipmentChargesController::fuel_surcharge(17169305);
ShipmentChargesController::fuel_surcharge(17160689);
ShipmentChargesController::fuel_surcharge(17162075);
ShipmentChargesController::fuel_surcharge(17127660);
ShipmentChargesController::fuel_surcharge(17159837);
ShipmentChargesController::fuel_surcharge(17146676);
ShipmentChargesController::fuel_surcharge(17159782);
ShipmentChargesController::fuel_surcharge(17161956);
ShipmentChargesController::fuel_surcharge(17163888);
ShipmentChargesController::fuel_surcharge(17165314);
ShipmentChargesController::fuel_surcharge(17163836);
ShipmentChargesController::fuel_surcharge(17165239);
ShipmentChargesController::fuel_surcharge(17162788);
ShipmentChargesController::fuel_surcharge(17149989);
ShipmentChargesController::fuel_surcharge(17154888);
ShipmentChargesController::fuel_surcharge(17158614);
ShipmentChargesController::fuel_surcharge(17164677);
ShipmentChargesController::fuel_surcharge(17166564);
ShipmentChargesController::fuel_surcharge(17164456);
ShipmentChargesController::fuel_surcharge(17164681);
ShipmentChargesController::fuel_surcharge(17155354);
ShipmentChargesController::fuel_surcharge(17163409);
ShipmentChargesController::fuel_surcharge(17140071);
ShipmentChargesController::fuel_surcharge(17144585);
ShipmentChargesController::fuel_surcharge(17158584);
ShipmentChargesController::fuel_surcharge(17158601);
ShipmentChargesController::fuel_surcharge(17170906);
ShipmentChargesController::fuel_surcharge(17158610);
ShipmentChargesController::fuel_surcharge(17140073);
ShipmentChargesController::fuel_surcharge(17164683);
ShipmentChargesController::fuel_surcharge(17158580);
ShipmentChargesController::fuel_surcharge(17158557);
ShipmentChargesController::fuel_surcharge(17150001);
ShipmentChargesController::fuel_surcharge(17158622);
ShipmentChargesController::fuel_surcharge(17157082);
ShipmentChargesController::fuel_surcharge(17154820);
ShipmentChargesController::fuel_surcharge(17149979);
ShipmentChargesController::fuel_surcharge(17123152);
ShipmentChargesController::fuel_surcharge(17158612);
ShipmentChargesController::fuel_surcharge(17162629);
ShipmentChargesController::fuel_surcharge(17158607);
ShipmentChargesController::fuel_surcharge(17173676);
ShipmentChargesController::fuel_surcharge(17158575);
ShipmentChargesController::fuel_surcharge(17158594);
ShipmentChargesController::fuel_surcharge(17164687);
ShipmentChargesController::fuel_surcharge(17164674);
ShipmentChargesController::fuel_surcharge(17158600);
ShipmentChargesController::fuel_surcharge(17172179);
ShipmentChargesController::fuel_surcharge(17173840);
ShipmentChargesController::fuel_surcharge(17156560);
ShipmentChargesController::fuel_surcharge(17154948);
ShipmentChargesController::fuel_surcharge(17157597);
ShipmentChargesController::fuel_surcharge(17172258);
ShipmentChargesController::fuel_surcharge(17158564);
ShipmentChargesController::fuel_surcharge(17158560);
ShipmentChargesController::fuel_surcharge(17160107);
ShipmentChargesController::fuel_surcharge(17142105);
ShipmentChargesController::fuel_surcharge(17158599);
ShipmentChargesController::fuel_surcharge(17157600);
ShipmentChargesController::fuel_surcharge(17158563);
ShipmentChargesController::fuel_surcharge(17160012);
ShipmentChargesController::fuel_surcharge(17171457);
ShipmentChargesController::fuel_surcharge(17164661);
ShipmentChargesController::fuel_surcharge(17151310);
ShipmentChargesController::fuel_surcharge(17170466);
ShipmentChargesController::fuel_surcharge(17158593);
ShipmentChargesController::fuel_surcharge(17140070);
ShipmentChargesController::fuel_surcharge(17158574);
ShipmentChargesController::fuel_surcharge(17172342);
ShipmentChargesController::fuel_surcharge(17158602);
ShipmentChargesController::fuel_surcharge(17173548);
ShipmentChargesController::fuel_surcharge(17158596);
ShipmentChargesController::fuel_surcharge(17164678);
ShipmentChargesController::fuel_surcharge(17164549);
ShipmentChargesController::fuel_surcharge(17158571);
ShipmentChargesController::fuel_surcharge(17158586);
ShipmentChargesController::fuel_surcharge(17158579);
ShipmentChargesController::fuel_surcharge(17158609);
ShipmentChargesController::fuel_surcharge(17158613);
ShipmentChargesController::fuel_surcharge(17155042);
ShipmentChargesController::fuel_surcharge(17158589);
ShipmentChargesController::fuel_surcharge(17158567);
ShipmentChargesController::fuel_surcharge(17158568);
ShipmentChargesController::fuel_surcharge(17158624);
ShipmentChargesController::fuel_surcharge(17158621);
ShipmentChargesController::fuel_surcharge(17158606);
ShipmentChargesController::fuel_surcharge(17162184);
ShipmentChargesController::fuel_surcharge(17162805);
ShipmentChargesController::fuel_surcharge(17164663);
ShipmentChargesController::fuel_surcharge(17171990);
ShipmentChargesController::fuel_surcharge(17117893);
ShipmentChargesController::fuel_surcharge(17113150);
ShipmentChargesController::fuel_surcharge(17113152);
ShipmentChargesController::fuel_surcharge(17147596);
ShipmentChargesController::fuel_surcharge(17147258);
ShipmentChargesController::fuel_surcharge(17147407);
ShipmentChargesController::fuel_surcharge(17117892);
ShipmentChargesController::fuel_surcharge(17161184);
ShipmentChargesController::fuel_surcharge(17117894);
ShipmentChargesController::fuel_surcharge(17163316);
ShipmentChargesController::fuel_surcharge(17165069);
ShipmentChargesController::fuel_surcharge(17164759);
ShipmentChargesController::fuel_surcharge(17172071);
ShipmentChargesController::fuel_surcharge(17165490);
ShipmentChargesController::fuel_surcharge(17141472);
ShipmentChargesController::fuel_surcharge(17162797);
ShipmentChargesController::fuel_surcharge(17134527);
ShipmentChargesController::fuel_surcharge(17160694);
ShipmentChargesController::fuel_surcharge(17160925);
ShipmentChargesController::fuel_surcharge(17161067);
ShipmentChargesController::fuel_surcharge(17162152);
ShipmentChargesController::fuel_surcharge(17169509);
ShipmentChargesController::fuel_surcharge(17165046);
ShipmentChargesController::fuel_surcharge(17169479);
ShipmentChargesController::fuel_surcharge(17170871);
ShipmentChargesController::fuel_surcharge(17172336);
ShipmentChargesController::fuel_surcharge(17169592);
ShipmentChargesController::fuel_surcharge(17170962);
ShipmentChargesController::fuel_surcharge(17147850);
ShipmentChargesController::fuel_surcharge(17170166);
ShipmentChargesController::fuel_surcharge(17172364);
ShipmentChargesController::fuel_surcharge(17170511);
ShipmentChargesController::fuel_surcharge(17170737);
ShipmentChargesController::fuel_surcharge(17162928);
ShipmentChargesController::fuel_surcharge(17170191);
ShipmentChargesController::fuel_surcharge(17157814);
ShipmentChargesController::fuel_surcharge(17169598);
ShipmentChargesController::fuel_surcharge(17170803);
ShipmentChargesController::fuel_surcharge(17164744);
ShipmentChargesController::fuel_surcharge(17126610);
ShipmentChargesController::fuel_surcharge(17093252);
ShipmentChargesController::fuel_surcharge(17167301);
ShipmentChargesController::fuel_surcharge(17157121);
ShipmentChargesController::fuel_surcharge(17157419);
ShipmentChargesController::fuel_surcharge(17160871);
ShipmentChargesController::fuel_surcharge(17163414);
ShipmentChargesController::fuel_surcharge(17167470);
ShipmentChargesController::fuel_surcharge(17167455);
ShipmentChargesController::fuel_surcharge(17167675);
ShipmentChargesController::fuel_surcharge(17163416);
ShipmentChargesController::fuel_surcharge(17149928);
ShipmentChargesController::fuel_surcharge(17163426);
ShipmentChargesController::fuel_surcharge(17163421);
ShipmentChargesController::fuel_surcharge(17163431);
ShipmentChargesController::fuel_surcharge(16979570);
ShipmentChargesController::fuel_surcharge(17163417);
ShipmentChargesController::fuel_surcharge(17167370);
ShipmentChargesController::fuel_surcharge(17167512);
ShipmentChargesController::fuel_surcharge(17167845);
ShipmentChargesController::fuel_surcharge(17163433);
ShipmentChargesController::fuel_surcharge(17162802);
ShipmentChargesController::fuel_surcharge(17163435);
ShipmentChargesController::fuel_surcharge(17163420);
ShipmentChargesController::fuel_surcharge(17163413);
ShipmentChargesController::fuel_surcharge(17163415);
ShipmentChargesController::fuel_surcharge(17166914);
ShipmentChargesController::fuel_surcharge(17167537);
ShipmentChargesController::fuel_surcharge(17134697);
ShipmentChargesController::fuel_surcharge(17148379);
ShipmentChargesController::fuel_surcharge(17163411);
ShipmentChargesController::fuel_surcharge(17167660);
ShipmentChargesController::fuel_surcharge(17167693);
ShipmentChargesController::fuel_surcharge(17163434);
ShipmentChargesController::fuel_surcharge(17163407);
ShipmentChargesController::fuel_surcharge(17158316);
ShipmentChargesController::fuel_surcharge(17167658);
ShipmentChargesController::fuel_surcharge(17163410);
ShipmentChargesController::fuel_surcharge(17163427);
ShipmentChargesController::fuel_surcharge(17163412);
ShipmentChargesController::fuel_surcharge(17163424);
ShipmentChargesController::fuel_surcharge(17163423);
ShipmentChargesController::fuel_surcharge(17158317);
ShipmentChargesController::fuel_surcharge(17109407);
ShipmentChargesController::fuel_surcharge(17163429);
ShipmentChargesController::fuel_surcharge(17163419);
ShipmentChargesController::fuel_surcharge(17163430);
ShipmentChargesController::fuel_surcharge(17163408);
ShipmentChargesController::fuel_surcharge(17158313);
ShipmentChargesController::fuel_surcharge(17167656);
ShipmentChargesController::fuel_surcharge(17134843);
ShipmentChargesController::fuel_surcharge(17170293);
ShipmentChargesController::fuel_surcharge(17171089);
ShipmentChargesController::fuel_surcharge(17170606);
ShipmentChargesController::fuel_surcharge(17134885);
ShipmentChargesController::fuel_surcharge(17150030);
ShipmentChargesController::fuel_surcharge(17150017);
ShipmentChargesController::fuel_surcharge(17141061);
ShipmentChargesController::fuel_surcharge(17150301);
ShipmentChargesController::fuel_surcharge(17171082);
ShipmentChargesController::fuel_surcharge(17168049);
ShipmentChargesController::fuel_surcharge(17143776);
ShipmentChargesController::fuel_surcharge(17158314);
ShipmentChargesController::fuel_surcharge(17160985);
ShipmentChargesController::fuel_surcharge(17156907);
ShipmentChargesController::fuel_surcharge(17159169);
ShipmentChargesController::fuel_surcharge(17163281);
ShipmentChargesController::fuel_surcharge(17165555);
ShipmentChargesController::fuel_surcharge(17164898);
ShipmentChargesController::fuel_surcharge(17165618);
ShipmentChargesController::fuel_surcharge(17158400);
ShipmentChargesController::fuel_surcharge(17164015);
ShipmentChargesController::fuel_surcharge(17163843);
ShipmentChargesController::fuel_surcharge(17162066);
ShipmentChargesController::fuel_surcharge(17163897);
ShipmentChargesController::fuel_surcharge(17163187);
ShipmentChargesController::fuel_surcharge(17162451);
ShipmentChargesController::fuel_surcharge(17158268);
ShipmentChargesController::fuel_surcharge(17170623);
ShipmentChargesController::fuel_surcharge(17161185);
ShipmentChargesController::fuel_surcharge(17161223);
ShipmentChargesController::fuel_surcharge(17154390);
ShipmentChargesController::fuel_surcharge(17159610);
ShipmentChargesController::fuel_surcharge(17172308);
ShipmentChargesController::fuel_surcharge(17172236);
ShipmentChargesController::fuel_surcharge(17155589);
ShipmentChargesController::fuel_surcharge(17159918);
ShipmentChargesController::fuel_surcharge(17169730);
ShipmentChargesController::fuel_surcharge(17171806);
ShipmentChargesController::fuel_surcharge(17168138);
ShipmentChargesController::fuel_surcharge(17171901);
ShipmentChargesController::fuel_surcharge(17164654);
ShipmentChargesController::fuel_surcharge(17160015);
ShipmentChargesController::fuel_surcharge(17122771);
ShipmentChargesController::fuel_surcharge(17158517);
ShipmentChargesController::fuel_surcharge(17154328);
ShipmentChargesController::fuel_surcharge(17155288);
ShipmentChargesController::fuel_surcharge(17157571);
ShipmentChargesController::fuel_surcharge(17157666);
ShipmentChargesController::fuel_surcharge(17155275);
ShipmentChargesController::fuel_surcharge(17157521);
ShipmentChargesController::fuel_surcharge(17168667);
ShipmentChargesController::fuel_surcharge(17166520);
ShipmentChargesController::fuel_surcharge(17155882);
ShipmentChargesController::fuel_surcharge(17167689);
ShipmentChargesController::fuel_surcharge(17169611);
ShipmentChargesController::fuel_surcharge(17145516);
ShipmentChargesController::fuel_surcharge(17161728);
ShipmentChargesController::fuel_surcharge(17166401);
ShipmentChargesController::fuel_surcharge(17151249);
ShipmentChargesController::fuel_surcharge(17152901);
ShipmentChargesController::fuel_surcharge(17151255);
ShipmentChargesController::fuel_surcharge(17168671);
ShipmentChargesController::fuel_surcharge(17156397);
ShipmentChargesController::fuel_surcharge(17161974);
ShipmentChargesController::fuel_surcharge(17170021);
ShipmentChargesController::fuel_surcharge(17161726);
ShipmentChargesController::fuel_surcharge(17123452);
ShipmentChargesController::fuel_surcharge(17162598);
ShipmentChargesController::fuel_surcharge(17157525);
ShipmentChargesController::fuel_surcharge(17162395);
ShipmentChargesController::fuel_surcharge(17161730);
ShipmentChargesController::fuel_surcharge(17161017);
ShipmentChargesController::fuel_surcharge(17146949);
ShipmentChargesController::fuel_surcharge(17146947);
ShipmentChargesController::fuel_surcharge(17143930);
ShipmentChargesController::fuel_surcharge(17157612);
ShipmentChargesController::fuel_surcharge(17159384);
ShipmentChargesController::fuel_surcharge(17124430);
ShipmentChargesController::fuel_surcharge(17146940);
ShipmentChargesController::fuel_surcharge(17170762);
ShipmentChargesController::fuel_surcharge(17143900);
ShipmentChargesController::fuel_surcharge(17162129);
ShipmentChargesController::fuel_surcharge(17162359);
ShipmentChargesController::fuel_surcharge(17157591);
ShipmentChargesController::fuel_surcharge(17162124);
ShipmentChargesController::fuel_surcharge(17165017);
ShipmentChargesController::fuel_surcharge(17146938);
ShipmentChargesController::fuel_surcharge(17017803);
ShipmentChargesController::fuel_surcharge(16695574);
ShipmentChargesController::fuel_surcharge(17151265);
ShipmentChargesController::fuel_surcharge(17040575);
ShipmentChargesController::fuel_surcharge(17151259);
ShipmentChargesController::fuel_surcharge(17144871);
ShipmentChargesController::fuel_surcharge(17124436);
ShipmentChargesController::fuel_surcharge(17024058);
ShipmentChargesController::fuel_surcharge(17156674);
ShipmentChargesController::fuel_surcharge(17173607);
ShipmentChargesController::fuel_surcharge(17169177);
ShipmentChargesController::fuel_surcharge(17147765);
ShipmentChargesController::fuel_surcharge(17161929);
ShipmentChargesController::fuel_surcharge(17170784);
ShipmentChargesController::fuel_surcharge(17158263);
ShipmentChargesController::fuel_surcharge(17124427);
ShipmentChargesController::fuel_surcharge(17169502);
ShipmentChargesController::fuel_surcharge(17131958);
ShipmentChargesController::fuel_surcharge(17124435);
ShipmentChargesController::fuel_surcharge(17125732);
ShipmentChargesController::fuel_surcharge(17167841);
ShipmentChargesController::fuel_surcharge(17167248);
ShipmentChargesController::fuel_surcharge(17167649);
ShipmentChargesController::fuel_surcharge(17162422);
ShipmentChargesController::fuel_surcharge(17156531);
ShipmentChargesController::fuel_surcharge(17165896);
ShipmentChargesController::fuel_surcharge(17173763);
ShipmentChargesController::fuel_surcharge(17163818);
ShipmentChargesController::fuel_surcharge(17154988);
ShipmentChargesController::fuel_surcharge(17169029);
ShipmentChargesController::fuel_surcharge(17162123);
ShipmentChargesController::fuel_surcharge(17167467);
ShipmentChargesController::fuel_surcharge(17157544);
ShipmentChargesController::fuel_surcharge(17122757);
ShipmentChargesController::fuel_surcharge(17125744);
ShipmentChargesController::fuel_surcharge(17152352);
ShipmentChargesController::fuel_surcharge(17164484);
ShipmentChargesController::fuel_surcharge(17130170);
ShipmentChargesController::fuel_surcharge(17152499);
ShipmentChargesController::fuel_surcharge(17143429);
ShipmentChargesController::fuel_surcharge(17156727);
ShipmentChargesController::fuel_surcharge(17161784);
ShipmentChargesController::fuel_surcharge(17049717);
ShipmentChargesController::fuel_surcharge(17098084);
ShipmentChargesController::fuel_surcharge(17160428);
ShipmentChargesController::fuel_surcharge(17160035);
ShipmentChargesController::fuel_surcharge(17171613);
ShipmentChargesController::fuel_surcharge(17155874);
ShipmentChargesController::fuel_surcharge(17161983);
ShipmentChargesController::fuel_surcharge(17159208);
ShipmentChargesController::fuel_surcharge(17096642);
ShipmentChargesController::fuel_surcharge(17161557);
ShipmentChargesController::fuel_surcharge(17170285);
ShipmentChargesController::fuel_surcharge(17172770);
ShipmentChargesController::fuel_surcharge(17170893);
ShipmentChargesController::fuel_surcharge(17138517);
ShipmentChargesController::fuel_surcharge(17149757);
ShipmentChargesController::fuel_surcharge(17170804);
ShipmentChargesController::fuel_surcharge(17171831);
ShipmentChargesController::fuel_surcharge(17121929);
ShipmentChargesController::fuel_surcharge(17162779);
ShipmentChargesController::fuel_surcharge(17171649);
ShipmentChargesController::fuel_surcharge(17172315);
ShipmentChargesController::fuel_surcharge(17171477);
ShipmentChargesController::fuel_surcharge(17172361);
ShipmentChargesController::fuel_surcharge(17172343);
ShipmentChargesController::fuel_surcharge(17171694);
ShipmentChargesController::fuel_surcharge(17170790);
ShipmentChargesController::fuel_surcharge(17171841);
ShipmentChargesController::fuel_surcharge(17171449);
ShipmentChargesController::fuel_surcharge(17170303);
ShipmentChargesController::fuel_surcharge(17172296);
ShipmentChargesController::fuel_surcharge(17171503);
ShipmentChargesController::fuel_surcharge(17136897);
ShipmentChargesController::fuel_surcharge(17170625);
ShipmentChargesController::fuel_surcharge(17170382);
ShipmentChargesController::fuel_surcharge(17170478);
ShipmentChargesController::fuel_surcharge(17170320);
ShipmentChargesController::fuel_surcharge(17170741);
ShipmentChargesController::fuel_surcharge(17161980);
ShipmentChargesController::fuel_surcharge(17162009);
ShipmentChargesController::fuel_surcharge(17154831);
ShipmentChargesController::fuel_surcharge(17170608);
ShipmentChargesController::fuel_surcharge(17140208);
ShipmentChargesController::fuel_surcharge(17142031);
ShipmentChargesController::fuel_surcharge(17141882);
ShipmentChargesController::fuel_surcharge(17152626);
ShipmentChargesController::fuel_surcharge(17164196);
ShipmentChargesController::fuel_surcharge(17168833);
ShipmentChargesController::fuel_surcharge(17162045);
ShipmentChargesController::fuel_surcharge(17166248);
ShipmentChargesController::fuel_surcharge(17128081);
ShipmentChargesController::fuel_surcharge(17128089);
ShipmentChargesController::fuel_surcharge(17049715);
ShipmentChargesController::fuel_surcharge(17128108);
ShipmentChargesController::fuel_surcharge(17128113);
ShipmentChargesController::fuel_surcharge(17169760);
ShipmentChargesController::fuel_surcharge(17169532);
ShipmentChargesController::fuel_surcharge(17160054);
ShipmentChargesController::fuel_surcharge(17153150);
ShipmentChargesController::fuel_surcharge(17153131);
ShipmentChargesController::fuel_surcharge(17155077);
ShipmentChargesController::fuel_surcharge(17172793);
ShipmentChargesController::fuel_surcharge(17143611);
ShipmentChargesController::fuel_surcharge(17151262);
ShipmentChargesController::fuel_surcharge(17124431);
ShipmentChargesController::fuel_surcharge(17164650);
ShipmentChargesController::fuel_surcharge(17170800);
ShipmentChargesController::fuel_surcharge(17135540);
ShipmentChargesController::fuel_surcharge(17153430);
ShipmentChargesController::fuel_surcharge(17161576);
ShipmentChargesController::fuel_surcharge(17170820);
ShipmentChargesController::fuel_surcharge(17152895);
ShipmentChargesController::fuel_surcharge(17158785);
ShipmentChargesController::fuel_surcharge(17170778);
ShipmentChargesController::fuel_surcharge(17163182);
ShipmentChargesController::fuel_surcharge(17130184);
ShipmentChargesController::fuel_surcharge(17034312);
ShipmentChargesController::fuel_surcharge(17158153);
ShipmentChargesController::fuel_surcharge(17158934);
ShipmentChargesController::fuel_surcharge(17158804);
ShipmentChargesController::fuel_surcharge(17158221);
ShipmentChargesController::fuel_surcharge(17162116);
ShipmentChargesController::fuel_surcharge(17169677);
ShipmentChargesController::fuel_surcharge(17159351);
ShipmentChargesController::fuel_surcharge(17173811);
ShipmentChargesController::fuel_surcharge(17159348);
ShipmentChargesController::fuel_surcharge(17169663);
ShipmentChargesController::fuel_surcharge(17159336);
ShipmentChargesController::fuel_surcharge(17173810);
ShipmentChargesController::fuel_surcharge(17144355);
ShipmentChargesController::fuel_surcharge(17159350);
ShipmentChargesController::fuel_surcharge(17169676);
ShipmentChargesController::fuel_surcharge(17173795);
ShipmentChargesController::fuel_surcharge(17159339);
ShipmentChargesController::fuel_surcharge(17159349);
ShipmentChargesController::fuel_surcharge(17159340);
ShipmentChargesController::fuel_surcharge(17169646);
ShipmentChargesController::fuel_surcharge(17173807);
ShipmentChargesController::fuel_surcharge(17142591);
ShipmentChargesController::fuel_surcharge(17161125);
ShipmentChargesController::fuel_surcharge(17152851);
ShipmentChargesController::fuel_surcharge(17149292);
ShipmentChargesController::fuel_surcharge(17156492);
ShipmentChargesController::fuel_surcharge(17146941);
ShipmentChargesController::fuel_surcharge(17158525);
ShipmentChargesController::fuel_surcharge(17152940);
ShipmentChargesController::fuel_surcharge(17152453);
ShipmentChargesController::fuel_surcharge(17158255);
ShipmentChargesController::fuel_surcharge(17159640);
ShipmentChargesController::fuel_surcharge(17165864);
ShipmentChargesController::fuel_surcharge(17149472);
ShipmentChargesController::fuel_surcharge(17156584);
ShipmentChargesController::fuel_surcharge(17163523);
ShipmentChargesController::fuel_surcharge(17169279);
ShipmentChargesController::fuel_surcharge(17149042);
ShipmentChargesController::fuel_surcharge(17173260);
ShipmentChargesController::fuel_surcharge(17153477);
ShipmentChargesController::fuel_surcharge(17173255);
ShipmentChargesController::fuel_surcharge(17148697);
ShipmentChargesController::fuel_surcharge(17173268);
ShipmentChargesController::fuel_surcharge(17149159);
ShipmentChargesController::fuel_surcharge(17172500);
ShipmentChargesController::fuel_surcharge(17149227);
ShipmentChargesController::fuel_surcharge(17173009);
ShipmentChargesController::fuel_surcharge(17149226);
ShipmentChargesController::fuel_surcharge(17173259);
ShipmentChargesController::fuel_surcharge(17149213);
ShipmentChargesController::fuel_surcharge(17173257);
ShipmentChargesController::fuel_surcharge(17173003);
ShipmentChargesController::fuel_surcharge(17173258);
ShipmentChargesController::fuel_surcharge(17148716);
ShipmentChargesController::fuel_surcharge(17149230);
ShipmentChargesController::fuel_surcharge(17133948);
ShipmentChargesController::fuel_surcharge(17079003);
ShipmentChargesController::fuel_surcharge(17171771);
ShipmentChargesController::fuel_surcharge(17106740);
ShipmentChargesController::fuel_surcharge(17106871);
ShipmentChargesController::fuel_surcharge(17106850);
ShipmentChargesController::fuel_surcharge(17106677);
ShipmentChargesController::fuel_surcharge(17106918);
ShipmentChargesController::fuel_surcharge(17106695);
ShipmentChargesController::fuel_surcharge(17106658);
ShipmentChargesController::fuel_surcharge(17106893);
ShipmentChargesController::fuel_surcharge(17119319);
ShipmentChargesController::fuel_surcharge(17106781);
ShipmentChargesController::fuel_surcharge(17106771);
ShipmentChargesController::fuel_surcharge(17106881);
ShipmentChargesController::fuel_surcharge(17106823);
ShipmentChargesController::fuel_surcharge(17148418);
ShipmentChargesController::fuel_surcharge(17119425);
ShipmentChargesController::fuel_surcharge(17148610);
ShipmentChargesController::fuel_surcharge(17106854);
ShipmentChargesController::fuel_surcharge(17106542);
ShipmentChargesController::fuel_surcharge(17106609);
ShipmentChargesController::fuel_surcharge(17106762);
ShipmentChargesController::fuel_surcharge(17060900);
ShipmentChargesController::fuel_surcharge(17106804);
ShipmentChargesController::fuel_surcharge(17106429);
ShipmentChargesController::fuel_surcharge(17119418);
ShipmentChargesController::fuel_surcharge(17172428);
ShipmentChargesController::fuel_surcharge(17106788);
ShipmentChargesController::fuel_surcharge(17106846);
ShipmentChargesController::fuel_surcharge(17106927);
ShipmentChargesController::fuel_surcharge(17106641);
ShipmentChargesController::fuel_surcharge(17148431);
ShipmentChargesController::fuel_surcharge(17106727);
ShipmentChargesController::fuel_surcharge(17119479);
ShipmentChargesController::fuel_surcharge(17119279);
ShipmentChargesController::fuel_surcharge(17106824);
ShipmentChargesController::fuel_surcharge(17106666);
ShipmentChargesController::fuel_surcharge(17148408);
ShipmentChargesController::fuel_surcharge(17061080);
ShipmentChargesController::fuel_surcharge(17106550);
ShipmentChargesController::fuel_surcharge(17106660);
ShipmentChargesController::fuel_surcharge(17106723);
ShipmentChargesController::fuel_surcharge(17119348);
ShipmentChargesController::fuel_surcharge(17106808);
ShipmentChargesController::fuel_surcharge(17106887);
ShipmentChargesController::fuel_surcharge(17106701);
ShipmentChargesController::fuel_surcharge(17106449);
ShipmentChargesController::fuel_surcharge(17037938);
ShipmentChargesController::fuel_surcharge(17106706);
ShipmentChargesController::fuel_surcharge(17106573);
ShipmentChargesController::fuel_surcharge(17106862);
ShipmentChargesController::fuel_surcharge(17119410);
ShipmentChargesController::fuel_surcharge(17106874);
ShipmentChargesController::fuel_surcharge(17027625);
ShipmentChargesController::fuel_surcharge(17154043);
ShipmentChargesController::fuel_surcharge(17158156);
ShipmentChargesController::fuel_surcharge(17158158);
ShipmentChargesController::fuel_surcharge(17167087);
ShipmentChargesController::fuel_surcharge(17126522);
ShipmentChargesController::fuel_surcharge(17158462);
ShipmentChargesController::fuel_surcharge(17158758);
ShipmentChargesController::fuel_surcharge(17161204);
ShipmentChargesController::fuel_surcharge(17160908);
ShipmentChargesController::fuel_surcharge(17158332);
ShipmentChargesController::fuel_surcharge(17167210);
ShipmentChargesController::fuel_surcharge(17167203);
ShipmentChargesController::fuel_surcharge(17167035);
ShipmentChargesController::fuel_surcharge(17167032);
ShipmentChargesController::fuel_surcharge(17158761);
ShipmentChargesController::fuel_surcharge(17151492);
ShipmentChargesController::fuel_surcharge(17175077);
ShipmentChargesController::fuel_surcharge(17156756);
ShipmentChargesController::fuel_surcharge(17156757);
ShipmentChargesController::fuel_surcharge(17144125);
ShipmentChargesController::fuel_surcharge(17161674);
ShipmentChargesController::fuel_surcharge(17161675);
ShipmentChargesController::fuel_surcharge(17168473);
ShipmentChargesController::fuel_surcharge(17161671);
ShipmentChargesController::fuel_surcharge(17168737);
ShipmentChargesController::fuel_surcharge(17168745);
ShipmentChargesController::fuel_surcharge(17146455);
ShipmentChargesController::fuel_surcharge(17168591);
ShipmentChargesController::fuel_surcharge(17149437);
ShipmentChargesController::fuel_surcharge(17148956);
ShipmentChargesController::fuel_surcharge(17135983);
ShipmentChargesController::fuel_surcharge(17168247);
ShipmentChargesController::fuel_surcharge(17157605);
ShipmentChargesController::fuel_surcharge(17148295);
ShipmentChargesController::fuel_surcharge(17166077);
ShipmentChargesController::fuel_surcharge(17178492);
ShipmentChargesController::fuel_surcharge(17139992);
ShipmentChargesController::fuel_surcharge(17159616);
ShipmentChargesController::fuel_surcharge(17158857);
ShipmentChargesController::fuel_surcharge(17159331);
ShipmentChargesController::fuel_surcharge(17173826);
ShipmentChargesController::fuel_surcharge(17173868);
ShipmentChargesController::fuel_surcharge(17171644);
ShipmentChargesController::fuel_surcharge(17171654);
ShipmentChargesController::fuel_surcharge(17178172);
ShipmentChargesController::fuel_surcharge(17178157);
ShipmentChargesController::fuel_surcharge(17157989);
ShipmentChargesController::fuel_surcharge(17173261);
ShipmentChargesController::fuel_surcharge(17173267);
ShipmentChargesController::fuel_surcharge(17173262);
ShipmentChargesController::fuel_surcharge(17058599);
ShipmentChargesController::fuel_surcharge(17150292);
ShipmentChargesController::fuel_surcharge(17096783);
ShipmentChargesController::fuel_surcharge(17092774);
ShipmentChargesController::fuel_surcharge(16885674);
ShipmentChargesController::fuel_surcharge(17149940);
ShipmentChargesController::fuel_surcharge(17133291);
ShipmentChargesController::fuel_surcharge(17175201);
ShipmentChargesController::fuel_surcharge(17150838);
ShipmentChargesController::fuel_surcharge(17150730);
ShipmentChargesController::fuel_surcharge(17150739);
ShipmentChargesController::fuel_surcharge(17150824);
ShipmentChargesController::fuel_surcharge(17169957);
ShipmentChargesController::fuel_surcharge(17173001);
ShipmentChargesController::fuel_surcharge(17173017);
ShipmentChargesController::fuel_surcharge(17172994);
ShipmentChargesController::fuel_surcharge(17173031);
ShipmentChargesController::fuel_surcharge(17173042);
ShipmentChargesController::fuel_surcharge(17173041);
ShipmentChargesController::fuel_surcharge(17172996);
ShipmentChargesController::fuel_surcharge(17173093);
ShipmentChargesController::fuel_surcharge(17173225);
ShipmentChargesController::fuel_surcharge(17173038);
ShipmentChargesController::fuel_surcharge(17173002);
ShipmentChargesController::fuel_surcharge(17173079);
ShipmentChargesController::fuel_surcharge(17173178);
ShipmentChargesController::fuel_surcharge(17173032);
ShipmentChargesController::fuel_surcharge(17173128);
ShipmentChargesController::fuel_surcharge(17173101);
ShipmentChargesController::fuel_surcharge(17173030);
ShipmentChargesController::fuel_surcharge(17173008);
ShipmentChargesController::fuel_surcharge(17149189);
ShipmentChargesController::fuel_surcharge(17149181);
ShipmentChargesController::fuel_surcharge(17149190);
ShipmentChargesController::fuel_surcharge(17149175);
ShipmentChargesController::fuel_surcharge(17173035);
ShipmentChargesController::fuel_surcharge(17149183);
ShipmentChargesController::fuel_surcharge(17149182);
ShipmentChargesController::fuel_surcharge(17173015);
ShipmentChargesController::fuel_surcharge(17149261);
ShipmentChargesController::fuel_surcharge(17173018);
ShipmentChargesController::fuel_surcharge(17172991);
ShipmentChargesController::fuel_surcharge(17173022);
ShipmentChargesController::fuel_surcharge(17173007);
ShipmentChargesController::fuel_surcharge(17173021);
ShipmentChargesController::fuel_surcharge(17173005);
ShipmentChargesController::fuel_surcharge(17173011);
ShipmentChargesController::fuel_surcharge(17173729);
ShipmentChargesController::fuel_surcharge(17173714);
ShipmentChargesController::fuel_surcharge(17149194);
ShipmentChargesController::fuel_surcharge(17149184);
ShipmentChargesController::fuel_surcharge(17149179);
ShipmentChargesController::fuel_surcharge(17149201);
ShipmentChargesController::fuel_surcharge(17149173);
ShipmentChargesController::fuel_surcharge(17149199);
ShipmentChargesController::fuel_surcharge(17149193);
ShipmentChargesController::fuel_surcharge(17149168);
ShipmentChargesController::fuel_surcharge(17149174);
ShipmentChargesController::fuel_surcharge(17149176);
ShipmentChargesController::fuel_surcharge(17149191);
ShipmentChargesController::fuel_surcharge(17149172);
ShipmentChargesController::fuel_surcharge(17149186);
ShipmentChargesController::fuel_surcharge(17149187);
ShipmentChargesController::fuel_surcharge(17149170);
ShipmentChargesController::fuel_surcharge(17149197);
ShipmentChargesController::fuel_surcharge(17173370);
ShipmentChargesController::fuel_surcharge(17173744);
ShipmentChargesController::fuel_surcharge(17172127);
ShipmentChargesController::fuel_surcharge(17172794);
ShipmentChargesController::fuel_surcharge(17173477);
ShipmentChargesController::fuel_surcharge(17167046);
ShipmentChargesController::fuel_surcharge(17173019);
ShipmentChargesController::fuel_surcharge(17173189);
ShipmentChargesController::fuel_surcharge(17173274);
ShipmentChargesController::fuel_surcharge(17173341);
ShipmentChargesController::fuel_surcharge(17173345);
ShipmentChargesController::fuel_surcharge(17173317);
ShipmentChargesController::fuel_surcharge(17173303);
ShipmentChargesController::fuel_surcharge(17173344);
ShipmentChargesController::fuel_surcharge(17173342);
ShipmentChargesController::fuel_surcharge(17173314);
ShipmentChargesController::fuel_surcharge(17173273);
ShipmentChargesController::fuel_surcharge(17173302);
ShipmentChargesController::fuel_surcharge(17173275);
ShipmentChargesController::fuel_surcharge(17173309);
ShipmentChargesController::fuel_surcharge(17173321);
ShipmentChargesController::fuel_surcharge(17173338);
ShipmentChargesController::fuel_surcharge(17173331);
ShipmentChargesController::fuel_surcharge(17173304);
ShipmentChargesController::fuel_surcharge(17173312);
ShipmentChargesController::fuel_surcharge(17173307);
ShipmentChargesController::fuel_surcharge(17173315);
ShipmentChargesController::fuel_surcharge(17173326);
ShipmentChargesController::fuel_surcharge(17169960);
ShipmentChargesController::fuel_surcharge(17169995);
ShipmentChargesController::fuel_surcharge(17178872);
ShipmentChargesController::fuel_surcharge(17174306);
ShipmentChargesController::fuel_surcharge(17156700);
ShipmentChargesController::fuel_surcharge(17163497);
ShipmentChargesController::fuel_surcharge(17178871);
ShipmentChargesController::fuel_surcharge(17174389);
ShipmentChargesController::fuel_surcharge(17155543);
ShipmentChargesController::fuel_surcharge(17187393);
ShipmentChargesController::fuel_surcharge(17187394);
ShipmentChargesController::fuel_surcharge(17187395);
ShipmentChargesController::fuel_surcharge(17187396);
ShipmentChargesController::fuel_surcharge(17187397);
ShipmentChargesController::fuel_surcharge(17187398);
ShipmentChargesController::fuel_surcharge(17187399);
ShipmentChargesController::fuel_surcharge(17187400);
ShipmentChargesController::fuel_surcharge(17187401);
ShipmentChargesController::fuel_surcharge(17187402);
ShipmentChargesController::fuel_surcharge(17187403);
ShipmentChargesController::fuel_surcharge(17187404);
ShipmentChargesController::fuel_surcharge(17187405);
ShipmentChargesController::fuel_surcharge(17187406);
ShipmentChargesController::fuel_surcharge(17187407);
ShipmentChargesController::fuel_surcharge(17187408);
ShipmentChargesController::fuel_surcharge(17187409);
ShipmentChargesController::fuel_surcharge(17187410);
ShipmentChargesController::fuel_surcharge(17187411);
ShipmentChargesController::fuel_surcharge(17187412);
ShipmentChargesController::fuel_surcharge(17187413);
ShipmentChargesController::fuel_surcharge(17187414);
ShipmentChargesController::fuel_surcharge(17187415);
ShipmentChargesController::fuel_surcharge(17187417);
ShipmentChargesController::fuel_surcharge(17187418);
ShipmentChargesController::fuel_surcharge(17187419);
ShipmentChargesController::fuel_surcharge(17187420);
ShipmentChargesController::fuel_surcharge(17187421);
ShipmentChargesController::fuel_surcharge(17187422);
ShipmentChargesController::fuel_surcharge(17187423);
ShipmentChargesController::fuel_surcharge(17187424);
ShipmentChargesController::fuel_surcharge(17187425);
ShipmentChargesController::fuel_surcharge(17187426);
ShipmentChargesController::fuel_surcharge(17187427);
ShipmentChargesController::fuel_surcharge(17187428);
ShipmentChargesController::fuel_surcharge(17187429);
ShipmentChargesController::fuel_surcharge(17187430);
ShipmentChargesController::fuel_surcharge(17187431);
ShipmentChargesController::fuel_surcharge(17187432);
ShipmentChargesController::fuel_surcharge(17187433);
ShipmentChargesController::fuel_surcharge(17187434);
ShipmentChargesController::fuel_surcharge(17187435);
ShipmentChargesController::fuel_surcharge(17187436);
ShipmentChargesController::fuel_surcharge(17187437);
ShipmentChargesController::fuel_surcharge(17187438);
ShipmentChargesController::fuel_surcharge(17187439);
ShipmentChargesController::fuel_surcharge(17187440);
ShipmentChargesController::fuel_surcharge(17187443);
ShipmentChargesController::fuel_surcharge(17187444);
ShipmentChargesController::fuel_surcharge(17187445);
ShipmentChargesController::fuel_surcharge(17187446);
ShipmentChargesController::fuel_surcharge(17187447);
ShipmentChargesController::fuel_surcharge(17187448);
ShipmentChargesController::fuel_surcharge(17187449);
ShipmentChargesController::fuel_surcharge(17187450);
ShipmentChargesController::fuel_surcharge(17187451);
ShipmentChargesController::fuel_surcharge(17187452);
ShipmentChargesController::fuel_surcharge(17187453);
ShipmentChargesController::fuel_surcharge(17187454);
ShipmentChargesController::fuel_surcharge(17187455);
ShipmentChargesController::fuel_surcharge(17187456);
ShipmentChargesController::fuel_surcharge(17187457);
ShipmentChargesController::fuel_surcharge(17187458);
ShipmentChargesController::fuel_surcharge(17187459);
ShipmentChargesController::fuel_surcharge(17187460);
ShipmentChargesController::fuel_surcharge(17187461);
ShipmentChargesController::fuel_surcharge(17187462);
ShipmentChargesController::fuel_surcharge(17187463);
ShipmentChargesController::fuel_surcharge(17187464);
ShipmentChargesController::fuel_surcharge(17169362);
ShipmentChargesController::fuel_surcharge(17160488);
ShipmentChargesController::fuel_surcharge(17160484);
ShipmentChargesController::fuel_surcharge(17173910);
ShipmentChargesController::fuel_surcharge(17152604);
ShipmentChargesController::fuel_surcharge(17174202);
ShipmentChargesController::fuel_surcharge(17176095);
ShipmentChargesController::fuel_surcharge(17171091);
ShipmentChargesController::fuel_surcharge(17154346);
ShipmentChargesController::fuel_surcharge(17143782);
ShipmentChargesController::fuel_surcharge(16966909);
ShipmentChargesController::fuel_surcharge(17169376);
ShipmentChargesController::fuel_surcharge(17149857);
ShipmentChargesController::fuel_surcharge(17100165);
ShipmentChargesController::fuel_surcharge(17160487);
ShipmentChargesController::fuel_surcharge(17104632);
ShipmentChargesController::fuel_surcharge(17144010);
ShipmentChargesController::fuel_surcharge(17152547);
ShipmentChargesController::fuel_surcharge(17075578);
ShipmentChargesController::fuel_surcharge(17169746);
ShipmentChargesController::fuel_surcharge(17152641);
ShipmentChargesController::fuel_surcharge(17149906);
ShipmentChargesController::fuel_surcharge(17104650);
ShipmentChargesController::fuel_surcharge(17149825);
ShipmentChargesController::fuel_surcharge(17104809);
ShipmentChargesController::fuel_surcharge(17130612);
ShipmentChargesController::fuel_surcharge(17117130);
ShipmentChargesController::fuel_surcharge(17117178);
ShipmentChargesController::fuel_surcharge(17152635);
ShipmentChargesController::fuel_surcharge(17174071);
ShipmentChargesController::fuel_surcharge(17173633);
ShipmentChargesController::fuel_surcharge(17150290);
ShipmentChargesController::fuel_surcharge(17104708);
ShipmentChargesController::fuel_surcharge(17104810);
ShipmentChargesController::fuel_surcharge(17151604);
ShipmentChargesController::fuel_surcharge(17152576);
ShipmentChargesController::fuel_surcharge(17173528);
ShipmentChargesController::fuel_surcharge(17100893);
ShipmentChargesController::fuel_surcharge(17143650);
ShipmentChargesController::fuel_surcharge(17072489);
ShipmentChargesController::fuel_surcharge(17062285);
ShipmentChargesController::fuel_surcharge(17172993);
ShipmentChargesController::fuel_surcharge(17104669);
ShipmentChargesController::fuel_surcharge(17173544);
ShipmentChargesController::fuel_surcharge(17152634);
ShipmentChargesController::fuel_surcharge(17169875);
ShipmentChargesController::fuel_surcharge(17152640);
ShipmentChargesController::fuel_surcharge(17173957);
ShipmentChargesController::fuel_surcharge(17170781);
ShipmentChargesController::fuel_surcharge(17170770);
ShipmentChargesController::fuel_surcharge(17149101);
ShipmentChargesController::fuel_surcharge(17147648);
ShipmentChargesController::fuel_surcharge(17149156);
ShipmentChargesController::fuel_surcharge(17149129);
ShipmentChargesController::fuel_surcharge(17149037);
ShipmentChargesController::fuel_surcharge(17176920);
ShipmentChargesController::fuel_surcharge(17176916);
ShipmentChargesController::fuel_surcharge(17176915);
ShipmentChargesController::fuel_surcharge(17171645);
ShipmentChargesController::fuel_surcharge(17158950);
ShipmentChargesController::fuel_surcharge(17125164);
ShipmentChargesController::fuel_surcharge(17147811);
ShipmentChargesController::fuel_surcharge(17180963);
ShipmentChargesController::fuel_surcharge(17173241);
ShipmentChargesController::fuel_surcharge(17149209);
ShipmentChargesController::fuel_surcharge(17149214);
ShipmentChargesController::fuel_surcharge(17173149);
ShipmentChargesController::fuel_surcharge(17149061);
ShipmentChargesController::fuel_surcharge(17149216);
ShipmentChargesController::fuel_surcharge(17173235);
ShipmentChargesController::fuel_surcharge(17149171);
ShipmentChargesController::fuel_surcharge(17149257);
ShipmentChargesController::fuel_surcharge(17149235);
ShipmentChargesController::fuel_surcharge(17173131);
ShipmentChargesController::fuel_surcharge(17149233);
ShipmentChargesController::fuel_surcharge(17173213);
ShipmentChargesController::fuel_surcharge(17146978);
ShipmentChargesController::fuel_surcharge(17146979);
ShipmentChargesController::fuel_surcharge(17173152);
ShipmentChargesController::fuel_surcharge(17149202);
ShipmentChargesController::fuel_surcharge(17149225);
ShipmentChargesController::fuel_surcharge(17149211);
ShipmentChargesController::fuel_surcharge(17189308);
ShipmentChargesController::fuel_surcharge(17189309);
ShipmentChargesController::fuel_surcharge(17189311);
ShipmentChargesController::fuel_surcharge(17189312);
ShipmentChargesController::fuel_surcharge(17177842);
ShipmentChargesController::fuel_surcharge(17177751);
ShipmentChargesController::fuel_surcharge(17174376);
ShipmentChargesController::fuel_surcharge(17178051);
ShipmentChargesController::fuel_surcharge(17177237);
ShipmentChargesController::fuel_surcharge(17177767);
ShipmentChargesController::fuel_surcharge(17176881);
ShipmentChargesController::fuel_surcharge(17167373);
ShipmentChargesController::fuel_surcharge(17168038);
ShipmentChargesController::fuel_surcharge(17174027);
ShipmentChargesController::fuel_surcharge(17173992);
ShipmentChargesController::fuel_surcharge(17173984);
ShipmentChargesController::fuel_surcharge(17173997);
ShipmentChargesController::fuel_surcharge(17173962);
ShipmentChargesController::fuel_surcharge(17173956);
ShipmentChargesController::fuel_surcharge(17182443);
ShipmentChargesController::fuel_surcharge(17182441);
ShipmentChargesController::fuel_surcharge(17174135);
ShipmentChargesController::fuel_surcharge(17184642);
ShipmentChargesController::fuel_surcharge(17158762);
ShipmentChargesController::fuel_surcharge(17173318);
ShipmentChargesController::fuel_surcharge(17185250);
ShipmentChargesController::fuel_surcharge(17184241);
ShipmentChargesController::fuel_surcharge(17186023);
ShipmentChargesController::fuel_surcharge(17184697);
ShipmentChargesController::fuel_surcharge(17184796);
ShipmentChargesController::fuel_surcharge(17184541);
ShipmentChargesController::fuel_surcharge(17184635);
ShipmentChargesController::fuel_surcharge(17185200);
ShipmentChargesController::fuel_surcharge(17184326);
ShipmentChargesController::fuel_surcharge(17176299);
ShipmentChargesController::fuel_surcharge(17179835);
ShipmentChargesController::fuel_surcharge(17179991);
ShipmentChargesController::fuel_surcharge(17183339);
ShipmentChargesController::fuel_surcharge(17179887);
ShipmentChargesController::fuel_surcharge(17180784);
ShipmentChargesController::fuel_surcharge(17181754);
ShipmentChargesController::fuel_surcharge(17178657);
ShipmentChargesController::fuel_surcharge(17183728);
ShipmentChargesController::fuel_surcharge(17178654);
ShipmentChargesController::fuel_surcharge(17179866);
ShipmentChargesController::fuel_surcharge(17176300);
ShipmentChargesController::fuel_surcharge(17182419);
ShipmentChargesController::fuel_surcharge(17176292);
ShipmentChargesController::fuel_surcharge(17176282);
ShipmentChargesController::fuel_surcharge(17176278);
ShipmentChargesController::fuel_surcharge(17176297);
ShipmentChargesController::fuel_surcharge(17181912);
ShipmentChargesController::fuel_surcharge(17176293);
ShipmentChargesController::fuel_surcharge(17181023);
ShipmentChargesController::fuel_surcharge(17181041);
ShipmentChargesController::fuel_surcharge(17183720);
ShipmentChargesController::fuel_surcharge(17160106);
ShipmentChargesController::fuel_surcharge(17176291);
ShipmentChargesController::fuel_surcharge(17180781);
ShipmentChargesController::fuel_surcharge(17178428);
ShipmentChargesController::fuel_surcharge(17178430);
ShipmentChargesController::fuel_surcharge(17188348);
ShipmentChargesController::fuel_surcharge(17153240);
ShipmentChargesController::fuel_surcharge(17175085);
ShipmentChargesController::fuel_surcharge(17189175);
ShipmentChargesController::fuel_surcharge(17175075);
ShipmentChargesController::fuel_surcharge(17175069);
ShipmentChargesController::fuel_surcharge(17179999);
ShipmentChargesController::fuel_surcharge(17165694);
ShipmentChargesController::fuel_surcharge(17179851);
ShipmentChargesController::fuel_surcharge(17166773);
ShipmentChargesController::fuel_surcharge(17184710);
ShipmentChargesController::fuel_surcharge(17138590);
ShipmentChargesController::fuel_surcharge(17158237);
ShipmentChargesController::fuel_surcharge(17178515);
ShipmentChargesController::fuel_surcharge(17182719);
ShipmentChargesController::fuel_surcharge(17176359);
ShipmentChargesController::fuel_surcharge(17178558);
ShipmentChargesController::fuel_surcharge(17180100);
ShipmentChargesController::fuel_surcharge(17167127);
ShipmentChargesController::fuel_surcharge(17182802);
ShipmentChargesController::fuel_surcharge(17149601);
ShipmentChargesController::fuel_surcharge(17184704);
ShipmentChargesController::fuel_surcharge(17174401);
ShipmentChargesController::fuel_surcharge(17173827);
ShipmentChargesController::fuel_surcharge(17167323);
ShipmentChargesController::fuel_surcharge(17180955);
ShipmentChargesController::fuel_surcharge(17011855);
ShipmentChargesController::fuel_surcharge(17157128);
ShipmentChargesController::fuel_surcharge(17154648);
ShipmentChargesController::fuel_surcharge(17175187);
ShipmentChargesController::fuel_surcharge(17153265);
ShipmentChargesController::fuel_surcharge(17184926);
ShipmentChargesController::fuel_surcharge(17184929);
ShipmentChargesController::fuel_surcharge(17176381);
ShipmentChargesController::fuel_surcharge(17184927);
ShipmentChargesController::fuel_surcharge(17177083);
ShipmentChargesController::fuel_surcharge(17180224);
ShipmentChargesController::fuel_surcharge(17180348);
ShipmentChargesController::fuel_surcharge(17158318);
ShipmentChargesController::fuel_surcharge(17148649);
ShipmentChargesController::fuel_surcharge(17180328);
ShipmentChargesController::fuel_surcharge(17068333);
ShipmentChargesController::fuel_surcharge(17184707);
ShipmentChargesController::fuel_surcharge(17175198);
ShipmentChargesController::fuel_surcharge(17184930);
ShipmentChargesController::fuel_surcharge(17175196);
ShipmentChargesController::fuel_surcharge(17176438);
ShipmentChargesController::fuel_surcharge(17176371);
ShipmentChargesController::fuel_surcharge(17179646);
ShipmentChargesController::fuel_surcharge(17172511);
ShipmentChargesController::fuel_surcharge(17172522);
ShipmentChargesController::fuel_surcharge(17179623);
ShipmentChargesController::fuel_surcharge(17177885);
ShipmentChargesController::fuel_surcharge(17172176);
ShipmentChargesController::fuel_surcharge(17186413);
ShipmentChargesController::fuel_surcharge(17183948);
ShipmentChargesController::fuel_surcharge(17177884);
ShipmentChargesController::fuel_surcharge(17182256);
ShipmentChargesController::fuel_surcharge(17180605);
ShipmentChargesController::fuel_surcharge(17183940);
ShipmentChargesController::fuel_surcharge(17180473);
ShipmentChargesController::fuel_surcharge(17183860);
ShipmentChargesController::fuel_surcharge(17180493);
ShipmentChargesController::fuel_surcharge(17172217);
ShipmentChargesController::fuel_surcharge(17166554);
ShipmentChargesController::fuel_surcharge(17159652);
ShipmentChargesController::fuel_surcharge(17159801);
ShipmentChargesController::fuel_surcharge(17155650);
ShipmentChargesController::fuel_surcharge(17166882);
ShipmentChargesController::fuel_surcharge(17158867);
ShipmentChargesController::fuel_surcharge(17155712);
ShipmentChargesController::fuel_surcharge(17155016);
ShipmentChargesController::fuel_surcharge(17182989);
ShipmentChargesController::fuel_surcharge(17159218);
ShipmentChargesController::fuel_surcharge(17159161);
ShipmentChargesController::fuel_surcharge(17152663);
ShipmentChargesController::fuel_surcharge(17155807);
ShipmentChargesController::fuel_surcharge(17152471);
ShipmentChargesController::fuel_surcharge(17187366);
ShipmentChargesController::fuel_surcharge(17159966);
ShipmentChargesController::fuel_surcharge(17159182);
ShipmentChargesController::fuel_surcharge(17159907);
ShipmentChargesController::fuel_surcharge(17158911);
ShipmentChargesController::fuel_surcharge(17152682);
ShipmentChargesController::fuel_surcharge(17169394);
ShipmentChargesController::fuel_surcharge(17172483);
ShipmentChargesController::fuel_surcharge(17173625);
ShipmentChargesController::fuel_surcharge(17165603);
ShipmentChargesController::fuel_surcharge(17165789);
ShipmentChargesController::fuel_surcharge(17148954);
ShipmentChargesController::fuel_surcharge(17182356);
ShipmentChargesController::fuel_surcharge(17182351);
ShipmentChargesController::fuel_surcharge(17172567);
ShipmentChargesController::fuel_surcharge(17176072);
ShipmentChargesController::fuel_surcharge(17153152);
ShipmentChargesController::fuel_surcharge(17176127);
ShipmentChargesController::fuel_surcharge(17112204);
ShipmentChargesController::fuel_surcharge(17166107);
ShipmentChargesController::fuel_surcharge(17172380);
ShipmentChargesController::fuel_surcharge(17175872);
ShipmentChargesController::fuel_surcharge(17180346);
ShipmentChargesController::fuel_surcharge(17163133);
ShipmentChargesController::fuel_surcharge(17151614);
ShipmentChargesController::fuel_surcharge(17075433);
ShipmentChargesController::fuel_surcharge(17075395);
ShipmentChargesController::fuel_surcharge(17149815);
ShipmentChargesController::fuel_surcharge(17069909);
ShipmentChargesController::fuel_surcharge(17152568);
ShipmentChargesController::fuel_surcharge(17149830);
ShipmentChargesController::fuel_surcharge(17149914);
ShipmentChargesController::fuel_surcharge(17149829);
ShipmentChargesController::fuel_surcharge(16636954);
ShipmentChargesController::fuel_surcharge(17100172);
ShipmentChargesController::fuel_surcharge(17149851);
ShipmentChargesController::fuel_surcharge(17104634);
ShipmentChargesController::fuel_surcharge(17160477);
ShipmentChargesController::fuel_surcharge(17149892);
ShipmentChargesController::fuel_surcharge(17173854);
ShipmentChargesController::fuel_surcharge(17149909);
ShipmentChargesController::fuel_surcharge(17171728);
ShipmentChargesController::fuel_surcharge(17169967);
ShipmentChargesController::fuel_surcharge(17152580);
ShipmentChargesController::fuel_surcharge(17173365);
ShipmentChargesController::fuel_surcharge(17173202);
ShipmentChargesController::fuel_surcharge(17173085);
ShipmentChargesController::fuel_surcharge(17173360);
ShipmentChargesController::fuel_surcharge(17173364);
ShipmentChargesController::fuel_surcharge(17173075);
ShipmentChargesController::fuel_surcharge(17173359);
ShipmentChargesController::fuel_surcharge(17173106);
ShipmentChargesController::fuel_surcharge(17173251);
ShipmentChargesController::fuel_surcharge(17173177);
ShipmentChargesController::fuel_surcharge(17173289);
ShipmentChargesController::fuel_surcharge(17173283);
ShipmentChargesController::fuel_surcharge(17173109);
ShipmentChargesController::fuel_surcharge(17173172);
ShipmentChargesController::fuel_surcharge(17173171);
ShipmentChargesController::fuel_surcharge(17173330);
ShipmentChargesController::fuel_surcharge(17173064);
ShipmentChargesController::fuel_surcharge(17173089);
ShipmentChargesController::fuel_surcharge(17173329);
ShipmentChargesController::fuel_surcharge(17168959);
ShipmentChargesController::fuel_surcharge(17168734);
ShipmentChargesController::fuel_surcharge(17168726);
ShipmentChargesController::fuel_surcharge(17168739);
ShipmentChargesController::fuel_surcharge(17168735);
ShipmentChargesController::fuel_surcharge(17168730);
ShipmentChargesController::fuel_surcharge(17168719);
ShipmentChargesController::fuel_surcharge(17168724);
ShipmentChargesController::fuel_surcharge(17168738);
ShipmentChargesController::fuel_surcharge(17168727);
ShipmentChargesController::fuel_surcharge(17168725);
ShipmentChargesController::fuel_surcharge(17168733);
ShipmentChargesController::fuel_surcharge(17168717);
ShipmentChargesController::fuel_surcharge(17168743);
ShipmentChargesController::fuel_surcharge(17099787);
ShipmentChargesController::fuel_surcharge(17171046);
ShipmentChargesController::fuel_surcharge(17149114);
ShipmentChargesController::fuel_surcharge(17149051);
ShipmentChargesController::fuel_surcharge(17149065);
ShipmentChargesController::fuel_surcharge(17079821);
ShipmentChargesController::fuel_surcharge(17173299);
ShipmentChargesController::fuel_surcharge(17173328);
ShipmentChargesController::fuel_surcharge(17173285);
ShipmentChargesController::fuel_surcharge(17173277);
ShipmentChargesController::fuel_surcharge(17173284);
ShipmentChargesController::fuel_surcharge(17173295);
ShipmentChargesController::fuel_surcharge(17173286);
ShipmentChargesController::fuel_surcharge(17149012);
ShipmentChargesController::fuel_surcharge(17074048);
ShipmentChargesController::fuel_surcharge(17170595);
ShipmentChargesController::fuel_surcharge(17173613);
ShipmentChargesController::fuel_surcharge(17183927);
ShipmentChargesController::fuel_surcharge(17183644);
ShipmentChargesController::fuel_surcharge(17173585);
ShipmentChargesController::fuel_surcharge(17188240);
ShipmentChargesController::fuel_surcharge(17180723);
ShipmentChargesController::fuel_surcharge(17186449);
ShipmentChargesController::fuel_surcharge(17158731);
ShipmentChargesController::fuel_surcharge(17184212);
ShipmentChargesController::fuel_surcharge(17178555);
ShipmentChargesController::fuel_surcharge(17178019);
ShipmentChargesController::fuel_surcharge(17178485);
ShipmentChargesController::fuel_surcharge(17188173);
ShipmentChargesController::fuel_surcharge(17184282);
ShipmentChargesController::fuel_surcharge(17182770);
ShipmentChargesController::fuel_surcharge(17187315);
ShipmentChargesController::fuel_surcharge(17183100);
ShipmentChargesController::fuel_surcharge(17141371);
ShipmentChargesController::fuel_surcharge(17174482);
ShipmentChargesController::fuel_surcharge(17148375);
ShipmentChargesController::fuel_surcharge(17188634);
ShipmentChargesController::fuel_surcharge(17186830);
ShipmentChargesController::fuel_surcharge(17178773);
ShipmentChargesController::fuel_surcharge(17173658);
ShipmentChargesController::fuel_surcharge(17184435);
ShipmentChargesController::fuel_surcharge(17178115);
ShipmentChargesController::fuel_surcharge(17170603);
ShipmentChargesController::fuel_surcharge(17173597);
ShipmentChargesController::fuel_surcharge(17178517);
ShipmentChargesController::fuel_surcharge(17173593);
ShipmentChargesController::fuel_surcharge(17181596);
ShipmentChargesController::fuel_surcharge(17173630);
ShipmentChargesController::fuel_surcharge(17173504);
ShipmentChargesController::fuel_surcharge(17173762);
ShipmentChargesController::fuel_surcharge(17173572);
ShipmentChargesController::fuel_surcharge(17173578);
ShipmentChargesController::fuel_surcharge(17173866);
ShipmentChargesController::fuel_surcharge(17186152);
ShipmentChargesController::fuel_surcharge(17190528);
ShipmentChargesController::fuel_surcharge(17179325);
ShipmentChargesController::fuel_surcharge(17184847);
ShipmentChargesController::fuel_surcharge(17179214);
ShipmentChargesController::fuel_surcharge(17187762);
ShipmentChargesController::fuel_surcharge(17188961);
ShipmentChargesController::fuel_surcharge(17190382);
ShipmentChargesController::fuel_surcharge(17184161);
ShipmentChargesController::fuel_surcharge(17186393);
ShipmentChargesController::fuel_surcharge(17188805);
ShipmentChargesController::fuel_surcharge(17174384);
ShipmentChargesController::fuel_surcharge(17158430);
ShipmentChargesController::fuel_surcharge(17156775);
ShipmentChargesController::fuel_surcharge(17174332);
ShipmentChargesController::fuel_surcharge(17156933);
ShipmentChargesController::fuel_surcharge(17187765);
ShipmentChargesController::fuel_surcharge(17173605);
ShipmentChargesController::fuel_surcharge(17187688);
ShipmentChargesController::fuel_surcharge(17176454);
ShipmentChargesController::fuel_surcharge(17180261);
ShipmentChargesController::fuel_surcharge(17158306);
ShipmentChargesController::fuel_surcharge(17163604);
ShipmentChargesController::fuel_surcharge(17157685);
ShipmentChargesController::fuel_surcharge(17158117);
ShipmentChargesController::fuel_surcharge(17162978);
ShipmentChargesController::fuel_surcharge(17185408);
ShipmentChargesController::fuel_surcharge(17173167);
ShipmentChargesController::fuel_surcharge(17192010);
ShipmentChargesController::fuel_surcharge(17174241);
ShipmentChargesController::fuel_surcharge(17173530);
ShipmentChargesController::fuel_surcharge(17173891);
ShipmentChargesController::fuel_surcharge(17173523);
ShipmentChargesController::fuel_surcharge(17173858);
ShipmentChargesController::fuel_surcharge(17173631);
ShipmentChargesController::fuel_surcharge(17173494);
ShipmentChargesController::fuel_surcharge(17173557);
ShipmentChargesController::fuel_surcharge(17173898);
ShipmentChargesController::fuel_surcharge(17072376);
ShipmentChargesController::fuel_surcharge(17174169);
ShipmentChargesController::fuel_surcharge(17160109);
ShipmentChargesController::fuel_surcharge(17072318);
ShipmentChargesController::fuel_surcharge(17108353);
ShipmentChargesController::fuel_surcharge(17172762);
ShipmentChargesController::fuel_surcharge(17187377);
ShipmentChargesController::fuel_surcharge(17184168);
ShipmentChargesController::fuel_surcharge(17064655);
ShipmentChargesController::fuel_surcharge(17173549);
ShipmentChargesController::fuel_surcharge(17173517);
ShipmentChargesController::fuel_surcharge(17174265);
ShipmentChargesController::fuel_surcharge(17174312);
ShipmentChargesController::fuel_surcharge(17157607);
ShipmentChargesController::fuel_surcharge(17174303);
ShipmentChargesController::fuel_surcharge(17173908);
ShipmentChargesController::fuel_surcharge(17177717);
ShipmentChargesController::fuel_surcharge(17178066);
ShipmentChargesController::fuel_surcharge(17179121);
ShipmentChargesController::fuel_surcharge(17183312);
ShipmentChargesController::fuel_surcharge(17183083);
ShipmentChargesController::fuel_surcharge(17178815);
ShipmentChargesController::fuel_surcharge(17188787);
ShipmentChargesController::fuel_surcharge(17177891);
ShipmentChargesController::fuel_surcharge(17183032);
ShipmentChargesController::fuel_surcharge(17183959);
ShipmentChargesController::fuel_surcharge(17190485);
ShipmentChargesController::fuel_surcharge(17179681);
ShipmentChargesController::fuel_surcharge(17184137);
ShipmentChargesController::fuel_surcharge(17190487);
ShipmentChargesController::fuel_surcharge(17177239);
ShipmentChargesController::fuel_surcharge(17177890);
ShipmentChargesController::fuel_surcharge(17190486);
ShipmentChargesController::fuel_surcharge(17177888);
ShipmentChargesController::fuel_surcharge(17190488);
ShipmentChargesController::fuel_surcharge(17173120);
ShipmentChargesController::fuel_surcharge(17192207);
ShipmentChargesController::fuel_surcharge(17180270);
ShipmentChargesController::fuel_surcharge(17176396);
ShipmentChargesController::fuel_surcharge(17167143);
ShipmentChargesController::fuel_surcharge(17172230);
ShipmentChargesController::fuel_surcharge(17171705);
ShipmentChargesController::fuel_surcharge(17171682);
ShipmentChargesController::fuel_surcharge(17168825);
ShipmentChargesController::fuel_surcharge(17172078);
ShipmentChargesController::fuel_surcharge(17173569);
ShipmentChargesController::fuel_surcharge(17171934);
ShipmentChargesController::fuel_surcharge(17184824);
ShipmentChargesController::fuel_surcharge(17185007);
ShipmentChargesController::fuel_surcharge(17164486);
ShipmentChargesController::fuel_surcharge(17185087);
ShipmentChargesController::fuel_surcharge(17171716);
ShipmentChargesController::fuel_surcharge(17167509);
ShipmentChargesController::fuel_surcharge(17184740);
ShipmentChargesController::fuel_surcharge(17188002);
ShipmentChargesController::fuel_surcharge(17185141);
ShipmentChargesController::fuel_surcharge(17184873);
ShipmentChargesController::fuel_surcharge(17190551);
ShipmentChargesController::fuel_surcharge(17184955);
ShipmentChargesController::fuel_surcharge(17176902);
ShipmentChargesController::fuel_surcharge(17182361);
ShipmentChargesController::fuel_surcharge(17173442);
ShipmentChargesController::fuel_surcharge(17177326);
ShipmentChargesController::fuel_surcharge(17173409);
ShipmentChargesController::fuel_surcharge(17172979);
ShipmentChargesController::fuel_surcharge(17191745);
ShipmentChargesController::fuel_surcharge(17188596);
ShipmentChargesController::fuel_surcharge(17178684);
ShipmentChargesController::fuel_surcharge(17173527);
ShipmentChargesController::fuel_surcharge(17173909);
ShipmentChargesController::fuel_surcharge(17153505);
ShipmentChargesController::fuel_surcharge(17173782);
ShipmentChargesController::fuel_surcharge(17173614);
ShipmentChargesController::fuel_surcharge(17173653);
ShipmentChargesController::fuel_surcharge(17182895);
ShipmentChargesController::fuel_surcharge(17072469);
ShipmentChargesController::fuel_surcharge(17173797);
ShipmentChargesController::fuel_surcharge(17174771);
ShipmentChargesController::fuel_surcharge(17174766);
ShipmentChargesController::fuel_surcharge(17174769);
ShipmentChargesController::fuel_surcharge(17174770);
ShipmentChargesController::fuel_surcharge(17174768);
ShipmentChargesController::fuel_surcharge(17174476);
ShipmentChargesController::fuel_surcharge(17174432);
ShipmentChargesController::fuel_surcharge(17170434);
ShipmentChargesController::fuel_surcharge(17173589);
ShipmentChargesController::fuel_surcharge(17157048);
ShipmentChargesController::fuel_surcharge(17071170);
ShipmentChargesController::fuel_surcharge(17157541);
ShipmentChargesController::fuel_surcharge(17158265);
ShipmentChargesController::fuel_surcharge(17178442);
ShipmentChargesController::fuel_surcharge(17174997);
ShipmentChargesController::fuel_surcharge(17190611);
ShipmentChargesController::fuel_surcharge(17178609);
ShipmentChargesController::fuel_surcharge(17154837);
ShipmentChargesController::fuel_surcharge(17173786);
ShipmentChargesController::fuel_surcharge(17173594);
ShipmentChargesController::fuel_surcharge(17173591);
ShipmentChargesController::fuel_surcharge(17174402);
ShipmentChargesController::fuel_surcharge(17151221);
ShipmentChargesController::fuel_surcharge(17184157);
ShipmentChargesController::fuel_surcharge(17164084);
ShipmentChargesController::fuel_surcharge(17172261);
ShipmentChargesController::fuel_surcharge(17154654);
ShipmentChargesController::fuel_surcharge(17173600);
ShipmentChargesController::fuel_surcharge(17173636);
ShipmentChargesController::fuel_surcharge(17150256);
ShipmentChargesController::fuel_surcharge(17166385);
ShipmentChargesController::fuel_surcharge(17184159);
ShipmentChargesController::fuel_surcharge(17183267);
ShipmentChargesController::fuel_surcharge(17174336);
ShipmentChargesController::fuel_surcharge(17173553);
ShipmentChargesController::fuel_surcharge(17157134);
ShipmentChargesController::fuel_surcharge(17174444);
ShipmentChargesController::fuel_surcharge(17168652);
ShipmentChargesController::fuel_surcharge(17049129);
ShipmentChargesController::fuel_surcharge(17174044);
ShipmentChargesController::fuel_surcharge(17155245);
ShipmentChargesController::fuel_surcharge(17191593);
ShipmentChargesController::fuel_surcharge(17179281);
ShipmentChargesController::fuel_surcharge(17173558);
ShipmentChargesController::fuel_surcharge(17173509);
ShipmentChargesController::fuel_surcharge(17173480);
ShipmentChargesController::fuel_surcharge(17184167);
ShipmentChargesController::fuel_surcharge(17188503);
ShipmentChargesController::fuel_surcharge(17108268);
ShipmentChargesController::fuel_surcharge(17174451);
ShipmentChargesController::fuel_surcharge(17188314);
ShipmentChargesController::fuel_surcharge(17156552);
ShipmentChargesController::fuel_surcharge(17189194);
ShipmentChargesController::fuel_surcharge(17185473);
ShipmentChargesController::fuel_surcharge(17187153);
ShipmentChargesController::fuel_surcharge(17189329);
ShipmentChargesController::fuel_surcharge(17157609);
ShipmentChargesController::fuel_surcharge(17179710);
ShipmentChargesController::fuel_surcharge(17158281);
ShipmentChargesController::fuel_surcharge(17064668);
ShipmentChargesController::fuel_surcharge(17179031);
ShipmentChargesController::fuel_surcharge(17188711);
ShipmentChargesController::fuel_surcharge(17189743);
ShipmentChargesController::fuel_surcharge(17173856);
ShipmentChargesController::fuel_surcharge(17177295);
ShipmentChargesController::fuel_surcharge(17191031);
ShipmentChargesController::fuel_surcharge(17182193);
ShipmentChargesController::fuel_surcharge(17158302);
ShipmentChargesController::fuel_surcharge(17182640);
ShipmentChargesController::fuel_surcharge(17098485);
ShipmentChargesController::fuel_surcharge(17182497);
ShipmentChargesController::fuel_surcharge(17184407);
ShipmentChargesController::fuel_surcharge(17190373);
ShipmentChargesController::fuel_surcharge(17188844);
ShipmentChargesController::fuel_surcharge(17188803);
ShipmentChargesController::fuel_surcharge(17189513);
ShipmentChargesController::fuel_surcharge(17186000);
ShipmentChargesController::fuel_surcharge(17186009);
ShipmentChargesController::fuel_surcharge(17188267);
ShipmentChargesController::fuel_surcharge(17186093);
ShipmentChargesController::fuel_surcharge(17188905);
ShipmentChargesController::fuel_surcharge(17191340);
ShipmentChargesController::fuel_surcharge(17189158);
ShipmentChargesController::fuel_surcharge(17186068);
ShipmentChargesController::fuel_surcharge(17190836);
ShipmentChargesController::fuel_surcharge(17186021);
ShipmentChargesController::fuel_surcharge(17185413);
ShipmentChargesController::fuel_surcharge(17190779);
ShipmentChargesController::fuel_surcharge(17177045);
ShipmentChargesController::fuel_surcharge(17177047);
ShipmentChargesController::fuel_surcharge(17191706);
ShipmentChargesController::fuel_surcharge(17186487);
ShipmentChargesController::fuel_surcharge(17190832);
ShipmentChargesController::fuel_surcharge(17172019);
ShipmentChargesController::fuel_surcharge(17189522);
ShipmentChargesController::fuel_surcharge(17189525);
ShipmentChargesController::fuel_surcharge(17180686);
ShipmentChargesController::fuel_surcharge(17180648);
ShipmentChargesController::fuel_surcharge(17177046);
ShipmentChargesController::fuel_surcharge(17171977);
ShipmentChargesController::fuel_surcharge(17190886);
ShipmentChargesController::fuel_surcharge(17150773);
ShipmentChargesController::fuel_surcharge(17175386);
ShipmentChargesController::fuel_surcharge(17173387);
ShipmentChargesController::fuel_surcharge(17190834);
ShipmentChargesController::fuel_surcharge(17190838);
ShipmentChargesController::fuel_surcharge(17180698);
ShipmentChargesController::fuel_surcharge(17192237);
ShipmentChargesController::fuel_surcharge(17176789);
ShipmentChargesController::fuel_surcharge(17180307);
ShipmentChargesController::fuel_surcharge(17180032);
ShipmentChargesController::fuel_surcharge(17136545);
ShipmentChargesController::fuel_surcharge(17191605);
ShipmentChargesController::fuel_surcharge(17181209);
ShipmentChargesController::fuel_surcharge(17178991);
ShipmentChargesController::fuel_surcharge(17183190);
ShipmentChargesController::fuel_surcharge(17187202);
ShipmentChargesController::fuel_surcharge(17182701);
ShipmentChargesController::fuel_surcharge(17186536);
ShipmentChargesController::fuel_surcharge(17186795);
ShipmentChargesController::fuel_surcharge(17190845);
ShipmentChargesController::fuel_surcharge(17176349);
ShipmentChargesController::fuel_surcharge(17191847);
ShipmentChargesController::fuel_surcharge(17191849);
ShipmentChargesController::fuel_surcharge(17176350);
ShipmentChargesController::fuel_surcharge(17191833);
ShipmentChargesController::fuel_surcharge(17191854);
ShipmentChargesController::fuel_surcharge(17191853);
ShipmentChargesController::fuel_surcharge(17191835);
ShipmentChargesController::fuel_surcharge(17187746);
ShipmentChargesController::fuel_surcharge(17185396);
ShipmentChargesController::fuel_surcharge(17188846);
ShipmentChargesController::fuel_surcharge(17174145);
ShipmentChargesController::fuel_surcharge(17164499);
ShipmentChargesController::fuel_surcharge(17187693);
ShipmentChargesController::fuel_surcharge(17184868);
ShipmentChargesController::fuel_surcharge(17176829);
ShipmentChargesController::fuel_surcharge(17185452);
ShipmentChargesController::fuel_surcharge(17184799);
ShipmentChargesController::fuel_surcharge(17186144);
ShipmentChargesController::fuel_surcharge(17185476);
ShipmentChargesController::fuel_surcharge(17164598);
ShipmentChargesController::fuel_surcharge(17185655);
ShipmentChargesController::fuel_surcharge(17185604);
ShipmentChargesController::fuel_surcharge(17185527);
ShipmentChargesController::fuel_surcharge(17185531);
ShipmentChargesController::fuel_surcharge(17184980);
ShipmentChargesController::fuel_surcharge(17184978);
ShipmentChargesController::fuel_surcharge(17161694);
ShipmentChargesController::fuel_surcharge(17163554);
ShipmentChargesController::fuel_surcharge(17163551);
ShipmentChargesController::fuel_surcharge(17110602);
ShipmentChargesController::fuel_surcharge(17190129);
ShipmentChargesController::fuel_surcharge(17190168);
ShipmentChargesController::fuel_surcharge(17190097);
ShipmentChargesController::fuel_surcharge(17189237);
ShipmentChargesController::fuel_surcharge(17189642);
ShipmentChargesController::fuel_surcharge(17189983);
ShipmentChargesController::fuel_surcharge(17188710);
ShipmentChargesController::fuel_surcharge(17188934);
ShipmentChargesController::fuel_surcharge(17189671);
ShipmentChargesController::fuel_surcharge(17186155);
ShipmentChargesController::fuel_surcharge(17192223);
ShipmentChargesController::fuel_surcharge(17184973);
ShipmentChargesController::fuel_surcharge(17191827);
ShipmentChargesController::fuel_surcharge(17191500);
ShipmentChargesController::fuel_surcharge(17190807);
ShipmentChargesController::fuel_surcharge(17183684);
ShipmentChargesController::fuel_surcharge(17185931);
ShipmentChargesController::fuel_surcharge(17192586);
ShipmentChargesController::fuel_surcharge(17184967);
ShipmentChargesController::fuel_surcharge(17184982);
ShipmentChargesController::fuel_surcharge(17183090);
ShipmentChargesController::fuel_surcharge(17184363);
ShipmentChargesController::fuel_surcharge(17182918);
ShipmentChargesController::fuel_surcharge(17183152);
ShipmentChargesController::fuel_surcharge(17177187);
ShipmentChargesController::fuel_surcharge(17186111);
ShipmentChargesController::fuel_surcharge(17191737);
ShipmentChargesController::fuel_surcharge(17191393);
ShipmentChargesController::fuel_surcharge(17190667);
ShipmentChargesController::fuel_surcharge(17185664);
ShipmentChargesController::fuel_surcharge(17186142);
ShipmentChargesController::fuel_surcharge(17191474);
ShipmentChargesController::fuel_surcharge(17191617);
ShipmentChargesController::fuel_surcharge(17191654);
ShipmentChargesController::fuel_surcharge(17191302);
ShipmentChargesController::fuel_surcharge(17159884);
ShipmentChargesController::fuel_surcharge(17191756);
ShipmentChargesController::fuel_surcharge(17186418);
ShipmentChargesController::fuel_surcharge(17191568);
ShipmentChargesController::fuel_surcharge(17190030);
ShipmentChargesController::fuel_surcharge(17191739);
ShipmentChargesController::fuel_surcharge(17182366);
ShipmentChargesController::fuel_surcharge(17181096);
ShipmentChargesController::fuel_surcharge(17181864);
ShipmentChargesController::fuel_surcharge(17181295);
ShipmentChargesController::fuel_surcharge(17181084);
ShipmentChargesController::fuel_surcharge(17181079);
ShipmentChargesController::fuel_surcharge(17182591);
ShipmentChargesController::fuel_surcharge(17181106);
ShipmentChargesController::fuel_surcharge(17180918);
ShipmentChargesController::fuel_surcharge(17181630);
ShipmentChargesController::fuel_surcharge(17180921);
ShipmentChargesController::fuel_surcharge(17180925);
ShipmentChargesController::fuel_surcharge(17181296);
ShipmentChargesController::fuel_surcharge(17182380);
ShipmentChargesController::fuel_surcharge(17182368);
ShipmentChargesController::fuel_surcharge(17187566);
ShipmentChargesController::fuel_surcharge(17187884);
ShipmentChargesController::fuel_surcharge(17187858);
ShipmentChargesController::fuel_surcharge(17182363);
ShipmentChargesController::fuel_surcharge(17181636);
ShipmentChargesController::fuel_surcharge(17187239);
ShipmentChargesController::fuel_surcharge(17187863);
ShipmentChargesController::fuel_surcharge(17182588);
ShipmentChargesController::fuel_surcharge(17181638);
ShipmentChargesController::fuel_surcharge(17187270);
ShipmentChargesController::fuel_surcharge(17187623);
ShipmentChargesController::fuel_surcharge(17187620);
ShipmentChargesController::fuel_surcharge(17187245);
ShipmentChargesController::fuel_surcharge(17187247);
ShipmentChargesController::fuel_surcharge(17187944);
ShipmentChargesController::fuel_surcharge(17193196);
ShipmentChargesController::fuel_surcharge(17193223);
ShipmentChargesController::fuel_surcharge(17193155);
ShipmentChargesController::fuel_surcharge(17193210);
ShipmentChargesController::fuel_surcharge(17193144);
ShipmentChargesController::fuel_surcharge(17193100);
ShipmentChargesController::fuel_surcharge(17159407);
ShipmentChargesController::fuel_surcharge(17159908);
ShipmentChargesController::fuel_surcharge(17176065);
ShipmentChargesController::fuel_surcharge(17183025);
ShipmentChargesController::fuel_surcharge(17186934);
ShipmentChargesController::fuel_surcharge(17176554);
ShipmentChargesController::fuel_surcharge(17182725);
ShipmentChargesController::fuel_surcharge(17177681);
ShipmentChargesController::fuel_surcharge(17177142);
ShipmentChargesController::fuel_surcharge(17185459);
ShipmentChargesController::fuel_surcharge(17178299);
ShipmentChargesController::fuel_surcharge(17178117);
ShipmentChargesController::fuel_surcharge(17176356);
ShipmentChargesController::fuel_surcharge(17191784);
ShipmentChargesController::fuel_surcharge(17180622);
ShipmentChargesController::fuel_surcharge(17183207);
ShipmentChargesController::fuel_surcharge(17176337);
ShipmentChargesController::fuel_surcharge(17176325);
ShipmentChargesController::fuel_surcharge(17187859);
ShipmentChargesController::fuel_surcharge(17176063);
ShipmentChargesController::fuel_surcharge(17182884);
ShipmentChargesController::fuel_surcharge(17180654);
ShipmentChargesController::fuel_surcharge(17180610);
ShipmentChargesController::fuel_surcharge(17182997);
ShipmentChargesController::fuel_surcharge(17192364);
ShipmentChargesController::fuel_surcharge(17182280);
ShipmentChargesController::fuel_surcharge(17178352);
ShipmentChargesController::fuel_surcharge(17180524);
ShipmentChargesController::fuel_surcharge(17176363);
ShipmentChargesController::fuel_surcharge(17182083);
ShipmentChargesController::fuel_surcharge(17183109);
ShipmentChargesController::fuel_surcharge(17175932);
ShipmentChargesController::fuel_surcharge(17182157);
ShipmentChargesController::fuel_surcharge(17184460);
ShipmentChargesController::fuel_surcharge(17193085);
ShipmentChargesController::fuel_surcharge(17193080);
ShipmentChargesController::fuel_surcharge(17193084);
ShipmentChargesController::fuel_surcharge(17193076);
ShipmentChargesController::fuel_surcharge(17178681);
ShipmentChargesController::fuel_surcharge(17187983);
ShipmentChargesController::fuel_surcharge(17193257);
ShipmentChargesController::fuel_surcharge(17175641);
ShipmentChargesController::fuel_surcharge(17175535);
ShipmentChargesController::fuel_surcharge(17175712);
ShipmentChargesController::fuel_surcharge(17175363);
ShipmentChargesController::fuel_surcharge(17175540);
ShipmentChargesController::fuel_surcharge(17175723);
ShipmentChargesController::fuel_surcharge(17177840);
ShipmentChargesController::fuel_surcharge(17188230);
ShipmentChargesController::fuel_surcharge(17181706);
ShipmentChargesController::fuel_surcharge(17176385);
ShipmentChargesController::fuel_surcharge(17183186);
ShipmentChargesController::fuel_surcharge(17184230);
ShipmentChargesController::fuel_surcharge(17184290);
ShipmentChargesController::fuel_surcharge(17180669);
ShipmentChargesController::fuel_surcharge(17186834);
ShipmentChargesController::fuel_surcharge(17180676);
ShipmentChargesController::fuel_surcharge(17180668);
ShipmentChargesController::fuel_surcharge(17180672);
ShipmentChargesController::fuel_surcharge(17117271);
ShipmentChargesController::fuel_surcharge(17180673);
ShipmentChargesController::fuel_surcharge(17180670);
ShipmentChargesController::fuel_surcharge(17184143);
ShipmentChargesController::fuel_surcharge(17193231);
ShipmentChargesController::fuel_surcharge(17180671);
ShipmentChargesController::fuel_surcharge(17180664);
ShipmentChargesController::fuel_surcharge(17183668);
ShipmentChargesController::fuel_surcharge(17178037);
ShipmentChargesController::fuel_surcharge(17180667);
ShipmentChargesController::fuel_surcharge(17180674);
ShipmentChargesController::fuel_surcharge(17180677);
ShipmentChargesController::fuel_surcharge(17184035);
ShipmentChargesController::fuel_surcharge(17168648);
ShipmentChargesController::fuel_surcharge(17168936);
ShipmentChargesController::fuel_surcharge(17168358);
ShipmentChargesController::fuel_surcharge(17167863);
ShipmentChargesController::fuel_surcharge(17182245);
ShipmentChargesController::fuel_surcharge(17168055);
ShipmentChargesController::fuel_surcharge(17182592);
ShipmentChargesController::fuel_surcharge(17167457);
ShipmentChargesController::fuel_surcharge(17181990);
ShipmentChargesController::fuel_surcharge(17168277);
ShipmentChargesController::fuel_surcharge(17180688);
ShipmentChargesController::fuel_surcharge(17186029);
ShipmentChargesController::fuel_surcharge(17178796);
ShipmentChargesController::fuel_surcharge(17186461);
ShipmentChargesController::fuel_surcharge(17186272);
ShipmentChargesController::fuel_surcharge(17178800);
ShipmentChargesController::fuel_surcharge(17144245);
ShipmentChargesController::fuel_surcharge(17181367);
ShipmentChargesController::fuel_surcharge(17180585);
ShipmentChargesController::fuel_surcharge(17185280);
ShipmentChargesController::fuel_surcharge(17183327);
ShipmentChargesController::fuel_surcharge(17144338);
ShipmentChargesController::fuel_surcharge(17181801);
ShipmentChargesController::fuel_surcharge(17183234);
ShipmentChargesController::fuel_surcharge(17186269);
ShipmentChargesController::fuel_surcharge(17183873);
ShipmentChargesController::fuel_surcharge(17161284);
ShipmentChargesController::fuel_surcharge(17185716);
ShipmentChargesController::fuel_surcharge(17183842);
ShipmentChargesController::fuel_surcharge(17182534);
ShipmentChargesController::fuel_surcharge(17182290);
ShipmentChargesController::fuel_surcharge(17183138);
ShipmentChargesController::fuel_surcharge(17174873);
ShipmentChargesController::fuel_surcharge(17174718);
ShipmentChargesController::fuel_surcharge(17160435);
ShipmentChargesController::fuel_surcharge(17186133);
ShipmentChargesController::fuel_surcharge(17171411);
ShipmentChargesController::fuel_surcharge(17171103);
ShipmentChargesController::fuel_surcharge(17172005);
ShipmentChargesController::fuel_surcharge(17180317);
ShipmentChargesController::fuel_surcharge(17184559);
ShipmentChargesController::fuel_surcharge(17177761);
ShipmentChargesController::fuel_surcharge(17183411);
ShipmentChargesController::fuel_surcharge(17180316);
ShipmentChargesController::fuel_surcharge(17176303);
ShipmentChargesController::fuel_surcharge(17176604);
ShipmentChargesController::fuel_surcharge(17186147);
ShipmentChargesController::fuel_surcharge(17181216);
ShipmentChargesController::fuel_surcharge(17167263);
ShipmentChargesController::fuel_surcharge(17183926);
ShipmentChargesController::fuel_surcharge(17179448);
ShipmentChargesController::fuel_surcharge(17186662);
ShipmentChargesController::fuel_surcharge(17186010);
ShipmentChargesController::fuel_surcharge(17187312);
ShipmentChargesController::fuel_surcharge(17166101);
ShipmentChargesController::fuel_surcharge(17182409);
ShipmentChargesController::fuel_surcharge(17187565);
ShipmentChargesController::fuel_surcharge(17187608);
ShipmentChargesController::fuel_surcharge(17187598);
ShipmentChargesController::fuel_surcharge(17187656);
ShipmentChargesController::fuel_surcharge(17187583);
ShipmentChargesController::fuel_surcharge(17187644);
ShipmentChargesController::fuel_surcharge(17187586);
ShipmentChargesController::fuel_surcharge(17185370);
ShipmentChargesController::fuel_surcharge(17191281);
ShipmentChargesController::fuel_surcharge(17191550);
ShipmentChargesController::fuel_surcharge(17184817);
ShipmentChargesController::fuel_surcharge(17185294);
ShipmentChargesController::fuel_surcharge(17182643);
ShipmentChargesController::fuel_surcharge(17174940);
ShipmentChargesController::fuel_surcharge(17160377);
ShipmentChargesController::fuel_surcharge(17183023);
ShipmentChargesController::fuel_surcharge(17161548);
ShipmentChargesController::fuel_surcharge(17185826);
ShipmentChargesController::fuel_surcharge(17180866);
ShipmentChargesController::fuel_surcharge(17181681);
ShipmentChargesController::fuel_surcharge(17180315);
ShipmentChargesController::fuel_surcharge(17180973);
ShipmentChargesController::fuel_surcharge(17180797);
ShipmentChargesController::fuel_surcharge(17157833);
ShipmentChargesController::fuel_surcharge(17179571);
ShipmentChargesController::fuel_surcharge(17173734);
ShipmentChargesController::fuel_surcharge(17181783);
ShipmentChargesController::fuel_surcharge(17174327);
ShipmentChargesController::fuel_surcharge(17182767);
ShipmentChargesController::fuel_surcharge(17182658);
ShipmentChargesController::fuel_surcharge(17180663);
ShipmentChargesController::fuel_surcharge(17179297);
ShipmentChargesController::fuel_surcharge(17183962);
ShipmentChargesController::fuel_surcharge(17178006);
ShipmentChargesController::fuel_surcharge(17171059);
ShipmentChargesController::fuel_surcharge(17187515);
ShipmentChargesController::fuel_surcharge(17173701);
ShipmentChargesController::fuel_surcharge(17175918);
ShipmentChargesController::fuel_surcharge(17173705);
ShipmentChargesController::fuel_surcharge(17158041);
ShipmentChargesController::fuel_surcharge(17190853);
ShipmentChargesController::fuel_surcharge(17184344);
ShipmentChargesController::fuel_surcharge(17117756);
ShipmentChargesController::fuel_surcharge(17173871);
ShipmentChargesController::fuel_surcharge(17184220);
ShipmentChargesController::fuel_surcharge(17172238);
ShipmentChargesController::fuel_surcharge(17188673);
ShipmentChargesController::fuel_surcharge(17182289);
ShipmentChargesController::fuel_surcharge(17180482);
ShipmentChargesController::fuel_surcharge(17176498);
ShipmentChargesController::fuel_surcharge(17191950);
ShipmentChargesController::fuel_surcharge(17170857);
ShipmentChargesController::fuel_surcharge(17139018);
ShipmentChargesController::fuel_surcharge(17178636);
ShipmentChargesController::fuel_surcharge(17182516);
ShipmentChargesController::fuel_surcharge(17178594);
ShipmentChargesController::fuel_surcharge(17179090);
ShipmentChargesController::fuel_surcharge(17148688);
ShipmentChargesController::fuel_surcharge(17177845);
ShipmentChargesController::fuel_surcharge(17175821);
ShipmentChargesController::fuel_surcharge(17175831);
ShipmentChargesController::fuel_surcharge(17177529);
ShipmentChargesController::fuel_surcharge(17181998);
ShipmentChargesController::fuel_surcharge(17161884);
ShipmentChargesController::fuel_surcharge(17160881);
ShipmentChargesController::fuel_surcharge(17161168);
ShipmentChargesController::fuel_surcharge(17160945);
ShipmentChargesController::fuel_surcharge(17158160);
ShipmentChargesController::fuel_surcharge(17168125);
ShipmentChargesController::fuel_surcharge(17180566);
ShipmentChargesController::fuel_surcharge(17161616);
ShipmentChargesController::fuel_surcharge(17187355);
ShipmentChargesController::fuel_surcharge(17179947);
ShipmentChargesController::fuel_surcharge(17179950);
ShipmentChargesController::fuel_surcharge(17179953);
ShipmentChargesController::fuel_surcharge(17179960);
ShipmentChargesController::fuel_surcharge(17180254);
ShipmentChargesController::fuel_surcharge(17182793);
ShipmentChargesController::fuel_surcharge(17180075);
ShipmentChargesController::fuel_surcharge(17183423);
ShipmentChargesController::fuel_surcharge(17176023);
ShipmentChargesController::fuel_surcharge(17176418);
ShipmentChargesController::fuel_surcharge(17173440);
ShipmentChargesController::fuel_surcharge(17178344);
ShipmentChargesController::fuel_surcharge(17178671);
ShipmentChargesController::fuel_surcharge(17179572);
ShipmentChargesController::fuel_surcharge(17183998);
ShipmentChargesController::fuel_surcharge(17173576);
ShipmentChargesController::fuel_surcharge(17179082);
ShipmentChargesController::fuel_surcharge(17161477);
ShipmentChargesController::fuel_surcharge(17174904);
ShipmentChargesController::fuel_surcharge(17158503);
ShipmentChargesController::fuel_surcharge(17182486);
ShipmentChargesController::fuel_surcharge(17185323);
ShipmentChargesController::fuel_surcharge(17180833);
ShipmentChargesController::fuel_surcharge(17178365);
ShipmentChargesController::fuel_surcharge(17178860);
ShipmentChargesController::fuel_surcharge(17186583);
ShipmentChargesController::fuel_surcharge(17181395);
ShipmentChargesController::fuel_surcharge(17177958);
ShipmentChargesController::fuel_surcharge(17178154);
ShipmentChargesController::fuel_surcharge(17182665);
ShipmentChargesController::fuel_surcharge(17183651);
ShipmentChargesController::fuel_surcharge(17180634);
ShipmentChargesController::fuel_surcharge(17182327);
ShipmentChargesController::fuel_surcharge(17176487);
ShipmentChargesController::fuel_surcharge(17176125);
ShipmentChargesController::fuel_surcharge(17179624);
ShipmentChargesController::fuel_surcharge(17183649);
ShipmentChargesController::fuel_surcharge(17179609);
ShipmentChargesController::fuel_surcharge(17182709);
ShipmentChargesController::fuel_surcharge(17174148);
ShipmentChargesController::fuel_surcharge(17156711);
ShipmentChargesController::fuel_surcharge(17174351);
ShipmentChargesController::fuel_surcharge(17174350);
ShipmentChargesController::fuel_surcharge(17174372);
ShipmentChargesController::fuel_surcharge(17191534);
ShipmentChargesController::fuel_surcharge(17174370);
ShipmentChargesController::fuel_surcharge(17174355);
ShipmentChargesController::fuel_surcharge(17174349);
ShipmentChargesController::fuel_surcharge(17174359);
ShipmentChargesController::fuel_surcharge(17174361);
ShipmentChargesController::fuel_surcharge(17174373);
ShipmentChargesController::fuel_surcharge(17174354);
ShipmentChargesController::fuel_surcharge(17181057);
ShipmentChargesController::fuel_surcharge(17180756);
ShipmentChargesController::fuel_surcharge(17180760);
ShipmentChargesController::fuel_surcharge(17181061);
ShipmentChargesController::fuel_surcharge(17180758);
ShipmentChargesController::fuel_surcharge(17180751);
ShipmentChargesController::fuel_surcharge(17180757);
ShipmentChargesController::fuel_surcharge(17181063);
ShipmentChargesController::fuel_surcharge(17180755);
ShipmentChargesController::fuel_surcharge(17181059);
ShipmentChargesController::fuel_surcharge(17180759);
ShipmentChargesController::fuel_surcharge(17181062);
ShipmentChargesController::fuel_surcharge(17191657);
ShipmentChargesController::fuel_surcharge(17177970);
ShipmentChargesController::fuel_surcharge(17184510);
ShipmentChargesController::fuel_surcharge(17182261);
ShipmentChargesController::fuel_surcharge(17178958);
ShipmentChargesController::fuel_surcharge(17173547);
ShipmentChargesController::fuel_surcharge(17184207);
ShipmentChargesController::fuel_surcharge(17180135);
ShipmentChargesController::fuel_surcharge(17167371);
ShipmentChargesController::fuel_surcharge(17184001);
ShipmentChargesController::fuel_surcharge(17185934);
ShipmentChargesController::fuel_surcharge(17167577);
ShipmentChargesController::fuel_surcharge(17039737);
ShipmentChargesController::fuel_surcharge(17185889);
ShipmentChargesController::fuel_surcharge(17181073);
ShipmentChargesController::fuel_surcharge(17185420);
ShipmentChargesController::fuel_surcharge(17186336);
ShipmentChargesController::fuel_surcharge(17186378);
ShipmentChargesController::fuel_surcharge(17179612);
ShipmentChargesController::fuel_surcharge(17185281);
ShipmentChargesController::fuel_surcharge(17186400);
ShipmentChargesController::fuel_surcharge(17187295);
ShipmentChargesController::fuel_surcharge(17179537);
ShipmentChargesController::fuel_surcharge(17193013);
ShipmentChargesController::fuel_surcharge(17179451);
ShipmentChargesController::fuel_surcharge(17184429);
ShipmentChargesController::fuel_surcharge(17193417);
ShipmentChargesController::fuel_surcharge(17182711);
ShipmentChargesController::fuel_surcharge(17193839);
ShipmentChargesController::fuel_surcharge(17178730);
ShipmentChargesController::fuel_surcharge(17193769);
ShipmentChargesController::fuel_surcharge(17177812);
ShipmentChargesController::fuel_surcharge(17185764);
ShipmentChargesController::fuel_surcharge(17164853);
ShipmentChargesController::fuel_surcharge(17193435);
ShipmentChargesController::fuel_surcharge(17177402);
ShipmentChargesController::fuel_surcharge(17179684);
ShipmentChargesController::fuel_surcharge(17190821);
ShipmentChargesController::fuel_surcharge(17191046);
ShipmentChargesController::fuel_surcharge(17192861);
ShipmentChargesController::fuel_surcharge(17192868);
ShipmentChargesController::fuel_surcharge(17169859);
ShipmentChargesController::fuel_surcharge(17190763);
ShipmentChargesController::fuel_surcharge(17192863);
ShipmentChargesController::fuel_surcharge(17192800);
ShipmentChargesController::fuel_surcharge(17189193);
ShipmentChargesController::fuel_surcharge(17190863);
ShipmentChargesController::fuel_surcharge(17191220);
ShipmentChargesController::fuel_surcharge(17189646);
ShipmentChargesController::fuel_surcharge(17192858);
ShipmentChargesController::fuel_surcharge(17191438);
ShipmentChargesController::fuel_surcharge(17174311);
ShipmentChargesController::fuel_surcharge(17191025);
ShipmentChargesController::fuel_surcharge(17151058);
ShipmentChargesController::fuel_surcharge(17170932);
ShipmentChargesController::fuel_surcharge(17176965);
ShipmentChargesController::fuel_surcharge(17176984);
ShipmentChargesController::fuel_surcharge(17190787);
ShipmentChargesController::fuel_surcharge(17185385);
ShipmentChargesController::fuel_surcharge(17151050);
ShipmentChargesController::fuel_surcharge(17167557);
ShipmentChargesController::fuel_surcharge(17171026);
ShipmentChargesController::fuel_surcharge(17191670);
ShipmentChargesController::fuel_surcharge(17191975);
ShipmentChargesController::fuel_surcharge(17176267);
ShipmentChargesController::fuel_surcharge(17179618);
ShipmentChargesController::fuel_surcharge(17179296);
ShipmentChargesController::fuel_surcharge(17179263);
ShipmentChargesController::fuel_surcharge(17176122);
ShipmentChargesController::fuel_surcharge(17176255);
ShipmentChargesController::fuel_surcharge(17180840);
ShipmentChargesController::fuel_surcharge(17176257);
ShipmentChargesController::fuel_surcharge(17179611);
ShipmentChargesController::fuel_surcharge(17176330);
ShipmentChargesController::fuel_surcharge(17176269);
ShipmentChargesController::fuel_surcharge(17176334);
ShipmentChargesController::fuel_surcharge(17176058);
ShipmentChargesController::fuel_surcharge(17177739);
ShipmentChargesController::fuel_surcharge(17179614);
ShipmentChargesController::fuel_surcharge(17176274);
ShipmentChargesController::fuel_surcharge(17179607);
ShipmentChargesController::fuel_surcharge(17177740);
ShipmentChargesController::fuel_surcharge(17180703);
ShipmentChargesController::fuel_surcharge(17177268);
ShipmentChargesController::fuel_surcharge(17179604);
ShipmentChargesController::fuel_surcharge(17179617);
ShipmentChargesController::fuel_surcharge(17179679);
ShipmentChargesController::fuel_surcharge(17179620);
ShipmentChargesController::fuel_surcharge(17179606);
ShipmentChargesController::fuel_surcharge(17179603);
ShipmentChargesController::fuel_surcharge(17179446);
ShipmentChargesController::fuel_surcharge(17178319);
ShipmentChargesController::fuel_surcharge(17178697);
ShipmentChargesController::fuel_surcharge(17177923);
ShipmentChargesController::fuel_surcharge(17179213);
ShipmentChargesController::fuel_surcharge(17174429);
ShipmentChargesController::fuel_surcharge(17183582);
ShipmentChargesController::fuel_surcharge(17178103);
ShipmentChargesController::fuel_surcharge(17182581);
ShipmentChargesController::fuel_surcharge(17179024);
ShipmentChargesController::fuel_surcharge(17188392);
ShipmentChargesController::fuel_surcharge(17176253);
ShipmentChargesController::fuel_surcharge(17178467);
ShipmentChargesController::fuel_surcharge(17178625);
ShipmentChargesController::fuel_surcharge(17175878);
ShipmentChargesController::fuel_surcharge(17180435);
ShipmentChargesController::fuel_surcharge(17191948);
ShipmentChargesController::fuel_surcharge(17178633);
ShipmentChargesController::fuel_surcharge(17189567);
ShipmentChargesController::fuel_surcharge(17191776);
ShipmentChargesController::fuel_surcharge(17181044);
ShipmentChargesController::fuel_surcharge(17181227);
ShipmentChargesController::fuel_surcharge(17183481);
ShipmentChargesController::fuel_surcharge(17192588);
ShipmentChargesController::fuel_surcharge(17192336);
ShipmentChargesController::fuel_surcharge(17181177);
ShipmentChargesController::fuel_surcharge(17192563);
ShipmentChargesController::fuel_surcharge(17071791);
ShipmentChargesController::fuel_surcharge(17186843);
ShipmentChargesController::fuel_surcharge(17187683);
ShipmentChargesController::fuel_surcharge(17191898);
ShipmentChargesController::fuel_surcharge(17177140);
ShipmentChargesController::fuel_surcharge(17172943);
ShipmentChargesController::fuel_surcharge(17184517);
ShipmentChargesController::fuel_surcharge(17183389);
ShipmentChargesController::fuel_surcharge(17184502);
ShipmentChargesController::fuel_surcharge(17155726);
ShipmentChargesController::fuel_surcharge(17193208);
ShipmentChargesController::fuel_surcharge(17184496);
ShipmentChargesController::fuel_surcharge(17167680);
ShipmentChargesController::fuel_surcharge(17184513);
ShipmentChargesController::fuel_surcharge(17172935);
ShipmentChargesController::fuel_surcharge(17186561);
ShipmentChargesController::fuel_surcharge(17176960);
ShipmentChargesController::fuel_surcharge(17170910);
ShipmentChargesController::fuel_surcharge(17178710);
ShipmentChargesController::fuel_surcharge(17182710);
ShipmentChargesController::fuel_surcharge(17182704);
ShipmentChargesController::fuel_surcharge(17178084);
ShipmentChargesController::fuel_surcharge(17182749);
ShipmentChargesController::fuel_surcharge(17174198);
ShipmentChargesController::fuel_surcharge(17174506);
ShipmentChargesController::fuel_surcharge(17182550);
ShipmentChargesController::fuel_surcharge(17182703);
ShipmentChargesController::fuel_surcharge(17180780);
ShipmentChargesController::fuel_surcharge(17186573);
ShipmentChargesController::fuel_surcharge(17166076);
ShipmentChargesController::fuel_surcharge(17183187);
ShipmentChargesController::fuel_surcharge(17186330);
ShipmentChargesController::fuel_surcharge(17101532);
ShipmentChargesController::fuel_surcharge(17167218);
ShipmentChargesController::fuel_surcharge(17137098);
ShipmentChargesController::fuel_surcharge(17187122);
ShipmentChargesController::fuel_surcharge(17186784);
ShipmentChargesController::fuel_surcharge(17178911);
ShipmentChargesController::fuel_surcharge(17185457);
ShipmentChargesController::fuel_surcharge(16909463);
ShipmentChargesController::fuel_surcharge(17180560);
ShipmentChargesController::fuel_surcharge(17186864);
ShipmentChargesController::fuel_surcharge(17179952);
ShipmentChargesController::fuel_surcharge(17172457);
ShipmentChargesController::fuel_surcharge(17181249);
ShipmentChargesController::fuel_surcharge(17179300);
ShipmentChargesController::fuel_surcharge(17186532);
ShipmentChargesController::fuel_surcharge(17126104);
ShipmentChargesController::fuel_surcharge(17148524);
ShipmentChargesController::fuel_surcharge(17106721);
ShipmentChargesController::fuel_surcharge(17172519);
ShipmentChargesController::fuel_surcharge(17180253);
ShipmentChargesController::fuel_surcharge(17172569);
ShipmentChargesController::fuel_surcharge(17181383);
ShipmentChargesController::fuel_surcharge(17179959);
ShipmentChargesController::fuel_surcharge(17172453);
ShipmentChargesController::fuel_surcharge(16988468);
ShipmentChargesController::fuel_surcharge(17180258);
ShipmentChargesController::fuel_surcharge(17172672);
ShipmentChargesController::fuel_surcharge(17172487);
ShipmentChargesController::fuel_surcharge(17119532);
ShipmentChargesController::fuel_surcharge(17179964);
ShipmentChargesController::fuel_surcharge(16934530);
ShipmentChargesController::fuel_surcharge(17172121);
ShipmentChargesController::fuel_surcharge(17193460);
ShipmentChargesController::fuel_surcharge(17183048);
ShipmentChargesController::fuel_surcharge(17185154);
ShipmentChargesController::fuel_surcharge(17193744);
ShipmentChargesController::fuel_surcharge(17186835);
ShipmentChargesController::fuel_surcharge(17186849);
ShipmentChargesController::fuel_surcharge(17187031);
ShipmentChargesController::fuel_surcharge(17180376);
ShipmentChargesController::fuel_surcharge(17161016);
ShipmentChargesController::fuel_surcharge(17119751);
ShipmentChargesController::fuel_surcharge(17181742);
ShipmentChargesController::fuel_surcharge(17157206);
ShipmentChargesController::fuel_surcharge(17193391);
ShipmentChargesController::fuel_surcharge(17179020);
ShipmentChargesController::fuel_surcharge(17166236);
ShipmentChargesController::fuel_surcharge(17181563);
ShipmentChargesController::fuel_surcharge(17182991);
ShipmentChargesController::fuel_surcharge(17183307);
ShipmentChargesController::fuel_surcharge(17183491);
ShipmentChargesController::fuel_surcharge(17184506);
ShipmentChargesController::fuel_surcharge(17190175);
ShipmentChargesController::fuel_surcharge(17183046);
ShipmentChargesController::fuel_surcharge(17184124);
ShipmentChargesController::fuel_surcharge(17179195);
ShipmentChargesController::fuel_surcharge(17157318);
ShipmentChargesController::fuel_surcharge(17181350);
ShipmentChargesController::fuel_surcharge(17183691);
ShipmentChargesController::fuel_surcharge(17191876);
ShipmentChargesController::fuel_surcharge(17170913);
ShipmentChargesController::fuel_surcharge(17181071);
ShipmentChargesController::fuel_surcharge(17189002);
ShipmentChargesController::fuel_surcharge(17189281);
ShipmentChargesController::fuel_surcharge(17168096);
ShipmentChargesController::fuel_surcharge(17183172);
ShipmentChargesController::fuel_surcharge(17188022);
ShipmentChargesController::fuel_surcharge(17193713);
ShipmentChargesController::fuel_surcharge(17161599);
ShipmentChargesController::fuel_surcharge(17191840);
ShipmentChargesController::fuel_surcharge(17188633);
ShipmentChargesController::fuel_surcharge(17188913);
ShipmentChargesController::fuel_surcharge(17188956);
ShipmentChargesController::fuel_surcharge(17193236);
ShipmentChargesController::fuel_surcharge(17191879);
ShipmentChargesController::fuel_surcharge(17189199);
ShipmentChargesController::fuel_surcharge(17185186);
ShipmentChargesController::fuel_surcharge(17161997);
ShipmentChargesController::fuel_surcharge(17185492);
ShipmentChargesController::fuel_surcharge(17180134);
ShipmentChargesController::fuel_surcharge(17183074);
ShipmentChargesController::fuel_surcharge(17183738);
ShipmentChargesController::fuel_surcharge(17184237);
ShipmentChargesController::fuel_surcharge(17190138);
ShipmentChargesController::fuel_surcharge(17186971);
ShipmentChargesController::fuel_surcharge(17180067);
ShipmentChargesController::fuel_surcharge(17183127);
ShipmentChargesController::fuel_surcharge(17186893);
ShipmentChargesController::fuel_surcharge(17177290);
ShipmentChargesController::fuel_surcharge(17178652);
ShipmentChargesController::fuel_surcharge(17186438);
ShipmentChargesController::fuel_surcharge(17191417);
ShipmentChargesController::fuel_surcharge(17191422);
ShipmentChargesController::fuel_surcharge(17191411);
ShipmentChargesController::fuel_surcharge(17191400);
ShipmentChargesController::fuel_surcharge(17191398);
ShipmentChargesController::fuel_surcharge(17191403);
ShipmentChargesController::fuel_surcharge(17191407);
ShipmentChargesController::fuel_surcharge(17191401);
ShipmentChargesController::fuel_surcharge(17191412);
ShipmentChargesController::fuel_surcharge(17191395);
ShipmentChargesController::fuel_surcharge(17191397);
ShipmentChargesController::fuel_surcharge(17177257);
ShipmentChargesController::fuel_surcharge(17177330);
ShipmentChargesController::fuel_surcharge(17177361);
ShipmentChargesController::fuel_surcharge(17187308);
ShipmentChargesController::fuel_surcharge(17167278);
ShipmentChargesController::fuel_surcharge(17177116);
ShipmentChargesController::fuel_surcharge(17179588);
ShipmentChargesController::fuel_surcharge(17177095);
ShipmentChargesController::fuel_surcharge(17188294);
ShipmentChargesController::fuel_surcharge(17192543);
ShipmentChargesController::fuel_surcharge(17192532);
ShipmentChargesController::fuel_surcharge(17192537);
ShipmentChargesController::fuel_surcharge(17192445);
ShipmentChargesController::fuel_surcharge(17192450);
ShipmentChargesController::fuel_surcharge(17192545);
ShipmentChargesController::fuel_surcharge(17192339);
ShipmentChargesController::fuel_surcharge(17192441);
ShipmentChargesController::fuel_surcharge(17192442);
ShipmentChargesController::fuel_surcharge(17154539);
ShipmentChargesController::fuel_surcharge(17192330);
ShipmentChargesController::fuel_surcharge(17187701);
ShipmentChargesController::fuel_surcharge(17192447);
ShipmentChargesController::fuel_surcharge(17188017);
ShipmentChargesController::fuel_surcharge(17192345);
ShipmentChargesController::fuel_surcharge(17192344);
ShipmentChargesController::fuel_surcharge(17187050);
ShipmentChargesController::fuel_surcharge(17176014);
ShipmentChargesController::fuel_surcharge(17150174);
ShipmentChargesController::fuel_surcharge(17187023);
ShipmentChargesController::fuel_surcharge(17188419);
ShipmentChargesController::fuel_surcharge(17173771);
ShipmentChargesController::fuel_surcharge(17167088);
ShipmentChargesController::fuel_surcharge(17150164);
ShipmentChargesController::fuel_surcharge(17179592);
ShipmentChargesController::fuel_surcharge(17177395);
ShipmentChargesController::fuel_surcharge(17173654);
ShipmentChargesController::fuel_surcharge(17169467);
ShipmentChargesController::fuel_surcharge(17149103);
ShipmentChargesController::fuel_surcharge(17183043);
ShipmentChargesController::fuel_surcharge(17190342);
ShipmentChargesController::fuel_surcharge(17191353);
ShipmentChargesController::fuel_surcharge(17178388);
ShipmentChargesController::fuel_surcharge(17178394);
ShipmentChargesController::fuel_surcharge(17178189);
ShipmentChargesController::fuel_surcharge(17191106);
ShipmentChargesController::fuel_surcharge(17182358);
ShipmentChargesController::fuel_surcharge(17176088);
ShipmentChargesController::fuel_surcharge(17182627);
ShipmentChargesController::fuel_surcharge(17182644);
ShipmentChargesController::fuel_surcharge(17175842);
ShipmentChargesController::fuel_surcharge(17161473);
ShipmentChargesController::fuel_surcharge(17182608);
ShipmentChargesController::fuel_surcharge(17175900);
ShipmentChargesController::fuel_surcharge(17184434);
ShipmentChargesController::fuel_surcharge(17184775);
ShipmentChargesController::fuel_surcharge(17182618);
ShipmentChargesController::fuel_surcharge(17183502);
ShipmentChargesController::fuel_surcharge(17176703);
ShipmentChargesController::fuel_surcharge(17184769);
ShipmentChargesController::fuel_surcharge(17184772);
ShipmentChargesController::fuel_surcharge(17184768);
ShipmentChargesController::fuel_surcharge(17184781);
ShipmentChargesController::fuel_surcharge(17149052);
ShipmentChargesController::fuel_surcharge(17182646);
ShipmentChargesController::fuel_surcharge(17182641);
ShipmentChargesController::fuel_surcharge(17182598);
ShipmentChargesController::fuel_surcharge(17175917);
ShipmentChargesController::fuel_surcharge(17187081);
ShipmentChargesController::fuel_surcharge(17182628);
ShipmentChargesController::fuel_surcharge(17189605);
ShipmentChargesController::fuel_surcharge(17182600);
ShipmentChargesController::fuel_surcharge(17172459);
ShipmentChargesController::fuel_surcharge(17173777);
ShipmentChargesController::fuel_surcharge(17187658);
ShipmentChargesController::fuel_surcharge(17180491);
ShipmentChargesController::fuel_surcharge(17174151);
ShipmentChargesController::fuel_surcharge(17183110);
ShipmentChargesController::fuel_surcharge(17191733);
ShipmentChargesController::fuel_surcharge(17192606);
ShipmentChargesController::fuel_surcharge(17145115);
ShipmentChargesController::fuel_surcharge(17180455);
ShipmentChargesController::fuel_surcharge(17171055);
ShipmentChargesController::fuel_surcharge(17192850);
ShipmentChargesController::fuel_surcharge(17184217);
ShipmentChargesController::fuel_surcharge(17170653);
ShipmentChargesController::fuel_surcharge(17170719);
ShipmentChargesController::fuel_surcharge(17170661);
ShipmentChargesController::fuel_surcharge(17167102);
ShipmentChargesController::fuel_surcharge(17170667);
ShipmentChargesController::fuel_surcharge(17170687);
ShipmentChargesController::fuel_surcharge(17170665);
ShipmentChargesController::fuel_surcharge(17170642);
ShipmentChargesController::fuel_surcharge(17170685);
ShipmentChargesController::fuel_surcharge(17170670);
ShipmentChargesController::fuel_surcharge(17170694);
ShipmentChargesController::fuel_surcharge(17170713);
ShipmentChargesController::fuel_surcharge(17170707);
ShipmentChargesController::fuel_surcharge(17170649);
ShipmentChargesController::fuel_surcharge(17170677);
ShipmentChargesController::fuel_surcharge(17170688);
ShipmentChargesController::fuel_surcharge(17170671);
ShipmentChargesController::fuel_surcharge(17170702);
ShipmentChargesController::fuel_surcharge(17170680);
ShipmentChargesController::fuel_surcharge(17170663);
ShipmentChargesController::fuel_surcharge(17189279);
ShipmentChargesController::fuel_surcharge(17170692);
ShipmentChargesController::fuel_surcharge(17179185);
ShipmentChargesController::fuel_surcharge(17170710);
ShipmentChargesController::fuel_surcharge(17170704);
ShipmentChargesController::fuel_surcharge(17170703);
ShipmentChargesController::fuel_surcharge(17170646);
ShipmentChargesController::fuel_surcharge(17154817);
ShipmentChargesController::fuel_surcharge(17154412);
ShipmentChargesController::fuel_surcharge(17154621);
ShipmentChargesController::fuel_surcharge(17192496);
ShipmentChargesController::fuel_surcharge(17188610);
ShipmentChargesController::fuel_surcharge(17180393);
ShipmentChargesController::fuel_surcharge(17170681);
ShipmentChargesController::fuel_surcharge(17170675);
ShipmentChargesController::fuel_surcharge(17170644);
ShipmentChargesController::fuel_surcharge(17182440);
ShipmentChargesController::fuel_surcharge(17148384);
ShipmentChargesController::fuel_surcharge(17170652);
ShipmentChargesController::fuel_surcharge(17192678);
ShipmentChargesController::fuel_surcharge(17178940);
ShipmentChargesController::fuel_surcharge(17184568);
ShipmentChargesController::fuel_surcharge(17194598);
ShipmentChargesController::fuel_surcharge(17194599);
ShipmentChargesController::fuel_surcharge(17178941);
ShipmentChargesController::fuel_surcharge(17167756);
ShipmentChargesController::fuel_surcharge(17157464);
ShipmentChargesController::fuel_surcharge(17156870);
ShipmentChargesController::fuel_surcharge(17181855);
ShipmentChargesController::fuel_surcharge(17178723);
ShipmentChargesController::fuel_surcharge(17188648);
ShipmentChargesController::fuel_surcharge(17191759);
ShipmentChargesController::fuel_surcharge(17189137);
ShipmentChargesController::fuel_surcharge(17099076);
ShipmentChargesController::fuel_surcharge(17189058);
ShipmentChargesController::fuel_surcharge(17188337);
ShipmentChargesController::fuel_surcharge(17188306);
ShipmentChargesController::fuel_surcharge(17185650);
ShipmentChargesController::fuel_surcharge(17163879);
ShipmentChargesController::fuel_surcharge(17184153);
ShipmentChargesController::fuel_surcharge(17177329);
ShipmentChargesController::fuel_surcharge(17185463);
ShipmentChargesController::fuel_surcharge(17177758);
ShipmentChargesController::fuel_surcharge(17177621);
ShipmentChargesController::fuel_surcharge(17185047);
ShipmentChargesController::fuel_surcharge(17176999);
ShipmentChargesController::fuel_surcharge(17178209);
ShipmentChargesController::fuel_surcharge(17177624);
ShipmentChargesController::fuel_surcharge(17178195);
ShipmentChargesController::fuel_surcharge(17178199);
ShipmentChargesController::fuel_surcharge(17178182);
ShipmentChargesController::fuel_surcharge(17178207);
ShipmentChargesController::fuel_surcharge(17182625);
ShipmentChargesController::fuel_surcharge(17181417);
ShipmentChargesController::fuel_surcharge(17191882);
ShipmentChargesController::fuel_surcharge(17191173);
ShipmentChargesController::fuel_surcharge(17175296);
ShipmentChargesController::fuel_surcharge(17190109);
ShipmentChargesController::fuel_surcharge(17184037);
ShipmentChargesController::fuel_surcharge(17175907);
ShipmentChargesController::fuel_surcharge(17182184);
ShipmentChargesController::fuel_surcharge(17182188);
ShipmentChargesController::fuel_surcharge(17175905);
ShipmentChargesController::fuel_surcharge(17182194);
ShipmentChargesController::fuel_surcharge(17174430);
ShipmentChargesController::fuel_surcharge(17184314);
ShipmentChargesController::fuel_surcharge(17180996);
ShipmentChargesController::fuel_surcharge(17162375);
ShipmentChargesController::fuel_surcharge(17180344);
ShipmentChargesController::fuel_surcharge(17168279);
ShipmentChargesController::fuel_surcharge(17182185);
ShipmentChargesController::fuel_surcharge(17182164);
ShipmentChargesController::fuel_surcharge(17175909);
ShipmentChargesController::fuel_surcharge(17175906);
ShipmentChargesController::fuel_surcharge(17179412);
ShipmentChargesController::fuel_surcharge(17182158);
ShipmentChargesController::fuel_surcharge(17168285);
ShipmentChargesController::fuel_surcharge(17179086);
ShipmentChargesController::fuel_surcharge(17189160);
ShipmentChargesController::fuel_surcharge(17179231);
ShipmentChargesController::fuel_surcharge(17175908);
ShipmentChargesController::fuel_surcharge(17168312);
ShipmentChargesController::fuel_surcharge(17182172);
ShipmentChargesController::fuel_surcharge(17182182);
ShipmentChargesController::fuel_surcharge(17175910);
ShipmentChargesController::fuel_surcharge(17182170);
ShipmentChargesController::fuel_surcharge(17167751);
ShipmentChargesController::fuel_surcharge(17189315);
ShipmentChargesController::fuel_surcharge(17167770);
ShipmentChargesController::fuel_surcharge(17181179);
ShipmentChargesController::fuel_surcharge(17184289);
ShipmentChargesController::fuel_surcharge(17167768);
ShipmentChargesController::fuel_surcharge(17178852);
ShipmentChargesController::fuel_surcharge(17184274);
ShipmentChargesController::fuel_surcharge(17180659);
ShipmentChargesController::fuel_surcharge(17180834);
ShipmentChargesController::fuel_surcharge(17168282);
ShipmentChargesController::fuel_surcharge(17180988);
ShipmentChargesController::fuel_surcharge(17182181);
ShipmentChargesController::fuel_surcharge(17194170);
ShipmentChargesController::fuel_surcharge(17184304);
ShipmentChargesController::fuel_surcharge(17180496);
ShipmentChargesController::fuel_surcharge(17172641);
ShipmentChargesController::fuel_surcharge(17180899);
ShipmentChargesController::fuel_surcharge(17174440);
ShipmentChargesController::fuel_surcharge(17190810);
ShipmentChargesController::fuel_surcharge(17180288);
ShipmentChargesController::fuel_surcharge(17177876);
ShipmentChargesController::fuel_surcharge(17177878);
ShipmentChargesController::fuel_surcharge(17160234);
ShipmentChargesController::fuel_surcharge(17160748);
ShipmentChargesController::fuel_surcharge(17173953);
ShipmentChargesController::fuel_surcharge(17148632);
ShipmentChargesController::fuel_surcharge(17175847);
ShipmentChargesController::fuel_surcharge(17172492);
ShipmentChargesController::fuel_surcharge(17194163);
ShipmentChargesController::fuel_surcharge(17126066);
ShipmentChargesController::fuel_surcharge(17172694);
ShipmentChargesController::fuel_surcharge(17185276);
ShipmentChargesController::fuel_surcharge(17178399);
ShipmentChargesController::fuel_surcharge(17148660);
ShipmentChargesController::fuel_surcharge(17178378);
ShipmentChargesController::fuel_surcharge(17187213);
ShipmentChargesController::fuel_surcharge(17178766);
ShipmentChargesController::fuel_surcharge(17182623);
ShipmentChargesController::fuel_surcharge(17178398);
ShipmentChargesController::fuel_surcharge(17182638);
ShipmentChargesController::fuel_surcharge(17182605);
ShipmentChargesController::fuel_surcharge(17182626);
ShipmentChargesController::fuel_surcharge(17187143);
ShipmentChargesController::fuel_surcharge(17185204);
ShipmentChargesController::fuel_surcharge(17190045);
ShipmentChargesController::fuel_surcharge(17178186);
ShipmentChargesController::fuel_surcharge(17192655);
ShipmentChargesController::fuel_surcharge(17179029);
ShipmentChargesController::fuel_surcharge(17192634);
ShipmentChargesController::fuel_surcharge(17192643);
ShipmentChargesController::fuel_surcharge(17170658);
ShipmentChargesController::fuel_surcharge(17192669);
ShipmentChargesController::fuel_surcharge(17148737);
ShipmentChargesController::fuel_surcharge(17185558);
ShipmentChargesController::fuel_surcharge(17148344);
ShipmentChargesController::fuel_surcharge(17178131);
ShipmentChargesController::fuel_surcharge(17193330);
ShipmentChargesController::fuel_surcharge(17180370);
ShipmentChargesController::fuel_surcharge(17177430);
ShipmentChargesController::fuel_surcharge(17193328);
ShipmentChargesController::fuel_surcharge(17181993);
ShipmentChargesController::fuel_surcharge(17189267);
ShipmentChargesController::fuel_surcharge(17175216);
ShipmentChargesController::fuel_surcharge(17191502);
ShipmentChargesController::fuel_surcharge(17178278);
ShipmentChargesController::fuel_surcharge(17148349);
ShipmentChargesController::fuel_surcharge(17148382);
ShipmentChargesController::fuel_surcharge(17192704);
ShipmentChargesController::fuel_surcharge(17167161);
ShipmentChargesController::fuel_surcharge(17189143);
ShipmentChargesController::fuel_surcharge(17183522);
ShipmentChargesController::fuel_surcharge(17184291);
ShipmentChargesController::fuel_surcharge(17180531);
ShipmentChargesController::fuel_surcharge(17183580);
ShipmentChargesController::fuel_surcharge(17176104);
ShipmentChargesController::fuel_surcharge(17176364);
ShipmentChargesController::fuel_surcharge(17168755);
ShipmentChargesController::fuel_surcharge(17184349);
ShipmentChargesController::fuel_surcharge(17184339);
ShipmentChargesController::fuel_surcharge(17189135);
ShipmentChargesController::fuel_surcharge(17178938);
ShipmentChargesController::fuel_surcharge(17182389);
ShipmentChargesController::fuel_surcharge(17166281);
ShipmentChargesController::fuel_surcharge(17182129);
ShipmentChargesController::fuel_surcharge(17180123);
ShipmentChargesController::fuel_surcharge(17171098);
ShipmentChargesController::fuel_surcharge(17177343);
ShipmentChargesController::fuel_surcharge(17186053);
ShipmentChargesController::fuel_surcharge(17183416);
ShipmentChargesController::fuel_surcharge(17175205);
ShipmentChargesController::fuel_surcharge(17178002);
ShipmentChargesController::fuel_surcharge(17178472);
ShipmentChargesController::fuel_surcharge(17189369);
ShipmentChargesController::fuel_surcharge(17182617);
ShipmentChargesController::fuel_surcharge(17166544);
ShipmentChargesController::fuel_surcharge(17178721);
ShipmentChargesController::fuel_surcharge(17185259);
ShipmentChargesController::fuel_surcharge(17178762);
ShipmentChargesController::fuel_surcharge(17192409);
ShipmentChargesController::fuel_surcharge(17192410);
ShipmentChargesController::fuel_surcharge(17192412);
ShipmentChargesController::fuel_surcharge(17177405);
ShipmentChargesController::fuel_surcharge(17192724);
ShipmentChargesController::fuel_surcharge(17192402);
ShipmentChargesController::fuel_surcharge(17171284);
ShipmentChargesController::fuel_surcharge(17191943);
ShipmentChargesController::fuel_surcharge(17179081);
ShipmentChargesController::fuel_surcharge(17190413);
ShipmentChargesController::fuel_surcharge(17182243);
ShipmentChargesController::fuel_surcharge(17171274);
ShipmentChargesController::fuel_surcharge(16911247);
ShipmentChargesController::fuel_surcharge(17183348);
ShipmentChargesController::fuel_surcharge(17183171);
ShipmentChargesController::fuel_surcharge(17178962);
ShipmentChargesController::fuel_surcharge(17174289);
ShipmentChargesController::fuel_surcharge(17178188);
ShipmentChargesController::fuel_surcharge(17177043);
ShipmentChargesController::fuel_surcharge(17179098);
ShipmentChargesController::fuel_surcharge(17191293);
ShipmentChargesController::fuel_surcharge(17177291);
ShipmentChargesController::fuel_surcharge(17176970);
ShipmentChargesController::fuel_surcharge(17181258);
ShipmentChargesController::fuel_surcharge(17176382);
ShipmentChargesController::fuel_surcharge(17180520);
ShipmentChargesController::fuel_surcharge(17021501);
ShipmentChargesController::fuel_surcharge(17180386);
ShipmentChargesController::fuel_surcharge(17131204);
ShipmentChargesController::fuel_surcharge(17193069);
ShipmentChargesController::fuel_surcharge(17189129);
ShipmentChargesController::fuel_surcharge(17180806);
ShipmentChargesController::fuel_surcharge(17161390);
ShipmentChargesController::fuel_surcharge(17151402);
ShipmentChargesController::fuel_surcharge(17151394);
ShipmentChargesController::fuel_surcharge(17151419);
ShipmentChargesController::fuel_surcharge(17180071);
ShipmentChargesController::fuel_surcharge(17176973);
ShipmentChargesController::fuel_surcharge(17177004);
ShipmentChargesController::fuel_surcharge(17177706);
ShipmentChargesController::fuel_surcharge(17176981);
ShipmentChargesController::fuel_surcharge(17193106);
ShipmentChargesController::fuel_surcharge(17177676);
ShipmentChargesController::fuel_surcharge(17157744);
ShipmentChargesController::fuel_surcharge(17177732);
ShipmentChargesController::fuel_surcharge(17159594);
ShipmentChargesController::fuel_surcharge(17184377);
ShipmentChargesController::fuel_surcharge(17183626);
ShipmentChargesController::fuel_surcharge(17182376);
ShipmentChargesController::fuel_surcharge(17193169);
ShipmentChargesController::fuel_surcharge(17176926);
ShipmentChargesController::fuel_surcharge(17193545);
ShipmentChargesController::fuel_surcharge(17183622);
ShipmentChargesController::fuel_surcharge(17124040);
ShipmentChargesController::fuel_surcharge(17151553);
ShipmentChargesController::fuel_surcharge(17193306);
ShipmentChargesController::fuel_surcharge(17180086);
ShipmentChargesController::fuel_surcharge(17183616);
ShipmentChargesController::fuel_surcharge(17192987);
ShipmentChargesController::fuel_surcharge(17183620);
ShipmentChargesController::fuel_surcharge(17183610);
ShipmentChargesController::fuel_surcharge(17183631);
ShipmentChargesController::fuel_surcharge(17159173);
ShipmentChargesController::fuel_surcharge(17193843);
ShipmentChargesController::fuel_surcharge(17183619);
ShipmentChargesController::fuel_surcharge(17183611);
ShipmentChargesController::fuel_surcharge(17175841);
ShipmentChargesController::fuel_surcharge(17182315);
ShipmentChargesController::fuel_surcharge(17193419);
ShipmentChargesController::fuel_surcharge(17183624);
ShipmentChargesController::fuel_surcharge(17180383);
ShipmentChargesController::fuel_surcharge(17192577);
ShipmentChargesController::fuel_surcharge(17177881);
ShipmentChargesController::fuel_surcharge(17151552);
ShipmentChargesController::fuel_surcharge(17171983);
ShipmentChargesController::fuel_surcharge(17171966);
ShipmentChargesController::fuel_surcharge(17193848);
ShipmentChargesController::fuel_surcharge(17183630);
ShipmentChargesController::fuel_surcharge(17151545);
ShipmentChargesController::fuel_surcharge(17183617);
ShipmentChargesController::fuel_surcharge(17184360);
ShipmentChargesController::fuel_surcharge(17184359);
ShipmentChargesController::fuel_surcharge(17174936);
ShipmentChargesController::fuel_surcharge(17150052);
ShipmentChargesController::fuel_surcharge(17183632);
ShipmentChargesController::fuel_surcharge(17181592);
ShipmentChargesController::fuel_surcharge(17193730);
ShipmentChargesController::fuel_surcharge(17181537);
ShipmentChargesController::fuel_surcharge(17193776);
ShipmentChargesController::fuel_surcharge(17193019);
ShipmentChargesController::fuel_surcharge(17193027);
ShipmentChargesController::fuel_surcharge(17175840);
ShipmentChargesController::fuel_surcharge(17172718);
ShipmentChargesController::fuel_surcharge(17172505);
ShipmentChargesController::fuel_surcharge(17172690);
ShipmentChargesController::fuel_surcharge(17176416);
ShipmentChargesController::fuel_surcharge(17178244);
ShipmentChargesController::fuel_surcharge(17180126);
ShipmentChargesController::fuel_surcharge(17176985);
ShipmentChargesController::fuel_surcharge(17180158);
ShipmentChargesController::fuel_surcharge(17176903);
ShipmentChargesController::fuel_surcharge(17175104);
ShipmentChargesController::fuel_surcharge(17140591);
ShipmentChargesController::fuel_surcharge(17181784);
ShipmentChargesController::fuel_surcharge(17184505);
ShipmentChargesController::fuel_surcharge(17182391);
ShipmentChargesController::fuel_surcharge(17182887);
ShipmentChargesController::fuel_surcharge(17137571);
ShipmentChargesController::fuel_surcharge(17171124);
ShipmentChargesController::fuel_surcharge(17133158);
ShipmentChargesController::fuel_surcharge(17188920);
ShipmentChargesController::fuel_surcharge(17166360);
ShipmentChargesController::fuel_surcharge(17188118);
ShipmentChargesController::fuel_surcharge(17188107);
ShipmentChargesController::fuel_surcharge(17188105);
ShipmentChargesController::fuel_surcharge(17133180);
ShipmentChargesController::fuel_surcharge(17193071);
ShipmentChargesController::fuel_surcharge(17182826);
ShipmentChargesController::fuel_surcharge(17184778);
ShipmentChargesController::fuel_surcharge(17180601);
ShipmentChargesController::fuel_surcharge(17173766);
ShipmentChargesController::fuel_surcharge(17172318);
ShipmentChargesController::fuel_surcharge(17184663);
ShipmentChargesController::fuel_surcharge(17171112);
ShipmentChargesController::fuel_surcharge(17178987);
ShipmentChargesController::fuel_surcharge(17173760);
ShipmentChargesController::fuel_surcharge(17188998);
ShipmentChargesController::fuel_surcharge(17179678);
ShipmentChargesController::fuel_surcharge(17151436);
ShipmentChargesController::fuel_surcharge(17151025);
ShipmentChargesController::fuel_surcharge(17179249);
ShipmentChargesController::fuel_surcharge(17187234);
ShipmentChargesController::fuel_surcharge(17176971);
ShipmentChargesController::fuel_surcharge(17178839);
ShipmentChargesController::fuel_surcharge(17176945);
ShipmentChargesController::fuel_surcharge(17173772);
ShipmentChargesController::fuel_surcharge(17163708);
ShipmentChargesController::fuel_surcharge(17185560);
ShipmentChargesController::fuel_surcharge(17179895);
ShipmentChargesController::fuel_surcharge(17190209);
ShipmentChargesController::fuel_surcharge(17184785);
ShipmentChargesController::fuel_surcharge(17174170);
ShipmentChargesController::fuel_surcharge(17190281);
ShipmentChargesController::fuel_surcharge(17180875);
ShipmentChargesController::fuel_surcharge(17174159);
ShipmentChargesController::fuel_surcharge(17184858);
ShipmentChargesController::fuel_surcharge(17194638);
ShipmentChargesController::fuel_surcharge(17192195);
ShipmentChargesController::fuel_surcharge(17153172);
ShipmentChargesController::fuel_surcharge(17193857);
ShipmentChargesController::fuel_surcharge(17148597);
ShipmentChargesController::fuel_surcharge(17183627);
ShipmentChargesController::fuel_surcharge(17172047);
ShipmentChargesController::fuel_surcharge(17184866);
ShipmentChargesController::fuel_surcharge(17184458);
ShipmentChargesController::fuel_surcharge(17193819);
ShipmentChargesController::fuel_surcharge(17151550);
ShipmentChargesController::fuel_surcharge(17185035);
ShipmentChargesController::fuel_surcharge(17174798);
ShipmentChargesController::fuel_surcharge(17179129);
ShipmentChargesController::fuel_surcharge(17184321);
ShipmentChargesController::fuel_surcharge(17178964);
ShipmentChargesController::fuel_surcharge(17174611);
ShipmentChargesController::fuel_surcharge(17180432);
ShipmentChargesController::fuel_surcharge(17180559);
ShipmentChargesController::fuel_surcharge(17180660);
ShipmentChargesController::fuel_surcharge(17180843);
ShipmentChargesController::fuel_surcharge(17175056);
ShipmentChargesController::fuel_surcharge(17193944);
ShipmentChargesController::fuel_surcharge(17176887);
ShipmentChargesController::fuel_surcharge(17181040);
ShipmentChargesController::fuel_surcharge(17174477);
ShipmentChargesController::fuel_surcharge(17175060);
ShipmentChargesController::fuel_surcharge(17184511);
ShipmentChargesController::fuel_surcharge(17177873);
ShipmentChargesController::fuel_surcharge(17174973);
ShipmentChargesController::fuel_surcharge(17155813);
ShipmentChargesController::fuel_surcharge(17153199);
ShipmentChargesController::fuel_surcharge(17184269);
ShipmentChargesController::fuel_surcharge(17184281);
ShipmentChargesController::fuel_surcharge(17177875);
ShipmentChargesController::fuel_surcharge(17184522);
ShipmentChargesController::fuel_surcharge(17175765);
ShipmentChargesController::fuel_surcharge(17184296);
ShipmentChargesController::fuel_surcharge(17184821);
ShipmentChargesController::fuel_surcharge(17192570);
ShipmentChargesController::fuel_surcharge(17176953);
ShipmentChargesController::fuel_surcharge(17176404);
ShipmentChargesController::fuel_surcharge(17173349);
ShipmentChargesController::fuel_surcharge(17173343);
ShipmentChargesController::fuel_surcharge(17176391);
ShipmentChargesController::fuel_surcharge(17164284);
ShipmentChargesController::fuel_surcharge(17185603);
ShipmentChargesController::fuel_surcharge(17184804);
ShipmentChargesController::fuel_surcharge(17185555);
ShipmentChargesController::fuel_surcharge(17177328);
ShipmentChargesController::fuel_surcharge(17186426);
ShipmentChargesController::fuel_surcharge(17174153);
ShipmentChargesController::fuel_surcharge(17184433);
ShipmentChargesController::fuel_surcharge(17178501);
ShipmentChargesController::fuel_surcharge(17185185);
ShipmentChargesController::fuel_surcharge(17190407);
ShipmentChargesController::fuel_surcharge(17172516);
ShipmentChargesController::fuel_surcharge(17119458);
ShipmentChargesController::fuel_surcharge(17172653);
ShipmentChargesController::fuel_surcharge(17175889);
ShipmentChargesController::fuel_surcharge(17175796);
ShipmentChargesController::fuel_surcharge(17175803);
ShipmentChargesController::fuel_surcharge(17184515);
ShipmentChargesController::fuel_surcharge(17191441);
ShipmentChargesController::fuel_surcharge(17193972);
ShipmentChargesController::fuel_surcharge(17192698);
ShipmentChargesController::fuel_surcharge(17106363);
ShipmentChargesController::fuel_surcharge(17189184);
ShipmentChargesController::fuel_surcharge(17151554);
ShipmentChargesController::fuel_surcharge(17192755);
ShipmentChargesController::fuel_surcharge(17188976);
ShipmentChargesController::fuel_surcharge(17190671);
ShipmentChargesController::fuel_surcharge(17190610);
ShipmentChargesController::fuel_surcharge(17188767);
ShipmentChargesController::fuel_surcharge(17190247);
ShipmentChargesController::fuel_surcharge(17114912);
ShipmentChargesController::fuel_surcharge(17115005);
ShipmentChargesController::fuel_surcharge(17178010);
ShipmentChargesController::fuel_surcharge(17184646);
ShipmentChargesController::fuel_surcharge(17175844);
ShipmentChargesController::fuel_surcharge(17175886);
ShipmentChargesController::fuel_surcharge(17176603);
ShipmentChargesController::fuel_surcharge(17186376);
ShipmentChargesController::fuel_surcharge(17188233);
ShipmentChargesController::fuel_surcharge(17175377);
ShipmentChargesController::fuel_surcharge(17191819);
ShipmentChargesController::fuel_surcharge(17179080);
ShipmentChargesController::fuel_surcharge(17183505);
ShipmentChargesController::fuel_surcharge(17183012);
ShipmentChargesController::fuel_surcharge(17181145);
ShipmentChargesController::fuel_surcharge(17182225);
ShipmentChargesController::fuel_surcharge(17180433);
ShipmentChargesController::fuel_surcharge(17181602);
ShipmentChargesController::fuel_surcharge(17181072);
ShipmentChargesController::fuel_surcharge(17192229);
ShipmentChargesController::fuel_surcharge(17176046);
ShipmentChargesController::fuel_surcharge(17186122);
ShipmentChargesController::fuel_surcharge(17172946);
ShipmentChargesController::fuel_surcharge(17178927);
ShipmentChargesController::fuel_surcharge(17191629);
ShipmentChargesController::fuel_surcharge(17182896);
ShipmentChargesController::fuel_surcharge(17192352);
ShipmentChargesController::fuel_surcharge(17170899);
ShipmentChargesController::fuel_surcharge(17185680);
ShipmentChargesController::fuel_surcharge(17172948);
ShipmentChargesController::fuel_surcharge(17184497);
ShipmentChargesController::fuel_surcharge(17191974);
ShipmentChargesController::fuel_surcharge(17192448);
ShipmentChargesController::fuel_surcharge(17184514);
ShipmentChargesController::fuel_surcharge(17191810);
ShipmentChargesController::fuel_surcharge(17192125);
ShipmentChargesController::fuel_surcharge(17172936);
ShipmentChargesController::fuel_surcharge(17192163);
ShipmentChargesController::fuel_surcharge(17172942);
ShipmentChargesController::fuel_surcharge(17172939);
ShipmentChargesController::fuel_surcharge(17170727);
ShipmentChargesController::fuel_surcharge(17193711);
ShipmentChargesController::fuel_surcharge(17170686);
ShipmentChargesController::fuel_surcharge(17193698);
ShipmentChargesController::fuel_surcharge(17193700);
ShipmentChargesController::fuel_surcharge(17193681);
ShipmentChargesController::fuel_surcharge(17170709);
ShipmentChargesController::fuel_surcharge(17193687);
ShipmentChargesController::fuel_surcharge(17170723);
ShipmentChargesController::fuel_surcharge(17193682);
ShipmentChargesController::fuel_surcharge(17170731);
ShipmentChargesController::fuel_surcharge(17193688);
ShipmentChargesController::fuel_surcharge(17193702);
ShipmentChargesController::fuel_surcharge(17193689);
ShipmentChargesController::fuel_surcharge(17191918);
ShipmentChargesController::fuel_surcharge(17178961);
ShipmentChargesController::fuel_surcharge(17193696);
ShipmentChargesController::fuel_surcharge(17184806);
ShipmentChargesController::fuel_surcharge(17180069);
ShipmentChargesController::fuel_surcharge(17180452);
ShipmentChargesController::fuel_surcharge(17182107);
ShipmentChargesController::fuel_surcharge(17086002);
ShipmentChargesController::fuel_surcharge(17186226);
ShipmentChargesController::fuel_surcharge(17115989);
ShipmentChargesController::fuel_surcharge(17025063);
ShipmentChargesController::fuel_surcharge(17186379);
ShipmentChargesController::fuel_surcharge(17189632);
ShipmentChargesController::fuel_surcharge(17181918);
ShipmentChargesController::fuel_surcharge(17191861);
ShipmentChargesController::fuel_surcharge(17040609);
ShipmentChargesController::fuel_surcharge(17183248);
ShipmentChargesController::fuel_surcharge(17187524);
ShipmentChargesController::fuel_surcharge(17188234);
ShipmentChargesController::fuel_surcharge(17180859);
ShipmentChargesController::fuel_surcharge(17193526);
ShipmentChargesController::fuel_surcharge(17085999);
ShipmentChargesController::fuel_surcharge(17192743);
ShipmentChargesController::fuel_surcharge(17180449);
ShipmentChargesController::fuel_surcharge(17178138);
ShipmentChargesController::fuel_surcharge(17177726);
ShipmentChargesController::fuel_surcharge(17190733);
ShipmentChargesController::fuel_surcharge(17178144);
ShipmentChargesController::fuel_surcharge(17178142);
ShipmentChargesController::fuel_surcharge(17177723);
ShipmentChargesController::fuel_surcharge(17143919);
ShipmentChargesController::fuel_surcharge(17178134);
ShipmentChargesController::fuel_surcharge(17179105);
ShipmentChargesController::fuel_surcharge(17188095);
ShipmentChargesController::fuel_surcharge(17183177);
ShipmentChargesController::fuel_surcharge(17181440);
ShipmentChargesController::fuel_surcharge(17181749);
ShipmentChargesController::fuel_surcharge(17183230);
ShipmentChargesController::fuel_surcharge(17181648);
ShipmentChargesController::fuel_surcharge(17176850);
ShipmentChargesController::fuel_surcharge(17137162);
ShipmentChargesController::fuel_surcharge(17178325);
ShipmentChargesController::fuel_surcharge(17176744);
ShipmentChargesController::fuel_surcharge(17175882);
ShipmentChargesController::fuel_surcharge(17176345);
ShipmentChargesController::fuel_surcharge(17181285);
ShipmentChargesController::fuel_surcharge(17182061);
ShipmentChargesController::fuel_surcharge(17183434);
ShipmentChargesController::fuel_surcharge(17095513);
ShipmentChargesController::fuel_surcharge(17177722);
ShipmentChargesController::fuel_surcharge(17180632);
ShipmentChargesController::fuel_surcharge(17151026);
ShipmentChargesController::fuel_surcharge(17182458);
ShipmentChargesController::fuel_surcharge(17182544);
ShipmentChargesController::fuel_surcharge(17151015);
ShipmentChargesController::fuel_surcharge(17184744);
ShipmentChargesController::fuel_surcharge(17186927);
ShipmentChargesController::fuel_surcharge(17156600);
ShipmentChargesController::fuel_surcharge(17154991);
ShipmentChargesController::fuel_surcharge(17185146);
ShipmentChargesController::fuel_surcharge(17184990);
ShipmentChargesController::fuel_surcharge(17192471);
ShipmentChargesController::fuel_surcharge(17156601);
ShipmentChargesController::fuel_surcharge(17185055);
ShipmentChargesController::fuel_surcharge(17191268);
ShipmentChargesController::fuel_surcharge(17191053);
ShipmentChargesController::fuel_surcharge(17191625);
ShipmentChargesController::fuel_surcharge(17191690);
ShipmentChargesController::fuel_surcharge(17179391);
ShipmentChargesController::fuel_surcharge(17186901);
ShipmentChargesController::fuel_surcharge(17179839);
ShipmentChargesController::fuel_surcharge(17187326);
ShipmentChargesController::fuel_surcharge(17188213);
ShipmentChargesController::fuel_surcharge(17182297);
ShipmentChargesController::fuel_surcharge(17183024);
ShipmentChargesController::fuel_surcharge(17192990);
ShipmentChargesController::fuel_surcharge(17157299);
ShipmentChargesController::fuel_surcharge(17193244);
ShipmentChargesController::fuel_surcharge(17193288);
ShipmentChargesController::fuel_surcharge(17191429);
ShipmentChargesController::fuel_surcharge(17149196);
ShipmentChargesController::fuel_surcharge(17149224);
ShipmentChargesController::fuel_surcharge(17149208);
ShipmentChargesController::fuel_surcharge(17149204);
ShipmentChargesController::fuel_surcharge(17149210);
ShipmentChargesController::fuel_surcharge(17149215);
ShipmentChargesController::fuel_surcharge(17149207);
ShipmentChargesController::fuel_surcharge(17180321);
ShipmentChargesController::fuel_surcharge(17192198);
ShipmentChargesController::fuel_surcharge(17183899);
ShipmentChargesController::fuel_surcharge(17186925);
ShipmentChargesController::fuel_surcharge(17173045);
ShipmentChargesController::fuel_surcharge(17183596);
ShipmentChargesController::fuel_surcharge(17185753);
ShipmentChargesController::fuel_surcharge(17184523);
ShipmentChargesController::fuel_surcharge(17185377);
ShipmentChargesController::fuel_surcharge(17152588);
ShipmentChargesController::fuel_surcharge(17178988);
ShipmentChargesController::fuel_surcharge(17185686);
ShipmentChargesController::fuel_surcharge(17185673);
ShipmentChargesController::fuel_surcharge(17178392);
ShipmentChargesController::fuel_surcharge(17178853);
ShipmentChargesController::fuel_surcharge(17172945);
ShipmentChargesController::fuel_surcharge(17179557);
ShipmentChargesController::fuel_surcharge(17184871);
ShipmentChargesController::fuel_surcharge(17112977);
ShipmentChargesController::fuel_surcharge(17172244);
ShipmentChargesController::fuel_surcharge(17183368);
ShipmentChargesController::fuel_surcharge(17181700);
ShipmentChargesController::fuel_surcharge(17185156);
ShipmentChargesController::fuel_surcharge(17165669);
ShipmentChargesController::fuel_surcharge(17178050);
ShipmentChargesController::fuel_surcharge(17176929);
ShipmentChargesController::fuel_surcharge(17179870);
ShipmentChargesController::fuel_surcharge(17177323);
ShipmentChargesController::fuel_surcharge(17184944);
ShipmentChargesController::fuel_surcharge(17185208);
ShipmentChargesController::fuel_surcharge(17185031);
ShipmentChargesController::fuel_surcharge(17183834);
ShipmentChargesController::fuel_surcharge(17178096);
ShipmentChargesController::fuel_surcharge(17177147);
ShipmentChargesController::fuel_surcharge(17185317);
ShipmentChargesController::fuel_surcharge(17185495);
ShipmentChargesController::fuel_surcharge(17182418);
ShipmentChargesController::fuel_surcharge(17185809);
ShipmentChargesController::fuel_surcharge(17185662);
ShipmentChargesController::fuel_surcharge(17178813);
ShipmentChargesController::fuel_surcharge(17185666);
ShipmentChargesController::fuel_surcharge(17185678);
ShipmentChargesController::fuel_surcharge(17185684);
ShipmentChargesController::fuel_surcharge(17185667);
ShipmentChargesController::fuel_surcharge(17192119);
ShipmentChargesController::fuel_surcharge(17185661);
ShipmentChargesController::fuel_surcharge(17184823);
ShipmentChargesController::fuel_surcharge(17181870);
ShipmentChargesController::fuel_surcharge(17187887);
ShipmentChargesController::fuel_surcharge(17187616);
ShipmentChargesController::fuel_surcharge(17193780);
ShipmentChargesController::fuel_surcharge(17187646);
ShipmentChargesController::fuel_surcharge(17187257);
ShipmentChargesController::fuel_surcharge(17187883);
ShipmentChargesController::fuel_surcharge(17187861);
ShipmentChargesController::fuel_surcharge(17193801);
ShipmentChargesController::fuel_surcharge(17187254);
ShipmentChargesController::fuel_surcharge(17193807);
ShipmentChargesController::fuel_surcharge(17193804);
ShipmentChargesController::fuel_surcharge(17193812);
ShipmentChargesController::fuel_surcharge(17187647);
ShipmentChargesController::fuel_surcharge(17193803);
ShipmentChargesController::fuel_surcharge(17193808);
ShipmentChargesController::fuel_surcharge(17187886);
ShipmentChargesController::fuel_surcharge(17187261);
ShipmentChargesController::fuel_surcharge(17187310);
ShipmentChargesController::fuel_surcharge(17193792);
ShipmentChargesController::fuel_surcharge(17187672);
ShipmentChargesController::fuel_surcharge(17187251);
ShipmentChargesController::fuel_surcharge(17193501);
ShipmentChargesController::fuel_surcharge(17193785);
ShipmentChargesController::fuel_surcharge(17187298);
ShipmentChargesController::fuel_surcharge(17187675);
ShipmentChargesController::fuel_surcharge(17187271);
ShipmentChargesController::fuel_surcharge(17187232);
ShipmentChargesController::fuel_surcharge(17187635);
ShipmentChargesController::fuel_surcharge(17193740);
ShipmentChargesController::fuel_surcharge(17193787);
ShipmentChargesController::fuel_surcharge(17187305);
ShipmentChargesController::fuel_surcharge(17187626);
ShipmentChargesController::fuel_surcharge(17187275);
ShipmentChargesController::fuel_surcharge(17194111);
ShipmentChargesController::fuel_surcharge(17193796);
ShipmentChargesController::fuel_surcharge(17194118);
ShipmentChargesController::fuel_surcharge(17187580);
ShipmentChargesController::fuel_surcharge(17193788);
ShipmentChargesController::fuel_surcharge(17194101);
ShipmentChargesController::fuel_surcharge(17193505);
ShipmentChargesController::fuel_surcharge(17194119);
ShipmentChargesController::fuel_surcharge(17194109);
ShipmentChargesController::fuel_surcharge(17187853);
ShipmentChargesController::fuel_surcharge(17187574);
ShipmentChargesController::fuel_surcharge(17194097);
ShipmentChargesController::fuel_surcharge(17187948);
ShipmentChargesController::fuel_surcharge(17194103);
ShipmentChargesController::fuel_surcharge(17187260);
ShipmentChargesController::fuel_surcharge(17194096);
ShipmentChargesController::fuel_surcharge(17187302);
ShipmentChargesController::fuel_surcharge(17187953);
ShipmentChargesController::fuel_surcharge(17187246);
ShipmentChargesController::fuel_surcharge(17193805);
ShipmentChargesController::fuel_surcharge(17187950);
ShipmentChargesController::fuel_surcharge(17194110);
ShipmentChargesController::fuel_surcharge(17194126);
ShipmentChargesController::fuel_surcharge(17193738);
ShipmentChargesController::fuel_surcharge(17193497);
ShipmentChargesController::fuel_surcharge(17194099);
ShipmentChargesController::fuel_surcharge(17193487);
ShipmentChargesController::fuel_surcharge(17193489);
ShipmentChargesController::fuel_surcharge(17187901);
ShipmentChargesController::fuel_surcharge(17185445);
ShipmentChargesController::fuel_surcharge(17184615);
ShipmentChargesController::fuel_surcharge(17173036);
ShipmentChargesController::fuel_surcharge(17172507);
ShipmentChargesController::fuel_surcharge(17173039);
ShipmentChargesController::fuel_surcharge(17149236);
ShipmentChargesController::fuel_surcharge(17149249);
ShipmentChargesController::fuel_surcharge(17173024);
ShipmentChargesController::fuel_surcharge(17173010);
ShipmentChargesController::fuel_surcharge(17175897);
ShipmentChargesController::fuel_surcharge(17079348);
ShipmentChargesController::fuel_surcharge(17173016);
ShipmentChargesController::fuel_surcharge(17171141);
ShipmentChargesController::fuel_surcharge(17185575);
ShipmentChargesController::fuel_surcharge(17184200);
ShipmentChargesController::fuel_surcharge(17185570);
ShipmentChargesController::fuel_surcharge(17185577);
ShipmentChargesController::fuel_surcharge(17191804);
ShipmentChargesController::fuel_surcharge(17184616);
ShipmentChargesController::fuel_surcharge(17185583);
ShipmentChargesController::fuel_surcharge(17185592);
ShipmentChargesController::fuel_surcharge(17185938);
ShipmentChargesController::fuel_surcharge(17160023);
ShipmentChargesController::fuel_surcharge(17184009);
ShipmentChargesController::fuel_surcharge(17185318);
ShipmentChargesController::fuel_surcharge(17193024);
ShipmentChargesController::fuel_surcharge(17185211);
ShipmentChargesController::fuel_surcharge(17184080);
ShipmentChargesController::fuel_surcharge(17162537);
ShipmentChargesController::fuel_surcharge(17180222);
ShipmentChargesController::fuel_surcharge(17191685);
ShipmentChargesController::fuel_surcharge(17149253);
ShipmentChargesController::fuel_surcharge(17185752);
ShipmentChargesController::fuel_surcharge(17172073);
ShipmentChargesController::fuel_surcharge(17171869);
ShipmentChargesController::fuel_surcharge(17185584);
ShipmentChargesController::fuel_surcharge(17193871);
ShipmentChargesController::fuel_surcharge(17193930);
ShipmentChargesController::fuel_surcharge(17193876);
ShipmentChargesController::fuel_surcharge(17193883);
ShipmentChargesController::fuel_surcharge(17193912);
ShipmentChargesController::fuel_surcharge(17193924);
ShipmentChargesController::fuel_surcharge(17193875);
ShipmentChargesController::fuel_surcharge(17193928);
ShipmentChargesController::fuel_surcharge(17193914);
ShipmentChargesController::fuel_surcharge(17193913);
ShipmentChargesController::fuel_surcharge(17193920);
ShipmentChargesController::fuel_surcharge(17193882);
ShipmentChargesController::fuel_surcharge(17193916);
ShipmentChargesController::fuel_surcharge(17193919);
ShipmentChargesController::fuel_surcharge(17193868);
ShipmentChargesController::fuel_surcharge(17193925);
ShipmentChargesController::fuel_surcharge(17193929);
ShipmentChargesController::fuel_surcharge(17193910);
ShipmentChargesController::fuel_surcharge(17193927);
ShipmentChargesController::fuel_surcharge(17193879);
ShipmentChargesController::fuel_surcharge(17193922);
ShipmentChargesController::fuel_surcharge(17193884);
ShipmentChargesController::fuel_surcharge(17193933);
ShipmentChargesController::fuel_surcharge(17173928);
ShipmentChargesController::fuel_surcharge(17170125);
ShipmentChargesController::fuel_surcharge(17159471);
ShipmentChargesController::fuel_surcharge(17182094);
ShipmentChargesController::fuel_surcharge(17191688);
ShipmentChargesController::fuel_surcharge(17175657);
ShipmentChargesController::fuel_surcharge(17177716);
ShipmentChargesController::fuel_surcharge(17177914);
ShipmentChargesController::fuel_surcharge(17183515);
ShipmentChargesController::fuel_surcharge(17184459);
ShipmentChargesController::fuel_surcharge(17185580);
ShipmentChargesController::fuel_surcharge(17185311);
ShipmentChargesController::fuel_surcharge(16635367);
ShipmentChargesController::fuel_surcharge(17169200);
ShipmentChargesController::fuel_surcharge(17070874);
ShipmentChargesController::fuel_surcharge(17170789);
ShipmentChargesController::fuel_surcharge(17177719);
ShipmentChargesController::fuel_surcharge(17171444);
ShipmentChargesController::fuel_surcharge(17159467);
ShipmentChargesController::fuel_surcharge(17126920);
ShipmentChargesController::fuel_surcharge(17148371);
ShipmentChargesController::fuel_surcharge(17070884);
ShipmentChargesController::fuel_surcharge(17174318);
ShipmentChargesController::fuel_surcharge(17186475);
ShipmentChargesController::fuel_surcharge(17185307);
ShipmentChargesController::fuel_surcharge(17187978);
ShipmentChargesController::fuel_surcharge(17180752);
ShipmentChargesController::fuel_surcharge(17182151);
ShipmentChargesController::fuel_surcharge(17178119);
ShipmentChargesController::fuel_surcharge(17191997);
ShipmentChargesController::fuel_surcharge(17191989);
ShipmentChargesController::fuel_surcharge(17164629);
ShipmentChargesController::fuel_surcharge(17173565);
ShipmentChargesController::fuel_surcharge(17191980);
ShipmentChargesController::fuel_surcharge(17173564);
ShipmentChargesController::fuel_surcharge(17191994);
ShipmentChargesController::fuel_surcharge(17191986);
ShipmentChargesController::fuel_surcharge(17191985);
ShipmentChargesController::fuel_surcharge(17191990);
ShipmentChargesController::fuel_surcharge(17192001);
ShipmentChargesController::fuel_surcharge(17181854);
ShipmentChargesController::fuel_surcharge(17190276);
ShipmentChargesController::fuel_surcharge(17191991);
ShipmentChargesController::fuel_surcharge(17191995);
ShipmentChargesController::fuel_surcharge(17184070);
ShipmentChargesController::fuel_surcharge(17188382);
ShipmentChargesController::fuel_surcharge(17173937);
ShipmentChargesController::fuel_surcharge(17177683);
ShipmentChargesController::fuel_surcharge(17177929);
ShipmentChargesController::fuel_surcharge(17187111);
ShipmentChargesController::fuel_surcharge(17180323);
ShipmentChargesController::fuel_surcharge(17156378);
ShipmentChargesController::fuel_surcharge(17192252);
ShipmentChargesController::fuel_surcharge(17192247);
ShipmentChargesController::fuel_surcharge(17184156);
ShipmentChargesController::fuel_surcharge(17184224);
ShipmentChargesController::fuel_surcharge(17180221);
ShipmentChargesController::fuel_surcharge(17159468);
ShipmentChargesController::fuel_surcharge(17179293);
ShipmentChargesController::fuel_surcharge(17179290);
ShipmentChargesController::fuel_surcharge(17182097);
ShipmentChargesController::fuel_surcharge(17192242);
ShipmentChargesController::fuel_surcharge(17180220);
ShipmentChargesController::fuel_surcharge(17161108);
ShipmentChargesController::fuel_surcharge(17178156);
ShipmentChargesController::fuel_surcharge(17181394);
ShipmentChargesController::fuel_surcharge(17179002);
ShipmentChargesController::fuel_surcharge(17185569);
ShipmentChargesController::fuel_surcharge(17191802);
ShipmentChargesController::fuel_surcharge(17177300);
ShipmentChargesController::fuel_surcharge(17188130);
ShipmentChargesController::fuel_surcharge(17187750);
ShipmentChargesController::fuel_surcharge(17182425);
ShipmentChargesController::fuel_surcharge(17165492);
ShipmentChargesController::fuel_surcharge(17180070);
ShipmentChargesController::fuel_surcharge(17160750);
ShipmentChargesController::fuel_surcharge(17183895);
ShipmentChargesController::fuel_surcharge(17181651);
ShipmentChargesController::fuel_surcharge(17176497);
ShipmentChargesController::fuel_surcharge(17178311);
ShipmentChargesController::fuel_surcharge(17188048);
ShipmentChargesController::fuel_surcharge(17190251);
ShipmentChargesController::fuel_surcharge(17191431);
ShipmentChargesController::fuel_surcharge(17179320);
ShipmentChargesController::fuel_surcharge(17180817);
ShipmentChargesController::fuel_surcharge(17190204);
ShipmentChargesController::fuel_surcharge(17183709);
ShipmentChargesController::fuel_surcharge(17177423);
ShipmentChargesController::fuel_surcharge(17165977);
ShipmentChargesController::fuel_surcharge(17159166);
ShipmentChargesController::fuel_surcharge(17176572);
ShipmentChargesController::fuel_surcharge(17179047);
ShipmentChargesController::fuel_surcharge(17169023);
ShipmentChargesController::fuel_surcharge(17177984);
ShipmentChargesController::fuel_surcharge(17182385);
ShipmentChargesController::fuel_surcharge(17107242);
ShipmentChargesController::fuel_surcharge(17163379);
ShipmentChargesController::fuel_surcharge(17193097);
ShipmentChargesController::fuel_surcharge(17182839);
ShipmentChargesController::fuel_surcharge(17180967);
ShipmentChargesController::fuel_surcharge(17195222);
ShipmentChargesController::fuel_surcharge(17173860);
ShipmentChargesController::fuel_surcharge(17192203);
ShipmentChargesController::fuel_surcharge(17190849);
ShipmentChargesController::fuel_surcharge(17195053);
ShipmentChargesController::fuel_surcharge(17191749);
ShipmentChargesController::fuel_surcharge(17195116);
ShipmentChargesController::fuel_surcharge(17195220);
ShipmentChargesController::fuel_surcharge(17194989);
ShipmentChargesController::fuel_surcharge(17195352);
ShipmentChargesController::fuel_surcharge(17136030);
ShipmentChargesController::fuel_surcharge(17153084);
ShipmentChargesController::fuel_surcharge(17141792);
ShipmentChargesController::fuel_surcharge(17190011);
ShipmentChargesController::fuel_surcharge(17184753);
ShipmentChargesController::fuel_surcharge(17169178);
ShipmentChargesController::fuel_surcharge(17192501);
ShipmentChargesController::fuel_surcharge(17164836);
ShipmentChargesController::fuel_surcharge(17192650);
ShipmentChargesController::fuel_surcharge(17192574);
ShipmentChargesController::fuel_surcharge(17195296);
ShipmentChargesController::fuel_surcharge(17192298);
ShipmentChargesController::fuel_surcharge(17192158);
ShipmentChargesController::fuel_surcharge(17191676);
ShipmentChargesController::fuel_surcharge(17193987);
ShipmentChargesController::fuel_surcharge(17177559);
ShipmentChargesController::fuel_surcharge(17193266);
ShipmentChargesController::fuel_surcharge(17192554);
ShipmentChargesController::fuel_surcharge(17181678);
ShipmentChargesController::fuel_surcharge(17181680);
ShipmentChargesController::fuel_surcharge(17181688);
ShipmentChargesController::fuel_surcharge(17184617);
ShipmentChargesController::fuel_surcharge(17128436);
ShipmentChargesController::fuel_surcharge(17182186);
ShipmentChargesController::fuel_surcharge(17182515);
ShipmentChargesController::fuel_surcharge(17178382);
ShipmentChargesController::fuel_surcharge(17190797);
ShipmentChargesController::fuel_surcharge(17193002);
ShipmentChargesController::fuel_surcharge(17190554);
ShipmentChargesController::fuel_surcharge(17113198);
ShipmentChargesController::fuel_surcharge(17181675);
ShipmentChargesController::fuel_surcharge(17132869);
ShipmentChargesController::fuel_surcharge(17192104);
ShipmentChargesController::fuel_surcharge(17122213);
ShipmentChargesController::fuel_surcharge(17188362);
ShipmentChargesController::fuel_surcharge(17178016);
ShipmentChargesController::fuel_surcharge(17190871);
ShipmentChargesController::fuel_surcharge(17171194);
ShipmentChargesController::fuel_surcharge(17194148);
ShipmentChargesController::fuel_surcharge(17188216);
ShipmentChargesController::fuel_surcharge(17125335);
ShipmentChargesController::fuel_surcharge(17173869);
ShipmentChargesController::fuel_surcharge(17192807);
ShipmentChargesController::fuel_surcharge(17065370);
ShipmentChargesController::fuel_surcharge(17178970);
ShipmentChargesController::fuel_surcharge(17187341);
ShipmentChargesController::fuel_surcharge(17178696);
ShipmentChargesController::fuel_surcharge(17181961);
ShipmentChargesController::fuel_surcharge(17191577);
ShipmentChargesController::fuel_surcharge(17182331);
ShipmentChargesController::fuel_surcharge(17190092);
ShipmentChargesController::fuel_surcharge(17184960);
ShipmentChargesController::fuel_surcharge(17154080);
ShipmentChargesController::fuel_surcharge(17193267);
ShipmentChargesController::fuel_surcharge(17193005);
ShipmentChargesController::fuel_surcharge(17190171);
ShipmentChargesController::fuel_surcharge(17181677);
ShipmentChargesController::fuel_surcharge(17181679);
ShipmentChargesController::fuel_surcharge(17193976);
ShipmentChargesController::fuel_surcharge(17193268);
ShipmentChargesController::fuel_surcharge(17177462);
ShipmentChargesController::fuel_surcharge(17193270);
ShipmentChargesController::fuel_surcharge(17178383);
ShipmentChargesController::fuel_surcharge(17188534);
ShipmentChargesController::fuel_surcharge(17178513);
ShipmentChargesController::fuel_surcharge(17176457);
ShipmentChargesController::fuel_surcharge(17188954);
ShipmentChargesController::fuel_surcharge(17152554);
ShipmentChargesController::fuel_surcharge(17175875);
ShipmentChargesController::fuel_surcharge(17173469);
ShipmentChargesController::fuel_surcharge(17143714);
ShipmentChargesController::fuel_surcharge(17149795);
ShipmentChargesController::fuel_surcharge(17149750);
ShipmentChargesController::fuel_surcharge(17149751);
ShipmentChargesController::fuel_surcharge(17152481);
ShipmentChargesController::fuel_surcharge(17152573);
ShipmentChargesController::fuel_surcharge(17149737);
ShipmentChargesController::fuel_surcharge(17170147);
ShipmentChargesController::fuel_surcharge(17150533);
ShipmentChargesController::fuel_surcharge(16994913);
ShipmentChargesController::fuel_surcharge(17150242);
ShipmentChargesController::fuel_surcharge(17169392);
ShipmentChargesController::fuel_surcharge(17150296);
ShipmentChargesController::fuel_surcharge(17175129);
ShipmentChargesController::fuel_surcharge(17169455);
ShipmentChargesController::fuel_surcharge(17169582);
ShipmentChargesController::fuel_surcharge(17149859);
ShipmentChargesController::fuel_surcharge(16636990);
ShipmentChargesController::fuel_surcharge(16958897);
ShipmentChargesController::fuel_surcharge(17149691);
ShipmentChargesController::fuel_surcharge(17149743);
ShipmentChargesController::fuel_surcharge(17149692);
ShipmentChargesController::fuel_surcharge(17149849);
ShipmentChargesController::fuel_surcharge(17164305);
ShipmentChargesController::fuel_surcharge(17161823);
ShipmentChargesController::fuel_surcharge(17152511);
ShipmentChargesController::fuel_surcharge(17166843);
ShipmentChargesController::fuel_surcharge(17152633);
ShipmentChargesController::fuel_surcharge(17152555);
ShipmentChargesController::fuel_surcharge(17152486);
ShipmentChargesController::fuel_surcharge(17149879);
ShipmentChargesController::fuel_surcharge(17169845);
ShipmentChargesController::fuel_surcharge(17065578);
ShipmentChargesController::fuel_surcharge(17171859);
ShipmentChargesController::fuel_surcharge(17169453);
ShipmentChargesController::fuel_surcharge(17149864);
ShipmentChargesController::fuel_surcharge(16637205);
ShipmentChargesController::fuel_surcharge(16637189);
ShipmentChargesController::fuel_surcharge(16988863);
ShipmentChargesController::fuel_surcharge(17174171);
ShipmentChargesController::fuel_surcharge(17130032);
ShipmentChargesController::fuel_surcharge(16637186);
ShipmentChargesController::fuel_surcharge(17170206);
ShipmentChargesController::fuel_surcharge(17149921);
ShipmentChargesController::fuel_surcharge(17149853);
ShipmentChargesController::fuel_surcharge(16958961);
ShipmentChargesController::fuel_surcharge(17175697);
ShipmentChargesController::fuel_surcharge(17117189);
ShipmentChargesController::fuel_surcharge(17152479);
ShipmentChargesController::fuel_surcharge(17193401);
ShipmentChargesController::fuel_surcharge(17194662);
ShipmentChargesController::fuel_surcharge(17195225);
ShipmentChargesController::fuel_surcharge(17195492);
ShipmentChargesController::fuel_surcharge(17194663);
ShipmentChargesController::fuel_surcharge(17193405);
ShipmentChargesController::fuel_surcharge(17179191);
ShipmentChargesController::fuel_surcharge(17195493);
ShipmentChargesController::fuel_surcharge(17195489);
ShipmentChargesController::fuel_surcharge(17192927);
ShipmentChargesController::fuel_surcharge(17179201);
ShipmentChargesController::fuel_surcharge(17193403);
ShipmentChargesController::fuel_surcharge(17195559);
ShipmentChargesController::fuel_surcharge(17192926);
ShipmentChargesController::fuel_surcharge(17193397);
ShipmentChargesController::fuel_surcharge(17192922);
ShipmentChargesController::fuel_surcharge(17192924);
ShipmentChargesController::fuel_surcharge(17194666);
ShipmentChargesController::fuel_surcharge(17195487);
ShipmentChargesController::fuel_surcharge(17195485);
ShipmentChargesController::fuel_surcharge(17192923);
ShipmentChargesController::fuel_surcharge(17179204);
ShipmentChargesController::fuel_surcharge(17195495);
ShipmentChargesController::fuel_surcharge(17179199);
ShipmentChargesController::fuel_surcharge(17194659);
ShipmentChargesController::fuel_surcharge(17194661);
ShipmentChargesController::fuel_surcharge(17195488);
ShipmentChargesController::fuel_surcharge(17195224);
ShipmentChargesController::fuel_surcharge(17195228);
ShipmentChargesController::fuel_surcharge(17195486);
ShipmentChargesController::fuel_surcharge(17152527);
ShipmentChargesController::fuel_surcharge(17175367);
ShipmentChargesController::fuel_surcharge(17188891);
ShipmentChargesController::fuel_surcharge(17175369);
ShipmentChargesController::fuel_surcharge(17170684);
ShipmentChargesController::fuel_surcharge(17184690);
ShipmentChargesController::fuel_surcharge(17116854);
ShipmentChargesController::fuel_surcharge(17192944);
ShipmentChargesController::fuel_surcharge(17184686);
ShipmentChargesController::fuel_surcharge(17178747);
ShipmentChargesController::fuel_surcharge(17192845);
ShipmentChargesController::fuel_surcharge(17192844);
ShipmentChargesController::fuel_surcharge(17152571);
ShipmentChargesController::fuel_surcharge(17152531);
ShipmentChargesController::fuel_surcharge(17152577);
ShipmentChargesController::fuel_surcharge(17180092);
ShipmentChargesController::fuel_surcharge(17176956);
ShipmentChargesController::fuel_surcharge(17159957);
ShipmentChargesController::fuel_surcharge(17153598);
ShipmentChargesController::fuel_surcharge(17173904);
ShipmentChargesController::fuel_surcharge(16637053);
ShipmentChargesController::fuel_surcharge(17175117);
ShipmentChargesController::fuel_surcharge(17143562);
ShipmentChargesController::fuel_surcharge(17171265);
ShipmentChargesController::fuel_surcharge(17075400);
ShipmentChargesController::fuel_surcharge(17160485);
ShipmentChargesController::fuel_surcharge(17133852);
ShipmentChargesController::fuel_surcharge(17194314);
ShipmentChargesController::fuel_surcharge(17126211);
ShipmentChargesController::fuel_surcharge(17137215);
ShipmentChargesController::fuel_surcharge(17141065);
ShipmentChargesController::fuel_surcharge(17136574);
ShipmentChargesController::fuel_surcharge(17140045);
ShipmentChargesController::fuel_surcharge(17182856);
ShipmentChargesController::fuel_surcharge(17193496);
ShipmentChargesController::fuel_surcharge(16987660);
ShipmentChargesController::fuel_surcharge(17119292);
ShipmentChargesController::fuel_surcharge(17119387);
ShipmentChargesController::fuel_surcharge(17148620);
ShipmentChargesController::fuel_surcharge(17182855);
ShipmentChargesController::fuel_surcharge(17171744);
ShipmentChargesController::fuel_surcharge(17164102);
ShipmentChargesController::fuel_surcharge(17148286);
ShipmentChargesController::fuel_surcharge(17149434);
ShipmentChargesController::fuel_surcharge(17182882);
ShipmentChargesController::fuel_surcharge(17119305);
ShipmentChargesController::fuel_surcharge(17119323);
ShipmentChargesController::fuel_surcharge(17119564);
ShipmentChargesController::fuel_surcharge(17148542);
ShipmentChargesController::fuel_surcharge(17119546);
ShipmentChargesController::fuel_surcharge(17119563);
ShipmentChargesController::fuel_surcharge(17106421);
ShipmentChargesController::fuel_surcharge(17119359);
ShipmentChargesController::fuel_surcharge(17119510);
ShipmentChargesController::fuel_surcharge(17119541);
ShipmentChargesController::fuel_surcharge(17119520);
ShipmentChargesController::fuel_surcharge(17119517);
ShipmentChargesController::fuel_surcharge(17119502);
ShipmentChargesController::fuel_surcharge(17106750);
ShipmentChargesController::fuel_surcharge(17106412);
ShipmentChargesController::fuel_surcharge(17119542);
ShipmentChargesController::fuel_surcharge(17106772);
ShipmentChargesController::fuel_surcharge(17119548);
ShipmentChargesController::fuel_surcharge(17119521);
ShipmentChargesController::fuel_surcharge(17119512);
ShipmentChargesController::fuel_surcharge(17119554);
ShipmentChargesController::fuel_surcharge(17119499);
ShipmentChargesController::fuel_surcharge(17119513);
ShipmentChargesController::fuel_surcharge(17106720);
ShipmentChargesController::fuel_surcharge(17106799);
ShipmentChargesController::fuel_surcharge(17119274);
ShipmentChargesController::fuel_surcharge(17119388);
ShipmentChargesController::fuel_surcharge(17148654);
ShipmentChargesController::fuel_surcharge(17119529);
ShipmentChargesController::fuel_surcharge(17172755);
ShipmentChargesController::fuel_surcharge(17119434);
ShipmentChargesController::fuel_surcharge(17119558);
ShipmentChargesController::fuel_surcharge(17148414);
ShipmentChargesController::fuel_surcharge(17106894);
ShipmentChargesController::fuel_surcharge(17119525);
ShipmentChargesController::fuel_surcharge(17119505);
ShipmentChargesController::fuel_surcharge(17119421);
ShipmentChargesController::fuel_surcharge(17119550);
ShipmentChargesController::fuel_surcharge(17106882);
ShipmentChargesController::fuel_surcharge(17172454);
ShipmentChargesController::fuel_surcharge(17119327);
ShipmentChargesController::fuel_surcharge(17119545);
ShipmentChargesController::fuel_surcharge(17172683);
ShipmentChargesController::fuel_surcharge(17119515);
ShipmentChargesController::fuel_surcharge(17148433);
ShipmentChargesController::fuel_surcharge(17119330);
ShipmentChargesController::fuel_surcharge(17126081);
ShipmentChargesController::fuel_surcharge(17119530);
ShipmentChargesController::fuel_surcharge(17119507);
ShipmentChargesController::fuel_surcharge(17119414);
ShipmentChargesController::fuel_surcharge(17172512);
ShipmentChargesController::fuel_surcharge(17194162);
ShipmentChargesController::fuel_surcharge(17119503);
ShipmentChargesController::fuel_surcharge(17119428);
ShipmentChargesController::fuel_surcharge(17148430);
ShipmentChargesController::fuel_surcharge(17172589);
ShipmentChargesController::fuel_surcharge(17172470);
ShipmentChargesController::fuel_surcharge(17119511);
ShipmentChargesController::fuel_surcharge(17106637);
ShipmentChargesController::fuel_surcharge(17119544);
ShipmentChargesController::fuel_surcharge(17106796);
ShipmentChargesController::fuel_surcharge(17119270);
ShipmentChargesController::fuel_surcharge(17061003);
ShipmentChargesController::fuel_surcharge(17148427);
ShipmentChargesController::fuel_surcharge(17126110);
ShipmentChargesController::fuel_surcharge(17119562);
ShipmentChargesController::fuel_surcharge(17106671);
ShipmentChargesController::fuel_surcharge(17148478);
ShipmentChargesController::fuel_surcharge(17172496);
ShipmentChargesController::fuel_surcharge(17106752);
ShipmentChargesController::fuel_surcharge(17126093);
ShipmentChargesController::fuel_surcharge(17119523);
ShipmentChargesController::fuel_surcharge(17148410);
ShipmentChargesController::fuel_surcharge(17119518);
ShipmentChargesController::fuel_surcharge(17061195);
ShipmentChargesController::fuel_surcharge(17119531);
ShipmentChargesController::fuel_surcharge(17119527);
ShipmentChargesController::fuel_surcharge(17148423);
ShipmentChargesController::fuel_surcharge(17119383);
ShipmentChargesController::fuel_surcharge(17119561);
ShipmentChargesController::fuel_surcharge(17119403);
ShipmentChargesController::fuel_surcharge(17119422);
ShipmentChargesController::fuel_surcharge(17148421);
ShipmentChargesController::fuel_surcharge(17119365);
ShipmentChargesController::fuel_surcharge(17119547);
ShipmentChargesController::fuel_surcharge(17169520);
ShipmentChargesController::fuel_surcharge(17189928);
ShipmentChargesController::fuel_surcharge(17176583);
ShipmentChargesController::fuel_surcharge(17183724);
ShipmentChargesController::fuel_surcharge(17193691);
ShipmentChargesController::fuel_surcharge(17156410);
ShipmentChargesController::fuel_surcharge(17176407);
ShipmentChargesController::fuel_surcharge(17198333);
ShipmentChargesController::fuel_surcharge(17198334);
ShipmentChargesController::fuel_surcharge(17198335);
ShipmentChargesController::fuel_surcharge(17198336);
ShipmentChargesController::fuel_surcharge(17198337);
ShipmentChargesController::fuel_surcharge(17198338);
ShipmentChargesController::fuel_surcharge(17198339);
ShipmentChargesController::fuel_surcharge(17198341);
ShipmentChargesController::fuel_surcharge(17198342);
ShipmentChargesController::fuel_surcharge(17198343);
ShipmentChargesController::fuel_surcharge(17198344);
ShipmentChargesController::fuel_surcharge(17198345);
ShipmentChargesController::fuel_surcharge(17198346);
ShipmentChargesController::fuel_surcharge(17198347);
ShipmentChargesController::fuel_surcharge(17198348);
ShipmentChargesController::fuel_surcharge(17198349);
ShipmentChargesController::fuel_surcharge(17198350);
ShipmentChargesController::fuel_surcharge(17198351);
ShipmentChargesController::fuel_surcharge(17198352);
ShipmentChargesController::fuel_surcharge(17198353);
ShipmentChargesController::fuel_surcharge(17198354);
ShipmentChargesController::fuel_surcharge(17198355);
ShipmentChargesController::fuel_surcharge(17198356);
ShipmentChargesController::fuel_surcharge(17198357);
ShipmentChargesController::fuel_surcharge(17198358);
ShipmentChargesController::fuel_surcharge(17198359);
ShipmentChargesController::fuel_surcharge(17198360);
ShipmentChargesController::fuel_surcharge(17198361);
ShipmentChargesController::fuel_surcharge(17198362);
ShipmentChargesController::fuel_surcharge(17198363);
ShipmentChargesController::fuel_surcharge(17198364);
ShipmentChargesController::fuel_surcharge(17198365);
ShipmentChargesController::fuel_surcharge(17198366);
ShipmentChargesController::fuel_surcharge(17198367);
ShipmentChargesController::fuel_surcharge(17198368);
ShipmentChargesController::fuel_surcharge(17198369);
ShipmentChargesController::fuel_surcharge(17148635);
ShipmentChargesController::fuel_surcharge(17186254);
ShipmentChargesController::fuel_surcharge(17119328);
ShipmentChargesController::fuel_surcharge(17170728);
ShipmentChargesController::fuel_surcharge(17173074);
ShipmentChargesController::fuel_surcharge(17200189);
ShipmentChargesController::fuel_surcharge(17200190);
ShipmentChargesController::fuel_surcharge(17200191);
ShipmentChargesController::fuel_surcharge(17200192);
ShipmentChargesController::fuel_surcharge(17200193);
ShipmentChargesController::fuel_surcharge(17200194);
ShipmentChargesController::fuel_surcharge(17200195);
ShipmentChargesController::fuel_surcharge(17200196);
ShipmentChargesController::fuel_surcharge(17200197);
ShipmentChargesController::fuel_surcharge(17200198);
ShipmentChargesController::fuel_surcharge(17200199);
ShipmentChargesController::fuel_surcharge(17200200);
ShipmentChargesController::fuel_surcharge(17200201);
ShipmentChargesController::fuel_surcharge(17200202);
ShipmentChargesController::fuel_surcharge(17200203);
ShipmentChargesController::fuel_surcharge(17200204);
ShipmentChargesController::fuel_surcharge(17200205);
ShipmentChargesController::fuel_surcharge(17200206);
ShipmentChargesController::fuel_surcharge(17200207);
ShipmentChargesController::fuel_surcharge(17200208);
ShipmentChargesController::fuel_surcharge(17200209);
ShipmentChargesController::fuel_surcharge(17200210);
ShipmentChargesController::fuel_surcharge(17200211);
ShipmentChargesController::fuel_surcharge(17200212);
ShipmentChargesController::fuel_surcharge(17200213);
ShipmentChargesController::fuel_surcharge(17200214);
ShipmentChargesController::fuel_surcharge(17200215);
ShipmentChargesController::fuel_surcharge(17200216);
ShipmentChargesController::fuel_surcharge(17200217);
ShipmentChargesController::fuel_surcharge(17200218);
ShipmentChargesController::fuel_surcharge(17200219);
ShipmentChargesController::fuel_surcharge(17200221);
ShipmentChargesController::fuel_surcharge(17200223);
ShipmentChargesController::fuel_surcharge(17200224);
ShipmentChargesController::fuel_surcharge(17200226);
ShipmentChargesController::fuel_surcharge(17200227);
ShipmentChargesController::fuel_surcharge(17200228);
ShipmentChargesController::fuel_surcharge(17200229);
ShipmentChargesController::fuel_surcharge(17200230);
ShipmentChargesController::fuel_surcharge(17200231);
ShipmentChargesController::fuel_surcharge(17200232);
ShipmentChargesController::fuel_surcharge(17200233);
ShipmentChargesController::fuel_surcharge(17200235);
ShipmentChargesController::fuel_surcharge(17200236);
ShipmentChargesController::fuel_surcharge(17200237);
ShipmentChargesController::fuel_surcharge(17200238);
ShipmentChargesController::fuel_surcharge(17200239);
ShipmentChargesController::fuel_surcharge(17200240);
ShipmentChargesController::fuel_surcharge(17200241);
ShipmentChargesController::fuel_surcharge(17200242);
ShipmentChargesController::fuel_surcharge(17200243);
ShipmentChargesController::fuel_surcharge(17200244);
ShipmentChargesController::fuel_surcharge(17200245);
ShipmentChargesController::fuel_surcharge(17200246);
ShipmentChargesController::fuel_surcharge(17200247);
ShipmentChargesController::fuel_surcharge(17200248);
ShipmentChargesController::fuel_surcharge(17200249);
ShipmentChargesController::fuel_surcharge(17200250);
ShipmentChargesController::fuel_surcharge(17144356);
ShipmentChargesController::fuel_surcharge(17173372);
ShipmentChargesController::fuel_surcharge(17176765);
ShipmentChargesController::fuel_surcharge(17180624);
ShipmentChargesController::fuel_surcharge(17174421);
ShipmentChargesController::fuel_surcharge(17161764);
ShipmentChargesController::fuel_surcharge(17205151);
ShipmentChargesController::fuel_surcharge(17205152);
ShipmentChargesController::fuel_surcharge(17205153);
ShipmentChargesController::fuel_surcharge(17205154);
ShipmentChargesController::fuel_surcharge(17205155);
ShipmentChargesController::fuel_surcharge(17205156);
ShipmentChargesController::fuel_surcharge(17205157);
ShipmentChargesController::fuel_surcharge(17205158);
ShipmentChargesController::fuel_surcharge(17205159);
ShipmentChargesController::fuel_surcharge(17205160);
ShipmentChargesController::fuel_surcharge(17205161);
ShipmentChargesController::fuel_surcharge(17205162);
ShipmentChargesController::fuel_surcharge(17205163);
ShipmentChargesController::fuel_surcharge(17205164);
ShipmentChargesController::fuel_surcharge(17205165);
ShipmentChargesController::fuel_surcharge(17205166);
ShipmentChargesController::fuel_surcharge(17210452);
ShipmentChargesController::fuel_surcharge(17210601);
}

        ActivityTrailController::createActivityTrailLog(Auth::id(), 6);

        $riders = Rider::where('status', 1)->select(['id', 'name','trax_id']);
        $pickup_statuses = V2PickupRequestStatus::all();
        $rider_statuses = V2PickupRequestRiderStatus::all();
        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
        $not_pick_reasons = V2PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $legends = V2PickupRequestLegend::all();
        $cut_off_time = '17:30:00';
        $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        if ($setting->exists()) {
            $setting = $setting->first();
            $cut_off_time = $setting->setting_value;
        }

        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if ($rider_settings->exists()) {
            $rider_settings = $rider_settings->first();
            $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        }

        return view('admin.v2_pickups.pending')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);
    }

    public function pending_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        }
        $today = Carbon::now()->startOfDay();
        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
            ->join('v2_pickup_request_rider_statuses as rs', 'rs.id', '=', 'v2_pickup_requests.rider_status')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v2_pickup_requests.current_rider_id')
            ->leftjoin('riders as lr', 'lr.id', '=', 'v2_pickup_requests.last_rider_id')
        //Assigned Date
            ->leftJoin('v2_pickup_request_attempts as vpa', function ($join) {
                $join->on('vpa.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('vpa.id', '=',
                        DB::raw('(select max(id) from v2_pickup_request_attempts where v2_pickup_request_attempts.pickup_request_id = v2_pickup_requests.id)'));
            })
        //End
            ->leftJoin('v2_pickup_note_requests as vpn', function ($join) {
                $join->on('vpn.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('vpn.id', '=',
                        DB::raw('(select max(id) from v2_pickup_note_requests where v2_pickup_note_requests.pickup_request_id = v2_pickup_requests.id)'));
            })
            ->leftJoin('v2_rider_pickups as vpr', function ($join) {
                $join->on('vpr.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('vpr.id', '=',
                        DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v2_pickup_requests.id)'));
            })
//            ->leftJoin('v2_rider_pickups as vpr', 'vpr.pickup_request_id', '=', 'v2_pickup_requests.id')

            ->select('v2_pickup_requests.id','v2_pickup_requests.reminder_status as reminder', 'v2_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link', 'v2_pickup_requests.received', 'v2_pickup_requests.received as received_link', 'usi.vendor as vendor_name', 'prs.name as pickup_status', 'rs.name as rider_status', 'v2_pickup_requests.attempts', 'cr.name as current_rider', 'lr.name as last_rider', 'v2_pickup_requests.try_and_buy', 'v2_pickup_requests.vendor', 'v2_pickup_requests.status_id', 'v2_pickup_requests.after_cut_off_time', 'vpn.pickup_note_id', 'vpn.pickup_note_id as pickup_note_no', 'vpr.shipments as shipments_rider_picked', 'vpa.created_at as assigned_date', 'v2_pickup_requests.reverse_pickup', 'vpr.rider_remarks as rider_remarks','usi.pickup_brand_name as brand_name', 'v2_pickup_requests.remarks as rev_remarks')
            ->whereNotIn('v2_pickup_requests.status_id', [2, 4]);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        $datatables = Datatables::of($pickup_requests)
            ->setRowAttr([
                'class' => function ($pickup_request) use ($today) {

                    if ($pickup_request->reverse_pickup == 1) {
                        return 'reverse_pickup_row';
                    }
                    if ($pickup_request->vendor != null) {
                        return 'vendor_row';
                    } else if ($pickup_request->try_and_buy == 1) {
                        return 'try_and_buy';
                    } else if (($pickup_request->status_id == 3) && ($pickup_request->attempts == 1)) {
                        return 'first_attempt';
                    } else if (($pickup_request->status_id == 3) && ($pickup_request->attempts == 2)) {
                        return 'second_attempt';
                    } else if (($pickup_request->status_id == 3) && ($pickup_request->attempts > 2)) {
                        return 'multiple_attempt';
                    } else if ($pickup_request->after_cut_off_time) {
                        return 'after_cut_off_time';
                    } else if (Carbon::parse($pickup_request->pickup_address_created_at)->startOfDay()->diffInDays($today) <= 6) {
                        return 'new_pickup';
                    }
                },
            ])
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                if($pickup_requests->reminder == 1)
                {
                    $test = str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
                    $test1 ='<td class="align-middle pickup_request_id sorting_1" ><b style="background-color: 	#00FF00; font-size: 17px;">'.$test.'</b></td>';
                    return $test1;

                }
                return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('bookings_link', function ($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('received_link', function ($pickup_request) {
                if ($pickup_request->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('trax_reason', function ($pickup_requests) {
                $reasons = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('reason_id');
                if ($attempts->exists()) {
                    $reason_ids = $attempts->pluck('reason_id')->toArray();
                    if (count($reason_ids) > 0) {
                        foreach ($reason_ids as $reason_id) {
                            $reasons .= V2PickupRequestNotPickReason::find($reason_id)->name . ',' . PHP_EOL;
                        }
                    }
                }
                return $reasons;
            })
            ->addColumn('trax_remarks', function ($pickup_requests) {
                $trax_remarks = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('trax_remarks');
                if ($attempts->exists()) {
                    $trax_remarks_rows = $attempts->pluck('trax_remarks')->toArray();
                    if (count($trax_remarks_rows) > 0) {
                        foreach ($trax_remarks_rows as $remark) {
                            $trax_remarks .= $remark . ',' . PHP_EOL;
                        }
                    }
                }
                return $trax_remarks;
            })
            ->addColumn('shipper_remarks', function ($pickup_requests) {
                $shipper_remarks = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('shipper_remarks');
                if ($attempts->exists()) {
                    $shipper_remarks_rows = $attempts->pluck('shipper_remarks')->toArray();
                    if (count($shipper_remarks_rows) > 0) {
                        foreach ($shipper_remarks_rows as $remark) {
                            $shipper_remarks .= $remark . ',' . PHP_EOL;
                        }
                    }
                }
                return $shipper_remarks;
            })
            ->addColumn('attempted_date', function ($pickup_requests) {
                $attempted_date = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id);
                if ($attempts->exists()) {
                    $attempted_date_rows = $attempts->pluck('attempt_date')->toArray();
                    if (count($attempted_date_rows) > 0) {
                        foreach ($attempted_date_rows as $index => $attempt_date) {
                            $attempted_date .= $attempt_date . ',' . PHP_EOL;
                        }
                    }
                }
                return $attempted_date;
            })
            ->editColumn('pickup_note_no', function ($pickup_requests) {
                if ($pickup_requests->pickup_note_id != null) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print" rel="' . $pickup_requests->pickup_note_id . '"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($pickup_requests->pickup_note_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }
                return '';
            })
            ->addColumn('action', function ($reminder_request) {
                $reminder_button = '<a href="javascript:void(0);" class="dropdown-item reminderMarkStatus" data-action="reminder"><i class="ft-plus-circle primary"></i> Reminder </a>';
                
                $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                    if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                        $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                        if ((session('role_id') == 1 || (in_array(583, session('permissions'))))) {
                            $dropdown .= $reminder_button;
                        }

                        if ($reminder_request->reverse_pickup == 1 && $reminder_request->rev_remarks == null) {

                            $dropdown .= $remarks_button;

                        }

                        $dropdown .= "
                            </div>
                        </div>
                    ";

                        return $dropdown;
                    } else {
                        return '';
                    }
            })
            ->addColumn('aging',function ($pickup_requests){
                $requested_date=$pickup_requests->requested_date;
                $settings = GlobalSettings::where('type', 'pickup_request_cut_off_time');
                if ($settings->exists()) {
                    $settings = $settings->first();
                    $days =Carbon::createFromTime($settings->setting_value, '0', '0', 'Asia/Karachi');
                   
                    $startTime = Carbon::parse($requested_date);
                    $endTime = Carbon::parse($days);

                    $totalDuration =  $startTime->diffInHours($endTime).' Hrs';
                   
                    //$difference =  $requested_date->diff($days)->format('%H:%I:%S')." Minutes";
                    //$difference=$requested_date-$days;
                    return $totalDuration;
                }
                //$days = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
            })
            ->editColumn('brand_name', function ($pickup_requests) {
                if($pickup_requests->brand_name==null){
                    $shipper = User::find($pickup_requests->user_id);
                    return $shipper->brand_name;
                }else{
                    return $pickup_requests->brand_name;
                }

            })
            ->addColumn('all_remarks',function ($pickup_requests){
                    return '<button class="btn btn-sm btn-outline-info align-middle all_remarks_btn" rel="' . $pickup_requests->id . '"><span class="align-middle">View Remarks</span></button>';
            });
            if($legend_filter = $request->get('legend_filter')){
                if($legend_filter==8){
                    $datatables->where('v2_pickup_requests.reverse_pickup',1);
                }
                elseif($legend_filter==2){
                    $datatables->where('v2_pickup_requests.vendor','<>',null);
                }
                elseif($legend_filter==3){
                    $datatables->where('v2_pickup_requests.try_and_buy',1)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==4){
                    $datatables->where('v2_pickup_requests.status_id',3)->where('v2_pickup_requests.attempts',1)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==5){
                    $datatables->where('v2_pickup_requests.status_id',3)->where('v2_pickup_requests.attempts',2)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==6){
                    $datatables->where('v2_pickup_requests.status_id',3)->where('v2_pickup_requests.attempts','>',2)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==7){
                    $datatables->where('v2_pickup_requests.after_cut_off_time','<>',null)
                    ->where('v2_pickup_requests.status_id','<>',3)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==1){
                    $datatables->where('v2_pickup_requests.created_at','<=',Carbon::now()->startOfDay()->addDays(6))
                        ->where('v2_pickup_requests.after_cut_off_time',null)
                        ->where('v2_pickup_requests.status_id','<>',3)
                        ->where('v2_pickup_requests.try_and_buy',null)
                        ->where('v2_pickup_requests.vendor',null)
                        ->where('v2_pickup_requests.reverse_pickup',null);
                }

            }
            
            if($legend_filter = $request->get('before_cut_off_time')){
                //to be made as before cut off time
                
                $cut_off_time = '17:30:00';
                $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
                if ($setting->exists()) {
                    $setting = $setting->first();
                    $cut_off_time = $setting->setting_value . ':00:00';
                    $cut_off_time = Carbon::parse($cut_off_time)->format('H:i:s');
                    $datatables->whereTime('v2_pickup_requests.created_at','<=',$cut_off_time);
                }

            }
//

        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $stop_date = date('Y-m-d H:i:s', strtotime($to . ' +1 day'));
            $datatables->whereBetween('v2_pickup_requests.created_at', [$from, $stop_date]);
        }

//
                    return $datatables->make(true);
    }

    public function pending_assign(Request $request)
    {
        $pickup_request_ids = $request->input('pickup_request_ids');
        $pickup_request_ids = explode(',', $pickup_request_ids);
        $rider_id = $request->input('rider');
        $rider_ids = $request->input('rider');
        $previous_rider_id = null;
        $riders = array();
        $riders['new'] = $rider_id;
        $riders['new_phone'] = $rider_ids;
        $notification_data = array();
        if (count($pickup_request_ids) == 0) {
            return redirect()->back()->with('error', 'No Pickups selected!');
        }

        array_unique($pickup_request_ids);
        $rider_cut_off_time = null;
        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if ($rider_settings->exists()) {
            $rider_settings = $rider_settings->first();
            if ($rider_settings->setting_value != 0 && $rider_settings->setting_value != null) {
                $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
            }
        }
        if ($rider_cut_off_time != null) {
            if (Carbon::now() > $rider_cut_off_time) {
                return redirect()->back()->with('error', 'Rider can not be assigned after cut off time!');
            }
        }

        if (empty($pickup_request_ids)) {
            return redirect()->back()->with('error', 'No Pickup Request Selected!');
        }

        if (empty($rider_id)) {
            return redirect()->back()->with('error', 'No Rider Selected!');
        }

//        foreach ($pickup_request_ids as $pickup_request_id) {
        //            $pickup_request = V2PickupRequest::find($pickup_request_id);
        //
        //            if ($pickup_request->status_id != 1) {
        //                return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        //            }
        //        }

        $pickups = 0;

        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
        $arrival_cut_off_time = '8';
        if ($settings->exists()) {
            $settings = $settings->first();
            $arrival_cut_off_time = $settings->setting_value;
        }

        $start_date = Carbon::now()->startOfDay();
        $end_date = Carbon::now()->endOfDay();
        $today = Carbon::today();
        $today->hour($arrival_cut_off_time)->minute(0)->second(0);

        $allowed_pickup_requests = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
//            $existing_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('rider_id', $rider_id)->whereBetween('attempt_date', [$start_date, $end_date]);
            $existing_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('attempt_date', '>', $today);

            if (!$existing_pickup_request_attempt->exists()) {
                $pickup_request = V2PickupRequest::find($pickup_request_id);

                $previous_rider_id = $pickup_request->current_rider_id;

                $pickup_request->rider_status = 2;
                $pickup_request->attempts = $pickup_request->attempts + 1;
                $pickup_request->current_rider_id = $rider_id;
                $pickup_request->last_updated_by = Auth::id();
                $pickup_request->save();

                $pickup_request_attempt = new V2PickupRequestAttempt();
                $pickup_request_attempt->pickup_request_id = $pickup_request_id;
                $pickup_request_attempt->rider_id = $rider_id;
                $pickup_request_attempt->attempt_date = Carbon::now();
                $pickup_request_attempt->assigned_by = Auth::id();
                $pickup_request_attempt->save();

                if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                    $allowed_pickup_requests[] = $pickup_request_id;
                }

                $pickups++;
                self::retail_pickup_assign($pickup_request_id, $rider_id);
            }
            else {
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if ($pickup_request->current_rider_id == $rider_id) {

                    if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                        $allowed_pickup_requests[] = $pickup_request_id;
                    }

                } else {
                    $previous_rider_id = $pickup_request->current_rider_id;
                    $riders['old_rider_id'] = $pickup_request->current_rider_id;
                    $riders['new_rider_id'] = $rider_id;

                    $pickup_request->rider_status = 2;
                    $pickup_request->current_rider_id = $rider_id;
                    $pickup_request->last_updated_by = Auth::id();
                    $pickup_request->save();
                    $existing_pickup_request_attempt = $existing_pickup_request_attempt->latest('id')->first();

                    $existing_pickup_rider = $existing_pickup_request_attempt->rider_id;

                    $existing_pickup_request_attempt->rider_id = $rider_id;
                    $existing_pickup_request_attempt->assigned_by = Auth::id();
                    $existing_pickup_request_attempt->save();

                    $pickup_note_request = $pickup_request->pickup_note_request;
                    if ($pickup_note_request) {
                        $pickup_note = $pickup_note_request->pickup_note;
                        $pickup_note_rider = $pickup_note->rider_id;
                        if ($existing_pickup_rider == $pickup_note_rider) {
                            $pickup_request->pickup_note_request->delete();
                            $pickup_note->pickups = $pickup_note->pickups - 1;
                            $pickup_note->save();
                        }
                    }
                    $pickups++;
                    if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                        $allowed_pickup_requests[] = $pickup_request_id;
                    }
                    if ($riders['old_rider_id'] != null && $riders['new_rider_id'] != null) {
                        NotificationsController::send(106, $riders, $pickup_request_id);
                        NotificationsController::send(107, $riders, $pickup_request_id);
                    }
                    self::retail_pickup_assign($pickup_request_id, $rider_id);
                }
            }
            $notification_data[] = ["rider_id" => $rider_id, "previous_rider" => $previous_rider_id, "pickup_request" => $pickup_request->id];
        }
        if (count($allowed_pickup_requests) > 0) {
            $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->first();
                if (!V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->whereIn('pickup_request_id', $allowed_pickup_requests)->exists()) {
                    $pickup_note->pickups += $pickups;

                    $pickup_note->save();

                }
                $pickup_note_id = $pickup_note->id;
            } else {
                $pickup_note = new V2PickupNote();

                $pickup_note->rider_id = $rider_id;
                $pickup_note->pickups = $pickups;
                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            }

            foreach ($allowed_pickup_requests as $pickup_request_id) {
                if (!V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->exists()) {
                    V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->where('status', 0)->delete();
                    $pickup_note_request = new V2PickupNoteRequest();

                    $pickup_note_request->pickup_note_id = $pickup_note_id;
                    $pickup_note_request->pickup_request_id = $pickup_request_id;

                    $pickup_note_request->save();
                    $pickup_request = V2PickupRequest::find($pickup_request_id);
                    $assigned_shipments = $pickup_request->pickup_request_shipments;
                    NotificationsController::send(42, $rider_id, $pickup_request->shipper_id);
                    if ($pickup_request->vendor == 1) {
                        NotificationsController::send(43, $pickup_request->id, $pickup_request->pickup_address->id);
                    }

                    if ($assigned_shipments) {
                        foreach ($assigned_shipments as $assigned_shipment) {
                            $shipment = $assigned_shipment->shipment;
//                    if ($shipment->booking_type_id == 3) {
                            //                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                            //                        if($pickup_request->try_and_buy == NULL){
                            //                            $pickup_request->try_and_buy = 1;
                            //                            $pickup_request->save();
                            //                        }
                            //                    }
                            if ($shipment->booking_type_id == 5) {
                                NotificationsController::send(77, $rider_id, $shipment->id);
                            }

                        }

                    }

                }
            }

            EmployeeAttendanceController::riders_attendance_mark($rider_id);

            foreach ($notification_data as $notification_datum){
                $previous_rider_id = $notification_datum["previous_rider"];
                $rider_id = $notification_datum["rider_id"];
                $pickup_request_id = $notification_datum["pickup_request"];
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if($pickup_request){
                    if ($previous_rider_id != NULL) {
                        NotificationsController::app_notification(2, $previous_rider_id, 2, $pickup_request->current_rider_id, $pickup_request->shipper_id);
                    }
                    if ($rider_id != NULL && $previous_rider_id == NULL) {
                        NotificationsController::app_notification(3, $rider_id, 2, $pickup_request->shipper_id);
                    } elseif ($rider_id != NULL && $previous_rider_id != NULL) {
                        NotificationsController::app_notification(1, $rider_id, 2, $previous_rider_id, $pickup_request->shipper_id);
                    }
                }
            }

            return redirect()->back()->with('success', 'Pickup Request(s) has been Assigned to the Rider!');
        } else {
            foreach ($notification_data as $notification_datum){
                $previous_rider_id = $notification_datum["previous_rider"];
                $rider_id = $notification_datum["rider_id"];
                $pickup_request_id = $notification_datum["pickup_request"];
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if($pickup_request){
                    if ($previous_rider_id != NULL) {
                        NotificationsController::app_notification(2, $previous_rider_id, 2, $pickup_request->current_rider_id, $pickup_request->shipper_id);
                    }
                    if ($rider_id != NULL && $previous_rider_id == NULL) {
                        NotificationsController::app_notification(3, $rider_id, 2, $pickup_request->shipper_id);
                    } elseif ($rider_id != NULL && $previous_rider_id != NULL) {
                        NotificationsController::app_notification(1, $rider_id, 2, $previous_rider_id, $pickup_request->shipper_id);
                    }
                }
            }
            return redirect()->back()->with('success', 'Pickup Request(s) rider updated / assigned!');
        }
        return redirect()->back()->with('error', 'Pickup Request(s) already assigned!');

    }

    public function pending_update(Request $request)
    {
        $pickup_request_ids = $request->pickup_request_ids;
        $pickup_request_ids = explode(',', $pickup_request_ids);

        $reason_id = $request->reason;
        $trax_remarks = $request->trax_remarks;
        if (count($pickup_request_ids) > 0) {
            foreach ($pickup_request_ids as $pickup_request_id) {
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if ($pickup_request) {
                    $rider_id = $pickup_request->current_rider_id;
                    if ($rider_id == null) {
                        $this->generate_trax_pickup($pickup_request->id);
                    }
                    $pickup_request_attempts = $pickup_request->pickup_attempt_latest;
//                    $pickup_request->status_id = 3;
                    $pickup_request->last_updated_by = Auth::id();
                    $pickup_request->save();
                    if ($pickup_request_attempts) {
                        $pickup_request_attempts->reason_id = $reason_id;
                        $pickup_request_attempts->trax_remarks = $trax_remarks;
                        $pickup_request_attempts->save();
                    }
                    $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->orderBy('id', 'desc')->first();
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    $pickup_note_request->status = 1;
                    $pickup_note_request->save();
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                    if ($pickup_note_requests_count == 0) {
                        V2PickupNote::where('id', $pickup_note_id)->update(['status' => 1]);
                    }
                    NotificationsController::send(105, $pickup_request_id, $reason_id);
                }
            }
            return redirect()->back()->with('success', 'Pickup(s) updated successfully!');
        }
        return redirect()->back()->with('error', 'Pickup(s) not selected!');

    }
    public function pending_all_bookings(Request $request)
    {
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                if (in_array($shipment_details->shipper_status_id,[1, 53])) {
                    $bookings[] = $shipment_details->tracking_number;
                }
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];
        } else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => false];
        }
    }

    public function pending_received_bookings(Request $request)
    {
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_received_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $bookings[] = $shipment_details->tracking_number;

            }

            return ['status' => 0, 'success' => 'Pending Booked Shipments', 'booked' => $bookings];
        } else {
            return ['status' => 0, 'success' => 'No Pending Booked Shipments', 'booked' => false];
        }
    }
    public function pending_reminder(Request $request){
        $id = $request->shipment_id;
        $data = V2PickupRequest::find($id);
        $data->reminder_status = 1;
        $data->reminder_status = 1;
        $data->save();
        return ['status'=>1,'success'=>"Reminder successfully Set"];

    }

    public function arrival_bulk_index(Request $request)
    {
        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name', 'trax_id')->get();
        return view('admin.v2_pickups.arrival_single_weight')->with(['riders' => $riders, 'global_rider_id' => $global_rider_id]);
    }
    public function arrival_bulk_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $user = $shipment->user;
            if($user->sub_segment_id == 2){
                $settings = GlobalSettings::where('type', 'global_rider_id')->first();

                if ($settings) {
                    $global_rider_id = $settings->setting_value;
                } else {
                    $global_rider_id = 0;
                }

                $pickup_request_id = null;
                $rider = null;
                $rider_assigned_flag = false;
                $shipment_origin = $shipment->pickup_address->city->hub_id;
                if (session('role_id') != 1) {
                    if (!in_array($shipment_origin, session('hubs'))) {
                        return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                    }

                }

                if ($shipment->warehouse == 1) {
                    if ($shipment->warehouse_order_status != 5) {
                        return ['status' => 1, 'error' => 'Shipment is not dispatched yet!'];
                    }
                }

                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                    if ($shipment->booking_type_id == 3) {
                        $details = array();
                        $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                        $shipment_items_count = count($shipment_items);

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipment_items'] = $shipment_items;
                        $details['shipment_items_count'] = $shipment_items_count;

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                    } else if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                        $details = array();
                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces_count'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        if ($shipment->shipper_status_id == 17) {
                            AdminPickupsController::generate($shipment->id);
                        }
                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                            $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        } else {
                            AdminPickupsController::generate($shipment->id);

                            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                            if ($pickup_request_shipment->exists()) {
                                $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                                $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                                $pickup_request = V2PickupRequest::find($pickup_request_id);
                                if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                    //                                $rider = Rider::find($rider_id)->name;
                                    $rider = '';
                                    $rider_assigned_flag = true;

                                } else {
                                    $rider = $pickup_request->rider->name;
                                }
                            }
                        }
                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipper'] = $shipment->user->name;
                        $details['pickup_request_id'] = str_pad($pickup_request_id, 6, '0', STR_PAD_LEFT);
                        $details['rider'] = $rider;
                        $details['pickup_request_id_unpadded'] = $pickup_request_id;
                        $details['rider_assigned'] = $rider_assigned_flag;

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                    }

                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Bulk Arrival is only allowed for shipments of General Logistics - Express Shipper(s)'];
            }
        }
        $shipment_item = ShipmentItem::find($request->tracking_number);
        if ($shipment_item) {
            $shipment = Shipment::find($shipment_item->shipment_id);
            $user = $shipment->user;
            if($user->sub_segment_id == 2) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['scanned_shipment_item'] = $shipment_item->id;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            }
            else{
                return ['status' => 1, 'error' => 'Bulk Arrival is only allowed for shipments of General Logistics - Express Shipper(s)'];
            }

        }
        $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number);
        if ($shipment_pieces->exists()) {
            $shipment_pieces = $shipment_pieces->first();
            $shipment = Shipment::find($shipment_pieces->shipment_id);

            $user = $shipment->user;
            if($user->sub_segment_id == 2) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                    $details = array();
                    $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                    $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Bulk Arrival is only allowed for shipments of General Logistics - Express Shipper(s)'];
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function bulk_arrival_submit(Request $request)
    {

        $shipment_ids = explode(',', $request->shipment_ids);

        $pickup_request_ids = array();

        $print_shipment_ids = array();

        $unassigned_pickup_requests = array();

        $pickup_rider_id = $request->rider_id;
        if ($pickup_rider_id) {
            $unassigned_pickup_requests = explode(',', $request->pickup_request_ids);
        }

        $walkin_shipment_ids = array();
        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                $piece_request_remarks = null;
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0)->orderBy('id', 'DESC')->first();
                    //region Taha
                    $pickup_request=V2PickupRequest::where('id', $pickup_request_shipment->pickup_request_id)->first();
                    //endregion

                    if ($shipment->shipper_status_id == 62) {
                        $shipment_pieces_request = ShipmentPiecesRequest::where('shipment_id', $shipment->id)->where('status', 1);
                        if ($shipment_pieces_request->exists()) {
                            $shipment_pieces_request = $shipment_pieces_request->first();
                            $shipment_pieces_request->status = 2;
                            $shipment_pieces_request->request_status_id = 4;
                            $shipment_pieces_request->last_updated_by_admin = Auth::id();
                            $shipment_pieces_request->last_updated_at = Carbon::now();
                            $shipment_pieces_request->department_id = session('department_id');
                            $shipment_pieces_request->save();
                            $piece_request_remarks = 'Resolved through Arrival';
                        }
                    }

                    if ($pickup_request_shipment) {
                        $reference_1_id = $pickup_request_shipment->pickup_request_id;
                        $rider_id=$pickup_request->current_rider_id;

                        if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                            $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;
                        }
                    } else {
                        $reference_1_id = null;
                    }
                    if($pickup_request->current_rider_id==null)
                    { 
                        $rider_id=$pickup_rider_id;
                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $actual_weight = $shipment->estimated_weight;
                    } else {
                        if ($request->volumetric_weight == "on") {
                            $actual_weight = (($request->length * $request->breadth * $request->height) / 5000);
                            $shipment->length = $request->length;
                            $shipment->breadth = $request->breadth;
                            $shipment->height = $request->height;
                        } else {
                            $actual_weight = $request->weight;
                        }

                        $not_include_shippers = [6693, 12412];
                        if (!in_array($shipment->user_id, $not_include_shippers)) {
                            $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                            if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {
                                $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                if($shipment_estimated_weight->exists()){
                                    $shipment_estimated_weight = $shipment_estimated_weight->first();
                                }
                                else{
                                    $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                }
                                $shipment_estimated_weight->shipment_id = $shipment->id;
                                $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                $shipment_estimated_weight->actual_weight= $actual_weight;
                                if (empty($request->weight)) {
                                    $shipment_estimated_weight->length = $request->length;
                                    $shipment_estimated_weight->breadth = $request->breadth;
                                    $shipment_estimated_weight->height = $request->height;
                                }
                                else{
                                    $shipment_estimated_weight->length = null;
                                    $shipment_estimated_weight->breadth = null;
                                    $shipment_estimated_weight->height = null;
                                }
                                $shipment_estimated_weight->save();
                                $actual_weight = $shipment->estimated_weight;

                                $shipment->length = NULL;
                                $shipment->breadth = NULL;
                                $shipment->height = NULL;
                            }
                        }
                    }
                    if ($shipment->booking_type_id == 4) {
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment->id);
                        if ($international_shipment->exists()) {
                            $city = City::find($shipment->consignee_city_id);
                            $hub_id = $city->hub_id;
                            $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
                            $check = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);

                            if ($shipment->walk_in_delivery_type_id == 1) {
                                $check_actual_weight = $check->door_actual_weight;
                            } else {
                                $check_actual_weight = $check->hub_actual_weight;
                            }
                            if ($actual_weight < $check_actual_weight) {
                                $actual_weight = $check_actual_weight;
                            }
                        } else {
                            $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $shipment->shipping_mode_id, 'delivery_type_id' => $shipment->walk_in_delivery_type_id])->first();
                            if ($actual_weight < $check['actual_weight']) {
                                $actual_weight = $check['actual_weight'];
                            }
                        }
                    }

                    $shipment->actual_weight = $actual_weight;
                    

                    if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
                        $receiving_sheet_shipment->status = 1;
                        $receiving_sheet_shipment->save();

                        $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

                        $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

                        $receiving_sheet->received = $receiving_sheet->received + 1;

                        $receiving_sheet->save();

                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    } else {
                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    }

                    $shipment->shipper_status_id = 2;
                    $shipment->consignee_status_id = 2;

                    $shipment->save();
                    $reference_2_id = null;
                    
                    ShipmentsJourneyController::add($shipment_id, 2, 2, null, $piece_request_remarks, null, Auth::id(), $reference_1_id, $reference_2_id,1,null,$rider_id);

                    $self_collection_shipment = SelfCollectionShipment::where('shipment_id', $shipment_id);
                    if ($self_collection_shipment->exists()) {
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $shipment->shipper_status_id = 15;
                            $shipment->consignee_status_id = 15;

                            $shipment->save();
                            ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                            NotificationsController::send(126, $shipment_id);
                        }
                    }
                    $shipment->refresh();
                    if ($shipment->walk_in_delivery_type_id == 2 && $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                        $shipment->shipper_status_id = 15;
                        $shipment->consignee_status_id = 15;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                        NotificationsController::send(126, $shipment_id);
                    }
                    if ($shipment->booking_type_id == 4) {
                        $print_shipment_ids[] = $shipment_id;
                    }
                    //Consolidated Shipments
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id);
                    if ($consolidated_shipment->exists()) {
                        $consolidated_shipment = $consolidated_shipment->first();
//                $user_shipping_info = UserShippingInfo::find($shipment->pickup_address_id);
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $check_all_consolidation_shipments = true;

                            $shipment->shipper_status_id = 58;
                            $shipment->consignee_status_id = 58;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 58, 58, null, $piece_request_remarks, null, Auth::id());

                            $consolidation_id = $consolidated_shipment->consolidation_id;
                            $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                            foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment) {
                                $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                                if ($check_remaining_consolidated_shipment->shipper_status_id != 58) {
                                    $check_all_consolidation_shipments = false;
                                }
                            }

                            if ($check_all_consolidation_shipments == true) {
                                foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment) {
                                    $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                    $update_all_consolidated_shipment->shipper_status_id = 59;
                                    $update_all_consolidated_shipment->consignee_status_id = 59;

                                    $update_all_consolidated_shipment->save();

                                    ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, null, $piece_request_remarks, null, Auth::id());
                                }
                            }
                        }
                    }
                    //Consolidated Shipments

                    $booking_sms = BookingSmsForShippers::where('user_id', $shipment->user_id)->where('status', 1);
                    if ($booking_sms->exists()) {
                        NotificationsController::send(3, $shipment_id);
                    }
                    if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                        if ($shipment->booking_type_id == 4) {
                            ShipmentChargesController::walkin_weight($shipment_id);
                        } else {
                            ShipmentChargesController::weight($shipment_id);
                            if ($shipment->business_category_id == 1) {
                                ShipmentChargesController::cash_handling($shipment_id);
                                ShipmentChargesController::insurance($shipment_id);
                                ShipmentChargesController::fuel_surcharge($shipment_id);
                            } else {
                                ShipmentChargesController::international_fuel_surcharge($shipment_id);
                            }
                        }

                        if($shipment->walk_in_status == 0) {
                            InitialChargesWebhookController::webhook_subscription($shipment_id);
                        }
                    }

                    if ($shipment->shipment_type != 2 && $shipment->charges_mode_id == 2 && $shipment->booking_type_id != 4) {
                        $shipment = Shipment::find($shipment_id);

                        $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                        $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                        $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                        $shipment->amount = $shipment->amount + $charges + $gst;

                        $shipment->save();

                        $print_shipment_ids[] = $shipment_id;
                    }
                    if (($shipment->charges_mode_id == 2 || $shipment->charges_mode_id == 1) && $shipment->booking_type_id == 4) {
                        $walkin_shipment_ids[] = $shipment->id;
                    }

                }
            } else {
                unset($shipment_ids[$key]);
            }
        }

        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->whereIn('pickup_request_id', $pickup_request_ids);

            if ($pickup_request_shipment->exists()) {
                $pickup_note_id = NULL;
                $pickup_request_shipment = $pickup_request_shipment->first();
                $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                $pickup_request_shipment->status = 1;
                $pickup_request_shipment->save();
                $pickup_request_received_shipment = new V2PickupReceivedShipment();
                $pickup_request_received_shipment->pickup_request_id = $pickup_request_id;
                $pickup_request_received_shipment->shipment_id = $shipment->id;

                $pickup_request = V2PickupRequest::find($pickup_request_id);
                $current_rider_id = $pickup_request->current_rider_id;
                $pickup_note_request = $pickup_request->pickup_note_request;
                if ($pickup_note_request) {
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    if ($current_rider_id == null) {
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        $current_rider_id = $pickup_note->rider_id;
                    }
                }

                $pickup_request_received_shipment->pickup_note_id = $pickup_note_id;
                $pickup_request_received_shipment->rider_id = $current_rider_id;

                $pickup_request_received_shipment->save();
                $pickup_request = $pickup_request_shipment->pickup_request;
                ShipmentsPickupJourneyController::add($shipment_id, 2, Auth::id(), $pickup_request->id);

                $pickup_request->received = $pickup_request->received + 1;
                $pickup_request->status_id = 2;
                $pickup_request->save();
            }
        }
        $pickup_note_ids = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_request = V2PickupRequest::find($pickup_request_id);

            if ($pickup_request->received >= 1) {
                if ($pickup_rider_id && in_array($pickup_request_id, $unassigned_pickup_requests)) {
                    $pickup_note_id = $this->generate_assigned_pickup($pickup_request_id, $pickup_rider_id);

                    if (!in_array($pickup_note_id, $pickup_note_ids)) {
                        $pickup_note_ids[] = $pickup_note_id;
                    }

                } else {
                    $pickup_note_request = $pickup_request->pickup_note_request;
                    if ($pickup_note_request) {
                        $pickup_note_id = $pickup_note_request->pickup_note_id;
                        $pickup_note_request->status = 1;
                        $pickup_note_request->save();
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        if ($pickup_note) {
                            if ($pickup_note->status == 0) {
                                if (!in_array($pickup_note_id, $pickup_note_ids)) {
                                    $pickup_note_ids[] = $pickup_note_id;
                                }
                            }
                        }
                    }
                    $this->retail_pickup_arrival($pickup_request_id);
                }

            }
        }
        if (!empty($pickup_note_ids)) {
            foreach ($pickup_note_ids as $pickup_note_id) {
                $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                if ($pickup_note_requests_count == 0) {
                    V2PickupNote::where('id', $pickup_note_id)->update(['status' => 1]);
                }
            }
        }

        NotificationsController::send(4, $shipment_ids);
        if (count($walkin_shipment_ids) > 0) {
            NotificationsController::send(85, $walkin_shipment_ids, Auth::id());
        }
        if (empty($print_shipment_ids)) {
            return redirect()->back()->with(['success' => 'Arrival Done']);
        } else {
            return redirect()->back()->with(['success' => 'Arrival Done', 'print_shipment_ids' => $print_shipment_ids]);
        }
//        return redirect()->route('admin.v2_pickups.pending.index')->with('success','Shipments arrived Successfully!');

    }

    public function generate_trax_pickup($pickup_request_id)
    {
        $pickup_request = V2PickupRequest::find($pickup_request_id);
        $settings = GlobalSettings::where('type', 'global_rider_id');
        if ($settings->exists()) {
            $settings = $settings->first();
            $rider_id = $settings->setting_value;
            $pickup_request_attempt = new V2PickupRequestAttempt();
            $pickup_request_attempt->pickup_request_id = $pickup_request_id;
            $pickup_request_attempt->rider_id = $rider_id;
            $pickup_request_attempt->attempt_date = Carbon::now();
            $pickup_request_attempt->assigned_by = Auth::id();
            $pickup_request_attempt->save();

            $pickup_request->rider_status = 2;
            $pickup_request->attempts = $pickup_request->attempts + 1;
            $pickup_request->current_rider_id = $rider_id;
            $pickup_request->last_updated_by = Auth::id();
            $pickup_request->save();

            $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->first();

                $pickup_note->pickups += 1;

                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            } else {
                $pickup_note = new V2PickupNote();

                $pickup_note->rider_id = $rider_id;
                $pickup_note->pickups = 1;
                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            }

            $pickup_note_request = new V2PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;

            $pickup_note_request->save();

            return $rider_id;
        }
    }

    public function generate_assigned_pickup($pickup_request_id, $rider_id)
    {
        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_attempt = new V2PickupRequestAttempt();
        $pickup_request_attempt->pickup_request_id = $pickup_request_id;
        $pickup_request_attempt->rider_id = $rider_id;
        $pickup_request_attempt->attempt_date = Carbon::now();
        $pickup_request_attempt->assigned_by = Auth::id();
        $pickup_request_attempt->save();

        $pickup_request->rider_status = 2;
        $pickup_request->attempts = $pickup_request->attempts + 1;
        $pickup_request->current_rider_id = $rider_id;
        $pickup_request->last_updated_by = Auth::id();
        $pickup_request->save();

        $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();

            $pickup_note->pickups += 1;

            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;

            $pickup_note_requests = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id);
            if ($pickup_note_requests->exists()) {

                $pickup_note_request = $pickup_note_requests->first();

                $pickup_note_request->pickup_note_id = $pickup_note_id;
                $pickup_note_request->pickup_request_id = $pickup_request_id;
                $pickup_note_request->status = 1;

                $pickup_note_request->save();
            } else {
                $pickup_note_request = new V2PickupNoteRequest();

                $pickup_note_request->pickup_note_id = $pickup_note_id;
                $pickup_note_request->pickup_request_id = $pickup_request_id;
                $pickup_note_request->status = 1;

                $pickup_note_request->save();
            }
        } else {
            $pickup_note = new V2PickupNote();

            $pickup_note->rider_id = $rider_id;
            $pickup_note->pickups = 1;
            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;

            $pickup_note_request = new V2PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;
            $pickup_note_request->status = 1;

            $pickup_note_request->save();
        }
        EmployeeAttendanceController::riders_attendance_mark($rider_id);

        return $pickup_note_id;
    }

    public function arrival_individual_index(Request $request)
    {
        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name','trax_id')->get();
        return view('admin.v2_pickups.arrival_individual_weight')->with(['riders' => $riders, 'global_rider_id' => $global_rider_id]);
    }

    public function arrival_individual_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $pickup_request_id = null;
            $rider = null;
            $rider_assigned_flag = false;
            $shipment_origin = $shipment->pickup_address->city->hub_id;
            if (session('role_id') != 1) {
                if (!in_array($shipment_origin, session('hubs'))) {
                    return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                }
            }

            if ($shipment->warehouse == 1) {
                if ($shipment->warehouse_order_status != 5) {
                    return ['status' => 1, 'error' => 'Shipment is not dispatched yet!'];
                }
            }

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }

            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                if ($shipment->booking_type_id == 3) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                    $details = array();
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces_count'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                } else {
                    if ($shipment->shipper_status_id == 17) {
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                            //                            $rider = Rider::find($rider_id)->name;
                            $rider = '';
                            $rider_assigned_flag = true;
                        } else {
                            $rider = $pickup_request->rider->name;
                        }
                    } else {
                        AdminPickupsController::generate($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                                $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        }
                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $actual_weight = $shipment->estimated_weight;
                    } else {
                        if (empty($request->weight)) {
                            $actual_weight = (($request->length * $request->breadth * $request->height) / 5000);

                            if($actual_weight < 0.1){
                                return ['status' => 1, 'error' => 'Volumetric weight cannot be less than 0.1'];
                            }
                            $shipment->length = $request->length;
                            $shipment->breadth = $request->breadth;
                            $shipment->height = $request->height;
                        } else {
                            $actual_weight = $request->weight;
                        }

                        $not_include_shippers = [6693, 12412];
                        if (!in_array($shipment->user_id, $not_include_shippers)) {
                            $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                            if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {

                                $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                if($shipment_estimated_weight->exists()){
                                    $shipment_estimated_weight = $shipment_estimated_weight->first();
                                }
                                else{
                                    $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                }
                                $shipment_estimated_weight->shipment_id = $shipment->id;
                                $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                $shipment_estimated_weight->actual_weight= $actual_weight;
                                if (empty($request->weight)) {
                                    $shipment_estimated_weight->length = $request->length;
                                    $shipment_estimated_weight->breadth = $request->breadth;
                                    $shipment_estimated_weight->height = $request->height;
                                }
                                else{
                                    $shipment_estimated_weight->length = null;
                                    $shipment_estimated_weight->breadth = null;
                                    $shipment_estimated_weight->height = null;
                                }
                                $shipment_estimated_weight->save();

                                $actual_weight = $shipment->estimated_weight;

                                $shipment->length = NULL;
                                $shipment->breadth = NULL;
                                $shipment->height = NULL;
                            }
                        }
                    }
                    if ($shipment->booking_type_id == 4) {
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment->id);
                        if ($international_shipment->exists()) {
                            $city = City::find($shipment->consignee_city_id);
                            $hub_id = $city->hub_id;
                            $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
                            $check = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);

                            if ($shipment->walk_in_delivery_type_id == 1) {
                                $check_actual_weight = $check->door_actual_weight;
                            } else {
                                $check_actual_weight = $check->hub_actual_weight;
                            }
                            if ($actual_weight < $check_actual_weight) {
                                $actual_weight = $check_actual_weight;
                            }
                        } else {
                            $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $shipment->shipping_mode_id, 'delivery_type_id' => $shipment->walk_in_delivery_type_id])->first();
                            if ($actual_weight < $check['actual_weight']) {
                                $actual_weight = $check['actual_weight'];
                            }
                        }
                    }
                    //here update amount
                    // if($shipment->booking_type_id==6){

                        // $ftl_request = FtlRequest::where('shipment_id',$shipment->id)->first();
                        // $other_amount = FtlRequestAdditionalCost::where('ftl_request_id',$ftl_request->id)->sum('amount');
                        // //amount or received_amount need to confirm
                        // $shipment->amount= ((($ftl_request->freight_charges/$ftl_request->weight)*$actual_weight)-$other_amount);
                    // }
                    $shipment->actual_weight = $actual_weight;
                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['weight'] = floatval($shipment->actual_weight);
                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;
                    $id = Auth::user();

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];

                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            $shipment_item = ShipmentItem::find($request->tracking_number);
            if ($shipment_item) {
                $shipment = Shipment::find($shipment_item->shipment_id);
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['scanned_shipment_item'] = $shipment_item->id;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number);
                if ($shipment_pieces->exists()) {
                    $shipment_pieces = $shipment_pieces->first();
                    $shipment = Shipment::find($shipment_pieces->shipment_id);
                    if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                        $details = array();
                        $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                        $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                    }
                }
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function arrival_try_and_buy_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                $rider_assigned_flag = false;
                if ($shipment->booking_type_id == 3) {
                    if ($shipment->shipper_status_id == 17) {
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                            //                            $rider = Rider::find($rider_id)->name;
                            $rider = '';
                            $rider_assigned_flag = true;
                        } else {
                            $rider = $pickup_request->rider->name;
                        }
                    } else {
                        AdminPickupsController::generate($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                                $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        }
                    }
                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        if ($request->has('weight')) {
                            $shipment->actual_weight = $request->weight;
                        }
                    }
                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['weight'] = floatval($shipment->actual_weight);
                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function arrival_individual_shipment_remove(Request $request)
    {
        $shipment = Shipment::find($request->id);

        if ($shipment) {
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                $shipment->actual_weight = null;
                $shipment->length = null;
                $shipment->breadth = null;
                $shipment->height = null;

                $shipment->save();

                return ['status' => 0, 'success' => 'Shipment has been removed'];
            } else {
                return ['status' => 1, 'error' => 'Given Shipment ID has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given ID is present'];
        }
    }

    public function individual_arrival_submit(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);

        $pickup_request_ids = array();

        $print_shipment_ids = array();

        $unassigned_pickup_requests = array();

        $pickup_rider_id = $request->rider_id;
        if ($pickup_rider_id) {
            $unassigned_pickup_requests = explode(',', $request->pickup_request_ids);
        }

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                if ($shipment->actual_weight == null) {
                    unset($shipment_ids[$key]);
                    continue;
                }
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                    $piece_request_remarks = null;
                    if ($shipment->shipper_status_id == 62) {
                        $shipment_pieces_request = ShipmentPiecesRequest::where('shipment_id', $shipment->id)->where('status', 1);
                        if ($shipment_pieces_request->exists()) {
                            $shipment_pieces_request = $shipment_pieces_request->first();
                            $shipment_pieces_request->status = 2;
                            $shipment_pieces_request->request_status_id = 4;
                            $shipment_pieces_request->last_updated_by_admin = Auth::id();
                            $shipment_pieces_request->last_updated_at = Carbon::now();
                            $shipment_pieces_request->department_id = session('department_id');
                            $shipment_pieces_request->save();
                            $piece_request_remarks = 'Resolved through Arrival';
                        }
                    }

                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0)->orderBy('id', 'DESC')->first();
                     //region Taha
                     $pickup_request=V2PickupRequest::where('id', $pickup_request_shipment->pickup_request_id)->first();
                     //endregion
                     
                    if ($pickup_request_shipment) {
                        $reference_1_id = $pickup_request_shipment->pickup_request_id;
                        $rider_id=$pickup_request->current_rider_id;

                        if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                            $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;
                        }
                    } else {
                        $reference_1_id = null;
                    }
                    if($pickup_request->current_rider_id==null)
                    { 
                        $rider_id=$pickup_rider_id;
                    }
                    if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
                        $receiving_sheet_shipment->status = 1;
                        $receiving_sheet_shipment->save();

                        $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

                        $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

                        $receiving_sheet->received = $receiving_sheet->received + 1;

                        $receiving_sheet->save();

                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    } else {
                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    }

                    $shipment->shipper_status_id = 2;
                    $shipment->consignee_status_id = 2;

                    $shipment->save();
                    $reference_2_id = null;
                    ShipmentsJourneyController::add($shipment_id, 2, 2, null, $piece_request_remarks, null, Auth::id(), $reference_1_id, $reference_2_id,1,null,$rider_id);

                    $self_collection_shipment = SelfCollectionShipment::where('shipment_id', $shipment_id);
                    if ($self_collection_shipment->exists()) {
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $shipment->shipper_status_id = 15;
                            $shipment->consignee_status_id = 15;

                            $shipment->save();
                            ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                            NotificationsController::send(126, $shipment_id);
                        }
                    }
                    $shipment->refresh();
                    if ($shipment->walk_in_delivery_type_id == 2 && $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                        $shipment->shipper_status_id = 15;
                        $shipment->consignee_status_id = 15;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                        NotificationsController::send(126, $shipment_id);
                    }
                    if ($shipment->booking_type_id == 4) {
                        $print_shipment_ids[] = $shipment_id;
                    }
                    //Consolidated Shipments
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                    if ($consolidated_shipment) {
//                $user_shipping_info = UserShippingInfo::find($shipment->pickup_address_id);
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $check_all_consolidation_shipments = true;

                            $shipment->shipper_status_id = 58;
                            $shipment->consignee_status_id = 58;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 58, 58, null, $piece_request_remarks, null, Auth::id());

                            $consolidation_id = $consolidated_shipment->consolidation_id;
                            $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                            foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment) {
                                $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                                if ($check_remaining_consolidated_shipment->shipper_status_id != 58) {
                                    $check_all_consolidation_shipments = false;
                                }
                            }

                            if ($check_all_consolidation_shipments == true) {
                                foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment) {
                                    $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                    $update_all_consolidated_shipment->shipper_status_id = 59;
                                    $update_all_consolidated_shipment->consignee_status_id = 59;

                                    $update_all_consolidated_shipment->save();

                                    ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, null, $piece_request_remarks, null, Auth::id());
                                }
                            }
                        }
                    }
                    //Consolidated Shipments

                    $booking_sms = BookingSmsForShippers::where('user_id', $shipment->user_id)->where('status', 1);
                    if ($booking_sms->exists()) {
                        NotificationsController::send(3, $shipment_id);
                    }
                    if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                        if ($shipment->booking_type_id == 4) {
                            ShipmentChargesController::walkin_weight($shipment_id);
                        } else {
                            ShipmentChargesController::weight($shipment_id);
                            if ($shipment->business_category_id == 1) {
                                ShipmentChargesController::cash_handling($shipment_id);
                                ShipmentChargesController::insurance($shipment_id);
                                ShipmentChargesController::fuel_surcharge($shipment_id);
                            } else {
                                ShipmentChargesController::international_fuel_surcharge($shipment_id);
                            }
                        }

                        if($shipment->walk_in_status == 0) {
                            InitialChargesWebhookController::webhook_subscription($shipment_id);
                        }
                    }



                    if ($shipment->shipment_type != 2 && $shipment->charges_mode_id == 2 && $shipment->booking_type_id != 4) {
                        $shipment = Shipment::find($shipment_id);

                        $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                        $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                        $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                        $shipment->amount = $shipment->amount + $charges + $gst;

                        $shipment->save();

                        $print_shipment_ids[] = $shipment_id;
                    }
                    if (($shipment->charges_mode_id == 2 || $shipment->charges_mode_id == 1) && $shipment->booking_type_id == 4) {
                        $shipment_ids = array($shipment->id);
                        NotificationsController::send(85, $shipment_ids, Auth::id());
                    }
                }
            } else {
                unset($shipment_ids[$key]);
            }
        }

        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->whereIn('pickup_request_id', $pickup_request_ids);

            if ($pickup_request_shipment->exists()) {
                $pickup_request_shipment = $pickup_request_shipment->first();
                $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                $pickup_request_shipment->status = 1;
                $pickup_request_shipment->save();
                $pickup_request_received_shipment = new V2PickupReceivedShipment();
                $pickup_request_received_shipment->pickup_request_id = $pickup_request_id;
                $pickup_request_received_shipment->shipment_id = $shipment->id;
                $pickup_note_id = NULL;
                $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->latest()->first();
                if($pickup_note_request){
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                }
                $pickup_request_received_shipment->pickup_note_id = $pickup_note_id;
                $pickup_request_received_shipment->save();
                $pickup_request = $pickup_request_shipment->pickup_request;
                ShipmentsPickupJourneyController::add($shipment_id, 2, Auth::id(), $pickup_request->id);

                $pickup_request->received = $pickup_request->received + 1;
                $pickup_request->status_id = 2;
                $pickup_request->save();

            }
        }
        $pickup_note_ids = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_request = V2PickupRequest::find($pickup_request_id);
            if ($pickup_request->received >= 1) {
                if ($pickup_rider_id && in_array($pickup_request_id, $unassigned_pickup_requests)) {
                    $pickup_note_id = $this->generate_assigned_pickup($pickup_request_id, $pickup_rider_id);

                    if (!in_array($pickup_note_id, $pickup_note_ids)) {
                        $pickup_note_ids[] = $pickup_note_id;
                    }

                } else {
                    $pickup_note_request = $pickup_request->pickup_note_request;
                    if ($pickup_note_request) {
                        $pickup_note_id = $pickup_note_request->pickup_note_id;
                        $pickup_note_request->status = 1;
                        $pickup_note_request->save();
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        if ($pickup_note) {
                            if ($pickup_note->status == 0) {
                                if (!in_array($pickup_note_id, $pickup_note_ids)) {
                                    $pickup_note_ids[] = $pickup_note_id;
                                }
                            }
                        }
                    }
                    $this->retail_pickup_arrival($pickup_request_id);
                }

            }
        }
        if (!empty($pickup_note_ids)) {
            foreach ($pickup_note_ids as $pickup_note_id) {
                $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                if ($pickup_note_requests_count == 0) {
                    V2PickupNote::where('id', $pickup_note_id)->update(['status' => 1]);
                }
            }
        }
        NotificationsController::send(4, $shipment_ids);

        if (empty($print_shipment_ids)) {
            return redirect()->back()->with(['success' => 'Arrival Done']);
        } else {
            return redirect()->back()->with(['success' => 'Arrival Done', 'print_shipment_ids' => $print_shipment_ids]);
        }
//        return redirect()->route('admin.v2_pickups.pending.index')->with('success','Shipments arrived Successfully!');

    }

    public function assigned_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      .vendor_pickup_row{
                        background-color: var(--light);
                      }
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';

        foreach ($request->ids as $id) {
            $pickup_note = V2PickupNote::find($id);
            $rider = Rider::find($pickup_note->rider_id);
            $route = $rider->route;
            $route_name = '';
            if ($route) {
                $route_name = $route->code . ' (' . $route->start . ' to ' . $route->end . ')';
            }
            $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Trax ID</strong></td>
                            <td>' . $rider->trax_id . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                          <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td> ' . $pickup_note->rider->city->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

            $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Vendor</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Sales Person</strong></td>
                            <td class="color primary"><strong>Person of Contact</strong></td>
                            <td class="color primary"><strong>Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

            $serial_number = 1;

            $pickup_note_requests = $pickup_note->pickup_note_requests;
            $reverse_pickup_shipment_ids = array();
            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = $pickup_note_request->pickup_request;

                $shipper = $pickup_request->shipper;
                $pickup_address = $pickup_request->pickup_address;
                $poc = SalePersonTag::join('admins as ad', 'ad.id', '=', 'sale_person_tags.admin_id')
                    ->leftjoin('users as us', 'us.id', '=', 'sale_person_tags.user_id')
                    ->leftjoin('shipper_contacts as sc', 'sc.shipper_id', '=', 'us.id')
                    ->where('sale_person_tags.status', 0)->where('us.id', $shipper->id)
                    ->select('ad.name as admin_name', 'ad.phone_number as admin_phone_number', 'sc.phone_number as phone_number', 'sc.poc')->get()->toArray();
//dd($poc);
                $pocName = "";
                $phoneNo = "";
                $names = "";
                $i = 0;
                foreach ($poc as $data) {
                    if ($i == null) {
                        if ($i == 0) {
                            $pocName .= '' . $data['poc'];
                            $phoneNo .= ' ' . $data['admin_phone_number'] . ',';
                            $phoneNo .= '' . $data['phone_number'];
                            $names = $data['admin_name'];
                            $i++;
                        } else {
                            $pocName .= ',' . $data['poc'];
                            $phoneNo .= ',' . $data['phone_number'];
                            $phoneNo .= ',' . $data['admin_phone_number'];

                        }
                    }
                }
                $color = '';
                if ($pickup_address->vendor != null) {
                    $color = 'vendor_pickup_row';
                }

                $html .= '
                          <tr class="' . $color . '">
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['vendor'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $names . '</td>
                            <td>' . $pocName . '</td>
                            <td>' . $phoneNo . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['booked'] . '</td>
                            <td>' . Carbon::parse($pickup_request['pickup_date'])->format('Y-m-d') . '</td>
                          </tr>
          ';

                $serial_number++;

                $pickup_request_shipments = $pickup_request->pickup_request_shipments;
                if ($pickup_request_shipments) {
                    foreach ($pickup_request_shipments as $pickup_request_shipment) {
                        if (Shipment::where('id', $pickup_request_shipment->shipment_id)->where('booking_type_id', 5)->exists()) {
                            $reverse_pickup_shipment_ids[] = $pickup_request_shipment->shipment_id;
                        }
                    }
                }
            }

            $html .= '
                        </tbody>
                      </table>

                      <hr>
        ';
            if (count($reverse_pickup_shipment_ids) > 0) {
                $airway_bill_html = '';
                $airway_bill_html = $this->print_air_waybill($reverse_pickup_shipment_ids, $rider->name);
                $html .= $airway_bill_html;
//                return response()->json(['status' => 0, 'shipment_ids' => $reverse_pickup_shipment_ids, 'rider_name' => $rider->name]);
            }
        }

        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }

    public function v2_pickups_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 7);
        $pickup_types = [['id' => 0, 'text' => 'Not Pick'], ['id' => 1, 'text' => 'Pick']];
        $pickup_not_pick_reasons = V2PickupRequestNotPickReason::all();

        return view('admin.v2_pickups.rider_pickups')->with(['pickup_types' => $pickup_types, 'pickup_not_pick_reasons' => $pickup_not_pick_reasons]);
    }

    public function pickups_list_v2(Request $request)
    {

        $excel = false;
        if ($request->get('excel') && $request->get('excel') == true) {
            $excel = true;
            ActivityTrailController::createActivityTrailLog(Auth::id(), 67);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $pickup_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp1` where `rp1`.`pickup_type` = 1 and `rp1`.`created_at` Between "' . $from . '" AND "' . $to . '") AS `pickup_picked`');
            $pickup_not_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp` where `rp`.`pickup_type` = 0 and `rp`.`created_at` Between "' . $from . '" AND "' . $to . '") AS `pickup_not_picked`');
        } else {
            $pickup_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp1` where `rp1`.`pickup_type` = 1) AS `pickup_picked`');
            $pickup_not_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp` where `rp`.`pickup_type` = 0) AS `pickup_not_picked`');
        }

        $rider_pickups = V2RiderPickup::leftjoin('v2_pickup_request_not_pick_reasons as pnpr', 'v2_rider_pickups.pickup_not_pick_reason_id', 'pnpr.id')
            ->join('v2_pickup_notes as pn', 'v2_rider_pickups.pickup_note_id', 'pn.id')
            ->join('v2_pickup_requests as pr', 'v2_rider_pickups.pickup_request_id', 'pr.id')
            ->join('riders as r', 'pn.rider_id', 'r.id')
            ->join('users as u', 'pr.shipper_id', 'u.id')
            ->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
            ->join('cities as c', 'usi.city_id', 'c.id')
            ->select('v2_rider_pickups.id', 'v2_rider_pickups.added_at', 'r.name as rider', 'u.name as shipper', 'usi.pickup_address', 'c.name as city', 'v2_rider_pickups.pickup_type', 'v2_rider_pickups.created_at', 'v2_rider_pickups.start_location_latitude', 'v2_rider_pickups.start_location_longitude', 'v2_rider_pickups.actual_location_latitude', 'v2_rider_pickups.actual_location_longitude', 'v2_rider_pickups.distance_from_start_to_actual', 'v2_rider_pickups.current_location_latitude', 'v2_rider_pickups.current_location_longitude', 'v2_rider_pickups.distance_from_current_to_actual', 'v2_rider_pickups.shipments', 'pnpr.name as reason', 'v2_rider_pickups.picture_path', 'v2_rider_pickups.pickup_note_id', 'v2_rider_pickups.pickup_request_id', $pickup_not_picked, $pickup_picked, 'v2_rider_pickups.rider_remarks as rider_remarks', 'v2_rider_pickups.audio_path');
        if (session('role_id') != 1) {
            $rider_pickups = $rider_pickups->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($rider_pickups)
            ->editColumn('pickup_note_id', function ($rider_pickup) {
                return str_pad($rider_pickup->pickup_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_request_id', function ($rider_pickup) {
                return str_pad($rider_pickup->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_type', function ($rider_pickup) {
                if ($rider_pickup->pickup_type == 0) {
                    return 'Not Pick';
                } else {
                    return 'Pick';
                }
            })
            ->editColumn('distance_from_start_to_actual', function ($rider_pickup) use ($excel) {
                $distance_from_start_to_actual = $rider_pickup->distance_from_start_to_actual;

                if ($distance_from_start_to_actual == 0) {
                    $distance_from_start_to_actual = 0;
                }

                if ($excel) {
                    return $distance_from_start_to_actual;
                }
                return '<a class="btn btn-sm btn-outline-info align-middle" href="http://maps.google.com/maps?saddr=' . $rider_pickup->start_location_latitude . ',' . $rider_pickup->start_location_longitude . '&daddr=' . $rider_pickup->actual_location_latitude . ',' . $rider_pickup->actual_location_longitude . '" target="_blank">' . $distance_from_start_to_actual . '</a>';
            })
            ->editColumn('distance_from_current_to_actual', function ($rider_pickup) use ($excel) {
                $distance_from_current_to_actual = $rider_pickup->distance_from_current_to_actual;

                if ($distance_from_current_to_actual == 0) {
                    $distance_from_current_to_actual = 0;
                }

                if ($rider_pickup->current_location_latitude && $rider_pickup->current_location_longitude && !$excel) {
                    return '<a class="btn btn-sm btn-outline-info align-middle" href="http://maps.google.com/maps?saddr=' . $rider_pickup->current_location_latitude . ',' . $rider_pickup->current_location_longitude . '&daddr=' . $rider_pickup->actual_location_latitude . ',' . $rider_pickup->actual_location_longitude . '" target="_blank">' . $distance_from_current_to_actual . '</a>';
                } else {
                    return $distance_from_current_to_actual;
                }
            })
            ->editColumn('picture_path', function ($rider_pickup) use ($excel) {
                if ($rider_pickup->pickup_type == 0) {
                    $image = '';
                    if ($rider_pickup->picture_path != null) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($rider_pickup->picture_path)) . '"><i class="la la-image"></i> View</button></div>';

                        if ($excel) {
                            return asset(Storage::url($rider_pickup->picture_path));
                        }
                        return $image;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('signature_via_app', function ($rider_pickup) use ($excel) {
                if ($rider_pickup->pickup_type == 1) {
                    $image = '';
                    if ($rider_pickup->picture_path != null) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm signature" data-link="' . asset(Storage::url($rider_pickup->picture_path)) . '"><i class="la la-image"></i> View</button></div>';
                        if ($excel) {
                            return asset(Storage::url($rider_pickup->picture_path));
                        }
                        return $image;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('audio_path', function ($rider_pickup) use ($excel) {
                $audio = '';
                if ($rider_pickup->audio_path != null) {
                    $audio .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm audio" data-link="' . asset(Storage::url($rider_pickup->audio_path)) . '"><i class="la la-file-sound-o"></i> Listen</button></div>';
                    if ($excel) {
                        return asset(Storage::url($rider_pickup->audio_path));
                    }
                    return $audio;
                } else {
                    return '-';
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $rider_pickups->whereBetween('v2_rider_pickups.created_at', [$from, $to]);
        }
        return $datatables->make(true);
    }
    public function pickups_action_log_index_v2()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 8);
        $pickup_actions = PickupAction::all();
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $admins = DB::connection('reports')->table('admins')->get(['id', 'name']);
        $cities = DB::connection('reports')->table('cities')->get(['id', 'name']);
        return view('admin.v2_pickups.action_log.index')->with(['pickup_actions' => $pickup_actions, 'riders' => $riders, 'admins' => $admins, 'cities' => $cities]);
    }
    public function pickups_action_log_list_v2(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 68);
        }
        $rider_pickup_action_logs = V2RiderPickupActionLog::join('pickup_actions as pa', 'v2_rider_pickup_action_logs.type_id', 'pa.id')
            ->join('v2_pickup_notes as pn', 'v2_rider_pickup_action_logs.pickup_note_id', 'pn.id')
            ->join('v2_pickup_requests as pr', 'v2_rider_pickup_action_logs.pickup_request_id', 'pr.id')
            ->join('v2_pickup_request_attempts as pra', 'pra.pickup_request_id', 'v2_rider_pickup_action_logs.pickup_request_id')
            ->join('riders as r', 'pr.current_rider_id', 'r.id')
            ->join('users as u', 'pr.shipper_id', 'u.id')
            ->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
            ->join('cities as c', 'usi.city_id', 'c.id')
            ->select('v2_rider_pickup_action_logs.id', 'v2_rider_pickup_action_logs.logged_at', 'r.name as rider', 'u.name as shipper', 'usi.pickup_address', 'c.name as city', 'pa.name as type', 'v2_rider_pickup_action_logs.pickup_note_id', 'v2_rider_pickup_action_logs.pickup_request_id', 'pr.created_at', 'pra.assigned_by', 'pr.city_id as city_id', 'pr.current_rider_id');

        if (session('role_id') != 1) {
            $rider_pickup_action_logs = $rider_pickup_action_logs->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($rider_pickup_action_logs)
        // ->editColumn('pickup_note_id', function ($rider_pickup_action_log) {
        //     return str_pad($rider_pickup_action_log->pickup_note_id, 6, '0', STR_PAD_LEFT);
        // })
            ->editColumn('pickup_request_id', function ($rider_pickup_action_log) {
                return str_pad($rider_pickup_action_log->pickup_request_id, 6, '0', STR_PAD_LEFT);
            });

        if ($pn_no = $request->get('search_pn_no')) {
            $rider_pickup_action_logs->where('v2_rider_pickup_action_logs.pickup_request_id', '=', $pn_no);
        }
        if ($assigned_by = $request->get('search_assigned_by')) {
            $rider_pickup_action_logs->where('pra.assigned_by', '=', $assigned_by);
        }
        if ($rider = $request->get('search_rider')) {
            $rider_pickup_action_logs->where('pr.current_rider_id', '=', $rider);
        }
        if ($city = $request->get('search_city')) {
            $rider_pickup_action_logs->where('pr.city_id', '=', $city);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $rider_pickup_action_logs->whereBetween('pr.created_at', [$from, $to]);
        }

        return $datatables->make(true);
    }

    public function arrival_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }

        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }
    public function arrival_piece_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62) {
                if ($shipment->pieces > 1) {
                    $rider_assigned_flag = false;
                    if ($shipment->shipper_status_id == 17) {
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                            //                            $rider = Rider::find($rider_id)->name;
                            $rider = '';
                            $rider_assigned_flag = true;
                        } else {
                            $rider = $pickup_request->rider->name;
                        }
                    } else {
                        AdminPickupsController::generate($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                                $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        }
                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        if ($request->has('weight')) {
                            $shipment->actual_weight = $request->weight;
                            
                        }
                    }
                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['amount'] = $shipment->amount;
                    $details['weight'] = floatval($shipment->actual_weight);
                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public static function cancel($shipment_id)
    {
        $pickup_request_assigned_shipment = V2PickupRequestShipment::where('shipment_id', $shipment_id)->where('status', 0)->orderBy('id', 'desc');

        if ($pickup_request_assigned_shipment->exists()) {
            $pickup_request_assigned_shipment = $pickup_request_assigned_shipment->first();

            $pickup_request = $pickup_request_assigned_shipment->pickup_request;

            $bookings = $pickup_request->booked - 1;

            $pickup_request->booked = $bookings;

            $pickup_request->save();

            ShipmentsPickupJourneyController::add($shipment_id, 4, null, $pickup_request->id);

            $pickup_request_assigned_shipment->delete();

            if ($bookings == 0) {
                $pickup_request->status_id = 4;

                $pickup_request->save();

                if ($pickup_request->pickup_note_request) {
                    $pickup_note = $pickup_request->pickup_note_request->pickup_note;

                    if ($bookings == 0) {
                        V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request->id)->delete();
                    }

                    $pickup_note_requests = V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id);

                    if ($pickup_note_requests->exists()) {
                        if ($bookings == 0) {
                            $pickup_note->pickups = $pickup_note->pickups - 1;
                        }

                        $pickup_note->save();
                    } else {
                        $pickup_note->pickups = 0;
                        $pickup_note->status = 1;

                        $pickup_note->save();
                    }
                }
            }
        }
    }

    public function print_air_waybill($shipment_ids, $rider_name)
    {
        $user_type = null;
        $user_id = null;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();

            $user_name = Auth::user()->name . ' (Admin) #' . $user_id;
        } else {
            $user_name = 'Unknown';
        }

        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        if ($user_type) {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

            $html = '<div>';

            $shipment_details = '';

            foreach ($shipment_ids as $shipment_id) {

                $shipment = Shipment::where('id', $shipment_id)->first();

                $table_start = '<table class="table table-sm table-bordered border twice mb-0" style="page-break-before: always; min-height: 80px;" >
                                <tbody><tr><td class="align-middle" style="width: 40%;">I hereby confirm that i have picked the shipment mentioned in the Description field</td><td class="align-middle" style="width: 30%;"><span class="font-weight-bold">Rider Name: </span><span class="line">' . $rider_name . '</span></td><td class="align-middle" style="width: 30%;"><span class="font-weight-bold">Rider Signature: </span><span class="w-200 ml-auto line"></span></td></tr></tbody>
                      </table>
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
                            ';
                $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                            <td class="color primary"><strong>Datetime</strong></td>
                            <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                            <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                ';

                $table_start .= '
                                <td class="color primary"><strong>Order ID</strong></td>
                                <td>' . $shipment->order_id . '</td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                                <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                              </tr>
                              <tr>
                                <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                                <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                              </tr>
                              <tr>
                                <td class="color secondary"><strong>Name</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->user->name . ' (' . $shipment->pickup_address->poc . ')</td>
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                                <td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                    <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                                <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              </tr>
                ';

                $table_end = '
                              <tr>
                                <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Estimated Weight</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . ' kg</strong></td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                ';

                $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                    ';

                $table_end .= '
                              </tr>';
                if($shipment->shipment_detail()->exists()){
                    if($shipment->shipment_detail->is_open==1){
                    $table_end .= '<tr>
                                <td colspan="2" class="color primary border twice-top twice-bottom twice-left"><strong>Open Box</strong></td>
                                <td colspan="4" class="border twice-top twice-bottom twice-left"><strong> Yes <span><img src="' . asset('img/open_box_icon.png') . '" ></span></strong></td>

                                </tr>';
                }
                }
                $table_end .= '<tr>
                                <td colspan="8" class="text-center border twice-top"><em>Kindly do not give any addtional charges to the Rider/Courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                              </tr>
                            </tbody>
                          </table>

                          <hr>
                ';

                $shipment_details .= $table_start;

                $item = $shipment->items->first();

                $shipment_details .= '
                            <tr>
                              <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                              <td class="color secondary border twice-top"><strong>Type</strong></td>
                              <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                              <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                              <td>' . $item->quantity . '</td>
                              <td colspan="2" class="border twice-top"></td>
                            </tr>
                            <tr>
                              <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                              <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                            </tr>
                ';

                $shipment_details .= $table_end;

            }
            $html .= $shipment_details;
            $html .= '</div>';

            return $html;
        }
    }

    public function rider_receiving_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 9);
        $pickup_actions = PickupAction::all();
        $default_date = Carbon::now();
        $riders = Rider::select('id', 'name')->where('status', 1)->get();
        $cities = City::select('id', 'name')->get();
        return view('admin.v2_pickups.receiving_sheet')->with(['riders' => $riders, 'cities' => $cities, 'pickup_actions' => $pickup_actions, 'default_date' => $default_date]);
    }

    public function rider_receiving_check_pickup(Request $request)
    {
        $pickup_date = $request->pickup_date;
        $rider_id = $request->rider_id;
        if ($pickup_date != null && $rider_id != null) {
            $pickup_note = V2PickupNote::whereDate('created_at', $pickup_date)->where('rider_id', $rider_id);
            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->latest()->first();
                if ($pickup_note) {
                    return response()->json(['status' => 0, 'pickup_note_id' => $pickup_note->id]);
                }
            }
            return response()->json(['status' => 1, 'error' => 'No Pickups found!']);
        }
        return response()->json(['status' => 1, 'error' => 'Please Select filters correctly!']);
    }

    public function rider_receiving_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      .vendor_pickup_row{
                        background-color: var(--light);
                      }
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $id = $request->id;
        $pickup_note = V2PickupNote::find($id);
        $rider = Rider::find($pickup_note->rider_id);
        $route = $rider->route;
        $route_name = '';
        if ($route) {
            $route_name = $route->code . ' (' . $route->start . ' to ' . $route->end . ')';
        }
        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td> ' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td> ' . $pickup_note->rider->city->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Vendor</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Rider Picked</strong></td>
                            <td class="color primary"><strong>Arrived</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

        $serial_number = 1;

        $pickup_note_requests = $pickup_note->pickup_note_requests;
        $reverse_pickup_shipment_ids = array();
        $total_booked = 0;
        $total_rider_picked = 0;
        $total_arrived = 0;
        foreach ($pickup_note_requests as $pickup_note_request) {
            $pickup_request = $pickup_note_request->pickup_request;

            $shipper = $pickup_request->shipper;
            $pickup_address = $pickup_request->pickup_address;
            $color = '';
            if ($pickup_address->vendor != null) {
                $color = 'vendor_pickup_row';
            }
            $rider_pickuped = 0;
            $pickup_request_received_shipments = 0;
            $pickup_date = '';
            $rider_pickups = V2RiderPickup::where('pickup_note_id', $pickup_note_request->pickup_note_id)->where('pickup_request_id', $pickup_request->id);
            if ($rider_pickups->exists()) {
                $rider_pickups = $rider_pickups->latest()->first();
                $rider_pickuped = $rider_pickups->shipments;
                if ($rider_pickups->added_at != '') {
                    $pickup_date = Carbon::parse($rider_pickups->added_at)->format('Y-m-d');
                }

            }
            $pickup_request_received_shipments = count($pickup_request->pickup_request_received_shipments);
            $html .= '
                          <tr class="' . $color . '">
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['vendor'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['booked'] . '</td>
                            <td>' . $rider_pickuped . '</td>
                            <td>' . $pickup_request_received_shipments . '</td>
                            <td>' . $pickup_date . '</td>
                          </tr>
          ';
            $total_booked += $pickup_request['booked'];
            $total_rider_picked += $rider_pickuped;
            $total_arrived += $pickup_request_received_shipments;
            $serial_number++;
        }

        $html .= '
                        <tr>
                          <td colspan="6" style="font-weight: bold; text-align: center;">Total</td>
                          <td  style="font-weight: bold">' . $total_booked . '</td>
                          <td  style="font-weight: bold">' . $total_rider_picked . '</td>
                          <td  style="font-weight: bold">' . $total_arrived . '</td>
                          <td  style="font-weight: bold">-</td>

                        </tr>
                        </tbody>
                      </table>
                      <br>
                      <div>Operation Staff Receiver</div>
                      <br>
                      <br>
                      <div>
                        Name : __________________________
                      </div>
                      <br>
                      <div>
                        Signature : ______________________
                      </div>

                      <hr>
        ';

        $html .= '


                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }
    public function rider_receiving_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 69);
        }

        $from = $request->get('search_date_from');
        $to = strval(Carbon::parse($request->get('search_date_to'))->addDay());

        $count = DB::table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id');

        if ($city = $request->get('search_city')) {
            $count = $count->join('cities as c', function ($join) use ($city) {
                $join->where('r.city_id', $city);
            });
        }

        if ($search_rider = $request->get('search_rider')) {
            $count = $count->where('r.id', '=', $search_rider);
        }

        $count = $count->whereBetween('v2_pickup_notes.created_at', [$from, $to]);

        $count = $count->count();

        $rider = V2PickupNote::join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->select('v2_pickup_notes.id as note_id', 'v2_pickup_notes.id as id', 'v2_pickup_notes.created_at as date', 'r.name as rider', DB::raw('(SELECT SUM(vprs.booked) FROM v2_pickup_note_requests AS vpnr LEFT JOIN v2_pickup_requests AS vprs ON vprs.id = vpnr.pickup_request_id WHERE vpnr.pickup_note_id = v2_pickup_notes.id ) AS total_shipment_count'), DB::raw('(SELECT COUNT(vpnr2.shipment_id) FROM v2_pickup_received_shipments AS vpnr2 WHERE vpnr2.pickup_note_id = v2_pickup_notes.id AND vpnr2.pickup_note_id is not null and vpnr2.created_at between "' . $from . '" and "' . $to . '") AS total_arrived_count'), DB::raw('(SELECT SUM(vrp.shipments) FROM v2_rider_pickups as vrp WHERE vrp.pickup_note_id = v2_pickup_notes.id) AS rider_picked'))
            ->whereBetween('v2_pickup_notes.created_at', [$from, $to])
            ->groupBy('v2_pickup_notes.id');

        if ($city = $request->get('search_city')) {
            $rider = $rider->join('cities as c', function ($join) use ($city) {
                    $join->where('r.city_id', $city);
            });
        }

        if ($search_rider = $request->get('search_rider')) {
            $rider = $rider->where('r.id', '=', $search_rider);
        }

        $datatable = Datatables::of($rider)
            ->setTotalRecords($count)
            ->editColumn('note_id', function ($rider) {
                if ($rider->note_id != null) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($rider->note_id, 6, '0', STR_PAD_LEFT) . '</span></button>'
                    ;
                }
            })
            ->addColumn('total_shipment', function ($data) {
                if ($data->total_shipment_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->total_shipment_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('total_arrived', function ($data) {
                if ($data->total_arrived_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->total_arrived_count . '</button>';
                } else {
                    return 0;
                }
            })

        ;

        return $datatable->make(true);

    }

    public function pickup_route_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 10);
        // $users = User::join('user_shipping_infos as usi','usi.user_id','=','users.id')->select('users.id','pickup_address','users.name','usi.id as address_id')->where('usi.status',1)->get();
        if (session('role_id') != 1) {
            $users = User::join('cities as c','c.id','=','users.city_id')->where('users.status',3)->whereIn('c.hub_id',session('hubs'))->select(['users.id', 'users.name'])->get();
        }
        else{
            $users = User::select(['id', 'name'])->get();
        }
        $cities = City::where('business_category_id', 1)->select(['id', 'name'])->get();
        $riders = Rider::where('status', 1)->select(['id', 'name'])->get();
        return view('admin.v2_pickups.pickup_route')->with(['cities' => $cities, 'riders' => $riders, 'users' => $users]);
    }

    public function rider_tracking_index()
    {
        // $users = User::join('user_shipping_infos as usi','usi.user_id','=','users.id')->select('users.id','pickup_address','users.name','usi.id as address_id')->where('usi.status',1)->get();
        //  $users = User::select(['id','name'])->get();
        //  $cities = City::where('business_category_id', 1)->select(['id','name'])->get();
        //  $riders = Rider::where('status', 1)->select(['id','name'])->get();
        //  return view('admin.v2_pickups.pickup_route')->with(['cities' => $cities,'riders' => $riders,'users' => $users]);
    }

    public function pickup_route_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 70);
        }
        $routes = Route::join('cities', 'routes.city_id', '=', 'cities.id')
            ->leftjoin('riders', 'riders.route_id', '=', 'routes.id')
            ->select(['cities.name as city', 'routes.id as id', 'routes.code as code', 'routes.start', 'routes.end', 'routes.junction', 'routes.status as status', 'routes.created_at', 'riders.name as rider'])->where('routes.route_type_id', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($routes)
            ->editColumn('status', function ($routes) {
                return ($routes->status == 0) ? 'Inactive' : 'Active';
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('active', $keyword) !== false) {
                    $query->where('routes.status', '=', 1);
                } else if (strpos('inactive', $keyword) !== false) {
                    $query->where('routes.status', '=', 0);
                } else {
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
                        $dropdown .= '<button type="button" class="dropdown-item update_route" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Route</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(95, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Route</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Route</div></button>';
                        }
                    }
                    $dropdown .= '<button type="button" class="dropdown-item assign_location" data-target-id=' . $result->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Shipper</div></button>';

                    $dropdown .= '<button type="button" class="dropdown-item view_location" data-target-id=' . $result->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipper</div></button>';

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function edit_route_ajax(Request $request)
    {
        $id = $request->route_id;
        $data = array();
        if ($id) {
            $route = Route::find($id);
            $city_id = $route->city_id;
            $code = $route->code;
            $start = $route->start;
            $end = $route->end;
            $junctions = $route->junction;
            $rider = Rider::where('route_id', $id);
            if ($rider->exists()) {
                $rider_id = $rider->select('id')->first();
                $rider_id = $rider_id->id;
            } else {
                $rider_id = null;
            }
            $data = (['city_id' => $city_id, 'code' => $code, 'start' => $start, 'end' => $end, 'rider_id' => $rider_id, 'junctions' => $junctions]);
            return response()->json(['details' => $data]);
        }
    }

    public static function retail_pickup_assign($pickup_request_id, $rider_id)
    {
        $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 1);
        if ($retail_pickup_note->exists()) {
            $retail_pickup_note = $retail_pickup_note->first();
            $retail_pickup_note->rider_id = $rider_id;
            $retail_pickup_note->assigned_by = Auth::id();
            $retail_pickup_note->assigned_at = Carbon::now();
            $retail_pickup_note->status = 2;
            $retail_pickup_note->save();
        }
    }
    public function retail_pickup_arrival($pickup_request_id)
    {
        $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 2);
        if ($retail_pickup_note->exists()) {
            $retail_pickup_note = $retail_pickup_note->first();
            $retail_pickup_note->status = 3;
            $retail_pickup_note->save();
        }
    }

    public function total_shipments(Request $request)
    {

        $note_id = $request->note_id;
        $note = V2PickupNote::find($note_id);
        $pickup_note_requests = $note->pickup_note_requests;
        $bookings = array();
        if ($pickup_note_requests) {
            foreach ($pickup_note_requests as $note) {
                $pickup_request_id = $note->pickup_request_id;
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                $pickup_request_shipments = $pickup_request->pickup_request_shipments;
                foreach ($pickup_request_shipments as $all_shipments) {
                    $shipment = $all_shipments->shipment_id;
                    $shipment_details = Shipment::find($shipment);
                    $bookings[] = $shipment_details->tracking_number;
                }
            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];

        } else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => false];
        }
    }

    public function arrived_shipments(Request $request)
    {
        $note_id = $request->note_id;
        $note = V2PickupNote::find($note_id);
        $arrived = array();
        if($note){
            $pickup_note_received_shipments = V2PickupReceivedShipment::where('pickup_note_id', $note->id)->pluck('shipment_id')->toArray();
            if(count($pickup_note_received_shipments) > 0){
                foreach ($pickup_note_received_shipments as $shipment) {
                    $shipment_details = Shipment::find($shipment);
                    $arrived[] = $shipment_details->tracking_number;
                }
                return ['status' => 0, 'success' => 'Arrived Shipments', 'arrived' => $arrived];
            }
            return ['status' => 0, 'success' => 'No Arrived Shipments', 'arrived' => false];
        }
        else {
            return ['status' => 0, 'success' => 'No Arrived Shipments', 'arrived' => false];
        }
    }

    public function unassigned_index()
    {
        $riders = Rider::where('status', 1)->select(['id', 'name']);
        $pickup_statuses = V2PickupRequestStatus::all();
        $rider_statuses = V2PickupRequestRiderStatus::all();
        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
        $not_pick_reasons = V2PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $legends = V2PickupRequestLegend::whereNotIn('id', [4, 5, 6])->get();
        $cut_off_time = '17:30:00';
        $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        if ($setting->exists()) {
            $setting = $setting->first();
            $cut_off_time = $setting->setting_value;
        }

        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if ($rider_settings->exists()) {
            $rider_settings = $rider_settings->first();
            $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        }

        return view('admin.v2_pickups.un_assigned')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);
    }

    public function unassigned_list(Request $request)
    {

        $today = Carbon::now()->startOfDay();
        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
        //Assigned Date

            ->select('v2_pickup_requests.id', 'v2_pickup_requests.id as pickup_request_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link', 'v2_pickup_requests.received', 'v2_pickup_requests.received as received_link', 'usi.vendor as vendor_name', 'prs.name as pickup_status', 'v2_pickup_requests.attempts', 'v2_pickup_requests.try_and_buy', 'v2_pickup_requests.vendor', 'v2_pickup_requests.status_id', 'v2_pickup_requests.after_cut_off_time', 'v2_pickup_requests.reverse_pickup')
            ->whereNull('v2_pickup_requests.current_rider_id')
            ->whereNotIn('v2_pickup_requests.status_id', [2, 4]);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        $datatables = Datatables::of($pickup_requests)
            ->setRowAttr([
                'class' => function ($pickup_request) use ($today) {
                    if ($pickup_request->reverse_pickup == 1) {
                        return 'reverse_pickup_row';
                    }
                    if ($pickup_request->vendor != null) {
                        return 'vendor_row';
                    } else if ($pickup_request->try_and_buy == 1) {
                        return 'try_and_buy';
                    } else if ($pickup_request->after_cut_off_time) {
                        return 'after_cut_off_time';
                    } else if (Carbon::parse($pickup_request->pickup_address_created_at)->startOfDay()->diffInDays($today) <= 6) {
                        return 'new_pickup';
                    }
                },
            ])
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('bookings_link', function ($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('action', function ($pickup_request) {
                if (session('role_id') == 1 || in_array(18, session('permissions'))) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>
                    </div>
                  </div>
          ';
                } else {
                    return '';
                }
            });

        return $datatables->make(true);
    }

    public function add_remarks(Request $request){
        $pickup_req = V2PickupRequest::find($request->v2_pickup_req_id);
        if($pickup_req){
            $pickup_req->remarks = $request->add_remark;
            $pickup_req->save();

            NotificationsController::send(177, $request->v2_pickup_req_id);

            return redirect()->back()->with('success', 'Remarks Added');

        }else{
            return redirect()->back()->with('error', 'Pickup Request Not Found!');

        }
    }

    public function all_remarks(Request $request){

        $v2_pickup_request = V2PickupRequest::find($request->pickup_req_id);
        if($v2_pickup_request){

        $trax_reason = '';
        $attempts = V2PickupRequestAttempt::where('pickup_request_id', $v2_pickup_request->id)->whereNotNull('reason_id');
        if ($attempts->exists()) {
            $reason_ids = $attempts->pluck('reason_id')->toArray();
            if (count($reason_ids) > 0) {
                foreach ($reason_ids as $reason_id) {
                    $trax_reason .= V2PickupRequestNotPickReason::find($reason_id)->name . ',' . PHP_EOL;
                }
            }
        }
        $trax_remarks = '';
        $attempts = V2PickupRequestAttempt::where('pickup_request_id', $v2_pickup_request->id)->whereNotNull('trax_remarks');
        if ($attempts->exists()) {
            $trax_remarks_rows = $attempts->pluck('trax_remarks')->toArray();
            if (count($trax_remarks_rows) > 0) {
                foreach ($trax_remarks_rows as $remark) {
                    $trax_remarks .= $remark . ',' . PHP_EOL;
                }
            }
        }

        $shipper_remarks = '';
        $attempts = V2PickupRequestAttempt::where('pickup_request_id', $v2_pickup_request->id)->whereNotNull('shipper_remarks');
        if ($attempts->exists()) {
            $shipper_remarks_rows = $attempts->pluck('shipper_remarks')->toArray();
            if (count($shipper_remarks_rows) > 0) {
                foreach ($shipper_remarks_rows as $remark) {
                    $shipper_remarks .= $remark . ',' . PHP_EOL;
                }
            }
        }

        $pickup_req = V2PickupRequest::leftJoin('v2_rider_pickups as vpr', function ($join) {
                            $join->on('vpr.pickup_request_id', '=', 'v2_pickup_requests.id')
                                ->where('vpr.id', '=',
                                    DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v2_pickup_requests.id)'));
                        })->select('vpr.rider_remarks as rider_remarks')
                        ->where('v2_pickup_requests.id',$request->pickup_req_id);
        $rider_remarks = '';
        if($pickup_req->exists()){
            $rider_remarks = $pickup_req->get()->first()->rider_remarks;

        }
        $data = [];
        $data['trax_reason'] = $trax_reason;
        $data['trax_remarks'] = $trax_remarks;
        $data['shipper_remarks'] = $shipper_remarks;
        $data['rider_remarks'] = $rider_remarks;
        $data['remarks'] = $v2_pickup_request->remarks; 
        $data['reverse_pickup'] = $v2_pickup_request->reverse_pickup;

    }else{
        $data = [];
        $data['trax_reason'] = '';
        $data['trax_remarks'] = '';
        $data['shipper_remarks'] = '';
        $data['rider_remarks'] = ''; 
        $data['remarks'] = ''; 
        $data['reverse_pickup'] = '';

    }
    return response()->json(['status' => 0, 'remarks' => $data]);




    }
}
