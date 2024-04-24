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
        $procedure = "DROP PROCEDURE IF EXISTS AllProductImportErrorN;
            CREATE PROCEDURE AllProductImportErrorN(IN P_vendor_id BIGINT UNSIGNED, IN v_type INT)
            BEGIN
            DROP TABLE IF EXISTS tempdata;
            DROP TABLE IF EXISTS withouterror;
            CREATE TEMPORARY TABLE tempdata
            SELECT
                tp.id, tp.uuid, tp.vendor_id, tp.stone_id, Case When p1.stone_id Is NOT Null Then 'This Stone ID is already available' else '' end as stone_id_error,
                tp.cert_no, Case When p1.cert_no Is NOT Null Then 'This Certificate No is already available' else '' end as cert_no_error,
                tp.cert_type, Case When tp.cert_type='IGI' ||  tp.cert_type='GIA' ||  tp.cert_type='HRD' || tp.cert_type='AGS' ||  tp.cert_type='Delight Grading' then '' else 'Certificate type is wrong' end as cert_type_error,
                tp.cert_url, Case When tp.cert_url Is Null Then 'Certificate URL cannot be null' else '' end as cert_url_error,
                tp.carat, Case When tp.carat Is Null Then 'carat cannot be null' else '' end as carat_error,
                tp.shape_id, ifnull((SELECT Case When sh.name Is Null Then 'Shape does not exist' else '' end from shapes sh where sh.name=tp.shape_id and sh.deleted_at is null and sh.status=1 group by sh.name),'') as shape_id_error,
                tp.color_id,
                case when tp.color_id Is Null || tp.color_id='' then
                    case when tp.colors_id is null then 'Color cannot be null' else '' end
                else
                    Case
                    When tp.color_id='D' || tp.color_id='E' || tp.color_id='F' || tp.color_id='G' || tp.color_id='H' || tp.color_id='I' || tp.color_id='J' || tp.color_id='K' || tp.color_id='L' || tp.color_id='M' || tp.color_id='N' || tp.color_id='O-P' || tp.color_id='Q-R' || tp.color_id='S-T' || tp.color_id='U-V' || tp.color_id='W-X' || tp.color_id='Y-Z' then ''
                    else 'Color does not exist' end
                end as color_id_error,
                tp.colors_id, case when tp.color_id Is Null then ifnull((SELECT Case When fc.name Is Null Then 'Fancy Color does not exist' else '' end from fancy_colors fc where fc.name=tp.colors_id and fc.deleted_at is null and fc.status=1 and fc.type=0 group by fc.name),'') else '' end as colors_id_error,
                tp.overtone_id, case when tp.color_id Is Null then ifnull((SELECT Case When fo.name Is Null Then 'Fancy Color Overtone does not exist' else '' end from fancy_colors fo where fo.name=tp.overtone_id and fo.deleted_at is null and fo.status=1 and fo.type=1 group by fo.name),'') else '' end as overtone_id_error,
                tp.intensity_id, case when tp.color_id Is Null then ifnull((SELECT Case When fi.name Is Null Then 'Fancy Color Intensity does not exist' else '' end from fancy_colors fi where fi.name=tp.intensity_id and fi.deleted_at is null and fi.status=1 and fi.type=2 group by fi.name),'') else '' end as intensity_id_error,
                tp.clarity_id, Case When tp.clarity_id='FL' || tp.clarity_id='IF' || tp.clarity_id='VVS1' || tp.clarity_id='VVS2' || tp.clarity_id='VS1' || tp.clarity_id='VS2' || tp.clarity_id='SI1' || tp.clarity_id='SI2' || tp.clarity_id='I1' || tp.clarity_id='I2' || tp.clarity_id='I3' then '' else 'Calrity does not exist' end as clarity_id_error,
                tp.polish_id, Case When tp.polish_id='EX' || tp.polish_id='VG' || tp.polish_id='G' || tp.polish_id='F' || tp.polish_id='P' Then '' else 'Polish does not exist' end as polish_id_error,
                tp.symmetry_id, Case When tp.symmetry_id='EX' || tp.symmetry_id='VG' || tp.symmetry_id='G' || tp.symmetry_id='F' || tp.symmetry_id='P' Then '' else 'Symmetry does not exist' end as symmetry_id_error,
                tp.fluorescence_id, Case When tp.fluorescence_id='NON' || tp.fluorescence_id='VS' || tp.fluorescence_id='STG' || tp.fluorescence_id='MED' || tp.fluorescence_id='FNT' || tp.fluorescence_id='SLT' || tp.fluorescence_id='VSL' Then '' else 'Fluorescence does not exist' end as fluorescence_id_error,
                tp.rapo_rate, case when tp.color_id is null then '' else Case When tp.rapo_rate Is Null Then 'Rapo Rate cannot be null' else '' end end as rapo_rate_error,
                tp.discount, case when tp.color_id is null then '' else Case When tp.discount Is Null Then 'Discount cannot be null' else '' end end as discount_error,
                tp.rate, Case When tp.rate Is Null Then 'Rate cannot be null' else '' end as rate_error,
                tp.amount, Case When tp.amount Is Null Then 'Amount cannot be null' else '' end as amount_error,
                tp.table_per, Case When tp.table_per Is Null Then 'Table% cannot be null' else '' end as table_per_error,
                tp.depth_per, Case When tp.depth_per Is Null Then 'Depth% cannot be null' else '' end as depth_per_error,
                tp.length, Case When tp.length Is Null Then 'Length cannot be null' else '' end as length_error,
                tp.width, Case When tp.width Is Null Then 'Width cannot be null' else '' end as width_error,
                tp.height, Case When tp.height Is Null Then 'Height cannot be null' else '' end as height_error,
                tp.country, Case When tp.country Is Null Then 'Location cannot be null' else '' end as country_error,
                0 as temptype,
                tp.diamond_type
            from
            temp_products tp
            left join products p1 ON tp.stone_id=p1.stone_id and p1.deleted_at is null
            left join products p2 ON tp.stone_id=p2.cert_no and p2.deleted_at is null
            where (case when v_type=1 then tp.vendor_id IS NULL else tp.vendor_id=P_vendor_id end) AND tp.import_type=0 AND tp.diamond_type=1 order by tp.id;

            CREATE TEMPORARY TABLE withouterror
            select * from (
                select id, uuid, case when stone_id_error='' && cert_no_error='' && cert_type_error='' && cert_url_error='' && carat_error='' && shape_id_error='' && color_id_error='' && colors_id_error='' && overtone_id_error='' && intensity_id_error='' && clarity_id_error='' && polish_id_error='' && symmetry_id_error='' && fluorescence_id_error='' && rapo_rate_error='' && discount_error='' && rate_error='' && amount_error='' && table_per_error='' && depth_per_error='' && country_error='' && length_error='' && width_error='' && height_error='' then 1 else 0 end as msg from tempdata
            ) j where j.msg=1;

            UPDATE temp_products tp
            INNER JOIN withouterror we ON we.id = tp.id
            SET tp.import_type=1
            WHERE we.msg = 1;

            UPDATE tempdata tp
            INNER JOIN withouterror we ON we.id = tp.id
            SET tp.temptype=1
            WHERE we.msg = 1;

            delete from tempdata where (case when v_type=1 then vendor_id IS NULL else vendor_id=P_vendor_id end) and temptype=1;
            select * from tempdata where (case when v_type=1 then vendor_id IS NULL else vendor_id=P_vendor_id end) AND temptype=0;

            END;";

            DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('_all_product_import_error_n_procedure');
    }
};
