<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\PettyCashStatement;
use App\Http\Models\Admin\PettyCashStatementDraft;
use App\Http\Models\Admin\PettyCashStatementDetailDraft;

class Seeder4288PettyCashPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 641, 'name' => 'Edit Petty Cash Statement - Edit Option For Finance', 'module_id' => 8),
            array('id' => 645, 'name' => 'Approved Petty Cash Statement - View', 'module_id' => 8),
        ));

        $hub_id_null = PettyCashStatement::where('hub_id',null)->get();

        foreach ($hub_id_null as $hub)
        {
            $detail = $hub->petty_cash_statement_details->first();
            $hub->hub_id = $detail->hub_id ?? null;
            $hub->zone_id = $detail->zone_id ?? null;
            $hub->station_manager_id = $detail->operation_manager_id ?? null;
            $hub->date = $hub->created_at;
            $hub->update();
        }


        DB::table('petty_cash_statement_drafts')->delete();
        DB::table('petty_cash_statement_detail_drafts')->delete();

    }
}
