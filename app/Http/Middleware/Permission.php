<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;

class Permission
{
    private $actions = [
        'admin' => [
            'dispute.index' => 1,
            'dispute.list' => 1,
            'dispute.create' => 2,
            'dispute.shipments' => 1,
            'dispute.resolve' => 4,
            'dispute.update' => 3,
            'dispute.update.submit' => 3,

            'accounts.pending' => 5,
            'accounts.pending.ajax' => 5,
            'accounts.active' => 11,
            'accounts.active.ajax' => 11,
            'accounts.block' => 15,
            'accounts.block.ajax' => 15,
            'accounts.view.profile' =>110,
            'accounts.get.pickups' =>110,
            'accounts.update.profile' =>111,
            'accounts.update.bank' =>112,


            'pickups.pending.index' => 17,
            'pickups.pending.list' => 17,
            'pickups.pending.assign' => 19,
            'pickups.pending.multiple_cancel' => 18,
            'pickups.pending.cancel' => 18,
            'pickups.assigned.index' => 20,
            'pickups.assigned.list' => 20,
            'pickups.assigned.cancel' => 21,
            'pickups.assigned.view_details' => 20,
            'pickups.assigned.print' => 22,
            'pickups.receive.index' => 23,
            'pickups.receive.list' => 23,
            'pickups.receive.pickup_note' => 24,
            'pickups.receive.shipment_details' => 24,
            'pickups.receive.arrival_of_shipments.index' => 24,
            'pickups.receive.arrival_of_shipments.store' => 24,
            'pickups.receive.summary.index' => 24,
            'pickups.receive.summary.list' => 24,
            'pickups.receive.summary.request.short_received' => 24,
            'pickups.receive.summary.request.over_received' => 24,
            'pickups.receive.summary.request.over_short_received' => 24,
            'pickups.receive.summary.request.done' => 24,
            'pickups.receive.summary.request.not_done' => 24,
            'pickups.bookedvsreceived.index' => 113,
            'pickups.bookedvsreceived.list' => 113,
            'pickups.bookedvsreceived.booked' => 113,
            'pickups.bookedvsreceived.received' => 113,

            'cargo.pending.index' => 25,
            'cargo.pending.list' => 25,
            'cargo.create.index' => 26,
            'cargo.create.shipment_details' => 26,
            'cargo.create.consignment_details' => 26,
            'cargo.create.store' => 26,
            'cargo.in_transit.index' => 27,
            'cargo.in_transit.list' => 27,
            'cargo.in_transit.print' => 27,
            'cargo.in_transit.junctions' => 30,
            'cargo.in_transit.details' => 30,
            'cargo.in_transit.receive_at_link' => 30,
            'cargo.in_transit.forwarding_details' => 27,
            'cargo.in_transit.update' => 28,
            'cargo.in_transit.receive' => 31,
            'cargo.in_transit.shipments' => 27,
            'cargo.receive.index' => 31,
            'cargo.receive.shipment_details' => 31,
            'cargo.receive.short_received' => 31,
            'cargo.receive.store' => 31,

            'sameday.index' => 32,
            'sameday.list' => 32,

            'delivery.pending.index' => 33,
            'delivery.pending.list' => 33,
            'delivery.note.index' => 35,
            'delivery.note.shipment.info' => 35,
            'delivery.note.create' => 35,
            'delivery.receive.index' => 36,
            'delivery.receive.list' => 36,
            'delivery.receive.tracking.search' => 37,
            'delivery.receive.update' => 38,
            'delivery.receive.update.list' => 38,
            'delivery.receive.update.remove' => 38,
            'delivery.receive.status' => 37,
            'delivery.receive.add.status' => 37,
            'delivery.receive.add.list' => 37,
            'delivery.receive.reason' => 37,
            'delivery.receive.delivered' => 37,
            'delivery.receive.shipmentstatuscheck' => 37,
            'delivery.receive.replacements' => 37,
            'delivery.receive.replacements.submit' => 37,
            'delivery.receive.trybuys' => 37,
            'delivery.receive.trybuys.submit' => 37,
            'delivery.receive.status.verify' => 39,
            'delivery.receive.verify.status.list' => 39,
            'delivery.receive.verify.status.submit' => 39,
            'delivery.completed.index' => 40,
            'delivery.completed.list' => 40,
            'delivery.completed.deposit.dncc' => 41,
            'delivery.completed.sdn.create' => 41,
            'delivery.completed.sdn.create.submit' => 41,
            'delivery.completed.dncc.list' => 41,
            'delivery.sdn.index' => 42,
            'delivery.sdn.list' => 42,
            'delivery.sdn.details' => 42,
            'delivery.sdn.ajax' => 42,
            'delivery.sdn.slip' => 43,
            'cash_collection.pending.index' => 105,
            'cash_collection.pending.list' => 105,
            'cash_collection.pending.collect' => 106,
            'cash_collection.pending.all' => 106,
            'misroute.index' => 107,
            'misroute.list' => 107,
            'misroute.shipment.info' => 108,
            'misroute.shipment.update' => 108,

            'return.index' => 44,
            'return.list' => 44,
            'return.confirmed' => 47,
            'return.confirmed.list' => 47,
            'return.confirmed.revert' => 109,
            'return.create.index' => 48,
            'return.create.shipment_details' => 48,
            'return.create.note.submit' => 48,
            'return.receive.index' => 49,
            'return.receive.list' => 49,
            'return.receive.update' => 51,
            'return.receive.update.list' => 51,
            'return.receive.update.remove' => 51,
            'return.receive.status' => 50,
            'return.receive.status.submit' => 50,
            'return.receive.status.delivered' => 50,
            'return.receive.status.list' => 50,
            'return.receive.reason' => 50,
            'return.receive.rn.print' => 49,

            'finance.outstanding_sdn.index' => 52,
            'finance.outstanding_sdn.list' => 52,
            'finance.outstanding_sdn.delivery_notes_list' => 52,
            'finance.outstanding_sdn.reconcile_delivery_notes' => 53,
            'finance.outstanding_sdn.export_to_excel' => 52,
            'finance.outstanding_shipments.index' => 54,
            'finance.outstanding_shipments.list' => 54,
            'finance.outstanding_shipments.resolved' => 55,
            'finance.outstanding_shipments.adjust_in_payment' => 56,
            'finance.outstanding_shipments.change_shipment_amount.index' => 57,
            'finance.outstanding_shipments.change_shipment_amount.shipment_details' => 58,
            'finance.outstanding_shipments.change_shipment_amount.store' => 58,
            'finance.make_payments.index' => 59,
            'finance.make_payments.list' => 59,
            'finance.make_payments.delivered_shipments' => 59,
            'finance.make_payments.returned_shipments' => 59,
            'finance.make_payments.adjusted_shipments' => 59,
            'finance.make_payments.shipment_details' => 59,
            'finance.make_payments.shipment_list' => 59,
            'finance.make_payments.verify' => 60,
            'finance.make_payments.export_bank_order' => 60,
            'finance.make_payments.store' => 60,
            'finance.done_payments.index' => 61,
            'finance.done_payments.list' => 61,
            'finance.done_payments.paid' => 62,
            'finance.done_payments.reverted' => 63,
            'finance.done_payments.delivered_shipments' => 61,
            'finance.done_payments.returned_shipments' => 61,
            'finance.done_payments.adjusted_shipments' => 61,
            'finance.done_payments.details_print' => 61,
            'finance.done_payments.details' => 61,
            'finance.done_payments.update_details' => 61,
            'finance.done_payments.export_to_excel' => 61,

            'reports.pickup_note.index' => 64,
            'reports.pickup_note.list' => 64,
            'reports.cargo_received.index' => 65,
            'reports.cargo_received.list' => 65,
            'reports.cargo_received.shipments' => 65,
            'reports.cargo_received.print' => 65,
            'reports.return_note.index' => 67,
            'reports.return_note.list' => 67,
            'reports.outstanding_shipments.index' => 68,
            'reports.outstanding_shipments.list' => 68,
            'reports.lead_time.index' => 69,
            'reports.lead_time.list' => 69,
            'reports.qsr.index' => 70,
            'reports.qsr.list' => 70,
            'reports.qa.index' => 71,
            'reports.qa.list' => 71,
            'reports.daily_pickup_sales.index' => 73,
            'reports.daily_pickup_sales.export_to_excel' => 73,
            'reports.daily_pickup_sales.download' => 73,
            'reports.customer_sales.index' => 74,
            'reports.customer_sales.export_to_excel' => 74,
            'reports.customer_sales.download' => 74,
            'reports.overall_sales.index' => 75,
            'reports.overall_sales.list' => 75,

            'packaging.index' => 76,
            'packaging.list' => 76,
            'packaging.add.submit' => 77,
            'packaging.send.submit' => 78,
            'packaging.requests.index' => 79,
            'packaging.requests.list' => 79,
            'packaging.requests.dispatch' => 80,

            'user_management.users.index' => 81,
            'user_management.users.list' => 81,
            'user_management.users.status' => 84,
            'user_management.users.add.index' => 82,
            'user_management.users.add.store' => 82,
            'user_management.users.update.index' => 83,
            'user_management.users.update.store' => 83,
            'user_management.roles.index' => 85,
            'user_management.roles.list' => 85,
            'user_management.roles.add.index' => 86,
            'user_management.roles.add.store' => 86,
            'user_management.roles.update.index' => 87,
            'user_management.roles.update.store' => 87,

            'management.city.index' => 88,
            'management.city.ajax' => 88,
            'management.city.form' => 89,
            'management.city.edit' => 90,
            'management.city' => 89,
            'management.city.status' => 91,
            'management.city.status.ajax' => 91,

            'management.route.index' => 92,
            'management.route.ajax' => 92,
            'management.route.add' => 93,
            'management.route.edit' => 94,
            'management.route.status' => 95,

            'management.rider.index' => 96,
            'management.rider.ajax' => 96,
            'management.rider.add' => 97,
            'management.rider.edit' => 98,
            'management.rider.status' => 99,

            'notifications.index' => 100,
            'notifications.list' => 100,
            'notifications.send_custom_email' => 103,
            'notifications.details' => 101,
            'notifications.status' => 102,
            'notifications.edit' => 101,

            'settings.pickup.index' => 104,
            'settings.pickup.weight.add' => 104,

            'settings.shipment_cancellation_cut_off_days.index' => 116,
            'settings.shipment_cancellation_cut_off_days.store' => 116,

            'cancelled_shipments.index' => 117,
            'cancelled_shipments.list' => 117,
            'cancelled_shipments.revert' => 118
        ],

        'shipper' => [
            'shipment.book.index' => 1,
            'shipment.book.store' => 1,
            'shipment.book.order_id' => 1,
            'shipment.book.excel_index' => 1,
            'shipment.book.excel_store' => 1,

            'orders.cancel' => 2,

            'shipment.receiving_sheet.index' => 3,
            'shipment.receiving_sheet.store' => 3,
            'shipment.receiving_sheet.list' => 3,
            'shipment.receiving_sheet.add' => 3,
            'shipment.receiving_sheet.void' => 3,
            'shipment.receiving_sheet.new' => 3,
            'shipment.receiving_sheet.info' => 3,
            'shipment.receiving_sheet_history.index' => 3,
            'shipment.receiving_sheet_history.short_received_list' => 3,
            'shipment.receiving_sheet_history.receiving_sheet_list' => 3,
            'shipment.receiving_sheet_history.booked_shipments' => 3,
            'shipment.receiving_sheet_history.received_shipments' => 3,
            'shipment.receiving_sheet_history.short_received_shipments' => 3,
            'shipment.receiving_sheet_history.void' => 3,
            'shipment.receiving_sheet_history.create' => 3,

            'packaging.requests.index' => 4,
            'packaging.requests.submit' => 4,

            'finance.payments.index' => 5,
            'finance.payments.list' => 5,
            'finance.payments.delivered_shipments' => 5,
            'finance.payments.returned_shipments' => 5,
            'finance.payments.adjusted_shipments' => 5,
            'finance.payments.details_print' => 5,
            'finance.payments.export_to_excel' => 5,

            'dispute.index' => 6,
            'dispute.list' => 6,
            'dispute.create' => 6,
            'dispute.get.shipments' => 6,
            'dispute.get.comments' => 6,
            'dispute.data' => 6,
            'dispute.rebook.index' => 7,
            'dispute.rebook.list' => 7,
            'dispute.rebook.shipment.info' => 7,
            'dispute.rebook.shipment.update' => 7,

            'reports.qsr.index' => 8,
            'reports.qsr.list' => 8,
            'reports.sales.index' => 8,
            'reports.sales.list' => 8
        ]
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::guard('admin')->check()) {
            $action = str_replace('admin.', '', $request->route()->getName());

            if (session('role_id') == 1 || !isset($this->actions['admin'][$action]) || in_array($this->actions['admin'][$action], session('permissions'))) {
                return $next($request);
            }
            else {
                return redirect()->route('admin.access_denied');
            }
        }
        else if (Auth::guard('substitute_users')->check()) {
            $action = str_replace('cod.', '', $request->route()->getName());

            if (session('user_type') == 1 || !isset($this->actions['shipper'][$action]) || in_array($this->actions['shipper'][$action], session('permissions'))) {
                return $next($request);
            }
            else {
                return redirect()->route('cod.access_denied');
            }
        }
        else {
            return $next($request);
        }
    }
}
