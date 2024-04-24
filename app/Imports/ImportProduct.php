<?php

namespace App\Imports;

use App\Models\Shape;
use App\Models\TempProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportProduct implements ToCollection, WithStartRow
{
    protected $details;
    public function  __construct($details)
    {
        $this->details = $details;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $data = [];
            $data['type'] = $this->details['diamond_type'];
            $data['uuid'] = $this->details['uuid'];
            $data['vendor_id'] = $this->details['vendor_id'] ?? null;

            $shape = $row[6];
            if($shape == "B" || $shape == "BR" || $shape == "RB" || $shape == "RBC" || $shape == "R" || $shape == "RD" || $shape == "ROUND BRILLIANT" || $shape == "RND"){
                $shapes = "Round";
            } else if($shape == "P" || $shape == "PS" || $shape == "PB" || $shape == "PMB" || $shape == "PE") {
                $shapes = "Pear";
            } else if($shape == "E" || $shape == "EM" || $shape == "EC") {
                $shapes = "Emerald";
            } else if($shape == "SQE" || $shape == "SQEM" || $shape == "A" || $shape == "AC" || $shape == "AS" || $shape == "SQ EMERALD" || $shape == "SE" || $shape == "SEM") {
                $shapes = "Asscher";
            } else if($shape == "PRN" || $shape == "PR" || $shape == "PRIN" || $shape == "PC" || $shape == "SMB" || $shape == "SQUARE MODIFIED" || $shape == "SQUARE MODIFIED BRILLIANT") {
                $shapes = "Princess";
            } else if($shape == "M" || $shape == "MQ" || $shape == "MQB" || $shape == "MB") {
                $shapes = "Marquise";
            } else if($shape == "CUSHION BRILLIANT" || $shape == "CB" || $shape == "C" || $shape == "CU" || $shape == "CUSH" || $shape == "CUBR") {
                $shapes = "Cush Brill";
            } else if($shape == "CM" || $shape == "CMB" || $shape == "CSMB") {
                $shapes = "Cush Mod";
            } else if($shape == "H" || $shape == "HS" || $shape == "HT" || $shape == "HB" || $shape == "HRT") {
                $shapes = "Heart";
            } else if($shape == "O" || $shape == "OV" || $shape == "OMB" || $shape == "OB") {
                $shapes = "Oval";
            } else if($shape == "RT" || $shape == "RAD" || $shape == "RA" || $shape == "RN" || $shape == "RADIANT" || $shape == "LR_BRILLIANT" || $shape == "SR" || $shape == "Sq Radiant" || $shape == "SQR" || $shape == "SQRA") {
                $shapes = "Radiant";
            } else if($shape == "T" || $shape == "TR" || $shape == "TRIL" || $shape == "TRL") {
                $shapes = "Trilliant";
            } else if($shape == "TA" || $shape == "TRA" || $shape == "TRI") {
                $shapes = "Triangle";
            } else {
                $shapes = $shape;
            }
            // dd($shapes);
            if($this->details['diamond_type'] == 1) {
                if($row[1] != null){
                    $data['availability'] = $row[1] ?? null;
                    $data['country'] = $row[2] ?? null;
                    $data['stone_id'] = $row[3] ?? null;
                    $data['cert_type'] = $row[4] ?? null;

                    // if($row[5] != null) {
                    //     $cert = explode(',', $row[5]);
                    //     if (array_key_exists('1', $cert)) {
                    //         $data['cert_no'] = str_replace(')', '', str_replace('"', '', $cert[1])) ?? null;
                    //     }
                    //     if (array_key_exists('0', $cert)) {
                    //         $data['cert_url'] = str_replace('=HYPERLINK(', '', str_replace('"', '', $cert[0])) ?? null;
                    //     }
                    // }
                    $data['cert_no'] = $row[5] ?? null;
                    $data['shape_id'] = $shapes ?? 'Other';
                    $data['carat'] = $row[7] ?? 0;
                    $data['color_id'] = $row[8] ?? null;
                    $data['clarity_id'] = $row[9] ?? null;
                    $data['cut_id'] = $row[10] ?? null;
                    $data['polish_id'] = $row[11] ?? null;
                    $data['symmetry_id'] = $row[12] ?? null;
                    $data['fluorescence_id'] = $row[13] ?? null;
                    $data['rapo_rate'] = $row[14] ?? 0;
                    $data['rapo_amount'] = ($row[7] * $row[14]) ?? 0;
                    $data['discount'] = $row[15] ?? 0;
                    $data['rate'] = $row[16] ?? 0;
                    $data['amount'] = $row[17] ?? 0;
                    $data['table_per'] = $row[18] ?? 0;
                    $data['depth_per'] = $row[19] ?? 0;
                    $data['length'] = $row[20] ?? 0;
                    $data['width'] = $row[21] ?? 0;
                    $data['height'] = $row[22] ?? 0;
                    $data['key_to_symbols'] = $row[23] ?? null;
                    $data['milky'] = $row[24] ?? "NONE";
                    $data['shade'] = $row[25] ?? "NONE";
                    $data['table_black'] = $row[26] ?? "NONE";
                    $data['side_black'] = $row[27] ?? "NONE";
                    $data['white_table'] = $row[28] ?? "NONE";
                    $data['white_side'] = $row[29] ?? "NONE";
                    $data['eye_clean'] = $row[30] ?? 0;
                    $data['ratio'] = $row[31] ?? 0;
                    $data['h_a'] = $row[32] ?? 'No';
                    $data['inscription'] = $row[33] ?? null;
                    $data['crown_angle'] = $row[34] ?? 0;
                    $data['crown_height'] = $row[35] ?? 0;
                    $data['pavilion_angle'] = $row[36] ?? 0;
                    $data['pavilion_height'] = $row[37] ?? 0;
                    $data['girdle'] = $row[38] ?? 0;
                    $data['table_open'] = $row[39] ?? "NONE";
                    $data['pavilion_open'] = $row[40] ?? "NONE";
                    $data['crown_open'] = $row[41] ?? "NONE";
                    $data['comment'] = $row[42] ?? null;
                    $data['girdle_desc'] = $row[43] ?? null;
                    $data['colors_id'] = $row[44] ?? null;
                    $data['intensity_id'] = $row[45] ?? null;
                    $data['overtone_id'] = $row[46] ?? null;

                    // if($row[47] != null) {
                    //     $image = explode(',', $row[47]);
                    //     $data['image'] = str_replace('=HYPERLINK(', '', str_replace('"', '', $image[0])) ?? null;
                    // }
                    // if($row[48] != null) {
                    //     $video = explode(',', $row[48]);
                    //     $data['video'] = str_replace('=HYPERLINK(', '', str_replace('"', '', $video[0])) ?? null;
                    // }

                    $data['image'] = $row[47] ?? null;
                    $data['video'] = $row[48] ?? null;
                    $data['cert_url'] = $row[49] ?? null;

                    $data['diamond_type'] = 1;
                    TempProduct::create($data);
                }
            }
            if($this->details['diamond_type'] == 2) {
                if($row[3] != null){
                    // if($row[5] != null) {
                    //     $cert = explode(',', $row[5]);
                    //     if (array_key_exists('1', $cert)) {
                    //         $data['cert_no'] = str_replace(')', '', str_replace('"', '', $cert[1])) ?? null;
                    //     }
                    //     if (array_key_exists('0', $cert)) {
                    //         $data['cert_url'] = str_replace('=HYPERLINK(', '', str_replace('"', '', $cert[0])) ?? null;
                    //     }
                    // }

                    $data['cert_no'] = $row[5] ?? null;
                    $data['availability'] = $row[1] ?? null;
                    $data['country'] = $row[2] ?? null;
                    $data['stone_id'] = $row[3] ?? null;
                    $data['cert_type'] = $row[4] ?? null;

                    $data['shape_id'] = $shapes ?? 'Other';
                    $data['diamond_type'] = $this->details['diamond_type'];
                    $data['carat'] = $row[7] ?? 0;
                    $data['color_id'] = $row[8] ?? null;
                    $data['colors_id'] = $row[9] ?? null;
                    $data['intensity_id'] = $row[10] ?? null;
                    $data['overtone_id'] = $row[11] ?? null;
                    $data['clarity_id'] = $row[12] ?? null;
                    $data['cut_id'] = $row[13] ?? null;
                    $data['polish_id'] = $row[14] ?? null;
                    $data['symmetry_id'] = $row[15] ?? null;
                    $data['fluorescence_id'] = $row[16] ?? null;
                    $data['fluorescence_color_id'] = $row[17] ?? null;
                    $data['rapo_rate'] = $row[18] ?? 0;
                    $data['rapo_amount'] = ($row[7] * $row[18]) ?? 0;
                    $data['discount'] = $row[19] ?? 0;
                    $data['rate'] = $row[20] ?? 0;
                    $data['amount'] = $row[21] ?? 0;
                    $data['measurement'] = $row[22] ?? null;
                    $data['ratio'] = $row[23] ?? 0;
                    $data['bgm_id'] = $row[24] ?? null;
                    $data['length'] = $row[25] ?? 0;
                    $data['width'] = $row[26] ?? 0;
                    $data['height'] = $row[27] ?? 0;
                    $data['table_per'] = $row[28] ?? 0;
                    $data['depth_per'] = $row[29] ?? 0;
                    $data['pair'] = $row[30] ?? 'No';
                    $data['h_a'] = $row[31] ?? 'No';
                    $data['city'] = $row[32] ?? null;
                    $data['eye_clean'] = $row[33] ?? 0;
                    $data['growth_type'] = $row[34] ?? null;
                    $data['treatment'] = $row[35] ?? null;

                    // if($row[36] != null) {
                    //     $image = explode(',', $row[36]);
                    //     $data['image'] = $image[0] ?? str_replace('=HYPERLINK(', '', str_replace('"', '', $image[0]));
                    // }
                    // if($row[37] != null) {
                    //     $video = explode(',', $row[37]);
                    //     $data['video'] = $video[0] ?? str_replace('=HYPERLINK(', '', str_replace('"', '', $video[0]));
                    // }

                    $data['image'] = $row[36] ?? null;
                    $data['video'] = $row[37] ?? null;
                    $data['cert_url'] = $row[38] ?? null;
                    // dd($data);
                    TempProduct::create($data);
                }
            }
        }
    }
}
