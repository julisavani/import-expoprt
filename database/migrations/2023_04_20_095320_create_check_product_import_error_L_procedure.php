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
        $procedure = "DROP PROCEDURE IF EXISTS CheckProductImportErrorL;
        CREATE PROCEDURE CheckProductImportErrorL(IN P_uuid CHAR(36))
        BEGIN
        DROP TABLE IF EXISTS tempdata;
        DROP TABLE IF EXISTS withouterror;
        CREATE TEMPORARY TABLE tempdata
        SELECT
            tp.id, tp.uuid, tp.stone_id, Case When p1.stone_id Is NOT Null Then 'This Stone ID is already available' else '' end as stone_id_error,
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
            tp.rapo_rate, case when tp.color_id is null then '' else Case When tp.rapo_rate Is Null Then 'Rapo Rate cannot be null' else '' end end as rapo_rate_error,
            tp.discount, case when tp.color_id is null then '' else Case When tp.discount Is Null Then 'Discount cannot be null' else '' end end as discount_error,
            tp.rate, Case When tp.rate Is Null Then 'Rate cannot be null' else '' end as rate_error,
            tp.amount, Case When tp.amount Is Null Then 'Amount cannot be null' else '' end as amount_error,
            tp.city, Case When tp.city Is Null Then 'City cannot be null' else '' end as city_error,
            tp.country, Case When tp.country Is Null Then 'Location cannot be null' else '' end as country_error,
            tp.growth_type, Case When tp.growth_type Is Null Then 'Growth Type cannot be null' else '' end as growth_type_error,
            0 as temptype,
            tp.diamond_type
        from
        temp_products tp
        left join products p1 ON tp.stone_id=p1.stone_id and p1.deleted_at is null
        left join products p2 ON tp.stone_id=p2.cert_no and p2.deleted_at is null
        where tp.uuid=P_uuid AND tp.import_type=0 order by tp.id;

        CREATE TEMPORARY TABLE withouterror
        select * from (
            select id, uuid, case when stone_id_error='' && carat_error='' && shape_id_error='' && color_id_error='' && colors_id_error='' && overtone_id_error='' && intensity_id_error='' && clarity_id_error='' && rapo_rate_error='' && discount_error='' && rate_error='' && amount_error='' && city_error='' && country_error='' && growth_type_error='' then 1 else 0 end as msg from tempdata
        ) j where j.msg=1;

        UPDATE temp_products tp
        INNER JOIN withouterror we ON we.id = tp.id
        SET tp.import_type=1
        WHERE we.msg = 1;

        UPDATE tempdata tp
        INNER JOIN withouterror we ON we.id = tp.id
        SET tp.temptype=1
        WHERE we.msg = 1;

        delete from tempdata where uuid=P_uuid and temptype=1;
        select * from tempdata where uuid=P_uuid AND temptype=0;

        END;";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('check_product_import_error_procedure');
    }
};
