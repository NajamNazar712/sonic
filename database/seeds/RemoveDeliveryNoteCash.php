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
        $delivery_note_ids = array(1119712, 1209741, 1205532, 1156405, 1209850, 1206399, 1202444, 1129121, 1210959, 1212116, 1204811, 1191492, 1207301, 1192829, 1217214, 1222880, 1221016, 1226118, 1224827, 1221032, 1222169, 1224952, 1213761, 1214701, 1225021, 1222670, 1219718);

        foreach ($delivery_note_ids as $note_id) {
            $note_details = DeliveryNote::where('id', $note_id)->where('cash_collection_status', 1)->first();
            if ($note_details) {
                $note_details->cash_collection_status = 0;
                $note_details->cash_collected_by = Null;
                $note_details->cash_collected_at = Null;
                $note_details->save();
            }
        }
    }
}
