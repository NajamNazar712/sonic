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
        $manifest_report = "create or replace view manifest_report as SELECT `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`,(CASE WHEN (`sj`.`shipper_status_id` = 2) THEN 1 ELSE 0 END) AS `arrival_count`,'Arrival' AS `Arrival`, NULL AS `manifest_code`,'manifest' AS `manifest`, NULL AS `mr_code`,'Misroute' AS `Misroute`, NULL AS `wmcode`,'Without_manifest' AS `Without_manifest`
FROM ((((((((`shipments` `sh`
JOIN `users` `u`)
JOIN `segments` `p`)
JOIN `sub_category_segments` `sp`)
JOIN `cities` `c`)
JOIN `zones` `z`)
JOIN `cities` `oc`)
JOIN `zones` `oz`)
JOIN `shipments_journey` `sj`)
WHERE ((`sh`.`id` = `sj`.`shipment_id`) AND (`sj`.`shipper_status_id` = '2') AND (`sh`.`user_id` = `u`.`id`) AND (`p`.`id` = `sp`.`segment_id`) AND (`u`.`segment_id` = `p`.`id`) AND (`u`.`sub_segment_id` = `sp`.`id`) AND (`c`.`zone_id` = `z`.`id`) AND (`oc`.`zone_id` = `oz`.`id`) AND (`sh`.`consignee_city_id` = `c`.`id`) AND (`u`.`city_id` = `oc`.`id`)) UNION
SELECT `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`, NULL AS `NULL`, NULL AS `arrival`,(CASE WHEN (`sj`.`shipper_status_id` = 3) THEN 1 ELSE 0 END) AS `manifs_cont`,'manifest' AS `manifest`, NULL AS `Misroute_code`,'Misroute' AS `Misroute`, NULL AS `wm_code`,'Without_manifest' AS `Without_manifest`
FROM ((((((((`shipments` `sh`
JOIN `users` `u`)
JOIN `segments` `p`)
JOIN `sub_category_segments` `sp`)
JOIN `cities` `c`)
JOIN `zones` `z`)
JOIN `cities` `oc`)
JOIN `zones` `oz`)
JOIN `shipments_journey` `sj`)
WHERE ((`sh`.`id` = `sj`.`shipment_id`) AND (`sj`.`shipper_status_id` = '3') AND (`sh`.`user_id` = `u`.`id`) AND (`p`.`id` = `sp`.`segment_id`) AND (`u`.`segment_id` = `p`.`id`) AND (`u`.`sub_segment_id` = `sp`.`id`) AND (`c`.`zone_id` = `z`.`id`) AND (`oc`.`zone_id` = `oz`.`id`) AND (`sh`.`consignee_city_id` = `c`.`id`) AND (`u`.`city_id` = `oc`.`id`)) UNION
SELECT `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`, NULL AS `NULL`, NULL AS `arrival`, NULL AS `NULL`, NULL AS `NULL`,(CASE WHEN (`sj`.`shipper_status_id` in (66,11)) THEN 1 ELSE 0 END) AS `mr_cont`,'Misroute' AS `Misroute`, NULL AS `wm_code`,'Without_manifest' AS `Without_manifest`
FROM ((((((((`shipments` `sh`
JOIN `users` `u`)
JOIN `segments` `p`)
JOIN `sub_category_segments` `sp`)
JOIN `cities` `c`)
JOIN `zones` `z`)
JOIN `cities` `oc`)
JOIN `zones` `oz`)
JOIN `shipments_journey` `sj`)
WHERE ((`sh`.`id` = `sj`.`shipment_id`) AND (`sj`.`shipper_status_id` in ('66','11')) AND (`sh`.`user_id` = `u`.`id`) AND (`p`.`id` = `sp`.`segment_id`) AND (`u`.`segment_id` = `p`.`id`) AND (`u`.`sub_segment_id` = `sp`.`id`) AND (`c`.`zone_id` = `z`.`id`) AND (`oc`.`zone_id` = `oz`.`id`) AND (`sh`.`consignee_city_id` = `c`.`id`) AND (`u`.`city_id` = `oc`.`id`)) UNION
SELECT `sh`.`created_at` AS `created_at`,`sh`.`id` AS `id`,`u`.`city_id` AS `origin`,`oc`.`name` AS `origin_name`,`oz`.`name` AS `origin_zonecode`,`sh`.`consignee_city_id` AS `shipment_destination`,`c`.`name` AS `destination_name`,`z`.`name` AS `destination_zonecode`,`p`.`name` AS `parent_prod_name`,`sp`.`name` AS `sub_prod_name`, NULL AS `NULL`, NULL AS `arrival`, NULL AS `NULL`, NULL AS `NULL`, NULL AS `NULL`, NULL AS `NULL`,(CASE WHEN (`sj`.`shipper_status_id` = 67) THEN 1 ELSE 0 END) AS `WM_cont`,'Without_manifest' AS `Without_manifest`
FROM ((((((((`shipments` `sh`
JOIN `users` `u`)
JOIN `segments` `p`)
JOIN `sub_category_segments` `sp`)
JOIN `cities` `c`)
JOIN `zones` `z`)
JOIN `cities` `oc`)
JOIN `zones` `oz`)
JOIN `shipments_journey` `sj`)
WHERE ((`sh`.`id` = `sj`.`shipment_id`) AND (`sj`.`shipper_status_id` = '67') AND (`sh`.`user_id` = `u`.`id`) AND (`p`.`id` = `sp`.`segment_id`) AND (`u`.`segment_id` = `p`.`id`) AND (`u`.`sub_segment_id` = `sp`.`id`) AND (`c`.`zone_id` = `z`.`id`) AND (`oc`.`zone_id` = `oz`.`id`) AND (`sh`.`consignee_city_id` = `c`.`id`) AND (`u`.`city_id` = `oc`.`id`))";

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
