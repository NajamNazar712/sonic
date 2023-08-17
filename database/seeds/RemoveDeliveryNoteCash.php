<?php

use App\Http\Models\Admin\DeliveryNote;
use Carbon\Carbon;
use Illuminate\Database\Seeder;


class RemoveDeliveryNoteCash extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $delivery_note_ids = array(1595358, 1595205, 1594718, 1595236, 1595132, 1594447, 1594657, 1595061, 1593075, 1595073, 1594739, 1594064, 1595134, 1594887, 1594450, 1594660, 1593636, 1595098, 1594803, 1594150, 1595017, 1594451, 1594661, 1593637, 1595104, 1594806, 1594305, 1594468, 1593867, 1595123, 1594446, 1594810, 1594625, 1593876, 1512941, 1513345, 1513706);

        foreach ($delivery_note_ids as $note_id) {
            $note_details = DeliveryNote::where('id', $note_id)->where('cash_collection_status', 1)->first();
            if ($note_details) {
                $note_details->cash_collection_status = 0;
                $note_details->cash_collected_by = NULL;
                $note_details->cash_collected_at = NULL;
                $note_details->save();
            }
        }
    }
}
