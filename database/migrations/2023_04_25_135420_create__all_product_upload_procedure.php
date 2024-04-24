<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedure = "DROP PROCEDURE IF EXISTS AllProductUpload;
            CREATE PROCEDURE AllProductUpload(IN P_vendor_id BIGINT UNSIGNED)
            BEGIN

            insert into products (id, stone_id, cert_no, cert_type, cert_url, image, video, diamond_type,
                size_id, carat, shape_id, color_id, colors_id, overtone_id, intensity_id, clarity_id, cut_id, polish_id,
                symmetry_id, fluorescence_id, rapo_rate, rapo_amount, discount, rate, amount, `table`, table_per, depth, depth_per, measurement, length, width, height, ratio,
                bgm_id, city, country, created_at, updated_at,
                milky, shade, crown_angle, crown_height, crown_open, pavilion_angle, pavilion_height, pavilion_open, white_table, white_side, table_black,
                side_black, table_open, girdle, girdle_desc, culet, key_to_symbols, fluorescence_color_id, pair, h_a, eye_clean, growth_type, vendor_id
            )

            select
            UUID(), tp.stone_id, tp.cert_no,
            case when tp.cert_type='IGI' then 1 when tp.cert_type='GIA' then 2 when tp.cert_type='HRD' then 3 when tp.cert_type='Delight Grading' then 4 else 0 end cert_type,
            tp.cert_url, tp.image, tp.video,
            tp.diamond_type,
            (select s.id from sizes s where s.name=tp.size_id and s.deleted_at is null limit 1) as size_id,
            tp.carat,
            ifnull((select sh.id from shapes sh where sh.name=tp.shape_id and sh.deleted_at is null limit 1), (select sh.id from shapes sh where sh.name='other' and sh.deleted_at is null limit 1)) as shape_id,
            (select c.id from colors c where c.name=tp.color_id and c.deleted_at is null limit 1) as color_id,
            (select fc.id from fancy_colors fc where fc.name=tp.colors_id and fc.deleted_at is null and fc.type=0 limit 1) as colors_id,
            (select fo.id from fancy_colors fo where fo.name=tp.overtone_id and fo.deleted_at is null and fo.type=1 limit 1) as overtone_id,
            (select fi.id from fancy_colors fi where fi.name=tp.intensity_id and fi.deleted_at is null and fi.type=2 limit 1) as intensity_id,
            (select cl.id from clarities cl where cl.name=tp.clarity_id and cl.deleted_at is null limit 1) as clarity_id,
            (select cu.id from finishes cu where cu.name=tp.cut_id and cu.deleted_at is null and cu.type=0 limit 1) as cut_id,
            (select po.id from finishes po where po.name=tp.polish_id and po.deleted_at is null and po.type=1 limit 1) as polish_id,
            (select sy.id from finishes sy where sy.name=tp.symmetry_id and sy.deleted_at is null and sy.type=2 limit 1) as symmetry_id,
            (select fl.id from fluorescences fl where fl.name=tp.fluorescence_id and fl.deleted_at is null AND fl.type=0 limit 1) as fluorescence_id,

            tp.rapo_rate, (tp.carat * tp.rapo_rate) as rapo_amount, tp.discount, (tp.rapo_rate + tp.rapo_rate * tp.discount / 100) as rate, (tp.carat * (tp.rapo_rate + tp.rapo_rate * tp.discount / 100)) as amount, tp.table, tp.table_per, tp.depth, tp.depth_per, tp.measurement, tp.length, tp.width, tp.height, tp.ratio,
            (select b.id from fluorescences b where b.name=tp.bgm_id and b.deleted_at is null AND b.type=2 limit 1) as bgm_id,
            tp.city, tp.country, now(), now(),
            tp.milky, tp.shade, tp.crown_angle, tp.crown_height, tp.crown_open, tp.pavilion_angle, tp.pavilion_height, tp.pavilion_open, tp.white_table, tp.white_side, tp.table_black,
            tp.side_black, tp.table_open, tp.girdle, tp.girdle_desc, tp.culet, tp.key_to_symbols, tp.fluorescence_color_id, tp.pair, tp.h_a, tp.eye_clean, tp.growth_type, tp.vendor_id
            from
            temp_products tp
            left join products p1 ON tp.stone_id=p1.stone_id
            left join products p2 ON tp.stone_id=p2.cert_no
            where tp.vendor_id=P_vendor_id and p1.stone_id is null and p2.cert_no is null and tp.imported_at is null and tp.import_type=1
            order by tp.id;

            update temp_products
            set imported_at=now()
            where vendor_id=P_vendor_id AND imported_at is null and import_type=1;
            END;";

            DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('_all_product_upload_procedure');
    }
};
