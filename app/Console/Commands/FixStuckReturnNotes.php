<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;

class FixStuckReturnNotes extends Command
{
    protected $signature   = 'return_notes:fix_stuck';
    protected $description = 'Auto-fix return notes stuck open because a shipment was re-added to a newer note (double-submit / quick-receive-bag bug). Runs daily at 07:00.';

    public function handle()
    {
        // ── FIND STUCK SHIPMENTS ────────────────────────────────────────────────
        // A shipment is "stuck" when ALL of the following are true:
        //   1. It is unconfirmed (rns_old.status = 0) inside an open return note (rn_old.status = 0).
        //   2. The exact same shipment also exists in a NEWER return note
        //      (rns_new.return_note_id > rns_old.return_note_id).
        //   3. That newer note's entry is already confirmed (rns_new.status = 1).
        //      Safety: if the newer entry is also status=0 (also stuck there), we skip it —
        //      we only clean up when the shipment has been properly received somewhere else.
        // Scope: return notes created this calendar year only.
        $stuckRows = DB::select("
            SELECT
                rns_old.shipment_id,
                rn_old.id AS stuck_return_note_id
            FROM return_note_shipments rns_old
            INNER JOIN return_notes rn_old
                ON  rn_old.id     = rns_old.return_note_id
                AND rn_old.status = 0
            INNER JOIN return_note_shipments rns_new
                ON  rns_new.shipment_id    = rns_old.shipment_id
                AND rns_new.return_note_id > rns_old.return_note_id
                AND rns_new.status         = 1
            WHERE rns_old.status = 0
              AND rn_old.created_at >= :year_start
            ORDER BY rn_old.id, rns_old.shipment_id
        ", ['year_start' => now()->startOfYear()->toDateString()]);

        if (empty($stuckRows)) {
            $this->info('[FixStuckReturnNotes] No stuck shipments found. Nothing to do.');
            Log::info('[FixStuckReturnNotes] No stuck shipments found.');
            return;
        }

        // Group shipments by the note they are stuck in so we handle each note atomically.
        $grouped = [];
        foreach ($stuckRows as $row) {
            $grouped[$row->stuck_return_note_id][] = $row->shipment_id;
        }

        $totalDeleted = 0;
        $notesClosed  = 0;

        foreach ($grouped as $noteId => $shipmentIds) {
            DB::beginTransaction();
            try {
                foreach ($shipmentIds as $shipmentId) {
                    // ── SAFETY RE-CHECK ─────────────────────────────────────────
                    // Before deleting, confirm the entry is still status=0 in this note.
                    // Protects against the rare case where another process confirmed it
                    // between the query above and this transaction.
                    $stillStuck = ReturnNoteShipment::where('return_note_id', $noteId)
                        ->where('shipment_id', $shipmentId)
                        ->where('status', 0)
                        ->exists();

                    if (!$stillStuck) {
                        Log::info("[FixStuckReturnNotes] Shipment {$shipmentId} in note {$noteId} already resolved — skipping.");
                        continue;
                    }

                    // Remove the duplicate / stuck entry from the old note.
                    DB::table('return_note_shipments')
                        ->where('return_note_id', $noteId)
                        ->where('shipment_id', $shipmentId)
                        ->where('status', 0)
                        ->delete();

                    // Keep shipments_count accurate.
                    DB::table('return_notes')
                        ->where('id', $noteId)
                        ->decrement('shipments_count');

                    Log::info("[FixStuckReturnNotes] Removed stuck shipment {$shipmentId} from return note {$noteId}.");
                    $totalDeleted++;
                }

                // ── AUTO-CLOSE CHECK ─────────────────────────────────────────────
                // After removing the stuck entries, if no unconfirmed shipments remain
                // in this note, close it automatically so the rider's note list clears.
                $remainingOpen = ReturnNoteShipment::where('return_note_id', $noteId)
                    ->where('status', 0)
                    ->count();

                if ($remainingOpen === 0) {
                    ReturnNote::where('id', $noteId)->update(['status' => 1]);
                    Log::info("[FixStuckReturnNotes] Return note {$noteId} auto-closed — no unconfirmed shipments remain.");
                    $notesClosed++;
                } else {
                    Log::info("[FixStuckReturnNotes] Return note {$noteId} still has {$remainingOpen} unconfirmed shipment(s) — left open.");
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("[FixStuckReturnNotes] Transaction failed for note {$noteId}: " . $e->getMessage());
                $this->error("Error on note {$noteId}: " . $e->getMessage());
            }
        }

        $summary = "Removed {$totalDeleted} stuck shipment(s). Auto-closed {$notesClosed} return note(s).";
        $this->info("[FixStuckReturnNotes] Done. {$summary}");
        Log::info("[FixStuckReturnNotes] Completed. {$summary}");
    }
}
