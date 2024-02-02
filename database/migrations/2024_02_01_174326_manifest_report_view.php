<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManifestReportView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $manifest_report = "CREATE Or Replace VIEW manifest_report AS select `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`,(case when (`sj`.`shipper_status_id` = 2) then 1 else 0 end) AS `arrival_count`,'Arrival' AS `Arrival`,NULL AS `manifest_code`,'manifest' AS `manifest`,NULL AS `mr_code`,'Misroute' AS `Misroute`,NULL AS `wmcode`,'Without_manifest' AS `Without_manifest` from ((((((((`shipments` `sh` join `users` `u`) join `segments` `p`) join `sub_category_segments` `sp`) join `cities` `c`) join `zones` `z`) join `cities` `oc`) join `zones` `oz`) join `shipments_journey` `sj`) where ((`sh`.`id` = `sj`.`shipment_id`) and (`sj`.`shipper_status_id` = '2') and (`sh`.`user_id` = `u`.`id`) and (`p`.`id` = `sp`.`segment_id`) and (`u`.`segment_id` = `p`.`id`) and (`u`.`sub_segment_id` = `sp`.`id`) and (`c`.`zone_id` = `z`.`id`) and (`oc`.`zone_id` = `oz`.`id`) and (`sh`.`consignee_city_id` = `c`.`id`) and (`u`.`city_id` = `oc`.`id`)) union select `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`,NULL AS `NULL`,NULL AS `arrival`,(case when (`sj`.`shipper_status_id` = 3) then 1 else 0 end) AS `manifs_cont`,'manifest' AS `manifest`,NULL AS `Misroute_code`,'Misroute' AS `Misroute`,NULL AS `wm_code`,'Without_manifest' AS `Without_manifest` from ((((((((`shipments` `sh` join `users` `u`) join `segments` `p`) join `sub_category_segments` `sp`) join `cities` `c`) join `zones` `z`) join `cities` `oc`) join `zones` `oz`) join `shipments_journey` `sj`) where ((`sh`.`id` = `sj`.`shipment_id`) and (`sj`.`shipper_status_id` = '3') and (`sh`.`user_id` = `u`.`id`) and (`p`.`id` = `sp`.`segment_id`) and (`u`.`segment_id` = `p`.`id`) and (`u`.`sub_segment_id` = `sp`.`id`) and (`c`.`zone_id` = `z`.`id`) and (`oc`.`zone_id` = `oz`.`id`) and (`sh`.`consignee_city_id` = `c`.`id`) and (`u`.`city_id` = `oc`.`id`)) union select `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`,NULL AS `NULL`,NULL AS `arrival`,NULL AS `NULL`,NULL AS `NULL`,(case when (`sj`.`shipper_status_id` = 11) then 1 else 0 end) AS `mr_cont`,'Misroute' AS `Misroute`,NULL AS `wm_code`,'Without_manifest' AS `Without_manifest` from ((((((((`shipments` `sh` join `users` `u`) join `segments` `p`) join `sub_category_segments` `sp`) join `cities` `c`) join `zones` `z`) join `cities` `oc`) join `zones` `oz`) join `shipments_journey` `sj`) where ((`sh`.`id` = `sj`.`shipment_id`) and (`sj`.`shipper_status_id` = '11') and (`sh`.`user_id` = `u`.`id`) and (`p`.`id` = `sp`.`segment_id`) and (`u`.`segment_id` = `p`.`id`) and (`u`.`sub_segment_id` = `sp`.`id`) and (`c`.`zone_id` = `z`.`id`) and (`oc`.`zone_id` = `oz`.`id`) and (`sh`.`consignee_city_id` = `c`.`id`) and (`u`.`city_id` = `oc`.`id`)) union select `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`,NULL AS `NULL`,NULL AS `arrival`,NULL AS `NULL`,NULL AS `NULL`,NULL AS `NULL`,NULL AS `NULL`,(case when (`sj`.`shipper_status_id` = 67) then 1 else 0 end) AS `WM_cont`,'Without_manifest' AS `Without_manifest` from ((((((((`shipments` `sh` join `users` `u`) join `segments` `p`) join `sub_category_segments` `sp`) join `cities` `c`) join `zones` `z`) join `cities` `oc`) join `zones` `oz`) join `shipments_journey` `sj`) where ((`sh`.`id` = `sj`.`shipment_id`) and (`sj`.`shipper_status_id` = '11') and (`sh`.`user_id` = `u`.`id`) and (`p`.`id` = `sp`.`segment_id`) and (`u`.`segment_id` = `p`.`id`) and (`u`.`sub_segment_id` = `sp`.`id`) and (`c`.`zone_id` = `z`.`id`) and (`oc`.`zone_id` = `oz`.`id`) and (`sh`.`consignee_city_id` = `c`.`id`) and (`u`.`city_id` = `oc`.`id`))";

        $manifest_report2 = "CREATE or replace VIEW manifest_report2 as
 select cast(`k`.`created_at` as date) AS `booking_date`,`k`.`origin_zonecode` AS `origin_zonecode`,`k`.`destination_zonecode` AS `destination_zonecode`,count(`k`.`id`) AS `COUNT(id)`,`k`.`parent_prod_name` AS `parent_prod_name`,`k`.`sub_prod_name` AS `sub_prod_name`,sum(`k`.`arrival_count`) AS `arrival`,sum(`k`.`manifest_code`) AS `manifest`,sum(`k`.`mr_code`) AS `misroute`,sum(`k`.`wmcode`) AS `withoutmanifest` from `manifest_report` `k` group by cast(`k`.`created_at` as date),`k`.`origin_zonecode`,`k`.`destination_zonecode`,`k`.`parent_prod_name`,`k`.`sub_prod_name`";

        DB::statement($manifest_report);
        DB::statement($manifest_report2);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS manifest_report;");
        DB::statement("DROP VIEW IF EXISTS manifest_report2;");
    }
}
