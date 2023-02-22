<?php

namespace App\Http\Middleware;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Support\Facades\Auth;
use Session;
use Closure;

class Permission
{
    private $actions = [
        'admin' => [
            'orders.index' => 621,
            'orders.self_collection.index' => 622,
            'orders.shipper_recall' => 139,

            'activity_trail.index' => 471,
            'activity_trail.list' => 471,

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
            'accounts.sister_account.add.account' => 241,
            'accounts.sister_account.add.submit' => 241,
            'accounts.merged_account.index' => 241,
            'accounts.merged_account.list' => 241,
            'accounts.merged_account.info' => 241,
            'accounts.sister_account.edit.index' => 242,
            'accounts.sister_account.edit.submit' => 242,
            'accounts.sister_account.merged_account.mapping.info' => 242,
            'accounts.sister_account.merged_account.mapping.submit' => 242,
            'accounts.receiving_sheet.index' => 364,
            'accounts.restrict_order_id.info' => 619,
            'accounts.restrict_order_id.submit' => 619,

            'corporate.reimbursement_setting.index' => 598,
            'corporate.reimbursement_setting.store' => 598,
            'corporate.reimbursement_setting.approve' => 599,
            'corporate.reimbursement_setting.reject' => 599,

            'daily_visit.index' => 265,
            'daily_visit.store' => 265,
            'daily_visit.business_card' => 264,
            'daily_visit.location_photo' => 264,
            'daily_visit.screen.index' => 791,
            'daily_visit.screen.edit' => 792,


            'pickups.un_assigned.index' => 17,
            'pickups.un_assigned.list' => 17,
            'pickups.pending.index' => 17,
            'pickups.pending.list' => 17,
            'pickups.pending.bookings' => 17,
            'pickups.pending.bookings.all' => 17,
            'pickups.pending.assign' => 19,
            'pickups.pending.multiple_cancel' => 18,
            'pickups.pending.cancel' => 18,
            'pickups.assigned.index' => 20,
            'pickups.assigned.list' => 20,
            'pickups.assigned.bookings.all' => 20,
            'pickups.assigned.pickups' => 20,
            'pickups.assigned.cancel' => 21,
            'pickups.assigned.view_details' => 20,
//            'pickups.assigned.print' => 22,
            'pickups.receive.index' => 23,
            'pickups.receive.list' => 23,
            'pickups.receive.bookings.all' => 23,
            'pickups.receive.pickup_note' => 24,
            'pickups.receive.shipment_details' => 24,
            'pickups.receive.arrival_of_shipments.index' => 24,
            'pickups.receive.arrival_of_shipments.store' => 24,
            'pickups.receive.summary.index' => 24,
            'pickups.receive.summary.list' => 24,
            'pickups.receive.summary.bookings.all' => 24,
            'pickups.receive.summary.received' => 24,
            'pickups.receive.summary.request.short_received' => 24,
            'pickups.receive.summary.request.over_received' => 24,
            'pickups.receive.summary.request.over_short_received' => 24,
            'pickups.receive.summary.request.done' => 24,
            'pickups.receive.summary.request.not_done' => 24,
            'pickups.bookedvsreceived.index' => 113,
            'pickups.bookedvsreceived.list' => 113,
            'pickups.bookedvsreceived.booked' => 113,
            'pickups.bookedvsreceived.received' => 113,
            'pickups.history.index' => 123,
            'pickups.history.list' => 123,
            'pickups.history.bookings.all' => 123,

            'v2_pickups.pending.index' => 17,
            'v2_pickups.arrival.individual.index' => 24,
            'v2_pickups.arrival.bulk.index' => 24,

            'v2_pickups.arrival.project_shippers.index' => 830,
            'v2_pickups.arrival.project_shippers.store' => 830,

            'v2_pickups.rider.index' => 271,
            'v2_pickups.action_log.index' => 272,
            'v2_pickups.rider_receiving.index' => 366,
            'v2_pickups.pickup_route.index' => 406,

            'v2_pickups.rider_receiving.dws.index' => 670,

            'v2_pickups.rider_tracking.index' => 446,
            'v2_pickups.rider_tracking.by_rider' => 446,
            'v2_pickups.rider_tracking.by_city' => 446,



            'cargo.pending.index' => 25,
            'cargo.pending.list' => 25,
            'cargo.draft.index' => 26,
            'cargo.draft.list' => 26,
            'cargo.create.index' => 26,
            'cargo.create.shipment_details' => 26,
            'cargo.create.consignment_details' => 26,
            'cargo.create.store' => 26,
            'cargo.in_transit.index' => 27,
            'cargo.in_transit.list' => 27,
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
            'cargo.history.index' => 124,
            'cargo.history.list' => 124,
            'cargo.history.shipments.' => 124,
            'cargo.history.print.' => 124,
            'cargo.mapping.index' => 198,
            'cargo.mapping.list' => 198,
            'cargo.mapping.store' => 198,
            'cargo.mapping.edit' => 198,
            'cargo.mapping.update' => 198,
            'cargo.mapping.manifest.index' => 544,
            'cargo.mapping.manifest.list' => 544,
            'cargo.mapping.manifest.store' => 548,
            'cargo.mapping.manifest.edit' => 549,
            'cargo.mapping.manifest.update' => 549,
            'cargo.mapping.manifest.status' => 550,
            'cargo.receive.quick.index' => 31,
            'cargo.receive.quick.shipment_details' => 31,
            'cargo.receive.quick.store' => 31,
            'cargo.receive.quick.list.index' => 31,
            'cargo.receive.quick.list.details' => 31,
            'cargo.receive.quick.list.ajax' => 31,
            'cargo.supply_chain.supply_chain_index' => 376,

            'cargo.supply_chain.shipment_on_hold.index' => 404,
            'cargo.supply_chain.shipment_on_hold.shipment_details' => 404,
            'cargo.supply_chain.shipment_on_hold.store' => 404,

            'cargo.supply_chain.shipment_on_hold.history.index' => 405,
            'cargo.supply_chain.shipment_on_hold.history.list' => 405,
            'cargo.supply_chain.shipment_on_hold.history.allow_dispatch_delivery' => 405,

            'master_cargo.bag.pending.index' => 25,
            'master_cargo.bag.pending.list' => 25,
            'master_cargo.bag.create.index' => 26,
            'master_cargo.bag.create.shipment_details' => 26,
            'master_cargo.bag.create.bag_details' => 26,
            'master_cargo.bag.create.store' => 26,
            'master_cargo.bag.create.open_bag.index' => 26,
            'master_cargo.bag.create.open_bag.store' => 26,
            'master_cargo.bag.history.index' => 124,
            'master_cargo.bag.history.list' => 124,
            'master_cargo.bag.receive.quick.index' => 31,
            'master_cargo.bag.receive.quick.store' => 31,

            'master_cargo.pending.index' => 25,
            'master_cargo.pending.list' => 25,
            'master_cargo.pending.shipments' => 25,
            'master_cargo.create.index' => 26,
            'master_cargo.create.bag_details' => 26,
            'master_cargo.create.cargo_details' => 26,
            'master_cargo.create.store' => 26,
            'master_cargo.in_transit.index' => 27,
            'master_cargo.in_transit.list' => 27,
            'master_cargo.in_transit.bags' => 27,
            'master_cargo.in_transit.shipments' => 27,
            'master_cargo.in_transit.receive' => 31,
            'master_cargo.receive.index' => 31,
            'master_cargo.receive.bag_details' => 31,
            'master_cargo.receive.short_received' => 31,
            'master_cargo.receive.store' => 31,
            'master_cargo.history.index' => 124,
            'master_cargo.history.list' => 124,
            'master_cargo.received.index' => 124,
            'master_cargo.received.list' => 124,
            'master_cargo.receive.quick.index' => 31,
            'master_cargo.receive.quick.bag_details' => 31,
            'master_cargo.receive.quick.store' => 31,
            'master_cargo.receive.quick.list.index' => 31,
            'master_cargo.receive.quick.list.details' => 31,
            'master_cargo.receive.quick.list.ajax' => 31,


            'cargo_manifest.bags.pending.index' => 545,
            'cargo_manifest.bags.pending.list' => 545,
            'cargo_manifest.bags.create.index' => 546,
            'cargo_manifest.bags.create.open_bag.index' => 559,
            'cargo_manifest.bags.history.index' => 547,
            'cargo_manifest.bags.history.list' => 547,
            'cargo_manifest.create' => 551,
            'cargo_manifest.store' => 551,
            'cargo_manifest.index' => 564,
            'cargo_manifest.list' => 564,
            'cargo_manifest.receive.index' => 554,
            'cargo_manifest.receive.store' => 554,
            'cargo_manifest.history' => 556,
            'cargo_manifest.history.list' => 556,

            'cargo_manifest.draft.setting.list' => 682,
            'cargo_manifest.draft.setting' => 682,
            'cargo_manifest.draft.update' => 682,




            'sameday.index' => 32,
            'sameday.list' => 32,

            'delivery.pending.index' => 33,
            'delivery.pending.list' => 33,
            'delivery.note.index' => 35,
            'delivery.note.shipment.info' => 35,
            'delivery.note.create' => 35,

            'delivery.quick_receiving.index' => 464,

            'delivery.receive.index' => 36,
            'delivery.receive.list' => 36,
            'delivery.receive.shipments' => 36,
            'delivery.receive.tracking.search' => 37,
            'delivery.receive.update' => 38,
            'delivery.receive.update.list' => 38,
            'delivery.receive.update.remove' => 38,
            'delivery.receive.status' => 37,
            'delivery.receive.add.status' => 37,
            'delivery.receive.add.list' => 37,
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
            'delivery.completed.shipments' => 40,
            'delivery.completed.shipments.delivered' => 40,
            'delivery.completed.deposit.dncc' => 41,
            'delivery.completed.sdn.create' => 41,
            'delivery.completed.sdn.create.submit' => 41,
            'delivery.completed.dncc.list' => 41,
            'delivery.sdn.index' => 42,
            'delivery.sdn.list' => 42,
            
            'delivery.sdn.back_to_deposit' => 604,

            'delivery.sdn.dncc.add' => 605,
            'delivery.sdn.dncc.get.add' => 605,
            'delivery.sdn.dncc.remove' => 605,
            'delivery.sdn.dncc.get.remove' => 605,
            'delivery.sdn.pncc.add' => 605,
            'delivery.sdn.pncc.get.add' => 605,
            'delivery.sdn.pncc.remove' => 605,
            'delivery.sdn.pncc.get.remove' => 605,

            'delivery.sdn.dn' => 42,
            'delivery.sdn.shipments' => 42,
            'delivery.sdn.details' => 42,
            'delivery.sdn.ajax' => 42,
            'delivery.sdn.slip' => 43,
            'delivery.cash_collection.pending.index' => 105,
            'delivery.cash_collection.pending.list' => 105,
            'delivery.cash_collection.pending.shipments' => 105,
            'delivery.cash_collection.pending.shipments.delivered' => 105,
            'delivery.cash_collection.pending.collect' => 106,
            'delivery.cash_collection.pending.all' => 106,
            'delivery.misroute.index' => 107,
            'delivery.misroute.list' => 107,
            'delivery.misroute.update.index' => 108,
            'delivery.misroute.update.shipment.info' => 108,
            'delivery.misroute.update.store' => 108,
            'delivery.misroute.shipment.update' => 108,
            'delivery.misroute.history.index' => 119,
            'delivery.misroute.history.list' => 119,
            'delivery.history.index' => 125,
            'delivery.history.list' => 125,
            'delivery.history.shipments' => 125,
            'delivery.history.shipments.delivered' => 125,
            'delivery.lost.index' => 127,
            'delivery.lost.list' => 127,
            'delivery.lost.confirm.status' => 128,
            'delivery.lost.reattempt.status' => 129,
            'delivery.lost.add.index' => 130,
            'delivery.lost.add.shipment.info' => 130,
            'delivery.lost.add.shipments.store' => 130,
            'delivery.intercept.index' => 193,
            'delivery.intercept.approve' => 194,
            'delivery.intercept.reject' => 195,
            'delivery.intercept.history.index' => 196,
            'delivery.fake_status.index' => 203,
            'delivery.fake_status.list' => 203,
            'delivery.replacement.not_collected.index' => 206,
            'delivery.replacement.not_collected.list' => 206,
            'delivery.replacement.not_collected.re_attempt' => 206,
            'delivery.replacement.not_collected.regular_re_attempt' => 206,
            'delivery.replacement.collected.index' => 207,
            'delivery.replacement.collected.list' => 207,
            'delivery.replacement.collected.change_booking_type' => 207,
            'delivery.replacement.logs.index' => 208,
            'delivery.replacement.logs.list' => 208,
            'delivery.fake_status.log.index' => 262,
            'delivery.fake_status.log.store' => 262,
            'delivery.cash_collection.retail.index' => 423,
            'delivery.completed.retail.index' => 424,
            'delivery.signature.index' => 441,
            'delivery.signature.list' => 441,
            'delivery.note.request_index' => 531,
            'delivery.note.request_list' => 531,

            'ftl.request.index' => 511,
            'ftl.request.list' => 511,
            'ftl.request.add' => 512,
            'ftl.request.view' => 513,
            'shipment.book.ftl.walk_in' => 516,
            'shipment.book.ftl.store' => 516,

            'return.index' => 44,
            'return.list' => 44,
            'return.confirm.status' => 45,
            'return.reattempt.status' => 46,
            'return.confirmed' => 47,
            'return.confirmed.list' => 47,
            'return.confirmed.revert' => 109,
            'return.create.index' => 48,
            'return.create.shipment_details' => 48,
            'return.create.note.submit' => 48,
            'return.receive.index' => 49,
            'return.receive.list' => 49,
            'return.receive.shipments' => 49,
            'return.receive.update' => 51,
            'return.receive.update.list' => 51,
            'return.receive.update.remove' => 51,
            'return.receive.status' => 50,
            'return.receive.status.submit' => 50,
            'return.receive.status.delivered' => 50,
            'return.receive.status.list' => 50,
            'return.receive.reason' => 50,
            'return.receive.rn.print' => 49,
            'return.history.index' => 126,
            'return.history.list' => 126,
            'return.history.shipments' => 126,
            'return.cx_sales.index' => 266,
            'return.cx_sales.list' => 266,
            'return.return_deliveries.index' => 566,
            'return.return_deliveries.list' => 566,
            'return.revert.index' => 643,
            'return.confirmation_pending_sms' => 675,

            'finance.outstanding_sdn.index' => 52,
            'finance.outstanding_sdn.list' => 52,
            'finance.outstanding_sdn.dncc' => 52,
            'finance.outstanding_sdn.shipments.delivered' => 52,
            'finance.outstanding_sdn.delivery_notes_list' => 52,
            'finance.outstanding_sdn.reconcile_delivery_notes' => 53,
            'finance.outstanding_sdn.export_to_excel' => 52,
            'finance.outstanding_shipments.index' => 54,
            'finance.outstanding_shipments.list' => 54,
            'finance.outstanding_shipments.dncc.print' => 54,
            'finance.outstanding_shipments.sdn.print' => 54,
            'finance.outstanding_shipments.resolved' => 55,
            'finance.outstanding_shipments.adjust_in_payment' => 56,
            'finance.change_shipment_amount.index' => 57,
            'finance.change_shipment_amount.shipment_details' => 57,
            'finance.change_shipment_amount.store' => 58,
            'finance.change_shipment_weight.index' => 134,
            'finance.change_shipment_weight.shipment_details' => 134,
            'finance.change_shipment_weight.store' => 135,
            'finance.add_shipment_adjustment.index' => 136,
            'finance.add_shipment_adjustment.shipment_details' => 136,
            'finance.add_shipment_adjustment.store' => 137,
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
            'finance.done_payments.excel_store' => 268,
            'finance.invoices.index' => 120,
            'finance.invoices.list' => 120,
            'finance.invoices.print' => 120,
            'finance.invoices.export_to_excel' => 120,
            'finance.invoices.email_reminder' => 121,
            'finance.invoices.mark_as_received' => 122,
            'finance.outstanding_shipments.walk_in_index' => 167,
            'finance.outstanding_shipments.walk_in_list' => 167,
            'finance.outstanding_shipments.walk_in_resolved' => 168,

            'finance.invoices.reimbursement.index' => 625,
            'finance.invoices.reimbursement.list' => 625,
            'finance.invoices.reimbursement.print' => 625,
            'finance.invoices.reimbursement.export_to_excel' => 625,
            'finance.invoices.reimbursement.print_gst_wise' => 625,
            'finance.invoices.reimbursement.print_origin_wise' => 625,

            'finance.invoices.slip' => 589,

            'finance.make_payments_pickup_wise.index' => 396,
            'finance.retail.make_payments.index' => 454,
            'finance.retail.make_payments.list' => 454,
            'finance.retail.done_payments.index' => 455,
            'finance.retail.done_payments.list' => 455,

            'petty_cash.make.index' => 145,
            'petty_cash.statements.index' => 146,
            'petty_cash.statements.list' => 146,
            'petty_cash.statements.edit' => 147,

            'petty_cash.approved.index' => 645,

            'petty_cash.approved.view' => 461,
            'petty_cash.approved.view.list' => 461,

            'petty_cash.draft.index' => 238,
            'petty_cash.draft.list' => 238,
            'petty_cash.draft.view' => 239,

            'petty_cash.rejected.index' => 243,
            'petty_cash.rejected.list' => 243,

            'month_closing.pending.index' => 408,
            'month_closing.pending.list' => 408,
            'month_closing.resolved.index' => 409,
            'month_closing.resolved.list' => 409,


            'reports.pickup_note.index' => 64,
            'reports.pickup_note.list' => 64,
            'reports.pickup_note.bookings' => 64,
            'reports.pickup_note.print' => 64,
            'reports.cargo_received.index' => 65,
            'reports.cargo_received.list' => 65,
            'reports.cargo_received.shipments' => 65,
            'reports.cargo_received.print' => 65,
            'reports.completed_delivery_notes.index' =>66,
            'reports.completed_delivery_notes.list' =>66,
            'reports.completed_delivery_notes.shipments' =>66,
            'reports.completed_delivery_notes.shipments.delivered' =>66,
            'reports.return_note.index' => 67,
            'reports.return_note.list' => 67,
            'reports.return_note.print' => 67,
            'reports.return_note.shipments' => 67,
            'reports.outstanding_shipments.index' => 68,
            'reports.outstanding_shipments.list' => 68,
            'reports.outstanding_shipments.dncc.print' => 68,
            'reports.outstanding_shipments.sdn.print' => 68,
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
            'reports.sales_person_performance.index' => 138,
            'reports.sales_person_performance.export_to_excel' => 138,
            'reports.sales_person_performance.download' => 138,
            'reports.negative_balance_customers.index' => 148,
            'reports.negative_balance_customers.list' => 148,
            'reports.call_verification.index' => 153,
            'reports.call_verification.list' => 153,
            'reports.petty_cash.index' => 156,
            'reports.petty_cash.list' => 156,
            'reports.fake_status.index' => 169,
            'reports.fake_status.list' => 169,
            'reports.fake_status.shipments.total' => 169,
            'reports.fake_status.shipments.undelivered' => 169,
            'reports.fake_status.fake_status_shipment' => 169,
            'reports.debriefing.index' => 170,
            'reports.debriefing.list' => 170,
            'reports.debriefing.export' => 170,
            'reports.cargo_returns_shipment.index' => 172,
            'reports.cargo_returns_shipment.list' => 172,
            'reports.return_reattempt_ratio.index' => 174,
            'reports.return_reattempt_ratio.list' => 174,
            'reports.multiple_payment_report.index' => 176,
            'reports.multiple_payment_report.list' => 176,
			'reports.revenue.index' => 177,
            'reports.revenue.list' => 177,
			'reports.crm.index' => 200,
            'reports.crm.list' => 200,
            'reports.gst.index' => 199,
            'reports.gst.list' => 199,
            'reports.summary.index' => 210,
            'reports.summary.data' => 210,
            'reports.summary.list' => 210,
            'reports.account_activation.index' => 246,
            'reports.account_activation.list' => 246,
            'reports.adjustments.index' => 248,
            'reports.adjustments.list' => 248,
            'reports.account_edit.index' => 255,
            'reports.sdn.index' => 252,
            'reports.sdn.list' => 252,
            'reports.account_edit.list' => 255,
            'reports.bank_history.index' => 257,
            'reports.bank_history.list' => 257,
            'reports.consignee_details.index' => 258,
            'reports.consignee_details.list' => 258,
            'reports.booked_and_cancelled.index' => 259,
            'reports.booked_and_cancelled.list' => 259,
            'reports.fake_status_shipments.index' => 263,
            'reports.fake_status_shipments.list' => 263,
            'reports.daily_visit.index' => 264,
            'reports.daily_visit.list' => 264,
            'reports.delivered_shipment.index' => 275,
            'reports.delivered_shipment.list' => 275,
            'reports.route_distribution.index' => 300,
            'reports.route_distribution.list' => 300,
            'reports.destination_delivery_received.index' => 300,
            'reports.destination_delivery_received.list' => 300,
            'reports.account_reconciliation.index' => 312,
            'reports.cargo_short_received_shipments.index' => 319,
            'reports.cargo_short_received_shipments.list' => 319,
            'reports.multiple_iban.index' => 328,
            'reports.multiple_iban.list' => 328,
            'reports.pickup_report.index' => 337,
            'reports.pickup_report.list' => 337,
            'reports.pickup_report.data' => 337,
            'reports.not_attempted_aging.index' => 360,
            'reports.not_attempted_aging.list' => 360,
            'reports.daily_monthly_adjustment.index' => 373,
            'reports.daily_monthly_adjustment.list' => 373,
            'reports.daily_monthly_adjustment.summary_list' => 373,
            'reports.petty_cash_expense_summary.index' => 395,
            'reports.app_efficiency.index' => 401,
            'reports.confirmation_pending_report.index' => 430,
            'reports.last_mile_app.index' => 437,
            'reports.weight_qc.index' => 444,
            'reports.weight_qc.list' => 444,
            'reports.master_cargo.bag.in_transit.index' => 472,
            'reports.master_cargo.bag.in_transit.list' => 472,
            'reports.master_cargo.short_received_shipments.index' =>476,
            'reports.master_cargo.short_received_shipments.list' => 476,

            'reports.manifest.short_received_shipments.index' =>555,
            'reports.manifest.short_received_shipments.list' => 555,

            'reports.retail_sales.index' =>493,
            'reports.retail_sales.list' => 493,

            'reports.shipper_insurance.index' => 502,
            'reports.shipper_insurance.list' => 502,
            
            'reports.reverse_pickup.index' => 624,
            'reports.reverse_pickup.list' => 624,
            
            'reports.crm_count.index' => 673,
            'reports.crm_count.list' => 673,

            'reports.pickup_history_cn_wise.index' => 679,
            'reports.pickup_history_cn_wise.list' => 679,

            'reports.rider_unresponsive_report.index' => 705,
            'reports.rider_unresponsive_report.list' => 705,

            'reports.mms.index' => 780,
            'reports.mms.list' => 780,

            'reports.ssr.index' => 793,
            'reports.ssr.list' => 793,

            'reports.shipper_summary.index' => 794,
            'reports.shipper_summary.list' => 794,

            'reports.crm_agent_wise_report.index' => 784,

            'packaging.index' => 76,
            'packaging.list' => 76,
            'packaging.add.submit' => 77,
            'packaging.send.submit' => 78,
            'packaging.requests.index' => 79,
            'packaging.requests.list' => 79,
            'packaging.requests.dispatch' => 80,
            'packaging.requests.confirm' => 226,
            'packaging.requests.cancel' => 227,
            'packaging.types.index' => 214,
            'packaging.types.list' => 214,
            'packaging.types.add' => 215,
            'packaging.types.edit' => 215,
            'packaging.types.details' => 215,
            'packaging.types.enable_disable' => 216,
            'packaging.warehouse.index' => 217,
            'packaging.warehouse.list' => 217,
            'packaging.warehouse.enable_disable' => 218,
            'packaging.warehouse.add' => 219,
            'packaging.warehouse.edit' => 219,
            'packaging.warehouse.master_add' => 220,
            'packaging.inventory.index' => 221,
            'packaging.inventory.list' => 221,

            'user_management.users.rejoin' => 620,

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
            'user_management.crm.index' => 188,
            'user_management.crm.list' => 188,
            'user_management.user_requests.index' => 394,
            'user_management.user_requests.list' => 394,
            //  Fuel Management Permissions
            'user_management.fuel_management.index' => 447,
            'user_management.fuel_management.list' => 447,
            'user_management.fuel_management.create' => 451,
            'user_management.fuel_management.search_by_card' => 451,
            'user_management.fuel_management.store' => 451,
            'user_management.fuel_management.approve' => 448,
            'user_management.fuel_management.edit' => 450,
            'user_management.fuel_management.history.index' => 452,

            'management.zonal.index' => 131,
            'management.zonal.list' => 131,
            'management.zonal.add.index' => 132,
            'management.zonal.add.store' => 133,
            'management.zonal.update.index' => 133,
            'management.zonal.update.store' => 133,
            'management.zonal.view_cities' => 131,

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

            'management.riders.rejoin' => 620,

            'management.rider.index' => 96,
            'management.rider.ajax' => 96,
            'management.rider.add' => 97,
            'management.rider.edit' => 98,
            'management.rider.status' => 99,

            'management.riders.permanent.index' => 377,
            'management.riders.permanent.list' => 377,
            'management.riders.incentive.index' => 378,
            'management.riders.incentive.list' => 378,
            'management.riders.blacklist.index' => 379,
            'management.riders.blacklist.list' => 379,
            'management.riders.sms_history.index' => 380,
            'management.riders.sms_history.list' => 380,
            'management.riders.rider_request.index' => 425,
            'management.riders.rider_request.list' => 425,


            'management.city_list' => 205,
            'management.territory.index' => 443,


            'management.area.index' => 443,


            'notifications.index' => 100,
            'notifications.list' => 100,
            'notifications.send_custom_email' => 103,
            'notifications.send_custom_notification' => 103,
            'notifications.details' => 101,
            'notifications.status' => 102,
            'notifications.edit' => 101,

            'app_notifications.index' => 601,
            'app_notifications.list' => 601,
            'app_notifications.details' => 602,
            'app_notifications.edit' => 602,
            'app_notifications.status' => 603,

            'crm.permissions' => 188,
            'crm.list' => 188,
            'crm.update.index' => 188,
            'crm.update.list' => 188,
            'crm.launched_re_open.index' => 233,
            'crm.launched_re_open.list' => 233,
            'crm.in_process.index' => 234,
            'crm.in_process.list' => 234,
            'crm.resolved.index' => 235,
            'crm.resolved.list' => 235,
            'crm.closed.index' => 236,
            'crm.closed.list' => 236,
            'crm.escalation_status' => 352,
            'crm.escalate' => 353,
            'crm.consignee_info.index' => 363,
            'crm.consignee_info.list' => 363,

            'settings.shippers.status_webhook.index' => 646,
            'settings.shippers.status_webhook.list' => 646,
            'settings.shippers.status_webhook.edit' => 646,
            'settings.shippers.status_webhook.update' => 646,

            'settings.pickup.index' => 104,
            'settings.pickup.weight.add' => 104,

            'settings.shipment_cancellation_cut_off_days.index' => 116,
            'settings.shipment_cancellation_cut_off_days.store' => 116,

            'settings.auto_account_disabled_days.auto_index' => 149,
            'settings.auto_account_disabled_days.auto_store' => 149,

            'settings.non_service_area.index' => 150,
            'settings.non_service_area.store' => 150,

            'settings.daily_pickup_sales_cron.index' => 151,
            'settings.daily_pickup_sales_cron.store' => 151,

            'settings.ticker.index' => 152,
            'settings.ticker.store' => 152,

            'settings.walk_in.index' => 154,
            'settings.walk_in.store' => 154,
            'settings.international_walk_in.index' => 154,
            'settings.international_walk_in.store' => 154,
		    'settings.petty_cash.heads.index' => 157,
            'settings.petty_cash.heads.list' => 157,
            'settings.petty_cash.heads.add' => 159,
            'settings.petty_cash.heads.edit' => 160,
            'settings.petty_cash.heads.active' => 161,
            'settings.petty_cash.heads.inactive' => 162,

            'settings.petty_cash.titles.index' => 158,
            'settings.petty_cash.titles.list' => 158,
            'settings.petty_cash.titles.add' => 163,
            'settings.petty_cash.titles.edit' => 164,
            'settings.petty_cash.titles.active' => 165,
            'settings.petty_cash.titles.inactive' => 166,

            'settings.petty_cash.consignee.index' => 462,
            'settings.petty_cash.consignee.list' => 462,
            'settings.petty_cash.consignee.store' => 462,
            'settings.petty_cash.consignee.edit' => 462,
            'settings.petty_cash.consignee.city.index' => 462,
            'settings.petty_cash.consignee.city.check' => 462,
            'settings.petty_cash.consignee.city.update' => 462,

			'settings.auto_invoice_generation_and_due_date.index' => 171,
            'settings.auto_invoice_generation_and_due_date.store' => 171,
			'settings.debriefing_report_cut_off_time.index' => 175,
            'settings.debriefing_report_cut_off_time.store' => 175,
            'settings.debriefing_break_time.index' => 674,
            'settings.debriefing_break_time.store' => 674,
            'settings.return_note_restriction_bypass.index' => 192,
            'settings.cod_cap_zones.index' => 197,
            'settings.cod_cap_zones.update' => 197,
			'settings.stock_movement.index' => 228,
            'settings.stock_movement.update' => 228,
            'settings.delivery_call_verification_ratio.index' => 231,
            'settings.delivery_call_verification_ratio.update' => 231,
            'settings.crm_cut_off_time_and_holidays.index' => 237,
            'settings.delivery_call_verification_ratio.list' => 237,
            'settings.crm_cut_off_time_and_holidays.update' => 237,
            'settings.delivery_call_verification_ratio.add' => 237,
			'settings.consolidation.max.index' => 256,
            'settings.consolidation.max.update' => 256,
            'settings.crm_case_nature_types.index' => 260,
            'settings.crm_case_nature_types.list' => 260,
            'settings.crm_case_nature_types.store' => 260,
            'settings.return_confirmation_pending_shipment_selection_time.index' => 253,
            'settings.return_confirmation_pending_shipment_selection_time.store' => 253,

            'settings.return_delivered_to_shipper_email_cut_off_time.index' => 269,
            'settings.return_delivered_to_shipper_email_cut_off_time.store' => 269,

            'settings.crm_reopen.index' => 274,
            'settings.crm_reopen.update' => 274,

            'settings.crm_comment.index' => 325,
            'settings.crm_comment.store' => 325,

            'settings.multiple_sale_tagging.index' => 279,
            'settings.multiple_sale_tagging.list' => 279,

            'settings.foc_account.index' => 302,
            'settings.minimum_chargeable_weight.index' => 303,

            'settings.sales.projection.percentage.index' => 313,
            'settings.sales.projection.reasons.index' => 314,
            'settings.sales.projection.shipment.index' => 318,

            'settings.sales.key_accounts.dashboard' => 397,
            'settings.sales.key_accounts.dashboard.details' => 397,

            'settings.settings.blacklist.index' => 329,
            'settings.blacklist.search.index' => 330,

            'settings.return.reason.index' => 335,
            'settings.return.reason.list' => 335,


            'settings.commission.index' => 331,


            'settings.commission.percentage.index' => 332,
            'settings.commission.percentage.store' => 332,

            'settings.escalation.launched.index' => 347,
            'settings.escalation.launched.list' => 347,
            'settings.escalation.launched.add.index' => 347,
            'settings.escalation.launched.add.store' => 347,
            'settings.escalation.launched.edit.index' => 347,
            'settings.escalation.launched.edit.store' => 347,

            'settings.escalation.in_process.index' => 348,
            'settings.escalation.in_process.list' => 348,
            'settings.escalation.in_process.add.index' => 348,
            'settings.escalation.in_process.add.store' => 348,
            'settings.escalation.in_process.edit.index' => 348,
            'settings.escalation.in_process.edit.store' => 348,

            'settings.escalation.levels.index' => 349,
            'settings.escalation.levels.store' => 349,

            'settings.escalation.tagging.index' => 350,
            'settings.escalation.tagging.list' => 350,
            'settings.escalation.tagging.add.index' => 350,
            'settings.escalation.tagging.add.store' => 350,
            'settings.escalation.tagging.edit.index' => 350,
            'settings.escalation.tagging.edit.store' => 350,

            'settings.default_agent.index' => 351,
            
            'settings.holidays.index' => 358,
            'settings.holidays.list' => 358,
            'settings.holidays.add' => 358,

            'settings.not_attempted_cron.index' => 359,
            'settings.not_attempted_cron.store' => 359,

            'settings.nsa_account.index' => 367,
            'settings.nsa_account.store' => 367,

            'settings.carrefour_account.index' => 580,
            'settings.carrefour_account.store' => 580,

            'settings.restrict_cities_intercept.index' => 375,
            'settings.restrict_cities_intercept.store' => 375,

            'settings.short_received_hub_wise_cron.index' => 387,
            'settings.short_received_hub_wise_cron.store' => 387,

            'settings.restrict_parcels_attempt.index' => 384,
            'settings.restrict_parcels_attempt.list' => 384,
            'settings.restrict_parcels_attempt.add' => 384,
            'settings.restrict_parcels_attempt.edit' => 384,
            'settings.restrict_parcels_attempt.enable_disable' => 384,

            'settings.runner.index' => 385,
            'settings.runner.list' => 385,
            'settings.runner.add' => 385,
            'settings.runner.unique' => 385,
            'settings.runner.enable_disable' => 385,

			'settings.sms_shipper_wise.index' => 388,
            'settings.sms_shipper_wise.update' => 388,
            

            'settings.pickup_address_wise_payment_accounts.index' => 391,

            'settings.international_rates.index' => 438,
            'settings.international_rates.upload.index' => 477,

            'settings.hr.rider_incentive.index' => 491,
            'settings.hr.rider_incentive.cron.index' => 494,

            'settings.shipment_status_eta.index' => 534,
            'settings.shipment_status_eta.list' => 534,


            'settings.shippers_origin_change.index' => 667,
            'settings.shippers_return_address.index' => 668,
            'settings.rcp_sms.index' => 680,

            'settings.consignee_sms_expire.index' => 683,
			'settings.sales.user_restriction.index' => 681,

            'settings.return_shipments_address.index' => 689,

            'settings.return_reason_mandatory.index' => 684,
            'settings.return_reason_mandatory.list' => 684,

            'settings.rider_deactivation_cron.index' => 707,
            'settings.rider_deactivation_cron.store' => 707,

            'settings.rider_shipment_attempt.index' => 562,
            'settings.rider_shipment_attempt.store' => 562,

            'settings.project_arrival_shippers.index' => 828,
            'settings.project_arrival_shippers.store' => 828,


            'dashboard.userwise' => 333,
            'dashboard.overall' => 334,


            'cancelled_shipments.index' => 117,
            'cancelled_shipments.list' => 117,
            'cancelled_shipments.revert' => 118,



            'shipment.book.walk_in' => 155,
            'shipment.book.store' => 155,

            'shipment.book.international_walk_in' => 155,
            'shipment.book.international_store' => 155,

            'shipment.history.walk_in_history' => 209,
            'shipment.history.walk_in_history_list' => 209,

			'shipment.consolidation.history.index' => 254,
            'shipment.consolidation.history.list' => 254,

            'intercept.index' => 245,
            'intercept.update' => 245,

            'scanning_history.index' => 306,
            'airway_journey.index' => 567,

            'dashboard.sales.index' => 315,

            'coordinates.add.index' => 324,
            'coordinates.add.submit' => 324,
            'coordinates.add.shipment_details' => 324,
            'coordinates.add.search.address' => 324,

            'dashboard.overall.commission' => 334,
            'dashboard.overall.commission.list' => 334,
            'dashboard.overall.commission.data' => 334,
             
            //handover module
            'handover.create.index' => 339,
            'handover.create.fetch' => 339,
            'handover.create.fetch1' => 339,
            'handover.create.shipment_details' => 339,
            'handover.create.store' => 339,

            'handover.receive.index' => 341,
            'handover.receive.shipment_details' => 341,
            'handover.receive.store' => 341,

            'handover.list.index' => 342,
            'handover.list.list' => 342,
            'handover.list.shipments' => 342,
            'handover.list.delivered' => 342,
            'handover.list.print' => 342,

            'handover.responsibles.index' => 340,
            'handover.responsibles.list' => 340,
            'handover.responsibles.add' => 340,
            'handover.responsibles.status' => 340,
            'handover.responsibles.details' => 340,
            'handover.responsibles.edit' => 340,


            'multiple_pieces.add.index' => 368,
            'multiple_pieces.hold.index' => 369,
            'multiple_pieces.resolved.index' => 370,

            'runner.index' => 386,
            'runner.list' => 386,
            'runner.add' => 386,
            'runner.add.submit' => 386,
            'runner.edit' => 386,
            'runner.edit.submit' => 386,

            'open_parcel_history.index' => 392,
            'open_parcel_history.list' => 392,
            'open_parcel_history.submit' => 392,
            'open_parcel_history.info' => 392,

            'international.tracking_upload.index' => 398,
            'international.tracking_upload.list' => 398,

            'international.shipment_status.index' => 508,
            'international.extra_service_charges.index' => 750,

            'telenor.arrival.index' => 421,
            'telenor.arrival.submit' => 421,
            'telenor.delivery.index' => 399,
            'telenor.delivery.submit' => 399,
            'telenor.return.index' => 400,
            'telenor.return.shipment_info' => 400,
            'telenor.return.submit' => 400,
            'telenor.call.index' => 402,
            'telenor.call.list' => 402,
            'telenor.return.bulk_return' => 525,
            'telenor.revert.bulk_revert' => 595,

            'carrefour.arrival.index' => 579,
            'carrefour.arrival.submit' => 579,
            'carrefour.delivery.index' => 606,
            'carrefour.delivery.submit' => 606,
            'carrefour.return.index' => 607,
            'carrefour.return.submit' => 607,


            'pam_leads.index' => 669,
            'pam_leads.list' => 669,
            'pam_leads.items' => 669,

            'leads.index' => 416,
            'leads.list' => 416,
            'leads.add_status' => 419,
//            'leads.tag_sale_person' => 420,
            'leads.lead_log' => 416,
            'leads.add_remarks' => 416,
            'leads.view_remarks' => 416,

            'human_resource.allusers' => 449,
            'human_resource.all_user_ajax' => 449,

            'human_resource.employee_directory.index' => 467,
            'human_resource.employee_directory.list' => 467,

            'human_resource.employee_directory.rejoin' => 652,

            'human_resource.employee_directory.edit' => 652,
            'human_resource.employee_directory.profile.update' => 652,
            'human_resource.employee_directory.medical.update' => 652,
            'human_resource.employee_directory.bank.update' => 652,
            'human_resource.employee_directory.reference.update' => 652,
            'human_resource.employee_directory.education.update' => 652,
            'human_resource.employee_directory.employment.update' => 652,
            'human_resource.employee_directory.attachments.update' => 652,
            'human_resource.employee_directory.rider.update' => 652,
            'human_resource.employee_directory.rider.deactivate' => 652,
            'human_resource.employee_directory.rider.activate' => 652,
            'human_resource.employee_directory.rider.incentive' => 652,
            'human_resource.employee_directory.rider.permanent' => 652,
            'human_resource.employee_directory.rider.blacklist' => 652,


            'human_resource.employee_directory.staff.deactivate' => 652,
            'human_resource.employee_directory.staff.activate' => 652,

            'human_resource.employee_directory.approve' => 652,
            'human_resource.employee_directory.reject' => 652,

            'human_resource.reporting_location.index' => 478,
            'human_resource.reporting_location.list' => 478,

            'human_resource.designation.hub.update' => 718,

            'human_resource.designation.index' => 481,
            'human_resource.designation.list' => 481,
            'human_resource.department.index' => 484,
            'human_resource.department.list' => 484,
            'human_resource.leave.index' => 613,
            'human_resource.leave.list' => 613,

            'human_resource.rider_incentive.index' => 492,
            'human_resource.rider_incentive.list' => 492,

            'human_resource.erf.index' => 506,
            'human_resource.erf.list' => 506,
            'human_resource.erf.add' => 522,


            'human_resource.fnf.index' => 568,
            'human_resource.fnf.list' => 568,
            'human_resource.fnf.add' => 569,
            'human_resource.fnf.rm.index' => 570,
            'human_resource.fnf.cs.index' => 571,
            'human_resource.fnf.administration.index' => 572,
            'human_resource.fnf.it_support.index' => 573,
            'human_resource.fnf.finance.index' => 574,
            'human_resource.fnf.hod_approval_index' => 575,
            'human_resource.fnf.hr.index' => 576,
            'human_resource.fnf.edit_fnf_request' => 577,
            'human_resource.fnf.fnf_history_index' => 578,
            'human_resource.fnf.reopen' => 713,

            'human_resource.employee_shifts.index' => 592,
            'human_resource.employee_shifts.list' => 592,

            'human_resource.adjustment.index' => 717,
            'human_resource.adjustment.list' => 717,

            'human_resource.fuel_allocation.index' => 831,
            'human_resource.fuel_allocation.list' => 831,

			'attendance.index' => 465,
            'attendance.list' => 465,
            'attendance.horizontal.index' => 465,
            'attendance.horizontal.table' => 465,
            'attendance.horizontal.list' => 465,
            'accounts.active.today' => 470,
            'accounts.active.today.ajax' => 470,

            'retail.pending_cash_collection.index' => 423,
            'retail.completed.index' => 424,
            'retail.accounts.index' => 428,
            'retail.franchise.index' => 431,
            'retail.franchise.list' => 431,
            'retail.trax_center.index' => 432,
            'retail.trax_center.list' => 432,
            'retail.users.index' => 474,
            'retail.users.list' => 474,


            'debriefing.supervisor.index' => 495,
            'debriefing.supervisor.list' => 495,
            'debriefing.agents_call_monitoring.index' => 496,
            'debriefing.agents_call_monitoring.list' => 496,
            'runner.intransit' => 501,
            'fleet.index' => 498,
            'route_management.index' => 499,
            'settings.escalation.launched.edit.index' => 507,
            'settings.escalation.in_process.edit.index' => 517,
			'reports.operation_service_level.index' => 524,
            'reports.operation_service_level.list' => 524,

            'reports.debriefing.agent_list' => 676,
            'reports.debriefing.agent_index' => 676,

			'finance.ftl_invoice.index' => 509,           
            'settings.debriefing_time_setting.index' => 526,
            'settings.debriefing_time_setting.update' => 526,

			'admin_otp.index' => 527,
            'admin_otp.list' => 527,

            'admin_otp.update' => 722,

            'rider_otp.index' => 563,
            'rider_otp.list' => 563,

            'rider_otp.update' => 721,

            'shipment_otp.index' => 815,
            'shipment_otp.list' => 815,

            'delivery.rider_request.index' => 35,
            'delivery.rider_request.list' => 35,
            'delivery.rider_request.approve' => 816,
            'delivery.rider_request.reject' => 817,
            'delivery.rider_request.update' => 818,
            'delivery.rider_request.update.list' => 818,
            'delivery.rider_request.update.remove' => 818,
            'delivery.rider_request.update.remove.bulk' => 818,
            'delivery.rider_request.add.shipments' => 818,

            'reports.work_code_master.index' => 532,
            'reports.work_code_master.list' => 532,
            
            'incidence_monitoring.index' => 535,
            'incidence_monitoring.list' => 535,
            'incidence_monitoring.add' => 536,
            'incidence_monitoring.view_report' => 539,

            'rider_delivery_note_otp.index' => 563,
            'rider_delivery_note_otp.list' => 563,

            'settings.last_mile_cron.index' => 565,

            'settings.dhl_sync_time.index' => 581,
            'settings.international_automation_user.index' => 582,

            'settings.ccd_booking.index' => 558,
            'accounts.auto_cancelation_days' => 660,
            'settings.omni.index' => 644,
            
            'return.rcp_agent.index' => 600,
            'return.rcp_agent.list' => 600,
            'sales.territory.territoryindex' =>630,
            'sales.designation.designationindex' =>631,
            'settings.sales.incentive.index' =>632,
            'reports.sales_incentive.index' =>633,
            'reports.sales_incentive.consolidated' =>634,

            'settings.auto_assigning.index' => 616,
            'settings.auto_assigning.list' => 616,

            'settings.reattempt_percentage.index' => 659,

            'settings.auto_tagging.index' => 639,
            'settings.auto_tagging.list' => 639,
            'reports.dws_report.index' => 642,
            'reports.osa_charges.index' => 647,
            'reports.revert.index' => 653,
            'qa_evaluation.index' => 648,
            'qa_evaluation.add' => 654,
            'qa_evaluation.edit' => 654,
            'qa_evaluation.view' => 655,
            'qa_evaluation.edit_activities' => 656,

            'qa.high_alert.shippers.index' => 692,

            'settings.lead_tagging.index' => 661,
            'settings.lead_tagging.list' => 661,
            'settings.lead_zones.index' => 664,
            'settings.lead_zones.list' => 664,
            'settings.lead_notification.index' => 671,
            'settings.lead_notification.list' => 671,
            
            'reports.crm_special_approval.index' => 688,
            'reports.crm_special_approval.list' => 688,
            'settings.cn_print_right.cn_print_right' =>699,

            
            'settings.auto_tag_territories.index' => 697,
            'settings.auto_tag_territories.list' => 697,

            'settings.referral.index' => 701,
            'settings.referral.list' => 701,

            'settings.lost_shipment_shippers.index' => 708,
            'settings.lost_shipment_admins.index' => 710,

            'settings.invoice_against_return_delivered_shipper.index' => 716,
			'settings.undelivered_sms_hub_wise.index' => 714,
			'settings.delivery_area_keyword.index' => 747,
            'nps.index' => 751,
            'nps.add' => 752,
            'nps.response.report' => 755,
            'nps.consolidate.report' => 756,

			'accounts.disable.account.intimation.survey.index' => 762,
			'accounts.disable.account.intimation.survey.report' => 768,
			'settings.pickup.weight_bypass' => 761,			'delivery.note.rider_category_bypass_request' => 757,
            'delivery.note.rider_category_bypass_weight' =>758,
            
            'vigilance.verification.index' => 770,
            'vigilance.verification.history.index' => 771,

            'vigilance.note.index' => 770,
            'vigilance.note.history.index' => 771,

            'settings.booking_destination_keyword.index' => 772,
            'settings.booking_destination_keyword.add' => 773,
            'qa.cx_training.index' =>777,
            'qa.cx_training.list' =>777,

            'return.confirmation_pending_manual_sms' =>781,
            
            'reports.employee_confirmation.index' => 786,
            'reports.employee_confirmation.list' => 786,

            'settings.complain_portal_shippers.index' => 788,

            'user_management.roles.permissions.index' => 789,
            'crm.bulk_claim.index' => 795,

            'international.wholesale.accounts.index' => 799,

            'international.wholesale.excel.index' => 804,
            'international.wholesale.invoices.index' => 807,

            'settings.non_cod_otp_shippers.index' => 820,
            'settings.non_cod_otp_shippers.store' => 820,

            'tracking.shipment_position.track' => 822,
            'tracking.shipment_position.upload' => 822,
            'tracking.shipment_position.list' => 822,

            'human_resource.employee_penalty.index' => 829,

            'reports.one_link_charges_summary.index' => 824,
			'reports.one_link_charges_summary.index' => 824,

            'settings.onelink_payment_charges.index' => 825,

			'reports.rider_pickup.index' => 823,
            'reports.rider_pickup.list' => 823,

			'settings.consignee_refused_otp_bypass.index' => 826,
            'otp_history.index' => 827,
        ],

        'shipper' => [
            'shipment.book.index' => 1,
            'shipment.book.store' => 1,
            'shipment.book.corporate.index' => 1,
            'shipment.book.corporate.store' => 1,
            'shipment.book.order_id' => 1,
            'shipment.book.excel_index' => 1,
            'shipment.book.excel_store' => 1,
            'shipment.book.errors' => 1,
            'shipment.book.corporate_excel_index' => 1,
            'shipment.book.corporate_excel_store' => 1,

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

            'return.pending.index' => 9,
            'return.pending.list' => 9,
            'return.pending.marked.status' => 9,
            'return.pending.marked.status.single' => 9,
            'return.reattempt_history.index' => 9,
            'return.reattempt_history.list' => 9,
            'return.sheet.pending.index' => 9,
            'return.sheet.pending.list' => 9,
            'return.sheet.receive.index' => 9,
            'return.sheet.receive.shipment_info' => 9,
            'return.sheet.receive.submit' => 9,
            'return.sheet.history.index' => 9,
            'return.sheet.history.list' => 9,

            'intercept.index' => 9,

            'packaging.requests.index' => 4,
            'packaging.requests.submit' => 4,

            'finance.payments.index' => 5,
            'finance.payments.list' => 5,
            'finance.payments.delivered_shipments' => 5,
            'finance.payments.returned_shipments' => 5,
            'finance.payments.adjusted_shipments' => 5,
            'finance.payments.details_print' => 5,
            'finance.payments.export_to_excel' => 5,
            'finance.payments.reconcile_through_receiving_sheet.index' => 5,
            'finance.payments.reconcile_through_receiving_sheet.list' => 5,
            'finance.invoice.index' => 14,
            'finance.invoice.list' => 14,

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
            
            'dispute.shipments.index' => 702,

            'reports.qsr.index' => 8,
            'reports.qsr.list' => 8,
            'reports.sales.index' => 8,
            'reports.sales.list' => 8,

            'crm.request.index' => 10,

            'settings.air_waybill_printing.index' => 11,
            'settings.air_waybill_printing.store' => 11,
            
            'reports.daraz_mis.index' => 15,
            'reports.daraz_mis.list' => 15,

            'reports.weight_reconciliation.index' => 8,
            'reports.weight_reconciliation.list' => 8,

            'crm.bulk_claim.index' => 19,

            
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
            if(session('department_id') == 7){
                if(!Session::has('sale_users_bypass')){
                    $sale_users_bypass = array();
                    $settings = GlobalSettings::where('type', 'sales_user_restriction_bypass');

                    if ($settings->exists()) {
                        $settings = $settings->first();
                        $sale_users_bypass = array_map('intval', explode(',', $settings->text));
                        session(['sale_users_bypass' => $sale_users_bypass]);

                    }
                    else{
                        session(['sale_users_bypass' => []]);
                    }
                }

            }
            else{
                session(['sale_users_bypass' => []]);
            }


            if (session('role_id') == 1 || !isset($this->actions['admin'][$action]) || in_array($this->actions['admin'][$action], session('permissions')) || (substr($action, 0, 4) == 'crm.' && session('role_id') == 6)) {
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
