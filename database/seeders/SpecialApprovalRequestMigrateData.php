<?php

use Illuminate\Database\Seeder;
use App\SpecialApprovalRequest;
use App\SpecialApprovalRequestAdmin;

class SpecialApprovalRequestMigrateData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $special_approval_request_data = SpecialApprovalRequest::all();

        foreach ($special_approval_request_data as $key => $value) {
            # code...
            $special_approval_request_admin = new SpecialApprovalRequestAdmin();
            $special_approval_request_admin->special_request_id = $value->id;
            $special_approval_request_admin->admin_id = $value->admin_id;
            $special_approval_request_admin->save();
        }
    }
}
