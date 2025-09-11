<?php

namespace App\Console\Commands;

use App\Http\Controllers\APIController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RebookDuplicateShipments extends Command
{
    protected $signature = 'shipments:rebook-via-controller {user_id : The user id}';

    protected $description = 'Fetch dup shipment payloads via SP, call shipment_book() controller, then delete old shipments on success';

    public function handle(): int
    {
        $userId     = (int) $this->argument('user_id');

        $apicontroller = new  APIController();
        $controllerInstance = $apicontroller;

        // 1) get payload-ready rows from your SP (no INSERT text, just data)
        $rows = DB::select('CALL sp_preview_user_duplicate_shipments_inserts(?)', [$userId]);
        dd($rows);
        // handle “info” row when no dupes
        if (empty($rows) || (count($rows) === 1 && isset($rows[0]->info))) {
            $this->info('No duplicate shipments payload for user: '.$userId);
            return self::SUCCESS;
        }

        $processed = 0; $deleted = 0; $failed = 0;

        foreach ($rows as $row) {
            // 2) build the payload array from the SP row, and MERGE user_id
            $payload = $this->rowToPayloadArray($row);
            $payload['user_id'] = $userId;
            $payload['app_type'] = 1;

//            try {
                // create a Request and merge payload (as you asked)
                $request = Request::create('', 'POST');
                $request->merge($payload);

                // call your controller method directly
                /** @var \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse $resp */
                $resp = app()->call([$controllerInstance, 'shipment_book'], ['request' => $request]);
                $statusCode = method_exists($resp, 'getStatusCode') ? $resp->getStatusCode() : 200;
                $data       = $this->normalizeResponse($resp);

                if ($this->isSuccess($statusCode, $data)) {
                    // 3) delete the old shipment id (from SP row, not by parsing text)
                    $oldId = (int) ($row->old_shipment_id ?? 0);
                    if ($oldId > 0) {
                        $del = DB::table('shipments')
                            ->where('user_id', $userId)
                            ->where('id', $oldId)
                            ->delete();
                        $deleted += $del;
                    }
                    $processed++;
                    $this->info("Booked OK; deleted old id {$oldId}");
                } else {
                    $failed++;
                    $this->error('booking failed | HTTP '.$statusCode.' | '.json_encode($data));
                }
                dd('here');
//            } catch (\Throwable $e) {
//                $failed++;
//                $this->error('Exception: '.$e->getMessage());
//                Log::error('shipment_book exception', ['user_id'=>$userId, 'e'=>$e]);
//            }
        }

        $this->info("Done. processed={$processed}, deleted_old={$deleted}, failed={$failed}");
        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Map one SP row to the exact form keys your controller expects.
     * (Keeps only non-null fields; add/remove keys as needed.)
     */
    protected function rowToPayloadArray(object $r): array
    {
        $arr = [
            // booking form fields returned by your SP
            'service_type_id'          => $r->service_type_id ?? null,
            'pickup_address_id'        => $r->pickup_address_id ?? null,
            'consignee_city_id'        => $r->consignee_city_id ?? null,
            'consignee_name'           => $r->consignee_name ?? null,
            'consignee_address'        => $r->consignee_address ?? null,
            'consignee_phone_number_1' => $r->consignee_phone_number_1 ?? null,
            'estimated_weight'         => $r->estimated_weight ?? null,
            'shipping_mode_id'         => $r->shipping_mode_id ?? null,
            'information_display'      => $r->information_display ?? null,
            'amount'                   => $r->amount ?? null,
            'payment_mode_id'          => $r->payment_mode_id ?? null,

            'item_product_type_id'     => $r->item_product_type_id ?? null,
            'item_description'         => $r->item_description ?? null,
            'item_quantity'            => $r->item_quantity ?? null,
            'item_insurance'           => $r->item_insurance ?? null,

            'return_address'           => $r->return_address ?? null,
            'return_contact_person'    => $r->return_contact_person ?? null,
            'return_vendor'            => $r->return_vendor ?? null,
            'return_phone_number'      => $r->return_phone_number ?? null,
            'return_email_address'     => $r->return_email_address ?? null,
            'return_city'              => $r->return_city ?? null,
            'return_address_id'        => $r->return_address_id ?? null,

            // include the composed note:
            // "old id : <id>\nold tracking : <tracking>"
            'special_instructions'     => $r->special_instructions ?? null,
        ];

        // prune nulls (keep 0/false)
        return array_filter($arr, fn($v) => !is_null($v));
    }

    protected function normalizeResponse($resp): array
    {
        if (is_array($resp)) return $resp;
        if (method_exists($resp, 'getContent')) {
            $json = json_decode($resp->getContent(), true);
            return is_array($json) ? $json : ['_raw' => (string) $resp->getContent()];
        }
        if (method_exists($resp, 'getData')) {
            $d = $resp->getData(true);
            return is_array($d) ? $d : (array) $d;
        }
        return ['_raw' => (string) $resp];
    }

    /**
     * Your controller returns errors with 'status' = 1.
     * We’ll consider success when 'status' is 0 / 'success' / true,
     * or any 2xx with no explicit error flag.
     */
    protected function isSuccess(int $http, array $data): bool
    {
        if ($http < 200 || $http >= 300) return false;
        if (isset($data['status'])) {
            // known error shape: status=1 => error
            if ($data['status'] === 1 || $data['status'] === '1') return false;
            if ($data['status'] === 0 || $data['status'] === '0' || $data['status'] === 'success' || $data['status'] === true) return true;
        }
        // fallback: 2xx is OK unless explicit error message is present
        return true;
    }
}
